<script setup>
import TripCardSkeleton from '../Skeletons/Cards/TripCardSkeleton.vue';
import BlogCardSkeleton from '../Skeletons/Cards/BlogCardSkeleton.vue';
import SkeletonBox from '../Skeletons/Base/SkeletonBox.vue';
import SkeletonText from '../Skeletons/Base/SkeletonText.vue';

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
    <div v-if="variant === 'card' || variant === 'trip'">
        <TripCardSkeleton :count="count" layout="grid" />
    </div>

    <div v-else-if="variant === 'article'">
        <BlogCardSkeleton :count="count" layout="grid" />
    </div>

    <div v-else-if="variant === 'hero'" class="rounded-[28px] sm:rounded-[36px] border border-slate-200/80 bg-white p-6 sm:p-10 lg:p-12 shadow-xs">
        <div class="grid grid-cols-1 gap-8 lg:grid-cols-2 lg:items-center">
            <div class="space-y-4 sm:space-y-5">
                <SkeletonBox width="130px" height="18px" rounded="rounded-full" />
                <div class="space-y-2.5">
                    <SkeletonBox width="75%" height="36px" rounded="rounded-xl" />
                    <SkeletonBox width="50%" height="36px" rounded="rounded-xl" />
                </div>
                <div class="space-y-2 pt-2">
                    <SkeletonText :lines="2" size="sm" lastLineWidth="80%" />
                </div>
                <div class="flex gap-3 pt-3">
                    <SkeletonBox width="140px" height="44px" rounded="rounded-full" />
                    <SkeletonBox width="160px" height="44px" rounded="rounded-full" />
                </div>
            </div>
            <div class="flex items-center justify-center">
                <div class="h-56 sm:h-72 w-full max-w-[480px] rounded-2xl sm:rounded-3xl skeleton-shimmer"></div>
            </div>
        </div>
    </div>
</template>
