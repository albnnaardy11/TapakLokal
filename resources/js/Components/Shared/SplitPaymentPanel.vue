<script setup>
import { computed, onMounted, onBeforeUnmount, ref, watch } from 'vue';
import axios from 'axios';
import { router } from '@inertiajs/vue3';
import { route } from 'ziggy-js';
const props = defineProps({ booking: Object, initialShares: Array });
const shares = ref(props.initialShares || []);
const currentPage = ref(1);
const visibleShares = computed(() => shares.value.slice(currentPage.value - 1, currentPage.value));
watch(() => props.initialShares, value => {
    shares.value = value || [];
    currentPage.value = Math.min(currentPage.value, Math.max(1, shares.value.length));
});
const busy = ref(false);
const message = ref('');
let polling;
let disposed = false;
const money = value => new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', maximumFractionDigits: 0 }).format(value);
const received = computed(() => shares.value.filter(s => s.status === 'paid').reduce((sum,s) => sum + s.amount, 0));
const fullyPaid = computed(() => props.booking.status === 'paid' || props.booking.payment?.status === 'paid' || (shares.value.length > 0 && shares.value.every(share => share.status === 'paid') && received.value === Number(props.booking.total)));
const labels = { pending: 'Belum dibayar', paid: 'Sudah dibayar', expired: 'Kedaluwarsa', cancelled: 'Dibatalkan', failed: 'Gagal', reconciliation_required: 'Perlu pemeriksaan / refund' };
const codeLabels = { va_number: 'Nomor virtual account', bill_key: 'Kode pembayaran', biller_code: 'Kode perusahaan', payment_code: 'Kode pembayaran' };
const codes = share => Object.entries(share.instructions || {}).filter(([key]) => key in codeLabels);
const copy = async value => { try { await navigator.clipboard.writeText(String(value)); message.value = 'Berhasil disalin ke clipboard.'; } catch { message.value = 'Gagal menyalin. Salin secara manual.'; } };
const generate = async () => {
    if (busy.value) return;
    busy.value = true;
    message.value = '';
    for (const share of shares.value) {
        if (disposed) break;
        if (share.instructions || share.status !== 'pending') continue;
        try {
            const { data } = await axios.post(route('split.charge', { booking: props.booking.id, share: share.id }), {}, { headers: { Accept: 'application/json' }, timeout: 15000 });
            shares.value = data.shares;
        } catch (error) {
            message.value = error.response?.data?.errors?.payment?.[0] || error.response?.data?.message || 'Sebagian instruksi belum tersedia. Tekan Siapkan Tagihan untuk mencoba kembali.';
            break;
        }
    }
    busy.value = false;
    if (!disposed && shares.value.length && shares.value.every(share => share.status === 'paid') && props.booking.status === 'awaiting_payment') router.reload({ only: ['order', 'splitShares'], preserveScroll: true });
};
const refresh = async () => {
    if (busy.value || document.visibilityState !== 'visible') return;
    busy.value = true;
    let changed = false;
    for (const share of shares.value) {
        if (disposed) break;
        if (!share.instructions || share.status !== 'pending') continue;
        try {
            const { data } = await axios.post(`${share.share_url}/status`, {}, { headers: { Accept: 'application/json' }, timeout: 8000 });
            changed ||= share.status !== data.share.status || props.booking.status !== data.bookingStatus;
            share.status = data.share.status;
        } catch { message.value = 'Status belum dapat diperiksa. Coba lagi sebentar.'; }
    }
    busy.value = false;
    if (changed && !disposed) router.reload({ only: ['order', 'splitShares'], preserveScroll: true });
};
const refreshOnReturn = () => {
    if (!disposed && document.visibilityState === 'visible') refresh();
};
onMounted(async () => {
    document.addEventListener('visibilitychange', refreshOnReturn);
    window.addEventListener('focus', refreshOnReturn);
    polling = setInterval(refresh, 30000);
    await generate();
    if (!disposed) await refresh();
});
onBeforeUnmount(() => {
    disposed = true;
    clearInterval(polling);
    document.removeEventListener('visibilitychange', refreshOnReturn);
    window.removeEventListener('focus', refreshOnReturn);
});
</script>
<template>
    <section class="space-y-5 rounded-2xl border border-slate-200 bg-white p-6 shadow-xs">
        <p v-if="fullyPaid" class="rounded-xl bg-emerald-50 p-4 font-bold text-emerald-700">Semua bagian sudah lunas. Booking berhasil dibayar.</p>
        <p v-else-if="['expired', 'cancelled'].includes(booking.status)" class="rounded-xl bg-rose-50 p-4 font-bold text-rose-700">Reservasi sudah ditutup. Jangan bayar tagihan yang tersisa. Hubungi bantuan untuk dana yang sudah masuk.</p>
        <h2 class="text-xl font-extrabold">Tagihan anggota · Split bill</h2>
        <p class="text-sm leading-6 text-slate-500">Semua tagihan menggunakan metode yang sama. Pemesan tetap mengelola booking dan tiket. Bagikan satu link untuk setiap anggota; pemesan juga dapat membayar bagian yang tersisa melalui link tersebut.</p>
        <div class="rounded-xl bg-blue-50 p-4"><p class="font-bold">{{ shares.filter(s => s.status === 'paid').length }} dari {{ shares.length }} bagian sudah dibayar</p><p class="mt-2 text-sm">Terkumpul {{ money(received) }} dari {{ money(booking.total) }}</p><p v-if="!fullyPaid" class="mt-2 text-xs text-rose-600">Batas bersama: {{ new Date(booking.expires_at).toLocaleString('id-ID', { timeZone: 'Asia/Jakarta' }) }} WIB</p></div>
        <p v-if="message" role="status" class="rounded-lg bg-slate-50 p-3 text-sm text-slate-600">{{ message }}</p>
        <div v-for="share in visibleShares" :key="share.id" class="rounded-xl border border-slate-200 p-4">
            <div class="flex flex-wrap justify-between gap-3"><div><h3 class="font-bold">{{ share.position }}. {{ share.label }}</h3><p class="mt-1 text-sm text-slate-500">{{ money(share.amount) }}</p></div><span class="text-xs font-bold" :class="share.status === 'paid' ? 'text-emerald-600' : 'text-rose-600'">{{ labels[share.status] || share.status }}</span></div>
            <template v-if="share.status === 'pending' && booking.status === 'awaiting_payment' && Date.parse(booking.expires_at) > Date.now()">
                <div v-if="share.instructions?.qr_url" class="mt-4 rounded-xl bg-slate-50 p-4 text-center">
                    <img :src="share.instructions.qr_url" :alt="`QR pembayaran ${share.label}, ${money(share.amount)}`" width="220" height="220" class="mx-auto size-55 max-w-full object-contain" />
                    <p class="mt-3 text-xs font-semibold">QR khusus bagian {{ share.position }} · {{ money(share.amount) }}</p>
                    <button type="button" class="mt-3 text-xs font-bold text-blue-600" @click="copy(share.instructions.qr_url)">Salin link QR</button>
                </div>
                <div v-for="[key, value] in codes(share)" :key="key" class="mt-3 rounded-lg bg-slate-50 p-3"><p class="text-xs text-slate-500">{{ codeLabels[key] }}</p><button type="button" class="mt-1 break-all text-left font-mono font-bold text-blue-600" :aria-label="`Salin ${codeLabels[key]} untuk ${share.label}`" @click="copy(value)">{{ value }}</button></div>
            </template>
            <div v-if="share.status === 'pending' && !fullyPaid" class="mt-4 flex flex-wrap gap-3"><a :href="share.share_url" target="_blank" rel="noopener" class="panel-secondary">Buka tagihan</a><button type="button" class="panel-primary" @click="copy(share.instructions?.qr_url || share.share_url)">{{ share.instructions?.qr_url ? 'Salin link gambar QR' : 'Salin link halaman tagihan' }}</button></div>
            <p v-if="!share.instructions && share.status === 'pending'" class="mt-3 text-xs text-slate-500">Instruksi belum siap. Siapkan tagihan sebelum membagikan link.</p>
        </div>
        <nav v-if="shares.length > 1" aria-label="Pilih tagihan anggota" class="flex flex-wrap items-center justify-between gap-3 border-t border-slate-100 pt-4">
            <p class="text-xs text-slate-500" aria-live="polite">Tagihan {{ currentPage }} dari {{ shares.length }} anggota</p>
            <div class="flex flex-wrap items-center gap-2">
                <button type="button" :disabled="currentPage === 1" class="min-h-10 rounded-lg border border-slate-200 px-3 text-xs font-semibold disabled:opacity-40" @click="currentPage--">Sebelumnya</button>
                <button v-for="(_, index) in shares" :key="index" type="button" :aria-label="`Tagihan anggota ${index + 1}`" :aria-current="currentPage === index + 1 ? 'page' : undefined" class="grid size-10 place-items-center rounded-lg border text-sm font-bold" :class="currentPage === index + 1 ? 'border-[#0175ea] bg-[#0175ea] text-white' : 'border-slate-200 bg-white text-[#17345e] hover:bg-blue-50'" @click="currentPage = index + 1">{{ index + 1 }}</button>
                <button type="button" :disabled="currentPage === shares.length" class="min-h-10 rounded-lg border border-slate-200 px-3 text-xs font-semibold disabled:opacity-40" @click="currentPage++">Berikutnya</button>
            </div>
        </nav>
        <div class="flex flex-wrap gap-3"><button v-if="!fullyPaid && booking.status === 'awaiting_payment'" type="button" :disabled="busy" class="panel-primary" @click="generate">{{ busy ? 'Memproses…' : 'Siapkan Tagihan' }}</button><button v-if="!fullyPaid" type="button" :disabled="busy" class="panel-secondary" @click="refresh">Cek semua pembayaran</button><a v-if="fullyPaid" :href="route('account.section', 'bookings')" class="panel-primary">Pesanan &amp; Tiket</a></div>
        <p v-if="!fullyPaid" class="text-xs leading-6 text-slate-500">Booking lunas setelah semua bagian dibayar. Jika reservasi berakhir sebelum lunas, dana yang masuk perlu ditindaklanjuti melalui proses refund oleh bantuan; dana tidak otomatis kembali.</p>
    </section>
</template>
