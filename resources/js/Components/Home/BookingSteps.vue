<script setup>
import { ArrowRight, CalendarDays, CreditCard, HeartHandshake, Search, Send } from 'lucide-vue-next';

defineEmits(['explore']);

const steps = [
    { title: 'Pilih trip', description: 'Temukan destinasi dan pengalaman yang kamu suka.', icon: Search },
    { title: 'Tentukan jadwal', description: 'Pilih tanggal keberangkatan yang pas untukmu.', icon: CalendarDays },
    { title: 'Booking & bayar', description: 'Lengkapi data dan pilih metode pembayaran.', icon: CreditCard },
    { title: 'Dukung lokal', description: 'Perjalananmu ikut mendukung usaha setempat.', icon: HeartHandshake },
    { title: 'Siap berangkat', description: 'Siapkan barangmu dan nikmati cerita baru.', icon: Send },
];
</script>

<template>
    <section class="mx-auto mt-20 max-w-[1180px] sm:mt-24" aria-labelledby="booking-steps-heading">
        <div class="grid overflow-hidden rounded-3xl border border-[#e2edfa] bg-white shadow-[0_6px_28px_rgba(23,75,120,0.04)] lg:grid-cols-[280px_minmax(0,1fr)]">
            <div class="relative isolate flex min-h-64 flex-col justify-end overflow-hidden bg-[#153e59] p-7 lg:min-h-full">
                <img src="https://images.unsplash.com/photo-1518548419970-58e3b4079ab2?auto=format&fit=crop&w=800&q=85" alt="Pemandangan pulau dan laut biru di Indonesia" loading="lazy" class="absolute inset-0 -z-20 size-full object-cover" />
                <div class="absolute inset-0 -z-10 bg-gradient-to-t from-[#082d46]/95 via-[#082d46]/30 to-transparent"></div>
                <span class="mb-auto w-fit rounded-full border border-white/30 bg-white/15 px-3 py-1.5 text-[10px] font-semibold text-white backdrop-blur-sm">Dari rencana jadi cerita</span>
                <p class="mt-12 text-2xl font-bold leading-tight tracking-tight text-white">Perjalanan seru,<br />mulai dari sini.</p>
                <p class="mt-3 text-xs leading-5 text-white/80">Temukan tempat baru, kenali cerita lokal, dan bawa pulang kenangan.</p>
            </div>

            <div class="min-w-0 px-5 py-8 sm:p-8">
                <div class="flex flex-wrap items-end justify-between gap-4">
                    <div>
                        <p class="text-[10px] font-bold tracking-[0.12em] text-[#078cff]">RENCANAKAN PERJALANANMU</p>
                        <h2 id="booking-steps-heading" class="mt-2 text-2xl font-extrabold tracking-tight text-[#172c50]">Cara booking trip</h2>
                        <p class="mt-2 text-sm text-slate-500">Mudah dan praktis, dalam lima langkah.</p>
                    </div>
                    <button type="button" class="group inline-flex items-center gap-2 rounded-full bg-[#edf6ff] px-4 py-2.5 text-xs font-bold text-[#078cff] transition duration-300 hover:bg-[#078cff] hover:text-white active:scale-95 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-[#078cff] motion-reduce:transition-none" @click="$emit('explore')">Mulai cari trip<ArrowRight class="size-4 transition-transform group-hover:translate-x-0.5 motion-reduce:transition-none" aria-hidden="true" /></button>
                </div>

                <ol class="mt-7 grid gap-3 sm:grid-cols-2 xl:grid-cols-5">
                    <li v-for="(step, index) in steps" :key="step.title" class="group relative rounded-2xl border border-[#e8eff8] bg-[#f8fbff] p-4 transition duration-300 ease-out hover:-translate-y-1 hover:border-[#b9ddff] hover:bg-white hover:shadow-[0_8px_20px_rgba(23,100,180,0.08)] motion-reduce:transform-none motion-reduce:transition-none">
                        <span class="absolute right-3 top-3 text-[10px] font-bold tabular-nums text-[#8aa9c6]">0{{ index + 1 }}</span>
                        <span class="mb-4 grid size-11 place-items-center rounded-xl bg-[#e3f1ff] text-[#078cff] transition-colors duration-300 group-hover:bg-[#078cff] group-hover:text-white motion-reduce:transition-none"><component :is="step.icon" class="size-5" :stroke-width="1.8" aria-hidden="true" /></span>
                        <h3 class="text-xs font-bold leading-5 text-[#172c50]">{{ step.title }}</h3>
                        <p class="mt-1.5 text-[11px] leading-[18px] text-slate-500">{{ step.description }}</p>
                    </li>
                </ol>
            </div>
        </div>
    </section>
</template>
