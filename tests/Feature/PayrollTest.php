<?php

namespace Tests\Feature;

use App\Models\Attendance;
use App\Models\Department;
use App\Models\Employee;
use App\Models\ExpenseClaim;
use App\Models\OvertimeRequest;
use App\Models\PayrollPeriod;
use App\Models\Payslip;
use App\Models\Position;
use App\Models\SalaryStructure;
use App\Models\User;
use App\Services\PayrollEngineService;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PayrollTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected Employee $employee;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();

        $department = Department::factory()->create(['name' => 'Finance & Accounting']);
        $position = Position::factory()->create(['title' => 'Payroll Specialist', 'department_id' => $department->id]);

        $this->employee = Employee::factory()->create([
            'user_id' => $this->user->id,
            'department_id' => $department->id,
            'position_id' => $position->id,
            'full_name' => 'Siti Aminah',
            'nik' => 'EMP-FIN-001',
            'ptkp_status' => 'TK/0',
            'bank_name' => 'BCA',
            'bank_account_number' => '1234567890',
            'is_active' => true,
        ]);

        SalaryStructure::create([
            'employee_id' => $this->employee->id,
            'basic_salary' => 10000000,
            'fixed_allowance' => 2000000,
            'daily_transport_allowance' => 25000,
            'daily_meal_allowance' => 35000,
            'use_bpjs_tk' => true,
            'use_bpjs_kes' => true,
        ]);
    }

    public function test_can_view_payroll_index_page(): void
    {
        PayrollPeriod::create([
            'name' => 'Payroll Oktober 2026',
            'start_date' => '2026-09-21',
            'end_date' => '2026-10-20',
            'payout_date' => '2026-10-25',
            'status' => 'DRAFT',
        ]);

        $response = $this->actingAs($this->user)->get(route('payroll.index'));

        $response->assertOk();
        $response->assertSee('Daftar Periode Penggajian');
        $response->assertSee('Payroll Oktober 2026');
    }

    public function test_can_create_new_payroll_period(): void
    {
        $response = $this->actingAs($this->user)->post(route('payroll.periods.store'), [
            'name' => 'Payroll November 2026',
            'start_date' => '2026-10-21',
            'end_date' => '2026-11-20',
            'payout_date' => '2026-11-25',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('payroll_periods', [
            'name' => 'Payroll November 2026',
            'status' => 'DRAFT',
        ]);
    }

    public function test_can_view_payroll_period_detail(): void
    {
        $period = PayrollPeriod::create([
            'name' => 'Payroll Oktober 2026',
            'start_date' => '2026-09-21',
            'end_date' => '2026-10-20',
            'payout_date' => '2026-10-25',
            'status' => 'DRAFT',
        ]);

        $response = $this->actingAs($this->user)->get(route('payroll.periods.show', $period));

        $response->assertOk();
        $response->assertSee('Payroll Oktober 2026');
        $response->assertSee('Hitung / Refresh Payroll');
    }

    public function test_payroll_engine_computes_accurate_amounts_and_breakdowns(): void
    {
        $period = PayrollPeriod::create([
            'name' => 'Payroll Oktober 2026',
            'start_date' => '2026-09-21',
            'end_date' => '2026-10-20',
            'payout_date' => '2026-10-25',
            'status' => 'DRAFT',
        ]);

        // Create 20 days present, 1 day late (30 mins), 1 day absent
        for ($i = 0; $i < 20; $i++) {
            Attendance::create([
                'employee_id' => $this->employee->id,
                'date' => Carbon::parse('2026-09-21')->addDays($i)->toDateString(),
                'status' => $i === 0 ? 'LATE' : 'PRESENT',
                'late_minutes' => $i === 0 ? 30 : 0,
            ]);
        }
        Attendance::create([
            'employee_id' => $this->employee->id,
            'date' => '2026-10-15',
            'status' => 'ABSENT',
            'late_minutes' => 0,
        ]);

        // Approved Overtime: 5 hours
        OvertimeRequest::create([
            'employee_id' => $this->employee->id,
            'date' => '2026-10-02',
            'start_time' => '17:00:00',
            'end_time' => '22:00:00',
            'total_hours' => 5,
            'reason' => 'Closing bulanan',
            'status' => 'APPROVED',
        ]);

        // Approved Expense Claim: 250,000
        ExpenseClaim::create([
            'employee_id' => $this->employee->id,
            'payroll_period_id' => $period->id,
            'title' => 'Bensin & Tol Meeting',
            'amount' => 250000,
            'claim_date' => '2026-10-05',
            'status' => 'APPROVED',
        ]);

        $engine = new PayrollEngineService;
        $payslip = $engine->computeEmployeePayroll($period, $this->employee);

        // Verification:
        // Basic: 10,000,000
        // Fixed allowance: 2,000,000
        // Meal (20 days * 35,000) = 700,000
        // Transport (20 days * 25,000) = 500,000
        // Overtime: (10,000,000 / 173) * 5 = 289,017.34
        // Gross = 10,000,000 + 2,000,000 + 700,000 + 500,000 + 289,017.34 = 13,489,017.34
        $this->assertEquals(20, $payslip->present_days);
        $this->assertEquals(1, $payslip->absent_days);
        $this->assertEquals(30, $payslip->late_minutes);
        $this->assertEquals(5, $payslip->overtime_hours);
        $this->assertEquals(13489017.34, (float) $payslip->gross_salary);

        // Deductions:
        // Late penalty: 30 * 1,000 = 30,000
        // Absent deduction: (1 / 22) * 10,000,000 = 454,545.45
        // BPJS TK: 10,000,000 * 3% = 300,000
        // BPJS Kes: min(10,000,000, 12,000,000) * 1% = 100,000
        // PPh 21 TER A for > 10,050,000: 5% of gross = 13,489,017.34 * 0.05 = 674,450.87
        // Total Deductions = 30,000 + 454,545.45 + 300,000 + 100,000 + 674,450.87 = 1,558,996.32
        $this->assertEquals(1558996.32, (float) $payslip->total_deductions);

        // Net salary: Gross - Deductions + Reimbursement (250,000)
        // 13,489,017.34 - 1,558,996.32 + 250,000 = 12,180,021.02
        $this->assertEquals(12180021.02, (float) $payslip->net_salary);

        // Verify items populated
        $this->assertDatabaseHas('payslip_items', [
            'payslip_id' => $payslip->id,
            'name' => 'Gaji Pokok',
            'amount' => 10000000,
        ]);
        $this->assertDatabaseHas('payslip_items', [
            'payslip_id' => $payslip->id,
            'name' => 'Reimbursement: Bensin & Tol Meeting',
            'amount' => 250000,
        ]);
    }

    public function test_can_generate_batch_payroll_and_view_payslip_detail(): void
    {
        $period = PayrollPeriod::create([
            'name' => 'Payroll Oktober 2026',
            'start_date' => '2026-09-21',
            'end_date' => '2026-10-20',
            'payout_date' => '2026-10-25',
            'status' => 'DRAFT',
        ]);

        $response = $this->actingAs($this->user)->post(route('payroll.periods.generate', $period));
        $response->assertRedirect();

        // Fresh check period has status APPROVED/PAID or has payslips generated
        $period->refresh();
        $this->assertEquals('APPROVED', $period->status);
        $this->assertGreaterThan(0, $period->total_net_amount);

        $payslip = Payslip::where('payroll_period_id', $period->id)->firstOrFail();

        $slipResponse = $this->actingAs($this->user)->get(route('payroll.payslips.show', $payslip));
        $slipResponse->assertOk();
        $slipResponse->assertSee('SLIP GAJI RESMI (CONFIDENTIAL)');
        $slipResponse->assertSee('PENERIMAAN (EARNINGS)');
        $slipResponse->assertSee('POTONGAN (DEDUCTIONS)');
        $slipResponse->assertSee('Gaji Pokok');
    }
}
