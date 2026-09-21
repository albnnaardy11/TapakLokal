<script setup>
import NavbarSkeleton from './NavbarSkeleton.vue';
import SkeletonBox from './Base/SkeletonBox.vue';
import SkeletonText from './Base/SkeletonText.vue';
</script>

<template>
    <div class="min-h-screen bg-[#f8fafc] font-sans text-slate-900" aria-hidden="true">
        <!-- Reusable 2-tier Navbar Skeleton -->
        <NavbarSkeleton />

        <main class="mx-auto max-w-[1240px] px-4 sm:px-6 lg:px-8 py-8 sm:py-12 space-y-10">
            <!-- Title & Breadcrumb -->
            <div class="space-y-3">
                <SkeletonBox width="190px" height="16px" rounded="rounded-md" />
                <SkeletonBox width="70%" height="44px" rounded="rounded-xl" />
                <div class="flex gap-4">
                    <SkeletonBox width="110px" height="20px" rounded="rounded-md" />
                    <SkeletonBox width="96px" height="20px" rounded="rounded-md" />
                </div>
            </div>

            <!-- Image Gallery (Grid of 3 images) -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 h-[380px] sm:h-[460px]">
                <div class="md:col-span-2 h-full rounded-2xl skeleton-shimmer"></div>
                <div class="hidden md:grid grid-rows-2 gap-4 h-full">
                    <div class="rounded-2xl skeleton-shimmer"></div>
                    <div class="rounded-2xl skeleton-shimmer"></div>
                </div>
            </div>

            <!-- 2 Columns: Details & Booking Card -->
            <div class="grid grid-cols-1 lg:grid-cols-[1fr_380px] gap-8">
                <div class="space-y-8">
                    <div class="rounded-2xl border border-slate-200/80 bg-white p-6 space-y-4">
                        <SkeletonBox width="190px" height="28px" rounded="rounded-lg" />
                        <SkeletonText :lines="3" size="sm" lastLineWidth="80%" />
                    </div>
                    <div class="rounded-2xl border border-slate-200/80 bg-white p-6 space-y-4">
                        <SkeletonBox width="160px" height="28px" rounded="rounded-lg" />
                        <div class="space-y-3">
                            <SkeletonBox v-for="n in 4" :key="n" height="48px" rounded="rounded-xl" />
                        </div>
                    </div>
                </div>

                <!-- Booking Card -->
                <div class="rounded-2xl border border-slate-200/80 bg-white p-6 space-y-5 h-fit shadow-xs">
                    <SkeletonBox width="140px" height="32px" rounded="rounded-lg" />
                    <div class="space-y-3">
                        <SkeletonBox height="44px" rounded="rounded-xl" />
                        <SkeletonBox height="44px" rounded="rounded-xl" />
                        <SkeletonBox height="48px" rounded="rounded-full" class="mt-2" />
                    </div>
                </div>
            </div>
        </main>
    </div>
</template>
