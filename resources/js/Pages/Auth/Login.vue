<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import { route } from 'ziggy-js';
import {
    ArrowLeft,
    CheckCircle2,
    Eye,
    EyeOff,
    Lock,
    LogIn,
    Mail,
    ShieldCheck,
    Sparkles
} from 'lucide-vue-next';
import { ref } from 'vue';

const props = defineProps({
    status: { type: String, default: null },
    error: { type: String, default: null },
    googleConfigured: { type: Boolean, default: false },
});

const showPassword = ref(false);

const form = useForm({
    email: '',
    password: '',
    remember: true,
});

const submit = () => {
    form.post(route('login.attempt'), {
        onFinish: () => form.reset('password'),
    });
};
</script>

<template>
    <Head title="Masuk ke Akun - TapakLokal" />

    <div class="relative min-h-screen overflow-x-hidden bg-[#f5f7fb] font-sans text-slate-900 selection:bg-[#3E7BEF] selection:text-white">
        <!-- Ambient background gradient glows matching Hero -->
        <div class="pointer-events-none absolute -top-20 left-1/2 -z-10 h-[700px] w-screen -translate-x-1/2 bg-[radial-gradient(ellipse_at_30%_20%,rgba(62,123,239,0.18),transparent_50%),radial-gradient(ellipse_at_75%_35%,rgba(154,211,255,0.25),transparent_45%)]" aria-hidden="true"></div>

        <!-- Top brand bar -->
        <header class="mx-auto flex h-16 max-w-[1180px] items-center justify-between px-5 sm:px-8">
            <Link href="/" class="group flex items-center gap-2 text-xs font-semibold text-slate-600 transition hover:text-[#3E7BEF]">
                <ArrowLeft class="size-4 transition-transform group-hover:-translate-x-1" />
                <span>Kembali ke Beranda</span>
            </Link>

            <Link href="/" class="text-left" aria-label="TapakLokal Beranda">
                <span class="text-xl font-extrabold tracking-[-0.07em] text-slate-900">
                    tapa<span class="text-[#3E7BEF]">k</span>lokal
                </span>
            </Link>
        </header>

        <!-- Main Card Container -->
        <main class="mx-auto flex max-w-[1180px] items-center justify-center px-4 py-8 sm:px-6 lg:py-12">
            <div class="w-full max-w-[460px]">
                <!-- Auth Box -->
                <div class="overflow-hidden rounded-3xl border border-[#dce5f0] bg-white p-7 shadow-[0_12px_36px_rgba(24,54,96,0.06)] sm:p-9">
                    <!-- Header -->
                    <div class="text-center">
                        <div class="inline-flex size-12 items-center justify-center rounded-2xl bg-[#edf3ff] text-[#3E7BEF] shadow-sm">
                            <LogIn class="size-6" />
                        </div>
                        <h1 class="mt-4 text-2xl font-extrabold tracking-tight text-[#172c50]">
                            Masuk ke Akun
                        </h1>
                        <p class="mt-1.5 text-xs text-slate-500">
                            Lanjutkan eksplorasi destinasi dan komunitas lokalmu.
                        </p>
                    </div>

                    <!-- Alerts / Flash Messages -->
                    <div v-if="status" class="mt-5 flex items-start gap-2.5 rounded-xl border border-emerald-200 bg-emerald-50/80 p-3 text-xs text-emerald-800">
                        <CheckCircle2 class="size-4 shrink-0 text-emerald-600" />
                        <span>{{ status }}</span>
                    </div>

                    <div v-if="error" class="mt-5 rounded-xl border border-rose-200 bg-rose-50 p-3 text-xs text-rose-700">
                        {{ error }}
                    </div>

                    <!-- Google OAuth Section -->
                    <div class="mt-6">
                        <a
                            :href="route('auth.google')"
                            class="group relative flex h-11 w-full items-center justify-center gap-3 rounded-xl border border-[#dce5f0] bg-white px-4 text-xs font-bold text-slate-700 shadow-sm transition hover:border-[#3E7BEF] hover:bg-[#edf3ff] hover:text-[#3E7BEF] active:scale-[0.99]"
                        >
                            <!-- Official Google "G" Icon -->
                            <svg class="size-4 shrink-0" viewBox="0 0 24 24">
                                <path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z" />
                                <path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z" />
                                <path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.06H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.94l2.85-2.22.81-.63z" />
                                <path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.06l3.66 2.84c.87-2.6 3.3-4.52 6.16-4.52z" />
                            </svg>
                            <span>Lanjutkan dengan Google</span>
                        </a>

                        <p v-if="!googleConfigured" class="mt-2 text-center text-[10px] text-amber-600">
                            💡 Mode pengembang: Pastikan Client ID Google sudah disetel di <code class="rounded bg-amber-100 px-1 py-0.5 font-mono">.env</code>.
                        </p>
                    </div>

                    <!-- Divider -->
                    <div class="my-6 flex items-center gap-3 text-slate-400">
                        <span class="h-px flex-1 bg-[#e4ecf6]"></span>
                        <span class="text-[11px] font-semibold text-slate-400">atau masuk dengan email</span>
                        <span class="h-px flex-1 bg-[#e4ecf6]"></span>
                    </div>

                    <!-- Form Login Email -->
                    <form @submit.prevent="submit" class="space-y-4">
                        <!-- Email Input -->
                        <div>
                            <label for="email" class="block text-xs font-bold text-[#172c50]">
                                Alamat Email
                            </label>
                            <div class="relative mt-1.5">
                                <Mail class="pointer-events-none absolute left-3.5 top-1/2 size-4 -translate-y-1/2 text-slate-400" />
                                <input
                                    id="email"
                                    v-model="form.email"
                                    type="email"
                                    required
                                    autofocus
                                    autocomplete="username"
                                    placeholder="nama@email.com"
                                    class="h-11 w-full rounded-xl border border-[#dce5f0] bg-[#f8fbfe] pl-10 pr-3.5 text-xs text-slate-800 transition placeholder:text-slate-400 focus:border-[#3E7BEF] focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#3E7BEF]/20"
                                    :class="{ 'border-rose-400 focus:border-rose-500 focus:ring-rose-200': form.errors.email }"
                                />
                            </div>
                            <p v-if="form.errors.email" class="mt-1 text-[11px] font-medium text-rose-600">
                                {{ form.errors.email }}
                            </p>
                        </div>

                        <!-- Password Input -->
                        <div>
                            <div class="flex items-center justify-between">
                                <label for="password" class="block text-xs font-bold text-[#172c50]">
                                    Kata Sandi
                                </label>
                                <a href="#" class="text-[11px] font-semibold text-[#3E7BEF] hover:underline">
                                    Lupa sandi?
                                </a>
                            </div>
                            <div class="relative mt-1.5">
                                <Lock class="pointer-events-none absolute left-3.5 top-1/2 size-4 -translate-y-1/2 text-slate-400" />
                                <input
                                    id="password"
                                    v-model="form.password"
                                    :type="showPassword ? 'text' : 'password'"
                                    required
                                    autocomplete="current-password"
                                    placeholder="••••••••"
                                    class="h-11 w-full rounded-xl border border-[#dce5f0] bg-[#f8fbfe] pl-10 pr-10 text-xs text-slate-800 transition placeholder:text-slate-400 focus:border-[#3E7BEF] focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#3E7BEF]/20"
                                    :class="{ 'border-rose-400 focus:border-rose-500 focus:ring-rose-200': form.errors.password }"
                                />
                                <button
                                    type="button"
                                    @click="showPassword = !showPassword"
                                    class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600"
                                    :aria-label="showPassword ? 'Sembunyikan sandi' : 'Tampilkan sandi'"
                                >
                                    <EyeOff v-if="showPassword" class="size-4" />
                                    <Eye v-else class="size-4" />
                                </button>
                            </div>
                            <p v-if="form.errors.password" class="mt-1 text-[11px] font-medium text-rose-600">
                                {{ form.errors.password }}
                            </p>
                        </div>

                        <!-- Remember Me -->
                        <div class="flex items-center">
                            <label class="flex cursor-pointer items-center gap-2 text-xs font-medium text-slate-600">
                                <input
                                    v-model="form.remember"
                                    type="checkbox"
                                    class="size-4 rounded border-slate-300 text-[#3E7BEF] focus:ring-[#3E7BEF]"
                                />
                                <span>Ingat saya di perangkat ini</span>
                            </label>
                        </div>

                        <!-- Submit Button -->
                        <button
                            type="submit"
                            :disabled="form.processing"
                            class="inline-flex h-11 w-full items-center justify-center gap-2 rounded-xl bg-[#3E7BEF] px-4 text-xs font-bold text-white shadow-[0_4px_12px_rgba(62,123,239,0.25)] transition hover:bg-[#2e6fe6] active:scale-[0.99] disabled:opacity-60"
                        >
                            <span v-if="form.processing">Memproses...</span>
                            <span v-else>Masuk Sekarang</span>
                        </button>
                    </form>

                    <!-- Footer prompt inside card -->
                    <div class="mt-6 border-t border-slate-100 pt-5 text-center text-xs text-slate-500">
                        Belum punya akun?
                        <a href="#" class="font-bold text-[#3E7BEF] hover:underline">
                            Daftar gratis
                        </a>
                    </div>
                </div>

                <!-- Trust and security badge -->
                <div class="mt-5 flex items-center justify-center gap-4 text-center text-[11px] text-slate-400">
                    <span class="flex items-center gap-1">
                        <ShieldCheck class="size-3.5 text-[#3E7BEF]" />
                        <span>Enkripsi SSL 256-bit</span>
                    </span>
                    <span>•</span>
                    <span class="flex items-center gap-1">
                        <Sparkles class="size-3.5 text-amber-500" />
                        <span>100% Aman & Tepercaya</span>
                    </span>
                </div>
            </div>
        </main>
    </div>
</template>
