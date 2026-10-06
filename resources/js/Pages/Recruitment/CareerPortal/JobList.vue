<script setup>
import { ref } from 'vue';

const props = defineProps({
    jobs: {
        type: Array,
        default: () => [],
    },
    departments: {
        type: Array,
        default: () => [],
    },
});
</script>

<template>
    <div class="min-h-screen bg-slate-950 text-slate-100 p-6">
        <h1 class="text-2xl font-bold mb-4">Daftar Lowongan Kerja</h1>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <div v-for="job in jobs" :key="job.id" class="bg-slate-900 border border-slate-800 rounded-2xl p-5">
                <h3 class="font-bold text-lg text-white mb-2">{{ job.title }}</h3>
                <p class="text-sm text-slate-400 mb-4">{{ job.description }}</p>
                <a :href="`/career/jobs/${job.slug}`" class="text-xs font-semibold text-indigo-400 hover:underline">Lihat Detail →</a>
            </div>
        </div>
    </div>
</template>
