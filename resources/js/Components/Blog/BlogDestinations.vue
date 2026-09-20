<script setup>
import { Link } from '@inertiajs/vue3';
import { ArrowRight } from 'lucide-vue-next';

const destinations = [
    { name: 'Bali', stories: '112 cerita', image: '1537996194471-e657df975ab4' },
    { name: 'Raja Ampat', stories: '86 cerita', image: '1516690561799-46d8f74f9abf' },
    { name: 'Labuan Bajo', stories: '68 cerita', image: '1507525428034-b723cf961d3e' },
    { name: 'Lombok', stories: '52 cerita', image: '1539367628448-4bc5c9d171c8' },
];

const imageUrl = (destination) => `https://images.unsplash.com/photo-${destination.image}?auto=format&fit=crop&w=700&q=84`;
</script>

<template>
    <section class="mx-auto mt-16 max-w-[1180px] sm:mt-20" aria-labelledby="destination-stories-heading">
        <div class="flex items-end justify-between gap-5">
            <div>
                <p class="text-xs font-bold tracking-[0.14em] text-[#3E7BEF]">JELAJAHI INDONESIA</p>
                <h2 id="destination-stories-heading" class="mt-2 text-2xl font-extrabold leading-tight tracking-tight text-[#172c50] sm:text-3xl">Jelajahi berdasarkan <span class="text-[#3E7BEF]">destinasi</span></h2>
                <p class="mt-2 text-sm text-slate-500">Temukan lebih banyak cerita dari berbagai daerah di Indonesia.</p>
            </div>
            <Link href="/" class="hidden shrink-0 items-center gap-2 text-sm font-bold text-[#3E7BEF] transition-colors hover:text-[#2e69d9] sm:inline-flex">Lihat semua destinasi <ArrowRight class="size-4" /></Link>
        </div>
        <div class="mt-7 flex snap-x snap-mandatory gap-3 overflow-x-auto pb-3 [scrollbar-width:none] [&::-webkit-scrollbar]:hidden">
            <Link v-for="destination in destinations" :key="destination.name" href="/" class="group relative h-40 w-[220px] shrink-0 snap-start overflow-hidden rounded-xl bg-[#173b70] focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-[#3E7BEF] sm:h-48 sm:w-[260px] lg:h-52 lg:w-[calc((100%-36px)/4)]">
                <img :src="imageUrl(destination)" :alt="`Pemandangan ${destination.name}`" loading="lazy" class="absolute inset-0 size-full object-cover transition-transform duration-500 group-hover:scale-105" />
                <span class="absolute inset-0 bg-[linear-gradient(180deg,rgba(9,30,52,0.02)_25%,rgba(9,30,52,0.84)_100%)]" aria-hidden="true"></span>
                <span class="absolute inset-x-0 bottom-0 flex items-end justify-between gap-3 p-4 text-white"><span><span class="block text-sm font-extrabold">{{ destination.name }}</span><span class="mt-0.5 block text-xs text-white/80">{{ destination.stories }}</span></span><span class="grid size-8 shrink-0 place-items-center rounded-full border border-white/60 bg-white/10 transition-colors group-hover:bg-white group-hover:text-[#3E7BEF]"><ArrowRight class="size-4" /></span></span>
            </Link>
        </div>
        <Link href="/" class="mt-3 inline-flex items-center gap-2 text-sm font-bold text-[#3E7BEF] sm:hidden">Lihat semua destinasi <ArrowRight class="size-4" /></Link>
    </section>
</template>
