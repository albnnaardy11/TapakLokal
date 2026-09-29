<script setup>
import { Head, Link } from '@inertiajs/vue3';
import { route } from 'ziggy-js';
import { ArrowRight, Compass } from 'lucide-vue-next';
import MainNavigation from '../Components/Shared/MainNavigation.vue';
import TravelBlog from '../Components/Home/TravelBlog.vue';
import Pagination from '../Components/Admin/Pagination.vue';
import MainFooter from '../Components/Shared/MainFooter.vue';

defineProps({ publishedArticles: Object });
</script>

<template>
    <Head title="Cerita Perjalanan">
        <meta name="description" content="Inspirasi destinasi, tips perjalanan, dan cerita lokal bersama TapakLokal." />
    </Head>

    <div class="min-h-screen bg-[#f8fafc] text-[#172c50] font-sans">
        <MainNavigation />

        <main class="mx-auto max-w-[1200px] px-4 sm:px-6 lg:px-8 pb-20 sm:pb-28 pt-6 sm:pt-8 lg:pt-12">
            <!-- Hero Section (Identical Structure to Accessibility Guide) -->
            <section aria-labelledby="blog-title" class="relative overflow-hidden rounded-[28px] sm:rounded-[36px] lg:rounded-[40px] border border-[#dce8f8] bg-[#f8fbff] p-6 sm:p-9 md:p-11 lg:p-12 xl:p-14 shadow-[0_4px_24px_rgba(37,99,235,0.03)]">
                <div class="grid grid-cols-1 items-center gap-8 sm:gap-10 lg:grid-cols-[1.1fr_1fr] lg:gap-8 xl:gap-12">
                    <!-- Left Content -->
                    <div class="text-left">
                        <h1 id="blog-title" class="text-3xl font-extrabold tracking-tight text-[#0f172a] sm:text-4xl md:text-5xl lg:text-[52px] xl:text-[54px] leading-[1.12] sm:leading-[1.08]">
                            Setiap perjalanan,<br />
                            <span class="text-[#2563eb]">selalu punya cerita.</span>
                        </h1>
                        <p class="mt-4 sm:mt-6 max-w-xl text-sm leading-relaxed text-[#475569] sm:text-base lg:text-[17px]">
                            Temukan inspirasi destinasi nusantara, tips perjalanan praktis, rekomendasi kuliner autentik, dan kisah penjelajah lokal bersama TapakLokal.
                        </p>

                        <!-- Quick navigation & action buttons -->
                        <div class="mt-6 sm:mt-8 flex flex-col sm:flex-row items-stretch sm:items-center gap-3 sm:gap-4">
                            <a
                                href="#cerita-perjalanan"
                                class="inline-flex min-h-[48px] items-center justify-center gap-2 rounded-full bg-[#2563eb] px-7 py-3.5 text-sm font-bold text-white shadow-[0_8px_20px_rgba(37,99,235,0.25)] transition-all duration-200 hover:bg-[#1d4ed8] hover:shadow-[0_12px_24px_rgba(37,99,235,0.35)] hover:-translate-y-0.5 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-[#2563eb] sm:px-8 sm:py-4 sm:text-base text-center"
                            >
                                <span>Jelajahi Cerita</span>
                                <ArrowRight class="size-4 shrink-0 stroke-[2.5]" aria-hidden="true" />
                            </a>
                            <Link
                                :href="route('catalog')"
                                class="inline-flex min-h-[48px] items-center justify-center gap-2 rounded-full border border-[#e2e8f0] bg-white px-6 py-3.5 text-sm font-bold text-[#1e293b] shadow-xs transition-all duration-200 hover:bg-slate-50 hover:border-[#cbd5e1] hover:shadow-sm hover:-translate-y-0.5 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-[#2563eb] sm:px-7 sm:py-4 sm:text-base text-center cursor-pointer"
                            >
                                <Compass class="size-4 shrink-0 text-[#2563eb] stroke-[2.2]" aria-hidden="true" />
                                <span>Cari Trip Terkait</span>
                            </Link>
                        </div>
                    </div>

                    <!-- Right Graphic -->
                    <div class="flex items-center justify-center lg:justify-end">
                        <img
                            src="/Assets/Images/blog/hero.png"
                            alt="Ilustrasi Cerita Perjalanan dan Destinasi TapakLokal"
                            class="w-full max-w-[360px] sm:max-w-[460px] md:max-w-[520px] lg:max-w-[580px] xl:max-w-[620px] h-auto object-contain select-none drop-shadow-xs"
                        />
                    </div>
                </div>
            </section>

            
        </main>

        <MainFooter />
    </div>
</template>
