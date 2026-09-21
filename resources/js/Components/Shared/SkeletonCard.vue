<script setup>
defineProps({
    variant: {
        type: String,
        default: 'card', // 'card' | 'trip' | 'article' | 'hero'
    },
    count: {
        type: Number,
        default: 1,
    },
});
</script>

<template>
    <div v-if="variant === 'card'" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4 sm:gap-5">
        <div
            v-for="n in count"
            :key="n"
            class="overflow-hidden rounded-2xl border border-slate-200/80 bg-white p-4 shadow-xs space-y-3.5"
        >
            <div class="h-44 w-full rounded-xl skeleton-shimmer"></div>
            <div class="space-y-2">
                <div class="h-4 w-20 rounded-md skeleton-shimmer"></div>
                <div class="h-5 w-4/5 rounded-md skeleton-shimmer"></div>
                <div class="h-3.5 w-3/5 rounded-md skeleton-shimmer"></div>
            </div>
            <div class="flex items-center justify-between pt-2 border-t border-slate-100">
                <div class="h-4 w-16 rounded-md skeleton-shimmer"></div>
                <div class="h-6 w-24 rounded-lg skeleton-shimmer"></div>
            </div>
        </div>
    </div>

    <div v-else-if="variant === 'article'" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
        <div
            v-for="n in count"
            :key="n"
            class="overflow-hidden rounded-2xl border border-slate-200/80 bg-white p-5 shadow-xs space-y-4"
        >
            <div class="h-48 w-full rounded-xl skeleton-shimmer"></div>
            <div class="flex items-center gap-2">
                <div class="h-4 w-16 rounded-full skeleton-shimmer"></div>
                <div class="h-4 w-24 rounded-full skeleton-shimmer"></div>
            </div>
            <div class="space-y-2">
                <div class="h-5 w-full rounded-md skeleton-shimmer"></div>
                <div class="h-4 w-5/6 rounded-md skeleton-shimmer"></div>
            </div>
        </div>
    </div>

    <div v-else-if="variant === 'hero'" class="rounded-[28px] sm:rounded-[36px] border border-slate-200/80 bg-white p-6 sm:p-10 lg:p-12 shadow-xs">
        <div class="grid grid-cols-1 gap-8 lg:grid-cols-2 lg:items-center">
            <div class="space-y-4 sm:space-y-5">
                <div class="h-4 w-32 rounded-full skeleton-shimmer"></div>
                <div class="space-y-2.5">
                    <div class="h-8 sm:h-11 w-3/4 rounded-xl skeleton-shimmer"></div>
                    <div class="h-8 sm:h-11 w-1/2 rounded-xl skeleton-shimmer"></div>
                </div>
                <div class="space-y-2 pt-2">
                    <div class="h-4 w-full rounded-md skeleton-shimmer"></div>
                    <div class="h-4 w-5/6 rounded-md skeleton-shimmer"></div>
                </div>
                <div class="flex gap-3 pt-3">
                    <div class="h-11 w-36 rounded-full skeleton-shimmer"></div>
                    <div class="h-11 w-44 rounded-full skeleton-shimmer"></div>
                </div>
            </div>
            <div class="flex items-center justify-center">
                <div class="h-56 sm:h-72 w-full max-w-[480px] rounded-2xl sm:rounded-3xl skeleton-shimmer"></div>
            </div>
        </div>
    </div>
</template>
