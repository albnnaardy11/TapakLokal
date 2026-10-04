<script setup>
import { ref } from 'vue';
import { Head, Link, useForm, usePage } from '@inertiajs/vue3';
import { route } from 'ziggy-js';
import { ArrowRight, Eye, EyeOff, Mail, LockKeyhole } from 'lucide-vue-next';
const page = usePage();
const showPassword = ref(false);
const forgot = ref(false);
const form = useForm({ email: '', password: '', remember: false });
function changeMode() { forgot.value = !forgot.value; showPassword.value = false; form.clearErrors(); form.reset('password'); }
function submit() {
    form.post(route(forgot.value ? 'password.email' : 'vendor.login.store'), { onFinish: () => { form.reset('password'); showPassword.value = false; } });
}
</script>
<template>
    <Head title="Masuk Mitra — TapakLokal" />
    <div class="vendor-auth min-h-screen bg-[#f5f9fd] text-[#173451]">
        <header class="border-b border-[#e4edf5] bg-white"><div class="mx-auto flex max-w-[1040px] items-center justify-between gap-4 px-5 py-4 sm:px-8"><Link :href="route('business.partner')" class="flex items-center gap-3"><img src="/Assets/Images/logo.webp" alt="TapakLokal Logo" class="h-8 w-auto object-contain" /><span class="border-l border-slate-200 pl-3 text-[9px] font-bold tracking-[.16em]">FOR<br />PARTNERS</span></Link><Link :href="route('business.partner')" class="group inline-flex items-center gap-2 rounded-full border border-[#0088ff] bg-[#f0f7ff] px-4 py-1.5 text-xs font-semibold text-[#0088ff] transition hover:bg-[#0088ff] hover:text-white sm:text-sm">Tentang kemitraan<ArrowRight class="size-3.5 transition-transform group-hover:translate-x-1" /></Link></div></header>
        <main class="mx-auto flex max-w-[1000px] items-center px-5 py-6 sm:px-8 lg:py-10">
            <div class="w-full overflow-hidden rounded-[28px] border border-[#e3ecf5] bg-white shadow-[0_20px_60px_-20px_rgba(38,91,140,.18)] sm:rounded-[32px] lg:grid lg:grid-cols-[1.1fr_1fr]">
                <aside class="hidden flex-col justify-center border-r border-[#e4edf5] bg-linear-to-br from-[#eaf4fd] via-[#f4f9fd] to-[#e6f1fb] p-10 lg:flex">
                    <h1 class="text-[38px] font-bold leading-[1.15] tracking-[-.035em]">Usaha lokalmu.<br /><span class="text-[#0088ff]">Kita tumbuh bersama.</span></h1>
                    <p class="mt-3.5 text-sm leading-6 text-[#6b829e]">Kelola penawaran, jadwal perjalanan, dan pesanan pelanggan melalui satu portal mitra TapakLokal.</p>
                    <div class="mt-6 flex justify-center"><img src="/Assets/Images/Corporate/login.svg" alt="Ilustrasi akses portal bisnis TapakLokal" class="h-[210px] w-full max-w-[320px] object-contain" /></div>
                </aside>
                <section class="p-6 sm:p-8 lg:p-10" aria-labelledby="vendor-auth-heading">
                    <div class="mb-6"><p class="text-[10px] font-bold uppercase tracking-[.15em] text-[#228cd1]">Akun mitra vendor</p><h2 id="vendor-auth-heading" class="mt-1.5 text-[23px] font-bold leading-[1.2] tracking-tight">{{ forgot ? 'Pulihkan akses akun' : 'Selamat datang kembali' }}</h2><p class="mt-2 text-xs leading-5 text-[#7186a0]">{{ forgot ? 'Masukkan email akun untuk menerima tautan pemulihan kata sandi.' : 'Masuk untuk melanjutkan pengelolaan usaha dan perjalanan pelangganmu.' }}</p></div>
                    <p v-if="page.props.flash?.success" role="status" class="mb-4 rounded-xl bg-emerald-50 p-3.5 text-sm text-emerald-800">{{ page.props.flash.success }}</p>
                    <form class="space-y-4" @submit.prevent="submit">
                        <label class="auth-label">Email akun<span class="auth-field"><Mail class="auth-field-icon" /><input v-model="form.email" type="email" required maxlength="150" autocomplete="username" placeholder="nama@gmail.com" /></span></label>
                        <label v-if="!forgot" class="auth-label">Kata sandi<span class="auth-field"><LockKeyhole class="auth-field-icon" /><input v-model="form.password" :type="showPassword ? 'text' : 'password'" required maxlength="128" autocomplete="current-password" placeholder="Masukkan kata sandi" /><button type="button" class="grid size-10 shrink-0 place-items-center rounded-lg text-[#7c92ac] hover:text-[#0088ff]" :aria-label="showPassword ? 'Sembunyikan kata sandi' : 'Tampilkan kata sandi'" :aria-pressed="showPassword" @click="showPassword = !showPassword"><component :is="showPassword ? EyeOff : Eye" class="size-[18px]" /></button></span></label>
                        <div v-if="!forgot" class="flex flex-wrap items-center justify-between gap-3 py-1 text-xs"><label class="flex cursor-pointer items-center gap-2 text-[#6b829e]"><input v-model="form.remember" type="checkbox" class="size-4 accent-[#0088ff]" />Ingat saya</label><button type="button" class="font-semibold text-[#0088ee]" @click="changeMode">Lupa kata sandi?</button></div>
                        <p v-for="(error, key) in form.errors" :key="key" role="alert" class="rounded-lg bg-red-50 px-3 py-2 text-xs leading-5 text-red-700">{{ error }}</p>
                        <button :disabled="form.processing" class="flex min-h-[44px] w-full items-center justify-center gap-2 rounded-full bg-[#0088ff] px-5 py-3 text-sm font-bold text-white shadow-[0_5px_15px_-5px_rgba(0,136,255,.4)] transition hover:bg-[#0078e2] disabled:cursor-wait disabled:opacity-60">{{ form.processing ? 'Memproses…' : forgot ? 'Kirim tautan pemulihan' : 'Masuk portal mitra' }}<ArrowRight class="size-4" /></button>
                    </form>
                    <p class="mt-5 text-center text-xs leading-6 text-[#7c92ac]"><template v-if="forgot">Sudah ingat kata sandi? <button type="button" class="font-bold text-[#0088ee]" @click="changeMode">Kembali masuk</button></template><template v-else>Belum menjadi mitra? <Link :href="route('business.partner')" class="font-bold text-[#0088ee]">Ajukan kemitraan</Link></template></p>
                    <p class="mt-5 border-t border-[#e6edf5] pt-4 text-[11px] leading-5 text-[#7c92ac]">Gunakan akun yang didaftarkan saat pengajuan. Akses portal mitra tersedia setelah verifikasi admin.</p>
                </section>
            </div>
        </main>
    </div>
</template>
<style scoped>
@reference "../../../css/app.css";
.auth-label { @apply block text-xs font-semibold text-[#385675]; }
.auth-field { @apply mt-1.5 flex min-h-[44px] items-center gap-3 rounded-xl border border-[#dfe8f2] bg-[#fbfdff] px-3.5 transition focus-within:border-[#0088ff] focus-within:bg-white focus-within:ring-3 focus-within:ring-[#0088ff]/10; }
.auth-field-icon { @apply size-[18px] shrink-0 text-[#8daac4]; }
.auth-field input { @apply min-w-0 flex-1 border-0 bg-transparent py-2.5 text-[13px] font-normal text-[#173451] outline-none placeholder:text-[#a1b1c5]; }
.auth-field input::-ms-reveal { display: none; }
.vendor-auth button:focus-visible, .vendor-auth a:focus-visible { outline: 2px solid #0088ff; outline-offset: 3px; }
</style>
