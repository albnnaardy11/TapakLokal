<script setup>
import { Head, Link, useForm, usePage } from '@inertiajs/vue3';
import { route } from 'ziggy-js';
import MainNavigation from '../Components/Shared/MainNavigation.vue';
import OrderConversationForm from '../Components/Shared/OrderConversationForm.vue';
const props = defineProps({ booking: Object, gatewayReady: Boolean });
const form = useForm({ reason: '' });
const action = useForm({});
const page = usePage();
const money = n => new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', maximumFractionDigits: 0 }).format(n);
</script>
<template>
    <Head :title="booking.reference" />
    <div class="min-h-screen overflow-x-hidden bg-[#f7f9fb] text-[#303e4c]">
        <MainNavigation />
        <main class="mx-auto max-w-4xl px-4 py-6 sm:px-6 sm:py-9">
            <Link :href="route('account')" class="inline-flex items-center text-xs font-bold text-[#3E7BEF] hover:underline">
                ← Kembali ke pemesanan
            </Link>
            
            <div class="mt-4">
                <h1 class="text-xl sm:text-2xl font-extrabold text-[#17345e] leading-tight">{{ booking.trip.title }}</h1>
                <p class="mt-1 text-xs text-slate-400 font-mono">{{ booking.reference }}</p>
            </div>

            <p v-if="page.props.flash?.success" class="mt-4 rounded-xl bg-emerald-50 p-3.5 text-xs font-semibold text-emerald-700">{{ page.props.flash.success }}</p>
            <p v-for="(error, key) in page.props.errors" :key="key" class="mt-3 text-xs font-semibold text-rose-600">{{ error }}</p>

            <div class="mt-6 grid items-start gap-5 md:grid-cols-[1fr_320px]">
                <section class="panel-surface p-5 sm:p-6">
                    <dl class="grid gap-4 sm:grid-cols-2">
                        <div v-for="(value, label) in { Status: booking.status, Peserta: booking.participants + ' orang', Berangkat: booking.trip.departure_date.slice(0,10), Selesai: booking.trip.end_date.slice(0,10), 'Titik kumpul': booking.trip.meeting_point, Vendor: booking.vendor.name, 'Kontak vendor': booking.vendor.phone, 'Nama pemesan': booking.contact_name }" :key="label" class="rounded-xl bg-slate-50/70 p-3 border border-slate-100">
                            <dt class="text-[10px] font-bold uppercase tracking-wider text-slate-400">{{ label }}</dt>
                            <dd class="mt-1 text-xs sm:text-sm font-bold text-slate-800 break-words">{{ value }}</dd>
                        </div>
                    </dl>
                    
                    <h2 class="mt-6 text-sm font-extrabold text-slate-800 border-t border-slate-100 pt-5">Itinerary & Fasilitas</h2>
                    <p class="mt-2.5 whitespace-pre-wrap text-xs leading-6 text-slate-600">{{ booking.trip.itinerary }}</p>
                </section>

                <aside class="panel-surface p-5 space-y-4">
                    <h2 class="text-sm font-extrabold text-slate-800 border-b border-slate-100 pb-3">Rincian Pembayaran</h2>
                    <dl class="space-y-2.5 text-xs">
                        <div class="flex justify-between text-slate-500"><dt>Subtotal</dt><dd class="font-semibold text-slate-700">{{ money(booking.subtotal) }}</dd></div>
                        <div class="flex justify-between text-slate-500"><dt>Diskon</dt><dd class="font-semibold text-emerald-600">-{{ money(booking.discount) }}</dd></div>
                        <div class="flex justify-between border-t border-slate-100 pt-3 text-sm font-extrabold text-slate-900"><dt>Total Pembayaran</dt><dd class="text-[#3E7BEF]">{{ money(booking.total) }}</dd></div>
                    </dl>
                    <div class="rounded-xl bg-blue-50/70 p-3 border border-blue-100 text-xs text-slate-700">
                        <span class="font-bold text-[#3E7BEF]">Status Pembayaran:</span> {{ booking.payment?.status || 'Belum dibayar' }}
                    </div>

                    <template v-if="booking.status === 'awaiting_payment'">
                        <p class="text-[11px] text-slate-400">Batas pembayaran: {{ new Date(booking.expires_at).toLocaleString('id-ID') }}</p>
                        <Link :href="route('checkout.payment', { type: 'trip', id: booking.id })" class="panel-primary w-full py-2.5 text-xs font-bold text-center">Lanjut Pembayaran</Link>
                        <p v-if="!gatewayReady" class="text-[11px] leading-5 text-amber-700">Pembayaran online belum aktif. Hubungi dukungan untuk informasi pesanan.</p>
                        <button class="panel-secondary w-full py-2.5 text-xs font-bold" :disabled="action.processing" @click="action.post(route('bookings.cancel', booking.id))">
                            Batalkan Pesanan
                        </button>
                    </template>
                </aside>
            </div>

            <form v-if="['paid', 'confirmed', 'ongoing', 'completed'].includes(booking.status) && !booking.refund" class="panel-surface mt-5 p-5 sm:p-6" @submit.prevent="form.post(route('bookings.refund', booking.id))">
                <h2 class="text-sm font-extrabold text-slate-800">Ajukan Pengembalian Dana (Refund)</h2>
                <textarea v-model="form.reason" required minlength="10" rows="3" class="panel-input mt-3 text-xs" placeholder="Jelaskan alasan pengajuan pengembalian dana"></textarea>
                <button :disabled="form.processing" class="panel-secondary mt-3 text-xs font-bold">Kirim Permintaan Refund</button>
            </form>
            <p v-if="booking.refund" class="mt-5 rounded-xl bg-blue-50 p-4 text-xs font-semibold text-blue-700">Status refund: {{ booking.refund.status }}</p>
            <OrderConversationForm :booking-id="booking.id" :reference="booking.reference" />
        </main>
    </div>
</template>
