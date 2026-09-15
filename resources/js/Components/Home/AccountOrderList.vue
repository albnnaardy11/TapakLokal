<script setup>
import { computed, ref, watch } from 'vue';
import { Link } from '@inertiajs/vue3';
import { CalendarDays, ChevronLeft, ChevronRight, ClipboardList, MapPin, Users } from 'lucide-vue-next';

const props = defineProps({ orders: { type: Array, required: true } });
defineEmits(['detail']);
const currentPage = ref(1);
const pageSize = 5;
const scrollArea = ref(null);
const pageCount = computed(() => Math.max(1, Math.ceil(props.orders.length / pageSize)));
const start = computed(() => (currentPage.value - 1) * pageSize);
const visibleOrders = computed(() => props.orders.slice(start.value, start.value + pageSize));
const pageNumbers = computed(() => {
    const first = Math.max(1, Math.min(currentPage.value - 2, pageCount.value - 4));
    return Array.from({ length: Math.min(5, pageCount.value) }, (_, index) => first + index);
});
watch(() => props.orders, () => { currentPage.value = 1; scrollArea.value?.scrollTo({ top: 0 }); });
const goToPage = (number) => {
    currentPage.value = Math.max(1, Math.min(number, pageCount.value));
    scrollArea.value?.scrollTo({ top: 0 });
};
</script>

<template>
    <section aria-label="Daftar pemesanan saya" class="overflow-hidden rounded-2xl border border-[#e1eaf5] bg-white shadow-[0_4px_20px_rgba(23,75,120,0.04)]">
        <div class="flex items-center justify-between gap-3 border-b border-slate-100 px-4 py-3">
            <span class="flex items-center gap-2 text-xs font-semibold text-[#183660]"><ClipboardList class="size-4 text-[#3E7BEF]" />Daftar pesanan</span>
            <span class="rounded-full bg-[#edf4ff] px-2.5 py-1 text-[10px] font-medium text-[#3E7BEF]" aria-live="polite">{{ orders.length }} pesanan</span>
        </div>
        <div ref="scrollArea" tabindex="0" role="region" aria-label="Daftar pesanan, dapat digulir" class="max-h-[420px] overflow-y-auto overscroll-contain bg-[#f8fafc] p-3 focus-visible:outline-2 focus-visible:outline-offset-[-2px] focus-visible:outline-[#3E7BEF] [scrollbar-width:thin] [scrollbar-color:#b9d5fa_transparent] sm:p-4">
            <div v-if="visibleOrders.length" class="space-y-3">
                <article v-for="order in visibleOrders" :key="order.id" class="flex flex-wrap gap-3 rounded-xl border border-[#e1eaf5] bg-white p-3 transition-colors hover:border-[#aacbff]">
                    <div class="relative h-24 w-28 shrink-0 overflow-hidden rounded-lg sm:w-36">
                        <img :src="order.image" :alt="order.name" loading="lazy" class="size-full object-cover" />
                        <span class="absolute left-2 top-2 rounded-md px-2 py-1 text-[9px] font-semibold" :class="order.status === 'Selesai' ? 'bg-emerald-500 text-white' : order.status === 'Batal' ? 'bg-rose-50 text-rose-600' : 'bg-blue-50 text-blue-600'">{{ order.status }}</span>
                    </div>
                    <div class="min-w-[120px] flex-1 py-1">
                        <h3 class="text-xs font-bold text-[#183660]">{{ order.name }}</h3>
                        <p class="mt-1 flex items-start gap-1 text-[10px] text-slate-500"><MapPin class="size-3 shrink-0" />{{ order.location }}</p>
                        <p class="mt-2 flex flex-wrap items-center gap-2 text-[10px] text-slate-500"><span class="flex items-center gap-1"><CalendarDays class="size-3" />{{ order.date }}</span><span class="flex items-center gap-1"><Users class="size-3" />{{ order.travelers }} Orang</span></p>
                        <span v-if="order.points" class="mt-2 inline-block rounded-md bg-teal-50 px-2 py-1 text-[9px] text-teal-600">+{{ order.points }} Poin didapat</span>
                    </div>
                    <div class="flex w-full flex-wrap items-center justify-between gap-2 border-t border-slate-100 pt-3 sm:w-auto sm:flex-col sm:items-end sm:justify-center sm:border-0 sm:pt-0">
                        <div class="sm:text-right"><strong class="text-xs text-[#183660]">{{ order.price }}</strong><p class="mt-1 text-[9px] text-teal-600">{{ order.payment }}</p></div>
                        <div class="flex gap-2"><button type="button" class="rounded-lg border border-sky-100 px-3 py-2 text-[10px] font-semibold text-[#078cff] transition-colors hover:bg-sky-50" @click="$emit('detail', order)">Detail</button><Link href="/" class="rounded-lg bg-[#078cff] px-3 py-2 text-[10px] font-semibold text-white transition-colors hover:bg-[#0878db]">Pesan Lagi</Link></div>
                    </div>
                </article>
            </div>
            <div v-else class="flex flex-col items-center gap-2 py-8 text-center"><ClipboardList class="size-8 text-[#a8c9f7]" /><p class="text-xs font-semibold text-slate-600">Belum ada pesanan untuk filter ini.</p><p class="text-[11px] text-slate-400">Coba pilih status atau jenis pesanan lainnya.</p></div>
        </div>
        <footer v-if="orders.length" class="flex flex-wrap items-center justify-between gap-3 border-t border-slate-100 px-4 py-3">
            <p class="text-[10px] text-slate-500" aria-live="polite">Menampilkan {{ start + 1 }}–{{ Math.min(start + pageSize, orders.length) }} dari {{ orders.length }} pesanan</p>
            <nav v-if="pageCount > 1" aria-label="Halaman pesanan" class="flex items-center gap-1">
                <button type="button" aria-label="Halaman sebelumnya" :disabled="currentPage === 1" class="grid size-8 place-items-center rounded-full text-[#3E7BEF] hover:bg-blue-50 disabled:cursor-not-allowed disabled:opacity-30" @click="goToPage(currentPage - 1)"><ChevronLeft class="size-4" /></button>
                <button v-for="number in pageNumbers" :key="number" type="button" :aria-label="`Halaman ${number}`" :aria-current="currentPage === number ? 'page' : undefined" class="grid size-8 place-items-center rounded-full text-xs font-semibold transition-colors" :class="currentPage === number ? 'bg-[#3E7BEF] text-white' : 'text-slate-500 hover:bg-blue-50 hover:text-[#3E7BEF]'" @click="goToPage(number)">{{ number }}</button>
                <button type="button" aria-label="Halaman berikutnya" :disabled="currentPage === pageCount" class="grid size-8 place-items-center rounded-full text-[#3E7BEF] hover:bg-blue-50 disabled:cursor-not-allowed disabled:opacity-30" @click="goToPage(currentPage + 1)"><ChevronRight class="size-4" /></button>
            </nav>
        </footer>
    </section>
</template>
