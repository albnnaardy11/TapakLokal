<script setup>
import SkeletonCircle from '../Base/SkeletonCircle.vue';
import SkeletonBox from '../Base/SkeletonBox.vue';

defineProps({
    count: {
        type: Number,
        default: 2,
    },
});
</script>

<template>
    <div class="grid gap-5 md:grid-cols-2" aria-hidden="true">
        <div
            v-for="n in count"
            :key="n"
            class="min-h-[240px] sm:min-h-[270px] rounded-2xl p-6 bg-slate-800/80 skeleton-shimmer relative overflow-hidden"
        >
            <div class="flex h-full max-w-[290px] flex-col justify-end space-y-3 relative z-10">
                <SkeletonCircle size="40px" />
                <SkeletonBox width="60%" height="22px" rounded="rounded-md" />
                <SkeletonBox width="90%" height="14px" rounded="rounded-md" />
                <SkeletonBox width="130px" height="38px" rounded="rounded-full" class="mt-2" />
            </div>
        </div>
    </div>
</template>
