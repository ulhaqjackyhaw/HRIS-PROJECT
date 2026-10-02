@extends('layouts.app')

@section('header', 'Profil Karyawan: ' . $employee->full_name)

@section('actions')
    <a href="{{ route('employees.index') }}" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 text-sm font-semibold rounded-xl transition-colors">
        Daftar Karyawan
    </a>
    <a href="{{ route('employees.edit', $employee) }}" class="inline-flex items-center gap-2 px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold rounded-xl shadow-sm transition-all">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
        </svg>
        <span>Edit Profil</span>
    </a>
@endsection

@section('content')
<div class="space-y-6">

    <!-- Profile Header Card -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
        <div class="h-28 bg-gradient-to-r from-slate-900 via-indigo-950 to-slate-900 px-6 sm:px-8 flex items-end">
        </div>
        <div class="px-6 sm:px-8 pb-6 pt-0 relative flex flex-col sm:flex-row sm:items-end sm:justify-between gap-4 -mt-12">
            <div class="flex flex-col sm:flex-row sm:items-end gap-4">
                <div class="w-24 h-24 rounded-2xl bg-gradient-to-tr from-indigo-600 to-violet-600 text-white font-extrabold text-2xl flex items-center justify-center border-4 border-white shadow-md shrink-0">
                    {{ strtoupper(substr($employee->full_name, 0, 2)) }}
                </div>
                <div class="space-y-1">
                    <div class="flex flex-wrap items-center gap-2">
                        <h2 class="text-2xl font-extrabold text-slate-900">{{ $employee->full_name }}</h2>
                        <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold {{ $employee->employment_status === 'PKWTT' ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-blue-50 text-blue-700 border border-blue-200' }}">
                            {{ $employee->employment_status }}
                        </span>

                        @php
                            $stage = $employee->lifecycle_stage ?? 'ACTIVE';
                            $stageBadge = match($stage) {
                                'ONBOARDING' => 'bg-amber-50 text-amber-700 border-amber-200',
                                'ACTIVE' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                                'SUSPENDED' => 'bg-rose-50 text-rose-700 border-rose-200',
                                'OFFBOARDING' => 'bg-purple-50 text-purple-700 border-purple-200',
                                'TERMINATED' => 'bg-slate-100 text-slate-700 border-slate-300',
                                default => 'bg-slate-100 text-slate-700 border-slate-200'
                            };
                        @endphp
                        <span class="px-2.5 py-0.5 rounded-full text-xs font-bold border {{ $stageBadge }}">
                            Stage: {{ $stage }}
                        </span>

                        <span class="px-2 py-0.5 rounded-md text-xs font-medium {{ $employee->is_active ? 'bg-emerald-100 text-emerald-800' : 'bg-rose-100 text-rose-800' }}">
                            {{ $employee->is_active ? 'Aktif' : 'Nonaktif' }}
                        </span>
                    </div>
                    <p class="text-sm text-slate-600 font-medium flex flex-wrap items-center gap-2">
                        <span>{{ $employee->position?->title ?? 'Belum ada jabatan' }}</span>
                        <span>&bull;</span>
                        <span class="text-indigo-600 font-semibold">{{ $employee->department?->name ?? 'Belum ada unit' }}</span>
                        <span>&bull;</span>
                        <span class="text-slate-400 font-mono">NIK: {{ $employee->nik }}</span>
                    </p>
                </div>
            </div>

            <div class="flex items-center gap-2 text-xs text-slate-500 font-medium sm:text-right">
                <div>
                    <div>Lokasi Kerja: <strong class="text-slate-800">{{ $employee->work_location ?? '-' }}</strong></div>
                    <div>Atasan: <strong class="text-slate-800">{{ $employee->manager?->full_name ?? 'Direksi / Top' }}</strong></div>
                </div>
            </div>
        </div>

        <!-- Life-Cycle Stage Visual Stepper -->
        <div class="px-6 sm:px-8 py-3.5 bg-slate-900 border-t border-slate-800 text-slate-300">
            <div class="flex items-center justify-between text-xs mb-2">
                <span class="font-bold uppercase tracking-wider text-slate-400 flex items-center gap-1.5">
                    <span class="w-2 h-2 rounded-full {{ $stage === 'ACTIVE' ? 'bg-emerald-400 animate-pulse' : 'bg-indigo-400' }}"></span>
                    Siklus Hidup Pegawai (Employee Life-Cycle State)
                </span>
                <span class="text-slate-400">
                    Fase Saat Ini: <strong class="text-white">{{ $stage }}</strong>
                </span>
            </div>

            <div class="grid grid-cols-5 gap-2 text-center text-[11px] font-semibold">
                @php
                    $stages = [
                        'ONBOARDING' => '1. Onboarding',
                        'ACTIVE' => '2. Active Working',
                        'SUSPENDED' => '3. Suspended',
                        'OFFBOARDING' => '4. Offboarding',
                        'TERMINATED' => '5. Terminated / Alumni'
                    ];
                    $reached = true;
                @endphp
                @foreach($stages as $key => $label)
                    @php
                        $isCurrent = ($stage === $key);
                    @endphp
                    <div class="py-2 px-1 rounded-xl transition-all {{ $isCurrent ? 'bg-indigo-600 text-white shadow-md' : 'bg-slate-800/80 text-slate-400 border border-slate-700/60' }}">
                        <span class="block truncate">{{ $label }}</span>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Quick Dynamic Metrics -->
        <div class="grid grid-cols-2 sm:grid-cols-4 border-t border-slate-100 bg-slate-50/50 divide-x divide-slate-100 text-center">
            <div class="p-4">
                <span class="text-xs text-slate-400 font-medium block uppercase tracking-wider">Usia (Dihitung Dinamis)</span>
                <span class="text-lg font-bold text-slate-800 mt-0.5 block">
                    {{ $employee->age ? $employee->age . ' Tahun' : '-' }}
                </span>
            </div>
            <div class="p-4">
                <span class="text-xs text-slate-400 font-medium block uppercase tracking-wider">Masa Kerja (Dinamis)</span>
                <span class="text-lg font-bold text-indigo-600 mt-0.5 block">
                    {{ $employee->tenure ?? '-' }}
                </span>
            </div>
            <div class="p-4">
                <span class="text-xs text-slate-400 font-medium block uppercase tracking-wider">TMT Bergabung</span>
                <span class="text-lg font-bold text-slate-800 mt-0.5 block">
                    {{ $employee->join_date ? $employee->join_date->translatedFormat('d M Y') : '-' }}
                </span>
            </div>
            <div class="p-4">
                <span class="text-xs text-slate-400 font-medium block uppercase tracking-wider">Status Kontrak</span>
                <span class="text-lg font-bold {{ $employee->end_date ? 'text-amber-600' : 'text-slate-800' }} mt-0.5 block">
                    {{ $employee->end_date ? 's/d ' . $employee->end_date->translatedFormat('d M Y') : 'PKWTT / Tetap' }}
                </span>
            </div>
        </div>
    </div>

    <!-- 2 Column Details Layout -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        <!-- Left Column: Personal, Payroll, Custom Fields (1 col) -->
        <div class="space-y-6">

            <!-- Data Personal & KTP -->
            <div class="bg-white p-6 rounded-2xl border border-slate-200/80 shadow-xs space-y-4">
                <h3 class="text-base font-bold text-slate-900 border-b border-slate-100 pb-3 flex items-center gap-2">
                    <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0m-5 8a2 2 0 100-4 2 2 0 000 4zm0 0c1.306 0 2.417.835 2.83 2M9 14a3.001 3.001 0 00-2.83 2M15 11h3m-3 4h2"></path>
                    </svg>
                    <span>Identitas Personal</span>
                </h3>

                <dl class="space-y-3 text-sm">
                    <div>
                        <dt class="text-xs text-slate-400 font-medium">Nomor KTP (NIK Kependudukan)</dt>
                        <dd class="font-mono font-semibold text-slate-800 mt-0.5">{{ $employee->ktp_number }}</dd>
                    </div>
                    <div class="grid grid-cols-2 gap-2">
                        <div>
                            <dt class="text-xs text-slate-400 font-medium">Jenis Kelamin</dt>
                            <dd class="font-medium text-slate-800 mt-0.5">{{ $employee->gender === 'MALE' ? 'Laki-laki' : 'Perempuan' }}</dd>
                        </div>
                        <div>
                            <dt class="text-xs text-slate-400 font-medium">Tanggal Lahir</dt>
                            <dd class="font-medium text-slate-800 mt-0.5">{{ $employee->birth_date ? $employee->birth_date->translatedFormat('d M Y') : '-' }}</dd>
                        </div>
                    </div>
                    <div class="grid grid-cols-2 gap-2">
                        <div>
                            <dt class="text-xs text-slate-400 font-medium">Agama</dt>
                            <dd class="font-medium text-slate-800 mt-0.5">{{ $employee->religion ?? '-' }}</dd>
                        </div>
                        <div>
                            <dt class="text-xs text-slate-400 font-medium">Status Pernikahan</dt>
                            <dd class="font-medium text-slate-800 mt-0.5">{{ $employee->marital_status ?? 'SINGLE' }}</dd>
                        </div>
                    </div>
                    <div>
                        <dt class="text-xs text-slate-400 font-medium">Alamat Sesuai KTP</dt>
                        <dd class="text-slate-700 mt-0.5 leading-relaxed">{{ $employee->ktp_address ?? '-' }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs text-slate-400 font-medium">Alamat Domisili</dt>
                        <dd class="text-slate-700 mt-0.5 leading-relaxed">{{ $employee->current_address ?? 'Sama dengan KTP' }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs text-slate-400 font-medium">Kontak Pribadi</dt>
                        <dd class="text-slate-800 mt-0.5">
                            <div>Email: <a href="mailto:{{ $employee->email }}" class="text-indigo-600 hover:underline">{{ $employee->email }}</a></div>
                            <div>No. HP: <span class="font-mono">{{ $employee->phone_number ?? '-' }}</span></div>
                        </dd>
                    </div>
                </dl>
            </div>

            <!-- Data Pajak & Payroll -->
            <div class="bg-white p-6 rounded-2xl border border-slate-200/80 shadow-xs space-y-4">
                <h3 class="text-base font-bold text-slate-900 border-b border-slate-100 pb-3 flex items-center gap-2">
                    <svg class="w-5 h-5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path>
                    </svg>
                    <span>Pajak (PPh 21 TER) & Payroll</span>
                </h3>

                <dl class="space-y-3 text-sm">
                    <div class="p-3 bg-amber-50/60 border border-amber-200/60 rounded-xl">
                        <dt class="text-xs text-amber-800 font-semibold uppercase tracking-wider">Status PTKP (Kunci Pajak TER)</dt>
                        <dd class="text-lg font-extrabold text-amber-900 mt-0.5">{{ $employee->ptkp_status ?? 'TK/0' }}</dd>
                        <span class="text-[11px] text-amber-700 block mt-0.5">Digunakan untuk penentuan skema TER PPh 21 Pasal 21</span>
                    </div>

                    <div>
                        <dt class="text-xs text-slate-400 font-medium">Nomor Pokok Wajib Pajak (NPWP)</dt>
                        <dd class="font-mono font-medium text-slate-800 mt-0.5">{{ $employee->npwp ?? 'Tidak / Belum ada' }}</dd>
                    </div>

                    <div class="grid grid-cols-2 gap-2">
                        <div>
                            <dt class="text-xs text-slate-400 font-medium">BPJS Ketenagakerjaan</dt>
                            <dd class="font-mono font-medium text-slate-800 mt-0.5">{{ $employee->bpjs_ketenagakerjaan_no ?? '-' }}</dd>
                        </div>
                        <div>
                            <dt class="text-xs text-slate-400 font-medium">BPJS Kesehatan</dt>
                            <dd class="font-mono font-medium text-slate-800 mt-0.5">{{ $employee->bpjs_kesehatan_no ?? '-' }}</dd>
                        </div>
                    </div>

                    <div class="p-3 bg-slate-50 border border-slate-100 rounded-xl">
                        <dt class="text-xs text-slate-500 font-semibold uppercase tracking-wider">Rekening Bank Payroll</dt>
                        <dd class="mt-1 space-y-0.5">
                            <div class="font-bold text-slate-900">{{ $employee->bank_name ?? 'Belum ditentukan' }}</div>
                            <div class="font-mono text-indigo-700 font-semibold text-base">{{ $employee->bank_account_number ?? '-' }}</div>
                            <div class="text-xs text-slate-500">a.n. {{ $employee->bank_account_holder ?? $employee->full_name }}</div>
                        </dd>
                    </div>
                </dl>
            </div>

            <!-- Custom Fields (JSONB) -->
            @if ($employee->custom_fields && count($employee->custom_fields) > 0)
                <div class="bg-white p-6 rounded-2xl border border-slate-200/80 shadow-xs space-y-4">
                    <h3 class="text-base font-bold text-slate-900 border-b border-slate-100 pb-3 flex items-center gap-2">
                        <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                        </svg>
                        <span>Atribut Khusus (JSONB)</span>
                    </h3>

                    <div class="grid grid-cols-2 gap-3 text-sm">
                        @foreach ($employee->custom_fields as $key => $val)
                            <div class="p-3 bg-slate-50 rounded-xl">
                                <span class="text-xs text-slate-400 font-medium block uppercase">{{ str_replace('_', ' ', $key) }}</span>
                                <span class="font-bold text-slate-800 text-sm mt-0.5 block">{{ is_array($val) ? json_encode($val) : $val }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

        </div>

        <!-- Right Column: Career Movements, Contracts History & Educations (2 cols) -->
        <div class="lg:col-span-2 space-y-6">

            <!-- RIWAYAT MUTASI & PERJALANAN KARIR (EMPLOYEE CAREER MOVEMENT) -->
            <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
                <div class="p-5 border-b border-slate-100 flex items-center justify-between">
                    <div>
                        <div class="flex items-center gap-2">
                            <h3 class="font-bold text-slate-900 text-base">Perjalanan Karir & Mutasi / Promosi</h3>
                            <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-indigo-50 text-indigo-700 border border-indigo-200">
                                Life-Cycle Movement
                            </span>
                        </div>
                        <p class="text-xs text-slate-400 mt-0.5">Mencatat promosi, mutasi divisi, demosi, dan transisi status kepegawaian dengan nomor SK resmi</p>
                    </div>
                    <button type="button" 
                            onclick="document.getElementById('form-add-career').classList.toggle('hidden')" 
                            class="px-3.5 py-1.5 bg-indigo-50 hover:bg-indigo-100 text-indigo-700 text-xs font-semibold rounded-lg transition-colors flex items-center gap-1.5 cursor-pointer">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                        </svg>
                        <span>+ Catat Mutasi / Promosi</span>
                    </button>
                </div>

                <!-- Form Tambah Mutasi / Promosi (Collapsible) -->
                <div id="form-add-career" class="hidden p-5 bg-indigo-50/40 border-b border-indigo-100">
                    <form action="{{ route('employees.careers.store', $employee) }}" method="POST" class="space-y-4">
                        @csrf
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                            <div>
                                <label class="block text-xs font-semibold text-slate-700 mb-1">Jenis Pergerakan Karir *</label>
                                <select name="transition_type" required class="w-full px-3 py-2 text-sm bg-white border border-slate-200 rounded-lg">
                                    <option value="PROMOTION">PROMOSI (Kenaikan Jabatan / Level)</option>
                                    <option value="MUTATION">MUTASI (Pindah Divisi / Cabang)</option>
                                    <option value="DEMOTION">DEMOSI (Penyesuaian Tanggung Jawab)</option>
                                    <option value="JOIN">JOIN (Penetapan / Onboarding Awal)</option>
                                    <option value="RESIGN">RESIGN (Pengunduran Diri)</option>
                                    <option value="TERMINATE">TERMINATE (PHK / Pensiun)</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-slate-700 mb-1">Tanggal Mulai Berlaku SK *</label>
                                <input type="date" name="effective_date" value="{{ date('Y-m-d') }}" required class="w-full px-3 py-2 text-sm bg-white border border-slate-200 rounded-lg">
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-slate-700 mb-1">Nomor Surat Keputusan (SK)</label>
                                <input type="text" name="reference_doc_no" placeholder="Contoh: SK/045/DIR-HC/X/2026" class="w-full px-3 py-2 text-sm bg-white border border-slate-200 rounded-lg">
                            </div>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                            <div>
                                <label class="block text-xs font-semibold text-slate-700 mb-1">Unit Kerja / Departemen Baru</label>
                                <select name="new_department_id" class="w-full px-3 py-2 text-sm bg-white border border-slate-200 rounded-lg">
                                    <option value="">— Tetap di Unit Saat Ini —</option>
                                    @foreach($departments as $dept)
                                        <option value="{{ $dept->id }}" {{ $employee->department_id == $dept->id ? 'selected' : '' }}>
                                            {{ $dept->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-slate-700 mb-1">Jabatan / Posisi Baru</label>
                                <select name="new_position_id" class="w-full px-3 py-2 text-sm bg-white border border-slate-200 rounded-lg">
                                    <option value="">— Tetap di Jabatan Saat Ini —</option>
                                    @foreach($positions as $pos)
                                        <option value="{{ $pos->id }}" {{ $employee->position_id == $pos->id ? 'selected' : '' }}>
                                            {{ $pos->title }} ({{ $pos->level ?? 'Staff' }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-slate-700 mb-1">Update Status Life-Cycle</label>
                                <select name="lifecycle_stage" class="w-full px-3 py-2 text-sm bg-white border border-slate-200 rounded-lg">
                                    <option value="ACTIVE" {{ $employee->lifecycle_stage === 'ACTIVE' ? 'selected' : '' }}>ACTIVE (Aktif Bekerja)</option>
                                    <option value="ONBOARDING" {{ $employee->lifecycle_stage === 'ONBOARDING' ? 'selected' : '' }}>ONBOARDING (Masa Orientasi)</option>
                                    <option value="SUSPENDED" {{ $employee->lifecycle_stage === 'SUSPENDED' ? 'selected' : '' }}>SUSPENDED (Skorsing/Cuti Diluar Tanggungan)</option>
                                    <option value="OFFBOARDING" {{ $employee->lifecycle_stage === 'OFFBOARDING' ? 'selected' : '' }}>OFFBOARDING (Masa Clearance)</option>
                                    <option value="TERMINATED" {{ $employee->lifecycle_stage === 'TERMINATED' ? 'selected' : '' }}>TERMINATED (Resmi Keluar)</option>
                                </select>
                            </div>
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">Alasan / Catatan Perubahan Karir</label>
                            <input type="text" name="notes" placeholder="Contoh: Kenaikan jabatan atas kinerja Q3 luar biasa..." class="w-full px-3 py-2 text-sm bg-white border border-slate-200 rounded-lg">
                        </div>

                        <div class="flex justify-end gap-2 pt-2">
                            <button type="button" onclick="document.getElementById('form-add-career').classList.add('hidden')" class="px-3.5 py-1.5 text-xs text-slate-600 bg-white border border-slate-200 rounded-lg">Batal</button>
                            <button type="submit" class="px-4 py-1.5 text-xs font-bold text-white bg-indigo-600 rounded-lg shadow-sm">Simpan SK & Perubahan Karir</button>
                        </div>
                    </form>
                </div>

                <!-- List Riwayat Karir -->
                @if ($employee->careerHistories->isEmpty())
                    <div class="p-8 text-center text-slate-400 text-sm">
                        Belum ada catatan mutasi atau promosi karir.
                    </div>
                @else
                    <div class="divide-y divide-slate-100">
                        @foreach ($employee->careerHistories as $career)
                            @php
                                $typeBadge = match($career->transition_type) {
                                    'PROMOTION' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                                    'MUTATION' => 'bg-blue-50 text-blue-700 border-blue-200',
                                    'DEMOTION' => 'bg-amber-50 text-amber-700 border-amber-200',
                                    'JOIN' => 'bg-indigo-50 text-indigo-700 border-indigo-200',
                                    'RESIGN', 'TERMINATE' => 'bg-rose-50 text-rose-700 border-rose-200',
                                    default => 'bg-slate-100 text-slate-700 border-slate-200'
                                };
                            @endphp
                            <div class="p-4 flex items-start justify-between hover:bg-slate-50/60 transition-colors">
                                <div class="space-y-1">
                                    <div class="flex items-center gap-2">
                                        <span class="px-2.5 py-0.5 rounded text-xs font-extrabold border {{ $typeBadge }}">
                                            {{ $career->transition_type }}
                                        </span>
                                        <span class="text-xs font-bold text-slate-800">
                                            {{ $career->effective_date ? $career->effective_date->translatedFormat('d M Y') : '-' }}
                                        </span>
                                        @if ($career->reference_doc_no)
                                            <span class="text-xs text-slate-500 font-mono">
                                                &bull; No: {{ $career->reference_doc_no }}
                                            </span>
                                        @endif
                                    </div>

                                    <div class="text-xs text-slate-600 flex flex-wrap items-center gap-1.5 pt-1">
                                        @if ($career->oldPosition || $career->newPosition)
                                            <span>Jabatan: 
                                                <strong class="text-slate-700">{{ $career->oldPosition?->title ?? 'Awal' }}</strong>
                                                &rarr;
                                                <strong class="text-indigo-600">{{ $career->newPosition?->title ?? $career->oldPosition?->title }}</strong>
                                            </span>
                                            <span>&bull;</span>
                                        @endif
                                        @if ($career->oldDepartment || $career->newDepartment)
                                            <span>Unit: 
                                                <strong class="text-slate-700">{{ $career->oldDepartment?->name ?? 'Awal' }}</strong>
                                                &rarr;
                                                <strong class="text-indigo-600">{{ $career->newDepartment?->name ?? $career->oldDepartment?->name }}</strong>
                                            </span>
                                        @endif
                                    </div>

                                    @if ($career->notes)
                                        <p class="text-xs text-slate-400 italic pt-0.5">{{ $career->notes }}</p>
                                    @endif
                                </div>

                                <form action="{{ route('employees.careers.destroy', [$employee, $career]) }}" method="POST" onsubmit="return confirm('Hapus jejak riwayat karir ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-xs text-rose-500 hover:text-rose-700 p-1.5 hover:bg-rose-50 rounded-lg transition-colors">
                                        Hapus
                                    </button>
                                </form>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

            <!-- RIWAYAT KONTRAK KERJA (PKWT) -->
            <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
                <div class="p-5 border-b border-slate-100 flex items-center justify-between">
                    <div>
                        <h3 class="font-bold text-slate-900 text-base">Histori Dokumen & Kontrak Kerja</h3>
                        <p class="text-xs text-slate-400 mt-0.5">Jejak perpanjangan PKWT tanpa menimpa histori dokumen terdahulu</p>
                    </div>
                    <button type="button" 
                            onclick="document.getElementById('form-add-contract').classList.toggle('hidden')" 
                            class="px-3.5 py-1.5 bg-indigo-50 hover:bg-indigo-100 text-indigo-700 text-xs font-semibold rounded-lg transition-colors flex items-center gap-1.5">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                        </svg>
                        <span>+ Tambah Kontrak</span>
                    </button>
                </div>

                <!-- Form Tambah Kontrak (Collapsible) -->
                <div id="form-add-contract" class="hidden p-5 bg-indigo-50/40 border-b border-indigo-100">
                    <form action="{{ route('employees.contracts.store', $employee) }}" method="POST" class="space-y-4">
                        @csrf
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-semibold text-slate-700 mb-1">Nomor Kontrak *</label>
                                <input type="text" name="contract_number" required placeholder="Contoh: 002/PKWT-EXT/2026" class="w-full px-3 py-2 text-sm bg-white border border-slate-200 rounded-lg">
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-slate-700 mb-1">Tipe Kontrak *</label>
                                <select name="contract_type" required class="w-full px-3 py-2 text-sm bg-white border border-slate-200 rounded-lg">
                                    <option value="PKWT-1">PKWT 1 (Tahun Pertama)</option>
                                    <option value="PKWT-2">PKWT 2 (Perpanjangan)</option>
                                    <option value="PKWTT">PKWTT (Pengangkatan Tetap)</option>
                                    <option value="PROBATION">Probation / Masa Percobaan</option>
                                    <option value="MAGANG">Magang</option>
                                </select>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-semibold text-slate-700 mb-1">Tanggal Mulai *</label>
                                <input type="date" name="start_date" required class="w-full px-3 py-2 text-sm bg-white border border-slate-200 rounded-lg">
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-slate-700 mb-1">Tanggal Selesai (Jika PKWT)</label>
                                <input type="date" name="end_date" class="w-full px-3 py-2 text-sm bg-white border border-slate-200 rounded-lg">
                            </div>
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">Catatan Tambahan</label>
                            <input type="text" name="notes" placeholder="Keterangan perpanjangan atau kesepakatan baru..." class="w-full px-3 py-2 text-sm bg-white border border-slate-200 rounded-lg">
                        </div>

                        <div class="flex justify-end gap-2 pt-2">
                            <button type="button" onclick="document.getElementById('form-add-contract').classList.add('hidden')" class="px-3 py-1.5 text-xs text-slate-600 bg-white border border-slate-200 rounded-lg">Batal</button>
                            <button type="submit" class="px-4 py-1.5 text-xs font-bold text-white bg-indigo-600 rounded-lg">Simpan Kontrak</button>
                        </div>
                    </form>
                </div>

                <!-- List Kontrak -->
                @if ($employee->contracts->isEmpty())
                    <div class="p-8 text-center text-slate-400 text-sm">
                        Belum ada dokumen histori kontrak yang tercatat.
                    </div>
                @else
                    <div class="divide-y divide-slate-100">
                        @foreach ($employee->contracts as $contract)
                            <div class="p-4 flex items-center justify-between hover:bg-slate-50/60 transition-colors">
                                <div>
                                    <div class="flex items-center gap-2">
                                        <span class="font-bold text-slate-800 text-sm">{{ $contract->contract_number }}</span>
                                        <span class="px-2 py-0.5 rounded text-[11px] font-semibold bg-slate-100 text-slate-700 border border-slate-200">
                                            {{ $contract->contract_type }}
                                        </span>
                                    </div>
                                    <p class="text-xs text-slate-500 mt-1">
                                        Periode: <strong class="text-slate-700">{{ $contract->start_date ? $contract->start_date->translatedFormat('d M Y') : '-' }}</strong> 
                                        s/d 
                                        <strong class="text-slate-700">{{ $contract->end_date ? $contract->end_date->translatedFormat('d M Y') : 'Seterusnya' }}</strong>
                                    </p>
                                    @if($contract->notes)
                                        <p class="text-xs text-slate-400 mt-0.5 italic">{{ $contract->notes }}</p>
                                    @endif
                                </div>

                                <form action="{{ route('employees.contracts.destroy', [$employee, $contract]) }}" method="POST" onsubmit="return confirm('Hapus histori kontrak ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-xs text-rose-500 hover:text-rose-700 p-1.5 hover:bg-rose-50 rounded-lg transition-colors">
                                        Hapus
                                    </button>
                                </form>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

            <!-- RIWAYAT PENDIDIKAN (1 to Many) -->
            <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
                <div class="p-5 border-b border-slate-100 flex items-center justify-between">
                    <div>
                        <h3 class="font-bold text-slate-900 text-base">Riwayat Pendidikan Formal</h3>
                        <p class="text-xs text-slate-400 mt-0.5">Membedakan kualifikasi pendidikan yang diakui perusahaan vs dimiliki</p>
                    </div>
                    <button type="button" 
                            onclick="document.getElementById('form-add-education').classList.toggle('hidden')" 
                            class="px-3.5 py-1.5 bg-indigo-50 hover:bg-indigo-100 text-indigo-700 text-xs font-semibold rounded-lg transition-colors flex items-center gap-1.5">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                        </svg>
                        <span>+ Tambah Pendidikan</span>
                    </button>
                </div>

                <!-- Form Tambah Pendidikan (Collapsible) -->
                <div id="form-add-education" class="hidden p-5 bg-indigo-50/40 border-b border-indigo-100">
                    <form action="{{ route('employees.educations.store', $employee) }}" method="POST" class="space-y-4">
                        @csrf
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                            <div>
                                <label class="block text-xs font-semibold text-slate-700 mb-1">Jenjang Pendidikan *</label>
                                <select name="education_level" required class="w-full px-3 py-2 text-sm bg-white border border-slate-200 rounded-lg">
                                    <option value="SMA/SMK">SMA / SMK</option>
                                    <option value="D3">D3 (Diploma)</option>
                                    <option value="D4">D4 (Sarjana Terapan)</option>
                                    <option value="S1" selected>S1 (Sarjana)</option>
                                    <option value="S2">S2 (Magister)</option>
                                    <option value="S3">S3 (Doktor)</option>
                                </select>
                            </div>
                            <div class="sm:col-span-2">
                                <label class="block text-xs font-semibold text-slate-700 mb-1">Jurusan / Program Studi *</label>
                                <input type="text" name="major" required placeholder="Contoh: Manajemen, Teknik Industri" class="w-full px-3 py-2 text-sm bg-white border border-slate-200 rounded-lg">
                            </div>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                            <div class="sm:col-span-2">
                                <label class="block text-xs font-semibold text-slate-700 mb-1">Nama Perguruan Tinggi / Sekolah *</label>
                                <input type="text" name="institution_name" required placeholder="Contoh: Universitas Indonesia" class="w-full px-3 py-2 text-sm bg-white border border-slate-200 rounded-lg">
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-slate-700 mb-1">Tahun Kelulusan</label>
                                <input type="number" name="graduation_year" min="1950" max="{{ date('Y') + 5 }}" placeholder="{{ date('Y') }}" class="w-full px-3 py-2 text-sm bg-white border border-slate-200 rounded-lg">
                            </div>
                        </div>

                        <div class="flex items-center gap-2 pt-1">
                            <input type="checkbox" id="is_recognized" name="is_recognized" value="1" checked class="w-4 h-4 text-indigo-600 rounded">
                            <label for="is_recognized" class="text-xs font-semibold text-slate-800">
                                Pendidikan Diakui Perusahaan (Menjadi dasar kualifikasi jabatan & grading gaji)
                            </label>
                        </div>

                        <div class="flex justify-end gap-2 pt-2">
                            <button type="button" onclick="document.getElementById('form-add-education').classList.add('hidden')" class="px-3 py-1.5 text-xs text-slate-600 bg-white border border-slate-200 rounded-lg">Batal</button>
                            <button type="submit" class="px-4 py-1.5 text-xs font-bold text-white bg-indigo-600 rounded-lg">Simpan Riwayat</button>
                        </div>
                    </form>
                </div>

                <!-- List Pendidikan -->
                @if ($employee->educations->isEmpty())
                    <div class="p-8 text-center text-slate-400 text-sm">
                        Belum ada riwayat pendidikan yang dicatat.
                    </div>
                @else
                    <div class="divide-y divide-slate-100">
                        @foreach ($employee->educations as $edu)
                            <div class="p-4 flex items-center justify-between hover:bg-slate-50/60 transition-colors">
                                <div class="flex items-start gap-3">
                                    <div class="w-10 h-10 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center font-bold text-xs shrink-0 mt-0.5">
                                        {{ $edu->education_level }}
                                    </div>
                                    <div>
                                        <div class="flex items-center gap-2">
                                            <h4 class="font-bold text-slate-900 text-sm">{{ $edu->major }}</h4>
                                            @if ($edu->is_recognized)
                                                <span class="px-2 py-0.5 rounded text-[10px] font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                                    Pendidikan Diakui
                                                </span>
                                            @else
                                                <span class="px-2 py-0.5 rounded text-[10px] font-semibold bg-slate-100 text-slate-600">
                                                    Pendidikan Dimiliki
                                                </span>
                                            @endif
                                        </div>
                                        <p class="text-xs text-slate-600 mt-0.5">
                                            {{ $edu->institution_name }} 
                                            @if($edu->graduation_year)
                                                &bull; Lulus Tahun {{ $edu->graduation_year }}
                                            @endif
                                        </p>
                                    </div>
                                </div>

                                <form action="{{ route('employees.educations.destroy', [$employee, $edu]) }}" method="POST" onsubmit="return confirm('Hapus riwayat pendidikan ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-xs text-rose-500 hover:text-rose-700 p-1.5 hover:bg-rose-50 rounded-lg transition-colors">
                                        Hapus
                                    </button>
                                </form>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

            <!-- TIM BAWAHAN (SUBORDINATES) JIKA MANAGER -->
            @if ($employee->subordinates->isNotEmpty())
                <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
                    <div class="p-5 border-b border-slate-100">
                        <h3 class="font-bold text-slate-900 text-base">Bawahan Langsung (Direct Reports)</h3>
                        <p class="text-xs text-slate-400 mt-0.5">Daftar staf yang melapor langsung ke {{ $employee->full_name }}</p>
                    </div>
                    <div class="divide-y divide-slate-100">
                        @foreach ($employee->subordinates as $sub)
                            <div class="p-3.5 px-5 flex items-center justify-between hover:bg-slate-50 transition-colors">
                                <div>
                                    <a href="{{ route('employees.show', $sub) }}" class="font-semibold text-slate-900 text-sm hover:text-indigo-600 hover:underline">
                                        {{ $sub->full_name }}
                                    </a>
                                    <p class="text-xs text-slate-400 font-mono">NIK: {{ $sub->nik }} &bull; {{ $sub->position?->title ?? '-' }}</p>
                                </div>
                                <span class="px-2.5 py-0.5 rounded-full text-xs font-medium {{ $sub->employment_status === 'PKWTT' ? 'bg-emerald-50 text-emerald-700' : 'bg-blue-50 text-blue-700' }}">
                                    {{ $sub->employment_status }}
                                </span>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

        </div>

    </div>

</div>
@endsection
