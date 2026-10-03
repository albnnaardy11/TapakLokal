<script setup>
import { computed, onBeforeUnmount, onMounted, ref } from 'vue';
import { Link, router, usePage } from '@inertiajs/vue3';
import axios from 'axios';
import { route } from 'ziggy-js';
import { Clock3 } from 'lucide-vue-next';
import Pagination from '../Admin/Pagination.vue';
const props = defineProps({ pendingOnly: Boolean });
const page = usePage();
const filter = ref(props.pendingOnly ? 'pending' : 'all');
const copied = ref('');
const now = ref(Date.now());
let tick = null;
let polling = null;
let disposed = false;
const checking = ref(false);
const statusMessage = ref('');
const checkPayments = async (automatic = false) => {
    if (checking.value || document.visibilityState !== 'visible') return;
    const pending = transactions.value.filter(item => item.payment.status === 'pending' && item.payment.method).slice(0, 10);
    if (!pending.length) return;
    checking.value = true;
    let changed = false;
    let failed = false;
    for (const item of pending) {
        if (disposed) break;
        try {
            const { data } = await axios.post(item.payment.instructions?.split_bill ? route('split.owner.status', item.id) : route('checkout.check', { type: item.kind, id: item.id }), { automatic: true }, { headers: { Accept: 'application/json' }, timeout: 8000 });
            changed ||= data.payment?.status !== item.payment.status;
        } catch {
            failed = true;
        }
    }
    checking.value = false;
    if (disposed) return;
    if (!automatic) statusMessage.value = failed ? 'Status belum dapat diperiksa. Coba lagi sebentar.' : changed ? 'Status pembayaran diperbarui.' : 'Pembayaran masih menunggu konfirmasi.';
    if (changed) router.reload({ only: ['records', 'souvenirOrders'], preserveScroll: true });
};
onMounted(() => {
    tick = setInterval(() => { now.value = Date.now(); }, 1000);
    checkPayments(true);
    polling = setInterval(() => checkPayments(true), 30000);
});
onBeforeUnmount(() => { disposed = true; if (tick) clearInterval(tick); if (polling) clearInterval(polling); });
const getRemaining = expiresAt => expiresAt ? Math.max(0, Math.floor((Date.parse(expiresAt) - now.value) / 1000)) : 0;
const getCountdownParts = expiresAt => {
    const sec = getRemaining(expiresAt);
    return [
        { label: 'Jam', value: String(Math.floor(sec / 3600)).padStart(2, '0') },
        { label: 'Menit', value: String(Math.floor((sec % 3600) / 60)).padStart(2, '0') },
        { label: 'Detik', value: String(sec % 60).padStart(2, '0') },
    ];
};
const paymentDialog = ref(null);
const selectedPayment = ref(null);
const showPayment = item => { selectedPayment.value = item; copied.value = ''; paymentDialog.value.showModal(); };
const paymentGuide = computed(() => {
    const item = selectedPayment.value;
    if (!item) return [];
    if (['alfamart', 'indomaret'].includes(item.payment.method)) return [
        'Datang ke ' + (item.payment.method === 'indomaret' ? 'Indomaret' : 'Alfamart / Alfamidi') + ' sebelum batas pembayaran.',
        'Sampaikan pembayaran Midtrans kepada kasir dan tunjukkan kode pembayaran pesanan ini.',
        'Periksa nominal yang disebutkan kasir. Konfirmasikan biaya tambahan gerai jika ada sebelum membayar.',
        'Selesaikan pembayaran dan simpan struk sebagai bukti transaksi.'
    ];
    if (item.payment.method === 'bca') return ['Salin nomor virtual account pesanan ini.', 'Buka aplikasi atau ATM BCA dan pilih BCA Virtual Account.', 'Masukkan nomor virtual account, lalu periksa penerima dan nominal.', 'Konfirmasikan pembayaran dan simpan bukti transaksi.'];
    if (item.payment.method === 'mandiri') return ['Catat kode perusahaan dan kode pembayaran pesanan.', 'Buka kanal pembayaran Mandiri dan pilih pembayaran tagihan / multipayment.', 'Masukkan kedua kode sesuai kolom yang tersedia, lalu periksa nominal.', 'Konfirmasikan pembayaran sebelum batas waktu dan simpan bukti transaksi.'];
    if (item.payment.instructions?.qr_url) return ['Buka aplikasi bank atau dompet digital yang mendukung QR pembayaran ini.', 'Pindai QR dari perangkat lain atau gunakan gambar QR melalui galeri jika didukung.', 'Periksa nama penerima dan jumlah tagihan sebelum mengonfirmasi.', 'Selesaikan pembayaran dan simpan bukti transaksi.'];
    return ['Buka instruksi pembayaran pesanan melalui tombol di bawah.', 'Periksa penerima dan nominal sebelum mengonfirmasi pembayaran.', 'Selesaikan pembayaran sebelum batas waktu dan simpan bukti transaksi.'];
});
const money = value => new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', maximumFractionDigits: 0 }).format(value || 0);
const date = (value, time = false) => value ? new Intl.DateTimeFormat('id-ID', { day: 'numeric', month: 'short', year: 'numeric', ...(time ? { hour: '2-digit', minute: '2-digit' } : {}), timeZone: 'Asia/Jakarta' }).format(new Date(value)) + (time ? ' WIB' : '') : '—';
const transactions = computed(() => [
    ...(page.props.records?.data || []).map(payment => ({ key: `trip-${payment.id}`, kind: 'trip', id: payment.booking_id, title: 'Perjalanan', reference: payment.booking?.reference || payment.reference, created: payment.created_at, expires: payment.booking?.expires_at, payment })),
    ...(page.props.souvenirOrders?.data || []).map(order => ({ key: `souvenir-${order.id}`, kind: 'souvenir', id: order.id, title: order.vendor?.name || 'Oleh-oleh', description: order.items?.map(item => item.name).join(', '), reference: order.reference, created: order.created_at, expires: order.expires_at, payment: order.payment || { status: order.status, amount: order.total } })),
].sort((a, b) => Date.parse(b.created) - Date.parse(a.created)));
const state = item => item.payment.status === 'pending' && item.expires && Date.parse(item.expires) <= now.value ? 'expired' : item.payment.status;
const labels = { pending: 'Menunggu pembayaran', paid: 'Pembayaran berhasil', expired: 'Kedaluwarsa', cancelled: 'Dibatalkan', failed: 'Pembayaran gagal', refunded: 'Dikembalikan', reconciliation_required: 'Pembayaran perlu diperiksa' };
const groups = computed(() => [...new Set(transactions.value.map(state))].sort((a, b) => a === 'pending' ? -1 : b === 'pending' ? 1 : 0).map(status => ({ status, label: labels[status] || status, items: transactions.value.filter(item => state(item) === status && (!props.pendingOnly || status === 'pending') && (filter.value === 'all' || filter.value === status)) })).filter(group => group.items.length));
const method = item => page.props.paymentMethods?.find(method => method.id === item.payment.method);
const codes = item => Object.entries(item.payment.instructions || {}).filter(([key]) => ['payment_code', 'va_number', 'bill_key', 'biller_code'].includes(key));
const codeLabels = { payment_code: 'Kode pembayaran', va_number: 'Nomor virtual account', bill_key: 'Kode pembayaran', biller_code: 'Kode perusahaan' };
const copy = async value => { try { await navigator.clipboard.writeText(String(value)); copied.value = 'Kode pembayaran berhasil disalin.'; } catch { copied.value = 'Salin kode pembayaran secara manual.'; } };
</script>
<template>
    <section class="space-y-6 text-[#17345e]" aria-label="Daftar transaksi akun">
        <div v-if="transactions.some(item => item.payment.status === 'pending' && item.payment.method)" class="flex flex-wrap items-center justify-end gap-3">
            <p v-if="statusMessage" role="status" class="text-xs text-slate-500">{{ statusMessage }}</p>
            <button type="button" :disabled="checking" @click="checkPayments(false)" class="min-h-10 rounded-lg border border-[#3e7bef] bg-white px-4 text-xs font-bold text-[#3e7bef] disabled:opacity-50">{{ checking ? 'Memeriksa pembayaran…' : 'Cek Status Pembayaran' }}</button>
        </div>
        <div v-if="!pendingOnly" class="rounded-2xl border border-[#dce6f4] bg-white p-5">
            <p class="text-sm font-bold">Riwayat pembayaranmu</p><p class="mt-1 text-xs leading-5 text-slate-500">Kelola pembayaran perjalanan dan produk lokal dalam satu tempat.</p>
            <div class="mt-4 flex flex-wrap gap-2" aria-label="Filter status transaksi"><button v-for="[value, label] in [['all', 'Semua'], ['pending', 'Menunggu pembayaran'], ['paid', 'Berhasil'], ['expired', 'Kedaluwarsa'], ['cancelled', 'Dibatalkan']]" :key="value" type="button" :aria-pressed="filter === value" class="min-h-10 rounded-lg border px-3 text-xs font-semibold transition focus-visible:outline-2 focus-visible:outline-[#3e7bef]" :class="filter === value ? 'border-[#3e7bef] bg-[#edf4ff] text-[#3e7bef]' : 'border-slate-200 text-slate-500 hover:border-blue-200'" @click="filter = value">{{ label }}</button></div>
        </div>
        <p v-if="copied" role="status" class="text-xs font-semibold text-[#3e7bef]">{{ copied }}</p>
        <section v-for="group in groups" :key="group.status" class="space-y-3">
            <h3 class="flex items-center gap-2 text-sm font-bold">{{ group.label }}<span class="rounded-full bg-[#edf4ff] px-2 py-0.5 text-xs text-[#3e7bef]">{{ group.items.length }}</span></h3>
            <article v-for="item in group.items" :key="item.key" class="overflow-hidden rounded-2xl border border-[#dce6f4] bg-white shadow-xs">
                <header class="flex flex-wrap items-start justify-between gap-3 border-b border-slate-100 px-5 py-4 sm:px-6">
                    <div><div class="flex flex-wrap items-center gap-3"><h4 class="text-sm font-bold">{{ item.title }}</h4><span class="text-xs text-slate-500">{{ date(item.created) }}</span></div><p class="mt-1.5 break-all text-[11px] text-slate-400">{{ item.reference }}</p><p v-if="item.description" class="mt-1 text-xs text-slate-500">{{ item.description }}</p></div>
                    <div v-if="group.status === 'pending' && item.expires" class="flex flex-col items-end gap-1.5 sm:flex-row sm:items-center sm:gap-3.5">
                        <div class="hidden text-right sm:block">
                            <p class="text-[11px] font-semibold text-rose-500">Bayar sebelum</p>
                            <p class="text-[11px] font-bold text-slate-700">{{ date(item.expires, true) }}</p>
                        </div>
                        <div class="flex items-start gap-1.5" role="timer" aria-label="Sisa waktu pembayaran">
                            <div v-for="part in getCountdownParts(item.expires)" :key="part.label" class="text-center">
                                <span class="grid size-9 place-items-center rounded-xl border border-rose-200 bg-rose-50 font-mono text-base font-extrabold text-rose-600 shadow-2xs">{{ part.value }}</span>
                                <span class="mt-1 block text-[10px] font-medium text-slate-500">{{ part.label }}</span>
                            </div>
                        </div>
                    </div><span v-else class="rounded-lg bg-slate-50 px-3 py-2 text-xs font-semibold">{{ group.label }}</span>
                </header>
                <div class="p-5 sm:p-6">
                    <div class="grid gap-5 lg:grid-cols-[1fr_1fr_auto]">
                        <div class="flex items-center gap-3"><img v-if="method(item)?.logo" :src="method(item).logo" :alt="method(item).name" width="72" height="40" class="h-10 w-18 shrink-0 object-contain" /><div><p class="text-xs text-slate-500">Metode pembayaran</p><p class="mt-1 text-sm font-bold">{{ method(item)?.name || 'Belum dipilih' }}</p></div></div>
                        <div class="border-t border-slate-100 pt-4 lg:border-t-0 lg:border-l lg:pt-0 lg:pl-5"><template v-if="codes(item).length"><div v-for="[key, value] in codes(item)" :key="key" class="mb-2 last:mb-0"><p class="text-xs text-slate-500">{{ codeLabels[key] }}</p><button type="button" :aria-label="`Salin ${codeLabels[key]}`" class="mt-1 break-all text-left text-sm font-extrabold hover:text-[#3e7bef] focus-visible:outline-2 focus-visible:outline-[#3e7bef]" @click="copy(value)">{{ value }}</button></div><p class="mt-2 text-[10px] text-slate-400">Tekan kode untuk menyalin</p></template><template v-else><p class="text-xs text-slate-500">Informasi pembayaran</p><p class="mt-1 text-sm font-semibold">{{ item.payment.instructions?.split_bill ? 'Split bill per anggota' : item.payment.instructions?.qr_url ? 'Pembayaran QR' : group.status === 'pending' ? 'Buat instruksi pembayaran' : group.label }}</p></template></div>
                        <div class="border-t border-slate-100 pt-4 lg:border-t-0 lg:border-l lg:pt-0 lg:pl-5"><p class="text-xs text-slate-500">Total pembayaran</p><p class="mt-1 text-lg font-extrabold">{{ money(item.payment.amount) }}</p></div>
                    </div>
                    <div class="mt-6 flex flex-col gap-3 border-t border-slate-100 pt-5 sm:flex-row sm:justify-end"><Link :href="route(item.kind === 'trip' ? 'bookings.show' : 'souvenirs.orders.show', item.id)" class="inline-flex min-h-11 items-center justify-center rounded-lg border border-[#3e7bef] px-6 text-sm font-bold text-[#3e7bef] transition hover:bg-[#edf4ff]">Lihat Detail</Link><Link v-if="item.payment.instructions?.split_bill" :href="route('checkout.payment', { type: 'trip', id: item.id })" class="panel-primary">Kelola split bill</Link><button v-else-if="group.status === 'pending'" type="button" @click="showPayment(item)" class="inline-flex min-h-11 items-center justify-center rounded-lg bg-[#3e7bef] px-6 text-sm font-bold text-white transition hover:bg-[#2866d4]">Lihat Cara Bayar</button></div>
                </div>
            </article>
        </section>
        <div v-if="!groups.length" class="overflow-hidden rounded-2xl border border-[#dce6f4] bg-white p-8 sm:p-12 shadow-[0_4px_24px_rgba(23,75,120,0.04)]">
            <div class="mx-auto flex max-w-lg flex-col items-center justify-center text-center">
                <img
                    src="/Assets/Images/account/notfound.svg"
                    alt="Tidak ada tagihan menunggu pembayaran"
                    class="mx-auto h-48 w-auto max-w-full object-contain sm:h-56"
                    loading="lazy"
                />
                <div class="mt-6 max-w-md">
                    <p class="text-[11px] font-bold uppercase tracking-[0.14em] text-[#3e7bef]">
                        {{ pendingOnly || filter === 'pending' ? 'Menunggu Pembayaran' : 'Tagihan & Transaksi' }}
                    </p>
                    <h3 class="mt-2 text-lg font-extrabold leading-snug text-[#17345e] sm:text-xl">
                        {{ pendingOnly || filter === 'pending' ? 'Tidak ada tagihan menunggu pembayaran' : 'Belum ada transaksi untuk status ini' }}
                    </h3>
                    <p class="mt-3 text-xs leading-6 text-slate-500">
                        {{ pendingOnly || filter === 'pending'
                            ? 'Semua transaksi sudah dibayar atau belum ada pesanan baru yang dibuat. Kamu dapat memesan paket trip atau oleh-oleh lokal kapan saja.'
                            : 'Aktivitas akunmu akan muncul di sini setelah pesanan dibuat atau status transaksi diperbarui.' }}
                    </p>
                </div>
                <div class="mt-6 flex flex-wrap items-center justify-center gap-3">
                    <button
                        v-if="!pendingOnly && filter !== 'all'"
                        type="button"
                        class="min-h-11 rounded-xl border border-[#3e7bef] bg-[#edf4ff] px-5 text-xs font-bold text-[#3e7bef] transition hover:bg-[#dcecff]"
                        @click="filter = 'all'"
                    >
                        Tampilkan Semua Transaksi
                    </button>
                    <Link
                        :href="route('catalog')"
                        class="inline-flex min-h-11 items-center justify-center rounded-xl bg-[#3e7bef] px-6 text-xs font-bold text-white shadow-xs transition hover:bg-[#2866d4]"
                    >
                        Temukan perjalanan berikutnya
                    </Link>
                    <Link
                        v-if="pendingOnly || filter === 'pending'"
                        :href="route('souvenirs.index')"
                        class="inline-flex min-h-11 items-center justify-center rounded-xl border border-[#cbdcf8] bg-white px-5 text-xs font-bold text-[#3e7bef] transition hover:bg-[#edf4ff]"
                    >
                        Beli Oleh-Oleh
                    </Link>
                </div>
            </div>
        </div>
        <div v-if="page.props.records?.last_page > 1" class="rounded-xl border border-[#dce6f4] bg-white p-3"><Pagination :records="page.props.records" /></div>
        <div v-if="page.props.souvenirOrders?.last_page > 1" class="rounded-xl border border-[#dce6f4] bg-white p-3"><Pagination :records="page.props.souvenirOrders" /></div>
        <dialog ref="paymentDialog" aria-labelledby="payment-guide-heading" class="fixed inset-0 m-auto max-h-[90dvh] w-[calc(100%-2rem)] max-w-xl overflow-hidden rounded-2xl bg-white p-0 text-[#17345e] shadow-2xl backdrop:bg-slate-900/50" @click.self="paymentDialog.close()">
            <div v-if="selectedPayment" class="flex max-h-[90dvh] flex-col">
                <header class="flex shrink-0 items-center justify-between border-b border-[#e2eaf5] px-6 py-5"><div><p class="text-[10px] font-bold uppercase tracking-wider text-[#3e7bef]">TapakLokal</p><h2 id="payment-guide-heading" class="mt-1 text-xl font-extrabold">Cara Pembayaran</h2></div><button type="button" aria-label="Tutup cara pembayaran" class="size-10 rounded-lg text-2xl hover:bg-[#edf4ff]" @click="paymentDialog.close()">×</button></header>
                <div class="overflow-y-auto p-6">
                    <div class="flex items-center justify-between gap-4"><h3 class="text-base font-bold">{{ method(selectedPayment)?.name || 'Metode belum dipilih' }}</h3><img v-if="method(selectedPayment)?.logo" :src="method(selectedPayment).logo" :alt="method(selectedPayment).name" class="h-9 w-20 object-contain" /></div>
                    <p class="mt-2 break-all text-[11px] text-slate-400">{{ selectedPayment.reference }}</p>
                    <div class="mt-5 space-y-4 rounded-xl border border-[#dce6f4] bg-[#f5f8ff] p-4"><div v-for="[key, value] in codes(selectedPayment)" :key="key"><p class="text-xs text-slate-500">{{ codeLabels[key] }}</p><div class="mt-1 flex items-center justify-between gap-3"><p class="break-all text-lg font-extrabold">{{ value }}</p><button type="button" class="min-h-9 px-2 text-xs font-bold text-[#3e7bef]" @click="copy(value)">Salin</button></div></div><div class="flex items-center justify-between gap-3"><span class="text-xs text-slate-500">Total pembayaran</span><strong class="text-lg">{{ money(selectedPayment.payment.amount) }}</strong></div><p v-if="selectedPayment.expires" class="border-t border-[#dce6f4] pt-3 text-xs leading-5 text-slate-500">Bayar sebelum <strong class="text-[#17345e]">{{ date(selectedPayment.expires, true) }}</strong></p></div>
                    <p v-if="copied" role="status" class="mt-3 text-xs text-[#3e7bef]">{{ copied }}</p>
                    <img v-if="selectedPayment.payment.instructions?.qr_url" :src="selectedPayment.payment.instructions.qr_url" alt="QR pembayaran pesanan" class="mx-auto mt-5 size-52 object-contain" />
                    <details open class="mt-6 border-y border-[#e2eaf5] py-4"><summary class="cursor-pointer text-sm font-bold">Instruksi Pembayaran</summary><ol class="mt-4 list-decimal space-y-3 pl-5 text-sm leading-6 text-slate-500"><li v-for="step in paymentGuide" :key="step">{{ step }}</li></ol></details>
                    <div class="mt-5"><h4 class="text-xs font-bold">Simpan bukti pembayaranmu</h4><p class="mt-2 text-xs leading-6 text-slate-500">Gunakan kode atau QR pesanan ini. Status diperbarui setelah pembayaran terverifikasi.</p></div>
                    <Link v-if="!codes(selectedPayment).length && !selectedPayment.payment.instructions?.qr_url" :href="route('checkout.payment', { type: selectedPayment.kind, id: selectedPayment.id })" class="mt-5 flex min-h-11 items-center justify-center rounded-lg border border-[#3e7bef] text-sm font-bold text-[#3e7bef]">Buka pembayaran pesanan</Link>
                </div>
                <footer class="shrink-0 border-t border-[#e2eaf5] p-4"><button type="button" class="min-h-11 w-full rounded-lg bg-[#3e7bef] text-sm font-bold text-white hover:bg-[#2866d4]" @click="paymentDialog.close()">Mengerti</button></footer>
            </div>
        </dialog>
    </section>
</template>
