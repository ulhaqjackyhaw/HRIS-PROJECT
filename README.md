# HRIS Enterprise Suite (Human Resource Information System)

Sistem Informasi Sumber Daya Manusia (HRIS) tingkat enterprise berbasis **Laravel 12 (PHP 8.3)**, **PostgreSQL**, dan **Tailwind CSS v4** dengan arsitektur modular multi-domain.

---

## 🏛️ Arsitektur Domain Modul

Aplikasi dirancang menggunakan pemisahan modul yang terstruktur:

| Modul | Status | Dokumentasi & Panduan |
| :--- | :--- | :--- |
| **Core HR** | 🟢 **Aktif (Siap Pakai)** | 📖 [Baca Dokumentasi Lengkap Modul Core HR (CORE_HR.md)](./CORE_HR.md) |
| **Time & Attendance** | 🟢 **Aktif (Siap Pakai)** | 📖 [Baca Dokumentasi Modul Attendance (ATTENDANCE.md)](./ATTENDANCE.md) |
| **Payroll & Tax (PPh 21/TER)** | 🟡 *Tahap Integrasi* | Slip Gaji Otomatis, Perhitungan BPJS, TER PPh 21, & Bank Transfer |
| **Recruitment & ATS** | ⚪ *Roadmap* | Lowongan Kerja, Pipeline Pelamar, & CV Parsing |
| **Performance Review** | ⚪ *Roadmap* | KPI/OKR Tracking & 360° Appraisal Review |
| **Employee Engagement** | ⚪ *Roadmap* | Feed Perusahaan, Polling, & Reward Badges |
| **Helpdesk & Ticketing** | ⚪ *Roadmap* | SLA Escalation, Ticket Lifecycle, & Pengaduan |
| **Analytics & Reporting** | ⚪ *Roadmap* | Laporan Ketenagakerjaan & Analisis Turn-over |

---

## 🚀 Fitur Utama Modul Core HR

Dokumentasi lengkap arsitektur basis data, normalisasi tabel, dan state machine kepegawaian dapat dilihat pada:
👉 **[CORE_HR.md](./CORE_HR.md)**

* **Master Organisasi Hierarkis:** Departemen bertingkat (*parent-child*) & formasi jabatan struktural/fungsional.
* **Master Profil Karyawan 360°:** NIK resmi, TMT masuk, KTP, data pajak PTKP, BPJS, perbankan, dan custom fields JSONB.
* **Perhitungan Dinamis (Dynamic Computation):** Usia (*age*) dan masa kerja (*tenure*) dihitung otomatis via accessor tanpa kolom statis di database.
* **Employee Life-Cycle (Siklus Hidup Pegawai):** State machine alur status: `ONBOARDING` &rarr; `ACTIVE` &rarr; `SUSPENDED` &rarr; `OFFBOARDING` &rarr; `TERMINATED`.
* **Career Movement & Audit Trail:** Log histori mutasi, promosi, dan SK kepegawaian tanpa menimpa data awal.
* **Early Warning System Kontrak:** Notifikasi masa berlaku kontrak PKWT yang akan habis dalam 30 hari ke depan.

---

## 🛠️ Panduan Menjalankan Sistem

### 1. Kebutuhan Sistem
* PHP 8.3+
* PostgreSQL 14+
* Composer 2.x
* Node.js 20+ & NPM

### 2. Instalasi
```bash
# Clone repository
git clone git@github.com:ulhaqjackyhaw/HRIS-PROJECT.git
cd HRIS-PROJECT

# Install dependensi backend & frontend
composer install
npm install

# Setup environment
cp .env.example .env
php artisan key:generate

# Konfigurasi database di .env, kemudian jalankan migrasi & seeder
php artisan migrate --seed
php artisan storage:link
```

### 3. Menjalankan Aplikasi
```bash
# Terminal 1: Backend Server
php artisan serve

# Terminal 2: Frontend Bundler
npm run dev
```

Buka peramban pada `http://127.0.0.1:8000`:
* **Akun Admin Core HR:** `admin@hris.corp` / `password`
* **Akun Karyawan Uji Presensi:** `karyawan@hris.local` / `password123`

---

## 🧪 Pengujian Otomatis (Testing)

Aplikasi memiliki rangkaian uji fitur otomatis (PHPUnit) dengan cakupan penuh:
```bash
php artisan test
```