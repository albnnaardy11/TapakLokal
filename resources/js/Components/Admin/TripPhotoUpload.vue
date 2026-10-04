<script setup>
import axios from 'axios';
import { ref } from 'vue';
import { route } from 'ziggy-js';
import { ImagePlus, Loader2, Trash2 } from 'lucide-vue-next';
defineProps({ modelValue: String, label: String, disabled: Boolean });
const emit = defineEmits(['update:modelValue', 'busy']);
const busy = ref(false);
const error = ref('');
async function upload(event) {
    const file = event.target.files?.[0];
    if (!file) return;
    error.value = '';
    if (!['image/jpeg', 'image/png', 'image/webp'].includes(file.type) || file.size > 15 * 1024 * 1024) {
        error.value = 'Pilih JPG, PNG, atau WebP maksimal 15 MB.';
        event.target.value = '';
        return;
    }
    busy.value = true;
    emit('busy', true);
    try {
        const data = new FormData();
        data.append('image', file);
        const response = await axios.post(route('vendor.trips.upload-image'), data);
        emit('update:modelValue', response.data.url);
    } catch (failure) {
        error.value = failure.response?.data?.errors?.image?.[0] || 'Foto gagal diunggah. Silakan coba lagi.';
    } finally {
        busy.value = false;
        emit('busy', false);
        event.target.value = '';
    }
}
</script>
<template>
    <div class="min-w-0 rounded-xl border border-slate-200 bg-white p-3">
        <p class="mb-2 text-xs font-semibold text-slate-700">{{ label }}</p>
        <div class="relative grid aspect-[4/3] place-items-center overflow-hidden rounded-lg bg-slate-50">
            <img v-if="modelValue" :src="modelValue" :alt="label" class="size-full object-cover" />
            <ImagePlus v-else class="size-8 text-blue-400" />
            <div v-if="busy" class="absolute inset-0 grid place-items-center bg-white/85"><Loader2 class="size-6 animate-spin text-blue-600" /></div>
        </div>
        <div class="mt-3 flex items-center gap-2"><label class="relative flex-1 cursor-pointer rounded-lg border border-blue-200 px-3 py-2 text-center text-xs font-semibold text-blue-600 focus-within:ring-2 focus-within:ring-blue-400"><input type="file" accept="image/jpeg,image/png,image/webp" :aria-label="`Unggah ${label}`" :disabled="disabled || busy" class="absolute inset-0 w-full cursor-pointer opacity-0" @change="upload" />{{ modelValue ? 'Ganti foto' : 'Unggah foto' }}</label><button v-if="modelValue" type="button" :disabled="disabled || busy" class="rounded-lg p-2 text-rose-600 hover:bg-rose-50" :aria-label="`Hapus ${label}`" @click="emit('update:modelValue', '')"><Trash2 class="size-4" /></button></div>
        <p v-if="error" role="alert" class="mt-2 text-xs leading-5 text-rose-600">{{ error }}</p>
    </div>
</template>
