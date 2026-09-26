<script setup>
import { computed } from 'vue';
import { router } from '@inertiajs/vue3';
import { route } from 'ziggy-js';
import DestinationCard from './DestinationCard.vue';
const props = defineProps({ destinations: { type: Array, default: () => [] } });
const cards = computed(() => props.destinations.map(item => ({ ...item, name: item.title, image: item.image_url })));
const open = item => router.visit(route('content.show', item.slug));
</script>
<template><section class="mx-auto mt-20 max-w-[1180px] sm:mt-24" aria-labelledby="destinations-heading"><h2 id="destinations-heading" class="mb-4 px-2 text-xl font-extrabold leading-tight tracking-tight text-[#263142]">Jelajahi keindahan <span class="text-[#20a0ff]">Indonesia</span></h2><div v-if="cards.length" class="grid gap-3.5 md:aspect-[1008/268] md:grid-cols-[284fr_213fr_485fr]"><div class="h-64 md:h-auto"><DestinationCard :destination="cards[0]" @select="open(cards[0])" /></div><div class="grid grid-cols-2 gap-3 md:grid-cols-1 md:grid-rows-2"><DestinationCard v-for="item in cards.slice(1,3)" :key="item.id" :destination="item" @select="open(item)" /></div><div class="grid grid-cols-2 gap-3.5"><DestinationCard v-for="item in cards.slice(3,7)" :key="item.id" :destination="item" @select="open(item)" /></div></div><p v-else class="panel-surface p-8 text-sm text-slate-500">Belum ada destinasi yang diterbitkan.</p></section></template>
