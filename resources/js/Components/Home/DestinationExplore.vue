<script setup>
import { computed } from 'vue';
import { router, Link } from '@inertiajs/vue3';
import { route } from 'ziggy-js';
import { ArrowRight } from 'lucide-vue-next';
import DestinationCard from './DestinationCard.vue';

const props = defineProps({
    destinations: {
        type: Array,
        default: () => [],
    },
});

const cards = computed(() =>
    props.destinations.map((item) => ({
        ...item,
        name: item.title,
        image: item.image_url,
    }))
);

const open = (item) => router.visit(route('content.show', item.slug));
</script>

<template>
    <section class="mx-auto mt-0 max-w-[1180px]" aria-labelledby="destinations-heading">
        <!-- Header with Title, Subtitle, and Link -->
        <div class="mb-5 sm:mb-6 flex flex-col sm:flex-row sm:items-end justify-between gap-3 px-1">
            <div>
                <!-- Main Heading with Accent Color -->
                <h2
                    id="destinations-heading"
                    class="text-xl sm:text-2xl lg:text-[26px] font-extrabold leading-tight tracking-tight text-slate-900"
                >
                    Jelajahi keindahan <span class="text-[#0088ff]">Indonesia</span>
                </h2>

                <!-- Subtitle -->
                <p class="mt-1 text-xs sm:text-sm font-medium text-slate-500">
                    Temukan surga tersembunyi dan tempat wisata paling populer di berbagai penjuru nusantara.
                </p>
            </div>

            <!-- Action Link -->
            <Link
                :href="typeof route === 'function' ? route('explore', 'destination') : '/explore/destination'"
                class="group inline-flex items-center gap-1.5 text-xs sm:text-sm font-bold text-[#0088ff] hover:text-[#0064d2] transition-colors py-1.5 px-3 rounded-xl hover:bg-sky-50/80 self-start sm:self-auto shrink-0 border border-transparent hover:border-sky-100"
            >
                <span>Lihat Semua Destinasi</span>
                <ArrowRight class="size-4 transition-transform group-hover:translate-x-1" />
            </Link>
        </div>

        <!-- 1:1 Bento Grid Layout (1 Large Tall Card Left + 3 Columns x 2 Rows Right) -->
        <div v-if="cards.length" class="grid grid-cols-1 md:grid-cols-[280fr_720fr] lg:grid-cols-[285fr_725fr] gap-3.5 sm:gap-4">
            <!-- 1. Left Large Tall Card (Spans full height of the 2 rows) -->
            <div class="h-72 sm:h-80 md:h-full">
                <DestinationCard
                    :destination="cards[0]"
                    class="size-full min-h-[280px] md:min-h-full"
                    @select="open(cards[0])"
                />
            </div>

            <!-- 2. Right Grid (Strictly 3 Columns x 2 Rows = 6 Equal Cards) -->
            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-3 grid-rows-2 gap-3.5 sm:gap-4">
                <div
                    v-for="item in cards.slice(1, 7)"
                    :key="item.id || item.slug || item.name"
                    class="aspect-[16/10] w-full"
                >
                    <DestinationCard :destination="item" class="size-full" @select="open(item)" />
                </div>
            </div>
        </div>

        <p v-else class="panel-surface p-8 text-sm text-slate-500">
            Belum ada destinasi yang diterbitkan.
        </p>
    </section>
</template>
