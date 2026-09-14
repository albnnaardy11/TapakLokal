<script setup>
defineProps({
    variant: { type: String, default: 'coast' },
});
</script>

<template>
    <div class="pointer-events-none absolute inset-y-0 left-1/2 -z-10 w-screen -translate-x-1/2 overflow-hidden" aria-hidden="true">
        <div class="absolute inset-0" :class="variant === 'coast' ? 'bg-[linear-gradient(180deg,transparent_0%,#eaf5ff_16%,#edf8fa_62%,transparent_100%)]' : 'bg-[linear-gradient(180deg,transparent_0%,#edf4ff_25%,#eef8fc_80%,transparent_100%)]'"></div>
        <div class="absolute -left-64 top-20 size-[580px] rounded-full bg-[#b9e0ff]/30 blur-3xl"></div>
        <div class="absolute -right-64 bottom-10 size-[620px] rounded-full bg-[#bbeae6]/25 blur-3xl"></div>

        <svg viewBox="0 0 300 650" fill="none" class="absolute top-20 hidden h-[650px] w-[300px] text-[#479dd0]/10 lg:block" :class="variant === 'coast' ? '-left-20' : '-right-20 rotate-180'">
            <path v-for="offset in 7" :key="offset" d="M-90 5C180-45 280 75 140 180S-60 345 95 430 300 600 150 700" :transform="`translate(${offset * 15} ${offset * 10})`" stroke="currentColor" stroke-width="1" />
        </svg>

        <svg viewBox="0 0 160 420" fill="none" class="absolute hidden h-[420px] w-40 text-[#479dd0]/30 xl:block" :class="variant === 'coast' ? 'right-[max(12px,calc((100vw-1460px)/2))] top-4' : 'left-[max(12px,calc((100vw-1460px)/2))] top-0 -scale-x-100'">
            <path d="M105 24C16 64 24 127 87 146S154 236 77 269 27 340 83 385" stroke="currentColor" stroke-width="1.5" stroke-dasharray="4 8" stroke-linecap="round" />
            <circle cx="105" cy="24" r="5" fill="#f5f7fb" stroke="currentColor" stroke-width="2" />
            <circle cx="83" cy="385" r="5" fill="#f5f7fb" stroke="currentColor" stroke-width="2" />
            <circle cx="87" cy="146" r="12" fill="#eaf5ff" />
            <path d="M87 137a6 6 0 0 0-6 6c0 5 6 10 6 10s6-5 6-10a6 6 0 0 0-6-6Z" stroke="currentColor" stroke-width="1.5" />
            <circle cx="87" cy="143" r="2" stroke="currentColor" />
        </svg>
    </div>
</template>
