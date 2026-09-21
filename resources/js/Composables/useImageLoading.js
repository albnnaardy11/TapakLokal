import { ref } from 'vue';

/**
 * Image Loading Composable.
 * Tracks image loading lifecycle to show placeholders and prevent Cumulative Layout Shift (CLS).
 */
export function useImageLoading() {
    const isLoaded = ref(false);
    const hasError = ref(false);

    const onLoad = () => {
        isLoaded.value = true;
        hasError.value = false;
    };

    const onError = () => {
        hasError.value = true;
        isLoaded.value = false;
    };

    const checkImage = (imgElement) => {
        if (!imgElement) return;
        if (imgElement.complete && imgElement.naturalHeight !== 0) {
            isLoaded.value = true;
            hasError.value = false;
        }
    };

    const reset = () => {
        isLoaded.value = false;
        hasError.value = false;
    };

    return {
        isLoaded,
        hasError,
        onLoad,
        onError,
        checkImage,
        reset,
    };
}
