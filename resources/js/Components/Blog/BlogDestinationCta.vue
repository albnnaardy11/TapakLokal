<script setup>
import { ArrowRight } from 'lucide-vue-next';
</script>

<template>
    <section class="mx-auto mt-16 max-w-[1180px] sm:mt-20" aria-labelledby="article-destination-heading">
        <div class="relative isolate min-h-[244px] overflow-hidden rounded-2xl border border-[#d8eafb] bg-[#eaf6ff] px-6 py-8 sm:px-9 sm:py-9 lg:grid lg:h-[244px] lg:min-h-0 lg:grid-cols-[400px_minmax(0,1fr)] lg:items-center lg:gap-10 lg:px-12 lg:py-0">
            <div class="relative hidden h-[244px] lg:block" aria-hidden="true">
                <img src="/Assets/Images/blog/cta-blog.webp" alt="" class="absolute -left-6 top-1 h-[234px] w-auto max-w-none object-contain" />
            </div>
            <div class="relative max-w-lg lg:justify-self-start"><h2 id="article-destination-heading" class="text-2xl font-extrabold leading-tight tracking-tight text-[#172c50] sm:text-3xl">Dari Artikel, Langsung ke Destinasi</h2><p class="mt-3 max-w-lg text-sm leading-6 text-slate-600">Temukan destinasi yang pernah kami bahas di blog, lalu rencanakan perjalananmu bersama TapakLokal.</p><a href="/" class="mt-4 inline-flex min-h-12 items-center gap-2 rounded-xl bg-[#3E7BEF] px-5 text-sm font-bold text-white shadow-[0_6px_16px_rgba(62,123,239,0.22)] transition-colors hover:bg-[#2e69d9]">Jelajahi Destinasi <ArrowRight class="size-4" /></a></div>
        </div>
    </section>
</template>
