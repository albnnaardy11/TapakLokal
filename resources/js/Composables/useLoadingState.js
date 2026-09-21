import { computed, onUnmounted, ref } from 'vue';

/**
 * Production-grade Loading State Composable with Anti-Flicker Threshold Protection.
 *
 * @param {Object} options
 * @param {number} [options.thresholdMs=150] Delay in ms before showing skeleton (prevents flicker on fast responses)
 * @param {number} [options.minDisplayMs=0] Minimum time skeleton stays visible once shown (prevents rapid flash)
 * @param {boolean} [options.initialLoading=false] Initial loading state
 * @param {*} [options.initialData=null] Initial data value
 */
export function useLoadingState(options = {}) {
    const {
        thresholdMs = 150,
        minDisplayMs = 0,
        initialLoading = false,
        initialData = null,
    } = options;

    const isPending = ref(initialLoading);
    const isLoading = ref(initialLoading);
    const data = ref(initialData);
    const error = ref(null);

    let thresholdTimer = null;
    let minDisplayTimer = null;
    let skeletonShownTimestamp = 0;
    let lastAsyncFunction = null;

    const isError = computed(() => !!error.value);
    const isEmpty = computed(() => {
        if (data.value === null || data.value === undefined) return false;
        if (Array.isArray(data.value)) return data.value.length === 0;
        if (typeof data.value === 'object') return Object.keys(data.value).length === 0;
        return false;
    });

    const clearTimers = () => {
        if (thresholdTimer) {
            clearTimeout(thresholdTimer);
            thresholdTimer = null;
        }
        if (minDisplayTimer) {
            clearTimeout(minDisplayTimer);
            minDisplayTimer = null;
        }
    };

    /**
     * Executes an async operation with anti-flicker protection.
     *
     * @param {Function} asyncFn
     * @returns {Promise<*>}
     */
    const execute = async (asyncFn) => {
        if (typeof asyncFn !== 'function') return;

        lastAsyncFunction = asyncFn;
        clearTimers();
        isPending.value = true;
        error.value = null;

        // Start threshold timer: only show skeleton if request takes longer than thresholdMs
        thresholdTimer = setTimeout(() => {
            if (isPending.value) {
                isLoading.value = true;
                skeletonShownTimestamp = Date.now();
            }
        }, thresholdMs);

        try {
            const result = await asyncFn();
            data.value = result;
            return result;
        } catch (err) {
            error.value = err?.message || 'Terjadi kesalahan saat memuat data.';
            throw err;
        } finally {
            isPending.value = false;
            clearTimers();

            // If skeleton was shown, honor minDisplayMs to prevent jarring flash
            if (isLoading.value && minDisplayMs > 0) {
                const elapsed = Date.now() - skeletonShownTimestamp;
                const remaining = Math.max(0, minDisplayMs - elapsed);

                if (remaining > 0) {
                    minDisplayTimer = setTimeout(() => {
                        isLoading.value = false;
                    }, remaining);
                } else {
                    isLoading.value = false;
                }
            } else {
                isLoading.value = false;
            }
        }
    };

    const retry = async () => {
        if (lastAsyncFunction) {
            return execute(lastAsyncFunction);
        }
    };

    const reset = () => {
        clearTimers();
        isPending.value = false;
        isLoading.value = false;
        error.value = null;
        data.value = initialData;
    };

    onUnmounted(() => {
        clearTimers();
    });

    return {
        isLoading,
        isPending,
        isError,
        isEmpty,
        data,
        error,
        execute,
        retry,
        reset,
    };
}
