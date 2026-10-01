<script setup>
import { computed } from 'vue';
import { Link, usePage } from '@inertiajs/vue3';
import { route } from 'ziggy-js';
import { ChevronRight } from 'lucide-vue-next';

const props = defineProps({
    tier: {
        type: String,
        default: null,
    },
    customClass: {
        type: String,
        default: '',
    },
});

const page = usePage();

const currentTier = computed(() => {
    if (props.tier) return props.tier;
    return page.props.auth?.user?.tier || 'Bronze';
});

// Traveloka Priority Authentic Color Palette (Clean, Simple, Professional)
const config = computed(() => {
    const t = (currentTier.value || 'Bronze').toLowerCase();
    
    if (t.includes('gold')) {
        return {
            tierName: 'Gold',
            gradient: 'linear-gradient(90deg, #C5963E 0%, #9E7321 100%)',
        };
    }
    
    if (t.includes('silver')) {
        return {
            tierName: 'Silver',
            gradient: 'linear-gradient(90deg, #748091 0%, #546071 100%)',
        };
    }

    if (t.includes('plat')) {
        return {
            tierName: 'Platinum',
            gradient: 'linear-gradient(90deg, #2D3748 0%, #1A202C 100%)',
        };
    }

    // Bronze Priority (Default Traveloka Bronze)
    return {
        tierName: 'Bronze',
        gradient: 'linear-gradient(90deg, #A86B3E 0%, #874E25 100%)',
    };
});
</script>

<template>
    <Link
        :href="route('priority.about')"
        class="group flex items-center justify-between gap-2 rounded-xl px-3.5 py-2.5 text-white transition-all duration-200 hover:brightness-105 active:scale-[0.99] select-none cursor-pointer shadow-xs border border-white/15"
        :class="customClass"
        :style="{ background: config.gradient }"
    >
        <!-- Semi-bold Indonesian Priority Text -->
        <p class="truncate text-[12px] sm:text-[12.5px] font-semibold tracking-tight leading-none text-white">
            Kamu adalah {{ config.tierName }} Prioritas
        </p>

        <ChevronRight
            class="size-4 shrink-0 text-white/90 stroke-[2.2] transition-transform duration-200 group-hover:translate-x-0.5"
        />
    </Link>
</template>

