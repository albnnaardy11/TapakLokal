<script setup>
import { Head, router, usePage } from '@inertiajs/vue3';
import { computed, onMounted, ref } from 'vue';
import { route } from 'ziggy-js';
import { ArrowRight, ShieldCheck, X } from 'lucide-vue-next';
import CorporateLogin from './CorporateLogin.vue';

const props = defineProps({ currentPortal: String, targetPortal: String });
const page = usePage();
const dialog = ref(null);
const busy = ref(false);
const labels = { traveler: 'Traveler', corporate: 'Corporate', vendor: 'Vendor', admin: 'Admin' };
const homes = { traveler: 'account', corporate: 'corporate.dashboard', vendor: 'vendor.dashboard', admin: 'admin.dashboard' };
const initials = computed(() => (page.props.auth?.user?.name || 'Akun').split(/\s+/).slice(0, 2).map(word => word[0]).join('').toUpperCase());

onMounted(() => dialog.value.showModal());
function stay() {
    if (!busy.value) router.visit(route(homes[props.currentPortal] || 'account'));
}
function logout() {
    busy.value = true;
    router.post(route('logout'), { switch_portal: props.targetPortal }, { onFinish: () => busy.value = false });
}
</script>
<template>
    <div inert aria-hidden="true" class="pointer-events-none h-screen overflow-hidden select-none">
        <CorporateLogin v-if="targetPortal==='corporate'" />
        <div v-else class="min-h-screen bg-[#f7f9fc]"><header class="border-b border-slate-200 bg-white px-10 py-6"><img src="/Assets/Images/logo.webp" alt="TapakLokal Logo" class="h-8 w-auto object-contain" /></header></div>
    </div>
    <Head title="Konfirmasi perpindahan akun — TapakLokal" />
    <dialog ref="dialog" aria-labelledby="portal-switch-title" aria-describedby="portal-switch-description" class="portal-switch-dialog m-auto max-h-[calc(100dvh-32px)] w-[calc(100%-32px)] max-w-[460px] overflow-y-auto rounded-[28px] border border-white/80 bg-white p-0 text-[#173451] shadow-[0_24px_80px_rgba(10,30,58,0.3)] backdrop:bg-slate-900/60 backdrop:backdrop-blur-sm sm:rounded-[32px]" @cancel.prevent="stay">
        <button type="button" :disabled="busy" autofocus aria-label="Tutup dan tetap di akun saat ini" class="absolute right-4 top-4 z-10 grid size-9 place-items-center rounded-full bg-white/80 text-slate-500 transition hover:bg-white hover:text-slate-800 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-blue-500 disabled:opacity-50" @click="stay"><X class="size-5" /></button>
        <div class="relative overflow-hidden bg-gradient-to-b from-[#e3f4ff] via-[#f1f9ff] to-white px-7 pt-6 sm:px-9">
            <svg aria-hidden="true" viewBox="0 0 300 158" fill="none" xmlns="http://www.w3.org/2000/svg" class="mx-auto h-[145px] w-[275px] max-w-full">
                <ellipse cx="150" cy="141" rx="108" ry="8" fill="#D9EAF5" fill-opacity=".65" />
                <circle cx="156" cy="75" r="65" fill="#D9F0FF" />
                <path d="M37 53h20M47 43v20M258 95h12M264 89v12" stroke="#8CCAF1" stroke-width="3" stroke-linecap="round" />
                <circle cx="242" cy="35" r="4" fill="#FFC65C" />
                <g transform="rotate(-9 105 85)">
                    <rect x="58" y="33" width="91" height="103" rx="14" fill="#BDDCF0" />
                    <rect x="58" y="29" width="91" height="103" rx="14" fill="white" stroke="#AED6EF" stroke-width="1.5" />
                    <rect x="58" y="29" width="91" height="22" rx="14" fill="#CBEAFF" />
                    <path d="M58 43h91v8H58z" fill="#CBEAFF" />
                    <circle cx="72" cy="40" r="2.5" fill="#67B8EE" />
                    <circle cx="81" cy="40" r="2.5" fill="#67B8EE" />
                    <circle cx="103" cy="78" r="15" fill="#E5F4FF" />
                    <circle cx="103" cy="73" r="5" fill="#49A9EA" />
                    <path d="M93 87c0-11 20-11 20 0" fill="#49A9EA" />
                    <path d="M79 108h49M88 118h31" stroke="#D7E6F1" stroke-width="4" stroke-linecap="round" />
                </g>
                <g transform="rotate(8 205 85)">
                    <rect x="167" y="42" width="78" height="95" rx="13" fill="#A7D2ED" />
                    <rect x="167" y="38" width="78" height="95" rx="13" fill="#0088FF" />
                    <rect x="180" y="53" width="52" height="40" rx="7" fill="#EAF7FF" />
                    <path d="M190 82V68h12v14m0 0V61h18v21M187 83h36" stroke="#0088FF" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" />
                    <path d="M179 107h49M188 118h30" stroke="#91D2FF" stroke-width="4" stroke-linecap="round" />
                </g>
                <circle cx="149" cy="109" r="24" fill="white" stroke="#D6EAF6" stroke-width="1.5" />
                <rect x="137" y="105" width="24" height="19" rx="5" fill="#FFBD4B" />
                <path d="M142 105v-6a7 7 0 0 1 14 0v6" stroke="#D79824" stroke-width="3.5" stroke-linecap="round" />
                <circle cx="149" cy="113" r="2" fill="#845717" />
                <path d="M149 114v3" stroke="#845717" stroke-width="2" stroke-linecap="round" />
                <path d="M139 42c13-12 28-9 36 0m-1-9 3 10-10 1" stroke="#369EE9" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" />
            </svg>
            <h1 id="portal-switch-title" class="mt-2 text-center text-[25px] font-bold leading-[1.3] tracking-tight sm:text-[27px]">Mau beralih ke {{ labels[targetPortal] }}?</h1>
            <p id="portal-switch-description" class="mx-auto mt-3 max-w-[330px] text-center text-sm leading-6 text-[#6b829e]">Keluar dari sesi {{ labels[currentPortal] }} terlebih dahulu, lalu masuk ke akun {{ labels[targetPortal] }}.</p>
        </div>
        <div class="px-7 pb-7 pt-5 sm:px-9 sm:pb-8">
            <div class="flex items-center gap-3 rounded-2xl border border-[#e0eaf4] bg-[#f7faff] p-4">
                <span class="grid size-11 shrink-0 place-items-center rounded-full bg-[#dcefff] text-sm font-bold text-[#077acc]">{{ initials }}</span>
                <div class="min-w-0 flex-1"><p class="truncate text-sm font-bold">{{ page.props.auth?.user?.name }}</p><p class="mt-1 truncate text-xs text-[#6b829e]">{{ page.props.auth?.user?.email }}</p></div>
                <span class="shrink-0 rounded-full bg-white px-2.5 py-1 text-[10px] font-semibold text-[#43769d] ring-1 ring-[#e0eaf4]">{{ labels[currentPortal] }}</span>
            </div>
            <div class="mt-4 flex items-start gap-2.5 text-xs leading-5 text-[#6b829e]"><ShieldCheck class="mt-0.5 size-4 shrink-0 text-[#3a93cc]" /><p>Data dan pesananmu tetap tersimpan. Sesi akun saat ini akan berakhir di semua tab browser ini.</p></div>
            <button :disabled="busy" class="mt-6 flex min-h-12 w-full items-center justify-center gap-2 rounded-full bg-[#0088ff] px-5 py-3 text-sm font-bold text-white transition hover:bg-[#0077df] focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-blue-500 disabled:cursor-wait disabled:opacity-60" @click="logout">{{ busy ? 'Mengakhiri sesi…' : 'Keluar & lanjut ke '+labels[targetPortal] }}<ArrowRight v-if="!busy" class="size-4" /></button>
            <button :disabled="busy" class="mt-2 min-h-11 w-full rounded-full px-4 py-2 text-sm font-bold text-[#0077cc] transition hover:bg-sky-50 focus-visible:outline-2 focus-visible:outline-blue-500 disabled:opacity-50" @click="stay">Tetap di akun ini</button>
        </div>
    </dialog>
</template>
<style scoped>
.portal-switch-dialog[open] { animation: portal-dialog-enter 180ms ease-out; }
@keyframes portal-dialog-enter { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
@media (prefers-reduced-motion: reduce) { .portal-switch-dialog[open] { animation: none; } }
</style>
