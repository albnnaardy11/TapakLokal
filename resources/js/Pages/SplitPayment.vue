<script setup>
import { computed, onMounted, onBeforeUnmount, ref } from 'vue';
import axios from 'axios';
import { Head } from '@inertiajs/vue3';
const props = defineProps({ share: Object, tripTitle: String, expiresAt: String, bookingStatus: String, checkUrl: String });
const share = ref(props.share);
const bookingStatus = ref(props.bookingStatus);
const busy = ref(false);
const message = ref('');
const now = ref(Date.now());
let polling; let tick;
const canPay = computed(() => share.value.status === 'pending' && bookingStatus.value === 'awaiting_payment' && Date.parse(props.expiresAt) > now.value);
const money = value => new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', maximumFractionDigits: 0 }).format(value);
const methodLabels = { bca: 'BCA Virtual Account', mandiri: 'Mandiri Bill Payment', gopay: 'GoPay / QR', ovo: 'OVO melalui QRIS', alfamart: 'Alfamart', indomaret: 'Indomaret' };
const codeLabels = { va_number: 'Nomor virtual account', bill_key: 'Kode pembayaran', biller_code: 'Kode perusahaan', payment_code: 'Kode pembayaran' };
const codes = computed(() => Object.entries(share.value.instructions || {}).filter(([key]) => key in codeLabels));
const check = async () => {
    if (busy.value || document.visibilityState !== 'visible') return;
    busy.value = true;
    try { const { data } = await axios.post(props.checkUrl, {}, { headers: { Accept: 'application/json' }, timeout: 8000 }); share.value = data.share; bookingStatus.value = data.bookingStatus; message.value = share.value.status === 'paid' ? 'Pembayaran bagian ini sudah berhasil.' : 'Status diperbarui.'; }
    catch (error) { message.value = error.response?.data?.errors?.payment?.[0] || error.response?.data?.message || 'Status belum dapat diperiksa. Coba lagi.'; }
    finally { busy.value = false; }
};
const copy = async value => { try { await navigator.clipboard.writeText(String(value)); message.value = 'Berhasil disalin ke clipboard.'; } catch { message.value = 'Salin secara manual.'; } };
const checkOnReturn = () => {
    if (share.value.status === 'pending') check();
};
onMounted(() => {
    tick = setInterval(() => now.value = Date.now(), 1000);
    polling = setInterval(checkOnReturn, 30000);
    document.addEventListener('visibilitychange', checkOnReturn);
    window.addEventListener('focus', checkOnReturn);
    checkOnReturn();
});
onBeforeUnmount(() => {
    clearInterval(tick);
    clearInterval(polling);
    document.removeEventListener('visibilitychange', checkOnReturn);
    window.removeEventListener('focus', checkOnReturn);
});
</script>
<template>
    <Head title="Tagihan anggota"><meta name="robots" content="noindex,nofollow" /><meta name="referrer" content="no-referrer" /></Head>
    <main class="min-h-screen bg-[#f5f9ff] px-4 py-10 text-[#17345e]">
        <section class="mx-auto max-w-xl space-y-5 rounded-2xl border border-slate-200 bg-white p-6 shadow-xs">
            <p class="text-sm font-bold text-blue-600">TapakLokal · Split bill</p>
            <h1 class="text-2xl font-extrabold">Tagihan {{ share.label }}</h1>
            <p class="text-sm text-slate-500">{{ tripTitle }}</p>
            <p class="text-3xl font-extrabold">{{ money(share.amount) }}</p>
            <p class="font-bold">{{ methodLabels[share.method] }}</p>
            <p class="text-xs text-rose-600">Bayar sebelum {{ new Date(expiresAt).toLocaleString('id-ID', { timeZone: 'Asia/Jakarta' }) }} WIB</p>
            <p v-if="share.status === 'paid'" class="rounded-xl bg-emerald-50 p-4 font-bold text-emerald-700">Bagian ini sudah dibayar. Tidak perlu membayar lagi.</p>
            <p v-else-if="!canPay" class="rounded-xl bg-rose-50 p-4 text-sm text-rose-700">{{ share.status === 'reconciliation_required' ? 'Pembayaran perlu diperiksa. Hubungi pemesan untuk tindak lanjut refund.' : 'Tagihan sudah ditutup. Jangan bayar QR atau kode ini.' }}</p>
            <template v-else>
                <p v-if="!share.instructions" class="rounded-xl bg-blue-50 p-4 text-sm">Pemesan belum menyiapkan instruksi pembayaran. Minta pemesan menekan Siapkan Tagihan.</p>
                <div v-for="[key,value] in codes" :key="key" class="rounded-xl border border-slate-200 p-4"><p class="text-xs text-slate-500">{{ codeLabels[key] }}</p><div class="mt-2 flex items-center justify-between gap-3"><strong class="break-all font-mono text-xl">{{ value }}</strong><button type="button" class="panel-secondary" @click="copy(value)">Salin</button></div></div>
                <div v-if="share.instructions?.qr_url" class="text-center"><img :src="share.instructions.qr_url" alt="QR pembayaran bagian anggota" width="240" height="240" class="mx-auto size-60" /><p class="mt-3 text-xs text-slate-500">Pindai menggunakan aplikasi pembayaran yang mendukung QR ini.</p><button type="button" class="panel-secondary mt-3" @click="copy(share.instructions.qr_url)">Salin link QR</button></div>
                <a v-if="share.instructions?.app_url" :href="share.instructions.app_url" class="panel-primary block text-center">Buka aplikasi pembayaran</a>
                <p class="text-xs leading-6 text-slate-500">{{ share.method === 'mandiri' ? 'Gunakan pembayaran tagihan / multipayment Mandiri dengan kode perusahaan dan kode pembayaran di atas.' : share.method === 'bca' ? 'Pilih BCA Virtual Account pada aplikasi atau ATM BCA, lalu masukkan nomor di atas.' : ['alfamart','indomaret'].includes(share.method) ? 'Tunjukkan kode pembayaran kepada kasir gerai yang dipilih. Periksa nominal dan simpan struk.' : 'Periksa penerima dan nominal sebelum mengonfirmasi pembayaran.' }}</p>
            </template>
            <button type="button" :disabled="busy" class="panel-primary w-full" @click="check">{{ busy ? 'Memeriksa…' : 'Cek Status Pembayaran' }}</button>
            <p v-if="message" role="status" class="rounded-lg bg-blue-50 p-3 text-sm">{{ message }}</p>
            <p class="text-xs leading-6 text-slate-500">Ini pembayaran satu bagian, bukan seluruh booking. Pemesan mengelola tiket setelah semua bagian lunas. Jaga kerahasiaan link pembayaran ini.</p>
        </section>
    </main>
</template>
