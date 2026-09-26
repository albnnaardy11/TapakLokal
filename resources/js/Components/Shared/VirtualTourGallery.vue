<script setup>
import { computed, ref } from 'vue';
import PanoramaViewer from './PanoramaViewer.vue';
const props = defineProps({ tours: { type: Array, default: () => [] } });
const selected = ref(null);
const active = computed(() => props.tours.find(tour => tour.id === selected.value) || props.tours[0]);
</script>
<template><section v-if="tours.length" class="my-10"><p class="text-xs font-bold uppercase tracking-widest text-blue-600">Jelajah 360°</p><h2 class="mt-2 text-2xl font-bold text-[#17375f]">Lihat suasana sebelum berangkat</h2><div class="my-5 flex flex-wrap gap-2"><button v-for="tour in tours" :key="tour.id" type="button" class="rounded-xl border px-4 py-2 text-xs font-semibold" :class="active.id === tour.id ? 'border-blue-600 bg-blue-600 text-white' : 'border-slate-200 bg-white text-slate-600'" :aria-pressed="active.id === tour.id" @click="selected = tour.id">{{ tour.title }}</button></div><PanoramaViewer :key="active.id" :tour="active" /><p v-if="active.description" class="mt-3 text-sm leading-6 text-slate-500">{{ active.description }}</p></section></template>
