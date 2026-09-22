<script setup>
import { ref } from 'vue';
import { Head, router } from '@inertiajs/vue3';
import { route } from 'ziggy-js';
import MainNavigation from '../Components/Shared/MainNavigation.vue';
import ContentCards from '../Components/Shared/ContentCards.vue';
import Pagination from '../Components/Admin/Pagination.vue';
const props = defineProps({ title: String, items: Object, type: String, query: String });
const search = ref(props.query);
</script>
<template>
    <Head :title="title" /><MainNavigation />
    <main class="mx-auto max-w-[1180px] px-5 py-12 sm:px-8"><p class="text-xs font-bold uppercase tracking-widest text-blue-500">JELAJAH LOKAL</p><h1 class="mt-3 text-3xl font-extrabold text-[#17375f]">{{ title }}</h1><form class="my-7 flex gap-2" @submit.prevent="router.get(route('explore', type), { q: search })"><input v-model="search" aria-label="Cari konten" type="search" class="panel-input max-w-lg" placeholder="Cari inspirasi…" /><button class="panel-primary">Cari</button></form><ContentCards :items="items.data" /><Pagination :records="items" /></main>
</template>
