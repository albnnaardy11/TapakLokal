<script setup>
import { ref, computed } from 'vue';
import { ChevronDown, MessageCircle, HelpCircle } from 'lucide-vue-next';

const props = defineProps({
    questions: {
        type: Array,
        default: () => [],
    },
});

const defaultFaqs = [
    {
        id: 1,
        category: 'Trip',
        question: 'Apa perbedaan mendasar antara Open Trip dan Private Trip?',
        answer: 'Open Trip adalah perjalanan wisata gabungan bersama traveler lain dengan jadwal dan kuota yang sudah ditentukan, sehingga biaya perjalanan menjadi jauh lebih hemat dan terjangkau. Sedangkan Private Trip adalah perjalanan eksklusif khusus Anda dan rombongan pribadi (keluarga/teman) dengan kebebasan penuh menentukan tanggal keberangkatan, titik kumpul, serta rute destinasi yang lebih fleksibel.',
    },
    {
        id: 2,
        category: 'Trip',
        question: 'Bagaimana jika kuota minimum peserta Open Trip tidak terpenuhi?',
        answer: 'Setiap paket Open Trip memiliki kuota minimum peserta yang tertera di detail perjalanan. Jika hingga batas waktu H-3 kuota belum tercapai, pihak mitra operator akan memberikan 3 solusi terbaik: reschedule ke tanggal berikutnya, penyesuaian biaya untuk rombongan kecil, atau garansi 100% uang kembali (full refund) tanpa potongan ke saldo TapakWallet Anda.',
    },
    {
        id: 3,
        category: 'Trip',
        question: 'Apakah rute, jadwal, dan meeting point Private Trip bisa disesuaikan (custom)?',
        answer: 'Tentu bisa! Pada Private Trip, Anda memiliki fleksibilitas penuh untuk menentukan titik kumpul (meeting point), jam keberangkatan, serta berdiskusi dengan operator untuk menyesuaikan destinasi, durasi kunjungan, maupun aktivitas khusus sesuai kenyamanan rombongan Anda.',
    },
    {
        id: 4,
        category: 'Pembayaran',
        question: 'Apa saja metode pembayaran resmi yang tersedia di TapakLokal?',
        answer: 'TapakLokal bermitra resmi dengan 7 metode pembayaran terverifikasi: Virtual Account Bank BCA, Virtual Account Bank Mandiri, Uang Elektronik (GoPay & OVO), Gerai Minimarket (Indomaret & Alfamart), serta saldo internal resmi TapakWallet dengan verifikasi sistem otomatis 24 jam.',
    },
    {
        id: 5,
        category: 'Pembayaran',
        question: 'Apa keuntungan melakukan pembayaran menggunakan TapakWallet?',
        answer: 'Pembayaran dengan TapakWallet 100% bebas biaya administrasi. E-tiket dan invoice Anda akan langsung terbit secara instan dalam 1 klik tanpa perlu transfer antar bank atau unggah bukti transfer manual. Selain itu, TapakWallet menjamin pengembalian dana (refund) tercepat jika terjadi pembatalan perjalanan.',
    },
    {
        id: 6,
        category: 'Pembayaran',
        question: 'Bagaimana kebijakan pembatalan pemesanan dan pengembalian dana (refund)?',
        answer: 'Pengajuan pembatalan dapat dilakukan langsung dari menu Akun Saya > Riwayat Pesanan. Nilai pengembalian dana akan dihitung secara transparan berdasarkan tenggat waktu pembatalan sesuai ketentuan masing-masing trip, dan saldo refund langsung dikembalikan ke akun Anda.',
    },
    {
        id: 7,
        category: 'Trip',
        question: 'Fasilitas apa saja yang sudah termasuk dalam paket perjalanan?',
        answer: 'Fasilitas yang disediakan bergantung pada jenis trip, namun umumnya sudah mencakup transportasi ber-AC/kapal wisata, tiket masuk objek wisata, pemandu lokal berlisensi, perlengkapan keselamatan (life jacket/alat snorkeling), makan sesuai jadwal, dan asuransi. Seluruh rincian tercantum transparan pada tab Fasilitas di setiap halaman detail trip.',
    },
];

const displayQuestions = computed(() => {
    return props.questions && props.questions.length > 0 ? props.questions : defaultFaqs;
});

// Manage open accordion IDs
const openQuestions = ref([1]); // First question open by default for immediate preview

const toggleQuestion = (id) => {
    if (openQuestions.value.includes(id)) {
        openQuestions.value = openQuestions.value.filter((qId) => qId !== id);
    } else {
        openQuestions.value = [...openQuestions.value, id];
    }
};
</script>

<template>
    <section class="mx-auto max-w-[1180px] px-1" aria-labelledby="catalog-faq-heading">
        <div class="grid items-stretch gap-5 sm:gap-6 lg:grid-cols-[360px_minmax(0,1fr)]">
            <!-- Left Poster Card (1:1 with Homepage TravelFaq Style) -->
            <div class="relative isolate flex min-h-[320px] flex-col justify-between overflow-hidden rounded-3xl bg-[#092244] p-7 sm:p-8 text-white shadow-xs">
                <!-- Background Image with Scenery & Contrast Gradients -->
                <img
                    src="https://images.unsplash.com/photo-1507525428034-b723cf961d3e?auto=format&fit=crop&w=900&q=85"
                    alt="Pemandangan pesisir pantai pulau di Indonesia"
                    loading="lazy"
                    class="absolute inset-0 -z-20 size-full object-cover brightness-[0.75]"
                />
                <div class="absolute inset-0 -z-10 bg-gradient-to-b from-[#061d3d]/90 via-[#061d3d]/70 to-[#04142b]/95"></div>

                <div>
                    <!-- Category Badge -->
                    <span class="inline-flex items-center gap-2 rounded-full border border-white/25 bg-white/10 px-3 py-1.5 text-[10px] font-bold uppercase tracking-wider text-white backdrop-blur-xs">
                        <MessageCircle class="size-3.5 text-[#38bdf8]" aria-hidden="true" />
                        PANDUAN & TEKNIS TRIP
                    </span>

                    <!-- Main Heading -->
                    <h2 id="catalog-faq-heading" class="mt-5 text-2xl sm:text-3xl font-extrabold leading-tight tracking-tight text-white">
                        Pertanyaan<br />Seputar Trip & Pembayaran
                    </h2>

                    <!-- Subtitle -->
                    <p class="mt-3 text-xs sm:text-sm leading-relaxed text-slate-200">
                        Pelajari alur Open Trip, Private Trip, dan sistem transaksi resmi kami sebelum memulai petualangan Anda.
                    </p>
                </div>

                <!-- Bottom Helper Indicator -->
                <div class="mt-6 flex items-center gap-2 text-xs font-semibold text-sky-200">
                    <HelpCircle class="size-4 shrink-0 text-[#38bdf8]" />
                    <span>Ada pertanyaan lain? Layanan bantuan aktif 24/7</span>
                </div>
            </div>

            <!-- Right Accordion Box (1:1 with Homepage TravelFaq Style) -->
            <div
                id="catalog-faq-questions"
                class="overflow-hidden rounded-3xl border border-[#e2edfa] bg-white px-5 sm:px-7 shadow-[0_4px_20px_rgba(23,75,120,0.04)]"
            >
                <div
                    v-for="(item, index) in displayQuestions"
                    :key="item.id"
                    class="border-b border-[#edf2f8] last:border-b-0"
                >
                    <h3>
                        <button
                            :id="`faq-trigger-${item.id}`"
                            type="button"
                            class="group flex min-h-[66px] w-full items-center gap-3.5 py-4 text-left text-sm sm:text-[14.5px] font-bold text-[#172c50] transition-colors hover:text-[#0088ff] focus-visible:outline-2 focus-visible:outline-offset-[-2px] focus-visible:outline-[#0088ff]"
                            :aria-expanded="openQuestions.includes(item.id)"
                            :aria-controls="`faq-answer-${item.id}`"
                            @click="toggleQuestion(item.id)"
                        >
                            <!-- Number Indicator -->
                            <span class="text-[11px] font-bold tabular-nums text-[#91a7be] group-hover:text-[#0088ff] transition-colors">
                                {{ String(index + 1).padStart(2, '0') }}
                            </span>

                            <!-- Question Text -->
                            <span class="flex-1 leading-snug">{{ item.question }}</span>

                            <!-- Rotating Chevron Down Icon -->
                            <span
                                class="grid size-7 shrink-0 place-items-center rounded-full transition-colors duration-300 group-hover:bg-[#edf6ff]"
                                :class="openQuestions.includes(item.id) ? 'bg-[#edf6ff] text-[#0088ff]' : 'bg-[#f6f8fb] text-slate-400'"
                            >
                                <ChevronDown
                                    class="size-4 transition-transform duration-300 motion-reduce:transition-none"
                                    :class="{ 'rotate-180': openQuestions.includes(item.id) }"
                                    aria-hidden="true"
                                />
                            </span>
                        </button>
                    </h3>

                    <!-- Expandable Answer with Smooth Grid-Template-Rows Transition -->
                    <div
                        :id="`faq-answer-${item.id}`"
                        role="region"
                        :aria-labelledby="`faq-trigger-${item.id}`"
                        :aria-hidden="!openQuestions.includes(item.id)"
                        class="grid transition-[grid-template-rows,opacity] duration-300 ease-out motion-reduce:transition-none"
                        :class="openQuestions.includes(item.id) ? 'grid-rows-[1fr] opacity-100' : 'grid-rows-[0fr] opacity-0'"
                    >
                        <div class="min-h-0 overflow-hidden">
                            <p class="pb-5 pl-7 pr-4 text-xs sm:text-[13px] leading-relaxed text-slate-500">
                                {{ item.answer }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</template>
