<script setup>
import { Link } from '@inertiajs/vue3';
import PanelLayout from '../../Components/Admin/PanelLayout.vue';
import { ArrowUpRight, CheckCircle2, Clock3 } from 'lucide-vue-next';
defineProps({ navigation: Array, stats: Array, alerts: Array, activity: Array, gatewayReady: Boolean });
const money = value => new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', maximumFractionDigits: 0 }).format(value);
</script>
<template>
    <PanelLayout title="Overview" subtitle="Pantau aktivitas platform dan prioritaskan pekerjaan yang membutuhkan perhatian." :navigation="navigation">
        <div class="mb-7 grid gap-4 sm:grid-cols-2 xl:grid-cols-4"><article v-for="stat in stats" :key="stat.label" class="rounded-2xl border border-slate-200/70 bg-white p-5"><p class="text-xs font-medium text-slate-500">{{ stat.label }}</p><p class="mt-4 text-3xl font-bold tracking-tight text-[#17345e]">{{ stat.money ? money(stat.value) : stat.value }}</p><p class="mt-3 text-[11px] text-slate-400">{{ stat.hint }}</p></article></div>
        <div class="grid gap-6 xl:grid-cols-[1.2fr_1fr]">
            <section class="panel-surface"><div class="border-b border-slate-100 p-5"><h2 class="text-sm font-bold">Perlu perhatian</h2><p class="mt-1 text-xs text-slate-400">Antrean kerja sesuai akses kamu</p></div><Link v-for="alert in alerts" :key="alert.label" :href="alert.url" class="flex items-center gap-3 border-b border-slate-50 px-5 py-4 last:border-0 hover:bg-blue-50/40"><span class="grid size-9 place-items-center rounded-xl" :class="alert.count ? 'bg-amber-50 text-amber-600' : 'bg-emerald-50 text-emerald-600'"><Clock3 v-if="alert.count" class="size-4" /><CheckCircle2 v-else class="size-4" /></span><span class="flex-1 text-sm font-medium">{{ alert.label }}</span><span class="rounded-lg bg-slate-100 px-2 py-1 text-xs font-semibold">{{ alert.count }}</span><ArrowUpRight class="size-4 text-slate-400" /></Link><p v-if="!alerts.length" class="p-8 text-center text-sm text-slate-400">Tidak ada antrean untuk peran ini.</p></section>
            <section class="panel-surface"><div class="border-b border-slate-100 p-5"><h2 class="text-sm font-bold">Aktivitas terbaru</h2><p class="mt-1 text-xs text-slate-400">Jejak tindakan pada platform</p></div><div v-for="item in activity" :key="item.id" class="flex items-start gap-3 px-5 py-4"><span class="mt-1.5 size-2 rounded-full bg-blue-400"></span><div><p class="text-xs font-semibold">{{ item.action }}</p><p class="mt-1 text-xs text-slate-400">{{ item.user?.name || 'Sistem' }} · {{ new Date(item.created_at).toLocaleString('id-ID') }}</p></div></div><p v-if="!activity.length" class="p-8 text-center text-sm text-slate-400">Belum ada aktivitas yang dapat ditampilkan.</p></section>
        </div>
        <div v-if="!gatewayReady" class="mt-6 rounded-xl border border-amber-100 bg-amber-50 px-5 py-4 text-xs leading-6 text-amber-800">Pembayaran online belum aktif. Pesanan tetap dapat dicatat, tetapi pembayaran dan pencairan tidak akan ditandai berhasil tanpa konfirmasi gateway.</div>
    </PanelLayout>
</template>

