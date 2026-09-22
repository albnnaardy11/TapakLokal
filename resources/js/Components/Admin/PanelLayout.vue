<script setup>
import { computed, ref } from 'vue';
import { Head, Link, usePage } from '@inertiajs/vue3';
import { route } from 'ziggy-js';
import { ArrowUpRight, ChevronDown, Compass, LayoutDashboard, LogOut, Menu, ShieldCheck, X } from 'lucide-vue-next';
const props = defineProps({ title: String, subtitle: String, navigation: { type: Array, default: () => [] }, vendor: Boolean });
const page = usePage();
const open = ref(false);
const groups = computed(() => Object.groupBy(props.navigation, item => item.group));
const initials = computed(() => page.props.auth?.user?.name?.slice(0, 2).toUpperCase() || 'TL');
</script>

<template>
    <Head :title="title" />
    <div class="min-h-screen bg-[#f6f8fc] text-[#22344d]">
        <button v-if="open" class="fixed inset-0 z-40 bg-slate-950/30 lg:hidden" aria-label="Tutup navigasi" @click="open = false"></button>
        <aside class="fixed inset-y-0 left-0 z-50 flex w-64 flex-col border-r border-slate-200/70 bg-white transition-transform lg:translate-x-0" :class="open ? 'translate-x-0' : '-translate-x-full'">
            <Link :href="route(vendor ? 'vendor.dashboard' : 'admin.dashboard')" class="flex items-center gap-2.5 px-6 py-7">
                <span class="grid size-9 place-items-center rounded-xl bg-[#3e7bef] text-white"><Compass class="size-5" /></span>
                <span class="text-lg font-extrabold tracking-tight">tapak<span class="text-[#3e7bef]">lokal</span><span class="mt-0.5 block text-[9px] font-semibold uppercase tracking-[0.18em] text-slate-400">{{ vendor ? 'Partner workspace' : 'Admin backoffice' }}</span></span>
            </Link>
            <nav class="flex-1 overflow-y-auto px-3 pb-5" aria-label="Navigasi panel">
                <Link :href="route(vendor ? 'vendor.dashboard' : 'admin.dashboard')" class="mb-5 flex items-center gap-3 rounded-xl px-3 py-3 text-sm font-semibold" :class="page.url === (vendor ? '/vendor' : '/admin') ? 'bg-blue-50 text-blue-600' : 'text-slate-500 hover:bg-slate-50'"><LayoutDashboard class="size-4" />Overview</Link>
                <details v-for="(items, group) in groups" :key="group" open class="mb-4">
                    <summary class="flex cursor-pointer list-none items-center justify-between px-3 py-2 text-[10px] font-bold uppercase tracking-widest text-slate-400">{{ group }}<ChevronDown class="size-3" /></summary>
                    <Link v-for="item in items" :key="item.key" :href="item.url" class="my-0.5 flex min-h-10 items-center rounded-lg px-3 text-xs font-medium transition-colors" :class="page.url.split('?')[0].startsWith(item.url.replace(/^https?:\/\/[^/]+/, '')) ? 'bg-[#3e7bef] text-white' : 'text-slate-600 hover:bg-blue-50 hover:text-blue-600'" @click="open = false">{{ item.label }}</Link>
                </details>
            </nav>
            <div class="border-t border-slate-100 p-4">
                <div class="mb-3 flex items-center gap-2.5"><span class="grid size-9 place-items-center rounded-full bg-blue-50 text-xs font-bold text-blue-600">{{ initials }}</span><div class="min-w-0"><p class="truncate text-xs font-semibold">{{ page.props.auth?.user?.name }}</p><p class="truncate text-[10px] text-slate-400">{{ page.props.auth?.user?.email }}</p></div></div>
                <Link :href="route('logout')" method="post" as="button" class="flex w-full items-center gap-2 rounded-lg px-2 py-2 text-xs text-slate-500 hover:bg-rose-50 hover:text-rose-600"><LogOut class="size-3.5" />Keluar</Link>
            </div>
        </aside>
        <div class="lg:pl-64">
            <header class="sticky top-0 z-30 flex h-[72px] items-center justify-between border-b border-slate-200/60 bg-white/95 px-5 backdrop-blur-sm lg:px-9">
                <div class="flex items-center gap-3"><button class="rounded-lg p-2 lg:hidden" aria-label="Buka navigasi" @click="open = true"><Menu class="size-5" /></button><span class="hidden text-xs text-slate-400 sm:inline">{{ vendor ? 'Partner' : 'Workspace' }} <span class="mx-3 text-slate-300">/</span><span class="font-medium text-slate-700">{{ title }}</span></span></div>
                <Link href="/" class="flex items-center gap-2 rounded-lg border border-slate-200 px-3 py-2 text-xs font-semibold text-slate-600 hover:bg-slate-50">Lihat website<ArrowUpRight class="size-3.5" /></Link>
            </header>
            <main class="mx-auto max-w-[1440px] px-5 py-7 lg:px-9 lg:py-9">
                <div class="mb-7 flex flex-wrap items-end justify-between gap-4"><div><p class="mb-2 text-[10px] font-bold uppercase tracking-[0.18em] text-blue-500">TAPAK LOKAL · {{ vendor ? 'PARTNER' : 'OPERATIONS' }}</p><h1 class="text-2xl font-bold tracking-tight text-[#17345e]">{{ title }}</h1><p v-if="subtitle" class="mt-2 max-w-2xl text-sm leading-6 text-slate-500">{{ subtitle }}</p></div><slot name="actions" /></div>
                <div v-if="page.props.flash?.success" role="status" class="mb-5 rounded-xl border border-emerald-100 bg-emerald-50 px-4 py-3 text-sm text-emerald-800">{{ page.props.flash.success }}</div>
                <div v-if="page.props.flash?.error || Object.keys(page.props.errors || {}).length" role="alert" class="mb-5 rounded-xl border border-rose-100 bg-rose-50 px-4 py-3 text-sm text-rose-700"><p v-if="page.props.flash?.error">{{ page.props.flash.error }}</p><p v-for="(error, key) in page.props.errors" :key="key">{{ error }}</p></div>
                <slot />
            </main>
        </div>
    </div>
</template>

