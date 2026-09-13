<script setup>
import { ref, computed } from 'vue';
import { Head, Link } from '@inertiajs/vue3';
import Navbar from '@/Components/Navbar.vue';
import BottomNav from '@/Components/BottomNav.vue';
import Footer from '@/Components/Footer.vue';
import {
    ShoppingBag,
    Star,
    Store,
    Clock,
    Truck,
    CheckCircle2,
    ArrowRight,
    Search,
    ShieldCheck,
    Plus,
    Minus,
    X,
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
        }),
    },
    categories: {
        type: Array,
        default: () => ['Semua Kategori', 'Kue & Pastry', 'Camilan Tradisional', 'Makanan Khas Siap Saji'],
    },
    items: {
        type: Array,
        default: () => [],
    },
});

const selectedCategory = ref('Semua Kategori');
const searchQuery = ref('');
const activeOrderModal = ref(null);
const orderQuantity = ref(1);
const orderAddress = ref('');
const toastMessage = ref(null);

function showToast(msg) {
    toastMessage.value = msg;
    setTimeout(() => {
        toastMessage.value = null;
    }, 3500);
}

function formatRupiah(num) {
    return new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: 'IDR',
        maximumFractionDigits: 0,
    }).format(num);
}

const filteredItems = computed(() => {
    return props.items.filter((item) => {
        const matchesCat = selectedCategory.value === 'Semua Kategori' || item.category === selectedCategory.value;
        const matchesQuery = !searchQuery.value ||
            item.title.toLowerCase().includes(searchQuery.value.toLowerCase()) ||
            item.origin.toLowerCase().includes(searchQuery.value.toLowerCase()) ||
            item.partner.toLowerCase().includes(searchQuery.value.toLowerCase());
        return matchesCat && matchesQuery;
    });
});

function openOrder(item) {
    activeOrderModal.value = item;
    orderQuantity.value = 1;
}

function submitOrder() {
    if (!orderAddress.value) {
        alert('Mohon masukkan kota & alamat pengiriman.');
        return;
    }
    const item = activeOrderModal.value;
    activeOrderModal.value = null;
    showToast(`Pesanan PO ${orderQuantity.value}x "${item.title}" berhasil dibuat! Estimasi sampai via Paxel.`);
}
</script>

<template>
    <Head>
        <title>Open PO Kuliner & Oleh-Oleh Nusantara - TapakLokal</title>
        <meta name="description" content="Titip oleh-oleh khas nusantara langsung dari produsen lokal saat tim open trip bertualang." />
    </Head>

    <div class="min-h-screen flex flex-col bg-slate-50 font-sans text-slate-800 antialiased selection:bg-[#0052cc] selection:text-white">
        <Navbar :user="user" active-menu="Open Po Kuliner" />

        <main class="flex-1">
            <!-- Hero Section Selaras -->
            <section class="relative pt-2 sm:pt-4 md:pt-6 pb-8 sm:pb-12">
                <div class="max-w-7xl mx-auto px-3 sm:px-6 lg:px-8">
                    <div class="relative rounded-2xl sm:rounded-3xl md:rounded-[36px] overflow-hidden min-h-[380px] sm:min-h-[440px] md:min-h-[480px] flex items-center justify-center text-center shadow-2xl">
                        <!-- Hero Image: Traditional Indonesian culinary market & fresh baked -->
                        <div class="absolute inset-0 z-0">
                            <img
                                src="https://images.unsplash.com/photo-1509440159596-0249088772ff?auto=format&fit=crop&w=2000&q=85"
                                alt="Open PO Kuliner TapakLokal"
                                class="w-full h-full object-cover object-center transform scale-105"
                            />
                            <div class="absolute inset-0 bg-gradient-to-b from-black/60 via-black/50 to-black/85"></div>
                            <div class="absolute inset-0 bg-radial-gradient from-transparent via-black/20 to-black/60"></div>
                        </div>

                        <!-- Hero Content -->
                        <div class="relative z-10 max-w-3xl mx-auto px-4 sm:px-6 py-10 sm:py-14 text-white">
                            <div class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-orange-500/25 border border-orange-400/40 text-orange-300 text-xs font-bold mb-3 backdrop-blur-xs">
                                <ShoppingBag class="w-3.5 h-3.5 text-orange-400" />
                                <span>Titip Langsung dari Sentra Kuliner Daerah</span>
                            </div>
                            <h1 class="text-2xl sm:text-4xl md:text-5xl font-extrabold tracking-tight leading-[1.2] drop-shadow-md">
                                Nikmati Kuliner Asli Dari Sumbernya
                            </h1>
                            <p class="mt-2.5 text-xs sm:text-sm md:text-base text-slate-100/90 font-normal leading-relaxed max-w-2xl mx-auto drop-shadow">
                                Titip beli oleh-oleh khas saat rombongan open trip berkeliling. Diproduksi fresh oleh UMKM lokal, dikirim dingin langsung ke depan pintu rumahmu.
                            </p>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Search & Filter Section -->
            <section class="max-w-7xl mx-auto px-3 sm:px-6 lg:px-8 pt-2 pb-2">
                <div class="bg-white rounded-2xl shadow-xs border border-gray-200/80 p-3 sm:p-4">
                    <div class="flex flex-col sm:flex-row items-center gap-3">
                        <div class="flex-1 w-full bg-gray-50 border border-gray-200 rounded-xl px-3.5 py-2.5 flex items-center gap-2">
                            <Search class="w-4 h-4 text-slate-400 shrink-0" />
                            <input
                                v-model="searchQuery"
                                type="text"
                                placeholder="Cari: Bakpia Kukus, Pie Susu, Pempek, Kopi Toraja..."
                                class="w-full bg-transparent text-xs sm:text-sm text-slate-800 placeholder-slate-400 border-none p-0 focus:outline-none focus:ring-0 font-medium"
                            />
                        </div>
                        <div class="flex items-center gap-2 text-xs font-semibold text-emerald-700 bg-emerald-50 px-3.5 py-2.5 rounded-xl border border-emerald-200 shrink-0">
                            <Truck class="w-4 h-4" />
                            <span>Packaging Aman & Ekspedisi Dingin</span>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Category Filter Pills -->
            <section class="max-w-7xl mx-auto px-3 sm:px-6 lg:px-8 py-4">
                <div class="flex items-center gap-2 overflow-x-auto pb-2 scrollbar-none -mx-1 px-1">
                    <button
                        v-for="cat in categories"
                        :key="cat"
                        type="button"
                        @click="selectedCategory = cat"
                        :class="[
                            'px-4 py-2 rounded-full text-xs font-bold transition whitespace-nowrap cursor-pointer shadow-2xs shrink-0',
                            selectedCategory === cat
                                ? 'bg-orange-500 text-white'
                                : 'bg-white text-slate-600 hover:bg-slate-100 border border-slate-200/80'
                        ]"
                    >
                        {{ cat }}
                    </button>
                </div>
            </section>

            <!-- Grid Items -->
            <section class="py-6 sm:py-10 bg-white">
                <div class="max-w-7xl mx-auto px-3 sm:px-6 lg:px-8">
                    <div class="flex items-center justify-between mb-6">
                        <div>
                            <h2 class="text-xl sm:text-2xl font-extrabold text-slate-900 tracking-tight">
                                Batch Open PO Aktif
                            </h2>
                            <p class="text-xs sm:text-sm text-slate-500 mt-0.5">
                                Segar langsung dari dapur produsen lokal binaan
                            </p>
                        </div>
                        <span class="text-xs font-bold text-slate-500 bg-slate-100 px-3 py-1 rounded-full">
                            {{ filteredItems.length }} Produk Siap Titip
                        </span>
                    </div>

                    <div v-if="filteredItems.length > 0" class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-3 gap-6">
                        <div
                            v-for="item in filteredItems"
                            :key="item.id"
                            class="bg-white rounded-2xl sm:rounded-3xl border border-gray-200/80 shadow-xs hover:shadow-xl transition-all duration-300 overflow-hidden flex flex-col justify-between"
                        >
                            <div>
                                <!-- Image Thumbnail -->
                                <div class="relative h-48 w-full bg-slate-100 overflow-hidden">
                                    <img
                                        :src="item.image"
                                        :alt="item.title"
                                        class="w-full h-full object-cover group-hover:scale-105 transition duration-500"
                                    />
                                    <div class="absolute top-3 left-3 bg-orange-600 text-white text-[10px] font-bold px-2.5 py-0.5 rounded-full shadow-xs">
                                        {{ item.badge }}
                                    </div>
                                    <div class="absolute bottom-3 left-3 bg-black/60 backdrop-blur-xs text-white text-[11px] font-medium px-2.5 py-0.5 rounded-md">
                                        {{ item.origin }}
                                    </div>
                                </div>

                                <!-- Body -->
                                <div class="p-4 sm:p-5">
                                    <div class="flex items-center justify-between text-xs text-slate-500 mb-1.5">
                                        <div class="flex items-center gap-1 font-semibold text-emerald-700">
                                            <Store class="w-3.5 h-3.5 text-emerald-600" />
                                            <span>{{ item.partner }}</span>
                                        </div>
                                        <div class="flex items-center gap-1 font-bold text-slate-800">
                                            <Star class="w-3.5 h-3.5 fill-amber-400 text-amber-400" />
                                            <span>{{ item.rating }}</span>
                                        </div>
                                    </div>

                                    <h3 class="font-bold text-slate-900 text-base leading-snug line-clamp-2 mt-1">
                                        {{ item.title }}
                                    </h3>

                                    <div class="mt-2.5 flex items-center justify-between text-xs text-slate-500">
                                        <span class="text-amber-600 font-medium flex items-center gap-1">
                                            <Clock class="w-3.5 h-3.5" />
                                            {{ item.close_date }}
                                        </span>
                                        <span class="text-[11px] text-slate-400">{{ item.shelf_life }}</span>
                                    </div>
                                </div>
                            </div>

                            <!-- Bottom Price & Order CTA -->
                            <div class="p-4 sm:p-5 pt-0">
                                <div class="pt-3 border-t border-gray-100 flex items-center justify-between">
                                    <div>
                                        <span class="text-[10px] text-slate-400 block">{{ item.sold }}</span>
                                        <span class="font-black text-slate-900 text-base sm:text-lg">{{ formatRupiah(item.price) }}</span>
                                    </div>
                                    <button
                                        type="button"
                                        @click="openOrder(item)"
                                        class="bg-orange-500 hover:bg-orange-600 text-white px-4 py-2 rounded-xl text-xs font-bold transition shadow-xs flex items-center gap-1 cursor-pointer"
                                    >
                                        <span>Titip PO</span>
                                        <ArrowRight class="w-3.5 h-3.5" />
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Empty state -->
                    <div v-else class="text-center py-16 bg-slate-50 rounded-2xl border border-slate-200">
                        <p class="text-slate-500 text-sm">Tidak ada kuliner yang cocok dengan kata kunci Anda.</p>
                        <button
                            type="button"
                            @click="selectedCategory = 'Semua Kategori'; searchQuery = ''"
                            class="mt-3 text-xs font-bold text-orange-600 hover:underline"
                        >
                            Reset Filter
                        </button>
                    </div>
                </div>
            </section>
        </main>

        <!-- Order Modal -->
        <div
            v-if="activeOrderModal"
            class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60 backdrop-blur-xs"
        >
            <div class="bg-white rounded-3xl max-w-md w-full p-6 shadow-2xl relative max-h-[90vh] flex flex-col overflow-hidden">
                <div class="flex items-center justify-between pb-3 border-b border-gray-100">
                    <div class="flex items-center gap-2">
                        <div class="w-8 h-8 rounded-xl bg-orange-50 text-orange-600 flex items-center justify-center">
                            <ShoppingBag class="w-4 h-4" />
                        </div>
                        <h3 class="font-bold text-slate-900 text-base">Titip Open PO Kuliner</h3>
                    </div>
                    <button type="button" @click="activeOrderModal = null" class="p-1.5 text-slate-400 hover:text-slate-700 rounded-full hover:bg-slate-100">
                        <X class="w-5 h-5" />
                    </button>
                </div>

                <div class="py-4 space-y-4 overflow-y-auto flex-1">
                    <div class="flex items-center gap-3">
                        <img :src="activeOrderModal.image" :alt="activeOrderModal.title" class="w-16 h-16 rounded-xl object-cover" />
                        <div>
                            <h4 class="font-bold text-slate-900 text-sm line-clamp-1">{{ activeOrderModal.title }}</h4>
                            <span class="text-xs text-slate-500 block">{{ activeOrderModal.origin }}</span>
                            <span class="text-sm font-black text-orange-600">{{ formatRupiah(activeOrderModal.price) }}</span>
                        </div>
                    </div>

                    <!-- Quantity Counter -->
                    <div class="flex items-center justify-between p-3 bg-slate-50 rounded-xl border border-slate-200">
                        <span class="text-xs font-bold text-slate-700">Jumlah Pesanan</span>
                        <div class="flex items-center gap-3">
                            <button
                                type="button"
                                @click="orderQuantity = Math.max(1, orderQuantity - 1)"
                                class="w-7 h-7 rounded-lg bg-white border border-gray-200 flex items-center justify-center font-bold text-slate-700"
                            >
                                <Minus class="w-3.5 h-3.5" />
                            </button>
                            <span class="text-sm font-black text-slate-900 min-w-[20px] text-center">{{ orderQuantity }}</span>
                            <button
                                type="button"
                                @click="orderQuantity++"
                                class="w-7 h-7 rounded-lg bg-white border border-gray-200 flex items-center justify-center font-bold text-slate-700"
                            >
                                <Plus class="w-3.5 h-3.5" />
                            </button>
                        </div>
                    </div>

                    <!-- Address input -->
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Kota & Alamat Pengiriman</label>
                        <textarea
                            v-model="orderAddress"
                            rows="2"
                            placeholder="Contoh: Jl. Sudirman No. 12, Kebayoran Baru, Jakarta Selatan"
                            class="w-full text-xs bg-slate-50 border border-slate-200 rounded-xl p-2.5 focus:ring-2 focus:ring-orange-500 focus:outline-none"
                        ></textarea>
                    </div>

                    <!-- Total Calculation -->
                    <div class="flex items-center justify-between p-3 bg-orange-50 rounded-xl border border-orange-200">
                        <span class="text-xs font-bold text-slate-700">Total Pembayaran</span>
                        <span class="text-lg font-black text-orange-600">{{ formatRupiah(activeOrderModal.price * orderQuantity) }}</span>
                    </div>
                </div>

                <div class="pt-3 border-t border-gray-100 flex items-center gap-2">
                    <button
                        type="button"
                        @click="submitOrder"
                        class="flex-1 bg-orange-500 hover:bg-orange-600 text-white py-2.5 rounded-xl text-xs font-bold transition shadow-xs cursor-pointer"
                    >
                        Pesan Titip PO Sekarang
                    </button>
                    <button
                        type="button"
                        @click="activeOrderModal = null"
                        class="px-4 py-2.5 rounded-xl border border-gray-200 text-xs font-bold text-slate-600 hover:bg-gray-50"
                    >
                        Batal
                    </button>
                </div>
            </div>
        </div>

        <Footer />
        <BottomNav active-menu="Open PO" :user="user" />

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
