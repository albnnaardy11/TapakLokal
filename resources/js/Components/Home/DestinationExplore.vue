<script setup>
import { ref } from 'vue';
import DestinationCard from './DestinationCard.vue';
import DestinationCardSkeleton from '../Skeletons/Cards/DestinationCardSkeleton.vue';

defineProps({
    isLoading: {
        type: Boolean,
        default: false,
    },
});

const emit = defineEmits(['select']);
const selectedDestination = ref('');
const destinations = {
    jogja: { name: 'Yogyakarta', trips: 34, image: 'https://images.unsplash.com/photo-1584810359583-96fc3448beaa?auto=format&fit=crop&w=800&q=85' },
    bali: { name: 'Bali', trips: 34, image: 'https://images.unsplash.com/photo-1537996194471-e657df975ab4?auto=format&fit=crop&w=640&q=85' },
    lombok: { name: 'Lombok', trips: 34, image: 'https://images.unsplash.com/photo-1518548419970-58e3b4079ab2?auto=format&fit=crop&w=640&q=85' },
    islands: { name: 'Yogyakarta', trips: 34, image: 'https://images.unsplash.com/photo-1516690561799-46d8f74f9abf?auto=format&fit=crop&w=800&q=85' },
    java: { name: 'Jawa Barat', trips: 34, image: 'https://images.unsplash.com/photo-1501179691627-eeaa65ea017c?auto=format&fit=crop&w=640&q=85' },
};

const selectDestination = (key) => {
    selectedDestination.value = key;
    emit('select', destinations[key].name);
};
</script>

<template>
    <section
        class="mx-auto mt-20 max-w-[1180px] sm:mt-24"
        aria-labelledby="destinations-heading"
        :aria-busy="isLoading"
    >
        <h2 id="destinations-heading" class="mb-4 px-2 text-xl font-extrabold leading-tight tracking-tight text-[#263142]">
            Jelajahi keindahan <span class="text-[#20a0ff]">indonesia</span>
        </h2>

        <!-- Section Skeleton when loading -->
        <DestinationCardSkeleton v-if="isLoading" variant="section" />

        <!-- Real Destination Grid -->
        <div v-else class="grid gap-3.5 md:aspect-[1008/268] md:grid-cols-[284fr_213fr_485fr]">
            <div class="h-64 md:h-auto">
                <DestinationCard :destination="destinations.jogja" :selected="selectedDestination === 'jogja'" @select="selectDestination('jogja')" />
            </div>
            <div class="grid grid-cols-2 gap-3 md:grid-cols-1 md:grid-rows-2">
                <DestinationCard v-for="key in ['bali', 'lombok']" :key="key" :destination="destinations[key]" :selected="selectedDestination === key" @select="selectDestination(key)" />
            </div>
            <div class="grid grid-rows-2 gap-5">
                <div v-for="(row, index) in [['islands', 'java'], ['java', 'islands']]" :key="index" class="grid gap-3.5" :class="index === 0 ? 'grid-cols-[267fr_204fr]' : 'grid-cols-[204fr_267fr]'">
                    <DestinationCard v-for="key in row" :key="key" :destination="destinations[key]" :selected="selectedDestination === `${key}-${index}`" @select="selectedDestination = `${key}-${index}`; emit('select', destinations[key].name)" />
                </div>
            </div>
        </div>
    </section>
</template>