# UI/UX & Frontend Design System Rules (HRIS Core)

Pedoman ini adalah aturan wajib yang harus ditaati oleh semua AI assistant dan developer ketika menambah, mengubah, atau memperbaiki tampilan frontend (Blade / Tailwind / JS) di proyek ini.

Rujukan lengkap dokumen desain: `UI_UX_DESIGN_GUIDELINES.md`.

---

## 1. Aturan Mutlak: Konsisten Modern Light Theme
- **DILARANG** mengubah layout atau halaman ke Dark Theme murni (`bg-slate-900`, `bg-slate-950`, dsb) tanpa persetujuan eksplisit user.
- **Standar Warna Palet:**
  - Halaman / Body: `bg-slate-50`
  - Kartu & Container: `bg-white border border-slate-200 shadow-xs/sm`
  - Kontrol Form / Input: `bg-slate-50 border border-slate-200 text-slate-900 focus:bg-white focus:border-indigo-600 focus:ring-1 focus:ring-indigo-600`
  - Teks Judul: `text-slate-900 font-bold / font-extrabold`
  - Teks Deskripsi: `text-slate-700` atau `text-slate-600`
  - Teks Sekunder: `text-slate-500`

---

## 2. Diferensiasi Interaktif (Clicked vs. Unclicked State)
Setiap pilihan jawaban atau tombol yang bisa dipilih **harus memiliki perbedaan visual yang mencolok dan instan**:
- **Skala Likert 1–5 (Tes Kepribadian / Gambaran Diri):**
  - Unclicked: `bg-slate-50 hover:bg-slate-100 border-2 border-slate-200 text-slate-700`
  - Clicked (1): `peer-checked:bg-rose-500 peer-checked:border-rose-600 peer-checked:text-white peer-checked:ring-4 peer-checked:ring-rose-500/20`
  - Clicked (2): `peer-checked:bg-amber-500 peer-checked:border-amber-600 peer-checked:text-white peer-checked:ring-4 peer-checked:ring-amber-500/20`
  - Clicked (3): `peer-checked:bg-slate-600 peer-checked:border-slate-700 peer-checked:text-white peer-checked:ring-4 peer-checked:ring-slate-500/20`
  - Clicked (4): `peer-checked:bg-blue-600 peer-checked:border-blue-700 peer-checked:text-white peer-checked:ring-4 peer-checked:ring-blue-500/20`
  - Clicked (5): `peer-checked:bg-emerald-600 peer-checked:border-emerald-700 peer-checked:text-white peer-checked:ring-4 peer-checked:ring-emerald-500/20`
  - Sertakan badge dinamis status `✓ Terjawab` saat opsi dipilih.
- **Pilihan Ganda:**
  - Unclicked: `bg-slate-50 border-2 border-slate-200`
  - Clicked: `peer-checked:border-indigo-600 peer-checked:bg-indigo-50/90 peer-checked:ring-4 peer-checked:ring-indigo-500/20` dengan badge huruf solid ungu `peer-checked:[&_.opt-key]:bg-indigo-600 peer-checked:[&_.opt-key]:text-white`.
- **Kraepelin Speed Math:**
  - Kolom aktif: `bg-blue-50/80 border-2 border-blue-500 shadow-md ring-4 ring-blue-500/10`
  - Pasangan angka yang dihitung: `bg-gradient-to-tr from-blue-600 to-indigo-600 text-white font-black scale-110 shadow-md`
  - Tombol numpad aktif: `active:scale-90 active:bg-blue-700 active:text-white`
  - Feedback kalkulasi: Flash hijau (benar) dan flash merah (salah).

---

## 3. Standar Mobile-Friendly (Khusus Non-HR & Publik)
- **Ukuran Sentuhan (Touch Target):** Minimal 44px–48px (`h-12` sampai `h-14` pada keypad ujian).
- **Zero Touch Delay:** Gunakan `style="touch-action: manipulation;"` pada tombol aksi cepat/numpad untuk menonaktifkan delay zoom browser 300ms.
- **Sticky Thumb Zone:** Tombol navigasi krusial/keypad pada layar ujian mobile harus dibuat `sticky bottom-2` atau `sticky bottom-3` dengan `backdrop-blur-xl bg-white/95 border border-slate-200`.
- **Kraepelin Auto-Center:** Jangan gunakan `justify-center` murni yang memotong kolom kiri pada mobile; gunakan `justify-start sm:justify-center` dan scroll otomatis kolom aktif dengan `scrollIntoView({ inline: 'center' })`.
- **Formulir Responsif:** Formulir multi-kolom harus stack menjadi 1 kolom di mobile (`grid-cols-1 sm:grid-cols-2`). Tombol submit utama harus `w-full sm:w-auto`.
- **Layar Login/Register:** Jangan gunakan `overflow-hidden` pada `body` agar formulir tidak terpotong saat keyboard virtual muncul.

---

## 4. Standar Formulir & Input Data (Form Styling Policy)
Semua formulir pendaftaran, profil, presensi, cuti, atau administrasi wajib menggunakan tata letak terang yang seragam:
- **Input Text, Date, Email, Tel, Number:**
  `w-full px-4 py-2.5 bg-slate-50 border border-slate-200 text-slate-900 placeholder-slate-400 rounded-xl text-sm focus:bg-white focus:border-indigo-600 focus:ring-1 focus:ring-indigo-600 focus:outline-none transition-colors`
- **Select Dropdown:**
  `w-full px-4 py-2.5 bg-slate-50 border border-slate-200 text-slate-900 rounded-xl text-sm focus:bg-white focus:border-indigo-600 focus:outline-none transition-colors`
- **Textarea:**
  `w-full px-4 py-2.5 bg-slate-50 border border-slate-200 text-slate-900 placeholder-slate-400 rounded-xl text-sm focus:bg-white focus:border-indigo-600 focus:outline-none transition-colors`
- **Input Form Dalam Grid/Tabel Compact:**
  `px-3 py-2 bg-white border border-slate-200 rounded-lg text-xs text-slate-900 placeholder-slate-400 focus:outline-none focus:border-indigo-600`
- **Label Formulir:**
  `block text-xs font-semibold text-slate-700 mb-1` (atau `mb-1.5`)
- **Teks Pendukung / Sub-Label / Hint:**
  `text-xs text-slate-500` (JANGAN gunakan `text-slate-400` yang terlalu tipis/pudar di latar putih).
- **Sub-Kartu / Grup Form Turunan:**
  `p-4 sm:p-5 rounded-2xl bg-slate-50 border border-slate-200`
- **Upload File / Dropzone Card:**
  `p-4 sm:p-5 rounded-2xl bg-white border-2 border-dashed border-indigo-200 hover:border-indigo-400 flex flex-col justify-between`
  - Sertakan badge status `✓ Tersimpan` (`bg-emerald-50 text-emerald-700 border-emerald-200`) atau `Wajib` (`bg-rose-50 text-rose-700 border-rose-200`).
  - Sertakan fungsi live feedback nama file saat dipilih (`updateFilenameDisplay(this, targetId)`).
  - Tombol file input: `file:mr-2 file:py-2 file:px-3 file:rounded-xl file:border-0 file:bg-indigo-600 file:text-white file:text-xs file:font-bold hover:file:bg-indigo-500 file:cursor-pointer`.
- **Radio & Checkbox:**
  `text-indigo-600 focus:ring-indigo-500` dengan label yang jelas `text-xs text-slate-800 font-medium`.

---

## 5. Larangan Regresi & Anti-Invisible Font (Strict Zero Regressions)
1. **DILARANG membuat teks putih (`text-white`) di atas latar terang (`bg-white` atau `bg-slate-50`):**
   - Periksa seluruh atribut `class` pada elemen `<input>`, `<select>`, `<textarea>`, `<label>`, `<span>`, dan `<h3>`.
   - `text-white` HANYA diperbolehkan pada tombol berwarna pekat/gradien (misal `bg-indigo-600 text-white`, `bg-gradient-to-r text-white`) atau badge berlatar gelap.
2. **DILARANG meninggalkan elemen dark-mode sisa:**
   - Dilarang menyisipkan `bg-slate-900`, `bg-slate-950`, atau `border-slate-700` di dalam form atau card bertema terang.
3. **DILARANG menghapus status visual interaktif:**
   - Status `peer-checked`, indikator nomor jawaban, dan keypad aktif Kraepelin tidak boleh dihilangkan.
4. **Validasi Kualitas Kode:**
   - Jalankan `vendor/bin/pint --dirty --format agent` untuk semua file PHP yang disentuh.
   - Jalankan `php artisan test --compact` untuk memastikan 100% tes tetap lulus.
   - Jalankan `npm run build` jika ada pembaruan frontend.
