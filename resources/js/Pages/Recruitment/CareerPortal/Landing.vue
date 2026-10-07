<script setup>
import { ref, computed } from 'vue';

const props = defineProps({
    jobs: {
        type: Array,
        default: () => []
    },
    departments: {
        type: Array,
        default: () => []
    },
    stats: {
        type: Object,
        default: () => ({
            total_openings: 0,
            total_departments: 0,
            remote_friendly_count: 0,
            satisfaction_rate: 98
        })
    }
});

// Reactive State
const searchQuery = ref('');
const selectedDepartment = ref('ALL');
const selectedWorkModel = ref('ALL');
const selectedJobForModal = ref(null);
const isModalOpen = ref(false);

// Filtered Jobs
const filteredJobs = computed(() => {
    return props.jobs.filter(job => {
        const matchesQuery = !searchQuery.value ||
            job.title.toLowerCase().includes(searchQuery.value.toLowerCase()) ||
            job.location.toLowerCase().includes(searchQuery.value.toLowerCase()) ||
            (job.department && job.department.name.toLowerCase().includes(searchQuery.value.toLowerCase()));

        const matchesDept = selectedDepartment.value === 'ALL' ||
            (job.department_id && job.department_id.toString() === selectedDepartment.value.toString());

        const matchesModel = selectedWorkModel.value === 'ALL' ||
            job.work_model === selectedWorkModel.value;

        return matchesQuery && matchesDept && matchesModel;
    });
});

const openQuickView = (job) => {
    selectedJobForModal.value = job;
    isModalOpen.value = true;
};

const closeModal = () => {
    isModalOpen.value = false;
    selectedJobForModal.value = null;
};

const pipelineStages = [
    { step: 1, title: 'Applied', desc: 'Screening CV & Portofolio otomatis oleh AI & Tim HR', badge: 'Tahap 1' },
    { step: 2, title: 'Psychotest Online', desc: 'Ujian logika penalaran, kepribadian & analisis kasus di portal', badge: 'Tahap 2' },
    { step: 3, title: 'Interview HR', desc: 'Eksplorasi motivasi, background, dan cultural fit', badge: 'Tahap 3' },
    { step: 4, title: 'Interview User', desc: 'Deep-dive teknis bersama calon atasan & tim', badge: 'Tahap 4' },
    { step: 5, title: 'Interview BOD', desc: 'Diskusi visi strategis dengan jajaran Direksi', badge: 'Tahap 5' },
    { step: 6, title: 'Medical Check-Up', desc: 'Pemeriksaan kesehatan di klinik lab rekanan', badge: 'Tahap 6' },
    { step: 7, title: 'Offering & Onboarding', desc: 'Penerbitan surat penawaran & auto-convert ke Core HR', badge: 'Tahap Final' },
];

const perks = [
    { title: 'Asuransi Kesehatan Kelas 1', desc: 'Rawat jalan & rawat inap untuk karyawan serta keluarga inti tercover.', icon: 'shield' },
    { title: 'Fleksibilitas Kerja', desc: 'Kebijakan kerja Hybrid & Remote dengan output-based performance mindset.', icon: 'clock' },
    { title: 'Annual Learning Budget', desc: 'Alokasi dana tahunan untuk kursus, buku, bootcamp, & sertifikasi profesional.', icon: 'book' },
    { title: 'Modern Workstation', desc: 'Perangkat kerja premium (MacBook Pro / ThinkPad) & setup monitor rumah.', icon: 'laptop' },
    { title: 'Wellness & Mental Health', desc: 'Sesi konseling berkala & keanggotaan gym / fitness rekanan.', icon: 'heart' },
    { title: 'Bonus & Profit Sharing', desc: 'Bonus performa semesteran & pembagian hasil performa korporat.', icon: 'sparkle' },
];
</script>

<template>
    <div class="min-h-screen bg-slate-950 text-slate-100 font-sans selection:bg-indigo-500 selection:text-white antialiased">
        <!-- Background Ambient Glow -->
        <div class="fixed inset-0 overflow-hidden pointer-events-none -z-10">
            <div class="absolute -top-40 left-1/2 -translate-x-1/2 w-[800px] h-[500px] bg-gradient-to-tr from-indigo-600/20 via-cyan-500/15 to-purple-600/20 blur-3xl rounded-full"></div>
            <div class="absolute top-[600px] -left-40 w-[600px] h-[600px] bg-blue-600/10 blur-3xl rounded-full"></div>
            <div class="absolute top-[1200px] -right-40 w-[600px] h-[600px] bg-violet-600/10 blur-3xl rounded-full"></div>
        </div>

        <!-- Sticky Navigation Header -->
        <header class="sticky top-0 z-40 backdrop-blur-xl bg-slate-950/80 border-b border-slate-800/80">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-20 flex items-center justify-between">
                <a href="/career" class="flex items-center space-x-3.5 group">
                    <div class="w-10 h-10 rounded-xl bg-gradient-to-tr from-indigo-500 via-indigo-600 to-cyan-500 flex items-center justify-center text-white font-black text-xl shadow-lg shadow-indigo-500/25 group-hover:scale-105 transition-transform">
                        H
                    </div>
                    <div>
                        <div class="flex items-center space-x-2">
                            <span class="font-extrabold tracking-tight text-lg text-white">HRIS Core</span>
                            <span class="text-xs font-semibold px-2 py-0.5 rounded-full bg-indigo-500/20 text-indigo-400 border border-indigo-500/30">Careers</span>
                        </div>
                        <span class="text-[11px] text-slate-400 font-medium tracking-wide">Enterprise Talent Acquisition</span>
                    </div>
                </a>

                <!-- Desktop Nav Links -->
                <nav class="hidden md:flex items-center space-x-8 text-sm font-medium text-slate-300">
                    <a href="#lowongan" class="hover:text-indigo-400 transition-colors">Lowongan Tersedia</a>
                    <a href="#budaya" class="hover:text-indigo-400 transition-colors">Budaya & Benefit</a>
                    <a href="#alur" class="hover:text-indigo-400 transition-colors">Tahapan Seleksi</a>
                    <a href="#faq" class="hover:text-indigo-400 transition-colors">FAQ</a>
                </nav>

                <!-- Auth Buttons -->
                <div class="flex items-center space-x-3">
                    <a href="/career/login" class="px-4 py-2 text-sm font-medium text-slate-300 hover:text-white transition-colors">
                        Masuk Pelamar
                    </a>
                    <a href="/career/register" class="px-5 py-2.5 rounded-xl text-sm font-semibold text-white bg-gradient-to-r from-indigo-600 via-indigo-500 to-cyan-500 hover:from-indigo-500 hover:to-cyan-400 shadow-md shadow-indigo-600/30 hover:shadow-indigo-500/40 transition-all">
                        Daftar Akun
                    </a>
                </div>
            </div>
        </header>

        <!-- Hero Section -->
        <section class="relative pt-20 pb-16 lg:pt-28 lg:pb-24 overflow-hidden text-center">
            <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
                <!-- Badge Pill -->
                <div class="inline-flex items-center space-x-2 px-4 py-1.5 rounded-full bg-indigo-500/10 border border-indigo-500/30 text-indigo-300 text-xs font-semibold uppercase tracking-wider mb-8">
                    <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                    <span>Kami Sedang Merekrut Talenta Terbaik</span>
                </div>

                <h1 class="text-4xl sm:text-6xl lg:text-7xl font-extrabold tracking-tight text-white leading-tight mb-6">
                    Bangun Masa Depan <br class="hidden sm:inline" />
                    <span class="bg-gradient-to-r from-indigo-400 via-cyan-300 to-purple-400 bg-clip-text text-transparent">
                        Bersama Tim Berdampak Tinggi
                    </span>
                </h1>

                <p class="text-lg sm:text-xl text-slate-300 max-w-3xl mx-auto mb-10 leading-relaxed font-normal">
                    Bergabunglah bersama pionir teknologi enterprise. Wujudkan inovasi sistem kepegawaian modern dengan lingkungan kerja yang inklusif, fleksibel, dan didukung apresiasi nyata.
                </p>

                <!-- Live Search Box Card -->
                <div class="max-w-4xl mx-auto bg-slate-900/90 backdrop-blur-2xl p-4 sm:p-5 rounded-2xl border border-slate-800 shadow-2xl shadow-indigo-950/50">
                    <div class="grid grid-cols-1 md:grid-cols-12 gap-3">
                        <!-- Keyword Search -->
                        <div class="md:col-span-5 relative">
                            <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                            </span>
                            <input
                                v-model="searchQuery"
                                type="text"
                                placeholder="Cari posisi (misal: Backend, Recruiter...)"
                                class="w-full pl-11 pr-4 py-3 bg-slate-950 border border-slate-700/80 rounded-xl text-sm text-white placeholder-slate-400 focus:outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500"
                            />
                        </div>

                        <!-- Department Filter -->
                        <div class="md:col-span-4 relative">
                            <select
                                v-model="selectedDepartment"
                                class="w-full px-4 py-3 bg-slate-950 border border-slate-700/80 rounded-xl text-sm text-white focus:outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500"
                            >
                                <option value="ALL">Semua Departemen</option>
                                <option v-for="dept in departments" :key="dept.id" :value="dept.id">
                                    {{ dept.name }} ({{ dept.job_postings_count || 0 }})
                                </option>
                            </select>
                        </div>

                        <!-- Work Model Filter -->
                        <div class="md:col-span-3">
                            <select
                                v-model="selectedWorkModel"
                                class="w-full px-4 py-3 bg-slate-950 border border-slate-700/80 rounded-xl text-sm text-white focus:outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500"
                            >
                                <option value="ALL">Semua Tipe Kerja</option>
                                <option value="REMOTE">Remote (WFH)</option>
                                <option value="HYBRID">Hybrid</option>
                                <option value="ON_SITE">On-site (WFO)</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Stats Counters -->
                <div class="grid grid-cols-2 md:grid-cols-4 gap-6 max-w-4xl mx-auto mt-12 pt-8 border-t border-slate-800/80 text-center">
                    <div>
                        <div class="text-3xl font-extrabold text-white tracking-tight">{{ stats.total_openings || 0 }}</div>
                        <div class="text-xs text-slate-400 uppercase tracking-wider mt-1 font-medium">Posisi Terbuka</div>
                    </div>
                    <div>
                        <div class="text-3xl font-extrabold text-indigo-400 tracking-tight">{{ stats.total_departments || 0 }}</div>
                        <div class="text-xs text-slate-400 uppercase tracking-wider mt-1 font-medium">Departemen Hiring</div>
                    </div>
                    <div>
                        <div class="text-3xl font-extrabold text-cyan-400 tracking-tight">{{ stats.remote_friendly_count || 0 }}</div>
                        <div class="text-xs text-slate-400 uppercase tracking-wider mt-1 font-medium">Remote / Hybrid Roles</div>
                    </div>
                    <div>
                        <div class="text-3xl font-extrabold text-emerald-400 tracking-tight">{{ stats.satisfaction_rate }}%</div>
                        <div class="text-xs text-slate-400 uppercase tracking-wider mt-1 font-medium">Employee Happiness Index</div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Vacancy List Section -->
        <section id="lowongan" class="py-16 bg-slate-950/60 border-t border-slate-900">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex flex-col md:flex-row md:items-end justify-between mb-10">
                    <div>
                        <span class="text-xs font-bold text-indigo-400 uppercase tracking-widest">Eksplorasi Karir</span>
                        <h2 class="text-3xl font-bold text-white mt-1">Daftar Lowongan Kerja Terbaru</h2>
                    </div>
                    <div class="mt-4 md:mt-0 text-sm text-slate-400">
                        Menampilkan <span class="font-bold text-white">{{ filteredJobs.length }}</span> posisi aktif
                    </div>
                </div>

                <!-- Job Grid -->
                <div v-if="filteredJobs.length > 0" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    <div
                        v-for="job in filteredJobs"
                        :key="job.id"
                        class="bg-slate-900/70 border border-slate-800 hover:border-indigo-500/50 rounded-2xl p-6 transition-all duration-300 hover:shadow-xl hover:shadow-indigo-950/40 hover:-translate-y-1 flex flex-col justify-between group"
                    >
                        <div>
                            <!-- Header Tags -->
                            <div class="flex items-center justify-between mb-4">
                                <span class="text-xs font-semibold px-2.5 py-1 rounded-lg bg-indigo-500/10 text-indigo-400 border border-indigo-500/20">
                                    {{ job.department ? job.department.name : 'Umum' }}
                                </span>
                                <span class="text-xs font-medium px-2 py-0.5 rounded-full"
                                    :class="{
                                        'bg-emerald-500/10 text-emerald-400 border border-emerald-500/20': job.work_model === 'REMOTE',
                                        'bg-cyan-500/10 text-cyan-400 border border-cyan-500/20': job.work_model === 'HYBRID',
                                        'bg-amber-500/10 text-amber-400 border border-amber-500/20': job.work_model === 'ON_SITE',
                                    }">
                                    {{ job.work_model === 'REMOTE' ? 'Remote' : (job.work_model === 'HYBRID' ? 'Hybrid' : 'On-site') }}
                                </span>
                            </div>

                            <h3 class="text-xl font-bold text-white group-hover:text-indigo-300 transition-colors line-clamp-1 mb-2">
                                {{ job.title }}
                            </h3>

                            <p class="text-sm text-slate-400 line-clamp-2 mb-4">
                                {{ job.description }}
                            </p>

                            <!-- Key Specs -->
                            <div class="space-y-1.5 text-xs text-slate-300 mb-6">
                                <div class="flex items-center space-x-2">
                                    <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/></svg>
                                    <span>{{ job.location }}</span>
                                </div>
                                <div class="flex items-center space-x-2">
                                    <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                                    <span>{{ job.experience_level }} • {{ job.employment_type }}</span>
                                </div>
                            </div>
                        </div>

                        <!-- Footer CTA & Salary -->
                        <div class="pt-4 border-t border-slate-800/80 flex items-center justify-between">
                            <div>
                                <span class="text-[10px] text-slate-500 uppercase tracking-wider block">Kompensasi</span>
                                <span class="text-xs font-semibold text-emerald-400">
                                    {{ job.is_salary_visible && job.min_salary ? 'Rp ' + Number(job.min_salary).toLocaleString('id-ID') : 'Kompetitif' }}
                                </span>
                            </div>

                            <div class="flex items-center space-x-2">
                                <button
                                    @click="openQuickView(job)"
                                    class="px-3 py-1.5 rounded-lg text-xs font-medium text-slate-300 hover:text-white bg-slate-800 hover:bg-slate-700 transition-colors"
                                >
                                    Detail
                                </button>
                                <a
                                    :href="`/career/jobs/${job.slug}`"
                                    class="px-3.5 py-1.5 rounded-lg text-xs font-semibold text-white bg-indigo-600 hover:bg-indigo-500 shadow-md shadow-indigo-600/30 transition-all"
                                >
                                    Lamar
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Empty State -->
                <div v-else class="text-center py-16 bg-slate-900/40 border border-dashed border-slate-800 rounded-2xl">
                    <div class="w-16 h-16 mx-auto mb-4 rounded-full bg-slate-800/80 flex items-center justify-center text-slate-400">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                    <h3 class="text-lg font-bold text-white mb-1">Tidak Ada Lowongan yang Cocok</h3>
                    <p class="text-sm text-slate-400 max-w-md mx-auto mb-4">Coba sesuaikan kata kunci pencarian atau reset filter departemen untuk melihat posisi lain.</p>
                    <button
                        @click="searchQuery = ''; selectedDepartment = 'ALL'; selectedWorkModel = 'ALL'"
                        class="px-4 py-2 rounded-xl text-xs font-semibold text-indigo-300 bg-indigo-500/10 border border-indigo-500/30 hover:bg-indigo-500/20 transition-all"
                    >
                        Reset Filter Pencarian
                    </button>
                </div>
            </div>
        </section>

        <!-- The 8-Stage Selection Pipeline Section -->
        <section id="alur" class="py-20 bg-slate-950 border-t border-slate-900 relative">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center max-w-3xl mx-auto mb-16">
                    <span class="text-xs font-bold text-indigo-400 uppercase tracking-widest">Transparansi Seleksi</span>
                    <h2 class="text-3xl sm:text-4xl font-extrabold text-white mt-1">
                        Peta 8 Tahapan Seleksi Kandidat
                    </h2>
                    <p class="text-slate-400 text-sm mt-3">
                        Setiap langkah dirancang adil, terstruktur, dan transparan. Anda dapat memantau linimasa status lamaran secara real-time di Candidate Dashboard.
                    </p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                    <div
                        v-for="stage in pipelineStages"
                        :key="stage.step"
                        class="p-5 rounded-2xl bg-slate-900/60 border border-slate-800 relative group hover:border-indigo-500/40 transition-all"
                    >
                        <div class="flex items-center justify-between mb-3">
                            <span class="w-8 h-8 rounded-lg bg-indigo-600/20 text-indigo-400 border border-indigo-500/30 flex items-center justify-center font-bold text-sm">
                                0{{ stage.step }}
                            </span>
                            <span class="text-[11px] font-semibold text-slate-400">{{ stage.badge }}</span>
                        </div>
                        <h4 class="text-base font-bold text-white mb-2">{{ stage.title }}</h4>
                        <p class="text-xs text-slate-400 leading-relaxed">{{ stage.desc }}</p>
                    </div>

                    <!-- Final Node: Converted to Employee -->
                    <div class="p-5 rounded-2xl bg-gradient-to-br from-indigo-900/40 via-cyan-900/30 to-slate-900/60 border border-cyan-500/40 relative">
                        <div class="flex items-center justify-between mb-3">
                            <span class="w-8 h-8 rounded-lg bg-emerald-500/20 text-emerald-300 border border-emerald-500/40 flex items-center justify-center font-bold text-sm">
                                ✓
                            </span>
                            <span class="text-[11px] font-semibold text-emerald-400">Core HR Engine</span>
                        </div>
                        <h4 class="text-base font-bold text-white mb-2">Auto-Onboarding Pegawai</h4>
                        <p class="text-xs text-slate-300 leading-relaxed">
                            Generate NIK otomatis, pembuatan akun portal internal, dan pencatatan masa kerja resmi di Core HR.
                        </p>
                    </div>
                </div>
            </div>
        </section>

        <!-- Company Culture & Perks Section -->
        <section id="budaya" class="py-20 bg-slate-900/40 border-t border-slate-900">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center max-w-3xl mx-auto mb-16">
                    <span class="text-xs font-bold text-indigo-400 uppercase tracking-widest">Keuntungan & Fasilitas</span>
                    <h2 class="text-3xl sm:text-4xl font-extrabold text-white mt-1">Mengapa Berkarier Bersama Kami?</h2>
                    <p class="text-slate-400 text-sm mt-3">Kami berinvestasi pada kesejahteraan, pertumbuhan karir, dan kenyamanan tim kami.</p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    <div
                        v-for="(perk, i) in perks"
                        :key="i"
                        class="p-6 rounded-2xl bg-slate-900/80 border border-slate-800 hover:border-slate-700 transition-colors"
                    >
                        <div class="w-12 h-12 rounded-xl bg-indigo-600/10 border border-indigo-500/20 flex items-center justify-center text-indigo-400 mb-4 font-bold">
                            ★
                        </div>
                        <h3 class="text-lg font-bold text-white mb-2">{{ perk.title }}</h3>
                        <p class="text-sm text-slate-400 leading-relaxed">{{ perk.desc }}</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- Talent Pool CTA Banner -->
        <section class="py-16 bg-gradient-to-r from-indigo-950 via-slate-900 to-indigo-950 border-t border-b border-indigo-900/40">
            <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
                <h3 class="text-2xl sm:text-3xl font-extrabold text-white mb-3">
                    Tidak Menemukan Posisi yang Tepat Hari Ini?
                </h3>
                <p class="text-slate-300 text-sm sm:text-base max-w-2xl mx-auto mb-6">
                    Daftarkan akun kandidat Anda sekarang dan lengkapi CV profil. Tim Talent Acquisition kami akan segera menghubungi Anda ketika posisi yang relevan terbuka.
                </p>
                <div class="flex flex-col sm:flex-row items-center justify-center space-y-3 sm:space-y-0 sm:space-x-4">
                    <a
                        href="/career/register"
                        class="w-full sm:w-auto px-8 py-3.5 rounded-xl font-bold text-sm text-white bg-gradient-to-r from-indigo-500 to-cyan-500 hover:from-indigo-400 hover:to-cyan-400 shadow-lg shadow-indigo-600/30 transition-all"
                    >
                        Daftar ke Talent Pool
                    </a>
                    <a
                        href="/career/login"
                        class="w-full sm:w-auto px-8 py-3.5 rounded-xl font-bold text-sm text-slate-300 bg-slate-800/80 hover:bg-slate-700 hover:text-white transition-all"
                    >
                        Login Pelamar
                    </a>
                </div>
            </div>
        </section>

        <!-- Footer -->
        <footer class="py-12 bg-slate-950 border-t border-slate-900 text-xs text-slate-500">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex flex-col md:flex-row items-center justify-between gap-4">
                <div class="flex items-center space-x-3">
                    <span class="font-bold text-slate-400">HRIS Core Talent Acquisition</span>
                    <span>•</span>
                    <span>Hak Cipta Dilindungi Undang-Undang</span>
                </div>
                <div class="flex items-center space-x-6 text-slate-400">
                    <a href="/career" class="hover:text-white">Karir</a>
                    <a href="/portal" class="hover:text-white">Portal HR Internal</a>
                    <a href="#faq" class="hover:text-white">Bantuan</a>
                </div>
            </div>
        </footer>

        <!-- Quick View Modal Drawer -->
        <div v-if="isModalOpen && selectedJobForModal" class="fixed inset-0 z-50 overflow-y-auto bg-black/70 backdrop-blur-sm flex items-center justify-center p-4">
            <div class="bg-slate-900 border border-slate-800 rounded-3xl max-w-2xl w-full p-6 sm:p-8 relative shadow-2xl">
                <button @click="closeModal" class="absolute top-5 right-5 text-slate-400 hover:text-white p-2">
                    ✕
                </button>

                <div class="flex items-center space-x-2 mb-3">
                    <span class="text-xs font-semibold px-2.5 py-1 rounded-lg bg-indigo-500/10 text-indigo-400 border border-indigo-500/20">
                        {{ selectedJobForModal.department ? selectedJobForModal.department.name : 'Umum' }}
                    </span>
                    <span class="text-xs font-semibold px-2.5 py-1 rounded-lg bg-cyan-500/10 text-cyan-400 border border-cyan-500/20">
                        {{ selectedJobForModal.work_model }}
                    </span>
                </div>

                <h3 class="text-2xl font-bold text-white mb-2">{{ selectedJobForModal.title }}</h3>
                <p class="text-xs text-slate-400 mb-6">{{ selectedJobForModal.location }} • {{ selectedJobForModal.experience_level }}</p>

                <div class="space-y-4 max-h-96 overflow-y-auto pr-2 text-sm text-slate-300 mb-6">
                    <div>
                        <h4 class="font-bold text-white mb-1">Deskripsi Posisi</h4>
                        <p class="text-xs text-slate-400 leading-relaxed whitespace-pre-line">{{ selectedJobForModal.description }}</p>
                    </div>

                    <div v-if="selectedJobForModal.requirements">
                        <h4 class="font-bold text-white mb-1">Kualifikasi & Persyaratan</h4>
                        <p class="text-xs text-slate-400 leading-relaxed whitespace-pre-line">{{ selectedJobForModal.requirements }}</p>
                    </div>

                    <div v-if="selectedJobForModal.benefits">
                        <h4 class="font-bold text-white mb-1">Benefit & Fasilitas</h4>
                        <p class="text-xs text-slate-400 leading-relaxed whitespace-pre-line">{{ selectedJobForModal.benefits }}</p>
                    </div>
                </div>

                <div class="pt-4 border-t border-slate-800 flex items-center justify-between">
                    <button @click="closeModal" class="px-4 py-2 rounded-xl text-xs font-semibold text-slate-400 hover:text-white">
                        Tutup
                    </button>
                    <a
                        :href="`/career/jobs/${selectedJobForModal.slug}`"
                        class="px-6 py-2.5 rounded-xl text-xs font-bold text-white bg-indigo-600 hover:bg-indigo-500 shadow-md shadow-indigo-600/30 transition-all"
                    >
                        Lanjutkan Melamar Sekarang →
                    </a>
                </div>
            </div>
        </div>
    </div>
</template>
