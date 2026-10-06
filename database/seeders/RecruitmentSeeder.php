<?php

namespace Database\Seeders;

use App\Models\Department;
use App\Models\JobPosting;
use App\Models\Psychotest;
use App\Models\User;
use Illuminate\Database\Seeder;

class RecruitmentSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::where('email', 'admin@hris.corp')->first();
        $deptIT = Department::where('code', 'IT')->first();
        $deptHC = Department::where('code', 'HC')->first();
        $deptFIN = Department::where('code', 'FIN')->first();

        // 1. Modul Psikotes: Tes Kraepelin & Tes Kepribadian Skala Likert 1-5
        $kraepelinTest = Psychotest::updateOrCreate(
            ['title' => 'Tes Kraepelin (Kecepatan, Ketelitian & Ketahanan Kerja)'],
            [
                'test_type' => Psychotest::TYPE_KRAEPELIN,
                'description' => 'Tes penjumlahan deret angka berkecepatan tinggi untuk mengukur ritme ketahanan konsentrasi, kecepatan kalkulasi, dan tingkat ketelitian di bawah tekanan waktu per kolom.',
                'duration_minutes' => 5,
                'passing_score' => 70,
                'is_active' => true,
                'questions_data' => [
                    'columns_count' => 6,
                    'seconds_per_column' => 20,
                    'rows_per_column' => 25,
                    'instructions' => 'Jumlahkan 2 angka berurutan dari bawah ke atas. Ketik digit terakhir dari hasil penjumlahan (misal: 7 + 8 = 15, ketik 5; 3 + 4 = 7, ketik 7). Kolom akan otomatis berpindah setelah waktu habis.',
                ],
            ]
        );

        $personalityTest = Psychotest::updateOrCreate(
            ['title' => 'Tes Karakter, Sikap Kerja & Penilaian Diri (Skala Likert 1-5)'],
            [
                'test_type' => Psychotest::TYPE_LIKERT_PERSONALITY,
                'description' => 'Asesmen kepribadian berbasis respon pernyataan diri untuk memetakan integritas, kerjasama tim, ketahanan stres, inisiatif, dan orientasi kerja profesional.',
                'duration_minutes' => 15,
                'passing_score' => 70,
                'is_active' => true,
                'questions_data' => [
                    'scale' => [
                        1 => 'Sangat Tidak Sesuai',
                        2 => 'Tidak Sesuai',
                        3 => 'Netral / Ragu-Ragu',
                        4 => 'Sesuai',
                        5 => 'Sangat Sesuai',
                    ],
                    'questions' => [
                        ['id' => 1, 'dimension' => 'Integritas & Kejujuran', 'statement' => 'Saya selalu mematuhi etika profesi dan bertindak jujur meskipun tanpa pengawasan langsung dari atasan.'],
                        ['id' => 2, 'dimension' => 'Ketahanan & Disiplin', 'statement' => 'Saya konsisten menyelesaikan tugas kerja yang menantang secara tuntas tepat waktu sebelum batas tenggat.'],
                        ['id' => 3, 'dimension' => 'Kerjasama Tim', 'statement' => 'Saya memprioritaskan keberhasilan dan keselarasan tim dibandingkan apresiasi individual semata.'],
                        ['id' => 4, 'dimension' => 'Stabilitas Emosi', 'statement' => 'Saya mampu mengendalikan emosi dan tetap berpikir jernih saat berada di bawah tekanan kerja tinggi.'],
                        ['id' => 5, 'dimension' => 'Inisiatif & Solusi', 'statement' => 'Saya proaktif mencari solusi alternatif ketika cara kerja konvensional menemui hambatan.'],
                        ['id' => 6, 'dimension' => 'Ketelitian & Detail', 'statement' => 'Saya teliti memeriksa kembali hasil pekerjaan sebelum mengirimkannya kepada atasan atau pemangku kepentingan.'],
                        ['id' => 7, 'dimension' => 'Adaptabilitas', 'statement' => 'Saya mudah beradaptasi dengan perubahan teknologi, alur kerja, maupun target operasional.'],
                        ['id' => 8, 'dimension' => 'Akuntabilitas', 'statement' => 'Saya berani mengakui kesalahan kerja secara terbuka dan segera mengambil langkah korektif tanpa mencari alasan.'],
                        ['id' => 9, 'dimension' => 'Kepemimpinan', 'statement' => 'Saya terdorong untuk menginspirasi dan mendukung rekan kerja lain agar bersama-sama mencapai performa terbaik.'],
                        ['id' => 10, 'dimension' => 'Manajemen Waktu', 'statement' => 'Saya selalu menyusun skala prioritas harian agar pekerjaan terorganisir dan tidak tumpang tindih.'],
                        ['id' => 11, 'dimension' => 'Keterbukaan Feedback', 'statement' => 'Saya menyambut baik kritik konstruktif sebagai sarana pembelajaran untuk pengembangan kompetensi saya.'],
                        ['id' => 12, 'dimension' => 'Komitmen Kerja', 'statement' => 'Saya memiliki dedikasi tinggi terhadap visi perusahaan dan bersedia memberikan kontribusi lebih ketika dibutuhkan.'],
                        ['id' => 13, 'dimension' => 'Komunikasi Asertif', 'statement' => 'Saya mampu menyampaikan argumen atau perbedaan pandangan secara santun, jelas, dan berorientasi solusi.'],
                        ['id' => 14, 'dimension' => 'Kemandirian Belajar', 'statement' => 'Saya terus memperbarui wawasan dan keterampilan profesional saya secara mandiri tanpa harus diperintah.'],
                        ['id' => 15, 'dimension' => 'Ketahanan Mental', 'statement' => 'Saya tidak mudah menyerah atau putus asa saat menghadapi penolakan ide atau kegagalan awal dalam proyek.'],
                    ],
                ],
            ]
        );

        // 2. Daftar Lowongan Kerja Sampel
        $jobs = [
            [
                'title' => 'Senior Backend Engineer (Go & Laravel)',
                'slug' => 'senior-backend-engineer-go-laravel',
                'department_id' => $deptIT?->id,
                'employment_type' => 'FULL_TIME',
                'work_model' => 'HYBRID',
                'location' => 'Jakarta HQ (Kuningan)',
                'experience_level' => 'Senior / Lead',
                'min_salary' => 18000000,
                'max_salary' => 28000000,
                'is_salary_visible' => true,
                'quota' => 2,
                'deadline' => now()->addDays(30),
                'status' => 'PUBLISHED',
                'description' => 'Kami mencari Senior Backend Engineer yang berpengalaman dalam merancang dan mengembangkan arsitektur microservices performa tinggi untuk ekosistem HRIS Enterprise. Anda akan berkolaborasi erat dengan tim Core Engine dan tim DevOps untuk memastikan keandalan, skalabilitas, dan keamanan data korporat.',
                'requirements' => "• Pengalaman minimal 4+ tahun dalam pengembangan software backend menggunakan Laravel / PHP 8+ atau Golang.\n• Pemahaman mendalam mengenai arsitektur PostgreSQL, optimasi query, indexing, dan locking mekanism.\n• Berpengalaman dengan Redis caching, message queue, dan event-driven architecture.\n• Memahami praktik CI/CD, Docker, Kubernetes, dan Automated Testing (Unit & Feature).\n• Kemampuan komunikasi yang baik dan kepemimpinan teknis dalam mentoring junior developer.",
                'benefits' => "• Gaji kompetitif + Performance Bonus Tahunan\n• BPJS Ketenagakerjaan & Kesehatan Kelas 1\n• Asuransi Rawat Inap Swasta Top-tier (Keluarga tercover)\n• Fasilitas Laptop Macbook Pro / Thinkpad X1 Carbon\n• Tunjangan Wellness & Annual Learning Budget Rp 8.000.000\n• Fleksibilitas Hybrid (2 hari WFH, 3 hari WFO)",
            ],
            [
                'title' => 'Lead Frontend Engineer (Vue 3 / Modern Web)',
                'slug' => 'lead-frontend-engineer-vue-3',
                'department_id' => $deptIT?->id,
                'employment_type' => 'FULL_TIME',
                'work_model' => 'REMOTE',
                'location' => 'Remote - Indonesia',
                'experience_level' => 'Senior / Lead',
                'min_salary' => 17000000,
                'max_salary' => 26000000,
                'is_salary_visible' => true,
                'quota' => 1,
                'deadline' => now()->addDays(25),
                'status' => 'PUBLISHED',
                'description' => 'Bergabunglah untuk memimpin revolusi UI/UX pada modul-modul HR Enterprise kami. Anda akan membangun komponen web yang responsif, berkecepatan tinggi, serta mengadopsi standar desain visual modern (glassmorphism, micro-animations, dan dynamic state management).',
                'requirements' => "• 4+ tahun pengalaman dalam arsitektur modern web (Vue 3, TypeScript, Vite, Tailwind CSS).\n• Pemahaman kuat mengenai Component Lifecycle, State Management (Pinia), dan Web Accessibility (a11y).\n• Berpengalaman dalam optimasi Core Web Vitals dan client-side caching.\n• Familiar dengan integrasi RESTful API & WebSocket real-time updates.",
                'benefits' => "• 100% Full Remote Work dari mana saja di Indonesia\n• Tunjangan Setup Home Office (Kursi ergonomis & Monitor)\n• Asuransi Kesehatan Swasta Full Cover\n• Jam kerja fleksibel berorientasi hasil (Output-based)",
            ],
            [
                'title' => 'Talent Acquisition & Technical Recruiter Specialist',
                'slug' => 'talent-acquisition-technical-recruiter',
                'department_id' => $deptHC?->id,
                'employment_type' => 'FULL_TIME',
                'work_model' => 'HYBRID',
                'location' => 'Jakarta HQ',
                'experience_level' => 'Mid Level',
                'min_salary' => 9000000,
                'max_salary' => 14000000,
                'is_salary_visible' => true,
                'quota' => 1,
                'deadline' => now()->addDays(20),
                'status' => 'PUBLISHED',
                'description' => 'Bertanggung jawab atas keseluruhan end-to-end talent acquisition lifecycle, mulai dari sourcing kandidat tech & non-tech, screening CV, koordinasi interview, psikotes online, negosiasi offering, hingga kelancaran proses onboarding ke sistem HRIS Core.',
                'requirements' => "• Minimal 2-3 tahun pengalaman sebagai Tech Recruiter / Talent Acquisition Specialist.\n• Memahami dasar-dasar terminologi teknologi software engineering, data, dan produk digital.\n• Pengalaman mengoperasikan Applicant Tracking System (ATS), LinkedIn Recruiter, dan psikotes tools.\n• Keterampilan negosiasi yang luwes, empati tinggi, dan komunikasi yang sangat terstruktur.",
                'benefits' => "• Bonus Insentif Rekrutmen (Success Placement Bonus)\n• Asuransi Kesehatan & Rawat Jalan\n• Program pengembangan karir & sertifikasi HR internasional",
            ],
            [
                'title' => 'People Operations & HR Generalist',
                'slug' => 'people-operations-hr-generalist',
                'department_id' => $deptHC?->id,
                'employment_type' => 'FULL_TIME',
                'work_model' => 'ON_SITE',
                'location' => 'Jakarta HQ',
                'experience_level' => 'Mid Level',
                'min_salary' => 8500000,
                'max_salary' => 12500000,
                'is_salary_visible' => false,
                'quota' => 1,
                'deadline' => now()->addDays(28),
                'status' => 'PUBLISHED',
                'description' => 'Mengelola operasional kepegawaian harian termasuk administrasi kontrak PKWT/PKWTT, rekapitulasi data kehadiran & cuti, audit data personal, serta menjaga employee relations dan kepatuhan regulasi ketenagakerjaan.',
                'requirements' => "• S1 Psikologi, Hukum, atau Manajemen SDM.\n• 2+ tahun pengalaman di bidang People Operations / HR Generalist.\n• Paham regulasi UU Ketenagakerjaan / Cipta Kerja dan pelaporan kepesertaan BPJS.\n• Teliti, disiplin tinggi dalam kerahasiaan data karyawan.",
                'benefits' => "• BPJS Kesehatan & Ketenagakerjaan lengkap\n• Asuransi rawat jalan swasta\n• Makan siang & snack bar harian di kantor",
            ],
            [
                'title' => 'Tax & Payroll Accounting Specialist',
                'slug' => 'tax-payroll-accounting-specialist',
                'department_id' => $deptFIN?->id,
                'employment_type' => 'FULL_TIME',
                'work_model' => 'HYBRID',
                'location' => 'Jakarta HQ',
                'experience_level' => 'Senior / Lead',
                'min_salary' => 12000000,
                'max_salary' => 17000000,
                'is_salary_visible' => true,
                'quota' => 1,
                'deadline' => now()->addDays(35),
                'status' => 'PUBLISHED',
                'description' => 'Memimpin rekonsiliasi penggajian, perhitungan pajak PPh 21 dengan skema Tarif Efektif Rata-Rata (TER), pelaporan SPT Masa/Tahunan, dan rekonsiliasi iuran BPJS Ketenagakerjaan & Kesehatan.',
                'requirements' => "• S1 Akuntansi / Perpajakan dengan sertifikasi Brevet A & B.\n• Minimal 3+ tahun pengalaman dalam kompensasi, payroll batching, dan perhitungan PPh 21 skema TER terkini.\n• Sangat menguasai Microsoft Excel tingkat lanjut (Lookup, Pivot, Power Query, Macros) dan sistem HRIS Payroll.\n• Memiliki integritas tinggi dan ketelitian kalkulasi angka numerik.",
                'benefits' => "• Tunjangan Sertifikasi Brevet C & Perpajakan Lanjutan\n• Tunjangan Hari Raya (THR) + Insentif Tutup Buku Tahunan\n• Asuransi Swasta & BPJS",
            ],
            [
                'title' => 'UI/UX & Product Design Specialist',
                'slug' => 'ui-ux-product-designer',
                'department_id' => $deptIT?->id,
                'employment_type' => 'FULL_TIME',
                'work_model' => 'HYBRID',
                'location' => 'Bandung Hub',
                'experience_level' => 'Mid Level',
                'min_salary' => 10000000,
                'max_salary' => 16000000,
                'is_salary_visible' => true,
                'quota' => 1,
                'deadline' => now()->addDays(40),
                'status' => 'PUBLISHED',
                'description' => 'Bertanggung jawab menerjemahkan kebutuhan bisnis dan alur interaksi HRIS yang kompleks menjadi antarmuka pengguna yang anggun, intuitif, dan memenuhi standar estetika visual modern.',
                'requirements' => "• Portofolio desain UI/UX web enterprise / SaaS yang solid di Figma.\n• Pengalaman dalam menyusun dan merawat Design System (Tokens, Auto-layout, Variants).\n• Mengerti prinsip UX research, usability testing, dan user journey mapping.\n• Pemahaman mengenai batasan teknis implementasi CSS/Tailwind merupakan nilai tambah besar.",
                'benefits' => "• Fasilitas iPad Pro + Apple Pencil untuk ideasi sketsa visual\n• Langganan Figma Pro & Design Resources tahunan\n• Lingkungan kerja kreatif di Bandung Hub",
            ],
            [
                'title' => 'Internship: People Operations & Employer Branding',
                'slug' => 'internship-people-operations',
                'department_id' => $deptHC?->id,
                'employment_type' => 'INTERNSHIP',
                'work_model' => 'HYBRID',
                'location' => 'Jakarta HQ',
                'experience_level' => 'Entry Level',
                'min_salary' => 3500000,
                'max_salary' => 4500000,
                'is_salary_visible' => true,
                'quota' => 2,
                'deadline' => now()->addDays(15),
                'status' => 'PUBLISHED',
                'description' => 'Program magang berbayar (Paid Internship) selama 6 bulan untuk mahasiswa tingkat akhir atau fresh graduates. Anda akan belajar langsung mengenai talent engagement, pembuatan konten employer branding, dan koordinasi orientasi kandidat.',
                'requirements' => "• Mahasiswa tingkat akhir / Fresh graduate jurusan Psikologi, Komunikasi, DKV, atau Manajemen SDM.\n• Antusias, proaktif, dan memiliki kepekaan visual / storytelling yang menarik di media sosial.\n• Bersedia magang hybrid selama periode 6 bulan dengan komitmen penuh.",
                'benefits' => "• Uang saku bulanan kompetitif (Paid Internship)\n• Sertifikat resmi magang korporat\n• Jalur prioritas rekrutmen permanen (Management Trainee Fast-track)",
            ],
        ];

        foreach ($jobs as $jobData) {
            $jobData['created_by'] = $admin?->id;
            JobPosting::updateOrCreate(
                ['slug' => $jobData['slug']],
                $jobData
            );
        }
    }
}
