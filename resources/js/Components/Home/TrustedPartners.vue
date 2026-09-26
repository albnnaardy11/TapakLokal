<script setup>
import { computed } from 'vue';

const props = defineProps({
    partners: {
        type: Array,
        default: () => [],
    },
});

const defaultPartners = [
    { name: 'Millennium Hotels and Resorts', image_url: '/Assets/Images/partners/partner-1.svg' },
    { name: 'ALL - Accor Live Limitless', image_url: '/Assets/Images/partners/partner-2.svg' },
    { name: 'Archipelago', image_url: '/Assets/Images/partners/partner-3.svg' },
    { name: 'IHG Hotels & Resorts', image_url: '/Assets/Images/partners/partner-4.svg' },
    { name: 'Ascott The Residence', image_url: '/Assets/Images/partners/partner-5.svg' },
];

const partnerList = computed(() => (props.partners && props.partners.length ? props.partners : defaultPartners));
</script>

<template>
    <section v-if="partnerList.length" class="mx-auto mt-20 max-w-[1180px] py-6 sm:mt-24 sm:py-8" aria-labelledby="trusted-partners-heading">
        <h2 id="trusted-partners-heading" class="text-center text-xl font-extrabold tracking-tight text-[#3e7bef]">
            Mereka Percaya Kami
        </h2>
        <div class="partner-marquee relative mt-8 overflow-hidden py-4 [mask-image:linear-gradient(to_right,transparent,black_5%,black_95%,transparent)] sm:mt-10" aria-label="Partner Tapak Lokal">
            <div class="partner-track flex w-max">
                <ul v-for="copy in 2" :key="copy" class="flex shrink-0 items-center" :aria-hidden="copy === 2 ? true : undefined">
                    <li
                        v-for="partner in partnerList"
                        :key="partner.name"
                        class="flex h-20 w-[190px] shrink-0 items-center justify-center px-6 sm:w-[220px]"
                        :aria-label="partner.name"
                    >
                        <img
                            v-if="partner.image_url"
                            :src="partner.image_url"
                            :alt="partner.name"
                            class="h-8 sm:h-10 w-full object-contain transition-opacity duration-200 opacity-80 hover:opacity-100"
                            loading="lazy"
                        />
                        <span
                            v-else
                            class="flex h-12 w-full items-center justify-center rounded-lg border border-dashed border-slate-200 px-3 text-center text-xs font-medium text-slate-400"
                        >
                            {{ partner.name }}
                        </span>
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
