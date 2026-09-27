<script setup>
import { computed, onMounted, onUnmounted, ref, watch } from 'vue';
import { Head, Link } from '@inertiajs/vue3';
import { route } from 'ziggy-js';
import {
    Info,
    TentTree,
    Crown,
    Utensils,
    CreditCard,
    Wallet,
    UserCheck,
    ShieldAlert,
    UsersRound,
    LayoutGrid,
    Search,
    ChevronRight,
    ChevronLeft,
    HelpCircle,
    Phone,
    Mail,
    MessageCircle,
    ArrowRight,
    AlertCircle,
    X,
} from 'lucide-vue-next';
import MainNavigation from '../Components/Shared/MainNavigation.vue';
import MainFooter from '../Components/Shared/MainFooter.vue';

const props = defineProps({
    categories: {
        type: Array,
        default: () => [],
    },
    popularArticles: {
        type: Array,
        default: () => [],
    },
    allArticles: {
        type: Array,
        default: () => [],
    },
    initialQuery: {
        type: String,
        default: '',
    },
});

// Icon mapping lookup
const iconMap = {
    Info,
    TentTree,
    Crown,
    Utensils,
    CreditCard,
    Wallet,
    UserCheck,
    ShieldAlert,
    UsersRound,
    LayoutGrid,
};

const getCategoryIcon = (iconName) => {
    return iconMap[iconName] || HelpCircle;
};

// Search Query
const searchQuery = ref(props.initialQuery || '');

// Quick Search Tags
const quickTags = [
    { label: '#CaraRefund', query: 'refund' },
    { label: '#MetodeBayar', query: 'pembayaran' },
    { label: '#KuotaOpenTrip', query: 'kuota' },
    { label: '#PO_OlehOleh', query: 'oleh-oleh' },
    { label: '#InfoUmum', query: 'informasi umum' },
    { label: '#TapakWallet', query: 'tapakwallet' },
    { label: '#LaporPungli', query: 'pungli' },
];

const setQuickTag = (tagQuery) => {
    searchQuery.value = tagQuery;
    currentPage.value = 1;
};

const defaultCategories = [
    {
        id: 'general-info',
        slug: 'general-info',
        name: 'Informasi Umum',
        shortName: 'Informasi Umum',
        icon: 'Info',
        colorClass: 'bg-cyan-50 text-cyan-600 border-cyan-100 group-hover:bg-cyan-600 group-hover:text-white',
    },
    {
        id: 'open-trip',
        slug: 'open-trip',
        name: 'Open Trip',
        shortName: 'Open Trip',
        icon: 'TentTree',
        colorClass: 'bg-sky-50 text-[#0088ff] border-sky-100 group-hover:bg-[#0088ff] group-hover:text-white',
    },
    {
        id: 'private-trip',
        slug: 'private-trip',
        name: 'Private Trip',
        shortName: 'Private Trip',
        icon: 'Crown',
        colorClass: 'bg-emerald-50 text-emerald-600 border-emerald-100 group-hover:bg-emerald-600 group-hover:text-white',
    },
    {
        id: 'culinary-po',
        slug: 'culinary-po',
        name: 'PO Oleh-Oleh & Kuliner',
        shortName: 'PO Oleh-Oleh',
        icon: 'Utensils',
        colorClass: 'bg-amber-50 text-amber-600 border-amber-100 group-hover:bg-amber-500 group-hover:text-white',
    },
    {
        id: 'payment-methods',
        slug: 'payment-methods',
        name: 'Metode Pembayaran',
        shortName: 'Metode Pembayaran',
        icon: 'CreditCard',
        colorClass: 'bg-violet-50 text-violet-600 border-violet-100 group-hover:bg-violet-600 group-hover:text-white',
    },
    {
        id: 'wallet',
        slug: 'wallet',
        name: 'TapakWallet & Refund',
        shortName: 'TapakWallet',
        icon: 'Wallet',
        colorClass: 'bg-indigo-50 text-indigo-600 border-indigo-100 group-hover:bg-indigo-600 group-hover:text-white',
    },
    {
        id: 'profile-security',
        slug: 'profile-security',
        name: 'Profil & Keamanan KYC',
        shortName: 'Akun & KYC',
        icon: 'UserCheck',
        colorClass: 'bg-blue-50 text-blue-600 border-blue-100 group-hover:bg-blue-600 group-hover:text-white',
    },
    {
        id: 'anti-pungli',
        slug: 'anti-pungli',
        name: 'Lapor Pungli & Standar',
        shortName: 'Lapor Pungli',
        icon: 'ShieldAlert',
        colorClass: 'bg-rose-50 text-rose-600 border-rose-100 group-hover:bg-rose-600 group-hover:text-white',
    },
    {
        id: 'partners-guide',
        slug: 'partners-guide',
        name: 'Mitra & Pemandu Lokal',
        shortName: 'Mitra & Guide',
        icon: 'UsersRound',
        colorClass: 'bg-teal-50 text-teal-600 border-teal-100 group-hover:bg-teal-600 group-hover:text-white',
    },
    {
        id: 'all',
        slug: 'all',
        name: 'Semua Kategori',
        shortName: 'Semua Produk',
        icon: 'LayoutGrid',
        colorClass: 'bg-slate-100 text-slate-700 border-slate-200 group-hover:bg-slate-800 group-hover:text-white',
    },
];

const effectiveCategories = computed(() => {
    return props.categories && props.categories.length > 0 ? props.categories : defaultCategories;
});

// Filtered articles
const filteredArticles = computed(() => {
    let list = props.allArticles && props.allArticles.length > 0 ? props.allArticles : [];

    if (!searchQuery.value.trim()) {
        // When not searching, show all or popular list
        return list;
    }

    const q = searchQuery.value.toLowerCase().trim();
    return list.filter((a) => {
        return (
            a.title.toLowerCase().includes(q) ||
            (a.summary && a.summary.toLowerCase().includes(q)) ||
            (a.content && a.content.toLowerCase().includes(q)) ||
            (a.categoryLabel && a.categoryLabel.toLowerCase().includes(q)) ||
            (a.tags && a.tags.some((t) => t.toLowerCase().includes(q)))
        );
    });
});

// Pagination (Limit to 5 items per page so it never gets too long)
const itemsPerPage = 5;
const currentPage = ref(1);

const totalPages = computed(() => {
    return Math.ceil(filteredArticles.value.length / itemsPerPage) || 1;
});

const paginatedArticles = computed(() => {
    const start = (currentPage.value - 1) * itemsPerPage;
    return filteredArticles.value.slice(start, start + itemsPerPage);
});

// Reset page when search query changes
watch(searchQuery, () => {
    currentPage.value = 1;
});

const goToPage = (page) => {
    if (page >= 1 && page <= totalPages.value) {
        currentPage.value = page;
        // Smooth scroll to top of topics section
        const el = document.getElementById('popular-topics-heading');
        if (el) {
            el.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
        }
    }
};

// Safe route helper
const safeRoute = (name, params) => {
    try {
        if (typeof route === 'function') {
            return params !== undefined ? route(name, params) : route(name);
        }
        return '#';
    } catch {
        return '#';
    }
};

// Contact Support Modal State
const isContactModalOpen = ref(false);
const openContactModal = () => {
    isContactModalOpen.value = true;
    document.body.style.overflow = 'hidden';
};
const closeContactModal = () => {
    isContactModalOpen.value = false;
    document.body.style.overflow = '';
};

// Keyboard listener for Escape key
const handleKeyDown = (e) => {
    if (e.key === 'Escape') {
        if (isContactModalOpen.value) closeContactModal();
    }
};

onMounted(() => {
    window.addEventListener('keydown', handleKeyDown);
});

onUnmounted(() => {
    window.removeEventListener('keydown', handleKeyDown);
    document.body.style.overflow = '';
});
</script>

<template>
    <Head title="Pusat Bantuan (Help Center) - TapakLokal">
        <meta
            name="description"
            content="Pusat Bantuan resmi TapakLokal: temukan jawaban dan panduan seputar Open Trip, Private Trip, Open PO Oleh-Oleh khas daerah, Metode Pembayaran, TapakWallet, kebijakan refund, dan anti-pungli."
        />
    </Head>

    <div class="min-h-screen bg-[#f8fafc] font-sans text-[#172c50] antialiased selection:bg-[#0088ff] selection:text-white flex flex-col justify-between">
        <div>
            <!-- Main Navigation on Top (Transparent integration) -->
            <MainNavigation :transparent-on-top="true" />

            <!-- Full-Width Edge-to-Edge Hero Section (Compact 1:1 with Homepage Hero Height) -->
            <section class="relative w-full overflow-hidden bg-[#0a2347] text-white" aria-labelledby="help-hero-title">
                <!-- Full Width Background Image -->
                <img
                    src="https://images.unsplash.com/photo-1507525428034-b723cf961d3e?auto=format&fit=crop&w=1920&q=88"
                    alt="Pusat Bantuan TapakLokal"
                    class="absolute inset-0 z-0 size-full object-cover object-center brightness-[0.82] transition-opacity duration-500"
                />
                <!-- Rich Brand Blue Gradient Overlay matching Navbar #3E7BEF / #0088ff -->
                <div class="absolute inset-0 z-[1] bg-[linear-gradient(180deg,rgba(10,35,71,0.60)_0%,rgba(62,123,239,0.40)_45%,rgba(3,36,84,0.90)_100%)] pointer-events-none"></div>
                <div class="absolute inset-0 z-[1] bg-[radial-gradient(ellipse_at_center,rgba(0,136,255,0.25)_0%,transparent_75%)] pointer-events-none"></div>

                <!-- Centered Hero Content Container (Balanced Height & Proportions) -->
                <div class="relative z-10 mx-auto w-full max-w-[1180px] px-4 pt-32 pb-8 sm:px-6 sm:pt-36 sm:pb-10 lg:px-0 lg:pt-40 lg:pb-12 flex flex-col items-center justify-center">
                    <!-- Hero Title -->
                    <div class="max-w-3xl text-center text-white drop-shadow-md mb-5 sm:mb-6">
                        <h1
                            id="help-hero-title"
                            class="text-2xl font-bold leading-tight tracking-tight sm:text-3xl lg:text-[36px]"
                        >
                            TapakLokal Help Center
                        </h1>
                        <p class="mt-2 text-xs sm:text-sm md:text-[15px] font-medium text-white/90">
                            Find your answers here
                        </p>
                    </div>

                    <!-- Large White Search Bar (1:1 Traveloka Reference Design) -->
                    <div class="w-full max-w-2xl mx-auto">
                        <div class="relative flex items-center">
                            <Search class="pointer-events-none absolute left-4 sm:left-5 size-5 text-slate-400" />
                            <input
                                v-model="searchQuery"
                                type="text"
                                placeholder="Type your topic here (e.g. pembayaran, refund, kuota, oleh-oleh)..."
                                class="h-12 sm:h-13.5 w-full rounded-full border-0 bg-white pl-12 sm:pl-14 pr-12 text-xs sm:text-sm font-medium text-slate-800 placeholder:text-slate-400 focus:outline-none focus:ring-4 focus:ring-sky-300/60 shadow-[0_10px_28px_rgba(0,35,80,0.28)] transition-all"
                            />
                            <button
                                v-if="searchQuery"
                                type="button"
                                class="absolute right-3.5 grid size-7 place-items-center rounded-full bg-slate-100 text-slate-500 hover:bg-slate-200 transition-colors cursor-pointer"
                                aria-label="Bersihkan pencarian"
                                @click="searchQuery = ''"
                            >
                                <X class="size-4" />
                            </button>
                        </div>

                        <!-- Clickable Quick Search Chips -->
                        <div class="mt-3 flex flex-wrap items-center justify-center gap-1.5 sm:gap-2">
                            <span class="text-[11px] font-bold text-white/85 hidden sm:inline">Pencarian Populer:</span>
                            <button
                                v-for="tag in quickTags"
                                :key="tag.label"
                                type="button"
                                class="rounded-full bg-white/15 backdrop-blur-md px-2.5 py-0.5 text-[11px] font-semibold text-white hover:bg-white/30 border border-white/20 transition-all cursor-pointer shadow-xs active:scale-95"
                                @click="setQuickTag(tag.query)"
                            >
                                {{ tag.label }}
                            </button>
                        </div>

                        <!-- Active Search Status Pill -->
                        <div v-if="searchQuery" class="mt-2.5 flex items-center justify-center gap-2 text-xs text-white/95">
                            <span class="bg-black/40 backdrop-blur-md px-3 py-0.5 rounded-full border border-white/20 text-[11px]">
                                Menampilkan {{ filteredArticles.length }} hasil untuk "<strong>{{ searchQuery }}</strong>"
                                <button
                                    type="button"
                                    class="underline font-bold hover:text-white cursor-pointer ml-1.5 text-sky-200"
                                    @click="searchQuery = ''"
                                >
                                    Reset
                                </button>
                            </span>
                        </div>
                    </div>
                </div>
            </section>

            <!-- MAIN CONTENT AREA: 2-Column Traveloka Reference Layout -->
            <main class="mx-auto max-w-[1180px] px-4 sm:px-6 lg:px-0 py-10 sm:py-14">
                <div class="grid grid-cols-1 lg:grid-cols-[1.15fr_1fr] gap-10 lg:gap-14 items-start">
                    
                    <!-- LEFT COLUMN: Popular Topics (Paginated & Compact) -->
                    <section aria-labelledby="popular-topics-heading" class="flex flex-col">
                        <div class="flex items-center justify-between pb-2 border-b border-slate-100 mb-2">
                            <h2
                                id="popular-topics-heading"
                                class="text-xl sm:text-2xl font-black tracking-tight text-slate-900"
                            >
                                {{ searchQuery ? 'Hasil Pencarian Topik' : 'Popular Topics' }}
                            </h2>

                            <span class="text-xs font-semibold text-slate-500">
                                Total {{ filteredArticles.length }} Artikel
                            </span>
                        </div>

                        <!-- Topics List (Links to dedicated article page) -->
                        <div v-if="paginatedArticles.length > 0" class="divide-y divide-slate-200/80">
                            <Link
                                v-for="item in paginatedArticles"
                                :key="item.id"
                                :href="safeRoute('help.article', { category: item.category, slug: item.slug })"
                                class="w-full py-4 text-left flex items-center justify-between gap-4 group transition-colors hover:bg-slate-50/80 -mx-2 px-2 rounded-xl"
                            >
                                <div class="flex-1 pr-2">
                                    <div class="flex items-center gap-2 mb-1">
                                        <span
                                            class="inline-block rounded-md px-2 py-0.5 text-[10px] font-extrabold tracking-wide uppercase"
                                            :class="{
                                                'bg-cyan-50 text-cyan-700': item.category === 'general-info' || item.category === 'general',
                                                'bg-sky-50 text-[#0088ff]': item.category === 'open-trip',
                                                'bg-emerald-50 text-emerald-700': item.category === 'private-trip',
                                                'bg-amber-50 text-amber-700': item.category === 'culinary-po',
                                                'bg-violet-50 text-violet-700': item.category === 'payment-methods' || item.category === 'payment',
                                                'bg-indigo-50 text-indigo-700': item.category === 'wallet',
                                                'bg-blue-50 text-blue-700': item.category === 'profile-security' || item.category === 'profile',
                                                'bg-rose-50 text-rose-700': item.category === 'anti-pungli',
                                                'bg-teal-50 text-teal-700': item.category === 'partners-guide' || item.category === 'partners',
                                            }"
                                        >
                                            {{ item.categoryLabel }}
                                        </span>
                                    </div>
                                    <h3 class="text-sm sm:text-[15px] font-bold text-slate-800 group-hover:text-[#0088ff] transition-colors leading-snug">
                                        {{ item.title }}
                                    </h3>
                                    <p class="mt-1 text-xs text-slate-500 line-clamp-1 font-normal">
                                        {{ item.summary }}
                                    </p>
                                </div>

                                <!-- Chevron Right Icon matching Traveloka Layout -->
                                <ChevronRight class="size-5 text-[#0088ff] shrink-0 transition-transform group-hover:translate-x-1" />
                            </Link>
                        </div>

                        <!-- Number Pagination Controls (Keeps list neat & clean) -->
                        <div
                            v-if="totalPages > 1"
                            class="mt-6 pt-4 border-t border-slate-100 flex items-center justify-between"
                        >
                            <span class="text-xs text-slate-500 font-medium">
                                Halaman {{ currentPage }} dari {{ totalPages }}
                            </span>

                            <div class="flex items-center gap-1.5">
                                <button
                                    type="button"
                                    :disabled="currentPage === 1"
                                    class="grid size-8 place-items-center rounded-lg border border-slate-200 text-slate-600 hover:bg-slate-100 disabled:opacity-40 disabled:cursor-not-allowed transition-colors cursor-pointer"
                                    aria-label="Halaman sebelumnya"
                                    @click="goToPage(currentPage - 1)"
                                >
                                    <ChevronLeft class="size-4" />
                                </button>

                                <button
                                    v-for="page in totalPages"
                                    :key="page"
                                    type="button"
                                    class="size-8 rounded-lg text-xs font-bold transition-all cursor-pointer"
                                    :class="currentPage === page ? 'bg-[#0088ff] text-white shadow-xs' : 'border border-slate-200 text-slate-700 hover:bg-slate-100'"
                                    @click="goToPage(page)"
                                >
                                    {{ page }}
                                </button>

                                <button
                                    type="button"
                                    :disabled="currentPage === totalPages"
                                    class="grid size-8 place-items-center rounded-lg border border-slate-200 text-slate-600 hover:bg-slate-100 disabled:opacity-40 disabled:cursor-not-allowed transition-colors cursor-pointer"
                                    aria-label="Halaman selanjutnya"
                                    @click="goToPage(currentPage + 1)"
                                >
                                    <ChevronRight class="size-4" />
                                </button>
                            </div>
                        </div>

                        <!-- Empty State when searching -->
                        <div v-if="filteredArticles.length === 0" class="py-12 text-center rounded-2xl bg-slate-50 border border-slate-200/60 p-6 mt-4">
                            <div class="grid size-12 place-items-center rounded-full bg-sky-100 text-[#0088ff] mx-auto mb-3">
                                <HelpCircle class="size-6" />
                            </div>
                            <h4 class="text-base font-bold text-slate-800">Topik tidak ditemukan</h4>
                            <p class="mt-1 text-xs text-slate-500 max-w-sm mx-auto">
                                Coba gunakan kata kunci yang lebih umum atau hubungi layanan bantuan Customer Service kami langsung.
                            </p>
                            <button
                                type="button"
                                class="mt-4 inline-flex items-center gap-1.5 rounded-xl bg-[#0088ff] px-4 py-2 text-xs font-bold text-white hover:bg-[#0076de] transition-colors shadow-xs cursor-pointer"
                                @click="openContactModal"
                            >
                                <MessageCircle class="size-3.5" />
                                <span>Hubungi CS TapakLokal</span>
                            </button>
                        </div>
                    </section>

                    <!-- RIGHT COLUMN: Explore by Product (Navigates directly to dedicated Category Help Page) -->
                    <section aria-labelledby="explore-products-heading" class="flex flex-col bg-white rounded-3xl p-6 sm:p-7 border border-slate-200/80 shadow-xs">
                        <div class="mb-6 flex items-center justify-between">
                            <h2
                                id="explore-products-heading"
                                class="text-xl sm:text-2xl font-black tracking-tight text-slate-900"
                            >
                                Explore by Product
                            </h2>
                            <span class="text-[11px] font-bold text-[#0088ff] bg-sky-50 px-2.5 py-1 rounded-full">
                                Kategori Panduan
                            </span>
                        </div>

                        <!-- Circular Icons Grid (Links to /bantuan/{category}) -->
                        <div class="grid grid-cols-3 sm:grid-cols-4 lg:grid-cols-5 gap-y-6 gap-x-2.5 sm:gap-x-3 text-center">
                            <Link
                                v-for="cat in effectiveCategories"
                                :key="cat.id"
                                :href="cat.slug === 'all' ? safeRoute('help.index') : safeRoute('help.category', { category: cat.slug })"
                                class="group flex flex-col items-center justify-start focus-visible:outline-none cursor-pointer"
                            >
                                <!-- Circular Button Icon -->
                                <div
                                    class="size-12 sm:size-13.5 lg:size-13 rounded-full flex items-center justify-center border transition-all duration-200 group-hover:scale-105 shadow-xs"
                                    :class="cat.colorClass"
                                >
                                    <component :is="getCategoryIcon(cat.icon)" class="size-5.5 sm:size-6 stroke-[2.2]" />
                                </div>

                                <!-- Label Below Icon -->
                                <span
                                    class="mt-2 text-[10.5px] sm:text-[11px] font-bold leading-tight transition-colors line-clamp-2 max-w-[80px] text-slate-700 group-hover:text-[#0088ff]"
                                >
                                    {{ cat.shortName }}
                                </span>
                            </Link>
                        </div>
                    </section>

                </div>

                <!-- BOTTOM SECTION: Contact Us (1:1 Traveloka Reference Style) -->
                <section aria-labelledby="contact-heading" class="mt-14 sm:mt-18 pt-10 border-t border-slate-200">
                    <div class="max-w-xl text-left">
                        <h3 id="contact-heading" class="text-lg sm:text-xl font-black text-slate-900">
                            Contact us
                        </h3>
                        <p class="mt-1 text-xs sm:text-sm text-slate-600 font-medium leading-relaxed">
                            Still have questions? Tap below to get in touch with our customer service.
                        </p>

                        <!-- Traveloka-Style Light Blue Pill Contact Button -->
                        <div class="mt-4">
                            <button
                                type="button"
                                class="inline-flex items-center gap-2 rounded-xl bg-sky-50 border border-sky-200 px-5 py-2.5 text-xs sm:text-sm font-extrabold text-[#0088ff] hover:bg-[#0088ff] hover:text-white shadow-xs transition-all duration-200 cursor-pointer hover:shadow-md"
                                @click="openContactModal"
                            >
                                <MessageCircle class="size-4" />
                                <span>Contact us</span>
                            </button>
                        </div>
                    </div>
                </section>
            </main>
        </div>

        <!-- CONTACT SUPPORT MODAL -->
        <Teleport to="body">
            <div
                v-if="isContactModalOpen"
                class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm animate-in fade-in duration-200"
                role="dialog"
                aria-modal="true"
                aria-label="Hubungi Customer Service TapakLokal"
                @click.self="closeContactModal"
            >
                <div class="relative w-full max-w-lg rounded-3xl bg-white shadow-2xl overflow-hidden border border-slate-100">
                    
                    <!-- Modal Header -->
                    <div class="p-6 border-b border-slate-100 bg-gradient-to-r from-sky-50 via-white to-indigo-50/40 flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <div class="grid size-11 place-items-center rounded-2xl bg-[#0088ff] text-white shadow-sm">
                                <Phone class="size-5.5" />
                            </div>
                            <div>
                                <h3 class="text-lg font-black text-[#172c50]">
                                    Hubungi Customer Service
                                </h3>
                                <p class="text-xs text-slate-500 font-medium">
                                    Tim CS TapakLokal siap membantumu
                                </p>
                            </div>
                        </div>

                        <button
                            type="button"
                            class="grid size-8 place-items-center rounded-full bg-slate-100 text-slate-500 hover:bg-slate-200 transition-colors cursor-pointer"
                            aria-label="Tutup"
                            @click="closeContactModal"
                        >
                            <X class="size-4" />
                        </button>
                    </div>

                    <!-- Modal Body: Contact Channels -->
                    <div class="p-6 space-y-3">
                        
                        <!-- 1. WhatsApp CS -->
                        <a
                            href="https://wa.me/6281234567890?text=Halo%20Admin%20TapakLokal,%20saya%20butuh%20bantuan%20seputar%20trip/produk"
                            target="_blank"
                            rel="noopener noreferrer"
                            class="group flex items-center justify-between p-4 rounded-2xl border border-emerald-200/80 bg-emerald-50/40 hover:bg-emerald-50 transition-all hover:border-emerald-300 shadow-xs"
                        >
                            <div class="flex items-center gap-3.5">
                                <div class="grid size-10 place-items-center rounded-xl bg-emerald-600 text-white shadow-xs">
                                    <MessageCircle class="size-5" />
                                </div>
                                <div class="text-left">
                                    <h4 class="text-xs sm:text-sm font-bold text-slate-900 group-hover:text-emerald-700 transition-colors">
                                        Chat WhatsApp Resmi CS
                                    </h4>
                                    <p class="text-[11px] text-slate-500 font-medium">
                                        Respon cepat setiap hari · 08.00 – 22.00 WIB
                                    </p>
                                </div>
                            </div>
                            <ArrowRight class="size-4 text-emerald-600 group-hover:translate-x-1 transition-transform" />
                        </a>

                        <!-- 2. Pesan Bantuan / Support Ticket -->
                        <Link
                            :href="safeRoute('account.section', 'support')"
                            class="group flex items-center justify-between p-4 rounded-2xl border border-sky-200/80 bg-sky-50/40 hover:bg-sky-50 transition-all hover:border-sky-300 shadow-xs"
                            @click="closeContactModal"
                        >
                            <div class="flex items-center gap-3.5">
                                <div class="grid size-10 place-items-center rounded-xl bg-[#0088ff] text-white shadow-xs">
                                    <Mail class="size-5" />
                                </div>
                                <div class="text-left">
                                    <h4 class="text-xs sm:text-sm font-bold text-slate-900 group-hover:text-[#0088ff] transition-colors">
                                        Kirim Tiket / Pesan Bantuan
                                    </h4>
                                    <p class="text-[11px] text-slate-500 font-medium">
                                        Pengaduan terstruktur dengan lampiran bukti
                                    </p>
                                </div>
                            </div>
                            <ArrowRight class="size-4 text-[#0088ff] group-hover:translate-x-1 transition-transform" />
                        </Link>

                        <!-- 3. Email Resmi -->
                        <a
                            href="mailto:support@tapaklokal.com"
                            class="group flex items-center justify-between p-4 rounded-2xl border border-slate-200 bg-white hover:bg-slate-50 transition-all hover:border-slate-300"
                        >
                            <div class="flex items-center gap-3.5">
                                <div class="grid size-10 place-items-center rounded-xl bg-slate-800 text-white shadow-xs">
                                    <Mail class="size-5" />
                                </div>
                                <div class="text-left">
                                    <h4 class="text-xs sm:text-sm font-bold text-slate-900 group-hover:text-[#0088ff] transition-colors">
                                        Email Dukungan Pengguna
                                    </h4>
                                    <p class="text-[11px] text-slate-500 font-medium">
                                        support@tapaklokal.com
                                    </p>
                                </div>
                            </div>
                            <ArrowRight class="size-4 text-slate-400 group-hover:translate-x-1 transition-transform" />
                        </a>

                        <!-- 4. Hotline Darurat Lapangan 24 Jam -->
                        <div class="p-3.5 rounded-2xl bg-amber-50 border border-amber-200/80 flex items-start gap-2.5">
                            <AlertCircle class="size-4.5 text-amber-600 shrink-0 mt-0.5" />
                            <div class="text-[11px] text-slate-700 leading-relaxed">
                                <strong class="font-bold text-amber-900">Kendala Darurat Saat Trip Berlangsung?</strong>
                                <p class="text-slate-600 mt-0.5">Hubungi Hotline Siaga Lapangan 24 Jam: <strong>0811-9988-7711</strong> (Khusus kondisi medis/keselamatan di lokasi trip).</p>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </Teleport>

        <!-- Main Footer matching Global Design -->
        <MainFooter />
    </div>
</template>
