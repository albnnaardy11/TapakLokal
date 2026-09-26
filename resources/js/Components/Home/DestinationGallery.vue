<script setup>
import { computed, ref } from 'vue';
import { Search, ChevronRight } from 'lucide-vue-next';
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

const filtered = computed(() =>
    props.tours.filter((tour) =>
        tour.title.toLocaleLowerCase('id-ID').includes(query.value.toLocaleLowerCase('id-ID'))
    )
);

// Batasi list hanya 5 destinasi saja
const displayTours = computed(() => filtered.value.slice(0, 5));

const active = computed(() => {
    if (chosen.value) {
        const found = props.tours.find((tour) => tour.id === chosen.value);
        if (found) return found;
    }
    return displayTours.value[0] || props.tours[0];
});
</script>

<template>
    <section
        class="mx-auto mt-20 grid max-w-[1180px] gap-2.5 text-[#172c70] sm:mt-24 lg:grid-cols-[704fr_462fr] lg:items-stretch"
        aria-labelledby="destination-gallery-heading"
    >
        <!-- Card Kiri: Virtual Tour Panorama 360 -->
        <div class="flex flex-col justify-between min-w-0 rounded-2xl bg-white/95 p-5 sm:p-6 shadow-sm">
            <div>
                <div class="mb-4 flex items-start gap-3">
                    <PanoramaMark class="size-11 shrink-0 text-[#078cff]" />
                    <div>
                        <h2 id="destination-gallery-heading" class="text-lg font-bold text-[#172c70]">
                            Jelajahi Destinasi dalam 360°
                        </h2>
                        <p class="mt-1 text-xs leading-5 text-[#788caf]">
                            Rasakan pengalaman virtual sebelum kamu berangkat. Pilih panorama untuk melihat suasana destinasi.
                        </p>
                    </div>
                </div>

                <PanoramaViewer v-if="active" :key="active.id" :tour="active" />
                <p v-else class="rounded-xl bg-sky-50 p-12 text-center text-sm">
                    Panorama belum diterbitkan.
                </p>
            </div>

            <!-- List Thumbnail Virtual Tour di Card Kiri disamakan 5 item sesuai list -->
            <div class="mt-3.5 grid grid-cols-5 gap-2 sm:gap-2.5">
                <button
                    v-for="tour in displayTours"
                    :key="tour.id"
                    type="button"
                    class="group relative aspect-[4/3] w-full overflow-hidden rounded-xl border-2 transition active:scale-95"
                    :class="active?.id === tour.id ? 'border-blue-500 ring-2 ring-blue-400/30' : 'border-transparent opacity-80 hover:opacity-100'"
                    :aria-pressed="active?.id === tour.id"
                    @click="chosen = tour.id"
                >
                    <img
                        :src="tour.image_url"
                        :alt="tour.title"
                        loading="lazy"
                        class="size-full object-cover transition group-hover:scale-105"
                    />
                    <span class="absolute inset-x-0 bottom-0 bg-slate-950/75 p-1.5 text-center text-[10px] font-medium text-white truncate block">
                        {{ tour.title }}
                    </span>
                </button>
            </div>
        </div>

        <!-- Card Kanan: List Destinasi (5 item, tinggi dipotong pas dengan 5 item) -->
        <div class="flex flex-col rounded-2xl bg-white/95 p-5 sm:p-6 shadow-sm">
            <label class="mb-3.5 flex items-center gap-3 rounded-xl border border-[#e5edf8] bg-[#f8fbff] px-3.5 py-2.5">
                <Search class="size-4 text-[#078cff]" />
                <input
                    v-model="query"
                    type="search"
                    placeholder="Cari destinasi..."
                    aria-label="Cari panorama destinasi"
                    class="w-full bg-transparent text-xs outline-none"
                />
            </label>

            <div class="flex flex-col gap-2.5">
                <button
                    v-for="tour in displayTours"
                    :key="tour.id"
                    type="button"
                    class="flex items-center gap-3 rounded-xl border p-2 text-left transition hover:border-[#8bc5ff]"
                    :class="active?.id === tour.id ? 'border-[#8bc5ff] bg-[#edf5ff]' : 'border-[#e5edf8] bg-white'"
                    @click="chosen = tour.id"
                >
                    <img :src="tour.image_url" alt="" loading="lazy" class="h-16 w-24 shrink-0 rounded-lg object-cover" />
                    <span class="min-w-0 flex-1">
                        <strong class="block truncate text-xs font-semibold text-[#172c70]">{{ tour.title }}</strong>
                        <span class="mt-0.5 block text-[10px] text-slate-500">Panorama 360°</span>
                    </span>
                    <ChevronRight class="size-4 shrink-0 text-blue-500" />
                </button>

                <p v-if="!displayTours.length" class="p-6 text-center text-xs text-slate-500">
                    Destinasi tidak ditemukan.
                </p>
            </div>
        </div>
    </section>
</template>
