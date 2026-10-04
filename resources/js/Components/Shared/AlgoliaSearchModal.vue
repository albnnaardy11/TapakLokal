<script setup>
import {
    BadgePercent,
    Calendar,
    ChevronRight,
    Compass,
    Crown,
    Landmark,
    MapPin,
    Mountain,
    Palmtree,
    Search,
    Ship,
    ShoppingBag,
    Sparkles,
    Sun,
    TentTree,
    Trees,
    Users,
    X,
} from 'lucide-vue-next';
import { computed, nextTick, onBeforeUnmount, onMounted, ref, watch } from 'vue';
import { router } from '@inertiajs/vue3';
import { route } from 'ziggy-js';

const props = defineProps({
    open: {
        type: Boolean,
        default: false,
    },
    initialQuery: {
        type: String,
        default: '',
    },
});

const emit = defineEmits(['close']);

const query = ref(props.initialQuery || '');
const inputRef = ref(null);
const activeFilter = ref('all'); // 'all' | 'open-trip' | 'private-trip' | 'destination' | 'souvenir'
const loading = ref(false);
const highlightedIndex = ref(-1);

const results = ref({
    query: '',
    trips: [],
    destinations: [],
    souvenirs: [],
    popularDestinations: [],
    quickCategories: [],
});

let debounceTimer = null;

const fetchSuggestions = async (searchTerm = '') => {
    loading.value = true;
    try {
        const url = new URL(typeof route === 'function' ? route('search.suggestions') : '/search/suggestions', window.location.origin);
        if (searchTerm) {
            url.searchParams.set('q', searchTerm);
        }
        const res = await fetch(url.toString(), {
            headers: {
                Accept: 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
            },
        });
        if (res.ok) {
            const data = await res.json();
            results.value = data;
            highlightedIndex.value = -1;
        }
    } catch (err) {
        console.error('Failed to fetch search suggestions:', err);
    } finally {
        loading.value = false;
    }
};

watch(
    () => props.open,
    (isOpen) => {
        if (isOpen) {
            query.value = props.initialQuery || '';
            fetchSuggestions(query.value);
            nextTick(() => {
                inputRef.value?.focus();
            });
            document.body.style.overflow = 'hidden';
        } else {
            document.body.style.overflow = '';
        }
    },
    { immediate: true }
);

watch(query, (newQuery) => {
    clearTimeout(debounceTimer);
    debounceTimer = setTimeout(() => {
        fetchSuggestions(newQuery.trim());
    }, 180);
});

// Filtered flat list of navigable items for keyboard navigation
const flattenedNavigableItems = computed(() => {
    const list = [];

    // 1. Quick Categories (Open Trip / Private Trip direct match)
    if (activeFilter.value === 'all' || activeFilter.value === 'open-trip' || activeFilter.value === 'private-trip') {
        results.value.quickCategories?.forEach((cat) => {
            if (activeFilter.value === 'all' || activeFilter.value === cat.type) {
                list.push({ ...cat, kind: 'category' });
            }
        });
    }

    // 2. Destinations
    if (activeFilter.value === 'all' || activeFilter.value === 'destination') {
        const dests = query.value.trim() ? results.value.destinations : results.value.popularDestinations;
        dests?.forEach((dest) => {
            list.push({ ...dest, kind: 'destination' });
        });
    }

    // 3. Trips
    if (activeFilter.value === 'all' || activeFilter.value === 'open-trip' || activeFilter.value === 'private-trip') {
        results.value.trips?.forEach((trip) => {
            if (activeFilter.value === 'all' || activeFilter.value === trip.type) {
                list.push({ ...trip, kind: 'trip' });
            }
        });
    }

    // 4. Souvenirs
    if (activeFilter.value === 'all' || activeFilter.value === 'souvenir') {
        results.value.souvenirs?.forEach((souvenir) => {
            list.push({ ...souvenir, kind: 'souvenir' });
        });
    }

    return list;
});

const navigateTo = (item) => {
    emit('close');
    if (!item) {
        submitSearch();
        return;
    }
    if (item.url) {
        router.visit(item.url);
    } else if (item.kind === 'destination') {
        router.visit(route('catalog', { q: item.name || item.title }));
    }
};

const submitSearch = () => {
    emit('close');
    const q = query.value.trim();
    if (activeFilter.value === 'open-trip') {
        router.visit(route('trips.category', { type: 'open-trip', q: q || undefined }));
    } else if (activeFilter.value === 'private-trip') {
        router.visit(route('trips.category', { type: 'private-trip', q: q || undefined }));
    } else if (activeFilter.value === 'souvenir') {
        router.visit(route('souvenirs.index', { q: q || undefined }));
    } else {
        router.visit(route('catalog', { q: q || undefined }));
    }
};

const handleKeyDown = (e) => {
    if (!props.open) return;

    if (e.key === 'Escape') {
        e.preventDefault();
        emit('close');
    } else if (e.key === 'ArrowDown') {
        e.preventDefault();
        if (flattenedNavigableItems.value.length > 0) {
            highlightedIndex.value = (highlightedIndex.value + 1) % flattenedNavigableItems.value.length;
            scrollHighlightedIntoView();
        }
    } else if (e.key === 'ArrowUp') {
        e.preventDefault();
        if (flattenedNavigableItems.value.length > 0) {
            highlightedIndex.value = (highlightedIndex.value - 1 + flattenedNavigableItems.value.length) % flattenedNavigableItems.value.length;
            scrollHighlightedIntoView();
        }
    } else if (e.key === 'Enter') {
        e.preventDefault();
        if (highlightedIndex.value >= 0 && flattenedNavigableItems.value[highlightedIndex.value]) {
            navigateTo(flattenedNavigableItems.value[highlightedIndex.value]);
        } else {
            submitSearch();
        }
    }
};

const scrollHighlightedIntoView = () => {
    nextTick(() => {
        const el = document.querySelector('[data-highlighted="true"]');
        if (el) {
            el.scrollIntoView({ block: 'nearest', behavior: 'smooth' });
        }
    });
};

const selectPopularChip = (destName) => {
    query.value = destName;
    fetchSuggestions(destName);
};

// Global Ctrl+K / Cmd+K listener
const handleGlobalShortcut = (e) => {
    if ((e.metaKey || e.ctrlKey) && e.key.toLowerCase() === 'k') {
        e.preventDefault();
        if (props.open) {
            emit('close');
        } else {
            // Can be opened by parent
        }
    }
};

onMounted(() => {
    window.addEventListener('keydown', handleKeyDown);
    window.addEventListener('keydown', handleGlobalShortcut);
});

onBeforeUnmount(() => {
    window.removeEventListener('keydown', handleKeyDown);
    window.removeEventListener('keydown', handleGlobalShortcut);
    clearTimeout(debounceTimer);
    document.body.style.overflow = '';
});
</script>

<template>
    <Teleport to="body">
        <Transition
            enter-active-class="transition duration-200 ease-out"
            enter-from-class="opacity-0"
            enter-to-class="opacity-100"
            leave-active-class="transition duration-150 ease-in"
            leave-from-class="opacity-100"
            leave-to-class="opacity-0"
        >
            <div
                v-if="open"
                class="fixed inset-0 z-50 flex items-end sm:items-start justify-center p-0 sm:p-4 md:p-6 lg:p-10 bg-slate-900/60 backdrop-blur-md"
                role="dialog"
                aria-modal="true"
                aria-label="Pencarian Cepat TapakLokal"
                @click.self="emit('close')"
            >
                <div
                    class="relative flex flex-col w-full max-w-3xl h-[100dvh] sm:h-auto sm:max-h-[85vh] overflow-hidden rounded-t-3xl sm:rounded-2xl bg-white shadow-2xl ring-1 ring-black/10 transition-all sm:my-auto"
                    @click.stop
                >
                    <!-- Mobile Drag Indicator -->
                    <div class="sm:hidden pt-2 pb-1 bg-white flex justify-center">
                        <div class="w-10 h-1 rounded-full bg-slate-200"></div>
                    </div>

                    <!-- 1. Search Input Bar Header -->
                    <div class="relative flex items-center border-b border-slate-100 px-3.5 py-3 sm:px-6">
                        <Search class="size-4.5 sm:size-5 shrink-0 text-[#0066cc]" aria-hidden="true" />
                        <input
                            ref="inputRef"
                            v-model="query"
                            type="search"
                            autocomplete="off"
                            spellcheck="false"
                            placeholder="Cari open trip, private trip, destinasi..."
                            class="w-full bg-transparent px-2.5 sm:px-3 text-sm sm:text-base font-semibold text-slate-800 placeholder:text-slate-400 focus:outline-none"
                            aria-label="Kata kunci pencarian"
                            @keydown.enter.prevent="submitSearch"
                        />

                        <!-- Loading indicator / Clear button / Mobile Close / Desktop ESC -->
                        <div class="flex items-center gap-1.5 sm:gap-2 shrink-0">
                            <span
                                v-if="loading"
                                class="size-4 animate-spin rounded-full border-2 border-sky-200 border-t-[#0088ff]"
                                aria-hidden="true"
                            ></span>
                            <button
                                v-if="query"
                                type="button"
                                class="flex size-6 items-center justify-center rounded-full text-slate-400 hover:bg-slate-100 hover:text-slate-600 transition"
                                aria-label="Hapus teks"
                                @click="query = ''; inputRef?.focus()"
                            >
                                <X class="size-3.5 sm:size-4" />
                            </button>
                            <button
                                type="button"
                                class="sm:hidden rounded-lg px-2 py-1 text-xs font-bold text-[#0066cc] hover:bg-sky-50 transition"
                                @click="emit('close')"
                            >
                                Tutup
                            </button>
                            <kbd
                                class="hidden sm:inline-flex items-center rounded-md border border-slate-200 bg-slate-50 px-2 py-0.5 text-[10px] font-bold text-slate-500 shadow-xs cursor-pointer hover:bg-slate-100"
                                @click="emit('close')"
                            >
                                ESC
                            </kbd>
                        </div>
                    </div>

                    <!-- 2. Filter Category Pills -->
                    <div class="flex items-center gap-1.5 sm:gap-2 border-b border-slate-100 bg-slate-50/70 px-3.5 sm:px-6 py-2 overflow-x-auto no-scrollbar text-xs shrink-0">
                        <button
                            type="button"
                            class="shrink-0 rounded-full px-3 py-1 font-semibold transition"
                            :class="activeFilter === 'all' ? 'bg-[#0066cc] text-white shadow-xs' : 'bg-white text-slate-600 border border-slate-200 hover:bg-slate-100'"
                            @click="activeFilter = 'all'"
                        >
                            Semua
                        </button>
                        <button
                            type="button"
                            class="shrink-0 inline-flex items-center gap-1 rounded-full px-3 py-1 font-semibold transition"
                            :class="activeFilter === 'open-trip' ? 'bg-[#0066cc] text-white shadow-xs' : 'bg-white text-slate-600 border border-slate-200 hover:bg-slate-100'"
                            @click="activeFilter = 'open-trip'"
                        >
                            <TentTree class="size-3.5" />
                            <span>Open Trip</span>
                        </button>
                        <button
                            type="button"
                            class="shrink-0 inline-flex items-center gap-1 rounded-full px-3 py-1 font-semibold transition"
                            :class="activeFilter === 'private-trip' ? 'bg-[#0066cc] text-white shadow-xs' : 'bg-white text-slate-600 border border-slate-200 hover:bg-slate-100'"
                            @click="activeFilter = 'private-trip'"
                        >
                            <Crown class="size-3.5" />
                            <span>Private Trip</span>
                        </button>
                        <button
                            type="button"
                            class="shrink-0 inline-flex items-center gap-1 rounded-full px-3 py-1 font-semibold transition"
                            :class="activeFilter === 'destination' ? 'bg-[#0066cc] text-white shadow-xs' : 'bg-white text-slate-600 border border-slate-200 hover:bg-slate-100'"
                            @click="activeFilter = 'destination'"
                        >
                            <MapPin class="size-3.5" />
                            <span>Destinasi</span>
                        </button>
                        <button
                            type="button"
                            class="shrink-0 inline-flex items-center gap-1 rounded-full px-3 py-1 font-semibold transition"
                            :class="activeFilter === 'souvenir' ? 'bg-[#0066cc] text-white shadow-xs' : 'bg-white text-slate-600 border border-slate-200 hover:bg-slate-100'"
                            @click="activeFilter = 'souvenir'"
                        >
                            <ShoppingBag class="size-3.5" />
                            <span>Oleh-Oleh</span>
                        </button>
                    </div>

                    <!-- 3. Search Results Content (Scrollable Container) -->
                    <div class="flex-1 overflow-y-auto p-3.5 sm:p-6 space-y-5 custom-scrollbar">
                        <!-- Direct Navigation Categories (Traveloka / Algolia Style) -->
                        <div v-if="results.quickCategories?.length && (activeFilter === 'all' || activeFilter === 'open-trip' || activeFilter === 'private-trip')" class="space-y-2">
                            <p class="text-[11px] font-bold uppercase tracking-wider text-slate-400">
                                {{ query ? `Jelajahi Kategori untuk "${query}"` : 'Kategori Pilihan' }}
                            </p>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                                <a
                                    v-for="(cat, idx) in results.quickCategories"
                                    :key="cat.type"
                                    v-show="activeFilter === 'all' || activeFilter === cat.type"
                                    :href="cat.url"
                                    class="group flex items-center justify-between gap-2 p-2.5 sm:p-3 rounded-xl border border-slate-100 bg-gradient-to-r hover:border-sky-300 hover:from-sky-50/80 hover:to-blue-50/40 transition cursor-pointer"
                                    :class="cat.type === 'open-trip' ? 'from-blue-50/50 to-white' : 'from-indigo-50/40 to-white'"
                                    @click.prevent="navigateTo(cat)"
                                >
                                    <div class="flex items-center gap-2.5 min-w-0">
                                        <div
                                            class="grid size-8 sm:size-9 shrink-0 place-items-center rounded-lg text-white"
                                            :class="cat.type === 'open-trip' ? 'bg-[#0088ff]' : (cat.type === 'private-trip' ? 'bg-amber-500' : 'bg-emerald-500')"
                                        >
                                            <TentTree v-if="cat.type === 'open-trip'" class="size-4.5 sm:size-5" />
                                            <Crown v-else-if="cat.type === 'private-trip'" class="size-4.5 sm:size-5" />
                                            <ShoppingBag v-else class="size-4.5 sm:size-5" />
                                        </div>
                                        <div class="min-w-0">
                                            <span class="block truncate text-xs font-bold text-slate-800 group-hover:text-[#0066cc]">
                                                {{ cat.title }}
                                            </span>
                                            <span class="block truncate text-[10px] text-slate-500">
                                                {{ cat.description }}
                                            </span>
                                        </div>
                                    </div>
                                    <span class="shrink-0 rounded-full bg-sky-100/80 px-2 py-0.5 text-[9px] font-bold text-[#0066cc]">
                                        {{ cat.badge }}
                                    </span>
                                </a>
                            </div>
                        </div>

                        <!-- Destinasi Populer / Matching Destinations -->
                        <div v-if="(activeFilter === 'all' || activeFilter === 'destination') && (results.destinations?.length || results.popularDestinations?.length)">
                            <div class="mb-2 flex items-center justify-between">
                                <p class="text-[11px] font-bold uppercase tracking-wider text-slate-400">
                                    {{ query ? 'Destinasi Terkait' : 'Destinasi Populer' }}
                                </p>
                            </div>
                            <div class="grid grid-cols-2 sm:grid-cols-4 gap-2">
                                <button
                                    v-for="dest in (query ? results.destinations : results.popularDestinations)"
                                    :key="dest.id || dest.name"
                                    type="button"
                                    class="group flex items-center gap-2 rounded-xl border border-slate-100 bg-white p-2 sm:p-2.5 text-left transition hover:border-[#8bc5ff] hover:bg-sky-50/50 hover:shadow-xs focus:outline-none focus-visible:ring-2 focus-visible:ring-[#0088ff]"
                                    @click="navigateTo({ ...dest, kind: 'destination' })"
                                >
                                    <div class="grid size-7 sm:size-8 shrink-0 place-items-center rounded-lg bg-sky-100 text-[#0088ff] group-hover:bg-[#0088ff] group-hover:text-white transition">
                                        <MapPin class="size-3.5 sm:size-4" />
                                    </div>
                                    <div class="min-w-0 flex-1">
                                        <span class="block truncate text-xs font-bold text-slate-800 group-hover:text-[#0066cc]">
                                            {{ dest.title || dest.name }}
                                        </span>
                                        <span class="block truncate text-[10px] text-slate-400">
                                            {{ dest.region || 'Indonesia' }}
                                        </span>
                                    </div>
                                </button>
                            </div>
                        </div>

                        <!-- Paket Trip Hasil Pencarian -->
                        <div v-if="results.trips?.length && (activeFilter === 'all' || activeFilter === 'open-trip' || activeFilter === 'private-trip')">
                            <div class="mb-2.5 flex items-center justify-between">
                                <p class="text-[11px] font-bold uppercase tracking-wider text-slate-400">
                                    Paket Trip Tersedia ({{ results.trips.length }})
                                </p>
                                <button
                                    type="button"
                                    class="text-xs font-bold text-[#0066cc] hover:underline"
                                    @click="submitSearch"
                                >
                                    Lihat Semua →
                                </button>
                            </div>
                            <div class="space-y-2">
                                <a
                                    v-for="trip in results.trips"
                                    :key="trip.id"
                                    v-show="activeFilter === 'all' || activeFilter === trip.type"
                                    :href="trip.url"
                                    class="group flex items-center gap-2.5 sm:gap-3.5 rounded-xl border border-slate-100 bg-white p-2 sm:p-2.5 transition hover:border-[#8bc5ff] hover:bg-sky-50/40 hover:shadow-sm"
                                    @click.prevent="navigateTo({ ...trip, kind: 'trip' })"
                                >
                                    <img
                                        :src="trip.image_url"
                                        :alt="trip.title"
                                        width="80"
                                        height="60"
                                        loading="lazy"
                                        class="size-13 sm:size-16 rounded-lg object-cover shrink-0 bg-slate-100"
                                    />
                                    <div class="min-w-0 flex-1">
                                        <div class="flex items-center gap-1.5">
                                            <span
                                                class="rounded px-1.5 py-0.5 text-[9px] font-extrabold"
                                                :class="trip.type === 'open-trip' ? 'bg-sky-100 text-[#0066cc]' : 'bg-amber-100 text-amber-800'"
                                            >
                                                {{ trip.type_label }}
                                            </span>
                                            <span class="truncate text-[10px] text-slate-400">
                                                {{ trip.destination }}
                                            </span>
                                        </div>
                                        <h4 class="mt-0.5 truncate text-xs sm:text-sm font-bold text-slate-800 group-hover:text-[#0066cc] transition-colors">
                                            {{ trip.title }}
                                        </h4>
                                        <div class="mt-0.5 flex items-center gap-2 text-[11px]">
                                            <span class="font-extrabold text-[#ff5e1f]">
                                                {{ trip.formatted_price }}
                                                <span class="text-[9px] font-normal text-slate-400">/pax</span>
                                            </span>
                                            <span v-if="trip.vendor_name" class="text-slate-400 truncate hidden sm:inline">
                                                · {{ trip.vendor_name }}
                                            </span>
                                        </div>
                                    </div>
                                    <ChevronRight class="size-4 shrink-0 text-slate-300 group-hover:text-[#0066cc] group-hover:translate-x-0.5 transition" />
                                </a>
                            </div>
                        </div>

                        <!-- Oleh-Oleh Hasil Pencarian -->
                        <div v-if="results.souvenirs?.length && (activeFilter === 'all' || activeFilter === 'souvenir')">
                            <p class="mb-2 text-[11px] font-bold uppercase tracking-wider text-slate-400">
                                Oleh-Oleh Khas Daerah
                            </p>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                                <a
                                    v-for="souvenir in results.souvenirs"
                                    :key="souvenir.id"
                                    :href="souvenir.url"
                                    class="group flex items-center gap-2.5 rounded-xl border border-slate-100 bg-white p-2 sm:p-2.5 transition hover:border-[#8bc5ff] hover:bg-sky-50/40"
                                    @click.prevent="navigateTo({ ...souvenir, kind: 'souvenir' })"
                                >
                                    <img
                                        :src="souvenir.image_url || '/Assets/Images/account/souvenir-fallback.svg'"
                                        :alt="souvenir.name"
                                        width="56"
                                        height="56"
                                        loading="lazy"
                                        class="size-11 sm:size-12 rounded-lg object-cover shrink-0 bg-slate-100"
                                    />
                                    <div class="min-w-0 flex-1">
                                        <span class="block truncate text-xs font-bold text-slate-800 group-hover:text-[#0066cc]">
                                            {{ souvenir.name }}
                                        </span>
                                        <span class="block truncate text-[10px] text-slate-400">
                                            Asal: {{ souvenir.city }}
                                        </span>
                                        <span class="text-[11px] font-bold text-[#ff5e1f]">
                                            {{ souvenir.formatted_price }}
                                        </span>
                                    </div>
                                </a>
                            </div>
                        </div>

                        <!-- Empty State when no results found -->
                        <div
                            v-if="!loading && query && !results.trips?.length && !results.destinations?.length && !results.souvenirs?.length"
                            class="py-8 text-center px-4"
                        >
                            <Compass class="mx-auto size-10 text-slate-300 mb-2" />
                            <p class="text-sm font-bold text-slate-700">
                                Tidak ada trip atau destinasi yang cocok dengan "{{ query }}"
                            </p>
                            <p class="mt-1 text-xs text-slate-400">
                                Coba kata kunci lain seperti "Bromo", "Bali", "Open Trip", atau "Salak".
                            </p>
                            <button
                                type="button"
                                class="mt-4 inline-flex items-center gap-2 rounded-xl bg-[#0088ff] px-4 py-2 text-xs font-bold text-white shadow-md hover:bg-[#0074e0] transition"
                                @click="submitSearch"
                            >
                                <Search class="size-4" />
                                <span>Cari di Seluruh Katalog</span>
                            </button>
                        </div>
                    </div>

                    <!-- 4. Footer -->
                    <!-- Mobile Footer -->
                    <div class="flex sm:hidden items-center justify-between border-t border-slate-100 bg-slate-50 px-4 py-2.5 text-xs text-slate-500 shrink-0">
                        <span class="text-[11px] text-slate-400">Pencarian TapakLokal</span>
                        <button
                            type="button"
                            class="text-xs font-bold text-[#0066cc]"
                            @click="submitSearch"
                        >
                            Lihat Semua Hasil →
                        </button>
                    </div>

                    <!-- Desktop Footer with Keyboard Tips -->
                    <div class="hidden sm:flex items-center justify-between border-t border-slate-100 bg-slate-50 px-6 py-2.5 text-[11px] text-slate-400 shrink-0">
                        <div class="flex items-center gap-3">
                            <span class="inline-flex items-center gap-1">
                                <kbd class="rounded border border-slate-200 bg-white px-1.5 py-0.5 font-mono text-[9px] shadow-2xs">↵</kbd>
                                Pilih
                            </span>
                            <span class="inline-flex items-center gap-1">
                                <kbd class="rounded border border-slate-200 bg-white px-1.5 py-0.5 font-mono text-[9px] shadow-2xs">↑</kbd>
                                <kbd class="rounded border border-slate-200 bg-white px-1.5 py-0.5 font-mono text-[9px] shadow-2xs">↓</kbd>
                                Navigasi
                            </span>
                            <span class="inline-flex items-center gap-1">
                                <kbd class="rounded border border-slate-200 bg-white px-1.5 py-0.5 font-mono text-[9px] shadow-2xs">ESC</kbd>
                                Tutup
                            </span>
                        </div>
                        <span class="font-bold text-[#0066cc]">TapakLokal Search Engine</span>
                    </div>
                </div>
            </div>
        </Transition>
    </Teleport>
</template>

<style scoped>
.custom-scrollbar::-webkit-scrollbar {
    width: 6px;
}
.custom-scrollbar::-webkit-scrollbar-track {
    background: #f8fafc;
}
.custom-scrollbar::-webkit-scrollbar-thumb {
    background: #cbd5e1;
    border-radius: 9999px;
}
.custom-scrollbar::-webkit-scrollbar-thumb:hover {
    background: #94a3b8;
}

.no-scrollbar::-webkit-scrollbar {
    display: none;
}
.no-scrollbar {
    -ms-overflow-style: none;
    scrollbar-width: none;
}
</style>
