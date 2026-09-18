<script setup>
import { ArrowRight, CalendarDays, Clock3 } from 'lucide-vue-next';

const stories = [
    { title: 'Kain tenun yang menjaga cerita Flores', category: 'UMKM Lokal', date: '13 Sep 2026', image: '1518548419970-58e3b4079ab2' },
    { title: 'Warung pesisir dan rasa yang tak tergantikan', category: 'Kuliner Lokal', date: '10 Sep 2026', image: '1507525428034-b723cf961d3e' },
    { title: 'Pemandu lokal yang merawat jalur pendakian', category: 'Cerita Komunitas', date: '7 Sep 2026', image: '1464822759023-fed622ff2c3b' },
];

const imageUrl = (story) => `https://images.unsplash.com/photo-${story.image}?auto=format&fit=crop&w=640&q=84`;
</script>

<template>
    <section class="mx-auto mt-20 max-w-[1180px] sm:mt-24" aria-labelledby="local-stories-heading">
        <div class="grid gap-7 lg:grid-cols-[0.84fr_2.16fr] lg:items-end lg:gap-10">
            <div><p class="text-[10px] font-bold tracking-[0.14em] text-[#3E7BEF]">CERITA DARI LOKAL</p><h2 id="local-stories-heading" class="mt-2 text-2xl font-extrabold leading-tight tracking-tight text-[#172c50] sm:text-3xl">Dari masyarakat, untuk <span class="text-[#3E7BEF]">Indonesia.</span></h2><p class="mt-3 max-w-sm text-sm leading-6 text-slate-500">Kenali lebih dekat pelaku lokal, UMKM, dan komunitas yang menjaga keunikan setiap destinasi.</p><a href="#cerita" class="mt-5 inline-flex min-h-10 items-center gap-2 rounded-lg bg-[#3E7BEF] px-4 text-sm font-bold text-white shadow-sm transition-colors hover:bg-[#2e69d9]">Baca cerita lokal <ArrowRight class="size-4" /></a></div>
            <div class="grid gap-4 sm:grid-cols-3">
                <article v-for="story in stories" :key="story.title" class="group overflow-hidden rounded-xl border border-[#e0eaf3] bg-white shadow-[0_3px_12px_rgba(23,75,120,0.04)] transition-[border-color,box-shadow] duration-200 hover:border-[#b9dafa] hover:shadow-[0_8px_20px_rgba(23,75,120,0.08)]">
                    <div class="relative aspect-[16/9] overflow-hidden bg-sky-100"><img :src="imageUrl(story)" :alt="story.title" loading="lazy" class="size-full object-cover transition-transform duration-500 group-hover:scale-105" /><span class="absolute bottom-2 left-2 rounded-md bg-[#3E7BEF] px-2 py-1 text-[8px] font-bold text-white">{{ story.category }}</span></div>
                    <div class="p-4"><h3 class="line-clamp-2 text-sm font-bold leading-snug text-[#172c50] transition-colors group-hover:text-[#3E7BEF]">{{ story.title }}</h3><div class="mt-4 flex items-center justify-between gap-2 text-[9px] text-slate-500"><span class="inline-flex items-center gap-1"><CalendarDays class="size-3 text-[#3E7BEF]" />{{ story.date }}</span><span class="inline-flex items-center gap-1"><Clock3 class="size-3 text-[#3E7BEF]" />5 menit</span></div></div>
                </article>
            </div>
        </div>
    </section>
</template>
