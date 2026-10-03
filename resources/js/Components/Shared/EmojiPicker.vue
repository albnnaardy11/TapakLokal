<script setup>
import { onMounted, onBeforeUnmount, ref } from 'vue';
const emit = defineEmits(['select', 'close']);
const host = ref(null);
const failed = ref(false);
let disposed = false;
const closeOnEscape = event => { if (event.key === 'Escape') emit('close'); };
onMounted(async () => {
    document.addEventListener('keydown', closeOnEscape);
    try {
        const [{ Picker }, { default: data }] = await Promise.all([import('emoji-mart'), import('@emoji-mart/data')]);
        if (disposed) return;
        const picker = new Picker({
            data,
            theme: 'light',
            set: 'native',
            previewPosition: 'none',
            skinTonePosition: 'search',
            perLine: 8,
            emojiButtonSize: 32,
            emojiSize: 22,
            maxFrequentRows: 2,
            autoFocus: true,
            onEmojiSelect: emoji => emit('select', emoji.native),
            onClickOutside: () => emit('close'),
        });
        host.value?.replaceChildren(picker);
    } catch { failed.value = true; }
});
onBeforeUnmount(() => { disposed = true; document.removeEventListener('keydown', closeOnEscape); });
</script>
<template>
    <div class="max-h-[min(420px,60dvh)] max-w-[calc(100vw-48px)] overflow-auto rounded-xl border border-slate-200 bg-white shadow-xl">
        <p v-if="failed" role="alert" class="p-4 text-xs text-rose-600">Emoji gagal dimuat. Tutup lalu coba kembali.</p>
        <div v-else ref="host" class="min-h-32"><p class="p-4 text-xs text-slate-500">Memuat emoji…</p></div>
    </div>
</template>
