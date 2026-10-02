@extends('layouts.app')

@section('header', 'Daftar Induk Karyawan')

@section('actions')
    <a href="{{ route('employees.create') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold rounded-xl shadow-sm transition-all">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
        </svg>
        <span>Tambah Karyawan</span>
    </a>
@endsection

@section('content')
<div class="space-y-6">

    <!-- Filters & Search Bar -->
    <div class="bg-white p-4 rounded-2xl border border-slate-200/80 shadow-xs">
        <form method="GET" action="{{ route('employees.index') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3">
            <!-- Search Text -->
            <div class="relative lg:col-span-2">
                <input type="text" 
                       name="search" 
                       value="{{ request('search') }}" 
                       placeholder="Cari nama, NIK, No. KTP, atau email..." 
                       class="w-full pl-10 pr-4 py-2 text-sm bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:outline-none focus:ring-2 focus:ring-indigo-500">
                <svg class="w-4 h-4 text-slate-400 absolute left-3.5 top-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                </svg>
            </div>

            <!-- Departemen -->
            <div>
                <select name="department_id" onchange="this.form.submit()" class="w-full py-2 px-3 text-sm bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    <option value="">Semua Departemen</option>
                    @foreach ($departments as $dept)
                        <option value="{{ $dept->id }}" {{ request('department_id') == $dept->id ? 'selected' : '' }}>
                            {{ $dept->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Status PKWT/PKWTT -->
            <div>
                <select name="employment_status" onchange="this.form.submit()" class="w-full py-2 px-3 text-sm bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    <option value="">Semua Status Ikatan</option>
                    <option value="PKWT" {{ request('employment_status') === 'PKWT' ? 'selected' : '' }}>PKWT (Kontrak)</option>
                    <option value="PKWTT" {{ request('employment_status') === 'PKWTT' ? 'selected' : '' }}>PKWTT (Tetap)</option>
                    <option value="MAGANG" {{ request('employment_status') === 'MAGANG' ? 'selected' : '' }}>Magang / Intern</option>
                </select>
            </div>

            <!-- Life-Cycle Stage -->
            <div>
                <select name="lifecycle_stage" onchange="this.form.submit()" class="w-full py-2 px-3 text-sm bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    <option value="">Semua Fase Siklus</option>
                    <option value="ONBOARDING" {{ request('lifecycle_stage') === 'ONBOARDING' ? 'selected' : '' }}>1. Onboarding</option>
                    <option value="ACTIVE" {{ request('lifecycle_stage') === 'ACTIVE' ? 'selected' : '' }}>2. Active Working</option>
                    <option value="SUSPENDED" {{ request('lifecycle_stage') === 'SUSPENDED' ? 'selected' : '' }}>3. Suspended</option>
                    <option value="OFFBOARDING" {{ request('lifecycle_stage') === 'OFFBOARDING' ? 'selected' : '' }}>4. Offboarding</option>
                    <option value="TERMINATED" {{ request('lifecycle_stage') === 'TERMINATED' ? 'selected' : '' }}>5. Terminated / Alumni</option>
                </select>
            </div>

            <!-- Action Buttons -->
            <div class="flex items-center gap-2">
                <button type="submit" class="flex-1 py-2 px-4 bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold text-sm rounded-xl transition-colors">
                    Filter
                </button>
                @if(request()->hasAny(['search', 'department_id', 'employment_status', 'lifecycle_stage', 'position_id', 'is_active']))
                    <a href="{{ route('employees.index') }}" class="py-2 px-3 text-slate-400 hover:text-slate-600 text-sm">
                        Reset
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Table Card -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-slate-600">
                <thead class="bg-slate-50 text-xs font-semibold text-slate-500 uppercase tracking-wider border-b border-slate-100">
                    <tr>
                        <th class="py-3.5 px-6">Identitas Karyawan</th>
                        <th class="py-3.5 px-6">Departemen & Jabatan</th>
                        <th class="py-3.5 px-6">Status & Siklus Hidup</th>
                        <th class="py-3.5 px-6">TMT & Masa Kerja</th>
                        <th class="py-3.5 px-6 text-center">Usia</th>
                        <th class="py-3.5 px-6">Status</th>
                        <th class="py-3.5 px-6 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($employees as $employee)
                        <tr class="hover:bg-slate-50/60 transition-colors">
                            <td class="py-4 px-6">
                                <div class="flex items-center space-x-3">
                                    <div class="w-10 h-10 rounded-xl bg-gradient-to-tr from-slate-800 to-slate-700 text-white font-bold flex items-center justify-center text-sm shadow-xs shrink-0">
                                        {{ strtoupper(substr($employee->full_name, 0, 2)) }}
                                    </div>
                                    <div>
                                        <a href="{{ route('employees.show', $employee) }}" class="font-bold text-slate-900 hover:text-indigo-600 hover:underline">
                                            {{ $employee->full_name }}
                                        </a>
                                        <div class="flex items-center gap-2 text-xs text-slate-400 font-mono mt-0.5">
                                            <span>NIK: {{ $employee->nik }}</span>
                                            <span>&bull;</span>
                                            <span class="font-sans">{{ $employee->email }}</span>
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td class="py-4 px-6">
                                <div class="font-medium text-slate-800">{{ $employee->department?->name ?? '—' }}</div>
                                <div class="text-xs text-indigo-600 font-medium">{{ $employee->position?->title ?? '—' }}</div>
                            </td>
                            <td class="py-4 px-6">
                                <div class="flex flex-col gap-1 items-start">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-semibold {{ $employee->employment_status === 'PKWTT' ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-blue-50 text-blue-700 border border-blue-200' }}">
                                        {{ $employee->employment_status }}
                                    </span>
                                    @php
                                        $stage = $employee->lifecycle_stage ?? 'ACTIVE';
                                        $stageBadge = match($stage) {
                                            'ONBOARDING' => 'bg-amber-50 text-amber-700 border-amber-200',
                                            'ACTIVE' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                                            'SUSPENDED' => 'bg-rose-50 text-rose-700 border-rose-200',
                                            'OFFBOARDING' => 'bg-purple-50 text-purple-700 border-purple-200',
                                            'TERMINATED' => 'bg-slate-100 text-slate-600 border-slate-200',
                                            default => 'bg-slate-100 text-slate-600 border-slate-200'
                                        };
                                    @endphp
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-extrabold border {{ $stageBadge }}">
                                        {{ $stage }}
                                    </span>
                                </div>
                            </td>
                            <td class="py-4 px-6">
                                <div class="text-slate-800 font-medium">{{ $employee->join_date ? $employee->join_date->translatedFormat('d M Y') : '-' }}</div>
                                <div class="text-xs text-slate-500 font-medium">{{ $employee->tenure ?? '-' }}</div>
                            </td>
                            <td class="py-4 px-6 text-center font-bold text-slate-800">
                                {{ $employee->age ? $employee->age . ' th' : '-' }}
                            </td>
                            <td class="py-4 px-6">
                                <span class="inline-flex items-center px-2 py-0.5 rounded-md text-xs font-medium {{ $employee->is_active ? 'bg-emerald-100 text-emerald-800' : 'bg-rose-100 text-rose-800' }}">
                                    {{ $employee->is_active ? 'Aktif' : 'Nonaktif' }}
                                </span>
                            </td>
                            <td class="py-4 px-6 text-right space-x-2">
                                <a href="{{ route('employees.show', $employee) }}" class="inline-flex px-3 py-1.5 bg-indigo-50 hover:bg-indigo-100 text-indigo-700 text-xs font-semibold rounded-lg transition-colors">
                                    Profil
                                </a>
                                <a href="{{ route('employees.edit', $employee) }}" class="inline-flex px-3 py-1.5 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-semibold rounded-lg transition-colors">
                                    Edit
                                </a>
                                <form action="{{ route('employees.destroy', $employee) }}" method="POST" class="inline-block" onsubmit="return confirm('Apakah Anda yakin ingin menonaktifkan karyawan ini?')">
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
                            <td colspan="7" class="py-12 text-center text-slate-400">
                                Belum ada data karyawan yang sesuai dengan kriteria pencarian.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($employees->hasPages())
            <div class="p-4 border-t border-slate-100">
                {{ $employees->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
