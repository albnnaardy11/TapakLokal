<script setup>
import {
    ShoppingBag,
    Star,
    Store,
    Clock,
    Truck,
    CheckCircle,
    ArrowRight
} from 'lucide-vue-next';

const props = defineProps({
    items: {
        type: Array,
        default: () => [],
    },
});

const emit = defineEmits(['order-item']);

function formatRupiah(number) {
    return new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: 'IDR',
        maximumFractionDigits: 0,
    }).format(number);
}
</script>

<template>
    <section id="open-po" class="py-8 sm:py-14 bg-slate-50/70 border-t border-slate-100">
        <div class="max-w-7xl mx-auto px-3 sm:px-6 lg:px-8">
            <!-- Header -->
            <div class="flex flex-col md:flex-row md:items-end justify-between mb-6 sm:mb-8 gap-3 sm:gap-4">
                <div>
                    <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-orange-50 text-orange-600 text-xs font-bold mb-2">
                        <ShoppingBag class="w-3.5 h-3.5 text-orange-500" />
                        <span>Titip Kuliner & Oleh-Oleh Asli</span>
                    </div>
                    <h2 class="text-xl sm:text-2xl md:text-3xl font-extrabold text-slate-900 tracking-tight">
                        Open PO Kuliner Nusantara
                    </h2>
                    <p class="text-slate-500 text-xs sm:text-sm mt-1">
                        Pesan oleh-oleh khas langsung dari produsen lokal saat tim open trip bertualang. Dijamin segar & original.
                    </p>
                </div>

                <div class="flex items-center gap-3">
                    <span class="text-xs font-semibold text-slate-500 bg-white px-3 py-1 rounded-full border border-slate-200 flex items-center gap-1.5 shadow-2xs">
                        <Truck class="w-4 h-4 text-emerald-600" />
                        Pengiriman Kilat Paxel / JNE YES
                    </span>
                </div>
            </div>

            <!-- Grid Items (1 on phone, 2 on tablet portrait, 3 on tablet landscape / medium laptop, 4 on desktop) -->
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4 sm:gap-6">

                <div
                    v-for="item in items"
                    :key="item.id"
                    class="bg-white rounded-2xl border border-gray-200/70 shadow-xs hover:shadow-lg transition-all duration-300 overflow-hidden flex flex-col justify-between"
                >
                    <div>
                        <!-- Thumbnail -->
                        <div class="relative h-44 w-full bg-slate-100 overflow-hidden">
                            <img
                                :src="item.image"
                                :alt="item.title"
                                class="w-full h-full object-cover group-hover:scale-105 transition duration-500"
                            />
                            <!-- Batch badge -->
                            <div class="absolute top-3 left-3 bg-orange-600 text-white text-[10px] font-bold px-2.5 py-0.5 rounded-full shadow-xs">
                                {{ item.badge }}
                            </div>

                            <!-- Origin Badge -->
                            <div class="absolute bottom-3 left-3 bg-black/60 backdrop-blur-xs text-white text-[11px] font-medium px-2.5 py-0.5 rounded-md">
                                {{ item.origin }}
                            </div>
                        </div>

                        <!-- Info Body -->
                        <div class="p-4">
                            <div class="flex items-center justify-between text-xs text-slate-500 mb-1">
                                <div class="flex items-center gap-1 text-[11px] font-semibold text-emerald-700">
                                    <Store class="w-3 h-3 text-emerald-600" />
                                    <span>{{ item.partner }}</span>
                                </div>
                                <div class="flex items-center gap-1 font-bold text-slate-800">
                                    <Star class="w-3 h-3 fill-amber-400 text-amber-400" />
                                    <span>{{ item.rating }}</span>
                                </div>
                            </div>

                            <h3 class="font-bold text-slate-900 text-sm leading-snug line-clamp-2 mt-1">
                                {{ item.title }}
                            </h3>

                            <div class="mt-2 text-[11px] text-amber-600 font-medium flex items-center gap-1">
                                <Clock class="w-3.5 h-3.5" />
                                <span>{{ item.close_date }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- Bottom Price & Order -->
                    <div class="p-4 pt-0">
                        <div class="pt-3 border-t border-gray-100 flex items-center justify-between">
                            <div>
                                <span class="text-[10px] text-slate-400 block">{{ item.sold }}</span>
                                <span class="font-black text-slate-900 text-base">{{ formatRupiah(item.price) }}</span>
                            </div>
                            <button
                                type="button"
                                @click="emit('order-item', item)"
                                class="bg-orange-500 hover:bg-orange-600 text-white px-3 py-1.5 rounded-xl text-xs font-bold transition shadow-xs flex items-center gap-1 cursor-pointer"
                            >
                                <span>Titip PO</span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</template>
