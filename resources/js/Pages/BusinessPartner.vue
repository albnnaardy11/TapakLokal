<script setup>
import { Head, Link } from '@inertiajs/vue3';
import { ref } from 'vue';
import {
    ArrowRight,
    BadgeCheck,
    Banknote,
    Building2,
    Calendar,
    Check,
    CheckCircle2,
    ChevronDown,
    Clock,
    Compass,
    Handshake,
    HelpCircle,
    Layers,
    MapPin,
    PackageCheck,
    ShieldCheck,
    ShoppingBag,
    Sparkles,
    Store,
    TrendingUp,
    Truck,
    Users,
    Utensils,
    Wallet,
    X,
    Zap,
} from 'lucide-vue-next';
import MainNavigation from '../Components/Shared/MainNavigation.vue';
import MainFooter from '../Components/Shared/MainFooter.vue';

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

// Trust Badges / Stats Bar (Traveloka Style)
const partnerHighlights = [
    {
        icon: Banknote,
        title: '0% Biaya Pendaftaran',
        subtitle: 'Bebas biaya langganan bulanan tanpa komitmen kuota penjualan minimum.',
    },
    {
        icon: Zap,
        title: 'Pencairan Dana 1x24 Jam',
        subtitle: 'Otomatis cair ke rekening bank Anda setelah trip atau pesanan selesai.',
    },
    {
        icon: Layers,
        title: 'Fitur Add-On Fleksibel',
        subtitle: 'Sediakan opsi upgrade Private Villa & Rumah Warga untuk margin ekstra.',
    },
    {
        icon: ShieldCheck,
        title: 'Smart Regional Logistics',
        subtitle: 'Proteksi otomatis makanan basah (1-3 hari) khusus kurir Sameday/Next-Day.',
    },
];

// 2 Partnership Pillars (Compact Traveloka Style)
const partnerTracks = [
    {
        id: 'trip',
        categoryTag: 'VENDOR TRIP WISATA',
        badgeColor: 'bg-[#2563eb]',
        title: 'Paket Trip Wisata (Open & Private)',
        description: 'Pasarkan paket wisata all-in-one dengan fleksibilitas opsi upgrade akomodasi bagi wisatawan.',
        image: 'https://images.unsplash.com/photo-1544644181-1484b3fdfc62?auto=format&fit=crop&w=700&q=80',
        imageAlt: 'Wisata Alam & Open Trip Indonesia',
        features: [
            'Paket All-in-One: Guide, transportasi & homestay bawaan',
            'Add-On Fleksibel: Opsi upgrade Private Villa & Rumah Warga',
            'Fleksibilitas Kuota: Dukungan Open Trip & Private Charter',
            'Pencairan 1x24 Jam: Otomatis cair setelah trip selesai',
        ],
        ctaText: 'Daftar Mitra Trip',
    },
    {
        id: 'souvenir',
        categoryTag: 'VENDOR OLEH-OLEH & KULINER',
        badgeColor: 'bg-emerald-600',
        title: 'Oleh-Oleh & Kuliner Khas Daerah',
        description: 'Jangkau penikmat kuliner nusantara dengan proteksi kesegaran berbasis ketahanan makanan.',
        image: 'https://images.unsplash.com/photo-1555396273-367ea4eb4db5?auto=format&fit=crop&w=700&q=80',
        imageAlt: 'Kuliner & Oleh-Oleh Tradisional Khas Daerah',
        features: [
            'Smart Shelf-Life: Makanan basah (1-3 hari) via Sameday/Next-Day',
            'Jangkauan Nasional: Makanan kering, kopi & kriya se-Indonesia',
            'Pick-up Logistik: Kurir otomatis jemput paket ke lokasi usaha',
            'Pencairan Aman: Otomatis ditransfer saat barang tiba di pembeli',
        ],
        ctaText: 'Daftar Mitra Oleh-Oleh',
    },
];

// Why Join TapakLokal (4 Value Propositions)
const whyJoinBenefits = [
    {
        icon: Users,
        title: 'Jangkau Wisatawan Se-Indonesia',
        description: 'Terhubung langsung dengan ribuan penjelajah aktif yang mencari pengalaman liburan autentik dan produk khas daerah.',
    },
    {
        icon: Wallet,
        title: 'Pencairan Dana Pasti & Tepat Waktu',
        description: 'Dana hasil penjualan langsung ditransfer ke rekening bank Anda dalam 1x24 jam setelah trip atau pesanan selesai.',
    },
    {
        icon: Layers,
        title: 'Manajemen Produk & Kuota Fleksibel',
        description: 'Atur jadwal keberangkatan, batas kuota peserta, opsi add-on villa, hingga daya tahan makanan secara mandiri.',
    },
    {
        icon: ShieldCheck,
        title: 'Keamanan Transaksi & Bebas Ribet',
        description: 'Seluruh pembayaran wisatawan diverifikasi secara otomatis. Tidak perlu lagi mencatat transfer manual atau khawatir cancelation bodong.',
    },
];

// 4 Simple Onboarding Steps (Traveloka Style)
const steps = [
    {
        number: 1,
        title: 'Daftar Kemitraan',
        description: 'Isi formulir online dan pilih kategori usaha Anda (Vendor Trip atau Vendor Oleh-Oleh) dalam 2 menit.',
        icon: Store,
    },
    {
        number: 2,
        title: 'Kurasi & Verifikasi',
        description: 'Tim kurasi TapakLokal memverifikasi data legalitas usaha atau portofolio rute wisata dalam 1x24 jam.',
        icon: BadgeCheck,
    },
    {
        number: 3,
        title: 'Unggah Paket & Produk',
        description: 'Atur jadwal open trip, opsi add-on villa/rumah warga, atau tentukan daya tahan produk kuliner Anda.',
        icon: PackageCheck,
    },
    {
        number: 4,
        title: 'Mulai Terima Pesanan',
        description: 'Terima booking dari wisatawan nusantara dan nikmati pencairan dana otomatis langsung ke rekening bank Anda.',
        icon: Banknote,
    },
];

// FAQs
const faqs = ref([
    {
        q: 'Siapa saja yang bisa bergabung menjadi Mitra Vendor di TapakLokal?',
        a: 'Kemitraan terbuka bagi Operator Trip Wisata (Open Trip & Private Trip) yang menyediakan paket wisata lengkap dengan pemandu dan transportasi, serta Produsen UMKM Oleh-Oleh Khas Daerah (makanan basah, makanan kering, kopi, dan kerajinan kriya autentik).',
        open: true,
    },
    {
        q: 'Bagaimana fitur Add-On Akomodasi (Villa / Rumah Warga) bekerja untuk Vendor Trip?',
        a: 'Paket trip Anda tetap all-in-one dengan homestay bawaan standar. Anda dapat menambahkan opsi Add-On seperti Upgrade ke Private Villa atau tinggal di Rumah Warga Lokal. Saat wisatawan memilih opsi tersebut saat checkout, selisih biaya langsung menjadi pendapatan tambahan vendor dan otomatis tercatat pada manifest peserta.',
        open: false,
    },
    {
        q: 'Bagaimana sistem memastikan oleh-oleh makanan basah tidak basi di perjalanan?',
        a: 'Sistem TapakLokal menggunakan Smart Regional Filter. Produk makanan basah dengan daya tahan 1-3 hari hanya akan ditampilkan dan dapat dibeli oleh pelanggan dalam radius jangkauan ekspedisi Sameday atau Next-Day dari dapur Anda.',
        open: false,
    },
    {
        q: 'Kapan dan bagaimana pencairan dana ditransfer ke rekening vendor?',
        a: 'Untuk Vendor Trip, dana otomatis cair ke rekening bank Anda 1x24 jam setelah trip selesai terlaksana tanpa kendala. Untuk Vendor Oleh-Oleh, dana cair segera setelah paket terkonfirmasi diterima dengan baik oleh pembeli berdasarkan pelacakan resi ekspedisi.',
        open: false,
    },
    {
        q: 'Apakah ada biaya pendaftaran atau biaya langganan bulanan?',
        a: 'Pendaftaran kemitraan di TapakLokal 100% Gratis selamanya tanpa ada biaya langganan bulanan maupun target penjualan minimum. TapakLokal hanya mengenakan platform fee transparan berbasis bagi hasil dari setiap transaksi yang berhasil.',
        open: false,
    },
]);

const toggleFaq = (index) => {
    faqs.value[index].open = !faqs.value[index].open;
};
</script>

<template>
    <Head>
        <title>Program Kemitraan & Mitra Vendor • TapakLokal</title>
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
                <!-- 1. HERO SECTION (APPROVED - PRESERVED 1:1)               -->
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

                            <!-- Action Buttons -->
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
                                        <p class="text-[10px] font-bold uppercase tracking-wider text-slate-400">EKOSISTEM MITRA</p>
                                        <p class="text-xs sm:text-sm font-extrabold text-slate-800 leading-tight">Trip & Oleh-Oleh Daerah</p>
                                    </div>
                                </div>

                                <!-- Floating Badge 2: Bottom Right - Pencairan Cepat & Transparan -->
                                <div class="absolute -bottom-2 -right-2 sm:-bottom-3 sm:-right-3 z-20 rounded-2xl border border-white/90 bg-white/95 p-3.5 sm:p-4 shadow-[0_16px_36px_rgba(37,99,235,0.18)] backdrop-blur-md max-w-[210px] sm:max-w-[230px]">
                                    <div class="flex items-center gap-1.5 text-[11px] font-bold text-slate-600">
                                        <Wallet class="size-3.5 text-[#2563eb]" /> Pencairan Dana
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
                <!-- 2. SECTION: PARTNER VALUE HIGHLIGHTS (B2B TRUST BAR)    -->
                <!-- ======================================================= -->
                <section class="mt-6 sm:mt-8" aria-label="Keunggulan Kemitraan">
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-3.5">
                        <div
                            v-for="(item, idx) in partnerHighlights"
                            :key="idx"
                            class="flex items-start gap-3 rounded-2xl border border-[#e2eaf4] bg-white p-4 shadow-[0_2px_8px_rgba(23,44,80,0.02)] transition-all duration-300 hover:border-[#93c5fd] hover:shadow-[0_6px_16px_rgba(37,99,235,0.06)]"
                        >
                            <div class="flex size-9 shrink-0 items-center justify-center rounded-xl bg-[#edf5fe] text-[#2563eb]">
                                <component :is="item.icon" class="size-4.5 stroke-[2.2]" />
                            </div>
                            <div class="pt-0.5">
                                <h2 class="text-xs sm:text-[13px] font-bold text-[#0f172a] leading-snug">
                                    {{ item.title }}
                                </h2>
                                <p class="mt-0.5 text-[11px] sm:text-[11.5px] leading-relaxed text-[#556987]">
                                    {{ item.subtitle }}
                                </p>
                            </div>
                        </div>
                    </div>
                </section>

                <!-- ======================================================= -->
                <!-- 3. SECTION: 2 PILAR KEMITRAAN (COMPACT TRAVELOKA STYLE)  -->
                <!-- ======================================================= -->
                <section class="mt-10 sm:mt-14" aria-labelledby="tracks-heading">
                    <div class="text-center max-w-2xl mx-auto">
                        <p class="text-[11px] font-bold uppercase tracking-[0.14em] text-[#2563eb]">
                            SPESIALISASI BISNIS
                        </p>
                        <h2 id="tracks-heading" class="mt-1 text-xl font-extrabold tracking-tight text-[#0f172a] sm:text-2xl md:text-3xl">
                            2 Kategori Mitra Resmi TapakLokal
                        </h2>
                        <p class="mt-1 text-xs sm:text-sm text-[#556987]">
                            Pilih jalur kemitraan yang sesuai dengan model bisnis dan operasional Anda.
                        </p>
                    </div>

                    <!-- 2 Compact Cards Grid -->
                    <div class="mt-6 sm:mt-8 grid grid-cols-1 md:grid-cols-2 gap-4 sm:gap-6">
                        <div
                            v-for="track in partnerTracks"
                            :key="track.id"
                            class="group flex flex-col justify-between overflow-hidden rounded-2xl sm:rounded-3xl border border-[#e2eaf4] bg-white p-4.5 sm:p-5 shadow-[0_2px_12px_rgba(15,23,42,0.03)] transition-all duration-300 hover:border-[#93c5fd] hover:shadow-[0_12px_28px_rgba(37,99,235,0.08)] hover:-translate-y-0.5"
                        >
                            <div>
                                <!-- Compact Image Banner -->
                                <div class="relative aspect-[21/9] sm:aspect-[2.4/1] w-full overflow-hidden rounded-xl bg-slate-100">
                                    <img
                                        :src="track.image"
                                        :alt="track.imageAlt"
                                        class="size-full object-cover object-center transition-transform duration-700 ease-out group-hover:scale-105"
                                    />
                                    <div class="absolute inset-0 bg-gradient-to-t from-slate-900/60 via-slate-900/20 to-transparent"></div>
                                    <div class="absolute top-2.5 left-2.5">
                                        <span class="rounded-full px-2.5 py-0.5 text-[9.5px] font-bold uppercase tracking-wider text-white shadow-xs backdrop-blur-xs" :class="track.badgeColor">
                                            {{ track.categoryTag }}
                                        </span>
                                    </div>
                                </div>

                                <!-- Text Details -->
                                <div class="mt-3.5">
                                    <h3 class="text-base sm:text-lg font-extrabold text-[#0f172a] group-hover:text-[#2563eb] transition-colors leading-snug">
                                        {{ track.title }}
                                    </h3>
                                    <p class="mt-1 text-xs text-[#556987] leading-relaxed">
                                        {{ track.description }}
                                    </p>

                                    <!-- Compact 1-line Feature Bullets -->
                                    <div class="mt-3.5 space-y-2 pt-3 border-t border-slate-100">
                                        <div
                                            v-for="(feat, fIdx) in track.features"
                                            :key="fIdx"
                                            class="flex items-center gap-2.5"
                                        >
                                            <div class="flex size-4.5 shrink-0 items-center justify-center rounded-full bg-[#edf5fe] text-[#2563eb]">
                                                <Check class="size-3 stroke-[3]" />
                                            </div>
                                            <span class="text-xs text-[#334155] font-medium leading-tight">
                                                {{ feat }}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Compact Action Button -->
                            <div class="mt-5 pt-1">
                                <button
                                    type="button"
                                    class="w-full inline-flex items-center justify-center gap-2 rounded-full bg-[#2563eb] hover:bg-[#1d4ed8] px-5 py-2.5 text-xs sm:text-sm font-bold text-white shadow-[0_4px_14px_rgba(37,99,235,0.2)] transition-all duration-200 hover:-translate-y-0.5 active:scale-98 cursor-pointer"
                                    @click="openRegistrationModal(track.id)"
                                >
                                    <span>{{ track.ctaText }}</span>
                                    <ArrowRight class="size-3.5 stroke-[2.5]" />
                                </button>
                            </div>
                        </div>
                    </div>
                </section>

                <!-- ======================================================= -->
                <!-- 4. SECTION: KENAPA BERMITRA (4 VALUE PROPS)             -->
                <!-- ======================================================= -->
                <section class="mt-10 sm:mt-14" aria-labelledby="why-partner-title">
                    <div class="relative overflow-hidden rounded-2xl sm:rounded-3xl border border-[#dce8f8] bg-[#f8fbff] p-5 sm:p-7 md:p-8 shadow-[0_2px_16px_rgba(37,99,235,0.03)]">
                        <div class="text-center max-w-2xl mx-auto">
                            <p class="text-[11px] font-bold uppercase tracking-[0.14em] text-[#2563eb]">
                                KEUNGGULAN EKOSISTEM
                            </p>
                            <h2 id="why-partner-title" class="mt-1 text-xl font-extrabold tracking-tight text-[#0f172a] sm:text-2xl">
                                Kenapa Bermitra dengan TapakLokal?
                            </h2>
                            <p class="mt-1 text-xs sm:text-sm text-[#556987]">
                                Infrastruktur andal dan transparan untuk mendukung pertumbuhan bisnis lokal Anda.
                            </p>
                        </div>

                        <div class="mt-6 sm:mt-8 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3.5 sm:gap-4">
                            <div
                                v-for="(item, idx) in whyJoinBenefits"
                                :key="idx"
                                class="group flex flex-col justify-between rounded-2xl border border-[#e2eaf4] bg-white p-4 sm:p-4.5 shadow-[0_2px_8px_rgba(23,44,80,0.02)] transition-all duration-300 hover:border-[#93c5fd] hover:shadow-[0_8px_20px_rgba(37,99,235,0.06)] hover:-translate-y-0.5"
                            >
                                <div>
                                    <div class="flex size-10 items-center justify-center rounded-xl bg-[#edf5fe] text-[#2563eb] transition-transform duration-300 group-hover:scale-105">
                                        <component :is="item.icon" class="size-5 stroke-[2.2]" />
                                    </div>
                                    <h3 class="mt-3 text-xs sm:text-sm font-bold text-[#0f172a] group-hover:text-[#2563eb] transition-colors leading-snug">
                                        {{ item.title }}
                                    </h3>
                                    <p class="mt-1 text-[11.5px] leading-relaxed text-[#556987]">
                                        {{ item.description }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>

                <!-- ======================================================= -->
                <!-- 5. SECTION: ALUR KEMITRAAN (4 LANGKAH BERGABUNG)        -->
                <!-- ======================================================= -->
                <section class="mt-10 sm:mt-14" aria-labelledby="steps-title">
                    <div class="text-center max-w-2xl mx-auto">
                        <p class="text-[11px] font-bold uppercase tracking-[0.14em] text-[#2563eb]">
                            PROSES ONBOARDING
                        </p>
                        <h2 id="steps-title" class="mt-1 text-xl font-extrabold tracking-tight text-[#0f172a] sm:text-2xl">
                            Mulai Jadi Mitra dalam 4 Langkah
                        </h2>
                        <p class="mt-1 text-xs sm:text-sm text-[#556987]">
                            Pendaftaran mudah, verifikasi cepat, dan Anda siap menerima pesanan pertama.
                        </p>
                    </div>

                    <div class="mt-6 sm:mt-8 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3.5 sm:gap-4">
                        <div
                            v-for="step in steps"
                            :key="step.number"
                            class="relative flex flex-col justify-between rounded-2xl border border-[#e2eaf4] bg-white p-4.5 sm:p-5 shadow-[0_2px_8px_rgba(23,44,80,0.02)] transition-all duration-300 hover:border-[#93c5fd] hover:shadow-[0_8px_20px_rgba(37,99,235,0.06)] hover:-translate-y-0.5"
                        >
                            <div>
                                <div class="flex items-center justify-between">
                                    <span class="grid size-7 place-items-center rounded-full bg-[#2563eb] text-[11px] font-extrabold text-white shadow-xs">
                                        {{ step.number }}
                                    </span>
                                    <div class="flex size-8.5 items-center justify-center rounded-xl bg-[#edf5fe] text-[#2563eb]">
                                        <component :is="step.icon" class="size-4.5 stroke-[2.2]" />
                                    </div>
                                </div>
                                <h3 class="mt-3.5 text-xs sm:text-sm font-bold text-[#0f172a]">
                                    {{ step.title }}
                                </h3>
                                <p class="mt-1 text-[11.5px] leading-relaxed text-[#556987]">
                                    {{ step.description }}
                                </p>
                            </div>
                        </div>
                    </div>
                </section>

                <!-- ======================================================= -->
                <!-- 6. SECTION: FAQ (Pertanyaan yang Sering Diajukan)       -->
                <!-- ======================================================= -->
                <section class="mt-10 sm:mt-14" aria-labelledby="faq-partner-title">
                    <div class="mx-auto max-w-2xl text-center">
                        <p class="text-[11px] font-bold uppercase tracking-[0.14em] text-[#2563eb]">
                            BANTUAN & INFORMASI
                        </p>
                        <h2 id="faq-partner-title" class="mt-1 text-xl font-extrabold tracking-tight text-[#111c38] sm:text-2xl">
                            Pertanyaan yang Sering Diajukan
                        </h2>
                        <p class="mt-1 text-xs sm:text-sm text-[#556987]">
                            Punya pertanyaan seputar program kemitraan TapakLokal? Temukan jawabannya di bawah ini.
                        </p>
                    </div>

                    <div class="mx-auto mt-6 max-w-3xl space-y-2.5">
                        <div
                            v-for="(faq, fIndex) in faqs"
                            :key="fIndex"
                            class="overflow-hidden rounded-2xl border border-[#e2eaf4] bg-white shadow-xs transition-all duration-200"
                        >
                            <button
                                type="button"
                                class="flex w-full items-center justify-between p-4 sm:p-4.5 text-left font-bold text-[#111c38] transition hover:text-[#2563eb] cursor-pointer"
                                @click="toggleFaq(fIndex)"
                            >
                                <span class="text-xs sm:text-sm pr-4">{{ faq.q }}</span>
                                <span
                                    class="flex size-6.5 shrink-0 items-center justify-center rounded-full bg-slate-100 text-slate-500 transition-transform duration-200"
                                    :class="{ 'rotate-180 bg-[#edf5fe] text-[#2563eb]': faq.open }"
                                >
                                    <ChevronDown class="size-3.5 stroke-[2.5]" />
                                </span>
                            </button>
                            <div
                                v-if="faq.open"
                                class="px-4 sm:px-4.5 pb-4 text-xs sm:text-[13px] leading-relaxed text-[#556987] border-t border-slate-100 pt-2.5"
                            >
                                {{ faq.a }}
                            </div>
                        </div>
                    </div>
                </section>

                <!-- ======================================================= -->
                <!-- 7. BOTTOM CTA BANNER (Cinematic Editorial Style)        -->
                <!-- ======================================================= -->
                <section class="mt-10 sm:mt-14">
                    <div class="relative overflow-hidden rounded-2xl sm:rounded-3xl border border-slate-700/40 p-6 sm:p-9 md:p-12 text-center text-white shadow-[0_16px_40px_rgba(5,19,41,0.3)] select-none">
                        <!-- Background Landscape Photo -->
                        <img
                            src="https://images.unsplash.com/photo-1507525428034-b723cf961d3e?auto=format&fit=crop&w=1600&q=85"
                            alt="Kemitraan Wisata Nusantara"
                            class="absolute inset-0 size-full object-cover object-center scale-105 transition-transform duration-1000 ease-out hover:scale-100"
                        />

                        <!-- Overlays -->
                        <div class="absolute inset-0 bg-gradient-to-r from-[#05172e]/92 via-[#092548]/82 to-[#061b34]/90"></div>
                        <div class="absolute inset-0 bg-gradient-to-t from-[#041224] via-transparent to-[#041224]/70"></div>

                        <!-- Ambient Soft Lighting -->
                        <div class="pointer-events-none absolute -top-24 -right-24 size-96 rounded-full bg-sky-500/15 blur-3xl"></div>
                        <div class="pointer-events-none absolute -bottom-24 -left-24 size-96 rounded-full bg-blue-600/15 blur-3xl"></div>

                        <!-- Content -->
                        <div class="relative z-10 mx-auto max-w-2xl">
                            <h2 class="text-xl font-black tracking-tight text-white sm:text-2xl lg:text-3xl leading-tight">
                                Siap Mengembangkan Usaha Wisata & Oleh-Oleh Anda?
                            </h2>
                            <p class="mt-2 text-xs sm:text-sm leading-relaxed text-slate-200/90 font-normal max-w-xl mx-auto">
                                Daftarkan paket trip atau produk lokal Anda dalam hitungan menit dan mulai jangkau ribuan wisatawan nusantara bersama TapakLokal.
                            </p>

                            <!-- Action Buttons -->
                            <div class="mt-6 flex flex-wrap items-center justify-center gap-3">
                                <button
                                    type="button"
                                    class="group inline-flex items-center gap-2 rounded-full bg-white px-6 py-2.5 sm:py-3 text-xs sm:text-sm font-bold text-[#0c2340] shadow-[0_8px_20px_rgba(0,0,0,0.25)] transition-all duration-300 hover:bg-[#0088ff] hover:text-white hover:shadow-[0_12px_28px_rgba(0,136,255,0.4)] hover:-translate-y-0.5 active:scale-98 cursor-pointer"
                                    @click="openRegistrationModal('trip')"
                                >
                                    <span>Daftar Jadi Mitra Sekarang</span>
                                    <ArrowRight class="size-3.5 stroke-[2.5] transition-transform duration-300 group-hover:translate-x-1" />
                                </button>

                                <Link
                                    href="/bantuan"
                                    class="inline-flex items-center gap-2 rounded-full border border-white/25 bg-white/10 px-5 py-2.5 sm:py-3 text-xs sm:text-sm font-bold text-white backdrop-blur-md transition-all duration-300 hover:bg-white hover:text-[#0c2340] hover:border-white hover:shadow-[0_8px_20px_rgba(0,0,0,0.2)] hover:-translate-y-0.5 active:scale-98"
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


