<script setup>
import { Head } from '@inertiajs/vue3';
import { computed, onBeforeUnmount, onMounted, ref } from 'vue';
import { BedDouble, CalendarDays, Camera, CheckCircle2, ChevronLeft, ChevronRight, Clock3, Compass, MapPin, Minus, Plus, Search, ShieldCheck, Star, Users, Utensils, X, XCircle } from 'lucide-vue-next';
import MainNavigation from '../Components/Home/MainNavigation.vue';

const props = defineProps({
    tripType: { type: String, required: true },
    trip: { type: String, required: true },
});

const isPrivateTrip = computed(() => props.tripType === 'private-trip');
const selectedImage = ref(0);
const isGalleryOpen = ref(false);
const galleryImageIndex = ref(0);
const activeTab = ref('Deskripsi');
const isDescriptionExpanded = ref(false);
const selectedHighlight = ref(0);
const highlightPage = ref(0);
const selectedHighlightPhoto = ref(0);
const selectedDestination = ref(0);
const routeSaved = ref(false);
const mapSearch = ref('');
const mapZoom = ref(12);
const travelers = ref(2);
const bookingMessage = ref('');

const openTrip = {
    title: 'Open Trip Tur Pulau Pramuka',
    startPoint: 'start Dermaga Kaliadem',
    duration: '2H1M',
    subtitle: 'Nikmati keindahan pulau-pulau eksotis di Kepulauan Seribu, mulai dari snorkeling, jelajah pulau, hingga bersantai di pantai pasir putih.',
    location: 'Kepulauan Seribu, DKI Jakarta',
    coordinates: '-5.7454,106.5834',
    price: 'Rp 450.000',
    originalPrice: 'Rp 550.000',
    label: 'Open Trip',
    date: 'Setiap Sabtu · Minggu',
    images: [
        'https://images.unsplash.com/photo-1516690561799-46d8f74f9abf?auto=format&fit=crop&w=1400&q=88',
        'https://images.unsplash.com/photo-1544551763-46a013bb70d5?auto=format&fit=crop&w=900&q=85',
        'https://images.unsplash.com/photo-1537996194471-e657df975ab4?auto=format&fit=crop&w=900&q=85',
        'https://images.unsplash.com/photo-1518548419970-58e3b4079ab2?auto=format&fit=crop&w=900&q=85',
    ],
};

const privateTrip = {
    title: 'Private Trip Labuan Bajo',
    startPoint: 'start Pelabuhan Labuan Bajo',
    duration: '3H2M',
    subtitle: 'Susun perjalanan privat bersama orang terdekatmu untuk menikmati pulau, laut, dan matahari terbenam Labuan Bajo dengan ritme yang kamu pilih sendiri.',
    location: 'Labuan Bajo, Nusa Tenggara Timur',
    coordinates: '-8.4964,119.8877',
    price: 'Rp 2.850.000',
    originalPrice: 'Rp 3.250.000',
    label: 'Private Trip',
    date: 'Pilih tanggal keberangkatanmu',
    images: [
        'https://images.unsplash.com/photo-1507525428034-b723cf961d3e?auto=format&fit=crop&w=1400&q=88',
        'https://images.unsplash.com/photo-1514282401047-d79a71a590e8?auto=format&fit=crop&w=900&q=85',
        'https://images.unsplash.com/photo-1544550285-f813152fb2fd?auto=format&fit=crop&w=900&q=85',
        'https://images.unsplash.com/photo-1493552152660-f915ab47ae9d?auto=format&fit=crop&w=900&q=85',
    ],
};

const detail = computed(() => isPrivateTrip.value ? privateTrip : openTrip);
const mapEmbedUrl = computed(() => {
    const mapTarget = mapSearch.value.trim() || detail.value.coordinates;

    return `https://www.google.com/maps?q=${encodeURIComponent(mapTarget)}&z=${mapZoom.value}&output=embed`;
});
const galleryImages = computed(() => [
    ...detail.value.images,
    'https://images.unsplash.com/photo-1470165518243-ff5f2f6f9f37?auto=format&fit=crop&w=1200&q=85',
    'https://images.unsplash.com/photo-1518509562904-e7ef99cdcc86?auto=format&fit=crop&w=1200&q=85',
    'https://images.unsplash.com/photo-1505881502353-a1986add3762?auto=format&fit=crop&w=1200&q=85',
    'https://images.unsplash.com/photo-1468413253725-0d5181091126?auto=format&fit=crop&w=1200&q=85',
    'https://images.unsplash.com/photo-1502680390469-be75c86b636f?auto=format&fit=crop&w=1200&q=85',
    'https://images.unsplash.com/photo-1510414842594-a61c69b5ae57?auto=format&fit=crop&w=1200&q=85',
    'https://images.unsplash.com/photo-1540541338287-41700207dee6?auto=format&fit=crop&w=1200&q=85',
    'https://images.unsplash.com/photo-1510414842594-a61c69b5ae57?auto=format&fit=crop&w=1200&q=85',
    'https://images.unsplash.com/photo-1559825481-12a05cc00344?auto=format&fit=crop&w=1200&q=85',
    'https://images.unsplash.com/photo-1540202404-a2f29016b523?auto=format&fit=crop&w=1200&q=85',
    'https://images.unsplash.com/photo-1498623116890-37e912163d5d?auto=format&fit=crop&w=1200&q=85',
    'https://images.unsplash.com/photo-1504214208698-ea1916a2195a?auto=format&fit=crop&w=1200&q=85',
]);
const tripHighlights = computed(() => isPrivateTrip.value ? [
    { title: 'Sailing di perairan Komodo', time: 'Hari 1 · 09.00', location: 'Labuan Bajo', description: 'Mulai perjalanan privat dengan kapal pilihan dan nikmati rute laut yang disesuaikan untuk rombonganmu.', images: [detail.value.images[0], detail.value.images[2], detail.value.images[3]], icon: Compass },
    { title: 'Snorkeling di spot pilihan', time: 'Hari 2 · 08.00', location: 'Perairan Komodo', description: 'Jelajahi titik snorkeling dengan air jernih dan pendamping lokal yang memahami kondisi perairan.', images: [detail.value.images[1], detail.value.images[0], detail.value.images[3]], icon: Camera },
    { title: 'Sunset di atas kapal', time: 'Hari 2 · 17.30', location: 'Pulau Kalong', description: 'Tutup hari dengan panorama senja dan waktu santai bersama rombongan sebelum kembali berlayar.', images: [detail.value.images[2], detail.value.images[3], detail.value.images[0]], icon: Star },
    { title: 'Trekking pulau pilihan', time: 'Hari 3 · 06.00', location: 'Pulau Padar', description: 'Mulai pagi dengan trekking ringan menuju titik pandang favorit dan nikmati lanskap kepulauan dari ketinggian.', images: [detail.value.images[3], detail.value.images[0], detail.value.images[1]], icon: MapPin },
    { title: 'Makan siang di kapal', time: 'Hari 3 · 12.00', location: 'Perairan Komodo', description: 'Waktu istirahat dengan makan siang yang disiapkan di kapal sebelum aktivitas berikutnya.', images: [detail.value.images[0], detail.value.images[1], detail.value.images[2]], icon: Utensils },
    { title: 'Dokumentasi perjalanan', time: 'Hari 3 · 15.00', location: 'Labuan Bajo', description: 'Simpan momen terbaik rombongan dengan sesi dokumentasi santai di penghujung perjalanan.', images: [detail.value.images[2], detail.value.images[1], detail.value.images[3]], icon: Camera },
] : [
    { title: 'Snorkeling Pulau Semak Daun', time: 'Hari 1 · 09.30', location: 'Kepulauan Seribu', description: 'Nikmati perairan jernih dan terumbu karang di spot snorkeling yang ramah untuk pemula.', images: [detail.value.images[1], detail.value.images[0], detail.value.images[3]], icon: Compass },
    { title: 'Island hopping Pulau Pramuka', time: 'Hari 1 · 12.30', location: 'Pulau Pramuka', description: 'Jelajahi pulau dan sudut pantai pilihan bersama pemandu lokal yang mengenal rute perjalanan.', images: [detail.value.images[0], detail.value.images[2], detail.value.images[1]], icon: MapPin },
    { title: 'Sunset & waktu bebas', time: 'Hari 1 · 17.00', location: 'Tepi pantai', description: 'Nikmati sore santai untuk foto, menikmati suasana pantai, atau beristirahat sebelum makan malam.', images: [detail.value.images[2], detail.value.images[3], detail.value.images[0]], icon: Camera },
    { title: 'Makan siang tepi pantai', time: 'Hari 2 · 12.00', location: 'Pulau Pramuka', description: 'Istirahat dengan makan siang dan waktu santai sebelum kembali menuju titik kumpul.', images: [detail.value.images[3], detail.value.images[1], detail.value.images[0]], icon: Utensils },
    { title: 'Jelajah mangrove', time: 'Hari 2 · 14.00', location: 'Pulau Pramuka', description: 'Kenali sisi lain pulau melalui rute mangrove singkat bersama pemandu lokal.', images: [detail.value.images[0], detail.value.images[3], detail.value.images[2]], icon: MapPin },
    { title: 'Dokumentasi akhir trip', time: 'Hari 2 · 15.30', location: 'Dermaga Kaliadem', description: 'Sesi foto singkat untuk menutup perjalanan sebelum kembali ke Jakarta.', images: [detail.value.images[2], detail.value.images[0], detail.value.images[1]], icon: Camera },
]);
const destinations = computed(() => isPrivateTrip.value ? [
    { name: 'Pelabuhan Labuan Bajo', subtitle: 'Titik awal perjalanan', time: 'Hari 1 · 08.30', activity: 'Briefing dan naik kapal', note: 'Datang 30 menit sebelum keberangkatan.', coordinates: '-8.4964,119.8877', image: detail.value.images[0] },
    { name: 'Pulau Kelor', subtitle: 'Spot trekking ringan', time: 'Hari 1 · 10.00', activity: 'Trekking dan foto panorama', note: 'Gunakan alas kaki yang tidak licin.', coordinates: '-8.5403,119.5736', image: detail.value.images[3] },
    { name: 'Pink Beach', subtitle: 'Pantai dan snorkeling', time: 'Hari 2 · 09.00', activity: 'Snorkeling dan waktu bebas', note: 'Bawa perlengkapan ganti setelah aktivitas laut.', coordinates: '-8.6398,119.5583', image: detail.value.images[1] },
    { name: 'Pulau Kalong', subtitle: 'Titik sunset', time: 'Hari 2 · 17.30', activity: 'Menikmati sunset di kapal', note: 'Siapkan kamera sebelum matahari terbenam.', coordinates: '-8.4453,119.5725', image: detail.value.images[2] },
] : [
    { name: 'Dermaga Kaliadem', subtitle: 'Titik kumpul', time: 'Hari 1 · 06.30', activity: 'Registrasi dan keberangkatan', note: 'Tunjukkan e-ticket saat registrasi.', coordinates: '-6.1049,106.8819', image: detail.value.images[0] },
    { name: 'Pulau Pramuka', subtitle: 'Pulau utama', time: 'Hari 1 · 09.00', activity: 'Jelajah pulau dan makan siang', note: 'Simpan uang tunai untuk kebutuhan pribadi.', coordinates: '-5.7454,106.5834', image: detail.value.images[0] },
    { name: 'Pulau Semak Daun', subtitle: 'Spot snorkeling', time: 'Hari 1 · 11.30', activity: 'Snorkeling dan dokumentasi', note: 'Ikuti instruksi pemandu sebelum masuk air.', coordinates: '-5.7022,106.5756', image: detail.value.images[1] },
    { name: 'Spot sunset', subtitle: 'Tepi pantai', time: 'Hari 1 · 17.00', activity: 'Waktu bebas dan foto', note: 'Tetap bersama rombongan saat menjelang malam.', coordinates: '-5.7490,106.5804', image: detail.value.images[2] },
]);
const itineraryDays = computed(() => isPrivateTrip.value ? [
    { day: 'Hari ke 1', meals: 'Lunch · Dinner', activities: ['Kumpul di Pelabuhan Labuan Bajo pukul 08.00', 'Briefing keselamatan dan naik kapal', 'Sailing menuju Pulau Kelor', 'Trekking ringan dan waktu foto', 'Makan siang di kapal', 'Check-in kabin atau homestay', 'Waktu bebas dan menikmati sunset', 'Makan malam'] },
    { day: 'Hari ke 2', meals: 'Breakfast · Lunch · Dinner', activities: ['Sarapan di kapal', 'Snorkeling di spot pilihan perairan Komodo', 'Makan siang dan waktu istirahat', 'Eksplorasi pantai atau pulau sesuai rute', 'Kembali ke kapal', 'Waktu bebas', 'Makan malam'] },
    { day: 'Hari ke 3', meals: 'Breakfast', activities: ['Sarapan', 'Dokumentasi akhir perjalanan', 'Check-out kabin atau homestay', 'Sailing kembali menuju Labuan Bajo', 'Tiba di pelabuhan dan trip selesai'] },
] : [
    { day: 'Hari ke 1', meals: 'Lunch · Dinner', activities: ['Kumpul di Dermaga Kaliadem pukul 06.30', 'Registrasi peserta dan briefing singkat', 'Penyeberangan menuju Pulau Pramuka', 'Check-in homestay dan makan siang', 'Jelajah pulau dan aktivitas snorkeling', 'Waktu bebas untuk menikmati sunset', 'Makan malam'] },
    { day: 'Hari ke 2', meals: 'Breakfast', activities: ['Sarapan di homestay', 'Waktu bebas dan persiapan pulang', 'Check-out homestay', 'Penyeberangan kembali ke Dermaga Kaliadem', 'Tiba di Jakarta dan trip selesai'] },
]);
const highlightPageCount = computed(() => Math.ceil(tripHighlights.value.length / 3));
const visibleHighlights = computed(() => tripHighlights.value.slice(highlightPage.value * 3, (highlightPage.value + 1) * 3));
const tabs = ['Deskripsi', 'Highlight', 'Destinasi', 'Itinerary', 'Fasilitas', 'Galeri', 'Lokasi'];
const facilities = [
    { label: 'Homestay', icon: BedDouble },
    { label: 'Makan 3x', icon: Utensils },
    { label: 'Snorkeling', icon: Compass },
    { label: 'Dokumentasi', icon: Camera },
    { label: 'P3K', icon: ShieldCheck },
    { label: 'Guide lokal', icon: Users },
];
const includes = ['Transportasi pulang-pergi sesuai titik kumpul', 'Akomodasi dan makan sesuai program', 'Tiket aktivitas dan perlengkapan snorkeling', 'Dokumentasi perjalanan', 'Pemandu lokal berpengalaman'];
const excludes = ['Transportasi menuju meeting point', 'Pengeluaran pribadi', 'Makan di luar program', 'Tiket aktivitas opsional', 'Asuransi perjalanan pribadi'];

const updateTravelers = (amount) => {
    travelers.value = Math.max(1, Math.min(12, travelers.value + amount));
};

const checkAvailability = () => {
    bookingMessage.value = `${detail.value.label} untuk ${travelers.value} orang siap dicek. Pilih tanggal keberangkatan untuk melanjutkan.`;
};

const openItinerary = () => {
    activeTab.value = 'Itinerary';
};

const selectHighlight = (index) => {
    selectedHighlight.value = index;
    selectedHighlightPhoto.value = 0;
};

const selectHighlightPage = (page) => {
    highlightPage.value = page;
    selectHighlight(page * 3);
};

const showHighlightPhoto = (index) => {
    const imageCount = tripHighlights.value[selectedHighlight.value].images.length;
    selectedHighlightPhoto.value = (index + imageCount) % imageCount;
};

const selectDestination = (index) => {
    selectedDestination.value = index;
    routeSaved.value = false;
};

const openDestinationMap = () => {
    const destination = destinations.value[selectedDestination.value];
    window.open(`https://www.google.com/maps/search/?api=1&query=${encodeURIComponent(destination.coordinates)}`, '_blank', 'noopener,noreferrer');
};

const saveRoute = () => {
    routeSaved.value = !routeSaved.value;
};

const openGallery = (imageIndex = selectedImage.value) => {
    galleryImageIndex.value = imageIndex;
    isGalleryOpen.value = true;
};

const closeGallery = () => {
    isGalleryOpen.value = false;
};

const showGalleryImage = (imageIndex) => {
    galleryImageIndex.value = (imageIndex + galleryImages.value.length) % galleryImages.value.length;
};

const handleGalleryKeydown = (event) => {
    if (!isGalleryOpen.value) {
        return;
    }

    if (event.key === 'Escape') {
        closeGallery();
    }

    if (event.key === 'ArrowLeft') {
        showGalleryImage(galleryImageIndex.value - 1);
    }

    if (event.key === 'ArrowRight') {
        showGalleryImage(galleryImageIndex.value + 1);
    }
};

onMounted(() => window.addEventListener('keydown', handleGalleryKeydown));
onBeforeUnmount(() => window.removeEventListener('keydown', handleGalleryKeydown));
</script>

<template>
    <Head :title="detail.title" />

    <div class="min-h-screen bg-[#f6f8fb] text-[#17375f]">
        <MainNavigation />

        <main class="mx-auto max-w-[1220px] px-4 py-6 sm:px-6 lg:px-8 lg:py-8">
            <div class="grid gap-6 lg:grid-cols-[minmax(0,1fr)_330px] lg:items-start">
                <section class="min-w-0">
                    <h1 class="max-w-3xl text-3xl font-extrabold leading-[1.05] tracking-tight text-[#173b70] sm:text-4xl">{{ detail.title }}<span class="mt-0.5 block">{{ detail.startPoint }} <span class="whitespace-nowrap text-[#1677e8]">{{ detail.duration }}</span></span></h1>
                    <p class="mt-3 max-w-2xl text-sm leading-6 text-slate-600">{{ detail.subtitle }}</p>

                    <div class="mt-6 grid gap-3 sm:grid-cols-[minmax(0,1fr)_132px]">
                        <div class="relative h-[280px] overflow-hidden rounded-2xl bg-slate-200 sm:h-[390px]">
                            <img :src="detail.images[selectedImage]" :alt="detail.title" class="size-full object-cover" />
                            <button type="button" :disabled="selectedImage === 0" class="absolute left-4 top-1/2 grid size-9 -translate-y-1/2 place-items-center rounded-full bg-white text-[#1677e8] shadow-md disabled:opacity-40" aria-label="Foto sebelumnya" @click="selectedImage -= 1"><ChevronLeft class="size-5" /></button>
                            <button type="button" :disabled="selectedImage === detail.images.length - 1" class="absolute right-4 top-1/2 grid size-9 -translate-y-1/2 place-items-center rounded-full bg-white text-[#1677e8] shadow-md disabled:opacity-40" aria-label="Foto berikutnya" @click="selectedImage += 1"><ChevronRight class="size-5" /></button>
                        </div>
                        <div class="grid grid-cols-3 gap-2 sm:grid-cols-1"><button v-for="(image, index) in detail.images.slice(1)" :key="image" type="button" class="relative overflow-hidden rounded-xl outline-none focus-visible:ring-2 focus-visible:ring-[#1677e8]" :class="selectedImage === index + 1 ? 'ring-2 ring-[#1677e8]' : ''" :aria-label="index === 2 ? 'Buka semua foto' : `Tampilkan foto ${index + 2}`" @click="index === 2 ? openGallery(index + 1) : selectedImage = index + 1"><img :src="image" alt="" class="size-full min-h-20 object-cover" /><span v-if="index === 2" class="absolute inset-0 grid place-items-center bg-slate-950/55 text-sm font-bold text-white">+{{ galleryImages.length - detail.images.length }} foto</span></button></div>
                    </div>

                    <section class="mt-6 overflow-hidden rounded-2xl border border-[#e1eaf5] bg-white shadow-[0_8px_26px_rgba(23,75,120,0.04)]">
                        <div class="overflow-x-auto border-b border-[#e6eef7] [scrollbar-width:none] [&::-webkit-scrollbar]:hidden"><nav class="flex min-w-max gap-1 px-4 sm:px-6" aria-label="Detail perjalanan"><button v-for="tab in tabs" :key="tab" type="button" class="border-b-[3px] px-3 py-4 text-sm font-semibold transition-colors sm:px-4" :class="activeTab === tab ? 'border-[#1694ed] text-[#1688e8]' : 'border-transparent text-[#38517b] hover:text-[#1688e8]'" @click="activeTab = tab">{{ tab }}</button></nav></div>

                        <div class="p-5 sm:p-6">
                            <template v-if="activeTab === 'Deskripsi'">
                                <h2 class="text-xl font-extrabold tracking-tight text-[#096ab9]">Tentang Trip Ini</h2>
                                <p class="mt-3 max-w-3xl text-sm leading-6 text-[#3f6094]">{{ isPrivateTrip ? (isDescriptionExpanded ? 'Perjalanan privat ini dirancang untuk rombongan yang ingin menikmati Labuan Bajo dengan lebih leluasa. Rute, aktivitas, waktu kunjungan, hingga pilihan kapal dapat disesuaikan bersama partner lokal kami agar pengalaman tetap nyaman dan berkesan.' : 'Perjalanan ini dirancang untuk rombongan pribadi yang ingin menikmati Labuan Bajo dengan lebih leluasa. Rute dan aktivitas dapat disesuaikan bersama partner lokal.') : (isDescriptionExpanded ? 'Pulau Pramuka adalah salah satu destinasi favorit di Kepulauan Seribu yang terkenal dengan keindahan bawah lautnya. Dalam trip ini, kamu akan diajak menjelajahi pulau-pulau sekitar, snorkeling di spot terbaik, dan menikmati suasana tropis yang masih alami. Cocok untuk kamu yang ingin liburan singkat dengan pengalaman seru dan berkesan!' : 'Pulau Pramuka adalah salah satu destinasi favorit di Kepulauan Seribu yang terkenal dengan keindahan bawah lautnya. Dalam trip ini, kamu akan diajak menjelajahi pulau-pulau sekitar, snorkeling di spot terbaik, dan menikmati suasana tropis yang masih alami.') }}</p>
                                <button type="button" class="mt-3 inline-flex items-center gap-1 text-xs font-bold text-[#1688e8] hover:text-[#096ab9]" @click="isDescriptionExpanded = !isDescriptionExpanded">{{ isDescriptionExpanded ? 'Tampilkan lebih sedikit' : 'Baca Selengkapnya' }}<ChevronRight class="size-4 transition-transform" :class="isDescriptionExpanded ? '-rotate-90' : 'rotate-90'" /></button>
                            </template>
                            <template v-else-if="activeTab === 'Destinasi'">
                                <div class="flex flex-wrap items-end justify-between gap-3"><div><p class="text-[11px] font-bold uppercase tracking-[0.16em] text-[#1688e8]">Panduan perjalanan</p><h2 class="mt-1 text-xl font-extrabold tracking-tight text-[#173b70]">Rute dan titik kunjungan</h2><p class="mt-1 text-xs text-[#60789c]">Pilih titik untuk melihat waktu, aktivitas, dan panduan praktisnya.</p></div><button type="button" class="inline-flex items-center gap-1.5 rounded-lg border px-3 py-2 text-xs font-bold transition" :class="routeSaved ? 'border-emerald-200 bg-emerald-50 text-emerald-700' : 'border-[#cfe2f6] bg-white text-[#1688e8] hover:bg-[#f4f9ff]'" @click="saveRoute"><CheckCircle2 class="size-4" />{{ routeSaved ? 'Rute tersimpan' : 'Simpan rute' }}</button></div>
                                <div class="mt-5 grid gap-4 lg:grid-cols-[minmax(0,0.85fr)_minmax(0,1.15fr)]"><div class="rounded-2xl border border-[#e1ebf6] bg-white p-2"><button v-for="(destination, index) in destinations" :key="destination.name" type="button" class="flex w-full items-center gap-3 rounded-xl p-3 text-left transition" :class="selectedDestination === index ? 'bg-[#edf7ff] text-[#173b70]' : 'text-[#496581] hover:bg-[#f8fbff]'" :aria-pressed="selectedDestination === index" @click="selectDestination(index)"><span class="grid size-8 shrink-0 place-items-center rounded-full text-[10px] font-extrabold" :class="selectedDestination === index ? 'bg-[#1688e8] text-white' : 'bg-[#edf6ff] text-[#1688e8]'">0{{ index + 1 }}</span><span class="min-w-0 flex-1"><span class="block truncate text-xs font-bold">{{ destination.name }}</span><span class="mt-0.5 block text-[10px] text-[#7186a2]">{{ destination.time }}</span></span><ChevronRight class="size-4 shrink-0" :class="selectedDestination === index ? 'text-[#1688e8]' : 'text-[#a6b7c9]'" /></button></div><article class="overflow-hidden rounded-2xl border border-[#dceaf7] bg-[#f8fbff]"><div class="relative h-44"><img :src="destinations[selectedDestination].image" :alt="destinations[selectedDestination].name" class="size-full object-cover" /><span class="absolute left-3 top-3 rounded-full bg-[#082d55]/75 px-2.5 py-1 text-[10px] font-bold text-white">Titik {{ selectedDestination + 1 }} dari {{ destinations.length }}</span></div><div class="p-5"><div class="flex items-start justify-between gap-3"><div><h3 class="text-lg font-extrabold text-[#173b70]">{{ destinations[selectedDestination].name }}</h3><p class="mt-1 text-xs text-[#7186a2]">{{ destinations[selectedDestination].subtitle }}</p></div><span class="rounded-lg bg-[#eaf4ff] px-2.5 py-1.5 text-[10px] font-bold text-[#1688e8]">{{ destinations[selectedDestination].time }}</span></div><dl class="mt-4 grid gap-3 sm:grid-cols-2"><div class="rounded-xl bg-white p-3"><dt class="text-[10px] font-semibold text-[#7186a2]">Aktivitas</dt><dd class="mt-1 text-xs font-bold text-[#31577f]">{{ destinations[selectedDestination].activity }}</dd></div><div class="rounded-xl bg-white p-3"><dt class="text-[10px] font-semibold text-[#7186a2]">Catatan traveler</dt><dd class="mt-1 text-xs font-bold leading-5 text-[#31577f]">{{ destinations[selectedDestination].note }}</dd></div></dl><button type="button" class="mt-4 inline-flex items-center gap-2 text-xs font-bold text-[#1688e8] transition hover:text-[#096ab9]" @click="openDestinationMap"><MapPin class="size-4" />Buka titik ini di Google Maps<ChevronRight class="size-4" /></button></div></article></div>
                                <p v-if="routeSaved" role="status" class="mt-4 rounded-xl bg-emerald-50 px-3 py-2 text-xs text-emerald-700">Rute kunjungan telah disimpan untuk trip ini.</p>
                            </template>
                            <template v-else-if="activeTab === 'Highlight'">
                                <div class="flex flex-wrap items-end justify-between gap-3"><div><p class="text-[11px] font-bold uppercase tracking-[0.16em] text-[#1688e8]">Yang akan kamu alami</p><h2 class="mt-1 text-xl font-extrabold tracking-tight text-[#173b70]">Highlight perjalanan</h2><p class="mt-1 text-xs text-[#60789c]">Pilih aktivitas untuk melihat detail rute, waktu, dan dokumentasinya.</p></div><span class="rounded-full bg-[#eaf4ff] px-3 py-1 text-[10px] font-bold text-[#1688e8]">{{ tripHighlights.length }} aktivitas utama</span></div>
                                <div class="mt-5 grid gap-3 sm:grid-cols-3"><button v-for="(item, visibleIndex) in visibleHighlights" :key="item.title" type="button" class="flex h-24 items-center gap-3 rounded-xl border p-3 text-left transition" :class="selectedHighlight === highlightPage * 3 + visibleIndex ? 'border-[#1688e8] bg-[#eef7ff] shadow-[0_5px_14px_rgba(22,136,232,0.12)]' : 'border-[#e1ebf6] bg-white hover:border-[#b9daf7] hover:bg-[#f8fbff]'" :aria-pressed="selectedHighlight === highlightPage * 3 + visibleIndex" @click="selectHighlight(highlightPage * 3 + visibleIndex)"><span class="grid size-9 shrink-0 place-items-center rounded-lg" :class="selectedHighlight === highlightPage * 3 + visibleIndex ? 'bg-[#1688e8] text-white' : 'bg-[#edf6ff] text-[#1688e8]'"><component :is="item.icon" class="size-4" /></span><span class="min-w-0"><span class="block line-clamp-2 text-xs font-bold leading-4 text-[#173b70]">{{ item.title }}</span><span class="mt-1 block text-[10px] text-[#7186a2]">{{ item.time }}</span></span></button></div>
                                <div v-if="highlightPageCount > 1" class="mt-4 flex items-center justify-center gap-2"><button type="button" class="grid size-8 place-items-center rounded-full border border-[#dbe8f5] text-[#1688e8] disabled:cursor-not-allowed disabled:opacity-35" aria-label="Halaman highlight sebelumnya" :disabled="highlightPage === 0" @click="selectHighlightPage(highlightPage - 1)"><ChevronLeft class="size-4" /></button><button v-for="page in highlightPageCount" :key="page" type="button" class="h-2 rounded-full transition-all" :class="highlightPage === page - 1 ? 'w-6 bg-[#1688e8]' : 'w-2 bg-[#c9deef] hover:bg-[#8dc3ed]'" :aria-label="`Halaman highlight ${page}`" :aria-current="highlightPage === page - 1 ? 'page' : undefined" @click="selectHighlightPage(page - 1)"></button><button type="button" class="grid size-8 place-items-center rounded-full border border-[#dbe8f5] text-[#1688e8] disabled:cursor-not-allowed disabled:opacity-35" aria-label="Halaman highlight berikutnya" :disabled="highlightPage === highlightPageCount - 1" @click="selectHighlightPage(highlightPage + 1)"><ChevronRight class="size-4" /></button></div>
                                <article class="mt-5 overflow-hidden rounded-2xl border border-[#dceaf7] bg-[#f8fbff] sm:grid sm:min-h-[320px] sm:grid-cols-[minmax(0,0.88fr)_minmax(0,1.12fr)]"><div class="relative h-52 overflow-hidden sm:h-[320px]"><img :src="tripHighlights[selectedHighlight].images[selectedHighlightPhoto]" :alt="tripHighlights[selectedHighlight].title" class="size-full object-cover" /><button type="button" class="absolute left-3 top-1/2 grid size-8 -translate-y-1/2 place-items-center rounded-full bg-white/90 text-[#1688e8] shadow-md" aria-label="Foto highlight sebelumnya" @click="showHighlightPhoto(selectedHighlightPhoto - 1)"><ChevronLeft class="size-4" /></button><button type="button" class="absolute right-3 top-1/2 grid size-8 -translate-y-1/2 place-items-center rounded-full bg-white/90 text-[#1688e8] shadow-md" aria-label="Foto highlight berikutnya" @click="showHighlightPhoto(selectedHighlightPhoto + 1)"><ChevronRight class="size-4" /></button><span class="absolute bottom-3 right-3 rounded-full bg-[#082d55]/70 px-2.5 py-1 text-[10px] font-bold text-white">{{ selectedHighlightPhoto + 1 }} / {{ tripHighlights[selectedHighlight].images.length }}</span></div><div class="flex min-w-0 flex-col p-5"><div class="flex items-start justify-between gap-3"><span class="grid size-10 place-items-center rounded-xl bg-[#eaf4ff] text-[#1688e8]"><component :is="tripHighlights[selectedHighlight].icon" class="size-5" /></span><span class="rounded-full bg-white px-2.5 py-1 text-[10px] font-bold text-[#1688e8] shadow-sm">{{ tripHighlights[selectedHighlight].time }}</span></div><h3 class="mt-4 text-lg font-extrabold text-[#173b70]">{{ tripHighlights[selectedHighlight].title }}</h3><p class="mt-2 text-xs leading-5 text-[#60789c]">{{ tripHighlights[selectedHighlight].description }}</p><div class="mt-auto flex flex-wrap items-center justify-between gap-3 pt-5"><span class="inline-flex items-center gap-1.5 text-[11px] font-semibold text-[#31577f]"><MapPin class="size-4 text-[#1688e8]" />{{ tripHighlights[selectedHighlight].location }}</span><button type="button" class="inline-flex items-center gap-1 text-xs font-bold text-[#1688e8] transition hover:text-[#096ab9]" @click="openItinerary">Lihat di itinerary<ChevronRight class="size-4" /></button></div></div></article>
                            </template>
                            <template v-else-if="activeTab === 'Itinerary'">
                                <div class="flex items-end justify-between gap-4"><div><p class="text-[11px] font-bold uppercase tracking-[0.16em] text-[#1688e8]">{{ detail.duration }}</p><h2 class="mt-1 text-xl font-extrabold tracking-tight text-[#173b70]">Rencana perjalanan</h2><p class="mt-1 text-xs text-[#60789c]">Agenda dapat menyesuaikan kondisi cuaca dan operasional lokal.</p></div><CalendarDays class="size-6 text-[#1688e8]" /></div><ol class="relative mt-6 space-y-5 before:absolute before:bottom-6 before:left-[17px] before:top-6 before:w-px before:bg-[#cfe4fa]"><li v-for="(itinerary, index) in itineraryDays" :key="itinerary.day" class="relative flex gap-4"><span class="z-10 grid size-9 shrink-0 place-items-center rounded-full border-4 border-white bg-[#1688e8] text-[10px] font-extrabold text-white shadow-[0_2px_7px_rgba(22,136,232,0.25)]">0{{ index + 1 }}</span><article class="min-w-0 flex-1 rounded-2xl border border-[#e1ebf6] bg-[#f9fcff] p-4"><div class="flex flex-wrap items-center justify-between gap-2"><h3 class="text-sm font-extrabold text-[#173b70]">{{ itinerary.day }}</h3><span class="rounded-full bg-[#eaf4ff] px-2.5 py-1 text-[10px] font-bold text-[#1688e8]">{{ itinerary.meals }}</span></div><ul class="mt-4 space-y-2"><li v-for="activity in itinerary.activities" :key="activity" class="flex gap-2 text-xs leading-5 text-[#496581]"><CheckCircle2 class="mt-0.5 size-3.5 shrink-0 text-[#1688e8]" />{{ activity }}</li></ul></article></li></ol>
                            </template>
                            <template v-else-if="activeTab === 'Fasilitas'">
                                <div class="flex items-end justify-between gap-4"><div><p class="text-[11px] font-bold uppercase tracking-[0.16em] text-[#1688e8]">Sudah termasuk</p><h2 class="mt-1 text-xl font-extrabold tracking-tight text-[#173b70]">Fasilitas perjalanan</h2></div><span class="text-xs font-semibold text-[#60789c]">{{ facilities.length }} fasilitas</span></div><div class="mt-5 grid grid-cols-2 gap-3 sm:grid-cols-3"><article v-for="facility in facilities" :key="facility.label" class="rounded-2xl border border-[#e1ebf6] bg-white p-4 transition hover:-translate-y-0.5 hover:shadow-[0_8px_20px_rgba(22,93,155,0.10)]"><span class="grid size-10 place-items-center rounded-xl bg-[#edf6ff] text-[#1688e8]"><component :is="facility.icon" class="size-5" /></span><h3 class="mt-3 text-xs font-bold text-[#173b70]">{{ facility.label }}</h3><p class="mt-1 text-[10px] leading-4 text-[#7186a2]">Tersedia selama perjalanan</p></article></div>
                            </template>
                            <template v-else-if="activeTab === 'Galeri'">
                                <div class="flex items-end justify-between gap-4"><div><p class="text-[11px] font-bold uppercase tracking-[0.16em] text-[#1688e8]">Momen perjalanan</p><h2 class="mt-1 text-xl font-extrabold tracking-tight text-[#173b70]">Galeri {{ detail.label }}</h2></div><button type="button" class="text-xs font-bold text-[#1688e8]" @click="openGallery()">Buka semua foto</button></div><div class="mt-5 grid grid-cols-2 gap-3 sm:grid-cols-3"><button v-for="(image, index) in galleryImages.slice(0, 6)" :key="`${image}-${index}`" type="button" class="group relative aspect-[4/3] overflow-hidden rounded-xl bg-slate-100 text-left" @click="openGallery(index)"><img :src="image" :alt="`Foto perjalanan ${index + 1}`" class="size-full object-cover transition duration-300 group-hover:scale-105" /><span v-if="index === 5" class="absolute inset-0 grid place-items-center bg-[#092f59]/65 text-sm font-bold text-white">+{{ galleryImages.length - 6 }} foto</span></button></div>
                            </template>
                            <template v-else-if="activeTab === 'Lokasi'">
                                <div class="flex items-end justify-between gap-4"><div><p class="text-[11px] font-bold uppercase tracking-[0.16em] text-[#1688e8]">Titik keberangkatan</p><h2 class="mt-1 text-xl font-extrabold tracking-tight text-[#173b70]">Lokasi perjalanan</h2></div><MapPin class="size-6 text-[#1688e8]" /></div><div class="relative mt-5 h-72 overflow-hidden rounded-2xl border border-[#dce9f5] bg-slate-100"><iframe :src="mapEmbedUrl" :title="`Peta lokasi ${detail.title}`" class="size-full border-0" loading="lazy" allowfullscreen referrerpolicy="no-referrer-when-downgrade"></iframe><div class="absolute bottom-3 left-3 rounded-xl bg-white px-3 py-2 shadow-[0_3px_14px_rgba(15,47,82,0.18)]"><p class="text-[10px] font-bold text-[#173b70]">{{ detail.startPoint }}</p><p class="mt-0.5 text-[9px] text-[#7186a2]">{{ detail.location }}</p></div></div>
                            </template>

                            <template v-if="activeTab === 'Deskripsi'">
                                <div class="mt-8 flex items-end justify-between gap-4"><div><h2 class="flex items-center gap-2 text-xl font-extrabold tracking-tight text-[#173b70]"><MapPin class="size-5 text-[#1688e8]" :stroke-width="2.5" />Destinasi</h2><p class="mt-1 pl-7 text-xs text-[#60789c]">Preview titik yang akan kamu kunjungi</p></div><button type="button" class="mb-1 hidden items-center gap-1 text-xs font-bold text-[#1688e8] transition-colors hover:text-[#096ab9] sm:inline-flex" @click="activeTab = 'Destinasi'">Lihat rute lengkap<ChevronRight class="size-4" /></button></div>
                                <div class="mt-4 flex gap-4 overflow-x-auto pb-3 [scrollbar-width:thin]"><article v-for="(image, index) in galleryImages.slice(0, 4)" :key="`${image}-${index}`" class="w-56 shrink-0 overflow-hidden rounded-xl border border-[#e4edf7] bg-white shadow-[0_4px_12px_rgba(30,69,110,0.10)]"><img :src="image" :alt="`Destinasi ${index + 1}`" class="h-32 w-full object-cover" /><div class="p-3"><p class="flex items-center gap-2 text-xs font-bold text-[#173b70]"><span class="grid size-5 shrink-0 place-items-center rounded-md bg-[#edf6ff] text-[#1688e8]"><MapPin class="size-3.5" :stroke-width="2.5" /></span>{{ index === 0 ? 'Pulau Pramuka' : index === 1 ? 'Pulau Semak Daun' : index === 2 ? 'Spot Snorkeling' : 'Pulau Air' }}</p><p class="mt-1 pl-7 text-[10px] text-[#7186a2]">{{ detail.location }}</p></div></article></div>
                                <button type="button" class="mt-3 inline-flex items-center gap-2 text-xs font-bold text-[#1688e8] sm:hidden" @click="activeTab = 'Destinasi'">Lihat rute lengkap<ChevronRight class="size-4" /></button>
                            </template>
                        </div>
                    </section>
                </section>

                <aside class="lg:sticky lg:top-[118px]"><section class="rounded-2xl border border-[#dce7f4] bg-white p-5 shadow-[0_8px_28px_rgba(23,75,120,0.08)]"><p class="text-xs text-slate-400 line-through">{{ detail.originalPrice }}</p><div class="mt-1 flex items-end gap-2"><p class="text-2xl font-extrabold tracking-tight text-[#173b70]">{{ detail.price }}</p><span class="pb-1 text-[10px] text-slate-500">/ orang</span></div><p class="mt-1 text-[11px] text-slate-500">Harga dapat berubah sesuai jadwal dan jumlah peserta.</p><div class="mt-4 rounded-xl bg-[#f5f9ff] p-3"><p class="text-[10px] font-semibold text-[#627a99]">Keberangkatan</p><p class="mt-1 flex items-center gap-2 text-xs font-bold text-[#31577f]"><CalendarDays class="size-4 text-[#1677e8]" />{{ detail.date }}</p></div><div class="mt-3 flex items-center justify-between rounded-xl border border-[#e1eaf5] px-3 py-2.5"><span class="flex items-center gap-2 text-xs font-semibold text-[#31577f]"><Users class="size-4 text-[#1677e8]" />Jumlah peserta</span><div class="flex items-center gap-2"><button type="button" class="grid size-7 place-items-center rounded-lg bg-[#f1f6fc] text-[#1677e8]" aria-label="Kurangi peserta" @click="updateTravelers(-1)"><Minus class="size-3.5" /></button><span class="w-4 text-center text-xs font-bold">{{ travelers }}</span><button type="button" class="grid size-7 place-items-center rounded-lg bg-[#eaf4ff] text-[#1677e8]" aria-label="Tambah peserta" @click="updateTravelers(1)"><Plus class="size-3.5" /></button></div></div><button type="button" class="mt-4 flex min-h-11 w-full items-center justify-center gap-2 rounded-xl bg-[#1677e8] px-4 text-xs font-bold text-white shadow-[0_5px_12px_rgba(22,119,232,0.25)] transition hover:bg-[#0d68d1]" @click="checkAvailability"><CalendarDays class="size-4" />Cek ketersediaan</button><p v-if="bookingMessage" role="status" class="mt-3 rounded-lg bg-emerald-50 px-3 py-2 text-[10px] leading-4 text-emerald-700">{{ bookingMessage }}</p><p class="mt-3 flex items-center gap-1.5 text-[10px] text-[#1677e8]"><Star class="size-3.5 fill-current" />Dapatkan hingga 400 poin TapakLokal</p></section>

                    <section class="mt-4 grid gap-4 rounded-2xl border border-[#e1eaf5] bg-white p-4 sm:grid-cols-2 lg:grid-cols-1 xl:grid-cols-2"><div><h2 class="flex items-center gap-2 text-xs font-bold text-[#173b70]"><CheckCircle2 class="size-4 text-emerald-500" />Termasuk</h2><ul class="mt-3 space-y-2"><li v-for="item in includes" :key="item" class="flex gap-2 text-[10px] leading-4 text-slate-600"><CheckCircle2 class="mt-0.5 size-3 shrink-0 text-emerald-500" />{{ item }}</li></ul></div><div><h2 class="flex items-center gap-2 text-xs font-bold text-[#173b70]"><XCircle class="size-4 text-rose-500" />Tidak termasuk</h2><ul class="mt-3 space-y-2"><li v-for="item in excludes" :key="item" class="flex gap-2 text-[10px] leading-4 text-slate-600"><XCircle class="mt-0.5 size-3 shrink-0 text-rose-500" />{{ item }}</li></ul></div></section>

                    <section class="mt-4 rounded-2xl border border-[#e1eaf5] bg-white p-4"><div class="flex items-center justify-between"><h2 class="flex items-center gap-2 text-xs font-bold text-[#173b70]"><Compass class="size-4 text-[#1677e8]" />Fasilitas</h2><span class="text-[10px] font-semibold text-[#1677e8]">Lihat semua</span></div><div class="mt-4 grid grid-cols-3 gap-2"><div v-for="facility in facilities" :key="facility.label" class="grid min-h-20 place-items-center rounded-xl bg-[#f7faff] p-2 text-center"><component :is="facility.icon" class="size-5 text-[#1677e8]" /><span class="mt-2 text-[9px] font-medium text-[#56708d]">{{ facility.label }}</span></div></div></section>

                    <section class="mt-4 overflow-hidden rounded-2xl border border-[#e1eaf5] bg-white p-4"><h2 class="flex items-center gap-2 text-xs font-bold text-[#173b70]"><MapPin class="size-4 text-[#1677e8]" />Lokasi</h2><div class="relative mt-3 h-64 overflow-hidden rounded-xl border border-slate-200 bg-slate-100"><iframe :src="mapEmbedUrl" :title="`Peta lokasi ${detail.title}`" class="size-full border-0" loading="lazy" allowfullscreen referrerpolicy="no-referrer-when-downgrade"></iframe><form class="absolute left-3 right-12 top-3" @submit.prevent><label class="flex h-10 items-center gap-2 rounded-lg bg-white px-3 text-slate-500 shadow-[0_2px_10px_rgba(15,47,82,0.2)]"><Search class="size-4 shrink-0" /><input v-model="mapSearch" type="search" class="min-w-0 flex-1 bg-transparent text-xs text-slate-700 outline-none placeholder:text-slate-400" placeholder="Cari lokasi atau alamat" aria-label="Cari lokasi pada peta" /></label></form><div class="absolute bottom-3 right-3 overflow-hidden rounded-lg bg-white shadow-[0_2px_10px_rgba(15,47,82,0.22)]"><button type="button" class="grid size-9 place-items-center border-b border-slate-200 text-[#173b70] transition hover:bg-slate-50 disabled:text-slate-300" aria-label="Perkecil peta" :disabled="mapZoom <= 3" @click="mapZoom -= 1"><Minus class="size-4" :stroke-width="3" /></button><button type="button" class="grid size-9 place-items-center text-[#173b70] transition hover:bg-slate-50 disabled:text-slate-300" aria-label="Perbesar peta" :disabled="mapZoom >= 18" @click="mapZoom += 1"><Plus class="size-4" :stroke-width="3" /></button></div></div></section>
                </aside>
            </div>
        </main>

        <Teleport to="body">
            <div v-if="isGalleryOpen" class="fixed inset-0 z-[100] flex items-center justify-center bg-[#071a32]/92 p-4 sm:p-8" role="dialog" aria-modal="true" aria-label="Galeri foto perjalanan" @click.self="closeGallery">
                <div class="flex h-full w-full max-w-6xl flex-col">
                    <div class="flex items-center justify-between gap-4 pb-4 text-white">
                        <div><p class="text-sm font-bold">Galeri {{ detail.title }}</p><p class="mt-0.5 text-xs text-white/65">{{ galleryImageIndex + 1 }} dari {{ galleryImages.length }} foto</p></div>
                        <button type="button" class="grid size-10 place-items-center rounded-full bg-white/12 text-white transition hover:bg-white/20 focus-visible:ring-2 focus-visible:ring-white" aria-label="Tutup galeri" @click="closeGallery"><X class="size-5" /></button>
                    </div>
                    <div class="relative min-h-0 flex-1 overflow-hidden rounded-2xl bg-black shadow-2xl"><img :src="galleryImages[galleryImageIndex]" :alt="`${detail.title} foto ${galleryImageIndex + 1}`" class="size-full object-contain" /><button type="button" class="absolute left-3 top-1/2 grid size-11 -translate-y-1/2 place-items-center rounded-full bg-white/90 text-[#1677e8] shadow-lg transition hover:bg-white sm:left-5" aria-label="Foto sebelumnya" @click="showGalleryImage(galleryImageIndex - 1)"><ChevronLeft class="size-6" /></button><button type="button" class="absolute right-3 top-1/2 grid size-11 -translate-y-1/2 place-items-center rounded-full bg-white/90 text-[#1677e8] shadow-lg transition hover:bg-white sm:right-5" aria-label="Foto berikutnya" @click="showGalleryImage(galleryImageIndex + 1)"><ChevronRight class="size-6" /></button></div>
                    <div class="mt-4 flex gap-2 overflow-x-auto pb-1 [scrollbar-width:thin]"><button v-for="(image, index) in galleryImages" :key="`${image}-${index}`" type="button" class="h-16 w-20 shrink-0 overflow-hidden rounded-lg border-2 transition sm:h-18 sm:w-24" :class="galleryImageIndex === index ? 'border-[#52a6ff] opacity-100' : 'border-transparent opacity-60 hover:opacity-100'" :aria-label="`Tampilkan foto ${index + 1}`" @click="showGalleryImage(index)"><img :src="image" alt="" class="size-full object-cover" /></button></div>
                </div>
            </div>
        </Teleport>
    </div>
</template>
