<script setup>
import { computed, ref } from 'vue';
import { ArrowLeft, ArrowRight, Expand, MapPin, X } from 'lucide-vue-next';
import PanoramaMark from '../Shared/PanoramaMark.vue';

const props = defineProps({
    tripType: { type: String, required: true },
});

const isPrivateTrip = computed(() => props.tripType === 'private-trip');
const selectedSpot = ref(0);
const isViewerOpen = ref(false);

const openTripSpots = [
    { name: 'Pulau Pramuka', label: 'Pulau utama', image: 'https://images.unsplash.com/photo-1516690561799-46d8f74f9abf?auto=format&fit=crop&w=1600&q=90' },
    { name: 'Pulau Semak Daun', label: 'Spot snorkeling', image: 'https://images.unsplash.com/photo-1544551763-46a013bb70d5?auto=format&fit=crop&w=1600&q=90' },
    { name: 'Pantai Perawan', label: 'Waktu bebas', image: 'https://images.unsplash.com/photo-1518548419970-58e3b4079ab2?auto=format&fit=crop&w=1600&q=90' },
    { name: 'Tepi mangrove', label: 'Jelajah pulau', image: 'https://images.unsplash.com/photo-1470165518243-ff5f2f6f9f37?auto=format&fit=crop&w=1600&q=90' },
];

const privateTripSpots = [
    { name: 'Pelabuhan Labuan Bajo', label: 'Titik keberangkatan', image: 'https://images.unsplash.com/photo-1507525428034-b723cf961d3e?auto=format&fit=crop&w=1600&q=90' },
    { name: 'Pulau Kelor', label: 'Trekking ringan', image: 'https://images.unsplash.com/photo-1493552152660-f915ab47ae9d?auto=format&fit=crop&w=1600&q=90' },
    { name: 'Pink Beach', label: 'Pantai dan laut', image: 'https://images.unsplash.com/photo-1514282401047-d79a71a590e8?auto=format&fit=crop&w=1600&q=90' },
    { name: 'Pulau Kalong', label: 'Titik sunset', image: 'https://images.unsplash.com/photo-1544550285-f813152fb2fd?auto=format&fit=crop&w=1600&q=90' },
];

const spots = computed(() => isPrivateTrip.value ? privateTripSpots : openTripSpots);
const activeSpot = computed(() => spots.value[selectedSpot.value]);

const showSpot = (index) => {
    selectedSpot.value = (index + spots.value.length) % spots.value.length;
};
</script>

<template>
    <section class="mt-7 overflow-hidden rounded-2xl border border-[#dfeaf5] bg-white shadow-[0_10px_28px_rgba(23,75,120,0.05)]" aria-labelledby="trip-panorama-heading">
        <div class="grid lg:grid-cols-[minmax(0,1fr)_330px]">
            <div class="min-w-0 p-5 sm:p-6">
                <div class="flex flex-wrap items-end justify-between gap-3">
                    <div class="flex items-start gap-3"><span class="grid size-10 shrink-0 place-items-center rounded-xl bg-[#edf7ff] text-[#1688e8]"><PanoramaMark class="size-7" /></span><div><p class="text-[10px] font-bold uppercase tracking-[0.16em] text-[#1688e8]">Preview khusus trip ini</p><h2 id="trip-panorama-heading" class="mt-1 text-xl font-extrabold tracking-tight text-[#173b70]">Rasakan rute dalam 360°</h2></div></div>
                    <span class="rounded-full bg-[#1688e8] px-3 py-1.5 text-[10px] font-extrabold text-white">360° Preview</span>
                </div>
                <p class="mt-3 max-w-2xl text-xs leading-5 text-[#60789c]">Kenali suasana setiap titik sebelum berangkat. Pilih spot pada trip untuk melihat preview panoramanya.</p>

                <div class="group relative mt-5 h-72 overflow-hidden rounded-2xl bg-[#092e56] sm:h-[360px]">
                    <img :src="activeSpot.image" :alt="`${activeSpot.name} preview 360 derajat`" class="size-full object-cover transition duration-500" />
                    <div class="absolute inset-0 bg-gradient-to-t from-[#062343]/80 via-transparent to-[#062343]/20"></div>
                    <div class="absolute left-4 top-4 inline-flex items-center gap-2 rounded-full bg-[#062343]/75 px-3 py-1.5 text-[10px] font-bold text-white backdrop-blur-sm"><MapPin class="size-3.5" />{{ activeSpot.name }}</div>
                    <span class="absolute right-4 top-4 grid size-10 place-items-center rounded-full border border-white/35 bg-white/12 text-white backdrop-blur-sm"><PanoramaMark class="size-7" /></span>
                    <div class="absolute bottom-4 left-4 right-4 flex items-end justify-between gap-3"><div><p class="text-xs font-semibold text-white/70">{{ activeSpot.label }}</p><p class="mt-1 text-lg font-extrabold text-white">{{ activeSpot.name }}</p></div><button type="button" class="inline-flex shrink-0 items-center gap-2 rounded-xl bg-white px-3 py-2 text-[10px] font-bold text-[#1688e8] shadow-lg transition hover:-translate-y-0.5 hover:bg-[#1688e8] hover:text-white" @click="isViewerOpen = true"><Expand class="size-4" />Layar penuh</button></div>
                    <button type="button" class="absolute left-4 top-1/2 grid size-9 -translate-y-1/2 place-items-center rounded-full bg-white/90 text-[#1688e8] shadow-lg transition hover:bg-white" aria-label="Spot sebelumnya" @click="showSpot(selectedSpot - 1)"><ArrowLeft class="size-4" /></button>
                    <button type="button" class="absolute right-4 top-1/2 grid size-9 -translate-y-1/2 place-items-center rounded-full bg-white/90 text-[#1688e8] shadow-lg transition hover:bg-white" aria-label="Spot berikutnya" @click="showSpot(selectedSpot + 1)"><ArrowRight class="size-4" /></button>
                </div>

                <div class="mt-4 flex gap-2 overflow-x-auto pb-1 [scrollbar-width:none] [&::-webkit-scrollbar]:hidden" aria-label="Pilih spot panorama"><button v-for="(spot, index) in spots" :key="spot.name" type="button" class="group/thumb relative h-16 w-28 shrink-0 overflow-hidden rounded-xl border-2 text-left transition sm:h-[72px] sm:w-32" :class="selectedSpot === index ? 'border-[#1688e8] shadow-[0_4px_12px_rgba(22,136,232,0.18)]' : 'border-transparent opacity-70 hover:opacity-100'" :aria-pressed="selectedSpot === index" @click="showSpot(index)"><img :src="spot.image" :alt="spot.name" class="size-full object-cover" /><span class="absolute inset-0 bg-gradient-to-t from-black/70 to-transparent"></span><span class="absolute bottom-2 left-2 right-2 truncate text-[9px] font-bold text-white">{{ spot.name }}</span></button></div>
            </div>

            <aside class="border-t border-[#e5eef7] bg-[#f8fbff] p-5 sm:p-6 lg:border-l lg:border-t-0">
                <p class="text-[10px] font-bold uppercase tracking-[0.16em] text-[#1688e8]">Spot dalam itinerary</p>
                <h3 class="mt-1 text-base font-extrabold text-[#173b70]">Pilih sudut yang ingin dilihat</h3>
                <p class="mt-2 text-[11px] leading-5 text-[#60789c]">Preview membantu kamu mengenali suasana lokasi sebelum hari keberangkatan.</p>
                <div class="mt-5 space-y-2"><button v-for="(spot, index) in spots" :key="spot.name" type="button" class="flex w-full items-center gap-3 rounded-xl border p-2.5 text-left transition" :class="selectedSpot === index ? 'border-[#8ec9f7] bg-white shadow-[0_4px_12px_rgba(22,102,171,0.08)]' : 'border-transparent bg-transparent hover:border-[#d9e9f6] hover:bg-white'" :aria-pressed="selectedSpot === index" @click="showSpot(index)"><span class="grid size-7 shrink-0 place-items-center rounded-full text-[10px] font-extrabold" :class="selectedSpot === index ? 'bg-[#1688e8] text-white' : 'bg-[#e7f3ff] text-[#1688e8]'">0{{ index + 1 }}</span><span class="min-w-0 flex-1"><span class="block truncate text-xs font-bold text-[#173b70]">{{ spot.name }}</span><span class="mt-0.5 block text-[10px] text-[#7186a2]">{{ spot.label }}</span></span><ArrowRight class="size-4 shrink-0" :class="selectedSpot === index ? 'text-[#1688e8]' : 'text-[#b1c0cf]'" /></button></div>
                <div class="mt-5 rounded-xl border border-[#d7e9f9] bg-white p-3"><p class="flex items-center gap-2 text-[11px] font-bold text-[#31577f]"><PanoramaMark class="size-5 text-[#1688e8]" />Nikmati sebelum berangkat</p><p class="mt-1.5 text-[10px] leading-4 text-[#7186a2]">Gunakan preview ini untuk memilih spot foto dan mempersiapkan aktivitasmu.</p></div>
            </aside>
        </div>
    </section>

    <Teleport to="body">
        <div v-if="isViewerOpen" class="fixed inset-0 z-[110] flex items-center justify-center bg-[#061c34]/95 p-4" role="dialog" aria-modal="true" :aria-label="`Preview 360 ${activeSpot.name}`" @click.self="isViewerOpen = false">
            <div class="relative h-full w-full max-w-6xl overflow-hidden rounded-2xl bg-black"><img :src="activeSpot.image" :alt="`${activeSpot.name} preview 360 derajat`" class="size-full object-contain" /><div class="absolute left-5 top-5 rounded-full bg-[#062343]/75 px-3 py-1.5 text-xs font-bold text-white backdrop-blur-sm">{{ activeSpot.name }} · 360° Preview</div><button type="button" class="absolute right-5 top-5 grid size-10 place-items-center rounded-full bg-white/15 text-white backdrop-blur-sm transition hover:bg-white/25" aria-label="Tutup preview" @click="isViewerOpen = false"><X class="size-5" /></button><button type="button" class="absolute left-5 top-1/2 grid size-11 -translate-y-1/2 place-items-center rounded-full bg-white/90 text-[#1688e8]" aria-label="Spot sebelumnya" @click="showSpot(selectedSpot - 1)"><ArrowLeft class="size-5" /></button><button type="button" class="absolute right-5 top-1/2 grid size-11 -translate-y-1/2 place-items-center rounded-full bg-white/90 text-[#1688e8]" aria-label="Spot berikutnya" @click="showSpot(selectedSpot + 1)"><ArrowRight class="size-5" /></button></div>
        </div>
    </Teleport>
</template>
