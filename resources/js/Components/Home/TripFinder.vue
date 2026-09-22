<script setup>
import { CalendarDays, ChevronDown, MapPin, Search, ShoppingBag, TentTree, X } from 'lucide-vue-next';
import { computed, ref } from 'vue';
import { router } from '@inertiajs/vue3';
import { route } from 'ziggy-js';

const activeType = ref('open-trip');
const destination = ref('');
const departureDate = ref('');
const category = ref('');
const searchMessage = ref('');
const destinationInput = ref(null);

const selectDestination = (name) => {
    activeType.value = 'open-trip';
    destination.value = name;
    searchMessage.value = name ? `Destinasi ${name} dipilih. Atur tanggal dan kategori, lalu klik Cari Sekarang.` : '';
    destinationInput.value?.focus({ preventScroll: true });
    destinationInput.value?.scrollIntoView({ behavior: window.matchMedia('(prefers-reduced-motion: reduce)').matches ? 'instant' : 'smooth', block: 'center' });
};

defineExpose({ selectDestination });

const tripTypes = [
    { id: 'open-trip', label: 'Open Trip', icon: TentTree },
    { id: 'private-trip', label: 'Private Trip', icon: MapPin },
    { id: 'oleh-oleh', label: 'Oleh-Oleh', icon: ShoppingBag },
];

const destinationPlaceholder = computed(() => activeType.value === 'oleh-oleh' ? 'Cari oleh-oleh khas daerah...' : 'Cari destinasi atau aktivitas...');

const searchTrips = () => {
    if (activeType.value === 'oleh-oleh') {
        router.get(route('explore', 'souvenir'), { q: destination.value.trim() });
        return;
    }
    router.get(route('catalog'), { q: destination.value.trim(), type: activeType.value, date: departureDate.value || undefined });
};
</script>

<template>
    <section class="absolute -bottom-14 left-1/2 z-20 w-[calc(100%-2rem)] max-w-[1120px] -translate-x-1/2 rounded-2xl border border-[#dce7f4] bg-white p-3 shadow-[0_18px_42px_rgba(17,54,92,0.20)] sm:w-[calc(100%-4rem)] sm:p-4">
        <div class="flex gap-1.5 overflow-x-auto pb-3">
            <button v-for="type in tripTypes" :key="type.id" class="inline-flex shrink-0 items-center gap-1.5 rounded-lg px-3.5 py-2 text-[11px] font-bold transition" :class="activeType === type.id ? 'bg-[#1677e8] text-white shadow-[0_3px_8px_rgba(22,119,232,0.28)]' : 'bg-[#f2f6fb] text-[#56708d] hover:bg-[#e8f2ff] hover:text-[#1677e8]'" @click="activeType = type.id; searchMessage = ''"><component :is="type.icon" class="size-3.5" />{{ type.label }}</button>
        </div>

        <form class="grid gap-2 rounded-xl bg-[#f5f8fc] p-2 sm:grid-cols-[minmax(0,1.7fr)_minmax(0,0.95fr)_auto]" @submit.prevent="searchTrips">
            <label class="flex min-w-0 items-center gap-2 rounded-lg border border-transparent bg-white px-3 py-3 text-slate-400 shadow-[0_1px_4px_rgba(29,73,120,0.08)] transition focus-within:border-[#91c5fc] focus-within:ring-2 focus-within:ring-[#dceeff]"><Search class="size-4 shrink-0 text-[#5f86b4]" /><input ref="destinationInput" v-model="destination" aria-label="Destinasi atau aktivitas" class="min-w-0 flex-1 bg-transparent text-xs text-slate-700 outline-none placeholder:text-slate-400" :placeholder="destinationPlaceholder" /></label>
            <label class="flex min-w-0 items-center gap-2 rounded-lg border border-transparent bg-white px-3 py-3 text-left text-xs text-slate-400 shadow-[0_1px_4px_rgba(29,73,120,0.08)] transition focus-within:border-[#91c5fc] focus-within:ring-2 focus-within:ring-[#dceeff]"><CalendarDays class="size-4 shrink-0 text-[#5f86b4]" /><input v-model="departureDate" type="date" class="min-w-0 flex-1 bg-transparent text-xs text-slate-600 outline-none" aria-label="Tanggal keberangkatan" /><button v-if="departureDate" type="button" class="text-slate-300 hover:text-[#1677e8]" aria-label="Hapus tanggal" @click="departureDate = ''"><X class="size-3.5" /></button></label>
            <button class="flex items-center justify-center gap-2 rounded-lg bg-[#1677e8] px-6 py-3 text-xs font-bold text-white shadow-[0_5px_12px_rgba(22,119,232,0.30)] transition hover:bg-[#0d68d1] focus:outline-none focus:ring-4 focus:ring-[#b9dcff] sm:min-w-36"><span>Cari Sekarang</span><span>→</span></button>
        </form>
        <p v-if="searchMessage" class="mt-2 rounded-lg bg-[#edf3ff] px-3 py-2 text-xs font-medium text-[#2868df]">{{ searchMessage }}</p>
    </section>
</template>
