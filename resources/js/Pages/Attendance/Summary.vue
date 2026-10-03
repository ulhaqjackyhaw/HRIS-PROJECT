<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, router } from '@inertiajs/vue3';
import { ref } from 'vue';

const props = defineProps({
    employees: Array,
    currentMonth: String,
});

const selectedMonth = ref(props.currentMonth);

const onMonthChange = () => {
    router.get(route('attendance.summary'), { month: selectedMonth.value }, {
        preserveState: true,
        replace: true,
    });
};
</script>

<template>
    <Head title="Rekapitulasi Kehadiran" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex justify-between items-center">
                <h2 class="text-xl font-semibold text-gray-800">
                    Rekapitulasi Presensi & Lembur Karyawan
                </h2>
                <input
                    type="month"
                    v-model="selectedMonth"
                    @change="onMonthChange"
                    class="rounded-xl border-gray-300 text-sm focus:ring-emerald-500 focus:border-emerald-500"
                />
            </div>
        </template>

        <div class="py-8 max-w-7xl mx-auto px-4 sm:px-6">
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                <table class="min-w-full divide-y divide-gray-200 text-sm">
                    <thead class="bg-gray-50 text-xs font-semibold text-gray-500 uppercase">
                        <tr>
                            <th class="px-6 py-3 text-left">Karyawan</th>
                            <th class="px-6 py-3 text-center text-emerald-600">Hadir</th>
                            <th class="px-6 py-3 text-center text-amber-600">Telat</th>
                            <th class="px-6 py-3 text-center text-blue-600">Cuti/Izin</th>
                            <th class="px-6 py-3 text-center text-rose-600">Alpa</th>
                            <th class="px-6 py-3 text-center">Total Telat (Mnt)</th>
                            <th class="px-6 py-3 text-center text-purple-600">Lembur (Jam)</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        <tr v-for="emp in employees" :key="emp.id" class="hover:bg-gray-50">
                            <td class="px-6 py-4">
                                <div class="font-medium text-gray-900">{{ emp.name }}</div>
                                <div class="text-xs text-gray-400">NIK: {{ emp.nik }} • {{ emp.department }}</div>
                            </td>
                            <td class="px-6 py-4 text-center font-semibold text-emerald-700">{{ emp.present_count }}</td>
                            <td class="px-6 py-4 text-center font-semibold text-amber-700">{{ emp.late_count }}</td>
                            <td class="px-6 py-4 text-center font-semibold text-blue-700">{{ emp.leave_count }}</td>
                            <td class="px-6 py-4 text-center font-semibold text-rose-700">{{ emp.absent_count }}</td>
                            <td class="px-6 py-4 text-center text-gray-600">{{ emp.total_late_minutes }} mnt</td>
                            <td class="px-6 py-4 text-center font-semibold text-purple-700">{{ emp.total_overtime_hours }} jam</td>
                        </tr>
                        <tr v-if="employees.length === 0">
                            <td colspan="7" class="px-6 py-8 text-center text-gray-400">
                                Tidak ada data presensi pada bulan ini.
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
