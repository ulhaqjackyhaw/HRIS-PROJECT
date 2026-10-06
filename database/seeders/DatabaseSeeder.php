<?php

namespace Database\Seeders;

use App\Models\Department;
use App\Models\Employee;
use App\Models\Position;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $user = User::firstOrCreate(
            ['email' => 'admin@hris.corp'],
            [
                'name' => 'HR Administrator',
                'password' => bcrypt('password'),
            ]
        );

        // 1. Departemen
        $deptBOD = Department::create([
            'code' => 'DIR',
            'name' => 'Direksi & Eksekutif',
            'description' => 'Jajaran pimpinan tertinggi korporat',
            'is_active' => true,
        ]);

        $deptHC = Department::create([
            'code' => 'HC',
            'name' => 'Human Capital Management',
            'parent_id' => $deptBOD->id,
            'description' => 'Pengembangan SDM, talent acquisition, payroll, dan hubungan industrial',
            'is_active' => true,
        ]);

        $deptFIN = Department::create([
            'code' => 'FIN',
            'name' => 'Finance & Accounting',
            'parent_id' => $deptBOD->id,
            'description' => 'Pengelolaan keuangan, perpajakan, dan perbendaharaan',
            'is_active' => true,
        ]);

        $deptIT = Department::create([
            'code' => 'IT',
            'name' => 'Information Technology',
            'parent_id' => $deptBOD->id,
            'description' => 'Pengembangan sistem software, infrastruktur, dan keamanan data',
            'is_active' => true,
        ]);

        // 2. Formasi Jabatan
        $posHCManager = Position::create([
            'department_id' => $deptHC->id,
            'code' => 'POS-HC-001',
            'title' => 'Human Capital Manager',
            'level' => 'Manager',
            'career_path' => 'Struktural',
            'job_function' => 'HC Management',
            'is_active' => true,
        ]);

        $posPayroll = Position::create([
            'department_id' => $deptHC->id,
            'code' => 'POS-HC-002',
            'title' => 'Payroll & Compensation Specialist',
            'level' => 'Senior Staff',
            'career_path' => 'Spesialis',
            'job_function' => 'Compensation & Benefit',
            'is_active' => true,
        ]);

        $posHRStaff = Position::create([
            'department_id' => $deptHC->id,
            'code' => 'POS-HC-003',
            'title' => 'People Operations Staff',
            'level' => 'Staff',
            'career_path' => 'Fungsional',
            'job_function' => 'Talent Management',
            'is_active' => true,
        ]);

        $posITDev = Position::create([
            'department_id' => $deptIT->id,
            'code' => 'POS-IT-001',
            'title' => 'Senior Software Engineer',
            'level' => 'Senior Staff',
            'career_path' => 'Spesialis',
            'job_function' => 'Application Engineering',
            'is_active' => true,
        ]);

        // 3. Karyawan (Manager)
        $manager = Employee::create([
            'user_id' => $user->id,
            'department_id' => $deptHC->id,
            'position_id' => $posHCManager->id,
            'nik' => 'HC-2021001',
            'employment_status' => 'PKWTT',
            'join_date' => Carbon::now()->subYears(4)->subMonths(3)->format('Y-m-d'),
            'current_contract_no' => 'SK-TETAP/HC/2022/012',
            'work_location' => 'Head Office Jakarta',
            'is_active' => true,
            'ktp_number' => '3273012304850001',
            'full_name' => 'Bambang Pratama, S.Psi., M.M.',
            'gender' => 'MALE',
            'birth_date' => '1985-04-12',
            'religion' => 'ISLAM',
            'marital_status' => 'MARRIED',
            'ktp_address' => 'Jl. Kebon Jeruk Indah No. 12, Kebon Jeruk, Jakarta Barat',
            'current_address' => 'Jl. Kebon Jeruk Indah No. 12, Kebon Jeruk, Jakarta Barat',
            'email' => 'bambang.pratama@hris.corp',
            'phone_number' => '081234567890',
            'npwp' => '01.234.567.8-012.000',
            'ptkp_status' => 'K/2',
            'bpjs_ketenagakerjaan_no' => '19028374651',
            'bpjs_kesehatan_no' => '0001928374651',
            'bank_name' => 'Bank Mandiri',
            'bank_account_number' => '1270009876543',
            'bank_account_holder' => 'Bambang Pratama',
            'custom_fields' => ['blood_type' => 'O', 'uniform_size' => 'XL'],
        ]);

        $manager->educations()->create([
            'education_level' => 'S1',
            'major' => 'Psikologi Industri & Organisasi',
            'institution_name' => 'Universitas Indonesia',
            'graduation_year' => 2007,
            'is_recognized' => true,
        ]);

        $manager->educations()->create([
            'education_level' => 'S2',
            'major' => 'Magister Manajemen Sumber Daya Manusia',
            'institution_name' => 'Universitas Gadjah Mada',
            'graduation_year' => 2011,
            'is_recognized' => true,
        ]);

        // 4. Karyawan (Staf PKWT - Akan Berakhir 15 Hari Lagi untuk pengujian notifikasi)
        $staff1 = Employee::create([
            'department_id' => $deptHC->id,
            'position_id' => $posPayroll->id,
            'manager_id' => $manager->id,
            'nik' => 'HC-2025014',
            'employment_status' => 'PKWT',
            'join_date' => Carbon::now()->subMonths(11)->subDays(15)->format('Y-m-d'),
            'end_date' => Carbon::today()->addDays(15)->format('Y-m-d'),
            'current_contract_no' => 'CTR/HC/2025/112',
            'work_location' => 'Head Office Jakarta',
            'is_active' => true,
            'ktp_number' => '3273011409970002',
            'full_name' => 'Siti Nurhaliza, S.Ak.',
            'gender' => 'FEMALE',
            'birth_date' => '1997-09-14',
            'religion' => 'ISLAM',
            'marital_status' => 'SINGLE',
            'ktp_address' => 'Jl. Tebet Barat Raya No. 45, Tebet, Jakarta Selatan',
            'current_address' => 'Jl. Tebet Barat Raya No. 45, Tebet, Jakarta Selatan',
            'email' => 'siti.nurhaliza@hris.corp',
            'phone_number' => '085712348899',
            'npwp' => '88.345.123.4-041.000',
            'ptkp_status' => 'TK/0',
            'bpjs_ketenagakerjaan_no' => '22019283741',
            'bpjs_kesehatan_no' => '0002233445566',
            'bank_name' => 'BCA',
            'bank_account_number' => '5420192831',
            'bank_account_holder' => 'Siti Nurhaliza',
            'custom_fields' => ['blood_type' => 'A', 'uniform_size' => 'M'],
        ]);

        $staff1->contracts()->create([
            'contract_number' => 'CTR/HC/2025/112',
            'contract_type' => 'PKWT-1',
            'start_date' => Carbon::now()->subMonths(11)->subDays(15)->format('Y-m-d'),
            'end_date' => Carbon::today()->addDays(15)->format('Y-m-d'),
            'notes' => 'Kontrak PKWT tahun pertama periode 12 bulan.',
        ]);

        $staff1->educations()->create([
            'education_level' => 'S1',
            'major' => 'Akuntansi Perpajakan',
            'institution_name' => 'Universitas Padjadjaran',
            'graduation_year' => 2020,
            'is_recognized' => true,
        ]);

        // 5. Karyawan IT
        $itStaff = Employee::create([
            'department_id' => $deptIT->id,
            'position_id' => $posITDev->id,
            'manager_id' => $manager->id,
            'nik' => 'IT-2024003',
            'employment_status' => 'PKWTT',
            'join_date' => Carbon::now()->subYears(2)->subMonths(4)->format('Y-m-d'),
            'current_contract_no' => 'SK-TETAP/IT/2024/007',
            'work_location' => 'Cabang Bandung',
            'is_active' => true,
            'ktp_number' => '3273010101960003',
            'full_name' => 'Rizky Ramadhan, S.Kom.',
            'gender' => 'MALE',
            'birth_date' => '1996-01-20',
            'religion' => 'ISLAM',
            'marital_status' => 'MARRIED',
            'ktp_address' => 'Jl. Dago Asri No. 8, Bandung',
            'current_address' => 'Jl. Dago Asri No. 8, Bandung',
            'email' => 'rizky.ramadhan@hris.corp',
            'phone_number' => '082199887766',
            'npwp' => '45.123.678.9-429.000',
            'ptkp_status' => 'K/1',
            'bpjs_ketenagakerjaan_no' => '21038475612',
            'bpjs_kesehatan_no' => '0003344556677',
            'bank_name' => 'BCA',
            'bank_account_number' => '8890123456',
            'bank_account_holder' => 'Rizky Ramadhan',
            'custom_fields' => ['blood_type' => 'B', 'uniform_size' => 'L'],
        ]);

        $itStaff->educations()->create([
            'education_level' => 'S1',
            'major' => 'Teknik Informatika',
            'institution_name' => 'Institut Teknologi Bandung',
            'graduation_year' => 2018,
            'is_recognized' => true,
        ]);

        $this->call([
            AttendanceModuleSeeder::class,
            LeaveTypeSeeder::class,
            RecruitmentSeeder::class,
        ]);
    }
}
