<script setup>
import { ref, computed, watch } from 'vue';

const props = defineProps({
    modelValue: [Number, String],
    disabled: Boolean,
    readonly: Boolean,
    required: Boolean,
    placeholder: {
        type: String,
        default: '0',
    },
    min: {
        type: Number,
        default: 0,
    },
    max: {
        type: Number,
        default: 100000000000,
    },
});

const emit = defineEmits(['update:modelValue', 'change']);

const formatDisplay = (num) => {
    if (num === null || num === undefined || num === '') return '';
    const clean = String(num).replace(/\D/g, '');
    if (!clean) return '';
    return new Intl.NumberFormat('id-ID').format(Number(clean));
};

const displayValue = ref(formatDisplay(props.modelValue));

watch(
    () => props.modelValue,
    (newVal) => {
        const formatted = formatDisplay(newVal);
        if (formatted !== displayValue.value) {
            displayValue.value = formatted;
        }
    },
    { immediate: true },
);

const onInput = (event) => {
    const raw = event.target.value;
    const cleanDigits = raw.replace(/\D/g, '');

    if (!cleanDigits) {
        displayValue.value = '';
        emit('update:modelValue', 0);
        emit('change', 0);
        return;
    }

    const numValue = Number(cleanDigits);
    displayValue.value = new Intl.NumberFormat('id-ID').format(numValue);
    emit('update:modelValue', numValue);
    emit('change', numValue);
};

const rupiahFormatted = computed(() => {
    const val = Number(props.modelValue);
    if (!val || val <= 0) return null;
    return new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: 'IDR',
        maximumFractionDigits: 0,
    }).format(val);
});
</script>

<template>
    <div class="relative w-full">
        <div class="relative flex items-center">
            <span
                class="pointer-events-none absolute left-3.5 flex items-center text-xs font-bold text-slate-400 select-none"
                :class="disabled ? 'text-slate-300' : ''"
            >
                Rp
            </span>
            <input
                type="text"
                inputmode="numeric"
                :value="displayValue"
                :disabled="disabled"
                :readonly="readonly"
                :required="required"
                :placeholder="placeholder"
                class="w-full rounded-xl border border-slate-200 bg-white py-2.5 pl-10 pr-3.5 text-xs font-semibold text-slate-800 outline-none transition focus:border-blue-500 focus:ring-2 focus:ring-blue-100 disabled:bg-slate-50 disabled:text-slate-400 read-only:bg-slate-50/80 read-only:cursor-not-allowed"
                @input="onInput"
            />
        </div>

        <!-- Live Rupiah Preview Badge -->
        <p
            v-if="rupiahFormatted"
            class="mt-1 flex items-center gap-1.5 text-[11px] font-semibold text-blue-600"
        >
            <span class="text-slate-500 font-medium">Terbaca:</span>
            <span class="rounded-md bg-blue-50 px-2 py-0.5 font-bold text-blue-700 border border-blue-100 shadow-2xs">
                {{ rupiahFormatted }}
            </span>
            <span class="text-slate-400 font-normal">/ peserta</span>
        </p>
    </div>
</template>
