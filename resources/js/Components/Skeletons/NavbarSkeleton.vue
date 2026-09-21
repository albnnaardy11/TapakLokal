<script setup>
import SkeletonBox from './Base/SkeletonBox.vue';
</script>

<template>
    <div class="h-[58px] lg:h-[102px]" aria-hidden="true">
        <header class="fixed inset-x-0 top-0 z-30 bg-white shadow-[0_1px_0_rgba(15,44,92,0.08)]">
            <!-- Top Tier Header (58px) -->
            <div class="mx-auto flex h-[58px] max-w-[1180px] items-center px-5 sm:px-8">
                <div class="shrink-0">
                    <span class="text-[15px] font-extrabold tracking-[-0.07em] text-slate-900">tapa<span class="text-[#3E7BEF]">k</span>lokal</span>
                </div>

                <div class="ml-auto hidden items-center gap-1 text-[11px] font-semibold text-slate-700 xl:flex">
                    <div class="flex h-8 items-center gap-1.5 rounded-lg px-2">
                        <SkeletonBox width="20px" height="14px" rounded="rounded-xs" />
                        <SkeletonBox width="48px" height="12px" rounded="rounded-xs" />
                    </div>
                    <span class="mx-1 h-5 w-px bg-slate-200"></span>
                    <div class="flex h-8 items-center gap-1.5 rounded-lg px-2">
                        <SkeletonBox width="14px" height="14px" rounded="rounded-full" />
                        <SkeletonBox width="40px" height="12px" rounded="rounded-xs" />
                    </div>
                    <div class="flex h-8 items-center gap-1.5 rounded-lg px-2">
                        <SkeletonBox width="14px" height="14px" rounded="rounded-full" />
                        <SkeletonBox width="40px" height="12px" rounded="rounded-xs" />
                    </div>
                    <div class="flex h-8 items-center gap-1.5 rounded-lg px-2">
                        <SkeletonBox width="14px" height="14px" rounded="rounded-full" />
                        <SkeletonBox width="48px" height="12px" rounded="rounded-xs" />
                    </div>
                    <div class="flex h-8 items-center gap-1.5 rounded-lg px-2">
                        <SkeletonBox width="14px" height="14px" rounded="rounded-full" />
                        <SkeletonBox width="56px" height="12px" rounded="rounded-xs" />
                    </div>
                    <span class="mx-1 h-5 w-px bg-slate-200"></span>
                    <SkeletonBox width="56px" height="28px" rounded="rounded-lg" class="ml-2" />
                    <SkeletonBox width="56px" height="28px" rounded="rounded-lg" class="ml-1" />
                </div>

                <div class="ml-auto flex items-center gap-2 xl:hidden">
                    <SkeletonBox width="48px" height="28px" rounded="rounded-lg" />
                    <SkeletonBox width="28px" height="28px" rounded="rounded-lg" />
                </div>
            </div>

            <!-- Bottom Tier Blue Nav Bar (44px) -->
            <nav class="hidden border-t border-slate-100 bg-[#3E7BEF] lg:block">
                <div class="mx-auto flex h-11 max-w-[1180px] items-center px-5 sm:px-8">
                    <div class="flex h-full items-center gap-2">
                        <div class="h-7 w-16 rounded-lg bg-white/20 skeleton-shimmer"></div>
                        <div class="h-7 w-16 rounded-lg bg-white/10 skeleton-shimmer"></div>
                        <div class="h-7 w-16 rounded-lg bg-white/10 skeleton-shimmer"></div>
                        <div class="h-7 w-20 rounded-lg bg-white/10 skeleton-shimmer"></div>
                        <div class="h-7 w-14 rounded-lg bg-white/10 skeleton-shimmer"></div>
                        <div class="h-7 w-28 rounded-lg bg-white/10 skeleton-shimmer"></div>
                    </div>
                    <div class="ml-auto w-52">
                        <div class="h-7 w-full rounded-full bg-white/90 skeleton-shimmer"></div>
                    </div>
                </div>
            </nav>
        </header>
    </div>
</template>
