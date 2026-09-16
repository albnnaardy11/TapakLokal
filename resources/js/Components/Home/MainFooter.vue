<script setup>
import {
    ArrowUp,
    CheckCircle2,
    Compass,
    Heart,
    Mail,
    MapPin,
    MessageCircle,
    Phone,
    Send,
    ShieldCheck,
    Sparkles,
    UsersRound,
    Wallet
} from 'lucide-vue-next';
import { ref } from 'vue';

const email = ref('');
const subscribeSuccess = ref(false);

const subscribe = () => {
    if (!email.value.trim()) return;
    subscribeSuccess.value = true;
    setTimeout(() => {
        subscribeSuccess.value = false;
        email.value = '';
    }, 3500);
};

const scrollToTop = () => {
    window.scrollTo({ top: 0, behavior: 'smooth' });
};

const navigationLinks = [
    {
        title: 'Jelajah & Trip',
        links: [
            { label: 'Open Trip Populer', desc: 'Jelajah bareng traveler lain' },
            { label: 'Open PO Kuliner', desc: 'Oleh-oleh & cita rasa lokal' },
            { label: 'Private & Family Trip', desc: 'Jadwal & rute fleksibel' },
            { label: 'Wisata Bahari & Pantai', desc: 'Karimunjawa, Bajo, dll' },
            { label: 'Eksplorasi Gunung & Alam', desc: 'Merbabu, Bromo, Ranu Kumbolo' },
            { label: 'Hidden Gems Nusantara', desc: 'Destinasi belum terjamah' },
        ],
    },
    {
        title: 'Fitur & Komunitas',
        links: [
            { label: 'Solit Bill', desc: 'Split bill mudah & adil' },
            { label: 'Mitra Akamsi', desc: 'Pemandu lokal terverifikasi' },
            { label: 'Member Tiers & Points', desc: 'Bronze, Silver, Gold' },
            { label: 'Voucher & Promo', desc: 'Diskon perjalanan hemat' },
            { label: 'Komunitas Petualang', desc: 'Forum & teman jalan' },
            { label: 'Blog & Cerita Perjalanan', desc: 'Tips & inspirasi jalan-jalan' },
        ],
    },
    {
        title: 'Bantuan & Kebijakan',
        links: [
            { label: 'Pusat Bantuan (FAQ)', desc: 'Pertanyaan sering diajukan' },
            { label: 'Panduan Pemesanan', desc: 'Cara reservasi & bayar' },
            { label: 'Kebijakan Refund', desc: 'Jaminan pembatalan transparan' },
            { label: 'Syarat & Ketentuan', desc: 'Ketentuan layanan kami' },
            { label: 'Kebijakan Privasi', desc: 'Keamanan data pengguna' },
            { label: 'Hubungi Customer Care', desc: 'Respons cepat setiap hari' },
        ],
    },
];

const paymentMethods = [
    { name: 'BCA', label: 'BCA' },
    { name: 'Mandiri', label: 'Mandiri' },
    { name: 'BNI', label: 'BNI' },
    { name: 'BRI', label: 'BRI' },
    { name: 'QRIS', label: 'QRIS' },
    { name: 'GoPay', label: 'GoPay' },
    { name: 'OVO', label: 'OVO' },
    { name: 'ShopeePay', label: 'ShopeePay' },
];

const socialMedia = [
    { name: 'Instagram', handle: '@tapaklokal.id' },
    { name: 'TikTok', handle: '@tapaklokal' },
    { name: 'YouTube', handle: 'TapakLokal Official' },
    { name: 'X / Twitter', handle: '@tapaklokal' },
];
</script>

<template>
    <footer class="mt-20 border-t border-[#dce5f0] bg-white text-slate-700 sm:mt-24">
        <!-- 1. Pre-Footer Call to Action (Newsletter & Community) -->
        <div class="mx-auto max-w-[1180px] px-5 pt-12 sm:px-8">
            <div class="relative isolate overflow-hidden rounded-[24px] bg-gradient-to-br from-[#183660] via-[#1d4277] to-[#3E7BEF] p-7 text-white shadow-[0_12px_32px_rgba(24,54,96,0.18)] sm:p-10 lg:p-12">
                <!-- Ambient glow decoration -->
                <div class="pointer-events-none absolute -right-20 -top-24 -z-10 size-80 rounded-full bg-sky-400/25 blur-3xl" aria-hidden="true"></div>
                <div class="pointer-events-none absolute -bottom-24 left-1/3 -z-10 size-72 rounded-full bg-indigo-500/20 blur-3xl" aria-hidden="true"></div>

                <div class="grid items-center gap-8 lg:grid-cols-[1.25fr_1fr]">
                    <div>
                        <div class="inline-flex items-center gap-2 rounded-full bg-white/15 px-3 py-1 text-[11px] font-bold tracking-wide text-white backdrop-blur-sm">
                            <Sparkles class="size-3.5 text-amber-300" />
                            <span>KOMUNITAS TAPAKLOKAL</span>
                        </div>
                        <h2 class="mt-3 text-2xl font-extrabold leading-tight tracking-tight text-white sm:text-3xl lg:text-4xl">
                            Jangan Lewatkan Cerita & Promo Tersembunyi.
                        </h2>
                        <p class="mt-3 text-xs leading-relaxed text-white/85 sm:text-sm">
                            Dapatkan kurasi destinasi otentik nusantara, potongan voucher bulanan, dan jadwal open trip eksklusif langsung ke email Anda.
                        </p>

                        <div class="mt-4 flex flex-wrap items-center gap-4 text-[11px] font-semibold text-white/80">
                            <span class="flex items-center gap-1.5"><CheckCircle2 class="size-3.5 text-emerald-400" /> Tanpa spam</span>
                            <span class="flex items-center gap-1.5"><CheckCircle2 class="size-3.5 text-emerald-400" /> 25.000+ Petualang</span>
                            <span class="flex items-center gap-1.5"><CheckCircle2 class="size-3.5 text-emerald-400" /> Berhenti langganan kapan saja</span>
                        </div>
                    </div>

                    <div>
                        <form @submit.prevent="subscribe" class="flex flex-col gap-2.5 sm:flex-row sm:items-center">
                            <div class="relative flex-1">
                                <Mail class="pointer-events-none absolute left-3.5 top-1/2 size-4 -translate-y-1/2 text-slate-400" />
                                <input
                                    v-model="email"
                                    type="email"
                                    required
                                    placeholder="Masukkan alamat email kamu..."
                                    class="h-11 w-full rounded-xl border border-white/20 bg-white/95 pl-10 pr-3 text-xs font-medium text-slate-800 placeholder:text-slate-400 focus:border-white focus:bg-white focus:outline-none focus:ring-2 focus:ring-sky-200"
                                />
                            </div>
                            <button
                                type="submit"
                                class="inline-flex h-11 items-center justify-center gap-2 rounded-xl bg-white px-5 text-xs font-bold text-[#183660] shadow-sm transition hover:bg-[#edf3ff] hover:text-[#3E7BEF] active:scale-[0.98]"
                            >
                                <span>Langganan</span>
                                <Send class="size-3.5 text-[#3E7BEF]" />
                            </button>
                        </form>

                        <p v-if="subscribeSuccess" class="mt-2.5 rounded-lg bg-white/20 px-3 py-1.5 text-center text-xs font-semibold text-emerald-200 backdrop-blur-sm">
                            ✓ Terima kasih! Anda telah terdaftar di buletin TapakLokal.
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- 2. Value Highlight Strip (Echoing the #3E7BEF brand color from the top bar) -->
        <div class="mx-auto mt-12 max-w-[1180px] px-5 sm:px-8">
            <div class="grid grid-cols-2 gap-3 border-y border-[#e6edf7] py-6 sm:grid-cols-4 sm:gap-4">
                <div class="flex items-center gap-3">
                    <span class="grid size-10 shrink-0 place-items-center rounded-xl bg-[#edf3ff] text-[#3E7BEF]">
                        <ShieldCheck class="size-5" />
                    </span>
                    <div>
                        <h3 class="text-xs font-bold text-[#172c50]">Pemandu Terverifikasi</h3>
                        <p class="text-[10px] text-slate-400">Akamsi teruji & berlisensi</p>
                    </div>
                </div>

                <div class="flex items-center gap-3">
                    <span class="grid size-10 shrink-0 place-items-center rounded-xl bg-[#edf3ff] text-[#3E7BEF]">
                        <Wallet class="size-5" />
                    </span>
                    <div>
                        <h3 class="text-xs font-bold text-[#172c50]">Solit Bill Cepat</h3>
                        <p class="text-[10px] text-slate-400">Bagi tagihan tanpa repot</p>
                    </div>
                </div>

                <div class="flex items-center gap-3">
                    <span class="grid size-10 shrink-0 place-items-center rounded-xl bg-[#edf3ff] text-[#3E7BEF]">
                        <Heart class="size-5" />
                    </span>
                    <div>
                        <h3 class="text-xs font-bold text-[#172c50]">Dukung Warga Lokal</h3>
                        <p class="text-[10px] text-slate-400">100% dampak langsung</p>
                    </div>
                </div>

                <div class="flex items-center gap-3">
                    <span class="grid size-10 shrink-0 place-items-center rounded-xl bg-[#edf3ff] text-[#3E7BEF]">
                        <MessageCircle class="size-5" />
                    </span>
                    <div>
                        <h3 class="text-xs font-bold text-[#172c50]">Bantuan 7 Hari/Minggu</h3>
                        <p class="text-[10px] text-slate-400">CS ramah & cepat tanggap</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- 3. Main Footer Links Grid -->
        <div class="mx-auto max-w-[1180px] px-5 py-12 sm:px-8 sm:py-14">
            <div class="grid grid-cols-1 gap-9 sm:grid-cols-2 lg:grid-cols-[1.3fr_1fr_1fr_1fr_1fr] lg:gap-8">
                <!-- Brand Info Column (Matches MainNavigation branding exactly) -->
                <div class="sm:col-span-2 lg:col-span-1">
                    <a href="/" class="inline-block" aria-label="TapakLokal Beranda">
                        <span class="text-[20px] font-extrabold tracking-[-0.07em] text-slate-900">
                            tapa<span class="text-[#3E7BEF]">k</span>lokal
                        </span>
                    </a>
                    <p class="mt-3 text-xs leading-relaxed text-slate-500">
                        Platform kurasi perjalanan lokal dan kuliner otentik Indonesia. Menjelajah bersama komunitas, memajukan warga lokal tanpa biaya siluman.
                    </p>

                    <!-- Contact details -->
                    <div class="mt-5 space-y-2 text-xs text-slate-600">
                        <div class="flex items-center gap-2.5">
                            <Phone class="size-3.5 text-[#3E7BEF]" />
                            <span class="font-medium">+62 821-3456-7890 (WhatsApp)</span>
                        </div>
                        <div class="flex items-center gap-2.5">
                            <Mail class="size-3.5 text-[#3E7BEF]" />
                            <span class="font-medium">halo@tapaklokal.id</span>
                        </div>
                        <div class="flex items-center gap-2.5">
                            <MapPin class="size-3.5 text-[#3E7BEF]" />
                            <span class="text-slate-500">Yogyakarta & Jakarta, Indonesia</span>
                        </div>
                    </div>

                    <!-- Locale & Country Badge matching Navbar -->
                    <div class="mt-5 inline-flex items-center gap-2 rounded-lg border border-[#e1eaf5] bg-[#f8fbff] px-2.5 py-1.5 text-[11px] font-semibold text-slate-700">
                        <span class="text-sm">🇮🇩</span>
                        <span>Indonesia · IDR (Rp)</span>
                    </div>
                </div>

                <!-- Navigation Columns -->
                <div v-for="nav in navigationLinks" :key="nav.title">
                    <h3 class="text-xs font-bold uppercase tracking-wider text-[#183660]">
                        {{ nav.title }}
                    </h3>
                    <ul class="mt-4 space-y-2.5">
                        <li v-for="item in nav.links" :key="item.label">
                            <a
                                href="#"
                                class="group inline-flex flex-col text-xs font-medium text-slate-600 transition-colors hover:text-[#3E7BEF]"
                            >
                                <span>{{ item.label }}</span>
                                <span class="text-[10px] text-slate-400 group-hover:text-slate-500">{{ item.desc }}</span>
                            </a>
                        </li>
                    </ul>
                </div>

                <!-- Payment & Trust Badges -->
                <div>
                    <h3 class="text-xs font-bold uppercase tracking-wider text-[#183660]">
                        Pembayaran Resmi
                    </h3>
                    <p class="mt-2 text-[11px] text-slate-500">
                        Didukung sistem pembayaran aman & terenkripsi:
                    </p>

                    <div class="mt-3 grid grid-cols-2 gap-1.5">
                        <div
                            v-for="method in paymentMethods"
                            :key="method.name"
                            class="flex h-7 items-center justify-center rounded-md border border-[#e3ebf6] bg-[#f9fbfe] px-2 text-[10px] font-bold text-slate-600 transition hover:border-[#b9ddff] hover:bg-white hover:text-[#3E7BEF]"
                        >
                            {{ method.label }}
                        </div>
                    </div>

                    <div class="mt-4 rounded-xl border border-sky-100 bg-[#edf6ff]/60 p-3 text-[10px] leading-4 text-slate-600">
                        <span class="font-bold text-[#183660]">Keamanan Terjamin</span>
                        <p class="mt-0.5 text-slate-500">Enkripsi 256-Bit SSL & verifikasi partner ketat.</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- 4. Bottom Ribbon (Copyright & Social Links) -->
        <div class="border-t border-[#e6edf7] bg-[#f8fbff]">
            <div class="mx-auto flex max-w-[1180px] flex-col items-center justify-between gap-4 px-5 py-5 sm:flex-row sm:px-8">
                <div class="flex flex-col items-center gap-1 text-center sm:items-start sm:text-left">
                    <p class="text-xs text-slate-500">
                        &copy; 2026 <strong class="font-bold text-slate-800">TapakLokal</strong>. Seluruh hak cipta dilindungi.
                    </p>
                    <p class="text-[10px] text-slate-400">
                        Bangga Buatan & Petualangan Indonesia · Dirancang selaras untuk pejalan nusantara.
                    </p>
                </div>

                <div class="flex items-center gap-2">
                    <a
                        v-for="social in socialMedia"
                        :key="social.name"
                        href="#"
                        class="rounded-lg border border-[#dce5f0] bg-white px-2.5 py-1 text-[11px] font-semibold text-slate-600 shadow-sm transition hover:border-[#3E7BEF] hover:bg-[#edf3ff] hover:text-[#3E7BEF]"
                        :title="social.handle"
                    >
                        {{ social.name }}
                    </a>

                    <button
                        type="button"
                        @click="scrollToTop"
                        class="ml-2 inline-flex size-8 items-center justify-center rounded-lg border border-[#dce5f0] bg-white text-[#3E7BEF] shadow-sm transition hover:bg-[#3E7BEF] hover:text-white"
                        aria-label="Kembali ke atas"
                        title="Kembali ke atas"
                    >
                        <ArrowUp class="size-4" />
                    </button>
                </div>
            </div>
        </div>
    </footer>
</template>
