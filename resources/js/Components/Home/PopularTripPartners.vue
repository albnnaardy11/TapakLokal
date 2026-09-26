<script setup>
import { computed, ref, onMounted, onUnmounted } from 'vue';
import { Link } from '@inertiajs/vue3';
import { route } from 'ziggy-js';
import {
    CheckCircle2,
    MapPin,
    Star,
    X,
    Compass,
    TentTree,
    Crown,
    Phone,
    ArrowRight,
    ShieldCheck,
    Clock,
} from 'lucide-vue-next';

const props = defineProps({
    eyebrow: {
        type: String,
        default: 'Mitra Perjalanan Resmi',
    },
    titlePrefix: {
        type: String,
        default: 'Popular Trip',
    },
    titleAccent: {
        type: String,
        default: 'Partner',
    },
    subtitle: {
        type: String,
        default: 'Temukan operator terverifikasi untuk Open Trip dan Private Trip pilihan dengan armada terawat dan pemandu berlisensi.',
    },
});

// Active filter: 'all' | 'open-trip' | 'private-trip'
const activeFilter = ref('all');

// 10 Curated Verified Travel Partners
const partners = [
    {
        id: 'brenggo',
        name: 'BRENGGO.ID',
        city: 'Malang, Jawa Timur',
        tagline: 'Spesialis Bromo, Ijen & Eksplorasi Jawa Timur',
        description: 'Operator tur terpercaya dengan armada 4x4 terawat dan pemandu lokal berlisensi resmi. Telah melayani ribuan wisatawan domestik dan mancanegara dengan standar keselamatan tinggi.',
        rating: 4.9,
        reviewsCount: 238,
        tripsCount: 45,
        phone: '0812-3456-7890',
        types: ['open-trip', 'private-trip'],
        svgType: 'mountain',
        destinations: [
            {
                title: 'Open Trip Bromo Midnight Sunrise',
                destination: 'Bromo, Jawa Timur',
                type: 'open-trip',
                slug: 'open-trip-bromo',
                duration: '1 Hari',
                price: 385000,
                image: 'https://images.unsplash.com/photo-1544644181-1484b3fdfc62?auto=format&fit=crop&w=600&q=80',
            },
            {
                title: 'Open Trip Tur Pulau Pramuka',
                destination: 'Kepulauan Seribu, Jakarta',
                type: 'open-trip',
                slug: 'pulau-pramuka',
                duration: '2H 1M',
                price: 495000,
                image: 'https://images.unsplash.com/photo-1516690561799-46d8f74f9abf?auto=format&fit=crop&w=600&q=80',
            },
            {
                title: 'Private Trip Kawah Ijen Blue Fire',
                destination: 'Banyuwangi, Jawa Timur',
                type: 'private-trip',
                slug: 'private-trip-kawah-ijen',
                duration: '2H 1M',
                price: 750000,
                image: 'https://images.unsplash.com/photo-1518548419970-58e3b4079ab2?auto=format&fit=crop&w=600&q=80',
            },
        ],
    },
    {
        id: 'nusantara-adv',
        name: 'Nusantara Adventure',
        city: 'DKI Jakarta & Kepulauan Seribu',
        tagline: 'Wisata Bahari & Pulau Eksotis Dekat Ibu Kota',
        description: 'Penyedia open trip dan private charter island hopping Kepulauan Seribu dengan kapal cepat, homestay ber-AC, pemandu ramah, dan dokumentasi bawah air berkualitas.',
        rating: 4.8,
        reviewsCount: 184,
        tripsCount: 32,
        phone: '0813-8899-1122',
        types: ['open-trip', 'private-trip'],
        svgType: 'islands',
        destinations: [
            {
                title: 'Open Trip Pulau Harapan Snorkeling',
                destination: 'Kepulauan Seribu, Jakarta',
                type: 'open-trip',
                slug: 'pulau-harapan',
                duration: '2H 1M',
                price: 420000,
                image: 'https://images.unsplash.com/photo-1507525428034-b723cf961d3e?auto=format&fit=crop&w=600&q=80',
            },
            {
                title: 'Private Trip Pulau Pari Sunset Beach',
                destination: 'Kepulauan Seribu, Jakarta',
                type: 'private-trip',
                slug: 'pulau-pari',
                duration: '2H 1M',
                price: 650000,
                image: 'https://images.unsplash.com/photo-1518548419970-58e3b4079ab2?auto=format&fit=crop&w=600&q=80',
            },
        ],
    },
    {
        id: 'komodo-sea',
        name: 'Komodo Sea Journey',
        city: 'Labuan Bajo, Nusa Tenggara Timur',
        tagline: 'Phinisi Sailing & Liveaboard Taman Nasional Komodo',
        description: 'Spesialis liveaboard berlayar mengelilingi pulau-pulau spektakuler di Labuan Bajo. Tersedia armada Phinisi Standard hingga Luxury dengan chef kapal berpengalaman.',
        rating: 4.95,
        reviewsCount: 310,
        tripsCount: 52,
        phone: '0821-4455-6677',
        types: ['open-trip', 'private-trip'],
        svgType: 'phinisi',
        destinations: [
            {
                title: 'Private Trip Labuan Bajo Luxury Phinisi',
                destination: 'Labuan Bajo, NTT',
                type: 'private-trip',
                slug: 'labuan-bajo',
                duration: '3H 2M',
                price: 3135000,
                image: 'https://images.unsplash.com/photo-1501179691627-eeaa65ea017c?auto=format&fit=crop&w=600&q=80',
            },
            {
                title: 'Open Trip Sailing Komodo Island 3D2N',
                destination: 'Pulau Komodo, NTT',
                type: 'open-trip',
                slug: 'open-trip-pulau-komodo',
                duration: '3H 2M',
                price: 2450000,
                image: 'https://images.unsplash.com/photo-1518548419970-58e3b4079ab2?auto=format&fit=crop&w=600&q=80',
            },
        ],
    },
    {
        id: 'bali-paradise',
        name: 'Bali Paradise Tour',
        city: 'Denpasar & Gianyar, Bali',
        tagline: 'Otentik Nusa Penida & Tur Budaya Pulau Dewata',
        description: 'Partner terkemuka untuk tur Nusa Penida, Ubud, Kintamani, dan Bali Timur. Pengemudi lokal berlisensi dan paham spot fotografi terbaik yang bebas antrean.',
        rating: 4.88,
        reviewsCount: 265,
        tripsCount: 60,
        phone: '0819-3322-1144',
        types: ['open-trip', 'private-trip'],
        svgType: 'lotus',
        destinations: [
            {
                title: 'Open Trip Nusa Penida West Coast',
                destination: 'Nusa Penida, Bali',
                type: 'open-trip',
                slug: 'nusa-penida-west',
                duration: '1 Hari',
                price: 385000,
                image: 'https://images.unsplash.com/photo-1537996194471-e657df975ab4?auto=format&fit=crop&w=600&q=80',
            },
            {
                title: 'Private Trip Ubud Sacred Waterfall & Culture',
                destination: 'Ubud, Gianyar, Bali',
                type: 'private-trip',
                slug: 'ubud-culture',
                duration: '1 Hari',
                price: 490000,
                image: 'https://images.unsplash.com/photo-1518548419970-58e3b4079ab2?auto=format&fit=crop&w=600&q=80',
            },
        ],
    },
    {
        id: 'raja-ampat-exp',
        name: 'Raja Ampat Expedition',
        city: 'Sorong & Waisai, Papua Barat',
        tagline: 'Surga Karang Terindah di Timur Nusantara',
        description: 'Jelajah pulau karang ikonik Piaynemo, Wayag, dan Misool bersama navigator laut asli Papua. Menghadirkan petualangan laut otentik dengan kearifan lokal.',
        rating: 4.98,
        reviewsCount: 154,
        tripsCount: 28,
        phone: '0852-7788-9900',
        types: ['open-trip', 'private-trip'],
        svgType: 'manta',
        destinations: [
            {
                title: 'Open Raja Ampat Piaynemo Explorer',
                destination: 'Raja Ampat, Papua',
                type: 'open-trip',
                slug: 'open-raja-ampat',
                duration: '6H 5M',
                price: 16500000,
                image: 'https://images.unsplash.com/photo-1516690561799-46d8f74f9abf?auto=format&fit=crop&w=600&q=80',
            },
            {
                title: 'Private Safari Misool Lagoon Hopping',
                destination: 'Misool, Raja Ampat',
                type: 'private-trip',
                slug: 'misool-safari',
                duration: '5H 4M',
                price: 18200000,
                image: 'https://images.unsplash.com/photo-1507525428034-b723cf961d3e?auto=format&fit=crop&w=600&q=80',
            },
        ],
    },
    {
        id: 'lombok-horizon',
        name: 'Lombok Horizon',
        city: 'Mataram & Senaru, NTB',
        tagline: 'Trekking Rinjani & Island Hopping 3 Gili',
        description: 'Operator resmi pendakian Taman Nasional Gunung Rinjani dengan standar perlengkapan mountain gear internasional dan paket liburan santai 3 Gili.',
        rating: 4.85,
        reviewsCount: 198,
        tripsCount: 38,
        phone: '0877-6655-4433',
        types: ['open-trip', 'private-trip'],
        svgType: 'sunpeak',
        destinations: [
            {
                title: 'Open Trip Snorkeling 3 Gili Lombok',
                destination: 'Gili Trawangan, Lombok',
                type: 'open-trip',
                slug: 'gili-snorkeling',
                duration: '1 Hari',
                price: 375000,
                image: 'https://images.unsplash.com/photo-1518548419970-58e3b4079ab2?auto=format&fit=crop&w=600&q=80',
            },
            {
                title: 'Private Trekking Rinjani Summit 3D2N',
                destination: 'Gunung Rinjani, Lombok',
                type: 'private-trip',
                slug: 'rinjani-summit',
                duration: '3H 2M',
                price: 1850000,
                image: 'https://images.unsplash.com/photo-1501179691627-eeaa65ea017c?auto=format&fit=crop&w=600&q=80',
            },
        ],
    },
    {
        id: 'derawan-escapes',
        name: 'Derawan Escapes',
        city: 'Berau, Kalimantan Timur',
        tagline: 'Danau Ubur-ubur Kakaban & Resort Terapung Maratua',
        description: 'Nikmati eksotisme kepulauan Derawan, berenang bersama penyu hijau raksasa, ubur-ubur tanpa sengat di Kakaban, serta kejernihan Danau Labuan Cermin.',
        rating: 4.92,
        reviewsCount: 142,
        tripsCount: 26,
        phone: '0812-9988-7766',
        types: ['open-trip', 'private-trip'],
        svgType: 'turtle',
        destinations: [
            {
                title: 'Open Trip Derawan & Kakaban Safari',
                destination: 'Kepulauan Derawan, Berau',
                type: 'open-trip',
                slug: 'derawan-safari',
                duration: '4H 3M',
                price: 2150000,
                image: 'https://images.unsplash.com/photo-1544551763-46a013bb70d5?auto=format&fit=crop&w=600&q=80',
            },
            {
                title: 'Private Tour Labuan Cermin Mirror Lake',
                destination: 'Biduk-Biduk, Kalimantan Timur',
                type: 'private-trip',
                slug: 'labuan-cermin',
                duration: '3H 2M',
                price: 2850000,
                image: 'https://images.unsplash.com/photo-1507525428034-b723cf961d3e?auto=format&fit=crop&w=600&q=80',
            },
        ],
    },
    {
        id: 'java-heritage',
        name: 'Java Heritage Trips',
        city: 'Yogyakarta & Magelang',
        tagline: 'Perjalanan Budaya, Candi Agung & Lava Tour',
        description: 'Menyajikan tur edukasi sejarah dan budaya Jawa secara eksklusif. Rasakan kemegahan Candi Borobudur saat fajar serta pacu adrenalin di lereng Gunung Merapi.',
        rating: 4.9,
        reviewsCount: 220,
        tripsCount: 42,
        phone: '0822-1133-5577',
        types: ['private-trip'],
        svgType: 'keraton',
        destinations: [
            {
                title: 'Private Trip Sunrise Borobudur & Merapi Jeep',
                destination: 'Yogyakarta & Magelang',
                type: 'private-trip',
                slug: 'borobudur-merapi',
                duration: '1 Hari',
                price: 550000,
                image: 'https://images.unsplash.com/photo-1584810359583-96fc3448beaa?auto=format&fit=crop&w=600&q=80',
            },
            {
                title: 'Private Caving Goa Jomblang & Pantai Timang',
                destination: 'Gunungkidul, Yogyakarta',
                type: 'private-trip',
                slug: 'jomblang-timang',
                duration: '1 Hari',
                price: 680000,
                image: 'https://images.unsplash.com/photo-1501179691627-eeaa65ea017c?auto=format&fit=crop&w=600&q=80',
            },
        ],
    },
    {
        id: 'toraja-culture',
        name: 'Toraja Culture Guide',
        city: 'Tana Toraja, Sulawesi Selatan',
        tagline: 'Eksplorasi Adat Megah & Negeri di Atas Awan',
        description: 'Pemandu adat bersertifikat yang mengajak Anda memahami filosofi arsitektur Tongkonan, upacara adat Rambu Solo, serta keindahan panorama Lolai.',
        rating: 4.86,
        reviewsCount: 112,
        tripsCount: 22,
        phone: '0853-4455-6677',
        types: ['private-trip'],
        svgType: 'tongkonan',
        destinations: [
            {
                title: 'Private Trip Toraja Heritage & Kete Kesu',
                destination: 'Rantepao, Tana Toraja',
                type: 'private-trip',
                slug: 'toraja-heritage',
                duration: '3H 2M',
                price: 1750000,
                image: 'https://images.unsplash.com/photo-1518548419970-58e3b4079ab2?auto=format&fit=crop&w=600&q=80',
            },
            {
                title: 'Private Trekking Negeri di Atas Awan Lolai',
                destination: 'Toraja Utara, Sulawesi Selatan',
                type: 'private-trip',
                slug: 'lolai-cloud-land',
                duration: '2H 1M',
                price: 890000,
                image: 'https://images.unsplash.com/photo-1537996194471-e657df975ab4?auto=format&fit=crop&w=600&q=80',
            },
        ],
    },
    {
        id: 'banda-neira',
        name: 'Banda Neira Spice Tour',
        city: 'Kepulauan Banda, Maluku Tengah',
        tagline: 'Jejak Kejayaan Jalur Rempah & Bawah Laut Historis',
        description: 'Napak tilas sejarah dunia di kepulauan penghasil pala tertua. Mengunjungi Benteng Belgica abad ke-17 dan menyelam di lava flow terumbu karang hidup.',
        rating: 4.96,
        reviewsCount: 135,
        tripsCount: 20,
        phone: '0812-4433-2211',
        types: ['open-trip', 'private-trip'],
        svgType: 'compass',
        destinations: [
            {
                title: 'Open Trip Jalur Rempah Banda Neira 5D4N',
                destination: 'Banda Neira, Maluku',
                type: 'open-trip',
                slug: 'banda-neira-5d4n',
                duration: '5H 4M',
                price: 4200000,
                image: 'https://images.unsplash.com/photo-1516690561799-46d8f74f9abf?auto=format&fit=crop&w=600&q=80',
            },
            {
                title: 'Private Snorkeling Pulau Hatta & Run',
                destination: 'Kepulauan Banda, Maluku',
                type: 'private-trip',
                slug: 'pulau-hatta-snorkeling',
                duration: '3H 2M',
                price: 2800000,
                image: 'https://images.unsplash.com/photo-1507525428034-b723cf961d3e?auto=format&fit=crop&w=600&q=80',
            },
        ],
    },
];

// Filter partners based on active category
const filteredPartners = computed(() => {
    if (activeFilter.value === 'open-trip') {
        return partners.filter((p) => p.types.includes('open-trip'));
    }
    if (activeFilter.value === 'private-trip') {
        return partners.filter((p) => p.types.includes('private-trip'));
    }
    return partners;
});

// Selected Vendor for Modal
const selectedVendor = ref(null);

const openVendorProfile = (partner) => {
    selectedVendor.value = partner;
    document.body.style.overflow = 'hidden';
};

const closeVendorProfile = () => {
    selectedVendor.value = null;
    document.body.style.overflow = '';
};

const onKeyDown = (e) => {
    if (e.key === 'Escape' && selectedVendor.value) {
        closeVendorProfile();
    }
};

onMounted(() => {
    window.addEventListener('keydown', onKeyDown);
});

onUnmounted(() => {
    window.removeEventListener('keydown', onKeyDown);
    document.body.style.overflow = '';
});

const formatPrice = (value) => {
    return new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: 'IDR',
        maximumFractionDigits: 0,
    }).format(value).replace(/\s/g, '');
};
</script>

<template>
    <section class="mx-auto max-w-[1180px]" aria-labelledby="popular-partners-heading">
        <!-- Header with Cohesive Design System Hierarchy (matching DestinationExplore & TripOptions) -->
        <div class="mb-5 sm:mb-6 flex flex-col md:flex-row md:items-end justify-between gap-4 px-1">
            <div>
                <!-- Eyebrow Badge -->
                <p class="text-xs font-bold uppercase tracking-[0.16em] text-[#0088ff]">
                    {{ eyebrow }}
                </p>

                <!-- Main Heading with Brand Blue Accent -->
                <h2
                    id="popular-partners-heading"
                    class="mt-1 text-xl sm:text-2xl lg:text-[26px] font-extrabold leading-tight tracking-tight text-slate-900"
                >
                    {{ titlePrefix }} <span class="text-[#0088ff]">{{ titleAccent }}</span>
                </h2>

                <!-- Cohesive Subtitle -->
                <p class="mt-1.5 text-xs sm:text-sm font-medium text-slate-500 max-w-2xl leading-relaxed">
                    {{ subtitle }}
                </p>
            </div>

            <!-- Clean Segmented Tabs (All / Open Trip / Private Trip) with Icons -->
            <div class="inline-flex rounded-xl bg-slate-100 p-1 border border-slate-200/80 shrink-0 self-start md:self-auto">
                <button
                    type="button"
                    class="px-3.5 py-1.5 text-xs sm:text-sm font-semibold rounded-lg transition-all duration-150 inline-flex items-center gap-1.5"
                    :class="activeFilter === 'all' ? 'bg-white text-[#0088ff] shadow-xs font-bold' : 'text-slate-600 hover:text-slate-900'"
                    @click="activeFilter = 'all'"
                >
                    <Compass class="size-3.5 sm:size-4" />
                    <span>Semua (10)</span>
                </button>
                <button
                    type="button"
                    class="px-3.5 py-1.5 text-xs sm:text-sm font-semibold rounded-lg transition-all duration-150 inline-flex items-center gap-1.5"
                    :class="activeFilter === 'open-trip' ? 'bg-[#0088ff] text-white shadow-xs font-bold' : 'text-slate-600 hover:text-slate-900'"
                    @click="activeFilter = 'open-trip'"
                >
                    <TentTree class="size-3.5 sm:size-4" />
                    <span>Open Trip</span>
                </button>
                <button
                    type="button"
                    class="px-3.5 py-1.5 text-xs sm:text-sm font-semibold rounded-lg transition-all duration-150 inline-flex items-center gap-1.5"
                    :class="activeFilter === 'private-trip' ? 'bg-[#0088ff] text-white shadow-xs font-bold' : 'text-slate-600 hover:text-slate-900'"
                    @click="activeFilter = 'private-trip'"
                >
                    <Crown class="size-3.5 sm:size-4" />
                    <span>Private Trip</span>
                </button>
            </div>
        </div>

        <!-- 1:1 Flat Clean Logos Grid (Matching Airline Reference) -->
        <!-- No heavy card borders, no drop shadows, no weird colored dots -->
        <div class="mt-9 grid grid-cols-2 sm:grid-cols-3 md:grid-cols-5 gap-y-7 gap-x-4 sm:gap-x-6">
            <button
                v-for="partner in filteredPartners"
                :key="partner.id"
                type="button"
                class="group flex flex-col items-center justify-center p-3 rounded-xl transition-all duration-200 hover:bg-slate-100/70 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-[#0088ff] text-center"
                @click="openVendorProfile(partner)"
            >
                <!-- Clean Logo Emblem Graphic -->
                <div class="h-10 sm:h-11 w-full flex items-center justify-center transition-transform duration-200 group-hover:scale-105">
                    <!-- 1. Mountain Peak (BRENGGO.ID) -->
                    <svg v-if="partner.svgType === 'mountain'" viewBox="0 0 48 36" class="h-9 w-auto">
                        <polygon points="12,32 24,10 36,32" fill="#0088ff" />
                        <polygon points="24,32 34,14 44,32" fill="#0f2744" opacity="0.9" />
                        <path d="M18,22 L24,10 L30,22 Z" fill="#ffffff" />
                    </svg>

                    <!-- 2. Archipelago Islands (Nusantara Adventure) -->
                    <svg v-else-if="partner.svgType === 'islands'" viewBox="0 0 48 36" class="h-9 w-auto">
                        <circle cx="24" cy="14" r="8" fill="#f59e0b" opacity="0.4" />
                        <path d="M6,28 C12,20 20,20 26,28 Z" fill="#059669" />
                        <path d="M22,28 C28,16 38,16 44,28 Z" fill="#047857" />
                    </svg>

                    <!-- 3. Phinisi Schooner (Komodo Sea Journey) -->
                    <svg v-else-if="partner.svgType === 'phinisi'" viewBox="0 0 48 36" class="h-9 w-auto">
                        <path d="M8,29 C14,33 34,33 40,29 L42,26 L6,26 Z" fill="#0f4c81" />
                        <path d="M22,6 L22,23 L9,23 Z" fill="#0284c7" />
                        <path d="M25,10 L25,23 L36,23 Z" fill="#38bdf8" />
                    </svg>

                    <!-- 4. Tropical Lotus Crest (Bali Paradise Tour) -->
                    <svg v-else-if="partner.svgType === 'lotus'" viewBox="0 0 48 36" class="h-9 w-auto">
                        <path d="M24,8 C19,15 19,23 24,30 C29,23 29,15 24,8 Z" fill="#ea580c" />
                        <path d="M20,17 C13,21 13,26 21,29 Z" fill="#f97316" />
                        <path d="M28,17 C35,21 35,26 27,29 Z" fill="#f97316" />
                    </svg>

                    <!-- 5. Oceanic Manta Ray (Raja Ampat Expedition) -->
                    <svg v-else-if="partner.svgType === 'manta'" viewBox="0 0 48 36" class="h-9 w-auto">
                        <path d="M24,10 C29,13 40,22 37,28 C30,26 27,24 24,34 C21,24 18,26 11,28 C8,22 19,13 24,10 Z" fill="#1d4ed8" />
                    </svg>

                    <!-- 6. Sun Peak Volcano (Lombok Horizon) -->
                    <svg v-else-if="partner.svgType === 'sunpeak'" viewBox="0 0 48 36" class="h-9 w-auto">
                        <circle cx="24" cy="16" r="9" fill="#f59e0b" opacity="0.4" />
                        <polygon points="10,30 24,11 38,30" fill="#dc2626" />
                    </svg>

                    <!-- 7. Sea Turtle (Derawan Escapes) -->
                    <svg v-else-if="partner.svgType === 'turtle'" viewBox="0 0 48 36" class="h-9 w-auto">
                        <ellipse cx="24" cy="20" rx="10" ry="8" fill="#0d9488" />
                        <circle cx="24" cy="9" r="3.5" fill="#0f766e" />
                        <circle cx="11" cy="15" r="2.8" fill="#14b8a6" />
                        <circle cx="37" cy="15" r="2.8" fill="#14b8a6" />
                        <circle cx="13" cy="26" r="2.5" fill="#14b8a6" />
                        <circle cx="35" cy="26" r="2.5" fill="#14b8a6" />
                    </svg>

                    <!-- 8. Royal Keraton Motif (Java Heritage Trips) -->
                    <svg v-else-if="partner.svgType === 'keraton'" viewBox="0 0 48 36" class="h-9 w-auto">
                        <path d="M24,6 L34,17 L30,30 L18,30 L14,17 Z" fill="#b45309" />
                        <circle cx="24" cy="18" r="4.5" fill="#fbbf24" />
                    </svg>

                    <!-- 9. Curved Tongkonan Roof (Toraja Culture Guide) -->
                    <svg v-else-if="partner.svgType === 'tongkonan'" viewBox="0 0 48 36" class="h-9 w-auto">
                        <path d="M8,11 Q24,24 40,11 L36,25 L12,25 Z" fill="#9a3412" />
                        <rect x="22" y="25" width="4" height="6" fill="#7c2d12" />
                    </svg>

                    <!-- 10. Navigational Compass Rose (Banda Neira Spice Tour) -->
                    <svg v-else viewBox="0 0 48 36" class="h-9 w-auto">
                        <circle cx="24" cy="18" r="11" fill="none" stroke="#1e3a8a" stroke-width="2" />
                        <polygon points="24,9 27.5,18 24,27 20.5,18" fill="#dc2626" />
                        <polygon points="15,18 24,21.5 33,18 24,14.5" fill="#1e3a8a" />
                    </svg>
                </div>

                <!-- Brand Name Below Logo (Clean, authentic typography matching Airline Reference) -->
                <span class="mt-2 text-xs sm:text-[13px] font-medium text-slate-700 tracking-tight transition-colors group-hover:text-[#0088ff]">
                    {{ partner.name }}
                </span>
            </button>
        </div>

        <!-- Interactive Modal: Vendor Profile & Destination List -->
        <Teleport to="body">
            <div
                v-if="selectedVendor"
                class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm"
                role="dialog"
                aria-modal="true"
                :aria-label="`Profil ${selectedVendor.name}`"
                @click.self="closeVendorProfile"
            >
                <div class="relative w-full max-w-2xl max-h-[88vh] overflow-hidden rounded-3xl bg-white shadow-2xl flex flex-col border border-slate-100">
                    <!-- Modal Header -->
                    <div class="relative p-6 sm:p-7 border-b border-slate-100 bg-gradient-to-r from-slate-50 via-white to-sky-50/50 flex items-start justify-between">
                        <div class="flex items-center gap-4">
                            <!-- Logo Avatar -->
                            <div class="grid size-14 place-items-center rounded-2xl bg-white border border-slate-200/90 shadow-xs p-2">
                                <span class="text-xl font-black text-[#0088ff]">
                                    {{ selectedVendor.name.charAt(0) }}
                                </span>
                            </div>

                            <!-- Name & City -->
                            <div>
                                <div class="flex items-center gap-2">
                                    <h3 class="text-xl font-extrabold text-[#172c50]">
                                        {{ selectedVendor.name }}
                                    </h3>
                                    <span class="inline-flex items-center gap-1 rounded-full bg-sky-100 px-2 py-0.5 text-[10px] font-bold text-[#0088ff]">
                                        <CheckCircle2 class="size-3" />
                                        Terverifikasi
                                    </span>
                                </div>
                                <p class="mt-1 flex items-center gap-1 text-xs text-slate-500 font-medium">
                                    <MapPin class="size-3.5 text-[#0088ff] shrink-0" />
                                    {{ selectedVendor.city }}
                                </p>
                            </div>
                        </div>

                        <!-- Close Button -->
                        <button
                            type="button"
                            class="grid size-9 place-items-center rounded-full bg-slate-100 text-slate-500 hover:bg-slate-200 hover:text-slate-800 transition-colors"
                            aria-label="Tutup"
                            @click="closeVendorProfile"
                        >
                            <X class="size-4" />
                        </button>
                    </div>

                    <!-- Modal Body (Scrollable) -->
                    <div class="flex-1 overflow-y-auto p-6 sm:p-7 space-y-6">
                        <!-- Badges & Highlights -->
                        <div class="flex flex-wrap items-center gap-2">
                            <span
                                v-if="selectedVendor.types.includes('open-trip')"
                                class="inline-flex items-center gap-1.5 rounded-lg bg-sky-50 px-3 py-1 text-xs font-bold text-[#0088ff]"
                            >
                                Spesialis Open Trip
                            </span>
                            <span
                                v-if="selectedVendor.types.includes('private-trip')"
                                class="inline-flex items-center gap-1.5 rounded-lg bg-emerald-50 px-3 py-1 text-xs font-bold text-emerald-600"
                            >
                                Layanan Private Trip
                            </span>
                            <span class="inline-flex items-center gap-1.5 rounded-lg bg-amber-50 px-3 py-1 text-xs font-bold text-amber-600">
                                <Star class="size-3.5 fill-amber-500 text-amber-500" />
                                {{ selectedVendor.rating }} ({{ selectedVendor.reviewsCount }} Ulasan)
                            </span>
                            <span class="inline-flex items-center gap-1.5 rounded-lg bg-slate-100 px-3 py-1 text-xs font-bold text-slate-700">
                                <ShieldCheck class="size-3.5 text-slate-500" />
                                {{ selectedVendor.tripsCount }}+ Trip Selesai
                            </span>
                        </div>

                        <!-- Vendor Bio Description -->
                        <div class="rounded-2xl bg-slate-50/80 p-4 border border-slate-100 text-xs sm:text-sm text-slate-600 leading-relaxed">
                            <p class="font-bold text-slate-800 mb-1 text-xs uppercase tracking-wide text-slate-400">Tentang Operator</p>
                            <p>{{ selectedVendor.description }}</p>
                        </div>

                        <!-- Vendor Destinations List -->
                        <div>
                            <div class="flex items-center justify-between mb-3.5">
                                <h4 class="text-sm sm:text-base font-extrabold text-[#172c50] flex items-center gap-2">
                                    <Compass class="size-4 text-[#0088ff]" />
                                    Pilihan Destinasi & Jadwal Trip
                                    <span class="text-xs font-normal text-slate-400">({{ selectedVendor.destinations.length }} paket)</span>
                                </h4>
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                                <article
                                    v-for="(dest, idx) in selectedVendor.destinations"
                                    :key="idx"
                                    class="group flex flex-col rounded-2xl border border-slate-200/80 bg-white overflow-hidden shadow-xs hover:border-[#0088ff]/40 hover:shadow-md transition-all duration-200"
                                >
                                    <!-- Image Thumbnail -->
                                    <div class="relative aspect-[16/10] overflow-hidden bg-slate-100">
                                        <img
                                            :src="dest.image"
                                            :alt="dest.title"
                                            class="size-full object-cover group-hover:scale-105 transition-transform duration-300"
                                            loading="lazy"
                                        />
                                        <div class="absolute top-2.5 left-2.5">
                                            <span
                                                class="rounded-md px-2 py-0.5 text-[9px] font-extrabold uppercase text-white shadow-xs"
                                                :class="dest.type === 'open-trip' ? 'bg-[#0088ff]' : 'bg-emerald-600'"
                                            >
                                                {{ dest.type === 'open-trip' ? 'Open Trip' : 'Private Trip' }}
                                            </span>
                                        </div>
                                    </div>

                                    <!-- Content -->
                                    <div class="p-3.5 flex flex-col flex-1">
                                        <h5 class="text-xs font-bold text-slate-800 line-clamp-2 leading-snug group-hover:text-[#0088ff] transition-colors">
                                            {{ dest.title }}
                                        </h5>
                                        <p class="mt-1 flex items-center gap-1 text-[11px] text-slate-500">
                                            <MapPin class="size-3 text-[#0088ff] shrink-0" />
                                            {{ dest.destination }}
                                        </p>
                                        <p class="mt-1 flex items-center gap-1 text-[11px] text-slate-400">
                                            <Clock class="size-3 text-slate-400 shrink-0" />
                                            {{ dest.duration }}
                                        </p>

                                        <!-- Price & Action -->
                                        <div class="mt-auto pt-3 border-t border-slate-100 flex items-center justify-between">
                                            <div>
                                                <p class="text-[9px] text-slate-400">Mulai</p>
                                                <p class="text-xs font-extrabold text-[#0088ff]">
                                                    {{ formatPrice(dest.price) }}
                                                    <span class="text-[9px] font-normal text-slate-400">/org</span>
                                                </p>
                                            </div>

                                            <Link
                                                :href="typeof route === 'function' ? route('trips.show', [dest.type, dest.slug]) : `/trips/${dest.type}/${dest.slug}`"
                                                class="inline-flex items-center gap-1 rounded-lg bg-sky-50 px-2.5 py-1 text-[11px] font-bold text-[#0088ff] hover:bg-[#0088ff] hover:text-white transition-colors"
                                                @click="closeVendorProfile"
                                            >
                                                Detail
                                                <ArrowRight class="size-3" />
                                            </Link>
                                        </div>
                                    </div>
                                </article>
                            </div>
                        </div>
                    </div>

                    <!-- Modal Footer -->
                    <div class="p-4 sm:p-5 border-t border-slate-100 bg-slate-50/50 flex flex-wrap items-center justify-between gap-3">
                        <div class="flex items-center gap-2 text-xs text-slate-500">
                            <Phone class="size-3.5 text-slate-400" />
                            Hubungi Operator: <strong class="text-slate-700">{{ selectedVendor.phone }}</strong>
                        </div>
                        <button
                            type="button"
                            class="px-4 py-2 rounded-xl bg-slate-200 hover:bg-slate-300 text-xs font-bold text-slate-700 transition-colors"
                            @click="closeVendorProfile"
                        >
                            Tutup
                        </button>
                    </div>
                </div>
            </div>
        </Teleport>
    </section>
</template>
