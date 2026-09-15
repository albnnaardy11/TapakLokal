<script setup>
import { computed, ref } from 'vue';
import { ArrowRight, ChevronDown, CircleHelp } from 'lucide-vue-next';

const props = defineProps({
    tripType: { type: String, required: true },
});

const openItems = ref([]);
const isPrivateTrip = computed(() => props.tripType === 'private-trip');
const questions = computed(() => isPrivateTrip.value ? [
    { question: 'Apa itu Private Trip Labuan Bajo?', answer: 'Private Trip adalah perjalanan khusus untuk rombonganmu dengan rute dan waktu yang dapat disesuaikan bersama partner lokal.' },
    { question: 'Berapa lama durasi private trip ini?', answer: 'Durasi perjalanan adalah 3 hari 2 malam sesuai program yang ditampilkan pada itinerary.' },
    { question: 'Di mana meeting point private trip?', answer: 'Titik kumpul berada di Pelabuhan Labuan Bajo. Detail koordinat tersedia pada tab Lokasi.' },
    { question: 'Apa saja yang termasuk dalam harga paket?', answer: 'Paket mencakup fasilitas sesuai rincian pada tab Fasilitas, termasuk transportasi utama dan aktivitas yang tercantum.' },
    { question: 'Apakah jadwal perjalanan bisa disesuaikan?', answer: 'Ya. Karena ini private trip, beberapa rute dan aktivitas dapat didiskusikan sebelum pemesanan dikonfirmasi.' },
    { question: 'Destinasi apa saja yang dikunjungi?', answer: 'Rute mencakup pilihan spot Labuan Bajo seperti Pulau Kelor, Pink Beach, dan Pulau Kalong sesuai jadwal perjalanan.' },
] : [
    { question: 'Apa itu tur Pulau start Dermaga Kaliadem?', answer: 'Open Trip Pulau Pramuka adalah perjalanan bersama menuju Kepulauan Seribu yang dimulai dari Dermaga Kaliadem.' },
    { question: 'Berapa lama durasi open trip Pulau Pramuka?', answer: 'Durasi perjalanan adalah 2 hari 1 malam. Agenda lengkap dapat dilihat pada tab Itinerary.' },
    { question: 'Di mana meeting point tur Pulau Pramuka?', answer: 'Titik kumpul berada di Dermaga Kaliadem. Koordinat dan panduan navigasi tersedia pada tab Lokasi.' },
    { question: 'Apa saja yang termasuk dalam harga open trip Pulau Pramuka?', answer: 'Fasilitas utama seperti penyeberangan, homestay, makan sesuai program, alat snorkeling, dan pendamping lokal sudah tercantum pada paket.' },
    { question: 'Apa saja yang tidak termasuk dalam tur Pulau Pramuka ini?', answer: 'Pengeluaran pribadi, transportasi menuju meeting point, serta kebutuhan di luar program tidak termasuk dalam paket.' },
    { question: 'Destinasi apa saja yang dikunjungi di open trip Pulau Pramuka?', answer: 'Kunjungan mencakup Pulau Pramuka, Pulau Semak Daun, spot snorkeling, dan titik sunset sesuai kondisi perjalanan.' },
]);

const allOpen = computed(() => openItems.value.length === questions.value.length);

const toggleItem = (index) => {
    openItems.value = openItems.value.includes(index)
        ? openItems.value.filter((item) => item !== index)
        : [...openItems.value, index];
};

const toggleAll = () => {
    openItems.value = allOpen.value ? [] : questions.value.map((_, index) => index);
};
</script>

<template>
    <section class="mt-7 overflow-hidden rounded-2xl border border-[#dfeaf5] bg-white shadow-[0_10px_28px_rgba(23,75,120,0.05)]" aria-labelledby="trip-faq-heading">
        <header class="flex flex-wrap items-center justify-between gap-4 border-b border-[#e8f0f7] px-5 py-4 sm:px-6"><div class="flex items-center gap-3"><span class="grid size-8 place-items-center rounded-lg bg-[#edf7ff] text-[#1688e8]"><CircleHelp class="size-4" :stroke-width="2.5" /></span><div><p class="text-[10px] font-bold uppercase tracking-[0.14em] text-[#1688e8]">Informasi trip</p><h2 id="trip-faq-heading" class="mt-0.5 text-base font-extrabold text-[#173b70]">Pertanyaan yang sering ditanyakan</h2></div></div><button type="button" class="group inline-flex items-center gap-2 text-xs font-bold text-[#1688e8] transition hover:text-[#096ab9]" @click="toggleAll">{{ allOpen ? 'Tutup semua' : 'Lihat semua' }}<ArrowRight class="size-4 transition-transform group-hover:translate-x-0.5" /></button></header>
        <div class="px-5 py-2 sm:px-6"><article v-for="(item, index) in questions" :key="item.question" class="border-b border-[#edf2f7] last:border-b-0"><button type="button" class="flex min-h-14 w-full items-center justify-between gap-4 py-3 text-left transition hover:text-[#1688e8]" :aria-expanded="openItems.includes(index)" @click="toggleItem(index)"><span class="flex min-w-0 items-center gap-3"><span class="w-5 shrink-0 text-xs font-extrabold tabular-nums" :class="openItems.includes(index) ? 'text-[#1688e8]' : 'text-[#9aafc4]'">0{{ index + 1 }}</span><span class="text-xs font-bold leading-5 text-[#31577f]">{{ item.question }}</span></span><span class="grid size-7 shrink-0 place-items-center rounded-full transition" :class="openItems.includes(index) ? 'bg-[#eaf5ff] text-[#1688e8]' : 'bg-transparent text-[#9aacbf]'"><ChevronDown class="size-4 transition-transform" :class="openItems.includes(index) ? 'rotate-180' : ''" /></span></button><div v-if="openItems.includes(index)" class="pb-4 pl-8 pr-10 text-xs leading-5 text-[#60789c]">{{ item.answer }}</div></article></div>
    </section>
</template>
