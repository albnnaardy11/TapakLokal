<script setup>
import { AlertTriangle, RotateCcw } from 'lucide-vue-next';

defineProps({
    title: {
        type: String,
        default: 'Gagal Memuat Data',
    },
    message: {
        type: String,
        default: 'Terjadi gangguan jaringan saat mengambil data. Silakan coba lagi.',
    },
    retryLabel: {
        type: String,
        default: 'Coba Lagi',
    },
    showRetry: {
        type: Boolean,
        default: true,
    },
});

defineEmits(['retry']);
</script>

<template>
    <div
        class="flex flex-col items-center justify-center rounded-2xl border border-red-100 bg-red-50/50 p-6 sm:p-10 text-center my-4"
        role="alert"
    >
        <div class="grid size-12 place-items-center rounded-2xl bg-red-100 text-red-600 mb-3">
            <AlertTriangle class="size-6" aria-hidden="true" />
        </div>
        <h3 class="text-base font-bold text-red-900">{{ title }}</h3>
        <p class="mt-1 max-w-md text-xs sm:text-sm text-red-700/80 leading-relaxed">{{ message }}</p>
        <div v-if="showRetry" class="mt-4">
            <button
                type="button"
                class="inline-flex items-center gap-2 rounded-full bg-white border border-red-200 px-4 py-2 text-xs font-bold text-red-700 shadow-xs transition hover:bg-red-100/60 focus-visible:outline-2 focus-visible:outline-red-500 active:scale-95"
                @click="$emit('retry')"
            >
                <RotateCcw class="size-3.5" aria-hidden="true" />
                {{ retryLabel }}
            </button>
        </div>
    </div>
</template>
