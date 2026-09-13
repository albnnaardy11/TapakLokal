<script setup>
import { ref, computed } from 'vue';
import { Head, Link } from '@inertiajs/vue3';
import Navbar from '@/Components/Navbar.vue';
import BottomNav from '@/Components/BottomNav.vue';
import Footer from '@/Components/Footer.vue';
import {
    BookOpen,
    Clock,
    User,
    ArrowRight,
    Search,
    Compass,
    Sparkles,
    Calendar,
    X
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
});

const selectedCategory = ref('Semua Kategori');
const searchQuery = ref('');
const activeArticleModal = ref(null);

const categories = ['Semua Kategori', 'Rute Hidden Gem', 'Tips & Panduan', 'Budaya & Adat', 'Kuliner Nusantara'];

const articles = [
    {
        id: 1,
        title: '5 Rute Hidden Gem di Sekitar Kaldera Bromo yang Jarang Diketahui Turis',
        category: 'Rute Hidden Gem',
        read_time: '4 Menit Baca',
        date: '10 September 2026',
        author: 'Cak Dimas (Mitra Akamsi Bromo)',
        image: 'https://images.unsplash.com/photo-1605649487212-47bdab064df8?auto=format&fit=crop&w=800&q=80',
        excerpt: 'Bosan dengan spot Penanjakan 1 yang padat? Warga Tengger punya bukit rahasia untuk menikmati sunrise Bromo dengan hening.',
        content: 'Bromo menyimpan magis yang luar biasa jika Anda tahu jalur yang tepat. Bukit Dingklik dan sabana B29 menawarkan sudut pandang yang jauh lebih intim tanpa kerumunan ratusan jeep.',
    },
    {
        id: 2,
        title: 'Etika Masuk Desa Adat Kete Kesu Toraja: Hal yang Wajib & Pantang Dilakukan',
        category: 'Budaya & Adat',
        read_time: '5 Menit Baca',
        date: '05 September 2026',
        author: 'Kak Pongtorra (Tetua Adat Kete Kesu)',
        image: 'https://images.unsplash.com/photo-1544644181-1484b3fdfc62?auto=format&fit=crop&w=800&q=80',
        excerpt: 'Menghormati leluhur dan upacara Rambu Solo membutuhkan kepekaan budaya. Simak panduan langsung dari tetua desa.',
        content: 'Dalam adat Toraja, setiap ukiran Tongkonan memiliki makna filosofi luhur. Jangan pernah melangkahi sesaji atau memegang patung Tau-Tau sembarangan tanpa izin tetua.',
    },
    {
        id: 3,
        title: 'Panduan Bertemu Pari Manta di Manta Point Labuan Bajo dengan Aman & Lestari',
        category: 'Tips & Panduan',
        read_time: '6 Menit Baca',
        date: '28 Agustus 2026',
        author: 'Bang Mansur (Penyelam Bajo)',
        image: 'https://images.unsplash.com/photo-1518548419970-58e3b4079ab2?auto=format&fit=crop&w=800&q=80',
        excerpt: 'Berenang bersama satwa raksasa yang anggun menuntut pemahaman arus laut selat Flores dan etika penyelaman.',
        content: 'Kunci utama saat snorkeling di Manta Point adalah tetap tenang dan jangan pernah menyentuh tubuh pari manta agar lapisan lendir pelindungnya tidak rusak.',
    },
    {
        id: 4,
        title: 'Rahasia Legit Pie Susu Asli Bali yang Bertahan Hingga Lintas Generasi',
        category: 'Kuliner Nusantara',
        read_time: '3 Menit Baca',
        date: '20 Agustus 2026',
        author: 'Tim Kurasi Kuliner TapakLokal',
        image: 'https://images.unsplash.com/photo-1557082673-e962804c8651?auto=format&fit=crop&w=800&q=80',
        excerpt: 'Mengapa kulit pie susu tradisional Denpasar selalu renyah berpadu dengan fla custard susu yang tidak terlalu manis?',
        content: 'Resep tradisional menggunakan mentega berkualitas tinggi dan teknik memanggang perlahan di oven batu bata tradisi.',
    },
];

const filteredArticles = computed(() => {
    return articles.filter((art) => {
        const matchesCat = selectedCategory.value === 'Semua Kategori' || art.category === selectedCategory.value;
        const matchesQuery = !searchQuery.value ||
            art.title.toLowerCase().includes(searchQuery.value.toLowerCase()) ||
            art.excerpt.toLowerCase().includes(searchQuery.value.toLowerCase());
        return matchesCat && matchesQuery;
    });
});
</script>

<template>
    <Head>
        <title>Blog & Catatan Petualang Nusantara - TapakLokal</title>
    </Head>

    <div class="min-h-screen flex flex-col bg-slate-50 font-sans text-slate-800 antialiased selection:bg-[#0052cc] selection:text-white">
        <Navbar :user="user" active-menu="Blog" />

        <main class="flex-1">
            <!-- Hero Section Selaras -->
            <section class="relative pt-2 sm:pt-4 md:pt-6 pb-8 sm:pb-12">
                <div class="max-w-7xl mx-auto px-3 sm:px-6 lg:px-8">
                    <div class="relative rounded-2xl sm:rounded-3xl md:rounded-[36px] overflow-hidden min-h-[380px] sm:min-h-[440px] flex items-center justify-center text-center shadow-2xl">
                        <div class="absolute inset-0 z-0">
                            <img
                                src="https://images.unsplash.com/photo-1596402184320-417e7178b2cd?auto=format&fit=crop&w=2000&q=85"
                                alt="Blog TapakLokal"
                                class="w-full h-full object-cover object-center transform scale-105"
                            />
                            <div class="absolute inset-0 bg-gradient-to-b from-black/60 via-black/50 to-black/85"></div>
                            <div class="absolute inset-0 bg-radial-gradient from-transparent via-black/20 to-black/60"></div>
                        </div>

                        <div class="relative z-10 max-w-3xl mx-auto px-4 sm:px-6 pt-6 pb-20 text-white">
                            <div class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-blue-500/25 border border-blue-400/40 text-blue-300 text-xs font-bold mb-4 backdrop-blur-xs">
                                <BookOpen class="w-3.5 h-3.5 text-amber-400" />
                                <span>Jurnal Eksplorasi & Cerita Akamsi</span>
                            </div>
                            <h1 class="text-2xl sm:text-4xl md:text-5xl lg:text-6xl font-extrabold tracking-tight leading-[1.2] drop-shadow-md">
                                Catatan Petualang Nusantara.
                            </h1>
                            <p class="mt-3 sm:mt-4 text-xs sm:text-sm md:text-base text-slate-100/90 font-normal leading-relaxed max-w-2xl mx-auto drop-shadow">
                                Inspirasi rute tersembunyi, panduan etika desa adat, dan wawasan kuliner otentik yang ditulis langsung oleh pemandu lokal.
                            </p>
                        </div>
                    </div>

                    <!-- Floating Search Bar -->
                    <div class="max-w-4xl mx-auto -mt-16 sm:-mt-20 md:-mt-22 relative z-20 px-1 sm:px-4">
                        <div class="bg-white rounded-2xl sm:rounded-3xl shadow-2xl border border-gray-100 p-4 backdrop-blur-xs">
                            <div class="bg-gray-50 border border-gray-200 rounded-xl px-3.5 py-2 flex items-center gap-2">
                                <Search class="w-4 h-4 text-slate-400 shrink-0" />
                                <input
                                    v-model="searchQuery"
                                    type="text"
                                    placeholder="Cari artikel, tips rute, atau cerita adat..."
                                    class="w-full bg-transparent text-xs sm:text-sm text-slate-800 placeholder-slate-400 border-none p-0 focus:outline-none focus:ring-0 font-medium"
                                />
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Category Pills -->
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
                                ? 'bg-[#0052cc] text-white'
                                : 'bg-white text-slate-600 hover:bg-slate-100 border border-slate-200/80'
                        ]"
                    >
                        {{ cat }}
                    </button>
                </div>
            </section>

            <!-- Articles Grid -->
            <section class="py-6 sm:py-10 bg-white">
                <div class="max-w-7xl mx-auto px-3 sm:px-6 lg:px-8">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                        <div
                            v-for="art in filteredArticles"
                            :key="art.id"
                            class="bg-white rounded-3xl border border-gray-200/80 shadow-xs hover:shadow-xl transition-all duration-300 overflow-hidden flex flex-col justify-between"
                        >
                            <div>
                                <div class="relative h-56 w-full overflow-hidden bg-slate-100">
                                    <img :src="art.image" :alt="art.title" class="w-full h-full object-cover hover:scale-105 transition duration-500" />
                                    <span class="absolute top-3 left-3 bg-[#0052cc] text-white text-[11px] font-bold px-3 py-1 rounded-full shadow-xs">
                                        {{ art.category }}
                                    </span>
                                </div>
                                <div class="p-6">
                                    <div class="flex items-center gap-3 text-xs text-slate-400 mb-2">
                                        <span class="flex items-center gap-1"><Calendar class="w-3.5 h-3.5" /> {{ art.date }}</span>
                                        <span>•</span>
                                        <span class="flex items-center gap-1"><Clock class="w-3.5 h-3.5" /> {{ art.read_time }}</span>
                                    </div>
                                    <h3 class="font-extrabold text-slate-900 text-lg leading-snug hover:text-[#0052cc] transition cursor-pointer" @click="activeArticleModal = art">
                                        {{ art.title }}
                                    </h3>
                                    <p class="mt-2 text-xs sm:text-sm text-slate-600 leading-relaxed line-clamp-2">
                                        {{ art.excerpt }}
                                    </p>
                                </div>
                            </div>

                            <div class="p-6 pt-0 border-t border-gray-100 flex items-center justify-between">
                                <div class="text-xs text-slate-500 font-medium">
                                    Oleh: <strong class="text-slate-800">{{ art.author }}</strong>
                                </div>
                                <button
                                    type="button"
                                    @click="activeArticleModal = art"
                                    class="text-[#0052cc] hover:text-[#003ea8] text-xs font-bold flex items-center gap-1 cursor-pointer"
                                >
                                    <span>Baca Lengkap</span>
                                    <ArrowRight class="w-3.5 h-3.5" />
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </main>

        <!-- Article Reader Modal -->
        <div
            v-if="activeArticleModal"
            class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60 backdrop-blur-xs"
        >
            <div class="bg-white rounded-3xl max-w-2xl w-full p-6 sm:p-8 shadow-2xl relative max-h-[90vh] flex flex-col overflow-hidden">
                <div class="flex items-center justify-between pb-3 border-b border-gray-100">
                    <span class="text-xs font-bold text-[#0052cc] uppercase">{{ activeArticleModal.category }}</span>
                    <button type="button" @click="activeArticleModal = null" class="p-1.5 text-slate-400 hover:text-slate-700 rounded-full hover:bg-slate-100">
                        <X class="w-5 h-5" />
                    </button>
                </div>

                <div class="py-4 space-y-4 overflow-y-auto flex-1 pr-1">
                    <h2 class="font-black text-slate-900 text-xl sm:text-2xl leading-tight">
                        {{ activeArticleModal.title }}
                    </h2>
                    <div class="flex items-center gap-2 text-xs text-slate-500">
                        <span>Penulis: <strong>{{ activeArticleModal.author }}</strong></span>
                        <span>•</span>
                        <span>{{ activeArticleModal.date }}</span>
                    </div>
                    <img :src="activeArticleModal.image" :alt="activeArticleModal.title" class="w-full h-56 object-cover rounded-2xl shadow-xs" />
                    <p class="text-sm text-slate-700 leading-relaxed font-normal">
                        {{ activeArticleModal.content }}
                    </p>
                    <div class="p-4 bg-blue-50 rounded-2xl border border-blue-200 text-xs text-slate-700 leading-relaxed">
                        <strong class="text-[#0052cc]">Catatan TapakLokal:</strong> Bertualanglah secara bertanggung jawab. Bawa pulang sampahmu, dukung warung warga setempat, dan patuhi kearifan lokal di mana pun kakimu berpijak.
                    </div>
                </div>

                <div class="pt-3 border-t border-gray-100 flex justify-end">
                    <button
                        type="button"
                        @click="activeArticleModal = null"
                        class="px-5 py-2 bg-[#0052cc] text-white rounded-xl text-xs font-bold"
                    >
                        Tutup Bacaan
                    </button>
                </div>
            </div>
        </div>

        <Footer />
        <BottomNav active-menu="Blog" :user="user" />
    </div>
</template>
