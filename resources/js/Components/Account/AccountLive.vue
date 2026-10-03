<script setup>
import { computed, ref, watch } from 'vue';
import { Link, router, useForm, usePage, usePoll } from '@inertiajs/vue3';
import { route } from 'ziggy-js';
import Pagination from '../Admin/Pagination.vue';
import AccountChat from './AccountChat.vue';
import AccountPoints from './AccountPoints.vue';
import AccountExploreBanner from './AccountExploreBanner.vue';
import AccountWallet from './AccountWallet.vue';
import AccountTransactions from './AccountTransactions.vue';
import AccountTransactionHistory from './AccountTransactionHistory.vue';
const page = usePage();
const section = computed(() => page.props.sectionKey);
const chatPolling = usePoll(5000, { only: ['records'] }, { autoStart: false });
watch(section, value => {
    if (['chat', 'support'].includes(value)) chatPolling.start();
    else chatPolling.stop();
}, { immediate: true });
const records = computed(() => page.props.records);
const bookingKind = computed(() => page.props.bookingFilters?.kind || 'all');
const bookingState = computed(() => page.props.bookingFilters?.state || 'all');
const filterBookings = (kind, state) => router.get(route('account.section', 'bookings'), { kind, state }, { preserveScroll: true, preserveState: true });
const form = useForm({ name: '', birth_date: '', phone: '', emergency_contact: '' });
const travelerId = ref(null);
const showTraveler = ref(false);
const profile = useForm({ name: page.props.auth.user.name, phone: page.props.auth.user.phone || '', city: page.props.auth.user.city || '' });
const password = useForm({ current_password: '', password: '', password_confirmation: '' });
const ticket = useForm({ subject: '', category: 'booking', body: '', booking_id: '' });
const review = useForm({ booking_id: '', rating: 5, body: '' });
const money = n => new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', maximumFractionDigits: 0 }).format(n);
const editTraveler = item => { travelerId.value = item?.id || null; form.name = item?.name || ''; form.birth_date = item?.birth_date?.slice(0,10) || ''; form.phone = item?.phone || ''; form.emergency_contact = item?.emergency_contact || ''; showTraveler.value = true; };
const saveTraveler = () => travelerId.value ? form.patch(route('travelers.update', travelerId.value), { onSuccess: () => showTraveler.value = false }) : form.post(route('travelers.store'), { onSuccess: () => showTraveler.value = false });
</script>
<template>
    <div>
        <AccountExploreBanner v-if="section === 'bookings'" compact />
        <section v-if="section === 'bookings'" class="mb-5">
            <div class="flex items-start justify-between gap-3"><div><h2 class="text-lg font-extrabold text-[#17345e]">Pemesanan Saya</h2><p class="mt-1 text-[11px] text-slate-500">Kelola seluruh perjalanan dan tiket yang sudah kamu pesan.</p></div></div>
            <div class="mt-4 flex flex-wrap items-center justify-between gap-3">
                <nav aria-label="Jenis pesanan" class="flex gap-2"><button v-for="(label, key) in { all: 'Semua', trip: 'Open Trip', po: 'Open PO' }" :key="key" type="button" :aria-pressed="bookingKind === key" class="rounded-full px-4 py-2 text-[11px] font-semibold" :class="bookingKind === key ? 'bg-[#0099ef] text-white' : 'bg-white text-slate-500 hover:bg-blue-50'" @click="filterBookings(key, bookingState)">{{ label }}</button></nav>
                <nav aria-label="Status pesanan" class="flex rounded-full bg-[#edf5fc] p-1"><button v-for="(label, key) in { all: 'Semua', active: 'Berlangsung', completed: 'Selesai', cancelled: 'Batal' }" :key="key" type="button" :aria-pressed="bookingState === key" class="rounded-full px-3 py-1.5 text-[10px] font-semibold" :class="bookingState === key ? 'bg-white text-[#0175ea] shadow-xs' : 'text-slate-500'" @click="filterBookings(bookingKind, key)">{{ label }}</button></nav>
            </div>
        </section>
        <p v-if="page.props.flash?.success" role="status" class="mb-4 rounded-lg bg-emerald-50 p-3 text-xs text-emerald-700">{{ page.props.flash.success }}</p><p v-for="(error, key) in page.props.errors" :key="key" role="alert" class="mb-3 text-xs text-rose-600">{{ error }}</p>
        <AccountPoints v-if="section === 'points'" />
        <AccountChat v-if="section === 'chat'" />
        <AccountWallet v-if="section === 'wallet'" />
        <AccountTransactions v-if="section === 'payments'" pending-only />
        <AccountTransactionHistory v-if="section === 'transactions'" />
        <div v-if="section === 'settings'" class="space-y-5">
            <form class="panel-surface p-5" @submit.prevent="profile.patch(route('account.profile'))"><h3 class="mb-5 text-sm font-bold">Data pribadi</h3><div class="grid gap-4 sm:grid-cols-2"><label v-for="(label, key) in { name: 'Nama lengkap', phone: 'Nomor ponsel', city: 'Kota' }" :key="key" class="text-xs font-semibold">{{ label }}<input v-model="profile[key]" :required="key === 'name'" class="panel-input mt-2" /></label></div><button :disabled="profile.processing" class="panel-primary mt-5">Simpan profil</button></form>
            <form class="panel-surface p-5" @submit.prevent="password.put(route('account.password'), { onSuccess: () => password.reset() })"><h3 class="mb-5 text-sm font-bold">Keamanan akun</h3><div class="grid gap-4"><label v-for="(label, key) in { current_password: 'Kata sandi saat ini', password: 'Kata sandi baru', password_confirmation: 'Konfirmasi kata sandi baru' }" :key="key" class="text-xs font-semibold">{{ label }}<input v-model="password[key]" type="password" :autocomplete="key === 'current_password' ? 'current-password' : 'new-password'" required class="panel-input mt-2" /></label></div><p class="mt-3 text-xs text-slate-400">Minimal 10 karakter, berisi huruf dan angka.</p><button :disabled="password.processing" class="panel-primary mt-5">Ubah kata sandi</button></form>
        </div>
        <template v-if="section === 'travelers'"><button class="panel-primary mb-4" @click="editTraveler(null)">Tambah wisatawan</button><form v-if="showTraveler" class="panel-surface mb-5 p-5" @submit.prevent="saveTraveler"><div class="grid gap-4 sm:grid-cols-2"><label v-for="(label, key) in { name: 'Nama lengkap', birth_date: 'Tanggal lahir (opsional)', phone: 'Nomor kontak', emergency_contact: 'Kontak darurat' }" :key="key" class="text-xs font-semibold">{{ label }}<input v-model="form[key]" :type="key === 'birth_date' ? 'date' : 'text'" :required="key !== 'birth_date'" class="panel-input mt-2" /></label></div><button :disabled="form.processing" class="panel-primary mt-4">Simpan wisatawan</button><button type="button" class="panel-secondary ml-2" @click="showTraveler = false">Batal</button></form></template>
        <form v-if="section === 'support'" class="panel-surface mb-5 p-5" @submit.prevent="ticket.post(route('support.store'))"><h3 class="mb-4 text-sm font-bold">Mulai percakapan</h3><div class="grid gap-4 sm:grid-cols-2"><input v-model="ticket.subject" required placeholder="Subjek" aria-label="Subjek" class="panel-input" /><select v-model="ticket.category" aria-label="Kategori" class="panel-input"><option value="booking">Pemesanan</option><option value="payment">Pembayaran</option><option value="account">Akun</option><option value="vendor">Hubungi vendor</option><option value="other">Lainnya</option></select><input v-model="ticket.booking_id" type="number" placeholder="ID pesanan terkait (opsional)" aria-label="ID pesanan" class="panel-input" /><textarea v-model="ticket.body" required minlength="10" placeholder="Ceritakan kebutuhanmu…" aria-label="Pesan" rows="3" class="panel-input sm:col-span-2"></textarea></div><button :disabled="ticket.processing" class="panel-primary mt-4">Kirim pesan</button></form>
        <form v-if="section === 'reviews' && page.props.reviewable.length" class="panel-surface mb-5 p-5" @submit.prevent="review.post(route('reviews.store'), { onSuccess: () => review.reset() })"><h3 class="mb-4 text-sm font-bold">Bagikan pengalaman perjalanan</h3><select v-model="review.booking_id" required class="panel-input" aria-label="Pilih perjalanan"><option disabled value="">Pilih perjalanan selesai</option><option v-for="booking in page.props.reviewable" :key="booking.id" :value="booking.id">{{ booking.trip.title }} · {{ booking.reference }}</option></select><label class="mt-4 block text-xs">Rating<select v-model="review.rating" class="panel-input mt-2"><option v-for="n in 5" :key="n" :value="n">{{ n }} bintang</option></select></label><textarea v-model="review.body" required minlength="10" rows="3" class="panel-input mt-4" placeholder="Bagaimana perjalananmu?" aria-label="Ulasan"></textarea><button :disabled="review.processing" class="panel-primary mt-4">Kirim ulasan</button></form>
        <h3 v-if="section === 'bookings' && bookingKind !== 'po' && records?.data?.length" class="mb-3 text-sm font-bold">Perjalanan</h3>
        <section v-if="records && !['transactions', 'payments', 'chat', 'points'].includes(section) && !(section === 'bookings' && bookingKind === 'po')">
            <div v-if="records.data.length" class="panel-surface overflow-hidden">
                <article v-for="item in records.data" :key="item.id" class="flex flex-col sm:flex-row items-start sm:items-center gap-3.5 sm:gap-4 border-b border-slate-100 p-4 sm:p-5 last:border-b-0">
                    <img v-if="item.trip?.image_url" :src="item.trip.image_url" :alt="item.trip.title" loading="lazy" class="h-28 sm:h-20 w-full sm:w-28 rounded-xl object-cover shrink-0" />
                    <div class="min-w-0 flex-1 w-full"><p class="text-sm font-bold text-[#183660]">{{ item.trip?.title || item.name || item.subject || item.reference || item.description }}</p><p v-if="item.reference" class="mt-1 text-[10px] text-slate-400 font-mono">{{ item.reference }}</p><p class="mt-1.5 whitespace-pre-wrap text-xs leading-5 text-slate-500">{{ item.body || item.phone || item.status || item.code || item.description }}</p><p v-if="item.total || item.amount" class="mt-2 text-sm font-semibold text-blue-600">{{ money(item.total || item.amount) }}</p><p v-if="item.points !== undefined" class="mt-2 text-sm font-bold" :class="item.points < 0 ? 'text-rose-500' : 'text-emerald-600'">{{ item.points > 0 ? '+' : '' }}{{ item.points }} points</p><p v-if="section === 'vouchers'" class="mt-2 text-xs text-blue-600 font-medium">Kode {{ item.code }} · {{ item.type === 'percent' ? item.value + '%' : money(item.value) }} · minimum {{ money(item.minimum_amount) }}</p><p v-if="item.rating" class="mt-2 text-xs text-amber-600 font-bold">{{ item.rating }} / 5 bintang</p></div>
                    <div class="flex flex-wrap items-center gap-2 w-full sm:w-auto mt-2 sm:mt-0 justify-end"><Link v-if="['bookings', 'transactions', 'wallet'].includes(section) && item.status === 'awaiting_payment' || ['transactions', 'wallet'].includes(section) && item.status === 'pending'" :href="route('checkout.payment', { type: 'trip', id: item.booking_id || item.id })" class="panel-primary">Lanjut bayar</Link><Link v-if="section === 'bookings'" :href="route('bookings.show', item.id)" class="panel-secondary text-center flex-1 sm:flex-initial">Detail & tiket</Link><Link v-if="['transactions', 'wallet'].includes(section)" :href="route('bookings.show', item.booking_id)" class="panel-secondary text-center flex-1 sm:flex-initial">Detail</Link><Link v-if="['chat', 'support'].includes(section)" :href="route('support.show', item.id)" class="panel-secondary text-center flex-1 sm:flex-initial">Buka</Link><button v-if="section === 'travelers'" class="panel-secondary" @click="editTraveler(item)">Edit</button><Link v-if="section === 'travelers'" :href="route('travelers.destroy', item.id)" method="delete" as="button" class="panel-secondary">Hapus</Link><Link v-if="section === 'favorites'" :href="route('favorites.destroy', item.id)" method="delete" as="button" class="panel-secondary">Hapus favorit</Link></div>
                </article>
                <Pagination :records="records" :hide-counter="section === 'bookings'" />
            </div>
            <!-- Empty state for Open Trip bookings -->
            <div v-else-if="section === 'bookings' && bookingKind === 'trip'" class="overflow-hidden rounded-2xl border border-[#dce6f4] bg-white p-8 sm:p-12 shadow-[0_4px_24px_rgba(23,75,120,0.04)]">
                <div class="mx-auto flex max-w-lg flex-col items-center justify-center text-center">
                    <img
                        src="/Assets/Images/account/notfound.svg"
                        alt="Belum ada pesanan perjalanan"
                        class="mx-auto h-48 w-auto max-w-full object-contain sm:h-56"
                        loading="lazy"
                    />
                    <div class="mt-6 max-w-md">
                        <p class="text-[11px] font-bold uppercase tracking-[0.14em] text-[#3e7bef]">
                            Pesanan Perjalanan
                        </p>
                        <h3 class="mt-2 text-lg font-extrabold leading-snug text-[#17345e] sm:text-xl">
                            {{ bookingState === 'all' ? 'Belum ada pesanan perjalanan' : 'Belum ada perjalanan untuk status ini' }}
                        </h3>
                        <p class="mt-3 text-xs leading-6 text-slate-500">
                            {{ bookingState === 'all'
                                ? 'Tiket open trip dan private tour yang kamu pesan akan muncul di sini. Temukan destinasi impianmu sekarang.'
                                : 'Coba pilih status pesanan lainnya untuk melihat tiket perjalananmu.' }}
                        </p>
                    </div>
                    <div class="mt-6 flex flex-wrap items-center justify-center gap-3">
                        <Link
                            :href="route('catalog')"
                            class="inline-flex min-h-11 items-center justify-center rounded-xl bg-[#3e7bef] px-6 text-xs font-bold text-white shadow-xs transition hover:bg-[#2866d4]"
                        >
                            Jelajahi Paket Trip
                        </Link>
                    </div>
                </div>
            </div>
            <!-- Empty state for All bookings (when both records and souvenirOrders are empty) -->
            <div v-else-if="section === 'bookings' && bookingKind === 'all' && !page.props.souvenirOrders?.data?.length" class="overflow-hidden rounded-2xl border border-[#dce6f4] bg-white p-8 sm:p-12 shadow-[0_4px_24px_rgba(23,75,120,0.04)]">
                <div class="mx-auto flex max-w-lg flex-col items-center justify-center text-center">
                    <img
                        src="/Assets/Images/account/notfound.svg"
                        alt="Belum ada pesanan"
                        class="mx-auto h-48 w-auto max-w-full object-contain sm:h-56"
                        loading="lazy"
                    />
                    <div class="mt-6 max-w-md">
                        <p class="text-[11px] font-bold uppercase tracking-[0.14em] text-[#3e7bef]">
                            Pemesanan Saya
                        </p>
                        <h3 class="mt-2 text-lg font-extrabold leading-snug text-[#17345e] sm:text-xl">
                            {{ bookingState === 'all' ? 'Belum ada riwayat pemesanan' : 'Belum ada pesanan untuk status ini' }}
                        </h3>
                        <p class="mt-3 text-xs leading-6 text-slate-500">
                            {{ bookingState === 'all'
                                ? 'Semua tiket open trip dan pesanan oleh-oleh lokal yang kamu pesan akan tersimpan di sini.'
                                : 'Coba pilih status pesanan lainnya untuk melihat tiket dan pesananmu.' }}
                        </p>
                    </div>
                    <div class="mt-6 flex flex-wrap items-center justify-center gap-3">
                        <Link
                            :href="route('catalog')"
                            class="inline-flex min-h-11 items-center justify-center rounded-xl bg-[#3e7bef] px-6 text-xs font-bold text-white shadow-xs transition hover:bg-[#2866d4]"
                        >
                            Jelajahi Trip
                        </Link>
                        <Link
                            :href="route('souvenirs.index')"
                            class="inline-flex min-h-11 items-center justify-center rounded-xl border border-[#3e7bef] bg-white px-6 text-xs font-bold text-[#3e7bef] transition hover:bg-[#edf4ff]"
                        >
                            Jelajahi Oleh-oleh
                        </Link>
                    </div>
                </div>
            </div>
            <!-- Standard fallback for other sections -->
            <div v-else-if="!records.data.length && section !== 'bookings'" class="panel-surface px-5 py-12 text-center">
                <p class="text-sm font-semibold text-slate-500">Belum ada data</p>
                <p class="mt-2 text-xs text-slate-400">Aktivitasmu akan muncul di sini setelah tersimpan.</p>
            </div>
        </section>

        <!-- Section: Souvenir Orders / Open PO -->
        <section v-if="page.props.souvenirOrders && !['transactions', 'payments', 'chat', 'points'].includes(section) && bookingKind !== 'trip'" :class="section === 'bookings' && bookingKind === 'po' ? 'mt-4' : 'mt-7'">
            <!-- When there are orders -->
            <template v-if="page.props.souvenirOrders.data.length">
                <div class="mb-3 flex items-center justify-between">
                    <h3 class="text-sm font-bold">Oleh-oleh & produk lokal</h3>
                    <Link :href="route('souvenirs.index')" class="text-xs font-semibold text-blue-600">Jelajahi etalase</Link>
                </div>
                <div class="panel-surface overflow-hidden">
                    <article v-for="order in page.props.souvenirOrders.data" :key="order.id" class="flex flex-wrap items-center justify-between gap-4 border-b border-slate-100 p-5 last:border-0">
                        <div>
                            <p class="text-sm font-bold">{{ order.vendor.name }}</p>
                            <p class="mt-1 text-xs text-slate-500">{{ order.items.map(item => item.name).join(', ') }}</p>
                            <p class="mt-2 font-mono text-xs text-slate-400">{{ order.reference }}</p>
                            <p class="mt-2 text-xs">{{ { awaiting_payment: 'Menunggu pembayaran', paid: 'Pembayaran diterima', processing: 'Disiapkan toko', shipped: 'Dikirim', ready_for_pickup: 'Siap diambil', completed: 'Selesai', cancelled: 'Dibatalkan', expired: 'Kedaluwarsa' }[order.status] || order.status }}</p>
                            <p class="mt-2 text-sm font-bold text-blue-600">{{ money(order.total) }}</p>
                        </div>
                        <div class="flex gap-2">
                            <Link v-if="order.status === 'awaiting_payment' && order.payment?.status === 'pending'" :href="route('checkout.payment', { type: 'souvenir', id: order.id })" class="panel-primary">Lanjut bayar</Link>
                            <Link :href="route('souvenirs.orders.show', order.id)" class="panel-secondary">Detail pesanan</Link>
                        </div>
                    </article>
                    <Pagination :records="page.props.souvenirOrders" :hide-counter="section === 'bookings'" />
                </div>
            </template>

            <!-- Centered Empty State for Open PO using /Assets/Images/account/notfound.svg -->
            <div v-else-if="section === 'bookings' && bookingKind === 'po'" class="overflow-hidden rounded-2xl border border-[#dce6f4] bg-white p-8 sm:p-12 shadow-[0_4px_24px_rgba(23,75,120,0.04)]">
                <div class="mx-auto flex max-w-lg flex-col items-center justify-center text-center">
                    <img
                        src="/Assets/Images/account/notfound.svg"
                        alt="Belum ada pesanan Open PO"
                        class="mx-auto h-48 w-auto max-w-full object-contain sm:h-56"
                        loading="lazy"
                    />
                    <div class="mt-6 max-w-md">
                        <p class="text-[11px] font-bold uppercase tracking-[0.14em] text-[#3e7bef]">
                            Pesanan Open PO
                        </p>
                        <h3 class="mt-2 text-lg font-extrabold leading-snug text-[#17345e] sm:text-xl">
                            {{ bookingState === 'all' ? 'Belum ada pesanan Open PO' : 'Belum ada pesanan untuk status ini' }}
                        </h3>
                        <p class="mt-3 text-xs leading-6 text-slate-500">
                            {{ bookingState === 'all'
                                ? 'Pesanan oleh-oleh dan pre-order produk lokal kamu akan muncul di sini setelah transaksi dibuat.'
                                : 'Coba pilih status pesanan lainnya untuk melihat riwayat Open PO kamu.' }}
                        </p>
                    </div>
                    <div class="mt-6 flex flex-wrap items-center justify-center gap-3">
                        <Link
                            :href="route('souvenirs.index')"
                            class="inline-flex min-h-11 items-center justify-center rounded-xl bg-[#3e7bef] px-6 text-xs font-bold text-white shadow-xs transition hover:bg-[#2866d4]"
                        >
                            Jelajahi Oleh-oleh
                        </Link>
                    </div>
                </div>
            </div>

            <!-- Standard fallback for other contexts -->
            <div v-else-if="section !== 'bookings'" class="panel-surface p-8 text-center text-xs text-slate-500">
                Belum ada pesanan oleh-oleh.
            </div>
        </section>
    </div>
</template>
