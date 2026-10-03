<script setup>
import { Link } from '@inertiajs/vue3';
import { ArrowRight, Compass } from 'lucide-vue-next';
defineProps({ compact: Boolean });
</script>

<template>
    <section class="relative isolate overflow-hidden rounded-2xl bg-[#123d68]" :class="compact ? 'mb-5 p-4 sm:px-5 sm:py-4' : 'mt-5 p-6 sm:p-7'" aria-labelledby="account-explore-heading">
        <img src="https://images.unsplash.com/photo-1516690561799-46d8f74f9abf?auto=format&fit=crop&w=1200&q=85" alt="" loading="lazy" class="absolute inset-0 -z-20 size-full object-cover" />
        <div class="absolute inset-0 -z-10 bg-gradient-to-r from-[#0e315a]/95 via-[#154b79]/80 to-[#123d68]/20"></div>
        <div v-if="compact" class="flex items-center justify-between gap-4">
            <div><h2 id="account-explore-heading" class="text-lg font-extrabold leading-tight text-white sm:text-xl">Perjalanan baru, cerita baru.</h2><p class="mt-1 text-[11px] text-white/85">Temukan pengalaman tak terlupakan di setiap sudut Indonesia.</p><Link href="/" class="mt-3 inline-flex items-center gap-3 rounded-lg bg-white px-3 py-2 text-[10px] font-bold text-[#1759a0] hover:bg-sky-50">Jelajahi Destinasi<ArrowRight class="size-3" /></Link></div>
            <p class="hidden shrink-0 -rotate-6 text-right text-sm font-semibold italic leading-5 text-white sm:block">Dukung Lokal<br />Jaga Budaya</p>
        </div>
        <div v-else class="flex flex-wrap items-center justify-between gap-6"><div class="max-w-sm"><span class="inline-flex items-center gap-1.5 rounded-full border border-white/25 bg-white/10 px-2.5 py-1 text-[9px] font-semibold tracking-wide text-sky-100"><Compass class="size-3" />CERITA BARU MENANTIMU</span><h2 id="account-explore-heading" class="mt-3 text-xl font-extrabold leading-tight tracking-tight text-white sm:text-2xl">Masih ingin menjelajah?</h2><p class="mt-2 text-xs leading-5 text-white/75">Dari pulau tersembunyi hingga cerita warga lokal, temukan tujuan perjalananmu berikutnya.</p></div><Link href="/" class="group inline-flex items-center gap-3 rounded-xl bg-white px-5 py-3 text-xs font-bold text-[#1759a0] shadow-lg transition hover:bg-sky-50 focus-visible:outline-2 focus-visible:outline-offset-4 focus-visible:outline-white">Jelajahi trip<ArrowRight class="size-4 transition-transform group-hover:translate-x-1 motion-reduce:transition-none" /></Link></div>
    </section>
</template>
