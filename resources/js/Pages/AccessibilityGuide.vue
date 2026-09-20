<script setup>
import { Head } from '@inertiajs/vue3';
import { ALargeSmall, ArrowDown, ArrowLeftRight, ArrowUp, BookOpen, Check, Contrast, Droplet, ImageOff, Keyboard, Link2, Monitor, Mouse, MoveVertical, PauseCircle, RotateCcw, Settings2, Type } from 'lucide-vue-next';
import MainNavigation from '../Components/Shared/MainNavigation.vue';

const features = [
    { title: 'Kontras', icon: Contrast, description: 'Ubah perbedaan warna teks dan latar agar konten lebih mudah dibaca.', options: 'Standar → Gelap → Terang → Tinggi', tip: 'Klik berulang untuk memilih tampilan yang nyaman untuk mata.' },
    { title: 'Spasi', icon: ArrowLeftRight, description: 'Perlebar jarak antarhuruf dan antarkata supaya teks tidak terasa rapat.', options: 'Standar → Sedang → Lebar → Sangat lebar', tip: 'Coba tingkat sedang terlebih dahulu, lalu sesuaikan kebutuhanmu.' },
    { title: 'Warna', icon: Droplet, description: 'Sesuaikan intensitas warna halaman tanpa mengubah isi kontennya.', options: 'Standar → Abu-abu → Saturasi rendah → Saturasi tinggi', tip: 'Pilih abu-abu untuk mengurangi distraksi warna saat membaca.' },
    { title: 'Ukuran teks', icon: ALargeSmall, description: 'Perbesar teks yang mengikuti skala ukuran halaman agar lebih mudah dibaca.', options: 'Standar → Besar → Lebih besar → Terbesar', tip: 'Ukuran bertambah menjadi 112,5%, 125%, lalu 150% dari ukuran dasar.' },
    { title: 'Sorot tautan', icon: Link2, description: 'Tambahkan garis bawah dan bingkai pada tautan agar tujuan navigasi lebih mudah dikenali.', options: 'Aktif / Nonaktif', tip: 'Klik sekali untuk mengaktifkan, lalu klik lagi untuk menonaktifkan.' },
    { title: 'Jeda animasi', icon: PauseCircle, description: 'Jeda animasi tampilan dan perpindahan otomatis carousel agar halaman lebih tenang.', options: 'Aktif / Nonaktif', tip: 'Kamu tetap bisa menggunakan tombol navigasi carousel secara manual.' },
    { title: 'Sembunyikan gambar', icon: ImageOff, description: 'Sembunyikan gambar dan latar bergambar untuk membantu fokus pada tulisan.', options: 'Aktif / Nonaktif', tip: 'Ruang gambar tetap dipertahankan agar susunan halaman tidak meloncat.' },
    { title: 'Ramah disleksia', icon: Type, description: 'Gunakan huruf sans-serif sederhana dengan jarak huruf, kata, dan baris yang lebih lega.', options: 'Aktif / Nonaktif', tip: 'Kenyamanan membaca berbeda untuk setiap orang. Kombinasikan dengan pengaturan spasi dan tinggi baris.' },
    { title: 'Kursor besar', icon: Mouse, description: 'Perbesar penunjuk mouse agar lebih mudah ditemukan saat menjelajahi halaman.', options: 'Aktif / Nonaktif', tip: 'Efek ini terlihat pada perangkat dengan mouse atau trackpad, bukan sentuhan.' },
    { title: 'Tinggi baris', icon: MoveVertical, description: 'Tambah jarak antarbaris agar lebih mudah mengikuti teks dari satu baris ke baris berikutnya.', options: 'Standar → Sedang → Renggang → Sangat renggang', tip: 'Gunakan bersama ukuran teks yang paling nyaman untukmu.' },
];
const steps = [
    { title: 'Buka panel', description: 'Tekan tombol bulat dengan simbol aksesibilitas di kanan bawah halaman.' },
    { title: 'Sesuaikan tampilan', description: 'Pilih fitur yang kamu butuhkan. Klik berulang untuk mengganti tingkat atau menonaktifkannya.' },
    { title: 'Lanjutkan menjelajah', description: 'Tutup panel dengan tombol × atau Escape. Pilihanmu tetap aktif saat berpindah halaman.' },
];
const shortcuts = [
    { key: 'Tab', description: 'Pindah ke kontrol berikutnya.' },
    { key: 'Shift + Tab', description: 'Kembali ke kontrol sebelumnya.' },
    { key: 'Enter', description: 'Aktifkan tombol atau buka tautan yang sedang dipilih.' },
    { key: 'Spasi', description: 'Aktifkan tombol yang sedang dipilih.' },
    { key: 'Escape', description: 'Tutup panel aksesibilitas.' },
];
</script>

<template>
    <Head title="Panduan Aksesibilitas">
        <meta name="description" content="Kenali pengaturan aksesibilitas TapakLokal: kontras, ukuran teks, spasi, warna, dan fitur lainnya untuk pengalaman menjelajah yang lebih nyaman." />
    </Head>
    <div class="min-h-screen bg-[#f8fafc] font-sans text-[#172c50]">
        <MainNavigation />
        <main class="mx-auto max-w-[1276px] px-5 pb-28 pt-8 sm:px-10 lg:px-12 lg:pt-12">
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
                        <ul class="mt-5 space-y-3 text-sm font-medium"><li v-for="benefit in ['10 fitur yang bisa dikombinasikan', 'Pilihan tersimpan di browser ini', 'Bisa kembali ke tampilan standar']" :key="benefit" class="flex items-start gap-2"><Check class="mt-0.5 size-4 shrink-0 text-[#3E7BEF]" aria-hidden="true" />{{ benefit }}</li></ul>
                    </div>
                </div>
            </section>

            <nav aria-label="Daftar isi panduan" class="mt-6 flex flex-wrap gap-2 text-sm font-semibold">
                <a v-for="item in [{ href: '#mulai', label: 'Cara menggunakan' }, { href: '#fitur', label: 'Kenali semua fitur' }, { href: '#keyboard', label: 'Navigasi keyboard' }, { href: '#pengaturan', label: 'Simpan & reset' }]" :key="item.href" :href="item.href" class="rounded-full border border-[#e1eaf3] bg-white px-4 py-2.5 text-slate-600 transition-colors hover:border-[#3E7BEF] hover:text-[#3E7BEF] focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-[#3E7BEF]">{{ item.label }}</a>
            </nav>

            <section id="mulai" aria-labelledby="start-title" class="mt-14 scroll-mt-32 sm:mt-16">
                <p class="text-xs font-bold uppercase tracking-[0.14em] text-[#3E7BEF]">MULAI DENGAN MUDAH</p>
                <h2 id="start-title" class="mt-2 text-2xl font-extrabold tracking-tight sm:text-3xl">Tiga langkah untuk lebih nyaman.</h2>
                <ol class="mt-7 grid gap-5 md:grid-cols-3">
                    <li v-for="(step, index) in steps" :key="step.title" class="rounded-2xl border border-[#e1eaf3] bg-white p-6"><span class="grid size-10 place-items-center rounded-full bg-[#edf4ff] text-sm font-extrabold text-[#3E7BEF]">0{{ index + 1 }}</span><h3 class="mt-5 text-lg font-bold">{{ step.title }}</h3><p class="mt-2 text-sm leading-6 text-slate-600">{{ step.description }}</p></li>
                </ol>
            </section>

            <section id="fitur" aria-labelledby="features-title" class="mt-16 scroll-mt-32">
                <p class="text-xs font-bold uppercase tracking-[0.14em] text-[#3E7BEF]">SESUAIKAN KEBUTUHANMU</p>
                <h2 id="features-title" class="mt-2 text-2xl font-extrabold tracking-tight sm:text-3xl">Kenali setiap fitur.</h2>
                <p class="mt-3 max-w-2xl text-sm leading-6 text-slate-600">Kartu berwarna biru menandakan fitur aktif. Pada fitur bertingkat, klik hingga kembali ke pilihan standar untuk menonaktifkannya.</p>
                <div class="mt-7 grid gap-5 md:grid-cols-2">
                    <article v-for="feature in features" :key="feature.title" class="rounded-2xl border border-[#e1eaf3] bg-white p-6 sm:p-7">
                        <div class="flex items-center gap-3"><span class="grid size-11 shrink-0 place-items-center rounded-xl bg-[#edf4ff] text-[#3E7BEF]"><component :is="feature.icon" class="size-5" aria-hidden="true" /></span><h3 class="text-lg font-bold">{{ feature.title }}</h3></div>
                        <p class="mt-4 text-sm leading-6 text-slate-600">{{ feature.description }}</p>
                        <p class="mt-4 rounded-xl bg-[#f5f8fd] px-4 py-3 text-xs font-semibold leading-6 text-[#31577f]">{{ feature.options }}</p>
                        <p class="mt-3 text-sm leading-6 text-slate-500">{{ feature.tip }}</p>
                    </article>
                </div>
            </section>

            <section id="keyboard" aria-labelledby="keyboard-title" class="mt-16 scroll-mt-32 rounded-2xl border border-[#e1eaf3] bg-white p-6 sm:p-8">
                <div class="grid gap-8 lg:grid-cols-[0.85fr_1.15fr] lg:gap-14">
                    <div><Keyboard class="size-7 text-[#3E7BEF]" aria-hidden="true" /><h2 id="keyboard-title" class="mt-4 text-2xl font-extrabold tracking-tight sm:text-3xl">Tetap mudah,<br />tanpa mouse.</h2><p class="mt-3 text-sm leading-6 text-slate-600">Gunakan keyboard untuk membuka dan mengatur panel. Garis fokus menunjukkan kontrol yang sedang dipilih.</p></div>
                    <dl class="divide-y divide-slate-100"><div v-for="shortcut in shortcuts" :key="shortcut.key" class="flex flex-col gap-2 py-3 first:pt-0 sm:flex-row sm:items-center sm:gap-5"><dt class="shrink-0 sm:w-28"><kbd class="inline-block rounded-lg border border-slate-200 bg-slate-50 px-3 py-1.5 font-sans text-xs font-bold">{{ shortcut.key }}</kbd></dt><dd class="text-sm leading-6 text-slate-600">{{ shortcut.description }}</dd></div></dl>
                </div>
            </section>

            <section id="pengaturan" aria-labelledby="settings-title" class="mt-16 scroll-mt-32">
                <h2 id="settings-title" class="text-2xl font-extrabold tracking-tight sm:text-3xl">Pengaturan tetap dalam kendalimu.</h2>
                <div class="mt-7 grid gap-5 md:grid-cols-3">
                    <article class="rounded-2xl border border-[#d8eafb] bg-[#edf4ff] p-6"><Monitor class="size-6 text-[#3E7BEF]" aria-hidden="true" /><h3 class="mt-4 text-lg font-bold">Tersimpan otomatis</h3><p class="mt-2 text-sm leading-6 text-slate-600">Pilihanmu disimpan di browser pada perangkat ini, termasuk setelah refresh. Di perangkat atau browser lain, atur kembali sesuai kebutuhan. Jika penyimpanan browser diblokir, pilihan tidak bertahan setelah refresh.</p></article>
                    <article class="rounded-2xl border border-[#d8eafb] bg-[#edf4ff] p-6"><RotateCcw class="size-6 text-[#3E7BEF]" aria-hidden="true" /><h3 class="mt-4 text-lg font-bold">Kembali ke standar</h3><p class="mt-2 text-sm leading-6 text-slate-600">Buka panel, gulir ke bawah, lalu tekan “Reset semua pengaturan”. Seluruh fitur kembali ke kondisi awal sekaligus.</p></article>
                    <article class="rounded-2xl border border-[#d8eafb] bg-[#edf4ff] p-6"><ArrowUp class="size-6 text-[#3E7BEF]" aria-hidden="true" /><h3 class="mt-4 text-lg font-bold">Kembali ke atas</h3><p class="mt-2 text-sm leading-6 text-slate-600">Setelah halaman digulir, tombol panah biru muncul di kanan bawah. Tekan untuk kembali ke bagian atas halaman.</p></article>
                </div>
            </section>
            <p class="mt-12 text-center text-sm leading-6 text-slate-500">Tidak ada satu pengaturan yang cocok untuk semua orang. Pilih kombinasi yang paling nyaman untukmu.</p>
        </main>
    </div>
</template>
