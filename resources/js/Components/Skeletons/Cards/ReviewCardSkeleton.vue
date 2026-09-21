<script setup>
import SkeletonCircle from '../Base/SkeletonCircle.vue';
import SkeletonBox from '../Base/SkeletonBox.vue';
import SkeletonText from '../Base/SkeletonText.vue';

defineProps({
    count: {
        type: Number,
        default: 3,
    },
});
</script>

<template>
    <div
        class="flex gap-5 overflow-hidden pb-4 pt-2"
        aria-hidden="true"
    >
        <div
            v-for="n in count"
            :key="n"
            class="flex w-[88%] shrink-0 flex-col rounded-2xl border border-[#e6eef7] bg-white p-6 space-y-4 sm:w-[calc((100%-20px)/2)] lg:w-[calc((100%-40px)/3)] shadow-xs"
        >
            <div class="flex justify-between items-center">
                <div class="flex gap-1">
                    <SkeletonBox v-for="s in 5" :key="s" width="14px" height="14px" rounded="rounded-xs" />
                </div>
                <SkeletonCircle size="28px" />
            </div>
            <div class="space-y-2 flex-1">
                <SkeletonBox width="65%" height="18px" rounded="rounded-md" />
                <SkeletonText :lines="3" size="sm" lastLineWidth="80%" />
            </div>
            <div class="flex items-center gap-3 pt-4 border-t border-slate-100">
                <SkeletonCircle size="44px" />
                <div class="space-y-1.5 flex-1">
                    <SkeletonBox width="100px" height="14px" rounded="rounded-md" />
                    <SkeletonBox width="70px" height="11px" rounded="rounded-sm" />
                </div>
            </div>
        </div>
    </div>
</template>
