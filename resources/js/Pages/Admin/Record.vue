<script setup>
import { computed, ref, watch } from 'vue';
import { Head, Link, useForm, usePage } from '@inertiajs/vue3';
import { route } from 'ziggy-js';
import {
    AlertTriangle,
    ArrowLeft,
    Check,
    ExternalLink,
    MessageSquare,
    Save,
    Shield,
    Trash2,
} from 'lucide-vue-next';
import PanelLayout from '../../Components/Admin/PanelLayout.vue';
import Fields from '../../Components/Admin/Fields.vue';

const props = defineProps({
    module: String,
    definition: Object,
    record: Object,
    navigation: Array,
    canManage: Boolean,
    canDelete: Boolean,
    messages: Object,
});

const page = usePage();
const deleting = ref(false);
const deletion = useForm({});
const form = useForm(
    Object.fromEntries(
        Object.entries(props.definition.fields || {}).map(([key, field]) => [
            key,
            props.record[key] ?? (field.type === 'number' ? 0 : ''),
        ]),
    ),
);

watch(
    () => props.record,
    (record) => {
        Object.entries(props.definition.fields || {}).forEach(([key, field]) => {
            const value = record[key];
            form[key] =
                value && ['date', 'datetime-local'].includes(field.type)
                    ? value.slice(0, field.type === 'date' ? 10 : 16)
                    : value ?? (field.type === 'number' ? 0 : '');
        });
        form.clearErrors();
    },
    { immediate: true },
);

const workflow = useForm({ note: '' });
const resourceUrl = (action, record) =>
    route('admin.panel.resources.' + action, {
        panel: page.props.adminPanel.key,
        module: props.module,
        ...(record ? { record } : {}),
    });

const permission = computed(
    () =>
        ({
            vendors: 'vendor.verify',
            trips: 'operations.manage',
            bookings: 'operations.manage',
            refunds: 'refund.approve',
            payouts: 'payout.approve',
        }[props.module]),
);

const canAct = computed(() =>
    permission.value ? page.props.auth.permissions.includes(permission.value) : false,
);

const labels = {
    verify: 'Verifikasi vendor',
    reject: 'Tolak',
    suspend: 'Nonaktifkan',
    publish: 'Terbitkan',
    archive: 'Arsipkan',
    confirm: 'Konfirmasi',
    start: 'Mulai perjalanan',
    complete: 'Selesaikan',
    cancel: 'Batalkan',
    approve: 'Setujui',
};

const chosenAction = useForm({ value: '' });
const act = (action) => {
    chosenAction.value = action;
};

const submitAction = () =>
    workflow.post(
        route('admin.panel.workflow', {
            panel: page.props.adminPanel.key,
            module: props.module,
            record: props.record.id,
            action: chosenAction.value,
        }),
        { onSuccess: () => (chosenAction.value = '') },
    );

const submitUpdate = () => {
    form.put(resourceUrl('update', props.record.id));
};
</script>

<template>
    <PanelLayout
        :title="definition.label"
        :subtitle="'Edit & Kelola ' + definition.label + ' #' + record.id"
        :navigation="navigation"
    >
        <template #actions>
            <div class="flex items-center gap-2">
                <Link
                    :href="resourceUrl('index')"
                    class="inline-flex items-center gap-1.5 rounded-xl border border-slate-200 bg-white px-3.5 py-2 text-xs font-bold text-slate-700 shadow-sm transition hover:bg-slate-50"
                >
                    <ArrowLeft class="size-3.5" />
                    <span>Kembali ke daftar</span>
                </Link>
                <button
                    v-if="canDelete"
                    type="button"
                    class="inline-flex items-center gap-1.5 rounded-xl border border-rose-200 bg-rose-50 px-3.5 py-2 text-xs font-bold text-rose-600 transition hover:bg-rose-100 active:scale-95"
                    @click="deleting = !deleting"
                >
                    <Trash2 class="size-3.5" />
                    <span>Hapus data</span>
                </button>
            </div>
        </template>

        <!-- Deletion Confirmation Box -->
        <div
            v-if="deleting"
            class="mb-6 rounded-2xl border border-rose-200 bg-rose-50/90 p-5 text-xs text-rose-900 shadow-sm"
        >
            <div class="flex items-start gap-3">
                <AlertTriangle class="size-5 shrink-0 text-rose-600 mt-0.5" />
                <div class="flex-1">
                    <h3 class="font-bold text-sm text-rose-950">Konfirmasi Hapus Data</h3>
                    <p class="mt-1 text-slate-600 leading-relaxed">
                        Apakah Anda yakin ingin menghapus data ini dari website? Data yang dihapus tidak akan ditampilkan kembali ke pengunjung.
                    </p>
                    <form
                        class="mt-4 flex items-center gap-2"
                        @submit.prevent="deletion.delete(resourceUrl('destroy', record.id))"
                    >
                        <button
                            class="rounded-xl bg-rose-600 px-4 py-2 font-bold text-white shadow-md shadow-rose-600/20 transition hover:bg-rose-700 disabled:opacity-50"
                            :disabled="deletion.processing"
                        >
                            {{ deletion.processing ? 'Menghapus…' : 'Ya, Hapus Sekarang' }}
                        </button>
                        <button
                            type="button"
                            class="rounded-xl border border-slate-300 bg-white px-4 py-2 font-bold text-slate-700 hover:bg-slate-50"
                            @click="deleting = false"
                        >
                            Batal
                        </button>
                    </form>
                    <p v-if="deletion.errors.delete" class="mt-2 font-bold text-rose-700">
                        {{ deletion.errors.delete }}
                    </p>
                </div>
            </div>
        </div>

        <!-- Main Form & Details Grid -->
        <div class="grid items-start gap-6 xl:grid-cols-[minmax(0,1fr)_320px]">
            <!-- Form Card -->
            <section class="rounded-2xl border border-slate-200/80 bg-white p-6 shadow-sm sm:p-8">
                <div class="mb-6 flex items-center justify-between border-b border-slate-100 pb-4">
                    <div>
                        <h2 class="text-base font-bold text-slate-900">
                            {{ Object.keys(definition.fields || {}).length ? 'Formulir Perubahan Data' : 'Informasi Lengkap' }}
                        </h2>
                        <p class="text-xs text-slate-400 mt-0.5">
                            ID Referensi: <strong class="text-slate-700">#{{ record.id }}</strong>
                            <span v-if="record.status" class="ml-2 inline-flex items-center rounded-md bg-slate-100 px-2 py-0.5 text-[10px] font-bold uppercase tracking-wider text-slate-700">
                                {{ record.status }}
                            </span>
                        </p>
                    </div>
                </div>

                <!-- Editable Form with Fields -->
                <form
                    v-if="Object.keys(definition.fields || {}).length"
                    class="space-y-6"
                    @submit.prevent="submitUpdate"
                >
                    <Fields
                        :fields="definition.fields"
                        :form="form"
                        :disabled="!canManage"
                    />

                    <!-- Submit Button -->
                    <div v-if="canManage" class="pt-4 border-t border-slate-100 flex items-center gap-3">
                        <button
                            class="inline-flex items-center gap-2 rounded-xl bg-gradient-to-r from-[#1677e8] to-[#078cff] px-6 py-3 text-xs font-bold text-white shadow-lg shadow-blue-500/25 transition hover:opacity-95 active:scale-95 disabled:opacity-50"
                            :disabled="form.processing"
                        >
                            <Save class="size-4" />
                            <span>{{ form.processing ? 'Menyimpan…' : 'Simpan Perubahan' }}</span>
                        </button>
                        <span v-if="form.recentlySuccessful" class="flex items-center gap-1 text-xs font-semibold text-emerald-600">
                            <Check class="size-4" />
                            Tersimpan!
                        </span>
                    </div>
                </form>

                <!-- Readonly Data Display for non-field resources -->
                <dl v-else class="grid gap-5 sm:grid-cols-2">
                    <div
                        v-for="(value, key) in record"
                        :key="key"
                        :class="['description', 'itinerary', 'body', 'reason'].includes(key) ? 'sm:col-span-2' : ''"
                        class="rounded-xl border border-slate-100 bg-slate-50/50 p-3.5"
                    >
                        <dt class="text-[10px] font-extrabold uppercase tracking-wider text-slate-400">
                            {{ String(key).replaceAll('_', ' ') }}
                        </dt>
                        <dd class="mt-1.5 whitespace-pre-wrap break-words text-xs font-medium leading-relaxed text-slate-800">
                            {{ value ?? '—' }}
                        </dd>
                    </div>
                </dl>

                <!-- Additional Links -->
                <div class="mt-6 flex flex-wrap items-center gap-3 pt-4 border-t border-slate-100">
                    <Link
                        v-if="module === 'support'"
                        :href="route('support.show', record.id)"
                        class="inline-flex items-center gap-2 rounded-xl bg-blue-50 px-4 py-2.5 text-xs font-bold text-blue-600 hover:bg-blue-100 transition"
                    >
                        <MessageSquare class="size-4" />
                        <span>Buka Percakapan Tiket</span>
                    </Link>
                    <a
                        v-if="module === 'media' || (module === 'vendors' && record.document_id)"
                        :href="route('media.show', module === 'media' ? record.id : record.document_id)"
                        target="_blank"
                        rel="noopener noreferrer"
                        class="inline-flex items-center gap-2 rounded-xl border border-slate-200 px-4 py-2.5 text-xs font-bold text-slate-700 hover:bg-slate-50 transition"
                    >
                        <ExternalLink class="size-4 text-slate-400" />
                        <span>Buka Dokumen / Media Terlampir</span>
                    </a>
                </div>
            </section>

            <!-- Actions / Workflow Sidebar -->
            <aside class="rounded-2xl border border-slate-200/80 bg-white p-6 shadow-sm">
                <div class="flex items-center gap-2 text-slate-900 font-bold text-sm">
                    <Shield class="size-4 text-blue-600" />
                    <h2>Aksi & Workflow</h2>
                </div>
                <p class="mt-1 text-xs leading-relaxed text-slate-400">
                    Aksi verifikasi dan workflow dicatat pada riwayat audit sistem.
                </p>

                <div v-if="canAct && (definition.actions || []).length" class="mt-5 space-y-2">
                    <button
                        v-for="action in definition.actions"
                        :key="action"
                        type="button"
                        class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-2.5 text-left text-xs font-bold text-slate-700 transition hover:border-blue-300 hover:bg-blue-50 hover:text-blue-700"
                        @click="act(action)"
                    >
                        {{ labels[action] || action }}
                    </button>
                </div>
                <div v-else class="mt-4 rounded-xl bg-slate-50 p-3 text-center text-xs text-slate-400">
                    Tidak ada aksi operasional khusus untuk modul ini.
                </div>

                <!-- Action Confirmation Form -->
                <form
                    v-if="chosenAction.value"
                    class="mt-5 border-t border-slate-100 pt-4"
                    @submit.prevent="submitAction"
                >
                    <p class="mb-2 text-xs font-bold text-slate-800">
                        {{ labels[chosenAction.value] || chosenAction.value }}?
                    </p>
                    <textarea
                        v-model="workflow.note"
                        placeholder="Catatan verifikasi (opsional)"
                        class="w-full rounded-xl border border-slate-200 bg-white p-2.5 text-xs text-slate-800 outline-none focus:border-blue-400 focus:ring-2 focus:ring-blue-100"
                        rows="3"
                    ></textarea>
                    <button
                        class="mt-3 flex w-full items-center justify-center rounded-xl bg-blue-600 py-2.5 text-xs font-bold text-white shadow-md transition hover:bg-blue-700 disabled:opacity-50"
                        :disabled="workflow.processing"
                    >
                        Konfirmasi Tindakan
                    </button>
                    <button
                        type="button"
                        class="mt-2 w-full text-center text-xs text-slate-400 hover:text-slate-600"
                        @click="chosenAction.value = ''"
                    >
                        Batal
                    </button>
                </form>
            </aside>
        </div>
    </PanelLayout>
</template>
