<script setup>
import { onMounted, ref, watch } from 'vue';
import { useImageLoading } from '../../Composables/useImageLoading';
import { ImageOff } from 'lucide-vue-next';

const props = defineProps({
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

const imgRef = ref(null);
const { isLoaded, hasError, onLoad, onError, checkImage, reset } = useImageLoading();

onMounted(() => {
    checkImage(imgRef.value);
});

watch(() => props.src, () => {
    reset();
    setTimeout(() => checkImage(imgRef.value), 50);
});
</script>

<template>
    <div
        class="relative overflow-hidden bg-slate-100"
        :class="[rounded]"
        :style="{
            aspectRatio: aspectRatio || undefined,
        }"
    >
        <!-- Skeleton Placeholder when image is not yet loaded -->
        <div
            v-if="!isLoaded && !hasError"
            class="absolute inset-0 size-full skeleton-shimmer z-0"
            aria-hidden="true"
        ></div>

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
            ref="imgRef"
            :src="src"
            :alt="alt"
            :loading="loading"
            :fetchpriority="fetchpriority"
            class="transition-opacity duration-300 ease-out"
            :class="[
                imageClass,
                isLoaded ? 'opacity-100' : 'opacity-0',
            ]"
            @load="onLoad"
            @error="onError"
        />
    </div>
</template>
