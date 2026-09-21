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

        <main class="mx-auto max-w-[1440px] px-5 pb-28 pt-10 sm:px-10 lg:px-12 lg:pt-16 space-y-20 sm:space-y-24">
            <!-- 1. Hero Section + Floating TripFinder -->
            <section class="mx-auto max-w-[1180px] space-y-6">
                <div class="relative flex min-h-[360px] sm:min-h-[470px] lg:min-h-[500px] items-center justify-center rounded-[24px] bg-slate-200/80 p-8 text-center skeleton-shimmer">
                    <div class="max-w-2xl space-y-4">
                        <div class="h-12 sm:h-16 w-3/4 mx-auto rounded-2xl bg-white/50"></div>
                        <div class="h-4 sm:h-5 w-1/2 mx-auto rounded-md bg-white/50"></div>
                    </div>
                </div>

                <!-- TripFinder Floating Bar -->
                <div class="mx-auto max-w-4xl -mt-12 sm:-mt-16 rounded-2xl bg-white p-4 sm:p-6 shadow-md border border-slate-200/80">
                    <div class="grid grid-cols-1 sm:grid-cols-4 gap-3">
                        <div class="h-12 rounded-xl skeleton-shimmer"></div>
                        <div class="h-12 rounded-xl skeleton-shimmer"></div>
                        <div class="h-12 rounded-xl skeleton-shimmer"></div>
                        <div class="h-12 rounded-xl skeleton-shimmer"></div>
                    </div>
                </div>
            </section>

            <!-- 2. Keunggulan TapakLokal (3 Value Proposition Cards) -->
            <section class="mx-auto max-w-[1180px]">
                <div class="grid grid-cols-1 sm:grid-cols-3 rounded-2xl border border-[#dce9f7] bg-white p-4 gap-4 shadow-xs">
                    <div v-for="n in 3" :key="n" class="flex items-center gap-3 px-3 py-2">
                        <div class="size-10 rounded-xl skeleton-shimmer"></div>
                        <div class="space-y-1.5 flex-1">
                            <div class="h-4 w-3/4 rounded-md skeleton-shimmer"></div>
                            <div class="h-3 w-1/2 rounded-md skeleton-shimmer"></div>
                        </div>
                    </div>
                </div>
            </section>

            <!-- 3. Trip Options Tabs -->
            <section class="mx-auto max-w-[1180px] flex gap-3 overflow-x-auto py-2">
                <div v-for="n in 6" :key="n" class="h-10 w-28 shrink-0 rounded-full skeleton-shimmer"></div>
            </section>

            <!-- 4. Destination Explore (4 Grid Cards) -->
            <section class="mx-auto max-w-[1180px] space-y-6">
                <div class="flex justify-between items-end">
                    <div class="space-y-2">
                        <div class="h-4 w-28 rounded-full skeleton-shimmer"></div>
                        <div class="h-8 w-60 rounded-xl skeleton-shimmer"></div>
                    </div>
                    <div class="h-4 w-20 rounded-md skeleton-shimmer"></div>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
                    <div v-for="n in 4" :key="n" class="rounded-2xl border border-slate-200/80 bg-white p-4 space-y-4 shadow-xs">
                        <div class="h-48 w-full rounded-xl skeleton-shimmer"></div>
                        <div class="space-y-2">
                            <div class="h-4 w-3/4 rounded-md skeleton-shimmer"></div>
                            <div class="h-3 w-1/2 rounded-md skeleton-shimmer"></div>
                        </div>
                    </div>
                </div>
            </section>

            <!-- 5. Partner Trips (Open Trip Dari Partner Terpercaya - Exact 1:1 Dark Blue Container) -->
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
                                <div class="h-28 w-full rounded-lg skeleton-shimmer"></div>
                                <div class="h-3.5 w-3/4 rounded-md skeleton-shimmer"></div>
                                <div class="h-3 w-1/2 rounded-md skeleton-shimmer"></div>
                                <div class="h-4 w-20 rounded-md skeleton-shimmer pt-1"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <!-- 6. Promo Banner 17an -->
            <section class="mx-auto max-w-[1180px]">
                <div class="h-36 sm:h-44 w-full rounded-2xl border border-sky-100 skeleton-shimmer"></div>
            </section>

            <!-- 7. Destination Gallery -->
            <section class="mx-auto max-w-[1180px] space-y-6">
                <div class="space-y-2">
                    <div class="h-4 w-32 rounded-full skeleton-shimmer"></div>
                    <div class="h-8 w-64 rounded-xl skeleton-shimmer"></div>
                </div>
                <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-3">
                    <div v-for="n in 6" :key="n" class="h-32 rounded-xl skeleton-shimmer"></div>
                </div>
            </section>

            <!-- 8. Why Choose Us -->
            <section class="mx-auto max-w-[1180px] space-y-6">
                <div class="space-y-2 text-center max-w-md mx-auto">
                    <div class="h-4 w-28 mx-auto rounded-full skeleton-shimmer"></div>
                    <div class="h-8 w-56 mx-auto rounded-xl skeleton-shimmer"></div>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-6">
                    <div v-for="n in 3" :key="n" class="rounded-2xl border border-slate-200/80 bg-white p-6 space-y-4">
                        <div class="size-12 rounded-2xl skeleton-shimmer"></div>
                        <div class="h-5 w-3/4 rounded-md skeleton-shimmer"></div>
                        <div class="h-3.5 w-full rounded-md skeleton-shimmer"></div>
                    </div>
                </div>
            </section>

            <!-- 9. Booking Steps -->
            <section class="mx-auto max-w-[1180px] rounded-2xl bg-white border border-[#dce9f7] p-8 space-y-6">
                <div class="h-7 w-52 rounded-lg skeleton-shimmer"></div>
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-6">
                    <div v-for="n in 3" :key="n" class="flex items-center gap-4">
                        <div class="size-10 rounded-full skeleton-shimmer shrink-0"></div>
                        <div class="space-y-2 flex-1">
                            <div class="h-4 w-3/4 rounded-md skeleton-shimmer"></div>
                            <div class="h-3 w-1/2 rounded-md skeleton-shimmer"></div>
                        </div>
                    </div>
                </div>
            </section>

            <!-- 10. Travel Blog -->
            <section class="mx-auto max-w-[1180px] space-y-6">
                <div class="space-y-2">
                    <div class="h-4 w-32 rounded-full skeleton-shimmer"></div>
                    <div class="h-8 w-56 rounded-xl skeleton-shimmer"></div>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                    <div v-for="n in 3" :key="n" class="rounded-2xl border border-slate-200/80 bg-white p-5 space-y-4">
                        <div class="h-48 w-full rounded-xl skeleton-shimmer"></div>
                        <div class="space-y-2">
                            <div class="h-4 w-24 rounded-full skeleton-shimmer"></div>
                            <div class="h-5 w-4/5 rounded-md skeleton-shimmer"></div>
                        </div>
                    </div>
                </div>
            </section>

            <!-- 11. FAQ & Reviews -->
            <section class="mx-auto max-w-[1180px] grid grid-cols-1 lg:grid-cols-2 gap-8">
                <div class="rounded-2xl border border-slate-200/80 bg-white p-6 space-y-4">
                    <div class="h-6 w-40 rounded-lg skeleton-shimmer"></div>
                    <div v-for="n in 4" :key="n" class="h-12 w-full rounded-xl skeleton-shimmer"></div>
                </div>
                <div class="rounded-2xl border border-slate-200/80 bg-white p-6 space-y-4">
                    <div class="h-6 w-40 rounded-lg skeleton-shimmer"></div>
                    <div class="space-y-3">
                        <div v-for="n in 2" :key="n" class="p-4 rounded-xl border border-slate-100 space-y-2">
                            <div class="h-4 w-32 rounded-md skeleton-shimmer"></div>
                            <div class="h-3.5 w-full rounded-md skeleton-shimmer"></div>
                        </div>
                    </div>
                </div>
            </section>
        </main>
    </div>
</template>
