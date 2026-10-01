<script setup>
import { computed, ref } from 'vue';
import { Head, Link, usePage } from '@inertiajs/vue3';
import { route } from 'ziggy-js';
import {
    ArrowRight,
    Award,
    Check,
    ChevronDown,
    ChevronRight,
    Coins,
    Crown,
    Gift,
    Headset,
    HeartHandshake,
    Percent,
    Shield,
    Sparkles,
    Star,
    Ticket,
    UserCheck,
    Zap,
} from 'lucide-vue-next';
import MainNavigation from '../Components/Shared/MainNavigation.vue';
import MainFooter from '../Components/Shared/MainFooter.vue';

const page = usePage();
const user = computed(() => page.props.auth?.user);

const activeTierTab = ref('silver');

const userTier = computed(() => (user.value?.tier || 'Bronze').toLowerCase());
const userPoints = computed(() => user.value?.points || 0);

const tiers = [
    {
        id: 'bronze',
        name: 'Bronze Priority',
        points: '0 Poin',
        tagline: 'Langkah Awal Petualangan Istimewa',
        gradient: 'from-[#A86B3E] to-[#874E25]',
        border: 'border-[#A86B3E]/30',
        badgeBg: 'bg-[#A86B3E]/15 text-[#874E25]',
        accent: '#A86B3E',
        discount: '5%',
        pointMultiplier: '1.0x',
        perks: [
            'Diskon 5% untuk semua paket Open Trip pilihan',
            'Layanan bantuan traveler standar prioritas',
            'Katalog promo bulanan khusus anggota',
            '1.0x Poin dasar reward setiap transaksi',
            'Akses komunitas traveler TapakLokal',
        ],
    },
    {
        id: 'silver',
        name: 'Silver Priority',
        points: '1.000 Poin',
        tagline: 'Kenyamanan Ekstra & Akses Eksklusif',
        gradient: 'from-[#748091] to-[#546071]',
        border: 'border-[#748091]/30',
        badgeBg: 'bg-[#748091]/15 text-[#546071]',
        accent: '#748091',
        discount: '10%',
        pointMultiplier: '1.5x',
        popular: true,
        perks: [
            'Diskon 10% untuk seluruh Open & Private Trip',
            'Akses 24 jam lebih awal ke Flash Sale & Open PO Oleh-oleh',
            'Garansi Fast-Track pengajuan refund & reschedule tiket',
            '1.5x Poin reward di setiap pemesanan trip',
            'Badge keanggotaan Silver Priority di profil & ulasan',
            'Bebas biaya administrasi pembatalan darurat (1x per tahun)',
        ],
    },
    {
        id: 'gold',
        name: 'Gold Priority',
        points: '5.000 Poin',
        tagline: 'Puncak Keistimewaan VIP Nusantara',
        gradient: 'from-[#C5963E] to-[#9E7321]',
        border: 'border-[#C5963E]/30',
        badgeBg: 'bg-[#C5963E]/15 text-[#9E7321]',
        accent: '#C5963E',
        discount: '15%',
        pointMultiplier: '2.0x',
        perks: [
            'Diskon tertinggi 15% tanpa batas kuota minimum',
            'Dedicated 24/7 Personal Travel Concierge via WhatsApp VIP',
            'Fasilitas VIP Pickup & Welcome Kit dari Mitra Trip lokal',
            '2.0x Poin reward ganda di semua aktivitas',
            'Hadiah merchandise & oleh-oleh eksklusif tahunan',
            'Undangan khusus acara gathering & festival budaya partner',
        ],
    },
];

const faqs = ref([
    {
        q: 'Bagaimana cara bergabung dengan TapakLokal Priority?',
        a: 'Keanggotaan TapakLokal Priority aktif secara otomatis begitu Anda membuat akun di TapakLokal. Anda akan langsung memulai dari tier Bronze Priority tanpa biaya pendaftaran apapun.',
        open: true,
    },
    {
        q: 'Bagaimana cara menaikkan tier keanggotaan saya?',
        a: 'Tier akan meningkat secara otomatis seiring bertambahnya Poin Anda dari pemesanan paket Open Trip, Private Trip, dan Open PO Oleh-oleh. Kumpulkan 1.000 Poin untuk Silver Priority, dan 5.000 Poin untuk Gold Priority.',
        open: false,
    },
    {
        q: 'Berapa lama masa berlaku status Priority saya?',
        a: 'Status Priority Anda berlaku selama 12 bulan penuh sejak tanggal pencapaian tier. Jika dalam periode tersebut Anda tetap aktif bertualang, status keanggotaan Anda akan diperpanjang secara otomatis.',
        open: false,
    },
    {
        q: 'Bagaimana cara menggunakan diskon member Priority?',
        a: 'Diskon member Priority diterapkan secara otomatis saat Anda melakukan checkout pemesanan trip di TapakLokal tanpa perlu memasukkan kode promo secara manual.',
        open: false,
    },
]);

const toggleFaq = (index) => {
    faqs.value[index].open = !faqs.value[index].open;
};
</script>

<template>
    <Head title="TapakLokal Priority — Keistimewaan Eksklusif Penjelajah" />

    <div class="min-h-screen bg-[#f8fafc] text-[#1c2938]">
        <!-- TOP NAVIGATION -->
        <MainNavigation />

        <!-- HERO BANNER SECTION (LUXURY TRAVELOKA PRIORITY BACKDROP) -->
        <section class="relative overflow-hidden bg-gradient-to-b from-[#0a182c] via-[#0f2444] to-[#0a182c] px-4 pt-16 pb-20 text-white sm:px-6 lg:px-8">
            <!-- Background Radial Glows & Geometric Starlight -->
            <div class="pointer-events-none absolute -left-20 -top-20 size-96 rounded-full bg-[#0088ff]/15 blur-3xl"></div>
            <div class="pointer-events-none absolute -right-20 top-1/2 size-96 rounded-full bg-[#f59e0b]/10 blur-3xl"></div>

            <div class="relative mx-auto max-w-5xl text-center">
                <!-- Program Badge -->
                <div class="inline-flex items-center gap-2 rounded-full border border-amber-400/30 bg-amber-400/10 px-4 py-1.5 text-xs font-bold text-amber-300 backdrop-blur-md shadow-xs">
                    <Award class="size-4" />
                    <span>PROGRAM KEANGGOTAAN EKSKLUSIF</span>
                </div>

                <!-- Main Heading -->
                <h1 class="mt-5 text-3xl font-black tracking-tight text-white sm:text-5xl lg:text-6xl sm:leading-[1.15]">
                    Satu Keanggotaan, <br class="hidden sm:inline" />
                    <span class="bg-gradient-to-r from-[#38bdf8] via-[#60a5fa] to-[#fbbf24] bg-clip-text text-transparent">
                        Ribuan Keistimewaan Nusantara.
                    </span>
                </h1>

                <!-- Subtitle -->
                <p class="mx-auto mt-4 max-w-2xl text-sm sm:text-base leading-relaxed text-slate-300">
                    TapakLokal Priority adalah apresiasi kami untuk para penjelajah setia. Nikmati diskon eksklusif instan, layanan pelanggan jalur kilat 24/7, dan perolehan poin berlipat di setiap perjalanan.
                </p>

                <!-- USER TIER STATUS CARD (IF LOGGED IN) -->
                <div
                    v-if="user"
                    class="mx-auto mt-10 max-w-xl overflow-hidden rounded-3xl border border-white/15 bg-white/10 p-5 sm:p-6 backdrop-blur-md shadow-2xl text-left"
                >
                    <div class="flex items-center justify-between gap-4">
                        <div class="flex items-center gap-3.5 min-w-0">
                            <!-- Avatar with Metallic Ring -->
                            <div class="size-13 shrink-0 overflow-hidden rounded-full border-2 border-amber-300/70 p-0.5 bg-white/10 shadow-md">
                                <img
                                    v-if="user.avatar"
                                    :src="user.avatar"
                                    :alt="user.name"
                                    referrerpolicy="no-referrer"
                                    class="size-full rounded-full object-cover"
                                />
                                <div v-else class="flex size-full items-center justify-center rounded-full bg-[#0088ff] text-base font-bold text-white">
                                    {{ user.name?.charAt(0).toUpperCase() || 'U' }}
                                </div>
                            </div>
                            <div class="min-w-0">
                                <p class="text-xs text-slate-300">Status Keanggotaan Anda</p>
                                <h3 class="truncate text-base font-extrabold text-white sm:text-lg">
                                    {{ user.name }}
                                </h3>
                            </div>
                        </div>

                        <div class="text-right shrink-0">
                            <span class="inline-flex items-center gap-1.5 rounded-full bg-gradient-to-r from-amber-400 to-yellow-500 px-3 py-1 text-xs font-black text-slate-900 shadow-sm">
                                <Crown class="size-3.5" />
                                {{ user.tier || 'Bronze Priority' }}
                            </span>
                            <p class="mt-1 text-xs font-semibold text-amber-200">
                                {{ userPoints }} Poin
                            </p>
                        </div>
                    </div>

                    <!-- Tier Progress Hint -->
                    <div class="mt-5 border-t border-white/10 pt-4">
                        <div class="flex items-center justify-between text-xs text-slate-300">
                            <span>Target Berikutnya: <strong class="text-white">{{ userTier === 'bronze' ? 'Silver Priority' : (userTier === 'silver' ? 'Gold Priority' : 'Maksimum VIP') }}</strong></span>
                            <span class="text-amber-300 font-bold">{{ userTier === 'gold' ? 'Tier Tertinggi' : (userTier === 'silver' ? `${Math.max(0, 5000 - userPoints)} Poin lagi` : `${Math.max(0, 1000 - userPoints)} Poin lagi`) }}</span>
                        </div>
                        <div class="mt-2 h-2 w-full overflow-hidden rounded-full bg-white/20">
                            <div
                                class="h-full bg-gradient-to-r from-[#0088ff] via-[#38bdf8] to-[#f59e0b] rounded-full transition-all duration-500"
                                :style="{
                                    width: userTier === 'gold' ? '100%' : (userTier === 'silver' ? `${Math.min(100, (userPoints / 5000) * 100)}%` : `${Math.min(100, (userPoints / 1000) * 100)}%`)
                                }"
                            ></div>
                        </div>
                    </div>
                </div>

                <!-- GUEST CALL TO ACTION -->
                <div v-else class="mt-8 flex flex-wrap items-center justify-center gap-4">
                    <Link
                        :href="route('catalog')"
                        class="inline-flex items-center gap-2 rounded-full bg-gradient-to-r from-[#0088ff] to-[#0066cc] px-7 py-3 text-sm font-bold text-white shadow-lg shadow-blue-500/30 transition hover:-translate-y-0.5 hover:shadow-blue-500/40"
                    >
                        <span>Jelajahi Paket Trip</span>
                        <ArrowRight class="size-4" />
                    </Link>
                </div>
            </div>
        </section>

        <!-- 3 TIER LEVELS SECTION (BRONZE, SILVER, GOLD) -->
        <section class="mx-auto -mt-8 max-w-6xl px-4 sm:px-6 lg:px-8">
            <div class="grid gap-6 md:grid-cols-3">
                <div
                    v-for="tier in tiers"
                    :key="tier.id"
                    class="relative flex flex-col justify-between overflow-hidden rounded-3xl border bg-white p-6 sm:p-7 shadow-[0_10px_30px_rgba(15,30,65,0.06)] transition-all duration-300 hover:-translate-y-1.5 hover:shadow-[0_20px_40px_rgba(15,30,65,0.12)]"
                    :class="[
                        tier.id === userTier ? 'ring-2 ring-[#0088ff] shadow-blue-500/10' : 'border-slate-200/80',
                    ]"
                >
                    <!-- Popular / Active Ribbon -->
                    <div
                        v-if="tier.id === userTier"
                        class="absolute -right-12 top-6 rotate-45 bg-[#0088ff] px-12 py-1 text-[10px] font-black uppercase tracking-wider text-white shadow-xs"
                    >
                        Tier Anda
                    </div>
                    <div
                        v-else-if="tier.popular"
                        class="absolute -right-12 top-6 rotate-45 bg-amber-500 px-12 py-1 text-[10px] font-black uppercase tracking-wider text-white shadow-xs"
                    >
                        Terpopuler
                    </div>

                    <div>
                        <!-- Tier Header Card -->
                        <div
                            class="rounded-2xl p-4 text-white shadow-sm"
                            :class="`bg-gradient-to-br ${tier.gradient}`"
                        >
                            <div class="flex items-center justify-between">
                                <span class="rounded-lg bg-white/20 px-2.5 py-1 text-[11px] font-black uppercase tracking-wider backdrop-blur-xs">
                                    {{ tier.name }}
                                </span>
                                <Award class="size-6 text-white/90" />
                            </div>
                            <p class="mt-3 text-2xl font-black">{{ tier.discount }} Diskon</p>
                            <p class="mt-0.5 text-xs text-white/80">{{ tier.tagline }}</p>
                        </div>

                        <!-- Requirements -->
                        <div class="mt-5 flex items-center justify-between border-b border-slate-100 pb-4 text-xs">
                            <span class="text-slate-500">Syarat Poin:</span>
                            <span class="font-extrabold text-slate-800">{{ tier.points }}</span>
                        </div>

                        <!-- Perks List -->
                        <div class="mt-5 space-y-3">
                            <p class="text-xs font-extrabold uppercase tracking-wider text-slate-400">Keuntungan Member:</p>
                            <ul class="space-y-2.5 text-xs text-slate-600">
                                <li
                                    v-for="(perk, idx) in tier.perks"
                                    :key="idx"
                                    class="flex items-start gap-2.5 leading-relaxed"
                                >
                                    <div class="mt-0.5 flex size-4.5 shrink-0 items-center justify-center rounded-full bg-emerald-50 text-emerald-600 border border-emerald-200">
                                        <Check class="size-3 stroke-[3]" />
                                    </div>
                                    <span>{{ perk }}</span>
                                </li>
                            </ul>
                        </div>
                    </div>

                    <!-- Bottom Action -->
                    <div class="mt-8 pt-4 border-t border-slate-100">
                        <Link
                            :href="route('catalog')"
                            class="flex w-full items-center justify-center gap-2 rounded-xl border border-slate-200 bg-slate-50 py-2.5 text-xs font-bold text-slate-700 transition hover:border-[#0088ff] hover:bg-[#edf5ff] hover:text-[#0088ff]"
                        >
                            <span>Lihat Trip untuk Tier Ini</span>
                            <ChevronRight class="size-3.5" />
                        </Link>
                    </div>
                </div>
            </div>
        </section>

        <!-- 4 CORE ADVANTAGES -->
        <section class="mx-auto max-w-6xl px-4 py-20 sm:px-6 lg:px-8">
            <div class="text-center">
                <p class="text-xs font-extrabold uppercase tracking-widest text-[#0088ff]">KEISTIMEWAAN EKSKLUSIF</p>
                <h2 class="mt-2 text-2xl font-black text-[#0f172a] sm:text-3xl">
                    Kenapa Member Priority Lebih Diuntungkan?
                </h2>
                <p class="mt-2 text-xs sm:text-sm text-slate-500 max-w-xl mx-auto">
                    Setiap level keanggotaan dirancang untuk menghadirkan pengalaman berlibur yang lebih mulus, hemat, dan tanpa rasa cemas.
                </p>
            </div>

            <div class="mt-12 grid gap-6 sm:grid-cols-2 lg:grid-cols-4">
                <div class="rounded-3xl border border-slate-200/80 bg-white p-6 shadow-xs transition hover:shadow-md">
                    <div class="flex size-12 items-center justify-center rounded-2xl bg-blue-50 text-[#0088ff] mb-4">
                        <Percent class="size-6 stroke-[2.2]" />
                    </div>
                    <h3 class="text-sm font-extrabold text-slate-900">Diskon Otomatis</h3>
                    <p class="mt-2 text-xs leading-relaxed text-slate-500">
                        Potongan harga langsung aktif saat Anda memilih trip tanpa repot mencari dan menyalin kode promo.
                    </p>
                </div>

                <div class="rounded-3xl border border-slate-200/80 bg-white p-6 shadow-xs transition hover:shadow-md">
                    <div class="flex size-12 items-center justify-center rounded-2xl bg-emerald-50 text-emerald-600 mb-4">
                        <Zap class="size-6 stroke-[2.2]" />
                    </div>
                    <h3 class="text-sm font-extrabold text-slate-900">Fast-Track Layanan</h3>
                    <p class="mt-2 text-xs leading-relaxed text-slate-500">
                        Permintaan bantuan, perubahan jadwal, dan verifikasi refund Anda diproses lebih cepat oleh tim spesialis.
                    </p>
                </div>

                <div class="rounded-3xl border border-slate-200/80 bg-white p-6 shadow-xs transition hover:shadow-md">
                    <div class="flex size-12 items-center justify-center rounded-2xl bg-amber-50 text-amber-600 mb-4">
                        <Coins class="size-6 stroke-[2.2]" />
                    </div>
                    <h3 class="text-sm font-extrabold text-slate-900">Multiplier Poin Reward</h3>
                    <p class="mt-2 text-xs leading-relaxed text-slate-500">
                        Raih poin hingga 2x lipat lebih banyak di setiap transaksi untuk ditukar dengan tiket trip gratis selanjutnya.
                    </p>
                </div>

                <div class="rounded-3xl border border-slate-200/80 bg-white p-6 shadow-xs transition hover:shadow-md">
                    <div class="flex size-12 items-center justify-center rounded-2xl bg-purple-50 text-purple-600 mb-4">
                        <Gift class="size-6 stroke-[2.2]" />
                    </div>
                    <h3 class="text-sm font-extrabold text-slate-900">Akses Spesial Mitra</h3>
                    <p class="mt-2 text-xs leading-relaxed text-slate-500">
                        Nikmati penjemputan eksklusif, welcome snack khas daerah, dan potongan harga spesial dari ratusan UMKM mitra.
                    </p>
                </div>
            </div>
        </section>

        <!-- FREQUENTLY ASKED QUESTIONS (FAQ) -->
        <section class="border-t border-slate-200/80 bg-white px-4 py-16 sm:px-6 lg:px-8">
            <div class="mx-auto max-w-3xl">
                <div class="text-center">
                    <p class="text-xs font-extrabold uppercase tracking-widest text-[#0088ff]">TANYA JAWAB</p>
                    <h2 class="mt-2 text-2xl font-black text-slate-900">Pertanyaan Seputar Priority</h2>
                </div>

                <div class="mt-8 space-y-3">
                    <div
                        v-for="(faq, idx) in faqs"
                        :key="idx"
                        class="overflow-hidden rounded-2xl border border-slate-200/90 bg-[#fbfcfe] transition"
                    >
                        <button
                            type="button"
                            class="flex w-full items-center justify-between p-4.5 text-left text-xs sm:text-sm font-bold text-slate-800 transition hover:text-[#0088ff] cursor-pointer"
                            @click="toggleFaq(idx)"
                        >
                            <span>{{ faq.q }}</span>
                            <ChevronDown
                                class="size-4 shrink-0 transition-transform duration-200"
                                :class="faq.open ? 'rotate-180 text-[#0088ff]' : 'text-slate-400'"
                            />
                        </button>
                        <div v-show="faq.open" class="border-t border-slate-100 px-4.5 pb-4 pt-3 text-xs leading-relaxed text-slate-500">
                            {{ faq.a }}
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- BOTTOM CALL TO ACTION -->
        <section class="bg-gradient-to-r from-[#0088ff] to-[#0055b3] px-4 py-14 text-center text-white sm:px-6">
            <div class="mx-auto max-w-2xl">
                <h2 class="text-2xl font-black sm:text-3xl">Siap Jelajahi Keindahan Nusantara?</h2>
                <p class="mt-2 text-xs sm:text-sm text-blue-100">
                    Pesan perjalanan pertamamu hari ini dan nikmati kemudahan serta keistimewaan member Priority.
                </p>
                <div class="mt-6 flex justify-center gap-3">
                    <Link
                        :href="route('catalog')"
                        class="inline-flex items-center gap-2 rounded-full bg-white px-7 py-3 text-xs sm:text-sm font-bold text-[#0066cc] shadow-lg transition hover:bg-sky-50 active:scale-95"
                    >
                        <span>Cari Destinasi Trip</span>
                        <ArrowRight class="size-4" />
                    </Link>
                </div>
            </div>
        </section>

        <!-- FOOTER -->
        <MainFooter />
    </div>
</template>
