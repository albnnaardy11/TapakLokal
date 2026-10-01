<script setup>
import { onMounted, onUnmounted, ref, watch } from 'vue';
import { X } from 'lucide-vue-next';

const props = defineProps({
    open: {
        type: Boolean,
        default: false,
    },
    autoDismissMs: {
        type: Number,
        default: 2000,
    },
});

const emit = defineEmits(['close']);

let timer = null;

const startAutoDismiss = () => {
    clearTimeout(timer);
    timer = setTimeout(() => {
        emit('close');
    }, props.autoDismissMs);
};

const stopAutoDismiss = () => {
    clearTimeout(timer);
};

watch(
    () => props.open,
    (isOpen) => {
        if (isOpen) {
            startAutoDismiss();
        } else {
            stopAutoDismiss();
        }
    },
    { immediate: true }
);

onUnmounted(() => {
    stopAutoDismiss();
});
</script>

<template>
    <Teleport to="body">
        <Transition
            enter-active-class="transition duration-200 ease-out"
            enter-from-class="opacity-0 scale-95"
            enter-to-class="opacity-100 scale-100"
            leave-active-class="transition duration-150 ease-in"
            leave-from-class="opacity-100 scale-100"
            leave-to-class="opacity-0 scale-95"
        >
            <div
                v-if="open"
                class="fixed inset-0 z-[150] flex items-center justify-center bg-slate-900/45 p-4 backdrop-blur-xs select-none"
                role="dialog"
                aria-modal="true"
                aria-labelledby="success-modal-title"
                @click.self="emit('close')"
            >
                <!-- 1:1 AUTHENTIC TRAVELOKA SUCCESS MODAL CARD -->
                <div
                    class="relative w-full max-w-[340px] sm:max-w-[360px] overflow-hidden rounded-[24px] bg-white px-7 pt-7 pb-8 text-center shadow-[0_20px_50px_rgba(15,30,65,0.22)] border border-slate-100 transition-all duration-200"
                >
                    <!-- Close button -->
                    <button
                        type="button"
                        class="absolute right-4 top-4 z-20 flex size-7 items-center justify-center rounded-full text-slate-400 hover:bg-slate-100 hover:text-slate-600 transition cursor-pointer"
                        aria-label="Tutup notifikasi"
                        @click="emit('close')"
                    >
                        <X class="size-4 stroke-[2]" />
                    </button>

                    <!-- TRAVELOKA ICONIC SHIELD & ARCHITECTURAL LINE-ART ILLUSTRATION -->
                    <div class="relative mx-auto flex h-[160px] w-full items-center justify-center">
                        <svg viewBox="0 0 320 160" fill="none" xmlns="http://www.w3.org/2000/svg" class="size-full overflow-visible">
                            <!-- Background Subtle Architectural City / Building Sketch (Soft Gray Lineart) -->
                            <g stroke="#cbd5e1" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" opacity="0.85">
                                <!-- Building Left -->
                                <rect x="92" y="24" width="60" height="106" rx="2" fill="white" stroke="#d5dde8" stroke-width="1.5"/>
                                <line x1="102" y1="36" x2="142" y2="36" />
                                <line x1="102" y1="50" x2="142" y2="50" />
                                <line x1="102" y1="64" x2="142" y2="64" />
                                <line x1="102" y1="78" x2="142" y2="78" />
                                <line x1="102" y1="92" x2="142" y2="92" />
                                
                                <!-- Background Center Foliage / Palm Leaves -->
                                <path d="M72 130C70 100 80 82 92 72M92 72C80 68 70 72 65 78M92 72C98 64 108 64 114 68M92 72C94 80 102 88 106 90" stroke="#cbd5e1" stroke-width="1.4"/>
                                <path d="M248 130C250 100 240 82 228 72M228 72C240 68 250 72 255 78M228 72C222 64 212 64 206 68M228 72C226 80 218 88 214 90" stroke="#cbd5e1" stroke-width="1.4"/>
                                
                                <!-- Building Right -->
                                <rect x="168" y="42" width="58" height="88" rx="2" fill="white" stroke="#d5dde8" stroke-width="1.5"/>
                                <line x1="178" y1="56" x2="216" y2="56" />
                                <line x1="178" y1="70" x2="216" y2="70" />
                                <line x1="178" y1="84" x2="216" y2="84" />
                                <line x1="178" y1="98" x2="216" y2="98" />
                            </g>

                            <!-- Baseline Shelf / Horizon Line -->
                            <line x1="48" y1="130" x2="272" y2="130" stroke="#e2e8f0" stroke-width="2.5" stroke-linecap="round"/>
                            <line x1="120" y1="130" x2="200" y2="130" stroke="#003580" stroke-width="3.5" stroke-linecap="round"/>

                            <!-- CENTRAL TRAVELOKA 3D GLOSSY SHIELD -->
                            <g filter="url(#shield-shadow)">
                                <!-- Outer Gradient Shield Frame -->
                                <path
                                    d="M160 30C160 30 196 38 206 50C206 82 190 114 160 126C130 114 114 82 114 50C124 38 160 30 160 30Z"
                                    fill="url(#shield-outer-gradient)"
                                    stroke="#ffffff"
                                    stroke-width="3"
                                    stroke-linejoin="round"
                                />

                                <!-- Inner Glossy Shield Surface -->
                                <path
                                    d="M160 36C160 36 191 43 199 53C199 80 185 107 160 118C135 107 121 80 121 53C129 43 160 36 160 36Z"
                                    fill="url(#shield-inner-gradient)"
                                />

                                <!-- Top-Left Subtle Light Reflection Arc -->
                                <path
                                    d="M160 38C142 42 126 50 124 62C123 78 127 94 135 103C131 93 128 78 130 64C132 54 145 44 160 41Z"
                                    fill="white"
                                    opacity="0.35"
                                />

                                <!-- Bright Emerald Checkmark Badge in Center -->
                                <path
                                    d="M145 76L155 86L176 65"
                                    stroke="#00c853"
                                    stroke-width="7"
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    fill="none"
                                />
                                <path
                                    d="M145 76L155 86L176 65"
                                    stroke="#10b981"
                                    stroke-width="5"
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    fill="none"
                                />
                            </g>

                            <!-- Gradient Definitions -->
                            <defs>
                                <linearGradient id="shield-outer-gradient" x1="160" y1="30" x2="160" y2="126" gradientUnits="userSpaceOnUse">
                                    <stop offset="0%" stop-color="#0284c7" />
                                    <stop offset="100%" stop-color="#0047ba" />
                                </linearGradient>
                                <linearGradient id="shield-inner-gradient" x1="160" y1="36" x2="160" y2="118" gradientUnits="userSpaceOnUse">
                                    <stop offset="0%" stop-color="#0298ea" />
                                    <stop offset="100%" stop-color="#0052cc" />
                                </linearGradient>
                                <filter id="shield-shadow" x="100" y="24" width="120" height="120" filterUnits="userSpaceOnUse" color-interpolation-filters="sRGB">
                                    <feDropShadow dx="0" dy="8" stdDeviation="8" flood-color="#0064d2" flood-opacity="0.28" />
                                </filter>
                            </defs>
                        </svg>
                    </div>

                    <!-- 1:1 TRAVELOKA TYPOGRAPHY (IDENTICAL TO REFERENCE) -->
                    <div class="mt-2">
                        <h2
                            id="success-modal-title"
                            class="text-[23px] sm:text-[25px] font-black tracking-[-0.02em] text-[#1c2938]"
                        >
                            Log In Successful!
                        </h2>
                        
                        <p class="mt-2.5 text-xs sm:text-[13px] font-medium leading-relaxed text-slate-500 max-w-[260px] mx-auto">
                            Welcome back. It's so nice to see you again!
                        </p>
                    </div>
                </div>
            </div>
        </Transition>
    </Teleport>
</template>
