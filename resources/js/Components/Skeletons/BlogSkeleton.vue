<script setup>
import NavbarSkeleton from './NavbarSkeleton.vue';
import BlogCardSkeleton from './Cards/BlogCardSkeleton.vue';
import SkeletonBox from './Base/SkeletonBox.vue';
</script>

<template>
    <div class="min-h-screen bg-[#f8fafc] font-sans text-[#172c50]" aria-hidden="true">
        <!-- Reusable 2-tier Navbar Skeleton -->
        <NavbarSkeleton />

        <main class="mx-auto max-w-[1440px] px-5 pb-24 pt-8 sm:px-10 lg:px-12 lg:pt-12 space-y-12">
            <!-- Featured Articles (1 Big + 2 Side cards) -->
            <section class="mx-auto max-w-[1180px]">
                <BlogCardSkeleton layout="featured" />
            </section>

            <!-- Travel Blog Articles Grid (6 Cards) -->
            <section class="mx-auto max-w-[1180px] space-y-6">
                <div class="flex justify-between items-end">
                    <div class="space-y-2">
                        <SkeletonBox width="130px" height="16px" rounded="rounded-full" />
                        <SkeletonBox width="240px" height="32px" rounded="rounded-xl" />
                    </div>
                </div>
                <BlogCardSkeleton :count="6" layout="grid" />
            </section>
        </main>
    </div>
</template>
