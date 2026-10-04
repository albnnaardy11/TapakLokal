<script setup>
import { onMounted, ref, watch } from 'vue';
import { sanitizeTripHtml } from '../../Composables/tripRichText';
const props = defineProps({ modelValue: String, label: String, disabled: Boolean });
const emit = defineEmits(['update:modelValue']);
const editor = ref(null);
const actions = [
    ['bold', 'Tebal', 'B'], ['italic', 'Miring', 'I'], ['underline', 'Garis bawah', 'U'],
    ['insertUnorderedList', 'Daftar poin', '• Daftar'], ['insertOrderedList', 'Daftar nomor', '1. Daftar'],
    ['formatBlock', 'Subjudul', 'Judul', 'h3'], ['formatBlock', 'Paragraf', 'Paragraf', 'p'], ['removeFormat', 'Hapus format', 'Reset'],
];
const publish = () => emit('update:modelValue', sanitizeTripHtml(editor.value.innerHTML));
function command(action) {
    editor.value.focus();
    document.execCommand(action[0], false, action[3] || null);
    publish();
}
function paste(event) {
    event.preventDefault();
    document.execCommand('insertText', false, event.clipboardData.getData('text/plain'));
    publish();
}
onMounted(() => { editor.value.innerHTML = sanitizeTripHtml(props.modelValue); });
watch(() => props.modelValue, value => {
    if (editor.value && document.activeElement !== editor.value) editor.value.innerHTML = sanitizeTripHtml(value);
});
</script>
<template>
    <div class="min-w-0 overflow-hidden rounded-xl border border-slate-200 bg-white focus-within:border-blue-400 focus-within:ring-2 focus-within:ring-blue-100">
        <div class="flex flex-wrap gap-1 border-b border-slate-200 bg-slate-50 p-2" role="toolbar" :aria-label="`Format ${label}`">
            <button v-for="action in actions" :key="action[1]" type="button" :disabled="disabled" :aria-label="action[1]" :title="action[1]" class="rounded px-2.5 py-1.5 text-xs font-semibold text-slate-600 hover:bg-blue-100 hover:text-blue-700 disabled:opacity-50" @mousedown.prevent @click="command(action)">{{ action[2] }}</button>
        </div>
        <div ref="editor" :contenteditable="!disabled" role="textbox" aria-multiline="true" :aria-label="label" class="trip-rich-content min-h-44 max-h-[560px] overflow-y-auto p-4 text-sm leading-7 text-slate-700 outline-none" @input="publish" @paste="paste" @drop.prevent></div>
    </div>
</template>
<style>
.trip-rich-content { overflow-wrap: anywhere; white-space: pre-wrap; min-width: 0; }
.trip-rich-content p, .trip-rich-content div { margin-bottom: .65em; }
.trip-rich-content ul { list-style: disc; padding-left: 1.5rem; }
.trip-rich-content ol { list-style: decimal; padding-left: 1.5rem; }
.trip-rich-content h2, .trip-rich-content h3 { font-weight: 700; font-size: 1.15em; margin: .8em 0 .4em; }
.trip-rich-content blockquote { border-left: 3px solid #078cff; padding-left: 1rem; }
</style>
