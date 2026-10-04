<script setup>
import { ref, computed } from 'vue';
import {
    UploadCloud,
    Image as ImageIcon,
    CheckCircle2,
    Sparkles,
    Trash2,
    RefreshCw,
    Link as LinkIcon,
    AlertCircle,
    Loader2,
} from 'lucide-vue-next';
import axios from 'axios';

const props = defineProps({
    modelValue: String,
    label: {
        type: String,
        default: 'Foto Utama Trip',
    },
    disabled: Boolean,
    readonly: Boolean,
    required: Boolean,
});

const emit = defineEmits(['update:modelValue', 'change']);

const fileInputRef = ref(null);
const isDragging = ref(false);
const isProcessing = ref(false);
const isUploading = ref(false);
const errorMessage = ref('');
const showUrlInput = ref(false);
const stats = ref(null);

const currentUrl = computed({
    get: () => props.modelValue || '',
    set: (val) => {
        emit('update:modelValue', val);
        emit('change', val);
    },
});

const triggerBrowse = () => {
    if (props.disabled || props.readonly || isProcessing.value || isUploading.value) return;
    fileInputRef.value?.click();
};

const handleDragOver = (e) => {
    e.preventDefault();
    if (!props.disabled && !props.readonly) {
        isDragging.value = true;
    }
};

const handleDragLeave = () => {
    isDragging.value = false;
};

const handleDrop = (e) => {
    e.preventDefault();
    isDragging.value = false;
    if (props.disabled || props.readonly) return;
    const files = e.dataTransfer?.files;
    if (files && files.length > 0) {
        processFile(files[0]);
    }
};

const onFileSelected = (e) => {
    const files = e.target.files;
    if (files && files.length > 0) {
        processFile(files[0]);
    }
};

/**
 * High-Quality Client-Side WebP Conversion & Compression
 * - Scales smoothly to max width 1600px with high smoothing
 * - Encodes to WebP at 88% quality (crystal clear, no blur/artifacts)
 * - Drastically shrinks size (e.g., 5MB -> 180KB) for 100% SEO & Performance
 */
const convertToWebp = (file) => {
    return new Promise((resolve, reject) => {
        const reader = new FileReader();
        reader.readAsDataURL(file);
        reader.onload = (event) => {
            const img = new Image();
            img.src = event.target.result;
            img.onload = () => {
                const origWidth = img.naturalWidth || img.width;
                const origHeight = img.naturalHeight || img.height;
                const maxWidth = 1600;

                let targetWidth = origWidth;
                let targetHeight = origHeight;

                if (origWidth > maxWidth) {
                    targetWidth = maxWidth;
                    targetHeight = Math.round((origHeight / origWidth) * maxWidth);
                }

                const canvas = document.createElement('canvas');
                canvas.width = targetWidth;
                canvas.height = targetHeight;

                const ctx = canvas.getContext('2d');
                ctx.imageSmoothingEnabled = true;
                ctx.imageSmoothingQuality = 'high';
                ctx.drawImage(img, 0, 0, targetWidth, targetHeight);

                canvas.toBlob(
                    (blob) => {
                        if (!blob) {
                            reject(new Error('Konversi WebP gagal.'));
                            return;
                        }
                        resolve({
                            blob,
                            width: targetWidth,
                            height: targetHeight,
                            origSize: file.size,
                            webpSize: blob.size,
                        });
                    },
                    'image/webp',
                    0.88, // 88% WebP quality: visually lossless & lightweight
                );
            };
            img.onerror = () => reject(new Error('Gagal memuat file gambar.'));
        };
        reader.onerror = () => reject(new Error('Gagal membaca file.'));
    });
};

const formatBytes = (bytes) => {
    if (!bytes || bytes === 0) return '0 B';
    const k = 1024;
    const sizes = ['B', 'KB', 'MB'];
    const i = Math.floor(Math.log(bytes) / Math.log(k));
    return (bytes / Math.pow(k, i)).toFixed(1) + ' ' + sizes[i];
};

const processFile = async (file) => {
    if (!file.type.startsWith('image/')) {
        errorMessage.value = 'Silakan pilih file gambar yang valid (JPG, PNG, atau WebP).';
        return;
    }

    errorMessage.value = '';
    isProcessing.value = true;
    stats.value = null;

    try {
        // Step 1: Client-Side WebP Conversion & High-Quality Optimization
        const { blob, width, height, origSize, webpSize } = await convertToWebp(file);
        const savingsPercent = origSize > webpSize ? Math.round(((origSize - webpSize) / origSize) * 100) : 0;

        stats.value = {
            width,
            height,
            origSizeFormatted: formatBytes(origSize),
            webpSizeFormatted: formatBytes(webpSize),
            savingsPercent,
        };

        isProcessing.value = false;
        isUploading.value = true;

        // Step 2: Upload to Vendor Trip Upload Endpoint
        const formData = new FormData();
        formData.append('image', blob, `${file.name.replace(/\.[^/.]+$/, '')}.webp`);

        const response = await axios.post('/vendor/trips/upload-image', formData, {
            headers: {
                'Content-Type': 'multipart/form-data',
            },
        });

        if (response.data && response.data.url) {
            currentUrl.value = response.data.url;
        } else {
            throw new Error('Respon server tidak memuat URL gambar.');
        }
    } catch (err) {
        console.error('Image upload error:', err);
        errorMessage.value = err.response?.data?.message || err.message || 'Gagal mengunggah foto. Silakan coba lagi.';
    } finally {
        isProcessing.value = false;
        isUploading.value = false;
        if (fileInputRef.value) {
            fileInputRef.value.value = '';
        }
    }
};

const removePhoto = () => {
    currentUrl.value = '';
    stats.value = null;
    errorMessage.value = '';
};
</script>

<template>
    <div class="space-y-3">
        <!-- Hidden Native File Input -->
        <input
            ref="fileInputRef"
            type="file"
            accept="image/jpeg,image/png,image/webp,image/avif"
            class="hidden"
            :disabled="disabled || readonly"
            @change="onFileSelected"
        />

        <!-- PREVIEW MODE: IF PHOTO EXISTS -->
        <div
            v-if="currentUrl"
            class="overflow-hidden rounded-2xl border border-slate-200 bg-white p-3.5 shadow-xs transition"
        >
            <div class="relative aspect-[16/8] w-full overflow-hidden rounded-xl bg-slate-950">
                <img
                    :src="currentUrl"
                    alt="Pratinjau foto trip"
                    class="size-full object-cover transition-transform duration-300 hover:scale-102"
                    loading="lazy"
                />

                <!-- High Quality WebP Badge Overlay -->
                <div class="absolute left-3 top-3 flex flex-wrap items-center gap-1.5">
                    <span
                        class="flex items-center gap-1 rounded-lg bg-emerald-500/90 px-2 py-1 text-[10px] font-bold tracking-wide text-white backdrop-blur-md shadow-xs"
                    >
                        <Sparkles class="size-3" />
                        WebP HD Teroptimasi
                    </span>
                    <span
                        v-if="stats"
                        class="rounded-lg bg-black/60 px-2 py-1 text-[10px] font-medium text-white backdrop-blur-md"
                    >
                        {{ stats.width }} × {{ stats.height }} px
                    </span>
                </div>

                <!-- Floating Actions -->
                <div v-if="!disabled && !readonly" class="absolute right-3 top-3 flex items-center gap-2">
                    <button
                        type="button"
                        class="flex size-8 items-center justify-center rounded-lg bg-black/60 text-white backdrop-blur-md transition hover:bg-black/80 hover:text-white"
                        title="Ganti foto"
                        @click="triggerBrowse"
                    >
                        <RefreshCw class="size-4" />
                    </button>
                    <button
                        type="button"
                        class="flex size-8 items-center justify-center rounded-lg bg-rose-600/85 text-white backdrop-blur-md transition hover:bg-rose-700"
                        title="Hapus foto"
                        @click="removePhoto"
                    >
                        <Trash2 class="size-4" />
                    </button>
                </div>
            </div>

            <!-- Optimization Stats Info -->
            <div class="mt-3 flex flex-wrap items-center justify-between gap-2 px-1 text-xs">
                <div class="flex items-center gap-2 text-slate-600">
                    <CheckCircle2 class="size-4 text-emerald-600" />
                    <span class="font-medium text-slate-700">Foto siap digunakan dengan performa cepat & SEO maksimal</span>
                </div>

                <div v-if="stats" class="flex items-center gap-2 font-mono text-[11px] text-slate-500">
                    <span class="rounded bg-slate-100 px-1.5 py-0.5">{{ stats.webpSizeFormatted }}</span>
                    <span v-if="stats.savingsPercent > 0" class="text-emerald-600 font-bold">
                        (Hemat {{ stats.savingsPercent }}%)
                    </span>
                </div>
            </div>

            <!-- Alternative: Edit URL directly -->
            <div v-if="!disabled && !readonly" class="mt-2.5 border-t border-slate-100 pt-2 px-1">
                <button
                    type="button"
                    class="text-[11px] font-medium text-blue-600 hover:text-blue-700 hover:underline flex items-center gap-1"
                    @click="showUrlInput = !showUrlInput"
                >
                    <LinkIcon class="size-3" />
                    {{ showUrlInput ? 'Sembunyikan URL' : 'Lihat / edit tautan URL foto' }}
                </button>
                <div v-if="showUrlInput" class="mt-2">
                    <input
                        v-model="currentUrl"
                        type="url"
                        class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3 py-1.5 font-mono text-xs text-slate-700 outline-none focus:border-blue-500 focus:bg-white"
                        placeholder="https://..."
                    />
                </div>
            </div>
        </div>

        <!-- UPLOAD DROPZONE MODE: IF NO PHOTO YET -->
        <div v-else>
            <div
                class="group relative flex flex-col items-center justify-center rounded-2xl border-2 border-dashed p-6 text-center transition-all duration-200"
                :class="[
                    isDragging
                        ? 'border-[#0088ff] bg-blue-50/60 scale-[1.005]'
                        : 'border-slate-200 bg-slate-50/50 hover:border-blue-300 hover:bg-blue-50/20',
                    disabled || readonly ? 'cursor-not-allowed opacity-60' : 'cursor-pointer',
                ]"
                @click="triggerBrowse"
                @dragover="handleDragOver"
                @dragleave="handleDragLeave"
                @drop="handleDrop"
            >
                <!-- Loading / Processing State -->
                <div v-if="isProcessing || isUploading" class="py-4 flex flex-col items-center gap-3">
                    <Loader2 class="size-8 animate-spin text-[#0088ff]" />
                    <div>
                        <p class="text-xs font-bold text-slate-800">
                            {{ isProcessing ? 'Mengompresi & Mengonversi ke WebP HD…' : 'Mengunggah foto teroptimasi…' }}
                        </p>
                        <p class="mt-1 text-[11px] text-slate-500">
                            Menjaga detail tetap tajam dan memperkecil ukuran file untuk skor SEO 100%.
                        </p>
                    </div>
                </div>

                <!-- Idle State -->
                <template v-else>
                    <div
                        class="flex size-12 items-center justify-center rounded-2xl bg-blue-50 text-[#0088ff] border border-blue-100 group-hover:scale-110 transition-transform duration-200 shadow-2xs"
                    >
                        <UploadCloud class="size-6 stroke-[2]" />
                    </div>

                    <div class="mt-3.5 space-y-1">
                        <p class="text-xs font-bold text-slate-800">
                            <span class="text-[#0088ff] hover:underline">Klik untuk unggah foto</span>
                            atau seret file ke sini
                        </p>
                        <p class="text-[11px] text-slate-500">
                            Mendukung JPG, PNG, WebP (Otomatis dikonversi ke WebP kualitas tinggi & kompresi ringan)
                        </p>
                    </div>
                </template>
            </div>

            <!-- Alternative URL Paste Toggle -->
            <div v-if="!disabled && !readonly" class="mt-2.5 flex items-center justify-between text-xs px-1">
                <button
                    type="button"
                    class="text-[11px] font-semibold text-slate-500 hover:text-blue-600 transition flex items-center gap-1.5"
                    @click="showUrlInput = !showUrlInput"
                >
                    <LinkIcon class="size-3" />
                    {{ showUrlInput ? 'Tutup input URL' : 'Atau tempel tautan URL gambar secara manual' }}
                </button>
            </div>

            <!-- Manual URL Input Field (Collapsible) -->
            <div v-if="showUrlInput" class="mt-2">
                <input
                    v-model="currentUrl"
                    type="url"
                    class="w-full rounded-xl border border-slate-200 bg-white px-3.5 py-2.5 text-xs font-medium text-slate-800 outline-none transition focus:border-blue-500 focus:ring-2 focus:ring-blue-100"
                    placeholder="Contoh: https://images.unsplash.com/photo-..."
                />
            </div>
        </div>

        <!-- Error Alert -->
        <div
            v-if="errorMessage"
            class="flex items-start gap-2 rounded-xl border border-rose-100 bg-rose-50 p-3 text-xs text-rose-700 leading-relaxed"
        >
            <AlertCircle class="size-4 shrink-0 text-rose-500 mt-0.5" />
            <span>{{ errorMessage }}</span>
        </div>
    </div>
</template>
