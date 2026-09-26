<script setup>
import { route } from 'ziggy-js';
import { Link, usePage } from '@inertiajs/vue3';
import { ArrowLeft, ArrowRight, Clock3, X } from 'lucide-vue-next';
import { computed, onBeforeUnmount, onMounted, ref } from 'vue';
import BlogCardSkeleton from '../Skeletons/Cards/BlogCardSkeleton.vue';
import ProgressiveImage from '../Shared/ProgressiveImage.vue';
import EmptyState from '../Shared/EmptyState.vue';

const props = defineProps({
    items: { type: Array, default: () => [] },
    isLoading: {
        type: Boolean,
        default: false,
    },
});

const articles = computed(() => props.items);
const currentPage = usePage();
const isBlogPage = computed(() => currentPage.component === 'Blog');

const carousel = ref(null);
const dialog = ref(null);
const selectedArticle = ref(null);
const page = ref(0);
const positions = ref([0]);
let observer;

function formatPublishedDate(dateStr) {
    if (!dateStr) return 'Sep 2026';
    try {
        const d = new Date(dateStr);
        if (isNaN(d.getTime())) return dateStr;
        return d.toLocaleDateString('id-ID', { day: 'numeric', month: 'short', year: 'numeric' });
    } catch {
        return dateStr;
    }
}

const syncPage = () => {
    const left = carousel.value?.scrollLeft || 0;
    page.value = positions.value.reduce((closest, position, index) => Math.abs(position - left) < Math.abs(positions.value[closest] - left) ? index : closest, 0);
};

const measure = () => {
    const element = carousel.value;
    if (!element || isBlogPage.value || !element.firstElementChild) {
        return;
    }
    const maximum = Math.max(0, element.scrollWidth - element.clientWidth);
    const step = element.firstElementChild.getBoundingClientRect().width + 20;
    positions.value = Array.from({ length: Math.ceil(maximum / step) + 1 }, (_, index) => Math.min(index * step, maximum));
    syncPage();
};

const move = (index) => {
    carousel.value?.scrollTo({ left: positions.value[index], behavior: window.matchMedia('(prefers-reduced-motion: reduce)').matches ? 'instant' : 'smooth' });
};

const readArticle = (article) => {
    selectedArticle.value = article;
    dialog.value?.showModal();
};

defineExpose({ readArticle });

onMounted(() => {
    if (carousel.value) {
        observer = new ResizeObserver(measure);
        observer.observe(carousel.value);
    }
});

onBeforeUnmount(() => observer?.disconnect());
</script>

<template>
    <section
        class="mx-auto mt-16 max-w-[1180px] sm:mt-20"
        aria-labelledby="travel-blog-heading"
        :aria-busy="isLoading"
    >
        <!-- Section Header -->
        <div class="flex items-end justify-between gap-5">
            <div>
                <p class="text-xs font-bold tracking-[0.14em] text-[#0175ea]">REKOMENDASI CERITA</p>
                <h2 id="travel-blog-heading" class="mt-2 text-2xl font-extrabold leading-tight tracking-tight text-[#172c50] sm:text-3xl">
                    Temukan cerita untuk <span class="text-[#0175ea]">perjalananmu</span>
                </h2>
                <p class="mt-2.5 text-sm text-slate-500">
                    Inspirasi dan panduan praktis dari pemandu lokal sebelum kamu berangkat.
                </p>
            </div>

            <!-- Carousel Nav Buttons -->
            <div v-if="!isBlogPage" class="hidden shrink-0 items-center gap-3 sm:flex">
                <span class="text-xs font-medium text-slate-400">{{ articles.length }} cerita</span>
                <div class="flex gap-2">
                    <button
                        type="button"
                        class="grid size-10 place-items-center rounded-full border border-sky-100 bg-white text-[#0175ea] shadow-xs transition hover:bg-sky-50 disabled:opacity-30 disabled:pointer-events-none"
                        :disabled="page === 0"
                        aria-label="Artikel sebelumnya"
                        @click="move(page - 1)"
                    >
                        <ArrowLeft class="size-4" />
                    </button>
                    <button
                        type="button"
                        class="grid size-10 place-items-center rounded-full bg-[#0175ea] text-white shadow-xs transition hover:bg-[#005fb8] disabled:opacity-30 disabled:pointer-events-none"
                        :disabled="page === positions.length - 1"
                        aria-label="Artikel berikutnya"
                        @click="move(page + 1)"
                    >
                        <ArrowRight class="size-4" />
                    </button>
                </div>
            </div>
        </div>

        <!-- Section Skeleton State -->
        <div v-if="isLoading" class="mt-7">
            <BlogCardSkeleton :count="isBlogPage ? 6 : 4" :layout="isBlogPage ? 'grid' : 'carousel'" />
        </div>

        <!-- Empty State -->
        <div v-else-if="articles.length === 0" class="mt-7">
            <EmptyState
                title="Tidak Ada Artikel"
                message="Belum ada artikel yang tersedia saat ini."
            />
        </div>

        <!-- Real Articles Content (Traveloka Explore Editorial Cards) -->
        <div
            v-else
            ref="carousel"
            :class="isBlogPage ? 'mt-7 grid gap-6 sm:grid-cols-2 lg:grid-cols-3' : 'mt-7 flex snap-x snap-mandatory gap-5 overflow-x-auto pb-4 pt-1 [scrollbar-width:none] [&::-webkit-scrollbar]:hidden'"
            @scroll="syncPage"
        >
            <article
                v-for="article in articles"
                :key="article.id"
                class="group flex flex-col overflow-hidden rounded-2xl border border-slate-100 bg-white shadow-[0_4px_16px_rgba(20,50,90,0.05)] transition-all duration-300 hover:-translate-y-1 hover:border-[#9ecfff] hover:shadow-[0_12px_28px_rgba(7,140,255,0.12)]"
                :class="isBlogPage ? 'min-w-0' : 'w-[85%] shrink-0 snap-start sm:w-[calc((100%-20px)/2)] lg:w-[calc((100%-60px)/4)]'"
            >
                <Link
                    :href="route('blog.show', { article: article.slug })"
                    class="flex h-full flex-col text-left outline-none focus-visible:ring-2 focus-visible:ring-[#0175ea]"
                    :aria-label="`Baca ${article.title}`"
                >
                    <!-- Cinematic Image 16:10 with Location Badge -->
                    <div class="relative aspect-[16/10] w-full overflow-hidden bg-sky-100">
                        <ProgressiveImage
                            :src="article.image_url"
                            :alt="article.title"
                            aspectRatio="16/10"
                            rounded="rounded-none"
                            imageClass="size-full object-cover transition-transform duration-500 group-hover:scale-105"
                        />
                    </div>

                    <!-- Card Body: Clean & Consistent with Site Design System -->
                    <div class="flex flex-1 flex-col p-4 sm:p-4.5">
                        <!-- Date & Reading Time -->
                        <div class="flex items-center gap-2 text-[11px] font-medium text-slate-400">
                            <span>{{ formatPublishedDate(article.published_at) }}</span>
                            <span class="text-slate-300">•</span>
                            <span class="inline-flex items-center gap-1">
                                <Clock3 class="size-3 text-slate-400" />
                                {{ article.metadata?.readTime || '4 mnt baca' }}
                            </span>
                        </div>

                        <!-- Title: 2 lines clamp, crisp typography -->
                        <h3 class="mt-2 text-sm font-bold leading-snug text-[#172c50] transition-colors duration-200 group-hover:text-[#0175ea] line-clamp-2">
                            {{ article.title }}
                        </h3>
                    </div>
                </Link>
            </article>
        </div>

        <!-- Carousel Pagination Dots -->
        <nav v-if="!isBlogPage && positions.length > 1" class="mt-3 flex justify-center gap-1" aria-label="Halaman artikel">
            <button
                v-for="(position, index) in positions"
                :key="index"
                type="button"
                class="grid min-h-8 min-w-8 place-items-center rounded-full focus-visible:outline-2 focus-visible:outline-[#0175ea]"
                :aria-label="`Halaman artikel ${index + 1}`"
                :aria-current="page === index ? 'page' : undefined"
                @click="move(index)"
            >
                <span
                    class="h-2 rounded-full transition-all"
                    :class="page === index ? 'w-6 bg-[#0175ea]' : 'w-2 bg-slate-200 hover:bg-sky-300'"
                ></span>
            </button>
        </nav>

        <!-- Article Dialog Preview -->
        <dialog
            ref="dialog"
            class="fixed inset-0 m-auto max-h-[85dvh] w-[calc(100%-2rem)] max-w-2xl overflow-y-auto rounded-3xl bg-white p-0 text-[#172c50] shadow-2xl backdrop:bg-slate-950/50 backdrop:backdrop-blur-sm"
            aria-labelledby="blog-article-title"
            @click="$event.target === dialog && dialog.close()"
        >
            <div v-if="selectedArticle" class="p-6 sm:p-9">
                <div class="flex items-center justify-between gap-4">
                    <span class="text-xs font-bold text-[#0175ea]">{{ selectedArticle.category }}</span>
                    <button
                        type="button"
                        autofocus
                        class="grid size-9 place-items-center rounded-full bg-slate-100 hover:bg-slate-200 focus-visible:outline-2 focus-visible:outline-[#0175ea]"
                        aria-label="Tutup artikel"
                        @click="dialog.close()"
                    >
                        <X class="size-5" />
                    </button>
                </div>
                <h2 id="blog-article-title" class="mt-5 text-2xl font-extrabold leading-tight">
                    {{ selectedArticle.title }}
                </h2>
                <p class="mt-4 text-sm font-medium leading-6 text-slate-500">
                    {{ selectedArticle.excerpt }}
                </p>
                <p class="mt-5 text-base leading-8 text-slate-600">
                    {{ selectedArticle.body }}
                </p>
            </div>
        </dialog>
    </section>
</template>
