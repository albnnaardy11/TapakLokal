<script setup>
import { Head, Link } from '@inertiajs/vue3';
import MainNavigation from '../Components/Shared/MainNavigation.vue';
import TripFinder from '../Components/Home/TripFinder.vue';
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
const hero = computed(() => {
    const sections = Array.isArray(props.cmsSections)
        ? props.cmsSections
        : (props.cmsSections ? Object.values(props.cmsSections) : []);
    return sections.find(section => section && section.slug === 'hero') || sections[0] || null;
});

// Section-level loading states (Level 3 Loading Architecture)
const partnerTripsLoading = false;
const destinationsLoading = false;
const galleryLoading = false;
const reviewsLoading = false;
</script>

<template>
    <Head :title="appName">
        <link rel="preload" as="image" href="/Assets/Images/benner/Benner-17an.svg" type="image/svg+xml" fetchpriority="high" />
    </Head>

    <div class="min-h-screen overflow-x-hidden bg-[#f8fafc] font-sans text-slate-900">
        <!-- Main Navigation with transparent hero integration at top -->
        <MainNavigation :transparent-on-top="true" />

        <!-- Full-Width Edge-to-Edge Hero Section -->
        <section class="relative w-full overflow-hidden bg-[#0c1f38] text-white min-h-[520px] sm:min-h-[540px] lg:min-h-[560px]">
            <!-- Full Width Background Image -->
            <img
                :src="hero?.image_url || 'https://images.unsplash.com/photo-1537996194471-e657df975ab4?auto=format&fit=crop&w=1920&q=88'"
                :alt="hero?.title || 'Jelajahi Indonesia'"
                class="absolute inset-0 z-0 size-full object-cover object-center brightness-[0.88] transition-opacity duration-500"
                @error="(e) => { e.target.src = 'https://images.unsplash.com/photo-1507525428034-b723cf961d3e?auto=format&fit=crop&w=1920&q=80'; }"
            />
            <div class="absolute inset-0 z-[1] bg-[linear-gradient(180deg,rgba(8,24,50,0.45)_0%,rgba(12,32,62,0.30)_40%,rgba(10,22,40,0.82)_100%)] pointer-events-none"></div>

            <!-- Centered Hero Content Container -->
            <div class="relative z-10 mx-auto max-w-[1180px] px-4 pt-32 pb-7 sm:px-6 sm:pt-36 sm:pb-8 lg:px-0 lg:pt-40 lg:pb-10 flex flex-col items-center justify-center">
                <!-- Hero Title -->
                <div class="max-w-3xl text-center text-white drop-shadow-md mb-4 sm:mb-5">
                    <h1 class="text-2xl font-bold leading-tight tracking-tight sm:text-3xl lg:text-[34px]">
                        {{ hero?.title || 'Jelajahi Indonesia Secara Otentik.' }}
                    </h1>
                </div>

                <!-- Integrated Search Component -->
                <TripFinder ref="tripFinder" :partners="cmsPartners" class="w-full" />
            </div>
        </section>

        <!-- Main Content Area with uniform 1180px width -->
        <main class="relative isolate mx-auto max-w-[1180px] px-4 pb-28 pt-6 sm:px-6 sm:pt-8 lg:px-0">
            <!-- Promo Banner Directly Below Hero -->
            <section class="w-full mb-12 sm:mb-16">
                <Link
                    href="/account/vouchers"
                    class="block overflow-hidden rounded-2xl border border-sky-100 bg-[#0088ff] shadow-[0_10px_24px_rgba(22,53,102,0.08)] transition-transform duration-200 hover:scale-[1.008]"
                >
                    <img
                        src="/Assets/Images/benner/Benner-17an.svg"
                        alt="Promo spesial kemerdekaan TapakLokal"
                        width="1531"
                        height="351"
                        loading="eager"
                        fetchpriority="high"
                        decoding="sync"
                        class="w-full h-auto aspect-[1531/351] object-cover block"
                    />
                </Link>
            </section>

            <div class="relative isolate flow-root">
                <TravelBackdrop />
                <DestinationExplore :destinations="cmsDestinations" />
                <PartnerTrips :items="featuredTrips" />
            </div>
            <DestinationGallery :tours="virtualTours" />
            <WhyChooseUs />
            <BookingSteps @explore="tripFinder?.selectDestination('')" />
            <TrustedPartners :partners="cmsPartners" />
            <TravelBlog :items="cmsArticles" />
            <div class="relative isolate flow-root pb-10 sm:pb-14">
                <TravelBackdrop variant="stories" />
                <TravelFaq :questions="cmsFaqs" />
                <TravelerReviews :reviews="[...(Array.isArray(travelerReviews) ? travelerReviews : Object.values(travelerReviews || {})), ...(Array.isArray(cmsTestimonials) ? cmsTestimonials : Object.values(cmsTestimonials || {}))]" />
            </div>
        </main>
    </div>
</template>
