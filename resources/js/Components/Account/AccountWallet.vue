<script setup>
import { computed, watch } from 'vue';
import { useForm, usePage } from '@inertiajs/vue3';
import { route } from 'ziggy-js';
const page = usePage();
const methods = computed(() => page.props.paymentMethods || []);
const preferences = useForm({ saved: [...(page.props.paymentPreferences?.saved || [])], primary: page.props.paymentPreferences?.primary || null });
watch(() => page.props.paymentPreferences, value => { preferences.saved = [...(value?.saved || [])]; preferences.primary = value?.primary || null; });
const toggle = id => { preferences.saved = preferences.saved.includes(id) ? preferences.saved.filter(value => value !== id) : [...preferences.saved, id]; if (!preferences.saved.includes(preferences.primary)) preferences.primary = preferences.saved[0] || null; };
</script>
<template>
    <form class="mb-6 rounded-2xl border border-slate-200 bg-white p-5 sm:p-6" @submit.prevent="preferences.put(route('account.payment-methods'), { preserveScroll: true })">
        <h3 class="text-base font-bold text-[#183660]">Metode pembayaranmu</h3>
        <p class="mt-2 text-xs leading-6 text-slate-500">Simpan pilihan yang sering kamu gunakan. Metode utama dipilih otomatis saat checkout; pembayaran tetap dilakukan untuk setiap pesanan.</p>
        <div class="mt-5 divide-y divide-slate-100">
            <div v-for="method in methods" :key="method.id" class="flex flex-wrap items-center gap-3 py-4" :class="!method.enabled ? 'opacity-50' : ''">
                <input :id="`save-${method.id}`" type="checkbox" :checked="preferences.saved.includes(method.id)" :disabled="!method.enabled || preferences.processing" :aria-label="`Simpan ${method.name}`" class="size-4 accent-blue-600" @change="toggle(method.id)" />
                <label :for="`save-${method.id}`" class="min-w-0 flex-1"><span class="block text-sm font-bold">{{ method.name }}</span><span class="mt-1 block text-xs text-slate-500">{{ method.description }}</span></label>
                <img v-if="method.logo" :src="method.logo" :alt="method.name" class="h-6 w-16 object-contain" loading="lazy" />
                <label v-if="preferences.saved.includes(method.id)" class="flex items-center gap-2 text-xs font-semibold text-blue-700"><input v-model="preferences.primary" type="radio" name="primary-payment" :value="method.id" :disabled="preferences.processing" class="accent-blue-600" />Utama</label>
                <span v-if="!method.enabled" class="text-xs">Belum tersedia</span>
            </div>
        </div>
        <p v-for="error in preferences.errors" :key="error" role="alert" class="mt-3 text-xs text-red-700">{{ error }}</p>
        <button :disabled="preferences.processing" class="panel-primary mt-5 disabled:opacity-50">{{ preferences.processing ? 'Menyimpan…' : 'Simpan pilihan pembayaran' }}</button>
        <p class="mt-4 text-xs leading-5 text-slate-500">Pilihan ini tersimpan di akunmu. Data kartu dan saldo dompet digital tidak disimpan oleh TapakLokal.</p>
    </form>
</template>
