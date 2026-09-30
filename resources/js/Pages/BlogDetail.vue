<script setup>
import { Head, Link } from '@inertiajs/vue3';
import { computed, onBeforeUnmount, onMounted, ref, watch } from 'vue';
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
import { articles, getArticleById } from '../Components/Home/travelArticles';

const props = defineProps({
    articleId: {
        type: String,
        default: 'bali',
    },
    content: {
        type: Object,
        default: null,
    },
});

// Article Resolution
const article = computed(() => {
    const slug = props.articleId || props.content?.slug || 'bali';
    const base = getArticleById(slug);

    if (props.content) {
        let meta = {};
        if (typeof props.content.metadata === 'object' && props.content.metadata !== null) {
            meta = props.content.metadata;
        } else if (typeof props.content.metadata === 'string') {
            try {
                meta = JSON.parse(props.content.metadata);
            } catch {
                meta = {};
            }
        }

        return {
            ...base,
            ...meta,
            id: props.content.slug || base.id,
            title: props.content.title || base.title,
            excerpt: props.content.excerpt || base.excerpt,
            category: props.content.category || base.category || 'Cerita Perjalanan',
            body: props.content.body || base.body,
            image: meta.image || base.image,
            image_url: props.content.image_url || meta.image_url || base.image_url,
        };
    }

    return base;
});

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
const isShareOpen = ref(false);
const isCopied = ref(false);
const toastMessage = ref('');
const isTocCollapsed = ref(false);
const readingProgress = ref(0);

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
    if (article.value?.gallery && article.value.gallery.length > 0) {
        return article.value.gallery;
    }

    const mainImg = article.value?.image_url
        || (article.value?.image?.startsWith?.('http')
            ? article.value.image
            : `https://images.unsplash.com/photo-${article.value?.image || '1537996194471-e657df975ab4'}?auto=format&fit=crop&w=1200&q=88`);

    return [mainImg];
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

const updateReadingProgress = () => {
    const scrollTop = window.scrollY;
    const docHeight = document.documentElement.scrollHeight - window.innerHeight;
    readingProgress.value = docHeight > 0 ? Math.min(100, (scrollTop / docHeight) * 100) : 0;
};

onMounted(() => {
    window.addEventListener('keydown', handleKeyDown);
    window.addEventListener('scroll', updateReadingProgress, { passive: true });
});

onBeforeUnmount(() => {
    window.removeEventListener('keydown', handleKeyDown);
    window.removeEventListener('scroll', updateReadingProgress);
    document.body.style.overflow = '';
});
</script>

<template>
    <Head :title="article.title">
        <meta name="description" :content="article.excerpt" />
    </Head>

    <div class="min-h-screen overflow-x-hidden bg-[#f8fafc] font-sans text-[#172c50]">
        <!-- Reading Progress Bar -->
        <div
            class="fixed left-0 top-0 z-[200] h-[3px] bg-gradient-to-r from-[#1677e8] to-[#38bdf8] transition-all duration-100 ease-out"
            :style="{ width: readingProgress + '%' }"
        ></div>

        <!-- Global Navigation -->
        <MainNavigation />

        <!-- ══ MAIN CONTENT ══ -->
        <main class="mx-auto max-w-[1220px] px-4 pb-16 pt-5 sm:px-6 sm:pt-6 lg:px-8">
            <!-- Breadcrumbs -->
            <nav class="mb-4 flex flex-wrap items-center gap-2 text-xs font-medium text-slate-500" aria-label="Breadcrumb">
                <Link href="/" class="transition-colors hover:text-[#1677e8]">Beranda</Link>
                <ChevronRight class="size-3 text-slate-400" />
                <Link :href="route('blog')" class="transition-colors hover:text-[#1677e8]">Cerita Perjalanan</Link>
                <ChevronRight class="size-3 text-slate-400" />
                <span class="font-semibold text-[#1677e8]">{{ article.category }}</span>
            </nav>

            <!-- ══ PROPORTIONAL HERO CARD (Text inside photo) ══ -->
            <section class="group relative overflow-hidden rounded-2xl sm:rounded-3xl border border-slate-200/90 bg-slate-900 shadow-md" aria-label="Hero artikel">
                <!-- Background Image & Gradient -->
                <div class="relative min-h-[320px] sm:min-h-[380px] md:h-[420px] w-full flex flex-col justify-end p-5 sm:p-7 md:p-8">
                    <img
                        :src="article.image_url || (article.image?.startsWith?.('http') ? article.image : `https://images.unsplash.com/photo-${article.image}?auto=format&fit=crop&w=1600&q=88`)"
                        :alt="article.title"
                        fetchpriority="high"
                        class="absolute inset-0 size-full object-cover transition-transform duration-700 ease-out group-hover:scale-105"
                    />

                    <!-- Soft cinematic gradient overlays for contrast -->
                    <div class="absolute inset-0 bg-gradient-to-t from-black/90 via-black/45 to-transparent"></div>
                    <div class="absolute inset-0 bg-gradient-to-r from-black/60 via-transparent to-transparent"></div>

                    <!-- Hero Content Inside Photo -->
                    <div class="relative z-10 max-w-3xl">
                        <!-- Category & Location Pills -->
                        <div class="flex flex-wrap items-center gap-2">
                            <span class="inline-flex items-center gap-1.5 rounded-full bg-[#1677e8] px-3 py-0.5 text-[11px] font-bold text-white shadow-md">
                                <Compass class="size-3" />
                                {{ article.category }}
                            </span>
                            <span v-if="article.location" class="inline-flex items-center gap-1.5 rounded-full border border-white/25 bg-black/35 px-3 py-0.5 text-[11px] font-medium text-white backdrop-blur-md">
                                <MapPin class="size-3 text-[#60b4ff]" />
                                {{ article.location }}
                            </span>
                        </div>

                        <!-- Proportional Title -->
                        <h1 class="mt-3 text-2xl font-black leading-snug tracking-tight text-white drop-shadow-md sm:text-3xl md:text-[34px]">
                            {{ article.title }}
                        </h1>

                        <!-- Excerpt (Compact, 1-2 lines) -->
                        <p v-if="article.excerpt" class="mt-2 line-clamp-2 text-xs sm:text-sm leading-relaxed text-white/85 max-w-2xl font-normal drop-shadow">
                            {{ article.excerpt }}
                        </p>

                        <!-- Author, Meta & Action Controls Bar inside Photo -->
                        <div class="mt-4 flex flex-wrap items-center justify-between gap-3 border-t border-white/20 pt-3.5">
                            <!-- Left: Author & Meta -->
                            <div class="flex flex-wrap items-center gap-3 sm:gap-4">
                                <div class="flex items-center gap-2.5">
                                    <div class="relative shrink-0">
                                        <img
                                            :src="article.author?.avatar || 'https://images.unsplash.com/photo-1534528741775-53994a69daeb?auto=format&fit=crop&w=120&q=80'"
                                            :alt="article.author?.name"
                                            class="size-8 sm:size-9 rounded-full border border-white/50 object-cover shadow"
                                        />
                                        <span class="absolute -bottom-0.5 -right-0.5 grid size-3.5 place-items-center rounded-full bg-[#1677e8] ring-1.5 ring-black">
                                            <Check class="size-2 stroke-[3] text-white" />
                                        </span>
                                    </div>
                                    <div>
                                        <p class="text-xs sm:text-sm font-bold text-white leading-none">{{ article.author?.name || 'Kontributor TapakLokal' }}</p>
                                        <p class="mt-0.5 text-[10px] text-white/60 leading-none">{{ article.author?.role || 'Traveler' }}</p>
                                    </div>
                                </div>

                                <div class="hidden sm:block h-3.5 w-px bg-white/20"></div>

                                <div class="flex flex-wrap items-center gap-x-3 gap-y-1 text-[11px] text-white/80">
                                    <span class="flex items-center gap-1"><CalendarDays class="size-3 text-[#60b4ff]" />{{ article.date || 'September 2026' }}</span>
                                    <span class="flex items-center gap-1"><Clock3 class="size-3 text-[#60b4ff]" />{{ article.readTime || '5 menit baca' }}</span>
                                    <span class="flex items-center gap-1"><Eye class="size-3 text-[#60b4ff]" />{{ article.views || '2.4k dibaca' }}</span>
                                </div>
                            </div>

                            <!-- Right: Interaction Buttons (Like, Bookmark, Share) -->
                            <div class="flex items-center gap-2">
                                <button
                                    type="button"
                                    class="inline-flex h-8 items-center gap-1.5 rounded-full border border-white/25 bg-black/35 px-3 text-xs font-bold text-white backdrop-blur-md transition-all hover:bg-rose-500/30 hover:border-rose-400/50"
                                    :class="{ '!border-rose-400 !bg-rose-600/50': isLiked }"
                                    :aria-label="isLiked ? 'Batal suka' : 'Sukai cerita ini'"
                                    @click="toggleLike"
                                >
                                    <Heart class="size-3.5 transition-all" :class="isLiked ? 'fill-rose-300 text-rose-200' : ''" />
                                    <span>{{ likeCount }}</span>
                                </button>
                                <button
                                    type="button"
                                    class="inline-flex h-8 items-center gap-1.5 rounded-full border border-white/25 bg-black/35 px-3 text-xs font-bold text-white backdrop-blur-md transition-all hover:bg-sky-500/30 hover:border-sky-400/50"
                                    :class="{ '!border-sky-400 !bg-sky-600/50': isSaved }"
                                    :aria-label="isSaved ? 'Hapus dari simpanan' : 'Simpan artikel'"
                                    @click="toggleSave"
                                >
                                    <Bookmark class="size-3.5" :class="isSaved ? 'fill-sky-200' : ''" />
                                    <span class="hidden sm:inline">{{ isSaved ? 'Tersimpan' : 'Simpan' }}</span>
                                </button>
                                <div class="relative">
                                    <button
                                        type="button"
                                        class="grid size-8 place-items-center rounded-full border border-white/25 bg-black/35 text-white backdrop-blur-md transition hover:bg-white/20"
                                        aria-label="Bagikan artikel"
                                        @click="isShareOpen = !isShareOpen"
                                    >
                                        <Share2 class="size-3.5" />
                                    </button>
                                    <div v-if="isShareOpen" class="absolute right-0 bottom-full z-50 mb-2 w-52 rounded-2xl border border-slate-100 bg-white p-2 shadow-2xl">
                                        <p class="px-3 py-1.5 text-[10px] font-bold uppercase tracking-wider text-slate-400">Bagikan Cerita</p>
                                        <button type="button" class="flex w-full items-center gap-2.5 rounded-xl px-3 py-2 text-left text-xs font-semibold text-slate-700 transition hover:bg-emerald-50 hover:text-emerald-700" @click="shareViaWhatsApp">
                                            <span class="grid size-6 place-items-center rounded-full bg-emerald-100 text-emerald-600"><MessageSquare class="size-3.5" /></span>
                                            WhatsApp
                                        </button>
                                        <button type="button" class="flex w-full items-center gap-2.5 rounded-xl px-3 py-2 text-left text-xs font-semibold text-slate-700 transition hover:bg-sky-50 hover:text-sky-700" @click="shareViaTwitter">
                                            <span class="grid size-6 place-items-center rounded-full bg-sky-100 text-sky-600"><Twitter class="size-3.5" /></span>
                                            Twitter / X
                                        </button>
                                        <button type="button" class="flex w-full items-center gap-2.5 rounded-xl px-3 py-2 text-left text-xs font-semibold text-slate-700 transition hover:bg-blue-50 hover:text-blue-700" @click="shareViaFacebook">
                                            <span class="grid size-6 place-items-center rounded-full bg-blue-100 text-blue-600"><Facebook class="size-3.5" /></span>
                                            Facebook
                                        </button>
                                        <div class="my-1 border-t border-slate-100"></div>
                                        <button type="button" class="flex w-full items-center gap-2.5 rounded-xl px-3 py-2 text-left text-xs font-semibold text-slate-700 transition hover:bg-[#edf7ff] hover:text-[#1677e8]" @click="copyArticleLink">
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
                    <section class="overflow-hidden rounded-2xl border border-[#dce8f5] bg-white shadow-sm" aria-labelledby="trending-heading">
                        <div class="flex items-center justify-between border-b border-[#edf4fa] px-5 py-3.5">
                            <h3 id="trending-heading" class="text-sm font-extrabold text-[#173b70]">Artikel Terpopuler</h3>
                            <Link :href="route('blog')" class="text-xs font-bold text-[#1677e8] hover:underline">Semua</Link>
                        </div>
                        <div class="divide-y divide-[#f0f5fa] px-4">
                            <Link
                                v-for="(pop, pIndex) in popularArticles"
                                :key="pop.id"
                                :href="route('blog.show', { article: pop.id })"
                                class="group flex items-start gap-3 py-3.5"
                            >
                                <div class="relative shrink-0">
                                    <img
                                        :src="`https://images.unsplash.com/photo-${pop.image}?auto=format&fit=crop&w=120&q=80`"
                                        :alt="pop.title"
                                        class="size-14 rounded-xl object-cover"
                                    />
                                    <span class="absolute -left-1.5 -top-1.5 grid size-5 place-items-center rounded-full bg-[#1677e8] text-[9px] font-black text-white">{{ pIndex + 1 }}</span>
                                </div>
                                <div class="min-w-0 flex-1">
                                    <span class="text-[10px] font-bold uppercase tracking-wider text-[#1677e8]">{{ pop.category }}</span>
                                    <h4 class="mt-0.5 line-clamp-2 text-xs font-bold leading-snug text-[#173b70] transition-colors group-hover:text-[#1677e8]">
                                        {{ pop.title }}
                                    </h4>
                                    <p class="mt-1 flex items-center gap-1.5 text-[10px] text-slate-400">
                                        <Clock3 class="size-3" />{{ pop.readTime }}
                                    </p>
                                </div>
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
                        class="group flex flex-col overflow-hidden rounded-2xl border border-[#e1eaf3] bg-white shadow-sm transition-all duration-300 hover:-translate-y-1 hover:border-[#b8dafa] hover:shadow-[0_12px_28px_rgba(23,75,120,0.1)]"
                    >
                        <Link :href="route('blog.show', { article: rel.id })" class="flex h-full flex-col">
                            <div class="relative aspect-[16/10] w-full overflow-hidden bg-sky-100">
                                <img
                                    :src="`https://images.unsplash.com/photo-${rel.image}?auto=format&fit=crop&w=700&q=85`"
                                    :alt="rel.title"
                                    loading="lazy"
                                    class="size-full object-cover transition-transform duration-500 group-hover:scale-105"
                                />
                                <div class="absolute inset-0 bg-gradient-to-t from-black/20 to-transparent opacity-0 transition-opacity group-hover:opacity-100"></div>
                                <span class="absolute left-3 top-3 rounded-full bg-white/95 px-3 py-1 text-[11px] font-bold text-[#1677e8] shadow-sm">
                                    {{ rel.category }}
                                </span>
                            </div>
                            <div class="flex flex-1 flex-col p-5">
                                <div class="flex items-center justify-between text-xs text-slate-400">
                                    <span class="flex items-center gap-1"><CalendarDays class="size-3" />{{ rel.date }}</span>
                                    <span class="flex items-center gap-1"><Clock3 class="size-3" />{{ rel.readTime }}</span>
                                </div>
                                <h3 class="mt-2.5 text-base font-bold leading-snug text-[#173b70] transition-colors group-hover:text-[#1677e8]">
                                    {{ rel.title }}
                                </h3>
                                <p class="mt-2 line-clamp-2 text-xs leading-5 text-slate-500">
                                    {{ rel.excerpt }}
                                </p>
                                <div class="mt-auto flex items-center justify-between border-t border-slate-100 pt-4">
                                    <span class="inline-flex items-center gap-1 text-xs font-bold text-[#1677e8]">
                                        Baca cerita <ArrowRight class="size-3.5 transition-transform group-hover:translate-x-1" />
                                    </span>
                                    <span class="flex items-center gap-1 text-[10px] text-slate-400"><Eye class="size-3" />{{ rel.views }}</span>
                                </div>
                            </div>
                        </Link>
                    </article>
                </div>
            </section>


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
