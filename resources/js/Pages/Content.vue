<script setup>
import { Head, Link } from '@inertiajs/vue3';
import MainNavigation from '../Components/Shared/MainNavigation.vue';
defineProps({ content: Object });
</script>
<template><Head :title="content.title"><meta name="description" :content="content.excerpt || content.title" /></Head><MainNavigation /><article class="mx-auto max-w-3xl px-5 py-12"><Link href="/" class="text-xs text-blue-600">← Beranda</Link><p class="mt-8 text-xs uppercase tracking-widest text-blue-500">TAPAK LOKAL</p><h1 class="mt-3 text-3xl font-bold leading-tight text-[#17345e]">{{ content.title }}</h1><p v-if="content.excerpt" class="mt-5 text-lg leading-8 text-slate-500">{{ content.excerpt }}</p><img v-if="content.image_url" :src="content.image_url" :alt="content.title" class="mt-7 aspect-video w-full rounded-2xl object-cover" /><div class="mt-8 whitespace-pre-wrap text-base leading-8 text-slate-600">{{ content.body }}</div></article></template>

