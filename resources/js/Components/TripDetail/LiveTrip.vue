<script setup>
import { computed } from 'vue';
import { Head, Link, useForm, usePage } from '@inertiajs/vue3';
import { route } from 'ziggy-js';
import { CalendarDays, MapPin, ShieldCheck, Star, Users } from 'lucide-vue-next';
import MainNavigation from '../Shared/MainNavigation.vue';
import VirtualTourGallery from '../Shared/VirtualTourGallery.vue';
const props = defineProps({ trip: Object, bookingKey: String, reviews: Array, virtualTours: Array });
const page = usePage();
const form = useForm({ trip_id: props.trip.id, participants: 1, contact_name: page.props.auth?.user?.name || '', contact_phone: page.props.auth?.user?.phone || '', promotion_code: '', idempotency_key: props.bookingKey });
const seats = computed(() => Math.max(0, props.trip.capacity - props.trip.reserved_seats));
const money = value => new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', maximumFractionDigits: 0 }).format(value);
</script>
<template>
    <Head :title="trip.title" /><MainNavigation />
    <div class="min-h-screen bg-[#f6f8fb] text-[#17375f]"><main class="mx-auto max-w-[1180px] px-5 py-8 sm:px-8">
        <Link :href="route('catalog')" class="text-xs font-semibold text-blue-600">← Kembali ke pilihan trip</Link>
        <div class="mt-6 flex flex-wrap items-end justify-between gap-4"><div><p class="text-xs font-bold uppercase tracking-widest text-blue-500">{{ trip.type === 'open-trip' ? 'Open Trip' : 'Private Trip' }}</p><h1 class="mt-2 text-3xl font-extrabold tracking-tight">{{ trip.title }}</h1><p class="mt-3 flex items-center gap-2 text-sm text-slate-500"><MapPin class="size-4" />{{ trip.destination }}</p></div><Link v-if="page.props.auth?.user" :href="route('favorites.store', trip.id)" method="post" as="button" class="panel-secondary">Simpan favorit</Link></div>
        <div v-if="trip.image_url" class="mt-7 aspect-[16/7] overflow-hidden rounded-3xl bg-blue-50"><img :src="trip.image_url" :alt="trip.title" class="size-full object-cover" fetchpriority="high" /></div>
        <VirtualTourGallery :tours="virtualTours" /><div class="mt-7 grid items-start gap-6 lg:grid-cols-[minmax(0,1fr)_340px]">
            <div class="grid gap-6"><section class="panel-surface p-6"><div class="flex flex-wrap gap-5 border-b border-slate-100 pb-5 text-xs text-slate-600"><span class="flex items-center gap-2"><CalendarDays class="size-4 text-blue-500" />{{ trip.departure_date.slice(0,10) }} — {{ trip.end_date.slice(0,10) }}</span><span class="flex items-center gap-2"><Users class="size-4 text-blue-500" />Sisa {{ seats }} tempat</span></div><h2 class="mt-5 font-bold">Tentang perjalanan</h2><p class="mt-3 whitespace-pre-wrap text-sm leading-7 text-slate-600">{{ trip.description }}</p></section>
                <section class="panel-surface p-6"><h2 class="font-bold">Rencana perjalanan</h2><p class="mt-3 whitespace-pre-wrap text-sm leading-7 text-slate-600">{{ trip.itinerary }}</p><h3 class="mt-6 text-sm font-bold">Titik kumpul</h3><p class="mt-2 text-sm text-slate-600">{{ trip.meeting_point }}</p></section>
                <section class="panel-surface p-6"><h2 class="font-bold">Ulasan traveler</h2><p v-if="!reviews.length" class="mt-3 text-sm text-slate-500">Belum ada ulasan untuk perjalanan ini.</p><article v-for="review in reviews" :key="review.id" class="mt-5 border-t border-slate-100 pt-5"><div class="flex items-center justify-between text-xs"><strong>{{ review.user.name }}</strong><span class="flex items-center gap-1 text-amber-600"><Star class="size-3 fill-current" />{{ review.rating }}/5</span></div><p class="mt-3 text-sm leading-6 text-slate-600">{{ review.body }}</p><p v-if="review.vendor_response" class="mt-3 rounded-xl bg-blue-50 p-3 text-xs leading-6">Tanggapan mitra: {{ review.vendor_response }}</p></article></section>
            </div>
            <aside class="panel-surface p-6 lg:sticky lg:top-32"><p class="flex items-center gap-2 text-xs text-slate-500"><ShieldCheck class="size-4 text-blue-500" />{{ trip.vendor.name }}</p><p class="mt-4 text-2xl font-extrabold text-blue-600">{{ money(trip.selling_price) }}<span class="text-xs font-normal text-slate-400"> / orang</span></p>
                <form v-if="page.props.auth?.user" class="mt-6 grid gap-4" @submit.prevent="form.post(route('bookings.store'))"><label class="text-xs font-semibold">Nama kontak<input v-model="form.contact_name" required maxlength="100" autocomplete="name" class="panel-input mt-2" /></label><label class="text-xs font-semibold">Nomor kontak<input v-model="form.contact_phone" required maxlength="30" type="tel" autocomplete="tel" class="panel-input mt-2" /></label><label class="text-xs font-semibold">Jumlah peserta<input v-model.number="form.participants" required type="number" min="1" :max="Math.min(50, seats)" class="panel-input mt-2" /></label><label class="text-xs font-semibold">Kode voucher<input v-model="form.promotion_code" maxlength="50" class="panel-input mt-2" /></label><p v-for="(error, key) in form.errors" :key="key" role="alert" class="text-xs text-rose-600">{{ error }}</p><div class="flex justify-between border-t border-slate-100 pt-4 text-sm"><span>Subtotal</span><strong>{{ money(trip.selling_price * form.participants) }}</strong></div><p class="text-[11px] leading-5 text-slate-500">Diskon dan ketersediaan diperiksa saat pesanan dibuat. Pembayaran dilanjutkan pada halaman pesanan.</p><button class="panel-primary w-full" :disabled="form.processing || seats < 1">{{ form.processing ? 'Membuat pesanan…' : seats ? 'Buat pesanan' : 'Kuota habis' }}</button></form>
                <Link v-else :href="route('login', { trip: trip.id })" class="panel-primary mt-6 w-full">Masuk untuk memesan</Link>
            </aside>
        </div>
    </main></div>
</template>
