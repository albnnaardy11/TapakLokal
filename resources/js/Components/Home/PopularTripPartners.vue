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

// 10 Curated Verified Travel Partners in Jawa Barat & Pulau Jawa
const partners = [
    {
        id: 'explorer',
        name: 'Explorer.id',
        logo: '/Assets/Images/logo-vendor/explorer.webp',
        city: 'DKI Jakarta & Jawa Barat',
        tagline: 'Platform Open Trip & Private Trip Terbesar di Indonesia',
        description: 'Operator terkurasi dari White Horse Group yang menghubungkan ribuan traveler setiap minggunya untuk destinasi hits Jawa Barat seperti Bandung, Sukabumi, Sentul, Dieng, hingga Kepulauan Seribu dengan armada pariwisata berstandar internasional.',
        rating: 4.95,
        reviewsCount: 1420,
        tripsCount: 120,
        phone: '0811-9234-567',
        types: ['open-trip', 'private-trip'],
        destinations: [
            {
                title: 'Open Trip Curug Cikaso & Ujung Genteng',
                destination: 'Sukabumi, Jawa Barat',
                type: 'open-trip',
                slug: 'curug-cikaso-ujung-genteng',
                duration: '2H 1M',
                price: 475000,
                image: 'https://images.unsplash.com/photo-1544644181-1484b3fdfc62?auto=format&fit=crop&w=600&q=80',
            },
            {
                title: 'Private Trip Kawah Putih & Glamping Ciwidey',
                destination: 'Bandung, Jawa Barat',
                type: 'private-trip',
                slug: 'ciwidey-glamping',
                duration: '1 Hari',
                price: 520000,
                image: 'https://images.unsplash.com/photo-1518548419970-58e3b4079ab2?auto=format&fit=crop&w=600&q=80',
            },
            {
                title: 'Open Trip Dieng Golden Sunrise & Candi',
                destination: 'Wonosobo, Jawa Tengah',
                type: 'open-trip',
                slug: 'dieng-sunrise',
                duration: '3H 2M',
                price: 650000,
                image: 'https://images.unsplash.com/photo-1507525428034-b723cf961d3e?auto=format&fit=crop&w=600&q=80',
            },
        ],
    },
    {
        id: 'kilikili',
        name: 'Kili Kili Adventure',
        logo: '/Assets/Images/logo-vendor/kilikili.webp',
        city: 'Jakarta, Jabar & Banten',
        tagline: 'Pelopor Open Trip: Berangkat Gak Kenal Pulang Jadi Saudara',
        description: 'Berdiri sejak 2014 dan berbadan hukum resmi PT Kili Kili Tour & Travel. Berpengalaman menangani puluhan ribu penjelajah untuk destinasi Jawa Barat, Kepulauan Seribu, Sawarna, Ujung Kulon, dan Bromo dengan dokumentasi fotografer pro.',
        rating: 4.9,
        reviewsCount: 890,
        tripsCount: 65,
        phone: '0813-8000-7711',
        types: ['open-trip'],
        destinations: [
            {
                title: 'Open Trip Pulau Harapan Island Hopping',
                destination: 'Kepulauan Seribu, Jakarta',
                type: 'open-trip',
                slug: 'pulau-harapan',
                duration: '2H 1M',
                price: 420000,
                image: 'https://images.unsplash.com/photo-1507525428034-b723cf961d3e?auto=format&fit=crop&w=600&q=80',
            },
            {
                title: 'Open Trip Pulau Peucang Taman Nasional Ujung Kulon',
                destination: 'Pandeglang, Banten',
                type: 'open-trip',
                slug: 'pulau-peucang-ujung-kulon',
                duration: '3H 2M',
                price: 780000,
                image: 'https://images.unsplash.com/photo-1518548419970-58e3b4079ab2?auto=format&fit=crop&w=600&q=80',
            },
            {
                title: 'Open Trip Pantai Sawarna Hidden Paradise',
                destination: 'Lebak, Banten',
                type: 'open-trip',
                slug: 'pantai-sawarna',
                duration: '2H 1M',
                price: 490000,
                image: 'https://images.unsplash.com/photo-1516690561799-46d8f74f9abf?auto=format&fit=crop&w=600&q=80',
            },
        ],
    },
    {
        id: 'javawisata',
        name: 'Java Wisata',
        logo: '/Assets/Images/logo-vendor/javawisata.webp',
        city: 'Bandung, Jawa Barat',
        tagline: 'Spesialis Tur Eksplorasi Priangan & Jawa Barat Sejak 2003',
        description: 'Operator wisata terverifikasi resmi ASITA & ASPPI di Bandung. Melayani ribuan perjalanan private & gathering ke seluruh destinasi unggulan Jawa Barat dengan armada terawat dan pemandu HPI bersertifikat.',
        rating: 4.92,
        reviewsCount: 460,
        tripsCount: 55,
        phone: '0811-234-5678',
        types: ['private-trip'],
        destinations: [
            {
                title: 'Private Trip Lembang Floating Market & Tangkuban Perahu',
                destination: 'Bandung Barat, Jabar',
                type: 'private-trip',
                slug: 'lembang-tangkuban-perahu',
                duration: '1 Hari',
                price: 450000,
                image: 'https://images.unsplash.com/photo-1501179691627-eeaa65ea017c?auto=format&fit=crop&w=600&q=80',
            },
            {
                title: 'Private Trip Pangalengan Tea Estate & Sunrise Cukul',
                destination: 'Pangalengan, Jawa Barat',
                type: 'private-trip',
                slug: 'pangalengan-rafting-cukul',
                duration: '1 Hari',
                price: 425000,
                image: 'https://images.unsplash.com/photo-1537996194471-e657df975ab4?auto=format&fit=crop&w=600&q=80',
            },
            {
                title: 'Private Trip Wisata Garut & Kamojang Crater',
                destination: 'Garut, Jawa Barat',
                type: 'private-trip',
                slug: 'wisata-garut-kamojang',
                duration: '2H 1M',
                price: 680000,
                image: 'https://images.unsplash.com/photo-1544644181-1484b3fdfc62?auto=format&fit=crop&w=600&q=80',
            },
        ],
    },
    {
        id: 'tourbandung',
        name: 'Tour Bandung',
        logo: '/Assets/Images/logo-vendor/tourbandung.webp',
        city: 'Bandung, Jawa Barat',
        tagline: 'Penyedia Paket Wisata Bandung, Ciwidey & Lembang Terpercaya',
        description: 'PT Tour Bandung Indonesia mengkhususkan diri pada pengalaman wisata otentik Bandung Raya. Pilihan utama untuk paket private trip keluarga, gathering, maupun open trip kebun teh dan kawah vulkanik Jawa Barat.',
        rating: 4.88,
        reviewsCount: 380,
        tripsCount: 40,
        phone: '0812-2000-8899',
        types: ['private-trip'],
        destinations: [
            {
                title: 'Private Trip Kawah Putih & Rancabali Tea Estate',
                destination: 'Ciwidey, Jawa Barat',
                type: 'private-trip',
                slug: 'kawah-putih-rancabali',
                duration: '1 Hari',
                price: 475000,
                image: 'https://images.unsplash.com/photo-1518548419970-58e3b4079ab2?auto=format&fit=crop&w=600&q=80',
            },
            {
                title: 'Private Trip Orchid Forest & Tangkuban Perahu',
                destination: 'Lembang, Jawa Barat',
                type: 'private-trip',
                slug: 'orchid-forest-lembang',
                duration: '1 Hari',
                price: 490000,
                image: 'https://images.unsplash.com/photo-1507525428034-b723cf961d3e?auto=format&fit=crop&w=600&q=80',
            },
        ],
    },
    {
        id: 'campatour',
        name: 'Campa Tour',
        logo: '/Assets/Images/logo-vendor/campatour.webp',
        city: 'Bogor & Sukabumi, Jawa Barat',
        tagline: 'Spesialis Trekking Sentul, Ekowisata & Petualangan Alam Jabar',
        description: 'PT Campa Pusaka Nusantara adalah travel operator petualangan terdepan untuk trekking keluarga di Sentul, ekspedisi Lembah Purba Situgunung, susur gua dan arung jeram Cisadane dengan standar safety internasional.',
        rating: 4.94,
        reviewsCount: 520,
        tripsCount: 48,
        phone: '0815-9988-7766',
        types: ['open-trip', 'private-trip'],
        destinations: [
            {
                title: 'Open Trip Trekking Curug Sentul & Leuwi Hejo',
                destination: 'Bogor, Jawa Barat',
                type: 'open-trip',
                slug: 'trekking-sentul-leuwi-hejo',
                duration: '1 Hari',
                price: 195000,
                image: 'https://images.unsplash.com/photo-1544644181-1484b3fdfc62?auto=format&fit=crop&w=600&q=80',
            },
            {
                title: 'Private Trip Ekspedisi Lembah Purba Situgunung',
                destination: 'Sukabumi, Jawa Barat',
                type: 'private-trip',
                slug: 'lembah-purba-situgunung',
                duration: '1 Hari',
                price: 550000,
                image: 'https://images.unsplash.com/photo-1516690561799-46d8f74f9abf?auto=format&fit=crop&w=600&q=80',
            },
            {
                title: 'Open Trip Rafting Cisadane & Fun Offroad',
                destination: 'Bogor, Jawa Barat',
                type: 'open-trip',
                slug: 'rafting-cisadane',
                duration: '1 Hari',
                price: 285000,
                image: 'https://images.unsplash.com/photo-1501179691627-eeaa65ea017c?auto=format&fit=crop&w=600&q=80',
            },
        ],
    },
    {
        id: 'labirutour',
        name: 'Labiru Tour',
        logo: '/Assets/Images/logo-vendor/labirutour.webp',
        city: 'Yogyakarta & Solo',
        tagline: 'Pemenang Penghargaan Tour Operator Terbaik Jawa Tengah & DIY',
        description: 'PT Labiru Grup Indonesia menghadirkan pengalaman private trip fleksibel bintang lima dengan konsep Mudah, Cepat, Menyenangkan di destinasi warisan budaya dan alam Yogyakarta, Borobudur, Solo, hingga Dieng Plateau.',
        rating: 4.96,
        reviewsCount: 780,
        tripsCount: 70,
        phone: '0813-9000-1122',
        types: ['private-trip'],
        destinations: [
            {
                title: 'Private Trip Borobudur Sunrise & Jeep Merapi Lava Tour',
                destination: 'Magelang & Sleman',
                type: 'private-trip',
                slug: 'borobudur-merapi',
                duration: '1 Hari',
                price: 550000,
                image: 'https://images.unsplash.com/photo-1584810359583-96fc3448beaa?auto=format&fit=crop&w=600&q=80',
            },
            {
                title: 'Private Trip Caving Goa Jomblang & Pantai Timang',
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
        id: 'rajawisata',
        name: 'Raja Wisata',
        logo: '/Assets/Images/logo-vendor/rajawisata.webp',
        city: 'DKI Jakarta & Jawa Barat',
        tagline: 'Everybody Needs Vacation: Wisata Bahari & Eksplorasi Jawa',
        description: 'PT Raja Wisata Indonesia telah melayani lebih dari 100.000 peserta sejak 2013 untuk open trip kepulauan, private gathering kantor, dan wisata pulau eksotis Jawa dengan akomodasi terbaik dan pemandu bersertifikasi.',
        rating: 4.89,
        reviewsCount: 650,
        tripsCount: 50,
        phone: '0812-8888-9900',
        types: ['open-trip'],
        destinations: [
            {
                title: 'Open Trip Pulau Pari Snorkeling & Pasir Perawan',
                destination: 'Kepulauan Seribu, Jakarta',
                type: 'open-trip',
                slug: 'pulau-pari',
                duration: '2H 1M',
                price: 395000,
                image: 'https://images.unsplash.com/photo-1507525428034-b723cf961d3e?auto=format&fit=crop&w=600&q=80',
            },
            {
                title: 'Open Trip Pulau Pramuka & Konservasi Penyu',
                destination: 'Kepulauan Seribu, Jakarta',
                type: 'open-trip',
                slug: 'pulau-pramuka',
                duration: '2H 1M',
                price: 380000,
                image: 'https://images.unsplash.com/photo-1516690561799-46d8f74f9abf?auto=format&fit=crop&w=600&q=80',
            },
        ],
    },
    {
        id: 'indonesiajuara',
        name: 'IndonesiaJuara',
        logo: '/Assets/Images/logo-vendor/indonesiajuara.webp',
        city: 'Malang & Banyuwangi',
        tagline: 'Operator Tur Petualangan Premium & Dokumentasi Sinematik',
        description: 'PT Indonesia Juara Wisata dikenal dengan pelayanan premium dan dokumentasi drone pro untuk rute ekspedisi Jawa Timur seperti Sunrise Bromo, Kawah Ijen Blue Fire, dan Air Terjun Tumpak Sewu.',
        rating: 4.97,
        reviewsCount: 820,
        tripsCount: 60,
        phone: '0811-9988-771',
        types: ['open-trip', 'private-trip'],
        destinations: [
            {
                title: 'Open Trip Bromo Midnight Sunrise Jeep 4x4',
                destination: 'Bromo, Jawa Timur',
                type: 'open-trip',
                slug: 'open-trip-bromo',
                duration: '1 Hari',
                price: 395000,
                image: 'https://images.unsplash.com/photo-1544644181-1484b3fdfc62?auto=format&fit=crop&w=600&q=80',
            },
            {
                title: 'Private Trip Kawah Ijen Blue Fire & Savana Baluran',
                destination: 'Banyuwangi, Jawa Timur',
                type: 'private-trip',
                slug: 'private-trip-kawah-ijen',
                duration: '2H 1M',
                price: 780000,
                image: 'https://images.unsplash.com/photo-1518548419970-58e3b4079ab2?auto=format&fit=crop&w=600&q=80',
            },
        ],
    },
    {
        id: 'funtrips',
        name: 'Funtrips Tour',
        logo: '/Assets/Images/logo-vendor/funtrips.webp',
        city: 'Jakarta & Jawa Barat',
        tagline: 'Penyelenggara Open Trip Terpercaya & Outing Gathering Perusahaan',
        description: 'PT Funtrips Tour and Travel fokus pada kemudahan pemesanan open trip akhir pekan dengan start poin terjangkau di Jakarta, Bekasi, dan Bandung untuk destinasi alam pulau Jawa dan pesisir nusantara.',
        rating: 4.87,
        reviewsCount: 410,
        tripsCount: 42,
        phone: '0813-1122-3344',
        types: ['open-trip'],
        destinations: [
            {
                title: 'Open Trip Sunrise Gunung Prau & Dieng',
                destination: 'Wonosobo, Jawa Tengah',
                type: 'open-trip',
                slug: 'gunung-prau-dieng',
                duration: '3H 2M',
                price: 630000,
                image: 'https://images.unsplash.com/photo-1518548419970-58e3b4079ab2?auto=format&fit=crop&w=600&q=80',
            },
            {
                title: 'Open Trip Situ Cileunca Rafting & Lakeside Camp',
                destination: 'Bandung, Jawa Barat',
                type: 'open-trip',
                slug: 'situ-cileunca-gathering',
                duration: '2H 1M',
                price: 350000,
                image: 'https://images.unsplash.com/photo-1537996194471-e657df975ab4?auto=format&fit=crop&w=600&q=80',
            },
        ],
    },
    {
        id: 'brenggo',
        name: 'BRENGGO.ID',
        logo: '/Assets/Images/logo-vendor/brenggo.webp',
        city: 'Malang, Jawa Timur',
        tagline: 'Spesialis Bromo, Kawah Ijen & Eksplorasi Eksotis Jawa Timur',
        description: 'Mitra resmi terverifikasi TapakLokal dengan armada 4x4 offroad terawat dan pemandu lokal berlisensi resmi. Melayani paket open trip Bromo midnight dan private trip Jawa Timur dengan jaminan standar kenyamanan terbaik.',
        rating: 4.93,
        reviewsCount: 390,
        tripsCount: 38,
        phone: '0812-3456-7890',
        types: ['open-trip', 'private-trip'],
        destinations: [
            {
                title: 'Open Trip Bromo Midnight Sunrise Jeep',
                destination: 'Bromo, Jawa Timur',
                type: 'open-trip',
                slug: 'open-trip-bromo-midnight',
                duration: '1 Hari',
                price: 385000,
                image: 'https://images.unsplash.com/photo-1544644181-1484b3fdfc62?auto=format&fit=crop&w=600&q=80',
            },
            {
                title: 'Private Trip Kawah Ijen Blue Fire Tour',
                destination: 'Banyuwangi, Jawa Timur',
                type: 'private-trip',
                slug: 'private-trip-kawah-ijen-brenggo',
                duration: '2H 1M',
                price: 750000,
                image: 'https://images.unsplash.com/photo-1518548419970-58e3b4079ab2?auto=format&fit=crop&w=600&q=80',
            },
        ],
    },
];

// Dynamic category counts
const counts = computed(() => ({
    all: partners.length,
    openTrip: partners.filter((p) => p.types.includes('open-trip')).length,
    privateTrip: partners.filter((p) => p.types.includes('private-trip')).length,
}));

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
        <!-- Header with Cohesive Design System Hierarchy -->
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
            <div class="inline-flex rounded-xl bg-slate-100 p-1 border border-slate-200/80 shrink-0 self-start md:self-auto shadow-xs">
                <button
                    type="button"
                    class="px-3.5 py-1.5 text-xs sm:text-sm font-semibold rounded-lg transition-all duration-200 inline-flex items-center gap-1.5 cursor-pointer"
                    :class="activeFilter === 'all' ? 'bg-white text-[#0088ff] shadow-sm font-bold scale-[1.02]' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-200/50'"
                    @click="activeFilter = 'all'"
                >
                    <Compass class="size-3.5 sm:size-4" />
                    <span>Semua ({{ counts.all }})</span>
                </button>
                <button
                    type="button"
                    class="px-3.5 py-1.5 text-xs sm:text-sm font-semibold rounded-lg transition-all duration-200 inline-flex items-center gap-1.5 cursor-pointer"
                    :class="activeFilter === 'open-trip' ? 'bg-white text-[#0088ff] shadow-sm font-bold scale-[1.02]' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-200/50'"
                    @click="activeFilter = 'open-trip'"
                >
                    <TentTree class="size-3.5 sm:size-4" />
                    <span>Open Trip ({{ counts.openTrip }})</span>
                </button>
                <button
                    type="button"
                    class="px-3.5 py-1.5 text-xs sm:text-sm font-semibold rounded-lg transition-all duration-200 inline-flex items-center gap-1.5 cursor-pointer"
                    :class="activeFilter === 'private-trip' ? 'bg-white text-[#0088ff] shadow-sm font-bold scale-[1.02]' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-200/50'"
                    @click="activeFilter = 'private-trip'"
                >
                    <Crown class="size-3.5 sm:size-4" />
                    <span>Private Trip ({{ counts.privateTrip }})</span>
                </button>
            </div>
        </div>

        <!-- 1:1 Flat Clean Logos Grid with Real Transparent .webp Vendor Logos -->
        <div class="mt-9 grid grid-cols-2 sm:grid-cols-3 md:grid-cols-5 gap-y-7 gap-x-4 sm:gap-x-6 min-h-[140px]">
            <Link
                v-for="partner in filteredPartners"
                :key="partner.id"
                :href="route('partners.show', { partner: partner.id })"
                class="group flex flex-col items-center justify-center p-3 rounded-xl transition-all duration-200 hover:bg-slate-100/70 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-[#3E7BEF] text-center animate-in fade-in zoom-in-95 duration-200 cursor-pointer"
            >
                <!-- Clean Logo Emblem Graphic Container with Optical Balance -->
                <div class="h-10 sm:h-12 w-full flex items-center justify-center transition-transform duration-200 group-hover:scale-105">
                    <img
                        :src="partner.logo"
                        :alt="partner.name"
                        class="h-8 sm:h-9 max-w-[115px] w-auto object-contain filter drop-shadow-xs transition-opacity duration-200"
                        loading="lazy"
                    />
                </div>

                <!-- Brand Name Below Logo -->
                <span class="mt-2 text-xs sm:text-[13px] font-medium text-slate-700 tracking-tight transition-colors group-hover:text-[#3E7BEF]">
                    {{ partner.name }}
                </span>
            </Link>
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
                            <!-- Logo Avatar with Real WebP Logo -->
                            <div class="grid size-14 place-items-center rounded-2xl bg-white border border-slate-200/90 shadow-xs p-2">
                                <img
                                    :src="selectedVendor.logo"
                                    :alt="selectedVendor.name"
                                    class="h-9 w-auto max-w-full object-contain"
                                />
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
                            <span class="inline-flex items-center gap-1 text-xs text-slate-500 font-medium ml-auto">
                                <Star class="size-3.5 fill-amber-400 text-amber-400" />
                                <strong class="text-slate-800 font-bold">{{ selectedVendor.rating }}</strong>
                                ({{ selectedVendor.reviewsCount }} ulasan)
                            </span>
                        </div>

                        <!-- Tagline & Description -->
                        <div class="rounded-2xl bg-slate-50 p-4 border border-slate-200/60">
                            <p class="text-xs font-bold text-[#0088ff] uppercase tracking-wider">
                                {{ selectedVendor.tagline }}
                            </p>
                            <p class="mt-1.5 text-xs sm:text-sm text-slate-600 leading-relaxed">
                                {{ selectedVendor.description }}
                            </p>
                        </div>

                        <!-- Trip Offerings List -->
                        <div>
                            <div class="flex items-center justify-between mb-3">
                                <h4 class="text-sm font-bold text-slate-900">
                                    Paket Trip Tersedia ({{ selectedVendor.destinations.length }})
                                </h4>
                                <span class="text-xs text-slate-400 font-medium">Jadwal Reguler & Private Charter</span>
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                <div
                                    v-for="dest in selectedVendor.destinations"
                                    :key="dest.slug"
                                    class="group/item flex flex-col rounded-2xl border border-slate-200/80 overflow-hidden bg-white hover:border-[#0088ff]/40 hover:shadow-md transition-all duration-200"
                                >
                                    <!-- Image thumbnail -->
                                    <div class="relative h-28 w-full overflow-hidden bg-slate-100">
                                        <img
                                            :src="dest.image"
                                            :alt="dest.title"
                                            class="size-full object-cover transition-transform duration-300 group-hover/item:scale-105"
                                        />
                                        <span
                                            class="absolute top-2 left-2 rounded-md px-2 py-0.5 text-[10px] font-bold text-white shadow-xs"
                                            :class="dest.type === 'open-trip' ? 'bg-[#0088ff]' : 'bg-emerald-600'"
                                        >
                                            {{ dest.type === 'open-trip' ? 'Open Trip' : 'Private Trip' }}
                                        </span>
                                        <span class="absolute bottom-2 right-2 rounded-md bg-black/60 backdrop-blur-xs px-1.5 py-0.5 text-[10px] font-semibold text-white inline-flex items-center gap-1">
                                            <Clock class="size-2.5" />
                                            {{ dest.duration }}
                                        </span>
                                    </div>

                                    <!-- Content -->
                                    <div class="p-3.5 flex flex-col flex-1 justify-between">
                                        <div>
                                            <p class="text-xs text-slate-400 font-medium line-clamp-1">
                                                {{ dest.destination }}
                                            </p>
                                            <h5 class="mt-0.5 text-xs sm:text-sm font-bold text-slate-900 line-clamp-1 group-hover/item:text-[#0088ff] transition-colors">
                                                {{ dest.title }}
                                            </h5>
                                        </div>

                                        <div class="mt-3 pt-2 border-t border-slate-100 flex items-center justify-between">
                                            <div>
                                                <span class="text-[10px] text-slate-400 block">Mulai dari</span>
                                                <span class="text-xs sm:text-sm font-extrabold text-[#0088ff]">
                                                    {{ formatPrice(dest.price) }}
                                                </span>
                                            </div>
                                            <Link
                                                :href="route('catalog.index')"
                                                class="rounded-lg bg-sky-50 px-2.5 py-1 text-xs font-bold text-[#0088ff] hover:bg-[#0088ff] hover:text-white transition-colors inline-flex items-center gap-1"
                                            >
                                                Pilih
                                                <ArrowRight class="size-3" />
                                            </Link>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Modal Footer -->
                    <div class="p-4 sm:p-5 border-t border-slate-100 bg-slate-50/80 flex flex-col sm:flex-row items-center justify-between gap-3">
                        <div class="flex items-center gap-2 text-xs text-slate-500">
                            <ShieldCheck class="size-4 text-emerald-500 shrink-0" />
                            <span>Jaminan Operator Terverifikasi & Transaksi Aman TapakLokal</span>
                        </div>
                        <div class="flex items-center gap-2 w-full sm:w-auto">
                            <a
                                :href="`https://wa.me/${selectedVendor.phone.replace(/[^0-9]/g, '')}`"
                                target="_blank"
                                rel="noopener noreferrer"
                                class="flex-1 sm:flex-none inline-flex items-center justify-center gap-1.5 rounded-xl border border-slate-300 bg-white px-4 py-2 text-xs font-bold text-slate-700 hover:bg-slate-50 transition-colors"
                            >
                                <Phone class="size-3.5" />
                                Chat Admin
                            </a>
                            <Link
                                :href="route('catalog.index')"
                                class="flex-1 sm:flex-none inline-flex items-center justify-center gap-1.5 rounded-xl bg-[#0088ff] px-4 py-2 text-xs font-bold text-white hover:bg-[#0077e6] shadow-xs transition-colors"
                            >
                                Lihat Semua Trip
                                <ArrowRight class="size-3.5" />
                            </Link>
                        </div>
                    </div>
                </div>
            </div>
        </Teleport>
    </section>
</template>