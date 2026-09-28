<script setup>
import { computed, ref, watch } from 'vue';
import {
    ArrowLeft,
    CheckCircle2,
    Lock,
    Mail,
    Phone,
    ShieldCheck,
    Sparkles,
    X,
} from 'lucide-vue-next';

const props = defineProps({
    open: {
        type: Boolean,
        default: false,
    },
    mode: {
        type: String,
        default: 'login', // 'login' | 'register'
    },
    title: {
        type: String,
        default: "Dapatkan komisi & promo menarik!",
    },
    subtitle: {
        type: String,
        default: "Yuk masuk untuk mulai jadi affiliate, dapatkan link komisi hingga 20%, dan cairkan saldo dengan mudah!",
    },
    recentlyUsed: {
        type: String,
        default: 'Recently used',
    },
});

const emit = defineEmits(['close', 'login-success', 'guest-continue']);

const screen = ref('social'); // 'social' | 'credentials' | 'register'
const identifier = ref('');
const password = ref('');
const notice = ref('');
const isLoading = ref(false);

watch(
    () => props.open,
    (isOpen) => {
        if (isOpen) {
            screen.value = 'social';
            identifier.value = '';
            password.value = '';
            notice.value = '';
            isLoading.value = false;
        }
    }
);

const handleSocialLogin = (provider) => {
    isLoading.value = true;
    notice.value = `Menghubungkan ke ${provider}...`;
    setTimeout(() => {
        isLoading.value = false;
        notice.value = `Berhasil masuk dengan ${provider}! Mengalihkan...`;
        setTimeout(() => {
            emit('login-success', { provider });
            emit('close');
        }, 1200);
    }, 900);
};

const handleManualSubmit = () => {
    isLoading.value = true;
    notice.value = 'Memproses autentikasi...';
    setTimeout(() => {
        isLoading.value = false;
        notice.value = 'Berhasil masuk! Selamat datang kembali.';
        setTimeout(() => {
            emit('login-success', { identifier: identifier.value });
            emit('close');
        }, 1200);
    }, 900);
};

const continueAsGuest = () => {
    emit('guest-continue');
    emit('close');
};
</script>

<template>
    <Teleport to="body">
        <Transition
            enter-active-class="transition duration-250 ease-out"
            enter-from-class="opacity-0"
            enter-to-class="opacity-100"
            leave-active-class="transition duration-200 ease-in"
            leave-from-class="opacity-100"
            leave-to-class="opacity-0"
        >
            <div
                v-if="open"
                class="fixed inset-0 z-[100] flex items-center justify-center bg-slate-900/60 p-4 backdrop-blur-xs select-none"
                role="dialog"
                aria-modal="true"
                aria-labelledby="auth-modal-title"
                @click.self="emit('close')"
            >
                <div
                    class="relative w-full max-w-[440px] overflow-hidden rounded-[28px] sm:rounded-[32px] bg-white shadow-[0_24px_60px_rgba(15,23,42,0.25)] border border-slate-100 transition-all transform duration-300"
                >
                    <!-- Close Button -->
                    <button
                        type="button"
                        class="absolute right-4 top-4 z-30 flex size-8 items-center justify-center rounded-full bg-white/80 text-slate-400 hover:bg-white hover:text-slate-700 shadow-xs backdrop-blur-xs transition cursor-pointer"
                        aria-label="Tutup modal"
                        @click="emit('close')"
                    >
                        <X class="size-4.5 stroke-[2.5]" />
                    </button>

                    <!-- SCREEN 1: 1:1 TRAVELOKA-STYLE SOCIAL SELECTION -->
                    <template v-if="screen === 'social'">
                        <!-- Header Banner with Sky Gradient & Travel Vector Illustration -->
                        <div class="relative overflow-hidden bg-gradient-to-b from-[#ddf1ff] via-[#edf7ff] to-white px-6 pt-7 pb-4 sm:px-8">
                            <!-- Background Subtle Circles -->
                            <div class="pointer-events-none absolute -right-6 -top-6 size-36 rounded-full bg-white/60 blur-xs"></div>
                            
                            <div class="flex items-start justify-between gap-4">
                                <div class="max-w-[230px] sm:max-w-[250px]">
                                    <h2
                                        id="auth-modal-title"
                                        class="text-xl sm:text-[23px] font-black tracking-tight text-[#0f172a] leading-[1.2]"
                                    >
                                        {{ title }}
                                    </h2>
                                </div>

                                <!-- Travel Illustration Graphic (Mountain & Traveler Backpack) -->
                                <div class="relative shrink-0 w-24 sm:w-28 h-20">
                                    <svg viewBox="0 0 140 110" fill="none" xmlns="http://www.w3.org/2000/svg" class="size-full">
                                        <!-- Clouds -->
                                        <path d="M20 28C20 23.58 23.58 20 28 20C29.6 20 31.08 20.47 32.33 21.3C33.8 17.58 37.4 15 41.6 15C46.9 15 51.2 19.3 51.2 24.6C51.2 25.1 51.15 25.6 51.05 26.1C53.3 26.6 55 28.6 55 31C55 33.76 52.76 36 50 36H28C23.58 36 20 32.42 20 28Z" fill="white" fill-opacity="0.8"/>
                                        <!-- Mountains -->
                                        <path d="M10 90L45 42L72 90H10Z" fill="#93c5fd" fill-opacity="0.6"/>
                                        <path d="M45 42L54 55L45 59L36 55L45 42Z" fill="white"/>
                                        <path d="M42 90L78 32L112 90H42Z" fill="#60a5fa" fill-opacity="0.7"/>
                                        <path d="M78 32L88 48L78 53L68 48L78 32Z" fill="white"/>
                                        
                                        <!-- Phone Frame / Portal -->
                                        <rect x="75" y="10" width="46" height="78" rx="8" fill="#0284c7" />
                                        <rect x="78" y="14" width="40" height="70" rx="5" fill="#f0f9ff" />
                                        
                                        <!-- Ticket / Badge in Phone -->
                                        <rect x="82" y="24" width="32" height="18" rx="4" fill="#e0f2fe" stroke="#38bdf8" stroke-width="1.5" stroke-dasharray="3 2"/>
                                        <circle cx="89" cy="33" r="3" fill="#0284c7"/>
                                        <rect x="95" y="30" width="14" height="2.5" rx="1" fill="#0284c7"/>
                                        <rect x="95" y="34" width="9" height="2" rx="1" fill="#94a3b8"/>
                                        
                                        <!-- Character Climbing/Jumping into Phone -->
                                        <!-- Head & Cap -->
                                        <circle cx="70" cy="36" r="6" fill="#fbcfe8"/>
                                        <path d="M64 34C64 31 67 30 71 30C75 30 77 32 78 34H64Z" fill="#10b981"/>
                                        <!-- Hoodie Green -->
                                        <path d="M64 42C64 40 66 38 70 38C74 38 76 40 76 42L78 56H62L64 42Z" fill="#22c55e"/>
                                        <!-- Backpack Orange -->
                                        <path d="M57 44C57 41.8 58.8 40 61 40H63V52H61C58.8 52 57 50.2 57 48V44Z" fill="#f97316"/>
                                        <!-- Arms outstretched to phone -->
                                        <path d="M74 42L86 34" stroke="#22c55e" stroke-width="3.5" stroke-linecap="round"/>
                                        <!-- Legs jumping -->
                                        <path d="M66 56L60 68" stroke="#0284c7" stroke-width="4" stroke-linecap="round"/>
                                        <path d="M74 56L84 66" stroke="#0284c7" stroke-width="4" stroke-linecap="round"/>
                                        <!-- Orange Shoes -->
                                        <ellipse cx="57" cy="70" rx="4" ry="2.5" fill="#f97316"/>
                                        <ellipse cx="87" cy="68" rx="4.5" ry="2.5" fill="#f97316"/>
                                    </svg>
                                </div>
                            </div>
                        </div>

                        <!-- Modal Body Actions -->
                        <div class="px-6 pb-6 pt-2 sm:px-8 sm:pb-8">
                            <!-- Google login -->
                            <div class="relative mt-2">
                                <div v-if="recentlyUsed" class="pointer-events-none absolute -top-[5px] -right-[9px] z-20 select-none">
                                    <div class="relative flex h-[25px] items-center whitespace-nowrap rounded-t-[8px] rounded-bl-[8px] bg-[#ff3d80] px-[10px] font-[Arial,sans-serif] text-[12px] leading-none font-bold text-white">
                                        <span>{{ recentlyUsed }}</span>
                                        <svg class="absolute top-full right-0 size-2.5 pointer-events-none" viewBox="0 0 10 10" fill="none" aria-hidden="true">
                                            <polygon points="0,0 10,0 0,10" fill="#a91850" />
                                        </svg>
                                    </div>
                                </div>

                                <button
                                    type="button"
                                    class="relative flex h-[50px] w-full cursor-pointer items-center justify-center rounded-full border border-[#eeeeee] bg-white px-[72px] font-[Arial,sans-serif] text-[17px] leading-5 font-bold text-[#303030] shadow-[0_2px_12px_rgba(0,0,0,0.04)] transition-colors duration-200 hover:bg-[#fafafa] focus-visible:outline-2 focus-visible:outline-offset-4 focus-visible:outline-[#4285f4] disabled:cursor-wait"
                                    :disabled="isLoading"
                                    @click="handleSocialLogin('Google')"
                                >
                                    <!-- Left Google Icon & Divider -->
                                    <span class="absolute inset-y-0 left-0 flex w-[66px] items-center justify-center" aria-hidden="true">
                                        <svg class="size-[22px] shrink-0" viewBox="0 0 24 24">
                                            <path
                                                fill="#4285F4"
                                                d="M23.745 12.27c0-.7-.06-1.4-.19-2.07H12v4.51h6.6c-.29 1.52-1.14 2.82-2.4 3.68v3.05h3.88c2.27-2.09 3.665-5.17 3.665-9.17Z"
                                            />
                                            <path
                                                fill="#34A853"
                                                d="M12 24c3.24 0 5.95-1.08 7.93-2.91l-3.88-3.05c-1.08.72-2.45 1.16-4.05 1.16-3.12 0-5.77-2.1-6.72-4.93H1.25v3.15C3.26 21.36 7.33 24 12 24Z"
                                            />
                                            <path
                                                fill="#FBBC05"
                                                d="M5.28 14.27c-.25-.72-.38-1.49-.38-2.27s.13-1.55.38-2.27V6.58H1.25C.45 8.18 0 10.04 0 12s.45 3.82 1.25 5.42l4.03-3.15Z"
                                            />
                                            <path
                                                fill="#EA4335"
                                                d="M12 4.75c1.77 0 3.35.61 4.6 1.8l3.42-3.42C17.95 1.19 15.24 0 12 0 7.33 0 3.26 2.64 1.25 6.58l4.03 3.15c.95-2.83 3.6-4.98 6.72-4.98Z"
                                            />
                                        </svg>
                                        <span class="absolute right-0 h-[30px] w-px bg-[#f1f1f1]"></span>
                                    </span>

                                    <!-- Centered Google Label -->
                                    <span>Google</span>
                                </button>
                            </div>

                            <!-- 2. NO. HP / EMAIL & FACEBOOK BUTTONS (2 Columns) -->
                            <div class="mt-3.5 grid grid-cols-2 gap-3">
                                <!-- No. HP / Email Button -->
                                <button
                                    type="button"
                                    class="flex min-h-[48px] items-center justify-center gap-2 rounded-full border border-slate-200 bg-white px-3 text-sm font-bold text-slate-800 shadow-xs transition-all duration-200 hover:border-slate-300 hover:bg-slate-50/90 active:scale-[0.99] cursor-pointer"
                                    :disabled="isLoading"
                                    @click="screen = 'credentials'"
                                >
                                    <!-- Phone/Mail Icon -->
                                    <Phone class="size-4 text-[#0088ff] shrink-0" />
                                    <span>No. HP / Email</span>
                                </button>

                                <!-- Facebook Button -->
                                <button
                                    type="button"
                                    class="flex min-h-[48px] items-center justify-center gap-2 rounded-full border border-slate-200 bg-white px-3 text-sm font-bold text-slate-800 shadow-xs transition-all duration-200 hover:border-slate-300 hover:bg-slate-50/90 active:scale-[0.99] cursor-pointer"
                                    :disabled="isLoading"
                                    @click="handleSocialLogin('Facebook')"
                                >
                                    <!-- Facebook Official Blue Logo -->
                                    <svg class="size-4.5 shrink-0" viewBox="0 0 24 24">
                                        <circle cx="12" cy="12" r="12" fill="#1877F2"/>
                                        <path
                                            d="M15.12 12.445l.405-2.64h-2.532V8.09c0-.726.355-1.433 1.496-1.433h1.157V4.41s-1.05-.18-2.054-.18c-2.096 0-3.465 1.27-3.465 3.57v1.995H7.817v2.64h2.31V19.5c.463.073.936.111 1.417.111s.954-.038 1.417-.111v-7.055h2.16z"
                                            fill="#FFFFFF"
                                        />
                                    </svg>
                                    <span>Facebook</span>
                                </button>
                            </div>

                            <!-- 3. OTHER OPTIONS LINK -->
                            <div class="mt-5 text-center">
                                <button
                                    type="button"
                                    class="text-sm font-extrabold text-[#0088ff] hover:text-[#0066cc] transition cursor-pointer"
                                    @click="screen = 'credentials'"
                                >
                                    Pilihan lainnya
                                </button>

                                <p class="mt-2 text-xs font-medium text-slate-500 leading-relaxed max-w-xs mx-auto">
                                    Harga lebih hemat dan komisi melimpah menunggumu. Buka semua fiturnya dengan masuk!
                                </p>
                            </div>

                            <!-- Status Notice / Feedback -->
                            <div
                                v-if="notice"
                                class="mt-4 flex items-center justify-center gap-2 rounded-xl bg-blue-50 border border-blue-200 px-3.5 py-2.5 text-xs font-semibold text-blue-700 animate-fade-in"
                            >
                                <Sparkles class="size-3.5 shrink-0 animate-spin text-[#0088ff]" />
                                <span>{{ notice }}</span>
                            </div>

                            <!-- 4. TERMS & PRIVACY NOTICE -->
                            <p class="mt-6 text-center text-[11px] leading-relaxed text-slate-400 px-2">
                                Dengan melanjutkan, kamu menyetujui
                                <a href="/bantuan" class="font-bold text-[#0088ff] hover:underline">Syarat & Ketentuan</a>
                                dan telah membaca
                                <a href="/bantuan" class="font-bold text-[#0088ff] hover:underline">Pemberitahuan Privasi</a>
                                TapakLokal.
                            </p>

                            <!-- 5. BROWSE AS A GUEST / GUEST ACTION -->
                            <div class="mt-6 pt-4 border-t border-slate-100 text-center">
                                <button
                                    type="button"
                                    class="text-sm font-bold text-[#0088ff] hover:text-[#0066cc] hover:underline transition cursor-pointer"
                                    @click="continueAsGuest"
                                >
                                    Lanjutkan sebagai tamu
                                </button>
                            </div>
                        </div>
                    </template>

                    <!-- SCREEN 2: EMAIL / PHONE NUMBER FORM -->
                    <template v-else>
                        <!-- Header with Back Button -->
                        <div class="flex items-center gap-3 border-b border-slate-100 px-5 py-4 sm:px-6">
                            <button
                                type="button"
                                class="flex size-9 items-center justify-center rounded-full text-slate-600 hover:bg-slate-100 transition cursor-pointer"
                                aria-label="Kembali"
                                @click="screen = 'social'; notice = ''"
                            >
                                <ArrowLeft class="size-5" />
                            </button>
                            <div>
                                <p class="text-[10px] font-bold uppercase tracking-[0.14em] text-[#0088ff]">
                                    TAPAKLOKAL
                                </p>
                                <h2 class="text-base font-extrabold text-[#0f172a]">
                                    Masuk dengan Email / No HP
                                </h2>
                            </div>
                        </div>

                        <form class="p-6 sm:p-8" @submit.prevent="handleManualSubmit">
                            <p class="text-xs sm:text-sm text-slate-600 leading-relaxed">
                                Masukkan email atau nomor WhatsApp yang terdaftar untuk mengakses panel affiliate kamu.
                            </p>

                            <!-- Input Identifier -->
                            <div class="mt-5">
                                <label class="block text-xs font-bold text-slate-700">
                                    Nomor HP atau Email
                                </label>
                                <div class="mt-1.5 flex min-h-[46px] items-center gap-2.5 rounded-xl border border-slate-200 bg-white px-3.5 transition focus-within:border-[#0088ff] focus-within:ring-2 focus-within:ring-[#0088ff]/15">
                                    <Phone v-if="!identifier.includes('@')" class="size-4 text-[#0088ff] shrink-0" />
                                    <Mail v-else class="size-4 text-[#0088ff] shrink-0" />
                                    <input
                                        v-model="identifier"
                                        type="text"
                                        required
                                        placeholder="0812xxxx atau email@domain.com"
                                        class="min-w-0 flex-1 bg-transparent text-sm text-slate-800 outline-none placeholder:text-slate-400 font-medium"
                                    />
                                </div>
                            </div>

                            <!-- Input Password -->
                            <div class="mt-4">
                                <label class="block text-xs font-bold text-slate-700">
                                    Kata Sandi
                                </label>
                                <div class="mt-1.5 flex min-h-[46px] items-center gap-2.5 rounded-xl border border-slate-200 bg-white px-3.5 transition focus-within:border-[#0088ff] focus-within:ring-2 focus-within:ring-[#0088ff]/15">
                                    <Lock class="size-4 text-[#0088ff] shrink-0" />
                                    <input
                                        v-model="password"
                                        type="password"
                                        required
                                        placeholder="Masukkan kata sandi akun"
                                        class="min-w-0 flex-1 bg-transparent text-sm text-slate-800 outline-none placeholder:text-slate-400 font-medium"
                                    />
                                </div>
                            </div>

                            <!-- Submit Button -->
                            <button
                                type="submit"
                                class="mt-6 flex min-h-[48px] w-full items-center justify-center gap-2 rounded-full bg-[#0088ff] hover:bg-[#0074d9] px-6 text-sm font-bold text-white shadow-[0_8px_20px_rgba(0,136,255,0.25)] transition-all duration-200 hover:-translate-y-0.5 active:scale-[0.99] cursor-pointer disabled:opacity-60"
                                :disabled="isLoading"
                            >
                                <span v-if="!isLoading">Masuk Sekarang</span>
                                <span v-else class="inline-flex items-center gap-2">
                                    <Sparkles class="size-4 animate-spin" />
                                    Memproses...
                                </span>
                            </button>

                            <!-- Notice Feedback -->
                            <p
                                v-if="notice"
                                class="mt-4 flex items-center justify-center gap-2 rounded-xl bg-emerald-50 border border-emerald-200 px-3.5 py-2.5 text-xs font-semibold text-emerald-700"
                            >
                                <CheckCircle2 class="size-4 shrink-0" />
                                <span>{{ notice }}</span>
                            </p>

                            <!-- Toggle Back to Social -->
                            <div class="mt-6 pt-4 border-t border-slate-100 text-center">
                                <button
                                    type="button"
                                    class="text-xs sm:text-sm font-bold text-[#0088ff] hover:underline cursor-pointer"
                                    @click="screen = 'social'"
                                >
                                    Kembali ke pilihan login cepat
                                </button>
                            </div>
                        </form>
                    </template>
                </div>
            </div>
        </Transition>
    </Teleport>
</template>
