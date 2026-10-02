@extends('layouts.app')

@section('header', 'Kelola Departemen & Unit Kerja')

@section('actions')
    <a href="{{ route('departments.create') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold rounded-xl shadow-sm transition-all">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
        </svg>
        <span>Tambah Departemen</span>
    </a>
@endsection

@section('content')
<div class="space-y-6">

    <!-- Filters & Search Bar -->
    <div class="bg-white p-4 rounded-2xl border border-slate-200/80 shadow-xs flex flex-col sm:flex-row gap-4 justify-between items-center">
        <form method="GET" action="{{ route('departments.index') }}" class="w-full sm:w-auto flex-1 flex flex-col sm:flex-row gap-3">
            <div class="relative flex-1 max-w-md">
                <input type="text" 
                       name="search" 
                       value="{{ request('search') }}" 
                       placeholder="Cari kode atau nama departemen..." 
                       class="w-full pl-10 pr-4 py-2 text-sm bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                <svg class="w-4 h-4 text-slate-400 absolute left-3.5 top-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                </svg>
            </div>

            <select name="status" onchange="this.form.submit()" class="py-2 px-3 text-sm bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:outline-none focus:ring-2 focus:ring-indigo-500">
                <option value="">Semua Status</option>
                <option value="1" {{ request('status') === '1' ? 'selected' : '' }}>Aktif</option>
                <option value="0" {{ request('status') === '0' ? 'selected' : '' }}>Nonaktif</option>
            </select>

            <button type="submit" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 font-medium text-sm rounded-xl transition-colors">
                Filter
            </button>
            @if(request()->hasAny(['search', 'status']))
                <a href="{{ route('departments.index') }}" class="px-3 py-2 text-slate-400 hover:text-slate-600 text-sm flex items-center">
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
                        <th class="py-3.5 px-6">Nama Departemen</th>
                        <th class="py-3.5 px-6">Unit Induk</th>
                        <th class="py-3.5 px-6 text-center">Jumlah Jabatan</th>
                        <th class="py-3.5 px-6 text-center">Karyawan</th>
                        <th class="py-3.5 px-6">Status</th>
                        <th class="py-3.5 px-6 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($departments as $dept)
                        <tr class="hover:bg-slate-50/60 transition-colors">
                            <td class="py-4 px-6 font-mono font-medium text-slate-800">
                                {{ $dept->code }}
                            </td>
                            <td class="py-4 px-6 font-semibold text-slate-900">
                                {{ $dept->name }}
                                @if($dept->description)
                                    <p class="text-xs text-slate-400 font-normal mt-0.5 truncate max-w-xs">{{ $dept->description }}</p>
                                @endif
                            </td>
                            <td class="py-4 px-6 text-slate-600">
                                {{ $dept->parent?->name ?? '— (Unit Utama)' }}
                            </td>
                            <td class="py-4 px-6 text-center font-semibold text-slate-700">
                                {{ $dept->positions_count }}
                            </td>
                            <td class="py-4 px-6 text-center font-semibold text-indigo-600">
                                {{ $dept->employees_count }}
                            </td>
                            <td class="py-4 px-6">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $dept->is_active ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-rose-50 text-rose-700 border border-rose-200' }}">
                                    {{ $dept->is_active ? 'Aktif' : 'Nonaktif' }}
                                </span>
                            </td>
                            <td class="py-4 px-6 text-right space-x-2">
                                <a href="{{ route('departments.edit', $dept) }}" class="inline-flex px-3 py-1.5 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-semibold rounded-lg transition-colors">
                                    Edit
                                </a>
                                <form action="{{ route('departments.destroy', $dept) }}" method="POST" class="inline-block" onsubmit="return confirm('Apakah Anda yakin ingin menghapus departemen ini?')">
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
                                Belum ada data departemen yang sesuai dengan pencarian.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($departments->hasPages())
            <div class="p-4 border-t border-slate-100">
                {{ $departments->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
