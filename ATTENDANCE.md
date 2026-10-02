# Modul Time & Attendance (Waktu, Presensi, & Lembur)
## HRIS Enterprise Suite & Architecture Documentation

Modul **Time & Attendance** merupakan modul operasional harian di dalam HRIS Enterprise yang mengelola:
1. **Shift Management** (Master shift, toleransi keterlambatan, shift lintas malam, dan roster penjadwalan kerja bulanan).
2. **Biometrics / Geo-location Check-in** (Presensi mandiri web/mobile dengan jepretan kamera wajah biometrik, pembacaan GPS realtime, validasi radius geofencing dengan rumus Haversine, serta pencegahan fake GPS).
3. **Overtime Management** (Pengajuan Surat Perintah Lembur / SPL, kalkulasi jam lembur, dan alur persetujuan bertingkat oleh HR/Atasan).

Modul ini dirancang secara modular dan independen tanpa menyentuh modul penggajian (Payroll), namun data rekonsiliasi kehadiran dan lembur yang dihasilkan telah dinormalisasi agar siap diintegrasikan dengan modul Payroll kapan pun dibutuhkan.

---

## 1. Arsitektur Alur Kerja Presensi (Workflow Architecture)

```text
┌────────────────────────────────────────────────────────────────────────┐
│                      1. SHIFT & POLICY MANAGEMENT                       │
│  ├── Master Shift Kerja (Pagi, Siang, Malam/Overnight, Fleksibel)     │
│  ├── Master Titik Lokasi Kantor (Koordinat Lat/Long + Radius Geofence) │
│  └── Roster Penjadwalan Bulanan Karyawan (Jadwal Kerja & Hari Libur)  │
└───────────────────────────────────┬────────────────────────────────────┘
                                    │
                                    ▼
┌────────────────────────────────────────────────────────────────────────┐
│             2. BIOMETRICS & GEO-LOCATION CHECK-IN ENGINE               │
│  ├── Capture Geolocation Real-time (GPS Lat/Lng via Browser API)       │
│  ├── Live Front Camera Biometric Snapshot (Webcam + Oval Guide Canvas) │
│  ├── Validasi Radius Geofencing (Kalkulasi Jarak Rumus Haversine)      │
│  ├── Anti-Fake GPS & Distance Restriction Verification                 │
│  └── Auto Calculate: On-Time / Late / Early-Leave / Total Work Minutes │
└───────────────────────────────────┬────────────────────────────────────┘
                                    │
                                    ▼
┌────────────────────────────────────────────────────────────────────────┐
│                    3. OVERTIME (LEMBUR) & SPL FLOW                     │
│  ├── Pengajuan Lembur Mandiri oleh Karyawan (Tanggal, Rentang Jam)    │
│  ├── Validasi Durasi Jam Lembur & Alasan Pekerjaan (Reason)            │
│  └── Workflow Approval Verifikator: PENDING ──> APPROVED / REJECTED    │
└───────────────────────────────────┬────────────────────────────────────┘
                                    │
                                    ▼
┌────────────────────────────────────────────────────────────────────────┐
│               4. ATTENDANCE MONITORING & AUDIT TRAIL LOG               │
│  ├── Rekapitulasi Presensi Harian (Hadir, Terlambat, Pulang Cepat)     │
│  ├── Audit Log Bukti Foto Snapshot Wajah & Jarak Meter ke Kantor       │
│  └── Ekspor & Integrasi Siap-Pakai ke Mesin Penggajian (Payroll Engine)│
└────────────────────────────────────────────────────────────────────────┘
```

---

## 2. Skema Database & Relasi Tabel

Struktur data modul Attendance dinormalisasi ke dalam 5 tabel utama di database **PostgreSQL**:

```text
┌────────────────────┐ 1        * ┌────────────────────┐
│  office_locations  │────────────│ employee_schedules │
└────────────────────┘            └────────────────────┘
                                            │ *
                                            │
                                            │ 1
┌────────────────────┐ 1        * ┌────────────────────┐ 1        * ┌────────────────────┐
│       shifts       │────────────│     employees      │────────────│    attendances     │
└────────────────────┘            └────────────────────┘            └────────────────────┘
                                            │ 1
                                            │
                                            │ *
                                  ┌────────────────────┐
                                  │ overtime_requests  │
                                  └────────────────────┘
```

### Detail Spesifikasi Tabel:

#### 1. `office_locations` (Master Titik Kantor & Radius Geofence)
* `id`: Primary key (UUID/BigInt).
* `name`: Nama kantor/cabang (misal: *Head Office Jakarta*, *Warehouse Cikarang*).
* `latitude` & `longitude`: Koordinat geografis presisi tinggi (`DECIMAL(10,8)` & `DECIMAL(11,8)`).
* `radius_meters`: Batas radius toleransi presensi dalam meter (contoh: `100` atau `150` meter).
* `address`: Alamat lengkap kantor.
* `is_active`: Status operasional lokasi.

#### 2. `shifts` (Master Jam Kerja & Toleransi)
* `id`: Primary key.
* `code` & `name`: Kode & label shift (misal: `SHIFT-PAGI`, `Regular Morning Shift`).
* `start_time` & `end_time`: Jam mulai dan jam selesai (format `H:i:s`).
* `late_tolerance_minutes`: Masa tenggang (*grace period*) keterlambatan (misal: `15` menit).
* `is_overnight`: Boolean penanda shift lintas malam hingga subuh hari berikutnya (misal: `21:00 - 06:00`).

#### 3. `employee_schedules` (Roster Penjadwalan Kerja Karyawan)
* `id`: Primary key.
* `employee_id`: Foreign key ke tabel master karyawan (`employees`).
* `shift_id`: Foreign key ke tabel master shift (`shifts`).
* `office_location_id`: Foreign key ke lokasi kantor penugasan (`office_locations`).
* `date`: Tanggal penugasan kerja (`DATE`).
* `is_day_off`: Boolean penanda hari libur terjadwal / akhir pekan.
* **Indeks Unik:** `['employee_id', 'date']` menjamin tidak ada jadwal ganda pada hari yang sama.

#### 4. `attendances` (Catatan Transaksi Presensi Harian)
* `id`: Primary key.
* `employee_id`: Foreign key ke tabel karyawan.
* `shift_id` & `office_location_id`: Salinan snapshot jadwal kerja hari berjalan.
* `date`: Tanggal presensi.
* **Data Clock-In (Masuk):**
  * `clock_in`: Timestamp waktu masuk.
  * `clock_in_lat`, `clock_in_lng`: Koordinat GPS saat clock-in.
  * `clock_in_distance_meters`: Jarak aktual ke titik kantor dalam meter.
  * `clock_in_photo_path`: Lokasi file foto snapshot biometrik wajah.
* **Data Clock-Out (Pulang):**
  * `clock_out`: Timestamp waktu pulang.
  * `clock_out_lat`, `clock_out_lng`: Koordinat GPS saat clock-out.
  * `clock_out_distance_meters`: Jarak aktual saat clock-out.
  * `clock_out_photo_path`: Lokasi file foto snapshot saat pulang.
* **Data Kalkulasi & Status:**
  * `status`: Enum (`PRESENT`, `LATE`, `EARLY_LEAVE`, `ABSENT`, `LEAVE`).
  * `late_minutes`: Jumlah menit keterlambatan (dihitung dari `start_time`).
  * `early_leave_minutes`: Jumlah menit pulang sebelum `end_time`.
  * `total_work_minutes`: Durasi akumulatif kerja bersih dalam menit.
* **Indeks Unik:** `['employee_id', 'date']`.

#### 5. `overtime_requests` (Surat Perintah Lembur / SPL)
* `id`: Primary key.
* `employee_id`: Karyawan yang mengajukan lembur.
* `date`: Tanggal pelaksanaan kerja lembur.
* `start_time` & `end_time`: Rentang waktu lembur.
* `total_hours`: Kalkulasi jam lembur (desimal, misal: `2.50` jam).
* `reason`: Justifikasi atau uraian tugas pekerjaan lembur.
* `status`: Status persetujuan (`PENDING`, `APPROVED`, `REJECTED`).
* `approved_by`: Foreign key ke karyawan/atasan yang menyetujui.
* `rejection_note`: Catatan alasan penolakan jika ditolak.

---

## 3. Logika Bisnis & Mesin Geofencing (`AttendanceService.php`)

Seluruh validasi presensi, matematika geofencing, dan kalkulasi waktu diisolasi di:
👉 [app/Services/AttendanceService.php](file:///c:/project/hris-core/app/Services/AttendanceService.php)

### A. Rumus Jarak Haversine (Anti-Fake GPS)
Sistem menggunakan perhitungan trigonometri bola bumi untuk mengukur jarak linier antara koordinat GPS perangkat karyawan $(lat_1, lon_1)$ dengan koordinat kantor $(lat_2, lon_2)$:

$$\Delta\phi = \text{deg2rad}(lat_2 - lat_1), \quad \Delta\lambda = \text{deg2rad}(lon_2 - lon_1)$$

$$a = \sin^2\left(\frac{\Delta\phi}{2}\right) + \cos(\text{rad}(lat_1)) \cdot \cos(\text{rad}(lat_2)) \cdot \sin^2\left(\frac{\Delta\lambda}{2}\right)$$

$$c = 2 \cdot \text{atan2}(\sqrt{a}, \sqrt{1 - a})$$

$$\text{distance} = R \cdot c \quad (\text{di mana } R = 6.371.000 \text{ meter})$$

> **Proteksi Geofence:** Jika $\text{distance} > \text{radius\_meters}$, permintaan presensi otomatis dibatalkan (`ValidationException`) dan sistem mencatat jarak pelanggaran.

### B. Otomatisasi Perhitungan Keterlambatan (*Grace Period*)
* Jadwal Shift: `08:00:00`, Toleransi: `15 menit` &rarr; Batas aman: `08:15:00`.
* Jika Clock-In dilakukan pukul `08:14:00` &rarr; Status dicatat `PRESENT` dan `late_minutes = 0`.
* Jika Clock-In dilakukan pukul `08:25:00` &rarr; Status berubah menjadi `LATE` dan `late_minutes = 25 menit` (dihitung secara presisi sejak jam mulai shift `08:00`).

### C. Otomatisasi Pulang Lebih Awal (*Early Leave*) & Durasi Kerja
* Saat Clock-Out dilakukan sebelum `end_time` shift, sistem menghitung selisih menit kekurangan jam kerja dan menetapkan status `EARLY_LEAVE`.
* Akumulasi durasi kerja bersih (`total_work_minutes`) dihitung otomatis dari selisih `clock_in` hingga `clock_out`.

### D. Engine Biometrik Snapshot Dual-Input
* **Base64 Canvas Snapshot:** Mengambil frame gambar langsung dari stream webcam peramban (`data:image/jpeg;base64,...`), kemudian didekode dan disimpan ke storage server.
* **Direct Multipart File:** Mendukung pengunggahan berkas gambar langsung (`.jpg`, `.jpeg`, `.png`).

---

## 4. Struktur Folder & Desain Modular MVC

Seluruh antarmuka dan kontroler modul Attendance dikelompokkan secara terstruktur:

```text
app/
├── Http/Controllers/
│   ├── AttendanceController.php       # Controller Check-in mandiri & Monitoring log HR
│   ├── ShiftController.php            # Controller CRUD Master Shift & Grace Period
│   ├── OfficeLocationController.php   # Controller Master Titik Kantor & Radius Meter
│   ├── EmployeeScheduleController.php # Controller Roster Penjadwalan Bulanan Pegawai
│   └── OvertimeRequestController.php  # Controller Pengajuan Lembur & Approval Workflow
├── Models/
│   ├── Attendance.php                 # Model Presensi
│   ├── Shift.php                      # Model Shift
│   ├── OfficeLocation.php             # Model Titik Kantor
│   ├── EmployeeSchedule.php           # Model Roster
│   └── OvertimeRequest.php            # Model Lembur (SPL)
└── Services/
    └── AttendanceService.php          # Engine Validasi Haversine, Foto, & Status

resources/views/modules/attendance/
├── check-in.blade.php                 # Terminal Presensi Mandiri (Webcam + GPS Realtime)
├── index.blade.php                    # Monitoring Presensi Harian HR & Audit Trail
├── shifts/
│   └── index.blade.php                # Master Shift Kerja & Toleransi Waktu
├── locations/
│   └── index.blade.php                # Master Titik Koordinat Kantor & Radius Geofence
├── schedules/
│   └── index.blade.php                # Kalender & Roster Penjadwalan Kerja
└── overtimes/
    └── index.blade.php                # Pengajuan Lembur & Panel Approval / Reject
```

---

## 5. Fitur Unggulan Terminal Presensi (`check-in.blade.php`)

Terminal presensi mandiri karyawan dilengkapi dengan fitur web modern:
1. **Live Camera Feed:** Stream kamera depan langsung via API browser `navigator.mediaDevices.getUserMedia`.
2. **Face Oval Guide Canvas:** Panduan bingkai oval transparan di tengah layar untuk memastikan foto selfie biometrik tepat di tengah dan tidak blur.
3. **Real-time GPS Meter:** Mendeteksi posisi GPS pengguna saat itu juga dan menghitung jarak meter ke kantor secara instan sebelum tombol absen ditekan.
4. **Adaptive Action Button:** Tombol presensi otomatis berubah secara cerdas:
   * Menjadi *"Clock-In Masuk"* jika belum presensi masuk.
   * Menjadi *"Clock-Out Pulang"* jika sudah presensi masuk namun belum pulang.
   * Menjadi *"Presensi Hari Ini Selesai"* jika sudah melakukan clock-in dan clock-out.
5. **Simulator Koordinat Localhost:** Tombol praktis khusus developer untuk mengisi koordinat simulator saat pengujian di laptop tanpa harus keluar kantor.

---

## 6. Panduan Menjalankan & Menguji Modul

### A. Migrasi Database & Seeder Data Uji Coba
Jalankan perintah berikut di terminal:
```bash
# 1. Jalankan migrasi tabel presensi
php artisan migrate

# 2. Isi data master (Kantor, Shift, Roster, Akun Karyawan Uji Coba)
php artisan db:seed --class=AttendanceModuleSeeder

# 3. Buat symlink storage untuk berkas foto snapshot
php artisan storage:link
```

### B. Akun Uji Coba Presensi
* **Halaman Login:** `http://127.0.0.1:8000/login`
* **Akun Karyawan (Self-Service Attendance):**
  * **Email:** `karyawan@hris.local`
  * **Password:** `password123`
* **Akun HR Administrator:**
  * **Email:** `admin@hris.corp`
  * **Password:** `password`

### C. Daftar Route / Menu Akses Cepat
| Fitur / Halaman | URL Akses Langsung |
| :--- | :--- |
| **Terminal Presensi Selfie (Karyawan)** | `http://127.0.0.1:8000/attendance/check-in` |
| **Monitoring Log Presensi (HR Admin)** | `http://127.0.0.1:8000/attendance/logs` |
| **Master Shift Kerja** | `http://127.0.0.1:8000/attendance/shifts` |
| **Master Titik Kantor & Radius** | `http://127.0.0.1:8000/attendance/locations` |
| **Roster Jadwal Kerja Pegawai** | `http://127.0.0.1:8000/attendance/schedules` |
| **Manajemen Lembur (SPL)** | `http://127.0.0.1:8000/attendance/overtimes` |

---

## 7. Hasil Pengujian Otomatis (Feature Tests)

Modul Attendance telah diverifikasi dengan serangkaian automated feature tests di [tests/Feature/AttendanceTest.php](file:///c:/project/hris-core/tests/Feature/AttendanceTest.php):

* ✅ Akses halaman terminal check-in.
* ✅ Clock-In berhasil di dalam radius geofence kantor (foto tersimpan, koordinat terekam).
* ✅ Clock-In gagal dan ditolak jika berada di luar radius geofence kantor.
* ✅ Deteksi otomatis keterlambatan (`LATE`) dan kalkulasi menit terlambat.
* ✅ Clock-Out berhasil dengan kalkulasi durasi kerja bersih dan deteksi `EARLY_LEAVE`.
* ✅ Manajemen Master Shift Kerja.
* ✅ Manajemen Master Lokasi Kantor & Radius Geofence.
* ✅ Pengajuan Lembur & Approval Workflow.

```bash
php artisan test --filter=AttendanceTest
# Result: 8 tests passed, 48 assertions

php artisan test
# Result: 27 tests passed, 149 assertions (100% Pass)
```

---

## 8. Panduan Git: Upload ke Branch Attendance

Gunakan perintah Git berikut untuk mengunggah modul ini ke branch `Modul-Attendance`:

```bash
# Pastikan berada di branch Modul-Attendance
git checkout Modul-Attendance

# Tambahkan seluruh berkas dokumentasi dan kode
git add ATTENDANCE.md README.md app/ resources/views/modules/attendance/ database/ tests/

# Buat commit deskriptif
git commit -m "feat(attendance): complete Shift Management, Biometrics/Geo-location Check-in, and Overtime module with documentation"

# Unggah ke repositori GitHub
git push -u origin Modul-Attendance
```
