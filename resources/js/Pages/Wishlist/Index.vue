<script setup>
import { ref } from 'vue';
import { Head, Link } from '@inertiajs/vue3';
import Navbar from '@/Components/Navbar.vue';
import BottomNav from '@/Components/BottomNav.vue';
import Footer from '@/Components/Footer.vue';
import {
    Heart,
    MapPin,
    Calendar,
    Star,
    Trash2,
    ArrowRight,
    Compass,
    Sparkles,
    ShoppingBag
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
});

const wishlistItems = ref([
    {
        id: 1,
        type: 'trip',
        title: 'Bromo Midnight Sunrise & Savana Pasir Berbisik',
        location: 'Probolinggo - Malang, Jawa Timur',
        price: 385000,
        rating: 4.9,
        image: 'https://images.unsplash.com/photo-1605649487212-47bdab064df8?auto=format&fit=crop&w=800&q=80',
        departure: '20 Sept 2026',
    },
    {
        id: 2,
        type: 'trip',
        title: 'Liveaboard Komodo, Padar & Pink Beach Hidden Cove',
        location: 'Labuan Bajo, Flores, NTT',
        price: 2450000,
        rating: 5.0,
        image: 'https://images.unsplash.com/photo-1518548419970-58e3b4079ab2?auto=format&fit=crop&w=800&q=80',
        departure: '25 Sept 2026',
    },
    {
        id: 3,
        type: 'po',
        title: 'Pie Susu Dhian Asli Bali Box 50 Pcs Fresh Baked',
        location: 'Denpasar, Bali',
        price: 115000,
        rating: 5.0,
        image: 'https://images.unsplash.com/photo-1557082673-e962804c8651?auto=format&fit=crop&w=600&q=80',
        departure: 'Tutup PO Besok 18:00',
    },
]);

function removeItem(id) {
    wishlistItems.value = wishlistItems.value.filter(item => item.id !== id);
}

function formatRupiah(num) {
    return new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: 'IDR',
        maximumFractionDigits: 0,
    }).format(num);
}
</script>

<template>
    <Head>
        <title>Wishlist Destinasi & Oleh-Oleh Impian - TapakLokal</title>
    </Head>

    <div class="min-h-screen flex flex-col bg-slate-50 font-sans text-slate-800 antialiased selection:bg-[#0052cc] selection:text-white">
        <Navbar :user="user" active-menu="Wishlist" />

        <main class="flex-1">
            <!-- Hero Section Selaras -->
            <section class="relative pt-2 sm:pt-4 md:pt-6 pb-8 sm:pb-12">
                <div class="max-w-7xl mx-auto px-3 sm:px-6 lg:px-8">
                    <div class="relative rounded-2xl sm:rounded-3xl md:rounded-[36px] overflow-hidden min-h-[340px] sm:min-h-[400px] flex items-center justify-center text-center shadow-2xl">
                        <div class="absolute inset-0 z-0">
                            <img
                                src="https://images.unsplash.com/photo-1516690561799-46d8f74f9abf?auto=format&fit=crop&w=2000&q=85"
                                alt="Wishlist TapakLokal"
                                class="w-full h-full object-cover object-center transform scale-105"
                            />
                            <div class="absolute inset-0 bg-gradient-to-b from-black/60 via-black/50 to-black/85"></div>
                            <div class="absolute inset-0 bg-radial-gradient from-transparent via-black/20 to-black/60"></div>
                        </div>

                        <div class="relative z-10 max-w-2xl mx-auto px-4 sm:px-6 pt-4 pb-16 text-white">
                            <div class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-rose-500/25 border border-rose-400/40 text-rose-300 text-xs font-bold mb-4 backdrop-blur-xs">
                                <Heart class="w-3.5 h-3.5 text-rose-400 fill-rose-400" />
                                <span>Koleksi Destinasi Tersimpan</span>
                            </div>
                            <h1 class="text-2xl sm:text-4xl md:text-5xl font-extrabold tracking-tight leading-[1.2] drop-shadow-md">
                                Wishlist Petualanganmu.
                            </h1>
                            <p class="mt-2 sm:mt-3 text-xs sm:text-sm md:text-base text-slate-100/90 font-normal max-w-xl mx-auto drop-shadow">
                                Simpan rute impian dan titip oleh-oleh favorit untuk direalisasikan saat waktu liburanmu tiba.
                            </p>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Wishlist Content List -->
            <section class="py-8 sm:py-12 bg-white">
                <div class="max-w-5xl mx-auto px-3 sm:px-6 lg:px-8">
                    <div class="flex items-center justify-between mb-6 pb-3 border-b border-gray-100">
                        <h2 class="text-lg sm:text-xl font-bold text-slate-900">
                            Tersimpan ({{ wishlistItems.length }} Item)
                        </h2>
                        <Link href="/open-trip" class="text-xs font-bold text-[#0052cc] hover:underline">
                            + Tambah Destinasi Lain
                        </Link>
                    </div>

                    <div v-if="wishlistItems.length > 0" class="space-y-4">
                        <div
                            v-for="item in wishlistItems"
                            :key="item.id"
                            class="flex flex-col sm:flex-row sm:items-center justify-between p-4 rounded-2xl bg-slate-50 border border-slate-200/80 hover:shadow-md transition gap-4"
                        >
                            <div class="flex items-center gap-4">
                                <img
                                    :src="item.image"
                                    :alt="item.title"
                                    class="w-20 h-20 sm:w-24 sm:h-24 rounded-2xl object-cover shrink-0"
                                />
                                <div>
                                    <div class="flex items-center gap-2 mb-1">
                                        <span
                                            :class="[
                                                'text-[10px] font-bold px-2 py-0.5 rounded-md uppercase',
                                                item.type === 'trip' ? 'bg-blue-100 text-[#0052cc]' : 'bg-orange-100 text-orange-700'
                                            ]"
                                        >
                                            {{ item.type === 'trip' ? 'Open Trip' : 'Titip PO' }}
                                        </span>
                                        <span class="text-xs text-slate-500 font-medium">{{ item.location }}</span>
                                    </div>
                                    <h3 class="font-bold text-slate-900 text-sm sm:text-base leading-snug line-clamp-1">
                                        {{ item.title }}
                                    </h3>
                                    <div class="text-xs text-slate-500 mt-1 flex items-center gap-1">
                                        <Calendar class="w-3.5 h-3.5 text-slate-400" />
                                        <span>{{ item.departure }}</span>
                                    </div>
                                    <div class="font-black text-slate-900 text-base sm:text-lg mt-1">
                                        {{ formatRupiah(item.price) }}
                                    </div>
                                </div>
                            </div>

                            <div class="flex items-center gap-2 self-end sm:self-center">
                                <button
                                    type="button"
                                    @click="removeItem(item.id)"
                                    class="p-2 text-slate-400 hover:text-red-500 hover:bg-white rounded-xl transition cursor-pointer"
                                    title="Hapus dari Wishlist"
                                >
                                    <Trash2 class="w-4 h-4" />
                                </button>
                                <Link
                                    :href="item.type === 'trip' ? '/open-trip' : '/open-po'"
                                    class="bg-[#0052cc] hover:bg-[#003ea8] text-white px-4 py-2 rounded-xl text-xs font-bold transition flex items-center gap-1.5 shadow-xs"
                                >
                                    <span>{{ item.type === 'trip' ? 'Booking Trip' : 'Pesan PO' }}</span>
                                    <ArrowRight class="w-3.5 h-3.5" />
                                </Link>
                            </div>
                        </div>
                    </div>

                    <div v-else class="text-center py-16 bg-slate-50 rounded-2xl border border-slate-200">
                        <Heart class="w-12 h-12 text-slate-300 mx-auto mb-3" />
                        <h3 class="font-bold text-slate-800 text-base">Wishlist Anda Masih Kosong</h3>
                        <p class="text-xs text-slate-500 mt-1 max-w-sm mx-auto">
                            Mulai tandai paket open trip atau oleh-oleh favorit Anda dengan menekan ikon hati.
                        </p>
                        <Link
                            href="/open-trip"
                            class="mt-4 inline-flex items-center gap-1.5 bg-[#0052cc] text-white font-bold px-4 py-2 rounded-xl text-xs"
                        >
                            <span>Eksplorasi Open Trip Sekarang</span>
                        </Link>
                    </div>
                </div>
            </section>
        </main>

        <Footer />
        <BottomNav active-menu="Wishlist" :user="user" />
    </div>
</template>
