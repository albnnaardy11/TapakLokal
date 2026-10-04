<script setup>
import { computed, ref } from 'vue';
import { ArrowRight, ChevronDown, CircleHelp } from 'lucide-vue-next';

const props = defineProps({
    items: { type: Array, default: () => [] },
    tripType: { type: String, required: true },
});

const openItems = ref([]);
const isPrivateTrip = computed(() => props.tripType === 'private-trip');
const questions = computed(() => props.items);

const allOpen = computed(() => openItems.value.length === questions.value.length);

const toggleItem = (index) => {
    openItems.value = openItems.value.includes(index)
        ? openItems.value.filter((item) => item !== index)
        : [...openItems.value, index];
};

const toggleAll = () => {
    openItems.value = allOpen.value ? [] : questions.value.map((_, index) => index);
};
</script>

<template>
    <section class="mt-7 overflow-hidden rounded-2xl border border-[#dfeaf5] bg-white shadow-[0_10px_28px_rgba(23,75,120,0.05)]" aria-labelledby="trip-faq-heading">
        <header class="flex flex-wrap items-center justify-between gap-4 border-b border-[#e8f0f7] px-5 py-4 sm:px-6"><div class="flex items-center gap-3"><span class="grid size-8 place-items-center rounded-lg bg-[#edf7ff] text-[#1688e8]"><CircleHelp class="size-4" :stroke-width="2.5" /></span><div><p class="text-[10px] font-bold uppercase tracking-[0.14em] text-[#1688e8]">Informasi trip</p><h2 id="trip-faq-heading" class="mt-0.5 text-base font-extrabold text-[#173b70]">Pertanyaan yang sering ditanyakan</h2></div></div><button type="button" class="group inline-flex items-center gap-2 text-xs font-bold text-[#1688e8] transition hover:text-[#096ab9]" @click="toggleAll">{{ allOpen ? 'Tutup semua' : 'Lihat semua' }}<ArrowRight class="size-4 transition-transform group-hover:translate-x-0.5" /></button></header>
        <p v-if="!questions.length" class="px-6 py-8 text-sm text-[#60789c]">Vendor belum menambahkan pertanyaan umum untuk trip ini.</p><div class="px-5 py-2 sm:px-6"><article v-for="(item, index) in questions" :key="item.question" class="border-b border-[#edf2f7] last:border-b-0"><button type="button" class="flex min-h-14 w-full items-center justify-between gap-4 py-3 text-left transition hover:text-[#1688e8]" :aria-expanded="openItems.includes(index)" @click="toggleItem(index)"><span class="flex min-w-0 items-center gap-3"><span class="w-5 shrink-0 text-xs font-extrabold tabular-nums" :class="openItems.includes(index) ? 'text-[#1688e8]' : 'text-[#9aafc4]'">0{{ index + 1 }}</span><span class="text-xs font-bold leading-5 text-[#31577f]">{{ item.question }}</span></span><span class="grid size-7 shrink-0 place-items-center rounded-full transition" :class="openItems.includes(index) ? 'bg-[#eaf5ff] text-[#1688e8]' : 'bg-transparent text-[#9aacbf]'"><ChevronDown class="size-4 transition-transform" :class="openItems.includes(index) ? 'rotate-180' : ''" /></span></button><div v-if="openItems.includes(index)" class="pb-4 pl-8 pr-10 text-xs leading-5 text-[#60789c]">{{ item.answer }}</div></article></div>
    </section>
</template>
