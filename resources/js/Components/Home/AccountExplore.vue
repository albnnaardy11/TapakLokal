<script setup>
import { Link } from '@inertiajs/vue3';
import { ArrowRight, Compass, Gift, MapPin, Utensils } from 'lucide-vue-next';

const recommendations = [
    { name: 'Kuliner', description: 'Cicipi rasa autentik dari dapur lokal.', icon: Utensils, image: '1601050690597-df0568f70950' },
    { name: 'Oleh-oleh', description: 'Bawa pulang cerita dalam setiap bingkisan.', icon: Gift, image: '1495474472287-4d71bcdd2085' },
    { name: 'Aktivitas', description: 'Temukan pengalaman seru di destinasi.', icon: MapPin, image: '1518548419970-58e3b4079ab2' },
];
</script>

<template>
    <section class="mt-7" aria-labelledby="account-extras-heading">
        <div class="mb-4 flex items-center gap-3"><span class="grid size-9 place-items-center rounded-xl bg-[#eaf3ff] text-[#3E7BEF]"><Compass class="size-5" /></span><div><h2 id="account-extras-heading" class="text-base font-bold tracking-tight text-[#203854]">Lengkapi perjalananmu</h2><p class="mt-1 text-[11px] leading-5 text-slate-500">Pilihan lokal untuk membuat setiap perjalanan lebih berkesan.</p></div></div>
        <div class="grid gap-3 sm:grid-cols-3">
            <Link v-for="item in recommendations" :key="item.name" href="/" class="group overflow-hidden rounded-2xl border border-[#e0e9f5] bg-white transition duration-300 hover:-translate-y-1 hover:border-[#bdd5fa] hover:shadow-[0_8px_24px_rgba(25,75,140,0.09)] focus-visible:outline-2 focus-visible:outline-[#3E7BEF] motion-reduce:transform-none motion-reduce:transition-none">
                <div class="relative h-28 overflow-hidden bg-sky-50"><img :src="`https://images.unsplash.com/photo-${item.image}?auto=format&fit=crop&w=480&q=85`" :alt="item.name" loading="lazy" class="size-full object-cover transition-transform duration-500 group-hover:scale-105 motion-reduce:transition-none" /><div class="absolute inset-0 bg-gradient-to-t from-slate-900/25 to-transparent"></div><span class="absolute bottom-3 left-3 grid size-8 place-items-center rounded-lg bg-white/95 text-[#3E7BEF] shadow-sm"><component :is="item.icon" class="size-4" /></span></div>
                <div class="p-4"><h3 class="text-sm font-bold text-[#203854]">{{ item.name }}</h3><p class="mt-1.5 min-h-10 text-[11px] leading-5 text-slate-500">{{ item.description }}</p><span class="mt-3 inline-flex items-center gap-2 text-[11px] font-semibold text-[#3E7BEF]">Lihat pilihan<ArrowRight class="size-3.5 transition-transform group-hover:translate-x-1 motion-reduce:transition-none" /></span></div>
            </Link>
        </div>
    </section>
    <section class="relative isolate mt-5 overflow-hidden rounded-2xl bg-[#123d68] p-6 sm:p-7" aria-labelledby="account-explore-heading">
        <img src="https://images.unsplash.com/photo-1516690561799-46d8f74f9abf?auto=format&fit=crop&w=1200&q=85" alt="" loading="lazy" class="absolute inset-0 -z-20 size-full object-cover" />
        <div class="absolute inset-0 -z-10 bg-gradient-to-r from-[#0e315a]/95 via-[#154b79]/80 to-[#123d68]/20"></div>
        <div class="flex flex-wrap items-center justify-between gap-6"><div class="max-w-sm"><span class="inline-flex items-center gap-1.5 rounded-full border border-white/25 bg-white/10 px-2.5 py-1 text-[9px] font-semibold tracking-wide text-sky-100"><Compass class="size-3" />CERITA BARU MENANTIMU</span><h2 id="account-explore-heading" class="mt-3 text-xl font-extrabold leading-tight tracking-tight text-white sm:text-2xl">Masih ingin menjelajah?</h2><p class="mt-2 text-xs leading-5 text-white/75">Dari pulau tersembunyi hingga cerita warga lokal, temukan tujuan perjalananmu berikutnya.</p></div><Link href="/" class="group inline-flex items-center gap-3 rounded-xl bg-white px-5 py-3 text-xs font-bold text-[#1759a0] shadow-lg transition hover:bg-sky-50 focus-visible:outline-2 focus-visible:outline-offset-4 focus-visible:outline-white">Jelajahi trip<ArrowRight class="size-4 transition-transform group-hover:translate-x-1 motion-reduce:transition-none" /></Link></div>
    </section>
</template>
