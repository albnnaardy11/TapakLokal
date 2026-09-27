<script setup>
import { ArrowRight } from 'lucide-vue-next';
import { Link } from '@inertiajs/vue3';
import { route } from 'ziggy-js';
import TripOptionSkeleton from '../Skeletons/Cards/TripOptionSkeleton.vue';

defineProps({
    isLoading: {
        type: Boolean,
        default: false,
    },
});

const tripOptions = [
    {
        id: 'open-trip',
        slug: 'pulau-pramuka',
        label: 'Open Trip',
        title: 'Open Trip',
        description: 'Gabung dengan traveler lain, nikmati perjalanan seru dengan biaya lebih hemat!',
        action: 'Lihat Open Trip',
        image: 'https://images.unsplash.com/photo-1464822759023-fed622ff2c3b?auto=format&fit=crop&w=1200&q=85',
        iconType: 'hiker',
    },
    {
        id: 'private-trip',
        slug: 'labuan-bajo',
        label: 'Private Trip',
        title: 'Private Trip',
        description: 'Perjalanan eksklusif untuk kamu, keluarga, atau teman. Bebas pilih waktu dan rute.',
        action: 'Lihat Private Trip',
        image: 'https://images.unsplash.com/photo-1529156069898-49953e39b3ac?auto=format&fit=crop&w=1200&q=85',
        iconType: 'crown',
    },
];
</script>

<template>
    <section
        class="mx-auto mt-8 max-w-[1180px] sm:mt-12 lg:mt-14"
        aria-labelledby="trip-options-heading"
        :aria-busy="isLoading"
    >
        <div>
            <div>
                <p class="text-xs font-bold uppercase tracking-[0.16em] text-[#3E7BEF]">Pilihan trip</p>
                <h2 id="trip-options-heading" class="mt-1 text-2xl font-extrabold tracking-tight text-slate-900 sm:text-3xl">Open Trip atau Private Trip?</h2>
                <p class="mt-1.5 text-sm text-slate-500">Apa pun gaya perjalananmu, kami punya pilihan yang pas.</p>
            </div>
        </div>

        <!-- Skeleton State -->
        <div v-if="isLoading" class="mt-5">
            <TripOptionSkeleton :count="2" />
        </div>

        <!-- Real Content -->
        <div v-else class="mt-5 grid gap-5 md:grid-cols-2">
            <article v-for="trip in tripOptions" :key="trip.id" class="group relative min-h-[240px] overflow-hidden rounded-2xl bg-slate-800 p-6 text-white shadow-[0_10px_24px_rgba(22,53,102,0.12)] transition duration-300 ease-out hover:-translate-y-1 hover:shadow-[0_18px_34px_rgba(23,105,170,0.20)] sm:min-h-[270px]">
                <img :src="trip.image" :alt="`${trip.title} bersama TapakLokal`" loading="lazy" class="absolute inset-0 size-full object-cover transition duration-500 group-hover:scale-105" />
                <div class="absolute inset-0 bg-gradient-to-r from-[#102129]/80 via-[#102129]/45 to-transparent"></div>
                <div class="relative flex h-full max-w-[290px] flex-col justify-end">
                    <!-- Distinct Category Icon (Person Hiking for Open Trip, Crown for Private Trip) -->
                    <span class="mb-4 grid size-11 place-items-center rounded-full bg-white text-[#0088ff] shadow-md transition-transform duration-300 group-hover:scale-108">
                        <!-- Official Person Hiking Icon for Open Trip -->
                        <svg
                            v-if="trip.iconType === 'hiker'"
                            class="size-5 fill-current"
                            viewBox="0 0 384 512"
                            aria-hidden="true"
                        >
                            <path d="M192 48a48 48 0 1 1 96 0 48 48 0 1 1 -96 0zm51.3 182.7L224.2 307l49.7 49.7c9 9 14.1 21.2 14.1 33.9l0 89.4c0 17.7-14.3 32-32 32s-32-14.3-32-32l0-82.7-73.9-73.9c-15.8-15.8-22.2-38.6-16.9-60.3l20.4-84c8.3-34.1 42.7-54.9 76.7-46.4c19 4.8 35.6 16.4 46.4 32.7L305.1 208l30.9 0 0-24c0-13.3 10.7-24 24-24s24 10.7 24 24l0 55.8c0 .1 0 .2 0 .2s0 .2 0 .2L384 488c0 13.3-10.7 24-24 24s-24-10.7-24-24l0-216-39.4 0c-16 0-31-8-39.9-21.4l-13.3-20zM81.1 471.9L117.3 334c3 4.2 6.4 8.2 10.1 11.9l41.9 41.9L142.9 488.1c-4.5 17.1-22 27.3-39.1 22.8s-27.3-22-22.8-39.1zm55.5-346L101.4 266.5c-3 12.1-14.9 19.9-27.2 17.9l-47.9-8c-14-2.3-22.9-16.3-19.2-30L31.9 155c9.5-34.8 41.1-59 77.2-59l4.2 0c15.6 0 27.1 14.7 23.3 29.8z"/>
                        </svg>

                        <!-- Official Crown Icon for Private Trip -->
                        <svg
                            v-else
                            class="size-5 fill-current"
                            viewBox="0 0 576 512"
                            aria-hidden="true"
                        >
                            <path d="M309 106c11.4-7 19-19.7 19-34c0-22.1-17.9-40-40-40s-40 17.9-40 40c0 14.4 7.6 27 19 34L209.7 220.6c-9.1 18.2-32.7 23.4-48.6 10.7L72 160c5-6.7 8-15 8-24c0-22.1-17.9-40-40-40S0 113.9 0 136s17.9 40 40 40c.2 0 .5 0 .7 0L86.4 427.4c5.5 30.4 32 52.6 63 52.6l277.2 0c30.9 0 57.4-22.1 63-52.6L535.3 176c.2 0 .5 0 .7 0c22.1 0 40-17.9 40-40s-17.9-40-40-40s-40 17.9-40 40c0 9 3 17.3 8 24l-89.1 71.3c-15.9 12.7-39.5 7.5-48.6-10.7L309 106z"/>
                        </svg>
                    </span>
                    <h3 class="text-xl font-extrabold">{{ trip.title }}</h3>
                    <p class="mt-1.5 text-sm leading-relaxed text-white/85">{{ trip.description }}</p>
                    <Link :href="route('trips.category', { type: trip.id })" class="mt-5 inline-flex w-fit items-center gap-2 rounded-full bg-white px-4 py-2.5 text-sm font-bold text-[#1769aa] transition hover:bg-[#e9f1ff] focus:outline-none focus:ring-4 focus:ring-white/40">{{ trip.action }} <ArrowRight class="size-4" /></Link>
                </div>
            </article>
        </div>
    </section>
</template>
