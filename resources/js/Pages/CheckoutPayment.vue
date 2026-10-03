<script setup>
import { computed, onBeforeUnmount, onMounted, ref, watch } from 'vue';
import axios from 'axios';
import { Link, useForm, usePage } from '@inertiajs/vue3';
import { route } from 'ziggy-js';
import { CheckCircle2, ChevronDown, Clock3, Copy, Download, LockKeyhole, X } from 'lucide-vue-next';
import CheckoutLayout from '../Components/Shared/CheckoutLayout.vue';
import CheckoutSummary from '../Components/Shared/CheckoutSummary.vue';
import PaymentMethodPicker from '../Components/Shared/PaymentMethodPicker.vue';
import CheckoutPromoPicker from '../Components/Shared/CheckoutPromoPicker.vue';
const props = defineProps({ kind: String, order: Object, paymentMethods: Array, paymentPreferences: Object, corporateWorkspaceUrl: String, gatewayReady: Boolean, serverNow: String, promotions: Array, appliedPromotion: Object });
const order = ref(props.order);
watch(() => props.order, value => { order.value = value; });
const page = usePage();
const isTrip = computed(() => props.kind === 'trip');
const payment = computed(() => order.value.payment);
const selected = ref(payment.value.method || props.paymentMethods.find(method => method.id === props.paymentPreferences?.primary && method.enabled)?.id || '');
watch(() => payment.value.method, value => { if (value) selected.value = value; });
const form = useForm({ method: selected.value });
const check = ref({ processing: false });
const cancel = useForm({});
const confirmingCancel = ref(false);
const statusMessage = ref('');
const pollingPaused = ref(false);
const copied = ref('');
const guideRef = ref(null);
const detailDialog = ref(null);
const scrollToGuide = () => {
    if (guideRef.value) {
        guideRef.value.open = true;
        guideRef.value.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
    }
};
const now = ref(Date.parse(props.serverNow));
let tick; let polling; let anchor = performance.now(); let server = Date.parse(props.serverNow);
watch(() => props.serverNow, value => { server = Date.parse(value); anchor = performance.now(); now.value = server; });
const remaining = computed(() => Math.max(0, Math.floor((Date.parse(order.value.expires_at) - now.value)/1000)));
const countdown = computed(() => `${String(Math.floor(remaining.value/60)).padStart(2,'0')}:${String(remaining.value%60).padStart(2,'0')}`);
const isRetail = computed(() => ['alfamart', 'indomaret'].includes(payment.value.method));
const retailCountdown = computed(() => [
    { label: 'Jam', value: String(Math.floor(remaining.value / 3600)).padStart(2, '0') },
    { label: 'Menit', value: String(Math.floor(remaining.value / 60) % 60).padStart(2, '0') },
    { label: 'Detik', value: String(remaining.value % 60).padStart(2, '0') },
]);
const paymentDeadline = computed(() => new Intl.DateTimeFormat('id-ID', { day: '2-digit', month: 'short', year: 'numeric', hour: '2-digit', minute: '2-digit', hourCycle: 'h23', timeZone: 'Asia/Jakarta' }).format(new Date(order.value.expires_at)) + ' WIB');
const money = value => new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', maximumFractionDigits: 0 }).format(value || 0);
const hasInstructions = computed(() => Object.keys(payment.value.instructions || {}).length > 0);
const pending = computed(() => order.value.status === 'awaiting_payment' && payment.value.status === 'pending');
const paid = computed(() => payment.value.status === 'paid');
const method = computed(() => props.paymentMethods.find(item => item.id === payment.value.method));
const detailsUrl = computed(() => props.corporateWorkspaceUrl || route(isTrip.value ? 'bookings.show' : 'souvenirs.orders.show', props.order.id));
const charge = () => { form.method = selected.value; form.post(route('checkout.charge', { type: props.kind, id: props.order.id })); };
const checkStatus = async (automatic = false) => {
    if (check.value.processing || form.processing || cancel.processing || confirmingCancel.value) return;
    check.value.processing = true;
    try {
        const { data } = await axios.post(route('checkout.check', { type: props.kind, id: order.value.id }), { automatic: automatic === true }, { headers: { Accept: 'application/json' }, timeout: 8000 });
        order.value = { ...order.value, status: data.status, payment: data.payment };
        server = Date.parse(data.serverNow); anchor = performance.now(); now.value = server;
        pollingPaused.value = false;
        statusMessage.value = automatic === true ? '' : data.payment.status === 'paid' ? 'Pembayaran sudah terverifikasi.' : 'Pembayaran belum terverifikasi. Selesaikan pembayaran menggunakan instruksi di halaman ini.';
    } catch (error) {
        pollingPaused.value = true;
        statusMessage.value = error.response?.data?.message || 'Pemeriksaan belum berhasil.';
    } finally {
        check.value.processing = false;
    }
};
const cancelOrder = () => cancel.post(route('checkout.cancel', { type: props.kind, id: order.value.id }), { preserveScroll: true, onSuccess: () => { confirmingCancel.value = false; } });
const closed = computed(() => !pending.value || !remaining.value);
const closedTitle = computed(() => payment.value.status === 'reconciliation_required' ? 'Pembayaran sedang diperiksa' : order.value.status === 'cancelled' ? 'Pesanan dibatalkan' : order.value.status === 'expired' || !remaining.value ? 'Batas pembayaran berakhir' : 'Pesanan tidak dapat dibayar');
const closedDescription = computed(() => payment.value.status === 'reconciliation_required' ? 'Pembayaran diterima setelah reservasi ditutup. Hubungi bantuan untuk tindak lanjut.' : order.value.status === 'cancelled' ? 'Reservasi sudah dilepas. Jangan bayar kode pembayaran pesanan ini.' : 'Reservasi sudah berakhir. Buat pesanan baru jika kamu ingin melanjutkan; jangan bayar kode pesanan ini.');
const copy = async value => { try { await navigator.clipboard.writeText(value); copied.value = 'Kode disalin'; } catch { copied.value = 'Salin kode yang ditampilkan secara manual.'; } };
const codes = computed(() => Object.entries(payment.value.instructions || {}).filter(([key]) => ['va_number','payment_code','bill_key','biller_code'].includes(key)));
const codeLabels = { va_number: 'Nomor virtual account', payment_code: 'Kode pembayaran', bill_key: 'Kode pembayaran', biller_code: 'Kode perusahaan' };
const guideTitle = computed(() => ['alfamart', 'indomaret'].includes(payment.value.method) ? 'Cara membayar di ' + method.value?.name : payment.value.method === 'bca' ? 'Cara membayar dengan BCA Virtual Account' : payment.value.method === 'mandiri' ? 'Cara membayar dengan Mandiri Bill Payment' : payment.value.method === 'ovo' ? 'Cara membayar OVO melalui QRIS' : payment.value.instructions?.qr_url ? 'Cara membayar GoPay / QRIS' : 'Cara membayar dengan GoPay');
const guide = computed(() => {
    if (['alfamart', 'indomaret'].includes(payment.value.method)) return [
        `Datang ke <strong>${payment.value.method === 'indomaret' ? 'Indomaret' : 'Alfamart'}</strong>.`,
        'Tunjukkan <strong>kode pembayaran</strong> ke kasir.',
        'Bayar dengan uang tunai sesuai total pembayaran (sudah termasuk biaya layanan).',
        'Transaksi selesai, simpan bukti pembayaranmu.'
    ];
    if (payment.value.method === 'bca') return [
        'Salin <strong>nomor BCA Virtual Account</strong> yang ditampilkan di atas.',
        'Buka aplikasi atau ATM BCA, lalu pilih pembayaran <strong>BCA Virtual Account</strong>.',
        'Masukkan nomor virtual account pesanan ini, lalu periksa nama penerima dan nominal tagihan.',
        'Konfirmasi pembayaran sebelum batas waktu berakhir dan simpan bukti transaksi.',
        'Kembali ke halaman ini untuk melihat status pembayaran yang terverifikasi.'
    ];
    if (payment.value.method === 'mandiri') return [
        'Catat <strong>kode perusahaan</strong> dan <strong>kode pembayaran</strong> yang ditampilkan di atas.',
        'Buka kanal pembayaran Mandiri dan pilih pembayaran <strong>tagihan / multipayment</strong>.',
        'Masukkan kode perusahaan dan kode pembayaran sesuai instruksi pada kanal Mandiri.',
        'Periksa nominal, konfirmasi pembayaran sebelum batas waktu berakhir, lalu simpan bukti transaksi.',
        'Kembali ke halaman ini untuk melihat status pembayaran.'
    ];
    if (payment.value.method === 'ovo' || payment.value.instructions?.qr_url) return [
        'Gunakan <strong>QR pembayaran pesanan</strong> yang ditampilkan di atas.',
        'Buka ' + (payment.value.method === 'ovo' ? '<strong>aplikasi OVO</strong>' : '<strong>aplikasi GoPay / e-wallet</strong> yang mendukung QRIS') + ', lalu pilih menu pindai / scan QR.',
        'Pindai QR dari perangkat lain, atau pilih screenshot QR dari galeri jika tersedia.',
        'Pastikan penerima dan total tagihan sesuai, lalu konfirmasi pembayaran sebelum batas waktu berakhir.',
        'Setelah berhasil, kembali ke halaman ini untuk melihat status pembayaran yang terverifikasi.'
    ];
    return [
        'Tekan tombol <strong>Buka aplikasi pembayaran</strong> untuk melanjutkan ke GoPay.',
        'Periksa penerima dan nominal tagihan di aplikasi sebelum mengonfirmasi pembayaran.',
        'Selesaikan pembayaran sebelum batas waktu berakhir dan simpan bukti transaksi.',
        'Kembali ke halaman ini untuk melihat status pembayaran yang terverifikasi.'
    ];
});
const stripHtml = text => (typeof text === 'string' ? text.replace(/<[^>]*>/g, '') : text);
const download = () => { const text = [`TapakLokal · ${props.order.reference}`, method.value?.name, ...codes.value.map(([key,value]) => `${codeLabels[key]}: ${value}`), `Total: ${props.order.total}`, `Batas waktu: ${props.order.expires_at}`, ...guide.value.map(stripHtml)].join('\n'); const blob = new Blob([text], { type: 'text/plain;charset=utf-8' }); const url = URL.createObjectURL(blob); const link = document.createElement('a'); link.href = url; link.download = `${props.order.reference}-pembayaran.txt`; link.click(); URL.revokeObjectURL(url); };
onMounted(() => { tick = window.setInterval(() => { now.value = server + performance.now() - anchor; },1000); polling = window.setInterval(() => { if (!pollingPaused.value && pending.value && props.gatewayReady && document.visibilityState === 'visible' && (hasInstructions.value || payment.value.checkout_url)) checkStatus(true); },30000); });
onBeforeUnmount(() => { clearInterval(tick); clearInterval(polling); });
</script>
<template>
    <CheckoutLayout :title="paid ? 'Pembayaran terverifikasi' : closed ? closedTitle : hasInstructions ? 'Selesaikan pembayaran' : 'Pilih cara pembayaran'" :subtitle="paid ? 'Pesanan tercatat di akunmu. Pantau konfirmasi vendor dari detail pesanan.' : closed ? 'Lihat status akhir dan pilih langkah berikutnya.' : hasInstructions ? 'Bayar menggunakan instruksi di bawah. Status diperiksa otomatis di halaman ini.' : 'Pilih metode, buat kode pembayaran, lalu selesaikan pembayaran di halaman ini.'" :step="paid ? 3 : 2" :back-url="corporateWorkspaceUrl || route('account.section', 'bookings')" back-label="Pesanan saya">
        <p v-if="cancel.errors.status" role="alert" class="rounded-xl bg-red-50 p-4 text-sm text-red-700">{{ cancel.errors.status }}</p>
        <div v-if="pending && !isRetail && (hasInstructions || payment.checkout_url)" class="flex flex-wrap items-center justify-between gap-3 rounded-xl bg-[#0175ea] px-6 py-4 text-sm font-semibold text-white"><span class="flex items-center gap-2"><Clock3 class="size-5" />{{ remaining ? 'Batas waktu pembayaran' : 'Batas waktu telah berakhir' }}</span><span class="rounded-lg bg-white/15 px-3 py-1 font-mono text-lg" role="timer" aria-label="Sisa waktu pembayaran">{{ countdown }}</span></div>
        <p v-if="page.props.flash?.success" role="status" class="rounded-xl border border-emerald-200 bg-emerald-50 p-4 text-sm text-emerald-800">{{ page.props.flash.success }}</p><p v-if="page.props.flash?.error" role="alert" class="rounded-xl bg-red-50 p-4 text-sm text-red-700">{{ page.props.flash.error }}</p><div v-if="Object.keys(form.errors).length" role="alert" class="rounded-xl bg-red-50 p-4 text-sm text-red-700"><p v-for="(error,field) in form.errors" :key="field">{{ error }}</p></div>
        <section v-if="paid" class="rounded-2xl border border-emerald-200 bg-white p-8"><CheckCircle2 class="size-12 text-emerald-600" /><h2 class="mt-5 text-xl font-extrabold">Terima kasih, pembayaranmu sudah diterima.</h2><p class="mt-3 text-sm leading-7 text-slate-600">Vendor menerima pembaruan pesanan ini. Rincian perjalanan atau pengiriman, chat, dan bukti transaksi tersedia dari akun traveler.</p><Link :href="detailsUrl" class="mt-6 inline-flex min-h-12 items-center rounded-xl bg-[#0175ea] px-5 text-sm font-bold text-white">Lihat detail pesanan</Link></section>
        <section v-else-if="closed" class="rounded-2xl border border-slate-200 bg-white p-8">
            <h2 class="text-lg font-extrabold">{{ closedTitle }}</h2>
            <p class="mt-3 text-sm leading-7 text-slate-600">{{ closedDescription }}</p>
            <div class="mt-5 flex flex-wrap gap-4">
                <Link v-if="payment.status !== 'reconciliation_required'" :href="isTrip ? route('checkout.review.trip', order.trip_id) : route('souvenirs.index')" class="panel-primary">Buat pesanan baru</Link>
                <Link :href="payment.status === 'reconciliation_required' ? route('help.index') : route('account.section', 'bookings')" class="panel-secondary">{{ payment.status === 'reconciliation_required' ? 'Hubungi bantuan' : 'Pesanan saya' }}</Link>
            </div>
        </section>
        <section v-else-if="isRetail && payment.instructions?.payment_code" class="overflow-hidden rounded-2xl border border-[#d7e0eb] bg-white">
            <div class="p-5 sm:p-7">
                <div class="flex flex-wrap items-center justify-between gap-5 border-b border-slate-100 pb-5">
                    <div class="flex items-center gap-4"><span class="grid size-12 shrink-0 place-items-center rounded-full bg-[#edf4ff] text-[#3e7bef]"><Clock3 class="size-6" /></span><div><h2 class="text-xl font-extrabold text-[#17345e] sm:text-2xl">Bayar sebelum</h2><p class="mt-1 text-sm text-slate-500">{{ paymentDeadline }}</p></div></div>
                    <div class="flex items-start gap-2" role="timer" aria-label="Sisa waktu pembayaran"><div v-for="part in retailCountdown" :key="part.label" class="text-center"><span class="grid size-9 place-items-center rounded-lg bg-[#edf4ff] font-mono text-base font-bold text-[#17345e]">{{ part.value }}</span><span class="mt-1 block text-[10px] text-slate-500">{{ part.label }}</span></div></div>
                </div>
                <div class="mt-5 flex items-center justify-between gap-5">
                    <div class="min-w-0"><p class="text-sm text-slate-500">Kode Bayar</p><div class="mt-2 flex items-center gap-2"><strong class="break-all text-xl font-extrabold tracking-wide text-[#17345e] sm:text-2xl">{{ payment.instructions.payment_code }}</strong><button type="button" aria-label="Salin kode bayar" class="grid size-9 shrink-0 place-items-center rounded-lg text-[#3e7bef] transition hover:bg-[#edf4ff] focus-visible:outline-2 focus-visible:outline-[#3e7bef]" @click="copy(payment.instructions.payment_code)"><Copy class="size-4" /></button></div></div>
                    <img v-if="method?.logo" :src="method.logo" :alt="method.name" width="96" height="40" class="max-h-10 w-20 shrink-0 object-contain sm:w-24" />
                </div>
                <div class="mt-5"><p class="text-sm text-slate-500">Total Tagihan</p><div class="mt-2 flex flex-wrap items-center justify-between gap-3"><div class="flex items-center gap-2"><strong class="text-xl font-extrabold text-[#17345e] sm:text-2xl">{{ money(order.total) }}</strong><button type="button" aria-label="Salin total tagihan" class="grid size-9 place-items-center rounded-lg text-[#3e7bef] transition hover:bg-[#edf4ff] focus-visible:outline-2 focus-visible:outline-[#3e7bef]" @click="copy(String(order.total))"><Copy class="size-4" /></button></div><button type="button" class="min-h-10 text-sm font-extrabold text-[#3e7bef] hover:underline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-[#3e7bef]" aria-haspopup="dialog" @click="detailDialog.showModal()">Lihat Detail</button></div></div>
                <p v-if="copied" role="status" class="mt-3 text-xs text-[#3e7bef]">{{ copied }}</p>
                <div class="mt-5 grid gap-3 sm:grid-cols-2">
                    <Link :href="route('account.section', 'payments')" class="text-center content-center min-h-12 rounded-lg border border-[#3e7bef] bg-white px-5 text-sm font-semibold text-[#3e7bef] transition hover:bg-[#edf4ff] focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-[#3e7bef]">Lihat Cara Bayar</Link>
                    <Link :href="route('account.section', 'payments')" class="text-center content-center min-h-12 rounded-lg border border-[#3e7bef] bg-white px-5 text-sm font-semibold text-[#3e7bef] transition hover:bg-[#edf4ff] focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-[#3e7bef]">Cek Status Pembayaran</Link>
                </div>
            </div>
            <div class="border-t-8 border-[#f5f7fa] px-5 py-6 sm:px-7"><h3 class="text-lg font-bold text-[#17345e]">Cara pembayaran</h3><details ref="guideRef" class="group mt-4"><summary class="flex min-h-12 cursor-pointer list-none items-center justify-between gap-3 border-b border-slate-100 py-3 text-sm font-semibold text-[#17345e] focus-visible:outline-2 focus-visible:outline-[#3e7bef] [&::-webkit-details-marker]:hidden"><span>Instruksi Pembayaran {{ method?.name }}</span><ChevronDown class="size-4 shrink-0 text-slate-500 transition-transform group-open:rotate-180" /></summary><ol class="space-y-3 pt-4 text-sm leading-6 text-slate-600"><li v-for="(item, index) in guide" :key="index" class="flex gap-3"><span class="font-semibold text-[#3e7bef]">{{ index + 1 }}.</span><span v-html="item"></span></li></ol></details></div>
        </section>
        <section v-else-if="hasInstructions" class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-xs"><div class="flex flex-wrap items-center justify-between gap-4 border-b border-slate-100 bg-[#edf6ff] p-6"><div class="flex min-w-0 items-center gap-4"><span v-if="method?.logo" class="grid h-14 w-24 shrink-0 place-items-center rounded-xl border border-blue-100 bg-white px-3"><img :src="method.logo" :alt="method.badge" width="96" height="40" class="max-h-10 w-full object-contain" /></span><div class="min-w-0"><p class="text-xs font-bold uppercase tracking-wide text-blue-600">Instruksi pembayaran</p><h2 class="mt-2 text-lg font-extrabold">{{ method?.name }}</h2></div></div><button type="button" class="inline-flex min-h-10 items-center gap-2 rounded-lg border border-blue-200 bg-white px-3 text-xs font-bold text-blue-600 transition hover:border-blue-300 hover:bg-blue-50" @click="download"><Download class="size-4" />Unduh panduan</button></div><div class="p-6"><div v-for="[key,value] in codes" :key="key" class="mb-4 rounded-xl border border-slate-200 bg-slate-50 p-5"><p class="text-xs text-slate-500">{{ codeLabels[key] }}</p><div class="mt-3 flex items-center justify-between gap-4"><p class="break-all font-mono text-xl font-extrabold sm:text-2xl">{{ value }}</p><button type="button" :aria-label="`Salin ${codeLabels[key]}`" class="grid size-11 shrink-0 place-items-center rounded-lg border border-slate-200 bg-white text-blue-600 transition hover:border-blue-300 hover:bg-blue-50" @click="copy(value)"><Copy class="size-4" /></button></div></div><div v-if="payment.instructions.qr_url && remaining" class="rounded-xl border border-slate-200 p-5 text-center"><img :src="payment.instructions.qr_url" alt="QR pembayaran pesanan dari Midtrans" width="240" height="240" class="mx-auto size-60 max-w-full object-contain" /><p class="mt-4 text-xs leading-6 text-slate-500">Pindai QR ini. Jangan gunakan QR dari pihak lain untuk pesanan yang sama.</p></div><div class="mt-5 grid grid-cols-1 gap-3 sm:grid-cols-2"><Link :href="route('account.section', 'payments')" class="text-center content-center flex min-h-11 items-center justify-center rounded-xl border border-[#0175ea] bg-white px-5 py-2.5 text-sm font-bold text-[#0175ea] shadow-xs transition-all duration-200 hover:border-[#0088ff] hover:bg-[#edf6ff] hover:text-[#0088ff] focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-[#0175ea] active:scale-[0.99]">Lihat Cara Bayar</Link><Link :href="route('account.section', 'payments')" class="text-center content-center flex min-h-11 items-center justify-center rounded-xl border border-[#0175ea] bg-white px-5 py-2.5 text-sm font-bold text-[#0175ea] shadow-xs transition-all duration-200 hover:border-[#0088ff] hover:bg-[#edf6ff] hover:text-[#0088ff] focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-[#0175ea] active:scale-[0.99]">Cek Status Pembayaran</Link></div><p v-if="copied" role="status" class="mt-3 text-xs text-blue-600">{{ copied }}</p><div class="mt-8 pt-2"><h3 class="text-base font-bold text-slate-900">Cara pembayaran</h3><details ref="guideRef" class="group mt-3 overflow-hidden rounded-xl border border-slate-200 bg-white shadow-2xs" open><summary class="flex min-h-12 cursor-pointer list-none items-center justify-between gap-3 bg-white px-5 py-3.5 text-sm font-bold text-slate-800 transition hover:bg-slate-50 focus-visible:outline-2 focus-visible:outline-[#0175ea] [&::-webkit-details-marker]:hidden"><span>Instruksi Pembayaran</span><ChevronDown class="size-4 shrink-0 text-slate-500 transition-transform duration-200 group-open:rotate-180" /></summary><ol class="space-y-3 border-t border-slate-200 p-5 text-sm leading-relaxed text-slate-600"><li v-for="(item, index) in guide" :key="index" class="flex gap-2.5"><span class="font-medium text-slate-500">{{ index + 1 }}.</span><span v-html="item"></span></li></ol><a v-if="payment.instructions?.app_url && remaining" :href="payment.instructions.app_url" class="mx-5 mb-5 flex min-h-11 items-center justify-center rounded-lg bg-blue-50 px-4 py-3 text-sm font-bold text-[#0175ea] transition hover:bg-blue-100">Lanjut ke aplikasi pembayaran</a></details></div></div></section>
        <section v-else class="rounded-2xl border border-slate-200 bg-white p-6 sm:p-7"><p class="mb-5 text-xs leading-6 text-slate-500">Kursi atau stok direservasi sampai {{ new Date(order.expires_at).toLocaleString('id-ID') }}. Instruksi pembayaran dibuat setelah kamu menekan tombol bayar.</p><div class="mb-6 flex flex-wrap items-center justify-between gap-3"><h2 class="text-lg font-extrabold">Bayar dengan metode pilihanmu</h2><span class="flex items-center gap-1.5 text-[11px] text-slate-500"><LockKeyhole class="size-3.5 text-blue-600" />Dikelola Midtrans</span></div><template v-if="payment.checkout_url"><p class="text-sm text-slate-600">Instruksi pembayaran sudah dibuat untuk pesanan ini.</p><a :href="payment.checkout_url" class="mt-5 block rounded-xl bg-blue-600 px-5 py-3 text-center font-bold text-white">Lanjutkan pembayaran yang tersimpan</a></template><template v-else><p v-if="payment.method" class="mb-5 rounded-xl bg-blue-50 p-4 text-sm leading-6 text-blue-800">Permintaan pembayaran sudah dikirim. Coba kembali dengan metode yang sama agar transaksi tidak dibuat ganda.</p><PaymentMethodPicker v-model="selected" :methods="paymentMethods" :preferences="paymentPreferences" :disabled="form.processing || !remaining || !!payment.method" /><CheckoutPromoPicker v-if="!corporateWorkspaceUrl" :kind="kind" :order-id="order.id" :promotions="promotions" :applied-promotion="appliedPromotion" :discount="order.discount" :disabled="form.processing || cancel.processing || check.processing || !remaining" :locked="!!payment.method || !!payment.checkout_url" /><p v-if="!gatewayReady" role="status" class="mt-6 rounded-xl bg-amber-50 p-4 text-sm leading-6 text-amber-900">Pembayaran online belum aktif. Pesanan tersimpan di akunmu; hubungi bantuan untuk tindak lanjut.</p><button type="button" :disabled="!paymentMethods.some(item => item.id === selected && item.enabled) || !gatewayReady || form.processing || !remaining" class="mt-7 min-h-12 w-full rounded-xl bg-[#0175ea] px-5 text-sm font-bold text-white hover:bg-blue-700 disabled:opacity-50" @click="charge">{{ form.processing ? 'Menyiapkan pembayaran…' : `Bayar dengan ${paymentMethods.find(item => item.id === selected)?.name || 'metode pilihan'}` }}</button></template></section>
        <p v-if="form.processing" role="status" class="rounded-xl bg-blue-50 p-4 text-sm text-blue-800">Sedang meminta kode pembayaran ke Midtrans. Jika penyedia tidak merespons, pesan kesalahan akan muncul. Pesananmu tetap tersimpan.</p>
        <dialog ref="detailDialog" class="fixed inset-0 m-auto max-h-[90dvh] w-[calc(100%-2rem)] max-w-xl overflow-hidden rounded-2xl bg-white p-0 text-[#17345e] shadow-2xl backdrop:bg-slate-900/50" aria-labelledby="payment-details-heading" @click.self="detailDialog.close()">
            <div class="flex max-h-[90dvh] flex-col">
                <header class="flex shrink-0 items-center justify-between gap-4 border-b border-slate-100 px-5 py-5 sm:px-6"><h2 id="payment-details-heading" class="text-xl font-extrabold">Detail Pembayaran</h2><button type="button" aria-label="Tutup detail pembayaran" class="grid size-9 place-items-center rounded-full text-slate-500 hover:bg-slate-100 focus-visible:outline-2 focus-visible:outline-[#3e7bef]" @click="detailDialog.close()"><X class="size-5" /></button></header>
                <div class="min-h-0 overflow-y-auto">
                    <section class="px-5 py-5 sm:px-6"><h3 class="text-sm font-bold">Rincian tagihan</h3><dl class="mt-4 space-y-3 text-sm"><div class="flex justify-between gap-4"><dt class="text-slate-500">{{ isTrip ? 'Harga perjalanan (' + order.participants + ' peserta)' : 'Total harga barang' }}</dt><dd class="shrink-0 font-semibold">{{ money(order.subtotal) }}</dd></div><div v-if="order.discount" class="flex justify-between gap-4"><dt class="text-slate-500">Potongan promo<template v-if="appliedPromotion"> · {{ appliedPromotion.code }}</template></dt><dd class="shrink-0 font-semibold text-[#3e7bef]">−{{ money(order.discount) }}</dd></div><div v-if="!isTrip && order.method === 'delivery'" class="flex justify-between gap-4"><dt class="text-slate-500">Ongkos kirim</dt><dd class="shrink-0 font-semibold">{{ money(order.shipping_fee) }}</dd></div><div class="flex items-center justify-between gap-4 border-t border-slate-100 pt-4 text-base font-extrabold"><dt>Total Bayar</dt><dd class="text-[#3e7bef]">{{ money(order.total) }}</dd></div></dl><p class="mt-3 text-xs leading-5 text-slate-500">Jika gerai mengenakan biaya tambahan, konfirmasikan nominalnya kepada kasir sebelum membayar.</p></section>
                    <section class="border-t border-slate-100 px-5 py-5 sm:px-6"><h3 class="text-sm font-bold">Metode pembayaran</h3><div class="mt-4 flex items-center gap-3"><img v-if="method?.logo" :src="method.logo" :alt="method.name" width="72" height="30" class="max-h-8 w-18 shrink-0 object-contain" /><div class="min-w-0 flex-1"><p class="text-sm font-semibold">{{ method?.name }}</p><p class="mt-1 text-xs text-slate-500">{{ paid ? 'Pembayaran terverifikasi' : 'Menunggu pembayaran' }}</p></div><strong class="shrink-0 text-sm">{{ money(order.total) }}</strong></div></section>
                    <section class="border-t-8 border-[#f5f7fa] px-5 py-5 sm:px-6"><h3 class="text-base font-extrabold">Detail pesanan</h3><p class="mt-3 text-sm font-bold">{{ order.vendor.name }}</p><p class="mt-1 break-all font-mono text-xs text-slate-500">{{ order.reference }}</p><template v-if="isTrip"><p class="mt-4 text-sm font-semibold">{{ order.trip.title }}</p><dl class="mt-3 space-y-2 text-xs text-slate-600"><div class="flex justify-between gap-4"><dt>Peserta</dt><dd>{{ order.participants }} orang</dd></div><div class="flex justify-between gap-4"><dt>Keberangkatan</dt><dd>{{ new Date(order.trip.departure_date).toLocaleDateString('id-ID', { day: 'numeric', month: 'short', year: 'numeric' }) }}</dd></div><div><dt class="text-slate-500">Titik kumpul</dt><dd class="mt-1 leading-5">{{ order.trip.meeting_point }}</dd></div></dl></template><div v-else class="mt-4 space-y-4"><article v-for="item in order.items" :key="item.id" class="flex justify-between gap-4 text-sm"><div><p class="font-semibold">{{ item.name }}</p><p class="mt-1 text-xs text-slate-500">{{ item.variant }} · {{ item.quantity }} unit</p></div><strong class="shrink-0">{{ money(item.unit_price * item.quantity) }}</strong></article><p class="text-xs text-slate-500">{{ order.method === 'pickup' ? 'Ambil di tempat' : 'Kirim ke rumah' }}</p><p v-if="order.address" class="whitespace-pre-line text-xs leading-5 text-slate-600">{{ order.address }}</p></div><div class="mt-5 border-t border-slate-100 pt-4"><p class="text-xs text-slate-500">Pemesan</p><p class="mt-1 text-sm font-semibold">{{ order.contact_name }}</p><p class="mt-1 text-xs text-slate-500">{{ order.contact_phone }}</p></div></section>
                </div>
                <footer class="shrink-0 border-t border-slate-100 px-5 py-4 sm:px-6"><button type="button" class="min-h-11 w-full rounded-lg bg-[#3e7bef] text-sm font-semibold text-white hover:bg-[#3268ce] focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-[#3e7bef]" @click="detailDialog.close()">Kembali ke pembayaran</button></footer>
            </div>
        </dialog>
        <template #summary><CheckoutSummary :title="isTrip ? order.trip.title : 'Pesanan oleh-oleh'" :vendor="order.vendor.name" :image="order.trip?.image_url" :reference="order.reference" :date="isTrip ? order.trip.departure_date : order.pickup_date" :end-date="order.trip?.end_date" :participants="isTrip ? order.participants : null" :lines="order.items" :method="isTrip ? null : order.method" :address="isTrip ? order.trip.meeting_point : order.address" :subtotal="order.subtotal" :shipping="order.shipping_fee" :discount="order.discount" :total="order.total" /></template>
    </CheckoutLayout>
</template>
