<script setup>
defineProps({
    lines: {
        type: Number,
        default: 1,
    },
    size: {
        type: String,
        default: 'md', // 'xs' | 'sm' | 'md' | 'lg' | 'xl' | '2xl'
    },
    rounded: {
        type: String,
        default: 'rounded-md',
    },
    lastLineWidth: {
        type: String,
        default: '70%',
    },
});

const heightMap = {
    xs: 'h-2.5',
    sm: 'h-3.5',
    md: 'h-4',
    lg: 'h-5',
    xl: 'h-7',
    '2xl': 'h-9',
};
</script>

<template>
    <div class="space-y-2 w-full" aria-hidden="true">
        <div
            v-for="i in lines"
            :key="i"
            class="skeleton-shimmer"
            :class="[
                heightMap[size] || 'h-4',
                rounded,
            ]"
            :style="{
                width: i === lines && lines > 1 ? lastLineWidth : '100%',
            }"
        ></div>
    </div>
</template>
