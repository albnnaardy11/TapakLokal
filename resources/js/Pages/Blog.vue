<script setup>
import { computed, ref } from 'vue';
import { Head, Link } from '@inertiajs/vue3';
import { route } from 'ziggy-js';
import {
    ArrowRight,
    ArrowUpRight,
    Search,
    MapPin,
    BookOpen,
    Compass,
    Backpack,
    Check,
    SlidersHorizontal,
    HeartHandshake,
} from 'lucide-vue-next';
import MainNavigation from '../Components/Shared/MainNavigation.vue';
import MainFooter from '../Components/Shared/MainFooter.vue';
import Pagination from '../Components/Admin/Pagination.vue';
import ProgressiveImage from '../Components/Shared/ProgressiveImage.vue';
import { articles } from '../Components/Home/travelArticles';

const props = defineProps({
    publishedArticles: {
        type: Object,
        default: () => ({ data: [] }),
    },
});

const query = ref('');
const category = ref('Semua cerita');
const checklist = ref([]);

const image = (id, width = 900) =>
    `https://images.unsplash.com/photo-${id}?auto=format&fit=crop&w=${width}&q=85`;

const library = computed(() =>
    props.publishedArticles?.data?.length
        ? props.publishedArticles.data.map(item => ({
              ...item,
              href: route('blog.show', item.slug),
              image: item.image_url || image('1469474968028-56623f02e42e'),
              category: item.category || 'Cerita Perjalanan',
          }))
        : articles
              .filter(item => ['bali', 'islands', 'local'].includes(item.id))
              .map(item => ({
                  ...item,
                  href: route('blog.show', item.id),
                  image: image(item.image),
                  published_at: null,
              }))
);

const categories = computed(() => [
    'Semua cerita',
    ...new Set(library.value.map(item => item.category)),
]);

const featured = computed(() => library.value.slice(0, 3));

const filtered = computed(() =>
    library.value.filter(
        item =>
            (category.value === 'Semua cerita' || category.value === item.category) &&
            `${item.title} ${item.excerpt} ${item.category}`
                .toLowerCase()
                .includes(query.value.trim().toLowerCase())
    )
);

const displayedArticles = computed(() => filtered.value.slice(0, 3));

const date = value =>
    value
        ? new Date(value).toLocaleDateString('id-ID', {
              day: 'numeric',
              month: 'short',
              year: 'numeric',
          })
        : 'Bacaan pilihan';

const localStory = articles.find(item => item.id === 'local');

const destinations = [
    { name: 'Bali', subtitle: 'Budaya, pesisir & ritme yang tenang', image: '1537996194471-e657df975ab4' },
    { name: 'Lombok', subtitle: 'Dari kaki gunung sampai tepi laut', image: '1539367628448-4bc5c9d171c8' },
    { name: 'Labuan Bajo', subtitle: 'Pulau-pulau kecil, cerita besar', image: '1516690561799-46d8f74f9abf' },
    { name: 'Jawa', subtitle: 'Jalur alam & rasa yang beragam', image: '1464822759023-fed622ff2c3b' },
];

const preparations = [
    { title: 'Tentukan ritme perjalanan', text: 'Pilih durasi, aktivitas, dan waktu istirahat yang nyaman.' },
    { title: 'Buat ruang di anggaran', text: 'Catat transportasi, penginapan, makan, dan dana cadangan.' },
    { title: 'Bawa yang benar-benar perlu', text: 'Sesuaikan perlengkapan dengan cuaca dan medan tujuan.' },
];

const togglePreparation = index => {
    checklist.value = checklist.value.includes(index)
        ? checklist.value.filter(item => item !== index)
        : [...checklist.value, index];
};

const reset = () => {
    query.value = '';
    category.value = 'Semua cerita';
};
</script>

<template>
    <Head title="Cerita Perjalanan">
        <meta name="description" content="Inspirasi destinasi, tips perjalanan, dan cerita lokal bersama TapakLokal." />
    </Head>

    <div class="min-h-screen bg-[#f8fafc] text-[#172c50] font-sans">
        <MainNavigation />

        <main class="mx-auto max-w-[1200px] px-4 sm:px-6 lg:px-8 pb-20 sm:pb-28 pt-6 sm:pt-8 lg:pt-12">
            <!-- Hero Section (Identical Structure to Accessibility Guide) -->
            <section aria-labelledby="blog-title" class="relative overflow-hidden rounded-[28px] sm:rounded-[36px] lg:rounded-[40px] border border-[#dce8f8] bg-[#f8fbff] p-6 sm:p-9 md:p-11 lg:p-12 xl:p-14 shadow-[0_4px_24px_rgba(37,99,235,0.03)]">
                <div class="grid grid-cols-1 items-center gap-8 sm:gap-10 lg:grid-cols-[1.1fr_1fr] lg:gap-8 xl:gap-12">
                    <!-- Left Content -->
                    <div class="text-left">
                        <h1 id="blog-title" class="text-3xl font-extrabold tracking-tight text-[#0f172a] sm:text-4xl md:text-5xl lg:text-[52px] xl:text-[54px] leading-[1.12] sm:leading-[1.08]">
                            Setiap perjalanan,<br />
                            <span class="text-[#2563eb]">selalu punya cerita.</span>
                        </h1>
                        <p class="mt-4 sm:mt-6 max-w-xl text-sm leading-relaxed text-[#475569] sm:text-base lg:text-[17px]">
                            Temukan inspirasi destinasi nusantara, tips praktis, rekomendasi kuliner autentik, dan kisah penjelajah lokal bersama TapakLokal.
                        </p>

                        <!-- Quick navigation & action buttons -->
                        <div class="mt-6 sm:mt-8 flex flex-col sm:flex-row items-stretch sm:items-center gap-3 sm:gap-4">
                            <a
                                href="#cerita-perjalanan"
                                class="inline-flex min-h-[48px] items-center justify-center gap-2 rounded-full bg-[#2563eb] px-7 py-3.5 text-sm font-bold text-white shadow-[0_8px_20px_rgba(37,99,235,0.25)] transition-all duration-200 hover:bg-[#1d4ed8] hover:shadow-[0_12px_24px_rgba(37,99,235,0.35)] hover:-translate-y-0.5 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-[#2563eb] sm:px-8 sm:py-4 sm:text-base text-center"
                            >
                                <span>Jelajahi Cerita</span>
                                <ArrowRight class="size-4 shrink-0 stroke-[2.5]" aria-hidden="true" />
                            </a>
                            <Link
                                :href="route('catalog')"
                                class="inline-flex min-h-[48px] items-center justify-center gap-2 rounded-full border border-[#e2e8f0] bg-white px-6 py-3.5 text-sm font-bold text-[#1e293b] shadow-xs transition-all duration-200 hover:bg-slate-50 hover:border-[#cbd5e1] hover:shadow-sm hover:-translate-y-0.5 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-[#2563eb] sm:px-7 sm:py-4 sm:text-base text-center cursor-pointer"
                            >
                                <Compass class="size-4 shrink-0 text-[#2563eb] stroke-[2.2]" aria-hidden="true" />
                                <span>Cari Trip Terkait</span>
                            </Link>
                        </div>
                    </div>

                    <!-- Right Graphic -->
                    <div class="flex items-center justify-center lg:justify-end">
                        <img
                            src="/Assets/Images/blog/hero.png"
                            alt="Ilustrasi Cerita Perjalanan dan Destinasi TapakLokal"
                            class="w-full max-w-[360px] sm:max-w-[460px] md:max-w-[520px] lg:max-w-[580px] xl:max-w-[620px] h-auto object-contain select-none drop-shadow-xs"
                        />
                    </div>
                </div>
            </section>

            <!-- Search & Filter Section -->
            <div id="cerita-perjalanan" class="scroll-mt-32">
                <section aria-label="Temukan bacaan" class="mt-8 border-b border-slate-200 pb-6 sm:mt-10">
                    <div class="flex flex-col justify-between gap-4 md:flex-row md:items-center">
                        <div class="flex items-center gap-2 text-sm font-bold text-[#173654]">
                            <BookOpen class="size-5 text-[#1389e8]" />
                            Temukan cerita untuk perjalananmu
                        </div>
                        <label class="flex items-center gap-3 rounded-lg border border-slate-200 bg-white px-4 py-3 md:w-80">
                            <Search class="size-4 shrink-0 text-[#1389e8]" />
                            <span class="sr-only">Cari judul atau topik artikel</span>
                            <input v-model="query" type="search" placeholder="Cari destinasi, cerita, atau tips..." class="min-w-0 flex-1 bg-transparent text-xs outline-none" />
                        </label>
                    </div>
                    <div class="mt-5 flex gap-2 overflow-x-auto pb-1" aria-label="Kategori artikel">
                        <button
                            v-for="item in categories"
                            :key="item"
                            :aria-pressed="category === item"
                            class="shrink-0 rounded-full border px-4 py-2.5 text-xs font-semibold transition-colors cursor-pointer"
                            :class="category === item ? 'border-[#118bea] bg-[#118bea] text-white' : 'border-slate-200 bg-white text-slate-600 hover:border-sky-300 hover:text-sky-700'"
                            @click="category = item"
                        >
                            {{ item }}
                        </button>
                    </div>
                </section>

                <template v-if="!query && category === 'Semua cerita'">
                    <!-- Editor's Pick Section -->
                    <section class="mt-10 sm:mt-12" aria-labelledby="editor-heading">
                        <div class="mb-5 flex items-end justify-between gap-4">
                            <div>
                                <p class="text-[10px] font-bold tracking-[0.16em] text-[#1389e8]">DIKURASI UNTUK RASA INGIN TAHUMU</p>
                                <h2 id="editor-heading" class="mt-2 text-2xl font-extrabold tracking-tight sm:text-[28px]">Pilihan editor minggu ini</h2>
                            </div>
                            <a href="#artikel-terbaru" class="flex items-center gap-2 text-xs font-bold text-[#1389e8]">
                                Semua artikel <ArrowRight class="size-4" />
                            </a>
                        </div>
                        <div class="grid gap-5 lg:grid-cols-[1.18fr_1fr]">
                            <Link v-if="featured[0]" :href="featured[0].href" class="group relative isolate flex min-h-[360px] flex-col justify-end overflow-hidden rounded-2xl bg-[#173654] p-6 sm:min-h-[420px] sm:p-8">
                                <img :src="featured[0].image" :alt="featured[0].title" loading="lazy" class="absolute inset-0 -z-20 size-full object-cover transition-transform duration-500 group-hover:scale-105 motion-reduce:transition-none" />
                                <div class="absolute inset-0 -z-10 bg-linear-to-t from-[#071d35]/95 via-[#071d35]/25 to-transparent"></div>
                                <span class="absolute left-6 top-6 rounded-md bg-white px-3 py-1.5 text-[10px] font-bold text-[#087ac8]">PILIHAN EDITOR</span>
                                <span class="text-[10px] font-bold uppercase tracking-widest text-sky-200">{{ featured[0].category }}</span>
                                <h3 class="mt-3 max-w-lg text-2xl font-extrabold leading-snug tracking-tight text-white sm:text-[29px]">{{ featured[0].title }}</h3>
                                <p class="mt-3 line-clamp-2 max-w-md text-xs leading-6 text-white/80">{{ featured[0].excerpt }}</p>
                                <div class="mt-5 flex items-center justify-between border-t border-white/20 pt-4 text-xs text-white/80">
                                    <span>{{ date(featured[0].published_at) }}</span>
                                    <span class="flex items-center gap-2 font-bold text-white">Baca cerita <ArrowUpRight class="size-4" /></span>
                                </div>
                            </Link>
                            <div class="grid gap-5">
                                <Link v-for="article in featured.slice(1)" :key="article.id" :href="article.href" class="group grid overflow-hidden rounded-xl border border-slate-200/80 bg-white sm:grid-cols-[42%_1fr]">
                                    <ProgressiveImage :src="article.image" :alt="article.title" rounded="rounded-none" class="min-h-44" />
                                    <div class="flex flex-col p-5">
                                        <span class="text-[10px] font-bold text-[#1389e8]">{{ article.category }}</span>
                                        <h3 class="mt-2 text-base font-extrabold leading-snug group-hover:text-[#1389e8]">{{ article.title }}</h3>
                                        <p class="mt-2 line-clamp-2 text-xs leading-5 text-slate-500">{{ article.excerpt }}</p>
                                        <span class="mt-auto flex items-center gap-2 pt-4 text-[10px] font-bold text-[#1389e8]">Baca selengkapnya <ArrowRight class="size-3.5" /></span>
                                    </div>
                                </Link>
                                <div v-if="featured.length === 1" class="flex flex-col justify-center rounded-xl border border-sky-100 bg-sky-50 p-8">
                                    <Compass class="size-8 text-sky-600" />
                                    <h3 class="mt-4 text-xl font-bold">Satu cerita bisa membuka banyak rencana.</h3>
                                    <p class="mt-3 text-sm leading-6 text-slate-500">Temukan destinasi dan aktivitas untuk perjalanan berikutnya.</p>
                                    <Link :href="route('explore', 'destination')" class="mt-5 text-sm font-bold text-sky-600">Jelajahi destinasi →</Link>
                                </div>
                            </div>
                        </div>
                    </section>

                    <!-- Destination Inspiration Section -->
                    <section class="mt-12 sm:mt-16" aria-labelledby="regions-heading">
                        <div class="mb-5 flex items-end justify-between gap-4">
                            <div>
                                <h2 id="regions-heading" class="text-2xl font-extrabold tracking-tight">Mulai dari tempat yang kamu impikan</h2>
                                <p class="mt-2 text-xs leading-5 text-slate-500">Kenali suasananya. Temukan alasan untuk berangkat.</p>
                            </div>
                            <MapPin class="hidden size-6 text-[#1389e8] sm:block" />
                        </div>
                        <div class="grid grid-cols-2 gap-3 sm:gap-4 lg:grid-cols-4">
                            <Link v-for="destination in destinations" :key="destination.name" :href="route('explore', { type: 'destination', q: destination.name })" class="group relative isolate flex min-h-56 flex-col justify-end overflow-hidden rounded-xl bg-slate-800 p-4 sm:min-h-64 sm:p-5">
                                <img :src="image(destination.image, 600)" :alt="`Inspirasi perjalanan ${destination.name}`" loading="lazy" class="absolute inset-0 -z-20 size-full object-cover transition-transform duration-500 group-hover:scale-105 motion-reduce:transition-none" />
                                <span class="absolute inset-0 -z-10 bg-linear-to-t from-slate-950/85 via-slate-950/5 to-transparent"></span>
                                <div class="flex items-center justify-between text-white">
                                    <h3 class="text-xl font-extrabold">{{ destination.name }}</h3>
                                    <ArrowUpRight class="size-4" />
                                </div>
                                <p class="mt-1.5 text-[10px] leading-4 text-white/80">{{ destination.subtitle }}</p>
                            </Link>
                        </div>
                    </section>

                    <!-- Travel Preparation Section -->
                    <section class="mt-12 grid gap-7 rounded-2xl border border-[#dcecf9] bg-[#eef7ff] p-6 sm:mt-16 sm:p-8 lg:grid-cols-[0.85fr_1.7fr]" aria-labelledby="prepare-heading">
                        <div>
                            <span class="inline-flex items-center gap-2 text-[10px] font-bold tracking-widest text-[#1389e8]"><Backpack class="size-4" /> CATATAN SEBELUM BERANGKAT</span>
                            <h2 id="prepare-heading" class="mt-3 text-2xl font-extrabold leading-tight tracking-tight">Rencana rapi.<br />Liburan lebih santai.</h2>
                            <p class="mt-3 text-xs leading-6 text-slate-500">Tandai yang sudah kamu siapkan. Checklist ini disimpan selama halaman terbuka.</p>
                            <span class="mt-4 inline-block text-xs font-bold text-[#1389e8]" role="status">{{ checklist.length }} dari 3 persiapan selesai</span>
                        </div>
                        <div class="grid gap-3 sm:grid-cols-3">
                            <button type="button" :aria-pressed="checklist.includes(index)" @click="togglePreparation(index)" v-for="(item, index) in preparations" :key="item.title" class="relative cursor-pointer rounded-xl border bg-white p-5 text-left transition-colors focus-visible:outline-2 focus-visible:outline-sky-500" :class="checklist.includes(index) ? 'border-sky-400' : 'border-white'">
                                <span class="flex size-8 items-center justify-center rounded-full border border-sky-200 text-xs font-bold text-sky-600 peer-focus-visible:outline-2 peer-focus-visible:outline-offset-4 peer-focus-visible:outline-sky-500">
                                    <Check v-if="checklist.includes(index)" class="size-4" />
                                    <template v-else>0{{ index + 1 }}</template>
                                </span>
                                <h3 class="mt-5 text-sm font-bold leading-5">{{ item.title }}</h3>
                                <p class="mt-2 text-[11px] leading-5 text-slate-500">{{ item.text }}</p>
                            </button>
                        </div>
                    </section>

                    <!-- Local Story Spotlight Section -->
                    <section class="mt-12 grid overflow-hidden rounded-2xl border border-slate-200 bg-white sm:mt-16 md:grid-cols-2" aria-labelledby="local-heading">
                        <ProgressiveImage :src="image(localStory.image)" :alt="localStory.title" rounded="rounded-none" class="min-h-64 md:min-h-80" />
                        <div class="flex flex-col justify-center p-6 sm:p-9">
                            <span class="flex items-center gap-2 text-[10px] font-bold tracking-widest text-[#1389e8]"><HeartHandshake class="size-4" /> LEBIH DEKAT DENGAN LOKAL</span>
                            <h2 id="local-heading" class="mt-4 text-2xl font-extrabold leading-tight tracking-tight sm:text-3xl">Bukan sekadar tempat.<br />Ada orang dan cerita di dalamnya.</h2>
                            <p class="mt-4 text-sm leading-7 text-slate-500">{{ localStory.excerpt }}</p>
                            <Link :href="route('blog.show', 'local')" class="mt-6 inline-flex items-center gap-2 text-xs font-bold text-[#1389e8]">
                                Kenali cerita lokal <ArrowRight class="size-4" />
                            </Link>
                        </div>
                    </section>
                </template>

                <!-- Latest Articles Section (3 items) -->
                <section id="artikel-terbaru" class="mt-12 scroll-mt-32 sm:mt-16" aria-labelledby="latest-heading">
                    <div class="mb-6 flex items-end justify-between gap-4">
                        <div>
                            <h2 id="latest-heading" class="text-2xl font-extrabold tracking-tight">{{ query || category !== 'Semua cerita' ? 'Cerita yang kamu cari' : 'Bacaan untuk perjalanan berikutnya' }}</h2>
                            <p class="mt-2 text-xs text-slate-500">{{ props.publishedArticles?.last_page > 1 ? 'Pencarian dan kategori berlaku pada halaman artikel ini.' : 'Inspirasi, panduan, dan sudut pandang baru dari TapakLokal.' }}</p>
                        </div>
                        <span class="flex shrink-0 items-center gap-2 text-[10px] text-slate-400" role="status">
                            <SlidersHorizontal class="size-3.5" /> {{ displayedArticles.length }} artikel
                        </span>
                    </div>
                    <div class="grid gap-5 sm:grid-cols-2 lg:grid-cols-3">
                        <article v-for="article in displayedArticles" :key="article.id" class="group overflow-hidden rounded-xl border border-slate-200/80 bg-white transition-shadow hover:shadow-lg hover:shadow-slate-200/40">
                            <Link :href="article.href" class="flex h-full flex-col">
                                <ProgressiveImage :src="article.image" :alt="article.title" rounded="rounded-none" aspect-ratio="16/10" />
                                <div class="flex flex-1 flex-col p-5">
                                    <span class="text-[10px] font-bold text-[#1389e8]">{{ article.category }}</span>
                                    <h3 class="mt-2 text-base font-extrabold leading-snug group-hover:text-[#1389e8]">{{ article.title }}</h3>
                                    <p class="mt-3 line-clamp-2 text-xs leading-6 text-slate-500">{{ article.excerpt }}</p>
                                    <div class="mt-auto flex items-center justify-between pt-5 text-[10px] text-slate-400">
                                        <span>{{ date(article.published_at) }}</span>
                                        <ArrowUpRight class="size-4 text-[#1389e8]" />
                                    </div>
                                </div>
                            </Link>
                        </article>
                    </div>
                    <div v-if="!displayedArticles.length" class="rounded-xl border border-dashed border-slate-300 bg-white px-6 py-12 text-center">
                        <Search class="mx-auto size-7 text-sky-500" />
                        <h3 class="mt-4 font-bold">Belum ada cerita yang cocok</h3>
                        <p class="mt-2 text-sm text-slate-500">Coba nama tempat atau topik lain.</p>
                        <button class="mt-5 text-sm font-bold text-[#1389e8] cursor-pointer" @click="reset">Lihat semua cerita</button>
                    </div>
                    <Pagination v-if="props.publishedArticles" :records="props.publishedArticles" />
                </section>

                <!-- Bottom CTA Section -->
                <section class="mt-12 flex flex-col items-start justify-between gap-6 rounded-2xl bg-[#087ed3] p-7 text-white sm:mt-16 sm:p-9 lg:flex-row lg:items-center">
                    <div>
                        <span class="text-[10px] font-bold tracking-widest text-sky-100">DARI CERITA JADI RENCANA</span>
                        <h2 class="mt-2 text-2xl font-extrabold tracking-tight">Destinasi impianmu tinggal selangkah lagi.</h2>
                        <p class="mt-3 max-w-xl text-xs leading-6 text-sky-100">Temukan open trip dan private trip untuk menjalani cerita versimu sendiri.</p>
                    </div>
                    <Link :href="route('catalog')" class="inline-flex shrink-0 items-center gap-4 rounded-lg bg-white px-5 py-3.5 text-xs font-bold text-[#087ed3] transition-colors hover:bg-sky-50">
                        Temukan perjalanan <ArrowRight class="size-4" />
                    </Link>
                </section>
            </div>
        </main>

        <MainFooter />
    </div>
</template>
