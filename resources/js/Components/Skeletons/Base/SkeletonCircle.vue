<script setup>
defineProps({
    size: {
        type: String,
        default: '40px', // e.g. '40px', '48px', '80px' or tailwind size class via customClass
    },
});
</script>

<template>
    <div
        class="shrink-0 rounded-full skeleton-shimmer"
        :style="{
            width: size,
            height: size,
        }"
        aria-hidden="true"
    ></div>
</template>
