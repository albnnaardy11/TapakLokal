<script setup>
import { Head, Link, router, usePage } from '@inertiajs/vue3';
import { route } from 'ziggy-js';
import { computed } from 'vue';
import { Award, Coins, Gift, Heart, Headset, LogOut, MessageSquare, Settings, Star, Users, Wallet, ChevronRight } from 'lucide-vue-next';
import MainNavigation from '../Components/Shared/MainNavigation.vue';
import PriorityBar from '../Components/Shared/PriorityBar.vue';
import AccountLive from '../Components/Account/AccountLive.vue';
import BookingPassIcon from '../Components/Shared/BookingPassIcon.vue';
import PurchaseListIcon from '../Components/Shared/PurchaseListIcon.vue';

const page = usePage();
const groups = [
    { label: 'AKTIVITAS & FINANSIAL', items: [['bookings', 'Pemesanan & Tiket', BookingPassIcon], ['transactions', 'Daftar Transaksi', PurchaseListIcon], ['wallet', 'Saldo & Pembayaran', Wallet], ['points', 'Points', Coins], ['vouchers', 'Voucher', Gift]] },
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
            
            <!-- Mobile User Profile & Tab Navigation Header (lg:hidden) -->
            <div class="space-y-3 lg:hidden">
                <!-- User Profile Header Card -->
                <div class="rounded-2xl border border-[#dce5f0] bg-white p-3.5 shadow-2xs space-y-3">
                    <div class="flex items-center justify-between gap-3">
                        <div class="flex items-center gap-3 min-w-0">
                            <div class="size-11 shrink-0 overflow-hidden rounded-full bg-[#0194f3] shadow-xs">
                                <img
                                    v-if="user.avatar"
                                    :src="user.avatar"
                                    :alt="user.name"
                                    referrerpolicy="no-referrer"
                                    class="size-full object-cover"
                                />
                                <span v-else class="grid size-full place-items-center text-base font-bold text-white">
                                    {{ user.name?.charAt(0).toUpperCase() || 'U' }}
                                </span>
                            </div>
                            <div class="min-w-0">
                                <h1 class="truncate text-sm font-bold text-slate-900">{{ user.name }}</h1>
                                <p class="truncate text-[10.5px] text-slate-400">{{ user.email }}</p>
                            </div>
                        </div>
                        <Link
                            :href="route('logout')"
                            method="post"
                            as="button"
                            class="rounded-xl border border-rose-200 bg-rose-50/70 p-2 text-rose-600 hover:bg-rose-100 transition shrink-0"
                            title="Keluar dari akun"
                            aria-label="Keluar dari akun"
                        >
                            <LogOut class="size-4" />
                        </Link>
                    </div>

                    <!-- Mobile Priority Tier Bar -->
                    <PriorityBar />
                </div>

                <!-- Horizontal Scrollable Tab Bar (Traveloka / Airbnb Style) -->
                <nav class="flex gap-2 overflow-x-auto pb-1 [scrollbar-width:none] [&::-webkit-scrollbar]:hidden" aria-label="Menu akun mobile">
                    <Link
                        v-for="[key, label, icon] in menu"
                        :key="key"
                        :href="route('account.section', key)"
                        preserve-scroll
                        class="inline-flex min-h-10 shrink-0 items-center gap-2 rounded-xl px-3.5 py-2 text-xs font-bold transition shadow-2xs"
                        :class="page.props.sectionKey === key ? 'bg-[#0194f3] text-white ring-2 ring-[#0194f3]/30' : 'bg-white border border-slate-200/90 text-slate-700 hover:bg-slate-50'"
                    >
                        <component
                            :is="icon"
                            class="size-4 shrink-0"
                            :class="page.props.sectionKey === key ? 'text-white' : 'text-[#0194f3]'"
                        />
                        <span>{{ label }}</span>
                    </Link>
                </nav>
            </div>

            <!-- Desktop Sticky Sidebar -->
            <aside class="hidden self-start overflow-hidden rounded-2xl border border-[#dce5f0] bg-white shadow-[0_3px_16px_rgba(15,44,92,0.04)] lg:sticky lg:top-[118px] lg:block lg:max-h-[calc(100dvh-134px)] lg:overflow-y-auto">
                <div class="p-4 border-b border-slate-100">
                    <div class="flex items-center gap-3">
                        <div class="size-14 shrink-0 overflow-hidden rounded-full bg-[#0194f3] shadow-xs">
                            <img
                                v-if="user.avatar"
                                :src="user.avatar"
                                :alt="user.name"
                                referrerpolicy="no-referrer"
                                class="size-full object-cover"
                            />
                            <span v-else class="grid size-full place-items-center text-lg font-bold text-white">
                                {{ user.name?.charAt(0).toUpperCase() || 'U' }}
                            </span>
                        </div>
                        <div class="min-w-0">
                            <h1 class="truncate text-sm font-bold text-slate-900">{{ user.name }}</h1>
                            <p class="mt-0.5 truncate text-[10.5px] text-slate-400">{{ user.email }}</p>
                        </div>
                    </div>

                    <!-- METALLIC TRAVELOKA PRIORITY BANNER CARD -->
                    <div class="mt-3.5">
                        <PriorityBar />
                    </div>
                </div>
                <nav aria-label="Menu akun" class="px-2.5 pb-3">
                    <div v-for="group in groups" :key="group.label">
                        <p class="mx-3 mb-2 mt-4 border-b border-slate-100 pb-2 text-[9px] font-semibold tracking-wide text-slate-400">{{ group.label }}</p>
                        <Link
                            v-for="[key, label, icon] in group.items"
                            :key="key"
                            :href="route('account.section', key)"
                            preserve-scroll
                            class="group flex min-h-11 w-full items-center gap-3 rounded-xl px-3 py-2.5 text-left text-[13px] font-medium transition-colors"
                            :class="page.props.sectionKey === key ? 'bg-[#0194f3] text-white shadow-sm font-semibold' : 'text-slate-700 hover:bg-[#edf5ff] hover:text-[#0194f3]'"
                            :aria-current="page.props.sectionKey === key ? 'page' : undefined"
                        >
                            <component
                                :is="icon"
                                class="size-4.5 shrink-0 transition-colors"
                                :class="page.props.sectionKey === key ? 'text-white' : 'text-[#0194f3] group-hover:text-[#0194f3]'"
                            />
                            <span>{{ label }}</span>
                        </Link>
                    </div>
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

