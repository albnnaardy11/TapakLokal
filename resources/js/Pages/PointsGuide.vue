<script setup>
import { computed, ref } from 'vue';
import { Head, Link } from '@inertiajs/vue3';
import { route } from 'ziggy-js';
import { ArrowRight, ArrowLeft, Coins, CircleCheck, History, Info, ChevronDown } from 'lucide-vue-next';
import MainNavigation from '../Components/Shared/MainNavigation.vue';
import MainFooter from '../Components/Shared/MainFooter.vue';

const activeSlide = ref(0);
const slides = [
    { image: '/Assets/Images/points-earned-phone.png', title: 'Perjalanan selesai. Poin bertambah.', description: 'Nikmati perjalananmu sampai tuntas. Poin tercatat otomatis ketika status perjalanan selesai.', icon: CircleCheck, label: 'DAPATKAN POIN', amount: '+25', caption: 'Contoh poin dari pesanan Rp250.000', items: ['Berlaku untuk perjalanan yang selesai', '1 poin untuk setiap Rp10.000', 'Hasil perhitungan dibulatkan ke bawah'] },
    { image: '/Assets/Images/points-history-phone.png', title: 'Kenali setiap perubahan saldomu.', description: 'Semua perolehan dan pengurangan tersimpan dalam satu riwayat. Kamu selalu tahu dari mana poin berasal.', icon: History, label: 'PANTAU AKTIVITAS', amount: '25', caption: 'Ilustrasi saldo setelah perjalanan selesai', items: ['Filter poin masuk dan pengurangan', 'Lihat tanggal dan referensi aktivitas', 'Buka detail perjalanan terkait'] },
    { image: '/Assets/Images/points-adjustment-phone.png', title: 'Poin yang jelas, tanpa tebak-tebakan.', description: 'Saldo mengikuti aktivitas yang tercatat. Pengembalian dana dapat menarik kembali poin dari pesanan terkait.', icon: Info, label: 'PAHAMI KETENTUAN', amount: '−25', caption: 'Contoh pengurangan poin karena refund', items: ['Refund dapat mengurangi poin', 'Penukaran poin belum tersedia', 'Masa kedaluwarsa belum diterapkan'] },
];
const slide = computed(() => slides[activeSlide.value]);
const move = step => { activeSlide.value = (activeSlide.value + step + slides.length) % slides.length; };
const questions = [
    { title: 'Apa itu Tapak Points?', text: 'Tapak Points adalah bentuk apresiasi untuk perjalanan yang diselesaikan melalui TapakLokal. Poin dicatat pada akun pemesan dan dapat dipantau melalui menu Points.' },
    { title: 'Bagaimana cara mendapatkan poin?', text: 'Pesan perjalanan, selesaikan pembayaran, lalu ikuti perjalanan sampai selesai. Sistem memberikan poin ketika status perjalanan berubah menjadi selesai.' },
    { title: 'Berapa poin yang saya dapatkan?', text: 'Setiap Rp10.000 dari total satu pesanan perjalanan menghasilkan 1 poin, dibulatkan ke bawah. Pesanan Rp250.000 menghasilkan 25 poin; Rp259.000 juga menghasilkan 25 poin.' },
    { title: 'Sudah membayar, mengapa poin belum masuk?', text: 'Pembayaran berhasil belum berarti perjalanan selesai. Poin masuk setelah perjalanan berstatus selesai. Jika status sudah selesai tetapi poin belum muncul, hubungi bantuan dengan referensi pesananmu.' },
    { title: 'Di mana saya melihat saldo dan riwayat poin?', text: 'Masuk ke akunmu, lalu buka menu Points. Gunakan filter Semua aktivitas, Poin masuk, atau Poin berkurang untuk menelusuri perubahan saldo.' },
    { title: 'Apa yang terjadi pada poin ketika pesanan mendapat refund?', text: 'Poin yang pernah diberikan untuk pesanan tersebut dapat ditarik kembali. Pengurangan tercatat di riwayat, sehingga kamu bisa menelusuri perubahan saldo.' },
    { title: 'Apakah poin dapat ditukar menjadi potongan harga?', text: 'Penukaran poin belum tersedia saat ini. Poin bukan saldo uang dan belum memiliki nilai tukar rupiah yang ditetapkan.' },
    { title: 'Apakah poin memiliki masa kedaluwarsa?', text: 'Masa kedaluwarsa poin belum diterapkan saat ini. Tidak ada estimasi kedaluwarsa yang ditampilkan sebelum ketentuannya tersedia.' },
];
</script>

<template>
    <Head title="Kenali Tapak Points" />
    <div class="overflow-x-hidden bg-white text-[#17345e]">
        <MainNavigation />
        <main>
            <section class="relative isolate bg-linear-to-br from-[#17345e] via-[#1768ce] to-[#0099ef] text-white">
                <div class="mx-auto grid max-w-6xl items-center gap-4 px-6 pb-10 pt-10 md:grid-cols-2 md:gap-10 md:pt-8">
                    <div class="relative order-2 mx-auto aspect-[1.1/1] w-full max-w-md self-end overflow-hidden md:order-1">
                        <img src="/Assets/Images/tapak-points-hiker-hero.png" alt="Pendaki perempuan membawa carrier, melihat ponsel, dengan koin Tapak Points di sekelilingnya" class="absolute inset-x-0 top-0 mx-auto h-auto w-full select-none" fetchpriority="high" />
                    </div>
                    <div class="order-1 md:order-2"><h1 class="text-4xl font-extrabold leading-tight tracking-tight lg:text-5xl">Cerita perjalananmu,<br /><span class="text-sky-200">punya nilai lebih.</span></h1><p class="mt-5 max-w-md text-sm leading-7 text-blue-100">Dapatkan Tapak Points dari perjalanan yang kamu selesaikan. Kenali cara kerjanya dan pantau setiap poin dari akunmu.</p><a href="#kenali-points" class="mt-6 inline-flex min-h-11 items-center gap-3 rounded-lg bg-white px-5 text-xs font-bold text-[#2866d4] transition hover:bg-sky-50">Kenali Tapak Points <ArrowRight class="size-4" /></a></div>
                </div>
            </section>

            <section id="kenali-points" class="relative -mt-10 scroll-mt-6 rounded-t-[2.5rem] bg-white px-6 pb-20 pt-16 sm:pb-24 sm:pt-20">
                <div class="mx-auto max-w-3xl text-center">
                    <p class="text-xs font-bold uppercase tracking-[0.2em] text-[#3e7bef]">Lebih dari sebuah perjalanan</p>
                    <h2 class="mt-3.5 text-3xl font-black tracking-tight text-[#17345e] sm:text-4xl sm:leading-tight">Pergi membawa cerita.<br class="sm:hidden" /> Pulang membawa poin.</h2>
                    <p class="mx-auto mt-4 max-w-2xl text-sm leading-relaxed text-slate-500 sm:text-base sm:leading-8">Tapak Points adalah apresiasi untuk kamu yang menjelajahi Indonesia bersama TapakLokal. Setiap perjalanan selesai jadi bagian dari riwayat poinmu.</p>
                </div>
                <div class="mx-auto mt-10 flex max-w-3xl flex-col items-center gap-6 rounded-3xl border border-[#dce6f4] bg-white p-7 shadow-[0_16px_50px_rgba(23,52,94,0.06)] sm:mt-12 sm:flex-row sm:gap-10 sm:p-10 md:gap-12 md:p-12">
                    <img src="/Assets/Images/tapak-points-wallet.png" alt="Ilustrasi dompet perjalanan biru dengan tiket dan koin Tapak Points" width="1280" height="1280" loading="lazy" class="size-36 shrink-0 object-contain drop-shadow-md sm:size-44 md:size-52" />
                    <div class="text-center sm:text-left">
                        <h3 class="text-xl font-black tracking-tight text-[#17345e] sm:text-2xl">Mulai dari perjalanan yang kamu suka</h3>
                        <p class="mt-3 text-sm leading-relaxed text-slate-500 sm:text-base sm:leading-7">Pesan perjalanan, selesaikan aktivitasnya, dan lihat poin bertambah otomatis. Semuanya tercatat di satu tempat.</p>
                    </div>
                </div>
            </section>

            <section class="relative bg-linear-to-b from-white to-[#edf6ff] px-5 pb-12 pt-4 sm:pb-16" aria-labelledby="benefits-heading">
                <div class="mx-auto max-w-4xl"><div class="text-center"><h2 id="benefits-heading" class="text-2xl font-extrabold sm:text-3xl">Kenali poinmu. Nikmati perjalananmu.</h2><p class="mt-3 text-sm leading-7 text-slate-500">Tiga hal sederhana untuk memahami Tapak Points.</p></div>
                    <div class="mt-8 overflow-hidden rounded-3xl border border-[#dce6f4] bg-white shadow-[0_16px_50px_rgba(23,52,94,0.07)]">
                        <div class="grid md:grid-cols-2">
                            <div class="p-6 sm:p-9" aria-live="polite"><p class="text-[10px] font-bold tracking-[0.18em] text-[#3e7bef]">0{{ activeSlide + 1 }} / {{ slide.label }}</p><h3 class="mt-4 text-2xl font-extrabold leading-snug">{{ slide.title }}</h3><p class="mt-4 text-xs leading-7 text-slate-500">{{ slide.description }}</p><ul class="mt-5 space-y-3"><li v-for="item in slide.items" :key="item" class="flex items-start gap-2 text-xs leading-5"><CircleCheck class="size-4 shrink-0 text-[#3e7bef]" />{{ item }}</li></ul></div>
                            <figure class="relative min-h-[340px] overflow-hidden bg-[#edf5ff] sm:min-h-[420px]"><img :src="slide.image" :key="slide.image" :alt="`Ilustrasi TapakLokal: ${slide.title}`" width="1448" height="1086" loading="lazy" class="absolute bottom-0 left-1/2 h-auto w-[125%] max-w-none -translate-x-1/2 object-contain" /></figure>
                        </div>
                        <div class="flex items-center justify-between border-t border-[#edf1f7] px-6 py-4"><div class="flex gap-2"><button v-for="(item, index) in slides" :key="item.label" type="button" :aria-label="`Tampilkan ${item.label.toLowerCase()}`" :aria-pressed="activeSlide === index" class="grid min-h-9 min-w-9 place-items-center rounded-lg hover:bg-blue-50" @click="activeSlide = index"><span class="h-2 rounded-full transition-all" :class="activeSlide === index ? 'w-6 bg-[#3e7bef]' : 'w-2 bg-blue-200'"></span></button></div><div class="flex gap-2"><button type="button" aria-label="Penjelasan sebelumnya" class="grid size-10 place-items-center rounded-full border border-[#dce6f4] text-[#3e7bef] hover:bg-blue-50" @click="move(-1)"><ArrowLeft class="size-4" /></button><button type="button" aria-label="Penjelasan berikutnya" class="grid size-10 place-items-center rounded-full border border-[#dce6f4] text-[#3e7bef] hover:bg-blue-50" @click="move(1)"><ArrowRight class="size-4" /></button></div></div>
                    </div>
                    <div class="mt-8 text-center"><Link :href="route('account.section', 'points')" class="inline-flex min-h-12 items-center gap-3 rounded-xl bg-[#3e7bef] px-7 text-sm font-bold text-white shadow-lg shadow-blue-200/50 transition hover:bg-[#2866d4]">Mulai perjalanan Points kamu <ArrowRight class="size-4" /></Link><p class="mt-3 text-[11px] text-slate-500">Masuk ke akun untuk melihat saldo dan aktivitasmu.</p></div>
                </div>
            </section>

            <section class="bg-[#edf6ff] px-5 pb-14 sm:pb-20" aria-labelledby="faq-heading"><div class="mx-auto grid max-w-4xl overflow-hidden rounded-3xl border border-[#dce6f4] bg-white md:grid-cols-[220px_1fr]"><div class="border-b border-[#edf1f7] p-7 md:border-b-0 md:border-r"><img src="/Assets/Images/tapak-points-faq.png" alt="Ilustrasi balon percakapan dan pertanyaan Tapak Points" width="1280" height="1280" loading="lazy" class="size-28 object-contain" /><h2 id="faq-heading" class="mt-5 text-xl font-extrabold leading-snug">Masih penasaran<br />tentang Points?</h2><p class="mt-3 text-xs leading-6 text-slate-500">Temukan jawaban sebelum memulai perjalanan berikutnya.</p></div><div class="px-5 sm:px-7"><details v-for="question in questions" :key="question.title" class="group border-b border-[#edf1f7] last:border-0"><summary class="flex cursor-pointer list-none items-center justify-between gap-4 py-5 text-xs font-semibold leading-6 [&::-webkit-details-marker]:hidden">{{ question.title }}<ChevronDown class="size-4 shrink-0 text-[#3e7bef] transition-transform group-open:rotate-180" /></summary><p class="pb-5 text-xs leading-7 text-slate-500">{{ question.text }}</p></details></div></div><div class="mx-auto mt-6 flex max-w-7xl items-start justify-center gap-3 text-left text-sm leading-6 text-slate-500 xl:items-center xl:whitespace-nowrap"><Info class="mt-0.5 size-5 shrink-0 text-[#3e7bef] xl:mt-0" /><p>Penukaran dan masa kedaluwarsa poin belum tersedia saat ini. Informasi di halaman ini mengikuti fitur Tapak Points yang sudah berjalan.</p></div></section>
        </main>
        <MainFooter />
    </div>
</template>
