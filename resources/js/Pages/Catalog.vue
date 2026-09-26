<script setup>
import { computed, reactive } from 'vue';
import { Head } from '@inertiajs/vue3';
import MainNavigation from '../Components/Shared/MainNavigation.vue';
import TripFinder from '../Components/Home/TripFinder.vue';
import TripOptions from '../Components/Home/TripOptions.vue';

const props = defineProps({
    trips: { type: Object, default: () => ({ data: [] }) },
    filters: { type: Object, default: () => ({}) },
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
    <Head title="Jelajahi Trip - TapakLokal" />

    <div class="min-h-screen overflow-x-hidden bg-[#f8fafc] font-sans text-slate-900">
        <!-- Main Navigation with transparent hero integration at top -->
        <MainNavigation :transparent-on-top="true" />

        <!-- Full-Width Edge-to-Edge Hero Section (Clean & Proportional) -->
        <section class="relative w-full overflow-hidden bg-[#0c1f38] text-white">
            <!-- Full Width Background Image -->
            <img
                src="https://images.unsplash.com/photo-1507525428034-b723cf961d3e?auto=format&fit=crop&w=1920&q=88"
                alt="Jelajahi Trip Indonesia"
                class="absolute inset-0 z-0 size-full object-cover object-center brightness-[0.88] transition-opacity duration-500"
            />
            <div class="absolute inset-0 z-[1] bg-[linear-gradient(180deg,rgba(8,24,50,0.45)_0%,rgba(12,32,62,0.30)_40%,rgba(10,22,40,0.82)_100%)] pointer-events-none"></div>

            <!-- Centered Hero Content Container (Tight & Proportional) -->
            <div class="relative z-10 mx-auto max-w-[1180px] px-4 pt-32 pb-4 sm:px-6 sm:pt-36 sm:pb-5 lg:px-0 lg:pt-36 lg:pb-6 flex flex-col items-center justify-center">
                <!-- Hero Title (Elevated with balanced proportional spacing above search) -->
                <div class="max-w-3xl text-center text-white drop-shadow-md mb-8 sm:mb-10 lg:mb-11">
                    <h1 class="text-2xl font-bold leading-tight tracking-tight sm:text-3xl lg:text-[34px]">
                        {{ heroTitle }}
                    </h1>
                </div>

                <!-- Integrated Search Component (without top service tabs) -->
                <TripFinder :show-service-tabs="false" :initial-category="filters.type" class="w-full" />
            </div>
        </section>

        <!-- Main Content Area with uniform 1180px width -->
        <main class="relative isolate mx-auto max-w-[1180px] px-4 pb-28 pt-4 sm:px-6 sm:pt-6 lg:px-0">
            <!-- Section Open Trip / Private Trip Directly Under Hero -->
            <TripOptions class="!mt-0" />
        </main>
    </div>
</template>
