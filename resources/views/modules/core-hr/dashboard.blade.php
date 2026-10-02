@extends('layouts.app')

@section('header', 'Dashboard Eksekutif HRIS')

@section('actions')
    <a href="{{ route('employees.create') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold rounded-xl shadow-sm transition-all focus:outline-none focus:ring-2 focus:ring-indigo-500">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
        </svg>
        <span>Tambah Karyawan</span>
    </a>
@endsection

@section('content')
<div class="space-y-8">

    <!-- KPI Metric Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
        <!-- Total Karyawan -->
        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs flex items-center justify-between">
            <div>
                <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Total Karyawan</p>
                <h3 class="text-2xl font-extrabold text-slate-800 mt-1">{{ number_format($totalEmployees) }}</h3>
                <p class="text-xs text-slate-400 mt-1">Terdaftar dalam master data</p>
            </div>
            <div class="w-12 h-12 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                </svg>
            </div>
        </div>

        <!-- Karyawan Aktif -->
        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs flex items-center justify-between">
            <div>
                <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Karyawan Aktif</p>
                <h3 class="text-2xl font-extrabold text-emerald-600 mt-1">{{ number_format($activeEmployees) }}</h3>
                <p class="text-xs text-slate-400 mt-1">Status aktif bekerja</p>
            </div>
            <div class="w-12 h-12 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            </div>
        </div>

        <!-- Total Departemen -->
        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs flex items-center justify-between">
            <div>
                <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Departemen</p>
                <h3 class="text-2xl font-extrabold text-slate-800 mt-1">{{ number_format($totalDepartments) }}</h3>
                <p class="text-xs text-slate-400 mt-1">Unit kerja organisasi</p>
            </div>
            <div class="w-12 h-12 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                </svg>
            </div>
        </div>

        <!-- Total Jabatan -->
        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs flex items-center justify-between">
            <div>
                <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Jabatan / Posisi</p>
                <h3 class="text-2xl font-extrabold text-slate-800 mt-1">{{ number_format($totalPositions) }}</h3>
                <p class="text-xs text-slate-400 mt-1">Formasi pekerjaan</p>
            </div>
            <div class="w-12 h-12 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                </svg>
            </div>
        </div>
    </div>

    <!-- Employee Life-Cycle State Machine Funnel -->
    <div class="bg-white p-6 rounded-2xl border border-slate-200/80 shadow-xs space-y-4">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
            <div>
                <h3 class="font-bold text-slate-900 text-base flex items-center gap-2">
                    <span class="w-2.5 h-2.5 rounded-full bg-indigo-600"></span>
                    Employee Life-Cycle Tracker
                </h3>
                <p class="text-xs text-slate-500">Pemantauan siklus kepegawaian & alur state machine sistem (Attract &rarr; Onboarding &rarr; Development &rarr; Offboarding &rarr; Alumni)</p>
            </div>
            <a href="{{ route('employees.index') }}" class="text-xs font-semibold text-indigo-600 hover:text-indigo-800 hover:underline">
                Lihat Direktori Karyawan &rarr;
            </a>
        </div>

        <div class="grid grid-cols-2 sm:grid-cols-5 gap-3 pt-2">
            <!-- 1. Onboarding -->
            <a href="{{ route('employees.index', ['stage' => 'ONBOARDING']) }}" class="p-3.5 rounded-xl border border-amber-200/80 bg-amber-50/50 hover:bg-amber-100/50 transition-colors group">
                <div class="flex items-center justify-between">
                    <span class="text-[10px] font-bold uppercase tracking-wider text-amber-700">1. Onboarding</span>
                    <span class="w-2 h-2 rounded-full bg-amber-400"></span>
                </div>
                <div class="text-xl font-extrabold text-amber-900 mt-2">{{ $stageCounts['ONBOARDING'] }}</div>
                <p class="text-[11px] text-amber-700/80 mt-0.5">Kelengkapan Berkas / TMT</p>
            </a>

            <!-- 2. Active -->
            <a href="{{ route('employees.index', ['stage' => 'ACTIVE']) }}" class="p-3.5 rounded-xl border border-emerald-200/80 bg-emerald-50/50 hover:bg-emerald-100/50 transition-colors group">
                <div class="flex items-center justify-between">
                    <span class="text-[10px] font-bold uppercase tracking-wider text-emerald-700">2. Active</span>
                    <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                </div>
                <div class="text-xl font-extrabold text-emerald-900 mt-2">{{ $stageCounts['ACTIVE'] }}</div>
                <p class="text-[11px] text-emerald-700/80 mt-0.5">Operasional & Payroll Aktif</p>
            </a>

            <!-- 3. Suspended -->
            <a href="{{ route('employees.index', ['stage' => 'SUSPENDED']) }}" class="p-3.5 rounded-xl border border-rose-200/80 bg-rose-50/50 hover:bg-rose-100/50 transition-colors group">
                <div class="flex items-center justify-between">
                    <span class="text-[10px] font-bold uppercase tracking-wider text-rose-700">3. Suspended</span>
                    <span class="w-2 h-2 rounded-full bg-rose-400"></span>
                </div>
                <div class="text-xl font-extrabold text-rose-900 mt-2">{{ $stageCounts['SUSPENDED'] }}</div>
                <p class="text-[11px] text-rose-700/80 mt-0.5">Skorsing / Unpaid Leave</p>
            </a>

            <!-- 4. Offboarding -->
            <a href="{{ route('employees.index', ['stage' => 'OFFBOARDING']) }}" class="p-3.5 rounded-xl border border-purple-200/80 bg-purple-50/50 hover:bg-purple-100/50 transition-colors group">
                <div class="flex items-center justify-between">
                    <span class="text-[10px] font-bold uppercase tracking-wider text-purple-700">4. Offboarding</span>
                    <span class="w-2 h-2 rounded-full bg-purple-400"></span>
                </div>
                <div class="text-xl font-extrabold text-purple-900 mt-2">{{ $stageCounts['OFFBOARDING'] }}</div>
                <p class="text-[11px] text-purple-700/80 mt-0.5">Clearance & Handover</p>
            </a>

            <!-- 5. Terminated -->
            <a href="{{ route('employees.index', ['stage' => 'TERMINATED']) }}" class="p-3.5 rounded-xl border border-slate-200/80 bg-slate-50/50 hover:bg-slate-100/50 transition-colors group">
                <div class="flex items-center justify-between">
                    <span class="text-[10px] font-bold uppercase tracking-wider text-slate-700">5. Terminated</span>
                    <span class="w-2 h-2 rounded-full bg-slate-400"></span>
                </div>
                <div class="text-xl font-extrabold text-slate-900 mt-2">{{ $stageCounts['TERMINATED'] }}</div>
                <p class="text-[11px] text-slate-500 mt-0.5">Alumni / Paklaring</p>
            </a>
        </div>
    </div>

    <!-- Alert Kontrak Jatuh Tempo (30 Hari ke Depan) -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
        <div class="p-5 border-b border-slate-100 flex items-center justify-between bg-amber-50/40">
            <div class="flex items-center space-x-3">
                <span class="w-3 h-3 rounded-full bg-amber-500 animate-ping"></span>
                <h3 class="font-bold text-slate-900 text-base">Peringatan: Kontrak PKWT Berakhir Dalam 30 Hari</h3>
            </div>
            <span class="px-2.5 py-1 text-xs font-semibold bg-amber-100 text-amber-800 rounded-full">
                {{ $expiringContracts->count() }} Perlu Ditinjau
            </span>
        </div>

        @if ($expiringContracts->isEmpty())
            <div class="p-8 text-center text-slate-500 text-sm">
                <svg class="w-12 h-12 text-slate-300 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                Tidak ada kontrak PKWT yang akan berakhir dalam 30 hari ke depan.
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm text-slate-600">
                    <thead class="bg-slate-50 text-xs font-semibold text-slate-500 uppercase tracking-wider border-b border-slate-100">
                        <tr>
                            <th class="py-3 px-5">Karyawan</th>
                            <th class="py-3 px-5">Unit & Posisi</th>
                            <th class="py-3 px-5">No. Kontrak</th>
                            <th class="py-3 px-5">Tgl Berakhir</th>
                            <th class="py-3 px-5">Sisa Hari</th>
                            <th class="py-3 px-5 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach ($expiringContracts as $contract)
                            @php
                                $daysLeft = Carbon\Carbon::today()->diffInDays($contract->end_date, false);
                            @endphp
                            <tr class="hover:bg-slate-50/60 transition-colors">
                                <td class="py-3.5 px-5">
                                    <div class="font-semibold text-slate-900">{{ $contract->employee?->full_name ?? '-' }}</div>
                                    <div class="text-xs text-slate-400 font-mono">{{ $contract->employee?->nik ?? '-' }}</div>
                                </td>
                                <td class="py-3.5 px-5">
                                    <div class="text-slate-800">{{ $contract->employee?->department?->name ?? '-' }}</div>
                                    <div class="text-xs text-slate-400">{{ $contract->employee?->position?->title ?? '-' }}</div>
                                </td>
                                <td class="py-3.5 px-5 font-mono text-xs text-slate-600">
                                    {{ $contract->contract_number ?? '-' }}
                                </td>
                                <td class="py-3.5 px-5 font-medium text-amber-700">
                                    {{ $contract->end_date ? $contract->end_date->translatedFormat('d M Y') : '-' }}
                                </td>
                                <td class="py-3.5 px-5">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold bg-rose-50 text-rose-700 border border-rose-200">
                                        {{ $daysLeft }} hari lagi
                                    </span>
                                </td>
                                <td class="py-3.5 px-5 text-right">
                                    @if ($contract->employee)
                                        <a href="{{ route('employees.show', $contract->employee) }}" class="text-xs font-semibold text-indigo-600 hover:text-indigo-800 hover:underline">
                                            Perbarui Kontrak &rarr;
                                        </a>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>

    <!-- 2 Column Section: Recent Employees & Department Breakdown -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Recent Employees (2 cols) -->
        <div class="lg:col-span-2 bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
            <div class="p-5 border-b border-slate-100 flex items-center justify-between">
                <div>
                    <h3 class="font-bold text-slate-900 text-base">Karyawan Terbaru</h3>
                    <p class="text-xs text-slate-400 mt-0.5">Pegawai yang baru bergabung di perusahaan</p>
                </div>
                <a href="{{ route('employees.index') }}" class="text-xs font-semibold text-indigo-600 hover:text-indigo-800 hover:underline">
                    Lihat Semua &rarr;
                </a>
            </div>

            @if ($recentEmployees->isEmpty())
                <div class="p-8 text-center text-slate-500 text-sm">
                    Belum ada data karyawan. Klik tombol di atas untuk menambahkan karyawan baru.
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm text-slate-600">
                        <thead class="bg-slate-50 text-xs font-semibold text-slate-500 uppercase tracking-wider border-b border-slate-100">
                            <tr>
                                <th class="py-3 px-5">Nama / NIK</th>
                                <th class="py-3 px-5">Departemen</th>
                                <th class="py-3 px-5">Jabatan</th>
                                <th class="py-3 px-5">TMT (Join Date)</th>
                                <th class="py-3 px-5">Tahapan Siklus</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @foreach ($recentEmployees as $emp)
                                <tr class="hover:bg-slate-50/60 transition-colors">
                                    <td class="py-3.5 px-5">
                                        <a href="{{ route('employees.show', $emp) }}" class="font-semibold text-slate-900 hover:text-indigo-600 hover:underline">
                                            {{ $emp->full_name }}
                                        </a>
                                        <div class="text-xs text-slate-400 font-mono">{{ $emp->nik }}</div>
                                    </td>
                                    <td class="py-3.5 px-5 text-slate-800">
                                        {{ $emp->department?->name ?? '-' }}
                                    </td>
                                    <td class="py-3.5 px-5 text-slate-800">
                                        {{ $emp->position?->title ?? '-' }}
                                    </td>
                                    <td class="py-3.5 px-5 text-slate-600">
                                        {{ $emp->join_date ? $emp->join_date->translatedFormat('d M Y') : '-' }}
                                    </td>
                                    <td class="py-3.5 px-5">
                                        @php
                                            $stageBadge = match($emp->lifecycle_stage) {
                                                'ACTIVE' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                                                'ONBOARDING' => 'bg-amber-50 text-amber-700 border-amber-200',
                                                'SUSPENDED' => 'bg-rose-50 text-rose-700 border-rose-200',
                                                'OFFBOARDING' => 'bg-purple-50 text-purple-700 border-purple-200',
                                                'TERMINATED' => 'bg-slate-100 text-slate-700 border-slate-200',
                                                default => 'bg-slate-50 text-slate-600 border-slate-200'
                                            };
                                        @endphp
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold border {{ $stageBadge }}">
                                            {{ $emp->lifecycle_stage ?? 'ONBOARDING' }}
                                        </span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>

        <!-- Department Breakdown (1 col) -->
        <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs p-5 flex flex-col justify-between">
            <div>
                <div class="flex items-center justify-between pb-4 border-b border-slate-100">
                    <div>
                        <h3 class="font-bold text-slate-900 text-base">Sebaran Unit Kerja</h3>
                        <p class="text-xs text-slate-400 mt-0.5">Distribusi personil per departemen</p>
                    </div>
                    <a href="{{ route('departments.index') }}" class="text-xs font-semibold text-indigo-600 hover:text-indigo-800 hover:underline">
                        Detail
                    </a>
                </div>

                <div class="mt-5 space-y-4">
                    @forelse ($departmentsSummary as $dept)
                        <div>
                            <div class="flex items-center justify-between text-xs font-medium mb-1">
                                <span class="text-slate-800 truncate font-semibold">{{ $dept->name }}</span>
                                <span class="text-slate-500 font-mono">{{ $dept->employees_count }} orang</span>
                            </div>
                            @php
                                $percent = $totalEmployees > 0 ? round(($dept->employees_count / $totalEmployees) * 100) : 0;
                            @endphp
                            <div class="w-full bg-slate-100 rounded-full h-2 overflow-hidden">
                                <div class="bg-indigo-600 h-2 rounded-full transition-all duration-500" style="width: {{ $percent }}%"></div>
                            </div>
                        </div>
                    @empty
                        <p class="text-xs text-slate-400 text-center py-4">Belum ada departemen yang dibuat.</p>
                    @endforelse
                </div>
            </div>

            <div class="mt-6 pt-4 border-t border-slate-100">
                <div class="bg-slate-50 rounded-xl p-3 text-xs text-slate-600 flex items-center justify-between">
                    <span>Modul Aktif:</span>
                    <span class="font-semibold text-indigo-600">Core HR & Payroll Ready</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Riwayat Pergerakan Karir Terkini (Mutasi / Promosi / Transisi) -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
        <div class="p-5 border-b border-slate-100 flex items-center justify-between">
            <div>
                <h3 class="font-bold text-slate-900 text-base">Pergerakan Karir & Mutasi Terkini</h3>
                <p class="text-xs text-slate-400 mt-0.5">Log histori mutasi jabatan, promosi, dan perubahan status kepegawaian</p>
            </div>
            <span class="px-2.5 py-1 text-xs font-semibold bg-indigo-50 text-indigo-700 rounded-full">
                Audit Trail SK Kepegawaian
            </span>
        </div>

        @if ($recentMovements->isEmpty())
            <div class="p-8 text-center text-slate-500 text-sm">
                Belum ada catatan mutasi atau promosi tercatat. Transisi karir dapat ditambahkan langsung dari halaman profil karyawan.
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm text-slate-600">
                    <thead class="bg-slate-50 text-xs font-semibold text-slate-500 uppercase tracking-wider border-b border-slate-100">
                        <tr>
                            <th class="py-3 px-5">Tgl SK / Efektif</th>
                            <th class="py-3 px-5">Karyawan</th>
                            <th class="py-3 px-5">Jenis Transisi</th>
                            <th class="py-3 px-5">Unit Asal &rarr; Unit Baru</th>
                            <th class="py-3 px-5">Jabatan Asal &rarr; Jabatan Baru</th>
                            <th class="py-3 px-5">No. Referensi SK</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach ($recentMovements as $move)
                            @php
                                $typeBadge = match($move->transition_type) {
                                    'PROMOTION' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                                    'MUTATION' => 'bg-blue-50 text-blue-700 border-blue-200',
                                    'DEMOTION' => 'bg-rose-50 text-rose-700 border-rose-200',
                                    'JOIN' => 'bg-indigo-50 text-indigo-700 border-indigo-200',
                                    'RESIGN', 'TERMINATE' => 'bg-slate-100 text-slate-700 border-slate-200',
                                    default => 'bg-slate-50 text-slate-600 border-slate-200'
                                };
                            @endphp
                            <tr class="hover:bg-slate-50/60 transition-colors">
                                <td class="py-3.5 px-5 font-medium text-slate-700">
                                    {{ $move->effective_date ? $move->effective_date->translatedFormat('d M Y') : '-' }}
                                </td>
                                <td class="py-3.5 px-5">
                                    @if ($move->employee)
                                        <a href="{{ route('employees.show', $move->employee) }}" class="font-semibold text-slate-900 hover:text-indigo-600 hover:underline">
                                            {{ $move->employee->full_name }}
                                        </a>
                                        <div class="text-xs text-slate-400 font-mono">{{ $move->employee->nik }}</div>
                                    @else
                                        -
                                    @endif
                                </td>
                                <td class="py-3.5 px-5">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold border {{ $typeBadge }}">
                                        {{ $move->transition_type }}
                                    </span>
                                </td>
                                <td class="py-3.5 px-5 text-xs text-slate-700">
                                    <span>{{ $move->oldDepartment?->name ?? 'Awal' }}</span>
                                    <span class="text-indigo-500 font-bold mx-1">&rarr;</span>
                                    <span class="font-semibold text-slate-900">{{ $move->newDepartment?->name ?? 'Tetap' }}</span>
                                </td>
                                <td class="py-3.5 px-5 text-xs text-slate-700">
                                    <span>{{ $move->oldPosition?->title ?? 'Awal' }}</span>
                                    <span class="text-indigo-500 font-bold mx-1">&rarr;</span>
                                    <span class="font-semibold text-slate-900">{{ $move->newPosition?->title ?? 'Tetap' }}</span>
                                </td>
                                <td class="py-3.5 px-5 font-mono text-xs text-slate-500">
                                    {{ $move->reference_doc_no ?? '-' }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>

</div>
@endsection
