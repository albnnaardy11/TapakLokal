<script setup>
const partners = [
    { name: 'Badan Gizi Nasional', image: 'https://placehold.co/360x128/f5f7fb/183a65?text=Badan+Gizi+Nasional' },
    { name: 'BPJS Kesehatan', image: 'https://placehold.co/360x128/f5f7fb/159c59?text=BPJS+Kesehatan' },
    { name: 'Epson', image: 'https://placehold.co/360x128/f5f7fb/151515?text=EPSON' },
    { name: 'Koperasi Desa Merah Putih', image: 'https://placehold.co/360x128/f5f7fb/ce2437?text=KOP+DES' },
    { name: 'Grab', image: 'https://placehold.co/360x128/f5f7fb/00b14f?text=Grab' },
    { name: 'Pertamina', image: 'https://placehold.co/360x128/f5f7fb/242424?text=PERTAMINA' },
];
</script>

<template>
    <section class="mx-auto mt-20 max-w-[1180px] py-6 sm:mt-24 sm:py-8" aria-labelledby="trusted-partners-heading">
        <h2 id="trusted-partners-heading" class="text-center text-xl font-extrabold tracking-tight text-[#3e7bef]">Mereka Percaya Kami</h2>
        <div class="partner-marquee relative mt-8 overflow-hidden py-4 [mask-image:linear-gradient(to_right,transparent,black_5%,black_95%,transparent)] sm:mt-10" aria-label="Partner Tapak Lokal">
            <div class="partner-track flex w-max">
                <ul v-for="copy in 2" :key="copy" class="flex shrink-0 items-center" :aria-hidden="copy === 2 ? true : undefined">
                    <li v-for="partner in partners" :key="partner.name" class="flex h-20 w-[180px] shrink-0 items-center justify-center px-5 sm:w-[210px]" :aria-label="partner.name">
                        <img v-if="partner.image" :src="partner.image" :alt="partner.name" class="h-16 w-full object-contain" loading="lazy" />
                        <span v-else class="flex h-16 w-full items-center justify-center rounded-lg border border-dashed border-slate-200 px-3 text-center text-xs font-medium text-slate-400">{{ partner.name }}</span>
                    </li>
                </ul>
            </div>
        </div>
    </section>
</template>

<style scoped>
.partner-track {
    animation: partner-slide 32s linear infinite;
}

.partner-marquee:hover .partner-track {
    animation-play-state: paused;
}

@keyframes partner-slide {
    to {
        transform: translateX(-50%);
    }
}

@media (prefers-reduced-motion: reduce) {
    .partner-marquee {
        overflow-x: auto;
    }

    .partner-track {
        animation: none;
    }

    .partner-track > ul[aria-hidden='true'] {
        display: none;
    }
}
</style>
