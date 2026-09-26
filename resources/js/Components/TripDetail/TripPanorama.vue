<script setup>
import { computed, ref } from 'vue';
import { ArrowLeft, ArrowRight, MapPin } from 'lucide-vue-next';
import PanoramaMark from '../Shared/PanoramaMark.vue';
import PanoramaViewer from '../Shared/PanoramaViewer.vue';

const props = defineProps({
    tripType: { type: String, required: true },
    tours: { type: Array, default: () => [] },
});

const isPrivateTrip = computed(() => props.tripType === 'private-trip');
const selectedSpot = ref(0);

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

const spots = computed(() => (isPrivateTrip.value ? privateTripSpots : openTripSpots));
const activeSpot = computed(() => spots.value[selectedSpot.value]);
const activeTour = computed(() => props.tours[selectedSpot.value] || {
    id: activeSpot.value.name,
    title: activeSpot.value.name,
    image_url: activeSpot.value.image,
});

const showSpot = (index) => {
    const count = props.tours.length || spots.value.length;
    selectedSpot.value = (index + count) % count;
};
</script>

<template>
    <section class="mt-7 overflow-hidden rounded-xl border border-[#dbe8f5] bg-white shadow-[0_12px_30px_rgba(23,75,120,0.07)]" aria-labelledby="trip-panorama-heading">
        <div class="grid lg:grid-cols-[minmax(0,1fr)_290px]">
            <div class="min-w-0 p-4 sm:p-5">
                <div class="flex flex-wrap items-center justify-between gap-3">
                    <div class="flex items-start gap-3">
                        <span class="grid size-9 shrink-0 place-items-center rounded-lg bg-[#edf7ff] text-[#1688e8]">
                            <PanoramaMark class="size-6" />
                        </span>
                        <div>
                            <p class="text-[10px] font-bold uppercase tracking-[0.16em] text-[#1688e8]">Preview khusus trip ini</p>
                            <h2 id="trip-panorama-heading" class="mt-0.5 text-lg font-extrabold tracking-tight text-[#173b70]">Rasakan rute dalam 360°</h2>
                        </div>
                    </div>
                    <span class="rounded-lg bg-[#1688e8] px-2.5 py-1.5 text-[10px] font-extrabold text-white shadow-[0_4px_10px_rgba(22,136,232,0.2)]">360° Interactive</span>
                </div>
                <p class="mt-2 max-w-xl text-[11px] leading-5 text-[#60789c]">
                    Kenali suasana setiap titik sebelum berangkat. Viewer berputar otomatis dan dapat digerakkan secara interaktif.
                </p>

                <div class="relative mt-4 overflow-hidden rounded-xl border border-[#dcebf7] bg-[#071a32] shadow-[0_10px_22px_rgba(21,77,126,0.12)]">
                    <PanoramaViewer :key="activeTour.id" :tour="activeTour" :auto-rotate="true" />
                    <button
                        type="button"
                        class="absolute left-3 top-1/2 z-10 grid size-8 -translate-y-1/2 place-items-center rounded-full bg-white/90 text-[#1688e8] shadow-lg transition hover:bg-white"
                        aria-label="Spot sebelumnya"
                        @click="showSpot(selectedSpot - 1)"
                    >
                        <ArrowLeft class="size-4" />
                    </button>
                    <button
                        type="button"
                        class="absolute right-3 top-1/2 z-10 grid size-8 -translate-y-1/2 place-items-center rounded-full bg-white/90 text-[#1688e8] shadow-lg transition hover:bg-white"
                        aria-label="Spot berikutnya"
                        @click="showSpot(selectedSpot + 1)"
                    >
                        <ArrowRight class="size-4" />
                    </button>
                </div>

                <div class="mt-3 flex gap-2 overflow-x-auto pb-1 [scrollbar-width:none] [&::-webkit-scrollbar]:hidden" aria-label="Pilih spot panorama">
                    <button
                        v-for="(spot, index) in (tours.length ? tours : spots)"
                        :key="spot.id || spot.name"
                        type="button"
                        class="group/thumb relative h-14 w-24 shrink-0 overflow-hidden rounded-lg border-2 text-left transition sm:h-16 sm:w-28"
                        :class="selectedSpot === index ? 'border-[#1688e8] shadow-[0_4px_12px_rgba(22,136,232,0.18)]' : 'border-transparent opacity-70 hover:opacity-100'"
                        :aria-pressed="selectedSpot === index"
                        @click="showSpot(index)"
                    >
                        <img :src="spot.image_url || spot.image" :alt="spot.title || spot.name" class="size-full object-cover" />
                        <span class="absolute inset-0 bg-gradient-to-t from-black/70 to-transparent"></span>
                        <span class="absolute bottom-2 left-2 right-2 truncate text-[9px] font-bold text-white">{{ spot.title || spot.name }}</span>
                    </button>
                </div>
            </div>

            <aside class="border-t border-[#e5eef7] bg-[#f6faff] p-4 sm:p-5 lg:border-l lg:border-t-0">
                <p class="text-[10px] font-bold uppercase tracking-[0.16em] text-[#1688e8]">Spot dalam itinerary</p>
                <h3 class="mt-1 text-[15px] font-extrabold text-[#173b70]">Pilih sudut yang ingin dilihat</h3>
                <p class="mt-1.5 text-[10px] leading-4 text-[#60789c]">Pilih titik itinerary untuk berpindah panorama.</p>
                <div class="mt-4 space-y-1.5">
                    <button
                        v-for="(spot, index) in spots"
                        :key="spot.name"
                        type="button"
                        class="flex w-full items-center gap-2.5 rounded-lg border p-2 text-left transition"
                        :class="selectedSpot === index ? 'border-[#8ec9f7] bg-white shadow-[0_4px_12px_rgba(22,102,171,0.08)]' : 'border-transparent bg-transparent hover:border-[#d9e9f6] hover:bg-white'"
                        :aria-pressed="selectedSpot === index"
                        @click="showSpot(index)"
                    >
                        <span
                            class="grid size-6 shrink-0 place-items-center rounded-full text-[9px] font-extrabold"
                            :class="selectedSpot === index ? 'bg-[#1688e8] text-white' : 'bg-[#e7f3ff] text-[#1688e8]'"
                        >
                            0{{ index + 1 }}
                        </span>
                        <span class="min-w-0 flex-1">
                            <span class="block truncate text-xs font-bold text-[#173b70]">{{ spot.name }}</span>
                            <span class="mt-0.5 block text-[10px] text-[#7186a2]">{{ spot.label }}</span>
                        </span>
                        <ArrowRight class="size-4 shrink-0" :class="selectedSpot === index ? 'text-[#1688e8]' : 'text-[#b1c0cf]'" />
                    </button>
                </div>
            </aside>
        </div>
    </section>
</template>
