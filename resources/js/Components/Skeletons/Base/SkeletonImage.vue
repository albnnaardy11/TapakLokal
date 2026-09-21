<script setup>
defineProps({
    aspectRatio: {
        type: String,
        default: '16/9', // '16/9' | '4/3' | '1/1' | '155/105' etc.
    },
    rounded: {
        type: String,
        default: 'rounded-2xl',
    },
    height: {
        type: String,
        default: null,
    },
});
</script>

<template>
    <div
        class="w-full overflow-hidden skeleton-shimmer"
        :class="[rounded]"
        :style="{
            aspectRatio: height ? undefined : aspectRatio,
            height: height || undefined,
        }"
        aria-hidden="true"
    ></div>
</template>
