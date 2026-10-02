<script setup>
import { computed, nextTick, onMounted, onUnmounted, ref } from 'vue';
import { ChevronDown, Check } from 'lucide-vue-next';

const props = defineProps({
    modelValue: {
        type: [String, Number],
        default: '',
    },
    options: {
        type: Array,
        required: true,
    },
    placeholder: {
        type: String,
        default: 'Pilih salah satu',
    },
    icon: {
        type: Object,
        default: null,
    },
    required: {
        type: Boolean,
        default: false,
    },
    disabled: {
        type: Boolean,
        default: false,
    },
});

const emit = defineEmits(['update:modelValue']);

const isOpen = ref(false);
const openUpward = ref(false);
const selectRef = ref(null);

const normalizedOptions = computed(() => {
    return props.options.map(opt => {
        if (typeof opt === 'object' && opt !== null) {
            return { value: opt.value, label: opt.label ?? opt.value };
        }
        return { value: opt, label: opt };
    });
});

const selectedOption = computed(() => {
    return normalizedOptions.value.find(opt => opt.value === props.modelValue);
});

function calculatePosition() {
    if (!selectRef.value) return;
    const rect = selectRef.value.getBoundingClientRect();
    const spaceBelow = window.innerHeight - rect.bottom;
    const spaceAbove = rect.top;
    // If space below is less than 220px and above has more space, open upwards
    openUpward.value = spaceBelow < 220 && spaceAbove > spaceBelow;
}

function toggle() {
    if (props.disabled) return;
    if (!isOpen.value) {
        calculatePosition();
    }
    isOpen.value = !isOpen.value;
}

function select(option) {
    emit('update:modelValue', option.value);
    isOpen.value = false;
}

function handleClickOutside(event) {
    if (selectRef.value && !selectRef.value.contains(event.target)) {
        isOpen.value = false;
    }
}

function handleKeydown(event) {
    if (event.key === 'Escape') {
        isOpen.value = false;
    }
}

onMounted(() => {
    document.addEventListener('click', handleClickOutside);
    document.addEventListener('keydown', handleKeydown);
    window.addEventListener('resize', calculatePosition);
    window.addEventListener('scroll', calculatePosition, true);
});

onUnmounted(() => {
    document.removeEventListener('click', handleClickOutside);
    document.removeEventListener('keydown', handleKeydown);
    window.removeEventListener('resize', calculatePosition);
    window.removeEventListener('scroll', calculatePosition, true);
});
</script>

<template>
    <div ref="selectRef" class="custom-select-container relative w-full">
        <!-- Trigger Button -->
        <button
            type="button"
            :disabled="disabled"
            :aria-expanded="isOpen"
            class="custom-select-trigger group flex min-h-[38px] w-full items-center justify-between gap-2.5 rounded-xl border border-[#dfe8f2] bg-[#fbfdff] px-3 py-1 text-left transition-all duration-150 hover:border-[#b8d4f0] focus:border-[#0088ff] focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#0088ff]/10 disabled:cursor-not-allowed disabled:opacity-60"
            :class="{ '!border-[#0088ff] !bg-white ring-2 ring-[#0088ff]/10 shadow-[0_2px_8px_rgba(0,136,255,.08)]': isOpen }"
            @click="toggle"
        >
            <div class="flex min-w-0 flex-1 items-center gap-2.5">
                <component
                    :is="icon"
                    v-if="icon"
                    class="size-4 shrink-0 transition-colors"
                    :class="isOpen ? 'text-[#0088ff]' : 'text-[#8daac4] group-hover:text-[#5a8bb8]'"
                />
                <span
                    class="truncate text-[12.5px] leading-tight"
                    :class="selectedOption ? 'font-medium text-[#173451]' : 'text-[#a1b1c5]'"
                >
                    {{ selectedOption ? selectedOption.label : placeholder }}
                </span>
            </div>

            <ChevronDown
                class="size-3.5 shrink-0 text-[#8daac4] transition-transform duration-200 ease-out group-hover:text-[#0088ff]"
                :class="{ 'rotate-180 text-[#0088ff]': isOpen }"
            />
        </button>

        <!-- Hidden input to satisfy native form validation if required -->
        <input
            type="text"
            :value="modelValue"
            :required="required"
            tabindex="-1"
            class="pointer-events-none absolute bottom-0 left-1/2 h-0 w-0 -translate-x-1/2 opacity-0"
            aria-hidden="true"
        />

        <!-- Dropdown Menu -->
        <Transition
            enter-active-class="transition duration-150 ease-out"
            :enter-from-class="openUpward ? 'opacity-0 translate-y-1 scale-[0.98]' : 'opacity-0 -translate-y-1 scale-[0.98]'"
            enter-to-class="opacity-100 translate-y-0 scale-100"
            leave-active-class="transition duration-100 ease-in"
            leave-from-class="opacity-100 translate-y-0 scale-100"
            :leave-to-class="openUpward ? 'opacity-0 translate-y-1 scale-[0.98]' : 'opacity-0 -translate-y-1 scale-[0.98]'"
        >
            <div
                v-if="isOpen"
                class="custom-select-menu absolute left-0 z-50 max-h-52 w-full overflow-y-auto rounded-2xl border border-[#e2edf8] bg-white p-1.5 shadow-[0_12px_36px_-8px_rgba(20,55,90,.18)] ring-1 ring-black/5"
                :class="openUpward ? 'bottom-full mb-1.5' : 'top-full mt-1.5'"
            >
                <div class="space-y-0.5 pr-0.5">
                    <button
                        v-for="option in normalizedOptions"
                        :key="option.value"
                        type="button"
                        class="flex w-full items-center justify-between gap-2 rounded-xl px-3 py-2 text-left text-[12.5px] font-normal transition-all duration-150"
                        :class="
                            modelValue === option.value
                                ? 'bg-[#f0f7ff] font-semibold text-[#0088ff]'
                                : 'text-[#2a4563] hover:bg-[#f4f9fd] hover:text-[#0088ff]'
                        "
                        @click="select(option)"
                    >
                        <span class="truncate">{{ option.label }}</span>
                        <Check
                            v-if="modelValue === option.value"
                            class="size-3.5 shrink-0 text-[#0088ff]"
                        />
                    </button>
                </div>
            </div>
        </Transition>
    </div>
</template>

<style scoped>
.custom-select-menu {
    scrollbar-width: thin;
    scrollbar-color: #cbd5e1 transparent;
    -webkit-overflow-scrolling: touch;
}
.custom-select-menu::-webkit-scrollbar {
    width: 4px;
}
.custom-select-menu::-webkit-scrollbar-track {
    background: transparent;
    margin: 4px 0;
}
.custom-select-menu::-webkit-scrollbar-thumb {
    background: #cbd5e1;
    border-radius: 9999px;
}
.custom-select-menu::-webkit-scrollbar-thumb:hover {
    background: #94a3b8;
}
</style>
