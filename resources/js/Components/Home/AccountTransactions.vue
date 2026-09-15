<script setup>
import { Clock3, CreditCard, QrCode, Search, Store, X } from 'lucide-vue-next';
import { computed, ref } from 'vue';

const query = ref('');
const order = ref('Semua Pesanan');
const date = ref('');
const category = ref('Semua');
const dialog = ref(null);
const selected = ref(null);
const action = ref('');
const transactions = [
    { id: 'AP082114607882', method: 'Offline Store', provider: 'Alfamart', amount: 'Rp 1.102.888', category: 'Open Trip', date: '2026-07-30', deadline: '31 Jul, 09:34', name: 'Open Trip Bali' },
    { id: 'X3171820106138', method: 'Metode Pembayaran', provider: 'QRIS', amount: 'Rp 702.888', category: 'Open PO', date: '2026-07-30', deadline: '31 Jul, 09:34', name: 'Oleh-oleh lokal' },
];
const results = computed(() => transactions.filter((item) => (category.value === 'Semua' || item.category === category.value) && (order.value === 'Semua Pesanan' || item.category === order.value) && (! date.value || item.date === date.value) && `${item.id} ${item.provider} ${item.name}`.toLowerCase().includes(query.value.trim().toLowerCase())));
const reset = () => { query.value = ''; order.value = 'Semua Pesanan'; date.value = ''; category.value = 'Semua'; };
const show = (item, mode) => { selected.value = item; action.value = mode; dialog.value.showModal(); };
</script>

<template>
    <section aria-labelledby="transactions-heading">
        <h2 id="transactions-heading" class="text-xl font-extrabold tracking-tight text-[#202e45]">Daftar Transaksi</h2>
        <p class="mt-1.5 text-xs leading-5 text-slate-500">Lihat rincian pesanan dan kelola pembayaran perjalananmu.</p>

        <div class="mt-5 rounded-2xl border border-[#e1e8f3] bg-white p-4 sm:p-5">
            <div class="grid gap-3 sm:grid-cols-2 xl:grid-cols-[1.4fr_1fr_1fr]">
                <label class="flex flex-col gap-2 text-[11px] font-semibold text-slate-500"><span>Cari transaksi</span><span class="flex h-10 items-center gap-2 rounded-lg border border-[#dce5f0] px-3 transition focus-within:border-[#078cff] focus-within:ring-2 focus-within:ring-sky-100"><Search class="size-4 shrink-0 text-[#078cff]" aria-hidden="true" /><input v-model="query" type="search" placeholder="Nama pesanan atau kode" class="min-w-0 flex-1 bg-transparent text-xs font-normal text-slate-700 outline-none" /></span></label>
                <label class="flex flex-col gap-2 text-[11px] font-semibold text-slate-500"><span>Jenis pesanan</span><select v-model="order" class="h-10 min-w-0 rounded-lg border border-[#dce5f0] bg-white px-3 text-xs font-normal text-slate-700 outline-[#078cff]"><option>Semua Pesanan</option><option>Open Trip</option><option>Open PO</option></select></label>
                <label class="flex flex-col gap-2 text-[11px] font-semibold text-slate-500"><span>Tanggal pembayaran</span><input v-model="date" type="date" class="h-10 min-w-0 rounded-lg border border-[#dce5f0] bg-white px-3 text-xs font-normal text-slate-700 outline-[#078cff]" /></label>
            </div>
            <div class="mt-4 flex flex-wrap items-center gap-2 border-t border-slate-100 pt-4"><button v-for="item in ['Semua', 'Open Trip', 'Open PO', 'Pengembalian Uang']" :key="item" type="button" class="rounded-full border px-3 py-2 text-[11px] font-medium transition duration-200 hover:border-[#078cff] focus-visible:outline-2 focus-visible:outline-[#078cff]" :class="category === item ? 'border-[#b0d7ff] bg-[#edf6ff] text-[#078cff]' : 'border-[#e2e8f0] bg-white text-slate-500'" :aria-pressed="category === item" @click="category = item">{{ item }}</button><button type="button" class="ml-auto px-2 py-2 text-[11px] font-semibold text-[#078cff] hover:underline" @click="reset">Reset filter</button></div>
        </div>

        <div class="mb-3 mt-6 flex items-center gap-2"><h3 class="text-sm font-bold text-[#303c53]">{{ category === 'Pengembalian Uang' ? 'Pengembalian Uang' : 'Menunggu Pembayaran' }}</h3><span class="rounded-full bg-[#e9f1fc] px-2 py-0.5 text-[10px] font-semibold text-[#1045a5]">{{ results.length }}</span></div>
        <div class="space-y-4">
            <article v-for="item in results" :key="item.id" class="overflow-hidden rounded-2xl border border-[#dfe7f2] bg-white transition duration-200 hover:border-[#bdd7f5] hover:shadow-[0_5px_20px_rgba(23,75,120,0.05)]">
                <div class="flex flex-wrap items-center justify-between gap-3 border-b border-[#edf2f8] px-5 py-4"><div class="flex items-center gap-3"><span class="grid size-9 shrink-0 place-items-center rounded-xl bg-[#edf5ff] text-[#1045a5]"><CreditCard class="size-[18px]" aria-hidden="true" /></span><div><h4 class="text-sm font-bold text-[#233b55]">{{ item.name }}</h4><p class="mt-0.5 text-[10px] text-slate-400">{{ item.category }} · Menunggu pembayaran</p></div></div><span class="inline-flex items-center gap-1.5 rounded-full bg-amber-50 px-2.5 py-1.5 text-[10px] font-medium text-amber-700"><Clock3 class="size-3" aria-hidden="true" />Bayar sebelum {{ item.deadline }}</span></div>
                <div class="grid gap-5 px-5 py-5 sm:grid-cols-[1fr_1fr_auto]">
                    <div><p class="mb-2 text-[10px] text-slate-400">Metode pembayaran</p><div class="flex items-center gap-2.5"><span class="grid size-9 shrink-0 place-items-center rounded-lg border border-slate-100 text-[#1045a5]"><QrCode v-if="item.provider === 'QRIS'" class="size-5" /><Store v-else class="size-5" /></span><div><p class="text-xs font-bold text-[#233b55]">{{ item.provider }}</p><p class="mt-0.5 text-[10px] text-slate-400">{{ item.provider === 'QRIS' ? 'Pembayaran QR' : 'Gerai retail' }}</p></div></div></div>
                    <div><p class="mb-2 text-[10px] text-slate-400">{{ item.provider === 'QRIS' ? 'NMID' : 'Kode pembayaran' }}</p><p class="break-all font-mono text-xs font-medium leading-9 text-[#34475a]">{{ item.id }}</p></div>
                    <div class="sm:text-right"><p class="mb-2 text-[10px] text-slate-400">Total pembayaran</p><p class="text-base font-extrabold leading-9 tabular-nums text-[#1045a5]">{{ item.amount }}</p></div>
                </div>
                <div class="flex flex-wrap justify-end gap-2 border-t border-[#edf2f8] bg-[#fbfcfe] px-5 py-3"><button type="button" class="min-h-9 rounded-lg border border-[#d3e1f3] bg-white px-4 text-[11px] font-semibold text-[#1045a5] transition hover:border-[#078cff] hover:bg-sky-50 focus-visible:outline-2 focus-visible:outline-[#078cff]" @click="show(item, 'instructions')">{{ item.provider === 'QRIS' ? 'Lihat QRIS' : 'Lihat cara bayar' }}</button><button type="button" class="min-h-9 rounded-lg bg-[#1045a5] px-4 text-[11px] font-semibold text-white transition hover:bg-[#083788] focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-[#078cff]" @click="show(item, 'status')">Cek status bayar</button></div>
            </article>
            <div v-if="! results.length" class="rounded-2xl border border-dashed border-[#dce5f0] bg-white px-5 py-10 text-center"><Search class="mx-auto mb-3 size-7 text-sky-300" /><p class="text-sm font-semibold text-slate-600">Tidak ada transaksi ditemukan</p><p class="mt-2 text-xs text-slate-400">Coba kata kunci lain atau atur ulang filter pencarian.</p><button class="mt-4 text-xs font-semibold text-[#078cff] hover:underline" @click="reset">Reset filter</button></div>
        </div>
        <p class="mt-3 text-[9px] text-slate-400">Data pembayaran contoh untuk pratinjau tampilan.</p>
        <dialog ref="dialog" class="fixed inset-0 m-auto w-[calc(100%-2rem)] max-w-md rounded-2xl bg-white p-6 text-slate-700 backdrop:bg-slate-900/50" aria-labelledby="payment-detail-heading"><template v-if="selected"><div class="flex justify-between gap-4"><h2 id="payment-detail-heading" class="text-base font-bold">{{ action === 'status' ? 'Status Pembayaran' : `Pembayaran ${selected.provider}` }}</h2><button autofocus aria-label="Tutup rincian pembayaran" @click="dialog.close()"><X class="size-5" /></button></div><p class="mt-4 text-sm">{{ selected.name }} · {{ selected.amount }}</p><p class="mt-2 text-xs text-slate-500">{{ selected.id }}</p><p class="mt-5 rounded-xl bg-sky-50 p-4 text-sm leading-6">{{ action === 'status' ? 'Menunggu pembayaran. Status ini adalah data contoh dan belum terhubung ke penyedia pembayaran.' : selected.provider === 'QRIS' ? 'Kode QR pembayaran belum tersedia karena integrasi QRIS belum diaktifkan.' : 'Panduan dan kode pembayaran asli akan tersedia setelah integrasi penyedia pembayaran diaktifkan. Jangan gunakan kode contoh ini untuk membayar.' }}</p></template></dialog>
    </section>
</template>
