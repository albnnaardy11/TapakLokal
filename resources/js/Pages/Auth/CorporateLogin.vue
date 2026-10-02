<script setup>
import { computed, ref } from 'vue';
import { Head, Link, useForm, usePage } from '@inertiajs/vue3';
import { route } from 'ziggy-js';
import { ArrowRight, Eye, EyeOff, Mail, LockKeyhole, UserRound } from 'lucide-vue-next';

const props = defineProps({ destination: { type: String, default: 'dashboard' } });
const page = usePage();
const mode = ref('login');
const showPassword = ref(false);
const form = useForm({ name: '', email: '', password: '', password_confirmation: '', remember: false, consent: false, next: props.destination });
const title = computed(() => mode.value === 'register' ? 'Buat akun corporate' : mode.value === 'forgot' ? 'Pulihkan akses akun' : 'Selamat datang kembali');
const googleUrl = computed(() => route('auth.socialite.redirect', {provider:'google', role:'corporate', intended: props.destination === 'register' ? '/corporate/register' : '/corporate/dashboard'}));
function changeMode(value) { mode.value = value; form.clearErrors(); form.reset('password', 'password_confirmation'); }
function submit() {
    const endpoint = mode.value === 'register' ? 'corporate.account.store' : mode.value === 'forgot' ? 'password.email' : 'corporate.login.store';
    form.post(route(endpoint), {onFinish: () => form.reset('password', 'password_confirmation')});
}
</script>
<template>
    <Head title="Masuk Corporate — TapakLokal" />
    <div class="corporate-auth min-h-screen bg-[#f5f9fd] text-[#173451]">
        <header class="border-b border-[#e4edf5] bg-white"><div class="mx-auto flex max-w-[1040px] items-center justify-between gap-4 px-5 py-4 sm:px-8"><Link :href="route('business.corporate')" class="flex items-center gap-3"><span class="text-2xl font-extrabold tracking-tight">tapak<span class="text-[#0088ff]">lokal</span></span><span class="border-l border-slate-200 pl-3 text-[9px] font-bold tracking-[.16em]">FOR<br />CORPORATES</span></Link><Link :href="route('business.corporate')" class="group inline-flex items-center gap-2 rounded-full border border-[#0088ff] bg-[#f0f7ff] px-4 py-1.5 text-xs font-semibold text-[#0088ff] transition-all duration-200 ease-in-out hover:border-[#0088ff] hover:bg-[#0088ff] hover:text-white active:scale-[0.98] sm:text-sm"><span>Tentang corporate</span><ArrowRight :stroke-width="2.5" class="size-3.5 transition-transform duration-200 ease-in-out group-hover:translate-x-1" /></Link></div></header>
        <main class="mx-auto flex max-w-[1000px] items-center px-5 py-6 sm:px-8 lg:py-10">
            <div class="w-full overflow-hidden rounded-[28px] border border-[#e3ecf5] bg-white shadow-[0_20px_60px_-20px_rgba(38,91,140,.18)] sm:rounded-[32px] lg:grid lg:grid-cols-[1.1fr_1fr]">
                <aside class="hidden flex-col justify-center border-b border-[#e4edf5] bg-gradient-to-br from-[#eaf4fd] via-[#f4f9fd] to-[#e6f1fb] p-8 sm:p-10 lg:flex lg:border-b-0 lg:border-r">
                    <div>
                        <h1 class="text-[36px] font-bold leading-[1.15] tracking-[-.035em] lg:text-[38px]">Rencana timmu.<br /><span class="text-[#0088ff]">Kami bantu wujudkan.</span></h1>
                        <p class="mt-3.5 text-sm leading-6 text-[#6b829e]">Dari gathering sampai perjalanan kerja. Atur kebutuhan, persetujuan, dan pembayaran bersama tim dalam satu tempat.</p>
                    </div>
                    <div class="mt-6 flex justify-center">
                        <img src="/Assets/Images/Corporate/login.svg" alt="Ilustrasi login corporate" class="h-[210px] w-full max-w-[320px] object-contain" />
                    </div>
                </aside>
                <section class="p-6 sm:p-8 lg:p-10" aria-labelledby="corporate-auth-heading">
                    <div class="mb-5">
                        <p class="text-[10px] font-bold uppercase tracking-[.15em] text-[#228cd1]">Akun corporate</p>
                        <h2 id="corporate-auth-heading" class="mt-1.5 text-[23px] font-bold leading-[1.2] tracking-tight">{{ title }}</h2>
                        <p class="mt-2 text-xs leading-5 text-[#7186a0]">{{ mode==='register' ? 'Mulai kelola perusahaan atau terima undangan tim dengan akunmu.' : mode==='forgot' ? 'Masukkan email akun untuk menerima tautan pemulihan kata sandi.' : 'Masuk untuk melanjutkan rencana perjalanan perusahaanmu.' }}</p>
                    </div>
                    <div>
                        <p v-if="page.props.flash?.success" role="status" class="mb-4 rounded-xl bg-emerald-50 p-3.5 text-sm text-emerald-800">{{ page.props.flash.success }}</p>
                        <a v-if="mode==='login'" :href="googleUrl" class="flex min-h-[44px] items-center rounded-full border border-[#dfe7f0] bg-white px-5 shadow-[0_3px_10px_rgba(20,55,80,.04)] transition hover:border-[#8ccaff] hover:bg-[#f9fcff]">
                            <svg aria-hidden="true" viewBox="0 0 24 24" class="size-[22px] shrink-0"><path fill="#4285F4" d="M21.6 12.23c0-.71-.06-1.39-.18-2.05H12v3.88h5.38a4.6 4.6 0 0 1-2 3.01v2.5h3.24c1.9-1.75 2.98-4.33 2.98-7.34Z"/><path fill="#34A853" d="M12 22c2.7 0 4.96-.9 6.62-2.43l-3.24-2.5c-.9.6-2.05.97-3.38.97-2.6 0-4.81-1.76-5.6-4.12H3.06v2.58A10 10 0 0 0 12 22Z"/><path fill="#FBBC05" d="M6.4 13.92a6 6 0 0 1 0-3.84V7.5H3.06a10 10 0 0 0 0 9l3.34-2.58Z"/><path fill="#EA4335" d="M12 5.96c1.47 0 2.78.5 3.82 1.49l2.86-2.87A9.6 9.6 0 0 0 12 2a10 10 0 0 0-8.94 5.5l3.34 2.58A5.98 5.98 0 0 1 12 5.96Z"/></svg>
                            <span class="ml-4 flex-1 border-l border-slate-100 pl-4 text-center text-sm font-bold">Lanjutkan dengan Google</span>
                        </a>
                        <div v-if="mode==='login'" class="my-4 flex items-center gap-3 text-[11px] text-[#8ca0b7]"><span class="h-px flex-1 bg-[#e6edf5]"></span>atau masuk dengan email<span class="h-px flex-1 bg-[#e6edf5]"></span></div>
                        <form class="space-y-3" @submit.prevent="submit">
                            <label v-if="mode==='register'" class="auth-label">Nama lengkap<span class="auth-field"><UserRound class="auth-field-icon" /><input v-model="form.name" required maxlength="100" autocomplete="name" placeholder="Nama lengkap kamu" /></span></label>
                            <label class="auth-label">Email akun<span class="auth-field"><Mail class="auth-field-icon" /><input v-model="form.email" type="email" required autocomplete="username" placeholder="nama@perusahaan.com" /></span></label>
                            <label v-if="mode!=='forgot'" class="auth-label">Kata sandi<span class="auth-field"><LockKeyhole class="auth-field-icon" /><input v-model="form.password" :type="showPassword?'text':'password'" required :minlength="mode==='register'?10:undefined" maxlength="128" :autocomplete="mode==='register'?'new-password':'current-password'" placeholder="Masukkan kata sandi" /><button type="button" class="grid size-10 shrink-0 place-items-center rounded-lg text-[#7c92ac] hover:text-[#0088ff]" :aria-label="showPassword?'Sembunyikan kata sandi':'Tampilkan kata sandi'" @click="showPassword=!showPassword"><component :is="showPassword?EyeOff:Eye" class="size-[18px]" /></button></span></label>
                            <label v-if="mode==='register'" class="auth-label">Ulangi kata sandi<span class="auth-field"><LockKeyhole class="auth-field-icon" /><input v-model="form.password_confirmation" type="password" required minlength="10" maxlength="128" autocomplete="new-password" placeholder="Ketik ulang kata sandi" /></span><span class="mt-2 block text-[11px] font-normal text-[#7c92ac]">Minimal 10 karakter, dengan huruf dan angka.</span></label>
                            <div v-if="mode==='login'" class="flex flex-wrap items-center justify-between gap-3 py-1 text-xs"><label class="flex cursor-pointer items-center gap-2 text-[#6b829e]"><input v-model="form.remember" type="checkbox" class="size-4 accent-[#0088ff]" />Ingat saya</label><button type="button" class="font-semibold text-[#0088ee]" @click="changeMode('forgot')">Lupa kata sandi?</button></div>
                            <label v-if="mode==='register'" class="flex items-start gap-3 text-xs leading-6 text-[#6b829e]"><input v-model="form.consent" required type="checkbox" class="mt-1 size-4 shrink-0 accent-[#0088ff]" /><span>Saya menyatakan data akun benar dan mengizinkan penggunaannya untuk pendaftaran serta akses layanan corporate.</span></label>
                            <p v-for="(error,key) in form.errors" :key="key" role="alert" class="rounded-lg bg-red-50 px-3 py-2 text-xs leading-5 text-red-700">{{ error }}</p>
                            <button :disabled="form.processing" class="flex min-h-[44px] w-full items-center justify-center gap-2 rounded-full bg-[#0088ff] px-5 py-3 text-sm font-bold text-white shadow-[0_5px_15px_-5px_rgba(0,136,255,.4)] transition hover:bg-[#0078e2] disabled:cursor-wait disabled:opacity-60">{{ form.processing?'Memproses…':mode==='register'?'Buat akun & lanjutkan':mode==='forgot'?'Kirim tautan pemulihan':'Masuk corporate' }}<ArrowRight class="size-4" /></button>
                        </form>
                        <p class="mt-4 text-center text-xs leading-6 text-[#7c92ac]">{{ mode==='login'?'Belum punya akun?':'Sudah punya akun?' }} <button type="button" class="font-bold text-[#0088ee]" @click="changeMode(mode==='login'?'register':'login')">{{ mode==='login'?'Daftar akun corporate':'Masuk corporate' }}</button></p>
                    </div>
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
.corporate-auth button:focus-visible, .corporate-auth a:focus-visible { outline: 2px solid #0088ff; outline-offset: 3px; }
</style>

