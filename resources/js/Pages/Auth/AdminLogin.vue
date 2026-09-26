<script setup>
import { Head, Link, useForm, usePage } from '@inertiajs/vue3';
import { ref } from 'vue';
import { route } from 'ziggy-js';
import {
    Activity,
    ArrowRight,
    Compass,
    Eye,
    EyeOff,
    Key,
    Lock,
    Server,
    Shield,
    ShieldAlert,
    ShieldCheck,
} from 'lucide-vue-next';

const page = usePage();
const showPassword = ref(false);

const form = useForm({
    email: '',
    password: '',
    remember: false,
});

const submit = () => {
    form.post(route('admin.login.store'), {
        onFinish: () => form.reset('password'),
    });
};
</script>

<template>
    <Head title="Admin Command Center — TapakLokal" />
    <div class="relative min-h-screen bg-[#070d18] text-slate-100 flex flex-col justify-between selection:bg-cyan-500 selection:text-black">
        <!-- Background Ambient Glow & Grid -->
        <div class="pointer-events-none absolute inset-0 overflow-hidden">
            <div class="absolute -top-40 left-1/2 -translate-x-1/2 size-[600px] rounded-full bg-cyan-600/10 blur-[140px]"></div>
            <div class="absolute bottom-0 right-0 size-[500px] rounded-full bg-indigo-600/10 blur-[130px]"></div>
            <div class="absolute inset-0 bg-[radial-gradient(#1e293b_1px,transparent_1px)] [background-size:24px_24px] opacity-25"></div>
        </div>

        <!-- Top Header Navigation -->
        <header class="relative z-10 flex items-center justify-between border-b border-slate-800/80 bg-[#070d18]/80 px-6 py-4 backdrop-blur-md lg:px-12">
            <div class="flex items-center gap-3">
                <Link href="/" class="flex items-center gap-2.5 font-black tracking-tight text-white group">
                    <span class="grid size-9 place-items-center rounded-xl bg-gradient-to-tr from-cyan-500 to-blue-600 text-white shadow-lg shadow-cyan-500/20 group-hover:scale-105 transition">
                        <Compass class="size-5" />
                    </span>
                    <span class="text-lg">tapak<span class="text-cyan-400">lokal</span></span>
                </Link>
                <div class="hidden sm:flex items-center gap-2 border-l border-slate-800 pl-3">
                    <span class="rounded-md border border-cyan-500/30 bg-cyan-500/10 px-2 py-0.5 text-[10px] font-bold tracking-wider text-cyan-300 uppercase">
                        Internal Console
                    </span>
                </div>
            </div>

            <div class="flex items-center gap-4 text-xs font-semibold">
                <div class="hidden md:flex items-center gap-2 rounded-full border border-emerald-500/20 bg-emerald-500/10 px-3 py-1 text-emerald-400">
                    <span class="size-2 rounded-full bg-emerald-400 animate-pulse"></span>
                    <span>System Operational</span>
                </div>
                <Link
                    href="/"
                    class="rounded-xl border border-slate-800 bg-slate-900/80 px-3.5 py-1.5 text-xs text-slate-300 transition hover:border-slate-700 hover:text-white"
                >
                    &larr; Beranda Utama
                </Link>
            </div>
        </header>

        <!-- Main Content Area -->
        <main class="relative z-10 flex flex-1 items-center justify-center p-6 sm:p-10">
            <div class="w-full max-w-4xl grid grid-cols-1 lg:grid-cols-[1.1fr_1fr] rounded-3xl border border-slate-800/80 bg-slate-900/60 shadow-2xl shadow-black/80 backdrop-blur-xl overflow-hidden">
                <!-- Left Info Banner -->
                <div class="relative hidden lg:flex flex-col justify-between p-10 bg-gradient-to-br from-[#0c1626] via-[#09111e] to-[#060a12] border-r border-slate-800/60">
                    <div>
                        <div class="inline-flex items-center gap-2 rounded-full border border-cyan-500/20 bg-cyan-950/40 px-3 py-1 text-[11px] font-bold tracking-wide text-cyan-300">
                            <ShieldCheck class="size-3.5 text-cyan-400" />
                            <span>ACCESS CONTROL ENGINE</span>
                        </div>

                        <h1 class="mt-6 text-3xl font-black leading-tight text-white tracking-tight">
                            Platform Management & Command Center.
                        </h1>

                        <p class="mt-4 text-xs leading-relaxed text-slate-400">
                            Portal terenkripsi untuk manajemen operasional, validasi transaksi, analitik pertumbuhan, dan tata kelola ekosistem TapakLokal.
                        </p>

                        <!-- System Security Badges -->
                        <div class="mt-8 space-y-3.5 text-xs">
                            <div class="flex items-start gap-3 rounded-2xl border border-slate-800 bg-slate-950/50 p-3 text-slate-300">
                                <Shield class="size-4 shrink-0 text-cyan-400 mt-0.5" />
                                <div>
                                    <p class="font-bold text-slate-200">Role-Based Access Control</p>
                                    <p class="text-[11px] text-slate-400">Hanya akun internal berizin resmi yang dapat mengelola sumber daya platform.</p>
                                </div>
                            </div>
                            <div class="flex items-start gap-3 rounded-2xl border border-slate-800 bg-slate-950/50 p-3 text-slate-300">
                                <Activity class="size-4 shrink-0 text-indigo-400 mt-0.5" />
                                <div>
                                    <p class="font-bold text-slate-200">Automated Audit Trail</p>
                                    <p class="text-[11px] text-slate-400">Setiap aktivitas login dan modifikasi data terekam lengkap demi kepatuhan sistem.</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Footer Warning -->
                    <div class="pt-6 border-t border-slate-800/60 text-[11px] text-slate-400 flex items-center gap-2">
                        <Server class="size-3.5 text-slate-400" />
                        <span>TapakLokal Core Engine v2.4</span>
                    </div>
                </div>

                <!-- Right Form Area -->
                <div class="p-8 sm:p-10 flex flex-col justify-center">
                    <div class="mb-6">
                        <div class="flex items-center gap-2 text-cyan-400 text-xs font-bold uppercase tracking-wider mb-2">
                            <Key class="size-4" />
                            <span>Admin Authentication</span>
                        </div>
                        <h2 class="text-2xl font-black text-white tracking-tight">
                            Masuk Administrator
                        </h2>
                        <p class="mt-1 text-xs text-slate-400">
                            Gunakan kredensial internal untuk membuka dashboard manajemen.
                        </p>
                    </div>

                    <!-- Flash Message -->
                    <div
                        v-if="page.props.flash?.error"
                        class="mb-5 rounded-2xl border border-rose-500/30 bg-rose-500/10 p-3.5 text-xs font-semibold text-rose-300 flex items-start gap-2"
                    >
                        <ShieldAlert class="size-4 shrink-0 text-rose-400 mt-0.5" />
                        <span>{{ page.props.flash.error }}</span>
                    </div>

                    <!-- Form -->
                    <form class="space-y-4" @submit.prevent="submit">
                        <div>
                            <label class="mb-1.5 block text-xs font-bold text-slate-300">
                                Email Administrator
                            </label>
                            <div class="relative">
                                <input
                                    v-model="form.email"
                                    type="email"
                                    autocomplete="email"
                                    required
                                    placeholder="admin@tapaklokal.test"
                                    class="w-full rounded-xl border border-slate-700 bg-slate-950/70 px-3.5 py-2.5 text-xs text-white placeholder-slate-600 outline-none transition focus:border-cyan-500 focus:ring-2 focus:ring-cyan-500/20"
                                />
                            </div>
                        </div>

                        <div>
                            <div class="mb-1.5 flex items-center justify-between">
                                <label class="text-xs font-bold text-slate-300">Kata Sandi</label>
                            </div>
                            <div class="relative">
                                <input
                                    v-model="form.password"
                                    :type="showPassword ? 'text' : 'password'"
                                    autocomplete="current-password"
                                    required
                                    placeholder="••••••••••••"
                                    class="w-full rounded-xl border border-slate-700 bg-slate-950/70 px-3.5 py-2.5 pr-10 text-xs text-white placeholder-slate-600 outline-none transition focus:border-cyan-500 focus:ring-2 focus:ring-cyan-500/20"
                                />
                                <button
                                    type="button"
                                    class="absolute right-3 top-2.5 text-slate-500 hover:text-slate-300"
                                    @click="showPassword = !showPassword"
                                >
                                    <EyeOff v-if="showPassword" class="size-4" />
                                    <Eye v-else class="size-4" />
                                </button>
                            </div>
                        </div>

                        <div class="flex items-center justify-between text-xs">
                            <label class="flex items-center gap-2 text-slate-400 cursor-pointer">
                                <input
                                    v-model="form.remember"
                                    type="checkbox"
                                    class="rounded border-slate-700 bg-slate-900 text-cyan-500 focus:ring-cyan-500"
                                />
                                <span>Ingat kredensial sesi</span>
                            </label>
                        </div>

                        <!-- Validation Errors -->
                        <div v-if="Object.keys(form.errors).length" class="space-y-1 rounded-xl border border-rose-500/30 bg-rose-950/30 p-3 text-xs font-semibold text-rose-400">
                            <p v-for="(error, key) in form.errors" :key="key">{{ error }}</p>
                        </div>

                        <!-- Submit Button -->
                        <button
                            :disabled="form.processing"
                            class="mt-2 flex w-full items-center justify-center gap-2 rounded-xl bg-gradient-to-r from-cyan-500 to-blue-600 py-3 text-xs font-bold text-white shadow-lg shadow-cyan-500/20 transition hover:brightness-110 active:scale-95 disabled:opacity-50"
                        >
                            <Lock class="size-4" />
                            <span>{{ form.processing ? 'Mengautentikasi…' : 'Buka Command Center' }}</span>
                            <ArrowRight class="size-4" />
                        </button>
                    </form>

                    <!-- Bottom Notice -->
                    <div class="mt-8 rounded-2xl border border-slate-800/80 bg-slate-950/40 p-3 text-center text-[11px] text-slate-400">
                        Bukan staf admin platform?
                        <Link :href="route('login')" class="font-bold text-cyan-400 hover:underline">
                            Login Wisatawan
                        </Link>
                        atau
                        <Link :href="route('vendor.login')" class="font-bold text-emerald-400 hover:underline">
                            Portal Mitra Vendor
                        </Link>
                    </div>
                </div>
            </div>
        </main>

        <!-- Footer -->
        <footer class="relative z-10 border-t border-slate-800/60 bg-[#070d18]/80 py-4 text-center text-xs text-slate-400 backdrop-blur-md">
            &copy; {{ new Date().getFullYear() }} TapakLokal. Seluruh hak cipta dilindungi. Portal Operasional & Administrasi Internal.
        </footer>
    </div>
</template>
