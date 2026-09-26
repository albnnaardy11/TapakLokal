<script setup>
import { useForm, Link } from '@inertiajs/vue3';
import { ref } from 'vue';
import { route } from 'ziggy-js';
import PanelLayout from '../../Components/Admin/PanelLayout.vue';
import Fields from '../../Components/Admin/Fields.vue';
const props = defineProps({ trip: Object });
const deleting = ref(false);
const deletion = useForm({});
const fields = {
title: { label: 'Nama trip', type: 'text', required: true }, slug: { label: 'Slug (huruf kecil dan tanda -)', type: 'text', required: true },
type: { label: 'Jenis layanan', type: 'select', options: ['open-trip', 'private-trip', 'open-po'], required: true },
destination: { label: 'Destinasi', type: 'text', required: true }, departure_date: { label: 'Tanggal keberangkatan', type: 'date', required: true },
end_date: { label: 'Tanggal selesai', type: 'date', required: true }, capacity: { label: 'Kuota peserta', type: 'number', required: true }, price: { label: 'Harga dasar vendor per peserta (IDR)', type: 'number', required: true },
meeting_point: { label: 'Titik kumpul', type: 'text', required: true }, image_url: { label: 'URL foto', type: 'url', required: false },
description: { label: 'Deskripsi', type: 'textarea', required: true }, itinerary: { label: 'Itinerary & fasilitas', type: 'textarea', required: true },
experience: { label: 'Galeri & detail perjalanan', type: 'structured', required: false },
status: { label: 'Simpan sebagai', type: 'select', options: ['draft', 'pending'], required: true },
};
const form = useForm(Object.fromEntries(Object.entries(fields).map(([key, field]) => [key, field.type === 'date' ? props.trip?.[key]?.slice(0, 10) || '' : key === 'status' ? 'draft' : props.trip?.[key] ?? field.options?.[0] ?? (field.type === 'number' ? 1 : '')])));
const navigation = [{ key: 'trips', label: 'Trip & Jadwal', group: 'Operasional', url: route('vendor.section', 'trips') }];
const submit = () => props.trip ? form.put(route('vendor.trips.update', props.trip.id)) : form.post(route('vendor.trips.store'));
</script>
<template><PanelLayout :title="trip ? 'Kelola Trip' : 'Trip Baru'" subtitle="Satu trip mewakili satu jadwal keberangkatan. Ajukan untuk ditinjau sebelum tampil di katalog." :navigation="navigation" vendor><template #actions><Link :href="route('vendor.section', 'trips')" class="panel-secondary">Kembali</Link></template><form class="panel-surface max-w-4xl p-6" @submit.prevent="submit"><Fields :fields="fields" :form="form" /><button :disabled="form.processing" class="panel-primary mt-6">Simpan trip</button></form><div v-if="trip" class="mt-5"><button class="panel-secondary text-rose-600" @click="deleting = !deleting">Hapus trip</button><form v-if="deleting" class="mt-3 panel-surface max-w-4xl p-5" @submit.prevent="deletion.delete(route('vendor.trips.destroy', trip.id))"><p class="mb-3 text-sm">Hapus trip ini dari katalog?</p><button class="panel-primary" :disabled="deletion.processing">Ya, hapus trip</button><button type="button" class="panel-secondary ml-3" @click="deleting = false">Batal</button><p v-if="deletion.errors.delete" class="mt-3 text-sm text-rose-600">{{ deletion.errors.delete }}</p></form></div></PanelLayout></template>

