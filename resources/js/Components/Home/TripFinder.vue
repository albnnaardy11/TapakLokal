<script setup>
import {
    Baby,
    CalendarDays,
    Check,
    ChevronDown,
    Coffee,
    Compass,
    Crown,
    Footprints,
    Gift,
    MapPin,
    Minus,
    Plus,
    Search,
    Shirt,
    ShoppingBag,
    Sparkles,
    Tag,
    TentTree,
    User,
    Users,
    UsersRound,
    Utensils,
    X,
} from 'lucide-vue-next';
import { computed, onBeforeUnmount, onMounted, ref } from 'vue';
import { router } from '@inertiajs/vue3';

const props = defineProps({
    partners: {
        type: Array,
        default: () => [],
    },
    showServiceTabs: {
        type: Boolean,
        default: true,
    },
    initialCategory: {
        type: String,
        default: '',
    },
});

const defaultPartners = [
    { id: 'p1', name: 'Millennium Hotels', image_url: '/Assets/Images/partners/partner-1.svg' },
    { id: 'p2', name: 'Accor', image_url: '/Assets/Images/partners/partner-2.svg' },
    { id: 'p3', name: 'Archipelago', image_url: '/Assets/Images/partners/partner-3.svg' },
    { id: 'p4', name: 'IHG Hotels', image_url: '/Assets/Images/partners/partner-4.svg' },
    { id: 'p5', name: 'The Ascott', image_url: '/Assets/Images/partners/partner-5.svg' },
];

const displayPartners = computed(() => {
    const list = Array.isArray(props.partners)
        ? props.partners
        : (props.partners ? Object.values(props.partners) : []);
    const valid = list.filter(item => item && (item.image_url || item.name));
    return valid.length > 0 ? valid : defaultPartners;
});

// Rotating Partners Slide (Cycle: 2 -> 4 -> 3 -> 1 logos every 3.5s)
const currentSlide = ref(0);
let partnerTimer = null;
const patternCounts = [2, 4, 3, 1];

const partnerSlides = computed(() => {
    const list = displayPartners.value;
    if (!list || list.length === 0) return [];
    if (list.length === 1) return [list];

    const slides = [];
    let currentIndex = 0;

    for (const count of patternCounts) {
        const targetCount = Math.min(count, list.length);
        const slideItems = [];
        for (let i = 0; i < targetCount; i++) {
            slideItems.push(list[(currentIndex + i) % list.length]);
        }
        slides.push(slideItems);
        currentIndex = (currentIndex + targetCount) % list.length;
    }

    return slides;
});

const activeSlideLogos = computed(() => {
    if (partnerSlides.value.length === 0) return [];
    return partnerSlides.value[currentSlide.value % partnerSlides.value.length];
});

// Active Tab ('trip' | 'souvenir')
const activeTab = ref('trip');

// Active open dropdown tracker ('tripCategory' | 'tripGuests' | 'souvenirCategory' | null)
const openDropdown = ref(null);

const toggleDropdown = (name) => {
    openDropdown.value = openDropdown.value === name ? null : name;
};

const closeDropdowns = () => {
    openDropdown.value = null;
};

// Global click-outside & ESC key listeners
const handleDocumentClick = (e) => {
    if (!e.target.closest('[data-dropdown]')) {
        closeDropdowns();
    }
};

const handleKeyDown = (e) => {
    if (e.key === 'Escape') {
        closeDropdowns();
    }
};

onMounted(() => {
    document.addEventListener('click', handleDocumentClick);
    document.addEventListener('keydown', handleKeyDown);

    // Rotate partner logos every 3.5 seconds (3 - 4 detik)
    partnerTimer = setInterval(() => {
        if (partnerSlides.value.length > 1) {
            currentSlide.value = (currentSlide.value + 1) % partnerSlides.value.length;
        }
    }, 3500);
});

onBeforeUnmount(() => {
    document.removeEventListener('click', handleDocumentClick);
    document.removeEventListener('keydown', handleKeyDown);

    if (partnerTimer) {
        clearInterval(partnerTimer);
    }
});

// Trip Form States
const tripLocation = ref('');
const tripCategory = ref(props.initialCategory || '');
const tripDate = ref('');
const tripDateInput = ref(null);
const adultCount = ref(2);
const childCount = ref(0);
const tripLocationInput = ref(null);

const formattedTripDate = computed(() => {
    if (!tripDate.value) return '';
    try {
        const [year, month, day] = tripDate.value.split('-');
        if (!year || !month || !day) return tripDate.value;
        const d = new Date(Number(year), Number(month) - 1, Number(day));
        return d.toLocaleDateString('id-ID', {
            weekday: 'short',
            day: 'numeric',
            month: 'short',
            year: 'numeric',
        });
    } catch {
        return tripDate.value;
    }
});

const openTripDatePicker = () => {
    try {
        tripDateInput.value?.showPicker();
    } catch {
        tripDateInput.value?.focus();
    }
};

const incrementAdults = () => {
    if (adultCount.value < 50) adultCount.value++;
};
const decrementAdults = () => {
    if (adultCount.value > 1) adultCount.value--;
};
const incrementChildren = () => {
    if (childCount.value < 30) childCount.value++;
};
const decrementChildren = () => {
    if (childCount.value > 0) childCount.value--;
};

// Selected Guests Label (e.g., "2 adults, 0 children")
const selectedGuestsLabel = computed(() => {
    const adults = adultCount.value;
    const children = childCount.value;
    const adultLabel = `${adults} adult${adults > 1 ? 's' : ''}`;
    const childLabel = `${children} child${children === 1 ? '' : 'ren'}`;
    return `${adultLabel}, ${childLabel}`;
});

// Oleh-Oleh Form States
const souvenirKeyword = ref('');
const souvenirLocation = ref('');
const souvenirCategory = ref('');
const souvenirDate = ref('');
const souvenirDateInput = ref(null);
const souvenirKeywordInput = ref(null);

const formattedSouvenirDate = computed(() => {
    if (!souvenirDate.value) return '';
    try {
        const [year, month, day] = souvenirDate.value.split('-');
        if (!year || !month || !day) return souvenirDate.value;
        const d = new Date(Number(year), Number(month) - 1, Number(day));
        return d.toLocaleDateString('id-ID', {
            weekday: 'short',
            day: 'numeric',
            month: 'short',
            year: 'numeric',
        });
    } catch {
        return souvenirDate.value;
    }
});

const openSouvenirDatePicker = () => {
    try {
        souvenirDateInput.value?.showPicker();
    } catch {
        souvenirDateInput.value?.focus();
    }
};

const searchMessage = ref('');

// Category options for Trip (Clean & Minimal)
const tripCategoryOptions = [
    {
        id: '',
        title: 'All',
        icon: null,
    },
    {
        id: 'open-trip',
        title: 'Open Trip',
        icon: TentTree,
    },
    {
        id: 'private-trip',
        title: 'Private Trip',
        icon: Crown,
    },
];

const selectedTripCategoryLabel = computed(() => {
    const found = tripCategoryOptions.find((o) => o.id === tripCategory.value);
    return found && found.id ? found.title : 'Pilih Kategori';
});

// Category options for Souvenirs (Clean & Minimal)
const souvenirCategoryOptions = [
    {
        id: '',
        title: 'All',
        icon: null,
    },
    {
        id: 'makanan',
        title: 'Makanan & Camilan',
        icon: Utensils,
    },
    {
        id: 'minuman',
        title: 'Minuman Tradisional',
        icon: Coffee,
    },
    {
        id: 'kerajinan',
        title: 'Kerajinan & Souvenir',
        icon: Gift,
    },
    {
        id: 'fashion',
        title: 'Batik & Kain Khas',
        icon: Shirt,
    },
];

const selectedSouvenirCategoryLabel = computed(() => {
    const found = souvenirCategoryOptions.find((o) => o.id === souvenirCategory.value);
    return found && found.id ? found.title : 'All';
});

const selectDestination = (name) => {
    activeTab.value = 'trip';
    tripLocation.value = name;
    searchMessage.value = name
        ? `Destinasi "${name}" dipilih. Silakan atur tanggal & kategori, lalu klik Cari Sekarang.`
        : '';
    tripLocationInput.value?.focus({ preventScroll: true });
    tripLocationInput.value?.scrollIntoView({
        behavior: window.matchMedia('(prefers-reduced-motion: reduce)').matches ? 'instant' : 'smooth',
        block: 'center',
    });
};

defineExpose({ selectDestination });

const searchTrips = () => {
    closeDropdowns();
    const totalPax = adultCount.value + childCount.value;
    router.get(route('catalog'), {
        q: tripLocation.value.trim() || undefined,
        type: tripCategory.value || undefined,
        date: tripDate.value || undefined,
        guests: totalPax > 0 ? totalPax : undefined,
    });
};

const searchSouvenirs = () => {
    closeDropdowns();
    const searchTerms = [souvenirKeyword.value.trim(), souvenirLocation.value.trim()]
        .filter(Boolean)
        .join(' ');

    router.get(route('explore', 'souvenir'), {
        q: searchTerms || undefined,
        category: souvenirCategory.value || undefined,
        date: souvenirDate.value || undefined,
    });
};
</script>

<template>
    <div class="w-full max-w-[1180px] mx-auto" aria-label="Pencarian Perjalanan dan Oleh-Oleh">
        <!-- 1. Top Service Tabs (Traveloka Style) -->
        <div v-if="showServiceTabs" class="flex items-center gap-2 sm:gap-3 border-b-2 border-white/80 pb-3.5 overflow-x-auto no-scrollbar">
            <button
                type="button"
                class="inline-flex items-center gap-2 rounded-full px-5 py-2 text-xs sm:text-sm font-bold transition-all duration-200"
                :class="
                    activeTab === 'trip'
                        ? 'bg-white text-slate-900 shadow-md ring-2 ring-white/30'
                        : 'text-white/85 hover:text-white hover:bg-white/10'
                "
                @click="
                    activeTab = 'trip';
                    closeDropdowns();
                    searchMessage = '';
                "
            >
                <Compass class="size-4 shrink-0" :class="activeTab === 'trip' ? 'text-[#0088ff]' : 'text-white'" />
                <span>Trip</span>
            </button>

            <button
                type="button"
                class="inline-flex items-center gap-2 rounded-full px-5 py-2 text-xs sm:text-sm font-bold transition-all duration-200"
                :class="
                    activeTab === 'souvenir'
                        ? 'bg-white text-slate-900 shadow-md ring-2 ring-white/30'
                        : 'text-white/85 hover:text-white hover:bg-white/10'
                "
                @click="
                    activeTab = 'souvenir';
                    closeDropdowns();
                    searchMessage = '';
                "
            >
                <ShoppingBag class="size-4 shrink-0" :class="activeTab === 'souvenir' ? 'text-[#0088ff]' : 'text-white'" />
                <span>Open PO Oleh-Oleh</span>
            </button>
        </div>

        <!-- 2. Sub-Category Filter Pills (Traveloka Style) -->
        <div v-if="activeTab === 'trip'" class="flex flex-wrap items-center gap-2" :class="showServiceTabs ? 'mt-3.5' : ''">
            <button
                v-for="opt in tripCategoryOptions"
                :key="opt.id"
                type="button"
                class="inline-flex items-center gap-1.5 rounded-full px-3.5 py-1 text-xs font-semibold transition-all duration-150"
                :class="
                    tripCategory === opt.id
                        ? 'bg-[#0088ff] text-white shadow-xs'
                        : 'bg-black/25 text-white/90 hover:bg-black/40 hover:text-white backdrop-blur-md'
                "
                @click="tripCategory = opt.id"
            >
                <component :is="opt.icon" v-if="opt.icon" class="size-3.5 shrink-0" />
                <span>{{ opt.title }}</span>
            </button>
        </div>

        <div v-else class="mt-3.5 flex flex-wrap items-center gap-2">
            <button
                v-for="opt in souvenirCategoryOptions"
                :key="opt.id"
                type="button"
                class="inline-flex items-center gap-1.5 rounded-full px-3.5 py-1 text-xs font-semibold transition-all duration-150"
                :class="
                    souvenirCategory === opt.id
                        ? 'bg-[#0088ff] text-white shadow-xs'
                        : 'bg-black/25 text-white/90 hover:bg-black/40 hover:text-white backdrop-blur-md'
                "
                @click="souvenirCategory = opt.id"
            >
                <component :is="opt.icon" v-if="opt.icon" class="size-3.5 shrink-0" />
                <span>{{ opt.title }}</span>
            </button>
        </div>

        <!-- 3. Desktop Labels Row (Clean White Text) -->
        <div
            v-if="activeTab === 'trip'"
            class="mt-3.5 hidden md:grid md:grid-cols-[1.5fr_1.2fr_1.2fr_auto] gap-3 px-5 text-xs font-semibold text-white/90 drop-shadow-sm"
        >
            <span>Lokasi / Destinasi</span>
            <span>Tanggal Berangkat</span>
            <span>Jumlah Tamu</span>
            <span class="w-12"></span>
        </div>

        <div
            v-else
            class="mt-3.5 hidden md:grid md:grid-cols-[1.4fr_1.2fr_1.2fr_auto] gap-3 px-5 text-xs font-semibold text-white/90 drop-shadow-sm"
        >
            <span>Cari Oleh-Oleh</span>
            <span>Kota / Asal Daerah</span>
            <span>Batas PO / Tanggal</span>
            <span class="w-12"></span>
        </div>

        <!-- 4. Unified Continuous Search Bar -->
        <!-- MODE 1: TRIP SEARCH FORM -->
        <form
            v-if="activeTab === 'trip'"
            class="mt-1.5 bg-white rounded-2xl md:rounded-full p-1.5 md:p-2 shadow-[0_20px_50px_rgba(0,0,0,0.30)] flex flex-col md:flex-row items-stretch md:items-center"
            @submit.prevent="searchTrips"
        >
            <!-- 1. Lokasi/Destinasi -->
            <div class="flex-1 flex items-center gap-3 px-4 py-2 border-b md:border-b-0 md:border-r border-slate-200">
                <MapPin class="size-5 shrink-0 text-[#0088ff]" />
                <div class="w-full min-w-0">
                    <span class="block md:hidden text-[10px] font-bold uppercase tracking-wider text-slate-400">Lokasi / Destinasi</span>
                    <input
                        id="trip-location-input"
                        ref="tripLocationInput"
                        v-model="tripLocation"
                        type="text"
                        placeholder="Contoh: Yogyakarta, Bali, Malang"
                        class="w-full bg-transparent text-xs sm:text-sm font-semibold text-slate-800 placeholder:text-slate-400 placeholder:font-normal focus:outline-none"
                    />
                </div>
                <button
                    v-if="tripLocation"
                    type="button"
                    class="text-slate-300 hover:text-slate-500"
                    aria-label="Hapus lokasi"
                    @click="tripLocation = ''"
                >
                    <X class="size-3.5" />
                </button>
            </div>

            <!-- 2. Tanggal Berangkat -->
            <div
                class="relative flex-1 flex items-center justify-between gap-2 px-4 py-2 border-b md:border-b-0 md:border-r border-slate-200 cursor-pointer transition hover:bg-slate-50/70"
                @click="openTripDatePicker"
            >
                <div class="flex items-center gap-3 min-w-0 pointer-events-none">
                    <CalendarDays class="size-5 shrink-0 text-[#0088ff]" />
                    <div class="min-w-0">
                        <span class="block md:hidden text-[10px] font-bold uppercase tracking-wider text-slate-400">Tanggal Berangkat</span>
                        <span
                            class="block truncate text-xs sm:text-sm font-semibold"
                            :class="tripDate ? 'text-slate-800' : 'text-slate-400 font-normal'"
                        >
                            {{ formattedTripDate || 'Pilih tanggal berangkat' }}
                        </span>
                    </div>
                </div>

                <!-- Invisible Native Date Input filling container -->
                <input
                    id="trip-date-input"
                    ref="tripDateInput"
                    v-model="tripDate"
                    type="date"
                    class="absolute inset-0 size-full opacity-0 cursor-pointer [color-scheme:light]"
                    aria-label="Tanggal Berangkat"
                    tabindex="-1"
                />

                <button
                    v-if="tripDate"
                    type="button"
                    class="relative z-10 text-slate-300 hover:text-slate-500 shrink-0 p-1"
                    aria-label="Hapus tanggal"
                    @click.stop="tripDate = ''"
                >
                    <X class="size-3.5" />
                </button>
            </div>

            <!-- 3. Jumlah Tamu (Custom Counter Dropdown) -->
            <div class="relative flex-1" data-dropdown>
                <button
                    type="button"
                    class="w-full flex items-center justify-between gap-3 px-4 py-2 text-left transition hover:bg-slate-50/70 focus:outline-none"
                    :class="openDropdown === 'tripGuests' ? 'bg-blue-50/50' : ''"
                    @click="toggleDropdown('tripGuests')"
                >
                    <div class="flex min-w-0 items-center gap-3">
                        <Users class="size-5 shrink-0 text-[#0088ff]" />
                        <div class="min-w-0">
                            <span class="block md:hidden text-[10px] font-bold uppercase tracking-wider text-slate-400">Jumlah Tamu</span>
                            <span class="truncate text-xs sm:text-sm font-semibold text-slate-800 block">
                                {{ selectedGuestsLabel }}
                            </span>
                        </div>
                    </div>
                    <ChevronDown
                        class="size-4 shrink-0 text-slate-400 transition-transform duration-200"
                        :class="openDropdown === 'tripGuests' ? 'rotate-180 text-[#0088ff]' : ''"
                    />
                </button>

                <!-- Dropdown Menu (Compact & Clean) -->
                <Transition
                    enter-active-class="transition duration-150 ease-out"
                    enter-from-class="transform opacity-0 -translate-y-1 scale-95"
                    enter-to-class="transform opacity-100 translate-y-0 scale-100"
                    leave-active-class="transition duration-100 ease-in"
                    leave-from-class="transform opacity-100 translate-y-0 scale-100"
                    leave-to-class="transform opacity-0 -translate-y-1 scale-95"
                >
                    <div
                        v-if="openDropdown === 'tripGuests'"
                        class="absolute right-0 top-full z-50 mt-2 w-[240px] sm:w-[250px] rounded-xl border border-slate-200/90 bg-white p-3 shadow-[0_15px_40px_rgba(15,35,70,0.20)] ring-1 ring-black/5"
                    >
                        <div class="space-y-2.5">
                            <!-- Row 1: Adult -->
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-2.5">
                                    <User class="size-4.5 shrink-0 text-[#0088ff]" />
                                    <span class="text-sm font-semibold text-slate-800">Adult</span>
                                </div>
                                <div class="flex items-center gap-1">
                                    <button
                                        type="button"
                                        :disabled="adultCount <= 1"
                                        class="flex size-7 items-center justify-center rounded-md bg-[#f1f5f9] text-[#0088ff] transition hover:bg-[#e2e8f0] active:scale-95 disabled:cursor-not-allowed disabled:opacity-30"
                                        @click.stop="decrementAdults"
                                        aria-label="Kurangi Dewasa"
                                    >
                                        <Minus class="size-3.5 stroke-[2.5]" />
                                    </button>
                                    <span class="w-7 border-b border-slate-300 pb-0.5 text-center text-sm font-bold text-slate-800">
                                        {{ adultCount }}
                                    </span>
                                    <button
                                        type="button"
                                        :disabled="adultCount >= 50"
                                        class="flex size-7 items-center justify-center rounded-md bg-[#f1f5f9] text-[#0088ff] transition hover:bg-[#e2e8f0] active:scale-95 disabled:cursor-not-allowed disabled:opacity-30"
                                        @click.stop="incrementAdults"
                                        aria-label="Tambah Dewasa"
                                    >
                                        <Plus class="size-3.5 stroke-[2.5]" />
                                    </button>
                                </div>
                            </div>

                            <!-- Row 2: Children -->
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-2.5">
                                    <Baby class="size-4.5 shrink-0 text-[#0088ff]" />
                                    <span class="text-sm font-semibold text-slate-800">Children</span>
                                </div>
                                <div class="flex items-center gap-1">
                                    <button
                                        type="button"
                                        :disabled="childCount <= 0"
                                        class="flex size-7 items-center justify-center rounded-md bg-[#f1f5f9] text-[#0088ff] transition hover:bg-[#e2e8f0] active:scale-95 disabled:cursor-not-allowed disabled:opacity-30"
                                        @click.stop="decrementChildren"
                                        aria-label="Kurangi Anak"
                                    >
                                        <Minus class="size-3.5 stroke-[2.5]" />
                                    </button>
                                    <span class="w-7 border-b border-slate-300 pb-0.5 text-center text-sm font-bold text-slate-800">
                                        {{ childCount }}
                                    </span>
                                    <button
                                        type="button"
                                        :disabled="childCount >= 30"
                                        class="flex size-7 items-center justify-center rounded-md bg-[#f1f5f9] text-[#0088ff] transition hover:bg-[#e2e8f0] active:scale-95 disabled:cursor-not-allowed disabled:opacity-30"
                                        @click.stop="incrementChildren"
                                        aria-label="Tambah Anak"
                                    >
                                        <Plus class="size-3.5 stroke-[2.5]" />
                                    </button>
                                </div>
                            </div>

                            <!-- Bottom Action: Done Button -->
                            <div class="flex justify-end pt-1">
                                <button
                                    type="button"
                                    class="rounded-lg bg-[#0088ff] px-4 py-1.5 text-xs font-bold text-white shadow-xs transition hover:bg-[#0074e0] active:scale-95"
                                    @click="closeDropdowns"
                                >
                                    Done
                                </button>
                            </div>
                        </div>
                    </div>
                </Transition>
            </div>

            <!-- 4. Tombol Search (Traveloka Style Orange Button) -->
            <div class="p-1 md:p-0">
                <button
                    type="submit"
                    class="w-full md:w-auto h-11 md:h-12 px-6 md:px-5 rounded-xl md:rounded-full bg-[#ff5e1f] hover:bg-[#e64e10] text-white flex items-center justify-center gap-2 shadow-[0_4px_14px_rgba(255,94,31,0.40)] transition-all duration-200 active:scale-95 shrink-0"
                    aria-label="Cari Sekarang"
                >
                    <Search class="size-5 stroke-[2.5]" />
                    <span class="md:hidden text-sm font-bold">Cari Sekarang</span>
                </button>
            </div>
        </form>

        <!-- MODE 2: OPEN PO OLEH-OLEH SEARCH FORM -->
        <form
            v-else
            class="mt-1.5 bg-white rounded-2xl md:rounded-full p-1.5 md:p-2 shadow-[0_20px_50px_rgba(0,0,0,0.30)] flex flex-col md:flex-row items-stretch md:items-center"
            @submit.prevent="searchSouvenirs"
        >
            <!-- 1. Nama Oleh-Oleh / Produk -->
            <div class="flex-1 flex items-center gap-3 px-4 py-2 border-b md:border-b-0 md:border-r border-slate-200">
                <ShoppingBag class="size-5 shrink-0 text-[#0088ff]" />
                <div class="w-full min-w-0">
                    <span class="block md:hidden text-[10px] font-bold uppercase tracking-wider text-slate-400">Cari Oleh-Oleh</span>
                    <input
                        id="souvenir-name-input"
                        ref="souvenirKeywordInput"
                        v-model="souvenirKeyword"
                        type="text"
                        placeholder="Contoh: Bakpia, Pie Susu, Strudel"
                        class="w-full bg-transparent text-xs sm:text-sm font-semibold text-slate-800 placeholder:text-slate-400 placeholder:font-normal focus:outline-none"
                    />
                </div>
                <button
                    v-if="souvenirKeyword"
                    type="button"
                    class="text-slate-300 hover:text-slate-500"
                    aria-label="Hapus kata kunci"
                    @click="souvenirKeyword = ''"
                >
                    <X class="size-3.5" />
                </button>
            </div>

            <!-- 2. Kota / Asal Daerah -->
            <div class="flex-1 flex items-center gap-3 px-4 py-2 border-b md:border-b-0 md:border-r border-slate-200">
                <MapPin class="size-5 shrink-0 text-[#0088ff]" />
                <div class="w-full min-w-0">
                    <span class="block md:hidden text-[10px] font-bold uppercase tracking-wider text-slate-400">Kota / Asal Daerah</span>
                    <input
                        id="souvenir-location-input"
                        v-model="souvenirLocation"
                        type="text"
                        placeholder="Contoh: Yogyakarta, Bali, Bandung"
                        class="w-full bg-transparent text-xs sm:text-sm font-semibold text-slate-800 placeholder:text-slate-400 placeholder:font-normal focus:outline-none"
                    />
                </div>
                <button
                    v-if="souvenirLocation"
                    type="button"
                    class="text-slate-300 hover:text-slate-500"
                    aria-label="Hapus asal daerah"
                    @click="souvenirLocation = ''"
                >
                    <X class="size-3.5" />
                </button>
            </div>

            <!-- 3. Batas PO / Tanggal -->
            <div
                class="relative flex-1 flex items-center justify-between gap-2 px-4 py-2 cursor-pointer transition hover:bg-slate-50/70"
                @click="openSouvenirDatePicker"
            >
                <div class="flex items-center gap-3 min-w-0 pointer-events-none">
                    <CalendarDays class="size-5 shrink-0 text-[#0088ff]" />
                    <div class="min-w-0">
                        <span class="block md:hidden text-[10px] font-bold uppercase tracking-wider text-slate-400">Batas PO / Tanggal</span>
                        <span
                            class="block truncate text-xs sm:text-sm font-semibold"
                            :class="souvenirDate ? 'text-slate-800' : 'text-slate-400 font-normal'"
                        >
                            {{ formattedSouvenirDate || 'Pilih batas tanggal' }}
                        </span>
                    </div>
                </div>

                <!-- Invisible Native Date Input filling container -->
                <input
                    id="souvenir-date-input"
                    ref="souvenirDateInput"
                    v-model="souvenirDate"
                    type="date"
                    class="absolute inset-0 size-full opacity-0 cursor-pointer [color-scheme:light]"
                    aria-label="Batas PO atau Tanggal"
                    tabindex="-1"
                />

                <button
                    v-if="souvenirDate"
                    type="button"
                    class="relative z-10 text-slate-300 hover:text-slate-500 shrink-0 p-1"
                    aria-label="Hapus batas tanggal"
                    @click.stop="souvenirDate = ''"
                >
                    <X class="size-3.5" />
                </button>
            </div>

            <!-- 4. Tombol Search -->
            <div class="p-1 md:p-0">
                <button
                    type="submit"
                    class="w-full md:w-auto h-11 md:h-12 px-6 md:px-5 rounded-xl md:rounded-full bg-[#ff5e1f] hover:bg-[#e64e10] text-white flex items-center justify-center gap-2 shadow-[0_4px_14px_rgba(255,94,31,0.40)] transition-all duration-200 active:scale-95 shrink-0"
                    aria-label="Cari Sekarang"
                >
                    <Search class="size-5 stroke-[2.5]" />
                    <span class="md:hidden text-sm font-bold">Cari Sekarang</span>
                </button>
            </div>
        </form>

        <p
            v-if="searchMessage"
            class="mt-3 rounded-xl bg-white/90 backdrop-blur px-4 py-2 text-xs font-medium text-[#1677e8] shadow-sm"
        >
            {{ searchMessage }}
        </p>

        <!-- 5. Trusted By / Dipercayai Oleh Bar (Compact Fit Content, Dynamic 2 -> 4 -> 3 -> 1 Logos) -->
        <div v-if="displayPartners.length > 0" class="mt-4 sm:mt-5 flex justify-center w-full">
            <div
                class="inline-flex items-center justify-center gap-3 sm:gap-4 rounded-xl sm:rounded-2xl bg-white/95 px-4 sm:px-6 py-2 sm:py-2.5 shadow-[0_8px_24px_rgba(0,0,0,0.18)] backdrop-blur-md transition-all duration-500 ease-out"
            >
                <!-- Label (rapat dengan logo) -->
                <span class="text-xs sm:text-[13px] font-bold italic tracking-wide text-[#0064d2] shrink-0 whitespace-nowrap">
                    Dipercayai oleh:
                </span>

                <!-- Animated Rotating Logos -->
                <div class="relative flex items-center justify-center min-h-[28px] overflow-hidden">
                    <Transition name="partner-slide" mode="out-in">
                        <div
                            :key="currentSlide"
                            class="inline-flex items-center justify-center gap-3 sm:gap-5"
                        >
                            <a
                                v-for="partner in activeSlideLogos"
                                :key="partner.id || partner.name"
                                :href="partner.website_url || undefined"
                                :target="partner.website_url ? '_blank' : undefined"
                                :rel="partner.website_url ? 'noopener noreferrer' : undefined"
                                class="group flex items-center justify-center transition-all duration-200 hover:scale-105 shrink-0"
                                :class="partner.website_url ? 'cursor-pointer' : 'cursor-default'"
                                :title="partner.name"
                            >
                                <img
                                    v-if="partner.image_url"
                                    :src="partner.image_url"
                                    :alt="partner.name"
                                    class="h-5 sm:h-6 max-h-6 max-w-[80px] sm:max-w-[105px] object-contain opacity-85 contrast-125 transition duration-200 group-hover:opacity-100"
                                    loading="lazy"
                                />
                                <span
                                    v-else
                                    class="text-[11px] sm:text-xs font-bold uppercase tracking-wider text-slate-700 group-hover:text-[#0064d2] whitespace-nowrap"
                                >
                                    {{ partner.name }}
                                </span>
                            </a>
                        </div>
                    </Transition>
                </div>
            </div>
        </div>
    </div>
</template>

<style scoped>
.partner-slide-enter-active,
.partner-slide-leave-active {
    transition: all 0.4s ease-in-out;
}
.partner-slide-enter-from {
    opacity: 0;
    transform: translateY(6px);
}
.partner-slide-leave-to {
    opacity: 0;
    transform: translateY(-6px);
}
</style>
