<script setup>
import { ArrowRight, UsersRound } from 'lucide-vue-next';
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
    },
    {
        id: 'private-trip',
        slug: 'labuan-bajo',
        label: 'Private Trip',
        title: 'Private Trip',
        description: 'Perjalanan eksklusif untuk kamu, keluarga, atau teman. Bebas pilih waktu dan rute.',
        action: 'Lihat Private Trip',
        image: 'https://images.unsplash.com/photo-1529156069898-49953e39b3ac?auto=format&fit=crop&w=1200&q=85',
    },
];
</script>

<template>
    <section
        class="mx-auto mt-20 max-w-[1180px] sm:mt-24"
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
                    <span class="mb-4 grid size-10 place-items-center rounded-full bg-white text-[#3E7BEF] shadow-sm"><UsersRound class="size-5" /></span>
                    <h3 class="text-xl font-extrabold">{{ trip.title }}</h3>
                    <p class="mt-1.5 text-sm leading-relaxed text-white/85">{{ trip.description }}</p>
                    <Link :href="route('catalog', { type: trip.id })" class="mt-5 inline-flex w-fit items-center gap-2 rounded-full bg-white px-4 py-2.5 text-sm font-bold text-[#1769aa] transition hover:bg-[#e9f1ff] focus:outline-none focus:ring-4 focus:ring-white/40">{{ trip.action }} <ArrowRight class="size-4" /></Link>
                </div>
            </article>
        </div>
    </section>
</template>
