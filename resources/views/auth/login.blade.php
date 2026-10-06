<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-slate-50">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Masuk - HRIS Core Enterprise</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700" rel="stylesheet" />

    <!-- Scripts & Styles via Vite -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen font-sans antialiased text-slate-800 flex items-center justify-center p-4 sm:p-6 lg:p-8 py-8 bg-slate-50 relative overflow-y-auto selection:bg-indigo-600 selection:text-white">

    <!-- Ambient background glow effects -->
    <div class="absolute -top-40 -left-40 w-96 h-96 bg-indigo-100/70 rounded-full blur-3xl pointer-events-none"></div>
    <div class="absolute -bottom-40 -right-40 w-96 h-96 bg-violet-100/70 rounded-full blur-3xl pointer-events-none"></div>

    <div class="w-full max-w-5xl bg-white border border-slate-200/90 rounded-3xl shadow-xl overflow-hidden grid grid-cols-1 lg:grid-cols-12 relative z-10 my-auto">

        <!-- Left Column: Branding & Feature Highlights (7 cols on lg) -->
        <div class="lg:col-span-7 bg-gradient-to-br from-indigo-50/70 via-slate-50/60 to-white p-8 sm:p-12 flex flex-col justify-between border-b lg:border-b-0 lg:border-r border-slate-200/90 relative">
            <div>
                <!-- Brand Header -->
                <div class="flex items-center space-x-3 mb-8">
                    <div class="w-12 h-12 rounded-2xl bg-gradient-to-tr from-indigo-600 to-violet-600 flex items-center justify-center text-white font-extrabold text-xl shadow-md">
                        HR
                    </div>
                    <div>
                        <span class="font-extrabold text-slate-900 text-xl tracking-wide block leading-tight">HRIS Core</span>
                        <span class="text-xs text-indigo-600 font-bold tracking-widest uppercase">Enterprise Edition</span>
                    </div>
                </div>

                <!-- Main Pitch -->
                <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight leading-snug">
                    Sistem Informasi Manajemen Sumber Daya Manusia Terpadu
                </h1>
                <p class="text-slate-600 text-sm mt-3 leading-relaxed">
                    Dirancang dengan fondasi arsitektur database modern untuk standar korporat di Indonesia, siap terintegrasi penuh ke modul Attendance & Payroll.
                </p>

                <!-- Value Highlights -->
                <div class="mt-8 space-y-4">
                    <div class="flex items-start gap-3.5 p-3.5 rounded-2xl bg-white border border-slate-200/90 shadow-xs">
                        <div class="w-8 h-8 rounded-xl bg-indigo-50 text-indigo-600 border border-indigo-200 flex items-center justify-center shrink-0 mt-0.5">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                            </svg>
                        </div>
                        <div>
                            <h4 class="text-sm font-bold text-slate-900">Hirarki Departemen & Jabatan Dinamis</h4>
                            <p class="text-xs text-slate-600 mt-0.5">Relasi organisasi terstruktur dengan level hirarki, jalur karir, dan fungsi kerja.</p>
                        </div>
                    </div>

                    <div class="flex items-start gap-3.5 p-3.5 rounded-2xl bg-white border border-slate-200/90 shadow-xs">
                        <div class="w-8 h-8 rounded-xl bg-emerald-50 text-emerald-600 border border-emerald-200 flex items-center justify-center shrink-0 mt-0.5">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <div>
                            <h4 class="text-sm font-bold text-slate-900">Kalkulasi Otomatis Usia & Masa Kerja</h4>
                            <p class="text-xs text-slate-600 mt-0.5">Dihitung secara dinamis via Laravel Accessor tanpa field mati di database.</p>
                        </div>
                    </div>

                    <div class="flex items-start gap-3.5 p-3.5 rounded-2xl bg-white border border-slate-200/90 shadow-xs">
                        <div class="w-8 h-8 rounded-xl bg-amber-50 text-amber-600 border border-amber-200 flex items-center justify-center shrink-0 mt-0.5">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path>
                            </svg>
                        </div>
                        <div>
                            <h4 class="text-sm font-bold text-slate-900">Fondasi Payroll & Pajak PPh 21 TER</h4>
                            <p class="text-xs text-slate-600 mt-0.5">Mendukung tarif TER PPh 21, status PTKP, BPJS TK/Kes, dan batch transfer bank.</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Footer Badge -->
            <div class="mt-8 pt-6 border-t border-slate-200 flex items-center justify-between text-xs text-slate-500">
                <span class="flex items-center gap-1.5 font-medium">
                    <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                    PostgreSQL 18 &bull; Laravel Core
                </span>
                <span class="font-medium text-slate-400">Security Protected</span>
            </div>
        </div>

        <!-- Right Column: Login Form (5 cols on lg) -->
        <div class="lg:col-span-5 p-8 sm:p-12 bg-white flex flex-col justify-between">
            <div>
                <div class="mb-6">
                    <h2 class="text-2xl font-bold text-slate-900">Selamat Datang</h2>
                    <p class="text-slate-500 text-sm mt-1">Silakan masuk menggunakan kredensial akun Anda</p>
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
                    <div class="mb-5 p-3.5 bg-rose-50 border border-rose-200 text-rose-700 text-sm rounded-xl shadow-xs">
                        <ul class="list-disc list-inside space-y-1">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <!-- Demo Account Quick Autofill -->
                <div class="mb-6 p-4 rounded-2xl bg-indigo-50/70 border border-indigo-200/80 shadow-xs">
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-xs font-bold text-indigo-700 uppercase tracking-wider">Akun Demo Administrator</span>
                        <button type="button" 
                                id="btn-autofill"
                                class="text-xs font-semibold text-indigo-600 hover:text-indigo-800 underline">
                            Isi Otomatis
                        </button>
                    </div>
                    <div class="text-xs font-mono text-slate-700 space-y-0.5">
                        <div>Email: <strong class="text-slate-900">admin@hris.corp</strong></div>
                        <div>Password: <strong class="text-slate-900">password</strong></div>
                    </div>
                </div>

                <!-- Form Login -->
                <form action="{{ route('login') }}" method="POST" class="space-y-5">
                    @csrf

                    <!-- Email Input -->
                    <div>
                        <label for="email" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-2">
                            Alamat Email
                        </label>
                        <div class="relative">
                            <input type="email" 
                                   id="email" 
                                   name="email" 
                                   value="{{ old('email', 'admin@hris.corp') }}" 
                                   required 
                                   autofocus 
                                   placeholder="nama@perusahaan.com" 
                                   class="w-full pl-11 pr-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-slate-900 placeholder-slate-400 text-sm focus:outline-none focus:bg-white focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all">
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
                                   value="password"
                                   required 
                                   placeholder="••••••••" 
                                   class="w-full pl-11 pr-11 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-slate-900 placeholder-slate-400 text-sm focus:outline-none focus:bg-white focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all">
                            <svg class="w-5 h-5 text-slate-400 absolute left-3.5 top-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                            </svg>
                            <!-- Toggle Password Visibility -->
                            <button type="button" id="btn-toggle-pwd" class="absolute right-3.5 top-3 text-slate-400 hover:text-slate-600 focus:outline-none">
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
                            <input type="checkbox" name="remember" value="1" checked class="w-4 h-4 rounded bg-slate-50 border-slate-300 text-indigo-600 focus:ring-indigo-500">
                            <span>Ingat sesi masuk saya</span>
                        </label>
                    </div>

                    <!-- Submit Button -->
                    <button type="submit" 
                            class="w-full py-3 px-4 rounded-xl bg-indigo-600 hover:bg-indigo-500 text-white font-bold text-sm shadow-md shadow-indigo-600/20 transition-all focus:outline-none focus:ring-2 focus:ring-indigo-500 cursor-pointer">
                        Masuk ke Dashboard &rarr;
                    </button>
                </form>
            </div>

            <!-- Footer note -->
            <div class="mt-8 text-center text-xs text-slate-500">
                &copy; {{ date('Y') }} HRIS Core Corporation. All rights reserved.
            </div>
        </div>

    </div>

    <!-- Interactive script for demo autofill and show/hide password -->
    <script>
        document.getElementById('btn-autofill')?.addEventListener('click', function() {
            document.getElementById('email').value = 'admin@hris.corp';
            document.getElementById('password').value = 'password';
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
