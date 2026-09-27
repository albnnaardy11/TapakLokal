<script setup>
import { computed, reactive } from 'vue';
import { Head } from '@inertiajs/vue3';
import MainNavigation from '../Components/Shared/MainNavigation.vue';
import CatalogSearchHero from '../Components/Catalog/CatalogSearchHero.vue';
import CatalogResultsSection from '../Components/Catalog/CatalogResultsSection.vue';
import TripCatalogFaq from '../Components/Home/TripCatalogFaq.vue';
import MainFooter from '../Components/Shared/MainFooter.vue';

const props = defineProps({
    type: { type: String, default: 'open-trip' },
    partner: { type: Object, default: () => null },
    trips: { type: Object, default: () => ({ data: [] }) },
    filters: { type: Object, default: () => ({}) },
    cmsDestinations: { type: Array, default: () => [] },
    cmsFaqs: { type: Array, default: () => [] },
});

const filters = reactive({
    q: props.filters?.q || '',
    type: props.type || props.filters?.type || 'open-trip',
    date: props.filters?.date || '',
    guests: props.filters?.guests || '',
});

const isOpenTrip = computed(() => filters.type === 'open-trip');

const pageTitle = computed(() => {
    if (props.partner?.name) {
        return `Pilihan Paket Trip ${props.partner.name} - TapakLokal`;
    }
    if (isOpenTrip.value) {
        return 'Pilihan Paket Open Trip Hemat & Seru - TapakLokal';
    }
    return 'Pilihan Paket Private Trip Eksklusif & Nyaman - TapakLokal';
});
</script>

<template>
    <Head :title="pageTitle" />

    <div class="min-h-screen flex flex-col justify-between overflow-x-hidden bg-[#f4f7fb] font-sans text-slate-900">
        <div>
            <!-- Main Navigation (Static in document flow like Traveloka search catalog) -->
            <MainNavigation :transparent-on-top="false" :is-static="true" />

            <!-- Daylight Sky Hero & Flight/Trip Style Multi-Field Search Bar (1:1 Reference Layout) -->
            <CatalogSearchHero :initial-filters="filters" :partner="partner" />

            <!-- Traveloka Style Catalog Results Section with Left Filters & Right Trip Listings -->
            <CatalogResultsSection :trips="trips" :initial-filters="filters" :partner="partner" />

            <!-- FAQ Section: Seputar Open/Private Trip & Pembayaran (1:1 Homepage Style) -->
            <TripCatalogFaq :questions="cmsFaqs" class="!mt-12 sm:!mt-16" />
        </div>

        <!-- Main Footer -->
        <MainFooter class="mt-16 sm:mt-20" />
    </div>
</template>
