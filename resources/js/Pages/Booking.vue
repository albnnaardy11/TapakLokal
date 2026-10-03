<script setup>
import BookingTicket from '../Components/Shared/BookingTicket.vue';
import { tripPackageFacilities, tripPackageExclusions } from '../Composables/tripPackageDetails';
import { Head, Link, useForm, usePage } from '@inertiajs/vue3';
import { route } from 'ziggy-js';
import { computed, onMounted, onBeforeUnmount, ref } from 'vue';
import {
    Bus,
    Check,
    CircleCheck,
    CircleX,
    CalendarDays,
    Camera,
    ChevronRight,
    ChevronDown,
    Compass,
    Headphones,
    ImageIcon,
    Info,
    MessageSquareMore,
    Phone,
    Sparkles,
    Ticket,
    TriangleAlert,
    User,
    Users,
    X,
} from 'lucide-vue-next';
import MainNavigation from '../Components/Shared/MainNavigation.vue';
const props = defineProps({ booking: Object, vendorLogo: String, ticketUrl: String, gatewayReady: Boolean });
const action = useForm({});
const page = usePage();
const photoDialog = ref(null);
const now = ref(Date.now());
let countdownInterval;
const secondsRemaining = computed(() => Math.max(0, Math.floor((Date.parse(props.booking.expires_at) - now.value) / 1000)));
const timer = computed(() => [
    { label: 'Jam', value: String(Math.floor(secondsRemaining.value / 3600)).padStart(2, '0') },
    { label: 'Menit', value: String(Math.floor(secondsRemaining.value / 60) % 60).padStart(2, '0') },
    { label: 'Detik', value: String(secondsRemaining.value % 60).padStart(2, '0') },
]);
onMounted(() => { countdownInterval = window.setInterval(() => { now.value = Date.now(); }, 1000); });
onBeforeUnmount(() => window.clearInterval(countdownInterval));
const itinerary = computed(() => (props.booking.trip.itinerary || '').split(/\n+/).map(line => line.trim()).filter(Boolean));
const itinerarySteps = computed(() => itinerary.value.map((line, index) => {
    const match = line.match(/^(Hari\s+[^:]+):\s*(.*)$/i);
    return { day: match?.[1] || `Hari ${index + 1}`, activity: match?.[2] || line };
}));
const currentStep = computed(() => ['paid', 'confirmed', 'ongoing', 'completed'].includes(props.booking.status) ? 3 : props.booking.status === 'awaiting_payment' ? 2 : 1);
const statusLabels = { awaiting_payment: 'Menunggu pembayaran', pending: 'Belum dibayar', paid: 'Pembayaran terverifikasi', confirmed: 'Trip dikonfirmasi', ongoing: 'Trip berlangsung', completed: 'Selesai', cancelled: 'Dibatalkan', expired: 'Batas pembayaran berakhir', failed: 'Pembayaran gagal', reconciliation_required: 'Pembayaran sedang diperiksa', refunded: 'Dana dikembalikan' };
const money = n => new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', maximumFractionDigits: 0 }).format(n);
const date = value => value ? new Intl.DateTimeFormat('id-ID', { day: 'numeric', month: 'long', year: 'numeric', timeZone: 'Asia/Jakarta' }).format(new Date(value)) : '—';
const shortDate = value => value ? new Intl.DateTimeFormat('id-ID', { day: 'numeric', month: 'short', year: 'numeric', timeZone: 'Asia/Jakarta' }).format(new Date(value)) : '—';
const vendorWhatsApp = computed(() => {
    const phone = (props.booking.vendor.phone || '').replace(/\D/g, '');
    return phone ? `https://wa.me/${phone.startsWith('0') ? '62' + phone.slice(1) : phone}` : null;
});
const deadline = value => value ? new Intl.DateTimeFormat('id-ID', { day: 'numeric', month: 'short', hour: '2-digit', minute: '2-digit', timeZone: 'Asia/Jakarta' }).format(new Date(value)) + ' WIB' : '—';

const packageIncludes = computed(() => tripPackageFacilities(props.booking.trip));
const packageExcludes = computed(() => tripPackageExclusions(props.booking.trip));
const facilityIcons = {
    transport: Bus,
    kendaraan: Bus,
    mobil: Bus,
    bus: Bus,
    kapal: Bus,
    guide: Compass,
    pemandu: Compass,
    tiket: Ticket,
    retribusi: Ticket,
    dokumen: Camera,
    foto: Camera,
    camera: Camera,
};

const getFacilityIcon = (facility) => {
    const key = (facility || '').toLowerCase().trim();
    for (const [name, icon] of Object.entries(facilityIcons)) {
        if (key.includes(name)) return icon;
    }
    return Sparkles;
};

const includedFacilities = computed(() => {
    const raw = props.booking.trip.experience?.included;
    if (Array.isArray(raw) && raw.length) {
        return raw;
    }
    return ['Transportasi', 'Guide Lokal', 'Tiket Masuk', 'Dokumentasi', 'Konsumsi', 'P3K'];
});
</script>
<template>
    <Head :title="booking.reference" />
    <div class="min-h-screen overflow-x-hidden bg-[#f7f9fb] text-[#303e4c]">
        <MainNavigation />
        <header class="relative isolate overflow-hidden bg-[#17345e] text-white">
            <img v-if="booking.trip.image_url" :src="booking.trip.image_url" alt="" class="absolute inset-0 -z-20 h-full w-full object-cover" /><div class="absolute inset-0 -z-10 bg-gradient-to-r from-[#102b4e]/95 via-[#102b4e]/70 to-[#102b4e]/30"></div>
            <div class="mx-auto max-w-6xl px-4 py-7 sm:px-6 sm:py-9"><Link :href="route('account.section', 'bookings')" class="text-[11px] text-white/70 hover:text-white">Pemesanan &amp; tiket / Detail pesanan</Link><div class="mt-3 flex flex-wrap items-end justify-between gap-5"><div><h1 class="text-2xl font-extrabold tracking-tight sm:text-3xl">{{ booking.trip.title }}</h1><p class="mt-3 text-xs text-white/85">{{ booking.trip.destination }}<span class="mx-3 text-white/40">|</span>Diselenggarakan oleh <strong>{{ booking.vendor.name }}</strong></p></div><div class="rounded-xl border border-white/20 bg-white/10 px-4 py-3 backdrop-blur-sm"><p class="text-[10px] text-white/70">Kode pesanan</p><p class="mt-1 break-all text-xs font-semibold">{{ booking.reference }}</p></div></div></div>
        </header>
        <main class="mx-auto max-w-6xl px-4 pb-10 sm:px-6">
            <nav aria-label="Tahapan pesanan" class="mx-auto flex max-w-3xl items-center gap-3 py-6 sm:gap-5"><template v-for="(label, index) in ['Detail pesanan', 'Pembayaran', 'Konfirmasi']" :key="label"><div v-if="index" class="h-px flex-1 bg-[#d5e1f2]"></div><div class="flex items-center gap-2 text-[11px] sm:text-xs" :aria-current="currentStep === index + 1 ? 'step' : undefined"><span class="grid size-7 shrink-0 place-items-center rounded-full border font-bold" :class="currentStep >= index + 1 ? 'border-[#3e7bef] bg-[#3e7bef] text-white' : 'border-[#bacce5] bg-white text-[#17345e]'">{{ index + 1 }}</span><span class="text-[#17345e]">{{ label }}</span></div></template></nav>
            <p v-if="page.props.flash?.success" role="status" class="mt-5 rounded-lg bg-[#edf4ff] p-4 text-xs text-[#285db3]">{{ page.props.flash.success }}</p><p v-for="(error, key) in page.props.errors" :key="key" role="alert" class="mt-3 text-xs text-rose-600">{{ error }}</p>
            <div class="grid items-start gap-5 lg:grid-cols-[minmax(0,1fr)_340px]">
                <div class="space-y-4">
                    <section class="overflow-hidden rounded-2xl border border-[#dce6f4] bg-white p-5 sm:p-6 shadow-xs">
                        <div class="flex flex-col gap-5 sm:flex-row sm:items-start">
                            <button v-if="booking.trip.image_url" type="button" class="group relative h-48 w-full shrink-0 overflow-hidden rounded-2xl sm:h-44 sm:w-56 text-left" aria-label="Lihat foto destinasi" @click="photoDialog.showModal()">
                                <img :src="booking.trip.image_url" :alt="booking.trip.title" class="size-full object-cover transition-transform duration-300 group-hover:scale-105" />
                                <span class="absolute bottom-2.5 right-2.5 inline-flex items-center gap-1.5 rounded-lg bg-[#17345e]/80 px-2.5 py-1.5 text-[11px] font-bold text-white shadow-xs backdrop-blur-xs transition group-hover:bg-[#17345e]">
                                    <ImageIcon class="size-3.5" />
                                    Lihat foto
                                </span>
                            </button>
                            <div class="flex min-w-0 flex-1 flex-col justify-between">
                                <div>
                                    <div class="flex flex-wrap items-center gap-2">
                                        <span class="text-xs font-extrabold text-[#0175ea]">
                                            {{ booking.trip.type === 'open-trip' ? 'Open Trip' : 'Private Trip' }}
                                        </span>
                                    </div>
                                    <h2 class="mt-2 text-xl font-extrabold tracking-tight text-[#17345e] sm:text-2xl">
                                        {{ booking.trip.title }}
                                    </h2>
                                    <div class="mt-2.5 flex flex-wrap items-center gap-2.5 text-xs font-medium text-slate-500">
                                        <div class="flex items-center gap-1.5">
                                            <CalendarDays class="size-4 text-slate-400" />
                                            <span>{{ shortDate(booking.trip.departure_date) }} – {{ shortDate(booking.trip.end_date) }}</span>
                                        </div>
                                        <span class="text-slate-300">|</span>
                                        <div class="flex items-center gap-1.5">
                                            <User class="size-4 text-slate-400" />
                                            <span>{{ booking.participants }} peserta</span>
                                        </div>
                                    </div>
                                    <div class="mt-3.5 flex flex-wrap items-center gap-2">
                                        <span v-for="facility in includedFacilities.slice(0, 4)" :key="facility" class="inline-flex items-center gap-1.5 rounded-xl bg-[#f0f6fc] px-3 py-1.5 text-xs font-semibold text-[#17345e]">
                                            <component :is="getFacilityIcon(facility)" class="size-3.5 text-[#3e7bef]" />
                                            <span>{{ facility }}</span>
                                        </span>
                                        <span v-if="includedFacilities.length > 4" class="inline-flex items-center rounded-xl bg-[#edf5fd] px-3 py-1.5 text-xs font-bold text-[#0175ea]">
                                            + {{ includedFacilities.length - 4 }} lainnya
                                        </span>
                                    </div>
                                </div>
                                <div class="mt-4 flex items-center gap-2.5 border-t border-slate-100 pt-3.5 sm:mt-5 sm:border-0 sm:pt-0">
                                    <img v-if="vendorLogo" :src="vendorLogo" :alt="booking.vendor.name" class="size-7 shrink-0 rounded-full border border-slate-100 bg-white object-contain" loading="lazy" />
                                    <span class="text-xs text-slate-500">
                                        Diselenggarakan oleh <strong class="font-bold text-[#17345e]">{{ booking.vendor.name }}</strong>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </section>
                    <section class="rounded-2xl border border-[#dce6f4] bg-white p-4 shadow-xs sm:p-5">
                        <h2 class="flex items-center gap-3 text-sm font-extrabold text-[#17345e]">
                            <span class="grid size-7 shrink-0 place-items-center rounded-lg bg-[#0175ea] text-white shadow-xs"><Bus class="size-4" :stroke-width="2.5" /></span>
                            Informasi keberangkatan
                        </h2>
                        <dl class="mt-5 grid gap-4 sm:ml-10 md:grid-cols-3 md:gap-0">
                            <div class="md:pr-5">
                                <dt class="text-[11px] text-[#7186a2]">Titik kumpul</dt>
                                <dd class="mt-1 text-xs font-bold leading-5 text-[#17345e]">{{ booking.trip.meeting_point }}</dd>
                            </div>
                            <div class="border-t border-[#e8eef7] pt-3 md:border-l md:border-t-0 md:px-5 md:pt-0">
                                <dt class="text-[11px] text-[#7186a2]">Waktu berkumpul</dt>
                                <dd class="mt-1 text-xs font-bold leading-5 text-[#17345e]">{{ shortDate(booking.trip.departure_date) }}</dd>
                                <p class="mt-0.5 text-[10px] leading-5 text-[#7186a2]">Konfirmasi jam berkumpul dengan mitra.</p>
                            </div>
                            <div class="border-t border-[#e8eef7] pt-3 md:border-l md:border-t-0 md:pl-5 md:pt-0">
                                <dt class="text-[11px] text-[#7186a2]">Kontak mitra perjalanan</dt>
                                <dd class="mt-1 flex items-center gap-2 text-xs font-bold leading-5 text-[#17345e]"><Phone class="size-3" />{{ booking.vendor.phone || 'Hubungi melalui pesan' }}</dd>
                                <a v-if="vendorWhatsApp" :href="vendorWhatsApp" target="_blank" rel="noopener noreferrer" class="mt-1.5 inline-flex min-h-7 items-center gap-1.5 rounded-md border border-emerald-300 bg-emerald-50/50 px-2.5 text-[10px] font-bold text-emerald-600 transition hover:bg-emerald-50"><svg viewBox="0 0 24 24" fill="currentColor" class="size-3.5" aria-hidden="true"><path d="M20.52 3.48A11.9 11.9 0 0 0 12.05 0C5.46 0 .1 5.36.1 11.95c0 2.1.55 4.15 1.6 5.96L0 24l6.24-1.64a11.9 11.9 0 0 0 5.8 1.48h.01c6.59 0 11.95-5.36 11.95-11.95 0-3.19-1.24-6.19-3.48-8.41ZM12.05 21.82a9.9 9.9 0 0 1-5.03-1.38l-.36-.21-3.7.97.99-3.61-.23-.37a9.86 9.86 0 0 1-1.51-5.27c0-5.47 4.45-9.92 9.93-9.92a9.85 9.85 0 0 1 7.02 2.91 9.85 9.85 0 0 1 2.9 7.02c0 5.47-4.45 9.92-9.92 9.92Zm5.44-7.43c-.3-.15-1.77-.87-2.04-.97-.28-.1-.48-.15-.68.15-.2.3-.77.97-.95 1.17-.17.2-.35.22-.65.07-.3-.15-1.26-.46-2.4-1.48-.89-.79-1.49-1.77-1.66-2.07-.18-.3-.02-.46.13-.61.14-.13.3-.35.45-.52.15-.18.2-.3.3-.5.1-.2.05-.37-.03-.52-.07-.15-.67-1.62-.92-2.22-.24-.58-.48-.5-.67-.51h-.57c-.2 0-.52.07-.8.37-.27.3-1.04 1.02-1.04 2.49 0 1.47 1.07 2.89 1.22 3.09.15.2 2.1 3.2 5.1 4.49.71.31 1.27.49 1.71.62.72.23 1.37.2 1.88.12.57-.09 1.77-.72 2.02-1.42.25-.7.25-1.3.17-1.42-.07-.12-.27-.2-.57-.35Z" /></svg>Chat via WhatsApp</a>
                            </div>
                        </dl>
                    </section>
                    <section class="rounded-2xl border border-[#dce6f4] bg-white p-4 shadow-xs sm:p-5">
                        <details open class="group">
                            <summary class="flex cursor-pointer list-none items-center gap-3 text-sm font-extrabold text-[#17345e] [&::-webkit-details-marker]:hidden">
                                <span class="grid size-7 shrink-0 place-items-center rounded-lg bg-[#eaf2ff] text-[#0175ea]">
                                    <svg viewBox="0 0 24 24" fill="none" class="size-4" aria-hidden="true">
                                        <path d="M6 5v14" stroke="currentColor" stroke-width="1.8" />
                                        <circle cx="6" cy="5" r="2.5" fill="currentColor" />
                                        <circle cx="6" cy="12" r="2.5" fill="currentColor" />
                                        <circle cx="6" cy="19" r="2.5" fill="currentColor" />
                                        <path d="M12 5h6M12 12h8M12 19h6" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" />
                                    </svg>
                                </span>
                                Rencana perjalanan
                                <ChevronDown class="ml-auto size-4 text-[#31577f] transition-transform group-open:rotate-180" />
                            </summary>
                            <ol class="ml-3.5 mt-4 sm:ml-10">
                                <li v-for="(step, index) in itinerarySteps" :key="index" class="relative grid grid-cols-[4.5rem_minmax(0,1fr)] gap-2 border-l border-[#d5e4fa] pb-2 pl-5 text-[11px] leading-5 text-[#45658b] last:border-transparent last:pb-0 sm:grid-cols-[5rem_minmax(0,1fr)]">
                                    <span class="absolute -left-[4.5px] top-1.5 size-2 rounded-full bg-[#0175ea]"></span>
                                    <span class="font-medium">{{ step.day }}</span>
                                    <span>{{ step.activity }}</span>
                                </li>
                            </ol>
                        </details>
                    </section>
                    <section class="rounded-2xl border border-[#dce6f4] bg-white p-4 shadow-xs sm:p-5">
                        <h2 class="flex items-center gap-3 text-sm font-extrabold text-[#17345e]">
                            <span class="grid size-7 shrink-0 place-items-center rounded-lg bg-orange-50 text-orange-500"><Info class="size-4" :stroke-width="2.5" /></span>
                            Informasi tambahan
                        </h2>
                        <div class="mt-3 grid items-start gap-3 sm:ml-10 sm:grid-cols-3">
                            <div class="rounded-lg bg-[#f4f8fc] p-3">
                                <h3 class="flex items-center gap-2 text-[11px] font-bold text-[#17345e]"><CircleCheck class="size-3.5 shrink-0 text-emerald-500" />Termasuk</h3>
                                <ul v-if="packageIncludes.length" tabindex="0" aria-label="Daftar fasilitas termasuk, gulir untuk melihat semua" class="mt-2 max-h-40 space-y-2 overflow-y-auto overscroll-contain pr-2 [scrollbar-width:thin]">
                                    <li v-for="(item, index) in packageIncludes" :key="index" class="flex items-start gap-2 text-[10px] leading-4 text-[#60789c]"><span class="shrink-0 font-bold text-emerald-600">{{ index + 1 }}.</span><span>{{ item.title }}<span v-if="item.note" class="mt-0.5 block text-[#7186a2]">{{ item.note }}</span></span></li>
                                </ul>
                                <p v-else class="mt-2 text-[10px] leading-4 text-[#60789c]">Konfirmasi fasilitas paket dengan mitra perjalanan.</p>
                            </div>
                            <div class="rounded-lg bg-[#f4f8fc] p-3">
                                <h3 class="flex items-center gap-2 text-[11px] font-bold text-[#17345e]"><CircleX class="size-3.5 shrink-0 text-rose-500" />Tidak termasuk</h3>
                                <ul v-if="packageExcludes.length" tabindex="0" aria-label="Daftar fasilitas tidak termasuk, gulir untuk melihat semua" class="mt-2 max-h-40 space-y-2 overflow-y-auto overscroll-contain pr-2 [scrollbar-width:thin]">
                                    <li v-for="(item, index) in packageExcludes" :key="index" class="flex items-start gap-2 text-[10px] leading-4 text-[#60789c]"><span class="shrink-0 font-bold text-rose-500">{{ index + 1 }}.</span><span>{{ item }}</span></li>
                                </ul>
                                <p v-else class="mt-2 text-[10px] leading-4 text-[#60789c]">Tanyakan rincian biaya di luar paket kepada mitra.</p>
                            </div>
                            <div class="rounded-lg bg-amber-50 p-3">
                                <h3 class="flex items-center gap-2 text-[11px] font-bold text-[#17345e]"><TriangleAlert class="size-3.5 shrink-0 text-amber-500" />Catatan penting</h3>
                                <p class="mt-2 text-[10px] leading-4 text-[#60789c]">Periksa titik kumpul dan jadwal sebelum berangkat. Konfirmasikan kebutuhan khusus kepada mitra.</p>
                                <p v-if="booking.special_request" tabindex="0" aria-label="Catatan pesanan" class="mt-2 max-h-24 overflow-y-auto overscroll-contain text-[10px] leading-4 text-[#60789c] [scrollbar-width:thin]">{{ booking.special_request }}</p>
                            </div>
                        </div>
                    </section>
                </div>
                <div class="space-y-4 lg:sticky lg:top-6"><BookingTicket :url="ticketUrl" :booking="booking" /><aside class="overflow-hidden rounded-2xl border border-[#dce6f4] bg-white"><div class="border-b border-[#e8eef7] px-6 py-5"><h2 class="text-base font-bold text-[#17345e]">Ringkasan pembayaran</h2></div><div class="p-6"><div class="mb-5 flex items-center gap-3 border-b border-[#e8eef7] pb-4"><img v-if="booking.trip.image_url" :src="booking.trip.image_url" alt="" class="h-14 w-18 rounded-lg object-cover" /><div><p class="text-xs font-bold text-[#17345e]">{{ booking.trip.title }}</p><p class="mt-1 text-[10px] leading-5 text-slate-500">{{ date(booking.trip.departure_date) }} · {{ booking.participants }} peserta</p></div></div><dl class="space-y-4 text-xs"><div v-if="booking.discount" class="flex justify-between"><dt class="text-slate-500">Potongan promo</dt><dd class="font-semibold text-[#3e7bef]">−{{ money(booking.discount) }}</dd></div><div class="flex items-center justify-between" :class="booking.discount ? 'border-t border-[#e8eef7] pt-4' : ''"><dt class="font-bold text-[#17345e]">Total pembayaran</dt><dd class="text-xl font-extrabold text-[#17345e]">{{ money(booking.total) }}</dd></div></dl><template v-if="booking.status === 'awaiting_payment'"><p class="mt-5 text-xs leading-6 text-slate-500">Selesaikan pembayaran sebelum<br /><strong class="text-[#17345e]">{{ deadline(booking.expires_at) }}</strong></p><div class="mt-3 flex items-center gap-2" role="timer" aria-label="Sisa waktu pembayaran"><div v-for="part in timer" :key="part.label" class="flex-1 text-center"><span class="block rounded-lg border border-rose-200 bg-rose-50 py-3 font-mono text-xl font-extrabold tabular-nums text-rose-600 shadow-2xs">{{ part.value }}</span><span class="mt-1.5 block text-[10px] font-medium text-slate-500">{{ part.label }}</span></div></div><p v-if="!secondsRemaining" role="status" class="mt-3 text-xs text-rose-600">Batas pembayaran sudah berakhir.</p><Link :href="route('checkout.payment', { type: 'trip', id: booking.id })" class="mt-4 flex min-h-12 items-center justify-center rounded-lg bg-[#3e7bef] text-sm font-bold text-white transition hover:bg-[#2866d4]">Lanjut Pembayaran</Link><button type="button" class="mt-3 min-h-11 w-full rounded-lg border border-[#dce6f4] text-xs font-semibold text-slate-500 hover:bg-slate-50 disabled:opacity-50" :disabled="action.processing" @click="action.post(route('bookings.cancel', booking.id))">{{ action.processing ? 'Membatalkan…' : 'Batalkan Pesanan' }}</button><p v-if="!gatewayReady" class="mt-4 text-xs leading-5 text-amber-700">Pembayaran online belum aktif. Hubungi dukungan untuk informasi pesanan.</p></template><template v-else-if="['cancelled', 'expired'].includes(booking.status)"><p class="mt-4 text-xs leading-6 text-slate-500">Reservasi sudah dilepas. Jangan bayar kode pembayaran pesanan ini.</p><Link :href="route('checkout.review.trip', booking.trip_id)" class="mt-4 flex min-h-11 items-center justify-center rounded-lg bg-[#3e7bef] text-xs font-bold text-white">Buat pesanan baru</Link></template></div></aside><section class="overflow-hidden rounded-2xl border border-[#dce6f4] bg-white p-4 sm:p-5 shadow-xs"><div class="flex items-center gap-3.5"><span class="grid size-11 shrink-0 place-items-center rounded-xl bg-[#edf5fd] text-[#17345e]"><Headphones class="size-5 text-[#17345e]" :stroke-width="2.2" /></span><div><h2 class="text-sm font-bold text-[#17345e]">Butuh bantuan?</h2><p class="mt-0.5 text-xs text-slate-500">Tim kami siap membantu kamu 24/7.</p></div></div><div class="mt-3.5 border-t border-slate-100 pt-3"><Link :href="route('account.section', 'chat')" class="group flex items-center justify-between transition hover:opacity-90"><div class="flex items-center gap-3"><span class="grid size-9 shrink-0 place-items-center rounded-xl bg-[#edf5fd] text-[#0175ea]"><MessageSquareMore class="size-4.5 text-[#0175ea]" :stroke-width="2" /></span><span class="text-xs font-bold text-[#17345e]">Hubungi Bantuan</span></div><ChevronRight class="size-4 text-slate-400 transition group-hover:translate-x-0.5 group-hover:text-[#0175ea]" /></Link></div></section></div>
            </div>
            <p v-if="booking.refund" class="mt-5 rounded-xl bg-blue-50 p-4 text-xs font-semibold text-blue-700">Status refund: {{ booking.refund.status }}</p>
            <dialog ref="photoDialog" aria-label="Foto destinasi" class="fixed inset-0 m-auto w-[calc(100%-2rem)] max-w-3xl rounded-xl bg-white p-3 backdrop:bg-slate-900/70" @click.self="photoDialog.close()"><button type="button" class="mb-3 min-h-10 px-3 text-xs font-bold text-[#17345e]" @click="photoDialog.close()">Tutup foto ×</button><img :src="booking.trip.image_url" :alt="booking.trip.title" class="max-h-[75dvh] w-full rounded-lg object-contain" /></dialog>
        </main>
    </div>
</template>
