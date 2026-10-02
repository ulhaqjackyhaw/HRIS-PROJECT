@extends('layouts.app')

@section('header', 'Kelola Jabatan & Formasi Posisi')

@section('actions')
    <a href="{{ route('positions.create') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold rounded-xl shadow-sm transition-all">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
        </svg>
        <span>Tambah Jabatan</span>
    </a>
@endsection

@section('content')
<div class="space-y-6">

    <!-- Filters & Search Bar -->
    <div class="bg-white p-4 rounded-2xl border border-slate-200/80 shadow-xs flex flex-col sm:flex-row gap-4 justify-between items-center">
        <form method="GET" action="{{ route('positions.index') }}" class="w-full flex-1 flex flex-col sm:flex-row flex-wrap gap-3">
            <div class="relative flex-1 min-w-[200px]">
                <input type="text" 
                       name="search" 
                       value="{{ request('search') }}" 
                       placeholder="Cari kode, nama jabatan, atau fungsi..." 
                       class="w-full pl-10 pr-4 py-2 text-sm bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:outline-none focus:ring-2 focus:ring-indigo-500">
                <svg class="w-4 h-4 text-slate-400 absolute left-3.5 top-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                </svg>
            </div>

            <!-- Departemen Filter -->
            <select name="department_id" onchange="this.form.submit()" class="py-2 px-3 text-sm bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:outline-none focus:ring-2 focus:ring-indigo-500">
                <option value="">Semua Departemen</option>
                @foreach ($departments as $dept)
                    <option value="{{ $dept->id }}" {{ request('department_id') == $dept->id ? 'selected' : '' }}>
                        {{ $dept->name }}
                    </option>
                @endforeach
            </select>

            <!-- Level Filter -->
            <select name="level" onchange="this.form.submit()" class="py-2 px-3 text-sm bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:outline-none focus:ring-2 focus:ring-indigo-500">
                <option value="">Semua Level</option>
                <option value="Staff" {{ request('level') === 'Staff' ? 'selected' : '' }}>Staff</option>
                <option value="Senior Staff" {{ request('level') === 'Senior Staff' ? 'selected' : '' }}>Senior Staff</option>
                <option value="Supervisor" {{ request('level') === 'Supervisor' ? 'selected' : '' }}>Supervisor</option>
                <option value="Assistant Manager" {{ request('level') === 'Assistant Manager' ? 'selected' : '' }}>Assistant Manager</option>
                <option value="Manager" {{ request('level') === 'Manager' ? 'selected' : '' }}>Manager</option>
                <option value="Director" {{ request('level') === 'Director' ? 'selected' : '' }}>Director</option>
            </select>

            <button type="submit" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 font-medium text-sm rounded-xl transition-colors">
                Filter
            </button>
            @if(request()->hasAny(['search', 'department_id', 'level']))
                <a href="{{ route('positions.index') }}" class="px-3 py-2 text-slate-400 hover:text-slate-600 text-sm flex items-center">
                    Reset
                </a>
            @endif
        </form>
    </div>

    <!-- Table Card -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-slate-600">
                <thead class="bg-slate-50 text-xs font-semibold text-slate-500 uppercase tracking-wider border-b border-slate-100">
                    <tr>
                        <th class="py-3.5 px-6">Kode</th>
                        <th class="py-3.5 px-6">Nama Jabatan</th>
                        <th class="py-3.5 px-6">Departemen</th>
                        <th class="py-3.5 px-6">Level & Jalur</th>
                        <th class="py-3.5 px-6">Fungsi Pekerjaan</th>
                        <th class="py-3.5 px-6 text-center">Karyawan</th>
                        <th class="py-3.5 px-6">Status</th>
                        <th class="py-3.5 px-6 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($positions as $position)
                        <tr class="hover:bg-slate-50/60 transition-colors">
                            <td class="py-4 px-6 font-mono font-medium text-slate-800">
                                {{ $position->code }}
                            </td>
                            <td class="py-4 px-6 font-semibold text-slate-900">
                                {{ $position->title }}
                            </td>
                            <td class="py-4 px-6 text-slate-700">
                                {{ $position->department?->name ?? '—' }}
                            </td>
                            <td class="py-4 px-6">
                                <div class="font-medium text-slate-800">{{ $position->level ?? 'Staff' }}</div>
                                <div class="text-xs text-indigo-600 font-medium">{{ $position->career_path ?? 'Umum' }}</div>
                            </td>
                            <td class="py-4 px-6 text-slate-600">
                                {{ $position->job_function ?? '—' }}
                            </td>
                            <td class="py-4 px-6 text-center font-semibold text-indigo-600">
                                {{ $position->employees_count }}
                            </td>
                            <td class="py-4 px-6">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $position->is_active ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-rose-50 text-rose-700 border border-rose-200' }}">
                                    {{ $position->is_active ? 'Aktif' : 'Nonaktif' }}
                                </span>
                            </td>
                            <td class="py-4 px-6 text-right space-x-2">
                                <a href="{{ route('positions.edit', $position) }}" class="inline-flex px-3 py-1.5 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-semibold rounded-lg transition-colors">
                                    Edit
                                </a>
                                <form action="{{ route('positions.destroy', $position) }}" method="POST" class="inline-block" onsubmit="return confirm('Apakah Anda yakin ingin menghapus formasi jabatan ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="inline-flex px-3 py-1.5 bg-rose-50 hover:bg-rose-100 text-rose-600 text-xs font-semibold rounded-lg transition-colors">
                                        Hapus
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="py-12 text-center text-slate-400">
                                Belum ada data formasi jabatan yang terdaftar.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($positions->hasPages())
            <div class="p-4 border-t border-slate-100">
                {{ $positions->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
