<script setup>
import { ref } from 'vue';

const props = defineProps({
    user: Object,
    profile: {
        type: Object,
        default: () => ({})
    }
});

const activeSection = ref(1);

const form = ref({
    // I. Data Diri
    full_name: props.profile.full_name || props.user.name || '',
    nickname: props.profile.nickname || '',
    gender: props.profile.gender || '',
    marital_status: props.profile.marital_status || '',
    birth_date: props.profile.birth_date || '',
    birth_place: props.profile.birth_place || '',
    religion: props.profile.religion || '',
    nationality: props.profile.nationality || 'Indonesia',
    ethnicity: props.profile.ethnicity || '',
    height_cm: props.profile.height_cm || '',
    weight_kg: props.profile.weight_kg || '',
    clothing_size: props.profile.clothing_size || '',
    shoe_size: props.profile.shoe_size || '',
    blood_type: props.profile.blood_type || '',
    blood_rhesus: props.profile.blood_rhesus || '',
    hobbies: props.profile.hobbies || '',
    medical_history: props.profile.medical_history || '',

    // II. Identitas Diri
    ktp_number: props.profile.ktp_number || '',
    ktp_expiry: props.profile.ktp_expiry || '',
    npwp_number: props.profile.npwp_number || '',
    npwp_expiry: props.profile.npwp_expiry || '',
    passport_number: props.profile.passport_number || '',
    bpjs_tk_number: props.profile.bpjs_tk_number || '',
    family_card_number: props.profile.family_card_number || '',
    sim_a_number: props.profile.sim_a_number || '',
    sim_c_number: props.profile.sim_c_number || '',

    // III. Kontak & Alamat
    phone_wa: props.profile.phone_wa || props.user.phone || '',
    email: props.profile.email || props.user.email || '',
    ktp_address: props.profile.ktp_address || '',
    ktp_province: props.profile.ktp_province || '',
    ktp_city: props.profile.ktp_city || '',
    domicile_address: props.profile.domicile_address || '',

    // IV-VIII Data
    strengths_weaknesses: props.profile.strengths_weaknesses || '',
    proudest_achievement: props.profile.proudest_achievement || '',
    expected_salary: props.profile.expected_salary || '',
    applied_before: props.profile.applied_before || false,
    willing_to_relocate: props.profile.willing_to_relocate || false,
    estimated_start_date: props.profile.estimated_start_date || '',
    recruitment_location: props.profile.recruitment_location || '',
    agreement_signed: props.profile.agreement_signed || false,
});
</script>

<template>
    <div class="min-h-screen bg-slate-950 text-slate-100 font-sans p-6 sm:p-10">
        <div class="max-w-5xl mx-auto">
            <!-- Header -->
            <div class="flex items-center justify-between mb-8 pb-4 border-b border-slate-800">
                <div>
                    <h1 class="text-2xl font-bold text-white">Formulir Kelengkapan Data Diri & CV</h1>
                    <p class="text-xs text-slate-400 mt-1">Lengkapi seluruh 8 bagian data untuk profil pelamar resmi.</p>
                </div>
                <a href="/career/dashboard" class="text-xs font-semibold text-slate-400 hover:text-white">
                    ← Kembali ke Dashboard
                </a>
            </div>

            <!-- Section Tabs -->
            <div class="flex flex-wrap gap-2 mb-8">
                <button
                    v-for="sec in [
                        { id: 1, label: 'I. Data Diri' },
                        { id: 2, label: 'II. Identitas Diri' },
                        { id: 3, label: 'III. Kontak & Alamat' },
                        { id: 4, label: 'IV. Data Keluarga' },
                        { id: 5, label: 'V. Pendidikan' },
                        { id: 6, label: 'VI. Pengalaman Kerja' },
                        { id: 7, label: 'VII. Referensi' },
                        { id: 8, label: 'VIII. Esai & Proses' },
                    ]"
                    :key="sec.id"
                    @click="activeSection = sec.id"
                    class="px-4 py-2 rounded-xl text-xs font-bold transition-all"
                    :class="activeSection === sec.id ? 'bg-indigo-600 text-white shadow-lg shadow-indigo-600/30' : 'bg-slate-900 text-slate-400 hover:text-white border border-slate-800'"
                >
                    {{ sec.label }}
                </button>
            </div>

            <!-- Form Content Box -->
            <div class="bg-slate-900 border border-slate-800 rounded-3xl p-6 sm:p-8">
                <!-- Section 1 -->
                <div v-if="activeSection === 1" class="space-y-4">
                    <h2 class="text-lg font-bold text-white mb-4">Bagian I: Data Diri</h2>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="text-xs text-slate-300 block mb-1">Nama Lengkap*</label>
                            <input v-model="form.full_name" type="text" class="w-full px-4 py-2.5 bg-slate-950 border border-slate-700 rounded-xl text-sm text-white" />
                        </div>
                        <div>
                            <label class="text-xs text-slate-300 block mb-1">Nama Panggilan*</label>
                            <input v-model="form.nickname" type="text" class="w-full px-4 py-2.5 bg-slate-950 border border-slate-700 rounded-xl text-sm text-white" />
                        </div>
                        <div>
                            <label class="text-xs text-slate-300 block mb-1">Jenis Kelamin*</label>
                            <select v-model="form.gender" class="w-full px-4 py-2.5 bg-slate-950 border border-slate-700 rounded-xl text-sm text-white">
                                <option value="Laki-laki">Laki-laki</option>
                                <option value="Perempuan">Perempuan</option>
                            </select>
                        </div>
                        <div>
                            <label class="text-xs text-slate-300 block mb-1">Status Pernikahan*</label>
                            <input v-model="form.marital_status" type="text" class="w-full px-4 py-2.5 bg-slate-950 border border-slate-700 rounded-xl text-sm text-white" />
                        </div>
                        <div>
                            <label class="text-xs text-slate-300 block mb-1">Tinggi (cm)*</label>
                            <input v-model="form.height_cm" type="number" class="w-full px-4 py-2.5 bg-slate-950 border border-slate-700 rounded-xl text-sm text-white" />
                        </div>
                        <div>
                            <label class="text-xs text-slate-300 block mb-1">Berat (kg)*</label>
                            <input v-model="form.weight_kg" type="number" class="w-full px-4 py-2.5 bg-slate-950 border border-slate-700 rounded-xl text-sm text-white" />
                        </div>
                    </div>
                </div>

                <!-- Section Navigation -->
                <div class="mt-8 pt-6 border-t border-slate-800 flex justify-between">
                    <button
                        v-if="activeSection > 1"
                        @click="activeSection--"
                        class="px-5 py-2.5 rounded-xl text-xs font-bold text-slate-400 bg-slate-800 hover:text-white"
                    >
                        ← Bagian Sebelumnya
                    </button>
                    <div v-else></div>

                    <button
                        v-if="activeSection < 8"
                        @click="activeSection++"
                        class="px-5 py-2.5 rounded-xl text-xs font-bold text-white bg-indigo-600 hover:bg-indigo-500 shadow-md"
                    >
                        Lanjut ke Bagian Berikutnya →
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>
