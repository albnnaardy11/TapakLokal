<script setup>
import { computed, onBeforeUnmount, ref, watch } from 'vue';
import { Head, Link, router, usePage } from '@inertiajs/vue3';
import { route } from 'ziggy-js';
import { ArrowLeft, ArrowRight, ChevronRight, Heart, MapPin, Minus, Package, Plus, Search, ShoppingBag, ShoppingCart, SlidersHorizontal, Store, Trash2, Truck, X } from 'lucide-vue-next';
import MainNavigation from '../Components/Shared/MainNavigation.vue';
import MainFooter from '../Components/Shared/MainFooter.vue';
import SouvenirProductCard from '../Components/Catalog/SouvenirProductCard.vue';
import { souvenirPhotoStyle, formatSouvenirPrice } from '../Components/Catalog/souvenirCatalog';

const props = defineProps({ view: { type: String, default: 'catalog' }, productId: String, shopId: String, reviews: { type: Array, default: () => [] }, products: { type: Array, default: () => [] }, shops: { type: Array, default: () => [] }, cartItems: { type: Array, default: () => [] }, cartCount: [String, Number], savedProducts: Array, followedStores: Array, pagination: Object, filters: { type: Object, default: () => ({}) } });
const page = usePage();
const query = ref(props.filters.q || '');
const activeTab = ref(props.filters.tab || 'products');
const categories = ref(props.filters.category || []);
const regions = ref(props.filters.region || []);
const availability = ref(props.filters.availability || []);
const reception = ref(props.filters.reception || []);
const minimumPrice = ref(props.filters.min ?? '');
const maximumPrice = ref(props.filters.max ?? '');
const sort = ref(props.filters.sort || 'recommended');
const mobileFiltersOpen = ref(false);
const quantity = ref(1);
const variant = ref('');
const note = ref('');
const productTab = ref('detail');
const storeTab = ref('home');
const galleryIndex = ref(0);
const shippingDialog = ref(null);
const notice = ref('');
const cart = computed(() => props.cartItems);


const product = computed(() => props.products.find(item => item.id === props.productId));
const shop = computed(() => props.shops.find(item => item.id === (props.shopId || product.value?.shopId)));
const shopProducts = computed(() => props.products.filter(item => item.shopId === shop.value?.id));
const relatedProducts = computed(() => props.products.filter(item => item.id !== product.value?.id && item.category === product.value?.category));
const gallery = computed(() => product.value ? [product.value] : []);
const regionOptions = ['Jawa Tengah', 'Bali', 'Lombok', 'DI Yogyakarta', 'Jawa Timur', 'Sumatera Barat'];
const categoryOptions = ['Makanan khas', 'Kopi & minuman', 'Kain & kerajinan'];
const filteredProducts = computed(() => props.products.filter(item => !reception.value.length || reception.value.includes('pickup') || !item.pickupOnly));
const filteredShops = computed(() => props.shops);
const wishlist = computed(() => props.products.filter(p => (props.savedProducts || []).map(Number).includes(p.databaseId)).map(p => p.id));
const following = computed(() => (props.followedStores || []).map(String));
const cartLines = computed(() => props.cartItems.map(entry => ({ ...entry, product: props.products.find(item => item.id === entry.id) })).filter(entry => entry.product));
const cartCount = computed(() => Number(props.cartCount || 0));
const cartTotal = computed(() => cartLines.value.reduce((total, entry) => total + entry.quantity * entry.product.price, 0));
const cartGroups = computed(() => props.shops.map(item => ({ shop: item, lines: cartLines.value.filter(entry => entry.product.shopId === item.id) })).filter(item => item.lines.length));
const pageTitle = computed(() => props.view === 'product' ? product.value?.name : props.view === 'store' ? shop.value?.name : props.view === 'cart' ? 'Keranjang oleh-oleh' : 'Etalase Oleh-oleh & Produk Lokal');
let filterTimer;
function applyFilters() {
    router.get(route('souvenirs.index'), { q: query.value || undefined, region: regions.value.length ? regions.value : undefined, category: categories.value.length ? categories.value : undefined, availability: availability.value.length ? availability.value : undefined, reception: reception.value.length ? reception.value : undefined, min: minimumPrice.value === '' ? undefined : minimumPrice.value, max: maximumPrice.value === '' ? undefined : maximumPrice.value, sort: sort.value, tab: activeTab.value }, { preserveState: true, preserveScroll: true, replace: true });
}
watch([query, categories, regions, availability, minimumPrice, maximumPrice, sort, activeTab, reception], () => {
    if (props.view !== 'catalog') return;
    clearTimeout(filterTimer);
    filterTimer = setTimeout(applyFilters, 350);
}, { deep: true });
onBeforeUnmount(() => clearTimeout(filterTimer));
watch(() => props.productId, () => { quantity.value = 1; variant.value = ''; note.value = ''; });
function resetFilters() {
    query.value = ''; categories.value = []; regions.value = []; availability.value = []; reception.value = []; minimumPrice.value = ''; maximumPrice.value = ''; sort.value = 'recommended';
}
function requireAccount() {
    if (page.props.auth?.user) return true;
    router.visit(route('login', { return_to: page.url.split('?')[0] }));
    return false;
}
function toggleSaved(list, id) {
    if (!requireAccount()) return;
    const kind = list === wishlist.value ? 'product' : 'store';
    const target = kind === 'product' ? props.products.find(p => p.id === id)?.databaseId : Number(id);
    router.post(route('souvenirs.saved'), { kind, target_id: target }, { preserveScroll: true });
}
function addToCart(buyNow = false) {
    if (!requireAccount()) return;
    router.post(route('souvenirs.cart.add'), { product_id: product.value.databaseId, variant: variant.value || product.value.variants[0], quantity: quantity.value, note: note.value }, { preserveScroll: true, onSuccess: () => { notice.value = 'Produk masuk keranjang.'; if (buyNow) router.visit(route('souvenirs.cart')); }, onError: errors => { notice.value = Object.values(errors)[0]; } });
}
function changeCartQuantity(entry, change) {
    router.put(route('souvenirs.cart.update', entry.key), { quantity: Math.max(0, entry.quantity + change) }, { preserveScroll: true, onError: errors => { notice.value = Object.values(errors)[0]; } });
}
function showCheckout(group) {
    router.visit(route('checkout.review.souvenir', { vendor: group.shop.id }));
}

</script>

<template>
    <Head :title="pageTitle"><meta name="description" head-key="description" :content="product?.description?.slice(0,155) || 'Pesan oleh-oleh, makanan khas, dan kerajinan dari mitra lokal terverifikasi.'" /><meta name="robots" head-key="robots" :content="view === 'cart' ? 'noindex,nofollow' : page.url.includes('?') ? 'noindex,follow' : 'index,follow'" /><link rel="canonical" head-key="canonical" :href="view === 'product' ? route('souvenirs.show', productId) : view === 'store' ? route('souvenirs.store',shopId) : route('souvenirs.index')" /></Head>
    <div class="marketplace min-h-screen bg-white font-sans text-[#172c50]">
        <MainNavigation :is-static="true" />
        <main class="mx-auto max-w-[1180px] px-4 pb-16 pt-5 sm:px-6 xl:px-0">
            <div class="flex flex-wrap items-center justify-between gap-3 border-b border-slate-100 pb-4">
                <nav aria-label="Breadcrumb" class="flex items-center gap-2 text-xs text-slate-500"><Link :href="route('open.preorder')" class="hover:text-[#0175ea]">Open Preorder</Link><ChevronRight class="size-3" aria-hidden="true" /><Link :href="route('souvenirs.index')" class="font-semibold text-[#0175ea]">Etalase lokal</Link><template v-if="view !== 'catalog'"><ChevronRight class="size-3" aria-hidden="true" /><span class="max-w-44 truncate">{{ view === 'cart' ? 'Keranjang' : pageTitle }}</span></template></nav>
                <Link :href="route('souvenirs.cart')" class="flex min-h-10 items-center gap-2 rounded-lg border border-slate-200 px-3 text-xs font-semibold hover:bg-sky-50"><ShoppingCart class="size-4 text-[#0175ea]" aria-hidden="true" />Keranjang <span class="rounded-full bg-[#edf6ff] px-2 py-0.5 text-[#0175ea]">{{ cartCount }}</span></Link>
            </div>
            <Link :href="route('souvenirs.orders')" class="mt-3 inline-block text-xs font-semibold text-[#0175ea]">Pesanan oleh-oleh saya</Link>
            <p v-if="notice" role="status" class="mt-3 rounded-xl bg-sky-50 p-3 text-xs text-[#075890]">{{ notice }}</p>

            <template v-if="view === 'catalog'"><h1 class="mt-6 text-xl font-bold">Oleh-oleh &amp; produk lokal</h1>
                <form class="mt-5 flex gap-2" role="search" @submit.prevent="applyFilters"><label class="flex min-w-0 flex-1 items-center gap-3 rounded-xl border border-slate-200 px-4"><Search class="size-4 text-[#0175ea]" aria-hidden="true" /><input v-model="query" aria-label="Cari produk atau toko" placeholder="Cari oleh-oleh, kerajinan, atau toko lokal" type="search" class="min-h-12 w-full min-w-0 bg-transparent text-sm outline-none" /></label><button type="button" :aria-expanded="mobileFiltersOpen" aria-controls="souvenir-filters" class="flex items-center gap-2 rounded-xl border border-slate-200 px-4 text-xs font-bold lg:hidden" @click="mobileFiltersOpen = !mobileFiltersOpen"><SlidersHorizontal class="size-4" aria-hidden="true" />Filter</button></form>
                <div class="mt-6 grid gap-6 lg:grid-cols-[210px_minmax(0,1fr)]">
                    <aside id="souvenir-filters" :class="mobileFiltersOpen ? 'block' : 'hidden lg:block'" aria-label="Filter etalase">
                        <div class="mb-3 flex items-center justify-between"><h2 class="text-sm font-bold">Filter</h2><button type="button" class="text-xs font-semibold text-[#0175ea]" @click="resetFilters">Reset</button></div>
                        <div class="rounded-xl border border-slate-200 px-4">
                            <fieldset class="filter-group"><legend>Kategori</legend><label v-for="item in categoryOptions" :key="item"><input v-model="categories" type="checkbox" :value="item" />{{ item }}</label></fieldset>
                            <fieldset class="filter-group"><legend>Lokasi toko</legend><label v-for="item in regionOptions" :key="item"><input v-model="regions" type="checkbox" :value="item" />{{ item }}</label></fieldset>
                            <fieldset class="filter-group"><legend>Harga</legend><input v-model="minimumPrice" aria-label="Harga minimum" type="number" min="0" placeholder="Harga minimum" class="panel-input mb-2" /><input v-model="maximumPrice" aria-label="Harga maksimum" type="number" min="0" placeholder="Harga maksimum" class="panel-input" /></fieldset>
                            <fieldset class="filter-group"><legend>Ketersediaan</legend><label v-for="item in ['Preorder', 'Ready stock']" :key="item"><input v-model="availability" type="checkbox" :value="item" />{{ item }}</label></fieldset>
                            <fieldset class="filter-group border-b-0"><legend>Cara menerima</legend><label><input v-model="reception" type="checkbox" value="delivery" />Kirim ke rumah</label><label><input v-model="reception" type="checkbox" value="pickup" />Ambil di tempat</label></fieldset>
                        </div>
                    </aside>
                    <section class="min-w-0" aria-label="Hasil pencarian">
                        <div role="group" aria-label="Jenis hasil" class="flex gap-2 border-b border-slate-200"><button v-for="tab in [{id:'products',label:'Produk',icon:ShoppingBag},{id:'stores',label:'Toko',icon:Store}]" :id="`results-tab-${tab.id}`" :key="tab.id" type="button" :aria-pressed="activeTab === tab.id" aria-controls="results-panel" class="flex items-center gap-2 border-b-2 px-5 py-3 text-sm font-bold" :class="activeTab === tab.id ? 'border-[#0175ea] text-[#0175ea]' : 'border-transparent text-slate-500'" @click="activeTab = tab.id"><component :is="tab.icon" class="size-4" aria-hidden="true" />{{ tab.label }}</button></div>
                        <div class="my-5 flex flex-wrap items-center justify-between gap-3"><p class="text-xs text-slate-500" aria-live="polite">{{ activeTab === 'products' ? filteredProducts.length + ' produk pada halaman ini' : filteredShops.length + ' toko pada halaman ini' }} ditemukan<span v-if="query"> untuk <strong class="text-[#172c50]">“{{ query }}”</strong></span></p><label v-if="activeTab === 'products'" class="flex items-center gap-2 text-xs">Urutkan:<select v-model="sort" class="rounded-lg border border-slate-200 p-2"><option value="recommended">Paling sesuai</option><option value="cheapest">Harga terendah</option><option value="expensive">Harga tertinggi</option></select></label></div>
                        <div id="results-panel" role="region" :aria-labelledby="`results-tab-${activeTab}`">
                            <div v-if="activeTab === 'products'" class="grid grid-cols-2 gap-3 sm:grid-cols-3 xl:grid-cols-4"><SouvenirProductCard v-for="item in filteredProducts" :key="item.id" :product="item" /></div>
                            <div v-else class="grid gap-4 sm:grid-cols-2 xl:grid-cols-3"><article v-for="item in filteredShops" :key="item.id" class="rounded-xl border border-slate-200 p-4"><div class="flex items-center gap-3"><span class="store-avatar">{{ item.initials }}</span><div class="min-w-0 flex-1"><h3 class="text-sm font-bold">{{ item.name }}</h3><p class="mt-1 text-xs text-slate-500">{{ item.city }}</p></div></div><div class="mt-4 grid grid-cols-2 gap-2"><Link v-for="entry in filteredProducts.filter(entry => entry.shopId === item.id).slice(0,2)" :key="entry.id" :href="route('souvenirs.show', entry.id)"><div class="aspect-square rounded-lg bg-no-repeat" :style="souvenirPhotoStyle(entry)" role="img" :aria-label="entry.name"></div><p class="mt-2 text-xs font-bold">{{ formatSouvenirPrice(entry.price) }}</p></Link></div><Link :href="route('souvenirs.store', item.id)" class="market-secondary mt-4 w-full">Lihat toko <ArrowRight class="size-4" aria-hidden="true" /></Link></article></div>
                            <div v-if="!filteredProducts.length" class="rounded-xl border border-dashed border-slate-300 py-16 text-center"><Package class="mx-auto size-8 text-slate-400" aria-hidden="true" /><h3 class="mt-3 font-bold">Belum ada hasil yang cocok.</h3><p class="mt-2 text-xs text-slate-500">Coba kata lain atau kurangi filter.</p><button type="button" class="market-primary mt-5" @click="resetFilters">Reset filter</button></div>
                        </div>
                    </section>
                </div>
            </template>

            <template v-else-if="view === 'product' && product">
                <div class="mt-7 grid items-start gap-6 md:grid-cols-2 xl:grid-cols-[360px_minmax(0,1fr)_280px]">
                    <section aria-label="Galeri produk"><div class="aspect-square overflow-hidden rounded-2xl bg-slate-100 bg-no-repeat" :style="souvenirPhotoStyle(gallery[galleryIndex] || product)" role="img" :aria-label="product.name"></div><div class="mt-3 grid grid-cols-2 gap-3"><button v-for="(image,index) in gallery" :key="image.id" type="button" :aria-label="`Lihat foto ${index + 1}`" :aria-pressed="galleryIndex === index" class="aspect-square rounded-xl border-2 bg-no-repeat" :class="galleryIndex === index ? 'border-[#0175ea]' : 'border-transparent'" :style="souvenirPhotoStyle(image)" @click="galleryIndex = index"></button></div><p class="mt-2 text-[10px] text-slate-400">Foto produk dari toko.</p></section>
                    <section><span class="rounded-md bg-sky-50 px-2 py-1 text-[11px] font-bold text-[#0175ea]">{{ product.availability }}</span><h1 class="mt-3 text-xl font-extrabold leading-snug sm:text-2xl">{{ product.name }}</h1><p class="mt-3 text-xs text-slate-500">Produk dari mitra lokal terverifikasi</p><p class="mt-5 text-3xl font-extrabold">{{ formatSouvenirPrice(product.price) }}</p><div class="mt-6 border-t border-slate-100 pt-5"><p class="text-xs font-bold">Pilih varian</p><div class="mt-3 flex flex-wrap gap-2"><button v-for="item in product.variants" :key="item" type="button" :aria-pressed="(variant || product.variants[0]) === item" class="rounded-lg border px-3 py-2 text-xs" :class="(variant || product.variants[0]) === item ? 'border-[#0175ea] bg-sky-50 text-[#0175ea]' : 'border-slate-200'" @click="variant = item">{{ item }}</button></div></div><div class="mt-6 flex border-b border-slate-200"><button v-for="tab in [{id:'detail',label:'Detail produk'},{id:'specs',label:'Spesifikasi'}]" :key="tab.id" type="button" class="border-b-2 px-3 py-3 text-xs font-bold" :class="productTab === tab.id ? 'border-[#0175ea] text-[#0175ea]' : 'border-transparent text-slate-500'" @click="productTab = tab.id">{{ tab.label }}</button></div><div v-if="productTab === 'detail'" class="mt-4 text-sm leading-7 text-slate-600"><p>{{ product.description }}</p><p class="mt-3">{{ product.care }}</p></div><dl v-else class="mt-4 grid grid-cols-2 gap-3 text-xs leading-6"><dt class="text-slate-500">Kategori</dt><dd>{{ product.category }}</dd><dt class="text-slate-500">Berat satuan</dt><dd>{{ product.weight }} gram</dd><dt class="text-slate-500">Persiapan</dt><dd>{{ product.preparation }}</dd><dt class="text-slate-500">Minimum beli</dt><dd>1 produk</dd></dl><div class="mt-7 border-y border-slate-100 py-5"><Link :href="route('souvenirs.store', shop.id)" class="flex items-center gap-3"><span class="store-avatar">{{ shop.initials }}</span><div class="flex-1"><h2 class="text-sm font-bold">{{ shop.name }}</h2><p class="mt-1 text-xs text-slate-500">{{ shop.city }} · {{ shopProducts.length }} produk</p></div><ChevronRight class="size-4 text-[#0175ea]" aria-hidden="true" /></Link></div><section class="mt-5"><h2 class="text-sm font-bold">Pengiriman & pengambilan</h2><p class="mt-3 flex items-center gap-2 text-xs text-slate-500"><MapPin class="size-4" aria-hidden="true" />Dari {{ shop.city }}</p><p class="mt-2 text-xs leading-6 text-slate-500">{{ product.preparation }} · {{ product.pickupOnly ? 'Hanya ambil di tempat' : 'Kirim ke rumah atau ambil di tempat' }}</p><button type="button" class="mt-3 text-xs font-bold text-[#0175ea]" @click="shippingDialog.showModal()">Lihat detail pengiriman</button></section></section>
                    <aside class="rounded-2xl border border-slate-200 p-5 md:col-span-2 xl:sticky xl:top-5 xl:col-span-1"><h2 class="text-sm font-bold">Atur jumlah dan catatan</h2><p class="mt-2 text-xs text-slate-500">Stok tersedia: {{ product.stock }} produk</p><div class="mt-4 inline-flex items-center rounded-lg border border-slate-200"><button type="button" aria-label="Kurangi jumlah" :disabled="quantity <= 1" class="p-3 disabled:opacity-30" @click="quantity--"><Minus class="size-4" /></button><span class="min-w-10 text-center text-sm" aria-live="polite">{{ quantity }}</span><button type="button" aria-label="Tambah jumlah" :disabled="quantity >= product.stock" class="p-3 disabled:opacity-30" @click="quantity++"><Plus class="size-4" /></button></div><label class="mt-4 block text-xs font-semibold" for="product-note">Catatan untuk toko</label><textarea id="product-note" v-model="note" maxlength="300" rows="2" class="panel-input mt-2" placeholder="Contoh: bungkus untuk hadiah"></textarea><div class="mt-5 flex items-center justify-between text-xs"><span class="text-slate-500">Subtotal</span><strong class="text-lg">{{ formatSouvenirPrice(product.price * quantity) }}</strong></div><button type="button" :disabled="product.stock < 1" class="market-primary mt-5 w-full disabled:opacity-50" @click="addToCart()"><Plus class="size-4" aria-hidden="true" />Tambah ke keranjang</button><button type="button" :disabled="product.stock < 1" class="market-secondary mt-2 w-full disabled:opacity-50" @click="addToCart(true)">Beli langsung</button><button type="button" :aria-pressed="wishlist.includes(product.id)" class="mt-4 flex w-full items-center justify-center gap-2 text-xs text-slate-500" @click="toggleSaved(wishlist,product.id)"><Heart class="size-4" :class="{'fill-[#0175ea] text-[#0175ea]':wishlist.includes(product.id)}" aria-hidden="true" />{{ wishlist.includes(product.id) ? 'Tersimpan' : 'Simpan produk' }}</button><p class="mt-4 text-[10px] leading-5 text-slate-400">Stok direservasi selama 30 menit setelah pesanan dibuat.</p></aside>
                </div>
                <section class="mt-10 border-t border-slate-100 pt-7"><h2 class="text-lg font-bold">Ulasan pembeli</h2><div class="mt-4 rounded-xl border border-slate-200 bg-slate-50 p-6"><article v-for="review in reviews" :key="review.id" class="mb-4 border-b border-slate-200 pb-4"><p class="text-xs font-semibold">{{ review.buyer }} · {{ review.rating }}/5 · {{ review.created_at }}</p><p class="mt-2 whitespace-pre-line text-sm">{{ review.body }}</p><p v-if="review.vendor_response" class="mt-2 text-xs">Balasan toko: {{ review.vendor_response }}</p></article><p v-if="!reviews.length" class="text-sm">Belum ada ulasan untuk produk ini.</p></div></section><section v-if="relatedProducts.length" class="mt-10"><h2 class="text-lg font-bold">Pilihan lain untukmu</h2><div class="mt-5 grid grid-cols-2 gap-4 sm:grid-cols-4"><SouvenirProductCard v-for="item in relatedProducts" :key="item.id" :product="item" /></div></section>
            </template>

            <template v-else-if="view === 'store' && shop">
                <section class="mt-6 flex flex-wrap items-center gap-4 rounded-2xl border border-slate-200 p-6"><span class="store-avatar !size-16 !text-lg">{{ shop.initials }}</span><div class="min-w-0 flex-1"><h1 class="text-xl font-extrabold">{{ shop.name }}</h1><p class="mt-2 text-xs text-slate-500">{{ shop.city }}</p><p class="mt-2 text-xs text-slate-500">{{ shopProducts.length }} produk pada halaman ini · {{ reviews.length }} ulasan terbaru</p></div><button type="button" class="market-secondary" :aria-pressed="following.includes(shop.id)" @click="toggleSaved(following,shop.id)">{{ following.includes(shop.id) ? 'Mengikuti' : 'Ikuti toko' }}</button></section>
                <nav aria-label="Halaman toko" class="mt-6 flex border-b border-slate-200"><button v-for="tab in [{id:'home',label:'Beranda'},{id:'products',label:'Produk'},{id:'reviews',label:'Ulasan'}]" :key="tab.id" type="button" class="border-b-2 px-5 py-3 text-sm font-bold" :class="storeTab === tab.id ? 'border-[#0175ea] text-[#0175ea]' : 'border-transparent text-slate-500'" @click="storeTab = tab.id">{{ tab.label }}</button></nav><div v-if="storeTab === 'reviews'" class="mt-6 rounded-xl bg-slate-50 p-8 text-sm text-slate-500"><article v-for="review in reviews" :key="review.id" class="mt-4 border-t border-slate-200 pt-4"><p class="text-xs font-semibold">{{ review.buyer }} · {{ review.rating }}/5</p><p class="mt-2 whitespace-pre-line text-sm">{{ review.body }}</p><p v-if="review.vendor_response" class="mt-2 text-xs">Balasan toko: {{ review.vendor_response }}</p></article><p v-if="!reviews.length">Belum ada ulasan pembeli untuk toko ini.</p></div><template v-else><p v-if="storeTab === 'home'" class="mt-6 max-w-2xl text-sm leading-7 text-slate-500">{{ shop.description }}</p><h2 class="mt-7 text-lg font-bold">{{ storeTab === 'home' ? 'Pilihan dari toko ini' : 'Semua produk' }}</h2><div class="mt-5 grid grid-cols-2 gap-4 sm:grid-cols-3 lg:grid-cols-4"><SouvenirProductCard v-for="item in shopProducts" :key="item.id" :product="item" /></div></template>
            </template>

            <template v-else-if="view === 'cart'">
                <h1 class="mt-7 text-2xl font-extrabold">Keranjang oleh-oleh</h1><p class="mt-2 text-sm text-slate-500">Pesanan dikelompokkan per toko agar jadwal dan ongkirnya jelas.</p><div v-if="!cartLines.length" class="mt-6 rounded-2xl border border-dashed border-slate-300 p-12 text-center"><ShoppingBag class="mx-auto size-8 text-slate-400" /><h2 class="mt-4 font-bold">Keranjangmu masih kosong.</h2><Link :href="route('souvenirs.index')" class="market-primary mt-5">Jelajahi etalase <ArrowRight class="size-4" /></Link></div><div v-else class="mt-6 grid items-start gap-6 lg:grid-cols-[1fr_300px]"><div class="space-y-5"><section v-for="group in cartGroups" :key="group.shop.id" class="rounded-2xl border border-slate-200 p-5"><Link :href="route('souvenirs.store',group.shop.id)" class="flex items-center gap-2 text-sm font-bold"><Store class="size-4 text-[#0175ea]" />{{ group.shop.name }}</Link><article v-for="entry in group.lines" :key="entry.key" class="mt-4 flex gap-4 border-t border-slate-100 pt-4"><Link :href="route('souvenirs.show',entry.id)" class="size-20 shrink-0 rounded-lg bg-no-repeat" :style="souvenirPhotoStyle(entry.product)" :aria-label="entry.product.name"></Link><div class="min-w-0 flex-1"><h3 class="text-sm font-semibold">{{ entry.product.name }}</h3><p class="mt-1 text-xs text-slate-500">{{ entry.variant }} · {{ entry.product.availability }}</p><p v-if="entry.note" class="mt-1 break-words text-xs text-slate-500">Catatan: {{ entry.note }}</p><p class="mt-2 text-sm font-bold">{{ formatSouvenirPrice(entry.product.price * entry.quantity) }}</p><div class="mt-3 flex items-center gap-3"><button type="button" aria-label="Kurangi jumlah dalam keranjang" :disabled="entry.quantity <= 1" class="rounded border border-slate-200 p-2 disabled:opacity-30" @click="changeCartQuantity(entry,-1)"><Minus class="size-3" /></button><span class="text-xs">{{ entry.quantity }}</span><button type="button" aria-label="Tambah jumlah dalam keranjang" :disabled="entry.quantity >= entry.product.stock" class="rounded border border-slate-200 p-2 disabled:opacity-30" @click="changeCartQuantity(entry,1)"><Plus class="size-3" /></button><button type="button" :aria-label="`Hapus ${entry.product.name}`" class="ml-auto p-2 text-slate-400 hover:text-rose-600" @click="changeCartQuantity(entry, -entry.quantity)"><Trash2 class="size-4" /></button></div></div></article><button type="button" class="market-secondary mt-5 w-full" @click="showCheckout(group)">Lihat ringkasan toko ini</button></section></div><aside class="rounded-2xl border border-slate-200 p-5"><h2 class="text-sm font-bold">Ringkasan keranjang</h2><div class="mt-5 flex justify-between text-xs"><span>{{ cartCount }} produk</span><strong>{{ formatSouvenirPrice(cartTotal) }}</strong></div><p class="mt-4 text-xs leading-6 text-slate-500">Ongkir dan waktu tiba belum dihitung. Lanjutkan per toko untuk melihat pilihan penerimaan.</p><p class="mt-4 rounded-lg bg-sky-50 p-3 text-xs leading-6 text-[#075890]">Keranjang tersimpan di akun Anda. Stok baru direservasi saat pesanan dibuat.</p></aside></div>

            </template>
        <nav v-if="pagination?.next || pagination?.previous" aria-label="Halaman etalase" class="mt-6 flex justify-center gap-4"><Link v-if="pagination.previous" :href="pagination.previous" class="market-secondary">Sebelumnya</Link><Link v-if="pagination.next" :href="pagination.next" class="market-secondary">Berikutnya</Link></nav></main>
        <MainFooter />
        <dialog ref="shippingDialog" aria-labelledby="shipping-title" class="fixed inset-0 m-auto max-h-[90dvh] w-[calc(100%-32px)] max-w-xl overflow-y-auto rounded-2xl border-0 p-6 text-[#172c50] backdrop:bg-slate-900/50" @click="event => { if (event.target === shippingDialog) shippingDialog.close(); }"><template v-if="product"><div class="flex justify-between"><h2 id="shipping-title" class="text-xl font-bold">Detail pengiriman</h2><button type="button" aria-label="Tutup detail pengiriman" @click="shippingDialog.close()"><X class="size-5" /></button></div><div class="mt-5 rounded-xl border border-slate-200 p-4 text-sm"><p>Dari {{ shop.city }}</p><p class="mt-2 text-xs text-slate-500">Berat {{ product.weight }} gram · {{ product.preparation }}</p></div><div v-if="!product.pickupOnly" class="mt-5 border-b border-slate-100 pb-5"><p class="flex items-center gap-2 text-sm font-bold"><Truck class="size-4 text-[#0175ea]" />Kirim ke rumah</p><p class="mt-2 text-xs leading-6 text-slate-500">Ongkir dan estimasi tiba menunggu alamat serta pilihan kurir. Lama pengiriman dimulai setelah barang siap.</p></div><div class="mt-5"><p class="flex items-center gap-2 text-sm font-bold"><Store class="size-4 text-[#0175ea]" />Ambil di tempat</p><p class="mt-2 text-xs leading-6 text-slate-500">Sesuaikan jadwal pengambilan dengan waktu persiapan. {{ product.pickupOnly ? 'Produk segar ini tidak menyediakan pengiriman jarak jauh.' : 'Lokasi dan jam pengambilan mengikuti toko.' }}</p></div></template></dialog>
    </div>
</template>

<style scoped>
@reference '../../css/app.css';
.market-primary { @apply inline-flex min-h-10 items-center justify-center gap-2 rounded-xl bg-[#0175ea] px-4 py-2.5 text-xs font-bold text-white transition hover:bg-[#005fb8]; }
.market-secondary { @apply inline-flex min-h-10 items-center justify-center gap-2 rounded-xl border border-[#0175ea] px-4 py-2.5 text-xs font-bold text-[#0175ea] transition hover:bg-sky-50; }
.store-avatar { @apply grid size-11 shrink-0 place-items-center rounded-full bg-[#edf6ff] text-xs font-bold text-[#0175ea]; }
.filter-group { @apply border-b border-slate-100 py-4; }
.filter-group legend { @apply float-left mb-3 w-full text-xs font-bold; }
.filter-group label { @apply mb-2 flex clear-both items-center gap-2 text-xs text-slate-500; }
.filter-group input[type='checkbox'] { @apply size-4 accent-[#0175ea]; }
.marketplace :is(button,a,input,select,textarea):focus-visible { outline: 2px solid #0175ea; outline-offset: 3px; }
</style>








