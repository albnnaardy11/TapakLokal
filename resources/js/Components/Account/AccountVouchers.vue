<script setup>
import { ArrowRight, Coins, Gift, Hourglass, Ticket, X } from 'lucide-vue-next';
import { computed, ref } from 'vue';
import AccountExploreBanner from './AccountExploreBanner.vue';

const tab = ref('Aktif');

const dialog = ref(null);
const info = ref({ title: '', text: '' });
const vouchers = [
    { code: 'TRIP20-DEMO', title: 'Diskon 20% Open Trip Hidden Gem Nusantara', value: '20%', type: 'Open Trip', minimum: 'Rp 600.000', validity: '31 Agu 2029', expired: false },
    { code: 'WEEKEND30-DEMO', title: 'Potongan Rp30RB Liburan Akhir Pekan', value: '30RB', type: 'Open Trip', minimum: 'Rp 200.000', validity: '31 Agu 2029', expired: false },
    { code: 'KULINER15-DEMO', title: 'Potongan Rp15RB PO Kuliner Khas Daerah', value: '15RB', type: 'Open PO', minimum: 'Rp 80.000', validity: '31 Agu 2029', expired: false },
    { code: 'LOKAL10-DEMO', title: 'Diskon 10% Oleh-oleh Pilihan Lokal', value: '10%', type: 'Open PO', minimum: 'Rp 100.000', validity: '31 Agu 2029', expired: false },
    { code: 'JELAJAH25-DEMO', title: 'Potongan Rp25RB Jelajah Destinasi Baru', value: '25RB', type: 'Open Trip', minimum: 'Rp 300.000', validity: '31 Agu 2029', expired: false },
    { code: 'TRIPOLD-DEMO', title: 'Diskon Liburan Musim Lalu', value: '10%', type: 'Open Trip', minimum: 'Rp 300.000', validity: '31 Agu 2025', expired: true },
];
const filtered = computed(() => vouchers.filter((item) => tab.value === 'Kedaluwarsa' ? item.expired : !item.expired && (tab.value === 'Aktif' || item.type === tab.value)));
const show = (title, text) => { info.value = { title, text }; dialog.value.showModal(); };
const useVoucher = (voucher) => {
    show(voucher.title, 'Voucher ini berlaku untuk ' + voucher.type + ' dengan minimum transaksi ' + voucher.minimum + '. Penggunaan voucher akan tersedia saat checkout terhubung. Voucher contoh belum diterapkan ke pesanan.');
};
</script>

<template>
    <section aria-labelledby="voucher-heading">
        <h2 id="voucher-heading" class="text-sm font-extrabold text-[#183660]">Voucher Saya</h2><p class="mt-1 text-[11px] leading-5 text-slate-500">Temukan voucher pilihan dan nikmati lebih banyak keuntungan perjalanan.</p>
        <div class="mt-4 grid gap-3 text-white sm:grid-cols-2">
            <div class="relative isolate overflow-hidden rounded-2xl border border-[#3185ef] bg-gradient-to-br from-[#1649ac] via-[#1679df] to-[#26bfda] p-6 shadow-[0_8px_24px_rgba(30,106,198,0.16)]"><div class="absolute inset-0 -z-10 bg-[radial-gradient(circle_at_85%_5%,#ffffff20_0_50px,transparent_51px)]"></div><Ticket class="mb-3 size-5 text-cyan-300" /><h3 class="text-sm font-bold">Cara Pakai & Dapatkan Voucher</h3><p class="mt-2 max-w-60 text-[11px] leading-5 text-inherit opacity-80">Pelajari cara menggunakan kupon pilihan dan nikmati keuntungan untuk perjalananmu.</p><button class="mt-3 rounded-lg bg-white px-4 py-2 text-[10px] font-bold text-[#1759a0] hover:bg-sky-50" @click="show('Tentang Voucher', 'Periksa minimum transaksi, jenis pesanan, dan masa berlaku sebelum memilih voucher. Semua kupon pada halaman ini adalah contoh pratinjau.')">Pelajari</button></div>
            <div class="relative isolate overflow-hidden rounded-2xl border border-[#c7e5f5] bg-gradient-to-br from-[#eaf3ff] via-[#e4f6ff] to-[#bceeea] p-6 text-[#183660]"><div class="absolute inset-0 -z-10 bg-[radial-gradient(circle_at_90%_100%,#ffffff20_0_70px,transparent_71px)]"></div><Gift class="mb-3 size-5 text-[#3E7BEF]" /><h3 class="text-sm font-bold">Selesaikan Misi Jalan-Jalan &<br />Putar Lucky Wheel!</h3><p class="mt-2 max-w-60 text-[11px] leading-5 text-inherit opacity-80">Temukan kesempatan mendapatkan voucher untuk Open Trip dan Open PO.</p><button class="mt-3 rounded-lg bg-[#078cff] px-4 py-2 text-[10px] font-bold text-white hover:bg-[#0878db]" @click="show('Misi & Lucky Wheel', 'Fitur misi dan Lucky Wheel belum diaktifkan. Hadiah dan aturan program akan tersedia saat program dimulai.')">Mulai Misi</button></div>
        </div>
        <div class="mt-4 grid gap-3 sm:grid-cols-2 xl:grid-cols-4"><div v-for="stat in [{ icon: Ticket, value: '5 Voucher', label: 'Total voucher aktif' }, { icon: Gift, value: '5 Voucher', label: 'Voucher dalam pratinjau' }, { icon: Hourglass, value: '0 Voucher', label: 'Segera kedaluwarsa' }, { icon: Coins, value: '100 Pts', label: 'Poin tersedia (contoh)' }]" :key="stat.label" class="flex items-center gap-2 rounded-2xl border border-[#e1eaf5] bg-gradient-to-br from-white to-blue-50 p-4 shadow-[0_4px_20px_rgba(23,75,120,0.04)]"><span class="grid size-10 shrink-0 place-items-center rounded-xl bg-[#edf4ff] text-[#3E7BEF]"><component :is="stat.icon" class="size-5" /></span><div><p class="text-xs font-bold text-[#26364d]">{{ stat.value }}</p><p class="mt-1 text-[9px] text-slate-400">{{ stat.label }}</p></div></div></div>
        <div class="mt-4 overflow-hidden rounded-2xl border border-[#e1eaf5] bg-white shadow-[0_4px_20px_rgba(23,75,120,0.04)]"><h3 class="border-b border-slate-100 px-4 py-3 text-xs font-semibold text-[#183660]">Riwayat Voucher</h3><div class="flex gap-1 overflow-x-auto border-b border-slate-100 p-3"><button v-for="item in ['Aktif', 'Open Trip', 'Open PO', 'Kedaluwarsa']" :key="item" class="min-h-8 shrink-0 rounded-full px-3 py-1.5 text-[10px] font-semibold transition focus-visible:outline-2 focus-visible:outline-[#3E7BEF]" :class="tab === item ? 'bg-[#3E7BEF] text-white shadow-sm' : 'bg-[#f8fafc] text-slate-500 hover:bg-[#edf4ff] hover:text-[#3E7BEF]'" :aria-pressed="tab === item" @click="tab = item">{{ item }}</button></div>
            <div tabindex="0" role="region" aria-label="Daftar voucher, dapat digulir" class="max-h-[420px] space-y-3 overflow-y-auto overscroll-contain bg-[#f8fafc] p-3 sm:p-4 [scrollbar-width:thin] [scrollbar-color:#b9d5fa_transparent] focus-visible:outline-2 focus-visible:outline-[#3E7BEF]">
                <article v-for="voucher in filtered" :key="voucher.code" class="flex overflow-hidden rounded-xl border border-[#e1eaf5] bg-white transition hover:border-sky-200 hover:shadow-md">
                    <div class="relative flex w-24 shrink-0 flex-col items-center justify-center bg-gradient-to-br from-[#3E7BEF] via-[#168fe8] to-[#20bcd4] px-2 py-5 text-white sm:w-28" :class="{ 'grayscale': voucher.expired }"><span class="text-[8px] font-semibold tracking-wider">DISKON</span><strong class="mt-1 text-2xl">{{ voucher.value }}</strong><span class="mt-1 text-[8px] text-white/80">{{ voucher.type }}</span><span class="absolute -right-2 -top-2 size-4 rounded-full bg-white"></span><span class="absolute -bottom-2 -right-2 size-4 rounded-full bg-white"></span></div>
                    <div class="min-w-0 flex-1 border-l border-dashed border-slate-200 p-3 sm:p-4"><span class="rounded-full bg-[#edf4ff] px-2 py-0.5 text-[8px] font-semibold text-[#3E7BEF]">{{ voucher.expired ? 'Kedaluwarsa' : voucher.type }}</span><h4 class="mt-2 text-xs font-bold leading-5 text-[#26364d]">{{ voucher.title }}</h4><p class="mt-1 text-[10px] text-slate-400">Min. transaksi {{ voucher.minimum }} · Berlaku untuk {{ voucher.type }}</p><div class="mt-3 flex flex-wrap items-center justify-between gap-2"><p class="text-[9px] text-slate-400">Berlaku hingga {{ voucher.validity }}</p><div class="flex flex-wrap items-center gap-2"><code class="rounded border border-slate-100 px-2 py-1 text-[8px] text-slate-500">{{ voucher.code }}</code><button :disabled="voucher.expired" class="inline-flex items-center gap-1.5 rounded-lg bg-[#078cff] px-3 py-1.5 text-[9px] font-semibold text-white hover:bg-[#0878db] disabled:cursor-not-allowed disabled:bg-slate-200 disabled:text-slate-400" @click="useVoucher(voucher)">Gunakan<ArrowRight class="size-3" /></button></div></div></div>
                </article>
            </div>
        </div>
        
        <AccountExploreBanner />
        <p class="mt-3 text-[9px] text-slate-400">Pratinjau desain · Voucher dan statistik adalah data contoh.</p>
        <dialog ref="dialog" class="fixed inset-0 m-auto w-[calc(100%-2rem)] max-w-md rounded-2xl bg-white p-6 text-slate-700 backdrop:bg-slate-900/50" aria-labelledby="voucher-info-title"><div class="flex items-center justify-between gap-3"><h2 id="voucher-info-title" class="font-bold">{{ info.title }}</h2><button autofocus class="rounded-full p-2 hover:bg-slate-100" aria-label="Tutup informasi voucher" @click="dialog.close()"><X class="size-4" /></button></div><p class="mt-4 text-sm leading-6 text-slate-500">{{ info.text }}</p></dialog>
    </section>
</template>
