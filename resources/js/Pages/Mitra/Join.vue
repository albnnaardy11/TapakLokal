<script setup>
import { ref, computed } from 'vue';
import { Head, Link } from '@inertiajs/vue3';
import Navbar from '@/Components/Navbar.vue';
import BottomNav from '@/Components/BottomNav.vue';
import Footer from '@/Components/Footer.vue';
import {
    Compass,
    Briefcase,
    ShoppingBag,
    Home,
    Car,
    ShieldCheck,
    Coins,
    Users,
    Sparkles,
    CheckCircle2,
    ArrowRight,
    Calculator,
    Award,
    HelpCircle,
    PhoneCall,
    MapPin,
    Star,
    ChevronDown,
    Send
} from 'lucide-vue-next';

const props = defineProps({
    appName: {
        type: String,
        default: 'TapakLokal',
    },
    user: {
        type: Object,
        default: () => ({
            name: 'albnnaardy',
            handle: '@petualangnyasar',
            points: 0,
        }),
    },
    stats: {
        type: Object,
        default: () => ({
            total_guides: '540+ Akamsi',
            total_merchants: '1.250+ UMKM',
            revenue_distributed: 'Rp 4,8 Miliar+',
            coverage_provinces: '29 Provinsi',
        }),
    },
});

// Partner Form State
const activeTab = ref('guide'); // 'guide' | 'merchant' | 'transport'
const formName = ref('');
const formPhone = ref('');
const formRegion = ref('');
const formExperience = ref('');
const isSubmitting = ref(false);
const isSuccess = ref(false);

// Potential Revenue Calculator State
const tripCount = ref(3); // trips per month
const travelerCount = ref(10); // travelers per trip
const pricePerPax = ref(450000); // IDR per traveler

const estimatedGrossRevenue = computed(() => {
    return tripCount.value * travelerCount.value * pricePerPax.value;
});

const estimatedNetPartner = computed(() => {
    // 85% goes directly to the local partner
    return Math.round(estimatedGrossRevenue.value * 0.85);
});

function formatRupiah(num) {
    return new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: 'IDR',
        maximumFractionDigits: 0,
    }).format(num);
}

function handleRegister() {
    if (!formName.value || !formPhone.value || !formRegion.value) {
        alert('Mohon lengkapi Nama, No. WhatsApp, dan Kota/Daerah Anda.');
        return;
    }
    isSubmitting.value = true;
    setTimeout(() => {
        isSubmitting.value = false;
        isSuccess.value = true;
    }, 1200);
}

const partnerBenefits = [
    {
        icon: Coins,
        title: 'Bagi Hasil Tertinggi Hingga 85%+',
        desc: 'Kami percaya ekonomi lokal harus dinikmati warga lokal. Tanpa potongan komisi terselubung dan tidak ada biaya langganan bulanan.',
    },
    {
        icon: Users,
        title: 'Akses ke Ribuan Petualang Antusias',
        desc: 'Paket trip atau produk kuliner Anda langsung dipromosikan ke petualang muda yang menghargai keotentikan dan kearifan lokal.',
    },
    {
        icon: ShieldCheck,
        title: 'Perlindungan Asuransi & Keamanan',
        desc: 'Setiap petualang dan pemandu dilindungi jaminan asuransi kecelakaan resmi selama agenda open trip berlangsung.',
    },
    {
        icon: Award,
        title: 'Pelatihan & Sertifikasi Hospitality',
        desc: 'Dapatkan pembekalan standar pemanduan ramah lingkungan, dasar pertolongan pertama (P3K), dan fotografi promosi.',
    },
];

const partnerStories = [
    {
        name: 'Cak Dimas Bromo',
        role: 'Mitra Akamsi Pemandu Bromo & Tengger',
        since: 'Mitra sejak 2024',
        revenue: 'Rata-rata Rp 12 - 18 Juta / Bulan',
        quote: 'Dulu saya sering ditawar murah oleh agen luar kota. Sejak gabung TapakLokal, harga trip saya dihargai pantas, tamunya ramah, dan uang masuk rekening langsung H+1 tanpa ribet.',
        image: 'https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?auto=format&fit=crop&w=400&q=80',
    },
    {
        name: 'Ibu Wayan Suartini',
        role: 'Produsen Pie Susu & Camilan Tradisi Bali',
        since: 'Mitra sejak 2025',
        revenue: 'Pesanan Open PO 800+ Box / Bulan',
        quote: 'Sistem titip belanja PO TapakLokal sangat membantu UMKM kami. Petualang yang habis trip tinggal ambil atau kirim paket fresh oven langsung tanpa stok mengendap.',
        image: 'https://images.unsplash.com/photo-1544005313-94ddf0286df2?auto=format&fit=crop&w=400&q=80',
    },
    {
        name: 'Bang Mansur Bajo',
        role: 'Nahkoda Kapal & Pemandu Bahari Flores',
        since: 'Mitra sejak 2024',
        revenue: 'Armada Berlayar 4x Seminggu',
        quote: 'Jadwal sewa kapal terisi teratur dari open trip TapakLokal. Kami nelayan lokal sekarang punya penghasilan pasti sepanjang tahun.',
        image: 'https://images.unsplash.com/photo-1500648767791-00dcc994a43e?auto=format&fit=crop&w=400&q=80',
    },
];

const steps = [
    {
        step: '01',
        title: 'Daftar Online Singkat',
        desc: 'Isi profil singkat, rute yang Anda kuasai, atau produk khas yang ingin dipasarkan dalam 2 menit.',
    },
    {
        step: '02',
        title: 'Verifikasi Komunitas',
        desc: 'Tim kurasi kami menghubungi Anda lewat WhatsApp dan memvalidasi keaslian rute serta identitas warga.',
    },
    {
        step: '03',
        title: 'Unggah Jadwal & Paket',
        desc: 'Kami bantu buatkan foto, jadwal keberangkatan, rincian biaya transparan, dan materi promosi.',
    },
    {
        step: '04',
        title: 'Terima Booking & Cuan',
        desc: 'Pemandu langsung jalan, tamu terverifikasi, dan pembayaran lunas masuk tepat waktu.',
    },
];

const faqs = [
    {
        q: 'Apakah ada biaya untuk mendaftar sebagai mitra?',
        a: 'Sama sekali tidak ada biaya pendaftaran (100% Gratis). Kami hanya menerapkan sistem bagi hasil sukses saat Anda menerima tamu atau pesanan.',
    },
    {
        q: 'Apakah saya harus berbadan hukum (PT / CV)?',
        a: 'Tidak wajib. Kami menyambut perorangan warga lokal (Akamsi), kelompok sadar wisata (Pokdarwis), pegiat komunitas desa, maupun UMKM rumahan.',
    },
    {
        q: 'Kapan hasil pembayaran trip atau pesanan kuliner dicairkan?',
        a: 'Pencairan dana langsung diproses otomatis H+1 setelah trip selesai terlaksana atau pesanan PO diterima oleh petualang, via transfer bank atau QRIS.',
    },
    {
        q: 'Bagaimana jika cuaca buruk atau trip terpaksa ditunda?',
        a: 'TapakLokal memiliki kebijakan penundaan cuaca ekstrem (Force Majeure) yang adil, melindungi keselamatan pemandu dan kompensasi logistik dasar mitra.',
    },
];

const activeFaq = ref(null);
function toggleFaq(idx) {
    activeFaq.value = activeFaq.value === idx ? null : idx;
}
</script>

<template>
    <Head>
        <title>Gabung Jadi Mitra Vendor & Pemandu Akamsi - TapakLokal</title>
        <meta name="description" content="Ajak petualang ke kampung halamanmu. Dapatkan penghasilan jutaan rupiah dengan bagi hasil adil s.d 85%+ bersama ekosistem pariwisata TapakLokal." />
    </Head>

    <div class="min-h-screen flex flex-col bg-slate-50 font-sans text-slate-800 antialiased selection:bg-[#0052cc] selection:text-white">
        <!-- Dual-Tier Navbar with Active Mitra Menu -->
        <Navbar
            :user="user"
            active-menu="Gabung Mitra"
        />

        <!-- Main Content -->
        <main class="flex-1">
            <!-- 1. HERO SECTION (Identical visual harmony with home hero) -->
            <section class="relative pt-2 sm:pt-4 md:pt-6 pb-8 sm:pb-12">
                <div class="max-w-7xl mx-auto px-3 sm:px-6 lg:px-8">
                    <!-- Banner Container (Rounded-3xl like main hero) -->
                    <div class="relative rounded-2xl sm:rounded-3xl md:rounded-[36px] overflow-hidden min-h-[420px] sm:min-h-[480px] md:min-h-[520px] flex items-center justify-center text-center shadow-2xl">
                        <!-- Hero Background: Authentic Indonesian mountain landscape with smiling local guide -->
                        <div class="absolute inset-0 z-0">
                            <img
                                src="https://images.unsplash.com/photo-1544644181-1484b3fdfc62?auto=format&fit=crop&w=2000&q=85"
                                alt="Mitra Akamsi TapakLokal Indonesia"
                                class="w-full h-full object-cover object-center transform scale-105"
                            />
                            <!-- Vignette & contrast gradient -->
                            <div class="absolute inset-0 bg-gradient-to-b from-black/60 via-black/50 to-black/85"></div>
                            <div class="absolute inset-0 bg-radial-gradient from-transparent via-black/20 to-black/60"></div>
                        </div>

                        <!-- Content Center -->
                        <div class="relative z-10 max-w-3xl mx-auto px-4 sm:px-6 pt-6 sm:pt-10 pb-28 sm:pb-32 md:pb-40 text-white">
                            <!-- Badge -->
                            <div class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-blue-500/25 border border-blue-400/40 text-blue-300 text-xs font-bold mb-4 backdrop-blur-xs">
                                <Sparkles class="w-3.5 h-3.5 text-amber-400" />
                                <span>Peluang Kemitraan Lokal 2026</span>
                            </div>

                            <!-- Main Headline -->
                            <h1 class="text-2xl sm:text-4xl md:text-5xl lg:text-6xl font-extrabold tracking-tight leading-[1.2] drop-shadow-md">
                                Bawa Cerita Daerahmu<br />
                                ke Seluruh Nusantara.
                            </h1>

                            <!-- Subtitle -->
                            <p class="mt-3 sm:mt-4 md:mt-5 text-xs sm:text-sm md:text-base text-slate-100/90 font-normal leading-relaxed max-w-2xl mx-auto drop-shadow">
                                Bergabunglah bersama <strong class="text-white">Mitra Akamsi & Merchant Kuliner TapakLokal</strong>. Tanpa biaya daftar, nikmati bagi hasil transparan hingga 85%+, dan sambut ribuan petualang setiap minggu.
                            </p>
                        </div>
                    </div>

                    <!-- 2. FLOATING ONBOARDING REGISTRATION WIDGET (Matching home floating widget) -->
                    <div class="max-w-5xl mx-auto -mt-20 sm:-mt-24 md:-mt-28 relative z-20 px-1 sm:px-4">
                        <div class="bg-white rounded-2xl sm:rounded-3xl shadow-2xl border border-gray-100 p-4 sm:p-6 md:p-8 backdrop-blur-xs">
                            <!-- Tab Switcher -->
                            <div class="flex items-center justify-center gap-2 sm:gap-4 mb-5 border-b border-gray-100 pb-3">
                                <!-- Tab 1: Pemandu Akamsi -->
                                <button
                                    type="button"
                                    @click="activeTab = 'guide'"
                                    :class="[
                                        'flex-1 sm:flex-initial flex items-center justify-center gap-1.5 sm:gap-2 px-3 sm:px-5 py-2 sm:py-2.5 rounded-full text-xs sm:text-sm font-bold transition cursor-pointer',
                                        activeTab === 'guide'
                                            ? 'bg-blue-50 text-[#0052cc] ring-1 ring-blue-200 shadow-2xs'
                                            : 'text-slate-600 hover:text-slate-900 hover:bg-gray-50'
                                    ]"
                                >
                                    <div :class="['w-5 h-5 sm:w-6 sm:h-6 rounded-full flex items-center justify-center shrink-0', activeTab === 'guide' ? 'bg-[#0052cc] text-white' : 'bg-blue-100 text-[#0052cc]']">
                                        <Compass class="w-3 h-3 sm:w-3.5 sm:h-3.5" />
                                    </div>
                                    <span class="whitespace-nowrap">Pemandu Akamsi</span>
                                </button>

                                <!-- Tab 2: Merchant Kuliner -->
                                <button
                                    type="button"
                                    @click="activeTab = 'merchant'"
                                    :class="[
                                        'flex-1 sm:flex-initial flex items-center justify-center gap-1.5 sm:gap-2 px-3 sm:px-5 py-2 sm:py-2.5 rounded-full text-xs sm:text-sm font-bold transition cursor-pointer',
                                        activeTab === 'merchant'
                                            ? 'bg-orange-50 text-orange-600 ring-1 ring-orange-200 shadow-2xs'
                                            : 'text-slate-600 hover:text-slate-900 hover:bg-gray-50'
                                    ]"
                                >
                                    <div :class="['w-5 h-5 sm:w-6 sm:h-6 rounded-full flex items-center justify-center shrink-0', activeTab === 'merchant' ? 'bg-orange-500 text-white' : 'bg-orange-100 text-orange-600']">
                                        <ShoppingBag class="w-3.5 h-3.5" />
                                    </div>
                                    <span class="whitespace-nowrap">Merchant Kuliner PO</span>
                                </button>

                                <!-- Tab 3: Homestay & Transport -->
                                <button
                                    type="button"
                                    @click="activeTab = 'transport'"
                                    :class="[
                                        'flex-1 sm:flex-initial flex items-center justify-center gap-1.5 sm:gap-2 px-3 sm:px-5 py-2 sm:py-2.5 rounded-full text-xs sm:text-sm font-bold transition cursor-pointer hidden md:flex',
                                        activeTab === 'transport'
                                            ? 'bg-emerald-50 text-emerald-700 ring-1 ring-emerald-200 shadow-2xs'
                                            : 'text-slate-600 hover:text-slate-900 hover:bg-gray-50'
                                    ]"
                                >
                                    <div :class="['w-5 h-5 sm:w-6 sm:h-6 rounded-full flex items-center justify-center shrink-0', activeTab === 'transport' ? 'bg-emerald-600 text-white' : 'bg-emerald-100 text-emerald-600']">
                                        <Car class="w-3 h-3 sm:w-3.5 sm:h-3.5" />
                                    </div>
                                    <span class="whitespace-nowrap">Armada & Homestay</span>
                                </button>
                            </div>

                            <!-- Success Banner -->
                            <div v-if="isSuccess" class="p-6 bg-emerald-50 border border-emerald-200 rounded-2xl text-center space-y-2">
                                <div class="w-12 h-12 rounded-full bg-emerald-100 text-emerald-600 flex items-center justify-center mx-auto">
                                    <CheckCircle2 class="w-7 h-7" />
                                </div>
                                <h3 class="font-bold text-slate-900 text-lg">Pendaftaran Anda Berhasil Terkirim!</h3>
                                <p class="text-xs sm:text-sm text-slate-600 max-w-md mx-auto">
                                    Halo <strong>{{ formName }}</strong>, tim kurator TapakLokal akan menghubungi Anda via WhatsApp di nomor <strong>{{ formPhone }}</strong> dalam 1x24 jam untuk verifikasi rute & onboarding.
                                </p>
                                <button
                                    type="button"
                                    @click="isSuccess = false; formName = ''; formPhone = ''; formRegion = ''; formExperience = ''"
                                    class="mt-3 inline-block text-xs font-bold text-[#0052cc] hover:underline"
                                >
                                    Daftarkan Layanan / Wilayah Lain
                                </button>
                            </div>

                            <!-- Interactive Registration Form -->
                            <form v-else @submit.prevent="handleRegister">
                                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-12 gap-3 sm:gap-4 items-center">
                                    <!-- 1. Nama Lengkap / Usaha -->
                                    <div class="sm:col-span-1 lg:col-span-3 bg-gray-50/90 border border-gray-200 rounded-xl px-3.5 py-2.5 transition focus-within:ring-2 focus-within:ring-[#0052cc]">
                                        <label class="block text-[10px] sm:text-[11px] font-bold text-slate-700 uppercase">
                                            {{ activeTab === 'merchant' ? 'Nama Toko / Usaha' : 'Nama Lengkap / Komunitas' }}
                                        </label>
                                        <input
                                            v-model="formName"
                                            required
                                            type="text"
                                            :placeholder="activeTab === 'merchant' ? 'Contoh: Bakpia Malioboro' : 'Contoh: Mas Dimas Tengger'"
                                            class="w-full bg-transparent text-xs sm:text-sm text-slate-800 placeholder-slate-400 border-none p-0 mt-1 focus:outline-none focus:ring-0 font-medium"
                                        />
                                    </div>

                                    <!-- 2. No. WhatsApp Aktif -->
                                    <div class="sm:col-span-1 lg:col-span-3 bg-gray-50/90 border border-gray-200 rounded-xl px-3.5 py-2.5 transition focus-within:ring-2 focus-within:ring-[#0052cc]">
                                        <label class="block text-[10px] sm:text-[11px] font-bold text-slate-700 uppercase">
                                            No. WhatsApp Aktif
                                        </label>
                                        <input
                                            v-model="formPhone"
                                            required
                                            type="tel"
                                            placeholder="Contoh: 08123456789"
                                            class="w-full bg-transparent text-xs sm:text-sm text-slate-800 placeholder-slate-400 border-none p-0 mt-1 focus:outline-none focus:ring-0 font-medium"
                                        />
                                    </div>

                                    <!-- 3. Kota / Daerah Domisili -->
                                    <div class="sm:col-span-1 lg:col-span-3 bg-gray-50/90 border border-gray-200 rounded-xl px-3.5 py-2.5 transition focus-within:ring-2 focus-within:ring-[#0052cc]">
                                        <label class="block text-[10px] sm:text-[11px] font-bold text-slate-700 uppercase">
                                            Daerah / Spot Wisata
                                        </label>
                                        <input
                                            v-model="formRegion"
                                            required
                                            type="text"
                                            placeholder="Contoh: Malang, Labuan Bajo, Bali"
                                            class="w-full bg-transparent text-xs sm:text-sm text-slate-800 placeholder-slate-400 border-none p-0 mt-1 focus:outline-none focus:ring-0 font-medium"
                                        />
                                    </div>

                                    <!-- 4. Submit Button -->
                                    <div class="sm:col-span-1 lg:col-span-3 flex items-stretch">
                                        <button
                                            type="submit"
                                            :disabled="isSubmitting"
                                            class="w-full bg-[#0052cc] hover:bg-[#003da6] text-white font-bold py-3.5 px-4 rounded-xl text-xs sm:text-sm flex items-center justify-center gap-2 transition shadow-md shadow-blue-600/20 cursor-pointer disabled:opacity-50"
                                        >
                                            <Send v-if="!isSubmitting" class="w-4 h-4" />
                                            <span>{{ isSubmitting ? 'Mengirim Data...' : 'Gabung Sekarang (Gratis)' }}</span>
                                        </button>
                                    </div>
                                </div>

                                <!-- Trust badge below form -->
                                <div class="mt-3.5 pt-3 border-t border-gray-100 flex flex-wrap items-center justify-between gap-2 text-xs text-slate-500">
                                    <div class="flex items-center gap-1.5 text-emerald-700 font-medium text-[11px]">
                                        <ShieldCheck class="w-4 h-4 text-emerald-600 shrink-0" />
                                        <span>Data Anda aman & langsung diverifikasi tim lapangan resmi TapakLokal.</span>
                                    </div>
                                    <span class="text-[11px] font-semibold text-slate-400">Gratis pendaftaran • Tanpa kontrak mengikat</span>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </section>

            <!-- 3. STATS HIGHLIGHT -->
            <section class="py-6 sm:py-8 bg-white border-y border-slate-100">
                <div class="max-w-7xl mx-auto px-3 sm:px-6 lg:px-8">
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 text-center">
                        <div class="p-4 bg-slate-50 rounded-2xl border border-slate-100">
                            <span class="block text-2xl sm:text-3xl font-black text-[#0052cc]">{{ stats.total_guides }}</span>
                            <span class="text-xs text-slate-500 font-semibold mt-0.5 block">Pemandu Akamsi Aktif</span>
                        </div>
                        <div class="p-4 bg-slate-50 rounded-2xl border border-slate-100">
                            <span class="block text-2xl sm:text-3xl font-black text-orange-600">{{ stats.total_merchants }}</span>
                            <span class="text-xs text-slate-500 font-semibold mt-0.5 block">Merchant Kuliner & UMKM</span>
                        </div>
                        <div class="p-4 bg-slate-50 rounded-2xl border border-slate-100">
                            <span class="block text-2xl sm:text-3xl font-black text-emerald-600">{{ stats.revenue_distributed }}</span>
                            <span class="text-xs text-slate-500 font-semibold mt-0.5 block">Pendapatan Terdistribusi</span>
                        </div>
                        <div class="p-4 bg-slate-50 rounded-2xl border border-slate-100">
                            <span class="block text-2xl sm:text-3xl font-black text-slate-900">{{ stats.coverage_provinces }}</span>
                            <span class="text-xs text-slate-500 font-semibold mt-0.5 block">Jangkauan Daerah Nusantara</span>
                        </div>
                    </div>
                </div>
            </section>

            <!-- 4. REVENUE SIMULATOR (Interaktif) -->
            <section class="py-12 sm:py-16 bg-slate-50">
                <div class="max-w-7xl mx-auto px-3 sm:px-6 lg:px-8">
                    <div class="bg-gradient-to-br from-blue-900 via-indigo-900 to-slate-900 rounded-3xl p-6 sm:p-10 md:p-12 text-white shadow-xl relative overflow-hidden">
                        <div class="relative z-10 grid grid-cols-1 lg:grid-cols-12 gap-8 items-center">
                            <!-- Left: Description -->
                            <div class="lg:col-span-6 space-y-4">
                                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-blue-500/20 text-blue-300 text-xs font-bold border border-blue-400/30">
                                    <Calculator class="w-3.5 h-3.5 text-blue-400" />
                                    <span>Simulasi Transparan</span>
                                </div>
                                <h2 class="text-2xl sm:text-3xl md:text-4xl font-extrabold tracking-tight leading-tight">
                                    Hitung Potensi Cuanmu Bersama TapakLokal.
                                </h2>
                                <p class="text-slate-300 text-xs sm:text-sm leading-relaxed">
                                    Anda bebas menentukan jadwal, kuota peserta, dan rute pandu. Kami menyalurkan <strong>hingga 85%+</strong> pendapatan kotor langsung ke rekening Anda tanpa potongan tersembunyi.
                                </p>

                                <div class="pt-2 space-y-2 text-xs text-slate-300">
                                    <div class="flex items-center gap-2">
                                        <CheckCircle2 class="w-4 h-4 text-emerald-400 shrink-0" />
                                        <span>Pencairan H+1 setelah trip terlaksana</span>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <CheckCircle2 class="w-4 h-4 text-emerald-400 shrink-0" />
                                        <span>Tidak ada denda pembatalan karena faktor cuaca</span>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <CheckCircle2 class="w-4 h-4 text-emerald-400 shrink-0" />
                                        <span>Tamu terverifikasi KTP & kontak darurat</span>
                                    </div>
                                </div>
                            </div>

                            <!-- Right: Interactive Sliders & Result Card -->
                            <div class="lg:col-span-6 bg-white/10 backdrop-blur-md rounded-2xl p-5 sm:p-7 border border-white/20 space-y-5">
                                <!-- Slider 1: Jumlah Trip -->
                                <div>
                                    <div class="flex justify-between text-xs font-bold mb-1.5">
                                        <span>Frekuensi Trip per Bulan</span>
                                        <span class="text-amber-400 font-extrabold text-sm">{{ tripCount }} Kali Trip</span>
                                    </div>
                                    <input
                                        v-model.number="tripCount"
                                        type="range"
                                        min="1"
                                        max="8"
                                        class="w-full accent-blue-400 cursor-pointer"
                                    />
                                    <div class="flex justify-between text-[10px] text-slate-400">
                                        <span>1x (Santai)</span>
                                        <span>4x (Weekend)</span>
                                        <span>8x (Penuh)</span>
                                    </div>
                                </div>

                                <!-- Slider 2: Jumlah Tamu per Trip -->
                                <div>
                                    <div class="flex justify-between text-xs font-bold mb-1.5">
                                        <span>Jumlah Tamu per Trip</span>
                                        <span class="text-amber-400 font-extrabold text-sm">{{ travelerCount }} Orang</span>
                                    </div>
                                    <input
                                        v-model.number="travelerCount"
                                        type="range"
                                        min="4"
                                        max="20"
                                        step="2"
                                        class="w-full accent-blue-400 cursor-pointer"
                                    />
                                    <div class="flex justify-between text-[10px] text-slate-400">
                                        <span>4 Orang</span>
                                        <span>12 Orang</span>
                                        <span>20 Orang</span>
                                    </div>
                                </div>

                                <!-- Slider 3: Tarif per Tamu -->
                                <div>
                                    <div class="flex justify-between text-xs font-bold mb-1.5">
                                        <span>Tarif Trip per Orang</span>
                                        <span class="text-amber-400 font-extrabold text-sm">{{ formatRupiah(pricePerPax) }}</span>
                                    </div>
                                    <input
                                        v-model.number="pricePerPax"
                                        type="range"
                                        min="200000"
                                        max="2000000"
                                        step="50000"
                                        class="w-full accent-blue-400 cursor-pointer"
                                    />
                                    <div class="flex justify-between text-[10px] text-slate-400">
                                        <span>Rp 200rb</span>
                                        <span>Rp 1 Juta</span>
                                        <span>Rp 2 Juta</span>
                                    </div>
                                </div>

                                <!-- Result Box -->
                                <div class="pt-3 border-t border-white/15 text-center bg-black/30 p-4 rounded-xl">
                                    <span class="text-[11px] uppercase tracking-wider text-slate-300 font-bold block">
                                        Estimasi Penghasilan Bersih Mitra / Bulan
                                    </span>
                                    <div class="text-2xl sm:text-3xl font-black text-amber-300 mt-1">
                                        {{ formatRupiah(estimatedNetPartner) }}
                                    </div>
                                    <span class="text-[10px] text-slate-400 mt-1 block">
                                        *Berdasarkan pembagian 85% pendapatan trip setelah biaya operasional dasar.
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <!-- 5. 4 PILAR KEUNTUNGAN MITRA -->
            <section class="py-12 sm:py-16 bg-white">
                <div class="max-w-7xl mx-auto px-3 sm:px-6 lg:px-8">
                    <div class="text-center max-w-2xl mx-auto mb-10">
                        <h2 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight">
                            Keuntungan Nyata Jadi Mitra TapakLokal
                        </h2>
                        <p class="text-xs sm:text-sm text-slate-500 mt-2">
                            Bukan sekadar aplikasi sewa tur, kami membangun gerakan kesejahteraan ekonomi warga kampung halaman.
                        </p>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                        <div
                            v-for="(item, idx) in partnerBenefits"
                            :key="idx"
                            class="p-5 rounded-2xl bg-slate-50 border border-slate-100 hover:shadow-lg transition-all duration-300 flex flex-col justify-between"
                        >
                            <div>
                                <div class="w-11 h-11 rounded-xl bg-blue-100 text-[#0052cc] flex items-center justify-center mb-4">
                                    <component :is="item.icon" class="w-6 h-6" />
                                </div>
                                <h3 class="font-bold text-slate-900 text-base leading-snug mb-2">
                                    {{ item.title }}
                                </h3>
                                <p class="text-xs text-slate-600 leading-relaxed">
                                    {{ item.desc }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <!-- 6. 4 LANGKAH MUDAH BERGABUNG -->
            <section class="py-12 sm:py-16 bg-slate-50 border-t border-slate-100">
                <div class="max-w-7xl mx-auto px-3 sm:px-6 lg:px-8">
                    <div class="text-center max-w-2xl mx-auto mb-10">
                        <span class="text-xs font-bold text-[#0052cc] uppercase tracking-wider">Proses Mudah & Cepat</span>
                        <h2 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight mt-1">
                            4 Langkah Memulai Kemitraan
                        </h2>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                        <div
                            v-for="step in steps"
                            :key="step.step"
                            class="bg-white p-5 rounded-2xl border border-gray-100 shadow-xs relative"
                        >
                            <span class="text-3xl font-black text-blue-200 block mb-2">{{ step.step }}</span>
                            <h3 class="font-bold text-slate-900 text-sm mb-1.5">{{ step.title }}</h3>
                            <p class="text-xs text-slate-500 leading-relaxed">{{ step.desc }}</p>
                        </div>
                    </div>
                </div>
            </section>

            <!-- 7. CERITA NYATA DARI MITRA -->
            <section class="py-12 sm:py-16 bg-white border-t border-slate-100">
                <div class="max-w-7xl mx-auto px-3 sm:px-6 lg:px-8">
                    <div class="text-center max-w-2xl mx-auto mb-10">
                        <h2 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight">
                            Cerita dari Rekan Mitra
                        </h2>
                        <p class="text-xs sm:text-sm text-slate-500 mt-2">
                            Mereka yang telah merasakan dampak positif kemitraan TapakLokal di daerahnya.
                        </p>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div
                            v-for="(story, idx) in partnerStories"
                            :key="idx"
                            class="p-6 bg-slate-50 rounded-2xl border border-slate-200/80 flex flex-col justify-between"
                        >
                            <div>
                                <div class="flex items-center gap-1 text-amber-400 mb-3">
                                    <Star v-for="s in 5" :key="s" class="w-4 h-4 fill-amber-400" />
                                </div>
                                <p class="text-xs sm:text-sm text-slate-700 italic leading-relaxed mb-6">
                                    "{{ story.quote }}"
                                </p>
                            </div>

                            <div class="pt-4 border-t border-slate-200/60 flex items-center gap-3">
                                <img
                                    :src="story.image"
                                    :alt="story.name"
                                    class="w-11 h-11 rounded-full object-cover border-2 border-[#0052cc]"
                                />
                                <div>
                                    <div class="font-bold text-xs sm:text-sm text-slate-900">{{ story.name }}</div>
                                    <div class="text-[11px] text-[#0052cc] font-semibold">{{ story.role }}</div>
                                    <div class="text-[10px] text-emerald-700 font-bold mt-0.5">{{ story.revenue }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <!-- 8. FAQ ACCORDION -->
            <section class="py-12 sm:py-16 bg-slate-50 border-t border-slate-100">
                <div class="max-w-3xl mx-auto px-4 sm:px-6">
                    <div class="text-center mb-8">
                        <h2 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight">
                            Pertanyaan Seputar Kemitraan
                        </h2>
                        <p class="text-xs sm:text-sm text-slate-500 mt-1">
                            Punya pertanyaan lain? Hubungi tim support kami via WhatsApp kapan saja.
                        </p>
                    </div>

                    <div class="space-y-3">
                        <div
                            v-for="(item, idx) in faqs"
                            :key="idx"
                            class="bg-white rounded-2xl border border-gray-200 overflow-hidden"
                        >
                            <button
                                type="button"
                                @click="toggleFaq(idx)"
                                class="w-full text-left p-4 flex items-center justify-between text-xs sm:text-sm font-bold text-slate-800 hover:text-[#0052cc] transition cursor-pointer"
                            >
                                <span>{{ item.q }}</span>
                                <ChevronDown
                                    class="w-4 h-4 text-slate-400 transition-transform"
                                    :class="activeFaq === idx ? 'rotate-180 text-[#0052cc]' : ''"
                                />
                            </button>
                            <div
                                v-if="activeFaq === idx"
                                class="px-4 pb-4 text-xs text-slate-600 leading-relaxed border-t border-gray-100 pt-3"
                            >
                                {{ item.a }}
                            </div>
                        </div>
                    </div>

                    <!-- Direct Help Box -->
                    <div class="mt-8 p-4 bg-white rounded-2xl border border-blue-200 text-center flex flex-col sm:flex-row items-center justify-between gap-3">
                        <div class="flex items-center gap-2.5 text-left">
                            <div class="w-9 h-9 rounded-xl bg-emerald-100 text-emerald-600 flex items-center justify-center font-bold">
                                <PhoneCall class="w-4 h-4" />
                            </div>
                            <div>
                                <span class="text-xs font-bold text-slate-900 block">Butuh Bimbingan Pendaftaran?</span>
                                <span class="text-[11px] text-slate-500">Konsultasi langsung dengan Tim Komunitas Mitra TapakLokal</span>
                            </div>
                        </div>
                        <a
                            href="https://wa.me/6281234567890?text=Halo%20TapakLokal,%20saya%20ingin%20bergabung%20sebagai%20mitra"
                            target="_blank"
                            class="bg-emerald-600 hover:bg-emerald-700 text-white font-bold px-4 py-2 rounded-xl text-xs flex items-center gap-1.5 transition shadow-xs whitespace-nowrap"
                        >
                            <span>Chat WhatsApp Tim Mitra</span>
                        </a>
                    </div>
                </div>
            </section>
        </main>

        <!-- Footer -->
        <Footer />

        <!-- Mobile Bottom Nav -->
        <BottomNav
            active-menu="Gabung Mitra"
            :user="user"
        />
    </div>
</template>
