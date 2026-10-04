<script setup>
import { ref, watch, onMounted, onBeforeUnmount } from 'vue';
import { usePage } from '@inertiajs/vue3';
import {
    CheckCircle2,
    XCircle,
    AlertTriangle,
    Info,
    X,
} from 'lucide-vue-next';

const page = usePage();
const toasts = ref([]);

const typeConfig = {
    success: {
        icon: CheckCircle2,
        iconContainerClass: 'bg-emerald-50 text-emerald-600 border-emerald-200/70',
        progressClass: 'bg-emerald-500',
        borderClass: 'border-slate-200/90 hover:border-emerald-200',
        defaultTitle: 'Berhasil',
    },
    danger: {
        icon: XCircle,
        iconContainerClass: 'bg-rose-50 text-rose-600 border-rose-200/70',
        progressClass: 'bg-rose-500',
        borderClass: 'border-slate-200/90 hover:border-rose-200',
        defaultTitle: 'Terjadi Kesalahan',
    },
    warning: {
        icon: AlertTriangle,
        iconContainerClass: 'bg-amber-50 text-amber-600 border-amber-200/70',
        progressClass: 'bg-amber-500',
        borderClass: 'border-slate-200/90 hover:border-amber-200',
        defaultTitle: 'Peringatan',
    },
    info: {
        icon: Info,
        iconContainerClass: 'bg-blue-50 text-[#0088ff] border-blue-200/70',
        progressClass: 'bg-[#0088ff]',
        borderClass: 'border-slate-200/90 hover:border-blue-200',
        defaultTitle: 'Informasi',
    },
};

let lastMessage = '';
let lastTime = 0;

const removeToast = (id) => {
    toasts.value = toasts.value.filter((t) => t.id !== id);
};

const addToast = ({ type = 'success', title, message, duration = 4500 }) => {
    if (!message) return;

    // Normalize type
    if (type === 'error') type = 'danger';
    if (!typeConfig[type]) type = 'info';

    // Prevent identical rapid duplicate toasts
    const now = Date.now();
    if (lastMessage === message && now - lastTime < 800) {
        return;
    }
    lastMessage = message;
    lastTime = now;

    // Detect contextual title if default
    let resolvedTitle = title;
    if (!resolvedTitle) {
        if (type === 'success') {
            if (/hapus|deleted/i.test(message)) {
                resolvedTitle = 'Berhasil Dihapus';
            } else if (/simpan|created|dibuat|tersimpan/i.test(message)) {
                resolvedTitle = 'Berhasil Disimpan';
            } else if (/perbarui|updated|diperbarui/i.test(message)) {
                resolvedTitle = 'Berhasil Diperbarui';
            } else {
                resolvedTitle = typeConfig.success.defaultTitle;
            }
        } else {
            resolvedTitle = typeConfig[type].defaultTitle;
        }
    }

    const id = Date.now() + '-' + Math.random().toString(36).slice(2, 7);

    const toastItem = {
        id,
        type,
        title: resolvedTitle,
        message,
        duration,
        progress: 100,
        paused: false,
        startTime: Date.now(),
        remaining: duration,
    };

    toasts.value.unshift(toastItem);

    // Limit maximum stacked toasts to 5
    if (toasts.value.length > 5) {
        toasts.value.pop();
    }

    // High precision progress timer (Filament style)
    const interval = 40;
    const timer = setInterval(() => {
        const item = toasts.value.find((t) => t.id === id);
        if (!item) {
            clearInterval(timer);
            return;
        }

        if (!item.paused) {
            item.remaining -= interval;
            item.progress = Math.max(0, (item.remaining / item.duration) * 100);

            if (item.remaining <= 0) {
                clearInterval(timer);
                removeToast(id);
            }
        }
    }, interval);
};

// Auto-listen to Inertia Flash Messages
watch(
    () => page.props.flash,
    (flash) => {
        if (!flash) return;
        if (flash.success) {
            addToast({ type: 'success', message: flash.success });
        }
        if (flash.error) {
            addToast({ type: 'danger', message: flash.error });
        }
        if (flash.warning) {
            addToast({ type: 'warning', message: flash.warning });
        }
        if (flash.info) {
            addToast({ type: 'info', message: flash.info });
        }
    },
    { deep: true, immediate: true },
);

// Listen to custom event for programmatic toast dispatching
const handleCustomToast = (event) => {
    if (event?.detail) {
        addToast(event.detail);
    }
};

onMounted(() => {
    window.addEventListener('app-toast', handleCustomToast);
});

onBeforeUnmount(() => {
    window.removeEventListener('app-toast', handleCustomToast);
});
</script>

<template>
    <!-- Fixed Top-Right Toast Container (Filament Position) -->
    <div
        aria-live="polite"
        class="fixed top-5 right-5 z-[999999] flex flex-col gap-2.5 w-[calc(100%-2.5rem)] max-w-sm pointer-events-none sm:top-6 sm:right-6"
    >
        <TransitionGroup
            enter-active-class="transform ease-out duration-300 transition"
            enter-from-class="translate-x-8 opacity-0 scale-95"
            enter-to-class="translate-x-0 opacity-100 scale-100"
            leave-active-class="transition ease-in duration-200"
            leave-from-class="opacity-100 scale-100"
            leave-to-class="translate-x-8 opacity-0 scale-95"
            move-class="transition ease-in-out duration-300"
        >
            <div
                v-for="item in toasts"
                :key="item.id"
                role="status"
                class="pointer-events-auto relative overflow-hidden rounded-2xl border bg-white p-4 shadow-[0_12px_36px_rgba(0,0,0,0.14)] ring-1 ring-black/5 transition-all duration-200 select-none"
                :class="typeConfig[item.type]?.borderClass"
                @mouseenter="item.paused = true"
                @mouseleave="item.paused = false"
            >
                <div class="flex items-start gap-3">
                    <!-- Filament-style Rounded Icon Container -->
                    <div
                        class="flex size-8 shrink-0 items-center justify-center rounded-xl border text-xs"
                        :class="typeConfig[item.type]?.iconContainerClass"
                    >
                        <component :is="typeConfig[item.type]?.icon" class="size-4.5 stroke-[2.2]" />
                    </div>

                    <!-- Notification Content -->
                    <div class="flex-1 min-w-0 pt-0.5 pr-1">
                        <h4 class="text-xs font-bold text-slate-800">
                            {{ item.title }}
                        </h4>
                        <p class="mt-0.5 text-xs font-medium text-slate-600 leading-relaxed break-words">
                            {{ item.message }}
                        </p>
                    </div>

                    <!-- Close Button -->
                    <button
                        type="button"
                        class="flex size-6 shrink-0 items-center justify-center rounded-lg text-slate-400 hover:bg-slate-100 hover:text-slate-600 transition"
                        title="Tutup notifikasi"
                        @click="removeToast(item.id)"
                    >
                        <X class="size-3.5 stroke-[2.5]" />
                    </button>
                </div>

                <!-- Filament Countdown Progress Bar -->
                <div class="absolute bottom-0 left-0 right-0 h-1 bg-slate-100/80">
                    <div
                        class="h-full transition-all duration-75 ease-linear"
                        :class="typeConfig[item.type]?.progressClass"
                        :style="{ width: `${item.progress}%` }"
                    />
                </div>
            </div>
        </TransitionGroup>
    </div>
</template>
