<script setup>
import { Camera, Check, Mail, Phone, Plus, ShieldCheck, UserRound, X } from 'lucide-vue-next';
import { onBeforeUnmount, reactive, ref } from 'vue';
import AccountSecurity from './AccountSecurity.vue';

const tab = ref('Profil');
const initial = { name: 'albnnaardy', gender: '', birth: '', city: 'Jakarta Timur' };
const form = reactive({ ...initial });
const saved = ref({ ...initial });
const notice = ref('');
const avatar = ref('');
const photoInput = ref(null);
const dialog = ref(null);
const contactType = ref('email');
const contact = ref('');
const emails = ref([]);
const phones = ref([]);
const save = () => { saved.value = { ...form }; notice.value = 'Perubahan profil tersimpan untuk pratinjau ini.'; };
const changePhoto = (event) => {
    const file = event.target.files?.[0];
    if (! file) { return; }
    if (! ['image/jpeg', 'image/png', 'image/webp'].includes(file.type) || file.size > 2 * 1024 * 1024) {
        notice.value = 'Pilih foto JPG, PNG, atau WebP maksimal 2 MB.';
        return;
    }
    if (avatar.value) { URL.revokeObjectURL(avatar.value); }
    avatar.value = URL.createObjectURL(file);
};
const addContact = (type) => { contactType.value = type; contact.value = ''; dialog.value.showModal(); };
const saveContact = () => {
    const target = contactType.value === 'email' ? emails : phones;
    const value = contact.value.trim();
    if (value && target.value.length < 3 && ! target.value.includes(value)) { target.value.push(value); }
    dialog.value.close();
    notice.value = 'Kontak ditambahkan ke pratinjau. Verifikasi dan penyimpanan akun belum tersedia.';
};
onBeforeUnmount(() => { if (avatar.value) { URL.revokeObjectURL(avatar.value); } });
</script>

<template>
    <section aria-labelledby="profile-heading" class="text-[#26364d]">
        <div class="flex items-center gap-3"><span class="grid size-11 place-items-center rounded-2xl bg-[#eaf3ff] text-[#1045a5]"><UserRound class="size-5" /></span><div><h2 id="profile-heading" class="text-xl font-extrabold tracking-tight">Pengaturan Akun</h2><p class="mt-1 text-xs text-slate-500">Kelola informasi pribadi dan kontak perjalananmu.</p></div></div>
        <div class="mt-5 inline-flex gap-1 rounded-xl border border-slate-200 bg-white p-1"><button v-for="item in ['Profil', 'Keamanan']" :key="item" class="rounded-lg px-5 py-2.5 text-xs font-semibold transition" :class="tab === item ? 'bg-[#1045a5] text-white shadow-sm' : 'text-slate-500 hover:bg-sky-50'" :aria-pressed="tab === item" @click="tab = item">{{ item }}</button></div>
        <p v-if="notice" role="status" class="mt-4 flex items-center justify-between gap-3 rounded-xl bg-sky-50 px-4 py-3 text-xs leading-5 text-[#175a9f]">{{ notice }}<button aria-label="Tutup pesan" @click="notice = ''"><X class="size-4" /></button></p>
        <template v-if="tab === 'Profil'">
            <form class="mt-5 overflow-hidden rounded-2xl border border-[#e1e8f3] bg-white" @submit.prevent="save">
                <div class="border-b border-slate-100 px-5 py-5 sm:px-6"><h3 class="text-sm font-bold">Data pribadi</h3><p class="mt-1 text-xs text-slate-400">Pastikan informasi profilmu selalu terbaru.</p></div>
                <div class="p-5 sm:p-6">
                    <div class="mb-6 flex flex-wrap items-center gap-4 rounded-xl bg-[#f6f9fd] p-4"><span class="relative grid size-16 shrink-0 place-items-center overflow-hidden rounded-full bg-[#1045a5] text-xl font-bold text-white"><img v-if="avatar" :src="avatar" alt="Foto profil pratinjau" class="size-full object-cover" /><template v-else>{{ form.name.charAt(0).toUpperCase() || 'A' }}</template></span><div class="min-w-0 flex-1"><p class="break-words text-sm font-bold">{{ saved.name }}</p><p class="mt-1 text-[11px] text-slate-400">JPG, PNG, atau WebP · Maks. 2 MB</p></div><input ref="photoInput" type="file" accept="image/jpeg,image/png,image/webp" class="hidden" @change="changePhoto" /><button type="button" class="inline-flex items-center gap-2 rounded-lg border border-[#d9e5f4] bg-white px-3 py-2 text-xs font-semibold text-[#1045a5] hover:bg-sky-50" @click="photoInput.click()"><Camera class="size-4" />Ganti foto</button></div>
                    <div class="grid gap-5 sm:grid-cols-2">
                        <label class="flex flex-col gap-2 text-xs font-semibold sm:col-span-2">Nama lengkap<input v-model="form.name" required maxlength="100" autocomplete="name" class="h-11 rounded-xl border border-[#dce5f0] px-3 text-sm font-normal outline-[#078cff]" /><span class="text-[10px] font-normal text-slate-400">Nama ini ditampilkan pada profilmu.</span></label>
                        <label class="flex flex-col gap-2 text-xs font-semibold">Jenis kelamin<select v-model="form.gender" class="h-11 rounded-xl border border-[#dce5f0] bg-white px-3 text-sm font-normal outline-[#078cff]"><option value="">Pilih jenis kelamin</option><option>Laki-laki</option><option>Perempuan</option><option>Tidak ingin menyebutkan</option></select></label>
                        <label class="flex min-w-0 flex-col gap-2 text-xs font-semibold">Tanggal lahir<input v-model="form.birth" type="date" :max="new Date().toLocaleDateString('en-CA')" autocomplete="bday" class="h-11 min-w-0 rounded-xl border border-[#dce5f0] bg-white px-3 text-sm font-normal outline-[#078cff]" /></label>
                        <label class="flex flex-col gap-2 text-xs font-semibold sm:col-span-2">Kota tempat tinggal<input v-model="form.city" maxlength="100" autocomplete="address-level2" placeholder="Contoh: Jakarta Timur" class="h-11 rounded-xl border border-[#dce5f0] px-3 text-sm font-normal outline-[#078cff]" /></label>
                    </div>
                </div>
                <div class="flex justify-end gap-2 border-t border-slate-100 bg-[#fbfcfe] px-5 py-4"><button type="button" class="rounded-lg border border-slate-200 bg-white px-4 py-2.5 text-xs font-semibold text-slate-500 hover:bg-slate-50" @click="Object.assign(form, saved)">Batalkan</button><button class="inline-flex items-center gap-2 rounded-lg bg-[#1045a5] px-4 py-2.5 text-xs font-semibold text-white hover:bg-[#083788]"><Check class="size-4" />Simpan perubahan</button></div>
            </form>
            <div v-for="section in [{ type: 'email', title: 'Alamat email', icon: Mail, items: emails }, { type: 'phone', title: 'Nomor ponsel', icon: Phone, items: phones }]" :key="section.type" class="mt-4 rounded-2xl border border-[#e1e8f3] bg-white p-5 sm:p-6">
                <div class="flex items-start gap-3"><span class="grid size-10 shrink-0 place-items-center rounded-xl bg-[#edf5ff] text-[#1045a5]"><component :is="section.icon" class="size-5" /></span><div class="flex-1"><h3 class="text-sm font-bold">{{ section.title }}</h3><p class="mt-1 text-[11px] text-slate-400">Kelola hingga 3 kontak untuk akunmu.</p></div><button :disabled="section.items.length >= 3" class="inline-flex items-center gap-1 rounded-lg px-2 py-2 text-xs font-semibold text-[#078cff] hover:bg-sky-50 disabled:opacity-40" @click="addContact(section.type)"><Plus class="size-4" />Tambah</button></div>
                <div v-if="section.items.length" class="mt-4 space-y-2"><div v-for="item in section.items" :key="item" class="flex flex-wrap items-center justify-between gap-2 rounded-xl border border-slate-100 bg-[#f8fbff] p-3"><p class="break-all text-xs font-medium">{{ item }}</p><span class="rounded-full bg-amber-50 px-2 py-1 text-[9px] text-amber-700">Belum diverifikasi</span></div></div>
                <div v-else class="mt-4 rounded-xl border border-dashed border-[#dce5f0] px-4 py-5 text-center"><p class="text-xs text-slate-500">Belum ada {{ section.title.toLowerCase() }} ditambahkan.</p><p class="mt-1 text-[10px] text-slate-400">Tambahkan kontak agar informasi perjalanan mudah dijangkau.</p></div>
            </div>
        </template>
        <AccountSecurity v-else />
        <p class="mt-4 text-[10px] text-slate-400">Pratinjau lokal · Perubahan belum disimpan ke server dan akan hilang saat halaman dimuat ulang.</p>
        <dialog ref="dialog" class="fixed inset-0 m-auto w-[calc(100%-2rem)] max-w-md rounded-2xl bg-white p-6 text-slate-700 backdrop:bg-slate-900/50" aria-labelledby="contact-title"><form @submit.prevent="saveContact"><div class="flex items-center justify-between"><h2 id="contact-title" class="font-bold">Tambah {{ contactType === 'email' ? 'alamat email' : 'nomor ponsel' }}</h2><button type="button" aria-label="Tutup" @click="dialog.close()"><X class="size-5" /></button></div><label class="mt-5 flex flex-col gap-2 text-xs">{{ contactType === 'email' ? 'Alamat email' : 'Nomor ponsel' }}<input v-model="contact" autofocus required :type="contactType === 'email' ? 'email' : 'tel'" :pattern="contactType === 'phone' ? '[+0-9() -]{8,20}' : undefined" maxlength="150" class="h-11 rounded-xl border border-slate-200 px-3 text-sm outline-[#078cff]" /></label><button class="mt-5 rounded-lg bg-[#1045a5] px-4 py-2.5 text-xs font-bold text-white">Tambahkan</button></form></dialog>
    </section>
</template>
