<script setup>
import { ref } from 'vue';
import {
    ChevronLeft,
    ChevronRight,
    Compass,
    Copy,
    Check,
    Gift,
    Heart,
    Info,
    MapPin,
    Palmtree,
    Percent,
    Plane,
    ShieldCheck,
    Tag,
    Ticket,
    Utensils,
    Waves,
    Zap,
} from 'lucide-vue-next';

// State for copied coupons
const copiedCode = ref(null);
const activeTooltip = ref(null);
const promoScrollContainer = ref(null);
const voucherScrollContainer = ref(null);

// Copy coupon code to clipboard
const copyCoupon = (code) => {
    navigator.clipboard.writeText(code);
    copiedCode.value = code;
    setTimeout(() => {
        if (copiedCode.value === code) {
            copiedCode.value = null;
        }
    }, 2500);
};

// Scroll promo cards container
const scrollPromos = (direction) => {
    if (promoScrollContainer.value) {
        const scrollAmount = direction === 'left' ? -380 : 380;
        promoScrollContainer.value.scrollBy({ left: scrollAmount, behavior: 'smooth' });
    }
};

// Scroll voucher cards container
const scrollVouchers = (direction) => {
    if (voucherScrollContainer.value) {
        const scrollAmount = direction === 'left' ? -340 : 340;
        voucherScrollContainer.value.scrollBy({ left: scrollAmount, behavior: 'smooth' });
    }
};

// Promo Banner Offers Data (1:1 Traveloka Inspired)
const promoOffers = [
    {
        id: 'promo-hotel-trip',
        title: 'Trip & Penginapan Harga Terbaik',
        discount: 'Diskon s.d. 30%',
        tag: 'HOTEL & SAILING',
        accentColor: 'from-[#1e293b] via-[#0f172a] to-transparent',
        badgeColor: 'bg-emerald-500',
        imageUrl: 'https://images.unsplash.com/photo-1540555700478-4be289fbecef?auto=format&fit=crop&w=800&q=80',
        features: ['Easy Refund', 'Easy Reschedule', '24/7 Live Guide Support'],
        terms: 'S&K berlaku • Periode s.d. 31 Okt 2026',
    },
    {
        id: 'promo-jelajah-dunia',
        title: 'Jelajahi Pesona Surga Bahari',
        discount: 'Cashback Rp 150.000',
        tag: 'OPEN TRIP SPESIAL',
        accentColor: 'from-[#0055d4] via-[#0070ea] to-[#00aaff]',
        badgeColor: 'bg-amber-400 text-slate-900',
        imageUrl: 'https://images.unsplash.com/photo-1516426122078-c23e76319801?auto=format&fit=crop&w=800&q=80',
        quote: 'Labuan Bajo, Raja Ampat & Derawan menantimu.',
        terms: 'Kuota terbatas setiap weekend',
    },
    {
        id: 'promo-injourney',
        brand: 'TAPAKLOKAL OFFICIAL',
        title: 'Liburan Nyaman Bersama Mitra Terverifikasi',
        discount: 'Hemat hingga 25%',
        tag: 'MITRA PREMIUM',
        accentColor: 'from-[#d97706] via-[#ea580c] to-[#f97316]',
        badgeColor: 'bg-white text-orange-600',
        imageUrl: 'https://images.unsplash.com/photo-1566073771259-6a8506099945?auto=format&fit=crop&w=800&q=80',
        features: ['All-in Transport AC', 'Homestay & Villa Mewah', 'Dokumentasi Drone'],
        terms: 'Periode promo: Sep, Okt & Nov 2026',
    },
    {
        id: 'promo-bromo-mountain',
        title: 'Ekspedisi Sunrise Bromo & Kawah Ijen',
        discount: 'Potongan Langsung Rp 75.000',
        tag: 'MOUNTAIN & JEEP 4X4',
        accentColor: 'from-[#065f46] via-[#047857] to-[#10b981]',
        badgeColor: 'bg-lime-400 text-slate-900',
        imageUrl: 'https://images.unsplash.com/photo-1588668214407-6ea9a6d8c272?auto=format&fit=crop&w=800&q=80',
        quote: 'Driver berpengalaman & spot sunrise terbaik tanpa macet.',
        terms: 'Berlaku untuk semua tipe booking',
    },
];

// New User & Special Ticket Coupons Data (1:1 Traveloka Ticket Design)
const couponVouchers = [
    {
        id: 'c-open-trip',
        serviceType: 'open-trip',
        categoryLabel: 'Open Trip',
        iconBg: 'bg-blue-100 text-[#0066d6]',
        title: 'Up to 8% for First Open Trip Booking',
        subtitle: 'Valid for New Users on TapakLokal',
        code: 'JALANYUK',
        minSpend: 'Min. transaksi Rp 300.000',
        validUntil: 'Berlaku s.d. 31 Des 2026',
        terms: 'Potongan 8% hingga Rp 100.000 untuk transaksi paket Open Trip pertama.',
    },
    {
        id: 'c-private-trip',
        serviceType: 'private-trip',
        categoryLabel: 'Private Trip',
        iconBg: 'bg-rose-100 text-rose-600',
        title: 'Up to 10% for First Private Trip Booking',
        subtitle: 'Valid for New Users on TapakLokal',
        code: 'TAPAKBARU',
        minSpend: 'Min. transaksi Rp 800.000',
        validUntil: 'Berlaku s.d. 31 Des 2026',
        terms: 'Potongan langsung Rp 150.000 untuk booking rombongan / keluarga pertama kali.',
    },
    {
        id: 'c-bahari',
        serviceType: 'bahari',
        categoryLabel: 'Wisata Bahari',
        iconBg: 'bg-teal-100 text-teal-600',
        title: 'Up to 12% for First Snorkeling & Island Tour',
        subtitle: 'Valid for New Users on TapakLokal',
        code: 'BAHARISERU',
        minSpend: 'Min. transaksi Rp 400.000',
        validUntil: 'Berlaku s.d. 31 Des 2026',
        terms: 'Diskon 12% untuk paket wisata pulau Pramuka, Pahawang, atau Komodo.',
    },
    {
        id: 'c-weekend-flash',
        serviceType: 'flash-sale',
        categoryLabel: 'Flash Weekend',
        iconBg: 'bg-amber-100 text-amber-600',
        title: 'Extra Rp 50.000 Weekend Outing Trip',
        subtitle: 'Valid on Weekend Departures',
        code: 'WEEKENDHEMAT',
        minSpend: 'Min. transaksi Rp 350.000',
        validUntil: 'Berlaku setiap Jumat - Minggu',
        terms: 'Cashback langsung Rp 50.000 dalam bentuk TapakPoints untuk keberangkatan weekend.',
    },
];
</script>

<template>
    <section class="w-full mt-12 sm:mt-14" aria-label="Promo dan Diskon">
        <!-- Main White Container with Crisp Shadow & Border -->
        <div class="rounded-3xl border border-slate-200/90 bg-white p-5 sm:p-7 md:p-8 shadow-[0_8px_30px_rgba(0,0,0,0.04)]">
            
            <!-- ================================================================= -->
            <!-- 1. SUBSECTION 1: "All promo offers" (Horizontal Promo Banners)    -->
            <!-- ================================================================= -->
            <div>
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-base sm:text-lg font-extrabold text-slate-900 tracking-tight">
                        All promo offers
                    </h3>
                    
                    <!-- Navigation Controls for Banners -->
                    <div class="hidden sm:flex items-center gap-2">
                        <button
                            type="button"
                            class="grid size-8 place-items-center rounded-full border border-slate-200 bg-white text-slate-600 shadow-2xs transition hover:bg-slate-50 active:scale-95 cursor-pointer"
                            aria-label="Scroll promo ke kiri"
                            @click="scrollPromos('left')"
                        >
                            <ChevronLeft class="size-4 stroke-[2.5]" />
                        </button>
                        <button
                            type="button"
                            class="grid size-8 place-items-center rounded-full border border-slate-200 bg-[#dff1ff] text-[#0066d6] shadow-2xs transition hover:bg-[#cbe8ff] active:scale-95 cursor-pointer"
                            aria-label="Scroll promo ke kanan"
                            @click="scrollPromos('right')"
                        >
                            <ChevronRight class="size-4 stroke-[2.5]" />
                        </button>
                    </div>
                </div>

                <!-- Horizontal Scrollable Banners Row -->
                <div
                    ref="promoScrollContainer"
                    class="flex gap-4 overflow-x-auto pb-3 pt-1 scroll-smooth snap-x snap-mandatory no-scrollbar"
                >
                    <!-- Card 1: Hotel & Trip (Dark Gradient with Neon & Photos) -->
                    <div
                        v-for="promo in promoOffers"
                        :key="promo.id"
                        class="group relative flex-none w-[310px] sm:w-[350px] md:w-[370px] h-[175px] sm:h-[185px] rounded-2xl overflow-hidden shadow-sm hover:shadow-lg transition-all duration-300 snap-start border border-slate-200/80 cursor-pointer"
                    >
                        <!-- Background Image -->
                        <img
                            :src="promo.imageUrl"
                            :alt="promo.title"
                            class="absolute inset-0 size-full object-cover transition-transform duration-500 group-hover:scale-105"
                            loading="lazy"
                        />

                        <!-- Gradient Backdrop Overlay -->
                        <div
                            v-if="promo.id === 'promo-jelajah-dunia'"
                            class="absolute inset-0 bg-gradient-to-r from-[#0055d4]/95 via-[#0070ea]/85 to-transparent z-10"
                        ></div>
                        <div
                            v-else-if="promo.id === 'promo-injourney'"
                            class="absolute inset-0 bg-gradient-to-r from-[#b45309]/95 via-[#d97706]/80 to-transparent z-10"
                        ></div>
                        <div
                            v-else-if="promo.id === 'promo-bromo-mountain'"
                            class="absolute inset-0 bg-gradient-to-r from-[#064e3b]/95 via-[#047857]/80 to-transparent z-10"
                        ></div>
                        <div
                            v-else
                            class="absolute inset-0 bg-gradient-to-r from-slate-950/90 via-slate-900/70 to-transparent z-10"
                        ></div>

                        <!-- Card Content Overlay -->
                        <div class="relative z-20 flex flex-col justify-between size-full p-4 text-white">
                            <div>
                                <div class="flex items-center gap-1.5">
                                    <span class="rounded bg-white/20 px-2 py-0.5 text-[9px] font-extrabold uppercase tracking-wider backdrop-blur-xs text-white">
                                        {{ promo.tag }}
                                    </span>
                                </div>

                                <h4 class="mt-2 text-sm sm:text-base font-extrabold leading-tight text-white line-clamp-2 max-w-[230px]">
                                    {{ promo.title }}
                                </h4>

                                <p class="mt-1 text-xs sm:text-sm font-black text-amber-300">
                                    {{ promo.discount }}
                                </p>
                            </div>

                            <!-- Bottom Feature Badges or Subtitle -->
                            <div>
                                <div v-if="promo.features" class="flex flex-wrap gap-1">
                                    <span
                                        v-for="(f, fIdx) in promo.features"
                                        :key="fIdx"
                                        class="rounded-md bg-white/15 px-1.5 py-0.5 text-[8.5px] font-bold text-slate-100 backdrop-blur-xs"
                                    >
                                        {{ f }}
                                    </span>
                                </div>
                                <p v-else-if="promo.quote" class="text-[10px] text-blue-100 italic line-clamp-1">
                                    {{ promo.quote }}
                                </p>
                                <span class="block text-[8.5px] text-slate-300 mt-1">
                                    {{ promo.terms }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- ================================================================= -->
            <!-- 3. SUBSECTION 2: "🎁 8% New User Coupons" (Ticket Coupon Cards)   -->
            <!-- ================================================================= -->
            <div class="mt-9 pt-7 border-t border-slate-100">
                
                <!-- Section Title & Subtitle -->
                <div class="flex flex-wrap items-center justify-between gap-2 mb-4">
                    <div>
                        <div class="flex items-center gap-2">
                            <div class="grid size-6 place-items-center rounded-lg bg-blue-50 text-[#0066d6]">
                                <Gift class="size-4" />
                            </div>
                            <h3 class="text-base sm:text-lg font-extrabold text-slate-900 tracking-tight">
                                8% New User Coupons
                            </h3>
                        </div>
                        <p class="mt-0.5 text-xs font-bold text-[#0066d6]">
                            Valid for First Transaction on TapakLokal App & Web
                        </p>
                    </div>

                    <!-- Navigation Controls for Vouchers -->
                    <div class="hidden sm:flex items-center gap-2">
                        <button
                            type="button"
                            class="grid size-8 place-items-center rounded-full border border-slate-200 bg-white text-slate-600 shadow-2xs transition hover:bg-slate-50 active:scale-95 cursor-pointer"
                            aria-label="Scroll kupon ke kiri"
                            @click="scrollVouchers('left')"
                        >
                            <ChevronLeft class="size-4 stroke-[2.5]" />
                        </button>
                        <button
                            type="button"
                            class="grid size-8 place-items-center rounded-full border border-slate-200 bg-[#dff1ff] text-[#0066d6] shadow-2xs transition hover:bg-[#cbe8ff] active:scale-95 cursor-pointer"
                            aria-label="Scroll kupon ke kanan"
                            @click="scrollVouchers('right')"
                        >
                            <ChevronRight class="size-4 stroke-[2.5]" />
                        </button>
                    </div>
                </div>

                <!-- Ticket Voucher Cards Carousel / Grid -->
                <div
                    ref="voucherScrollContainer"
                    class="flex gap-4 overflow-x-auto pb-2 pt-1 scroll-smooth snap-x snap-mandatory no-scrollbar"
                >
                    <div
                        v-for="voucher in couponVouchers"
                        :key="voucher.id"
                        class="relative flex-none w-[290px] sm:w-[320px] md:w-[340px] rounded-2xl border border-slate-200/90 bg-white shadow-2xs hover:shadow-md transition-all duration-200 snap-start overflow-hidden"
                    >
                        <!-- Left & Right Circular Ticket Notches (Traveloka Style) -->
                        <div class="absolute -left-2.5 top-[58%] -translate-y-1/2 size-5 rounded-full bg-[#f4f7fb] border-r border-slate-200/80 z-20 pointer-events-none"></div>
                        <div class="absolute -right-2.5 top-[58%] -translate-y-1/2 size-5 rounded-full bg-[#f4f7fb] border-l border-slate-200/80 z-20 pointer-events-none"></div>

                        <!-- Top Half: Icon, Title, Info -->
                        <div class="p-3.5 pb-2.5">
                            <div class="flex items-start gap-2.5">
                                <!-- Category Icon Badge -->
                                <div class="grid size-7 shrink-0 place-items-center rounded-full text-xs font-bold" :class="voucher.iconBg">
                                    <Tag v-if="voucher.serviceType === 'open-trip'" class="size-3.5" />
                                    <Compass v-else-if="voucher.serviceType === 'private-trip'" class="size-3.5" />
                                    <Waves v-else-if="voucher.serviceType === 'bahari'" class="size-3.5" />
                                    <Zap v-else class="size-3.5" />
                                </div>

                                <!-- Title & Details -->
                                <div class="min-w-0 flex-1">
                                    <div class="flex items-start justify-between gap-1">
                                        <h4 class="text-xs font-extrabold text-slate-800 leading-snug line-clamp-1">
                                            {{ voucher.title }}
                                        </h4>
                                        
                                        <!-- Info Tooltip Button -->
                                        <button
                                            type="button"
                                            class="text-slate-400 hover:text-slate-600 transition shrink-0 cursor-pointer"
                                            :title="voucher.terms"
                                            @click="activeTooltip = activeTooltip === voucher.id ? null : voucher.id"
                                        >
                                            <Info class="size-3.5" />
                                        </button>
                                    </div>
                                    <p class="text-[10px] text-slate-400 mt-0.5">
                                        {{ voucher.subtitle }}
                                    </p>
                                </div>
                            </div>

                            <!-- Popover info tooltip if opened -->
                            <div v-if="activeTooltip === voucher.id" class="mt-2 rounded-lg bg-slate-50 p-2 text-[10px] text-slate-600 border border-slate-200/70">
                                <p class="font-bold text-slate-700">{{ voucher.terms }}</p>
                                <p class="text-slate-400 mt-0.5">{{ voucher.minSpend }} • {{ voucher.validUntil }}</p>
                            </div>
                        </div>

                        <!-- Dashed Divider Line with Notches Alignment -->
                        <div class="relative px-3.5">
                            <div class="border-b border-dashed border-slate-200"></div>
                        </div>

                        <!-- Bottom Half: Voucher Code & Copy Action Button -->
                        <div class="p-3.5 pt-2 flex items-center justify-between bg-slate-50/40">
                            <div class="flex items-center gap-1.5 text-xs font-mono font-bold text-slate-700">
                                <Copy class="size-3 text-slate-400" />
                                <span class="tracking-wider uppercase">{{ voucher.code }}</span>
                            </div>

                            <button
                                type="button"
                                class="inline-flex items-center gap-1 rounded-full px-3.5 py-1 text-xs font-bold transition-all active:scale-95 cursor-pointer shadow-2xs"
                                :class="copiedCode === voucher.code
                                    ? 'bg-emerald-100 text-emerald-700'
                                    : 'bg-[#dff1ff] text-[#0066d6] hover:bg-[#cbe8ff]'"
                                @click="copyCoupon(voucher.code)"
                            >
                                <Check v-if="copiedCode === voucher.code" class="size-3 stroke-[2.5]" />
                                <span>{{ copiedCode === voucher.code ? 'Tersalin' : 'Copy' }}</span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </section>
</template>

<style scoped>
.no-scrollbar::-webkit-scrollbar {
    display: none;
}
.no-scrollbar {
    -ms-overflow-style: none;
    scrollbar-width: none;
}
</style>
