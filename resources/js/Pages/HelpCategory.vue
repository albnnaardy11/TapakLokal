<script setup>
import { computed, ref, watch } from 'vue';
import { Head, Link } from '@inertiajs/vue3';
import { route } from 'ziggy-js';
import {
    ArrowLeft,
    Search,
    ChevronRight,
    ChevronLeft,
    HelpCircle,
    MessageCircle,
    Phone,
    Mail,
    AlertCircle,
    ArrowRight,
    X,
} from 'lucide-vue-next';
import MainNavigation from '../Components/Shared/MainNavigation.vue';
import MainFooter from '../Components/Shared/MainFooter.vue';

const props = defineProps({
    category: {
        type: Object,
        required: true,
    },
    popularArticles: {
        type: Array,
        default: () => [],
    },
    subcategories: {
        type: Object,
        default: () => ({}),
    },
    articles: {
        type: Array,
        default: () => [],
    },
    initialQuery: {
        type: String,
        default: '',
    },
});

const searchQuery = ref(props.initialQuery || '');
const isShowingAllPopular = ref(false);

// Filtered articles when searching
const filteredArticles = computed(() => {
    if (!searchQuery.value.trim()) {
        return [];
    }
    const q = searchQuery.value.toLowerCase().trim();
    return props.articles.filter((a) => {
        return (
            a.title.toLowerCase().includes(q) ||
            (a.summary && a.summary.toLowerCase().includes(q)) ||
            (a.content && a.content.toLowerCase().includes(q)) ||
            (a.tags && a.tags.some((t) => t.toLowerCase().includes(q)))
        );
    });
});

// Displayed popular topics list
const displayedPopular = computed(() => {
    if (isShowingAllPopular.value) {
        return props.popularArticles;
    }
    return props.popularArticles.slice(0, 4);
});

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

// Contact Support Modal
const isContactModalOpen = ref(false);
const openContactModal = () => {
    isContactModalOpen.value = true;
    document.body.style.overflow = 'hidden';
};
const closeContactModal = () => {
    isContactModalOpen.value = false;
    document.body.style.overflow = '';
};
</script>

<template>
    <Head :title="`Help Center - ${category.name} | TapakLokal`">
        <meta
            name="description"
            :content="category.description || `Panduan dan informasi bantuan mengenai ${category.name} di TapakLokal.`"
        />
    </Head>

    <div class="min-h-screen bg-[#f8fafc] font-sans text-[#172c50] antialiased selection:bg-[#0088ff] selection:text-white flex flex-col justify-between">
        <div>
            <!-- Main Navigation on Top -->
            <MainNavigation />

            <!-- Clean Crisp Header Bar (Contrasts cleanly with Navbar blue row) -->
            <section class="w-full bg-white border-b border-slate-200/90 shadow-2xs py-4 sm:py-4.5">
                <div class="mx-auto w-full max-w-[1180px] px-4 sm:px-6 lg:px-0 flex flex-col md:flex-row items-center justify-between gap-4">
                    
                    <!-- Back Link and Category Title -->
                    <div class="flex items-center gap-3 self-start md:self-auto">
                        <Link
                            :href="safeRoute('help.index')"
                            class="inline-flex items-center justify-center size-9 rounded-full bg-slate-100 hover:bg-slate-200 text-slate-700 transition-colors cursor-pointer shrink-0"
                            aria-label="Kembali ke Pusat Bantuan Utama"
                        >
                            <ArrowLeft class="size-4.5 text-slate-700" />
                        </Link>
                        <h1 class="text-lg sm:text-xl font-black tracking-tight text-slate-900 flex items-center gap-2">
                            <span>Help Center – {{ category.name }}</span>
                        </h1>
                    </div>

                    <!-- Search Input (Crisp border & smooth focus) -->
                    <div class="w-full md:w-96 relative">
                        <Search class="pointer-events-none absolute left-3.5 top-1/2 -translate-y-1/2 size-4 text-slate-400" />
                        <input
                            v-model="searchQuery"
                            type="text"
                            placeholder="Type your topic here (e.g. refund)"
                            class="h-10 w-full rounded-full border border-slate-200 bg-slate-50/80 pl-10 pr-9 text-xs sm:text-sm font-medium text-slate-800 placeholder:text-slate-400 focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#3E7BEF] focus:border-transparent transition-all shadow-2xs"
                        />
                        <button
                            v-if="searchQuery"
                            type="button"
                            class="absolute right-2.5 top-1/2 -translate-y-1/2 grid size-5 place-items-center rounded-full bg-slate-200 text-slate-600 hover:bg-slate-300 transition-colors cursor-pointer"
                            @click="searchQuery = ''"
                        >
                            <X class="size-3" />
                        </button>
                    </div>

                </div>
            </section>

            <!-- MAIN BODY CONTENT (1:1 with Traveloka Reference Screenshots 2 & 3) -->
            <main class="mx-auto max-w-[1180px] px-4 sm:px-6 lg:px-0 py-10 sm:py-14">
                
                <!-- If Searching: Show Live Search Results -->
                <div v-if="searchQuery.trim()" class="mb-12">
                    <div class="flex items-center justify-between pb-3 border-b border-slate-200 mb-6">
                        <h2 class="text-xl font-extrabold text-slate-900">
                            Hasil Pencarian di {{ category.name }}
                        </h2>
                        <span class="text-xs font-semibold text-slate-500">
                            {{ filteredArticles.length }} topik ditemukan
                        </span>
                    </div>

                    <div v-if="filteredArticles.length > 0" class="divide-y divide-slate-200 bg-white rounded-2xl p-6 border border-slate-200/80 shadow-xs">
                        <Link
                            v-for="item in filteredArticles"
                            :key="item.id"
                            :href="safeRoute('help.article', { category: category.slug, slug: item.slug })"
                            class="w-full py-4 text-left flex items-center justify-between gap-4 group transition-colors hover:bg-slate-50 -mx-2 px-2 rounded-xl"
                        >
                            <div class="flex-1 pr-2">
                                <h3 class="text-sm font-bold text-slate-800 group-hover:text-[#0088ff] transition-colors leading-snug">
                                    {{ item.title }}
                                </h3>
                                <p class="mt-1 text-xs text-slate-500 line-clamp-1">
                                    {{ item.summary }}
                                </p>
                            </div>
                            <ChevronRight class="size-4.5 text-[#0088ff] shrink-0 transition-transform group-hover:translate-x-1" />
                        </Link>
                    </div>

                    <div v-else class="py-12 text-center rounded-2xl bg-white border border-slate-200/80 p-6">
                        <HelpCircle class="size-8 text-slate-400 mx-auto mb-2" />
                        <h3 class="text-sm font-bold text-slate-800">Tidak ada topik yang cocok</h3>
                        <p class="text-xs text-slate-500 mt-1">Coba gunakan kata kunci lain atau lihat daftar lengkap di bawah.</p>
                    </div>
                </div>

                <!-- 3D Magnifying Glass Icon Graphic (1:1 with Traveloka Reference) -->
                <div class="flex flex-col items-center justify-center mb-8">
                    <div class="relative size-20 sm:size-24 flex items-center justify-center">
                        <div class="absolute inset-0 rounded-full bg-gradient-to-tr from-sky-400 to-[#0088ff] opacity-15 blur-xl"></div>
                        <!-- Stylized 3D Magnifying Glass Icon -->
                        <div class="relative size-16 sm:size-20 rounded-full bg-gradient-to-br from-sky-300 via-[#0088ff] to-[#005bb5] p-1 shadow-lg flex items-center justify-center">
                            <div class="size-full rounded-full bg-gradient-to-br from-sky-100 to-sky-50 flex items-center justify-center border-2 border-white/60">
                                <Search class="size-7 sm:size-9 text-[#0088ff] stroke-[2.4]" />
                            </div>
                        </div>
                    </div>
                </div>

                <!-- SECTION 1: Popular Topics (Clean List with Chevrons) -->
                <section aria-labelledby="popular-topics-heading" class="max-w-3xl mx-auto mb-14">
                    <h2
                        id="popular-topics-heading"
                        class="text-base sm:text-lg font-black tracking-tight text-slate-900 mb-2 pb-2"
                    >
                        Popular Topics
                    </h2>

                    <!-- List items with subtle dividers -->
                    <div class="divide-y divide-slate-200 border-t border-b border-slate-200">
                        <Link
                            v-for="item in displayedPopular"
                            :key="item.id"
                            :href="safeRoute('help.article', { category: category.slug, slug: item.slug })"
                            class="w-full py-3.5 text-left flex items-center justify-between gap-4 group transition-colors hover:bg-white px-2 rounded-lg"
                        >
                            <span class="text-xs sm:text-sm font-bold text-slate-800 group-hover:text-[#0088ff] transition-colors leading-snug">
                                {{ item.title }}
                            </span>
                            <ChevronRight class="size-4.5 text-[#0088ff] shrink-0 transition-transform group-hover:translate-x-1" />
                        </Link>
                    </div>

                    <!-- "More Articles" link button if more than 4 -->
                    <div v-if="popularArticles.length > 4" class="mt-3 text-left">
                        <button
                            type="button"
                            class="text-xs font-bold text-[#0088ff] hover:underline cursor-pointer transition-colors"
                            @click="isShowingAllPopular = !isShowingAllPopular"
                        >
                            {{ isShowingAllPopular ? 'Tampilkan Lebih Sedikit' : 'More Articles' }}
                        </button>
                    </div>
                </section>

                <!-- SECTION 2: Category Info (Subcategory Multi-Column Grid 1:1 with Traveloka Reference) -->
                <section aria-labelledby="category-info-heading" class="mt-12 pt-10 border-t border-slate-200">
                    <h2
                        id="category-info-heading"
                        class="text-lg sm:text-xl font-black text-slate-900 mb-8"
                    >
                        {{ category.name }} Info
                    </h2>

                    <!-- 3-Column Subcategory Grid -->
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8 sm:gap-10">
                        <div
                            v-for="(articlesList, subcategoryTitle) in subcategories"
                            :key="subcategoryTitle"
                            class="flex flex-col"
                        >
                            <h3 class="text-xs sm:text-[13px] font-black text-slate-900 uppercase tracking-wider mb-4 pb-2 border-b border-slate-200">
                                {{ subcategoryTitle }}
                            </h3>

                            <div class="divide-y divide-slate-100 space-y-1">
                                <Link
                                    v-for="item in articlesList"
                                    :key="item.id"
                                    :href="safeRoute('help.article', { category: category.slug, slug: item.slug })"
                                    class="py-2.5 flex items-center justify-between gap-3 group text-left transition-colors hover:text-[#0088ff]"
                                >
                                    <span class="text-xs font-medium text-slate-700 group-hover:text-[#0088ff] transition-colors leading-relaxed">
                                        {{ item.title }}
                                    </span>
                                    <ChevronRight class="size-4 text-[#0088ff] shrink-0 opacity-80 group-hover:opacity-100 group-hover:translate-x-0.5 transition-all" />
                                </Link>
                            </div>
                        </div>
                    </div>
                </section>

                <!-- BOTTOM SECTION: Contact Us -->
                <section aria-labelledby="contact-heading" class="mt-16 pt-10 border-t border-slate-200">
                    <div class="max-w-xl text-left">
                        <h3 id="contact-heading" class="text-lg sm:text-xl font-black text-slate-900">
                            Contact us
                        </h3>
                        <p class="mt-1 text-xs sm:text-sm text-slate-600 font-medium leading-relaxed">
                            Still have questions? Tap below to get in touch with our customer service.
                        </p>

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

                    <div class="p-6 space-y-3">
                        <a
                            href="https://wa.me/6281234567890?text=Halo%20Admin%20TapakLokal,%20saya%20butuh%20bantuan"
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

                        <div class="p-3.5 rounded-2xl bg-amber-50 border border-amber-200/80 flex items-start gap-2.5">
                            <AlertCircle class="size-4.5 text-amber-600 shrink-0 mt-0.5" />
                            <div class="text-[11px] text-slate-700 leading-relaxed">
                                <strong class="font-bold text-amber-900">Kendala Darurat Saat Trip Berlangsung?</strong>
                                <p class="text-slate-600 mt-0.5">Hubungi Hotline Siaga Lapangan 24 Jam: <strong>0811-9988-7711</strong></p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </Teleport>

        <!-- Main Footer -->
        <MainFooter />
    </div>
</template>
