<script setup>
import { Head, Link } from '@inertiajs/vue3';
import Navbar from '@/Components/Navbar.vue';
import BottomNav from '@/Components/BottomNav.vue';
import Footer from '@/Components/Footer.vue';
import {
    Compass,
    ShieldCheck,
    HeartHandshake,
    Coins,
    Users,
    MapPin,
    Building2,
    Sparkles,
    CheckCircle2,
    ArrowRight
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
        }),
    },
    hubs: {
        type: Array,
        default: () => [],
    },
});

const values = [
    {
        title: 'Ekonomi Berakar di Desa',
        desc: 'Lebih dari 85% biaya trip Anda dialokasikan langsung kepada warga lokal: homestay penduduk, sewa perahu nelayan, porter gunung, dan warung tradisional.',
        icon: Coins,
    },
    {
        title: 'Kejujuran Tanpa Biaya Tersembunyi',
        desc: 'Kami muak dengan skema wisata yang membebani turis dengan biaya masuk mendadak atau komisi toko oleh-oleh paksaan. Di TapakLokal, semua biaya transparan sejak hari pertama.',
        icon: ShieldCheck,
    },
    {
        title: 'Akamsi Adalah Pahlawan Budaya',
        desc: 'Pemandu lokal (Anak Kampung Sini) bukan sekadar penunjuk jalan. Mereka adalah juru kisah, penjaga hutan adat, dan pelindung kelestarian alam nusantara.',
        icon: HeartHandshake,
    },
];
</script>

<template>
    <Head>
        <title>Tentang Kami - Manifesto TapakLokal Indonesia</title>
        <meta name="description" content="Kisah, nilai, dan misi TapakLokal dalam memberdayakan pemandu lokal dan UMKM kuliner di pelosok nusantara." />
    </Head>

    <div class="min-h-screen flex flex-col bg-slate-50 font-sans text-slate-800 antialiased selection:bg-[#0052cc] selection:text-white">
        <Navbar :user="user" active-menu="Tentang Kami" />

        <main class="flex-1">
            <!-- Hero Section Selaras -->
            <section class="relative pt-2 sm:pt-4 md:pt-6 pb-8 sm:pb-12">
                <div class="max-w-7xl mx-auto px-3 sm:px-6 lg:px-8">
                    <div class="relative rounded-2xl sm:rounded-3xl md:rounded-[36px] overflow-hidden min-h-[380px] sm:min-h-[440px] md:min-h-[480px] flex items-center justify-center text-center shadow-2xl">
                        <!-- Hero Image: Traditional culture & village elders in Toraja / Bali -->
                        <div class="absolute inset-0 z-0">
                            <img
                                src="https://images.unsplash.com/photo-1544644181-1484b3fdfc62?auto=format&fit=crop&w=2000&q=85"
                                alt="Tentang TapakLokal"
                                class="w-full h-full object-cover object-center transform scale-105"
                            />
                            <div class="absolute inset-0 bg-gradient-to-b from-black/60 via-black/50 to-black/85"></div>
                            <div class="absolute inset-0 bg-radial-gradient from-transparent via-black/20 to-black/60"></div>
                        </div>

                        <!-- Hero Content -->
                        <div class="relative z-10 max-w-3xl mx-auto px-4 sm:px-6 pt-6 sm:pt-10 pb-20 sm:pb-24 text-white">
                            <div class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-blue-500/25 border border-blue-400/40 text-blue-300 text-xs font-bold mb-4 backdrop-blur-xs">
                                <HeartHandshake class="w-3.5 h-3.5 text-amber-400" />
                                <span>Manifesto Gerakan Kami</span>
                            </div>
                            <h1 class="text-2xl sm:text-4xl md:text-5xl lg:text-6xl font-extrabold tracking-tight leading-[1.2] drop-shadow-md">
                                Menjaga Jiwa Petualang,<br />
                                Menghidupkan Desa.
                            </h1>
                            <p class="mt-3 sm:mt-4 text-xs sm:text-sm md:text-base text-slate-100/90 font-normal leading-relaxed max-w-2xl mx-auto drop-shadow">
                                TapakLokal lahir dari keresahan akan wisata massal yang sering kali meninggalkan sampah dan meminggirkan warga lokal. Kami hadir sebagai jembatan langsung antara petualang sejati dan warga kampung halaman.
                            </p>
                        </div>
                    </div>
                </div>
            </section>

            <!-- 3 Pilar Nilai Utama -->
            <section class="py-12 sm:py-16 bg-white">
                <div class="max-w-7xl mx-auto px-3 sm:px-6 lg:px-8">
                    <div class="text-center max-w-2xl mx-auto mb-10">
                        <span class="text-xs font-bold text-[#0052cc] uppercase tracking-wider">Prinsip Fundamental</span>
                        <h2 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight mt-1">
                            Nilai yang Kami Pegang Teguh
                        </h2>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div
                            v-for="(val, idx) in values"
                            :key="idx"
                            class="p-6 rounded-3xl bg-slate-50 border border-slate-100 hover:shadow-lg transition duration-300 flex flex-col justify-between"
                        >
                            <div>
                                <div class="w-12 h-12 rounded-2xl bg-blue-100 text-[#0052cc] flex items-center justify-center mb-4">
                                    <component :is="val.icon" class="w-6 h-6" />
                                </div>
                                <h3 class="font-bold text-slate-900 text-lg mb-2">{{ val.title }}</h3>
                                <p class="text-xs sm:text-sm text-slate-600 leading-relaxed">{{ val.desc }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Hub Kantor Wilayah Komunitas -->
            <section class="py-12 sm:py-16 bg-slate-50 border-t border-slate-100">
                <div class="max-w-7xl mx-auto px-3 sm:px-6 lg:px-8">
                    <div class="text-center max-w-2xl mx-auto mb-10">
                        <span class="text-xs font-bold text-[#0052cc] uppercase tracking-wider">Jaringan Komunitas</span>
                        <h2 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight mt-1">
                            Kantor & Hub Wilayah TapakLokal
                        </h2>
                        <p class="text-xs sm:text-sm text-slate-500 mt-2">
                            Kami hadir secara fisik di simpul-simpul petualangan nusantara untuk memastikan kualitas dan keselamatan trip.
                        </p>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                        <div
                            v-for="hub in hubs"
                            :key="hub.city"
                            class="bg-white p-5 rounded-2xl border border-gray-200/80 shadow-xs"
                        >
                            <div class="w-10 h-10 rounded-xl bg-blue-50 text-[#0052cc] flex items-center justify-center font-bold mb-3">
                                <Building2 class="w-5 h-5" />
                            </div>
                            <h3 class="font-bold text-slate-900 text-sm">{{ hub.city }}</h3>
                            <p class="text-xs text-slate-500 mt-1">{{ hub.address }}</p>
                            <div class="mt-3 pt-2.5 border-t border-gray-100 text-[11px] font-semibold text-[#0052cc]">
                                {{ hub.lead }}
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <!-- CTA Kemitraan -->
            <section class="py-12 sm:py-16 bg-white border-t border-slate-100">
                <div class="max-w-5xl mx-auto px-4 text-center">
                    <div class="bg-gradient-to-r from-blue-900 to-indigo-900 rounded-3xl p-8 sm:p-12 text-white shadow-xl">
                        <h2 class="text-2xl sm:text-3xl font-extrabold tracking-tight">
                            Ingin Mengembangkan Wisata di Daerahmu?
                        </h2>
                        <p class="mt-3 text-xs sm:text-sm text-slate-300 max-w-xl mx-auto leading-relaxed">
                            Kami membuka kolaborasi seluas-luasnya dengan pemuda lokal, tetua adat, pegiat homestay, dan pengrajin oleh-oleh di seluruh penjuru Indonesia.
                        </p>
                        <div class="mt-6 flex flex-wrap justify-center gap-3">
                            <Link
                                href="/gabung-mitra"
                                class="bg-amber-400 hover:bg-amber-300 text-slate-900 font-extrabold px-6 py-3 rounded-xl text-xs sm:text-sm transition shadow-md"
                            >
                                Daftar Jadi Mitra Akamsi
                            </Link>
                            <Link
                                href="/open-trip"
                                class="bg-white/10 hover:bg-white/20 text-white font-bold px-6 py-3 rounded-xl text-xs sm:text-sm transition border border-white/20"
                            >
                                Jelajahi Open Trip Aktif
                            </Link>
                        </div>
                    </div>
                </div>
            </section>
        </main>

        <Footer />
        <BottomNav active-menu="Tentang Kami" :user="user" />
    </div>
</template>
