<script setup>
import { Link } from '@inertiajs/vue3';
import { Compass, Mail, Phone, MapPin, Heart } from 'lucide-vue-next';
</script>

<template>
    <footer class="bg-slate-900 text-slate-300 pt-10 sm:pt-14 pb-24 md:pb-10 border-t border-slate-800">
        <div class="max-w-7xl mx-auto px-3 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-12 gap-8 mb-10 sm:mb-12">

                <!-- Brand Bio -->
                <div class="lg:col-span-4 space-y-4">
                    <Link href="/" class="flex items-center gap-2.5 group inline-flex">
                        <div class="w-9 h-9 rounded-xl bg-[#0052cc] flex items-center justify-center text-white shadow-xs group-hover:scale-105 transition">
                            <Compass class="w-5 h-5" />
                        </div>
                        <span class="tracking-tight text-2xl font-black">
                            <span class="text-white font-extrabold">TAPAK</span>
                            <span class="text-blue-400 font-black">LOKAL</span>
                        </span>
                    </Link>

                    <p class="text-xs sm:text-sm text-slate-400 leading-relaxed max-w-sm">
                        Platform open trip & titip kuliner nusantara berbasis pemberdayaan warga lokal (Mitra Akamsi). Otentik, jujur, tanpa biaya tersembunyi.
                    </p>

                    <div class="pt-2 text-xs text-slate-400 space-y-1.5">
                        <div class="flex items-center gap-2">
                            <MapPin class="w-4 h-4 text-blue-400 shrink-0" />
                            <span>Jl. Danau Beratan No. 11, Denpasar & Hub Jakarta Selatan</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <Mail class="w-4 h-4 text-blue-400 shrink-0" />
                            <span>halo@tapaklokal.id</span>
                        </div>
                    </div>
                </div>

                <!-- Kolom 2: Eksplorasi -->
                <div class="lg:col-span-2 space-y-3">
                    <h4 class="text-xs font-bold text-white uppercase tracking-wider">Eksplorasi</h4>
                    <ul class="space-y-2 text-xs">
                        <li><Link href="/open-trip" class="hover:text-white transition">Open Trip Populer</Link></li>
                        <li><Link href="/open-po" class="hover:text-white transition">Open PO Kuliner Asli</Link></li>
                        <li><Link href="/tentang-kami" class="hover:text-white transition">Tentang Kami</Link></li>
                        <li><Link href="/gabung-mitra" class="hover:text-white transition">Direktori Akamsi</Link></li>
                        <li><Link href="/blog" class="hover:text-white transition">Catatan Petualang</Link></li>
                    </ul>
                </div>

                <!-- Kolom 3: Fitur & Komunitas -->
                <div class="lg:col-span-3 space-y-3">
                    <h4 class="text-xs font-bold text-white uppercase tracking-wider">Fitur & Komunitas</h4>
                    <ul class="space-y-2 text-xs">
                        <li><Link href="/member-tiers" class="hover:text-white transition">Member Tiers & Poin</Link></li>
                        <li><Link href="/wishlist" class="hover:text-white transition">Wishlist Destinasi</Link></li>
                        <li><Link href="/gabung-mitra" class="hover:text-white transition font-semibold text-amber-400">Gabung Mitra Akamsi</Link></li>
                        <li><Link href="/gabung-mitra" class="hover:text-white transition font-semibold text-orange-400">Daftar Merchant UMKM</Link></li>
                    </ul>
                </div>


                <!-- Kolom 4: Pembayaran & Keamanan -->
                <div class="lg:col-span-3 space-y-3">
                    <h4 class="text-xs font-bold text-white uppercase tracking-wider">Metode Pembayaran Aman</h4>
                    <p class="text-xs text-slate-400 leading-relaxed">
                        Mendukung pembayaran instan via QRIS, Transfer Bank Nasional, & E-Wallet dengan enkripsi terproteksi.
                    </p>
                    <div class="flex flex-wrap gap-2 pt-2">
                        <span class="px-2.5 py-1 bg-slate-800 border border-slate-700 rounded-md text-[11px] font-semibold text-white">QRIS</span>
                        <span class="px-2.5 py-1 bg-slate-800 border border-slate-700 rounded-md text-[11px] font-semibold text-white">BCA</span>
                        <span class="px-2.5 py-1 bg-slate-800 border border-slate-700 rounded-md text-[11px] font-semibold text-white">Mandiri</span>
                        <span class="px-2.5 py-1 bg-slate-800 border border-slate-700 rounded-md text-[11px] font-semibold text-white">GoPay</span>
                        <span class="px-2.5 py-1 bg-slate-800 border border-slate-700 rounded-md text-[11px] font-semibold text-white">ShopeePay</span>
                    </div>
                </div>
            </div>

            <!-- Bottom Copyright -->
            <div class="pt-8 border-t border-slate-800 text-xs text-slate-500 flex flex-col sm:flex-row items-center justify-between gap-4">
                <p>&copy; {{ new Date().getFullYear() }} TapakLokal Indonesia. Hak Cipta Dilindungi.</p>
                <div class="flex items-center gap-1">
                    <span>Dibuat dengan</span>
                    <Heart class="w-3.5 h-3.5 text-red-500 fill-red-500 inline" />
                    <span>untuk pemberdayaan komunitas lokal nusantara.</span>
                </div>
            </div>
        </div>
    </footer>
</template>
