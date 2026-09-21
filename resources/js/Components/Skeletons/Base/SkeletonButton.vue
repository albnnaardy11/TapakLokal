<script setup>
defineProps({
    size: {
        type: String,
        default: 'md', // 'sm' | 'md' | 'lg'
    },
    width: {
        type: String,
        default: '120px',
    },
    pill: {
        type: Boolean,
        default: true,
    },
});

const heightMap = {
    sm: 'h-8',
    md: 'h-10',
    lg: 'h-12',
};
</script>

<template>
    <div
        class="skeleton-shimmer shrink-0"
        :class="[
            heightMap[size] || 'h-10',
            pill ? 'rounded-full' : 'rounded-xl',
        ]"
        :style="{
            width: width,
        }"
        aria-hidden="true"
    ></div>
</template>
