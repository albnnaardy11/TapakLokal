<script setup>
import { Award, Coins, Gift, Hourglass, Sparkles, X } from 'lucide-vue-next';
import { computed, ref } from 'vue';
import AccountExploreBanner from './AccountExploreBanner.vue';

const activeTab = ref('Aktif');
const dialog = ref(null);
const dialogTitle = ref('');
const dialogText = ref('');
const history = [
    { id: 'PT-006', status: 'Aktif', type: 'redeem', title: 'Tukar Poin — Voucher Diskon 20% Open Trip', date: '28 Jul 2026', amount: -800, description: 'Penukaran poin untuk voucher perjalanan Open Trip.', voucher: 'Diskon 20% Open Trip', code: 'OT-DEMO-20', voucherStatus: 'Voucher Aktif' },
    { id: 'PT-005', status: 'Aktif', type: 'earn', title: 'Poin Diperoleh — Open Trip Curug Nangka Bogor', date: '20 Jul 2026', amount: 390, description: 'Bonus poin dari perjalanan yang telah selesai.' },
    { id: 'PT-004', status: 'Aktif', type: 'redeem', title: 'Tukar Poin — Voucher Diskon 15% Kuliner Hidden Gem', date: '15 Jul 2026', amount: -300, description: 'Penukaran poin untuk menikmati kuliner lokal.', voucher: 'Diskon 15% Kuliner Hidden Gem', code: 'FOOD-DEMO-15', voucherStatus: 'Voucher Aktif' },
    { id: 'PT-003', status: 'Aktif', type: 'earn', title: 'Poin Diperoleh — Private Trip Raja Ampat', date: '10 Jul 2026', amount: 810, description: 'Bonus poin dari perjalanan yang telah selesai.', expiring: 10 },
    { id: 'PT-002', status: 'Tertunda', type: 'earn', title: 'Poin Perjalanan — Open Trip Bromo', date: '02 Agu 2026', amount: 150, description: 'Poin menunggu penyelesaian perjalanan dan belum masuk saldo.' },
    { id: 'PT-001', status: 'Dibatalkan', type: 'earn', title: 'Poin Perjalanan — Open Trip Bali', date: '05 Jul 2026', amount: 75, description: 'Poin dibatalkan karena pemesanan dibatalkan. Tidak memengaruhi saldo.' },
];
const completed = history.filter((item) => item.status === 'Aktif');
const balance = completed.reduce((total, item) => total + item.amount, 0);
const visibleHistory = computed(() => history.filter((item) => item.status === activeTab.value));
const formatPoints = (value) => new Intl.NumberFormat('id-ID').format(value);
const statistics = [
    { label: 'Total Didapat', value: formatPoints(completed.filter((item) => item.amount > 0).reduce((total, item) => total + item.amount, 0)), unit: 'pts', icon: Coins },
    { label: 'Sudah Ditukar', value: formatPoints(-completed.filter((item) => item.amount < 0).reduce((total, item) => total + item.amount, 0)), unit: 'pts', icon: Gift },
    { label: 'Segera Kedaluwarsa', value: completed.reduce((total, item) => total + (item.expiring || 0), 0), unit: 'pts', icon: Hourglass },
    { label: 'Anggota Sejak', value: '2025', unit: '', icon: Award },
];
const showInfo = (title, text) => {
    dialogTitle.value = title;
    dialogText.value = text;
    dialog.value.showModal();
};
</script>

<template>
    <section aria-labelledby="points-heading">
        <h2 id="points-heading" class="text-sm font-extrabold text-[#183660]">Points Saya</h2><p class="mt-1 text-[11px] leading-5 text-slate-500">Pantau perolehan poin dan hadiah dari setiap perjalananmu.</p>
        <div class="mt-4 grid gap-3 sm:grid-cols-2">
            <div class="relative isolate overflow-hidden rounded-2xl border border-[#3185ef] bg-gradient-to-br from-[#1649ac] via-[#1679df] to-[#26bfda] p-6 text-white shadow-[0_8px_24px_rgba(30,106,198,0.16)]">
                <div class="pointer-events-none absolute -right-12 -top-16 -z-10 size-60 rounded-full border-[28px] border-white/10" aria-hidden="true"></div>
                <div class="pointer-events-none absolute -bottom-24 right-8 -z-10 size-56 rounded-full border border-white/20" aria-hidden="true"></div>
                <span class="inline-flex items-center gap-2 rounded-full border border-white/25 bg-white/15 px-3 py-1.5 text-[10px] font-semibold tracking-wide"><Coins class="size-4 text-sky-100" aria-hidden="true" />TAPAKLOKAL POINTS</span>
                <p class="mt-5 text-4xl font-extrabold tracking-tight">{{ formatPoints(balance) }}</p>
                <p class="mt-1 text-xs text-sky-100">Total poin tersedia</p>
                <button class="mt-5 rounded-xl bg-white px-4 py-2.5 text-[10px] font-bold text-[#3E7BEF] transition hover:bg-sky-50 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-[#3E7BEF]" @click="showInfo('Tentang Points', 'Poin dan statistik pada halaman ini adalah contoh tampilan. Saldo, perolehan, masa berlaku, dan riwayat akan mengikuti data akun setelah sistem points terhubung.')">Tentang Points</button>
            </div>
            <div class="relative isolate flex flex-col items-start overflow-hidden rounded-2xl border border-[#c7e5f5] bg-gradient-to-br from-[#eaf3ff] via-[#e4f6ff] to-[#bceeea] p-6 text-[#183660] shadow-[0_8px_24px_rgba(30,106,198,0.06)]">
                <div class="pointer-events-none absolute -right-14 -bottom-24 -z-10 size-64 rounded-full border-[30px] border-white/35" aria-hidden="true"></div>
                <div class="pointer-events-none absolute -top-12 -right-8 -z-10 size-40 rounded-full bg-white/35" aria-hidden="true"></div>
                <span class="mb-3 grid size-9 place-items-center rounded-xl border border-white/80 bg-white/70 text-[#1685da]"><Sparkles class="size-5" aria-hidden="true" /></span>
                <h3 class="max-w-52 text-sm font-bold leading-tight">Perjalanan seru,<br />hadiahnya juga!</h3>
                <p class="mt-2 max-w-56 text-xs leading-5 text-slate-500">Pelajari cara mendapatkan dan menukarkan poin untuk perjalanan berikutnya.</p>
                <button class="mt-4 rounded-xl bg-[#078cff] px-4 py-2.5 text-[10px] font-bold text-white transition hover:bg-[#0878db] focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-[#3E7BEF]" @click="showInfo('Panduan Points', 'Rincian program, aktivitas yang menghasilkan poin, dan pilihan penukaran akan tersedia saat program points diaktifkan. Belum ada penambahan atau penukaran poin pada pratinjau ini.')">Pelajari</button>
            </div>
        </div>

        <div class="mt-4 grid gap-3 sm:grid-cols-2 xl:grid-cols-4">
            <div v-for="(stat, index) in statistics" :key="stat.label" class="group relative isolate overflow-hidden rounded-2xl border p-4 transition duration-200 hover:-translate-y-0.5 hover:shadow-md motion-reduce:transform-none motion-reduce:transition-none" :class="['border-blue-100 bg-gradient-to-br from-white to-blue-50', 'border-cyan-100 bg-gradient-to-br from-white to-cyan-50', 'border-rose-100 bg-gradient-to-br from-white to-rose-50', 'border-indigo-100 bg-gradient-to-br from-white to-indigo-50'][index]">
                <div aria-hidden="true" class="pointer-events-none absolute -bottom-9 -right-8 -z-10 size-28 rounded-full border-[18px] opacity-40" :class="['border-blue-100', 'border-cyan-100', 'border-rose-100', 'border-indigo-100'][index]"></div>
                <div class="flex items-center gap-2.5"><span class="grid size-9 shrink-0 place-items-center rounded-xl border border-white/80 shadow-sm" :class="['bg-blue-100 text-blue-600', 'bg-cyan-100 text-cyan-700', 'bg-rose-100 text-rose-600', 'bg-indigo-100 text-indigo-600'][index]"><component :is="stat.icon" class="size-4" aria-hidden="true" /></span><p class="text-[10px] font-semibold leading-4 text-slate-600">{{ stat.label }}</p></div>
                <p class="mt-4 flex items-baseline gap-1.5 text-2xl font-extrabold tracking-tight text-[#183660]">{{ stat.value }}<span class="text-[10px] font-medium tracking-normal text-slate-400">{{ stat.unit }}</span></p>
                <p class="mt-1.5 text-[9px] leading-4 text-slate-500">{{ ['Poin dari perjalananmu', 'Ditukar menjadi hadiah', 'Gunakan sebelum berakhir', 'Awal cerita perjalananmu'][index] }}</p>
            </div>
        </div>

        <div class="mt-4 overflow-hidden rounded-2xl border border-[#e1eaf5] bg-white shadow-[0_4px_20px_rgba(23,75,120,0.04)]">
            <h3 class="border-b border-slate-100 px-4 py-3 text-xs font-semibold text-[#183660]">Riwayat Points</h3>
            <div class="flex gap-1 overflow-x-auto border-b border-slate-100 p-3" role="tablist" aria-label="Status points"><button v-for="tab in ['Aktif', 'Tertunda', 'Kedaluwarsa', 'Dibatalkan']" :id="`points-tab-${tab}`" :key="tab" role="tab" :aria-selected="activeTab === tab" aria-controls="points-history" class="min-h-8 shrink-0 rounded-full px-3 py-1.5 text-[10px] font-semibold transition-colors hover:text-[#078cff] focus-visible:outline-2 focus-visible:outline-[#078cff]" :class="activeTab === tab ? 'bg-[#3E7BEF] text-white shadow-sm' : 'bg-[#f8fafc] text-slate-500'" @click="activeTab = tab">{{ tab }}</button></div>
            <div v-if="visibleHistory.length" id="points-history" role="tabpanel" :aria-labelledby="`points-tab-${activeTab}`" class="max-h-[420px] space-y-3 overflow-y-auto overscroll-contain bg-[#f8fafc] p-3 sm:p-4 [scrollbar-width:thin] [scrollbar-color:#b9d5fa_transparent]">
                <article v-for="item in visibleHistory" :key="item.id" class="flex gap-3 rounded-xl border border-[#e1eaf5] bg-white p-4 transition-colors hover:border-[#aacbff]">
                    <span class="grid size-8 shrink-0 place-items-center rounded-lg" :class="item.type === 'earn' ? 'bg-sky-50 text-[#078cff]' : 'bg-teal-50 text-[#3E7BEF]'"><Coins v-if="item.type === 'earn'" class="size-4" /><Gift v-else class="size-4" /></span>
                    <div class="min-w-0 flex-1">
                        <div class="flex flex-wrap justify-between gap-2"><div class="flex flex-wrap gap-1.5"><span class="rounded-full bg-amber-50 px-2 py-0.5 text-[9px] font-medium text-amber-700">{{ item.type === 'earn' ? 'Poin masuk' : 'Tukar poin' }}</span><span class="rounded-full bg-[#edf6ff] px-2 py-0.5 text-[9px] text-[#078cff]">{{ item.status === 'Aktif' ? (item.voucherStatus || 'Selesai') : item.status }}</span></div><time class="text-[10px] text-slate-400">{{ item.date }}</time></div>
                        <div class="mt-2 flex items-start justify-between gap-3"><h4 class="text-xs font-bold leading-5 text-[#25364d]">{{ item.title }}</h4><span class="shrink-0 text-sm font-bold tabular-nums" :class="item.status !== 'Aktif' ? 'text-slate-400' : item.amount > 0 ? 'text-[#078cff]' : 'text-rose-500'">{{ item.amount > 0 ? '+' : '−' }}{{ formatPoints(Math.abs(item.amount)) }} <span class="text-[9px] font-normal">pts</span></span></div>
                        <p class="mt-1 text-[11px] leading-5 text-slate-400">{{ item.description }}</p>
                        <button v-if="item.voucher" class="mt-3 flex w-full items-center gap-2 rounded-lg border border-blue-100 bg-blue-50/70 px-3 py-2.5 text-left transition hover:bg-blue-100/70 focus-visible:outline-2 focus-visible:outline-[#3E7BEF]" @click="showInfo(item.voucher, `Kode ${item.code} adalah voucher contoh dari riwayat penukaran. Belum dapat digunakan untuk transaksi nyata.`)"><Gift class="size-4 shrink-0 text-[#3E7BEF]" /><span class="flex-1 text-[10px] font-semibold text-[#183660]">{{ item.voucher }}</span><span class="rounded border border-blue-200 bg-white px-1.5 py-1 font-mono text-[9px] text-[#3E7BEF]">{{ item.code }}</span></button>
                    </div>
                </article>
            </div>
            <div v-else id="points-history" role="tabpanel" :aria-labelledby="`points-tab-${activeTab}`" class="flex min-h-[220px] flex-col items-center justify-center px-6 py-10 text-center">
                <div class="relative mb-4 grid size-16 place-items-center rounded-full bg-[#eef7ff]" aria-hidden="true"><Coins class="size-8 text-[#3E7BEF]" :stroke-width="1.4" /><span class="absolute -right-2 bottom-0 grid size-10 place-items-center rounded-xl bg-white text-teal-500 shadow-[0_4px_20px_rgba(23,75,120,0.04)]"><Sparkles class="size-5" /></span><span class="absolute left-0 top-2 size-3 rounded-full bg-teal-200"></span></div>
                <h4 class="text-sm font-bold text-[#25364d]">{{ activeTab === 'Aktif' ? 'Belum Ada Aktivitas Points' : `Belum Ada Points ${activeTab}` }}</h4>
                <p class="mt-2 max-w-72 text-xs leading-5 text-slate-400">{{ activeTab === 'Aktif' ? 'Riwayat perolehan dan penggunaan poinmu akan muncul di sini.' : `Tidak ada riwayat poin dengan status ${activeTab.toLowerCase()} saat ini.` }}</p>
                <button class="mt-5 rounded-lg bg-[#078cff] px-5 py-2.5 text-xs font-semibold text-white transition hover:bg-[#0878db] focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-[#3E7BEF]" @click="showInfo('Tukar Points', 'Pilihan hadiah dan penukaran belum tersedia. Saldo contoh tidak dapat ditukarkan dan tidak akan berubah.')">Tukar Points</button>
            </div>
        </div>
        <p class="mt-3 text-[9px] text-slate-400">Pratinjau desain · Saldo dan statistik adalah data contoh.</p>
        <AccountExploreBanner />
        <dialog ref="dialog" class="fixed inset-0 m-auto w-[calc(100%-2rem)] max-w-md rounded-2xl bg-white p-6 text-slate-700 shadow-xl backdrop:bg-slate-900/50" aria-labelledby="points-info-title"><div class="flex items-center justify-between gap-3"><h2 id="points-info-title" class="text-base font-bold">{{ dialogTitle }}</h2><button autofocus class="rounded-full p-2 hover:bg-slate-100" aria-label="Tutup informasi points" @click="dialog.close()"><X class="size-4" /></button></div><p class="mt-4 text-sm leading-6 text-slate-500">{{ dialogText }}</p></dialog>
    </section>
</template>
