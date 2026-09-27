<script setup>
import { computed, onMounted, onUnmounted, ref, watch } from 'vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { route } from 'ziggy-js';
import {
    Search,
    ChevronRight,
    Compass,
    TentTree,
    Crown,
    Utensils,
    ShoppingBag,
    Wallet,
    UserCheck,
    ShieldAlert,
    UsersRound,
    LayoutGrid,
    CheckCircle2,
    X,
    ThumbsUp,
    ThumbsDown,
    Phone,
    Mail,
    MessageCircle,
    ArrowRight,
    HelpCircle,
    Clock,
    AlertCircle,
    ShieldCheck,
    Share2,
    Check,
} from 'lucide-vue-next';
import MainNavigation from '../Components/Shared/MainNavigation.vue';
import MainFooter from '../Components/Shared/MainFooter.vue';

// Search Query
const searchQuery = ref('');

// Active Product Filter: 'all' | 'open-trip' | 'private-trip' | 'culinary-po' | 'wallet' | 'profile' | 'anti-pungli' | 'partners'
const activeProduct = ref('all');

// Product Categories (Explore by Product) - 1:1 with Traveloka Circular Icon Grid Style
const productCategories = [
    {
        id: 'open-trip',
        name: 'Open Trip',
        shortName: 'Open Trip',
        icon: TentTree,
        colorClass: 'bg-sky-50 text-[#0088ff] border-sky-100 group-hover:bg-[#0088ff] group-hover:text-white',
        activeClass: 'bg-[#0088ff] text-white ring-4 ring-sky-100 shadow-md',
        badge: 'Trip Gabungan',
    },
    {
        id: 'private-trip',
        name: 'Private Trip',
        shortName: 'Private Trip',
        icon: Crown,
        colorClass: 'bg-emerald-50 text-emerald-600 border-emerald-100 group-hover:bg-emerald-600 group-hover:text-white',
        activeClass: 'bg-emerald-600 text-white ring-4 ring-emerald-100 shadow-md',
        badge: 'Trip Eksklusif',
    },
    {
        id: 'culinary-po',
        name: 'PO Oleh-Oleh & Kuliner',
        shortName: 'PO Oleh-Oleh',
        icon: Utensils,
        colorClass: 'bg-amber-50 text-amber-600 border-amber-100 group-hover:bg-amber-500 group-hover:text-white',
        activeClass: 'bg-amber-500 text-white ring-4 ring-amber-100 shadow-md',
        badge: 'Kuliner Daerah',
    },
    {
        id: 'wallet',
        name: 'TapakWallet & Bayar',
        shortName: 'TapakWallet',
        icon: Wallet,
        colorClass: 'bg-indigo-50 text-indigo-600 border-indigo-100 group-hover:bg-indigo-600 group-hover:text-white',
        activeClass: 'bg-indigo-600 text-white ring-4 ring-indigo-100 shadow-md',
        badge: 'Transaksi Aman',
    },
    {
        id: 'profile',
        name: 'Profil & Keamanan KYC',
        shortName: 'Akun & KYC',
        icon: UserCheck,
        colorClass: 'bg-blue-50 text-blue-600 border-blue-100 group-hover:bg-blue-600 group-hover:text-white',
        activeClass: 'bg-blue-600 text-white ring-4 ring-blue-100 shadow-md',
        badge: 'Data Wisatawan',
    },
    {
        id: 'anti-pungli',
        name: 'Lapor Pungli & Standar',
        shortName: 'Lapor Pungli',
        icon: ShieldAlert,
        colorClass: 'bg-rose-50 text-rose-600 border-rose-100 group-hover:bg-rose-600 group-hover:text-white',
        activeClass: 'bg-rose-600 text-white ring-4 ring-rose-100 shadow-md',
        badge: 'Anti Biaya Siluman',
    },
    {
        id: 'partners',
        name: 'Mitra & Pemandu Lokal',
        shortName: 'Mitra & Guide',
        icon: UsersRound,
        colorClass: 'bg-teal-50 text-teal-600 border-teal-100 group-hover:bg-teal-600 group-hover:text-white',
        activeClass: 'bg-teal-600 text-white ring-4 ring-teal-100 shadow-md',
        badge: 'Lisensi HPI',
    },
    {
        id: 'all',
        name: 'Semua Kategori',
        shortName: 'Semua Produk',
        icon: LayoutGrid,
        colorClass: 'bg-slate-100 text-slate-700 border-slate-200 group-hover:bg-slate-800 group-hover:text-white',
        activeClass: 'bg-slate-800 text-white ring-4 ring-slate-200 shadow-md',
        badge: 'Pusat Bantuan',
    },
];

// Rich Help Articles Database tailored specifically to TapakLokal 3 Products + Platform
const articles = [
    // 1. OPEN TRIP
    {
        id: 'open-trip-quota',
        category: 'open-trip',
        categoryLabel: 'Open Trip',
        title: 'Bagaimana jika kuota minimum peserta Open Trip tidak terpenuhi hingga batas waktu?',
        summary: 'Garansi 100% uang kembali (Full Refund) atau pilihan reschedule & penyesuaian rombongan kecil tanpa ribet.',
        content: `
Setiap paket **Open Trip** di TapakLokal memiliki kuota minimum peserta (biasanya 5–10 orang) agar perjalanan dapat berlangsung optimal dan ekonomis.

Jika hingga batas waktu konfirmasi (**H-3 sebelum keberangkatan**) kuota minimum belum tercapai, sistem TapakLokal dan mitra operator resmi akan memberikan **3 Solusi Terbaik**:

1. **Garansi 100% Pengembalian Dana (Full Refund)**:
   Dana Anda akan dikembalikan secara penuh 100% tanpa potongan biaya administrasi apa pun ke saldo TapakWallet Anda (instan) atau rekening bank asal (1–2 hari kerja).
2. **Reschedule Tanggal Bebas Biaya**:
   Anda dapat memilih tanggal keberangkatan berikutnya pada destinasi yang sama tanpa dikenakan biaya perubahan jadwal.
3. **Penyesuaian Biaya Rombongan Kecil (Small Group Option)**:
   Jika seluruh peserta yang sudah mendaftar sepakat untuk tetap berangkat walau kuota kurang, mitra dapat menawarkan penyesuaian biaya minim agar trip tetap jalan sesuai rencana.

> **Tips:** Anda akan menerima notifikasi otomatis via WhatsApp dan Email terkait status kuota Open Trip Anda secara berkala.
        `,
        isPopular: true,
        tags: ['kuota', 'batal', 'refund', 'open trip', 'peserta'],
    },
    {
        id: 'open-trip-solo-traveler',
        category: 'open-trip',
        categoryLabel: 'Open Trip',
        title: 'Apakah peserta Solo Traveler (sendirian) bisa ikut Open Trip dan bagaimana pembagian kamarnya?',
        summary: 'Bisa banget! Lebih dari 65% peserta Open Trip TapakLokal adalah solo traveler dengan pembagian kamar terpisah gender.',
        content: `
Sangat bisa! Konsep utama **Open Trip TapakLokal** adalah menghubungkan para penjelajah lokal dari berbagai daerah: *"Berangkat gak kenal, pulang jadi saudara"*.

- **Pembagian Kamar / Tenda**: Pembagian kamar hotel atau tenda glamping akan dipisahkan secara ketat sesuai jenis kelamin (Pria dengan Pria, Wanita dengan Wanita).
- **Solo Traveler Room Upgrade**: Jika Anda menginginkan privasi satu kamar sendiri *(Single Supplement)*, Anda dapat memilih opsi upgrade kamar saat proses pemesanan.
- **Teman & Dokumentasi**: Tour Leader kami selalu siap membantu dokumentasi foto & video estetik selama perjalanan agar solo traveler tetap memiliki kenangan liburan terbaik.
        `,
        isPopular: false,
        tags: ['solo traveler', 'kamar', 'tenda', 'teman baru', 'open trip'],
    },
    {
        id: 'open-trip-meeting-point',
        category: 'open-trip',
        categoryLabel: 'Open Trip',
        title: 'Bagaimana cara menentukan Titik Kumpul (Meeting Point) dan kapan grup koordinasi dibuat?',
        summary: 'Titik kumpul terpusat di lokasi strategis seperti stasiun/bandara dan grup WhatsApp dibuat H-1 keberangkatan.',
        content: `
Setiap paket Open Trip memiliki pilihan **Titik Kumpul Resmi (Meeting Point)** yang tercantum jelas di halaman detail trip:

1. **Pilihan Titik Kumpul**:
   Biasanya berlokasi di titik transportasi utama (misal: Stasiun Pasar Senen, Bandara Juanda Surabaya, Terminal Baranangsiang Bogor, atau Rest Area KM 57).
2. **Grup Koordinasi WhatsApp**:
   Koordinator tim lapangan / Tour Leader resmi akan mengundang Anda ke grup WhatsApp koordinasi selambat-lambatnya **H-1 pukul 12.00 WIB**.
3. **Ketepatan Waktu**:
   Peserta diharapkan tiba di titik kumpul maksimal 30 menit sebelum jam keberangkatan yang telah ditentukan.
        `,
        isPopular: true,
        tags: ['titik kumpul', 'meeting point', 'whatsapp', 'jadwal', 'stasiun'],
    },

    // 2. PRIVATE TRIP
    {
        id: 'private-trip-custom',
        category: 'private-trip',
        categoryLabel: 'Private Trip',
        title: 'Bagaimana cara mengajukan custom rute, tanggal keberangkatan, dan titik jemput untuk Private Trip?',
        summary: 'Fleksibilitas penuh untuk liburan keluarga, rombongan teman, atau outing kantor dengan penjemputan door-to-door.',
        content: `
Paket **Private Trip** di TapakLokal dirancang khusus untuk Anda yang menginginkan kebebasan dan privasi maksimal bersama rombongan pribadi.

**Keunggulan Private Trip TapakLokal:**
- **Bebas Tentukan Tanggal**: Berangkat kapan saja setiap hari tanpa menunggu kuota peserta lain.
- **Titik Jemput Fleksibel (*Door-to-Door*)**: Penjemputan langsung di rumah, hotel, stasiun, atau kantor Anda di area penjemputan mitra.
- **Kustomisasi Itinerary & Durasi**: Ingin menambah spot sunset, durasi santai di kafe lokal, atau memilih resto kuliner khas tertentu? Diskusikan langsung dengan tim kami!

**Cara Mengajukan Custom Private Trip:**
1. Buka halaman katalog dan pilih paket berlabel **Private Trip**.
2. Klik tombol **"Chat Admin / Kustomisasi Trip"** pada profil mitra.
3. Sebutkan jumlah peserta, tanggal yang diinginkan, dan preferensi destinasi Anda.
4. Tim mitra resmi TapakLokal akan menerbitkan rincian invoice khusus ke akun TapakLokal Anda.
        `,
        isPopular: true,
        tags: ['private trip', 'custom', 'keluarga', 'door to door', 'jadwal'],
    },
    {
        id: 'private-trip-capacity',
        category: 'private-trip',
        categoryLabel: 'Private Trip',
        title: 'Berapa batas minimal dan maksimal jumlah peserta untuk memesan Private Trip?',
        summary: 'Mulai dari 2 orang hingga gathering rombongan besar (50+ orang) dengan armada eksekutif berstandar pariwisata.',
        content: `
Private Trip di TapakLokal tidak memiliki batasan kaku:
- **Private Couple / Family (2–6 Orang)**: Menggunakan armada nyaman seperti Toyota Innova Reborn, Avanza Veloz, atau HiAce Commuter.
- **Medium Group (7–15 Orang)**: Menggunakan Toyota HiAce Premio atau Isuzu Elf Long Luxury.
- **Corporate & Community Gathering (20–100+ Orang)**: Menggunakan Medium Bus hingga Big Bus Pariwisata HDD/SHD dengan fasilitas audio karaoke, charging port, dan asuransi penuh.
        `,
        isPopular: false,
        tags: ['kapasitas', 'jumlah orang', 'bus', 'hiace', 'private trip'],
    },

    // 3. OPEN PO OLEH-OLEH & KULINER KHAS
    {
        id: 'culinary-po-how-it-works',
        category: 'culinary-po',
        categoryLabel: 'PO Oleh-Oleh',
        title: 'Bagaimana sistem pemesanan & pengiriman Open PO Oleh-Oleh & Kuliner Khas Daerah?',
        summary: 'Titip beli kuliner legendaris dan suvenir autentik langsung dari UMKM lokal destinasi trip, dijamin asli dan fresh.',
        content: `
Fitur **Open PO Oleh-Oleh & Kuliner Khas** di TapakLokal menghubungkan traveler dan pecinta kuliner nusantara langsung dengan UMKM lokal terbaik di setiap daerah tujuan trip (cth: Peuyeum Bandung, Keripik Tempe Malang, Bakpia Kukus Jogja, Kopi Ijen, Sambal Roa, Kain Tenun Tradisional).

**Alur Pemesanan:**
1. **Pilih Produk PO**: Buka menu *Kuliner Lokal* atau tab *Open PO Oleh-Oleh* pada destinasi trip yang Anda pilih.
2. **Pilih Metode Penerimaan**:
   - **Diserahkan saat Trip**: Jika Anda juga memesan trip, pesanan oleh-oleh Anda akan dibawakan langsung oleh Tour Leader saat trip berakhir.
   - **Kirim ke Rumah (Jastip Pengiriman)**: Pesanan dikirim langsung ke alamat rumah Anda melalui ekspedisi kurir kilat terpercaya.
3. **Pembayaran Aman**: Dana Anda ditahan aman di sistem TapakLokal dan baru diteruskan ke UMKM setelah produk terkonfirmasi diterima dengan baik.
        `,
        isPopular: true,
        tags: ['open po', 'oleh-oleh', 'kuliner', 'jastip', 'makanan khas'],
    },
    {
        id: 'culinary-po-freshness-guarantee',
        category: 'culinary-po',
        categoryLabel: 'PO Oleh-Oleh',
        title: 'Bagaimana jaminan kesegaran makanan dan kebijakan retur jika oleh-oleh rusak saat diterima?',
        summary: 'Standar kemasan vacuum/insulasi khusus dan Garansi 100% Uang Kembali jika produk diterima rusak atau basi.',
        content: `
Kualitas dan keaslian produk adalah prioritas utama TapakLokal:
- **Standar Kemasan Makanan Basah**: Makanan basah khas daerah dikemas menggunakan teknologi *vacuum pack* dan insulasi *thermal bag* dengan *ice gel* untuk menjaga suhu dan ketahanan rasa.
- **Produk UMKM Terverifikasi**: Hanya UMKM terdaftar yang memiliki izin edar P-IRT / Sertifikasi Halal yang dapat menjual produk pangan di TapakLokal.
- **Garansi 100% Ganti Baru / Refund**: Jika paket makanan sampai dalam kondisi rusak, pecah, atau basi akibat keterlambatan, sertakan video *unboxing* dalam waktu 1x24 jam untuk klaim penggantian dana penuh 100%.
        `,
        isPopular: true,
        tags: ['kesegaran', 'garansi', 'rusak', 'retur', 'umkm'],
    },

    // 4. TAPAKWALLET & PEMBAYARAN
    {
        id: 'wallet-methods',
        category: 'wallet',
        categoryLabel: 'TapakWallet & Bayar',
        title: 'Metode pembayaran apa saja yang didukung dan bagaimana cara bayar dengan TapakWallet?',
        summary: 'Dukungan QRIS instan, Virtual Account semua bank, E-Wallet, minimarket, serta TapakWallet bebas biaya admin.',
        content: `
TapakLokal menyediakan pilihan pembayaran resmi terlengkap dan terenkripsi 256-bit:

1. **TapakWallet**:
   - Saldo dompet digital terintegrasi TapakLokal.
   - Checkout 1-klik instan tanpa biaya admin transaksi.
   - Penampungan dana refund tercepat (langsung cair dalam hitungan detik).
2. **QRIS (Quick Response Code Indonesian Standard)**:
   - Berlaku untuk semua aplikasi m-banking (BCA, Mandiri Livin, BRImo, BNI, CIMB) dan e-wallet (GoPay, OVO, Dana, ShopeePay, LinkAja).
3. **Virtual Account Bank (Otomatis Terverifikasi)**:
   - Bank BCA, Bank Mandiri, Bank BNI, Bank BRI, Bank Permata.
4. **Gerai Retail Modern**:
   - Pembayaran tunai melalui kasir Indomaret dan Alfamart di seluruh Indonesia.
        `,
        isPopular: true,
        tags: ['pembayaran', 'qris', 'virtual account', 'tapakwallet', 'bca'],
    },
    {
        id: 'wallet-refund-flow',
        category: 'wallet',
        categoryLabel: 'TapakWallet & Bayar',
        title: 'Bagaimana alur dan estimasi waktu proses pengembalian dana (Refund)?',
        summary: 'Refund ke TapakWallet cair dalam 1-5 menit; refund ke rekening bank diproses 1-3 hari kerja.',
        content: `
Jika pengajuan pembatalan atau refund Anda telah disetujui sesuai syarat dan ketentuan:
- **Pencairan ke Saldo TapakWallet**:
  Proses instan **1–5 Menit**. Saldo dapat langsung digunakan untuk memesan trip lain atau dicairkan ke rekening bank pribadi Anda kapan saja.
- **Pencairan ke Rekening Bank Asal (Transfer Bank)**:
  Memerlukan waktu **1–3 Hari Kerja** (tidak termasuk hari libur dan akhir pekan) tergantung kliring bank tujuan.
- **Pencairan Kartu Kredit / E-Wallet**:
  Memerlukan waktu **3–7 Hari Kerja** sesuai kebijakan penerbit kartu dan operator dompet digital.
        `,
        isPopular: true,
        tags: ['refund', 'pengembalian dana', 'saldo', 'rekening', 'estimasi'],
    },

    // 5. PROFIL & KEAMANAN KYC
    {
        id: 'profile-kyc-importance',
        category: 'profile',
        categoryLabel: 'Profil & Keamanan',
        title: 'Mengapa saya perlu melakukan Verifikasi Identitas (KYC) di akun TapakLokal?',
        summary: 'Diperlukan untuk aktivasi asuransi keselamatan jiwa resmi dan syarat manifest izin masuk kawasan wisata alam.',
        content: `
Verifikasi Identitas (*Know Your Customer / KYC*) di TapakLokal bukan sekadar formalitas, melainkan syarat krusial untuk keselamatan Anda:
1. **Aktivasi Asuransi Jiwa & Kecelakaan**:
   Setiap tiket trip di TapakLokal terhubung dengan polis asuransi perjalanan resmi yang membutuhkan data nama sesuai KTP/Paspor dan NIK yang valid.
2. **Izin Masuk Kawasan Konservasi & Taman Nasional**:
   Destinasi seperti Gunung Bromo, Taman Nasional Komodo, Ujung Kulon, dan Dieng mewajibkan data manifest pengunjung resmi sebelum memasuki pos perizinan.
3. **Keamanan Transaksi & Saldo**:
   Mencegah penyalahgunaan akun, pencurian poin voucher, dan penipuan digital. Data pribadi Anda dienkripsi ketat sesuai standar UU Perlindungan Data Pribadi (PDP).
        `,
        isPopular: false,
        tags: ['kyc', 'ktp', 'asuransi', 'keamanan akun', 'manifest'],
    },

    // 6. ANTI-PUNGLI & TRANSPARANSI BIAYA
    {
        id: 'anti-pungli-policy',
        category: 'anti-pungli',
        categoryLabel: 'Lapor Pungli',
        title: 'Bagaimana komitmen TapakLokal terhadap transparansi harga dan pencegahan Pungutan Liar (Pungli)?',
        summary: 'Semua harga paket adalah harga All-In transparan. Tidak ada biaya siluman atau pungutan liar tak berizin.',
        content: `
TapakLokal didirikan dengan komitmen kuat menciptakan ekosistem pariwisata lokal yang beretika, transparan, dan terpercaya:

- **Kebijakan Harga All-In Transparan**:
  Semua biaya tercantum jelas: tiket masuk resmi destinasi wisata, retribusi daerah, biaya parkir, biaya kapal penyeberangan, dan tips pemandu telah diatur di dalam rincian paket tanpa ada pungutan misterius di lokasi.
- **Kanal Lapor Pungli Cepat**:
  Jika Anda menemukan oknum atau pihak di lapangan yang meminta pungutan liar di luar rincian *Exclude* paket, Anda dapat langsung menekan tombol **"Lapor Pungli"** di aplikasi atau menghubungi Hotline Darurat TapakLokal.
- **Sanksi Mitra & Kompensasi**:
  Mitra yang terbukti melakukan pungli akan dikenai sanksi pemutusan kemitraan, dan peserta berhak menerima kompensasi penggantian dana.
        `,
        isPopular: true,
        tags: ['anti pungli', 'pungli', 'transparansi harga', 'lapor', 'biaya siluman'],
    },

    // 7. MITRA & PEMANDU LOKAL
    {
        id: 'partners-guide-standards',
        category: 'partners',
        categoryLabel: 'Mitra & Pemandu',
        title: 'Apa standar verifikasi bagi Pemandu Wisata dan Operator Trip yang terdaftar di TapakLokal?',
        summary: 'Semua mitra wajib berbadan hukum legal, memiliki lisensi resmi HPI, dan armada lulus uji kelaikan jalan.',
        content: `
Kami menerapkan proses kurasi dan sertifikasi ketat sebelum mitra operator dapat membuka open trip maupun private trip di platform TapakLokal:
1. **Legalitas Usaha**: Wajib berbadan hukum resmi (PT/CV/Koperasi Wisata) dengan NIB dan TDUP pariwisata yang aktif.
2. **Sertifikasi Pemandu (Tour Leader & Guide)**: Wajib memiliki sertifikat kompetensi pemandu wisata dari BNSP / lisensi HPI (Himpunan Pramuwisata Indonesia).
3. **Standar Armada Pariwisata**: Kendaraan pariwisata wajib memiliki izin trayek pariwisata, uji berkala KIR aktif, sabuk pengaman berfungsi, serta supir berlisensi SIM pariwisata.
4. **Pelatihan *First Aid* (P3K)**: Pemandu dibekali keterampilan tanggap darurat pertolongan pertama di alam bebas (laut dan gunung).
        `,
        isPopular: false,
        tags: ['mitra', 'pemandu', 'guide', 'hpi', 'legalitas'],
    },
];

// Computed Filtered Articles
const filteredArticles = computed(() => {
    let result = articles;

    // Filter by product category
    if (activeProduct.value !== 'all') {
        result = result.filter((a) => a.category === activeProduct.value);
    }

    // Filter by search query
    if (searchQuery.value.trim()) {
        const q = searchQuery.value.toLowerCase().trim();
        result = result.filter((a) => {
            return (
                a.title.toLowerCase().includes(q) ||
                a.summary.toLowerCase().includes(q) ||
                a.content.toLowerCase().includes(q) ||
                a.categoryLabel.toLowerCase().includes(q) ||
                a.tags.some((t) => t.toLowerCase().includes(q))
            );
        });
    }

    return result;
});

// Popular Topics (displayed in left column when no active search)
const popularArticles = computed(() => {
    if (searchQuery.value.trim()) {
        return filteredArticles.value;
    }
    if (activeProduct.value !== 'all') {
        return filteredArticles.value;
    }
    return articles.filter((a) => a.isPopular);
});

// Modal / Drawer state for reading an article
const selectedArticle = ref(null);
const feedbackGiven = ref({}); // articleId -> 'yes' | 'no'
const isCopied = ref(false);

const openArticle = (article) => {
    selectedArticle.value = article;
    document.body.style.overflow = 'hidden';
};

const closeArticle = () => {
    selectedArticle.value = null;
    document.body.style.overflow = '';
};

const giveFeedback = (articleId, type) => {
    feedbackGiven.value = { ...feedbackGiven.value, [articleId]: type };
};

const copyArticleLink = () => {
    navigator.clipboard.writeText(window.location.href);
    isCopied.value = true;
    setTimeout(() => {
        isCopied.value = false;
    }, 2500);
};

// Contact Support Modal State
const isContactModalOpen = ref(false);
const openContactModal = () => {
    isContactModalOpen.value = true;
    document.body.style.overflow = 'hidden';
};
const closeContactModal = () => {
    isContactModalOpen.value = false;
    document.body.style.overflow = '';
};

// Keyboard listener for Escape key
const handleKeyDown = (e) => {
    if (e.key === 'Escape') {
        if (selectedArticle.value) closeArticle();
        if (isContactModalOpen.value) closeContactModal();
    }
};

onMounted(() => {
    window.addEventListener('keydown', handleKeyDown);
});

onUnmounted(() => {
    window.removeEventListener('keydown', handleKeyDown);
    document.body.style.overflow = '';
});

// Helper for route
const safeRoute = (name, params) => {
    try {
        if (typeof route === 'function') {
            return params !== undefined ? route(name, params) : route(name);
        }
        return '#';
    } catch {
        return '#';
    }
};
</script>

<template>
    <Head title="Pusat Bantuan (Help Center) - TapakLokal">
        <meta
            name="description"
            content="Pusat Bantuan resmi TapakLokal: temukan jawaban dan panduan seputar Open Trip, Private Trip, Open PO Oleh-Oleh khas daerah, TapakWallet, kebijakan refund, dan anti-pungli."
        />
    </Head>

    <div class="min-h-screen bg-[#f8fafc] font-sans text-[#172c50] antialiased selection:bg-[#0088ff] selection:text-white flex flex-col justify-between">
        <div>
            <!-- Main Navigation on Top (Transparent integration) -->
            <MainNavigation :transparent-on-top="true" />

            <!-- HERO HEADER: 1:1 with Traveloka Vibrant Sky-Blue Help Center Header -->
            <section
                class="relative w-full overflow-hidden bg-gradient-to-b from-[#0084f3] via-[#018af7] to-[#0170d4] text-white pt-28 sm:pt-36 pb-12 sm:pb-16 px-4 shadow-inner"
                aria-labelledby="help-hero-title"
            >
                <!-- Subtle Background Geo/Cloud Accents -->
                <div class="absolute -right-20 -top-20 size-80 rounded-full bg-white/10 blur-2xl pointer-events-none"></div>
                <div class="absolute -left-20 bottom-0 size-72 rounded-full bg-sky-300/15 blur-2xl pointer-events-none"></div>

                <div class="relative z-10 mx-auto max-w-[840px] text-center">
                    <!-- Title matching Traveloka Style -->
                    <h1
                        id="help-hero-title"
                        class="text-2xl sm:text-3xl md:text-[38px] font-black tracking-tight text-white drop-shadow-xs leading-tight"
                    >
                        TapakLokal Help Center
                    </h1>

                    <!-- Subtitle -->
                    <p class="mt-2 text-sm sm:text-base md:text-lg font-medium text-white/90">
                        Find your answers here
                    </p>

                    <!-- Large White Search Bar (1:1 Traveloka Reference Design) -->
                    <div class="mt-6 sm:mt-8 relative max-w-2xl mx-auto">
                        <div class="relative flex items-center">
                            <Search class="pointer-events-none absolute left-4 sm:left-5 size-5 sm:size-5.5 text-slate-400" />
                            <input
                                v-model="searchQuery"
                                type="text"
                                placeholder="Type your topic here (e.g. refund, kuota open trip, oleh-oleh)..."
                                class="h-12 sm:h-14 w-full rounded-full border-0 bg-white pl-12 sm:pl-14 pr-12 text-xs sm:text-sm md:text-[15px] font-medium text-slate-800 placeholder:text-slate-400 focus:outline-none focus:ring-4 focus:ring-sky-300/50 shadow-[0_8px_24px_rgba(0,40,90,0.18)] transition-all"
                            />
                            <button
                                v-if="searchQuery"
                                type="button"
                                class="absolute right-4 grid size-7 place-items-center rounded-full bg-slate-100 text-slate-500 hover:bg-slate-200 transition-colors cursor-pointer"
                                aria-label="Bersihkan pencarian"
                                @click="searchQuery = ''"
                            >
                                <X class="size-4" />
                            </button>
                        </div>

                        <!-- Active Search Status Pill -->
                        <div v-if="searchQuery" class="mt-3 flex items-center justify-center gap-2 text-xs text-white/90">
                            <span>Menampilkan {{ filteredArticles.length }} hasil untuk "<strong>{{ searchQuery }}</strong>"</span>
                            <button
                                type="button"
                                class="underline font-bold hover:text-white cursor-pointer ml-1"
                                @click="searchQuery = ''"
                            >
                                Reset
                            </button>
                        </div>
                    </div>
                </div>
            </section>

            <!-- MAIN CONTENT AREA: 2-Column Traveloka Reference Layout -->
            <main class="mx-auto max-w-[1180px] px-4 sm:px-6 lg:px-0 py-10 sm:py-14">
                <div class="grid grid-cols-1 lg:grid-cols-[1.15fr_1fr] gap-10 lg:gap-14 items-start">
                    
                    <!-- LEFT COLUMN: Popular Topics (List with Chevrons & Clean Dividers) -->
                    <section aria-labelledby="popular-topics-heading" class="flex flex-col">
                        <div class="flex items-center justify-between pb-2 border-b border-slate-100 mb-2">
                            <h2
                                id="popular-topics-heading"
                                class="text-xl sm:text-2xl font-black tracking-tight text-slate-900"
                            >
                                {{ searchQuery ? 'Hasil Pencarian Topik' : activeProduct !== 'all' ? `Topik ${productCategories.find(p => p.id === activeProduct)?.name}` : 'Popular Topics' }}
                            </h2>

                            <!-- Reset filter button if active -->
                            <button
                                v-if="activeProduct !== 'all' && !searchQuery"
                                type="button"
                                class="text-xs font-bold text-[#0088ff] hover:underline cursor-pointer inline-flex items-center gap-1"
                                @click="activeProduct = 'all'"
                            >
                                <span>Lihat Semua</span>
                                <ChevronRight class="size-3" />
                            </button>
                        </div>

                        <!-- Topics List -->
                        <div v-if="popularArticles.length > 0" class="divide-y divide-slate-200/80">
                            <button
                                v-for="item in popularArticles"
                                :key="item.id"
                                type="button"
                                class="w-full py-4.5 text-left flex items-center justify-between gap-4 group transition-colors hover:bg-slate-50/80 -mx-2 px-2 rounded-xl cursor-pointer"
                                @click="openArticle(item)"
                            >
                                <div class="flex-1 pr-2">
                                    <div class="flex items-center gap-2 mb-1">
                                        <span
                                            class="inline-block rounded-md px-2 py-0.5 text-[10px] font-extrabold tracking-wide uppercase"
                                            :class="{
                                                'bg-sky-50 text-[#0088ff]': item.category === 'open-trip',
                                                'bg-emerald-50 text-emerald-700': item.category === 'private-trip',
                                                'bg-amber-50 text-amber-700': item.category === 'culinary-po',
                                                'bg-indigo-50 text-indigo-700': item.category === 'wallet',
                                                'bg-blue-50 text-blue-700': item.category === 'profile',
                                                'bg-rose-50 text-rose-700': item.category === 'anti-pungli',
                                                'bg-teal-50 text-teal-700': item.category === 'partners',
                                            }"
                                        >
                                            {{ item.categoryLabel }}
                                        </span>
                                    </div>
                                    <h3 class="text-sm sm:text-[15px] font-bold text-slate-800 group-hover:text-[#0088ff] transition-colors leading-snug">
                                        {{ item.title }}
                                    </h3>
                                    <p class="mt-1 text-xs text-slate-500 line-clamp-1 font-normal">
                                        {{ item.summary }}
                                    </p>
                                </div>

                                <!-- Chevron Right Icon matching Traveloka Layout -->
                                <ChevronRight class="size-5 text-[#0088ff] shrink-0 transition-transform group-hover:translate-x-1" />
                            </button>
                        </div>

                        <!-- Empty State when searching -->
                        <div v-else class="py-12 text-center rounded-2xl bg-slate-50 border border-slate-200/60 p-6 mt-4">
                            <div class="grid size-12 place-items-center rounded-full bg-sky-100 text-[#0088ff] mx-auto mb-3">
                                <HelpCircle class="size-6" />
                            </div>
                            <h4 class="text-base font-bold text-slate-800">Topik tidak ditemukan</h4>
                            <p class="mt-1 text-xs text-slate-500 max-w-sm mx-auto">
                                Coba gunakan kata kunci yang lebih umum atau hubungi layanan bantuan Customer Service kami langsung.
                            </p>
                            <button
                                type="button"
                                class="mt-4 inline-flex items-center gap-1.5 rounded-xl bg-[#0088ff] px-4 py-2 text-xs font-bold text-white hover:bg-[#0076de] transition-colors shadow-xs cursor-pointer"
                                @click="openContactModal"
                            >
                                <MessageCircle class="size-3.5" />
                                <span>Hubungi CS TapakLokal</span>
                            </button>
                        </div>
                    </section>

                    <!-- RIGHT COLUMN: Explore by Product (Circular Icon Grid 1:1 with Traveloka Reference) -->
                    <section aria-labelledby="explore-products-heading" class="flex flex-col bg-white rounded-3xl p-6 sm:p-7 border border-slate-200/80 shadow-xs">
                        <div class="mb-6 flex items-center justify-between">
                            <h2
                                id="explore-products-heading"
                                class="text-xl sm:text-2xl font-black tracking-tight text-slate-900"
                            >
                                Explore by Product
                            </h2>
                            <span class="text-[11px] font-bold text-[#0088ff] bg-sky-50 px-2.5 py-1 rounded-full">
                                3 Layanan Utama
                            </span>
                        </div>

                        <!-- Circular Icons Grid (4 columns on mobile/tablet, 3-4 on desktop) -->
                        <div class="grid grid-cols-3 sm:grid-cols-4 gap-y-6 gap-x-3 text-center">
                            <button
                                v-for="cat in productCategories"
                                :key="cat.id"
                                type="button"
                                class="group flex flex-col items-center justify-start focus-visible:outline-none cursor-pointer"
                                @click="activeProduct = activeProduct === cat.id ? 'all' : cat.id"
                            >
                                <!-- Circular Button Icon -->
                                <div
                                    class="size-13 sm:size-14 rounded-full flex items-center justify-center border transition-all duration-200 group-hover:scale-105 shadow-xs"
                                    :class="activeProduct === cat.id ? cat.activeClass : cat.colorClass"
                                >
                                    <component :is="cat.icon" class="size-6 sm:size-6.5 stroke-[2.2]" />
                                </div>

                                <!-- Label Below Icon -->
                                <span
                                    class="mt-2 text-[11px] sm:text-xs font-bold leading-tight transition-colors line-clamp-2 max-w-[90px]"
                                    :class="activeProduct === cat.id ? 'text-[#0088ff]' : 'text-slate-700 group-hover:text-[#0088ff]'"
                                >
                                    {{ cat.shortName }}
                                </span>
                            </button>
                        </div>
                    </section>

                </div>

                <!-- BOTTOM SECTION: Contact Us (1:1 Traveloka Reference Style) -->
                <section aria-labelledby="contact-heading" class="mt-14 sm:mt-18 pt-10 border-t border-slate-200">
                    <div class="max-w-xl text-left">
                        <h3 id="contact-heading" class="text-lg sm:text-xl font-black text-slate-900">
                            Contact us
                        </h3>
                        <p class="mt-1 text-xs sm:text-sm text-slate-600 font-medium leading-relaxed">
                            Still have questions? Tap below to get in touch with our customer service.
                        </p>

                        <!-- Traveloka-Style Light Blue Pill Contact Button -->
                        <div class="mt-4">
                            <button
                                type="button"
                                class="inline-flex items-center gap-2 rounded-xl bg-sky-50 border border-sky-200 px-5 py-2.5 text-xs sm:text-sm font-extrabold text-[#0088ff] hover:bg-[#0088ff] hover:text-white shadow-xs transition-all duration-200 cursor-pointer hover:shadow-md"
                                @click="openContactModal"
                            >
                                <MessageCircle class="size-4" />
                                <span>Contact us</span>
                            </button>
                        </div>
                    </div>
                </section>
            </main>
        </div>

        <!-- ARTICLE DETAIL MODAL / DRAWER -->
        <Teleport to="body">
            <div
                v-if="selectedArticle"
                class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm animate-in fade-in duration-200"
                role="dialog"
                aria-modal="true"
                :aria-label="selectedArticle.title"
                @click.self="closeArticle"
            >
                <div class="relative w-full max-w-2xl max-h-[88vh] overflow-hidden rounded-3xl bg-white shadow-2xl flex flex-col border border-slate-100">
                    
                    <!-- Modal Header -->
                    <div class="p-6 sm:p-7 border-b border-slate-100 bg-gradient-to-r from-slate-50 via-white to-sky-50/40 flex items-start justify-between gap-4">
                        <div>
                            <div class="flex items-center gap-2 mb-1.5">
                                <span
                                    class="inline-block rounded-md px-2 py-0.5 text-[10px] font-extrabold tracking-wide uppercase"
                                    :class="{
                                        'bg-sky-50 text-[#0088ff]': selectedArticle.category === 'open-trip',
                                        'bg-emerald-50 text-emerald-700': selectedArticle.category === 'private-trip',
                                        'bg-amber-50 text-amber-700': selectedArticle.category === 'culinary-po',
                                        'bg-indigo-50 text-indigo-700': selectedArticle.category === 'wallet',
                                        'bg-blue-50 text-blue-700': selectedArticle.category === 'profile',
                                        'bg-rose-50 text-rose-700': selectedArticle.category === 'anti-pungli',
                                        'bg-teal-50 text-teal-700': selectedArticle.category === 'partners',
                                    }"
                                >
                                    {{ selectedArticle.categoryLabel }}
                                </span>
                                <span class="text-[11px] text-slate-400 font-medium">Panduan Resmi TapakLokal</span>
                            </div>
                            <h3 class="text-base sm:text-lg md:text-xl font-black text-[#172c50] leading-snug">
                                {{ selectedArticle.title }}
                            </h3>
                        </div>

                        <!-- Close Button -->
                        <button
                            type="button"
                            class="grid size-9 place-items-center rounded-full bg-slate-100 text-slate-500 hover:bg-slate-200 hover:text-slate-800 transition-colors shrink-0 cursor-pointer"
                            aria-label="Tutup artikel"
                            @click="closeArticle"
                        >
                            <X class="size-4.5" />
                        </button>
                    </div>

                    <!-- Modal Body (Scrollable formatted text) -->
                    <div class="flex-1 overflow-y-auto p-6 sm:p-7 space-y-5 text-xs sm:text-sm text-slate-700 leading-relaxed">
                        
                        <!-- Content Paragraphs & Lists -->
                        <div class="prose prose-sm prose-slate max-w-none space-y-4">
                            <div
                                v-for="(paragraph, idx) in selectedArticle.content.trim().split('\n\n')"
                                :key="idx"
                                class="text-slate-700"
                            >
                                <!-- Render Blockquote Tips -->
                                <div
                                    v-if="paragraph.startsWith('>')"
                                    class="rounded-2xl bg-sky-50/80 border-l-4 border-[#0088ff] p-4 text-xs font-medium text-slate-700"
                                >
                                    {{ paragraph.replace(/^>\s*/, '').replace(/\*\*(.*?)\*\*/g, '$1') }}
                                </div>

                                <!-- Render Lists -->
                                <div v-else-if="paragraph.startsWith('1.') || paragraph.startsWith('-')">
                                    <ul class="space-y-2 my-2 pl-4 list-disc text-slate-700">
                                        <li
                                            v-for="(item, itemIdx) in paragraph.split('\n')"
                                            :key="itemIdx"
                                            class="leading-relaxed"
                                        >
                                            <span v-html="item.replace(/^(\d+\.|\-)\s*/, '').replace(/\*\*(.*?)\*\*/g, '<strong>$1</strong>')"></span>
                                        </li>
                                    </ul>
                                </div>

                                <!-- Render Regular Paragraph -->
                                <p v-else class="leading-relaxed" v-html="paragraph.replace(/\*\*(.*?)\*\*/g, '<strong>$1</strong>')"></p>
                            </div>
                        </div>

                        <!-- Helpful Rating Widget -->
                        <div class="mt-8 pt-6 border-t border-slate-100 rounded-2xl bg-slate-50 p-4 sm:p-5 flex flex-col sm:flex-row items-center justify-between gap-4">
                            <div>
                                <h4 class="text-xs sm:text-[13px] font-bold text-slate-900">
                                    Apakah artikel ini membantu Anda?
                                </h4>
                                <p class="text-[11px] text-slate-500 font-medium">
                                    Masukan Anda sangat berharga untuk peningkatan panduan kami.
                                </p>
                            </div>

                            <div class="flex items-center gap-2">
                                <button
                                    type="button"
                                    class="inline-flex items-center gap-1.5 rounded-xl px-3.5 py-1.5 text-xs font-bold transition-all cursor-pointer"
                                    :class="feedbackGiven[selectedArticle.id] === 'yes' ? 'bg-[#0088ff] text-white shadow-xs' : 'bg-white text-slate-700 border border-slate-200 hover:bg-slate-100'"
                                    @click="giveFeedback(selectedArticle.id, 'yes')"
                                >
                                    <ThumbsUp class="size-3.5" />
                                    <span>Ya</span>
                                </button>
                                <button
                                    type="button"
                                    class="inline-flex items-center gap-1.5 rounded-xl px-3.5 py-1.5 text-xs font-bold transition-all cursor-pointer"
                                    :class="feedbackGiven[selectedArticle.id] === 'no' ? 'bg-rose-600 text-white shadow-xs' : 'bg-white text-slate-700 border border-slate-200 hover:bg-slate-100'"
                                    @click="giveFeedback(selectedArticle.id, 'no')"
                                >
                                    <ThumbsDown class="size-3.5" />
                                    <span>Tidak</span>
                                </button>
                                <button
                                    type="button"
                                    class="p-2 rounded-xl bg-white border border-slate-200 text-slate-600 hover:text-[#0088ff] transition-colors cursor-pointer"
                                    title="Salin Tautan"
                                    @click="copyArticleLink"
                                >
                                    <Check v-if="isCopied" class="size-3.5 text-emerald-600" />
                                    <Share2 v-else class="size-3.5" />
                                </button>
                            </div>
                        </div>

                    </div>

                    <!-- Modal Footer -->
                    <div class="p-4 sm:p-5 border-t border-slate-100 bg-slate-50/80 flex items-center justify-between">
                        <span class="text-xs text-slate-500 font-medium flex items-center gap-1.5">
                            <ShieldCheck class="size-4 text-emerald-500 shrink-0" />
                            Standar Layanan Resmi TapakLokal
                        </span>

                        <button
                            type="button"
                            class="inline-flex items-center gap-1.5 rounded-xl bg-[#0088ff] px-4 py-2 text-xs font-bold text-white hover:bg-[#0076de] transition-colors shadow-xs cursor-pointer"
                            @click="closeArticle"
                        >
                            <span>Mengerti</span>
                        </button>
                    </div>
                </div>
            </div>
        </Teleport>

        <!-- CONTACT SUPPORT MODAL -->
        <Teleport to="body">
            <div
                v-if="isContactModalOpen"
                class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm animate-in fade-in duration-200"
                role="dialog"
                aria-modal="true"
                aria-label="Hubungi Customer Service TapakLokal"
                @click.self="closeContactModal"
            >
                <div class="relative w-full max-w-lg rounded-3xl bg-white shadow-2xl overflow-hidden border border-slate-100">
                    
                    <!-- Modal Header -->
                    <div class="p-6 border-b border-slate-100 bg-gradient-to-r from-sky-50 via-white to-indigo-50/40 flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <div class="grid size-11 place-items-center rounded-2xl bg-[#0088ff] text-white shadow-sm">
                                <Phone class="size-5.5" />
                            </div>
                            <div>
                                <h3 class="text-lg font-black text-[#172c50]">
                                    Hubungi Customer Service
                                </h3>
                                <p class="text-xs text-slate-500 font-medium">
                                    Tim CS TapakLokal siap membantumu
                                </p>
                            </div>
                        </div>

                        <button
                            type="button"
                            class="grid size-8 place-items-center rounded-full bg-slate-100 text-slate-500 hover:bg-slate-200 transition-colors cursor-pointer"
                            aria-label="Tutup"
                            @click="closeContactModal"
                        >
                            <X class="size-4" />
                        </button>
                    </div>

                    <!-- Modal Body: Contact Channels -->
                    <div class="p-6 space-y-3">
                        
                        <!-- 1. WhatsApp CS -->
                        <a
                            href="https://wa.me/6281234567890?text=Halo%20Admin%20TapakLokal,%20saya%20butuh%20bantuan%20seputar%20trip/produk"
                            target="_blank"
                            rel="noopener noreferrer"
                            class="group flex items-center justify-between p-4 rounded-2xl border border-emerald-200/80 bg-emerald-50/40 hover:bg-emerald-50 transition-all hover:border-emerald-300 shadow-xs"
                        >
                            <div class="flex items-center gap-3.5">
                                <div class="grid size-10 place-items-center rounded-xl bg-emerald-600 text-white shadow-xs">
                                    <MessageCircle class="size-5" />
                                </div>
                                <div class="text-left">
                                    <h4 class="text-xs sm:text-sm font-bold text-slate-900 group-hover:text-emerald-700 transition-colors">
                                        Chat WhatsApp Resmi CS
                                    </h4>
                                    <p class="text-[11px] text-slate-500 font-medium">
                                        Respon cepat setiap hari · 08.00 – 22.00 WIB
                                    </p>
                                </div>
                            </div>
                            <ArrowRight class="size-4 text-emerald-600 group-hover:translate-x-1 transition-transform" />
                        </a>

                        <!-- 2. Pesan Bantuan / Support Ticket -->
                        <Link
                            :href="safeRoute('account.section', 'support')"
                            class="group flex items-center justify-between p-4 rounded-2xl border border-sky-200/80 bg-sky-50/40 hover:bg-sky-50 transition-all hover:border-sky-300 shadow-xs"
                            @click="closeContactModal"
                        >
                            <div class="flex items-center gap-3.5">
                                <div class="grid size-10 place-items-center rounded-xl bg-[#0088ff] text-white shadow-xs">
                                    <Mail class="size-5" />
                                </div>
                                <div class="text-left">
                                    <h4 class="text-xs sm:text-sm font-bold text-slate-900 group-hover:text-[#0088ff] transition-colors">
                                        Kirim Tiket / Pesan Bantuan
                                    </h4>
                                    <p class="text-[11px] text-slate-500 font-medium">
                                        Pengaduan terstruktur dengan lampiran bukti
                                    </p>
                                </div>
                            </div>
                            <ArrowRight class="size-4 text-[#0088ff] group-hover:translate-x-1 transition-transform" />
                        </Link>

                        <!-- 3. Email Resmi -->
                        <a
                            href="mailto:support@tapaklokal.com"
                            class="group flex items-center justify-between p-4 rounded-2xl border border-slate-200 bg-white hover:bg-slate-50 transition-all hover:border-slate-300"
                        >
                            <div class="flex items-center gap-3.5">
                                <div class="grid size-10 place-items-center rounded-xl bg-slate-800 text-white shadow-xs">
                                    <Mail class="size-5" />
                                </div>
                                <div class="text-left">
                                    <h4 class="text-xs sm:text-sm font-bold text-slate-900 group-hover:text-[#0088ff] transition-colors">
                                        Email Dukungan Pengguna
                                    </h4>
                                    <p class="text-[11px] text-slate-500 font-medium">
                                        support@tapaklokal.com
                                    </p>
                                </div>
                            </div>
                            <ArrowRight class="size-4 text-slate-400 group-hover:translate-x-1 transition-transform" />
                        </a>

                        <!-- 4. Hotline Darurat Lapangan 24 Jam -->
                        <div class="p-3.5 rounded-2xl bg-amber-50 border border-amber-200/80 flex items-start gap-2.5">
                            <AlertCircle class="size-4.5 text-amber-600 shrink-0 mt-0.5" />
                            <div class="text-[11px] text-slate-700 leading-relaxed">
                                <strong class="font-bold text-amber-900">Kendala Darurat Saat Trip Berlangsung?</strong>
                                <p class="text-slate-600 mt-0.5">Hubungi Hotline Siaga Lapangan 24 Jam: <strong>0811-9988-7711</strong> (Khusus kondisi medis/keselamatan di lokasi trip).</p>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </Teleport>

        <!-- Main Footer matching Global Design -->
        <MainFooter />
    </div>
</template>
