<template>
    <div class="min-h-screen bg-[#f8fafc] font-sans text-slate-900">
        <!-- Skeleton Navbar -->
        <div class="h-[58px] lg:h-[70px] border-b border-slate-100 bg-white/95 px-5 sm:px-8">
            <div class="mx-auto flex h-full max-w-[1200px] items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="h-6 w-28 rounded-lg skeleton-shimmer"></div>
                </div>
                <div class="hidden items-center gap-6 lg:flex">
                    <div class="h-4 w-16 rounded-md skeleton-shimmer"></div>
                    <div class="h-4 w-20 rounded-md skeleton-shimmer"></div>
                    <div class="h-4 w-20 rounded-md skeleton-shimmer"></div>
                    <div class="h-4 w-24 rounded-md skeleton-shimmer"></div>
                </div>
                <div class="flex items-center gap-3">
                    <div class="h-8 w-16 rounded-lg skeleton-shimmer"></div>
                    <div class="h-8 w-20 rounded-lg skeleton-shimmer"></div>
                </div>
            </div>
        </div>

        <main class="mx-auto max-w-[1200px] px-4 sm:px-6 lg:px-8 py-8 sm:py-12">
            <div class="grid grid-cols-1 lg:grid-cols-[280px_1fr] gap-8">
                <!-- Sidebar -->
                <div class="rounded-2xl border border-slate-200/80 bg-white p-6 space-y-6 h-fit">
                    <div class="flex items-center gap-4">
                        <div class="size-14 rounded-full skeleton-shimmer"></div>
                        <div class="space-y-2 flex-1">
                            <div class="h-4 w-3/4 rounded-md skeleton-shimmer"></div>
                            <div class="h-3 w-1/2 rounded-md skeleton-shimmer"></div>
                        </div>
                    </div>
                    <div class="space-y-2 pt-4 border-t border-slate-100">
                        <div v-for="n in 5" :key="n" class="h-10 w-full rounded-xl skeleton-shimmer"></div>
                    </div>
                </div>

                <!-- Main Account Area -->
                <div class="rounded-2xl border border-slate-200/80 bg-white p-6 sm:p-8 space-y-6">
                    <div class="h-7 w-48 rounded-lg skeleton-shimmer"></div>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div v-for="n in 4" :key="n" class="h-12 w-full rounded-xl skeleton-shimmer"></div>
                    </div>
                    <div class="h-44 w-full rounded-xl skeleton-shimmer mt-6"></div>
                </div>
            </div>
        </main>
    </div>
</template>
