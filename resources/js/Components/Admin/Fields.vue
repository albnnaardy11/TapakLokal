<script setup>
defineProps({ fields: Object, form: Object, disabled: Boolean });
</script>
<template>
    <div class="grid gap-5 sm:grid-cols-2">
        <label v-for="(field, key) in fields" :key="key" class="flex min-w-0 flex-col gap-2 text-xs font-semibold text-slate-600" :class="field.type === 'textarea' ? 'sm:col-span-2' : ''">
            {{ field.label }}{{ field.required ? ' *' : '' }}
            <textarea v-if="field.type === 'textarea'" v-model="form[key]" :disabled="disabled" :required="field.required" rows="5" class="panel-input resize-y"></textarea>
            <select v-else-if="field.type === 'select'" v-model="form[key]" :disabled="disabled" :required="field.required" class="panel-input"><option disabled value="">Pilih</option><option v-for="option in field.options" :key="option" :value="option">{{ option }}</option></select>
            <input v-else v-model="form[key]" :type="field.type" :disabled="disabled" :required="field.required" :min="field.type === 'number' ? 0 : undefined" class="panel-input" />
            <span v-if="form.errors?.[key]" class="text-xs font-normal text-rose-600">{{ form.errors[key] }}</span>
        </label>
    </div>
</template>

