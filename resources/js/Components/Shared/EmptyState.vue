<script setup>
import { Compass, RefreshCcw } from 'lucide-vue-next';
import { Link } from '@inertiajs/vue3';

defineProps({
    title: {
        type: String,
        default: 'Belum Ada Data',
    },
    message: {
        type: String,
        default: 'Saat ini belum ada data yang tersedia untuk kategori ini.',
    },
    actionLabel: {
        type: String,
        default: null,
    },
    actionRoute: {
        type: String,
        default: null,
    },
});

defineEmits(['action']);
</script>

<template>
    <div
        class="flex flex-col items-center justify-center rounded-2xl border border-dashed border-slate-200 bg-white/70 p-8 sm:p-12 text-center my-4"
        role="status"
    >
        <div class="grid size-14 place-items-center rounded-2xl bg-sky-50 text-[#3E7BEF] mb-4">
            <slot name="icon">
                <Compass class="size-7" aria-hidden="true" />
            </slot>
        </div>
        <h3 class="text-base sm:text-lg font-bold text-slate-800">{{ title }}</h3>
        <p class="mt-1.5 max-w-md text-xs sm:text-sm text-slate-500 leading-relaxed">{{ message }}</p>
        <div v-if="actionLabel" class="mt-5">
            <Link
                v-if="actionRoute"
                :href="actionRoute"
                class="inline-flex items-center gap-2 rounded-full bg-[#3E7BEF] px-5 py-2.5 text-xs sm:text-sm font-bold text-white shadow-sm transition hover:bg-[#2e69d9] focus-visible:outline-2 focus-visible:outline-[#3E7BEF]"
            >
                {{ actionLabel }}
            </Link>
            <button
                v-else
                type="button"
                class="inline-flex items-center gap-2 rounded-full bg-[#3E7BEF] px-5 py-2.5 text-xs sm:text-sm font-bold text-white shadow-sm transition hover:bg-[#2e69d9] focus-visible:outline-2 focus-visible:outline-[#3E7BEF]"
                @click="$emit('action')"
            >
                {{ actionLabel }}
            </button>
        </div>
    </div>
</template>
