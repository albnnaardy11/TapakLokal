<script setup>
import { router, usePage } from '@inertiajs/vue3';
import { computed, onBeforeUnmount, onMounted, ref } from 'vue';
import AccessibilityGuideSkeleton from '../Skeletons/AccessibilityGuideSkeleton.vue';
import AccountSkeleton from '../Skeletons/AccountSkeleton.vue';
import BlogSkeleton from '../Skeletons/BlogSkeleton.vue';
import TripDetailSkeleton from '../Skeletons/TripDetailSkeleton.vue';
import WelcomeSkeleton from '../Skeletons/WelcomeSkeleton.vue';

const page = usePage();
const isSlowNavigation = ref(false);
const targetPath = ref('');
let thresholdTimer = null;
let finishTimer = null;

// Threshold before displaying page-level skeleton on slow network (anti-flicker policy)
const NAVIGATION_THRESHOLD_MS = 250;
const FADE_OUT_MS = 150;

const currentSkeletonComponent = computed(() => {
    const currentComponent = page?.component || '';
    const path = targetPath.value || (typeof window !== 'undefined' ? window.location.pathname : '');

    if (currentComponent === 'AccessibilityGuide' || path.startsWith('/panduan-aksesibilitas')) {
        return AccessibilityGuideSkeleton;
    }
    if (currentComponent === 'Blog' || currentComponent === 'BlogDetail' || path.startsWith('/blog') || path.startsWith('/cerita-perjalanan')) {
        return BlogSkeleton;
    }
    if (currentComponent === 'TripDetail' || path.startsWith('/trips') || path.startsWith('/trip')) {
        return TripDetailSkeleton;
    }
    if (currentComponent === 'Account' || path.startsWith('/account') || path.startsWith('/akun')) {
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

    if (thresholdTimer) clearTimeout(thresholdTimer);
    if (finishTimer) clearTimeout(finishTimer);

    // Anti-flicker: only show page skeleton if page transition takes longer than threshold
    thresholdTimer = setTimeout(() => {
        isSlowNavigation.value = true;
    }, NAVIGATION_THRESHOLD_MS);
};

const handleFinish = () => {
    if (thresholdTimer) {
        clearTimeout(thresholdTimer);
        thresholdTimer = null;
    }
    if (finishTimer) {
        clearTimeout(finishTimer);
        finishTimer = null;
    }

    if (isSlowNavigation.value) {
        finishTimer = setTimeout(() => {
            isSlowNavigation.value = false;
            targetPath.value = '';
        }, FADE_OUT_MS);
    } else {
        isSlowNavigation.value = false;
        targetPath.value = '';
    }
};

let removeStartListener = null;
let removeFinishListener = null;
let removeNavigateListener = null;

onMounted(() => {
    removeStartListener = router.on('start', handleStart);
    removeFinishListener = router.on('finish', handleFinish);
    removeNavigateListener = router.on('navigate', handleFinish);
});

onBeforeUnmount(() => {
    if (thresholdTimer) clearTimeout(thresholdTimer);
    if (finishTimer) clearTimeout(finishTimer);
    if (removeStartListener) removeStartListener();
    if (removeFinishListener) removeFinishListener();
    if (removeNavigateListener) removeNavigateListener();
});
</script>

<template>
    <Transition
        enter-active-class="transition-opacity duration-150 ease-out"
        enter-from-class="opacity-0"
        enter-to-class="opacity-100"
        leave-active-class="transition-opacity duration-200 ease-in pointer-events-none"
        leave-from-class="opacity-100"
        leave-to-class="opacity-0"
    >
        <div
            v-if="isSlowNavigation"
            class="fixed inset-0 z-[100] h-screen w-screen overflow-y-auto bg-[#f8fafc] select-none"
            aria-hidden="true"
        >
            <component :is="currentSkeletonComponent" />
        </div>
    </Transition>
</template>
