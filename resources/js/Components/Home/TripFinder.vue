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
import { route } from 'ziggy-js';

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
});

onBeforeUnmount(() => {
    document.removeEventListener('click', handleDocumentClick);
    document.removeEventListener('keydown', handleKeyDown);
});

// Trip Form States
const tripLocation = ref('');
const tripCategory = ref('');
const tripDate = ref('');
const adultCount = ref(2);
const childCount = ref(0);
const tripLocationInput = ref(null);

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
const souvenirKeywordInput = ref(null);

const searchMessage = ref('');

// Category options for Trip (Clean & Minimal)
const tripCategoryOptions = [
    {
        id: '',
        title: 'Semua Kategori',
        icon: Compass,
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
        title: 'Semua Kategori',
        icon: Sparkles,
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
    return found && found.id ? found.title : 'Semua Kategori';
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
    <section
        class="absolute -bottom-24 sm:-bottom-20 md:-bottom-16 left-1/2 z-30 w-[calc(100%-1.5rem)] max-w-[1140px] -translate-x-1/2 rounded-2xl sm:rounded-3xl border border-[#dce7f4] bg-white p-3.5 sm:p-5 shadow-[0_20px_50px_rgba(17,54,92,0.18)]"
        aria-label="Pencarian Perjalanan dan Oleh-Oleh"
    >
        <!-- Tab Selector: 2 Modes (Trip & Open PO Oleh-Oleh) -->
        <div class="mb-3.5 flex items-center gap-2 border-b border-slate-100 pb-3">
            <button
                type="button"
                class="inline-flex items-center gap-2 rounded-xl px-4 py-2.5 text-xs sm:text-sm font-bold transition-all duration-200"
                :class="
                    activeTab === 'trip'
                        ? 'bg-[#1677e8] text-white shadow-[0_4px_12px_rgba(22,119,232,0.30)]'
                        : 'bg-[#f1f5f9] text-slate-600 hover:bg-[#e2e8f0] hover:text-slate-900'
                "
                @click="
                    activeTab = 'trip';
                    closeDropdowns();
                    searchMessage = '';
                "
            >
                <Compass class="size-4 shrink-0" />
                <span>Trip</span>
            </button>

            <button
                type="button"
                class="inline-flex items-center gap-2 rounded-xl px-4 py-2.5 text-xs sm:text-sm font-bold transition-all duration-200"
                :class="
                    activeTab === 'souvenir'
                        ? 'bg-[#1677e8] text-white shadow-[0_4px_12px_rgba(22,119,232,0.30)]'
                        : 'bg-[#f1f5f9] text-slate-600 hover:bg-[#e2e8f0] hover:text-slate-900'
                "
                @click="
                    activeTab = 'souvenir';
                    closeDropdowns();
                    searchMessage = '';
                "
            >
                <ShoppingBag class="size-4 shrink-0" />
                <span>Open PO Oleh-Oleh</span>
            </button>
        </div>

        <!-- MODE 1: TRIP SEARCH FORM -->
        <form
            v-if="activeTab === 'trip'"
            class="grid grid-cols-1 gap-3 sm:grid-cols-2 lg:grid-cols-[1.4fr_1.1fr_1.1fr_1fr_auto] lg:items-end"
            @submit.prevent="searchTrips"
        >
            <!-- 1. Lokasi/Destinasi -->
            <div>
                <label for="trip-location-input" class="mb-1.5 block text-xs font-bold text-slate-800">
                    Lokasi/Destinasi
                </label>
                <div
                    class="relative flex h-[46px] items-center gap-2.5 rounded-xl border border-slate-200 bg-white px-3.5 shadow-[0_1px_3px_rgba(0,0,0,0.04)] transition hover:border-slate-300 focus-within:border-[#1677e8] focus-within:ring-2 focus-within:ring-blue-100"
                >
                    <MapPin class="size-4 shrink-0 text-slate-400" />
                    <input
                        id="trip-location-input"
                        ref="tripLocationInput"
                        v-model="tripLocation"
                        type="text"
                        placeholder="Contoh: Yogyakarta, Bali Malang"
                        class="w-full min-w-0 bg-transparent text-xs sm:text-[13px] text-slate-800 placeholder:text-slate-400 focus:outline-none"
                    />
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
            </div>

            <!-- 2. Kategori (Clean & Simple Dropdown) -->
            <div class="relative" data-dropdown>
                <label class="mb-1.5 block text-xs font-bold text-slate-800">
                    Kategori
                </label>
                <button
                    type="button"
                    class="flex h-[46px] w-full items-center justify-between gap-2 rounded-xl border border-slate-200 bg-white px-3 text-left shadow-[0_1px_3px_rgba(0,0,0,0.04)] transition-all duration-150 hover:border-slate-300 hover:bg-slate-50/50 focus:border-[#1677e8] focus:outline-none focus:ring-2 focus:ring-blue-100"
                    :class="openDropdown === 'tripCategory' ? 'border-[#1677e8] ring-2 ring-blue-100' : ''"
                    @click="toggleDropdown('tripCategory')"
                >
                    <div class="flex min-w-0 items-center gap-2">
                        <Footprints class="size-4 shrink-0 text-[#0088ff]" />
                        <span
                            class="truncate text-xs sm:text-[13px] font-semibold text-slate-800"
                        >
                            {{ selectedTripCategoryLabel }}
                        </span>
                    </div>
                    <ChevronDown
                        class="size-4 shrink-0 text-slate-400 transition-transform duration-200"
                        :class="openDropdown === 'tripCategory' ? 'rotate-180 text-[#1677e8]' : ''"
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
                        v-if="openDropdown === 'tripCategory'"
                        class="absolute left-0 top-full z-50 mt-1.5 w-full min-w-[200px] sm:min-w-[220px] rounded-xl border border-slate-200/90 bg-white p-2 shadow-[0_12px_30px_rgba(15,35,70,0.15)] ring-1 ring-black/5"
                    >
                        <div class="space-y-1">
                            <button
                                v-for="opt in tripCategoryOptions"
                                :key="opt.id"
                                type="button"
                                class="flex w-full items-center justify-between gap-2.5 rounded-lg px-3 py-2 text-left transition-all duration-150"
                                :class="
                                    tripCategory === opt.id
                                        ? 'bg-blue-50 text-[#0088ff] font-semibold'
                                        : 'text-slate-700 hover:bg-slate-50 hover:text-slate-900 font-medium'
                                "
                                @click="
                                    tripCategory = opt.id;
                                    closeDropdowns();
                                "
                            >
                                <div class="flex items-center gap-2.5">
                                    <component
                                        :is="opt.icon"
                                        class="size-4.5 shrink-0"
                                        :class="tripCategory === opt.id ? 'text-[#0088ff]' : 'text-slate-400'"
                                    />
                                    <span class="text-xs sm:text-[13px]">
                                        {{ opt.title }}
                                    </span>
                                </div>
                                <Check
                                    v-if="tripCategory === opt.id"
                                    class="size-4 shrink-0 text-[#0088ff]"
                                />
                            </button>
                        </div>
                    </div>
                </Transition>
            </div>

            <!-- 3. Tanggal Berangkat -->
            <div>
                <label for="trip-date-input" class="mb-1.5 block text-xs font-bold text-slate-800">
                    Tanggal Berangkat
                </label>
                <div
                    class="relative flex h-[46px] items-center gap-2 rounded-xl border border-slate-200 bg-white px-3.5 shadow-[0_1px_3px_rgba(0,0,0,0.04)] transition hover:border-slate-300 focus-within:border-[#1677e8] focus-within:ring-2 focus-within:ring-blue-100"
                >
                    <CalendarDays class="size-4 shrink-0 text-slate-400" />
                    <input
                        id="trip-date-input"
                        v-model="tripDate"
                        type="date"
                        class="w-full min-w-0 cursor-pointer bg-transparent text-xs sm:text-[13px] text-slate-800 focus:outline-none"
                        :class="tripDate ? 'text-slate-800 font-medium' : 'text-slate-400'"
                    />
                    <button
                        v-if="tripDate"
                        type="button"
                        class="text-slate-300 hover:text-slate-500"
                        aria-label="Hapus tanggal"
                        @click="tripDate = ''"
                    >
                        <X class="size-3.5" />
                    </button>
                </div>
            </div>

            <!-- 4. Jumlah Tamu / Occupants (Custom Counter Dropdown) -->
            <div class="relative" data-dropdown>
                <label class="mb-1.5 block text-xs font-bold text-slate-800">
                    Jumlah Tamu
                </label>
                <button
                    type="button"
                    class="flex h-[46px] w-full items-center justify-between gap-2 rounded-xl border border-slate-200 bg-white px-3 text-left shadow-[0_1px_3px_rgba(0,0,0,0.04)] transition-all duration-150 hover:border-slate-300 hover:bg-slate-50/50 focus:border-[#1677e8] focus:outline-none focus:ring-2 focus:ring-blue-100"
                    :class="openDropdown === 'tripGuests' ? 'border-[#1677e8] ring-2 ring-blue-100' : ''"
                    @click="toggleDropdown('tripGuests')"
                >
                    <div class="flex min-w-0 items-center gap-2">
                        <Users class="size-4 shrink-0 text-[#0088ff]" />
                        <span class="truncate text-xs sm:text-[13px] font-semibold text-slate-800">
                            {{ selectedGuestsLabel }}
                        </span>
                    </div>
                    <ChevronDown
                        class="size-4 shrink-0 text-slate-400 transition-transform duration-200"
                        :class="openDropdown === 'tripGuests' ? 'rotate-180 text-[#1677e8]' : ''"
                    />
                </button>

                <!-- Dropdown Menu (Compact & Crisp) -->
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
                        class="absolute right-0 top-full z-50 mt-1.5 w-[240px] sm:w-[250px] rounded-xl border border-slate-200/90 bg-white p-3 shadow-[0_12px_30px_rgba(15,35,70,0.15)] ring-1 ring-black/5"
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

            <!-- 5. Tombol Submit -->
            <button
                type="submit"
                class="flex h-[46px] w-full items-center justify-center gap-2 rounded-xl bg-[#0c57c4] px-6 text-xs sm:text-sm font-bold text-white shadow-[0_4px_14px_rgba(12,87,196,0.30)] transition-all duration-200 hover:bg-[#0947a5] hover:shadow-[0_6px_18px_rgba(12,87,196,0.40)] active:scale-[0.98] sm:col-span-2 lg:col-span-1 lg:min-w-[140px]"
            >
                <span>Cari Sekarang</span>
            </button>
        </form>

        <!-- MODE 2: OPEN PO OLEH-OLEH SEARCH FORM -->
        <form
            v-else
            class="grid grid-cols-1 gap-3 sm:grid-cols-2 lg:grid-cols-[1.4fr_1.1fr_1.1fr_1fr_auto] lg:items-end"
            @submit.prevent="searchSouvenirs"
        >
            <!-- 1. Nama Oleh-Oleh / Produk -->
            <div>
                <label for="souvenir-name-input" class="mb-1.5 block text-xs font-bold text-slate-800">
                    Cari Oleh-Oleh
                </label>
                <div
                    class="relative flex h-[46px] items-center gap-2.5 rounded-xl border border-slate-200 bg-white px-3.5 shadow-[0_1px_3px_rgba(0,0,0,0.04)] transition hover:border-slate-300 focus-within:border-[#1677e8] focus-within:ring-2 focus-within:ring-blue-100"
                >
                    <ShoppingBag class="size-4 shrink-0 text-slate-400" />
                    <input
                        id="souvenir-name-input"
                        ref="souvenirKeywordInput"
                        v-model="souvenirKeyword"
                        type="text"
                        placeholder="Contoh: Bakpia, Pie Susu, Strudel"
                        class="w-full min-w-0 bg-transparent text-xs sm:text-[13px] text-slate-800 placeholder:text-slate-400 focus:outline-none"
                    />
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
            </div>

            <!-- 2. Kota / Asal Daerah -->
            <div>
                <label for="souvenir-location-input" class="mb-1.5 block text-xs font-bold text-slate-800">
                    Kota / Asal Daerah
                </label>
                <div
                    class="relative flex h-[46px] items-center gap-2.5 rounded-xl border border-slate-200 bg-white px-3.5 shadow-[0_1px_3px_rgba(0,0,0,0.04)] transition hover:border-slate-300 focus-within:border-[#1677e8] focus-within:ring-2 focus-within:ring-blue-100"
                >
                    <MapPin class="size-4 shrink-0 text-slate-400" />
                    <input
                        id="souvenir-location-input"
                        v-model="souvenirLocation"
                        type="text"
                        placeholder="Contoh: Yogyakarta, Bali, Bandung"
                        class="w-full min-w-0 bg-transparent text-xs sm:text-[13px] text-slate-800 placeholder:text-slate-400 focus:outline-none"
                    />
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
            </div>

            <!-- 3. Kategori Produk (Clean & Simple Dropdown) -->
            <div class="relative" data-dropdown>
                <label class="mb-1.5 block text-xs font-bold text-slate-800">
                    Kategori Produk
                </label>
                <button
                    type="button"
                    class="flex h-[46px] w-full items-center justify-between gap-2 rounded-xl border border-slate-200 bg-white px-3 text-left shadow-[0_1px_3px_rgba(0,0,0,0.04)] transition-all duration-150 hover:border-slate-300 hover:bg-slate-50/50 focus:border-[#1677e8] focus:outline-none focus:ring-2 focus:ring-blue-100"
                    :class="openDropdown === 'souvenirCategory' ? 'border-[#1677e8] ring-2 ring-blue-100' : ''"
                    @click="toggleDropdown('souvenirCategory')"
                >
                    <div class="flex min-w-0 items-center gap-2">
                        <Tag class="size-4 shrink-0 text-[#0088ff]" />
                        <span
                            class="truncate text-xs sm:text-[13px] font-semibold text-slate-800"
                        >
                            {{ selectedSouvenirCategoryLabel }}
                        </span>
                    </div>
                    <ChevronDown
                        class="size-4 shrink-0 text-slate-400 transition-transform duration-200"
                        :class="openDropdown === 'souvenirCategory' ? 'rotate-180 text-[#1677e8]' : ''"
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
                        v-if="openDropdown === 'souvenirCategory'"
                        class="absolute left-0 top-full z-50 mt-1.5 w-full min-w-[200px] sm:min-w-[220px] rounded-xl border border-slate-200/90 bg-white p-2 shadow-[0_12px_30px_rgba(15,35,70,0.15)] ring-1 ring-black/5"
                    >
                        <div class="space-y-1">
                            <button
                                v-for="opt in souvenirCategoryOptions"
                                :key="opt.id"
                                type="button"
                                class="flex w-full items-center justify-between gap-2.5 rounded-lg px-3 py-2 text-left transition-all duration-150"
                                :class="
                                    souvenirCategory === opt.id
                                        ? 'bg-blue-50 text-[#0088ff] font-semibold'
                                        : 'text-slate-700 hover:bg-slate-50 hover:text-slate-900 font-medium'
                                "
                                @click="
                                    souvenirCategory = opt.id;
                                    closeDropdowns();
                                "
                            >
                                <div class="flex items-center gap-2.5">
                                    <component
                                        :is="opt.icon"
                                        class="size-4.5 shrink-0"
                                        :class="souvenirCategory === opt.id ? 'text-[#0088ff]' : 'text-slate-400'"
                                    />
                                    <span class="text-xs sm:text-[13px]">
                                        {{ opt.title }}
                                    </span>
                                </div>
                                <Check
                                    v-if="souvenirCategory === opt.id"
                                    class="size-4 shrink-0 text-[#0088ff]"
                                />
                            </button>
                        </div>
                    </div>
                </Transition>
            </div>

            <!-- 4. Batas PO / Pengiriman -->
            <div>
                <label for="souvenir-date-input" class="mb-1.5 block text-xs font-bold text-slate-800">
                    Batas PO / Tanggal
                </label>
                <div
                    class="relative flex h-[46px] items-center gap-2 rounded-xl border border-slate-200 bg-white px-3.5 shadow-[0_1px_3px_rgba(0,0,0,0.04)] transition hover:border-slate-300 focus-within:border-[#1677e8] focus-within:ring-2 focus-within:ring-blue-100"
                >
                    <CalendarDays class="size-4 shrink-0 text-slate-400" />
                    <input
                        id="souvenir-date-input"
                        v-model="souvenirDate"
                        type="date"
                        class="w-full min-w-0 cursor-pointer bg-transparent text-xs sm:text-[13px] text-slate-800 focus:outline-none"
                        :class="souvenirDate ? 'text-slate-800 font-medium' : 'text-slate-400'"
                    />
                    <button
                        v-if="souvenirDate"
                        type="button"
                        class="text-slate-300 hover:text-slate-500"
                        aria-label="Hapus batas tanggal"
                        @click="souvenirDate = ''"
                    >
                        <X class="size-3.5" />
                    </button>
                </div>
            </div>

            <!-- 5. Tombol Submit -->
            <button
                type="submit"
                class="flex h-[46px] w-full items-center justify-center gap-2 rounded-xl bg-[#0c57c4] px-6 text-xs sm:text-sm font-bold text-white shadow-[0_4px_14px_rgba(12,87,196,0.30)] transition-all duration-200 hover:bg-[#0947a5] hover:shadow-[0_6px_18px_rgba(12,87,196,0.40)] active:scale-[0.98] sm:col-span-2 lg:col-span-1 lg:min-w-[140px]"
            >
                <span>Cari Sekarang</span>
            </button>
        </form>

        <p
            v-if="searchMessage"
            class="mt-2.5 rounded-lg bg-[#edf3ff] px-3.5 py-2 text-xs font-medium text-[#2868df]"
        >
            {{ searchMessage }}
        </p>
    </section>
</template>
