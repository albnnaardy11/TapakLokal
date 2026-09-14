<script setup>
import { Head } from '@inertiajs/vue3';
import MainNavigation from '../Components/Home/MainNavigation.vue';
import TripFinder from '../Components/Home/TripFinder.vue';
import TripOptions from '../Components/Home/TripOptions.vue';
import FlashSale from '../Components/Home/FlashSale.vue';

defineProps({
    appName: { type: String, default: 'TapakLokal' },
});
</script>

<template>
    <Head :title="appName" />

    <div class="min-h-screen overflow-x-hidden bg-[#f5f7fb] font-sans text-slate-900">
        <MainNavigation />

        <main class="mx-auto max-w-[1440px] px-5 pb-28 pt-10 sm:px-10 lg:px-12 lg:pt-16">
            <section class="relative mx-auto max-w-[1180px]">
                <div class="relative isolate flex min-h-[360px] items-center justify-center overflow-hidden rounded-[24px] bg-slate-700 px-5 py-16 text-center sm:min-h-[470px] lg:min-h-[500px]">
                    <img src="https://images.unsplash.com/photo-1537996194471-e657df975ab4?auto=format&amp;fit=crop&amp;w=1800&amp;q=88" alt="Pura di Bali saat senja" class="absolute inset-0 -z-20 size-full object-cover" />
                    <div class="absolute inset-0 -z-10 bg-[#102129]/45"></div>
                    <div class="max-w-3xl text-white drop-shadow-lg">
                        <h1 class="text-4xl font-extrabold leading-[0.98] tracking-tight sm:text-6xl lg:text-7xl">Jelajahi Indonesia<br />Secara Otentik.</h1>
                        <p class="mx-auto mt-7 max-w-xl text-sm font-medium leading-relaxed text-white/80 sm:text-base">Destinasi tersembunyi, kuliner jujur, pemandu lokal terverifikasi—<br class="hidden sm:block" />tanpa biaya tersembunyi, langsung berdampak ke komunitas</p>
                    </div>
                </div>
                <TripFinder />
            </section>

            <section class="mx-auto mt-20 max-w-[1180px] sm:mt-24 lg:mt-24">
                <img src="/Assets/Images/benner/Benner-17an.svg" alt="Promo spesial kemerdekaan TapakLokal" class="w-full rounded-[24px] shadow-[0_10px_24px_rgba(22,53,102,0.1)]" />
            </section>

            <TripOptions />

            <FlashSale />
        </main>
    </div>
</template>
