<script setup>
import { computed } from 'vue';
import { router, Link } from '@inertiajs/vue3';
import { route } from 'ziggy-js';
import { ArrowRight, Compass } from 'lucide-vue-next';
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
        <!-- Header with Badge, Icon, Subtitle, and Link -->
        <div class="mb-5 sm:mb-6 flex flex-col sm:flex-row sm:items-end justify-between gap-3 px-1">
            <div>
                <!-- Category Pill Badge -->
                <div class="inline-flex items-center gap-1.5 rounded-full bg-sky-50 border border-sky-100 px-3 py-1 text-[11px] sm:text-xs font-bold text-[#0088ff] mb-2 shadow-xs">
                    <Compass class="size-3.5 text-[#0088ff]" />
                    <span>Destinasi Pilihan</span>
                </div>

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

        <!-- Bento Grid -->
        <div v-if="cards.length" class="grid gap-3.5 md:aspect-[1008/268] md:grid-cols-[284fr_213fr_485fr]">
            <div class="h-64 md:h-auto">
                <DestinationCard :destination="cards[0]" @select="open(cards[0])" />
            </div>
            <div class="grid grid-cols-2 gap-3 md:grid-cols-1 md:grid-rows-2">
                <DestinationCard v-for="item in cards.slice(1, 3)" :key="item.id" :destination="item" @select="open(item)" />
            </div>
            <div class="grid grid-cols-2 gap-3.5">
                <DestinationCard v-for="item in cards.slice(3, 7)" :key="item.id" :destination="item" @select="open(item)" />
            </div>
        </div>
        <p v-else class="panel-surface p-8 text-sm text-slate-500">
            Belum ada destinasi yang diterbitkan.
        </p>
    </section>
</template>
