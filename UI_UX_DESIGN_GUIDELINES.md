# Standar Desain UI/UX & Aturan Pengembangan Frontend (UI/UX Design System Guidelines)

Dokumen ini adalah **pedoman permanen (AI Rules)** untuk seluruh AI coding assistant dan pengembang dalam memodifikasi, menambah, atau merefaktor views antarmuka pengguna di proyek HRIS Core. Aturan ini bertujuan menjaga konsistensi desain, menghindari perubahan tema sepihak (*unwanted theme flips*), dan menjamin keramahan pengguna di perangkat mobile (*mobile-first*).

---

## 1. Filosofi Tema: Konsisten Light Theme (Tema Terang)

1. **Dilarang keras mengubah tema aplikasi menjadi Dark Mode murni** (`bg-slate-900`, `bg-slate-950`, border gelap) tanpa persetujuan eksplisit dari pengguna.
2. Seluruh modul (Candidate Portal, Recruitment ATS, Attendance, Core HR, Auth, dan Portal Hub) **wajib menggunakan palet Modern Light Theme**:
   - **Background Halaman / Body:** `bg-slate-50` (abu-abu terang lembut, bukan putih polos silau).
   - **Background Kartu & Panel:** `bg-white` dengan border halus `border-slate-200` atau `border-slate-200/90`.
   - **Background Input / Kontrol:** `bg-slate-50 border border-slate-200 text-slate-900 focus:bg-white focus:border-indigo-600 focus:ring-1 focus:ring-indigo-600`.
   - **Heading / Judul Utama:** `text-slate-900 font-extrabold` atau `font-bold`.
   - **Teks Paragraf / Konten:** `text-slate-700` atau `text-slate-600`.
   - **Teks Pendukung / Muted:** `text-slate-500` atau `text-slate-400`.
   - **Bayangan Elemen (Elevation):** Gunakan `shadow-xs`, `shadow-sm`, atau `shadow-md` bervolume lembut.

---

## 2. Diferensiasi Tombol & Pilihan Jawaban (Clicked vs. Unclicked State)

Setiap elemen interaktif (terutama pada tes psikotes, kuis, evaluasi, tab, dan seleksi filter) **wajib membedakan keadaan sebelum diklik vs. setelah diklik** dengan kontras visual tinggi:

### A. Skala Likert 1–5 (Tes Kepribadian / Gambaran Diri)
* **Sebelum Diklik (Unclicked):**
  - Latar `bg-slate-50` hover `hover:bg-slate-100`, border `border-2 border-slate-200`, teks `text-slate-700`.
* **Setelah Diklik (Clicked / Checked):**
  - Menggunakan warna solid bervolume, border warna tegas, cincin indikator tebal (`ring-4`), dan bayangan aktif:
    - **Nilai 1 (Sangat Tidak Sesuai):** `peer-checked:bg-rose-500 peer-checked:border-rose-600 peer-checked:text-white peer-checked:ring-4 peer-checked:ring-rose-500/20`
    - **Nilai 2 (Tidak Sesuai):** `peer-checked:bg-amber-500 peer-checked:border-amber-600 peer-checked:text-white peer-checked:ring-4 peer-checked:ring-amber-500/20`
    - **Nilai 3 (Netral / Ragu):** `peer-checked:bg-slate-600 peer-checked:border-slate-700 peer-checked:text-white peer-checked:ring-4 peer-checked:ring-slate-500/20`
    - **Nilai 4 (Sesuai):** `peer-checked:bg-blue-600 peer-checked:border-blue-700 peer-checked:text-white peer-checked:ring-4 peer-checked:ring-blue-500/20`
    - **Nilai 5 (Sangat Sesuai):** `peer-checked:bg-emerald-600 peer-checked:border-emerald-700 peer-checked:text-white peer-checked:ring-4 peer-checked:ring-emerald-500/20`
* **Badge Status:** Kartu pertanyaan wajib memunculkan badge hijau `✓ Terjawab` saat opsi dipilih (menggantikan badge netral `Belum`).

### B. Tes Pilihan Ganda (A, B, C, D)
* **Sebelum Dipilih:** Kartu `bg-slate-50 hover:bg-slate-100 border-2 border-slate-200`, badge huruf latar putih border abu-abu.
* **Setelah Dipilih:** Kartu `peer-checked:border-indigo-600 peer-checked:bg-indigo-50/90 peer-checked:ring-4 peer-checked:ring-indigo-500/20`, badge huruf terisi warna solid ungu tua `peer-checked:[&_.opt-key]:bg-indigo-600 peer-checked:[&_.opt-key]:text-white` dengan teks pertanyaan tebal `peer-checked:[&_.opt-text]:text-indigo-950 font-bold`.

### C. Tes Kraepelin (Pauli Speed Math)
* **Kolom Aktif:** Diberi highlight `bg-blue-50/80 border-2 border-blue-500 shadow-md ring-4 ring-blue-500/10`.
* **Digit yang Dihitung:** Pasangan digit yang harus dijumlahkan disorot gradien solid `bg-gradient-to-tr from-blue-600 to-indigo-600 text-white font-black scale-110 shadow-md`.
* **Umpan Balik Visual Instan:** Flash hijau saat kalkulasi benar dan flash merah lembut saat salah.
* **Numpad Interaktif:** Tombol numpad memiliki status tekan `active:scale-90 active:bg-blue-700 active:text-white`.

---

## 3. Aturan Standar Mobile-Friendly (Khususnya Role Non-HR)

Peran non-HR (Pelamar pada Portal Karir, Peserta Tes Online, dan Karyawan pada Presensi ESS) mayoritas mengakses sistem melalui perangkat seluler / smartphone. Wajib menerapkan prinsip berikut:

### A. Kenyamanan Sentuhan (Touch Target & Delay)
* Tinggi tombol interaktif minimal **44px hingga 48px** (`h-12` sampai `h-14` untuk numpad).
* Pada virtual keypad atau tombol kalkulasi cepat, wajib menyematkan `style="touch-action: manipulation;"` untuk menghilangkan jeda *double-tap zoom delay* (300ms) di browser mobile.
* Gunakan efek `active:scale-95` untuk feedback taktil instan di layar sentuh.

### B. Posisi Sticky Bottom untuk Tombol Penting
* Virtual keypad pada tes Kraepelin dan tombol submit pada tes kepribadian/general wajib menggunakan `sticky bottom-2` atau `sticky bottom-3` dengan latar `backdrop-blur-xl bg-white/95 border border-slate-200 shadow-xl` agar selalu berada dalam jangkauan ibu jari (*thumb zone*) tanpa perlu scroll berulang kali.

### C. Viewport & Kolom Scrolling Kraepelin
* Wrapper kolom Kraepelin wajib menggunakan `justify-start sm:justify-center` pada kontainer `overflow-x-auto` agar Kolom 1 tidak terpotong di layar 360px–400px.
* Saat berpindah kolom ("PINDAH!"), sistem JavaScript wajib secara otomatis memposisikan kolom aktif ke tengah layar:
  ```javascript
  const activeCol = document.getElementById(`col-${currentColumnIdx}`);
  if (activeCol) {
      activeCol.scrollIntoView({ behavior: 'smooth', inline: 'center', block: 'nearest' });
  }
  ```

### D. Linimasa Seleksi (8-Stage Pipeline Stepper)
* Pada layar mobile, tahapan seleksi ditampilkan sebagai rel geser horizontal (*horizontal scroll rail*):
  `flex overflow-x-auto pb-3 gap-2.5 sm:grid sm:grid-cols-4 lg:grid-cols-8 sm:overflow-visible`
* Hindari memotong teks label tahapan (*no awkward truncate*); gunakan `break-words leading-tight`.

### E. Formulir & Responsivitas Tata Letak
* Semua form input multi-kolom wajib beralih ke 1 kolom pada ponsel (`grid-cols-1 sm:grid-cols-2 lg:grid-cols-3`).
* Tombol CTA utama (Lamar Sekarang, Simpan Profil, Mulai Tes) wajib melebar penuh pada ponsel (`w-full sm:w-auto text-center justify-center`).
* Halaman autentikasi (Login/Register) **dilarang menggunakan `overflow-hidden` pada body**; wajib menggunakan `min-h-screen py-8 overflow-y-auto` agar formulir tetap dapat digulir ketika keyboard virtual ponsel muncul.

---

## 4. Hirarki Warna Modul (Color Identity Mapping)

| Domain / Modul | Warna Utama | Tailwind Classes |
| :--- | :--- | :--- |
| **Core HR & Kepegawaian** | Indigo / Violet | `bg-indigo-600 text-white shadow-indigo-600/30`, badge: `bg-indigo-50 text-indigo-700 border-indigo-200` |
| **Recruitment & ATS** | Cyan / Sky | `bg-cyan-600 text-white shadow-cyan-600/30`, badge: `bg-cyan-50 text-cyan-700 border-cyan-200` |
| **Time & Attendance (ESS)** | Amber / Warm Yellow | `bg-amber-600 text-white shadow-amber-600/30`, badge: `bg-amber-50 text-amber-700 border-amber-200` |
| **Candidate Portal (Karir)** | Indigo / Sky Gradient | `from-indigo-600 to-cyan-600`, badge: `bg-indigo-50 text-indigo-700 border-indigo-200` |
| **Status Sukses / Lulus / Hired** | Emerald / Hijau | `bg-emerald-50 text-emerald-700 border-emerald-200` |
| **Status Gagal / Ditolak / Danger** | Rose / Merah | `bg-rose-50 text-rose-700 border-rose-200` |
| **Status Menunggu / Pending / Review** | Amber / Oranye | `bg-amber-50 text-amber-700 border-amber-200` |

---

## 5. Larangan Regresi (Do Not Break Rules)

1. **JANGAN** mengganti kelas warna terang kembali ke kelas dark theme (`bg-slate-900`, `bg-slate-950`, `bg-slate-800`).
2. **JANGAN** membuat teks putih (`text-white`) di atas latar terang (`bg-white` atau `bg-slate-50`). Pastikan rasio kontras selalu terbaca jelas (`text-slate-900` atau `text-slate-700`).
3. **JANGAN** menghapus status visual `peer-checked` pada formulir jawaban tes.
4. **JANGAN** menghilangkan tombol `sticky` atau `touch-action: manipulation` pada fitur ujian mobile.
5. Jalankan `vendor/bin/pint --dirty --format agent` dan pastikan seluruh test suite `php artisan test --compact` tetap lulus 100% setelah setiap perubahan kode.
