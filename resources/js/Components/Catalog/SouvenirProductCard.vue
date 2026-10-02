<script setup>
import { Link } from '@inertiajs/vue3';
import { route } from 'ziggy-js';
import { MapPin } from 'lucide-vue-next';
import { formatSouvenirPrice, souvenirPhotoStyle } from './souvenirCatalog';

defineProps({ product: { type: Object, required: true } });
</script>

<template>
    <article class="group overflow-hidden rounded-xl border border-slate-200 bg-white transition hover:border-sky-300 hover:shadow-md">
        <Link :href="route('souvenirs.show', product.id)" class="block">
            <div class="relative aspect-square overflow-hidden bg-slate-100">
                <img v-if="product.imageUrl" :src="product.imageUrl" :alt="product.name" width="300" height="300" loading="lazy" decoding="async" class="size-full object-cover transition-transform group-hover:scale-[1.03] motion-reduce:transform-none" /><div v-else class="grid size-full place-items-center text-xs text-slate-500">Foto belum tersedia</div>
                <span class="absolute left-2 top-2 rounded-md px-2 py-1 text-[10px] font-bold" :class="product.availability === 'Preorder' ? 'bg-amber-50 text-amber-800' : 'bg-white text-[#075890]'">{{ product.availability }}</span>
            </div>
            <div class="p-3">
                <h3 class="min-h-10 text-xs font-semibold leading-5 text-[#172c50] sm:text-sm">{{ product.name }}</h3>
                <p class="mt-2 text-base font-extrabold text-[#172c50]">{{ formatSouvenirPrice(product.price) }}</p>
                <p class="mt-2 flex items-center gap-1 text-[10px] text-slate-500"><MapPin class="size-3" aria-hidden="true" />{{ product.shop.city }}</p>
                <p class="mt-1 text-[10px] leading-5 text-slate-500">{{ product.preparation }}</p>
            </div>
        </Link>
        <Link :href="route('souvenirs.store', product.shopId)" class="block border-t border-slate-100 px-3 py-2.5 text-[11px] font-semibold text-[#0175ea] hover:bg-sky-50">{{ product.shop.name }}</Link>
    </article>
</template>

