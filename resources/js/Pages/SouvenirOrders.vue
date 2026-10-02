<script setup>
import { Head, Link, router, usePage, useForm } from '@inertiajs/vue3';
import { route } from 'ziggy-js';
import MainNavigation from '../Components/Shared/MainNavigation.vue';
import MainFooter from '../Components/Shared/MainFooter.vue';
import OrderConversationForm from '../Components/Shared/OrderConversationForm.vue';
import { formatSouvenirPrice as money } from '../Components/Catalog/souvenirCatalog';
defineProps({ orders: Object, order: Object, paymentEnabled: Boolean });
const page = usePage();
const review = useForm({ item_id: '', rating: 5, body: '' });
const labels = { awaiting_payment: 'Menunggu pembayaran', paid: 'Pembayaran diterima', processing: 'Disiapkan toko', ready_for_pickup: 'Siap diambil', shipped: 'Dikirim', completed: 'Selesai', cancelled: 'Dibatalkan', expired: 'Kedaluwarsa' };
const update = (order, status) => router.put(route('souvenirs.orders.update', order.id), { status });
</script>
<template>
    <Head title="Pesanan Oleh-oleh"><meta name="robots" head-key="robots" content="noindex,nofollow" /></Head>
    <MainNavigation :is-static="true" />
    <main class="mx-auto max-w-[1180px] px-4 py-10 text-[#172c50]">
        <Link :href="route('souvenirs.index')" class="text-sm font-semibold text-[#0175ea]">← Etalase lokal</Link>
        <h1 class="mt-5 text-2xl font-bold">{{ order ? order.reference : 'Pesanan oleh-oleh' }}</h1>
        <p v-for="(error,key) in page.props.errors" :key="key" role="alert" class="mt-3 text-sm text-red-700">{{ error }}</p>
        <template v-if="order">
            <div class="mt-6 grid gap-6 lg:grid-cols-[1fr_340px]">
                <section class="rounded-xl border border-slate-200 p-6">
                    <p class="font-semibold">{{ order.vendor.name }} · {{ labels[order.status] }}</p>
                    <p v-if="order.payment?.status === 'reconciliation_required'" role="status" class="mt-4 rounded-lg bg-amber-50 p-4 text-sm text-amber-900">Pembayaran memerlukan pemeriksaan tim keuangan. Hubungi bantuan dengan nomor pesanan ini sebelum mencoba pembayaran lagi.</p>
                    <article v-for="item in order.items" :key="item.id" class="mt-5 flex justify-between gap-4 border-b border-slate-100 pb-4"><div><h2 class="text-sm font-semibold">{{ item.name }}</h2><p class="mt-1 text-xs text-slate-500">{{ item.variant }} · {{ item.quantity }} unit</p><p v-if="item.note" class="mt-2 text-xs">Catatan: {{ item.note }}</p></div><p class="text-sm font-bold">{{ money(item.unit_price * item.quantity) }}</p></article>
                    <h2 class="mt-6 font-bold">{{ order.method === 'pickup' ? 'Pengambilan di tempat' : 'Kirim ke rumah' }}</h2>
                    <p class="mt-3 text-sm">{{ order.contact_name }} · {{ order.contact_phone }}</p><p class="mt-2 whitespace-pre-line text-sm">{{ order.address }}</p><p v-if="order.pickup_date" class="mt-2 text-sm">Tanggal: {{ order.pickup_date.slice(0,10) }}</p><p v-if="order.delivery_service" class="mt-2 text-sm">Layanan: {{ order.delivery_service }}</p><p v-if="order.tracking_number" class="mt-2 text-sm">Nomor resi: <strong>{{ order.tracking_number }}</strong></p>
<form v-if="order.status === 'completed'" class="mt-6 border-t border-slate-200 pt-5" @submit.prevent="review.post(route('souvenirs.review'), {onSuccess: () => review.reset()})"><h2 class="font-bold">Ulas pesananmu</h2><label class="mt-4 block text-xs font-semibold">Produk<select v-model="review.item_id" required class="panel-input mt-2"><option value="">Pilih produk</option><option v-for="item in order.items" :key="item.id" :value="item.id">{{ item.name }} · {{ item.variant }}</option></select></label><label class="mt-4 block text-xs font-semibold">Rating<select v-model="review.rating" class="panel-input mt-2"><option v-for="n in 5" :key="n" :value="n">{{ n }} dari 5</option></select></label><label class="mt-4 block text-xs font-semibold">Pengalamanmu<textarea v-model="review.body" required minlength="10" maxlength="2000" class="panel-input mt-2"></textarea></label><p v-for="error in review.errors" :key="error" role="alert" class="mt-3 text-xs text-red-700">{{ error }}</p><button :disabled="review.processing" class="panel-primary mt-4">Kirim ulasan untuk ditinjau</button></form>
</section>
                <aside class="h-fit rounded-xl border border-slate-200 p-6"><h2 class="font-bold">Ringkasan pembayaran</h2><p class="mt-5 flex justify-between text-sm"><span>Produk</span>{{ money(order.subtotal) }}</p><p class="mt-3 flex justify-between text-sm"><span>Ongkir</span>{{ money(order.shipping_fee) }}</p><p class="mt-5 flex justify-between border-t border-slate-200 pt-5 font-bold"><span>Total</span>{{ money(order.total) }}</p>
                    <template v-if="order.status === 'awaiting_payment'"><p class="mt-4 text-xs leading-6">Batas pembayaran: {{ new Date(order.expires_at).toLocaleString('id-ID') }}. Stok dilepas jika pesanan kedaluwarsa.</p><Link :href="route('checkout.payment', { type: 'souvenir', id: order.id })" class="panel-primary mt-4 w-full text-center">Lanjut pembayaran</Link><p v-if="!paymentEnabled" class="mt-2 text-xs">Pembayaran belum diaktifkan oleh pengelola.</p><button class="panel-secondary mt-3 w-full" @click="update(order, 'cancelled')">Batalkan pesanan</button></template>
                    <button v-if="['shipped','ready_for_pickup'].includes(order.status)" class="panel-primary mt-5 w-full" @click="update(order, 'completed')">Konfirmasi pesanan diterima</button>
                    <Link :href="route('help.index')" class="mt-5 block text-xs font-semibold text-[#0175ea]">Butuh bantuan pesanan?</Link>
                </aside>
            </div>
            <OrderConversationForm :souvenir-order-id="order.id" :reference="order.reference" />
        </template>
        <template v-else><Link v-for="item in orders?.data" :key="item.id" :href="route('souvenirs.orders.show',item.id)" class="mt-4 block rounded-xl border border-slate-200 p-5"><p class="font-bold">{{ item.reference }}</p><p class="mt-2 text-sm">{{ item.vendor.name }} · {{ labels[item.status] }}</p><p class="mt-3 font-semibold">{{ money(item.total) }}</p></Link><p v-if="!orders?.data.length" class="py-12 text-sm text-slate-500">Belum ada pesanan oleh-oleh.</p><nav class="mt-6 flex gap-4" aria-label="Halaman pesanan"><Link v-if="orders?.prev_page_url" :href="orders.prev_page_url">Sebelumnya</Link><Link v-if="orders?.next_page_url" :href="orders.next_page_url">Berikutnya</Link></nav></template>
    </main>
    <MainFooter />
</template>

