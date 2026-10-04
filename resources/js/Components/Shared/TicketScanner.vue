<script setup>
import { ref, nextTick, onBeforeUnmount } from 'vue';
import { router } from '@inertiajs/vue3';
import { BrowserCodeReader, BrowserQRCodeReader } from '@zxing/browser';
import { AlertCircle, Camera, CameraOff, QrCode, Upload } from 'lucide-vue-next';

const video = ref(null);
const fileInputRef = ref(null);
const active = ref(false);
const error = ref('');
const link = ref('');
let controls = null;
let disposed = false;

const stop = () => {
    controls?.stop();
    controls = null;
    active.value = false;
};

const verify = (value) => {
    try {
        const url = new URL(value, window.location.origin);
        if (url.origin !== window.location.origin || !/^\/vendor\/tickets\/\d+$/.test(url.pathname) || !url.searchParams.has('signature')) {
            throw new Error('invalid_url');
        }
        stop();
        router.visit(url.pathname + url.search);
    } catch {
        error.value = 'QR bukan tiket TapakLokal yang valid untuk situs ini.';
    }
};

const start = async () => {
    error.value = '';
    active.value = true;
    await nextTick();

    try {
        const reader = new BrowserQRCodeReader();
        let selectedDeviceId = undefined;

        // Try enumerating devices to find either back camera or any available webcam
        if (navigator.mediaDevices?.enumerateDevices) {
            try {
                const devices = await BrowserCodeReader.listVideoInputDevices();
                if (devices && devices.length > 0) {
                    const backCamera = devices.find(d => /back|rear|environment|belakang/i.test(d.label));
                    selectedDeviceId = backCamera ? backCamera.deviceId : devices[0].deviceId;
                }
            } catch (enumErr) {
                console.warn('Could not enumerate video devices:', enumErr);
            }
        }

        if (selectedDeviceId) {
            controls = await reader.decodeFromVideoDevice(selectedDeviceId, video.value, (result, failure, scannerControls) => {
                if (result) {
                    scannerControls.stop();
                    verify(result.getText());
                }
            });
        } else {
            // Use ideal constraint so devices without rear camera (laptops/PCs) do not throw OverconstrainedError
            try {
                controls = await reader.decodeFromConstraints(
                    { video: { facingMode: { ideal: 'environment' } } },
                    video.value,
                    (result, failure, scannerControls) => {
                        if (result) {
                            scannerControls.stop();
                            verify(result.getText());
                        }
                    }
                );
            } catch {
                // Fallback to basic video constraint for any connected webcam
                controls = await reader.decodeFromConstraints(
                    { video: true },
                    video.value,
                    (result, failure, scannerControls) => {
                        if (result) {
                            scannerControls.stop();
                            verify(result.getText());
                        }
                    }
                );
            }
        }

        if (disposed || !active.value) {
            controls?.stop();
        }
    } catch (err) {
        stop();
        console.error('Camera scanner error:', err);
        const name = err?.name || '';
        if (name === 'NotAllowedError' || name === 'PermissionDeniedError') {
            error.value = 'Izin kamera ditolak. Silakan izinkan akses kamera di pengaturan izin browser (klik ikon di samping address bar).';
        } else if (name === 'NotFoundError' || name === 'DevicesNotFoundError') {
            error.value = 'Perangkat kamera tidak ditemukan di komputer/laptop ini. Anda dapat mengunggah foto QR tiket atau menempel link tiket.';
        } else if (name === 'NotReadableError' || name === 'TrackStartError') {
            error.value = 'Kamera sedang digunakan oleh program lain (misal Zoom/Teams/OBS). Tutup program tersebut lalu coba lagi.';
        } else if (name === 'OverconstrainedError') {
            error.value = 'Pengaturan kamera tidak didukung perangkat ini. Gunakan opsi unggah foto QR di bawah.';
        } else {
            error.value = 'Kamera tidak dapat dibuka. Pastikan browser memiliki izin kamera atau gunakan opsi unggah foto QR tiket di bawah.';
        }
    }
};

const onFileSelected = async (event) => {
    const file = event.target.files?.[0];
    if (!file) return;
    error.value = '';
    try {
        const objectUrl = URL.createObjectURL(file);
        const reader = new BrowserQRCodeReader();
        const result = await reader.decodeFromImageUrl(objectUrl);
        URL.revokeObjectURL(objectUrl);
        if (result) {
            verify(result.getText());
        }
    } catch {
        error.value = 'QR code tidak terdeteksi pada gambar. Pastikan gambar tiket jelas dan tidak terpotong.';
    } finally {
        if (fileInputRef.value) {
            fileInputRef.value.value = '';
        }
    }
};

onBeforeUnmount(() => {
    disposed = true;
    stop();
});
</script>

<template>
    <section class="mb-5 rounded-2xl border border-blue-100 bg-white p-5 shadow-xs">
        <div class="flex items-start justify-between gap-4">
            <div>
                <h2 class="text-sm font-bold text-[#17345e] flex items-center gap-2">
                    <QrCode class="size-4 text-[#0088ff]" />
                    Scan QR Tiket Peserta
                </h2>
                <p class="mt-1 text-xs text-slate-500">
                    Pindai tiket digital peserta untuk verifikasi identitas dan konfirmasi kehadiran check-in.
                </p>
            </div>
        </div>

        <!-- Camera Viewfinder -->
        <div v-show="active" class="relative mt-4 overflow-hidden rounded-2xl bg-slate-950 border border-slate-800">
            <video
                ref="video"
                playsinline
                muted
                class="max-h-72 w-full object-cover"
            />
            <div class="pointer-events-none absolute inset-0 flex items-center justify-center">
                <div class="relative size-44 sm:size-48 rounded-2xl border-2 border-[#0088ff]/80 shadow-[0_0_0_9999px_rgba(0,0,0,0.4)]">
                    <span class="absolute -top-1 -left-1 size-4 border-t-3 border-l-3 border-[#0088ff]" />
                    <span class="absolute -top-1 -right-1 size-4 border-t-3 border-r-3 border-[#0088ff]" />
                    <span class="absolute -bottom-1 -left-1 size-4 border-b-3 border-l-3 border-[#0088ff]" />
                    <span class="absolute -bottom-1 -right-1 size-4 border-b-3 border-r-3 border-[#0088ff]" />
                </div>
            </div>
        </div>

        <!-- Controls & Options -->
        <div class="mt-4 flex flex-wrap items-center gap-3">
            <button
                type="button"
                class="inline-flex min-h-10 items-center justify-center gap-2 rounded-xl px-4 text-xs font-bold text-white transition shadow-xs cursor-pointer"
                :class="active ? 'bg-slate-700 hover:bg-slate-800' : 'bg-[#0088ff] hover:bg-[#0076e0]'"
                @click="active ? stop() : start()"
            >
                <component :is="active ? CameraOff : Camera" class="size-4" />
                {{ active ? 'Tutup kamera' : 'Buka kamera scanner' }}
            </button>

            <!-- Upload File Option -->
            <label
                class="inline-flex min-h-10 items-center justify-center gap-2 rounded-xl border border-slate-200 bg-white px-4 text-xs font-semibold text-slate-700 hover:bg-slate-50 hover:border-slate-300 transition shadow-2xs cursor-pointer"
            >
                <Upload class="size-4 text-slate-500" />
                <span>Unggah foto / screenshot QR</span>
                <input
                    ref="fileInputRef"
                    type="file"
                    accept="image/*"
                    class="hidden"
                    @change="onFileSelected"
                />
            </label>
        </div>

        <!-- Manual Link Input -->
        <form class="mt-4 flex flex-wrap gap-2.5" @submit.prevent="verify(link)">
            <input
                v-model="link"
                required
                type="text"
                placeholder="Atau tempel link / URL QR tiket"
                aria-label="Link tiket"
                class="panel-input min-w-0 flex-1 text-xs"
            />
            <button class="panel-secondary text-xs font-bold cursor-pointer">
                Periksa tiket
            </button>
        </form>

        <!-- Informative Feedback Messages -->
        <div v-if="error" role="alert" class="mt-3 flex items-start gap-2.5 text-xs text-rose-700 bg-rose-50 border border-rose-100 rounded-xl p-3.5 leading-relaxed">
            <AlertCircle class="size-4 shrink-0 mt-0.5 text-rose-500" />
            <div>
                <p class="font-semibold">{{ error }}</p>
            </div>
        </div>
    </section>
</template>
