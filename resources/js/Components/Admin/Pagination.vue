<script setup>
import { Link } from '@inertiajs/vue3';
defineProps({ records: Object });
const label = (text) => text.replace(/&laquo;|&raquo;/g, '').replace('Previous', 'Sebelumnya').replace('Next', 'Berikutnya').trim();
</script>
<template>
    <div v-if="records?.last_page > 1" class="flex flex-wrap items-center justify-between gap-3 border-t border-slate-100 px-4 py-4">
        <span class="text-xs text-slate-400">{{ records.from }}–{{ records.to }} dari {{ records.total }}</span>
        <nav class="flex flex-wrap gap-1" aria-label="Halaman data"><template v-for="(link, i) in records.links" :key="i"><Link v-if="link.url" :href="link.url" preserve-scroll class="rounded-lg px-3 py-2 text-xs" :class="link.active ? 'bg-blue-600 text-white' : 'bg-slate-50 text-slate-600 hover:bg-blue-50'" :aria-current="link.active ? 'page' : undefined">{{ label(link.label) }}</Link><span v-else class="px-2 py-2 text-xs text-slate-300">{{ label(link.label) }}</span></template></nav>
    </div>
</template>

