<script setup>
import { ref } from 'vue';
import { Head, Link } from '@inertiajs/vue3';
import { route } from 'ziggy-js';
import {
    ArrowLeft,
    ChevronRight,
    Search,
    ThumbsUp,
    ThumbsDown,
    Share2,
    Check,
    ShieldCheck,
    HelpCircle,
    MessageCircle,
    Phone,
    Mail,
    AlertCircle,
    ArrowRight,
    X,
    FileText,
} from 'lucide-vue-next';
import MainNavigation from '../Components/Shared/MainNavigation.vue';
import MainFooter from '../Components/Shared/MainFooter.vue';

const props = defineProps({
    category: {
        type: Object,
        required: true,
    },
    article: {
        type: Object,
        required: true,
    },
    relatedArticles: {
        type: Array,
        default: () => [],
    },
});

// Feedback state
const feedback = ref(null); // 'yes' | 'no'
const isCopied = ref(false);

const giveFeedback = (type) => {
    feedback.value = type;
};

const copyArticleLink = () => {
    navigator.clipboard.writeText(window.location.href);
    isCopied.value = true;
    setTimeout(() => {
        isCopied.value = false;
    }, 2500);
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
    <Head :title="`${article.title} - Pusat Bantuan TapakLokal`">
        <meta
            name="description"
            :content="article.summary || `${article.title}. Panduan resmi dan solusi bantuan dari TapakLokal.`"
        />
    </Head>

    <div class="min-h-screen bg-[#f8fafc] font-sans text-[#172c50] antialiased selection:bg-[#0088ff] selection:text-white flex flex-col justify-between">
        <div>
            <!-- Main Navigation on Top -->
            <MainNavigation />

            <!-- Clean Crisp Header Bar (Contrasts cleanly with Navbar blue row) -->
            <section class="w-full bg-white border-b border-slate-200/90 shadow-2xs py-3.5 sm:py-4">
                <div class="mx-auto w-full max-w-[1180px] px-4 sm:px-6 lg:px-0 flex items-center justify-between gap-4">
                    
                    <!-- Back Link and Breadcrumbs -->
                    <div class="flex items-center gap-3">
                        <Link
                            :href="safeRoute('help.category', { category: category.slug })"
                            class="inline-flex items-center justify-center size-8.5 rounded-full bg-slate-100 hover:bg-slate-200 text-slate-700 transition-colors cursor-pointer shrink-0"
                            :aria-label="`Kembali ke ${category.name}`"
                        >
                            <ArrowLeft class="size-4 text-slate-700" />
                        </Link>
                        <span class="text-xs sm:text-sm font-bold tracking-tight text-slate-800 flex items-center gap-1.5 flex-wrap">
                            <Link :href="safeRoute('help.index')" class="text-slate-500 hover:text-[#0088ff] transition-colors">Help Center</Link>
                            <ChevronRight class="size-3.5 text-slate-400" />
                            <Link :href="safeRoute('help.category', { category: category.slug })" class="text-slate-500 hover:text-[#0088ff] transition-colors">{{ category.name }}</Link>
                            <ChevronRight class="size-3.5 text-slate-400 hidden sm:inline" />
                            <span class="text-slate-900 font-extrabold truncate max-w-[240px] sm:max-w-md hidden sm:inline">{{ article.title }}</span>
                        </span>
                    </div>

                    <!-- Right Quick Link to Main Help -->
                    <Link
                        :href="safeRoute('help.index')"
                        class="text-xs font-bold text-slate-600 hover:text-[#0088ff] bg-slate-100 hover:bg-slate-200 px-3 py-1.5 rounded-lg transition-colors hidden sm:inline-flex items-center gap-1.5"
                    >
                        <HelpCircle class="size-3.5" />
                        <span>Semua Kategori</span>
                    </Link>

                </div>
            </section>

            <!-- MAIN ARTICLE BODY -->
            <main class="mx-auto max-w-[1180px] px-4 sm:px-6 lg:px-0 py-8 sm:py-12">
                
                <div class="grid grid-cols-1 lg:grid-cols-[1fr_340px] gap-8 lg:gap-12 items-start">
                    
                    <!-- LEFT / MAIN CONTENT: Article Card -->
                    <article class="bg-white rounded-3xl border border-slate-200/80 p-6 sm:p-9 shadow-xs">
                        
                        <!-- Header Badges -->
                        <div class="flex flex-wrap items-center gap-2 mb-3">
                            <span
                                class="inline-block rounded-md px-2.5 py-1 text-[11px] font-extrabold tracking-wide uppercase"
                                :class="{
                                    'bg-cyan-50 text-cyan-700': category.slug === 'general-info' || category.slug === 'general',
                                    'bg-sky-50 text-[#0088ff]': category.slug === 'open-trip',
                                    'bg-emerald-50 text-emerald-700': category.slug === 'private-trip',
                                    'bg-amber-50 text-amber-700': category.slug === 'culinary-po',
                                    'bg-violet-50 text-violet-700': category.slug === 'payment-methods' || category.slug === 'payment',
                                    'bg-indigo-50 text-indigo-700': category.slug === 'wallet',
                                    'bg-blue-50 text-blue-700': category.slug === 'profile-security' || category.slug === 'profile',
                                    'bg-rose-50 text-rose-700': category.slug === 'anti-pungli',
                                    'bg-teal-50 text-teal-700': category.slug === 'partners-guide' || category.slug === 'partners',
                                }"
                            >
                                {{ category.name }}
                            </span>

                            <span v-if="article.subcategory" class="text-xs text-slate-500 font-semibold bg-slate-100 px-2.5 py-1 rounded-md">
                                {{ article.subcategory }}
                            </span>

                            <span class="text-xs text-slate-400 font-medium ml-auto flex items-center gap-1">
                                <ShieldCheck class="size-3.5 text-emerald-500" />
                                Terverifikasi
                            </span>
                        </div>

                        <!-- Article Title -->
                        <h1 class="text-xl sm:text-2xl lg:text-[26px] font-black text-slate-900 leading-snug tracking-tight mb-4">
                            {{ article.title }}
                        </h1>

                        <!-- Summary Lead -->
                        <p v-if="article.summary" class="text-sm font-medium text-slate-600 pb-5 mb-6 border-b border-slate-100 leading-relaxed">
                            {{ article.summary }}
                        </p>

                        <!-- Formatted Article Content -->
                        <div class="prose prose-slate max-w-none text-xs sm:text-sm text-slate-700 leading-relaxed space-y-4">
                            <div
                                v-for="(paragraph, idx) in article.content.trim().split('\n\n')"
                                :key="idx"
                            >
                                <!-- Render Headings ### -->
                                <h3
                                    v-if="paragraph.startsWith('###')"
                                    class="text-sm sm:text-base font-black text-slate-900 mt-5 mb-2"
                                >
                                    {{ paragraph.replace(/^###\s*/, '') }}
                                </h3>

                                <!-- Render Blockquote Tips -->
                                <div
                                    v-else-if="paragraph.startsWith('>')"
                                    class="rounded-2xl bg-sky-50/80 border-l-4 border-[#0088ff] p-4 text-xs sm:text-[13px] font-medium text-slate-700 my-3"
                                >
                                    {{ paragraph.replace(/^>\s*/, '').replace(/\*\*(.*?)\*\*/g, '$1') }}
                                </div>

                                <!-- Render Lists -->
                                <div v-else-if="paragraph.startsWith('1.') || paragraph.startsWith('-')">
                                    <ul class="space-y-2 my-2 pl-4 list-disc text-slate-700">
                                        <li
                                            v-for="(item, itemIdx) in paragraph.split('\n')"
                                            :key="itemIdx"
                                            class="leading-relaxed"
                                        >
                                            <span v-html="item.replace(/^(\d+\.|\-)\s*/, '').replace(/\*\*(.*?)\*\*/g, '<strong>$1</strong>')"></span>
                                        </li>
                                    </ul>
                                </div>

                                <!-- Render Regular Paragraph -->
                                <p v-else class="leading-relaxed" v-html="paragraph.replace(/\*\*(.*?)\*\*/g, '<strong>$1</strong>')"></p>
                            </div>
                        </div>

                        <!-- Feedback Rating Widget -->
                        <div class="mt-10 pt-6 border-t border-slate-100 rounded-2xl bg-slate-50 p-5 flex flex-col sm:flex-row items-center justify-between gap-4">
                            <div>
                                <h4 class="text-xs sm:text-sm font-bold text-slate-900">
                                    Apakah artikel ini membantu Anda?
                                </h4>
                                <p class="text-[11px] sm:text-xs text-slate-500 font-medium">
                                    Bantu kami meningkatkan kualitas panduan layanan TapakLokal.
                                </p>
                            </div>

                            <div class="flex items-center gap-2">
                                <button
                                    type="button"
                                    class="inline-flex items-center gap-1.5 rounded-xl px-4 py-2 text-xs font-bold transition-all cursor-pointer"
                                    :class="feedback === 'yes' ? 'bg-[#0088ff] text-white shadow-xs' : 'bg-white text-slate-700 border border-slate-200 hover:bg-slate-100'"
                                    @click="giveFeedback('yes')"
                                >
                                    <ThumbsUp class="size-3.5" />
                                    <span>Ya</span>
                                </button>
                                <button
                                    type="button"
                                    class="inline-flex items-center gap-1.5 rounded-xl px-4 py-2 text-xs font-bold transition-all cursor-pointer"
                                    :class="feedback === 'no' ? 'bg-rose-600 text-white shadow-xs' : 'bg-white text-slate-700 border border-slate-200 hover:bg-slate-100'"
                                    @click="giveFeedback('no')"
                                >
                                    <ThumbsDown class="size-3.5" />
                                    <span>Tidak</span>
                                </button>
                                <button
                                    type="button"
                                    class="p-2 rounded-xl bg-white border border-slate-200 text-slate-600 hover:text-[#0088ff] transition-colors cursor-pointer"
                                    title="Salin Tautan Artikel"
                                    @click="copyArticleLink"
                                >
                                    <Check v-if="isCopied" class="size-4 text-emerald-600" />
                                    <Share2 v-else class="size-4" />
                                </button>
                            </div>
                        </div>

                    </article>

                    <!-- RIGHT SIDEBAR: Related Articles & Support Contact -->
                    <aside class="space-y-6">
                        
                        <!-- Related Articles in this category -->
                        <div class="bg-white rounded-3xl border border-slate-200/80 p-6 shadow-xs">
                            <h3 class="text-sm font-black text-slate-900 mb-4 pb-2 border-b border-slate-100 flex items-center gap-2">
                                <FileText class="size-4 text-[#0088ff]" />
                                <span>Topik Terkait di {{ category.name }}</span>
                            </h3>

                            <div v-if="relatedArticles.length > 0" class="divide-y divide-slate-100 space-y-1">
                                <Link
                                    v-for="rel in relatedArticles"
                                    :key="rel.id"
                                    :href="safeRoute('help.article', { category: category.slug, slug: rel.slug })"
                                    class="py-2.5 flex items-center justify-between gap-3 group text-left"
                                >
                                    <span class="text-xs font-semibold text-slate-700 group-hover:text-[#0088ff] transition-colors line-clamp-2">
                                        {{ rel.title }}
                                    </span>
                                    <ChevronRight class="size-4 text-[#0088ff] shrink-0 opacity-70 group-hover:opacity-100 group-hover:translate-x-0.5 transition-all" />
                                </Link>
                            </div>

                            <!-- Back to Category Link -->
                            <div class="mt-4 pt-3 border-t border-slate-100">
                                <Link
                                    :href="safeRoute('help.category', { category: category.slug })"
                                    class="text-xs font-bold text-[#0088ff] hover:underline flex items-center gap-1"
                                >
                                    <span>Lihat Semua Topik {{ category.name }}</span>
                                    <ChevronRight class="size-3" />
                                </Link>
                            </div>
                        </div>

                        <!-- Need more help? Card -->
                        <div class="bg-gradient-to-br from-sky-50 to-indigo-50/50 rounded-3xl border border-sky-100 p-6">
                            <div class="grid size-10 place-items-center rounded-2xl bg-[#0088ff] text-white shadow-xs mb-3">
                                <MessageCircle class="size-5" />
                            </div>
                            <h4 class="text-sm font-black text-slate-900">
                                Butuh Bantuan Lain?
                            </h4>
                            <p class="text-xs text-slate-600 mt-1 leading-relaxed">
                                Tim customer service TapakLokal siap membantumu setiap hari.
                            </p>

                            <button
                                type="button"
                                class="mt-4 w-full rounded-xl bg-[#0088ff] py-2.5 px-4 text-xs font-bold text-white hover:bg-[#0076de] transition-colors shadow-xs cursor-pointer flex items-center justify-center gap-2"
                                @click="openContactModal"
                            >
                                <MessageCircle class="size-3.5" />
                                <span>Hubungi Customer Service</span>
                            </button>
                        </div>

                    </aside>

                </div>

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
