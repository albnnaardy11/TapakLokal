<script setup>
import NavbarSkeleton from './NavbarSkeleton.vue';
import SkeletonBox from './Base/SkeletonBox.vue';
import SkeletonCircle from './Base/SkeletonCircle.vue';
</script>

<template>
    <div class="min-h-screen bg-[#f8fafc] font-sans text-slate-900" aria-hidden="true">
        <!-- Reusable 2-tier Navbar Skeleton -->
        <NavbarSkeleton />

        <main class="mx-auto max-w-[1180px] px-5 py-8 sm:py-12">
            <div class="grid grid-cols-1 lg:grid-cols-[280px_1fr] gap-8">
                <!-- Sidebar Profile Card Skeleton -->
                <div class="rounded-2xl border border-slate-200/80 bg-white p-6 space-y-6 h-fit shadow-xs">
                    <div class="flex flex-col items-center space-y-3 text-center">
                        <SkeletonCircle size="80px" />
                        <SkeletonBox width="130px" height="20px" rounded="rounded-md" />
                        <SkeletonBox width="170px" height="14px" rounded="rounded-md" />
                    </div>
                    <div class="space-y-2 pt-4 border-t border-slate-100">
                        <SkeletonBox v-for="n in 5" :key="n" height="40px" rounded="rounded-xl" />
                    </div>
                </div>

                <!-- Content Area Skeleton -->
                <div class="space-y-6">
                    <div class="rounded-2xl border border-slate-200/80 bg-white p-6 sm:p-8 space-y-6 shadow-xs">
                        <div class="flex justify-between items-center border-b border-slate-100 pb-4">
                            <SkeletonBox width="160px" height="24px" rounded="rounded-lg" />
                            <SkeletonBox width="96px" height="32px" rounded="rounded-lg" />
                        </div>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div v-for="n in 4" :key="n" class="space-y-2">
                                <SkeletonBox width="96px" height="14px" rounded="rounded-md" />
                                <SkeletonBox height="44px" rounded="rounded-xl" />
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>
</template>
