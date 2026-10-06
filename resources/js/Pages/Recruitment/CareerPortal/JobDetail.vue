<script setup>
import { ref } from 'vue';

const props = defineProps({
    job: {
        type: Object,
        required: true,
    },
    relatedJobs: {
        type: Array,
        default: () => [],
    },
});
</script>

<template>
    <div class="min-h-screen bg-slate-950 text-slate-100 font-sans selection:bg-indigo-500 selection:text-white">
        <!-- Header -->
        <header class="sticky top-0 z-40 backdrop-blur-xl bg-slate-950/85 border-b border-slate-800">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-20 flex items-center justify-between">
                <a href="/career" class="flex items-center space-x-3.5">
                    <div class="w-10 h-10 rounded-xl bg-gradient-to-tr from-indigo-500 to-cyan-500 flex items-center justify-center text-white font-black text-xl shadow-lg">
                        H
                    </div>
                    <div>
                        <span class="font-extrabold tracking-tight text-lg text-white">HRIS Core</span>
                        <span class="text-xs text-slate-400 block font-medium">Careers Portal</span>
                    </div>
                </a>

                <div class="flex items-center space-x-4">
                    <a href="/career" class="text-xs font-semibold text-slate-400 hover:text-white transition-colors">
                        ← Kembali ke Lowongan
                    </a>
                    <a :href="`/career/register?job_id=${job.id}`" class="px-5 py-2.5 rounded-xl text-xs font-bold text-white bg-indigo-600 hover:bg-indigo-500 shadow-md shadow-indigo-600/30 transition-all">
                        Lamar Posisi Sekarang
                    </a>
                </div>
            </div>
        </header>

        <main class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
            <div class="bg-slate-900/80 border border-slate-800 rounded-3xl p-6 sm:p-10 mb-10 shadow-2xl">
                <div class="flex flex-wrap items-center gap-2 mb-4">
                    <span class="text-xs font-semibold px-3 py-1 rounded-lg bg-indigo-500/10 text-indigo-400 border border-indigo-500/20">
                        {{ job.department ? job.department.name : 'Umum' }}
                    </span>
                    <span class="text-xs font-semibold px-3 py-1 rounded-lg bg-cyan-500/10 text-cyan-400 border border-cyan-500/20">
                        {{ job.work_model }}
                    </span>
                    <span class="text-xs font-semibold px-3 py-1 rounded-lg bg-emerald-500/10 text-emerald-400 border border-emerald-500/20">
                        {{ job.salary_formatted || 'Kompetitif' }}
                    </span>
                </div>

                <h1 class="text-3xl sm:text-4xl font-extrabold text-white mb-3">{{ job.title }}</h1>
                <p class="text-sm text-slate-400 mb-8">{{ job.location }} • {{ job.experience_level }} • {{ job.employment_type }}</p>

                <div class="space-y-8 text-slate-300">
                    <div>
                        <h3 class="text-lg font-bold text-white mb-2">Deskripsi Posisi</h3>
                        <p class="text-sm text-slate-300 leading-relaxed whitespace-pre-line">{{ job.description }}</p>
                    </div>

                    <div v-if="job.requirements">
                        <h3 class="text-lg font-bold text-white mb-2">Kualifikasi & Persyaratan</h3>
                        <p class="text-sm text-slate-300 leading-relaxed whitespace-pre-line">{{ job.requirements }}</p>
                    </div>

                    <div v-if="job.benefits">
                        <h3 class="text-lg font-bold text-white mb-2">Benefit & Fasilitas</h3>
                        <p class="text-sm text-slate-300 leading-relaxed whitespace-pre-line">{{ job.benefits }}</p>
                    </div>
                </div>

                <div class="mt-12 pt-8 border-t border-slate-800 flex flex-col sm:flex-row items-center justify-between gap-4">
                    <div>
                        <div class="text-sm font-bold text-white">Siap mengambil langkah karir berikutnya?</div>
                        <div class="text-xs text-slate-400">Bergabunglah dan kembangkan potensi terbaik Anda bersama kami.</div>
                    </div>
                    <a :href="`/career/register?job_id=${job.id}`" class="px-8 py-3 rounded-xl text-sm font-bold text-white bg-indigo-600 hover:bg-indigo-500 shadow-lg shadow-indigo-600/30 transition-all text-center">
                        Lamar Posisi Ini Sekarang
                    </a>
                </div>
            </div>
        </main>
    </div>
</template>
