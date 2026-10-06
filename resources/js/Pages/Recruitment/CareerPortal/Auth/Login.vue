<script setup>
import { ref } from 'vue';

const form = ref({
    email: '',
    password: '',
});
</script>

<template>
    <div class="min-h-screen bg-slate-950 flex items-center justify-center p-4">
        <div class="w-full max-w-md bg-slate-900 border border-slate-800 rounded-3xl p-8">
            <h1 class="text-2xl font-bold text-white text-center mb-2">Masuk Portal Pelamar</h1>
            <p class="text-xs text-slate-400 text-center mb-6">Akses linimasa seleksi dan ujian psikotes online.</p>

            <form class="space-y-4">
                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1">Email</label>
                    <input v-model="form.email" type="email" class="w-full px-4 py-3 bg-slate-950 border border-slate-700 rounded-xl text-sm text-white" />
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1">Password</label>
                    <input v-model="form.password" type="password" class="w-full px-4 py-3 bg-slate-950 border border-slate-700 rounded-xl text-sm text-white" />
                </div>
                <button type="submit" class="w-full py-3 bg-indigo-600 hover:bg-indigo-500 rounded-xl font-bold text-sm text-white">
                    Masuk
                </button>
            </form>
        </div>
    </div>
</template>
