<script setup>
defineProps({
    width: {
        type: String,
        default: '100%',
    },
    height: {
        type: String,
        default: 'auto',
    },
    aspectRatio: {
        type: String,
        default: null,
    },
    rounded: {
        type: String,
        default: 'rounded-xl',
    },
    shimmer: {
        type: Boolean,
        default: true,
    },
});
</script>

<template>
    <div
        :class="[
            rounded,
            shimmer ? 'skeleton-shimmer' : 'bg-slate-200/80',
        ]"
        :style="{
            width: width,
            height: height !== 'auto' ? height : undefined,
            aspectRatio: aspectRatio || undefined,
        }"
        aria-hidden="true"
    ></div>
</template>
