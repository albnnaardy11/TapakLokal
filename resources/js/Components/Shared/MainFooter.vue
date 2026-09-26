<script setup>
import { ref } from 'vue';
import { Link } from '@inertiajs/vue3';
import { route } from 'ziggy-js';
import { Mail, CheckCircle, Handshake } from 'lucide-vue-next';

const email = ref('');
const isSubscribed = ref(false);

const handleSubscribe = () => {
    if (!email.value || !email.value.includes('@')) return;
    isSubscribed.value = true;
    email.value = '';
    setTimeout(() => {
        isSubscribed.value = false;
    }, 4000);
};

function safeRoute(name, params) {
    try {
        if (typeof route === 'function') {
            return params !== undefined ? route(name, params) : route(name);
        }
        return '#';
    } catch {
        return '#';
    }
}
</script>

<template>
    <footer class="w-full font-sans">
        <!-- Top Sky-Blue Newsletter Bar (1:1 with Traveloka Design) -->
        <div class="w-full bg-[#0088ff] text-white">
            <div class="mx-auto flex max-w-[1180px] flex-col items-center justify-between gap-4 px-4 py-5 sm:px-6 md:flex-row lg:px-0">
                <!-- Left Title / Pitch (2 Baris, Lebih Besar, Bahasa Indonesia) -->
                <h3 class="text-center text-base sm:text-lg lg:text-[19px] font-extrabold leading-snug tracking-tight text-white md:text-left">
                    Selalu dapatkan tips wisata & rekomendasi destinasi,<br class="hidden sm:inline" />
                    serta penawaran promo terbaru dari Tapak Lokal.
                </h3>

                <!-- Right Newsletter Input & Button Form -->
                <form @submit.prevent="handleSubscribe" class="flex w-full flex-col sm:flex-row items-center gap-2 md:w-auto">
                    <div class="relative w-full sm:w-64">
                        <Mail class="pointer-events-none absolute left-3 top-1/2 size-4 -translate-y-1/2 text-slate-400" />
                        <input
                            v-model="email"
                            type="email"
                            required
                            placeholder="Masukkan Alamat Email Anda"
                            class="h-10 w-full rounded-md border-0 bg-white pl-9 pr-3 text-xs text-slate-800 placeholder:text-slate-400 focus:outline-none focus:ring-2 focus:ring-orange-400 shadow-sm"
                        />
                    </div>
                    <button
                        type="submit"
                        class="h-10 shrink-0 rounded-md bg-[#ff5e1f] px-4 text-xs font-bold text-white shadow-sm transition hover:bg-[#e64f12] active:scale-95"
                    >
                        Langganan Newsletter
                    </button>
                </form>
            </div>
            <div v-if="isSubscribed" class="bg-[#0070d6] px-4 py-1 text-center text-xs font-semibold text-emerald-200">
                <CheckCircle class="inline mr-1 size-3.5" /> Terima kasih! Anda telah berhasil berlangganan newsletter Tapak Lokal.
            </div>
        </div>

        <!-- Main Deep-Navy Footer Body (1:1 with Traveloka Design) -->
        <div class="w-full bg-[#032454] text-white">
            <div class="mx-auto max-w-[1180px] px-4 pt-12 pb-10 sm:px-6 lg:px-0">
                <div class="grid grid-cols-1 gap-10 sm:grid-cols-2 lg:grid-cols-[1.3fr_1.05fr_1.15fr_1.25fr] lg:gap-8">
                    <!-- Column 1: Brand, Certifications, Partner Button & Payments -->
                    <div class="flex flex-col items-start">
                        <!-- Brand Logo (Traveloka Style with Blue Bird/Compass) -->
                        <Link href="/" class="inline-flex items-center gap-1.5 outline-none focus-visible:ring-2 focus-visible:ring-white">
                            <span class="text-2xl font-black tracking-tight text-white">
                                tapak<span class="text-[#0194f3]">lokal</span>
                            </span>
                            <!-- Swift Bird Icon matching Traveloka silhouette -->
                            <svg class="size-6 text-[#0194f3]" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                                <path d="M21.7 4.2c-.4-.3-1-.2-1.3.2l-5.6 7.4-4.8-2.7c-.5-.3-1.1-.1-1.4.4l-6 10c-.3.5-.1 1.1.4 1.4.2.1.4.1.6.1.4 0 .7-.2.9-.5l5.2-8.6 4.9 2.8c.4.2.9.2 1.3-.1l6.8-9c.3-.4.2-1-.1-1.4zM22.5 2.5c-.7-.4-1.6-.2-2 .5l-3.2 5.5 3.8 2.2 2-6.5c.3-.8-.1-1.4-.6-1.7z" opacity="0.9" />
                            </svg>
                        </Link>

                        <!-- Partner with Tapak Lokal Button (Traveloka Light Blue Pill) -->
                        <div class="mt-6">
                            <Link
                                :href="safeRoute('vendor.login')"
                                class="inline-flex items-center gap-2 rounded-full bg-[#0194f3] px-5 py-2 text-xs font-bold text-white shadow-sm transition hover:bg-[#0080d3] active:scale-95"
                            >
                                <Handshake class="size-3.5" />
                                <span>Partner with Tapak Lokal</span>
                            </Link>
                        </div>

                        <!-- Payment Partners -->
                        <div class="mt-7">
                            <h4 class="text-xs font-bold text-white tracking-wide">Payment Partners</h4>
                            <div class="mt-3 flex flex-wrap items-center gap-2">
                                <!-- Mastercard -->
                                <div class="flex h-7 w-11 items-center justify-center rounded bg-white shadow-xs" title="Mastercard">
                                    <div class="flex items-center -space-x-1.5">
                                        <div class="size-3.5 rounded-full bg-[#eb001b]"></div>
                                        <div class="size-3.5 rounded-full bg-[#f79e1b] opacity-80"></div>
                                    </div>
                                </div>
                                <!-- VISA -->
                                <div class="flex h-7 w-11 items-center justify-center rounded bg-white shadow-xs" title="VISA">
                                    <span class="text-[11px] font-black italic tracking-tighter text-[#1a1f71]">VISA</span>
                                </div>
                                <!-- PayPal -->
                                <div class="flex h-7 w-11 items-center justify-center rounded bg-white shadow-xs" title="PayPal">
                                    <span class="text-[11px] font-black italic text-[#003087]">Pay<span class="text-[#0079c1]">Pal</span></span>
                                </div>
                                <!-- QRIS / BCA -->
                                <div class="flex h-7 w-11 items-center justify-center rounded bg-white shadow-xs" title="QRIS / Bank Transfer">
                                    <span class="text-[9px] font-black tracking-tight text-[#005fb8]">QRIS</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Column 2: About Tapak Lokal (from Mindmap Trust & Local) + Follow us on -->
                    <div class="flex flex-col">
                        <h4 class="text-sm font-bold text-white">About Tapak Lokal</h4>
                        <ul class="mt-3.5 space-y-2 text-xs text-[#9eb6d7]">
                            <li>
                                <Link :href="safeRoute('catalog')" class="transition hover:text-white">Cara Pemesanan (How to Book)</Link>
                            </li>
                            <li>
                                <Link :href="safeRoute('account.section', 'support')" class="transition hover:text-white">Pusat Bantuan & Kontak</Link>
                            </li>
                            <li>
                                <Link :href="safeRoute('explore', 'destination')" class="transition hover:text-white">Pemandu Lokal Terverifikasi</Link>
                            </li>
                            <li>
                                <Link :href="safeRoute('catalog')" class="transition hover:text-white">UMKM / Usaha Lokal</Link>
                            </li>
                            <li>
                                <Link :href="safeRoute('account')" class="transition hover:text-white">Verifikasi Identitas (KYC)</Link>
                            </li>
                            <li>
                                <Link :href="safeRoute('account')" class="transition hover:text-white">Identitas Digital & Anti-Pungli</Link>
                            </li>
                            <li>
                                <Link :href="safeRoute('content.show', 'tentang-kami')" class="transition hover:text-white">Tentang Kami (About Us)</Link>
                            </li>
                        </ul>

                        <!-- Follow us on (with specific Brand Icons) -->
                        <div class="mt-7">
                            <h4 class="text-xs font-bold text-white tracking-wide">Follow us on</h4>
                            <div class="mt-3 space-y-2.5 text-xs text-[#9eb6d7]">
                                <!-- Facebook -->
                                <a href="https://facebook.com" target="_blank" rel="noopener noreferrer" class="flex items-center gap-2.5 transition hover:text-white">
                                    <div class="grid size-5 place-items-center rounded bg-[#1877f2] text-white">
                                        <svg class="size-3 fill-current" viewBox="0 0 24 24"><path d="M9 8H6v4h3v12h5V12h3.6l.4-4h-4V6.3c0-.9.2-1.3 1.2-1.3H18V0h-3.8C10.5 0 9 1.6 9 4.6V8z"/></svg>
                                    </div>
                                    <span class="font-medium">Facebook</span>
                                </a>
                                <!-- Instagram -->
                                <a href="https://instagram.com" target="_blank" rel="noopener noreferrer" class="flex items-center gap-2.5 transition hover:text-white">
                                    <div class="grid size-5 place-items-center rounded bg-gradient-to-tr from-[#f09433] via-[#dc2743] to-[#bc1888] text-white">
                                        <svg class="size-3 fill-current" viewBox="0 0 24 24"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"/></svg>
                                    </div>
                                    <span class="font-medium">Instagram</span>
                                </a>
                                <!-- Youtube -->
                                <a href="https://youtube.com" target="_blank" rel="noopener noreferrer" class="flex items-center gap-2.5 transition hover:text-white">
                                    <div class="grid size-5 place-items-center rounded bg-[#ff0000] text-white">
                                        <svg class="size-3 fill-current" viewBox="0 0 24 24"><path d="M23.498 6.186a3.016 3.016 0 0 0-2.122-2.136C19.505 3.545 12 3.545 12 3.545s-7.505 0-9.377.505A3.017 3.017 0 0 0 .502 6.186C0 8.07 0 12 0 12s0 3.93.502 5.814a3.016 3.016 0 0 0 2.122 2.136c1.871.505 9.376.505 9.376.505s7.505 0 9.377-.505a3.015 3.015 0 0 0 2.122-2.136C24 15.93 24 12 24 12s0-3.93-.502-5.814zM9.545 15.568V8.432L15.818 12l-6.273 3.568z"/></svg>
                                    </div>
                                    <span class="font-medium">Youtube</span>
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- Column 3: Products (from Mindmap Discover & Plan & Experience) -->
                    <div class="flex flex-col">
                        <h4 class="text-sm font-bold text-white">Products</h4>
                        <ul class="mt-3.5 space-y-2 text-xs text-[#9eb6d7]">
                            <li>
                                <Link :href="safeRoute('catalog', { type: 'open-trip' })" class="transition hover:text-white">Pencarian Open & Private Trip</Link>
                            </li>
                            <li>
                                <Link :href="safeRoute('explore', 'destination')" class="transition hover:text-white">Peta Wisata Interaktif</Link>
                            </li>
                            <li>
                                <Link :href="safeRoute('explore', 'destination')" class="transition hover:text-white">Tempat Tersembunyi (Hidden Gems)</Link>
                            </li>
                            <li>
                                <Link :href="safeRoute('explore', 'culinary')" class="transition hover:text-white">Kuliner / Makanan Khas</Link>
                            </li>
                            <li>
                                <Link :href="safeRoute('catalog')" class="transition hover:text-white">Tur Virtual 360 Destinasi</Link>
                            </li>
                            <li>
                                <Link :href="safeRoute('catalog')" class="transition hover:text-white">Pembuat Rencana Liburan</Link>
                            </li>
                            <li>
                                <Link :href="safeRoute('catalog')" class="transition hover:text-white">Prediksi Cuaca Wisata</Link>
                            </li>
                            <li>
                                <Link :href="safeRoute('explore', 'destination')" class="transition hover:text-white">Eksplorasi Budaya Lokal</Link>
                            </li>
                            <li>
                                <Link :href="safeRoute('catalog')" class="transition hover:text-white">Kalender Acara / Agenda</Link>
                            </li>
                            <li>
                                <Link :href="safeRoute('account')" class="transition hover:text-white">Cek Kesehatan & Kesiapan Fisik</Link>
                            </li>
                        </ul>
                    </div>

                    <!-- Column 4: Others (from Mindmap Book & Transact, Growth & Engagement) + Download App -->
                    <div class="flex flex-col">
                        <h4 class="text-sm font-bold text-white">Others</h4>
                        <ul class="mt-3.5 space-y-2 text-xs text-[#9eb6d7]">
                            <li>
                                <Link :href="safeRoute('account.section', 'referrals')" class="transition hover:text-white">Tapak Lokal Afiliator</Link>
                            </li>
                            <li>
                                <Link :href="safeRoute('blog')" class="transition hover:text-white">Blog & Cerita Perjalanan</Link>
                            </li>
                            <li>
                                <Link :href="safeRoute('catalog')" class="transition hover:text-white">Bagi Tagihan Otomatis (Split Bill)</Link>
                            </li>
                            <li>
                                <Link :href="safeRoute('catalog')" class="transition hover:text-white">Buka Pre-Order (PO) Lokal</Link>
                            </li>
                            <li>
                                <Link :href="safeRoute('account')" class="transition hover:text-white">Rekening Bersama & Refund</Link>
                            </li>
                            <li>
                                <Link :href="safeRoute('account')" class="transition hover:text-white">Misi, Tantangan & Reward</Link>
                            </li>
                            <li>
                                <Link :href="safeRoute('catalog')" class="transition hover:text-white">Tebak Tempat Berhadiah</Link>
                            </li>
                            <li>
                                <Link :href="safeRoute('vendor.login')" class="transition hover:text-white">Portal Vendor & Cash Flow</Link>
                            </li>
                            <li>
                                <Link :href="safeRoute('accessibility.guide')" class="transition hover:text-white">Panduan Aksesibilitas</Link>
                            </li>
                            <li>
                                <Link :href="safeRoute('content.show', 'kebijakan-privasi')" class="transition hover:text-white">Privacy Notice & Syarat</Link>
                            </li>
                        </ul>
                    </div>
                </div>

                <!-- Copyright Bar (Bottom Center) -->
                <div class="mt-14 border-t border-[#0b336b] pt-6 text-center text-xs text-[#7ea0cc]">
                    <p>Copyright © 2026 Tapak Lokal. All rights reserved</p>
                </div>
            </div>
        </div>
    </footer>
</template>
