<script setup>
import { BadgeCheck, CircleDollarSign, CreditCard, Handshake, Sprout, Star } from 'lucide-vue-next';

const benefits = [
    { title: 'Trip Terverifikasi', description: 'Trip pilihan melalui proses verifikasi untuk perjalanan yang lebih tenang.', icon: BadgeCheck },
    { title: 'Harga Transparan', description: 'Rincian biaya jelas sejak awal. Rencanakan perjalanan sesuai anggaranmu.', icon: CircleDollarSign },
    { title: 'Partner Lokal', description: 'Jelajahi sisi autentik destinasi bersama partner yang mengenal daerahnya.', icon: Handshake },
    { title: 'Pembayaran Aman', description: 'Pilihan metode pembayaran tepercaya untuk memudahkan perjalananmu.', icon: CreditCard },
    { title: 'Dukung Lokal', description: 'Setiap perjalanan ikut mendukung usaha dan masyarakat setempat.', icon: Sprout },
    { title: 'Review Traveler', description: 'Temukan inspirasi dan pertimbangan dari pengalaman traveler lain.', icon: Star },
];
</script>

<template>
    <section class="relative isolate mx-auto mt-20 max-w-[1180px] overflow-hidden rounded-3xl border border-[#deedff] bg-gradient-to-br from-[#f0f8ff] via-white to-[#e8f2ff] px-5 py-10 sm:mt-24 sm:px-8 sm:py-12" aria-labelledby="why-choose-us-heading">
        <div class="pointer-events-none absolute -right-24 -top-32 -z-10 size-80 rounded-full bg-sky-200/30 blur-3xl" aria-hidden="true"></div>
        <div class="mx-auto max-w-xl text-center">
            <span class="inline-flex items-center gap-2 rounded-full border border-sky-100 bg-white/90 px-3 py-1.5 text-[10px] font-bold tracking-wide text-[#078cff]"><BadgeCheck class="size-3.5" aria-hidden="true" />TEMAN PERJALANANMU</span>
            <h2 id="why-choose-us-heading" class="mt-4 text-2xl font-extrabold leading-tight tracking-tight text-[#172c50] sm:text-3xl">Kenapa pilih trip di <span class="text-[#078cff]">Tapak Lokal?</span></h2>
            <p class="mt-3 text-sm leading-relaxed text-slate-500">Bukan sekadar tempat memesan trip.<br class="sm:hidden" /> Kami menemani setiap langkah perjalananmu.</p>
        </div>

        <ul class="mt-8 grid grid-cols-1 gap-3 sm:grid-cols-2 lg:grid-cols-3 lg:gap-4">
            <li v-for="benefit in benefits" :key="benefit.title" class="group flex items-start gap-4 rounded-2xl border border-white bg-white/85 p-5 shadow-[0_2px_8px_rgba(23,75,120,0.03)] transition duration-300 ease-out hover:-translate-y-1 hover:border-[#b9ddff] hover:bg-white hover:shadow-[0_10px_24px_rgba(23,100,180,0.09)] motion-reduce:transform-none motion-reduce:transition-none">
                <span class="grid size-12 shrink-0 place-items-center rounded-2xl bg-[#edf6ff] text-[#078cff] transition-colors duration-300 group-hover:bg-[#078cff] group-hover:text-white motion-reduce:transition-none"><component :is="benefit.icon" class="size-6" :stroke-width="1.8" aria-hidden="true" /></span>
                <div class="min-w-0 pt-0.5">
                    <h3 class="text-sm font-bold text-[#172c50]">{{ benefit.title }}</h3>
                    <p class="mt-2 text-xs leading-5 text-slate-500">{{ benefit.description }}</p>
                </div>
            </li>
        </ul>
    </section>
</template>
