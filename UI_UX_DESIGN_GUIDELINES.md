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

## 5. Standar Spesifikasi Komponen Formulir (Form Components Specification)

Semua form input pada modul yang ada maupun modul baru yang akan dibangun (misal: Payroll, Performance Review, Onboarding, Leave Request, ESS Attendance, Candidate Profile) **wajib mengikuti spesifikasi seragam berikut**:

### A. Elemen Input, Select, & Textarea
* **Input Text, Date, Email, Telp, Number, Password:**
  ```html
  <input type="text" name="..." value="..." placeholder="..." 
         class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 text-slate-900 placeholder-slate-400 focus:bg-white focus:border-indigo-600 focus:ring-1 focus:ring-indigo-600 focus:outline-none rounded-xl text-sm transition-colors" />
  ```
* **Select Dropdown:**
  ```html
  <select name="..." class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 text-slate-900 rounded-xl text-sm focus:bg-white focus:border-indigo-600 focus:outline-none transition-colors">
  ```
* **Textarea:**
  ```html
  <textarea name="..." rows="3" placeholder="..." class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 text-slate-900 placeholder-slate-400 focus:bg-white focus:border-indigo-600 focus:outline-none rounded-xl text-sm transition-colors"></textarea>
  ```
* **Input Compact (di dalam Sub-Card / Tabel / Grid):**
  ```html
  <input type="text" class="px-3 py-2 bg-white border border-slate-200 text-slate-900 placeholder-slate-400 focus:outline-none focus:border-indigo-600 rounded-lg text-xs" />
  ```

### B. Label, Sub-Label, & Bantuan Form (Form Hints)
* **Label Utama:** `block text-xs font-semibold text-slate-700 mb-1.5`
* **Label Wajib:** `<span class="text-rose-500 font-bold">*</span>`
* **Sub-Label / Hint:** `text-xs text-slate-500` *(DILARANG menggunakan `text-slate-400` di latar putih karena terlalu pudar)*.
* **Judul Bagian Form (Section Title):** `text-xl font-bold text-slate-900`
* **Sub-Judul Grup (Group Subtitle):** `text-sm font-bold text-slate-900`

### C. Dropzone Unggah Berkas (Digital Documents Upload)
* Menggunakan kartu berbingkai putus-putus (*dashed*) dengan indikator status jelas:
  ```html
  <div class="p-4 sm:p-5 rounded-2xl bg-white border-2 border-dashed border-indigo-200 hover:border-indigo-400 transition-all flex flex-col justify-between shadow-xs">
      <!-- Badge Tersimpan / Wajib -->
      <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">✓ Tersimpan</span>
      <!-- File input dengan preview nama file -->
      <input type="file" onchange="updateFilenameDisplay(this, 'preview-id')" class="w-full text-xs text-slate-600 file:mr-2 file:py-2 file:px-3 file:rounded-xl file:border-0 file:bg-indigo-600 file:text-white file:text-xs file:font-bold hover:file:bg-indigo-500 file:cursor-pointer" />
      <div id="preview-id" class="text-[11px] font-bold text-indigo-700 hidden truncate"></div>
  </div>
  ```

### D. Tombol Aksi Bawah & Mobile Sticky Action Bar
* Di desktop: Tombol simpan utama berada di kanan bawah kartu formulir:
  `w-full sm:w-auto px-8 py-3.5 rounded-2xl font-extrabold text-sm text-white bg-gradient-to-r from-indigo-600 to-cyan-600 shadow-xl shadow-indigo-600/30`
* Di mobile: Ditambahkan sticky action bar di bawah layar:
  `md:hidden fixed bottom-0 left-0 right-0 z-50 bg-white/95 backdrop-blur-xl border-t border-slate-200 shadow-2xl p-3 flex items-center gap-3`

---

## 6. Larangan Regresi & Anti-Invisible Font (Strict Zero Regressions)

1. **ANTI-INVISIBLE FONT:**
   - **DILARANG KERAS** menyisipkan kelas `text-white` pada elemen `<input>`, `<select>`, `<textarea>`, `<label>`, `<span>`, `<h1>`, `<h2>`, atau `<h3>` di dalam kontainer berlatar terang (`bg-white` atau `bg-slate-50`).
   - Nilai input dan teks ketikan user **WAJIB** `text-slate-900` agar terbaca dengan kontras sempurna.
   - Kelas `text-white` **HANYA** diperbolehkan pada tombol warna solid pekat / tombol gradien (`bg-indigo-600 text-white`, `bg-gradient-to-r text-white`) atau badge berlatar gelap.
2. **ANTI-DARK LEAKS:**
   - Dilarang meninggalkan elemen dark-mode sisa (`bg-slate-900`, `bg-slate-950`, `border-slate-700`) di dalam layout terang.
3. **ANTI-REGRESI INTERAKTIF:**
   - Status visual `peer-checked`, indikator nomor jawaban ujian, dan keypad aktif Kraepelin tidak boleh dihilangkan.
4. **STANDAR AKSESIBILITAS & MOBILE:**
   - Minimal ukuran touch target tombol interaktif adalah 44px (`min-height: 44px; touch-action: manipulation;`).
5. **VERIFIKASI WAJIB:**
   - Jalankan `vendor/bin/pint --dirty --format agent` untuk semua file PHP yang disentuh.
   - Jalankan `php artisan test --compact` dan pastikan seluruh test suite tetap lulus 100%.
   - Jalankan `npm run build` jika ada pembaruan frontend.
