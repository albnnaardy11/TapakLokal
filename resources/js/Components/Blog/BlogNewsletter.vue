<script setup>
import { CheckCircle2, Mail, Send } from 'lucide-vue-next';
import { ref } from 'vue';

const email = ref('');
const agreed = ref(false);
const message = ref('');

const subscribe = () => {
    message.value = 'Terima kasih. Inspirasi perjalanan akan dikirim ke emailmu.';
};
</script>

<template>
    <section class="mx-auto mt-16 max-w-[1180px] sm:mt-20" aria-labelledby="newsletter-heading">
        <div class="relative isolate overflow-hidden rounded-2xl border border-[#d8eafb] bg-[#eaf6ff] px-6 py-8 sm:px-9 sm:py-9">
            <div class="pointer-events-none absolute -right-10 -top-16 size-48 rounded-full border-[24px] border-white/45" aria-hidden="true"></div>
            <div class="pointer-events-none absolute bottom-0 right-24 h-16 w-36 rounded-t-full border-x border-t border-[#b9ddfb]/70" aria-hidden="true"></div>
            <div class="relative grid gap-7 lg:grid-cols-[0.9fr_1.1fr] lg:items-center lg:gap-12">
                <div><p class="text-xs font-bold tracking-[0.14em] text-[#3E7BEF]">GABUNG KOMUNITAS TAPAKLOKAL</p><h2 id="newsletter-heading" class="mt-2 max-w-lg text-2xl font-extrabold leading-tight tracking-tight text-[#172c50] sm:text-3xl">Dapatkan cerita dan <span class="text-[#3E7BEF]">inspirasi terbaru</span></h2><p class="mt-3 max-w-lg text-sm leading-6 text-slate-600">Langganan newsletter untuk rekomendasi destinasi, tips singkat, dan promo menarik dari TapakLokal.</p></div>
                <form class="relative" @submit.prevent="subscribe"><div class="flex flex-col gap-3 sm:flex-row"><label class="flex min-h-12 flex-1 items-center gap-3 rounded-xl border border-[#cfe3f4] bg-white px-4 focus-within:border-[#3E7BEF] focus-within:ring-2 focus-within:ring-[#3E7BEF]/15"><Mail class="size-4 shrink-0 text-[#3E7BEF]" /><input v-model="email" aria-label="Alamat email" required type="email" autocomplete="email" placeholder="Masukkan alamat email kamu" class="min-w-0 flex-1 bg-transparent text-sm text-[#172c50] outline-none placeholder:text-slate-500" /></label><button type="submit" class="inline-flex min-h-12 items-center justify-center gap-2 rounded-xl bg-[#3E7BEF] px-5 text-sm font-bold text-white shadow-[0_6px_16px_rgba(7,140,255,0.22)] transition-colors hover:bg-[#2e69d9] focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-[#3E7BEF]">Langganan <Send class="size-4" /></button></div><label class="mt-3 flex items-start gap-2 text-xs leading-5 text-slate-500"><input v-model="agreed" type="checkbox" required class="mt-1 size-3.5 rounded border-slate-300 text-[#3E7BEF] focus:ring-[#3E7BEF]" />Saya setuju menerima email dari TapakLokal.</label><p role="status" v-if="message" class="mt-3 flex items-center gap-2 text-xs font-semibold text-emerald-700"><CheckCircle2 class="size-4" />{{ message }}</p></form>
            </div>
        </div>
    </section>
</template>
