<script setup>
import { computed, ref, watch } from 'vue';
import { Link } from '@inertiajs/vue3';
import { route } from 'ziggy-js';
import {
    ArrowRight,
    Award,
    Calendar,
    Camera,
    Check,
    ChevronDown,
    ChevronLeft,
    ChevronRight,
    Clock,
    Compass,
    Copy,
    Crown,
    ExternalLink,
    Flame,
    Gift,
    Grid,
    Heart,
    HelpCircle,
    Info,
    LayoutList,
    Map,
    MapPin,
    RotateCcw,
    Shield,
    ShieldCheck,
    SlidersHorizontal,
    Sparkles,
    Star,
    Tag,
    Ticket,
    Truck,
    User,
    Users,
    Utensils,
    Waves,
    X,
    Zap,
} from 'lucide-vue-next';
import CatalogPromoSection from './CatalogPromoSection.vue';

const props = defineProps({
    trips: {
        type: Object,
        default: () => ({ data: [] }),
    },
    initialFilters: {
        type: Object,
        default: () => ({}),
    },
    partner: {
        type: Object,
        default: () => null,
    },
});

const emit = defineEmits(['filter-change']);

// Curated Indonesian Trip Catalog Data
const curatedTripsData = [
    {
        id: 'komodo-phinisi',
        slug: 'sailing-komodo-3d2n-phinisi-superior',
        title: 'Sailing Komodo 3D2N Phinisi Superior - Labuan Bajo',
        type: 'open-trip',
        categoryName: 'Open Trip',
        destination: 'Labuan Bajo, Nusa Tenggara Timur',
        meetingPoint: 'Bandara Komodo / Pelabuhan Marina',
        duration: '3 Hari 2 Malam (3H2M)',
        durationCode: '3d2n',
        price: 2450000,
        originalPrice: 2850000,
        rating: 8.8,
        ratingLabel: 'Sangat Bagus',
        reviewCount: 1240,
        bookedThisWeek: 24,
        isPreferred: true,
        instantConfirmation: true,
        freeReschedule: true,
        stars: 5,
        facilities: ['Transport AC', 'Kapal Phinisi AC', 'Makan 3x Sehari', 'Free Drone & GoPro', 'Alat Snorkeling'],
        promoCode: 'TAPAKLOKAL',
        promoBadge: 'Diskon 8% Pengguna Baru! Gunakan kode: TAPAKLOKAL',
        reviewSnippet: {
            author: 'F***n',
            badge: 'Verified Traveler',
            quote: 'Kapal phinisinya sangat bersih dan nyaman. Makanan lezat, guide sangat ramah dan hasil video drone-nya estetik banget!',
        },
        images: [
            'https://images.unsplash.com/photo-1516426122078-c23e76319801?auto=format&fit=crop&w=800&q=80',
            'https://images.unsplash.com/photo-1507525428034-b723cf961d3e?auto=format&fit=crop&w=800&q=80',
            'https://images.unsplash.com/photo-1544644181-1484b3fdfc62?auto=format&fit=crop&w=800&q=80',
        ],
    },
    {
        id: 'bromo-midnight',
        slug: 'bromo-midnight-sunrise-jeep-4x4',
        title: 'Bromo Midnight Sunrise & Pasir Berbisik Jeep 4x4',
        type: 'open-trip',
        categoryName: 'Open Trip',
        destination: 'Bromo, Probolinggo & Malang',
        meetingPoint: 'Stasiun Malang Kota / Surabaya Gubeng',
        duration: '1 Hari (Midnight Tour)',
        durationCode: '1d',
        price: 350000,
        originalPrice: 420000,
        rating: 8.6,
        ratingLabel: 'Sangat Bagus',
        reviewCount: 3150,
        bookedThisWeek: 58,
        isPreferred: true,
        instantConfirmation: true,
        freeReschedule: false,
        stars: 4,
        facilities: ['Jeep Hardtop 4x4', 'Tiket Masuk Bromo', 'Driver & Guide', 'Dokumentasi Foto', 'Air Mineral'],
        promoCode: 'BROMOSERU',
        promoBadge: 'Flash Sale Weekend! Potongan langsung Rp 70.000',
        reviewSnippet: {
            author: 'R***a',
            badge: 'Solo Traveler',
            quote: 'Driver ramah dan on time, spot sunrise Kingkong Hill dapet angle terbaik tanpa desak-desakan. Pasir berbisik sangat magis!',
        },
        images: [
            'https://images.unsplash.com/photo-1588668214407-6ea9a6d8c272?auto=format&fit=crop&w=800&q=80',
            'https://images.unsplash.com/photo-1518548419970-58e3b4079ab2?auto=format&fit=crop&w=800&q=80',
        ],
    },
    {
        id: 'nusa-penida-west',
        slug: 'nusa-penida-1-day-tour-west-snorkeling',
        title: 'Nusa Penida 1 Day Tour Barat & Snorkeling Manta Bay',
        type: 'open-trip',
        categoryName: 'Open Trip',
        destination: 'Nusa Penida, Bali',
        meetingPoint: 'Pelabuhan Sanur, Denpasar Bali',
        duration: '1 Hari (Day Trip)',
        durationCode: '1d',
        price: 490000,
        originalPrice: 580000,
        rating: 8.7,
        ratingLabel: 'Sangat Bagus',
        reviewCount: 2420,
        bookedThisWeek: 42,
        isPreferred: true,
        instantConfirmation: true,
        freeReschedule: true,
        stars: 4,
        facilities: ['Fastboat PP Sanur', 'Mobil AC di Penida', 'Makan Siang Resto', 'Boat & Alat Snorkeling', 'Tiket Wisata'],
        promoCode: 'BALIHEBAT',
        promoBadge: 'Spesial Liburan Domestik! Cashback 10% TapakPoints',
        reviewSnippet: {
            author: 'D***y',
            badge: 'Verified Traveler',
            quote: 'Beruntung banget ketemu 3 ekor ikan pari manta pas snorkeling! Tour guidenya jago fotoin spot Kelingking Beach.',
        },
        images: [
            'https://images.unsplash.com/photo-1537996194471-e657df975ab4?auto=format&fit=crop&w=800&q=80',
            'https://images.unsplash.com/photo-1544644181-1484b3fdfc62?auto=format&fit=crop&w=800&q=80',
        ],
    },
    {
        id: 'dieng-sikunir-private',
        slug: 'private-trip-dieng-golden-sunrise-sikunir',
        title: 'Private Trip Dieng Golden Sunrise Sikunir & Kawah Sikidang',
        type: 'private-trip',
        categoryName: 'Private Trip',
        destination: 'Dieng Plateau, Wonosobo',
        meetingPoint: 'Stasiun Purwokerto / Yogyakarta / Semarang',
        duration: '2 Hari 1 Malam (2H1M)',
        durationCode: '2d1n',
        price: 890000,
        originalPrice: 1150000,
        rating: 8.9,
        ratingLabel: 'Luar Biasa',
        reviewCount: 890,
        bookedThisWeek: 16,
        isPreferred: true,
        instantConfirmation: false,
        freeReschedule: true,
        stars: 5,
        facilities: ['Mobil Private All-in', 'Homestay Water Heater', 'Makan 4x Khas Dieng', 'Tiket Sunrise Sikunir', 'Local Guide'],
        promoCode: 'DIENGPRIVATE',
        promoBadge: 'Free Upgrade Kamar Homestay Deluxe untuk Keluarga',
        reviewSnippet: {
            author: 'H***i',
            badge: 'Family Traveler',
            quote: 'Private trip keluarga sangat nyaman. Mobil bersih, driver sopan dan waktu trip fleksibel bisa request berhenti foto di mana aja.',
        },
        images: [
            'https://images.unsplash.com/photo-1578632767115-351597cf2477?auto=format&fit=crop&w=800&q=80',
            'https://images.unsplash.com/photo-1507525428034-b723cf961d3e?auto=format&fit=crop&w=800&q=80',
        ],
    },
    {
        id: 'derawan-whale-shark',
        slug: 'derawan-paradise-island-hopping-whale-shark',
        title: 'Derawan Paradise Island Hopping & Whale Shark Talisayan',
        type: 'open-trip',
        categoryName: 'Open Trip',
        destination: 'Kepulauan Derawan, Kalimantan Timur',
        meetingPoint: 'Bandara Kalimarau, Berau',
        duration: '4 Hari 3 Malam (4H3M)',
        durationCode: '4d3n',
        price: 2350000,
        originalPrice: 2700000,
        rating: 9.1,
        ratingLabel: 'Luar Biasa',
        reviewCount: 650,
        bookedThisWeek: 19,
        isPreferred: true,
        instantConfirmation: true,
        freeReschedule: true,
        stars: 5,
        facilities: ['Speedboat Private Hopping', 'Water Villa Homestay', 'Makan 9x', 'Renang Bareng Whale Shark', 'Foto Underwater'],
        promoCode: 'DERAWANTRIP',
        promoBadge: 'Diskon 8% Pengguna Baru! Gunakan kode: TAPAKLOKAL',
        reviewSnippet: {
            author: 'B***o',
            badge: 'Verified Traveler',
            quote: 'Pengalaman seumur hidup berenang bareng hiu paus dan ubur-ubur tanpa sengat di Kakaban. Air lautnya sejernih kaca!',
        },
        images: [
            'https://images.unsplash.com/photo-1544551763-46a013bb70d5?auto=format&fit=crop&w=800&q=80',
            'https://images.unsplash.com/photo-1507525428034-b723cf961d3e?auto=format&fit=crop&w=800&q=80',
        ],
    },
    {
        id: 'bandung-ciwidey-private',
        slug: 'private-trip-bandung-ciwidey-kawah-putih',
        title: 'Private Trip Eksplorasi Lembang & Ciwidey Kawah Putih',
        type: 'private-trip',
        categoryName: 'Private Trip',
        destination: 'Bandung & Ciwidey, Jawa Barat',
        meetingPoint: 'Stasiun KCIC Whoosh Padalarang / Bandung Kota',
        duration: '2 Hari 1 Malam (2H1M)',
        durationCode: '2d1n',
        price: 750000,
        originalPrice: 950000,
        rating: 8.5,
        ratingLabel: 'Sangat Bagus',
        reviewCount: 1560,
        bookedThisWeek: 35,
        isPreferred: true,
        instantConfirmation: true,
        freeReschedule: true,
        stars: 4,
        facilities: ['Mobil Innova Reborn AC', 'Tiket Kawah Putih & Orchid', 'Makan Siang Sunda', 'Driver & Guide', 'BBM + Tol Parkir'],
        promoCode: 'JABARSERU',
        promoBadge: 'Penawaran Spesial Group Outing: Diskon Rp 150.000',
        reviewSnippet: {
            author: 'S***a',
            badge: 'Verified Traveler',
            quote: 'Sangat cocok untuk rombongan keluarga. Driver tahu jalan alternatif bebas macet di Lembang dan spot kuliner hidden gem!',
        },
        images: [
            'https://images.unsplash.com/photo-1506744038136-46273834b3fb?auto=format&fit=crop&w=800&q=80',
            'https://images.unsplash.com/photo-1464822759023-fed622ff2c3b?auto=format&fit=crop&w=800&q=80',
        ],
    },
    {
        id: 'raja-ampat-pianemo',
        slug: 'raja-ampat-wayag-pianemo-4d3n-eksklusif',
        title: 'Raja Ampat Wayag & Pianemo 4D3N Pesona Surga Timur',
        type: 'open-trip',
        categoryName: 'Open Trip',
        destination: 'Raja Ampat, Papua Barat Daya',
        meetingPoint: 'Pelabuhan Sorong / Bandara DEO',
        duration: '4 Hari 3 Malam (4H3M)',
        durationCode: '4d3n',
        price: 4950000,
        originalPrice: 5800000,
        rating: 9.3,
        ratingLabel: 'Luar Biasa',
        reviewCount: 410,
        bookedThisWeek: 12,
        isPreferred: true,
        instantConfirmation: false,
        freeReschedule: true,
        stars: 5,
        facilities: ['Speedboat Khusus Raja Ampat', 'Resort Terapung', 'Makan 3x Sehari', 'PIN Raja Ampat', 'Dokumentasi Drone & DSLR'],
        promoCode: 'SURGAPAPUA',
        promoBadge: 'Bonus Free TapakLokal Travel Kit & Drybag 15L',
        reviewSnippet: {
            author: 'M***a',
            badge: 'Verified Traveler',
            quote: 'Gak ada kata lain selain SURGA! View puncak Wayag dan Pianemo beneran magis. Guide lokal sangat profesional dan menjaga alam.',
        },
        images: [
            'https://images.unsplash.com/photo-1516426122078-c23e76319801?auto=format&fit=crop&w=800&q=80',
            'https://images.unsplash.com/photo-1544644181-1484b3fdfc62?auto=format&fit=crop&w=800&q=80',
        ],
    },
    {
        id: 'kepulauan-seribu-pramuka',
        slug: 'open-trip-pulau-pramuka-harapan-snorkeling',
        title: 'Pulau Pramuka & Pulau Harapan Snorkeling Bahari Seribu',
        type: 'open-trip',
        categoryName: 'Open Trip',
        destination: 'Kepulauan Seribu, DKI Jakarta',
        meetingPoint: 'Dermaga Muara Angke / Marina Ancol',
        duration: '2 Hari 1 Malam (2H1M)',
        durationCode: '2d1n',
        price: 380000,
        originalPrice: 450000,
        rating: 8.4,
        ratingLabel: 'Sangat Bagus',
        reviewCount: 2890,
        bookedThisWeek: 64,
        isPreferred: false,
        instantConfirmation: true,
        freeReschedule: true,
        stars: 4,
        facilities: ['Kapal PP Muara Angke', 'Homestay AC', 'Makan 3x Prasmanan', 'Kapal Jelajah & Snorkeling', 'BBQ Ikan Laut'],
        promoCode: 'PULAUSERIBU',
        promoBadge: 'Paket Hemat Akhir Pekan Bareng Sahabat & Komunitas',
        reviewSnippet: {
            author: 'A***f',
            badge: 'Traveler',
            quote: 'Trip hemat dekat Jakarta yang sangat seru. Sunset di pulau tak berpenghuni dan penangkaran penyu sangat berkesan!',
        },
        images: [
            'https://images.unsplash.com/photo-1507525428034-b723cf961d3e?auto=format&fit=crop&w=800&q=80',
            'https://images.unsplash.com/photo-1464822759023-fed622ff2c3b?auto=format&fit=crop&w=800&q=80',
        ],
    },
    {
        id: 'sumba-wairinding',
        slug: 'sailing-overland-sumba-bukit-wairinding',
        title: 'Sailing & Overland Sumba 4D3N Bukit Wairinding & Weekuri Lagoon',
        type: 'open-trip',
        categoryName: 'Open Trip',
        destination: 'Sumba Timur & Barat, NTT',
        meetingPoint: 'Bandara Tambolaka (TMC) / Waingapu',
        duration: '4 Hari 3 Malam (4H3M)',
        durationCode: '4d3n',
        price: 2750000,
        originalPrice: 3200000,
        rating: 9.2,
        ratingLabel: 'Luar Biasa',
        reviewCount: 520,
        bookedThisWeek: 21,
        isPreferred: true,
        instantConfirmation: true,
        freeReschedule: true,
        stars: 5,
        facilities: ['Transport AC 4WD', 'Hotel Resort Bintang', 'Makan 3x Sehari', 'Tiket Adat Kampung Ratenggaro', 'Dokumentasi Drone'],
        promoCode: 'SUMBAEKSOTIS',
        promoBadge: 'Best Seller Overland NTT! Cashback Rp 100.000',
        reviewSnippet: {
            author: 'N***a',
            badge: 'Verified Traveler',
            quote: 'Sunset di Bukit Wairinding dan air jernih Danau Weekuri tak terlupakan seumur hidup. Guidenya mantap!',
        },
        images: [
            'https://images.unsplash.com/photo-1507525428034-b723cf961d3e?auto=format&fit=crop&w=800&q=80',
            'https://images.unsplash.com/photo-1518548419970-58e3b4079ab2?auto=format&fit=crop&w=800&q=80',
        ],
    },
    {
        id: 'karimunjawa-paradise',
        slug: 'open-trip-karimunjawa-paradise-snorkeling-hiu',
        title: 'Karimunjawa Island Paradise 3D2N Snorkeling Hiu & Sunset Tanjung Gelam',
        type: 'open-trip',
        categoryName: 'Open Trip',
        destination: 'Kepulauan Karimunjawa, Jawa Tengah',
        meetingPoint: 'Pelabuhan Kartini, Jepara',
        duration: '3 Hari 2 Malam (3H2M)',
        durationCode: '3d2n',
        price: 850000,
        originalPrice: 1050000,
        rating: 8.7,
        ratingLabel: 'Sangat Bagus',
        reviewCount: 1840,
        bookedThisWeek: 45,
        isPreferred: true,
        instantConfirmation: true,
        freeReschedule: true,
        stars: 4,
        facilities: ['Tiket Express Bahari PP', 'Homestay / Hotel AC', 'Makan Prasmanan BBQ Ikan', 'Boat Snorkeling & Alat', 'Dokumentasi Underwater'],
        promoCode: 'KARIMUNHEMAT',
        promoBadge: 'Free BBQ Ikan Bakar di Pulau Cilik & Penangkaran Hiu',
        reviewSnippet: {
            author: 'K***n',
            badge: 'Verified Traveler',
            quote: 'Berenang bareng hiu di kolam penangkaran seru banget. Ikan bakar di pantai tak berpenghuni gurih mantap.',
        },
        images: [
            'https://images.unsplash.com/photo-1544551763-46a013bb70d5?auto=format&fit=crop&w=800&q=80',
            'https://images.unsplash.com/photo-1516426122078-c23e76319801?auto=format&fit=crop&w=800&q=80',
        ],
    },
    {
        id: 'kawah-ijen-baluran',
        slug: 'private-trip-kawah-ijen-blue-fire-baluran',
        title: 'Private Trip Kawah Ijen Blue Fire & Baluran Africa Van Java 2D1N',
        type: 'private-trip',
        categoryName: 'Private Trip',
        destination: 'Banyuwangi, Jawa Timur',
        meetingPoint: 'Stasiun Ketapang / Bandara Banyuwangi',
        duration: '2 Hari 1 Malam (2H1M)',
        durationCode: '2d1n',
        price: 650000,
        originalPrice: 800000,
        rating: 8.9,
        ratingLabel: 'Luar Biasa',
        reviewCount: 960,
        bookedThisWeek: 31,
        isPreferred: true,
        instantConfirmation: true,
        freeReschedule: true,
        stars: 5,
        facilities: ['Mobil Private All In', 'Masker Gas Khusus Ijen', 'Guide Lokal Berlisensi', 'Tiket Masuk Ijen & Baluran', 'Air Mineral & Snack'],
        promoCode: 'IJENSERU',
        promoBadge: 'Spesial Liburan Keluarga & Sahabat Diskon 10%',
        reviewSnippet: {
            author: 'G***g',
            badge: 'Adventure Seeker',
            quote: 'Api biru Kawah Ijen terlihat jelas jam 3 subuh! Savana Bekol di Baluran beneran berasa lagi di savana Afrika.',
        },
        images: [
            'https://images.unsplash.com/photo-1588668214407-6ea9a6d8c272?auto=format&fit=crop&w=800&q=80',
            'https://images.unsplash.com/photo-1464822759023-fed622ff2c3b?auto=format&fit=crop&w=800&q=80',
        ],
    },
    {
        id: 'belitung-laskar-pelangi',
        slug: 'open-trip-belitung-laskar-pelangi-island-hopping',
        title: 'Belitung Laskar Pelangi 3D2N Island Hopping Batu Garuda & Lengkuas',
        type: 'open-trip',
        categoryName: 'Open Trip',
        destination: 'Belitung, Kepulauan Bangka Belitung',
        meetingPoint: 'Bandara H.A.S. Hanandjoeddin, Tanjung Pandan',
        duration: '3 Hari 2 Malam (3H2M)',
        durationCode: '3d2n',
        price: 1150000,
        originalPrice: 1400000,
        rating: 8.8,
        ratingLabel: 'Sangat Bagus',
        reviewCount: 1420,
        bookedThisWeek: 28,
        isPreferred: true,
        instantConfirmation: true,
        freeReschedule: true,
        stars: 4,
        facilities: ['Transport AC Selama Trip', 'Hotel Bintang 3', 'Traditional Boat Hopping', 'Mie Belitung & Es Jeruk Kunci', 'Life Jacket & Snorkel'],
        promoCode: 'BELITUNGASIK',
        promoBadge: 'Free Kunjungan Museum Kata Andrea Hirata & Danau Kaolin',
        reviewSnippet: {
            author: 'E***a',
            badge: 'Verified Traveler',
            quote: 'Batu granit raksasa di Pantai Tanjung Tinggi beneran megah. Mercusuar Pulau Lengkuas view lautnya juara!',
        },
        images: [
            'https://images.unsplash.com/photo-1507525428034-b723cf961d3e?auto=format&fit=crop&w=800&q=80',
            'https://images.unsplash.com/photo-1544551763-46a013bb70d5?auto=format&fit=crop&w=800&q=80',
        ],
    },
    {
        id: 'pahawang-nemo',
        slug: 'open-trip-pahawang-island-snorkeling-nemo',
        title: 'Open Trip Pulau Pahawang 3D2N Snorkeling Nemo & Pasir Timbul',
        type: 'open-trip',
        categoryName: 'Open Trip',
        destination: 'Pulau Pahawang, Lampung Selatan',
        meetingPoint: 'Pelabuhan Merak / Dermaga Ketapang Lampung',
        duration: '3 Hari 2 Malam (3H2M)',
        durationCode: '3d2n',
        price: 580000,
        originalPrice: 720000,
        rating: 8.5,
        ratingLabel: 'Sangat Bagus',
        reviewCount: 2150,
        bookedThisWeek: 50,
        isPreferred: false,
        instantConfirmation: true,
        freeReschedule: true,
        stars: 4,
        facilities: ['Kapal Ferry Merak-Bakauheni', 'Homestay Terapung', 'Makan 4x BBQ', 'Kapal Jelajah Pulau', 'Foto Underwater Bareng Nemo'],
        promoCode: 'PAHAWANGTOP',
        promoBadge: 'Trip Favorit Weekend Murah & Seru dari Jakarta',
        reviewSnippet: {
            author: 'T***o',
            badge: 'Weekend Explorer',
            quote: 'Spot underwater Cukuh Bedil banyak ikan nemo gemas. Pasir Timbul view laut gradasi birunya estetik banget.',
        },
        images: [
            'https://images.unsplash.com/photo-1544644181-1484b3fdfc62?auto=format&fit=crop&w=800&q=80',
            'https://images.unsplash.com/photo-1516426122078-c23e76319801?auto=format&fit=crop&w=800&q=80',
        ],
    },
    {
        id: 'banda-neira-heritage',
        slug: 'open-trip-eksplorasi-banda-neira-benteng-belgica',
        title: 'Ekspedisi Sejarah Banda Neira 5D4N Benteng Belgica & Lava Flow Snorkeling',
        type: 'open-trip',
        categoryName: 'Open Trip',
        destination: 'Kepulauan Banda, Maluku Tengah',
        meetingPoint: 'Pelabuhan Tulehu / Bandara Pattimura Ambon',
        duration: '5 Hari 4 Malam (5H4M)',
        durationCode: '5d4n',
        price: 3850000,
        originalPrice: 4500000,
        rating: 9.4,
        ratingLabel: 'Luar Biasa',
        reviewCount: 310,
        bookedThisWeek: 9,
        isPreferred: true,
        instantConfirmation: false,
        freeReschedule: true,
        stars: 5,
        facilities: ['Tiket Kapal Cepat Ambon-Banda PP', 'Penginapan Heritage', 'Makan 3x Khas Banda Pala', 'Tour Guide Sejarawan', 'Full Snorkeling Gear'],
        promoCode: 'BANDANEIRA',
        promoBadge: 'Pengalaman Bersejarah Terbaik Jalur Rempah Dunia',
        reviewSnippet: {
            author: 'W***y',
            badge: 'Heritage Traveler',
            quote: '"Jangan mati sebelum ke Banda Neira" - beneran nyata indahnya. Karang di spot Lava Flow paling sehat di dunia!',
        },
        images: [
            'https://images.unsplash.com/photo-1506744038136-46273834b3fb?auto=format&fit=crop&w=800&q=80',
            'https://images.unsplash.com/photo-1516426122078-c23e76319801?auto=format&fit=crop&w=800&q=80',
        ],
    },
    {
        id: 'toraja-heritage',
        slug: 'private-trip-tana-toraja-cultural-heritage',
        title: 'Private Trip Tana Toraja Cultural Heritage & Negeri di Atas Awan Lolai 3D2N',
        type: 'private-trip',
        categoryName: 'Private Trip',
        destination: 'Tana Toraja, Sulawesi Selatan',
        meetingPoint: 'Bandara Sultan Hasanuddin Makassar / Bandara Toraja',
        duration: '3 Hari 2 Malam (3H2M)',
        durationCode: '3d2n',
        price: 1850000,
        originalPrice: 2200000,
        rating: 9.0,
        ratingLabel: 'Luar Biasa',
        reviewCount: 480,
        bookedThisWeek: 14,
        isPreferred: true,
        instantConfirmation: true,
        freeReschedule: true,
        stars: 5,
        facilities: ['Mobil Private Innova AC', 'Hotel Heritage Rantepao', 'Makan Masakan Khas Toraja', 'Tiket Adat Kete Kesu & Londa', 'Local Guide Toraja'],
        promoCode: 'TORAJAJUARA',
        promoBadge: 'Eksplorasi Budaya Tradisi Tertua di Dunia',
        reviewSnippet: {
            author: 'V***i',
            badge: 'Culture Enthusiast',
            quote: 'Sunrise di Lolai negeri di atas awan luar biasa magis. Rumah adat Tongkonan di Kete Kesu terawat sangat megah.',
        },
        images: [
            'https://images.unsplash.com/photo-1464822759023-fed622ff2c3b?auto=format&fit=crop&w=800&q=80',
            'https://images.unsplash.com/photo-1507525428034-b723cf961d3e?auto=format&fit=crop&w=800&q=80',
        ],
    },
    {
        id: 'wakatobi-marine',
        slug: 'open-trip-wakatobi-marine-biosphere-snorkeling',
        title: 'Wakatobi Marine Biosphere 4D3N Pulau Wangi-Wangi & Dolphin Watching',
        type: 'open-trip',
        categoryName: 'Open Trip',
        destination: 'Wakatobi, Sulawesi Tenggara',
        meetingPoint: 'Bandara Matahora (WNI), Wangi-Wangi',
        duration: '4 Hari 3 Malam (4H3M)',
        durationCode: '4d3n',
        price: 2950000,
        originalPrice: 3500000,
        rating: 9.3,
        ratingLabel: 'Luar Biasa',
        reviewCount: 390,
        bookedThisWeek: 11,
        isPreferred: true,
        instantConfirmation: true,
        freeReschedule: true,
        stars: 5,
        facilities: ['Boat Jelajah Hoga & Tomia', 'Resort Tepi Pantai', 'Makan Seafood Segar 3x', 'Alat Snorkeling Pro', 'Dokumentasi Drone & Underwater'],
        promoCode: 'WAKATOBIINDOPREMIUM',
        promoBadge: 'Surga Karang Terumbu Terbaik Segitiga Karang Dunia',
        reviewSnippet: {
            author: 'L***n',
            badge: 'Diver & Snorkeler',
            quote: 'Bisa lihat puluhan lumba-lumba liar melompat dekat perahu pas sunrise. Visibilitas air laut tembus hingga 25 meter!',
        },
        images: [
            'https://images.unsplash.com/photo-1544551763-46a013bb70d5?auto=format&fit=crop&w=800&q=80',
            'https://images.unsplash.com/photo-1537996194471-e657df975ab4?auto=format&fit=crop&w=800&q=80',
        ],
    },
    {
        id: 'green-canyon-pangandaran',
        slug: 'open-trip-green-canyon-body-rafting-pangandaran',
        title: 'Open Trip Green Canyon Body Rafting & Pantai Batu Karas 2D1N',
        type: 'open-trip',
        categoryName: 'Open Trip',
        destination: 'Pangandaran & Ciamis, Jawa Barat',
        meetingPoint: 'Plaza Semanggi Jakarta / Bandung Pasteur',
        duration: '2 Hari 1 Malam (2H1M)',
        durationCode: '2d1n',
        price: 420000,
        originalPrice: 520000,
        rating: 8.6,
        ratingLabel: 'Sangat Bagus',
        reviewCount: 1650,
        bookedThisWeek: 38,
        isPreferred: false,
        instantConfirmation: true,
        freeReschedule: true,
        stars: 3,
        facilities: ['Transport Bus Pariwisata AC', 'Homestay AC', 'Full Body Rafting Green Canyon', 'Makan 3x Khas Sunda', 'Instruktur & Lifeguard'],
        promoCode: 'GREENCANYON',
        promoBadge: 'Sensasi Body Rafting Arung Jeram Air Sebening Zamrud',
        reviewSnippet: {
            author: 'C***a',
            badge: 'Adventure Squad',
            quote: 'Body rafting sepanjang 3 km airnya jernih banget dan sejuk diapit tebing batu alam lumut. Seru banget!',
        },
        images: [
            'https://images.unsplash.com/photo-1506744038136-46273834b3fb?auto=format&fit=crop&w=800&q=80',
            'https://images.unsplash.com/photo-1518548419970-58e3b4079ab2?auto=format&fit=crop&w=800&q=80',
        ],
    },
    {
        id: 'likupang-bunaken',
        slug: 'private-trip-likupang-bunaken-manado',
        title: 'Private Trip Likupang DPSP & Taman Nasional Bunaken Manado 3D2N',
        type: 'private-trip',
        categoryName: 'Private Trip',
        destination: 'Manado & Likupang, Sulawesi Utara',
        meetingPoint: 'Bandara Sam Ratulangi Manado',
        duration: '3 Hari 2 Malam (3H2M)',
        durationCode: '3d2n',
        price: 2150000,
        originalPrice: 2600000,
        rating: 9.0,
        ratingLabel: 'Luar Biasa',
        reviewCount: 430,
        bookedThisWeek: 15,
        isPreferred: true,
        instantConfirmation: true,
        freeReschedule: true,
        stars: 5,
        facilities: ['Mobil Private All In', 'Speedboat Bunaken PP', 'Hotel Bintang 4 Manado', 'Kuliner Ikan Bakar Rica & Tinutuan', 'Guide Lokal Berlisensi'],
        promoCode: 'LIKUPANGSERU',
        promoBadge: 'Destinasi Pariwisata Super Prioritas Sulawesi Utara',
        reviewSnippet: {
            author: 'O***n',
            badge: 'Verified Traveler',
            quote: 'Dinding karang Bunaken beneran spektakuler dengan penyu-penyu raksasa yang berenang santai di samping kita.',
        },
        images: [
            'https://images.unsplash.com/photo-1516426122078-c23e76319801?auto=format&fit=crop&w=800&q=80',
            'https://images.unsplash.com/photo-1544551763-46a013bb70d5?auto=format&fit=crop&w=800&q=80',
        ],
    },
    {
        id: 'ujung-kulon-peucang',
        slug: 'open-trip-pulau-peucang-ujung-kulon',
        title: 'Open Trip Pulau Peucang 3D2N Cidaon Savana & Snorkeling Karang Copong',
        type: 'open-trip',
        categoryName: 'Open Trip',
        destination: 'Taman Nasional Ujung Kulon, Pandeglang Banten',
        meetingPoint: 'Dermaga Sumur Banten / Jakarta',
        duration: '3 Hari 2 Malam (3H2M)',
        durationCode: '3d2n',
        price: 750000,
        originalPrice: 900000,
        rating: 8.7,
        ratingLabel: 'Sangat Bagus',
        reviewCount: 1120,
        bookedThisWeek: 26,
        isPreferred: true,
        instantConfirmation: true,
        freeReschedule: true,
        stars: 4,
        facilities: ['Kapal Jelajah Ujung Kulon', 'Penginapan Wisma Peucang', 'Makan 4x Selama Trip', 'Kanoing Sungai Cigenter', 'Ranger Balai TNUK'],
        promoCode: 'PEUCANGSERU',
        promoBadge: 'Wildlife Safari: Lihat Banteng Jawa, Rusa & Merak Liar',
        reviewSnippet: {
            author: 'B***m',
            badge: 'Wildlife Lover',
            quote: 'Pasir putih Peucang super halus seperti tepung. Rusa-rusa jinak berkeliaran di depan kamar penginapan.',
        },
        images: [
            'https://images.unsplash.com/photo-1507525428034-b723cf961d3e?auto=format&fit=crop&w=800&q=80',
            'https://images.unsplash.com/photo-1588668214407-6ea9a6d8c272?auto=format&fit=crop&w=800&q=80',
        ],
    },
    {
        id: 'danau-toba-samosir',
        slug: 'private-trip-danau-toba-samosir-parapat',
        title: 'Private Trip Eksplorasi Danau Toba, Pulau Samosir & Air Terjun Sipiso-piso 3D2N',
        type: 'private-trip',
        categoryName: 'Private Trip',
        destination: 'Danau Toba & Samosir, Sumatera Utara',
        meetingPoint: 'Bandara Kualanamu Medan / Bandara Silangit',
        duration: '3 Hari 2 Malam (3H2M)',
        durationCode: '3d2n',
        price: 1250000,
        originalPrice: 1550000,
        rating: 8.9,
        ratingLabel: 'Luar Biasa',
        reviewCount: 1350,
        bookedThisWeek: 33,
        isPreferred: true,
        instantConfirmation: true,
        freeReschedule: true,
        stars: 5,
        facilities: ['Mobil Private All In', 'Kapal Ferry Penyeberangan', 'Hotel View Danau Toba', 'Makan Khas Batak Halal / Nasional', 'Driver & Guide Lokal'],
        promoCode: 'TOBAMANTAP',
        promoBadge: 'Pemandangan Kaldera Terbesar Dunia dari Puncak Bukit Holbung',
        reviewSnippet: {
            author: 'P***a',
            badge: 'Family Traveler',
            quote: 'View Danau Toba dari Bukit Holbung dan Desa Tomok sangat menakjubkan. Suasana sejuk dan nyaman buat keluarga.',
        },
        images: [
            'https://images.unsplash.com/photo-1506744038136-46273834b3fb?auto=format&fit=crop&w=800&q=80',
            'https://images.unsplash.com/photo-1464822759023-fed622ff2c3b?auto=format&fit=crop&w=800&q=80',
        ],
    },
    {
        id: 'morotai-dodola',
        slug: 'open-trip-morotai-dodola-maluku-utara',
        title: 'Eksplorasi Morotai & Pulau Dodola Pasir Timbul Terpanjang 4D3N',
        type: 'open-trip',
        categoryName: 'Open Trip',
        destination: 'Pulau Morotai, Maluku Utara',
        meetingPoint: 'Bandara Leo Wattimena (OTI), Morotai / Ternate',
        duration: '4 Hari 3 Malam (4H3M)',
        durationCode: '4d3n',
        price: 2650000,
        originalPrice: 3100000,
        rating: 9.2,
        ratingLabel: 'Luar Biasa',
        reviewCount: 290,
        bookedThisWeek: 8,
        isPreferred: true,
        instantConfirmation: true,
        freeReschedule: true,
        stars: 5,
        facilities: ['Speedboat Island Hopping', 'Resort Tepi Pantai', 'Makan 3x Seafood', 'Kunjungan Museum Perang Dunia II', 'Snorkeling Gear Lengkap'],
        promoCode: 'MOROTAIEKSPEDISI',
        promoBadge: 'Mutiara Tersembunyi Indonesia Timur di Bibir Pasifik',
        reviewSnippet: {
            author: 'J***n',
            badge: 'Island Hopper',
            quote: 'Jalan kaki di atas pasir timbul yang membelah laut antara Dodola Besar dan Dodola Kecil rasanya seperti di surga!',
        },
        images: [
            'https://images.unsplash.com/photo-1537996194471-e657df975ab4?auto=format&fit=crop&w=800&q=80',
            'https://images.unsplash.com/photo-1544644181-1484b3fdfc62?auto=format&fit=crop&w=800&q=80',
        ],
    },
    {
        id: 'gorontalo-whale-shark',
        slug: 'private-trip-gorontalo-whale-shark-olele',
        title: 'Private Trip Renang Bareng Whale Shark Botubarani & Marine Park Olele 2D1N',
        type: 'private-trip',
        categoryName: 'Private Trip',
        destination: 'Gorontalo, Sulawesi',
        meetingPoint: 'Bandara Djalaluddin Gorontalo',
        duration: '2 Hari 1 Malam (2H1M)',
        durationCode: '2d1n',
        price: 980000,
        originalPrice: 1200000,
        rating: 8.8,
        ratingLabel: 'Sangat Bagus',
        reviewCount: 620,
        bookedThisWeek: 18,
        isPreferred: true,
        instantConfirmation: true,
        freeReschedule: true,
        stars: 4,
        facilities: ['Mobil Private All In', 'Perahu Katamaran Whale Shark', 'Makan Siang Ikan Bakar Gorontalo', 'Alat Snorkeling Lengkap', 'Guide Lokal'],
        promoCode: 'GORONTALOHIU',
        promoBadge: 'Sensasi Dekat Hanya 20 Meter dari Pesisir Pantai',
        reviewSnippet: {
            author: 'T***y',
            badge: 'Verified Buyer',
            quote: 'Hiu paus langsung berenang di samping perahu kita pas baru naik 5 menit dari pantai. Pengalaman luar biasa!',
        },
        images: [
            'https://images.unsplash.com/photo-1544551763-46a013bb70d5?auto=format&fit=crop&w=800&q=80',
            'https://images.unsplash.com/photo-1507525428034-b723cf961d3e?auto=format&fit=crop&w=800&q=80',
        ],
    },
    {
        id: 'rinjani-lombok',
        slug: 'open-trip-gunung-rinjani-trekking-sembalun',
        title: 'Open Trip Gunung Rinjani Trekking Plawangan Sembalun & Danau Segara Anak 3D2N',
        type: 'open-trip',
        categoryName: 'Open Trip',
        destination: 'Lombok Timur & Utara, NTB',
        meetingPoint: 'Bandara Internasional Lombok (LOP) / Mataram',
        duration: '3 Hari 2 Malam (3H2M)',
        durationCode: '3d2n',
        price: 1750000,
        originalPrice: 2100000,
        rating: 9.3,
        ratingLabel: 'Luar Biasa',
        reviewCount: 890,
        bookedThisWeek: 22,
        isPreferred: true,
        instantConfirmation: true,
        freeReschedule: true,
        stars: 5,
        facilities: ['Tenda Dome & Matras Busa', 'Porter & Guide Porter Gunung', 'Makan 3x Sehari Masak Hangat', 'Tiket Masuk TNGR & Asuransi', 'Transport Pickup'],
        promoCode: 'RINJANISUMMIT',
        promoBadge: 'Puncak Tertinggi Kedua di Indonesia dengan View Segara Anak',
        reviewSnippet: {
            author: 'A***d',
            badge: 'Mountain Climber',
            quote: 'Pemandangan Danau Segara Anak dan Gunung Barujari dari Plawangan Sembalun bikin semua rasa lelah trekking terbayar tuntas!',
        },
        images: [
            'https://images.unsplash.com/photo-1588668214407-6ea9a6d8c272?auto=format&fit=crop&w=800&q=80',
            'https://images.unsplash.com/photo-1464822759023-fed622ff2c3b?auto=format&fit=crop&w=800&q=80',
        ],
    },
    {
        id: 'pulau-pari-perawan',
        slug: 'open-trip-pulau-pari-pantai-pasir-perawan',
        title: 'Open Trip Pulau Pari 2D1N Sepeda Santai & Sunset Pantai Pasir Perawan',
        type: 'open-trip',
        categoryName: 'Open Trip',
        destination: 'Kepulauan Seribu Selatan, DKI Jakarta',
        meetingPoint: 'Pelabuhan Muara Angke / Marina Ancol',
        duration: '2 Hari 1 Malam (2H1M)',
        durationCode: '2d1n',
        price: 360000,
        originalPrice: 440000,
        rating: 8.4,
        ratingLabel: 'Sangat Bagus',
        reviewCount: 3100,
        bookedThisWeek: 60,
        isPreferred: false,
        instantConfirmation: true,
        freeReschedule: true,
        stars: 3,
        facilities: ['Kapal PP Muara Angke', 'Homestay AC Nyaman', 'Sepeda Santai Selama Trip', 'Makan 3x Prasmanan + BBQ Ikan', 'Tiket Pantai Pasir Perawan'],
        promoCode: 'PULIKPARI',
        promoBadge: 'Trip Santai Akhir Pekan Terpopuler Dekat Jakarta',
        reviewSnippet: {
            author: 'M***l',
            badge: 'Casual Traveler',
            quote: 'Keliling pulau naik sepeda sore-sore adem banget. Pantai Pasir Perawan tenang ombaknya cocok buat healing.',
        },
        images: [
            'https://images.unsplash.com/photo-1507525428034-b723cf961d3e?auto=format&fit=crop&w=800&q=80',
            'https://images.unsplash.com/photo-1518548419970-58e3b4079ab2?auto=format&fit=crop&w=800&q=80',
        ],
    },
];

// Combine DB props with curated trips
const allAvailableTrips = computed(() => {
    const dbTrips = (props.trips?.data || []).map((t, idx) => ({
        id: t.id || `db-${idx}`,
        slug: t.slug || `trip-${t.id}`,
        title: t.title || 'Paket Wisata Eksplorasi Nusantara',
        type: t.type || 'open-trip',
        categoryName: t.type === 'private-trip' ? 'Private Trip' : 'Open Trip',
        destination: t.destination || 'Indonesia',
        meetingPoint: t.meeting_point || 'Meeting Point Kota Terdekat',
        duration: '3 Hari 2 Malam (3H2M)',
        durationCode: '3d2n',
        price: Number(t.selling_price || t.price || 1500000),
        originalPrice: Math.round(Number(t.selling_price || t.price || 1500000) * 1.15),
        rating: 8.7,
        ratingLabel: 'Sangat Bagus',
        reviewCount: 320 + idx * 45,
        bookedThisWeek: 15 + idx * 3,
        isPreferred: true,
        instantConfirmation: true,
        freeReschedule: true,
        stars: 5,
        facilities: ['Transport AC PP', 'Makan Termasuk', 'Dokumentasi Foto', 'Tour Guide Lokal', 'Tiket Objek Wisata'],
        promoCode: 'TAPAKLOKAL',
        promoBadge: 'Diskon 8% Pengguna Baru! Gunakan kode: TAPAKLOKAL',
        reviewSnippet: {
            author: 'Pengguna TapakLokal',
            badge: 'Verified Buyer',
            quote: 'Perjalanan sangat terorganisir dengan baik, fasilitas sesuai deskripsi dan pemandu lokal sangat ramah.',
        },
        images: [
            t.image_url || 'https://images.unsplash.com/photo-1507525428034-b723cf961d3e?auto=format&fit=crop&w=800&q=80',
            'https://images.unsplash.com/photo-1516426122078-c23e76319801?auto=format&fit=crop&w=800&q=80',
        ],
    }));

    // If dbTrips exist, merge with curated items ensuring unique ids/slugs
    if (dbTrips.length > 0) {
        const slugs = new Set(dbTrips.map(d => d.slug));
        const nonDuplicateCurated = curatedTripsData.filter(c => !slugs.has(c.slug));
        return [...dbTrips, ...nonDuplicateCurated];
    }

    return curatedTripsData;
});

// Sidebar & Filter States
const priceMin = ref(0);
const priceMax = ref(15000000);
const selectedDateIdea = ref('');
const selectedPromos = ref([]);
const selectedTripTypes = ref(props.initialFilters?.type ? [props.initialFilters.type] : []);
const selectedFacilities = ref([]);
const selectedStars = ref([]);
const selectedPicks = ref([]);
const sortBy = ref('popular');
const viewMode = ref('list'); // 'list' | 'grid'
const activeImageIndex = ref({});
const copiedCode = ref(false);
const showAllFacilities = ref(false);
const showAllTripTypes = ref(false);

// Collapsible Accordion Sections
const openAccordions = ref({
    price: true,
    promo: true,
    tripType: true,
    facilities: true,
    starRating: true,
    picks: true,
});

const toggleAccordion = (section) => {
    openAccordions.value[section] = !openAccordions.value[section];
};

// Copy voucher code
const copyPromoCode = () => {
    navigator.clipboard.writeText('TAPAKLOKAL');
    copiedCode.value = true;
    setTimeout(() => {
        copiedCode.value = false;
    }, 2500);
};

// Date Ideas Quick Presets
const dateIdeas = [
    { id: 'today', label: 'Hari Ini', dates: '28 Sep - 29 Sep' },
    { id: 'tomorrow', label: 'Besok', dates: '29 Sep - 30 Sep' },
    { id: 'this-weekend', label: 'Weekend Ini', dates: '03 Okt - 04 Okt' },
    { id: 'next-weekend', label: 'Weekend Depan', dates: '10 Okt - 11 Okt' },
];

const selectDateIdea = (id) => {
    selectedDateIdea.value = selectedDateIdea.value === id ? '' : id;
};

// Reset all filters
const resetAllFilters = () => {
    priceMin.value = 0;
    priceMax.value = 15000000;
    selectedDateIdea.value = '';
    selectedPromos.value = [];
    selectedTripTypes.value = [];
    selectedFacilities.value = [];
    selectedStars.value = [];
    selectedPicks.value = [];
    sortBy.value = 'popular';
};

// Image Carousel navigation on cards
const nextCardImage = (tripId, imagesLength) => {
    const current = activeImageIndex.value[tripId] || 0;
    activeImageIndex.value[tripId] = (current + 1) % imagesLength;
};

const prevCardImage = (tripId, imagesLength) => {
    const current = activeImageIndex.value[tripId] || 0;
    activeImageIndex.value[tripId] = (current - 1 + imagesLength) % imagesLength;
};

const setCardImage = (tripId, idx) => {
    activeImageIndex.value[tripId] = idx;
};

// Filtered & Sorted Trips
const filteredTrips = computed(() => {
    return allAvailableTrips.value.filter((trip) => {
        // Price Filter
        if (trip.price < priceMin.value || trip.price > priceMax.value) {
            return false;
        }

        // Trip Type Filter
        if (selectedTripTypes.value.length > 0) {
            if (!selectedTripTypes.value.includes(trip.type)) {
                return false;
            }
        }

        // Star Rating Filter (1 to 5 Stars)
        if (selectedStars.value.length > 0) {
            if (!selectedStars.value.includes(trip.stars)) {
                return false;
            }
        }

        // Preferred Picks Filter
        if (selectedPicks.value.includes('preferred') && !trip.isPreferred) {
            return false;
        }
        if (selectedPicks.value.includes('instant') && !trip.instantConfirmation) {
            return false;
        }
        if (selectedPicks.value.includes('free-reschedule') && !trip.freeReschedule) {
            return false;
        }

        return true;
    }).sort((a, b) => {
        if (sortBy.value === 'price-asc') return a.price - b.price;
        if (sortBy.value === 'price-desc') return b.price - a.price;
        if (sortBy.value === 'rating-desc') return b.rating - a.rating;
        if (sortBy.value === 'popular') return b.reviewCount - a.reviewCount;
        return 0;
    });
});

// Price Formatter (IDR)
const formatCurrency = (val) => {
    return new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: 'IDR',
        maximumFractionDigits: 0,
    }).format(val).replace('Rp', 'Rp ');
};

const formatNumberOnly = (val) => {
    return new Intl.NumberFormat('id-ID').format(val);
};

// Pagination States (8 items max per page)
const itemsPerPage = 8;
const currentPage = ref(1);

const totalPages = computed(() => {
    return Math.max(1, Math.ceil(filteredTrips.value.length / itemsPerPage));
});

const paginatedTrips = computed(() => {
    const start = (currentPage.value - 1) * itemsPerPage;
    return filteredTrips.value.slice(start, start + itemsPerPage);
});

// Visible page items for pagination display (1:1 with reference design)
const visiblePages = computed(() => {
    const total = totalPages.value;
    const current = currentPage.value;

    if (total <= 6) {
        return Array.from({ length: total }, (_, i) => i + 1);
    }

    if (current <= 3) {
        return [1, 2, 3, 4, 5, '...', total];
    } else if (current >= total - 2) {
        return [1, '...', total - 4, total - 3, total - 2, total - 1, total];
    } else {
        return [1, '...', current - 1, current, current + 1, '...', total];
    }
});

const goToPage = (page) => {
    if (page === '...' || page < 1 || page > totalPages.value || page === currentPage.value) {
        return;
    }
    currentPage.value = page;
    const el = document.getElementById('catalog-results-anchor');
    if (el) {
        el.scrollIntoView({ behavior: 'smooth', block: 'start' });
    } else {
        window.scrollTo({ top: 320, behavior: 'smooth' });
    }
};

const prevPage = () => {
    if (currentPage.value > 1) {
        goToPage(currentPage.value - 1);
    }
};

const nextPage = () => {
    if (currentPage.value < totalPages.value) {
        goToPage(currentPage.value + 1);
    }
};

// Reset page to 1 when any filter changes
watch(
    [
        selectedTripTypes,
        selectedPromos,
        selectedFacilities,
        selectedStars,
        selectedPicks,
        priceMin,
        priceMax,
        sortBy,
        selectedDateIdea,
    ],
    () => {
        currentPage.value = 1;
    },
    { deep: true }
);

// Mobile filter drawer state
const isMobileFilterOpen = ref(false);

const activeFilterCount = computed(() => {
    let count = selectedTripTypes.value.length + selectedPromos.value.length + selectedFacilities.value.length + selectedStars.value.length + selectedPicks.value.length;
    if (selectedDateIdea.value) count += 1;
    if (priceMax.value < 15000000) count += 1;
    return count;
});

const applyMobileFilter = () => {
    isMobileFilterOpen.value = false;
    const el = document.getElementById('catalog-results-anchor');
    if (el) {
        el.scrollIntoView({ behavior: 'smooth', block: 'start' });
    }
};

// Header Title
const listingSectionTitle = computed(() => {
    if (props.partner?.name) {
        return `Paket Trip Populer bersama ${props.partner.name}`;
    }
    if (props.initialFilters?.type === 'open-trip') {
        return 'Paket Open Trip Populer di Indonesia';
    }
    if (props.initialFilters?.type === 'private-trip') {
        return 'Paket Private Trip Eksklusif di Indonesia';
    }
    return 'Paket Open Trip & Private Trip Terbaik';
});
</script>

<template>
    <section class="mx-auto max-w-[1180px] px-4 sm:px-6 lg:px-0 mt-8 mb-16" aria-label="Hasil Pencarian & Katalog Trip">
        <div class="grid grid-cols-1 lg:grid-cols-[285px_1fr] gap-6 items-start">
            
            <!-- ========================================================= -->
            <!-- LEFT COLUMN: PROMO, MAP & FILTER SIDEBAR (Traveloka Style) -->
            <!-- ========================================================= -->
            <aside class="hidden lg:block space-y-4">
                
                <!-- 1. Top Promo Discount Card (Traveloka Blue Gradient) -->
                <div class="relative overflow-hidden rounded-2xl bg-gradient-to-br from-[#0055d4] via-[#006ee6] to-[#00aaff] p-4 text-white shadow-[0_8px_24px_rgba(0,100,230,0.22)]">
                    <!-- Background Accent Waves -->
                    <div class="absolute -right-6 -bottom-8 size-32 rounded-full bg-white/10 blur-xl pointer-events-none"></div>
                    <div class="absolute right-2 top-2 opacity-20 pointer-events-none">
                        <Ticket class="size-20 rotate-12" />
                    </div>

                    <div class="relative z-10">
                        <h3 class="text-base font-extrabold leading-tight">
                            Get 10% off up to <span class="text-amber-300">Rp 150.000!</span>
                        </h3>
                        <p class="mt-1 text-[11px] text-blue-50 leading-relaxed">
                            Gunakan kode kupon promo saat checkout pesanan trip pertamamu di TapakLokal.
                        </p>

                        <!-- Copy Code Bar -->
                        <div class="mt-3 flex items-center justify-between rounded-xl bg-white/15 p-1.5 backdrop-blur-md border border-white/25">
                            <div class="flex items-center gap-1.5 pl-2">
                                <Tag class="size-3.5 text-amber-300" />
                                <span class="font-mono text-xs font-black tracking-wider text-white">TAPAKLOKAL</span>
                            </div>
                            <button
                                type="button"
                                class="inline-flex items-center gap-1 rounded-lg bg-white px-2.5 py-1 text-[10.5px] font-bold text-[#0066d6] shadow-xs transition hover:bg-blue-50 active:scale-95 cursor-pointer"
                                @click="copyPromoCode"
                            >
                                <Check v-if="copiedCode" class="size-3 text-emerald-600" />
                                <Copy v-else class="size-3" />
                                <span>{{ copiedCode ? 'Tersalin!' : 'Salin Kode' }}</span>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- 2. Interactive Map / Location Preview Card (1:1 Traveloka Reference) -->
                <div class="group relative overflow-hidden rounded-2xl border border-sky-200/90 bg-[#e3f2fd] h-[72px] sm:h-[76px] shadow-2xs cursor-pointer transition hover:shadow-md hover:border-[#0088ff]/60">
                    <!-- Stylized SVG Map Background with Streets & Water Area -->
                    <svg class="absolute inset-0 size-full opacity-60 pointer-events-none" viewBox="0 0 300 80" fill="none" preserveAspectRatio="xMidYMid slice" xmlns="http://www.w3.org/2000/svg">
                        <!-- Water / Coastline -->
                        <path d="M0,50 Q60,30 120,55 T240,40 T300,60 L300,80 L0,80 Z" fill="#bfe3fc" />
                        <path d="M0,0 L80,0 Q100,30 140,20 L160,0 L0,0 Z" fill="#cfe9fc" />
                        <!-- Roads & Grid Lines -->
                        <path d="M-20,35 L320,15" stroke="#ffffff" stroke-width="4" stroke-linecap="round" />
                        <path d="M50,-10 L80,90" stroke="#ffffff" stroke-width="3" stroke-linecap="round" />
                        <path d="M190,-10 L160,90" stroke="#ffffff" stroke-width="3.5" stroke-linecap="round" />
                        <path d="M-10,60 L310,45" stroke="#ffffff" stroke-width="2.5" stroke-dasharray="6,4" />
                        <path d="M120,-10 L130,90" stroke="#ffffff" stroke-width="2" />
                    </svg>

                    <!-- Content Overlay: Left Crisp Pin & Right Open Maps Button -->
                    <div class="relative z-10 flex size-full items-center justify-between px-5">
                        <!-- Left: Clear Crisp Location Pin (No blinking) -->
                        <div class="flex items-center">
                            <MapPin class="size-6 text-[#0088ff] drop-shadow-xs stroke-[2.2]" />
                        </div>

                        <!-- Right: Open Maps Button -->
                        <button
                            type="button"
                            class="inline-flex items-center gap-1.5 rounded-xl bg-[#0088ff] px-3.5 py-2 text-xs font-bold text-white shadow-[0_4px_12px_rgba(0,136,255,0.25)] transition hover:bg-[#0074e0] active:scale-95 cursor-pointer"
                        >
                            <Map class="size-3.5" />
                            <span>Open Maps</span>
                        </button>
                    </div>
                </div>

                <!-- 3. Trip Date Ideas (Quick Filter Chips 2x2 Grid) -->
                <div class="rounded-2xl border border-slate-200/90 bg-white p-4 shadow-2xs">
                    <div class="flex items-center justify-between">
                        <h4 class="text-xs font-bold text-slate-800 uppercase tracking-wider">Pilihan Tanggal Trip</h4>
                        <span class="text-[10px] text-slate-400 font-medium">Cepat</span>
                    </div>

                    <div class="mt-3 grid grid-cols-2 gap-2">
                        <button
                            v-for="date in dateIdeas"
                            :key="date.id"
                            type="button"
                            class="flex flex-col items-start rounded-xl border p-2.5 text-left transition-all duration-150 cursor-pointer"
                            :class="selectedDateIdea === date.id 
                                ? 'border-[#0088ff] bg-blue-50/70 text-[#0066d6] ring-1 ring-[#0088ff]' 
                                : 'border-slate-200/80 bg-slate-50/50 hover:bg-slate-100/80 text-slate-700'"
                            @click="selectDateIdea(date.id)"
                        >
                            <span class="text-xs font-bold">{{ date.label }}</span>
                            <span class="mt-0.5 text-[10px] text-slate-500 font-medium">{{ date.dates }}</span>
                        </button>
                    </div>
                </div>

                <!-- 4. Price Range Filter Card (Dual Slider & Inputs) -->
                <div class="rounded-2xl border border-slate-200/90 bg-white p-4 shadow-2xs">
                    <div class="flex items-center justify-between">
                        <div>
                            <h4 class="text-xs font-bold text-slate-800 uppercase tracking-wider">Rentang Harga</h4>
                            <p class="text-[10.5px] text-slate-400">Per orang, per paket trip</p>
                        </div>
                        <button
                            type="button"
                            class="text-xs font-bold text-[#0088ff] hover:underline cursor-pointer"
                            @click="priceMin = 0; priceMax = 15000000"
                        >
                            Reset
                        </button>
                    </div>

                    <!-- Slider Bar -->
                    <div class="mt-4 px-1">
                        <input
                            v-model.number="priceMax"
                            type="range"
                            min="300000"
                            max="15000000"
                            step="100000"
                            class="w-full h-1.5 bg-slate-200 rounded-lg appearance-none cursor-pointer accent-[#0088ff]"
                        />
                    </div>

                    <!-- Price Inputs Box (Traveloka IDR input format) -->
                    <div class="mt-3 grid grid-cols-2 gap-2">
                        <div class="rounded-xl border border-slate-200/90 bg-slate-50/50 px-2.5 py-1.5">
                            <span class="block text-[9px] font-bold text-slate-400 uppercase">Min</span>
                            <span class="text-xs font-bold text-slate-800">IDR 0</span>
                        </div>
                        <div class="rounded-xl border border-slate-200/90 bg-slate-50/50 px-2.5 py-1.5">
                            <span class="block text-[9px] font-bold text-slate-400 uppercase">Max</span>
                            <span class="text-xs font-bold text-slate-800">IDR {{ formatNumberOnly(priceMax) }}</span>
                        </div>
                    </div>
                </div>

                <!-- 5. Promo & Discount (Collapsible Accordion) -->
                <div class="rounded-2xl border border-slate-200/90 bg-white p-4 shadow-2xs">
                    <button
                        type="button"
                        class="flex w-full items-center justify-between text-left cursor-pointer group"
                        @click="toggleAccordion('promo')"
                    >
                        <h4 class="text-xs font-bold text-slate-800 uppercase tracking-wider">Promo & Diskon</h4>
                        <div class="grid size-7.5 place-items-center rounded-full bg-[#e8f2ff] text-[#0088ff] transition-all group-hover:bg-[#d8eaff] shrink-0">
                            <ChevronDown class="size-4 stroke-[2.5] transition-transform duration-200" :class="openAccordions.promo ? 'rotate-180' : ''" />
                        </div>
                    </button>

                    <div v-show="openAccordions.promo" class="mt-3 space-y-2.5 pt-1">
                        <label class="flex items-center gap-2.5 text-xs text-slate-700 cursor-pointer hover:text-slate-900">
                            <input v-model="selectedPromos" type="checkbox" value="for-you" class="size-4 rounded border-slate-300 text-[#0088ff] focus:ring-[#0088ff]" />
                            <span class="font-medium">Promo for You (8% OFF)</span>
                        </label>
                        <label class="flex items-center gap-2.5 text-xs text-slate-700 cursor-pointer hover:text-slate-900">
                            <input v-model="selectedPromos" type="checkbox" value="domestic" class="size-4 rounded border-slate-300 text-[#0088ff] focus:ring-[#0088ff]" />
                            <span class="font-medium">Promo Domestik Spesial</span>
                        </label>
                        <label class="flex items-center gap-2.5 text-xs text-slate-700 cursor-pointer hover:text-slate-900">
                            <input v-model="selectedPromos" type="checkbox" value="extra" class="size-4 rounded border-slate-300 text-[#0088ff] focus:ring-[#0088ff]" />
                            <span class="font-medium">Extra Cashback TapakPoints</span>
                        </label>
                    </div>
                </div>

                <!-- 6. Tipe & Kategori Trip (Collapsible Accordion) -->
                <div class="rounded-2xl border border-slate-200/90 bg-white p-4 shadow-2xs">
                    <button
                        type="button"
                        class="flex w-full items-center justify-between text-left cursor-pointer group"
                        @click="toggleAccordion('tripType')"
                    >
                        <h4 class="text-xs font-bold text-slate-800 uppercase tracking-wider">Tipe & Kategori Trip</h4>
                        <div class="grid size-7.5 place-items-center rounded-full bg-[#e8f2ff] text-[#0088ff] transition-all group-hover:bg-[#d8eaff] shrink-0">
                            <ChevronDown class="size-4 stroke-[2.5] transition-transform duration-200" :class="openAccordions.tripType ? 'rotate-180' : ''" />
                        </div>
                    </button>

                    <div v-show="openAccordions.tripType" class="mt-3 space-y-2.5 pt-1">
                        <label class="flex items-center gap-2.5 text-xs text-slate-700 cursor-pointer hover:text-slate-900">
                            <input v-model="selectedTripTypes" type="checkbox" value="open-trip" class="size-4 rounded border-slate-300 text-[#0088ff] focus:ring-[#0088ff]" />
                            <span class="font-medium">Open Trip (Gabungan Hemat)</span>
                        </label>
                        <label class="flex items-center gap-2.5 text-xs text-slate-700 cursor-pointer hover:text-slate-900">
                            <input v-model="selectedTripTypes" type="checkbox" value="private-trip" class="size-4 rounded border-slate-300 text-[#0088ff] focus:ring-[#0088ff]" />
                            <span class="font-medium">Private Trip (Eksklusif & Rute Bebas)</span>
                        </label>
                        <label class="flex items-center gap-2.5 text-xs text-slate-700 cursor-pointer hover:text-slate-900">
                            <input v-model="selectedTripTypes" type="checkbox" value="snorkeling" class="size-4 rounded border-slate-300 text-[#0088ff] focus:ring-[#0088ff]" />
                            <span class="font-medium">Wisata Bahari & Snorkeling</span>
                        </label>
                        <label class="flex items-center gap-2.5 text-xs text-slate-700 cursor-pointer hover:text-slate-900">
                            <input v-model="selectedTripTypes" type="checkbox" value="mountain" class="size-4 rounded border-slate-300 text-[#0088ff] focus:ring-[#0088ff]" />
                            <span class="font-medium">Pendakian & Gunung Bromo</span>
                        </label>

                        <div v-show="showAllTripTypes" class="space-y-2.5 pt-1">
                            <label class="flex items-center gap-2.5 text-xs text-slate-700 cursor-pointer hover:text-slate-900">
                                <input v-model="selectedTripTypes" type="checkbox" value="culture" class="size-4 rounded border-slate-300 text-[#0088ff] focus:ring-[#0088ff]" />
                                <span class="font-medium">Wisata Budaya & Heritage</span>
                            </label>
                            <label class="flex items-center gap-2.5 text-xs text-slate-700 cursor-pointer hover:text-slate-900">
                                <input v-model="selectedTripTypes" type="checkbox" value="family" class="size-4 rounded border-slate-300 text-[#0088ff] focus:ring-[#0088ff]" />
                                <span class="font-medium">Family Gathering & Outing</span>
                            </label>
                        </div>

                        <button
                            type="button"
                            class="pt-1 text-xs font-bold text-[#0088ff] hover:underline cursor-pointer"
                            @click="showAllTripTypes = !showAllTripTypes"
                        >
                            {{ showAllTripTypes ? 'Sembunyikan' : 'Lihat Semua (See All)' }}
                        </button>
                    </div>
                </div>

                <!-- 7. Fasilitas Termasuk / Popular Facilities (Collapsible Accordion) -->
                <div class="rounded-2xl border border-slate-200/90 bg-white p-4 shadow-2xs">
                    <button
                        type="button"
                        class="flex w-full items-center justify-between text-left cursor-pointer group"
                        @click="toggleAccordion('facilities')"
                    >
                        <h4 class="text-xs font-bold text-slate-800 uppercase tracking-wider">Fasilitas Termasuk</h4>
                        <div class="grid size-7.5 place-items-center rounded-full bg-[#e8f2ff] text-[#0088ff] transition-all group-hover:bg-[#d8eaff] shrink-0">
                            <ChevronDown class="size-4 stroke-[2.5] transition-transform duration-200" :class="openAccordions.facilities ? 'rotate-180' : ''" />
                        </div>
                    </button>

                    <div v-show="openAccordions.facilities" class="mt-3 space-y-2.5 pt-1">
                        <label class="flex items-center gap-2.5 text-xs text-slate-700 cursor-pointer hover:text-slate-900">
                            <input v-model="selectedFacilities" type="checkbox" value="transport" class="size-4 rounded border-slate-300 text-[#0088ff] focus:ring-[#0088ff]" />
                            <span class="font-medium">Transportasi AC PP</span>
                        </label>
                        <label class="flex items-center gap-2.5 text-xs text-slate-700 cursor-pointer hover:text-slate-900">
                            <input v-model="selectedFacilities" type="checkbox" value="meals" class="size-4 rounded border-slate-300 text-[#0088ff] focus:ring-[#0088ff]" />
                            <span class="font-medium">Makan & Minum Termasuk</span>
                        </label>
                        <label class="flex items-center gap-2.5 text-xs text-slate-700 cursor-pointer hover:text-slate-900">
                            <input v-model="selectedFacilities" type="checkbox" value="stay" class="size-4 rounded border-slate-300 text-[#0088ff] focus:ring-[#0088ff]" />
                            <span class="font-medium">Penginapan / Homestay / Kapal</span>
                        </label>
                        <label class="flex items-center gap-2.5 text-xs text-slate-700 cursor-pointer hover:text-slate-900">
                            <input v-model="selectedFacilities" type="checkbox" value="drone" class="size-4 rounded border-slate-300 text-[#0088ff] focus:ring-[#0088ff]" />
                            <span class="font-medium">Dokumentasi Foto & Drone</span>
                        </label>

                        <div v-show="showAllFacilities" class="space-y-2.5 pt-1">
                            <label class="flex items-center gap-2.5 text-xs text-slate-700 cursor-pointer hover:text-slate-900">
                                <input v-model="selectedFacilities" type="checkbox" value="tickets" class="size-4 rounded border-slate-300 text-[#0088ff] focus:ring-[#0088ff]" />
                                <span class="font-medium">Tiket Masuk & Retribusi</span>
                            </label>
                            <label class="flex items-center gap-2.5 text-xs text-slate-700 cursor-pointer hover:text-slate-900">
                                <input v-model="selectedFacilities" type="checkbox" value="guide" class="size-4 rounded border-slate-300 text-[#0088ff] focus:ring-[#0088ff]" />
                                <span class="font-medium">Tour Guide Berlisensi</span>
                            </label>
                            <label class="flex items-center gap-2.5 text-xs text-slate-700 cursor-pointer hover:text-slate-900">
                                <input v-model="selectedFacilities" type="checkbox" value="snorkeling-gear" class="size-4 rounded border-slate-300 text-[#0088ff] focus:ring-[#0088ff]" />
                                <span class="font-medium">Alat Snorkeling Lengkap</span>
                            </label>
                        </div>

                        <button
                            type="button"
                            class="pt-1 text-xs font-bold text-[#0088ff] hover:underline cursor-pointer"
                            @click="showAllFacilities = !showAllFacilities"
                        >
                            {{ showAllFacilities ? 'Sembunyikan' : 'Lihat Semua (See All)' }}
                        </button>
                    </div>
                </div>

                <!-- 8. Star Rating (1:1 with User Screenshot Reference) -->
                <div class="rounded-2xl border border-slate-200/90 bg-white p-4 shadow-2xs">
                    <button
                        type="button"
                        class="flex w-full items-center justify-between text-left cursor-pointer group"
                        @click="toggleAccordion('starRating')"
                    >
                        <h4 class="text-sm font-extrabold text-slate-900 tracking-tight">Star Rating</h4>
                        <div class="grid size-7.5 place-items-center rounded-full bg-[#e8f2ff] text-[#0088ff] transition-all group-hover:bg-[#d8eaff] shrink-0">
                            <ChevronDown class="size-4 stroke-[2.5] transition-transform duration-200" :class="openAccordions.starRating ? 'rotate-180' : ''" />
                        </div>
                    </button>

                    <div v-show="openAccordions.starRating" class="mt-3.5 space-y-3 pt-1">
                        <label
                            v-for="star in [1, 2, 3, 4, 5]"
                            :key="star"
                            class="flex items-center gap-3 text-sm font-bold text-slate-900 cursor-pointer select-none hover:text-[#0088ff] transition-colors"
                        >
                            <input
                                v-model="selectedStars"
                                type="checkbox"
                                :value="star"
                                class="size-5 rounded border-slate-300 text-[#0088ff] focus:ring-[#0088ff] cursor-pointer accent-[#0088ff]"
                            />
                            <div class="flex items-center gap-1.5">
                                <span>{{ star }}</span>
                                <Star class="size-4.5 fill-amber-400 text-amber-400 drop-shadow-xs" />
                            </div>
                        </label>

                        <button
                            v-if="selectedStars.length > 0"
                            type="button"
                            class="pt-1 text-xs font-bold text-[#0088ff] hover:underline cursor-pointer block"
                            @click="selectedStars = []"
                        >
                            Hapus filter rating
                        </button>
                    </div>
                </div>

                <!-- 9. TapakLokal's Picks / Preferred Partner -->
                <div class="rounded-2xl border border-slate-200/90 bg-white p-4 shadow-2xs">
                    <button
                        type="button"
                        class="flex w-full items-center justify-between text-left cursor-pointer group"
                        @click="toggleAccordion('picks')"
                    >
                        <h4 class="text-xs font-bold text-slate-800 uppercase tracking-wider">TapakLokal's Picks</h4>
                        <div class="grid size-7.5 place-items-center rounded-full bg-[#e8f2ff] text-[#0088ff] transition-all group-hover:bg-[#d8eaff] shrink-0">
                            <ChevronDown class="size-4 stroke-[2.5] transition-transform duration-200" :class="openAccordions.picks ? 'rotate-180' : ''" />
                        </div>
                    </button>

                    <div v-show="openAccordions.picks" class="mt-3 space-y-2.5 pt-1">
                        <label class="flex items-center gap-2.5 text-xs text-slate-700 cursor-pointer hover:text-slate-900">
                            <input v-model="selectedPicks" type="checkbox" value="preferred" class="size-4 rounded border-slate-300 text-[#0088ff] focus:ring-[#0088ff]" />
                            <span class="font-medium">Preferred Partner</span>
                        </label>
                        <label class="flex items-center gap-2.5 text-xs text-slate-700 cursor-pointer hover:text-slate-900">
                            <input v-model="selectedPicks" type="checkbox" value="instant" class="size-4 rounded border-slate-300 text-[#0088ff] focus:ring-[#0088ff]" />
                            <span class="font-medium">Konfirmasi Instan</span>
                        </label>
                        <label class="flex items-center gap-2.5 text-xs text-slate-700 cursor-pointer hover:text-slate-900">
                            <input v-model="selectedPicks" type="checkbox" value="free-reschedule" class="size-4 rounded border-slate-300 text-[#0088ff] focus:ring-[#0088ff]" />
                            <span class="font-medium">Bebas Reschedule / Refund</span>
                        </label>
                    </div>
                </div>

                <!-- Bottom Filter Actions (Reset & Terapkan) -->
                <div class="grid grid-cols-2 gap-2 pt-1">
                    <button
                        type="button"
                        class="rounded-xl border border-slate-200 bg-white py-2.5 text-xs font-bold text-slate-700 shadow-2xs transition hover:bg-slate-50 cursor-pointer"
                        @click="resetAllFilters"
                    >
                        Reset Filter
                    </button>
                    <button
                        type="button"
                        class="rounded-xl bg-[#0088ff] hover:bg-[#0074e0] py-2.5 text-xs font-bold text-white shadow-[0_4px_14px_rgba(0,136,255,0.30)] transition active:scale-95 cursor-pointer"
                    >
                        Terapkan ({{ filteredTrips.length }})
                    </button>
                </div>
            </aside>

            <!-- ========================================================= -->
            <!-- RIGHT COLUMN: HEADER, CAROUSEL PICKS, & TRIP CARDS (1:1)  -->
            <!-- ========================================================= -->
            <main class="min-w-0 space-y-4">
                
                <!-- 1. Top Header Bar with Sort & View Toggle -->
                <div id="catalog-results-anchor" class="flex flex-wrap items-center justify-between gap-3 border-b border-slate-200/80 pb-4 scroll-mt-24">
                    <div>
                        <h2 class="text-xl sm:text-2xl font-extrabold text-[#173b70] tracking-tight">
                            {{ listingSectionTitle }}
                        </h2>
                        <p class="mt-0.5 text-xs text-slate-500">
                            Menampilkan <span class="font-bold text-[#0088ff]">{{ filteredTrips.length }}</span> paket trip terverifikasi (Halaman {{ currentPage }} dari {{ totalPages }})
                        </p>
                    </div>

                    <!-- Right Controls: Sort by & View Toggle -->
                    <div class="flex items-center gap-3">
                        <!-- Mobile Filter Button (lg:hidden) -->
                        <button
                            type="button"
                            class="inline-flex lg:hidden items-center gap-2 rounded-xl bg-white border border-slate-200 px-3.5 py-2 text-xs font-bold text-slate-800 shadow-2xs hover:bg-slate-50 active:scale-95 transition"
                            @click="isMobileFilterOpen = true"
                        >
                            <SlidersHorizontal class="size-3.5 text-[#0088ff]" />
                            <span>Filter</span>
                            <span v-if="activeFilterCount > 0" class="flex size-4.5 items-center justify-center rounded-full bg-[#0088ff] text-[10px] font-bold text-white">
                                {{ activeFilterCount }}
                            </span>
                        </button>

                        <!-- Sort by Dropdown -->
                        <div class="flex items-center gap-1.5 text-xs font-semibold text-slate-600">
                            <span class="hidden sm:inline text-slate-400">Sort by:</span>
                            <div class="relative">
                                <select
                                    v-model="sortBy"
                                    class="rounded-xl border border-slate-200 bg-white px-3 py-2 text-xs font-bold text-[#0088ff] shadow-2xs focus:outline-none cursor-pointer pr-7"
                                    aria-label="Urutkan hasil"
                                >
                                    <option value="popular">Paling Populer</option>
                                    <option value="price-asc">Harga Terendah</option>
                                    <option value="price-desc">Harga Tertinggi</option>
                                    <option value="rating-desc">Rating Tertinggi</option>
                                </select>
                            </div>
                        </div>

                        <!-- View Switch Toggle -->
                        <div class="hidden sm:flex items-center gap-0.5 rounded-xl border border-slate-200 bg-white p-0.5 shadow-2xs">
                            <button
                                type="button"
                                class="grid size-7 place-items-center rounded-lg transition"
                                :class="viewMode === 'list' ? 'bg-slate-100 text-[#0088ff]' : 'text-slate-400 hover:text-slate-600'"
                                title="Tampilan List"
                                @click="viewMode = 'list'"
                            >
                                <LayoutList class="size-4" />
                            </button>
                            <button
                                type="button"
                                class="grid size-7 place-items-center rounded-lg transition"
                                :class="viewMode === 'grid' ? 'bg-slate-100 text-[#0088ff]' : 'text-slate-400 hover:text-slate-600'"
                                title="Tampilan Grid"
                                @click="viewMode = 'grid'"
                            >
                                <Grid class="size-4" />
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Mobile Active Filters Chips (Quick Clear) -->
                <div v-if="activeFilterCount > 0" class="flex flex-wrap items-center gap-1.5 pt-1 lg:hidden">
                    <span class="text-[11px] font-semibold text-slate-400">Filter Aktif:</span>
                    <button
                        v-for="t in selectedTripTypes"
                        :key="t"
                        type="button"
                        class="inline-flex items-center gap-1 rounded-lg bg-blue-50 border border-blue-200/80 px-2 py-0.5 text-[11px] font-bold text-[#0066d6]"
                        @click="selectedTripTypes = selectedTripTypes.filter(x => x !== t)"
                    >
                        <span>{{ t === 'open-trip' ? 'Open Trip' : t === 'private-trip' ? 'Private Trip' : t }}</span>
                        <X class="size-3" />
                    </button>
                    <button
                        v-if="selectedDateIdea"
                        type="button"
                        class="inline-flex items-center gap-1 rounded-lg bg-blue-50 border border-blue-200/80 px-2 py-0.5 text-[11px] font-bold text-[#0066d6]"
                        @click="selectedDateIdea = null"
                    >
                        <span>Tanggal: {{ dateIdeas.find(d => d.id === selectedDateIdea)?.label }}</span>
                        <X class="size-3" />
                    </button>
                    <button
                        v-if="priceMax < 15000000"
                        type="button"
                        class="inline-flex items-center gap-1 rounded-lg bg-blue-50 border border-blue-200/80 px-2 py-0.5 text-[11px] font-bold text-[#0066d6]"
                        @click="priceMax = 15000000"
                    >
                        <span>Max IDR {{ formatNumberOnly(priceMax) }}</span>
                        <X class="size-3" />
                    </button>
                    <button
                        type="button"
                        class="text-[11px] font-bold text-rose-600 hover:underline px-1"
                        @click="resetAllFilters"
                    >
                        Reset
                    </button>
                </div>

                <!-- Trip Cards Section (1:1 Traveloka Card Layout) -->
                <div v-if="filteredTrips.length > 0">
                    
                    <!-- 1. LIST VIEW (Horizontal Traveloka Layout) -->
                    <div v-if="viewMode === 'list'" class="space-y-4">
                        <article
                            v-for="trip in paginatedTrips"
                            :key="trip.id"
                            class="group relative overflow-hidden rounded-2xl border border-slate-200/90 bg-white shadow-2xs hover:shadow-md hover:border-blue-300/80 transition-all duration-200 md:grid md:grid-cols-[240px_1fr_195px] md:h-[225px]"
                        >
                            <!-- PHOTO SECTION -->
                            <div class="relative shrink-0 overflow-hidden bg-slate-100 group/photo w-full md:w-full h-48 md:h-full">
                                <img
                                    :key="activeImageIndex[trip.id] || 0"
                                    :src="trip.images[activeImageIndex[trip.id] || 0]"
                                    :alt="trip.title"
                                    class="size-full object-cover transition-all duration-300 group-hover:scale-105"
                                    loading="lazy"
                                />
                                <div class="absolute inset-0 bg-gradient-to-t from-black/45 via-transparent to-transparent pointer-events-none"></div>

                                <button
                                    v-if="trip.images.length > 1"
                                    type="button"
                                    class="absolute left-2 top-1/2 -translate-y-1/2 z-20 grid size-6 place-items-center rounded-full bg-black/45 text-white backdrop-blur-xs hover:bg-black/75 transition-all opacity-0 group-hover:opacity-100 active:scale-90 cursor-pointer shadow-sm"
                                    aria-label="Foto sebelumnya"
                                    @click.stop.prevent="prevCardImage(trip.id, trip.images.length)"
                                >
                                    <ChevronLeft class="size-3.5 stroke-[2.5]" />
                                </button>

                                <button
                                    v-if="trip.images.length > 1"
                                    type="button"
                                    class="absolute right-2 top-1/2 -translate-y-1/2 z-20 grid size-6 place-items-center rounded-full bg-black/45 text-white backdrop-blur-xs hover:bg-black/75 transition-all opacity-0 group-hover:opacity-100 active:scale-90 cursor-pointer shadow-sm"
                                    aria-label="Foto berikutnya"
                                    @click.stop.prevent="nextCardImage(trip.id, trip.images.length)"
                                >
                                    <ChevronRight class="size-3.5 stroke-[2.5]" />
                                </button>

                                <div class="absolute bottom-2.5 left-2.5 z-10">
                                    <span class="rounded-md bg-black/60 px-2 py-0.5 text-[10px] font-bold text-white backdrop-blur-xs flex items-center gap-1">
                                        <Clock class="size-3" />
                                        <span>{{ trip.duration }}</span>
                                    </span>
                                </div>

                                <div v-if="trip.images.length > 1" class="absolute bottom-2.5 right-2.5 z-10 flex items-center gap-1">
                                    <button
                                        v-for="(_, imgIdx) in trip.images"
                                        :key="imgIdx"
                                        type="button"
                                        class="h-1.5 rounded-full transition-all cursor-pointer"
                                        :class="(activeImageIndex[trip.id] || 0) === imgIdx ? 'bg-white w-3.5' : 'bg-white/50 w-1.5 hover:bg-white/80'"
                                        :aria-label="`Pilih foto ke-${imgIdx + 1}`"
                                        @click.stop.prevent="setCardImage(trip.id, imgIdx)"
                                    ></button>
                                </div>
                            </div>

                            <!-- MIDDLE DETAILS -->
                            <div class="flex flex-col justify-between p-3.5 sm:p-4 min-w-0">
                                <div>
                                    <div class="flex items-start justify-between gap-3">
                                        <Link
                                            :href="route('trips.show', { tripType: trip.type, trip: trip.slug })"
                                            class="text-sm font-extrabold text-[#173b70] hover:text-[#0088ff] transition-colors leading-snug line-clamp-2"
                                        >
                                            {{ trip.title }}
                                        </Link>

                                        <div class="text-right shrink-0">
                                            <p class="text-xs font-black text-[#0088ff] leading-none">
                                                {{ trip.rating }} <span class="text-[10px] font-normal text-slate-400">({{ trip.reviewCount }})</span>
                                            </p>
                                            <p class="text-[9.5px] font-bold text-slate-500 mt-0.5 leading-tight">
                                                {{ trip.ratingLabel }}
                                            </p>
                                        </div>
                                    </div>

                                    <div class="mt-1 flex items-center gap-1.5 text-xs">
                                        <span class="font-bold text-[#0088ff] text-[11px]">{{ trip.categoryName }}</span>
                                        <div class="flex text-amber-400">
                                            <Star v-for="s in trip.stars" :key="s" class="size-2.5 fill-amber-400" />
                                        </div>
                                    </div>

                                    <div class="mt-1 flex items-center gap-1 text-[11px] text-slate-500">
                                        <MapPin class="size-3 shrink-0 text-slate-400" />
                                        <span class="truncate">{{ trip.destination }}</span>
                                    </div>

                                    <div class="mt-1.5 flex flex-wrap gap-1">
                                        <span
                                            v-for="(fac, fIdx) in trip.facilities.slice(0, 3)"
                                            :key="fIdx"
                                            class="rounded bg-slate-100 px-1.5 py-0.5 text-[9.5px] font-medium text-slate-600"
                                        >
                                            {{ fac }}
                                        </span>
                                    </div>

                                    <div v-if="trip.promoBadge" class="mt-1.5 inline-flex items-center gap-1 rounded bg-emerald-50 px-2 py-0.5 text-[10px] font-semibold text-emerald-700 border border-emerald-200/60 max-w-full">
                                        <Gift class="size-3 text-emerald-600 shrink-0" />
                                        <span class="truncate">{{ trip.promoBadge }}</span>
                                    </div>
                                </div>

                                <div v-if="trip.reviewSnippet" class="mt-2 text-[10.5px] text-slate-500 border-t border-slate-100 pt-1.5">
                                    <p class="font-bold text-slate-700 truncate">
                                        {{ trip.reviewSnippet.author }} <span class="font-normal text-slate-400">| {{ trip.reviewSnippet.badge }}</span>
                                    </p>
                                    <p class="italic line-clamp-1 text-slate-500 mt-0.5">
                                        "{{ trip.reviewSnippet.quote }}"
                                    </p>
                                </div>
                            </div>

                            <!-- RIGHT PRICING -->
                            <div class="flex flex-col justify-between p-3.5 sm:p-4 text-right border-t md:border-t-0 md:border-l border-slate-100 bg-white shrink-0 w-full md:w-full">
                                <div class="w-full text-right">
                                    <span class="text-[10px] text-slate-400 block">Price around</span>
                                    <p v-if="trip.originalPrice" class="text-[10.5px] text-slate-400 line-through">
                                        {{ formatCurrency(trip.originalPrice) }}
                                    </p>
                                    <p class="text-base font-extrabold text-[#ff5e1f] leading-tight">
                                        {{ formatCurrency(trip.price) }}
                                    </p>
                                    <span class="text-[9.5px] text-slate-400 block font-normal">/orang/trip</span>
                                    <p class="mt-1.5 text-[10px] font-bold text-red-600">
                                        Booked {{ trip.bookedThisWeek }} times
                                    </p>
                                </div>

                                <div class="mt-3">
                                    <Link
                                        :href="route('trips.show', { tripType: trip.type, trip: trip.slug })"
                                        class="inline-flex w-full items-center justify-center rounded-full bg-[#0088ff] hover:bg-[#0074e0] px-4 py-2 text-xs font-bold text-white shadow-xs transition active:scale-95 text-center cursor-pointer"
                                    >
                                        <span>Pilih Trip</span>
                                    </Link>
                                </div>
                            </div>
                        </article>
                    </div>

                    <!-- 2. GRID VIEW (Compact 2-Column Traveloka Layout) -->
                    <div v-else class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <article
                            v-for="trip in paginatedTrips"
                            :key="trip.id"
                            class="group relative flex flex-col justify-between rounded-2xl border border-slate-200/90 bg-white shadow-2xs hover:shadow-md hover:border-blue-300/80 transition-all duration-200 overflow-hidden"
                        >
                            <!-- TOP PHOTO -->
                            <div class="relative shrink-0 overflow-hidden bg-slate-100 group/photo w-full h-40 sm:h-44">
                                <img
                                    :key="activeImageIndex[trip.id] || 0"
                                    :src="trip.images[activeImageIndex[trip.id] || 0]"
                                    :alt="trip.title"
                                    class="size-full object-cover transition-all duration-300 group-hover:scale-105"
                                    loading="lazy"
                                />
                                <div class="absolute inset-0 bg-gradient-to-t from-black/45 via-transparent to-transparent pointer-events-none"></div>

                                <button
                                    v-if="trip.images.length > 1"
                                    type="button"
                                    class="absolute left-2 top-1/2 -translate-y-1/2 z-20 grid size-6 place-items-center rounded-full bg-black/45 text-white backdrop-blur-xs hover:bg-black/75 transition-all opacity-0 group-hover:opacity-100 active:scale-90 cursor-pointer shadow-sm"
                                    aria-label="Foto sebelumnya"
                                    @click.stop.prevent="prevCardImage(trip.id, trip.images.length)"
                                >
                                    <ChevronLeft class="size-3.5 stroke-[2.5]" />
                                </button>

                                <button
                                    v-if="trip.images.length > 1"
                                    type="button"
                                    class="absolute right-2 top-1/2 -translate-y-1/2 z-20 grid size-6 place-items-center rounded-full bg-black/45 text-white backdrop-blur-xs hover:bg-black/75 transition-all opacity-0 group-hover:opacity-100 active:scale-90 cursor-pointer shadow-sm"
                                    aria-label="Foto berikutnya"
                                    @click.stop.prevent="nextCardImage(trip.id, trip.images.length)"
                                >
                                    <ChevronRight class="size-3.5 stroke-[2.5]" />
                                </button>

                                <div class="absolute bottom-2 left-2 z-10">
                                    <span class="rounded-md bg-black/60 px-1.5 py-0.5 text-[9.5px] font-bold text-white backdrop-blur-xs flex items-center gap-1">
                                        <Clock class="size-2.5" />
                                        <span>{{ trip.duration }}</span>
                                    </span>
                                </div>

                                <div v-if="trip.images.length > 1" class="absolute bottom-2 right-2 z-10 flex items-center gap-1">
                                    <button
                                        v-for="(_, imgIdx) in trip.images"
                                        :key="imgIdx"
                                        type="button"
                                        class="h-1.5 rounded-full transition-all cursor-pointer"
                                        :class="(activeImageIndex[trip.id] || 0) === imgIdx ? 'bg-white w-3' : 'bg-white/50 w-1.5 hover:bg-white/80'"
                                        :aria-label="`Pilih foto ke-${imgIdx + 1}`"
                                        @click.stop.prevent="setCardImage(trip.id, imgIdx)"
                                    ></button>
                                </div>
                            </div>

                            <!-- MIDDLE CONTENT -->
                            <div class="p-3 sm:p-3.5 flex-1 flex flex-col space-y-1.5">
                                <Link
                                    :href="route('trips.show', { tripType: trip.type, trip: trip.slug })"
                                    class="text-xs sm:text-sm font-extrabold text-[#173b70] hover:text-[#0088ff] transition-colors leading-snug line-clamp-2"
                                >
                                    {{ trip.title }}
                                </Link>

                                <div class="flex items-center justify-between text-[10.5px]">
                                    <span class="font-bold text-[#0088ff]">{{ trip.categoryName }}</span>
                                    <span class="font-bold text-slate-700">
                                        ⭐ {{ trip.rating }} <span class="text-slate-400 font-normal">({{ trip.reviewCount }})</span>
                                    </span>
                                </div>

                                <div class="flex items-center gap-1 text-[10.5px] text-slate-500">
                                    <MapPin class="size-3 shrink-0 text-slate-400" />
                                    <span class="truncate">{{ trip.destination }}</span>
                                </div>

                                <div v-if="trip.promoBadge" class="inline-flex items-center gap-1 rounded bg-emerald-50 px-2 py-0.5 text-[9.5px] font-semibold text-emerald-700 border border-emerald-200/60 truncate max-w-full">
                                    <Gift class="size-2.5 text-emerald-600 shrink-0" />
                                    <span class="truncate">{{ trip.promoBadge }}</span>
                                </div>
                            </div>

                            <!-- COMPACT BOTTOM BAR (Price on Left, Button on Right) -->
                            <div class="px-3 sm:px-3.5 py-2.5 border-t border-slate-100 bg-slate-50/50 flex items-center justify-between gap-2">
                                <div class="min-w-0">
                                    <span v-if="trip.originalPrice" class="text-[9px] text-slate-400 line-through block leading-tight">
                                        {{ formatCurrency(trip.originalPrice) }}
                                    </span>
                                    <span v-else class="text-[9px] text-slate-400 block leading-tight">Mulai dari</span>
                                    <p class="text-sm sm:text-[15px] font-black text-[#ff5e1f] leading-none">
                                        {{ formatCurrency(trip.price) }}
                                    </p>
                                    <span class="text-[8.5px] text-slate-400 font-normal">/orang/trip</span>
                                </div>

                                <Link
                                    :href="route('trips.show', { tripType: trip.type, trip: trip.slug })"
                                    class="inline-flex items-center justify-center rounded-full bg-[#0088ff] hover:bg-[#0074e0] px-4 py-1.5 text-xs font-bold text-white shadow-xs transition active:scale-95 shrink-0 cursor-pointer"
                                >
                                    <span>Pilih Trip</span>
                                </Link>
                            </div>
                        </article>
                    </div>

                    <!-- Pagination Bar (1:1 with User Screenshot Reference) -->
                    <nav
                        v-if="totalPages > 1"
                        class="mt-10 mb-4 flex items-center justify-center gap-2.5 sm:gap-3.5 select-none"
                        aria-label="Navigasi Halaman Katalog"
                    >
                        <!-- Previous Button (<) -->
                        <button
                            type="button"
                            :disabled="currentPage === 1"
                            class="grid size-9 sm:size-10 place-items-center rounded-full bg-[#f2f4f7] text-slate-400 hover:bg-slate-200 transition-all active:scale-95 disabled:opacity-40 disabled:pointer-events-none cursor-pointer shadow-2xs"
                            aria-label="Halaman Sebelumnya"
                            @click="prevPage"
                        >
                            <ChevronLeft class="size-4 sm:size-4.5 stroke-[2.5]" />
                        </button>

                        <!-- Page Number Badges & Ellipsis -->
                        <template v-for="(p, pIdx) in visiblePages" :key="pIdx">
                            <!-- Ellipsis (...) -->
                            <span
                                v-if="p === '...'"
                                class="flex size-9 sm:size-10 items-center justify-center text-sm font-bold text-slate-600 tracking-widest"
                            >
                                ...
                            </span>

                            <!-- Page Number Button -->
                            <button
                                v-else
                                type="button"
                                class="grid size-9 sm:size-10 place-items-center rounded-full text-xs sm:text-sm transition-all active:scale-95 cursor-pointer shadow-2xs font-extrabold"
                                :class="currentPage === p 
                                    ? 'bg-[#b4ec51] text-[#004bb1] ring-1 ring-lime-400/40 shadow-sm scale-105' 
                                    : 'bg-white text-[#0066d6] border border-slate-100/90 hover:bg-blue-50/60 hover:border-blue-200'"
                                :aria-current="currentPage === p ? 'page' : undefined"
                                @click="goToPage(p)"
                            >
                                {{ p }}
                            </button>
                        </template>

                        <!-- Next Button (>) -->
                        <button
                            type="button"
                            :disabled="currentPage === totalPages"
                            class="grid size-9 sm:size-10 place-items-center rounded-full bg-[#dff1ff] text-[#0066d6] hover:bg-[#cbe8ff] transition-all active:scale-95 disabled:opacity-40 disabled:pointer-events-none cursor-pointer shadow-2xs"
                            aria-label="Halaman Berikutnya"
                            @click="nextPage"
                        >
                            <ChevronRight class="size-4 sm:size-4.5 stroke-[2.5]" />
                        </button>
                    </nav>
                </div>

                <!-- Empty State if no trips match -->
                <div v-else class="rounded-2xl border border-dashed border-slate-200 bg-white p-12 text-center shadow-xs">
                    <div class="mx-auto grid size-14 place-items-center rounded-full bg-blue-50 text-[#0088ff]">
                        <Compass class="size-7" />
                    </div>
                    <h3 class="mt-4 text-base font-extrabold text-slate-800">Tidak ada paket trip yang cocok</h3>
                    <p class="mt-1 text-xs text-slate-500 max-w-sm mx-auto">
                        Coba sesuaikan rentang harga, pilih kategori lain, atau reset semua filter pencarian.
                    </p>
                    <button
                        type="button"
                        class="mt-4 inline-flex items-center gap-1.5 rounded-xl bg-[#0088ff] px-4 py-2 text-xs font-bold text-white shadow-xs transition hover:bg-[#0074e0] cursor-pointer"
                        @click="resetAllFilters"
                    >
                        <RotateCcw class="size-3.5" />
                        <span>Reset Semua Filter</span>
                    </button>
                </div>

            </main>
        </div>

        <!-- Explore Promos & Discount Coupons Section (Traveloka Inspired) -->
        <CatalogPromoSection />

        <!-- ==================================================================== -->
        <!-- MOBILE STICKY FLOATING FILTER BUTTON (1-Thumb Access on Mobile)       -->
        <!-- ==================================================================== -->
        <div class="fixed bottom-6 left-1/2 -translate-x-1/2 z-30 lg:hidden pointer-events-auto">
            <button
                type="button"
                class="inline-flex items-center gap-2.5 rounded-full bg-[#0088ff] hover:bg-[#0074e0] text-white px-5 py-3 text-xs font-bold shadow-[0_8px_24px_rgba(0,136,255,0.45)] ring-2 ring-white active:scale-95 transition-all cursor-pointer"
                @click="isMobileFilterOpen = true"
            >
                <SlidersHorizontal class="size-4" />
                <span>Filter & Urutkan</span>
                <span v-if="activeFilterCount > 0" class="flex size-5 items-center justify-center rounded-full bg-white text-[10.5px] font-black text-[#0088ff]">
                    {{ activeFilterCount }}
                </span>
            </button>
        </div>

        <!-- ==================================================================== -->
        <!-- MOBILE FILTER DRAWER MODAL (Traveloka / Airbnb Style)                -->
        <!-- ==================================================================== -->
        <Teleport to="body">
            <Transition
                enter-active-class="transition-opacity duration-250 ease-out"
                enter-from-class="opacity-0"
                enter-to-class="opacity-100"
                leave-active-class="transition-opacity duration-200 ease-in"
                leave-from-class="opacity-100"
                leave-to-class="opacity-0"
            >
                <div
                    v-if="isMobileFilterOpen"
                    class="fixed inset-0 z-[100] flex items-end sm:items-center justify-center bg-slate-900/60 backdrop-blur-xs p-0 sm:p-4"
                    role="dialog"
                    aria-modal="true"
                    aria-label="Filter Hasil Pencarian"
                    @click.self="isMobileFilterOpen = false"
                >
                    <div
                        class="w-full sm:max-w-lg max-h-[90dvh] flex flex-col rounded-t-3xl sm:rounded-3xl bg-white shadow-2xl overflow-hidden"
                    >
                        <!-- Modal Header -->
                        <div class="flex items-center justify-between border-b border-slate-100 px-5 py-4 shrink-0 bg-white">
                            <div class="flex items-center gap-2">
                                <SlidersHorizontal class="size-4.5 text-[#0088ff]" />
                                <h3 class="text-base font-extrabold text-slate-800">Filter Paket Trip</h3>
                                <span v-if="activeFilterCount > 0" class="rounded-full bg-blue-50 text-[#0088ff] px-2 py-0.5 text-[10px] font-bold">
                                    {{ activeFilterCount }} aktif
                                </span>
                            </div>
                            <div class="flex items-center gap-3">
                                <button
                                    type="button"
                                    class="text-xs font-bold text-[#0088ff] hover:underline"
                                    @click="resetAllFilters"
                                >
                                    Reset
                                </button>
                                <button
                                    type="button"
                                    class="grid size-8 place-items-center rounded-full bg-slate-100 text-slate-600 hover:bg-slate-200 transition"
                                    aria-label="Tutup filter"
                                    @click="isMobileFilterOpen = false"
                                >
                                    <X class="size-4.5" />
                                </button>
                            </div>
                        </div>

                        <!-- Modal Scrollable Content -->
                        <div class="flex-1 overflow-y-auto p-5 space-y-4 [scrollbar-width:thin]">
                            <!-- 1. Pilihan Tanggal Trip -->
                            <div class="rounded-2xl border border-slate-200/90 bg-white p-4">
                                <div class="flex items-center justify-between">
                                    <h4 class="text-xs font-bold text-slate-800 uppercase tracking-wider">Pilihan Tanggal Trip</h4>
                                    <span class="text-[10px] text-slate-400 font-medium">Cepat</span>
                                </div>
                                <div class="mt-3 grid grid-cols-2 gap-2">
                                    <button
                                        v-for="date in dateIdeas"
                                        :key="date.id"
                                        type="button"
                                        class="flex flex-col items-start rounded-xl border p-2.5 text-left transition-all"
                                        :class="selectedDateIdea === date.id 
                                            ? 'border-[#0088ff] bg-blue-50/70 text-[#0066d6] ring-1 ring-[#0088ff]' 
                                            : 'border-slate-200/80 bg-slate-50/50 text-slate-700'"
                                        @click="selectDateIdea(date.id)"
                                    >
                                        <span class="text-xs font-bold">{{ date.label }}</span>
                                        <span class="mt-0.5 text-[10px] text-slate-500 font-medium">{{ date.dates }}</span>
                                    </button>
                                </div>
                            </div>

                            <!-- 2. Rentang Harga -->
                            <div class="rounded-2xl border border-slate-200/90 bg-white p-4">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <h4 class="text-xs font-bold text-slate-800 uppercase tracking-wider">Rentang Harga</h4>
                                        <p class="text-[10.5px] text-slate-400">Per orang, per paket trip</p>
                                    </div>
                                    <button
                                        type="button"
                                        class="text-xs font-bold text-[#0088ff] hover:underline"
                                        @click="priceMin = 0; priceMax = 15000000"
                                    >
                                        Reset
                                    </button>
                                </div>
                                <div class="mt-4 px-1">
                                    <input
                                        v-model.number="priceMax"
                                        type="range"
                                        min="300000"
                                        max="15000000"
                                        step="100000"
                                        class="w-full h-2 bg-slate-200 rounded-lg appearance-none cursor-pointer accent-[#0088ff]"
                                    />
                                </div>
                                <div class="mt-3 grid grid-cols-2 gap-2">
                                    <div class="rounded-xl border border-slate-200/90 bg-slate-50/50 px-3 py-2">
                                        <span class="block text-[9px] font-bold text-slate-400 uppercase">Min</span>
                                        <span class="text-xs font-bold text-slate-800">IDR 0</span>
                                    </div>
                                    <div class="rounded-xl border border-slate-200/90 bg-slate-50/50 px-3 py-2">
                                        <span class="block text-[9px] font-bold text-slate-400 uppercase">Max</span>
                                        <span class="text-xs font-bold text-slate-800">IDR {{ formatNumberOnly(priceMax) }}</span>
                                    </div>
                                </div>
                            </div>

                            <!-- 3. Tipe & Kategori Trip -->
                            <div class="rounded-2xl border border-slate-200/90 bg-white p-4">
                                <h4 class="text-xs font-bold text-slate-800 uppercase tracking-wider mb-3">Tipe & Kategori Trip</h4>
                                <div class="space-y-3">
                                    <label class="flex items-center gap-3 text-xs text-slate-700 cursor-pointer">
                                        <input v-model="selectedTripTypes" type="checkbox" value="open-trip" class="size-4.5 rounded border-slate-300 text-[#0088ff] focus:ring-[#0088ff]" />
                                        <span class="font-medium">Open Trip (Gabungan Hemat)</span>
                                    </label>
                                    <label class="flex items-center gap-3 text-xs text-slate-700 cursor-pointer">
                                        <input v-model="selectedTripTypes" type="checkbox" value="private-trip" class="size-4.5 rounded border-slate-300 text-[#0088ff] focus:ring-[#0088ff]" />
                                        <span class="font-medium">Private Trip (Eksklusif & Bebas)</span>
                                    </label>
                                    <label class="flex items-center gap-3 text-xs text-slate-700 cursor-pointer">
                                        <input v-model="selectedTripTypes" type="checkbox" value="snorkeling" class="size-4.5 rounded border-slate-300 text-[#0088ff] focus:ring-[#0088ff]" />
                                        <span class="font-medium">Wisata Bahari & Snorkeling</span>
                                    </label>
                                    <label class="flex items-center gap-3 text-xs text-slate-700 cursor-pointer">
                                        <input v-model="selectedTripTypes" type="checkbox" value="mountain" class="size-4.5 rounded border-slate-300 text-[#0088ff] focus:ring-[#0088ff]" />
                                        <span class="font-medium">Pendakian & Gunung Bromo</span>
                                    </label>
                                </div>
                            </div>

                            <!-- 4. Promo & Diskon -->
                            <div class="rounded-2xl border border-slate-200/90 bg-white p-4">
                                <h4 class="text-xs font-bold text-slate-800 uppercase tracking-wider mb-3">Promo & Diskon</h4>
                                <div class="space-y-3">
                                    <label class="flex items-center gap-3 text-xs text-slate-700 cursor-pointer">
                                        <input v-model="selectedPromos" type="checkbox" value="for-you" class="size-4.5 rounded border-slate-300 text-[#0088ff] focus:ring-[#0088ff]" />
                                        <span class="font-medium">Promo for You (8% OFF)</span>
                                    </label>
                                    <label class="flex items-center gap-3 text-xs text-slate-700 cursor-pointer">
                                        <input v-model="selectedPromos" type="checkbox" value="domestic" class="size-4.5 rounded border-slate-300 text-[#0088ff] focus:ring-[#0088ff]" />
                                        <span class="font-medium">Promo Domestik Spesial</span>
                                    </label>
                                    <label class="flex items-center gap-3 text-xs text-slate-700 cursor-pointer">
                                        <input v-model="selectedPromos" type="checkbox" value="extra" class="size-4.5 rounded border-slate-300 text-[#0088ff] focus:ring-[#0088ff]" />
                                        <span class="font-medium">Extra Cashback TapakPoints</span>
                                    </label>
                                </div>
                            </div>

                            <!-- 5. Fasilitas Trip -->
                            <div class="rounded-2xl border border-slate-200/90 bg-white p-4">
                                <h4 class="text-xs font-bold text-slate-800 uppercase tracking-wider mb-3">Fasilitas Trip</h4>
                                <div class="space-y-3">
                                    <label class="flex items-center gap-3 text-xs text-slate-700 cursor-pointer">
                                        <input v-model="selectedFacilities" type="checkbox" value="transport" class="size-4.5 rounded border-slate-300 text-[#0088ff] focus:ring-[#0088ff]" />
                                        <span class="font-medium">Transportasi AC PP</span>
                                    </label>
                                    <label class="flex items-center gap-3 text-xs text-slate-700 cursor-pointer">
                                        <input v-model="selectedFacilities" type="checkbox" value="meals" class="size-4.5 rounded border-slate-300 text-[#0088ff] focus:ring-[#0088ff]" />
                                        <span class="font-medium">Makan & Konsumsi Termasuk</span>
                                    </label>
                                    <label class="flex items-center gap-3 text-xs text-slate-700 cursor-pointer">
                                        <input v-model="selectedFacilities" type="checkbox" value="snorkeling" class="size-4.5 rounded border-slate-300 text-[#0088ff] focus:ring-[#0088ff]" />
                                        <span class="font-medium">Alat Snorkeling Lengkap</span>
                                    </label>
                                    <label class="flex items-center gap-3 text-xs text-slate-700 cursor-pointer">
                                        <input v-model="selectedFacilities" type="checkbox" value="drone" class="size-4.5 rounded border-slate-300 text-[#0088ff] focus:ring-[#0088ff]" />
                                        <span class="font-medium">Dokumentasi Drone & GoPro</span>
                                    </label>
                                </div>
                            </div>
                        </div>

                        <!-- Modal Sticky Footer Action -->
                        <div class="border-t border-slate-100 p-4 bg-white shrink-0 grid grid-cols-2 gap-3">
                            <button
                                type="button"
                                class="rounded-xl border border-slate-200 bg-white py-3 text-xs font-bold text-slate-700 transition hover:bg-slate-50"
                                @click="resetAllFilters"
                            >
                                Reset Filter
                            </button>
                            <button
                                type="button"
                                class="rounded-xl bg-[#0088ff] hover:bg-[#0074e0] py-3 text-xs font-bold text-white shadow-md transition active:scale-95"
                                @click="applyMobileFilter"
                            >
                                Terapkan ({{ filteredTrips.length }})
                            </button>
                        </div>
                    </div>
                </div>
            </Transition>
        </Teleport>
    </section>
</template>
