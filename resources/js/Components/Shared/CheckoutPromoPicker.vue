<script setup>
import { computed, ref, watch } from 'vue';
import { useForm } from '@inertiajs/vue3';
import { route } from 'ziggy-js';
import { BadgePercent, Check, ChevronRight, Clock3, TicketPercent, X } from 'lucide-vue-next';

const props = defineProps({ kind: String, orderId: Number, promotions: { type: Array, default: () => [] }, appliedPromotion: Object, discount: Number, disabled: Boolean, locked: Boolean });
const dialog = ref(null);
const form = useForm({ promotion_code: props.appliedPromotion?.code || '' });
const money = value => new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', maximumFractionDigits: 0 }).format(value || 0);
const normalizedCode = computed(() => form.promotion_code.trim().toUpperCase());
const selectedPromotion = computed(() => props.promotions.find(promo => promo.code === normalizedCode.value));
const expiry = value => new Intl.DateTimeFormat('id-ID', { day: 'numeric', month: 'short', year: 'numeric' }).format(new Date(value));
watch(() => props.appliedPromotion, value => { form.promotion_code = value?.code || ''; });
const open = () => { form.clearErrors(); form.promotion_code = props.appliedPromotion?.code || ''; dialog.value.showModal(); };
const choose = code => { form.promotion_code = code; form.clearErrors(); };
const apply = (remove = false) => {
    if (props.locked || props.disabled || form.processing) return;
    form.promotion_code = remove === true ? '' : normalizedCode.value;
    form.put(route('checkout.promotion', { type: props.kind, id: props.orderId }), { preserveScroll: true, onSuccess: () => dialog.value.close() });
};
</script>

<template>
    <div class="mt-6">
        <button type="button" :disabled="disabled" class="group flex w-full items-center gap-4 rounded-xl border border-[#d5e4fc] bg-[#edf4ff] px-4 py-4 text-left transition hover:border-[#3e7bef] hover:bg-[#e5efff] focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-[#3e7bef] disabled:opacity-60" aria-haspopup="dialog" @click="open">
            <span class="grid size-11 shrink-0 place-items-center rounded-xl bg-white text-[#3e7bef] shadow-xs"><TicketPercent class="size-6" /></span>
            <span class="min-w-0 flex-1"><span class="block text-sm font-bold text-[#17345e]">{{ appliedPromotion ? `Promo ${appliedPromotion.code} terpasang` : 'Pakai promo, perjalanan lebih hemat' }}</span><span class="mt-1 block text-xs leading-5 text-[#526b8e]">{{ appliedPromotion ? `Kamu hemat ${money(discount)}` : 'Masukkan kode atau pilih promo yang tersedia' }}</span></span>
            <ChevronRight class="size-5 shrink-0 text-[#3e7bef] transition group-hover:translate-x-0.5" />
        </button>
    </div>

    <dialog ref="dialog" class="fixed inset-0 m-auto max-h-[90dvh] w-[calc(100%-2rem)] max-w-xl overflow-hidden rounded-2xl bg-white p-0 text-[#17345e] shadow-2xl backdrop:bg-slate-900/50" aria-labelledby="checkout-promo-title" @click.self="dialog.close()" @cancel="form.processing && $event.preventDefault()">
        <div class="flex max-h-[90dvh] flex-col">
            <header class="flex shrink-0 items-start justify-between gap-4 border-b border-slate-100 px-5 py-5 sm:px-6">
                <div><div class="flex items-center gap-2"><BadgePercent class="size-6 text-[#3e7bef]" /><h2 id="checkout-promo-title" class="text-xl font-extrabold">Promo untuk pesananmu</h2></div><p class="mt-2 text-xs leading-5 text-slate-500">Pilih satu promo terbaik sebelum membuat kode pembayaran.</p></div>
                <button type="button" :disabled="form.processing" aria-label="Tutup promo" class="grid size-9 shrink-0 place-items-center rounded-full text-slate-500 hover:bg-slate-100 focus-visible:outline-2 focus-visible:outline-[#3e7bef]" @click="dialog.close()"><X class="size-5" /></button>
            </header>
            <div class="min-h-0 overflow-y-auto px-5 py-5 sm:px-6">
                <p v-if="locked" role="status" class="mb-5 rounded-xl bg-amber-50 p-4 text-xs leading-6 text-amber-900">Kode pembayaran sudah dibuat. Promo dan nominal pesanan tidak dapat diubah lagi.</p>
                <form v-if="kind === 'trip'" @submit.prevent="apply()">
                    <label for="checkout-promo-code" class="mb-2 block text-xs font-bold text-slate-600">Punya kode promo?</label>
                    <div class="flex gap-2"><input id="checkout-promo-code" v-model="form.promotion_code" :disabled="locked || form.processing" maxlength="50" autocomplete="off" placeholder="Masukkan kode promo" class="min-w-0 flex-1 rounded-xl border border-slate-200 bg-white px-4 py-3 text-sm uppercase outline-none placeholder:normal-case focus:border-[#3e7bef] focus:ring-2 focus:ring-blue-100 disabled:bg-slate-50" @input="form.clearErrors()" /><button :disabled="!normalizedCode || locked || form.processing" class="rounded-xl bg-[#3e7bef] px-4 text-sm font-bold text-white hover:bg-[#3268ce] disabled:opacity-50">Pakai</button></div>
                    <p v-if="form.errors.promotion_code" role="alert" class="mt-3 text-xs leading-5 text-red-600">{{ form.errors.promotion_code }}</p>
                </form>
                <div class="mb-4 mt-6 flex items-center justify-between gap-3"><h3 class="text-sm font-bold">Promo yang tersedia</h3><span class="rounded-full bg-blue-50 px-2.5 py-1 text-[11px] font-bold text-[#3e7bef]">{{ promotions.length }} promo</span></div>
                <div v-if="!promotions.length" class="my-2 flex flex-col items-center justify-center rounded-2xl border border-slate-200/80 bg-gradient-to-b from-slate-50/60 to-white px-6 py-8 text-center shadow-2xs">
                    <div class="relative mb-4 flex size-28 items-center justify-center">
                        <div class="absolute inset-0 rounded-full bg-gradient-to-tr from-sky-100 via-blue-50 to-indigo-100/60 blur-xl"></div>
                        <svg class="relative size-24 drop-shadow-sm" viewBox="0 0 120 120" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                            <defs>
                                <linearGradient id="promo-bg-ticket" x1="15" y1="20" x2="85" y2="85" gradientUnits="userSpaceOnUse">
                                    <stop stop-color="#bfdbfe" />
                                    <stop offset="1" stop-color="#93c5fd" />
                                </linearGradient>
                                <linearGradient id="promo-main-ticket" x1="25" y1="35" x2="100" y2="100" gradientUnits="userSpaceOnUse">
                                    <stop stop-color="#38bdf8" />
                                    <stop offset="1" stop-color="#0284c7" />
                                </linearGradient>
                                <linearGradient id="promo-coin" x1="68" y1="18" x2="98" y2="48" gradientUnits="userSpaceOnUse">
                                    <stop stop-color="#fde047" />
                                    <stop offset="1" stop-color="#f59e0b" />
                                </linearGradient>
                                <filter id="promo-shadow" x="10" y="20" width="100" height="90" filterUnits="userSpaceOnUse" color-interpolation-filters="sRGB">
                                    <feDropShadow dx="0" dy="6" stdDeviation="4" flood-color="#0369a1" flood-opacity="0.18" />
                                </filter>
                            </defs>
                            <g transform="rotate(-14 48 56)">
                                <path d="M22 36C22 32.6863 24.6863 30 28 30H78C81.3137 30 84 32.6863 84 36V45C81.2386 45 79 47.2386 79 50C79 52.7614 81.2386 55 84 55V64C84 67.3137 81.3137 70 78 70H28C24.6863 70 22 67.3137 22 64V55C24.7614 55 27 52.7614 27 50C27 47.2386 24.7614 45 22 45V36Z" fill="url(#promo-bg-ticket)" opacity="0.65" />
                            </g>
                            <g transform="rotate(8 60 62)" filter="url(#promo-shadow)">
                                <path d="M30 42C30 38.6863 32.6863 36 36 36H90C93.3137 36 96 38.6863 96 42V51.5C92.6863 51.5 90 54.1863 90 57.5C90 60.8137 92.6863 63.5 96 63.5V73C96 76.3137 93.3137 79 90 79H36C32.6863 79 30 76.3137 30 73V63.5C33.3137 63.5 36 60.8137 36 57.5C36 54.1863 33.3137 51.5 30 51.5V42Z" fill="url(#promo-main-ticket)" />
                                <line x1="72" y1="39" x2="72" y2="76" stroke="#ffffff" stroke-opacity="0.45" stroke-dasharray="3 3" stroke-width="1.5" />
                                <rect x="40" y="47" width="22" height="4" rx="2" fill="#ffffff" fill-opacity="0.9" />
                                <rect x="40" y="55" width="15" height="3" rx="1.5" fill="#ffffff" fill-opacity="0.6" />
                                <rect x="40" y="62" width="20" height="3" rx="1.5" fill="#ffffff" fill-opacity="0.6" />
                                <circle cx="83" cy="57.5" r="5" fill="#ffffff" fill-opacity="0.25" />
                                <path d="M81 59.5L85 55.5M81.5 56H81.51M84.5 59H84.51" stroke="#ffffff" stroke-width="1.2" stroke-linecap="round" />
                            </g>
                            <g transform="translate(0, -2)">
                                <circle cx="86" cy="32" r="14" fill="#d97706" opacity="0.3" />
                                <circle cx="86" cy="30" r="13" fill="url(#promo-coin)" />
                                <circle cx="86" cy="30" r="10.5" stroke="#fef08a" stroke-width="1.2" stroke-opacity="0.8" fill="none" />
                                <path d="M82.5 33.5L89.5 26.5" stroke="#ffffff" stroke-width="2" stroke-linecap="round" />
                                <circle cx="83.5" cy="27.5" r="1.5" fill="#ffffff" />
                                <circle cx="88.5" cy="32.5" r="1.5" fill="#ffffff" />
                            </g>
                            <path d="M22 26C22 26 23.5 29 25 30C26.5 31 29 31.5 29 31.5C29 31.5 26.5 32 25 33C23.5 34 22 37 22 37C22 37 20.5 34 19 33C17.5 32 15 31.5 15 31.5C15 31.5 17.5 31 19 30C20.5 29 22 26 22 26Z" fill="#38bdf8" />
                            <path d="M101 78C101 78 101.8 79.5 102.5 80C103.2 80.5 104.5 80.8 104.5 80.8C104.5 80.8 103.2 81.1 102.5 81.6C101.8 82.1 101 83.6 101 83.6C101 83.6 100.2 82.1 99.5 81.6C98.8 81.1 97.5 80.8 97.5 80.8C97.5 80.8 98.8 80.5 99.5 80C100.2 79.5 101 78 101 78Z" fill="#fbbf24" />
                            <circle cx="106" cy="42" r="2" fill="#818cf8" opacity="0.8" />
                            <circle cx="20" cy="74" r="2" fill="#38bdf8" opacity="0.6" />
                        </svg>
                    </div>
                    <h4 class="text-sm font-bold text-[#17345e]">{{ kind === 'trip' ? 'Belum ada promo aktif' : 'Promo oleh-oleh belum tersedia' }}</h4>
                    <p class="mt-2 max-w-xs text-xs leading-relaxed text-slate-500">{{ kind === 'trip' ? 'Jika punya kode promo, masukkan pada kolom di atas. Kamu tetap bisa melanjutkan pembayaran.' : 'Kamu tetap bisa melanjutkan pembayaran dengan harga pada ringkasan pesanan.' }}</p>
                </div>
                <div v-else class="space-y-3">
                    <button v-for="promo in promotions" :key="promo.code" type="button" :disabled="!promo.eligible || locked || form.processing" class="w-full rounded-xl border p-4 text-left transition focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-[#3e7bef] disabled:cursor-not-allowed" :class="normalizedCode === promo.code ? 'border-[#3e7bef] bg-blue-50/60 ring-1 ring-[#3e7bef]' : 'border-slate-200 hover:border-blue-300'" @click="choose(promo.code)">
                        <div class="flex items-start gap-3"><span class="grid size-10 shrink-0 place-items-center rounded-lg bg-blue-50 text-[#3e7bef]"><TicketPercent class="size-5" /></span><div class="min-w-0 flex-1"><p class="text-base font-extrabold">{{ promo.type === 'percent' ? `Diskon ${promo.value}%` : `Diskon ${money(promo.value)}` }}</p><p class="mt-1 text-xs leading-5 text-slate-500">{{ promo.name }}</p></div><span class="grid size-5 shrink-0 place-items-center rounded-full border" :class="normalizedCode === promo.code ? 'border-[#3e7bef] bg-[#3e7bef] text-white' : 'border-slate-300'"><Check v-if="normalizedCode === promo.code" class="size-3.5" /></span></div>
                        <div class="mt-4 flex flex-wrap items-center justify-between gap-2 border-t border-dashed border-slate-200 pt-3"><span class="rounded-md bg-slate-100 px-2.5 py-1 font-mono text-xs font-bold tracking-wide">{{ promo.code }}</span><span class="flex items-center gap-1 text-[11px] text-slate-500"><Clock3 class="size-3.5" />Berlaku sampai {{ expiry(promo.ends_at) }}</span></div>
                        <p class="mt-3 text-xs text-slate-500">Minimum transaksi {{ money(promo.minimum_amount) }}<template v-if="promo.maximum_discount"> · Maks. {{ money(promo.maximum_discount) }}</template></p>
                        <p class="mt-2 text-xs font-semibold" :class="promo.eligible ? 'text-[#3e7bef]' : 'text-slate-500'">{{ promo.eligible ? `Hemat ${money(promo.discount)} untuk pesanan ini` : 'Pesanan belum memenuhi syarat promo ini' }}</p>
                    </button>
                </div>
            </div>
            <footer class="shrink-0 border-t border-slate-100 bg-white px-5 py-4 sm:px-6">
                <div v-if="selectedPromotion?.eligible && !locked" class="mb-3 flex items-center justify-between text-xs"><span class="text-slate-500">Potongan untuk pesananmu</span><strong class="text-[#3e7bef]">−{{ money(selectedPromotion.discount) }}</strong></div>
                <button v-if="kind === 'trip' && !locked" type="button" :disabled="!normalizedCode || selectedPromotion?.eligible === false || form.processing" class="min-h-12 w-full rounded-xl bg-[#3e7bef] text-sm font-bold text-white hover:bg-[#3268ce] disabled:opacity-50" @click="apply()">{{ form.processing ? 'Menerapkan promo…' : 'Terapkan promo' }}</button>
                <button v-else type="button" class="min-h-12 w-full rounded-xl bg-[#3e7bef] text-sm font-bold text-white" @click="dialog.close()">Kembali ke pembayaran</button>
                <button v-if="appliedPromotion && !locked" type="button" :disabled="form.processing" class="mt-3 min-h-10 w-full text-xs font-semibold text-slate-500 hover:text-red-600" @click="apply(true)">Hapus promo yang terpasang</button>
            </footer>
        </div>
    </dialog>
</template>
