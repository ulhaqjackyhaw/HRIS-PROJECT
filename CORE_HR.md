# Modul Core HR (Human Resource Core System)
## HRIS Enterprise Suite & Architecture Documentation

Modul **Core HR** merupakan pondasi inti (*master data backbone*) dan *state machine* sentral dari seluruh rangkaian sistem HRIS Enterprise. Seluruh modul lanjutan—seperti **Time & Attendance**, **Payroll Engine (PPh 21 TER & BPJS)**, **Performance Review (KPI/OKR)**, dan **Recruitment**—bergantung penuh pada integritas data organisasi dan siklus kepegawaian yang dikelola di dalam modul ini.

---

## 1. Arsitektur Database & Normalisasi Tabel

Struktur basis data modul Core HR telah dirancang mengikuti kaidah normalisasi 3NF (*Third Normal Form*) dengan database **PostgreSQL** untuk mencegah *god-table* serta memastikan konsistensi data jangka panjang.

```text
       ┌────────────────┐ 1       * ┌────────────────┐
       │  departments   │───────────│   positions    │
       └────────────────┘           └────────────────┘
                │ 1                         │ 1
                │                           │
                │ *                         │ *
       ┌─────────────────────────────────────────────┐
       │                  employees                  │
       └─────────────────────────────────────────────┘
          │ 1              │ 1               │ 1
          │ *              │ *               │ *
┌──────────────────┐ ┌──────────────────┐ ┌───────────────────────────┐
│employee_contracts│ │employee_education│ │ employee_career_histories │
└──────────────────┘ └──────────────────┘ └───────────────────────────┘
```

### Penjelasan Entitas & Tabel:

1. **`departments` (Master Unit Kerja):**
   * Mendukung struktur organisasi hierarki bertingkat (*parent-child relationship*), misal: `Direksi` &rarr; `Human Capital Management` &rarr; `People Operations`.
2. **`positions` (Master Formasi & Posisi Jabatan):**
   * Menyimpan formasi jabatan, tingkat level karir (*Staff, Senior, Lead, Manager, Direksi*), jalur karir (*Struktural, Spesialis, Fungsional*), dan fungsi pekerjaan.
3. **`employees` (Master Data Karyawan):**
   * **Organisasi & Akun:** Terhubung dengan `user_id` untuk ESS (*Employee Self-Service*), `department_id`, `position_id`, dan `manager_id` (atasan langsung).
   * **Data Personal & Kependudukan:** Nomor KTP (NIK KTP), nama lengkap, jenis kelamin, tanggal lahir, agama, status perkawinan, alamat KTP, dan alamat domisili.
   * **Kesiapan Payroll & Pajak (Tax Ready):** NPWP, status PTKP (*TK/0, K/1, K/2, dst.* untuk TER PPh 21), No. BPJS Ketenagakerjaan, No. BPJS Kesehatan, Nama Bank, dan Nomor Rekening.
   * **Custom Fields (JSONB PostgreSQL):** Fleksibilitas data dinamis tanpa perlu mengubah skema tabel (misal: ukuran seragam, golongan darah, kontak darurat).
4. **`employee_contracts` (Histori Perjanjian Kerja):**
   * Mencatat nomor kontrak, jenis kontrak (`PKWT-1`, `PKWT-2`, `PKWTT`, `MAGANG`), tanggal mulai, tanggal berakhir, dan dokumen lampiran.
5. **`employee_education` (Histori Pendidikan Formal):**
   * Tingkat pendidikan (`SMA/SMK`, `D3`, `D4/S1`, `S2`, `S3`), nama universitas/institusi, jurusan, tahun kelulusan, dan status pengakuan linier perusahaan.
6. **`employee_career_histories` (Audit Trail Pergerakan Karir):**
   * Mencatat jejak rekam mutasi, promosi, demosi, dan terminasi beserta No. Surat Keputusan (SK) Direksi tanpa menimpa data historis sebelumnya.

---

## 2. Dynamic Attribute Computation (Tanpa Kolom Statis)

Sesuai *best practice* arsitektur HRIS, data yang berubah seiring waktu **tidak disimpan statis** di tabel basis data:
* **Usia (Age):** Dihitung secara *on-the-fly* melalui accessor Eloquent `$employee->age` berdasarkan selisih `birth_date` terhadap tanggal hari ini.
* **Masa Kerja (Tenure):** Dihitung dinamis via `$employee->tenure` berdasarkan selisih `join_date` (TMT Masuk) terhadap hari ini (format: *X Tahun Y Bulan*).

---

## 3. State Machine: Employee Life-Cycle (Siklus Hidup Karyawan)

Siklus perjalanan pegawai dikelola menggunakan *finite-state machine* kolom `lifecycle_stage` di tabel `employees`:

```text
[1. ONBOARDING] ──> [2. ACTIVE] ──> [3. SUSPENDED] ──> [4. OFFBOARDING] ──> [5. TERMINATED]
  (Input Berkas)      (Operasional)    (Skorsing/Cuti)   (Notice / Clearance)  (Alumni/Paklaring)
```

| Tahapan Status | Deskripsi Bisnis | Peran di Sistem |
| :--- | :--- | :--- |
| **`ONBOARDING`** | Calon pegawai baru diterima dan sedang melengkapi berkas administrasi (KTP, NPWP, Ijazah, Buku Tabungan). | Penetapan NIK resmi, pencatatan TMT masuk (*join date*), pembuatan akun ESS. |
| **`ACTIVE`** | Karyawan aktif operasional. | Akses akun aktif (`is_active = true`), integrasi jadwal shift & absensi terbuka, sinkron dengan engine penggajian (*Payroll Ready*). |
| **`SUSPENDED`** | Karyawan menjalani masa skorsing atau cuti di luar tanggungan (*unpaid leave*). | Akses akun dinonaktifkan sementara, hak absensi ditangguhkan. |
| **`OFFBOARDING`** | Karyawan menjalani masa *one-month notice*, serah terima pekerjaan (*handover*), dan *exit clearance checklist*. | Memicu audit pengembalian aset kantor (laptop, ID card), verifikasi kasbon/utang dengan Finance. |
| **`TERMINATED`** | Karyawan telah resmi keluar (Resign, Habis Kontrak, Pensiun, atau PHK). | Akun dinonaktifkan (`is_active = false`), data diarsipkan secara aman untuk penerbitan surat paklaring. |

### Sinkronisasi Otomatis Transisi Karir:
Setiap pencatatan SK di [EmployeeCareerHistoryController.php](file:///c:/project/hris-core/app/Http/Controllers/EmployeeCareerHistoryController.php) akan otomatis menyinkronkan departemen, jabatan, dan `lifecycle_stage` terkini pada baris utama profil karyawan.

---

## 4. Struktur Folder Views & MVC Modular

Struktur template Blade untuk modul Core HR telah dirapikan ke dalam direktori modular **`resources/views/modules/core-hr/`**:

```text
resources/views/
├── auth/                               # Login & Logout Authentication
├── layouts/                            # Master Template Layout (layouts/app.blade.php)
├── portal/                             # Enterprise Module Launcher & Hub
└── modules/
    └── core-hr/                        # Modul Core HR
        ├── dashboard.blade.php         # Dashboard Eksekutif Core HR & Life-Cycle Tracker
        ├── departments/                # Manajemen Unit Kerja
        │   ├── index.blade.php
        │   ├── create.blade.php
        │   └── edit.blade.php
        ├── positions/                  # Manajemen Formasi Jabatan
        │   ├── index.blade.php
        │   ├── create.blade.php
        │   └── edit.blade.php
        └── employees/                  # Master Data Karyawan, Kontrak, & Karir
            ├── index.blade.php         # Direktori Pegawai (Filter Stage, Dept, Search)
            ├── create.blade.php        # Form Pendaftaran Karyawan Baru
            ├── show.blade.php          # Profil 360°, Timeline Karir, & Life-Cycle Bar
            └── edit.blade.php          # Edit Master Profil Karyawan
```

---

## 5. Fitur Utama Tampilan Core HR

1. **Dashboard Eksekutif Core HR:**
   * Metrik KPI: Total Karyawan, Karyawan Aktif, Total Departemen, Total Formasi Jabatan.
   * **Employee Life-Cycle Tracker Funnel:** Pemantauan jumlah personil di tiap fase (*Onboarding, Active, Suspended, Offboarding, Terminated*).
   * **Peringatan Kontrak PKWT (Early Warning System):** Notifikasi otomatis untuk kontrak kerja yang akan berakhir dalam 30 hari ke depan.
   * **Log Riwayat Mutasi & SK Terkini:** Audit trail pergerakan karir terbaru lintas divisi/jabatan.
2. **Direktori & Profil Karyawan 360°:**
   * Detail biodata kependudukan, pajak & perbankan.
   * Bar progress interaktif alur siklus kepegawaian.
   * Form modal pencatatan SK Mutasi, Promosi, dan Demosi.
   * Manajemen riwayat perpanjangan kontrak kerja (PKWT) dan arsip pendidikan.

---

## 6. Panduan Menjalankan Modul Core HR

### A. Konfigurasi Lingkungan (`.env`)
Pastikan koneksi database PostgreSQL telah disesuaikan:
```env
DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=hris_db
DB_USERNAME=postgres
DB_PASSWORD=your_password
```

### B. Migrasi & Seeder Master Data
```bash
# Jalankan migrasi tabel
php artisan migrate

# Jalankan seeder data awal organisasi & pegawai
php artisan db:seed
```

### C. Akun Login Default
* **URL Login:** `http://127.0.0.1:8000/login`
* **Email:** `admin@hris.corp`
* **Password:** `password`

### D. Menjalankan Server & Kompilasi Frontend
```bash
# Terminal 1: Web Server
php artisan serve

# Terminal 2: Vite Compiler (Tailwind CSS v4)
npm run build
# atau mode development:
npm run dev
```

### E. Eksekusi Unit & Feature Testing
Modul Core HR dilengkapi dengan pengujian otomatis PHPUnit yang mencakup alur CRUD Departemen, Posisi, Karyawan, Kontrak, Pendidikan, dan Transisi Karir:
```bash
php artisan test --filter=HrisMvcTest
```
*(Seluruh skenario pengujian lulus 100% tanpa error).*
