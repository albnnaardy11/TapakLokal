<script setup>
import {
    ShieldCheck,
    Coins,
    HeartHandshake,
    Compass,
    CheckCircle2,
    ArrowRight
} from 'lucide-vue-next';

const benefits = [
    {
        icon: ShieldCheck,
        title: '100% Pemandu Akamsi Terverifikasi',
        desc: 'Dipandu oleh warga lokal yang memahami sejarah, adat istiadat, dan jalur rahasia terbaik di setiap sudut daerah.',
    },
    {
        icon: Coins,
        title: 'Transparan Tanpa Biaya Terselubung',
        desc: 'Harga yang Anda bayar adalah harga final. Rincian pengeluaran jelas dan dapat dibagi adil via fitur Solit Bill.',
    },
    {
        icon: HeartHandshake,
        title: 'Dampak Langsung ke Ekonomi Warga',
        desc: 'Lebih dari 80% biaya trip langsung berputar di ekosistem desa: homestay lokal, persewaan perahu nelayan, & warung tradisional.',
    },
    {
        icon: Compass,
        title: 'Pengalaman Otentik Bukan Wisata Massal',
        desc: 'Menikmati kuliner tersembunyi, tradisi kopi sore bersama tetua adat, dan spot alam yang jarang terjamah turis umum.',
    },
];
</script>

<template>
    <section id="mitra-akamsi" class="py-10 sm:py-16 bg-white border-t border-slate-100">
        <div class="max-w-7xl mx-auto px-3 sm:px-6 lg:px-8">
            <div class="bg-gradient-to-br from-slate-900 via-slate-800 to-[#002f7a] rounded-2xl sm:rounded-3xl p-5 sm:p-8 md:p-12 text-white relative overflow-hidden shadow-2xl">
                <!-- Ambient blur rings -->
                <div class="absolute -right-20 -top-20 w-80 h-80 bg-blue-500/20 rounded-full blur-3xl pointer-events-none"></div>
                <div class="absolute -left-20 -bottom-20 w-80 h-80 bg-orange-500/10 rounded-full blur-3xl pointer-events-none"></div>

                <div class="relative z-10 grid grid-cols-1 lg:grid-cols-12 gap-6 sm:gap-10 items-center">
                    <div class="lg:col-span-6">
                        <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-blue-500/20 text-blue-300 text-xs font-bold mb-3 sm:mb-4 border border-blue-400/30">
                            <ShieldCheck class="w-4 h-4 text-blue-400" />
                            <span>Gerakan Petualang Berdaya</span>
                        </div>
                        <h2 class="text-2xl sm:text-3xl md:text-4xl font-extrabold tracking-tight leading-tight">
                            Kenapa Bertualang Lewat <span class="text-blue-400">TapakLokal</span>?
                        </h2>
                        <p class="mt-3 sm:mt-4 text-slate-300 text-xs sm:text-sm md:text-base leading-relaxed">
                            Kami menghubungkan petualang dengan <strong class="text-white">Mitra Akamsi (Anak Kampung Sini)</strong> — komunitas pemandu lokal terverifikasi di pelosok nusantara untuk pariwisata yang jujur, lestari, dan memberdayakan.
                        </p>

                        <div class="mt-5 sm:mt-6 flex flex-col sm:flex-row gap-3 sm:gap-4">
                            <a
                                href="#open-trip"
                                class="bg-[#0052cc] hover:bg-[#0042a3] text-white px-5 sm:px-6 py-2.5 sm:py-3 rounded-xl font-bold text-xs sm:text-sm transition shadow-lg flex items-center justify-center gap-2"
                            >
                                <span>Mulai Eksplorasi</span>
                                <ArrowRight class="w-4 h-4" />
                            </a>
                            <a
                                href="#gabung-akamsi"
                                class="bg-white/10 hover:bg-white/15 text-white border border-white/20 px-5 py-2.5 sm:py-3 rounded-xl font-semibold text-xs sm:text-sm transition flex items-center justify-center"
                            >
                                Jadi Mitra Akamsi
                            </a>
                        </div>
                    </div>


                    <div class="lg:col-span-6 grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div
                            v-for="(item, idx) in benefits"
                            :key="idx"
                            class="bg-white/5 backdrop-blur-xs border border-white/10 rounded-2xl p-4.5 hover:bg-white/10 transition"
                        >
                            <div class="w-10 h-10 rounded-xl bg-blue-500/20 text-blue-300 flex items-center justify-center mb-3">
                                <component :is="item.icon" class="w-5 h-5" />
                            </div>
                            <h3 class="font-bold text-white text-sm leading-snug">
                                {{ item.title }}
                            </h3>
                            <p class="text-xs text-slate-300 mt-1.5 leading-relaxed">
                                {{ item.desc }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</template>
