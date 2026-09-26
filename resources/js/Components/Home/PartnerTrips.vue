<script setup>
import { ArrowLeft, ArrowRight, ChevronRight, Star } from 'lucide-vue-next';
import { Link } from '@inertiajs/vue3';
import { route } from 'ziggy-js';
import { computed, onBeforeUnmount, onMounted, ref } from 'vue';
import SkeletonBox from '../Skeletons/Base/SkeletonBox.vue';

const props = defineProps({
    items: { type: Array, default: () => [] },
    isLoading: {
        type: Boolean,
        default: false,
    },
});

defineEmits(['select']);

const carouselRef = ref(null);
const currentPage = ref(0);
const totalPages = ref(1);
let resizeObserver = null;
let autoplayTimer = null;
let isHovered = false;

const formatDuration = (startStr, endStr) => {
    if (!startStr || !endStr) return '3 hari 2 malam';
    try {
        const start = new Date(startStr);
        const end = new Date(endStr);
        const diffTime = Math.abs(end - start);
        const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24)) + 1;
        if (diffDays <= 1) return '1 hari';
        const nights = Math.max(1, diffDays - 1);
        return `${diffDays} hari ${nights} malam`;
    } catch {
        return '3 hari 2 malam';
    }
};

const formatPrice = (value) => {
    if (!value && value !== 0) return 'Rp350.000';
    return new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: 'IDR',
        maximumFractionDigits: 0,
    }).format(value).replace(/\s/g, '');
};

const defaultTrips = [
    {
        id: 1,
        slug: 'open-trip-pulau-komodo',
        type: 'open-trip',
        name: 'Open Trip pulau komodo',
        location: 'Malang',
        duration: '3 hari 2 malam',
        price: 'Rp350.000',
        rating: '4.8/5',
        vendor: { name: 'BRENGGO.ID' },
        image: 'https://images.unsplash.com/photo-1518548419970-58e3b4079ab2?auto=format&fit=crop&w=800&q=85',
    },
    {
        id: 2,
        slug: 'open-trip-bromo',
        type: 'open-trip',
        name: 'Open Trip Bromo',
        location: 'Malang',
        duration: '3 hari 2 malam',
        price: 'Rp350.000',
        rating: '4.8/5',
        vendor: { name: 'BRENGGO.ID' },
        image: 'https://images.unsplash.com/photo-1544644181-1484b3fdfc62?auto=format&fit=crop&w=800&q=85',
    },
    {
        id: 3,
        slug: 'open-raja-ampat',
        type: 'open-trip',
        name: 'Open Raja Ampat',
        location: 'Papua',
        duration: '6 hari 5 malam',
        price: 'Rp15.000.000',
        rating: '4.8/5',
        vendor: { name: 'BRENGGO.ID' },
        image: '/Assets/Images/logo-vendor/a6f6f78acd99081647225f8a7a9d6369.jpg',
    },
];

const trips = computed(() => {
    if (!props.items || props.items.length === 0) {
        return defaultTrips;
    }
    return props.items.map((trip) => ({
        id: trip.id,
        slug: trip.slug,
        type: trip.type || 'open-trip',
        name: trip.title || trip.name,
        location: trip.destination || 'Malang',
        duration: formatDuration(trip.departure_date, trip.end_date),
        price: formatPrice(trip.selling_price || trip.price),
        rating: trip.reviews_avg_rating ? `${Number(trip.reviews_avg_rating).toFixed(1)}/5` : '4.8/5',
        vendor: { name: trip.vendor?.name || 'BRENGGO.ID' },
        image: trip.image_url || trip.image || 'https://images.unsplash.com/photo-1518548419970-58e3b4079ab2?auto=format&fit=crop&w=800&q=85',
    }));
});

const calculatePagination = () => {
    const el = carouselRef.value;
    if (!el) return;
    const maxScroll = Math.max(0, el.scrollWidth - el.clientWidth);
    if (maxScroll <= 5) {
        totalPages.value = 1;
        currentPage.value = 0;
        return;
    }
    const firstChild = el.firstElementChild;
    const cardWidth = firstChild ? firstChild.getBoundingClientRect().width : (el.clientWidth / 3);
    const stride = cardWidth + 14;
    const count = Math.max(1, Math.ceil(maxScroll / stride) + 1);
    totalPages.value = count;
    currentPage.value = Math.min(count - 1, Math.max(0, Math.round(el.scrollLeft / stride)));
};

const goToPage = (page) => {
    const el = carouselRef.value;
    if (!el) return;
    const firstChild = el.firstElementChild;
    const cardWidth = firstChild ? firstChild.getBoundingClientRect().width : (el.clientWidth / 3);
    const stride = cardWidth + 14;
    el.scrollTo({
        left: page * stride,
        behavior: window.matchMedia('(prefers-reduced-motion: reduce)').matches ? 'instant' : 'smooth',
    });
    currentPage.value = page;
};

const prevPage = () => {
    if (currentPage.value > 0) {
        goToPage(currentPage.value - 1);
    } else {
        goToPage(totalPages.value - 1);
    }
};

const nextPage = () => {
    if (currentPage.value < totalPages.value - 1) {
        goToPage(currentPage.value + 1);
    } else {
        goToPage(0);
    }
};

const onScroll = () => {
    const el = carouselRef.value;
    if (!el) return;
    const firstChild = el.firstElementChild;
    const cardWidth = firstChild ? firstChild.getBoundingClientRect().width : (el.clientWidth / 3);
    const stride = cardWidth + 14;
    currentPage.value = Math.min(totalPages.value - 1, Math.max(0, Math.round(el.scrollLeft / stride)));
};

const pauseAutoplay = () => window.clearInterval(autoplayTimer);
const startAutoplay = () => {
    pauseAutoplay();
    if (!isHovered && totalPages.value > 1 && !window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
        autoplayTimer = window.setInterval(() => {
            nextPage();
        }, 5000);
    }
};

onMounted(() => {
    calculatePagination();
    if (carouselRef.value) {
        resizeObserver = new ResizeObserver(calculatePagination);
        resizeObserver.observe(carouselRef.value);
    }
    startAutoplay();
});

onBeforeUnmount(() => {
    pauseAutoplay();
    resizeObserver?.disconnect();
});
</script>

<template>
    <section
        class="mx-auto mt-10 sm:mt-12 lg:mt-14 max-w-[1180px]"
        aria-labelledby="partner-trips-heading"
        :aria-busy="isLoading"
    >
        <!-- Skeleton State -->
        <div v-if="isLoading" class="relative overflow-hidden rounded-3xl bg-[#1c2e4a] p-6 lg:p-9" aria-hidden="true">
            <div class="grid grid-cols-1 lg:grid-cols-[38%_minmax(0,1fr)] gap-8 items-center">
                <div class="space-y-4 text-white">
                    <div class="h-8 w-3/4 rounded-lg bg-white/30 skeleton-shimmer"></div>
                    <div class="space-y-2 pt-2">
                        <div class="h-4 w-full rounded-md bg-white/20 skeleton-shimmer"></div>
                        <div class="h-4 w-4/5 rounded-md bg-white/20 skeleton-shimmer"></div>
                    </div>
                    <div class="h-12 w-48 rounded-xl bg-white/30 skeleton-shimmer mt-6"></div>
                    <div class="h-9 w-44 rounded-lg bg-white/20 skeleton-shimmer mt-4"></div>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3.5">
                    <div v-for="n in 3" :key="n" class="rounded-2xl bg-white p-3 space-y-3 shadow-md">
                        <SkeletonBox height="115px" rounded="rounded-xl" />
                        <SkeletonBox width="80%" height="14px" rounded="rounded-md" />
                        <SkeletonBox width="50%" height="11px" rounded="rounded-md" />
                        <SkeletonBox width="60px" height="15px" rounded="rounded-md" />
                    </div>
                </div>
            </div>
        </div>

        <!-- Real 1:1 Design State -->
        <div
            v-else
            class="relative isolate overflow-hidden rounded-3xl bg-[#192b42] p-6 sm:p-8 lg:p-9 shadow-[0_16px_40px_rgba(15,35,65,0.22)]"
            @mouseenter="isHovered = true; pauseAutoplay()"
            @mouseleave="isHovered = false; startAutoplay()"
        >
            <!-- Background scenery image & backdrop gradient -->
            <img
                src="/Assets/Images/logo-vendor/a6f6f78acd99081647225f8a7a9d6369.jpg"
                alt=""
                class="absolute inset-0 -z-20 size-full object-cover object-center"
                loading="lazy"
            />
            <div class="absolute inset-0 -z-10 bg-gradient-to-r from-[#172c47]/95 via-[#1d3554]/92 to-[#22446d]/80 backdrop-blur-[2px]"></div>

            <div class="grid grid-cols-1 lg:grid-cols-[36%_minmax(0,1fr)] gap-8 items-stretch">
                <!-- Left Column: Header, Description, Vendor Badge, CTA Link -->
                <div class="flex flex-col justify-between text-white py-1">
                    <div>
                        <h2
                            id="partner-trips-heading"
                            class="text-2xl sm:text-[28px] lg:text-[30px] font-black leading-tight tracking-tight text-white"
                        >
                            Open Trip Dari Partner Terpercaya
                        </h2>
                        <p class="mt-3 text-xs sm:text-sm font-medium leading-relaxed text-white/80 max-w-sm">
                            Nikmati perjalanan seru dengan vendor pilihan yang telah melalui proses verifikasi dan KYC dari Tapak Lokal
                        </p>
                    </div>

                    <div class="mt-8 space-y-4">
                        <!-- Vendor Badge Card matching 1:1 design -->
                        <div class="inline-flex items-center gap-3 rounded-xl bg-white px-4 py-2.5 shadow-lg max-w-[230px]">
                            <img
                                src="/Assets/Images/logo-vendor/logo-brenggo-tour.jpg"
                                alt="Logo BRENGGO.ID"
                                class="h-9 w-9 rounded-md object-contain shrink-0"
                            />
                            <div class="min-w-0">
                                <p class="text-sm font-black uppercase tracking-wider text-slate-900 leading-none">
                                    BRENGGO<span class="text-[#dc2626]">.ID</span>
                                </p>
                                <p class="mt-1 text-[9px] font-semibold text-slate-500 leading-tight truncate">
                                    Liburan Asik Bersama Kami
                                </p>
                            </div>
                        </div>

                        <!-- Pill Button -->
                        <div>
                            <Link
                                :href="route('catalog', { type: 'open-trip' })"
                                class="group inline-flex items-center gap-1.5 rounded-lg bg-slate-700/60 px-4 py-2 text-xs font-semibold text-white/90 backdrop-blur-md transition duration-200 hover:bg-slate-700/90 hover:text-white focus-visible:outline-2 focus-visible:outline-white"
                            >
                                <span>Lihat Semua Trip Dari Partner</span>
                                <ChevronRight class="size-3.5 transition-transform group-hover:translate-x-0.5" />
                            </Link>
                        </div>
                    </div>
                </div>

                <!-- Right Column: 3-Card Carousel + Bottom-Right Navigation Controls -->
                <div class="relative min-w-0 flex flex-col justify-between">
                    <!-- Cards Carousel -->
                    <div
                        id="partner-trip-list"
                        ref="carouselRef"
                        class="flex snap-x snap-mandatory gap-3.5 sm:gap-4 overflow-x-auto scroll-smooth pb-1 pt-0.5 [scrollbar-width:none] [&::-webkit-scrollbar]:hidden"
                        aria-label="Pilihan open trip partner"
                        @scroll="onScroll"
                    >
                        <Link
                            v-for="trip in trips"
                            :key="trip.id"
                            :href="route('trips.show', { tripType: trip.type, trip: trip.slug })"
                            class="group flex w-[230px] sm:w-[250px] lg:w-[calc((100%-28px)/3)] shrink-0 snap-start flex-col overflow-hidden rounded-2xl bg-white text-left shadow-lg transition-all duration-300 hover:-translate-y-1 hover:shadow-2xl focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-white"
                            :aria-label="`Pilih ${trip.name}, ${trip.price}`"
                        >
                            <!-- Card Image -->
                            <div class="relative aspect-[16/10] w-full overflow-hidden bg-slate-100">
                                <img
                                    :src="trip.image"
                                    :alt="trip.name"
                                    class="size-full object-cover transition-transform duration-500 group-hover:scale-105 motion-reduce:transition-none"
                                    loading="lazy"
                                />
                            </div>

                            <!-- Card Body -->
                            <div class="flex flex-1 flex-col p-3.5 sm:p-4">
                                <h3 class="text-xs sm:text-sm font-extrabold text-slate-900 line-clamp-1 group-hover:text-[#078cff] transition-colors">
                                    {{ trip.name }}
                                </h3>
                                <p class="mt-1 text-[10px] sm:text-[11px] font-medium text-slate-500">
                                    {{ trip.location }} &bull; {{ trip.duration }}
                                </p>
                                <p class="mt-2 text-xs sm:text-sm font-extrabold text-slate-900">
                                    {{ trip.price }}
                                </p>

                                <!-- Card Footer: Vendor Logo + Star Rating -->
                                <div class="mt-auto flex items-center justify-between border-t border-slate-100 pt-3 text-[10px]">
                                    <div class="flex items-center gap-1.5 font-bold uppercase tracking-wide text-[#dc2626]">
                                        <img
                                            src="/Assets/Images/logo-vendor/logo-brenggo-tour.jpg"
                                            alt=""
                                            class="size-3.5 rounded-full object-contain"
                                        />
                                        <span>{{ trip.vendor.name }}</span>
                                    </div>
                                    <div class="flex items-center gap-1 font-bold text-slate-700">
                                        <Star class="size-3.5 fill-[#facc15] text-[#facc15]" aria-hidden="true" />
                                        <span>{{ trip.rating }}</span>
                                    </div>
                                </div>
                            </div>
                        </Link>
                    </div>

                    <!-- Bottom Right Controls: Pagination Dots + Navigation Arrows -->
                    <div class="mt-4 flex items-center justify-between sm:justify-end gap-3.5 pt-1">
                        <!-- Pagination Indicators -->
                        <div v-if="totalPages > 1" class="flex items-center gap-1.5" aria-label="Halaman trip">
                            <button
                                v-for="(_, page) in totalPages"
                                :key="page"
                                type="button"
                                class="h-2 rounded-full transition-all duration-300 focus-visible:outline-none"
                                :class="currentPage === page ? 'w-6 bg-white shadow-sm' : 'w-2 bg-white/40 hover:bg-white/70'"
                                :aria-label="`Pergi ke slide ${page + 1}`"
                                @click="goToPage(page)"
                            />
                        </div>

                        <!-- Left & Right Arrow Buttons -->
                        <div v-if="totalPages > 1" class="flex items-center gap-2">
                            <button
                                type="button"
                                class="grid size-8 place-items-center rounded-full bg-white/20 text-white backdrop-blur-sm transition duration-200 hover:bg-white hover:text-slate-900 active:scale-95 disabled:opacity-30 disabled:cursor-not-allowed focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-white"
                                :disabled="currentPage === 0"
                                aria-label="Trip sebelumnya"
                                @click="prevPage"
                            >
                                <ArrowLeft class="size-4" />
                            </button>
                            <button
                                type="button"
                                class="grid size-8 place-items-center rounded-full bg-white/20 text-white backdrop-blur-sm transition duration-200 hover:bg-white hover:text-slate-900 active:scale-95 disabled:opacity-30 disabled:cursor-not-allowed focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-white"
                                :disabled="currentPage >= totalPages - 1"
                                aria-label="Trip berikutnya"
                                @click="nextPage"
                            >
                                <ArrowRight class="size-4" />
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</template>
