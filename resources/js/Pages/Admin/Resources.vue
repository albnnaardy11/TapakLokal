<script setup>
import { ref, watch } from 'vue';
import { Link, router, useForm, usePage } from '@inertiajs/vue3';
import { route } from 'ziggy-js';
import { ArrowUpRight, Plus, Search, Upload, X } from 'lucide-vue-next';
import PanelLayout from '../../Components/Admin/PanelLayout.vue';
import Pagination from '../../Components/Admin/Pagination.vue';
import Fields from '../../Components/Admin/Fields.vue';

const props = defineProps({
    module: String,
    definition: Object,
    records: Object,
    filters: Object,
    navigation: Array,
    canManage: Boolean,
});

const page = usePage();
const resourceUrl = (action, record) =>
    route('admin.panel.resources.' + action, {
        panel: page.props.adminPanel.key,
        module: props.module,
        ...(record ? { record } : {}),
    });

const creating = ref(false);
const query = ref(props.filters.q || '');
const form = useForm({});
const upload = useForm({ file: null, alt_text: '', visibility: 'public' });

watch(
    () => props.module,
    () => {
        creating.value = false;
        query.value = props.filters.q || '';
        form.clearErrors();
    },
    { immediate: true },
);

const newRecord = () => {
    Object.entries(props.definition.fields).forEach(([key, field]) => {
        form[key] = field.type === 'number' ? 0 : field.options?.[0] || '';
    });
    creating.value = true;
};

const submit = () =>
    form
        .transform((data) =>
            Object.fromEntries(Object.keys(props.definition.fields).map((key) => [key, form[key]])),
        )
        .post(resourceUrl('store'), { onSuccess: () => (creating.value = false) });

const search = () => router.get(resourceUrl('index'), { q: query.value }, { preserveState: true });
const label = (value) => value.replaceAll('_', ' ');
</script>

<template>
    <PanelLayout
        :title="definition.label"
        :subtitle="'Kelola ' + definition.label.toLowerCase() + ' dalam satu alur kerja yang terpusat.'"
        :navigation="navigation"
    >
        <template #actions>
            <button
                v-if="canManage && Object.keys(definition.fields).length && definition.create !== false"
                class="inline-flex items-center gap-2 rounded-xl bg-gradient-to-r from-[#1677e8] to-[#078cff] px-4 py-2.5 text-xs font-bold text-white shadow-md shadow-blue-500/20 transition hover:opacity-95 active:scale-95"
                @click="newRecord"
            >
                <Plus class="size-4" />
                <span>Tambah data</span>
            </button>
            <button
                v-if="module === 'media' && canManage"
                class="inline-flex items-center gap-2 rounded-xl bg-gradient-to-r from-[#1677e8] to-[#078cff] px-4 py-2.5 text-xs font-bold text-white shadow-md shadow-blue-500/20 transition hover:opacity-95 active:scale-95"
                @click="creating = !creating"
            >
                <Upload class="size-4" />
                <span>Upload media</span>
            </button>
        </template>

        <!-- Form Create/Upload -->
        <section v-if="creating" class="mb-6 rounded-2xl border border-slate-200/80 bg-white p-6 shadow-sm">
            <div class="mb-5 flex items-center justify-between border-b border-slate-100 pb-3">
                <h2 class="text-sm font-bold text-slate-900">
                    {{ module === 'media' ? 'Upload Media Baru' : 'Tambah ' + definition.label + ' Baru' }}
                </h2>
                <button
                    class="rounded-lg p-1.5 text-slate-400 hover:bg-slate-100 hover:text-slate-700"
                    aria-label="Tutup formulir"
                    @click="creating = false"
                >
                    <X class="size-4" />
                </button>
            </div>
            <form v-if="module !== 'media'" @submit.prevent="submit">
                <Fields :fields="definition.fields" :form="form" />
                <button
                    class="mt-6 inline-flex items-center gap-2 rounded-xl bg-gradient-to-r from-[#1677e8] to-[#078cff] px-5 py-2.5 text-xs font-bold text-white shadow-md shadow-blue-500/20 transition hover:opacity-95 disabled:opacity-50"
                    :disabled="form.processing"
                >
                    Simpan Data
                </button>
            </form>
            <form
                v-else
                class="grid max-w-xl gap-4"
                @submit.prevent="
                    upload.post(route('media.store'), {
                        onSuccess: () => {
                            creating = false;
                            upload.reset();
                        },
                    })
                "
            >
                <div>
                    <label class="mb-1.5 block text-xs font-bold text-slate-700">Pilih Berkas</label>
                    <input
                        type="file"
                        accept="image/jpeg,image/png,image/webp,application/pdf"
                        required
                        class="w-full rounded-xl border border-slate-200 bg-slate-50 p-2.5 text-xs text-slate-600 file:mr-3 file:rounded-lg file:border-0 file:bg-blue-50 file:px-3 file:py-1 file:text-xs file:font-semibold file:text-blue-600 hover:file:bg-blue-100"
                        @change="upload.file = $event.target.files[0]"
                    />
                </div>
                <div>
                    <label class="mb-1.5 block text-xs font-bold text-slate-700">Alt Text / Deskripsi</label>
                    <input
                        v-model="upload.alt_text"
                        placeholder="Deskripsi gambar atau keterangan media"
                        class="panel-input"
                    />
                </div>
                <div>
                    <label class="mb-1.5 block text-xs font-bold text-slate-700">Visibilitas</label>
                    <select v-model="upload.visibility" class="panel-input">
                        <option value="public">Publik (Gambar umum / konten)</option>
                        <option value="private">Privat (Dokumen internal / reservasi)</option>
                    </select>
                </div>
                <p class="text-xs text-slate-400">Format yang didukung: JPG, PNG, WebP atau PDF. Ukuran maks 5 MB.</p>
                <button
                    class="inline-flex w-fit items-center gap-2 rounded-xl bg-gradient-to-r from-[#1677e8] to-[#078cff] px-5 py-2.5 text-xs font-bold text-white shadow-md shadow-blue-500/20 transition hover:opacity-95 disabled:opacity-50"
                    :disabled="upload.processing"
                >
                    Mulai Upload
                </button>
            </form>
        </section>

        <!-- Table Card -->
        <section class="overflow-hidden rounded-2xl border border-slate-200/80 bg-white shadow-sm">
            <div class="flex flex-wrap items-center justify-between gap-3 border-b border-slate-100 p-4 sm:px-6">
                <p class="text-xs font-medium text-slate-500">
                    <strong class="font-bold text-slate-900">{{ records.total }}</strong> data tercatat
                </p>
                <form class="relative w-full sm:w-72" @submit.prevent="search">
                    <Search class="absolute left-3 top-3 size-4 text-slate-400" />
                    <input
                        v-model="query"
                        type="search"
                        placeholder="Cari data, lalu Enter…"
                        class="w-full rounded-xl border border-slate-200 bg-slate-50/80 py-2 pl-9 pr-3 text-xs font-medium text-slate-800 outline-none transition focus:border-blue-400 focus:bg-white focus:ring-2 focus:ring-blue-100"
                        aria-label="Cari data"
                    />
                </form>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead class="bg-slate-50/80 text-[10px] font-extrabold uppercase tracking-wider text-slate-400">
                        <tr>
                            <th
                                v-for="column in definition.columns"
                                :key="column"
                                class="whitespace-nowrap px-6 py-3.5"
                            >
                                {{ label(column) }}
                            </th>
                            <th class="px-6 py-3.5 text-right font-extrabold">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-slate-700">
                        <tr
                            v-for="record in records.data"
                            :key="record.id"
                            class="transition-colors hover:bg-blue-50/25"
                        >
                            <td
                                v-for="column in definition.columns"
                                :key="column"
                                class="max-w-64 truncate px-6 py-4"
                            >
                                <span
                                    v-if="column === 'status'"
                                    class="inline-flex items-center rounded-lg px-2.5 py-1 text-[10px] font-bold uppercase tracking-wider"
                                    :class="
                                        ['published', 'verified', 'paid', 'completed', 'active'].includes(record[column])
                                            ? 'bg-emerald-50 text-emerald-700 border border-emerald-200/60'
                                            : ['failed', 'rejected', 'suspended'].includes(record[column])
                                              ? 'bg-rose-50 text-rose-600 border border-rose-200/60'
                                              : 'bg-slate-100 text-slate-600 border border-slate-200/60'
                                    "
                                >
                                    {{ record[column] }}
                                </span>
                                <template v-else>{{ record[column] ?? '—' }}</template>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <Link
                                    :href="resourceUrl('show', record.id)"
                                    class="inline-flex items-center gap-1 rounded-lg bg-blue-50 px-2.5 py-1 text-[11px] font-bold text-blue-600 transition hover:bg-blue-600 hover:text-white"
                                >
                                    <span>Buka</span>
                                    <ArrowUpRight class="size-3" />
                                </Link>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div v-if="!records.data.length" class="px-6 py-16 text-center">
                <p class="font-bold text-slate-700">Belum ada data</p>
                <p class="mt-1 text-xs text-slate-400">Data akan muncul setelah ditambahkan atau ada aktivitas terkait.</p>
            </div>

            <Pagination :records="records" />
        </section>

        <p v-if="['refunds', 'payouts'].includes(module)" class="mt-4 text-xs leading-6 text-slate-500">
            Persetujuan internal tidak memindahkan dana secara langsung. Status dibayar hanya boleh mengikuti hasil transaksi yang terverifikasi.
        </p>
    </PanelLayout>
</template>
