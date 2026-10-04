<script setup>
import { computed, onBeforeUnmount, onMounted, ref } from 'vue';
import { Link, usePage } from '@inertiajs/vue3';
import { route } from 'ziggy-js';
import {
    Award,
    ChevronDown,
    ChevronRight,
    ClipboardList,
    Clock3,
    Coins,
    Crown,
    Gift,
    Heart,
    Headset,
    LogOut,
    MessageSquare,
    ReceiptText,
    Settings,
    Shield,
    Star,
    User as UserIcon,
    Users,
    Wallet,
} from 'lucide-vue-next';

const props = defineProps({
    compact: { type: Boolean, default: false },
    isTransparent: { type: Boolean, default: false },
});

const page = usePage();
const user = computed(() => page.props.auth?.user);
const permissions = computed(() => page.props.auth?.permissions || []);
const isAdmin = computed(() => permissions.value.includes('admin.access'));
const isVendor = computed(() => !isAdmin.value && permissions.value.includes('vendor.access'));
const isTraveler = computed(() => !isAdmin.value && !isVendor.value);
const adminPanels = [
    ['super', 'Super Admin', 'system.view'],
    ['content', 'Content Admin', 'content.view'],
    ['operations', 'Operations Admin', 'operations.view'],
    ['finance', 'Finance Admin', 'finance.view'],
    ['growth', 'Growth Admin', 'growth.view'],
];
const roleLabel = computed(() => isAdmin.value
    ? adminPanels.filter(([, , permission]) => permissions.value.includes(permission)).map(([, label]) => label).join(' · ') || 'Admin'
    : isVendor.value ? 'Mitra Vendor' : 'Traveler');

const open = ref(false);
const root = ref(null);
const trigger = ref(null);

const points = computed(() => user.value?.points ?? 0);
const tier = computed(() => user.value?.tier ?? 'Bronze');

// Professional Traveloka Priority standard color palette
const tierConfig = computed(() => {
    if (!isTraveler.value) return {
        gradientBorder: 'linear-gradient(135deg, #078cff, #173b70)',
        glowShadow: '0 2px 8px rgba(7,140,255,0.18)',
        avatarBg: 'bg-[#0175ea]', headerGradient: 'from-[#f5faff] to-[#e8f3ff]',
        textTitle: 'text-[#173b70]', textSub: 'text-[#526e91]', iconColor: 'text-[#0175ea]',
    };
    const t = (tier.value || 'Bronze').toLowerCase();
    if (t === 'gold') {
        return {
            name: 'Gold Priority',
            gradientBorder: 'linear-gradient(135deg, #C5963E 0%, #9E7321 100%)',
            glowShadow: '0 2px 8px rgba(197, 150, 62, 0.25)',
            avatarBg: 'bg-gradient-to-br from-[#0064d2] to-[#0047BA]',
            badgeBg: 'bg-gradient-to-r from-[#C5963E] to-[#9E7321] text-white font-bold',
            headerGradient: 'from-[#fef9ee] via-[#fdf3dc] to-[#fae8bf]',
            textTitle: 'text-[#78350f]',
            textSub: 'text-[#92400e]',
            iconColor: 'text-[#9E7321]',
        };
    }
    if (t === 'silver') {
        return {
            name: 'Silver Priority',
            gradientBorder: 'linear-gradient(135deg, #748091 0%, #546071 100%)',
            glowShadow: '0 2px 8px rgba(116, 128, 145, 0.25)',
            avatarBg: 'bg-gradient-to-br from-[#0064d2] to-[#0047BA]',
            badgeBg: 'bg-gradient-to-r from-[#748091] to-[#546071] text-white',
            headerGradient: 'from-[#f8fafc] via-[#f1f5f9] to-[#e2e8f0]',
            textTitle: 'text-slate-800',
            textSub: 'text-slate-600',
            iconColor: 'text-[#546071]',
        };
    }
    // Bronze Priority (Traveloka signature warm bronze)
    return {
        name: 'Bronze Priority',
        gradientBorder: 'linear-gradient(135deg, #A86B3E 0%, #874E25 100%)',
        glowShadow: '0 2px 8px rgba(168, 107, 62, 0.25)',
        avatarBg: 'bg-gradient-to-br from-[#0064d2] to-[#0047BA]',
        badgeBg: 'bg-gradient-to-r from-[#A86B3E] to-[#874E25] text-white',
        headerGradient: 'from-[#fcf6f0] via-[#f8ebe0] to-[#f0d8c2]',
        textTitle: 'text-[#4a230f]',
        textSub: 'text-[#7c3f1c]',
        iconColor: 'text-[#874E25]',
    };
});

import BookingPassIcon from './BookingPassIcon.vue';
import PurchaseListIcon from './PurchaseListIcon.vue';

const handle = computed(() => {
    if (!user.value) return '@petualang';
    if (user.value.username) return `@${user.value.username}`;
    if (user.value.email) return `@${user.value.email.split('@')[0]}`;
    return '@petualangnyasar';
});

const travelerGroups = [
    {
        title: 'AKUN & PEMBAYARAN',
        items: [
            { key: 'payments', label: 'Menunggu Pembayaran', icon: Clock3 },
            { key: 'transactions', label: 'Daftar Transaksi', icon: PurchaseListIcon },
            { key: 'wallet', label: 'Metode Pembayaran', icon: Wallet },
            { key: 'points', label: 'Points', icon: Coins, extra: computed(() => `${points.value} poin`) },
            { key: 'settings', label: 'Akun Saya', icon: Settings, extra: 'Edit profil' },
        ],
    },
    {
        title: 'PERJALANANMU',
        items: [
            { key: 'bookings', label: 'Pemesanan & Tiket', icon: BookingPassIcon },
            { key: 'favorites', label: 'OT & OP Favorit', icon: Heart },
            { key: 'travelers', label: 'Daftar Wisatawan', icon: Users },
            { key: 'vouchers', label: 'Voucher', icon: Gift },
            { key: 'support', label: 'Pesan Bantuan', icon: Headset },
        ],
    },
];

const groups = computed(() => {
    if (isAdmin.value) return [{ title: 'PANEL PENGELOLAAN', items: adminPanels
        .filter(([, , permission]) => permissions.value.includes(permission))
        .map(([key, label]) => ({ key, label, icon: Shield, href: route('admin.panel.dashboard', { panel: key }) })) }];
    if (isVendor.value) return [{ title: 'PORTAL MITRA', items: [
        ['dashboard', 'Overview', ClipboardList], ['trips', 'Trip & Jadwal', BookingPassIcon],
        ['bookings', 'Pemesanan', PurchaseListIcon], ['finance', 'Keuangan', Wallet],
        ['reviews', 'Rating & Ulasan', Star], ['profile', 'Profil & Verifikasi', Settings],
        ['support', 'Bantuan & Chat', Headset],
    ].map(([key, label, icon]) => ({ key, label, icon, href: route('vendor.section', key) }))
        .concat([{ key: 'souvenirs', label: 'Produk & Pesanan Oleh-oleh', icon: Gift, href: route('vendor.souvenirs') }]) }];
    return travelerGroups;
});

const close = (restore = false) => {
    open.value = false;
    if (restore) {
        trigger.value?.focus();
    }
};

const outside = (event) => {
    if (!root.value?.contains(event.target)) {
        close();
    }
};

onMounted(() => document.addEventListener('pointerdown', outside));
onBeforeUnmount(() => document.removeEventListener('pointerdown', outside));
</script>

<template>
    <div
        v-if="user"
        ref="root"
        class="relative inline-block text-left"
        @keydown.esc.stop.prevent="close(true)"
        @focusout="!$event.currentTarget.contains($event.relatedTarget) && close()"
    >
        <!-- TRIGGER BUTTON (1:1 with Traveloka Priority Member Bar) -->
        <button
            ref="trigger"
            type="button"
            class="group flex items-center gap-2.5 rounded-2xl py-1 px-2 text-left transition-all duration-200 cursor-pointer select-none focus-visible:outline-2 focus-visible:outline-[#078cff]"
            :class="[
                isTransparent
                    ? 'hover:bg-white/15'
                    : 'hover:bg-[#edf5ff]/90',
                open ? (isTransparent ? 'bg-white/20' : 'bg-[#edf5ff]') : '',
            ]"
            :aria-expanded="open"
            aria-label="Menu akun pengguna"
            @click="open = !open"
        >
            <!-- Avatar with Metallic Gradient Border (1 Color Family) -->
            <div
                class="relative size-9.5 shrink-0 rounded-full p-[2px] transition-all duration-300 group-hover:scale-105"
                :style="{
                    background: tierConfig.gradientBorder,
                    boxShadow: tierConfig.glowShadow,
                }"
            >
                <div class="size-full rounded-full overflow-hidden bg-white p-[1px]">
                    <img
                        v-if="user.avatar"
                        :src="user.avatar"
                        :alt="user.name"
                        referrerpolicy="no-referrer"
                        class="size-full rounded-full object-cover"
                    />
                    <div
                        v-else
                        class="flex size-full items-center justify-center rounded-full text-white"
                        :class="tierConfig.avatarBg"
                    >
                        <UserIcon class="size-4.5 stroke-[2.2]" />
                    </div>
                </div>
            </div>

            <!-- User Name & Handle Subtitle -->
            <div v-if="!compact" class="min-w-0 pr-0.5">
                <p
                    class="truncate text-[13px] font-extrabold leading-tight tracking-tight transition-colors duration-200"
                    :class="isTransparent ? 'text-white' : 'text-slate-900'"
                >
                    {{ user.name }}
                </p>
                <p
                    class="truncate text-[11px] font-medium leading-tight transition-colors duration-200"
                    :class="isTransparent ? 'text-white/80' : 'text-slate-500'"
                >
                    {{ handle }}
                </p>
            </div>

            <!-- Dropdown Chevron Arrow -->
            <ChevronDown
                class="size-3.5 shrink-0 transition-transform duration-200"
                :class="[
                    open ? 'rotate-180' : '',
                    isTransparent ? 'text-white/90' : 'text-[#3E7BEF]',
                ]"
            />
        </button>

        <!-- DROPDOWN MENU POPOVER -->
        <Transition
            enter-active-class="transition duration-200 ease-out"
            enter-from-class="translate-y-2 scale-95 opacity-0"
            leave-active-class="transition duration-150 ease-in"
            leave-to-class="translate-y-1 scale-95 opacity-0"
        >
            <div
                v-if="open"
                class="absolute right-0 top-full z-50 mt-2.5 max-h-[calc(100dvh-80px)] w-[290px] max-w-[calc(100vw-24px)] origin-top-right overflow-y-auto rounded-2xl border border-slate-200/90 bg-white shadow-[0_16px_48px_rgba(15,44,92,0.18)] [scrollbar-width:thin]"
                aria-label="Menu akun"
            >
                <!-- Card Header with Tier Metallic Gradient -->
                <div class="bg-gradient-to-br px-4.5 py-4 border-b border-slate-100" :class="tierConfig.headerGradient">
                    <div class="flex items-center gap-3">
                        <div
                            class="size-12 shrink-0 rounded-full p-[2px] shadow-sm"
                            :style="{
                                background: tierConfig.gradientBorder,
                                boxShadow: tierConfig.glowShadow,
                            }"
                        >
                            <div class="size-full rounded-full overflow-hidden bg-white p-[1px]">
                                <img
                                    v-if="user.avatar"
                                    :src="user.avatar"
                                    :alt="user.name"
                                    referrerpolicy="no-referrer"
                                    class="size-full rounded-full object-cover"
                                />
                                <div
                                    v-else
                                    class="flex size-full items-center justify-center rounded-full text-white"
                                    :class="tierConfig.avatarBg"
                                >
                                    <UserIcon class="size-5.5 stroke-[2.2]" />
                                </div>
                            </div>
                        </div>
                        <div class="min-w-0 flex-1">
                            <p class="truncate text-[15px] font-black leading-tight" :class="tierConfig.textTitle">
                                {{ user.name }}
                            </p>
                            <p class="truncate text-xs font-medium opacity-85" :class="tierConfig.textSub">
                                {{ handle }}
                            </p>
                        </div>
                    </div>

                    <p v-if="!isTraveler" class="mt-3 flex items-center gap-2 rounded-xl bg-white/80 px-3 py-2 text-xs font-bold text-[#173b70]"><Shield class="size-4 shrink-0 text-[#0175ea]" />{{ roleLabel }}</p>
                    <!-- Tier Badge & Point Navigation -->
                    <Link
                        v-if="isTraveler"
                        :href="route('account.section', 'points')"
                        class="mt-3 flex items-center justify-between rounded-xl bg-white/85 hover:bg-white px-3 py-2 text-xs font-bold shadow-2xs transition backdrop-blur-xs"
                        :class="tierConfig.textTitle"
                        @click="close()"
                    >
                        <div class="flex items-center gap-1.5">
                            <Award class="size-4" :class="tierConfig.iconColor" />
                            <span>{{ tierConfig.name }}</span>
                        </div>
                        <div class="flex items-center gap-1 text-[11px] font-extrabold text-[#0066cc]">
                            <span>{{ points }} Poin</span>
                            <ChevronRight class="size-3.5 text-slate-400" />
                        </div>
                    </Link>
                </div>

                <Link v-if="isTraveler" :href="route('souvenirs.orders')" class="mx-2 flex min-h-10 items-center gap-2 rounded-xl px-3 text-xs font-semibold text-[#0175ea]" @click="close()"><ReceiptText class="size-4" />Pesanan oleh-oleh</Link>
<!-- Navigation Groups -->
                <div class="p-2 space-y-1">
                    <div v-for="group in groups" :key="group.title">
                        <p class="px-3 pb-1 pt-2.5 text-[9px] font-extrabold tracking-wider text-slate-400 uppercase">
                            {{ group.title }}
                        </p>
                        <Link
                            v-for="item in group.items"
                            :key="item.key"
                            :href="item.href || route('account.section', item.key)"
                            class="group flex min-h-9.5 items-center gap-2.5 rounded-xl px-3 py-2 text-xs font-medium text-slate-700 transition duration-200 hover:bg-[#edf5ff] hover:text-[#0194f3] focus-visible:outline-2 focus-visible:outline-[#0194f3]"
                            @click="close()"
                        >
                            <component
                                :is="item.icon"
                                class="size-4.5 text-[#0194f3] transition-transform group-hover:scale-110"
                                :stroke-width="1.9"
                            />
                            <span class="flex-1 truncate font-semibold">{{ item.label }}</span>
                            <span
                                v-if="typeof item.extra === 'object' ? item.extra?.value : item.extra"
                                class="text-[10px] font-bold text-slate-400 group-hover:text-[#0066cc]"
                            >
                                {{ typeof item.extra === 'object' ? item.extra?.value : item.extra }}
                            </span>
                            <ChevronRight
                                v-else
                                class="size-3 text-slate-300 transition-transform group-hover:translate-x-0.5"
                            />
                        </Link>
                    </div>
                </div>

                <!-- Footer: Logout Action -->
                <div class="border-t border-slate-100 p-2">
                    <Link
                        :href="route('logout')"
                        method="post"
                        as="button"
                        class="flex w-full min-h-9.5 items-center gap-2.5 rounded-xl px-3 py-2 text-xs font-bold text-rose-600 transition hover:bg-rose-50 hover:text-rose-700 cursor-pointer"
                        @click="close()"
                    >
                        <LogOut class="size-4 shrink-0 text-rose-500" />
                        <span>Keluar</span>
                    </Link>
                </div>
            </div>
        </Transition>
    </div>
</template>
