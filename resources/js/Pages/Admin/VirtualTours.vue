<script setup>
import { ref } from 'vue';
import { Link, useForm } from '@inertiajs/vue3';
import { route } from 'ziggy-js';
import PanelLayout from '../../Components/Admin/PanelLayout.vue';
import PanoramaViewer from '../../Components/Shared/PanoramaViewer.vue';
defineProps({ tours: Object, navigation: Array });
const editing = ref(null), preview = ref(null), search = ref(''), targets = ref([]), targetError = ref('');
const blank = { title: '', slug: '', description: '', status: 'draft', placement: 'homepage', trip_id: '', content_page_id: '', position: 0, file: null };
const form = useForm({ ...blank });
function edit(tour) {
    editing.value = tour?.id || null; form.clearErrors();
    Object.assign(form, blank, tour ? Object.fromEntries(Object.keys(blank).filter(key => key !== 'file').map(key => [key, tour[key] ?? ''])) : {});
    targets.value = tour?.target_title ? [{ id: tour.trip_id || tour.content_page_id, title: tour.target_title }] : [];
    document.getElementById('tour-editor')?.scrollIntoView({ behavior: 'smooth' });
}
async function findTargets() {
    targetError.value = '';
    try {
        const response = await fetch(route('admin.tours.targets', { type: form.placement, q: search.value }), { headers: { Accept: 'application/json' } });
        if (!response.ok) throw new Error('Pencarian gagal. Periksa koneksi atau sesi login.');
        targets.value = await response.json();
    } catch (error) { targetError.value = error.message; }
}
function save() { form.post(editing.value ? route('admin.tours.update', editing.value) : route('admin.tours.store'), { forceFormData: true, preserveScroll: true, onSuccess: () => edit(null) }); }
</script>
<template>
    <PanelLayout title="Panorama 360°" subtitle="Unggah panorama, pilih penempatan, lalu terbitkan." :navigation="navigation">
        <div class="grid items-start gap-6 xl:grid-cols-[minmax(0,1fr)_380px]">
            <section class="panel-surface overflow-hidden"><div class="flex items-center justify-between border-b border-slate-100 p-5"><h2 class="font-bold">Koleksi panorama</h2><button class="panel-secondary" @click="edit(null)">Tambah panorama</button></div><p v-if="!tours.data.length" class="p-6 text-sm text-slate-500">Belum ada panorama. Unggah foto pertama untuk memulai.</p><article v-for="tour in tours.data" :key="tour.id" class="border-b border-slate-100 p-5"><div class="flex flex-wrap items-center justify-between gap-3"><div><h3 class="font-semibold">{{ tour.title }}</h3><p class="mt-1 text-xs text-slate-500">{{ tour.status }} · {{ tour.placement }} · Urutan {{ tour.position }}</p><p v-if="tour.target_title" class="mt-1 text-xs text-slate-500">{{ tour.target_title }}</p></div><div class="flex gap-2"><button class="panel-secondary" @click="preview = preview?.id === tour.id ? null : tour.preview">Pratinjau</button><button class="panel-primary" @click="edit(tour)">Atur</button></div></div><div v-if="preview?.id === tour.id" class="mt-4"><PanoramaViewer :key="tour.id" :tour="preview" /></div></article><nav class="flex flex-wrap gap-2 p-5" aria-label="Halaman koleksi"><Link v-for="link in tours.links" :key="link.label" :href="link.url || '#'" class="rounded-lg px-3 py-2 text-xs" :class="link.active ? 'bg-blue-600 text-white' : 'bg-slate-100'" :aria-disabled="!link.url"><span v-html="link.label"></span></Link></nav></section>
            <form id="tour-editor" class="panel-surface grid gap-4 p-6" @submit.prevent="save"><h2 class="font-bold">{{ editing ? 'Atur panorama' : 'Panorama baru' }}</h2><label class="text-xs font-semibold">Judul<input v-model="form.title" class="panel-input mt-2" required maxlength="180" /></label><label class="text-xs font-semibold">Slug<input v-model="form.slug" class="panel-input mt-2" required maxlength="180" pattern="[a-z0-9]+(-[a-z0-9]+)*" /></label><label class="text-xs font-semibold">Deskripsi<textarea v-model="form.description" class="panel-input mt-2" maxlength="3000" /></label><label class="text-xs font-semibold">Foto panorama<input :key="editing || 'new'" type="file" accept="image/jpeg,image/png,image/webp" class="mt-2 block w-full text-xs" :required="!editing" @change="form.file = $event.target.files[0]" /></label><p class="text-xs leading-5 text-slate-500">JPG/PNG/WebP maksimal 25 MB. Gunakan rasio 2:1 atau foto dengan metadata GPano. Foto dioptimalkan otomatis; area yang tidak direkam ditampilkan gelap.</p><label class="text-xs font-semibold">Tampilkan di<select v-model="form.placement" class="panel-input mt-2" @change="targets = []; form.trip_id = ''; form.content_page_id = ''"><option value="homepage">Beranda</option><option value="trip">Detail trip</option><option value="destination">Destinasi / Hidden Gem</option></select></label><div v-if="form.placement !== 'homepage'" class="grid gap-2"><label class="text-xs font-semibold">Cari halaman tujuan<input v-model="search" class="panel-input mt-2" placeholder="Judul trip atau destinasi" /></label><button type="button" class="panel-secondary" @click="findTargets">Cari</button><p v-if="targetError" role="alert" class="text-xs text-rose-600">{{ targetError }}</p><select v-if="form.placement === 'trip'" v-model="form.trip_id" required class="panel-input" aria-label="Trip tujuan"><option value="">Pilih trip</option><option v-for="target in targets" :key="target.id" :value="target.id">{{ target.title }} {{ target.status ? '(' + target.status + ')' : '' }}</option></select><select v-else v-model="form.content_page_id" required class="panel-input" aria-label="Destinasi tujuan"><option value="">Pilih destinasi</option><option v-for="target in targets" :key="target.id" :value="target.id">{{ target.title }} {{ target.status ? '(' + target.status + ')' : '' }}</option></select></div><label class="text-xs font-semibold">Urutan<input v-model.number="form.position" class="panel-input mt-2" type="number" min="0" max="10000" required /></label><label class="text-xs font-semibold">Status<select v-model="form.status" class="panel-input mt-2"><option value="draft">Draft</option><option value="published">Terbit</option><option value="archived">Arsip</option></select></label><p class="text-xs leading-5 text-slate-500">Panorama hanya tampil jika berstatus Terbit dan halaman tujuannya sudah diterbitkan. Trip juga harus berasal dari vendor terverifikasi.</p><p v-for="(message, key) in form.errors" :key="key" role="alert" class="text-xs text-rose-600">{{ message }}</p><progress v-if="form.progress" :value="form.progress.percentage" max="100" class="w-full" /><button class="panel-primary" :disabled="form.processing">{{ form.processing ? 'Menyimpan…' : 'Simpan pengaturan' }}</button></form>
        </div>
    </PanelLayout>
</template>
