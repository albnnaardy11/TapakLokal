<script setup>
import { ArrowRight, ChevronLeft, ChevronRight, Clock, Star } from 'lucide-vue-next';
import { Link } from '@inertiajs/vue3';
import { computed, onBeforeUnmount, onMounted, ref } from 'vue';

const carousel = ref(null);
const currentPage = ref(0);
let autoplayTimer = null;
let countdownTimer = null;

// Countdown timer state (hours, minutes, seconds)
const timeLeft = ref({
    hours: 11,
    minutes: 42,
    seconds: 35,
});

const formatTwoDigits = (num) => String(num).padStart(2, '0');

const tickCountdown = () => {
    if (timeLeft.value.seconds > 0) {
        timeLeft.value.seconds--;
    } else {
        timeLeft.value.seconds = 59;
        if (timeLeft.value.minutes > 0) {
            timeLeft.value.minutes--;
        } else {
            timeLeft.value.minutes = 59;
            if (timeLeft.value.hours > 0) {
                timeLeft.value.hours--;
            } else {
                // reset to 12 hours for continuous promo demo
                timeLeft.value.hours = 12;
            }
        }
    }
};

const tripProducts = [
    {
        id: 1,
        title: 'Open Trip Bromo Sunrise & Savana',
        category: 'Open Trip',
        location: 'Malang, Jawa Timur',
        duration: '3H 2M',
        slotsLeft: 'Sisa 3 Kursi',
        price: 'Rp 350.000',
        originalPrice: 'Rp 550.000',
        discount: '36%',
        rating: '4.9',
        type: 'open-trip',
        slug: 'open-trip-bromo',
        image: 'https://images.unsplash.com/photo-1544644181-1484b3fdfc62?auto=format&fit=crop&w=640&q=85',
    },
    {
        id: 2,
        title: 'Open Trip Labuan Bajo & Komodo',
        category: 'Open Trip',
        location: 'Labuan Bajo, NTT',
        duration: '3H 2M',
        slotsLeft: 'Sisa 2 Kursi',
        price: 'Rp 1.850.000',
        originalPrice: 'Rp 2.450.000',
        discount: '25%',
        rating: '4.95',
        type: 'open-trip',
        slug: 'open-trip-pulau-komodo',
        image: 'https://images.unsplash.com/photo-1518548419970-58e3b4079ab2?auto=format&fit=crop&w=640&q=85',
    },
    {
        id: 3,
        title: 'Private Trip Nusa Penida & Snorkeling',
        category: 'Private Trip',
        location: 'Nusa Penida, Bali',
        duration: '2H 1M',
        slotsLeft: 'Promo Eksklusif',
        price: 'Rp 650.000',
        originalPrice: 'Rp 950.000',
        discount: '32%',
        rating: '4.85',
        type: 'private-trip',
        slug: 'private-trip-nusa-penida',
        image: 'https://images.unsplash.com/photo-1537996194471-e657df975ab4?auto=format&fit=crop&w=640&q=85',
    },
    {
        id: 4,
        title: 'Open Trip Dieng Golden Sunrise',
        category: 'Open Trip',
        location: 'Wonosobo, Jawa Tengah',
        duration: '2H 1M',
        slotsLeft: 'Sisa 4 Kursi',
        price: 'Rp 420.000',
        originalPrice: 'Rp 600.000',
        discount: '30%',
        rating: '4.8',
        type: 'open-trip',
        slug: 'open-trip-bromo',
        image: 'https://images.unsplash.com/photo-1506744038136-46273834b3fb?auto=format&fit=crop&w=640&q=85',
    },
    {
        id: 5,
        title: 'Open Trip Derawan & Maratua Island',
        category: 'Open Trip',
        location: 'Berau, Kalimantan Timur',
        duration: '4H 3M',
        slotsLeft: 'Sisa 2 Kursi',
        price: 'Rp 2.100.000',
        originalPrice: 'Rp 2.800.000',
        discount: '25%',
        rating: '4.9',
        type: 'open-trip',
        slug: 'open-raja-ampat',
        image: 'https://images.unsplash.com/photo-1507525428034-b723cf961d3e?auto=format&fit=crop&w=640&q=85',
    },
    {
        id: 6,
        title: 'Private Trip Pahawang Snorkeling',
        category: 'Private Trip',
        location: 'Pesawaran, Lampung',
        duration: '2H 1M',
        slotsLeft: 'Sisa 5 Kursi',
        price: 'Rp 490.000',
        originalPrice: 'Rp 700.000',
        discount: '30%',
        rating: '4.85',
        type: 'private-trip',
        slug: 'open-trip-bromo',
        image: 'https://images.unsplash.com/photo-1544551763-46a013bb70d5?auto=format&fit=crop&w=640&q=85',
    },
];

const pageCount = computed(() => {
    // 6 items total, each page scrolls by 2-3 items
    return 3;
});
const pagePositions = computed(() => Array.from({ length: pageCount.value }, (_, index) => index));

const moveToPage = (page) => {
    if (!carousel.value) return;
    const maxScroll = carousel.value.scrollWidth - carousel.value.clientWidth;
    if (maxScroll <= 0) return;
    currentPage.value = page;
    carousel.value.scrollTo({
        left: (maxScroll / (pageCount.value - 1)) * page,
        behavior: 'smooth',
    });
};

const nextPage = () => moveToPage((currentPage.value + 1) % pageCount.value);
const prevPage = () => moveToPage((currentPage.value - 1 + pageCount.value) % pageCount.value);

const updateCurrentPage = () => {
    if (!carousel.value) return;
    const maxScroll = carousel.value.scrollWidth - carousel.value.clientWidth;
    if (maxScroll <= 0) {
        currentPage.value = 0;
        return;
    }
    currentPage.value = Math.round((carousel.value.scrollLeft / maxScroll) * (pageCount.value - 1));
};

const startAutoplay = () => {
    stopAutoplay();
    autoplayTimer = window.setInterval(() => {
        nextPage();
    }, 4500);
};

const stopAutoplay = () => {
    if (autoplayTimer) {
        window.clearInterval(autoplayTimer);
        autoplayTimer = null;
    }
};

const getTripUrl = (trip) => {
    try {
        if (typeof route === 'function') {
            return route('trips.show', { tripType: trip.type, trip: trip.slug });
        }
    } catch {
        // Fallback
    }
    return typeof route === 'function' ? route('catalog', { type: trip.type }) : '/jelajah';
};

onMounted(() => {
    startAutoplay();
    countdownTimer = window.setInterval(tickCountdown, 1000);
});

onBeforeUnmount(() => {
    stopAutoplay();
    if (countdownTimer) {
        window.clearInterval(countdownTimer);
        countdownTimer = null;
    }
});
</script>

<template>
    <section class="mx-auto mt-16 max-w-[1180px] sm:mt-20">
        <div class="relative isolate overflow-hidden rounded-[24px] bg-[#2b70d1] shadow-[0_12px_28px_rgba(22,53,102,0.16)]">
            <!-- Background Scenery Image (Breathtaking Indonesian Nature Landscape) -->
            <img
                src="https://images.unsplash.com/photo-1518548419970-58e3b4079ab2?auto=format&fit=crop&w=1920&q=85"
                alt="Pemandangan Eksotis Trip Indonesia"
                class="absolute inset-0 -z-20 size-full object-cover opacity-50"
            />
            <div class="absolute inset-0 -z-10 bg-[linear-gradient(90deg,rgba(22,88,187,0.96)_0%,rgba(38,107,204,0.90)_33%,rgba(91,154,232,0.52)_66%,rgba(171,207,248,0.28)_100%)]"></div>

            <div class="relative grid lg:grid-cols-[335px_minmax(0,1fr)]">
                <!-- Left Banner: Flash Sale Badge, Countdown Timer, Title & CTA -->
                <div class="flex flex-col justify-center px-6 py-7 text-white sm:px-8 sm:py-8">
                    <!-- Original Flash Lightning Icon Badge -->
                    <div class="flex items-center gap-2.5 text-base font-extrabold tracking-wide text-white sm:text-lg">
                        <span class="grid size-7.5 shrink-0 place-items-center rounded-lg bg-white/15 shadow-sm">
                            <svg viewBox="0 0 64 88" class="h-5 w-3.5" aria-hidden="true">
                                <defs>
                                    <radialGradient id="flash-sale-lightning-fill" cx="48%" cy="48%" r="58%">
                                        <stop offset="0%" stop-color="#d8f5ff" />
                                        <stop offset="35%" stop-color="#8fd5ff" />
                                        <stop offset="100%" stop-color="#2575d4" />
                                    </radialGradient>
                                </defs>
                                <path
                                    d="M39.5 3 6.5 51.2a5.2 5.2 0 0 0 4.3 8.1h17.5l-2.7 23.2c-.6 5.2 6.1 7.2 8.3 2.5l25.2-48.8a5.2 5.2 0 0 0-4.7-7.6H37.3l4.1-20.1C42.4 3.7 42.2 1.3 39.5 3Z"
                                    fill="url(#flash-sale-lightning-fill)"
                                    stroke="#16a9f4"
                                    stroke-linejoin="round"
                                    stroke-width="3.8"
                                />
                            </svg>
                        </span>
                        FLASH SALE TRIP
                    </div>

                    <!-- Live Countdown Timer -->
                    <div class="mt-3.5 inline-flex w-fit items-center gap-2 rounded-xl bg-white/15 px-3 py-1 text-xs font-semibold text-white backdrop-blur-md border border-white/20">
                        <span class="text-white/90">Berakhir dalam:</span>
                        <div class="flex items-center gap-1 font-mono text-xs font-bold text-white">
                            <span class="rounded bg-white/25 px-1.5 py-0.5">{{ formatTwoDigits(timeLeft.hours) }}</span>
                            <span>:</span>
                            <span class="rounded bg-white/25 px-1.5 py-0.5">{{ formatTwoDigits(timeLeft.minutes) }}</span>
                            <span>:</span>
                            <span class="rounded bg-[#ef3037] px-1.5 py-0.5 text-white shadow-sm">{{ formatTwoDigits(timeLeft.seconds) }}</span>
                        </div>
                    </div>

                    <!-- Headline & Description -->
                    <h2 class="mt-4 text-xl font-extrabold leading-tight tracking-tight sm:text-2xl">
                        Paket Trip Pilihan Harga Spesial!
                    </h2>
                    <p class="mt-2.5 text-xs leading-relaxed text-white/90 sm:text-sm">
                        Diskon liburan terbatas hingga 36% untuk Open Trip dan Private Trip pilihan dengan kuota promo terbatas.
                    </p>

                    <!-- CTA Link (Directly below description) -->
                    <div class="mt-5">
                        <Link
                            :href="typeof route === 'function' ? route('catalog') : '/jelajah'"
                            class="inline-flex items-center gap-2 rounded-lg bg-white px-4 py-2.5 text-xs font-bold text-[#175a9f] shadow-md transition duration-200 hover:bg-[#e9f1ff] active:scale-95 sm:text-sm"
                        >
                            <span>Lihat Semua Promo Trip</span>
                            <ArrowRight class="size-3.5 sm:size-4" />
                        </Link>
                    </div>
                </div>

                <!-- Right Column: Interactive Trip Cards Carousel (Fits 3 Full Cards) -->
                <div
                    class="relative min-w-0 bg-white/[0.04] px-3 pb-13 pt-5 sm:px-5 sm:pb-14"
                    @mouseenter="stopAutoplay"
                    @mouseleave="startAutoplay"
                >
                    <div
                        ref="carousel"
                        class="flex snap-x snap-mandatory gap-2.5 overflow-x-auto scroll-smooth pb-3 pt-1.5 [scrollbar-width:none] [&::-webkit-scrollbar]:hidden"
                        @scroll="updateCurrentPage"
                    >
                        <div
                            v-for="trip in tripProducts"
                            :key="trip.id"
                            class="shrink-0 snap-start pl-2 pr-0.5 pt-1 pb-1"
                        >
                            <article
                                class="group relative flex h-[315px] w-[212px] flex-col justify-between rounded-[16px] bg-white shadow-[0_6px_18px_rgba(15,45,95,0.16)] transition-all duration-300 hover:-translate-y-1 hover:shadow-[0_12px_24px_rgba(15,45,95,0.22)]"
                            >
                                <!-- 1:1 Red Bookmark Ribbon with Fold Triangle (100% Guaranteed Unclipped) -->
                                <div class="absolute top-3.5 -left-2 z-20 flex items-center pointer-events-none drop-shadow-md">
                                    <div class="relative flex h-[22px] min-w-[46px] items-center justify-center rounded-r-[7px] bg-[#e52335] px-2">
                                        <span class="text-[11px] font-black tracking-tight text-white leading-none">
                                            {{ trip.discount }}
                                        </span>
                                        <!-- Ribbon Fold Triangle Underneath Left Edge -->
                                        <span class="absolute -bottom-[7px] left-0 size-0 border-t-[7px] border-t-[#8f121d] border-l-[7px] border-l-transparent"></span>
                                    </div>
                                </div>

                                <!-- Trip Card Image -->
                                <div class="relative h-[142px] w-full overflow-hidden rounded-t-[16px] bg-slate-100">
                                    <img
                                        :src="trip.image"
                                        :alt="trip.title"
                                        loading="lazy"
                                        class="size-full object-cover transition duration-500 group-hover:scale-105"
                                    />
                                </div>

                                <!-- Trip Card Content -->
                                <div class="flex flex-1 flex-col justify-between p-3 text-slate-700">
                                    <div>
                                        <!-- Duration & Slots Left -->
                                        <div class="flex items-center justify-between text-[9.5px] font-semibold text-slate-500">
                                            <div class="flex items-center gap-1">
                                                <Clock class="size-3 text-slate-400" />
                                                <span>{{ trip.duration }}</span>
                                            </div>
                                            <span class="rounded bg-rose-50 px-1.5 py-0.5 font-bold text-rose-600">
                                                {{ trip.slotsLeft }}
                                            </span>
                                        </div>

                                        <!-- Trip Title -->
                                        <h3 class="mt-1 line-clamp-2 text-[12px] font-bold leading-snug text-slate-900 group-hover:text-blue-600 transition-colors">
                                            {{ trip.title }}
                                        </h3>
                                    </div>

                                    <!-- Pricing & Booking Button -->
                                    <div class="mt-2 border-t border-slate-100 pt-2">
                                        <div class="flex items-baseline justify-between">
                                            <span class="text-[9.5px] font-medium text-slate-400">Harga Flash Sale</span>
                                            <div class="flex items-center gap-1 text-[9.5px] text-amber-500">
                                                <Star class="size-2.5 fill-amber-400 text-amber-400" />
                                                <span class="font-bold text-slate-700">{{ trip.rating }}</span>
                                            </div>
                                        </div>

                                        <div class="mt-0.5 flex items-center justify-between gap-1">
                                            <div>
                                                <p class="text-[13px] font-extrabold leading-none text-[#ef3037]">
                                                    {{ trip.price }}
                                                </p>
                                                <p class="mt-0.5 text-[9.5px] text-slate-400 line-through">
                                                    {{ trip.originalPrice }}
                                                </p>
                                            </div>

                                            <Link
                                                :href="getTripUrl(trip)"
                                                class="inline-flex items-center gap-1 rounded-full bg-[#1875d1] px-2.5 py-1 text-[10px] font-bold text-white shadow-sm transition hover:bg-[#125ca7] active:scale-95"
                                            >
                                                <span>Pesan</span>
                                                <ArrowRight class="size-2.5" />
                                            </Link>
                                        </div>
                                    </div>
                                </div>
                            </article>
                        </div>
                    </div>

                    <!-- Carousel Dots & Navigation Controls (Bottom Right) -->
                    <div class="absolute bottom-4 right-5 z-10 flex items-center gap-3 sm:right-7">
                        <!-- Dots indicator -->
                        <div class="flex items-center gap-1.5" aria-label="Halaman carousel trip">
                            <button
                                v-for="page in pagePositions"
                                :key="page"
                                type="button"
                                class="h-2 rounded-full transition-all duration-300"
                                :class="currentPage === page ? 'w-6 bg-white' : 'w-2 bg-white/40 hover:bg-white/70'"
                                :aria-label="`Tampilkan halaman promo ${page + 1}`"
                                :aria-current="currentPage === page"
                                @click="moveToPage(page)"
                            ></button>
                        </div>

                        <!-- Prev / Next Navigation Buttons -->
                        <div class="flex items-center gap-1.5">
                            <button
                                type="button"
                                class="grid size-8 place-items-center rounded-full border border-white/50 bg-black/15 text-white backdrop-blur-sm transition hover:bg-white hover:text-[#175da8]"
                                aria-label="Promo trip sebelumnya"
                                @click="prevPage"
                            >
                                <ChevronLeft class="size-4" />
                            </button>
                            <button
                                type="button"
                                class="grid size-8 place-items-center rounded-full border border-white/50 bg-black/15 text-white backdrop-blur-sm transition hover:bg-white hover:text-[#175da8]"
                                aria-label="Promo trip berikutnya"
                                @click="nextPage"
                            >
                                <ChevronRight class="size-4" />
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</template>
