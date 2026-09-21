<div class="min-h-screen bg-[#f8fafc] font-sans text-slate-900">
    @include('skeletons.navbar')

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
                    @for ($i = 0; $i < 5; $i++)
                        <div class="h-10 w-full rounded-xl skeleton-shimmer"></div>
                    @endfor
                </div>
            </div>

            <!-- Main Account Area -->
            <div class="rounded-2xl border border-slate-200/80 bg-white p-6 sm:p-8 space-y-6">
                <div class="h-7 w-48 rounded-lg skeleton-shimmer"></div>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    @for ($i = 0; $i < 4; $i++)
                        <div class="h-12 w-full rounded-xl skeleton-shimmer"></div>
                    @endfor
                </div>
                <div class="h-44 w-full rounded-xl skeleton-shimmer mt-6"></div>
            </div>
        </div>
    </main>
</div>
