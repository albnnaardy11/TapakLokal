<script setup>
import {
    BadgePercent,
    TicketPercent,
    Briefcase,
    Building2,
    ChevronDown,
    ChevronRight,
    CircleHelp,
    Handshake,
    Menu,
    Search,
    ShoppingBag,
    Store,
    UsersRound,
    X,
} from 'lucide-vue-next';
import { computed, defineAsyncComponent, onBeforeUnmount, onMounted, ref, watch } from 'vue';
import { Link, router, usePage } from '@inertiajs/vue3';
import { route } from 'ziggy-js';

import FlagIcon from './FlagIcon.vue';
import OrderNavigation from './OrderNavigation.vue';

const page = usePage();

const props = defineProps({
    transparentOnTop: {
        type: Boolean,
        default: false,
    },
    hideOnScroll: {
        type: Boolean,
        default: false,
    },
    isStatic: {
        type: Boolean,
        default: false,
    },
});

const isMobileMenuOpen = ref(false);
const openPopover = ref(null);
const currentPage = usePage();
const activeNav = computed(() => {
    if (currentPage.component === 'Blog' || currentPage.component === 'BlogDetail') return 'Cerita Perjalanan';
    if (currentPage.component === 'Welcome') return 'Beranda';
    if (currentPage.component === 'Catalog') return 'Cari Trip';
    if (['OpenPreorder', 'SouvenirMarketplace'].includes(currentPage.component)) return 'Open Preorder';
    return null;
});
const globalSearch = ref('');
const notification = ref('');

const isScrolled = ref(false);
const handleScroll = () => {
    isScrolled.value = window.scrollY > 180;
};

onMounted(() => {
    window.addEventListener('scroll', handleScroll, { passive: true });
    window.addEventListener('keydown', handleGlobalKeydown);
    handleScroll();

    // Otomatis buka modal login / register jika ada parameter ?auth= di URL
    if (typeof window !== 'undefined') {
        const urlParams = new URLSearchParams(window.location.search);
        if (urlParams.has('auth')) {
            const authMode = urlParams.get('auth') === 'register' ? 'register' : 'login';
            openAuthModal(authMode);
            const cleanUrl = new URL(window.location.href);
            cleanUrl.searchParams.delete('auth');
            window.history.replaceState({}, '', cleanUrl.pathname + (cleanUrl.search ? cleanUrl.search : ''));
        }
    }
});

onBeforeUnmount(() => {
    window.removeEventListener('scroll', handleScroll);
    window.removeEventListener('keydown', handleGlobalKeydown);
});

const isTransparent = computed(() => props.transparentOnTop && !isScrolled.value);

const locales = [
    { code: 'id', flag: 'id', label: 'Indonesia · IDR', short: 'IDR | ID', notification: 'Bahasa Indonesia dipilih' },
];
const selectedLocale = ref(locales[0]);

const selectLocale = (loc) => {
    selectedLocale.value = loc;
    notify(loc.notification);
    openPopover.value = null;
};

const navItems = [
    { label: 'Beranda', href: '/', description: 'Kembali ke halaman utama' },
    { label: 'Cari Trip', href: typeof route === 'function' ? route('catalog') : '/cari-trip', description: 'Open trip dan private trip pilihan' },
    { label: 'Open Preorder', href: typeof route === 'function' ? route('open.preorder') : '/open-preorder', description: 'Pre-order produk dan kuliner khas daerah' },
    { label: 'Cerita Perjalanan', href: typeof route === 'function' ? route('blog') : '/blog', description: 'Inspirasi dan panduan untuk perjalananmu' },
];

const notify = (message) => {
    notification.value = message;
    window.setTimeout(() => (notification.value = ''), 2800);
};

const selectNavigation = (item) => {
    isMobileMenuOpen.value = false;
    openPopover.value = null;
    if (item.href) { return; }
    notify(`${item.label} dipilih — ${item.description}`);
};

const AuthModal = defineAsyncComponent(() => import('./AuthModal.vue'));
import AccountDropdown from './AccountDropdown.vue';
const LoginSuccessModal = defineAsyncComponent(() => import('./LoginSuccessModal.vue'));
const AlgoliaSearchModal = defineAsyncComponent(() => import('./AlgoliaSearchModal.vue'));

const isSearchModalOpen = ref(false);
const searchModalQuery = ref('');

const openSearchModal = (query = '') => {
    searchModalQuery.value = query || globalSearch.value || '';
    isSearchModalOpen.value = true;
};

const handleGlobalKeydown = (e) => {
    if ((e.metaKey || e.ctrlKey) && (e.key === 'k' || e.key === 'K')) {
        e.preventDefault();
        openSearchModal();
    }
};

const submitGlobalSearch = () => {
    openSearchModal(globalSearch.value);
};

const isAuthModalOpen = ref(false);
const authModalMode = ref('login');
const authModalTitle = ref("Masuk untuk mulai perjalananmu");
const authModalSubtitle = ref("Simpan trip, kelola pesanan, dan dapatkan poin serta kemudahan transaksi.");

const isSuccessModalOpen = ref(false);
const successUserData = ref(null);

watch(
    () => page.props.flash?.login_success_data,
    (data) => {
        if (data) {
            successUserData.value = data;
            isSuccessModalOpen.value = true;
        }
    },
    { immediate: true }
);

const handleLoginSuccess = (payload) => {
    isAuthModalOpen.value = false;
    successUserData.value = {
        name: payload?.name || page.props.auth?.user?.name || 'Petualang TapakLokal',
        email: payload?.email || page.props.auth?.user?.email || '',
        avatar: payload?.avatar || page.props.auth?.user?.avatar || '',
        role: payload?.role || (page.props.auth?.user?.hasPermission ? (page.props.auth?.user?.hasPermission('vendor.access') ? 'Mitra Bisnis' : 'Wisatawan') : 'Wisatawan'),
        tier: payload?.tier || page.props.auth?.user?.tier || 'Bronze Priority',
    };
    isSuccessModalOpen.value = true;
};

const openAuthModal = (mode = 'login') => {
    if (page.props.auth?.user) {
        router.visit(typeof route === 'function' ? route('account') : '/account');
        return;
    }
    authModalMode.value = mode;
    if (mode === 'register') {
        authModalTitle.value = "Buat akun & mulai petualanganmu!";
        authModalSubtitle.value = "Daftar cepat dengan Google, No. HP, atau gunakan email & nomor WhatsApp.";
    } else {
        authModalTitle.value = "Masuk untuk mulai perjalananmu";
        authModalSubtitle.value = "Simpan trip, kelola pesanan, dan nikmati promo eksklusif TapakLokal.";
    }
    isAuthModalOpen.value = true;
};
</script>

<template>
    <div :class="[isStatic ? 'w-full' : transparentOnTop ? 'h-0' : 'h-[58px] lg:h-[102px]']">
        <header
            :class="[
                isStatic
                    ? 'relative z-40 bg-white text-slate-900 shadow-[0_4px_15px_rgba(15,44,92,0.08)]'
                    : [
                        'fixed inset-x-0 top-0 z-40 transition-all duration-300 ease-in-out',
                        hideOnScroll && isScrolled
                            ? '-translate-y-full opacity-0 pointer-events-none shadow-none'
                            : 'translate-y-0 opacity-100',
                        isTransparent
                            ? 'bg-white/0 text-white shadow-none'
                            : 'bg-white/95 backdrop-blur-md text-slate-900 shadow-[0_4px_15px_rgba(15,44,92,0.08)]'
                    ]
            ]"
        >
            <div class="mx-auto flex h-[58px] max-w-[1180px] items-center px-4 sm:px-6 lg:px-0">
                <!-- Logo -->
                <Link href="/" class="shrink-0 flex items-center gap-2" aria-label="TapakLokal beranda" @click="selectNavigation(navItems[0])">
                    <img
                        src="/Assets/Images/logo.webp"
                        alt="TapakLokal Logo"
                        class="h-7 sm:h-8 w-auto object-contain transition-all duration-300"
                        :class="isTransparent ? 'brightness-0 invert drop-shadow-sm' : ''"
                    />
                </Link>

                <!-- Desktop Right Nav -->
                <div
                    class="ml-auto hidden items-center gap-1 text-[11px] font-semibold transition-colors duration-500 lg:flex"
                    :class="isTransparent ? 'text-white/90' : 'text-slate-700'"
                >
                    <!-- Locale Selector -->
                    <div class="relative">
                        <button
                            class="flex h-8 items-center gap-1.5 rounded-lg px-2 transition-all duration-300"
                            :class="isTransparent ? 'hover:bg-white/15 text-white' : 'hover:bg-[#edf3ff] text-slate-700'"
                            @click="openPopover = openPopover === 'locale' ? null : 'locale'"
                        >
                            <FlagIcon :country="selectedLocale.flag" custom-class="h-3.5 w-5" />
                            <span>{{ selectedLocale.short }}</span>
                            <ChevronDown
                                class="size-3 transition-all duration-300"
                                :class="[
                                    isTransparent ? 'text-[#38bdf8]' : 'text-[#3E7BEF]',
                                    { 'rotate-180': openPopover === 'locale' }
                                ]"
                            />
                        </button>
                        <div v-if="openPopover === 'locale'" class="absolute right-0 top-full z-50 mt-2 w-48 rounded-xl border border-slate-100 bg-white p-1.5 shadow-[0_12px_30px_rgba(15,44,92,0.16)] text-slate-700">
                            <button
                                v-for="loc in locales"
                                :key="loc.code"
                                class="flex w-full items-center gap-2.5 rounded-lg px-3 py-2 text-left transition hover:bg-[#edf3ff]"
                                :class="selectedLocale.code === loc.code ? 'bg-[#edf3ff]/70 font-bold text-[#3E7BEF]' : 'text-slate-700'"
                                @click="selectLocale(loc)"
                            >
                                <FlagIcon :country="loc.flag" custom-class="h-3.5 w-5" />
                                <span>{{ loc.label }}</span>
                                <span v-if="selectedLocale.code === loc.code" class="ml-auto text-[11px] font-bold text-[#3E7BEF]">✓</span>
                            </button>
                        </div>
                    </div>

                    <span class="mx-1 h-5 w-px transition-colors duration-500" :class="isTransparent ? 'bg-white/20' : 'bg-slate-200'"></span>

                    <button
                        class="flex h-8 items-center gap-1 rounded-lg px-2 transition-all duration-300"
                        :class="isTransparent ? 'hover:bg-white/15 text-white' : 'hover:bg-[#edf3ff] text-slate-700'"
                        @click="router.visit(route('discount'))"
                    >
                        <TicketPercent class="size-3.5 transition-colors duration-500" :class="isTransparent ? 'text-[#38bdf8]' : 'text-[#3E7BEF]'" />
                        Discount
                    </button>

                    <!-- Bisnis Dropdown Popover (Mitra Vendor & Corporate B2B) -->
                    <div class="relative">
                        <button
                            type="button"
                            class="flex h-8 items-center gap-1 rounded-lg px-2 transition-all duration-300 cursor-pointer"
                            :class="[
                                isTransparent ? 'hover:bg-white/15 text-white' : 'hover:bg-[#edf3ff] text-slate-700',
                                openPopover === 'business' ? (isTransparent ? 'bg-white/20' : 'bg-[#edf3ff] text-[#3E7BEF]') : ''
                            ]"
                            @click="openPopover = openPopover === 'business' ? null : 'business'"
                        >
                            <Briefcase class="size-3.5 transition-colors duration-500" :class="isTransparent ? 'text-[#38bdf8]' : 'text-[#3E7BEF]'" />
                            <span>Bisnis</span>
                            <ChevronDown
                                class="size-3 transition-transform duration-300"
                                :class="[
                                    isTransparent ? 'text-[#38bdf8]' : 'text-[#3E7BEF]',
                                    { 'rotate-180': openPopover === 'business' }
                                ]"
                            />
                        </button>

                        <div
                            v-if="openPopover === 'business'"
                            class="absolute right-0 top-full z-50 mt-2 w-52 rounded-2xl border border-slate-100 bg-white p-1.5 shadow-[0_12px_32px_rgba(15,44,92,0.12)] text-slate-700"
                        >
                            <Link
                                :href="route('business.partner')"
                                class="group flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-bold text-slate-800 transition hover:bg-[#edf3ff] hover:text-[#0066d6]"
                                @click="openPopover = null"
                            >
                                <Handshake class="size-5 shrink-0 text-slate-800 transition group-hover:text-[#0066d6]" />
                                <span>Partnership</span>
                            </Link>

                            <Link
                                :href="route('business.corporate')"
                                class="group flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-bold text-slate-800 transition hover:bg-[#edf3ff] hover:text-[#0066d6]"
                                @click="openPopover = null"
                            >
                                <Briefcase class="size-5 shrink-0 text-slate-800 transition group-hover:text-[#0066d6]" />
                                <span>For Corporates</span>
                            </Link>

                            <Link
                                :href="route('business.affiliate')"
                                class="group flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-bold text-slate-800 transition hover:bg-[#edf3ff] hover:text-[#0066d6]"
                                @click="openPopover = null"
                            >
                                <BadgePercent class="size-5 shrink-0 text-slate-800 transition group-hover:text-[#0066d6]" />
                                <span>Affiliate</span>
                            </Link>
                        </div>
                    </div>

                    <button
                        class="flex h-8 items-center gap-1 rounded-lg px-2 transition-all duration-300"
                        :class="isTransparent ? 'hover:bg-white/15 text-white' : 'hover:bg-[#edf3ff] text-slate-700'"
                        @click="router.visit(typeof route === 'function' ? route('help.index') : '/bantuan')"
                    >
                        <CircleHelp class="size-3.5 transition-colors duration-500" :class="isTransparent ? 'text-[#38bdf8]' : 'text-[#3E7BEF]'" />
                        Bantuan
                    </button>

                    <span class="mx-1 h-5 w-px transition-colors duration-500" :class="isTransparent ? 'bg-white/20' : 'bg-slate-200'"></span>

                    <!-- Desktop User Profile / Auth State -->
                    <OrderNavigation :is-transparent="isTransparent" />
                    <AccountDropdown
                        v-if="currentPage.props.auth?.user"
                        :is-transparent="isTransparent"
                        class="ml-2"
                    />

                    <template v-else>
                        <button
                            type="button"
                            class="ml-2 rounded-lg px-3 py-2 text-xs font-bold transition-all duration-300 cursor-pointer"
                            :class="isTransparent ? 'text-white hover:bg-white/15' : 'text-[#3E7BEF] hover:bg-[#edf3ff]'"
                            @click="openAuthModal('login')"
                        >
                            Masuk
                        </button>

                        <button
                            type="button"
                            class="rounded-lg px-3 py-2 text-xs font-bold text-white shadow-sm transition-all duration-300 cursor-pointer"
                            :class="isTransparent ? 'bg-[#0066cc] hover:bg-[#0054ad]' : 'bg-[#3E7BEF] hover:bg-[#2e69d9]'"
                            @click="openAuthModal('register')"
                        >
                            Daftar
                        </button>
                    </template>
                </div>

                <!-- Mobile Header Right -->
                <div class="ml-auto mr-1 flex items-center gap-0.5 lg:hidden">
                    <OrderNavigation :is-transparent="isTransparent" />
                    <AccountDropdown
                        v-if="currentPage.props.auth?.user"
                        :compact="true"
                        :is-transparent="isTransparent"
                    />
                    <button
                        v-else
                        type="button"
                        class="min-h-[44px] min-w-[44px] inline-flex items-center justify-center rounded-lg px-2.5 text-xs font-bold transition-colors duration-500 cursor-pointer"
                        :class="isTransparent ? 'text-white hover:bg-white/15' : 'text-[#3E7BEF] hover:bg-[#edf3ff]'"
                        @click="openAuthModal('login')"
                    >
                        Masuk
                    </button>
                </div>

                <button
                    class="min-h-[44px] min-w-[44px] inline-flex items-center justify-center rounded-lg p-2 transition-all duration-300 lg:hidden"
                    :class="isTransparent ? 'text-white hover:bg-white/15' : 'text-[#3E7BEF] hover:bg-[#edf3ff]'"
                    :aria-expanded="isMobileMenuOpen"
                    aria-label="Buka menu navigasi"
                    @click="isMobileMenuOpen = !isMobileMenuOpen"
                >
                    <X v-if="isMobileMenuOpen" class="size-6" />
                    <Menu v-else class="size-6" />
                </button>
            </div>

            <!-- Secondary Nav Row (Desktop) -->
            <nav
                class="hidden lg:block transition-all duration-500 ease-in-out"
                :class="
                    isTransparent
                        ? 'bg-[#3E7BEF]/0'
                        : 'bg-[#3E7BEF] shadow-sm'
                "
            >
                <div class="mx-auto flex h-11 max-w-[1180px] items-center px-4 sm:px-6 lg:px-0">
                    <div class="flex h-full items-center gap-1">
                        <component
                            :is="item.href ? Link : 'button'"
                            :href="item.href"
                            :aria-current="activeNav === item.label ? 'page' : undefined"
                            v-for="item in navItems"
                            :key="item.label"
                            class="relative flex h-8 items-center rounded-lg px-3 text-sm font-semibold transition-all duration-200"
                            :class="
                                isTransparent
                                    ? [
                                        activeNav === item.label ? 'text-white font-bold' : 'text-white/85',
                                        'hover:bg-white/15 hover:text-white'
                                    ]
                                    : [
                                        activeNav === item.label ? 'text-white font-bold' : 'text-white/85',
                                        'hover:bg-white/10 hover:text-white'
                                    ]
                            "
                            @click="selectNavigation(item)"
                        >
                            {{ item.label }}
                            <span v-if="activeNav === item.label" class="absolute inset-x-3 bottom-0.5 h-0.5 rounded-full bg-white"></span>
                        </component>
                    </div>

                    <div class="ml-auto w-56 sm:w-64">
                        <button
                            type="button"
                            @click="openSearchModal(globalSearch)"
                            class="group flex h-7.5 w-full items-center justify-between rounded-full px-3 text-xs shadow-xs transition-all duration-300 cursor-pointer"
                            :class="
                                isTransparent
                                    ? 'bg-white/15 text-white/90 ring-1 ring-white/25 hover:bg-white/25 hover:ring-white/50'
                                    : 'bg-white text-slate-600 hover:bg-slate-50 ring-1 ring-slate-200/80 shadow-inner'
                            "
                            aria-label="Cari destinasi atau trip (Ctrl+K)"
                        >
                            <span class="flex items-center gap-1.5 min-w-0">
                                <Search class="size-3.5 shrink-0 transition-colors duration-500" :class="isTransparent ? 'text-white' : 'text-[#3E7BEF]'" />
                                <span class="truncate" :class="isTransparent ? 'text-white/80' : 'text-slate-400'">
                                    {{ globalSearch || 'Cari destinasi atau trip...' }}
                                </span>
                            </span>
                            <kbd
                                class="hidden sm:inline-flex items-center gap-0.5 rounded px-1.5 py-0.5 text-[10px] font-bold transition"
                                :class="isTransparent ? 'bg-white/20 text-white/90' : 'bg-slate-100 text-slate-500 border border-slate-200'"
                            >
                                <span class="text-[9px]">⌘</span>K
                            </kbd>
                        </button>
                    </div>
                </div>
            </nav>

            <!-- Mobile Menu Dropdown -->
            <div v-if="isMobileMenuOpen" class="max-h-[calc(100dvh-70px)] overflow-y-auto border-t border-slate-100 bg-white px-5 py-4 shadow-[0_12px_24px_rgba(15,44,92,0.12)] text-slate-800 lg:hidden">
                <div class="mb-3.5">
                    <button
                        type="button"
                        @click="isMobileMenuOpen = false; openSearchModal(globalSearch)"
                        class="flex h-11 w-full items-center justify-between rounded-xl border border-slate-200 bg-slate-50 px-3.5 text-left text-sm text-slate-400 transition hover:border-[#3E7BEF] hover:bg-white focus:outline-none"
                    >
                        <span class="flex items-center gap-2.5 min-w-0">
                            <Search class="size-4.5 text-[#3E7BEF]" />
                            <span class="truncate text-slate-500 font-medium">
                                {{ globalSearch || 'Cari destinasi atau trip...' }}
                            </span>
                        </span>
                        <span class="rounded bg-[#edf3ff] px-2 py-0.5 text-[11px] font-bold text-[#3E7BEF]">Cari</span>
                    </button>
                </div>
                <div class="grid grid-cols-2 gap-2">
                    <component
                        :is="item.href ? Link : 'button'"
                        :href="item.href"
                        :aria-current="activeNav === item.label ? 'page' : undefined"
                        v-for="item in navItems"
                        :key="item.label"
                        class="flex min-h-11 items-center rounded-xl px-3 py-2 text-left text-sm font-semibold transition"
                        :class="activeNav === item.label ? 'bg-[#edf3ff] text-[#3E7BEF] font-bold' : 'text-slate-700 hover:bg-[#edf3ff] hover:text-[#3E7BEF]'"
                        @click="selectNavigation(item)"
                    >
                        {{ item.label }}
                    </component>
                </div>

                <!-- Bisnis Section in Mobile Drawer -->
                <div class="mt-4 border-t border-slate-100 pt-3.5">
                    <p class="text-[10px] font-extrabold uppercase tracking-wider text-slate-400 mb-2">Bisnis & Kemitraan</p>
                    <div class="space-y-1.5">
                        <Link
                            :href="route('business.partner')"
                            class="flex min-h-11 items-center gap-3 rounded-xl px-3 py-2 text-xs font-bold text-slate-700 hover:bg-[#edf3ff] hover:text-[#0066d6] transition"
                            @click="isMobileMenuOpen = false"
                        >
                            <Handshake class="size-4.5 text-[#3E7BEF]" />
                            <span>Partnership Vendor</span>
                        </Link>
                        <Link
                            :href="route('business.corporate')"
                            class="flex min-h-11 items-center gap-3 rounded-xl px-3 py-2 text-xs font-bold text-slate-700 hover:bg-[#edf3ff] hover:text-[#0066d6] transition"
                            @click="isMobileMenuOpen = false"
                        >
                            <Briefcase class="size-4.5 text-[#3E7BEF]" />
                            <span>For Corporates (B2B)</span>
                        </Link>
                        <Link
                            :href="route('business.affiliate')"
                            class="flex min-h-11 items-center gap-3 rounded-xl px-3 py-2 text-xs font-bold text-slate-700 hover:bg-[#edf3ff] hover:text-[#0066d6] transition"
                            @click="isMobileMenuOpen = false"
                        >
                            <BadgePercent class="size-4.5 text-[#3E7BEF]" />
                            <span>Affiliate Program</span>
                        </Link>
                    </div>
                </div>

                <div class="mt-4 flex items-center justify-between border-t border-slate-100 pt-3.5">
                    <span class="text-xs font-medium text-slate-500">Bahasa & Mata Uang</span>
                    <div class="flex items-center gap-1.5">
                        <button
                            v-for="loc in locales"
                            :key="loc.code"
                            type="button"
                            class="flex items-center gap-1.5 rounded-lg border px-3 py-1.5 text-xs font-semibold transition"
                            :class="selectedLocale.code === loc.code ? 'border-[#3E7BEF] bg-[#edf3ff] text-[#3E7BEF]' : 'border-slate-200 text-slate-600 hover:bg-slate-50'"
                            @click="selectLocale(loc); isMobileMenuOpen = false"
                        >
                            <FlagIcon :country="loc.flag" custom-class="h-3.5 w-4.5" />
                            <span>{{ loc.short }}</span>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Traveloka 1:1 Auth Modal -->
            <AuthModal v-if="isAuthModalOpen"
                :open="isAuthModalOpen"
                :mode="authModalMode"
                :title="authModalTitle"
                :subtitle="authModalSubtitle"
                @close="isAuthModalOpen = false"
                @login-success="handleLoginSuccess"
            />

            <!-- Traveloka Style Login Success Modal -->
            <LoginSuccessModal v-if="isSuccessModalOpen"
                :open="isSuccessModalOpen"
                :user="successUserData"
                @close="isSuccessModalOpen = false"
            />

            <!-- Toast Notification -->
            <Transition enter-active-class="transition duration-200" enter-from-class="translate-y-2 opacity-0" leave-active-class="transition duration-150" leave-to-class="translate-y-2 opacity-0">
                <div v-if="notification" class="fixed bottom-5 right-5 z-50 max-w-sm rounded-xl border border-[#3E7BEF]/20 bg-white px-4 py-3 text-sm font-semibold text-slate-700 shadow-xl">
                    <span class="mr-2 inline-grid size-5 place-items-center rounded-full bg-[#3E7BEF] text-xs text-white">✓</span>
                    {{ notification }}
                </div>
            </Transition>

            <!-- Algolia DocSearch / Traveloka Style Instant Search Modal -->
            <AlgoliaSearchModal
                v-if="isSearchModalOpen"
                :open="isSearchModalOpen"
                :initial-query="searchModalQuery"
                @close="isSearchModalOpen = false"
            />
        </header>
    </div>
</template>
