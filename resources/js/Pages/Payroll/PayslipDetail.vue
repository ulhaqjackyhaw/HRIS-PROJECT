<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';

const props = defineProps({
    payslip: Object,
});

const formatRupiah = (val) => {
    return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', maximumFractionDigits: 0 }).format(val || 0);
};

const printSlip = () => {
    window.print();
};
</script>

<template>
    <Head :title="'Slip Gaji - ' + payslip.employee.full_name" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex justify-between items-center print:hidden">
                <h2 class="text-xl font-bold text-gray-800">Pratinjau Slip Gaji</h2>
                <button
                    @click="printSlip"
                    class="px-4 py-2 bg-gray-900 hover:bg-black text-white text-xs font-semibold rounded-xl shadow-sm transition cursor-pointer"
                >
                    🖨️ Cetak / Simpan PDF
                </button>
            </div>
        </template>

        <div class="py-8 max-w-3xl mx-auto px-4 sm:px-6 print:p-0 print:max-w-none">
            <div class="bg-white p-8 rounded-3xl border border-gray-200 shadow-sm space-y-6 print:border-none print:shadow-none print:p-0">
                <!-- Header Perusahaan -->
                <div class="flex justify-between items-start border-b border-gray-200 pb-6">
                    <div>
                        <h1 class="text-2xl font-black text-gray-900 tracking-tight">PT ENTERPRISE HRIS INDONESIA</h1>
                        <p class="text-xs text-gray-500 mt-1">SLIP GAJI RESMI (CONFIDENTIAL)</p>
                    </div>
                    <div class="text-right">
                        <span class="text-xs text-gray-400">Periode:</span>
                        <div class="text-sm font-bold text-gray-800">{{ payslip.payroll_period.name }}</div>
                        <div class="text-xs text-gray-500">Cair: {{ payslip.payroll_period.payout_date }}</div>
                    </div>
                </div>

                <!-- Informasi Pegawai -->
                <div class="grid grid-cols-2 gap-4 text-xs bg-gray-50 p-4 rounded-2xl">
                    <div>
                        <span class="text-gray-400 block">Nama Karyawan:</span>
                        <span class="font-bold text-gray-800 text-sm">{{ payslip.employee.full_name }}</span>
                        <span class="text-gray-500 block mt-1">NIK: {{ payslip.employee.nik }}</span>
                    </div>
                    <div>
                        <span class="text-gray-400 block">Jabatan & Departemen:</span>
                        <span class="font-semibold text-gray-800">{{ payslip.employee.position?.title }}</span>
                        <span class="text-gray-500 block mt-1">{{ payslip.employee.department?.name }} • PTKP: {{ payslip.employee.ptkp_status }}</span>
                    </div>
                </div>

                <!-- Breakdown Pendapatan vs Potongan -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8 text-sm">
                    <!-- Kolom Penerimaan (Earnings) -->
                    <div>
                        <h4 class="font-bold text-emerald-800 border-b border-emerald-100 pb-2 mb-3">PENERIMAAN (EARNINGS)</h4>
                        <div class="space-y-2">
                            <div
                                v-for="item in payslip.items.filter(i => i.type === 'EARNING')"
                                :key="item.id"
                                class="flex justify-between text-xs"
                            >
                                <span class="text-gray-600">{{ item.name }}</span>
                                <span class="font-medium text-gray-900">{{ formatRupiah(item.amount) }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- Kolom Pemotongan (Deductions) -->
                    <div>
                        <h4 class="font-bold text-rose-800 border-b border-rose-100 pb-2 mb-3">POTONGAN (DEDUCTIONS)</h4>
                        <div class="space-y-2">
                            <div
                                v-for="item in payslip.items.filter(i => i.type === 'DEDUCTION')"
                                :key="item.id"
                                class="flex justify-between text-xs"
                            >
                                <span class="text-gray-600">{{ item.name }}</span>
                                <span class="font-medium text-rose-600">{{ formatRupiah(item.amount) }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Ringkasan THP -->
                <div class="border-t border-dashed border-gray-200 pt-6">
                    <div class="flex justify-between items-center bg-indigo-50/60 p-4 rounded-2xl">
                        <div>
                            <span class="text-xs text-indigo-900/60 font-semibold block uppercase tracking-wider">Gaji Bersih Ditransfer (THP)</span>
                            <span class="text-xs text-gray-400">Rekening: {{ payslip.employee.bank_name || 'BCA' }} - {{ payslip.employee.bank_account_number || '-' }}</span>
                        </div>
                        <div class="text-2xl font-black text-indigo-700">
                            {{ formatRupiah(payslip.net_salary) }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
