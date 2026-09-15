<script setup>
import { Check, Eye, EyeOff, Laptop, LockKeyhole, ShieldCheck, Smartphone, X } from 'lucide-vue-next';
import { computed, reactive, ref } from 'vue';

const passwords = reactive({ current: '', next: '', confirm: '' });
const visible = reactive({ current: false, next: false, confirm: false });
const notice = ref('');
const error = ref('');
const sessions = ref([
    { id: 'mobile', name: 'Mobile App · Android', location: 'Bandung, Indonesia', time: '2 jam lalu', current: true, icon: Smartphone },
    { id: 'laptop', name: 'Laptop · MacBook', location: 'Bandung, Indonesia', time: '3 jam lalu', current: false, icon: Laptop },
]);
const pendingSession = ref(null);
const dialog = ref(null);
const fields = [{ key: 'current', label: 'Kata sandi saat ini', autocomplete: 'current-password' }, { key: 'next', label: 'Kata sandi baru', autocomplete: 'new-password' }, { key: 'confirm', label: 'Konfirmasi kata sandi baru', autocomplete: 'new-password' }];
const checks = computed(() => [
    { label: 'Minimal 8 karakter', valid: passwords.next.length >= 8 },
    { label: 'Mengandung angka', valid: /\d/.test(passwords.next) },
    { label: 'Mengandung simbol', valid: /[^a-zA-Z0-9\s]/.test(passwords.next) },
]);
const reset = () => { passwords.current = ''; passwords.next = ''; passwords.confirm = ''; Object.keys(visible).forEach((key) => visible[key] = false); error.value = ''; };
const submit = () => {
    error.value = '';
    if (! checks.value.every((check) => check.valid)) { error.value = 'Kata sandi baru belum memenuhi persyaratan.'; return; }
    if (passwords.next !== passwords.confirm) { error.value = 'Konfirmasi kata sandi belum cocok.'; return; }
    if (passwords.current === passwords.next) { error.value = 'Gunakan kata sandi baru yang berbeda.'; return; }
    reset();
    notice.value = 'Validasi formulir berhasil. Kata sandi akun tidak diubah karena autentikasi belum terhubung.';
};
const askRemove = (session) => { pendingSession.value = session; dialog.value.showModal(); };
const remove = () => { sessions.value = sessions.value.filter((session) => session.id !== pendingSession.value.id); dialog.value.close(); notice.value = 'Sesi contoh dihapus dari tampilan. Tidak ada perangkat nyata yang dikeluarkan.'; };
</script>

<template>
    <div class="mt-5 space-y-4">
        <p v-if="notice" role="status" class="flex items-start justify-between gap-3 rounded-xl bg-sky-50 px-4 py-3 text-xs leading-5 text-[#175a9f]">{{ notice }}<button aria-label="Tutup pesan keamanan" @click="notice = ''"><X class="size-4" /></button></p>
        <form class="overflow-hidden rounded-2xl border border-[#e1e8f3] bg-white" @submit.prevent="submit">
            <div class="flex items-center gap-3 border-b border-slate-100 px-5 py-5 sm:px-6"><span class="grid size-10 shrink-0 place-items-center rounded-xl bg-[#edf5ff] text-[#1045a5]"><LockKeyhole class="size-5" /></span><div><h3 class="text-sm font-bold">Ubah kata sandi</h3><p class="mt-1 text-[11px] leading-5 text-slate-400">Gunakan kata sandi berbeda dari akun lain untuk menjaga keamananmu.</p></div></div>
            <div class="space-y-5 p-5 sm:p-6">
                <p class="rounded-lg bg-[#f6f9fd] px-3 py-2 text-[11px] leading-5 text-slate-500">Formulir pratinjau. Gunakan data uji, bukan kata sandi akun asli.</p>
                <div v-for="field in fields" :key="field.key"><label :for="`password-${field.key}`" class="mb-2 block text-xs font-semibold">{{ field.label }}</label><div class="flex h-11 items-center overflow-hidden rounded-xl border border-[#dce5f0] transition focus-within:border-[#078cff] focus-within:ring-2 focus-within:ring-sky-100"><input :id="`password-${field.key}`" v-model="passwords[field.key]" :type="visible[field.key] ? 'text' : 'password'" :autocomplete="field.autocomplete" required maxlength="128" :placeholder="field.key === 'confirm' ? 'Ulangi kata sandi baru' : 'Masukkan kata sandi'" class="min-w-0 flex-1 bg-transparent px-3 text-sm outline-none" /><button type="button" class="grid h-full w-11 place-items-center text-slate-400 hover:bg-sky-50 hover:text-[#078cff]" :aria-label="`${visible[field.key] ? 'Sembunyikan' : 'Tampilkan'} ${field.label.toLowerCase()}`" :aria-pressed="visible[field.key]" @click="visible[field.key] = !visible[field.key]"><EyeOff v-if="visible[field.key]" class="size-4" /><Eye v-else class="size-4" /></button></div><div v-if="field.key === 'next'" class="mt-2 flex flex-wrap gap-x-4 gap-y-1"><span v-for="check in checks" :key="check.label" class="inline-flex items-center gap-1 text-[10px]" :class="check.valid ? 'text-teal-600' : 'text-slate-400'"><Check class="size-3" />{{ check.label }}</span></div></div>
                <p v-if="error" role="alert" class="text-xs text-rose-600">{{ error }}</p>
            </div>
            <div class="flex justify-end gap-2 border-t border-slate-100 bg-[#fbfcfe] px-5 py-4"><button type="button" class="rounded-lg border border-slate-200 bg-white px-4 py-2.5 text-xs font-semibold text-slate-500 hover:bg-slate-50" @click="reset">Batalkan</button><button class="inline-flex items-center gap-2 rounded-lg bg-[#1045a5] px-4 py-2.5 text-xs font-semibold text-white hover:bg-[#083788]"><ShieldCheck class="size-4" />Simpan perubahan</button></div>
        </form>
        <section class="rounded-2xl border border-[#e1e8f3] bg-white p-5 sm:p-6" aria-labelledby="sessions-heading"><div><h3 id="sessions-heading" class="text-sm font-bold">Sesi aktif</h3><p class="mt-1 text-[11px] text-slate-400">Contoh perangkat yang terhubung ke akunmu.</p></div><div class="mt-4 divide-y divide-slate-100"><div v-for="session in sessions" :key="session.id" class="flex items-center gap-3 py-4"><span class="grid size-11 shrink-0 place-items-center rounded-xl bg-[#f1f6fc] text-[#6885a7]"><component :is="session.icon" class="size-5" /></span><div class="min-w-0 flex-1"><p class="text-xs font-semibold">{{ session.name }}</p><p class="mt-1 text-[10px] leading-5 text-slate-400">{{ session.location }} · {{ session.time }}</p></div><span v-if="session.current" class="rounded-full bg-teal-50 px-2.5 py-1.5 text-[9px] font-semibold text-teal-600">Sesi ini</span><button v-else class="rounded-lg bg-rose-50 px-3 py-2 text-[11px] font-semibold text-rose-500 transition hover:bg-rose-100" @click="askRemove(session)">Hapus sesi</button></div></div></section>
        <dialog ref="dialog" class="fixed inset-0 m-auto w-[calc(100%-2rem)] max-w-sm rounded-2xl bg-white p-6 text-slate-700 backdrop:bg-slate-900/50" aria-labelledby="remove-session-heading"><h2 id="remove-session-heading" class="font-bold">Hapus sesi contoh?</h2><p class="mt-3 text-sm leading-6 text-slate-500">{{ pendingSession?.name }} akan dihapus dari pratinjau daftar perangkat.</p><div class="mt-5 flex justify-end gap-2"><button autofocus class="rounded-lg border border-slate-200 px-4 py-2 text-xs" @click="dialog.close()">Batal</button><button class="rounded-lg bg-rose-500 px-4 py-2 text-xs font-semibold text-white" @click="remove">Hapus sesi</button></div></dialog>
    </div>
</template>
