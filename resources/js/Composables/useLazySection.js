import { onBeforeUnmount, onMounted, ref } from 'vue';

/**
 * Lazy Section Composable using IntersectionObserver.
 * Triggers loading/rendering when section approaches the viewport.
 *
 * @param {Object} options
 * @param {string} [options.rootMargin='200px'] Pre-load margin before entering viewport
 * @param {number} [options.threshold=0.01] Intersection threshold
 * @param {boolean} [options.once=true] Only observe once until triggered
 */
export function useLazySection(options = {}) {
    const {
        rootMargin = '200px',
        threshold = 0.01,
        once = true,
    } = options;

    const targetRef = ref(null);
    const isVisible = ref(false);
    const hasLoaded = ref(false);
    let observer = null;

    onMounted(() => {
        if (typeof window === 'undefined' || !('IntersectionObserver' in window)) {
            isVisible.value = true;
            hasLoaded.value = true;
            return;
        }

        if (!targetRef.value) {
            isVisible.value = true;
            hasLoaded.value = true;
            return;
        }

        observer = new IntersectionObserver((entries) => {
            entries.forEach((entry) => {
                if (entry.isIntersecting) {
                    isVisible.value = true;
                    hasLoaded.value = true;
                    if (once && observer && targetRef.value) {
                        observer.unobserve(targetRef.value);
                    }
                } else if (!once) {
                    isVisible.value = false;
                }
            });
        }, {
            rootMargin,
            threshold,
        });

        observer.observe(targetRef.value);
    });

    onBeforeUnmount(() => {
        if (observer) {
            observer.disconnect();
            observer = null;
        }
    });

    return {
        targetRef,
        isVisible,
        hasLoaded,
    };
}
