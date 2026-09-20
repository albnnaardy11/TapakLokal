<script setup>
import { Head, Link } from '@inertiajs/vue3';
import { computed, nextTick, onBeforeUnmount, onMounted, ref, watch } from 'vue';
import {
    ArrowLeft,
    ArrowRight,
    ArrowUpRight,
    Bookmark,
    CalendarDays,
    Check,
    CheckCircle2,
    ChevronDown,
    ChevronLeft,
    ChevronRight,
    Clock3,
    Compass,
    Copy,
    Eye,
    Facebook,
    Heart,
    HelpCircle,
    Lightbulb,
    ListTree,
    MapPin,
    MapPinned,
    MessageSquare,
    Send,
    Share2,
    ShieldCheck,
    Sparkles,
    Star,
    Tag,
    Twitter,
    UserCheck,
    Users,
    Utensils,
    X,
} from 'lucide-vue-next';
import MainNavigation from '../Components/Shared/MainNavigation.vue';
import BlogDestinations from '../Components/Blog/BlogDestinations.vue';
import BlogDestinationCta from '../Components/Blog/BlogDestinationCta.vue';
import BlogNewsletter from '../Components/Blog/BlogNewsletter.vue';
import { articles, getArticleById } from '../Components/Home/travelArticles';

const props = defineProps({
    articleId: {
        type: String,
        default: 'bali',
    },
});

// Article Resolution
const article = computed(() => getArticleById(props.articleId));

// Related & Popular Articles
const relatedArticles = computed(() => {
    return articles.filter((item) => item.id !== article.value.id).slice(0, 3);
});

const popularArticles = computed(() => {
    return [...articles].sort((a, b) => (b.views || '').localeCompare(a.views || '')).slice(0, 4);
});

// Interaction States
const isSaved = ref(false);
const isLiked = ref(false);
const likeCount = ref(128);
const activeFontSize = ref('normal'); // 'normal' | 'medium' | 'large'
const isShareOpen = ref(false);
const isCopied = ref(false);
const toastMessage = ref('');
const isTocCollapsed = ref(false);

// Reaction States
const reactions = ref({
    helpful: { count: 0, active: false },
    inspiring: { count: 0, active: false },
    wantToGo: { count: 0, active: false },
});

// Initialize reactions from article data
watch(
    () => article.value,
    (current) => {
        if (current) {
            reactions.value = {
                helpful: { count: current.reactions?.helpful || 45, active: false },
                inspiring: { count: current.reactions?.inspiring || 32, active: false },
                wantToGo: { count: current.reactions?.wantToGo || 68, active: false },
            };
        }
    },
    { immediate: true },
);

// Comments System
const comments = ref([]);
const newCommentName = ref('');
const newCommentRole = ref('Traveler');
const newCommentRating = ref(5);
const newCommentText = ref('');
const isSubmittingComment = ref(false);

watch(
    () => article.value,
    (current) => {
        if (current) {
            comments.value = current.initialComments ? JSON.parse(JSON.stringify(current.initialComments)) : [];
        }
    },
    { immediate: true },
);

// Gallery Lightbox
const isGalleryOpen = ref(false);
const galleryIndex = ref(0);
const allImages = computed(() => {
    if (article.value.gallery && article.value.gallery.length > 0) {
        return article.value.gallery;
    }

    return [
        `https://images.unsplash.com/photo-${article.value.image}?auto=format&fit=crop&w=1200&q=88`,
    ];
});

const openGallery = (index = 0) => {
    galleryIndex.value = index;
    isGalleryOpen.value = true;
    document.body.style.overflow = 'hidden';
};

const closeGallery = () => {
    isGalleryOpen.value = false;
    document.body.style.overflow = '';
};

const nextImage = () => {
    galleryIndex.value = (galleryIndex.value + 1) % allImages.value.length;
};

const prevImage = () => {
    galleryIndex.value = (galleryIndex.value - 1 + allImages.value.length) % allImages.value.length;
};

// Toast Helper
const showToast = (message) => {
    toastMessage.value = message;
    setTimeout(() => {
        toastMessage.value = '';
    }, 3200);
};

// Bookmark Action
const toggleSave = () => {
    isSaved.value = !isSaved.value;
    showToast(isSaved.value ? 'Artikel disimpan ke daftar bacaan kamu!' : 'Artikel dihapus dari daftar simpanan.');
};

// Like Action
const toggleLike = () => {
    if (isLiked.value) {
        likeCount.value -= 1;
        isLiked.value = false;
    } else {
        likeCount.value += 1;
        isLiked.value = true;
        showToast('Terima kasih telah menyukai cerita ini!');
    }
};

// Reaction Action
const toggleReaction = (type) => {
    const item = reactions.value[type];
    if (item.active) {
        item.count -= 1;
        item.active = false;
    } else {
        item.count += 1;
        item.active = true;
        const labels = {
            helpful: 'Sangat Membantu',
            inspiring: 'Menginspirasi',
            wantToGo: 'Ingin Berkunjung',
        };
        showToast(`Tanggapan "${labels[type]}" berhasil dicatat!`);
    }
};

// Share Actions
const copyArticleLink = async () => {
    try {
        await navigator.clipboard.writeText(window.location.href);
        isCopied.value = true;
        showToast('Tautan artikel berhasil disalin ke clipboard!');
        setTimeout(() => {
            isCopied.value = false;
            isShareOpen.value = false;
        }, 2000);
    } catch {
        showToast('Tautan siap disalin dari bilah peramban.');
    }
};

const shareViaWhatsApp = () => {
    const text = encodeURIComponent(`${article.value.title} - Baca selengkapnya di TapakLokal: ${window.location.href}`);
    window.open(`https://wa.me/?text=${text}`, '_blank');
    isShareOpen.value = false;
};

const shareViaTwitter = () => {
    const text = encodeURIComponent(`${article.value.title} via @TapakLokal`);
    window.open(`https://twitter.com/intent/tweet?text=${text}&url=${encodeURIComponent(window.location.href)}`, '_blank');
    isShareOpen.value = false;
};

const shareViaFacebook = () => {
    window.open(`https://www.facebook.com/sharer/sharer.php?u=${encodeURIComponent(window.location.href)}`, '_blank');
    isShareOpen.value = false;
};

// Comment Actions
const handleCommentLike = (comment) => {
    comment.likes = (comment.likes || 0) + 1;
    showToast('Apresiasi komentar terkirim.');
};

const submitComment = () => {
    if (!newCommentName.value.trim() || !newCommentText.value.trim()) {
        showToast('Harap isi nama dan komentar kamu.');

        return;
    }

    isSubmittingComment.value = true;

    setTimeout(() => {
        const newEntry = {
            id: Date.now(),
            name: newCommentName.value.trim(),
            avatar: 'https://images.unsplash.com/photo-1535713875002-d1d0cf377fde?auto=format&fit=crop&w=120&q=80',
            role: newCommentRole.value || 'Traveler',
            date: 'Baru saja',
            rating: newCommentRating.value,
            content: newCommentText.value.trim(),
            likes: 0,
        };

        comments.value.unshift(newEntry);
        newCommentText.value = '';
        newCommentName.value = '';
        isSubmittingComment.value = false;
        showToast('Cerita atau komentar kamu berhasil dipublikasikan!');
    }, 400);
};

// Smooth Scroll to TOC
const scrollToSection = (sectionId) => {
    const el = document.getElementById(sectionId);
    if (el) {
        el.scrollIntoView({ behavior: 'smooth', block: 'start' });
    }
};

// Keyboard listener for gallery
const handleKeyDown = (e) => {
    if (!isGalleryOpen.value) {
        return;
    }
    if (e.key === 'Escape') {
        closeGallery();
    }
    if (e.key === 'ArrowRight') {
        nextImage();
    }
    if (e.key === 'ArrowLeft') {
        prevImage();
    }
};

onMounted(() => {
    window.addEventListener('keydown', handleKeyDown);
});

onBeforeUnmount(() => {
    window.removeEventListener('keydown', handleKeyDown);
    document.body.style.overflow = '';
});
</script>

<template>
    <Head :title="article.title">
        <meta name="description" :content="article.excerpt" />
    </Head>

    <div class="min-h-screen overflow-x-hidden bg-[#f8fafc] font-sans text-[#172c50]">
        <!-- Global Navigation -->
        <MainNavigation />

        <main class="mx-auto max-w-[1220px] px-4 py-6 sm:px-6 sm:py-8 lg:px-8 lg:py-10">
            <!-- Breadcrumbs -->
            <nav class="mb-6 flex flex-wrap items-center gap-2 text-xs font-medium text-slate-500" aria-label="Breadcrumb">
                <Link href="/" class="transition-colors hover:text-[#3E7BEF]">Beranda</Link>
                <ChevronRight class="size-3.5 text-slate-300" aria-hidden="true" />
                <Link :href="route('blog')" class="transition-colors hover:text-[#3E7BEF]">Cerita Perjalanan</Link>
                <ChevronRight class="size-3.5 text-slate-300" aria-hidden="true" />
                <span class="font-semibold text-[#3E7BEF]">{{ article.category }}</span>
            </nav>

            <!-- ARTICLE HERO HEADER -->
            <header class="mx-auto max-w-4xl text-left">
                <!-- Category Badge & Location -->
                <div class="flex flex-wrap items-center gap-2.5 sm:gap-3">
                    <span class="inline-flex items-center gap-1.5 rounded-full border border-[#bfe0ff] bg-[#edf6ff] px-3.5 py-1 text-xs font-bold text-[#1677e8] shadow-sm">
                        <Compass class="size-3.5" aria-hidden="true" />
                        {{ article.category }}
                    </span>
                    <span v-if="article.location" class="inline-flex items-center gap-1 text-xs font-semibold text-slate-500">
                        <MapPin class="size-3.5 text-[#3E7BEF]" aria-hidden="true" />
                        {{ article.location }}
                    </span>
                </div>

                <!-- Main Title -->
                <h1 class="mt-4 text-3xl font-extrabold leading-[1.18] tracking-tight text-[#173b70] sm:text-4xl lg:text-[42px]">
                    {{ article.title }}
                </h1>

                <!-- Excerpt / Subtitle -->
                <p class="mt-4 text-base leading-relaxed text-slate-600 sm:text-lg">
                    {{ article.excerpt }}
                </p>

                <!-- Author, Date & Reading Meta Bar -->
                <div class="mt-6 flex flex-col gap-4 border-y border-[#e2edf7] py-4 sm:flex-row sm:items-center sm:justify-between">
                    <!-- Author Information -->
                    <div class="flex items-center gap-3.5">
                        <div class="relative">
                            <img
                                :src="article.author?.avatar || 'https://images.unsplash.com/photo-1534528741775-53994a69daeb?auto=format&fit=crop&w=120&q=80'"
                                :alt="`Foto ${article.author?.name || 'Penulis'}`"
                                class="size-12 rounded-full border-2 border-white object-cover shadow-md"
                            />
                            <span class="absolute -bottom-1 -right-1 grid size-5 place-items-center rounded-full bg-[#1677e8] text-white ring-2 ring-white" title="Kontributor Terverifikasi">
                                <Check class="size-3 stroke-[3]" />
                            </span>
                        </div>
                        <div>
                            <div class="flex items-center gap-2">
                                <span class="text-sm font-bold text-[#173b70]">{{ article.author?.name || 'Kontributor TapakLokal' }}</span>
                                <span class="hidden rounded-md bg-[#eef7ff] px-2 py-0.5 text-[10px] font-bold text-[#1677e8] sm:inline-block">
                                    {{ article.author?.badge || 'Kontributor Lokal' }}
                                </span>
                            </div>
                            <div class="mt-0.5 flex flex-wrap items-center gap-x-3 gap-y-1 text-xs text-slate-500">
                                <span class="flex items-center gap-1">
                                    <CalendarDays class="size-3.5 text-[#3E7BEF]" />
                                    {{ article.date || 'September 2026' }}
                                </span>
                                <span>·</span>
                                <span class="flex items-center gap-1">
                                    <Clock3 class="size-3.5 text-[#3E7BEF]" />
                                    {{ article.readTime || '4 menit baca' }}
                                </span>
                                <span>·</span>
                                <span class="flex items-center gap-1">
                                    <Eye class="size-3.5 text-[#3E7BEF]" />
                                    {{ article.views || '1.8k dibaca' }}
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- Article Interaction Controls -->
                    <div class="flex items-center gap-2 self-start sm:self-auto">
                        <!-- Like Button -->
                        <button
                            type="button"
                            class="inline-flex h-9 items-center gap-1.5 rounded-full border px-3.5 text-xs font-bold transition-all"
                            :class="isLiked ? 'border-rose-200 bg-rose-50 text-rose-600 shadow-sm' : 'border-[#dce8f5] bg-white text-slate-600 hover:border-rose-200 hover:bg-rose-50 hover:text-rose-600'"
                            :aria-label="isLiked ? 'Batal suka cerita ini' : 'Sukai cerita ini'"
                            @click="toggleLike"
                        >
                            <Heart class="size-4 transition-transform" :class="isLiked ? 'fill-rose-600 scale-110' : ''" />
                            <span>{{ likeCount }}</span>
                        </button>

                        <!-- Bookmark Button -->
                        <button
                            type="button"
                            class="inline-flex h-9 items-center gap-1.5 rounded-full border px-3.5 text-xs font-bold transition-all"
                            :class="isSaved ? 'border-[#1677e8] bg-[#edf7ff] text-[#1677e8]' : 'border-[#dce8f5] bg-white text-slate-600 hover:border-[#badeff] hover:bg-[#f4f9ff] hover:text-[#1677e8]'"
                            :aria-label="isSaved ? 'Hapus dari simpanan' : 'Simpan artikel'"
                            @click="toggleSave"
                        >
                            <Bookmark class="size-4" :class="isSaved ? 'fill-[#1677e8]' : ''" />
                            <span class="hidden sm:inline">{{ isSaved ? 'Tersimpan' : 'Simpan' }}</span>
                        </button>

                        <!-- Share Dropdown Button -->
                        <div class="relative">
                            <button
                                type="button"
                                class="grid size-9 place-items-center rounded-full border border-[#dce8f5] bg-white text-slate-600 transition-colors hover:border-[#3E7BEF] hover:bg-[#edf7ff] hover:text-[#3E7BEF]"
                                aria-label="Bagikan artikel"
                                @click="isShareOpen = !isShareOpen"
                            >
                                <Share2 class="size-4" />
                            </button>

                            <!-- Share Popover -->
                            <div
                                v-if="isShareOpen"
                                class="absolute right-0 top-full z-40 mt-2 w-48 rounded-2xl border border-[#dfe9f4] bg-white p-2 shadow-xl"
                            >
                                <p class="px-3 py-1.5 text-[11px] font-bold uppercase tracking-wider text-slate-400">Bagikan Cerita</p>
                                <button
                                    type="button"
                                    class="flex w-full items-center gap-2.5 rounded-xl px-3 py-2 text-left text-xs font-semibold text-slate-700 transition hover:bg-emerald-50 hover:text-emerald-700"
                                    @click="shareViaWhatsApp"
                                >
                                    <span class="grid size-6 place-items-center rounded-full bg-emerald-100 text-emerald-600">
                                        <MessageSquare class="size-3.5" />
                                    </span>
                                    WhatsApp
                                </button>
                                <button
                                    type="button"
                                    class="flex w-full items-center gap-2.5 rounded-xl px-3 py-2 text-left text-xs font-semibold text-slate-700 transition hover:bg-sky-50 hover:text-sky-700"
                                    @click="shareViaTwitter"
                                >
                                    <span class="grid size-6 place-items-center rounded-full bg-sky-100 text-sky-600">
                                        <Twitter class="size-3.5" />
                                    </span>
                                    Twitter / X
                                </button>
                                <button
                                    type="button"
                                    class="flex w-full items-center gap-2.5 rounded-xl px-3 py-2 text-left text-xs font-semibold text-slate-700 transition hover:bg-blue-50 hover:text-blue-700"
                                    @click="shareViaFacebook"
                                >
                                    <span class="grid size-6 place-items-center rounded-full bg-blue-100 text-blue-600">
                                        <Facebook class="size-3.5" />
                                    </span>
                                    Facebook
                                </button>
                                <div class="my-1 border-t border-slate-100"></div>
                                <button
                                    type="button"
                                    class="flex w-full items-center gap-2.5 rounded-xl px-3 py-2 text-left text-xs font-semibold text-slate-700 transition hover:bg-[#edf7ff] hover:text-[#1677e8]"
                                    @click="copyArticleLink"
                                >
                                    <span class="grid size-6 place-items-center rounded-full bg-slate-100 text-slate-600">
                                        <Check v-if="isCopied" class="size-3.5 text-emerald-600" />
                                        <Copy v-else class="size-3.5" />
                                    </span>
                                    {{ isCopied ? 'Tersalin!' : 'Salin Tautan' }}
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </header>

            <!-- FEATURED HERO IMAGE & GALLERY PREVIEW -->
            <section class="mt-6 overflow-hidden rounded-2xl border border-[#dce8f5] bg-slate-100 shadow-[0_8px_30px_rgba(23,75,120,0.06)] sm:rounded-3xl" aria-label="Foto utama artikel">
                <div class="group relative aspect-[16/9] w-full overflow-hidden bg-slate-900 sm:aspect-[21/9] lg:max-h-[500px]">
                    <img
                        :src="`https://images.unsplash.com/photo-${article.image}?auto=format&fit=crop&w=1600&q=90`"
                        :alt="article.title"
                        fetchpriority="high"
                        class="size-full object-cover transition-transform duration-700 group-hover:scale-105"
                    />
                    <div class="absolute inset-0 bg-gradient-to-t from-black/60 via-transparent to-transparent"></div>

                    <!-- Image Caption & Location Tag -->
                    <div class="absolute inset-x-0 bottom-0 flex flex-col justify-between gap-3 p-4 text-white sm:flex-row sm:items-end sm:p-6">
                        <p class="max-w-xl text-xs font-medium text-white/90 sm:text-sm">
                            {{ article.heroCaption || article.excerpt }}
                        </p>
                        <button
                            type="button"
                            class="inline-flex shrink-0 items-center gap-2 rounded-xl bg-white/90 px-4 py-2 text-xs font-bold text-[#173b70] backdrop-blur-md transition hover:bg-white hover:shadow-lg focus-visible:outline-2 focus-visible:outline-white"
                            @click="openGallery(0)"
                        >
                            <Sparkles class="size-4 text-[#1677e8]" />
                            Buka Galeri Foto ({{ allImages.length }})
                        </button>
                    </div>
                </div>
            </section>

            <!-- 2-COLUMN MAIN CONTENT & SIDEBAR LAYOUT -->
            <div class="mt-10 grid gap-8 lg:grid-cols-[minmax(0,1fr)_340px] lg:items-start xl:gap-12">
                <!-- LEFT COLUMN: ARTICLE BODY, TOC, SECTIONS, QUOTES, COMMENTS -->
                <article class="min-w-0">
                    <!-- Table of Contents (Daftar Isi) -->
                    <div v-if="article.sections && article.sections.length > 0" class="mb-8 overflow-hidden rounded-2xl border border-[#dce8f5] bg-white p-5 shadow-[0_4px_20px_rgba(23,75,120,0.03)] sm:p-6">
                        <div class="flex items-center justify-between">
                            <h2 class="flex items-center gap-2.5 text-base font-extrabold text-[#173b70]">
                                <span class="grid size-7 place-items-center rounded-lg bg-[#edf6ff] text-[#1677e8]">
                                    <ListTree class="size-4" />
                                </span>
                                Daftar Isi Cerita
                            </h2>
                            <button
                                type="button"
                                class="text-xs font-bold text-[#1677e8] hover:underline"
                                @click="isTocCollapsed = !isTocCollapsed"
                            >
                                {{ isTocCollapsed ? 'Buka' : 'Tutup' }}
                            </button>
                        </div>
                        <ol v-if="!isTocCollapsed" class="mt-4 space-y-2.5 border-t border-[#edf4fa] pt-4 text-xs font-semibold text-[#38517b]">
                            <li v-for="(sec, sIndex) in article.sections" :key="sec.id" class="flex items-center gap-2.5 transition hover:text-[#1677e8]">
                                <span class="grid size-5 shrink-0 place-items-center rounded-md bg-[#f0f6fc] text-[10px] font-bold text-[#1677e8]">0{{ sIndex + 1 }}</span>
                                <button
                                    type="button"
                                    class="text-left hover:underline"
                                    @click="scrollToSection(sec.id)"
                                >
                                    {{ sec.heading }}
                                </button>
                            </li>
                        </ol>
                    </div>

                    <!-- Lead Story Paragraph -->
                    <div
                        class="prose prose-slate max-w-none text-[#2d4567]"
                        :class="{
                            'text-base leading-relaxed sm:text-lg sm:leading-8': activeFontSize === 'normal',
                            'text-lg leading-relaxed sm:text-xl sm:leading-9': activeFontSize === 'medium',
                            'text-xl leading-relaxed sm:text-2xl sm:leading-10': activeFontSize === 'large',
                        }"
                    >
                        <p class="first-letter:float-left first-letter:mr-3 first-letter:text-5xl first-letter:font-extrabold first-letter:text-[#1677e8]">
                            {{ article.body }}
                        </p>
                    </div>

                    <!-- Structured Editorial Sections -->
                    <div v-if="article.sections && article.sections.length > 0" class="mt-8 space-y-12">
                        <section
                            v-for="(sec, idx) in article.sections"
                            :id="sec.id"
                            :key="sec.id"
                            class="scroll-mt-28"
                        >
                            <!-- Section Heading -->
                            <div class="flex items-start gap-3">
                                <span class="mt-1 flex size-7 shrink-0 items-center justify-center rounded-lg bg-[#eaf4ff] text-xs font-extrabold text-[#1677e8]">
                                    {{ idx + 1 }}
                                </span>
                                <h2 class="text-xl font-extrabold tracking-tight text-[#173b70] sm:text-2xl">
                                    {{ sec.heading }}
                                </h2>
                            </div>

                            <!-- Section Paragraphs -->
                            <div class="mt-4 space-y-4 text-slate-700" :class="activeFontSize === 'normal' ? 'text-base leading-8' : activeFontSize === 'medium' ? 'text-lg leading-9' : 'text-xl leading-10'">
                                <p v-for="(pText, pIdx) in sec.content" :key="pIdx">
                                    {{ pText }}
                                </p>
                            </div>

                            <!-- Section Highlight Quote -->
                            <div
                                v-if="sec.quote"
                                class="my-7 overflow-hidden rounded-2xl border-l-4 border-[#1677e8] bg-[#f0f7ff] p-5 shadow-sm sm:p-6"
                            >
                                <p class="text-base font-semibold italic leading-relaxed text-[#173b70] sm:text-lg">
                                    “{{ sec.quote.text }}”
                                </p>
                                <p v-if="sec.quote.author" class="mt-3 text-xs font-bold uppercase tracking-wider text-[#1677e8]">
                                    — {{ sec.quote.author }}
                                </p>
                            </div>

                            <!-- Section Image Insertion -->
                            <figure v-if="sec.image" class="my-7 overflow-hidden rounded-2xl border border-[#dce8f5] bg-slate-50">
                                <button type="button" class="group relative block w-full overflow-hidden" @click="openGallery(0)">
                                    <img
                                        :src="sec.image"
                                        :alt="sec.imageCaption || sec.heading"
                                        class="size-full max-h-[420px] object-cover transition duration-500 group-hover:scale-105"
                                    />
                                    <span class="absolute right-3 top-3 grid size-8 place-items-center rounded-full bg-black/60 text-white backdrop-blur-sm transition group-hover:bg-[#1677e8]">
                                        <Sparkles class="size-4" />
                                    </span>
                                </button>
                                <figcaption v-if="sec.imageCaption" class="p-3 text-center text-xs font-medium text-slate-500">
                                    {{ sec.imageCaption }}
                                </figcaption>
                            </figure>

                            <!-- Pro-Tips Callout Box -->
                            <div
                                v-if="sec.proTip"
                                class="my-7 rounded-2xl border border-[#cbe3fa] bg-gradient-to-br from-[#eff7ff] to-[#f6faff] p-5 shadow-sm sm:p-6"
                            >
                                <div class="flex items-center gap-2.5 text-sm font-extrabold text-[#1677e8]">
                                    <ShieldCheck class="size-5 text-[#1677e8]" />
                                    <span>{{ sec.proTip.title }}</span>
                                </div>
                                <ul class="mt-3 space-y-2.5">
                                    <li
                                        v-for="(tip, tIdx) in sec.proTip.items"
                                        :key="tIdx"
                                        class="flex items-start gap-2.5 text-xs font-medium leading-relaxed text-[#2a4d77] sm:text-sm"
                                    >
                                        <CheckCircle2 class="mt-0.5 size-4 shrink-0 text-[#1677e8]" />
                                        <span>{{ tip }}</span>
                                    </li>
                                </ul>
                            </div>

                            <!-- Destination Spots Cards -->
                            <div v-if="sec.spots && sec.spots.length > 0" class="my-7 space-y-3">
                                <h3 class="text-sm font-extrabold uppercase tracking-wider text-slate-500">Spot Kunjungan Rekomendasi</h3>
                                <div class="grid gap-3 sm:grid-cols-2">
                                    <div
                                        v-for="spot in sec.spots"
                                        :key="spot.name"
                                        class="rounded-2xl border border-[#e1ecf7] bg-white p-4 shadow-[0_4px_16px_rgba(23,75,120,0.03)]"
                                    >
                                        <div class="flex items-start justify-between gap-2">
                                            <h4 class="text-sm font-extrabold text-[#173b70]">{{ spot.name }}</h4>
                                            <span class="rounded-md bg-[#edf6ff] px-2 py-0.5 text-[10px] font-bold text-[#1677e8]">Rekomendasi</span>
                                        </div>
                                        <p class="mt-1 flex items-center gap-1.5 text-xs text-slate-500">
                                            <MapPin class="size-3.5 text-[#3E7BEF]" />
                                            {{ spot.location }}
                                        </p>
                                        <dl class="mt-3 grid grid-cols-2 gap-2 border-t border-[#f0f5fa] pt-2 text-[11px]">
                                            <div>
                                                <dt class="text-slate-400">Jam Buka</dt>
                                                <dd class="font-bold text-[#274c77]">{{ spot.hours }}</dd>
                                            </div>
                                            <div>
                                                <dt class="text-slate-400">Tiket Masuk</dt>
                                                <dd class="font-bold text-[#274c77]">{{ spot.ticket }}</dd>
                                            </div>
                                        </dl>
                                        <p v-if="spot.note" class="mt-2 text-[11px] leading-relaxed text-slate-500">
                                            💡 {{ spot.note }}
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </section>
                    </div>

                    <!-- TOPIC TAGS -->
                    <div class="mt-10 flex flex-wrap items-center gap-2 border-t border-[#e2edf7] pt-6">
                        <span class="flex items-center gap-1.5 text-xs font-bold text-slate-400">
                            <Tag class="size-3.5" />
                            Topik Terkait:
                        </span>
                        <span
                            v-for="tag in article.tags || ['Wisata Otentik', 'Tips Liburan', 'TapakLokal']"
                            :key="tag"
                            class="rounded-full border border-[#dce8f5] bg-white px-3 py-1 text-xs font-semibold text-[#274c77] transition hover:border-[#3E7BEF] hover:bg-[#edf7ff] hover:text-[#3E7BEF]"
                        >
                            #{{ tag }}
                        </span>
                    </div>

                    <!-- READER REACTION SECTION -->
                    <section class="mt-8 rounded-2xl border border-[#dce8f5] bg-gradient-to-b from-white to-[#fbfdff] p-6 text-center shadow-[0_8px_24px_rgba(23,75,120,0.04)]" aria-labelledby="reaction-heading">
                        <p class="text-[11px] font-bold uppercase tracking-[0.14em] text-[#1677e8]">TANGGAPAN PEMBACA</p>
                        <h3 id="reaction-heading" class="mt-1 text-lg font-extrabold text-[#173b70]">Apakah cerita perjalanan ini bermanfaat?</h3>
                        <p class="mt-1 text-xs text-slate-500">Bantu kami mengetahui artikel seperti apa yang paling kamu sukai.</p>
                        <div class="mt-5 flex flex-wrap justify-center gap-3">
                            <button
                                type="button"
                                class="inline-flex items-center gap-2 rounded-xl border px-4 py-2.5 text-xs font-bold transition-all duration-200 focus-visible:outline-2 focus-visible:outline-[#1677e8]"
                                :class="reactions.helpful.active ? 'border-[#1677e8] bg-[#edf7ff] text-[#1677e8] shadow-sm' : 'border-[#dce8f5] bg-white text-slate-700 hover:border-[#badeff] hover:bg-[#f8fbff]'"
                                @click="toggleReaction('helpful')"
                            >
                                <span class="text-base">❤️</span>
                                <span>Sangat Membantu</span>
                                <span class="rounded-full bg-slate-100 px-2 py-0.5 text-[10px] text-slate-600" :class="reactions.helpful.active ? 'bg-[#1677e8] text-white' : ''">
                                    {{ reactions.helpful.count }}
                                </span>
                            </button>

                            <button
                                type="button"
                                class="inline-flex items-center gap-2 rounded-xl border px-4 py-2.5 text-xs font-bold transition-all duration-200 focus-visible:outline-2 focus-visible:outline-[#1677e8]"
                                :class="reactions.inspiring.active ? 'border-[#1677e8] bg-[#edf7ff] text-[#1677e8] shadow-sm' : 'border-[#dce8f5] bg-white text-slate-700 hover:border-[#badeff] hover:bg-[#f8fbff]'"
                                @click="toggleReaction('inspiring')"
                            >
                                <span class="text-base">💡</span>
                                <span>Menginspirasi</span>
                                <span class="rounded-full bg-slate-100 px-2 py-0.5 text-[10px] text-slate-600" :class="reactions.inspiring.active ? 'bg-[#1677e8] text-white' : ''">
                                    {{ reactions.inspiring.count }}
                                </span>
                            </button>

                            <button
                                type="button"
                                class="inline-flex items-center gap-2 rounded-xl border px-4 py-2.5 text-xs font-bold transition-all duration-200 focus-visible:outline-2 focus-visible:outline-[#1677e8]"
                                :class="reactions.wantToGo.active ? 'border-[#1677e8] bg-[#edf7ff] text-[#1677e8] shadow-sm' : 'border-[#dce8f5] bg-white text-slate-700 hover:border-[#badeff] hover:bg-[#f8fbff]'"
                                @click="toggleReaction('wantToGo')"
                            >
                                <span class="text-base">📌</span>
                                <span>Ingin Kesana</span>
                                <span class="rounded-full bg-slate-100 px-2 py-0.5 text-[10px] text-slate-600" :class="reactions.wantToGo.active ? 'bg-[#1677e8] text-white' : ''">
                                    {{ reactions.wantToGo.count }}
                                </span>
                            </button>
                        </div>
                    </section>

                    <!-- AUTHOR BIO CARD -->
                    <section class="mt-8 rounded-2xl border border-[#dfeaf5] bg-white p-5 shadow-[0_6px_20px_rgba(23,75,120,0.04)] sm:p-6" aria-labelledby="author-bio-heading">
                        <div class="flex flex-col gap-4 sm:flex-row sm:items-center">
                            <img
                                :src="article.author?.avatar || 'https://images.unsplash.com/photo-1534528741775-53994a69daeb?auto=format&fit=crop&w=160&q=80'"
                                :alt="article.author?.name"
                                class="size-16 rounded-2xl border border-[#cfe2f6] object-cover shadow-sm sm:size-20"
                            />
                            <div class="min-w-0 flex-1">
                                <div class="flex flex-wrap items-center gap-2">
                                    <span class="text-[10px] font-bold uppercase tracking-[0.14em] text-[#1677e8]">TENTANG PENULIS</span>
                                    <span class="inline-flex items-center gap-1 rounded-md bg-emerald-50 px-2 py-0.5 text-[10px] font-bold text-emerald-700">
                                        <ShieldCheck class="size-3" /> Kontributor Resmi
                                    </span>
                                </div>
                                <h3 id="author-bio-heading" class="mt-1 text-base font-extrabold text-[#173b70] sm:text-lg">
                                    {{ article.author?.name }}
                                </h3>
                                <p class="text-xs font-semibold text-[#1677e8]">{{ article.author?.role }}</p>
                                <p class="mt-2 text-xs leading-5 text-slate-600 sm:text-sm">
                                    {{ article.author?.bio }}
                                </p>
                            </div>
                        </div>
                    </section>

                    <!-- CONNECTED TRIP BANNER (CTA TO ACTUAL TRIP) -->
                    <section v-if="article.relatedTrip" class="mt-8 overflow-hidden rounded-2xl border border-[#bcdcfe] bg-gradient-to-r from-[#eff6ff] to-[#f4faff] p-5 shadow-sm sm:p-6">
                        <div class="flex flex-col gap-5 sm:flex-row sm:items-center sm:justify-between">
                            <div class="flex items-center gap-4">
                                <img
                                    :src="article.relatedTrip.image"
                                    :alt="article.relatedTrip.title"
                                    class="size-20 shrink-0 rounded-xl object-cover shadow-sm"
                                />
                                <div>
                                    <span class="rounded-full bg-[#1677e8] px-2.5 py-0.5 text-[10px] font-bold uppercase text-white">
                                        {{ article.relatedTrip.badge || 'Trip Terkait' }}
                                    </span>
                                    <h4 class="mt-1 text-sm font-extrabold text-[#173b70] sm:text-base">
                                        {{ article.relatedTrip.title }}
                                    </h4>
                                    <p class="mt-0.5 text-xs text-slate-500">
                                        Durasi {{ article.relatedTrip.duration }} · Mulai <span class="font-extrabold text-[#1677e8]">{{ article.relatedTrip.price }}</span>
                                    </p>
                                </div>
                            </div>
                            <Link
                                :href="route('trips.show', { tripType: article.relatedTrip.type, trip: article.relatedTrip.slug })"
                                class="inline-flex min-h-11 shrink-0 items-center justify-center gap-2 rounded-xl bg-[#1677e8] px-5 text-xs font-bold text-white shadow-[0_6px_16px_rgba(22,119,232,0.22)] transition hover:bg-[#0875d0]"
                            >
                                Lihat Detail Trip <ArrowRight class="size-4" />
                            </Link>
                        </div>
                    </section>

                    <!-- COMMENTS & DISCUSSION SECTION -->
                    <section class="mt-10 border-t border-[#e2edf7] pt-8" aria-labelledby="comments-heading">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-[10px] font-bold uppercase tracking-[0.14em] text-[#1677e8]">DISKUSI TRAVELER</p>
                                <h3 id="comments-heading" class="mt-1 text-xl font-extrabold text-[#173b70]">
                                    Cerita & Tanggapan ({{ comments.length }})
                                </h3>
                            </div>
                            <span class="rounded-full bg-[#edf7ff] px-3 py-1 text-xs font-bold text-[#1677e8]">
                                Komunitas TapakLokal
                            </span>
                        </div>

                        <!-- Write Comment Box -->
                        <form class="mt-6 rounded-2xl border border-[#dce8f5] bg-white p-5 shadow-[0_4px_16px_rgba(23,75,120,0.03)]" @submit.prevent="submitComment">
                            <h4 class="text-xs font-extrabold uppercase tracking-wider text-slate-500">Bagikan Pengalaman atau Pertanyaanmu</h4>
                            <div class="mt-4 grid gap-3 sm:grid-cols-2">
                                <label class="block text-xs font-semibold text-slate-600">
                                    Nama Kamu
                                    <input
                                        v-model="newCommentName"
                                        type="text"
                                        required
                                        placeholder="Contoh: Sarah Aulia"
                                        class="mt-1 w-full rounded-xl border border-[#cfe2f6] bg-[#fbfdff] px-3.5 py-2.5 text-xs font-medium text-slate-800 outline-none focus:border-[#1677e8] focus:ring-2 focus:ring-[#1677e8]/15"
                                    />
                                </label>
                                <label class="block text-xs font-semibold text-slate-600">
                                    Status / Profil
                                    <input
                                        v-model="newCommentRole"
                                        type="text"
                                        placeholder="Contoh: Backpacker / Solo Traveler"
                                        class="mt-1 w-full rounded-xl border border-[#cfe2f6] bg-[#fbfdff] px-3.5 py-2.5 text-xs font-medium text-slate-800 outline-none focus:border-[#1677e8] focus:ring-2 focus:ring-[#1677e8]/15"
                                    />
                                </label>
                            </div>
                            <div class="mt-3">
                                <label class="block text-xs font-semibold text-slate-600">
                                    Pesan / Cerita Kamu
                                    <textarea
                                        v-model="newCommentText"
                                        rows="3"
                                        required
                                        placeholder="Tuliskan pengalaman atau tips tambahan yang berguna untuk sesama traveler..."
                                        class="mt-1 w-full rounded-xl border border-[#cfe2f6] bg-[#fbfdff] p-3 text-xs font-medium text-slate-800 outline-none focus:border-[#1677e8] focus:ring-2 focus:ring-[#1677e8]/15"
                                    ></textarea>
                                </label>
                            </div>
                            <div class="mt-3 flex flex-wrap items-center justify-between gap-3">
                                <div class="flex items-center gap-1">
                                    <span class="mr-2 text-xs font-semibold text-slate-500">Beri Rating:</span>
                                    <button
                                        v-for="star in 5"
                                        :key="star"
                                        type="button"
                                        class="p-0.5 text-amber-400"
                                        @click="newCommentRating = star"
                                    >
                                        <Star class="size-4" :class="star <= newCommentRating ? 'fill-amber-400' : 'text-slate-300'" />
                                    </button>
                                </div>
                                <button
                                    type="submit"
                                    :disabled="isSubmittingComment"
                                    class="inline-flex min-h-10 items-center justify-center gap-2 rounded-xl bg-[#1677e8] px-5 text-xs font-bold text-white shadow-sm transition hover:bg-[#0875d0] disabled:opacity-50"
                                >
                                    <Send class="size-3.5" />
                                    Kirim Komentar
                                </button>
                            </div>
                        </form>

                        <!-- Comments List -->
                        <div class="mt-6 space-y-4">
                            <article
                                v-for="c in comments"
                                :key="c.id"
                                class="rounded-2xl border border-[#e1ebf6] bg-white p-5 shadow-[0_3px_12px_rgba(23,75,120,0.02)]"
                            >
                                <div class="flex items-start justify-between gap-3">
                                    <div class="flex items-center gap-3">
                                        <img
                                            :src="c.avatar"
                                            :alt="`Avatar ${c.name}`"
                                            class="size-10 rounded-full object-cover"
                                        />
                                        <div>
                                            <div class="flex items-center gap-2">
                                                <h4 class="text-xs font-extrabold text-[#173b70]">{{ c.name }}</h4>
                                                <span class="rounded bg-slate-100 px-1.5 py-0.5 text-[9px] font-bold text-slate-500">
                                                    {{ c.role }}
                                                </span>
                                            </div>
                                            <div class="mt-0.5 flex items-center gap-2 text-[10px] text-slate-400">
                                                <span>{{ c.date }}</span>
                                                <div class="flex text-amber-400">
                                                    <Star v-for="s in (c.rating || 5)" :key="s" class="size-3 fill-amber-400" />
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <button
                                        type="button"
                                        class="inline-flex items-center gap-1 rounded-full border border-slate-200 px-2.5 py-1 text-[11px] font-semibold text-slate-500 hover:border-rose-200 hover:text-rose-600"
                                        @click="handleCommentLike(c)"
                                    >
                                        <Heart class="size-3" />
                                        <span>{{ c.likes || 0 }}</span>
                                    </button>
                                </div>
                                <p class="mt-3 text-xs leading-relaxed text-slate-700 sm:text-sm">
                                    {{ c.content }}
                                </p>
                            </article>
                        </div>
                    </section>
                </article>

                <!-- RIGHT COLUMN: STICKY SIDEBAR -->
                <aside class="space-y-6 lg:sticky lg:top-[120px]">
                    <!-- Quick Destination Facts Card -->
                    <section class="overflow-hidden rounded-2xl border border-[#dce8f5] bg-white p-5 shadow-[0_6px_22px_rgba(23,75,120,0.04)]" aria-labelledby="quick-facts-heading">
                        <div class="flex items-center gap-2 border-b border-[#edf4fa] pb-3">
                            <span class="grid size-7 place-items-center rounded-lg bg-[#edf6ff] text-[#1677e8]">
                                <MapPinned class="size-4" />
                            </span>
                            <h3 id="quick-facts-heading" class="text-sm font-extrabold text-[#173b70]">
                                Ringkasan Destinasi
                            </h3>
                        </div>
                        <dl class="mt-4 space-y-3 text-xs">
                            <div>
                                <dt class="font-semibold text-slate-400">Lokasi Utama</dt>
                                <dd class="mt-0.5 font-bold text-[#173b70]">{{ article.location || 'Indonesia' }}</dd>
                            </div>
                            <div>
                                <dt class="font-semibold text-slate-400">Waktu Kunjung Terbaik</dt>
                                <dd class="mt-0.5 font-bold text-[#173b70]">{{ article.quickFacts?.bestTime || 'Mei – Oktober' }}</dd>
                            </div>
                            <div>
                                <dt class="font-semibold text-slate-400">Estimasi Budget Harian</dt>
                                <dd class="mt-0.5 font-bold text-[#1677e8]">{{ article.quickFacts?.budget || 'Rp 300.000 – 600.000' }}</dd>
                            </div>
                            <div>
                                <dt class="font-semibold text-slate-400">Durasi Ideal</dt>
                                <dd class="mt-0.5 font-bold text-[#173b70]">{{ article.quickFacts?.duration || '3 – 4 Hari' }}</dd>
                            </div>
                            <div>
                                <dt class="font-semibold text-slate-400">Akses & Transportasi</dt>
                                <dd class="mt-0.5 font-bold text-[#173b70]">{{ article.quickFacts?.access || 'Kendaraan Sewa / Lokal' }}</dd>
                            </div>
                        </dl>
                    </section>

                    <!-- Related Trip Card (Booking Widget) -->
                    <section v-if="article.relatedTrip" class="overflow-hidden rounded-2xl border border-[#d6e8fb] bg-gradient-to-b from-[#f3f9ff] to-white p-5 shadow-[0_8px_24px_rgba(22,119,232,0.06)]">
                        <div class="flex items-center justify-between">
                            <span class="rounded-full bg-[#1677e8] px-2.5 py-0.5 text-[10px] font-bold text-white uppercase tracking-wider">
                                {{ article.relatedTrip.type === 'private-trip' ? 'Private Trip' : 'Open Trip' }}
                            </span>
                            <span class="flex items-center gap-1 text-xs font-bold text-amber-500">
                                <Star class="size-3.5 fill-current" />
                                {{ article.relatedTrip.rating }}
                            </span>
                        </div>
                        <div class="mt-3 overflow-hidden rounded-xl">
                            <img
                                :src="article.relatedTrip.image"
                                :alt="article.relatedTrip.title"
                                class="h-36 w-full object-cover"
                            />
                        </div>
                        <h4 class="mt-3 text-sm font-extrabold text-[#173b70] leading-snug">
                            {{ article.relatedTrip.title }}
                        </h4>
                        <p class="mt-1 text-[11px] text-slate-500">
                            Durasi: {{ article.relatedTrip.duration }} · {{ article.relatedTrip.reviewsCount }} ulasan
                        </p>
                        <div class="mt-4 flex items-baseline justify-between border-t border-[#e5eff8] pt-3">
                            <div>
                                <p class="text-[10px] text-slate-400 line-through">{{ article.relatedTrip.originalPrice }}</p>
                                <p class="text-base font-extrabold text-[#1677e8]">{{ article.relatedTrip.price }}</p>
                            </div>
                            <Link
                                :href="route('trips.show', { tripType: article.relatedTrip.type, trip: article.relatedTrip.slug })"
                                class="inline-flex min-h-9 items-center gap-1.5 rounded-xl bg-[#1677e8] px-3.5 text-xs font-bold text-white shadow-sm transition hover:bg-[#0875d0]"
                            >
                                Pesan Trip <ArrowRight class="size-3.5" />
                            </Link>
                        </div>
                    </section>

                    <!-- Popular Trending Articles -->
                    <section class="rounded-2xl border border-[#dce8f5] bg-white p-5 shadow-[0_4px_18px_rgba(23,75,120,0.03)]" aria-labelledby="trending-heading">
                        <div class="flex items-center justify-between border-b border-[#edf4fa] pb-3">
                            <h3 id="trending-heading" class="text-sm font-extrabold text-[#173b70]">
                                Cerita Populer Lainnya
                            </h3>
                            <Link :href="route('blog')" class="text-xs font-bold text-[#1677e8] hover:underline">Semua</Link>
                        </div>
                        <div class="mt-4 divide-y divide-[#f0f5fa]">
                            <Link
                                v-for="(pop, pIndex) in popularArticles"
                                :key="pop.id"
                                :href="route('blog.show', { article: pop.id })"
                                class="group flex items-start gap-3 py-3 first:pt-0 last:pb-0"
                            >
                                <span class="grid size-6 shrink-0 place-items-center rounded-lg bg-[#edf6ff] text-xs font-extrabold text-[#1677e8]">
                                    0{{ pIndex + 1 }}
                                </span>
                                <div class="min-w-0 flex-1">
                                    <span class="text-[10px] font-bold uppercase tracking-wider text-[#1677e8]">{{ pop.category }}</span>
                                    <h4 class="mt-0.5 line-clamp-2 text-xs font-bold text-[#173b70] transition-colors group-hover:text-[#1677e8]">
                                        {{ pop.title }}
                                    </h4>
                                    <p class="mt-1 flex items-center gap-2 text-[10px] text-slate-400">
                                        <span>{{ pop.date }}</span>
                                        <span>·</span>
                                        <span>{{ pop.readTime }}</span>
                                    </p>
                                </div>
                            </Link>
                        </div>
                    </section>

                    <!-- Explore Categories Widget -->
                    <section class="rounded-2xl border border-[#dce8f5] bg-white p-5 shadow-[0_4px_18px_rgba(23,75,120,0.03)]">
                        <h3 class="text-sm font-extrabold text-[#173b70]">Kategori Cerita</h3>
                        <div class="mt-3 flex flex-wrap gap-1.5">
                            <Link
                                v-for="cat in ['Inspirasi Destinasi', 'Tips Perjalanan', 'Cerita Lokal', 'Panduan Liburan', 'Wisata Alam']"
                                :key="cat"
                                :href="route('blog')"
                                class="rounded-full border border-[#e1eaf5] bg-[#f8fbff] px-3 py-1 text-xs font-semibold text-slate-700 transition hover:border-[#1677e8] hover:bg-[#edf6ff] hover:text-[#1677e8]"
                            >
                                {{ cat }}
                            </Link>
                        </div>
                    </section>
                </aside>
            </div>

            <!-- BOTTOM SECTION: RELATED ARTICLES 3-COLUMN GRID -->
            <section class="mt-16 border-t border-[#e2edf7] pt-12 sm:mt-20" aria-labelledby="related-heading">
                <div class="flex items-end justify-between gap-5">
                    <div>
                        <p class="text-xs font-bold tracking-[0.14em] text-[#1677e8]">BACA CERITA LAINNYA</p>
                        <h2 id="related-heading" class="mt-2 text-2xl font-extrabold leading-tight tracking-tight text-[#173b70] sm:text-3xl">
                            Inspirasi & panduan <span class="text-[#1677e8]">terkait</span>
                        </h2>
                    </div>
                    <Link :href="route('blog')" class="hidden items-center gap-1.5 text-xs font-bold text-[#1677e8] hover:underline sm:inline-flex">
                        Lihat semua cerita <ArrowRight class="size-3.5" />
                    </Link>
                </div>

                <div class="mt-7 grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
                    <article
                        v-for="rel in relatedArticles"
                        :key="rel.id"
                        class="group flex flex-col overflow-hidden rounded-2xl border border-[#e1eaf3] bg-white shadow-[0_3px_12px_rgba(23,75,120,0.04)] transition-all duration-200 hover:border-[#b8dafa] hover:shadow-[0_8px_20px_rgba(23,75,120,0.08)]"
                    >
                        <Link :href="route('blog.show', { article: rel.id })" class="flex h-full flex-col">
                            <div class="relative aspect-[16/10] w-full overflow-hidden bg-sky-100">
                                <img
                                    :src="`https://images.unsplash.com/photo-${rel.image}?auto=format&fit=crop&w=700&q=85`"
                                    :alt="rel.title"
                                    loading="lazy"
                                    class="size-full object-cover transition-transform duration-500 group-hover:scale-105"
                                />
                                <span class="absolute left-3 top-3 rounded-full bg-white/95 px-3 py-1 text-xs font-bold text-[#1677e8] shadow-sm">
                                    {{ rel.category }}
                                </span>
                            </div>
                            <div class="flex flex-1 flex-col p-5">
                                <div class="flex items-center justify-between text-xs text-slate-400">
                                    <span>{{ rel.date }}</span>
                                    <span class="flex items-center gap-1"><Clock3 class="size-3" />{{ rel.readTime }}</span>
                                </div>
                                <h3 class="mt-2.5 text-base font-bold leading-snug text-[#173b70] transition-colors group-hover:text-[#1677e8]">
                                    {{ rel.title }}
                                </h3>
                                <p class="mt-2 line-clamp-2 text-xs leading-5 text-slate-500">
                                    {{ rel.excerpt }}
                                </p>
                                <div class="mt-auto flex items-center justify-between pt-5">
                                    <span class="inline-flex items-center gap-1 text-xs font-bold text-[#1677e8]">
                                        Baca cerita <ArrowRight class="size-3.5 transition-transform group-hover:translate-x-1" />
                                    </span>
                                </div>
                            </div>
                        </Link>
                    </article>
                </div>
            </section>

            <!-- DESIGN SYSTEM BOTTOM SECTIONS -->
            <BlogDestinations />
            <BlogDestinationCta />
            <BlogNewsletter />
        </main>

        <!-- GLOBAL TOAST NOTIFICATION -->
        <Transition
            enter-active-class="transition duration-200"
            enter-from-class="translate-y-2 opacity-0"
            leave-active-class="transition duration-150"
            leave-to-class="translate-y-2 opacity-0"
        >
            <div
                v-if="toastMessage"
                class="fixed bottom-6 right-6 z-50 flex items-center gap-2.5 rounded-2xl border border-[#3E7BEF]/20 bg-white px-5 py-3.5 text-xs font-bold text-[#173b70] shadow-2xl"
            >
                <span class="grid size-5 place-items-center rounded-full bg-[#1677e8] text-white">
                    <Check class="size-3 stroke-[3]" />
                </span>
                <span>{{ toastMessage }}</span>
            </div>
        </Transition>

        <!-- FULLSCREEN GALLERY LIGHTBOX MODAL -->
        <Teleport to="body">
            <div
                v-if="isGalleryOpen"
                class="fixed inset-0 z-[110] flex items-center justify-center bg-[#071a32]/94 p-4 backdrop-blur-sm sm:p-8"
                role="dialog"
                aria-modal="true"
                aria-label="Galeri Foto Cerita Perjalanan"
                @click.self="closeGallery"
            >
                <div class="flex h-full w-full max-w-6xl flex-col">
                    <!-- Gallery Top Bar -->
                    <div class="flex items-center justify-between pb-4 text-white">
                        <div>
                            <p class="text-sm font-bold">{{ article.title }}</p>
                            <p class="text-xs text-white/60">{{ galleryIndex + 1 }} dari {{ allImages.length }} foto</p>
                        </div>
                        <button
                            type="button"
                            class="grid size-10 place-items-center rounded-full bg-white/10 text-white transition hover:bg-white/20"
                            aria-label="Tutup galeri"
                            @click="closeGallery"
                        >
                            <X class="size-5" />
                        </button>
                    </div>

                    <!-- Main Image Viewport with Nav Arrows -->
                    <div class="relative min-h-0 flex-1 overflow-hidden rounded-2xl bg-black">
                        <img
                            :src="allImages[galleryIndex]"
                            :alt="`Foto ${galleryIndex + 1}`"
                            class="size-full object-contain"
                        />
                        <button
                            type="button"
                            class="absolute left-3 top-1/2 grid size-11 -translate-y-1/2 place-items-center rounded-full bg-white/90 text-[#1677e8] shadow-lg transition hover:bg-white sm:left-5"
                            aria-label="Foto sebelumnya"
                            @click="prevImage"
                        >
                            <ChevronLeft class="size-6" />
                        </button>
                        <button
                            type="button"
                            class="absolute right-3 top-1/2 grid size-11 -translate-y-1/2 place-items-center rounded-full bg-white/90 text-[#1677e8] shadow-lg transition hover:bg-white sm:right-5"
                            aria-label="Foto berikutnya"
                            @click="nextImage"
                        >
                            <ChevronRight class="size-6" />
                        </button>
                    </div>

                    <!-- Gallery Thumbnail Strip -->
                    <div class="mt-4 flex gap-2.5 overflow-x-auto pb-1 [scrollbar-width:thin]">
                        <button
                            v-for="(img, idx) in allImages"
                            :key="idx"
                            type="button"
                            class="h-16 w-24 shrink-0 overflow-hidden rounded-xl border-2 transition"
                            :class="galleryIndex === idx ? 'border-[#3E7BEF] opacity-100' : 'border-transparent opacity-50 hover:opacity-100'"
                            @click="galleryIndex = idx"
                        >
                            <img :src="img" alt="" class="size-full object-cover" />
                        </button>
                    </div>
                </div>
            </div>
        </Teleport>
    </div>
</template>
