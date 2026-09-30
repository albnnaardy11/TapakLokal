<script setup>
import { computed } from 'vue';
import { Link } from '@inertiajs/vue3';
import { ChevronLeft, ChevronRight } from 'lucide-vue-next';

const props = defineProps({
    records: {
        type: Object,
        default: () => ({}),
    },
});

const prevLink = computed(() => {
    const links = props.records?.links;
    if (!links || links.length === 0) return null;
    return links.find((l, idx) => idx === 0 || l.label.toLowerCase().includes('prev') || l.label.includes('«') || l.label.includes('&laquo;')) || links[0];
});

const nextLink = computed(() => {
    const links = props.records?.links;
    if (!links || links.length === 0) return null;
    return links.find((l, idx) => idx === links.length - 1 || l.label.toLowerCase().includes('next') || l.label.includes('»') || l.label.includes('&raquo;')) || links[links.length - 1];
});

const pageLinks = computed(() => {
    const links = props.records?.links;
    if (!links || links.length <= 1) return [];
    return links.filter((link, idx) => {
        const isFirst = idx === 0 && (link.label.toLowerCase().includes('prev') || link.label.includes('&laquo;') || link.label.includes('«'));
        const isLast = idx === links.length - 1 && (link.label.toLowerCase().includes('next') || link.label.includes('&raquo;') || link.label.includes('»'));
        return !isFirst && !isLast;
    });
});

const cleanLabel = (text) => {
    if (!text) return '';
    return text.replace(/&laquo;|&raquo;|«|»/g, '').trim();
};
</script>

<template>
    <div
        v-if="records?.last_page > 1"
        class="flex flex-col sm:flex-row items-center justify-between gap-4 border-t border-slate-100/90 pt-5 pb-2 select-none"
    >
        <!-- Result Counter -->
        <div class="text-xs text-slate-500 font-medium order-2 sm:order-1">
            Menampilkan
            <span class="font-bold text-slate-800">{{ (records.from ?? 1).toLocaleString('id-ID') }}</span>
            –
            <span class="font-bold text-slate-800">{{ (records.to ?? records.total).toLocaleString('id-ID') }}</span>
            dari
            <span class="font-bold text-slate-800">{{ (records.total ?? 0).toLocaleString('id-ID') }}</span>
            hasil
        </div>

        <!-- Navigation Controls -->
        <nav
            class="flex items-center gap-1.5 sm:gap-2 order-1 sm:order-2"
            aria-label="Navigasi Halaman"
        >
            <!-- Previous Button -->
            <Link
                v-if="prevLink?.url"
                :href="prevLink.url"
                preserve-scroll
                class="inline-flex h-9 items-center gap-1.5 rounded-xl border border-slate-200/90 bg-white px-3 text-xs font-bold text-slate-700 shadow-2xs transition-all duration-150 hover:border-[#1677e8] hover:bg-[#edf7ff] hover:text-[#1677e8] active:scale-95"
                aria-label="Halaman sebelumnya"
            >
                <ChevronLeft class="size-4 stroke-[2.2]" />
                <span class="hidden sm:inline">Sebelumnya</span>
            </Link>
            <span
                v-else
                class="inline-flex h-9 items-center gap-1.5 rounded-xl border border-slate-100 bg-slate-50/70 px-3 text-xs font-medium text-slate-300 opacity-60 cursor-not-allowed select-none"
                aria-disabled="true"
            >
                <ChevronLeft class="size-4 stroke-[2.2]" />
                <span class="hidden sm:inline">Sebelumnya</span>
            </span>

            <!-- Page Number Pills & Ellipsis -->
            <div class="flex items-center gap-1">
                <template v-for="(link, i) in pageLinks" :key="i">
                    <!-- Ellipsis -->
                    <span
                        v-if="cleanLabel(link.label) === '...' || (!link.url && !link.active)"
                        class="flex size-9 items-center justify-center text-xs font-bold text-slate-400 tracking-wider"
                    >
                        ...
                    </span>

                    <!-- Active Page -->
                    <span
                        v-else-if="link.active"
                        class="grid size-9 place-items-center rounded-xl bg-[#1677e8] text-xs font-extrabold text-white shadow-sm shadow-blue-500/25 border border-[#1677e8]"
                        aria-current="page"
                    >
                        {{ cleanLabel(link.label) }}
                    </span>

                    <!-- Page Link -->
                    <Link
                        v-else
                        :href="link.url"
                        preserve-scroll
                        class="grid size-9 place-items-center rounded-xl border border-slate-200/80 bg-white text-xs font-bold text-slate-600 shadow-2xs transition-all duration-150 hover:border-[#1677e8] hover:bg-[#edf7ff] hover:text-[#1677e8] active:scale-95"
                    >
                        {{ cleanLabel(link.label) }}
                    </Link>
                </template>
            </div>

            <!-- Next Button -->
            <Link
                v-if="nextLink?.url"
                :href="nextLink.url"
                preserve-scroll
                class="inline-flex h-9 items-center gap-1.5 rounded-xl border border-slate-200/90 bg-white px-3 text-xs font-bold text-slate-700 shadow-2xs transition-all duration-150 hover:border-[#1677e8] hover:bg-[#edf7ff] hover:text-[#1677e8] active:scale-95"
                aria-label="Halaman berikutnya"
            >
                <span class="hidden sm:inline">Berikutnya</span>
                <ChevronRight class="size-4 stroke-[2.2]" />
            </Link>
            <span
                v-else
                class="inline-flex h-9 items-center gap-1.5 rounded-xl border border-slate-100 bg-slate-50/70 px-3 text-xs font-medium text-slate-300 opacity-60 cursor-not-allowed select-none"
                aria-disabled="true"
            >
                <span class="hidden sm:inline">Berikutnya</span>
                <ChevronRight class="size-4 stroke-[2.2]" />
            </span>
        </nav>
    </div>
</template>
