<script setup>
import { ref } from 'vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { route } from 'ziggy-js';
import { Search, X } from 'lucide-vue-next';
import MainNavigation from '../Components/Shared/MainNavigation.vue';
import MainFooter from '../Components/Shared/MainFooter.vue';
import ContentCards from '../Components/Shared/ContentCards.vue';
import Pagination from '../Components/Admin/Pagination.vue';

const props = defineProps({
    title: { type: String, default: 'Destinasi' },
    items: { type: Object, default: () => ({ data: [] }) },
    type: { type: String, default: 'destination' },
    query: { type: String, default: '' },
});

const search = ref(props.query || '');

const quickKeywords = ['Labuan Bajo', 'Bali', 'Bromo', 'Lombok', 'Yogyakarta', 'Raja Ampat'];

const doSearch = (keyword = null) => {
    if (keyword !== null) {
        search.value = keyword;
    }
    router.get(route('explore', props.type), { q: search.value ? search.value.trim() : undefined });
};

const clearSearch = () => {
    search.value = '';
    doSearch('');
};
</script>

<template>
    <Head :title="`${title} - Jelajah Lokal TapakLokal`" />

    <div class="min-h-screen bg-[#f8fafc] font-sans text-[#172c50]">
        <MainNavigation />

        <main class="mx-auto max-w-[1220px] px-4 py-8 sm:px-6 lg:px-8">


            <!-- Search Bar & Suggestions -->
            <div class="mb-8 rounded-2xl border border-[#dce8f5] bg-white p-4 sm:p-5 shadow-sm">
                <form class="flex flex-col sm:flex-row gap-2.5" @submit.prevent="doSearch()">
                    <div class="relative flex-1">
                        <Search class="absolute left-3.5 top-1/2 size-4 -translate-y-1/2 text-slate-400" />
                        <input
                            v-model="search"
                            aria-label="Cari konten"
                            type="search"
                            class="w-full rounded-xl border border-slate-200 bg-slate-50/50 pl-10 pr-9 py-2.5 text-xs sm:text-sm text-slate-700 outline-none transition focus:border-[#1677e8] focus:bg-white focus:ring-2 focus:ring-[#1677e8]/20"
                            placeholder="Cari destinasi, daerah, atau kata kunci (contoh: Labuan Bajo, Bali, Bromo)..."
                        />
                        <button
                            v-if="search"
                            type="button"
                            class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600"
                            @click="clearSearch"
                        >
                            <X class="size-4" />
                        </button>
                    </div>
                    <button
                        type="submit"
                        class="inline-flex min-h-[42px] items-center justify-center gap-2 rounded-xl bg-[#1677e8] px-6 text-xs sm:text-sm font-bold text-white shadow-sm shadow-[#1677e8]/25 transition hover:bg-[#0f60c7]"
                    >
                        <Search class="size-4" />
                        <span>Cari</span>
                    </button>
                </form>

                <!-- Quick Keywords Pills -->
                <div class="mt-3.5 flex flex-wrap items-center gap-1.5 text-xs">
                    <span class="font-semibold text-slate-400">Populer:</span>
                    <button
                        v-for="kw in quickKeywords"
                        :key="kw"
                        type="button"
                        class="rounded-full border border-slate-200/90 bg-[#f8fbff] px-2.5 py-1 text-[11px] font-medium text-slate-600 transition hover:border-[#1677e8] hover:bg-[#edf7ff] hover:text-[#1677e8]"
                        @click="doSearch(kw)"
                    >
                        {{ kw }}
                    </button>
                </div>
            </div>

            <!-- Content Grid or Empty State -->
            <div v-if="items.data && items.data.length > 0">
                <div class="mb-4 flex items-center justify-between text-xs text-slate-500">
                    <p>Menampilkan <strong>{{ items.data.length }}</strong> hasil<span v-if="query"> untuk "<strong>{{ query }}</strong>"</span></p>
                    <button v-if="query" type="button" class="text-[#1677e8] font-bold hover:underline" @click="clearSearch">
                        Reset Pencarian
                    </button>
                </div>
                <ContentCards :items="items.data" />
                <div class="mt-8">
                    <Pagination :records="items" />
                </div>
            </div>

            <!-- Rich Empty State -->
            <div v-else class="rounded-3xl border border-dashed border-slate-200 bg-white p-10 sm:p-14 text-center">
                <div class="mx-auto grid size-14 place-items-center rounded-2xl bg-[#edf7ff] text-[#1677e8]">
                    <Search class="size-7" />
                </div>
                <h3 class="mt-4 text-base sm:text-lg font-bold text-[#173b70]">
                    <span v-if="query">Tidak ditemukan hasil untuk "{{ query }}"</span>
                    <span v-else>Belum ada konten pada kategori ini</span>
                </h3>
                <p class="mx-auto mt-2 max-w-md text-xs sm:text-sm text-slate-500 leading-relaxed">
                    Coba gunakan kata kunci lain seperti <strong>Bali</strong>, <strong>Labuan Bajo</strong>, atau <strong>Lombok</strong> untuk melihat inspirasi perjalanan.
                </p>
                <div class="mt-6 flex flex-wrap justify-center gap-2">
                    <button
                        type="button"
                        class="rounded-xl bg-[#1677e8] px-4 py-2 text-xs font-bold text-white shadow-sm hover:bg-[#0f60c7]"
                        @click="clearSearch"
                    >
                        Lihat Semua {{ title }}
                    </button>
                    <button
                        v-for="kw in ['Labuan Bajo', 'Bali', 'Lombok', 'Yogyakarta']"
                        :key="kw"
                        type="button"
                        class="rounded-xl border border-slate-200 bg-slate-50 px-3.5 py-2 text-xs font-semibold text-slate-700 hover:bg-[#edf7ff] hover:text-[#1677e8] hover:border-[#badeff]"
                        @click="doSearch(kw)"
                    >
                        Cari "{{ kw }}"
                    </button>
                </div>
            </div>
        </main>

        <MainFooter />
    </div>
</template>
