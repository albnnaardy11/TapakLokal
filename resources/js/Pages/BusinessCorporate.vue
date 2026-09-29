<script setup>
import { computed, ref } from 'vue';
import { Head, Link } from '@inertiajs/vue3';
import { route } from 'ziggy-js';
import { Menu, UserRound, Globe, ArrowRight, ArrowUpRight, CalendarDays, Check, ChevronDown, Compass, FileText, Info, MapPin, MessageCircle, RotateCcw, ShoppingBag, Users, Wallet, X } from 'lucide-vue-next';
import MainFooter from '../Components/Shared/MainFooter.vue';

const mobileNavigationOpen = ref(false);
const corporateLinks = [
    { href: '#products', label: 'Layanan kami' },
    { href: '#features', label: 'Kemudahan' },
    { href: '#benefits', label: 'Manfaat' },
    { href: '#stories', label: 'Inspirasi' },
    { href: '#faq', label: 'FAQ' },
];
// Existing partner assets are initial logo choices; replace with corporate client logos before publishing.
const corporateLogos = [
    { name: 'Millennium Hotels and Resorts', src: '/Assets/Images/partners/partner-1.svg' },
    { name: 'ALL Accor Live Limitless', src: '/Assets/Images/partners/partner-2.svg' },
    { name: 'Archipelago', src: '/Assets/Images/partners/partner-3.svg' },
    { name: 'IHG Hotels and Resorts', src: '/Assets/Images/partners/partner-4.svg' },
    { name: 'Ascott The Residence', src: '/Assets/Images/partners/partner-5.svg' },
];
const activeProduct = ref(0);
const activeFeature = ref(0);
const activeStory = ref(0);
const hoveredRole = ref(null);
const pinnedRole = ref(null);
const openFaq = ref(0);
const consultationDialog = ref(null);
const brief = ref({ company: '', name: '', destination: '', participants: '', notes: '' });
const photo = (id, width = 1000) => `https://images.unsplash.com/${id}?auto=format&fit=crop&w=${width}&q=85`;
const landscape = photo('photo-1537996194471-e657df975ab4', 1400);
const products = [
    { title: 'Private & company trip', icon: Compass, eyebrow: 'PERJALANAN YANG PUNYA CERITA', heading: 'Satu tim. Banyak cerita baru.', description: 'Bawa tim keluar dari rutinitas. Jelajahi destinasi Indonesia dengan private trip yang bisa disesuaikan dengan agenda perusahaan.', image: landscape, alt: 'Pura dan lanskap hijau Bali', points: ['Pilihan destinasi dan durasi perjalanan', 'Transportasi, pemandu, dan akomodasi sesuai paket', 'Agenda outing, gathering, hingga team building'], cta: 'Jelajahi private trip', routeName: 'trips.category', params: { type: 'private-trip' } },
    { title: 'Pengalaman lokal', icon: MapPin, eyebrow: 'LEBIH DEKAT DENGAN INDONESIA', heading: 'Pengalaman baru, koneksi lebih dekat.', description: 'Temukan sisi lain sebuah destinasi bersama pemandu lokal. Dari alam terbuka hingga budaya setempat, pilih kegiatan yang relevan untuk tim Anda.', image: photo('photo-1544644181-1484b3fdfc62'), alt: 'Pemandangan destinasi wisata Indonesia', points: ['Aktivitas alam dan budaya dalam pilihan trip', 'Pengalaman bersama mitra di daerah tujuan', 'Pilihan perjalanan untuk kelompok kecil maupun besar'], cta: 'Temukan pengalaman', routeName: 'trips.category', params: { type: 'open-trip' } },
    { title: 'Oleh-oleh & bingkisan', icon: ShoppingBag, eyebrow: 'KENANGAN YANG BISA DIBAWA PULANG', heading: 'Sentuhan lokal untuk setiap apresiasi.', description: 'Lengkapi perjalanan tim dengan oleh-oleh khas daerah. Temukan produk lokal untuk dibawa pulang atau dibagikan kepada rekan kerja.', image: photo('photo-1555396273-367ea4eb4db5'), alt: 'Suasana kuliner lokal', points: ['Pilihan kuliner dan produk khas daerah', 'Dukung pelaku usaha lokal lewat setiap pembelian', 'Diskusikan kebutuhan bingkisan dalam konsultasi'], cta: 'Diskusikan bingkisan', consultation: true },
];
const selectedProduct = computed(() => products[activeProduct.value]);
const features = [
    { icon: Compass, title: 'Rencana perjalanan, sesuai kebutuhan', text: 'Mulai dari tujuan, tanggal, dan jumlah peserta. Susun kebutuhan tim sebagai dasar memilih paket dan berdiskusi dengan tim TapakLokal.', label: 'Rencana perjalanan', rows: [['Destinasi', 'Yogyakarta'], ['Durasi', '3 hari, 2 malam'], ['Peserta', '24 orang'], ['Agenda', 'Team gathering']], note: 'Contoh kebutuhan perjalanan' },
    { icon: Users, title: 'Koordinasi tim yang lebih terarah', text: 'Tetapkan PIC dan rangkum kebutuhan peserta sebelum pemesanan, agar detail penting bisa dibahas dalam satu konsultasi.', label: 'Kebutuhan peserta', rows: [['PIC perusahaan', 'Tim People & Culture'], ['Titik kumpul', 'Kantor perusahaan'], ['Kamar', 'Sesuai kebutuhan'], ['Konsumsi', 'Catat kebutuhan khusus']], note: 'Contoh daftar koordinasi' },
    { icon: Wallet, title: 'Rincian biaya untuk bahan persetujuan', text: 'Diskusikan anggaran serta komponen paket sejak awal. Gunakan rincian penawaran untuk proses persetujuan internal perusahaan Anda.', label: 'Komponen penawaran', rows: [['Transportasi', 'Sesuai rute'], ['Akomodasi', 'Sesuai pilihan paket'], ['Aktivitas', 'Sesuai agenda'], ['Tambahan', 'Dikonfirmasi terpisah']], note: 'Ilustrasi rincian, bukan penawaran harga' },
    { icon: MessageCircle, title: 'Ada teman untuk berdiskusi', text: 'Sampaikan pertanyaan tentang itinerary, fasilitas, dan kebutuhan khusus melalui pusat bantuan sebelum menentukan perjalanan.', label: 'Catatan konsultasi', rows: [['Tujuan', 'Outing tahunan'], ['Prioritas', 'Aktivitas bersama'], ['Kebutuhan khusus', 'Menu vegetarian'], ['Langkah berikutnya', 'Diskusikan pilihan trip']], note: 'Contoh brief konsultasi' },
];
const roles = [
    { title: 'Untuk PIC perjalanan', english: 'For bookers', subtitle: 'Lebih terencana, lebih tenang.', position: '0%', points: ['Temukan paket sesuai agenda perusahaan', 'Rangkum tanggal dan kebutuhan peserta', 'Diskusikan detail trip sebelum memesan'], summary: 'Dari mengumpulkan kebutuhan hingga menentukan itinerary, mulai dari satu brief yang jelas.', label: 'HR, GA & koordinator tim' },
    { title: 'Untuk peserta', english: 'For travelers', subtitle: 'Fokus pada pengalaman.', position: '50%', points: ['Kenali itinerary dan fasilitas paket', 'Jelajahi destinasi bersama pemandu lokal', 'Sampaikan kebutuhan khusus kepada PIC'], summary: 'Lebih banyak kesempatan mengenal tempat baru sekaligus rekan satu tim.', label: 'Karyawan & peserta perjalanan' },
    { title: 'Untuk tim finance', english: 'For finance', subtitle: 'Anggaran dengan arah yang jelas.', position: '100%', points: ['Diskusikan batas anggaran sejak awal', 'Tinjau komponen biaya dalam penawaran', 'Konfirmasikan ketentuan pembayaran'], summary: 'Jadikan rincian kebutuhan perjalanan sebagai dasar pembahasan biaya dan persetujuan internal.', label: 'Finance & procurement' },
];
const stories = [
    { tag: 'COMPANY GATHERING', title: 'Jeda dari rutinitas. Ruang untuk lebih dekat.', text: 'Bayangkan agenda tim di Yogyakarta: menikmati budaya, berbagi meja makan, dan pulang dengan cerita yang sama. Diskusikan perpaduan kegiatan yang pas untuk tim Anda.', place: 'Yogyakarta', duration: 'Inspirasi 3 hari, 2 malam', image: photo('photo-1596402184320-417e7178b2cd') },
    { tag: 'TEAM RETREAT', title: 'Ide segar sering datang dari tempat baru.', text: 'Bali bisa menjadi latar untuk sesi refleksi tim, aktivitas di alam, dan waktu bersama. Seimbangkan agenda perusahaan dengan ruang untuk beristirahat.', place: 'Bali', duration: 'Inspirasi 3 hari, 2 malam', image: landscape },
    { tag: 'APRESIASI KARYAWAN', title: 'Rayakan pencapaian lewat sebuah perjalanan.', text: 'Berikan waktu untuk menikmati lanskap Indonesia dan pengalaman bersama. Pilih private trip berdasarkan minat peserta dan anggaran perusahaan.', place: 'Destinasi pilihan tim', duration: 'Durasi sesuai kebutuhan', image: photo('photo-1544644181-1484b3fdfc62') },
];
const faqs = [
    { question: 'Apa itu TapakLokal for Corporates?', answer: 'TapakLokal for Corporates membantu perusahaan menjajaki kebutuhan perjalanan tim, private trip, dan pengalaman lokal. Mulai dengan konsultasi untuk membahas destinasi, jumlah peserta, agenda, dan anggaran.' },
    { question: 'Layanan apa saja yang bisa didiskusikan?', answer: 'Anda dapat mendiskusikan private trip, company gathering, aktivitas lokal, dan kebutuhan oleh-oleh. Transportasi, akomodasi, konsumsi, dan pemandu mengikuti fasilitas paket serta penawaran yang disepakati.' },
    { question: 'Apakah ada minimum jumlah peserta?', answer: 'Kapasitas dan ketentuan peserta bergantung pada paket serta mitra penyelenggara. Cantumkan estimasi peserta saat konsultasi agar pilihan perjalanan dapat disesuaikan.' },
    { question: 'Apakah tersedia dashboard dan approval perusahaan?', answer: 'Saat ini halaman ini melayani informasi dan konsultasi kebutuhan corporate. Dashboard khusus perusahaan, approval berjenjang, dan laporan anggaran otomatis belum tersedia. Persetujuan perjalanan tetap mengikuti proses internal perusahaan Anda.' },
    { question: 'Bagaimana pembayaran dan kebutuhan invoice?', answer: 'Metode, jadwal pembayaran, dan kebutuhan dokumen perusahaan perlu dikonfirmasi saat pembahasan penawaran. Sampaikan kebutuhan invoice sejak awal; ketentuan akhir mengikuti paket dan kesepakatan pemesanan.' },
    { question: 'Bagaimana cara memulai?', answer: 'Klik “Konsultasikan perjalanan”, lengkapi brief, lalu buka draf email. Periksa dan kirim email melalui aplikasi email Anda. Anda juga dapat melihat pilihan private trip atau mengunjungi Pusat Bantuan TapakLokal.' },
];
const insights = [
    { title: 'Temukan destinasi untuk agenda tim berikutnya', category: 'INSPIRASI DESTINASI', image: landscape, routeName: 'blog', action: 'Baca inspirasi' },
    { title: 'Kenali pilihan private trip untuk rombongan', category: 'PANDUAN PERJALANAN', image: photo('photo-1544644181-1484b3fdfc62', 700), routeName: 'trips.category', params: { type: 'private-trip' }, action: 'Lihat pilihan trip' },
    { title: 'Siapkan detail sebelum melakukan pemesanan', category: 'PUSAT BANTUAN', image: '/Assets/Images/accessibility/laptop-closeup.jpg', routeName: 'help.index', action: 'Baca panduan' },
];
const emailHref = computed(() => {
    const body = `Halo tim TapakLokal,\n\nSaya ingin berdiskusi tentang perjalanan perusahaan.\nPerusahaan: ${brief.value.company}\nNama PIC: ${brief.value.name}\nDestinasi: ${brief.value.destination || 'Butuh rekomendasi'}\nJumlah peserta: ${brief.value.participants}\nKebutuhan: ${brief.value.notes}\n\nTerima kasih.`;
    return `mailto:support@tapaklokal.com?subject=${encodeURIComponent(`Konsultasi corporate — ${brief.value.company}`)}&body=${encodeURIComponent(body)}`;
});
function openConsultation() { consultationDialog.value?.showModal(); }
function prepareEmail() { window.location.href = emailHref.value; }
function isFlipped(index) { return hoveredRole.value === index || pinnedRole.value === index; }
function hoverRole(event, index) { if (event.pointerType === 'mouse') hoveredRole.value = index; }
function selectProduct(event, index, direction) {
    const next = (index + direction + products.length) % products.length;
    activeProduct.value = next;
    event.currentTarget.parentElement.children[next].focus();
}
</script>

<template>
    <Head title="For Corporates — Perjalanan Tim & Pengalaman Lokal">
        <meta name="description" content="Rencanakan private trip, company gathering, dan pengalaman lokal bersama TapakLokal. Diskusikan destinasi, kebutuhan peserta, dan anggaran perjalanan perusahaan Anda." />
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
                <div class="px-5 pt-5 text-center sm:pt-6 lg:pt-5">
                    <h1 class="hero-title mx-auto max-w-5xl text-[28px] leading-[1.3] font-bold tracking-[-0.035em] text-[#26292c] sm:text-[34px] lg:text-[38px]">
                        Cara lebih <span class="hero-handwritten">mudah</span><br />mengatur perjalanan perusahaan
                    </h1>
                </div>
                <div class="hero-composition" role="img" aria-label="Ilustrasi laptop dan ponsel TapakLokal dengan brief perjalanan, agenda tim, pengalaman lokal, serta perencanaan anggaran.">
                    <img src="/Assets/Images/corporate-device-mockup.png" alt="" width="1536" height="1024" fetchpriority="high" class="hero-devices" />
                    <div class="hero-float hero-float-brief" aria-hidden="true">
                        <p class="hero-card-title">Satu brief,<br />detail perjalanan lebih jelas</p>
                        <div class="mt-3 flex border-b border-slate-100 text-[9px] font-semibold"><span class="flex-1 pb-2 text-slate-400">Kebutuhan tim</span><span class="flex-1 border-b-2 border-[#8fd600] pb-2 text-[#009cf0]">Rencana trip</span></div>
                        <div class="mt-2 grid grid-cols-3 gap-2 rounded-lg bg-[#f5fafd] p-2 text-[9px]"><span class="text-slate-400">Destinasi</span><span class="text-slate-400">Peserta</span><span class="text-slate-400">Durasi</span><strong>Bali</strong><strong>24 orang</strong><strong>3 hari</strong></div>
                    </div>
                    <div class="hero-float hero-float-agenda" aria-hidden="true">
                        <p class="hero-card-title">Agenda yang pas untuk tim</p>
                        <div class="relative mt-3 flex items-center justify-center gap-3 rounded-lg bg-[#f1fbff] py-3"><span class="text-[9px] leading-4 text-slate-500">Aktivitas<br /><strong class="text-[#009cf0]">bersama</strong></span><div class="hero-donut"><span><strong class="block text-xl leading-6 text-[#07345a]">3 hari</strong><span class="text-[8px] text-slate-500">Penuh cerita</span></span></div><span class="text-[9px] leading-4 text-slate-500">Waktu<br /><strong class="text-[#2761a4]">bebas</strong></span></div>
                    </div>
                    <div class="hero-float hero-float-experience" aria-hidden="true">
                        <p class="hero-card-title">Lebih dekat dengan pengalaman lokal</p>
                        <div class="relative mt-3 flex h-28 items-center justify-center overflow-hidden rounded-lg bg-[#ecf9ff]"><div class="absolute size-28 rounded-full border-[18px] border-[#d3f0fc]"></div><div class="relative rotate-[-9deg] rounded-xl bg-[#0872bd] p-3 text-white shadow-lg"><Compass class="size-12" /></div><span class="absolute top-2 right-7 flex size-9 items-center justify-center rounded-full bg-[#29c2ea] text-white"><MapPin class="size-5" /></span><span class="absolute bottom-2 left-7 flex size-8 items-center justify-center rounded-full bg-[#ffb12d] text-white"><Users class="size-4" /></span></div>
                    </div>
                    <div class="hero-float hero-float-budget" aria-hidden="true">
                        <p class="hero-card-title">Rencana sesuai anggaran Anda</p>
                        <div class="relative mt-3 flex h-28 items-center justify-center overflow-hidden rounded-lg bg-[#effaff]"><div class="absolute size-32 rounded-full border-[20px] border-[#dcf5ff]"></div><div class="absolute h-16 w-24 -translate-x-3 -translate-y-1 rotate-[-17deg] rounded-lg bg-[#94d900]"></div><div class="relative h-16 w-24 rotate-[-8deg] rounded-lg bg-[#009ef1] p-3 text-white shadow-lg"><Wallet class="size-7" /><span class="mt-1 block h-1 w-12 rounded bg-[#075a9e]"></span></div><Check class="absolute top-2 right-8 size-5 text-[#83c800]" /></div>
                    </div>
                </div>
                <p class="text-center text-[10px] tracking-wide text-[#4d7e99]">Ilustrasi pengalaman TapakLokal · Sesuaikan perjalanan melalui konsultasi</p>
                <div class="corporate-trust-strip" aria-label="Trusted by 100+ companies">
                    <div class="trust-badge">
                        <svg class="trust-laurel" viewBox="0 0 32 64" fill="currentColor" aria-hidden="true">
                            <path d="M27 60C8 49 6 23 24 5" fill="none" stroke="currentColor" stroke-width="1.5" />
                            <path d="M23 10c-5-1-5-6-2-10 4 3 5 7 2 10ZM17 18c-6-1-8-6-6-11 5 2 8 6 6 11ZM12 28C6 27 3 22 4 17c6 1 9 5 8 11ZM10 39C3 37 0 32 1 27c6 2 10 6 9 12ZM14 50C7 50 2 46 2 40c7 1 11 4 12 10ZM22 59C15 62 9 60 7 54c6-2 12 0 15 5ZM19 18c0-6 4-9 10-9-1 5-5 9-10 9ZM14 28c1-6 6-9 11-8-2 5-6 8-11 8ZM13 40c1-6 5-9 11-8-2 5-6 8-11 8ZM17 50c1-6 5-8 11-7-2 5-6 8-11 7Z" />
                        </svg>
                        <p>Trusted by <strong>100+</strong><br />companies</p>
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
            <section id="products" class="corp-container py-16 lg:py-24">
                <div class="text-center">
                    <p class="corp-eyebrow">LAYANAN KAMI</p>
                    <h2 class="corp-heading mt-3">
                        Ke mana pun agendanya,
                        <br class="sm:hidden" />
                        mulai dari sini.
                    </h2>
                    <p class="corp-description mx-auto mt-4">Pilihan perjalanan dan sentuhan lokal untuk kebutuhan perusahaan Anda.</p>
                </div>
                <div role="tablist" aria-label="Layanan corporate" class="mx-auto mt-8 flex max-w-2xl gap-1 rounded-2xl bg-slate-100 p-1.5 sm:rounded-full">
                    <button v-for="(product, index) in products" :id="`product-tab-${index}`" :key="product.title" role="tab" :tabindex="activeProduct === index ? 0 : -1" :aria-selected="activeProduct === index" aria-controls="product-panel" class="flex flex-1 items-center justify-center gap-2 rounded-xl px-2 py-3 text-xs font-semibold transition sm:rounded-full sm:text-sm" :class="activeProduct === index ? 'bg-white text-[#0077df] shadow-sm' : 'text-slate-500 hover:text-slate-900'" @click="activeProduct = index" @keydown.right.prevent="selectProduct($event, index, 1)" @keydown.left.prevent="selectProduct($event, index, -1)">
                        <component :is="product.icon" class="hidden size-4 sm:block" />
                        {{ product.title }}
                    </button>
                </div>
                <div id="product-panel" role="tabpanel" :aria-labelledby="`product-tab-${activeProduct}`" class="mt-10 grid overflow-hidden rounded-[28px] border border-slate-100 bg-[#f7fafc] md:grid-cols-2">
                    <div class="relative min-h-64 md:min-h-[400px]">
                        <img :src="selectedProduct.image" :alt="selectedProduct.alt" loading="lazy" class="absolute inset-0 size-full object-cover" />
                        <span class="absolute bottom-6 left-6 rounded-full bg-white/95 px-4 py-2 text-xs font-semibold text-[#07345a]">Bersama TapakLokal</span>
                    </div>
                    <div class="flex flex-col items-start justify-center p-7 lg:p-12">
                        <p class="corp-eyebrow !text-[10px]">{{ selectedProduct.eyebrow }}</p>
                        <h3 class="mt-3 text-3xl leading-tight font-bold tracking-tight text-[#07345a]">{{ selectedProduct.heading }}</h3>
                        <p class="mt-4 text-sm leading-7 text-slate-600">{{ selectedProduct.description }}</p>
                        <ul class="mt-5 space-y-3">
                            <li v-for="point in selectedProduct.points" :key="point" class="flex gap-3 text-sm">
                                <Check class="mt-0.5 size-4 shrink-0 text-[#0088ff]" />
                                {{ point }}
                            </li>
                        </ul>
                        <button v-if="selectedProduct.consultation" class="corp-text-link mt-7" @click="openConsultation">
                            {{ selectedProduct.cta }}
                            <ArrowRight class="size-4" />
                        </button>
                        <Link v-else :href="route(selectedProduct.routeName, selectedProduct.params)" class="corp-text-link mt-7">
                            {{ selectedProduct.cta }}
                            <ArrowRight class="size-4" />
                        </Link>
                    </div>
                </div>
            </section>
            <section id="features" class="bg-[#f1f8fc] py-16 lg:py-24">
                <div class="corp-container">
                    <div class="max-w-2xl">
                        <p class="corp-eyebrow">LEBIH MUDAH DI SETIAP LANGKAH</p>
                        <h2 class="corp-heading mt-3">
                            Detailnya terencana.
                            <br />
                            Tim Anda tinggal melangkah.
                        </h2>
                    </div>
                    <div class="mt-10 grid items-center gap-10 lg:grid-cols-2 lg:gap-20">
                        <div>
                            <div v-for="(feature, index) in features" :key="feature.title" class="border-b border-[#d3e3ed]">
                                <h3>
                                    <button :id="`feature-${index}`" class="flex w-full items-center gap-4 py-5 text-left" :aria-expanded="activeFeature === index" :aria-controls="`feature-copy-${index}`" @click="activeFeature = index">
                                        <component :is="feature.icon" class="size-5 shrink-0 text-[#0088ff]" />
                                        <span class="flex-1 text-base font-semibold text-[#07345a]">{{ feature.title }}</span>
                                        <ChevronDown class="size-4 transition-transform" :class="{ 'rotate-180': activeFeature === index }" />
                                    </button>
                                </h3>
                                <p v-show="activeFeature === index" :id="`feature-copy-${index}`" class="pb-6 pl-9 text-sm leading-7 text-slate-600">{{ feature.text }}</p>
                            </div>
                        </div>
                        <div class="relative rounded-[26px] border border-white bg-[#dfedf6] p-5 sm:p-9">
                            <div class="overflow-hidden rounded-2xl bg-white shadow-[0_16px_45px_-20px_#07345a50]">
                                <div class="flex items-center justify-between border-b border-slate-100 px-6 py-4">
                                    <span class="text-sm font-bold text-[#07345a]">
                                        tapak
                                        <span class="text-[#0088ff]">lokal</span>
                                    </span>
                                    <span class="text-[9px] font-semibold tracking-widest text-slate-400">CORPORATE BRIEF</span>
                                </div>
                                <div class="p-6 sm:p-8">
                                    <span class="text-[10px] font-semibold tracking-widest text-[#0088ff]">MULAI DARI RENCANA YANG JELAS</span>
                                    <h3 class="mt-2 text-xl font-bold text-[#07345a]">{{ features[activeFeature].label }}</h3>
                                    <div class="mt-6 divide-y divide-slate-100">
                                        <div v-for="row in features[activeFeature].rows" :key="row[0]" class="flex justify-between gap-4 py-3.5 text-xs">
                                            <span class="text-slate-500">{{ row[0] }}</span>
                                            <span class="text-right font-semibold text-slate-700">{{ row[1] }}</span>
                                        </div>
                                    </div>
                                    <div class="mt-5 flex items-center gap-2 rounded-lg bg-[#eff8ff] p-3 text-[11px] text-[#1674b9]">
                                        <FileText class="size-4 shrink-0" />
                                        {{ features[activeFeature].note }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
            <section id="benefits" class="corp-container py-16 lg:py-24">
                <div class="flex flex-col justify-between gap-4 sm:flex-row sm:items-end">
                    <div>
                        <p class="corp-eyebrow">SATU PERJALANAN, BANYAK MANFAAT</p>
                        <h2 class="corp-heading mt-3">
                            Dirancang untuk setiap
                            <br />
                            peran di tim Anda.
                        </h2>
                    </div>
                    <p class="max-w-60 text-sm leading-6 text-slate-500">
                        Arahkan kursor atau ketuk kartu
                        <br class="hidden sm:block" />
                        untuk melihat manfaatnya.
                        <RotateCcw class="mt-3 size-5 text-[#0088ff]" />
                    </p>
                </div>
                <div class="mt-10 grid gap-6 md:grid-cols-3">
                    <button v-for="(role, index) in roles" :key="role.title" type="button" class="role-card text-left" :class="{ 'is-flipped': isFlipped(index) }" :aria-label="`${role.title}: ${isFlipped(index) ? 'tutup manfaat' : 'lihat manfaat'}`" :aria-expanded="isFlipped(index)" :aria-controls="`role-benefits-${index}`" @pointerenter="hoverRole($event, index)" @pointerleave="hoveredRole = null" @click="pinnedRole = pinnedRole === index ? null : index; hoveredRole = null" @keydown.esc="pinnedRole = null; hoveredRole = null">
                        <span class="role-inner">
                            <span class="role-front" :aria-hidden="isFlipped(index)">
                                <span class="role-photo" :style="{ backgroundPosition: `${role.position} top`, backgroundImage: 'url(/Assets/Images/corporate-roles.png)' }"></span>
                                <span class="flex items-center justify-between gap-3 bg-white px-6 py-5">
                                    <span>
                                        <span class="block text-[10px] font-semibold tracking-[0.12em] text-slate-400 uppercase">{{ role.english }}</span>
                                        <span class="mt-1 block text-base font-bold text-[#07345a]">{{ role.title }}</span>
                                    </span>
                                    <Info class="size-5 shrink-0 text-[#0088ff]" />
                                </span>
                            </span>
                            <span :id="`role-benefits-${index}`" class="role-back" :aria-hidden="!isFlipped(index)">
                                <span class="text-xs font-semibold tracking-widest text-[#0088ff] uppercase">{{ role.english }}</span>
                                <span class="mt-3 block text-3xl leading-tight font-bold tracking-tight text-[#07345a]">{{ role.subtitle }}</span>
                                <span class="mt-6 flex flex-col gap-4">
                                    <span v-for="point in role.points" :key="point" class="flex gap-3 text-sm leading-6 text-slate-600">
                                        <Check class="mt-1 size-4 shrink-0 text-[#0088ff]" />
                                        {{ point }}
                                    </span>
                                </span>
                                <span class="mt-auto block border-t border-sky-100 pt-5">
                                    <span class="block text-sm leading-6 text-slate-500">{{ role.summary }}</span>
                                    <span class="mt-3 block text-xs font-bold text-[#07345a]">{{ role.label }}</span>
                                </span>
                                <span class="mt-4 flex items-center gap-2 text-[10px] text-slate-400">
                                    <RotateCcw class="size-3" />
                                    Ketuk untuk membalik kartu
                                </span>
                            </span>
                        </span>
                    </button>
                </div>
                <div class="mt-10 text-center">
                    <p class="text-lg font-semibold text-[#07345a]">Mari temukan perjalanan yang pas untuk tim Anda.</p>
                    <button class="corp-button mt-5" @click="openConsultation">
                        Saya tertarik
                        <ArrowRight class="size-4" />
                    </button>
                </div>
            </section>
            <section id="stories" class="bg-[#07345a] py-16 text-white lg:py-20">
                <div class="corp-container">
                    <div class="flex flex-col justify-between gap-5 md:flex-row md:items-end">
                        <div>
                            <p class="corp-eyebrow !text-sky-300">CERITA TIM DIMULAI DI SINI</p>
                            <h2 class="corp-heading mt-3 !text-white">
                                Agenda berbeda.
                                <br />
                                Sama-sama berkesan.
                            </h2>
                        </div>
                        <p class="max-w-xs text-sm leading-6 text-blue-100/75">Gambaran perjalanan yang bisa Anda rencanakan bersama tim. Sesuaikan dengan tujuan perusahaan Anda.</p>
                    </div>
                    <div class="mt-10 grid overflow-hidden rounded-2xl bg-[#0d4069] md:grid-cols-[1.05fr_1fr]">
                        <img :src="stories[activeStory].image" :alt="stories[activeStory].place" class="h-64 w-full object-cover md:h-full md:min-h-80" loading="lazy" />
                        <div class="flex flex-col items-start justify-center p-7 sm:p-10">
                            <span class="text-[10px] font-semibold tracking-[0.2em] text-sky-300">{{ stories[activeStory].tag }}</span>
                            <h3 class="mt-4 text-3xl leading-tight font-semibold tracking-tight">{{ stories[activeStory].title }}</h3>
                            <p class="mt-4 text-sm leading-7 text-blue-100/80">{{ stories[activeStory].text }}</p>
                            <div class="mt-6 flex flex-wrap gap-4 text-xs text-blue-100">
                                <span class="flex items-center gap-1.5">
                                    <MapPin class="size-3.5" />
                                    {{ stories[activeStory].place }}
                                </span>
                                <span class="flex items-center gap-1.5">
                                    <CalendarDays class="size-3.5" />
                                    {{ stories[activeStory].duration }}
                                </span>
                            </div>
                            <button class="mt-7 inline-flex items-center gap-2 text-sm font-semibold text-white underline decoration-sky-400 underline-offset-8" @click="openConsultation">
                                Rencanakan perjalanan serupa
                                <ArrowUpRight class="size-4" />
                            </button>
                        </div>
                    </div>
                    <div class="mt-6 flex items-center justify-between gap-4">
                        <span class="text-[11px] text-blue-100/60">Ilustrasi agenda, bukan testimoni pelanggan.</span>
                        <div class="flex gap-2">
                            <button v-for="(story, index) in stories" :key="story.tag" :aria-label="`Lihat inspirasi ${story.tag}`" :aria-pressed="activeStory === index" class="h-9 rounded-full px-3 text-xs transition" :class="activeStory === index ? 'bg-white text-[#07345a]' : 'border border-white/25 text-white hover:bg-white/10'" @click="activeStory = index">0{{ index + 1 }}</button>
                        </div>
                    </div>
                </div>
            </section>
            <div class="corp-container flex flex-wrap items-center justify-center gap-x-10 gap-y-4 border-b border-slate-100 py-8 text-sm font-semibold text-slate-400">
                <span class="text-xs font-normal text-slate-500">Banyak cara mengenal Indonesia</span>
                <span>Alam & petualangan</span>
                <span>Budaya & tradisi</span>
                <span>Kuliner & kriya</span>
            </div>
            <section id="faq" class="corp-container grid gap-10 py-16 lg:grid-cols-[0.75fr_1.25fr] lg:gap-20 lg:py-24">
                <div>
                    <p class="corp-eyebrow">ADA YANG INGIN DITANYAKAN?</p>
                    <h2 class="corp-heading mt-3">
                        Kenali lebih dekat,
                        <br />
                        rencanakan lebih yakin.
                    </h2>
                    <p class="corp-description mt-4">Hal-hal yang perlu diketahui sebelum memulai perjalanan perusahaan.</p>
                    <Link :href="route('help.index')" class="corp-text-link mt-6">
                        Kunjungi pusat bantuan
                        <ArrowUpRight class="size-4" />
                    </Link>
                </div>
                <div>
                    <div v-for="(faq, index) in faqs" :key="faq.question" class="border-b border-slate-200 first:border-t">
                        <h3>
                            <button :id="`faq-question-${index}`" class="flex w-full items-center justify-between gap-5 py-5 text-left text-sm font-semibold text-[#07345a]" :aria-expanded="openFaq === index" :aria-controls="`faq-answer-${index}`" @click="openFaq = openFaq === index ? null : index">
                                {{ faq.question }}
                                <ChevronDown class="size-4 shrink-0 text-[#0088ff] transition-transform" :class="{ 'rotate-180': openFaq === index }" />
                            </button>
                        </h3>
                        <div v-show="openFaq === index" :id="`faq-answer-${index}`" role="region" :aria-labelledby="`faq-question-${index}`" class="pb-5 pr-6 text-sm leading-7 text-slate-500">{{ faq.answer }}</div>
                    </div>
                </div>
            </section>
            <section class="border-t border-slate-100 bg-[#fbfcfd] py-16 lg:py-20">
                <div class="corp-container">
                    <div class="flex flex-wrap items-end justify-between gap-4">
                        <div>
                            <p class="corp-eyebrow">BEKAL SEBELUM BERANGKAT</p>
                            <h2 class="corp-heading mt-3">Inspirasi untuk langkah berikutnya.</h2>
                        </div>
                        <Link :href="route('blog')" class="corp-text-link">
                            Jelajahi blog
                            <ArrowUpRight class="size-4" />
                        </Link>
                    </div>
                    <div class="mt-9 grid gap-7 md:grid-cols-3">
                        <Link v-for="insight in insights" :key="insight.title" :href="route(insight.routeName, insight.params)" class="group">
                            <div class="overflow-hidden rounded-2xl bg-slate-100">
                                <img :src="insight.image" alt="" class="aspect-[16/10] w-full object-cover transition duration-500 motion-safe:group-hover:scale-105" loading="lazy" />
                            </div>
                            <p class="mt-5 text-[10px] font-semibold tracking-[0.13em] text-[#0088ff]">{{ insight.category }}</p>
                            <h3 class="mt-2 text-xl leading-snug font-semibold tracking-tight text-[#07345a] group-hover:text-[#0088ff]">{{ insight.title }}</h3>
                            <span class="mt-4 inline-flex items-center gap-2 text-xs font-semibold text-slate-500">
                                {{ insight.action }}
                                <ArrowRight class="size-3.5" />
                            </span>
                        </Link>
                    </div>
                </div>
            </section>
        </main>
        <MainFooter />
        <dialog ref="consultationDialog" aria-labelledby="consultation-heading" class="consultation-dialog fixed m-auto max-h-[90dvh] w-[calc(100%-2rem)] max-w-lg overflow-y-auto rounded-3xl bg-white p-0 text-slate-800 shadow-2xl backdrop:bg-[#032454]/60" @click.self="consultationDialog.close()">
            <div class="p-6 sm:p-8">
                <div class="flex items-start justify-between gap-4">
                    <div>
                        <p class="corp-eyebrow">MULAI PERJALANAN TIM ANDA</p>
                        <h2 id="consultation-heading" class="mt-2 text-2xl font-bold tracking-tight text-[#07345a]">Ceritakan rencana Anda.</h2>
                    </div>
                    <button aria-label="Tutup konsultasi" class="rounded-full bg-slate-100 p-2 hover:bg-slate-200" @click="consultationDialog.close()">
                        <X class="size-4" />
                    </button>
                </div>
                <p class="mt-3 text-sm leading-6 text-slate-500">Isi brief singkat ini untuk menyiapkan email konsultasi ke tim TapakLokal.</p>
                <form class="mt-6 grid gap-4" @submit.prevent="prepareEmail">
                    <label class="corp-label">
                        Nama perusahaan
                        <input v-model="brief.company" required maxlength="150" autocomplete="organization" class="corp-input" placeholder="Nama perusahaan Anda" />
                    </label>
                    <label class="corp-label">
                        Nama PIC
                        <input v-model="brief.name" required maxlength="100" autocomplete="name" class="corp-input" placeholder="Nama penanggung jawab" />
                    </label>
                    <div class="grid grid-cols-2 gap-4">
                        <label class="corp-label">
                            Destinasi tujuan
                            <input v-model="brief.destination" maxlength="100" class="corp-input" placeholder="Contoh: Yogyakarta" />
                        </label>
                        <label class="corp-label">
                            Jumlah peserta
                            <input v-model="brief.participants" required type="number" min="1" max="10000" class="corp-input" placeholder="Contoh: 24" />
                        </label>
                    </div>
                    <label class="corp-label">
                        Tanggal & kebutuhan lainnya
                        <textarea v-model="brief.notes" required maxlength="1500" rows="3" class="corp-input resize-y" placeholder="Rencana tanggal, anggaran, dan agenda tim..."></textarea>
                    </label>
                    <button type="submit" class="corp-button w-full justify-center">
                        Buka draf email
                        <ArrowUpRight class="size-4" />
                    </button>
                    <p class="text-xs leading-5 text-slate-500">
                        Brief belum dikirim atau disimpan. Periksa dan kirim melalui aplikasi email Anda ke
                        <a href="mailto:support@tapaklokal.com" class="text-[#0077df] underline">support@tapaklokal.com</a>
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
.corp-heading { @apply text-3xl leading-[1.2] font-bold tracking-[-0.035em] text-[#07345a] sm:text-[38px]; }
.corp-description { @apply max-w-xl text-sm leading-7 text-slate-500 sm:text-base; }
.corp-button { @apply inline-flex cursor-pointer items-center gap-3 rounded-full bg-[#0088ff] px-6 py-3.5 text-sm font-semibold text-white transition hover:bg-[#0071d6] focus-visible:outline-2 focus-visible:outline-offset-4 focus-visible:outline-blue-500; }
.corp-text-link { @apply inline-flex items-center gap-2 text-sm font-semibold text-[#0077df] hover:text-[#07345a]; }
.corp-label { @apply flex flex-col gap-1.5 text-xs font-semibold text-slate-700; }
.corp-input { @apply w-full rounded-lg border border-slate-200 bg-white px-3 py-2.5 text-sm font-normal outline-none focus:border-blue-400 focus:ring-2 focus:ring-blue-100; }
.corporate-page section[id] { scroll-margin-top: 96px; }
.corporate-hero { background: linear-gradient(180deg, #fff 12%, #f5fcff 32%, #c4e9fb 100%); }
.hero-handwritten { position: relative; display: inline-block; color: #009cf0; font-family: 'Segoe Print', 'Bradley Hand', cursive; font-size: 1.25em; font-weight: 700; font-style: italic; line-height: 1.3; letter-spacing: -.065em; }
.hero-handwritten::after { content: ''; position: absolute; left: 0; right: -3%; bottom: 0; height: 8px; border-top: 4px solid #009cf0; border-radius: 50%; transform: rotate(-3deg); }
.hero-composition { position: relative; width: min(100% - 40px, 1000px); height: 484px; margin: 38px auto 0; zoom: .76; }
.hero-devices { position: absolute; z-index: 2; left: 20%; top: 54px; width: 63%; height: auto; }
.hero-float { position: absolute; z-index: 3; width: 230px; padding: 13px 10px 10px; border: 1px solid #f3f7fa; border-radius: 15px; background: #fff; box-shadow: 0 5px 22px #1b567817; }
.hero-card-title { text-align: center; font-size: 12px; font-weight: 650; line-height: 1.3; letter-spacing: -.025em; color: #30383d; }
.hero-float-brief { left: 6%; top: 0; }
.hero-float-agenda { right: 1%; top: 2px; }
.hero-float-experience { left: 0; top: 185px; }
.hero-float-budget { right: -3%; top: 250px; }
.hero-donut { display: grid; width: 91px; height: 91px; place-items: center; border-radius: 50%; background: conic-gradient(#2566a9 0 28%, #2bc3e8 28% 100%); transform: rotate(-15deg); }
.hero-donut > span { display: flex; width: 63px; height: 63px; flex-direction: column; align-items: center; justify-content: center; border-radius: 50%; background: #f8fcff; transform: rotate(15deg); }
.corporate-trust-strip { display: flex; align-items: center; gap: 32px; width: 100%; max-width: 1600px; margin: 0 auto; padding: 23px 24px 27px; color: #5c7b8c; }
.trust-badge { display: flex; flex-shrink: 0; align-items: center; gap: 3px; text-align: center; font-size: 13px; line-height: 1.15; font-weight: 500; }
.trust-laurel { height: 48px; width: 25px; opacity: .8; }
.corporate-logo-marquee { min-width: 0; flex: 1; overflow: hidden; mask-image: linear-gradient(to right, transparent, black 4%, black 96%, transparent); }
.corporate-logo-marquee:focus-visible { outline: 2px solid #0088ff; outline-offset: 4px; }
.corporate-logo-track { display: flex; width: max-content; animation: corporate-logos-left 28s linear infinite; }
.corporate-logo-group { display: flex; flex-shrink: 0; align-items: center; gap: 60px; padding-right: 60px; }
.corporate-company-logo { display: block; width: 150px; height: 45px; object-fit: contain; filter: grayscale(1); opacity: .65; }
.corporate-logo-marquee:hover .corporate-logo-track, .corporate-logo-marquee:focus-within .corporate-logo-track { animation-play-state: paused; }
@keyframes corporate-logos-left { to { transform: translateX(-33.333333%); } }
@media (max-width: 767px) { .corporate-trust-strip { gap: 15px; padding: 20px 16px 24px; } .trust-badge { font-size: 10px; } .trust-laurel { height: 38px; width: 18px; } .corporate-logo-group { gap: 30px; padding-right: 30px; } .corporate-company-logo { width: 115px; height: 36px; } }
@media (prefers-reduced-motion: reduce) { .corporate-logo-track { animation: none; } .corporate-logo-group[aria-hidden="true"] { display: none; } .corporate-logo-marquee { overflow-x: auto; } }
@media (min-width: 768px) and (max-width: 1023px) { .hero-composition { width: 720px; height: 406px; margin-top: 30px; zoom: .85; } .hero-float { width: 184px; padding: 10px 8px 8px; } .hero-card-title { font-size: 10px; } .hero-float-experience { top: 145px; } .hero-float-budget { top: 205px; right: 0; } .hero-float-brief { left: 1%; } .hero-float-agenda { right: 0; } .hero-devices { top: 65px; left: 18%; width: 67%; } }
@media (max-width: 767px) { .hero-title { font-size: clamp(25px, 6.4vw, 32px); } .hero-composition { zoom: 1; display: grid; grid-template-columns: 1fr 1fr; gap: 12px; width: calc(100% - 32px); height: auto; margin-top: 24px; padding-bottom: 18px; } .hero-devices { position: relative; grid-column: 1 / -1; left: auto; top: auto; width: 100%; margin-bottom: 0; } .hero-float { position: relative; inset: auto; width: 100%; padding: 12px 8px 8px; } .hero-float-brief { align-self: stretch; } .hero-float-experience, .hero-float-budget { display: block; } .hero-card-title { font-size: 10px; } .hero-donut { width: 70px; height: 70px; } .hero-donut > span { width: 48px; height: 48px; } .hero-donut strong { font-size: 15px; } }

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
@media (max-width: 767px) { .role-card { max-width: 420px; margin-inline: auto; } }
@media (prefers-reduced-motion: reduce) { .role-inner { transition: none; } }
</style>
