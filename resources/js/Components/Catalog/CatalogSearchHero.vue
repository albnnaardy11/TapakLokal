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
    Sparkles,
    User,
    Users,
    X,
} from 'lucide-vue-next';

const props = defineProps({
    initialFilters: {
        type: Object,
        default: () => ({}),
    },
});

const emit = defineEmits(['search']);

// Search form states
const tripType = ref(props.initialFilters?.type || '');
const destination = ref(props.initialFilters?.q || '');
const meetingPoint = ref(props.initialFilters?.from || 'Semua Meeting Point');
const departureDate = ref(props.initialFilters?.date || '');
const duration = ref(props.initialFilters?.duration || '');
const verifiedOnly = ref(true);
const packageTier = ref('Reguler');

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
    <section class="relative w-full pt-20 sm:pt-24 lg:pt-26" aria-labelledby="catalog-search-heading">
        <div class="mx-auto max-w-[1180px] px-3 sm:px-6 lg:px-0">
            <!-- Daylight Sky Scenic Banner Container (1:1 Reference Style) -->
            <div class="relative overflow-hidden rounded-[24px] sm:rounded-[28px] bg-[#d9effe] shadow-[0_16px_40px_rgba(20,70,130,0.12)] border border-sky-100/80">
                <!-- Daylight Blue Sky with Soft White Clouds Background Image -->
                <img
                    src="https://images.unsplash.com/photo-1513002749550-c59d786b8e6c?auto=format&fit=crop&w=1920&q=85"
                    alt="Langit Biru Cerah Indonesia"
                    class="absolute inset-0 size-full object-cover object-center"
                    loading="eager"
                />
                <div class="absolute inset-0 bg-gradient-to-b from-sky-100/20 via-sky-200/30 to-sky-300/40"></div>

                <!-- Frosted Translucent Title Bar (1:1 with Reference Image) -->
                <div class="relative z-10 mx-3 my-6 sm:mx-6 sm:my-8">
                    <div class="rounded-t-[18px] sm:rounded-t-[20px] bg-[#293d56]/82 px-5 py-3 sm:px-6 sm:py-3.5 text-white backdrop-blur-md border-t border-x border-white/25 flex items-center justify-between shadow-xs">
                        <h1 id="catalog-search-heading" class="text-sm font-extrabold tracking-tight sm:text-base md:text-lg text-white drop-shadow-xs truncate">
                            {{ bannerTitle }}
                        </h1>
                    </div>

                    <!-- Floating White Search Box Container (Versi TapakLokal) -->
                    <div class="rounded-b-[18px] sm:rounded-b-[20px] bg-white p-4 sm:p-5 shadow-[0_20px_45px_rgba(15,40,80,0.16)] border-x border-b border-slate-100">
                        <!-- Top Filter Row: Pill Tabs on Left + Controls on Right -->
                        <div class="flex flex-wrap items-center justify-between gap-3 border-b border-slate-100 pb-3.5">
                            <!-- Left Pill Tabs -->
                            <div class="flex items-center gap-1.5 p-0.5 rounded-full bg-slate-100/90 border border-slate-200/70">
                                <button
                                    type="button"
                                    class="rounded-full px-3.5 py-1 text-xs font-bold transition-all duration-200"
                                    :class="tripType === '' ? 'bg-[#0088ff] text-white shadow-xs' : 'text-slate-600 hover:text-slate-900 hover:bg-white/60'"
                                    @click="tripType = ''"
                                >
                                    Semua Trip
                                </button>
                                <button
                                    type="button"
                                    class="inline-flex items-center gap-1.5 rounded-full px-3.5 py-1 text-xs font-bold transition-all duration-200"
                                    :class="tripType === 'open-trip' ? 'bg-[#0088ff] text-white shadow-xs' : 'text-slate-600 hover:text-slate-900 hover:bg-white/60'"
                                    @click="tripType = 'open-trip'"
                                >
                                    <!-- Solid Person Hiking Icon -->
                                    <svg class="size-3 fill-current" viewBox="0 0 384 512" aria-hidden="true">
                                        <path d="M192 48a48 48 0 1 1 96 0 48 48 0 1 1 -96 0zm51.3 182.7L224.2 307l49.7 49.7c9 9 14.1 21.2 14.1 33.9l0 89.4c0 17.7-14.3 32-32 32s-32-14.3-32-32l0-82.7-73.9-73.9c-15.8-15.8-22.2-38.6-16.9-60.3l20.4-84c8.3-34.1 42.7-54.9 76.7-46.4c19 4.8 35.6 16.4 46.4 32.7L305.1 208l30.9 0 0-24c0-13.3 10.7-24 24-24s24 10.7 24 24l0 55.8c0 .1 0 .2 0 .2s0 .2 0 .2L384 488c0 13.3-10.7 24-24 24s-24-10.7-24-24l0-216-39.4 0c-16 0-31-8-39.9-21.4l-13.3-20zM81.1 471.9L117.3 334c3 4.2 6.4 8.2 10.1 11.9l41.9 41.9L142.9 488.1c-4.5 17.1-22 27.3-39.1 22.8s-27.3-22-22.8-39.1zm55.5-346L101.4 266.5c-3 12.1-14.9 19.9-27.2 17.9l-47.9-8c-14-2.3-22.9-16.3-19.2-30L31.9 155c9.5-34.8 41.1-59 77.2-59l4.2 0c15.6 0 27.1 14.7 23.3 29.8z"/>
                                    </svg>
                                    <span>Open Trip</span>
                                </button>
                                <button
                                    type="button"
                                    class="inline-flex items-center gap-1.5 rounded-full px-3.5 py-1 text-xs font-bold transition-all duration-200"
                                    :class="tripType === 'private-trip' ? 'bg-[#0088ff] text-white shadow-xs' : 'text-slate-600 hover:text-slate-900 hover:bg-white/60'"
                                    @click="tripType = 'private-trip'"
                                >
                                    <!-- Solid Crown Icon -->
                                    <svg class="size-3 fill-current" viewBox="0 0 576 512" aria-hidden="true">
                                        <path d="M309 106c11.4-7 19-19.7 19-34c0-22.1-17.9-40-40-40s-40 17.9-40 40c0 14.4 7.6 27 19 34L209.7 220.6c-9.1 18.2-32.7 23.4-48.6 10.7L72 160c5-6.7 8-15 8-24c0-22.1-17.9-40-40-40S0 113.9 0 136s17.9 40 40 40c.2 0 .5 0 .7 0L86.4 427.4c5.5 30.4 32 52.6 63 52.6l277.2 0c30.9 0 57.4-22.1 63-52.6L535.3 176c.2 0 .5 0 .7 0c22.1 0 40-17.9 40-40s-17.9-40-40-40s-40 17.9-40 40c0 9 3 17.3 8 24l-89.1 71.3c-15.9 12.7-39.5 7.5-48.6-10.7L309 106z"/>
                                    </svg>
                                    <span>Private Trip</span>
                                </button>
                            </div>

                            <!-- Right Controls: Toggle, Guest Counter, Package Tier -->
                            <div class="flex flex-wrap items-center gap-3 sm:gap-4 text-xs font-semibold text-slate-700">
                                <!-- Direct / Verified Only Checkbox -->
                                <label class="inline-flex items-center gap-2 cursor-pointer select-none">
                                    <input
                                        v-model="verifiedOnly"
                                        type="checkbox"
                                        class="size-4 rounded border-slate-300 text-[#0088ff] focus:ring-[#0088ff]/20"
                                    />
                                    <span class="text-slate-700 text-xs font-medium">Vendor Terverifikasi</span>
                                </label>

                                <!-- Guest Selector Trigger -->
                                <div class="relative" data-dropdown>
                                    <button
                                        type="button"
                                        class="inline-flex items-center gap-1.5 text-xs font-bold text-slate-700 hover:text-[#0088ff] transition-colors"
                                        @click="toggleDropdown('guests')"
                                    >
                                        <Users class="size-3.5 text-[#0088ff]" />
                                        <span>{{ totalGuestsLabel }}</span>
                                        <ChevronDown class="size-3 text-slate-400" />
                                    </button>

                                    <!-- Guest Dropdown Menu -->
                                    <div
                                        v-if="activeDropdown === 'guests'"
                                        class="absolute right-0 top-full z-50 mt-2 w-64 rounded-2xl border border-slate-100 bg-white p-3.5 shadow-xl ring-1 ring-black/5"
                                    >
                                        <div class="space-y-3">
                                            <div class="flex items-center justify-between">
                                                <div>
                                                    <p class="text-xs font-bold text-slate-800">Dewasa</p>
                                                    <p class="text-[10px] text-slate-400">Usia 12+ tahun</p>
                                                </div>
                                                <div class="flex items-center gap-2">
                                                    <button
                                                        type="button"
                                                        :disabled="adults <= 1"
                                                        class="grid size-6.5 place-items-center rounded-md bg-slate-100 text-slate-700 hover:bg-slate-200 disabled:opacity-30 disabled:cursor-not-allowed"
                                                        @click="adults = Math.max(1, adults - 1)"
                                                    >
                                                        <Minus class="size-3" />
                                                    </button>
                                                    <span class="w-5 text-center text-xs font-bold text-slate-800">{{ adults }}</span>
                                                    <button
                                                        type="button"
                                                        :disabled="adults >= 20"
                                                        class="grid size-6.5 place-items-center rounded-md bg-blue-50 text-[#0088ff] hover:bg-blue-100"
                                                        @click="adults = Math.min(20, adults + 1)"
                                                    >
                                                        <Plus class="size-3" />
                                                    </button>
                                                </div>
                                            </div>

                                            <div class="flex items-center justify-between border-t border-slate-100 pt-2.5">
                                                <div>
                                                    <p class="text-xs font-bold text-slate-800">Anak-anak</p>
                                                    <p class="text-[10px] text-slate-400">Usia 2-11 tahun</p>
                                                </div>
                                                <div class="flex items-center gap-2">
                                                    <button
                                                        type="button"
                                                        :disabled="children <= 0"
                                                        class="grid size-6.5 place-items-center rounded-md bg-slate-100 text-slate-700 hover:bg-slate-200 disabled:opacity-30 disabled:cursor-not-allowed"
                                                        @click="children = Math.max(0, children - 1)"
                                                    >
                                                        <Minus class="size-3" />
                                                    </button>
                                                    <span class="w-5 text-center text-xs font-bold text-slate-800">{{ children }}</span>
                                                    <button
                                                        type="button"
                                                        :disabled="children >= 10"
                                                        class="grid size-6.5 place-items-center rounded-md bg-blue-50 text-[#0088ff] hover:bg-blue-100"
                                                        @click="children = Math.min(10, children + 1)"
                                                    >
                                                        <Plus class="size-3" />
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Package Tier Selector -->
                                <div class="relative" data-dropdown>
                                    <button
                                        type="button"
                                        class="inline-flex items-center gap-1.5 text-xs font-bold text-slate-700 hover:text-[#0088ff] transition-colors"
                                        @click="toggleDropdown('tier')"
                                    >
                                        <Sparkles class="size-3.5 text-amber-500" />
                                        <span>Paket {{ packageTier }}</span>
                                        <ChevronDown class="size-3 text-slate-400" />
                                    </button>

                                    <div
                                        v-if="activeDropdown === 'tier'"
                                        class="absolute right-0 top-full z-50 mt-2 w-36 rounded-xl border border-slate-100 bg-white p-1.5 shadow-xl ring-1 ring-black/5"
                                    >
                                        <button
                                            type="button"
                                            class="w-full text-left px-3 py-1.5 rounded-lg text-xs font-bold transition"
                                            :class="packageTier === 'Reguler' ? 'bg-blue-50 text-[#0088ff]' : 'text-slate-700 hover:bg-slate-50'"
                                            @click="packageTier = 'Reguler'; activeDropdown = null"
                                        >
                                            Paket Reguler
                                        </button>
                                        <button
                                            type="button"
                                            class="w-full text-left px-3 py-1.5 rounded-lg text-xs font-bold transition"
                                            :class="packageTier === 'VIP' ? 'bg-blue-50 text-[#0088ff]' : 'text-slate-700 hover:bg-slate-50'"
                                            @click="packageTier = 'VIP'; activeDropdown = null"
                                        >
                                            Paket VIP
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Main Input Form: Multi-box layout (1:1 Reference Style with bordered cards) -->
                        <form class="mt-3.5 grid grid-cols-1 gap-2.5 sm:grid-cols-2 lg:grid-cols-[1.3fr_auto_1.3fr_1.1fr_1fr_auto] items-stretch" @submit.prevent="handleSearch">
                            <!-- Box 1: From / Meeting Point -->
                            <div class="relative flex flex-col justify-center rounded-xl border border-slate-200 bg-white px-3.5 py-2 hover:border-blue-400 focus-within:border-[#0088ff] focus-within:ring-2 focus-within:ring-[#0088ff]/15 transition">
                                <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Dari (Meeting Point)</span>
                                <div class="mt-0.5 flex items-center gap-2">
                                    <MapPin class="size-4 shrink-0 text-[#0088ff]" />
                                    <input
                                        v-model="meetingPoint"
                                        type="text"
                                        placeholder="Contoh: Jakarta / Semua Kota"
                                        class="w-full bg-transparent text-xs sm:text-sm font-bold text-slate-800 placeholder:text-slate-400 placeholder:font-normal focus:outline-none"
                                    />
                                </div>
                            </div>

                            <!-- Swap Icon Button (Center Column) -->
                            <div class="hidden lg:flex items-center justify-center">
                                <button
                                    type="button"
                                    class="grid size-8 place-items-center rounded-full border border-slate-200 bg-white text-[#0088ff] shadow-xs hover:bg-slate-50 hover:border-[#0088ff] transition active:scale-90"
                                    aria-label="Tukar lokasi keberangkatan dan tujuan"
                                    title="Tukar lokasi"
                                    @click="swapLocations"
                                >
                                    <ArrowLeftRight class="size-3.5" />
                                </button>
                            </div>

                            <!-- Box 2: To / Destination -->
                            <div class="relative flex flex-col justify-center rounded-xl border border-slate-200 bg-white px-3.5 py-2 hover:border-blue-400 focus-within:border-[#0088ff] focus-within:ring-2 focus-within:ring-[#0088ff]/15 transition">
                                <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Ke (Destinasi Tujuan)</span>
                                <div class="mt-0.5 flex items-center gap-2">
                                    <Compass class="size-4 shrink-0 text-[#0088ff]" />
                                    <input
                                        v-model="destination"
                                        type="text"
                                        placeholder="Contoh: Labuan Bajo, Bromo, Bali"
                                        class="w-full bg-transparent text-xs sm:text-sm font-bold text-slate-800 placeholder:text-slate-400 placeholder:font-normal focus:outline-none"
                                    />
                                    <button
                                        v-if="destination"
                                        type="button"
                                        class="text-slate-300 hover:text-slate-500"
                                        @click="destination = ''"
                                    >
                                        <X class="size-3.5" />
                                    </button>
                                </div>
                            </div>

                            <!-- Box 3: Departure Date -->
                            <div
                                class="relative flex flex-col justify-center rounded-xl border border-slate-200 bg-white px-3.5 py-2 hover:border-blue-400 focus-within:border-[#0088ff] focus-within:ring-2 focus-within:ring-[#0088ff]/15 transition cursor-pointer"
                                @click="openDatePicker"
                            >
                                <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Tanggal Berangkat</span>
                                <div class="mt-0.5 flex items-center gap-2">
                                    <CalendarDays class="size-4 shrink-0 text-[#0088ff]" />
                                    <span
                                        class="text-xs sm:text-sm font-bold truncate block"
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
                            <div class="relative flex flex-col justify-center rounded-xl border border-slate-200 bg-white px-3.5 py-2 hover:border-blue-400 focus-within:border-[#0088ff] focus-within:ring-2 focus-within:ring-[#0088ff]/15 transition">
                                <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Durasi Trip</span>
                                <div class="mt-0.5 flex items-center gap-2">
                                    <Clock class="size-4 shrink-0 text-[#0088ff]" />
                                    <select
                                        v-model="duration"
                                        class="w-full bg-transparent text-xs sm:text-sm font-bold text-slate-800 focus:outline-none cursor-pointer"
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

                            <!-- Search Action Button (Vibrant Blue Pill/Card Button) -->
                            <div class="flex items-center sm:col-span-2 lg:col-span-1">
                                <button
                                    type="submit"
                                    class="inline-flex min-h-[46px] w-full items-center justify-center gap-2 rounded-xl bg-[#0088ff] px-6 py-3 text-xs sm:text-sm font-extrabold text-white shadow-[0_6px_18px_rgba(0,136,255,0.30)] transition-all duration-200 hover:bg-[#0077e6] hover:shadow-[0_8px_24px_rgba(0,136,255,0.40)] active:scale-95"
                                >
                                    <Search class="size-4" />
                                    <span>Cari Trip</span>
                                </button>
                            </div>
                        </form>

                        <!-- Bottom Inspiration & Quick Link Row -->
                        <div class="mt-3.5 flex flex-wrap items-center justify-between gap-2 text-xs text-slate-500 pt-3 border-t border-slate-100">
                            <div class="flex items-center gap-1.5 flex-wrap">
                                <span class="text-[11px] text-slate-400">Destinasi Populer:</span>
                                <button
                                    v-for="dest in popularDestinations"
                                    :key="dest"
                                    type="button"
                                    class="rounded-md bg-slate-100/80 px-2 py-0.5 text-[11px] font-semibold text-slate-600 transition hover:bg-blue-50 hover:text-[#0088ff]"
                                    @click="selectQuickDestination(dest)"
                                >
                                    {{ dest }}
                                </button>
                            </div>

                            <button
                                type="button"
                                class="inline-flex items-center gap-1 text-[11px] font-bold text-[#0088ff] hover:underline"
                                @click="selectQuickDestination('')"
                            >
                                <Compass class="size-3.5" />
                                <span>Butuh inspirasi liburan? Jelajahi trip pilihan &rarr;</span>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Bottom Spacer -->
                <div class="h-5 sm:h-6"></div>
            </div>
        </div>
    </section>
</template>
