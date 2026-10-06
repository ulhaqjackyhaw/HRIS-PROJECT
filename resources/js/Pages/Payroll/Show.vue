<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';

const props = defineProps({
    period: Object,
});

const runCalculation = () => {
    if (confirm('Jalankan kalkulasi payroll otomatis untuk semua karyawan di periode ini?')) {
        router.post(route('payroll.periods.generate', props.period.id));
    }
};

const formatRupiah = (val) => {
    return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', maximumFractionDigits: 0 }).format(val || 0);
};
</script>

<template>
    <Head :title="period.name" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                <div>
                    <h2 class="text-xl font-bold text-gray-800">{{ period.name }}</h2>
                    <p class="text-xs text-gray-500 mt-1">
                        Rentang Cut-Off: {{ period.start_date }} s/d {{ period.end_date }} • Tanggal Cair: {{ period.payout_date }}
                    </p>
                </div>
                <div class="flex items-center gap-3">
                    <span
                        :class="{
                            'bg-amber-100 text-amber-700': period.status === 'DRAFT' || period.status === 'PROCESSING',
                            'bg-emerald-100 text-emerald-700': period.status === 'APPROVED' || period.status === 'PAID'
                        }"
                        class="px-3 py-1 rounded-full text-xs font-semibold"
                    >
                        Status: {{ period.status }}
                    </span>
                    <button
                        @click="runCalculation"
                        class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-semibold rounded-xl shadow-sm transition"
                    >
                        ⚡ Hitung / Refresh Payroll
                    </button>
                </div>
            </div>
        </template>

        <div class="py-8 max-w-7xl mx-auto px-4 sm:px-6 space-y-6">
            <!-- Kartu Ringkasan Finansial -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="bg-white p-5 rounded-2xl border border-gray-100 shadow-sm">
                    <span class="text-xs text-gray-400 font-medium uppercase">Total Gaji Bruto (Gross)</span>
                    <div class="text-2xl font-bold text-gray-900 mt-1">{{ formatRupiah(period.total_gross_amount) }}</div>
                </div>
                <div class="bg-white p-5 rounded-2xl border border-gray-100 shadow-sm">
                    <span class="text-xs text-gray-400 font-medium uppercase">Total Potongan (Pajak + BPJS + Denda)</span>
                    <div class="text-2xl font-bold text-rose-600 mt-1">{{ formatRupiah(period.total_deduction_amount) }}</div>
                </div>
                <div class="bg-white p-5 rounded-2xl border border-gray-100 shadow-sm">
                    <span class="text-xs text-gray-400 font-medium uppercase">Total Pengeluaran Bersih (THP)</span>
                    <div class="text-2xl font-bold text-emerald-600 mt-1">{{ formatRupiah(period.total_net_amount) }}</div>
                </div>
            </div>

            <!-- Tabel Payslips Karyawan -->
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
                <div class="p-5 border-b border-gray-100 font-semibold text-gray-800">
                    Daftar Slip Gaji Karyawan ({{ period.payslips.length }} Karyawan)
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm divide-y divide-gray-100">
                        <thead class="bg-gray-50 text-xs font-semibold text-gray-400 uppercase">
                            <tr>
                                <th class="px-6 py-3">Karyawan</th>
                                <th class="px-6 py-3 text-center">Kehadiran</th>
                                <th class="px-6 py-3 text-right">Pendapatan Kotor</th>
                                <th class="px-6 py-3 text-right">Total Potongan</th>
                                <th class="px-6 py-3 text-right font-bold">Gaji Bersih (THP)</th>
                                <th class="px-6 py-3 text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            <tr v-for="slip in period.payslips" :key="slip.id" class="hover:bg-gray-50">
                                <td class="px-6 py-4">
                                    <div class="font-semibold text-gray-900">{{ slip.employee.full_name }}</div>
                                    <div class="text-xs text-gray-400">NIK: {{ slip.employee.nik }} • {{ slip.employee.position?.title }}</div>
                                </td>
                                <td class="px-6 py-4 text-center text-xs">
                                    <span class="text-emerald-600 font-semibold">{{ slip.present_days }}H</span> • 
                                    <span class="text-purple-600 font-semibold">{{ slip.overtime_hours }}J Lembur</span> • 
                                    <span class="text-rose-500 font-semibold">{{ slip.late_minutes }}M Telat</span>
                                </td>
                                <td class="px-6 py-4 text-right text-gray-700">{{ formatRupiah(slip.gross_salary) }}</td>
                                <td class="px-6 py-4 text-right text-rose-600">{{ formatRupiah(slip.total_deductions) }}</td>
                                <td class="px-6 py-4 text-right font-bold text-emerald-700">{{ formatRupiah(slip.net_salary) }}</td>
                                <td class="px-6 py-4 text-right">
                                    <Link
                                        :href="route('payroll.payslips.show', slip.id)"
                                        class="text-indigo-600 hover:text-indigo-900 text-xs font-semibold"
                                    >
                                        Lihat Slip &rarr;
                                    </Link>
                                </td>
                            </tr>
                            <tr v-if="period.payslips.length === 0">
                                <td colspan="6" class="py-8 text-center text-gray-400 text-sm">
                                    Data kalkulasi belum tersedia. Klik tombol "Hitung / Refresh Payroll" di atas.
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
