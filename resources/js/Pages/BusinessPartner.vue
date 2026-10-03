<script setup>
import { computed, onMounted, onUnmounted, ref } from 'vue';
import { Head, useForm } from '@inertiajs/vue3';
import { route } from 'ziggy-js';
import {
    ArrowRight, ArrowUpRight, CalendarDays, Check, CheckCircle2, ChevronDown,
    Clock, Compass, FileText, LayoutDashboard, MapPin,
    ShieldCheck, Sparkles, Star, Store, TentTree, Users, Wallet, X, Eye, EyeOff
} from 'lucide-vue-next';
import BusinessLanding from '../Components/Shared/BusinessLanding.vue';

const registrationDialog = ref(null);
const selectedTrack = ref('trip');
const isTrackDropdownOpen = ref(false);
const trackDropdownRef = ref(null);

const partnershipOptions = [
    {
        value: 'trip',
        label: 'Vendor trip wisata',
        description: 'Open trip, private tour & pemandu lokal',
        icon: TentTree,
    },
    {
        value: 'souvenir',
        label: 'Oleh-oleh & kuliner',
        description: 'Makanan khas, cinderamata & kerajinan lokal',
        icon: Store,
    },
];

const currentTrackOption = computed(() => {
    return partnershipOptions.find(opt => opt.value === selectedTrack.value) || partnershipOptions[0];
});

function selectTrack(value) {
    selectedTrack.value = value;
    isTrackDropdownOpen.value = false;
}

function handleDropdownClickOutside(event) {
    if (trackDropdownRef.value && !trackDropdownRef.value.contains(event.target)) {
        isTrackDropdownOpen.value = false;
    }
}

onMounted(() => {
    document.addEventListener('click', handleDropdownClickOutside);
});

onUnmounted(() => {
    document.removeEventListener('click', handleDropdownClickOutside);
});

const brief = useForm({ business: '', name: '', city: '', email: '', phone: '', password: '', password_confirmation: '', notes: '' });
const submitted = ref(false);
const passwordVisible = ref(false);
const confirmationVisible = ref(false);
const links = [{ href: '#kemitraan', label: 'Pilihan Kemitraan' }, { href: '#manfaat', label: 'Solusi Bisnis' }, { href: '#cara-bergabung', label: 'Cara Bergabung' }, { href: '#faq', label: 'Tanya Jawab' }];
const tracks = [
    {
        id: 'trip',
        icon: TentTree,
        badge: 'JALUR VENDOR WISATA',
        label: 'Operator Trip & Wisata',
        tagline: 'Open Trip & Private Tour',
        title: 'Perjalanan Istimewa Anda, Ditemukan Ribuan Penjelajah.',
        description: 'Bawa keahlian lokal Anda ke panggung nasional. Tawarkan open trip dan private tour dengan sistem booking otomatis, kalender kuota terpadu, dan proteksi pembayaran resmi.',
        cta: 'Daftar sebagai Vendor Trip',
        preview: {
            image: '/Assets/Images/partner-trip-showcase.jpg',
            tag: 'Open & Private Trip',
            status: 'Pemandu Terverifikasi',
            title: 'Eksplorasi Curug & Lembah Hijau Nusantara',
            location: 'Malang & Lumajang, Jawa Timur',
            duration: '3 Hari 2 Malam',
            capacity: 'Maks. 14 Peserta / trip',
            rating: '4.95',
            reviews: '128 ulasan',
            price: 'Rp 1.450.000',
            priceUnit: '/ orang',
            features: [
                { label: 'Sistem Kalender', value: 'Live Booking Real-Time' },
                { label: 'Rekening Bersama', value: 'Garansi Dana 100% Aman' },
                { label: 'Pencairan Dana', value: 'H+1 Selesai Perjalanan' },
            ],
        },
        highlights: [
            {
                title: 'Itinerary & Titik Kumpul Terstruktur',
                description: 'Susun rute perjalanan, meeting point, fasilitas paket, dan ketentuan trip dalam format yang rapi dan mudah dipahami wisatawan.',
            },
            {
                title: 'Kalender Keberangkatan & Kuota Otomatis',
                description: 'Atur tanggal keberangkatan dan batas kuota peserta tanpa khawatir terjadi double booking atau jadwal bertabrakan.',
            },
            {
                title: 'Jaminan Pembayaran & Perlindungan Vendor',
                description: 'Wisatawan membayar di muka melalui TapakLokal. Dana tersimpan aman di rekening bersama dan langsung dicairkan tanpa potongan tersembunyi.',
            },
        ],
    },
    {
        id: 'souvenir',
        icon: Store,
        badge: 'JALUR UMKM & KULINER',
        label: 'Oleh-oleh & Kuliner Lokal',
        tagline: 'UMKM, Kriya & Makanan Khas',
        title: 'Dari Cerita Usaha Lokal, Jadi Bagian dari Setiap Perjalanan.',
        description: 'Kenalkan cita rasa autentik dan karya kriya khas daerah Anda kepada ribuan penjelajah. Pasarkan produk unggulan dengan fitur pre-order dan jangkauan pengiriman antar-kota.',
        cta: 'Ajukan Kemitraan Produk Lokal',
        preview: {
            image: '/Assets/Images/partner-souvenir-showcase.jpg',
            tag: 'Kriya & Kuliner UMKM',
            status: 'Mitra UMKM Terkurasi',
            title: 'Kopi Robusta Asli & Jajanan Khas Nusantara',
            location: 'Sentra Oleh-Oleh Daerah',
            duration: 'Produksi Harian Fresh',
            capacity: 'Ready Stock & Pre-Order',
            rating: '4.98',
            reviews: '246 ulasan',
            price: 'Rp 45.000 - Rp 180.000',
            priceUnit: '/ paket',
            features: [
                { label: 'Integrasi Kurir', value: 'Instan & Logistik Nasional' },
                { label: 'Opsi Pembelian', value: 'Pre-Order (PO) & Ready' },
                { label: 'Laporan Penjualan', value: 'Dashboard Omzet Real-Time' },
            ],
        },
        highlights: [
            {
                title: 'Etalase Digital & Cerita Produk Autentik',
                description: 'Tampilkan foto produk terbaik, detail komposisi, varian rasa, serta kisah unik di balik proses pembuatan usaha lokal Anda.',
            },
            {
                title: 'Sistem Pre-Order (PO) Wisatawan',
                description: 'Wisatawan dapat memesan oleh-oleh sebelum jadwal trip tiba untuk diambil langsung di destinasi atau dikirim ke hotel/rumah.',
            },
            {
                title: 'Pengelolaan Stok & Pesanan Praktis',
                description: 'Terima notifikasi order instan, atur kuota produksi harian, dan nikmati pencairan saldo berkala langsung ke rekening usaha Anda.',
            },
        ],
    },
];

const currentTrack = computed(() => tracks.find((t) => t.id === selectedTrack.value) || tracks[0]);
const steps = [
    { title: 'Kenalkan usaha Anda', description: 'Pilih jenis kemitraan dan ceritakan usaha Anda melalui formulir minat.' },
    { title: 'Diskusikan kebutuhan', description: 'Tim kami meninjau profil usaha dan membahas kesesuaian kerja sama.' },
    { title: 'Siapkan penawaran', description: 'Lengkapi informasi paket atau produk, harga, dan ketentuan layanan.' },
    { title: 'Mulai bertumbuh', description: 'Setelah disetujui, kelola penawaran dan layani pelanggan Anda.' },
];
const faqs = [
    { q: 'Siapa yang dapat mengajukan kemitraan?', a: 'Operator open trip dan private trip, penyedia pengalaman wisata, serta pelaku usaha oleh-oleh, kuliner, dan kerajinan lokal dapat mengajukan minat. Tim kemitraan akan mendiskusikan kesesuaian layanan dan kesiapan usaha Anda.' },
    { q: 'Apa yang perlu disiapkan untuk bergabung?', a: 'Siapkan nama usaha, nama penanggung jawab, wilayah operasional, kontak yang dapat dihubungi, dan gambaran paket atau produk Anda. Kelengkapan legalitas dan dokumen pendukung akan dibahas saat peninjauan.' },
    { q: 'Apakah mengirim formulir langsung mengaktifkan akun vendor?', a: 'Belum. Pengajuan tersimpan dan ditinjau oleh admin pengelola vendor. Tim menghubungi kontak yang Anda cantumkan. Aktivasi akun vendor dilakukan terpisah setelah verifikasi.' },
    { q: 'Bagaimana biaya kerja sama dan pencairan dana?', a: 'Biaya layanan, pembagian hasil, serta jadwal pencairan dibahas bersama tim dan mengikuti ketentuan kerja sama yang disepakati. Pastikan Anda memahami ketentuannya sebelum mengaktifkan penawaran.' },
    { q: 'Saya sudah menjadi vendor. Bagaimana cara masuk?', a: 'Gunakan tombol Masuk pada navigasi untuk membuka portal vendor dengan akun yang telah diberikan kepada Anda.' },
];
function openRegistration(type = selectedTrack.value) {
    selectedTrack.value = type;
    isTrackDropdownOpen.value = false;
    registrationDialog.value?.showModal();
}
function closeRegistration() {
    passwordVisible.value = false;
    confirmationVisible.value = false;
    registrationDialog.value?.close();
}
function handleDialogBackdrop(event) {
    if (event.target === registrationDialog.value) {
        const rect = registrationDialog.value.getBoundingClientRect();
        const clickedInside =
            rect.top <= event.clientY &&
            event.clientY <= rect.top + rect.height &&
            rect.left <= event.clientX &&
            event.clientX <= rect.left + rect.width;
        if (!clickedInside) {
            closeRegistration();
        }
    }
}
function submitApplication() {
    submitted.value = false;
    brief.transform(data => ({ ...data, track: selectedTrack.value })).post(route('vendor-applications.store'), {
        preserveScroll: true,
        onSuccess: () => { submitted.value = true; brief.reset(); },
    });
}
</script>

<template>
    <Head title="Mitra Vendor — Tumbuh Bersama TapakLokal"><meta name="description" content="Kembangkan usaha wisata dan produk lokal bersama TapakLokal. Pelajari pilihan kemitraan, solusi pengelolaan usaha, dan cara bergabung." /></Head>
    <BusinessLanding program="PARTNERS" :links="links" :faqs="faqs" :login-href="route('vendor.login')" cta="Jadi mitra" @join="openRegistration()">
        <section class="business-hero flex min-h-[calc(100dvh-68px)] flex-col justify-between">
            <div class="business-container my-auto grid w-full flex-1 items-center gap-8 py-6 sm:py-10 lg:grid-cols-[1.05fr_0.95fr] lg:gap-14 lg:py-8 xl:gap-16">
                <div>
                    <p class="business-eyebrow">TAPAKLOKAL PARTNER PROGRAM</p>
                    <h1 class="mt-3 sm:mt-4 text-3xl leading-[1.18] font-bold tracking-[-0.04em] text-[#07345a] sm:text-5xl lg:text-[48px] xl:text-[52px]">
                        Usaha lokal Anda.<br />Peluang yang<br /><span class="business-handwritten">lebih luas.</span>
                    </h1>
                    <p class="business-copy mt-4 sm:mt-5 max-w-md text-xs sm:text-sm lg:text-base leading-relaxed text-slate-500">
                        Hubungkan pengalaman wisata dan produk terbaik Anda dengan lebih banyak penjelajah. Kita tumbuh bersama, dari potensi lokal.
                    </p>
                    <div class="mt-6 sm:mt-7 flex flex-col sm:flex-row gap-3">
                        <button class="business-button w-full sm:w-auto inline-flex" @click="openRegistration()">Mulai jadi mitra <ArrowRight class="size-4" /></button>
                        <a href="#kemitraan" class="business-button business-button-secondary w-full sm:w-auto inline-flex">Jelajahi kemitraan</a>
                    </div>
                </div>
                <div class="relative">
                    <div class="relative h-[240px] xs:h-[280px] sm:h-[400px] lg:h-[430px] xl:h-[460px] overflow-hidden rounded-t-[70px] sm:rounded-t-[110px] rounded-b-2xl shadow-lg shadow-sky-950/5">
                        <img src="/Assets/Images/partner-hero-person.jpg" alt="Pelaku usaha wisata mengelola layanan perjalanan menggunakan tablet" width="1200" height="896" fetchpriority="high" class="business-photo object-[48%_center]" />
                        <div class="absolute inset-x-0 bottom-0 bg-gradient-to-t from-[#07345a]/85 to-transparent px-5 sm:px-6 pt-12 sm:pt-16 pb-6 sm:pb-10 text-white">
                            <p class="text-[9px] sm:text-[10px] font-semibold tracking-[.15em]">BERAKAR LOKAL. BERKEMBANG BERSAMA.</p>
                            <p class="mt-1.5 sm:mt-2 text-sm sm:text-base lg:text-lg font-semibold leading-snug">Anda fokus pada pengalaman.<br />Kami bantu membuka peluang.</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="w-full shrink-0 border-t border-[#a9d5eb]/50 bg-white/40 backdrop-blur-xs">
                <div class="business-container grid gap-2.5 py-3.5 sm:grid-cols-3 sm:gap-6 sm:py-5">
                    <div v-for="(item, index) in ['Jangkauan pasar lebih luas', 'Informasi usaha lebih terstruktur', 'Pendampingan awal kemitraan']" :key="item" class="flex items-center gap-2.5 sm:gap-3 text-xs font-semibold text-[#315a70] sm:text-sm">
                        <span class="text-xs font-normal text-[#678c9e]">0{{ index + 1 }}</span>
                        <span>{{ item }}</span>
                    </div>
                </div>
            </div>
        </section>

        <section id="kemitraan" class="business-container business-section scroll-mt-24">
            <!-- Section Header -->
            <div class="mx-auto max-w-3xl text-center">
                <h2 class="text-2xl sm:text-4xl lg:text-[42px] font-bold tracking-tight text-[#07345a] leading-tight">
                    Dua Cara Bergabung.<br class="hidden sm:inline" />
                    <span class="text-[#009cf0]"> Satu Semangat Tumbuh Bersama.</span>
                </h2>

                <!-- Modern Interactive Segmented Tabs -->
                <div class="mt-6 sm:mt-8 flex justify-center w-full">
                    <div class="grid grid-cols-2 w-full max-w-md rounded-2xl border border-slate-200/80 bg-slate-100/90 p-1 sm:flex sm:w-auto sm:max-w-none sm:p-1.5 shadow-xs" role="tablist" aria-label="Pilihan jenis kemitraan">
                        <button
                            v-for="track in tracks"
                            :key="track.id"
                            role="tab"
                            :aria-selected="selectedTrack === track.id"
                            class="flex flex-col sm:flex-row items-center text-center sm:text-left gap-1.5 sm:gap-3 rounded-xl px-2.5 py-2.5 sm:px-5 sm:py-2.5 transition-all duration-200 cursor-pointer justify-center"
                            :class="selectedTrack === track.id
                                ? 'bg-white text-[#07345a] shadow-md shadow-slate-900/5 ring-1 ring-black/5 font-bold'
                                : 'text-slate-500 hover:text-[#07345a] hover:bg-white/50 font-medium'"
                            @click="selectedTrack = track.id"
                        >
                            <span
                                class="flex size-7 sm:size-8 items-center justify-center rounded-lg transition-colors shrink-0"
                                :class="selectedTrack === track.id ? 'bg-[#009cf0] text-white shadow-xs' : 'bg-slate-200/80 text-slate-500'"
                            >
                                <component :is="track.icon" class="size-3.5 sm:size-4" />
                            </span>
                            <span class="text-xs sm:text-sm font-bold leading-tight sm:whitespace-nowrap">{{ track.label }}</span>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Dynamic Showcase Card -->
            <div class="mt-8 sm:mt-10 overflow-hidden rounded-2xl sm:rounded-3xl border border-sky-100 bg-gradient-to-b from-white via-[#f7fbff] to-[#edf6fd] p-4 sm:p-8 lg:p-10 shadow-xl shadow-sky-950/5 transition-all duration-300">
                <div class="grid items-center gap-6 sm:gap-8 lg:grid-cols-[1.05fr_1fr] lg:gap-12 xl:gap-16">
                    <!-- Left: Realistic Live Mockup Card -->
                    <div class="relative">
                        <div class="overflow-hidden rounded-2xl border border-slate-200/80 bg-white shadow-lg shadow-sky-900/5 transition duration-300 hover:shadow-xl">
                            <!-- Showcase Hero Photo -->
                            <div class="relative h-44 sm:h-56 overflow-hidden">
                                <img
                                    :src="currentTrack.preview.image"
                                    :alt="currentTrack.preview.title"
                                    class="size-full object-cover transition-transform duration-500 hover:scale-105"
                                    loading="lazy"
                                />
                            </div>

                            <!-- Card Body -->
                            <div class="p-4 sm:p-6">
                                <h4 class="text-base sm:text-lg font-bold text-[#07345a] leading-snug">
                                    {{ currentTrack.preview.title }}
                                </h4>

                                <!-- Meta Info -->
                                <div class="mt-2.5 sm:mt-3 flex flex-wrap items-center gap-y-1.5 gap-x-3 sm:gap-x-4 text-xs text-slate-500">
                                    <span class="flex items-center gap-1.5 font-medium text-slate-600">
                                        <MapPin class="size-3.5 text-[#009cf0]" /> {{ currentTrack.preview.location }}
                                    </span>
                                    <span class="flex items-center gap-1.5 font-medium text-slate-600">
                                        <Clock class="size-3.5 text-[#009cf0]" /> {{ currentTrack.preview.duration }}
                                    </span>
                                    <span class="flex items-center gap-1.5 font-medium text-slate-600">
                                        <Users class="size-3.5 text-[#009cf0]" /> {{ currentTrack.preview.capacity }}
                                    </span>
                                </div>

                                <!-- Mini Features Spec Grid -->
                                <div class="mt-3.5 sm:mt-4 grid grid-cols-3 gap-1.5 sm:gap-2 border-t border-slate-100 pt-3 sm:pt-4">
                                    <div
                                        v-for="spec in currentTrack.preview.features"
                                        :key="spec.label"
                                        class="rounded-lg sm:rounded-xl bg-sky-50/70 p-2 sm:p-2.5 text-center ring-1 ring-sky-100/80"
                                    >
                                        <p class="text-[9px] sm:text-[10px] font-medium text-[#507693] truncate sm:whitespace-normal">{{ spec.label }}</p>
                                        <p class="mt-0.5 sm:mt-1 text-[10px] sm:text-[11px] font-bold text-[#07345a] leading-tight">{{ spec.value }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Right: Track Highlights & Benefits -->
                    <div>
                        <h3 class="text-xl sm:text-2xl lg:text-3xl font-bold tracking-tight text-[#07345a] leading-snug">
                            {{ currentTrack.title }}
                        </h3>

                        <!-- Highlights List -->
                        <div class="mt-5 sm:mt-6 space-y-3 sm:space-y-3.5">
                            <div
                                v-for="(hl, index) in currentTrack.highlights"
                                :key="hl.title"
                                class="flex items-start gap-3 sm:gap-3.5 rounded-xl sm:rounded-2xl border border-white/80 bg-white/90 p-3 sm:p-4 shadow-xs transition hover:border-sky-200 hover:bg-white hover:shadow-sm"
                            >
                                <span class="flex size-6 sm:size-7 shrink-0 items-center justify-center rounded-lg sm:rounded-xl bg-sky-50 text-[11px] sm:text-xs font-bold text-[#009cf0]">
                                    0{{ index + 1 }}
                                </span>
                                <div>
                                    <h4 class="text-xs sm:text-sm font-bold text-[#07345a]">{{ hl.title }}</h4>
                                    <p class="mt-0.5 text-[11px] sm:text-xs leading-relaxed text-slate-500">{{ hl.description }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section id="manfaat" class="bg-[#f7fafc]">
            <div class="business-container business-section">
                <div class="mx-auto max-w-2xl text-center">
                    <p class="business-eyebrow">LEBIH RAPI MENGELOLA, LEBIH SIAP BERKEMBANG</p>
                    <h2 class="business-heading mt-3 sm:mt-4">Partner untuk perjalanan<br />bisnis Anda berikutnya.</h2>
                    <p class="business-copy mt-3 sm:mt-5">Dari penawaran yang mudah dipahami hingga persiapan operasional, bangun pengalaman yang meyakinkan sejak awal.</p>
                </div>
                <div class="mt-8 sm:mt-12 grid gap-5 lg:grid-cols-3">
                    <article class="rounded-2xl border border-slate-200/70 bg-white p-5 sm:p-7 lg:col-span-2">
                        <div class="flex items-center gap-3">
                            <LayoutDashboard class="size-5 sm:size-6 text-[#009cf0]" />
                            <h3 class="text-lg sm:text-xl font-bold text-[#07345a]">Semua detail, satu pandangan.</h3>
                        </div>
                        <p class="business-copy mt-2 sm:mt-3 max-w-xl">Persiapkan paket, jadwal, dan informasi peserta dengan struktur yang jelas untuk tim Anda.</p>
                        <div class="mt-6 sm:mt-8 overflow-hidden rounded-xl border border-slate-200 bg-[#fbfdff] p-4 sm:p-5">
                            <div class="flex justify-between gap-3 text-xs">
                                <span class="font-bold text-[#07345a]">Ringkasan keberangkatan</span>
                                <span class="text-slate-400">Contoh tampilan</span>
                            </div>
                            <div class="mt-4 sm:mt-5 grid grid-cols-3 gap-2 sm:gap-3">
                                <div v-for="(value, index) in ['12 Okt', '12 / 16', '3 hari']" :key="value" class="rounded-lg bg-white p-2.5 sm:p-3 text-center sm:text-left ring-1 ring-slate-100">
                                    <p class="text-[9px] sm:text-[10px] text-slate-500">{{ ['Jadwal', 'Peserta', 'Durasi'][index] }}</p>
                                    <p class="mt-1 sm:mt-2 text-base sm:text-2xl font-bold text-[#07345a]">{{ value }}</p>
                                </div>
                            </div>
                            <div class="mt-4 sm:mt-5 flex items-center gap-2.5 sm:gap-3 rounded-lg bg-sky-50 px-3.5 py-2.5 sm:px-4 sm:py-3 text-xs text-[#075890]">
                                <CalendarDays class="size-4 shrink-0" />
                                <span>Detail perjalanan siap ditinjau bersama tim</span>
                            </div>
                        </div>
                    </article>
                    <article class="flex flex-col justify-between rounded-2xl bg-[#07345a] p-5 sm:p-7 text-white">
                        <div>
                            <Users class="size-6 sm:size-7 text-sky-300" />
                            <h3 class="mt-4 sm:mt-5 text-lg sm:text-xl font-bold">Kekuatan lokal,<br />peluang lebih besar.</h3>
                            <p class="mt-3 sm:mt-4 text-xs sm:text-sm leading-6 sm:leading-7 text-sky-100/80">Cerita, pengetahuan, dan keramahan Anda adalah bagian berharga dari setiap perjalanan. Perkenalkan ke lebih banyak calon pelanggan.</p>
                        </div>
                        <div class="mt-6 sm:mt-auto flex items-center gap-3 pt-6 sm:pt-9 border-t border-white/10 sm:border-t-0">
                            <span class="flex size-9 sm:size-11 items-center justify-center rounded-full border border-white/20 shrink-0">
                                <MapPin class="size-4 sm:size-5" />
                            </span>
                            <p class="text-xs leading-5 text-sky-100">Dari daerah Anda,<br />untuk penjelajah Indonesia.</p>
                        </div>
                    </article>
                </div>
                <div class="mt-8 sm:mt-9 grid gap-6 sm:gap-8 sm:grid-cols-3">
                    <div v-for="item in [{ icon: FileText, title: 'Penawaran yang jelas', text: 'Bantu pelanggan memahami fasilitas, harga, dan ketentuan sejak awal.' }, { icon: Wallet, title: 'Kerja sama transparan', text: 'Bahas biaya layanan dan mekanisme pembayaran sebelum memulai.' }, { icon: ShieldCheck, title: 'Tumbuh dengan kesiapan', text: 'Tinjau kelengkapan usaha dan standar layanan bersama tim kemitraan.' }]" :key="item.title" class="rounded-xl bg-white p-4 sm:p-0 sm:bg-transparent border border-slate-100 sm:border-0 shadow-xs sm:shadow-none">
                        <component :is="item.icon" class="size-5 sm:size-6 text-[#009cf0]" />
                        <h3 class="mt-3 sm:mt-4 text-sm sm:text-base font-bold text-[#07345a]">{{ item.title }}</h3>
                        <p class="mt-1.5 sm:mt-2 text-xs sm:text-sm leading-relaxed sm:leading-6 text-slate-500">{{ item.text }}</p>
                    </div>
                </div>
            </div>
        </section>

        <section id="cara-bergabung" class="business-container business-section">
            <p class="business-eyebrow">LANGKAH KECIL, PELUANG BARU</p>
            <div class="mt-3 sm:mt-4 flex flex-col justify-between gap-3 sm:gap-5 sm:flex-row sm:items-end">
                <h2 class="business-heading">Mulai dari usaha Anda.<br />Kami bantu langkah berikutnya.</h2>
                <button class="business-text-link self-start sm:self-auto" @click="openRegistration()">Ajukan kemitraan <ArrowUpRight class="size-4" /></button>
            </div>
            <ol class="mt-8 sm:mt-12 grid gap-6 sm:gap-8 grid-cols-1 sm:grid-cols-2 lg:grid-cols-4">
                <li v-for="(step, index) in steps" :key="step.title" class="border-t border-sky-200 pt-4 sm:pt-5">
                    <span class="text-2xl sm:text-3xl font-light text-[#009cf0]">0{{ index + 1 }}</span>
                    <h3 class="mt-3 sm:mt-5 font-bold text-[#07345a] text-sm sm:text-base">{{ step.title }}</h3>
                    <p class="mt-1.5 sm:mt-3 text-xs sm:text-sm leading-relaxed sm:leading-6 text-slate-500">{{ step.description }}</p>
                </li>
            </ol>
        </section>

        <template #dialogs>
            <dialog
                ref="registrationDialog"
                aria-labelledby="partner-dialog-title"
                aria-describedby="partner-dialog-description"
                style="overflow: hidden !important;"
                class="fixed inset-0 m-auto w-[calc(100%-24px)] sm:w-[calc(100%-32px)] max-w-lg !overflow-hidden rounded-3xl sm:rounded-[32px] border-0 p-0 bg-white shadow-[0_25px_60px_-15px_rgba(7,52,90,0.35)] backdrop:bg-slate-900/60 backdrop:backdrop-blur-xs open:flex flex-col max-h-[min(90vh,760px)]"
                @click="handleDialogBackdrop"
                @cancel="closeRegistration"
            >
                <form class="flex flex-col h-full max-h-[min(90vh,760px)] overflow-hidden" @submit.prevent="submitApplication">
                    <!-- Fixed Header inside modal card -->
                    <div class="px-6 pt-6 sm:px-8 sm:pt-7 pb-2 shrink-0 bg-white">
                        <div class="flex items-start justify-between gap-4">
                            <div>
                                <p class="business-eyebrow">KENALKAN USAHA ANDA</p>
                                <h2 id="partner-dialog-title" class="mt-1.5 sm:mt-2 text-xl sm:text-2xl font-bold text-[#07345a]">Ajukan kemitraan TapakLokal</h2>
                            </div>
                            <button
                                type="button"
                                aria-label="Tutup formulir"
                                class="grid size-9 shrink-0 place-items-center rounded-full text-slate-400 hover:bg-slate-100 hover:text-slate-700 transition-colors cursor-pointer"
                                @click="closeRegistration"
                            >
                                <X class="size-5" />
                            </button>
                        </div>
                        <p id="partner-dialog-description" class="mt-2 text-xs sm:text-sm leading-relaxed sm:leading-6 text-slate-500">
                            Lengkapi data usaha Anda. Pengajuan akan dikirim langsung ke admin pengelola vendor untuk ditinjau.
                        </p>
                    </div>

                    <!-- Scrollable Form Body: Inset so scrollbar is strictly inside modal card and never extends past rounded corners -->
                    <div class="partner-modal-scroll flex-1 overflow-y-auto pl-6 sm:pl-8 pr-5 sm:pr-7 py-2 space-y-3.5 sm:space-y-4">
                        <!-- Custom Styled Dropdown for Jenis Kemitraan -->
                        <div ref="trackDropdownRef" class="relative">
                            <label id="partnership-track-label" class="block text-xs font-semibold text-slate-700">
                                Jenis kemitraan
                            </label>
                            <button
                                type="button"
                                aria-haspopup="listbox"
                                :aria-expanded="isTrackDropdownOpen"
                                aria-labelledby="partnership-track-label"
                                class="mt-1.5 sm:mt-2 flex w-full items-center justify-between gap-3 rounded-xl border bg-white px-3.5 py-2.5 sm:py-3 text-left transition-all"
                                :class="isTrackDropdownOpen ? 'border-sky-500 ring-2 ring-sky-100 shadow-sm' : 'border-slate-200 hover:border-sky-300'"
                                @click="isTrackDropdownOpen = !isTrackDropdownOpen"
                            >
                                <div class="flex items-center gap-3 min-w-0">
                                    <span
                                        class="grid size-8 shrink-0 place-items-center rounded-lg"
                                        :class="selectedTrack === 'trip' ? 'bg-sky-50 text-sky-600' : 'bg-amber-50 text-amber-600'"
                                    >
                                        <component :is="currentTrackOption.icon" class="size-4" />
                                    </span>
                                    <div class="min-w-0">
                                        <p class="truncate text-xs sm:text-sm font-bold text-[#07345a]">
                                            {{ currentTrackOption.label }}
                                        </p>
                                        <p class="truncate text-[11px] text-slate-400">
                                            {{ currentTrackOption.description }}
                                        </p>
                                    </div>
                                </div>
                                <ChevronDown
                                    class="size-4 shrink-0 text-slate-400 transition-transform duration-200"
                                    :class="{ 'rotate-180 text-sky-600': isTrackDropdownOpen }"
                                />
                            </button>

                            <!-- Custom Dropdown Menu -->
                            <Transition
                                enter-active-class="transition duration-150 ease-out"
                                enter-from-class="opacity-0 -translate-y-1 scale-[0.98]"
                                enter-to-class="opacity-100 translate-y-0 scale-100"
                                leave-active-class="transition duration-100 ease-in"
                                leave-from-class="opacity-100 translate-y-0 scale-100"
                                leave-to-class="opacity-0 -translate-y-1 scale-[0.98]"
                            >
                                <div
                                    v-if="isTrackDropdownOpen"
                                    role="listbox"
                                    class="absolute left-0 top-full z-50 mt-1.5 w-full rounded-2xl border border-sky-100 bg-white p-1.5 shadow-[0_16px_40px_-8px_rgba(7,52,90,0.18)] ring-1 ring-black/5"
                                >
                                    <div class="space-y-1">
                                        <button
                                            v-for="opt in partnershipOptions"
                                            :key="opt.value"
                                            type="button"
                                            role="option"
                                            :aria-selected="selectedTrack === opt.value"
                                            class="group flex w-full items-center justify-between gap-3 rounded-xl p-2.5 text-left transition-all"
                                            :class="selectedTrack === opt.value ? 'bg-sky-50/80 text-sky-950 font-medium' : 'text-slate-700 hover:bg-slate-50'"
                                            @click="selectTrack(opt.value)"
                                        >
                                            <div class="flex items-center gap-3 min-w-0">
                                                <span
                                                    class="grid size-9 shrink-0 place-items-center rounded-xl transition-transform group-hover:scale-105"
                                                    :class="opt.value === 'trip' ? 'bg-sky-100 text-sky-600' : 'bg-amber-100 text-amber-700'"
                                                >
                                                    <component :is="opt.icon" class="size-4.5" />
                                                </span>
                                                <div class="min-w-0">
                                                    <p class="text-xs sm:text-sm font-bold text-[#07345a]">
                                                        {{ opt.label }}
                                                    </p>
                                                    <p class="text-[11px] text-slate-500">
                                                        {{ opt.description }}
                                                    </p>
                                                </div>
                                            </div>
                                            <span v-if="selectedTrack === opt.value" class="grid size-6 shrink-0 place-items-center rounded-full bg-sky-600 text-white shadow-xs">
                                                <Check class="size-3.5 stroke-[2.5]" />
                                            </span>
                                        </button>
                                    </div>
                                </div>
                            </Transition>
                        </div>

                        <label class="block text-xs font-semibold">Nama usaha<input v-model="brief.business" required maxlength="120" autocomplete="organization" class="business-input mt-1.5 sm:mt-2" /></label>
                        <div class="grid gap-3 sm:gap-4 sm:grid-cols-2">
                            <label class="block text-xs font-semibold">Nama penanggung jawab<input v-model="brief.name" required maxlength="100" autocomplete="name" class="business-input mt-1.5 sm:mt-2" /></label>
                            <label class="block text-xs font-semibold">Kota operasional<input v-model="brief.city" required maxlength="100" autocomplete="address-level2" class="business-input mt-1.5 sm:mt-2" /></label>
                        </div>
                        <label class="block text-xs font-semibold">Alamat Gmail<input v-model="brief.email" type="email" required maxlength="150" autocomplete="email" placeholder="nama@gmail.com" class="business-input mt-1.5 sm:mt-2" /><span class="mt-1 block text-[11px] font-normal text-slate-500">Wajib menggunakan alamat @gmail.com.</span></label><label class="block text-xs font-semibold">Nomor telepon<input v-model="brief.phone" type="tel" required maxlength="16" autocomplete="tel" placeholder="081234567890" class="business-input mt-1.5 sm:mt-2" /></label>
                        <label class="block text-xs font-semibold">Password untuk login<span class="relative mt-2 block"><input v-model="brief.password" :type="passwordVisible ? 'text' : 'password'" required minlength="10" autocomplete="new-password" class="business-input !pr-14 [&::-ms-reveal]:hidden" /><button type="button" :aria-label="passwordVisible ? 'Sembunyikan password' : 'Tampilkan password'" :aria-pressed="passwordVisible" class="absolute inset-y-0 right-1 my-auto grid size-11 place-items-center rounded-lg text-slate-400 transition-colors hover:bg-sky-50 hover:text-[#0099ef] focus-visible:outline-2 focus-visible:outline-[#0099ef]" @click="passwordVisible = !passwordVisible"><component :is="passwordVisible ? EyeOff : Eye" class="size-5" /></button></span><span class="mt-1 block text-[11px] font-normal text-slate-500">Minimal 10 karakter, mengandung huruf dan angka. Login vendor aktif setelah verifikasi admin.</span></label><label class="block text-xs font-semibold">Konfirmasi password<span class="relative mt-2 block"><input v-model="brief.password_confirmation" :type="confirmationVisible ? 'text' : 'password'" required minlength="10" autocomplete="new-password" class="business-input !pr-14 [&::-ms-reveal]:hidden" /><button type="button" :aria-label="confirmationVisible ? 'Sembunyikan konfirmasi password' : 'Tampilkan konfirmasi password'" :aria-pressed="confirmationVisible" class="absolute inset-y-0 right-1 my-auto grid size-11 place-items-center rounded-lg text-slate-400 transition-colors hover:bg-sky-50 hover:text-[#0099ef] focus-visible:outline-2 focus-visible:outline-[#0099ef]" @click="confirmationVisible = !confirmationVisible"><component :is="confirmationVisible ? EyeOff : Eye" class="size-5" /></button></span></label><label class="block text-xs font-semibold">Ceritakan paket atau produk Anda<textarea v-model="brief.notes" rows="3" maxlength="1500" class="business-input mt-1.5 sm:mt-2"></textarea></label>
                    </div>

                    <!-- Fixed Footer inside modal card -->
                    <div class="px-6 pb-6 pt-3 sm:px-8 sm:pb-7 shrink-0 bg-white">
                        <p v-if="submitted" role="status" class="mb-3 rounded-xl bg-emerald-50 p-3 text-sm text-emerald-700">Pengajuan dan akun berhasil dibuat. Gunakan Gmail dan password tadi untuk login vendor setelah verifikasi admin.</p><p v-for="(error, field) in brief.errors" :key="field" role="alert" class="mb-2 text-xs text-rose-600">{{ error }}</p><button type="submit" :disabled="brief.processing" class="business-button inline-flex w-full disabled:opacity-60">
                            {{ brief.processing ? 'Mengirim…' : 'Kirim pengajuan' }} <ArrowUpRight class="size-4" />
                        </button>
                        <p class="mt-2 text-center text-[11px] sm:text-xs leading-5 text-slate-500">
                            Ditinjau oleh admin pengelola vendor TapakLokal
                        </p>
                    </div>
                </form>
            </dialog>
        </template>
    </BusinessLanding>
</template>

<style scoped>
.partner-modal-scroll {
    scrollbar-width: thin;
    scrollbar-color: #cbd5e1 transparent;
    -webkit-overflow-scrolling: touch;
    overscroll-behavior: contain;
}
.partner-modal-scroll::-webkit-scrollbar {
    width: 6px;
}
.partner-modal-scroll::-webkit-scrollbar-track {
    background: transparent;
    margin: 20px 0;
}
.partner-modal-scroll::-webkit-scrollbar-thumb {
    background: #cbd5e1;
    border-radius: 9999px;
}
.partner-modal-scroll::-webkit-scrollbar-thumb:hover {
    background: #94a3b8;
}
</style>
