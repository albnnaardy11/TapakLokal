<script setup>
import { Head, Link, router, usePage } from '@inertiajs/vue3';
import { route } from 'ziggy-js';
import { computed, ref, watch } from 'vue';
import { CheckCircle2, Clock3, CircleSlash, CalendarDays, ChevronDown, ChevronRight, ClipboardList, Coins, Compass, Gift, Heart, Headset, LogOut, MapPin, MessageSquare, ReceiptText, Search, Settings, Star, TentTree, Users, Wallet, X } from 'lucide-vue-next';
import MainNavigation from '../Components/Home/MainNavigation.vue';
import AccountTransactions from '../Components/Home/AccountTransactions.vue';
import AccountPoints from '../Components/Home/AccountPoints.vue';
import AccountVouchers from '../Components/Home/AccountVouchers.vue';
import AccountProfile from '../Components/Home/AccountProfile.vue';
import AccountCommunity from '../Components/Home/AccountCommunity.vue';
import AccountWallet from '../Components/Home/AccountWallet.vue';
import AccountExplore from '../Components/Home/AccountExplore.vue';
import AccountOrderList from '../Components/Home/AccountOrderList.vue';

const activeMenu = ref('Pemesanan & Tiket');
const type = ref('Semua');
const status = ref('Semua');
const orderQuery = ref('');
const orderYear = ref('Semua Tahun');
const detail = ref(null);
const modal = ref(null);
const logoutDialog = ref(null);
const groups = [
    { label: 'AKTIVITAS & FINANSIAL', items: [['Pemesanan & Tiket', ClipboardList], ['Daftar Transaksi', ReceiptText], ['Saldo & Pembayaran', Wallet], ['Points', Coins], ['Voucher', Gift]] },
    { label: 'INTERAKSI & KOMUNITAS', items: [['OT & OP Favorit', Heart], ['Daftar Wisatawan', Users], ['Chat', MessageSquare]] },
    { label: 'Dukungan & Akun', items: [['Rating & Ulasan', Star], ['Pesan Bantuan', Headset]] },
];
const page = usePage();
const loading = ref(false);
const navigationError = ref('');
const menuItems = [...groups.flatMap((group) => group.items.map(([label]) => label)), 'Akun Saya'];
const navigate = (section) => {
    if (menuItems.includes(section)) {
        router.get(route('account'), { section }, {
            preserveState: true,
            preserveScroll: true,
            onStart: () => { loading.value = true; navigationError.value = ''; },
            onFinish: () => { loading.value = false; },
            onError: () => { navigationError.value = 'Halaman belum berhasil dimuat. Silakan coba lagi.'; },
        });
    }
};
watch(() => page.url, (url) => {
    const section = new URL(url, 'http://localhost').searchParams.get('section');
    const allowed = [...groups.flatMap((group) => group.items.map(([label]) => label)), 'Akun Saya'];
    activeMenu.value = allowed.includes(section) ? section : 'Pemesanan & Tiket';
}, { immediate: true });
const photo = (id) => `https://images.unsplash.com/photo-${id}?auto=format&fit=crop&w=1000&q=85`;
const mountain = photo('1464822759023-fed622ff2c3b');
const beach = photo('1518548419970-58e3b4079ab2');
const trips = [
    { name: 'Open Trip Mt. Merbabu via Suwanting', location: 'Magelang, Jawa Tengah', price: 'Rp 702.800', image: mountain, rating: '4.8' },
    { name: 'Open Trip Ranu Kumbolo', location: 'Lumajang, Jawa Timur', price: 'Rp 1.250.000', image: photo('1501179691627-eeaa65ea017c'), rating: '4.7' },
    { name: 'Open Trip Candi Prambanan', location: 'Sleman, Yogyakarta', price: 'Rp 450.000', image: photo('1537996194471-e657df975ab4'), rating: '4.8' },
    { name: 'Open Trip Karimunjawa', location: 'Jepara, Jawa Tengah', price: 'Rp 1.500.000', image: beach, rating: '4.9' },
];
const orders = [
    { id: 'TL-2025-001', name: 'Pantai Drini', location: 'Gunungkidul, Yogyakarta', price: 'Rp 702.800', image: beach, type: 'Open Trip', status: 'Selesai', date: '24 Sep 2025', travelers: 2, points: 800, payment: 'Pembayaran berhasil' },
    ...['Karimunjawa', 'Ranu Kumbolo', 'Prambanan', 'Pulau Pramuka', 'Merbabu', 'Dieng', 'Labuan Bajo', 'Bromo', 'Raja Ampat', 'Lombok', 'Bali'].map((destination, index) => ({
        id: `DEMO-ORDER-${index + 2}`, name: index % 4 === 0 ? `Oleh-oleh ${destination}` : `Open Trip ${destination}`,
        location: destination, price: `Rp ${(450000 + index * 125000).toLocaleString('id-ID')}`,
        image: index % 2 === 0 ? beach : mountain, type: index % 4 === 0 ? 'Open PO' : 'Open Trip',
        status: ['Selesai', 'Berlangsung', 'Batal'][index % 3], date: `${index + 10} Sep 2026`, travelers: 2,
        points: index % 3 === 0 ? 300 : 0, payment: index % 3 === 2 ? 'Pesanan dibatalkan' : 'Pembayaran berhasil',
    })),
];
const filteredOrders = computed(() => orders.filter((order) => {
    const matchesType = type.value === 'Semua' || order.type === type.value;
    const matchesStatus = status.value === 'Semua' || order.status === status.value;
    const matchesYear = orderYear.value === 'Semua Tahun' || order.date.includes(orderYear.value);
    const query = orderQuery.value.trim().toLowerCase();
    const matchesQuery = !query || `${order.id} ${order.name} ${order.location}`.toLowerCase().includes(query);

    return matchesType && matchesStatus && matchesYear && matchesQuery;
}));
const showDetail = (item) => { detail.value = item; modal.value.showModal(); };
const confirmLogout = () => { logoutDialog.value.showModal(); };
const logout = () => { window.location.assign('/'); };
</script>

<template>
    <Head :title="activeMenu" />
    <div class="min-h-screen bg-[#f7f9fb] text-[#303e4c]">
        <MainNavigation />
        <div class="mx-auto grid max-w-[1220px] gap-5 px-4 py-5 sm:px-5 lg:grid-cols-[260px_minmax(0,1fr)] lg:gap-7">
            <label class="flex flex-col gap-2 text-xs font-semibold lg:hidden">Menu akun<select :value="activeMenu" class="h-11 w-full rounded-xl border border-slate-200 bg-white px-3 text-sm outline-[#078cff]" @change="navigate($event.target.value)"><option v-for="item in menuItems" :key="item">{{ item }}</option></select></label>
            <aside class="hidden self-start overflow-hidden rounded-2xl border border-[#dce5f0] bg-white shadow-[0_3px_16px_rgba(15,44,92,0.04)] lg:sticky lg:top-[118px] lg:block lg:max-h-[calc(100dvh-134px)] lg:overflow-y-auto [scrollbar-width:thin] [scrollbar-color:#d5e2f2_transparent]">
                <div class="p-4"><div class="flex items-center gap-3"><span class="grid size-14 place-items-center rounded-full bg-[#3E7BEF] text-lg font-bold text-white">A</span><div><h1 class="text-sm font-bold">Albnnaardy</h1><p class="mt-1 text-[10px] text-slate-400">@petualangnyasar</p></div></div><button class="mt-4 flex w-full items-center justify-between rounded-lg bg-gradient-to-r from-[#a87850] to-[#c99b74] px-3 py-2.5 text-xs font-bold text-white" @click="navigate('Points')"><span>✦ Kamu adalah Bronze Priority</span><ChevronRight class="size-4" /></button></div>
                <nav aria-label="Menu akun" class="px-2.5 pb-2"><div v-for="group in groups" :key="group.label"><p class="mx-3 mb-2 mt-5 border-b border-slate-100 pb-2 text-[9px] font-semibold tracking-wide text-slate-400">{{ group.label }}</p><button v-for="[label, icon] in group.items" :key="label" class="flex min-h-11 w-full items-center gap-3 rounded-xl px-3 py-2.5 text-left text-[13px] font-medium transition-colors duration-200 focus-visible:outline-2 focus-visible:outline-offset-[-2px] focus-visible:outline-[#3E7BEF]" :class="activeMenu === label ? 'bg-[#3E7BEF] text-white shadow-[0_3px_8px_rgba(62,123,239,0.16)]' : 'text-slate-600 hover:bg-[#edf3ff] hover:text-[#3E7BEF]'" :aria-current="activeMenu === label ? 'page' : undefined" @click="navigate(label)"><component :is="icon" class="size-4 shrink-0" :class="activeMenu === label ? 'text-white' : 'text-[#3E7BEF]'" />{{ label }}</button></div><div class="mt-4 border-t border-slate-100 pt-3"><button class="flex min-h-11 w-full items-center gap-3 rounded-xl px-3 py-2.5 text-left text-[13px] font-medium transition-colors duration-200 focus-visible:outline-2 focus-visible:outline-offset-[-2px] focus-visible:outline-[#3E7BEF]" :class="activeMenu === 'Akun Saya' ? 'bg-[#3E7BEF] text-white shadow-[0_3px_8px_rgba(62,123,239,0.16)]' : 'text-slate-600 hover:bg-[#edf3ff] hover:text-[#3E7BEF]'" :aria-current="activeMenu === 'Akun Saya' ? 'page' : undefined" @click="navigate('Akun Saya')"><Settings class="size-4 shrink-0" :class="activeMenu === 'Akun Saya' ? 'text-white' : 'text-[#3E7BEF]'" />Akun Saya</button><button type="button" class="mt-1 flex min-h-11 w-full items-center gap-3 rounded-xl px-3 py-2.5 text-left text-[13px] font-medium text-slate-600 transition-colors hover:bg-rose-50 hover:text-rose-600 focus-visible:outline-2 focus-visible:outline-offset-[-2px] focus-visible:outline-rose-500" @click="confirmLogout"><LogOut class="size-4 shrink-0 text-rose-500" />Keluar</button></div></nav>
            </aside>
            <main class="min-w-0" :aria-busy="loading"><p v-if="navigationError" role="alert" class="mb-4 rounded-xl bg-rose-50 p-4 text-sm text-rose-600">{{ navigationError }}</p><div v-if="loading" role="status" class="space-y-4"><span class="sr-only">Memuat halaman akun…</span><div aria-hidden="true" class="space-y-4 animate-pulse motion-reduce:animate-none"><div class="h-7 w-48 rounded-lg bg-slate-200"></div><div class="h-32 rounded-2xl bg-slate-200/70"></div><div class="grid grid-cols-2 gap-3"><div v-for="n in 4" :key="n" class="h-24 rounded-xl bg-slate-200/60"></div></div><div class="h-56 rounded-2xl bg-slate-200/60"></div></div></div><div v-show="!loading">
                <div v-if="activeMenu === 'Pemesanan & Tiket'" class="relative isolate overflow-hidden rounded-lg bg-[#123b53] px-4 py-4 text-white"><img :src="mountain" alt="Pegunungan Indonesia" class="absolute inset-0 -z-20 size-full object-cover" /><div class="absolute inset-0 -z-10 bg-gradient-to-r from-[#123b53]/90 via-[#123b53]/35 to-transparent"></div><h2 class="text-lg font-extrabold">Perjalanan baru, cerita baru.</h2><p class="text-[9px] text-white/80">Temukan pengalaman tak terlupakan di setiap sudut Indonesia.</p><Link href="/" class="mt-2 inline-flex items-center gap-3 rounded-full bg-white px-3 py-1.5 text-[9px] font-bold text-[#175a9f]">Jelajahi Destinasi<ChevronRight class="size-3" /></Link></div>
                <template v-if="activeMenu === 'Pemesanan & Tiket'">
                    <div class="mb-3 mt-4"><h2 class="text-sm font-extrabold text-[#183660]">Pemesanan Saya</h2><p class="text-[9px] text-slate-400">Kelola seluruh perjalanan dan tiket yang sudah kamu pesan.</p></div>
                    <section class="mb-3 space-y-2.5" aria-label="Filter pemesanan">
                        <div class="flex gap-2 overflow-x-auto pb-1 [scrollbar-width:thin]" aria-label="Filter jenis pesanan"><button v-for="item in ['Semua', 'Open Trip', 'Open PO']" :key="item" type="button" class="group inline-flex min-h-8 shrink-0 items-center gap-2 rounded-xl border px-3 py-1.5 text-[9px] font-semibold transition-colors duration-200 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-[#3E7BEF]" :class="type === item ? 'border-[#3E7BEF] bg-[#3E7BEF] text-white shadow-[0_3px_9px_rgba(62,123,239,0.22)]' : 'border-[#e1eaf5] bg-white text-[#56708d] hover:border-[#b9d5fa] hover:bg-[#edf4ff] hover:text-[#3E7BEF]'" :aria-pressed="type === item" @click="type = item"><span class="grid size-4 place-items-center rounded" :class="type === item ? 'bg-white/15' : 'bg-[#edf4ff] text-[#3E7BEF]'"><component :is="item === 'Semua' ? ClipboardList : item === 'Open Trip' ? TentTree : Gift" class="size-3" :stroke-width="1.8" aria-hidden="true" /></span>{{ item }}</button></div>
                        <div class="grid gap-2 sm:grid-cols-[minmax(0,1.8fr)_minmax(128px,.72fr)_minmax(128px,.72fr)_minmax(112px,.52fr)]"><label class="flex min-h-9 items-center gap-2 rounded-lg border border-[#e1eaf5] bg-white px-3 focus-within:border-[#9fc8fb] focus-within:ring-2 focus-within:ring-blue-50"><Search class="size-3.5 shrink-0 text-[#3E7BEF]" /><input v-model="orderQuery" type="search" placeholder="Cari nama trip atau kode pesanan…" aria-label="Cari pesanan" class="min-w-0 flex-1 bg-transparent text-[9px] text-[#183660] outline-none placeholder:text-slate-400" /></label><label class="relative flex min-h-9 items-center gap-2 rounded-lg border border-[#e1eaf5] bg-white px-3 text-[#56708d] focus-within:border-[#9fc8fb] focus-within:ring-2 focus-within:ring-blue-50"><ClipboardList class="size-3.5 shrink-0 text-[#3E7BEF]" /><select v-model="type" aria-label="Jenis pesanan" class="min-w-0 flex-1 appearance-none bg-transparent pr-4 text-[9px] outline-none"><option value="Semua">Semua pesanan</option><option>Open Trip</option><option>Open PO</option></select><ChevronDown class="pointer-events-none absolute right-2.5 size-3 text-[#7d9abb]" /></label><label class="relative flex min-h-9 items-center gap-2 rounded-lg border border-[#e1eaf5] bg-white px-3 text-[#56708d] focus-within:border-[#9fc8fb] focus-within:ring-2 focus-within:ring-blue-50"><CheckCircle2 class="size-3.5 shrink-0 text-[#3E7BEF]" /><select v-model="status" aria-label="Status pesanan" class="min-w-0 flex-1 appearance-none bg-transparent pr-4 text-[9px] outline-none"><option value="Semua">Semua status</option><option>Berlangsung</option><option>Selesai</option><option>Batal</option></select><ChevronDown class="pointer-events-none absolute right-2.5 size-3 text-[#7d9abb]" /></label><label class="relative flex min-h-9 items-center gap-2 rounded-lg border border-[#e1eaf5] bg-white px-3 text-[#56708d] focus-within:border-[#9fc8fb] focus-within:ring-2 focus-within:ring-blue-50"><CalendarDays class="size-3.5 shrink-0 text-[#3E7BEF]" /><select v-model="orderYear" aria-label="Tahun pesanan" class="min-w-0 flex-1 appearance-none bg-transparent pr-4 text-[9px] outline-none"><option>Semua Tahun</option><option>2026</option><option>2025</option></select><ChevronDown class="pointer-events-none absolute right-2.5 size-3 text-[#7d9abb]" /></label></div>
                    </section>
                    <AccountOrderList :orders="filteredOrders" @detail="showDetail" />
                </template>
                <AccountTransactions v-show="activeMenu === 'Daftar Transaksi'" />
                <AccountPoints v-show="activeMenu === 'Points'" />
                <AccountVouchers v-show="activeMenu === 'Voucher'" />
                <AccountProfile v-show="activeMenu === 'Akun Saya'" />
                <AccountCommunity v-show="['OT & OP Favorit', 'Daftar Wisatawan', 'Chat', 'Rating & Ulasan', 'Pesan Bantuan'].includes(activeMenu)" :section="activeMenu" />
                <AccountWallet v-show="activeMenu === 'Saldo &amp; Pembayaran'" @transactions="navigate('Daftar Transaksi')" />
<section v-if="!menuItems.includes(activeMenu)" class="my-4 rounded-xl border border-slate-100 bg-white p-6"><h2 class="text-lg font-bold text-[#183660]">{{ activeMenu }}</h2><div v-if="activeMenu === 'Akun Saya'" class="mt-4 space-y-3 text-sm"><p>Nama: Albnnaardy</p><p>Username: @petualangnyasar</p></div><p v-else class="mt-4 text-sm text-slate-400">Belum ada data {{ activeMenu.toLowerCase() }} untuk akun contoh ini.</p><button class="mt-4 text-xs font-semibold text-[#078cff]" @click="activeMenu = 'Pemesanan & Tiket'">Kembali ke pemesanan</button></section>
                <section v-if="activeMenu === 'Pemesanan & Tiket'" class="mt-5"><div class="mb-3 flex items-center justify-between"><div class="flex gap-2"><TentTree class="size-5 text-[#078cff]" /><div><h2 class="text-xs font-bold text-[#183660]">Open Trip</h2><p class="text-[8px] text-slate-400">Jelajahi berbagai pengalaman seru bersama traveler lainnya.</p></div></div><Link href="/" class="text-[9px] text-[#078cff]">Lihat Semua →</Link></div><div class="grid grid-cols-2 gap-2 lg:grid-cols-4"><button v-for="trip in trips" :key="trip.name" class="group overflow-hidden rounded-lg border border-slate-100 bg-white text-left transition hover:-translate-y-0.5 hover:shadow-md" @click="showDetail(trip)"><div class="relative h-20 overflow-hidden"><img :src="trip.image" :alt="trip.name" class="size-full object-cover transition group-hover:scale-105" /><span class="absolute left-2 top-2 flex items-center gap-1 rounded-full bg-white px-1.5 py-0.5 text-[8px]"><Star class="size-2.5 fill-yellow-400 text-yellow-400" />{{ trip.rating }}</span><span class="absolute bottom-1 left-2 rounded-full bg-[#078cff] px-2 text-[7px] text-white">Open Trip</span></div><div class="p-2"><h3 class="text-[9px] font-bold leading-tight">{{ trip.name }}</h3><p class="mt-1 text-[8px] text-slate-400">{{ trip.location }}</p><p class="mt-1 text-[8px] text-slate-400">Mei 20 · 3H2M</p><p class="mt-2 text-[10px] font-bold text-[#078cff]">{{ trip.price }}</p></div></button></div></section>
                <AccountExplore v-if="activeMenu === 'Pemesanan &amp; Tiket'" />
                <p v-if="activeMenu === 'Pemesanan & Tiket'" class="mt-4 text-[9px] text-slate-400">Pratinjau akun · Profil, pesanan, dan saldo menggunakan data contoh.</p>
            </div></main>
        </div>
        <dialog ref="modal" class="fixed inset-0 m-auto w-[calc(100%-2rem)] max-w-md rounded-2xl p-5 text-slate-700 backdrop:bg-slate-900/50"><div v-if="detail"><button autofocus class="float-right rounded-full p-2 hover:bg-slate-100" aria-label="Tutup detail" @click="modal.close()"><X class="size-4" /></button><h2 class="mb-4 text-lg font-bold">{{ detail.name }}</h2><img :src="detail.image" :alt="detail.name" class="h-44 w-full rounded-xl object-cover" /><p class="mt-4 text-sm">{{ detail.location }}</p><p class="mt-2 font-bold text-[#078cff]">{{ detail.price }}</p><p class="mt-3 text-xs text-slate-400">Informasi contoh untuk pratinjau halaman akun.</p></div></dialog>
        <dialog ref="logoutDialog" class="fixed inset-0 m-auto w-[calc(100%-2rem)] max-w-[400px] rounded-2xl border border-[#e1eaf5] bg-white p-0 text-slate-700 shadow-[0_20px_60px_rgba(15,44,92,0.24)] backdrop:bg-slate-900/45" aria-labelledby="logout-heading"><div class="p-5 sm:p-6"><div class="flex items-start gap-3"><span class="grid size-10 shrink-0 place-items-center rounded-xl bg-rose-50 text-rose-600"><LogOut class="size-[19px]" /></span><div class="min-w-0 flex-1"><h2 id="logout-heading" class="text-[15px] font-bold text-[#183660]">Keluar dari akun?</h2><p class="mt-1.5 text-[11px] leading-5 text-slate-500">Sesi akunmu akan diakhiri dan kamu kembali ke beranda.</p></div><button type="button" aria-label="Tutup konfirmasi" class="-mt-1 rounded-full p-2 text-slate-400 transition-colors hover:bg-slate-100 hover:text-slate-600" @click="logoutDialog.close()"><X class="size-4" /></button></div><div class="mt-4 rounded-xl border border-rose-100 bg-rose-50/70 px-3 py-2.5 text-[10px] leading-4 text-rose-700">Pastikan tidak ada perubahan penting yang belum disimpan sebelum keluar.</div></div><div class="flex flex-col-reverse gap-2 border-t border-slate-100 px-5 py-4 sm:flex-row sm:justify-end sm:px-6"><button type="button" autofocus class="min-h-10 rounded-lg border border-[#dbe7f5] bg-white px-4 text-xs font-semibold text-[#38506c] transition-colors hover:bg-slate-50 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-[#3E7BEF]" @click="logoutDialog.close()">Batal</button><button type="button" class="min-h-10 rounded-lg bg-rose-500 px-4 text-xs font-semibold text-white transition-colors hover:bg-rose-600 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-rose-500" @click="logout">Ya, keluar</button></div></dialog>
    </div>
</template>
