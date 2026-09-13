<script setup>
import { ref } from 'vue';
import { Head } from '@inertiajs/vue3';
import Navbar from '@/Components/Navbar.vue';
import HeroSection from '@/Components/HeroSection.vue';
import TripListSection from '@/Components/TripListSection.vue';
import PoKulinerSection from '@/Components/PoKulinerSection.vue';
import AkamsiSection from '@/Components/AkamsiSection.vue';
import SolitBillModal from '@/Components/SolitBillModal.vue';
import BottomNav from '@/Components/BottomNav.vue';
import Footer from '@/Components/Footer.vue';
import { CheckCircle2, Sparkles, X } from 'lucide-vue-next';


const props = defineProps({
    appName: {
        type: String,
        default: 'TapakLokal',
    },
    user: {
        type: Object,
        default: () => ({
            name: 'albnnaardy',
            handle: '@petualangnyasar',
            points: 0,
            tier: 'Petualang Perintis',
        }),
    },
    tripCategories: {
        type: Array,
        default: () => [],
    },
    featuredTrips: {
        type: Array,
        default: () => [],
    },
    poKulinerItems: {
        type: Array,
        default: () => [],
    },
});

const currentMenu = ref('Beranda');
const selectedCategory = ref('Semua Kategori');
const searchQuery = ref('');
const isSolitBillOpen = ref(false);
const toastMessage = ref(null);

function showToast(msg) {
    toastMessage.value = msg;
    setTimeout(() => {
        toastMessage.value = null;
    }, 3500);
}

function handleSearchFromWidget(filterData) {
    if (filterData.location) {
        searchQuery.value = filterData.location;
    }
    if (filterData.category && filterData.category !== 'Pilih Kategori') {
        selectedCategory.value = filterData.category;
    }

    if (filterData.type === 'po') {
        const poElement = document.getElementById('open-po');
        if (poElement) {
            poElement.scrollIntoView({ behavior: 'smooth' });
        }
        showToast(`Menampilkan Open PO Kuliner untuk "${filterData.location || 'Semua Daerah'}"`);
    } else {
        const tripElement = document.getElementById('open-trip');
        if (tripElement) {
            tripElement.scrollIntoView({ behavior: 'smooth' });
        }
        showToast(`Mencari Trip untuk ${filterData.personCount || 1} orang di "${filterData.location || 'Semua Rute'}"`);
    }
}

function handleSearchFromNav(query) {
    searchQuery.value = query;
    const tripElement = document.getElementById('open-trip');
    if (tripElement) {
        tripElement.scrollIntoView({ behavior: 'smooth' });
    }
    showToast(`Mencari "${query}" di TapakLokal...`);
}

function handleSelectTrip(trip) {
    showToast(`Membuka rincian lengkap untuk: ${trip.title}`);
}

function handleOrderItem(item) {
    showToast(`Memasukkan "${item.title}" ke keranjang titip PO!`);
}

function handleToggleWishlist(tripId) {
    showToast('Wishlist destinasi berhasil diperbarui!');
}
</script>

<template>
    <Head>
        <title>TapakLokal - Jelajahi Indonesia Secara Otentik</title>
        <meta name="description" content="Platform open trip dan titip kuliner nusantara langsung bersama pemandu lokal (Mitra Akamsi) tanpa biaya terselubung." />
    </Head>

    <div class="min-h-screen flex flex-col bg-slate-50 font-sans text-slate-800 antialiased selection:bg-[#0052cc] selection:text-white">
        <!-- Dual-tier Navigation Bar -->
        <Navbar
            :user="user"
            :active-menu="currentMenu"
            @navigate="currentMenu = $event"
            @open-solit-bill="isSolitBillOpen = true"
            @search="handleSearchFromNav"
        />

        <!-- Main Content Area -->
        <main class="flex-1">
            <!-- Hero Section (Ulun Danu Beratan background + Floating Dual-Tab Search Widget) -->
            <HeroSection
                :categories="tripCategories"
                @search="handleSearchFromWidget"
            />

            <!-- Category Pills Filter Bar -->
            <div class="max-w-7xl mx-auto px-3 sm:px-6 lg:px-8 pt-3 sm:pt-4 pb-2">
                <div class="flex items-center gap-2 overflow-x-auto pb-2 scrollbar-none -mx-1 px-1">
                    <button
                        v-for="cat in tripCategories"
                        :key="cat"
                        type="button"
                        @click="selectedCategory = cat"
                        :class="[
                            'px-3.5 sm:px-4 py-1.5 sm:py-2 rounded-full text-xs font-bold transition whitespace-nowrap cursor-pointer shadow-2xs shrink-0',
                            selectedCategory === cat
                                ? 'bg-[#0052cc] text-white'
                                : 'bg-white text-slate-600 hover:bg-slate-100 border border-slate-200/80'
                        ]"
                    >
                        {{ cat }}
                    </button>
                </div>
            </div>

            <!-- Open Trip Populer Section -->
            <TripListSection
                :trips="featuredTrips"
                :active-category="selectedCategory"
                :filter-query="searchQuery"
                @select-trip="handleSelectTrip"
                @toggle-wishlist="handleToggleWishlist"
            />

            <!-- Open PO Kuliner Nusantara Section -->
            <PoKulinerSection
                :items="poKulinerItems"
                @order-item="handleOrderItem"
            />

            <!-- Mitra Akamsi (Pemandu Lokal) Philosophy Section -->
            <AkamsiSection />
        </main>

        <!-- Footer -->
        <Footer />

        <!-- Mobile Bottom Navigation (Phone Viewport Only) -->
        <BottomNav
            :active-menu="currentMenu"
            :user="user"
            @navigate="currentMenu = $event"
            @open-solit-bill="isSolitBillOpen = true"
        />

        <!-- Solit Bill Interactive Modal -->
        <SolitBillModal
            :is-open="isSolitBillOpen"
            @close="isSolitBillOpen = false"
        />

        <!-- Notification Toast (Responsive: floats above bottom nav on mobile) -->
        <div
            v-if="toastMessage"
            class="fixed bottom-20 sm:bottom-6 left-4 right-4 sm:left-auto sm:right-6 z-50 bg-slate-900/95 text-white px-4 py-3 rounded-2xl shadow-2xl border border-slate-700 flex items-center justify-between sm:justify-start gap-3 backdrop-blur-md transition-all animate-bounce-short"
        >
            <div class="flex items-center gap-2.5">
                <CheckCircle2 class="w-5 h-5 text-emerald-400 shrink-0" />
                <span class="text-xs sm:text-sm font-medium">{{ toastMessage }}</span>
            </div>
            <button
                type="button"
                @click="toastMessage = null"
                class="text-slate-400 hover:text-white p-1 rounded-full cursor-pointer ml-1"
            >
                <X class="w-4 h-4" />
            </button>
        </div>
    </div>
</template>

<style scoped>
@keyframes bounceShort {
    0%, 100% { transform: translateY(0); }
    50% { transform: translateY(-4px); }
}
.animate-bounce-short {
    animation: bounceShort 0.4s ease-out;
}
</style>
