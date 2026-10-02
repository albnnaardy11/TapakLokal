<script setup>
import { Link, router, usePage } from '@inertiajs/vue3';
import { route } from 'ziggy-js';
import PanelLayout from '../../Components/Admin/PanelLayout.vue';
import { formatSouvenirPrice as money } from '../../Components/Catalog/souvenirCatalog';
defineProps({ orders: Object, ledger: Array, status: String });
const page = usePage();
const navigation = [{key:'souvenir-finance',label:'Keuangan oleh-oleh',group:'Keuangan',url:route('admin.souvenirs.finance')}];
const filter = event => router.get(route('admin.souvenirs.finance'), { status: event.target.value || undefined });
</script>
<template>
    <PanelLayout title="Keuangan oleh-oleh" subtitle="Status gateway dan pencatatan dana per pesanan." :navigation="navigation">
        <p class="mb-5 rounded-lg bg-amber-50 p-4 text-sm leading-6 text-amber-900">Hak vendor tercatat setelah pembeli menyelesaikan pesanan. Pencatatan vendor payable belum membuktikan transfer ke rekening vendor. Pembayaran terlambat dan refund memerlukan rekonsiliasi keuangan sebelum dana dicairkan.</p>
        <label class="mb-5 block text-xs font-semibold">Status pembayaran<select :value="status" class="panel-input mt-2 max-w-sm" @change="filter"><option value="">Semua status</option><option value="pending">Menunggu</option><option value="paid">Dibayar</option><option value="failed">Gagal</option><option value="reconciliation_required">Perlu rekonsiliasi</option></select></label>
        <section class="panel-surface p-6"><h2 class="font-bold">Pesanan</h2><article v-for="order in orders.data" :key="order.id" class="mt-5 border-t border-slate-100 pt-5"><p class="text-sm font-bold">{{ order.reference }} · {{ order.vendor.name }}</p><p class="mt-2 text-xs">Pesanan: {{ order.status }} · Gateway: {{ order.payment?.status }}</p><dl class="mt-3 grid grid-cols-2 gap-2 text-sm"><dt>Total pembeli</dt><dd>{{ money(order.total) }}</dd><dt>Hak vendor + ongkir</dt><dd>{{ money(order.vendor_amount) }}</dd><dt>Markup platform</dt><dd>{{ money(order.platform_fee) }}</dd><dt>Referensi gateway</dt><dd class="break-all">{{ order.payment?.provider_reference || 'Belum tersedia' }}</dd></dl><button v-if="order.payment && page.props.auth?.permissions?.includes('refund.approve')" class="panel-secondary mt-4" @click="router.post(route('admin.souvenirs.reconcile',order.payment.id))">Periksa ulang ke Midtrans</button></article><p v-if="!orders.data.length" class="mt-5 text-sm text-slate-500">Belum ada transaksi.</p><nav class="mt-5 flex gap-4" aria-label="Halaman transaksi"><Link v-if="orders.prev_page_url" :href="orders.prev_page_url">Sebelumnya</Link><Link v-if="orders.next_page_url" :href="orders.next_page_url">Berikutnya</Link></nav></section>
        <section class="panel-surface mt-6 overflow-x-auto p-6"><h2 class="font-bold">100 entri jurnal terbaru</h2><table class="mt-5 w-full text-left text-xs"><caption class="sr-only">Jurnal berpasangan untuk penerimaan dan penyelesaian pesanan</caption><thead><tr><th scope="col" class="p-2">Referensi</th><th scope="col" class="p-2">Akun</th><th scope="col" class="p-2">Jumlah bertanda</th></tr></thead><tbody><tr v-for="entry in ledger" :key="entry.id" class="border-t border-slate-100"><td class="break-all p-2">{{ entry.reference }}</td><td class="p-2">{{ entry.account }}</td><td class="whitespace-nowrap p-2">{{ money(entry.amount) }}</td></tr></tbody></table></section>
    </PanelLayout>
</template>
