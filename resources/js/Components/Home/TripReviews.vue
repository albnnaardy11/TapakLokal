<script setup>
import { computed, ref } from 'vue';
import { ArrowRight, Star, ThumbsDown, ThumbsUp } from 'lucide-vue-next';

const props = defineProps({
    tripType: { type: String, required: true },
});

const showAllReviews = ref(false);
const likedReviews = ref([]);
const isPrivateTrip = computed(() => props.tripType === 'private-trip');
const reviews = computed(() => isPrivateTrip.value ? [
    { name: 'Nadia Puspita', initials: 'NP', time: '2 minggu yang lalu', rating: 5, text: 'Rute private trip sangat fleksibel dan kru kapal responsif. Semua kebutuhan rombongan kami dibantu dengan baik.', likes: 46 },
    { name: 'Fajar Ramadhan', initials: 'FR', time: '3 minggu yang lalu', rating: 5, text: 'Labuan Bajo terasa lebih nyaman karena itinerary disusun rapi. Dokumentasinya juga memuaskan.', likes: 38 },
    { name: 'Salsa Putri', initials: 'SP', time: '1 bulan yang lalu', rating: 5, text: 'Pelayanan vendor baik, kapal bersih, dan spot yang dikunjungi sesuai ekspektasi.', likes: 31 },
    { name: 'Dimas Pratama', initials: 'DP', time: '1 bulan yang lalu', rating: 4, text: 'Perjalanan nyaman dan pemandu sangat membantu selama aktivitas laut.', likes: 22 },
] : [
    { name: 'Joko Yusuf Royyan', initials: 'JY', time: '10 bulan, 1 minggu yang lalu', rating: 5, text: 'Secara keseluruhan, Open Trip ID menawarkan pengalaman tur yang luar biasa. Lihat banyak.', likes: 100 },
    { name: 'Joko Yusuf Royyan', initials: 'JY', time: '10 bulan, 1 minggu yang lalu', rating: 5, text: 'Pemandu ramah, rundown jelas, dan fasilitas yang diberikan sesuai dengan informasi trip.', likes: 88 },
    { name: 'Joko Yusuf Royyan', initials: 'JY', time: '10 bulan, 1 minggu yang lalu', rating: 5, text: 'Perjalanan menyenangkan untuk liburan singkat. Spot snorkelingnya bagus dan aman untuk pemula.', likes: 74 },
    { name: 'Alya Rahma', initials: 'AR', time: '11 bulan yang lalu', rating: 5, text: 'Rekomendasi untuk yang ingin liburan ke Kepulauan Seribu tanpa repot mengurus banyak hal.', likes: 52 },
]);
const visibleReviews = computed(() => showAllReviews.value ? reviews.value : reviews.value.slice(0, 3));
const distribution = [
    { label: '5 star', value: 244, width: '100%' },
    { label: '4 star', value: 0, width: '0%' },
    { label: '3 star', value: 0, width: '0%' },
    { label: '2 star', value: 0, width: '0%' },
    { label: '1 star', value: 0, width: '0%' },
];

const toggleLike = (index) => {
    likedReviews.value = likedReviews.value.includes(index)
        ? likedReviews.value.filter((item) => item !== index)
        : [...likedReviews.value, index];
};
</script>

<template>
    <section class="mt-7 overflow-hidden rounded-2xl border border-[#dfeaf5] bg-white shadow-[0_10px_28px_rgba(23,75,120,0.05)]" aria-labelledby="trip-reviews-heading">
        <header class="flex flex-wrap items-center justify-between gap-4 border-b border-[#e8f0f7] px-5 py-4 sm:px-6"><h2 id="trip-reviews-heading" class="flex items-center gap-2 text-base font-extrabold text-[#173b70]"><span class="grid size-7 place-items-center rounded-full bg-[#1688e8] text-white"><Star class="size-3.5 fill-current" /></span>Ulasan (244)</h2><button type="button" class="group inline-flex items-center gap-2 text-xs font-bold text-[#1688e8] transition hover:text-[#096ab9]" @click="showAllReviews = !showAllReviews">{{ showAllReviews ? 'Tampilkan ringkas' : 'Lihat Semua' }}<ArrowRight class="size-4 transition-transform group-hover:translate-x-0.5" /></button></header>
        <div class="grid lg:grid-cols-[365px_minmax(0,1fr)]"><aside class="border-b border-[#e8f0f7] bg-[#fbfdff] p-5 sm:p-6 lg:border-b-0 lg:border-r"><div class="grid grid-cols-[132px_minmax(0,1fr)] items-center gap-4"><div class="text-center"><p class="text-2xl font-extrabold tracking-tight text-[#173b70]">5.0 / 5.0</p><div class="mt-2 flex justify-center gap-0.5 text-[#f5a000]"><Star v-for="index in 5" :key="index" class="size-4 fill-current" /></div><p class="mt-3 text-[10px] font-semibold text-[#7186a2]">Berdasarkan 244 ulasan</p></div><div class="space-y-2"><div v-for="item in distribution" :key="item.label" class="grid grid-cols-[40px_minmax(0,1fr)_18px] items-center gap-2 text-[10px]"><span class="font-bold text-[#60789c]">{{ item.label }}</span><span class="h-1.5 overflow-hidden rounded-full bg-[#e8f0f7]"><span class="block h-full rounded-full bg-[#1688e8]" :style="{ width: item.width }"></span></span><span class="text-right font-bold text-[#60789c]">{{ item.value }}</span></div></div></div></aside>
            <div class="min-w-0 p-4 sm:p-5"><div class="grid gap-3 md:grid-cols-2 xl:grid-cols-3"><article v-for="(review, index) in visibleReviews" :key="`${review.name}-${index}`" class="rounded-xl border border-[#e1ebf6] bg-white p-4 transition hover:-translate-y-0.5 hover:shadow-[0_7px_18px_rgba(22,93,155,0.08)]"><div class="flex items-start gap-3"><span class="grid size-8 shrink-0 place-items-center rounded-full bg-[#1688e8] text-[10px] font-extrabold text-white">{{ review.initials }}</span><div class="min-w-0"><h3 class="truncate text-xs font-extrabold text-[#31577f]">{{ review.name }}</h3><p class="mt-1 text-[10px] text-[#9aacc0]">{{ review.time }}</p></div></div><div class="mt-3 flex gap-0.5 text-[#f5a000]"><Star v-for="star in review.rating" :key="star" class="size-3.5 fill-current" /></div><p class="mt-3 min-h-12 text-[11px] leading-5 text-[#7186a2]">{{ review.text }}</p><div class="mt-3 flex items-center gap-3 border-t border-[#edf3f8] pt-3"><button type="button" class="inline-flex items-center gap-1 text-[10px] font-semibold transition" :class="likedReviews.includes(index) ? 'text-[#1688e8]' : 'text-[#9aacc0] hover:text-[#1688e8]'" :aria-pressed="likedReviews.includes(index)" @click="toggleLike(index)"><ThumbsUp class="size-3.5" :class="likedReviews.includes(index) ? 'fill-current' : ''" />{{ review.likes + (likedReviews.includes(index) ? 1 : 0) }}</button><span class="inline-flex items-center gap-1 text-[10px] font-semibold text-[#9aacc0]"><ThumbsDown class="size-3.5" />0</span></div></article></div></div>
        </div>
    </section>
</template>
