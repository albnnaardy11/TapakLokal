<script setup>
import { computed } from 'vue';
import { Wallet } from 'lucide-vue-next';
const props = defineProps({ methods: Array, preferences: Object, disabled: Boolean });
const selected = defineModel({ type: String });
const groups = computed(() => Object.groupBy(props.methods || [], method => method.group));
</script>
<template>
    <fieldset class="space-y-6"><legend class="sr-only">Pilih metode pembayaran</legend><div v-for="(items,group) in groups" :key="group"><h3 class="mb-3 text-xs font-bold uppercase tracking-wider text-slate-500">{{ group }}</h3><div class="space-y-3"><label v-for="method in items" :key="method.id" class="flex items-center gap-4 rounded-xl border p-4 transition focus-within:ring-2 focus-within:ring-blue-300" :class="!method.enabled || disabled ? 'cursor-not-allowed border-slate-100 bg-slate-50 opacity-60' : selected === method.id ? 'cursor-pointer border-[#0175ea] bg-[#f1f8ff] shadow-xs' : 'cursor-pointer border-slate-200 hover:border-blue-300'"><input v-model="selected" :disabled="!method.enabled || disabled" :value="method.id" type="radio" name="checkout-method" class="size-4 shrink-0 accent-[#0175ea]" /><div class="min-w-0 flex-1"><p class="text-sm font-bold">{{ method.name }}<span v-if="preferences?.primary === method.id" class="ml-2 rounded bg-blue-100 px-2 py-0.5 text-[10px] text-blue-700">Utama</span><span v-else-if="preferences?.saved?.includes(method.id)" class="ml-2 text-[10px] font-medium text-slate-500">Tersimpan</span></p><p class="mt-1 text-xs leading-5 text-slate-500">{{ method.description }}</p></div><span class="grid h-10 w-20 shrink-0 place-items-center rounded-lg bg-white px-2"><img v-if="method.logo" :src="method.logo" :alt="method.badge" width="72" height="30" class="max-h-7 w-full object-contain" /><Wallet v-else class="size-5 text-[#0175ea]" /></span></label></div></div></fieldset>
</template>
