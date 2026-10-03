<script setup>
import { ref, watch } from 'vue';
import QRCode from 'qrcode';
import { QrCode } from 'lucide-vue-next';
const props = defineProps({ url: String, booking: Object });
const image = ref('');
const error = ref('');
watch(() => props.url, async url => {
    image.value = ''; error.value = '';
    if (!url) return;
    try { image.value = await QRCode.toDataURL(url, { width: 280, margin: 2, errorCorrectionLevel: 'M' }); }
    catch { error.value = 'QR tiket gagal dibuat. Muat ulang halaman.'; }
}, { immediate: true });
</script>
<template>
    <section v-if="url" class="rounded-2xl border border-[#dce6f4] bg-white p-5 text-center">
        <h2 class="flex items-center justify-center gap-2 text-sm font-bold text-[#17345e]"><QrCode class="size-5 text-blue-600" />QR Tiket Perjalanan</h2>
        <img v-if="image" :src="image" alt="QR tiket untuk dipindai vendor" width="280" height="280" class="mx-auto mt-3 w-56 max-w-full" />
        <p v-if="error" role="alert" class="mt-3 text-xs text-rose-600">{{ error }}</p>
        <p class="mt-2 break-all text-xs font-bold">{{ booking.reference }}</p>
        <p class="mt-2 text-xs leading-5 text-slate-500">Tunjukkan QR ini kepada vendor saat berkumpul. Berlaku untuk {{ booking.participants }} peserta dalam pesanan ini.</p>
        <p v-if="booking.checked_in_at" class="mt-3 rounded-lg bg-emerald-50 p-2 text-xs font-bold text-emerald-700">Sudah check-in</p>
        <a v-if="image" :href="image" :download="`tiket-${booking.reference}.png`" class="panel-secondary mt-3">Unduh QR tiket</a>
    </section>
</template>
