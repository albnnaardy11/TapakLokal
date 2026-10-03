<script setup>
import { ref, onBeforeUnmount } from 'vue';
import { router } from '@inertiajs/vue3';
import { BrowserQRCodeReader } from '@zxing/browser';
const video = ref(null);
const active = ref(false);
const error = ref('');
const link = ref('');
let controls;
let disposed = false;
const stop = () => { controls?.stop(); controls = null; active.value = false; };
const verify = value => {
    try {
        const url = new URL(value);
        if (url.origin !== window.location.origin || !/^\/vendor\/tickets\/\d+$/.test(url.pathname) || !url.searchParams.has('signature')) throw new Error();
        stop(); router.visit(url.href);
    } catch { error.value = 'QR bukan tiket TapakLokal yang valid untuk situs ini.'; }
};
const start = async () => {
    error.value = ''; active.value = true;
    try {
        const reader = new BrowserQRCodeReader();
        controls = await reader.decodeFromVideoDevice(undefined, video.value, (result, failure, scannerControls) => {
            if (result) { scannerControls.stop(); verify(result.getText()); }
        });
        if (disposed || !active.value) controls.stop();
    } catch { stop(); error.value = 'Kamera tidak dapat dibuka. Izinkan akses kamera dan gunakan HTTPS atau localhost. Kamu juga bisa memasukkan link tiket.'; }
};
onBeforeUnmount(() => { disposed = true; stop(); });
</script>
<template>
    <section class="mb-5 rounded-2xl border border-blue-100 bg-white p-5">
        <h2 class="text-sm font-bold">Scan QR tiket peserta</h2><p class="mt-2 text-xs text-slate-500">Pindai tiket, periksa peserta, lalu konfirmasi check-in.</p>
        <video ref="video" v-show="active" playsinline muted class="mt-3 max-h-64 w-full rounded-xl bg-black" />
        <button type="button" class="panel-primary mt-3" @click="active ? stop() : start()">{{ active ? 'Tutup kamera' : 'Buka kamera scanner' }}</button>
        <form class="mt-3 flex flex-wrap gap-2" @submit.prevent="verify(link)"><input v-model="link" required type="url" placeholder="Atau tempel link QR tiket" aria-label="Link tiket" class="panel-input min-w-0 flex-1" /><button class="panel-secondary">Periksa tiket</button></form>
        <p v-if="error" role="alert" class="mt-3 text-xs text-rose-600">{{ error }}</p>
    </section>
</template>
