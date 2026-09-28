<script setup>
import { Head, Link } from '@inertiajs/vue3';
import { computed, ref } from 'vue';
import {
    ArrowDown,
    ArrowRight,
    BadgeCheck,
    Banknote,
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
    Minus,
    PackageCheck,
    Plus,
    ShieldCheck,
    ShoppingBag,
    Sparkles,
    Star,
    Store,
    Timer,
    TrendingUp,
    Truck,
    Users,
    Utensils,
    Wallet,
    X,
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

// 4 Benefits (Matching Affiliate 2-Col Editorial Style)
const whyJoinBenefits = [
    {
        title: 'Paket All-in-One + Opsi Add-On Bebas',
        description: 'Jual paket trip lengkap standar homestay, atau sediakan pilihan upgrade villa & rumah warga lokal yang telah Anda kurasi.',
        icon: Compass,
    },
    {
        title: 'Smart Regional Food Logistics',
        description: 'Oleh-oleh basah (1-3 hari) otomatis terproteksi khusus pembeli sameday/next-day, makanan kering bebas kirim se-Indonesia.',
        icon: Truck,
    },
    {
        title: 'Sistem Booking & Manifest Otomatis',
        description: 'Semua data peserta trip, pesanan oleh-oleh, dan pilihan add-on tercatat rapi secara real-time di dashboard Anda.',
        icon: TrendingUp,
    },
    {
        title: 'Pencairan Dana 1x24 Jam Tanpa Potongan Tersembunyi',
        description: 'Dana langsung cair ke rekening bank Anda setelah trip selesai atau barang tiba di tujuan dengan aman.',
        icon: Wallet,
    },
];

// 4 Steps (Matching Affiliate 4-Card Process Layout with Circular Arrows)
const steps = [
    {
        number: 1,
        title: 'Pilih Kategori Kemitraan',
        description: 'Daftar sebagai Vendor Paket Trip Wisata (Open/Private) atau Produsen Oleh-Oleh Khas Daerah.',
        icon: Store,
    },
    {
        number: 2,
        title: 'Kurasi & Verifikasi Usaha',
        description: 'Kirimkan profil usaha, izin edar kuliner, atau portofolio rute wisata Anda untuk kurasi 1x24 jam.',
        icon: BadgeCheck,
    },
    {
        number: 3,
        title: 'Unggah Paket & Atur Add-On',
        description: 'Atur jadwal open trip, opsi upgrade villa privat, atau tentukan ketahanan produk oleh-oleh.',
        icon: Box,
    },
    {
        number: 4,
        title: 'Terima Pesanan & Cairkan Hasil',
        description: 'Terima booking dari ribuan wisatawan dan nikmati pencairan dana 1x24 jam langsung ke rekening.',
        icon: Banknote,
    },
];

// 3 Feature Showcase Cards (Matching Affiliate 3-Card Catalog Grid 1:1)
const featureCards = [
    {
        id: 1,
        type: 'trip',
        badge: 'Vendor Trip Wisata',
        location: 'Seluruh Destinasi Indonesia',
        title: 'Paket Open & Private Trip All-in-One',
        description: 'Paket wisata lengkap mencakup pemandu lokal (guide), transportasi, tiket destinasi, dan akomodasi homestay bawaan dalam satu paket siap jalan.',
        highlightLabel: 'Include Homestay & Guide',
        perkBadge: 'Trip All-in-One',
        buttonText: 'Daftar Vendor Trip',
        image: 'https://images.unsplash.com/photo-1544644181-1484b3fdfc62?auto=format&fit=crop&w=800&q=85',
    },
    {
        id: 2,
        type: 'trip',
        badge: 'Fitur Unggulan Trip',
        location: 'Fleksibilitas Upgrade Wisatawan',
        title: 'Fitur Add-On Villa & Rumah Warga',
        description: 'Wisatawan yang menginginkan privasi atau pengalaman otentik dapat memilih opsi upgrade ke Private Villa atau tinggal di Rumah Warga kurasi Anda.',
        highlightLabel: 'Extra Margin untuk Vendor',
        perkBadge: 'Add-On Fleksibel',
        buttonText: 'Pelajari Sistem Add-On',
        image: 'https://images.unsplash.com/photo-1580587771525-78b9dba3b914?auto=format&fit=crop&w=800&q=85',
    },
    {
        id: 3,
        type: 'souvenir',
        badge: 'Vendor Oleh-Oleh Daerah',
        location: 'Sentra UMKM & Kuliner Nusantara',
        title: 'Smart Regional Food & Souvenir',
        description: 'Sistem logistik cerdas memproteksi makanan basah (1-3 hari) agar hanya dibeli radius sameday/next-day, sementara makanan kering & kriya bisa dikirim se-Indonesia.',
        highlightLabel: 'Anti Basi & Resi Otomatis',
        perkBadge: 'Regional Protected',
        buttonText: 'Daftar Vendor Oleh-Oleh',
        image: 'https://images.unsplash.com/photo-1555396273-367ea4eb4db5?auto=format&fit=crop&w=800&q=85',
    },
];

// Interactive Revenue Potential Calculator
const calcType = ref('trip'); // 'trip' | 'souvenir'
const calcTripPrice = ref(1500000);
const calcTripBookings = ref(20);
const calcSouvenirPrice = ref(75000);
const calcSouvenirOrders = ref(80);

const formatCurrency = (amount) => {
    return new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: 'IDR',
        maximumFractionDigits: 0,
    }).format(amount);
};

const calculatedMonthlyRevenue = computed(() => {
    if (calcType.value === 'trip') {
        return calcTripPrice.value * calcTripBookings.value;
    }
    return calcSouvenirPrice.value * calcSouvenirOrders.value;
});

const changeTripPrice = (delta) => {
    const next = calcTripPrice.value + delta;
    if (next >= 250000 && next <= 25000000) calcTripPrice.value = next;
};

const changeTripBookings = (delta) => {
    const next = calcTripBookings.value + delta;
    if (next >= 1 && next <= 200) calcTripBookings.value = next;
};

const changeSouvenirPrice = (delta) => {
    const next = calcSouvenirPrice.value + delta;
    if (next >= 15000 && next <= 1000000) calcSouvenirPrice.value = next;
};

const changeSouvenirOrders = (delta) => {
    const next = calcSouvenirOrders.value + delta;
    if (next >= 5 && next <= 1000) calcSouvenirOrders.value = next;
};

// FAQ List (1:1 with Affiliate FAQ structure)
const faqs = ref([
    {
        q: 'Siapa saja yang bisa bergabung menjadi Mitra Vendor di TapakLokal?',
        a: 'Kemitraan dibuka untuk dua kelompok utama: (1) Operator Trip Wisata yang menyelenggarakan Open Trip maupun Private Trip lengkap dengan akomodasi dan pemandu, dan (2) Produsen/UMKM Oleh-Oleh Khas Daerah yang memproduksi makanan basah, makanan kering, kopi, maupun kerajinan kriya khas daerah di Indonesia.',
        open: true,
    },
    {
        q: 'Bagaimana fitur Add-On Akomodasi (Villa / Rumah Warga) bekerja untuk Vendor Trip?',
        a: 'Paket trip Anda tetap dikemas all-in-one dengan homestay bawaan standar. Namun, Anda dapat menyertakan opsi Add-On berupa Upgrade ke Private Villa atau tinggal di Rumah Warga Lokal yang sudah Anda kurasi. Ketika wisatawan memilih opsi add-on saat checkout, selisih biaya tersebut langsung masuk sebagai pendapatan tambahan Anda dan otomatis tercatat pada manifest peserta.',
        open: false,
    },
    {
        q: 'Bagaimana sistem memastikan oleh-oleh makanan basah tetap segar sampai ke pembeli?',
        a: 'Sistem TapakLokal menerapkan Smart Regional Filter. Produk kuliner dengan ketahanan 1-3 hari hanya akan ditampilkan dan dapat dibeli oleh pelanggan dalam jangkauan ekspedisi Sameday atau Next-Day dari lokasi produksi Anda. Sedangkan makanan kering dan kriya dapat dipesan oleh wisatawan di seluruh Indonesia.',
        open: false,
    },
    {
        q: 'Kapan dan bagaimana pencairan dana ditransfer ke rekening vendor?',
        a: 'Untuk Vendor Trip, dana otomatis dicairkan ke rekening bank Anda 1x24 jam setelah trip selesai terlaksana tanpa kendala. Untuk Vendor Oleh-Oleh, dana cair segera setelah paket terkonfirmasi diterima dengan baik oleh pembeli berdasarkan pelacakan resi ekspedisi.',
        open: false,
    },
    {
        q: 'Apakah ada biaya pendaftaran atau biaya langganan bulanan?',
        a: 'Pendaftaran kemitraan di TapakLokal 100% Gratis selamanya tanpa ada biaya langganan bulanan maupun kuota target penjualan minimum. TapakLokal hanya mengenakan platform fee transparan berbasis persentase bagi hasil dari setiap transaksi yang berhasil.',
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
                <!-- 1. HERO SECTION (APPROVED - DO NOT MODIFY)               -->
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

                <!-- ========================================================== -->
                <!-- 2. SECTION: KENAPA JADI MITRA TAPAK LOKAL? (2-COL POLAROID)-->
                <!-- ========================================================== -->
                <section class="mt-14 sm:mt-20" aria-labelledby="why-partner-title">
                    <div class="grid grid-cols-1 items-center gap-10 lg:grid-cols-12 lg:gap-12 xl:gap-16">
                        <!-- Left Side: Layered Travel Photos (With Paper Tape / Tempelan Kertas) -->
                        <div class="lg:col-span-6 flex items-center justify-center">
                            <div class="relative w-full max-w-[460px] py-6 sm:py-8 select-none">
                                <!-- Main Center Photo (Local Tour Boat / Expedition) -->
                                <div class="relative z-10 mx-auto w-[82%] sm:w-[85%] transition-transform duration-300 hover:scale-[1.02]">
                                    <div class="absolute -top-2.5 left-1/2 -translate-x-1/2 z-30 h-4.5 sm:h-5 w-14 sm:w-16 -rotate-1 rounded-xs bg-[#ebe3d0]/90 border-t border-b border-[#cfc3ad] shadow-xs backdrop-blur-xs pointer-events-none"></div>
                                    <div class="overflow-hidden rounded-[24px] sm:rounded-[32px] border-4 border-white bg-white shadow-[0_16px_40px_rgba(15,44,92,0.12)]">
                                        <img
                                            src="https://images.unsplash.com/photo-1518548419970-58e3b4079ab2?auto=format&fit=crop&w=800&q=85"
                                            alt="Kapal Wisata & Tour Operator Lokal"
                                            class="aspect-[4/3] w-full object-cover"
                                        />
                                    </div>
                                </div>

                                <!-- Floating Top-Left Photo (Private Villa & Homestay Kurasi) -->
                                <div class="absolute -top-1 -left-1 sm:-left-3 z-20 w-32 sm:w-40 -rotate-8 transition-transform duration-300 hover:rotate-0 hover:scale-105">
                                    <div class="absolute -top-2.5 left-1/2 -translate-x-1/2 z-30 h-4.5 sm:h-5 w-12 sm:w-14 -rotate-3 rounded-xs bg-[#ebe3d0]/95 border-t border-b border-[#cfc3ad] shadow-xs backdrop-blur-xs pointer-events-none"></div>
                                    <div class="overflow-hidden rounded-2xl border-4 border-white bg-white shadow-[0_12px_28px_rgba(15,44,92,0.16)]">
                                        <img
                                            src="https://images.unsplash.com/photo-1580587771525-78b9dba3b914?auto=format&fit=crop&w=400&q=80"
                                            alt="Villa & Homestay Lokal"
                                            class="aspect-square w-full object-cover"
                                        />
                                    </div>
                                </div>

                                <!-- Floating Bottom-Right Photo (Kuliner & Oleh-Oleh Daerah) -->
                                <div class="absolute -bottom-2 -right-1 sm:-right-3 z-20 w-32 sm:w-40 rotate-8 transition-transform duration-300 hover:rotate-0 hover:scale-105">
                                    <div class="absolute -top-2.5 left-1/2 -translate-x-1/2 z-30 h-4.5 sm:h-5 w-12 sm:w-14 rotate-3 rounded-xs bg-[#ebe3d0]/95 border-t border-b border-[#cfc3ad] shadow-xs backdrop-blur-xs pointer-events-none"></div>
                                    <div class="overflow-hidden rounded-2xl border-4 border-white bg-white shadow-[0_12px_28px_rgba(15,44,92,0.16)]">
                                        <img
                                            src="https://images.unsplash.com/photo-1555396273-367ea4eb4db5?auto=format&fit=crop&w=400&q=80"
                                            alt="Oleh-oleh Tradisional Daerah"
                                            class="aspect-square w-full object-cover"
                                        />
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Right Side: Title & 4 Benefit Items -->
                        <div class="lg:col-span-6 space-y-6">
                            <div>
                                <h2 id="why-partner-title" class="text-2xl sm:text-3xl lg:text-[36px] font-extrabold tracking-tight text-[#0f172a] leading-tight">
                                    Kenapa Bermitra dengan<br />
                                    Tapak Lokal?
                                </h2>
                            </div>

                            <!-- 4 Compact Benefit Rows (1:1 with Affiliate style) -->
                            <div class="space-y-4 pt-1 sm:space-y-5">
                                <div
                                    v-for="(item, idx) in whyJoinBenefits"
                                    :key="idx"
                                    class="group flex items-start gap-4 rounded-2xl p-1 transition-all duration-200"
                                >
                                    <div class="flex size-11 sm:size-12 shrink-0 items-center justify-center rounded-2xl bg-[#edf5fe] text-[#2563eb] transition-transform duration-200 group-hover:scale-105 group-hover:bg-[#2563eb] group-hover:text-white">
                                        <component :is="item.icon" class="size-5.5 sm:size-6 stroke-[2.2]" />
                                    </div>
                                    <div class="pt-0.5">
                                        <h3 class="text-sm sm:text-base font-bold text-[#0f172a] group-hover:text-[#2563eb] transition-colors">
                                            {{ item.title }}
                                        </h3>
                                        <p class="mt-1 text-xs sm:text-[13.5px] leading-relaxed text-[#556987]">
                                            {{ item.description }}
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>

                <!-- ========================================================== -->
                <!-- 3. SECTION: MULAI JADI MITRA DALAM 4 LANGKAH               -->
                <!-- ========================================================== -->
                <section class="mt-14 sm:mt-20" aria-labelledby="steps-partner-title">
                    <div class="relative overflow-hidden rounded-[28px] border border-[#dce8f8] bg-[#f8fbff] p-6 sm:rounded-[36px] sm:p-9 md:p-11 lg:rounded-[40px] lg:p-12 xl:p-14 shadow-[0_4px_24px_rgba(37,99,235,0.03)]">
                        <div class="mx-auto max-w-2xl text-center">
                            <p class="text-xs font-bold uppercase tracking-[0.14em] text-[#2563eb]">
                                CARA BERGABUNG
                            </p>
                            <h2 id="steps-partner-title" class="mt-2 text-2xl font-extrabold tracking-tight text-[#111c38] sm:text-3xl lg:text-4xl">
                                Mulai Jadi Mitra dalam <span class="text-[#2563eb]">4 Langkah</span>
                            </h2>
                            <p class="mt-2.5 text-xs leading-relaxed text-[#556987] sm:text-sm">
                                Proses pendaftaran cepat, kurasi mudah, dan langsung terhubung ke ribuan wisatawan.
                            </p>
                        </div>

                        <!-- Desktop Layout (lg:flex with standalone circular arrows in between) -->
                        <div class="mt-8 sm:mt-12 hidden lg:flex items-stretch justify-between gap-2 xl:gap-3">
                            <template v-for="(step, index) in steps" :key="step.number">
                                <!-- Card -->
                                <div
                                    class="group relative flex flex-1 flex-col items-center justify-between rounded-[24px] border border-[#e2eaf4] bg-white p-5 xl:p-6 text-center shadow-[0_2px_12px_rgba(23,44,80,0.03)] min-h-[250px] transition-all duration-300 hover:border-[#93c5fd] hover:shadow-[0_12px_28px_rgba(37,99,235,0.08)] hover:-translate-y-1"
                                >
                                    <span class="absolute left-4 top-4 grid size-7 place-items-center rounded-full bg-[#2563eb] text-xs font-extrabold text-white shadow-xs">
                                        {{ step.number }}
                                    </span>
                                    <div class="mt-2 grid size-14 xl:size-16 place-items-center rounded-2xl bg-[#edf5fe] text-[#2563eb] transition-transform duration-300 group-hover:scale-105">
                                        <component :is="step.icon" class="size-7 xl:size-8 stroke-[2.2]" />
                                    </div>
                                    <div class="mt-4 flex flex-col items-center flex-1 justify-center">
                                        <h3 class="text-sm xl:text-base font-bold text-[#111c38] group-hover:text-[#2563eb] transition-colors">
                                            {{ step.title }}
                                        </h3>
                                        <p class="mt-1.5 text-xs leading-relaxed text-[#556987]">
                                            {{ step.description }}
                                        </p>
                                    </div>
                                </div>

                                <!-- Standalone Arrow Between Cards -->
                                <div
                                    v-if="index < steps.length - 1"
                                    class="flex items-center justify-center shrink-0 px-1 text-[#2563eb]"
                                >
                                    <div class="flex size-8 xl:size-9 items-center justify-center rounded-full bg-[#edf5fe] border border-[#d8eafb] text-[#2563eb] shadow-xs">
                                        <ArrowRight class="size-4 xl:size-4.5 stroke-[2.5]" />
                                    </div>
                                </div>
                            </template>
                        </div>

                        <!-- Mobile & Tablet Layout (< lg) -->
                        <div class="mt-8 flex flex-col sm:grid sm:grid-cols-2 gap-4 sm:gap-6 lg:hidden">
                            <template v-for="(step, index) in steps" :key="step.number">
                                <div class="flex flex-col">
                                    <div
                                        class="group relative flex flex-col items-center justify-between rounded-[20px] sm:rounded-[24px] border border-[#e2eaf4] bg-white p-6 text-center shadow-[0_2px_12px_rgba(23,44,80,0.03)] h-full min-h-[230px] transition-all duration-300 hover:border-[#93c5fd]"
                                    >
                                        <span class="absolute left-4 top-4 grid size-7 place-items-center rounded-full bg-[#2563eb] text-xs font-extrabold text-white shadow-xs">
                                            {{ step.number }}
                                        </span>
                                        <div class="mt-2 grid size-14 place-items-center rounded-2xl bg-[#edf5fe] text-[#2563eb]">
                                            <component :is="step.icon" class="size-7 stroke-[2.2]" />
                                        </div>
                                        <div class="mt-4 flex flex-col items-center flex-1 justify-center">
                                            <h3 class="text-base font-bold text-[#111c38]">
                                                {{ step.title }}
                                            </h3>
                                            <p class="mt-2 text-xs sm:text-[13px] leading-relaxed text-[#556987]">
                                                {{ step.description }}
                                            </p>
                                        </div>
                                    </div>

                                    <!-- Arrow Down on Mobile -->
                                    <div
                                        v-if="index < steps.length - 1"
                                        class="flex sm:hidden justify-center py-2 text-[#2563eb]"
                                    >
                                        <div class="flex size-8 items-center justify-center rounded-full bg-[#edf5fe] border border-[#d8eafb] shadow-xs">
                                            <ArrowDown class="size-4 stroke-[2.5]" />
                                        </div>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </div>
                </section>

                <!-- ========================================================== -->
                <!-- 4. SECTION: FITUR & SPESIALISASI KEMITRAAN (3-CARD GRID)   -->
                <!-- ========================================================== -->
                <section class="mt-14 sm:mt-20" aria-labelledby="catalog-partner-title">
                    <div class="flex flex-col justify-between gap-3 sm:flex-row sm:items-end">
                        <div>
                            <p class="text-xs font-bold uppercase tracking-[0.14em] text-[#2563eb]">
                                SPESIALISASI KEMITRAAN
                            </p>
                            <h2 id="catalog-partner-title" class="mt-2 text-2xl font-extrabold tracking-tight text-[#111c38] sm:text-3xl">
                                2 Jalur Kemitraan Unggulan TapakLokal
                            </h2>
                            <p class="mt-1 text-xs leading-relaxed text-[#556987] sm:text-sm">
                                Dirancang khusus sesuai spesialisasi bisnis Anda dengan sistem dan fitur terlengkap di Indonesia.
                            </p>
                        </div>

                        <button
                            type="button"
                            class="inline-flex items-center gap-1.5 text-xs sm:text-sm font-bold text-[#2563eb] transition-colors hover:text-[#1d4ed8] cursor-pointer"
                            @click="openRegistrationModal('trip')"
                        >
                            <span>Daftar Kemitraan Sekarang</span>
                            <ArrowRight class="size-4" />
                        </button>
                    </div>

                    <!-- 3 Showcase Cards (1:1 Matching Affiliate Trip Cards Grid) -->
                    <div class="mt-8 grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
                        <div
                            v-for="card in featureCards"
                            :key="card.id"
                            class="group flex flex-col overflow-hidden rounded-[20px] sm:rounded-[24px] border border-[#e2eaf4] bg-white shadow-[0_2px_12px_rgba(23,44,80,0.03)] transition-all duration-300 hover:-translate-y-1 hover:border-[#93c5fd] hover:shadow-[0_16px_36px_rgba(37,99,235,0.09)]"
                        >
                            <!-- Card Image -->
                            <div class="relative aspect-[16/10] w-full overflow-hidden bg-slate-100">
                                <img
                                    :src="card.image"
                                    :alt="card.title"
                                    class="size-full object-cover object-center transition-transform duration-500 group-hover:scale-105"
                                />
                                <div class="absolute top-3 left-3">
                                    <span class="rounded-lg bg-[#2563eb] px-2.5 py-1 text-[11px] font-bold text-white shadow-xs">
                                        {{ card.badge }}
                                    </span>
                                </div>
                            </div>

                            <!-- Card Body Info -->
                            <div class="flex flex-1 flex-col p-5 sm:p-6">
                                <!-- Location / Subtitle -->
                                <div class="flex items-center gap-1.5 text-xs font-semibold text-slate-500">
                                    <MapPin class="size-3.5 text-[#2563eb] shrink-0" />
                                    <span>{{ card.location }}</span>
                                </div>

                                <!-- Title -->
                                <h3 class="mt-2 text-base sm:text-lg font-bold text-[#111c38] line-clamp-1 group-hover:text-[#2563eb] transition-colors">
                                    {{ card.title }}
                                </h3>

                                <!-- Description -->
                                <p class="mt-1.5 text-xs sm:text-[13px] leading-relaxed text-[#556987] line-clamp-3">
                                    {{ card.description }}
                                </p>

                                <!-- Perk & Highlight Bar -->
                                <div class="mt-auto pt-4 flex items-center justify-between border-t border-slate-100">
                                    <div class="text-xs font-bold text-slate-700 truncate pr-2">
                                        {{ card.highlightLabel }}
                                    </div>

                                    <div class="shrink-0 rounded-lg border border-emerald-200 bg-emerald-50 px-2.5 py-1 text-xs font-bold text-emerald-600">
                                        {{ card.perkBadge }}
                                    </div>
                                </div>

                                <!-- Quick Action Button -->
                                <div class="mt-4 pt-1">
                                    <button
                                        type="button"
                                        class="w-full inline-flex items-center justify-center gap-2 rounded-xl bg-[#edf5fe] hover:bg-[#2563eb] text-[#2563eb] hover:text-white py-2.5 text-xs font-bold transition-all duration-200 cursor-pointer active:scale-98"
                                        @click="openRegistrationModal(card.type)"
                                    >
                                        <span>{{ card.buttonText }}</span>
                                        <ArrowRight class="size-3.5" />
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>

                <!-- ========================================================== -->
                <!-- 5. INTERACTIVE CALCULATOR (Potensi Omzet Kemitraan)        -->
                <!-- ========================================================== -->
                <section class="mt-14 sm:mt-20" aria-labelledby="calc-partner-title">
                    <div class="relative overflow-hidden rounded-[28px] border border-[#dce8f8] bg-[#f8fbff] p-6 sm:rounded-[36px] sm:p-9 md:p-11 lg:rounded-[40px] lg:p-12 xl:p-14 shadow-[0_4px_24px_rgba(37,99,235,0.03)]">
                        <div class="grid grid-cols-1 items-center gap-10 lg:grid-cols-12 lg:gap-8 xl:gap-12">
                            <!-- Left Side: Controls & Steppers -->
                            <div class="lg:col-span-6 xl:col-span-6">
                                <h2 id="calc-partner-title" class="mt-2 text-2xl sm:text-3xl lg:text-[34px] font-extrabold tracking-tight text-[#0f172a] leading-tight">
                                    Hitung Potensi Omzet Usaha Anda
                                </h2>
                                <p class="mt-2 text-xs sm:text-sm text-[#556987] leading-relaxed max-w-lg">
                                    Simulasikan estimasi perputaran omzet bulanan dari paket trip wisata atau penjualan produk oleh-oleh khas daerah Anda.
                                </p>

                                <!-- Track Switcher (Trip vs Souvenir) -->
                                <div class="mt-5 inline-flex rounded-xl border border-slate-200 bg-white p-1 shadow-2xs">
                                    <button
                                        type="button"
                                        class="rounded-lg px-4 py-2 text-xs font-bold transition cursor-pointer"
                                        :class="calcType === 'trip' ? 'bg-[#2563eb] text-white shadow-xs' : 'text-slate-600 hover:text-slate-900'"
                                        @click="calcType = 'trip'"
                                    >
                                        Vendor Trip Wisata
                                    </button>
                                    <button
                                        type="button"
                                        class="rounded-lg px-4 py-2 text-xs font-bold transition cursor-pointer"
                                        :class="calcType === 'souvenir' ? 'bg-[#2563eb] text-white shadow-xs' : 'text-slate-600 hover:text-slate-900'"
                                        @click="calcType = 'souvenir'"
                                    >
                                        Vendor Oleh-Oleh Daerah
                                    </button>
                                </div>

                                <!-- Steppers for Trip -->
                                <template v-if="calcType === 'trip'">
                                    <!-- Stepper 1: Rata-rata harga paket trip -->
                                    <div class="mt-5 flex flex-col sm:flex-row sm:items-center justify-between gap-2.5 py-1">
                                        <span class="text-xs sm:text-sm font-semibold text-slate-700">
                                            Rata-rata harga paket trip
                                        </span>
                                        <div class="flex items-center justify-between gap-3 rounded-2xl border border-slate-200 bg-white px-3 py-2 shadow-xs min-w-[210px] sm:min-w-[230px]">
                                            <button
                                                type="button"
                                                aria-label="Kurangi harga paket trip"
                                                class="flex size-7 items-center justify-center rounded-lg text-slate-500 hover:bg-slate-100 hover:text-slate-800 active:scale-95 transition cursor-pointer disabled:opacity-30 disabled:cursor-not-allowed"
                                                :disabled="calcTripPrice <= 500000"
                                                @click="changeTripPrice(-250000)"
                                            >
                                                <Minus class="size-4 stroke-[2.5]" />
                                            </button>
                                            <span class="text-xs sm:text-sm font-extrabold text-slate-900">
                                                {{ formatCurrency(calcTripPrice) }}
                                            </span>
                                            <button
                                                type="button"
                                                aria-label="Tambah harga paket trip"
                                                class="flex size-7 items-center justify-center rounded-lg text-slate-500 hover:bg-slate-100 hover:text-slate-800 active:scale-95 transition cursor-pointer disabled:opacity-30 disabled:cursor-not-allowed"
                                                :disabled="calcTripPrice >= 25000000"
                                                @click="changeTripPrice(250000)"
                                            >
                                                <Plus class="size-4 stroke-[2.5]" />
                                            </button>
                                        </div>
                                    </div>

                                    <!-- Stepper 2: Peserta / Booking per bulan -->
                                    <div class="mt-2.5 flex flex-col sm:flex-row sm:items-center justify-between gap-2.5 py-1">
                                        <span class="text-xs sm:text-sm font-semibold text-slate-700">
                                            Jumlah peserta / booking per bulan
                                        </span>
                                        <div class="flex items-center justify-between gap-3 rounded-2xl border border-slate-200 bg-white px-3 py-2 shadow-xs min-w-[210px] sm:min-w-[230px]">
                                            <button
                                                type="button"
                                                aria-label="Kurangi jumlah peserta"
                                                class="flex size-7 items-center justify-center rounded-lg text-slate-500 hover:bg-slate-100 hover:text-slate-800 active:scale-95 transition cursor-pointer disabled:opacity-30 disabled:cursor-not-allowed"
                                                :disabled="calcTripBookings <= 1"
                                                @click="changeTripBookings(-1)"
                                            >
                                                <Minus class="size-4 stroke-[2.5]" />
                                            </button>
                                            <span class="text-xs sm:text-sm font-extrabold text-slate-900">
                                                {{ calcTripBookings }} orang
                                            </span>
                                            <button
                                                type="button"
                                                aria-label="Tambah jumlah peserta"
                                                class="flex size-7 items-center justify-center rounded-lg text-slate-500 hover:bg-slate-100 hover:text-slate-800 active:scale-95 transition cursor-pointer disabled:opacity-30 disabled:cursor-not-allowed"
                                                :disabled="calcTripBookings >= 200"
                                                @click="changeTripBookings(1)"
                                            >
                                                <Plus class="size-4 stroke-[2.5]" />
                                            </button>
                                        </div>
                                    </div>
                                </template>

                                <!-- Steppers for Souvenir -->
                                <template v-else>
                                    <!-- Stepper 1: Rata-rata harga oleh-oleh -->
                                    <div class="mt-5 flex flex-col sm:flex-row sm:items-center justify-between gap-2.5 py-1">
                                        <span class="text-xs sm:text-sm font-semibold text-slate-700">
                                            Rata-rata harga per item / box
                                        </span>
                                        <div class="flex items-center justify-between gap-3 rounded-2xl border border-slate-200 bg-white px-3 py-2 shadow-xs min-w-[210px] sm:min-w-[230px]">
                                            <button
                                                type="button"
                                                aria-label="Kurangi harga oleh-oleh"
                                                class="flex size-7 items-center justify-center rounded-lg text-slate-500 hover:bg-slate-100 hover:text-slate-800 active:scale-95 transition cursor-pointer disabled:opacity-30 disabled:cursor-not-allowed"
                                                :disabled="calcSouvenirPrice <= 25000"
                                                @click="changeSouvenirPrice(-10000)"
                                            >
                                                <Minus class="size-4 stroke-[2.5]" />
                                            </button>
                                            <span class="text-xs sm:text-sm font-extrabold text-slate-900">
                                                {{ formatCurrency(calcSouvenirPrice) }}
                                            </span>
                                            <button
                                                type="button"
                                                aria-label="Tambah harga oleh-oleh"
                                                class="flex size-7 items-center justify-center rounded-lg text-slate-500 hover:bg-slate-100 hover:text-slate-800 active:scale-95 transition cursor-pointer disabled:opacity-30 disabled:cursor-not-allowed"
                                                :disabled="calcSouvenirPrice >= 500000"
                                                @click="changeSouvenirPrice(10000)"
                                            >
                                                <Plus class="size-4 stroke-[2.5]" />
                                            </button>
                                        </div>
                                    </div>

                                    <!-- Stepper 2: Jumlah pesanan oleh-oleh per bulan -->
                                    <div class="mt-2.5 flex flex-col sm:flex-row sm:items-center justify-between gap-2.5 py-1">
                                        <span class="text-xs sm:text-sm font-semibold text-slate-700">
                                            Jumlah pesanan per bulan
                                        </span>
                                        <div class="flex items-center justify-between gap-3 rounded-2xl border border-slate-200 bg-white px-3 py-2 shadow-xs min-w-[210px] sm:min-w-[230px]">
                                            <button
                                                type="button"
                                                aria-label="Kurangi jumlah pesanan"
                                                class="flex size-7 items-center justify-center rounded-lg text-slate-500 hover:bg-slate-100 hover:text-slate-800 active:scale-95 transition cursor-pointer disabled:opacity-30 disabled:cursor-not-allowed"
                                                :disabled="calcSouvenirOrders <= 10"
                                                @click="changeSouvenirOrders(-10)"
                                            >
                                                <Minus class="size-4 stroke-[2.5]" />
                                            </button>
                                            <span class="text-xs sm:text-sm font-extrabold text-slate-900">
                                                {{ calcSouvenirOrders }} pesanan
                                            </span>
                                            <button
                                                type="button"
                                                aria-label="Tambah jumlah pesanan"
                                                class="flex size-7 items-center justify-center rounded-lg text-slate-500 hover:bg-slate-100 hover:text-slate-800 active:scale-95 transition cursor-pointer disabled:opacity-30 disabled:cursor-not-allowed"
                                                :disabled="calcSouvenirOrders >= 1000"
                                                @click="changeSouvenirOrders(10)"
                                            >
                                                <Plus class="size-4 stroke-[2.5]" />
                                            </button>
                                        </div>
                                    </div>
                                </template>
                            </div>

                            <!-- Right Side: Result Card + Tilted Polaroid Photo with Washi Tape -->
                            <div class="lg:col-span-6 xl:col-span-6 flex items-center justify-center lg:justify-end">
                                <div class="relative w-full max-w-[500px] rounded-[28px] border border-[#d8eafb] bg-white/95 p-6 sm:p-7 shadow-[0_12px_36px_rgba(37,99,235,0.06)]">
                                    <div class="flex flex-col sm:flex-row items-center sm:items-center justify-between gap-5 sm:gap-6">
                                        <!-- Result Content -->
                                        <div class="flex-1 text-center sm:text-left pt-2 sm:pt-4">
                                            <p class="text-xs sm:text-sm font-bold text-slate-700">
                                                Estimasi Omzet Penjualan
                                            </p>
                                            <div class="mt-1 text-2xl sm:text-3xl lg:text-[32px] font-black tracking-tight text-[#2563eb]">
                                                {{ formatCurrency(calculatedMonthlyRevenue) }}
                                            </div>
                                            <p class="mt-0.5 text-xs font-semibold text-slate-500">
                                                per bulan
                                            </p>

                                            <p class="mt-4 text-[11px] leading-relaxed text-slate-400 max-w-[210px] mx-auto sm:mx-0">
                                                *Perhitungan adalah simulasi potensi omzet. Pencairan dana otomatis masuk ke rekening setelah trip/pesanan selesai.
                                            </p>

                                            <div class="mt-5">
                                                <button
                                                    type="button"
                                                    class="inline-flex items-center justify-center gap-2 rounded-full bg-[#2563eb] hover:bg-[#1d4ed8] px-5 sm:px-6 py-3 text-xs sm:text-sm font-bold text-white shadow-[0_8px_20px_rgba(37,99,235,0.25)] transition-all duration-200 hover:-translate-y-0.5 active:scale-95 text-center cursor-pointer"
                                                    @click="openRegistrationModal(calcType)"
                                                >
                                                    <span>Daftar Kemitraan</span>
                                                    <ArrowRight class="size-4 stroke-[2.5]" />
                                                </button>
                                            </div>
                                        </div>

                                        <!-- Tilted Photo with Washi Tape -->
                                        <div class="relative shrink-0 select-none pt-6 pb-2 sm:pt-7 sm:pb-3">
                                            <div class="hidden sm:block absolute top-1.5 -right-2 z-20 text-right">
                                                <span class="inline-block text-[10px] font-bold text-slate-600 italic rotate-6 bg-blue-50 border border-blue-200/80 px-2 py-0.5 rounded-md shadow-2xs">
                                                    Bisnis Lokal Naik Kelas
                                                </span>
                                            </div>

                                            <div class="relative w-36 sm:w-40 lg:w-44 rotate-6 rounded-2xl border-4 border-white bg-white p-1.5 pb-3 shadow-[0_12px_28px_rgba(15,44,92,0.14)] transition-transform duration-300 hover:rotate-3 hover:scale-105">
                                                <div class="absolute -top-2.5 left-1/2 -translate-x-1/2 w-12 h-5 bg-[#e2d9c8]/90 border-t border-b border-[#cfc3ad] -rotate-2 rounded-xs shadow-xs z-10"></div>
                                                <img
                                                    src="https://images.unsplash.com/photo-1544644181-1484b3fdfc62?auto=format&fit=crop&w=400&q=80"
                                                    alt="Keindahan Alam Nusantara"
                                                    class="aspect-[3/4] w-full rounded-xl object-cover"
                                                />
                                                <p class="mt-2 text-center text-[9px] sm:text-[10px] font-bold text-slate-600 leading-tight">
                                                    Mitra Resmi Nusantara
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>

                <!-- ========================================================== -->
                <!-- 6. SECTION: FAQ (Pertanyaan yang Sering Diajukan)          -->
                <!-- ========================================================== -->
                <section class="mt-14 sm:mt-20" aria-labelledby="faq-partner-title">
                    <div class="mx-auto max-w-2xl text-center">
                        <p class="text-xs font-bold uppercase tracking-[0.14em] text-[#2563eb]">
                            BANTUAN & INFORMASI
                        </p>
                        <h2 id="faq-partner-title" class="mt-2 text-2xl font-extrabold tracking-tight text-[#111c38] sm:text-3xl">
                            Pertanyaan yang Sering Diajukan
                        </h2>
                        <p class="mt-2 text-xs sm:text-sm text-[#556987]">
                            Punya pertanyaan seputar program kemitraan TapakLokal? Temukan jawabannya di bawah ini.
                        </p>
                    </div>

                    <div class="mx-auto mt-8 max-w-3xl space-y-3">
                        <div
                            v-for="(faq, fIndex) in faqs"
                            :key="fIndex"
                            class="overflow-hidden rounded-[20px] border border-[#e2eaf4] bg-white shadow-xs transition-all duration-200"
                        >
                            <button
                                type="button"
                                class="flex w-full items-center justify-between p-5 text-left font-bold text-[#111c38] transition hover:text-[#2563eb] cursor-pointer"
                                @click="toggleFaq(fIndex)"
                            >
                                <span class="text-xs sm:text-sm pr-4">{{ faq.q }}</span>
                                <span
                                    class="flex size-7 shrink-0 items-center justify-center rounded-full bg-slate-100 text-slate-500 transition-transform duration-200"
                                    :class="{ 'rotate-180 bg-[#edf5fe] text-[#2563eb]': faq.open }"
                                >
                                    <svg class="size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                                        <polyline points="6 9 12 15 18 9"></polyline>
                                    </svg>
                                </span>
                            </button>
                            <div
                                v-if="faq.open"
                                class="px-5 pb-5 text-xs sm:text-sm leading-relaxed text-[#556987] border-t border-slate-100 pt-3"
                            >
                                {{ faq.a }}
                            </div>
                        </div>
                    </div>
                </section>

                <!-- ========================================================== -->
                <!-- 7. BOTTOM CTA BANNER (Cinematic Editorial Style)           -->
                <!-- ========================================================== -->
                <section class="mt-14 sm:mt-20">
                    <div class="relative overflow-hidden rounded-[28px] sm:rounded-[36px] lg:rounded-[40px] border border-slate-700/40 p-8 sm:p-12 md:p-16 text-center text-white shadow-[0_20px_50px_rgba(5,19,41,0.35)] select-none">
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
                            <h2 class="text-2xl font-black tracking-tight text-white sm:text-3xl lg:text-4xl leading-tight">
                                Siap Mengembangkan Usaha Wisata & Oleh-Oleh Anda?
                            </h2>
                            <p class="mt-3.5 text-xs sm:text-sm md:text-[15px] leading-relaxed text-slate-200/90 font-normal max-w-xl mx-auto">
                                Daftarkan paket trip atau produk lokal Anda dalam hitungan menit dan mulai jangkau ribuan wisatawan nusantara bersama TapakLokal.
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

