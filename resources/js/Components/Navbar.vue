<script setup>
import { ref } from 'vue';
import { Link, router } from '@inertiajs/vue3';
import {
    Gift,
    Building2,
    CircleHelp,
    Users,
    Search,
    ChevronDown,
    Menu,
    X,
    Compass,
    Sparkles,
    Briefcase
} from 'lucide-vue-next';

const props = defineProps({
    user: {
        type: Object,
        default: () => ({
            name: 'albnnaardy',
            handle: '@petualangnyasar',
            points: 0,
        }),
    },
    activeMenu: {
        type: String,
        default: 'Beranda',
    },
});

const emit = defineEmits(['navigate', 'open-solit-bill', 'search']);

const isMobileMenuOpen = ref(false);
const searchQuery = ref('');
const isCurrencyDropdownOpen = ref(false);
const isBisnisDropdownOpen = ref(false);

const navLinks = [
    { name: 'Beranda', href: '/', isRoute: true },
    { name: 'Open Trip', href: '/open-trip', isRoute: true },
    { name: 'Open Po Kuliner', href: '/open-po', isRoute: true },
    { name: 'Mitra Akamsi', href: '/gabung-mitra', isRoute: true },
    { name: 'Gabung Mitra', href: '/gabung-mitra', isRoute: true, highlight: true },
    { name: 'Solit Bill', href: '#', isAction: 'solit-bill' },
    { name: 'Wishlist', href: '/wishlist', isRoute: true },
    { name: 'Blog', href: '/blog', isRoute: true },
    { name: 'Member Tiers', href: '/member-tiers', isRoute: true },
];

function handleNavClick(link) {
    if (link.isAction === 'solit-bill') {
        emit('open-solit-bill');
    } else if (link.isRoute) {
        router.visit(link.href);
    } else {
        emit('navigate', link.name);
    }
    isMobileMenuOpen.value = false;
}


function handleSearch() {
    emit('search', searchQuery.value);
}
</script>


<template>
    <header class="w-full bg-white border-b border-gray-100 sticky top-0 z-40 shadow-xs">
        <!-- Top Utility & Profile Bar -->
        <div class="max-w-7xl mx-auto px-3 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-15 sm:h-16 md:h-18">
                <!-- Brand Logo -->
                <div class="flex items-center gap-2 sm:gap-3">
                    <a href="/" class="flex items-center gap-2 sm:gap-2.5 group">
                        <!-- Icon Pin/Compass Logo -->
                        <div class="w-8 h-8 sm:w-9 sm:h-9 rounded-xl bg-gradient-to-tr from-[#0047ba] to-[#0066ff] flex items-center justify-center text-white shadow-sm group-hover:shadow transition">
                            <Compass class="w-4 h-4 sm:w-5 sm:h-5 transition-transform group-hover:rotate-45" />
                        </div>
                        <span class="tracking-tight text-xl sm:text-2xl font-black">
                            <span class="text-slate-900 font-extrabold">TAPAK</span>
                            <span class="text-[#0052cc] font-black">LOKAL</span>
                        </span>
                    </a>
                </div>

                <!-- Right Utility Menus & Profile (Desktop & Tablet Landscape) -->
                <div class="hidden md:flex items-center gap-4 lg:gap-6">
                    <!-- Currency / Locale Selector -->
                    <div class="relative">
                        <button
                            type="button"
                            @click="isCurrencyDropdownOpen = !isCurrencyDropdownOpen"
                            class="flex items-center gap-1.5 text-xs lg:text-sm font-semibold text-slate-700 hover:text-[#0052cc] transition cursor-pointer"
                        >
                            <!-- Indonesian Flag Badge -->
                            <span class="inline-flex flex-col w-4.5 h-3 sm:w-5 sm:h-3.5 rounded-xs overflow-hidden border border-gray-300 shadow-2xs">
                                <span class="w-full h-1/2 bg-[#FF0000]"></span>
                                <span class="w-full h-1/2 bg-white"></span>
                            </span>
                            <span>IDR | ID</span>
                            <ChevronDown class="w-3.5 h-3.5 text-slate-400" />
                        </button>

                        <div
                            v-if="isCurrencyDropdownOpen"
                            class="absolute right-0 mt-2 w-44 bg-white rounded-xl shadow-lg border border-gray-100 py-1.5 z-50 text-xs font-medium text-slate-700"
                        >
                            <div class="px-3 py-1.5 text-slate-400 font-semibold uppercase tracking-wider text-[10px]">Mata Uang</div>
                            <button @click="isCurrencyDropdownOpen = false" class="w-full text-left px-3 py-1.5 flex items-center justify-between bg-blue-50/60 text-[#0052cc] font-bold">
                                <span>🇮🇩 Indonesian Rupiah</span>
                                <span>IDR</span>
                            </button>
                            <button @click="isCurrencyDropdownOpen = false" class="w-full text-left px-3 py-1.5 flex items-center justify-between hover:bg-gray-50 text-slate-600">
                                <span>🇺🇸 US Dollar</span>
                                <span>USD</span>
                            </button>
                        </div>
                    </div>

                    <!-- Promo Link -->
                    <a href="#promo" class="flex items-center gap-1.5 text-xs lg:text-sm font-semibold text-slate-700 hover:text-[#0052cc] transition">
                        <Gift class="w-4 h-4 text-[#0052cc]" />
                        <span>Promo</span>
                    </a>

                    <!-- Bisnis Dropdown -->
                    <div class="relative">
                        <button
                            type="button"
                            @click="isBisnisDropdownOpen = !isBisnisDropdownOpen"
                            class="flex items-center gap-1.5 text-xs lg:text-sm font-semibold text-slate-700 hover:text-[#0052cc] transition cursor-pointer"
                        >
                            <Building2 class="w-4 h-4 text-slate-600" />
                            <span>Bisnis</span>
                            <ChevronDown class="w-3.5 h-3.5 text-slate-400" />
                        </button>
                        <div
                            v-if="isBisnisDropdownOpen"
                            class="absolute right-0 mt-2 w-56 bg-white rounded-xl shadow-xl border border-gray-100 py-2 z-50 text-xs text-slate-700"
                        >
                            <Link
                                href="/gabung-mitra"
                                @click="isBisnisDropdownOpen = false"
                                class="px-4 py-2.5 bg-blue-50/60 hover:bg-blue-100/70 text-[#0052cc] font-bold flex items-center justify-between transition"
                            >
                                <span>Daftar Jadi Mitra Vendor</span>
                                <span class="bg-[#0052cc] text-white text-[9px] px-1.5 py-0.5 rounded-md font-bold">Baru</span>
                            </Link>
                            <div class="h-[1px] bg-gray-100 my-1"></div>
                            <Link href="/gabung-mitra" @click="isBisnisDropdownOpen = false" class="block px-4 py-2 hover:bg-gray-50 hover:text-[#0052cc] font-medium">Mitra Pemandu Akamsi</Link>
                            <Link href="/gabung-mitra" @click="isBisnisDropdownOpen = false" class="block px-4 py-2 hover:bg-gray-50 hover:text-[#0052cc] font-medium">Merchant PO Kuliner & Oleh-Oleh</Link>
                            <Link href="/gabung-mitra" @click="isBisnisDropdownOpen = false" class="block px-4 py-2 hover:bg-gray-50 hover:text-[#0052cc] font-medium">Armada Jeep & Homestay</Link>
                        </div>
                    </div>


                    <!-- Bantuan Link (Hidden on tight tablet, visible on lg) -->
                    <a href="#bantuan" class="hidden lg:flex items-center gap-1.5 text-xs lg:text-sm font-semibold text-slate-700 hover:text-[#0052cc] transition">
                        <CircleHelp class="w-4 h-4 text-slate-600" />
                        <span>Bantuan</span>
                    </a>

                    <!-- Komunitas Link (Hidden on tight tablet, visible on lg) -->
                    <a href="#komunitas" class="hidden lg:flex items-center gap-1.5 text-xs lg:text-sm font-semibold text-slate-700 hover:text-[#0052cc] transition">
                        <Users class="w-4 h-4 text-slate-600" />
                        <span>Komunitas</span>
                    </a>

                    <!-- User Profile Badge (Exact design from screenshot) -->
                    <div class="flex items-center gap-2.5 lg:gap-3 pl-2.5 lg:pl-3 border-l border-gray-200">
                        <div class="flex items-center gap-2 lg:gap-2.5">
                            <!-- Avatar Circle with Blue Border -->
                            <div class="w-8 h-8 lg:w-9 lg:h-9 rounded-full bg-blue-100 border-2 border-[#0052cc] flex items-center justify-center text-[#0052cc] font-bold text-xs lg:text-sm shadow-2xs">
                                <span class="uppercase">{{ user.name ? user.name.charAt(0) : 'A' }}</span>
                            </div>
                            <!-- Name & Handle -->
                            <div class="text-left leading-tight">
                                <div class="font-bold text-xs lg:text-sm text-slate-900 flex items-center gap-1">
                                    {{ user.name }}
                                </div>
                                <div class="text-[10px] lg:text-[11px] text-slate-500 font-normal">
                                    {{ user.handle }}
                                </div>
                            </div>
                        </div>

                        <!-- Divider line -->
                        <div class="h-6 lg:h-7 w-[1px] bg-gray-300"></div>

                        <!-- Points Badge -->
                        <div class="text-center min-w-[32px] lg:min-w-[38px] leading-tight">
                            <span class="block font-black text-xs lg:text-sm text-[#0052cc]">{{ user.points }}</span>
                            <span class="block text-[9px] lg:text-[10px] text-slate-500 font-medium -mt-0.5">point</span>
                        </div>
                    </div>
                </div>

                <!-- Mobile Header Right (Phone & Tablet Portrait) -->
                <div class="flex items-center gap-2 md:hidden">
                    <div class="flex items-center gap-1.5 pr-2 border-r border-gray-200">
                        <div class="w-7 h-7 rounded-full bg-blue-100 border border-[#0052cc] flex items-center justify-center text-[#0052cc] font-bold text-xs">
                            {{ user.name ? user.name.charAt(0).toUpperCase() : 'A' }}
                        </div>
                        <div class="text-xs font-bold text-[#0052cc]">{{ user.points }} pt</div>
                    </div>
                    <button
                        type="button"
                        @click="isMobileMenuOpen = !isMobileMenuOpen"
                        aria-label="Toggle Menu"
                        class="p-2 rounded-lg text-slate-700 hover:bg-gray-100 focus:outline-none cursor-pointer"
                    >
                        <Menu v-if="!isMobileMenuOpen" class="w-6 h-6" />
                        <X v-else class="w-6 h-6" />
                    </button>
                </div>
            </div>
        </div>

        <!-- Secondary Navigation Bar (Deep Brand Blue: #0052cc / #0047ba) -->
        <nav class="bg-[#0052cc] text-white shadow-inner">
            <div class="max-w-7xl mx-auto px-3 sm:px-6 lg:px-8">
                <div class="flex flex-col md:flex-row items-stretch md:items-center justify-between py-1.5 md:py-0 md:h-13 gap-2 md:gap-4">
                    <!-- Nav Items (Scrollable horizontally on mobile & tablet) -->
                    <div class="flex items-center space-x-1 lg:space-x-2 overflow-x-auto py-1 scrollbar-none -mx-1 px-1">
                        <button
                            v-for="link in navLinks"
                            :key="link.name"
                            type="button"
                            @click="handleNavClick(link)"
                            :class="[
                                'px-2.5 sm:px-3 py-1.5 rounded-md text-xs sm:text-sm font-medium transition whitespace-nowrap shrink-0 cursor-pointer flex items-center gap-1.5',
                                activeMenu === link.name
                                    ? 'bg-white/20 text-white font-bold shadow-2xs'
                                    : link.highlight
                                        ? 'bg-amber-400 text-slate-900 font-extrabold hover:bg-amber-300 shadow-xs'
                                        : 'text-white/90 hover:bg-white/10 hover:text-white'
                            ]"
                        >
                            <span>{{ link.name }}</span>
                            <span
                                v-if="link.highlight && activeMenu !== link.name"
                                class="text-[9px] px-1.5 py-0.5 bg-slate-900 text-amber-300 font-black rounded-full tracking-wider uppercase"
                            >
                                Mitra
                            </span>
                        </button>
                    </div>


                    <!-- Search Input (Right side, responsive width) -->
                    <div class="w-full md:w-64 lg:w-80 shrink-0 pb-1 md:pb-0">
                        <div class="relative">
                            <input
                                v-model="searchQuery"
                                @keyup.enter="handleSearch"
                                type="text"
                                placeholder="Search ..."
                                class="w-full bg-white text-slate-800 placeholder-slate-400 text-xs sm:text-sm pl-4 pr-9 py-1.5 sm:py-2 rounded-full border-none focus:outline-none focus:ring-2 focus:ring-blue-300 shadow-inner"
                            />
                            <button
                                type="button"
                                @click="handleSearch"
                                aria-label="Search"
                                class="absolute right-2.5 top-1/2 -translate-y-1/2 text-slate-400 hover:text-[#0052cc] transition cursor-pointer"
                            >
                                <Search class="w-4 h-4" />
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </nav>

        <!-- Mobile Drawer Menu (Slide Down on Phone) -->
        <div v-if="isMobileMenuOpen" class="md:hidden bg-white border-b border-gray-200 px-4 pt-3 pb-6 space-y-4 shadow-xl animate-fade-in">
            <!-- User summary in drawer -->
            <div class="flex items-center justify-between p-3 bg-blue-50/70 rounded-2xl border border-blue-100">
                <div class="flex items-center gap-2.5">
                    <div class="w-10 h-10 rounded-full bg-[#0052cc] text-white flex items-center justify-center font-bold text-sm">
                        {{ user.name ? user.name.charAt(0).toUpperCase() : 'A' }}
                    </div>
                    <div>
                        <div class="font-bold text-sm text-slate-900">{{ user.name }}</div>
                        <div class="text-xs text-slate-500">{{ user.handle }}</div>
                    </div>
                </div>
                <div class="text-right">
                    <span class="text-xs text-slate-500 block">Saldo Poin</span>
                    <span class="text-base font-black text-[#0052cc]">{{ user.points }} Poin</span>
                </div>
            </div>

            <!-- Quick utilities in mobile drawer -->
            <div class="grid grid-cols-2 gap-2 pb-2 border-b border-gray-100">
                <a
                    href="#promo"
                    @click="isMobileMenuOpen = false"
                    class="flex items-center gap-2 p-2.5 rounded-xl bg-gray-50 hover:bg-blue-50 text-xs font-semibold text-slate-700 transition"
                >
                    <Gift class="w-4 h-4 text-[#0052cc]" />
                    <span>Promo Spesial</span>
                </a>
                <a
                    href="#bantuan"
                    @click="isMobileMenuOpen = false"
                    class="flex items-center gap-2 p-2.5 rounded-xl bg-gray-50 hover:bg-blue-50 text-xs font-semibold text-slate-700 transition"
                >
                    <CircleHelp class="w-4 h-4 text-slate-600" />
                    <span>Pusat Bantuan</span>
                </a>
                <a
                    href="#mitra-akamsi"
                    @click="isMobileMenuOpen = false"
                    class="flex items-center gap-2 p-2.5 rounded-xl bg-gray-50 hover:bg-blue-50 text-xs font-semibold text-slate-700 transition"
                >
                    <Building2 class="w-4 h-4 text-slate-600" />
                    <span>Mitra Bisnis</span>
                </a>
                <a
                    href="#komunitas"
                    @click="isMobileMenuOpen = false"
                    class="flex items-center gap-2 p-2.5 rounded-xl bg-gray-50 hover:bg-blue-50 text-xs font-semibold text-slate-700 transition"
                >
                    <Users class="w-4 h-4 text-slate-600" />
                    <span>Komunitas</span>
                </a>
            </div>

            <!-- Navigation Links in Drawer -->
            <div class="space-y-1">
                <button
                    v-for="link in navLinks"
                    :key="link.name"
                    type="button"
                    @click="handleNavClick(link)"
                    :class="[
                        'block w-full text-left px-3 py-2 rounded-xl text-sm font-medium transition cursor-pointer flex items-center justify-between',
                        activeMenu === link.name
                            ? 'bg-blue-50 text-[#0052cc] font-bold'
                            : 'text-slate-700 hover:bg-gray-50'
                    ]"
                >
                    <span>{{ link.name }}</span>
                    <span
                        v-if="link.highlight"
                        class="text-[10px] px-2 py-0.5 bg-amber-400 text-slate-950 font-black rounded-full"
                    >
                        Mitra
                    </span>
                </button>
            </div>

            <!-- Partner Recruitment Card in Mobile Drawer -->
            <div class="pt-2">
                <div class="p-3.5 bg-gradient-to-r from-blue-50 to-indigo-50 rounded-2xl border border-blue-200">
                    <div class="flex items-center justify-between mb-1">
                        <span class="text-xs font-bold text-slate-900">Punya Paket Trip / Kuliner?</span>
                        <span class="bg-[#0052cc] text-white text-[9px] font-bold px-1.5 py-0.5 rounded-full">0% Biaya</span>
                    </div>
                    <p class="text-[11px] text-slate-600 mb-3">Raih jutaan rupiah dari ribuan petualang TapakLokal.</p>
                    <Link
                        href="/gabung-mitra"
                        @click="isMobileMenuOpen = false"
                        class="block w-full text-center py-2 bg-[#0052cc] hover:bg-[#0042a3] text-white rounded-xl text-xs font-bold transition shadow-xs"
                    >
                        Daftar Jadi Mitra Sekarang
                    </Link>
                </div>
            </div>
        </div>
    </header>
</template>

