<script setup>
import { computed, onBeforeUnmount, onMounted, ref, watch } from 'vue';
import { router } from '@inertiajs/vue3';
import { route } from 'ziggy-js';
import {
    ArrowLeftRight,
    ArrowRight,
    CalendarDays,
    Check,
    ChevronDown,
    Clock,
    Compass,
    Crown,
    MapPin,
    Minus,
    Plus,
    Search,
    User,
    Users,
    X,
} from 'lucide-vue-next';

const props = defineProps({
    initialFilters: {
        type: Object,
        default: () => ({}),
    },
    partner: {
        type: Object,
        default: () => null,
    },
});

const emit = defineEmits(['search']);

// Search form states
const tripType = ref(props.initialFilters?.type || '');
const destination = ref(props.initialFilters?.q || '');
const meetingPoint = ref(props.initialFilters?.from || 'Semua Meeting Point');
const departureDate = ref(props.initialFilters?.date || '');
const duration = ref(props.initialFilters?.duration || '');

// Pax counter state
const adults = ref(Number(props.initialFilters?.guests) || 1);
const children = ref(0);

// Dropdowns tracker
const activeDropdown = ref(null);
const dateInputRef = ref(null);

const toggleDropdown = (name) => {
    activeDropdown.value = activeDropdown.value === name ? null : name;
};

const closeDropdowns = (e) => {
    if (!e.target.closest('[data-dropdown]')) {
        activeDropdown.value = null;
    }
};

onMounted(() => {
    window.addEventListener('click', closeDropdowns);
});

onBeforeUnmount(() => {
    window.removeEventListener('click', closeDropdowns);
});

watch(
    () => props.initialFilters,
    (newVal) => {
        if (newVal) {
            tripType.value = newVal.type || '';
            destination.value = newVal.q || '';
            departureDate.value = newVal.date || '';
            if (newVal.guests) {
                adults.value = Math.max(1, Number(newVal.guests));
            }
        }
    },
    { deep: true }
);

// Formatted display values
const totalGuestsLabel = computed(() => {
    const total = adults.value + children.value;
    if (children.value > 0) {
        return `${adults.value} Dewasa, ${children.value} Anak (${total} Pax)`;
    }
    return `${adults.value} Dewasa (${total} Pax)`;
});

const formattedDateDisplay = computed(() => {
    if (!departureDate.value) return '';
    try {
        const d = new Date(departureDate.value);
        return d.toLocaleDateString('id-ID', {
            day: 'numeric',
            month: 'short',
            year: 'numeric',
        });
    } catch {
        return departureDate.value;
    }
});

// Translucent Title Bar text
const bannerTitle = computed(() => {
    if (props.partner?.name) {
        return `Cari & Pesan Paket Trip ${props.partner.name}`;
    }
    if (tripType.value === 'open-trip') {
        return 'Cari & Pesan Paket Open Trip Murah & Seru di Indonesia';
    }
    if (tripType.value === 'private-trip') {
        return 'Cari & Booking Private Trip Eksklusif (Bebas Rute & Waktu)';
    }
    return 'Cari & Pesan Tiket Trip Wisata Terbaik di Indonesia';
});

// Swap Meeting Point and Destination
const swapLocations = () => {
    if (meetingPoint.value === 'Semua Meeting Point') {
        meetingPoint.value = destination.value || 'Jakarta';
        destination.value = '';
    } else {
        const temp = meetingPoint.value;
        meetingPoint.value = destination.value || 'Semua Meeting Point';
        destination.value = temp === 'Semua Meeting Point' ? '' : temp;
    }
};

const openDatePicker = () => {
    if (dateInputRef.value) {
        if (typeof dateInputRef.value.showPicker === 'function') {
            dateInputRef.value.showPicker();
        } else {
            dateInputRef.value.focus();
        }
    }
};

const handleSearch = () => {
    activeDropdown.value = null;
    const queryParams = {};

    if (destination.value.trim()) {
        queryParams.q = destination.value.trim();
    }
    if (tripType.value) {
        queryParams.type = tripType.value;
    }
    if (departureDate.value) {
        queryParams.date = departureDate.value;
    }
    const totalGuests = adults.value + children.value;
    if (totalGuests > 1) {
        queryParams.guests = totalGuests;
    }

    emit('search', queryParams);

    router.get(
        typeof route === 'function' ? route('catalog') : '/cari-trip',
        queryParams,
        {
            preserveState: true,
            preserveScroll: true,
        }
    );
};

// Popular destination quick picks
const popularDestinations = [
    'Labuan Bajo',
    'Bromo',
    'Raja Ampat',
    'Pulau Pramuka',
    'Bali',
    'Dieng',
    'Derawan',
];

const selectQuickDestination = (dest) => {
    destination.value = dest;
    handleSearch();
};
</script>

<template>
    <section class="relative w-full pt-1 sm:pt-2.5" aria-labelledby="catalog-search-heading">
        <div class="mx-auto max-w-[1180px] px-4 sm:px-6 lg:px-0">
            <!-- Daylight Sky Scenic Banner Container (Slightly more compact height) -->
            <div class="relative overflow-hidden rounded-[22px] sm:rounded-[26px] bg-[#d9effe] min-h-[210px] sm:min-h-[230px] md:min-h-[250px] shadow-[0_12px_32px_rgba(20,70,130,0.09)] border border-sky-100/80">
                <!-- Daylight Blue Sky with Soft White Clouds Background Image -->
                <img
                    src="https://images.unsplash.com/photo-1513002749550-c59d786b8e6c?auto=format&fit=crop&w=1920&q=85"
                    alt="Langit Biru Cerah Indonesia"
                    class="absolute inset-0 size-full object-cover object-center"
                    loading="eager"
                />
                <div class="absolute inset-0 bg-gradient-to-b from-sky-100/15 via-sky-200/20 to-sky-300/30"></div>

                <!-- Top Left: Vendor / Airline Wings Emblem -->
                <div class="relative z-10 px-6 pt-5 sm:px-8 sm:pt-6">
                    <div class="flex items-center gap-2.5">
                        <img
                            v-if="partner?.logo"
                            :src="partner.logo"
                            :alt="partner.name"
                            class="h-12 sm:h-14 md:h-16 max-w-[200px] w-auto object-contain filter drop-shadow-md"
                        />
                        <svg v-else class="h-10 sm:h-12 md:h-14 w-auto drop-shadow-md" viewBox="0 0 100 42" fill="none" xmlns="http://www.w3.org/2000/svg" aria-label="Logo Partner">
                            <path d="M4 14C16 14 34 8 52 16C37 17 23 20 12 25C27 21 44 22 58 30C41 28 29 32 18 38C35 33 52 36 70 42C80 38 90 32 96 24C90 26 80 26 72 22C84 20 94 14 98 8C88 12 76 13 64 11C76 8 86 3 90 0C76 4 60 7 44 8C30 9 17 11 4 14Z" fill="url(#wingGradientHero)" />
                            <defs>
                                <linearGradient id="wingGradientHero" x1="4" y1="21" x2="98" y2="21" gradientUnits="userSpaceOnUse">
                                    <stop stop-color="#245ec7" />
                                    <stop offset="0.6" stop-color="#3E7BEF" />
                                    <stop offset="1" stop-color="#00c8b3" />
                                </linearGradient>
                            </defs>
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Elevated Search Engine Unit (Elevated higher onto Hero Banner) -->
            <div class="relative z-20 w-[98%] sm:w-[96%] md:w-[95%] lg:w-[95%] max-w-[1125px] mx-auto -mt-28 sm:-mt-32 md:-mt-36">
                <!-- Frosted Translucent Title Bar (60% -> 30% -> 0% Opacity Gradient extending under white card) -->
                <div class="rounded-t-[18px] sm:rounded-t-[20px] bg-gradient-to-r from-slate-900/60 via-slate-900/30 to-transparent px-5 pt-2.5 pb-5 sm:px-6 sm:pt-3 sm:pb-6 text-white">
                    <h1 id="catalog-search-heading" class="text-sm sm:text-base md:text-lg font-extrabold tracking-tight text-white drop-shadow-md truncate">
                        {{ bannerTitle }}
                    </h1>
                </div>

                <!-- Floating White Search Box Container (Overlapping seamlessly with rounded corners) -->
                <div class="relative z-10 -mt-3.5 sm:-mt-4 rounded-[18px] sm:rounded-[20px] bg-white p-3.5 sm:p-4 shadow-[0_16px_36px_rgba(15,44,92,0.12)] border border-slate-200/90">
                    <!-- Top Filter Row: Pill Tabs -->
                    <div class="flex items-center border-b border-slate-100 pb-2.5">
                        <!-- Pill Tabs (Homepage Style) -->
                        <div class="flex items-center gap-1 p-0.5 rounded-full bg-slate-100/90 border border-slate-200/70">
                            <button
                                type="button"
                                class="rounded-full px-3 py-0.5 text-[11px] font-bold transition-all duration-200"
                                :class="tripType === '' ? 'bg-[#3E7BEF] text-white shadow-xs' : 'text-slate-600 hover:text-slate-900 hover:bg-white/60'"
                                @click="tripType = ''"
                            >
                                Semua Trip
                            </button>
                            <button
                                type="button"
                                class="inline-flex items-center gap-1.5 rounded-full px-3 py-0.5 text-[11px] font-bold transition-all duration-200"
                                :class="tripType === 'open-trip' ? 'bg-[#3E7BEF] text-white shadow-xs' : 'text-slate-600 hover:text-slate-900 hover:bg-white/60'"
                                @click="tripType = 'open-trip'"
                            >
                                <!-- Solid Person Hiking Icon -->
                                <svg class="size-2.5 fill-current" viewBox="0 0 384 512" aria-hidden="true">
                                    <path d="M192 48a48 48 0 1 1 96 0 48 48 0 1 1 -96 0zm51.3 182.7L224.2 307l49.7 49.7c9 9 14.1 21.2 14.1 33.9l0 89.4c0 17.7-14.3 32-32 32s-32-14.3-32-32l0-82.7-73.9-73.9c-15.8-15.8-22.2-38.6-16.9-60.3l20.4-84c8.3-34.1 42.7-54.9 76.7-46.4c19 4.8 35.6 16.4 46.4 32.7L305.1 208l30.9 0 0-24c0-13.3 10.7-24 24-24s24 10.7 24 24l0 55.8c0 .1 0 .2 0 .2s0 .2 0 .2L384 488c0 13.3-10.7 24-24 24s-24-10.7-24-24l0-216-39.4 0c-16 0-31-8-39.9-21.4l-13.3-20zM81.1 471.9L117.3 334c3 4.2 6.4 8.2 10.1 11.9l41.9 41.9L142.9 488.1c-4.5 17.1-22 27.3-39.1 22.8s-27.3-22-22.8-39.1zm55.5-346L101.4 266.5c-3 12.1-14.9 19.9-27.2 17.9l-47.9-8c-14-2.3-22.9-16.3-19.2-30L31.9 155c9.5-34.8 41.1-59 77.2-59l4.2 0c15.6 0 27.1 14.7 23.3 29.8z"/>
                                </svg>
                                <span>Open Trip</span>
                            </button>
                            <button
                                type="button"
                                class="inline-flex items-center gap-1.5 rounded-full px-3 py-0.5 text-[11px] font-bold transition-all duration-200"
                                :class="tripType === 'private-trip' ? 'bg-[#3E7BEF] text-white shadow-xs' : 'text-slate-600 hover:text-slate-900 hover:bg-white/60'"
                                @click="tripType = 'private-trip'"
                            >
                                <!-- Solid Crown Icon -->
                                <svg class="size-2.5 fill-current" viewBox="0 0 576 512" aria-hidden="true">
                                    <path d="M309 106c11.4-7 19-19.7 19-34c0-22.1-17.9-40-40-40s-40 17.9-40 40c0 14.4 7.6 27 19 34L209.7 220.6c-9.1 18.2-32.7 23.4-48.6 10.7L72 160c5-6.7 8-15 8-24c0-22.1-17.9-40-40-40S0 113.9 0 136s17.9 40 40 40c.2 0 .5 0 .7 0L86.4 427.4c5.5 30.4 32 52.6 63 52.6l277.2 0c30.9 0 57.4-22.1 63-52.6L535.3 176c.2 0 .5 0 .7 0c22.1 0 40-17.9 40-40s-17.9-40-40-40s-40 17.9-40 40c0 9 3 17.3 8 24l-89.1 71.3c-15.9 12.7-39.5 7.5-48.6-10.7L309 106z"/>
                                </svg>
                                <span>Private Trip</span>
                            </button>
                        </div>
                    </div>

                    <!-- Main Input Form: Multi-box layout (Integrated Guest Selector) -->
                    <form class="mt-2.5 grid grid-cols-1 gap-2 sm:grid-cols-2 lg:grid-cols-[1.15fr_auto_1.15fr_1fr_0.9fr_1fr_auto] items-center" @submit.prevent="handleSearch">
                        <!-- Box 1: From / Meeting Point -->
                        <div class="relative flex flex-col justify-center rounded-xl border border-slate-200 bg-white px-3 py-1.5 hover:border-[#3E7BEF]/60 focus-within:border-[#3E7BEF] focus-within:ring-2 focus-within:ring-[#3E7BEF]/15 transition min-h-[44px]">
                            <span class="text-[9px] font-bold uppercase tracking-wider text-slate-400 leading-tight">Dari (Meeting Point)</span>
                            <div class="mt-0.5 flex items-center gap-1.5">
                                <MapPin class="size-3.5 shrink-0 text-[#3E7BEF]" />
                                <input
                                    v-model="meetingPoint"
                                    type="text"
                                    placeholder="Jakarta / Semua Kota"
                                    class="w-full bg-transparent text-xs font-semibold text-slate-800 placeholder:text-slate-400 placeholder:font-normal focus:outline-none"
                                />
                            </div>
                        </div>

                        <!-- Swap Icon Button (Center Column) -->
                        <div class="hidden lg:flex items-center justify-center">
                            <button
                                type="button"
                                class="grid size-7 place-items-center rounded-full border border-slate-200 bg-white text-[#3E7BEF] shadow-2xs hover:bg-blue-50 hover:border-[#3E7BEF] transition active:scale-90"
                                aria-label="Tukar lokasi keberangkatan dan tujuan"
                                title="Tukar lokasi"
                                @click="swapLocations"
                            >
                                <ArrowLeftRight class="size-3" />
                            </button>
                        </div>

                        <!-- Box 2: To / Destination -->
                        <div class="relative flex flex-col justify-center rounded-xl border border-slate-200 bg-white px-3 py-1.5 hover:border-[#3E7BEF]/60 focus-within:border-[#3E7BEF] focus-within:ring-2 focus-within:ring-[#3E7BEF]/15 transition min-h-[44px]">
                            <span class="text-[9px] font-bold uppercase tracking-wider text-slate-400 leading-tight">Ke (Destinasi Tujuan)</span>
                            <div class="mt-0.5 flex items-center gap-1.5">
                                <Compass class="size-3.5 shrink-0 text-[#3E7BEF]" />
                                <input
                                    v-model="destination"
                                    type="text"
                                    placeholder="Labuan Bajo, Bromo, Bali"
                                    class="w-full bg-transparent text-xs font-semibold text-slate-800 placeholder:text-slate-400 placeholder:font-normal focus:outline-none"
                                />
                                <button
                                    v-if="destination"
                                    type="button"
                                    class="text-slate-300 hover:text-slate-500"
                                    @click="destination = ''"
                                >
                                    <X class="size-3" />
                                </button>
                            </div>
                        </div>

                        <!-- Box 3: Departure Date -->
                        <div
                            class="relative flex flex-col justify-center rounded-xl border border-slate-200 bg-white px-3 py-1.5 hover:border-[#3E7BEF]/60 focus-within:border-[#3E7BEF] focus-within:ring-2 focus-within:ring-[#3E7BEF]/15 transition cursor-pointer min-h-[44px]"
                            @click="openDatePicker"
                        >
                            <span class="text-[9px] font-bold uppercase tracking-wider text-slate-400 leading-tight">Tanggal Berangkat</span>
                            <div class="mt-0.5 flex items-center gap-1.5">
                                <CalendarDays class="size-3.5 shrink-0 text-[#3E7BEF]" />
                                <span
                                    class="text-xs font-semibold truncate block"
                                    :class="departureDate ? 'text-slate-800' : 'text-slate-400 font-normal'"
                                >
                                    {{ formattedDateDisplay || 'Pilih tanggal' }}
                                </span>
                            </div>
                            <input
                                ref="dateInputRef"
                                v-model="departureDate"
                                type="date"
                                class="absolute inset-0 size-full opacity-0 cursor-pointer"
                                aria-label="Tanggal Keberangkatan"
                            />
                        </div>

                        <!-- Box 4: Duration / Durasi Trip -->
                        <div class="relative flex flex-col justify-center rounded-xl border border-slate-200 bg-white px-3 py-1.5 hover:border-[#3E7BEF]/60 focus-within:border-[#3E7BEF] focus-within:ring-2 focus-within:ring-[#3E7BEF]/15 transition min-h-[44px]">
                            <span class="text-[9px] font-bold uppercase tracking-wider text-slate-400 leading-tight">Durasi Trip</span>
                            <div class="mt-0.5 flex items-center gap-1.5">
                                <Clock class="size-3.5 shrink-0 text-[#3E7BEF]" />
                                <select
                                    v-model="duration"
                                    class="w-full bg-transparent text-xs font-semibold text-slate-800 focus:outline-none cursor-pointer"
                                >
                                    <option value="">Semua Durasi</option>
                                    <option value="1d">1 Hari (Day Trip)</option>
                                    <option value="2d1n">2 Hari 1 Malam (2H1M)</option>
                                    <option value="3d2n">3 Hari 2 Malam (3H2M)</option>
                                    <option value="4d3n">4 Hari 3 Malam (4H3M)</option>
                                    <option value="5d+">5+ Hari (Long Trip)</option>
                                </select>
                            </div>
                        </div>

                        <!-- Box 5: Guests / Jumlah Tamu (Counter Dropdown) -->
                        <div
                            class="relative flex flex-col justify-center rounded-xl border border-slate-200 bg-white px-3 py-1.5 hover:border-[#3E7BEF]/60 focus-within:border-[#3E7BEF] focus-within:ring-2 focus-within:ring-[#3E7BEF]/15 transition cursor-pointer min-h-[44px]"
                            data-dropdown
                            @click="toggleDropdown('guests')"
                        >
                            <span class="text-[9px] font-bold uppercase tracking-wider text-slate-400 leading-tight">Jumlah Tamu</span>
                            <div class="mt-0.5 flex items-center justify-between gap-1">
                                <div class="flex items-center gap-1.5 min-w-0">
                                    <Users class="size-3.5 shrink-0 text-[#3E7BEF]" />
                                    <span class="text-xs font-semibold text-slate-800 truncate block">
                                        {{ totalGuestsLabel }}
                                    </span>
                                </div>
                                <ChevronDown
                                    class="size-3 shrink-0 text-slate-400 transition-transform duration-200"
                                    :class="activeDropdown === 'guests' ? 'rotate-180 text-[#3E7BEF]' : ''"
                                />
                            </div>

                            <!-- Dropdown Menu for Guests -->
                            <div
                                v-if="activeDropdown === 'guests'"
                                class="absolute right-0 top-full z-50 mt-2 w-64 rounded-xl border border-slate-100 bg-white p-3.5 shadow-[0_16px_36px_rgba(15,44,92,0.18)] ring-1 ring-black/5"
                                @click.stop
                            >
                                <div class="space-y-3">
                                    <!-- Adults row -->
                                    <div class="flex items-center justify-between">
                                        <div>
                                            <p class="text-xs font-bold text-slate-800">Dewasa</p>
                                            <p class="text-[10px] text-slate-400">Usia 12+ tahun</p>
                                        </div>
                                        <div class="flex items-center gap-1.5">
                                            <button
                                                type="button"
                                                :disabled="adults <= 1"
                                                class="grid size-6 place-items-center rounded-md bg-slate-100 text-slate-700 hover:bg-slate-200 disabled:opacity-30 disabled:cursor-not-allowed"
                                                @click.stop="adults = Math.max(1, adults - 1)"
                                            >
                                                <Minus class="size-2.5" />
                                            </button>
                                            <span class="w-4 text-center text-xs font-bold text-slate-800">{{ adults }}</span>
                                            <button
                                                type="button"
                                                :disabled="adults >= 20"
                                                class="grid size-6 place-items-center rounded-md bg-blue-50 text-[#3E7BEF] hover:bg-blue-100"
                                                @click.stop="adults = Math.min(20, adults + 1)"
                                            >
                                                <Plus class="size-2.5" />
                                            </button>
                                        </div>
                                    </div>

                                    <!-- Children row -->
                                    <div class="flex items-center justify-between border-t border-slate-100 pt-2.5">
                                        <div>
                                            <p class="text-xs font-bold text-slate-800">Anak-anak</p>
                                            <p class="text-[10px] text-slate-400">Usia 2-11 tahun</p>
                                        </div>
                                        <div class="flex items-center gap-1.5">
                                            <button
                                                type="button"
                                                :disabled="children <= 0"
                                                class="grid size-6 place-items-center rounded-md bg-slate-100 text-slate-700 hover:bg-slate-200 disabled:opacity-30 disabled:cursor-not-allowed"
                                                @click.stop="children = Math.max(0, children - 1)"
                                            >
                                                <Minus class="size-2.5" />
                                            </button>
                                            <span class="w-4 text-center text-xs font-bold text-slate-800">{{ children }}</span>
                                            <button
                                                type="button"
                                                :disabled="children >= 10"
                                                class="grid size-6 place-items-center rounded-md bg-blue-50 text-[#3E7BEF] hover:bg-blue-100"
                                                @click.stop="children = Math.min(10, children + 1)"
                                            >
                                                <Plus class="size-2.5" />
                                            </button>
                                        </div>
                                    </div>

                                    <!-- Done button -->
                                    <div class="flex justify-end pt-1 border-t border-slate-100">
                                        <button
                                            type="button"
                                            class="rounded-lg bg-[#3E7BEF] px-3.5 py-1 text-[11px] font-bold text-white shadow-xs transition hover:bg-[#2b6be8] active:scale-95"
                                            @click.stop="activeDropdown = null"
                                        >
                                            Selesai
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Search Action Button (Traveloka Vibrant Orange Button) -->
                        <div class="flex items-center sm:col-span-2 lg:col-span-1">
                            <button
                                type="submit"
                                class="inline-flex min-h-[44px] w-full items-center justify-center gap-1.5 rounded-xl bg-[#ff5e1f] px-5 py-2 text-xs font-bold text-white shadow-[0_4px_14px_rgba(255,94,31,0.35)] transition-all duration-200 hover:bg-[#e64e10] hover:shadow-[0_6px_18px_rgba(255,94,31,0.45)] active:scale-95 cursor-pointer"
                            >
                                <Search class="size-4 stroke-[2.5]" />
                                <span>Cari Trip</span>
                            </button>
                        </div>
                    </form>

                    <!-- Bottom Inspiration & Quick Link Row -->
                    <div class="mt-2.5 flex items-center gap-1.5 text-[11px] text-slate-500 pt-2 border-t border-slate-100 flex-wrap">
                    <span class="text-[10.5px] text-slate-400">Destinasi Populer:</span>
                    <button
                        v-for="dest in popularDestinations"
                        :key="dest"
                        type="button"
                        class="rounded-md bg-slate-100/80 px-2 py-0.5 text-[10.5px] font-medium text-slate-600 transition hover:bg-blue-50 hover:text-[#3E7BEF]"
                        @click="selectQuickDestination(dest)"
                    >
                        {{ dest }}
                    </button>
                </div>
                </div>
            </div>
        </div>
    </section>
</template>
