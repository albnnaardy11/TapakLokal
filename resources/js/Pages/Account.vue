<script setup>
import { Head, Link, router, usePage } from '@inertiajs/vue3';
import { route } from 'ziggy-js';
import { computed } from 'vue';
import { ClipboardList, Coins, Gift, Heart, Headset, LogOut, MessageSquare, ReceiptText, Settings, Star, Users, Wallet, ChevronRight } from 'lucide-vue-next';
import MainNavigation from '../Components/Shared/MainNavigation.vue';
import AccountLive from '../Components/Account/AccountLive.vue';
const page = usePage();
const groups = [
    { label: 'AKTIVITAS & FINANSIAL', items: [['bookings', 'Pemesanan & Tiket', ClipboardList], ['transactions', 'Daftar Transaksi', ReceiptText], ['wallet', 'Saldo & Pembayaran', Wallet], ['points', 'Points', Coins], ['vouchers', 'Voucher', Gift]] },
    { label: 'INTERAKSI & KOMUNITAS', items: [['favorites', 'OT & OP Favorit', Heart], ['travelers', 'Daftar Wisatawan', Users], ['chat', 'Chat', MessageSquare], ['reviews', 'Rating & Ulasan', Star], ['support', 'Pesan Bantuan', Headset]] },
    { label: 'AKUN', items: [['settings', 'Akun Saya', Settings]] },
];
const menu = groups.flatMap(group => group.items);
const user = computed(() => page.props.auth.user);
const navigate = key => router.get(route('account.section', key), {}, { preserveScroll: true });
</script>
<template>
    <Head :title="page.props.sectionLabel" />
    <div class="min-h-screen overflow-x-hidden bg-[#f7f9fb] text-[#303e4c]">
        <MainNavigation />
        <div class="mx-auto grid max-w-[1220px] gap-5 px-4 py-5 sm:px-5 lg:grid-cols-[260px_minmax(0,1fr)] lg:gap-7">
            <label class="flex flex-col gap-2 text-xs font-semibold lg:hidden">Menu akun<select :value="page.props.sectionKey" class="panel-input" @change="navigate($event.target.value)"><option v-for="[key, label] in menu" :key="key" :value="key">{{ label }}</option></select></label>
            <aside class="hidden self-start overflow-hidden rounded-2xl border border-[#dce5f0] bg-white shadow-[0_3px_16px_rgba(15,44,92,0.04)] lg:sticky lg:top-[118px] lg:block lg:max-h-[calc(100dvh-134px)] lg:overflow-y-auto">
                <div class="p-4"><div class="flex items-center gap-3"><span class="grid size-14 shrink-0 place-items-center rounded-full bg-[#3E7BEF] text-lg font-bold text-white">{{ user.name.charAt(0).toUpperCase() }}</span><div class="min-w-0"><h1 class="truncate text-sm font-bold">{{ user.name }}</h1><p class="mt-1 truncate text-[10px] text-slate-400">{{ user.email }}</p></div></div></div>
                <nav aria-label="Menu akun" class="px-2.5 pb-3">
                    <div v-for="group in groups" :key="group.label"><p class="mx-3 mb-2 mt-4 border-b border-slate-100 pb-2 text-[9px] font-semibold tracking-wide text-slate-400">{{ group.label }}</p><Link v-for="[key, label, icon] in group.items" :key="key" :href="route('account.section', key)" preserve-scroll class="flex min-h-11 w-full items-center gap-3 rounded-xl px-3 py-2.5 text-left text-[13px] font-medium transition-colors" :class="page.props.sectionKey === key ? 'bg-[#3E7BEF] text-white shadow-sm' : 'text-slate-600 hover:bg-[#edf3ff] hover:text-[#3E7BEF]'" :aria-current="page.props.sectionKey === key ? 'page' : undefined"><component :is="icon" class="size-4 shrink-0" />{{ label }}</Link></div>
                    <Link :href="route('logout')" method="post" as="button" class="mt-3 flex min-h-11 w-full items-center gap-3 rounded-xl px-3 text-[13px] font-medium text-rose-500 hover:bg-rose-50"><LogOut class="size-4" />Keluar</Link>
                </nav>
            </aside>
            <main class="min-w-0">
                <section class="relative isolate mb-5 flex min-h-[150px] flex-col justify-center overflow-hidden rounded-xl bg-[#123b53] px-5 py-5 text-white sm:min-h-[164px] sm:px-6"><img src="https://images.unsplash.com/photo-1464822759023-fed622ff2c3b?auto=format&fit=crop&w=1000&q=85" alt="Pegunungan Indonesia" class="absolute inset-0 -z-20 size-full object-cover" /><div class="absolute inset-0 -z-10 bg-gradient-to-r from-[#123b53]/90 via-[#123b53]/35 to-transparent"></div><h2 class="text-lg font-extrabold">Perjalanan baru, cerita baru.</h2><p class="mt-1 text-[11px] text-white/80">Temukan pengalaman tak terlupakan di setiap sudut Indonesia.</p><Link :href="route('catalog')" class="mt-3 inline-flex w-fit items-center gap-3 rounded-full bg-white px-3.5 py-2 text-[10px] font-bold text-[#175a9f]">Jelajahi Destinasi<ChevronRight class="size-3" /></Link></section>
                <AccountLive :key="page.props.sectionKey" />
            </main>
        </div>
    </div>
</template>

