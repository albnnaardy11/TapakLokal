<script setup>
import { CalendarDays, ChevronDown, MapPin, Search, ShoppingBag, TentTree, X } from 'lucide-vue-next';
import { computed, ref } from 'vue';

const activeType = ref('open-trip');
const destination = ref('');
const departureDate = ref('');
const category = ref('');
const searchMessage = ref('');
const destinationInput = ref(null);

const selectDestination = (name) => {
    activeType.value = 'open-trip';
    destination.value = name;
    searchMessage.value = name ? `Destinasi ${name} dipilih. Atur tanggal dan kategori, lalu klik Cari Sekarang.` : '';
    destinationInput.value?.focus({ preventScroll: true });
    destinationInput.value?.scrollIntoView({ behavior: window.matchMedia('(prefers-reduced-motion: reduce)').matches ? 'instant' : 'smooth', block: 'center' });
};

defineExpose({ selectDestination });

const tripTypes = [
    { id: 'open-trip', label: 'Open Trip', icon: TentTree },
    { id: 'private-trip', label: 'Private Trip', icon: MapPin },
    { id: 'oleh-oleh', label: 'Oleh-Oleh', icon: ShoppingBag },
];

const destinationPlaceholder = computed(() => activeType.value === 'oleh-oleh' ? 'Cari oleh-oleh khas daerah...' : 'Cari destinasi atau aktivitas...');

const searchTrips = () => {
    const term = destination.value.trim() || 'semua destinasi';
    const date = departureDate.value ? new Intl.DateTimeFormat('id-ID', { dateStyle: 'long' }).format(new Date(`${departureDate.value}T00:00:00`)) : 'tanggal fleksibel';
    const selectedCategory = category.value || 'semua kategori';
    searchMessage.value = `Menampilkan ${activeType.value.replace('-', ' ')} untuk ${term}, ${date}, ${selectedCategory}.`;
};
</script>

<template>
    <section class="absolute -bottom-4 left-1/2 z-20 w-[calc(100%-2rem)] max-w-[1080px] -translate-x-1/2 rounded-2xl bg-white/95 p-4 shadow-[0_12px_28px_rgba(22,53,102,0.18)] backdrop-blur sm:-bottom-3 sm:w-[calc(100%-4rem)] sm:p-5">
        <div class="flex gap-1.5 overflow-x-auto pb-2">
            <button v-for="type in tripTypes" :key="type.id" class="inline-flex shrink-0 items-center gap-1.5 rounded-lg px-3 py-1.5 text-[11px] font-bold transition" :class="activeType === type.id ? 'bg-[#3E7BEF] text-white shadow-sm' : 'bg-[#eef4ff] text-slate-500 hover:bg-[#dfeaff]'" @click="activeType = type.id; searchMessage = ''"><component :is="type.icon" class="size-3.5" />{{ type.label }}</button>
        </div>

        <form class="grid gap-2 rounded-xl bg-[#f8faff] p-2 sm:grid-cols-[minmax(0,1.7fr)_minmax(0,0.95fr)_minmax(0,1.05fr)_auto] sm:p-2.5" @submit.prevent="searchTrips">
            <label class="flex min-w-0 items-center gap-2 rounded-lg bg-white px-3 py-3 text-slate-400 shadow-[0_1px_5px_rgba(62,123,239,0.12)] ring-1 ring-slate-100 transition focus-within:ring-[#3E7BEF]/50"><Search class="size-4 shrink-0" /><input ref="destinationInput" v-model="destination" aria-label="Destinasi atau aktivitas" class="min-w-0 flex-1 bg-transparent text-xs text-slate-700 outline-none placeholder:text-slate-400" :placeholder="destinationPlaceholder" /></label>
            <label class="flex min-w-0 items-center gap-2 rounded-lg bg-white px-3 py-3 text-left text-xs text-slate-400 shadow-[0_1px_5px_rgba(62,123,239,0.12)] ring-1 ring-slate-100"><CalendarDays class="size-4 shrink-0" /><input v-model="departureDate" type="date" class="min-w-0 flex-1 bg-transparent text-xs text-slate-600 outline-none" aria-label="Tanggal keberangkatan" /><button v-if="departureDate" type="button" class="text-slate-300 hover:text-[#3E7BEF]" aria-label="Hapus tanggal" @click="departureDate = ''"><X class="size-3.5" /></button></label>
            <label class="flex min-w-0 items-center gap-2 rounded-lg bg-white px-3 py-3 text-left text-xs text-slate-400 shadow-[0_1px_5px_rgba(62,123,239,0.12)] ring-1 ring-slate-100"><ShoppingBag class="size-4 shrink-0" /><select v-model="category" class="min-w-0 flex-1 appearance-none bg-transparent text-xs text-slate-600 outline-none" aria-label="Kategori"><option value="">Semua Kategori</option><option value="wisata-alam">Wisata Alam</option><option value="budaya">Budaya & Sejarah</option><option value="kuliner">Kuliner Lokal</option><option value="petualangan">Petualangan</option></select><ChevronDown class="size-4 shrink-0" /></label>
            <button class="flex items-center justify-center gap-2 rounded-lg bg-[#3E7BEF] px-6 py-3 text-xs font-bold text-white shadow-[0_5px_12px_rgba(62,123,239,0.35)] transition hover:bg-[#2868df] focus:outline-none focus:ring-4 focus:ring-[#3E7BEF]/30 sm:min-w-36"><span>Cari Sekarang</span><span>→</span></button>
        </form>
        <p v-if="searchMessage" class="mt-2 rounded-lg bg-[#edf3ff] px-3 py-2 text-xs font-medium text-[#2868df]">{{ searchMessage }}</p>
    </section>
</template>
