<div class="min-h-screen bg-[#f8fafc] font-sans text-[#172c50]">
    @include('skeletons.navbar')

    <main class="mx-auto max-w-[1440px] px-5 pb-24 pt-8 sm:px-10 lg:px-12 lg:pt-12 space-y-12">
        <!-- Featured Articles (1 Big + 2 Side cards) -->
        <section class="mx-auto max-w-[1180px]">
            <div class="grid gap-5 lg:grid-cols-[1.65fr_1fr]">
                <div class="rounded-[24px] bg-slate-200 min-h-[440px] sm:min-h-[510px] p-6 sm:p-9 flex flex-col justify-end space-y-4 skeleton-shimmer">
                    <div class="h-4 w-40 rounded-md bg-white/40"></div>
                    <div class="h-10 w-4/5 rounded-xl bg-white/40"></div>
                    <div class="h-4 w-3/5 rounded-md bg-white/40"></div>
                </div>
                <div class="grid gap-5 sm:grid-cols-2 lg:grid-cols-1">
                    @for ($i = 0; $i < 2; $i++)
                        <div class="rounded-2xl border border-[#e4edf7] bg-white p-4 space-y-3">
                            <div class="h-40 w-full rounded-xl skeleton-shimmer"></div>
                            <div class="h-4 w-28 rounded-md skeleton-shimmer"></div>
                            <div class="h-5 w-4/5 rounded-md skeleton-shimmer"></div>
                        </div>
                    @endfor
                </div>
            </div>
        </section>

        <!-- Travel Blog Articles Grid (6 Cards) -->
        <section class="mx-auto max-w-[1180px] space-y-6">
            <div class="flex justify-between items-end">
                <div class="space-y-2">
                    <div class="h-4 w-32 rounded-full skeleton-shimmer"></div>
                    <div class="h-8 w-60 rounded-xl skeleton-shimmer"></div>
                </div>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                @for ($i = 0; $i < 6; $i++)
                    <div class="rounded-2xl border border-slate-200/80 bg-white p-5 space-y-4">
                        <div class="h-48 w-full rounded-xl skeleton-shimmer"></div>
                        <div class="space-y-2">
                            <div class="h-4 w-24 rounded-full skeleton-shimmer"></div>
                            <div class="h-5 w-full rounded-md skeleton-shimmer"></div>
                            <div class="h-3.5 w-4/5 rounded-md skeleton-shimmer"></div>
                        </div>
                    </div>
                @endfor
            </div>
        </section>
    </main>
</div>
