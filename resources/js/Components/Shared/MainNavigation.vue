<script setup>
import { Bell, ChevronDown, CircleHelp, Menu, Search, ShoppingBag, UsersRound, X } from 'lucide-vue-next';
import { computed, onBeforeUnmount, onMounted, ref } from 'vue';
import { Link, router, usePage } from '@inertiajs/vue3';

import FlagIcon from './FlagIcon.vue';

const props = defineProps({
    transparentOnTop: {
        type: Boolean,
        default: false,
    },
});

const isMobileMenuOpen = ref(false);
const openPopover = ref(null);
const currentPage = usePage();
const activeNav = computed(() => (currentPage.component === 'Blog' || currentPage.component === 'BlogDetail') ? 'Cerita Perjalanan' : currentPage.component === 'Welcome' ? 'Beranda' : null);
const globalSearch = ref('');
const notification = ref('');

const isScrolled = ref(false);
const handleScroll = () => {
    isScrolled.value = window.scrollY > 30;
};

onMounted(() => {
    window.addEventListener('scroll', handleScroll, { passive: true });
    handleScroll();
});

onBeforeUnmount(() => {
    window.removeEventListener('scroll', handleScroll);
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
    { label: 'Cari Trip', href: typeof route === 'function' ? route('catalog') : '/catalog', description: 'Open trip dan private trip pilihan' },
    { label: 'Destinasi', href: typeof route === 'function' ? route('explore', 'destination') : '/explore/destination', description: 'Temukan inspirasi perjalanan di Indonesia' },
    { label: 'Kuliner Lokal', href: typeof route === 'function' ? route('explore', 'culinary') : '/explore/culinary', description: 'Produk lokal pilihan dari berbagai daerah' },
    { label: 'Promo', href: typeof route === 'function' ? route('account.section', 'vouchers') : '/account/vouchers', description: 'Penawaran perjalanan dan produk pilihan' },
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

const submitGlobalSearch = () => router.get(typeof route === 'function' ? route('catalog') : '/catalog', { q: globalSearch.value.trim() });
const openAuthModal = (mode) => {
    const target = currentPage.props.auth?.user
        ? (typeof route === 'function' ? route('account') : '/account')
        : mode === 'register'
            ? (typeof route === 'function' ? route('register') : '/register')
            : (typeof route === 'function' ? route('login') : '/login');
    router.visit(target);
};
</script>

<template>
    <div :class="transparentOnTop && !isScrolled ? 'h-0' : 'h-[58px] lg:h-[102px]'">
        <header
            class="fixed inset-x-0 top-0 z-40 transition-all duration-300"
            :class="
                isTransparent
                    ? 'bg-transparent text-white'
                    : 'bg-white/95 backdrop-blur-md text-slate-900 shadow-[0_4px_15px_rgba(15,44,92,0.08)]'
            "
        >
            <div class="mx-auto flex h-[58px] max-w-[1180px] items-center px-5 sm:px-8">
                <!-- Logo -->
                <Link href="/" class="shrink-0 text-left" aria-label="TapakLokal beranda" @click="selectNavigation(navItems[0])">
                    <span
                        class="text-[15px] font-extrabold tracking-[-0.07em] transition-colors"
                        :class="isTransparent ? 'text-white' : 'text-slate-900'"
                    >
                        tapa<span :class="isTransparent ? 'text-[#38bdf8]' : 'text-[#3E7BEF]'">k</span>lokal
                    </span>
                </Link>

                <!-- Desktop Right Nav -->
                <div
                    class="ml-auto hidden items-center gap-1 text-[11px] font-semibold transition-colors xl:flex"
                    :class="isTransparent ? 'text-white/90' : 'text-slate-700'"
                >
                    <!-- Locale Selector -->
                    <div class="relative">
                        <button
                            class="flex h-8 items-center gap-1.5 rounded-lg px-2 transition"
                            :class="isTransparent ? 'hover:bg-white/10 text-white' : 'hover:bg-[#edf3ff] text-slate-700'"
                            @click="openPopover = openPopover === 'locale' ? null : 'locale'"
                        >
                            <FlagIcon :country="selectedLocale.flag" custom-class="h-3.5 w-5" />
                            <span>{{ selectedLocale.short }}</span>
                            <ChevronDown
                                class="size-3 transition-transform duration-150"
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

                    <span class="mx-1 h-5 w-px" :class="isTransparent ? 'bg-white/20' : 'bg-slate-200'"></span>

                    <button
                        class="flex h-8 items-center gap-1 rounded-lg px-2 transition"
                        :class="isTransparent ? 'hover:bg-white/10 text-white' : 'hover:bg-[#edf3ff] text-slate-700'"
                        @click="router.visit(typeof route === 'function' ? route('account.section', 'vouchers') : '/account/vouchers')"
                    >
                        <Bell class="size-3.5" :class="isTransparent ? 'text-[#38bdf8]' : 'text-[#3E7BEF]'" />
                        Promo
                    </button>

                    <button
                        class="flex h-8 items-center gap-1 rounded-lg px-2 transition"
                        :class="isTransparent ? 'hover:bg-white/10 text-white' : 'hover:bg-[#edf3ff] text-slate-700'"
                        @click="router.visit(typeof route === 'function' ? route(currentPage.props.auth?.permissions?.includes('admin.access') ? 'admin.dashboard' : currentPage.props.auth?.permissions?.includes('vendor.access') ? 'vendor.dashboard' : 'account.section', currentPage.props.auth?.permissions?.includes('admin.access') || currentPage.props.auth?.permissions?.includes('vendor.access') ? {} : 'support') : '/account')"
                    >
                        <ShoppingBag class="size-3.5" :class="isTransparent ? 'text-[#38bdf8]' : 'text-[#3E7BEF]'" />
                        Bisnis
                        <ChevronDown class="size-3" :class="isTransparent ? 'text-[#38bdf8]' : 'text-[#3E7BEF]'" />
                    </button>

                    <button
                        class="flex h-8 items-center gap-1 rounded-lg px-2 transition"
                        :class="isTransparent ? 'hover:bg-white/10 text-white' : 'hover:bg-[#edf3ff] text-slate-700'"
                        @click="router.visit(typeof route === 'function' ? route('account.section', 'support') : '/account/support')"
                    >
                        <CircleHelp class="size-3.5" :class="isTransparent ? 'text-[#38bdf8]' : 'text-[#3E7BEF]'" />
                        Bantuan
                    </button>

                    <span class="mx-1 h-5 w-px" :class="isTransparent ? 'bg-white/20' : 'bg-slate-200'"></span>

                    <button
                        type="button"
                        class="ml-2 rounded-lg px-3 py-2 text-xs font-bold transition"
                        :class="isTransparent ? 'text-white hover:bg-white/15' : 'text-[#3E7BEF] hover:bg-[#edf3ff]'"
                        @click="openAuthModal('login')"
                    >
                        {{ currentPage.props.auth?.user ? 'Akun Saya' : 'Masuk' }}
                    </button>

                    <button
                        v-if="!currentPage.props.auth?.user"
                        type="button"
                        class="rounded-lg px-3 py-2 text-xs font-bold text-white shadow-sm transition"
                        :class="isTransparent ? 'bg-[#0088ff] hover:bg-[#0074e0]' : 'bg-[#3E7BEF] hover:bg-[#2e69d9]'"
                        @click="openAuthModal('register')"
                    >
                        Daftar
                    </button>
                </div>

                <!-- Mobile Header Right -->
                <button
                    type="button"
                    class="ml-auto mr-2 min-h-9 rounded-lg px-2.5 text-xs font-bold transition xl:hidden"
                    :class="isTransparent ? 'text-white' : 'text-[#3E7BEF]'"
                    @click="openAuthModal('login')"
                >
                    {{ currentPage.props.auth?.user ? 'Akun Saya' : 'Masuk' }}
                </button>

                <button
                    class="rounded-lg p-2 transition xl:hidden"
                    :class="isTransparent ? 'text-white hover:bg-white/15' : 'text-[#3E7BEF] hover:bg-[#edf3ff]'"
                    :aria-expanded="isMobileMenuOpen"
                    aria-label="Buka menu"
                    @click="isMobileMenuOpen = !isMobileMenuOpen"
                >
                    <X v-if="isMobileMenuOpen" class="size-5" />
                    <Menu v-else class="size-5" />
                </button>
            </div>

            <!-- Secondary Nav Row (Desktop) -->
            <nav
                class="hidden lg:block transition-all duration-300"
                :class="
                    isTransparent
                        ? 'bg-transparent'
                        : 'bg-[#3E7BEF]'
                "
            >
                <div class="mx-auto flex h-11 max-w-[1180px] items-center px-5 sm:px-8">
                    <div class="flex h-full items-center gap-1">
                        <component
                            :is="item.href ? Link : 'button'"
                            :href="item.href"
                            :aria-current="activeNav === item.label ? 'page' : undefined"
                            v-for="item in navItems"
                            :key="item.label"
                            class="relative flex h-8 items-center rounded-lg px-3 text-sm font-semibold transition-all duration-150"
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

                    <form class="ml-auto w-52" @submit.prevent="submitGlobalSearch">
                        <label
                            class="flex h-7 items-center gap-1.5 rounded-full px-3 shadow-sm transition-all"
                            :class="isTransparent ? 'bg-white/15 text-white placeholder:text-white/70 ring-1 ring-white/25 hover:bg-white/25 focus-within:bg-white/30 focus-within:ring-white/50' : 'bg-white text-slate-400'"
                        >
                            <input
                                v-model="globalSearch"
                                class="min-w-0 flex-1 bg-transparent text-xs outline-none"
                                :class="isTransparent ? 'text-white placeholder:text-white/75' : 'text-slate-700 placeholder:text-slate-400'"
                                placeholder="Cari destinasi atau trip"
                            />
                            <Search class="size-3.5" :class="isTransparent ? 'text-white' : 'text-[#3E7BEF]'" />
                        </label>
                    </form>
                </div>
            </nav>

            <!-- Mobile Menu Dropdown -->
            <div v-if="isMobileMenuOpen" class="border-t border-slate-100 bg-white px-5 py-4 shadow-[0_12px_20px_rgba(15,44,92,0.08)] text-slate-800 lg:hidden">
                <form class="mb-3" @submit.prevent="submitGlobalSearch">
                    <label class="flex h-10 items-center gap-2 rounded-xl border border-slate-200 bg-slate-50 px-3 text-slate-400">
                        <Search class="size-4 text-[#3E7BEF]" />
                        <input v-model="globalSearch" class="min-w-0 flex-1 bg-transparent text-sm text-slate-700 outline-none" placeholder="Cari destinasi..." />
                    </label>
                </form>
                <div class="grid grid-cols-2 gap-1.5">
                    <component
                        :is="item.href ? Link : 'button'"
                        :href="item.href"
                        :aria-current="activeNav === item.label ? 'page' : undefined"
                        v-for="item in navItems"
                        :key="item.label"
                        class="rounded-lg px-3 py-2 text-left text-sm font-semibold text-slate-600 transition hover:bg-[#edf3ff] hover:text-[#3E7BEF]"
                        @click="selectNavigation(item)"
                    >
                        {{ item.label }}
                    </component>
                </div>
                <div class="mt-3 flex items-center justify-between border-t border-slate-100 pt-3">
                    <span class="text-xs font-medium text-slate-500">Bahasa & Mata Uang</span>
                    <div class="flex items-center gap-1.5">
                        <button
                            v-for="loc in locales"
                            :key="loc.code"
                            type="button"
                            class="flex items-center gap-1.5 rounded-lg border px-2.5 py-1 text-xs font-semibold transition"
                            :class="selectedLocale.code === loc.code ? 'border-[#3E7BEF] bg-[#edf3ff] text-[#3E7BEF]' : 'border-slate-200 text-slate-600 hover:bg-slate-50'"
                            @click="selectLocale(loc); isMobileMenuOpen = false"
                        >
                            <FlagIcon :country="loc.flag" custom-class="h-3 w-4" />
                            <span>{{ loc.short }}</span>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Toast Notification -->
            <Transition enter-active-class="transition duration-200" enter-from-class="translate-y-2 opacity-0" leave-active-class="transition duration-150" leave-to-class="translate-y-2 opacity-0">
                <div v-if="notification" class="fixed bottom-5 right-5 z-50 max-w-sm rounded-xl border border-[#3E7BEF]/20 bg-white px-4 py-3 text-sm font-semibold text-slate-700 shadow-xl">
                    <span class="mr-2 inline-grid size-5 place-items-center rounded-full bg-[#3E7BEF] text-xs text-white">✓</span>
                    {{ notification }}
                </div>
            </Transition>
        </header>
    </div>
</template>
