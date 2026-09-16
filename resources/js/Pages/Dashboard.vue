<script setup>
import { Head, Link, router } from '@inertiajs/vue3';
import { route } from 'ziggy-js';
import {
    ArrowRight,
    Award,
    Calendar,
    CheckCircle2,
    Coins,
    Compass,
    ExternalLink,
    Gift,
    LogOut,
    Mail,
    ReceiptText,
    Shield,
    Sparkles,
    TentTree,
    User,
    Wallet
} from 'lucide-vue-next';

const props = defineProps({
    user: {
        type: Object,
        required: true,
    },
    recentActivities: {
        type: Array,
        default: () => [],
    },
});

const logout = () => {
    router.post(route('logout'));
};
</script>

<template>
    <Head title="Dashboard Petualang - TapakLokal" />

    <div class="min-h-screen bg-[#f5f7fb] font-sans text-slate-900">
        <!-- Top Navbar -->
        <header class="sticky top-0 z-30 border-b border-[#dce5f0] bg-white/95 backdrop-blur-sm shadow-[0_1px_0_rgba(15,44,92,0.06)]">
            <div class="mx-auto flex h-16 max-w-[1180px] items-center justify-between px-5 sm:px-8">
                <div class="flex items-center gap-6">
                    <Link href="/" class="text-left" aria-label="TapakLokal Beranda">
                        <span class="text-xl font-extrabold tracking-[-0.07em] text-slate-900">
                            tapa<span class="text-[#3E7BEF]">k</span>lokal
                        </span>
                    </Link>

                    <nav class="hidden items-center gap-1 text-xs font-semibold sm:flex">
                        <Link href="/dashboard" class="rounded-lg bg-[#edf3ff] px-3 py-1.5 text-[#3E7BEF]">
                            Dashboard
                        </Link>
                        <Link href="/" class="rounded-lg px-3 py-1.5 text-slate-600 transition hover:bg-slate-100 hover:text-slate-900">
                            Eksplorasi Trip
                        </Link>
                        <Link href="/account" class="rounded-lg px-3 py-1.5 text-slate-600 transition hover:bg-slate-100 hover:text-slate-900">
                            Detail Akun & Pesanan
                        </Link>
                    </nav>
                </div>

                <div class="flex items-center gap-3">
                    <div class="flex items-center gap-2.5 rounded-full border border-[#dce5f0] bg-[#f8fbfe] py-1 pl-1 pr-3">
                        <img
                            v-if="user.avatar"
                            :src="user.avatar"
                            :alt="user.name"
                            class="size-7 rounded-full object-cover border border-white"
                        />
                        <span
                            v-else
                            class="grid size-7 place-items-center rounded-full bg-[#3E7BEF] text-xs font-bold text-white"
                        >
                            {{ user.name.charAt(0).toUpperCase() }}
                        </span>
                        <span class="hidden text-xs font-bold text-slate-700 sm:inline">
                            {{ user.name }}
                        </span>
                    </div>

                    <button
                        type="button"
                        @click="logout"
                        class="inline-flex h-9 items-center gap-1.5 rounded-xl border border-[#dce5f0] bg-white px-3 text-xs font-bold text-slate-700 transition hover:border-rose-300 hover:bg-rose-50 hover:text-rose-600"
                        title="Keluar dari akun"
                    >
                        <LogOut class="size-3.5 text-rose-500" />
                        <span class="hidden sm:inline">Keluar</span>
                    </button>
                </div>
            </div>
        </header>

        <!-- Main Content -->
        <main class="mx-auto max-w-[1180px] px-5 py-8 sm:px-8 sm:py-10">
            <!-- Greeting Banner -->
            <div class="relative isolate overflow-hidden rounded-[24px] bg-gradient-to-r from-[#183660] via-[#1d4277] to-[#3E7BEF] p-7 text-white shadow-[0_12px_32px_rgba(24,54,96,0.14)] sm:p-9">
                <div class="pointer-events-none absolute -right-16 -top-20 -z-10 size-72 rounded-full bg-sky-400/25 blur-3xl" aria-hidden="true"></div>

                <div class="flex flex-col gap-5 sm:flex-row sm:items-center sm:justify-between">
                    <div class="flex items-center gap-4">
                        <img
                            v-if="user.avatar"
                            :src="user.avatar"
                            :alt="user.name"
                            class="size-16 rounded-2xl border-2 border-white/30 object-cover shadow-md"
                        />
                        <div
                            v-else
                            class="grid size-16 place-items-center rounded-2xl bg-white/15 text-2xl font-black text-white shadow-md backdrop-blur-sm"
                        >
                            {{ user.name.charAt(0).toUpperCase() }}
                        </div>

                        <div>
                            <div class="flex items-center gap-2">
                                <h1 class="text-xl font-extrabold sm:text-2xl">
                                    Halo, {{ user.name }}! 👋
                                </h1>
                                <span v-if="user.google_id" class="rounded-md bg-white/20 px-2 py-0.5 text-[10px] font-bold text-white backdrop-blur-sm" title="Terhubung dengan Google">
                                    Google Login
                                </span>
                            </div>
                            <p class="mt-1 text-xs text-white/80">
                                {{ user.email }} · {{ user.username }}
                            </p>
                        </div>
                    </div>

                    <div class="flex flex-wrap gap-2.5">
                        <Link
                            href="/"
                            class="inline-flex h-10 items-center gap-2 rounded-xl bg-white px-4 text-xs font-bold text-[#183660] shadow-sm transition hover:bg-[#edf3ff] hover:text-[#3E7BEF]"
                        >
                            <Compass class="size-4 text-[#3E7BEF]" />
                            <span>Jelajahi Trip</span>
                        </Link>
                        <Link
                            href="/account"
                            class="inline-flex h-10 items-center gap-2 rounded-xl border border-white/25 bg-white/10 px-4 text-xs font-bold text-white backdrop-blur-sm transition hover:bg-white/20"
                        >
                            <span>Kelola Akun</span>
                            <ArrowRight class="size-3.5" />
                        </Link>
                    </div>
                </div>
            </div>

            <!-- Stats Grid -->
            <div class="mt-7 grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
                <!-- Member Tier -->
                <div class="flex items-center gap-3.5 rounded-2xl border border-[#dce5f0] bg-white p-5 shadow-sm">
                    <span class="grid size-11 place-items-center rounded-xl bg-amber-50 text-amber-600">
                        <Award class="size-6" />
                    </span>
                    <div>
                        <p class="text-[11px] font-semibold uppercase tracking-wider text-slate-400">Status Keanggotaan</p>
                        <h3 class="mt-0.5 text-sm font-extrabold capitalize text-slate-800">
                            {{ user.member_tier }} Priority
                        </h3>
                    </div>
                </div>

                <!-- Points -->
                <div class="flex items-center gap-3.5 rounded-2xl border border-[#dce5f0] bg-white p-5 shadow-sm">
                    <span class="grid size-11 place-items-center rounded-xl bg-blue-50 text-[#3E7BEF]">
                        <Coins class="size-6" />
                    </span>
                    <div>
                        <p class="text-[11px] font-semibold uppercase tracking-wider text-slate-400">TapakPoints</p>
                        <h3 class="mt-0.5 text-sm font-extrabold text-[#3E7BEF]">
                            {{ user.points.toLocaleString('id-ID') }} Poin
                        </h3>
                    </div>
                </div>

                <!-- Wallet -->
                <div class="flex items-center gap-3.5 rounded-2xl border border-[#dce5f0] bg-white p-5 shadow-sm">
                    <span class="grid size-11 place-items-center rounded-xl bg-emerald-50 text-emerald-600">
                        <Wallet class="size-6" />
                    </span>
                    <div>
                        <p class="text-[11px] font-semibold uppercase tracking-wider text-slate-400">Saldo Dompet</p>
                        <h3 class="mt-0.5 text-sm font-extrabold text-slate-800">
                            Rp {{ Number(user.wallet_balance).toLocaleString('id-ID') }}
                        </h3>
                    </div>
                </div>

                <!-- Account Security -->
                <div class="flex items-center gap-3.5 rounded-2xl border border-[#dce5f0] bg-white p-5 shadow-sm">
                    <span class="grid size-11 place-items-center rounded-xl bg-indigo-50 text-indigo-600">
                        <Shield class="size-6" />
                    </span>
                    <div>
                        <p class="text-[11px] font-semibold uppercase tracking-wider text-slate-400">Verifikasi Email</p>
                        <h3 class="mt-0.5 flex items-center gap-1.5 text-sm font-extrabold text-emerald-600">
                            <CheckCircle2 class="size-4" />
                            <span>Terverifikasi</span>
                        </h3>
                    </div>
                </div>
            </div>

            <!-- Quick Action Cards -->
            <div class="mt-8">
                <h2 class="text-sm font-extrabold text-[#183660]">
                    Akses Cepat Fitur TapakLokal
                </h2>
                <div class="mt-3 grid grid-cols-1 gap-4 sm:grid-cols-3">
                    <Link
                        href="/"
                        class="group flex flex-col justify-between rounded-2xl border border-[#dce5f0] bg-white p-5 shadow-sm transition duration-200 hover:-translate-y-0.5 hover:border-[#3E7BEF] hover:shadow-md"
                    >
                        <div class="flex items-center justify-between">
                            <span class="grid size-10 place-items-center rounded-xl bg-[#edf3ff] text-[#3E7BEF]">
                                <TentTree class="size-5" />
                            </span>
                            <ArrowRight class="size-4 text-slate-300 transition group-hover:translate-x-1 group-hover:text-[#3E7BEF]" />
                        </div>
                        <div class="mt-4">
                            <h3 class="text-xs font-bold text-slate-800">Open Trip Nusantara</h3>
                            <p class="mt-1 text-[11px] text-slate-400">Jelajahi trip terverifikasi bersama tour guide asli daerah.</p>
                        </div>
                    </Link>

                    <Link
                        href="/account"
                        class="group flex flex-col justify-between rounded-2xl border border-[#dce5f0] bg-white p-5 shadow-sm transition duration-200 hover:-translate-y-0.5 hover:border-[#3E7BEF] hover:shadow-md"
                    >
                        <div class="flex items-center justify-between">
                            <span class="grid size-10 place-items-center rounded-xl bg-amber-50 text-amber-600">
                                <ReceiptText class="size-5" />
                            </span>
                            <ArrowRight class="size-4 text-slate-300 transition group-hover:translate-x-1 group-hover:text-amber-600" />
                        </div>
                        <div class="mt-4">
                            <h3 class="text-xs font-bold text-slate-800">Pemesanan & Tiket</h3>
                            <p class="mt-1 text-[11px] text-slate-400">Cek status tiket aktif, jadwal keberangkatan, dan riwayat trip.</p>
                        </div>
                    </Link>

                    <Link
                        href="/account"
                        class="group flex flex-col justify-between rounded-2xl border border-[#dce5f0] bg-white p-5 shadow-sm transition duration-200 hover:-translate-y-0.5 hover:border-[#3E7BEF] hover:shadow-md"
                    >
                        <div class="flex items-center justify-between">
                            <span class="grid size-10 place-items-center rounded-xl bg-purple-50 text-purple-600">
                                <Gift class="size-5" />
                            </span>
                            <ArrowRight class="size-4 text-slate-300 transition group-hover:translate-x-1 group-hover:text-purple-600" />
                        </div>
                        <div class="mt-4">
                            <h3 class="text-xs font-bold text-slate-800">Voucher & Keuntungan</h3>
                            <p class="mt-1 text-[11px] text-slate-400">Klaim promo spesial anggota dan tukarkan TapakPoints milikmu.</p>
                        </div>
                    </Link>
                </div>
            </div>

            <!-- Recent Activity Table -->
            <div class="mt-8 rounded-2xl border border-[#dce5f0] bg-white p-6 shadow-sm">
                <div class="flex items-center justify-between">
                    <div>
                        <h2 class="text-sm font-extrabold text-[#183660]">Aktivitas Akun Terbaru</h2>
                        <p class="mt-0.5 text-[11px] text-slate-400">Catatan transaksi dan pembaruan akunmu.</p>
                    </div>
                    <Link href="/account" class="text-xs font-bold text-[#3E7BEF] hover:underline">
                        Lihat Semua
                    </Link>
                </div>

                <div class="mt-4 divide-y divide-slate-100">
                    <div
                        v-for="activity in recentActivities"
                        :key="activity.id"
                        class="flex items-center justify-between py-3"
                    >
                        <div class="flex items-center gap-3">
                            <span class="grid size-8 place-items-center rounded-lg bg-slate-50 text-[#3E7BEF]">
                                <Sparkles class="size-4" />
                            </span>
                            <div>
                                <h3 class="text-xs font-bold text-slate-800">{{ activity.title }}</h3>
                                <p class="text-[10px] text-slate-400">{{ activity.date }} · Kategori {{ activity.type }}</p>
                            </div>
                        </div>

                        <span
                            class="rounded-md border px-2 py-0.5 text-[10px] font-bold"
                            :class="activity.badge"
                        >
                            {{ activity.status }}
                        </span>
                    </div>
                </div>
            </div>
        </main>
    </div>
</template>
