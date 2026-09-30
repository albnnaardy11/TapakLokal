<script setup>
import { Link } from '@inertiajs/vue3';
import { route } from 'ziggy-js';
import { ArrowUpRight, Compass, MapPin } from 'lucide-vue-next';

defineProps({
    items: { type: Array, default: () => [] },
    blog: Boolean,
});
</script>

<template>
    <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
        <article
            v-for="item in items"
            :key="item.id"
            class="group flex flex-col overflow-hidden rounded-2xl border border-[#e4edf7] bg-white shadow-sm transition-all duration-300 hover:-translate-y-1 hover:border-[#badeff] hover:shadow-[0_12px_24px_rgba(23,75,120,0.08)]"
        >
            <Link
                :href="blog ? route('blog.show', item.slug) : route('content.show', item.slug)"
                class="flex h-full flex-col focus-visible:outline-2 focus-visible:outline-[#1677e8]"
            >
                <div class="relative aspect-[16/10] overflow-hidden bg-sky-50">
                    <img
                        v-if="item.image_url"
                        :src="item.image_url"
                        :alt="item.title"
                        loading="lazy"
                        class="size-full object-cover transition-transform duration-500 group-hover:scale-105"
                    />
                    <div class="absolute inset-0 bg-gradient-to-t from-black/30 via-transparent to-transparent opacity-0 transition-opacity group-hover:opacity-100"></div>
                    <span
                        v-if="item.category"
                        class="absolute left-3 top-3 inline-flex items-center gap-1 rounded-full bg-white/95 px-2.5 py-0.5 text-[11px] font-bold text-[#1677e8] shadow-sm backdrop-blur-sm"
                    >
                        <Compass class="size-3" />
                        {{ item.category }}
                    </span>
                </div>

                <div class="flex flex-1 flex-col p-5">
                    <h3 class="text-base font-bold text-[#17375f] transition-colors group-hover:text-[#1677e8] sm:text-lg">
                        {{ item.title }}
                    </h3>
                    <p class="mt-2 line-clamp-2 text-xs leading-relaxed text-slate-500 sm:text-sm">
                        {{ item.excerpt }}
                    </p>
                    <div class="mt-auto pt-4">
                        <span class="inline-flex items-center gap-1.5 text-xs font-bold text-[#1677e8] group-hover:underline">
                            <span>Jelajahi</span>
                            <ArrowUpRight class="size-3.5 transition-transform group-hover:translate-x-0.5 group-hover:-translate-y-0.5" />
                        </span>
                    </div>
                </div>
            </Link>
        </article>
    </div>
</template>
