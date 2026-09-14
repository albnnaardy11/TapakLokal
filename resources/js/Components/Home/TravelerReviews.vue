<script setup>
import { ArrowLeft, ArrowRight, MapPin, Quote, Star } from 'lucide-vue-next';
import { ref } from 'vue';

const carousel = ref(null);
const reviews = [
    { name: 'Nadia Putri', initials: 'NP', trip: 'Open Trip Bali', color: 'bg-sky-100 text-sky-700', quote: 'Jadwalnya pas, tidak terburu-buru. Paling suka waktu diajak mencoba tempat makan lokal yang belum pernah aku dengar sebelumnya.', highlight: 'Banyak cerita baru' },
    { name: 'Raka Pratama', initials: 'RP', trip: 'Private Trip Lombok', color: 'bg-teal-100 text-teal-700', quote: 'Liburan keluarga jadi lebih santai. Bisa diskusi rencana perjalanan dulu, jadi aktivitasnya cocok untuk semua anggota keluarga.', highlight: 'Nyaman bersama keluarga' },
    { name: 'Alya Rahma', initials: 'AR', trip: 'Open Trip Bromo', color: 'bg-violet-100 text-violet-700', quote: 'Berangkat sendiri, pulang bawa teman baru. Pemandunya ramah dan membantu kami menikmati perjalanan dari awal sampai selesai.', highlight: 'Solo trip, teman baru' },
    { name: 'Dimas Saputra', initials: 'DS', trip: 'Private Trip Raja Ampat', color: 'bg-amber-100 text-amber-700', quote: 'Pemandangannya luar biasa, tapi cerita dari pemandu lokal yang bikin perjalanan ini makin berkesan. Ingin kembali lagi suatu hari.', highlight: 'Lebih dari sekadar liburan' },
];

const move = (direction) => {
    const element = carousel.value;
    if (! element) {
        return;
    }
    const step = element.firstElementChild.getBoundingClientRect().width + 20;
    const maximum = element.scrollWidth - element.clientWidth;
    const target = direction > 0 && element.scrollLeft >= maximum - 2 ? 0 : direction < 0 && element.scrollLeft <= 2 ? maximum : element.scrollLeft + direction * step;
    element.scrollTo({ left: target, behavior: window.matchMedia('(prefers-reduced-motion: reduce)').matches ? 'instant' : 'smooth' });
};
</script>

<template>
    <section class="relative isolate mx-auto mt-20 max-w-[1180px] overflow-hidden rounded-3xl border border-[#e1edf9] bg-gradient-to-br from-[#edf7ff] via-[#f8fbff] to-white px-5 py-9 sm:mt-24 sm:p-9" aria-labelledby="traveler-reviews-heading">
        <div class="pointer-events-none absolute -right-12 -top-20 -z-10 size-64 rounded-full bg-sky-100/70 blur-3xl" aria-hidden="true"></div>
        <div class="flex flex-wrap items-end justify-between gap-5">
            <div class="max-w-2xl">
                <span class="text-[10px] font-bold tracking-[0.12em] text-[#078cff]">CERITA SELEPAS PERJALANAN</span>
                <h2 id="traveler-reviews-heading" class="mt-2 text-2xl font-extrabold leading-tight tracking-tight text-[#172c50] sm:text-3xl">Pulang membawa kenangan.<br class="hidden sm:block" /><span class="text-[#078cff]">Berbagi cerita di sini.</span></h2>
                <p class="mt-3 text-sm leading-6 text-slate-500">Sudut pandang traveler tentang destinasi, teman baru, dan pengalaman lokal.</p>
            </div>
            <div class="flex items-center gap-2">
                <button type="button" class="grid size-10 place-items-center rounded-full border border-[#d4e7fa] bg-white text-[#078cff] transition hover:bg-sky-50 active:scale-95 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-[#078cff]" aria-label="Ulasan sebelumnya" @click="move(-1)"><ArrowLeft class="size-4" aria-hidden="true" /></button>
                <button type="button" class="grid size-10 place-items-center rounded-full bg-[#078cff] text-white shadow-sm transition hover:bg-[#0875d2] active:scale-95 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-[#078cff]" aria-label="Ulasan berikutnya" @click="move(1)"><ArrowRight class="size-4" aria-hidden="true" /></button>
            </div>
        </div>

        <div ref="carousel" class="mt-7 flex snap-x snap-mandatory gap-5 overflow-x-auto pb-4 pt-2 [scrollbar-width:none] [&::-webkit-scrollbar]:hidden" role="region" aria-label="Contoh ulasan traveler" tabindex="0">
            <figure v-for="review in reviews" :key="review.name" class="group flex w-[88%] shrink-0 snap-start flex-col rounded-2xl border border-[#e6eef7] bg-white p-6 shadow-[0_3px_12px_rgba(23,75,120,0.03)] transition duration-300 hover:-translate-y-1 hover:border-[#bbdeff] hover:shadow-[0_10px_24px_rgba(23,100,180,0.08)] motion-reduce:transform-none motion-reduce:transition-none sm:w-[calc((100%-20px)/2)] lg:w-[calc((100%-40px)/3)]">
                <div class="flex items-center justify-between gap-3">
                    <div class="flex items-center gap-1" aria-label="Rating contoh 5 dari 5"><Star v-for="star in 5" :key="star" class="size-3.5 fill-[#ffbf38] text-[#ffbf38]" aria-hidden="true" /></div>
                    <Quote class="size-7 fill-sky-50 text-[#bddfff]" :stroke-width="1.5" aria-hidden="true" />
                </div>
                <blockquote class="mt-4 flex-1">
                    <p class="text-base font-bold text-[#172c50]">{{ review.highlight }}</p>
                    <p class="mt-3 text-[13px] leading-6 text-slate-500">“{{ review.quote }}”</p>
                </blockquote>
                <figcaption class="mt-6 flex items-center gap-3 border-t border-[#edf2f8] pt-5">
                    <span class="grid size-11 shrink-0 place-items-center rounded-full text-xs font-bold" :class="review.color" aria-hidden="true">{{ review.initials }}</span>
                    <div><p class="text-xs font-bold text-[#172c50]">{{ review.name }}</p><p class="mt-1 flex items-center gap-1 text-[10px] text-slate-400"><MapPin class="size-3" aria-hidden="true" />{{ review.trip }}</p></div>
                </figcaption>
            </figure>
        </div>
        <p class="mt-2 text-[10px] text-slate-400">Pratinjau desain · Nama, rating, dan ulasan di atas adalah contoh.</p>
    </section>
</template>
