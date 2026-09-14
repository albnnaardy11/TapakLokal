<script setup>
import { ArrowLeft, ArrowRight, Clock3, X } from 'lucide-vue-next';
import { onBeforeUnmount, onMounted, ref } from 'vue';

const carousel = ref(null);
const dialog = ref(null);
const selectedArticle = ref(null);
const page = ref(0);
const positions = ref([0]);
let observer;

const articles = [
    { id: 'bali', category: 'Inspirasi Destinasi', title: 'Menikmati sisi tenang Bali, jauh dari keramaian', image: '1537996194471-e657df975ab4', excerpt: 'Dari suasana pura hingga pesisir yang menenangkan, temukan ide untuk perjalanan yang lebih santai.', body: 'Sisakan waktu untuk menikmati satu daerah tanpa terburu-buru berpindah tempat. Berjalan di sekitar penginapan, mencoba warung setempat, dan berbincang dengan pemandu lokal bisa menjadi bagian paling berkesan dari perjalanan. Saat mengunjungi pura, ikuti petunjuk berpakaian dan hormati area yang khusus digunakan untuk ibadah.' },
    { id: 'islands', category: 'Tips Perjalanan', title: 'Pertama kali island hopping? Mulai dari sini', image: '1516690561799-46d8f74f9abf', excerpt: 'Persiapan sederhana agar perjalanan antarpulau terasa lebih nyaman dan menyenangkan.', body: 'Pilih rute yang memberi cukup waktu untuk beristirahat. Konfirmasikan titik temu, fasilitas kapal, serta perlengkapan keselamatan kepada operator sebelum berangkat. Siapkan tas tahan air untuk barang penting, botol minum, dan pakaian ganti. Jadwal perjalanan laut dapat berubah mengikuti cuaca; beri ruang fleksibel dalam rencanamu.' },
    { id: 'local', category: 'Cerita Lokal', title: 'Liburan lebih berarti dengan mendukung usaha lokal', image: '1518548419970-58e3b4079ab2', excerpt: 'Pilihan kecil dalam perjalananmu bisa memberi manfaat bagi masyarakat di destinasi.', body: 'Cobalah makan di warung milik warga, menggunakan jasa pemandu setempat, dan membeli kerajinan langsung dari pembuatnya. Tanyakan cerita di balik produk yang kamu temui. Selalu minta izin sebelum memotret orang dan hormati kebiasaan komunitas yang kamu kunjungi.' },
    { id: 'packing', category: 'Panduan Liburan', title: 'Packing ringan untuk liburan singkat ke pantai', image: '1501179691627-eeaa65ea017c', excerpt: 'Bawa yang kamu perlukan dan sisakan ruang untuk cerita baru selama perjalanan.', body: 'Mulai dari kebutuhan utama: dokumen perjalanan, obat pribadi, pakaian nyaman, dan perlengkapan mandi seperlunya. Gunakan tas kecil untuk kegiatan harian dan simpan barang elektronik dalam pelindung tahan air. Periksa fasilitas penginapan sebelum membawa handuk atau perlengkapan tambahan.' },
    { id: 'snorkeling', category: 'Wisata Alam', title: 'Menikmati keindahan laut dengan lebih bertanggung jawab', image: '1546026423-cc4642628d2b', excerpt: 'Kenali kebiasaan sederhana untuk menjaga tempat yang kita kunjungi tetap indah.', body: 'Dengarkan pengarahan pemandu sebelum masuk ke air dan gunakan perlengkapan yang sesuai. Nikmati terumbu karang dari jarak aman tanpa menyentuh atau menginjaknya. Hindari memberi makan satwa laut, bawa kembali sampahmu, dan pilih kegiatan yang sesuai kemampuan berenangmu.' },
];

const syncPage = () => {
    const left = carousel.value?.scrollLeft || 0;
    page.value = positions.value.reduce((closest, position, index) => Math.abs(position - left) < Math.abs(positions.value[closest] - left) ? index : closest, 0);
};
const measure = () => {
    const element = carousel.value;
    if (! element) {
        return;
    }
    const maximum = Math.max(0, element.scrollWidth - element.clientWidth);
    const step = element.firstElementChild.getBoundingClientRect().width + 20;
    positions.value = Array.from({ length: Math.ceil(maximum / step) + 1 }, (_, index) => Math.min(index * step, maximum));
    syncPage();
};
const move = (index) => {
    carousel.value?.scrollTo({ left: positions.value[index], behavior: window.matchMedia('(prefers-reduced-motion: reduce)').matches ? 'instant' : 'smooth' });
};
const readArticle = (article) => {
    selectedArticle.value = article;
    dialog.value.showModal();
};
onMounted(() => {
    observer = new ResizeObserver(measure);
    observer.observe(carousel.value);
});
onBeforeUnmount(() => observer?.disconnect());
</script>

<template>
    <section class="mx-auto mt-20 max-w-[1180px] sm:mt-24" aria-labelledby="travel-blog-heading">
        <div class="flex items-end justify-between gap-5">
            <div>
                <p class="text-[10px] font-bold tracking-[0.12em] text-[#078cff]">CATATAN PERJALANAN</p>
                <h2 id="travel-blog-heading" class="mt-2 text-2xl font-extrabold leading-tight tracking-tight text-[#172c50] sm:text-3xl">Baca dan bangkitkan <span class="text-[#078cff]">semangat liburanmu</span></h2>
                <p class="mt-3 text-sm text-slate-500">Cerita, tips, dan inspirasi untuk perjalanan berikutnya.</p>
            </div>
            <div class="hidden shrink-0 gap-2 sm:flex">
                <button type="button" class="grid size-10 place-items-center rounded-full border border-sky-100 bg-white text-[#078cff] transition hover:bg-sky-50 disabled:opacity-35 focus-visible:outline-2 focus-visible:outline-[#078cff]" :disabled="page === 0" aria-label="Artikel sebelumnya" @click="move(page - 1)"><ArrowLeft class="size-4" /></button>
                <button type="button" class="grid size-10 place-items-center rounded-full bg-[#078cff] text-white transition hover:bg-[#0875d2] disabled:opacity-35 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-[#078cff]" :disabled="page === positions.length - 1" aria-label="Artikel berikutnya" @click="move(page + 1)"><ArrowRight class="size-4" /></button>
            </div>
        </div>
        <div ref="carousel" class="mt-7 flex snap-x snap-mandatory gap-5 overflow-x-auto pb-5 pt-1 [scrollbar-width:none] [&::-webkit-scrollbar]:hidden" @scroll="syncPage">
            <article v-for="article in articles" :key="article.id" class="group flex w-[85%] shrink-0 snap-start flex-col overflow-hidden rounded-2xl border border-[#e4edf7] bg-white shadow-sm transition duration-300 hover:-translate-y-1 hover:shadow-[0_12px_28px_rgba(23,100,180,0.1)] motion-reduce:transform-none motion-reduce:transition-none sm:w-[calc((100%-20px)/2)] lg:w-[calc((100%-60px)/4)]">
                <button type="button" class="flex h-full flex-col text-left outline-none focus-visible:ring-2 focus-visible:ring-inset focus-visible:ring-[#078cff]" :aria-label="`Baca ${article.title}`" @click="readArticle(article)">
                    <div class="relative aspect-[4/3] w-full overflow-hidden bg-sky-100">
                        <img :src="`https://images.unsplash.com/photo-${article.image}?auto=format&fit=crop&w=640&q=85`" :alt="article.category" loading="lazy" class="size-full object-cover transition-transform duration-500 group-hover:scale-105 motion-reduce:transition-none" />
                        <span class="absolute left-3 top-3 rounded-full bg-white/95 px-3 py-1.5 text-[9px] font-bold text-[#0875d2] shadow-sm">{{ article.category }}</span>
                    </div>
                    <div class="flex flex-1 flex-col p-5">
                        <span class="flex items-center gap-1.5 text-[10px] text-slate-400"><Clock3 class="size-3.5" aria-hidden="true" />1 menit baca</span>
                        <h3 class="mt-3 text-base font-bold leading-snug text-[#172c50] transition-colors group-hover:text-[#078cff]">{{ article.title }}</h3>
                        <p class="mt-2 text-xs leading-5 text-slate-500">{{ article.excerpt }}</p>
                        <span class="mt-auto inline-flex items-center gap-2 pt-5 text-xs font-bold text-[#078cff]">Baca cerita<ArrowRight class="size-4 transition-transform group-hover:translate-x-1 motion-reduce:transition-none" aria-hidden="true" /></span>
                    </div>
                </button>
            </article>
        </div>
        <nav v-if="positions.length > 1" class="mt-2 flex justify-center gap-1" aria-label="Halaman artikel">
            <button v-for="(position, index) in positions" :key="index" type="button" class="grid min-h-8 min-w-8 place-items-center rounded-full focus-visible:outline-2 focus-visible:outline-[#078cff]" :aria-label="`Halaman artikel ${index + 1}`" :aria-current="page === index ? 'page' : undefined" @click="move(index)"><span class="h-2 rounded-full transition-all" :class="page === index ? 'w-6 bg-[#078cff]' : 'w-2 bg-slate-200 hover:bg-sky-300'"></span></button>
        </nav>
        <dialog ref="dialog" class="fixed inset-0 m-auto max-h-[85dvh] w-[calc(100%-2rem)] max-w-2xl overflow-y-auto rounded-3xl bg-white p-0 text-[#172c50] shadow-2xl backdrop:bg-slate-950/50 backdrop:backdrop-blur-sm" aria-labelledby="blog-article-title" @click=" $event.target === dialog && dialog.close()">
            <div v-if="selectedArticle" class="p-6 sm:p-9">
                <div class="flex items-center justify-between gap-4"><span class="text-xs font-bold text-[#078cff]">{{ selectedArticle.category }}</span><button type="button" autofocus class="grid size-9 place-items-center rounded-full bg-slate-100 hover:bg-slate-200 focus-visible:outline-2 focus-visible:outline-[#078cff]" aria-label="Tutup artikel" @click="dialog.close()"><X class="size-5" /></button></div>
                <h2 id="blog-article-title" class="mt-5 text-2xl font-extrabold leading-tight">{{ selectedArticle.title }}</h2>
                <p class="mt-4 text-sm font-medium leading-6 text-slate-500">{{ selectedArticle.excerpt }}</p>
                <p class="mt-5 text-base leading-8 text-slate-600">{{ selectedArticle.body }}</p>
            </div>
        </dialog>
    </section>
</template>
