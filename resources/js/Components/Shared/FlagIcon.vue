<script setup>
defineProps({
    country: {
        type: String,
        required: true,
    },
    customClass: {
        type: String,
        default: 'h-3.5 w-5',
    },
});
</script>

<template>
    <!-- Indonesian Flag -->
    <svg
        v-if="country.toLowerCase() === 'id'"
        :class="['inline-block shrink-0 rounded-[2px] shadow-[0_0_0_1px_rgba(15,44,92,0.15)] overflow-hidden align-middle', customClass]"
        viewBox="0 0 20 14"
        fill="none"
        xmlns="http://www.w3.org/2000/svg"
        aria-label="Bendera Indonesia"
    >
        <rect width="20" height="7" fill="#E11D48" />
        <rect y="7" width="20" height="7" fill="#FFFFFF" />
    </svg>

    <!-- British Flag (UK / English) -->
    <svg
        v-else-if="country.toLowerCase() === 'gb' || country.toLowerCase() === 'uk' || country.toLowerCase() === 'en'"
        :class="['inline-block shrink-0 rounded-[2px] shadow-[0_0_0_1px_rgba(15,44,92,0.15)] overflow-hidden align-middle', customClass]"
        viewBox="0 0 60 42"
        fill="none"
        xmlns="http://www.w3.org/2000/svg"
        aria-label="Bendera Inggris"
    >
        <rect width="60" height="42" fill="#012169" />
        <path d="M0 0L60 42M60 0L0 42" stroke="#FFFFFF" stroke-width="8" />
        <path d="M0 0L60 42M60 0L0 42" stroke="#C8102E" stroke-width="4.5" />
        <path d="M30 0V42M0 21H60" stroke="#FFFFFF" stroke-width="12" />
        <path d="M30 0V42M0 21H60" stroke="#C8102E" stroke-width="7" />
    </svg>

    <!-- US Flag -->
    <svg
        v-else-if="country.toLowerCase() === 'us'"
        :class="['inline-block shrink-0 rounded-[2px] shadow-[0_0_0_1px_rgba(15,44,92,0.15)] overflow-hidden align-middle', customClass]"
        viewBox="0 0 741 390"
        fill="none"
        xmlns="http://www.w3.org/2000/svg"
        aria-label="Bendera Amerika Serikat"
    >
        <rect width="741" height="390" fill="#B22234" />
        <path d="M0 30h741M0 90h741M0 150h741M0 210h741M0 270h741M0 330h741" stroke="#FFFFFF" stroke-width="30" />
        <rect width="296.4" height="210" fill="#3C3B6E" />
    </svg>
</template>
