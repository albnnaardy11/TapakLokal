<script setup>
import { Head, Link, useForm, usePage } from '@inertiajs/vue3';
import { computed, ref } from 'vue';
import { route } from 'ziggy-js';
import {
    ArrowRight,
    CheckCircle2,
    Compass,
    Eye,
    EyeOff,
    Heart,
    Lock,
    MapPin,
    Shield,
    Sparkles,
    User,
    UserCheck,
} from 'lucide-vue-next';

const props = defineProps({
    mode: { type: String, default: 'login' },
    token: String,
    email: String,
});

const page = usePage();
const showPassword = ref(false);

const form = useForm({
    name: '',
    email: props.email || '',
    password: '',
    password_confirmation: '',
    remember: false,
    token: props.token || '',
});

const title = computed(() => {
    if (props.mode === 'register') return 'Mulai Petualanganmu.';
    if (props.mode === 'forgot') return 'Pulihkan Kata Sandi.';
    if (props.mode === 'reset') return 'Buat Kata Sandi Baru.';
    return 'Selamat Datang, Penjelajah!';
});

const subtitle = computed(() => {
    if (props.mode === 'register') return 'Daftar sekarang untuk memesan open trip impian dan jelajahi surga tersembunyi Indonesia.';
    if (props.mode === 'forgot') return 'Masukkan email akun wisatawan Anda untuk menerima instruksi pemulihan kata sandi.';
    if (props.mode === 'reset') return 'Tentukan kata sandi baru yang kuat untuk akun wisatawan Anda.';
    return 'Masuk ke akun wisatawan Anda untuk melihat tiket perjalanan, simpan wishlist, dan review destinasi favorit.';
});

const submit = () => {
    form.post(
        route(
            {
                login: 'login.store',
                register: 'register.store',
                forgot: 'password.email',
                reset: 'password.update',
            }[props.mode],
        ),
        {
            onFinish: () => form.reset('password', 'password_confirmation'),
        },
    );
};
</script>

<template>
    <Head :title="title" />
    <div class="grid min-h-screen bg-[#f8fafc] lg:grid-cols-[1.15fr_1fr]">
        <!-- Left Branding Showcase Panel -->
        <section class="relative hidden flex-col justify-between overflow-hidden bg-[#0d2238] p-12 lg:p-16 text-white lg:flex">
            <!-- Background Image & Gradient overlay -->
            <img
                src="https://images.unsplash.com/photo-1507525428034-b723cf961d3e?auto=format&fit=crop&w=1600&q=85"
                alt="Pantai Eksotis Indonesia"
                class="absolute inset-0 -z-20 size-full object-cover opacity-40 scale-105 transition duration-1000"
            />
            <div class="absolute inset-0 -z-10 bg-gradient-to-t from-[#071727] via-[#0d253f]/85 to-[#0e3a63]/75"></div>

            <!-- Top Logo -->
            <Link href="/" class="flex items-center gap-3 text-2xl font-black tracking-tight text-white group">
                <span class="grid size-11 place-items-center rounded-2xl bg-gradient-to-br from-[#078cff] to-[#0060df] text-white shadow-xl shadow-blue-500/30 group-hover:scale-105 transition">
                    <Compass class="size-6" />
                </span>
                <span>tapak<span class="text-[#38bdf8]">lokal</span></span>
            </Link>

            <!-- Center Value Prop -->
            <div class="max-w-md py-8">
                <span class="inline-flex items-center gap-1.5 rounded-full border border-sky-400/30 bg-sky-500/15 px-3.5 py-1 text-xs font-bold tracking-wide text-sky-200 backdrop-blur-md">
                    <Sparkles class="size-3.5 text-sky-300" />
                    PORTAL WISATAWAN & PENJELAJAH
                </span>

                <h1 class="mt-6 text-4xl font-black leading-[1.18] tracking-tight lg:text-5xl text-white">
                    Jelajahi Negeri.<br />
                    <span class="bg-gradient-to-r from-sky-300 via-teal-200 to-amber-200 bg-clip-text text-transparent">Dukung Masyarakat Lokal.</span>
                </h1>

                <p class="mt-5 text-sm leading-relaxed text-slate-200">
                    Temukan ratusan destinasi autentik, open trip berpemandu lokal bersertifikat, dan tur 360° interaktif di seluruh kepulauan Nusantara.
                </p>

                <!-- Perks list -->
                <div class="mt-8 space-y-3 text-xs text-slate-200">
                    <div class="flex items-center gap-3">
                        <div class="grid size-6 place-items-center rounded-full bg-sky-500/20 text-sky-300 border border-sky-500/30">
                            <CheckCircle2 class="size-3.5" />
                        </div>
                        <span>Tiket & e-voucher instan tanpa antre</span>
                    </div>
                    <div class="flex items-center gap-3">
                        <div class="grid size-6 place-items-center rounded-full bg-sky-500/20 text-sky-300 border border-sky-500/30">
                            <CheckCircle2 class="size-3.5" />
                        </div>
                        <span>Pemandu lokal ramah, berpengalaman & terverifikasi</span>
                    </div>
                    <div class="flex items-center gap-3">
                        <div class="grid size-6 place-items-center rounded-full bg-sky-500/20 text-sky-300 border border-sky-500/30">
                            <CheckCircle2 class="size-3.5" />
                        </div>
                        <span>Garansi pembayaran aman melalui Midtrans Gateway</span>
                    </div>
                </div>
            </div>

            <!-- Bottom Footnote -->
            <div class="flex items-center justify-between border-t border-white/10 pt-6 text-xs text-slate-300">
                <p class="flex items-center gap-2">
                    <MapPin class="size-4 text-sky-400" />
                    <span>Jelajahi Sabang sampai Merauke</span>
                </p>
                <p>&copy; {{ new Date().getFullYear() }} TapakLokal</p>
            </div>
        </section>

        <!-- Right Form Panel -->
        <main class="flex items-center justify-center px-6 py-12 sm:px-10 lg:px-14">
            <div class="w-full max-w-md">
                <!-- Mobile Logo -->
                <Link href="/" class="mb-8 flex items-center gap-2.5 text-xl font-black text-slate-900 lg:hidden">
                    <span class="grid size-9 place-items-center rounded-xl bg-blue-600 text-white shadow-md">
                        <Compass class="size-5" />
                    </span>
                    <span>tapak<span class="text-blue-600">lokal</span></span>
                </Link>

                <!-- Header Title & Subtitle -->
                <div>
                    <span v-if="mode === 'login'" class="inline-flex items-center gap-1.5 rounded-full bg-blue-50 px-3 py-1 text-xs font-bold text-blue-600 mb-3">
                        <User class="size-3.5" />
                        Akun Wisatawan
                    </span>
                    <h2 class="text-2xl sm:text-3xl font-black tracking-tight text-slate-900">
                        {{ title }}
                    </h2>
                    <p class="mt-2 text-xs sm:text-sm font-medium leading-relaxed text-slate-500">
                        {{ subtitle }}
                    </p>
                </div>

                <!-- Success Flash Alert -->
                <div
                    v-if="page.props.flash?.success"
                    role="status"
                    class="mt-5 rounded-xl border border-emerald-200 bg-emerald-50 p-3.5 text-xs font-semibold text-emerald-800"
                >
                    {{ page.props.flash.success }}
                </div>

                <!-- Error Flash Alert -->
                <div
                    v-if="page.props.flash?.error"
                    role="alert"
                    class="mt-5 rounded-xl border border-rose-200 bg-rose-50 p-3.5 text-xs font-semibold text-rose-800"
                >
                    {{ page.props.flash.error }}
                </div>

                <!-- Authentication Form -->
                <form class="mt-6 space-y-4" @submit.prevent="submit">
                    <!-- Name (Register Only) -->
                    <div v-if="mode === 'register'">
                        <label class="mb-1.5 block text-xs font-bold text-slate-700">Nama Lengkap</label>
                        <input
                            v-model="form.name"
                            autocomplete="name"
                            required
                            placeholder="Contoh: Budi Santoso"
                            class="w-full rounded-xl border border-slate-200 bg-white px-3.5 py-2.5 text-xs font-medium text-slate-800 outline-none transition focus:border-blue-500 focus:ring-2 focus:ring-blue-100"
                        />
                    </div>

                    <!-- Email -->
                    <div>
                        <label class="mb-1.5 block text-xs font-bold text-slate-700">Alamat Email</label>
                        <input
                            v-model="form.email"
                            type="email"
                            autocomplete="email"
                            required
                            placeholder="nama@email.com"
                            class="w-full rounded-xl border border-slate-200 bg-white px-3.5 py-2.5 text-xs font-medium text-slate-800 outline-none transition focus:border-blue-500 focus:ring-2 focus:ring-blue-100"
                        />
                    </div>

                    <!-- Password -->
                    <div v-if="mode !== 'forgot'">
                        <div class="mb-1.5 flex items-center justify-between">
                            <label class="text-xs font-bold text-slate-700">Kata Sandi</label>
                            <Link
                                v-if="mode === 'login'"
                                :href="route('password.request')"
                                class="text-[11px] font-bold text-blue-600 hover:text-blue-700"
                            >
                                Lupa kata sandi?
                            </Link>
                        </div>
                        <div class="relative">
                            <input
                                v-model="form.password"
                                :type="showPassword ? 'text' : 'password'"
                                :autocomplete="mode === 'login' ? 'current-password' : 'new-password'"
                                required
                                placeholder="••••••••"
                                class="w-full rounded-xl border border-slate-200 bg-white px-3.5 py-2.5 pr-10 text-xs font-medium text-slate-800 outline-none transition focus:border-blue-500 focus:ring-2 focus:ring-blue-100"
                            />
                            <button
                                type="button"
                                class="absolute right-3 top-2.5 text-slate-400 hover:text-slate-600"
                                aria-label="Lihat kata sandi"
                                @click="showPassword = !showPassword"
                            >
                                <EyeOff v-if="showPassword" class="size-4" />
                                <Eye v-else class="size-4" />
                            </button>
                        </div>
                        <span v-if="mode !== 'login'" class="mt-1.5 block text-[11px] font-normal text-slate-400">
                            Minimal 10 karakter, berisi huruf dan angka.
                        </span>
                    </div>

                    <!-- Password Confirmation (Register & Reset) -->
                    <div v-if="['register', 'reset'].includes(mode)">
                        <label class="mb-1.5 block text-xs font-bold text-slate-700">Konfirmasi Kata Sandi</label>
                        <input
                            v-model="form.password_confirmation"
                            type="password"
                            autocomplete="new-password"
                            required
                            placeholder="••••••••"
                            class="w-full rounded-xl border border-slate-200 bg-white px-3.5 py-2.5 text-xs font-medium text-slate-800 outline-none transition focus:border-blue-500 focus:ring-2 focus:ring-blue-100"
                        />
                    </div>

                    <!-- Remember Me (Login Only) -->
                    <div v-if="mode === 'login'" class="flex items-center text-xs">
                        <label class="flex items-center gap-2 text-slate-600 cursor-pointer">
                            <input
                                v-model="form.remember"
                                type="checkbox"
                                class="rounded border-slate-300 text-blue-600 focus:ring-blue-500"
                            />
                            <span>Ingat saya di perangkat ini</span>
                        </label>
                    </div>

                    <!-- Validation Errors -->
                    <div v-if="Object.keys(form.errors).length" class="space-y-1 rounded-xl bg-rose-50 p-3 text-xs font-semibold text-rose-600">
                        <p v-for="(error, key) in form.errors" :key="key">{{ error }}</p>
                    </div>

                    <!-- Submit Button -->
                    <button
                        :disabled="form.processing"
                        class="mt-2 flex w-full items-center justify-center gap-2 rounded-xl bg-gradient-to-r from-[#1677e8] to-[#078cff] py-3 text-xs font-bold text-white shadow-lg shadow-blue-500/25 transition hover:opacity-95 active:scale-95 disabled:opacity-50"
                    >
                        <span>{{ form.processing ? 'Memproses…' : mode === 'login' ? 'Masuk Sekarang' : mode === 'register' ? 'Buat Akun Wisatawan' : 'Perbarui Kata Sandi' }}</span>
                        <ArrowRight class="size-4" />
                    </button>
                </form>

                <!-- Footer Switcher Link -->
                <div class="mt-8 text-center text-xs text-slate-500">
                    <p v-if="mode === 'login'">
                        Belum punya akun?
                        <Link :href="route('register')" class="font-bold text-blue-600 hover:underline">
                            Daftar sebagai wisatawan
                        </Link>
                    </p>
                    <p v-else-if="mode !== 'login'">
                        Sudah punya akun wisatawan?
                        <Link :href="route('login')" class="font-bold text-blue-600 hover:underline">
                            Kembali ke halaman masuk
                        </Link>
                    </p>

                    <!-- Subtle Internal Portals Link for convenience -->
                    <div class="mt-6 pt-6 border-t border-slate-200/80 flex items-center justify-center gap-4 text-[11px] text-slate-400">
                        <Link :href="route('vendor.login')" class="hover:text-emerald-600 transition">
                            Portal Mitra Vendor &rarr;
                        </Link>
                        <span>&bull;</span>
                        <Link :href="route('admin.login')" class="hover:text-slate-700 transition">
                            Portal Admin Platform &rarr;
                        </Link>
                    </div>
                </div>
            </div>
        </main>
    </div>
</template>
