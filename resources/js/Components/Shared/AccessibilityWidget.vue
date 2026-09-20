<script setup>
import { ArrowUp, ArrowLeftRight, BookOpen, Contrast, Droplet, ImageOff, Link2, Mouse, PauseCircle, RotateCcw, Settings2, ALargeSmall, MoveVertical, X } from 'lucide-vue-next';
import { Link } from '@inertiajs/vue3';
import { route } from 'ziggy-js';
import { onBeforeUnmount, onMounted, reactive, ref, watch } from 'vue';

const storageKey = 'tapaklokal-accessibility-v1';
const defaults = { contrast: 0, spacing: 0, color: 0, text: 0, links: 0, animation: 0, images: 0, dyslexia: 0, cursor: 0, line: 0 };
const settings = reactive({ ...defaults });
const panel = ref(null);
const trigger = ref(null);
const isOpen = ref(false);
const showBackToTop = ref(false);
const announcement = ref('');
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
const cycle = (control) => {
    settings[control.key] = (settings[control.key] + 1) % control.labels.length;
    announcement.value = `${control.description || control.labels[settings[control.key]]}: ${settings[control.key] ? 'aktif' : 'standar'}`;
};
const reset = () => {
    Object.assign(settings, defaults);
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

watch(settings, applySettings);
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
});
onBeforeUnmount(() => {
    window.removeEventListener('scroll', updateScroll);
    for (const key of Object.keys(defaults)) {
        delete document.documentElement.dataset[`a11y${key[0].toUpperCase()}${key.slice(1)}`];
    }
});
</script>

<template>
    <Teleport to="body">
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
