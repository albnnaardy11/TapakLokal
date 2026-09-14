<script setup>
import { ArrowRight, UsersRound } from 'lucide-vue-next';
import { ref } from 'vue';

const selectedTrip = ref('open-trip');

const tripOptions = [
    {
        id: 'open-trip',
        label: 'Open Trip',
        title: 'Open Trip',
        description: 'Gabung dengan traveler lain, nikmati perjalanan seru dengan biaya lebih hemat!',
        action: 'Lihat Open Trip',
        image: 'https://images.unsplash.com/photo-1464822759023-fed622ff2c3b?auto=format&fit=crop&w=1200&q=85',
    },
    {
        id: 'private-trip',
        label: 'Private Trip',
        title: 'Private Trip',
        description: 'Perjalanan eksklusif untuk kamu, keluarga, atau teman. Bebas pilih waktu dan rute.',
        action: 'Lihat Private Trip',
        image: 'https://images.unsplash.com/photo-1529156069898-49953e39b3ac?auto=format&fit=crop&w=1200&q=85',
    },
];

const selectTrip = (trip) => {
    selectedTrip.value = trip.id;
};
</script>

<template>
    <section class="mx-auto mt-20 max-w-[1180px] sm:mt-24">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <p class="text-xs font-bold uppercase tracking-[0.16em] text-[#3E7BEF]">Pilihan trip</p>
                <h2 class="mt-1 text-2xl font-extrabold tracking-tight text-slate-900 sm:text-3xl">Open Trip atau Private Trip?</h2>
                <p class="mt-1.5 text-sm text-slate-500">Apa pun gaya perjalananmu, kami punya pilihan yang pas.</p>
            </div>

            <div class="inline-flex w-full rounded-full bg-[#e9f1ff] p-1 sm:w-auto" role="tablist" aria-label="Jenis trip">
                <button v-for="trip in tripOptions" :key="trip.id" type="button" class="flex-1 rounded-full px-5 py-2 text-sm font-semibold transition-colors duration-200 sm:flex-none" :class="selectedTrip === trip.id ? 'bg-[#1769aa] text-white shadow-sm' : 'text-[#1769aa] hover:bg-white/70'" :aria-selected="selectedTrip === trip.id" role="tab" @click="selectTrip(trip)">{{ trip.label }}</button>
            </div>
        </div>

        <div class="mt-5 grid gap-5 md:grid-cols-2">
            <article v-for="trip in tripOptions" :key="trip.id" class="group relative min-h-[240px] cursor-pointer overflow-hidden rounded-2xl bg-slate-800 p-6 text-white shadow-[0_10px_24px_rgba(22,53,102,0.12)] transition duration-300 ease-out active:scale-[0.99] focus:outline-none focus-visible:ring-2 focus-visible:ring-[#3E7BEF] focus-visible:ring-offset-2 sm:min-h-[270px]" :class="selectedTrip === trip.id ? 'z-10 scale-[1.025] shadow-[0_18px_34px_rgba(23,105,170,0.26)]' : 'opacity-85 hover:opacity-100'" role="button" tabindex="0" :aria-pressed="selectedTrip === trip.id" @click="selectTrip(trip)" @keydown.enter="selectTrip(trip)" @keydown.space.prevent="selectTrip(trip)">
                <img :src="trip.image" :alt="`${trip.title} bersama TapakLokal`" class="absolute inset-0 size-full object-cover transition duration-500 group-hover:scale-105" />
                <div class="absolute inset-0 bg-gradient-to-r from-[#102129]/80 via-[#102129]/45 to-transparent"></div>
                <div class="relative flex h-full max-w-[290px] flex-col justify-end">
                    <span class="mb-4 grid size-10 place-items-center rounded-full bg-white text-[#3E7BEF] shadow-sm"><UsersRound class="size-5" /></span>
                    <h3 class="text-xl font-extrabold">{{ trip.title }}</h3>
                    <p class="mt-1.5 text-sm leading-relaxed text-white/85">{{ trip.description }}</p>
                    <button type="button" class="mt-5 inline-flex w-fit items-center gap-2 rounded-full bg-white px-4 py-2.5 text-sm font-bold text-[#1769aa] transition hover:bg-[#e9f1ff] focus:outline-none focus:ring-4 focus:ring-white/40" @click="selectTrip(trip)">{{ trip.action }} <ArrowRight class="size-4" /></button>
                </div>
            </article>
        </div>

    </section>
</template>
