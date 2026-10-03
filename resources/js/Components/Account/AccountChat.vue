<script setup>
import { computed, ref, watch, onMounted, onBeforeUnmount, nextTick } from 'vue';
import { router, usePage } from '@inertiajs/vue3';
import axios from 'axios';
import { route } from 'ziggy-js';
import { Search, MessageCircle, Send, Plus, ArrowLeft, Info, Headphones, Smile } from 'lucide-vue-next';
import Pagination from '../Admin/Pagination.vue';
import EmojiPicker from '../Shared/EmojiPicker.vue';
const page = usePage();
const search = ref('');
const selected = ref(null);
const messages = ref([]);
const body = ref('');
const error = ref('');
const loading = ref(false);
const sending = ref(false);
const creating = ref(false);
const subject = ref('');
const category = ref('booking');
const bookingId = ref('');
const composer = ref(null);
const emojiOpen = ref(false);
const insertEmoji = async emoji => {
    const input = composer.value;
    const start = input?.selectionStart ?? body.value.length;
    const end = input?.selectionEnd ?? start;
    body.value = body.value.slice(0, start) + emoji + body.value.slice(end);
    emojiOpen.value = false;
    await nextTick();
    input?.focus();
    input?.setSelectionRange(start + emoji.length, start + emoji.length);
};
const quickMessages = [
    { label: 'Kendala pesanan', category: 'booking', text: 'Halo TapakLokal, saya membutuhkan bantuan terkait pesanan saya.' },
    { label: 'Bantuan pembayaran', category: 'payment', text: 'Halo TapakLokal, saya mengalami kendala pembayaran. Mohon bantuannya.' },
    { label: 'Ajukan pengaduan', category: 'other', text: 'Halo TapakLokal, saya ingin menyampaikan pengaduan terkait layanan perjalanan.' },
    { label: 'Bantuan akun', category: 'account', text: 'Halo TapakLokal, saya membutuhkan bantuan terkait akun saya.' },
];
const useQuickMessage = async item => {
    body.value = item.text;
    if (creating.value) { category.value = item.category; subject.value = item.label; }
    await nextTick();
    composer.value?.focus();
};
const list = ref(null);
const olderUrl = ref(null);
let timer;
let disposed = false;
let version = 0;
const tickets = computed(() => (page.props.records?.data || []).filter(ticket => ticket.subject.toLowerCase().includes(search.value.toLowerCase())));
const time = value => new Date(value).toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' });
const scrollBottom = async () => { await nextTick(); if (list.value) list.value.scrollTop = list.value.scrollHeight; };
const open = async ticket => {
    const requestVersion = ++version;
    selected.value = ticket; creating.value = false; messages.value = []; body.value = ''; error.value = ''; loading.value = true;
    try {
        const { data } = await axios.get(route('support.show', ticket.id), { headers: { Accept: 'application/json' }, timeout: 10000 });
        if (disposed || requestVersion !== version) return;
        selected.value = data.ticket; messages.value = data.messages.data.reverse(); olderUrl.value = data.messages.next_page_url;
        scrollBottom();
    } catch { if (requestVersion === version) error.value = 'Percakapan gagal dibuka. Coba kembali.'; }
    finally { if (requestVersion === version) loading.value = false; }
};
const refresh = async () => {
    if (!selected.value || creating.value || loading.value || sending.value || document.visibilityState !== 'visible') return;
    const current = version;
    loading.value = true;
    try {
        const { data } = await axios.get(route('support.show', selected.value.id), { headers: { Accept: 'application/json' }, timeout: 8000 });
        if (disposed || current !== version) return;
        const nearBottom = !list.value || list.value.scrollHeight - list.value.scrollTop - list.value.clientHeight < 100;
        const existing = new Map(messages.value.map(message => [message.id, message]));
        data.messages.data.forEach(message => existing.set(message.id, message));
        messages.value = [...existing.values()].sort((a, b) => a.id - b.id); selected.value = data.ticket;
        if (nearBottom) scrollBottom();
    } catch { if (current === version) error.value = 'Koneksi terputus. Mencoba menghubungkan kembali…'; }
    finally { if (current === version) loading.value = false; }
};
const older = async () => {
    if (!olderUrl.value || loading.value) return;
    const current = version; loading.value = true;
    try {
        const { data } = await axios.get(olderUrl.value, { headers: { Accept: 'application/json' }, timeout: 8000 });
        if (current !== version || disposed) return;
        const existing = new Map(messages.value.map(message => [message.id, message]));
        data.messages.data.forEach(message => existing.set(message.id, message));
        messages.value = [...existing.values()].sort((a,b) => a.id - b.id); olderUrl.value = data.messages.next_page_url;
    } catch { error.value = 'Pesan sebelumnya gagal dimuat.'; }
    finally { if (current === version) loading.value = false; }
};
const send = async () => {
    if (!body.value.trim() || sending.value) return;
    sending.value = true; error.value = '';
    try {
        if (creating.value) {
            const { data } = await axios.post(route('support.store'), { subject: subject.value, category: category.value, booking_id: bookingId.value || null, body: body.value }, { headers: { Accept: 'application/json' }, timeout: 10000 });
            body.value = ''; await open(data.ticket); router.reload({ only: ['records'], preserveScroll: true });
        } else {
            const { data } = await axios.post(route('support.reply', selected.value.id), { body: body.value }, { headers: { Accept: 'application/json' }, timeout: 10000 });
            if (!messages.value.some(message => message.id === data.message.id)) messages.value.push(data.message);
            body.value = ''; scrollBottom();
        }
    } catch (failure) { error.value = Object.values(failure.response?.data?.errors || {}).flat()[0] || failure.response?.data?.message || 'Pesan gagal dikirim. Coba kembali.'; }
    finally { sending.value = false; }
};
const newChat = () => { version++; selected.value = null; creating.value = true; body.value = ''; subject.value = 'Bantuan TapakLokal'; category.value = 'other'; bookingId.value = ''; olderUrl.value = null; messages.value = []; error.value = ''; loading.value = false; };
let initialized = false;
watch(() => page.props.records, records => {
    if (initialized || !records) return;
    initialized = true;
    const support = records.data?.find(ticket => ticket.category !== 'vendor' && ticket.status !== 'closed');
    if (support) open(support);
    else newChat();
}, { immediate: true });
onMounted(() => { timer = setInterval(refresh, 2000); window.addEventListener('focus', refresh); });
onBeforeUnmount(() => { disposed = true; clearInterval(timer); window.removeEventListener('focus', refresh); });
</script>
<template>
    <div class="flex h-[min(650px,calc(100dvh-160px))] min-h-80 flex-col lg:h-[calc(100dvh-134px)]">
        <div class="mb-4 flex shrink-0 items-start gap-3 rounded-xl border border-blue-200 bg-blue-50 p-3 text-xs leading-5 text-[#31577f]"><Info class="mt-0.5 size-4 shrink-0 text-blue-500" /><p>Chat langsung dengan tim bantuan atau mitra perjalanan. Jaga keamanan akun dan lakukan pembayaran melalui TapakLokal.</p></div>
        <section class="grid min-h-0 flex-1 overflow-hidden rounded-2xl border border-[#dce6f4] bg-white shadow-xs md:grid-cols-[260px_minmax(0,1fr)]">
            <aside class="flex min-h-0 flex-col border-r border-slate-100" :class="selected || creating ? 'hidden md:flex' : 'flex'">
                <div class="flex items-center justify-between p-5"><h2 class="text-xl font-extrabold">Chat</h2><button type="button" aria-label="Mulai percakapan baru" class="grid size-9 place-items-center rounded-full bg-blue-50 text-blue-600" @click="newChat"><Plus class="size-5" /></button></div>
                <label class="relative mx-4 mb-4"><Search class="absolute left-3 top-3 size-4 text-slate-400" /><input v-model="search" placeholder="Cari percakapan" aria-label="Cari percakapan" class="w-full rounded-xl border border-slate-200 py-2.5 pl-9 pr-3 text-xs" /></label>
                <div class="flex-1 overflow-y-auto px-2"><button v-for="ticket in tickets" :key="ticket.id" type="button" class="mb-1 flex w-full items-center gap-3 rounded-xl p-3 text-left hover:bg-blue-50" :class="selected?.id === ticket.id ? 'bg-blue-50' : ''" @click="open(ticket)"><span class="grid size-10 shrink-0 place-items-center rounded-full bg-[#edf5ff] text-blue-600"><Headphones class="size-5" /></span><span class="min-w-0 flex-1"><span class="block truncate text-xs font-bold">{{ ticket.subject }}</span><span class="mt-1 block text-[10px] text-slate-500">{{ ticket.category === 'vendor' ? 'Mitra perjalanan' : 'Tim bantuan' }} · {{ ticket.status === 'closed' ? 'Ditutup' : 'Terbuka' }}</span></span></button><p v-if="!tickets.length" class="p-4 text-center text-xs text-slate-400">Belum ada percakapan.</p></div>
                <Pagination v-if="page.props.records" :records="page.props.records" />
                <button type="button" class="panel-primary m-4" @click="newChat">Mulai chat</button>
            </aside>
            <div class="flex min-h-0 flex-col" :class="!selected && !creating ? 'hidden md:flex' : 'flex'">
                <template v-if="selected || creating">
                    <header class="flex items-center gap-3 border-b border-slate-100 p-4"><button type="button" class="md:hidden" aria-label="Kembali ke daftar chat" @click="version++; selected = null; creating = false"><ArrowLeft class="size-5" /></button><span class="grid size-10 place-items-center rounded-full bg-blue-50 text-blue-600"><Headphones class="size-5" /></span><div><h3 class="text-sm font-bold">{{ creating || selected.category !== 'vendor' ? 'TapakLokal · Tim Bantuan' : selected.subject }}</h3><p class="mt-1 text-[10px] text-slate-500">{{ creating ? 'Tim bantuan TapakLokal' : selected.status === 'closed' ? 'Percakapan ditutup' : 'Pesan diperbarui otomatis' }}</p></div></header>
                    <div ref="list" class="min-h-0 flex-1 space-y-3 overflow-y-auto bg-[#fafcff] p-4" aria-live="polite">
<div v-if="creating" class="max-w-[90%] rounded-2xl border border-blue-100 bg-white p-4"><h4 class="text-xs font-bold text-[#17345e]">Selamat datang di bantuan TapakLokal</h4><p class="mt-2 text-xs leading-6 text-slate-500">Ada yang bisa kami bantu? Ceritakan kendala atau pengaduanmu, atau pilih pesan cepat di bawah. Sertakan kode pesanan jika berkaitan dengan perjalanan.</p></div>
                        <button v-if="olderUrl && !creating" type="button" :disabled="loading" class="block w-full text-xs text-blue-600" @click="older">Muat pesan sebelumnya</button>
                        <article v-for="message in messages" :key="message.id" class="w-fit max-w-[90%] rounded-2xl border px-4 py-3" :class="message.user_id === page.props.auth.user.id ? 'ml-auto border-blue-100 bg-blue-50' : 'border-slate-200 bg-white'"><p class="text-[10px] font-bold text-[#31577f]">{{ message.user?.name }}</p><p class="mt-1 whitespace-pre-wrap break-words text-xs leading-6">{{ message.body }}</p><p class="mt-1 text-right text-[10px] text-slate-400">{{ time(message.created_at) }}</p></article>
                    </div>
                    <div class="shrink-0 border-t border-slate-100 p-4"><div v-if="creating || selected.status !== 'closed'" class="mb-3 flex gap-2 overflow-x-auto pb-1"><button v-for="item in quickMessages" :key="item.label" type="button" class="shrink-0 rounded-full border border-[#0175ea] bg-white px-3 py-2 text-xs font-semibold text-[#0175ea] transition hover:bg-blue-50" @click="useQuickMessage(item)">{{ item.label }}</button></div><p v-if="error" role="alert" class="mb-3 text-xs text-rose-600">{{ error }}</p><form v-if="creating || selected.status !== 'closed'" class="flex items-end gap-3" @submit.prevent="send"><div class="relative flex min-w-0 flex-1 items-center gap-2 rounded-full border border-slate-300 bg-white px-3 py-2 focus-within:border-slate-400">
<button type="button" aria-label="Pilih emoji" :aria-expanded="emojiOpen" class="grid size-7 shrink-0 place-items-center rounded-full text-slate-900 hover:bg-slate-100" @click="emojiOpen = !emojiOpen"><Smile class="size-5" :stroke-width="1.8" /></button>
<EmojiPicker v-if="emojiOpen" class="absolute bottom-full left-0 z-20 mb-2" @select="insertEmoji" @close="emojiOpen = false" />
<textarea ref="composer" v-model="body" required maxlength="5000" rows="1" aria-label="Tulis pesan" placeholder="Tulis Pesan…" class="max-h-24 min-w-0 flex-1 resize-none border-0 bg-transparent py-1 text-sm leading-5 text-slate-800 outline-none placeholder:text-slate-400 focus:ring-0" @keydown.enter.exact.prevent="send" @keydown.esc="emojiOpen = false" />
</div><button type="submit" :disabled="sending || !body.trim() || (creating && !subject.trim())" aria-label="Kirim pesan" class="grid size-11 shrink-0 place-items-center rounded-full bg-[#0175ea] text-white disabled:opacity-40"><Send class="size-5" /></button></form><p v-else class="text-center text-xs text-slate-500">Percakapan ditutup. Mulai chat baru jika membutuhkan bantuan.</p></div>
                </template>
                <div v-else class="grid flex-1 place-items-center p-8 text-center"><div><MessageCircle class="mx-auto size-12 text-blue-300" /><h3 class="mt-4 text-lg font-bold">Percakapanmu di sini</h3><p class="mt-2 text-xs text-slate-500">Pilih percakapan atau mulai chat baru.</p><button type="button" class="panel-primary mt-5" @click="newChat">Mulai chat</button></div></div>
            </div>
        </section>
    </div>
</template>
