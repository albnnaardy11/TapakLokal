<script setup>
import { Head, Link, useForm, usePage } from '@inertiajs/vue3';
import { ref } from 'vue';
import { route } from 'ziggy-js';
import {
    ArrowRight,
    CalendarCheck,
    CheckCircle2,
    Compass,
    Eye,
    EyeOff,
    HelpCircle,
    Lock,
    ShieldCheck,
    Store,
    TrendingUp,
    Users,
    Wallet,
} from 'lucide-vue-next';

const page = usePage();
const showPassword = ref(false);

const form = useForm({
    email: '',
    password: '',
    remember: false,
});

const submit = () => {
    form.post(route('vendor.login.store'), {
        onFinish: () => form.reset('password'),
    });
};
</script>

<template>
    <Head title="Portal Mitra Vendor & Pemandu Lokal — TapakLokal" />
    <div class="relative min-h-screen bg-[#041a14] text-slate-100 flex flex-col justify-between selection:bg-emerald-500 selection:text-black">
        <!-- Background Ambient Glow & Patterns -->
        <div class="pointer-events-none absolute inset-0 overflow-hidden">
            <div class="absolute -top-32 -left-32 size-[550px] rounded-full bg-emerald-600/15 blur-[140px]"></div>
            <div class="absolute bottom-0 right-10 size-[500px] rounded-full bg-teal-500/10 blur-[130px]"></div>
            <div class="absolute inset-0 bg-[radial-gradient(#064e3b_1px,transparent_1px)] [background-size:28px_28px] opacity-30"></div>
        </div>

        <!-- Top Header Navigation -->
        <header class="relative z-10 flex items-center justify-between border-b border-emerald-900/60 bg-[#041a14]/85 px-6 py-4 backdrop-blur-md lg:px-12">
            <div class="flex items-center gap-3">
                <Link href="/" class="flex items-center gap-2.5 font-black tracking-tight text-white group">
                    <span class="grid size-9 place-items-center rounded-xl bg-gradient-to-tr from-emerald-500 to-teal-400 text-slate-950 shadow-lg shadow-emerald-500/20 group-hover:scale-105 transition">
                        <Store class="size-5" />
                    </span>
                    <span class="text-lg">tapak<span class="text-emerald-400">lokal</span></span>
                </Link>
                <div class="hidden sm:flex items-center gap-2 border-l border-emerald-800/80 pl-3">
                    <span class="rounded-md border border-emerald-500/30 bg-emerald-500/10 px-2 py-0.5 text-[10px] font-bold tracking-wider text-emerald-300 uppercase">
                        Vendor Partner Hub
                    </span>
                </div>
            </div>

            <div class="flex items-center gap-4 text-xs font-semibold">
                <a
                    href="https://wa.me/6281234567890?text=Halo%20Admin%20TapakLokal,%20saya%20tertarik%20bergabung%20menjadi%20mitra%20vendor."
                    target="_blank"
                    rel="noopener noreferrer"
                    class="hidden sm:flex items-center gap-1.5 text-emerald-400 hover:text-emerald-300 transition"
                >
                    <HelpCircle class="size-4" />
                    <span>Dukungan Kemitraan</span>
                </a>
                <Link
                    href="/"
                    class="rounded-xl border border-emerald-800/80 bg-emerald-950/60 px-3.5 py-1.5 text-xs text-emerald-200 transition hover:border-emerald-600 hover:text-white"
                >
                    &larr; Beranda
                </Link>
            </div>
        </header>

        <!-- Main Content Area -->
        <main class="relative z-10 flex flex-1 items-center justify-center p-6 sm:p-10">
            <div class="w-full max-w-4xl grid grid-cols-1 lg:grid-cols-[1.1fr_1fr] rounded-3xl border border-emerald-900/60 bg-emerald-950/40 shadow-2xl shadow-black/80 backdrop-blur-xl overflow-hidden">
                <!-- Left Info Showcase -->
                <div class="relative hidden lg:flex flex-col justify-between p-10 bg-gradient-to-br from-[#062c22] via-[#042018] to-[#02140e] border-r border-emerald-900/40">
                    <div>
                        <div class="inline-flex items-center gap-2 rounded-full border border-emerald-400/30 bg-emerald-500/10 px-3 py-1 text-[11px] font-bold tracking-wide text-emerald-300">
                            <ShieldCheck class="size-3.5 text-emerald-400" />
                            <span>MITRA RESMI TERVERIFIKASI</span>
                        </div>

                        <h1 class="mt-6 text-3xl font-black leading-tight text-white tracking-tight">
                            Tumbuhkan Bisnis Wisata Lokal Bersama Kami.
                        </h1>

                        <p class="mt-4 text-xs leading-relaxed text-emerald-200/80">
                            Kelola jadwal keberangkatan open trip, kuota peserta, dan pantau pembayaran masuk langsung dalam satu dashboard terintegrasi.
                        </p>

                        <!-- Partner Value Highlights -->
                        <div class="mt-8 space-y-3.5 text-xs">
                            <div class="flex items-start gap-3 rounded-2xl border border-emerald-800/50 bg-emerald-900/30 p-3 text-emerald-100">
                                <CalendarCheck class="size-4 shrink-0 text-emerald-400 mt-0.5" />
                                <div>
                                    <p class="font-bold text-white">Manajemen Kuota & Jadwal</p>
                                    <p class="text-[11px] text-emerald-300/80">Atur ketersediaan trip, tanggal berangkat, dan batasan peserta secara mandiri.</p>
                                </div>
                            </div>
                            <div class="flex items-start gap-3 rounded-2xl border border-emerald-800/50 bg-emerald-900/30 p-3 text-emerald-100">
                                <Wallet class="size-4 shrink-0 text-amber-400 mt-0.5" />
                                <div>
                                    <p class="font-bold text-white">Pencairan Dana Otomatis</p>
                                    <p class="text-[11px] text-emerald-300/80">Dana hasil pemesanan diproses transparan dan langsung disalurkan ke rekening Anda.</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Bottom Stat -->
                    <div class="pt-6 border-t border-emerald-900/50 text-[11px] text-emerald-400 flex items-center justify-between">
                        <span>Pemberdayaan Pemandu Lokal Indonesia</span>
                        <span class="font-bold text-amber-400">100% Terintegrasi</span>
                    </div>
                </div>

                <!-- Right Form Area -->
                <div class="p-8 sm:p-10 flex flex-col justify-center">
                    <div class="mb-6">
                        <div class="flex items-center gap-2 text-emerald-400 text-xs font-bold uppercase tracking-wider mb-2">
                            <Store class="size-4" />
                            <span>Vendor Authentication</span>
                        </div>
                        <h2 class="text-2xl font-black text-white tracking-tight">
                            Masuk Portal Mitra
                        </h2>
                        <p class="mt-1 text-xs text-emerald-200/70">
                            Masukkan email dan kata sandi akun mitra vendor resmi Anda.
                        </p>
                    </div>

                    <!-- Flash Message -->
                    <div
                        v-if="page.props.flash?.error"
                        class="mb-5 rounded-2xl border border-rose-500/30 bg-rose-500/10 p-3.5 text-xs font-semibold text-rose-300"
                    >
                        {{ page.props.flash.error }}
                    </div>

                    <!-- Form -->
                    <form class="space-y-4" @submit.prevent="submit">
                        <div>
                            <label class="mb-1.5 block text-xs font-bold text-emerald-100">
                                Email Mitra Vendor
                            </label>
                            <input
                                v-model="form.email"
                                type="email"
                                autocomplete="email"
                                required
                                placeholder="vendor@tapaklokal.test"
                                class="w-full rounded-xl border border-emerald-800/80 bg-emerald-950/70 px-3.5 py-2.5 text-xs text-white placeholder-emerald-700 outline-none transition focus:border-emerald-400 focus:ring-2 focus:ring-emerald-400/20"
                            />
                        </div>

                        <div>
                            <div class="mb-1.5 flex items-center justify-between">
                                <label class="text-xs font-bold text-emerald-100">Kata Sandi</label>
                                <a
                                    href="https://wa.me/6281234567890?text=Halo%20Admin,%20saya%20lupa%20kata%20sandi%20akun%20vendor%20saya."
                                    target="_blank"
                                    rel="noopener noreferrer"
                                    class="text-[11px] font-bold text-emerald-400 hover:text-emerald-300"
                                >
                                    Lupa sandi?
                                </a>
                            </div>
                            <div class="relative">
                                <input
                                    v-model="form.password"
                                    :type="showPassword ? 'text' : 'password'"
                                    autocomplete="current-password"
                                    required
                                    placeholder="••••••••••••"
                                    class="w-full rounded-xl border border-emerald-800/80 bg-emerald-950/70 px-3.5 py-2.5 pr-10 text-xs text-white placeholder-emerald-700 outline-none transition focus:border-emerald-400 focus:ring-2 focus:ring-emerald-400/20"
                                />
                                <button
                                    type="button"
                                    class="absolute right-3 top-2.5 text-emerald-500 hover:text-emerald-300"
                                    @click="showPassword = !showPassword"
                                >
                                    <EyeOff v-if="showPassword" class="size-4" />
                                    <Eye v-else class="size-4" />
                                </button>
                            </div>
                        </div>

                        <div class="flex items-center justify-between text-xs">
                            <label class="flex items-center gap-2 text-emerald-300/80 cursor-pointer">
                                <input
                                    v-model="form.remember"
                                    type="checkbox"
                                    class="rounded border-emerald-800 bg-emerald-950 text-emerald-500 focus:ring-emerald-400"
                                />
                                <span>Ingat saya di perangkat ini</span>
                            </label>
                        </div>

                        <!-- Validation Errors -->
                        <div v-if="Object.keys(form.errors).length" class="space-y-1 rounded-xl border border-rose-500/30 bg-rose-950/30 p-3 text-xs font-semibold text-rose-400">
                            <p v-for="(error, key) in form.errors" :key="key">{{ error }}</p>
                        </div>

                        <!-- Submit Button -->
                        <button
                            :disabled="form.processing"
                            class="mt-2 flex w-full items-center justify-center gap-2 rounded-xl bg-gradient-to-r from-emerald-500 to-teal-500 py-3 text-xs font-bold text-slate-950 shadow-lg shadow-emerald-500/20 transition hover:brightness-110 active:scale-95 disabled:opacity-50"
                        >
                            <Store class="size-4" />
                            <span>{{ form.processing ? 'Masuk ke Portal…' : 'Buka Dashboard Vendor' }}</span>
                            <ArrowRight class="size-4" />
                        </button>
                    </form>

                    <!-- Bottom Notice -->
                    <div class="mt-8 rounded-2xl border border-emerald-900/60 bg-emerald-950/40 p-3.5 text-center text-[11px] text-emerald-300/80">
                        Bukan mitra vendor?
                        <Link :href="route('login')" class="font-bold text-emerald-400 hover:underline">
                            Login Wisatawan
                        </Link>
                        atau
                        <Link :href="route('admin.login')" class="font-bold text-cyan-400 hover:underline">
                            Admin Internal
                        </Link>
                    </div>
                </div>
            </div>
        </main>

        <!-- Footer -->
        <footer class="relative z-10 border-t border-emerald-900/60 bg-[#041a14]/80 py-4 text-center text-xs text-emerald-400/80 backdrop-blur-md">
            &copy; {{ new Date().getFullYear() }} TapakLokal. Ekosistem Mitra & Pemandu Lokal Terpercaya.
        </footer>
    </div>
</template>
