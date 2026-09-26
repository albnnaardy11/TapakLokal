<script setup>
import { Head } from '@inertiajs/vue3';
import MainNavigation from '../Components/Shared/MainNavigation.vue';
import TravelBlog from '../Components/Home/TravelBlog.vue';
import Pagination from '../Components/Admin/Pagination.vue';
defineProps({ publishedArticles: Object });
</script>
<template>
    <Head title="Cerita Perjalanan"><meta name="description" content="Inspirasi destinasi, tips perjalanan, dan cerita lokal bersama TapakLokal." /></Head>
    <div class="min-h-screen bg-[#f8fafc] text-[#172c50]">
        <MainNavigation />
        <main class="mx-auto max-w-[1180px] px-5 py-12 sm:px-8">
            <header class="mb-9 rounded-3xl bg-[#edf6ff] px-6 py-10 sm:p-12"><p class="text-xs font-bold uppercase tracking-widest text-blue-600">CERITA TAPAKLOKAL</p><h1 class="mt-3 text-3xl font-extrabold tracking-tight sm:text-4xl">Perjalanan yang punya cerita.</h1><p class="mt-4 max-w-xl text-sm leading-7 text-slate-600">Kenali destinasi, temui komunitasnya, dan temukan inspirasi untuk perjalanan berikutnya.</p></header>
            <TravelBlog :items="publishedArticles.data" /><Pagination :records="publishedArticles" />
        </main>
    </div>
</template>
