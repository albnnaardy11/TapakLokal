<script setup>
import { route } from 'ziggy-js';
import { computed, ref } from 'vue';
import { BadgeCheck, CalendarDays, ChevronLeft, ChevronRight, Clock3, ExternalLink, Heart, MapPin, Star, Users } from 'lucide-vue-next';

const props = defineProps({ items: { type: Array, default: () => [] }, vendor: Object, tripType: { type: String, required: true } });
const currentPage = ref(1);
const savedTrips = ref([]);
const pageSize = 3;
const trips = computed(() => props.items.map(item => ({ ...item, location: item.destination, duration: 'Lihat jadwal perjalanan', price: new Intl.NumberFormat('id-ID').format(item.selling_price), image: item.image_url, url: route('trips.show', {tripType: item.type, trip: item.slug}) })));
const pageCount = computed(() => Math.ceil(trips.value.length / pageSize));
const visibleTrips = computed(() => trips.value.slice((currentPage.value - 1) * pageSize, currentPage.value * pageSize));
const toggleSaved = (title) => { savedTrips.value = savedTrips.value.includes(title) ? savedTrips.value.filter((item) => item !== title) : [...savedTrips.value, title]; };
</script>

<template>
    <section class="relative mt-7 overflow-hidden rounded-xl border border-[#e1ebf6] bg-white shadow-[0_6px_18px_rgba(23,75,120,0.04)]" aria-labelledby="recommendation-heading">
        <div class="absolute right-0 top-0 h-52 w-1/2 opacity-60 [background:repeating-radial-gradient(ellipse_at_100%_0%,transparent_0_20px,#d8ecff_21px_22px,transparent_23px_44px)]"></div>
        <header class="relative flex flex-wrap items-center justify-between gap-5 border-b border-[#edf3f8] px-5 py-5 sm:px-6"><div><p class="text-[10px] font-bold uppercase tracking-[0.16em] text-[#1688e8]">Pilihan partner</p><h2 id="recommendation-heading" class="mt-1 text-xl font-extrabold tracking-tight text-[#082d61]">Jelajahi trip lain dari vendor ini</h2><p class="mt-1 text-xs text-[#7186a2]">Temukan lebih banyak pengalaman seru bersama partner terpercaya TapakLokal.</p></div><a v-if="false" href="#" class="group inline-flex items-center gap-2 rounded-full border-2 border-[#b8dcff] bg-white px-4 py-2 text-xs font-extrabold text-[#1688e8] transition-colors duration-200 hover:border-[#1688e8] hover:bg-[#1688e8] hover:text-white hover:shadow-[0_5px_12px_rgba(22,136,232,0.22)]">Lihat semua trip vendor <ChevronRight class="size-4 transition-transform duration-200 group-hover:translate-x-0.5" /></a></header>
        <div class="relative bg-white p-5 sm:p-6">
            <section class="flex flex-col gap-4 border-b border-[#e5eef7] pb-5 lg:flex-row lg:items-center lg:justify-between" aria-label="Informasi vendor">
                <div class="flex items-center gap-3.5">
                    <span class="grid size-12 shrink-0 place-items-center rounded-xl border border-[#e4edf6] bg-gradient-to-br from-blue-50 to-indigo-50 shadow-xs">
                        <span class="text-lg font-black text-[#1688e8]">{{ (vendor?.name || 'M').slice(0, 1).toUpperCase() }}</span>
                    </span>
                    <div>
                        <p class="text-[10px] font-bold uppercase tracking-[0.14em] text-[#1688e8]">Diselenggarakan oleh</p>
                        <p class="mt-0.5 flex items-center gap-1.5 text-base font-extrabold text-[#082d61]">
                            {{ vendor?.name || 'Mitra TapakLokal' }}
                            <BadgeCheck class="size-4 shrink-0 fill-[#1688e8] text-white" />
                        </p>
                        <p class="text-[11px] text-[#7186a2]">Partner terverifikasi TapakLokal</p>
                    </div>
                </div>

                <div class="grid w-full grid-cols-1 divide-y divide-[#e5eef7] rounded-xl border border-[#e5eef7] bg-[#fbfdff] shadow-xs sm:w-auto sm:grid-cols-3 sm:divide-x sm:divide-y-0">
                    <div class="flex items-center gap-3 px-4 py-2.5 sm:px-4 sm:py-3">
                        <span class="grid size-8 shrink-0 place-items-center rounded-full bg-[#fff5db]">
                            <Star class="size-4 fill-[#f5a000] text-[#f5a000]" />
                        </span>
                        <div class="min-w-0">
                            <p class="text-xs font-extrabold text-[#173b70] leading-tight">Mitra resmi</p>
                            <p class="mt-0.5 text-[10px] text-[#7186a2] leading-tight">Terverifikasi</p>
                        </div>
                    </div>

                    <div class="flex items-center gap-3 px-4 py-2.5 sm:px-4 sm:py-3">
                        <span class="grid size-8 shrink-0 place-items-center rounded-full bg-[#eaf5ff]">
                            <Clock3 class="size-4 text-[#1688e8]" />
                        </span>
                        <div class="min-w-0">
                            <p class="text-xs font-extrabold text-[#173b70] leading-tight">Bantuan vendor</p>
                            <p class="mt-0.5 text-[10px] text-[#7186a2] leading-tight">Melalui pesanan</p>
                        </div>
                    </div>

                    <div class="flex items-center gap-3 px-4 py-2.5 sm:px-4 sm:py-3">
                        <span class="grid size-8 shrink-0 place-items-center rounded-full bg-[#eaf5ff]">
                            <Users class="size-4 text-[#1688e8]" />
                        </span>
                        <div class="min-w-0">
                            <p class="text-xs font-extrabold text-[#173b70] leading-tight">{{ trips.length }} trip tersedia</p>
                            <p class="mt-0.5 text-[10px] text-[#7186a2] leading-tight">Pilihan perjalanan</p>
                        </div>
                    </div>
                </div>
            </section>
            <div class="mt-5"><div class="mb-3 flex items-center justify-between"><h3 class="text-base font-extrabold text-[#082d61]">Trip yang mungkin kamu suka</h3><span class="text-[10px] font-semibold text-[#60789c]">{{ trips.length }} trip tersedia</span></div><p v-if="!trips.length" class="py-5 text-sm text-[#7186a2]">Belum ada trip lain yang tersedia dari vendor ini.</p><div class="relative"><div class="grid gap-3 sm:grid-cols-2 xl:grid-cols-3"><article v-for="trip in visibleTrips" :key="trip.title" class="overflow-hidden rounded-lg border border-[#dce9f5] bg-white"><div class="relative h-32 overflow-hidden"><img :src="trip.image" :alt="trip.title" class="size-full object-cover" /><span class="absolute left-3 top-3 rounded-md bg-[#1688e8] px-2.5 py-1 text-[10px] font-bold text-white">{{ tripType === 'private-trip' ? 'Private Trip' : 'Open Trip' }}</span><button type="button" class="absolute right-3 top-3 grid size-8 place-items-center rounded-full bg-white/95 text-[#082d61]" :aria-label="`Simpan ${trip.title}`" @click="toggleSaved(trip.title)"><Heart class="size-4" :class="savedTrips.includes(trip.title) ? 'fill-[#ef4d63] text-[#ef4d63]' : ''" /></button></div><div class="p-3"><h3 class="truncate text-sm font-extrabold text-[#082d61]">{{ trip.title }}</h3><p class="mt-2 flex items-center gap-1.5 text-[11px] text-[#60789c]"><MapPin class="size-3.5 text-[#1688e8]" />{{ trip.location }}</p><p class="mt-1 flex items-center gap-1.5 text-[11px] text-[#60789c]"><CalendarDays class="size-3.5 text-[#1688e8]" />{{ trip.duration }}</p><div class="mt-3 flex items-center justify-between border-t border-[#e7eef5] pt-3"><span class="text-base font-extrabold text-[#082d61]">Rp {{ trip.price }}<small class="ml-1 text-[10px] font-medium text-[#7186a2]">/orang</small></span><a :href="trip.url" class="inline-flex items-center gap-1 text-[10px] font-bold text-[#1688e8]">Detail <ChevronRight class="size-3.5" /></a></div></div></article></div><button v-if="currentPage > 1" type="button" class="absolute -left-4 top-1/2 hidden size-10 -translate-y-1/2 place-items-center rounded-full border border-[#dce8f3] bg-white text-[#1688e8] shadow-lg xl:grid" aria-label="Rekomendasi sebelumnya" @click="currentPage -= 1"><ChevronLeft class="size-5" /></button><button type="button" class="absolute -right-4 top-1/2 hidden size-10 -translate-y-1/2 place-items-center rounded-full border border-[#dce8f3] bg-white text-[#1688e8] shadow-lg xl:grid disabled:opacity-40" :disabled="currentPage >= pageCount" aria-label="Rekomendasi berikutnya" @click="currentPage += 1"><ChevronRight class="size-5" /></button></div><footer v-if="pageCount > 1" class="mt-4 flex justify-end gap-2"><button v-for="page in pageCount" :key="page" type="button" class="size-2 rounded-full transition-all" :class="currentPage === page ? 'w-6 bg-[#1688e8]' : 'bg-[#c7dcef]'" :aria-label="`Halaman ${page}`" @click="currentPage = page"></button></footer></div>
        </div>    </section>
</template>
