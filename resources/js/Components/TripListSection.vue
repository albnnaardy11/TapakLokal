<script setup>
import { ref, computed } from 'vue';
import { Link } from '@inertiajs/vue3';
import {
    Star,
    Clock,
    MapPin,
    Users,
    ShieldCheck,
    ArrowRight,
    Heart,
    Flame,
    Gift
} from 'lucide-vue-next';

const props = defineProps({
    trips: {
        type: Array,
        default: () => [],
    },
    activeCategory: {
        type: String,
        default: 'Semua Kategori',
    },
    filterQuery: {
        type: String,
        default: '',
    },
});

const emit = defineEmits(['select-trip', 'toggle-wishlist']);

const bookmarkedIds = ref(new Set());

function toggleWishlist(id) {
    if (bookmarkedIds.value.has(id)) {
        bookmarkedIds.value.delete(id);
    } else {
        bookmarkedIds.value.add(id);
    }
    emit('toggle-wishlist', id);
}

function formatRupiah(number) {
    return new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: 'IDR',
        maximumFractionDigits: 0,
    }).format(number);
}

const filteredTrips = computed(() => {
    return props.trips.filter((trip) => {
        const matchesCategory =
            props.activeCategory === 'Semua Kategori' ||
            trip.category === props.activeCategory;
        const matchesQuery =
            !props.filterQuery ||
            trip.title.toLowerCase().includes(props.filterQuery.toLowerCase()) ||
            trip.location.toLowerCase().includes(props.filterQuery.toLowerCase());
        return matchesCategory && matchesQuery;
    });
});
</script>

<template>
    <section id="open-trip" class="py-8 sm:py-12 bg-white">
        <div class="max-w-7xl mx-auto px-3 sm:px-6 lg:px-8">
            <!-- Section Header -->
            <div class="flex flex-col md:flex-row md:items-end justify-between mb-6 sm:mb-8 gap-3 sm:gap-4">
                <div>
                    <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-blue-50 text-[#0052cc] text-xs font-bold mb-2">
                        <Flame class="w-3.5 h-3.5 text-orange-500" />
                        <span>Jadwal Keberangkatan Terdekat</span>
                    </div>
                    <h2 class="text-xl sm:text-2xl md:text-3xl font-extrabold text-slate-900 tracking-tight">
                        Open Trip Bersama Mitra Akamsi
                    </h2>
                    <p class="text-slate-500 text-xs sm:text-sm mt-1">
                        Jelajah bareng teman baru dengan kurasi rute lokal otentik tanpa biaya tersembunyi.
                    </p>
                </div>

                <div class="flex items-center gap-2">
                    <span class="text-xs font-bold text-slate-500 bg-slate-100 px-3 py-1 rounded-full">Total {{ filteredTrips.length }} Trip Aktif</span>
                </div>
            </div>

            <!-- Trip Grid (1 col on phone, 2 on tablet portrait, 3 on tablet landscape / medium desktop, 4 on large desktop) -->
            <div v-if="filteredTrips.length > 0" class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4 sm:gap-6">

                <div
                    v-for="trip in filteredTrips"
                    :key="trip.id"
                    class="group bg-white rounded-2xl border border-gray-100 shadow-sm hover:shadow-xl transition-all duration-300 overflow-hidden flex flex-col"
                >
                    <!-- Trip Image Thumbnail -->
                    <Link :href="route('trip.show', trip.id)" class="relative h-48 sm:h-52 w-full overflow-hidden bg-slate-100 block">
                        <img
                            :src="trip.image"
                            :alt="trip.title"
                            class="w-full h-full object-cover group-hover:scale-105 transition duration-500"
                        />
                        <!-- Duration Badge -->
                        <div class="absolute top-3 left-3 bg-black/60 backdrop-blur-xs text-white text-[11px] font-semibold px-2.5 py-1 rounded-full flex items-center gap-1">
                            <Clock class="w-3 h-3 text-amber-400" />
                            <span>{{ trip.duration }}</span>
                        </div>

                        <!-- Slots remaining badge -->
                        <div class="absolute bottom-3 left-3 bg-amber-500/90 backdrop-blur-xs text-white text-[10px] font-bold px-2 py-0.5 rounded-md">
                            Sisa {{ trip.slots_left }} Slot Kuota
                        </div>
                    </Link>

                    <!-- Wishlist Button -->
                    <button
                        type="button"
                        @click="toggleWishlist(trip.id)"
                        class="absolute top-3 right-3 w-8 h-8 rounded-full bg-white/90 backdrop-blur-xs flex items-center justify-center text-slate-600 hover:text-red-500 transition shadow-xs cursor-pointer z-10"
                    >
                        <Heart
                            :class="[
                                'w-4 h-4 transition',
                                bookmarkedIds.has(trip.id) ? 'fill-red-500 text-red-500' : ''
                            ]"
                        />
                    </button>

                    <!-- Content Body -->
                    <div class="p-4 flex-1 flex flex-col justify-between">
                        <div>
                            <!-- Location & Rating -->
                            <div class="flex items-center justify-between text-xs text-slate-500 mb-1.5">
                                <div class="flex items-center gap-1 truncate text-slate-500 font-medium">
                                    <MapPin class="w-3.5 h-3.5 text-[#0052cc] shrink-0" />
                                    <span class="truncate">{{ trip.location }}</span>
                                </div>
                                <div class="flex items-center gap-1 font-bold text-slate-800 shrink-0">
                                    <Star class="w-3.5 h-3.5 fill-amber-400 text-amber-400" />
                                    <span>{{ trip.rating }}</span>
                                </div>
                            </div>

                            <!-- Trip Title -->
                            <Link :href="route('trip.show', trip.id)" class="block">
                                <h3 class="font-bold text-slate-900 text-sm sm:text-base leading-snug line-clamp-2 group-hover:text-[#0052cc] transition">
                                    {{ trip.title }}
                                </h3>
                            </Link>

                            <!-- Akamsi Local Guide Badge -->
                            <div class="mt-2.5 py-1.5 px-2 bg-slate-50 rounded-lg flex items-center gap-1.5 border border-slate-100">
                                <ShieldCheck class="w-3.5 h-3.5 text-emerald-600 shrink-0" />
                                <div class="truncate text-[11px]">
                                    <span class="font-semibold text-slate-800">{{ trip.akamsi.name }}</span>
                                    <span class="text-slate-500 ml-1">({{ trip.akamsi.experience }})</span>
                                </div>
                            </div>

                            <!-- All-Inclusive Feature Checklist -->
                            <div class="mt-2.5 space-y-1 text-[11px] bg-blue-50/50 p-2 rounded-xl border border-blue-100/60">
                                <div class="flex items-center gap-1.5 text-slate-700 truncate" :title="trip.accommodation ? trip.accommodation.name : 'Penginapan Termasuk'">
                                    <span class="text-blue-600 font-bold shrink-0">🏨</span>
                                    <span class="truncate font-medium">{{ trip.accommodation ? trip.accommodation.name : 'Penginapan + Vendor Termasuk' }}</span>
                                </div>
                                <div class="flex items-center gap-1.5 text-slate-700 truncate" :title="trip.vehicle ? trip.vehicle.name : 'Kendaraan Lokal Termasuk'">
                                    <span class="text-emerald-600 font-bold shrink-0">🚙</span>
                                    <span class="truncate font-medium">{{ trip.vehicle ? trip.vehicle.name : 'Kendaraan di Lokasi Termasuk' }}</span>
                                </div>
                                <div class="flex items-center gap-1.5 text-amber-800 truncate">
                                    <span class="text-amber-600 font-bold shrink-0">🍽️</span>
                                    <span class="truncate font-semibold">Bebas Pilih Menu (Rp 0 Tambahan)</span>
                                </div>
                                <div class="flex items-center gap-1.5 text-emerald-800 truncate">
                                    <span class="text-emerald-600 font-bold shrink-0">🎁</span>
                                    <span class="truncate font-bold">Gratis Oleh-Oleh Khas (Tanpa Add-on)</span>
                                </div>
                            </div>
                        </div>

                        <!-- Price & CTA -->
                        <div class="mt-3.5 pt-3 border-t border-gray-100 flex items-center justify-between">
                            <div>
                                <span class="text-[10px] text-slate-400 font-medium block">Harga All-Inclusive</span>
                                <div class="font-black text-slate-900 text-base sm:text-lg">
                                    {{ formatRupiah(trip.price) }}
                                    <span class="text-[10px] text-slate-400 font-normal">/pax</span>
                                </div>
                            </div>

                            <Link
                                :href="route('trip.show', trip.id)"
                                class="bg-[#0052cc] hover:bg-[#0041a3] text-white px-3.5 py-2 rounded-xl text-xs font-bold transition flex items-center gap-1 shadow-2xs cursor-pointer"
                            >
                                <span>Rincian & Foto</span>
                                <ArrowRight class="w-3.5 h-3.5" />
                            </Link>
                        </div>
                    </div>
                </div>
            </div>

            <div v-else class="text-center py-12 bg-slate-50 rounded-2xl">
                <p class="text-slate-500 text-sm">Tidak ada trip yang sesuai dengan filter pencarian Anda.</p>
            </div>
        </div>
    </section>
</template>
