@extends('layouts.app', ['title' => 'Daftar Lowongan Pekerjaan'])

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-white border border-slate-200/90 p-6 rounded-3xl shadow-xs">
        <div>
            <div class="flex items-center space-x-2 text-xs font-bold text-cyan-700 uppercase tracking-wider mb-1">
                <span>Talent Acquisition</span>
                <span>•</span>
                <span>Job Postings</span>
            </div>
            <h1 class="text-2xl font-extrabold text-slate-900 tracking-tight">Kelola Lowongan Pekerjaan</h1>
            <p class="text-xs sm:text-sm text-slate-500 mt-0.5">
                Daftar posisi lowongan yang dipublikasikan ke portal publik pelamar (/career).
            </p>
        </div>

        <div class="flex items-center space-x-3">
            <a href="{{ route('recruitment.jobs.create') }}" class="px-5 py-2.5 rounded-xl text-xs font-bold text-white bg-gradient-to-r from-cyan-600 to-indigo-600 hover:from-cyan-500 hover:to-indigo-500 shadow-md shadow-cyan-600/20 transition-all flex items-center space-x-1.5">
                <span>+ Terbitkan Lowongan Baru</span>
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs font-semibold shadow-xs">
            ✓ {{ session('success') }}
        </div>
    @endif

    <!-- Filter & Search Bar -->
    <div class="p-4 rounded-2xl bg-white border border-slate-200/90 shadow-xs">
        <form action="{{ route('recruitment.jobs.index') }}" method="GET" class="grid grid-cols-1 sm:grid-cols-4 gap-3">
            <div class="sm:col-span-2">
                <input
                    type="text"
                    name="search"
                    value="{{ request('search') }}"
                    placeholder="Cari judul lowongan atau lokasi..."
                    class="w-full px-4 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-900 placeholder-slate-400 focus:outline-none focus:bg-white focus:border-cyan-600"
                />
            </div>
            <div>
                <select name="status" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-900 focus:outline-none focus:bg-white focus:border-cyan-600">
                    <option value="">-- Semua Status --</option>
                    <option value="PUBLISHED" {{ request('status') === 'PUBLISHED' ? 'selected' : '' }}>PUBLISHED (Aktif)</option>
                    <option value="DRAFT" {{ request('status') === 'DRAFT' ? 'selected' : '' }}>DRAFT (Konsep)</option>
                    <option value="CLOSED" {{ request('status') === 'CLOSED' ? 'selected' : '' }}>CLOSED (Ditutup)</option>
                </select>
            </div>
            <div class="flex items-center space-x-2">
                <select name="department_id" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-900 focus:outline-none focus:bg-white focus:border-cyan-600">
                    <option value="">-- Departemen --</option>
                    @foreach($departments as $dept)
                        <option value="{{ $dept->id }}" {{ request('department_id') == $dept->id ? 'selected' : '' }}>{{ $dept->name }}</option>
                    @endforeach
                </select>
                <button type="submit" class="px-4 py-2 bg-slate-800 hover:bg-slate-700 text-xs font-bold text-white rounded-xl transition-colors shadow-xs">
                    Filter
                </button>
            </div>
        </form>
    </div>

    <!-- Jobs Table -->
    <div class="p-6 rounded-3xl bg-white border border-slate-200/90 shadow-xs overflow-hidden">
        @if($jobs->isEmpty())
            <div class="py-12 text-center text-xs text-slate-500">
                Tidak ada lowongan pekerjaan yang cocok dengan filter yang dipilih.
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="border-b border-slate-200 text-[11px] font-bold text-slate-500 uppercase tracking-wider">
                            <th class="pb-3">Posisi & Departemen</th>
                            <th class="pb-3">Model & Tipe</th>
                            <th class="pb-3">Status</th>
                            <th class="pb-3">Pelamar</th>
                            <th class="pb-3">Dilihat</th>
                            <th class="pb-3">Tenggat</th>
                            <th class="pb-3 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-xs">
                        @foreach($jobs as $job)
                            <tr class="hover:bg-slate-50 transition-colors">
                                <td class="py-4 pr-4">
                                    <div class="font-bold text-slate-900 text-sm">{{ $job->title }}</div>
                                    <div class="text-[11px] text-slate-500 mt-0.5">
                                        {{ $job->department->name ?? 'Tanpa Departemen' }} • {{ $job->location }}
                                    </div>
                                </td>
                                <td class="py-4 pr-4">
                                    <span class="inline-block px-2 py-0.5 rounded text-[10px] font-semibold bg-slate-100 text-slate-700 border border-slate-200">
                                        {{ $job->work_model_label }}
                                    </span>
                                    <span class="block text-[11px] text-slate-500 mt-1">
                                        {{ $job->employment_type_label }} • {{ $job->job_level }}
                                    </span>
                                </td>
                                <td class="py-4 pr-4">
                                    <span class="inline-block px-2.5 py-1 rounded-lg text-[10px] font-bold {{ $job->status === 'PUBLISHED' ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : ($job->status === 'CLOSED' ? 'bg-rose-50 text-rose-700 border border-rose-200' : 'bg-slate-100 text-slate-600 border border-slate-200') }}">
                                        {{ $job->status }}
                                    </span>
                                </td>
                                <td class="py-4 pr-4">
                                    <a href="{{ route('recruitment.applications.index', ['job_id' => $job->id]) }}" class="font-bold text-cyan-700 hover:underline">
                                        {{ $job->applications_count }} Pelamar &rarr;
                                    </a>
                                </td>
                                <td class="py-4 pr-4 text-slate-500">
                                    {{ $job->views_count }} views
                                </td>
                                <td class="py-4 pr-4 text-slate-500 text-[11px]">
                                    {{ $job->deadline?->format('d M Y') ?? 'Tanpa batas' }}
                                </td>
                                <td class="py-4 text-right">
                                    <div class="inline-flex items-center space-x-2">
                                        <!-- Quick Toggle Publish/Close -->
                                        <form action="{{ route('recruitment.jobs.toggle-status', $job->id) }}" method="POST">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="p-1.5 rounded-lg text-[11px] font-semibold text-slate-600 hover:bg-slate-100 hover:text-slate-900" title="{{ $job->status === 'PUBLISHED' ? 'Tutup Lowongan' : 'Publikasikan' }}">
                                                {{ $job->status === 'PUBLISHED' ? '🔒 Tutup' : '🚀 Buka' }}
                                            </button>
                                        </form>

                                        <a href="{{ route('career.jobs.show', $job->slug) }}" target="_blank" class="p-1.5 rounded-lg text-[11px] font-semibold text-indigo-600 hover:bg-indigo-50" title="Buka di Candidate Portal">
                                            ↗ Portal
                                        </a>

                                        <a href="{{ route('recruitment.jobs.edit', $job->id) }}" class="p-1.5 rounded-lg text-[11px] font-semibold text-cyan-700 hover:bg-cyan-50">
                                            ✏ Edit
                                        </a>

                                        <form action="{{ route('recruitment.jobs.destroy', $job->id) }}" method="POST" onsubmit="return confirm('Hapus lowongan ini? Seluruh data pelamar tetap tersimpan.');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="p-1.5 rounded-lg text-[11px] font-semibold text-rose-600 hover:bg-rose-50">
                                                ✕ Hapus
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="mt-6 pt-4 border-t border-slate-100">
                {{ $jobs->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
