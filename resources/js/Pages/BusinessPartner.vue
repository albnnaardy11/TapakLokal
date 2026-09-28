<script setup>
import { Head, Link } from '@inertiajs/vue3';
import { computed, ref } from 'vue';
import {
    AlertCircle,
    ArrowRight,
    Award,
    BadgeCheck,
    BedDouble,
    Box,
    Building2,
    Calendar,
    Check,
    CheckCircle2,
    ChevronDown,
    ChevronRight,
    Clock,
    Compass,
    Handshake,
    HelpCircle,
    Home,
    MapPin,
    MessageCircle,
    PackageCheck,
    Percent,
    Phone,
    Plus,
    RefreshCw,
    ShieldAlert,
    ShieldCheck,
    ShoppingBag,
    Sparkles,
    Star,
    Store,
    Timer,
    TrendingUp,
    Truck,
    UserCheck,
    Users,
    Utensils,
    Wallet,
    X,
} from 'lucide-vue-next';
import MainNavigation from '../Components/Shared/MainNavigation.vue';
import MainFooter from '../Components/Shared/MainFooter.vue';

// Selected Partnership Track Tab
const selectedTrack = ref('trip'); // 'trip' | 'souvenir'

// Partner Registration Modal State
const isModalOpen = ref(false);
const partnerCategory = ref('trip'); // 'trip' | 'souvenir'
const formData = ref({
    businessName: '',
    ownerName: '',
    phone: '',
    city: '',
    specialty: '',
    notes: '',
});
const isSubmitting = ref(false);
const submitSuccess = ref(false);

const openRegistrationModal = (type = 'trip') => {
    partnerCategory.value = type;
    formData.value = {
        businessName: '',
        ownerName: '',
        phone: '',
        city: '',
        specialty: '',
        notes: '',
    };
    submitSuccess.value = false;
    isModalOpen.value = true;
};

const handleRegisterSubmit = () => {
    isSubmitting.value = true;
    setTimeout(() => {
        isSubmitting.value = false;
        submitSuccess.value = true;
    }, 1000);
};

// Interactive Accommodation Add-On Simulator State
const selectedAddon = ref('villa'); // 'default' | 'villa' | 'local_house'

const accommodationOptions = [
    {
        id: 'default',
        title: 'Homestay Bawaan Trip (Included)',
        badge: 'All-in-One Standar',
        price: 'Sudah termasuk di paket trip',
        desc: 'Kamar bersih & nyaman bersama rombongan trip, lokasi strategis dekat objek wisata utama.',
        tag: 'Paket Dasar',
        icon: Home,
    },
    {
        id: 'villa',
        title: 'Upgrade Private Villa',
        badge: 'Add-On Rekomendasi Vendor',
        price: '+Rp350.000 / malam',
        desc: 'Private pool, view pegunungan/pantai, cocok untuk keluarga atau pasangan yang butuh privasi maksimal.',
        tag: 'Favorit Wisatawan',
        icon: Building2,
    },
    {
        id: 'local_house',
        title: 'Tinggal di Rumah Warga Lokal',
        badge: 'Add-On Budaya Otentik',
        price: '+Rp150.000 / malam',
        desc: 'Pengalaman hidup membaur dengan warga lokal, mencicipi masakan rumahan autentik, dan ramah budaya.',
        tag: 'Pengalaman Otentik',
        icon: Users,
    },
];

// Interactive Food Shelf-Life & Regional Logistics Simulator
const selectedFoodType = ref('wet'); // 'wet' | 'dry' | 'frozen'

const foodLogistics = {
    wet: {
        title: 'Oleh-Oleh Basah / Cepat Basi (1 - 3 Hari)',
        examples: 'Bakpia Basah, Bika Ambon Fresh, Pempek Segar, Lapis Legit Basah',
        shelfLife: 'Maks. 2 - 3 Hari pada suhu ruang',
        coverage: 'Same-day / Next-day Regional Terdekat (Radius 1-2 Provinsi)',
        courier: 'Kurir Kilat Paxel Sameday / Cargo Udara 1 Hari',
        packaging: 'Kemasan Vacuum Sealed + Kardus Bersegel Anti Remuk',
        badgeColor: 'bg-amber-100 text-amber-800 border-amber-200',
    },
    dry: {
        title: 'Oleh-Oleh Kering & Kerajinan (14 - 90 Hari)',
        examples: 'Keripik Tempe, Kopi Gayo, Dodol Garut, Abon Sapi, Kain Tenun',
        shelfLife: '14 Hari hingga 3 Bulan',
        coverage: 'Seluruh Indonesia (Sabang sampai Merauke)',
        courier: 'Ekspedisi Reguler & Kargo Udara/Darat (JNE, J&T, SiCepat, dll)',
        packaging: 'Kotak Gift Box Premium TapakLokal + Bubble Wrap Tebal',
        badgeColor: 'bg-emerald-100 text-emerald-800 border-emerald-200',
    },
    frozen: {
        title: 'Oleh-Oleh Frozen & Olahan Dingin (7 - 30 Hari)',
        examples: 'Bandeng Presto Beku, Rendang Frozen, Siomay Beku, Sei Sapi',
        shelfLife: 'Hingga 1 Bulan dalam Freezer',
        coverage: 'Kota-kota Besar dengan Rute Cold-Chain (Jawa, Bali, Sumatra)',
        courier: 'Cold Storage Transit & Kurir Ice Pack Khusus',
        packaging: 'Insulated Foil Thermal Bag + Dry Ice / Gel Pack',
        badgeColor: 'bg-sky-100 text-sky-800 border-sky-200',
    },
};

// FAQ Accordion
const faqs = ref([
    {
        q: 'Siapa saja yang bisa mendaftar sebagai Mitra Vendor TapakLokal?',
        a: 'Kami membuka kemitraan untuk 2 kategori: (1) Vendor Trip Wisata yang menyelenggarakan Open Trip & Private Trip berizin/berpengalaman, dan (2) Produsen/UMKM Oleh-Oleh Khas Daerah (kuliner, kopi, kriya) yang memiliki produk berkualitas asli daerah.',
        open: true,
    },
    {
        q: 'Bagaimana fitur Add-On Akomodasi (Villa / Rumah Warga) bekerja untuk Vendor Trip?',
        a: 'Paket trip Anda tetap all-in-one dengan homestay bawaan. Namun, Anda dapat menambahkan opsi add-on villa privat atau rumah warga lokal yang telah Anda kurasi. Wisatawan yang ingin upgrade akomodasi cukup memilih add-on saat checkout, dan selisih biayanya langsung menjadi pendapatan tambahan bagi Anda.',
        open: false,
    },
    {
        q: 'Bagaimana sistem memastikan oleh-oleh makanan basah tidak basi di jalan?',
        a: 'Sistem TapakLokal menerapkan Smart Regional Filter. Produk makanan basah (daya tahan 1-3 hari) hanya dapat dipesan oleh pembeli yang berada di radius ekspedisi Sameday/Nextday dari lokasi dapur Anda. Pembeli di luar radius tidak bisa checkout produk tersebut demi menjaga kesegaran.',
        open: false,
    },
    {
        q: 'Kapan dan bagaimana pembayaran dicairkan ke rekening vendor?',
        a: 'Untuk Vendor Trip, dana dicairkan 1x24 jam setelah jadwal trip selesai terlaksana tanpa komplain. Untuk Vendor Oleh-Oleh, dana otomatis cair ke rekening Anda setelah paket diterima oleh pelanggan via resi ekspedisi terverifikasi.',
        open: false,
    },
    {
        q: 'Apakah ada biaya pendaftaran atau bulanan untuk menjadi mitra?',
        a: 'Tidak ada biaya pendaftaran ataupun biaya langganan bulanan (100% Gratis). TapakLokal hanya mengenakan platform fee kecil berbasis bagi hasil per transaksi yang berhasil.',
        open: false,
    },
]);

const toggleFaq = (idx) => {
    faqs.value[idx].open = !faqs.value[idx].open;
};
</script>

<template>
    <Head>
        <title>Partnership & Mitra Vendor • TapakLokal</title>
        <meta
            name="description"
            content="Bermitra dengan TapakLokal. Daftarkan paket Open/Private Trip dengan fitur Add-On Villa/Rumah Warga, atau pasarkan Oleh-Oleh Khas Daerah dengan jangkauan pengiriman regional cerdas."
        />
    </Head>

    <div class="min-h-screen bg-[#f8fafc] font-sans text-[#172c50] selection:bg-[#0088ff] selection:text-white">
        <!-- Main Navigation -->
        <MainNavigation :transparent-on-top="false" />

        <main class="w-full overflow-x-hidden">
            <div class="mx-auto max-w-[1200px] px-4 pt-2.5 pb-20 sm:px-6 sm:pt-3 sm:pb-28 md:pt-4 lg:px-8 lg:pt-5">
                
                <!-- ======================================================= -->
                <!-- 1. HERO BANNER: DUAL TRACK PARTNERSHIP                   -->
                <!-- ======================================================= -->
                <section
                    aria-labelledby="partner-hero-title"
                    class="relative overflow-hidden rounded-[28px] border border-[#dce8f8] bg-[#f8fbff] p-6 sm:rounded-[36px] sm:p-9 md:p-11 lg:rounded-[40px] lg:p-12 xl:p-14 shadow-[0_4px_24px_rgba(37,99,235,0.03)]"
                >
                    <div class="grid grid-cols-1 items-center gap-8 lg:grid-cols-[1.1fr_1fr] lg:gap-8 xl:gap-12">
                        <!-- Left Content -->
                        <div class="text-left">
                            <p class="text-xs font-bold uppercase tracking-[0.14em] text-[#2563eb]">
                                PROGRAM MITRA RESMI
                            </p>
                            <h1
                                id="partner-hero-title"
                                class="mt-2 text-3xl font-extrabold tracking-tight text-[#0f172a] sm:text-4xl md:text-5xl lg:text-[48px] xl:text-[52px] leading-[1.12] sm:leading-[1.08]"
                            >
                                Kembangkan Bisnis Wisata &<br />
                                <span class="text-[#2563eb]">Oleh-Oleh Khas Daerah.</span>
                            </h1>

                            <p class="mt-4 sm:mt-6 max-w-xl text-sm leading-relaxed text-[#475569] sm:text-base lg:text-[17px]">
                                Wadah resmi bagi <strong>Operator Trip (Open & Private Trip All-in-One)</strong> dengan fleksibilitas Add-On akomodasi, serta <strong>UMKM Oleh-Oleh Khas Daerah</strong> dengan jangkauan logistik cerdas berbasis daya tahan makanan.
                            </p>

                            <!-- Dual Action Buttons -->
                            <div class="mt-6 sm:mt-8 flex flex-wrap items-center gap-3 sm:gap-3.5">
                                <button
                                    type="button"
                                    class="group inline-flex min-h-[44px] sm:min-h-[48px] items-center justify-center gap-2 whitespace-nowrap rounded-full bg-[#2563eb] px-6 sm:px-7 py-3 text-xs sm:text-sm font-bold text-white shadow-[0_8px_20px_rgba(37,99,235,0.25)] transition-all duration-300 hover:bg-[#1d4ed8] hover:shadow-[0_12px_28px_rgba(37,99,235,0.4)] hover:-translate-y-0.5 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-[#2563eb] text-center cursor-pointer"
                                    @click="openRegistrationModal('trip')"
                                >
                                    <span>Daftar Mitra Trip</span>
                                    <ArrowRight class="size-4 shrink-0 stroke-[2.5] transition-transform duration-300 group-hover:translate-x-1" aria-hidden="true" />
                                </button>

                                <button
                                    type="button"
                                    class="inline-flex min-h-[44px] sm:min-h-[48px] items-center justify-center gap-2 whitespace-nowrap rounded-full border border-[#e2eaf4] bg-white px-5 sm:px-6 py-3 text-xs sm:text-sm font-bold text-[#1e293b] shadow-xs transition-all duration-200 hover:bg-slate-50 hover:border-[#cbd5e1] hover:shadow-sm hover:-translate-y-0.5 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-[#2563eb] text-center cursor-pointer"
                                    @click="openRegistrationModal('souvenir')"
                                >
                                    <ShoppingBag class="size-4 text-[#2563eb]" />
                                    <span>Mitra Oleh-Oleh</span>
                                </button>
                            </div>
                        </div>

                        <!-- Right Graphic: Hero Photo with Floating Partner Metrics -->
                        <div class="relative flex items-center justify-center lg:justify-end select-none">
                            <div class="relative w-full max-w-[390px] sm:max-w-[430px] lg:max-w-[460px] py-4">
                                <!-- Main Photo Container -->
                                <div class="relative overflow-hidden rounded-[28px] sm:rounded-[36px] border border-[#dce8f8] bg-white p-2 sm:p-2.5 shadow-[0_20px_50px_rgba(37,99,235,0.12)]">
                                    <div class="relative overflow-hidden rounded-[22px] sm:rounded-[28px] aspect-[4/3.8] sm:aspect-[4/3.5] bg-slate-100">
                                        <img
                                            src="/Assets/Images/partner-hero-person.jpg"
                                            alt="Mitra Vendor Wisata & Oleh-Oleh TapakLokal"
                                            class="size-full object-cover object-center transition-transform duration-700 ease-out hover:scale-105"
                                        />
                                        <!-- Soft Gradient Overlay at the bottom -->
                                        <div class="absolute inset-0 bg-gradient-to-t from-slate-900/30 via-transparent to-transparent"></div>
                                    </div>
                                </div>

                                <!-- Floating Badge 1: Top Left - Ekosistem Bisnis -->
                                <div class="absolute -top-1 -left-2 sm:-top-2 sm:-left-3 z-20 flex items-center gap-2.5 rounded-2xl border border-white/90 bg-white/95 px-3.5 py-2.5 shadow-[0_12px_32px_rgba(15,23,42,0.12)] backdrop-blur-md">
                                    <div class="flex size-8 items-center justify-center rounded-xl bg-[#2563eb] text-white shadow-xs">
                                        <Handshake class="size-4" />
                                    </div>
                                    <div>
                                        <p class="text-[10px] font-bold uppercase tracking-wider text-slate-400">EKOSISTEM BISNIS</p>
                                        <p class="text-xs sm:text-sm font-extrabold text-slate-800 leading-tight">Trip & Oleh-Oleh Daerah</p>
                                    </div>
                                </div>

                                <!-- Floating Badge 2: Bottom Right - Pencairan Cepat & Transparan -->
                                <div class="absolute -bottom-2 -right-2 sm:-bottom-3 sm:-right-3 z-20 rounded-2xl border border-white/90 bg-white/95 p-3.5 sm:p-4 shadow-[0_16px_36px_rgba(37,99,235,0.18)] backdrop-blur-md max-w-[210px] sm:max-w-[230px]">
                                    <div class="flex items-center gap-1.5 text-[11px] font-bold text-slate-600">
                                        <Wallet class="size-3.5 text-[#2563eb]" /> Pencairan Mitra
                                    </div>
                                    <p class="mt-1.5 text-base sm:text-lg font-black text-slate-900 tracking-tight">
                                        1x24 Jam
                                    </p>
                                    <p class="text-[10px] font-semibold text-slate-400 mt-0.5">
                                        Otomatis & Tanpa Potongan Tersembunyi
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>

                <!-- ======================================================= -->
                <!-- 2. PILIH JALUR KEMITRAAN: 2 KATEGORI UTAMA               -->
                <!-- ======================================================= -->
                <section class="mt-16 sm:mt-24">
                    <div class="text-center">
                        <p class="text-xs font-bold uppercase tracking-[0.14em] text-[#2563eb]">
                            DUAL TRACK KEMITRAAN
                        </p>
                        <h2 class="mt-2 text-2xl font-black tracking-tight text-[#0f172a] sm:text-3xl md:text-4xl">
                            2 Kategori Mitra Resmi TapakLokal
                        </h2>
                        <p class="mt-3 text-xs sm:text-sm md:text-base text-slate-600 max-w-2xl mx-auto">
                            Dirancang khusus sesuai spesialisasi bisnis Anda dengan sistem dan fitur terlengkap di Indonesia.
                        </p>
                    </div>

                    <!-- Category Tab Selector -->
                    <div class="mt-8 flex justify-center">
                        <div class="inline-flex rounded-full border border-slate-200 bg-white p-1.5 shadow-xs">
                            <button
                                type="button"
                                class="flex items-center gap-2 rounded-full px-5 py-2.5 text-xs sm:text-sm font-bold transition-all cursor-pointer"
                                :class="selectedTrack === 'trip' ? 'bg-[#2563eb] text-white shadow-xs' : 'text-slate-600 hover:text-slate-900'"
                                @click="selectedTrack = 'trip'"
                            >
                                <Compass class="size-4" />
                                <span>1. Mitra Vendor Trip Wisata</span>
                            </button>

                            <button
                                type="button"
                                class="flex items-center gap-2 rounded-full px-5 py-2.5 text-xs sm:text-sm font-bold transition-all cursor-pointer"
                                :class="selectedTrack === 'souvenir' ? 'bg-[#2563eb] text-white shadow-xs' : 'text-slate-600 hover:text-slate-900'"
                                @click="selectedTrack = 'souvenir'"
                            >
                                <ShoppingBag class="size-4" />
                                <span>2. Mitra Vendor Oleh-Oleh</span>
                            </button>
                        </div>
                    </div>

                    <!-- =================================================== -->
                    <!-- TRACK 1: VENDOR TRIP WISATA DETAILS                -->
                    <!-- =================================================== -->
                    <div v-if="selectedTrack === 'trip'" class="mt-10 grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
                        <!-- Left Info Card -->
                        <div class="lg:col-span-6 space-y-6">
                            <div class="rounded-3xl border border-blue-100 bg-white p-6 sm:p-8 shadow-xs">
                                <span class="rounded-full bg-blue-50 px-3 py-1 text-xs font-bold text-[#2563eb] border border-blue-100">
                                    Paket All-in-One + Add-On Fleksibel
                                </span>
                                <h3 class="mt-3 text-xl sm:text-2xl font-black text-[#0f172a]">
                                    Penyedia Trip Wisata (Open & Private Trip)
                                </h3>
                                <p class="mt-3 text-xs sm:text-sm text-slate-600 leading-relaxed">
                                    Paket trip Anda sudah mencakup akomodasi standar bawaan, pemandu lokal (guide), transportasi, dan tiket destinasi dalam satu paket lengkap.
                                </p>

                                <!-- Core Features List -->
                                <div class="mt-6 space-y-3.5">
                                    <div class="flex items-start gap-3">
                                        <div class="flex size-6 shrink-0 items-center justify-center rounded-full bg-emerald-100 text-emerald-600 mt-0.5">
                                            <Check class="size-3.5 stroke-[3]" />
                                        </div>
                                        <div>
                                            <p class="text-xs sm:text-sm font-bold text-slate-800">Paket Wisata All-in-One</p>
                                            <p class="text-xs text-slate-500">Trip hemat tanpa ribet, homestay bawaan + guide sudah include dari vendor.</p>
                                        </div>
                                    </div>

                                    <div class="flex items-start gap-3">
                                        <div class="flex size-6 shrink-0 items-center justify-center rounded-full bg-blue-100 text-[#2563eb] mt-0.5">
                                            <Plus class="size-3.5 stroke-[3]" />
                                        </div>
                                        <div>
                                            <p class="text-xs sm:text-sm font-bold text-slate-800">Fitur Add-On Custom Akomodasi</p>
                                            <p class="text-xs text-slate-500">
                                                Jika wisatawan tidak ingin homestay bawaan, Anda dapat menawarkan opsi <strong>Upgrade ke Private Villa</strong> atau <strong>Tinggal di Rumah Warga Lokal</strong> yang telah Anda kurasi.
                                            </p>
                                        </div>
                                    </div>

                                    <div class="flex items-start gap-3">
                                        <div class="flex size-6 shrink-0 items-center justify-center rounded-full bg-indigo-100 text-indigo-600 mt-0.5">
                                            <Users class="size-3.5 stroke-[3]" />
                                        </div>
                                        <div>
                                            <p class="text-xs sm:text-sm font-bold text-slate-800">Dukungan Open & Private Trip</p>
                                            <p class="text-xs text-slate-500">Atur kuota kursi per tanggal keberangkatan atau tawarkan private charter untuk keluarga & kantor.</p>
                                        </div>
                                    </div>
                                </div>

                                <div class="mt-8 pt-6 border-t border-slate-100">
                                    <button
                                        type="button"
                                        class="w-full sm:w-auto inline-flex items-center justify-center gap-2 rounded-full bg-[#2563eb] px-6 py-3.5 text-xs sm:text-sm font-bold text-white shadow-md hover:bg-[#1d4ed8] transition cursor-pointer"
                                        @click="openRegistrationModal('trip')"
                                    >
                                        <span>Daftar Jadi Vendor Trip</span>
                                        <ArrowRight class="size-4" />
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Right Interactive Add-On Accommodation Simulator -->
                        <div class="lg:col-span-6">
                            <div class="rounded-3xl border border-slate-200 bg-white p-6 sm:p-8 shadow-xs">
                                <div class="flex items-center justify-between border-b border-slate-100 pb-4">
                                    <div>
                                        <p class="text-xs font-bold uppercase tracking-wider text-[#2563eb]">SIMULATOR WISATAWAN</p>
                                        <h4 class="text-base sm:text-lg font-black text-slate-900">Pilihan Akomodasi di Halaman Trip</h4>
                                    </div>
                                    <span class="rounded-full bg-blue-50 px-2.5 py-1 text-[11px] font-bold text-[#2563eb]">
                                        Add-On Ready
                                    </span>
                                </div>

                                <p class="mt-4 text-xs text-slate-500">
                                    Cobalah klik opsi di bawah untuk melihat bagaimana wisatawan dapat memilih akomodasi bawaan atau upgrade villa/rumah warga saat memesan trip Anda:
                                </p>

                                <!-- Interactive Options -->
                                <div class="mt-4 space-y-3">
                                    <div
                                        v-for="opt in accommodationOptions"
                                        :key="opt.id"
                                        class="relative rounded-2xl border p-4 transition-all cursor-pointer"
                                        :class="selectedAddon === opt.id ? 'border-[#2563eb] bg-blue-50/40 ring-2 ring-[#2563eb]/20' : 'border-slate-200 bg-white hover:border-slate-300'"
                                        @click="selectedAddon = opt.id"
                                    >
                                        <div class="flex items-start justify-between gap-3">
                                            <div class="flex items-start gap-3">
                                                <div
                                                    class="flex size-9 shrink-0 items-center justify-center rounded-xl transition"
                                                    :class="selectedAddon === opt.id ? 'bg-[#2563eb] text-white' : 'bg-slate-100 text-slate-600'"
                                                >
                                                    <component :is="opt.icon" class="size-4.5" />
                                                </div>
                                                <div>
                                                    <div class="flex items-center gap-2">
                                                        <p class="text-xs sm:text-sm font-bold text-slate-900">{{ opt.title }}</p>
                                                        <span class="rounded-full bg-slate-100 px-2 py-0.5 text-[10px] font-bold text-slate-600">
                                                            {{ opt.tag }}
                                                        </span>
                                                    </div>
                                                    <p class="mt-1 text-[11px] text-slate-500 leading-relaxed">{{ opt.desc }}</p>
                                                </div>
                                            </div>

                                            <div class="text-right shrink-0">
                                                <p class="text-xs font-black text-[#2563eb]">{{ opt.price }}</p>
                                                <div class="mt-1.5 flex justify-end">
                                                    <div
                                                        class="size-4.5 rounded-full border flex items-center justify-center transition"
                                                        :class="selectedAddon === opt.id ? 'border-[#2563eb] bg-[#2563eb] text-white' : 'border-slate-300 bg-white'"
                                                    >
                                                        <Check v-if="selectedAddon === opt.id" class="size-3 stroke-[3]" />
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Dynamic Booking Summary Box -->
                                <div class="mt-5 rounded-2xl bg-slate-50 p-4 border border-slate-200/80">
                                    <div class="flex items-center justify-between text-xs font-semibold text-slate-700">
                                        <span>Status Pilihan Wisatawan:</span>
                                        <span class="font-bold text-[#2563eb]">
                                            {{ accommodationOptions.find(o => o.id === selectedAddon)?.badge }}
                                        </span>
                                    </div>
                                    <p class="mt-1 text-[11px] text-slate-500">
                                        Vendor langsung menerima detail pilihan akomodasi ini pada manifest peserta untuk dipersiapkan sebelum keberangkatan.
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- =================================================== -->
                    <!-- TRACK 2: VENDOR OLEH-OLEH DETAILS                   -->
                    <!-- =================================================== -->
                    <div v-else class="mt-10 grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
                        <!-- Left Info Card -->
                        <div class="lg:col-span-6 space-y-6">
                            <div class="rounded-3xl border border-amber-100 bg-white p-6 sm:p-8 shadow-xs">
                                <span class="rounded-full bg-amber-50 px-3 py-1 text-xs font-bold text-amber-700 border border-amber-200">
                                    Logistik Cerdas Berbasis Ketahanan Makanan
                                </span>
                                <h3 class="mt-3 text-xl sm:text-2xl font-black text-[#0f172a]">
                                    Produsen & Toko Oleh-Oleh Khas Daerah
                                </h3>
                                <p class="mt-3 text-xs sm:text-sm text-slate-600 leading-relaxed">
                                    Jual produk kuliner autentik dan kerajinan khas daerah Anda ke seluruh wisatawan nusantara dengan sistem pengiriman yang menjamin kesegaran barang.
                                </p>

                                <!-- Core Features List -->
                                <div class="mt-6 space-y-3.5">
                                    <div class="flex items-start gap-3">
                                        <div class="flex size-6 shrink-0 items-center justify-center rounded-full bg-amber-100 text-amber-700 mt-0.5">
                                            <Timer class="size-3.5 stroke-[3]" />
                                        </div>
                                        <div>
                                            <p class="text-xs sm:text-sm font-bold text-slate-800">Filter Ketahanan Produk (Shelf-Life)</p>
                                            <p class="text-xs text-slate-500">
                                                Klasifikasi makanan basah (1-3 hari), makanan beku/frozen, atau makanan kering (hingga 3 bulan).
                                            </p>
                                        </div>
                                    </div>

                                    <div class="flex items-start gap-3">
                                        <div class="flex size-6 shrink-0 items-center justify-center rounded-full bg-sky-100 text-sky-600 mt-0.5">
                                            <Truck class="size-3.5 stroke-[3]" />
                                        </div>
                                        <div>
                                            <p class="text-xs sm:text-sm font-bold text-slate-800">Smart Regional Matching</p>
                                            <p class="text-xs text-slate-500">
                                                Makanan basah hanya dapat dipesan oleh pembeli di rute sameday/next-day, menghindari risiko makanan basi di ekspedisi.
                                            </p>
                                        </div>
                                    </div>

                                    <div class="flex items-start gap-3">
                                        <div class="flex size-6 shrink-0 items-center justify-center rounded-full bg-emerald-100 text-emerald-600 mt-0.5">
                                            <PackageCheck class="size-3.5 stroke-[3]" />
                                        </div>
                                        <div>
                                            <p class="text-xs sm:text-sm font-bold text-slate-800">Standar Kemasan & Resi Otomatis</p>
                                            <p class="text-xs text-slate-500">Integrasi resi kurir otomatis (Paxel, JNE, SiCepat) dan panduan kemasan aman.</p>
                                        </div>
                                    </div>
                                </div>

                                <div class="mt-8 pt-6 border-t border-slate-100">
                                    <button
                                        type="button"
                                        class="w-full sm:w-auto inline-flex items-center justify-center gap-2 rounded-full bg-amber-600 px-6 py-3.5 text-xs sm:text-sm font-bold text-white shadow-md hover:bg-amber-700 transition cursor-pointer"
                                        @click="openRegistrationModal('souvenir')"
                                    >
                                        <ShoppingBag class="size-4" />
                                        <span>Daftar Jadi Vendor Oleh-Oleh</span>
                                        <ArrowRight class="size-4" />
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Right Interactive Regional & Shelf-Life Simulator -->
                        <div class="lg:col-span-6">
                            <div class="rounded-3xl border border-slate-200 bg-white p-6 sm:p-8 shadow-xs">
                                <div class="flex items-center justify-between border-b border-slate-100 pb-4">
                                    <div>
                                        <p class="text-xs font-bold uppercase tracking-wider text-amber-600">SMART LOGISTICS SIMULATOR</p>
                                        <h4 class="text-base sm:text-lg font-black text-slate-900">Ketahanan Produk & Radius Kirim</h4>
                                    </div>
                                    <span class="rounded-full bg-amber-50 px-2.5 py-1 text-[11px] font-bold text-amber-700 border border-amber-200">
                                        Auto Protected
                                    </span>
                                </div>

                                <!-- Food Category Filter Buttons -->
                                <div class="mt-4 grid grid-cols-3 gap-2">
                                    <button
                                        type="button"
                                        class="rounded-xl border p-2.5 text-center transition cursor-pointer"
                                        :class="selectedFoodType === 'wet' ? 'border-amber-500 bg-amber-50/70 font-bold text-amber-800' : 'border-slate-200 text-slate-600 hover:bg-slate-50'"
                                        @click="selectedFoodType = 'wet'"
                                    >
                                        <p class="text-xs">Makanan Basah</p>
                                        <p class="text-[10px] text-amber-600 font-semibold mt-0.5">1 - 3 Hari</p>
                                    </button>

                                    <button
                                        type="button"
                                        class="rounded-xl border p-2.5 text-center transition cursor-pointer"
                                        :class="selectedFoodType === 'frozen' ? 'border-sky-500 bg-sky-50/70 font-bold text-sky-800' : 'border-slate-200 text-slate-600 hover:bg-slate-50'"
                                        @click="selectedFoodType = 'frozen'"
                                    >
                                        <p class="text-xs">Frozen Food</p>
                                        <p class="text-[10px] text-sky-600 font-semibold mt-0.5">Cold-Chain</p>
                                    </button>

                                    <button
                                        type="button"
                                        class="rounded-xl border p-2.5 text-center transition cursor-pointer"
                                        :class="selectedFoodType === 'dry' ? 'border-emerald-500 bg-emerald-50/70 font-bold text-emerald-800' : 'border-slate-200 text-slate-600 hover:bg-slate-50'"
                                        @click="selectedFoodType = 'dry'"
                                    >
                                        <p class="text-xs">Kering / Awet</p>
                                        <p class="text-[10px] text-emerald-600 font-semibold mt-0.5">14 - 90 Hari</p>
                                    </button>
                                </div>

                                <!-- Logistics Detail Card -->
                                <div class="mt-5 rounded-2xl border border-slate-200 bg-slate-50/80 p-5 space-y-3.5">
                                    <div class="flex items-center justify-between">
                                        <h5 class="text-sm font-bold text-slate-800">{{ foodLogistics[selectedFoodType].title }}</h5>
                                        <span class="rounded-full px-2.5 py-0.5 text-[10px] font-bold border" :class="foodLogistics[selectedFoodType].badgeColor">
                                            {{ foodLogistics[selectedFoodType].shelfLife }}
                                        </span>
                                    </div>

                                    <div class="border-t border-slate-200/80 pt-3 space-y-2.5 text-xs">
                                        <div>
                                            <span class="text-slate-500 font-medium">Contoh Produk:</span>
                                            <p class="font-semibold text-slate-800 mt-0.5">{{ foodLogistics[selectedFoodType].examples }}</p>
                                        </div>

                                        <div>
                                            <span class="text-slate-500 font-medium">Jangkauan Pembeli (Regional Coverage):</span>
                                            <p class="font-semibold text-[#2563eb] mt-0.5 flex items-center gap-1.5">
                                                <MapPin class="size-3.5" /> {{ foodLogistics[selectedFoodType].coverage }}
                                            </p>
                                        </div>

                                        <div>
                                            <span class="text-slate-500 font-medium">Layanan Kurir Otomatis:</span>
                                            <p class="font-semibold text-slate-800 mt-0.5 flex items-center gap-1.5">
                                                <Truck class="size-3.5 text-slate-600" /> {{ foodLogistics[selectedFoodType].courier }}
                                            </p>
                                        </div>

                                        <div>
                                            <span class="text-slate-500 font-medium">Standar Kemasan Pengiriman:</span>
                                            <p class="font-semibold text-slate-800 mt-0.5 flex items-center gap-1.5">
                                                <Box class="size-3.5 text-slate-600" /> {{ foodLogistics[selectedFoodType].packaging }}
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>

                <!-- ======================================================= -->
                <!-- 3. KEUNGGULAN BERMITRA DENGAN TAPAKLOKAL               -->
                <!-- ======================================================= -->
                <section class="mt-20 sm:mt-28">
                    <div class="text-center">
                        <p class="text-xs font-bold uppercase tracking-[0.14em] text-[#2563eb]">
                            MENGAPA BERGABUNG?
                        </p>
                        <h2 class="mt-2 text-2xl font-black tracking-tight text-[#0f172a] sm:text-3xl md:text-4xl">
                            Ekosistem Bisnis yang Menguntungkan Vendor
                        </h2>
                    </div>

                    <div class="mt-10 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                        <!-- Perk 1 -->
                        <div class="rounded-3xl border border-slate-100 bg-white p-6 shadow-xs transition hover:shadow-md hover:-translate-y-1">
                            <div class="flex size-12 items-center justify-center rounded-2xl bg-blue-50 text-[#2563eb]">
                                <Users class="size-6" />
                            </div>
                            <h3 class="mt-4 text-base font-bold text-slate-900">Jangkauan Wisatawan Luas</h3>
                            <p class="mt-2 text-xs sm:text-sm text-slate-500 leading-relaxed">
                                Produk & trip Anda dipromosikan ke ribuan pembeli aktif dan jaringan puluhan affiliator konten kreator TapakLokal.
                            </p>
                        </div>

                        <!-- Perk 2 -->
                        <div class="rounded-3xl border border-slate-100 bg-white p-6 shadow-xs transition hover:shadow-md hover:-translate-y-1">
                            <div class="flex size-12 items-center justify-center rounded-2xl bg-emerald-50 text-emerald-600">
                                <Wallet class="size-6" />
                            </div>
                            <h3 class="mt-4 text-base font-bold text-slate-900">Pencairan Dana 1x24 Jam</h3>
                            <p class="mt-2 text-xs sm:text-sm text-slate-500 leading-relaxed">
                                Dana pembayaran otomatis cair ke rekening bank Anda setelah trip selesai atau barang tiba di tangan pembeli tanpa ribet.
                            </p>
                        </div>

                        <!-- Perk 3 -->
                        <div class="rounded-3xl border border-slate-100 bg-white p-6 shadow-xs transition hover:shadow-md hover:-translate-y-1">
                            <div class="flex size-12 items-center justify-center rounded-2xl bg-indigo-50 text-indigo-600">
                                <Calendar class="size-6" />
                            </div>
                            <h3 class="mt-4 text-base font-bold text-slate-900">Dashboard Jadwal & Manifest</h3>
                            <p class="mt-2 text-xs sm:text-sm text-slate-500 leading-relaxed">
                                Rekap peserta trip, pilihan add-on akomodasi, no WA rombongan, serta stok oleh-oleh tercatat rapi secara real-time.
                            </p>
                        </div>

                        <!-- Perk 4 -->
                        <div class="rounded-3xl border border-slate-100 bg-white p-6 shadow-xs transition hover:shadow-md hover:-translate-y-1">
                            <div class="flex size-12 items-center justify-center rounded-2xl bg-amber-50 text-amber-600">
                                <ShieldCheck class="size-6" />
                            </div>
                            <h3 class="mt-4 text-base font-bold text-slate-900">Perlindungan Tanpa Pungli</h3>
                            <p class="mt-2 text-xs sm:text-sm text-slate-500 leading-relaxed">
                                Standar transparansi harga anti-pungli, perlindungan asuransi trip, dan customer service 24/7 yang siap mendampingi.
                            </p>
                        </div>
                    </div>
                </section>

                <!-- ======================================================= -->
                <!-- 4. CARA BERGABUNG: 4 LANGKAH MUDAH                     -->
                <!-- ======================================================= -->
                <section class="mt-20 sm:mt-28">
                    <div class="rounded-[28px] sm:rounded-[36px] bg-[#f0f7ff] border border-blue-100 p-8 sm:p-12 lg:p-14">
                        <div class="text-center">
                            <p class="text-xs font-bold uppercase tracking-[0.14em] text-[#2563eb]">
                                ALUR KEMITRAAN
                            </p>
                            <h2 class="mt-2 text-2xl font-black tracking-tight text-[#0f172a] sm:text-3xl">
                                Mulai Jadi Mitra dalam 4 Langkah
                            </h2>
                        </div>

                        <div class="mt-10 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                            <!-- Step 1 -->
                            <div class="relative rounded-2xl bg-white p-6 shadow-xs">
                                <span class="flex size-9 items-center justify-center rounded-xl bg-[#2563eb] text-sm font-black text-white">1</span>
                                <h4 class="mt-4 text-sm font-bold text-slate-900">Pilih Kategori Kemitraan</h4>
                                <p class="mt-1.5 text-xs text-slate-500 leading-relaxed">
                                    Tentukan apakah Anda mendaftar sebagai Vendor Paket Trip Wisata atau Vendor Oleh-Oleh Khas Daerah.
                                </p>
                            </div>

                            <!-- Step 2 -->
                            <div class="relative rounded-2xl bg-white p-6 shadow-xs">
                                <span class="flex size-9 items-center justify-center rounded-xl bg-[#2563eb] text-sm font-black text-white">2</span>
                                <h4 class="mt-4 text-sm font-bold text-slate-900">Isi Data & Kurasi Usaha</h4>
                                <p class="mt-1.5 text-xs text-slate-500 leading-relaxed">
                                    Kirimkan profil usaha, kontak, serta portofolio trip atau izin edar produk oleh-oleh Anda untuk ditinjau.
                                </p>
                            </div>

                            <!-- Step 3 -->
                            <div class="relative rounded-2xl bg-white p-6 shadow-xs">
                                <span class="flex size-9 items-center justify-center rounded-xl bg-[#2563eb] text-sm font-black text-white">3</span>
                                <h4 class="mt-4 text-sm font-bold text-slate-900">Unggah Paket & Add-On</h4>
                                <p class="mt-1.5 text-xs text-slate-500 leading-relaxed">
                                    Input jadwal trip, homestay, opsi add-on villa, atau atur ketahanan produk dan foto oleh-oleh.
                                </p>
                            </div>

                            <!-- Step 4 -->
                            <div class="relative rounded-2xl bg-white p-6 shadow-xs">
                                <span class="flex size-9 items-center justify-center rounded-xl bg-emerald-500 text-sm font-black text-white">4</span>
                                <h4 class="mt-4 text-sm font-bold text-slate-900">Terima Booking & Cairkan Dana</h4>
                                <p class="mt-1.5 text-xs text-slate-500 leading-relaxed">
                                    Terima pesanan otomatis, sambut wisatawan, dan nikmati pencairan dana 1x24 jam langsung ke rekening.
                                </p>
                            </div>
                        </div>

                        <!-- CTA inside steps -->
                        <div class="mt-10 text-center">
                            <button
                                type="button"
                                class="inline-flex items-center gap-2 rounded-full bg-[#2563eb] px-8 py-3.5 text-sm font-bold text-white shadow-md hover:bg-[#1d4ed8] transition cursor-pointer"
                                @click="openRegistrationModal(selectedTrack)"
                            >
                                <span>Daftar Kemitraan Sekarang</span>
                                <ArrowRight class="size-4" />
                            </button>
                        </div>
                    </div>
                </section>

                <!-- ======================================================= -->
                <!-- 5. FAQ SEPUTAR KEMITRAAN                               -->
                <!-- ======================================================= -->
                <section class="mt-20 sm:mt-28">
                    <div class="text-center">
                        <p class="text-xs font-bold uppercase tracking-[0.14em] text-[#2563eb]">
                            TANYA JAWAB
                        </p>
                        <h2 class="mt-2 text-2xl font-black tracking-tight text-[#0f172a] sm:text-3xl">
                            Pertanyaan Seputar Kemitraan
                        </h2>
                    </div>

                    <div class="mt-8 max-w-3xl mx-auto space-y-3">
                        <div
                            v-for="(faq, idx) in faqs"
                            :key="idx"
                            class="rounded-2xl border border-slate-200 bg-white overflow-hidden transition"
                        >
                            <button
                                type="button"
                                class="flex w-full items-center justify-between p-5 text-left font-bold text-sm sm:text-base text-slate-900 transition hover:bg-slate-50 cursor-pointer"
                                @click="toggleFaq(idx)"
                            >
                                <span>{{ faq.q }}</span>
                                <ChevronDown
                                    class="size-4.5 text-slate-400 shrink-0 transition-transform duration-200"
                                    :class="{ 'rotate-180 text-[#2563eb]': faq.open }"
                                />
                            </button>
                            <div v-show="faq.open" class="px-5 pb-5 text-xs sm:text-sm text-slate-600 leading-relaxed border-t border-slate-100 pt-3">
                                {{ faq.a }}
                            </div>
                        </div>
                    </div>
                </section>

                <!-- ======================================================= -->
                <!-- 6. BOTTOM CINEMATIC CTA BANNER                         -->
                <!-- ======================================================= -->
                <section class="mt-20 sm:mt-28">
                    <div class="relative overflow-hidden rounded-[28px] sm:rounded-[36px] lg:rounded-[40px] border border-slate-700/40 p-8 sm:p-12 md:p-16 text-center text-white shadow-[0_20px_50px_rgba(5,19,41,0.35)] select-none">
                        <!-- Background Landscape Photo -->
                        <img
                            src="https://images.unsplash.com/photo-1507525428034-b723cf961d3e?auto=format&fit=crop&w=1600&q=85"
                            alt="Kemitraan Wisata Indonesia"
                            class="absolute inset-0 size-full object-cover object-center scale-105"
                        />

                        <!-- Overlays -->
                        <div class="absolute inset-0 bg-gradient-to-r from-[#05172e]/92 via-[#092548]/85 to-[#061b34]/90"></div>
                        <div class="absolute inset-0 bg-gradient-to-t from-[#041224] via-transparent to-[#041224]/70"></div>

                        <!-- Content -->
                        <div class="relative z-10 mx-auto max-w-2xl">
                            <h2 class="text-2xl font-black tracking-tight text-white sm:text-3xl lg:text-4xl leading-tight">
                                Siap Menjadi Bagian dari Kemajuan Wisata Nusantara?
                            </h2>
                            <p class="mt-3.5 text-xs sm:text-sm md:text-[15px] leading-relaxed text-slate-200/90 font-normal max-w-xl mx-auto">
                                Daftarkan paket trip atau produk oleh-oleh Anda hari ini. Bersama-sama, kita majukan ekosistem pariwisata dan ekonomi lokal Indonesia.
                            </p>

                            <!-- Action Buttons -->
                            <div class="mt-8 flex flex-wrap items-center justify-center gap-3 sm:gap-4">
                                <button
                                    type="button"
                                    class="group inline-flex items-center gap-2.5 rounded-full bg-white px-7 py-3.5 text-xs sm:text-sm font-bold text-[#0c2340] shadow-[0_10px_28px_rgba(0,0,0,0.3)] transition-all duration-300 hover:bg-[#0088ff] hover:text-white hover:shadow-[0_14px_35px_rgba(0,136,255,0.5)] hover:-translate-y-0.5 active:scale-98 cursor-pointer"
                                    @click="openRegistrationModal('trip')"
                                >
                                    <span>Daftar Jadi Mitra Sekarang</span>
                                    <ArrowRight class="size-4 stroke-[2.5] transition-transform duration-300 group-hover:translate-x-1" />
                                </button>

                                <Link
                                    href="/bantuan"
                                    class="inline-flex items-center gap-2 rounded-full border border-white/25 bg-white/10 px-6 py-3.5 text-xs sm:text-sm font-bold text-white backdrop-blur-md transition-all duration-300 hover:bg-white hover:text-[#0c2340] hover:border-white hover:shadow-[0_10px_28px_rgba(0,0,0,0.25)] hover:-translate-y-0.5 active:scale-98"
                                >
                                    <span>Pusat Bantuan Mitra</span>
                                </Link>
                            </div>
                        </div>
                    </div>
                </section>

            </div>
        </main>

        <!-- =========================================================== -->
        <!-- PARTNER REGISTRATION MODAL                                  -->
        <!-- =========================================================== -->
        <Teleport to="body">
            <Transition
                enter-active-class="transition duration-200 ease-out"
                enter-from-class="opacity-0"
                enter-to-class="opacity-100"
                leave-active-class="transition duration-150 ease-in"
                leave-from-class="opacity-100"
                leave-to-class="opacity-0"
            >
                <div
                    v-if="isModalOpen"
                    class="fixed inset-0 z-[100] flex items-center justify-center bg-slate-900/60 p-4 backdrop-blur-xs"
                    role="dialog"
                    aria-modal="true"
                    @click.self="isModalOpen = false"
                >
                    <div class="relative w-full max-w-[500px] overflow-hidden rounded-[28px] sm:rounded-[32px] bg-white shadow-[0_24px_60px_rgba(15,23,42,0.25)] border border-slate-100">
                        <!-- Close Button -->
                        <button
                            type="button"
                            class="absolute right-4 top-4 z-20 flex size-8 items-center justify-center rounded-full bg-slate-100 text-slate-500 hover:bg-slate-200 transition cursor-pointer"
                            @click="isModalOpen = false"
                        >
                            <X class="size-4" />
                        </button>

                        <!-- Modal Header -->
                        <div class="bg-gradient-to-b from-[#edf5ff] to-white px-6 pt-7 pb-4 sm:px-8">
                            <span class="rounded-full bg-blue-100 px-3 py-1 text-[11px] font-bold text-[#2563eb]">
                                {{ partnerCategory === 'trip' ? 'Kemitraan Trip Wisata' : 'Kemitraan Oleh-Oleh Khas' }}
                            </span>
                            <h3 class="mt-2 text-xl font-black text-slate-900">
                                Formulir Pendaftaran Mitra
                            </h3>
                            <p class="mt-1 text-xs text-slate-500">
                                Isi data usaha Anda, tim kurasi TapakLokal akan menghubungi Anda via WhatsApp dalam 1x24 jam.
                            </p>
                        </div>

                        <!-- Form Body / Success Screen -->
                        <div class="p-6 sm:p-8">
                            <div v-if="submitSuccess" class="text-center py-6">
                                <div class="mx-auto flex size-14 items-center justify-center rounded-full bg-emerald-100 text-emerald-600">
                                    <CheckCircle2 class="size-8 stroke-[2.5]" />
                                </div>
                                <h4 class="mt-4 text-lg font-extrabold text-slate-900">Pendaftaran Berhasil Terkirim!</h4>
                                <p class="mt-2 text-xs sm:text-sm text-slate-600 leading-relaxed">
                                    Terima kasih telah bergabung. Tim kemitraan TapakLokal akan segera meninjau data <strong>{{ formData.businessName || 'usaha Anda' }}</strong> dan menghubungi via WhatsApp.
                                </p>
                                <button
                                    type="button"
                                    class="mt-6 rounded-full bg-[#2563eb] px-6 py-2.5 text-xs sm:text-sm font-bold text-white shadow-xs hover:bg-[#1d4ed8] transition cursor-pointer"
                                    @click="isModalOpen = false"
                                >
                                    Selesai
                                </button>
                            </div>

                            <form v-else @submit.prevent="handleRegisterSubmit" class="space-y-4">
                                <!-- Pilih Kategori -->
                                <div>
                                    <label class="block text-xs font-bold text-slate-700">Kategori Usaha</label>
                                    <div class="mt-1.5 grid grid-cols-2 gap-2">
                                        <button
                                            type="button"
                                            class="rounded-xl border p-2.5 text-xs font-bold transition text-left cursor-pointer"
                                            :class="partnerCategory === 'trip' ? 'border-[#2563eb] bg-blue-50/50 text-[#2563eb]' : 'border-slate-200 text-slate-600'"
                                            @click="partnerCategory = 'trip'"
                                        >
                                            <Compass class="size-3.5 mb-1" />
                                            <span>Paket Trip Wisata</span>
                                        </button>
                                        <button
                                            type="button"
                                            class="rounded-xl border p-2.5 text-xs font-bold transition text-left cursor-pointer"
                                            :class="partnerCategory === 'souvenir' ? 'border-[#2563eb] bg-blue-50/50 text-[#2563eb]' : 'border-slate-200 text-slate-600'"
                                            @click="partnerCategory = 'souvenir'"
                                        >
                                            <ShoppingBag class="size-3.5 mb-1" />
                                            <span>Oleh-Oleh / Kuliner</span>
                                        </button>
                                    </div>
                                </div>

                                <!-- Nama Usaha -->
                                <div>
                                    <label class="block text-xs font-bold text-slate-700">Nama Usaha / Brand</label>
                                    <input
                                        v-model="formData.businessName"
                                        type="text"
                                        required
                                        placeholder="Contoh: Brenggo Tour / Bakpia Pathok 25"
                                        class="mt-1 w-full rounded-xl border border-slate-200 px-3.5 py-2.5 text-xs sm:text-sm text-slate-800 placeholder:text-slate-400 focus:border-[#2563eb] focus:ring-1 focus:ring-[#2563eb] outline-none"
                                    />
                                </div>

                                <!-- Nama Penanggung Jawab & No WA -->
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                    <div>
                                        <label class="block text-xs font-bold text-slate-700">Nama Pemilik / PIC</label>
                                        <input
                                            v-model="formData.ownerName"
                                            type="text"
                                            required
                                            placeholder="Nama lengkap"
                                            class="mt-1 w-full rounded-xl border border-slate-200 px-3.5 py-2.5 text-xs sm:text-sm text-slate-800 placeholder:text-slate-400 focus:border-[#2563eb] focus:ring-1 focus:ring-[#2563eb] outline-none"
                                        />
                                    </div>
                                    <div>
                                        <label class="block text-xs font-bold text-slate-700">Nomor WhatsApp Aktif</label>
                                        <input
                                            v-model="formData.phone"
                                            type="tel"
                                            required
                                            placeholder="0812xxxxxxxx"
                                            class="mt-1 w-full rounded-xl border border-slate-200 px-3.5 py-2.5 text-xs sm:text-sm text-slate-800 placeholder:text-slate-400 focus:border-[#2563eb] focus:ring-1 focus:ring-[#2563eb] outline-none"
                                        />
                                    </div>
                                </div>

                                <!-- Kota / Asal Daerah -->
                                <div>
                                    <label class="block text-xs font-bold text-slate-700">Kota / Regional Basis Usaha</label>
                                    <input
                                        v-model="formData.city"
                                        type="text"
                                        required
                                        placeholder="Contoh: Labuan Bajo, NTT / Yogyakarta"
                                        class="mt-1 w-full rounded-xl border border-slate-200 px-3.5 py-2.5 text-xs sm:text-sm text-slate-800 placeholder:text-slate-400 focus:border-[#2563eb] focus:ring-1 focus:ring-[#2563eb] outline-none"
                                    />
                                </div>

                                <!-- Detail Tambahan -->
                                <div>
                                    <label class="block text-xs font-bold text-slate-700">
                                        {{ partnerCategory === 'trip' ? 'Destinasi Utama & Opsi Add-on Homestay/Villa' : 'Jenis Produk Kuliner & Ketahanan Makanan' }}
                                    </label>
                                    <textarea
                                        v-model="formData.notes"
                                        rows="2"
                                        placeholder="Jelaskan secara singkat paket trip atau produk oleh-oleh Anda..."
                                        class="mt-1 w-full rounded-xl border border-slate-200 px-3.5 py-2 text-xs sm:text-sm text-slate-800 placeholder:text-slate-400 focus:border-[#2563eb] focus:ring-1 focus:ring-[#2563eb] outline-none"
                                    ></textarea>
                                </div>

                                <button
                                    type="submit"
                                    class="w-full mt-4 flex items-center justify-center gap-2 rounded-full bg-[#2563eb] px-6 py-3 text-xs sm:text-sm font-bold text-white shadow-md hover:bg-[#1d4ed8] transition cursor-pointer disabled:opacity-60"
                                    :disabled="isSubmitting"
                                >
                                    <Sparkles v-if="isSubmitting" class="size-4 animate-spin" />
                                    <span v-if="!isSubmitting">Kirim Formulir Pendaftaran</span>
                                    <span v-else>Mengirim Data...</span>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </Transition>
        </Teleport>

        <!-- Main Footer -->
        <MainFooter />
    </div>
</template>
