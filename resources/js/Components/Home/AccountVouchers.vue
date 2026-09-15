<script setup>
import { ArrowRight, Coins, Gift, Hourglass, Ticket, X } from 'lucide-vue-next';
import { computed, ref } from 'vue';

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
        <h2 id="voucher-heading" class="text-xl font-extrabold tracking-tight text-[#202e45]">Voucher Saya</h2>
        <div class="mt-4 grid overflow-hidden rounded-2xl text-white sm:grid-cols-2">
            <div class="relative isolate overflow-hidden bg-[#12445d] p-5"><div class="absolute inset-0 -z-10 bg-[radial-gradient(circle_at_85%_5%,#ffffff20_0_50px,transparent_51px)]"></div><Ticket class="mb-3 size-5 text-cyan-300" /><h3 class="text-sm font-bold">Cara Pakai & Dapatkan Voucher</h3><p class="mt-2 max-w-60 text-[11px] leading-5 text-white/75">Pelajari cara menggunakan kupon pilihan dan nikmati keuntungan untuk perjalananmu.</p><button class="mt-3 rounded-lg bg-[#22d3cb] px-4 py-2 text-[10px] font-bold text-[#12445d] hover:bg-teal-200" @click="show('Tentang Voucher', 'Periksa minimum transaksi, jenis pesanan, dan masa berlaku sebelum memilih voucher. Semua kupon pada halaman ini adalah contoh pratinjau.')">Pelajari</button></div>
            <div class="relative isolate overflow-hidden bg-gradient-to-br from-[#10558c] to-[#209ddb] p-5"><div class="absolute inset-0 -z-10 bg-[radial-gradient(circle_at_90%_100%,#ffffff20_0_70px,transparent_71px)]"></div><Gift class="mb-3 size-5 text-sky-200" /><h3 class="text-sm font-bold">Selesaikan Misi Jalan-Jalan &<br />Putar Lucky Wheel!</h3><p class="mt-2 max-w-60 text-[11px] leading-5 text-white/75">Temukan kesempatan mendapatkan voucher untuk Open Trip dan Open PO.</p><button class="mt-3 rounded-lg bg-white px-4 py-2 text-[10px] font-bold text-[#10558c] hover:bg-sky-50" @click="show('Misi & Lucky Wheel', 'Fitur misi dan Lucky Wheel belum diaktifkan. Hadiah dan aturan program akan tersedia saat program dimulai.')">Mulai Misi</button></div>
        </div>
        <div class="mt-4 grid grid-cols-2 gap-3 xl:grid-cols-4"><div v-for="stat in [{ icon: Ticket, value: '5 Voucher', label: 'Total voucher aktif' }, { icon: Gift, value: '5 Voucher', label: 'Voucher dalam pratinjau' }, { icon: Hourglass, value: '0 Voucher', label: 'Segera kedaluwarsa' }, { icon: Coins, value: '100 Pts', label: 'Poin tersedia (contoh)' }]" :key="stat.label" class="flex items-center gap-2 rounded-xl border border-slate-100 bg-white p-3 shadow-sm"><span class="grid size-10 shrink-0 place-items-center rounded-lg bg-gradient-to-br from-sky-500 to-[#104c78] text-white"><component :is="stat.icon" class="size-5" /></span><div><p class="text-xs font-bold text-[#26364d]">{{ stat.value }}</p><p class="mt-1 text-[9px] text-slate-400">{{ stat.label }}</p></div></div></div>
        <div class="mt-4 overflow-hidden rounded-2xl border border-[#e2e9f3] bg-white"><h3 class="px-5 pt-5 text-sm font-bold text-[#26364d]">Riwayat Voucher</h3><div class="mt-2 flex gap-2 overflow-x-auto border-b border-slate-100 px-4"><button v-for="item in ['Aktif', 'Open Trip', 'Open PO', 'Kedaluwarsa']" :key="item" class="shrink-0 border-b-2 px-3 py-3 text-[11px] transition" :class="tab === item ? 'border-teal-400 text-[#1045a5]' : 'border-transparent text-slate-400 hover:text-[#078cff]'" :aria-pressed="tab === item" @click="tab = item">{{ item }}</button></div>
            <div class="max-h-[360px] space-y-3 overflow-y-auto p-4 [scrollbar-width:thin]">
                <article v-for="voucher in filtered" :key="voucher.code" class="flex overflow-hidden rounded-xl border border-[#e3e9f1] bg-white shadow-sm transition hover:border-sky-200 hover:shadow-md">
                    <div class="relative flex w-24 shrink-0 flex-col items-center justify-center bg-gradient-to-br from-cyan-500 to-[#007398] px-2 py-5 text-white sm:w-28" :class="{ 'grayscale': voucher.expired }"><span class="text-[8px] font-semibold tracking-wider">DISKON</span><strong class="mt-1 text-2xl">{{ voucher.value }}</strong><span class="mt-1 text-[8px] text-white/80">{{ voucher.type }}</span><span class="absolute -right-2 -top-2 size-4 rounded-full bg-white"></span><span class="absolute -bottom-2 -right-2 size-4 rounded-full bg-white"></span></div>
                    <div class="min-w-0 flex-1 border-l border-dashed border-slate-200 p-3 sm:p-4"><span class="rounded-full bg-orange-50 px-2 py-0.5 text-[8px] font-semibold text-orange-500">{{ voucher.expired ? 'Kedaluwarsa' : voucher.type }}</span><h4 class="mt-2 text-xs font-bold leading-5 text-[#26364d]">{{ voucher.title }}</h4><p class="mt-1 text-[10px] text-slate-400">Min. transaksi {{ voucher.minimum }} · Berlaku untuk {{ voucher.type }}</p><div class="mt-3 flex flex-wrap items-center justify-between gap-2"><p class="text-[9px] text-slate-400">Berlaku hingga {{ voucher.validity }}</p><div class="flex flex-wrap items-center gap-2"><code class="rounded border border-slate-100 px-2 py-1 text-[8px] text-slate-500">{{ voucher.code }}</code><button :disabled="voucher.expired" class="inline-flex items-center gap-1.5 rounded-md bg-[#1045a5] px-3 py-1.5 text-[9px] font-semibold text-white hover:bg-[#083788] disabled:cursor-not-allowed disabled:bg-slate-200 disabled:text-slate-400" @click="useVoucher(voucher)">Gunakan<ArrowRight class="size-3" /></button></div></div></div>
                </article>
            </div>
        </div>
        
        <div class="mt-5 flex flex-wrap items-center justify-between gap-3 rounded-xl bg-gradient-to-r from-[#1045a5] to-[#158bc9] px-5 py-4 text-white"><div><h3 class="text-xs font-bold">Punya Voucher Penghemat? Pakai Sekarang!</h3><p class="mt-1 text-[10px] text-white/75">Periksa syarat voucher sebelum memilih perjalanan berikutnya.</p></div><button class="rounded-lg bg-teal-300 px-4 py-2.5 text-[10px] font-bold text-[#1045a5] hover:bg-teal-200" @click="show('Gunakan Voucher', 'Penukaran voucher dilakukan saat checkout setelah sistem pemesanan terhubung. Kode contoh di halaman ini belum berlaku untuk transaksi nyata.')">Gunakan Voucher</button></div>
        <p class="mt-3 text-[9px] text-slate-400">Pratinjau desain · Voucher dan statistik adalah data contoh.</p>
        <dialog ref="dialog" class="fixed inset-0 m-auto w-[calc(100%-2rem)] max-w-md rounded-2xl bg-white p-6 text-slate-700 backdrop:bg-slate-900/50" aria-labelledby="voucher-info-title"><div class="flex items-center justify-between gap-3"><h2 id="voucher-info-title" class="font-bold">{{ info.title }}</h2><button autofocus class="rounded-full p-2 hover:bg-slate-100" aria-label="Tutup informasi voucher" @click="dialog.close()"><X class="size-4" /></button></div><p class="mt-4 text-sm leading-6 text-slate-500">{{ info.text }}</p></dialog>
    </section>
</template>
