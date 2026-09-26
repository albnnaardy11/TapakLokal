<script setup>
import { computed, onBeforeUnmount, onMounted, ref } from 'vue';
import { Head, Link, router, usePage } from '@inertiajs/vue3';
import { route } from 'ziggy-js';
import {
    ArrowUpRight,
    Bell,
    Check,
    ChevronDown,
    Compass,
    Globe,
    KeyRound,
    LayoutDashboard,
    LogOut,
    Menu,
    MessageSquare,
    Settings,
    Shield,
    ShieldCheck,
    User,
    X,
} from 'lucide-vue-next';

const props = defineProps({
    title: String,
    subtitle: String,
    navigation: { type: Array, default: () => [] },
    vendor: Boolean,
});

const page = usePage();
const open = ref(false);
const showProfileMenu = ref(false);
const showNotifications = ref(false);
const showMessages = ref(false);

const groups = computed(() => Object.groupBy(props.navigation, (item) => item.group));
const user = computed(() => page.props.auth?.user || {});
const userName = computed(() => user.value?.name || (props.vendor ? 'Partner Vendor' : 'Admin TapakLokal'));
const initials = computed(() => (userName.value.slice(0, 1) || 'A').toUpperCase());
const panel = computed(() => page.props.adminPanel);
const roleLabel = computed(() => {
    if (props.vendor) return 'Partner Vendor';
    return panel.value?.label || 'Super Admin';
});
const homeUrl = computed(() => (props.vendor ? route('vendor.dashboard') : panel.value?.url || route('admin.dashboard')));

const notifications = ref([
    { id: 1, title: 'Pemesanan baru terkonfirmasi', time: '5 menit lalu', read: false, type: 'booking' },
    { id: 2, title: 'Vendor baru menunggu verifikasi KYC', time: '1 jam lalu', read: false, type: 'vendor' },
    { id: 3, title: 'Review bintang 5 baru ditambahkan', time: '3 jam lalu', read: true, type: 'review' },
]);

const messages = ref([
    { id: 1, sender: 'Budi Santoso', snippet: 'Halo admin, tiket booking #TL-4821 sudah bisa di-download?', time: '10m lalu', unread: true },
    { id: 2, sender: 'Brenggo Tour', snippet: 'Update kuota kursi untuk Open Trip Bromo weekend ini.', time: '2j lalu', unread: true },
    { id: 3, sender: 'Siti Rahma', snippet: 'Terima kasih atas bantuan proses refundnya!', time: '1h lalu', unread: false },
]);

const unreadNotifCount = computed(() => notifications.value.filter((n) => !n.read).length);
const unreadMsgCount = computed(() => messages.value.filter((m) => m.unread).length);

const toggleProfile = () => {
    showProfileMenu.value = !showProfileMenu.value;
    showNotifications.value = false;
    showMessages.value = false;
};

const toggleNotifications = () => {
    showNotifications.value = !showNotifications.value;
    showProfileMenu.value = false;
    showMessages.value = false;
};

const toggleMessages = () => {
    showMessages.value = !showMessages.value;
    showProfileMenu.value = false;
    showNotifications.value = false;
};

const closeAllDropdowns = () => {
    showProfileMenu.value = false;
    showNotifications.value = false;
    showMessages.value = false;
};

const markAllNotifsRead = () => {
    notifications.value.forEach((n) => (n.read = true));
};

const markAllMessagesRead = () => {
    messages.value.forEach((m) => (m.unread = false));
};

const handleClickOutside = (e) => {
    if (!e.target.closest('[data-dropdown]')) {
        closeAllDropdowns();
    }
};

onMounted(() => {
    document.addEventListener('click', handleClickOutside);
});

onBeforeUnmount(() => {
    document.removeEventListener('click', handleClickOutside);
});
</script>

<template>
    <Head :title="title" />
    <div class="min-h-screen bg-[#f4f7fb] font-sans text-[#1e293b]">
        <!-- Mobile Overlay -->
        <button
            v-if="open"
            class="fixed inset-0 z-40 bg-slate-950/40 backdrop-blur-sm lg:hidden"
            aria-label="Tutup navigasi"
            @click="open = false"
        ></button>

        <!-- Sidebar -->
        <aside
            class="fixed inset-y-0 left-0 z-50 flex w-64 flex-col border-r border-slate-200/80 bg-white shadow-[0_4px_24px_rgba(15,35,65,0.04)] transition-transform duration-300 lg:translate-x-0"
            :class="open ? 'translate-x-0' : '-translate-x-full'"
        >
            <!-- Sidebar Brand Header -->
            <div class="border-b border-slate-100 px-6 py-5">
                <Link :href="homeUrl" class="flex items-center gap-3">
                    <span class="grid size-10 place-items-center rounded-xl bg-gradient-to-br from-[#078cff] to-[#1677e8] text-white shadow-md shadow-blue-500/25">
                        <Compass class="size-5" />
                    </span>
                    <div>
                        <span class="text-lg font-black tracking-tight text-slate-900">
                            tapak<span class="text-[#078cff]">lokal</span>
                        </span>
                        <span class="block text-[9px] font-bold uppercase tracking-[0.16em] text-slate-400">
                            {{ vendor ? 'Partner Portal' : panel?.label || 'Admin Panel' }}
                        </span>
                    </div>
                </Link>
            </div>

            <!-- Sidebar Navigation -->
            <nav class="flex-1 overflow-y-auto px-3.5 py-5 [scrollbar-width:thin]" aria-label="Navigasi panel">
                <!-- Panel Switcher for Multi-Panel Admins -->
                <div v-if="!vendor && panel?.available?.length > 1" class="mb-5 px-1.5">
                    <label class="mb-1.5 block text-[10px] font-extrabold uppercase tracking-wider text-slate-400">
                        Panel Kerja
                    </label>
                    <div class="relative">
                        <select
                            :value="panel.url"
                            class="w-full appearance-none rounded-xl border border-slate-200 bg-slate-50/80 px-3 py-2.5 pr-8 text-xs font-bold text-slate-700 outline-none transition focus:border-blue-400 focus:bg-white focus:ring-2 focus:ring-blue-100"
                            @change="router.visit($event.target.value)"
                        >
                            <option v-for="item in panel.available" :key="item.key" :value="item.url">
                                {{ item.label }}
                            </option>
                        </select>
                        <ChevronDown class="pointer-events-none absolute right-3 top-3 size-3.5 text-slate-400" />
                    </div>
                </div>

                <!-- Overview Link -->
                <Link
                    :href="homeUrl"
                    class="mb-4 flex items-center gap-3 rounded-xl px-3.5 py-2.5 text-xs font-bold transition-all duration-200"
                    :class="
                        page.url === (vendor ? '/vendor' : '/admin/' + panel?.key)
                            ? 'bg-gradient-to-r from-[#1677e8] to-[#078cff] text-white shadow-md shadow-blue-500/25'
                            : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900'
                    "
                >
                    <LayoutDashboard class="size-4" />
                    <span>{{ panel?.key === 'super' ? 'Laporan & Pengawasan' : 'Overview' }}</span>
                </Link>

                <!-- Navigation Groups -->
                <div v-for="(items, group) in groups" :key="group" class="mb-5">
                    <p class="mb-1.5 px-3 text-[10px] font-extrabold uppercase tracking-widest text-slate-400">
                        {{ group }}
                    </p>
                    <div class="space-y-0.5">
                        <Link
                            v-for="item in items"
                            :key="item.key"
                            :href="item.url"
                            class="flex min-h-9 items-center justify-between rounded-xl px-3.5 py-2 text-xs font-semibold transition-all duration-200"
                            :class="
                                page.url.split('?')[0].startsWith(item.url.replace(/^https?:\/\/[^/]+/, ''))
                                    ? 'bg-gradient-to-r from-[#1677e8] to-[#078cff] text-white shadow-md shadow-blue-500/25'
                                    : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900'
                            "
                            @click="open = false"
                        >
                            <span>{{ item.label }}</span>
                        </Link>
                    </div>
                </div>
            </nav>
        </aside>

        <!-- Main Wrapper -->
        <div class="lg:pl-64">
            <!-- Top Navbar Header matching 1:1 user screenshot -->
            <header class="sticky top-0 z-30 flex h-[74px] items-center justify-between border-b border-slate-200/70 bg-white/90 px-5 backdrop-blur-md lg:px-9">
                <!-- Left: Mobile Toggle & Breadcrumb -->
                <div class="flex items-center gap-3">
                    <button
                        class="rounded-xl p-2 text-slate-600 hover:bg-slate-100 lg:hidden"
                        aria-label="Buka navigasi"
                        @click="open = true"
                    >
                        <Menu class="size-5" />
                    </button>
                    <div class="hidden items-center gap-2 text-xs font-medium text-slate-400 sm:flex">
                        <span>{{ vendor ? 'Partner' : 'Workspace' }}</span>
                        <span class="text-slate-300">/</span>
                        <span class="font-bold text-slate-800">{{ title }}</span>
                    </div>
                </div>

                <!-- Right: Notification, Chat/Message, Admin Profile Box & Website Link -->
                <div class="flex items-center gap-2.5 sm:gap-4">
                    <!-- Notification Bell Dropdown -->
                    <div class="relative" data-dropdown>
                        <button
                            type="button"
                            class="relative grid size-10 place-items-center rounded-full bg-slate-100 text-slate-600 transition hover:bg-slate-200 hover:text-slate-900 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-blue-400"
                            aria-label="Lihat notifikasi"
                            @click="toggleNotifications"
                        >
                            <Bell class="size-4" />
                            <span
                                v-if="unreadNotifCount > 0"
                                class="absolute right-2 top-2 size-2 rounded-full bg-rose-500 ring-2 ring-white"
                            ></span>
                        </button>

                        <!-- Notification Dropdown Menu -->
                        <div
                            v-if="showNotifications"
                            class="absolute right-0 top-12 z-50 w-80 rounded-2xl border border-slate-200/80 bg-white p-3 shadow-2xl animate-in fade-in zoom-in-95 duration-150"
                        >
                            <div class="flex items-center justify-between border-b border-slate-100 pb-2.5 px-2">
                                <h3 class="text-xs font-bold text-slate-900">Notifikasi</h3>
                                <button
                                    type="button"
                                    class="text-[10px] font-semibold text-blue-600 hover:text-blue-700"
                                    @click="markAllNotifsRead"
                                >
                                    Tandai dibaca
                                </button>
                            </div>
                            <div class="divide-y divide-slate-50 py-1 max-h-72 overflow-y-auto">
                                <div
                                    v-for="item in notifications"
                                    :key="item.id"
                                    class="flex items-start gap-2.5 p-2 rounded-xl transition hover:bg-slate-50"
                                    :class="{ 'bg-blue-50/40': !item.read }"
                                >
                                    <span class="mt-1 size-2 shrink-0 rounded-full" :class="item.read ? 'bg-slate-300' : 'bg-blue-500'"></span>
                                    <div class="min-w-0 flex-1">
                                        <p class="text-xs font-medium text-slate-800 leading-snug">{{ item.title }}</p>
                                        <p class="mt-0.5 text-[10px] text-slate-400">{{ item.time }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Chat/Message Dropdown -->
                    <div class="relative" data-dropdown>
                        <button
                            type="button"
                            class="relative grid size-10 place-items-center rounded-full bg-slate-100 text-slate-600 transition hover:bg-slate-200 hover:text-slate-900 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-blue-400"
                            aria-label="Lihat pesan masuk"
                            @click="toggleMessages"
                        >
                            <MessageSquare class="size-4" />
                            <span
                                v-if="unreadMsgCount > 0"
                                class="absolute right-2 top-2 size-2 rounded-full bg-rose-500 ring-2 ring-white"
                            ></span>
                        </button>

                        <!-- Messages Dropdown Menu -->
                        <div
                            v-if="showMessages"
                            class="absolute right-0 top-12 z-50 w-80 rounded-2xl border border-slate-200/80 bg-white p-3 shadow-2xl animate-in fade-in zoom-in-95 duration-150"
                        >
                            <div class="flex items-center justify-between border-b border-slate-100 pb-2.5 px-2">
                                <h3 class="text-xs font-bold text-slate-900">Pesan & Support</h3>
                                <button
                                    type="button"
                                    class="text-[10px] font-semibold text-blue-600 hover:text-blue-700"
                                    @click="markAllMessagesRead"
                                >
                                    Tandai dibaca
                                </button>
                            </div>
                            <div class="divide-y divide-slate-50 py-1 max-h-72 overflow-y-auto">
                                <div
                                    v-for="msg in messages"
                                    :key="msg.id"
                                    class="p-2 rounded-xl transition hover:bg-slate-50"
                                    :class="{ 'bg-blue-50/40': msg.unread }"
                                >
                                    <div class="flex items-center justify-between">
                                        <p class="text-xs font-bold text-slate-800">{{ msg.sender }}</p>
                                        <span class="text-[9px] text-slate-400">{{ msg.time }}</span>
                                    </div>
                                    <p class="mt-1 text-[11px] text-slate-500 line-clamp-1 leading-snug">{{ msg.snippet }}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Divider -->
                    <div class="h-6 w-px bg-slate-200"></div>

                    <!-- Admin Profile Pill matching exact 1:1 image -->
                    <div class="relative" data-dropdown>
                        <button
                            type="button"
                            class="flex items-center gap-3 rounded-full py-1 pl-2 pr-1 transition hover:bg-slate-100/80 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-blue-400"
                            aria-label="Menu akun admin"
                            @click="toggleProfile"
                        >
                            <!-- Text Label (Right Aligned Name & Role) -->
                            <div class="hidden text-right sm:block">
                                <p class="text-xs font-black tracking-tight text-slate-900 leading-tight">
                                    {{ userName }}
                                </p>
                                <p class="text-[10px] font-semibold text-slate-400 leading-tight">
                                    {{ roleLabel }}
                                </p>
                            </div>

                            <!-- Circular Coral/Red Avatar -->
                            <span class="grid size-10 place-items-center rounded-full bg-gradient-to-br from-rose-500 via-rose-600 to-red-600 text-sm font-black text-white shadow-md ring-2 ring-white">
                                {{ initials }}
                            </span>
                        </button>

                        <!-- Profile Dropdown Menu -->
                        <div
                            v-if="showProfileMenu"
                            class="absolute right-0 top-14 z-50 w-64 rounded-2xl border border-slate-200/80 bg-white p-2.5 shadow-2xl animate-in fade-in zoom-in-95 duration-150"
                        >
                            <div class="border-b border-slate-100 p-2.5">
                                <p class="text-xs font-bold text-slate-900">{{ userName }}</p>
                                <p class="text-[10px] text-slate-400 truncate">{{ user.email || 'admin@tapaklokal.test' }}</p>
                                <span class="mt-2 inline-block rounded-md bg-blue-50 px-2 py-0.5 text-[9px] font-bold uppercase tracking-wider text-blue-600">
                                    {{ roleLabel }}
                                </span>
                            </div>
                            <div class="py-1 text-xs">
                                <Link
                                    :href="route('account')"
                                    class="flex items-center gap-2.5 rounded-xl px-3 py-2 text-slate-700 transition hover:bg-slate-50"
                                    @click="closeAllDropdowns"
                                >
                                    <User class="size-4 text-slate-400" />
                                    <span>Profil Saya</span>
                                </Link>
                                <Link
                                    :href="route('password.change')"
                                    class="flex items-center gap-2.5 rounded-xl px-3 py-2 text-slate-700 transition hover:bg-slate-50"
                                    @click="closeAllDropdowns"
                                >
                                    <KeyRound class="size-4 text-slate-400" />
                                    <span>Ganti Kata Sandi</span>
                                </Link>
                                <a
                                    href="/"
                                    target="_blank"
                                    class="flex items-center justify-between rounded-xl px-3 py-2 text-slate-700 transition hover:bg-slate-50"
                                    @click="closeAllDropdowns"
                                >
                                    <span class="flex items-center gap-2.5">
                                        <Globe class="size-4 text-slate-400" />
                                        <span>Lihat Website</span>
                                    </span>
                                    <ArrowUpRight class="size-3.5 text-slate-400" />
                                </a>
                            </div>
                            <div class="border-t border-slate-100 pt-1">
                                <Link
                                    :href="route('logout')"
                                    method="post"
                                    as="button"
                                    class="flex w-full items-center gap-2.5 rounded-xl px-3 py-2 text-xs font-semibold text-rose-600 transition hover:bg-rose-50"
                                    @click="closeAllDropdowns"
                                >
                                    <LogOut class="size-4" />
                                    <span>Keluar</span>
                                </Link>
                            </div>
                        </div>
                    </div>

                    <!-- Lihat Website Link -->
                    <a
                        href="/"
                        target="_blank"
                        class="hidden md:inline-flex items-center gap-1.5 rounded-xl border border-slate-200 bg-white px-3.5 py-2 text-xs font-bold text-slate-700 shadow-sm transition hover:border-slate-300 hover:bg-slate-50 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-blue-400"
                    >
                        <span>Lihat website</span>
                        <ArrowUpRight class="size-3.5 text-slate-400" />
                    </a>
                </div>
            </header>

            <!-- Main Content Area -->
            <main class="mx-auto max-w-[1440px] px-5 py-7 lg:px-9 lg:py-8">
                <!-- Page Title Header -->
                <div class="mb-7 flex flex-wrap items-end justify-between gap-4">
                    <div>
                        <div class="mb-2 flex items-center gap-2">
                            <span class="inline-flex items-center gap-1 rounded-md bg-blue-50 px-2 py-0.5 text-[9px] font-extrabold uppercase tracking-wider text-blue-600">
                                <Shield class="size-3" />
                                <span>TAPAK LOKAL &bull; {{ vendor ? 'PARTNER' : panel?.label || 'ADMIN' }}</span>
                            </span>
                        </div>
                        <h1 class="text-2xl sm:text-3xl font-black tracking-tight text-slate-900">
                            {{ title }}
                        </h1>
                        <p v-if="subtitle" class="mt-1.5 max-w-2xl text-xs sm:text-sm font-medium leading-relaxed text-slate-500">
                            {{ subtitle }}
                        </p>
                    </div>
                    <slot name="actions" />
                </div>

                <!-- Flash Notifications -->
                <div
                    v-if="page.props.flash?.success"
                    role="status"
                    class="mb-6 flex items-center gap-3 rounded-2xl border border-emerald-200/80 bg-emerald-50/90 px-4 py-3.5 text-xs font-semibold text-emerald-800 shadow-sm"
                >
                    <Check class="size-4 shrink-0 text-emerald-600" />
                    <span>{{ page.props.flash.success }}</span>
                </div>
                <div
                    v-if="page.props.flash?.error || Object.keys(page.props.errors || {}).length"
                    role="alert"
                    class="mb-6 rounded-2xl border border-rose-200/80 bg-rose-50/90 p-4 text-xs font-semibold text-rose-800 shadow-sm"
                >
                    <p v-if="page.props.flash?.error">{{ page.props.flash.error }}</p>
                    <p v-for="(error, key) in page.props.errors" :key="key">{{ error }}</p>
                </div>

                <!-- Slot Content -->
                <slot />
            </main>
        </div>
    </div>
</template>
