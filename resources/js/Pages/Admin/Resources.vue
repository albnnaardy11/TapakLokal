<script setup>
import { ref, watch } from 'vue';
import { Link, router, useForm } from '@inertiajs/vue3';
import { route } from 'ziggy-js';
import { ArrowUpRight, Plus, Search, Upload, X } from 'lucide-vue-next';
import PanelLayout from '../../Components/Admin/PanelLayout.vue';
import Pagination from '../../Components/Admin/Pagination.vue';
import Fields from '../../Components/Admin/Fields.vue';
const props = defineProps({ module: String, definition: Object, records: Object, filters: Object, navigation: Array, canManage: Boolean });
const creating = ref(false);
const query = ref(props.filters.q || '');
const form = useForm({});
const upload = useForm({ file: null, alt_text: '', visibility: 'public' });
watch(() => props.module, () => { creating.value = false; query.value = props.filters.q || ''; form.clearErrors(); }, { immediate: true });
const newRecord = () => { Object.entries(props.definition.fields).forEach(([key, field]) => form[key] = field.type === 'number' ? 0 : field.options?.[0] || ''); creating.value = true; };
const submit = () => form.transform(data => Object.fromEntries(Object.keys(props.definition.fields).map(key => [key, form[key]]))).post(route('admin.resources.store', props.module), { onSuccess: () => creating.value = false });
const search = () => router.get(route('admin.resources.index', props.module), { q: query.value }, { preserveState: true });
const label = value => value.replaceAll('_', ' ');
</script>
<template>
    <PanelLayout :title="definition.label" :subtitle="'Kelola ' + definition.label.toLowerCase() + ' dalam satu alur kerja yang terpusat.'" :navigation="navigation">
        <template #actions><button v-if="canManage && Object.keys(definition.fields).length && definition.create !== false" class="panel-primary" @click="newRecord"><Plus class="size-4" />Tambah data</button><button v-if="module === 'media' && canManage" class="panel-primary" @click="creating = !creating"><Upload class="size-4" />Upload media</button></template>
        <section v-if="creating" class="panel-surface mb-6 p-5 sm:p-6">
            <div class="mb-5 flex items-center justify-between"><h2 class="text-sm font-bold">{{ module === 'media' ? 'Upload media' : 'Data baru' }}</h2><button aria-label="Tutup formulir" @click="creating = false"><X class="size-4" /></button></div>
            <form v-if="module !== 'media'" @submit.prevent="submit"><Fields :fields="definition.fields" :form="form" /><button class="panel-primary mt-5" :disabled="form.processing">Simpan data</button></form>
            <form v-else class="grid max-w-xl gap-4" @submit.prevent="upload.post(route('media.store'), { onSuccess: () => { creating = false; upload.reset(); } })"><input type="file" accept="image/jpeg,image/png,image/webp,application/pdf" required @change="upload.file = $event.target.files[0]" /><input v-model="upload.alt_text" placeholder="Deskripsi gambar / alt text" class="panel-input" /><select v-model="upload.visibility" class="panel-input"><option value="public">Publik (gambar)</option><option value="private">Privat</option></select><p class="text-xs text-slate-400">JPG, PNG, WebP atau PDF privat. Maksimal 5 MB.</p><button class="panel-primary w-fit" :disabled="upload.processing">Upload</button></form>
        </section>
        <section class="panel-surface">
            <div class="flex flex-wrap items-center justify-between gap-3 border-b border-slate-100 p-4"><p class="text-xs text-slate-500"><strong class="text-slate-800">{{ records.total }}</strong> data tercatat</p><form class="relative w-full sm:w-72" @submit.prevent="search"><Search class="absolute left-3 top-3 size-4 text-slate-400" /><input v-model="query" type="search" placeholder="Cari data, lalu Enter…" class="panel-input pl-9" aria-label="Cari data" /></form></div>
            <div class="overflow-x-auto"><table class="w-full text-left text-xs"><thead class="bg-slate-50/70 text-[10px] uppercase tracking-wide text-slate-400"><tr><th v-for="column in definition.columns" :key="column" class="whitespace-nowrap px-5 py-3 font-semibold">{{ label(column) }}</th><th class="px-5 py-3">Detail</th></tr></thead><tbody><tr v-for="record in records.data" :key="record.id" class="border-t border-slate-100 hover:bg-blue-50/20"><td v-for="column in definition.columns" :key="column" class="max-w-64 truncate px-5 py-4"><span v-if="column === 'status'" class="rounded-md px-2 py-1 text-[10px] font-semibold" :class="['published', 'verified', 'paid', 'completed', 'active'].includes(record[column]) ? 'bg-emerald-50 text-emerald-700' : ['failed', 'rejected', 'suspended'].includes(record[column]) ? 'bg-rose-50 text-rose-600' : 'bg-slate-100 text-slate-600'">{{ record[column] }}</span><template v-else>{{ record[column] ?? '—' }}</template></td><td class="px-5 py-4"><Link :href="route('admin.resources.show', [module, record.id])" class="inline-flex items-center gap-1 font-semibold text-blue-600">Buka<ArrowUpRight class="size-3" /></Link></td></tr></tbody></table></div>
            <div v-if="!records.data.length" class="px-6 py-16 text-center"><p class="font-semibold text-slate-600">Belum ada data</p><p class="mt-2 text-xs text-slate-400">Data akan muncul setelah ditambahkan atau ada aktivitas terkait.</p></div>
            <Pagination :records="records" />
        </section>
        <p v-if="['refunds', 'payouts'].includes(module)" class="mt-4 text-xs leading-6 text-slate-500">Persetujuan internal tidak memindahkan dana. Status dibayar hanya boleh mengikuti hasil transaksi yang terverifikasi.</p>
    </PanelLayout>
</template>

