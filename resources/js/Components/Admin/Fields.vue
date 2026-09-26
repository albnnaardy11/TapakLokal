<script setup>
import StructuredFields from './StructuredFields.vue';

defineProps({
    fields: Object,
    form: Object,
    disabled: Boolean,
});
</script>

<template>
    <div class="grid gap-6 sm:grid-cols-2">
        <label
            v-for="(field, key) in fields"
            :key="key"
            class="flex min-w-0 flex-col gap-1.5 text-xs font-bold text-slate-700"
            :class="field.type === 'textarea' ? 'sm:col-span-2' : ''"
        >
            <div class="flex items-center justify-between">
                <span>{{ field.label }}</span>
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

            <!-- Select Dropdown -->
            <select
                v-else-if="field.type === 'select'"
                v-model="form[key]"
                :disabled="disabled"
                :required="field.required"
                class="w-full rounded-xl border border-slate-200 bg-white px-3.5 py-2.5 text-xs font-medium text-slate-800 outline-none transition focus:border-blue-500 focus:ring-2 focus:ring-blue-100 disabled:bg-slate-50 disabled:text-slate-400"
            >
                <option disabled value="">Pilih {{ field.label }}</option>
                <option v-for="option in field.options" :key="option" :value="option">
                    {{ option }}
                </option>
            </select>

            <!-- Text / Number / Date / URL Input -->
            <input
                v-else
                v-model="form[key]"
                :type="field.type"
                :disabled="disabled"
                :required="field.required"
                :min="field.type === 'number' ? 0 : undefined"
                class="w-full rounded-xl border border-slate-200 bg-white px-3.5 py-2.5 text-xs font-medium text-slate-800 outline-none transition focus:border-blue-500 focus:ring-2 focus:ring-blue-100 disabled:bg-slate-50 disabled:text-slate-400"
                :placeholder="'Masukkan ' + field.label.toLowerCase()"
            />

            <!-- Validation Error Notice -->
            <span
                v-if="form.errors?.[key]"
                class="text-[11px] font-semibold text-rose-600"
            >
                {{ form.errors[key] }}
            </span>
        </label>
    </div>
</template>
