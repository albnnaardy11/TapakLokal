<script setup>
import { Bell, ChevronDown, CircleHelp, Menu, Search, ShoppingBag, UsersRound, X } from 'lucide-vue-next';
import { ref } from 'vue';
import AuthModal from './AuthModal.vue';

const isMobileMenuOpen = ref(false);
const openPopover = ref(null);
const activeNav = ref('Beranda');
const globalSearch = ref('');
const notification = ref('');
const isAuthModalOpen = ref(false);
const authMode = ref('login');
const navItems = [
    { label: 'Beranda', description: 'Kembali ke halaman utama' },
    { label: 'Cari Trip', description: 'Open trip dan private trip pilihan' },
    { label: 'Destinasi', description: 'Temukan inspirasi perjalanan di Indonesia' },
    { label: 'Kuliner Lokal', description: 'Produk lokal pilihan dari berbagai daerah' },
    { label: 'Promo', description: 'Penawaran perjalanan dan produk pilihan' },
    { label: 'Cerita Perjalanan', description: 'Inspirasi dan panduan untuk perjalananmu' },
];

const notify = (message) => {
    notification.value = message;
    window.setTimeout(() => (notification.value = ''), 2800);
};

const selectNavigation = (item) => {
    activeNav.value = item.label;
    isMobileMenuOpen.value = false;
    openPopover.value = null;
    notify(`${item.label} dipilih — ${item.description}`);
};

const submitGlobalSearch = () => notify(globalSearch.value.trim() ? `Mencari “${globalSearch.value.trim()}”...` : 'Masukkan kata kunci untuk mulai mencari.');
const openAuthModal = (mode) => { authMode.value = mode; isAuthModalOpen.value = true; };
</script>

<template>
    <div class="h-[58px] lg:h-[102px]">
    <header class="fixed inset-x-0 top-0 z-30 bg-white shadow-[0_1px_0_rgba(15,44,92,0.08)]">
        <div class="mx-auto flex h-[58px] max-w-[1180px] items-center px-5 sm:px-8">
            <button class="shrink-0 text-left" aria-label="TapakLokal beranda" @click="selectNavigation(navItems[0])"><span class="text-[15px] font-extrabold tracking-[-0.07em] text-slate-900">tapa<span class="text-[#3E7BEF]">k</span>lokal</span></button>

            <div class="ml-auto hidden items-center gap-1 text-[11px] font-semibold text-slate-700 xl:flex">
                <div class="relative"><button class="flex h-8 items-center gap-1 rounded-lg px-2 transition hover:bg-[#edf3ff]" @click="openPopover = openPopover === 'locale' ? null : 'locale'"><span class="text-sm">🇮🇩</span><span>IDR | ID</span><ChevronDown class="size-3 text-[#3E7BEF]" /></button><div v-if="openPopover === 'locale'" class="absolute right-0 top-full z-50 mt-2 w-48 rounded-xl border border-slate-100 bg-white p-1.5 shadow-[0_12px_30px_rgba(15,44,92,0.16)]"><button class="w-full rounded-lg px-3 py-2.5 text-left hover:bg-[#edf3ff]" @click="notify('Bahasa Indonesia dipilih'); openPopover = null">🇮🇩 Indonesia · IDR</button><button class="w-full rounded-lg px-3 py-2.5 text-left hover:bg-[#edf3ff]" @click="notify('English selected'); openPopover = null">🇬🇧 English · USD</button></div></div>
                <span class="mx-1 h-5 w-px bg-slate-200"></span>
                <button class="flex h-8 items-center gap-1 rounded-lg px-2 transition hover:bg-[#edf3ff]" @click="notify('Promo terbaru sedang disiapkan untukmu.')"><Bell class="size-3.5 text-[#3E7BEF]" />Promo</button>
                <button class="flex h-8 items-center gap-1 rounded-lg px-2 transition hover:bg-[#edf3ff]" @click="notify('Menu Bisnis dibuka.')"><ShoppingBag class="size-3.5 text-[#3E7BEF]" />Bisnis<ChevronDown class="size-3 text-[#3E7BEF]" /></button>
                <button class="flex h-8 items-center gap-1 rounded-lg px-2 transition hover:bg-[#edf3ff]" @click="notify('Pusat bantuan siap membantu.')"><CircleHelp class="size-3.5 text-[#3E7BEF]" />Bantuan</button>
                <button class="flex h-8 items-center gap-1 rounded-lg px-2 transition hover:bg-[#edf3ff]" @click="notify('Komunitas TapakLokal dipilih.')"><UsersRound class="size-3.5 text-[#3E7BEF]" />Komunitas</button>
                <span class="mx-1 h-5 w-px bg-slate-200"></span>
                <button type="button" class="ml-2 rounded-lg px-3 py-2 text-xs font-bold text-[#3E7BEF] hover:bg-[#edf3ff]" @click="openAuthModal('login')">Masuk</button><button type="button" class="rounded-lg bg-[#3E7BEF] px-3 py-2 text-xs font-bold text-white shadow-sm hover:bg-[#2e69d9]" @click="openAuthModal('register')">Daftar</button>
            </div>
            <button type="button" class="ml-auto mr-2 min-h-9 rounded-lg px-2.5 text-xs font-bold text-[#3E7BEF] xl:hidden" @click="openAuthModal('login')">Masuk</button><button class="rounded-lg p-2 text-[#3E7BEF] hover:bg-[#edf3ff] xl:hidden" :aria-expanded="isMobileMenuOpen" aria-label="Buka menu" @click="isMobileMenuOpen = !isMobileMenuOpen"><X v-if="isMobileMenuOpen" class="size-5" /><Menu v-else class="size-5" /></button>
        </div>

        <nav class="hidden border-t border-slate-100 bg-[#3E7BEF] lg:block"><div class="mx-auto flex h-11 max-w-[1180px] items-center px-5 sm:px-8"><div class="flex h-full items-center gap-1"><button v-for="item in navItems" :key="item.label" class="relative flex h-8 items-center rounded-lg px-3 text-sm font-semibold transition-colors duration-150 hover:bg-white/10" :class="activeNav === item.label ? 'bg-white/12 text-white' : 'text-white/80 hover:text-white'" @click="selectNavigation(item)">{{ item.label }}<span v-if="activeNav === item.label" class="absolute inset-x-3 -bottom-1.5 h-0.5 rounded-full bg-white"></span></button></div><form class="ml-auto w-52" @submit.prevent="submitGlobalSearch"><label class="flex h-7 items-center gap-1.5 rounded-full bg-white px-3 text-slate-400 shadow-sm"><input v-model="globalSearch" class="min-w-0 flex-1 bg-transparent text-xs outline-none placeholder:text-slate-400" placeholder="Cari destinasi atau trip" /><Search class="size-3.5 text-[#3E7BEF]" /></label></form></div></nav>

        <div v-if="isMobileMenuOpen" class="border-t border-slate-100 bg-white px-5 py-4 shadow-[0_12px_20px_rgba(15,44,92,0.08)] lg:hidden"><form class="mb-3" @submit.prevent="submitGlobalSearch"><label class="flex h-10 items-center gap-2 rounded-xl border border-slate-200 bg-slate-50 px-3 text-slate-400"><Search class="size-4 text-[#3E7BEF]" /><input v-model="globalSearch" class="min-w-0 flex-1 bg-transparent text-sm text-slate-700 outline-none" placeholder="Cari destinasi..." /></label></form><div class="grid grid-cols-2 gap-1.5"><button v-for="item in navItems" :key="item.label" class="rounded-lg px-3 py-2 text-left text-sm font-semibold text-slate-600 transition hover:bg-[#edf3ff] hover:text-[#3E7BEF]" @click="selectNavigation(item)">{{ item.label }}</button></div></div>
        <Transition enter-active-class="transition duration-200" enter-from-class="translate-y-2 opacity-0" leave-active-class="transition duration-150" leave-to-class="translate-y-2 opacity-0"><div v-if="notification" class="fixed bottom-5 right-5 z-50 max-w-sm rounded-xl border border-[#3E7BEF]/20 bg-white px-4 py-3 text-sm font-semibold text-slate-700 shadow-xl"><span class="mr-2 inline-grid size-5 place-items-center rounded-full bg-[#3E7BEF] text-xs text-white">✓</span>{{ notification }}</div></Transition>
        <AuthModal :open="isAuthModalOpen" :mode="authMode" @close="isAuthModalOpen = false" />
    </header>
    </div>
</template>
