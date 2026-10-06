<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-slate-50">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Masuk Portal Karyawan (ESS) - HRIS Core</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700" rel="stylesheet" />

    <!-- Scripts & Styles via Vite -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen font-sans antialiased text-slate-800 flex items-center justify-center p-4 sm:p-6 lg:p-8 py-8 bg-slate-50 relative overflow-y-auto selection:bg-amber-600 selection:text-white">

    <!-- Ambient background glow effects (Amber & Warm Theme) -->
    <div class="absolute -top-40 -left-40 w-96 h-96 bg-amber-100/70 rounded-full blur-3xl pointer-events-none"></div>
    <div class="absolute -bottom-40 -right-40 w-96 h-96 bg-orange-100/70 rounded-full blur-3xl pointer-events-none"></div>

    <div class="w-full max-w-5xl bg-white border border-slate-200/90 rounded-3xl shadow-xl overflow-hidden grid grid-cols-1 lg:grid-cols-12 relative z-10 my-auto">

        <!-- Left Column: Employee ESS Pitch & Features (7 cols on lg) -->
        <div class="lg:col-span-7 bg-gradient-to-br from-amber-50/70 via-slate-50/60 to-white p-8 sm:p-12 flex flex-col justify-between border-b lg:border-b-0 lg:border-r border-slate-200/90 relative">
            <div>
                <!-- Brand Header -->
                <div class="flex items-center space-x-3 mb-8">
                    <div class="w-12 h-12 rounded-2xl bg-gradient-to-tr from-amber-500 to-orange-600 flex items-center justify-center text-white font-extrabold text-xl shadow-md shadow-amber-500/20">
                        ESS
                    </div>
                    <div>
                        <span class="font-extrabold text-slate-900 text-xl tracking-wide block leading-tight">HRIS Portal Karyawan</span>
                        <span class="text-xs text-amber-700 font-bold tracking-widest uppercase">Employee Self-Service (ESS)</span>
                    </div>
                </div>

                <!-- Main Pitch -->
                <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight leading-snug">
                    Layanan Mandiri Presensi, Cuti, & Administrasi Karyawan
                </h1>
                <p class="text-slate-600 text-sm mt-3 leading-relaxed">
                    Akses cepat untuk pencatatan presensi selfie & geolokasi, cek sisa cuti tahunan, pengajuan lembur, dan jadwal kerja harian Anda.
                </p>

                <!-- Value Highlights for Employee -->
                <div class="mt-8 space-y-4">
                    <div class="flex items-start gap-3.5 p-3.5 rounded-2xl bg-white border border-slate-200/90 shadow-xs">
                        <div class="w-8 h-8 rounded-xl bg-amber-50 text-amber-700 border border-amber-200 flex items-center justify-center shrink-0 mt-0.5">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            </svg>
                        </div>
                        <div>
                            <h4 class="text-sm font-bold text-slate-900">Presensi Mandiri Selfie & Geofence GPS</h4>
                            <p class="text-xs text-slate-600 mt-0.5">Check-in dan check-out kerja harian akurat langsung dari ponsel atau browser Anda.</p>
                        </div>
                    </div>

                    <div class="flex items-start gap-3.5 p-3.5 rounded-2xl bg-white border border-slate-200/90 shadow-xs">
                        <div class="w-8 h-8 rounded-xl bg-emerald-50 text-emerald-600 border border-emerald-200 flex items-center justify-center shrink-0 mt-0.5">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                            </svg>
                        </div>
                        <div>
                            <h4 class="text-sm font-bold text-slate-900">Pengajuan Cuti & Izin Real-Time</h4>
                            <p class="text-xs text-slate-600 mt-0.5">Pantau kuota cuti tahunan berjalan dan status persetujuan manajer tanpa birokrasi manual.</p>
                        </div>
                    </div>

                    <div class="flex items-start gap-3.5 p-3.5 rounded-2xl bg-white border border-slate-200/90 shadow-xs">
                        <div class="w-8 h-8 rounded-xl bg-blue-50 text-blue-600 border border-blue-200 flex items-center justify-center shrink-0 mt-0.5">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <div>
                            <h4 class="text-sm font-bold text-slate-900">Roster Jadwal Shift & Lembur</h4>
                            <p class="text-xs text-slate-600 mt-0.5">Lihat jadwal kerja, lokasi penempatan kantor, serta ajukan lembur dengan kalkulasi resmi.</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Footer Badge -->
            <div class="mt-8 pt-6 border-t border-slate-200 flex items-center justify-between text-xs text-slate-500">
                <span class="flex items-center gap-1.5 font-medium">
                    <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                    Sistem Operasional Karyawan Aktif
                </span>
                <span class="font-medium text-slate-400">Mobile Responsive</span>
            </div>
        </div>

        <!-- Right Column: Login Form (5 cols on lg) -->
        <div class="lg:col-span-5 p-8 sm:p-12 bg-white flex flex-col justify-between">
            <div>
                <div class="mb-6">
                    <div class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-xs font-bold bg-amber-50 text-amber-700 border border-amber-200 mb-3">
                        <span class="w-1.5 h-1.5 rounded-full bg-amber-600"></span>
                        Portal Layanan Mandiri Karyawan
                    </div>
                    <h2 class="text-2xl font-bold text-slate-900">Masuk Karyawan</h2>
                    <p class="text-slate-500 text-sm mt-1">Gunakan akun internal perusahaan Anda</p>
                </div>

                <!-- Alert Messages -->
                @if (session('success'))
                    <div class="mb-5 p-3.5 bg-emerald-50 border border-emerald-200 text-emerald-800 text-sm rounded-xl flex items-center gap-2 shadow-xs">
                        <svg class="w-4 h-4 shrink-0 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                        <span>{{ session('success') }}</span>
                    </div>
                @endif

                @if ($errors->any())
                    <div class="mb-5 p-4 bg-rose-50 border border-rose-200 text-rose-700 text-xs rounded-xl shadow-xs space-y-2">
                        <ul class="list-disc list-inside space-y-1">
                            @foreach ($errors->all() as $error)
                                <li class="font-medium">{{ $error }}</li>
                            @endforeach
                        </ul>
                        @if (session('redirect_portal_url'))
                            <div class="pt-2 border-t border-rose-200/60">
                                <a href="{{ session('redirect_portal_url') }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-bold bg-rose-600 hover:bg-rose-700 text-white shadow-xs transition-all">
                                    {{ session('redirect_portal_label') ?? 'Buka Portal Terkait' }} &rarr;
                                </a>
                            </div>
                        @endif
                    </div>
                @endif

                <!-- Demo Account Quick Autofill -->
                <div class="mb-6 p-4 rounded-2xl bg-amber-50/70 border border-amber-200/80 shadow-xs">
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-xs font-bold text-amber-800 uppercase tracking-wider">Akun Demo Karyawan</span>
                        <button type="button" 
                                id="btn-autofill-employee"
                                class="text-xs font-semibold text-amber-700 hover:text-amber-900 underline cursor-pointer">
                            Isi Otomatis
                        </button>
                    </div>
                    <div class="text-xs font-mono text-slate-700 space-y-0.5">
                        <div>Email: <strong class="text-slate-900">karyawan@hris.local</strong></div>
                        <div>Password: <strong class="text-slate-900">password123</strong></div>
                        <div class="text-[11px] text-slate-500 pt-0.5">(Budi Santoso - Senior Software Engineer)</div>
                    </div>
                </div>

                <!-- Form Login Karyawan -->
                <form action="{{ route('employee.login.submit') }}" method="POST" class="space-y-5">
                    @csrf

                    <!-- Email Input -->
                    <div>
                        <label for="email" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-2">
                            Alamat Email Karyawan
                        </label>
                        <div class="relative">
                            <input type="email" 
                                   id="email" 
                                   name="email" 
                                   value="{{ old('email', 'karyawan@hris.local') }}" 
                                   required 
                                   autofocus 
                                   placeholder="nama@hris.corp" 
                                   class="w-full pl-11 pr-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-slate-900 placeholder-slate-400 text-sm focus:outline-none focus:bg-white focus:ring-2 focus:ring-amber-500 focus:border-transparent transition-all">
                            <svg class="w-5 h-5 text-slate-400 absolute left-3.5 top-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207"></path>
                            </svg>
                        </div>
                    </div>

                    <!-- Password Input -->
                    <div>
                        <div class="flex items-center justify-between mb-2">
                            <label for="password" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider">
                                Kata Sandi
                            </label>
                        </div>
                        <div class="relative">
                            <input type="password" 
                                   id="password" 
                                   name="password" 
                                   value="password123"
                                   required 
                                   placeholder="••••••••" 
                                   class="w-full pl-11 pr-11 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-slate-900 placeholder-slate-400 text-sm focus:outline-none focus:bg-white focus:ring-2 focus:ring-amber-500 focus:border-transparent transition-all">
                            <svg class="w-5 h-5 text-slate-400 absolute left-3.5 top-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                            </svg>
                            <!-- Toggle Password Visibility -->
                            <button type="button" id="btn-toggle-pwd" class="absolute right-3.5 top-3 text-slate-400 hover:text-slate-600 focus:outline-none cursor-pointer">
                                <svg id="eye-icon" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                </svg>
                            </button>
                        </div>
                    </div>

                    <!-- Remember Me -->
                    <div class="flex items-center justify-between pt-1">
                        <label class="flex items-center space-x-2 text-xs text-slate-600 cursor-pointer">
                            <input type="checkbox" name="remember" value="1" checked class="w-4 h-4 rounded bg-slate-50 border-slate-300 text-amber-600 focus:ring-amber-500">
                            <span>Ingat sesi masuk saya</span>
                        </label>
                    </div>

                    <!-- Submit Button -->
                    <button type="submit" 
                            class="w-full py-3 px-4 rounded-xl bg-amber-600 hover:bg-amber-500 text-white font-bold text-sm shadow-md shadow-amber-600/20 transition-all focus:outline-none focus:ring-2 focus:ring-amber-500 cursor-pointer">
                        Masuk ke Portal Karyawan &rarr;
                    </button>
                </form>

                <!-- Role Switcher Links -->
                <div class="mt-6 pt-5 border-t border-slate-100 space-y-2">
                    <p class="text-[11px] font-semibold text-slate-400 uppercase tracking-wider text-center">Bukan Karyawan Perusahaan?</p>
                    <div class="grid grid-cols-2 gap-2 text-center text-xs">
                        <a href="{{ route('login') }}" class="p-2.5 rounded-xl border border-slate-200 bg-slate-50 hover:bg-indigo-50 hover:border-indigo-200 text-slate-700 hover:text-indigo-700 font-semibold transition-all">
                            Portal Administrator HR &rarr;
                        </a>
                        <a href="{{ route('career.login') }}" class="p-2.5 rounded-xl border border-slate-200 bg-slate-50 hover:bg-cyan-50 hover:border-cyan-200 text-slate-700 hover:text-cyan-700 font-semibold transition-all">
                            Portal Pelamar Karir &rarr;
                        </a>
                    </div>
                </div>
            </div>

            <!-- Footer note -->
            <div class="mt-8 text-center text-xs text-slate-500">
                &copy; {{ date('Y') }} HRIS Core Corporation &bull; ESS Portal
            </div>
        </div>

    </div>

    <!-- Interactive script for demo autofill and show/hide password -->
    <script>
        document.getElementById('btn-autofill-employee')?.addEventListener('click', function() {
            document.getElementById('email').value = 'karyawan@hris.local';
            document.getElementById('password').value = 'password123';
        });

        document.getElementById('btn-toggle-pwd')?.addEventListener('click', function() {
            const pwdInput = document.getElementById('password');
            if (pwdInput.type === 'password') {
                pwdInput.type = 'text';
            } else {
                pwdInput.type = 'password';
            }
        });
    </script>
</body>
</html>
