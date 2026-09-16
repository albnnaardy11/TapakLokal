<script setup>
import { ArrowRight, ChevronRight, MapPin, Play, Search, X } from 'lucide-vue-next';
import PanoramaMark from '../Shared/PanoramaMark.vue';
import { computed, nextTick, ref } from 'vue';

defineEmits(['select']);

const imageUrl = (id) => `https://images.unsplash.com/photo-${id}?auto=format&fit=crop&w=1200&q=85`;
const photos = [
    imageUrl('1516690561799-46d8f74f9abf'),
    imageUrl('1518548419970-58e3b4079ab2'),
    imageUrl('1546026423-cc4642628d2b'),
    imageUrl('1501179691627-eeaa65ea017c'),
    imageUrl('1537996194471-e657df975ab4'),
];
const destinations = [
    { id: 'pramuka', name: 'Pulau Pramuka', image: photos[0] },
    { id: 'pari', name: 'Pulau Pari', image: photos[1] },
    { id: 'tidung', name: 'Pulau Tidung', image: photos[2] },
    { id: 'harapan', name: 'Pulau Harapan', image: photos[3] },
    { id: 'kelapa', name: 'Pulau Kelapa', image: photos[4] },
];
const selectedDestination = ref(destinations[0]);
const selectedPhoto = ref(0);
const query = ref('');
const searchInput = ref(null);
const thumbnails = ref(null);
const destinationList = ref(null);
const region = 'Kab. Administrasi Kepulauan Seribu';
const filteredDestinations = computed(() => destinations.filter((destination) => `${destination.name} ${region}`.toLocaleLowerCase('id-ID').includes(query.value.trim().toLocaleLowerCase('id-ID'))));

const navigateDestinations = (event) => {
    const buttons = Array.from(destinationList.value?.querySelectorAll('button') || []);
    if (! buttons.length) {
        return;
    }

    const current = buttons.indexOf(event.target);
    if (! ['ArrowDown', 'ArrowUp', 'Home', 'End', 'Escape'].includes(event.key) || (current === -1 && ! ['ArrowDown', 'ArrowUp'].includes(event.key))) {
        return;
    }

    event.preventDefault();
    if (event.key === 'Escape') {
        searchInput.value?.focus({ preventScroll: true });
        return;
    }

    let index = event.key === 'ArrowUp' ? Math.max(0, current - 1) : Math.min(buttons.length - 1, current + 1);
    if (event.key === 'Home') {
        index = 0;
    } else if (event.key === 'End' || (current === -1 && event.key === 'ArrowUp')) {
        index = buttons.length - 1;
    }

    const button = buttons[index];
    button.focus({ preventScroll: true });
    const list = destinationList.value;
    const top = button.getBoundingClientRect().top - list.getBoundingClientRect().top;
    const bottom = top + button.offsetHeight;
    if (top < 4) {
        list.scrollTop += top - 4;
    } else if (bottom > list.clientHeight - 4) {
        list.scrollTop += bottom - list.clientHeight + 4;
    }
};
const gallery = computed(() => [
    { name: selectedDestination.value.name, image: selectedDestination.value.image },
    { name: 'Pantai Pasir Perawan', image: photos[1] },
    { name: 'Snorkeling Area', image: photos[2] },
    { name: 'Hutan Mangrove', image: photos[3] },
    { name: 'Bukit Taman Nasional', image: photos[4] },
]);

const chooseDestination = (destination) => {
    selectedDestination.value = destination;
    selectedPhoto.value = 0;
    thumbnails.value?.scrollTo({ left: 0 });
};

const nextPhoto = async () => {
    selectedPhoto.value = (selectedPhoto.value + 1) % gallery.value.length;
    await nextTick();
    const element = thumbnails.value;
    const card = element?.children[selectedPhoto.value];
    if (card) {
        element.scrollTo({ left: card.offsetLeft - element.offsetLeft, behavior: window.matchMedia('(prefers-reduced-motion: reduce)').matches ? 'instant' : 'smooth' });
    }
};

const showAll = () => {
    query.value = '';
    searchInput.value?.focus({ preventScroll: true });
};
</script>

<template>
    <section class="mx-auto mt-20 grid max-w-[1180px] gap-2.5 text-[#172c70] sm:mt-24 lg:grid-cols-[704fr_462fr]" aria-labelledby="destination-gallery-heading">
        <div class="min-w-0 rounded-2xl bg-white/95 p-5 sm:p-6 lg:min-h-[560px]">
            <div class="mb-5 grid grid-cols-[40px_minmax(0,1fr)] items-start gap-x-3 gap-y-2 sm:grid-cols-[44px_minmax(0,1fr)]">
                <div class="col-start-2 flex flex-col items-start gap-2">
                    <span class="inline-flex h-5 items-center rounded-full bg-[#078cff] px-2.5 text-[10px] font-bold leading-none"> <span class="text-white">360°</span></span>
                </div>
                <div class="row-start-2 flex h-7 items-center justify-center">
                    <PanoramaMark class="size-10 shrink-0 text-[#078cff] sm:size-11" />
                </div>
                <div class="col-start-2 row-start-2 min-w-0">
                    <h2 id="destination-gallery-heading" class="text-base font-bold leading-7 tracking-tight sm:text-lg">Jelajahi Destinasi dalam 360°</h2>
                    <p class="mt-1.5 max-w-[440px] text-[11px] leading-[18px] text-[#788caf]">Rasakan pengalaman virtual sebelum kamu berangkat. Nikmati keindahan destinasi impian di {{ selectedDestination.name }} dan sekitarnya dalam tampilan 360° yang imersif.</p>
                </div>
            </div>

            <div class="group/preview relative isolate aspect-[665/275] min-h-52 overflow-hidden rounded-xl bg-sky-100 sm:min-h-0">
                <img :src="gallery[selectedPhoto].image" :alt="`${gallery[selectedPhoto].name} — ${selectedDestination.name}`" class="absolute inset-0 -z-20 size-full object-cover" loading="lazy" />
                <div class="absolute inset-0 -z-10 bg-gradient-to-t from-black/30 via-transparent to-transparent"></div>
                <span class="absolute left-3 top-2.5 inline-flex h-[25px] items-center gap-1.5 rounded-full bg-[#092b43]/85 px-4 text-[9px] font-semibold text-white transition duration-500 ease-out [@media(hover:hover)]:-translate-y-2 [@media(hover:hover)]:opacity-0 group-hover/preview:translate-y-0 group-hover/preview:opacity-100 group-focus-within/preview:translate-y-0 group-focus-within/preview:opacity-100 motion-reduce:transition-none"><MapPin class="size-3.5" aria-hidden="true" />{{ selectedDestination.name }}</span>
                <PanoramaMark class="pointer-events-none absolute left-1/2 top-1/2 size-[72px] -translate-x-1/2 -translate-y-1/2 text-white drop-shadow-md transition duration-500 ease-out [@media(hover:hover)]:scale-90 [@media(hover:hover)]:opacity-0 group-hover/preview:scale-100 group-hover/preview:opacity-100 group-focus-within/preview:scale-100 group-focus-within/preview:opacity-100 motion-reduce:transition-none" />
                <div class="absolute inset-x-0 bottom-0 h-16 transition duration-500 ease-out [@media(hover:hover)]:pointer-events-none [@media(hover:hover)]:translate-y-3 [@media(hover:hover)]:opacity-0 group-hover/preview:pointer-events-auto group-hover/preview:translate-y-0 group-hover/preview:opacity-100 group-focus-within/preview:pointer-events-auto group-focus-within/preview:translate-y-0 group-focus-within/preview:opacity-100 motion-reduce:transition-none">
                <button type="button" class="group absolute bottom-3 left-1/2 inline-flex h-10 -translate-x-1/2 items-center gap-2.5 whitespace-nowrap rounded-full bg-white px-5 text-xs font-bold text-[#078cff] shadow-[0_4px_16px_rgba(0,0,0,0.15)] transition duration-300 ease-out hover:-translate-y-0.5 hover:bg-[#078cff] hover:text-white hover:shadow-[0_8px_24px_rgba(0,130,255,0.3)] active:scale-95 focus-visible:outline-2 focus-visible:outline-offset-4 focus-visible:outline-[#078cff] motion-reduce:transition-none" @click="$emit('select', selectedDestination.name)"><Play class="size-4 fill-current" aria-hidden="true" />Lihat Tour<ArrowRight class="ml-2 size-4 transition-transform duration-300 group-hover:translate-x-1 motion-reduce:transition-none" aria-hidden="true" /></button>
                </div>
            </div>

            <div class="relative mt-3">
                <div ref="thumbnails" class="relative flex min-w-0 snap-x snap-mandatory gap-2.5 overflow-x-auto p-1.5 [scrollbar-width:none] [&::-webkit-scrollbar]:hidden" aria-label="Galeri foto destinasi">
                    <button v-for="(photo, index) in gallery" :key="index" type="button" class="group relative isolate h-[108px] w-[calc((100%-10px)/2)] shrink-0 snap-start overflow-hidden rounded-xl outline-none transition duration-300 ease-out hover:-translate-y-0.5 active:scale-[0.98] focus-visible:ring-2 focus-visible:ring-[#078cff] motion-reduce:transition-none sm:w-[calc((100%-30px)/4)]" :class="selectedPhoto === index ? 'ring-2 ring-[#078cff] ring-offset-2 shadow-[0_4px_12px_rgba(0,140,255,0.2)]' : 'hover:ring-2 hover:ring-[#a9d7ff] hover:shadow-md'" :aria-pressed="selectedPhoto === index" :aria-label="`Tampilkan foto ${photo.name}`" @click="selectedPhoto = index">
                        <img :src="photo.image" alt="" loading="lazy" class="absolute inset-0 -z-20 size-full object-cover transition-transform duration-500 ease-out group-hover:scale-110 group-focus-visible:scale-110 motion-reduce:transition-none" />
                        <div class="absolute inset-0 -z-10 bg-gradient-to-t from-black/80 to-transparent"></div>
                        <PanoramaMark class="pointer-events-none absolute left-2.5 top-2.5 size-6 -translate-y-1 scale-90 text-white opacity-0 transition duration-300 ease-out group-hover:translate-y-0 group-hover:scale-100 group-hover:opacity-100 group-focus-visible:translate-y-0 group-focus-visible:scale-100 group-focus-visible:opacity-100 motion-reduce:transition-none" />
                        <span class="absolute bottom-2.5 left-2.5 right-2.5 text-left text-[10px] font-semibold leading-snug text-white drop-shadow-sm">{{ photo.name }}</span>
                        <span class="pointer-events-none absolute right-2.5 top-2.5 -translate-y-1 rounded-full bg-[#073c50]/80 px-2 py-1 text-[8px] font-bold leading-none text-white opacity-0 backdrop-blur-sm transition duration-300 ease-out group-hover:translate-y-0 group-hover:opacity-100 group-focus-visible:translate-y-0 group-focus-visible:opacity-100 motion-reduce:transition-none">360°</span>
                    </button>
                </div>
                <button type="button" class="absolute -right-2 top-1/2 z-10 grid size-8 -translate-y-1/2 place-items-center rounded-full border border-sky-100 bg-white text-[#078cff] shadow-md transition duration-300 hover:scale-105 hover:bg-[#078cff] hover:text-white active:scale-95 focus-visible:outline-2 focus-visible:outline-[#078cff] motion-reduce:transition-none" aria-label="Foto berikutnya" @click="nextPhoto"><ChevronRight class="size-5" aria-hidden="true" /></button>
            </div>
        </div>

        <div class="flex h-[560px] min-h-0 min-w-0 flex-col overflow-hidden rounded-2xl bg-white/95 p-5 sm:p-6">
            <div class="mb-4 flex items-center gap-3">
                <label class="flex min-w-0 flex-1 items-center gap-3 rounded-xl border border-[#e5edf8] bg-[#f8fbff] px-3.5 py-1 transition duration-200 focus-within:border-[#078cff] focus-within:bg-white focus-within:ring-4 focus-within:ring-sky-100">
                    <Search class="size-5 shrink-0 text-[#078cff]" aria-hidden="true" />
                    <input ref="searchInput" v-model="query" type="search" placeholder="Cari destinasi..." aria-label="Cari destinasi" aria-controls="gallery-destination-list" class="min-w-0 w-full bg-transparent py-2.5 text-xs font-medium text-[#172c70] outline-none placeholder:text-slate-400" @keydown="navigateDestinations" />
                </label>
            </div>

            <div v-if="filteredDestinations.length" id="gallery-destination-list" ref="destinationList" class="grid min-h-0 flex-1 basis-0 content-start auto-rows-[96px] gap-2.5 overflow-y-auto overscroll-contain p-1 pr-2 [scrollbar-color:#b5d8ff_transparent] [scrollbar-width:thin]" aria-label="Pilihan destinasi" @keydown="navigateDestinations">
                <button v-for="destination in filteredDestinations" :key="destination.id" type="button" class="group flex min-h-[68px] w-full items-center gap-3 rounded-xl border p-2 text-left transition duration-300 ease-out hover:-translate-y-0.5 hover:shadow-[0_5px_16px_rgba(25,105,180,0.1)] active:translate-y-0 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-[#078cff] motion-reduce:transition-none" :class="selectedDestination.id === destination.id ? 'border-[#8bc5ff] bg-[#edf5ff]' : 'border-[#e5edf8] bg-white hover:border-[#b5d8ff] hover:bg-[#f4f9ff]'" :aria-pressed="selectedDestination.id === destination.id" @click="chooseDestination(destination)">
                    <span class="h-[52px] w-[95px] shrink-0 overflow-hidden rounded-lg"><img :src="destination.image" alt="" loading="lazy" class="size-full object-cover transition-transform duration-500 group-hover:scale-105 motion-reduce:transition-none" /></span>
                    <div class="min-w-0 flex-1"><h3 class="text-[11px] font-bold">{{ destination.name }}</h3><p class="mt-1 text-[9px] leading-relaxed text-[#788caf]">{{ region }}</p></div>
                    <ChevronRight class="mr-1 size-4 shrink-0 text-[#078cff] transition-transform duration-300 group-hover:translate-x-1 motion-reduce:transition-none" aria-hidden="true" />
                </button>
            </div>
                <div v-else class="flex flex-1 flex-col items-center justify-center rounded-xl bg-[#f8fbff] px-4 py-10 text-center">
                    <Search class="mb-4 size-8 text-[#078cff]/60" aria-hidden="true" />
                    <p class="text-sm font-semibold">Destinasi tidak ditemukan</p>
                    <p class="mt-2 text-xs text-slate-500">Coba nama pulau lain atau kata kunci yang lebih singkat.</p>
                    <button type="button" class="mt-4 inline-flex items-center gap-2 rounded-lg bg-sky-50 px-3 py-2 text-xs font-semibold text-[#078cff] hover:bg-sky-100" @click="showAll"><X class="size-3" aria-hidden="true" />Hapus pencarian</button>
                </div>
            <p class="sr-only" role="status">{{ filteredDestinations.length }} destinasi ditemukan. Destinasi dipilih: {{ selectedDestination.name }}.</p>
        </div>
    </section>
</template>
