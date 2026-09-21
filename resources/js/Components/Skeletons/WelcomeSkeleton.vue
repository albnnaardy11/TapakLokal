<script setup>
import NavbarSkeleton from './NavbarSkeleton.vue';
import SkeletonBox from './Base/SkeletonBox.vue';
import SkeletonText from './Base/SkeletonText.vue';
import DestinationCardSkeleton from './Cards/DestinationCardSkeleton.vue';
import TripOptionSkeleton from './Cards/TripOptionSkeleton.vue';
import BlogCardSkeleton from './Cards/BlogCardSkeleton.vue';
import ReviewCardSkeleton from './Cards/ReviewCardSkeleton.vue';
</script>

<template>
    <div class="min-h-screen bg-[#f8fafc] font-sans text-slate-900" aria-hidden="true">
        <!-- Reusable 2-tier Navbar Skeleton -->
        <NavbarSkeleton />

        <main class="mx-auto max-w-[1440px] px-5 pb-28 pt-10 sm:px-10 lg:px-12 lg:pt-16 space-y-20 sm:space-y-24">
            <!-- 1. Hero Section + Floating TripFinder -->
            <section class="mx-auto max-w-[1180px] space-y-6">
                <div class="relative flex min-h-[360px] sm:min-h-[470px] lg:min-h-[500px] items-center justify-center rounded-[24px] bg-slate-200/90 p-8 text-center skeleton-shimmer">
                    <div class="max-w-2xl space-y-4">
                        <div class="h-12 sm:h-16 w-3/4 mx-auto rounded-2xl bg-white/50"></div>
                        <div class="h-4 sm:h-5 w-1/2 mx-auto rounded-md bg-white/50"></div>
                    </div>
                </div>

                <!-- TripFinder Floating Bar -->
                <div class="mx-auto max-w-4xl -mt-12 sm:-mt-16 rounded-2xl bg-white p-4 sm:p-6 shadow-md border border-slate-200/80">
                    <div class="grid grid-cols-1 sm:grid-cols-4 gap-3">
                        <SkeletonBox height="48px" rounded="rounded-xl" />
                        <SkeletonBox height="48px" rounded="rounded-xl" />
                        <SkeletonBox height="48px" rounded="rounded-xl" />
                        <SkeletonBox height="48px" rounded="rounded-xl" />
                    </div>
                </div>
            </section>

            <!-- 2. Keunggulan TapakLokal (3 Value Proposition Cards) -->
            <section class="mx-auto max-w-[1180px]">
                <div class="grid grid-cols-1 sm:grid-cols-3 rounded-2xl border border-[#dce9f7] bg-white p-4 gap-4 shadow-xs">
                    <div v-for="n in 3" :key="n" class="flex items-center gap-3 px-3 py-2">
                        <SkeletonBox width="40px" height="40px" rounded="rounded-xl" />
                        <div class="space-y-1.5 flex-1">
                            <SkeletonBox width="70%" height="14px" rounded="rounded-md" />
                            <SkeletonBox width="50%" height="11px" rounded="rounded-md" />
                        </div>
                    </div>
                </div>
            </section>

            <!-- 3. Trip Options (Open vs Private) -->
            <section class="mx-auto max-w-[1180px] space-y-4">
                <div class="space-y-2">
                    <SkeletonBox width="90px" height="14px" rounded="rounded-md" />
                    <SkeletonBox width="280px" height="28px" rounded="rounded-xl" />
                </div>
                <TripOptionSkeleton :count="2" />
            </section>

            <!-- 4. Destination Explore (Grid Layout) -->
            <section class="mx-auto max-w-[1180px] space-y-4">
                <SkeletonBox width="240px" height="24px" rounded="rounded-md" />
                <DestinationCardSkeleton variant="section" />
            </section>

            <!-- 5. Partner Trips (Open Trip Dari Partner Terpercaya) -->
            <section class="mx-auto max-w-[1180px]">
                <div class="relative overflow-hidden rounded-2xl bg-[#19375f] p-6 lg:p-8">
                    <div class="grid grid-cols-1 lg:grid-cols-[44.5%_1fr] gap-6 items-center">
                        <div class="space-y-4 text-white">
                            <div class="h-7 w-3/4 rounded-lg bg-white/40 skeleton-shimmer"></div>
                            <div class="space-y-2 pt-1">
                                <div class="h-4 w-full rounded-md bg-white/30 skeleton-shimmer"></div>
                                <div class="h-4 w-4/5 rounded-md bg-white/30 skeleton-shimmer"></div>
                            </div>
                            <div class="h-12 w-44 rounded-md bg-white/30 skeleton-shimmer mt-4"></div>
                            <div class="h-9 w-48 rounded-md bg-white/20 skeleton-shimmer mt-3"></div>
                        </div>
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3.5">
                            <div v-for="n in 3" :key="n" class="rounded-xl bg-white p-3 space-y-2.5 shadow-sm">
                                <SkeletonBox height="110px" rounded="rounded-lg" />
                                <SkeletonBox width="75%" height="13px" rounded="rounded-md" />
                                <SkeletonBox width="50%" height="10px" rounded="rounded-md" />
                                <SkeletonBox width="60px" height="14px" rounded="rounded-md" class="pt-1" />
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <!-- 6. Promo Banner 17an -->
            <section class="mx-auto max-w-[1180px]">
                <SkeletonBox height="160px" rounded="rounded-2xl" />
            </section>

            <!-- 7. Destination Gallery -->
            <section class="mx-auto max-w-[1180px] space-y-6">
                <div class="space-y-2">
                    <SkeletonBox width="130px" height="16px" rounded="rounded-full" />
                    <SkeletonBox width="260px" height="28px" rounded="rounded-xl" />
                </div>
                <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-3">
                    <SkeletonBox v-for="n in 6" :key="n" height="128px" rounded="rounded-xl" />
                </div>
            </section>

            <!-- 8. Why Choose Us -->
            <section class="mx-auto max-w-[1180px] space-y-6">
                <div class="space-y-2 text-center max-w-md mx-auto">
                    <SkeletonBox width="120px" height="14px" rounded="rounded-full" class="mx-auto" />
                    <SkeletonBox width="220px" height="28px" rounded="rounded-xl" class="mx-auto" />
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-6">
                    <div v-for="n in 3" :key="n" class="rounded-2xl border border-slate-200/80 bg-white p-6 space-y-4">
                        <SkeletonBox width="48px" height="48px" rounded="rounded-2xl" />
                        <SkeletonBox width="75%" height="18px" rounded="rounded-md" />
                        <SkeletonText :lines="2" size="sm" lastLineWidth="70%" />
                    </div>
                </div>
            </section>

            <!-- 9. Travel Blog -->
            <section class="mx-auto max-w-[1180px] space-y-6">
                <div class="space-y-2">
                    <SkeletonBox width="140px" height="14px" rounded="rounded-full" />
                    <SkeletonBox width="240px" height="28px" rounded="rounded-xl" />
                </div>
                <BlogCardSkeleton :count="3" layout="grid" />
            </section>

            <!-- 10. Reviews -->
            <section class="mx-auto max-w-[1180px] space-y-4">
                <SkeletonBox width="220px" height="24px" rounded="rounded-md" />
                <ReviewCardSkeleton :count="3" />
            </section>
        </main>
    </div>
</template>
