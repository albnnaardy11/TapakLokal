<script setup>
import { ref, computed, watch } from 'vue';
import {
    X,
    MapPin,
    Calendar,
    Users,
    Star,
    Clock,
    ShieldCheck,
    CheckCircle2,
    Home,
    Car,
    Utensils,
    Sparkles,
    ChevronRight,
    ArrowRight,
    AlertCircle,
    Info,
    Heart
} from 'lucide-vue-next';

const props = defineProps({
    trip: {
        type: Object,
        default: null,
    },
    isOpen: {
        type: Boolean,
        default: false,
    },
});

const emit = defineEmits(['close', 'confirm-booking', 'toggle-wishlist']);

// Booking State
const bookingType = ref('open'); // 'open' | 'private'
const paxCount = ref(1);
const customerName = ref('');
const customerPhone = ref('');
const specialNotes = ref('');
const selectedMeals = ref({});
const bookingSuccess = ref(false);

// Initialize selected meals default when trip changes
watch(
    () => props.trip,
    (newTrip) => {
        if (newTrip && newTrip.meals && newTrip.meals.options) {
            const initial = {};
            newTrip.meals.options.forEach((opt, idx) => {
                initial[idx] = opt.menu_choices[0]; // default to first choice
            });
            selectedMeals.value = initial;
            paxCount.value = 1;
            bookingType.value = 'open';
            bookingSuccess.value = false;
        }
    },
    { immediate: true }
);

function formatRupiah(num) {
    return new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: 'IDR',
        maximumFractionDigits: 0,
    }).format(num || 0);
}

const pricePerPax = computed(() => {
    if (!props.trip) return 0;
    return bookingType.value === 'private'
        ? (props.trip.private_price || props.trip.price * 1.3)
        : props.trip.price;
});

const totalPrice = computed(() => {
    return pricePerPax.value * paxCount.value;
});

function handlePaxChange(delta) {
    const minPax = bookingType.value === 'private' ? 2 : 1;
    const maxPax = bookingType.value === 'private' ? 20 : (props.trip?.slots_left || 10);
    const newVal = paxCount.value + delta;
    if (newVal >= minPax && newVal <= maxPax) {
        paxCount.value = newVal;
    }
}

function handleConfirm() {
    if (!customerName.value.trim() || !customerPhone.value.trim()) {
        alert('Mohon isi nama lengkap dan nomor WhatsApp pemesan terlebih dahulu.');
        return;
    }

    bookingSuccess.value = true;
    emit('confirm-booking', {
        trip: props.trip,
        bookingType: bookingType.value,
        paxCount: paxCount.value,
        totalPrice: totalPrice.value,
        customerName: customerName.value,
        customerPhone: customerPhone.value,
        selectedMeals: selectedMeals.value,
        notes: specialNotes.value,
    });
}

function closeModal() {
    bookingSuccess.value = false;
    emit('close');
}
</script>

<template>
    <div
        v-if="isOpen && trip"
        class="fixed inset-0 z-50 flex items-center justify-center p-2 sm:p-4 bg-black/60 backdrop-blur-xs transition-opacity duration-300"
    >
        <div class="bg-white rounded-2xl sm:rounded-3xl max-w-2xl w-full shadow-2xl relative max-h-[92vh] flex flex-col overflow-hidden border border-slate-100">
            <!-- Modal Header -->
            <div class="px-5 py-4 border-b border-gray-100 flex items-center justify-between bg-slate-50/80 shrink-0">
                <div class="flex items-center gap-2.5">
                    <div class="w-8 h-8 rounded-xl bg-blue-600 text-white flex items-center justify-center shadow-xs">
                        <Sparkles class="w-4 h-4" />
                    </div>
                    <div>
                        <h3 class="font-extrabold text-slate-900 text-sm sm:text-base leading-none">
                            Detail Paket All-Inclusive
                        </h3>
                        <span class="text-[11px] text-slate-500 font-medium">Penginapan + Kendaraan + Bebas Pilih Menu</span>
                    </div>
                </div>

                <button
                    type="button"
                    @click="closeModal"
                    class="w-8 h-8 rounded-full bg-white hover:bg-slate-100 border border-slate-200 text-slate-500 flex items-center justify-center transition cursor-pointer"
                >
                    <X class="w-4 h-4" />
                </button>
            </div>

            <!-- Modal Content (Scrollable) -->
            <div v-if="!bookingSuccess" class="overflow-y-auto p-4 sm:p-6 space-y-6 flex-1 text-slate-700 text-xs sm:text-sm">
                <!-- Top Image & Quick Badges -->
                <div class="relative h-48 sm:h-56 rounded-2xl overflow-hidden shadow-xs">
                    <img
                        :src="trip.image"
                        :alt="trip.title"
                        class="w-full h-full object-cover"
                    />
                    <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-black/20 to-transparent"></div>

                    <div class="absolute top-3 left-3 flex items-center gap-2">
                        <span class="bg-blue-600/90 text-white text-[11px] font-bold px-2.5 py-1 rounded-full backdrop-blur-xs flex items-center gap-1">
                            <Clock class="w-3 h-3" />
                            {{ trip.duration }}
                        </span>
                        <span class="bg-amber-500/90 text-white text-[11px] font-bold px-2.5 py-1 rounded-full backdrop-blur-xs flex items-center gap-1">
                            <Star class="w-3 h-3 fill-white" />
                            {{ trip.rating }} ({{ trip.review_count }} ulasan)
                        </span>
                    </div>

                    <div class="absolute bottom-3 left-3 right-3 text-white">
                        <div class="flex items-center gap-1.5 text-xs text-slate-200 mb-1">
                            <MapPin class="w-3.5 h-3.5 text-blue-400 shrink-0" />
                            <span>{{ trip.location }}</span>
                        </div>
                        <h2 class="font-extrabold text-base sm:text-xl leading-snug drop-shadow-md">
                            {{ trip.title }}
                        </h2>
                    </div>
                </div>

                <!-- 1. Trip Type Switcher (Open Trip vs Private Trip) -->
                <div class="bg-slate-50 p-3 sm:p-4 rounded-2xl border border-slate-200/80">
                    <div class="font-bold text-slate-900 text-xs sm:text-sm mb-2.5 flex items-center justify-between">
                        <span>Pilih Tipe Pemesanan:</span>
                        <span class="text-[11px] font-normal text-slate-500">Sesuaikan dengan rombonganmu</span>
                    </div>
                    <div class="grid grid-cols-2 gap-2.5">
                        <!-- Open Trip Option -->
                        <div
                            @click="bookingType = 'open'"
                            :class="[
                                'p-3 rounded-xl border-2 transition cursor-pointer flex flex-col justify-between',
                                bookingType === 'open'
                                    ? 'border-[#0052cc] bg-blue-50/70 shadow-2xs'
                                    : 'border-gray-200 bg-white hover:border-slate-300'
                            ]"
                        >
                            <div class="flex items-center justify-between mb-1">
                                <span class="font-bold text-slate-900 text-xs sm:text-sm">Paket Open Trip</span>
                                <div :class="['w-4 h-4 rounded-full border flex items-center justify-center', bookingType === 'open' ? 'border-[#0052cc] bg-[#0052cc]' : 'border-slate-300']">
                                    <div v-if="bookingType === 'open'" class="w-1.5 h-1.5 rounded-full bg-white"></div>
                                </div>
                            </div>
                            <p class="text-[11px] text-slate-500 mb-2">Gabung bareng teman baru, jadwal keberangkatan fix.</p>
                            <div class="font-extrabold text-[#0052cc] text-sm sm:text-base">
                                {{ formatRupiah(trip.price) }} <span class="text-[10px] text-slate-500 font-normal">/pax</span>
                            </div>
                        </div>

                        <!-- Private Trip Option -->
                        <div
                            @click="bookingType = 'private'"
                            :class="[
                                'p-3 rounded-xl border-2 transition cursor-pointer flex flex-col justify-between',
                                bookingType === 'private'
                                    ? 'border-emerald-600 bg-emerald-50/70 shadow-2xs'
                                    : 'border-gray-200 bg-white hover:border-slate-300'
                            ]"
                        >
                            <div class="flex items-center justify-between mb-1">
                                <span class="font-bold text-slate-900 text-xs sm:text-sm">Paket Private Trip</span>
                                <div :class="['w-4 h-4 rounded-full border flex items-center justify-center', bookingType === 'private' ? 'border-emerald-600 bg-emerald-600' : 'border-slate-300']">
                                    <div v-if="bookingType === 'private'" class="w-1.5 h-1.5 rounded-full bg-white"></div>
                                </div>
                            </div>
                            <p class="text-[11px] text-slate-500 mb-2">Khusus rombonganmu saja, tanggal & rute fleksibel.</p>
                            <div class="font-extrabold text-emerald-700 text-sm sm:text-base">
                                {{ formatRupiah(trip.private_price || trip.price * 1.35) }} <span class="text-[10px] text-slate-500 font-normal">/pax</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- 2. PENGINAPAN INCLUDE & NAMA VENDOR -->
                <div v-if="trip.accommodation" class="border border-blue-100 bg-blue-50/40 rounded-2xl p-4 space-y-3">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-2 text-slate-900 font-extrabold text-xs sm:text-sm">
                            <div class="w-6 h-6 rounded-lg bg-blue-100 text-blue-700 flex items-center justify-center">
                                <Home class="w-3.5 h-3.5" />
                            </div>
                            <span>Akomodasi & Penginapan Termasuk</span>
                        </div>
                        <span class="inline-flex items-center gap-1 text-[10px] font-bold text-emerald-700 bg-emerald-100 px-2 py-0.5 rounded-full">
                            <CheckCircle2 class="w-3 h-3" />
                            Include Paket
                        </span>
                    </div>

                    <div class="bg-white rounded-xl p-3 border border-blue-100 space-y-2">
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-1">
                            <span class="font-bold text-slate-900 text-xs sm:text-sm">{{ trip.accommodation.name }}</span>
                            <span class="text-[11px] text-blue-700 font-semibold bg-blue-50 px-2 py-0.5 rounded-md border border-blue-100">
                                Vendor: {{ trip.accommodation.vendor }}
                            </span>
                        </div>
                        <div class="text-[11px] text-slate-600 flex items-center gap-1.5">
                            <span class="font-medium text-slate-800">Tipe:</span>
                            <span>{{ trip.accommodation.room_type }}</span>
                        </div>
                        <div class="text-[11px] text-slate-500 bg-slate-50 p-2 rounded-lg border border-slate-100">
                            <strong>Fasilitas:</strong> {{ trip.accommodation.facilities }}
                        </div>
                    </div>
                </div>

                <!-- 3. KENDARAAN LOKAL INCLUDE & NAMA VENDOR -->
                <div v-if="trip.vehicle" class="border border-emerald-100 bg-emerald-50/40 rounded-2xl p-4 space-y-3">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-2 text-slate-900 font-extrabold text-xs sm:text-sm">
                            <div class="w-6 h-6 rounded-lg bg-emerald-100 text-emerald-700 flex items-center justify-center">
                                <Car class="w-3.5 h-3.5" />
                            </div>
                            <span>Kendaraan di Tempat Wisata Termasuk</span>
                        </div>
                        <span class="inline-flex items-center gap-1 text-[10px] font-bold text-emerald-700 bg-emerald-100 px-2 py-0.5 rounded-full">
                            <CheckCircle2 class="w-3 h-3" />
                            Include Paket
                        </span>
                    </div>

                    <div class="bg-white rounded-xl p-3 border border-emerald-100 space-y-2">
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-1">
                            <span class="font-bold text-slate-900 text-xs sm:text-sm">{{ trip.vehicle.name }}</span>
                            <span class="text-[11px] text-emerald-700 font-semibold bg-emerald-50 px-2 py-0.5 rounded-md border border-emerald-100">
                                {{ trip.vehicle.vendor }}
                            </span>
                        </div>
                        <div class="text-[11px] text-slate-600 flex items-center gap-1.5">
                            <span class="font-medium text-slate-800">Kategori:</span>
                            <span>{{ trip.vehicle.type }} ({{ trip.vehicle.capacity }})</span>
                        </div>
                    </div>
                </div>

                <!-- 4. BEBAS PILIH MENU MAKANAN LOKAL (NO EXTRA FEE) -->
                <div v-if="trip.meals && trip.meals.options" class="border border-amber-200 bg-amber-50/40 rounded-2xl p-4 space-y-3">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-1">
                        <div class="flex items-center gap-2 text-slate-900 font-extrabold text-xs sm:text-sm">
                            <div class="w-6 h-6 rounded-lg bg-amber-100 text-amber-800 flex items-center justify-center">
                                <Utensils class="w-3.5 h-3.5" />
                            </div>
                            <span>Pilihan Kuliner Lokal (Bebas Pilih Sesuai Selera)</span>
                        </div>
                        <span class="inline-flex items-center gap-1 text-[10px] font-bold text-emerald-800 bg-emerald-100 px-2.5 py-0.5 rounded-full self-start sm:self-auto">
                            <CheckCircle2 class="w-3 h-3" />
                            Tanpa Tambahan Biaya (Rp 0)
                        </span>
                    </div>

                    <p class="text-[11px] text-amber-900/80 leading-relaxed">
                        Di TapakLokal Anda tidak diseragamkan dengan satu menu. Silakan tentukan menu favorit khas daerah untuk setiap sesi makan:
                    </p>

                    <!-- Interactive Food Choices Radio Groups -->
                    <div class="space-y-3">
                        <div
                            v-for="(session, sIdx) in trip.meals.options"
                            :key="sIdx"
                            class="bg-white rounded-xl p-3 border border-amber-200/80 shadow-2xs"
                        >
                            <span class="block font-bold text-slate-900 text-xs mb-2 text-amber-900">
                                🥣 {{ session.session }}
                            </span>

                            <div class="space-y-1.5">
                                <label
                                    v-for="(menu, mIdx) in session.menu_choices"
                                    :key="mIdx"
                                    :class="[
                                        'flex items-center justify-between p-2 rounded-lg border transition cursor-pointer text-xs',
                                        selectedMeals[sIdx] === menu
                                            ? 'border-amber-400 bg-amber-50/60 font-semibold text-slate-900'
                                            : 'border-slate-100 hover:bg-slate-50 text-slate-700'
                                    ]"
                                >
                                    <div class="flex items-center gap-2 truncate">
                                        <input
                                            type="radio"
                                            :name="'meal_session_' + sIdx"
                                            :value="menu"
                                            v-model="selectedMeals[sIdx]"
                                            class="text-amber-600 focus:ring-amber-500 w-3.5 h-3.5"
                                        />
                                        <span class="truncate">{{ menu }}</span>
                                    </div>
                                    <span class="text-[10px] font-bold text-emerald-600 bg-emerald-50 px-1.5 py-0.5 rounded shrink-0 ml-2">
                                        Termasuk (+Rp 0)
                                    </span>
                                </label>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- 5. Mitra Akamsi Pemandu -->
                <div v-if="trip.akamsi" class="bg-slate-50 rounded-2xl p-3.5 border border-slate-200 flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-full bg-emerald-100 text-emerald-700 flex items-center justify-center font-black">
                            <ShieldCheck class="w-5 h-5" />
                        </div>
                        <div>
                            <span class="block font-bold text-slate-900 text-xs sm:text-sm">{{ trip.akamsi.name }}</span>
                            <span class="text-[11px] text-slate-500">{{ trip.akamsi.badge }} • {{ trip.akamsi.experience }}</span>
                        </div>
                    </div>
                    <span class="text-[10px] text-emerald-700 font-bold bg-emerald-50 border border-emerald-200 px-2 py-1 rounded-md">
                        Pemandu Terverifikasi
                    </span>
                </div>

                <!-- 6. Form Pemesan & Kuota Pax -->
                <div class="space-y-3 pt-2">
                    <div class="font-bold text-slate-900 text-xs sm:text-sm flex items-center justify-between">
                        <span>Data Pemesan & Jumlah Peserta</span>
                        <span class="text-[11px] font-normal text-slate-500">
                            {{ bookingType === 'open' ? `Sisa ${trip.slots_left} slot` : 'Bebas tentukan pax rombongan' }}
                        </span>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <div>
                            <label class="block text-[11px] font-semibold text-slate-600 mb-1">Nama Pemesan</label>
                            <input
                                v-model="customerName"
                                type="text"
                                placeholder="Contoh: Budi Santoso"
                                class="w-full px-3 py-2 bg-slate-50 border border-gray-200 rounded-xl text-xs focus:bg-white focus:ring-2 focus:ring-blue-500 focus:outline-none"
                            />
                        </div>
                        <div>
                            <label class="block text-[11px] font-semibold text-slate-600 mb-1">Nomor WhatsApp</label>
                            <input
                                v-model="customerPhone"
                                type="tel"
                                placeholder="Contoh: 081234567890"
                                class="w-full px-3 py-2 bg-slate-50 border border-gray-200 rounded-xl text-xs focus:bg-white focus:ring-2 focus:ring-blue-500 focus:outline-none"
                            />
                        </div>
                    </div>

                    <!-- Pax Selector -->
                    <div class="flex items-center justify-between p-3 bg-slate-50 rounded-xl border border-slate-200">
                        <div>
                            <span class="font-bold text-slate-900 block text-xs">Jumlah Wisatawan (Pax)</span>
                            <span class="text-[10px] text-slate-500">Termasuk penginapan, armada & konsumsi all-in</span>
                        </div>

                        <div class="flex items-center gap-3">
                            <button
                                type="button"
                                @click="handlePaxChange(-1)"
                                class="w-7 h-7 rounded-lg bg-white border border-slate-300 text-slate-700 font-bold hover:bg-slate-100 flex items-center justify-center transition cursor-pointer"
                            >
                                -
                            </button>
                            <span class="font-black text-slate-900 text-sm min-w-5 text-center">{{ paxCount }}</span>
                            <button
                                type="button"
                                @click="handlePaxChange(1)"
                                class="w-7 h-7 rounded-lg bg-white border border-slate-300 text-slate-700 font-bold hover:bg-slate-100 flex items-center justify-center transition cursor-pointer"
                            >
                                +
                            </button>
                        </div>
                    </div>

                    <!-- Catatan Tambahan -->
                    <div>
                        <label class="block text-[11px] font-semibold text-slate-600 mb-1">Catatan Tambahan / Alergi (Opsional)</label>
                        <input
                            v-model="specialNotes"
                            type="text"
                            placeholder="Contoh: Mohon menu tidak pedas, alergi seafood, dll"
                            class="w-full px-3 py-2 bg-slate-50 border border-gray-200 rounded-xl text-xs focus:bg-white focus:ring-2 focus:ring-blue-500 focus:outline-none"
                        />
                    </div>
                </div>
            </div>

            <!-- Booking Success State -->
            <div v-else class="p-8 text-center space-y-4 my-auto">
                <div class="w-16 h-16 rounded-full bg-emerald-100 text-emerald-600 mx-auto flex items-center justify-center">
                    <CheckCircle2 class="w-8 h-8" />
                </div>
                <h3 class="text-xl font-black text-slate-900">Pemesanan Paket Berhasil!</h3>
                <p class="text-xs sm:text-sm text-slate-600 max-w-md mx-auto leading-relaxed">
                    Terima kasih <strong>{{ customerName }}</strong>! Tim Mitra Akamsi TapakLokal akan segera menghubungi WhatsApp kamu di <strong>{{ customerPhone }}</strong> untuk konfirmasi penjemputan dan rincian penginapan <strong>{{ trip.accommodation?.name }}</strong>.
                </p>

                <div class="bg-slate-50 p-4 rounded-2xl border border-slate-200 max-w-md mx-auto text-left text-xs space-y-2">
                    <div class="flex justify-between">
                        <span class="text-slate-500">Paket:</span>
                        <span class="font-bold text-slate-900">{{ trip.title }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-slate-500">Tipe Trip:</span>
                        <span class="font-bold text-slate-900">{{ bookingType === 'open' ? 'Open Trip (Gabung)' : 'Private Trip (Rombongan Sendiri)' }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-slate-500">Jumlah Peserta:</span>
                        <span class="font-bold text-slate-900">{{ paxCount }} Pax</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-slate-500">Pilihan Makanan:</span>
                        <span class="font-bold text-slate-900">Sudah Sesuai Selera (Rp 0 Tambahan)</span>
                    </div>
                    <div class="flex justify-between pt-2 border-t border-slate-200 text-sm">
                        <span class="font-extrabold text-slate-900">Total Biaya All-In:</span>
                        <span class="font-black text-[#0052cc]">{{ formatRupiah(totalPrice) }}</span>
                    </div>
                </div>

                <div class="pt-4">
                    <button
                        type="button"
                        @click="closeModal"
                        class="bg-[#0052cc] hover:bg-[#003894] text-white font-bold px-6 py-2.5 rounded-xl text-xs sm:text-sm transition cursor-pointer shadow-md"
                    >
                        Selesai & Kembali
                    </button>
                </div>
            </div>

            <!-- Modal Footer (Price Summary & CTA) -->
            <div v-if="!bookingSuccess" class="p-4 sm:p-5 border-t border-gray-100 bg-slate-50 flex items-center justify-between gap-3 shrink-0">
                <div>
                    <span class="text-[10px] text-slate-500 font-medium block">
                        Total Biaya ({{ paxCount }} Pax • All-Inclusive)
                    </span>
                    <div class="font-black text-slate-900 text-base sm:text-xl text-[#0052cc]">
                        {{ formatRupiah(totalPrice) }}
                    </div>
                </div>

                <div class="flex items-center gap-2">
                    <button
                        type="button"
                        @click="closeModal"
                        class="px-3.5 py-2.5 rounded-xl border border-gray-300 text-xs font-bold text-slate-600 hover:bg-gray-100 transition cursor-pointer"
                    >
                        Batal
                    </button>
                    <button
                        type="button"
                        @click="handleConfirm"
                        class="bg-[#0052cc] hover:bg-[#003894] text-white px-5 py-2.5 rounded-xl text-xs sm:text-sm font-bold transition flex items-center gap-1.5 shadow-md shadow-blue-500/20 cursor-pointer"
                    >
                        <span>Pesan Paket Sekarang</span>
                        <ArrowRight class="w-3.5 h-3.5" />
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>
