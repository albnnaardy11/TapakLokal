<script setup>
import { ref, computed, onMounted, onBeforeUnmount } from 'vue';
import {
    TentTree,
    Crown,
    ShoppingBag,
    Check,
    ChevronDown,
    FileText,
    Clock,
    CheckCircle2,
    Archive,
    Sparkles,
    CircleDot,
} from 'lucide-vue-next';

const props = defineProps({
    modelValue: [String, Number],
    options: {
        type: Array,
        default: () => [],
    },
    placeholder: {
        type: String,
        default: 'Pilih opsi…',
    },
    disabled: {
        type: Boolean,
        default: false,
    },
    required: {
        type: Boolean,
        default: false,
    },
});

const emit = defineEmits(['update:modelValue', 'change']);

const isOpen = ref(false);
const dropdownRef = ref(null);

const presetMeta = {
    'open-trip': {
        label: 'Open Trip',
        description: 'Jadwal gabungan publik dengan kuota peserta terbuka',
        icon: TentTree,
        colorClass: 'bg-blue-50 text-[#0088ff] border-blue-200/70',
        badge: 'Tenda & Alam',
    },
    'private-trip': {
        label: 'Private Trip',
        description: 'Perjalanan privat eksklusif khusus rombongan atau keluarga',
        icon: Crown,
        colorClass: 'bg-amber-50 text-amber-600 border-amber-200/70',
        badge: 'Eksklusif',
    },
    'open-po': {
        label: 'Open PO (Oleh-oleh)',
        description: 'Pre-order makanan khas, kriya & cinderamata daerah',
        icon: ShoppingBag,
        colorClass: 'bg-emerald-50 text-emerald-600 border-emerald-200/70',
        badge: 'Oleh-oleh',
    },
    draft: {
        label: 'Draft',
        description: 'Disimpan sebagai konsep, belum tampil ke publik',
        icon: FileText,
        colorClass: 'bg-slate-100 text-slate-600 border-slate-200',
    },
    pending: {
        label: 'Menunggu Review',
        description: 'Menunggu peninjauan dan persetujuan tim admin',
        icon: Clock,
        colorClass: 'bg-amber-50 text-amber-600 border-amber-200',
    },
    published: {
        label: 'Diterbitkan',
        description: 'Aktif dan dapat dipesan oleh pelanggan',
        icon: CheckCircle2,
        colorClass: 'bg-emerald-50 text-emerald-600 border-emerald-200',
    },
    archived: {
        label: 'Diarsipkan',
        description: 'Tidak lagi aktif atau ditampilkan ke publik',
        icon: Archive,
        colorClass: 'bg-slate-100 text-slate-500 border-slate-200',
    },
};

const formatText = (val) => {
    if (!val) return '';
    return String(val)
        .replace(/[-_]/g, ' ')
        .replace(/\b\w/g, (char) => char.toUpperCase());
};

const normalizedOptions = computed(() => {
    return props.options.map((opt) => {
        if (typeof opt === 'object' && opt !== null) {
            const val = opt.value ?? opt.id;
            const meta = presetMeta[val] || {};
            return {
                value: val,
                label: opt.label ?? opt.title ?? meta.label ?? formatText(val),
                description: opt.description ?? meta.description ?? '',
                icon: opt.icon ?? meta.icon ?? CircleDot,
                colorClass: opt.colorClass ?? meta.colorClass ?? 'bg-slate-50 text-slate-600 border-slate-200',
                badge: opt.badge ?? meta.badge ?? '',
            };
        }

        const meta = presetMeta[opt] || {};
        return {
            value: opt,
            label: meta.label || formatText(opt),
            description: meta.description || '',
            icon: meta.icon || CircleDot,
            colorClass: meta.colorClass || 'bg-slate-50 text-slate-600 border-slate-200',
            badge: meta.badge || '',
        };
    });
});

const currentSelected = computed(() => {
    return normalizedOptions.value.find((o) => o.value === props.modelValue);
});

const toggle = () => {
    if (props.disabled) return;
    isOpen.value = !isOpen.value;
};

const select = (option) => {
    if (props.disabled) return;
    emit('update:modelValue', option.value);
    emit('change', option.value);
    isOpen.value = false;
};

const handleClickOutside = (e) => {
    if (dropdownRef.value && !dropdownRef.value.contains(e.target)) {
        isOpen.value = false;
    }
};

const handleKeyDown = (e) => {
    if (e.key === 'Escape') {
        isOpen.value = false;
    }
};

onMounted(() => {
    document.addEventListener('click', handleClickOutside);
    document.addEventListener('keydown', handleKeyDown);
});

onBeforeUnmount(() => {
    document.removeEventListener('click', handleClickOutside);
    document.removeEventListener('keydown', handleKeyDown);
});
</script>

<template>
    <div ref="dropdownRef" class="relative w-full">
        <!-- Trigger Button -->
        <button
            type="button"
            :disabled="disabled"
            class="group flex min-h-[46px] w-full items-center justify-between gap-3 rounded-xl border bg-white px-3.5 py-2 text-left text-xs transition-all duration-150 outline-none cursor-pointer"
            :class="[
                isOpen
                    ? 'border-blue-500 ring-2 ring-blue-100 shadow-sm'
                    : 'border-slate-200 hover:border-blue-300 hover:bg-slate-50/50',
                disabled ? 'cursor-not-allowed bg-slate-50 opacity-60' : '',
            ]"
            @click="toggle"
        >
            <!-- Selected Content -->
            <div class="flex min-w-0 flex-1 items-center gap-2.5">
                <template v-if="currentSelected">
                    <span
                        class="flex size-7 shrink-0 items-center justify-center rounded-lg border text-xs"
                        :class="currentSelected.colorClass"
                    >
                        <component :is="currentSelected.icon" class="size-4" />
                    </span>
                    <div class="min-w-0 flex-1">
                        <span class="block truncate font-bold text-slate-800">
                            {{ currentSelected.label }}
                        </span>
                        <span
                            v-if="currentSelected.description"
                            class="block truncate text-[10px] font-normal text-slate-400"
                        >
                            {{ currentSelected.description }}
                        </span>
                    </div>
                </template>
                <template v-else>
                    <span class="text-xs font-normal text-slate-400">
                        {{ placeholder }}
                    </span>
                </template>
            </div>

            <!-- Trailing Arrow -->
            <ChevronDown
                class="size-4 shrink-0 text-slate-400 transition-transform duration-200 group-hover:text-slate-600"
                :class="isOpen ? 'rotate-180 text-blue-600' : ''"
            />
        </button>

        <!-- Dropdown Menu -->
        <Transition
            enter-active-class="transition duration-150 ease-out"
            enter-from-class="transform scale-95 opacity-0 -translate-y-1"
            enter-to-class="transform scale-100 opacity-100 translate-y-0"
            leave-active-class="transition duration-100 ease-in"
            leave-from-class="transform scale-100 opacity-100 translate-y-0"
            leave-to-class="transform scale-95 opacity-0 -translate-y-1"
        >
            <div
                v-if="isOpen"
                class="absolute left-0 top-full z-50 mt-1.5 w-full min-w-[260px] overflow-hidden rounded-2xl border border-slate-200 bg-white p-1.5 shadow-xl ring-1 ring-black/5"
            >
                <div class="max-h-64 space-y-1 overflow-y-auto">
                    <button
                        v-for="opt in normalizedOptions"
                        :key="opt.value"
                        type="button"
                        class="group flex w-full items-center justify-between gap-3 rounded-xl px-3 py-2.5 text-left text-xs transition-colors duration-150 cursor-pointer"
                        :class="
                            modelValue === opt.value
                                ? 'bg-blue-50/90 text-blue-900 font-bold border border-blue-200/80 shadow-2xs'
                                : 'hover:bg-slate-50 text-slate-700'
                        "
                        @click="select(opt)"
                    >
                        <!-- Icon & Label -->
                        <div class="flex min-w-0 items-center gap-3">
                            <span
                                class="flex size-8 shrink-0 items-center justify-center rounded-xl border text-xs transition group-hover:scale-105"
                                :class="opt.colorClass"
                            >
                                <component :is="opt.icon" class="size-4" />
                            </span>
                            <div class="min-w-0">
                                <div class="flex items-center gap-2">
                                    <span
                                        class="truncate font-bold"
                                        :class="modelValue === opt.value ? 'text-blue-900' : 'text-slate-800'"
                                    >
                                        {{ opt.label }}
                                    </span>
                                    <span
                                        v-if="opt.badge"
                                        class="rounded-md bg-slate-100 px-1.5 py-0.5 text-[9px] font-bold uppercase tracking-wider text-slate-500"
                                    >
                                        {{ opt.badge }}
                                    </span>
                                </div>
                                <p
                                    v-if="opt.description"
                                    class="mt-0.5 line-clamp-1 text-[11px] font-normal leading-relaxed text-slate-500"
                                >
                                    {{ opt.description }}
                                </p>
                            </div>
                        </div>

                        <!-- Checkmark Indicator -->
                        <div
                            v-if="modelValue === opt.value"
                            class="flex size-5 shrink-0 items-center justify-center rounded-full bg-[#0088ff] text-white shadow-xs"
                        >
                            <Check class="size-3 stroke-[3]" />
                        </div>
                    </button>
                </div>
            </div>
        </Transition>
    </div>
</template>
