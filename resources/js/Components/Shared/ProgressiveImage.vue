<script setup>
import { ref } from 'vue';
import { ImageOff } from 'lucide-vue-next';

defineProps({
    src: {
        type: String,
        required: true,
    },
    alt: {
        type: String,
        default: '',
    },
    aspectRatio: {
        type: String,
        default: null, // e.g. '16/9', '4/3', '155/105'
    },
    rounded: {
        type: String,
        default: 'rounded-xl',
    },
    imageClass: {
        type: String,
        default: 'size-full object-cover',
    },
    loading: {
        type: String,
        default: 'lazy', // 'lazy' | 'eager'
    },
    fetchpriority: {
        type: String,
        default: 'auto', // 'auto' | 'high' | 'low'
    },
});

const hasError = ref(false);
const onError = () => {
    hasError.value = true;
};
</script>

<template>
    <div
        class="relative overflow-hidden bg-slate-100"
        :class="[rounded]"
        :style="{
            aspectRatio: aspectRatio || undefined,
        }"
    >
        <!-- Error Fallback -->
        <div
            v-if="hasError"
            class="absolute inset-0 flex flex-col items-center justify-center bg-slate-100 text-slate-400 p-2 text-center"
            role="img"
            :aria-label="alt || 'Gambar tidak dapat dimuat'"
        >
            <ImageOff class="size-6 mb-1 text-slate-300" aria-hidden="true" />
            <span class="text-[10px] text-slate-400 font-medium">Gambar tidak tersedia</span>
        </div>

        <!-- Real Image -->
        <img
            v-show="!hasError"
            :src="src"
            :alt="alt"
            :loading="loading"
            :fetchpriority="fetchpriority"
            :class="[imageClass]"
            @error="onError"
        />
    </div>
</template>
