<script setup>
import { tripPackageFacilities } from '../Composables/tripPackageDetails';
import { Head, router, usePage } from '@inertiajs/vue3';
import { route } from 'ziggy-js';
import { computed, onBeforeUnmount, onMounted, ref, watch } from 'vue';
import { BedDouble, CalendarDays, Camera, CheckCircle2, ChevronLeft, ChevronRight, Clock3, MessageCircle, Compass, Copy, MapPin, Minus, Navigation, Plus, ShieldCheck, Star, Users, Utensils, X, XCircle } from 'lucide-vue-next';
import TripRichContent from '../Components/TripDetail/TripRichContent.vue';
import MainNavigation from '../Components/Shared/MainNavigation.vue';

import TripFaq from '../Components/TripDetail/TripFaq.vue';
import TripPanorama from '../Components/TripDetail/TripPanorama.vue';
import TripReviews from '../Components/TripDetail/TripReviews.vue';
import TripRecommendations from '../Components/TripDetail/TripRecommendations.vue';

const props = defineProps({
    tripType: { type: String, required: true },
    trip: { type: String, required: true },
    tripData: Object,
    bookingKey: String,
    canBook: Boolean,
    reviews: Array,
    virtualTours: { type: Array, default: () => [] },
    relatedTrips: { type: Array, default: () => [] },
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
const selectedFacilityCategory = ref('Semua');
const preparedPackingItems = ref([]);
const isCoordinatesCopied = ref(false);
const page = usePage();
const travelers = ref(1);
const bookingMessage = ref('');
const departureDate = computed(() => props.tripData?.departure_date?.slice(0, 10) || '');
const seatsAvailable = computed(() => Math.max(0, (props.tripData?.capacity || 0) - (props.tripData?.reserved_seats || 0)));
const canBook = computed(() => props.canBook && seatsAvailable.value > 0);
watch(() => [page.props.auth?.user?.id, page.url], () => {
    if (page.props.auth?.user && page.url.includes('book=1')) { checkAvailability(); }
});

const experience = computed(() => props.tripData?.experience || {});
const iconMap = { BedDouble, Camera, Compass, MapPin, ShieldCheck, Star, Users, Utensils, CalendarDays };
const safeImage = value => typeof value === 'string' && (/^https?:\/\//i.test(value) || /^\/(?!\/)/.test(value));
const departureDateLabel = computed(() => departureDate.value ? new Intl.DateTimeFormat('id-ID', { day: 'numeric', month: 'long', year: 'numeric' }).format(new Date(departureDate.value + 'T00:00:00')) : 'Jadwal belum tersedia');
const galleryImages = computed(() => [...new Set([props.tripData?.image_url, ...(experience.value.detail?.images || [])].filter(safeImage))]);
const detail = computed(() => {
    const trip = props.tripData || {};
    const days = Math.max(1, Math.round((new Date(trip.end_date || trip.departure_date) - new Date(trip.departure_date)) / 86400000) + 1);
    return { title: trip.title, startPoint: trip.meeting_point, duration: days + 'H' + (days - 1) + 'M', subtitle: experience.value.detail?.subtitle || '', location: trip.destination, coordinates: experience.value.detail?.coordinates || trip.meeting_point + ', ' + trip.destination, price: new Intl.NumberFormat('id-ID').format(trip.selling_price || 0), originalPrice: null, date: departureDateLabel.value, images: galleryImages.value.slice(0, 4) };
});
const mapEmbedUrl = computed(() => 'https://www.google.com/maps?q=' + encodeURIComponent(detail.value.coordinates) + '&z=12&output=embed');
const mapDirectionsUrl = computed(() => 'https://www.google.com/maps/dir/?api=1&destination=' + encodeURIComponent(detail.value.coordinates));
const tripHighlights = computed(() => (experience.value.highlights || []).map(item => ({ ...item, icon: iconMap[item.icon] || Compass, images: [...new Set([...(item.images || []), item.image_url].filter(safeImage))] })));
const destinations = computed(() => (experience.value.destinations || []).map(item => ({ ...item, image: item.image_url || item.image, coordinates: item.coordinates || item.name + ', ' + detail.value.location })));
const itineraryDays = computed(() => experience.value.itineraryDays || []);
const vendorInfo = computed(() => ({ name: props.tripData?.vendor?.name || 'Mitra trip' }));
const facilityDetails = computed(() => tripPackageFacilities(props.tripData || {}));
const facilityCategories = computed(() => ['Semua', ...new Set(facilityDetails.value.map(item => item.category))]);
const filteredFacilityDetails = computed(() => selectedFacilityCategory.value === 'Semua' ? facilityDetails.value : facilityDetails.value.filter(item => item.category === selectedFacilityCategory.value));
const packingItems = computed(() => experience.value.packingItems || []);
const highlightPageCount = computed(() => Math.ceil(tripHighlights.value.length / 3));
const visibleHighlights = computed(() => tripHighlights.value.slice(highlightPage.value * 3, (highlightPage.value + 1) * 3));
const tabs = ['Deskripsi', 'Highlight', 'Destinasi', 'Itinerary', 'Fasilitas', 'Lokasi'];
const facilities = computed(() => (experience.value.facilities || []).map(item => ({ ...item, icon: iconMap[item.icon] || CheckCircle2 })));
const includes = computed(() => experience.value.included || experience.value.includes || []);
const excludes = computed(() => experience.value.excluded || experience.value.excludes || []);
const panoramaTours = computed(() => [...props.virtualTours, ...(experience.value.panoramas || [])].filter(item => safeImage(item.image_url)));

const updateTravelers = (amount) => {
    travelers.value = Math.max(1, Math.min(50, seatsAvailable.value, travelers.value + amount));
};

const checkAvailability = () => {
    bookingMessage.value = '';
    if (!page.props.auth?.user) {
        router.visit(route('login', { trip: props.tripData.id }));
        return;
    }
    if (!canBook.value) {
        bookingMessage.value = 'Jadwal ini sudah berakhir atau kuotanya habis. Pilih jadwal lain di Cari Trip.';
        return;
    }
    router.visit(route('checkout.review.trip', { trip: props.tripData.id, participants: travelers.value }));
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

const togglePackingItem = (item) => {
    preparedPackingItems.value = preparedPackingItems.value.includes(item)
        ? preparedPackingItems.value.filter((preparedItem) => preparedItem !== item)
        : [...preparedPackingItems.value, item];
};

const copyDestinationCoordinates = async () => {
    try {
        await navigator.clipboard.writeText(detail.value.coordinates);
        isCoordinatesCopied.value = true;
        window.setTimeout(() => {
            isCoordinatesCopied.value = false;
        }, 1800);
    } catch {
        isCoordinatesCopied.value = false;
    }
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

onMounted(() => {
    window.addEventListener('keydown', handleGalleryKeydown);
    if (page.props.auth?.user && new URLSearchParams(window.location.search).get('book') === '1') {
        checkAvailability();
        const url = new URL(window.location.href); url.searchParams.delete('book'); window.history.replaceState({}, '', url.pathname + url.search);
    }
});
onBeforeUnmount(() => window.removeEventListener('keydown', handleGalleryKeydown));
</script>

<template>
    
    <Head :title="detail.title" />

    <div class="min-h-screen overflow-x-hidden bg-[#f6f8fb] text-[#17375f]">
        <MainNavigation />

        <main class="trip-detail mx-auto max-w-[1220px] px-3 pt-4 pb-28 sm:px-6 sm:pt-6 sm:pb-28 lg:px-8 lg:py-8">
            <div class="grid gap-6 lg:grid-cols-[minmax(0,1fr)_330px] lg:items-start">
                <section class="min-w-0">
                    <div class="mb-5 sm:mb-6">
                        <h1 class="max-w-3xl break-words text-2xl font-extrabold leading-[1.08] tracking-tight text-[#173b70] sm:text-4xl">
                            {{ detail.title }}
                            <span class="mt-0.5 block text-[#31577f]">{{ detail.startPoint }} <span class="whitespace-nowrap text-[#1677e8]">{{ detail.duration }}</span></span>
                        </h1>
                        <p v-if="detail.subtitle" class="mt-3 whitespace-pre-line max-w-2xl text-xs leading-5 text-[#60789c] sm:text-sm">{{ detail.subtitle }}</p>
                    </div>
                    <section class="relative isolate overflow-hidden rounded-[28px] bg-[#0d73d4] p-3 shadow-[0_18px_42px_rgba(18,95,178,0.18)] sm:p-4" aria-label="Galeri perjalanan" v-if="galleryImages.length">
                        <span class="pointer-events-none absolute -left-24 top-10 size-64 rounded-full border-[38px] border-white/10"></span><span class="pointer-events-none absolute -right-20 -top-24 size-72 rounded-full border-[50px] border-white/10"></span>
                        <div class="relative z-10 grid h-[280px] grid-cols-4 grid-rows-2 gap-2 sm:h-[390px] sm:gap-3">
                            <div class="relative col-span-4 row-span-2 overflow-hidden rounded-2xl bg-slate-200 sm:col-span-2"><img :src="detail.images[selectedImage]" :alt="detail.title" class="size-full object-cover transition duration-500" /><span class="absolute inset-x-0 bottom-0 h-1/2 bg-gradient-to-t from-[#062e5e]/65 to-transparent"></span><div class="absolute bottom-3 left-3 flex items-center gap-2 rounded-lg bg-slate-950/45 px-2.5 py-1.5 text-[10px] font-bold text-white backdrop-blur sm:bottom-4 sm:left-4"><Camera class="size-3.5" />Foto {{ selectedImage + 1 }} dari {{ detail.images.length }}</div><button type="button" :disabled="selectedImage === 0" class="absolute left-3 top-1/2 grid size-9 -translate-y-1/2 place-items-center rounded-full bg-white/95 text-[#1677e8] shadow-lg transition hover:scale-105 disabled:opacity-40" aria-label="Foto sebelumnya" @click="selectedImage -= 1"><ChevronLeft class="size-5" /></button><button type="button" :disabled="selectedImage === detail.images.length - 1" class="absolute right-3 top-1/2 grid size-9 -translate-y-1/2 place-items-center rounded-full bg-white/95 text-[#1677e8] shadow-lg transition hover:scale-105 disabled:opacity-40" aria-label="Foto berikutnya" @click="selectedImage += 1"><ChevronRight class="size-5" /></button></div>
                            <button v-for="(image, index) in detail.images.slice(1)" :key="image" type="button" class="group relative hidden overflow-hidden rounded-xl text-left outline-none focus-visible:ring-2 focus-visible:ring-white sm:block" :class="index === 0 ? 'col-span-2' : ''" :aria-label="index === 2 ? 'Buka semua foto' : `Tampilkan foto ${index + 2}`" @click="index === 2 ? openGallery(index + 1) : selectedImage = index + 1"><img :src="image" alt="" class="size-full object-cover transition duration-500 group-hover:scale-105" /><span class="absolute inset-0 bg-gradient-to-t from-[#062e5e]/50 to-transparent"></span><span v-if="index === 2" class="absolute inset-0 grid place-items-center bg-[#062e5e]/55 text-xs font-extrabold text-white backdrop-blur-[2px]">Lihat {{ galleryImages.length }} foto</span></button>
                        </div>
                    </section>

                    <section class="mt-6 overflow-hidden rounded-2xl border border-[#e1eaf5] bg-white shadow-[0_8px_26px_rgba(23,75,120,0.04)]">
                        <div class="overflow-x-auto border-b border-[#e6eef7] [scrollbar-width:none] [&::-webkit-scrollbar]:hidden"><nav class="flex min-w-max gap-1 px-4 sm:px-6" aria-label="Detail perjalanan"><button v-for="tab in tabs" :key="tab" type="button" class="border-b-[3px] px-3 py-4 text-sm font-semibold transition-colors sm:px-4" :class="activeTab === tab ? 'border-[#1694ed] text-[#1688e8]' : 'border-transparent text-[#38517b] hover:text-[#1688e8]'" @click="activeTab = tab">{{ tab }}</button></nav></div>

                        <div class="p-5 sm:p-6">
                            <template v-if="activeTab === 'Deskripsi'">
                                <h2 class="text-xl font-extrabold tracking-tight text-[#096ab9]">Tentang Trip Ini</h2>
                                <TripRichContent class="mt-3 text-[#3f6094]" :html="experience.description_html" :text="tripData.description" />
                            </template>
                            <template v-else-if="activeTab === 'Destinasi'">
                                <div class="flex flex-wrap items-end justify-between gap-3"><div><p class="text-[11px] font-bold uppercase tracking-[0.16em] text-[#1688e8]">Panduan perjalanan</p><h2 class="mt-1 text-xl font-extrabold tracking-tight text-[#173b70]">Rute dan titik kunjungan</h2><p class="mt-1 text-xs text-[#60789c]">Pilih titik untuk melihat waktu, aktivitas, dan panduan praktisnya.</p></div><button type="button" class="inline-flex items-center gap-1.5 rounded-lg border px-3 py-2 text-xs font-bold transition" :class="routeSaved ? 'border-emerald-200 bg-emerald-50 text-emerald-700' : 'border-[#cfe2f6] bg-white text-[#1688e8] hover:bg-[#f4f9ff]'" @click="saveRoute"><CheckCircle2 class="size-4" />{{ routeSaved ? 'Rute tersimpan' : 'Simpan rute' }}</button></div>
                                <p v-if="!destinations.length" class="mt-5 text-sm text-slate-500">Vendor belum menambahkan rute kunjungan.</p><div v-if="destinations.length" class="mt-5 grid gap-4 lg:grid-cols-[minmax(0,0.85fr)_minmax(0,1.15fr)]"><div class="rounded-2xl border border-[#e1ebf6] bg-white p-2"><button v-for="(destination, index) in destinations" :key="destination.name" type="button" class="flex w-full items-center gap-3 rounded-xl p-3 text-left transition" :class="selectedDestination === index ? 'bg-[#edf7ff] text-[#173b70]' : 'text-[#496581] hover:bg-[#f8fbff]'" :aria-pressed="selectedDestination === index" @click="selectDestination(index)"><span class="grid size-8 shrink-0 place-items-center rounded-full text-[10px] font-extrabold" :class="selectedDestination === index ? 'bg-[#1688e8] text-white' : 'bg-[#edf6ff] text-[#1688e8]'">0{{ index + 1 }}</span><span class="min-w-0 flex-1"><span class="block truncate text-xs font-bold">{{ destination.name }}</span><span class="mt-0.5 block text-[10px] text-[#7186a2]">{{ destination.time }}</span></span><ChevronRight class="size-4 shrink-0" :class="selectedDestination === index ? 'text-[#1688e8]' : 'text-[#a6b7c9]'" /></button></div><article class="overflow-hidden rounded-2xl border border-[#dceaf7] bg-[#f8fbff]"><div class="relative h-44"><img :src="destinations[selectedDestination].image" :alt="destinations[selectedDestination].name" class="size-full object-cover" /><span class="absolute left-3 top-3 rounded-full bg-[#082d55]/75 px-2.5 py-1 text-[10px] font-bold text-white">Titik {{ selectedDestination + 1 }} dari {{ destinations.length }}</span></div><div class="p-5"><div class="flex items-start justify-between gap-3"><div><h3 class="text-lg font-extrabold text-[#173b70]">{{ destinations[selectedDestination].name }}</h3><p class="mt-1 text-xs text-[#7186a2]">{{ destinations[selectedDestination].subtitle }}</p></div><span class="rounded-lg bg-[#eaf4ff] px-2.5 py-1.5 text-[10px] font-bold text-[#1688e8]">{{ destinations[selectedDestination].time }}</span></div><dl class="mt-4 grid gap-3 sm:grid-cols-2"><div class="rounded-xl bg-white p-3"><dt class="text-[10px] font-semibold text-[#7186a2]">Aktivitas</dt><dd class="mt-1 text-xs font-bold text-[#31577f]">{{ destinations[selectedDestination].activity }}</dd></div><div class="rounded-xl bg-white p-3"><dt class="text-[10px] font-semibold text-[#7186a2]">Catatan traveler</dt><dd class="mt-1 text-xs font-bold leading-5 text-[#31577f]">{{ destinations[selectedDestination].note }}</dd></div></dl><button type="button" class="mt-4 inline-flex items-center gap-2 text-xs font-bold text-[#1688e8] transition hover:text-[#096ab9]" @click="openDestinationMap"><MapPin class="size-4" />Buka titik ini di Google Maps<ChevronRight class="size-4" /></button></div></article></div>
                                <p v-if="routeSaved" role="status" class="mt-4 rounded-xl bg-emerald-50 px-3 py-2 text-xs text-emerald-700">Rute kunjungan telah disimpan untuk trip ini.</p>
                            </template>
                            <template v-else-if="activeTab === 'Highlight'">
                                <div class="flex flex-wrap items-end justify-between gap-3"><div><p class="text-[11px] font-bold uppercase tracking-[0.16em] text-[#1688e8]">Yang akan kamu alami</p><h2 class="mt-1 text-xl font-extrabold tracking-tight text-[#173b70]">Highlight perjalanan</h2><p class="mt-1 text-xs text-[#60789c]">Pilih aktivitas untuk melihat detail rute, waktu, dan dokumentasinya.</p></div><span class="rounded-full bg-[#eaf4ff] px-3 py-1 text-[10px] font-bold text-[#1688e8]">{{ tripHighlights.length }} aktivitas utama</span></div>
                                <div class="mt-5 grid gap-3 sm:grid-cols-3"><button v-for="(item, visibleIndex) in visibleHighlights" :key="item.title" type="button" class="flex h-24 items-center gap-3 rounded-xl border p-3 text-left transition" :class="selectedHighlight === highlightPage * 3 + visibleIndex ? 'border-[#1688e8] bg-[#eef7ff] shadow-[0_5px_14px_rgba(22,136,232,0.12)]' : 'border-[#e1ebf6] bg-white hover:border-[#b9daf7] hover:bg-[#f8fbff]'" :aria-pressed="selectedHighlight === highlightPage * 3 + visibleIndex" @click="selectHighlight(highlightPage * 3 + visibleIndex)"><span class="grid size-9 shrink-0 place-items-center rounded-lg" :class="selectedHighlight === highlightPage * 3 + visibleIndex ? 'bg-[#1688e8] text-white' : 'bg-[#edf6ff] text-[#1688e8]'"><component :is="item.icon" class="size-4" /></span><span class="min-w-0"><span class="block line-clamp-2 text-xs font-bold leading-4 text-[#173b70]">{{ item.title }}</span><span class="mt-1 block text-[10px] text-[#7186a2]">{{ item.time }}</span></span></button></div>
                                <div v-if="highlightPageCount > 1" class="mt-4 flex items-center justify-center gap-2"><button type="button" class="grid size-8 place-items-center rounded-full border border-[#dbe8f5] text-[#1688e8] disabled:cursor-not-allowed disabled:opacity-35" aria-label="Halaman highlight sebelumnya" :disabled="highlightPage === 0" @click="selectHighlightPage(highlightPage - 1)"><ChevronLeft class="size-4" /></button><button v-for="page in highlightPageCount" :key="page" type="button" class="h-2 rounded-full transition-all" :class="highlightPage === page - 1 ? 'w-6 bg-[#1688e8]' : 'w-2 bg-[#c9deef] hover:bg-[#8dc3ed]'" :aria-label="`Halaman highlight ${page}`" :aria-current="highlightPage === page - 1 ? 'page' : undefined" @click="selectHighlightPage(page - 1)"></button><button type="button" class="grid size-8 place-items-center rounded-full border border-[#dbe8f5] text-[#1688e8] disabled:cursor-not-allowed disabled:opacity-35" aria-label="Halaman highlight berikutnya" :disabled="highlightPage === highlightPageCount - 1" @click="selectHighlightPage(highlightPage + 1)"><ChevronRight class="size-4" /></button></div>
                                <p v-if="!tripHighlights.length" class="mt-5 text-sm text-slate-500">Vendor belum menambahkan highlight perjalanan.</p><article v-if="tripHighlights.length" class="mt-5 overflow-hidden rounded-2xl border border-[#dceaf7] bg-[#f8fbff] sm:grid sm:min-h-[320px] sm:grid-cols-[minmax(0,0.88fr)_minmax(0,1.12fr)]"><div class="relative h-52 overflow-hidden sm:h-[320px]"><img v-if="tripHighlights[selectedHighlight].images.length" :src="tripHighlights[selectedHighlight].images[selectedHighlightPhoto]" :alt="tripHighlights[selectedHighlight].title" class="size-full object-cover" /><button type="button" class="absolute left-3 top-1/2 grid size-8 -translate-y-1/2 place-items-center rounded-full bg-white/90 text-[#1688e8] shadow-md" :disabled="tripHighlights[selectedHighlight].images.length < 2" aria-label="Foto highlight sebelumnya" @click="showHighlightPhoto(selectedHighlightPhoto - 1)"><ChevronLeft class="size-4" /></button><button type="button" class="absolute right-3 top-1/2 grid size-8 -translate-y-1/2 place-items-center rounded-full bg-white/90 text-[#1688e8] shadow-md" :disabled="tripHighlights[selectedHighlight].images.length < 2" aria-label="Foto highlight berikutnya" @click="showHighlightPhoto(selectedHighlightPhoto + 1)"><ChevronRight class="size-4" /></button><span v-if="tripHighlights[selectedHighlight].images.length" class="absolute bottom-3 right-3 rounded-full bg-[#082d55]/70 px-2.5 py-1 text-[10px] font-bold text-white">{{ selectedHighlightPhoto + 1 }} / {{ tripHighlights[selectedHighlight].images.length }}</span></div><div class="flex min-w-0 flex-col p-5"><div class="flex items-start justify-between gap-3"><span class="grid size-10 place-items-center rounded-xl bg-[#eaf4ff] text-[#1688e8]"><component :is="tripHighlights[selectedHighlight].icon" class="size-5" /></span><span class="rounded-full bg-white px-2.5 py-1 text-[10px] font-bold text-[#1688e8] shadow-sm">{{ tripHighlights[selectedHighlight].time }}</span></div><h3 class="mt-4 text-lg font-extrabold text-[#173b70]">{{ tripHighlights[selectedHighlight].title }}</h3><p class="mt-2 text-xs leading-5 text-[#60789c]">{{ tripHighlights[selectedHighlight].description }}</p><div class="mt-auto flex flex-wrap items-center justify-between gap-3 pt-5"><span class="inline-flex items-center gap-1.5 text-[11px] font-semibold text-[#31577f]"><MapPin class="size-4 text-[#1688e8]" />{{ tripHighlights[selectedHighlight].location }}</span><button type="button" class="inline-flex items-center gap-1 text-xs font-bold text-[#1688e8] transition hover:text-[#096ab9]" @click="openItinerary">Lihat di itinerary<ChevronRight class="size-4" /></button></div></div></article>
                            </template>
                            <template v-else-if="activeTab === 'Itinerary'">
                                <div class="flex items-end justify-between gap-4"><div><p class="text-[11px] font-bold uppercase tracking-[0.16em] text-[#1688e8]">{{ detail.duration }}</p><h2 class="mt-1 text-xl font-extrabold tracking-tight text-[#173b70]">Rencana perjalanan</h2><p class="mt-1 text-xs text-[#60789c]">Rincian agenda dari penyelenggara perjalanan.</p></div><CalendarDays class="size-6 text-[#1688e8]" /></div><TripRichContent class="mt-4 text-[#496581]" :html="experience.itinerary_html" :text="tripData.itinerary" /><ol class="relative mt-6 space-y-5 before:absolute before:bottom-6 before:left-[17px] before:top-6 before:w-px before:bg-[#cfe4fa]"><li v-for="(itinerary, index) in itineraryDays" :key="itinerary.day" class="relative flex gap-4"><span class="z-10 grid size-9 shrink-0 place-items-center rounded-full border-4 border-white bg-[#1688e8] text-[10px] font-extrabold text-white shadow-[0_2px_7px_rgba(22,136,232,0.25)]">0{{ index + 1 }}</span><article class="min-w-0 flex-1 rounded-2xl border border-[#e1ebf6] bg-[#f9fcff] p-4"><div class="flex flex-wrap items-center justify-between gap-2"><h3 class="text-sm font-extrabold text-[#173b70]">{{ itinerary.day }}</h3><span class="rounded-full bg-[#eaf4ff] px-2.5 py-1 text-[10px] font-bold text-[#1688e8]">{{ itinerary.meals }}</span></div><ul class="mt-4 space-y-2"><li v-for="activity in itinerary.activities" :key="activity" class="flex gap-2 text-xs leading-5 text-[#496581]"><CheckCircle2 class="mt-0.5 size-3.5 shrink-0 text-[#1688e8]" />{{ activity }}</li></ul></article></li></ol>
                            </template>
                            <template v-else-if="activeTab === 'Fasilitas'">
                                <div class="flex flex-wrap items-end justify-between gap-3"><div><p class="text-[11px] font-bold uppercase tracking-[0.16em] text-[#1688e8]">Rincian paket</p><h2 class="mt-1 text-xl font-extrabold tracking-tight text-[#173b70]">Fasilitas yang kamu dapatkan</h2><p class="mt-1 text-xs text-[#60789c]">Lihat isi paket secara lengkap sebelum memilih jadwal.</p></div><span class="rounded-full bg-[#eaf4ff] px-3 py-1 text-[10px] font-bold text-[#1688e8]">{{ facilityDetails.length }} fasilitas</span></div>
                                <div class="mt-5 flex gap-2 overflow-x-auto pb-1 [scrollbar-width:none] [&::-webkit-scrollbar]:hidden"><button v-for="category in facilityCategories" :key="category" type="button" class="shrink-0 rounded-full border px-3 py-2 text-xs font-bold transition" :class="selectedFacilityCategory === category ? 'border-[#1688e8] bg-[#1688e8] text-white shadow-[0_4px_10px_rgba(22,136,232,0.18)]' : 'border-[#dbe8f5] bg-white text-[#60789c] hover:border-[#a7d1f3] hover:text-[#1688e8]'" :aria-pressed="selectedFacilityCategory === category" @click="selectedFacilityCategory = category">{{ category }}</button></div>
                                <div class="mt-5 grid items-start gap-5 lg:grid-cols-[minmax(0,1.2fr)_minmax(250px,0.8fr)]"><section class="overflow-hidden rounded-2xl border border-[#e1ebf6] bg-white"><div class="flex items-center justify-between border-b border-[#edf3f8] px-4 py-3"><h3 class="flex items-center gap-2 text-sm font-bold text-[#173b70]"><CheckCircle2 class="size-4 text-emerald-500" />Termasuk dalam paket</h3><span class="text-[10px] font-semibold text-[#7186a2]">{{ filteredFacilityDetails.length }} item</span></div><ul class="divide-y divide-[#edf3f8]"><li v-for="facility in filteredFacilityDetails" :key="facility.title" class="flex gap-3 px-4 py-3"><CheckCircle2 class="mt-0.5 size-4 shrink-0 text-emerald-500" /><div><p class="text-xs font-bold text-[#31577f]">{{ facility.title }}</p><p class="mt-1 text-[11px] leading-5 text-[#7186a2]">{{ facility.note }}</p></div></li></ul></section><aside class="rounded-2xl border border-[#dceaf7] bg-[#f6faff] p-4"><div class="flex items-center justify-between gap-3"><div><p class="text-[10px] font-bold uppercase tracking-[0.14em] text-[#1688e8]">Checklist traveler</p><h3 class="mt-1 text-sm font-extrabold text-[#173b70]">Yang perlu kamu siapkan</h3></div><span class="rounded-full bg-white px-2.5 py-1 text-[10px] font-bold text-[#1688e8]">{{ preparedPackingItems.length }}/{{ packingItems.length }}</span></div><p class="mt-2 text-[11px] leading-5 text-[#60789c]">Tandai barang yang sudah siap agar persiapanmu tidak terlewat.</p><ul class="mt-4 space-y-2"><li v-for="item in packingItems" :key="item"><button type="button" class="flex w-full items-center gap-2 rounded-xl bg-white px-3 py-2.5 text-left text-xs transition hover:bg-[#edf6ff]" :class="preparedPackingItems.includes(item) ? 'text-[#1688e8]' : 'text-[#496581]'" :aria-pressed="preparedPackingItems.includes(item)" @click="togglePackingItem(item)"><span class="grid size-5 shrink-0 place-items-center rounded-md border" :class="preparedPackingItems.includes(item) ? 'border-[#1688e8] bg-[#1688e8] text-white' : 'border-[#c9dcea] bg-white text-transparent'"><CheckCircle2 class="size-3.5" /></span>{{ item }}</button></li></ul><div class="mt-4 rounded-xl border border-rose-100 bg-rose-50 p-3"><p class="flex items-center gap-1.5 text-[11px] font-bold text-rose-700"><XCircle class="size-4" />Tidak termasuk</p><p class="mt-1 text-[11px] leading-5 text-rose-600">{{ excludes.join(', ') || 'Belum ada rincian dari vendor.' }}</p></div></aside></div>
                            </template>
                            <template v-else-if="activeTab === 'Lokasi'">
                                <div class="flex flex-wrap items-end justify-between gap-3"><div><p class="text-[11px] font-bold uppercase tracking-[0.16em] text-[#1688e8]">Titik keberangkatan</p><h2 class="mt-1 text-xl font-extrabold tracking-tight text-[#173b70]">Panduan menuju lokasi</h2><p class="mt-1 text-xs text-[#60789c]">Informasi penting agar kamu tiba tepat waktu di titik kumpul.</p></div><span class="inline-flex items-center gap-1.5 rounded-full border border-[#d6e8f8] bg-white px-3 py-1.5 text-[10px] font-bold text-[#1688e8]"><MapPin class="size-3.5" />Meeting point</span></div>
                                <div class="mt-5 grid overflow-hidden rounded-2xl border border-[#dfe9f4] bg-white shadow-[0_10px_26px_rgba(27,75,122,0.06)] lg:grid-cols-[300px_minmax(0,1fr)]"><aside class="flex flex-col p-5 sm:p-6"><div class="flex items-start gap-3"><span class="grid size-10 shrink-0 place-items-center rounded-xl border border-[#d7e9f9] bg-[#f4faff] text-[#1688e8]"><MapPin class="size-5" :stroke-width="2.5" /></span><div><p class="text-[10px] font-bold uppercase tracking-[0.12em] text-[#7186a2]">Lokasi kumpul</p><h3 class="mt-1 text-sm font-extrabold leading-5 text-[#173b70]">{{ detail.startPoint }}</h3><p class="mt-1 text-[11px] leading-4 text-[#60789c]">{{ detail.location }}</p></div></div><div class="mt-5 grid grid-cols-2 divide-x divide-[#e7eef6] rounded-xl border border-[#e1ebf5] bg-[#fbfdff]"><div class="p-3"><p class="text-[10px] font-semibold text-[#7186a2]">Waktu kumpul</p><p class="mt-1 text-xs font-extrabold text-[#173b70]">{{ experience.detail?.meetingTime || 'Belum diisi' }}</p></div><div class="p-3"><p class="text-[10px] font-semibold text-[#7186a2]">Datang lebih awal</p><p class="mt-1 text-xs font-extrabold text-[#b66d11]">{{ experience.detail?.arrivalNote || 'Ikuti arahan vendor' }}</p></div></div><div class="mt-5 border-y border-[#e7eef6] py-4"><div class="flex items-center justify-between gap-3"><div><p class="text-[10px] font-bold uppercase tracking-[0.12em] text-[#7186a2]">Koordinat lokasi</p><p class="mt-1 font-mono text-[11px] font-bold text-[#31577f]">{{ detail.coordinates }}</p></div><button type="button" class="grid size-8 place-items-center rounded-lg border border-[#d7e9f9] text-[#1688e8] transition hover:bg-[#edf7ff]" :aria-label="isCoordinatesCopied ? 'Koordinat tersalin' : 'Salin koordinat'" @click="copyDestinationCoordinates"><CheckCircle2 v-if="isCoordinatesCopied" class="size-4 text-emerald-500" /><Copy v-else class="size-4" /></button></div></div><div class="mt-4 flex gap-2"><CheckCircle2 class="mt-0.5 size-4 shrink-0 text-emerald-500" /><p class="text-[11px] leading-5 text-[#60789c]">{{ experience.detail?.meetingNote || 'Bawa e-ticket perjalanan Anda.' }}</p></div><a :href="mapDirectionsUrl" target="_blank" rel="noopener noreferrer" class="mt-5 inline-flex min-h-11 items-center justify-center gap-2 rounded-xl bg-[#1688e8] px-4 text-xs font-bold text-white shadow-[0_5px_12px_rgba(22,136,232,0.22)] transition hover:bg-[#0875d0]"><Navigation class="size-4" />Mulai navigasi</a></aside><div class="relative h-72 border-t border-[#e2ebf4] bg-slate-100 lg:h-auto lg:min-h-[370px] lg:border-l lg:border-t-0"><iframe :src="mapEmbedUrl" :title="`Peta lokasi ${detail.title}`" class="size-full border-0" loading="lazy" allowfullscreen referrerpolicy="no-referrer-when-downgrade"></iframe><div class="pointer-events-none absolute left-4 top-4 inline-flex items-center gap-1.5 rounded-lg bg-white px-3 py-2 text-[10px] font-bold text-[#31577f] shadow-[0_3px_12px_rgba(15,47,82,0.16)]"><span class="grid size-5 place-items-center rounded-full bg-[#eaf4ff] text-[#1688e8]"><MapPin class="size-3" /></span>{{ detail.location }}</div></div></div>
                            </template>

                            <template v-if="activeTab === 'Deskripsi'">
                                <div class="mt-8 flex items-end justify-between gap-4"><div><h2 class="flex items-center gap-2 text-xl font-extrabold tracking-tight text-[#173b70]"><MapPin class="size-5 text-[#1688e8]" :stroke-width="2.5" />Destinasi</h2><p class="mt-1 pl-7 text-xs text-[#60789c]">Preview titik yang akan kamu kunjungi</p></div><button type="button" class="mb-1 hidden items-center gap-1 text-xs font-bold text-[#1688e8] transition-colors hover:text-[#096ab9] sm:inline-flex" @click="activeTab = 'Destinasi'">Lihat rute lengkap<ChevronRight class="size-4" /></button></div>
                                <div class="mt-4 flex gap-4 overflow-x-auto pb-3 [scrollbar-width:thin]"><article v-for="(destination, index) in destinations.slice(0, 4)" :key="index" class="w-56 shrink-0 overflow-hidden rounded-xl border border-[#e4edf7] bg-white shadow-[0_4px_12px_rgba(30,69,110,0.10)]"><img v-if="destination.image" :src="destination.image" :alt="destination.name" class="h-32 w-full object-cover" /><div class="p-3"><p class="flex items-center gap-2 text-xs font-bold text-[#173b70]"><span class="grid size-5 shrink-0 place-items-center rounded-md bg-[#edf6ff] text-[#1688e8]"><MapPin class="size-3.5" :stroke-width="2.5" /></span>{{ destination.name }}</p><p class="mt-1 pl-7 text-[10px] text-[#7186a2]">{{ detail.location }}</p></div></article></div>
                                <button type="button" class="mt-3 inline-flex items-center gap-2 text-xs font-bold text-[#1688e8] sm:hidden" @click="activeTab = 'Destinasi'">Lihat rute lengkap<ChevronRight class="size-4" /></button>
                            </template>
                        </div>
                    </section>
                </section>

                <aside class="self-start lg:fixed lg:top-[118px] lg:right-[max(2rem,calc((100vw-1220px)/2))] lg:z-20 lg:w-[330px]">
                    <section class="rounded-2xl border border-[#dce7f4] bg-white p-5 shadow-[0_8px_28px_rgba(23,75,120,0.08)]">
                        <p v-if="detail.originalPrice" class="text-xs text-slate-400 line-through">{{ detail.originalPrice }}</p>
                        <div class="mt-1 flex items-end gap-2">
                            <p class="text-2xl font-extrabold tracking-tight text-[#173b70]">{{ detail.price }}</p>
                            <span class="pb-1 text-[10px] text-slate-500">/ orang</span>
                        </div>
                        <p class="mt-1 text-[11px] text-slate-500">Harga dapat berubah sesuai jadwal dan jumlah peserta.</p>
                        <div class="mt-4 rounded-xl bg-[#f5f9ff] p-3">
                            <p class="text-[10px] font-semibold text-[#627a99]">Keberangkatan</p>
                            <p class="mt-1 flex items-center gap-2 text-xs font-bold text-[#31577f]">
                                <CalendarDays class="size-4 text-[#1677e8]" />{{ detail.date }}
                            </p>
                        </div>
                        <button
                            type="button"
                            class="mt-4 flex min-h-11 w-full items-center justify-center gap-2 rounded-xl bg-[#1677e8] px-4 text-xs font-bold text-white shadow-[0_5px_12px_rgba(22,119,232,0.25)] transition hover:bg-[#0d68d1]"
                            @click="checkAvailability"
                            :disabled="!canBook"
                        >
                            <CalendarDays class="size-4" />{{ !canBook ? 'Jadwal tidak tersedia' : page.props.auth?.user ? 'Pesan trip' : 'Masuk untuk memesan' }}
                        </button>
                        <a v-if="!canBook" :href="route('catalog')" class="mt-3 block text-center text-xs font-bold text-[#1688e8]">Cari jadwal lain</a>
                        <button type="button" class="mt-3 min-h-11 w-full rounded-xl border border-blue-200 px-4 text-xs font-bold text-[#1688e8]" @click="router.visit(route('account.section', 'support'))">Tanya lewat bantuan</button>
                    </section>

                    <section class="mt-4 rounded-2xl border border-[#e1eaf5] bg-white p-4"><div class="flex items-center justify-between"><h2 class="flex items-center gap-2 text-xs font-bold text-[#173b70]"><Compass class="size-4 text-[#1677e8]" />Fasilitas</h2><span class="text-[10px] font-semibold text-[#1677e8]">Lihat semua</span></div><div class="mt-4 grid grid-cols-3 gap-2"><div v-for="facility in facilities" :key="facility.label" class="grid min-h-20 place-items-center rounded-xl bg-[#f7faff] p-2 text-center"><component :is="facility.icon" class="size-5 text-[#1677e8]" /><span class="mt-2 text-[9px] font-medium text-[#56708d]">{{ facility.label }}</span></div></div></section>

                </aside>

            <div class="lg:col-start-1">
            <section class="mt-7 rounded-2xl border border-[#dfeaf5] bg-white p-6" aria-labelledby="vendor-heading">
                <p class="text-xs font-bold uppercase tracking-wider text-[#1688e8]">Diselenggarakan oleh</p>
                <h2 id="vendor-heading" class="mt-2 text-xl font-extrabold text-[#173b70]">{{ vendorInfo.name }}</h2>
                <p class="mt-2 text-sm text-[#60789c]">Mitra terverifikasi TapakLokal. Hubungi vendor melalui chat dari detail pesanan kamu.</p>
            </section>
            <TripPanorama :trip-type="tripType" :tours="panoramaTours" />
            <TripFaq :trip-type="tripType" :items="experience.faqs || []" />
            <TripReviews :trip-type="tripType" :items="reviews || []" />
            <TripRecommendations :trip-type="tripType" :items="relatedTrips" :vendor="tripData.vendor" />
            </div>
            </div>
        </main>

        <!-- Mobile Fixed Bottom Floating Booking Action Bar (lg:hidden) -->
        <aside aria-label="Aksi pemesanan cepat mobile" class="fixed inset-x-0 bottom-0 z-40 border-t border-[#dce7f4] bg-white/95 px-4 py-3 shadow-[0_-8px_25px_rgba(15,44,92,0.12)] backdrop-blur-md lg:hidden">
            <div class="mx-auto flex max-w-lg items-center justify-between gap-3">
                <div class="min-w-0">
                    <span v-if="detail.originalPrice" class="block text-[10px] text-slate-400 line-through leading-tight">{{ detail.originalPrice }}</span>
                    <div class="flex items-baseline gap-1">
                        <p class="text-lg sm:text-xl font-black text-[#173b70] leading-none">{{ detail.price }}</p>
                        <span class="text-[10px] text-slate-500 font-medium">/ pax</span>
                    </div>
                </div>
                <div class="flex items-center gap-2 shrink-0">
                    <button type="button" class="grid size-11 place-items-center rounded-xl border border-blue-200 text-[#1688e8]" aria-label="Tanya lewat bantuan" @click="router.visit(route('account.section', 'support'))"><MessageCircle class="size-5" /></button>
                    <button
                        type="button"
                        class="inline-flex min-h-11 items-center justify-center gap-1.5 rounded-xl bg-[#1677e8] px-5 py-2.5 text-xs font-bold text-white shadow-md shadow-[#1677e8]/30 transition hover:bg-[#0d68d1] active:scale-95 cursor-pointer"
                        @click="checkAvailability"
                        :disabled="!canBook"
                    >
                        <CalendarDays class="size-4" />
                        <span>{{ !canBook ? 'Tidak tersedia' : page.props.auth?.user ? 'Pesan trip' : 'Masuk & pesan' }}</span>
                    </button>
                </div>
            </div>
        </aside>

        <p v-if="bookingMessage" role="alert" class="mx-auto mb-6 max-w-[1180px] rounded-xl bg-amber-50 p-4 text-sm text-amber-900">{{ bookingMessage }}</p>

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

<style scoped>
.trip-detail { overflow-wrap: anywhere; }
.trip-detail :deep(p), .trip-detail :deep(h1), .trip-detail :deep(h2), .trip-detail :deep(h3), .trip-detail :deep(li) { overflow-wrap: anywhere; }
</style>
