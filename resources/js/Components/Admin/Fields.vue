<script setup>
import StructuredFields from './StructuredFields.vue';
import CustomSelect from './CustomSelect.vue';
import CurrencyInput from './CurrencyInput.vue';
import ImageUpload from './ImageUpload.vue';

defineProps({
    fields: Object,
    form: Object,
    disabled: Boolean,
});
</script>

<template>
    <div class="grid gap-6 sm:grid-cols-2">
        <div
            v-for="(field, key) in fields"
            :key="key"
            class="flex min-w-0 flex-col gap-1.5 text-xs font-bold text-slate-700"
            :class="['textarea', 'structured', 'image'].includes(field.type) || key === 'image_url' ? 'sm:col-span-2' : ''"
        >
            <div class="flex items-center justify-between">
                <span class="flex items-center gap-1.5">
                    {{ field.label }}
                    <span
                        v-if="field.readonly"
                        class="rounded-md bg-slate-100 px-1.5 py-0.5 text-[9px] font-bold uppercase tracking-wider text-slate-500"
                    >
                        Otomatis
                    </span>
                </span>
                <span v-if="field.required" class="text-[10px] font-semibold text-rose-500">Wajib</span>
                <span v-else class="text-[10px] font-normal text-slate-400">Opsional</span>
            </div>

            <!-- Structured Fields Editor -->
            <StructuredFields
                v-if="field.type === 'structured'"
                v-model="form[key]"
                :disabled="disabled"
            />

            <!-- Textarea -->
            <textarea
                v-else-if="field.type === 'textarea'"
                v-model="form[key]"
                :disabled="disabled"
                :required="field.required"
                rows="4"
                class="w-full rounded-xl border border-slate-200 bg-white p-3 text-xs font-medium text-slate-800 outline-none transition focus:border-blue-500 focus:ring-2 focus:ring-blue-100 disabled:bg-slate-50 disabled:text-slate-400"
                :placeholder="'Tulis ' + field.label.toLowerCase() + '…'"
            ></textarea>

            <!-- Custom Styled Select Dropdown -->
            <CustomSelect
                v-else-if="field.type === 'select'"
                v-model="form[key]"
                :options="field.options"
                :placeholder="'Pilih ' + field.label"
                :disabled="disabled"
                :required="field.required"
            />

            <!-- Currency Input (IDR / Rupiah) -->
            <CurrencyInput
                v-else-if="field.type === 'currency' || key === 'price' || field.label.includes('(IDR)')"
                v-model="form[key]"
                :disabled="disabled || field.disabled"
                :readonly="field.readonly"
                :required="field.required"
                :placeholder="field.placeholder || '0'"
            />

            <!-- Image / Photo Uploader (with Client-Side WebP Conversion & Optimization) -->
            <ImageUpload
                v-else-if="field.type === 'image' || key === 'image_url'"
                v-model="form[key]"
                :label="field.label"
                :disabled="disabled || field.disabled"
                :readonly="field.readonly"
                :required="field.required"
            />

            <!-- Text / Number / Date / URL Input -->
            <input
                v-else
                v-model="form[key]"
                :type="field.type"
                :disabled="disabled || field.disabled"
                :readonly="field.readonly"
                :required="field.required"
                :min="field.type === 'number' ? 0 : undefined"
                class="w-full rounded-xl border border-slate-200 bg-white px-3.5 py-2.5 text-xs font-medium text-slate-800 outline-none transition focus:border-blue-500 focus:ring-2 focus:ring-blue-100 disabled:bg-slate-50 disabled:text-slate-400 read-only:bg-slate-50/80 read-only:cursor-not-allowed read-only:text-slate-600 read-only:border-slate-200/90 focus:read-only:border-slate-200 focus:read-only:ring-0"
                :placeholder="field.placeholder || ('Masukkan ' + field.label.toLowerCase())"
            />

            <!-- Validation Error Notice -->
            <span
                v-if="form.errors?.[key]"
                class="text-[11px] font-semibold text-rose-600"
            >
                {{ form.errors[key] }}
            </span>
        </div>
    </div>
</template>
