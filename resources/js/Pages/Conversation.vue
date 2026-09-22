<script setup>
import { Head, Link, useForm, usePage } from '@inertiajs/vue3';
import { route } from 'ziggy-js';
import MainNavigation from '../Components/Shared/MainNavigation.vue';
import Pagination from '../Components/Admin/Pagination.vue';
defineProps({ ticket: Object, messages: Object });
const page = usePage();
const form = useForm({ body: '' });
</script>
<template><Head title="Percakapan" /><MainNavigation /><main class="mx-auto max-w-3xl px-5 py-8"><Link :href="route('account.section', 'support')" class="text-xs text-blue-600">← Pusat bantuan</Link><h1 class="mt-5 text-xl font-bold">{{ ticket.subject }}</h1><p class="mt-2 text-xs text-slate-400">Tiket #{{ ticket.id }} · {{ ticket.status }}</p><section class="panel-surface mt-6 p-5"><article v-for="message in [...messages.data].reverse()" :key="message.id" class="mb-4 max-w-[90%] rounded-xl p-4" :class="message.user_id === page.props.auth.user.id ? 'ml-auto bg-blue-50' : 'bg-slate-50'"><p class="text-xs font-bold">{{ message.user.name }}</p><p class="mt-2 whitespace-pre-wrap break-words text-sm leading-6">{{ message.body }}</p><p class="mt-2 text-[10px] text-slate-400">{{ new Date(message.created_at).toLocaleString('id-ID') }}</p></article><Pagination :records="messages" /><form v-if="ticket.status !== 'closed'" class="mt-4 border-t border-slate-100 pt-4" @submit.prevent="form.post(route('support.reply', ticket.id), { preserveScroll: true, onSuccess: () => form.reset() })"><textarea v-model="form.body" required maxlength="5000" rows="3" class="panel-input" placeholder="Tulis pesan…"></textarea><p v-for="error in form.errors" :key="error" class="mt-2 text-xs text-rose-600">{{ error }}</p><button :disabled="form.processing" class="panel-primary mt-3">Kirim pesan</button></form></section></main></template>

