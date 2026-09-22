<script setup>
import { computed } from 'vue';
import { Link, useForm } from '@inertiajs/vue3';
import { route } from 'ziggy-js';
import PanelLayout from '../../Components/Admin/PanelLayout.vue';
import Pagination from '../../Components/Admin/Pagination.vue';
const props = defineProps({ section: String, vendor: Object, records: Object, stats: Array });
const sections = { trips: 'Trip & Jadwal', bookings: 'Pemesanan', finance: 'Keuangan', reviews: 'Rating & Ulasan', profile: 'Profil & Verifikasi', support: 'Bantuan & Chat' };
const navigation = Object.entries(sections).map(([key, label]) => ({ key, label, group: ['profile', 'support'].includes(key) ? 'Bisnis Anda' : 'Operasional', url: route('vendor.section', key) }));
const form = useForm({ name: props.vendor?.name || '', city: props.vendor?.city || '', email: props.vendor?.email || '', phone: props.vendor?.phone || '', description: props.vendor?.description || '', document_id: props.vendor?.document_id || '' });
const upload = useForm({ file: null, alt_text: 'Dokumen verifikasi mitra', visibility: 'private' });
const statusForm = useForm({ status: '' });
const money = n => new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', maximumFractionDigits: 0 }).format(n || 0);
const transition = (item, status) => { statusForm.status = status; statusForm.put(route('vendor.bookings.update', item.id)); };
</script>
<template>
    <PanelLayout :title="sections[section] || 'Overview Partner'" subtitle="Kelola perjalanan dan layanan lokal, dari satu workspace." :navigation="navigation" vendor>
        <template #actions><Link v-if="section === 'trips' && vendor?.status === 'verified'" :href="route('vendor.trips.create')" class="panel-primary">Tambah trip / jadwal</Link></template>
        <div v-if="!vendor || vendor.status !== 'verified'" class="mb-6 rounded-xl border border-amber-100 bg-amber-50 p-4 text-sm leading-6 text-amber-800">Status mitra: <strong>{{ vendor?.status || 'belum terdaftar' }}</strong>. Lengkapi profil dan dokumen agar tim operasional dapat meninjau bisnis kamu.</div>
        <template v-if="section === 'profile' || !vendor">
            <form class="panel-surface max-w-3xl p-6" @submit.prevent="form.put(route('vendor.profile.save'))"><h2 class="mb-5 font-bold">Identitas bisnis</h2><div class="grid gap-4 sm:grid-cols-2"><label v-for="(label, key) in { name: 'Nama bisnis', city: 'Kota', email: 'Email bisnis', phone: 'Nomor kontak', document_id: 'ID dokumen verifikasi' }" :key="key" class="text-xs font-semibold">{{ label }}<input v-model="form[key]" :type="key === 'email' ? 'email' : key === 'document_id' ? 'number' : 'text'" :required="key !== 'document_id'" class="panel-input mt-2" /></label><label class="text-xs font-semibold sm:col-span-2">Deskripsi<textarea v-model="form.description" rows="4" class="panel-input mt-2"></textarea></label></div><button :disabled="form.processing" class="panel-primary mt-5">Simpan profil</button></form>
            <form class="panel-surface mt-5 max-w-3xl p-6" @submit.prevent="upload.post(route('media.store'))"><h2 class="text-sm font-bold">Dokumen verifikasi privat</h2><p class="mt-2 text-xs leading-6 text-slate-500">Upload JPG, PNG, WebP atau PDF maksimal 5 MB. Gunakan ID dari hasil upload pada profil di atas.</p><input required type="file" class="mt-4 block text-xs" accept="image/jpeg,image/png,image/webp,application/pdf" @change="upload.file = $event.target.files[0]" /><button :disabled="upload.processing" class="panel-secondary mt-4">Upload dokumen</button></form>
        </template>
        <template v-else-if="section === 'dashboard'"><div class="grid gap-4 sm:grid-cols-3"><article v-for="stat in stats" :key="stat.label" class="panel-surface p-6"><p class="text-xs text-slate-500">{{ stat.label }}</p><p class="mt-4 text-3xl font-bold">{{ stat.money ? money(stat.value) : stat.value }}</p></article></div><div class="panel-surface mt-6 p-7"><h2 class="text-lg font-bold">Siapkan perjalanan berikutnya.</h2><p class="mt-2 max-w-xl text-sm leading-7 text-slate-500">Periksa jadwal, kuota, dan pesanan. Trip yang diajukan akan ditinjau tim Tapak Lokal sebelum terbit.</p><Link :href="route('vendor.section', 'trips')" class="panel-primary mt-5">Kelola trip</Link></div></template>
        <section v-else class="panel-surface">
            <article v-for="item in records?.data || []" :key="item.id" class="flex flex-wrap items-center justify-between gap-4 border-b border-slate-100 p-5">
                <div class="min-w-0"><p class="text-sm font-bold">{{ item.title || item.reference || item.subject || item.trip?.title || 'Payout #' + item.id }}</p><p class="mt-2 text-xs text-slate-500">{{ item.destination || item.contact_name || item.body || '' }}</p><p class="mt-2 text-xs text-slate-400">{{ item.departure_date?.slice(0, 10) || '' }} {{ item.status }}</p><p v-if="item.total || item.amount || item.price" class="mt-2 text-sm font-semibold text-blue-600">{{ money(item.total || item.amount || item.price) }}</p></div>
                <div class="flex flex-wrap gap-2"><Link v-if="section === 'trips'" :href="route('vendor.trips.edit', item.id)" class="panel-secondary">Kelola</Link><Link v-if="section === 'support'" :href="route('support.show', item.id)" class="panel-secondary">Buka chat</Link><template v-if="section === 'bookings'"><button v-if="item.status === 'paid'" class="panel-primary" :disabled="statusForm.processing" @click="transition(item, 'confirmed')">Konfirmasi</button><button v-if="item.status === 'confirmed'" class="panel-secondary" :disabled="statusForm.processing" @click="transition(item, 'ongoing')">Mulai</button><button v-if="item.status === 'ongoing'" class="panel-secondary" :disabled="statusForm.processing" @click="transition(item, 'completed')">Selesai</button></template></div>
            </article>
            <p v-if="!records?.data?.length" class="p-16 text-center text-sm text-slate-400">Belum ada data untuk bagian ini.</p><Pagination :records="records" />
        </section>
        <p v-if="section === 'finance'" class="mt-4 text-xs text-slate-500">Payout dibuat setelah perjalanan selesai. Persetujuan dan pengiriman dana memiliki status terpisah.</p>
    </PanelLayout>
</template>

