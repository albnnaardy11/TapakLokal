<script setup>
import { Head, Link } from '@inertiajs/vue3';
import {
    ALargeSmall,
    ArrowDown,
    ArrowLeftRight,
    ArrowRight,
    ArrowUp,
    BookOpen,
    Check,
    Cloud,
    Compass,
    Contrast,
    ImageOff,
    Link2,
    Mouse,
    MoveVertical,
    Palette,
    PauseCircle,
    RotateCcw,
    ScanLine,
    Settings2,
    SlidersHorizontal,
    Type,
    Volume2
} from 'lucide-vue-next';
import MainNavigation from '../Components/Shared/MainNavigation.vue';

const features = [
    { title: 'Kontras', icon: Contrast, description: 'Ubah perbedaan warna teks dan latar agar konten lebih mudah dibaca.', options: 'Standar · Gelap · Terang · Tinggi' },
    { title: 'Spasi', icon: ArrowLeftRight, description: 'Atur jarak antar huruf dan antar kata supaya teks lebih rapat atau renggang.', options: 'Standar · Sedang · Lebar' },
    { title: 'Warna', icon: Palette, description: 'Sesuaikan intensitas warna halaman tanpa mengubah isi kontennya.', options: 'Standar · Abu-abu · Saturasi rendah · Saturasi tinggi' },
    { title: 'Ukuran teks', icon: ALargeSmall, description: 'Perbesar teks agar lebih mudah dibaca.', options: 'Standar · Besar · Lebih besar · Terbesar' },
    { title: 'Sorot tautan', icon: Link2, description: 'Tambahkan garis bawah dan highlight pada tautan.', options: 'Aktif / Nonaktif' },
    { title: 'Jeda animasi', icon: PauseCircle, description: 'Jeda animasi dan carousel agar halaman lebih tenang.', options: 'Aktif / Nonaktif' },
    { title: 'Sembunyikan gambar', icon: ImageOff, description: 'Sembunyikan gambar yang tidak diperlukan.', options: 'Aktif / Nonaktif' },
    { title: 'Ramah disleksia', icon: Type, description: 'Gunakan font yang lebih ramah untuk disleksia.', options: 'Aktif / Nonaktif' },
    { title: 'Kursor besar', icon: Mouse, description: 'Perbesar penunjuk mouse agar lebih mudah ditemukan.', options: 'Aktif / Nonaktif' },
    { title: 'Tinggi baris', icon: MoveVertical, description: 'Atur jarak antar baris agar teks lebih mudah dibaca.', options: 'Standar · Sedang · Renggang · Sangat renggang' },
    { title: 'Baca teks', icon: Volume2, description: 'Bacakan teks di halaman menggunakan teknologi suara.', options: 'Aktif / Nonaktif' },
    { title: 'Garis penuntun', icon: ScanLine, description: 'Tampilkan garis penuntun horizontal untuk membantu fokus membaca.', options: 'Aktif / Nonaktif' },
];

const steps = [
    { title: 'Buka panel', icon: SlidersHorizontal, description: 'Tekan tombol aksesibilitas di kanan bawah halaman.' },
    { title: 'Sesuaikan tampilan', icon: ArrowLeftRight, description: 'Pilih fitur yang kamu butuhkan.' },
    { title: 'Mulai menjelajah', icon: Compass, description: 'Tampilan akan langsung menyesuaikan dengan pilihanmu.' },
];

const shortcuts = [
    { key: 'Tab', description: 'Pindah ke kontrol berikutnya.' },
    { key: 'Shift + Tab', description: 'Kembali ke kontrol sebelumnya.' },
    { key: 'Enter', description: 'Aktifkan tombol atau buka pilihan.' },
    { key: 'Spasi', description: 'Aktifkan tombol yang sedang dipilih.' },
    { key: 'Escape', description: 'Tutup panel aksesibilitas.' },
];

const openAccessibilityPanel = () => {
    const trigger = document.querySelector('.accessibility-fab');
    if (trigger) {
        trigger.click();
    } else {
        document.getElementById('accessibility-panel')?.showModal();
    }
};

const resetSettings = () => {
    const resetBtn = document.querySelector('.accessibility-reset');
    if (resetBtn) {
        resetBtn.click();
    }
};
</script>

<template>
    <Head title="Panduan Aksesibilitas">
        <meta name="description" content="Kenali pengaturan aksesibilitas TapakLokal: kontras, ukuran teks, spasi, warna, dan fitur lainnya untuk pengalaman menjelajah yang lebih nyaman." />
    </Head>
    <div class="min-h-screen bg-[#f8fafc] font-sans text-[#172c50]">
        <MainNavigation />
        <main class="mx-auto max-w-[1200px] px-5 pb-28 pt-8 sm:px-8 lg:pt-12">
            <!-- Hero Section (Preserved as requested) -->
            <section aria-labelledby="guide-title" class="relative overflow-hidden rounded-3xl border border-[#d8eafb] bg-[#edf4ff] p-6 sm:p-10 lg:p-12">
                <div class="grid gap-8 lg:grid-cols-[1.6fr_1fr] lg:items-center lg:gap-16">
                    <div>
                        <span class="inline-flex items-center gap-2 rounded-full border border-[#3E7BEF]/15 bg-white px-3 py-1.5 text-xs font-bold text-[#2e69d9]"><BookOpen class="size-4" aria-hidden="true" />PANDUAN TAPAKLOKAL</span>
                        <h1 id="guide-title" class="mt-5 text-3xl font-extrabold leading-tight tracking-tight sm:text-4xl lg:text-5xl">Jelajah lebih nyaman,<br /><span class="text-[#3E7BEF]">dengan caramu.</span></h1>
                        <p class="mt-5 max-w-xl text-base leading-7 text-slate-600">Panduan aksesibilitas untuk menyesuaikan tampilan TapakLokal dengan kebutuhanmu. Mulai dari teks yang lebih besar hingga halaman yang lebih tenang.</p>
                        <a href="#mulai" class="mt-6 inline-flex min-h-12 items-center gap-2 rounded-xl bg-[#3E7BEF] px-5 text-sm font-bold text-white transition-colors hover:bg-[#2e69d9] focus-visible:outline-2 focus-visible:outline-offset-4 focus-visible:outline-[#3E7BEF]">Mulai dari sini <ArrowDown class="size-4" aria-hidden="true" /></a>
                    </div>
                    <div class="rounded-2xl border border-white bg-white/85 p-6 shadow-[0_12px_32px_rgba(23,75,120,0.06)] sm:p-7">
                        <span class="grid size-12 place-items-center rounded-2xl bg-[#edf4ff] text-[#3E7BEF]"><Settings2 class="size-6" aria-hidden="true" /></span>
                        <h2 class="mt-5 text-xl font-bold">Satu panel, banyak pilihan.</h2>
                        <p class="mt-2 text-sm leading-6 text-slate-500">Atur tampilan tanpa mengubah isi cerita dan informasi perjalanan.</p>
                        <ul class="mt-5 space-y-3 text-sm font-medium"><li v-for="benefit in ['12 fitur yang bisa dikombinasikan', 'Pilihan tersimpan di browser ini', 'Bisa kembali ke tampilan standar']" :key="benefit" class="flex items-start gap-2"><Check class="mt-0.5 size-4 shrink-0 text-[#3E7BEF]" aria-hidden="true" />{{ benefit }}</li></ul>
                    </div>
                </div>
            </section>

            <!-- Section 1: Tiga langkah untuk lebih nyaman -->
            <section id="mulai" aria-labelledby="start-title" class="mt-14 scroll-mt-32 rounded-3xl border border-[#d8eafb] bg-[#f0f6fe] p-6 sm:p-8 lg:p-10">
                <div class="grid gap-8 lg:grid-cols-[1fr_2.2fr] lg:items-center">
                    <div>
                        <p class="text-xs font-bold uppercase tracking-[0.14em] text-[#3E7BEF]">MULAI DENGAN MUDAH</p>
                        <h2 id="start-title" class="mt-2 text-2xl font-extrabold tracking-tight text-[#172c50] sm:text-3xl">Tiga langkah untuk<br />lebih nyaman.</h2>
                        <p class="mt-3 text-xs leading-relaxed text-slate-500 sm:text-sm">Ikuti langkah ini untuk menyesuaikan tampilan sesuai kebutuhanmu.</p>
                    </div>
                    <div class="grid gap-4 sm:grid-cols-3">
                        <div v-for="(step, index) in steps" :key="step.title" class="relative flex flex-col items-center rounded-2xl border border-[#e2eaf4] bg-white p-5 text-center shadow-[0_2px_8px_rgba(23,44,80,0.03)]">
                            <span class="absolute left-3.5 top-3.5 grid size-6 place-items-center rounded-full bg-[#3E7BEF] text-xs font-extrabold text-white">{{ index + 1 }}</span>
                            <span class="mt-2 grid size-12 place-items-center rounded-2xl bg-[#edf4ff] text-[#3E7BEF]"><component :is="step.icon" class="size-5" aria-hidden="true" /></span>
                            <h3 class="mt-3 text-sm font-bold text-[#172c50]">{{ step.title }}</h3>
                            <p class="mt-1 text-xs leading-relaxed text-slate-500">{{ step.description }}</p>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Section 2: Fitur aksesibilitas (12 cards in 4-column grid) -->
            <section id="fitur" aria-labelledby="features-title" class="mt-16 scroll-mt-32">
                <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
                    <div>
                        <p class="text-xs font-bold uppercase tracking-[0.14em] text-[#3E7BEF]">SESUAIKAN DENGAN KEBUTUHANMU</p>
                        <h2 id="features-title" class="mt-2 text-2xl font-extrabold tracking-tight text-[#172c50] sm:text-3xl">Fitur aksesibilitas.</h2>
                        <p class="mt-2 max-w-2xl text-xs leading-relaxed text-slate-500 sm:text-sm">Pilih dan atur tampilan sesuai preferensimu. Semua perubahan bisa dilakukan langsung di panel aksesibilitas tanpa mengubah isi cerita dan informasi perjalanan.</p>
                    </div>
                    <button type="button" class="inline-flex shrink-0 items-center gap-2 rounded-full bg-[#3E7BEF] px-5 py-2.5 text-xs font-bold text-white shadow-sm transition-all hover:bg-[#2e69d9]" @click="openAccessibilityPanel">
                        <svg width="18" height="18" viewBox="0 0 32 32" fill="none" aria-hidden="true">
                            <circle cx="19" cy="5.5" r="3" fill="currentColor" />
                            <path d="M8 13l4-3 5.5 2-1.5 6h8l1.5 9M9 17a7.5 7.5 0 1 0 11 8" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                        Buka panel aksesibilitas
                    </button>
                </div>

                <div class="mt-8 grid gap-4 sm:grid-cols-2 lg:grid-cols-4 sm:gap-5">
                    <article v-for="feature in features" :key="feature.title" class="flex flex-col justify-between rounded-2xl border border-[#e2eaf4] bg-white p-5 shadow-[0_2px_8px_rgba(23,44,80,0.02)] transition-all hover:border-[#bcd2ff] hover:shadow-md">
                        <div>
                            <span class="mb-3.5 grid size-10 place-items-center rounded-xl bg-[#edf4ff] text-[#3E7BEF]"><component :is="feature.icon" class="size-5" aria-hidden="true" /></span>
                            <h3 class="text-[15px] font-bold text-[#172c50]">{{ feature.title }}</h3>
                            <p class="mt-1.5 text-xs leading-relaxed text-slate-500">{{ feature.description }}</p>
                        </div>
                        <div class="mt-4 rounded-lg bg-[#f0f5ff] px-2.5 py-1.5 text-center text-[11px] font-semibold text-[#245bbf]">
                            {{ feature.options }}
                        </div>
                    </article>
                </div>
            </section>

            <!-- Section 3: Tetap mudah, tanpa mouse (1:1 with design reference) -->
            <section id="keyboard" aria-labelledby="keyboard-title" class="mt-16 scroll-mt-32 overflow-hidden rounded-[28px] border border-[#d8eafb] bg-[#ebf4fd]">
                <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between">
                    <!-- Left: Laptop Image flush to top/bottom/left -->
                    <div class="relative h-64 sm:h-72 lg:h-[310px] w-full lg:w-[320px] xl:w-[350px] shrink-0 overflow-hidden flex items-center justify-start">
                        <img
                            src="/Assets/Images/accessibility/laptop-closeup.webp"
                            alt="Keyboard navigasi aksesibilitas"
                            class="h-full w-full object-cover object-left mix-blend-multiply select-none pointer-events-none"
                        />
                    </div>

                    <!-- Middle: Title & Description -->
                    <div class="flex-1 px-6 sm:px-8 lg:px-4 py-6 lg:py-8 max-w-[340px]">
                        <h2 id="keyboard-title" class="text-2xl sm:text-3xl lg:text-[34px] font-extrabold tracking-tight text-[#172c50] leading-[1.16]">
                            Tetap mudah,<br />tanpa mouse.
                        </h2>
                        <p class="mt-4 text-xs sm:text-[14px] leading-relaxed text-[#4b5e76]">
                            Gunakan keyboard untuk membuka dan mengatur panel. Garis fokus menunjukkan kontrol yang sedang dipilih.
                        </p>
                    </div>

                    <!-- Right: White Card Container with Shortcuts -->
                    <div class="p-6 sm:p-8 lg:p-6 lg:pr-8 shrink-0">
                        <div class="w-full lg:w-[370px] xl:w-[390px] rounded-2xl bg-white p-5 sm:p-6 shadow-sm border border-[#e2eaf4]">
                            <dl class="divide-y divide-[#f1f5f9]">
                                <div v-for="shortcut in shortcuts" :key="shortcut.key" class="flex items-center gap-4 py-2.5 first:pt-0 last:pb-0">
                                    <dt class="shrink-0">
                                        <kbd class="inline-flex items-center justify-center min-w-[76px] rounded-full bg-[#edf4ff] px-3.5 py-1 text-center font-sans text-xs font-bold text-[#172c50]">{{ shortcut.key }}</kbd>
                                    </dt>
                                    <dd class="text-xs sm:text-[13.5px] font-medium text-[#475569]">{{ shortcut.description }}</dd>
                                </div>
                            </dl>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Section 4: Pengaturan tetap dalam kendalimu -->
            <section id="pengaturan" aria-labelledby="settings-title" class="mt-16 scroll-mt-32">
                <div class="flex flex-col gap-2 sm:flex-row sm:items-end sm:justify-between">
                    <div>
                        <h2 id="settings-title" class="text-2xl font-extrabold tracking-tight text-[#172c50] sm:text-3xl">Pengaturan tetap dalam kendalimu.</h2>
                        <p class="mt-1 text-xs text-slate-500 sm:text-sm">Semua pilihan tersimpan otomatis di browser pada perangkat ini.</p>
                    </div>
                    <button type="button" class="inline-flex items-center gap-1.5 text-xs font-bold text-[#3E7BEF] transition-colors hover:text-[#2e69d9]" @click="resetSettings">
                        <RotateCcw class="size-3.5" aria-hidden="true" />
                        Reset semua pengaturan
                    </button>
                </div>

                <div class="mt-6 grid gap-5 md:grid-cols-3">
                    <article class="flex items-start gap-4 rounded-2xl border border-[#e2eaf4] bg-white p-5 shadow-[0_2px_8px_rgba(23,44,80,0.02)]">
                        <span class="grid size-12 shrink-0 place-items-center rounded-2xl bg-[#edf4ff] text-[#3E7BEF]"><Cloud class="size-6" aria-hidden="true" /></span>
                        <div>
                            <h3 class="text-sm font-bold text-[#172c50]">Tersimpan otomatis</h3>
                            <p class="mt-1 text-xs leading-relaxed text-slate-500">Pilihanmu akan tetap tersimpan, bahkan setelah browser ditutup.</p>
                        </div>
                    </article>
                    <article class="flex items-start gap-4 rounded-2xl border border-[#e2eaf4] bg-white p-5 shadow-[0_2px_8px_rgba(23,44,80,0.02)]">
                        <span class="grid size-12 shrink-0 place-items-center rounded-2xl bg-[#edf4ff] text-[#3E7BEF]"><RotateCcw class="size-6" aria-hidden="true" /></span>
                        <div>
                            <h3 class="text-sm font-bold text-[#172c50]">Kembali ke standar</h3>
                            <p class="mt-1 text-xs leading-relaxed text-slate-500">Buka panel, lalu tekan “Reset semua pengaturan” untuk kembali ke kondisi awal.</p>
                        </div>
                    </article>
                    <article class="flex items-start gap-4 rounded-2xl border border-[#e2eaf4] bg-white p-5 shadow-[0_2px_8px_rgba(23,44,80,0.02)]">
                        <span class="grid size-12 shrink-0 place-items-center rounded-2xl bg-[#edf4ff] text-[#3E7BEF]"><ArrowUp class="size-6" aria-hidden="true" /></span>
                        <div>
                            <h3 class="text-sm font-bold text-[#172c50]">Kembali ke atas</h3>
                            <p class="mt-1 text-xs leading-relaxed text-slate-500">Setelah halaman digulir, tombol ini akan muncul di kanan bawah untuk kembali ke bagian atas halaman.</p>
                        </div>
                    </article>
                </div>
            </section>

            <!-- Section 5: Bottom Banner (Akses Untuk Semua) -->
            <section aria-labelledby="inclusive-cta-title" class="relative mt-16 overflow-hidden rounded-3xl bg-[#0b3b8c] text-white shadow-xl">
                <!-- Background image with overlay -->
                <img src="/Assets/Images/accessibility/inclusive-travel-banner.jpg" alt="" class="absolute inset-0 h-full w-full object-cover object-center opacity-45" />
                <div class="absolute inset-0 bg-gradient-to-r from-[#072d6e]/95 via-[#0b3b8c]/85 to-transparent"></div>

                <div class="relative z-10 flex flex-col justify-between gap-8 p-8 sm:p-12 lg:flex-row lg:items-center lg:p-14">
                    <div class="max-w-xl">
                        <p class="text-xs font-bold uppercase tracking-[0.16em] text-cyan-200">AKSES UNTUK SEMUA</p>
                        <h2 id="inclusive-cta-title" class="mt-3 text-2xl font-extrabold leading-tight tracking-tight sm:text-3xl lg:text-4xl">Perjalanan yang lebih inklusif<br />dimulai dari sini.</h2>
                        <p class="mt-3 max-w-lg text-xs leading-relaxed text-white/80 sm:text-sm">Tapak Lokal berkomitmen untuk menghadirkan pengalaman menjelajah yang nyaman dan setara untuk semua orang.</p>
                        <Link href="/" class="mt-6 inline-flex items-center gap-2 rounded-xl bg-white px-6 py-3 text-xs font-bold text-[#172c50] shadow-md transition-colors hover:bg-slate-100 sm:text-sm">
                            Mulai jelajah sekarang <ArrowRight class="size-4" aria-hidden="true" />
                        </Link>
                    </div>

                    <div class="hidden text-right lg:block">
                        <p class="font-serif text-2xl italic text-white/90 drop-shadow-md sm:text-3xl">Lebih Banyak<br />Cerita, Untuk Semua</p>
                        <svg class="ml-auto mt-2 h-3 w-44 text-cyan-300 opacity-80" viewBox="0 0 180 12" fill="none">
                            <path d="M2 9C45 2 135 2 178 9" stroke="currentColor" stroke-width="3" stroke-linecap="round" />
                        </svg>
                    </div>
                </div>
            </section>
        </main>
    </div>
</template>
