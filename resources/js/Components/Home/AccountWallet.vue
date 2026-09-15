<script setup>
import { Building2, Check, Plus, Smartphone, Store, Trash2, X, ArrowDownLeft, ArrowUpRight, ChevronRight, CreditCard, Eye, EyeOff, ReceiptText, ShieldCheck, Wallet } from 'lucide-vue-next';
import { ref } from 'vue';
import AccountExploreBanner from './AccountExploreBanner.vue';

defineEmits(['transactions']);
const balanceVisible = ref(true);
const methodDialog = ref(null);
const selectedMethod = ref('');
const savedMethods = ref([]);
const primaryMethod = ref('');
const failedLogos = ref({});
const paymentMethods = [
    { id: 'wallet', name: 'Tapak Lokal Wallet', description: 'Saldo Tapak Lokal', icon: Wallet },
    { id: 'gopay', logo: 'https://commons.wikimedia.org/wiki/Special:FilePath/GoPay%20logo.svg', name: 'GoPay', description: 'Dompet digital', icon: Smartphone },
    { id: 'ovo', logo: 'https://commons.wikimedia.org/wiki/Special:FilePath/Logo%20ovo%20purple.svg', name: 'OVO', description: 'Dompet digital', icon: Smartphone },
    { id: 'indomaret', logo: 'https://commons.wikimedia.org/wiki/Special:FilePath/Indomaret.svg', name: 'Indomaret', description: 'Pembayaran di gerai', icon: Store },
    { id: 'alfamart', logo: 'https://commons.wikimedia.org/wiki/Special:FilePath/Alfamart%20logo.svg', name: 'Alfamart', description: 'Pembayaran di gerai', icon: Store },
    { id: 'mandiri', logo: 'https://commons.wikimedia.org/wiki/Special:FilePath/Bank%20Mandiri%20logo%202016.svg', name: 'Mandiri', description: 'Mobile banking', icon: Building2 },
    { id: 'bca', logo: 'https://commons.wikimedia.org/wiki/Special:FilePath/Bank%20Central%20Asia.svg', name: 'BCA', description: 'Mobile banking', icon: Building2 },
];
const openMethods = () => { selectedMethod.value = ''; methodDialog.value.showModal(); };
const addMethod = () => {
    const method = paymentMethods.find((item) => item.id === selectedMethod.value);
    if (!method || savedMethods.value.some((item) => item.id === method.id)) { return; }
    savedMethods.value.push(method);
    if (!primaryMethod.value) { primaryMethod.value = method.id; }
    methodDialog.value.close();
};
const removeMethod = (id) => {
    savedMethods.value = savedMethods.value.filter((item) => item.id !== id);
    if (primaryMethod.value === id) { primaryMethod.value = savedMethods.value[0]?.id ?? ''; }
};
</script>

<template>
    <section aria-labelledby="wallet-heading">
        <h2 id="wallet-heading" class="text-sm font-extrabold text-[#183660]">Saldo & Pembayaran</h2>
        <p class="mt-1 text-[11px] leading-5 text-slate-500">Pantau saldo dan temukan informasi pembayaran perjalananmu.</p>

        <div class="mt-4 grid gap-3 sm:grid-cols-2">
            <section class="overflow-hidden rounded-2xl border border-[#e1eaf5] bg-white shadow-[0_4px_20px_rgba(23,75,120,0.04)]" aria-label="Ringkasan saldo">
                <div class="flex items-center justify-between border-b border-slate-100 px-4 py-3"><span class="flex items-center gap-2 text-xs font-semibold text-[#183660]"><Wallet class="size-4 text-[#3E7BEF]" />Saldo saya</span><span class="rounded-full bg-[#edf4ff] px-2.5 py-1 text-[9px] font-medium text-[#3E7BEF]">Pratinjau</span></div>
                <div class="bg-gradient-to-br from-[#edf4ff] to-white p-5"><div class="flex items-center gap-2"><p class="text-[11px] text-slate-500">Saldo tersedia</p><button type="button" class="grid size-8 place-items-center rounded-full text-slate-500 transition-colors hover:bg-blue-100 focus-visible:outline-2 focus-visible:outline-[#3E7BEF]" :aria-label="balanceVisible ? 'Sembunyikan saldo' : 'Tampilkan saldo'" :aria-pressed="!balanceVisible" @click="balanceVisible = !balanceVisible"><component :is="balanceVisible ? Eye : EyeOff" class="size-4" /></button></div><p class="mt-1 text-3xl font-extrabold tracking-tight text-[#183660]" aria-live="polite">{{ balanceVisible ? 'Rp 0' : 'Rp ••••••' }}</p><p class="mt-3 text-[10px] leading-5 text-slate-500">Saldo akunmu akan ditampilkan di sini.</p></div>
                <div class="grid grid-cols-2 divide-x divide-slate-100 border-t border-slate-100"><div class="p-4"><p class="flex items-center gap-1.5 text-[10px] text-slate-500"><ArrowDownLeft class="size-3.5 text-teal-500" />Saldo masuk</p><p class="mt-2 text-xs font-bold text-[#183660]">{{ balanceVisible ? 'Rp 0' : '••••••' }}</p></div><div class="p-4"><p class="flex items-center gap-1.5 text-[10px] text-slate-500"><ArrowUpRight class="size-3.5 text-[#3E7BEF]" />Saldo keluar</p><p class="mt-2 text-xs font-bold text-[#183660]">{{ balanceVisible ? 'Rp 0' : '••••••' }}</p></div></div>
            </section>

            <section class="flex flex-col overflow-hidden rounded-2xl border border-[#e1eaf5] bg-white shadow-[0_4px_20px_rgba(23,75,120,0.04)]" aria-labelledby="wallet-payment-heading">
                <div class="flex items-center gap-2 border-b border-slate-100 px-4 py-3"><CreditCard class="size-4 text-[#3E7BEF]" /><h3 id="wallet-payment-heading" class="text-xs font-semibold text-[#183660]">Metode pembayaran</h3></div>
                <div class="flex flex-1 flex-col p-4">
                    <div v-if="!savedMethods.length" class="py-3"><span class="grid size-11 place-items-center rounded-xl bg-[#edf4ff] text-[#3E7BEF]"><CreditCard class="size-5" /></span><h4 class="mt-4 text-xs font-bold text-[#183660]">Pilih cara bayar favoritmu</h4><p class="mt-2 text-[11px] leading-5 text-slate-500">Tambahkan wallet, dompet digital, gerai retail, atau mobile banking.</p></div>
                    <ul v-else class="max-h-60 space-y-2 overflow-y-auto [scrollbar-width:thin]">
                        <li v-for="method in savedMethods" :key="method.id" class="flex items-center gap-3 rounded-xl border border-[#e1eaf5] p-3"><span class="grid h-10 w-16 shrink-0 place-items-center rounded-lg border border-slate-100 bg-white p-2"><img v-if="method.logo &amp;&amp; !failedLogos[method.id]" :src="method.logo" :alt="method.name" class="max-h-6 w-full object-contain" @error="failedLogos[method.id] = true" /><component v-else :is="method.icon" class="size-5 text-[#3E7BEF]" /></span><div class="min-w-0 flex-1"><p class="text-xs font-semibold text-[#183660]">{{ method.name }}</p><span v-if="primaryMethod === method.id" class="text-[10px] text-[#3E7BEF]">Pilihan utama</span><button v-else type="button" class="text-[10px] text-slate-500 hover:text-[#3E7BEF]" @click="primaryMethod = method.id">Jadikan utama</button></div><button type="button" :aria-label="`Hapus ${method.name}`" class="rounded-lg p-2 text-slate-400 hover:bg-red-50 hover:text-red-600" @click="removeMethod(method.id)"><Trash2 class="size-3.5" /></button></li>
                    </ul>
                    <div class="mt-4 flex flex-wrap items-center gap-3"><button type="button" :disabled="savedMethods.length === paymentMethods.length" class="inline-flex min-h-9 items-center gap-2 rounded-lg bg-[#078cff] px-4 text-[11px] font-semibold text-white transition-colors hover:bg-[#0878db] disabled:cursor-not-allowed disabled:opacity-40" @click="openMethods"><Plus class="size-3.5" />Tambah metode</button><button type="button" class="text-[11px] font-semibold text-[#3E7BEF] hover:underline" @click="$emit('transactions')">Lihat transaksi</button></div>
                    <p class="mt-3 text-[10px] leading-4 text-slate-400">Pilihan sementara untuk pratinjau, belum menghubungkan akun pembayaran.</p>
                </div>
            </section>
        </div>

        <section class="mt-4 overflow-hidden rounded-2xl border border-[#e1eaf5] bg-white shadow-[0_4px_20px_rgba(23,75,120,0.04)]" aria-labelledby="wallet-history-heading">
            <div class="flex items-center justify-between gap-3 border-b border-slate-100 px-4 py-3"><h3 id="wallet-history-heading" class="flex items-center gap-2 text-xs font-semibold text-[#183660]"><ReceiptText class="size-4 text-[#3E7BEF]" />Riwayat saldo</h3><span class="rounded-full bg-[#edf4ff] px-2.5 py-1 text-[10px] font-medium text-[#3E7BEF]">0 aktivitas</span></div>
            <div class="bg-[#f8fafc] p-4"><div class="flex flex-col items-center rounded-xl border border-[#e1eaf5] bg-white px-4 py-8 text-center"><span class="grid size-12 place-items-center rounded-full bg-[#edf4ff] text-[#3E7BEF]"><Wallet class="size-5" /></span><h4 class="mt-3 text-xs font-semibold text-[#183660]">Belum ada aktivitas saldo</h4><p class="mt-2 max-w-xs text-[11px] leading-5 text-slate-500">Riwayat saldo masuk dan keluar akan muncul di sini saat ada aktivitas pada akunmu.</p></div></div>
        </section>
        <p class="mt-3 flex items-start gap-1.5 text-[10px] leading-5 text-slate-400"><ShieldCheck class="mt-0.5 size-3.5 shrink-0" />Data saldo masih berupa pratinjau. Isi saldo dan penarikan belum tersedia.</p>
        <dialog ref="methodDialog" aria-labelledby="add-payment-heading" class="fixed inset-0 m-auto max-h-[85dvh] w-[calc(100%-2rem)] max-w-md overflow-hidden rounded-2xl bg-white p-0 text-slate-700 shadow-xl backdrop:bg-slate-900/40">
            <div class="max-h-[85dvh] overflow-y-auto overscroll-contain p-5 [scrollbar-width:thin] [scrollbar-color:#cbd5e1_transparent]"><div class="flex items-center justify-between gap-3"><h2 id="add-payment-heading" class="text-sm font-bold text-[#183660]">Tambah metode pembayaran</h2><button type="button" autofocus aria-label="Tutup pilihan metode" class="rounded-full p-2 hover:bg-slate-100" @click="methodDialog.close()"><X class="size-4" /></button></div>
            <p class="mt-1 text-[11px] leading-5 text-slate-500">Pilih metode yang ingin ditampilkan di akunmu.</p>
            <form class="mt-4" @submit.prevent="addMethod">
                <fieldset class="space-y-2"><legend class="sr-only">Pilihan metode pembayaran</legend><label v-for="method in paymentMethods" :key="method.id" class="flex cursor-pointer items-center gap-3 rounded-xl border p-3 transition-colors focus-within:ring-2 focus-within:ring-blue-200" :class="savedMethods.some(item => item.id === method.id) ? 'cursor-not-allowed border-slate-100 bg-slate-50 opacity-50' : selectedMethod === method.id ? 'border-[#3E7BEF] bg-[#edf4ff]' : 'border-[#e1eaf5] hover:bg-slate-50'"><span class="grid h-10 w-16 shrink-0 place-items-center rounded-lg border border-slate-100 bg-white p-2"><img v-if="method.logo &amp;&amp; !failedLogos[method.id]" :src="method.logo" :alt="method.name" class="max-h-6 w-full object-contain" @error="failedLogos[method.id] = true" /><component v-else :is="method.icon" class="size-5 text-[#3E7BEF]" /></span><span class="flex-1"><span class="block text-xs font-semibold text-[#183660]">{{ method.name }}</span><span class="text-[10px] text-slate-500">{{ savedMethods.some(item => item.id === method.id) ? 'Sudah ditambahkan' : method.description }}</span></span><input v-model="selectedMethod" type="radio" name="payment-method" :value="method.id" :disabled="savedMethods.some(item => item.id === method.id)" class="size-4 accent-[#3E7BEF]" /></label></fieldset>
                <button type="submit" :disabled="!selectedMethod" class="mt-4 min-h-10 w-full rounded-xl bg-[#078cff] text-xs font-semibold text-white hover:bg-[#0878db] disabled:cursor-not-allowed disabled:opacity-40">Tambahkan metode</button>
            </form>
            </div>
        </dialog>
        <AccountExploreBanner />
    </section>
</template>
