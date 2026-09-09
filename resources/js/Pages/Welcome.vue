<script setup>
import { ref, computed, onMounted } from 'vue';
import { Head, Link } from '@inertiajs/vue3';
import {
    Activity,
    CheckCircle2,
    XCircle,
    Database,
    Layers,
    Server,
    Zap,
    Cpu,
    Boxes,
    Terminal,
    Copy,
    Check,
    ArrowRight,
    Sparkles,
    ShieldCheck,
    Flame
} from 'lucide-vue-next';

const props = defineProps({
    appName: {
        type: String,
        default: 'TapakLokal',
    },
    laravelVersion: {
        type: String,
        default: '13.x',
    },
    phpVersion: {
        type: String,
        default: '8.4',
    },
    dbStatus: {
        type: Object,
        default: () => ({
            connected: false,
            driver: 'pgsql',
            database: 'tapaklokal',
            host: '127.0.0.1',
            port: '5432',
            error: null,
        }),
    },
    redisStatus: {
        type: Object,
        default: () => ({
            connected: false,
            client: 'predis',
            host: '127.0.0.1',
            port: '6379',
            error: null,
        }),
    },
});

const counter = ref(0);
const copiedCommand = ref(null);
const activeTab = ref('overview');

const techStack = computed(() => [
    {
        name: 'Laravel',
        version: props.laravelVersion,
        sub: `PHP ${props.phpVersion}`,
        icon: Flame,
        color: 'from-red-500/20 to-orange-500/10 text-red-400 border-red-500/30',
        badge: 'Backend Framework',
        status: 'Ready',
    },
    {
        name: 'Vue.js 3',
        version: 'v3.5+',
        sub: 'Composition API',
        icon: Boxes,
        color: 'from-emerald-500/20 to-teal-500/10 text-emerald-400 border-emerald-500/30',
        badge: 'Frontend UI',
        status: 'Ready',
    },
    {
        name: 'Inertia.js',
        version: 'v2.x',
        sub: 'Monolith SPA',
        icon: Zap,
        color: 'from-purple-500/20 to-indigo-500/10 text-purple-400 border-purple-500/30',
        badge: 'Bridge Adapter',
        status: 'Ready',
    },
    {
        name: 'PostgreSQL',
        version: props.dbStatus.driver?.toUpperCase() || 'PGSQL',
        sub: `${props.dbStatus.host}:${props.dbStatus.port}`,
        icon: Database,
        color: props.dbStatus.connected
            ? 'from-sky-500/20 to-blue-500/10 text-sky-400 border-sky-500/30'
            : 'from-amber-500/20 to-yellow-500/10 text-amber-400 border-amber-500/30',
        badge: 'Database Engine',
        status: props.dbStatus.connected ? 'Connected' : 'Configured (5432)',
    },
    {
        name: 'Redis Cache',
        version: props.redisStatus.client || 'predis',
        sub: `${props.redisStatus.host}:${props.redisStatus.port}`,
        icon: Server,
        color: props.redisStatus.connected
            ? 'from-rose-500/20 to-red-500/10 text-rose-400 border-rose-500/30'
            : 'from-violet-500/20 to-purple-500/10 text-violet-400 border-violet-500/30',
        badge: 'Cache & Session',
        status: props.redisStatus.connected ? 'Connected' : 'Configured (6379)',
    },
    {
        name: 'Tailwind CSS',
        version: 'v4.0',
        sub: 'Vite Plugin Engine',
        icon: Sparkles,
        color: 'from-cyan-500/20 to-blue-500/10 text-cyan-400 border-cyan-500/30',
        badge: 'Design System',
        status: 'Ready',
    },
]);

const copyToClipboard = (text, id) => {
    navigator.clipboard.writeText(text);
    copiedCommand.value = id;
    setTimeout(() => {
        copiedCommand.value = null;
    }, 2000);
};
</script>

<template>
    <Head :title="`${appName} - Modern Fullstack Stack`" />

    <div class="min-h-screen bg-slate-950 text-slate-100 flex flex-col justify-between selection:bg-indigo-500 selection:text-white relative overflow-hidden font-sans">
        <!-- Ambient background lighting effects -->
        <div class="pointer-events-none absolute -top-40 left-1/2 -translate-x-1/2 w-[800px] h-[450px] bg-gradient-to-tr from-indigo-600/30 via-purple-600/20 to-cyan-500/20 blur-[130px] rounded-full"></div>
        <div class="pointer-events-none absolute top-1/2 -right-40 w-[500px] h-[500px] bg-gradient-to-br from-emerald-600/15 to-sky-600/10 blur-[140px] rounded-full"></div>
        <div class="pointer-events-none absolute -bottom-40 -left-40 w-[500px] h-[500px] bg-gradient-to-tr from-rose-600/15 to-violet-600/10 blur-[140px] rounded-full"></div>

        <!-- Header / Navigation -->
        <header class="w-full border-b border-slate-800/80 bg-slate-950/70 backdrop-blur-xl sticky top-0 z-50">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between">
                <div class="flex items-center space-x-3">
                    <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-indigo-500 via-purple-500 to-cyan-400 p-[1px] shadow-lg shadow-indigo-500/20 flex items-center justify-center">
                        <div class="w-full h-full bg-slate-950 rounded-[11px] flex items-center justify-center">
                            <Layers class="w-5 h-5 text-indigo-400" />
                        </div>
                    </div>
                    <div>
                        <span class="text-lg font-bold tracking-tight bg-gradient-to-r from-white via-slate-200 to-slate-400 bg-clip-text text-transparent">
                            {{ appName }}
                        </span>
                        <span class="ml-2 text-xs px-2 py-0.5 rounded-full bg-indigo-500/10 border border-indigo-500/30 text-indigo-300 font-medium">
                            Fullstack Active
                        </span>
                    </div>
                </div>

                <div class="flex items-center space-x-3">
                    <div class="hidden sm:flex items-center space-x-2 text-xs text-slate-400 bg-slate-900/80 px-3 py-1.5 rounded-lg border border-slate-800">
                        <span class="inline-block w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                        <span>Laravel {{ laravelVersion }} &bull; PHP {{ phpVersion }}</span>
                    </div>
                    <a
                        href="https://github.com"
                        target="_blank"
                        rel="noreferrer"
                        class="text-xs px-3.5 py-1.5 rounded-lg bg-slate-800 hover:bg-slate-700 text-slate-200 font-medium transition-all duration-200 border border-slate-700 flex items-center space-x-1.5"
                    >
                        <span>Workspace</span>
                        <ArrowRight class="w-3.5 h-3.5 text-slate-400" />
                    </a>
                </div>
            </div>
        </header>

        <!-- Main Hero & Dashboard -->
        <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10 sm:py-16 w-full z-10 flex-1">
            <!-- Hero Banner -->
            <div class="text-center max-w-3xl mx-auto mb-12 sm:mb-16">
                <div class="inline-flex items-center space-x-2 px-3.5 py-1.5 rounded-full bg-gradient-to-r from-indigo-500/10 via-purple-500/10 to-pink-500/10 border border-indigo-500/30 text-indigo-300 text-xs font-medium mb-6 backdrop-blur-md shadow-inner">
                    <Sparkles class="w-3.5 h-3.5 text-indigo-400 animate-spin" style="animation-duration: 6s" />
                    <span>Inertia.js + Vue 3 + PostgreSQL + Redis Berhasil Terpasang!</span>
                </div>

                <h1 class="text-4xl sm:text-6xl font-extrabold tracking-tight text-white mb-6 leading-tight">
                    Stack Modern
                    <span class="bg-gradient-to-r from-indigo-400 via-purple-300 to-cyan-300 bg-clip-text text-transparent">
                        Laravel & Vue 3
                    </span>
                    Siap Digunakan
                </h1>

                <p class="text-base sm:text-lg text-slate-400 leading-relaxed max-w-2xl mx-auto">
                    Aplikasi <span class="text-slate-200 font-medium">{{ appName }}</span> telah dikonfigurasi penuh dengan Vue 3 Composition API, Inertia bridge, PostgreSQL database, dan Redis caching/session driver.
                </p>
            </div>

            <!-- Tech Stack Status Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5 mb-12">
                <div
                    v-for="(item, index) in techStack"
                    :key="index"
                    class="group relative rounded-2xl bg-slate-900/60 backdrop-blur-xl border border-slate-800/80 p-6 hover:border-slate-700/80 transition-all duration-300 hover:shadow-xl hover:shadow-indigo-500/5 hover:-translate-y-1"
                >
                    <div class="flex items-start justify-between mb-4">
                        <div
                            class="w-12 h-12 rounded-xl bg-gradient-to-br flex items-center justify-center border shadow-inner transition-transform duration-300 group-hover:scale-110"
                            :class="item.color"
                        >
                            <component :is="item.icon" class="w-6 h-6" />
                        </div>
                        <span class="text-xs px-2.5 py-1 rounded-full font-medium bg-slate-800/80 border border-slate-700/60 text-slate-300">
                            {{ item.badge }}
                        </span>
                    </div>

                    <h3 class="text-lg font-bold text-white mb-1 flex items-center justify-between">
                        <span>{{ item.name }}</span>
                        <span class="text-xs font-mono text-indigo-400 bg-indigo-500/10 px-2 py-0.5 rounded border border-indigo-500/20">
                            {{ item.version }}
                        </span>
                    </h3>

                    <p class="text-xs text-slate-400 mb-4 font-mono">
                        {{ item.sub }}
                    </p>

                    <div class="pt-3 border-t border-slate-800/80 flex items-center justify-between text-xs">
                        <span class="text-slate-400">Status</span>
                        <span
                            class="inline-flex items-center space-x-1.5 font-medium"
                            :class="item.status.includes('Connected') || item.status === 'Ready' ? 'text-emerald-400' : 'text-amber-400'"
                        >
                            <CheckCircle2 v-if="item.status.includes('Connected') || item.status === 'Ready'" class="w-3.5 h-3.5 text-emerald-400" />
                            <Activity v-else class="w-3.5 h-3.5 text-amber-400" />
                            <span>{{ item.status }}</span>
                        </span>
                    </div>
                </div>
            </div>

            <!-- Interactive Features & System Overview -->
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 mb-12">
                <!-- Interactive Vue State Demo -->
                <div class="lg:col-span-6 rounded-2xl bg-gradient-to-b from-slate-900/80 to-slate-900/40 backdrop-blur-xl border border-slate-800 p-6 sm:p-8 flex flex-col justify-between shadow-lg">
                    <div>
                        <div class="flex items-center space-x-3 mb-4">
                            <div class="p-2 rounded-lg bg-emerald-500/10 border border-emerald-500/30 text-emerald-400">
                                <Cpu class="w-5 h-5" />
                            </div>
                            <div>
                                <h3 class="text-lg font-bold text-white">Live Vue 3 Reactive State</h3>
                                <p class="text-xs text-slate-400">Uji reaktivitas Composition API Vue 3 di Inertia</p>
                            </div>
                        </div>

                        <div class="p-5 rounded-xl bg-slate-950/80 border border-slate-800 mb-6 flex items-center justify-between">
                            <div>
                                <span class="text-xs uppercase tracking-wider text-slate-400 block mb-1 font-semibold">Reactive Counter Value</span>
                                <span class="text-3xl font-extrabold text-white font-mono">{{ counter }}</span>
                            </div>
                            <div class="flex items-center space-x-2">
                                <button
                                    @click="counter--"
                                    class="w-10 h-10 rounded-lg bg-slate-800 hover:bg-slate-700 text-white font-bold transition flex items-center justify-center border border-slate-700 active:scale-95"
                                >
                                    -
                                </button>
                                <button
                                    @click="counter++"
                                    class="w-10 h-10 rounded-lg bg-indigo-600 hover:bg-indigo-500 text-white font-bold transition flex items-center justify-center border border-indigo-500 shadow-lg shadow-indigo-600/30 active:scale-95"
                                >
                                    +
                                </button>
                                <button
                                    @click="counter = 0"
                                    class="text-xs px-3 py-2 rounded-lg bg-slate-800/80 hover:bg-slate-700 text-slate-300 font-medium transition border border-slate-700"
                                >
                                    Reset
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="flex items-center justify-between text-xs text-slate-400 pt-4 border-t border-slate-800">
                        <span class="flex items-center space-x-1.5">
                            <ShieldCheck class="w-4 h-4 text-emerald-400" />
                            <span>Vite HMR & Tailwind v4 aktif</span>
                        </span>
                        <span class="font-mono text-indigo-300">resources/js/Pages/Welcome.vue</span>
                    </div>
                </div>

                <!-- Useful Artisan / NPM Commands -->
                <div class="lg:col-span-6 rounded-2xl bg-gradient-to-b from-slate-900/80 to-slate-900/40 backdrop-blur-xl border border-slate-800 p-6 sm:p-8 flex flex-col justify-between shadow-lg">
                    <div>
                        <div class="flex items-center space-x-3 mb-4">
                            <div class="p-2 rounded-lg bg-indigo-500/10 border border-indigo-500/30 text-indigo-400">
                                <Terminal class="w-5 h-5" />
                            </div>
                            <div>
                                <h3 class="text-lg font-bold text-white">Quick Terminal Commands</h3>
                                <p class="text-xs text-slate-400">Perintah cepat untuk development sehari-hari</p>
                            </div>
                        </div>

                        <div class="space-y-3 mb-4">
                            <div
                                class="p-3 rounded-xl bg-slate-950/80 border border-slate-800 flex items-center justify-between group hover:border-slate-700 transition"
                            >
                                <div class="font-mono text-xs text-slate-300">
                                    <span class="text-indigo-400">$</span> php artisan migrate
                                </div>
                                <button
                                    @click="copyToClipboard('php artisan migrate', 'migrate')"
                                    class="text-xs px-2.5 py-1 rounded bg-slate-800 hover:bg-slate-700 text-slate-300 transition flex items-center space-x-1"
                                >
                                    <component :is="copiedCommand === 'migrate' ? Check : Copy" class="w-3.5 h-3.5" :class="copiedCommand === 'migrate' ? 'text-emerald-400' : ''" />
                                    <span>{{ copiedCommand === 'migrate' ? 'Copied' : 'Copy' }}</span>
                                </button>
                            </div>

                            <div
                                class="p-3 rounded-xl bg-slate-950/80 border border-slate-800 flex items-center justify-between group hover:border-slate-700 transition"
                            >
                                <div class="font-mono text-xs text-slate-300">
                                    <span class="text-indigo-400">$</span> npm run dev
                                </div>
                                <button
                                    @click="copyToClipboard('npm run dev', 'npmdev')"
                                    class="text-xs px-2.5 py-1 rounded bg-slate-800 hover:bg-slate-700 text-slate-300 transition flex items-center space-x-1"
                                >
                                    <component :is="copiedCommand === 'npmdev' ? Check : Copy" class="w-3.5 h-3.5" :class="copiedCommand === 'npmdev' ? 'text-emerald-400' : ''" />
                                    <span>{{ copiedCommand === 'npmdev' ? 'Copied' : 'Copy' }}</span>
                                </button>
                            </div>

                            <div
                                class="p-3 rounded-xl bg-slate-950/80 border border-slate-800 flex items-center justify-between group hover:border-slate-700 transition"
                            >
                                <div class="font-mono text-xs text-slate-300">
                                    <span class="text-indigo-400">$</span> php artisan route:list
                                </div>
                                <button
                                    @click="copyToClipboard('php artisan route:list', 'routes')"
                                    class="text-xs px-2.5 py-1 rounded bg-slate-800 hover:bg-slate-700 text-slate-300 transition flex items-center space-x-1"
                                >
                                    <component :is="copiedCommand === 'routes' ? Check : Copy" class="w-3.5 h-3.5" :class="copiedCommand === 'routes' ? 'text-emerald-400' : ''" />
                                    <span>{{ copiedCommand === 'routes' ? 'Copied' : 'Copy' }}</span>
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="flex items-center justify-between text-xs text-slate-400 pt-3 border-t border-slate-800">
                        <span>Database: <strong class="text-slate-200">{{ dbStatus.database }}</strong></span>
                        <span>Redis Client: <strong class="text-slate-200">{{ redisStatus.client }}</strong></span>
                    </div>
                </div>
            </div>
        </main>

        <!-- Footer -->
        <footer class="w-full border-t border-slate-800/80 bg-slate-950/70 backdrop-blur-xl py-6 z-10">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex flex-col sm:flex-row items-center justify-between gap-4 text-xs text-slate-400">
                <div class="flex items-center space-x-2">
                    <span class="font-semibold text-slate-300">{{ appName }}</span>
                    <span>&bull;</span>
                    <span>Laravel v{{ laravelVersion }} (PHP v{{ phpVersion }})</span>
                </div>
                <div class="flex items-center space-x-4">
                    <a href="https://inertiajs.com" target="_blank" rel="noreferrer" class="hover:text-indigo-400 transition">Inertia.js</a>
                    <a href="https://vuejs.org" target="_blank" rel="noreferrer" class="hover:text-emerald-400 transition">Vue 3</a>
                    <a href="https://tailwindcss.com" target="_blank" rel="noreferrer" class="hover:text-cyan-400 transition">Tailwind CSS</a>
                    <a href="https://laravel.com" target="_blank" rel="noreferrer" class="hover:text-red-400 transition">Laravel</a>
                </div>
            </div>
        </footer>
    </div>
</template>
