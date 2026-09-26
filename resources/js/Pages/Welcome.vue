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
import { computed, ref } from 'vue';
import ContentCards from '../Components/Shared/ContentCards.vue';
import TripCards from '../Components/Shared/TripCards.vue';

const tripFinder = ref(null);

const props = defineProps({
    appName: { type: String, default: 'TapakLokal' },
    cmsPartners: { type: Array, default: () => [] },
    cmsFaqs: { type: Array, default: () => [] },
    cmsSections: { type: Array, default: () => [] },
    cmsArticles: { type: Array, default: () => [] },
    cmsTestimonials: { type: Array, default: () => [] },
    cmsDestinations: { type: Array, default: () => [] },
    featuredTrips: { type: Array, default: () => [] },
    virtualTours: { type: Array, default: () => [] },
    travelerReviews: { type: Array, default: () => [] },
});
const hero = computed(() => props.cmsSections.find(section => section.slug === 'hero') || props.cmsSections[0]);

// Section-level loading states (Level 3 Loading Architecture)
const tripOptionsLoading = false;
const partnerTripsLoading = false;
const destinationsLoading = false;
const galleryLoading = false;
const reviewsLoading = false;
</script>

<template>
    <Head :title="appName" />

    <div class="min-h-screen overflow-x-hidden bg-[#f8fafc] font-sans text-slate-900">
        <MainNavigation />

        <main class="relative isolate mx-auto max-w-[1440px] px-4 pb-28 pt-3 sm:px-8 sm:pt-4 lg:px-10 lg:pt-5">
            <div class="pointer-events-none absolute -top-16 left-1/2 -z-10 h-[900px] w-screen -translate-x-1/2 bg-[radial-gradient(ellipse_at_0%_25%,rgba(154,211,255,0.35),transparent_45%),radial-gradient(ellipse_at_100%_45%,rgba(182,228,235,0.32),transparent_40%)]" aria-hidden="true"></div>
            <section class="relative mx-auto max-w-[1180px]">
                <div class="relative isolate flex min-h-[340px] items-center justify-center overflow-hidden rounded-[24px] bg-[#3d2b20] px-5 py-12 text-center sm:min-h-[400px] sm:px-8 sm:py-14 lg:min-h-[430px]">
                    <img :src="hero?.image_url || 'https://images.unsplash.com/photo-1537996194471-e657df975ab4?auto=format&fit=crop&w=1800&q=88'" :alt="hero?.title || 'Jelajahi Indonesia'" class="absolute inset-0 -z-20 size-full object-cover" />
                    <div class="absolute inset-0 -z-10 bg-[linear-gradient(180deg,rgba(103,63,29,0.18)_0%,rgba(21,27,29,0.48)_55%,rgba(11,27,34,0.72)_100%)]"></div>
                    <div class="max-w-3xl text-white drop-shadow-lg">
                        <h1 class="text-4xl font-extrabold leading-[0.98] tracking-tight sm:text-6xl lg:text-7xl">{{ hero?.title || 'Jelajahi Indonesia Secara Otentik.' }}</h1>
                        <p class="mx-auto mt-6 max-w-xl text-sm font-medium leading-relaxed text-white/85 sm:text-base">{{ hero?.excerpt || 'Temukan destinasi dan pengalaman bersama komunitas lokal.' }}</p>
                    </div>
                </div>
                <TripFinder ref="tripFinder" />
            </section>

            <TripOptions :is-loading="tripOptionsLoading" />
            <div class="relative isolate flow-root">
                <TravelBackdrop />
                <DestinationExplore :destinations="cmsDestinations" />
                <PartnerTrips :items="featuredTrips" />
            </div>
            <section class="mx-auto mt-20 max-w-[1180px] sm:mt-24">
                <img src="/Assets/Images/benner/Benner-17an.svg" alt="Promo spesial kemerdekaan TapakLokal" class="w-full rounded-2xl border border-sky-100 shadow-[0_10px_24px_rgba(22,53,102,0.08)]" />
            </section>
            <DestinationGallery :tours="virtualTours" />
            <WhyChooseUs />
            <BookingSteps @explore="tripFinder?.selectDestination('')" />
            <TrustedPartners :partners="cmsPartners" />
            <TravelBlog :items="cmsArticles" />
            <div class="relative isolate flow-root pb-10 sm:pb-14">
                <TravelBackdrop variant="stories" />
                <TravelFaq :questions="cmsFaqs" />
                <TravelerReviews :reviews="[...travelerReviews, ...cmsTestimonials]" />
            </div>
        </main>
    </div>
</template>
