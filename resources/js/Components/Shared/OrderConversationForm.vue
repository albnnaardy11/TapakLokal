<script setup>
import { useForm } from '@inertiajs/vue3';
import { route } from 'ziggy-js';
const props = defineProps({ bookingId: Number, souvenirOrderId: Number, reference: String });
const form = useForm({ booking_id: props.bookingId || null, souvenir_order_id: props.souvenirOrderId || null, category: 'vendor', subject: `Pesanan ${props.reference}`, body: '' });
</script>
<template>
    <form class="mt-6 rounded-xl border border-slate-200 bg-white p-5" @submit.prevent="form.post(route('support.store'))">
        <h2 class="font-bold text-[#173b70]">Chat vendor</h2><p class="mt-2 text-sm text-slate-500">Pesan diteruskan ke vendor pesanan ini. Tim operasional dapat membantu jika diperlukan.</p>
        <label class="mt-4 block text-sm font-semibold" :for="`message-${reference}`">Pesan<textarea :id="`message-${reference}`" v-model="form.body" required minlength="10" maxlength="5000" rows="3" class="mt-2 w-full rounded-xl border border-slate-300 p-3 font-normal" placeholder="Tanyakan informasi pesanan kamu…" /></label>
        <p v-for="(error, field) in form.errors" :key="field" role="alert" class="mt-2 text-sm text-red-700">{{ error }}</p>
        <button type="submit" :disabled="form.processing" class="mt-4 rounded-xl bg-[#1688e8] px-5 py-3 text-sm font-bold text-white disabled:opacity-50">{{ form.processing ? 'Mengirim…' : 'Kirim ke vendor' }}</button>
    </form>
</template>
