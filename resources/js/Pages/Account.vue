<script setup>
import { Head, Link, router, usePage } from '@inertiajs/vue3';
import { route } from 'ziggy-js';
import { computed, ref, watch } from 'vue';
import { CalendarDays, ChevronRight, ClipboardList, Coins, Compass, Gift, Heart, Headset, MapPin, MessageSquare, ReceiptText, Settings, Star, TentTree, Users, Wallet, X } from 'lucide-vue-next';
import MainNavigation from '../Components/Home/MainNavigation.vue';
import AccountTransactions from '../Components/Home/AccountTransactions.vue';
import AccountPoints from '../Components/Home/AccountPoints.vue';
import AccountVouchers from '../Components/Home/AccountVouchers.vue';
import AccountProfile from '../Components/Home/AccountProfile.vue';
import AccountCommunity from '../Components/Home/AccountCommunity.vue';
import AccountWallet from '../Components/Home/AccountWallet.vue';

const activeMenu = ref('Pemesanan & Tiket');
const type = ref('Semua');
const status = ref('Semua');
const detail = ref(null);
const modal = ref(null);
const groups = [
    { label: 'AKTIVITAS & FINANSIAL', items: [['Pemesanan & Tiket', ClipboardList], ['Daftar Transaksi', ReceiptText], ['Saldo & Pembayaran', Wallet], ['Points', Coins], ['Voucher', Gift]] },
    { label: 'INTERAKSI & KOMUNITAS', items: [['OT & OP Favorit', Heart], ['Daftar Wisatawan', Users], ['Chat', MessageSquare]] },
    { label: 'Dukungan & Akun', items: [['Rating & Ulasan', Star], ['Pesan Bantuan', Headset]] },
];
const page = usePage();
const menuItems = [...groups.flatMap((group) => group.items.map(([label]) => label)), 'Akun Saya'];
const navigate = (section) => {
    if (menuItems.includes(section)) {
        router.get(route('account'), { section }, { preserveState: true, preserveScroll: true });
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
const showOrder = computed(() => type.value !== 'Open PO' && ['Semua', 'Selesai'].includes(status.value));
const showDetail = (item) => { detail.value = item; modal.value.showModal(); };
</script>

<template>
    <Head :title="activeMenu" />
    <div class="min-h-screen bg-[#f7f9fb] text-[#303e4c]">
        <MainNavigation />
        <div class="mx-auto grid max-w-[1220px] gap-5 px-4 py-5 sm:px-5 lg:grid-cols-[260px_minmax(0,1fr)] lg:gap-7">
            <label class="flex flex-col gap-2 text-xs font-semibold lg:hidden">Menu akun<select :value="activeMenu" class="h-11 w-full rounded-xl border border-slate-200 bg-white px-3 text-sm outline-[#078cff]" @change="navigate($event.target.value)"><option v-for="item in menuItems" :key="item">{{ item }}</option></select></label>
            <aside class="hidden self-start overflow-hidden rounded-xl border border-[#d9dfe5] bg-white shadow-sm lg:sticky lg:top-[118px] lg:block lg:max-h-[calc(100dvh-134px)] lg:overflow-y-auto [scrollbar-width:thin]">
                <div class="p-4"><div class="flex items-center gap-3"><span class="grid size-14 place-items-center rounded-full bg-[#0b43a4] text-lg font-bold text-white">A</span><div><h1 class="text-sm font-bold">Albnnaardy</h1><p class="mt-1 text-[10px] text-slate-400">@petualangnyasar</p></div></div><button class="mt-4 flex w-full items-center justify-between rounded-lg bg-gradient-to-r from-[#a87850] to-[#c99b74] px-3 py-2.5 text-xs font-bold text-white" @click="navigate('Points')"><span>✦ Kamu adalah Bronze Priority</span><ChevronRight class="size-4" /></button></div>
                <nav aria-label="Menu akun"><div v-for="group in groups" :key="group.label"><p class="mx-6 mb-2 mt-2 border-b border-slate-100 pb-1 text-[9px] text-slate-400">{{ group.label }}</p><button v-for="[label, icon] in group.items" :key="label" class="flex w-full items-center gap-3 px-6 py-3 text-left text-sm transition-colors" :class="activeMenu === label ? 'bg-[#1045a5] text-white' : 'hover:bg-[#edf5ff]'" :aria-current="activeMenu === label ? 'page' : undefined" @click="navigate(label)"><component :is="icon" class="size-4 shrink-0" :class="activeMenu === label ? 'text-white' : 'text-[#1045a5]'" />{{ label }}</button></div><div class="mt-4 border-t border-slate-200 py-3"><button class="flex w-full items-center gap-3 px-6 py-3 text-left text-sm transition-colors" :class="activeMenu === 'Akun Saya' ? 'bg-[#1045a5] text-white' : 'hover:bg-[#edf5ff]'" :aria-current="activeMenu === 'Akun Saya' ? 'page' : undefined" @click="navigate('Akun Saya')"><Settings class="size-4 shrink-0" :class="activeMenu === 'Akun Saya' ? 'text-white' : 'text-[#1045a5]'" />Akun Saya</button><Link href="/" class="flex items-center gap-3 px-6 py-3 text-sm hover:bg-sky-50"><Compass class="size-4 text-[#1045a5]" />Kembali ke Beranda</Link></div></nav>
            </aside>
            <main class="min-w-0">
                <div v-if="activeMenu === 'Pemesanan & Tiket'" class="relative isolate overflow-hidden rounded-lg bg-[#123b53] px-4 py-4 text-white"><img :src="mountain" alt="Pegunungan Indonesia" class="absolute inset-0 -z-20 size-full object-cover" /><div class="absolute inset-0 -z-10 bg-gradient-to-r from-[#123b53]/90 via-[#123b53]/35 to-transparent"></div><h2 class="text-lg font-extrabold">Perjalanan baru, cerita baru.</h2><p class="text-[9px] text-white/80">Temukan pengalaman tak terlupakan di setiap sudut Indonesia.</p><Link href="/" class="mt-2 inline-flex items-center gap-3 rounded-full bg-white px-3 py-1.5 text-[9px] font-bold text-[#175a9f]">Jelajahi Destinasi<ChevronRight class="size-3" /></Link></div>
                <template v-if="activeMenu === 'Pemesanan & Tiket'">
                    <div class="mb-3 mt-4"><h2 class="text-sm font-extrabold text-[#183660]">Pemesanan Saya</h2><p class="text-[9px] text-slate-400">Kelola seluruh perjalanan dan tiket yang sudah kamu pesan.</p></div>
                    <div class="mb-3 flex flex-wrap justify-between gap-3"><div class="flex gap-1 rounded-full bg-white p-1"><button v-for="item in ['Semua', 'Open Trip', 'Open PO']" :key="item" class="rounded-full px-3 py-1.5 text-[9px] font-semibold" :class="type === item ? 'bg-[#09a1ed] text-white' : 'text-slate-500 hover:bg-sky-50'" @click="type = item">{{ item }}</button></div><div class="flex items-center gap-1"><button v-for="item in ['Semua', 'Berlangsung', 'Selesai', 'Batal']" :key="item" class="rounded-full px-3 py-1 text-[8px] font-semibold" :class="status === item ? 'bg-white text-[#078cff] shadow-sm' : 'text-slate-400 hover:text-[#078cff]'" @click="status = item">{{ item }}</button></div></div>
                    <article v-if="showOrder" class="flex flex-wrap gap-3 rounded-xl bg-white p-2 shadow-[0_3px_15px_rgba(23,75,120,0.05)]"><div class="relative h-24 w-36 overflow-hidden rounded-lg"><img :src="beach" alt="Pantai Drini" class="size-full object-cover" /><span class="absolute left-2 top-2 rounded bg-emerald-500 px-2 py-0.5 text-[8px] text-white">Selesai</span></div><div class="flex-1 py-1"><h3 class="text-xs font-bold">Pantai Drini</h3><p class="mt-1 flex items-center gap-1 text-[9px] text-slate-400"><MapPin class="size-3" />Gunungkidul, Yogyakarta</p><p class="mt-2 flex items-center gap-2 text-[9px] text-slate-400"><CalendarDays class="size-3" />24 Sep 2025 <Users class="size-3" />2 Orang</p><span class="mt-3 inline-block rounded bg-[#effafa] px-3 py-1 text-[8px] text-teal-600">+800 Poin didapat</span></div><div class="flex flex-col items-end justify-center gap-2 p-1"><strong class="text-xs">Rp 702.800</strong><span class="rounded bg-teal-50 px-2 py-0.5 text-[8px] text-teal-600">Pembayaran berhasil</span><div class="flex gap-2"><button class="rounded-md border border-sky-100 px-4 py-2 text-[9px] text-[#078cff] hover:bg-sky-50" @click="showDetail({ name: 'Pantai Drini', location: 'Gunungkidul, Yogyakarta', price: 'Rp 702.800', image: beach })">Detail</button><Link href="/" class="rounded-md bg-[#078cff] px-4 py-2 text-[9px] text-white">Pesan Lagi</Link></div></div></article>
                    <div v-else class="rounded-xl border border-dashed border-slate-200 bg-white py-8 text-center text-xs text-slate-400">Belum ada pesanan untuk filter ini.</div>
                </template>
                <AccountTransactions v-show="activeMenu === 'Daftar Transaksi'" />
                <AccountPoints v-show="activeMenu === 'Points'" />
                <AccountVouchers v-show="activeMenu === 'Voucher'" />
                <AccountProfile v-show="activeMenu === 'Akun Saya'" />
                <AccountCommunity v-show="['OT & OP Favorit', 'Daftar Wisatawan', 'Chat', 'Rating & Ulasan', 'Pesan Bantuan'].includes(activeMenu)" :section="activeMenu" />
                <AccountWallet v-show="activeMenu === 'Saldo &amp; Pembayaran'" @transactions="navigate('Daftar Transaksi')" />
<section v-if="!menuItems.includes(activeMenu)" class="my-4 rounded-xl border border-slate-100 bg-white p-6"><h2 class="text-lg font-bold text-[#183660]">{{ activeMenu }}</h2><div v-if="activeMenu === 'Akun Saya'" class="mt-4 space-y-3 text-sm"><p>Nama: Albnnaardy</p><p>Username: @petualangnyasar</p></div><p v-else class="mt-4 text-sm text-slate-400">Belum ada data {{ activeMenu.toLowerCase() }} untuk akun contoh ini.</p><button class="mt-4 text-xs font-semibold text-[#078cff]" @click="activeMenu = 'Pemesanan & Tiket'">Kembali ke pemesanan</button></section>
                <section v-if="activeMenu === 'Pemesanan & Tiket'" class="mt-5"><div class="mb-3 flex items-center justify-between"><div class="flex gap-2"><TentTree class="size-5 text-[#078cff]" /><div><h2 class="text-xs font-bold text-[#183660]">Open Trip</h2><p class="text-[8px] text-slate-400">Jelajahi berbagai pengalaman seru bersama traveler lainnya.</p></div></div><Link href="/" class="text-[9px] text-[#078cff]">Lihat Semua →</Link></div><div class="grid grid-cols-2 gap-2 lg:grid-cols-4"><button v-for="trip in trips" :key="trip.name" class="group overflow-hidden rounded-lg border border-slate-100 bg-white text-left transition hover:-translate-y-0.5 hover:shadow-md" @click="showDetail(trip)"><div class="relative h-20 overflow-hidden"><img :src="trip.image" :alt="trip.name" class="size-full object-cover transition group-hover:scale-105" /><span class="absolute left-2 top-2 flex items-center gap-1 rounded-full bg-white px-1.5 py-0.5 text-[8px]"><Star class="size-2.5 fill-yellow-400 text-yellow-400" />{{ trip.rating }}</span><span class="absolute bottom-1 left-2 rounded-full bg-[#078cff] px-2 text-[7px] text-white">Open Trip</span></div><div class="p-2"><h3 class="text-[9px] font-bold leading-tight">{{ trip.name }}</h3><p class="mt-1 text-[8px] text-slate-400">{{ trip.location }}</p><p class="mt-1 text-[8px] text-slate-400">Mei 20 · 3H2M</p><p class="mt-2 text-[10px] font-bold text-[#078cff]">{{ trip.price }}</p></div></button></div></section>
                <section v-if="activeMenu === 'Pemesanan & Tiket'" class="mt-4"><h2 class="text-xs font-bold text-[#183660]">🍴 Lengkapi Perjalananmu</h2><p class="mb-2 text-[8px] text-slate-400">Rekomendasi kuliner dan oleh-oleh di sekitar destinasi yang kamu kunjungi.</p><div class="grid gap-2 sm:grid-cols-3"><Link v-for="(label, index) in ['Kuliner', 'Oleh-Oleh', 'Aktivitas']" :key="label" href="/" class="flex items-center gap-2 rounded-lg border border-slate-100 bg-white p-1.5 hover:bg-sky-50"><img :src="trips[index].image" alt="" class="h-9 w-14 rounded object-cover" /><div class="flex-1"><h3 class="text-[9px] font-bold">{{ label }}</h3><p class="text-[8px] text-slate-400">Pilihan lokal untuk perjalananmu</p></div><ChevronRight class="size-3 text-[#078cff]" /></Link></div></section>
                <div v-if="activeMenu === 'Pemesanan & Tiket'" class="mt-3 flex items-center gap-3 rounded-lg bg-gradient-to-r from-[#078bd5] to-[#36b9ee] px-4 py-3 text-white"><Compass class="size-7" /><div class="flex-1"><h2 class="text-xs font-bold">Masih ingin menjelajah?</h2><p class="text-[8px] text-white/80">Temukan destinasi dan pengalaman menarik bersama Tapak Lokal.</p></div><Link href="/" class="rounded-full bg-white px-3 py-1.5 text-[9px] text-[#078cff]">Jelajahi Trip →</Link></div>
                <p v-if="activeMenu === 'Pemesanan & Tiket'" class="mt-4 text-[9px] text-slate-400">Pratinjau akun · Profil, pesanan, dan saldo menggunakan data contoh.</p>
            </main>
        </div>
        <dialog ref="modal" class="fixed inset-0 m-auto w-[calc(100%-2rem)] max-w-md rounded-2xl p-5 text-slate-700 backdrop:bg-slate-900/50"><div v-if="detail"><button autofocus class="float-right rounded-full p-2 hover:bg-slate-100" aria-label="Tutup detail" @click="modal.close()"><X class="size-4" /></button><h2 class="mb-4 text-lg font-bold">{{ detail.name }}</h2><img :src="detail.image" :alt="detail.name" class="h-44 w-full rounded-xl object-cover" /><p class="mt-4 text-sm">{{ detail.location }}</p><p class="mt-2 font-bold text-[#078cff]">{{ detail.price }}</p><p class="mt-3 text-xs text-slate-400">Informasi contoh untuk pratinjau halaman akun.</p></div></dialog>
    </div>
</template>
