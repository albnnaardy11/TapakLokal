<script setup>
import { Link } from '@inertiajs/vue3';
import { route } from 'ziggy-js';
import {
    Landmark,
    Smartphone,
    Store,
    Wallet,
    CheckCircle2,
    HelpCircle,
    ChevronRight,
} from 'lucide-vue-next';

const props = defineProps({
    eyebrow: {
        type: String,
        default: 'SISTEM PEMBAYARAN RESMI',
    },
    titlePrefix: {
        type: String,
        default: 'Popular',
    },
    titleAccent: {
        type: String,
        default: 'Payments',
    },
    description: {
        type: String,
        default: 'Pilihan metode pembayaran lengkap dan terverifikasi untuk memastikan setiap transaksi berjalan cepat, aman, dan tanpa biaya tersembunyi. Klik logo untuk panduan pembayaran.',
    },
});

const safeRoute = (name, params) => {
    try {
        if (typeof route === 'function') {
            return params !== undefined ? route(name, params) : route(name);
        }
        return '#';
    } catch {
        return '#';
    }
};
</script>

<template>
    <section class="mx-auto max-w-[1180px]" aria-labelledby="popular-payments-heading">
        <!-- Header -->
        <div class="px-1 flex flex-col sm:flex-row sm:items-end sm:justify-between gap-3">
            <div>
                <p class="text-xs font-bold uppercase tracking-[0.16em] text-[#0088ff]">
                    {{ eyebrow }}
                </p>
                <h2
                    id="popular-payments-heading"
                    class="mt-1 text-xl sm:text-2xl lg:text-[26px] font-extrabold leading-tight tracking-tight text-slate-900"
                >
                    {{ titlePrefix }} <span class="text-[#0088ff]">{{ titleAccent }}</span>
                </h2>
                <p class="mt-1.5 text-xs sm:text-sm font-medium text-slate-500 max-w-2xl leading-relaxed">
                    {{ description }}
                </p>
            </div>

            <Link
                :href="safeRoute('help.category', { category: 'payment-methods' })"
                class="text-xs font-bold text-[#0088ff] hover:underline shrink-0 inline-flex items-center gap-1"
            >
                <span>Lihat Semua Metode Bayar</span>
                <ChevronRight class="size-3.5" />
            </Link>
        </div>

        <!-- 4 Categorized Payment Cards (Fills Full 1180px Width Cleanly) -->
        <div class="mt-7 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-5 px-1">
            <!-- 1. Transfer Bank & Virtual Account -->
            <div class="flex flex-col justify-between rounded-2xl border border-slate-200/90 bg-white p-5 shadow-2xs hover:border-[#0088ff]/40 hover:shadow-xs transition-all duration-200">
                <div>
                    <!-- Header with Icon & Category Name -->
                    <div class="flex items-center justify-between pb-3.5 border-b border-slate-100">
                        <div class="flex items-center gap-2.5">
                            <div class="grid size-8 place-items-center rounded-lg bg-sky-50 text-[#0088ff]">
                                <Landmark class="size-4" />
                            </div>
                            <div>
                                <h3 class="text-xs sm:text-[13px] font-bold text-slate-900 leading-snug">
                                    Transfer Bank & VA
                                </h3>
                                <p class="text-[11px] text-slate-500">Virtual Account Resmi</p>
                            </div>
                        </div>

                        <Link
                            :href="safeRoute('help.category', { category: 'payment-methods' })"
                            class="text-[11px] font-semibold text-slate-400 hover:text-[#0088ff] transition-colors"
                            title="Panduan Pembayaran Bank"
                        >
                            <HelpCircle class="size-4" />
                        </Link>
                    </div>

                    <!-- Payment Logos Area with Clickable Links -->
                    <div class="flex items-center justify-around gap-2 py-5 min-h-[76px]">
                        <!-- BCA -->
                        <Link
                            :href="safeRoute('help.article', { category: 'payment-methods', slug: 'cara-bayar-bca-virtual-account' })"
                            class="group/item flex h-11 w-24 sm:w-28 items-center justify-center rounded-xl p-1.5 hover:bg-slate-50 transition-all cursor-pointer"
                            title="Cara Bayar via BCA Virtual Account"
                        >
                            <img
                                src="/Assets/Images/logo-bank/bca.webp"
                                alt="Bank BCA"
                                class="max-h-7 sm:max-h-8 w-auto max-w-[95px] object-contain transition-transform duration-200 group-hover/item:scale-108"
                                loading="lazy"
                            />
                        </Link>

                        <!-- Mandiri -->
                        <Link
                            :href="safeRoute('help.article', { category: 'payment-methods', slug: 'cara-bayar-mandiri-virtual-account' })"
                            class="group/item flex h-11 w-24 sm:w-28 items-center justify-center rounded-xl p-1.5 hover:bg-slate-50 transition-all cursor-pointer"
                            title="Cara Bayar via Mandiri Virtual Account"
                        >
                            <img
                                src="/Assets/Images/logo-bank/mandiri.webp"
                                alt="Bank Mandiri"
                                class="max-h-7 sm:max-h-8 w-auto max-w-[100px] object-contain transition-transform duration-200 group-hover/item:scale-108"
                                loading="lazy"
                            />
                        </Link>
                    </div>
                </div>

                <!-- Footer Status -->
                <div class="pt-3 border-t border-slate-100 flex items-center justify-between text-[11px]">
                    <span class="inline-flex items-center gap-1.5 font-semibold text-slate-600">
                        <CheckCircle2 class="size-3.5 text-[#0088ff]" />
                        Verifikasi 24/7
                    </span>
                    <span class="text-slate-400 font-medium">BCA & Mandiri</span>
                </div>
            </div>

            <!-- 2. Uang Elektronik (E-Wallet) -->
            <div class="flex flex-col justify-between rounded-2xl border border-slate-200/90 bg-white p-5 shadow-2xs hover:border-[#0088ff]/40 hover:shadow-xs transition-all duration-200">
                <div>
                    <!-- Header with Icon & Category Name -->
                    <div class="flex items-center justify-between pb-3.5 border-b border-slate-100">
                        <div class="flex items-center gap-2.5">
                            <div class="grid size-8 place-items-center rounded-lg bg-sky-50 text-[#0088ff]">
                                <Smartphone class="size-4" />
                            </div>
                            <div>
                                <h3 class="text-xs sm:text-[13px] font-bold text-slate-900 leading-snug">
                                    Uang Elektronik
                                </h3>
                                <p class="text-[11px] text-slate-500">E-Wallet Indonesia</p>
                            </div>
                        </div>

                        <Link
                            :href="safeRoute('help.category', { category: 'payment-methods' })"
                            class="text-[11px] font-semibold text-slate-400 hover:text-[#0088ff] transition-colors"
                            title="Panduan Pembayaran E-Wallet"
                        >
                            <HelpCircle class="size-4" />
                        </Link>
                    </div>

                    <!-- Payment Logos Area with Clickable Links -->
                    <div class="flex items-center justify-around gap-2 py-5 min-h-[76px]">
                        <!-- GoPay -->
                        <Link
                            :href="safeRoute('help.article', { category: 'payment-methods', slug: 'cara-bayar-tapaklokal-lewat-gopay' })"
                            class="group/item flex h-11 w-24 sm:w-28 items-center justify-center rounded-xl p-1.5 hover:bg-slate-50 transition-all cursor-pointer"
                            title="Cara Bayar via GoPay / GoPay Later"
                        >
                            <img
                                src="/Assets/Images/logo-bank/gopay.webp"
                                alt="GoPay"
                                class="max-h-6 sm:max-h-7 w-auto max-w-[100px] object-contain transition-transform duration-200 group-hover/item:scale-108"
                                loading="lazy"
                            />
                        </Link>

                        <!-- OVO -->
                        <Link
                            :href="safeRoute('help.article', { category: 'payment-methods', slug: 'cara-bayar-tapaklokal-lewat-ovo' })"
                            class="group/item flex h-11 w-24 sm:w-28 items-center justify-center rounded-xl p-1.5 hover:bg-slate-50 transition-all cursor-pointer"
                            title="Cara Bayar via OVO"
                        >
                            <img
                                src="/Assets/Images/logo-bank/ovo.webp"
                                alt="OVO"
                                class="max-h-9 sm:max-h-10 w-auto max-w-[48px] object-contain transition-transform duration-200 group-hover/item:scale-108"
                                loading="lazy"
                            />
                        </Link>
                    </div>
                </div>

                <!-- Footer Status -->
                <div class="pt-3 border-t border-slate-100 flex items-center justify-between text-[11px]">
                    <span class="inline-flex items-center gap-1.5 font-semibold text-slate-600">
                        <CheckCircle2 class="size-3.5 text-[#0088ff]" />
                        Konfirmasi Instan
                    </span>
                    <span class="text-slate-400 font-medium">GoPay & OVO</span>
                </div>
            </div>

            <!-- 3. Gerai Retail -->
            <div class="flex flex-col justify-between rounded-2xl border border-slate-200/90 bg-white p-5 shadow-2xs hover:border-[#0088ff]/40 hover:shadow-xs transition-all duration-200">
                <div>
                    <!-- Header with Icon & Category Name -->
                    <div class="flex items-center justify-between pb-3.5 border-b border-slate-100">
                        <div class="flex items-center gap-2.5">
                            <div class="grid size-8 place-items-center rounded-lg bg-sky-50 text-[#0088ff]">
                                <Store class="size-4" />
                            </div>
                            <div>
                                <h3 class="text-xs sm:text-[13px] font-bold text-slate-900 leading-snug">
                                    Gerai Retail
                                </h3>
                                <p class="text-[11px] text-slate-500">Minimarket Terdekat</p>
                            </div>
                        </div>

                        <Link
                            :href="safeRoute('help.category', { category: 'payment-methods' })"
                            class="text-[11px] font-semibold text-slate-400 hover:text-[#0088ff] transition-colors"
                            title="Panduan Pembayaran Minimarket"
                        >
                            <HelpCircle class="size-4" />
                        </Link>
                    </div>

                    <!-- Payment Logos Area with Clickable Links -->
                    <div class="flex items-center justify-around gap-2 py-5 min-h-[76px]">
                        <!-- Indomaret -->
                        <Link
                            :href="safeRoute('help.article', { category: 'payment-methods', slug: 'cara-bayar-tapaklokal-lewat-indomaret' })"
                            class="group/item flex h-11 w-24 sm:w-28 items-center justify-center rounded-xl p-1.5 hover:bg-slate-50 transition-all cursor-pointer"
                            title="Cara Bayar via Gerai Indomaret"
                        >
                            <img
                                src="/Assets/Images/logo-bank/indomaret.webp"
                                alt="Indomaret"
                                class="max-h-7 sm:max-h-8 w-auto max-w-[95px] object-contain transition-transform duration-200 group-hover/item:scale-108"
                                loading="lazy"
                            />
                        </Link>

                        <!-- Alfamart -->
                        <Link
                            :href="safeRoute('help.article', { category: 'payment-methods', slug: 'cara-bayar-tapaklokal-lewat-alfamart' })"
                            class="group/item flex h-11 w-24 sm:w-28 items-center justify-center rounded-xl p-1.5 hover:bg-slate-50 transition-all cursor-pointer"
                            title="Cara Bayar via Gerai Alfamart"
                        >
                            <img
                                src="/Assets/Images/logo-bank/alfa.webp"
                                alt="Alfamart"
                                class="max-h-7 sm:max-h-8 w-auto max-w-[95px] object-contain transition-transform duration-200 group-hover/item:scale-108"
                                loading="lazy"
                            />
                        </Link>
                    </div>
                </div>

                <!-- Footer Status -->
                <div class="pt-3 border-t border-slate-100 flex items-center justify-between text-[11px]">
                    <span class="inline-flex items-center gap-1.5 font-semibold text-slate-600">
                        <CheckCircle2 class="size-3.5 text-[#0088ff]" />
                        Bayar di Kasir
                    </span>
                    <span class="text-slate-400 font-medium">Tanpa Kartu</span>
                </div>
            </div>

            <!-- 4. Dompet Digital Resmi (Tapak Lokal Wallet) -->
            <div class="flex flex-col justify-between rounded-2xl border border-slate-200/90 bg-white p-5 shadow-2xs hover:border-[#0088ff]/40 hover:shadow-xs transition-all duration-200">
                <div>
                    <!-- Header with Icon & Category Name -->
                    <div class="flex items-center justify-between pb-3.5 border-b border-slate-100">
                        <div class="flex items-center gap-2.5">
                            <div class="grid size-8 place-items-center rounded-lg bg-sky-50 text-[#0088ff]">
                                <Wallet class="size-4" />
                            </div>
                            <div>
                                <h3 class="text-xs sm:text-[13px] font-bold text-slate-900 leading-snug">
                                    Dompet Digital Resmi
                                </h3>
                                <p class="text-[11px] text-slate-500">Saldo Akun TapakLokal</p>
                            </div>
                        </div>

                        <Link
                            :href="safeRoute('help.category', { category: 'wallet' })"
                            class="text-[11px] font-semibold text-slate-400 hover:text-[#0088ff] transition-colors"
                            title="Panduan TapakWallet"
                        >
                            <HelpCircle class="size-4" />
                        </Link>
                    </div>

                    <!-- Payment Logos Area with Clickable Links -->
                    <div class="flex items-center justify-center py-5 min-h-[76px]">
                        <!-- Tapak Lokal Wallet -->
                        <Link
                            :href="safeRoute('help.article', { category: 'wallet', slug: 'keuntungan-tapakwallet-dan-cara-top-up-saldo' })"
                            class="group/wallet flex items-center gap-2 rounded-xl bg-slate-100/90 hover:bg-sky-50 border border-slate-200/80 hover:border-sky-200 px-4 py-2 transition-all duration-200 hover:scale-105 shadow-2xs cursor-pointer"
                            title="Panduan Pembayaran dengan TapakWallet"
                        >
                            <div class="grid size-6 place-items-center rounded-lg bg-[#0088ff] text-white group-hover/wallet:bg-[#0074df] transition-colors">
                                <Wallet class="size-3.5" />
                            </div>
                            <span class="text-xs sm:text-[13px] font-black tracking-tight text-[#0c2340]">
                                tapak<span class="text-[#0088ff]">wallet</span>
                            </span>
                        </Link>
                    </div>
                </div>

                <!-- Footer Status -->
                <div class="pt-3 border-t border-slate-100 flex items-center justify-between text-[11px]">
                    <span class="inline-flex items-center gap-1.5 font-semibold text-slate-600">
                        <CheckCircle2 class="size-3.5 text-[#0088ff]" />
                        Bebas Biaya Admin
                    </span>
                    <span class="text-slate-400 font-medium">Checkout Kilat</span>
                </div>
            </div>
        </div>
    </section>
</template>
