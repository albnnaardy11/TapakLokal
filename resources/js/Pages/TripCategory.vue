<script setup>
import { computed, reactive } from 'vue';
import { Head } from '@inertiajs/vue3';
import MainNavigation from '../Components/Shared/MainNavigation.vue';
import CatalogSearchHero from '../Components/Catalog/CatalogSearchHero.vue';
import MainFooter from '../Components/Shared/MainFooter.vue';

const props = defineProps({
    type: { type: String, default: 'open-trip' },
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
            <!-- Main Navigation with solid top bar -->
            <MainNavigation :transparent-on-top="false" />

            <!-- Daylight Sky Hero & Flight/Trip Style Multi-Field Search Bar (1:1 Reference Layout) -->
            <CatalogSearchHero :initial-filters="filters" />
        </div>

        <!-- Main Footer -->
        <MainFooter class="mt-16 sm:mt-20" />
    </div>
</template>
