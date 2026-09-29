<script setup>
import { computed, ref } from 'vue';
import { Head, Link } from '@inertiajs/vue3';
import { route } from 'ziggy-js';
import {
    Menu, UserRound, Globe, ArrowRight, ArrowUpRight, CalendarDays, Check,
    ChevronDown, Compass, FileText, Info, MapPin, MessageCircle, RotateCcw,
    ShoppingBag, Users, Wallet, X, CreditCard, ShieldCheck, Sparkles,
    AlertTriangle, TrendingUp, Plane, Lock
} from 'lucide-vue-next';
import MainFooter from '../Components/Shared/MainFooter.vue';

const mobileNavigationOpen = ref(false);
const corporateLinks = [
    { href: '#solutions', label: 'Solusi Bisnis' },
    { href: '#features', label: 'Fitur Unggulan' },
    { href: '#benefits', label: 'Manfaat Tim' },
    { href: '#faq', label: 'Tanya Jawab' },
];
// Existing partner assets are initial logo choices; replace with corporate client logos before publishing.
const corporateLogos = [
    { name: 'Millennium Hotels and Resorts', src: '/Assets/Images/partners/partner-1.svg' },
    { name: 'ALL Accor Live Limitless', src: '/Assets/Images/partners/partner-2.svg' },
    { name: 'Archipelago', src: '/Assets/Images/partners/partner-3.svg' },
    { name: 'IHG Hotels and Resorts', src: '/Assets/Images/partners/partner-4.svg' },
    { name: 'Ascott The Residence', src: '/Assets/Images/partners/partner-5.svg' },
];
const trustedTripVendors = [
    { name: 'Explorer Tour', src: '/Assets/Images/logo-vendor/explorer.webp' },
    { name: 'Campa Tour', src: '/Assets/Images/logo-vendor/campatour.webp' },
    { name: 'Kili Kili Adventure', src: '/Assets/Images/logo-vendor/kilikili.webp' },
    { name: 'Java Wisata', src: '/Assets/Images/logo-vendor/javawisata.webp' },
    { name: 'Tour Bandung', src: '/Assets/Images/logo-vendor/tourbandung.webp' },
    { name: 'Raja Wisata', src: '/Assets/Images/logo-vendor/rajawisata.webp' },
    { name: 'Labiru Tour', src: '/Assets/Images/logo-vendor/labirutour.webp' },
    { name: 'Indonesia Juara', src: '/Assets/Images/logo-vendor/indonesiajuara.webp' },
    { name: 'Fun Trips', src: '/Assets/Images/logo-vendor/funtrips.webp' },
    { name: 'Brenggo Tour', src: '/Assets/Images/logo-vendor/brenggo.webp' },
];
const hoveredRole = ref(null);
const pinnedRole = ref(null);
const openFaq = ref(0);
const consultationDialog = ref(null);
const brief = ref({ company: '', name: '', destination: '', participants: '', notes: '' });
const roles = [
    { title: 'Untuk PIC perjalanan', english: 'HR, GA & KOORDINATOR TRIP', subtitle: 'Lebih terencana, lebih tenang.', position: '0%', points: ['Temukan paket sesuai agenda dan anggaran perusahaan', 'Rangkum tanggal dan kebutuhan seluruh peserta', 'Diskusikan detail fasilitas trip sebelum memesan'], summary: 'Dari mengumpulkan kebutuhan hingga menentukan itinerary, semua beres dalam satu brief yang jelas.', label: 'HR, GA & koordinator tim' },
    { title: 'Untuk peserta', english: 'KARYAWAN & PESERTA PERJALANAN', subtitle: 'Fokus pada pengalaman seru.', position: '50%', points: ['Kenali itinerary lengkap dan fasilitas perjalanan', 'Jelajahi destinasi seru bersama pemandu lokal terbaik', 'Sampaikan kebutuhan khusus tim kepada PIC dengan mudah'], summary: 'Lebih banyak ruang untuk rehat, membangun keakraban, dan menikmati destinasi bersama rekan kerja.', label: 'Karyawan & peserta perjalanan' },
    { title: 'Untuk tim finance', english: 'TIM KEUANGAN & FINANCE', subtitle: 'Anggaran transparan dan terkontrol.', position: '100%', points: ['Tetapkan batas plafon anggaran sejak awal perencanaan', 'Tinjau rincian biaya penawaran secara transparan', 'Pilih skema pembayaran tempo atau invoicing resmi'], summary: 'Laporan pengeluaran tercatat rapi sebagai dasar persetujuan internal dan kepatuhan pajak kantor.', label: 'Finance & procurement' },
];
const faqs = [
    { question: 'Apa itu TapakLokal for Corporates?', answer: 'TapakLokal for Corporates adalah solusi perjalanan bisnis dan corporate gathering terpadu dari TapakLokal. Kami membantu perusahaan merencanakan agenda outing, gathering tahunan, business trip, hingga private trip dengan alur yang mudah, transparan, dan hemat anggaran.' },
    { question: 'Layanan apa saja yang bisa didiskusikan dan dipesan?', answer: 'Anda dapat merencanakan company gathering, outing kantor, private trip rombongan, aktivitas team building lokal, hingga penyediaan transportasi, akomodasi, konsumsi, dan pemandu lokal profesional.' },
    { question: 'Apakah ada batas minimum jumlah peserta?', answer: 'Kapasitas peserta sangat fleksibel mulai dari rombongan kecil (tim divisi) hingga gathering skala besar ratusan orang. Cantumkan estimasi jumlah peserta saat konsultasi agar penawaran paket dapat disesuaikan.' },
    { question: 'Bagaimana dengan sistem persetujuan (approval) dan invoice perusahaan?', answer: 'Kami mendukung kebutuhan administrasi perusahaan, termasuk penerbitan invoice resmi, bukti transaksi digital, e-Faktur Pajak, serta penyesuaian alur persetujuan internal dan skema pembayaran yang disepakati.' },
    { question: 'Bagaimana skema pembayaran yang tersedia?', answer: 'Tersedia pilihan pembayaran fleksibel mulai dari kartu kredit korporat, transfer bank, hingga skema invoicing dengan termin pembayaran (term of payment) sesuai kesepakatan kerjasama perusahaan.' },
    { question: 'Bagaimana cara memulai konsultasi perjalanan tim?', answer: 'Klik tombol “Mulai sekarang” atau “Konsultasikan perjalanan”, lengkapi brief singkat kebutuhan tim Anda, lalu kirimkan email ke tim konsultan kami. Kami akan segera menghubungi Anda dengan rekomendasi terbaik.' },
];
const emailHref = computed(() => {
    const body = `Halo tim TapakLokal,\n\nSaya ingin berdiskusi tentang perjalanan perusahaan.\nPerusahaan: ${brief.value.company}\nNama PIC: ${brief.value.name}\nDestinasi: ${brief.value.destination || 'Butuh rekomendasi'}\nJumlah peserta: ${brief.value.participants}\nKebutuhan: ${brief.value.notes}\n\nTerima kasih.`;
    return `mailto:support@tapaklokal.com?subject=${encodeURIComponent(`Konsultasi corporate — ${brief.value.company}`)}&body=${encodeURIComponent(body)}`;
});
function openConsultation() { consultationDialog.value?.showModal(); }
function prepareEmail() { window.location.href = emailHref.value; }
function isFlipped(index) { return hoveredRole.value === index || pinnedRole.value === index; }
function hoverRole(event, index) { if (event.pointerType === 'mouse') hoveredRole.value = index; }
</script>

<template>
    <Head title="Solusi Perjalanan Bisnis & Corporate Gathering — TapakLokal">
        <meta name="description" content="Kelola perjalanan bisnis, company gathering, dan agenda tim perusahaan Anda dengan mudah bersama TapakLokal. Praktis, transparan, dan hemat anggaran." />
    </Head>
    <div class="corporate-page min-h-screen bg-white font-sans text-slate-800">
        <header class="corporate-header sticky top-0 z-40 bg-white/95 backdrop-blur-md" @keydown.esc="mobileNavigationOpen = false">
            <nav aria-label="Navigasi corporate" class="mx-auto flex h-[68px] max-w-[1600px] items-center justify-between gap-5 px-5 sm:px-8">
                <Link :href="route('home')" aria-label="TapakLokal beranda" class="flex shrink-0 items-center gap-3">
                    <span class="text-[23px] font-extrabold tracking-[-0.065em] text-slate-800">tapak<span class="text-[#009cf0]">lokal</span></span>
                    <span class="border-l border-slate-200 pl-3 text-[10px] leading-tight font-semibold tracking-wide text-slate-500">FOR<br /><span class="text-[13px] font-bold text-[#009cf0]">CORPORATES</span></span>
                </Link>
                <div class="hidden items-center gap-7 text-xs font-semibold text-slate-800 lg:flex">
                    <a v-for="item in corporateLinks" :key="item.href" :href="item.href" class="py-3 transition hover:text-[#009cf0]">{{ item.label }}</a>
                </div>
                <div class="flex shrink-0 items-center gap-3">
                    <span class="mr-2 hidden items-center gap-1.5 text-xs text-slate-600 xl:flex" aria-label="Bahasa Indonesia"><Globe class="size-4" /> ID</span>
                    <Link :href="route('home', { auth: 'login' })" class="hidden items-center gap-2 rounded-full bg-[#e1f4ff] px-4 py-2.5 text-xs font-bold text-[#075890] sm:inline-flex"><UserRound class="size-4" /> Masuk</Link>
                    <button class="corp-button !hidden !bg-[#009cf0] !px-6 !py-2.5 !text-xs sm:!inline-flex" @click="openConsultation">Mulai sekarang</button>
                    <button class="rounded-lg p-2 text-[#07345a] lg:hidden" :aria-expanded="mobileNavigationOpen" aria-controls="corporate-mobile-navigation" :aria-label="mobileNavigationOpen ? 'Tutup menu corporate' : 'Buka menu corporate'" @click="mobileNavigationOpen = !mobileNavigationOpen"><X v-if="mobileNavigationOpen" class="size-6" /><Menu v-else class="size-6" /></button>
                </div>
            </nav>
            <nav v-if="mobileNavigationOpen" id="corporate-mobile-navigation" aria-label="Navigasi corporate mobile" class="border-t border-slate-100 bg-white px-5 pb-5 lg:hidden">
                <a v-for="item in corporateLinks" :key="item.href" :href="item.href" class="block border-b border-slate-100 py-3 text-sm font-semibold text-slate-700" @click="mobileNavigationOpen = false">{{ item.label }}</a>
                <div class="mt-4 flex gap-3 sm:hidden"><Link :href="route('home', { auth: 'login' })" class="flex flex-1 items-center justify-center rounded-full bg-sky-50 py-3 text-sm font-semibold text-[#075890]">Masuk</Link><button class="corp-button flex-1 justify-center !py-3" @click="mobileNavigationOpen = false; openConsultation()">Mulai sekarang</button></div>
            </nav>
        </header>
        <main>
            <section id="corporate-hero" class="corporate-hero overflow-hidden">
                <div class="px-5 pt-6 text-center sm:pt-8 lg:pt-8">
                    <h1 class="hero-title mx-auto max-w-5xl text-[26px] leading-[1.3] font-bold tracking-[-0.035em] text-[#26292c] sm:text-[34px] lg:text-[38px]">
                        Cara lebih <span class="hero-handwritten">mudah</span><br />mengatur perjalanan perusahaan
                    </h1>
                </div>
                <div class="my-auto flex flex-col items-center">
                    <div class="hero-composition" role="img" aria-label="Ilustrasi laptop dan ponsel TapakLokal dengan brief perjalanan, agenda tim, serta perencanaan anggaran.">
                        <img src="/Assets/Images/corporate-device-mockup.png" alt="" width="1536" height="1024" fetchpriority="high" class="hero-devices" />
                        <div class="hero-float hero-float-brief" aria-hidden="true">
                            <p class="hero-card-title">Satu brief,<br />detail perjalanan lebih jelas</p>
                            <div class="mt-2 sm:mt-3 flex border-b border-slate-100 text-[8px] sm:text-[9px] font-semibold"><span class="flex-1 pb-1.5 sm:pb-2 text-slate-400">Kebutuhan tim</span><span class="flex-1 border-b-2 border-[#8fd600] pb-1.5 sm:pb-2 text-[#009cf0]">Rencana trip</span></div>
                            <div class="mt-1.5 sm:mt-2 grid grid-cols-3 gap-1 sm:gap-2 rounded-lg bg-[#f5fafd] p-1.5 sm:p-2 text-[8px] sm:text-[9px] text-center"><span class="text-slate-400">Destinasi</span><span class="text-slate-400">Peserta</span><span class="text-slate-400">Durasi</span><strong class="truncate">Bali</strong><strong class="truncate">24 orang</strong><strong class="truncate">3 hari</strong></div>
                        </div>
                        <div class="hero-float hero-float-agenda" aria-hidden="true">
                            <p class="hero-card-title">Agenda yang pas untuk tim</p>
                            <div class="relative mt-2 sm:mt-3 flex items-center justify-center gap-2 sm:gap-3 rounded-lg bg-[#f1fbff] py-2 sm:py-3"><span class="text-[8px] sm:text-[9px] leading-3.5 sm:leading-4 text-slate-500">Aktivitas<br /><strong class="text-[#009cf0]">bersama</strong></span><div class="hero-donut"><span><strong class="block text-base sm:text-xl leading-5 sm:leading-6 text-[#07345a]">3 hari</strong><span class="text-[7px] sm:text-[8px] text-slate-500">Penuh cerita</span></span></div><span class="text-[8px] sm:text-[9px] leading-3.5 sm:leading-4 text-slate-500">Waktu<br /><strong class="text-[#2761a4]">bebas</strong></span></div>
                        </div>
                        <div class="hero-float hero-float-budget" aria-hidden="true">
                            <p class="hero-card-title">Rencanakan sesuai anggaran Anda</p>
                            <div class="relative mt-2 sm:mt-3 flex h-24 sm:h-28 items-center justify-center overflow-hidden rounded-lg bg-[#effaff]"><div class="absolute size-28 sm:size-32 rounded-full border-[16px] sm:border-[20px] border-[#dcf5ff]"></div><div class="absolute h-14 sm:h-16 w-20 sm:w-24 -translate-x-3 -translate-y-1 rotate-[-17deg] rounded-lg bg-[#94d900]"></div><div class="relative h-14 sm:h-16 w-20 sm:w-24 rotate-[-8deg] rounded-lg bg-[#009ef1] p-2.5 sm:p-3 text-white shadow-lg"><Wallet class="size-6 sm:size-7" /><span class="mt-1 block h-1 w-10 sm:w-12 rounded bg-[#075a9e]"></span></div><Check class="absolute top-2 right-4 sm:right-8 size-4 sm:size-5 text-[#83c800]" /></div>
                        </div>
                        <div class="hero-float hero-float-ticket" aria-hidden="true">
                            <p class="hero-card-title">Persetujuan & tiket instan</p>
                            <div class="mt-2 sm:mt-2.5 rounded-xl border border-sky-100 bg-gradient-to-br from-[#f6fbff] to-[#edf7fe] p-2 sm:p-2.5 shadow-2xs">
                                <div class="flex items-center justify-between">
                                    <span class="text-[8px] sm:text-[9px] font-bold text-[#07345a]">E-Tiket & Hotel</span>
                                    <span class="rounded-full bg-emerald-500 px-1.5 sm:px-2 py-0.5 text-[7px] sm:text-[8px] font-bold text-white shadow-2xs">Siap Digunakan ✓</span>
                                </div>
                                <div class="mt-1.5 sm:mt-2 flex items-center gap-1.5 sm:gap-2 rounded-lg bg-white p-1.5 sm:p-2 border border-sky-100 shadow-2xs">
                                    <span class="flex size-6 sm:size-7 shrink-0 items-center justify-center rounded-md bg-sky-50 text-[#0088ff] border border-sky-100">
                                        <Plane class="size-3 sm:size-3.5" />
                                    </span>
                                    <div class="min-w-0 flex-1">
                                        <span class="block truncate text-[9px] sm:text-[10px] font-bold text-slate-700">CGK ⇄ DPS • 24 Tiket</span>
                                        <span class="block text-[7px] sm:text-[8px] text-emerald-600 font-semibold truncate">Terkonfirmasi otomatis</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <p class="mt-3 text-center text-[10px] tracking-wide text-[#4d7e99]">Ilustrasi pengalaman TapakLokal · Sesuaikan perjalanan melalui konsultasi</p>
                </div>
                <div class="corporate-trust-strip" aria-label="Trusted by 100+ companies">
                    <div class="trust-badge">
                        <svg class="trust-laurel" viewBox="0 0 32 64" fill="currentColor" aria-hidden="true">
                            <path d="M27 60C8 49 6 23 24 5" fill="none" stroke="currentColor" stroke-width="1.5" />
                            <path d="M23 10c-5-1-5-6-2-10 4 3 5 7 2 10ZM17 18c-6-1-8-6-6-11 5 2 8 6 6 11ZM12 28C6 27 3 22 4 17c6 1 9 5 8 11ZM10 39C3 37 0 32 1 27c6 2 10 6 9 12ZM14 50C7 50 2 46 2 40c7 1 11 4 12 10ZM22 59C15 62 9 60 7 54c6-2 12 0 15 5ZM19 18c0-6 4-9 10-9-1 5-5 9-10 9ZM14 28c1-6 6-9 11-8-2 5-6 8-11 8ZM13 40c1-6 5-9 11-8-2 5-6 8-11 8ZM17 50c1-6 5-8 11-7-2 5-6 8-11 7Z" />
                        </svg>
                        <p>Dipercaya oleh <strong>100+</strong><br />perusahaan</p>
                        <svg class="trust-laurel -scale-x-100" viewBox="0 0 32 64" fill="currentColor" aria-hidden="true">
                            <path d="M27 60C8 49 6 23 24 5" fill="none" stroke="currentColor" stroke-width="1.5" />
                            <path d="M23 10c-5-1-5-6-2-10 4 3 5 7 2 10ZM17 18c-6-1-8-6-6-11 5 2 8 6 6 11ZM12 28C6 27 3 22 4 17c6 1 9 5 8 11ZM10 39C3 37 0 32 1 27c6 2 10 6 9 12ZM14 50C7 50 2 46 2 40c7 1 11 4 12 10ZM22 59C15 62 9 60 7 54c6-2 12 0 15 5ZM19 18c0-6 4-9 10-9-1 5-5 9-10 9ZM14 28c1-6 6-9 11-8-2 5-6 8-11 8ZM13 40c1-6 5-9 11-8-2 5-6 8-11 8ZM17 50c1-6 5-8 11-7-2 5-6 8-11 7Z" />
                        </svg>
                    </div>
                    <div class="corporate-logo-marquee" tabindex="0" aria-label="Logo perusahaan. Arahkan kursor atau fokuskan untuk menghentikan animasi.">
                        <div class="corporate-logo-track">
                            <div v-for="copy in 3" :key="copy" class="corporate-logo-group" :aria-hidden="copy > 1 ? true : undefined">
                                <img v-for="logo in corporateLogos" :key="logo.src" :src="logo.src" :alt="copy === 1 ? logo.name : ''" width="150" height="45" class="corporate-company-logo" />
                            </div>
                        </div>
                    </div>
                </div>
            </section>
            <section id="solutions" class="corp-container py-14 sm:py-16 lg:py-24">
                <div class="mx-auto max-w-4xl text-center">
                    <h2 class="text-2xl font-extrabold tracking-tight text-[#07345a] sm:text-4xl lg:text-[42px] lg:leading-[1.25]">
                        Kelola Perjalanan Bisnis Tim Anda Bersama
                        <br class="hidden sm:inline" />
                        <span class="text-[#0088ff]">TapakLokal Corporates</span>
                    </h2>
                </div>

                <div class="mt-10 sm:mt-12 grid grid-cols-1 gap-6 sm:gap-8 lg:grid-cols-2">
                    <!-- 1. Corporate Booking Platform (Spans 2 columns on lg) -->
                    <div class="group relative overflow-hidden rounded-2xl sm:rounded-[28px] border border-slate-200/80 bg-gradient-to-br from-white via-[#f7fbfe] to-[#eaf5fc] p-6 sm:p-10 lg:col-span-2 lg:p-12 shadow-[0_12px_40px_-15px_rgba(7,52,90,0.06)] transition-all duration-300 hover:border-sky-300 hover:shadow-[0_20px_50px_-20px_rgba(0,136,255,0.12)]">
                        <div class="grid grid-cols-1 items-center gap-8 lg:grid-cols-12 lg:gap-12">
                            <!-- Left Copy -->
                            <div class="lg:col-span-5">
                                <h3 class="text-xl font-bold tracking-tight text-[#07345a] sm:text-3xl">Portal Pemesanan Perusahaan</h3>
                                <p class="mt-2.5 sm:mt-3 text-xs leading-relaxed text-slate-600 sm:text-base">
                                    Pesan tiket pesawat, hotel, dan trip tim langsung dalam satu platform terpadu, lengkap dengan pengaturan batas anggaran serta laporan keuangan transparan.
                                </p>
                                <ul class="mt-4 sm:mt-5 space-y-2.5 sm:space-y-3 text-xs sm:text-sm text-slate-700">
                                    <li class="flex items-start gap-2.5">
                                        <span class="mt-1.5 size-1.5 shrink-0 rounded-full bg-[#0088ff]"></span>
                                        <span>Daftar akun perusahaan, dapatkan persetujuan, dan langsung pesan—tanpa instalasi aplikasi tambahan</span>
                                    </li>
                                    <li class="flex items-start gap-2.5">
                                        <span class="mt-1.5 size-1.5 shrink-0 rounded-full bg-[#0088ff]"></span>
                                        <span>Rencanakan dan pantau seluruh agenda perjalanan bisnis di mana saja dan kapan saja</span>
                                    </li>
                                </ul>
                                <button type="button" class="corp-button mt-6 sm:mt-7 !px-6 !py-3 !text-xs sm:!text-sm" @click="openConsultation">
                                    Mulai sekarang
                                    <ArrowRight class="size-4" />
                                </button>
                            </div>

                            <!-- Right Mockup Image -->
                            <div class="relative flex items-center justify-center lg:col-span-7">
                                <div class="absolute -right-10 -top-10 size-72 rounded-full bg-sky-300/30 blur-3xl pointer-events-none"></div>
                                <img
                                    src="/Assets/Images/laptop-bawah.png"
                                    alt="TapakLokal Corporate Booking Platform UI di Laptop dan Smartphone"
                                    width="1200"
                                    height="800"
                                    loading="lazy"
                                    class="relative z-10 w-full max-w-[580px] object-contain drop-shadow-[0_15px_35px_rgba(7,52,90,0.15)] transition-transform duration-500 motion-safe:group-hover:scale-[1.02]"
                                />
                            </div>
                        </div>
                    </div>

                    <!-- 2. Personalized Services (Bottom Left) -->
                    <div class="group relative flex flex-col justify-between overflow-hidden rounded-2xl sm:rounded-[28px] border border-slate-200/80 bg-white p-6 sm:p-9 shadow-[0_12px_40px_-15px_rgba(7,52,90,0.06)] transition-all duration-300 hover:border-sky-300 hover:shadow-[0_20px_50px_-20px_rgba(0,136,255,0.12)]">
                        <div>
                            <h3 class="text-xl font-bold tracking-tight text-[#07345a] sm:text-2xl">Layanan Pendampingan Khusus</h3>
                            <p class="mt-2.5 sm:mt-3 text-xs leading-relaxed text-slate-600 sm:text-sm">
                                Butuh paket gathering khusus, outing, atau agenda perusahaan unik? Tim konsultan perjalanan kami siap mendampingi Anda setiap saat.
                            </p>
                            <ul class="mt-4 sm:mt-5 space-y-2.5 text-xs sm:text-sm text-slate-700">
                                <li class="flex items-start gap-2.5">
                                    <span class="mt-1.5 size-1.5 shrink-0 rounded-full bg-[#0088ff]"></span>
                                    <span>Konsultasi gratis untuk gathering perusahaan, retreat, dan paket rombongan besar</span>
                                </li>
                                <li class="flex items-start gap-2.5">
                                    <span class="mt-1.5 size-1.5 shrink-0 rounded-full bg-[#0088ff]"></span>
                                    <span>Dukungan tim bantuan profesional dan responsif untuk setiap kebutuhan tim Anda</span>
                                </li>
                            </ul>
                        </div>

                        <!-- Chat & Live CS Display -->
                        <div class="bento-dot-bg relative mt-6 sm:mt-8 flex min-h-[190px] sm:min-h-[200px] flex-col justify-center rounded-2xl border border-sky-100/80 bg-[#f8fbfe] p-4 sm:p-6">
                            <div class="relative mx-auto w-full max-w-xs space-y-3">
                                <!-- CS Avatar & Speech Bubble -->
                                <div class="flex items-start gap-2.5 sm:gap-3">
                                    <div class="relative shrink-0">
                                        <img src="https://images.unsplash.com/photo-1573496359142-b8d87734a5a2?auto=format&fit=crop&w=120&q=80" alt="Aurelia CS" class="size-9 sm:size-10 rounded-full border-2 border-white object-cover shadow-sm" />
                                        <span class="absolute bottom-0 right-0 size-2.5 sm:size-3 rounded-full border-2 border-white bg-emerald-500"></span>
                                    </div>
                                    <div class="rounded-2xl rounded-tl-xs border border-sky-200/90 bg-white px-3.5 sm:px-4 py-2.5 sm:py-3 shadow-sm">
                                        <span class="block text-[9px] sm:text-[10px] font-bold text-[#0088ff]">Aurelia, Konsultan TapakLokal Bisnis</span>
                                        <p class="mt-0.5 text-xs sm:text-sm font-semibold text-[#07345a]">Halo! Ada agenda perjalanan tim yang ingin kami bantu?</p>
                                    </div>
                                </div>
                                <!-- Typing Indicator Bubble -->
                                <div class="ml-9 sm:ml-12 flex w-fit items-center gap-1.5 rounded-full border border-sky-100 bg-white/95 px-3 py-1.5 shadow-xs">
                                    <span class="typing-dot size-1.5 rounded-full bg-[#0088ff]"></span>
                                    <span class="typing-dot size-1.5 rounded-full bg-[#0088ff]"></span>
                                    <span class="typing-dot size-1.5 rounded-full bg-[#0088ff]"></span>
                                    <span class="ml-1 text-[8px] sm:text-[9px] font-medium text-slate-400">Konsultan siap membantu</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- 3. API Integration (Bottom Right) -->
                    <div class="group relative flex flex-col justify-between overflow-hidden rounded-2xl sm:rounded-[28px] border border-slate-200/80 bg-white p-6 sm:p-9 shadow-[0_12px_40px_-15px_rgba(7,52,90,0.06)] transition-all duration-300 hover:border-sky-300 hover:shadow-[0_20px_50px_-20px_rgba(0,136,255,0.12)]">
                        <div>
                            <h3 class="text-xl font-bold tracking-tight text-[#07345a] sm:text-2xl">Integrasi Sistem & API Perusahaan</h3>
                            <p class="mt-2.5 sm:mt-3 text-xs leading-relaxed text-slate-600 sm:text-sm">
                                Hubungkan inventaris trip, tiket, dan seluruh kapabilitas platform kami langsung ke sistem internal perusahaan Anda.
                            </p>
                            <ul class="mt-4 sm:mt-5 space-y-2.5 text-xs sm:text-sm text-slate-700">
                                <li class="flex items-start gap-2.5">
                                    <span class="mt-1.5 size-1.5 shrink-0 rounded-full bg-[#0088ff]"></span>
                                    <span>Gunakan alur pemesanan, pengecekan kebijakan anggaran, dan rekap di sistem HR/ERP Anda</span>
                                </li>
                                <li class="flex items-start gap-2.5">
                                    <span class="mt-1.5 size-1.5 shrink-0 rounded-full bg-[#0088ff]"></span>
                                    <span>Pengalaman pemesanan yang cepat, aman, dan terhubung mulus antar platform</span>
                                </li>
                            </ul>
                        </div>

                        <!-- Architecture Diagram Visual -->
                        <div class="relative mt-6 sm:mt-8 flex min-h-[190px] sm:min-h-[220px] items-center justify-center rounded-2xl border border-sky-100/80 bg-gradient-to-b from-[#f8fcff] to-[#edf7fc] p-4 sm:p-6 overflow-hidden">
                            <div class="relative flex w-full max-w-xs items-center justify-between z-10">
                                <!-- TapakLokal Core API Node (Blue Isometric Stack with Concentric Radar Circles exactly behind it) -->
                                <div class="flex flex-col items-center">
                                    <div class="relative flex size-12 sm:size-14 items-center justify-center">
                                        <!-- Concentric Radar Rings EXACTLY Centered Behind Icon -->
                                        <div class="pointer-events-none absolute left-1/2 top-1/2 -translate-x-1/2 -translate-y-1/2 -z-10 flex items-center justify-center">
                                            <!-- Static Concentric Rings -->
                                            <div class="absolute size-20 sm:size-24 rounded-full border border-sky-300/50"></div>
                                            <div class="absolute size-32 sm:size-36 rounded-full border border-sky-300/40"></div>
                                            <div class="absolute size-44 sm:size-48 rounded-full border border-sky-200/35"></div>
                                            <div class="absolute size-56 sm:size-64 rounded-full border border-sky-200/25"></div>
                                            <div class="absolute size-72 sm:size-80 rounded-full border border-sky-100/20"></div>

                                            <!-- Animated Expanding Radar Waves -->
                                            <div class="radar-pulse-ring size-20 sm:size-24"></div>
                                            <div class="radar-pulse-ring size-20 sm:size-24 [animation-delay:1s]"></div>
                                            <div class="radar-pulse-ring size-20 sm:size-24 [animation-delay:2s]"></div>
                                        </div>

                                        <svg viewBox="0 0 64 64" class="relative z-10 size-12 sm:size-14 drop-shadow-md">
                                            <ellipse cx="32" cy="46" rx="26" ry="10" fill="#0066cc" />
                                            <ellipse cx="32" cy="42" rx="26" ry="10" fill="#0088ff" />
                                            <ellipse cx="32" cy="38" rx="26" ry="10" fill="#29c2ea" />
                                            <ellipse cx="32" cy="30" rx="26" ry="10" fill="#0066cc" />
                                            <ellipse cx="32" cy="26" rx="26" ry="10" fill="#0088ff" />
                                            <ellipse cx="32" cy="22" rx="26" ry="10" fill="#38bdf8" />
                                            <ellipse cx="32" cy="14" rx="26" ry="10" fill="#0077df" />
                                            <ellipse cx="32" cy="10" rx="26" ry="10" fill="#60a5fa" />
                                            <path d="M32 4 L36 10 L32 16 L28 10 Z" fill="white" />
                                        </svg>
                                    </div>
                                    <span class="mt-1.5 text-[9px] sm:text-[10px] font-bold text-[#07345a]">API TapakLokal</span>
                                </div>

                                <!-- Connecting Flow Line with Arrow -->
                                <div class="relative flex-1 mx-2 flex items-center justify-center">
                                    <svg class="w-full h-7 sm:h-8 overflow-visible" viewBox="0 0 100 20" fill="none">
                                        <defs>
                                            <linearGradient id="streamGrad" x1="0%" y1="0%" x2="100%" y2="0%">
                                                <stop offset="0%" stop-color="#0088ff" />
                                                <stop offset="100%" stop-color="#10b981" />
                                            </linearGradient>
                                        </defs>
                                        <line x1="6" y1="10" x2="86" y2="10" stroke="#dbeafe" stroke-width="2.5" stroke-linecap="round" />
                                        <line x1="6" y1="10" x2="86" y2="10" stroke="url(#streamGrad)" stroke-width="2.5" stroke-linecap="round" class="flow-line-animated" />
                                        <polygon points="86,6 96,10 86,14" fill="#10b981" />
                                    </svg>
                                    <div class="data-particle absolute left-1 top-1/2 -translate-y-1/2 size-1.5 sm:size-2 rounded-full bg-[#0088ff] shadow-[0_0_6px_#0088ff]"></div>
                                </div>

                                <!-- Corporate Enterprise System Node (Green Isometric Stack) -->
                                <div class="flex flex-col items-center">
                                    <div class="relative flex size-12 sm:size-14 items-center justify-center">
                                        <svg viewBox="0 0 64 64" class="size-12 sm:size-14 drop-shadow-md">
                                            <ellipse cx="32" cy="46" rx="26" ry="10" fill="#047857" />
                                            <ellipse cx="32" cy="42" rx="26" ry="10" fill="#059669" />
                                            <ellipse cx="32" cy="38" rx="26" ry="10" fill="#10b981" />
                                            <ellipse cx="32" cy="30" rx="26" ry="10" fill="#047857" />
                                            <ellipse cx="32" cy="26" rx="26" ry="10" fill="#059669" />
                                            <ellipse cx="32" cy="22" rx="26" ry="10" fill="#34d399" />
                                            <ellipse cx="32" cy="14" rx="26" ry="10" fill="#059669" />
                                            <ellipse cx="32" cy="10" rx="26" ry="10" fill="#6ee7b7" />
                                            <path d="M26 10 L30 14 L38 6" stroke="white" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" fill="none" />
                                        </svg>
                                    </div>
                                    <span class="mt-1.5 text-[9px] sm:text-[10px] font-bold text-[#07345a]">Sistem Kantor</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
            <section id="features" class="relative overflow-hidden border-t border-sky-100/60 bg-[#f6fbfe] py-16 sm:py-20 lg:py-28">
                <div class="corp-container">
                    <!-- Section Heading -->
                    <div class="mx-auto max-w-4xl text-center">
                        <h2 class="text-2xl font-extrabold tracking-tight text-[#07345a] sm:text-4xl lg:text-[42px] lg:leading-[1.25]">
                            Semua Fitur Lengkap untuk Perjalanan Bisnis yang Lebih Hemat, Praktis & Bebas Repot
                        </h2>
                        <p class="mx-auto mt-3.5 sm:mt-4 max-w-2xl text-xs sm:text-base leading-relaxed text-slate-600">
                            Dirancang khusus untuk membantu tim HR, GA, dan Keuangan mengelola perjalanan dinas serta event kantor dengan kendali penuh, transparansi langsung, dan alur serba otomatis.
                        </p>
                    </div>

                    <!-- 4 Feature Cards Grid (2x2) -->
                    <div class="mt-10 sm:mt-14 grid grid-cols-1 gap-6 sm:gap-8 lg:grid-cols-2">
                        <!-- Card 1: Transparent reporting -->
                        <div class="group relative flex flex-col justify-between overflow-hidden rounded-2xl sm:rounded-[28px] border border-slate-200/80 bg-white p-6 sm:p-9 shadow-[0_10px_35px_-15px_rgba(7,52,90,0.06)] transition-all duration-300 hover:border-sky-300 hover:shadow-[0_20px_50px_-20px_rgba(0,136,255,0.12)]">
                            <div>
                                <h3 class="text-xl font-bold tracking-tight text-[#07345a] sm:text-2xl">Laporan Pengeluaran Transparan</h3>
                                <p class="mt-2 text-xs sm:text-sm leading-relaxed text-slate-600">
                                    Pantau setiap pengeluaran perjalanan bisnis dalam satu dashboard: rekap transaksi otomatis, kepatuhan anggaran, hingga ekspor data instan.
                                </p>
                            </div>

                            <!-- Reporting Mockup UI -->
                            <div class="mt-6 sm:mt-8 rounded-2xl border border-sky-100/90 bg-gradient-to-br from-[#f8fcff] via-white to-[#f0f8ff] p-4 sm:p-6 shadow-xs">
                                <div class="flex items-center justify-between border-b border-slate-100 pb-3 text-[10px] sm:text-[11px] font-semibold text-slate-400">
                                    <span>REKAP PENGELUARAN KANTOR</span>
                                    <span class="inline-flex items-center gap-1 text-[#0088ff] font-bold">
                                        <TrendingUp class="size-3.5" /> +14.8% dari bulan lalu
                                    </span>
                                </div>
                                <div class="mt-3.5 sm:mt-4 space-y-2.5 sm:space-y-3">
                                    <!-- Stat 1: Total transaction -->
                                    <div class="flex flex-wrap sm:flex-nowrap items-center justify-between gap-2 rounded-xl bg-slate-50/80 p-2.5 sm:p-3 border border-slate-100/80">
                                        <span class="text-xs font-semibold text-slate-600">Total Transaksi</span>
                                        <span class="rounded-lg bg-[#e1f4ff] px-2.5 sm:px-3 py-1 text-[11px] sm:text-xs font-bold text-[#0077df] shadow-2xs">
                                            Rp3.100.000.222
                                        </span>
                                    </div>
                                    <!-- Stat 2: Total booking -->
                                    <div class="flex flex-wrap sm:flex-nowrap items-center justify-between gap-2 rounded-xl bg-slate-50/80 p-2.5 sm:p-3 border border-slate-100/80">
                                        <span class="text-xs font-semibold text-slate-600">Total Perjalanan</span>
                                        <span class="rounded-lg bg-[#e6fbf2] px-2.5 sm:px-3 py-1 text-[11px] sm:text-xs font-bold text-[#059669] shadow-2xs">
                                            776 Perjalanan
                                        </span>
                                    </div>
                                    <!-- Stat 3: Policy compliance -->
                                    <div class="rounded-xl bg-slate-50/80 p-2.5 sm:p-3 border border-slate-100/80">
                                        <div class="flex flex-wrap sm:flex-nowrap items-center justify-between gap-2">
                                            <span class="text-xs font-semibold text-slate-600">Kepatuhan Kebijakan Anggaran</span>
                                            <span class="rounded-lg bg-[#fef7e7] px-2.5 sm:px-3 py-1 text-[11px] sm:text-xs font-bold text-[#d97706] shadow-2xs">
                                                99% Sesuai Anggaran
                                            </span>
                                        </div>
                                        <div class="mt-2.5 h-1.5 w-full overflow-hidden rounded-full bg-slate-200">
                                            <div class="h-full w-[99%] rounded-full bg-emerald-500 transition-all duration-1000"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Card 2: Flexible payment options -->
                        <div class="group relative flex flex-col justify-between overflow-hidden rounded-2xl sm:rounded-[28px] border border-slate-200/80 bg-white p-6 sm:p-9 shadow-[0_10px_35px_-15px_rgba(7,52,90,0.06)] transition-all duration-300 hover:border-sky-300 hover:shadow-[0_20px_50px_-20px_rgba(0,136,255,0.12)]">
                            <div>
                                <h3 class="text-xl font-bold tracking-tight text-[#07345a] sm:text-2xl">Pilihan Pembayaran Fleksibel</h3>
                                <p class="mt-2 text-xs sm:text-sm leading-relaxed text-slate-600">
                                    Pilih metode pembayaran yang paling sesuai untuk perusahaan Anda: sistem invoice tempo (termin pembayaran), kartu kredit korporat, hingga reimbursement otomatis.
                                </p>
                            </div>

                            <!-- Payment Options Mockup UI -->
                            <div class="mt-6 sm:mt-8 rounded-2xl border border-sky-100/90 bg-gradient-to-br from-[#f8fcff] via-white to-[#f0f8ff] p-4 sm:p-6 shadow-xs">
                                <div class="space-y-2.5">
                                    <!-- Item 1: Invoicing (Selected) -->
                                    <div class="flex items-center justify-between gap-2 rounded-xl border-2 border-emerald-400 bg-emerald-50/70 p-2.5 sm:p-3 shadow-xs">
                                        <div class="flex items-center gap-2.5 sm:gap-3 min-w-0">
                                            <div class="flex size-8 sm:size-9 shrink-0 items-center justify-center rounded-lg bg-emerald-500 text-white shadow-xs">
                                                <FileText class="size-4 sm:size-5" />
                                            </div>
                                            <div class="min-w-0">
                                                <span class="block truncate text-xs font-bold text-slate-800">Invoicing Terpusat (Tempo)</span>
                                                <span class="block text-[9px] sm:text-[10px] text-slate-500 truncate">Tempo Net 30 & e-Faktur Pajak resmi</span>
                                            </div>
                                        </div>
                                        <span class="inline-flex shrink-0 items-center gap-1 rounded-full bg-emerald-500 px-2 sm:px-2.5 py-0.5 text-[9px] sm:text-[10px] font-bold text-white shadow-xs">
                                            Dipilih <Check class="size-3" />
                                        </span>
                                    </div>

                                    <!-- Item 2: Corporate credit card -->
                                    <div class="flex items-center justify-between gap-2 rounded-xl border border-slate-200 bg-white p-2.5 sm:p-3 text-slate-600 transition hover:bg-slate-50">
                                        <div class="flex items-center gap-2.5 sm:gap-3 min-w-0">
                                            <div class="flex size-8 sm:size-9 shrink-0 items-center justify-center rounded-lg bg-slate-100 text-slate-500">
                                                <CreditCard class="size-4 sm:size-5" />
                                            </div>
                                            <div class="min-w-0">
                                                <span class="block truncate text-xs font-bold text-slate-700">Kartu Kredit Perusahaan</span>
                                                <span class="block text-[9px] sm:text-[10px] text-slate-400 truncate">Batas anggaran & divisi otomatis</span>
                                            </div>
                                        </div>
                                        <span class="text-[9px] sm:text-[10px] font-semibold text-slate-400 shrink-0">Tersedia</span>
                                    </div>

                                    <!-- Item 3: Personal credit card -->
                                    <div class="flex items-center justify-between gap-2 rounded-xl border border-slate-200 bg-white p-2.5 sm:p-3 text-slate-600 transition hover:bg-slate-50">
                                        <div class="flex items-center gap-2.5 sm:gap-3 min-w-0">
                                            <div class="flex size-8 sm:size-9 shrink-0 items-center justify-center rounded-lg bg-slate-100 text-slate-500">
                                                <UserRound class="size-4 sm:size-5" />
                                            </div>
                                            <div class="min-w-0">
                                                <span class="block truncate text-xs font-bold text-slate-700">Reimbursement & Personal</span>
                                                <span class="block text-[9px] sm:text-[10px] text-slate-400 truncate">Unduh bukti transaksi instan</span>
                                            </div>
                                        </div>
                                        <span class="text-[9px] sm:text-[10px] font-semibold text-slate-400 shrink-0">Tersedia</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Card 3: Travel policy setup -->
                        <div class="group relative flex flex-col justify-between overflow-hidden rounded-2xl sm:rounded-[28px] border border-slate-200/80 bg-white p-6 sm:p-9 shadow-[0_10px_35px_-15px_rgba(7,52,90,0.06)] transition-all duration-300 hover:border-sky-300 hover:shadow-[0_20px_50px_-20px_rgba(0,136,255,0.12)]">
                            <div>
                                <h3 class="text-xl font-bold tracking-tight text-[#07345a] sm:text-2xl">Pengaturan Kebijakan Anggaran Perjalanan</h3>
                                <p class="mt-2 text-xs sm:text-sm leading-relaxed text-slate-600">
                                    Atur batas plafon anggaran dan fasilitas otomatis berdasarkan jabatan, divisi, atau level karyawan untuk mencegah kelebihan biaya tanpa verifikasi manual yang merepotkan.
                                </p>
                            </div>

                            <!-- Policy Simulator Mockup UI -->
                            <div class="mt-6 sm:mt-8 rounded-2xl border border-sky-100/90 bg-gradient-to-br from-[#f8fcff] via-white to-[#f0f8ff] p-4 sm:p-6 shadow-xs">
                                <!-- Route Tag -->
                                <div class="inline-flex items-center gap-1.5 sm:gap-2 rounded-lg bg-sky-50 px-2.5 sm:px-3 py-1 sm:py-1.5 text-[11px] sm:text-xs font-bold text-[#0077df] border border-sky-100">
                                    <Plane class="size-3.5 sm:size-4 text-[#0088ff]" />
                                    <span>Jakarta (CGK) ⇄ Bali (DPS)</span>
                                </div>

                                <div class="mt-3.5 space-y-2.5">
                                    <!-- Option 1: Business Class (Non-compliant) -->
                                    <div class="flex flex-wrap sm:flex-nowrap items-center justify-between gap-2 rounded-xl border border-rose-100 bg-rose-50/40 p-2.5 sm:p-3">
                                        <div>
                                            <span class="block text-[10px] sm:text-[11px] font-medium text-slate-500">Kelas Bisnis (Penerbangan)</span>
                                            <span class="block text-xs font-bold text-slate-700">Rp3.122.000</span>
                                        </div>
                                        <span class="inline-flex items-center gap-1 rounded-lg bg-rose-100 px-2 sm:px-2.5 py-0.5 sm:py-1 text-[9px] sm:text-[10px] font-bold text-rose-700 border border-rose-200">
                                            <AlertTriangle class="size-3 text-rose-600" /> Melebihi Plafon
                                        </span>
                                    </div>

                                    <!-- Option 2: Economy Class (Compliant) -->
                                    <div class="flex flex-wrap sm:flex-nowrap items-center justify-between gap-2 rounded-xl border border-emerald-200 bg-emerald-50/60 p-2.5 sm:p-3 shadow-2xs">
                                        <div>
                                            <span class="block text-[10px] sm:text-[11px] font-medium text-emerald-800">Kelas Ekonomi (Fleksibel)</span>
                                            <span class="block text-xs font-bold text-[#07345a]">Rp2.122.000</span>
                                        </div>
                                        <span class="inline-flex items-center gap-1 rounded-lg bg-emerald-500 px-2 sm:px-2.5 py-0.5 sm:py-1 text-[9px] sm:text-[10px] font-bold text-white shadow-2xs">
                                            <Check class="size-3" /> Sesuai Anggaran
                                        </span>
                                    </div>
                                </div>
                                <p class="mt-3 text-center text-[9px] sm:text-[10px] text-slate-400">
                                    Otomatis mendeteksi kebijakan kelas penerbangan & batas anggaran per divisi
                                </p>
                            </div>
                        </div>

                        <!-- Card 4: Built-in approval system -->
                        <div class="group relative flex flex-col justify-between overflow-hidden rounded-2xl sm:rounded-[28px] border border-slate-200/80 bg-white p-6 sm:p-9 shadow-[0_10px_35px_-15px_rgba(7,52,90,0.06)] transition-all duration-300 hover:border-sky-300 hover:shadow-[0_20px_50px_-20px_rgba(0,136,255,0.12)]">
                            <div>
                                <h3 class="text-xl font-bold tracking-tight text-[#07345a] sm:text-2xl">Sistem Persetujuan (Approval) Otomatis</h3>
                                <p class="mt-2 text-xs sm:text-sm leading-relaxed text-slate-600">
                                    Pangkas birokrasi pengajuan perjalanan dinas. Tentukan hierarki persetujuan berjenjang untuk manajer dan tim finance hanya dengan beberapa klik.
                                </p>
                            </div>

                            <!-- Approval Flow Mockup UI -->
                            <div class="mt-6 sm:mt-8 rounded-2xl border border-sky-100/90 bg-gradient-to-br from-[#f8fcff] via-white to-[#f0f8ff] p-4 sm:p-6 shadow-xs">
                                <div class="relative flex flex-col items-center">
                                    <!-- Step 1: Traveler -->
                                    <div class="flex w-full items-center gap-2.5 sm:gap-3 rounded-xl border border-slate-200 bg-white p-2.5 sm:p-3 shadow-2xs">
                                        <img src="https://images.unsplash.com/photo-1534528741775-53994a69daeb?auto=format&fit=crop&w=120&q=80" alt="Pemohon" class="size-8 sm:size-9 rounded-full object-cover border border-slate-200" />
                                        <div class="flex-1 min-w-0">
                                            <span class="block truncate text-xs font-bold text-[#07345a]">Pemohon (Rizky R. - Produk)</span>
                                            <span class="block text-[9px] sm:text-[10px] text-slate-500 truncate">Pengajuan Trip • Gathering Bali</span>
                                        </div>
                                        <span class="rounded bg-sky-50 px-2 py-0.5 text-[8px] sm:text-[9px] font-bold text-[#0088ff] shrink-0">Diajukan</span>
                                    </div>

                                    <!-- Connecting Line -->
                                    <div class="my-1.5 flex flex-col items-center">
                                        <div class="h-3.5 sm:h-4 w-0.5 border-l-2 border-dashed border-emerald-400"></div>
                                        <span class="rounded-full bg-emerald-50 px-2 py-0.5 text-[7px] sm:text-[8px] font-bold text-emerald-600 border border-emerald-200">
                                            Lolos verifikasi anggaran ✓
                                        </span>
                                        <div class="h-3.5 sm:h-4 w-0.5 border-l-2 border-dashed border-emerald-400"></div>
                                    </div>

                                    <!-- Step 2: Approver -->
                                    <div class="flex w-full items-center justify-between gap-2 rounded-xl border-2 border-emerald-400 bg-emerald-50/70 p-2.5 sm:p-3 shadow-xs">
                                        <div class="flex items-center gap-2.5 sm:gap-3 min-w-0">
                                            <img src="https://images.unsplash.com/photo-1573496359142-b8d87734a5a2?auto=format&fit=crop&w=120&q=80" alt="Penyetujui" class="size-8 sm:size-9 rounded-full object-cover border border-emerald-300 shrink-0" />
                                            <div class="min-w-0">
                                                <span class="block truncate text-xs font-bold text-emerald-950">Penyetujui (Sarah A. - Finance)</span>
                                                <span class="block text-[9px] sm:text-[10px] text-emerald-700 truncate">Pengajuan disetujui & rilis</span>
                                            </div>
                                        </div>
                                        <span class="inline-flex items-center gap-1 rounded-full bg-emerald-500 px-2 sm:px-2.5 py-0.5 sm:py-1 text-[9px] sm:text-[10px] font-bold text-white shadow-xs shrink-0">
                                            Disetujui
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Bottom CTA Banner -->
                    <div class="mt-12 sm:mt-16 text-center">
                        <h3 class="text-xl font-bold tracking-tight text-[#07345a] sm:text-3xl">
                            Siap Tingkatkan Efisiensi Perjalanan Bisnis Perusahaan Anda?
                        </h3>
                        <div class="mt-5 sm:mt-6 flex flex-wrap items-center justify-center gap-4">
                            <button type="button" class="corp-button w-full sm:w-auto justify-center !bg-[#0088ff] !px-8 !py-4 !text-sm shadow-xl shadow-blue-500/25 transition-transform duration-200 hover:scale-[1.03] hover:!bg-[#0074d9]" @click="openConsultation">
                                Jadwalkan Konsultasi / Demo Gratis
                                <ArrowRight class="size-4" />
                            </button>
                        </div>
                    </div>
                </div>
            </section>

            <section id="benefits" class="corp-container py-14 sm:py-16 lg:py-24">
                <div>
                    <p class="corp-eyebrow">SATU PERJALANAN, BANYAK MANFAAT</p>
                    <h2 class="corp-heading mt-3">
                        Dirancang untuk Setiap
                        <br />
                        Peran di Tim Anda.
                    </h2>
                </div>
                <div class="mt-8 sm:mt-10 grid gap-6 md:grid-cols-3">
                    <button v-for="(role, index) in roles" :key="role.title" type="button" class="role-card text-left" :class="{ 'is-flipped': isFlipped(index) }" :aria-label="`${role.title}: ${isFlipped(index) ? 'tutup manfaat' : 'lihat manfaat'}`" :aria-expanded="isFlipped(index)" :aria-controls="`role-benefits-${index}`" @pointerenter="hoverRole($event, index)" @pointerleave="hoveredRole = null" @click="pinnedRole = pinnedRole === index ? null : index; hoveredRole = null" @keydown.esc="pinnedRole = null; hoveredRole = null">
                        <span class="role-inner">
                            <span class="role-front" :aria-hidden="isFlipped(index)">
                                <span class="role-photo" :style="{ backgroundPosition: `${role.position} top`, backgroundImage: 'url(/Assets/Images/corporate-roles.png)' }"></span>
                                <span class="flex items-center justify-between gap-3 bg-white px-5 sm:px-6 py-4 sm:py-5">
                                    <span>
                                        <span class="block text-[10px] font-semibold tracking-[0.12em] text-[#0088ff] uppercase">{{ role.english }}</span>
                                        <span class="mt-1 block text-sm sm:text-base font-bold text-[#07345a]">{{ role.title }}</span>
                                    </span>
                                    <Info class="size-5 shrink-0 text-[#0088ff]" />
                                </span>
                            </span>
                            <span :id="`role-benefits-${index}`" class="role-back" :aria-hidden="!isFlipped(index)">
                                <span class="text-xs font-semibold tracking-widest text-[#0088ff] uppercase">{{ role.english }}</span>
                                <span class="mt-2.5 sm:mt-3 block text-2xl sm:text-3xl leading-tight font-bold tracking-tight text-[#07345a]">{{ role.subtitle }}</span>
                                <span class="mt-4 sm:mt-6 flex flex-col gap-3 sm:gap-4">
                                    <span v-for="point in role.points" :key="point" class="flex gap-2.5 sm:gap-3 text-xs sm:text-sm leading-5 sm:leading-6 text-slate-600">
                                        <Check class="mt-0.5 sm:mt-1 size-4 shrink-0 text-[#0088ff]" />
                                        {{ point }}
                                    </span>
                                </span>
                                <span class="mt-auto block border-t border-sky-100 pt-4 sm:pt-5">
                                    <span class="block text-xs sm:text-sm leading-5 sm:leading-6 text-slate-500">{{ role.summary }}</span>
                                    <span class="mt-2.5 sm:mt-3 block text-xs font-bold text-[#07345a]">{{ role.label }}</span>
                                </span>
                                <span class="mt-3 sm:mt-4 flex items-center gap-2 text-[10px] text-slate-400">
                                    <RotateCcw class="size-3" />
                                    Ketuk untuk melihat ringkasan
                                </span>
                            </span>
                        </span>
                    </button>
                </div>
                <div class="mt-8 sm:mt-10 text-center">
                    <p class="text-base sm:text-lg font-semibold text-[#07345a]">Mari wujudkan agenda perjalanan tim yang berkesan bersama kami.</p>
                    <button class="corp-button mt-4 sm:mt-5" @click="openConsultation">
                        Konsultasikan Perjalanan Sekarang
                        <ArrowRight class="size-4" />
                    </button>
                </div>
            </section>

            <!-- Trusted Trip Vendors Section (Marquee to Left) -->
            <section class="border-y border-[#d5ecfb] bg-[#eaf5fc] py-10 sm:py-16 overflow-hidden" aria-label="Vendor trip terpercaya">
                <div class="mx-auto max-w-[1600px] px-5 sm:px-8">
                    <h3 class="text-center text-base sm:text-xl lg:text-2xl font-extrabold tracking-tight text-[#009cf0]">
                        Vendor trip terpercaya
                    </h3>
                    <div class="vendor-logo-marquee mt-6 sm:mt-10 overflow-hidden" tabindex="0" aria-label="Daftar logo vendor trip terpercaya. Arahkan kursor atau fokuskan untuk menjeda animasi.">
                        <div class="vendor-logo-track">
                            <div v-for="copy in 3" :key="copy" class="vendor-logo-group" :aria-hidden="copy > 1 ? true : undefined">
                                <div
                                    v-for="vendor in trustedTripVendors"
                                    :key="vendor.name + '-' + copy"
                                    class="group/vendor flex h-16 sm:h-24 w-36 sm:w-56 shrink-0 items-center justify-center p-1.5 sm:p-2 cursor-pointer transition-transform duration-300 hover:scale-105 sm:hover:scale-110"
                                    :title="vendor.name"
                                >
                                    <img
                                        :src="vendor.src"
                                        :alt="copy === 1 ? vendor.name : ''"
                                        class="h-10 sm:h-18 max-h-12 sm:max-h-20 w-auto max-w-[130px] sm:max-w-[220px] object-contain filter grayscale opacity-65 transition-all duration-300 group-hover/vendor:grayscale-0 group-hover/vendor:opacity-100"
                                        loading="lazy"
                                    />
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
            <section id="faq" class="corp-container grid gap-8 sm:gap-10 py-14 sm:py-16 lg:grid-cols-[0.75fr_1.25fr] lg:gap-20 lg:py-24">
                <div>
                    <p class="corp-eyebrow">PERTANYAAN UMUM (FAQ)</p>
                    <h2 class="corp-heading mt-3">
                        Kenali Lebih Dekat,
                        <br />
                        Rencanakan Lebih Yakin.
                    </h2>
                    <p class="corp-description mt-4">Informasi lengkap seputar pemesanan, layanan, dan kerjasama perjalanan bisnis perusahaan Anda.</p>
                    <Link :href="route('help.index')" class="corp-text-link mt-6">
                        Kunjungi Pusat Bantuan
                        <ArrowUpRight class="size-4" />
                    </Link>
                </div>
                <div>
                    <div v-for="(faq, index) in faqs" :key="faq.question" class="border-b border-slate-200 first:border-t">
                        <h3>
                            <button :id="`faq-question-${index}`" class="flex w-full items-center justify-between gap-4 py-4 sm:py-5 text-left text-xs sm:text-sm font-semibold text-[#07345a]" :aria-expanded="openFaq === index" :aria-controls="`faq-answer-${index}`" @click="openFaq = openFaq === index ? null : index">
                                <span>{{ faq.question }}</span>
                                <ChevronDown class="size-4 shrink-0 text-[#0088ff] transition-transform duration-200" :class="{ 'rotate-180': openFaq === index }" />
                            </button>
                        </h3>
                        <div v-show="openFaq === index" :id="`faq-answer-${index}`" role="region" :aria-labelledby="`faq-question-${index}`" class="pb-4 sm:pb-5 pr-4 sm:pr-6 text-xs sm:text-sm leading-relaxed text-slate-500">{{ faq.answer }}</div>
                    </div>
                </div>
            </section>
        </main>
        <MainFooter />
        <dialog ref="consultationDialog" aria-labelledby="consultation-heading" class="consultation-dialog fixed m-auto max-h-[90dvh] w-[calc(100%-1.5rem)] sm:w-[calc(100%-2rem)] max-w-lg overflow-y-auto rounded-2xl sm:rounded-3xl bg-white p-0 text-slate-800 shadow-2xl backdrop:bg-[#032454]/60" @click.self="consultationDialog.close()">
            <div class="p-5 sm:p-8">
                <div class="flex items-start justify-between gap-4">
                    <div>
                        <p class="corp-eyebrow">KONSULTASI PERJALANAN TIM</p>
                        <h2 id="consultation-heading" class="mt-2 text-xl sm:text-2xl font-bold tracking-tight text-[#07345a]">Ceritakan Rencana Perjalanan Anda.</h2>
                    </div>
                    <button aria-label="Tutup konsultasi" class="rounded-full bg-slate-100 p-2 hover:bg-slate-200" @click="consultationDialog.close()">
                        <X class="size-4" />
                    </button>
                </div>
                <p class="mt-2.5 sm:mt-3 text-xs sm:text-sm leading-5 sm:leading-6 text-slate-500">Isi brief singkat ini agar tim konsultan TapakLokal dapat menyiapkan rekomendasi destinasi, itinerary, dan estimasi anggaran terbaik.</p>
                <form class="mt-5 sm:mt-6 grid gap-3.5 sm:gap-4" @submit.prevent="prepareEmail">
                    <label class="corp-label">
                        Nama Perusahaan
                        <input v-model="brief.company" required maxlength="150" autocomplete="organization" class="corp-input" placeholder="Contoh: PT Nusantara Maju" />
                    </label>
                    <label class="corp-label">
                        Nama PIC (Penanggung Jawab)
                        <input v-model="brief.name" required maxlength="100" autocomplete="name" class="corp-input" placeholder="Nama lengkap Anda" />
                    </label>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5 sm:gap-4">
                        <label class="corp-label">
                            Destinasi Tujuan
                            <input v-model="brief.destination" maxlength="100" class="corp-input" placeholder="Contoh: Yogyakarta / Labuan Bajo" />
                        </label>
                        <label class="corp-label">
                            Jumlah Peserta
                            <input v-model="brief.participants" required type="number" min="1" max="10000" class="corp-input" placeholder="Contoh: 30" />
                        </label>
                    </div>
                    <label class="corp-label">
                        Tanggal & Kebutuhan Khusus
                        <textarea v-model="brief.notes" required maxlength="1500" rows="3" class="corp-input resize-y" placeholder="Rencana tanggal pelaksanaan, perkiraan anggaran, agenda outing, gala dinner, atau kebutuhan khusus lainnya..."></textarea>
                    </label>
                    <button type="submit" class="corp-button w-full justify-center">
                        Buka Draf Email Konsultasi
                        <ArrowUpRight class="size-4" />
                    </button>
                    <p class="text-xs leading-5 text-slate-500">
                        Brief belum dikirim atau disimpan secara publik. Periksa dan kirim melalui aplikasi email Anda ke
                        <a href="mailto:support@tapaklokal.com" class="text-[#0077df] underline font-semibold">support@tapaklokal.com</a>
                        .
                    </p>
                </form>
            </div>
        </dialog>
    </div>
</template>

<style scoped>
@reference '../../css/app.css';
.corp-container { @apply mx-auto w-full max-w-[1180px] px-5 sm:px-8 xl:px-0; }
.corp-eyebrow { @apply text-[11px] font-bold tracking-[0.16em] text-[#0088ff]; }
.corp-heading { @apply text-2xl leading-[1.2] font-bold tracking-[-0.035em] text-[#07345a] sm:text-[38px]; }
.corp-description { @apply max-w-xl text-xs leading-6 text-slate-500 sm:text-base sm:leading-7; }
.corp-button { @apply inline-flex cursor-pointer items-center gap-3 rounded-full bg-[#0088ff] px-6 py-3.5 text-sm font-semibold text-white transition hover:bg-[#0071d6] focus-visible:outline-2 focus-visible:outline-offset-4 focus-visible:outline-blue-500; }
.corp-text-link { @apply inline-flex items-center gap-2 text-sm font-semibold text-[#0077df] hover:text-[#07345a]; }
.corp-label { @apply flex flex-col gap-1.5 text-xs font-semibold text-slate-700; }
.corp-input { @apply w-full rounded-lg border border-slate-200 bg-white px-3 py-2.5 text-base sm:text-sm font-normal outline-none focus:border-blue-400 focus:ring-2 focus:ring-blue-100; }
.corporate-page section[id] { scroll-margin-top: 96px; }
.corporate-hero { min-height: calc(100dvh - 68px); display: flex; flex-direction: column; justify-content: space-between; background: linear-gradient(180deg, #fff 10%, #f5fcff 32%, #c4e9fb 100%); }
.hero-handwritten { position: relative; display: inline-block; color: #009cf0; font-family: 'Segoe Print', 'Bradley Hand', cursive; font-size: 1.25em; font-weight: 700; font-style: italic; line-height: 1.3; letter-spacing: -.065em; }
.hero-handwritten::after { content: ''; position: absolute; left: 0; right: -3%; bottom: 0; height: 8px; border-top: 4px solid #009cf0; border-radius: 50%; transform: rotate(-3deg); }
.hero-composition { position: relative; width: min(100% - 40px, 1000px); height: 484px; margin: 20px auto 0; zoom: .8; }
.hero-devices { position: absolute; z-index: 2; left: 20%; top: 54px; width: 63%; height: auto; }
.hero-float { position: absolute; z-index: 3; width: 230px; padding: 13px 10px 10px; border: 1px solid #f3f7fa; border-radius: 15px; background: #fff; box-shadow: 0 5px 22px #1b567817; }
.hero-card-title { text-align: center; font-size: 12px; font-weight: 650; line-height: 1.3; letter-spacing: -.025em; color: #30383d; }
.hero-float-brief { left: 6%; top: 0; }
.hero-float-agenda { right: 1%; top: 2px; }
.hero-float-budget { left: 0; top: 185px; }
.hero-float-ticket { left: 83.5%; top: 215px; width: 220px; right: auto; }
.hero-donut { display: grid; width: 91px; height: 91px; place-items: center; border-radius: 50%; background: conic-gradient(#2566a9 0 28%, #2bc3e8 28% 100%); transform: rotate(-15deg); }
.hero-donut > span { display: flex; width: 63px; height: 63px; flex-direction: column; align-items: center; justify-content: center; border-radius: 50%; background: #f8fcff; transform: rotate(15deg); }
.corporate-trust-strip { display: flex; align-items: center; gap: 32px; width: 100%; max-width: 1600px; margin: 0 auto; padding: 20px 24px 30px; color: #5c7b8c; }
.trust-badge { display: flex; flex-shrink: 0; align-items: center; gap: 3px; text-align: center; font-size: 13px; line-height: 1.15; font-weight: 500; }
.trust-laurel { height: 48px; width: 25px; opacity: .8; }
.corporate-logo-marquee { min-width: 0; flex: 1; overflow: hidden; mask-image: linear-gradient(to right, transparent, black 4%, black 96%, transparent); }
.corporate-logo-marquee:focus-visible { outline: 2px solid #0088ff; outline-offset: 4px; }
.corporate-logo-track { display: flex; width: max-content; animation: corporate-logos-left 28s linear infinite; }
.corporate-logo-group { display: flex; flex-shrink: 0; align-items: center; gap: 60px; padding-right: 60px; }
.corporate-company-logo { display: block; width: 150px; height: 45px; object-fit: contain; filter: grayscale(1); opacity: .65; }
.corporate-logo-marquee:hover .corporate-logo-track, .corporate-logo-marquee:focus-within .corporate-logo-track { animation-play-state: paused; }
@keyframes corporate-logos-left { to { transform: translateX(-33.333333%); } }
@media (max-width: 767px) {
    .corporate-trust-strip { flex-direction: column; gap: 12px; padding: 16px 16px 20px; text-align: center; }
    .trust-badge { justify-content: center; font-size: 11px; }
    .trust-laurel { height: 32px; width: 16px; }
    .corporate-logo-marquee { width: 100%; }
    .corporate-logo-group { gap: 24px; padding-right: 24px; }
    .corporate-company-logo { width: 95px; height: 30px; }
}
@media (prefers-reduced-motion: reduce) { .corporate-logo-track { animation: none; } .corporate-logo-group[aria-hidden="true"] { display: none; } .corporate-logo-marquee { overflow-x: auto; } }

/* Vendor Partners Marquee */
.vendor-logo-marquee {
    min-width: 0;
    width: 100%;
    overflow: hidden;
    mask-image: linear-gradient(to right, transparent, black 6%, black 94%, transparent);
}
.vendor-logo-marquee:focus-visible {
    outline: 2px solid #0088ff;
    outline-offset: 4px;
}
.vendor-logo-track {
    display: flex;
    width: max-content;
    animation: vendor-logos-left 28s linear infinite;
}
.vendor-logo-group {
    display: flex;
    flex-shrink: 0;
    align-items: center;
    gap: 64px;
    padding-right: 64px;
}
.vendor-logo-marquee:hover .vendor-logo-track,
.vendor-logo-marquee:focus-within .vendor-logo-track {
    animation-play-state: paused;
}
@keyframes vendor-logos-left {
    to {
        transform: translateX(-33.333333%);
    }
}
@media (max-width: 767px) {
    .vendor-logo-group {
        gap: 24px;
        padding-right: 24px;
    }
}
@media (prefers-reduced-motion: reduce) {
    .vendor-logo-track {
        animation: none;
    }
    .vendor-logo-group[aria-hidden="true"] {
        display: none;
    }
    .vendor-logo-marquee {
        overflow-x: auto;
    }
}
@media (min-width: 768px) and (max-width: 1023px) { .hero-composition { width: 720px; height: 406px; margin-top: 30px; zoom: .85; } .hero-float { width: 184px; padding: 10px 8px 8px; } .hero-card-title { font-size: 10px; } .hero-float-budget { top: 145px; left: 0; } .hero-float-brief { left: 1%; } .hero-float-agenda { right: 0; } .hero-float-ticket { top: 155px; left: 83%; width: 175px; right: auto; } .hero-devices { top: 65px; left: 18%; width: 67%; } }
@media (max-width: 767px) {
    .corporate-hero { min-height: auto; }
    .hero-title { font-size: clamp(23px, 6vw, 30px); }
    .hero-composition { zoom: 1; display: grid; grid-template-columns: 1fr 1fr; gap: 10px; width: calc(100% - 24px); height: auto; margin-top: 18px; padding-bottom: 14px; }
    .hero-devices { position: relative; grid-column: 1 / -1; left: auto; top: auto; width: 100%; margin-bottom: 0; }
    .hero-float { position: relative; inset: auto; width: 100%; padding: 10px 8px 8px; border-radius: 12px; }
    .hero-float-brief, .hero-float-agenda, .hero-float-budget, .hero-float-ticket { left: auto; right: auto; top: auto; width: 100%; }
    .hero-float-brief { align-self: stretch; }
    .hero-card-title { font-size: 10px; }
    .hero-donut { width: 64px; height: 64px; }
    .hero-donut > span { width: 44px; height: 44px; }
    .hero-donut strong { font-size: 13px; line-height: 1.1; }
}

.corporate-page button { cursor: pointer; }
.corporate-page :is(a, button):focus-visible { outline: 3px solid #0088ff; outline-offset: 4px; }
.role-card { display: block; width: 100%; min-height: 510px; perspective: 1400px; border-radius: 24px; }
.role-inner { position: relative; display: grid; min-height: 510px; height: 100%; width: 100%; transform-style: preserve-3d; transition: transform 650ms cubic-bezier(.22,.7,.2,1); }
.is-flipped .role-inner { transform: rotateY(180deg); }
.role-front, .role-back { position: relative; grid-area: 1 / 1; overflow: hidden; border-radius: 24px; backface-visibility: hidden; -webkit-backface-visibility: hidden; box-shadow: 0 6px 28px #07345a0d; border: 1px solid #edf1f5; }
.role-front { display: flex; flex-direction: column; background: #edf8ff; }
.role-photo { display: block; flex: 1; background-size: 300% auto; background-repeat: no-repeat; }
.role-back { display: flex; flex-direction: column; transform: rotateY(180deg); background: white; padding: 28px; }
@media (min-width: 768px) and (max-width: 1023px) { .role-inner { min-height: 570px; } .role-back { padding: 20px; } .role-photo { background-size: auto 100%; } }
@media (max-width: 767px) {
    .role-card { max-width: 420px; min-height: 480px; margin-inline: auto; }
    .role-inner { min-height: 480px; }
    .role-back { padding: 22px 18px; }
}
@media (prefers-reduced-motion: reduce) { .role-inner { transition: none; } }

/* Bento Grid Micro-Animations */
.bento-dot-bg {
    background-image: radial-gradient(#bae6fd 1.3px, transparent 1.3px);
    background-size: 16px 16px;
}

@keyframes typing-bounce {
    0%, 80%, 100% { transform: translateY(0); opacity: 0.5; }
    40% { transform: translateY(-4px); opacity: 1; }
}
.typing-dot {
    animation: typing-bounce 1.4s infinite ease-in-out both;
}
.typing-dot:nth-child(1) { animation-delay: -0.32s; }
.typing-dot:nth-child(2) { animation-delay: -0.16s; }
.typing-dot:nth-child(3) { animation-delay: 0s; }

@keyframes radar-pulse-wave {
    0% { transform: scale(0.6); opacity: 0.7; }
    100% { transform: scale(2.6); opacity: 0; }
}
.radar-pulse-ring {
    position: absolute;
    border-radius: 50%;
    border: 1.5px solid rgba(0, 136, 255, 0.45);
    animation: radar-pulse-wave 3s cubic-bezier(0.22, 1, 0.36, 1) infinite;
    pointer-events: none;
}

@keyframes dash-flow {
    from { stroke-dashoffset: 32; }
    to { stroke-dashoffset: 0; }
}
.flow-line-animated {
    stroke-dasharray: 6 6;
    animation: dash-flow 1.8s linear infinite;
}

@keyframes particle-travel {
    0% { transform: translateX(0); opacity: 0; }
    20% { opacity: 1; }
    80% { opacity: 1; }
    100% { transform: translateX(85px); opacity: 0; }
}
.data-particle {
    animation: particle-travel 2s cubic-bezier(0.4, 0, 0.2, 1) infinite;
}
</style>
