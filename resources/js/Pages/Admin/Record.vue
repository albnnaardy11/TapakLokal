<script setup>
import { computed, watch } from 'vue';
import { Link, useForm, usePage } from '@inertiajs/vue3';
import { route } from 'ziggy-js';
import PanelLayout from '../../Components/Admin/PanelLayout.vue';
import Fields from '../../Components/Admin/Fields.vue';
const props = defineProps({ module: String, definition: Object, record: Object, navigation: Array, canManage: Boolean, messages: Object });
const form = useForm(Object.fromEntries(Object.entries(props.definition.fields).map(([key, field]) => [key, props.record[key] ?? (field.type === 'number' ? 0 : '')])));
watch(() => props.record, record => {
    Object.entries(props.definition.fields).forEach(([key, field]) => {
        const value = record[key];
        form[key] = value && ['date', 'datetime-local'].includes(field.type) ? value.slice(0, field.type === 'date' ? 10 : 16) : value ?? (field.type === 'number' ? 0 : '');
    });
    form.clearErrors();
}, { immediate: true });
const workflow = useForm({ note: '' });
const page = usePage();
const permission = computed(() => ({ vendors: 'vendor.verify', trips: 'operations.manage', bookings: 'operations.manage', refunds: 'refund.approve', payouts: 'payout.approve' }[props.module]));
const canAct = computed(() => page.props.auth.permissions.includes(permission.value));
const labels = { verify: 'Verifikasi vendor', reject: 'Tolak', suspend: 'Nonaktifkan', publish: 'Terbitkan', archive: 'Arsipkan', confirm: 'Konfirmasi', start: 'Mulai perjalanan', complete: 'Selesaikan', cancel: 'Batalkan', approve: 'Setujui' };
const chosenAction = useForm({ value: '' });
const act = action => { chosenAction.value = action; };
const submitAction = () => workflow.post(route('admin.workflow', [props.module, props.record.id, chosenAction.value]), { onSuccess: () => chosenAction.value = '' });
</script>
<template>
    <PanelLayout :title="definition.label" :subtitle="'Detail data #' + record.id" :navigation="navigation">
        <template #actions><Link :href="route('admin.resources.index', module)" class="panel-secondary">Kembali ke daftar</Link></template>
        <div class="grid items-start gap-6 xl:grid-cols-[minmax(0,1fr)_300px]">
            <section class="panel-surface p-5 sm:p-7">
                <form v-if="Object.keys(definition.fields).length" @submit.prevent="form.put(route('admin.resources.update', [module, record.id]))"><Fields :fields="definition.fields" :form="form" :disabled="!canManage" /><button v-if="canManage" class="panel-primary mt-6" :disabled="form.processing">Simpan perubahan</button></form>
                <dl v-else class="grid gap-5 sm:grid-cols-2"><div v-for="(value, key) in record" :key="key" :class="['description', 'itinerary', 'body', 'reason'].includes(key) ? 'sm:col-span-2' : ''"><dt class="text-[10px] font-bold uppercase tracking-wider text-slate-400">{{ key.replaceAll('_', ' ') }}</dt><dd class="mt-2 whitespace-pre-wrap break-words text-sm leading-6">{{ value ?? '—' }}</dd></div></dl>
                <Link v-if="module === 'support'" :href="route('support.show', record.id)" class="panel-primary mt-5">Buka percakapan</Link>
                <a v-if="module === 'media' || (module === 'vendors' && record.document_id)" :href="route('media.show', module === 'media' ? record.id : record.document_id)" target="_blank" rel="noopener noreferrer" class="panel-secondary mt-5">Buka dokumen / media</a>
            </section>
            <aside class="panel-surface p-5"><h2 class="text-sm font-bold">Tindakan</h2><p class="mt-2 text-xs leading-6 text-slate-500">Setiap perubahan operasional dicatat. Aksi hanya berlaku jika status memenuhi syarat.</p><div v-if="canAct" class="mt-4 grid gap-2"><button v-for="action in definition.actions || []" :key="action" class="panel-secondary" @click="act(action)">{{ labels[action] }}</button></div><p v-else class="mt-4 text-xs text-slate-400">Tidak ada tindakan tambahan untuk akses ini.</p><form v-if="chosenAction.value" class="mt-5 border-t border-slate-100 pt-5" @submit.prevent="submitAction"><p class="mb-3 text-xs font-bold">{{ labels[chosenAction.value] }} data ini?</p><textarea v-model="workflow.note" placeholder="Catatan peninjauan (opsional)" class="panel-input" rows="3"></textarea><button class="panel-primary mt-3 w-full" :disabled="workflow.processing">Konfirmasi tindakan</button><button type="button" class="mt-3 w-full text-xs text-slate-500" @click="chosenAction.value = ''">Batal</button></form></aside>
        </div>
    </PanelLayout>
</template>
