@extends('layouts.app', ['title' => 'Edit Lowongan Pekerjaan'])

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between bg-white border border-slate-200/90 p-6 rounded-3xl shadow-xs">
        <div>
            <a href="{{ route('recruitment.jobs.index') }}" class="text-xs font-semibold text-slate-500 hover:text-indigo-600 transition-colors">
                ← Kembali ke Daftar Lowongan
            </a>
            <h1 class="text-2xl font-extrabold text-slate-900 mt-1">Edit Lowongan: {{ $job->title }}</h1>
        </div>

        <a href="{{ route('career.jobs.show', $job->slug) }}" target="_blank" class="px-4 py-2 rounded-xl text-xs font-bold text-cyan-700 bg-cyan-50 hover:bg-cyan-100 border border-cyan-200 transition-colors">
            Lihat di Portal ↗
        </a>
    </div>

    @if($errors->any())
        <div class="p-4 rounded-2xl bg-rose-50 border border-rose-200 text-rose-700 text-xs space-y-1">
            <div class="font-bold">Harap periksa isian form:</div>
            @foreach($errors->all() as $err)
                <div>• {{ $err }}</div>
            @endforeach
        </div>
    @endif

    <form action="{{ route('recruitment.jobs.update', $job->id) }}" method="POST" class="p-8 rounded-3xl bg-white border border-slate-200/90 shadow-xs space-y-6">
        @csrf
        @method('PUT')

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
            <div class="sm:col-span-2">
                <label class="block text-xs font-bold text-slate-700 mb-1.5">Judul Posisi Pekerjaan*</label>
                <input type="text" name="title" value="{{ old('title', $job->title) }}" required class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm text-slate-900 focus:outline-none focus:bg-white focus:border-cyan-600" />
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1.5">Departemen / Unit Kerja*</label>
                <select name="department_id" required class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm text-slate-900 focus:outline-none focus:bg-white focus:border-cyan-600">
                    @foreach($departments as $dept)
                        <option value="{{ $dept->id }}" {{ old('department_id', $job->department_id) == $dept->id ? 'selected' : '' }}>{{ $dept->name }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1.5">Jenjang Karir (Job Level)*</label>
                <select name="job_level" required class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm text-slate-900 focus:outline-none focus:bg-white focus:border-cyan-600">
                    @foreach(['Entry Level', 'Junior', 'Mid-Level', 'Senior / Lead', 'Manager', 'Head of Department', 'Director'] as $lvl)
                        <option value="{{ $lvl }}" {{ old('job_level', $job->job_level) == $lvl ? 'selected' : '' }}>{{ $lvl }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1.5">Tipe Kontrak Kerja*</label>
                <select name="employment_type" required class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm text-slate-900 focus:outline-none focus:bg-white focus:border-cyan-600">
                    <option value="FULL_TIME" {{ old('employment_type', $job->employment_type) == 'FULL_TIME' ? 'selected' : '' }}>Penuh Waktu (Full-Time)</option>
                    <option value="CONTRACT" {{ old('employment_type', $job->employment_type) == 'CONTRACT' ? 'selected' : '' }}>Kontrak (PKWT)</option>
                    <option value="PART_TIME" {{ old('employment_type', $job->employment_type) == 'PART_TIME' ? 'selected' : '' }}>Paruh Waktu (Part-Time)</option>
                    <option value="INTERNSHIP" {{ old('employment_type', $job->employment_type) == 'INTERNSHIP' ? 'selected' : '' }}>Magang (Internship)</option>
                </select>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1.5">Model Kerja (Work Model)*</label>
                <select name="work_model" required class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm text-slate-900 focus:outline-none focus:bg-white focus:border-cyan-600">
                    <option value="ONSITE" {{ old('work_model', $job->work_model) == 'ONSITE' ? 'selected' : '' }}>Onsite (Kantor)</option>
                    <option value="HYBRID" {{ old('work_model', $job->work_model) == 'HYBRID' ? 'selected' : '' }}>Hybrid (Kantor & Rumah)</option>
                    <option value="REMOTE" {{ old('work_model', $job->work_model) == 'REMOTE' ? 'selected' : '' }}>Remote (WFH Penuh)</option>
                </select>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1.5">Lokasi Penempatan*</label>
                <input type="text" name="location" value="{{ old('location', $job->location) }}" required class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm text-slate-900 focus:outline-none focus:bg-white focus:border-cyan-600" />
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1.5">Batas Akhir Pendaftaran (Deadline)</label>
                <input type="date" name="deadline" value="{{ old('deadline', $job->deadline?->format('Y-m-d')) }}" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm text-slate-900 focus:outline-none focus:bg-white focus:border-cyan-600" />
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1.5">Gaji Minimum (Rp / Bulan)</label>
                <input type="number" name="salary_min" value="{{ old('salary_min', $job->salary_min) }}" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm text-slate-900 placeholder-slate-400 focus:outline-none focus:bg-white focus:border-cyan-600" />
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1.5">Gaji Maksimum (Rp / Bulan)</label>
                <input type="number" name="salary_max" value="{{ old('salary_max', $job->salary_max) }}" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm text-slate-900 placeholder-slate-400 focus:outline-none focus:bg-white focus:border-cyan-600" />
            </div>

            <div class="sm:col-span-2">
                <label class="inline-flex items-center space-x-2 cursor-pointer">
                    <input type="checkbox" name="hide_salary" value="1" {{ old('hide_salary', $job->hide_salary) ? 'checked' : '' }} class="w-4 h-4 rounded text-cyan-600 focus:ring-cyan-500" />
                    <span class="text-xs text-slate-600">Sembunyikan nominal gaji dari publik (tampilkan sebagai "Gaji Kompetitif / Dirahasiakan")</span>
                </label>
            </div>
        </div>

        <div class="space-y-4 pt-4 border-t border-slate-100">
            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1.5">Deskripsi Peran & Tanggung Jawab*</label>
                <textarea name="description" rows="5" required class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm text-slate-900 focus:outline-none focus:bg-white focus:border-cyan-600">{{ old('description', $job->description) }}</textarea>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1.5">Kualifikasi & Persyaratan (Requirements)</label>
                <textarea name="requirements" rows="5" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm text-slate-900 focus:outline-none focus:bg-white focus:border-cyan-600">{{ old('requirements', $job->requirements) }}</textarea>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1.5">Fasilitas & Benefit (Benefits)</label>
                <textarea name="benefits" rows="4" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm text-slate-900 focus:outline-none focus:bg-white focus:border-cyan-600">{{ old('benefits', $job->benefits) }}</textarea>
            </div>
        </div>

        <div class="pt-4 border-t border-slate-100 flex items-center justify-between">
            <div class="flex items-center space-x-3">
                <label class="text-xs font-bold text-slate-700">Status Lowongan:</label>
                <select name="status" class="px-3 py-1.5 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-900">
                    <option value="PUBLISHED" {{ old('status', $job->status) == 'PUBLISHED' ? 'selected' : '' }}>PUBLISHED (Aktif)</option>
                    <option value="DRAFT" {{ old('status', $job->status) == 'DRAFT' ? 'selected' : '' }}>DRAFT (Konsep)</option>
                    <option value="CLOSED" {{ old('status', $job->status) == 'CLOSED' ? 'selected' : '' }}>CLOSED (Ditutup)</option>
                </select>
            </div>

            <button type="submit" class="px-6 py-2.5 rounded-xl text-xs font-bold text-white bg-gradient-to-r from-cyan-600 to-indigo-600 hover:from-cyan-500 hover:to-indigo-500 shadow-md shadow-cyan-600/20 transition-all">
                Simpan Perubahan Lowongan →
            </button>
        </div>
    </form>
</div>
@endsection
