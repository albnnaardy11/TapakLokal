<script setup>
import { useForm, Link } from '@inertiajs/vue3';
import { ref, watch } from 'vue';
import { route } from 'ziggy-js';
import PanelLayout from '../../Components/Admin/PanelLayout.vue';
import Fields from '../../Components/Admin/Fields.vue';
import TripRichEditor from '../../Components/Admin/TripRichEditor.vue';
import TripPhotoUpload from '../../Components/Admin/TripPhotoUpload.vue';
import TripExperienceFields from '../../Components/Admin/TripExperienceFields.vue';
import { plainTripHtml, textToTripHtml } from '../../Composables/tripRichText';

const props = defineProps({ trip: Object });
const deleting = ref(false);
const deletion = useForm({});
const pendingUploads = ref(0);
const uploadState = busy => { pendingUploads.value = Math.max(0, pendingUploads.value + (busy ? 1 : -1)); };

const fields = {
    title: { label: 'Nama trip', type: 'text', required: true },
    slug: {
        label: 'Slug (huruf kecil dan tanda -)',
        type: 'text',
        required: true,
        readonly: true,
        placeholder: 'Otomatis dibuat dari nama trip…',
    },
    type: { label: 'Jenis layanan', type: 'select', options: ['open-trip', 'private-trip'], required: true },
    destination: { label: 'Destinasi', type: 'text', required: true },
    departure_date: { label: 'Tanggal keberangkatan', type: 'date', required: true },
    end_date: { label: 'Tanggal selesai', type: 'date', required: true },
    capacity: { label: 'Kuota peserta', type: 'number', required: true },
    price: { label: 'Harga dasar vendor per peserta (IDR)', type: 'currency', required: true },
    meeting_point: { label: 'Titik kumpul', type: 'text', required: true },
    image_url: { label: 'Foto utama trip', type: 'image', required: false },
    description: { label: 'Deskripsi', type: 'textarea', required: true },
    itinerary: { label: 'Itinerary & fasilitas', type: 'textarea', required: true },
    experience: { label: 'Galeri & detail perjalanan', type: 'structured', required: false },
};

const form = useForm({
    status: 'draft',
    ...Object.fromEntries(
        Object.entries(fields).map(([key, field]) => [
            key,
            field.type === 'date'
                ? props.trip?.[key]?.slice(0, 10) || ''
                : key === 'status'
                  ? 'draft'
                  : props.trip?.[key] ?? field.options?.[0] ?? (field.type === 'number' ? 1 : ''),
        ]),
    ),
});

const previous = props.trip?.experience || {};
const existingImages = [...new Set([props.trip?.image_url, ...(previous.detail?.images || []), ...(previous.gallery || [])].filter(Boolean))].slice(0, 5);
form.experience = {
    ...previous,
    description_html: previous.description_html || textToTripHtml(props.trip?.description || ''),
    itinerary_html: previous.itinerary_html || textToTripHtml(props.trip?.itinerary || ''),
    detail: { ...previous.detail, subtitle: previous.detail?.subtitle || '', coordinates: previous.detail?.coordinates || '', images: Array.from({ length: 5 }, (_, i) => existingImages[i] || '') },
    highlights: (previous.highlights || []).map(item => ({ ...item, icon: item.icon || 'Compass', images: item.images || (item.image_url ? [item.image_url] : []) })),
    destinations: (previous.destinations || []).map(item => ({ ...item, image_url: item.image_url || item.image || '' })),
    itineraryDays: previous.itineraryDays || [], facilityDetails: previous.facilityDetails || [],
    included: previous.included || previous.includes || [], excluded: previous.excluded || previous.excludes || [],
    packingItems: previous.packingItems || [], faqs: previous.faqs || [], panoramas: previous.panoramas || [],
    facilities: previous.facilities || [],
};

const slugify = (text) => {
    return (text || '')
        .toString()
        .toLowerCase()
        .normalize('NFD')
        .replace(/[\u0300-\u036f]/g, '')
        .replace(/[^a-z0-9]+/g, '-')
        .replace(/^-+|-+$/g, '')
        .replace(/-+/g, '-');
};

watch(
    () => form.title,
    (newTitle) => {
        form.slug = slugify(newTitle);
    },
);

const fieldGroups = [
    {
        title: 'Informasi perjalanan',
        description: 'Identitas dan destinasi yang akan dilihat pelanggan.',
        keys: ['title', 'slug', 'type', 'destination'],
    },
    {
        title: 'Jadwal & harga',
        description: 'Pastikan tanggal, kuota, dan harga sudah sesuai.',
        keys: ['departure_date', 'end_date', 'capacity', 'price', 'meeting_point'],
    },
    {
        title: 'Foto & pengalaman',
        description: 'Jelaskan agenda dan fasilitas dengan lengkap.',
        keys: [],
    },
];

const groupFields = (group) => Object.fromEntries(group.keys.map((key) => [key, fields[key]]));
const navigation = [{ key: 'trips', label: 'Trip & Jadwal', group: 'Operasional', url: route('vendor.section', 'trips') }];
const submit = () => {
    if (pendingUploads.value) return;
    form.clearErrors();
    const experience = JSON.parse(JSON.stringify(form.experience));
    for (const item of experience.highlights) delete item.image_url;
    experience.detail.images = experience.detail.images.filter(Boolean);
    for (const key of ['included', 'excluded', 'packingItems']) experience[key] = experience[key].map(item => item.trim()).filter(Boolean);
    for (const day of experience.itineraryDays) day.activities = day.activities.map(item => item.trim()).filter(Boolean);
    if (form.status === 'pending' && experience.detail.images.length !== 5) {
        form.setError('experience.detail.images', 'Unggah lima foto perjalanan sebelum mengirim ke admin.');
        return;
    }
    form.transform(data => ({ ...data, experience, image_url: experience.detail.images[0] || '', description: plainTripHtml(experience.description_html), itinerary: plainTripHtml(experience.itinerary_html) }));
    return props.trip ? form.put(route('vendor.trips.update', props.trip.id)) : form.post(route('vendor.trips.store'));
};
</script>

<template>
    <PanelLayout
        :title="trip ? 'Kelola Trip' : 'Trip Baru'"
        subtitle="Satu trip mewakili satu jadwal keberangkatan. Ajukan untuk ditinjau sebelum tampil di katalog."
        :navigation="navigation"
        vendor
    >
        <template #actions>
            <Link :href="route('vendor.section', 'trips')" class="panel-secondary">
                Kembali
            </Link>
        </template>

        <form class="panel-surface min-w-0 max-w-5xl p-5 sm:p-8 [overflow-wrap:anywhere]" @submit.prevent="submit">
            <div class="mb-7 border-b border-slate-100 pb-5">
                <p class="text-xs font-bold uppercase tracking-widest text-blue-600">Detail penawaran</p>
                <h2 class="mt-2 text-xl font-bold text-[#103b60]">Siapkan pengalaman perjalanan Anda</h2>
                <p class="mt-2 text-sm leading-6 text-slate-500">
                    Lengkapi informasi di bawah. Trip akan tampil di katalog setelah disetujui admin operasional.
                </p>
            </div>

            <section
                v-for="(group, index) in fieldGroups"
                :key="group.title"
                class="mb-8 border-b border-slate-100 pb-8 last:border-0"
            >
                <div class="mb-5 flex gap-3">
                    <span class="grid size-8 shrink-0 place-items-center rounded-full bg-blue-50 text-sm font-bold text-blue-600">
                        {{ index + 1 }}
                    </span>
                    <div>
                        <h3 class="font-bold text-[#103b60]">{{ group.title }}</h3>
                        <p class="mt-1 text-sm text-slate-500">{{ group.description }}</p>
                    </div>
                </div>
                <Fields :fields="groupFields(group)" :form="form" />
                <div v-if="index === 0" class="mt-5 space-y-5">
                    <label class="grid gap-2 text-xs font-semibold text-slate-600">Ringkasan singkat di bawah judul <span class="font-normal">Maksimal 350 karakter. Deskripsi lengkap ditampilkan pada tab Deskripsi.</span><textarea v-model="form.experience.detail.subtitle" maxlength="350" rows="3" class="panel-input"></textarea></label>
                    <div><p class="mb-2 text-xs font-semibold text-slate-600">Deskripsi lengkap <span class="text-rose-500">Wajib</span></p><TripRichEditor v-model="form.experience.description_html" label="Deskripsi perjalanan" :disabled="form.processing" /></div>
                </div>
                <div v-if="index === 1" class="mt-5 space-y-5">
                    <div class="grid gap-4 sm:grid-cols-2"><label class="grid gap-2 text-xs font-semibold">Waktu kumpul<input v-model="form.experience.detail.meetingTime" class="panel-input" placeholder="06.30 WIB" /></label><label class="grid gap-2 text-xs font-semibold">Anjuran datang lebih awal<input v-model="form.experience.detail.arrivalNote" class="panel-input" placeholder="30 menit" /></label><label class="grid gap-2 text-xs font-semibold sm:col-span-2">Catatan titik kumpul<textarea v-model="form.experience.detail.meetingNote" rows="3" class="panel-input" /></label></div>
                    <label class="grid gap-2 text-xs font-semibold text-slate-600">Koordinat titik kumpul (opsional)<input v-model="form.experience.detail.coordinates" placeholder="Contoh: -6.2000, 106.8166" class="panel-input" /><span class="font-normal text-slate-400">Jika kosong, peta memakai nama titik kumpul dan destinasi.</span></label>
                    <div><p class="mb-2 text-xs font-semibold text-slate-600">Penjelasan itinerary <span class="text-rose-500">Wajib</span></p><TripRichEditor v-model="form.experience.itinerary_html" label="Penjelasan itinerary" :disabled="form.processing" /></div>
                </div>
                <div v-if="index === 2" class="space-y-5">
                    <p class="text-sm leading-6 text-slate-500">Unggah <strong>5 foto berbeda</strong> sebelum mengirim pengajuan. Foto pertama menjadi sampul. JPG, PNG, atau WebP, maksimal 15 MB per foto.</p>
                    <div class="grid min-w-0 gap-3 sm:grid-cols-2 lg:grid-cols-3"><TripPhotoUpload v-for="(_, photoIndex) in form.experience.detail.images" :key="photoIndex" v-model="form.experience.detail.images[photoIndex]" :label="photoIndex === 0 ? '1. Foto sampul' : `${photoIndex + 1}. Foto galeri`" :disabled="form.processing" @busy="uploadState" /></div>
                    <p v-if="form.errors['experience.detail.images']" role="alert" class="text-sm text-rose-600">{{ form.errors['experience.detail.images'] }}</p>
                    <TripExperienceFields v-model="form.experience" :disabled="form.processing || pendingUploads > 0" @busy="uploadState" />
                </div>
            </section>

            <div v-if="Object.keys(form.errors).length" role="alert" class="rounded-xl bg-rose-50 p-4 text-sm text-rose-700"><p class="mb-2 font-bold">Periksa kembali data berikut:</p><p v-for="(error, key) in form.errors" :key="key">{{ error }}</p></div>
            <p v-if="pendingUploads" role="status" class="text-sm text-blue-600">Tunggu hingga semua foto selesai diunggah sebelum menyimpan.</p>

            <div class="mt-8 flex flex-wrap justify-end gap-3 border-t border-slate-100 pt-5">
                <button
                    type="submit"
                    :disabled="form.processing || pendingUploads > 0"
                    class="panel-secondary"
                    @click="form.status = 'draft'"
                >
                    Simpan draf
                </button>
                <button
                    type="submit"
                    :disabled="form.processing || pendingUploads > 0"
                    class="panel-primary"
                    @click="form.status = 'pending'"
                >
                    {{ form.processing ? 'Menyimpan…' : 'Kirim ke admin' }}
                </button>
            </div>
        </form>

        <div v-if="trip" class="mt-8 max-w-5xl rounded-2xl border border-rose-200 bg-rose-50/50 p-6">
            <div class="flex flex-wrap items-center justify-between gap-4">
                <div>
                    <h3 class="text-sm font-bold text-rose-900">Hapus Trip Ini</h3>
                    <p class="mt-1 text-xs text-rose-700">Trip yang dihapus akan ditarik dari katalog publik.</p>
                </div>
                <button
                    type="button"
                    class="rounded-xl border border-rose-300 bg-white px-4 py-2 text-xs font-bold text-rose-600 shadow-xs transition hover:bg-rose-600 hover:text-white"
                    @click="deleting = !deleting"
                >
                    {{ deleting ? 'Batal' : 'Hapus trip' }}
                </button>
            </div>
            <form
                v-if="deleting"
                class="mt-4 rounded-xl border border-rose-200 bg-white p-4"
                @submit.prevent="deletion.delete(route('vendor.trips.destroy', trip.id))"
            >
                <p class="text-xs font-medium text-slate-700">Apakah Anda yakin ingin menghapus trip <strong>"{{ trip.title }}"</strong>? Tindakan ini tidak dapat dibatalkan.</p>
                <div class="mt-4 flex items-center gap-3">
                    <button
                        type="submit"
                        class="rounded-xl bg-rose-600 px-4 py-2 text-xs font-bold text-white shadow-xs transition hover:bg-rose-700 disabled:opacity-50"
                        :disabled="deletion.processing"
                    >
                        {{ deletion.processing ? 'Menghapus…' : 'Ya, hapus permanen' }}
                    </button>
                    <button
                        type="button"
                        class="rounded-xl border border-slate-200 bg-slate-50 px-4 py-2 text-xs font-semibold text-slate-600 transition hover:bg-slate-100"
                        @click="deleting = false"
                    >
                        Batal
                    </button>
                </div>
                <p v-if="deletion.errors.delete" class="mt-3 text-xs font-semibold text-rose-600">{{ deletion.errors.delete }}</p>
            </form>
        </div>
    </PanelLayout>
</template>
