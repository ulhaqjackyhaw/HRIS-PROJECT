<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RoleAuthenticationTest extends TestCase
{
    use RefreshDatabase;

    public function test_candidate_can_login_at_candidate_portal(): void
    {
        $candidate = User::factory()->candidate()->create([
            'email' => 'pelamar@hris.corp',
            'password' => bcrypt('password123'),
        ]);

        $response = $this->post(route('career.login.submit'), [
            'email' => 'pelamar@hris.corp',
            'password' => 'password123',
        ]);

        $this->assertAuthenticatedAs($candidate);
        $response->assertRedirect(route('career.dashboard'));
    }

    public function test_employee_cannot_login_at_candidate_portal(): void
    {
        $employee = User::factory()->employee()->create([
            'email' => 'staff@hris.corp',
            'password' => bcrypt('password123'),
        ]);

        $response = $this->post(route('career.login.submit'), [
            'email' => 'staff@hris.corp',
            'password' => 'password123',
        ]);

        $this->assertGuest();
        $response->assertSessionHasErrors('email');
    }

    public function test_hr_cannot_login_at_candidate_portal(): void
    {
        $hr = User::factory()->hr()->create([
            'email' => 'admin@hris.corp',
            'password' => bcrypt('password123'),
        ]);

        $response = $this->post(route('career.login.submit'), [
            'email' => 'admin@hris.corp',
            'password' => 'password123',
        ]);

        $this->assertGuest();
        $response->assertSessionHasErrors('email');
    }

    public function test_hr_can_login_at_hr_portal(): void
    {
        $hr = User::factory()->hr()->create([
            'email' => 'admin@hris.corp',
            'password' => bcrypt('password123'),
        ]);

        $response = $this->post(route('login'), [
            'email' => 'admin@hris.corp',
            'password' => 'password123',
        ]);

        $this->assertAuthenticatedAs($hr);
        $response->assertRedirect(route('portal'));
    }

    public function test_employee_cannot_login_at_hr_portal(): void
    {
        User::factory()->employee()->create([
            'email' => 'karyawan@hris.local',
            'password' => bcrypt('password123'),
        ]);

        $response = $this->post(route('login'), [
            'email' => 'karyawan@hris.local',
            'password' => 'password123',
        ]);

        $this->assertGuest();
        $response->assertSessionHasErrors('email');
    }

    public function test_candidate_cannot_login_at_hr_portal(): void
    {
        User::factory()->candidate()->create([
            'email' => 'pelamar@hris.corp',
            'password' => bcrypt('password123'),
        ]);

        $response = $this->post(route('login'), [
            'email' => 'pelamar@hris.corp',
            'password' => 'password123',
        ]);

        $this->assertGuest();
        $response->assertSessionHasErrors('email');
    }

    public function test_employee_can_login_at_employee_portal(): void
    {
        $employee = User::factory()->employee()->create([
            'email' => 'karyawan@hris.local',
            'password' => bcrypt('password123'),
        ]);

        $response = $this->post(route('employee.login.submit'), [
            'email' => 'karyawan@hris.local',
            'password' => 'password123',
        ]);

        $this->assertAuthenticatedAs($employee);
        $response->assertRedirect(route('employee.dashboard'));
    }

    public function test_candidate_cannot_login_at_employee_portal(): void
    {
        User::factory()->candidate()->create([
            'email' => 'pelamar@hris.corp',
            'password' => bcrypt('password123'),
        ]);

        $response = $this->post(route('employee.login.submit'), [
            'email' => 'pelamar@hris.corp',
            'password' => 'password123',
        ]);

        $this->assertGuest();
        $response->assertSessionHasErrors('email');
    }

    public function test_candidate_cannot_access_hr_routes(): void
    {
        $candidate = User::factory()->candidate()->create();

        $response = $this->actingAs($candidate)->get(route('portal'));

        $response->assertRedirect(route('career.dashboard'));
    }

    public function test_employee_cannot_access_core_hr_admin_routes(): void
    {
        $employee = User::factory()->employee()->create();

        $response = $this->actingAs($employee)->get(route('dashboard'));

        $response->assertRedirect(route('employee.dashboard'));
    }

    public function test_employee_can_access_employee_dashboard(): void
    {
        $employee = User::factory()->employee()->create();

        $response = $this->actingAs($employee)->get(route('employee.dashboard'));

        $response->assertStatus(200);
        $response->assertSee('Dashboard Karyawan');
    }

    public function test_employee_can_access_all_dedicated_employee_portal_views(): void
    {
        $employee = User::factory()->employee()->create();

        $routes = [
            'employee.attendance.check-in',
            'employee.attendance.history',
            'employee.leaves.index',
            'employee.overtimes.index',
            'employee.schedules.index',
            'employee.profile',
        ];

        foreach ($routes as $routeName) {
            $response = $this->actingAs($employee)->get(route($routeName));
            $response->assertStatus(200);
        }
    }
}
