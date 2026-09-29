<script setup>
import { computed, ref } from 'vue';
import { Head, Link } from '@inertiajs/vue3';
import { route } from 'ziggy-js';
import { ArrowRight, ArrowUpRight, Compass, MapPin, Share2, TrendingUp, Users, Wallet } from 'lucide-vue-next';
import BusinessLanding from '../Components/Shared/BusinessLanding.vue';
import AuthModal from '../Components/Shared/AuthModal.vue';

const isAuthModalOpen = ref(false);
const bookings = ref(10);
const averagePrice = ref(2500000);
const commissionRate = ref(10);
const commission = computed(() => Math.round(Number(bookings.value) * Number(averagePrice.value) * Number(commissionRate.value) / 100));
const currency = (value) => new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', maximumFractionDigits: 0 }).format(value);
const links = [{ href: '#manfaat', label: 'Kenapa Affiliate' }, { href: '#cara-kerja', label: 'Cara Kerja' }, { href: '#simulasi', label: 'Simulasi Komisi' }, { href: '#faq', label: 'Tanya Jawab' }];
const steps = [
    { icon: Users, title: 'Mulai dengan akun Anda', text: 'Buat akun TapakLokal dan pelajari ketentuan program affiliate yang berlaku.' },
    { icon: Compass, title: 'Temukan trip yang relevan', text: 'Pilih pengalaman yang cocok dengan minat dan kebutuhan audiens Anda.' },
    { icon: Share2, title: 'Bagikan rekomendasi', text: 'Gunakan tautan affiliate resmi pada konten, komunitas, atau kanal Anda.' },
    { icon: Wallet, title: 'Dapatkan komisi', text: 'Komisi mengikuti pemesanan yang memenuhi syarat dan ketentuan program.' },
];
const faqs = [
    { q: 'Untuk siapa program affiliate ini?', a: 'Program ini ditujukan untuk kreator konten, travel blogger, pengelola komunitas, dan siapa pun yang senang berbagi rekomendasi perjalanan. Pelajari ketentuan program sebelum mulai mempromosikan trip.' },
    { q: 'Apakah membuat akun langsung menjadikan saya affiliate?', a: 'Membuat akun adalah langkah awal. Aktivasi affiliate dan akses tautan resmi mengikuti ketentuan program. Hubungi tim TapakLokal melalui pusat bantuan untuk informasi pendaftaran affiliate.' },
    { q: 'Berapa komisi yang akan saya dapatkan?', a: 'Besaran komisi mengikuti paket, transaksi yang memenuhi syarat, dan ketentuan program yang berlaku. Angka pada kalkulator adalah asumsi simulasi yang dapat Anda ubah, bukan jaminan penghasilan atau penawaran tarif resmi.' },
    { q: 'Di mana saya bisa membagikan rekomendasi?', a: 'Anda dapat berbagi melalui media sosial, blog, dan komunitas yang mengizinkan promosi. Gunakan informasi paket yang akurat dan sampaikan secara terbuka bahwa tautan Anda merupakan tautan affiliate.' },
    { q: 'Bagaimana pelacakan dan pencairan komisi?', a: 'Gunakan tautan affiliate resmi yang diberikan setelah aktivasi program. Ketentuan atribusi, validasi transaksi, serta pencairan perlu dikonfirmasi melalui informasi program atau tim TapakLokal.' },
];
function openRegistration() {
    isAuthModalOpen.value = true;
}
</script>

<template>
    <Head title="Affiliate — Berbagi Perjalanan, Membuka Peluang"><meta name="description" content="Jadikan rekomendasi perjalanan Anda lebih berarti bersama TapakLokal Affiliate. Kenali cara kerja program dan simulasikan potensi komisi Anda." /></Head>
    <BusinessLanding program="AFFILIATES" :links="links" :faqs="faqs" :login-href="route('home', { auth: 'login' })" cta="Mulai bergabung" @join="openRegistration">
        <section class="business-hero flex min-h-[calc(100dvh-68px)] flex-col justify-between">
            <div class="business-container my-auto grid w-full flex-1 items-center gap-8 py-6 sm:py-10 lg:grid-cols-[1.05fr_0.95fr] lg:gap-14 lg:py-8 xl:gap-16">
                <div>
                    <p class="business-eyebrow">TAPAKLOKAL AFFILIATE PROGRAM</p>
                    <h1 class="mt-3 sm:mt-4 text-3xl leading-[1.18] font-bold tracking-[-0.04em] text-[#07345a] sm:text-5xl lg:text-[48px] xl:text-[52px]">
                        Cerita perjalanan Anda.<br />Peluang<br /><span class="business-handwritten">berikutnya.</span>
                    </h1>
                    <p class="business-copy mt-4 sm:mt-5 max-w-md text-xs sm:text-sm lg:text-base leading-relaxed text-slate-500">
                        Bantu orang lain menemukan perjalanan yang tepat. Bangun peluang penghasilan dari rekomendasi yang berarti.
                    </p>
                    <div class="mt-6 sm:mt-7 flex flex-col sm:flex-row gap-3">
                        <button class="business-button w-full sm:w-auto inline-flex" @click="openRegistration">Mulai jadi affiliate <ArrowRight class="size-4" /></button>
                        <a href="#simulasi" class="business-button business-button-secondary w-full sm:w-auto inline-flex">Simulasikan komisi</a>
                    </div>
                </div>
                <div class="relative">
                    <div class="relative h-[240px] xs:h-[280px] sm:h-[400px] lg:h-[430px] xl:h-[460px] overflow-hidden rounded-t-[70px] sm:rounded-t-[110px] rounded-b-2xl shadow-lg shadow-sky-950/5">
                        <img src="/Assets/Images/affiliate-hero-person.jpg" alt="Kreator membagikan rekomendasi perjalanan melalui ponsel" width="1200" height="896" fetchpriority="high" class="business-photo object-[48%_center]" />
                        <div class="absolute inset-x-0 bottom-0 bg-gradient-to-t from-[#07345a]/85 to-transparent px-5 sm:px-6 pt-12 sm:pt-16 pb-6 sm:pb-10 text-white">
                            <p class="text-[9px] sm:text-[10px] font-semibold tracking-[.15em]">BAGIKAN CERITA. DAPATKAN KOMISI.</p>
                            <p class="mt-1.5 sm:mt-2 text-sm sm:text-base lg:text-lg font-semibold leading-snug">Rekomendasi autentik Anda,<br />berdampak nyata bagi wisata lokal.</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="w-full shrink-0 border-t border-[#a9d5eb]/50 bg-white/40 backdrop-blur-xs">
                <div class="business-container grid gap-2.5 py-3.5 sm:grid-cols-3 sm:gap-6 sm:py-5">
                    <div v-for="(item, index) in ['Rekomendasi terkurasi & resmi', 'Komisi transparan per pesanan', 'Materi promosi & tautan siap pakai']" :key="item" class="flex items-center gap-2.5 sm:gap-3 text-xs font-semibold text-[#315a70] sm:text-sm">
                        <span class="text-xs font-normal text-[#678c9e]">0{{ index + 1 }}</span>
                        <span>{{ item }}</span>
                    </div>
                </div>
            </div>
        </section>

        <section id="manfaat" class="business-container business-section">
            <div class="grid gap-8 lg:grid-cols-[1fr_1fr] lg:gap-20"><div><p class="business-eyebrow">REKOMENDASI YANG PUNYA NILAI</p><h2 class="business-heading mt-4">Anda punya cerita.<br />Indonesia punya banyak<br />tempat untuk ditemukan.</h2></div><p class="business-copy self-end">Ubah pengetahuan perjalanan Anda menjadi rekomendasi yang membantu orang lain. Mulai dari destinasi favorit, pengalaman bersama pemandu lokal, hingga trip yang cocok untuk komunitas Anda.</p></div>
            <div class="mt-12 grid gap-8 md:grid-cols-3"><article v-for="item in [{ icon: Compass, title: 'Relevan dengan audiens', text: 'Pilih perjalanan yang sesuai dengan gaya, minat, dan kebutuhan orang yang mengikuti cerita Anda.', label: 'REKOMENDASI LEBIH PERSONAL' }, { icon: Share2, title: 'Bagikan dengan cara Anda', text: 'Buat cerita lewat video, artikel, atau percakapan komunitas. Tetap autentik dengan gaya Anda sendiri.', label: 'RUANG UNTUK KREATIVITAS' }, { icon: Wallet, title: 'Buka peluang penghasilan', text: 'Dapatkan komisi dari transaksi yang memenuhi ketentuan program melalui tautan affiliate resmi Anda.', label: 'CERITA YANG MEMBERI NILAI' }]" :key="item.title" class="border-t border-sky-200 pt-6"><component :is="item.icon" class="size-7 text-[#009cf0]" /><h3 class="mt-5 text-xl font-bold text-[#07345a]">{{ item.title }}</h3><p class="mt-3 text-sm leading-7 text-slate-500">{{ item.text }}</p><p class="mt-6 text-[10px] font-bold tracking-widest text-[#007bbd]">{{ item.label }}</p></article></div>
        </section>

        <section id="cara-kerja" class="bg-[#f3faff]"><div class="business-container business-section grid gap-12 lg:grid-cols-[.9fr_1.1fr] lg:gap-20">
            <div class="relative min-h-[360px] overflow-hidden rounded-2xl"><img src="/Assets/Images/360/mt-gede-basecamp.jpg" alt="Suasana destinasi pegunungan Indonesia" width="800" height="600" loading="lazy" class="absolute inset-0 business-photo" /><div class="absolute inset-0 bg-gradient-to-t from-[#07345a] via-[#07345a]/20 to-transparent"></div><div class="relative flex h-full min-h-[360px] flex-col justify-end p-7 sm:p-9"><span class="w-fit rounded-full border border-white/40 px-3 py-1.5 text-[10px] font-semibold tracking-widest text-white">DARI CERITA KE AKSI</span><p class="mt-5 text-3xl leading-tight font-bold tracking-tight text-white">Perjalanan hebat<br />sering dimulai dari<br />rekomendasi sederhana.</p><Link :href="route('trips.category', { type: 'open-trip' })" class="mt-6 inline-flex items-center gap-2 text-sm font-semibold text-white">Temukan inspirasi trip <ArrowUpRight class="size-4" /></Link></div></div>
            <div><p class="business-eyebrow">BAGAIMANA CARANYA?</p><h2 class="business-heading mt-4">Empat langkah menuju<br />peluang baru.</h2><ol class="mt-8 space-y-6"><li v-for="(step, index) in steps" :key="step.title" class="flex gap-5"><span class="flex size-10 shrink-0 items-center justify-center rounded-full border border-sky-200 bg-white text-sm font-bold text-[#009cf0]">0{{ index + 1 }}</span><div><h3 class="text-base font-bold text-[#07345a]">{{ step.title }}</h3><p class="mt-2 text-sm leading-6 text-slate-500">{{ step.text }}</p></div></li></ol></div>
        </div></section>

        <section id="simulasi" class="business-container business-section">
            <div class="grid gap-10 lg:grid-cols-[.8fr_1.2fr] lg:gap-20">
                <div><p class="business-eyebrow">HITUNG PELUANG ANDA</p><h2 class="business-heading mt-4">Rekomendasi kecil.<br />Potensi yang berarti.</h2><p class="business-copy mt-5">Coba beberapa skenario untuk melihat bagaimana nilai pemesanan dan komisi memengaruhi potensi pendapatan Anda.</p><div class="mt-7 flex items-start gap-3 rounded-xl bg-sky-50 p-4 text-xs leading-6 text-[#527386]"><TrendingUp class="mt-1 size-5 shrink-0 text-[#009cf0]" /><p>Simulasi menggunakan asumsi Anda. Tarif, transaksi yang memenuhi syarat, dan pencairan mengikuti ketentuan program.</p></div></div>
                <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-xl shadow-slate-900/5">
                    <div class="space-y-7 p-6 sm:p-8">
                        <div><div class="flex items-center justify-between gap-3"><label for="affiliate-bookings" class="text-sm font-semibold text-[#07345a]">Pemesanan per bulan</label><output for="affiliate-bookings" class="rounded-lg bg-sky-50 px-3 py-1.5 text-sm font-bold text-[#007bbd]">{{ bookings }} pesanan</output></div><input id="affiliate-bookings" v-model.number="bookings" type="range" min="1" max="100" step="1" class="mt-5 w-full accent-[#009cf0]" /><div class="mt-1 flex justify-between text-[10px] text-slate-400"><span>1 pesanan</span><span>100 pesanan</span></div></div>
                        <div class="grid gap-5 sm:grid-cols-2"><label class="text-sm font-semibold text-[#07345a]" for="affiliate-price">Rata-rata nilai pesanan<select id="affiliate-price" v-model.number="averagePrice" class="business-input mt-3"><option v-for="value in [500000, 1000000, 2500000, 5000000, 10000000]" :key="value" :value="value">{{ currency(value) }}</option></select></label><label class="text-sm font-semibold text-[#07345a]" for="affiliate-rate">Asumsi komisi<select id="affiliate-rate" v-model.number="commissionRate" class="business-input mt-3"><option v-for="value in [5, 10, 15, 20]" :key="value" :value="value">{{ value }}%</option></select></label></div>
                    </div>
                    <div class="bg-[#07345a] px-6 py-7 text-white sm:px-8"><p class="text-xs text-sky-100">Estimasi komisi per bulan</p><output class="mt-2 block text-3xl font-bold tracking-tight sm:text-4xl" aria-live="polite" aria-atomic="true">{{ currency(commission) }}</output><p class="mt-4 border-t border-white/15 pt-4 text-[11px] leading-6 text-sky-100/75">{{ bookings }} pesanan × {{ currency(averagePrice) }} × {{ commissionRate }}%<br />Hasil simulasi, bukan jaminan pendapatan.</p></div>
                </div>
            </div>
        </section>

        <section class="border-y border-slate-100 bg-[#fbfdff]"><div class="business-container grid items-center gap-8 py-12 md:grid-cols-[1.1fr_1fr]"><div><p class="business-eyebrow">BUKAN SEKADAR MEMBAGIKAN TAUTAN</p><h2 class="mt-4 text-2xl leading-snug font-bold tracking-tight text-[#07345a]">Setiap rekomendasi membuka<br />pintu untuk pelaku wisata lokal.</h2></div><div class="grid grid-cols-3 gap-4"><div v-for="item in [{ icon: MapPin, label: 'Destinasi lebih dikenal' }, { icon: Users, label: 'Pemandu lokal berdaya' }, { icon: Compass, label: 'Pengalaman lebih autentik' }]" :key="item.label" class="flex flex-col gap-3 border-l border-sky-200 pl-4"><component :is="item.icon" class="size-6 text-[#009cf0]" /><p class="text-xs leading-5 font-semibold text-[#527386]">{{ item.label }}</p></div></div></div></section>
        <template #closing><section class="bg-[#e9f7ff]"><div class="business-container flex flex-col items-start justify-between gap-7 py-12 sm:flex-row sm:items-center"><div><p class="business-eyebrow">CERITA ANDA BISA JADI AWALNYA</p><h2 class="business-heading mt-3">Bagikan inspirasi.<br />Buka peluang bersama.</h2></div><button class="business-button inline-flex shrink-0" @click="openRegistration">Mulai bergabung <ArrowRight class="size-4" /></button></div></section></template>
        <template #dialogs><AuthModal :open="isAuthModalOpen" mode="register" title="Mulai dari akun TapakLokal Anda" subtitle="Buat akun untuk memulai. Aktivasi affiliate mengikuti ketentuan program kemitraan." @close="isAuthModalOpen = false" /></template>
    </BusinessLanding>
</template>
