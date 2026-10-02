<script setup>
import { Head, Link, router } from '@inertiajs/vue3';
import { route } from 'ziggy-js';
import MainNavigation from '../Components/Shared/MainNavigation.vue';
defineProps({ notifications: Object });
function markRead(item) { router.post(route('notifications.read', item.id), {}, { preserveScroll: true }); }
</script>
<template>
    <Head title="Notifikasi"><meta name="robots" content="noindex, nofollow" /></Head>
    <MainNavigation />
    <main class="mx-auto min-h-[60vh] max-w-3xl px-5 py-10">
        <h1 class="text-2xl font-extrabold text-[#173b70]">Notifikasi</h1><p class="mt-2 text-sm text-slate-500">Perkembangan pesanan trip dan oleh-oleh kamu.</p>
        <p v-if="!notifications.data.length" class="mt-8 rounded-xl border border-slate-200 p-8 text-center text-slate-500">Belum ada notifikasi.</p>
        <div class="mt-6 space-y-3">
            <article v-for="item in notifications.data" :key="item.id" class="rounded-xl border p-5" :class="item.read_at ? 'border-slate-200 bg-white' : 'border-blue-200 bg-blue-50/50'">
                <h2 class="font-bold text-[#173b70]">{{ item.data.title }}</h2><p class="mt-1 text-sm text-slate-600">{{ item.data.reference }}</p><time class="mt-1 block text-xs text-slate-500">{{ new Date(item.created_at).toLocaleString('id-ID') }}</time>
                <div class="mt-4 flex flex-wrap gap-4 text-sm font-semibold"><Link :href="item.data.url" class="text-blue-600">Lihat pesanan</Link><button v-if="!item.read_at" type="button" class="text-slate-600" @click="markRead(item)">Tandai sudah dibaca</button></div>
            </article>
        </div>
        <nav class="mt-6 flex justify-between" aria-label="Halaman notifikasi"><Link v-if="notifications.prev_page_url" :href="notifications.prev_page_url" class="rounded-lg border px-4 py-2">Sebelumnya</Link><Link v-if="notifications.next_page_url" :href="notifications.next_page_url" class="ml-auto rounded-lg border px-4 py-2">Berikutnya</Link></nav>
    </main>
</template>
