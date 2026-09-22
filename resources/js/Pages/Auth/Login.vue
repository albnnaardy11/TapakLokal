<script setup>
import { Head, Link, useForm, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';
import { route } from 'ziggy-js';
import { Compass, ArrowRight, ShieldCheck } from 'lucide-vue-next';
const props = defineProps({ mode: { type: String, default: 'login' }, token: String, email: String });
const page = usePage();
const form = useForm({ name: '', email: props.email || '', password: '', password_confirmation: '', remember: false, token: props.token || '' });
const title = computed(() => ({ login: 'Selamat datang kembali.', register: 'Mulai perjalananmu.', forgot: 'Pulihkan akunmu.', reset: 'Buat kata sandi baru.' }[props.mode]));
const submit = () => form.post(route({ login: 'login.store', register: 'register.store', forgot: 'password.email', reset: 'password.update' }[props.mode]), { onFinish: () => form.reset('password', 'password_confirmation') });
</script>
<template>
    <Head :title="title" />
    <div class="grid min-h-screen bg-[#f7f9fc] lg:grid-cols-2">
        <section class="relative hidden flex-col justify-between overflow-hidden bg-[#173d6b] p-14 text-white lg:flex">
            <Link href="/" class="flex items-center gap-3 text-xl font-bold"><Compass class="size-8" />tapaklokal</Link>
            <div><span class="rounded-full border border-white/20 px-3 py-1.5 text-xs tracking-wide">TEMAN PERJALANAN LOKAL</span><h1 class="mt-7 max-w-lg text-5xl font-semibold leading-tight tracking-tight">Perjalanan bermakna.<br />Dampak untuk lokal.</h1><p class="mt-6 max-w-sm text-base leading-7 text-blue-100/75">Satu tempat untuk wisatawan, mitra perjalanan, dan tim yang menghubungkan semuanya.</p></div>
            <p class="flex items-center gap-2 text-xs text-blue-100/70"><ShieldCheck class="size-4" />Akses sesuai peran, dalam satu ekosistem.</p>
        </section>
        <main class="flex items-center justify-center px-6 py-14">
            <div class="w-full max-w-sm"><Link href="/" class="mb-12 flex items-center gap-2 text-lg font-bold text-blue-600 lg:hidden"><Compass />tapaklokal</Link><h2 class="text-3xl font-bold tracking-tight text-[#17345e]">{{ title }}</h2><p class="mt-3 text-sm leading-6 text-slate-500">{{ mode === 'login' ? 'Masuk untuk mengelola perjalanan atau workspace kamu.' : 'Gunakan email aktif dan kata sandi yang aman.' }}</p>
                <p v-if="page.props.flash?.success" role="status" class="mt-5 rounded-xl bg-emerald-50 p-3 text-sm text-emerald-700">{{ page.props.flash.success }}</p>
                <form class="mt-8 space-y-5" @submit.prevent="submit">
                    <label v-if="mode === 'register'" class="block text-xs font-semibold text-slate-600">Nama lengkap<input v-model="form.name" autocomplete="name" required class="panel-input mt-2" /></label>
                    <label class="block text-xs font-semibold text-slate-600">Alamat email<input v-model="form.email" type="email" autocomplete="email" required class="panel-input mt-2" /></label>
                    <label v-if="mode !== 'forgot'" class="block text-xs font-semibold text-slate-600">Kata sandi<input v-model="form.password" type="password" :autocomplete="mode === 'login' ? 'current-password' : 'new-password'" required class="panel-input mt-2" /><span v-if="mode !== 'login'" class="mt-2 block font-normal text-slate-400">Minimal 10 karakter, berisi huruf dan angka.</span></label>
                    <label v-if="['register', 'reset'].includes(mode)" class="block text-xs font-semibold text-slate-600">Konfirmasi kata sandi<input v-model="form.password_confirmation" type="password" autocomplete="new-password" required class="panel-input mt-2" /></label>
                    <div v-if="mode === 'login'" class="flex items-center justify-between text-xs"><label class="flex items-center gap-2 text-slate-500"><input v-model="form.remember" type="checkbox" />Ingat saya</label><Link :href="route('password.request')" class="font-semibold text-blue-600">Lupa kata sandi?</Link></div>
                    <p v-for="(error, key) in form.errors" :key="key" role="alert" class="text-xs text-rose-600">{{ error }}</p>
                    <button :disabled="form.processing" class="panel-primary w-full justify-between">{{ form.processing ? 'Memproses…' : mode === 'login' ? 'Masuk' : mode === 'register' ? 'Buat akun' : 'Lanjutkan' }}<ArrowRight class="size-4" /></button>
                </form>
                <p class="mt-7 text-center text-xs text-slate-500"><Link :href="route(mode === 'login' ? 'register' : 'login')" class="font-semibold text-blue-600">{{ mode === 'login' ? 'Belum punya akun? Daftar sebagai wisatawan' : 'Kembali ke halaman masuk' }}</Link></p>
            </div>
        </main>
    </div>
</template>

