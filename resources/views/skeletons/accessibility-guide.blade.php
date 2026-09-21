<div class="min-h-screen bg-[#f8fafc] font-sans">
    @include('skeletons.navbar')

    <main class="mx-auto max-w-[1200px] px-4 sm:px-6 lg:px-8 pb-20 sm:pb-28 pt-6 sm:pt-8 lg:pt-12 space-y-12 sm:space-y-16">
        <!-- Hero Section Skeleton (1:1 with 2-column Hero design) -->
        <section class="rounded-[28px] sm:rounded-[36px] lg:rounded-[40px] border border-[#dce8f8] bg-[#f8fbff] p-6 sm:p-9 md:p-11 lg:p-12 xl:p-14">
            <div class="grid grid-cols-1 items-center gap-8 sm:gap-10 lg:grid-cols-[1.1fr_1fr] lg:gap-8 xl:gap-12">
                <div class="space-y-5">
                    <div class="space-y-3">
                        <div class="h-9 sm:h-12 w-4/5 rounded-xl skeleton-shimmer"></div>
                        <div class="h-9 sm:h-12 w-3/5 rounded-xl skeleton-shimmer"></div>
                    </div>
                    <div class="space-y-2 pt-2">
                        <div class="h-4 w-full rounded-md skeleton-shimmer"></div>
                        <div class="h-4 w-11/12 rounded-md skeleton-shimmer"></div>
                        <div class="h-4 w-4/5 rounded-md skeleton-shimmer"></div>
                    </div>
                    <div class="flex flex-col sm:flex-row gap-3 pt-3">
                        <div class="h-12 w-full sm:w-40 rounded-full skeleton-shimmer"></div>
                        <div class="h-12 w-full sm:w-48 rounded-full skeleton-shimmer"></div>
                    </div>
                </div>
                <div class="flex items-center justify-center lg:justify-end">
                    <div class="h-64 sm:h-80 w-full max-w-[480px] rounded-3xl skeleton-shimmer"></div>
                </div>
            </div>
        </section>

        <!-- Section 1 Skeleton: Tiga Langkah -->
        <section class="rounded-[24px] sm:rounded-[28px] border border-[#d8eafb] bg-[#edf5fe] p-5 sm:p-8 lg:p-10">
            <div class="grid gap-6 sm:gap-8 lg:grid-cols-[1fr_2.4fr] lg:items-center">
                <div class="space-y-3">
                    <div class="h-3.5 w-32 rounded-full skeleton-shimmer"></div>
                    <div class="h-8 w-48 rounded-lg skeleton-shimmer"></div>
                    <div class="h-4 w-full rounded-md skeleton-shimmer"></div>
                </div>
                <div class="grid gap-4 grid-cols-1 sm:grid-cols-3 sm:gap-5">
                    @for ($i = 0; $i < 3; $i++)
                        <div class="rounded-[20px] sm:rounded-[24px] border border-[#e2eaf4] bg-white p-5 sm:p-6 space-y-3 text-center min-h-[190px] flex flex-col items-center justify-center">
                            <div class="size-12 rounded-2xl skeleton-shimmer"></div>
                            <div class="h-4 w-28 rounded-md skeleton-shimmer"></div>
                            <div class="h-3 w-36 rounded-md skeleton-shimmer"></div>
                        </div>
                    @endfor
                </div>
            </div>
        </section>

        <!-- Section 2 Skeleton: 12 Fitur Aksesibilitas (4-columns) -->
        <section class="space-y-6">
            <div class="space-y-2">
                <div class="h-3.5 w-36 rounded-full skeleton-shimmer"></div>
                <div class="h-8 w-56 rounded-lg skeleton-shimmer"></div>
                <div class="h-4 w-2/3 rounded-md skeleton-shimmer"></div>
            </div>
            <div class="grid gap-3.5 sm:gap-4 lg:gap-5 grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
                @for ($i = 0; $i < 12; $i++)
                    <div class="rounded-[20px] sm:rounded-[22px] border border-[#e4edf7] bg-white p-5 space-y-4">
                        <div class="size-10 rounded-2xl skeleton-shimmer"></div>
                        <div class="h-4 w-24 rounded-md skeleton-shimmer"></div>
                        <div class="h-3 w-full rounded-md skeleton-shimmer"></div>
                        <div class="h-7 w-full rounded-xl skeleton-shimmer"></div>
                    </div>
                @endfor
            </div>
        </section>

        <!-- Section 3 Skeleton: Navigasi Keyboard -->
        <section class="rounded-[24px] sm:rounded-[28px] border border-[#d8eafb] bg-[#edf5fe] p-6 lg:p-8">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 items-center">
                <div class="space-y-4">
                    <div class="h-9 w-64 rounded-xl skeleton-shimmer"></div>
                    <div class="h-4 w-5/6 rounded-md skeleton-shimmer"></div>
                </div>
                <div class="rounded-[20px] sm:rounded-[22px] bg-white p-5 sm:p-6 space-y-3">
                    @for ($i = 0; $i < 5; $i++)
                        <div class="flex items-center gap-3 py-1.5">
                            <div class="h-6 w-16 rounded-full skeleton-shimmer"></div>
                            <div class="h-4 flex-1 rounded-md skeleton-shimmer"></div>
                        </div>
                    @endfor
                </div>
            </div>
        </section>

        <!-- Section 4 Skeleton: 3 Control Cards -->
        <section class="space-y-6">
            <div class="space-y-2">
                <div class="h-3.5 w-32 rounded-full skeleton-shimmer"></div>
                <div class="h-7 w-60 rounded-lg skeleton-shimmer"></div>
            </div>
            <div class="grid gap-4 sm:gap-5 grid-cols-1 md:grid-cols-3">
                @for ($i = 0; $i < 3; $i++)
                    <div class="rounded-[20px] sm:rounded-[24px] border border-[#e2eaf4] bg-white p-6 space-y-4">
                        <div class="flex justify-between items-center">
                            <div class="size-11 rounded-2xl skeleton-shimmer"></div>
                            <div class="h-5 w-16 rounded-full skeleton-shimmer"></div>
                        </div>
                        <div class="h-5 w-32 rounded-md skeleton-shimmer"></div>
                        <div class="h-3.5 w-full rounded-md skeleton-shimmer"></div>
                    </div>
                @endfor
            </div>
        </section>

        <!-- Section 5 Skeleton: Bottom CTA Banner -->
        <section class="rounded-[24px] sm:rounded-[28px] bg-slate-900/10 p-6 sm:p-10 lg:p-14">
            <div class="space-y-4 max-w-xl">
                <div class="h-3.5 w-28 rounded-full skeleton-shimmer"></div>
                <div class="h-9 w-4/5 rounded-xl skeleton-shimmer"></div>
                <div class="h-4 w-full rounded-md skeleton-shimmer"></div>
                <div class="h-11 w-44 rounded-full skeleton-shimmer pt-2"></div>
            </div>
        </section>
    </main>
</div>
