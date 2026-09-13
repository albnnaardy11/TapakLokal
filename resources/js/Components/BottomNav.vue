<script setup>
import {
    Compass,
    MapPin,
    ShoppingBag,
    ReceiptText,
    User
} from 'lucide-vue-next';

const props = defineProps({
    activeMenu: {
        type: String,
        default: 'Beranda',
    },
    user: {
        type: Object,
        default: () => ({ name: 'albnnaardy' }),
    },
});

const emit = defineEmits(['navigate', 'open-solit-bill']);

function handleClick(item) {
    if (item.action === 'solit-bill') {
        emit('open-solit-bill');
    } else {
        emit('navigate', item.name);
        if (item.href && item.href.startsWith('#')) {
            const el = document.querySelector(item.href);
            if (el) el.scrollIntoView({ behavior: 'smooth' });
        }
    }
}

const navItems = [
    { name: 'Beranda', icon: Compass, href: '#' },
    { name: 'Open Trip', icon: MapPin, href: '#open-trip' },
    { name: 'Open PO', icon: ShoppingBag, href: '#open-po' },
    { name: 'Solit Bill', icon: ReceiptText, action: 'solit-bill' },
];
</script>

<template>
    <!-- Fixed Mobile Bottom Navigation Bar (Phone only: md:hidden) -->
    <nav class="md:hidden fixed bottom-0 left-0 right-0 z-40 bg-white/95 backdrop-blur-md border-t border-gray-200/80 px-2 py-1.5 shadow-[0_-4px_16px_rgba(0,0,0,0.06)]">
        <div class="flex items-center justify-around">
            <button
                v-for="item in navItems"
                :key="item.name"
                type="button"
                @click="handleClick(item)"
                class="flex flex-col items-center justify-center py-1 px-3 rounded-xl transition cursor-pointer relative"
                :class="activeMenu === item.name ? 'text-[#0052cc]' : 'text-slate-500 hover:text-slate-800'"
            >
                <component
                    :is="item.icon"
                    class="w-5 h-5 transition-transform"
                    :class="activeMenu === item.name ? 'scale-110' : ''"
                />
                <span class="text-[10px] font-semibold mt-0.5 tracking-tight whitespace-nowrap">
                    {{ item.name }}
                </span>
                <!-- Active dot -->
                <span
                    v-if="activeMenu === item.name"
                    class="w-1 h-1 rounded-full bg-[#0052cc] mt-0.5"
                ></span>
            </button>
        </div>
    </nav>
</template>
