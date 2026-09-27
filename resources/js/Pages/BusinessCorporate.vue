<script setup>
import { Head } from '@inertiajs/vue3';
import MainNavigation from '../Components/Shared/MainNavigation.vue';
import MainFooter from '../Components/Shared/MainFooter.vue';
</script>

<template>
    <Head>
        <title>For Corporates • TapakLokal</title>
    </Head>

    <div class="flex min-h-screen flex-col bg-white font-sans text-slate-900 selection:bg-[#0088ff] selection:text-white">
        <MainNavigation :transparent-on-top="false" />

        <main class="flex-1">
            <!-- Konten for corporate -->
        </main>

        <MainFooter />
    </div>
</template>
