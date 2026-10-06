<!DOCTYPE html>
<html lang="id" class="h-full bg-slate-50 text-slate-900">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Registrasi Akun Pelamar - HRIS Careers</title>

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
            <h1 class="text-2xl font-bold text-slate-900">Daftar Akun Pelamar</h1>
            <p class="text-xs text-slate-500 mt-1">Buat profil kandidat untuk melamar lowongan kerja dan mengikuti seleksi.</p>
        </div>

        @if($errors->any())
            <div class="mb-6 p-4 rounded-xl bg-rose-50 border border-rose-200 text-rose-700 text-xs space-y-1">
                @foreach($errors->all() as $error)
                    <div class="flex items-center space-x-2">
                        <span>•</span>
                        <span>{{ $error }}</span>
                    </div>
                @endforeach
            </div>
        @endif

        <form action="{{ route('career.register.submit') }}" method="POST" class="space-y-4">
            @csrf
            <input type="hidden" name="redirect_job" value="{{ request('job') ?? '' }}">

            <div>
                <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1.5">Nama Lengkap</label>
                <input
                    type="text"
                    name="name"
                    value="{{ old('name') }}"
                    required
                    placeholder="Contoh: Budi Pratama"
                    class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-sm text-slate-900 placeholder-slate-400 focus:outline-none focus:bg-white focus:border-indigo-600 focus:ring-1 focus:ring-indigo-600"
                />
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1.5">Alamat Email</label>
                <input
                    type="email"
                    name="email"
                    value="{{ old('email') }}"
                    required
                    placeholder="nama@email.com"
                    class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-sm text-slate-900 placeholder-slate-400 focus:outline-none focus:bg-white focus:border-indigo-600 focus:ring-1 focus:ring-indigo-600"
                />
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1.5">Nomor WhatsApp Aktif</label>
                <input
                    type="tel"
                    name="phone"
                    value="{{ old('phone') }}"
                    required
                    placeholder="081234567890"
                    class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-sm text-slate-900 placeholder-slate-400 focus:outline-none focus:bg-white focus:border-indigo-600 focus:ring-1 focus:ring-indigo-600"
                />
            </div>

            <div>
                <label for="register_password" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1.5">Kata Sandi</label>
                <div class="relative">
                    <input
                        type="password"
                        id="register_password"
                        name="password"
                        required
                        placeholder="Minimal 8 karakter"
                        class="w-full pl-4 pr-11 py-3 bg-slate-50 border border-slate-200 rounded-xl text-sm text-slate-900 placeholder-slate-400 focus:outline-none focus:bg-white focus:border-indigo-600 focus:ring-1 focus:ring-indigo-600"
                    />
                    <button type="button" id="btn-toggle-register-pwd" aria-label="Tampilkan atau sembunyikan kata sandi" class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-slate-400 hover:text-slate-600 focus:outline-none cursor-pointer z-10 touch-manipulation">
                        <svg id="eye-icon-register" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                        </svg>
                        <svg id="eye-slash-icon-register" class="w-5 h-5 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l18 18"/>
                        </svg>
                    </button>
                </div>
            </div>

            <button
                type="submit"
                class="w-full py-3 rounded-xl font-bold text-sm text-white bg-indigo-600 hover:bg-indigo-500 shadow-md shadow-indigo-600/20 transition-all mt-2 cursor-pointer"
            >
                Buat Akun Pelamar
            </button>
        </form>

        <div class="mt-6 pt-6 border-t border-slate-100 text-center text-xs text-slate-500">
            Sudah memiliki akun?
            <a href="{{ route('career.login') }}" class="font-bold text-indigo-600 hover:underline">Masuk di Sini</a>
        </div>

        <div class="mt-4 text-center">
            <a href="{{ route('career.landing') }}" class="text-xs text-slate-400 hover:text-slate-600 transition-colors">
                ← Kembali ke Beranda Karir
            </a>
        </div>
    </div>

    <script>
        document.getElementById('btn-toggle-register-pwd')?.addEventListener('click', function() {
            const pwdInput = document.getElementById('register_password');
            const eyeIcon = document.getElementById('eye-icon-register');
            const eyeSlashIcon = document.getElementById('eye-slash-icon-register');
            if (pwdInput) {
                const isPassword = pwdInput.type === 'password';
                pwdInput.type = isPassword ? 'text' : 'password';
                if (eyeIcon && eyeSlashIcon) {
                    eyeIcon.classList.toggle('hidden', isPassword);
                    eyeSlashIcon.classList.toggle('hidden', !isPassword);
                }
            }
        });
    </script>
</body>
</html>
