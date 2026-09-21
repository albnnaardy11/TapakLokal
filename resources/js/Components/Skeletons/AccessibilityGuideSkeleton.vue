<script setup>
import NavbarSkeleton from './NavbarSkeleton.vue';
import SkeletonBox from './Base/SkeletonBox.vue';
import SkeletonText from './Base/SkeletonText.vue';
</script>

<template>
    <div class="min-h-screen bg-[#f8fafc] font-sans text-slate-900" aria-hidden="true">
        <!-- Reusable 2-tier Navbar Skeleton -->
        <NavbarSkeleton />

        <main class="mx-auto max-w-[1180px] px-5 py-8 sm:py-12 space-y-10">
            <!-- Header Skeleton -->
            <div class="space-y-3 text-center max-w-2xl mx-auto">
                <SkeletonBox width="130px" height="16px" rounded="rounded-full" class="mx-auto" />
                <SkeletonBox width="75%" height="44px" rounded="rounded-xl" class="mx-auto" />
                <SkeletonBox width="60%" height="16px" rounded="rounded-md" class="mx-auto" />
            </div>

            <!-- Grid of Guide Features -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <div v-for="n in 6" :key="n" class="rounded-2xl border border-slate-200/80 bg-white p-6 space-y-4 shadow-xs">
                    <SkeletonBox width="48px" height="48px" rounded="rounded-xl" />
                    <SkeletonBox width="70%" height="20px" rounded="rounded-md" />
                    <SkeletonText :lines="2" size="sm" lastLineWidth="80%" />
                </div>
            </div>
        </main>
    </div>
</template>
