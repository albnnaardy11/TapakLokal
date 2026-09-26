<script setup>
import {
    Landmark,
    Smartphone,
    Store,
    Wallet,
    CheckCircle2,
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
        default: 'Pilihan metode pembayaran lengkap dan terverifikasi untuk memastikan setiap transaksi berjalan cepat, aman, dan tanpa biaya tersembunyi.',
    },
});
</script>

<template>
    <section class="mx-auto max-w-[1180px]" aria-labelledby="popular-payments-heading">
        <!-- Header -->
        <div class="px-1">
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

        <!-- 4 Categorized Payment Cards (Fills Full 1180px Width Cleanly) -->
        <div class="mt-7 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-5 px-1">
            <!-- 1. Transfer Bank & Virtual Account -->
            <div class="flex flex-col justify-between rounded-2xl border border-slate-200/90 bg-white p-5 shadow-2xs hover:border-[#0088ff]/40 hover:shadow-xs transition-all duration-200">
                <div>
                    <!-- Header with Icon & Category Name -->
                    <div class="flex items-center gap-2.5 pb-3.5 border-b border-slate-100">
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

                    <!-- Payment Logos Area with Proportional Wrappers -->
                    <div class="flex items-center justify-around gap-2 py-5 min-h-[76px]">
                        <!-- BCA -->
                        <div class="flex h-11 w-24 sm:w-28 items-center justify-center" title="Bank Central Asia (BCA)">
                            <img
                                src="/Assets/Images/logo-bank/bca.webp"
                                alt="Bank BCA"
                                class="max-h-7 sm:max-h-8 w-auto max-w-[95px] object-contain transition-transform duration-200 hover:scale-105"
                                loading="lazy"
                            />
                        </div>

                        <!-- Mandiri -->
                        <div class="flex h-11 w-24 sm:w-28 items-center justify-center" title="Bank Mandiri">
                            <img
                                src="/Assets/Images/logo-bank/mandiri.webp"
                                alt="Bank Mandiri"
                                class="max-h-7 sm:max-h-8 w-auto max-w-[100px] object-contain transition-transform duration-200 hover:scale-105"
                                loading="lazy"
                            />
                        </div>
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
                    <div class="flex items-center gap-2.5 pb-3.5 border-b border-slate-100">
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

                    <!-- Payment Logos Area with Proportional Wrappers -->
                    <div class="flex items-center justify-around gap-2 py-5 min-h-[76px]">
                        <!-- GoPay -->
                        <div class="flex h-11 w-24 sm:w-28 items-center justify-center" title="GoPay">
                            <img
                                src="/Assets/Images/logo-bank/gopay.webp"
                                alt="GoPay"
                                class="max-h-6 sm:max-h-7 w-auto max-w-[100px] object-contain transition-transform duration-200 hover:scale-105"
                                loading="lazy"
                            />
                        </div>

                        <!-- OVO -->
                        <div class="flex h-11 w-24 sm:w-28 items-center justify-center" title="OVO">
                            <img
                                src="/Assets/Images/logo-bank/ovo.webp"
                                alt="OVO"
                                class="max-h-9 sm:max-h-10 w-auto max-w-[48px] object-contain transition-transform duration-200 hover:scale-105"
                                loading="lazy"
                            />
                        </div>
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
                    <div class="flex items-center gap-2.5 pb-3.5 border-b border-slate-100">
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

                    <!-- Payment Logos Area with Proportional Wrappers -->
                    <div class="flex items-center justify-around gap-2 py-5 min-h-[76px]">
                        <!-- Indomaret -->
                        <div class="flex h-11 w-24 sm:w-28 items-center justify-center" title="Indomaret">
                            <img
                                src="/Assets/Images/logo-bank/indomaret.webp"
                                alt="Indomaret"
                                class="max-h-7 sm:max-h-8 w-auto max-w-[95px] object-contain transition-transform duration-200 hover:scale-105"
                                loading="lazy"
                            />
                        </div>

                        <!-- Alfamart -->
                        <div class="flex h-11 w-24 sm:w-28 items-center justify-center" title="Alfamart">
                            <img
                                src="/Assets/Images/logo-bank/alfa.webp"
                                alt="Alfamart"
                                class="max-h-7 sm:max-h-8 w-auto max-w-[95px] object-contain transition-transform duration-200 hover:scale-105"
                                loading="lazy"
                            />
                        </div>
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
                    <div class="flex items-center gap-2.5 pb-3.5 border-b border-slate-100">
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

                    <!-- Payment Logos Area with Proportional Wrappers -->
                    <div class="flex items-center justify-center py-5 min-h-[76px]">
                        <!-- Tapak Lokal Wallet -->
                        <div class="flex items-center gap-2 rounded-xl bg-slate-100/90 border border-slate-200/80 px-4 py-2 transition-transform duration-200 hover:scale-105 shadow-2xs" title="Tapak Lokal Wallet">
                            <div class="grid size-6 place-items-center rounded-lg bg-[#0088ff] text-white">
                                <Wallet class="size-3.5" />
                            </div>
                            <span class="text-xs sm:text-[13px] font-black tracking-tight text-[#0c2340]">
                                tapak<span class="text-[#0088ff]">wallet</span>
                            </span>
                        </div>
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
