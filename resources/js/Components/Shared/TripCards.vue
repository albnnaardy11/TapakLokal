<script setup>
import { Link } from '@inertiajs/vue3';
import { route } from 'ziggy-js';
import { CalendarDays, MapPin, ArrowUpRight } from 'lucide-vue-next';
defineProps({ trips: { type: Array, default: () => [] } });
const money = value => new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', maximumFractionDigits: 0 }).format(value);
</script>
<template>
    <div class="grid gap-5 sm:grid-cols-2 lg:grid-cols-3">
        <article v-for="trip in trips" :key="trip.id" class="panel-surface">
            <Link :href="route('trips.show', [trip.type, trip.slug])" class="group block h-full">
                <div class="relative aspect-[16/10] overflow-hidden bg-blue-50"><img v-if="trip.image_url" :src="trip.image_url" :alt="trip.title" loading="lazy" class="size-full object-cover transition-transform motion-safe:group-hover:scale-105" /><span class="absolute bottom-3 left-3 rounded-lg bg-white px-2 py-1 text-[10px] font-bold text-blue-600">{{ trip.type === 'open-trip' ? 'Open Trip' : 'Private Trip' }}</span></div>
                <div class="p-5"><p class="text-[10px] uppercase tracking-wider text-slate-400">{{ trip.vendor.name }}</p><h2 class="mt-2 text-base font-bold text-[#17375f] group-hover:text-blue-600">{{ trip.title }}</h2><p class="mt-2 flex items-center gap-1 text-xs text-slate-500"><MapPin class="size-3" />{{ trip.destination }}</p><p class="mt-2 flex items-center gap-1 text-xs text-slate-500"><CalendarDays class="size-3" />{{ trip.departure_date.slice(0, 10) }} · Sisa {{ Math.max(0, trip.capacity - trip.reserved_seats) }} tempat</p><div class="mt-5 flex items-center justify-between"><p class="font-bold text-blue-600">{{ money(trip.selling_price) }} <span class="text-xs font-normal text-slate-400">/ orang</span></p><ArrowUpRight class="size-5 text-blue-600" /></div></div>
            </Link>
        </article>
    </div>
    <div v-if="!trips.length" class="panel-surface py-16 text-center"><h2 class="font-bold text-slate-600">Belum ada trip tersedia</h2><p class="mt-2 text-sm text-slate-400">Coba pencarian lain atau cek kembali jadwal berikutnya.</p></div>
</template>
