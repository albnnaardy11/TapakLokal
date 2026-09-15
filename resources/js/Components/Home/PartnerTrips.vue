<script setup>
import { ArrowRight, ChevronRight, Compass, Star } from 'lucide-vue-next';
import { nextTick, onBeforeUnmount, onMounted, ref } from 'vue';

defineEmits(['select']);

const carousel = ref(null);
const showAll = ref(false);
const pagePositions = ref([0]);
const currentPage = ref(0);
let resizeObserver;
let autoplayTimer;
let isHovered = false;
let hasFocus = false;

const trips = [
    { id: 'komodo', name: 'Open Trip pulau komodo', destination: 'Pulau Komodo', location: 'Malang', duration: '3 hari 2 malam', price: 'Rp350.000', image: 'https://images.unsplash.com/photo-1518548419970-58e3b4079ab2?auto=format&fit=crop&w=640&q=85' },
    { id: 'bromo', name: 'Open Trip Bromo', destination: 'Bromo', location: 'Malang', duration: '3 hari 2 malam', price: 'Rp350.000', image: 'https://images.unsplash.com/photo-1501179691627-eeaa65ea017c?auto=format&fit=crop&w=640&q=85' },
    { id: 'raja-ampat', name: 'Open Raja Ampat', destination: 'Raja Ampat', location: 'Papua', duration: '6 hari 5 malam', price: 'Rp15.000.000', image: 'https://images.unsplash.com/photo-1516690561799-46d8f74f9abf?auto=format&fit=crop&w=640&q=85' },
    { id: 'bali', name: 'Open Trip Bali', destination: 'Bali', location: 'Bali', duration: '3 hari 2 malam', price: 'Rp350.000', image: 'https://images.unsplash.com/photo-1537996194471-e657df975ab4?auto=format&fit=crop&w=640&q=85' },
];

const updateCurrentPage = () => {
    const scrollLeft = carousel.value?.scrollLeft || 0;
    currentPage.value = pagePositions.value.reduce((nearest, position, index) => Math.abs(position - scrollLeft) < Math.abs(pagePositions.value[nearest] - scrollLeft) ? index : nearest, 0);
};

const updatePagination = () => {
    const element = carousel.value;
    if (! element || showAll.value) {
        return;
    }

    const maximumScroll = Math.max(0, element.scrollWidth - element.clientWidth);
    const step = (element.firstElementChild?.getBoundingClientRect().width || 180) + 14;
    pagePositions.value = Array.from({ length: Math.ceil(maximumScroll / step) + 1 }, (_, index) => Math.min(index * step, maximumScroll));
    updateCurrentPage();
};

const moveToPage = (page) => {
    carousel.value?.scrollTo({
        left: pagePositions.value[page],
        behavior: window.matchMedia('(prefers-reduced-motion: reduce)').matches ? 'instant' : 'smooth',
    });
};

const nextTrip = () => {
    if (! showAll.value) {
        moveToPage((currentPage.value + 1) % pagePositions.value.length);
    }
};

const pauseAutoplay = () => window.clearInterval(autoplayTimer);
const startAutoplay = () => {
    pauseAutoplay();
    if (! isHovered && ! hasFocus && ! showAll.value && ! window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
        autoplayTimer = window.setInterval(nextTrip, 4500);
    }
};

const toggleAll = async () => {
    showAll.value = ! showAll.value;
    await nextTick();
    updatePagination();
    startAutoplay();
};

onMounted(() => {
    updatePagination();
    resizeObserver = new ResizeObserver(updatePagination);
    resizeObserver.observe(carousel.value);
    startAutoplay();
});
onBeforeUnmount(() => {
    pauseAutoplay();
    resizeObserver?.disconnect();
});
</script>

<template>
    <section class="mx-auto mt-20 max-w-[1180px] sm:mt-24" aria-labelledby="partner-trips-heading">
        <div class="relative isolate overflow-hidden rounded-2xl bg-[#19375f]" @mouseenter="isHovered = true; pauseAutoplay()" @mouseleave="isHovered = false; startAutoplay()" @focusin="hasFocus = true; pauseAutoplay()" @focusout="hasFocus = $event.currentTarget.contains($event.relatedTarget); startAutoplay()">
            <img :src="trips[0].image" alt="" class="absolute inset-0 -z-20 size-full object-cover" loading="lazy" />
            <div class="absolute inset-0 -z-10 bg-[linear-gradient(90deg,rgba(13,43,88,0.94),rgba(32,52,76,0.82)_48%,rgba(108,117,117,0.83))]"></div>
            <div class="grid lg:grid-cols-[44.5%_minmax(0,1fr)]">
                <div class="flex flex-col items-start px-7 py-8 text-white">
                    <h2 id="partner-trips-heading" class="text-xl font-extrabold leading-tight">Open Trip Dari Partner Terpercaya</h2>
                    <p class="mt-2 max-w-[390px] text-sm font-semibold leading-tight">Nikmati perjalanan seru dengan vendor pilihan yang<br class="hidden xl:block" /> telah melalui proses verifikasi dan KYC dari Tapak Lokal</p>
                    <div class="mt-10 flex items-center gap-2 rounded-md bg-white px-3 py-2 text-[#ed1728]">
                        <Compass class="size-7" aria-hidden="true" />
                        <div><p class="text-sm font-extrabold leading-none">BRENGGO<span class="text-slate-800">.ID</span></p><p class="mt-0.5 text-[6px] leading-none text-slate-700">Urusan Asik Bersama Kami</p></div>
                    </div>
                    <button type="button" class="mt-6 inline-flex min-h-9 items-center gap-3 rounded-md bg-white/20 px-4 py-2 text-xs font-medium transition hover:bg-white/35 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-white" :aria-expanded="showAll" aria-controls="partner-trip-list" @click="toggleAll">{{ showAll ? 'Tampilkan Carousel' : 'Lihat Semua Trip Dari Partner' }}<ChevronRight class="size-4" aria-hidden="true" /></button>
                </div>
                <div class="relative min-w-0 py-4 pl-5 pr-16 lg:pl-0 lg:pr-[13%]">
                    <div id="partner-trip-list" ref="carousel" :class="showAll ? 'grid grid-cols-1 gap-3.5 sm:grid-cols-2' : 'flex snap-x snap-mandatory gap-3.5 overflow-x-auto [scrollbar-width:none] [&::-webkit-scrollbar]:hidden'" aria-label="Pilihan open trip partner" @scroll="updateCurrentPage">
                        <button v-for="trip in trips" :key="trip.id" type="button" class="group flex shrink-0 snap-start flex-col overflow-hidden rounded-xl bg-white text-left text-[#46535c] outline-none transition hover:shadow-lg focus-visible:ring-2 focus-visible:ring-inset focus-visible:ring-[#20a0ff]" :class="showAll ? 'w-full' : 'w-[180px] lg:w-[calc((100%-28px)/3)]'" :aria-label="`Pilih ${trip.name}, ${trip.price}`" @click="$emit('select', trip.destination)">
                            <div class="aspect-[155/105] w-full overflow-hidden"><img :src="trip.image" :alt="trip.destination" loading="lazy" class="size-full object-cover transition-transform duration-500 group-hover:scale-105 motion-reduce:transition-none" /></div>
                            <div class="flex min-h-[108px] w-full flex-1 flex-col p-2.5">
                                <h3 class="text-xs font-extrabold leading-tight">{{ trip.name }}</h3>
                                <p class="mt-1 text-[8px]">{{ trip.location }} · {{ trip.duration }}</p>
                                <p class="mt-2 text-xs font-extrabold">{{ trip.price }}</p>
                                <div class="mt-auto flex items-center justify-between gap-1 pt-6">
                                    <span class="inline-flex items-center gap-1 text-[9px] font-extrabold text-[#ed1728]"><Compass class="size-3" aria-hidden="true" /><span>BRENGGO<span class="text-slate-700">.ID</span></span></span>
                                    <span class="inline-flex items-center gap-0.5 text-[8px] font-semibold"><Star class="size-3 fill-[#ffd43b] text-[#ffd43b]" aria-hidden="true" />4.8/5</span>
                                </div>
                            </div>
                        </button>
                    </div>
                    <nav v-if="! showAll && pagePositions.length > 1" class="mt-3 flex justify-center gap-1" aria-label="Halaman trip partner">
                        <button v-for="(position, page) in pagePositions" :key="page" type="button" class="grid min-h-7 min-w-7 place-items-center rounded-full focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-white" :aria-label="`Tampilkan halaman trip ${page + 1}`" :aria-current="currentPage === page ? 'page' : undefined" aria-controls="partner-trip-list" @click="moveToPage(page)">
                            <span class="h-2 rounded-full transition-all motion-reduce:transition-none" :class="currentPage === page ? 'w-6 bg-white' : 'w-2 bg-white/45 hover:bg-white/75'"></span>
                        </button>
                    </nav>
                    <button v-if="! showAll" type="button" class="absolute right-3 top-1/2 grid size-10 -translate-y-1/2 place-items-center rounded-full bg-white text-[#175a9f] shadow-md transition hover:bg-sky-100 focus-visible:outline-2 focus-visible:outline-offset-4 focus-visible:outline-white lg:right-[3%]" aria-label="Trip partner berikutnya" @click="nextTrip"><ArrowRight class="size-5" aria-hidden="true" /></button>
                </div>
            </div>
        </div>
    </section>
</template>