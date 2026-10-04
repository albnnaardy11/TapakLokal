<script setup>
import { computed, reactive } from 'vue';
import { Head } from '@inertiajs/vue3';
import MainNavigation from '../Components/Shared/MainNavigation.vue';
import TripFinder from '../Components/Home/TripFinder.vue';
import MainFooter from '../Components/Shared/MainFooter.vue';
import TripOptions from '../Components/Home/TripOptions.vue';
import FlashSale from '../Components/Home/FlashSale.vue';
import PopularTripPartners from '../Components/Home/PopularTripPartners.vue';
import PopularPayments from '../Components/Home/PopularPayments.vue';
import TapakWalletBanner from '../Components/Home/TapakWalletBanner.vue';
import TripCatalogFaq from '../Components/Home/TripCatalogFaq.vue';

const props = defineProps({
    trips: { type: Object, default: () => ({ data: [] }) },
    filters: { type: Object, default: () => ({}) },
    featuredTrips: { type: Array, default: () => [] },
    cmsDestinations: { type: Array, default: () => [] },
    cmsFaqs: { type: Array, default: () => [] },
    cmsPartners: { type: Array, default: () => [] },
});

const filters = reactive({
    q: props.filters?.q || '',
    type: props.filters?.type || '',
    date: props.filters?.date || '',
    guests: props.filters?.guests || '',
});

const heroTitle = computed(() => {
    if (filters.type === 'open-trip') {
        return 'Temukan Open Trip Seru & Hemat di Indonesia.';
    }
    if (filters.type === 'private-trip') {
        return 'Eksplorasi Eksklusif dengan Private Trip.';
    }
    return 'Temukan Perjalanan dan Trip Impianmu.';
});
</script>

<template>
    <Head title="Cari & Jelajahi Trip - TapakLokal" />

    <div class="min-h-screen overflow-x-hidden bg-[#f8fafc] font-sans text-slate-900">
        <!-- Main Navigation with transparent hero integration at top -->
        <MainNavigation :transparent-on-top="true" />

        <!-- Full-Width Edge-to-Edge Hero Section (overflow-visible for search popovers) -->
        <section class="relative z-20 w-full overflow-visible bg-[#0c1f38] text-white min-h-[475px] sm:min-h-[495px] lg:min-h-[500px]">
            <!-- Full Width Background Image (contained in overflow-hidden) -->
            <div class="absolute inset-0 z-0 overflow-hidden pointer-events-none">
                <img
                    src="https://images.unsplash.com/photo-1507525428034-b723cf961d3e?auto=format&fit=crop&w=1920&q=88"
                    alt="Jelajahi Trip Indonesia"
                    class="size-full object-cover object-center brightness-[0.88] transition-opacity duration-500"
                />
                <div class="absolute inset-0 bg-[linear-gradient(180deg,rgba(8,24,50,0.45)_0%,rgba(12,32,62,0.30)_40%,rgba(10,22,40,0.82)_100%)]"></div>
            </div>

            <!-- Centered Hero Content Container (Shifted down proportionally) -->
            <div class="relative z-10 mx-auto max-w-[1180px] px-4 pt-36 pb-6 sm:px-6 sm:pt-40 sm:pb-8 lg:px-0 lg:pt-44 lg:pb-10 flex flex-col items-center justify-center">
                <!-- Hero Title -->
                <div class="max-w-3xl text-center text-white drop-shadow-md mb-6 sm:mb-7 lg:mb-8">
                    <h1 class="text-2xl font-bold leading-tight tracking-tight sm:text-3xl lg:text-[34px]">
                        {{ heroTitle }}
                    </h1>
                </div>

                <!-- Integrated Search Component -->
                <TripFinder :show-service-tabs="false" :initial-category="filters.type" :partners="cmsPartners" class="w-full" />
            </div>
        </section>

        <!-- Main Content Area with uniform 1180px width -->
        <main class="relative isolate mx-auto max-w-[1180px] px-4 pb-14 pt-4 sm:px-6 sm:pt-6 sm:pb-18 lg:px-0">
            <!-- Section Open Trip / Private Trip Directly Under Hero -->
            <TripOptions class="!mt-0" />

            <!-- Flash Sale Section -->
            <FlashSale :items="featuredTrips?.length ? featuredTrips : trips?.data" class="!mt-12 sm:!mt-16" />

            <!-- Popular Trip Partner Logos Section (1:1 with Airline Reference Layout) -->
            <PopularTripPartners class="!mt-14 sm:!mt-18" />

            <!-- Popular Payments Section (1:1 with Reference Layout) -->
            <PopularPayments class="!mt-14 sm:!mt-18" />

            <!-- TapakWallet Explanatory Banner (1:1 with Reference Layout) -->
            <TapakWalletBanner class="!mt-6 sm:!mt-8" />

            <!-- FAQ Section: Teknis Open/Private Trip & Pembayaran (1:1 Homepage Style) -->
            <TripCatalogFaq :questions="cmsFaqs" class="!mt-14 sm:!mt-18" />
        </main>

        <MainFooter />
    </div>
</template>


