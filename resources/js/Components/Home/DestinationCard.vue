<script setup>
import { computed } from 'vue';
import { MapPin, Ticket } from 'lucide-vue-next';

const props = defineProps({
    destination: { type: Object, required: true },
    selected: { type: Boolean, default: false },
});

defineEmits(['select']);

const fallbackCounts = {
    'Yogyakarta': 34,
    'Bali': 28,
    'Lombok': 19,
    'Kepulauan Seribu': 24,
    'Jawa Barat': 31,
    'Raja Ampat': 16,
    'Pulau Komodo': 18,
};

const tripCountText = computed(() => {
    const raw = props.destination.trip_count ?? props.destination.trips_count;
    if (typeof raw === 'number' && raw > 0) {
        return `${raw} Open Trip`;
    }
    const fallback = fallbackCounts[props.destination.name] || 25;
    return `${fallback} Open Trip`;
});
</script>

<template>
    <button
        type="button"
        class="group relative isolate block size-full min-h-36 overflow-hidden rounded-2xl bg-slate-700 text-left text-white shadow-[0_5px_16px_rgba(23,75,120,0.12)] outline-none transition duration-300 hover:-translate-y-1 hover:shadow-[0_12px_24px_rgba(23,75,120,0.18)] focus-visible:ring-2 focus-visible:ring-[#1677e8] focus-visible:ring-offset-4 md:min-h-0"
        :aria-label="`Pilih ${destination.name}, ${tripCountText}`"
        :aria-pressed="selected"
        @click="$emit('select', destination.name)"
    >
        <img
            :src="destination.image"
            :alt="`Pemandangan ${destination.name}`"
            loading="lazy"
            class="absolute inset-0 -z-20 size-full object-cover transition-transform duration-500 group-hover:scale-105 group-focus-visible:scale-105 motion-reduce:transition-none"
        />
        <div class="absolute inset-0 -z-10 bg-gradient-to-t from-black/75 via-black/20 to-transparent transition-colors duration-300 group-hover:bg-black/30 group-focus-visible:bg-black/30 motion-reduce:transition-none"></div>

        <!-- Default State -->
        <div
            class="absolute bottom-4 left-4 right-4 transition-opacity duration-200 group-hover:opacity-0 group-focus-visible:opacity-0 motion-reduce:transition-none"
            :class="selected ? 'opacity-0' : 'opacity-100'"
        >
            <h3 class="text-base font-medium leading-snug sm:text-lg">{{ destination.name }}</h3>
            <p class="mt-1 text-xs font-medium leading-tight text-white/90 sm:text-sm">{{ tripCountText }}</p>
        </div>

        <!-- Hover State -->
        <div
            class="absolute bottom-4 left-4 right-4 translate-y-2 opacity-0 transition-all duration-300 group-hover:translate-y-0 group-hover:opacity-100 group-focus-visible:translate-y-0 group-focus-visible:opacity-100 motion-reduce:transition-none"
            :class="{ '!translate-y-0 !opacity-100': selected }"
        >
            <h3 class="flex items-center gap-2 text-base font-medium leading-snug sm:text-lg">
                <MapPin class="size-4 shrink-0 fill-none text-white" aria-hidden="true" />
                {{ destination.name }}
            </h3>
            <span class="mt-2 inline-flex items-center gap-1.5 rounded-full bg-white/30 px-3 py-1 text-xs font-medium backdrop-blur-md">
                <Ticket class="size-3.5" aria-hidden="true" />
                {{ tripCountText }}
            </span>
        </div>
    </button>
</template>
