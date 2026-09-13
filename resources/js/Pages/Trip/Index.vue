<script setup>
import { ref, computed } from 'vue';
import { Head, Link } from '@inertiajs/vue3';
import Navbar from '@/Components/Navbar.vue';
import BottomNav from '@/Components/BottomNav.vue';
import Footer from '@/Components/Footer.vue';
import TripDetailModal from '@/Components/TripDetailModal.vue';
import {
    Compass,
    MapPin,
    Calendar,
    Users,
    Star,
    Clock,
    ShieldCheck,
    ArrowRight,
    Search,
    SlidersHorizontal,
    Heart,
    Flame,
    X,
    CheckCircle2,
    Sparkles
} from 'lucide-vue-next';

const props = defineProps({
    appName: {
        type: String,
        default: 'TapakLokal',
    },
    user: {
        type: Object,
        default: () => ({
            name: 'albnnaardy',
            handle: '@petualangnyasar',
            points: 0,
            tier: 'Petualang Perintis',
        }),
    },
    regions: {
        type: Array,
        default: () => ['Semua Wilayah', 'Jawa', 'Bali & Nusa Tenggara', 'Sulawesi', 'Papua & Maluku'],
    },
    trips: {
        type: Array,
        default: () => [],
    },
});

const selectedRegion = ref('Semua Wilayah');
const selectedDifficulty = ref('Semua');
const searchQuery = ref('');
const sortBy = ref('popular');
const bookmarkedIds = ref(new Set());
const activeTripModal = ref(null);
const toastMessage = ref(null);

function showToast(msg) {
    toastMessage.value = msg;
    setTimeout(() => {
        toastMessage.value = null;
    }, 3500);
}

function toggleWishlist(id) {
    if (bookmarkedIds.value.has(id)) {
        bookmarkedIds.value.delete(id);
        showToast('Trip dihapus dari wishlist.');
    } else {
        bookmarkedIds.value.add(id);
        showToast('Trip berhasil disimpan ke wishlist!');
    }
}

function formatRupiah(number) {
    return new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: 'IDR',
        maximumFractionDigits: 0,
    }).format(number);
}

const filteredTrips = computed(() => {
    return props.trips.filter((trip) => {
        const matchesRegion = selectedRegion.value === 'Semua Wilayah' || trip.region === selectedRegion.value;
        const matchesDiff = selectedDifficulty.value === 'Semua' || trip.difficulty === selectedDifficulty.value;
        const matchesQuery = !searchQuery.value ||
            trip.title.toLowerCase().includes(searchQuery.value.toLowerCase()) ||
            trip.location.toLowerCase().includes(searchQuery.value.toLowerCase());
        return matchesRegion && matchesDiff && matchesQuery;
    }).sort((a, b) => {
        if (sortBy.value === 'price-low') return a.price - b.price;
        if (sortBy.value === 'price-high') return b.price - a.price;
        if (sortBy.value === 'rating') return b.rating - a.rating;
        return b.review_count - a.review_count;
    });
});

function handleBookTrip(bookingData) {
    showToast(`Pemesanan ${bookingData.bookingType === 'open' ? 'Open Trip' : 'Private Trip'} "${bookingData.trip.title}" (${bookingData.paxCount} Pax) berhasil dicatat!`);
    activeTripModal.value = null;
}
</script>

<template>
    <Head>
        <title>Open Trip Otentik Nusantara - TapakLokal</title>
        <meta name="description" content="Temukan jadwal open trip terbaik ke seluruh penjuru Indonesia bersama pemandu lokal terverifikasi (Mitra Akamsi)." />
    </Head>

    <div class="min-h-screen flex flex-col bg-slate-50 font-sans text-slate-800 antialiased selection:bg-[#0052cc] selection:text-white">
        <Navbar :user="user" active-menu="Open Trip" />

        <main class="flex-1">
            <!-- Hero Section Selaras -->
            <section class="relative pt-2 sm:pt-4 md:pt-6 pb-8 sm:pb-12">
                <div class="max-w-7xl mx-auto px-3 sm:px-6 lg:px-8">
                    <div class="relative rounded-2xl sm:rounded-3xl md:rounded-[36px] overflow-hidden min-h-[380px] sm:min-h-[440px] md:min-h-[480px] flex items-center justify-center text-center shadow-2xl">
                        <!-- Hero Image: Indonesian mountain sunrise -->
                        <div class="absolute inset-0 z-0">
                            <img
                                src="https://images.unsplash.com/photo-1518548419970-58e3b4079ab2?auto=format&fit=crop&w=2000&q=85"
                                alt="Open Trip TapakLokal"
                                class="w-full h-full object-cover object-center transform scale-105"
                            />
                            <div class="absolute inset-0 bg-gradient-to-b from-black/60 via-black/50 to-black/85"></div>
                            <div class="absolute inset-0 bg-radial-gradient from-transparent via-black/20 to-black/60"></div>
                        </div>

                        <!-- Hero Content -->
                        <div class="relative z-10 max-w-3xl mx-auto px-4 sm:px-6 pt-6 sm:pt-10 pb-24 sm:pb-28 md:pb-36 text-white">
                            <div class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-blue-500/25 border border-blue-400/40 text-blue-300 text-xs font-bold mb-4 backdrop-blur-xs">
                                <Compass class="w-3.5 h-3.5 text-amber-400" />
                                <span>Paket All-Inclusive Open Trip & Private Trip</span>
                            </div>
                            <h1 class="text-2xl sm:text-4xl md:text-5xl lg:text-6xl font-extrabold tracking-tight leading-[1.2] drop-shadow-md">
                                Petualangan Nusantara<br />
                                Tanpa Repot Mandiri.
                            </h1>
                            <p class="mt-3 sm:mt-4 text-xs sm:text-sm md:text-base text-slate-100/90 font-normal leading-relaxed max-w-2xl mx-auto drop-shadow">
                                Semua paket sudah termasuk <strong>penginapan transparan</strong>, <strong>kendaraan di lokasi</strong>, dan <strong>bebas pilih menu kuliner lokal</strong> sesuai seleramu tanpa biaya tambahan sepeser pun.
                            </p>
                        </div>
                    </div>

                    <!-- Floating Search & Filter Bar -->
                    <div class="max-w-5xl mx-auto -mt-16 sm:-mt-22 md:-mt-24 relative z-20 px-1 sm:px-4">
                        <div class="bg-white rounded-2xl sm:rounded-3xl shadow-2xl border border-gray-100 p-4 sm:p-5 backdrop-blur-xs">
                            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-12 gap-3 items-center">
                                <!-- Search input -->
                                <div class="sm:col-span-1 lg:col-span-5 bg-gray-50/90 border border-gray-200/80 rounded-xl px-3.5 py-2 flex items-center gap-2 focus-within:ring-2 focus-within:ring-[#0052cc]">
                                    <Search class="w-4 h-4 text-slate-400 shrink-0" />
                                    <input
                                        v-model="searchQuery"
                                        type="text"
                                        placeholder="Cari destinasi: Bromo, Bajo, Toraja..."
                                        class="w-full bg-transparent text-xs sm:text-sm text-slate-800 placeholder-slate-400 border-none p-0 focus:outline-none focus:ring-0 font-medium"
                                    />
                                </div>

                                <!-- Region Dropdown -->
                                <div class="sm:col-span-1 lg:col-span-3 bg-gray-50/90 border border-gray-200/80 rounded-xl px-3 py-2">
                                    <label class="block text-[10px] font-bold text-slate-600 uppercase">Wilayah</label>
                                    <select
                                        v-model="selectedRegion"
                                        class="w-full bg-transparent text-xs font-semibold text-slate-800 border-none p-0 focus:outline-none focus:ring-0 cursor-pointer"
                                    >
                                        <option v-for="r in regions" :key="r" :value="r">{{ r }}</option>
                                    </select>
                                </div>

                                <!-- Difficulty Dropdown -->
                                <div class="sm:col-span-1 lg:col-span-2 bg-gray-50/90 border border-gray-200/80 rounded-xl px-3 py-2">
                                    <label class="block text-[10px] font-bold text-slate-600 uppercase">Tingkat</label>
                                    <select
                                        v-model="selectedDifficulty"
                                        class="w-full bg-transparent text-xs font-semibold text-slate-800 border-none p-0 focus:outline-none focus:ring-0 cursor-pointer"
                                    >
                                        <option value="Semua">Semua Tingkat</option>
                                        <option value="Mudah">Santai (Mudah)</option>
                                        <option value="Sedang">Sedang</option>
                                        <option value="Menantang">Menantang</option>
                                    </select>
                                </div>

                                <!-- Sort Dropdown -->
                                <div class="sm:col-span-1 lg:col-span-2 bg-gray-50/90 border border-gray-200/80 rounded-xl px-3 py-2">
                                    <label class="block text-[10px] font-bold text-slate-600 uppercase">Urutkan</label>
                                    <select
                                        v-model="sortBy"
                                        class="w-full bg-transparent text-xs font-semibold text-slate-800 border-none p-0 focus:outline-none focus:ring-0 cursor-pointer"
                                    >
                                        <option value="popular">Terpopuler</option>
                                        <option value="rating">Rating Tertinggi</option>
                                        <option value="price-low">Harga Termurah</option>
                                        <option value="price-high">Harga Tertinggi</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Trip Grid Section -->
            <section class="py-8 sm:py-12 bg-white">
                <div class="max-w-7xl mx-auto px-3 sm:px-6 lg:px-8">
                    <div class="flex items-center justify-between mb-6">
                        <div>
                            <h2 class="text-xl sm:text-2xl font-extrabold text-slate-900 tracking-tight">
                                Eksplorasi Paket Open Trip
                            </h2>
                            <p class="text-xs sm:text-sm text-slate-500 mt-0.5">
                                Menampilkan {{ filteredTrips.length }} trip aktif terverifikasi
                            </p>
                        </div>
                    </div>

                    <!-- Cards Grid -->
                    <div v-if="filteredTrips.length > 0" class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-3 gap-6">
                        <div
                            v-for="trip in filteredTrips"
                            :key="trip.id"
                            class="group bg-white rounded-2xl sm:rounded-3xl border border-gray-200/80 shadow-sm hover:shadow-xl transition-all duration-300 overflow-hidden flex flex-col justify-between"
                        >
                            <!-- Thumbnail -->
                            <div>
                                <div class="relative h-52 w-full overflow-hidden bg-slate-100">
                                    <img
                                        :src="trip.image"
                                        :alt="trip.title"
                                        class="w-full h-full object-cover group-hover:scale-105 transition duration-500"
                                    />
                                    <div class="absolute top-3 left-3 bg-black/65 backdrop-blur-xs text-white text-[11px] font-semibold px-2.5 py-1 rounded-full flex items-center gap-1">
                                        <Clock class="w-3 h-3 text-amber-400" />
                                        <span>{{ trip.duration }}</span>
                                    </div>
                                    <button
                                        type="button"
                                        @click="toggleWishlist(trip.id)"
                                        class="absolute top-3 right-3 w-8 h-8 rounded-full bg-white/90 backdrop-blur-xs flex items-center justify-center text-slate-600 hover:text-red-500 transition shadow-xs cursor-pointer"
                                    >
                                        <Heart :class="['w-4 h-4 transition', bookmarkedIds.has(trip.id) ? 'fill-red-500 text-red-500' : '']" />
                                    </button>
                                    <div class="absolute bottom-3 left-3 bg-amber-500/90 backdrop-blur-xs text-white text-[10px] font-bold px-2 py-0.5 rounded-md">
                                        Sisa {{ trip.slots_left }} Slot Kuota
                                    </div>
                                </div>

                                <!-- Card Details -->
                                <div class="p-4 sm:p-5">
                                    <div class="flex items-center justify-between text-xs text-slate-500 mb-2">
                                        <div class="flex items-center gap-1 truncate font-medium">
                                            <MapPin class="w-3.5 h-3.5 text-[#0052cc] shrink-0" />
                                            <span class="truncate">{{ trip.location }}</span>
                                        </div>
                                        <div class="flex items-center gap-1 font-bold text-slate-800 shrink-0">
                                            <Star class="w-3.5 h-3.5 fill-amber-400 text-amber-400" />
                                            <span>{{ trip.rating }}</span>
                                        </div>
                                    </div>

                                    <h3 class="font-bold text-slate-900 text-base leading-snug line-clamp-2 group-hover:text-[#0052cc] transition">
                                        {{ trip.title }}
                                    </h3>

                                    <!-- Highlights -->
                                    <div class="mt-3 flex flex-wrap gap-1.5">
                                        <span
                                            v-for="(h, idx) in trip.highlights.slice(0, 3)"
                                            :key="idx"
                                            class="text-[10px] px-2 py-0.5 bg-slate-100 text-slate-600 rounded-md font-medium"
                                        >
                                            {{ h }}
                                        </span>
                                    </div>

                                    <!-- Akamsi Local Badge -->
                                    <div class="mt-3 py-1.5 px-2.5 bg-slate-50 rounded-xl flex items-center gap-2 border border-slate-100">
                                        <ShieldCheck class="w-4 h-4 text-emerald-600 shrink-0" />
                                        <div class="truncate text-[11px]">
                                            <span class="font-bold text-slate-900">{{ trip.akamsi.name }}</span>
                                            <span class="text-slate-500 ml-1">({{ trip.akamsi.experience }})</span>
                                        </div>
                                    </div>

                                    <!-- All-Inclusive Feature Inclusions -->
                                    <div class="mt-2.5 space-y-1 text-[11px] bg-blue-50/50 p-2.5 rounded-xl border border-blue-100/60">
                                        <div class="flex items-center gap-1.5 text-slate-700 truncate" :title="trip.accommodation ? trip.accommodation.name : 'Penginapan Termasuk'">
                                            <span class="text-blue-600 font-bold shrink-0">🏨</span>
                                            <span class="truncate font-medium">{{ trip.accommodation ? trip.accommodation.name : 'Penginapan + Vendor Termasuk' }}</span>
                                        </div>
                                        <div class="flex items-center gap-1.5 text-slate-700 truncate" :title="trip.vehicle ? trip.vehicle.name : 'Kendaraan Lokal Termasuk'">
                                            <span class="text-emerald-600 font-bold shrink-0">🚙</span>
                                            <span class="truncate font-medium">{{ trip.vehicle ? trip.vehicle.name : 'Kendaraan di Lokasi Termasuk' }}</span>
                                        </div>
                                        <div class="flex items-center gap-1.5 text-amber-800 truncate">
                                            <span class="text-amber-600 font-bold shrink-0">🍽️</span>
                                            <span class="truncate font-semibold">Bebas Pilih Menu (Rp 0 Tambahan)</span>
                                        </div>
                                        <div class="flex items-center gap-1.5 text-emerald-800 truncate">
                                            <span class="text-emerald-600 font-bold shrink-0">🎁</span>
                                            <span class="truncate font-bold">Gratis Oleh-Oleh Khas (Tanpa Add-on)</span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Bottom Price & CTA -->
                            <div class="p-4 sm:p-5 pt-0">
                                <div class="pt-3 border-t border-gray-100 flex items-center justify-between">
                                    <div>
                                        <span class="text-[10px] text-slate-400 font-medium block">All-Inclusive Mulai</span>
                                        <div class="font-black text-slate-900 text-base sm:text-lg">
                                            {{ formatRupiah(trip.price) }}
                                            <span class="text-[10px] text-slate-400 font-normal">/pax</span>
                                        </div>
                                    </div>
                                    <Link
                                        :href="route('trip.show', trip.id)"
                                        class="bg-[#0052cc] hover:bg-[#003ea8] text-white px-4 py-2 rounded-xl text-xs font-bold transition flex items-center gap-1.5 shadow-xs cursor-pointer"
                                    >
                                        <span>Rincian & Foto</span>
                                        <ArrowRight class="w-3.5 h-3.5" />
                                    </Link>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Empty state -->
                    <div v-else class="text-center py-16 bg-slate-50 rounded-2xl border border-slate-200">
                        <p class="text-slate-500 text-sm">Tidak ada trip yang sesuai dengan filter pencarian Anda.</p>
                        <button
                            type="button"
                            @click="selectedRegion = 'Semua Wilayah'; selectedDifficulty = 'Semua'; searchQuery = ''"
                            class="mt-3 text-xs font-bold text-[#0052cc] hover:underline"
                        >
                            Reset Filter Pencarian
                        </button>
                    </div>
                </div>
            </section>
        </main>

        <Footer />
        <BottomNav active-menu="Open Trip" :user="user" />

        <!-- Notification Toast -->
        <div
            v-if="toastMessage"
            class="fixed bottom-20 sm:bottom-6 left-4 right-4 sm:left-auto sm:right-6 z-50 bg-slate-900/95 text-white px-4 py-3 rounded-2xl shadow-2xl border border-slate-700 flex items-center justify-between sm:justify-start gap-3 backdrop-blur-md"
        >
            <div class="flex items-center gap-2.5">
                <CheckCircle2 class="w-5 h-5 text-emerald-400 shrink-0" />
                <span class="text-xs sm:text-sm font-medium">{{ toastMessage }}</span>
            </div>
            <button type="button" @click="toastMessage = null" class="text-slate-400 hover:text-white p-1">
                <X class="w-4 h-4" />
            </button>
        </div>
    </div>
</template>
