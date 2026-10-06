<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link } from '@inertiajs/vue3';

const props = defineProps({
    periods: Object,
});

const formatRupiah = (val) => {
    return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', maximumFractionDigits: 0 }).format(val || 0);
};
</script>

<template>
    <Head title="Periode Payroll" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex justify-between items-center">
                <h2 class="text-xl font-bold text-gray-800">Daftar Periode Payroll</h2>
            </div>
        </template>

        <div class="py-8 max-w-7xl mx-auto px-4 sm:px-6 space-y-6">
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
                <table class="w-full text-left text-sm divide-y divide-gray-100">
                    <thead class="bg-gray-50 text-xs font-semibold text-gray-400 uppercase">
                        <tr>
                            <th class="px-6 py-3">Nama Periode</th>
                            <th class="px-6 py-3">Rentang Cut-Off</th>
                            <th class="px-6 py-3">Tanggal Cair</th>
                            <th class="px-6 py-3 text-center">Status</th>
                            <th class="px-6 py-3 text-right">Total Netto</th>
                            <th class="px-6 py-3 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        <tr v-for="period in periods.data" :key="period.id" class="hover:bg-gray-50">
                            <td class="px-6 py-4 font-semibold text-gray-900">{{ period.name }}</td>
                            <td class="px-6 py-4 text-xs">{{ period.start_date }} s/d {{ period.end_date }}</td>
                            <td class="px-6 py-4 text-xs">{{ period.payout_date }}</td>
                            <td class="px-6 py-4 text-center">
                                <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-700">
                                    {{ period.status }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-right font-bold text-emerald-700">{{ formatRupiah(period.total_net_amount) }}</td>
                            <td class="px-6 py-4 text-right">
                                <Link :href="route('payroll.periods.show', period.id)" class="text-indigo-600 hover:text-indigo-900 text-xs font-semibold">
                                    Buka Matriks &rarr;
                                </Link>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
