<script setup>
import { Head, Link } from '@inertiajs/vue3';
import { computed, ref } from 'vue';
import {
    ArrowDown,
    ArrowRight,
    BadgePercent,
    Banknote,
    Calculator,
    Check,
    Clock,
    Compass,
    Copy,
    CreditCard,
    ExternalLink,
    HelpCircle,
    Link2,
    MapPin,
    Minus,
    Mountain,
    Plus,
    Share2,
    ShieldCheck,
    Sparkles,
    TrendingUp,
    UserPlus,
    Users,
    Wallet,
} from 'lucide-vue-next';
import MainNavigation from '../Components/Shared/MainNavigation.vue';
import MainFooter from '../Components/Shared/MainFooter.vue';
import AuthModal from '../Components/Shared/AuthModal.vue';

// Toast Notification State
const toastMessage = ref('');
const showToast = ref(false);
let toastTimeout = null;

const triggerToast = (msg) => {
    toastMessage.value = msg;
    showToast.value = true;
    if (toastTimeout) clearTimeout(toastTimeout);
    toastTimeout = setTimeout(() => {
        showToast.value = false;
    }, 2800);
};

// Traveloka 1:1 Login / Auth Modal State
const isAuthModalOpen = ref(false);
const authModalTitle = ref("We've got a deal you can't resist!");
const authModalSubtitle = ref("Yuk masuk untuk mulai jadi affiliate, nikmati komisi hingga 20%, dan cairkan saldo dengan mudah!");
const pendingAction = ref(null);

const openAffiliateAuthModal = (type = 'join', data = null) => {
    pendingAction.value = { type, data };
    if (type === 'copy_promo') {
        authModalTitle.value = "Yuk masuk untuk salin link affiliate!";
    } else if (type === 'copy_referral') {
        authModalTitle.value = "Masuk untuk bagikan link referral!";
    } else {
        authModalTitle.value = "We've got a deal you can't resist!";
    }
    isAuthModalOpen.value = true;
};

const handleLoginSuccess = () => {
    if (pendingAction.value?.type === 'copy_promo' && pendingAction.value.data) {
        copyPromoLink(pendingAction.value.data);
    } else if (pendingAction.value?.type === 'copy_referral') {
        triggerToast('Link referral siap dibagikan!');
    } else {
        triggerToast('Selamat bergabung di Affiliate TapakLokal!');
    }
};

const handleGuestContinue = () => {
    if (pendingAction.value?.type === 'copy_promo' && pendingAction.value.data) {
        copyPromoLink(pendingAction.value.data);
    } else if (pendingAction.value?.type === 'copy_referral') {
        triggerToast('Link referral siap dibagikan!');
    } else {
        triggerToast('Melanjutkan sebagai penjelajah tamu.');
    }
};

// Copy link simulation
const copiedTripId = ref(null);
const copyPromoLink = (trip) => {
    copiedTripId.value = trip.id;
    const dummyUrl = `https://tapaklokal.com/trips/${trip.slug}?ref=AFF-${trip.id}98X`;
    if (navigator.clipboard) {
        navigator.clipboard.writeText(dummyUrl).catch(() => {});
    }
    triggerToast(`Link promo "${trip.title}" berhasil disalin!`);
    setTimeout(() => {
        copiedTripId.value = null;
    }, 2000);
};

// Interactive Commission Calculator State
const calcMonthlyBookings = ref(10);
const calcAvgPrice = ref(2500000);
const calcCommissionRate = ref(10);

const changeAvgPrice = (delta) => {
    const newVal = calcAvgPrice.value + delta;
    if (newVal >= 500000 && newVal <= 25000000) {
        calcAvgPrice.value = newVal;
    }
};

const changeMonthlyBookings = (delta) => {
    const newVal = calcMonthlyBookings.value + delta;
    if (newVal >= 1 && newVal <= 100) {
        calcMonthlyBookings.value = newVal;
    }
};

const changeCommissionRate = (delta) => {
    const newVal = calcCommissionRate.value + delta;
    if (newVal >= 1 && newVal <= 50) {
        calcCommissionRate.value = newVal;
    }
};

const calculatedMonthlyCommission = computed(() => {
    const totalVolume = calcMonthlyBookings.value * calcAvgPrice.value;
    return Math.round((totalVolume * calcCommissionRate.value) / 100);
});

const calculatedAnnualCommission = computed(() => {
    return calculatedMonthlyCommission.value * 12;
});

const formatCurrency = (val) => {
    return new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: 'IDR',
        maximumFractionDigits: 0,
    }).format(val);
};

// Featured Trip Items
const featuredTrips = [
    {
        id: 1,
        slug: 'open-trip-raja-ampat',
        badge: 'Open Trip',
        location: 'Raja Ampat, Papua Barat',
        title: 'Open Trip Raja Ampat',
        description: 'Jelajahi surga tersembunyi dengan laut biru jernih dan keindahan bawah laut berkelas dunia.',
        price: 'Rp9.500.000',
        priceNumeric: 9500000,
        rate: 10,
        image: 'https://images.unsplash.com/photo-1544644181-1484b3fdfc62?auto=format&fit=crop&w=800&q=85',
    },
    {
        id: 2,
        slug: 'open-trip-bromo',
        badge: 'Open Trip',
        location: 'Bromo, Jawa Timur',
        title: 'Open Trip Bromo',
        description: 'Saksikan keindahan matahari terbit dari salah satu gunung berapi paling ikonik di Nusantara.',
        price: 'Rp1.250.000',
        priceNumeric: 1250000,
        rate: 12,
        image: 'https://images.unsplash.com/photo-1588668214407-6ea9a6d8c272?auto=format&fit=crop&w=800&q=85',
    },
    {
        id: 3,
        slug: 'explore-labuan-bajo',
        badge: 'Paket Wisata',
        location: 'Labuan Bajo, Nusa Tenggara Timur',
        title: 'Explore Labuan Bajo',
        description: 'Nikmati keindahan pulau-pulau eksotis, trekking Pulau Padar, dan bertemu langsung dengan komodo.',
        price: 'Rp4.800.000',
        priceNumeric: 4800000,
        rate: 15,
        image: 'https://images.unsplash.com/photo-1518548419970-58e3b4079ab2?auto=format&fit=crop&w=800&q=85',
    },
];

// 4 Step Process
const steps = [
    {
        number: 1,
        title: 'Daftar Affiliate',
        description: 'Buat akun affiliate dengan mudah dalam 2 menit tanpa syarat minimal followers.',
        icon: UserPlus,
    },
    {
        number: 2,
        title: 'Dapatkan Link Referral',
        description: 'Pilih paket wisata favorit audiensmu dan dapatkan tautan referral unik.',
        icon: Link2,
    },
    {
        number: 3,
        title: 'Bagikan ke Audiens',
        description: 'Sebarkan link di media sosial, TikTok, Instagram Bio, blog, atau grup WhatsApp.',
        icon: Share2,
    },
    {
        number: 4,
        title: 'Dapatkan Komisi',
        description: 'Terima komisi transparan setiap kali ada pemesanan yang berhasil melalui link kamu.',
        icon: Banknote,
    },
];

// Why Join Benefit Points (2-Column Layout matching design)
const whyJoinBenefits = [
    {
        title: 'Dapatkan Komisi',
        description: 'Komisi dari setiap pemesanan yang dilakukan melalui link affiliate kamu.',
        icon: Wallet,
    },
    {
        title: 'Pilih Paket Wisata Terlengkap',
        description: 'Ratusan destinasi lokal dengan kategori beragam yang bisa kamu promosikan.',
        icon: Compass,
    },
    {
        title: 'Tracking Otomatis',
        description: 'Semua klik, pemesanan, dan komisi tercatat real-time di dashboard kamu.',
        icon: TrendingUp,
    },
    {
        title: 'Fleksibel & Mudah',
        description: 'Bisa dilakukan kapan saja, di mana saja, tanpa biaya awal.',
        icon: Clock,
    },
];

// Real Impact Items
const impactPoints = [
    {
        title: 'Destinasi lokal lebih dikenal',
        description: 'Semakin banyak wisatawan yang menjelajahi tempat-tempat indah dan budaya tersembunyi di Indonesia.',
        icon: Mountain,
    },
    {
        title: 'Pelaku wisata berkembang',
        description: 'Memberikan kesempatan lebih besar bagi pemandu, perahu lokal, dan homestay lokal untuk terus bertumbuh.',
        icon: Users,
    },
    {
        title: 'Ekonomi daerah ikut tumbuh',
        description: 'Setiap perjalanan berkontribusi langsung pada kesejahteraan masyarakat di sekitar destinasi wisata.',
        icon: TrendingUp,
    },
];

// FAQs
const faqs = ref([
    {
        q: 'Siapa saja yang bisa bergabung menjadi Affiliate TapakLokal?',
        a: 'Semua orang bisa bergabung! Baik kamu seorang Content Creator, Travel Blogger, pemilik komunitas wisata, agen tur freelance, maupun traveler enthusiast yang suka merekomendasikan liburan seru ke teman dan keluarga.',
        open: false,
    },
    {
        q: 'Berapa besar komisi yang akan saya dapatkan?',
        a: 'Komisi berkisar antara 10% hingga 20% dari total nilai transaksi per pemesanan paket trip yang berhasil, tergantung pada jenis paket dan kategori program kemitraan.',
        open: false,
    },
    {
        q: 'Bagaimana sistem pelacakan (tracking) link affiliate bekerja?',
        a: 'Setiap link affiliate dilengkapi dengan cookie tracking berdurasi 30 hari. Jika audiens mengklik link kamu dan melakukan pemesanan dalam 30 hari, komisi otomatis masuk ke akun affiliate kamu.',
        open: false,
    },
    {
        q: 'Kapan komisi affiliate dicairkan?',
        a: 'Komisi dapat dicairkan langsung ke rekening bank lokal kamu (BCA, Mandiri, BRI, BNI, dll) atau TapakWallet setiap minggu setelah trip selesai dilaksanakan dengan minimal penarikan Rp 50.000.',
        open: false,
    },
    {
        q: 'Apakah ada biaya pendaftaran atau target bulanan?',
        a: 'Tidak ada! Pendaftaran program affiliate TapakLokal 100% gratis selamanya tanpa ada biaya tersembunyi maupun kuota target penjualan minimum.',
        open: false,
    },
]);

const toggleFaq = (index) => {
    faqs.value[index].open = !faqs.value[index].open;
};
</script>

<template>
    <Head>
        <title>Program Affiliate • TapakLokal</title>
        <meta name="description" content="Gabung Program Affiliate TapakLokal. Bagikan link trip wisata keindahan Indonesia, nikmati komisi hingga 20% per pemesanan. Gratis, mudah, dan transparan." />
    </Head>

    <div class="min-h-screen bg-[#f8fafc] font-sans text-[#172c50] selection:bg-[#0088ff] selection:text-white">
        <!-- Main Navigation -->
        <MainNavigation :transparent-on-top="false" />

        <!-- Traveloka 1:1 Auth Modal -->
        <AuthModal
            :open="isAuthModalOpen"
            :title="authModalTitle"
            :subtitle="authModalSubtitle"
            @close="isAuthModalOpen = false"
            @login-success="handleLoginSuccess"
            @guest-continue="handleGuestContinue"
        />

        <!-- Toast Feedback Notification -->
        <Transition
            enter-active-class="transition duration-300 ease-out"
            enter-from-class="transform translate-y-4 opacity-0 scale-95"
            enter-to-class="transform translate-y-0 opacity-100 scale-100"
            leave-active-class="transition duration-200 ease-in"
            leave-from-class="transform translate-y-0 opacity-100 scale-100"
            leave-to-class="transform translate-y-4 opacity-0 scale-95"
        >
            <div
                v-if="showToast"
                class="fixed bottom-6 right-6 z-50 flex items-center gap-3 rounded-2xl border border-emerald-200 bg-white/95 px-4 py-3 shadow-[0_12px_36px_rgba(15,44,92,0.18)] backdrop-blur-md"
            >
                <div class="flex size-7 shrink-0 items-center justify-center rounded-full bg-emerald-500 text-white">
                    <Check class="size-4 stroke-[3]" />
                </div>
                <p class="text-xs sm:text-sm font-semibold text-slate-800">{{ toastMessage }}</p>
            </div>
        </Transition>

        <main class="w-full overflow-x-hidden">
            <!-- Unified Page Container -->
            <div class="mx-auto max-w-[1200px] px-4 pt-6 pb-20 sm:px-6 sm:pt-8 sm:pb-28 md:pt-10 lg:px-8 lg:pt-12 xl:pt-14">
                <!-- ======================================================= -->
                <!-- 1. HERO SECTION (CARD STYLE MATCHING ACCESSIBILITY GUIDE) -->
                <!-- ======================================================= -->
                <section
                    aria-labelledby="affiliate-hero-title"
                    class="relative overflow-hidden rounded-[28px] border border-[#dce8f8] bg-[#f8fbff] p-6 sm:rounded-[36px] sm:p-9 md:p-11 lg:rounded-[40px] lg:p-12 xl:p-14 shadow-[0_4px_24px_rgba(37,99,235,0.03)]"
                >
                    <div class="grid grid-cols-1 items-center gap-8 lg:grid-cols-[1.1fr_1fr] lg:gap-8 xl:gap-12">
                        <!-- Left Content -->
                        <div class="text-left">
                            <p class="text-xs font-bold uppercase tracking-[0.14em] text-[#2563eb]">
                                PROGRAM KEMITRAAN RESMI
                            </p>
                            <h1
                                id="affiliate-hero-title"
                                class="mt-2 text-3xl font-extrabold tracking-tight text-[#0f172a] sm:text-4xl md:text-5xl lg:text-[48px] xl:text-[52px] leading-[1.12] sm:leading-[1.08]"
                            >
                                Bagikan Perjalanan,<br />
                                <span class="text-[#2563eb]">Dapatkan Penghasilan.</span>
                            </h1>

                            <p class="mt-4 sm:mt-6 max-w-xl text-sm leading-relaxed text-[#475569] sm:text-base lg:text-[17px]">
                                Ajak lebih banyak orang menjelajahi keindahan Indonesia dan dapatkan komisi dari setiap pemesanan melalui link affiliate kamu.
                            </p>

                            <!-- Action Buttons -->
                            <div class="mt-6 sm:mt-8 flex flex-col sm:flex-row items-stretch sm:items-center gap-3 sm:gap-4">
                                <button
                                    type="button"
                                    class="group inline-flex min-h-[48px] items-center justify-center gap-2 rounded-full bg-[#2563eb] px-7 py-3.5 text-sm font-bold text-white shadow-[0_8px_20px_rgba(37,99,235,0.25)] transition-all duration-300 hover:bg-[#1d4ed8] hover:shadow-[0_12px_28px_rgba(37,99,235,0.4)] hover:-translate-y-0.5 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-[#2563eb] sm:px-8 sm:py-4 sm:text-base text-center cursor-pointer"
                                    @click="openAffiliateAuthModal('join')"
                                >
                                    <span>Gabung Jadi Affiliate</span>
                                    <ArrowRight class="size-4 shrink-0 stroke-[2.5] transition-transform duration-300 group-hover:translate-x-1" aria-hidden="true" />
                                </button>

                                <Link
                                    href="/cari-trip"
                                    class="inline-flex min-h-[48px] items-center justify-center gap-2 rounded-full border border-[#e2eaf4] bg-white px-6 py-3.5 text-sm font-bold text-[#1e293b] shadow-xs transition-all duration-200 hover:bg-slate-50 hover:border-[#cbd5e1] hover:shadow-sm hover:-translate-y-0.5 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-[#2563eb] sm:px-7 sm:py-4 sm:text-base text-center"
                                >
                                    <span>Lihat Paket Wisata</span>
                                </Link>
                            </div>
                        </div>

                        <!-- Right Graphic: Affiliate Live Dashboard Card (Proportional & Enlarged) -->
                        <div class="flex items-center justify-center lg:justify-end">
                            <div class="w-full max-w-[390px] sm:max-w-[430px] lg:max-w-[460px] select-none">
                                <!-- Foreground Mockup: Affiliate Live Dashboard Card -->
                                <div class="rounded-[28px] sm:rounded-[32px] border border-[#bfdbfe] bg-white p-6 sm:p-7 shadow-[0_20px_48px_rgba(37,99,235,0.12)] backdrop-blur-md">
                                    <!-- Card Header -->
                                    <div class="flex items-center justify-between border-b border-slate-100 pb-4">
                                        <div class="flex items-center gap-3">
                                            <div class="flex size-11 items-center justify-center rounded-2xl bg-[#2563eb] text-white shadow-xs">
                                                <BadgePercent class="size-5.5" />
                                            </div>
                                            <div>
                                                <p class="text-sm sm:text-base font-bold text-slate-800 leading-tight">TapakLokal Affiliate</p>
                                                <p class="text-xs text-slate-400 font-medium mt-0.5">Panel Kemitraan Resmi</p>
                                            </div>
                                        </div>
                                      
                                    </div>

                                    <!-- Commission Balance Card -->
                                    <div class="mt-4 sm:mt-5 rounded-2xl bg-gradient-to-br from-[#1e40af] via-[#2563eb] to-[#3b82f6] p-5 sm:p-5.5 text-white shadow-md">
                                        <div class="flex items-center justify-between text-xs sm:text-[13px] text-blue-100">
                                            <span class="inline-flex items-center gap-1.5 font-medium">
                                                <Wallet class="size-3.5 sm:size-4" /> Total Komisi Kamu
                                            </span>
                                            <span class="rounded-lg bg-white/20 px-2 py-0.5 text-[10px] sm:text-[11px] font-bold text-emerald-200">
                                                Komisi s.d 20%
                                            </span>
                                        </div>
                                        <div class="mt-2 text-2xl sm:text-3xl font-black tracking-tight">
                                            Rp 4.850.000
                                        </div>
                                        <div class="mt-2.5 flex items-center justify-between text-xs text-blue-100 pt-2.5 border-t border-white/20">
                                            <span>18 Booking Berhasil</span>
                                            <span class="font-bold text-white flex items-center gap-1">
                                                <span class="size-1.5 rounded-full bg-emerald-300"></span> Siap Cair
                                            </span>
                                        </div>
                                    </div>

                                    <!-- Active Referral Link Snippet -->
                                    <div class="mt-4 rounded-2xl border border-slate-100 bg-[#f8fafc] p-3.5 sm:p-4">
                                        <div class="flex items-center justify-between text-xs sm:text-[13px]">
                                            <span class="font-bold text-slate-700">Link Referral Kamu:</span>
                                            <span class="text-xs text-[#2563eb] font-semibold">Tervalidasi</span>
                                        </div>
                                        <div class="mt-2 flex items-center justify-between gap-2 rounded-xl bg-white border border-slate-200 px-3 py-2 text-xs sm:text-sm text-slate-600 font-mono">
                                            <span class="truncate">tapaklokal.com/ref?a=TL77</span>
                                            <button
                                                type="button"
                                                class="inline-flex shrink-0 items-center gap-1 rounded-lg bg-[#2563eb] px-3 py-1.5 text-xs font-bold text-white transition hover:bg-[#1d4ed8] cursor-pointer shadow-xs"
                                                @click="openAffiliateAuthModal('copy_referral')"
                                            >
                                                <Copy class="size-3" />
                                                <span>Salin</span>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>

                <!-- ========================================================== -->
                <!-- 2. SECTION: KENAPA JADI AFFILIATE TAPAK LOKAL? (2-COL)     -->
                <!-- ========================================================== -->
                <section class="mt-14 sm:mt-20" aria-labelledby="why-join-title">
                    <div class="grid grid-cols-1 items-center gap-10 lg:grid-cols-12 lg:gap-12 xl:gap-16">
                        <!-- Left Side: Layered Travel Photos (With Paper Tape / Tempelan Kertas) -->
                        <div class="lg:col-span-6 flex items-center justify-center">
                            <div class="relative w-full max-w-[460px] py-6 sm:py-8 select-none">
                                <!-- Main Center Photo -->
                                <div class="relative z-10 mx-auto w-[82%] sm:w-[85%] transition-transform duration-300 hover:scale-[1.02]">
                                    <!-- Optional subtle top tape for main card -->
                                    <div class="absolute -top-2.5 left-1/2 -translate-x-1/2 z-30 h-4.5 sm:h-5 w-14 sm:w-16 -rotate-1 rounded-xs bg-[#ebe3d0]/90 border-t border-b border-[#cfc3ad] shadow-xs backdrop-blur-xs pointer-events-none"></div>
                                    <div class="overflow-hidden rounded-[24px] sm:rounded-[32px] border-4 border-white bg-white shadow-[0_16px_40px_rgba(15,44,92,0.12)]">
                                        <img
                                            src="https://images.unsplash.com/photo-1464822759023-fed622ff2c3b?auto=format&fit=crop&w=800&q=85"
                                            alt="Petualangan Wisata Indonesia"
                                            class="aspect-[4/3] w-full object-cover"
                                        />
                                    </div>
                                </div>

                                <!-- Floating Top-Left Photo (Tanah Lot Bali with Paper Tape) -->
                                <div class="absolute -top-1 -left-1 sm:-left-3 z-20 w-32 sm:w-40 -rotate-8 transition-transform duration-300 hover:rotate-0 hover:scale-105">
                                    <!-- Paper Tape Graphic -->
                                    <div class="absolute -top-2.5 left-1/2 -translate-x-1/2 z-30 h-4.5 sm:h-5 w-12 sm:w-14 -rotate-3 rounded-xs bg-[#ebe3d0]/95 border-t border-b border-[#cfc3ad] shadow-xs backdrop-blur-xs pointer-events-none"></div>
                                    <div class="overflow-hidden rounded-2xl border-4 border-white bg-white shadow-[0_12px_28px_rgba(15,44,92,0.16)]">
                                        <img
                                            src="https://images.unsplash.com/photo-1537996194471-e657df975ab4?auto=format&fit=crop&w=400&q=80"
                                            alt="Tanah Lot Bali"
                                            class="aspect-square w-full object-cover"
                                        />
                                    </div>
                                </div>

                                <!-- Floating Bottom-Right Photo (Gunung Bromo with Paper Tape) -->
                                <div class="absolute -bottom-2 -right-1 sm:-right-3 z-20 w-32 sm:w-40 rotate-8 transition-transform duration-300 hover:rotate-0 hover:scale-105">
                                    <!-- Paper Tape Graphic -->
                                    <div class="absolute -top-2.5 left-1/2 -translate-x-1/2 z-30 h-4.5 sm:h-5 w-12 sm:w-14 rotate-3 rounded-xs bg-[#ebe3d0]/95 border-t border-b border-[#cfc3ad] shadow-xs backdrop-blur-xs pointer-events-none"></div>
                                    <div class="overflow-hidden rounded-2xl border-4 border-white bg-white shadow-[0_12px_28px_rgba(15,44,92,0.16)]">
                                        <img
                                            src="https://images.unsplash.com/photo-1588668214407-6ea9a6d8c272?auto=format&fit=crop&w=400&q=80"
                                            alt="Gunung Bromo"
                                            class="aspect-square w-full object-cover"
                                        />
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Right Side: Title & 4 Feature Items -->
                        <div class="lg:col-span-6 space-y-6">
                            <div>
                                <h2 id="why-join-title" class="text-2xl sm:text-3xl lg:text-[36px] font-extrabold tracking-tight text-[#0f172a] leading-tight">
                                    Kenapa Jadi Affiliate<br />
                                    Tapak Lokal?
                                </h2>
                            </div>

                            <!-- 4 Compact Benefit Rows -->
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
                <!-- 3. SECTION: MULAI DAPATKAN KOMISI DALAM 4 LANGKAH          -->
                <!-- ========================================================== -->
                <section
                    class="mt-14 sm:mt-20"
                    aria-labelledby="steps-title"
                >
                    <div
                        class="relative overflow-hidden rounded-[28px] border border-[#dce8f8] bg-[#f8fbff] p-6 sm:rounded-[36px] sm:p-9 md:p-11 lg:rounded-[40px] lg:p-12 xl:p-14 shadow-[0_4px_24px_rgba(37,99,235,0.03)]"
                    >
                        <div class="mx-auto max-w-2xl text-center">
                            <p class="text-xs font-bold uppercase tracking-[0.14em] text-[#2563eb]">
                                CARA KERJA
                            </p>
                            <h2 id="steps-title" class="mt-2 text-2xl font-extrabold tracking-tight text-[#111c38] sm:text-3xl lg:text-4xl">
                                Mulai Dapatkan Komisi dalam <span class="text-[#2563eb]">4 Langkah</span>
                            </h2>
                            <p class="mt-2.5 text-xs leading-relaxed text-[#556987] sm:text-sm">
                                Gabung sekarang dan mulai perjalanan kamu sebagai affiliate Tapak Lokal.
                            </p>
                        </div>

                        <!-- Desktop Layout (lg:flex with standalone arrows in between) -->
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

                                <!-- Standalone Arrow Between Cards (Outside Cards) -->
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

                                    <!-- Arrow Down on Mobile (between vertically stacked cards) -->
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
            <!-- 4. SECTION: PAKET WISATA YANG BISA KAMU PROMOSIKAN         -->
            <!-- ========================================================== -->
            <section class="mt-14 sm:mt-20" aria-labelledby="catalog-title">
                <div class="flex flex-col justify-between gap-3 sm:flex-row sm:items-end">
                    <div>
                        <p class="text-xs font-bold uppercase tracking-[0.14em] text-[#2563eb]">
                            KATALOG REKOMENDASI
                        </p>
                        <h2 id="catalog-title" class="mt-2 text-2xl font-extrabold tracking-tight text-[#111c38] sm:text-3xl">
                            Paket Wisata yang Bisa Kamu Promosikan
                        </h2>
                        <p class="mt-1 text-xs leading-relaxed text-[#556987] sm:text-sm">
                            Pilih dari ribuan paket wisata menarik di seluruh Indonesia dengan komisi menguntungkan.
                        </p>
                    </div>

                    <Link
                        href="/cari-trip"
                        class="inline-flex items-center gap-1.5 text-xs sm:text-sm font-bold text-[#2563eb] transition-colors hover:text-[#1d4ed8]"
                    >
                        <span>Lihat Semua Paket</span>
                        <ArrowRight class="size-4" />
                    </Link>
                </div>

                <!-- 3 Trip Cards -->
                <div class="mt-8 grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
                    <div
                        v-for="trip in featuredTrips"
                        :key="trip.id"
                        class="group flex flex-col overflow-hidden rounded-[20px] sm:rounded-[24px] border border-[#e2eaf4] bg-white shadow-[0_2px_12px_rgba(23,44,80,0.03)] transition-all duration-300 hover:-translate-y-1 hover:border-[#93c5fd] hover:shadow-[0_16px_36px_rgba(37,99,235,0.09)]"
                    >
                        <!-- Trip Image -->
                        <div class="relative aspect-[16/10] w-full overflow-hidden bg-slate-100">
                            <img
                                :src="trip.image"
                                :alt="trip.title"
                                class="size-full object-cover object-center transition-transform duration-500 group-hover:scale-105"
                            />
                            <div class="absolute top-3 left-3">
                                <span class="rounded-lg bg-[#2563eb] px-2.5 py-1 text-[11px] font-bold text-white shadow-xs">
                                    {{ trip.badge }}
                                </span>
                            </div>
                        </div>

                        <!-- Trip Body Info -->
                        <div class="flex flex-1 flex-col p-5 sm:p-6">
                            <!-- Location -->
                            <div class="flex items-center gap-1.5 text-xs font-semibold text-slate-500">
                                <MapPin class="size-3.5 text-[#2563eb] shrink-0" />
                                <span>{{ trip.location }}</span>
                            </div>

                            <!-- Title -->
                            <h3 class="mt-2 text-base sm:text-lg font-bold text-[#111c38] line-clamp-1 group-hover:text-[#2563eb] transition-colors">
                                {{ trip.title }}
                            </h3>

                            <!-- Description -->
                            <p class="mt-1.5 text-xs sm:text-[13px] leading-relaxed text-[#556987] line-clamp-2">
                                {{ trip.description }}
                            </p>

                            <!-- Price & Commission Bar -->
                            <div class="mt-auto pt-4 flex items-center justify-between border-t border-slate-100">
                                <div>
                                    <span class="text-base sm:text-lg font-extrabold text-[#2563eb]">
                                        {{ trip.price }}
                                    </span>
                                    <span class="text-xs text-slate-400 font-medium"> /orang</span>
                                </div>

                                <div class="rounded-lg border border-emerald-200 bg-emerald-50 px-2.5 py-1 text-xs font-bold text-emerald-600">
                                    Komisi {{ trip.rate }}%
                                </div>
                            </div>

                            <!-- Quick Action Button -->
                            <div class="mt-4 pt-1">
                                <button
                                    type="button"
                                    class="w-full inline-flex items-center justify-center gap-2 rounded-xl bg-[#edf5fe] hover:bg-[#2563eb] text-[#2563eb] hover:text-white py-2.5 text-xs font-bold transition-all duration-200 cursor-pointer active:scale-98"
                                    @click="openAffiliateAuthModal('copy_promo', trip)"
                                >
                                    <component :is="copiedTripId === trip.id ? Check : Copy" class="size-3.5" />
                                    <span>{{ copiedTripId === trip.id ? 'Tersalin!' : 'Salin Link Promosi' }}</span>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <!-- ========================================================== -->
            <!-- 5. INTERACTIVE CALCULATOR (Simulasi Potensi Komisi)        -->
            <!-- ========================================================== -->
            <section class="mt-14 sm:mt-20" aria-labelledby="calc-title">
                <div class="relative overflow-hidden rounded-[28px] border border-[#dce8f8] bg-[#f8fbff] p-6 sm:rounded-[36px] sm:p-9 md:p-11 lg:rounded-[40px] lg:p-12 xl:p-14 shadow-[0_4px_24px_rgba(37,99,235,0.03)]">
                    <div class="grid grid-cols-1 items-center gap-10 lg:grid-cols-12 lg:gap-8 xl:gap-12">
                        <!-- Left Side: Controls & Steppers -->
                        <div class="lg:col-span-6 xl:col-span-6">
                           

                            <h2 id="calc-title" class="mt-2 text-2xl sm:text-3xl lg:text-[34px] font-extrabold tracking-tight text-[#0f172a] leading-tight">
                                Lihat Potensi Penghasilanmu
                            </h2>
                            <p class="mt-2 text-xs sm:text-sm text-[#556987] leading-relaxed max-w-lg">
                                Coba hitung estimasi komisi berdasarkan rata-rata harga paket wisata dan jumlah pemesanan tiap bulan.
                            </p>

                            <!-- Stepper 1: Rata-rata harga paket wisata -->
                            <div class="mt-6 flex flex-col sm:flex-row sm:items-center justify-between gap-2.5 py-1">
                                <span class="text-xs sm:text-sm font-semibold text-slate-700">
                                    Rata-rata harga paket wisata
                                </span>
                                <div class="flex items-center justify-between gap-3 rounded-2xl border border-slate-200 bg-white px-3 py-2 shadow-xs min-w-[210px] sm:min-w-[230px]">
                                    <button
                                        type="button"
                                        aria-label="Kurangi harga paket wisata"
                                        class="flex size-7 items-center justify-center rounded-lg text-slate-500 hover:bg-slate-100 hover:text-slate-800 active:scale-95 transition cursor-pointer disabled:opacity-30 disabled:cursor-not-allowed"
                                        :disabled="calcAvgPrice <= 500000"
                                        @click="changeAvgPrice(-250000)"
                                    >
                                        <Minus class="size-4 stroke-[2.5]" />
                                    </button>
                                    <span class="text-xs sm:text-sm font-extrabold text-slate-900">
                                        {{ formatCurrency(calcAvgPrice) }}
                                    </span>
                                    <button
                                        type="button"
                                        aria-label="Tambah harga paket wisata"
                                        class="flex size-7 items-center justify-center rounded-lg text-slate-500 hover:bg-slate-100 hover:text-slate-800 active:scale-95 transition cursor-pointer disabled:opacity-30 disabled:cursor-not-allowed"
                                        :disabled="calcAvgPrice >= 25000000"
                                        @click="changeAvgPrice(250000)"
                                    >
                                        <Plus class="size-4 stroke-[2.5]" />
                                    </button>
                                </div>
                            </div>

                            <!-- Stepper 2: Jumlah pemesanan per bulan -->
                            <div class="mt-2.5 flex flex-col sm:flex-row sm:items-center justify-between gap-2.5 py-1">
                                <span class="text-xs sm:text-sm font-semibold text-slate-700">
                                    Jumlah pemesanan per bulan
                                </span>
                                <div class="flex items-center justify-between gap-3 rounded-2xl border border-slate-200 bg-white px-3 py-2 shadow-xs min-w-[210px] sm:min-w-[230px]">
                                    <button
                                        type="button"
                                        aria-label="Kurangi jumlah pemesanan"
                                        class="flex size-7 items-center justify-center rounded-lg text-slate-500 hover:bg-slate-100 hover:text-slate-800 active:scale-95 transition cursor-pointer disabled:opacity-30 disabled:cursor-not-allowed"
                                        :disabled="calcMonthlyBookings <= 1"
                                        @click="changeMonthlyBookings(-1)"
                                    >
                                        <Minus class="size-4 stroke-[2.5]" />
                                    </button>
                                    <span class="text-xs sm:text-sm font-extrabold text-slate-900">
                                        {{ calcMonthlyBookings }}
                                    </span>
                                    <button
                                        type="button"
                                        aria-label="Tambah jumlah pemesanan"
                                        class="flex size-7 items-center justify-center rounded-lg text-slate-500 hover:bg-slate-100 hover:text-slate-800 active:scale-95 transition cursor-pointer disabled:opacity-30 disabled:cursor-not-allowed"
                                        :disabled="calcMonthlyBookings >= 100"
                                        @click="changeMonthlyBookings(1)"
                                    >
                                        <Plus class="size-4 stroke-[2.5]" />
                                    </button>
                                </div>
                            </div>

                            <!-- Stepper 3: Persentase komisi -->
                            <div class="mt-2.5 flex flex-col sm:flex-row sm:items-center justify-between gap-2.5 py-1">
                                <span class="text-xs sm:text-sm font-semibold text-slate-700">
                                    Persentase komisi
                                </span>
                                <div class="flex items-center justify-between gap-3 rounded-2xl border border-slate-200 bg-white px-3 py-2 shadow-xs min-w-[210px] sm:min-w-[230px]">
                                    <button
                                        type="button"
                                        aria-label="Kurangi persentase komisi"
                                        class="flex size-7 items-center justify-center rounded-lg text-slate-500 hover:bg-slate-100 hover:text-slate-800 active:scale-95 transition cursor-pointer disabled:opacity-30 disabled:cursor-not-allowed"
                                        :disabled="calcCommissionRate <= 1"
                                        @click="changeCommissionRate(-1)"
                                    >
                                        <Minus class="size-4 stroke-[2.5]" />
                                    </button>
                                    <span class="text-xs sm:text-sm font-extrabold text-slate-900">
                                        {{ calcCommissionRate }}%
                                    </span>
                                    <button
                                        type="button"
                                        aria-label="Tambah persentase komisi"
                                        class="flex size-7 items-center justify-center rounded-lg text-slate-500 hover:bg-slate-100 hover:text-slate-800 active:scale-95 transition cursor-pointer disabled:opacity-30 disabled:cursor-not-allowed"
                                        :disabled="calcCommissionRate >= 50"
                                        @click="changeCommissionRate(1)"
                                    >
                                        <Plus class="size-4 stroke-[2.5]" />
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Right Side: Result Card + Tilted Polaroid Photo with Washi Tape -->
                        <div class="lg:col-span-6 xl:col-span-6 flex items-center justify-center lg:justify-end">
                            <div class="relative w-full max-w-[500px] rounded-[28px] border border-[#d8eafb] bg-white/95 p-6 sm:p-7 shadow-[0_12px_36px_rgba(37,99,235,0.06)]">
                                <div class="flex flex-col sm:flex-row items-center sm:items-center justify-between gap-5 sm:gap-6">
                                    <!-- Result Content -->
                                    <div class="flex-1 text-center sm:text-left pt-2 sm:pt-4">
                                        <p class="text-xs sm:text-sm font-bold text-slate-700">
                                            Estimasi Penghasilan
                                        </p>
                                        <div class="mt-1 text-2xl sm:text-3xl lg:text-[32px] font-black tracking-tight text-[#2563eb]">
                                            {{ formatCurrency(calculatedMonthlyCommission) }}
                                        </div>
                                        <p class="mt-0.5 text-xs font-semibold text-slate-500">
                                            per bulan
                                        </p>

                                        <p class="mt-4 text-[11px] leading-relaxed text-slate-400 max-w-[210px] mx-auto sm:mx-0">
                                            *Perhitungan ini adalah estimasi. Nilai dapat berbeda sesuai paket dan kebijakan komisi.
                                        </p>

                                        <div class="mt-5">
                                            <button
                                                type="button"
                                                class="inline-flex items-center justify-center gap-2 rounded-full bg-[#2563eb] hover:bg-[#1d4ed8] px-5 sm:px-6 py-3 text-xs sm:text-sm font-bold text-white shadow-[0_8px_20px_rgba(37,99,235,0.25)] transition-all duration-200 hover:-translate-y-0.5 active:scale-95 text-center cursor-pointer"
                                                @click="openAffiliateAuthModal('join')"
                                            >
                                                <span>Mulai Jadi Affiliate</span>
                                                <ArrowRight class="size-4 stroke-[2.5]" />
                                            </button>
                                        </div>
                                    </div>

                                    <!-- Tilted Photo with Washi Tape (Tempel seperti hero section) -->
                                    <div class="relative shrink-0 select-none pt-6 pb-2 sm:pt-7 sm:pb-3">
                                        <!-- Floating Caption Badge -->
                                        <div class="hidden sm:block absolute top-1.5 -right-2 z-20 text-right">
                                            <span class="inline-block text-[10px] font-bold text-slate-600 italic rotate-6 bg-amber-50 border border-amber-200/80 px-2 py-0.5 rounded-md shadow-2xs">
                                                Perjalanan Lebih Bermakna
                                            </span>
                                        </div>

                                        <!-- Polaroid Photo Card with Tape -->
                                        <div class="relative w-36 sm:w-40 lg:w-44 rotate-6 rounded-2xl border-4 border-white bg-white p-1.5 pb-3 shadow-[0_12px_28px_rgba(15,44,92,0.14)] transition-transform duration-300 hover:rotate-3 hover:scale-105">
                                            <!-- Washi Tape Graphic -->
                                            <div class="absolute -top-2.5 left-1/2 -translate-x-1/2 w-12 h-5 bg-[#e2d9c8]/90 border-t border-b border-[#cfc3ad] -rotate-2 rounded-xs shadow-xs z-10"></div>
                                            <img
                                                src="https://images.unsplash.com/photo-1537996194471-e657df975ab4?auto=format&fit=crop&w=400&q=80"
                                                alt="Destinasi Wisata Nusantara"
                                                class="aspect-[3/4] w-full rounded-xl object-cover"
                                            />
                                            <p class="mt-2 text-center text-[9px] sm:text-[10px] font-bold text-slate-600 leading-tight">
                                                Rezeki Pelaku Lokal
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
            <!-- 7. SECTION: FAQ (Pertanyaan yang Sering Diajukan)          -->
            <!-- ========================================================== -->
            <section class="mt-14 sm:mt-20" aria-labelledby="faq-title">
                <div class="mx-auto max-w-2xl text-center">
                    <p class="text-xs font-bold uppercase tracking-[0.14em] text-[#2563eb]">
                        BANTUAN & INFORMASI
                    </p>
                    <h2 id="faq-title" class="mt-2 text-2xl font-extrabold tracking-tight text-[#111c38] sm:text-3xl">
                        Pertanyaan yang Sering Diajukan
                    </h2>
                    <p class="mt-2 text-xs sm:text-sm text-[#556987]">
                        Punya pertanyaan seputar program affiliate? Temukan jawabannya di bawah ini.
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
            <!-- 8. BOTTOM CTA BANNER (Cinematic Editorial Style)           -->
            <!-- ========================================================== -->
            <section class="mt-14 sm:mt-20">
                <div class="relative overflow-hidden rounded-[28px] sm:rounded-[36px] lg:rounded-[40px] border border-slate-700/40 p-8 sm:p-12 md:p-16 text-center text-white shadow-[0_20px_50px_rgba(5,19,41,0.35)] select-none">
                    <!-- Background Landscape Photo -->
                    <img
                        src="https://images.unsplash.com/photo-1544644181-1484b3fdfc62?auto=format&fit=crop&w=1600&q=85"
                        alt="Pesona Alam Indonesia"
                        class="absolute inset-0 size-full object-cover object-center scale-105 transition-transform duration-1000 ease-out hover:scale-100"
                    />

                    <!-- Cinematic Deep Ocean & Midnight Vignette Overlays -->
                    <div class="absolute inset-0 bg-gradient-to-r from-[#05172e]/92 via-[#092548]/82 to-[#061b34]/90"></div>
                    <div class="absolute inset-0 bg-gradient-to-t from-[#041224] via-transparent to-[#041224]/70"></div>

                    <!-- Ambient Soft Lighting -->
                    <div class="pointer-events-none absolute -top-24 -right-24 size-96 rounded-full bg-sky-500/15 blur-3xl"></div>
                    <div class="pointer-events-none absolute -bottom-24 -left-24 size-96 rounded-full bg-blue-600/15 blur-3xl"></div>

                    <!-- Content -->
                    <div class="relative z-10 mx-auto max-w-2xl">
                      
                        <h2 class="text-2xl font-black tracking-tight text-white sm:text-3xl lg:text-4xl leading-tight">
                            Siap Memulai Perjalanan Affiliate Kamu?
                        </h2>
                        <p class="mt-3.5 text-xs sm:text-sm md:text-[15px] leading-relaxed text-slate-200/90 font-normal max-w-xl mx-auto">
                            Daftar sekarang, dapatkan link referral dalam hitungan menit, dan mulai bagikan keindahan Nusantara sambil mengalirkan penghasilan.
                        </p>

                    
                        <!-- Action Buttons -->
                        <div class="mt-8 flex flex-wrap items-center justify-center gap-3 sm:gap-4">
                            <button
                                type="button"
                                class="group inline-flex items-center gap-2.5 rounded-full bg-white px-7 py-3.5 text-xs sm:text-sm font-bold text-[#0c2340] shadow-[0_10px_28px_rgba(0,0,0,0.3)] transition-all duration-300 hover:bg-[#0088ff] hover:text-white hover:shadow-[0_14px_35px_rgba(0,136,255,0.5)] hover:-translate-y-0.5 active:scale-98 cursor-pointer"
                                @click="openAffiliateAuthModal('join')"
                            >
                                <span>Daftar Affiliate Sekarang</span>
                                <ArrowRight class="size-4 stroke-[2.5] transition-transform duration-300 group-hover:translate-x-1" />
                            </button>

                            <Link
                                href="/bantuan"
                                class="inline-flex items-center gap-2 rounded-full border border-white/25 bg-white/10 px-6 py-3.5 text-xs sm:text-sm font-bold text-white backdrop-blur-md transition-all duration-300 hover:bg-white hover:text-[#0c2340] hover:border-white hover:shadow-[0_10px_28px_rgba(0,0,0,0.25)] hover:-translate-y-0.5 active:scale-98"
                            >
                                <span>Pusat Bantuan</span>
                            </Link>
                        </div>
                    </div>
                </div>
            </section>
        </div>
    </main>

    <!-- Main Footer -->
    <MainFooter />
</div>
</template>

