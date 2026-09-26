<script setup>
import { ref } from 'vue';
import { Link } from '@inertiajs/vue3';
import { route } from 'ziggy-js';
import {
    Wallet,
    X,
    ShieldCheck,
    Zap,
    RefreshCw,
    ArrowRight,
    CheckCircle2,
} from 'lucide-vue-next';

const isModalOpen = ref(false);

const openModal = () => {
    isModalOpen.value = true;
};

const closeModal = () => {
    isModalOpen.value = false;
};

function safeRoute(name, params) {
    try {
        if (typeof route === 'function') {
            return params !== undefined ? route(name, params) : route(name);
        }
        return '/account';
    } catch {
        return '/account';
    }
}
</script>

<template>
    <section class="mx-auto max-w-[1180px] px-1" aria-label="Informasi TapakWallet">
        <!-- Vibrant Light-Blue Oceanic Gradient Banner matching Navbar & Traveloka Signature -->
        <div class="relative overflow-hidden rounded-2xl border border-white/30 bg-gradient-to-r from-[#0062d2] via-[#0088ff] to-[#01a2f3] px-5 py-4 sm:px-7 sm:py-5 shadow-[0_8px_30px_rgba(0,136,255,0.22)] transition-all duration-300 hover:shadow-[0_12px_36px_rgba(0,136,255,0.30)]">
            <!-- Subtle Radial Light Glow & Texture -->
            <div class="pointer-events-none absolute -right-16 -top-16 size-48 rounded-full bg-white/20 blur-2xl"></div>
            <div class="pointer-events-none absolute -bottom-10 left-1/3 size-40 rounded-full bg-cyan-200/25 blur-xl"></div>

            <div class="relative z-10 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                <!-- Left Content: Icon, Brand & Catchy Large Tagline -->
                <div class="flex flex-wrap items-center gap-3 sm:gap-6 text-sm sm:text-[15px] lg:text-base">
                    <!-- Brand Pill -->
                    <div class="inline-flex items-center gap-2.5 font-bold text-white tracking-tight shrink-0">
                        <div class="grid size-8 sm:size-9 place-items-center rounded-xl bg-white/20 text-white shadow-xs backdrop-blur-xs">
                            <Wallet class="size-4 sm:size-4.5" />
                        </div>
                        <span class="text-base sm:text-lg font-black tracking-tight text-white drop-shadow-xs">
                            tapak<span class="text-sky-200">wallet</span>
                        </span>
                    </div>

                    <!-- Divider Dot on Desktop -->
                    <span class="hidden sm:inline-block size-1.5 rounded-full bg-white/40"></span>

                    <!-- Large Tagline with High-Contrast Highlight -->
                    <p class="leading-relaxed text-white font-medium text-sm sm:text-[15px] lg:text-base">
                        <span class="font-extrabold text-yellow-300 drop-shadow-xs">TPasti, TPraktis, TPercaya</span>
                        <span class="text-white drop-shadow-xs"> buat wujudkan semua kebutuhan liburanmu!</span>
                    </p>
                </div>

                <!-- Right Action: Clean Crisp White Pill Button -->
                <button
                    type="button"
                    @click="openModal"
                    class="inline-flex w-fit shrink-0 items-center justify-center rounded-full bg-white px-6 py-2.5 sm:px-7 sm:py-2.5 text-xs sm:text-sm font-extrabold text-[#0070d6] shadow-sm transition-all duration-150 hover:bg-sky-50 hover:shadow-md hover:scale-102 active:scale-95"
                >
                    Learn More
                </button>
            </div>
        </div>

        <!-- Informative Modal Explaining TapakWallet -->
        <teleport to="body">
            <transition
                enter-active-class="transition duration-200 ease-out"
                enter-from-class="opacity-0"
                enter-to-class="opacity-100"
                leave-active-class="transition duration-150 ease-in"
                leave-from-class="opacity-100"
                leave-to-class="opacity-0"
            >
                <div
                    v-if="isModalOpen"
                    class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/60 p-4 backdrop-blur-xs"
                    @click.self="closeModal"
                    aria-modal="true"
                    role="dialog"
                >
                    <div class="relative w-full max-w-lg overflow-hidden rounded-3xl bg-white shadow-2xl transition-all">
                        <!-- Modal Header Banner with Matching Vibrant Gradient -->
                        <div class="relative bg-gradient-to-r from-[#0062d2] via-[#0088ff] to-[#01a2f3] p-6 text-white">
                            <button
                                type="button"
                                @click="closeModal"
                                class="absolute right-4 top-4 grid size-8 place-items-center rounded-full bg-white/15 text-white transition hover:bg-white/30"
                                aria-label="Tutup modal"
                            >
                                <X class="size-4" />
                            </button>

                            <div class="flex items-center gap-3">
                                <div class="grid size-11 place-items-center rounded-2xl bg-white/20 text-white backdrop-blur-xs shadow-xs">
                                    <Wallet class="size-6 text-white" />
                                </div>
                                <div>
                                    <span class="text-[11px] font-extrabold uppercase tracking-widest text-yellow-300">
                                        Dompet Digital Resmi
                                    </span>
                                    <h3 class="text-xl font-black tracking-tight text-white">
                                        Mengenal Tapak<span class="text-sky-200">Wallet</span>
                                    </h3>
                                </div>
                            </div>

                            <p class="mt-3 text-xs sm:text-[13px] leading-relaxed text-white/90">
                                Solusi pembayaran saldo terintegrasi di TapakLokal untuk kemudahan pemesanan Open Trip dan Private Trip tanpa repot.
                            </p>
                        </div>

                        <!-- Modal Body: 3 Pillars of TapakWallet (TPasti, TPraktis, TPercaya) -->
                        <div class="space-y-4 p-6">
                            <!-- 1. TPasti -->
                            <div class="flex items-start gap-3.5 rounded-2xl border border-slate-100 bg-slate-50/60 p-3.5">
                                <div class="mt-0.5 grid size-9 shrink-0 place-items-center rounded-xl bg-sky-100 text-[#0088ff]">
                                    <ShieldCheck class="size-5" />
                                </div>
                                <div>
                                    <h4 class="text-sm font-bold text-slate-900">
                                        <span class="text-[#0088ff]">TPasti</span> — Pasti Aman & Berlisensi
                                    </h4>
                                    <p class="mt-0.5 text-xs text-slate-500 leading-relaxed">
                                        Setiap transaksi diproteksi standar enkripsi perbankan tinggi. Saldo dan riwayat transaksi tercatat rapi secara real-time.
                                    </p>
                                </div>
                            </div>

                            <!-- 2. TPraktis -->
                            <div class="flex items-start gap-3.5 rounded-2xl border border-slate-100 bg-slate-50/60 p-3.5">
                                <div class="mt-0.5 grid size-9 shrink-0 place-items-center rounded-xl bg-sky-100 text-[#0088ff]">
                                    <Zap class="size-5" />
                                </div>
                                <div>
                                    <h4 class="text-sm font-bold text-slate-900">
                                        <span class="text-[#0088ff]">TPraktis</span> — Checkout Instan 1-Klik
                                    </h4>
                                    <p class="mt-0.5 text-xs text-slate-500 leading-relaxed">
                                        Bayar tiket perjalanan langsung dari saldo akun tanpa perlu transfer antar bank atau upload bukti pembayaran manual.
                                    </p>
                                </div>
                            </div>

                            <!-- 3. TPercaya -->
                            <div class="flex items-start gap-3.5 rounded-2xl border border-slate-100 bg-slate-50/60 p-3.5">
                                <div class="mt-0.5 grid size-9 shrink-0 place-items-center rounded-xl bg-sky-100 text-[#0088ff]">
                                    <RefreshCw class="size-5" />
                                </div>
                                <div>
                                    <h4 class="text-sm font-bold text-slate-900">
                                        <span class="text-[#0088ff]">TPercaya</span> — Bebas Biaya & Garansi Refund
                                    </h4>
                                    <p class="mt-0.5 text-xs text-slate-500 leading-relaxed">
                                        100% bebas biaya administrasi. Jika terjadi pembatalan sesuai kebijakan trip, pengembalian dana langsung masuk kembali ke saldo TapakWallet.
                                    </p>
                                </div>
                            </div>
                        </div>

                        <!-- Modal Footer -->
                        <div class="flex items-center justify-between border-t border-slate-100 bg-slate-50 px-6 py-4">
                            <span class="inline-flex items-center gap-1.5 text-xs font-semibold text-slate-500">
                                <CheckCircle2 class="size-4 text-[#0088ff]" />
                                Terintegrasi di Akun Anda
                            </span>

                            <div class="flex items-center gap-2">
                                <button
                                    type="button"
                                    @click="closeModal"
                                    class="rounded-xl px-4 py-2 text-xs font-bold text-slate-600 transition hover:bg-slate-200/70"
                                >
                                    Tutup
                                </button>
                                <Link
                                    :href="safeRoute('account.section', { section: 'wallet' })"
                                    class="inline-flex items-center gap-1.5 rounded-xl bg-[#0088ff] px-4 py-2 text-xs font-bold text-white shadow-sm transition hover:bg-[#0077e6] active:scale-95"
                                >
                                    <span>Cek Saldo Saya</span>
                                    <ArrowRight class="size-3.5" />
                                </Link>
                            </div>
                        </div>
                    </div>
                </div>
            </transition>
        </teleport>
    </section>
</template>
