<script setup>
import { ref } from 'vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { route } from 'ziggy-js';
import { ArrowUpRight, ChevronDown, MapPin, Search, ShoppingBag, Store, Truck } from 'lucide-vue-next';
import MainNavigation from '../Components/Shared/MainNavigation.vue';
import MainFooter from '../Components/Shared/MainFooter.vue';

const souvenirImage = '/Assets/Images/partner-souvenir-showcase.jpg';
const query = ref('');
const selectedRegion = ref('Semua daerah');
const selectedAvailability = ref('Semua');
const openFaq = ref(0);

function showCollection() {
    router.get(route('souvenirs.index'), {
        q: query.value,
        region: selectedRegion.value === 'Semua daerah' ? '' : selectedRegion.value,
        availability: selectedAvailability.value === 'Semua' ? '' : selectedAvailability.value,
    });
}

const regions = [
    { name: 'Jawa Tengah' },
    { name: 'Bali' },
    { name: 'Lombok' },
    { name: 'DI Yogyakarta' },
    { name: 'Jawa Timur' },
    { name: 'Sumatera Barat' },
];

const steps = [
    { title: 'Pilih yang kamu suka', text: 'Cek varian, jumlah, dan waktu produk siap.' },
    { title: 'Tentukan cara menerima', text: 'Kirim ke alamat atau ambil di toko yang menyediakan layanan.' },
    { title: 'Bayar & pantau pesanan', text: 'Ikuti proses persiapan sampai barang diterima.' },
];
const faqs = [
    { question: 'Apa bedanya preorder dan ready stock?', answer: 'Ready stock berarti barang sudah tersedia. Produk preorder baru disiapkan atau dibuat sesuai jadwal toko. Periksa waktu persiapan dan jadwal siap sebelum memesan.' },
    { question: 'Bisa dikirim langsung ke rumah?', answer: 'Bisa untuk produk dan toko yang menyediakan pengiriman. Waktu persiapan barang berbeda dari waktu perjalanan paket. Ketersediaan kurir, ongkir, dan estimasi tiba perlu diperiksa saat pemesanan.' },
    { question: 'Kalau sedang wisata, bisa ambil di toko?', answer: 'Pilih pengambilan di toko jika tersedia, lalu sepakati tanggal, jam, dan lokasi dengan penjual. Untuk makanan segar, sesuaikan waktu pengambilan dengan rencana perjalananmu.' },
    { question: 'Semua makanan bisa dikirim ke luar kota?', answer: 'Tidak semua. Masa simpan, kemasan, dan lama pengiriman menentukan apakah makanan cocok dikirim. Produk segar bisa dibatasi untuk pengambilan langsung.' },
    { question: 'Sudah bisa melakukan pembayaran di sini?', answer: 'Belum. Koleksi ini merupakan contoh tampilan. Pemesanan, pembayaran, pelacakan, serta ketentuan pembatalan dan refund akan tersedia setelah katalog dan layanan toko terhubung.' },
];
</script>

<template>
    <Head title="Open Preorder — Oleh-oleh & Produk Lokal">
        <meta name="description" content="Temukan oleh-oleh, makanan khas, kopi, dan kerajinan lokal di TapakLokal. Kenali pilihan preorder, ready stock, pengiriman, dan ambil di toko." />
        <link rel="preload" as="image" :href="souvenirImage" />
    </Head>

    <div class="preorder-page min-h-screen bg-[#f8fafc] font-sans text-[#172c50]">
        <MainNavigation :transparent-on-top="true" />

        <section aria-labelledby="preorder-title" class="relative min-h-[475px] w-full overflow-hidden bg-[#0c1f38] text-white sm:min-h-[495px] lg:min-h-[500px]">
            <img
                :src="souvenirImage"
                alt="Pilihan kopi, jajanan khas, kain lokal, dan kerajinan anyaman"
                fetchpriority="high"
                class="absolute inset-0 z-0 size-full object-cover object-center brightness-[0.88]"
            />
            <div class="pointer-events-none absolute inset-0 z-[1] bg-[linear-gradient(180deg,rgba(8,24,50,0.45)_0%,rgba(12,32,62,0.30)_40%,rgba(10,22,40,0.82)_100%)]"></div>

            <div class="relative z-10 mx-auto flex max-w-[1180px] flex-col items-center px-4 pb-8 pt-36 sm:px-6 sm:pt-40 lg:px-0 lg:pt-44">
                <h1 id="preorder-title" class="mb-6 max-w-3xl text-center text-2xl font-bold leading-tight tracking-tight drop-shadow-md sm:mb-7 sm:text-3xl lg:mb-8 lg:text-[34px]">
                    Temukan Oleh-oleh & Produk Lokal Favoritmu.
                </h1>

                <form class="w-full" role="search" @submit.prevent="showCollection">
                    <div class="mb-3 flex flex-wrap gap-2">
                        <button
                            v-for="availability in ['Semua', 'Preorder', 'Ready stock']"
                            :key="availability"
                            type="button"
                            :aria-pressed="selectedAvailability === availability"
                            class="rounded-full px-4 py-1.5 text-xs font-semibold transition cursor-pointer"
                            :class="selectedAvailability === availability ? 'bg-[#0088ff] text-white' : 'bg-black/25 text-white/90 backdrop-blur-md hover:bg-black/40'"
                            @click="selectedAvailability = availability"
                        >{{ availability === 'Semua' ? 'Semua produk' : availability }}</button>
                    </div>
                    <div class="mb-1.5 hidden grid-cols-[1.4fr_1fr_auto] gap-3 px-5 text-xs font-semibold text-white/90 drop-shadow-sm md:grid">
                        <label for="po-hero-query">Cari oleh-oleh</label>
                        <label for="po-hero-region">Kota / asal daerah</label>
                        <span class="w-32"></span>
                    </div>
                    <div class="flex flex-col items-stretch rounded-2xl bg-white p-1.5 text-[#172c50] shadow-[0_20px_50px_rgba(0,0,0,0.30)] md:flex-row md:items-center md:rounded-full md:p-2">
                        <div class="flex min-w-0 flex-[1.4] items-center gap-3 border-b border-slate-200 px-4 py-3 md:border-b-0 md:border-r">
                            <ShoppingBag class="size-5 shrink-0 text-[#0088ff]" aria-hidden="true" />
                            <div class="min-w-0 flex-1">
                                <label for="po-hero-query" class="mb-1 block text-[10px] font-bold text-slate-400 md:hidden">CARI OLEH-OLEH</label>
                                <input id="po-hero-query" v-model="query" type="search" placeholder="Cari kopi, jajanan, kerajinan…" class="min-h-6 w-full min-w-0 bg-transparent text-xs font-semibold outline-none placeholder:font-normal placeholder:text-slate-400 sm:text-sm" />
                            </div>
                        </div>
                        <div class="flex min-w-0 flex-1 items-center gap-3 px-4 py-3">
                            <MapPin class="size-5 shrink-0 text-[#0088ff]" aria-hidden="true" />
                            <div class="min-w-0 flex-1">
                                <label for="po-hero-region" class="mb-1 block text-[10px] font-bold text-slate-400 md:hidden">KOTA / ASAL DAERAH</label>
                                <select id="po-hero-region" v-model="selectedRegion" class="min-h-6 w-full bg-transparent text-xs font-semibold sm:text-sm">
                                    <option>Semua daerah</option>
                                    <option v-for="region in regions" :key="region.name">{{ region.name }}</option>
                                </select>
                            </div>
                        </div>
                        <button type="submit" class="inline-flex min-h-12 shrink-0 items-center justify-center gap-2 rounded-xl bg-[#0088ff] px-6 text-sm font-bold text-white transition hover:bg-[#0064d2] cursor-pointer md:rounded-full">
                            <Search class="size-5" aria-hidden="true" /> Cari produk
                        </button>
                    </div>
                    <div class="mt-5 flex flex-wrap items-center justify-center gap-3 text-xs text-white">
                        <span class="inline-flex items-center gap-2 rounded-full border border-white/25 bg-white/10 px-4 py-2 font-semibold backdrop-blur-md">
                            <Truck class="size-4 shrink-0" aria-hidden="true" /> Kirim ke rumah
                        </span>
                        <span class="inline-flex items-center gap-2 rounded-full border border-white/25 bg-white/10 px-4 py-2 font-semibold backdrop-blur-md">
                            <Store class="size-4 shrink-0" aria-hidden="true" /> Ambil di tempat
                        </span>
                    </div>
                </form>
            </div>
        </section>

        <main class="mx-auto max-w-[1180px] px-4 pb-16 pt-8 sm:px-6 sm:pb-24 sm:pt-10 xl:px-0">
            <section aria-label="Jelajahi etalase lokal" class="mb-8 flex flex-col gap-4 border-b border-slate-200 pb-6 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h2 class="text-lg font-bold">Belum tahu mau pesan apa?</h2>
                    <p class="mt-1 text-sm text-slate-500">Jelajahi produk lokal atau temukan toko dari daerah pilihanmu.</p>
                </div>
                <div class="flex flex-wrap gap-3">
                    <Link :href="route('souvenirs.index')" class="po-primary">Lihat semua produk <ShoppingBag class="size-4" aria-hidden="true" /></Link>
                    <Link :href="route('souvenirs.index', { tab: 'stores' })" class="inline-flex min-h-11 items-center justify-center gap-2 rounded-full border border-[#0175ea] px-5 py-3 text-xs font-bold text-[#0175ea] hover:bg-sky-50">Jelajahi toko <Store class="size-4" aria-hidden="true" /></Link>
                </div>
            </section>
            <section id="cara-pesan" aria-labelledby="steps-heading" class="scroll-mt-32 overflow-hidden rounded-3xl border border-[#e2edfa] bg-white">
                <div class="grid gap-5 border-b border-[#e2edfa] p-6 sm:grid-cols-2 sm:p-8">
                    <div class="flex items-start gap-4">
                        <span class="grid size-11 shrink-0 place-items-center rounded-xl bg-[#edf6ff] text-[#0175ea]"><Truck class="size-5" aria-hidden="true" /></span>
                        <div><h3 class="text-sm font-bold">Kirim ke alamatmu</h3><p class="mt-1 text-xs leading-6 text-slate-500">Produk dikirim setelah siap. Cek jangkauan pengiriman dan estimasi tiba dari toko.</p></div>
                    </div>
                    <div class="flex items-start gap-4">
                        <span class="grid size-11 shrink-0 place-items-center rounded-xl bg-[#edf6ff] text-[#0175ea]"><Store class="size-5" aria-hidden="true" /></span>
                        <div><h3 class="text-sm font-bold">Ambil saat wisata</h3><p class="mt-1 text-xs leading-6 text-slate-500">Ambil langsung di toko yang menyediakan layanan. Sesuaikan tanggal dan jam dengan perjalananmu.</p></div>
                    </div>
                </div>
                <div class="grid lg:grid-cols-[300px_1fr]"><div class="flex flex-col justify-between bg-[#123f57] p-7 text-white sm:p-8"><span class="flex items-center gap-2 text-[10px] font-semibold tracking-[.12em]"><ShoppingBag class="size-4" aria-hidden="true" />DARI TOKO LOKAL KE KAMU</span><div class="mt-9"><h2 id="steps-heading" class="text-3xl font-extrabold leading-tight tracking-tight">Pesan dulu.<br />Nikmati kemudian.</h2><p class="mt-4 text-xs leading-6 text-white/75">Preorder adalah waktu persiapannya. Dikirim atau diambil adalah pilihan menerima barangnya.</p></div></div><div class="p-6 sm:p-8"><p class="po-eyebrow">ALUR PEMESANAN</p><ol class="mt-5 grid gap-5 sm:grid-cols-3"><li v-for="(step, index) in steps" :key="step.title"><span class="grid size-9 place-items-center rounded-full bg-[#edf6ff] text-xs font-bold text-[#0175ea]">0{{ index + 1 }}</span><h3 class="mt-4 text-sm font-bold leading-6">{{ step.title }}</h3><p class="mt-2 text-xs leading-6 text-slate-500">{{ step.text }}</p></li></ol><div class="mt-7 flex items-start gap-3 border-t border-slate-100 pt-5"><Truck class="mt-1 size-5 shrink-0 text-[#0175ea]" aria-hidden="true" /><p class="text-xs leading-6 text-slate-500"><strong class="text-[#172c50]">Waktu siap ≠ waktu tiba.</strong> Produk PO disiapkan terlebih dahulu. Lama pengiriman dihitung setelah paket diserahkan ke kurir.</p></div></div></div>
            </section>

            <section aria-labelledby="faq-heading" class="mt-14 grid gap-7 sm:mt-20 lg:grid-cols-[.7fr_1.3fr] lg:gap-16">
                <div><p class="po-eyebrow">SEBELUM TITIP PESAN</p><h2 id="faq-heading" class="po-heading mt-2">Biar lebih yakin<br />sebelum memilih.</h2><p class="mt-4 max-w-xs text-sm leading-7 text-slate-500">Kenali waktu persiapan dan cara menerima produk favoritmu.</p><Link :href="route('help.index')" class="mt-5 inline-flex items-center gap-2 text-xs font-bold text-[#0175ea]">Kunjungi pusat bantuan <ArrowUpRight class="size-4" aria-hidden="true" /></Link></div>
                <div class="rounded-2xl border border-[#e2edfa] bg-white px-5 sm:px-7"><div v-for="(faq, index) in faqs" :key="faq.question" class="border-b border-slate-100 last:border-0"><h3><button :id="`po-question-${index}`" type="button" :aria-expanded="openFaq === index" :aria-controls="`po-answer-${index}`" class="flex min-h-16 w-full items-center justify-between gap-4 py-4 text-left text-sm font-semibold hover:text-[#0175ea] cursor-pointer" @click="openFaq = openFaq === index ? null : index"><span>{{ faq.question }}</span><ChevronDown class="size-4 shrink-0 text-[#0175ea] transition-transform motion-reduce:transition-none" :class="{ 'rotate-180': openFaq === index }" aria-hidden="true" /></button></h3><p v-show="openFaq === index" :id="`po-answer-${index}`" role="region" :aria-labelledby="`po-question-${index}`" class="pb-5 pr-5 text-xs leading-6 text-slate-500">{{ faq.answer }}</p></div></div>
            </section>
        </main>
        <MainFooter />
    </div>
</template>

<style scoped>
@reference '../../css/app.css';

.po-primary {
    @apply inline-flex min-h-11 items-center justify-center gap-2 rounded-full bg-[#0175ea] px-5 py-3 text-xs font-bold text-white transition hover:bg-[#005fb8];
}

.po-eyebrow {
    @apply text-[10px] font-bold tracking-[.14em] text-[#0175ea];
}

.po-heading {
    @apply text-2xl font-extrabold leading-tight tracking-tight text-[#172c50] sm:text-3xl;
}

.preorder-page :is(button, a, input, select):focus-visible {
    outline: 2px solid #0175ea;
    outline-offset: 4px;
}
</style>

