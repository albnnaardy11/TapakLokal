<script setup>
import { computed, ref, watch } from 'vue';
import { ArrowLeft, CheckCircle2, Mail, Phone, X } from 'lucide-vue-next';

const props = defineProps({ open: { type: Boolean, default: false }, mode: { type: String, default: 'login' } });
const emit = defineEmits(['close']);
const screen = ref('choice');
const identifier = ref('');
const password = ref('');
const notice = ref('');

const isRegistering = computed(() => screen.value === 'register');
const isRegisterChoice = computed(() => screen.value === 'register-choice');

watch(() => props.open, (isOpen) => {
    if (isOpen) {
        screen.value = props.mode === 'register' ? 'register-choice' : 'choice';
        identifier.value = '';
        password.value = '';
        notice.value = '';
    }
});

const continueWithGoogle = () => {
    notice.value = 'Login Google siap dihubungkan ke autentikasi aplikasi.';
};

const submit = () => {
    notice.value = isRegistering.value ? 'Data pendaftaran siap dilanjutkan.' : 'Login berhasil diproses.';
};
</script>

<template>
    <Teleport to="body">
        <div v-if="open" class="fixed inset-0 z-[100] flex items-center justify-center bg-[#08213d]/55 p-4 backdrop-blur-[2px]" role="dialog" aria-modal="true" aria-labelledby="auth-heading" @click.self="emit('close')">
            <section class="w-full overflow-hidden bg-white shadow-[0_24px_70px_rgba(8,33,61,0.28)]" :class="isRegistering ? 'max-w-[600px] rounded-2xl' : 'max-w-[440px] rounded-3xl'">
                <template v-if="screen === 'choice' || screen === 'register-choice'">
                    <header class="relative overflow-hidden bg-[#e9f6ff] px-6 pb-7 pt-6 sm:px-8">
                        <div class="absolute -right-9 -top-10 size-36 rounded-full border-[18px] border-white/45"></div>
                        <button type="button" class="absolute right-4 top-4 grid size-9 place-items-center rounded-full text-[#55739b] hover:bg-white/70" aria-label="Tutup" @click="emit('close')"><X class="size-5" /></button>
                        <p class="text-[11px] font-bold uppercase tracking-[0.14em] text-[#1688e8]">TapakLokal</p>
                        <h2 id="auth-heading" class="mt-2 max-w-xs text-2xl font-extrabold leading-tight text-[#173b70]">{{ isRegisterChoice ? 'Buat akun untuk mulai menjelajah' : 'Masuk untuk mulai perjalananmu' }}</h2>
                        <p class="mt-2 text-sm text-[#60789c]">{{ isRegisterChoice ? 'Daftar cepat dengan Google atau gunakan nomor HP dan email.' : 'Simpan trip, kelola pesanan, dan dapatkan poin.' }}</p>
                    </header>
                    <div class="p-6 sm:p-8">
                        <button type="button" class="flex min-h-12 w-full items-center justify-center gap-3 rounded-xl border border-[#dce7f4] bg-white px-4 text-sm font-bold text-[#173b70] shadow-[0_5px_16px_rgba(23,75,120,0.07)] hover:border-[#9bc9f4]" @click="continueWithGoogle"><span class="grid size-6 place-items-center rounded-full bg-white text-base font-extrabold"><span class="text-[#4285f4]">G</span></span>{{ isRegisterChoice ? 'Daftar cepat dengan Google' : 'Lanjutkan dengan Google' }}</button>
                        <button type="button" class="mt-3 flex min-h-12 w-full items-center justify-center gap-3 rounded-xl border border-[#bfd9f4] bg-white px-4 text-sm font-bold text-[#1688e8] hover:bg-[#f6fbff]" @click="screen = isRegisterChoice ? 'register' : 'credentials'"><Mail class="size-5" />{{ isRegisterChoice ? 'Daftar dengan nomor HP atau email' : 'Lanjutkan dengan nomor HP atau email' }}</button>
                        <p v-if="notice" class="mt-4 flex items-start gap-2 rounded-xl bg-emerald-50 px-3 py-3 text-xs leading-5 text-emerald-700"><CheckCircle2 class="mt-0.5 size-4 shrink-0" />{{ notice }}</p>
                        <div class="my-6 flex items-center gap-3 text-[11px] text-[#8aa0b8]"><span class="h-px flex-1 bg-[#e7eff7]"></span>atau<span class="h-px flex-1 bg-[#e7eff7]"></span></div>
                        <p class="text-center text-sm text-[#60789c]"><template v-if="isRegisterChoice">Sudah punya akun? <button type="button" class="font-extrabold text-[#1688e8]" @click="screen = 'choice'">Masuk</button></template><template v-else>Belum punya akun? <button type="button" class="font-extrabold text-[#1688e8]" @click="screen = 'register-choice'">Daftar sekarang</button></template></p>
                        <p class="mt-6 text-center text-[11px] leading-5 text-[#7186a2]">Dengan melanjutkan, kamu menyetujui <a href="#" class="font-semibold text-[#1688e8]">Syarat & Ketentuan</a> serta <a href="#" class="font-semibold text-[#1688e8]">Kebijakan Privasi</a>.</p>
                    </div>
                </template>
                <template v-else>
                    <header class="flex items-center gap-3 border-b border-[#e7eef5] px-5 py-4 sm:px-6"><button type="button" class="grid size-10 place-items-center rounded-full text-[#31577f] hover:bg-[#f2f7fc]" aria-label="Kembali" @click="screen = isRegistering ? 'register-choice' : 'choice'; notice = ''"><ArrowLeft class="size-5" /></button><div><p class="text-[10px] font-bold uppercase tracking-[0.14em] text-[#1688e8]">TapakLokal</p><h2 id="auth-heading" class="mt-0.5 text-lg font-extrabold text-[#173b70]">{{ isRegistering ? 'Masukkan nomor HP atau email' : 'Masuk ke akunmu' }}</h2></div><button type="button" class="ml-auto grid size-9 place-items-center rounded-full text-[#55739b] hover:bg-[#f2f7fc]" aria-label="Tutup" @click="emit('close')"><X class="size-5" /></button></header>
                    <form class="p-6 sm:p-8" @submit.prevent="submit"><p class="text-sm leading-6 text-[#60789c]">{{ isRegistering ? 'Gunakan nomor HP atau email untuk membuat akun TapakLokal.' : 'Masukkan email atau nomor HP dan kata sandimu.' }}</p><label class="mt-6 block text-xs font-bold text-[#31577f]">Nomor HP atau email<div class="mt-2 flex min-h-12 items-center gap-3 rounded-xl border border-[#cfe0ef] bg-white px-4 focus-within:border-[#1688e8] focus-within:ring-2 focus-within:ring-[#1688e8]/15"><Phone v-if="!identifier.includes('@')" class="size-4 text-[#1688e8]" /><Mail v-else class="size-4 text-[#1688e8]" /><input v-model="identifier" required autocomplete="username" placeholder="0812xxxx atau nama@email.com" class="min-w-0 flex-1 bg-transparent text-sm font-normal outline-none placeholder:text-[#9aabc0]" /></div></label><label v-if="!isRegistering" class="mt-4 block text-xs font-bold text-[#31577f]">Kata sandi<input v-model="password" required type="password" autocomplete="current-password" placeholder="Masukkan kata sandi" class="mt-2 min-h-12 w-full rounded-xl border border-[#cfe0ef] px-4 text-sm font-normal outline-none focus:border-[#1688e8] focus:ring-2 focus:ring-[#1688e8]/15" /></label><button type="submit" class="mt-6 flex min-h-12 w-full items-center justify-center rounded-xl bg-[#1688e8] px-4 text-sm font-extrabold text-white shadow-[0_6px_16px_rgba(22,136,232,0.25)] hover:bg-[#0875d0]">{{ isRegistering ? 'Lanjutkan' : 'Masuk' }}</button><p v-if="notice" class="mt-4 flex items-start gap-2 rounded-xl bg-emerald-50 px-3 py-3 text-xs leading-5 text-emerald-700"><CheckCircle2 class="mt-0.5 size-4 shrink-0" />{{ notice }}</p></form>
                </template>
            </section>
        </div>
    </Teleport>
</template>