<script setup>
import { useForm, Link } from '@inertiajs/vue3';
import { route } from 'ziggy-js';
import PanelLayout from '../../Components/Admin/PanelLayout.vue';
import Fields from '../../Components/Admin/Fields.vue';
const props = defineProps({ trip: Object });
const fields = {
title: { label: 'Nama trip', type: 'text', required: true }, slug: { label: 'Slug (huruf kecil dan tanda -)', type: 'text', required: true },
type: { label: 'Jenis layanan', type: 'select', options: ['open-trip', 'private-trip', 'open-po'], required: true },
destination: { label: 'Destinasi', type: 'text', required: true }, departure_date: { label: 'Tanggal keberangkatan', type: 'date', required: true },
end_date: { label: 'Tanggal selesai', type: 'date', required: true }, capacity: { label: 'Kuota peserta', type: 'number', required: true }, price: { label: 'Harga per peserta (IDR)', type: 'number', required: true },
meeting_point: { label: 'Titik kumpul', type: 'text', required: true }, image_url: { label: 'URL foto', type: 'url', required: false },
description: { label: 'Deskripsi', type: 'textarea', required: true }, itinerary: { label: 'Itinerary & fasilitas', type: 'textarea', required: true },
status: { label: 'Simpan sebagai', type: 'select', options: ['draft', 'pending'], required: true },
};
const form = useForm(Object.fromEntries(Object.entries(fields).map(([key, field]) => [key, field.type === 'date' ? props.trip?.[key]?.slice(0, 10) || '' : key === 'status' ? 'draft' : props.trip?.[key] ?? field.options?.[0] ?? (field.type === 'number' ? 1 : '')])));
const navigation = [{ key: 'trips', label: 'Trip & Jadwal', group: 'Operasional', url: route('vendor.section', 'trips') }];
const submit = () => props.trip ? form.put(route('vendor.trips.update', props.trip.id)) : form.post(route('vendor.trips.store'));
</script>
<template><PanelLayout :title="trip ? 'Kelola Trip' : 'Trip Baru'" subtitle="Satu trip mewakili satu jadwal keberangkatan. Ajukan untuk ditinjau sebelum tampil di katalog." :navigation="navigation" vendor><template #actions><Link :href="route('vendor.section', 'trips')" class="panel-secondary">Kembali</Link></template><form class="panel-surface max-w-4xl p-6" @submit.prevent="submit"><Fields :fields="fields" :form="form" /><button :disabled="form.processing" class="panel-primary mt-6">Simpan trip</button></form></PanelLayout></template>

