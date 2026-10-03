<?php

namespace Database\Seeders;

use App\Models\LeaveType;
use Illuminate\Database\Seeder;

class LeaveTypeSeeder extends Seeder
{
    public function run(): void
    {
        $leaveTypes = [
            [
                'code' => 'ANNUAL',
                'name' => 'Cuti Tahunan (Annual Leave)',
                'description' => 'Hak istirahat tahunan karyawan selama 12 hari kerja per tahun sesuai UU Ketenagakerjaan.',
                'deducts_annual_quota' => true,
                'is_paid' => true,
                'requires_attachment' => false,
                'max_days' => 12,
            ],
            [
                'code' => 'SICK_CERT',
                'name' => 'Sakit (Dengan Surat Keterangan Dokter)',
                'description' => 'Izin ketidakhadiran karena kondisi medis/sakit disertai bukti surat dokter resmi.',
                'deducts_annual_quota' => false,
                'is_paid' => true,
                'requires_attachment' => true,
                'max_days' => 14,
            ],
            [
                'code' => 'MARRIAGE',
                'name' => 'Izin Menikah Karyawan',
                'description' => 'Izin pernikahan pekerja sesuai Pasal 93 ayat (4) huruf a UU No. 13/2003 (Maksimal 3 hari berbayar).',
                'deducts_annual_quota' => false,
                'is_paid' => true,
                'requires_attachment' => true,
                'max_days' => 3,
            ],
            [
                'code' => 'MATERNITY',
                'name' => 'Cuti Melahirkan (Maternity Leave)',
                'description' => 'Hak cuti bagi pekerja perempuan selama 1,5 bulan sebelum dan 1,5 bulan sesudah melahirkan (Maksimal 90 hari kalender).',
                'deducts_annual_quota' => false,
                'is_paid' => true,
                'requires_attachment' => true,
                'max_days' => 90,
            ],
            [
                'code' => 'PATERNITY',
                'name' => 'Cuti Suami (Istri Melahirkan / Keguguran)',
                'description' => 'Izin mendampingi istri yang melahirkan atau mengalami keguguran kandungan (2 hari berbayar).',
                'deducts_annual_quota' => false,
                'is_paid' => true,
                'requires_attachment' => true,
                'max_days' => 2,
            ],
            [
                'code' => 'BEREAVEMENT',
                'name' => 'Izin Duka Cita (Keluarga Inti Wafat)',
                'description' => 'Izin kematian suami/istri, orang tua/mertua, anak, atau menantu (2 hari berbayar).',
                'deducts_annual_quota' => false,
                'is_paid' => true,
                'requires_attachment' => false,
                'max_days' => 2,
            ],
            [
                'code' => 'HAJJ',
                'name' => 'Ibadah Keagamaan / Haji',
                'description' => 'Izin menunaikan kewajiban ibadah keagamaan haji pertama kali sesuai penetapan pemerintah.',
                'deducts_annual_quota' => false,
                'is_paid' => true,
                'requires_attachment' => true,
                'max_days' => 40,
            ],
            [
                'code' => 'UNPAID',
                'name' => 'Izin Tanpa Upah (Unpaid Leave / Di Luar Tanggungan)',
                'description' => 'Izin tidak masuk kerja di luar cuti resmi atau kuota cuti tahunan habis. Upah akan dipotong prorata harian pada sistem Payroll.',
                'deducts_annual_quota' => false,
                'is_paid' => false,
                'requires_attachment' => false,
                'max_days' => 30,
            ],
        ];

        foreach ($leaveTypes as $type) {
            LeaveType::updateOrCreate(
                ['code' => $type['code']],
                $type
            );
        }
    }
}
