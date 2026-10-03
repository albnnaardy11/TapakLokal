<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import { route } from 'ziggy-js';
const props = defineProps({ booking: Object, trip: Object, checkInUrl: String });
const form = useForm({});
</script>
<template>
    <Head title="Verifikasi tiket" />
    <main class="min-h-screen bg-slate-50 px-4 py-10 text-[#17345e]"><section class="mx-auto max-w-xl rounded-2xl border border-slate-200 bg-white p-6">
        <Link :href="route('vendor.section', 'bookings')" class="text-xs text-blue-600">← Pemesanan vendor</Link>
        <h1 class="mt-5 text-xl font-bold">Tiket terverifikasi</h1><h2 class="mt-4 text-lg font-bold">{{ trip.title }}</h2><p class="mt-2 text-xs">{{ booking.reference }}</p>
        <p class="mt-4 text-sm">Pemesan: <strong>{{ booking.contact_name }}</strong></p><p class="mt-2 text-sm">{{ booking.participants }} peserta</p>
        <ol class="mt-4 list-inside list-decimal space-y-2 text-sm"><li v-for="(person, index) in booking.traveler_details" :key="index">{{ person.name }}</li></ol>
        <p v-if="booking.checked_in_at" class="mt-5 rounded-xl bg-emerald-50 p-4 text-sm font-bold text-emerald-700">Sudah check-in pada {{ new Date(booking.checked_in_at).toLocaleString('id-ID') }}. Jangan check-in ulang.</p>
        <form v-else class="mt-5" @submit.prevent="form.post(checkInUrl, { preserveScroll: true })"><p class="mb-3 text-xs text-slate-500">Pastikan nama dan seluruh peserta sesuai sebelum melanjutkan.</p><button :disabled="form.processing" class="panel-primary w-full">Konfirmasi check-in {{ booking.participants }} peserta</button></form>
    </section></main>
</template>
