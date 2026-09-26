<script setup>
import { reactive } from 'vue';
import { Head, router } from '@inertiajs/vue3';
import { route } from 'ziggy-js';
import MainNavigation from '../Components/Shared/MainNavigation.vue';
import TripCards from '../Components/Shared/TripCards.vue';
import Pagination from '../Components/Admin/Pagination.vue';
const props = defineProps({ trips: Object, filters: Object });
const filters = reactive({
    q: props.filters.q || '',
    type: props.filters.type || '',
    date: props.filters.date || '',
    guests: props.filters.guests || '',
});
</script>
<template>
    <Head title="Jelajahi Trip" /><MainNavigation />
    <main class="mx-auto max-w-6xl px-5 py-10"><p class="text-xs font-bold uppercase tracking-widest text-blue-500">JELAJAH LOKAL</p><h1 class="mt-3 text-3xl font-bold text-[#17345e]">Temukan perjalanan berikutnya.</h1><p class="mt-3 text-sm text-slate-500">Jadwal dan paket dari mitra lokal terverifikasi.</p>
        <form class="my-7 grid items-end gap-3 sm:grid-cols-[2fr_1fr_1fr_1fr_auto]" @submit.prevent="router.get(route('catalog'), filters)">
            <label class="text-xs font-semibold text-slate-500">Destinasi atau trip<input v-model="filters.q" type="search" placeholder="Cari perjalanan…" class="panel-input mt-2" /></label>
            <label class="text-xs font-semibold text-slate-500">Jenis trip<select v-model="filters.type" class="panel-input mt-2"><option value="">Semua jenis</option><option value="open-trip">Open Trip</option><option value="private-trip">Private Trip</option></select></label>
            <label class="text-xs font-semibold text-slate-500">Keberangkatan<input v-model="filters.date" type="date" class="panel-input mt-2" /></label>
            <label class="text-xs font-semibold text-slate-500">Jumlah Orang<select v-model="filters.guests" class="panel-input mt-2"><option value="">Semua Pax</option><option value="1">1 Orang</option><option value="2">2 Orang</option><option value="4">4 Orang</option><option value="6">6 Orang</option><option value="12">12 Orang</option></select></label>
            <button class="panel-primary">Cari</button>
        </form>
        <TripCards :trips="trips.data" /><Pagination :records="trips" />
    </main>
</template>
