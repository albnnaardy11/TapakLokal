<script setup>
import { ref } from 'vue';
import { Head, Link } from '@inertiajs/vue3';
import Navbar from '@/Components/Navbar.vue';
import BottomNav from '@/Components/BottomNav.vue';
import Footer from '@/Components/Footer.vue';
import {
    Crown,
    Sparkles,
    CheckCircle2,
    Gift,
    Award,
    ArrowRight,
    Coins,
    ShieldCheck,
    Zap,
    X
} from 'lucide-vue-next';

const props = defineProps({
    appName: {
        type: String,
        default: 'TapakLokal',
    },
    user: {
        type: Object,
        default: () => ({
            name: 'albnnaardy',
            handle: '@petualangnyasar',
            points: 0,
            tier: 'Petualang Perintis',
        }),
    },
});

const toastMessage = ref(null);

function showToast(msg) {
    toastMessage.value = msg;
    setTimeout(() => {
        toastMessage.value = null;
    }, 3500);
}

const tiers = [
    {
        name: 'Petualang Perintis',
        badge: 'Level 1',
        points: '0 - 499 Poin',
        color: 'border-slate-200 bg-white text-slate-800',
        badgeColor: 'bg-slate-100 text-slate-700',
        perks: [
            '1x Point Multiplier setiap transaksi',
            'Diskon 3% untuk paket Open Trip pertama',
            'Akses komunitas WhatsApp Petualang Nusantara',
            'Sertifikat digital penjelajah lokal',
        ],
    },
    {
        name: 'Petualang Penjelajah',
        badge: 'Level 2 • Populer',
        points: '500 - 1.999 Poin',
        color: 'border-blue-300 bg-gradient-to-b from-blue-50/50 to-white shadow-md text-slate-900',
        badgeColor: 'bg-[#0052cc] text-white',
        highlight: true,
        perks: [
            '1.5x Point Multiplier setiap transaksi',
            'Diskon 7% untuk semua paket Open Trip & Open PO',
            'Prioritas pemesanan kuota saat slot tersisa sedikit',
            'Gratis ongkir 1x sebulan untuk Titip PO Paxel',
            'Customer support jalur cepat 24/7',
        ],
    },
    {
        name: 'Pendekar Nusantara',
        badge: 'Level VIP',
        points: '2.000+ Poin',
        color: 'border-amber-300 bg-gradient-to-b from-amber-50/50 to-white shadow-lg text-slate-900',
        badgeColor: 'bg-amber-400 text-slate-950 font-black',
        perks: [
            '2x Point Multiplier dobel poin setiap trip',
            'Diskon 12% tanpa batas maksimal kuota',
            'Akses jalur eksklusif rute belum terpublikasi',
            'Paket merchandise Kaos & Buff Akamsi Eksklusif',
            'Undangan gathering tahunan bersama tetua adat',
        ],
    },
];

const rewards = [
    {
        id: 1,
        title: 'Voucher Potongan Rp 50.000 Open Trip',
        cost: 250,
        type: 'Voucher Diskon',
    },
    {
        id: 2,
        title: 'Gratis Ongkir Titip PO Paxel Seluruh Jawa & Bali',
        cost: 150,
        type: 'Gratis Ongkir',
    },
    {
        id: 3,
        title: 'Merchandise Resmi Buff Petualang TapakLokal',
        cost: 400,
        type: 'Fisik',
    },
];

function redeemReward(reward) {
    if (props.user.points < reward.cost) {
        showToast(`Poin Anda (${props.user.points} pt) belum mencukupi untuk menukar "${reward.title}". Ikuti open trip untuk mengumpulkan poin!`);
    } else {
        showToast(`Berhasil menukarkan "${reward.title}"! Kode voucher telah dikirimkan ke WhatsApp Anda.`);
    }
}
</script>

<template>
    <Head>
        <title>Member Tiers & Loyalty Points - TapakLokal</title>
    </Head>

    <div class="min-h-screen flex flex-col bg-slate-50 font-sans text-slate-800 antialiased selection:bg-[#0052cc] selection:text-white">
        <Navbar :user="user" active-menu="Member Tiers" />

        <main class="flex-1">
            <!-- Hero Section Selaras -->
            <section class="relative pt-2 sm:pt-4 md:pt-6 pb-8 sm:pb-12">
                <div class="max-w-7xl mx-auto px-3 sm:px-6 lg:px-8">
                    <div class="relative rounded-2xl sm:rounded-3xl md:rounded-[36px] overflow-hidden min-h-[380px] sm:min-h-[440px] flex items-center justify-center text-center shadow-2xl">
                        <div class="absolute inset-0 z-0">
                            <img
                                src="https://images.unsplash.com/photo-1516690561799-46d8f74f9abf?auto=format&fit=crop&w=2000&q=85"
                                alt="Member Tiers TapakLokal"
                                class="w-full h-full object-cover object-center transform scale-105"
                            />
                            <div class="absolute inset-0 bg-gradient-to-b from-black/60 via-black/50 to-black/85"></div>
                            <div class="absolute inset-0 bg-radial-gradient from-transparent via-black/20 to-black/60"></div>
                        </div>

                        <div class="relative z-10 max-w-3xl mx-auto px-4 sm:px-6 pt-6 pb-20 text-white">
                            <div class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-amber-500/25 border border-amber-400/40 text-amber-300 text-xs font-bold mb-4 backdrop-blur-xs">
                                <Crown class="w-3.5 h-3.5 text-amber-400" />
                                <span>Program Loyalitas Petualang</span>
                            </div>
                            <h1 class="text-2xl sm:text-4xl md:text-5xl lg:text-6xl font-extrabold tracking-tight leading-[1.2] drop-shadow-md">
                                Tingkatkan Levelmu,<br />
                                Jelajahi Lebih Dekat.
                            </h1>
                            <p class="mt-3 sm:mt-4 text-xs sm:text-sm md:text-base text-slate-100/90 font-normal leading-relaxed max-w-2xl mx-auto drop-shadow">
                                Setiap kilometer perjalanan dan setiap titip oleh-oleh menambah poinmu. Nikmati potongan harga langsung, kuota prioritas, dan reward eksklusif.
                            </p>
                        </div>
                    </div>

                    <!-- Floating User Status Card -->
                    <div class="max-w-3xl mx-auto -mt-16 sm:-mt-20 relative z-20 px-1 sm:px-4">
                        <div class="bg-white rounded-2xl sm:rounded-3xl shadow-2xl border border-gray-100 p-5 sm:p-6 backdrop-blur-xs flex flex-col sm:flex-row items-center justify-between gap-4">
                            <div class="flex items-center gap-3.5 text-left w-full sm:w-auto">
                                <div class="w-12 h-12 rounded-2xl bg-blue-100 text-[#0052cc] border-2 border-[#0052cc] flex items-center justify-center font-black text-base shadow-xs">
                                    {{ user.name ? user.name.charAt(0).toUpperCase() : 'A' }}
                                </div>
                                <div>
                                    <div class="flex items-center gap-2">
                                        <h3 class="font-extrabold text-slate-900 text-base">{{ user.name }}</h3>
                                        <span class="text-[10px] bg-blue-50 text-[#0052cc] border border-blue-200 px-2 py-0.5 rounded-md font-bold">
                                            {{ user.tier }}
                                        </span>
                                    </div>
                                    <p class="text-xs text-slate-500">{{ user.handle }} • Petualang Aktif</p>
                                </div>
                            </div>

                            <div class="flex items-center justify-between sm:justify-end gap-6 w-full sm:w-auto pt-3 sm:pt-0 border-t sm:border-t-0 border-gray-100">
                                <div class="text-left sm:text-right">
                                    <span class="text-[11px] text-slate-400 block font-medium">Saldo Poin Saat Ini</span>
                                    <span class="text-2xl font-black text-[#0052cc]">{{ user.points }} Poin</span>
                                </div>
                                <Link
                                    href="/open-trip"
                                    class="bg-[#0052cc] hover:bg-[#003da6] text-white px-4 py-2 rounded-xl text-xs font-bold transition shadow-xs whitespace-nowrap"
                                >
                                    Kumpulkan Poin
                                </Link>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Tiers Grid -->
            <section class="py-12 sm:py-16 bg-white">
                <div class="max-w-7xl mx-auto px-3 sm:px-6 lg:px-8">
                    <div class="text-center max-w-2xl mx-auto mb-12">
                        <span class="text-xs font-bold text-[#0052cc] uppercase tracking-wider">Tingkatan Keanggotaan</span>
                        <h2 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight mt-1">
                            Pilih Level Petualanganmu
                        </h2>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div
                            v-for="t in tiers"
                            :key="t.name"
                            :class="[
                                'rounded-3xl p-6 border transition-all duration-300 flex flex-col justify-between relative',
                                t.color,
                                t.highlight ? 'ring-2 ring-[#0052cc]' : ''
                            ]"
                        >
                            <span v-if="t.highlight" class="absolute -top-3 left-1/2 -translate-x-1/2 bg-[#0052cc] text-white text-[10px] font-extrabold px-3 py-0.5 rounded-full uppercase tracking-wider shadow-xs">
                                Pilihan Utama
                            </span>

                            <div>
                                <div class="flex items-center justify-between mb-2">
                                    <span :class="['text-[11px] font-bold px-2.5 py-1 rounded-full', t.badgeColor]">
                                        {{ t.badge }}
                                    </span>
                                    <span class="text-xs font-bold text-slate-500">{{ t.points }}</span>
                                </div>

                                <h3 class="text-xl font-black text-slate-900 mt-2">{{ t.name }}</h3>

                                <ul class="mt-6 space-y-3">
                                    <li
                                        v-for="(perk, pIdx) in t.perks"
                                        :key="pIdx"
                                        class="flex items-start gap-2.5 text-xs text-slate-700"
                                    >
                                        <CheckCircle2 class="w-4 h-4 text-emerald-600 shrink-0 mt-0.5" />
                                        <span>{{ perk }}</span>
                                    </li>
                                </ul>
                            </div>

                            <div class="mt-8 pt-4 border-t border-gray-200/80">
                                <Link
                                    href="/open-trip"
                                    class="block w-full text-center py-2.5 rounded-xl text-xs font-bold transition bg-slate-900 text-white hover:bg-slate-800 shadow-xs"
                                >
                                    Eksplorasi Trip Untuk Level Ini
                                </Link>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Rewards Redemption Section -->
            <section class="py-12 sm:py-16 bg-slate-50 border-t border-slate-100">
                <div class="max-w-5xl mx-auto px-3 sm:px-6 lg:px-8">
                    <div class="flex items-center justify-between mb-8">
                        <div>
                            <h2 class="text-xl sm:text-2xl font-extrabold text-slate-900">
                                Katalog Penukaran Poin
                            </h2>
                            <p class="text-xs sm:text-sm text-slate-500 mt-0.5">
                                Tukarkan poin reward-mu dengan voucher potongan harga & merchandise
                            </p>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                        <div
                            v-for="r in rewards"
                            :key="r.id"
                            class="p-5 rounded-2xl bg-white border border-gray-200/80 shadow-xs flex flex-col justify-between"
                        >
                            <div>
                                <span class="text-[10px] font-bold text-[#0052cc] bg-blue-50 px-2 py-0.5 rounded-md uppercase">
                                    {{ r.type }}
                                </span>
                                <h4 class="font-bold text-slate-900 text-sm mt-2 leading-snug">{{ r.title }}</h4>
                            </div>

                            <div class="mt-6 pt-3 border-t border-gray-100 flex items-center justify-between">
                                <span class="text-sm font-black text-amber-600">{{ r.cost }} Poin</span>
                                <button
                                    type="button"
                                    @click="redeemReward(r)"
                                    class="bg-slate-900 hover:bg-[#0052cc] text-white px-3 py-1.5 rounded-xl text-xs font-bold transition cursor-pointer"
                                >
                                    Tukar Poin
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </main>

        <Footer />
        <BottomNav active-menu="Member Tiers" :user="user" />

        <!-- Notification Toast -->
        <div
            v-if="toastMessage"
            class="fixed bottom-20 sm:bottom-6 left-4 right-4 sm:left-auto sm:right-6 z-50 bg-slate-900/95 text-white px-4 py-3 rounded-2xl shadow-2xl border border-slate-700 flex items-center justify-between sm:justify-start gap-3 backdrop-blur-md"
        >
            <div class="flex items-center gap-2.5">
                <Sparkles class="w-5 h-5 text-amber-400 shrink-0" />
                <span class="text-xs sm:text-sm font-medium">{{ toastMessage }}</span>
            </div>
            <button type="button" @click="toastMessage = null" class="text-slate-400 hover:text-white p-1">
                <X class="w-4 h-4" />
            </button>
        </div>
    </div>
</template>
