<script setup>
import { computed, ref } from 'vue';
import { Link, usePage } from '@inertiajs/vue3';
import { route } from 'ziggy-js';
import Pagination from '../Admin/Pagination.vue';
const page = usePage();
const detailDialog = ref(null);
const selected = ref(null);
const showDetail = item => { selected.value = item; detailDialog.value.showModal(); }; 
const query = ref('');
const product = ref('all');
const status = ref('all');
const selectedDate = ref('');
const reset = () => { query.value = ''; product.value = 'all'; status.value = 'all'; selectedDate.value = ''; };
const money = value => new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', maximumFractionDigits: 0 }).format(value || 0);
const date = value => value ? new Intl.DateTimeFormat('id-ID', { day: 'numeric', month: 'short', year: 'numeric', timeZone: 'Asia/Jakarta' }).format(new Date(value)) : '—';
const dateKey = value => value ? new Intl.DateTimeFormat('en-CA', { year: 'numeric', month: '2-digit', day: '2-digit', timeZone: 'Asia/Jakarta' }).format(new Date(value)) : '';
const transactions = computed(() => (page.props.transactions?.data || []).map(({ kind, record }) => kind === 'trip'
    ? { key: 'trip-' + record.id, kind, id: record.booking_id, title: record.booking?.trip?.title || 'Pesanan perjalanan', image: record.booking?.trip?.image_url, booking: record.booking, vendor: record.booking?.vendor?.name, reference: record.booking?.reference || record.reference, created: record.created_at, expires: record.booking?.expires_at, payment: record }
    : { key: 'souvenir-' + record.id, kind, id: record.id, title: record.vendor?.name || 'Produk lokal', description: record.items?.map(item => item.name).join(', '), created: record.created_at, expires: record.expires_at, reference: record.reference, payment: record.payment || { status: record.status, amount: record.total } }
));
const state = item => item.payment.status === 'pending' && item.expires && Date.parse(item.expires) <= Date.now() ? 'expired' : item.payment.status;
const labels = { paid: 'Berhasil', pending: 'Menunggu pembayaran', expired: 'Kedaluwarsa', cancelled: 'Dibatalkan', failed: 'Tidak berhasil', refunded: 'Dikembalikan' };
const filtered = computed(() => transactions.value.filter(item => state(item) !== 'pending' && (product.value === 'all' || item.kind === product.value) && (!selectedDate.value || dateKey(item.created) === selectedDate.value) && (status.value === 'all' || (status.value === 'failed' ? ['failed', 'expired', 'cancelled'].includes(state(item)) : state(item) === status.value)) && [item.title, item.reference, item.description].join(' ').toLowerCase().includes(query.value.trim().toLowerCase())));
</script>
<template>
    <section class="overflow-hidden rounded-2xl border border-[#dce6f4] bg-white text-[#17345e]" aria-label="Riwayat transaksi TapakLokal">
        <div class="p-4 sm:p-5">
            <div class="grid gap-3 md:grid-cols-[1.3fr_1fr_1fr]">
                <label class="text-xs font-semibold text-slate-500">Cari transaksi<input v-model="query" type="search" placeholder="Kode pesanan, toko, atau produk" class="mt-2 min-h-11 w-full rounded-lg border border-[#dce6f4] px-3 text-xs font-normal outline-none focus:border-[#3e7bef]" /></label>
                <label class="text-xs font-semibold text-slate-500">Produk<select v-model="product" class="mt-2 min-h-11 w-full rounded-lg border border-[#dce6f4] bg-white px-3 text-xs font-normal outline-none focus:border-[#3e7bef]"><option value="all">Semua produk</option><option value="trip">Perjalanan &amp; trip</option><option value="souvenir">Oleh-oleh &amp; produk lokal</option></select></label>
                <label class="text-xs font-semibold text-slate-500">Tanggal transaksi<input v-model="selectedDate" type="date" class="mt-2 min-h-11 w-full rounded-lg border border-[#dce6f4] px-3 text-xs font-normal outline-none focus:border-[#3e7bef]" /></label>
            </div>
            <div class="mt-5 flex flex-wrap items-center gap-2"><span class="mr-1 text-xs font-bold">Status</span><button v-for="[key, label] in [['all', 'Semua'], ['paid', 'Berhasil'], ['failed', 'Tidak berhasil'], ['refunded', 'Dikembalikan']]" :key="key" type="button" :aria-pressed="status === key" class="min-h-10 rounded-lg border px-3 text-xs font-semibold" :class="status === key ? 'border-[#3e7bef] bg-[#edf4ff] text-[#3e7bef]' : 'border-slate-200 text-slate-500 hover:border-blue-200'" @click="status = key">{{ label }}</button><button type="button" class="min-h-10 px-3 text-xs font-bold text-[#3e7bef] hover:underline" @click="reset">Reset filter</button></div>
            <Link :href="route('account.section', 'payments')" class="mt-5 flex min-h-12 items-center justify-between rounded-lg border border-[#cbdcf8] bg-[#f5f8ff] px-4 text-xs font-semibold transition hover:bg-[#edf4ff]"><span>Menunggu Pembayaran</span><span class="text-[#3e7bef]">Lihat tagihan →</span></Link>
        </div>
        <div class="space-y-3 bg-[#f7f9fc] p-3 sm:p-4">
            <article v-for="item in filtered" :key="item.key" class="rounded-2xl border border-[#dce6f4] bg-white p-5 shadow-xs transition hover:border-[#c5d7f3] sm:p-6">
                <div class="flex flex-wrap items-center gap-x-3 gap-y-2 text-xs"><span class="font-bold">{{ item.kind === 'trip' ? 'Perjalanan' : 'Oleh-oleh' }}</span><span class="text-slate-500">{{ date(item.created) }}</span><span class="rounded px-2 py-1 font-semibold" :class="state(item) === 'paid' ? 'bg-[#edf4ff] text-[#3e7bef]' : 'bg-slate-100 text-slate-600'">{{ labels[state(item)] || state(item) }}</span><span class="break-all text-[11px] text-slate-400">{{ item.reference }}</span></div>
                <div class="mt-5 flex flex-col justify-between gap-4 sm:flex-row"><div class="flex min-w-0 items-start gap-4"><img v-if="item.image" :src="item.image" :alt="item.title" class="size-20 shrink-0 rounded-xl object-cover" loading="lazy" /><div><h3 class="text-sm font-extrabold">{{ item.title }}</h3><p class="mt-2 text-xs leading-6 text-slate-500">{{ item.description || item.vendor || 'Perjalanan bersama mitra TapakLokal' }}</p></div></div><div class="shrink-0 border-t border-slate-100 pt-3 sm:min-w-40 sm:border-t-0 sm:border-l sm:pt-0 sm:pl-5"><p class="text-xs text-slate-500">Total pembayaran</p><p class="mt-1 text-lg font-extrabold">{{ money(item.payment.amount) }}</p></div></div>
                <div class="mt-5 flex flex-wrap items-center justify-end gap-3"><button type="button" @click="showDetail(item)" class="inline-flex min-h-11 items-center px-3 text-xs font-bold text-[#3e7bef] hover:underline">Lihat Detail Transaksi</button><Link :href="route(item.kind === 'trip' ? 'catalog' : 'souvenirs.index')" class="inline-flex min-h-11 items-center justify-center rounded-lg bg-[#3e7bef] px-6 text-xs font-bold text-white transition hover:bg-[#2866d4]">{{ item.kind === 'trip' ? 'Jelajahi Trip' : 'Belanja Lagi' }}</Link></div>
            </article>
            <div v-if="!filtered.length" class="rounded-xl border border-[#e2eaf5] bg-white px-6 py-10 sm:py-12">
                <div class="mx-auto flex max-w-xl flex-col items-center gap-6 text-center sm:flex-row sm:gap-8 sm:text-left">
                    <svg viewBox="0 0 220 180" fill="none" class="h-40 w-48 shrink-0" role="img" aria-labelledby="travel-history-art"><title id="travel-history-art">Catatan perjalanan dengan peta pegunungan dan tiket</title>
                        <path d="M20 148H201" stroke="#dce6f4" stroke-width="2" stroke-linecap="round" />
                        <path d="M35 132V45L83 33L133 47L176 35V126L133 138L83 124L35 132Z" fill="#f3f7fd" stroke="#9cb9e3" stroke-width="2" stroke-linejoin="round" />
                        <path d="M83 34V124M133 47V138" stroke="#c4d5ed" stroke-width="2" />
                        <path d="M47 97L68 65L89 94L110 72L130 101" stroke="#17345e" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" />
                        <path d="M61 76L68 65L76 76" stroke="#3e7bef" stroke-width="2.5" stroke-linejoin="round" />
                        <path d="M54 114C85 102 94 128 118 116C133 108 139 83 157 74" stroke="#3e7bef" stroke-width="2" stroke-dasharray="4 6" stroke-linecap="round" />
                        <path d="M158 47C149 47 143 53 143 61C143 72 158 85 158 85C158 85 173 72 173 61C173 53 167 47 158 47Z" fill="#3e7bef" /><circle cx="158" cy="61" r="5" fill="white" />
                        <rect x="104" y="110" width="90" height="43" rx="7" fill="white" stroke="#17345e" stroke-width="2" />
                        <path d="M167 112V151" stroke="#9cb9e3" stroke-width="2" stroke-dasharray="3 4" /><path d="M117 124H150M117 134H139" stroke="#3e7bef" stroke-width="3" stroke-linecap="round" />
                        <path d="M180 121V141M175 124V139M185 124V139" stroke="#17345e" stroke-width="2" />
                        <circle cx="46" cy="24" r="5" stroke="#9cb9e3" stroke-width="2" /><path d="M191 71H201M196 66V76" stroke="#9cb9e3" stroke-width="2" stroke-linecap="round" />
                    </svg>
                    <div><p class="text-[10px] font-bold uppercase tracking-[0.14em] text-[#3e7bef]">Catatan perjalananmu</p><h3 class="mt-2 text-lg font-extrabold leading-7 text-[#17345e]">{{ transactions.length ? 'Belum ada hasil yang cocok' : 'Perjalanan selesai, kenangan tersimpan' }}</h3><p class="mt-3 text-xs leading-6 text-slate-500">{{ transactions.length ? 'Coba ubah kata pencarian, tanggal, atau produk untuk menemukan transaksi yang kamu cari.' : 'Riwayat transaksi akan muncul di sini setelah perjalananmu selesai. Pesanan yang masih menunggu pembayaran tersedia di menu tagihan.' }}</p><button v-if="transactions.length" type="button" class="mt-5 min-h-10 rounded-lg border border-[#3e7bef] px-4 text-xs font-bold text-[#3e7bef] hover:bg-[#edf4ff]" @click="reset">Reset filter</button><Link v-else :href="route('catalog')" class="mt-5 inline-flex min-h-10 items-center justify-center rounded-lg bg-[#3e7bef] px-4 text-xs font-bold text-white hover:bg-[#2866d4]">Temukan perjalanan berikutnya</Link></div>
                </div>
            </div>
        </div>
        <dialog ref="detailDialog" aria-labelledby="transaction-detail-title" class="fixed inset-0 m-auto max-h-[90dvh] w-[calc(100%-2rem)] max-w-xl overflow-auto rounded-2xl bg-white p-6 text-[#17345e] shadow-xl backdrop:bg-slate-900/50" @click.self="detailDialog.close()">
            <template v-if="selected"><header class="flex items-center justify-between gap-4"><h2 id="transaction-detail-title" class="text-xl font-extrabold">Detail Transaksi</h2><button type="button" aria-label="Tutup detail transaksi" class="min-h-10 px-3 text-xl" @click="detailDialog.close()">×</button></header><p class="mt-4 break-all text-xs text-slate-500">{{ selected.reference }}</p><img v-if="selected.image" :src="selected.image" :alt="selected.title" class="mt-5 h-40 w-full rounded-xl object-cover" /><h3 class="mt-5 font-bold">{{ selected.title }}</h3><p class="mt-2 text-sm text-slate-500">{{ selected.vendor }}</p><dl class="mt-6 space-y-4 border-t border-slate-100 pt-5 text-sm"><div class="flex justify-between gap-4"><dt>Status pesanan</dt><dd class="font-bold">Selesai</dd></div><div class="flex justify-between gap-4"><dt>Tanggal transaksi</dt><dd>{{ date(selected.created) }}</dd></div><template v-if="selected.booking"><div class="flex justify-between"><dt>Peserta</dt><dd>{{ selected.booking.participants }} orang</dd></div><div class="flex justify-between gap-4"><dt>Perjalanan</dt><dd>{{ date(selected.booking.trip?.departure_date) }} – {{ date(selected.booking.trip?.return_date) }}</dd></div></template><div class="flex justify-between"><dt>Metode pembayaran</dt><dd>{{ page.props.paymentMethods?.find(method => method.id === selected.payment.method)?.name || '—' }}</dd></div><div class="flex justify-between border-t border-slate-100 pt-4 font-extrabold"><dt>Total pembayaran</dt><dd>{{ money(selected.payment.amount) }}</dd></div></dl><button type="button" class="mt-6 min-h-11 w-full rounded-lg bg-[#3e7bef] text-sm font-bold text-white" @click="detailDialog.close()">Tutup</button></template>
        </dialog>
        <div v-if="page.props.transactions" class="border-t border-[#dce6f4] bg-white p-4"><Pagination :records="page.props.transactions" /></div>
    </section>
</template>
