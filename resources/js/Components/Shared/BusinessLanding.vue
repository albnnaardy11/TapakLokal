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
        <header class="corporate-header sticky top-0 z-40 bg-white/95 backdrop-blur-md" @keydown.esc="mobileNavigationOpen = false">
            <nav :aria-label="`Navigasi ${program.toLowerCase()}`" class="mx-auto flex h-[68px] max-w-[1600px] items-center justify-between gap-5 px-5 sm:px-8">
                <Link :href="route('home')" aria-label="TapakLokal beranda" class="flex shrink-0 items-center gap-3">
                    <img src="/Assets/Images/logo.webp" alt="TapakLokal Logo" class="h-8 w-auto object-contain" />
                    <span class="border-l border-slate-200 pl-3 text-[10px] leading-tight font-semibold tracking-wide text-slate-500">FOR<br /><span class="text-[13px] font-bold text-[#009cf0]">{{ program }}</span></span>
                </Link>
                <div class="hidden items-center gap-7 text-xs font-semibold text-slate-800 lg:flex">
                    <a v-for="item in links" :key="item.href" :href="item.href" class="py-3 transition hover:text-[#009cf0]">{{ item.label }}</a>
                </div>
                <div class="flex shrink-0 items-center gap-3">
                    <span class="mr-2 hidden items-center gap-1.5 text-xs text-slate-600 xl:flex" aria-label="Bahasa Indonesia"><Globe class="size-4" /> ID</span>
                    <Link :href="loginHref" class="hidden items-center gap-2 rounded-full bg-[#e1f4ff] px-4 py-2.5 text-xs font-bold text-[#075890] sm:inline-flex"><UserRound class="size-4" /> Masuk</Link>
                    <button class="corp-button !hidden !bg-[#009cf0] !px-6 !py-2.5 !text-xs sm:!inline-flex" @click="join">{{ cta }}</button>
                    <button class="rounded-lg p-2 text-[#07345a] lg:hidden" :aria-expanded="mobileNavigationOpen" :aria-controls="`business-mobile-navigation-${program.toLowerCase()}`" :aria-label="mobileNavigationOpen ? `Tutup menu ${program}` : `Buka menu ${program}`" @click="mobileNavigationOpen = !mobileNavigationOpen"><X v-if="mobileNavigationOpen" class="size-6" /><Menu v-else class="size-6" /></button>
                </div>
            </nav>
            <nav v-if="mobileNavigationOpen" :id="`business-mobile-navigation-${program.toLowerCase()}`" :aria-label="`Navigasi ${program.toLowerCase()} mobile`" class="border-t border-slate-100 bg-white px-5 pb-5 lg:hidden">
                <a v-for="item in links" :key="item.href" :href="item.href" class="block border-b border-slate-100 py-3 text-sm font-semibold text-slate-700" @click="mobileNavigationOpen = false">{{ item.label }}</a>
                <div class="mt-4 flex gap-3 sm:hidden"><Link :href="loginHref" class="flex flex-1 items-center justify-center rounded-full bg-sky-50 py-3 text-sm font-semibold text-[#075890]">Masuk</Link><button class="corp-button flex-1 justify-center !py-3" @click="join">{{ cta }}</button></div>
            </nav>
        </header>
        <main>
            <slot />
            <section id="faq" class="business-container business-section grid gap-8 sm:gap-10 lg:grid-cols-[.8fr_1.2fr] lg:gap-20">
                <div>
                    <p class="business-eyebrow">PERTANYAAN UMUM</p>
                    <h2 class="business-heading mt-3 sm:mt-4">Kenali lebih dekat.<br />Mulai lebih yakin.</h2>
                    <p class="business-copy mt-3 sm:mt-5">Temukan informasi yang Anda butuhkan sebelum bergabung dengan TapakLokal.</p>
                    <Link :href="route('help.index')" class="business-text-link mt-5 sm:mt-6">Kunjungi pusat bantuan <ArrowUpRight class="size-4" /></Link>
                </div>
                <div class="border-t border-slate-200">
                    <div v-for="(faq, index) in faqs" :key="faq.q" class="border-b border-slate-200">
                        <h3>
                            <button
                                :id="`business-question-${index}`"
                                :aria-expanded="openFaq === index"
                                :aria-controls="`business-answer-${index}`"
                                class="flex w-full items-center justify-between gap-4 sm:gap-6 py-4 sm:py-6 text-left text-sm font-semibold sm:text-base cursor-pointer"
                                @click="openFaq = openFaq === index ? null : index"
                            >
                                <span>{{ faq.q }}</span>
                                <ChevronDown class="size-4 sm:size-5 shrink-0 text-[#009cf0] transition-transform duration-200" :class="{ 'rotate-180': openFaq === index }" />
                            </button>
                        </h3>
                        <div v-show="openFaq === index" :id="`business-answer-${index}`" role="region" :aria-labelledby="`business-question-${index}`" class="pb-4 sm:pb-6 pr-2 sm:pr-8 text-xs sm:text-sm leading-relaxed sm:leading-7 text-slate-500">
                            {{ faq.a }}
                        </div>
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
