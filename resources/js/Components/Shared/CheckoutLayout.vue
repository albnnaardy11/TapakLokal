<script setup>
import { Head, Link, usePage } from '@inertiajs/vue3';
import { route } from 'ziggy-js';
import { ArrowLeft, Check, LockKeyhole } from 'lucide-vue-next';
const props = defineProps({ title: String, subtitle: String, step: Number, backUrl: String });
const page = usePage();
const steps = ['Detail pesanan', 'Pembayaran', 'Konfirmasi'];
</script>
<template>
    <Head :title="title"><meta name="robots" head-key="robots" content="noindex,nofollow" /></Head>
    <div class="min-h-screen bg-[#f5f7fa] text-[#17345e]">
        <header class="border-b border-slate-200 bg-white"><div class="mx-auto flex min-h-20 max-w-[1180px] flex-wrap items-center justify-between gap-4 px-5 py-4"><Link href="/" class="text-2xl font-black tracking-tight" aria-label="TapakLokal beranda">tapak<span class="text-[#0088ff]">lokal</span></Link><ol class="flex items-center gap-3 text-xs sm:gap-6" aria-label="Tahapan pemesanan"><li v-for="(label,index) in steps" :key="label" class="flex items-center gap-2" :class="step === index + 1 ? 'font-bold text-[#0175ea]' : 'text-slate-500'" :aria-current="step === index + 1 ? 'step' : undefined"><span class="grid size-6 place-items-center rounded-full text-[11px] font-bold" :class="step >= index + 1 ? 'bg-[#0175ea] text-white' : 'bg-slate-100'"><Check v-if="step > index + 1" class="size-3.5" /><template v-else>{{ index + 1 }}</template></span><span class="hidden sm:inline">{{ label }}</span></li></ol></div></header>
        <main class="mx-auto max-w-[1180px] px-5 pb-16 pt-7 sm:pt-10">
            <div class="mb-7 flex flex-wrap items-center justify-between gap-4"><Link :href="backUrl || route('account')" class="inline-flex min-h-11 items-center gap-2 text-sm font-semibold text-slate-600"><ArrowLeft class="size-4" />Kembali</Link><Link :href="route('account')" class="flex items-center gap-2 text-xs text-slate-500"><LockKeyhole class="size-3.5 text-[#0175ea]" /><span>{{ page.props.auth.user.name }}<span class="mt-1 block break-all text-[11px]">{{ page.props.auth.user.email }}</span></span></Link></div>
            <div class="mb-8"><h1 class="text-2xl font-extrabold tracking-tight sm:text-3xl">{{ title }}</h1><p class="mt-2 text-sm leading-6 text-slate-500">{{ subtitle }}</p></div>
            <div class="grid items-start gap-6 lg:grid-cols-[minmax(0,1fr)_350px]"><div class="min-w-0 space-y-6"><slot /></div><aside class="min-w-0 space-y-5 lg:sticky lg:top-6"><slot name="summary" /></aside></div>
            <footer class="mt-10 flex flex-wrap items-center justify-between gap-4 border-t border-slate-200 pt-5 text-xs text-slate-500"><span>TapakLokal · perjalanan dan karya lokal</span><div class="flex gap-5"><Link :href="route('account.section','bookings')">Pesanan saya</Link><Link :href="route('help.index')">Bantuan</Link></div></footer>
        </main>
    </div>
</template>
