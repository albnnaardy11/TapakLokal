<script setup>
import { ref } from 'vue';
import { Head, Link } from '@inertiajs/vue3';
import { route } from 'ziggy-js';
import {
    ArrowRight, ChevronRight, Coins, TicketPercent, Trophy,
    Crown, Timer, Utensils, Plane, Gift, X
} from 'lucide-vue-next';
import MainNavigation from '../Components/Shared/MainNavigation.vue';
import MainFooter from '../Components/Shared/MainFooter.vue';

const props = defineProps({
    promotions: { type: Array, default: () => [] }
});

const dialog = ref(null);
const selectedReward = ref(null);
const notice = ref('');

const photo = (id, width = 800) => `https://images.unsplash.com/${id}?auto=format&fit=crop&w=${width}&q=85`;
const island = photo('photo-1518548419970-58e3b4079ab2', 1800);

const challenges = [
    { title: 'Temukan surga tersembunyi', text: 'Jelajahi 5 destinasi impian di Indonesia.', image: island, points: 150, value: 3, total: 5, progress: '3 / 5 destinasi', type: 'EKSPLORASI', href: route('explore', 'destination') },
    { title: 'Berburu rasa lokal', text: 'Kenali 5 kuliner khas dari berbagai daerah.', image: photo('photo-1504674900247-0877df9cc836'), points: 150, value: 3, total: 5, progress: '3 / 5 kuliner', type: 'KULINER', href: route('open.preorder') },
    { title: 'Jadi traveler aktif', text: 'Temukan inspirasi perjalanan selama 7 hari.', image: photo('photo-1464822759023-fed622ff2c3b'), points: 300, value: 4, total: 7, progress: '4 / 7 hari', type: 'KEBIASAAN BAIK', href: route('catalog') },
    { title: 'Kenalan lebih dekat', text: 'Lengkapi profil untuk perjalanan lebih mudah.', image: photo('photo-1488646953014-85cb44e25828'), points: 50, value: 80, total: 100, progress: '80% lengkap', type: 'PROFIL SAYA', href: route('account') },
];

const rewards = [
    { title: 'Kupon Open Trip', description: 'Bawa rencana liburanmu selangkah lebih dekat.', label: 'DISKON OPEN TRIP', amount: 'Rp50.000', points: '1.000', icon: Plane, popular: true },
    { title: 'Kupon Kuliner Lokal', description: 'Cicipi rasa autentik, dengan harga lebih asyik.', label: 'DISKON KULINER', amount: 'Rp25.000', points: '500', icon: Utensils },
    { title: 'Voucher Eksklusif Member', description: 'Keuntungan spesial untuk perjalanan pilihan.', label: 'KHUSUS MEMBER', amount: 'EXCLUSIVE', points: '2.000', icon: Crown },
    { title: 'Bonus 100 Poin', description: 'Tambahan poin untuk reward incaranmu.', label: 'TAMBAH SEMANGAT', amount: '+100 Poin', points: '750', icon: Coins },
];

const showReward = (reward) => {
    selectedReward.value = reward;
    dialog.value?.showModal();
};

const showInfo = (title, description) => {
    showReward({ title, description });
};

const copyCode = async (code) => {
    try {
        await navigator.clipboard.writeText(code);
        notice.value = `Kode ${code} berhasil disalin.`;
    } catch {
        notice.value = `Salin kode secara manual: ${code}`;
    }
};
</script>

<template>
    <Head title="Discount & Rewards — Jelajah lebih, dapatkan lebih" />
    <MainNavigation />

    <div class="min-h-screen bg-[#f5f8fb] text-[#152c52] font-sans antialiased">
        <main class="mx-auto max-w-[1180px] px-4 py-6 sm:px-6 sm:py-8 lg:px-8">
            <!-- Hero Section -->
            <section class="relative overflow-hidden rounded-2xl bg-[#0a2540] min-h-[300px] sm:min-h-[340px] flex items-center shadow-lg">
                <img
                    :src="island"
                    alt="Pura di tebing pesisir Bali"
                    class="absolute inset-0 size-full object-cover object-center"
                    fetchpriority="high"
                />
                <div class="absolute inset-0 bg-gradient-to-r from-[#072444]/95 via-[#072444]/80 to-transparent"></div>
                <div class="relative z-10 p-6 sm:p-10 lg:p-12 max-w-2xl text-white">
                    <h1 class="text-2xl sm:text-4xl lg:text-[40px] font-extrabold tracking-tight leading-tight">
                        Jelajahi lebih banyak.<br />
                        <span class="text-[#38bdf8]">Dapatkan lebih banyak.</span>
                    </h1>
                    <p class="mt-3 sm:mt-4 text-xs sm:text-sm lg:text-base leading-relaxed text-sky-100/90 max-w-xl">
                        Destinasi baru, pengalaman seru, dan reward untukmu.<br class="hidden sm:inline" />
                        Ubah setiap petualangan menjadi keuntungan.
                    </p>
                    <div class="mt-6 flex flex-wrap items-center gap-3 sm:gap-4">
                        <a
                            href="#tantangan"
                            class="inline-flex items-center gap-2 rounded-xl bg-[#009cf0] px-5 py-3 text-xs sm:text-sm font-bold text-white shadow-md shadow-sky-500/20 transition-all hover:bg-[#0086d1] hover:shadow-lg active:scale-95"
                        >
                            Mulai jelajah <ArrowRight class="size-4" />
                        </a>
                        <a
                            href="#rewards"
                            class="inline-flex items-center gap-2 rounded-xl bg-white/10 backdrop-blur-md border border-white/20 px-5 py-3 text-xs sm:text-sm font-bold text-white transition-all hover:bg-white/20 active:scale-95"
                        >
                            <Gift class="size-4" /> Lihat reward
                        </a>
                    </div>
                </div>
            </section>

            <!-- Preview Notice -->
            <div class="mt-4 mb-2 text-[11px] text-slate-500 flex items-center gap-1.5">
                <span class="text-sky-500 font-bold">●</span> Preview pengalaman rewards · Angka dan progres di bawah adalah ilustrasi.
            </div>

            <!-- Summary Stats Cards -->
            <section class="mt-4 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4" aria-label="Ringkasan reward contoh">
                <button
                    @click="showInfo('Poin Saya', 'Ini adalah contoh tampilan saldo poin. Buka akunmu untuk melihat saldo yang sebenarnya.')"
                    class="group flex items-center justify-between gap-3 rounded-2xl border border-slate-200/80 bg-white p-4 sm:p-5 text-left shadow-xs transition-all hover:shadow-md hover:border-sky-200"
                >
                    <div class="flex items-center gap-3.5 min-w-0">
                        <div class="flex size-12 shrink-0 items-center justify-center rounded-2xl bg-amber-50 text-amber-500 border border-amber-100">
                            <Coins class="size-6" />
                        </div>
                        <div class="min-w-0">
                            <small class="block text-[11px] font-semibold text-slate-500">Poin Saya</small>
                            <strong class="block text-lg sm:text-xl font-extrabold text-[#07345a]">2.450 <span class="text-xs font-semibold text-slate-400">Poin</span></strong>
                            <span class="block text-[10px] text-slate-400 truncate">Tukar poin, wujudkan perjalanan.</span>
                        </div>
                    </div>
                    <ChevronRight class="size-4 text-slate-300 transition-transform group-hover:translate-x-0.5 group-hover:text-sky-500" />
                </button>

                <Link
                    :href="route('account.section', 'vouchers')"
                    class="group flex items-center justify-between gap-3 rounded-2xl border border-slate-200/80 bg-white p-4 sm:p-5 text-left shadow-xs transition-all hover:shadow-md hover:border-sky-200"
                >
                    <div class="flex items-center gap-3.5 min-w-0">
                        <div class="flex size-12 shrink-0 items-center justify-center rounded-2xl bg-sky-50 text-[#009cf0] border border-sky-100">
                            <TicketPercent class="size-6" />
                        </div>
                        <div class="min-w-0">
                            <small class="block text-[11px] font-semibold text-slate-500">Kupon Saya</small>
                            <strong class="block text-lg sm:text-xl font-extrabold text-[#07345a]">Lihat Kupon Akun</strong>
                            <span class="block text-[10px] text-slate-400 truncate">Hemat di perjalanan berikutnya.</span>
                        </div>
                    </div>
                    <ChevronRight class="size-4 text-slate-300 transition-transform group-hover:translate-x-0.5 group-hover:text-sky-500" />
                </Link>

                <a
                    href="#tantangan"
                    class="group flex items-center justify-between gap-3 rounded-2xl border border-slate-200/80 bg-white p-4 sm:p-5 text-left shadow-xs transition-all hover:shadow-md hover:border-sky-200"
                >
                    <div class="flex items-center gap-3.5 min-w-0">
                        <div class="flex size-12 shrink-0 items-center justify-center rounded-2xl bg-amber-50 text-amber-500 border border-amber-100">
                            <Trophy class="size-6" />
                        </div>
                        <div class="min-w-0">
                            <small class="block text-[11px] font-semibold text-slate-500">Challenge Selesai</small>
                            <strong class="block text-lg sm:text-xl font-extrabold text-[#07345a]">12 Challenge</strong>
                            <span class="block text-[10px] text-slate-400 truncate">Setiap langkah layak dirayakan.</span>
                        </div>
                    </div>
                    <ChevronRight class="size-4 text-slate-300 transition-transform group-hover:translate-x-0.5 group-hover:text-sky-500" />
                </a>

                <a
                    href="#rewards"
                    class="group flex items-center justify-between gap-3 rounded-2xl border border-slate-200/80 bg-white p-4 sm:p-5 text-left shadow-xs transition-all hover:shadow-md hover:border-sky-200"
                >
                    <div class="flex items-center gap-3.5 min-w-0 flex-1">
                        <div class="flex size-12 shrink-0 items-center justify-center rounded-2xl bg-blue-50 text-blue-600 border border-blue-100">
                            <Crown class="size-6" />
                        </div>
                        <div class="min-w-0 flex-1">
                            <small class="block text-[11px] font-semibold text-slate-500">Katalog Hadiah</small>
                            <strong class="block text-lg sm:text-xl font-extrabold text-[#07345a]">Tukar Reward</strong>
                            <span class="block text-[10px] text-slate-400 mt-0.5 truncate">Voucher & diskon eksklusif</span>
                        </div>
                    </div>
                    <ChevronRight class="size-4 text-slate-300 transition-transform group-hover:translate-x-0.5 group-hover:text-sky-500" />
                </a>
            </section>

            <!-- Tantangan Section -->
            <section id="tantangan" class="mt-12 scroll-mt-24">
                <div class="flex flex-col sm:flex-row sm:items-end justify-between gap-2 mb-6">
                    <div>
                        <div class="flex items-center gap-2.5">
                            <h2 class="text-xl sm:text-2xl font-extrabold text-[#07345a]">Tantangan untukmu</h2>
                            <span class="rounded-md bg-sky-50 px-2 py-0.5 text-[10px] font-bold tracking-wider text-[#009cf0]">LET'S EXPLORE</span>
                        </div>
                        <p class="mt-1 text-xs sm:text-sm text-slate-500">Pengalaman baru menanti. Selesaikan tantangannya, kumpulkan poinnya.</p>
                    </div>
                    <Link :href="route('explore', 'destination')" class="inline-flex items-center gap-1.5 text-xs sm:text-sm font-bold text-[#009cf0] hover:text-[#0086d1]">
                        Jelajahi destinasi <ArrowRight class="size-4" />
                    </Link>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-5">
                    <article
                        v-for="challenge in challenges"
                        :key="challenge.title"
                        class="flex flex-col overflow-hidden rounded-2xl border border-slate-200/80 bg-white shadow-xs transition-all hover:shadow-md hover:border-sky-200"
                    >
                        <div class="relative h-36 w-full overflow-hidden bg-slate-100">
                            <img :src="challenge.image" :alt="challenge.title" loading="lazy" class="size-full object-cover transition-transform duration-300 hover:scale-105" />
                            <span class="absolute top-2.5 right-2.5 rounded-full bg-[#009cf0] px-2.5 py-0.5 text-[10px] font-bold text-white shadow-xs">
                                ● Berlangsung
                            </span>
                        </div>
                        <div class="flex flex-1 flex-col p-4 sm:p-5">
                            <span class="text-[10px] font-bold tracking-wider text-slate-400 uppercase">{{ challenge.type }}</span>
                            <h3 class="mt-1 text-sm font-bold text-[#07345a] line-clamp-1">{{ challenge.title }}</h3>
                            <p class="mt-1 text-xs text-slate-500 line-clamp-2">{{ challenge.text }}</p>

                            <div class="mt-auto pt-4">
                                <div class="flex items-center justify-between text-xs font-bold mb-1.5">
                                    <span class="inline-flex items-center gap-1.5 text-[#07345a]">
                                        <span class="flex size-4 items-center justify-center rounded-full bg-amber-400 text-[9px] font-black text-amber-950 shadow-xs">P</span>
                                        +{{ challenge.points }} Poin
                                    </span>
                                    <span class="text-[11px] font-medium text-slate-400">{{ challenge.progress }}</span>
                                </div>
                                <div class="h-2 w-full rounded-full bg-slate-100 overflow-hidden">
                                    <div class="h-full rounded-full bg-[#009cf0]" :style="{ width: `${(challenge.value / challenge.total) * 100}%` }"></div>
                                </div>
                                <Link
                                    :href="challenge.href"
                                    class="mt-3.5 inline-flex w-full items-center justify-center gap-1.5 rounded-xl bg-[#009cf0] py-2.5 text-xs font-bold text-white transition-all hover:bg-[#0086d1] active:scale-98"
                                >
                                    Lanjutkan <ArrowRight class="size-3.5" />
                                </Link>
                            </div>
                        </div>
                    </article>
                </div>
            </section>

            <!-- Daily Challenge Panel -->
            <section class="mt-8 rounded-2xl border border-sky-200 bg-gradient-to-br from-sky-100 via-sky-50 to-white p-5 sm:p-7 shadow-xs">
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 items-center">
                    <div class="lg:col-span-2 flex flex-col sm:flex-row items-start sm:items-center gap-5">
                        <div class="flex size-14 shrink-0 items-center justify-center rounded-2xl bg-[#009cf0] text-white shadow-md shadow-sky-500/20">
                            <Timer class="size-7" />
                        </div>
                        <div>
                            <div class="flex items-center gap-2">
                                <h2 class="text-lg sm:text-xl font-extrabold text-[#07345a]">Daily Challenge</h2>
                                <span class="rounded-md bg-sky-200/60 px-2 py-0.5 text-[10px] font-bold text-[#075890]">TANTANGAN HARIAN</span>
                            </div>
                            <p class="mt-1 text-xs sm:text-sm text-slate-600">Satu langkah kecil hari ini, lebih dekat ke reward impian.</p>
                            <div class="mt-3 flex items-center gap-3">
                                <span class="inline-flex items-center gap-1.5 text-xs font-bold text-[#07345a]">
                                    <span class="flex size-4 items-center justify-center rounded-full bg-amber-400 text-[9px] font-black text-amber-950">P</span>
                                    +50 Poin
                                </span>
                                <span class="text-xs text-slate-500">Jelajahi TapakLokal hari ini (1 / 3 destinasi)</span>
                            </div>
                        </div>
                    </div>
                    <div class="flex flex-col items-center justify-center rounded-xl bg-white p-4 border border-sky-100 shadow-xs text-center">
                        <span class="text-[11px] font-semibold text-slate-400">Tantangan baru setiap hari</span>
                        <div class="mt-2 flex items-center gap-2 text-2xl font-black text-[#07345a]">
                            <span>24</span><span class="text-sky-400">:</span><span>00</span><span class="text-sky-400">:</span><span>00</span>
                        </div>
                        <Link :href="route('explore', 'destination')" class="mt-3 inline-flex w-full items-center justify-center gap-1.5 rounded-xl bg-[#009cf0] py-2 text-xs font-bold text-white transition-all hover:bg-[#0086d1]">
                            Mulai sekarang <ArrowRight class="size-3.5" />
                        </Link>
                    </div>
                </div>
            </section>

            <!-- Rewards Section -->
            <section id="rewards" class="mt-12 scroll-mt-24">
                <div class="flex flex-col sm:flex-row sm:items-end justify-between gap-2 mb-6">
                    <div>
                        <h2 class="text-xl sm:text-2xl font-extrabold text-[#07345a]">Reward pilihan untukmu</h2>
                        <p class="mt-1 text-xs sm:text-sm text-slate-500">Petualanganmu punya nilai lebih. Temukan hadiah yang paling kamu suka.</p>
                    </div>
                    <span class="inline-flex items-center gap-1.5 text-xs font-bold text-slate-500">
                        <Gift class="size-4 text-[#009cf0]" /> Pilihan reward
                    </span>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-5">
                    <article
                        v-for="reward in rewards"
                        :key="reward.title"
                        class="relative flex flex-col overflow-hidden rounded-2xl border border-slate-200/80 bg-white p-4 shadow-xs transition-all hover:shadow-md hover:border-sky-200"
                    >
                        <span v-if="reward.popular" class="absolute top-2.5 left-2.5 z-10 rounded-md bg-rose-500 px-2 py-0.5 text-[9px] font-bold text-white shadow-xs">
                            Favorit traveler
                        </span>
                        <div class="relative mt-4 flex items-center justify-between rounded-xl bg-gradient-to-r from-sky-400 to-[#009cf0] p-4 text-white shadow-inner">
                            <div>
                                <small class="block text-[10px] font-bold tracking-wider uppercase opacity-90">{{ reward.label }}</small>
                                <strong class="block text-xl font-black">{{ reward.amount }}</strong>
                            </div>
                            <component :is="reward.icon" class="size-8 opacity-90" />
                        </div>
                        <div class="mt-4 flex flex-1 flex-col">
                            <h3 class="text-sm font-bold text-[#07345a]">{{ reward.title }}</h3>
                            <p class="mt-1 text-xs text-slate-500 line-clamp-2">{{ reward.description }}</p>
                            <div class="mt-auto pt-4 flex items-center justify-between gap-2">
                                <span class="inline-flex items-center gap-1.5 text-xs font-extrabold text-[#07345a]">
                                    <span class="flex size-4 items-center justify-center rounded-full bg-amber-400 text-[9px] font-black text-amber-950">P</span>
                                    {{ reward.points }} Poin
                                </span>
                                <button
                                    @click="showReward(reward)"
                                    class="inline-flex items-center gap-1 rounded-xl bg-sky-50 px-3 py-1.5 text-xs font-bold text-[#009cf0] hover:bg-[#009cf0] hover:text-white transition-all"
                                >
                                    Tukar <ChevronRight class="size-3.5" />
                                </button>
                            </div>
                        </div>
                    </article>
                </div>
            </section>

            <!-- Promo Aktif Saat Ini -->
            <section v-if="props.promotions.length" class="mt-12 mb-12">
                <div class="mb-6">
                    <h2 class="text-xl sm:text-2xl font-extrabold text-[#07345a]">Promo aktif saat ini</h2>
                    <p class="mt-1 text-xs sm:text-sm text-slate-500">Kode penawaran yang tersedia untuk pemesananmu.</p>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                    <article v-for="promotion in props.promotions" :key="promotion.id" class="rounded-2xl border border-slate-200 bg-white p-5 shadow-xs">
                        <TicketPercent class="size-6 text-[#009cf0]" />
                        <h3 class="mt-3 font-bold text-sm text-[#07345a]">{{ promotion.name }}</h3>
                        <code class="mt-2 block rounded-lg bg-sky-50 px-3 py-1.5 text-xs font-mono font-bold text-[#009cf0]">{{ promotion.code }}</code>
                        <button @click="copyCode(promotion.code)" class="mt-4 inline-flex w-full items-center justify-center rounded-xl bg-[#009cf0] py-2 text-xs font-bold text-white transition-all hover:bg-[#0086d1]">
                            Salin kode
                        </button>
                    </article>
                </div>
                <p v-if="notice" role="status" class="mt-2 text-xs text-emerald-600 font-semibold">{{ notice }}</p>
            </section>
        </main>
    </div>

    <MainFooter />

    <!-- Detail Dialog -->
    <dialog
        ref="dialog"
        class="fixed inset-0 m-auto w-[calc(100%-32px)] max-w-md rounded-2xl border-0 p-6 sm:p-8 shadow-2xl backdrop:bg-slate-900/60 backdrop:backdrop-blur-xs"
        @click="event => { if (event.target === dialog) dialog.close(); }"
    >
        <div v-if="selectedReward">
            <div class="flex items-start justify-between">
                <div class="flex size-12 items-center justify-center rounded-2xl bg-sky-50 text-[#009cf0] border border-sky-100">
                    <Gift class="size-6" />
                </div>
                <button
                    aria-label="Tutup detail reward"
                    class="rounded-full p-1.5 text-slate-400 hover:bg-slate-100 hover:text-slate-600 transition-colors"
                    @click="dialog.close()"
                >
                    <X class="size-5" />
                </button>
            </div>
            <span class="mt-4 block text-[10px] font-bold tracking-wider text-slate-400 uppercase">PREVIEW PROGRAM REWARDS</span>
            <h2 class="mt-1 text-xl font-bold text-[#07345a]">{{ selectedReward.title }}</h2>
            <p class="mt-2 text-xs leading-relaxed text-slate-500">{{ selectedReward.description }}</p>
            <p v-if="selectedReward.points" class="mt-3 rounded-xl bg-sky-50 p-3 text-xs leading-relaxed text-[#075890]">
                Penukaran {{ selectedReward.points }} poin ini adalah contoh pengalaman. Belum ada poin yang dipotong atau kupon yang diterbitkan.
            </p>
            <div class="mt-6">
                <Link :href="route('account')" class="inline-flex w-full items-center justify-center gap-2 rounded-xl bg-[#009cf0] py-3 text-xs font-bold text-white shadow-md shadow-sky-500/20 transition-all hover:bg-[#0086d1]">
                    Buka akun saya <ArrowRight class="size-4" />
                </Link>
            </div>
        </div>
    </dialog>
</template>
