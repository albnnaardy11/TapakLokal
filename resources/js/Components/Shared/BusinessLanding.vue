<script setup>
import { ref } from 'vue';
import { Link } from '@inertiajs/vue3';
import { route } from 'ziggy-js';
import { ArrowUpRight, ChevronDown, Globe, Menu, UserRound, X } from 'lucide-vue-next';
import MainFooter from './MainFooter.vue';

defineProps({
    program: { type: String, required: true },
    links: { type: Array, required: true },
    faqs: { type: Array, required: true },
    loginHref: { type: String, required: true },
    cta: { type: String, default: 'Mulai sekarang' },
});
const emit = defineEmits(['join']);
const mobileNavigationOpen = ref(false);
const openFaq = ref(0);
function join() {
    mobileNavigationOpen.value = false;
    emit('join');
}
</script>

<template>
    <div class="business-page min-h-screen bg-white font-sans text-slate-800">
        <header class="sticky top-0 z-40 border-b border-slate-100 bg-white/95 backdrop-blur-md" @keydown.esc="mobileNavigationOpen = false">
            <nav :aria-label="`Navigasi ${program}`" class="mx-auto flex h-[68px] max-w-[1600px] items-center justify-between gap-4 px-5 sm:px-8">
                <Link :href="route('home')" aria-label="TapakLokal beranda" class="flex shrink-0 items-center gap-3">
                    <span class="text-[23px] font-extrabold tracking-[-0.065em]">tapak<span class="text-[#009cf0]">lokal</span></span>
                    <span class="border-l border-slate-200 pl-3 text-[10px] leading-tight font-semibold tracking-wide text-slate-500">FOR<br /><span class="text-xs font-bold text-[#009cf0]">{{ program }}</span></span>
                </Link>
                <div class="hidden items-center gap-7 text-xs font-semibold lg:flex"><a v-for="link in links" :key="link.href" :href="link.href" class="py-3 hover:text-[#009cf0]">{{ link.label }}</a></div>
                <div class="flex items-center gap-3">
                    <span class="hidden items-center gap-1.5 text-xs text-slate-600 xl:flex"><Globe class="size-4" /> ID</span>
                    <Link :href="loginHref" class="hidden items-center gap-2 rounded-full bg-sky-50 px-4 py-2.5 text-xs font-bold text-[#075890] sm:inline-flex"><UserRound class="size-4" /> Masuk</Link>
                    <button class="business-button hidden !px-5 !py-2.5 !text-xs sm:inline-flex" @click="join">{{ cta }}</button>
                    <button :aria-expanded="mobileNavigationOpen" aria-controls="business-mobile-nav" :aria-label="mobileNavigationOpen ? 'Tutup menu' : 'Buka menu'" class="p-2 lg:hidden" @click="mobileNavigationOpen = !mobileNavigationOpen"><X v-if="mobileNavigationOpen" class="size-6" /><Menu v-else class="size-6" /></button>
                </div>
            </nav>
            <nav v-if="mobileNavigationOpen" id="business-mobile-nav" aria-label="Navigasi mobile" class="border-t border-slate-100 px-5 pb-5 lg:hidden">
                <a v-for="link in links" :key="link.href" :href="link.href" class="block border-b border-slate-100 py-3 text-sm font-semibold" @click="mobileNavigationOpen = false">{{ link.label }}</a>
                <div class="mt-4 flex gap-3 sm:hidden"><Link :href="loginHref" class="business-button business-button-secondary flex-1">Masuk</Link><button class="business-button flex-1" @click="join">{{ cta }}</button></div>
            </nav>
        </header>
        <main>
            <slot />
            <section id="faq" class="business-container business-section grid gap-10 lg:grid-cols-[.8fr_1.2fr] lg:gap-20">
                <div><p class="business-eyebrow">PERTANYAAN UMUM</p><h2 class="business-heading mt-4">Kenali lebih dekat.<br />Mulai lebih yakin.</h2><p class="business-copy mt-5">Temukan informasi yang Anda butuhkan sebelum bergabung dengan TapakLokal.</p><Link :href="route('help.index')" class="business-text-link mt-6">Kunjungi pusat bantuan <ArrowUpRight class="size-4" /></Link></div>
                <div class="border-t border-slate-200">
                    <div v-for="(faq, index) in faqs" :key="faq.q" class="border-b border-slate-200">
                        <h3><button :id="`business-question-${index}`" :aria-expanded="openFaq === index" :aria-controls="`business-answer-${index}`" class="flex w-full items-center justify-between gap-6 py-6 text-left text-sm font-semibold sm:text-base" @click="openFaq = openFaq === index ? null : index">{{ faq.q }}<ChevronDown class="size-5 shrink-0 text-[#009cf0] transition-transform" :class="{ 'rotate-180': openFaq === index }" /></button></h3>
                        <div v-show="openFaq === index" :id="`business-answer-${index}`" role="region" :aria-labelledby="`business-question-${index}`" class="pb-6 pr-8 text-sm leading-7 text-slate-500">{{ faq.a }}</div>
                    </div>
                </div>
            </section>
            <slot name="closing" />
        </main>
        <MainFooter />
        <slot name="dialogs" />
    </div>
</template>

<style src="../../../css/business.css"></style>
