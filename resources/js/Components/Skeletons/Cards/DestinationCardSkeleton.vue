<script setup>
import SkeletonBox from '../Base/SkeletonBox.vue';

defineProps({
    variant: {
        type: String,
        default: 'section', // 'section' (full explore grid) | 'single'
    },
});
</script>

<template>
    <div
        v-if="variant === 'single'"
        class="relative overflow-hidden rounded-2xl bg-slate-200 skeleton-shimmer min-h-[140px] w-full"
        aria-hidden="true"
    >
        <div class="absolute bottom-3 left-3 space-y-1">
            <SkeletonBox width="80px" height="16px" rounded="rounded-md" />
            <SkeletonBox width="45px" height="12px" rounded="rounded-sm" />
        </div>
    </div>

    <div
        v-else
        class="grid gap-3.5 md:aspect-[1008/268] md:grid-cols-[284fr_213fr_485fr]"
        aria-hidden="true"
    >
        <div class="h-64 rounded-2xl md:h-auto skeleton-shimmer relative p-4 flex flex-col justify-end">
            <div class="space-y-1.5">
                <div class="h-5 w-28 rounded-md bg-white/40"></div>
                <div class="h-3.5 w-16 rounded-md bg-white/30"></div>
            </div>
        </div>
        <div class="grid grid-cols-2 gap-3 md:grid-cols-1 md:grid-rows-2">
            <div class="h-32 rounded-2xl md:h-full skeleton-shimmer relative p-3 flex flex-col justify-end">
                <div class="space-y-1">
                    <div class="h-4 w-20 rounded-md bg-white/40"></div>
                    <div class="h-3 w-12 rounded-md bg-white/30"></div>
                </div>
            </div>
            <div class="h-32 rounded-2xl md:h-full skeleton-shimmer relative p-3 flex flex-col justify-end">
                <div class="space-y-1">
                    <div class="h-4 w-20 rounded-md bg-white/40"></div>
                    <div class="h-3 w-12 rounded-md bg-white/30"></div>
                </div>
            </div>
        </div>
        <div class="grid grid-rows-2 gap-5">
            <div class="grid grid-cols-[267fr_204fr] gap-3.5">
                <div class="h-28 rounded-2xl md:h-full skeleton-shimmer"></div>
                <div class="h-28 rounded-2xl md:h-full skeleton-shimmer"></div>
            </div>
            <div class="grid grid-cols-[204fr_267fr] gap-3.5">
                <div class="h-28 rounded-2xl md:h-full skeleton-shimmer"></div>
                <div class="h-28 rounded-2xl md:h-full skeleton-shimmer"></div>
            </div>
        </div>
    </div>
</template>
