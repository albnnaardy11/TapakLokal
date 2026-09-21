<script setup>
import { Head, Link } from '@inertiajs/vue3';
import {
    ALargeSmall,
    ArrowLeftRight,
    ArrowRight,
    ArrowUp,
    BookmarkCheck,
    Compass,
    Contrast,
    ImageOff,
    Keyboard,
    Link2,
    Mouse,
    MoveVertical,
    Palette,
    PauseCircle,
    RotateCcw,
    ScanLine,
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
            <!-- Hero Section (1:1 with Design Mockup) -->
            <section aria-labelledby="guide-title" class="relative overflow-hidden rounded-[32px] sm:rounded-[40px] border border-[#dce8f8] bg-[#f8fbff] p-8 sm:p-12 lg:p-12 xl:p-14 shadow-[0_4px_24px_rgba(37,99,235,0.03)]">
                <div class="grid grid-cols-1 items-center gap-10 lg:grid-cols-[1.1fr_1fr] lg:gap-8 xl:gap-12">
                    <!-- Left Content -->
                    <div>
                        <h1 id="guide-title" class="text-4xl font-extrabold tracking-tight text-[#0f172a] sm:text-5xl lg:text-[54px] leading-[1.08]">
                            Jelajah lebih nyaman,<br />
                            <span class="text-[#2563eb]">dengan caramu.</span>
                        </h1>
                        <p class="mt-6 max-w-xl text-base leading-relaxed text-[#475569] sm:text-[17px]">
                            Sesuaikan kenyamanan membaca dan navigasi di TapakLokal. Mulai dari memperbesar teks, mengatur kontras warna, hingga menggunakan bantuan suara dan keyboard.
                        </p>

                        <!-- Quick navigation & action buttons -->
                        <div class="mt-8 flex flex-wrap items-center gap-3.5 sm:gap-4">
                            <a
                                href="#mulai"
                                class="inline-flex items-center gap-2 rounded-full bg-[#2563eb] px-7 py-3.5 text-sm font-bold text-white shadow-[0_8px_20px_rgba(37,99,235,0.25)] transition-all duration-200 hover:bg-[#1d4ed8] hover:shadow-[0_12px_24px_rgba(37,99,235,0.35)] hover:-translate-y-0.5 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-[#2563eb] sm:px-8 sm:py-4 sm:text-base"
                            >
                                Mulai Panduan
                                <ArrowRight class="size-4 stroke-[2.5]" aria-hidden="true" />
                            </a>
                            <button
                                type="button"
                                class="inline-flex items-center gap-2 rounded-full border border-[#e2e8f0] bg-white px-6 py-3.5 text-sm font-bold text-[#1e293b] shadow-xs transition-all duration-200 hover:bg-slate-50 hover:border-[#cbd5e1] hover:shadow-sm hover:-translate-y-0.5 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-[#2563eb] sm:px-7 sm:py-4 sm:text-base"
                                @click="openAccessibilityPanel"
                            >
                                <SlidersHorizontal class="size-4 text-[#2563eb] stroke-[2.2]" aria-hidden="true" />
                                Buka Panel Aksesibilitas
                            </button>
                        </div>
                    </div>

                    <!-- Right Graphic -->
                    <div class="flex items-center justify-center lg:justify-end">
                        <img
                            src="/Assets/Images/accessibility/hero.webp"
                            alt="Ilustrasi Panel Aksesibilitas dan Destinasi TapakLokal"
                            class="w-full max-w-[540px] h-auto object-contain select-none drop-shadow-xs lg:max-w-[580px] xl:max-w-[620px]"
                        />
                    </div>
                </div>
            </section>

            <!-- Section 1: Tiga langkah untuk lebih nyaman -->
            <section id="mulai" aria-labelledby="start-title" class="mt-14 scroll-mt-32 rounded-[28px] border border-[#d8eafb] bg-[#edf5fe] p-6 sm:p-8 lg:p-10">
                <div class="grid gap-8 lg:grid-cols-[1fr_2.4fr] lg:items-center">
                    <div>
                        <p class="text-xs font-bold uppercase tracking-[0.14em] text-[#2563eb]">MULAI DENGAN MUDAH</p>
                        <h2 id="start-title" class="mt-2 text-2xl font-extrabold tracking-tight text-[#111c38] sm:text-3xl">Tiga langkah untuk<br />lebih nyaman.</h2>
                        <p class="mt-3 text-xs leading-relaxed text-[#556987] sm:text-sm">Ikuti langkah ini untuk menyesuaikan tampilan sesuai kebutuhanmu.</p>
                    </div>
                    <div class="grid gap-4 sm:grid-cols-3 sm:gap-5">
                        <div v-for="(step, index) in steps" :key="step.title" class="relative flex flex-col items-center justify-center rounded-[24px] border border-[#e2eaf4] bg-white p-6 sm:p-7 text-center shadow-[0_2px_12px_rgba(23,44,80,0.03)] min-h-[220px] transition-all duration-200 hover:border-[#b8d2fc] hover:shadow-[0_8px_24px_rgba(37,99,235,0.06)]">
                            <span class="absolute left-4 top-4 grid size-7 place-items-center rounded-full bg-[#2563eb] text-xs font-extrabold text-white shadow-sm">{{ index + 1 }}</span>
                            <span class="mt-1 grid size-14 place-items-center rounded-2xl bg-[#edf5fe] text-[#2563eb]"><component :is="step.icon" class="size-7 stroke-[2.2]" aria-hidden="true" /></span>
                            <h3 class="mt-4 text-base font-bold text-[#111c38]">{{ step.title }}</h3>
                            <p class="mt-1.5 text-xs sm:text-[13px] leading-relaxed text-[#556987]">{{ step.description }}</p>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Section 2: Fitur aksesibilitas (12 cards in 4-column grid) -->
            <section id="fitur" aria-labelledby="features-title" class="mt-16 scroll-mt-32">
                <div>
                    <p class="text-xs font-bold uppercase tracking-[0.14em] text-[#2563eb]">SESUAIKAN DENGAN KEBUTUHANMU</p>
                    <h2 id="features-title" class="mt-2 text-2xl font-extrabold tracking-tight text-[#111c38] sm:text-3xl">Fitur aksesibilitas.</h2>
                    <p class="mt-2 max-w-2xl text-xs leading-relaxed text-[#556987] sm:text-sm">Pilih dan atur tampilan sesuai preferensimu. Semua perubahan bisa dilakukan langsung di panel aksesibilitas tanpa mengubah isi cerita dan informasi perjalanan.</p>
                </div>

                <div class="mt-8 grid gap-4 sm:grid-cols-2 lg:grid-cols-4 sm:gap-5 lg:gap-5">
                    <article v-for="feature in features" :key="feature.title" class="flex flex-col justify-between rounded-[22px] border border-[#e4edf7] bg-white p-5 sm:p-6 shadow-[0_2px_8px_rgba(23,44,80,0.02)] transition-all duration-200 hover:border-[#b8d2fc] hover:shadow-[0_8px_20px_rgba(37,99,235,0.06)]">
                        <div>
                            <span class="mb-4 grid size-11 place-items-center rounded-2xl bg-[#edf5fe] text-[#2563eb]"><component :is="feature.icon" class="size-5" aria-hidden="true" /></span>
                            <h3 class="text-base font-bold text-[#111c38]">{{ feature.title }}</h3>
                            <p class="mt-1.5 text-xs sm:text-[13px] leading-relaxed text-[#556987]">{{ feature.description }}</p>
                        </div>
                        <div class="mt-5 rounded-xl bg-[#edf5fe] px-3 py-2 text-center text-xs font-semibold text-[#2563eb]">
                            {{ feature.options }}
                        </div>
                    </article>
                </div>
            </section>

            <!-- Section 3: Tetap mudah, tanpa mouse (1:1 with design reference) -->
            <section id="keyboard" aria-labelledby="keyboard-title" class="relative mt-16 scroll-mt-32 overflow-hidden rounded-[28px] border border-[#d8eafb] bg-[#edf5fe]">
                <div class="grid grid-cols-1 lg:grid-cols-[280px_1fr_390px] xl:grid-cols-[310px_1fr_410px] items-stretch min-h-[320px]">
                    <!-- Left: Laptop Image full height from top to bottom -->
                    <div class="relative h-64 sm:h-72 lg:h-full w-full overflow-hidden flex items-center justify-start pointer-events-none select-none">
                        <img
                            src="/Assets/Images/accessibility/laptop-closeup.jpg"
                            alt="Keyboard navigasi aksesibilitas"
                            class="h-full w-full object-cover object-left mix-blend-multiply"
                        />
                    </div>

                    <!-- Middle: Title & Description aligned at the top -->
                    <div class="flex flex-col justify-start px-6 py-6 sm:px-8 lg:pt-11 lg:pb-6 lg:pl-6 lg:pr-6">
                        <h2 id="keyboard-title" class="text-3xl sm:text-4xl lg:text-[38px] xl:text-[40px] font-extrabold tracking-tight text-[#111c38] leading-[1.12]">
                            Tetap mudah,<br />tanpa mouse.
                        </h2>
                        <p class="mt-4 max-w-[340px] text-sm sm:text-base lg:text-[15px] leading-relaxed text-[#4b5e76]">
                            Gunakan keyboard untuk membuka dan mengatur panel. Garis fokus menunjukkan kontrol yang sedang dipilih.
                        </p>
                    </div>

                    <!-- Right: White Card Container with Shortcuts -->
                    <div class="flex items-center p-6 sm:p-8 lg:p-5 lg:pr-7">
                        <div class="w-full rounded-[22px] bg-white p-5 sm:p-6 shadow-sm border border-[#e2eaf4]">
                            <dl class="divide-y divide-[#f1f5f9]">
                                <div v-for="shortcut in shortcuts" :key="shortcut.key" class="flex items-center gap-3.5 py-2.5 first:pt-0 last:pb-0">
                                    <dt class="shrink-0">
                                        <kbd class="inline-flex min-w-[76px] items-center justify-center rounded-full bg-[#edf4ff] px-3.5 py-1 text-center font-sans text-xs font-bold text-[#172c50]">{{ shortcut.key }}</kbd>
                                    </dt>
                                    <dd class="text-xs sm:text-[13.5px] font-medium text-[#475569]">{{ shortcut.description }}</dd>
                                </div>
                            </dl>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Section 4: Pengaturan tetap dalam kendalimu (Traveloka Style) -->
            <section id="pengaturan" aria-labelledby="settings-title" class="mt-16 scroll-mt-32">
                <div class="flex flex-col gap-2 sm:flex-row sm:items-end sm:justify-between">
                    <div>
                        <p class="text-xs font-bold uppercase tracking-[0.14em] text-[#2563eb]">KEMUDAHAN KONTROL</p>
                        <h2 id="settings-title" class="mt-2 text-2xl font-extrabold tracking-tight text-[#111c38] sm:text-3xl">Pengaturan tetap dalam kendalimu.</h2>
                        <p class="mt-1 text-xs text-[#556987] sm:text-sm">Semua preferensi tersimpan aman di browser perangkat ini untuk kunjungan berikutnya.</p>
                    </div>
                </div>

                <div class="mt-6 grid gap-5 md:grid-cols-3">
                    <article class="group relative flex flex-col justify-between rounded-[24px] border border-[#e2eaf4] bg-white p-6 shadow-[0_2px_12px_rgba(23,44,80,0.03)] transition-all duration-300 hover:-translate-y-1 hover:border-[#93c5fd] hover:shadow-[0_12px_28px_rgba(37,99,235,0.08)]">
                        <div>
                            <div class="flex items-center justify-between">
                                <span class="grid size-12 place-items-center rounded-2xl bg-gradient-to-br from-[#eff6ff] to-[#dbeafe] text-[#2563eb] shadow-sm border border-[#bfdbfe]/50 transition-transform duration-300 group-hover:scale-105">
                                    <BookmarkCheck class="size-6 stroke-[2.2]" aria-hidden="true" />
                                </span>
                                <span class="inline-flex items-center rounded-full bg-[#eff6ff] px-2.5 py-0.5 text-[11px] font-bold text-[#2563eb] border border-[#dbeafe]">
                                    Otomatis
                                </span>
                            </div>
                            <h3 class="mt-5 text-base font-bold text-[#111c38] group-hover:text-[#2563eb] transition-colors">Tersimpan otomatis</h3>
                            <p class="mt-2 text-xs sm:text-[13px] leading-relaxed text-[#556987]">Pilihan aksesibilitasmu akan tetap tersimpan aman di browser, bahkan setelah jendela ditutup.</p>
                        </div>
                    </article>

                    <article class="group relative flex flex-col justify-between rounded-[24px] border border-[#e2eaf4] bg-white p-6 shadow-[0_2px_12px_rgba(23,44,80,0.03)] transition-all duration-300 hover:-translate-y-1 hover:border-[#93c5fd] hover:shadow-[0_12px_28px_rgba(37,99,235,0.08)]">
                        <div>
                            <div class="flex items-center justify-between">
                                <span class="grid size-12 place-items-center rounded-2xl bg-gradient-to-br from-[#eff6ff] to-[#dbeafe] text-[#2563eb] shadow-sm border border-[#bfdbfe]/50 transition-transform duration-300 group-hover:scale-105">
                                    <RotateCcw class="size-6 stroke-[2.2]" aria-hidden="true" />
                                </span>
                                <span class="inline-flex items-center rounded-full bg-[#eff6ff] px-2.5 py-0.5 text-[11px] font-bold text-[#2563eb] border border-[#dbeafe]">
                                    1 Klik
                                </span>
                            </div>
                            <h3 class="mt-5 text-base font-bold text-[#111c38] group-hover:text-[#2563eb] transition-colors">Kembali ke standar</h3>
                            <p class="mt-2 text-xs sm:text-[13px] leading-relaxed text-[#556987]">Buka panel, lalu pilih “Reset semua pengaturan” kapan saja untuk kembali ke tampilan awal bawaan.</p>
                        </div>
                    </article>

                    <article class="group relative flex flex-col justify-between rounded-[24px] border border-[#e2eaf4] bg-white p-6 shadow-[0_2px_12px_rgba(23,44,80,0.03)] transition-all duration-300 hover:-translate-y-1 hover:border-[#93c5fd] hover:shadow-[0_12px_28px_rgba(37,99,235,0.08)]">
                        <div>
                            <div class="flex items-center justify-between">
                                <span class="grid size-12 place-items-center rounded-2xl bg-gradient-to-br from-[#eff6ff] to-[#dbeafe] text-[#2563eb] shadow-sm border border-[#bfdbfe]/50 transition-transform duration-300 group-hover:scale-105">
                                    <ArrowUp class="size-6 stroke-[2.2]" aria-hidden="true" />
                                </span>
                                <span class="inline-flex items-center rounded-full bg-[#eff6ff] px-2.5 py-0.5 text-[11px] font-bold text-[#2563eb] border border-[#dbeafe]">
                                    Pintasan
                                </span>
                            </div>
                            <h3 class="mt-5 text-base font-bold text-[#111c38] group-hover:text-[#2563eb] transition-colors">Kembali ke atas</h3>
                            <p class="mt-2 text-xs sm:text-[13px] leading-relaxed text-[#556987]">Setelah halaman digulir, tombol pintas akan otomatis muncul di sudut kanan bawah untuk navigasi cepat.</p>
                        </div>
                    </article>
                </div>
            </section>

            <!-- Section 5: Bottom Banner (Akses Untuk Semua) -->
            <section aria-labelledby="inclusive-cta-title" class="relative mt-16 overflow-hidden rounded-[28px] bg-[#072454] text-white shadow-xl">
                <!-- Background image with smooth subtle overlay -->
                <img src="/Assets/Images/accessibility/inclusive-travel-banner.jpg" alt="" class="absolute inset-0 h-full w-full object-cover object-center opacity-85" />
                <div class="absolute inset-0 bg-gradient-to-r from-[#041838]/85 via-[#072960]/50 to-transparent"></div>

                <div class="relative z-10 flex flex-col justify-between gap-8 p-8 sm:p-12 lg:flex-row lg:items-center lg:p-14">
                    <div class="max-w-xl drop-shadow-[0_2px_8px_rgba(0,0,0,0.25)]">
                        <p class="text-xs font-bold uppercase tracking-[0.16em] text-cyan-300">AKSES UNTUK SEMUA</p>
                        <h2 id="inclusive-cta-title" class="mt-3 text-2xl font-extrabold leading-tight tracking-tight text-white sm:text-3xl lg:text-4xl">Perjalanan yang lebih inklusif<br />dimulai dari sini.</h2>
                        <p class="mt-3 max-w-lg text-xs leading-relaxed text-white/90 sm:text-sm">Tapak Lokal berkomitmen untuk menghadirkan pengalaman menjelajah yang nyaman dan setara untuk semua orang.</p>
                        <Link href="/" class="mt-6 inline-flex items-center gap-2 rounded-full bg-white px-7 py-3.5 text-xs font-bold text-[#172c50] shadow-lg transition-all hover:bg-slate-100 hover:scale-[1.02] sm:text-sm">
                            Mulai jelajah sekarang <ArrowRight class="size-4" aria-hidden="true" />
                        </Link>
                    </div>

                    <div class="hidden text-right lg:block">
                        <p class="font-serif text-2xl italic text-white drop-shadow-[0_2px_8px_rgba(0,0,0,0.35)] sm:text-3xl">Lebih Banyak<br />Cerita, Untuk Semua</p>
                        <svg class="ml-auto mt-2 h-3 w-44 text-cyan-300 drop-shadow-sm opacity-90" viewBox="0 0 180 12" fill="none">
                            <path d="M2 9C45 2 135 2 178 9" stroke="currentColor" stroke-width="3" stroke-linecap="round" />
                        </svg>
                    </div>
                </div>
            </section>
        </main>
    </div>
</template>
