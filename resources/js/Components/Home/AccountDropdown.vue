<script setup>
import { Link } from '@inertiajs/vue3';
import { route } from 'ziggy-js';
import { onBeforeUnmount, onMounted, ref } from 'vue';
import { Award, ChevronDown, ChevronRight, ClipboardList, Coins, Gift, Heart, Headset, LogOut, ReceiptText, UserRound, Users, Wallet } from 'lucide-vue-next';

defineProps({ compact: { type: Boolean, default: false } });
const open = ref(false);
const root = ref(null);
const trigger = ref(null);
const groups = [
    { title: 'AKUN & PEMBAYARAN', items: [['Points', Coins, '0 poin'], ['Akun Saya', UserRound, 'Edit profil'], ['Saldo & Pembayaran', Wallet, ''], ['Daftar Transaksi', ReceiptText, '']] },
    { title: 'PERJALANANMU', items: [['Pemesanan & Tiket', ClipboardList, ''], ['OT & OP Favorit', Heart, ''], ['Daftar Wisatawan', Users, ''], ['Voucher', Gift, ''], ['Pesan Bantuan', Headset, '']] },
];
const close = (restore = false) => { open.value = false; if (restore) { trigger.value?.focus(); } };
const outside = (event) => { if (! root.value?.contains(event.target)) { close(); } };
onMounted(() => document.addEventListener('pointerdown', outside));
onBeforeUnmount(() => document.removeEventListener('pointerdown', outside));
</script>

<template>
    <div ref="root" class="relative" @keydown.esc.stop.prevent="close(true)" @focusout="! $event.currentTarget.contains($event.relatedTarget) && close()">
        <button ref="trigger" type="button" class="flex items-center gap-2 rounded-xl p-1.5 text-left transition hover:bg-[#edf5ff] focus-visible:outline-2 focus-visible:outline-[#078cff]" :aria-expanded="open" aria-label="Menu akun" @click="open = ! open">
            <span class="grid size-8 place-items-center rounded-full border-2 border-[#B7874B] bg-[#1045a5] text-xs font-bold text-white ring-2 ring-[#f6ead9]">A</span>
            <template v-if="! compact"><span><span class="block text-[11px] font-bold text-slate-800">albnnaardy</span><span class="block text-[9px] text-slate-400">Bronze Priority</span></span><span class="mx-1 h-6 w-px bg-slate-200"></span><span class="text-center"><span class="block text-sm font-bold text-[#078cff]">0</span><span class="block text-[9px] text-slate-400">points</span></span><ChevronDown class="size-3.5 text-[#078cff] transition-transform duration-200" :class="{ 'rotate-180': open }" /></template>
        </button>
        <Transition enter-active-class="transition duration-200 ease-out" enter-from-class="translate-y-2 scale-95 opacity-0" leave-active-class="transition duration-150 ease-in" leave-to-class="translate-y-1 scale-95 opacity-0">
            <div v-if="open" class="absolute right-0 top-full z-50 mt-3 max-h-[calc(100dvh-80px)] w-[310px] max-w-[calc(100vw-32px)] origin-top-right overflow-y-auto rounded-2xl border border-[#e1eaf5] bg-white shadow-[0_16px_50px_rgba(15,44,92,0.2)] [scrollbar-width:thin]" aria-label="Pilihan akun">
                <div class="bg-gradient-to-br from-[#f7eee4] to-[#eed9c2] p-5"><p class="text-base font-extrabold text-[#493723]">albnnaardy</p><Link :href="route('account', { section: 'Points' })" class="mt-3 flex items-center gap-2 text-xs font-semibold text-[#90623e] hover:text-[#684326]" @click="close()"><Award class="size-4" />Bronze Priority<ChevronRight class="ml-auto size-4" /></Link></div>
                <div class="p-2"><div v-for="group in groups" :key="group.title"><p class="px-3 pb-1 pt-3 text-[9px] font-semibold tracking-wider text-slate-400">{{ group.title }}</p><Link v-for="[label, icon, extra] in group.items" :key="label" :href="route('account', { section: label })" class="group flex items-center gap-3 rounded-xl px-3 py-2.5 text-xs font-medium text-[#34475a] transition duration-200 hover:bg-[#edf6ff] hover:text-[#078cff] focus-visible:outline-2 focus-visible:outline-[#078cff]" @click="close()"><component :is="icon" class="size-[18px] text-[#078cff]" :stroke-width="1.7" /><span class="flex-1">{{ label }}</span><span v-if="extra" class="text-[10px] text-slate-400">{{ extra }}</span><ChevronRight v-else class="size-3 text-slate-300 transition-transform group-hover:translate-x-0.5" /></Link></div></div>
                <div class="border-t border-slate-100 p-2"><button type="button" disabled class="flex w-full items-center gap-3 rounded-xl px-3 py-2.5 text-xs text-slate-400" title="Logout tersedia setelah autentikasi dihubungkan"><LogOut class="size-[18px]" />Keluar<span class="ml-auto text-[9px]">Mode pratinjau</span></button></div>
            </div>
        </Transition>
    </div>
</template>
