<script setup>
import { router, usePage } from '@inertiajs/vue3';
import { computed, onMounted, onUnmounted, ref } from 'vue';
import AccessibilityGuideSkeleton from '../Skeletons/AccessibilityGuideSkeleton.vue';
import AccountSkeleton from '../Skeletons/AccountSkeleton.vue';
import BlogSkeleton from '../Skeletons/BlogSkeleton.vue';
import TripDetailSkeleton from '../Skeletons/TripDetailSkeleton.vue';
import WelcomeSkeleton from '../Skeletons/WelcomeSkeleton.vue';

const page = usePage();
// Start with true on initial page load / reload so the skeleton is active during initial mount/hydrate
const isLoading = ref(true);
const targetPath = ref('');
let startTimeout = null;
let finishTimeout = null;

const currentSkeletonComponent = computed(() => {
    const path = targetPath.value || (typeof window !== 'undefined' ? window.location.pathname : '');
    const currentComponent = page?.component || '';

    if (path.includes('/panduan-aksesibilitas') || currentComponent === 'AccessibilityGuide') {
        return AccessibilityGuideSkeleton;
    }
    if (path.includes('/blog') || path.includes('/cerita-perjalanan') || currentComponent === 'Blog' || currentComponent === 'BlogDetail') {
        return BlogSkeleton;
    }
    if (path.includes('/trip') || currentComponent === 'TripDetail') {
        return TripDetailSkeleton;
    }
    if (path.includes('/account') || path.includes('/akun') || currentComponent === 'Account') {
        return AccountSkeleton;
    }
    return WelcomeSkeleton;
});

const handleStart = (event) => {
    try {
        if (event?.detail?.visit?.url) {
            targetPath.value = event.detail.visit.url.pathname || '';
        }
    } catch {
        targetPath.value = '';
    }

    if (startTimeout) clearTimeout(startTimeout);
    if (finishTimeout) clearTimeout(finishTimeout);

    // If navigation takes longer than 30ms (lag / network load), show skeleton
    startTimeout = setTimeout(() => {
        isLoading.value = true;
    }, 30);
};

const handleFinish = () => {
    if (startTimeout) clearTimeout(startTimeout);
    if (finishTimeout) clearTimeout(finishTimeout);

    // Smooth transition when loaded
    finishTimeout = setTimeout(() => {
        isLoading.value = false;
        targetPath.value = '';
    }, 150);
};

let removeStartListener = null;
let removeFinishListener = null;
let removeNavigateListener = null;

onMounted(() => {
    // Initial page load / reload: show skeleton during hydration then smoothly reveal page
    finishTimeout = setTimeout(() => {
        isLoading.value = false;
    }, 280);

    removeStartListener = router.on('start', handleStart);
    removeFinishListener = router.on('finish', handleFinish);
    removeNavigateListener = router.on('navigate', handleFinish);
});

onUnmounted(() => {
    if (startTimeout) clearTimeout(startTimeout);
    if (finishTimeout) clearTimeout(finishTimeout);
    if (removeStartListener) removeStartListener();
    if (removeFinishListener) removeFinishListener();
    if (removeNavigateListener) removeNavigateListener();
});
</script>

<template>
    <Transition
        enter-active-class="transition-opacity duration-200 ease-out"
        enter-from-class="opacity-0"
        enter-to-class="opacity-100"
        leave-active-class="transition-opacity duration-300 ease-in-out"
        leave-from-class="opacity-100"
        leave-to-class="opacity-0"
    >
        <div
            v-if="isLoading"
            class="absolute inset-x-0 top-0 z-[100] min-h-full w-full bg-[#f8fafc] pointer-events-none select-none"
            aria-hidden="true"
        >
            <component :is="currentSkeletonComponent" />
        </div>
    </Transition>
</template>
