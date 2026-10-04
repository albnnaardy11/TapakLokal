<script setup>
import { computed } from 'vue';
import { sanitizeTripHtml, textToTripHtml } from '../../Composables/tripRichText';
const props = defineProps({ html: String, text: String });
const content = computed(() => props.html ? sanitizeTripHtml(props.html) : textToTripHtml(props.text));
</script>
<template><div class="trip-rich-content text-sm leading-7" v-html="content"></div></template>
<style>
.trip-rich-content { overflow-wrap: anywhere; white-space: pre-wrap; min-width: 0; }
.trip-rich-content p, .trip-rich-content div { margin-bottom: .65em; }
.trip-rich-content ul { list-style: disc; padding-left: 1.5rem; }
.trip-rich-content ol { list-style: decimal; padding-left: 1.5rem; }
.trip-rich-content h2, .trip-rich-content h3 { font-weight: 700; font-size: 1.15em; margin: .8em 0 .4em; }
</style>
