<script setup>
import { ArrowUp, ArrowLeftRight, BookOpen, Contrast, Droplet, ImageOff, Link2, Mouse, PauseCircle, RotateCcw, ScanLine, Settings2, Square, ALargeSmall, MoveVertical, Volume2, X } from 'lucide-vue-next';
import { Link, usePage } from '@inertiajs/vue3';
import { route } from 'ziggy-js';
import { computed, onBeforeUnmount, onMounted, reactive, ref, watch } from 'vue';

const page = usePage();
const isBackoffice = computed(() => {
    const url = page.url || '';
    const component = page.component || '';
    return url.startsWith('/admin') || url.startsWith('/vendor') || component.startsWith('Admin/') || component.startsWith('Vendor/');
});

const storageKey = 'tapaklokal-accessibility-v1';
const defaults = { contrast: 0, spacing: 0, color: 0, text: 0, links: 0, animation: 0, images: 0, dyslexia: 0, cursor: 0, line: 0, speech: 0, guide: 0 };
const settings = reactive({ ...defaults });
const panel = ref(null);
const trigger = ref(null);
const isOpen = ref(false);
const showBackToTop = ref(false);
const announcement = ref('');

// Select-to-speak state
const selectedText = ref('');
const bubblePosition = reactive({ top: 0, left: 0 });
const showSpeechBubble = ref(false);
const isSpeaking = ref(false);

// Reading ruler state
const rulerY = ref(-100);
const isRulerVisible = ref(false);

const controls = [
    { key: 'contrast', icon: Contrast, labels: ['Kontras standar', 'Kontras gelap', 'Kontras terang', 'Kontras tinggi'] },
    { key: 'spacing', icon: ArrowLeftRight, labels: ['Spasi standar', 'Spasi sedang', 'Spasi lebar', 'Spasi sangat lebar'] },
    { key: 'color', icon: Droplet, labels: ['Warna standar', 'Abu-abu', 'Saturasi rendah', 'Saturasi tinggi'] },
    { key: 'text', icon: ALargeSmall, labels: ['Teks standar', 'Teks besar', 'Teks lebih besar', 'Teks terbesar'] },
    { key: 'links', icon: Link2, labels: ['Sorot tautan', 'Tautan disorot'] },
    { key: 'animation', icon: PauseCircle, labels: ['Animasi dijeda', 'Animasi dijeda'], description: 'Jeda animasi' },
    { key: 'images', icon: ImageOff, labels: ['Sembunyikan gambar', 'Gambar disembunyikan'] },
    { key: 'dyslexia', labels: ['Ramah disleksia', 'Ramah disleksia aktif'] },
    { key: 'cursor', icon: Mouse, labels: ['Kursor besar', 'Kursor besar aktif'] },
    { key: 'line', icon: MoveVertical, labels: ['Tinggi baris', 'Baris sedang', 'Baris renggang', 'Baris sangat renggang'] },
    { key: 'speech', icon: Volume2, labels: ['Baca teks', 'Baca teks aktif'], description: 'Baca teks terpilih' },
    { key: 'guide', icon: ScanLine, labels: ['Garis penuntun', 'Garis penuntun aktif'], description: 'Garis penuntun baca' },
];

const applySettings = () => {
    for (const key of Object.keys(defaults)) {
        document.documentElement.dataset[`a11y${key[0].toUpperCase()}${key.slice(1)}`] = String(settings[key]);
    }
    try {
        localStorage.setItem(storageKey, JSON.stringify(settings));
    } catch {
        // Preferences still work when browser storage is unavailable.
    }
};

const stopSpeech = () => {
    if (typeof window !== 'undefined' && 'speechSynthesis' in window) {
        window.speechSynthesis.cancel();
    }
    isSpeaking.value = false;
};

const cycle = (control) => {
    settings[control.key] = (settings[control.key] + 1) % control.labels.length;
    announcement.value = `${control.description || control.labels[settings[control.key]]}: ${settings[control.key] ? 'aktif' : 'standar'}`;
    if (control.key === 'speech' && !settings.speech) {
        stopSpeech();
        showSpeechBubble.value = false;
    }
    if (control.key === 'guide' && !settings.guide) {
        isRulerVisible.value = false;
    }
};

const reset = () => {
    Object.assign(settings, defaults);
    stopSpeech();
    showSpeechBubble.value = false;
    isRulerVisible.value = false;
    announcement.value = 'Semua pengaturan aksesibilitas telah direset.';
};

const openPanel = () => {
    panel.value.showModal();
    isOpen.value = true;
};

const closePanel = () => panel.value.close();

const onClose = () => {
    isOpen.value = false;
    trigger.value?.focus({ preventScroll: true });
};

const updateScroll = () => { showBackToTop.value = window.scrollY > 300; };

const backToTop = () => {
    window.scrollTo({ top: 0, behavior: settings.animation || window.matchMedia('(prefers-reduced-motion: reduce)').matches ? 'instant' : 'smooth' });
};

const updateBubblePosition = () => {
    const selection = window.getSelection();
    if (!selection || selection.rangeCount === 0) return;
    const text = selection.toString().trim();
    if (!text) return;
    try {
        const range = selection.getRangeAt(0);
        const rect = range.getBoundingClientRect();
        if (rect.width > 0 && rect.height > 0) {
            selectedText.value = text;
            bubblePosition.top = rect.top + window.scrollY;
            bubblePosition.left = Math.min(
                (document.documentElement.clientWidth || window.innerWidth) - 75,
                Math.max(75, rect.left + window.scrollX + rect.width / 2)
            );
        }
    } catch {
        // Safe fallback
    }
};

const handleSelectionEnd = () => {
    if (!settings.speech) {
        showSpeechBubble.value = false;
        return;
    }
    setTimeout(() => {
        const selection = window.getSelection();
        const text = selection ? selection.toString().trim() : '';
        if (!text) {
            if (!isSpeaking.value) {
                showSpeechBubble.value = false;
            }
            return;
        }
        updateBubblePosition();
        showSpeechBubble.value = true;
        speakText(text);
    }, 40);
};

const handleSelectionChange = () => {
    if (!settings.speech) return;
    const selection = window.getSelection();
    const text = selection ? selection.toString().trim() : '';
    if (!text && !isSpeaking.value) {
        showSpeechBubble.value = false;
        lastSpokenText = '';
    }
};

let lastSpokenText = '';

const speakText = (text) => {
    if (!text || typeof window === 'undefined' || !('speechSynthesis' in window)) {
        return;
    }
    if (text === lastSpokenText && isSpeaking.value) {
        return;
    }

    window.speechSynthesis.cancel();
    lastSpokenText = text;

    const utterance = new SpeechSynthesisUtterance(text);
    utterance.lang = 'id-ID';

    const voices = window.speechSynthesis.getVoices?.() || [];
    const idVoice = voices.find(v => v.lang.startsWith('id') || v.lang.includes('ID'));
    if (idVoice) {
        utterance.voice = idVoice;
    }

    utterance.onstart = () => {
        isSpeaking.value = true;
        announcement.value = 'Membaca teks: ' + text.slice(0, 40);
    };
    utterance.onend = () => {
        isSpeaking.value = false;
        announcement.value = 'Selesai membaca teks.';
    };
    utterance.onerror = () => {
        isSpeaking.value = false;
    };

    window.speechSynthesis.speak(utterance);
};

const toggleSpeak = () => {
    if (isSpeaking.value) {
        stopSpeech();
        announcement.value = 'Pembacaan suara dihentikan.';
    } else if (selectedText.value) {
        speakText(selectedText.value);
    }
};

const handlePointerMove = (e) => {
    if (settings.guide) {
        rulerY.value = e.clientY;
        isRulerVisible.value = true;
    }
};

const handlePointerLeave = () => {
    isRulerVisible.value = false;
};

watch(settings, applySettings);
watch(() => settings.speech, (newVal) => {
    if (!newVal) {
        stopSpeech();
        showSpeechBubble.value = false;
        lastSpokenText = '';
    }
});

onMounted(() => {
    try {
        const saved = JSON.parse(localStorage.getItem(storageKey) || '{}');
        for (const control of controls) {
            const value = saved?.[control.key];
            if (Number.isInteger(value) && value >= 0 && value < control.labels.length) {
                settings[control.key] = value;
            }
        }
    } catch {
        // Invalid saved preferences fall back to the standard display.
    }
    applySettings();
    updateScroll();
    window.addEventListener('scroll', updateScroll, { passive: true });
    document.addEventListener('mouseup', handleSelectionEnd, { passive: true });
    document.addEventListener('keyup', handleSelectionEnd, { passive: true });
    document.addEventListener('selectionchange', handleSelectionChange, { passive: true });
    window.addEventListener('resize', updateBubblePosition, { passive: true });
    window.addEventListener('pointermove', handlePointerMove, { passive: true });
    document.documentElement.addEventListener('pointerleave', handlePointerLeave, { passive: true });
});

onBeforeUnmount(() => {
    window.removeEventListener('scroll', updateScroll);
    document.removeEventListener('mouseup', handleSelectionEnd);
    document.removeEventListener('keyup', handleSelectionEnd);
    document.removeEventListener('selectionchange', handleSelectionChange);
    window.removeEventListener('resize', updateBubblePosition);
    window.removeEventListener('pointermove', handlePointerMove);
    document.documentElement.removeEventListener('pointerleave', handlePointerLeave);
    stopSpeech();
    for (const key of Object.keys(defaults)) {
        delete document.documentElement.dataset[`a11y${key[0].toUpperCase()}${key.slice(1)}`];
    }
});
</script>

<template>
    <Teleport v-if="!isBackoffice" to="body">
        <!-- Reading Guide Ruler -->
        <div
            v-if="settings.guide && isRulerVisible"
            class="accessibility-reading-ruler"
            :style="{ top: `${rulerY}px` }"
            aria-hidden="true"
        ></div>

        <!-- Automatic Select to Speak floating action & stop button -->
        <button
            v-if="settings.speech && showSpeechBubble"
            type="button"
            class="accessibility-speech-bubble"
            :class="{ 'is-speaking': isSpeaking }"
            :style="{ top: `${bubblePosition.top}px`, left: `${bubblePosition.left}px` }"
            :aria-label="isSpeaking ? 'Hentikan pembacaan teks' : 'Baca ulang teks terpilih'"
            @click="toggleSpeak"
        >
            <component :is="isSpeaking ? Square : Volume2" :size="15" aria-hidden="true" />
            <span>{{ isSpeaking ? 'Hentikan Suara' : 'Baca Ulang' }}</span>
        </button>

        <div class="accessibility-tools" :style="{ '--accessibility-panel-bottom': showBackToTop ? '148px' : '84px' }">
            <div class="accessibility-launchers">
                <button ref="trigger" type="button" class="accessibility-fab" aria-label="Buka pengaturan aksesibilitas" aria-haspopup="dialog" aria-controls="accessibility-panel" :aria-expanded="isOpen" @click="openPanel">
                    <svg width="32" height="32" viewBox="0 0 32 32" fill="none" aria-hidden="true">
                        <circle cx="19" cy="5.5" r="3" fill="currentColor" />
                        <path d="M8 13l4-3 5.5 2-1.5 6h8l1.5 9M9 17a7.5 7.5 0 1 0 11 8" stroke="currentColor" stroke-width="3.5" stroke-linecap="round" stroke-linejoin="round" />
                    </svg>
                </button>
                <button v-if="showBackToTop" type="button" class="accessibility-fab accessibility-fab--top" aria-label="Kembali ke atas" @click="backToTop"><ArrowUp :size="29" :stroke-width="3" aria-hidden="true" /></button>
            </div>
            <dialog id="accessibility-panel" ref="panel" class="accessibility-panel" aria-labelledby="accessibility-title" @close="onClose" @click="($event.target === panel) && closePanel()">
                <header class="accessibility-header">
                    <div><h2 id="accessibility-title"><Settings2 :size="16" aria-hidden="true" /> Aksesibilitas</h2><p>Personalisasi tampilan Anda</p></div>
                    <button type="button" class="accessibility-close" aria-label="Tutup aksesibilitas" autofocus @click="closePanel"><X :size="24" aria-hidden="true" /></button>
                </header>
                <div class="accessibility-content">
                    <Link :href="route('accessibility.guide')" class="accessibility-guide-button" @click="closePanel"><BookOpen :size="19" aria-hidden="true" />Panduan aksesibilitas inklusif</Link>
                    <div class="accessibility-grid">
                        <button v-for="control in controls" :key="control.key" type="button" class="accessibility-tile" :class="{ 'is-active': settings[control.key] > 0 }" :aria-pressed="settings[control.key] > 0" :aria-label="`${control.description || control.labels[settings[control.key]]}, ${settings[control.key] ? 'aktif' : 'nonaktif atau standar'}`" @click="cycle(control)">
                            <component :is="control.icon" v-if="control.icon" :size="23" aria-hidden="true" />
                            <span v-else class="accessibility-dyslexia-icon" aria-hidden="true">Df</span>
                            <span>{{ control.labels[settings[control.key]] }}</span>
                            <span v-if="control.labels.length > 2" class="accessibility-levels" aria-hidden="true"><i v-for="level in control.labels.length - 1" :key="level" :class="{ filled: settings[control.key] >= level }"></i></span>
                            <span v-else-if="settings[control.key]" class="accessibility-active-dot" aria-hidden="true"></span>
                        </button>
                    </div>
                    <button type="button" class="accessibility-reset" @click="reset"><RotateCcw :size="19" aria-hidden="true" />Reset semua pengaturan</button>
                    <p class="accessibility-credit">Jelajah tanpa batas bersama TapakLokal</p>
                </div>
                <p class="sr-only" role="status" aria-live="polite">{{ announcement }}</p>
            </dialog>
        </div>
    </Teleport>
</template>

