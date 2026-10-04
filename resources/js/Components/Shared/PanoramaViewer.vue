<script setup>
import { onBeforeUnmount, onMounted, ref, watch } from 'vue';
import { Maximize2, Minimize2, Play, Pause, RotateCw, ZoomIn, ZoomOut, RefreshCw } from 'lucide-vue-next';

const props = defineProps({
    tour: { type: Object, required: true },
    autoRotate: { type: Boolean, default: true },
    rotateSpeed: { type: Number, default: 0.00018 },
});

const canvas = ref(null);
const container = ref(null);
const ready = ref(false);
const isRotating = ref(props.autoRotate);
const isFullscreen = ref(false);
const error = ref('');

let gl, program, texture, buffer, resizeObserver, intersectObserver, image, frameId = null;
let locViewport, locView;
let yaw = 0, pitch = 0, fov = 75, isDisposed = false, isVisible = false, previousFrame = 0, drag = null;
const shaders = [];

function createShader(type, source) {
    const s = gl.createShader(type);
    shaders.push(s);
    gl.shaderSource(s, source);
    gl.compileShader(s);
    if (!gl.getShaderParameter(s, gl.COMPILE_STATUS)) {
        throw new Error('Renderer panorama tidak tersedia di perangkat ini.');
    }
    return s;
}

function updateCanvasDimensions() {
    const cv = canvas.value;
    const ct = container.value;
    if (cv && ct && gl) {
        const dpr = Math.min(window.devicePixelRatio || 1, 2);
        const cssWidth = ct.clientWidth || (isFullscreen.value ? window.innerWidth : 600);
        const cssHeight = ct.clientHeight || (isFullscreen.value ? window.innerHeight : 400);

        const physicalWidth = Math.round(cssWidth * dpr);
        const physicalHeight = Math.round(cssHeight * dpr);

        if (cv.width !== physicalWidth || cv.height !== physicalHeight) {
            cv.width = physicalWidth;
            cv.height = physicalHeight;
            gl.viewport(0, 0, physicalWidth, physicalHeight);
            if (locViewport) gl.uniform2f(locViewport, physicalWidth, physicalHeight);
        }
    }
}

function render(timestamp) {
    if (!ready.value || isDisposed || !gl || !isVisible) {
        frameId = null;
        return;
    }

    const elapsed = Math.min(timestamp - previousFrame || 16, 48);
    previousFrame = timestamp;

    if (isRotating.value && !drag && document.visibilityState === 'visible') {
        yaw += elapsed * props.rotateSpeed;
    }

    updateCanvasDimensions();

    if (locView) {
        gl.uniform3f(locView, yaw, pitch, (fov * Math.PI) / 360);
    }
    gl.drawArrays(gl.TRIANGLES, 0, 6);

    frameId = requestAnimationFrame(render);
}

function startRenderLoop() {
    if (!frameId && ready.value && !isDisposed && isVisible) {
        previousFrame = performance.now();
        frameId = requestAnimationFrame(render);
    }
}

function stopRenderLoop() {
    if (frameId) {
        cancelAnimationFrame(frameId);
        frameId = null;
    }
}

let resumeTimer;
function resumeRotation() {
    clearTimeout(resumeTimer);
    if (props.autoRotate && !window.matchMedia?.('(prefers-reduced-motion: reduce)')?.matches) {
        resumeTimer = window.setTimeout(() => {
            isRotating.value = true;
            startRenderLoop();
        }, 3000);
    }
}

function toggleRotation() {
    isRotating.value = !isRotating.value;
    if (isRotating.value) {
        startRenderLoop();
    }
}

function resetView() {
    yaw = 0;
    pitch = 0;
    fov = 75;
    startRenderLoop();
}

function initWebGL() {
    if (isDisposed || !canvas.value) return;

    try {
        gl = canvas.value.getContext('webgl', {
            alpha: false,
            antialias: true,
            depth: false,
            stencil: false,
            preserveDrawingBuffer: false,
            powerPreference: 'high-performance',
        });

        if (!gl) throw new Error('Perangkat ini belum mendukung WebGL untuk tampilan 360°.');

        program = gl.createProgram();
        gl.attachShader(
            program,
            createShader(
                gl.VERTEX_SHADER,
                `attribute vec2 position;
                void main(){
                    gl_Position = vec4(position, 0.0, 1.0);
                }`
            )
        );
        gl.attachShader(
            program,
            createShader(
                gl.FRAGMENT_SHADER,
                `#ifdef GL_FRAGMENT_PRECISION_HIGH
                precision highp float;
                #else
                precision mediump float;
                #endif

                uniform sampler2D panorama;
                uniform vec2 viewport;
                uniform vec3 view;

                #define PI 3.14159265358979323846
                #define TWO_PI 6.28318530717958647692

                void main() {
                    vec2 p = (gl_FragCoord.xy / viewport * 2.0 - 1.0) * tan(view.z);
                    vec3 d = normalize(vec3(p.x * (viewport.x / viewport.y), p.y, 1.0));

                    // Pitch rotation (X axis)
                    float cp = cos(view.y);
                    float sp = sin(view.y);
                    d = vec3(d.x, d.y * cp + d.z * sp, -d.y * sp + d.z * cp);

                    // Yaw rotation (Y axis)
                    float cy = cos(view.x);
                    float sy = sin(view.x);
                    d = vec3(d.x * cy + d.z * sy, d.y, -d.x * sy + d.z * cy);

                    // Equirectangular projection mapping
                    float phi = atan(d.x, d.z);
                    float theta = asin(clamp(d.y, -1.0, 1.0));

                    vec2 uv = vec2(
                        fract((phi / TWO_PI) + 0.5),
                        clamp(0.5 - (theta / PI), 0.001, 0.999)
                    );

                    gl_FragColor = texture2D(panorama, uv);
                }`
            )
        );
        gl.linkProgram(program);
        if (!gl.getProgramParameter(program, gl.LINK_STATUS)) {
            throw new Error('Renderer tidak dapat dimulai.');
        }
        gl.useProgram(program);

        locViewport = gl.getUniformLocation(program, 'viewport');
        locView = gl.getUniformLocation(program, 'view');

        buffer = gl.createBuffer();
        gl.bindBuffer(gl.ARRAY_BUFFER, buffer);
        gl.bufferData(
            gl.ARRAY_BUFFER,
            new Float32Array([-1, -1, 1, -1, -1, 1, -1, 1, 1, -1, 1, 1]),
            gl.STATIC_DRAW
        );

        const position = gl.getAttribLocation(program, 'position');
        gl.enableVertexAttribArray(position);
        gl.vertexAttribPointer(position, 2, gl.FLOAT, false, 0, 0);

        texture = gl.createTexture();
        gl.bindTexture(gl.TEXTURE_2D, texture);
        gl.texParameteri(gl.TEXTURE_2D, gl.TEXTURE_MIN_FILTER, gl.LINEAR);
        gl.texParameteri(gl.TEXTURE_2D, gl.TEXTURE_MAG_FILTER, gl.LINEAR);
        gl.texParameteri(gl.TEXTURE_2D, gl.TEXTURE_WRAP_S, gl.CLAMP_TO_EDGE);
        gl.texParameteri(gl.TEXTURE_2D, gl.TEXTURE_WRAP_T, gl.CLAMP_TO_EDGE);

        loadTexture(props.tour.image_url);
    } catch (err) {
        error.value = err.message;
    }
}

function loadTexture(url) {
    if (!url || isDisposed || !gl) return;
    ready.value = false;
    error.value = '';

    image = new Image();
    image.crossOrigin = 'anonymous';
    image.onload = () => {
        if (isDisposed || !gl) return;
        try {
            const maxGpuDim = gl.getParameter(gl.MAX_TEXTURE_SIZE) || 4096;
            const targetWidth = Math.min(maxGpuDim, Math.max(image.width, 2048));
            const targetHeight = Math.round(targetWidth / 2);

            const offCanvas = document.createElement('canvas');
            offCanvas.width = targetWidth;
            offCanvas.height = targetHeight;
            const ctx = offCanvas.getContext('2d', { alpha: false });
            ctx.imageSmoothingEnabled = true;
            ctx.imageSmoothingQuality = 'high';

            const imgW = image.width;
            const imgH = image.height;
            const imgAspect = imgW / imgH;

            if (Math.abs(imgAspect - 2.0) < 0.08) {
                ctx.drawImage(image, 0, 0, targetWidth, targetHeight);
            } else {
                const drawH = Math.round(targetWidth / imgAspect);
                const drawY = Math.max(0, Math.round((targetHeight - drawH) / 2));

                if (drawY > 0) {
                    const sampleH = Math.max(10, Math.min(60, Math.round(imgH * 0.08)));
                    ctx.drawImage(image, 0, 0, imgW, sampleH, 0, 0, targetWidth, drawY + 2);
                    ctx.drawImage(
                        image,
                        0,
                        imgH - sampleH,
                        imgW,
                        sampleH,
                        0,
                        drawY + drawH - 2,
                        targetWidth,
                        targetHeight - (drawY + drawH) + 2
                    );
                }

                ctx.drawImage(image, 0, drawY, targetWidth, drawH);
            }

            gl.bindTexture(gl.TEXTURE_2D, texture);
            gl.pixelStorei(gl.UNPACK_FLIP_Y_WEBGL, false);
            gl.texImage2D(gl.TEXTURE_2D, 0, gl.RGB, gl.RGB, gl.UNSIGNED_BYTE, offCanvas);
            ready.value = true;
            updateCanvasDimensions();
            startRenderLoop();
        } catch (failure) {
            error.value = failure.message || 'Gagal memproses tekstur 360°.';
        }
    };
    image.onerror = () => {
        error.value = 'Foto panorama tidak dapat dimuat.';
    };
    image.src = url;
}

function pointerDown(event) {
    if (!ready.value) return;
    clearTimeout(resumeTimer);
    isRotating.value = false;
    drag = { x: event.clientX, y: event.clientY };
    canvas.value?.setPointerCapture(event.pointerId);
}

function pointerMove(event) {
    if (!drag) return;
    yaw -= (event.clientX - drag.x) * 0.0032;
    pitch = Math.max(-1.3, Math.min(1.3, pitch + (event.clientY - drag.y) * 0.0032));
    drag = { x: event.clientX, y: event.clientY };
    if (!frameId) startRenderLoop();
}

function pointerUp() {
    drag = null;
    resumeRotation();
}

function zoom(amount) {
    fov = Math.max(30, Math.min(95, fov + amount));
    startRenderLoop();
}

function keyboard(event) {
    if (!['ArrowLeft', 'ArrowRight', 'ArrowUp', 'ArrowDown', '+', '-', 'r', 'R', 'f', 'F', ' '].includes(event.key)) return;
    event.preventDefault();
    clearTimeout(resumeTimer);
    isRotating.value = false;

    if (event.key === 'ArrowLeft') yaw -= 0.08;
    if (event.key === 'ArrowRight') yaw += 0.08;
    if (event.key === 'ArrowUp') pitch = Math.min(1.3, pitch + 0.08);
    if (event.key === 'ArrowDown') pitch = Math.max(-1.3, pitch - 0.08);
    if (event.key === '+') zoom(-6);
    if (event.key === '-') zoom(6);
    if (event.key === 'r' || event.key === 'R') resetView();
    if (event.key === 'f' || event.key === 'F') toggleFullscreen();
    if (event.key === ' ') toggleRotation();

    if (event.key !== ' ' && event.key !== 'r' && event.key !== 'R') {
        resumeRotation();
    }
    startRenderLoop();
}

function onFullscreenChange() {
    isFullscreen.value = Boolean(
        document.fullscreenElement ||
        document.webkitFullscreenElement ||
        document.mozFullScreenElement ||
        document.msFullscreenElement
    );
    window.setTimeout(() => {
        updateCanvasDimensions();
        startRenderLoop();
    }, 50);
}

async function toggleFullscreen() {
    try {
        if (document.fullscreenElement) {
            await document.exitFullscreen();
        } else if (container.value) {
            if (container.value.requestFullscreen) {
                await container.value.requestFullscreen();
            } else if (container.value.webkitRequestFullscreen) {
                await container.value.webkitRequestFullscreen();
            }
        }
    } catch {
        error.value = 'Layar penuh tidak didukung di peramban ini.';
    }
}

watch(
    () => props.tour.image_url,
    (newUrl) => {
        if (newUrl && gl) loadTexture(newUrl);
    }
);

onMounted(() => {
    // Respect prefers-reduced-motion accessibility standard
    if (window.matchMedia?.('(prefers-reduced-motion: reduce)')?.matches) {
        isRotating.value = false;
    }

    document.addEventListener('fullscreenchange', onFullscreenChange);
    document.addEventListener('webkitfullscreenchange', onFullscreenChange);

    if (container.value && window.IntersectionObserver) {
        intersectObserver = new IntersectionObserver(
            ([entry]) => {
                isVisible = entry.isIntersecting;
                if (isVisible) {
                    if (!gl) initWebGL();
                    startRenderLoop();
                } else {
                    stopRenderLoop();
                }
            },
            { threshold: 0.1 }
        );
        intersectObserver.observe(container.value);
    } else {
        isVisible = true;
        initWebGL();
    }

    if (container.value && window.ResizeObserver) {
        resizeObserver = new ResizeObserver(() => {
            updateCanvasDimensions();
            if (isVisible) startRenderLoop();
        });
        resizeObserver.observe(container.value);
    }
});

onBeforeUnmount(() => {
    isDisposed = true;
    stopRenderLoop();
    clearTimeout(resumeTimer);
    document.removeEventListener('fullscreenchange', onFullscreenChange);
    document.removeEventListener('webkitfullscreenchange', onFullscreenChange);
    intersectObserver?.disconnect();
    resizeObserver?.disconnect();

    if (image) {
        image.onload = null;
        image.onerror = null;
        image.src = '';
    }
    if (gl) {
        if (texture) gl.deleteTexture(texture);
        if (buffer) gl.deleteBuffer(buffer);
        shaders.forEach((s) => gl.deleteShader(s));
        if (program) gl.deleteProgram(program);
        gl.getExtension('WEBGL_lose_context')?.loseContext();
    }
});
</script>

<template>
    <section
        ref="container"
        class="group relative overflow-hidden bg-gradient-to-b from-[#081c3b] to-[#040e1f] text-white shadow-xl select-none"
        :class="[
            isFullscreen
                ? 'fixed inset-0 z-[9999] h-screen w-screen rounded-none'
                : 'h-[240px] min-h-[240px] rounded-none sm:h-[300px] sm:min-h-[300px]'
        ]"
        role="region"
        :aria-label="`Viewer panorama interaktif 360 derajat ${tour.title}`"
    >
        <!-- Canvas Viewer with high-dpi rendering -->
        <canvas
            ref="canvas"
            class="w-full cursor-grab touch-none active:cursor-grabbing block"
            :class="[isFullscreen ? 'h-full' : 'h-[240px] sm:h-[300px]']"
            tabindex="0"
            role="img"
            :aria-label="`Panorama 360 derajat ${tour.title}. Gunakan tombol panah untuk memutar, plus minus untuk zoom, R untuk reset, dan F untuk layar penuh.`"
            @pointerdown="pointerDown"
            @pointermove="pointerMove"
            @pointerup="pointerUp"
            @pointercancel="pointerUp"
            @keydown="keyboard"
            @webglcontextlost.prevent="error = 'Koneksi grafis terhenti. Silakan muat ulang halaman.'"
        ></canvas>

        <!-- Low-res Thumbnail Blurred Placeholder (Instant Preview while WebGL texture is decoding) -->
        <img
            v-if="!ready && tour.thumbnail_url"
            :src="tour.thumbnail_url"
            :alt="tour.title"
            width="480"
            height="240"
            class="absolute inset-0 size-full object-cover blur-sm opacity-40 transition-opacity duration-500 pointer-events-none"
            aria-hidden="true"
        />

        <!-- Loading / Error Overlay -->
        <div
            v-if="!ready"
            class="absolute inset-0 grid place-content-center gap-3 bg-[#081c3b]/85 p-8 text-center backdrop-blur-sm z-10"
        >
            <span
                v-if="!error"
                class="mx-auto size-10 animate-spin rounded-full border-3 border-sky-400/20 border-t-sky-400"
                aria-hidden="true"
            ></span>
            <p class="text-base font-bold text-white tracking-tight">{{ tour.title }}</p>
            <p class="text-xs text-sky-200/90">{{ error || 'Memuat panorama 360° resolusi tinggi…' }}</p>
            <a
                v-if="error && tour.image_url"
                :href="tour.image_url"
                target="_blank"
                rel="noopener"
                class="mt-2 text-xs text-sky-400 underline hover:text-sky-300 focus:outline-none focus:ring-2 focus:ring-sky-400"
            >
                Buka gambar sumber
            </a>
        </div>

        <!-- Top Indicator Badge -->
        <div class="absolute top-3 left-3 flex items-center gap-2 z-20 pointer-events-none">
            <span class="inline-flex items-center gap-1.5 rounded-full bg-black/60 px-2.5 py-1 text-[10px] font-semibold text-white backdrop-blur-md border border-white/10 shadow-sm">
                <span class="size-2 rounded-full bg-emerald-400 animate-pulse" aria-hidden="true"></span>
                <span>360° Interaktif</span>
            </span>
        </div>

        <!-- Bottom Controls Overlay -->
        <div
            v-if="ready"
            class="absolute inset-x-0 bottom-0 flex items-center justify-between p-3.5 text-xs z-20 pointer-events-none"
        >
            <div class="pointer-events-auto">
                <button
                    type="button"
                    class="inline-flex items-center gap-1.5 rounded-lg bg-black/60 px-2.5 py-1.5 text-[11px] font-medium text-white/90 backdrop-blur-md transition hover:bg-black/80 active:scale-95 border border-white/10 focus:outline-none focus-visible:ring-2 focus-visible:ring-[#0088ff]"
                    :aria-label="isRotating ? 'Jeda putaran otomatis' : 'Mulai putaran otomatis'"
                    :title="isRotating ? 'Jeda Putaran (Spasi)' : 'Mulai Putaran (Spasi)'"
                    @click="toggleRotation"
                >
                    <Pause v-if="isRotating" class="size-3.5 text-sky-400" aria-hidden="true" />
                    <Play v-else class="size-3.5 text-sky-400" aria-hidden="true" />
                    <span class="hidden sm:inline">{{ isRotating ? 'Jeda' : 'Putar' }}</span>
                </button>
            </div>

            <div class="flex items-center gap-1.5 pointer-events-auto">
                <button
                    type="button"
                    class="inline-flex items-center justify-center size-8 rounded-lg bg-black/60 text-white backdrop-blur-md transition hover:bg-black/80 active:scale-95 border border-white/10 focus:outline-none focus-visible:ring-2 focus-visible:ring-[#0088ff]"
                    aria-label="Reset sudut pandang ke awal"
                    title="Reset Posisi (R)"
                    @click="resetView"
                >
                    <RefreshCw class="size-3.5" aria-hidden="true" />
                </button>
                <button
                    type="button"
                    class="inline-flex items-center justify-center size-8 rounded-lg bg-black/60 text-white backdrop-blur-md transition hover:bg-black/80 active:scale-95 border border-white/10 focus:outline-none focus-visible:ring-2 focus-visible:ring-[#0088ff]"
                    aria-label="Perbesar tampilan panorama"
                    title="Perbesar (+)"
                    @click="zoom(-10)"
                >
                    <ZoomIn class="size-4" aria-hidden="true" />
                </button>
                <button
                    type="button"
                    class="inline-flex items-center justify-center size-8 rounded-lg bg-black/60 text-white backdrop-blur-md transition hover:bg-black/80 active:scale-95 border border-white/10 focus:outline-none focus-visible:ring-2 focus-visible:ring-[#0088ff]"
                    aria-label="Perkecil tampilan panorama"
                    title="Perkecil (-)"
                    @click="zoom(10)"
                >
                    <ZoomOut class="size-4" aria-hidden="true" />
                </button>
                <button
                    type="button"
                    class="inline-flex items-center gap-1.5 rounded-lg bg-black/60 px-3 py-1.5 text-xs font-semibold text-white backdrop-blur-md transition hover:bg-black/80 active:scale-95 border border-white/10 focus:outline-none focus-visible:ring-2 focus-visible:ring-[#0088ff]"
                    :aria-label="isFullscreen ? 'Keluar dari mode layar penuh' : 'Tampilkan mode layar penuh'"
                    :title="isFullscreen ? 'Keluar Layar Penuh (F / Esc)' : 'Layar Penuh (F)'"
                    @click="toggleFullscreen"
                >
                    <Minimize2 v-if="isFullscreen" class="size-3.5 text-sky-400" aria-hidden="true" />
                    <Maximize2 v-else class="size-3.5 text-sky-400" aria-hidden="true" />
                    <span class="hidden sm:inline">{{ isFullscreen ? 'Keluar' : 'Layar Penuh' }}</span>
                </button>
            </div>
        </div>

        <!-- Non-fatal notification error -->
        <p
            v-if="ready && error"
            role="alert"
            class="absolute left-4 top-16 max-w-sm rounded-lg bg-rose-950/85 border border-rose-500/30 p-3 text-xs text-rose-200 backdrop-blur-md z-30"
        >
            {{ error }}
        </p>
    </section>
</template>

<style scoped>
:fullscreen,
:-webkit-full-screen {
    width: 100vw !important;
    height: 100vh !important;
    max-width: 100vw !important;
    max-height: 100vh !important;
    border-radius: 0 !important;
}

:fullscreen canvas,
:-webkit-full-screen canvas {
    width: 100vw !important;
    height: 100vh !important;
}
</style>
