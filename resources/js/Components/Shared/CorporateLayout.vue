<script setup>
import { Head, Link, router, usePage } from '@inertiajs/vue3';
import { route } from 'ziggy-js';
import { Building2, ArrowLeft, ChevronRight } from 'lucide-vue-next';
defineProps({ title: String, company: Object });
const page = usePage();
</script>
<template>
    <Head :title="title + ' — Corporate'" />
    <div class="min-h-screen bg-[#f7f9fc] text-[#173451]">
        <header class="border-b border-slate-200 bg-white"><div class="mx-auto flex min-h-20 max-w-[1440px] flex-wrap items-center justify-between gap-4 px-5 py-4 lg:px-10"><Link :href="route('business.corporate')" class="flex items-center gap-3"><img src="/Assets/Images/logo.webp" alt="TapakLokal Logo" class="h-8 w-auto object-contain" /><span class="border-l border-slate-200 pl-3 text-[10px] font-bold tracking-[.14em]">FOR<br />CORPORATES</span></Link><div class="flex items-center gap-6 text-sm"><Link :href="route('corporate.dashboard')" class="font-semibold">Perusahaan saya</Link><span class="hidden text-slate-500 sm:block">{{ page.props.auth?.user?.name }}</span><button type="button" class="font-semibold text-slate-600" @click="router.post(route('logout'), {switch_portal:'corporate'})">Keluar</button></div></div></header>
        <main class="mx-auto max-w-[1440px] px-5 py-8 lg:px-10 lg:py-10"><div class="mb-7 flex items-center gap-2 text-xs text-slate-500"><Link :href="route('corporate.dashboard')" class="inline-flex items-center gap-2"><Building2 class="size-4" /> Corporate</Link><ChevronRight class="size-3" /><span>{{ company?.name || title }}</span></div><p v-if="page.props.flash?.success" role="status" class="mb-6 rounded-lg border border-emerald-200 bg-emerald-50 px-5 py-4 text-sm text-emerald-900">{{ page.props.flash.success }}</p><p v-if="page.props.flash?.error" role="alert" class="mb-6 rounded-lg border border-red-200 bg-red-50 px-5 py-4 text-sm text-red-800">{{ page.props.flash.error }}</p><slot /></main>
        <footer class="mx-auto flex max-w-[1440px] items-center justify-between gap-5 border-t border-slate-200 px-5 py-6 text-xs text-slate-500 lg:px-10"><Link :href="route('home')" class="inline-flex items-center gap-2"><ArrowLeft class="size-3" /> Kembali ke TapakLokal</Link><span>Perjalanan tim. Dampak lokal.</span></footer>
    </div>
</template>

