<?php

namespace App\Http\Controllers;

use App\Models\Department;
use App\Models\Employee;
use App\Models\Position;
use Illuminate\Contracts\View\View;

class ModulePortalController extends Controller
{
    /**
     * Get the defined enterprise domains / modules.
     *
     * @return array<string, array<string, mixed>>
     */
    public static function getDomains(): array
    {
        return [
            'core-hr' => [
                'name' => 'Core HR & Kepegawaian',
                'category' => 'Master Data & Organization',
                'description' => 'Organization Units, Employee Life-Cycle, RBAC, Master Data & Profil Karyawan',
                'status' => 'active',
                'status_label' => 'Aktif & Beroperasi',
                'color' => 'indigo',
                'gradient' => 'from-indigo-600 to-violet-600',
                'bg_glow' => 'bg-indigo-500/10 text-indigo-400 border-indigo-500/30',
                'route' => 'dashboard',
                'features' => [
                    'Master Unit Kerja / Departemen & Hierarki',
                    'Formasi Kursi Jabatan & Jalur Karir',
                    'Daftar Induk Profil Karyawan Lengkap',
                    'Kalkulasi Otomatis Usia & Masa Kerja (Dinamis)',
                    'Manajemen Histori Kontrak PKWT & Pendidikan',
                ],
            ],
            'recruitment' => [
                'name' => 'Recruitment & ATS',
                'category' => 'Talent Acquisition',
                'description' => 'Applicant Tracking System, Job Posting, Candidate Portal, & CV Parser Job',
                'status' => 'in_progress',
                'status_label' => 'Dalam Pengembangan',
                'color' => 'blue',
                'gradient' => 'from-blue-600 to-cyan-600',
                'bg_glow' => 'bg-blue-500/10 text-blue-400 border-blue-500/30',
                'route' => 'modules.show',
                'features' => [
                    'Penerbitan Lowongan Kerja (Job Posting)',
                    'Pipeline Seleksi Pelamar & Tahapan Tes',
                    'Candidate Portal & Submission',
                    'AI CV Parsing & Resume Screening',
                    'Penjadwalan Interview & Offering Letter',
                ],
            ],
            'attendance' => [
                'name' => 'Attendance & Waktu Kerja',
                'category' => 'Time & Attendance',
                'description' => 'Shift Management, Biometrics/Geo-location Check-in, & Overtime Tracking',
                'status' => 'active',
                'status_label' => 'Aktif Digunakan',
                'color' => 'amber',
                'gradient' => 'from-amber-600 to-orange-600',
                'bg_glow' => 'bg-amber-500/10 text-amber-400 border-amber-500/30',
                'route' => 'attendance.check-in',
                'features' => [
                    'Presensi Biometrik Wajah Selfie & Geolocation GPS',
                    'Validasi Geofence Radius Kantor (Anti-Fake GPS)',
                    'Manajemen Shift Kerja Dinamis (Pagi, Reguler, Malam)',
                    'Roster & Penjadwalan Kerja Karyawan Bulanan',
                    'Pengajuan Cuti & Izin Kerja Terintegrasi Kalender Kehadiran',
                    'Pengajuan & Approval Surat Perintah Lembur (SPL)',
                    'Rekapitulasi Presensi & Lembur Bulanan (Siap Payroll)',
                ],
            ],
            'payroll' => [
                'name' => 'Payroll & Kompensasi',
                'category' => 'Compensation & Benefits',
                'description' => 'Salary Engine, Tax (PPh 21/TER), BPJS Ketenagakerjaan/Kesehatan, & Reimbursement',
                'status' => 'in_progress',
                'status_label' => 'Fondasi DB Siap',
                'color' => 'emerald',
                'gradient' => 'from-emerald-600 to-teal-600',
                'bg_glow' => 'bg-emerald-500/10 text-emerald-400 border-emerald-500/30',
                'route' => 'modules.show',
                'features' => [
                    'Kalkulasi PPh 21 Skema Tarif Efektif Rata-Rata (TER)',
                    'Perhitungan Iuran BPJS Ketenagakerjaan & Kesehatan',
                    'Formula Gaji Pokok, Tunjangan, & Potongan',
                    'Batch Export Transfer Payroll Bank (BCA, Mandiri, dll.)',
                    'Pengajuan & Klaim Reimbursement / Expense',
                ],
            ],
            'performance' => [
                'name' => 'Performance & OKR',
                'category' => 'Talent Management',
                'description' => 'KPI/OKR Tracking, 360 Degree Feedback, Talent Pool, & Succession Planning',
                'status' => 'planned',
                'status_label' => 'Segera Hadir',
                'color' => 'purple',
                'gradient' => 'from-purple-600 to-pink-600',
                'bg_glow' => 'bg-purple-500/10 text-purple-400 border-purple-500/30',
                'route' => 'modules.show',
                'features' => [
                    'Penyusunan Objective & Key Results (OKR)',
                    'Penilaian Kinerja Karyawan Berkala (KPI)',
                    'Evaluasi 360 Derajat (Atasan, Rekan, Bawahan)',
                    'Pemetaan 9-Box Matrix Talent Pool',
                    'Rencana Suksesi Kepemimpinan (Succession Planning)',
                ],
            ],
            'engagement' => [
                'name' => 'Engagement & Budaya',
                'category' => 'Employee Experience',
                'description' => 'Feed/Social Vibe, Polling, Rewards & Badges, serta Peer Recognition',
                'status' => 'planned',
                'status_label' => 'Segera Hadir',
                'color' => 'rose',
                'gradient' => 'from-rose-600 to-pink-600',
                'bg_glow' => 'bg-rose-500/10 text-rose-400 border-rose-500/30',
                'route' => 'modules.show',
                'features' => [
                    'Internal Social Feed & Pengumuman Perusahaan',
                    'Polling Cepat & Survei Kepuasan Karyawan',
                    'Sistem Gamifikasi, Badges, & Reward Points',
                    'Apresiasi Antar Karyawan (Kudos & Shoutouts)',
                    'Kalender Ulang Tahun & Perayaan Masa Kerja',
                ],
            ],
            'helpdesk' => [
                'name' => 'HR Helpdesk & Layanan',
                'category' => 'Internal Services',
                'description' => 'Ticket Lifecycle, SLA Escalation, serta Manajemen Vendor Layanan',
                'status' => 'planned',
                'status_label' => 'Segera Hadir',
                'color' => 'sky',
                'gradient' => 'from-sky-600 to-blue-600',
                'bg_glow' => 'bg-sky-500/10 text-sky-400 border-sky-500/30',
                'route' => 'modules.show',
                'features' => [
                    'Tiket Pengaduan & Permintaan Layanan HR',
                    'Pelacakan Service Level Agreement (SLA)',
                    'Eskalasi Tiket Otomatis Berjenjang',
                    'Pusat Bantuan & Knowledge Base Karyawan',
                    'Manajemen Vendor Asuransi & Rekanan Bisnis',
                ],
            ],
            'analytics' => [
                'name' => 'People Analytics & BI',
                'category' => 'Strategic Insights',
                'description' => 'Aggregated Read-Models, Export Services, & Predictive Turnover Insights',
                'status' => 'planned',
                'status_label' => 'Segera Hadir',
                'color' => 'teal',
                'gradient' => 'from-teal-600 to-emerald-600',
                'bg_glow' => 'bg-teal-500/10 text-teal-400 border-teal-500/30',
                'route' => 'modules.show',
                'features' => [
                    'Dashboard Analitik Eksekutif Real-time',
                    'Analisis Tingkat Turnover & Retensi Karyawan',
                    'Prediksi Tren SDM berbasis Machine Learning',
                    'Export Data Laporan Kemenaker & Manajemen',
                    'Agregasi Biaya Tenaga Kerja (Labor Cost Analytics)',
                ],
            ],
        ];
    }

    /**
     * Show the main Enterprise App Launcher / Module Hub.
     */
    public function index(): View
    {
        $domains = self::getDomains();

        $stats = [
            'total_employees' => Employee::count(),
            'total_departments' => Department::count(),
            'total_positions' => Position::count(),
        ];

        return view('portal.index', compact('domains', 'stats'));
    }

    /**
     * Show the preview and roadmap for an unreleased module.
     */
    public function show(string $module): View
    {
        $domains = self::getDomains();

        abort_if(! isset($domains[$module]), 404);

        $domain = $domains[$module];
        $domain['slug'] = $module;

        return view('portal.module-preview', compact('domain', 'domains'));
    }
}
