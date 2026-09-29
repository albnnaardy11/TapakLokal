<script setup>
import { computed, ref } from 'vue';
import { Head } from '@inertiajs/vue3';
import { route } from 'ziggy-js';
import {
    ArrowRight, ArrowUpRight, CalendarDays, Check, CheckCircle2,
    Clock, Compass, FileText, LayoutDashboard, MapPin,
    Sparkles, Star, Store, Users, Wallet, X
} from 'lucide-vue-next';
import BusinessLanding from '../Components/Shared/BusinessLanding.vue';

const registrationDialog = ref(null);
const selectedTrack = ref('trip');
const brief = ref({ business: '', name: '', city: '', contact: '', notes: '' });
const links = [{ href: '#kemitraan', label: 'Pilihan Kemitraan' }, { href: '#manfaat', label: 'Solusi Bisnis' }, { href: '#cara-bergabung', label: 'Cara Bergabung' }, { href: '#faq', label: 'Tanya Jawab' }];
const tracks = [
    {
        id: 'trip',
        icon: Compass,
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
    { q: 'Apakah mengirim formulir langsung mengaktifkan akun vendor?', a: 'Belum. Formulir menyiapkan email pengajuan minat untuk tim TapakLokal. Anda perlu mengirimkannya melalui aplikasi email. Aktivasi dan akses portal vendor mengikuti hasil peninjauan tim.' },
    { q: 'Bagaimana biaya kerja sama dan pencairan dana?', a: 'Biaya layanan, pembagian hasil, serta jadwal pencairan dibahas bersama tim dan mengikuti ketentuan kerja sama yang disepakati. Pastikan Anda memahami ketentuannya sebelum mengaktifkan penawaran.' },
    { q: 'Saya sudah menjadi vendor. Bagaimana cara masuk?', a: 'Gunakan tombol Masuk pada navigasi untuk membuka portal vendor dengan akun yang telah diberikan kepada Anda.' },
];
function openRegistration(type = selectedTrack.value) {
    selectedTrack.value = type;
    registrationDialog.value?.showModal();
}
function prepareEmail() {
    const body = `Halo tim TapakLokal,\n\nSaya ingin mengajukan kemitraan.\nKategori: ${selectedTrack.value === 'trip' ? 'Vendor Trip' : 'Oleh-oleh & Kuliner'}\nUsaha: ${brief.value.business}\nPIC: ${brief.value.name}\nKota: ${brief.value.city}\nKontak: ${brief.value.contact}\nTentang usaha: ${brief.value.notes}\n\nTerima kasih.`;
    window.location.href = `mailto:support@tapaklokal.com?subject=${encodeURIComponent(`Pengajuan kemitraan — ${brief.value.business}`)}&body=${encodeURIComponent(body)}`;
}
</script>

<template>
    <Head title="Mitra Vendor — Tumbuh Bersama TapakLokal"><meta name="description" content="Kembangkan usaha wisata dan produk lokal bersama TapakLokal. Pelajari pilihan kemitraan, solusi pengelolaan usaha, dan cara bergabung." /></Head>
    <BusinessLanding program="PARTNERS" :links="links" :faqs="faqs" :login-href="route('vendor.login')" cta="Jadi mitra" @join="openRegistration()">
        <section class="business-hero flex min-h-[calc(100dvh-68px)] flex-col justify-between">
            <div class="business-container my-auto grid w-full flex-1 items-center gap-8 py-6 sm:py-10 lg:grid-cols-[1.05fr_0.95fr] lg:gap-14 lg:py-8 xl:gap-16">
                <div>
                    <p class="business-eyebrow">TAPAKLOKAL PARTNER PROGRAM</p>
                    <h1 class="mt-4 text-[36px] leading-[1.15] font-bold tracking-[-0.045em] text-[#07345a] sm:text-5xl lg:text-[48px] xl:text-[52px]">
                        Usaha lokal Anda.<br />Peluang yang<br /><span class="business-handwritten">lebih luas.</span>
                    </h1>
                    <p class="business-copy mt-5 max-w-md text-sm leading-relaxed text-slate-500 sm:text-base">
                        Hubungkan pengalaman wisata dan produk terbaik Anda dengan lebih banyak penjelajah. Kita tumbuh bersama, dari potensi lokal.
                    </p>
                    <div class="mt-7 flex flex-wrap gap-3">
                        <button class="business-button inline-flex" @click="openRegistration()">Mulai jadi mitra <ArrowRight class="size-4" /></button>
                        <a href="#kemitraan" class="business-button business-button-secondary inline-flex">Jelajahi kemitraan</a>
                    </div>
                </div>
                <div class="relative">
                    <div class="relative h-[320px] overflow-hidden rounded-t-[110px] rounded-b-2xl sm:h-[400px] lg:h-[430px] xl:h-[460px]">
                        <img src="/Assets/Images/partner-hero-person.jpg" alt="Pelaku usaha wisata mengelola layanan perjalanan menggunakan tablet" width="1200" height="896" fetchpriority="high" class="business-photo object-[48%_center]" />
                        <div class="absolute inset-x-0 bottom-0 bg-gradient-to-t from-[#07345a]/85 to-transparent px-6 pt-16 pb-8 text-white sm:pb-10">
                            <p class="text-[10px] font-semibold tracking-[.15em]">BERAKAR LOKAL. BERKEMBANG BERSAMA.</p>
                            <p class="mt-2 text-base font-semibold sm:text-lg">Anda fokus pada pengalaman.<br />Kami bantu membuka peluang.</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="w-full shrink-0 border-t border-[#a9d5eb]/50">
                <div class="business-container grid gap-4 py-4 sm:grid-cols-3 sm:gap-6 sm:py-5">
                    <div v-for="(item, index) in ['Jangkauan pasar lebih luas', 'Informasi usaha lebih terstruktur', 'Pendampingan awal kemitraan']" :key="item" class="flex items-center gap-3 text-xs font-semibold text-[#315a70] sm:text-sm">
                        <span class="text-xs font-normal text-[#678c9e]">0{{ index + 1 }}</span>
                        <span>{{ item }}</span>
                    </div>
                </div>
            </div>
        </section>

        <section id="kemitraan" class="business-container business-section scroll-mt-24">
            <!-- Section Header -->
            <div class="mx-auto max-w-3xl text-center">
                <h2 class="text-[30px] font-bold tracking-tight text-[#07345a] sm:text-4xl lg:text-[42px] leading-tight">
                    Dua Cara Bergabung.<br class="hidden sm:inline" />
                    <span class="text-[#009cf0]">Satu Semangat Tumbuh Bersama.</span>
                </h2>

                <!-- Modern Interactive Segmented Tabs -->
                <div class="mt-8 flex justify-center">
                    <div class="inline-flex rounded-2xl border border-slate-200/80 bg-slate-100/90 p-1.5 shadow-xs" role="tablist" aria-label="Pilihan jenis kemitraan">
                        <button
                            v-for="track in tracks"
                            :key="track.id"
                            role="tab"
                            :aria-selected="selectedTrack === track.id"
                            class="flex items-center gap-3 rounded-xl px-4 py-2.5 sm:px-6 sm:py-3 text-left transition-all duration-200 cursor-pointer"
                            :class="selectedTrack === track.id
                                ? 'bg-white text-[#07345a] shadow-md shadow-slate-900/5 ring-1 ring-black/5 font-bold'
                                : 'text-slate-500 hover:text-[#07345a] hover:bg-white/50 font-medium'"
                            @click="selectedTrack = track.id"
                        >
                            <span
                                class="flex size-8 sm:size-9 items-center justify-center rounded-lg transition-colors shrink-0"
                                :class="selectedTrack === track.id ? 'bg-[#009cf0] text-white shadow-xs' : 'bg-slate-200/80 text-slate-500'"
                            >
                                <component :is="track.icon" class="size-4 sm:size-5" />
                            </span>
                            <div>
                                <div class="text-xs sm:text-sm font-bold leading-tight">{{ track.label }}</div>
                                <div class="text-[10px] sm:text-[11px] font-normal text-slate-400">{{ track.tagline }}</div>
                            </div>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Dynamic Showcase Card -->
            <div class="mt-10 overflow-hidden rounded-3xl border border-sky-100 bg-gradient-to-b from-white via-[#f7fbff] to-[#edf6fd] p-6 sm:p-8 lg:p-10 shadow-xl shadow-sky-950/5 transition-all duration-300">
                <div class="grid items-center gap-8 lg:grid-cols-[1.05fr_1fr] lg:gap-12 xl:gap-16">
                    <!-- Left: Realistic Live Mockup Card -->
                    <div class="relative">
                        <div class="overflow-hidden rounded-2xl border border-slate-200/80 bg-white shadow-lg shadow-sky-900/5 transition duration-300 hover:shadow-xl">
                            <!-- Showcase Hero Photo -->
                            <div class="relative h-48 sm:h-56 overflow-hidden">
                                <img
                                    :src="currentTrack.preview.image"
                                    :alt="currentTrack.preview.title"
                                    class="size-full object-cover transition-transform duration-500 hover:scale-105"
                                    loading="lazy"
                                />
                            </div>

                            <!-- Card Body -->
                            <div class="p-5 sm:p-6">
                                <h4 class="text-base sm:text-lg font-bold text-[#07345a] leading-snug">
                                    {{ currentTrack.preview.title }}
                                </h4>

                                <!-- Meta Info -->
                                <div class="mt-3 flex flex-wrap items-center gap-y-1.5 gap-x-4 text-xs text-slate-500">
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
                                <div class="mt-4 grid grid-cols-3 gap-2 border-t border-slate-100 pt-4">
                                    <div
                                        v-for="spec in currentTrack.preview.features"
                                        :key="spec.label"
                                        class="rounded-xl bg-sky-50/70 p-2.5 text-center ring-1 ring-sky-100"
                                    >
                                        <p class="text-[10px] font-medium text-[#507693]">{{ spec.label }}</p>
                                        <p class="mt-1 text-[11px] font-bold text-[#07345a] leading-tight">{{ spec.value }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Right: Track Highlights & Benefits -->
                    <div>
                        <h3 class="text-2xl font-bold tracking-tight text-[#07345a] sm:text-3xl leading-snug">
                            {{ currentTrack.title }}
                        </h3>

                        <!-- Highlights List -->
                        <div class="mt-6 space-y-3.5">
                            <div
                                v-for="(hl, index) in currentTrack.highlights"
                                :key="hl.title"
                                class="flex items-start gap-3.5 rounded-2xl border border-white/80 bg-white/90 p-3.5 sm:p-4 shadow-xs transition hover:border-sky-200 hover:bg-white hover:shadow-sm"
                            >
                                <span class="flex size-7 shrink-0 items-center justify-center rounded-xl bg-sky-50 text-xs font-bold text-[#009cf0]">
                                    0{{ index + 1 }}
                                </span>
                                <div>
                                    <h4 class="text-sm font-bold text-[#07345a]">{{ hl.title }}</h4>
                                    <p class="mt-0.5 text-xs leading-relaxed text-slate-500">{{ hl.description }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section id="manfaat" class="bg-[#f7fafc]">
            <div class="business-container business-section">
                <div class="mx-auto max-w-2xl text-center"><p class="business-eyebrow">LEBIH RAPI MENGELOLA, LEBIH SIAP BERKEMBANG</p><h2 class="business-heading mt-4">Partner untuk perjalanan<br />bisnis Anda berikutnya.</h2><p class="business-copy mt-5">Dari penawaran yang mudah dipahami hingga persiapan operasional, bangun pengalaman yang meyakinkan sejak awal.</p></div>
                <div class="mt-12 grid gap-5 lg:grid-cols-3">
                    <article class="rounded-2xl border border-slate-200/70 bg-white p-7 lg:col-span-2"><div class="flex items-center gap-3"><LayoutDashboard class="size-6 text-[#009cf0]" /><h3 class="text-xl font-bold text-[#07345a]">Semua detail, satu pandangan.</h3></div><p class="business-copy mt-3 max-w-xl">Persiapkan paket, jadwal, dan informasi peserta dengan struktur yang jelas untuk tim Anda.</p><div class="mt-8 overflow-hidden rounded-xl border border-slate-200 bg-[#fbfdff] p-5"><div class="flex justify-between gap-3 text-xs"><span class="font-bold text-[#07345a]">Ringkasan keberangkatan</span><span class="text-slate-400">Contoh tampilan</span></div><div class="mt-5 grid grid-cols-3 gap-3"><div v-for="(value, index) in ['12 Okt', '12 / 16', '3 hari']" :key="value" class="rounded-lg bg-white p-3 ring-1 ring-slate-100"><p class="text-[10px] text-slate-500">{{ ['Jadwal', 'Peserta', 'Durasi'][index] }}</p><p class="mt-2 text-lg font-bold text-[#07345a] sm:text-2xl">{{ value }}</p></div></div><div class="mt-5 flex items-center gap-3 rounded-lg bg-sky-50 px-4 py-3 text-xs text-[#075890]"><CalendarDays class="size-4 shrink-0" />Detail perjalanan siap ditinjau bersama tim</div></div></article>
                    <article class="flex flex-col rounded-2xl bg-[#07345a] p-7 text-white"><Users class="size-7 text-sky-300" /><h3 class="mt-5 text-xl font-bold">Kekuatan lokal,<br />peluang lebih besar.</h3><p class="mt-4 text-sm leading-7 text-sky-100/80">Cerita, pengetahuan, dan keramahan Anda adalah bagian berharga dari setiap perjalanan. Perkenalkan ke lebih banyak calon pelanggan.</p><div class="mt-auto flex items-center gap-3 pt-9"><span class="flex size-11 items-center justify-center rounded-full border border-white/20"><MapPin class="size-5" /></span><p class="text-xs leading-5 text-sky-100">Dari daerah Anda,<br />untuk penjelajah Indonesia.</p></div></article>
                </div>
                <div class="mt-9 grid gap-8 sm:grid-cols-3"><div v-for="item in [{ icon: FileText, title: 'Penawaran yang jelas', text: 'Bantu pelanggan memahami fasilitas, harga, dan ketentuan sejak awal.' }, { icon: Wallet, title: 'Kerja sama transparan', text: 'Bahas biaya layanan dan mekanisme pembayaran sebelum memulai.' }, { icon: ShieldCheck, title: 'Tumbuh dengan kesiapan', text: 'Tinjau kelengkapan usaha dan standar layanan bersama tim kemitraan.' }]" :key="item.title"><component :is="item.icon" class="size-6 text-[#009cf0]" /><h3 class="mt-4 text-base font-bold text-[#07345a]">{{ item.title }}</h3><p class="mt-2 text-sm leading-6 text-slate-500">{{ item.text }}</p></div></div>
            </div>
        </section>
        <section id="cara-bergabung" class="business-container business-section">
            <p class="business-eyebrow">LANGKAH KECIL, PELUANG BARU</p><div class="mt-4 flex flex-col justify-between gap-5 sm:flex-row sm:items-end"><h2 class="business-heading">Mulai dari usaha Anda.<br />Kami bantu langkah berikutnya.</h2><button class="business-text-link" @click="openRegistration()">Ajukan kemitraan <ArrowUpRight class="size-4" /></button></div>
            <ol class="mt-12 grid gap-8 sm:grid-cols-2 lg:grid-cols-4"><li v-for="(step, index) in steps" :key="step.title" class="border-t border-sky-200 pt-5"><span class="text-3xl font-light text-[#009cf0]">0{{ index + 1 }}</span><h3 class="mt-5 font-bold text-[#07345a]">{{ step.title }}</h3><p class="mt-3 text-sm leading-6 text-slate-500">{{ step.description }}</p></li></ol>
        </section>
        <template #closing><section class="bg-[#e9f7ff]"><div class="business-container flex flex-col items-start justify-between gap-7 py-12 sm:flex-row sm:items-center"><div><p class="business-eyebrow">BERSAMA TAPAKLOKAL</p><h2 class="business-heading mt-3">Potensi lokal Anda,<br />layak dikenal lebih luas.</h2></div><button class="business-button inline-flex shrink-0" @click="openRegistration()">Mari jadi mitra <ArrowRight class="size-4" /></button></div></section></template>
        <template #dialogs>
            <dialog ref="registrationDialog" aria-labelledby="partner-dialog-title" aria-describedby="partner-dialog-description" class="fixed inset-0 m-auto w-[calc(100%-32px)] max-w-lg rounded-2xl border-0 p-6 shadow-2xl sm:p-8" @click="event => { if (event.target === registrationDialog) registrationDialog.close(); }">
                <div class="flex items-start justify-between gap-5"><div><p class="business-eyebrow">KENALKAN USAHA ANDA</p><h2 id="partner-dialog-title" class="mt-2 text-2xl font-bold text-[#07345a]">Mulai percakapan kemitraan</h2></div><button aria-label="Tutup formulir" class="rounded-full p-2 hover:bg-slate-100" @click="registrationDialog.close()"><X class="size-5" /></button></div>
                <p id="partner-dialog-description" class="mt-3 text-sm leading-6 text-slate-500">Isi detail berikut untuk menyiapkan email ke tim kami. Pengajuan baru terkirim setelah Anda mengirim email dari aplikasi email Anda.</p>
                <form class="mt-6 space-y-4" @submit.prevent="prepareEmail">
                    <label class="block text-xs font-semibold">Jenis kemitraan<select v-model="selectedTrack" class="business-input mt-2"><option value="trip">Vendor trip wisata</option><option value="souvenir">Oleh-oleh & kuliner</option></select></label>
                    <label class="block text-xs font-semibold">Nama usaha<input v-model="brief.business" required maxlength="120" autocomplete="organization" class="business-input mt-2" /></label>
                    <div class="grid gap-4 sm:grid-cols-2"><label class="block text-xs font-semibold">Nama penanggung jawab<input v-model="brief.name" required maxlength="100" autocomplete="name" class="business-input mt-2" /></label><label class="block text-xs font-semibold">Kota operasional<input v-model="brief.city" required maxlength="100" autocomplete="address-level2" class="business-input mt-2" /></label></div>
                    <label class="block text-xs font-semibold">Email atau nomor telepon<input v-model="brief.contact" required maxlength="150" class="business-input mt-2" /></label>
                    <label class="block text-xs font-semibold">Ceritakan paket atau produk Anda<textarea v-model="brief.notes" rows="3" maxlength="1500" class="business-input mt-2"></textarea></label>
                    <button type="submit" class="business-button inline-flex w-full">Siapkan email pengajuan <ArrowUpRight class="size-4" /></button><p class="text-center text-xs leading-5 text-slate-500">Tujuan: support@tapaklokal.com</p>
                </form>
            </dialog>
        </template>
    </BusinessLanding>
</template>
