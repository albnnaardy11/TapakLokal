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
        default: 'carousel', // 'carousel' | 'grid'
    },
});
</script>

<template>
    <div
        v-if="layout === 'grid'"
        class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-5"
        aria-hidden="true"
    >
        <div
            v-for="n in count"
            :key="n"
            class="flex flex-col overflow-hidden rounded-xl border border-slate-200/80 bg-white p-2.5 shadow-xs space-y-2.5"
        >
            <SkeletonImage aspectRatio="155/105" rounded="rounded-lg" />
            <div class="flex flex-col flex-1 space-y-2 pt-1">
                <SkeletonText :lines="1" size="xs" />
                <SkeletonText :lines="1" size="xs" lastLineWidth="50%" />
                <SkeletonBox width="60px" height="14px" rounded="rounded-md" />
                <div class="mt-auto flex items-center justify-between pt-3 border-t border-slate-100">
                    <SkeletonBox width="54px" height="12px" rounded="rounded-sm" />
                    <SkeletonBox width="36px" height="12px" rounded="rounded-sm" />
                </div>
            </div>
        </div>
    </div>

    <div
        v-else
        class="flex gap-3.5 overflow-hidden"
        aria-hidden="true"
    >
        <div
            v-for="n in count"
            :key="n"
            class="flex shrink-0 flex-col overflow-hidden rounded-xl bg-white p-2.5 shadow-xs w-[180px] lg:w-[calc((100%-28px)/3)] space-y-2.5"
        >
            <SkeletonImage aspectRatio="155/105" rounded="rounded-lg" />
            <div class="flex flex-col flex-1 space-y-2 pt-1">
                <SkeletonBox width="85%" height="13px" rounded="rounded-sm" />
                <SkeletonBox width="60%" height="10px" rounded="rounded-sm" />
                <SkeletonBox width="70px" height="13px" rounded="rounded-sm" class="mt-1" />
                <div class="mt-auto flex items-center justify-between pt-3 border-t border-slate-100">
                    <SkeletonBox width="50px" height="11px" rounded="rounded-sm" />
                    <SkeletonBox width="30px" height="11px" rounded="rounded-sm" />
                </div>
            </div>
        </div>
    </div>
</template>
