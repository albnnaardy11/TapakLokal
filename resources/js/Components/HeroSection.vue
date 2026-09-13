<script setup>
import { ref } from 'vue';
import {
    Briefcase,
    ShoppingBag,
    MapPin,
    Calendar,
    Users,
    ChevronDown,
    Search,
    Sparkles,
    CheckCircle2
} from 'lucide-vue-next';

const props = defineProps({
    categories: {
        type: Array,
        default: () => [
            'Semua Kategori',
            'Open Trip Alam & Gunung',
            'Island Hopping & Bahari',
            'Budaya & Desa Adat',
            'Kuliner Trail Otentik',
        ],
    },
});

const emit = defineEmits(['search']);

const activeTab = ref('trip'); // 'trip' | 'po'
const location = ref('');
const selectedCategory = ref('');
const departureDate = ref('');
const personCount = ref(12);
const isCategoryDropdownOpen = ref(false);
const isPersonDropdownOpen = ref(false);

function selectTab(tab) {
    activeTab.value = tab;
}

function handleSearch() {
    emit('search', {
        type: activeTab.value,
        location: location.value,
        category: selectedCategory.value,
        departureDate: departureDate.value,
        personCount: personCount.value,
    });
}
</script>

<template>
    <section class="relative pt-2 sm:pt-4 md:pt-6 pb-8 sm:pb-12">
        <div class="max-w-7xl mx-auto px-3 sm:px-6 lg:px-8">
            <!-- Hero Banner Container (Large Rounded Corners like screenshot) -->
            <div class="relative rounded-2xl sm:rounded-3xl md:rounded-[36px] overflow-hidden min-h-[360px] sm:min-h-[440px] md:min-h-[500px] lg:min-h-[540px] flex items-center justify-center text-center shadow-2xl">
                <!-- Background Image: Pura Ulun Danu Beratan Bali at Twilight -->
                <div class="absolute inset-0 z-0">
                    <img
                        src="https://images.unsplash.com/photo-1537996194471-e657df975ab4?auto=format&fit=crop&w=2000&q=85"
                        alt="Pura Ulun Danu Beratan Bali - TapakLokal"
                        class="w-full h-full object-cover object-center transform scale-105 transition duration-1000"
                    />
                    <!-- Gradients & Vignette for high contrast and readability -->
                    <div class="absolute inset-0 bg-gradient-to-b from-black/55 via-black/45 to-black/80"></div>
                    <div class="absolute inset-0 bg-radial-gradient from-transparent via-black/20 to-black/60"></div>
                </div>

                <!-- Content Center -->
                <div class="relative z-10 max-w-3xl mx-auto px-4 sm:px-6 pt-6 sm:pt-10 pb-24 sm:pb-28 md:pb-36 text-white">
                    <!-- Main Headline -->
                    <h1 class="text-2xl sm:text-4xl md:text-5xl lg:text-6xl font-extrabold tracking-tight leading-[1.2] drop-shadow-md">
                        Jelajahi Indonesia<br />
                        Secara Otentik.
                    </h1>

                    <!-- Subtitle -->
                    <p class="mt-3 sm:mt-4 md:mt-5 text-xs sm:text-sm md:text-base text-slate-100/90 font-normal leading-relaxed max-w-2xl mx-auto drop-shadow">
                        Destinasi tersembunyi, kuliner jujur, pemandu lokal terverifikasi - tanpa biaya terselubung, langsung berdampak ke komunitas.
                    </p>
                </div>
            </div>

            <!-- Floating Search Widget (Overlapping Hero bottom) -->
            <div class="max-w-5xl mx-auto -mt-16 sm:-mt-22 md:-mt-26 lg:-mt-28 relative z-20 px-1 sm:px-4">
                <div class="bg-white rounded-2xl sm:rounded-3xl shadow-2xl border border-gray-100 p-3.5 sm:p-5 md:p-6 backdrop-blur-xs">
                    <!-- Top Tab Switcher -->
                    <div class="flex items-center justify-center gap-2 sm:gap-4 mb-3.5 sm:mb-5 border-b border-gray-100 pb-3">
                        <!-- Tab 1: Paket Trip (Wisata) -->
                        <button
                            type="button"
                            @click="selectTab('trip')"
                            :class="[
                                'flex-1 sm:flex-initial flex items-center justify-center gap-1.5 sm:gap-2 px-3 sm:px-5 py-2 sm:py-2.5 rounded-full text-xs sm:text-sm font-bold transition cursor-pointer',
                                activeTab === 'trip'
                                    ? 'bg-blue-50 text-[#0052cc] ring-1 ring-blue-200 shadow-2xs'
                                    : 'text-slate-600 hover:text-slate-900 hover:bg-gray-50'
                            ]"
                        >
                            <div :class="['w-5 h-5 sm:w-6 sm:h-6 rounded-full flex items-center justify-center shrink-0', activeTab === 'trip' ? 'bg-[#0052cc] text-white' : 'bg-blue-100 text-[#0052cc]']">
                                <Briefcase class="w-3 h-3 sm:w-3.5 sm:h-3.5" />
                            </div>
                            <span class="whitespace-nowrap">Paket Trip (Wisata)</span>
                        </button>

                        <!-- Tab 2: Open PO (Oleh-Oleh) -->
                        <button
                            type="button"
                            @click="selectTab('po')"
                            :class="[
                                'flex-1 sm:flex-initial flex items-center justify-center gap-1.5 sm:gap-2 px-3 sm:px-5 py-2 sm:py-2.5 rounded-full text-xs sm:text-sm font-bold transition cursor-pointer',
                                activeTab === 'po'
                                    ? 'bg-orange-50 text-orange-600 ring-1 ring-orange-200 shadow-2xs'
                                    : 'text-slate-600 hover:text-slate-900 hover:bg-gray-50'
                            ]"
                        >
                            <div :class="['w-5 h-5 sm:w-6 sm:h-6 rounded-full flex items-center justify-center shrink-0', activeTab === 'po' ? 'bg-orange-500 text-white' : 'bg-orange-100 text-orange-600']">
                                <ShoppingBag class="w-3 h-3 sm:w-3.5 sm:h-3.5" />
                            </div>
                            <span class="whitespace-nowrap">Open PO (Oleh-Oleh)</span>
                        </button>
                    </div>

                    <!-- Search Input Form Row (Responsive: 1 col on phone, 2 cols on tablet, 12 cols on desktop) -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-12 gap-2.5 sm:gap-3 lg:gap-4 items-center">
                        <!-- 1. Lokasi / Destinasi -->
                        <div class="sm:col-span-1 lg:col-span-4 bg-gray-50/90 hover:bg-gray-50 border border-gray-200/80 rounded-xl px-3 py-2 sm:px-3.5 sm:py-2.5 transition focus-within:ring-2 focus-within:ring-[#0052cc] focus-within:border-transparent">
                            <label class="block text-[10px] sm:text-[11px] font-bold text-slate-700 tracking-wide uppercase">
                                Lokasi/Destinasi
                            </label>
                            <div class="flex items-center gap-2 mt-1">
                                <MapPin class="w-4 h-4 text-slate-400 shrink-0" />
                                <input
                                    v-model="location"
                                    type="text"
                                    placeholder="Contoh: Yogyakarta, Bali, Malang"
                                    class="w-full bg-transparent text-xs sm:text-sm text-slate-800 placeholder-slate-400 border-none p-0 focus:outline-none focus:ring-0 font-medium"
                                />
                            </div>
                        </div>

                        <!-- 2. Kategori / Jenis PO -->
                        <div class="sm:col-span-1 lg:col-span-3 relative bg-gray-50/90 hover:bg-gray-50 border border-gray-200/80 rounded-xl px-3 py-2 sm:px-3.5 sm:py-2.5 transition">
                            <label class="block text-[10px] sm:text-[11px] font-bold text-slate-700 tracking-wide uppercase">
                                Kategori
                            </label>
                            <div
                                @click="isCategoryDropdownOpen = !isCategoryDropdownOpen"
                                class="flex items-center justify-between gap-2 mt-1 cursor-pointer"
                            >
                                <div class="flex items-center gap-2 truncate">
                                    <Sparkles class="w-4 h-4 text-slate-400 shrink-0" />
                                    <span class="text-xs sm:text-sm font-medium text-slate-800 truncate">
                                        {{ selectedCategory || 'Pilih Kategori' }}
                                    </span>
                                </div>
                                <ChevronDown class="w-4 h-4 text-slate-400 shrink-0" />
                            </div>

                            <!-- Dropdown Menu -->
                            <div
                                v-if="isCategoryDropdownOpen"
                                class="absolute left-0 right-0 top-full mt-2 bg-white rounded-xl shadow-xl border border-gray-100 py-2 z-30 max-h-56 overflow-y-auto"
                            >
                                <button
                                    v-for="cat in categories"
                                    :key="cat"
                                    type="button"
                                    @click="selectedCategory = cat; isCategoryDropdownOpen = false"
                                    class="w-full text-left px-4 py-2 text-xs font-medium text-slate-700 hover:bg-blue-50 hover:text-[#0052cc] transition"
                                >
                                    {{ cat }}
                                </button>
                            </div>
                        </div>

                        <!-- 3. Tanggal Berangkat -->
                        <div class="sm:col-span-1 lg:col-span-2 bg-gray-50/90 hover:bg-gray-50 border border-gray-200/80 rounded-xl px-3 py-2 sm:px-3.5 sm:py-2.5 transition focus-within:ring-2 focus-within:ring-[#0052cc] focus-within:border-transparent">
                            <label class="block text-[10px] sm:text-[11px] font-bold text-slate-700 tracking-wide uppercase">
                                Tanggal Berangkat
                            </label>
                            <div class="flex items-center gap-2 mt-1">
                                <Calendar class="w-4 h-4 text-slate-400 shrink-0" />
                                <input
                                    v-model="departureDate"
                                    type="date"
                                    class="w-full bg-transparent text-xs text-slate-800 border-none p-0 focus:outline-none focus:ring-0 font-medium"
                                />
                            </div>
                        </div>

                        <!-- 4. Jumlah Orang -->
                        <div class="sm:col-span-1 lg:col-span-2 relative bg-gray-50/90 hover:bg-gray-50 border border-gray-200/80 rounded-xl px-3 py-2 sm:px-3.5 sm:py-2.5 transition">
                            <label class="block text-[10px] sm:text-[11px] font-bold text-slate-700 tracking-wide uppercase">
                                Jumlah Orang
                            </label>
                            <div
                                @click="isPersonDropdownOpen = !isPersonDropdownOpen"
                                class="flex items-center justify-between gap-2 mt-1 cursor-pointer"
                            >
                                <div class="flex items-center gap-2">
                                    <Users class="w-4 h-4 text-slate-400 shrink-0" />
                                    <span class="text-xs sm:text-sm font-medium text-slate-800">
                                        {{ personCount }} Orang
                                    </span>
                                </div>
                                <ChevronDown class="w-4 h-4 text-slate-400 shrink-0" />
                            </div>

                            <!-- Dropdown Menu -->
                            <div
                                v-if="isPersonDropdownOpen"
                                class="absolute left-0 right-0 top-full mt-2 bg-white rounded-xl shadow-xl border border-gray-100 py-2 z-30 max-h-48 overflow-y-auto"
                            >
                                <button
                                    v-for="count in [1, 2, 4, 6, 8, 10, 12, 16, 20]"
                                    :key="count"
                                    type="button"
                                    @click="personCount = count; isPersonDropdownOpen = false"
                                    class="w-full text-left px-4 py-2 text-xs font-medium text-slate-700 hover:bg-blue-50 hover:text-[#0052cc] transition"
                                >
                                    {{ count }} Orang
                                </button>
                            </div>
                        </div>

                        <!-- 5. Tombol Cari Sekarang -->
                        <div class="sm:col-span-2 lg:col-span-1 flex items-stretch">
                            <button
                                type="button"
                                @click="handleSearch"
                                class="w-full bg-[#0047ba] hover:bg-[#003894] active:scale-95 text-white font-bold py-3 sm:py-3.5 px-4 rounded-xl text-xs sm:text-sm flex items-center justify-center gap-1.5 transition shadow-md shadow-blue-500/20 whitespace-nowrap cursor-pointer"
                            >
                                <span>Cari Sekarang</span>
                            </button>
                        </div>
                    </div>

                    <!-- Quick Trend Badges (Scrollable horizontally on phone) -->
                    <div class="mt-3.5 sm:mt-4 pt-3 border-t border-gray-100 flex items-center gap-2 text-xs text-slate-500 overflow-x-auto scrollbar-none pb-0.5">
                        <span class="font-bold text-slate-700 shrink-0 text-[11px] sm:text-xs">Populer:</span>
                        <div class="flex items-center gap-1.5 shrink-0">
                            <button
                                v-for="tag in ['Bromo Midnight', 'Labuan Bajo', 'Tumpak Sewu', 'Dieng', 'Bakpia Kukus', 'Pie Susu Bali']"
                                :key="tag"
                                type="button"
                                @click="location = tag; handleSearch()"
                                class="px-2.5 py-1 bg-slate-100 hover:bg-blue-50 hover:text-[#0052cc] rounded-lg font-medium transition cursor-pointer text-[11px] whitespace-nowrap"
                            >
                                {{ tag }}
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</template>
