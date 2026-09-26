<script setup>
import { Link } from '@inertiajs/vue3';
import { ArrowRight, TrendingUp, Share2, Wallet, CheckCircle2, Sparkles } from 'lucide-vue-next';

defineProps({
    linkHref: {
        type: String,
        default: '/register',
    },
});
</script>

<template>
    <section class="mx-auto mt-20 max-w-[1180px] sm:mt-24 px-4 sm:px-6 lg:px-0" aria-labelledby="affiliate-banner-heading">
        <!-- Container dengan Traveloka Signature Mesh Gradient & Luxury Glass Border -->
        <div class="traveloka-gradient relative overflow-hidden rounded-3xl border border-white/20 border-t-white/40 p-7 sm:p-10 lg:p-12 shadow-[0_20px_50px_rgba(1,117,234,0.25)]">
            <!-- Travel Vector Contour Lines (Tekstur Gelombang Kontur & Flight Path) -->
            <svg
                class="pointer-events-none absolute inset-0 size-full stroke-white/[0.09] [mask-image:radial-gradient(ellipse_at_center,white,transparent_80%)]"
                viewBox="0 0 1180 380"
                fill="none"
                xmlns="http://www.w3.org/2000/svg"
                preserveAspectRatio="none"
                aria-hidden="true"
            >
                <path d="M-80 90C160 40 320 220 580 150C840 80 1020 260 1260 180" stroke-width="1.8" stroke-dasharray="6 6" />
                <path d="M-60 170C200 110 380 310 640 230C900 150 1080 340 1280 270" stroke-width="1.5" />
                <path d="M-40 250C220 200 440 360 700 300C960 240 1120 380 1300 330" stroke-width="1.2" stroke-dasharray="4 4" />
                
                <!-- Destination Waypoint Dots -->
                <circle cx="580" cy="150" r="4.5" fill="white" fill-opacity="0.35" />
                <circle cx="640" cy="230" r="5" fill="white" fill-opacity="0.3" />
                <circle cx="1020" cy="260" r="4" fill="white" fill-opacity="0.25" />
            </svg>

            <!-- Decorative Twinkle Stars / Sparkles di Background -->
            <svg class="pointer-events-none absolute top-8 left-1/3 size-6 text-white/30 animate-pulse" viewBox="0 0 24 24" fill="currentColor">
                <path d="M12 0L14.5 9.5L24 12L14.5 14.5L12 24L9.5 14.5L0 12L9.5 9.5L12 0Z"/>
            </svg>
            <svg class="pointer-events-none absolute bottom-12 left-1/4 size-4 text-white/20" viewBox="0 0 24 24" fill="currentColor">
                <path d="M12 0L14.5 9.5L24 12L14.5 14.5L12 24L9.5 14.5L0 12L9.5 9.5L12 0Z"/>
            </svg>
            <svg class="pointer-events-none absolute top-12 right-12 size-5 text-cyan-200/35 animate-pulse" viewBox="0 0 24 24" fill="currentColor">
                <path d="M12 0L14.5 9.5L24 12L14.5 14.5L12 24L9.5 14.5L0 12L9.5 9.5L12 0Z"/>
            </svg>

            <!-- Multi-Point Ambient Lighting (Ciri Khas Depth Visual Platform Traveloka) -->
            <div class="pointer-events-none absolute -left-20 -top-20 size-80 rounded-full bg-white/15 blur-3xl" aria-hidden="true"></div>
            <div class="pointer-events-none absolute right-10 bottom-0 size-96 rounded-full bg-[#00d2ff]/25 blur-3xl" aria-hidden="true"></div>
            <div class="pointer-events-none absolute -left-10 -bottom-16 size-72 rounded-full bg-amber-400/15 blur-3xl" aria-hidden="true"></div>

            <div class="relative z-10 grid items-center gap-8 lg:grid-cols-12 lg:gap-10">
                <!-- Kolom Kiri: Teks & Aksi -->
                <div class="lg:col-span-7">
                    <!-- Judul Utama -->
                    <h2
                        id="affiliate-banner-heading"
                        class="text-2xl font-extrabold leading-tight tracking-tight text-white sm:text-3xl lg:text-[35px]"
                    >
                        Gabung Jadi Affiliate: Bagikan Keindahan Indonesia, Dapatkan Komisi Menarik
                    </h2>

                    <!-- Deskripsi Pelengkap -->
                    <p class="mt-3.5 max-w-xl text-xs sm:text-sm leading-relaxed text-blue-50/95">
                        Ajak teman, keluarga, dan pengikutmu menjelajahi destinasi otentik bersama TapakLokal. Raih komisi hingga 10% untuk setiap pemesanan trip yang berhasil.
                    </p>

                    <!-- Poin Keunggulan Ringkas -->
                    <div class="mt-5 flex flex-wrap gap-x-6 gap-y-2 text-xs font-semibold text-white/95">
                        <span class="inline-flex items-center gap-1.5 drop-shadow-sm">
                            <CheckCircle2 class="size-4 text-[#4ade80] shrink-0" />
                            Pendaftaran Gratis
                        </span>
                        <span class="inline-flex items-center gap-1.5 drop-shadow-sm">
                            <CheckCircle2 class="size-4 text-[#4ade80] shrink-0" />
                            Komisi s.d 10%
                        </span>
                        <span class="inline-flex items-center gap-1.5 drop-shadow-sm">
                            <CheckCircle2 class="size-4 text-[#4ade80] shrink-0" />
                            Pencairan Otomatis
                        </span>
                    </div>

                    <!-- Tombol Pelajari Lebih Lanjut (Traveloka Clean Button Style) -->
                    <div class="mt-7">
                        <Link
                            :href="linkHref"
                            class="group inline-flex items-center gap-2 rounded-xl bg-white px-6 py-3.5 text-xs sm:text-sm font-bold text-[#0256af] shadow-[0_8px_20px_rgba(2,30,85,0.22)] transition-all duration-200 hover:bg-[#f0f7ff] hover:shadow-[0_12px_28px_rgba(2,30,85,0.28)] hover:scale-[1.01] active:scale-95"
                        >
                            <span>Pelajari Lebih Lanjut</span>
                            <ArrowRight class="size-4 text-[#0175ea] transition-transform duration-200 group-hover:translate-x-1" />
                        </Link>
                    </div>
                </div>

                <!-- Kolom Kanan: Mockup Tablet / iPad Showcase dengan Floating Badges -->
                <div class="flex justify-center lg:col-span-5 lg:justify-end">
                    <div class="relative w-full max-w-[420px] transition-transform duration-300 hover:scale-[1.02]">
                        <!-- Floating Badge 1: Notifikasi Pencairan Komisi (Kiri Atas) -->
                        <div class="pointer-events-none absolute -top-3.5 -left-4 z-20 hidden sm:flex items-center gap-2.5 rounded-xl border border-white/80 bg-white/95 px-3 py-2 shadow-[0_12px_24px_rgba(0,30,80,0.2)] backdrop-blur-md">
                            <div class="flex size-7 shrink-0 items-center justify-center rounded-lg bg-emerald-500 text-white shadow-xs">
                                <CheckCircle2 class="size-4" />
                            </div>
                            <div class="pr-1 text-left">
                                <p class="text-[10px] font-extrabold text-slate-800 leading-none">Komisi Baru!</p>
                                <p class="mt-1 text-[9px] font-bold text-emerald-600 leading-none">+Rp 250.000 Masuk</p>
                            </div>
                        </div>

                        <!-- Floating Badge 2: Link Referral (Kanan Bawah) -->
                        <div class="pointer-events-none absolute -bottom-3 -right-3 z-20 hidden sm:flex items-center gap-2.5 rounded-xl border border-white/80 bg-white/95 px-3 py-2 shadow-[0_12px_24px_rgba(0,30,80,0.2)] backdrop-blur-md">
                            <div class="flex size-7 shrink-0 items-center justify-center rounded-lg bg-[#0175ea] text-white shadow-xs">
                                <Share2 class="size-3.5 text-white" />
                            </div>
                            <div class="pr-1 text-left">
                                <p class="text-[10px] font-extrabold text-slate-800 leading-none">Link Referral</p>
                                <p class="mt-1 text-[9px] font-bold text-[#0175ea] leading-none">Siap Dibagikan</p>
                            </div>
                        </div>

                        <!-- Tablet Frame Outer Bezel -->
                        <div class="relative overflow-hidden rounded-[26px] border-[6px] border-[#0a1122] bg-[#0a1122] shadow-[0_25px_50px_-12px_rgba(0,12,38,0.55)]">
                            <!-- Camera Notch / Dot -->
                            <div class="absolute top-2 left-1/2 -translate-x-1/2 z-20 size-1.5 rounded-full bg-slate-700"></div>

                            <!-- Screen Container -->
                            <div class="relative overflow-hidden rounded-[20px] bg-[#f8fafc] text-slate-800 p-3.5 sm:p-4 text-left select-none">
                                <!-- App Header Inside Tablet -->
                                <div class="flex items-center justify-between border-b border-slate-100 pb-2.5">
                                    <div class="flex items-center gap-2">
                                        <div class="flex size-6 items-center justify-center rounded-lg bg-[#0175ea] text-[10px] font-bold text-white shadow-xs">
                                            TL
                                        </div>
                                        <span class="text-xs font-bold text-slate-800">TapakLokal Affiliate</span>
                                    </div>
                                    <span class="rounded-full bg-emerald-50 px-2 py-0.5 text-[9px] font-bold text-emerald-600 border border-emerald-200">
                                        ● Aktif
                                    </span>
                                </div>

                                <!-- Commission Summary Card (Traveloka-style Card) -->
                                <div class="mt-2.5 rounded-xl bg-gradient-to-br from-[#02438e] via-[#016bd8] to-[#018cf2] p-3 text-white shadow-sm">
                                    <div class="flex items-center justify-between text-[10px] text-blue-100">
                                        <span class="inline-flex items-center gap-1">
                                            <Wallet class="size-3" /> Total Penghasilan
                                        </span>
                                        <span class="inline-flex items-center gap-0.5 text-emerald-300 font-semibold text-[9px]">
                                            <TrendingUp class="size-2.5" /> +24% bln ini
                                        </span>
                                    </div>
                                    <div class="mt-1 text-base font-extrabold sm:text-lg tracking-tight">
                                        Rp 4.850.000
                                    </div>
                                    <div class="mt-1 flex items-center justify-between text-[9px] text-sky-100/90 pt-1 border-t border-white/15">
                                        <span>18 Pesanan Berhasil</span>
                                        <span class="font-semibold text-white">Siap Tarik</span>
                                    </div>
                                </div>

                                <!-- Popular Trips Ready To Share -->
                                <div class="mt-3">
                                    <div class="flex items-center justify-between text-[10px] font-semibold text-slate-500 mb-1.5">
                                        <span>Trip Populer Dibagikan</span>
                                        <span class="text-[9px] text-[#0175ea] font-medium">Lihat Semua</span>
                                    </div>

                                    <!-- Trip Item 1 -->
                                    <div class="flex items-center justify-between gap-2 rounded-lg border border-slate-100 bg-white p-2 shadow-xs mb-1.5">
                                        <div class="flex items-center gap-2 min-w-0">
                                            <img
                                                src="https://images.unsplash.com/photo-1518548419970-58e3b4079ab2?auto=format&fit=crop&w=120&q=80"
                                                alt="Pulau Komodo"
                                                class="size-8 rounded-md object-cover shrink-0"
                                            />
                                            <div class="min-w-0">
                                                <div class="truncate text-[10px] font-bold text-slate-800">Open Trip Komodo</div>
                                                <div class="text-[9px] text-emerald-600 font-semibold">Komisi: Rp 120.000 / pax</div>
                                            </div>
                                        </div>
                                        <button type="button" class="inline-flex shrink-0 items-center gap-1 rounded-md bg-[#edf5ff] px-2 py-1 text-[9px] font-semibold text-[#0175ea]">
                                            <Share2 class="size-2.5" />
                                            <span>Bagikan</span>
                                        </button>
                                    </div>

                                    <!-- Trip Item 2 -->
                                    <div class="flex items-center justify-between gap-2 rounded-lg border border-slate-100 bg-white p-2 shadow-xs">
                                        <div class="flex items-center gap-2 min-w-0">
                                            <img
                                                src="https://images.unsplash.com/photo-1501179691627-eeaa65ea017c?auto=format&fit=crop&w=120&q=80"
                                                alt="Bromo Sunrise"
                                                class="size-8 rounded-md object-cover shrink-0"
                                            />
                                            <div class="min-w-0">
                                                <div class="truncate text-[10px] font-bold text-slate-800">Bromo Sunrise Tour</div>
                                                <div class="text-[9px] text-emerald-600 font-semibold">Komisi: Rp 85.000 / pax</div>
                                            </div>
                                        </div>
                                        <button type="button" class="inline-flex shrink-0 items-center gap-1 rounded-md bg-[#edf5ff] px-2 py-1 text-[9px] font-semibold text-[#0175ea]">
                                            <Share2 class="size-2.5" />
                                            <span>Bagikan</span>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</template>

<style scoped>
.traveloka-gradient {
    /* Traveloka Premium Multi-Stop Oceanic Mesh */
    background:
        radial-gradient(110% 110% at 92% 95%, rgba(0, 210, 255, 0.35) 0%, transparent 55%),
        radial-gradient(85% 85% at 10% 12%, rgba(255, 255, 255, 0.14) 0%, transparent 45%),
        radial-gradient(70% 70% at 4% 96%, rgba(245, 158, 11, 0.16) 0%, transparent 40%),
        linear-gradient(130deg, #022353 0%, #03418f 28%, #016ad7 62%, #0087f0 100%);
}
</style>
