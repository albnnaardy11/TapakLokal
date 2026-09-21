# Panduan & Arsitektur Skeleton Loading System — TapakLokal

Dokumen ini menjelaskan arsitektur, prinsip desain, hierarki loading, konvensi penamaan, dan tata cara implementasi Skeleton Loading di platform **TapakLokal** (Laravel + Vue 3 + Inertia.js + Vite + Tailwind CSS v4).

---

## 1. Arsitektur Umum

Sistem Skeleton Loading di TapakLokal dibangun dengan prinsip decoupled, progressive, anti-flicker, dan non-blocking:

```text
Browser Request / Navigasi
           ↓
   Laravel Blade Shell (resources/views/app.blade.php)
           ↓
     Inertia.js App (resources/js/app.js)
           ↓
   Vue Page Component (misal: Welcome.vue)
           ↓
 ┌─────────────────────────────────────────────────────────────┐
 │ Level 1: Page Loading Coordinator (GlobalSkeletonLoader)    │
 │ (Hanya aktif jika navigasi Inertia > 250ms / slow network) │
 └─────────────────────────────────────────────────────────────┘
           ↓
 ┌─────────────────────────────────────────────────────────────┐
 │ Level 2: Section-Level Loading (useLoadingState / Props)    │
 │ (Independen per section, anti-flicker threshold 150ms)     │
 └─────────────────────────────────────────────────────────────┘
           ↓
 ┌─────────────────────────────────────────────────────────────┐
 │ Level 3: Card & Base Skeletons (Base Primitives + Cards)    │
 │ (SkeletonBox, SkeletonText, TripCardSkeleton, dsb)          │
 └─────────────────────────────────────────────────────────────┘
           ↓
 ┌─────────────────────────────────────────────────────────────┐
 │ Level 4: Progressive Image Loading (ProgressiveImage)       │
 │ (Placeholder shimmer → Smooth fade-in tanpa CLS)            │
 └─────────────────────────────────────────────────────────────┘
```

Prinsip fundamental:
> *"Render what is ready. Skeleton what is not ready. Lazy-load what is not needed yet. Never block the entire experience unnecessarily."*

---

## 2. Hierarki Loading

| Level | Ruang Lingkup | Tanggung Jawab & Implementasi | Kapan Digunakan |
| :--- | :--- | :--- | :--- |
| **Level 1: Initial Boot** | Application Shell | `app.blade.php` sebagai HTML shell murni. Tanpa Blade skeleton duplikat. | Saat browser pertama kali mengunduh bundle HTML/JS/CSS. |
| **Level 2: Page-Level** | Halaman Penuh | `GlobalSkeletonLoader.vue` + Page Skeletons (`WelcomeSkeleton.vue`, dsb). | **Hanya** saat navigasi Inertia lambat (>250ms threshold) atau data halaman belum siap. Navigasi cepat (<250ms) cukup memakai top progress bar. |
| **Level 3: Section-Level** | Blok Konten / Section | Section components (`DestinationExplore.vue`, `PartnerTrips.vue`) + `useLoadingState`. | Komponen asynchronous yang mengambil data spesifik tanpa memblokir section lain. |
| **Level 4: Media & Gambar** | Aset Visual | `ProgressiveImage.vue` + `useImageLoading`. | Gambar hero, thumbnail kartu, banner, dan foto galeri untuk mencegah Cumulative Layout Shift (CLS). |

---

## 3. Tanggung Jawab Blade (Blade Responsibility)

- **File**: `resources/views/app.blade.php`
- **Aturan**:
  1. Blade **hanya** berfungsi sebagai application entry point / HTML shell (`<!DOCTYPE html>`, meta tags, fonts, `@vite`, `@inertiaHead`, `@inertia`).
  2. **Dilarang** membuat full-page skeleton berbasis Blade (misal: `resources/views/skeletons/*`).
  3. Seluruh rendering UI skeleton adalah tanggung jawab **Vue 3**.

---

## 4. Tanggung Jawab Vue (Vue Responsibility)

- Seluruh skeleton components ditempatkan di `resources/js/Components/Skeletons/`:
  - `Base/`: Primitif atomik (`SkeletonBox.vue`, `SkeletonText.vue`, `SkeletonImage.vue`, `SkeletonCircle.vue`, `SkeletonButton.vue`).
  - `Cards/`: Molekul kartu domain (`TripCardSkeleton.vue`, `DestinationCardSkeleton.vue`, `BlogCardSkeleton.vue`, `ReviewCardSkeleton.vue`, `TripOptionSkeleton.vue`).
  - `Pages/`: Organisme halaman utuh (`WelcomeSkeleton.vue`, `BlogSkeleton.vue`, `TripDetailSkeleton.vue`, `AccountSkeleton.vue`, `AccessibilityGuideSkeleton.vue`).
  - `NavbarSkeleton.vue`: Reusable header navigation skeleton.

---

## 5. Page Skeleton

Page skeleton hanya menampilkan representasi makro dari halaman utuh.
- Menggunakan `aria-hidden="true"` agar screen reader tidak membaca konten semu.
- Mengikuti dimensi, padding, dan grid responsive asli halaman target.
- Dikoordinasikan oleh `GlobalSkeletonLoader.vue` secara deterministik berdasarkan `page.component`.

---

## 6. Section Skeleton

Setiap section yang memuat data secara asynchronous mengontrol state loading-nya sendiri.
- Menggunakan prop `isLoading` atau composable `useLoadingState()`.
- Container section diberi atribut `:aria-busy="isLoading"`.
- Jika Section A selesai dalam 200ms dan Section B selesai dalam 700ms, Section A langsung menampilkan data riil tanpa menunggu Section B.

---

## 7. Image Loading State (`ProgressiveImage`)

- **Komponen**: `resources/js/Components/Shared/ProgressiveImage.vue`
- **Composable**: `resources/js/Composables/useImageLoading.js`
- **Fitur**:
  - Container dengan fixed aspect-ratio (`aspect-[155/105]`, `aspect-[4/3]`, dsb).
  - Skeleton shimmer placeholder saat gambar dalam proses download.
  - Smooth opacity fade-in (`duration-300`) saat gambar siap.
  - Fallback error state jika URL gambar gagal dimuat.
  - Zero Cumulative Layout Shift (CLS).

---

## 8. Lazy Loading (`useLazySection`)

- **Composable**: `resources/js/Composables/useLazySection.js`
- Menggunakan `IntersectionObserver` dengan `rootMargin: '200px'` sehingga section di bawah fold mulai dimuat sebelum user menggulir tepat di atasnya.
- Menghemat konsumsi bandwidth, CPU parsing, dan memory DOM.

---

## 9. Error State (`ErrorState`)

- **Komponen**: `resources/js/Components/Shared/ErrorState.vue`
- Ditampilkan saat fetch API gagal (network error / 500 error).
- Memiliki semantic accessibility `role="alert"`.
- Menyediakan pesan yang ramah pengguna dan tombol **Coba Lagi (Retry)**.

---

## 10. Empty State (`EmptyState`)

- **Komponen**: `resources/js/Components/Shared/EmptyState.vue`
- Ditampilkan saat fetch API sukses namun dataset bernilai kosong (`[]` atau `{}`).
- Dilarang menampilkan skeleton tak berujung jika data memang kosong.
- Menyediakan ikon konteks, judul, deskripsi, dan tombol aksi opsional (Call to Action).

---

## 11. Inertia Navigation & Anti-Flicker Policy

- **Inertia Progress Bar**: Dikonfigurasi di `resources/js/app.js` dengan warna brand `#3E7BEF`.
- **Threshold Policy**:
  - `GlobalSkeletonLoader.vue` memiliki delay threshold **250ms**.
  - Navigasi instan (<250ms) hanya menampilkan thin top progress bar tanpa memunculkan skeleton overlay putih, menjaga kenyamanan UX SPA.
  - Navigasi yang memakan waktu >250ms akan menampilkan page skeleton yang sesuai secara halus.

---

## 12. Cara Menambahkan Skeleton Baru

### A. Jika membuat Section Asynchronous baru:
1. Buat atau gunakan Card Skeleton di `resources/js/Components/Skeletons/Cards/`.
2. Pada komponen section Anda:
   ```vue
   <script setup>
   import { useLoadingState } from '@/Composables/useLoadingState';
   import TripCardSkeleton from '@/Components/Skeletons/Cards/TripCardSkeleton.vue';
   import EmptyState from '@/Components/Shared/EmptyState.vue';
   import ErrorState from '@/Components/Shared/ErrorState.vue';

   const { isLoading, isError, isEmpty, data, execute, retry } = useLoadingState({
       thresholdMs: 150,
       initialLoading: true,
   });

   onMounted(() => {
       execute(fetchMyData);
   });
   </script>

   <template>
       <section :aria-busy="isLoading">
           <TripCardSkeleton v-if="isLoading" :count="4" layout="grid" />
           <ErrorState v-else-if="isError" @retry="retry" />
           <EmptyState v-else-if="isEmpty" message="Tidak ada data ditemukan." />
           <div v-else class="grid grid-cols-4 gap-4">
               <RealCard v-for="item in data" :key="item.id" :item="item" />
           </div>
       </section>
   </template>
   ```

### B. Jika membuat Halaman Baru (Page-Level Skeleton):
1. Buat `resources/js/Components/Skeletons/NewPageSkeleton.vue`.
2. Susun menggunakan `NavbarSkeleton` dan Base Primitives (`SkeletonBox`, `SkeletonText`, dsb).
3. Daftarkan mapping komponen pada `GlobalSkeletonLoader.vue`:
   ```javascript
   if (currentComponent === 'NewPage' || path.startsWith('/new-page')) {
       return NewPageSkeleton;
   }
   ```

---

## 13. Konvensi Penamaan (Naming Conventions)

- **Base Primitives**: `Skeleton[Shape].vue` (contoh: `SkeletonBox.vue`, `SkeletonText.vue`, `SkeletonImage.vue`, `SkeletonCircle.vue`, `SkeletonButton.vue`).
- **Domain Cards**: `[Feature]CardSkeleton.vue` (contoh: `TripCardSkeleton.vue`, `DestinationCardSkeleton.vue`, `BlogCardSkeleton.vue`, `ReviewCardSkeleton.vue`).
- **Page Skeletons**: `[PageName]Skeleton.vue` (contoh: `WelcomeSkeleton.vue`, `BlogSkeleton.vue`, `TripDetailSkeleton.vue`, `AccountSkeleton.vue`).
- **Composables**: `use[Feature].js` (contoh: `useLoadingState.js`, `useImageLoading.js`, `useLazySection.js`).
- **Props**: Gunakan prop `isLoading` (tipe Boolean, default `false`) pada section components.

---

## 14. Prosedur Pengujian (Testing Procedure)

Sebelum merilis perubahan ke production, jalankan checklist pengujian berikut:

1. **Automated Unit & Feature Tests**:
   ```bash
   php artisan test
   ```
2. **PHP Code Style Check**:
   ```bash
   vendor/bin/pint --format agent
   ```
3. **Vite Bundle Build**:
   ```bash
   npm run build
   ```
4. **Manual & Network Throttling Tests**:
   - **Fast Connection**: Klik menu navigasi antar halaman. Pastikan transisi mulus dengan top progress bar tanpa flash skeleton mendadak.
   - **Slow 3G / 1000ms Latency**: Buka halaman via direct URL dan navigasi Inertia. Pastikan skeleton muncul proporsional sesuai tata letak akhir tanpa layout shift.
   - **Network Error**: Simulasikan request gagal, pastikan `ErrorState` tampil dengan tombol Retry yang berfungsi.
   - **Empty Response**: Simulasikan data kosong, pastikan `EmptyState` tampil tanpa skeleton abadi.
   - **Prefers Reduced Motion**: Aktifkan mode reduced motion di OS / DevTools, pastikan animasi shimmer berhenti dan berubah menjadi warna solid statis yang rapi.
   - **Responsive Viewport**: Uji pada Mobile (375px), Tablet (768px), dan Desktop (1280px).
