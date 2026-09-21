<script setup>
import { articles } from './travelArticles';
import { Link, usePage } from '@inertiajs/vue3';
import { ArrowLeft, ArrowRight, CalendarDays, Clock3, Compass, MapPinned, Utensils, X } from 'lucide-vue-next';
import { computed, nextTick, onBeforeUnmount, onMounted, ref, watch } from 'vue';
import BlogCardSkeleton from '../Skeletons/Cards/BlogCardSkeleton.vue';
import ProgressiveImage from '../Shared/ProgressiveImage.vue';
import EmptyState from '../Shared/EmptyState.vue';

defineProps({
    isLoading: {
        type: Boolean,
        default: false,
    },
});

const currentPage = usePage();
const isBlogPage = computed(() => currentPage.component === 'Blog');

const carousel = ref(null);
const dialog = ref(null);
const selectedArticle = ref(null);
const page = ref(0);
const positions = ref([0]);
const activeCategory = ref('Semua');
const categories = [
    { label: 'Semua', icon: Compass },
    { label: 'Inspirasi Destinasi', icon: MapPinned },
    { label: 'Tips Perjalanan', icon: Compass },
    { label: 'Cerita Lokal', icon: Utensils },
    { label: 'Panduan Liburan', icon: CalendarDays },
    { label: 'Wisata Alam', icon: Compass },
];
const articleDates = { bali: '12 Sep 2026', islands: '10 Sep 2026', local: '7 Sep 2026', packing: '4 Sep 2026', snorkeling: '2 Sep 2026' };
const filteredArticles = computed(() => activeCategory.value === 'Semua' ? articles : articles.filter((article) => article.category === activeCategory.value));
let observer;

const syncPage = () => {
    const left = carousel.value?.scrollLeft || 0;
    page.value = positions.value.reduce((closest, position, index) => Math.abs(position - left) < Math.abs(positions.value[closest] - left) ? index : closest, 0);
};
const measure = () => {
    const element = carousel.value;
    if (! element || isBlogPage.value || ! element.firstElementChild) {
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
    dialog.value.showModal();
};
defineExpose({ readArticle });

watch(activeCategory, async () => {
    await nextTick();
    measure();
});

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
        <div class="flex gap-2 overflow-x-auto pb-2 [scrollbar-width:none] [&::-webkit-scrollbar]:hidden" aria-label="Filter kategori cerita">
            <button v-for="category in categories" :key="category.label" type="button" class="inline-flex h-10 shrink-0 items-center gap-2 rounded-full border px-4 text-xs font-bold transition-colors focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-[#3E7BEF]" :aria-pressed="activeCategory === category.label" :class="activeCategory === category.label ? 'border-[#3E7BEF] bg-[#3E7BEF] text-white shadow-[0_5px_12px_rgba(7,140,255,0.2)]' : 'border-[#e1eaf3] bg-white text-[#486581] hover:border-[#b8dafa] hover:bg-[#f5faff]'" @click="activeCategory = category.label"><component :is="category.icon" class="size-4" />{{ category.label }}</button>
        </div>
        <div class="mt-8 flex items-end justify-between gap-5">
            <div>
                <p class="text-xs font-bold tracking-[0.14em] text-[#3E7BEF]">REKOMENDASI CERITA</p>
                <h2 id="travel-blog-heading" class="mt-2 text-2xl font-extrabold leading-tight tracking-tight text-[#172c50] sm:text-3xl">Temukan cerita untuk <span class="text-[#3E7BEF]">perjalananmu</span></h2>
                <p class="mt-3 text-sm text-slate-500">Panduan yang membantu sebelum memilih dan berangkat.</p>
                <Link v-if="currentPage.component !== 'Blog'" :href="route('blog')" class="mt-4 inline-flex items-center gap-2 text-sm font-bold text-[#3E7BEF] hover:underline">Lihat semua cerita <ArrowRight class="size-4" aria-hidden="true" /></Link>
            </div>
            <div v-if="!isBlogPage" class="hidden shrink-0 items-center gap-3 sm:flex"><span class="text-xs text-slate-500">{{ filteredArticles.length }} cerita</span><div class="flex gap-2"><button type="button" class="grid size-10 place-items-center rounded-full border border-sky-100 bg-white text-[#3E7BEF] transition hover:bg-sky-50 disabled:opacity-35 focus-visible:outline-2 focus-visible:outline-[#3E7BEF]" :disabled="page === 0" aria-label="Artikel sebelumnya" @click="move(page - 1)"><ArrowLeft class="size-4" /></button><button type="button" class="grid size-10 place-items-center rounded-full bg-[#3E7BEF] text-white transition hover:bg-[#2e69d9] disabled:opacity-35 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-[#3E7BEF]" :disabled="page === positions.length - 1" aria-label="Artikel berikutnya" @click="move(page + 1)"><ArrowRight class="size-4" /></button></div></div>
        </div>

        <!-- Section Skeleton State -->
        <div v-if="isLoading" class="mt-7">
            <BlogCardSkeleton :count="isBlogPage ? 6 : 4" :layout="isBlogPage ? 'grid' : 'carousel'" />
        </div>

        <!-- Empty State -->
        <div v-else-if="filteredArticles.length === 0" class="mt-7">
            <EmptyState
                title="Tidak Ada Artikel"
                :message="`Belum ada artikel yang tersedia untuk kategori ${activeCategory}.`"
                actionLabel="Tampilkan Semua Kategori"
                @action="activeCategory = 'Semua'"
            />
        </div>

        <!-- Real Articles Content -->
        <div v-else ref="carousel" :class="isBlogPage ? 'mt-7 grid gap-6 sm:grid-cols-2 lg:grid-cols-3' : 'mt-7 flex snap-x snap-mandatory gap-5 overflow-x-auto pb-5 pt-1 [scrollbar-width:none] [&::-webkit-scrollbar]:hidden'" @scroll="syncPage">
            <article v-for="article in filteredArticles" :key="article.id" class="group flex flex-col overflow-hidden rounded-2xl border border-[#e1eaf3] bg-white shadow-[0_3px_12px_rgba(23,75,120,0.04)] transition-[border-color,box-shadow] duration-200 hover:border-[#b8dafa] hover:shadow-[0_8px_20px_rgba(23,75,120,0.08)] motion-reduce:transition-none" :class="isBlogPage ? 'min-w-0' : 'w-[85%] shrink-0 snap-start sm:w-[calc((100%-20px)/2)] lg:w-[calc((100%-60px)/4)]'">
                <Link :href="route('blog.show', { article: article.id })" class="flex h-full flex-col text-left outline-none focus-visible:ring-2 focus-visible:ring-inset focus-visible:ring-[#3E7BEF]" :aria-label="`Baca ${article.title}`">
                    <div class="relative aspect-[4/3] w-full overflow-hidden bg-sky-100">
                        <ProgressiveImage
                            :src="`https://images.unsplash.com/photo-${article.image}?auto=format&fit=crop&w=640&q=85`"
                            :alt="article.category"
                            aspectRatio="4/3"
                            rounded="rounded-none"
                            imageClass="size-full object-cover transition-transform duration-500 group-hover:scale-105 motion-reduce:transition-none"
                        />
                        <span class="absolute left-3 top-3 rounded-full bg-white/95 px-3 py-1.5 text-xs font-bold text-[#2e69d9] shadow-sm z-10">{{ article.category }}</span>
                    </div>
                    <div class="flex flex-1 flex-col p-5">
                        <span class="flex items-center justify-between gap-3 text-xs text-slate-500"><span>{{ articleDates[article.id] }}</span><span class="inline-flex items-center gap-1.5"><Clock3 class="size-3.5" aria-hidden="true" />4 menit baca</span></span>
                        <h3 class="mt-3 text-base font-bold leading-snug text-[#172c50] transition-colors group-hover:text-[#3E7BEF]">{{ article.title }}</h3>
                        <p class="mt-2 text-sm leading-6 text-slate-500">{{ article.excerpt }}</p>
                        <span class="mt-auto flex justify-start pt-6"><span class="inline-flex items-center gap-2 text-xs font-bold text-[#3E7BEF] transition-colors duration-200 hover:text-[#2e69d9]">Baca cerita <ArrowRight class="size-3.5 transition-transform duration-200 group-hover:translate-x-1" aria-hidden="true" /></span></span>
                    </div>
                </Link>
            </article>
        </div>
        <nav v-if="!isBlogPage && positions.length > 1" class="mt-2 flex justify-center gap-1" aria-label="Halaman artikel">
            <button v-for="(position, index) in positions" :key="index" type="button" class="grid min-h-8 min-w-8 place-items-center rounded-full focus-visible:outline-2 focus-visible:outline-[#3E7BEF]" :aria-label="`Halaman artikel ${index + 1}`" :aria-current="page === index ? 'page' : undefined" @click="move(index)"><span class="h-2 rounded-full transition-all" :class="page === index ? 'w-6 bg-[#3E7BEF]' : 'w-2 bg-slate-200 hover:bg-sky-300'"></span></button>
        </nav>
        <dialog ref="dialog" class="fixed inset-0 m-auto max-h-[85dvh] w-[calc(100%-2rem)] max-w-2xl overflow-y-auto rounded-3xl bg-white p-0 text-[#172c50] shadow-2xl backdrop:bg-slate-950/50 backdrop:backdrop-blur-sm" aria-labelledby="blog-article-title" @click=" $event.target === dialog && dialog.close()">
            <div v-if="selectedArticle" class="p-6 sm:p-9">
                <div class="flex items-center justify-between gap-4"><span class="text-xs font-bold text-[#3E7BEF]">{{ selectedArticle.category }}</span><button type="button" autofocus class="grid size-9 place-items-center rounded-full bg-slate-100 hover:bg-slate-200 focus-visible:outline-2 focus-visible:outline-[#3E7BEF]" aria-label="Tutup artikel" @click="dialog.close()"><X class="size-5" /></button></div>
                <h2 id="blog-article-title" class="mt-5 text-2xl font-extrabold leading-tight">{{ selectedArticle.title }}</h2>
                <p class="mt-4 text-sm font-medium leading-6 text-slate-500">{{ selectedArticle.excerpt }}</p>
                <p class="mt-5 text-base leading-8 text-slate-600">{{ selectedArticle.body }}</p>
            </div>
        </dialog>
    </section>
</template>
