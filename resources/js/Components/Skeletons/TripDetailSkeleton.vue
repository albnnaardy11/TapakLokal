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

        <main class="mx-auto max-w-[1240px] px-4 sm:px-6 lg:px-8 py-8 sm:py-12 space-y-10">
            <!-- Title & Breadcrumb -->
            <div class="space-y-3">
                <div class="h-4 w-48 rounded-md skeleton-shimmer"></div>
                <div class="h-9 sm:h-12 w-3/4 rounded-xl skeleton-shimmer"></div>
                <div class="flex gap-4">
                    <div class="h-5 w-28 rounded-md skeleton-shimmer"></div>
                    <div class="h-5 w-24 rounded-md skeleton-shimmer"></div>
                </div>
            </div>

            <!-- Image Gallery (Grid of 3-4 images) -->
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
                        <div class="h-7 w-48 rounded-lg skeleton-shimmer"></div>
                        <div class="space-y-2">
                            <div class="h-4 w-full rounded-md skeleton-shimmer"></div>
                            <div class="h-4 w-5/6 rounded-md skeleton-shimmer"></div>
                            <div class="h-4 w-4/5 rounded-md skeleton-shimmer"></div>
                        </div>
                    </div>
                    <div class="rounded-2xl border border-slate-200/80 bg-white p-6 space-y-4">
                        <div class="h-7 w-40 rounded-lg skeleton-shimmer"></div>
                        <div class="space-y-3">
                            <div v-for="n in 4" :key="n" class="h-12 w-full rounded-xl skeleton-shimmer"></div>
                        </div>
                    </div>
                </div>

                <!-- Booking Card -->
                <div class="rounded-2xl border border-slate-200/80 bg-white p-6 space-y-5 h-fit shadow-sm">
                    <div class="h-8 w-36 rounded-lg skeleton-shimmer"></div>
                    <div class="space-y-3">
                        <div class="h-11 w-full rounded-xl skeleton-shimmer"></div>
                        <div class="h-11 w-full rounded-xl skeleton-shimmer"></div>
                        <div class="h-12 w-full rounded-full skeleton-shimmer"></div>
                    </div>
                </div>
            </div>
        </main>
    </div>
</template>
