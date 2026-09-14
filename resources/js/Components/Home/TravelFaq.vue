<script setup>
import { ArrowDown, ChevronDown, MessageCircle } from 'lucide-vue-next';
import { ref } from 'vue';

const openQuestions = ref([]);
const questions = [
    { id: 'trip-type', question: 'Apa bedanya Open Trip dan Private Trip?', answer: 'Open Trip mempertemukan kamu dengan peserta lain dalam satu jadwal perjalanan. Private Trip ditujukan untuk rombonganmu sendiri, sehingga pengaturan perjalanan dapat dibicarakan dengan partner penyelenggara.' },
    { id: 'schedule', question: 'Apakah bisa request tanggal?', answer: 'Open Trip mengikuti jadwal keberangkatan yang tersedia. Untuk Private Trip, kamu dapat mendiskusikan tanggal pilihan dengan partner. Ketersediaan jadwal perlu dikonfirmasi sebelum melakukan pemesanan.' },
    { id: 'included', question: 'Apa saja yang termasuk dalam harga?', answer: 'Fasilitas berbeda untuk setiap paket. Periksa bagian fasilitas termasuk dan tidak termasuk pada rincian trip, terutama transportasi, penginapan, makan, tiket masuk, dan kebutuhan pribadi sebelum memesan.' },
    { id: 'payment', question: 'Bagaimana sistem pembayaran?', answer: 'Periksa pilihan metode pembayaran, jumlah yang harus dibayar, dan batas waktunya pada rincian pemesanan. Ketentuan pelunasan dapat berbeda antartrip, jadi pastikan kamu memahaminya sebelum melanjutkan pembayaran.' },
    { id: 'cancellation', question: 'Bagaimana kalau trip dibatalkan?', answer: 'Ketentuan pembatalan, perubahan jadwal, dan pengembalian dana mengikuti kebijakan paket serta partner penyelenggara. Baca ketentuan yang tercantum sebelum memesan dan konfirmasikan penanganannya kepada partner jika terjadi pembatalan.' },
    { id: 'insurance', question: 'Apakah ada asuransi perjalanan?', answer: 'Asuransi belum tentu termasuk dalam setiap paket. Periksa rincian fasilitas dan tanyakan kepada partner mengenai ketersediaan, cakupan, serta pengecualiannya sebelum berangkat.' },
];

const toggleQuestion = (id) => {
    openQuestions.value = openQuestions.value.includes(id) ? openQuestions.value.filter((question) => question !== id) : [...openQuestions.value, id];
};
const toggleAll = () => {
    openQuestions.value = openQuestions.value.length === questions.length ? [] : questions.map((question) => question.id);
};
</script>

<template>
    <section class="mx-auto mt-20 grid max-w-[1180px] items-stretch gap-5 sm:mt-24 lg:grid-cols-[360px_minmax(0,1fr)] lg:gap-6" aria-labelledby="faq-heading">
        <div class="relative isolate flex min-h-[300px] flex-col items-start overflow-hidden rounded-3xl bg-[#123f57] p-7 sm:p-8">
            <img src="https://images.unsplash.com/photo-1518548419970-58e3b4079ab2?auto=format&fit=crop&w=800&q=85" alt="Pemandangan pesisir pulau di Indonesia" loading="lazy" class="absolute inset-0 -z-20 size-full object-cover" />
            <div class="absolute inset-0 -z-10 bg-gradient-to-br from-[#082d46]/90 via-[#082d46]/65 to-[#082d46]/20"></div>
            <span class="inline-flex items-center gap-2 rounded-full border border-white/25 bg-white/10 px-3 py-1.5 text-[10px] font-semibold text-white backdrop-blur-sm"><MessageCircle class="size-3.5" aria-hidden="true" />SEPUTAR PERJALANAN</span>
            <h2 id="faq-heading" class="mt-5 text-3xl font-extrabold leading-tight tracking-tight text-white">Masih ada<br />pertanyaan?</h2>
            <p class="mt-3 max-w-[260px] text-sm leading-6 text-white/85">Kenali cara kerja trip sebelum berangkat. Temukan jawaban untuk rencana perjalananmu di sini.</p>
            <button type="button" class="group mt-6 inline-flex items-center gap-3 rounded-full bg-white px-5 py-3 text-xs font-bold text-[#0875d2] shadow-sm transition duration-300 hover:-translate-y-0.5 hover:bg-sky-50 active:scale-95 focus-visible:outline-2 focus-visible:outline-offset-4 focus-visible:outline-white motion-reduce:transform-none motion-reduce:transition-none" aria-controls="faq-questions" :aria-expanded="openQuestions.length === questions.length" @click="toggleAll">{{ openQuestions.length === questions.length ? 'Tutup semua jawaban' : 'Lihat semua jawaban' }}<ArrowDown class="size-4 transition-transform duration-300 motion-reduce:transition-none" :class="{ 'rotate-180': openQuestions.length === questions.length }" aria-hidden="true" /></button>
        </div>

        <div id="faq-questions" class="overflow-hidden rounded-3xl border border-[#e2edfa] bg-white px-5 shadow-[0_4px_20px_rgba(23,75,120,0.04)] sm:px-6">
            <div v-for="(item, index) in questions" :key="item.id" class="border-b border-[#edf2f8] last:border-b-0">
                <h3>
                    <button :id="`faq-trigger-${item.id}`" type="button" class="group flex min-h-[65px] w-full items-center gap-3 rounded-lg py-4 text-left text-sm font-semibold text-[#172c50] transition-colors hover:text-[#078cff] focus-visible:outline-2 focus-visible:outline-offset-[-2px] focus-visible:outline-[#078cff]" :aria-expanded="openQuestions.includes(item.id)" :aria-controls="`faq-answer-${item.id}`" @click="toggleQuestion(item.id)">
                        <span class="text-[10px] font-medium tabular-nums text-[#91a7be]">0{{ index + 1 }}</span>
                        <span class="flex-1">{{ item.question }}</span>
                        <span class="grid size-7 shrink-0 place-items-center rounded-full transition-colors duration-300 group-hover:bg-[#edf6ff]" :class="openQuestions.includes(item.id) ? 'bg-[#edf6ff] text-[#078cff]' : 'bg-[#f6f8fb] text-slate-400'"><ChevronDown class="size-4 transition-transform duration-300 motion-reduce:transition-none" :class="{ 'rotate-180': openQuestions.includes(item.id) }" aria-hidden="true" /></span>
                    </button>
                </h3>
                <div :id="`faq-answer-${item.id}`" role="region" :aria-labelledby="`faq-trigger-${item.id}`" :aria-hidden="! openQuestions.includes(item.id)" :inert="! openQuestions.includes(item.id)" class="grid transition-[grid-template-rows,opacity] duration-300 ease-out motion-reduce:transition-none" :class="openQuestions.includes(item.id) ? 'grid-rows-[1fr] opacity-100' : 'grid-rows-[0fr] opacity-0'">
                    <div class="min-h-0 overflow-hidden"><p class="pb-5 pl-6 pr-8 text-[13px] leading-6 text-slate-500">{{ item.answer }}</p></div>
                </div>
            </div>
        </div>
    </section>
</template>
