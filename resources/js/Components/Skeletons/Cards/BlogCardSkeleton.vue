<script setup>
import SkeletonImage from '../Base/SkeletonImage.vue';
import SkeletonText from '../Base/SkeletonText.vue';
import SkeletonBox from '../Base/SkeletonBox.vue';

defineProps({
    count: {
        type: Number,
        default: 1,
    },
    layout: {
        type: String,
        default: 'grid', // 'grid' | 'carousel' | 'featured'
    },
});
</script>

<template>
    <div
        v-if="layout === 'grid'"
        class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6"
        aria-hidden="true"
    >
        <div
            v-for="n in count"
            :key="n"
            class="flex flex-col overflow-hidden rounded-2xl border border-slate-200/80 bg-white p-5 space-y-4 shadow-xs"
        >
            <div class="relative">
                <SkeletonImage aspectRatio="4/3" rounded="rounded-xl" />
                <div class="absolute left-3 top-3">
                    <SkeletonBox width="64px" height="24px" rounded="rounded-full" />
                </div>
            </div>
            <div class="space-y-3 flex-1 flex flex-col">
                <div class="flex items-center justify-between">
                    <SkeletonBox width="70px" height="14px" rounded="rounded-md" />
                    <SkeletonBox width="60px" height="14px" rounded="rounded-md" />
                </div>
                <SkeletonText :lines="1" size="lg" />
                <SkeletonText :lines="2" size="sm" lastLineWidth="75%" />
                <div class="mt-auto pt-4 flex items-center justify-between">
                    <SkeletonBox width="80px" height="16px" rounded="rounded-md" />
                </div>
            </div>
        </div>
    </div>

    <div
        v-else-if="layout === 'carousel'"
        class="flex gap-5 overflow-hidden pb-5 pt-1"
        aria-hidden="true"
    >
        <div
            v-for="n in count"
            :key="n"
            class="flex flex-col rounded-2xl border border-[#e1eaf3] bg-white p-5 space-y-4 w-[85%] shrink-0 sm:w-[calc((100%-20px)/2)] lg:w-[calc((100%-60px)/4)]"
        >
            <div class="relative">
                <SkeletonImage aspectRatio="4/3" rounded="rounded-xl" />
                <div class="absolute left-3 top-3">
                    <SkeletonBox width="56px" height="22px" rounded="rounded-full" />
                </div>
            </div>
            <div class="space-y-2.5 flex-1 flex flex-col">
                <div class="flex justify-between">
                    <SkeletonBox width="60px" height="12px" rounded="rounded-md" />
                    <SkeletonBox width="50px" height="12px" rounded="rounded-md" />
                </div>
                <SkeletonText :lines="1" size="md" />
                <SkeletonText :lines="2" size="xs" lastLineWidth="70%" />
                <div class="mt-auto pt-3">
                    <SkeletonBox width="75px" height="14px" rounded="rounded-md" />
                </div>
            </div>
        </div>
    </div>

    <div
        v-else-if="layout === 'featured'"
        class="grid gap-5 lg:grid-cols-[1.65fr_1fr]"
        aria-hidden="true"
    >
        <div class="rounded-[24px] bg-slate-200 min-h-[440px] sm:min-h-[510px] p-6 sm:p-9 flex flex-col justify-end space-y-4 skeleton-shimmer">
            <SkeletonBox width="140px" height="16px" rounded="rounded-md" />
            <SkeletonBox width="75%" height="36px" rounded="rounded-xl" />
            <SkeletonBox width="50%" height="16px" rounded="rounded-md" />
        </div>
        <div class="grid gap-5 sm:grid-cols-2 lg:grid-cols-1">
            <div v-for="n in 2" :key="n" class="rounded-2xl border border-[#e4edf7] bg-white p-4 space-y-3">
                <SkeletonImage height="160px" rounded="rounded-xl" />
                <SkeletonBox width="90px" height="14px" rounded="rounded-md" />
                <SkeletonBox width="80%" height="18px" rounded="rounded-md" />
            </div>
        </div>
    </div>
</template>
