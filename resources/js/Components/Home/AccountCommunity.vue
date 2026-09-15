<script setup>
import { computed, ref } from 'vue';
import AccountExploreBanner from './AccountExploreBanner.vue';
import { Heart, MessageCircle, Plus, Search, Send, Star, Users, X } from 'lucide-vue-next';
defineProps({ section: { type: String, required: true } });
const filter = ref('Semua');
const search = ref('');
const favorites = ref([{ id: 1, name: 'Open Trip Bali', type: 'Open Trip', place: 'Bali', price: 'Rp 750.000', image: '1537996194471-e657df975ab4' }, { id: 2, name: 'Private Trip Lombok', type: 'Private Trip', place: 'Lombok', price: 'Rp 1.500.000', image: '1518548419970-58e3b4079ab2' }]);
const visibleFavorites = computed(() => favorites.value.filter((item) => (filter.value === 'Semua' || item.type === filter.value) && item.name.toLowerCase().includes(search.value.toLowerCase())));
const travelers = ref([{ id: 1, name: 'Albnnaardy', relation: 'Diri sendiri' }]);
const travelerName = ref('');
const relation = ref('Keluarga');
const dialog = ref(null);
const editing = ref(null);
const openTraveler = (item = null) => { editing.value = item; travelerName.value = item?.name || ''; relation.value = item?.relation || 'Keluarga'; dialog.value.showModal(); };
const saveTraveler = () => { if (editing.value) { editing.value.name = travelerName.value.trim(); editing.value.relation = relation.value; } else { travelers.value.push({ id: Date.now(), name: travelerName.value.trim(), relation: relation.value }); } dialog.value.close(); };
const conversations = ref([{ id: 1, name: 'Partner Trip Bali', subtitle: 'Informasi perjalanan', messages: [{ own: false, text: 'Halo! Ada yang ingin ditanyakan tentang perjalananmu?' }] }, { id: 2, name: 'Tim TapakLokal', subtitle: 'Bantuan pemesanan', messages: [{ own: false, text: 'Selamat datang di pratinjau chat TapakLokal.' }] }]);
const selectedChat = ref(1);
const chat = computed(() => conversations.value.find((item) => item.id === selectedChat.value));
const draft = ref('');
const send = () => { if (!draft.value.trim()) { return; } chat.value.messages.push({ own: true, text: draft.value.trim() }); draft.value = ''; };
const rating = ref(0);
const reviewText = ref('');
const reviews = ref([]);
const reviewError = ref('');
const saveReview = () => { if (!rating.value) { reviewError.value = 'Pilih rating terlebih dahulu.'; return; } reviews.value.unshift({ id: Date.now(), rating: rating.value, text: reviewText.value.trim() }); rating.value = 0; reviewText.value = ''; reviewError.value = ''; };
const subject = ref('');
const category = ref('Pemesanan');
const helpText = ref('');
const tickets = ref([]);
const saveTicket = () => { tickets.value.unshift({ id: Date.now(), subject: subject.value.trim(), category: category.value, text: helpText.value.trim() }); subject.value = ''; helpText.value = ''; };
const control = 'w-full rounded-xl border border-[#dce5f0] bg-white px-3 py-3 text-sm outline-[#078cff]';
const primary = 'rounded-lg bg-[#1045a5] px-4 py-2.5 text-xs font-semibold text-white transition hover:bg-[#083788] focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-[#078cff]';
</script>

<template>
    <section class="text-[#26364d]">
        <h2 :class="section === 'OT & OP Favorit' ? 'text-sm font-extrabold text-[#183660]' : 'text-xl font-extrabold tracking-tight'">{{ section }}</h2>
        <p class="mt-2 text-xs text-slate-500">{{ { 'OT & OP Favorit': 'Simpan inspirasi perjalanan dan temukan kembali trip pilihanmu.', 'Daftar Wisatawan': 'Kelola daftar peserta untuk memudahkan pemesanan berikutnya.', Chat: 'Percakapan dengan partner dan tim TapakLokal.', 'Rating & Ulasan': 'Bagikan cerita dan penilaian perjalananmu.', 'Pesan Bantuan': 'Ceritakan kendalamu agar lebih mudah ditangani.' }[section] }}</p>

        <template v-if="section === 'OT & OP Favorit'">
            <div class="mt-4 rounded-2xl border border-[#e1eaf5] bg-white p-4 shadow-[0_4px_20px_rgba(23,75,120,0.04)]">
                <label class="flex h-10 items-center gap-2 rounded-xl border border-[#e1eaf5] px-3 focus-within:border-[#078cff] focus-within:ring-2 focus-within:ring-sky-100"><Search class="size-4 text-[#078cff]" /><input v-model="search" type="search" placeholder="Cari perjalanan favoritmu…" aria-label="Cari trip favorit" class="min-w-0 flex-1 bg-transparent text-xs outline-none" /></label>
                <div class="mt-3 flex flex-wrap gap-1"><button v-for="item in ['Semua', 'Open Trip', 'Private Trip']" :key="item" type="button" class="min-h-8 rounded-full px-3 py-1.5 text-[10px] font-semibold transition-colors focus-visible:outline-2 focus-visible:outline-[#3E7BEF]" :class="filter === item ? 'bg-[#3E7BEF] text-white shadow-sm' : 'bg-[#f8fafc] text-slate-500 hover:bg-[#edf4ff]'" :aria-pressed="filter === item" @click="filter = item">{{ item }}</button></div>
            </div>
            <section class="mt-4 overflow-hidden rounded-2xl border border-[#e1eaf5] bg-white shadow-[0_4px_20px_rgba(23,75,120,0.04)]" aria-label="Daftar favorit">
                <div class="flex items-center justify-between border-b border-slate-100 px-4 py-3"><h3 class="flex items-center gap-2 text-xs font-semibold text-[#183660]"><Heart class="size-4 text-[#3E7BEF]" />Pilihan favoritmu</h3><span class="rounded-full bg-[#edf4ff] px-2.5 py-1 text-[10px] text-[#3E7BEF]" aria-live="polite">{{ visibleFavorites.length }} tersimpan</span></div>
                <div tabindex="0" role="region" aria-label="Daftar favorit, dapat digulir" class="max-h-[520px] overflow-y-auto overscroll-contain bg-[#f8fafc] p-3 sm:p-4 [scrollbar-width:thin] [scrollbar-color:#b9d5fa_transparent]">
                    <div class="grid gap-3 sm:grid-cols-2"><article v-for="item in visibleFavorites" :key="item.id" class="group overflow-hidden rounded-xl border border-[#e1eaf5] bg-white transition duration-200 hover:border-[#aacbff] hover:shadow-md"><div class="relative h-36 overflow-hidden"><img :src="`https://images.unsplash.com/photo-${item.image}?auto=format&fit=crop&w=640&q=85`" :alt="item.name" loading="lazy" class="size-full object-cover transition-transform duration-300 group-hover:scale-105 motion-reduce:transform-none" /><div class="pointer-events-none absolute inset-0 bg-gradient-to-t from-slate-900/40 to-transparent"></div><span class="absolute bottom-3 left-3 rounded-full bg-white/95 px-2.5 py-1 text-[9px] font-semibold text-[#3E7BEF]">{{ item.type }}</span><button type="button" class="absolute right-3 top-3 rounded-full bg-white p-2 text-rose-500 shadow-sm transition-colors hover:bg-rose-50 focus-visible:outline-2 focus-visible:outline-[#3E7BEF]" :aria-label="`Hapus ${item.name} dari favorit`" @click="favorites = favorites.filter((favorite) => favorite.id !== item.id)"><Heart class="size-4 fill-current" /></button></div><div class="p-4"><h3 class="text-xs font-bold text-[#183660]">{{ item.name }}</h3><p class="mt-1.5 text-[11px] text-slate-500">{{ item.place }}</p><div class="mt-4 border-t border-slate-100 pt-3"><p class="text-[9px] text-slate-400">Mulai dari</p><p class="mt-1 text-sm font-bold text-[#078cff]">{{ item.price }} <span class="text-[9px] font-normal text-slate-400">/ orang</span></p></div></div></article></div>
                    <div v-if="!visibleFavorites.length" class="rounded-xl border border-[#e1eaf5] bg-white px-4 py-10 text-center"><Heart class="mx-auto size-8 text-blue-200" /><p class="mt-3 text-xs font-semibold text-[#183660]">Belum ada trip favorit yang sesuai.</p><p class="mt-2 text-[11px] text-slate-500">Coba kata kunci atau jenis perjalanan lainnya.</p></div>
                </div>
            </section>
            <AccountExploreBanner />
        </template>

        <template v-else-if="section === 'Daftar Wisatawan'">
            <div class="mt-5 rounded-2xl border border-[#e1e8f3] bg-white p-5"><div class="mb-4 flex items-center justify-between gap-3"><h3 class="text-sm font-bold">Wisatawan tersimpan <span class="text-slate-400">({{ travelers.length }})</span></h3><button :class="primary" @click="openTraveler()"><Plus class="mr-1 inline size-4" />Tambah</button></div><div v-for="person in travelers" :key="person.id" class="flex items-center gap-3 border-t border-slate-100 py-4"><span class="grid size-11 place-items-center rounded-xl bg-sky-50 text-[#1045a5]"><Users class="size-5" /></span><div class="flex-1"><p class="text-sm font-semibold">{{ person.name }}</p><p class="mt-1 text-xs text-slate-400">{{ person.relation }}</p></div><button class="text-xs font-semibold text-[#078cff]" @click="openTraveler(person)">Edit</button></div></div>
        </template>

        <div v-else-if="section === 'Chat'" class="mt-5 grid overflow-hidden rounded-2xl border border-[#e1e8f3] bg-white lg:grid-cols-[210px_minmax(0,1fr)]">
            <div class="border-b border-slate-100 p-2 lg:border-b-0 lg:border-r"><button v-for="item in conversations" :key="item.id" class="flex w-full items-center gap-2 rounded-xl p-3 text-left" :class="selectedChat === item.id ? 'bg-sky-50' : 'hover:bg-slate-50'" @click="selectedChat = item.id; draft = ''"><MessageCircle class="size-5 shrink-0 text-[#078cff]" /><span><span class="block text-xs font-bold">{{ item.name }}</span><span class="mt-1 block text-[10px] text-slate-400">{{ item.subtitle }}</span></span></button></div>
            <div class="min-w-0"><h3 class="border-b border-slate-100 p-4 text-sm font-bold">{{ chat.name }}</h3><div class="flex h-80 flex-col gap-3 overflow-y-auto bg-[#f8fbff] p-4" aria-live="polite"><p v-for="(message, index) in chat.messages" :key="index" class="max-w-[85%] whitespace-pre-wrap break-words rounded-2xl px-4 py-3 text-xs leading-5" :class="message.own ? 'self-end bg-[#1045a5] text-white' : 'self-start border border-slate-100 bg-white text-slate-600'">{{ message.text }}</p></div><form class="flex gap-2 border-t border-slate-100 p-3" @submit.prevent="send"><input v-model="draft" maxlength="2000" placeholder="Tulis pesan contoh…" aria-label="Pesan chat" :class="control" /><button :class="primary" :disabled="!draft.trim()" aria-label="Tambahkan pesan ke pratinjau"><Send class="size-4" /></button></form></div>
        </div>

        <template v-else-if="section === 'Rating & Ulasan'">
            <form class="mt-5 rounded-2xl border border-[#e1e8f3] bg-white p-5 sm:p-6" @submit.prevent="saveReview"><h3 class="text-sm font-bold">Ulas perjalanan Pantai Drini</h3><p class="mt-1 text-xs text-slate-400">Bagaimana pengalaman perjalananmu?</p><div class="my-4 flex gap-2"><button v-for="value in 5" :key="value" type="button" class="rounded-lg p-1 focus-visible:outline-2 focus-visible:outline-[#078cff]" :aria-label="`Rating ${value} dari 5`" :aria-pressed="rating === value" @click="rating = value"><Star class="size-7" :class="value <= rating ? 'fill-amber-400 text-amber-400' : 'text-slate-200'" /></button></div><textarea v-model="reviewText" required minlength="10" maxlength="2000" rows="4" placeholder="Ceritakan pengalamanmu (minimal 10 karakter)" aria-label="Isi ulasan" :class="control"></textarea><p v-if="reviewError" role="alert" class="mt-2 text-xs text-rose-500">{{ reviewError }}</p><div class="mt-4 flex justify-end"><button :class="primary">Simpan ulasan contoh</button></div></form><article v-for="review in reviews" :key="review.id" class="mt-4 rounded-2xl border border-[#e1e8f3] bg-white p-5"><p class="text-xs font-semibold text-amber-600">★ {{ review.rating }} / 5 · Pantai Drini</p><p class="mt-3 whitespace-pre-wrap break-words text-sm leading-6 text-slate-600">{{ review.text }}</p><span class="mt-3 block text-[10px] text-slate-400">Pratinjau · Belum dipublikasikan</span></article>
        </template>

        <template v-else-if="section === 'Pesan Bantuan'">
            <form class="mt-5 space-y-4 rounded-2xl border border-[#e1e8f3] bg-white p-5 sm:p-6" @submit.prevent="saveTicket"><label class="flex flex-col gap-2 text-xs font-semibold">Topik bantuan<select v-model="category" :class="control"><option>Pemesanan</option><option>Pembayaran</option><option>Voucher & Points</option><option>Akun</option><option>Lainnya</option></select></label><label class="flex flex-col gap-2 text-xs font-semibold">Judul pesan<input v-model="subject" required maxlength="120" placeholder="Ringkasan kendalamu" :class="control" /></label><label class="flex flex-col gap-2 text-xs font-semibold">Detail kendala<textarea v-model="helpText" required minlength="10" maxlength="3000" rows="5" placeholder="Jelaskan kendala tanpa menyertakan kata sandi atau data pembayaran sensitif." :class="control"></textarea></label><div class="flex justify-end"><button :class="primary">Simpan pesan contoh</button></div></form><div v-if="tickets.length" class="mt-5"><h3 class="text-sm font-bold">Pesan tersimpan</h3><article v-for="ticket in tickets" :key="ticket.id" class="mt-3 rounded-xl border border-[#e1e8f3] bg-white p-4"><p class="text-[10px] text-[#078cff]">{{ ticket.category }} · Draf lokal</p><h4 class="mt-2 text-sm font-bold">{{ ticket.subject }}</h4><p class="mt-2 whitespace-pre-wrap break-words text-xs leading-5 text-slate-500">{{ ticket.text }}</p></article></div>
        </template>
        <p class="mt-4 text-[10px] leading-5 text-slate-400">Pratinjau lokal · Data contoh dan perubahan tidak disimpan ke server. Chat, ulasan, dan bantuan tidak dikirim kepada pihak lain.</p>
        <dialog ref="dialog" class="fixed inset-0 m-auto w-[calc(100%-2rem)] max-w-md rounded-2xl bg-white p-6 text-slate-700 backdrop:bg-slate-900/50" aria-labelledby="traveler-dialog"><form class="space-y-4" @submit.prevent="saveTraveler"><div class="flex justify-between"><h2 id="traveler-dialog" class="font-bold">{{ editing ? 'Edit' : 'Tambah' }} wisatawan</h2><button type="button" aria-label="Tutup" @click="dialog.close()"><X class="size-5" /></button></div><label class="flex flex-col gap-2 text-xs">Nama lengkap<input v-model="travelerName" autofocus required maxlength="100" :class="control" /></label><label class="flex flex-col gap-2 text-xs">Hubungan<select v-model="relation" :class="control"><option>Diri sendiri</option><option>Keluarga</option><option>Teman</option><option>Lainnya</option></select></label><button :class="primary">Simpan wisatawan</button></form></dialog>
    </section>
</template>
