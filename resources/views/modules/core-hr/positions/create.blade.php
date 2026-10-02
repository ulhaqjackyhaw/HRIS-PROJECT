@extends('layouts.app')

@section('header', 'Tambah Formasi Jabatan')

@section('content')
<div class="max-w-3xl mx-auto space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-xl font-bold text-slate-900">Formulir Jabatan</h2>
            <p class="text-sm text-slate-500">Definisikan kursi/posisi jabatan, tingkatan level, dan jalur karir</p>
        </div>
        <a href="{{ route('positions.index') }}" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 text-sm font-semibold rounded-xl transition-colors">
            Kembali
        </a>
    </div>

    <div class="bg-white p-6 sm:p-8 rounded-2xl border border-slate-200/80 shadow-xs">
        <form action="{{ route('positions.store') }}" method="POST" class="space-y-6">
            @csrf

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                <!-- Kode Jabatan -->
                <div>
                    <label for="code" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-2">
                        Kode Jabatan <span class="text-rose-500">*</span>
                    </label>
                    <input type="text" 
                           id="code" 
                           name="code" 
                           value="{{ old('code') }}" 
                           placeholder="Contoh: POS-HR-01, MGR-FIN-01" 
                           required 
                           class="w-full px-4 py-2.5 text-sm bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:outline-none focus:ring-2 focus:ring-indigo-500">
                </div>

                <!-- Nama / Judul Jabatan -->
                <div>
                    <label for="title" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-2">
                        Nama Jabatan <span class="text-rose-500">*</span>
                    </label>
                    <input type="text" 
                           id="title" 
                           name="title" 
                           value="{{ old('title') }}" 
                           placeholder="Contoh: Payroll Specialist, HR Manager" 
                           required 
                           class="w-full px-4 py-2.5 text-sm bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:outline-none focus:ring-2 focus:ring-indigo-500">
                </div>
            </div>

            <!-- Departemen Penempatan -->
            <div>
                <label for="department_id" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-2">
                    Departemen / Unit Kerja
                </label>
                <select id="department_id" name="department_id" class="w-full px-4 py-2.5 text-sm bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    <option value="">— Pilih Departemen —</option>
                    @foreach ($departments as $dept)
                        <option value="{{ $dept->id }}" {{ old('department_id') == $dept->id ? 'selected' : '' }}>
                            {{ $dept->code }} - {{ $dept->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-6">
                <!-- Level Jabatan -->
                <div>
                    <label for="level" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-2">
                        Level Hirarki
                    </label>
                    <select id="level" name="level" class="w-full px-4 py-2.5 text-sm bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        <option value="Staff" {{ old('level') === 'Staff' ? 'selected' : '' }}>Staff</option>
                        <option value="Senior Staff" {{ old('level') === 'Senior Staff' ? 'selected' : '' }}>Senior Staff</option>
                        <option value="Supervisor" {{ old('level') === 'Supervisor' ? 'selected' : '' }}>Supervisor</option>
                        <option value="Assistant Manager" {{ old('level') === 'Assistant Manager' ? 'selected' : '' }}>Assistant Manager</option>
                        <option value="Manager" {{ old('level') === 'Manager' ? 'selected' : '' }}>Manager</option>
                        <option value="General Manager" {{ old('level') === 'General Manager' ? 'selected' : '' }}>General Manager</option>
                        <option value="Director" {{ old('level') === 'Director' ? 'selected' : '' }}>Director</option>
                    </select>
                </div>

                <!-- Jalur Karir -->
                <div>
                    <label for="career_path" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-2">
                        Jalur Karir
                    </label>
                    <select id="career_path" name="career_path" class="w-full px-4 py-2.5 text-sm bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        <option value="Struktural" {{ old('career_path') === 'Struktural' ? 'selected' : '' }}>Struktural</option>
                        <option value="Fungsional" {{ old('career_path') === 'Fungsional' ? 'selected' : '' }}>Fungsional</option>
                        <option value="Spesialis" {{ old('career_path') === 'Spesialis' ? 'selected' : '' }}>Spesialis</option>
                        <option value="Umum" {{ old('career_path') === 'Umum' ? 'selected' : '' }}>Umum</option>
                    </select>
                </div>

                <!-- Fungsi Pekerjaan -->
                <div>
                    <label for="job_function" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-2">
                        Fungsi Pekerjaan
                    </label>
                    <input type="text" 
                           id="job_function" 
                           name="job_function" 
                           value="{{ old('job_function') }}" 
                           placeholder="Contoh: HC Operations" 
                           class="w-full px-4 py-2.5 text-sm bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:outline-none focus:ring-2 focus:ring-indigo-500">
                </div>
            </div>

            <!-- Status Aktif -->
            <div class="flex items-center gap-3 pt-2">
                <input type="checkbox" 
                       id="is_active" 
                       name="is_active" 
                       value="1" 
                       {{ old('is_active', '1') ? 'checked' : '' }} 
                       class="w-4 h-4 text-indigo-600 rounded border-slate-300 focus:ring-indigo-500">
                <label for="is_active" class="text-sm font-medium text-slate-700">
                    Formasi Jabatan Aktif Tersedia
                </label>
            </div>

            <div class="pt-4 border-t border-slate-100 flex justify-end gap-3">
                <a href="{{ route('positions.index') }}" class="px-5 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 text-sm font-semibold rounded-xl transition-colors">
                    Batal
                </a>
                <button type="submit" class="px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold rounded-xl shadow-sm transition-all focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    Simpan Formasi Jabatan
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
