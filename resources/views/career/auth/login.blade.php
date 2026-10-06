<!DOCTYPE html>
<html lang="id" class="h-full bg-slate-50 text-slate-900">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Masuk Candidate Portal - HRIS Careers</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700,800" rel="stylesheet" />

    <!-- Styles via Vite -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen font-sans antialiased bg-slate-50 text-slate-800 flex items-center justify-center p-4 sm:p-6 py-8 relative selection:bg-indigo-600 selection:text-white">
    <div class="w-full max-w-md bg-white border border-slate-200/90 rounded-3xl p-6 sm:p-8 shadow-xl relative my-auto">
        <div class="text-center mb-8">
            <a href="{{ route('career.landing') }}" class="inline-flex items-center space-x-2 mb-4 group">
                <div class="w-10 h-10 rounded-xl bg-gradient-to-tr from-indigo-600 to-cyan-500 flex items-center justify-center text-white font-black text-xl shadow-md">
                    H
                </div>
                <span class="font-extrabold text-xl text-slate-900">HRIS Careers</span>
            </a>
            <h1 class="text-2xl font-bold text-slate-900">Masuk Portal Pelamar</h1>
            <p class="text-xs text-slate-500 mt-1">Pantau status lamaran kerja dan akses ujian psikotes online.</p>
        </div>

        @if($errors->any())
            <div class="mb-6 p-4 rounded-xl bg-rose-50 border border-rose-200 text-rose-700 text-xs space-y-2">
                @foreach($errors->all() as $error)
                    <div class="flex items-start space-x-2">
                        <span>•</span>
                        <span class="font-medium">{{ $error }}</span>
                    </div>
                @endforeach
                @if (session('redirect_portal_url'))
                    <div class="pt-2 border-t border-rose-200/60">
                        <a href="{{ session('redirect_portal_url') }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-bold bg-rose-600 hover:bg-rose-700 text-white shadow-xs transition-all">
                            {{ session('redirect_portal_label') ?? 'Buka Portal Internal' }} &rarr;
                        </a>
                    </div>
                @endif
            </div>
        @endif

        <!-- Demo Account Quick Autofill for Candidate -->
        <div class="mb-5 p-3.5 rounded-2xl bg-indigo-50/70 border border-indigo-200/80 shadow-xs">
            <div class="flex items-center justify-between mb-1.5">
                <span class="text-[11px] font-bold text-indigo-800 uppercase tracking-wider">Akun Demo Pelamar</span>
                <button type="button" 
                        id="btn-autofill-candidate"
                        class="text-[11px] font-semibold text-indigo-700 hover:text-indigo-900 underline cursor-pointer">
                    Isi Otomatis
                </button>
            </div>
            <div class="text-xs font-mono text-slate-700 space-y-0.5">
                <div>Email: <strong class="text-slate-900">ulhaqjackyhaw@gmail.com</strong></div>
                <div>Password: <strong class="text-slate-900">password</strong></div>
            </div>
        </div>

        <form action="{{ route('career.login.submit') }}" method="POST" class="space-y-4">
            @csrf
            <input type="hidden" name="redirect_job" value="{{ request('job') ?? '' }}">

            <div>
                <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1.5">Alamat Email</label>
                <input
                    type="email"
                    id="candidate_email"
                    name="email"
                    value="{{ old('email') }}"
                    required
                    placeholder="nama@email.com"
                    class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-sm text-slate-900 placeholder-slate-400 focus:outline-none focus:bg-white focus:border-indigo-600 focus:ring-1 focus:ring-indigo-600"
                />
            </div>

            <div>
                <div class="flex items-center justify-between mb-1.5">
                    <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider">Kata Sandi</label>
                    <a href="#" class="text-xs text-indigo-600 hover:underline">Lupa Password?</a>
                </div>
                <input
                    type="password"
                    id="candidate_password"
                    name="password"
                    required
                    placeholder="••••••••"
                    class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-sm text-slate-900 placeholder-slate-400 focus:outline-none focus:bg-white focus:border-indigo-600 focus:ring-1 focus:ring-indigo-600"
                />
            </div>

            <button
                type="submit"
                class="w-full py-3 rounded-xl font-bold text-sm text-white bg-indigo-600 hover:bg-indigo-500 shadow-md shadow-indigo-600/20 transition-all mt-2 cursor-pointer"
            >
                Masuk ke Portal Pelamar &rarr;
            </button>
        </form>

        <div class="mt-6 pt-5 border-t border-slate-100 text-center text-xs text-slate-500">
            Belum memiliki akun pelamar?
            <a href="{{ route('career.register') }}" class="font-bold text-indigo-600 hover:underline">Daftar Akun Baru</a>
        </div>

        <!-- Role Switcher Links -->
        <div class="mt-5 pt-4 border-t border-slate-100 space-y-2">
            <p class="text-[11px] font-semibold text-slate-400 uppercase tracking-wider text-center">Staf Internal Perusahaan?</p>
            <div class="grid grid-cols-2 gap-2 text-center text-xs">
                <a href="{{ route('employee.login') }}" class="p-2.5 rounded-xl border border-slate-200 bg-slate-50 hover:bg-amber-50 hover:border-amber-200 text-slate-700 hover:text-amber-700 font-semibold transition-all">
                    Portal Karyawan (ESS) &rarr;
                </a>
                <a href="{{ route('login') }}" class="p-2.5 rounded-xl border border-slate-200 bg-slate-50 hover:bg-indigo-50 hover:border-indigo-200 text-slate-700 hover:text-indigo-700 font-semibold transition-all">
                    Administrator HR &rarr;
                </a>
            </div>
        </div>

        <div class="mt-4 text-center">
            <a href="{{ route('career.landing') }}" class="text-xs text-slate-400 hover:text-slate-600 transition-colors">
                ← Kembali ke Beranda Karir
            </a>
        </div>
    </div>

    <script>
        document.getElementById('btn-autofill-candidate')?.addEventListener('click', function() {
            document.getElementById('candidate_email').value = 'ulhaqjackyhaw@gmail.com';
            document.getElementById('candidate_password').value = 'password';
        });
    </script>
</body>
</html>
