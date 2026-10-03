<script setup>
import { computed, ref, onMounted, onBeforeUnmount, watch } from 'vue';
import { Link, router, usePage } from '@inertiajs/vue3';
import { route } from 'ziggy-js';
import { Bell, ShoppingCart, MessageCircle, X, Clock3, Tickets, Package, ChevronRight, CheckCheck, Store } from 'lucide-vue-next';
const page = usePage();
const navigation = computed(() => page.props.navigation || {});
const opened = ref(false);
const tab = ref('transactions');
const container = ref(null);
const trigger = ref(null);
const marking = ref(false);
const permissions = computed(() => page.props.auth?.permissions || []);
const messageUrl = computed(() => {
    if (permissions.value.includes('admin.access') && permissions.value.includes('operations.view')) return route('admin.resources.index', 'support');
    if (permissions.value.includes('vendor.access')) return route('vendor.section', 'support');
    return route('account.section', 'chat');
});
const isTransaction = item => /\/(bookings|payments|checkout|oleh-oleh\/pesanan)(\/|\?|$)/.test(item.data?.url || '');
const notices = computed(() => (navigation.value.notifications || []).filter(item => tab.value === 'transactions' ? isTransaction(item) : !isTransaction(item)));
const close = () => { opened.value = false; };
const outside = event => { if (!container.value?.contains(event.target)) close(); };
const escape = event => { if (event.key === 'Escape' && opened.value) { close(); trigger.value?.focus(); } };
onMounted(() => { document.addEventListener('pointerdown', outside); document.addEventListener('keydown', escape); });
onBeforeUnmount(() => { document.removeEventListener('pointerdown', outside); document.removeEventListener('keydown', escape); });
watch(() => page.url, close);
const toggle = () => { if (!page.props.auth?.user) { router.visit(route('notifications.index')); return; } opened.value = !opened.value; };
const readAll = () => {
    if (marking.value || !navigation.value.unreadCount) return;
    marking.value = true;
    router.post(route('notifications.read-all'), {}, { preserveScroll: true, onFinish: () => { marking.value = false; } });
};
const openNotice = item => {
    const url = item.data?.url;
    let target = route('notifications.index');
    try { const parsed = new URL(url, window.location.origin); if (parsed.origin === window.location.origin) target = parsed.pathname + parsed.search + parsed.hash; } catch {}
    const visit = () => { close(); router.visit(target); };
    if (item.read_at) visit();
    else router.post(route('notifications.read', item.id), {}, { preserveScroll: true, onSuccess: visit });
};
const date = value => new Date(value).toLocaleString('id-ID', { day: 'numeric', month: 'short', hour: '2-digit', minute: '2-digit' });
defineProps({ isTransparent: Boolean });
</script>
<template>
    <div class="flex items-center gap-1" :class="isTransparent ? 'text-white' : 'text-[#173b70]'">
        <Link :href="route('souvenirs.cart')" class="relative grid size-10 place-items-center rounded-full hover:bg-blue-100/20 focus-visible:ring-2 focus-visible:ring-blue-500" :aria-label="`Keranjang, ${navigation.cartCount || 0} produk`"><ShoppingCart class="size-5" /><span v-if="navigation.cartCount" class="absolute right-0 top-0 min-w-4 rounded-full bg-[#0088ff] px-1 text-center text-[10px] font-bold text-white">{{ navigation.cartCount > 99 ? '99+' : navigation.cartCount }}</span></Link>
        <div ref="container" class="relative">
            <button ref="trigger" type="button" :aria-expanded="opened" aria-label="Buka notifikasi" class="relative grid size-10 place-items-center rounded-xl transition hover:bg-blue-100/30 focus-visible:ring-2 focus-visible:ring-blue-500" :class="opened ? 'bg-[#edf5ff] text-[#3e7bef]' : ''" @click="toggle"><Bell class="size-5" /><span v-if="navigation.unreadCount" class="absolute right-0 top-0 min-w-4 rounded-full bg-red-500 px-1 text-center text-[10px] font-bold text-white">{{ navigation.unreadCount > 99 ? '99+' : navigation.unreadCount }}</span></button>
            <section v-if="opened" aria-label="Notifikasi TapakLokal" class="fixed inset-x-3 top-20 z-[100] flex max-h-[min(680px,80dvh)] flex-col overflow-hidden rounded-2xl border border-[#dce6f4] bg-white text-[#17345e] shadow-[0_16px_60px_rgba(23,52,94,0.18)] sm:absolute sm:inset-x-auto sm:right-0 sm:top-full sm:mt-3 sm:w-[390px]">
                <header class="flex items-center justify-between gap-3 px-5 py-4"><div class="flex items-center gap-2"><h2 class="text-base font-extrabold">Notifikasi</h2><span v-if="navigation.unreadCount" class="rounded-full bg-blue-50 px-2 py-1 text-[10px] font-bold text-[#3e7bef]">{{ navigation.unreadCount }} baru</span></div><button type="button" aria-label="Tutup notifikasi" class="grid size-8 place-items-center rounded-lg text-slate-400 hover:bg-blue-50" @click="close"><X class="size-4" /></button></header>
                <nav aria-label="Kategori notifikasi" class="grid shrink-0 grid-cols-2 border-b border-[#e8eef7]"><button v-for="item in [{ key: 'transactions', label: 'Transaksi' }, { key: 'updates', label: 'Update' }]" :key="item.key" type="button" :aria-pressed="tab === item.key" class="border-b-2 py-3 text-sm font-bold transition-colors" :class="tab === item.key ? 'border-[#3e7bef] text-[#3e7bef]' : 'border-transparent text-slate-400 hover:bg-blue-50'" @click="tab = item.key">{{ item.label }}</button></nav>
                <div class="min-h-0 overflow-y-auto overscroll-contain">
                    <div v-if="tab === 'transactions'" class="border-b border-[#edf1f7] p-5"><div class="flex items-center justify-between"><h3 class="text-xs font-bold">Pesanan kamu</h3><Link :href="route('account.section', 'transactions')" class="text-[11px] font-semibold text-[#3e7bef]" @click="close">Lihat semua</Link></div><Link :href="route('account.section', 'payments')" class="mt-3 flex items-center gap-2 rounded-xl bg-[#edf5ff] px-3 py-3 text-xs font-semibold text-[#2866d4] hover:bg-blue-100" @click="close"><Clock3 class="size-4" />Menunggu pembayaran<ChevronRight class="ml-auto size-4" /></Link><div class="mt-4 grid grid-cols-2 gap-3"><Link :href="route('account.section', 'bookings')" class="flex flex-col items-center gap-2 rounded-xl border border-[#e8eef7] py-3 text-[11px] hover:bg-blue-50" @click="close"><Tickets class="size-6 text-[#3e7bef]" />Perjalanan & tiket</Link><Link :href="route('account.section', { section: 'bookings', kind: 'po' })" class="flex flex-col items-center gap-2 rounded-xl border border-[#e8eef7] py-3 text-[11px] hover:bg-blue-50" @click="close"><Package class="size-6 text-[#3e7bef]" />Pesanan oleh-oleh</Link></div></div>
                    <div v-if="permissions.includes('vendor.access') && tab === 'transactions'" class="border-b border-[#edf1f7] p-5"><h3 class="text-xs font-bold">Aktivitas mitra</h3><p class="mt-2 text-[11px] leading-5 text-slate-500">Kelola pesanan dan layanan pelanggan dari panel mitra.</p><Link :href="route('vendor.section', 'dashboard')" class="mt-3 flex items-center justify-center gap-2 rounded-lg border border-[#3e7bef] px-3 py-2.5 text-xs font-bold text-[#3e7bef] hover:bg-blue-50" @click="close"><Store class="size-4" />Buka panel mitra</Link></div>
                    <div class="px-5 pt-4"><h3 class="text-xs font-bold">{{ tab === 'transactions' ? 'Kabar transaksi terbaru' : 'Update untuk kamu' }}</h3><p class="mt-1 text-[10px] text-slate-400">Dari pemberitahuan terbaru akunmu</p></div>
                    <div v-if="notices.length" class="py-2"><button v-for="item in notices" :key="item.id" type="button" class="flex w-full items-start gap-3 px-5 py-4 text-left transition hover:bg-blue-50" :class="!item.read_at ? 'bg-[#f5f9ff]' : ''" @click="openNotice(item)"><span class="mt-1 grid size-8 shrink-0 place-items-center rounded-lg bg-blue-50 text-[#3e7bef]"><Bell class="size-4" /></span><span class="min-w-0 flex-1"><span class="block text-xs font-bold leading-5">{{ item.data.title || 'Pemberitahuan TapakLokal' }}</span><span class="mt-1 block break-words text-[11px] leading-5 text-slate-500">{{ item.data.reference || item.data.message }}</span><time class="mt-2 block text-[10px] text-slate-400">{{ date(item.created_at) }}</time></span><span v-if="!item.read_at" class="mt-2 size-2 shrink-0 rounded-full bg-[#3e7bef]"></span></button></div>
                    <div v-else class="px-6 py-7 text-center"><img src="/Assets/Images/tapak-points-faq.png" alt="" class="mx-auto size-24 object-contain" /><h4 class="mt-3 text-sm font-bold">Belum ada {{ tab === 'transactions' ? 'kabar transaksi' : 'update terbaru' }}</h4><p class="mt-2 text-xs leading-6 text-slate-500">{{ tab === 'transactions' ? 'Perkembangan pembayaran dan pesananmu akan muncul di sini.' : 'Pemberitahuan akun dan informasi lainnya akan muncul di sini.' }}</p><Link :href="route('catalog')" class="mt-4 inline-flex rounded-lg bg-[#3e7bef] px-5 py-2.5 text-xs font-bold text-white hover:bg-[#2866d4]" @click="close">Jelajahi perjalanan</Link></div>
                </div>
                <footer class="flex shrink-0 items-center justify-between gap-3 border-t border-[#e8eef7] bg-white px-4 py-3"><button type="button" :disabled="marking || !navigation.unreadCount" class="inline-flex items-center gap-1.5 text-[11px] font-semibold text-[#3e7bef] disabled:cursor-default disabled:text-slate-400" @click="readAll"><CheckCheck class="size-4" />Tandai semua dibaca</button><Link :href="route('notifications.index')" class="text-[11px] font-semibold text-[#3e7bef] hover:underline" @click="close">Lihat selengkapnya</Link></footer>
            </section>
        </div>
        <Link :href="messageUrl" class="grid size-10 place-items-center rounded-full hover:bg-blue-100/20" aria-label="Chat dan bantuan"><MessageCircle class="size-5" /></Link>
    </div>
</template>
