<script setup>
import { computed } from 'vue';
import { Link, usePage } from '@inertiajs/vue3';
import { route } from 'ziggy-js';
import { Bell, ShoppingCart, MessageCircle } from 'lucide-vue-next';
const page = usePage();
const navigation = computed(() => page.props.navigation || {});
const messageUrl = computed(() => {
    const permissions = page.props.auth?.permissions || [];
    if (permissions.includes('admin.access') && permissions.includes('operations.view')) return route('admin.resources.index', 'support');
    if (permissions.includes('vendor.access')) return route('vendor.section', 'support');
    return route('account.section', 'chat');
});
defineProps({ isTransparent: Boolean });
</script>
<template>
    <div class="flex items-center gap-1" :class="isTransparent ? 'text-white' : 'text-[#173b70]'">
        <Link :href="route('souvenirs.cart')" class="relative grid size-10 place-items-center rounded-full hover:bg-blue-100/20 focus-visible:ring-2 focus-visible:ring-blue-500" :aria-label="`Keranjang, ${navigation.cartCount || 0} produk`">
            <ShoppingCart class="size-5" /><span v-if="navigation.cartCount" class="absolute right-0 top-0 min-w-4 rounded-full bg-[#0088ff] px-1 text-center text-[10px] font-bold text-white">{{ navigation.cartCount > 99 ? '99+' : navigation.cartCount }}</span>
        </Link>
        <Link :href="route('notifications.index')" class="relative grid size-10 place-items-center rounded-full hover:bg-blue-100/20 focus-visible:ring-2 focus-visible:ring-blue-500" :aria-label="`Notifikasi, ${navigation.unreadCount || 0} belum dibaca`">
            <Bell class="size-5" /><span v-if="navigation.unreadCount" class="absolute right-0 top-0 min-w-4 rounded-full bg-red-500 px-1 text-center text-[10px] font-bold text-white">{{ navigation.unreadCount > 99 ? '99+' : navigation.unreadCount }}</span>
        </Link>
        <Link :href="messageUrl" class="grid size-10 place-items-center rounded-full hover:bg-blue-100/20" aria-label="Chat dan bantuan"><MessageCircle class="size-5" /></Link>
    </div>
</template>
