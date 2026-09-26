<script setup>
import { Link } from '@inertiajs/vue3';
import { ArrowRight, TrendingUp, Share2, Wallet, CheckCircle2 } from 'lucide-vue-next';

defineProps({
    linkHref: {
        type: String,
        default: '/register',
    },
});
</script>

<template>
    <section class="mx-auto mt-20 max-w-[1180px] sm:mt-24" aria-labelledby="affiliate-banner-heading">
        <div class="relative overflow-hidden rounded-3xl bg-gradient-to-r from-[#0c2f66] via-[#0759bf] to-[#078cff] p-7 sm:p-10 lg:p-12 shadow-[0_16px_44px_rgba(7,100,200,0.18)]">
            <!-- Decorative Background Glows -->
            <div class="pointer-events-none absolute -left-20 -top-20 size-80 rounded-full bg-blue-400/20 blur-3xl" aria-hidden="true"></div>
            <div class="pointer-events-none absolute -right-20 -bottom-20 size-96 rounded-full bg-sky-300/20 blur-3xl" aria-hidden="true"></div>

            <div class="relative z-10 grid items-center gap-8 lg:grid-cols-12 lg:gap-10">
                <!-- Kolom Kiri: Teks & Aksi -->
                <div class="lg:col-span-7">
                    <!-- Judul Utama (Sesuai gambar referensi) -->
                    <h2
                        id="affiliate-banner-heading"
                        class="text-2xl font-extrabold leading-tight tracking-tight text-white sm:text-3xl lg:text-[34px]"
                    >
                        Gabung Jadi Affiliate: Bagikan Keindahan Indonesia, Dapatkan Komisi Menarik
                    </h2>

                    <!-- Deskripsi Pelengkap -->
                    <p class="mt-3.5 max-w-xl text-xs sm:text-sm leading-relaxed text-blue-100/90">
                        Ajak teman, keluarga, dan pengikutmu menjelajahi destinasi otentik bersama TapakLokal. Raih komisi hingga 10% untuk setiap pemesanan trip yang berhasil.
                    </p>

                    <!-- Poin Keunggulan Ringkas -->
                    <div class="mt-5 flex flex-wrap gap-x-6 gap-y-2 text-xs font-medium text-white/90">
                        <span class="inline-flex items-center gap-1.5">
                            <CheckCircle2 class="size-4 text-emerald-300 shrink-0" />
                            Pendaftaran Gratis
                        </span>
                        <span class="inline-flex items-center gap-1.5">
                            <CheckCircle2 class="size-4 text-emerald-300 shrink-0" />
                            Komisi s.d 10%
                        </span>
                        <span class="inline-flex items-center gap-1.5">
                            <CheckCircle2 class="size-4 text-emerald-300 shrink-0" />
                            Pencairan Otomatis
                        </span>
                    </div>

                    <!-- Tombol Pelajari Lebih Lanjut -->
                    <div class="mt-7">
                        <Link
                            :href="linkHref"
                            class="group inline-flex items-center gap-2 rounded-xl bg-white px-6 py-3.5 text-xs sm:text-sm font-bold text-[#0c2f66] shadow-md transition duration-200 hover:bg-sky-50 hover:shadow-lg active:scale-95"
                        >
                            <span>Pelajari Lebih Lanjut</span>
                            <ArrowRight class="size-4 text-[#078cff] transition-transform duration-200 group-hover:translate-x-1" />
                        </Link>
                    </div>
                </div>

                <!-- Kolom Kanan: Mockup Tablet / iPad Showcase -->
                <div class="flex justify-center lg:col-span-5 lg:justify-end">
                    <div class="relative w-full max-w-[420px] transition-transform duration-300 hover:scale-[1.02]">
                        <!-- Tablet Frame Outer Bezel -->
                        <div class="relative overflow-hidden rounded-[26px] border-[6px] border-[#0f172a] bg-[#0f172a] shadow-[0_20px_40px_rgba(0,0,0,0.35)]">
                            <!-- Camera Notch / Dot -->
                            <div class="absolute top-2 left-1/2 -translate-x-1/2 z-20 size-1.5 rounded-full bg-slate-700"></div>

                            <!-- Screen Container -->
                            <div class="relative overflow-hidden rounded-[20px] bg-[#f8fafc] text-slate-800 p-3.5 sm:p-4 text-left select-none">
                                <!-- App Header Inside Tablet -->
                                <div class="flex items-center justify-between border-b border-slate-100 pb-2.5">
                                    <div class="flex items-center gap-2">
                                        <div class="flex size-6 items-center justify-center rounded-lg bg-[#078cff] text-[10px] font-bold text-white">
                                            TL
                                        </div>
                                        <span class="text-xs font-bold text-slate-800">TapakLokal Affiliate</span>
                                    </div>
                                    <span class="rounded-full bg-emerald-50 px-2 py-0.5 text-[9px] font-bold text-emerald-600 border border-emerald-200">
                                        ● Aktif
                                    </span>
                                </div>

                                <!-- Commission Summary Card -->
                                <div class="mt-2.5 rounded-xl bg-gradient-to-br from-[#0c2f66] to-[#078cff] p-3 text-white shadow-sm">
                                    <div class="flex items-center justify-between text-[10px] text-blue-100">
                                        <span class="inline-flex items-center gap-1">
                                            <Wallet class="size-3" /> Total Penghasilan
                                        </span>
                                        <span class="inline-flex items-center gap-0.5 text-emerald-300 font-semibold text-[9px]">
                                            <TrendingUp class="size-2.5" /> +24% bln ini
                                        </span>
                                    </div>
                                    <div class="mt-1 text-base font-extrabold sm:text-lg">
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
                                        <span class="text-[9px] text-[#078cff]">Lihat Semua</span>
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
                                                <div class="text-[9px] text-emerald-600 font-medium">Komisi: Rp 120.000 / pax</div>
                                            </div>
                                        </div>
                                        <button type="button" class="inline-flex shrink-0 items-center gap-1 rounded-md bg-[#edf5ff] px-2 py-1 text-[9px] font-semibold text-[#078cff]">
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
                                                <div class="text-[9px] text-emerald-600 font-medium">Komisi: Rp 85.000 / pax</div>
                                            </div>
                                        </div>
                                        <button type="button" class="inline-flex shrink-0 items-center gap-1 rounded-md bg-[#edf5ff] px-2 py-1 text-[9px] font-semibold text-[#078cff]">
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
