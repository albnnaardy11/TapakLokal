<script setup>
import { Link, router, usePage } from '@inertiajs/vue3';
import { route } from 'ziggy-js';
import { computed, onBeforeUnmount, onMounted, ref } from 'vue';
import {
    Award,
    ChevronDown,
    ChevronRight,
    ClipboardList,
    Coins,
    Gift,
    Heart,
    Headset,
    LayoutDashboard,
    LogIn,
    LogOut,
    ReceiptText,
    UserRound,
    Users,
    Wallet
} from 'lucide-vue-next';

defineProps({ compact: { type: Boolean, default: false } });

const page = usePage();
const authUser = computed(() => page.props.auth?.user);

const open = ref(false);
const root = ref(null);
const trigger = ref(null);

const groups = [
    {
        title: 'AKUN & PEMBAYARAN',
        items: [
            ['Dashboard', LayoutDashboard, 'Ringkasan', '/dashboard'],
            ['Akun Saya', UserRound, 'Edit profil', null],
            ['Points', Coins, 'TapakPoints', null],
            ['Saldo & Pembayaran', Wallet, '', null],
            ['Daftar Transaksi', ReceiptText, '', null],
        ],
    },
    {
        title: 'PERJALANANMU',
        items: [
            ['Pemesanan & Tiket', ClipboardList, '', null],
            ['OT & OP Favorit', Heart, '', null],
            ['Daftar Wisatawan', Users, '', null],
            ['Voucher', Gift, '', null],
            ['Pesan Bantuan', Headset, '', null],
        ],
    },
];

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

const handleLogout = () => {
    close();
    router.post(route('logout'));
};

onMounted(() => document.addEventListener('pointerdown', outside));
onBeforeUnmount(() => document.removeEventListener('pointerdown', outside));
</script>

<template>
    <div ref="root" class="relative" @keydown.esc.stop.prevent="close(true)" @focusout="!$event.currentTarget.contains($event.relatedTarget) && close()">
        <!-- 1. Guest State (Not Logged In) -->
        <template v-if="!authUser">
            <Link
                :href="route('login')"
                class="inline-flex items-center gap-2 rounded-xl border border-[#3E7BEF]/30 bg-[#edf3ff] px-3.5 py-1.5 text-xs font-bold text-[#3E7BEF] shadow-sm transition hover:bg-[#3E7BEF] hover:text-white active:scale-95"
                :class="{ 'size-9 justify-center p-0': compact }"
                title="Masuk ke Akun"
            >
                <LogIn class="size-4 shrink-0" />
                <span v-if="!compact">Masuk / Daftar</span>
            </Link>
        </template>

        <!-- 2. Authenticated State -->
        <template v-else>
            <button
                ref="trigger"
                type="button"
                class="flex items-center gap-2 rounded-xl p-1.5 text-left transition hover:bg-[#edf5ff] focus-visible:outline-2 focus-visible:outline-[#3E7BEF]"
                :aria-expanded="open"
                aria-label="Menu akun"
                @click="open = !open"
            >
                <img
                    v-if="authUser.avatar"
                    :src="authUser.avatar"
                    :alt="authUser.name"
                    class="size-8 rounded-full border border-sky-300 object-cover"
                />
                <span
                    v-else
                    class="grid size-8 place-items-center rounded-full border-2 border-[#B7874B] bg-[#3E7BEF] text-xs font-bold text-white ring-2 ring-[#f6ead9]"
                >
                    {{ authUser.name.charAt(0).toUpperCase() }}
                </span>

                <template v-if="!compact">
                    <span class="max-w-[100px] truncate">
                        <span class="block truncate text-[11px] font-bold text-slate-800">{{ authUser.name }}</span>
                        <span class="block text-[9px] capitalize text-slate-400">{{ authUser.member_tier }} Priority</span>
                    </span>
                    <span class="mx-1 h-6 w-px bg-slate-200"></span>
                    <span class="text-center">
                        <span class="block text-sm font-bold text-[#3E7BEF]">{{ authUser.points || 0 }}</span>
                        <span class="block text-[9px] text-slate-400">points</span>
                    </span>
                    <ChevronDown class="size-3.5 text-[#3E7BEF] transition-transform duration-200" :class="{ 'rotate-180': open }" />
                </template>
            </button>

            <!-- Dropdown Menu -->
            <Transition
                enter-active-class="transition duration-200 ease-out"
                enter-from-class="translate-y-2 scale-95 opacity-0"
                leave-active-class="transition duration-150 ease-in"
                leave-to-class="translate-y-1 scale-95 opacity-0"
            >
                <div
                    v-if="open"
                    class="absolute right-0 top-full z-50 mt-2.5 max-h-[calc(100dvh-76px)] w-[286px] max-w-[calc(100vw-24px)] origin-top-right overflow-y-auto rounded-2xl border border-[#e1eaf5] bg-white shadow-[0_14px_42px_rgba(15,44,92,0.18)] [scrollbar-width:thin]"
                    aria-label="Pilihan akun"
                >
                    <!-- Header Banner inside dropdown -->
                    <div class="bg-gradient-to-br from-[#183660] to-[#3E7BEF] p-4 text-white">
                        <p class="truncate text-[14px] font-extrabold">{{ authUser.name }}</p>
                        <p class="truncate text-[10px] text-white/70">{{ authUser.email }}</p>
                        <Link
                            :href="route('dashboard')"
                            class="mt-2.5 flex items-center gap-1.5 rounded-lg bg-white/20 px-2.5 py-1 text-[10px] font-bold text-white backdrop-blur-sm transition hover:bg-white/30"
                            @click="close()"
                        >
                            <Award class="size-3.5" />
                            <span class="capitalize">{{ authUser.member_tier }} Priority · Buka Dashboard</span>
                            <ChevronRight class="ml-auto size-3" />
                        </Link>
                    </div>

                    <!-- Groups Navigation -->
                    <div class="p-2">
                        <div v-for="group in groups" :key="group.title">
                            <p class="px-2.5 pb-1 pt-2 text-[8px] font-semibold tracking-wider text-slate-400">
                                {{ group.title }}
                            </p>
                            <Link
                                v-for="[label, icon, extra, directUrl] in group.items"
                                :key="label"
                                :href="directUrl || route('account', { section: label })"
                                class="group flex min-h-9 items-center gap-2.5 rounded-xl px-2.5 py-2 text-[11px] font-medium text-[#34475a] transition duration-200 hover:bg-[#edf6ff] hover:text-[#3E7BEF] focus-visible:outline-2 focus-visible:outline-[#3E7BEF]"
                                @click="close()"
                            >
                                <component :is="icon" class="size-4 text-[#3E7BEF]" :stroke-width="1.7" />
                                <span class="flex-1">{{ label }}</span>
                                <span v-if="extra" class="text-[9px] text-slate-400">{{ extra }}</span>
                                <ChevronRight v-else class="size-3 text-slate-300 transition-transform group-hover:translate-x-0.5" />
                            </Link>
                        </div>
                    </div>

                    <!-- Logout Button -->
                    <div class="border-t border-slate-100 p-2">
                        <button
                            type="button"
                            class="flex min-h-9 w-full items-center gap-2.5 rounded-xl px-2.5 py-2 text-[11px] font-semibold text-rose-600 transition hover:bg-rose-50 focus-visible:outline-2 focus-visible:outline-rose-400"
                            @click="handleLogout"
                        >
                            <LogOut class="size-4" />
                            <span>Keluar dari Akun</span>
                        </button>
                    </div>
                </div>
            </Transition>
        </template>
    </div>
</template>
