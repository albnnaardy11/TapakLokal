<script setup>
import { ref, computed } from 'vue';
import { Head, Link } from '@inertiajs/vue3';
import Navbar from '@/Components/Navbar.vue';
import BottomNav from '@/Components/BottomNav.vue';
import Footer from '@/Components/Footer.vue';
import {
    MapPin,
    Calendar,
    Users,
    Star,
    Clock,
    ShieldCheck,
    ArrowRight,
    Heart,
    CheckCircle2,
    Sparkles,
    ChevronRight,
    Home,
    Car,
    Utensils,
    Gift,
    Building2,
    Compass,
    Eye,
    X,
    ChevronLeft,
    Phone,
    Share2,
    Check
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
    trip: {
        type: Object,
        required: true,
    },
    relatedTrips: {
        type: Array,
        default: () => [],
    },
});

// Gallery state
const activeGalleryCategory = ref('Semua'); // 'Semua' | 'Wisata' | 'Penginapan' | 'Kendaraan' | 'Makanan'
const lightboxImage = ref(null);
const lightboxIndex = ref(0);

// Booking state
const bookingType = ref('open'); // 'open' | 'private'
const paxCount = ref(1);
const customerName = ref('');
const customerPhone = ref('');
const specialNotes = ref('');
const selectedMeals = ref({});
const bookingSuccess = ref(false);
const isWishlisted = ref(false);
const toastMessage = ref(null);

function showToast(msg) {
    toastMessage.value = msg;
    setTimeout(() => {
        toastMessage.value = null;
    }, 3500);
}

// Flat list of all gallery items
const allGalleryItems = computed(() => {
    if (!props.trip.gallery) {
        return [{ url: props.trip.image, caption: props.trip.title, category: 'Spot Wisata' }];
    }
    const items = [];
    if (props.trip.gallery.destinations) {
        props.trip.gallery.destinations.forEach((d) => items.push({ ...d, category: 'Wisata' }));
    }
    if (props.trip.gallery.accommodation) {
        props.trip.gallery.accommodation.forEach((a) => items.push({ ...a, category: 'Penginapan' }));
    }
    if (props.trip.gallery.vehicle) {
        props.trip.gallery.vehicle.forEach((v) => items.push({ ...v, category: 'Kendaraan' }));
    }
    if (props.trip.gallery.food) {
        props.trip.gallery.food.forEach((f) => items.push({ ...f, category: 'Makanan' }));
    }
    return items;
});

const filteredGallery = computed(() => {
    if (activeGalleryCategory.value === 'Semua') {
        return allGalleryItems.value;
    }
    return allGalleryItems.value.filter(
        (img) => img.category === activeGalleryCategory.value
    );
});

// Initialize default meals selection
if (props.trip.meals && props.trip.meals.options) {
    props.trip.meals.options.forEach((session, sIdx) => {
        selectedMeals.value[sIdx] = session.menu_choices[0];
    });
}

function formatRupiah(num) {
    return new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: 'IDR',
        maximumFractionDigits: 0,
    }).format(num || 0);
}

const pricePerPax = computed(() => {
    return bookingType.value === 'private'
        ? (props.trip.private_price || props.trip.price * 1.35)
        : props.trip.price;
});

const totalPrice = computed(() => {
    return pricePerPax.value * paxCount.value;
});

function handlePaxChange(delta) {
    const minPax = bookingType.value === 'private' ? 2 : 1;
    const maxPax = bookingType.value === 'private' ? 20 : (props.trip.slots_left || 12);
    const newVal = paxCount.value + delta;
    if (newVal >= minPax && newVal <= maxPax) {
        paxCount.value = newVal;
    }
}

function openLightbox(index) {
    lightboxIndex.value = index;
    lightboxImage.value = filteredGallery.value[index];
}

function nextLightboxImage() {
    if (lightboxIndex.value < filteredGallery.value.length - 1) {
        lightboxIndex.value++;
    } else {
        lightboxIndex.value = 0;
    }
    lightboxImage.value = filteredGallery.value[lightboxIndex.value];
}

function prevLightboxImage() {
    if (lightboxIndex.value > 0) {
        lightboxIndex.value--;
    } else {
        lightboxIndex.value = filteredGallery.value.length - 1;
    }
    lightboxImage.value = filteredGallery.value[lightboxIndex.value];
}

function handleConfirmBooking() {
    if (!customerName.value.trim() || !customerPhone.value.trim()) {
        showToast('Mohon isi nama lengkap dan nomor WhatsApp pemesan.');
        return;
    }
    bookingSuccess.value = true;
    showToast(`Pemesanan paket "${props.trip.title}" berhasil dicatat!`);
}

function handleShare() {
    if (navigator.clipboard) {
        navigator.clipboard.writeText(window.location.href);
        showToast('Tautan trip berhasil disalin ke clipboard!');
    }
}
</script>

<template>
    <Head>
        <title>{{ trip.title }} (All-Inclusive) - TapakLokal</title>
        <meta
            name="description"
            :content="`Paket ${trip.title} all-inclusive: sudah termasuk penginapan vendor ${trip.accommodation?.name}, kendaraan lokal, bebas pilih makanan, & gratis oleh-oleh.`"
        />
    </Head>

    <div class="min-h-screen flex flex-col bg-slate-50 font-sans text-slate-800 antialiased selection:bg-[#0052cc] selection:text-white">
        <Navbar :user="user" active-menu="Open Trip" />

        <main class="flex-1 pb-16">
            <!-- Breadcrumbs Bar -->
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-4 pb-2">
                <nav class="flex items-center gap-2 text-xs text-slate-500 font-medium">
                    <Link :href="route('home')" class="hover:text-[#0052cc] transition">Beranda</Link>
                    <ChevronRight class="w-3.5 h-3.5 text-slate-400" />
                    <Link :href="route('trip.index')" class="hover:text-[#0052cc] transition">Open Trip</Link>
                    <ChevronRight class="w-3.5 h-3.5 text-slate-400" />
                    <span class="text-slate-900 font-semibold truncate max-w-[200px] sm:max-w-none">{{ trip.title }}</span>
                </nav>
            </div>

            <!-- Trip Header Section -->
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-3">
                <div class="flex flex-col md:flex-row md:items-start justify-between gap-4">
                    <div>
                        <!-- All-Inclusive Guarantee Pill -->
                        <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs font-bold mb-2">
                            <CheckCircle2 class="w-3.5 h-3.5 text-emerald-600" />
                            <span>Paket All-Inclusive: Penginapan + Kendaraan + Bebas Pilih Makanan + Gratis Oleh-Oleh</span>
                        </div>

                        <h1 class="text-2xl sm:text-3xl md:text-4xl font-black text-slate-900 tracking-tight leading-tight">
                            {{ trip.title }}
                        </h1>

                        <div class="flex flex-wrap items-center gap-3 sm:gap-5 mt-2.5 text-xs text-slate-600">
                            <div class="flex items-center gap-1.5 font-semibold text-slate-800">
                                <Star class="w-4 h-4 fill-amber-400 text-amber-400" />
                                <span>{{ trip.rating }} ({{ trip.review_count }} ulasan)</span>
                            </div>
                            <div class="flex items-center gap-1">
                                <MapPin class="w-3.5 h-3.5 text-[#0052cc]" />
                                <span>{{ trip.location }}</span>
                            </div>
                            <div class="flex items-center gap-1">
                                <Clock class="w-3.5 h-3.5 text-slate-500" />
                                <span>Durasi: {{ trip.duration }}</span>
                            </div>
                            <div class="flex items-center gap-1 text-emerald-700 font-medium">
                                <ShieldCheck class="w-3.5 h-3.5" />
                                <span>Pemandu: {{ trip.akamsi.name }} ({{ trip.akamsi.experience }})</span>
                            </div>
                        </div>
                    </div>

                    <!-- Action Buttons (Share & Wishlist) -->
                    <div class="flex items-center gap-2 shrink-0">
                        <button
                            type="button"
                            @click="handleShare"
                            class="px-3 py-2 bg-white hover:bg-slate-100 border border-gray-200 text-slate-700 rounded-xl text-xs font-bold flex items-center gap-1.5 transition cursor-pointer shadow-2xs"
                        >
                            <Share2 class="w-3.5 h-3.5" />
                            <span>Bagikan</span>
                        </button>
                        <button
                            type="button"
                            @click="isWishlisted = !isWishlisted; showToast(isWishlisted ? 'Trip berhasil disimpan ke wishlist!' : 'Dihapus dari wishlist')"
                            :class="[
                                'px-3 py-2 rounded-xl text-xs font-bold flex items-center gap-1.5 transition cursor-pointer border shadow-2xs',
                                isWishlisted
                                    ? 'bg-red-50 text-red-600 border-red-200'
                                    : 'bg-white hover:bg-slate-100 border-gray-200 text-slate-700'
                            ]"
                        >
                            <Heart :class="['w-3.5 h-3.5', isWishlisted ? 'fill-red-500' : '']" />
                            <span>{{ isWishlisted ? 'Tersimpan' : 'Simpan' }}</span>
                        </button>
                    </div>
                </div>
            </div>

            <!-- High-End Interactive Photo Gallery System (Airbnb-Style Mosaic Grid) -->
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-3">
                <div class="relative rounded-3xl overflow-hidden shadow-xl border border-gray-100 bg-slate-900">
                    <!-- Mosaic Grid (Desktop 5 images, mobile 1 big image) -->
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-2 h-[320px] sm:h-[420px] md:h-[480px]">
                        <!-- Main Hero Image (Spans 2 cols & 2 rows) -->
                        <div
                            @click="openLightbox(0)"
                            class="md:col-span-2 relative group overflow-hidden cursor-pointer bg-slate-800"
                        >
                            <img
                                :src="allGalleryItems[0]?.url || trip.image"
                                :alt="allGalleryItems[0]?.caption || trip.title"
                                class="w-full h-full object-cover group-hover:scale-105 transition duration-500"
                            />
                            <div class="absolute inset-0 bg-gradient-to-t from-black/60 via-transparent to-transparent opacity-80 group-hover:opacity-60 transition"></div>
                            <div class="absolute bottom-4 left-4 text-white">
                                <span class="bg-blue-600/90 text-white text-[11px] font-bold px-2.5 py-1 rounded-md backdrop-blur-xs">
                                    {{ allGalleryItems[0]?.category || 'Spot Utama' }}
                                </span>
                                <p class="font-bold text-sm sm:text-base mt-1 drop-shadow">{{ allGalleryItems[0]?.caption || trip.title }}</p>
                            </div>
                        </div>

                        <!-- 4 Supporting Images in 2x2 Grid (Visible on tablet & desktop) -->
                        <div class="hidden md:grid md:col-span-2 grid-cols-2 gap-2">
                            <div
                                v-for="(img, idx) in allGalleryItems.slice(1, 5)"
                                :key="idx"
                                @click="openLightbox(idx + 1)"
                                class="relative group overflow-hidden cursor-pointer bg-slate-800 h-full"
                            >
                                <img
                                    :src="img.url"
                                    :alt="img.caption"
                                    class="w-full h-full object-cover group-hover:scale-105 transition duration-500"
                                />
                                <div class="absolute inset-0 bg-black/25 group-hover:bg-transparent transition"></div>
                                <div class="absolute bottom-2 left-2 text-white">
                                    <span class="bg-black/60 text-[10px] font-bold px-2 py-0.5 rounded backdrop-blur-xs">
                                        {{ img.category }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Floating View All Photos Button -->
                    <button
                        type="button"
                        @click="openLightbox(0)"
                        class="absolute bottom-4 right-4 bg-white/95 hover:bg-white text-slate-900 px-4 py-2 rounded-xl text-xs font-extrabold shadow-lg backdrop-blur-md flex items-center gap-2 transition cursor-pointer border border-gray-200"
                    >
                        <Eye class="w-4 h-4 text-[#0052cc]" />
                        <span>Lihat Semua Foto ({{ allGalleryItems.length }} Foto)</span>
                    </button>
                </div>

                <!-- Gallery Category Filter Tabs -->
                <div class="flex items-center gap-2 mt-4 overflow-x-auto pb-1 scrollbar-none">
                    <span class="text-xs font-bold text-slate-500 shrink-0 mr-1">Filter Galeri:</span>
                    <button
                        v-for="cat in ['Semua', 'Wisata', 'Penginapan', 'Kendaraan', 'Makanan']"
                        :key="cat"
                        type="button"
                        @click="activeGalleryCategory = cat"
                        :class="[
                            'px-3.5 py-1.5 rounded-full text-xs font-bold transition whitespace-nowrap cursor-pointer shrink-0',
                            activeGalleryCategory === cat
                                ? 'bg-[#0052cc] text-white shadow-2xs'
                                : 'bg-white text-slate-600 hover:bg-slate-100 border border-slate-200'
                        ]"
                    >
                        {{ cat === 'Semua' ? `Semua Foto (${allGalleryItems.length})` : cat }}
                    </button>
                </div>
            </div>

            <!-- Main Detail & Booking Layout (2 Columns: Left Details, Right Sticky Booking Card) -->
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-6">
                <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
                    <!-- LEFT COLUMN: All-Inclusive Details, Vendors, Meals, Itinerary (8 cols) -->
                    <div class="lg:col-span-8 space-y-8">
                        <!-- 4 VENDOR & IDENTITY SHOWCASE CARDS -->
                        <div class="space-y-4">
                            <h2 class="text-lg sm:text-xl font-black text-slate-900 flex items-center gap-2">
                                <Sparkles class="w-5 h-5 text-[#0052cc]" />
                                <span>Identitas & Mitra Penyedia Resmi (Transparan)</span>
                            </h2>

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <!-- 1. Biro Travel Penyelenggara -->
                                <div class="bg-white rounded-2xl p-4.5 border border-gray-200/80 shadow-xs flex flex-col justify-between space-y-3">
                                    <div class="flex items-start gap-3">
                                        <div class="w-12 h-12 rounded-2xl overflow-hidden bg-blue-50 border border-blue-100 shrink-0">
                                            <img
                                                :src="trip.travel_partner?.logo || 'https://images.unsplash.com/photo-1599305445671-ac291c95aaa9?auto=format&fit=crop&w=120&q=80'"
                                                :alt="trip.travel_partner?.name"
                                                class="w-full h-full object-cover"
                                            />
                                        </div>
                                        <div>
                                            <span class="text-[10px] font-extrabold uppercase text-blue-700 tracking-wide block">Biro Travel Resmi</span>
                                            <h3 class="font-extrabold text-slate-900 text-sm leading-tight">{{ trip.travel_partner?.name || 'Mitra Travel Resmi' }}</h3>
                                            <span class="text-[11px] text-slate-500 block mt-0.5">{{ trip.travel_partner?.license }}</span>
                                        </div>
                                    </div>
                                    <div class="pt-2 border-t border-slate-100 flex items-center justify-between text-[11px]">
                                        <span class="text-slate-500">{{ trip.travel_partner?.trips_completed }}</span>
                                        <span class="font-bold text-amber-600 flex items-center gap-1">
                                            <Star class="w-3.5 h-3.5 fill-amber-400" />
                                            {{ trip.travel_partner?.rating }} Rating
                                        </span>
                                    </div>
                                </div>

                                <!-- 2. Penginapan & Vendor Homestay -->
                                <div class="bg-white rounded-2xl p-4.5 border border-gray-200/80 shadow-xs flex flex-col justify-between space-y-3">
                                    <div class="flex items-start gap-3">
                                        <div class="w-12 h-12 rounded-2xl overflow-hidden bg-emerald-50 border border-emerald-100 shrink-0">
                                            <img
                                                :src="trip.accommodation?.logo || 'https://images.unsplash.com/photo-1566073771259-6a8506099945?auto=format&fit=crop&w=120&q=80'"
                                                :alt="trip.accommodation?.name"
                                                class="w-full h-full object-cover"
                                            />
                                        </div>
                                        <div>
                                            <span class="text-[10px] font-extrabold uppercase text-emerald-700 tracking-wide block">Penginapan Termasuk</span>
                                            <h3 class="font-extrabold text-slate-900 text-sm leading-tight">{{ trip.accommodation?.name }}</h3>
                                            <span class="text-[11px] text-emerald-700 font-semibold block mt-0.5">Mitra: {{ trip.accommodation?.vendor }}</span>
                                        </div>
                                    </div>
                                    <div class="pt-2 border-t border-slate-100 text-[11px] text-slate-600">
                                        <span class="font-medium">Tipe:</span> {{ trip.accommodation?.room_type }}
                                    </div>
                                </div>

                                <!-- 3. Restoran UMKM Lokal Mitra -->
                                <div class="bg-white rounded-2xl p-4.5 border border-gray-200/80 shadow-xs flex flex-col justify-between space-y-3">
                                    <div class="flex items-start gap-3">
                                        <div class="w-12 h-12 rounded-2xl overflow-hidden bg-amber-50 border border-amber-100 shrink-0">
                                            <img
                                                :src="trip.restaurant_umkm?.logo || 'https://images.unsplash.com/photo-1555396273-367ea4eb4db5?auto=format&fit=crop&w=120&q=80'"
                                                :alt="trip.restaurant_umkm?.name"
                                                class="w-full h-full object-cover"
                                            />
                                        </div>
                                        <div>
                                            <span class="text-[10px] font-extrabold uppercase text-amber-800 tracking-wide block">Dapur & Restoran UMKM</span>
                                            <h3 class="font-extrabold text-slate-900 text-sm leading-tight">{{ trip.restaurant_umkm?.name }}</h3>
                                            <span class="text-[11px] text-slate-500 block mt-0.5">{{ trip.restaurant_umkm?.vendor }}</span>
                                        </div>
                                    </div>
                                    <div class="pt-2 border-t border-slate-100 text-[11px] text-slate-600">
                                        <span class="font-medium text-amber-900">Spesialisasi:</span> {{ trip.restaurant_umkm?.specialty }}
                                    </div>
                                </div>

                                <!-- 4. GRATIS OLEH-OLEH KHAS DAERAH (NO ADD-ON) -->
                                <div class="bg-gradient-to-br from-amber-50 to-orange-50/60 rounded-2xl p-4.5 border border-amber-200 shadow-xs flex flex-col justify-between space-y-3">
                                    <div class="flex items-start gap-3">
                                        <div class="w-12 h-12 rounded-2xl bg-amber-500 text-white flex items-center justify-center font-bold shrink-0 shadow-xs">
                                            <Gift class="w-6 h-6" />
                                        </div>
                                        <div>
                                            <span class="text-[10px] font-extrabold uppercase text-emerald-800 bg-emerald-100 px-2 py-0.5 rounded-full inline-block mb-1">
                                                Include Gratis (Rp 0 / Tanpa Add-On)
                                            </span>
                                            <h3 class="font-extrabold text-slate-900 text-sm leading-tight">{{ trip.free_souvenir?.name }}</h3>
                                            <span class="text-[11px] text-slate-600 block mt-0.5">{{ trip.free_souvenir?.vendor }}</span>
                                        </div>
                                    </div>
                                    <div class="pt-2 border-t border-amber-200/60 text-[11px] text-amber-900 font-medium">
                                        {{ trip.free_souvenir?.description }}
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- KENDARAAN DI TEMPAT WISATA & DRIVER VENDOR -->
                        <div class="bg-white rounded-3xl p-6 border border-gray-200/80 shadow-xs space-y-4">
                            <div class="flex items-center justify-between">
                                <h3 class="text-base sm:text-lg font-extrabold text-slate-900 flex items-center gap-2">
                                    <Car class="w-5 h-5 text-emerald-600" />
                                    <span>Armada Kendaraan di Lokasi (Termasuk di Paket)</span>
                                </h3>
                                <span class="text-[11px] font-bold text-emerald-700 bg-emerald-50 px-2.5 py-1 rounded-full border border-emerald-200">
                                    Sudah Include
                                </span>
                            </div>

                            <div class="p-4 bg-slate-50 rounded-2xl border border-slate-100 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                                <div class="space-y-1">
                                    <span class="font-bold text-slate-900 text-sm sm:text-base block">{{ trip.vehicle.name }}</span>
                                    <span class="text-xs text-slate-600 block">Disediakan oleh: <strong>{{ trip.vehicle.vendor }}</strong></span>
                                    <span class="text-xs text-slate-500 block">{{ trip.vehicle.type }} • {{ trip.vehicle.capacity }}</span>
                                </div>
                                <div class="text-xs text-emerald-700 bg-white px-3 py-1.5 rounded-xl border border-emerald-200 font-bold self-start sm:self-center">
                                    Driver Akamsi Berlisensi
                                </div>
                            </div>
                        </div>

                        <!-- BEBAS PILIH MENU MAKANAN LOKAL SESUAI SELERA (NO EXTRA FEE) -->
                        <div class="bg-white rounded-3xl p-6 border border-gray-200/80 shadow-xs space-y-4">
                            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2">
                                <div>
                                    <h3 class="text-base sm:text-lg font-extrabold text-slate-900 flex items-center gap-2">
                                        <Utensils class="w-5 h-5 text-amber-600" />
                                        <span>Bebas Pilih Menu Makanan Lokal Sesuai Selera</span>
                                    </h3>
                                    <p class="text-xs text-slate-500 mt-0.5">
                                        Di TapakLokal, Anda bebas menentukan hidangan otentik khas daerah untuk tiap sesi makan tanpa biaya tambahan sepeser pun!
                                    </p>
                                </div>
                                <span class="inline-flex items-center gap-1 text-xs font-bold text-emerald-800 bg-emerald-100 px-3 py-1 rounded-full shrink-0">
                                    <CheckCircle2 class="w-3.5 h-3.5" />
                                    Harga Flat (+Rp 0)
                                </span>
                            </div>

                            <!-- Interactive Meal Choices per Session -->
                            <div class="space-y-4 pt-2">
                                <div
                                    v-for="(session, sIdx) in trip.meals?.options"
                                    :key="sIdx"
                                    class="bg-amber-50/30 rounded-2xl p-4 border border-amber-200/80 space-y-2.5"
                                >
                                    <span class="font-bold text-slate-900 text-xs sm:text-sm text-amber-950 block">
                                        🥣 {{ session.session }}
                                    </span>

                                    <div class="space-y-2">
                                        <label
                                            v-for="(menu, mIdx) in session.menu_choices"
                                            :key="mIdx"
                                            :class="[
                                                'flex items-center justify-between p-3 rounded-xl border transition cursor-pointer text-xs sm:text-sm',
                                                selectedMeals[sIdx] === menu
                                                    ? 'border-amber-400 bg-white font-bold text-slate-900 shadow-2xs'
                                                    : 'border-slate-200/80 bg-white/70 hover:bg-white text-slate-700'
                                            ]"
                                        >
                                            <div class="flex items-center gap-2.5 truncate">
                                                <input
                                                    type="radio"
                                                    :name="'page_meal_' + sIdx"
                                                    :value="menu"
                                                    v-model="selectedMeals[sIdx]"
                                                    class="text-amber-600 focus:ring-amber-500 w-4 h-4"
                                                />
                                                <span class="truncate">{{ menu }}</span>
                                            </div>
                                            <span class="text-[11px] font-bold text-emerald-700 bg-emerald-50 border border-emerald-100 px-2 py-0.5 rounded shrink-0 ml-2">
                                                Include (+Rp 0)
                                            </span>
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- SPOTS & RUTE UNGGULAN ITINERARY -->
                        <div class="bg-white rounded-3xl p-6 border border-gray-200/80 shadow-xs space-y-4">
                            <h3 class="text-base sm:text-lg font-extrabold text-slate-900 flex items-center gap-2">
                                <Compass class="w-5 h-5 text-[#0052cc]" />
                                <span>Spot Wisata & Rute Petualangan</span>
                            </h3>

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                <div
                                    v-for="(h, idx) in trip.highlights"
                                    :key="idx"
                                    class="p-3 bg-slate-50 rounded-xl border border-slate-100 flex items-center gap-2 text-xs font-semibold text-slate-800"
                                >
                                    <div class="w-6 h-6 rounded-lg bg-blue-100 text-[#0052cc] flex items-center justify-center font-bold text-[11px] shrink-0">
                                        {{ idx + 1 }}
                                    </div>
                                    <span>{{ h }}</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- RIGHT COLUMN: Sticky / Floating Booking Card (4 cols) -->
                    <div class="lg:col-span-4">
                        <div class="sticky top-6 bg-white rounded-3xl p-5 sm:p-6 border border-gray-200 shadow-xl space-y-5">
                            <!-- Switcher: Open Trip vs Private Trip -->
                            <div>
                                <label class="block text-xs font-bold text-slate-700 mb-2">Pilih Tipe Perjalanan:</label>
                                <div class="grid grid-cols-2 gap-2 bg-slate-100 p-1.5 rounded-2xl">
                                    <button
                                        type="button"
                                        @click="bookingType = 'open'"
                                        :class="[
                                            'py-2 px-3 rounded-xl text-xs font-bold transition cursor-pointer text-center',
                                            bookingType === 'open'
                                                ? 'bg-white text-[#0052cc] shadow-xs'
                                                : 'text-slate-600 hover:text-slate-900'
                                        ]"
                                    >
                                        Open Trip
                                    </button>
                                    <button
                                        type="button"
                                        @click="bookingType = 'private'"
                                        :class="[
                                            'py-2 px-3 rounded-xl text-xs font-bold transition cursor-pointer text-center',
                                            bookingType === 'private'
                                                ? 'bg-emerald-600 text-white shadow-xs'
                                                : 'text-slate-600 hover:text-slate-900'
                                        ]"
                                    >
                                        Private Trip
                                    </button>
                                </div>
                            </div>

                            <!-- Price Display -->
                            <div class="p-3.5 bg-slate-50 rounded-2xl border border-slate-100">
                                <span class="text-[10px] text-slate-400 font-bold uppercase tracking-wider block">Harga All-Inclusive</span>
                                <div class="font-black text-2xl text-slate-900 mt-0.5">
                                    {{ formatRupiah(pricePerPax) }}
                                    <span class="text-xs text-slate-400 font-normal">/pax</span>
                                </div>
                                <span class="text-[11px] text-slate-500 mt-1 block">
                                    {{ bookingType === 'open' ? 'Gabung rombongan, jadwal keberangkatan fix' : 'Khusus rombonganmu, tanggal bebas tentukan' }}
                                </span>
                            </div>

                            <!-- Pax Counter -->
                            <div class="flex items-center justify-between p-3 bg-slate-50 rounded-xl border border-slate-100">
                                <div>
                                    <span class="text-xs font-bold text-slate-800 block">Jumlah Wisatawan</span>
                                    <span class="text-[10px] text-slate-500">Sisa kuota: {{ trip.slots_left }} orang</span>
                                </div>
                                <div class="flex items-center gap-2.5">
                                    <button
                                        type="button"
                                        @click="handlePaxChange(-1)"
                                        class="w-7 h-7 rounded-lg bg-white border border-slate-300 text-slate-700 font-bold hover:bg-slate-100 flex items-center justify-center transition cursor-pointer"
                                    >
                                        -
                                    </button>
                                    <span class="font-black text-slate-900 text-sm min-w-5 text-center">{{ paxCount }}</span>
                                    <button
                                        type="button"
                                        @click="handlePaxChange(1)"
                                        class="w-7 h-7 rounded-lg bg-white border border-slate-300 text-slate-700 font-bold hover:bg-slate-100 flex items-center justify-center transition cursor-pointer"
                                    >
                                        +
                                    </button>
                                </div>
                            </div>

                            <!-- All Inclusive Checklist in Sidebar -->
                            <div class="space-y-1.5 text-xs text-slate-600 bg-emerald-50/50 p-3 rounded-2xl border border-emerald-100">
                                <span class="font-bold text-slate-900 block text-[11px] mb-1">Sudah Termasuk (Rp 0 Tambahan):</span>
                                <div class="flex items-center gap-1.5 text-emerald-800">
                                    <Check class="w-3.5 h-3.5 text-emerald-600 shrink-0" />
                                    <span>Penginapan ({{ trip.accommodation?.name }})</span>
                                </div>
                                <div class="flex items-center gap-1.5 text-emerald-800">
                                    <Check class="w-3.5 h-3.5 text-emerald-600 shrink-0" />
                                    <span>Kendaraan di Lokasi ({{ trip.vehicle?.name }})</span>
                                </div>
                                <div class="flex items-center gap-1.5 text-emerald-800">
                                    <Check class="w-3.5 h-3.5 text-emerald-600 shrink-0" />
                                    <span>Bebas Pilih Menu Lokal Khas Daerah</span>
                                </div>
                                <div class="flex items-center gap-1.5 text-emerald-800 font-semibold">
                                    <Check class="w-3.5 h-3.5 text-emerald-600 shrink-0" />
                                    <span>Gratis Oleh-Oleh Khas (Tanpa Add-on)</span>
                                </div>
                            </div>

                            <!-- Booking Form Inputs (if not yet success) -->
                            <div v-if="!bookingSuccess" class="space-y-3">
                                <div>
                                    <label class="block text-[11px] font-semibold text-slate-600 mb-1">Nama Pemesan</label>
                                    <input
                                        v-model="customerName"
                                        type="text"
                                        placeholder="Contoh: Budi Santoso"
                                        class="w-full px-3 py-2 bg-slate-50 border border-gray-200 rounded-xl text-xs focus:bg-white focus:ring-2 focus:ring-[#0052cc] focus:outline-none"
                                    />
                                </div>
                                <div>
                                    <label class="block text-[11px] font-semibold text-slate-600 mb-1">Nomor WhatsApp</label>
                                    <input
                                        v-model="customerPhone"
                                        type="tel"
                                        placeholder="Contoh: 081234567890"
                                        class="w-full px-3 py-2 bg-slate-50 border border-gray-200 rounded-xl text-xs focus:bg-white focus:ring-2 focus:ring-[#0052cc] focus:outline-none"
                                    />
                                </div>
                                <div>
                                    <label class="block text-[11px] font-semibold text-slate-600 mb-1">Catatan Tambahan (Opsional)</label>
                                    <input
                                        v-model="specialNotes"
                                        type="text"
                                        placeholder="Contoh: Menu tidak pedas, alergi udang"
                                        class="w-full px-3 py-2 bg-slate-50 border border-gray-200 rounded-xl text-xs focus:bg-white focus:ring-2 focus:ring-[#0052cc] focus:outline-none"
                                    />
                                </div>

                                <!-- Total Breakdown -->
                                <div class="pt-2 border-t border-gray-100 flex items-center justify-between">
                                    <div>
                                        <span class="text-[10px] text-slate-400 font-bold block">Total Biaya ({{ paxCount }} Pax)</span>
                                        <span class="font-black text-xl text-[#0052cc]">{{ formatRupiah(totalPrice) }}</span>
                                    </div>
                                    <button
                                        type="button"
                                        @click="handleConfirmBooking"
                                        class="bg-[#0052cc] hover:bg-[#003da6] text-white px-5 py-3 rounded-xl text-xs font-bold transition flex items-center gap-1.5 shadow-md shadow-blue-500/20 cursor-pointer"
                                    >
                                        <span>Pesan Paket Sekarang</span>
                                        <ArrowRight class="w-3.5 h-3.5" />
                                    </button>
                                </div>
                            </div>

                            <!-- Success Screen in Sidebar -->
                            <div v-else class="p-4 bg-emerald-50 rounded-2xl border border-emerald-200 text-center space-y-2">
                                <div class="w-10 h-10 rounded-full bg-emerald-600 text-white flex items-center justify-center mx-auto">
                                    <CheckCircle2 class="w-6 h-6" />
                                </div>
                                <h4 class="font-extrabold text-slate-900 text-sm">Pemesanan Paket Berhasil!</h4>
                                <p class="text-xs text-slate-600 leading-relaxed">
                                    Terima kasih <strong>{{ customerName }}</strong>! Mitra Akamsi akan segera menghubungi nomor WhatsApp <strong>{{ customerPhone }}</strong> untuk konfirmasi rute dan oleh-oleh gratis.
                                </p>
                                <button
                                    type="button"
                                    @click="bookingSuccess = false"
                                    class="mt-2 text-xs font-bold text-emerald-800 hover:underline"
                                >
                                    Pesan untuk jadwal lain
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- RELATED TRIPS SECTION -->
            <div v-if="relatedTrips.length > 0" class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-16">
                <div class="flex items-center justify-between mb-6">
                    <div>
                        <h3 class="text-xl sm:text-2xl font-black text-slate-900 tracking-tight">Rekomendasi Paket Trip Lainnya</h3>
                        <p class="text-xs sm:text-sm text-slate-500">Semua paket all-inclusive sudah termasuk penginapan, kendaraan & makanan lokal.</p>
                    </div>
                    <Link :href="route('trip.index')" class="text-xs font-bold text-[#0052cc] hover:underline flex items-center gap-1">
                        <span>Lihat Semua</span>
                        <ArrowRight class="w-3.5 h-3.5" />
                    </Link>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                    <Link
                        v-for="rel in relatedTrips"
                        :key="rel.id"
                        :href="route('trip.show', rel.id)"
                        class="group bg-white rounded-2xl border border-gray-200/80 shadow-xs hover:shadow-xl transition-all duration-300 overflow-hidden flex flex-col justify-between"
                    >
                        <div class="relative h-48 overflow-hidden bg-slate-100">
                            <img
                                :src="rel.image"
                                :alt="rel.title"
                                class="w-full h-full object-cover group-hover:scale-105 transition duration-500"
                            />
                            <div class="absolute top-3 left-3 bg-black/60 text-white text-[10px] font-semibold px-2 py-0.5 rounded-full">
                                {{ rel.duration }}
                            </div>
                        </div>
                        <div class="p-4 flex-1 flex flex-col justify-between">
                            <div>
                                <span class="text-[11px] text-slate-500 font-medium block mb-1">{{ rel.location }}</span>
                                <h4 class="font-bold text-slate-900 text-sm group-hover:text-[#0052cc] transition line-clamp-2">
                                    {{ rel.title }}
                                </h4>
                            </div>
                            <div class="mt-4 pt-3 border-t border-slate-100 flex items-center justify-between">
                                <div>
                                    <span class="text-[10px] text-slate-400 block">All-Inclusive</span>
                                    <span class="font-black text-slate-900 text-sm">{{ formatRupiah(rel.price) }}/pax</span>
                                </div>
                                <span class="text-xs font-bold text-[#0052cc] flex items-center gap-1 group-hover:translate-x-1 transition">
                                    <span>Lihat Detail</span>
                                    <ArrowRight class="w-3 h-3" />
                                </span>
                            </div>
                        </div>
                    </Link>
                </div>
            </div>
        </main>

        <!-- PHOTO GALLERY LIGHTBOX MODAL -->
        <div
            v-if="lightboxImage"
            class="fixed inset-0 z-50 flex items-center justify-center p-3 sm:p-6 bg-black/90 backdrop-blur-md"
        >
            <div class="relative max-w-4xl w-full max-h-[90vh] flex flex-col items-center justify-center">
                <!-- Close Button -->
                <button
                    type="button"
                    @click="lightboxImage = null"
                    class="absolute top-2 right-2 z-10 w-9 h-9 rounded-full bg-black/60 text-white hover:bg-black/80 flex items-center justify-center transition cursor-pointer"
                >
                    <X class="w-5 h-5" />
                </button>

                <!-- Navigation Arrows -->
                <button
                    type="button"
                    @click="prevLightboxImage"
                    class="absolute left-2 sm:-left-12 top-1/2 -translate-y-1/2 w-10 h-10 rounded-full bg-white/20 hover:bg-white/40 text-white flex items-center justify-center transition cursor-pointer"
                >
                    <ChevronLeft class="w-6 h-6" />
                </button>
                <button
                    type="button"
                    @click="nextLightboxImage"
                    class="absolute right-2 sm:-right-12 top-1/2 -translate-y-1/2 w-10 h-10 rounded-full bg-white/20 hover:bg-white/40 text-white flex items-center justify-center transition cursor-pointer"
                >
                    <ChevronRight class="w-6 h-6" />
                </button>

                <!-- Lightbox Image View -->
                <div class="w-full rounded-2xl overflow-hidden bg-black/50 flex items-center justify-center">
                    <img
                        :src="lightboxImage.url"
                        :alt="lightboxImage.caption"
                        class="max-h-[75vh] w-auto max-w-full object-contain rounded-xl"
                    />
                </div>

                <!-- Lightbox Caption & Category -->
                <div class="mt-3 text-center text-white">
                    <span class="inline-block bg-blue-600 text-[10px] font-bold px-2.5 py-0.5 rounded-full mb-1">
                        {{ lightboxImage.category }}
                    </span>
                    <p class="text-xs sm:text-sm font-semibold text-slate-200">{{ lightboxImage.caption }}</p>
                    <span class="text-[11px] text-slate-400 mt-0.5 block">
                        {{ lightboxIndex + 1 }} dari {{ filteredGallery.length }} Foto
                    </span>
                </div>
            </div>
        </div>

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
