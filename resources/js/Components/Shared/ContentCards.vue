<script setup>
import { Link } from '@inertiajs/vue3';
import { route } from 'ziggy-js';
import { ArrowUpRight } from 'lucide-vue-next';
defineProps({ items: { type: Array, default: () => [] }, blog: Boolean });
</script>
<template>
    <div class="grid gap-5 sm:grid-cols-2 lg:grid-cols-3">
        <article v-for="item in items" :key="item.id" class="overflow-hidden rounded-2xl border border-[#e4edf7] bg-white shadow-sm">
            <Link :href="blog ? route('blog.show', item.slug) : route('content.show', item.slug)" class="group block h-full focus-visible:outline-2 focus-visible:outline-blue-500">
                <div class="aspect-[16/10] overflow-hidden bg-sky-50"><img v-if="item.image_url" :src="item.image_url" :alt="item.title" loading="lazy" class="size-full object-cover transition-transform duration-300 motion-safe:group-hover:scale-105" /></div>
                <div class="p-5"><h3 class="text-lg font-bold text-[#17375f] group-hover:text-blue-600">{{ item.title }}</h3><p class="mt-3 line-clamp-3 text-sm leading-6 text-slate-500">{{ item.excerpt }}</p><span class="mt-5 inline-flex items-center gap-2 text-xs font-bold text-blue-600">Baca selengkapnya<ArrowUpRight class="size-4" /></span></div>
            </Link>
        </article>
    </div>
    <p v-if="!items.length" class="rounded-2xl border border-slate-100 bg-white px-6 py-14 text-center text-sm text-slate-500">Belum ada konten yang diterbitkan.</p>
</template>
