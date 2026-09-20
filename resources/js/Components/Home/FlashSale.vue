<script setup>
import { ArrowRight, ChevronRight, MapPin, ShoppingCart } from 'lucide-vue-next';
import { computed, onBeforeUnmount, onMounted, ref } from 'vue';

const carousel = ref(null);
const currentPage = ref(0);
const addedProduct = ref(null);
let autoplayTimer;

const products = [
    { id: 1, name: 'Kopi Arabika Gayo', location: 'Aceh Tengah', price: 'Rp 60.000', originalPrice: 'Rp 75.000', discount: '20%', image: 'https://images.unsplash.com/photo-1495474472287-4d71bcdd2085?auto=format&fit=crop&w=640&q=85' },
    { id: 2, name: 'Rendang Kemasan', location: 'Payakumbuh', price: 'Rp 48.000', originalPrice: 'Rp 60.000', discount: '20%', image: 'https://images.unsplash.com/photo-1601050690597-df0568f70950?auto=format&fit=crop&w=640&q=85' },
    { id: 3, name: 'Keripik Pisang Cokelat', location: 'Lampung', price: 'Rp 30.000', originalPrice: 'Rp 38.000', discount: '21%', image: 'https://images.unsplash.com/photo-1571771894821-ce9b6c11b08e?auto=format&fit=crop&w=640&q=85' },
    { id: 4, name: 'Madu Hutan Sumbawa', location: 'Sumbawa', price: 'Rp 85.000', originalPrice: 'Rp 105.000', discount: '19%', image: 'https://images.unsplash.com/photo-1587049352846-4a222e784d38?auto=format&fit=crop&w=640&q=85' },
    { id: 5, name: 'Sambal Roa Asli', location: 'Manado', price: 'Rp 42.000', originalPrice: 'Rp 52.000', discount: '19%', image: 'https://images.unsplash.com/photo-1599490659213-e2b9527bd087?auto=format&fit=crop&w=640&q=85' },
];

const pageCount = 3;
const pagePositions = computed(() => Array.from({ length: pageCount }, (_, index) => index));

const moveToPage = (page) => {
    if (! carousel.value) {
        return;
    }

    const maximumScroll = carousel.value.scrollWidth - carousel.value.clientWidth;
    currentPage.value = page;
    carousel.value.scrollTo({ left: (maximumScroll / (pageCount - 1)) * page, behavior: document.documentElement.dataset.a11yAnimation === '1' || window.matchMedia('(prefers-reduced-motion: reduce)').matches ? 'instant' : 'smooth' });
};

const nextPage = () => moveToPage((currentPage.value + 1) % pageCount);

const updateCurrentPage = () => {
    if (! carousel.value) {
        return;
    }

    const maximumScroll = carousel.value.scrollWidth - carousel.value.clientWidth;
    currentPage.value = maximumScroll ? Math.round((carousel.value.scrollLeft / maximumScroll) * (pageCount - 1)) : 0;
};

const startAutoplay = () => {
    window.clearInterval(autoplayTimer);
    autoplayTimer = window.setInterval(() => {
        if (document.documentElement.dataset.a11yAnimation !== '1' && !window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
            nextPage();
        }
    }, 4500);
};

const pauseAutoplay = () => window.clearInterval(autoplayTimer);

const addToCart = (product) => {
    addedProduct.value = product.id;
    window.setTimeout(() => {
        if (addedProduct.value === product.id) {
            addedProduct.value = null;
        }
    }, 1800);
};

onMounted(startAutoplay);
onBeforeUnmount(() => window.clearInterval(autoplayTimer));
</script>

<template>
    <section class="mx-auto mt-20 max-w-[1180px] sm:mt-24">
        <div class="relative isolate overflow-hidden rounded-[24px] bg-[#2b70d1] shadow-[0_12px_28px_rgba(22,53,102,0.16)]">
            <img src="https://images.unsplash.com/photo-1556911220-e15b29be8c8f?auto=format&fit=crop&w=1800&q=80" alt="Produk kuliner lokal" class="absolute inset-0 -z-20 size-full object-cover opacity-45" />
            <div class="absolute inset-0 -z-10 bg-[linear-gradient(90deg,rgba(22,88,187,0.96)_0%,rgba(38,107,204,0.9)_31%,rgba(91,154,232,0.56)_64%,rgba(171,207,248,0.32)_100%)]"></div>
            <div class="relative grid lg:grid-cols-[360px_minmax(0,1fr)]">
                <div class="px-7 py-9 text-white sm:px-10 lg:py-11">
                    <div class="flex items-center gap-3 text-lg font-extrabold">
                        <span class="grid size-8 shrink-0 place-items-center rounded-lg bg-white/15 shadow-sm">
                            <svg viewBox="0 0 64 88" class="h-6 w-4" aria-hidden="true">
                            <defs>
                                <radialGradient id="flash-sale-lightning-fill" cx="48%" cy="48%" r="58%">
                                    <stop offset="0%" stop-color="#d8f5ff" />
                                    <stop offset="35%" stop-color="#8fd5ff" />
                                    <stop offset="100%" stop-color="#2575d4" />
                                </radialGradient>
                            </defs>
                                <path d="M39.5 3 6.5 51.2a5.2 5.2 0 0 0 4.3 8.1h17.5l-2.7 23.2c-.6 5.2 6.1 7.2 8.3 2.5l25.2-48.8a5.2 5.2 0 0 0-4.7-7.6H37.3l4.1-20.1C42.4 3.7 42.2 1.3 39.5 3Z" fill="url(#flash-sale-lightning-fill)" stroke="#16a9f4" stroke-linejoin="round" stroke-width="3.8" />
                            </svg>
                        </span>
                        FLASH SALE
                    </div>
                    <h2 class="mt-5 text-2xl font-extrabold leading-tight">Oleh-Oleh & Kuliner Khas Daerah</h2>
                    <p class="mt-4 max-w-xs text-base leading-relaxed text-white/90">Produk lokal pilihan dengan harga spesial hanya untuk waktu terbatas.</p>
                    <button type="button" class="mt-8 inline-flex items-center gap-3 rounded-lg bg-white px-5 py-3 text-sm font-bold text-[#175a9f] transition hover:bg-[#e9f1ff]">Lihat Semua <ArrowRight class="size-4" /></button>
                </div>

                <div class="relative min-w-0 bg-white/[0.03] px-5 pb-14 pt-5 sm:px-7" @mouseenter="pauseAutoplay" @mouseleave="startAutoplay">
                    <div ref="carousel" class="flex snap-x snap-mandatory gap-3 overflow-x-auto scroll-smooth pb-2 [scrollbar-width:none] [&::-webkit-scrollbar]:hidden" @scroll="updateCurrentPage">
                        <article v-for="product in products" :key="product.id" class="relative h-[290px] w-[220px] shrink-0 snap-start rounded-[14px] bg-white shadow-[0_8px_18px_rgba(14,53,108,0.18)]">
                            <div class="relative h-[150px] overflow-hidden rounded-t-[14px] bg-[#dcdcdc]">
                                <img :src="product.image" :alt="product.name" class="size-full object-cover transition duration-500 hover:scale-105" />
                            </div>
                            <div class="rounded-b-[14px] p-3 text-slate-700"><p class="flex items-center gap-1 text-[9px] font-medium"><MapPin class="size-3.5 fill-[#20a0f5] text-white" />Toko: {{ product.location }}</p><p class="mt-0.5 text-[7px] font-medium leading-tight text-slate-600">Estimasi Tiba: 3 Hari Setelah<br />PO Ditutup</p><h3 class="mt-1.5 line-clamp-2 text-[13px] font-medium leading-tight">{{ product.name }}</h3><p class="mt-0.5 text-sm font-extrabold leading-tight text-[#ef3037]">Pesan: {{ product.price }}</p><div class="mt-3 flex items-end justify-between gap-1"><p class="text-xs leading-tight text-slate-700">Harga Normal:<br /><span class="text-slate-400 line-through">{{ product.originalPrice }}</span></p><button type="button" class="inline-flex items-center gap-1 rounded-full bg-[#21a0ef] px-2.5 py-1.5 text-[10px] font-medium text-white transition hover:bg-[#168bd4]" @click="addToCart(product)"><ShoppingCart class="size-3" />{{ addedProduct === product.id ? 'Ditambahkan' : 'Keranjang' }}</button></div></div>
                        </article>
                    </div>

                    <div class="absolute bottom-4 right-5 z-10 flex items-center gap-3 sm:right-7"><div class="flex items-center gap-1.5" aria-label="Halaman produk"><button v-for="page in pagePositions" :key="page" type="button" class="h-2 rounded-full transition-all" :class="currentPage === page ? 'w-6 bg-white' : 'w-2 bg-white/45 hover:bg-white/75'" :aria-label="`Tampilkan halaman ${page + 1}`" :aria-current="currentPage === page" @click="moveToPage(page)"></button></div><button type="button" class="grid size-8 place-items-center rounded-full border border-white/60 text-white transition hover:bg-white hover:text-[#1761bf]" aria-label="Produk berikutnya" @click="nextPage"><ChevronRight class="size-4" /></button></div>
                </div>
            </div>
        </div>
    </section>
</template>
