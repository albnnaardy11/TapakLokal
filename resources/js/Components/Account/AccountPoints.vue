<script setup>
import { Award, ArrowDownLeft, ArrowUpRight, ArrowRight, Coins, Info, Wallet, CircleCheck, History, RotateCcw, X } from 'lucide-vue-next';
import { computed, ref } from 'vue';
import { Link, router, usePage } from '@inertiajs/vue3';
import { route } from 'ziggy-js';
import Pagination from '../Admin/Pagination.vue';

const page = usePage();
const dialog = ref(null);
const summary = computed(() => page.props.pointSummary || {});
const records = computed(() => page.props.records);
const number = value => new Intl.NumberFormat('id-ID').format(Number(value || 0));
const date = value => new Intl.DateTimeFormat('id-ID', { day: 'numeric', month: 'short', year: 'numeric', timeZone: 'Asia/Jakarta' }).format(new Date(value));
const tabs = [{ key: 'all', label: 'Semua aktivitas' }, { key: 'earned', label: 'Poin masuk' }, { key: 'deducted', label: 'Poin berkurang' }];
const filter = activity => router.get(route('account.section', 'points'), { activity }, { preserveScroll: true, preserveState: true });
const statistics = computed(() => [
    { label: 'Total diperoleh', value: number(summary.value.earned), unit: 'poin', icon: ArrowDownLeft, note: 'Akumulasi poin yang masuk' },
    { label: 'Total pengurangan', value: number(summary.value.deducted), unit: 'poin', icon: ArrowUpRight, note: 'Termasuk penyesuaian refund' },
    { label: 'Anggota sejak', value: summary.value.memberSince || '—', unit: '', icon: Award, note: 'Awal perjalananmu bersama kami' },
]);
</script>

<template>
    <section class="space-y-5 text-[#17345e]" aria-labelledby="points-heading">
        <header class="flex items-center justify-between gap-4">
            <div><h2 id="points-heading" class="text-xl font-extrabold tracking-tight">Points Saya</h2><p class="mt-1 text-xs leading-5 text-slate-500">Setiap perjalanan membawa cerita. Setiap poin punya nilainya.</p></div>
            <span class="hidden rounded-full border border-[#dce6f4] bg-white px-3 py-2 text-[10px] font-bold text-[#3e7bef] sm:inline-flex">TAPAK REWARDS</span>
        </header>

        <div class="grid overflow-hidden rounded-2xl border border-[#dce6f4] shadow-sm md:grid-cols-[1.1fr_1fr]">
            <div class="relative isolate overflow-hidden bg-linear-to-br from-[#17345e] via-[#1768ce] to-[#0099ef] p-6 text-white sm:p-7">
                <div aria-hidden="true" class="pointer-events-none absolute -right-16 -top-16 -z-10 size-64 rounded-full border-[35px] border-white/10"></div>
                <div aria-hidden="true" class="pointer-events-none absolute -bottom-24 right-12 -z-10 size-56 rounded-full border border-white/20"></div>
                <div class="flex items-baseline gap-2"><p class="text-5xl font-extrabold tracking-tight tabular-nums">{{ number(page.props.pointBalance) }}</p><span class="text-sm text-blue-100">poin</span></div>
                <p class="mt-2 text-xs text-blue-100">Saldo poin kamu saat ini</p>
                <button type="button" class="mt-6 inline-flex min-h-10 items-center gap-2 rounded-lg border border-white/40 bg-white/10 px-4 text-xs font-semibold transition-colors hover:bg-white/20 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-white" @click="dialog.showModal()"><Info class="size-4" />Tentang Points</button>
            </div>
            <div class="flex flex-col items-start justify-center bg-[#edf5ff] p-6 sm:p-7">
                <h3 class="text-lg font-extrabold leading-snug">Jelajahi lebih jauh,<br />kumpulkan lebih banyak.</h3>
                <p class="mt-3 max-w-xs text-xs leading-6 text-slate-500">Selesaikan perjalananmu dan dapatkan poin yang tercatat otomatis di akunmu.</p>
                <Link :href="route('points.guide')" class="mt-5 inline-flex min-h-10 items-center gap-3 rounded-lg bg-[#3e7bef] px-4 text-xs font-bold text-white transition-colors hover:bg-[#2866d4]">Pelajari lebih lanjut <ArrowRight class="size-4" /></Link>
            </div>
        </div>

        <div class="grid gap-3 sm:grid-cols-3">
            <div v-for="stat in statistics" :key="stat.label" class="rounded-2xl border border-[#dce6f4] bg-white p-5 shadow-xs">
                <div class="flex items-center gap-2"><span class="grid size-8 shrink-0 place-items-center rounded-lg bg-[#edf5ff] text-[#3e7bef]"><component :is="stat.icon" class="size-4" /></span><p class="text-xs font-medium text-slate-500">{{ stat.label }}</p></div>
                <p class="mt-4 text-2xl font-extrabold tabular-nums">{{ stat.value }} <span class="text-xs font-normal text-slate-400">{{ stat.unit }}</span></p>
                <p class="mt-1 text-[10px] leading-5 text-slate-500">{{ stat.note }}</p>
            </div>
        </div>

        <section class="overflow-hidden rounded-2xl border border-[#dce6f4] bg-white shadow-xs" aria-labelledby="points-history-title">
            <header class="flex flex-wrap items-center justify-between gap-2 px-5 pt-5"><h3 id="points-history-title" class="text-sm font-bold">Riwayat Points</h3><span class="text-[11px] text-slate-400">{{ number(records?.total) }} aktivitas tercatat</span></header>
            <nav aria-label="Filter riwayat poin" class="mt-4 flex gap-2 overflow-x-auto border-b border-[#e8eef7] px-5 pb-4">
                <button v-for="tab in tabs" :key="tab.key" type="button" :aria-pressed="page.props.pointFilter === tab.key" class="min-h-10 shrink-0 rounded-lg border px-4 text-xs font-semibold transition-colors focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-[#3e7bef]" :class="page.props.pointFilter === tab.key ? 'border-[#3e7bef] bg-[#edf4ff] text-[#3e7bef]' : 'border-[#e1e9f3] text-slate-500 hover:border-[#3e7bef] hover:bg-[#f5f8ff]'" @click="filter(tab.key)">{{ tab.label }}</button>
            </nav>
            <div v-if="records?.data?.length" class="divide-y divide-[#edf1f7]">
                <article v-for="entry in records.data" :key="entry.id" class="flex items-start gap-3 p-5 transition-colors hover:bg-[#f8fbff] sm:gap-4">
                    <span class="grid size-10 shrink-0 place-items-center rounded-xl" :class="entry.points > 0 ? 'bg-[#edf5ff] text-[#3e7bef]' : 'bg-orange-50 text-orange-600'"><component :is="entry.points > 0 ? ArrowDownLeft : ArrowUpRight" class="size-5" /></span>
                    <div class="min-w-0 flex-1">
                        <div class="flex flex-wrap items-center justify-between gap-2"><span class="text-[10px] font-semibold" :class="entry.points > 0 ? 'text-[#3e7bef]' : 'text-orange-600'">{{ entry.points > 0 ? 'Poin diperoleh' : 'Pengurangan poin' }}</span><time :datetime="entry.created_at" class="text-[10px] text-slate-400">{{ date(entry.created_at) }}</time></div>
                        <div class="mt-2 flex flex-wrap items-start justify-between gap-2"><h4 class="text-xs font-bold leading-6">{{ entry.description || 'Penyesuaian poin' }}</h4><p class="shrink-0 text-base font-extrabold tabular-nums" :class="entry.points > 0 ? 'text-[#3e7bef]' : 'text-orange-600'">{{ entry.points > 0 ? '+' : '' }}{{ number(entry.points) }} <span class="text-[10px] font-normal">poin</span></p></div>
                        <p class="mt-1 break-all text-[10px] text-slate-400">Referensi: {{ entry.reference }}</p>
                        <Link v-if="entry.booking_id" :href="route('bookings.show', entry.booking_id)" class="mt-3 inline-flex items-center gap-1 text-[11px] font-semibold text-[#3e7bef] hover:underline">Lihat perjalanan <ArrowRight class="size-3" /></Link>
                    </div>
                </article>
            </div>
            <div v-else class="flex flex-col items-center px-6 py-12 text-center">
                <span class="grid size-16 place-items-center rounded-2xl bg-[#edf5ff] text-[#3e7bef]"><Coins class="size-8" /></span>
                <h4 class="mt-4 text-sm font-bold">{{ page.props.pointFilter === 'all' ? 'Perjalanan berikutnya, poin pertamamu' : 'Belum ada aktivitas di kategori ini' }}</h4>
                <p class="mt-2 max-w-sm text-xs leading-6 text-slate-500">Poin dari perjalanan yang selesai akan tercatat di sini. Semua perubahan saldo dapat kamu pantau melalui riwayat ini.</p>
            </div>
            <div v-if="records?.last_page > 1" class="border-t border-[#edf1f7] p-4"><Pagination :records="records" /></div>
        </section>
        <p class="flex items-start gap-2 text-[11px] leading-5 text-slate-500"><Info class="mt-0.5 size-4 shrink-0 text-[#3e7bef]" />Saldo mengikuti aktivitas poin yang tercatat. Pengembalian dana dapat mengurangi poin yang sebelumnya diperoleh.</p>
        <dialog ref="dialog" aria-labelledby="points-info-title" aria-describedby="points-info-description" class="fixed inset-0 m-auto max-h-[90dvh] w-[calc(100%-2rem)] max-w-lg overflow-hidden rounded-2xl border border-[#dce6f4] bg-white p-0 text-[#17345e] shadow-2xl backdrop:bg-slate-900/50 backdrop:backdrop-blur-sm" @click.self="dialog.close()">
            <div class="flex max-h-[90dvh] flex-col">
                <header class="flex shrink-0 items-center justify-between gap-4 border-b border-[#edf1f7] px-5 py-4 sm:px-7">
                    <h2 id="points-info-title" class="text-base font-extrabold">Tentang Tapak Points</h2>
                    <button type="button" autofocus aria-label="Tutup informasi poin" class="grid size-9 shrink-0 place-items-center rounded-full text-slate-400 transition-colors hover:bg-[#edf5ff] hover:text-[#3e7bef] focus-visible:outline-2 focus-visible:outline-[#3e7bef]" @click="dialog.close()"><X class="size-5" /></button>
                </header>
                <div class="min-h-0 overflow-y-auto overscroll-contain px-5 pb-6 sm:px-7">
                    <div aria-hidden="true" class="relative mx-auto my-6 flex h-32 w-48 items-center justify-center">
                        <div class="absolute size-32 rounded-full bg-[#edf5ff]"></div>
                        <div class="absolute bottom-0 h-3 w-32 rounded-[50%] bg-[#dcecff]"></div>
                        <div class="relative grid h-20 w-24 -rotate-12 place-items-center rounded-2xl border border-[#6ba6ff] bg-linear-to-br from-[#0099ef] to-[#2866d4] text-white shadow-lg shadow-blue-200/70"><Wallet class="size-12" :stroke-width="1.5" /></div>
                        <span class="absolute right-7 top-0 grid size-11 rotate-12 place-items-center rounded-full border-4 border-amber-100 bg-amber-300 text-amber-700 shadow-sm"><Coins class="size-6" /></span>
                        <span class="absolute left-5 top-4 grid size-8 -rotate-12 place-items-center rounded-full border-2 border-amber-100 bg-amber-300 text-amber-700"><Coins class="size-5" /></span>
                    </div>
                    <div class="text-center"><h3 class="text-xl font-extrabold tracking-tight">Perjalananmu punya nilai lebih.</h3><p id="points-info-description" class="mx-auto mt-3 max-w-sm text-xs leading-6 text-slate-500">Tapak Points adalah apresiasi untuk setiap perjalanan yang kamu selesaikan bersama TapakLokal.</p></div>
                    <ul class="mt-6 space-y-4">
                        <li class="flex items-start gap-3"><span class="grid size-10 shrink-0 place-items-center rounded-xl bg-[#edf5ff] text-[#3e7bef]"><CircleCheck class="size-5" /></span><div><h4 class="text-xs font-bold leading-5">Selesaikan perjalanan, dapatkan poin</h4><p class="mt-1 text-xs leading-5 text-slate-500">Poin masuk otomatis setelah status perjalanan selesai.</p></div></li>
                        <li class="flex items-start gap-3"><span class="grid size-10 shrink-0 place-items-center rounded-xl bg-[#edf5ff] text-[#3e7bef]"><Coins class="size-5" /></span><div><h4 class="text-xs font-bold leading-5">Setiap Rp10.000 menghasilkan 1 poin</h4><p class="mt-1 text-xs leading-5 text-slate-500">Dihitung dari total pesanan dan dibulatkan ke bawah. Contoh: Rp250.000 menghasilkan 25 poin.</p></div></li>
                        <li class="flex items-start gap-3"><span class="grid size-10 shrink-0 place-items-center rounded-xl bg-[#edf5ff] text-[#3e7bef]"><History class="size-5" /></span><div><h4 class="text-xs font-bold leading-5">Semua aktivitas tercatat</h4><p class="mt-1 text-xs leading-5 text-slate-500">Lihat perolehan dan perubahan saldo kapan saja di Riwayat Points.</p></div></li>
                        <li class="flex items-start gap-3"><span class="grid size-10 shrink-0 place-items-center rounded-xl bg-[#edf5ff] text-[#3e7bef]"><RotateCcw class="size-5" /></span><div><h4 class="text-xs font-bold leading-5">Saldo menyesuaikan pengembalian dana</h4><p class="mt-1 text-xs leading-5 text-slate-500">Poin dari pesanan yang mendapat refund dapat ditarik kembali.</p></div></li>
                    </ul>
                    <div class="mt-6 flex items-start gap-2.5 rounded-xl border border-[#dce6f4] bg-[#f5f9ff] p-3.5"><Info class="mt-0.5 size-4 shrink-0 text-[#3e7bef]" /><p class="text-[11px] leading-5 text-slate-500">Penukaran dan masa kedaluwarsa poin belum tersedia saat ini.</p></div>
                </div>
                <footer class="shrink-0 border-t border-[#edf1f7] bg-white px-5 py-4 sm:px-7"><button type="button" class="min-h-11 w-full rounded-lg bg-[#3e7bef] px-5 text-sm font-bold text-white transition-colors hover:bg-[#2866d4] focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-[#3e7bef]" @click="dialog.close()">Mengerti</button></footer>
            </div>
        </dialog>
    </section>
</template>
