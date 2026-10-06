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

## 4. Larangan Regresi (Zero Regressions)
1. DILARANG kembali ke skema warna gelap (dark mode).
2. DILARANG membuat teks putih di atas latar terang atau sebaliknya (kontras wajib AA/AAA).
3. DILARANG menghapus status visual `peer-checked` atau indikator nomor yang sudah dijawab.
4. Pastikan `vendor/bin/pint --dirty --format agent` dijalankan dan `php artisan test --compact` tetap lulus 100%.
