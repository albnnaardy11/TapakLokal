<script setup>
import { Head, Link } from '@inertiajs/vue3';
import { ref } from 'vue';
import { ArrowRight, ArrowUpRight, Clock3 } from 'lucide-vue-next';
import BlogDestinationCta from '../Components/Blog/BlogDestinationCta.vue';
import BlogDestinations from '../Components/Blog/BlogDestinations.vue';
import BlogLocalStories from '../Components/Blog/BlogLocalStories.vue';
import BlogNewsletter from '../Components/Blog/BlogNewsletter.vue';
import MainNavigation from '../Components/Shared/MainNavigation.vue';
import TravelBlog from '../Components/Home/TravelBlog.vue';
import { articles } from '../Components/Home/travelArticles';

const travelBlog = ref(null);
const featured = articles[0];
const supportingArticles = articles.slice(1, 3);
const articleImage = (article, width) => `https://images.unsplash.com/photo-${article.image}?auto=format&fit=crop&w=${width}&q=88`;
</script>

<template>
    <Head title="Cerita Perjalanan">
        <meta name="description" content="Inspirasi destinasi, tips perjalanan, dan cerita lokal bersama TapakLokal." />
    </Head>
    <div class="min-h-screen overflow-x-hidden bg-[#f8fafc] font-sans text-[#172c50]">
        <MainNavigation />
        <main class="mx-auto max-w-[1440px] px-5 pb-24 pt-8 sm:px-10 lg:px-12 lg:pt-12">
            <section aria-labelledby="blog-title" class="mx-auto max-w-[1180px]">
                <h1 id="blog-title" class="sr-only">Cerita Perjalanan</h1>
                <div class="grid gap-5 lg:grid-cols-[1.65fr_1fr]">
                    <article class="group relative isolate overflow-hidden rounded-[24px] bg-[#294541]">
                        <img :src="articleImage(featured, 1400)" alt="Pura Bali di tepi danau dengan pegunungan di kejauhan" fetchpriority="high" class="absolute inset-0 -z-20 size-full object-cover motion-safe:transition-transform motion-safe:duration-700 motion-safe:group-hover:scale-105" />
                        <div class="absolute inset-0 -z-10 bg-[linear-gradient(180deg,rgba(9,25,36,0.12)_10%,rgba(9,25,36,0.08)_30%,rgba(9,25,36,0.92)_100%)]" aria-hidden="true"></div>
                        <Link :href="route('blog.show', { article: featured.id })" class="flex h-full min-h-[440px] w-full flex-col items-start justify-between gap-20 p-6 text-left text-white focus-visible:outline-2 focus-visible:-outline-offset-4 focus-visible:outline-white sm:min-h-[510px] sm:p-9">
                            <span aria-hidden="true"></span>
                            <span class="flex w-full flex-col gap-4">
                                <span class="text-xs font-semibold text-white/80">Bali, Indonesia · Inspirasi Destinasi</span>
                                <span class="max-w-lg text-3xl font-bold leading-[1.15] tracking-tight sm:text-[40px]">{{ featured.title }}</span>
                                <span class="max-w-md text-sm leading-6 text-white/80">Berhenti sejenak. Temukan ritme yang lebih pelan di antara pura, danau, dan cerita setempat.</span>
                                <span class="mt-2 flex items-center justify-between border-t border-white/25 pt-5">
                                    <span class="flex items-center gap-2 text-xs text-white/80"><Clock3 class="size-3.5" aria-hidden="true" />5 menit baca</span>
                                    <span class="group/cta relative inline-flex h-10 w-10 items-center overflow-hidden rounded-full bg-white text-[#172c50] transition-[width,background-color,color] duration-300 hover:w-[132px] hover:bg-[#3E7BEF] hover:text-white">
                                        <ArrowUpRight class="absolute left-2.5 size-5 transition-all duration-200 group-hover/cta:-translate-y-2 group-hover/cta:translate-x-2 group-hover/cta:opacity-0" aria-hidden="true" />
                                        <span class="ml-4 whitespace-nowrap text-sm font-bold opacity-0 transition-opacity duration-150 group-hover/cta:opacity-100 group-hover/cta:delay-100">Baca cerita</span>
                                        <ArrowRight class="ml-2 size-4 shrink-0 -translate-x-2 opacity-0 transition-all duration-150 group-hover/cta:translate-x-0 group-hover/cta:opacity-100 group-hover/cta:delay-100" aria-hidden="true" />
                                    </span>
                                </span>
                            </span>
                        </Link>
                    </article>
                    <div class="grid gap-5 sm:grid-cols-2 lg:grid-cols-1">
                        <article v-for="(item, index) in supportingArticles" :key="item.id" class="group overflow-hidden rounded-2xl border border-[#e4edf7] bg-white shadow-[0_4px_16px_rgba(23,75,120,0.04)] transition-shadow hover:shadow-[0_8px_24px_rgba(23,75,120,0.1)]">
                            <Link :href="route('blog.show', { article: item.id })" class="flex h-full w-full flex-col text-left focus-visible:outline-2 focus-visible:-outline-offset-4 focus-visible:outline-[#3E7BEF] lg:flex-row">
                                <span class="relative block h-44 w-full shrink-0 overflow-hidden bg-sky-100 lg:h-auto lg:w-[43%]">
                                    <img :src="articleImage(item, 640)" :alt="index === 0 ? 'Pemandangan kepulauan dan laut tropis' : 'Lanskap hijau destinasi Indonesia'" class="size-full object-cover motion-safe:transition-transform motion-safe:duration-700 motion-safe:group-hover:scale-105 lg:absolute lg:inset-0" />
                                </span>
                                <span class="flex flex-1 flex-col gap-3 p-5 lg:py-7">
                                    <span class="text-xs font-bold uppercase tracking-[0.08em] text-[#3E7BEF]">{{ item.category }}</span>
                                    <span class="text-lg font-bold leading-snug tracking-tight transition-colors group-hover:text-[#3E7BEF]">{{ item.title }}</span>
                                    <span class="line-clamp-2 text-sm leading-6 text-slate-500">{{ item.excerpt }}</span>
                                    <span class="mt-auto flex items-center justify-between gap-3 pt-3"><span class="inline-flex items-center gap-1.5 text-xs text-slate-500"><Clock3 class="size-3" aria-hidden="true" />4 menit baca</span><span class="group/cta relative grid size-10 place-items-center overflow-hidden rounded-full bg-[#edf6ff] text-[#3E7BEF] transition-colors duration-300 hover:bg-[#3E7BEF] hover:text-white"><ArrowUpRight class="absolute size-5 transition-all duration-300 ease-out group-hover/cta:-translate-y-2 group-hover/cta:translate-x-2 group-hover/cta:opacity-0" aria-hidden="true" /><ArrowRight class="size-5 -translate-x-2 opacity-0 transition-all duration-300 ease-out group-hover/cta:translate-x-0 group-hover/cta:opacity-100" aria-hidden="true" /></span></span>
                                </span>
                            </Link>
                        </article>
                    </div>
                </div>
            </section>
            <div id="cerita" class="flow-root scroll-mt-28">
                <TravelBlog ref="travelBlog" />
            </div>
            <BlogDestinations />
            <BlogDestinationCta />
            <BlogLocalStories />
            <BlogNewsletter />
        </main>
    </div>
</template>
