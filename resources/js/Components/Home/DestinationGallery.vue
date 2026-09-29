<script setup>
import { computed, nextTick, ref, watch } from 'vue';
import { Search, ChevronRight, ChevronLeft, X, Compass } from 'lucide-vue-next';
import PanoramaMark from '../Shared/PanoramaMark.vue';
import PanoramaViewer from '../Shared/PanoramaViewer.vue';

const props = defineProps({
    tours: {
        type: Array,
        default: () => [],
    },
});

const query = ref('');
const chosen = ref(null);
const listContainer = ref(null);
const thumbnailSlider = ref(null);

const filtered = computed(() => {
    const q = query.value.trim().toLocaleLowerCase('id-ID');
    if (!q) return props.tours;
    return props.tours.filter((tour) =>
        tour.title.toLocaleLowerCase('id-ID').includes(q)
    );
});

// Tampilkan semua destinasi hasil pencarian tanpa batas slice
const displayTours = computed(() => filtered.value);

const active = computed(() => {
    if (chosen.value) {
        const found = props.tours.find((tour) => tour.id === chosen.value);
        if (found) return found;
    }
    return displayTours.value[0] || props.tours[0];
});

function selectTour(tourId) {
    chosen.value = tourId;
}

function clearQuery() {
    query.value = '';
}

// Navigasi slide thumbnail pada card kiri
function slideThumbnails(direction) {
    if (!thumbnailSlider.value) return;
    const scrollAmount = 240 * direction;
    thumbnailSlider.value.scrollBy({ left: scrollAmount, behavior: 'smooth' });
}

// Sinkronisasi scroll saat destinasi aktif berubah
watch(
    () => active.value?.id,
    async (newId) => {
        if (!newId) return;
        await nextTick();

        // Posisikan thumbnail yang aktif ke tengah slider
        if (thumbnailSlider.value) {
            const activeThumb = thumbnailSlider.value.querySelector(`[data-thumb-id="${newId}"]`);
            if (activeThumb) {
                const containerWidth = thumbnailSlider.value.clientWidth;
                const thumbLeft = activeThumb.offsetLeft;
                const thumbWidth = activeThumb.clientWidth;
                thumbnailSlider.value.scrollTo({
                    left: thumbLeft - (containerWidth / 2) + (thumbWidth / 2),
                    behavior: 'smooth',
                });
            }
        }

        // Posisikan item yang aktif di dalam list card kanan
        if (listContainer.value) {
            const activeListItem = listContainer.value.querySelector(`[data-list-id="${newId}"]`);
            if (activeListItem) {
                activeListItem.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
            }
        }
    }
);
</script>

<template>
    <section
        class="mx-auto mt-20 grid max-w-[1180px] gap-3 text-[#172c70] sm:mt-24 lg:grid-cols-[704fr_462fr] lg:items-stretch"
        aria-labelledby="destination-gallery-heading"
    >
        <!-- Card Kiri: Virtual Tour Panorama 360 -->
        <div class="flex flex-col justify-between min-w-0 rounded-2xl bg-white/95 p-5 sm:p-6 shadow-sm border border-slate-100">
            <div>
                <div class="mb-4 flex items-start gap-3">
                    <PanoramaMark class="size-11 shrink-0 text-[#0088ff]" />
                    <div>
                        <h2 id="destination-gallery-heading" class="text-lg sm:text-xl font-bold text-[#172c70]">
                            Jelajahi Destinasi dalam 360°
                        </h2>
                        <p class="mt-1 text-xs leading-5 text-[#788caf]">
                            Rasakan pengalaman virtual sebelum kamu berangkat. Pilih panorama untuk melihat suasana destinasi secara menyeluruh.
                        </p>
                    </div>
                </div>

                <!-- Panorama WebGL Canvas Viewer -->
                <div class="overflow-hidden rounded-xl bg-slate-900">
                    <PanoramaViewer v-if="active" :key="active.id" :tour="active" />
                    <div v-else class="flex flex-col items-center justify-center rounded-xl bg-sky-50/70 p-12 text-center text-sm text-slate-500">
                        <Compass class="size-8 text-sky-400 mb-2" />
                        <p>Panorama belum diterbitkan.</p>
                    </div>
                </div>
            </div>

            <!-- List Thumbnail Virtual Tour di Card Kiri: Horizontal Slidable Carousel -->
            <div class="mt-4">
                <div class="mb-2 flex items-center justify-between text-xs">
                    <span class="font-medium text-slate-500 text-[11px]">Pilihan Cepat Panorama</span>
                    <span class="text-[11px] font-semibold text-[#0088ff]">
                        {{ active ? active.title : 'Pilih Destinasi' }}
                    </span>
                </div>

                <div class="relative group/slider">
                    <!-- Tombol Navigasi Slide Kiri -->
                    <button
                        type="button"
                        class="absolute left-1 top-1/2 -translate-y-1/2 z-10 flex size-7 items-center justify-center rounded-full bg-slate-900/70 text-white shadow-md backdrop-blur-sm transition hover:bg-slate-900 hover:scale-110 active:scale-95 disabled:opacity-0"
                        aria-label="Geser ke kiri"
                        @click="slideThumbnails(-1)"
                    >
                        <ChevronLeft class="size-4" />
                    </button>

                    <!-- Container Thumbnail Slidable -->
                    <div
                        ref="thumbnailSlider"
                        class="flex gap-2.5 overflow-x-auto scroll-smooth no-scrollbar py-1 px-0.5"
                    >
                        <button
                            v-for="tour in displayTours"
                            :key="tour.id"
                            :data-thumb-id="tour.id"
                            type="button"
                            class="group relative aspect-[4/3] w-24 sm:w-28 shrink-0 overflow-hidden rounded-xl border-2 transition-all duration-200 active:scale-95"
                            :class="active?.id === tour.id
                                ? 'border-[#0088ff] ring-2 ring-[#0088ff]/30 shadow-md scale-[1.02]'
                                : 'border-transparent opacity-75 hover:opacity-100 hover:border-slate-300'"
                            :aria-pressed="active?.id === tour.id"
                            @click="selectTour(tour.id)"
                        >
                            <img
                                :src="tour.image_url"
                                :alt="tour.title"
                                loading="lazy"
                                class="size-full object-cover transition duration-300 group-hover:scale-105"
                            />
                            <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-black/20 to-transparent"></div>
                            <span class="absolute inset-x-0 bottom-0 p-1.5 text-center text-[10px] font-medium leading-tight text-white truncate block drop-shadow-sm">
                                {{ tour.title }}
                            </span>
                            <span
                                v-if="active?.id === tour.id"
                                class="absolute top-1 right-1 size-2 rounded-full bg-[#0088ff] ring-2 ring-white"
                            ></span>
                        </button>
                    </div>

                    <!-- Tombol Navigasi Slide Kanan -->
                    <button
                        type="button"
                        class="absolute right-1 top-1/2 -translate-y-1/2 z-10 flex size-7 items-center justify-center rounded-full bg-slate-900/70 text-white shadow-md backdrop-blur-sm transition hover:bg-slate-900 hover:scale-110 active:scale-95 disabled:opacity-0"
                        aria-label="Geser ke kanan"
                        @click="slideThumbnails(1)"
                    >
                        <ChevronRight class="size-4" />
                    </button>
                </div>
            </div>
        </div>

        <!-- Card Kanan: List Destinasi 360 (Semua item dengan fitur scroll/slide) -->
        <div class="flex flex-col rounded-2xl bg-white/95 p-5 sm:p-6 shadow-sm border border-slate-100 min-w-0">
            <!-- Search Input -->
            <label class="mb-3 flex items-center gap-2.5 rounded-xl border border-[#e5edf8] bg-[#f8fbff] px-3.5 py-2.5 transition-colors focus-within:border-[#0088ff] focus-within:bg-white focus-within:ring-2 focus-within:ring-blue-100">
                <Search class="size-4 text-[#0088ff] shrink-0" />
                <input
                    v-model="query"
                    type="search"
                    placeholder="Cari destinasi..."
                    aria-label="Cari panorama destinasi"
                    class="w-full bg-transparent text-xs text-slate-800 placeholder-slate-400 outline-none"
                />
                <button
                    v-if="query"
                    type="button"
                    class="size-4 rounded-full text-slate-400 hover:text-slate-600 transition"
                    aria-label="Hapus pencarian"
                    @click="clearQuery"
                >
                    <X class="size-3.5" />
                </button>
            </label>

            <!-- Header Info & Item Counter -->
            <div class="mb-2.5 flex items-center justify-between px-0.5">
                <span class="text-xs font-bold text-[#172c70]">
                    Daftar Lokasi 360°
                </span>
                <span class="rounded-full bg-sky-50 border border-sky-100 px-2 py-0.5 text-[10px] font-semibold text-[#0088ff]">
                    {{ displayTours.length }} Destinasi
                </span>
            </div>

            <!-- List Item Container (Scrollable / Slidable dengan custom scrollbar) -->
            <div
                ref="listContainer"
                class="flex flex-1 flex-col gap-2.5 overflow-y-auto pr-1 sm:pr-1.5 max-h-[380px] sm:max-h-[440px] lg:max-h-[470px] custom-scrollbar scroll-smooth"
            >
                <button
                    v-for="tour in displayTours"
                    :key="tour.id"
                    :data-list-id="tour.id"
                    type="button"
                    class="group flex items-center gap-3 rounded-xl border p-2 text-left transition duration-200"
                    :class="active?.id === tour.id
                        ? 'border-[#0088ff] bg-[#edf5ff] shadow-sm ring-1 ring-[#0088ff]/30'
                        : 'border-[#e5edf8] bg-white hover:border-[#8bc5ff] hover:bg-[#f8fbff]'"
                    @click="selectTour(tour.id)"
                >
                    <!-- Thumbnail Image -->
                    <div class="relative shrink-0 overflow-hidden rounded-lg bg-slate-100">
                        <img
                            :src="tour.image_url"
                            alt=""
                            loading="lazy"
                            class="h-16 w-24 object-cover transition-transform duration-300 group-hover:scale-105"
                        />
                        <span class="absolute bottom-1 right-1 rounded bg-black/70 px-1 py-0.5 text-[8px] font-bold tracking-wider text-white">
                            360°
                        </span>
                    </div>

                    <!-- Detail Text -->
                    <div class="min-w-0 flex-1">
                        <strong
                            class="block truncate text-xs font-semibold text-[#172c70] group-hover:text-[#0088ff] transition-colors"
                            :title="tour.title"
                        >
                            {{ tour.title }}
                        </strong>
                        <div class="mt-1 flex items-center gap-1.5 text-[10px]">
                            <template v-if="active?.id === tour.id">
                                <span class="inline-flex items-center gap-1 font-semibold text-[#0088ff]">
                                    <span class="size-1.5 rounded-full bg-[#0088ff] animate-pulse"></span>
                                    Sedang Ditampilkan
                                </span>
                            </template>
                            <template v-else>
                                <span class="text-slate-500">Panorama 360°</span>
                            </template>
                        </div>
                    </div>

                    <!-- Right Arrow -->
                    <ChevronRight
                        class="size-4 shrink-0 transition-transform duration-200 group-hover:translate-x-0.5"
                        :class="active?.id === tour.id ? 'text-[#0088ff]' : 'text-slate-400'"
                    />
                </button>

                <!-- Empty State -->
                <div
                    v-if="!displayTours.length"
                    class="flex flex-col items-center justify-center p-8 text-center text-xs text-slate-500 my-auto"
                >
                    <Compass class="mb-2 size-8 text-sky-300" />
                    <p class="font-medium text-slate-700">Destinasi tidak ditemukan</p>
                    <p class="mt-0.5 text-[11px] text-slate-400">Coba kata kunci pencarian yang lain</p>
                    <button
                        v-if="query"
                        type="button"
                        class="mt-3 rounded-lg bg-sky-50 px-3 py-1.5 text-xs font-semibold text-[#0088ff] hover:bg-sky-100 transition"
                        @click="clearQuery"
                    >
                        Reset Pencarian
                    </button>
                </div>
            </div>
        </div>
    </section>
</template>

<style scoped>
/* Custom sleek scrollbar for right card */
.custom-scrollbar::-webkit-scrollbar {
    width: 5px;
}
.custom-scrollbar::-webkit-scrollbar-track {
    background: #f1f5f9;
    border-radius: 9999px;
}
.custom-scrollbar::-webkit-scrollbar-thumb {
    background: #cbd5e1;
    border-radius: 9999px;
}
.custom-scrollbar::-webkit-scrollbar-thumb:hover {
    background: #94a3b8;
}

/* Hide scrollbar for clean horizontal carousel */
.no-scrollbar::-webkit-scrollbar {
    display: none;
}
.no-scrollbar {
    -ms-overflow-style: none;
    scrollbar-width: none;
}
</style>
