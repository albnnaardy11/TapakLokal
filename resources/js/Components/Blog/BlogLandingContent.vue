<script setup>
import { Link } from '@inertiajs/vue3';
import { ArrowRight, Backpack, CalendarDays, CheckCircle2, Clock3, Compass, HeartHandshake, MapPinned, ShieldCheck, WalletCards, Waves } from 'lucide-vue-next';
import { computed, ref } from 'vue';
import { articles } from '../Home/travelArticles';

const emit = defineEmits(['read']);
const activeNeed = ref('Semua');
const needs = [
    { label: 'Semua', icon: Compass, description: 'Pilihan artikel untukmu' },
    { label: 'Pertama kali', icon: Backpack, description: 'Mulai merencanakan trip' },
    { label: 'Atur budget', icon: WalletCards, description: 'Liburan lebih terukur' },
    { label: 'Wisata laut', icon: Waves, description: 'Aman dan bertanggung jawab' },
    { label: 'Cerita lokal', icon: HeartHandshake, description: 'Lebih dekat dengan warga' },
];
const mappedNeeds = {
    'Pertama kali': ['islands', 'packing'],
    'Atur budget': ['packing', 'local'],
    'Wisata laut': ['islands', 'snorkeling'],
    'Cerita lokal': ['local', 'bali'],
};
const visibleArticles = computed(() => activeNeed.value === 'Semua' ? articles.slice(1) : articles.filter((article) => mappedNeeds[activeNeed.value].includes(article.id)));
const articleImage = (article, width = 900) => `https://images.unsplash.com/photo-${article.image}?auto=format&fit=crop&w=${width}&q=88`;
</script>

<template>
    <div id="cerita" class="scroll-mt-28">
        <section class="mt-14 border-y border-[#e2ebf4] bg-white py-8 sm:mt-20 sm:py-10" aria-labelledby="travel-need-heading">
            <div class="mx-auto max-w-[1180px] px-5 sm:px-10 lg:px-12">
                <div class="flex flex-col gap-2 sm:flex-row sm:items-end sm:justify-between">
                    <div>
                        <p class="text-[10px] font-bold tracking-[0.14em] text-[#078cff]">MULAI DARI KEBUTUHANMU</p>
                        <h2 id="travel-need-heading" class="mt-2 text-2xl font-extrabold tracking-tight text-[#172c50] sm:text-3xl">Biar rencana liburan lebih jelas.</h2>
                    </div>
                    <p class="max-w-md text-sm leading-6 text-slate-500">Pilih topik yang paling dekat dengan perjalananmu, lalu temukan panduan yang relevan.</p>
                </div>
                <div class="mt-7 flex gap-3 overflow-x-auto pb-1 [scrollbar-width:none] [&::-webkit-scrollbar]:hidden">
                    <button v-for="need in needs" :key="need.label" type="button" class="group flex min-w-[174px] flex-1 items-center gap-3 rounded-xl border px-4 py-3.5 text-left transition-colors focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-[#078cff] sm:min-w-0" :class="activeNeed === need.label ? 'border-[#078cff] bg-[#eff8ff] text-[#075cae]' : 'border-[#e1eaf3] bg-white text-[#486581] hover:border-[#b8dafa] hover:bg-[#f8fbff]'" @click="activeNeed = need.label">
                        <span class="grid size-9 shrink-0 place-items-center rounded-lg" :class="activeNeed === need.label ? 'bg-[#078cff] text-white' : 'bg-[#eef6ff] text-[#078cff]'"><component :is="need.icon" class="size-4" /></span>
                        <span><span class="block text-xs font-bold">{{ need.label }}</span><span class="mt-0.5 block text-[10px] leading-4 text-slate-500">{{ need.description }}</span></span>
                    </button>
                </div>
            </div>
        </section>

        <section class="mx-auto mt-16 max-w-[1180px] px-5 sm:mt-20 sm:px-10 lg:px-12" aria-labelledby="article-choice-heading">
            <div class="flex items-end justify-between gap-5">
                <div>
                    <p class="text-[10px] font-bold tracking-[0.14em] text-[#078cff]">PILIHAN UNTUKMU</p>
                    <h2 id="article-choice-heading" class="mt-2 text-2xl font-extrabold tracking-tight text-[#172c50] sm:text-3xl">{{ activeNeed === 'Semua' ? 'Artikel yang membantu sebelum berangkat' : `Panduan ${activeNeed.toLowerCase()}` }}</h2>
                </div>
                <span class="hidden text-xs text-slate-500 sm:block">{{ visibleArticles.length }} artikel pilihan</span>
            </div>
            <div class="mt-7 grid gap-4 lg:grid-cols-2">
                <article v-for="article in visibleArticles" :key="article.id" class="group overflow-hidden rounded-2xl border border-[#dfeaf4] bg-white shadow-[0_5px_18px_rgba(23,75,120,0.04)] transition-shadow hover:shadow-[0_12px_28px_rgba(23,75,120,0.10)]">
                    <Link :href="route('blog.show', { article: article.id })" class="grid h-full w-full text-left sm:grid-cols-[42%_minmax(0,1fr)] focus-visible:outline-2 focus-visible:outline-offset-[-2px] focus-visible:outline-[#078cff]">
                        <span class="relative block min-h-48 overflow-hidden bg-sky-100 sm:min-h-full"><img :src="articleImage(article)" :alt="article.title" loading="lazy" class="absolute inset-0 size-full object-cover transition-transform duration-500 group-hover:scale-105" /><span class="absolute left-3 top-3 rounded-full bg-white/95 px-2.5 py-1 text-[9px] font-bold text-[#0875d2] shadow-sm">{{ article.category }}</span></span>
                        <span class="flex min-w-0 flex-col p-5 sm:p-6"><span class="inline-flex items-center gap-1.5 text-[10px] text-slate-400"><Clock3 class="size-3.5" />4 menit baca</span><span class="mt-3 text-lg font-bold leading-snug tracking-tight text-[#172c50] transition-colors group-hover:text-[#078cff]">{{ article.title }}</span><span class="mt-2 line-clamp-2 text-xs leading-5 text-slate-500">{{ article.excerpt }}</span><span class="mt-auto flex items-center gap-2 pt-5 text-xs font-bold text-[#078cff]">Baca panduan <ArrowRight class="size-4 transition-transform duration-200 group-hover:translate-x-1" /></span></span>
                    </Link>
                </article>
            </div>
        </section>

        <section class="mx-auto mt-16 max-w-[1180px] px-5 sm:mt-20 sm:px-10 lg:px-12" aria-labelledby="trip-promo-heading">
            <div class="relative isolate overflow-hidden rounded-2xl bg-[#0a6fc9] px-6 py-8 text-white sm:px-9 sm:py-10">
                <img src="https://images.unsplash.com/photo-1469474968028-56623f02e42e?auto=format&fit=crop&w=1500&q=84" alt="Lanskap pegunungan Indonesia" loading="lazy" class="absolute inset-0 -z-20 size-full object-cover opacity-35" />
                <div class="absolute inset-0 -z-10 bg-[linear-gradient(90deg,rgba(5,70,132,0.98),rgba(7,115,205,0.82))]" aria-hidden="true"></div>
                <div class="relative flex flex-col gap-7 lg:flex-row lg:items-center lg:justify-between">
                    <div class="max-w-2xl"><span class="inline-flex items-center gap-2 rounded-full bg-white/15 px-3 py-1.5 text-[10px] font-bold tracking-[0.1em]"><MapPinned class="size-3.5" />SUDAH PUNYA IDE?</span><h2 id="trip-promo-heading" class="mt-4 text-2xl font-extrabold leading-tight tracking-tight sm:text-3xl">Ubah inspirasi menjadi perjalanan yang benar-benar bisa kamu jalani.</h2><p class="mt-3 max-w-xl text-sm leading-6 text-white/80">Cari open trip dan private trip pilihan, lengkap dengan jadwal, fasilitas, dan informasi partner.</p></div>
                    <Link href="/" class="inline-flex min-h-11 shrink-0 items-center justify-center gap-2 rounded-xl bg-white px-5 text-sm font-bold text-[#0875d2] transition-colors hover:bg-[#eaf6ff] focus-visible:outline-2 focus-visible:outline-offset-4 focus-visible:outline-white">Cari trip sekarang <ArrowRight class="size-4" /></Link>
                </div>
            </div>
        </section>

        <section class="mx-auto mt-16 max-w-[1180px] px-5 sm:mt-20 sm:px-10 lg:px-12" aria-labelledby="ready-heading">
            <div class="grid gap-8 border-t border-[#e2ebf4] pt-10 lg:grid-cols-[0.82fr_1.18fr] lg:items-center">
                <div><p class="text-[10px] font-bold tracking-[0.14em] text-[#078cff]">SEBELUM BERANGKAT</p><h2 id="ready-heading" class="mt-2 text-2xl font-extrabold leading-tight tracking-tight text-[#172c50] sm:text-3xl">Hal kecil yang membuat perjalanan lebih nyaman.</h2><p class="mt-3 max-w-md text-sm leading-6 text-slate-500">Gunakan ini sebagai pengingat cepat ketika trip sudah mulai dekat.</p></div>
                <div class="grid gap-3 sm:grid-cols-3"><div class="rounded-xl border border-[#e0eaf4] bg-white p-5"><CalendarDays class="size-5 text-[#078cff]" /><p class="mt-4 text-sm font-bold text-[#172c50]">Cek jadwal</p><p class="mt-1 text-xs leading-5 text-slate-500">Simpan waktu kumpul dan kontak partner.</p></div><div class="rounded-xl border border-[#e0eaf4] bg-white p-5"><ShieldCheck class="size-5 text-[#078cff]" /><p class="mt-4 text-sm font-bold text-[#172c50]">Siapkan perlengkapan</p><p class="mt-1 text-xs leading-5 text-slate-500">Sesuaikan barang bawaan dengan aktivitas.</p></div><div class="rounded-xl border border-[#e0eaf4] bg-white p-5"><CheckCircle2 class="size-5 text-[#078cff]" /><p class="mt-4 text-sm font-bold text-[#172c50]">Pesan dengan yakin</p><p class="mt-1 text-xs leading-5 text-slate-500">Baca fasilitas dan ketentuan sebelum checkout.</p></div></div>
            </div>
        </section>
    </div>
</template>
