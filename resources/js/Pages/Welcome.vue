<script setup>
import { Head } from '@inertiajs/vue3';
import MainNavigation from '../Components/Shared/MainNavigation.vue';
import TripFinder from '../Components/Home/TripFinder.vue';
import TripOptions from '../Components/Home/TripOptions.vue';
import DestinationExplore from '../Components/Home/DestinationExplore.vue';
import PartnerTrips from '../Components/Home/PartnerTrips.vue';
import DestinationGallery from '../Components/Home/DestinationGallery.vue';
import WhyChooseUs from '../Components/Home/WhyChooseUs.vue';
import BookingSteps from '../Components/Home/BookingSteps.vue';
import TrustedPartners from '../Components/Home/TrustedPartners.vue';
import TravelBlog from '../Components/Home/TravelBlog.vue';
import TravelFaq from '../Components/Home/TravelFaq.vue';
import TravelerReviews from '../Components/Home/TravelerReviews.vue';
import TravelBackdrop from '../Components/Home/TravelBackdrop.vue';
import { BadgeCheck, ShieldCheck, Star } from 'lucide-vue-next';
import { ref } from 'vue';

const tripFinder = ref(null);

defineProps({
    appName: { type: String, default: 'TapakLokal' },
});
</script>

<template>
    <Head :title="appName" />

    <div class="min-h-screen overflow-x-hidden bg-[#f8fafc] font-sans text-slate-900">
        <MainNavigation />

        <main class="relative isolate mx-auto max-w-[1440px] px-5 pb-28 pt-10 sm:px-10 lg:px-12 lg:pt-16">
            <div class="pointer-events-none absolute -top-16 left-1/2 -z-10 h-[900px] w-screen -translate-x-1/2 bg-[radial-gradient(ellipse_at_0%_25%,rgba(154,211,255,0.35),transparent_45%),radial-gradient(ellipse_at_100%_45%,rgba(182,228,235,0.32),transparent_40%)]" aria-hidden="true"></div>
            <section class="relative mx-auto max-w-[1180px]">
                <div class="relative isolate flex min-h-[360px] items-center justify-center overflow-hidden rounded-[24px] bg-[#3d2b20] px-5 py-16 text-center sm:min-h-[470px] sm:px-8 lg:min-h-[500px]">
                    <img src="https://images.unsplash.com/photo-1537996194471-e657df975ab4?auto=format&amp;fit=crop&amp;w=1800&amp;q=88" alt="Pura di Bali saat senja" class="absolute inset-0 -z-20 size-full object-cover" />
                    <div class="absolute inset-0 -z-10 bg-[linear-gradient(180deg,rgba(103,63,29,0.18)_0%,rgba(21,27,29,0.48)_55%,rgba(11,27,34,0.72)_100%)]"></div>
                    <div class="max-w-3xl text-white drop-shadow-lg">
                        <h1 class="text-4xl font-extrabold leading-[0.98] tracking-tight sm:text-6xl lg:text-7xl">Jelajahi Indonesia<br />Secara Otentik.</h1>
                        <p class="mx-auto mt-7 max-w-xl text-sm font-medium leading-relaxed text-white/85 sm:text-base">Destinasi tersembunyi, kuliner jujur, pemandu lokal terverifikasi—<br class="hidden sm:block" />tanpa biaya tersembunyi, langsung berdampak ke komunitas.</p>
                    </div>
                </div>
                <TripFinder ref="tripFinder" />
            </section>

            <section class="relative z-10 mx-auto max-w-[1180px] pt-24 sm:pt-28" aria-label="Keunggulan TapakLokal">
                <div class="grid overflow-hidden rounded-2xl border border-[#dce9f7] bg-white shadow-[0_8px_30px_rgba(23,75,120,0.06)] sm:grid-cols-3">
                    <div class="flex items-center gap-3 border-b border-[#e8f0f8] px-5 py-4 sm:border-b-0 sm:border-r sm:px-6"><span class="grid size-10 shrink-0 place-items-center rounded-xl bg-[#edf6ff] text-[#1677e8]"><BadgeCheck class="size-5" /></span><div><p class="text-sm font-bold text-[#17375f]">Partner terverifikasi</p><p class="mt-0.5 text-[11px] leading-4 text-slate-500">Trip pilihan melalui proses kurasi.</p></div></div>
                    <div class="flex items-center gap-3 border-b border-[#e8f0f8] px-5 py-4 sm:border-b-0 sm:border-r sm:px-6"><span class="grid size-10 shrink-0 place-items-center rounded-xl bg-[#edf6ff] text-[#1677e8]"><ShieldCheck class="size-5" /></span><div><p class="text-sm font-bold text-[#17375f]">Harga lebih transparan</p><p class="mt-0.5 text-[11px] leading-4 text-slate-500">Rincian biaya terlihat sebelum pesan.</p></div></div>
                    <div class="flex items-center gap-3 px-5 py-4 sm:px-6"><span class="grid size-10 shrink-0 place-items-center rounded-xl bg-[#edf6ff] text-[#1677e8]"><Star class="size-5" /></span><div><p class="text-sm font-bold text-[#17375f]">Cerita dari traveler</p><p class="mt-0.5 text-[11px] leading-4 text-slate-500">Pilih berdasarkan pengalaman nyata.</p></div></div>
                </div>
            </section>

            <TripOptions />
            <div class="relative isolate flow-root">
                <TravelBackdrop />
                <DestinationExplore @select="tripFinder?.selectDestination($event)" />
                <PartnerTrips @select="tripFinder?.selectDestination($event)" />
            </div>
            <section class="mx-auto mt-20 max-w-[1180px] sm:mt-24">
                <img src="/Assets/Images/benner/Benner-17an.svg" alt="Promo spesial kemerdekaan TapakLokal" class="w-full rounded-2xl border border-sky-100 shadow-[0_10px_24px_rgba(22,53,102,0.08)]" />
            </section>
            <DestinationGallery @select="tripFinder?.selectDestination($event)" />
            <WhyChooseUs />
            <BookingSteps @explore="tripFinder?.selectDestination('')" />
            <TrustedPartners />
            <TravelBlog />
            <div class="relative isolate flow-root pb-10 sm:pb-14">
                <TravelBackdrop variant="stories" />
                <TravelFaq />
                <TravelerReviews />
            </div>
        </main>
    </div>
</template>
