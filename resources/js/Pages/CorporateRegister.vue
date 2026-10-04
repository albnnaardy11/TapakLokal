<script setup>
import { Head, Link, useForm, usePage } from '@inertiajs/vue3';
import { computed, onMounted, ref } from 'vue';
import { route } from 'ziggy-js';
import { ArrowRight, Building2, UserRound, Mail, Phone, Briefcase, Wallet, Compass, LockKeyhole, Eye, EyeOff } from 'lucide-vue-next';
import CustomSelect from '../Components/Shared/CustomSelect.vue';

const page = usePage();
const loggedIn = computed(() => ! ! page.props.auth?.user);
const showPassword = ref(false);

const form = useForm({
    pic_name: page.props.auth?.user?.name || '',
    position: '',
    work_email: page.props.auth?.user?.email || '',
    phone: page.props.auth?.user?.phone || '',
    name: '',
    password: '',
    password_confirmation: '',
    budget_range: '',
    source: '',
    consent: false,
    marketing_consent: false,
});
const draftKey = 'tapaklokal.corporate.registration-draft';
const draftRestored = ref(false);
const storageError = ref('');
const draftFields = ['pic_name', 'position', 'work_email', 'phone', 'name', 'budget_range', 'source'];

const positionOptions = ['HR', 'General Affairs', 'Procurement', 'Finance', 'Management', 'Other'];
const budgetOptions = [
    { value: 'under-10m', label: 'Di bawah Rp10 juta' },
    { value: '10m-50m', label: 'Rp10–50 juta' },
    { value: '50m-100m', label: 'Rp50–100 juta' },
    { value: 'over-100m', label: 'Di atas Rp100 juta' },
];
const sourceOptions = [
    { value: 'search', label: 'Pencarian internet' },
    { value: 'social', label: 'Media sosial' },
    { value: 'partner', label: 'Rekomendasi partner' },
    { value: 'event', label: 'Acara / komunitas' },
    { value: 'other', label: 'Lainnya' },
];

onMounted(() => {
    try {
        const raw = sessionStorage.getItem(draftKey);
        if (! raw) return;
        const draft = JSON.parse(raw);
        if (! draft || ! Number.isFinite(draft.expiresAt) || draft.expiresAt <= Date.now()) {
            sessionStorage.removeItem(draftKey);
            return;
        }
        for (const key of draftFields) {
            if (typeof draft.fields?.[key] === 'string') form[key] = draft.fields[key];
        }
        draftRestored.value = true;
    } catch {
        storageError.value = 'Isian sebelumnya tidak dapat dipulihkan. Silakan periksa kembali formulir.';
    }
});

function submit() {
    form.post(route('corporate.register.store'), {
        onSuccess: () => {
            try { sessionStorage.removeItem(draftKey); } catch { /* Storage may be unavailable in private browsing. */ }
        },
    });
}
</script>

<template>
    <Head title="Daftar Perusahaan — TapakLokal Corporate" />
    <div class="corporate-register flex min-h-screen flex-col bg-[#f5f9fd] text-[#173451]">
        <!-- Header -->
        <header class="border-b border-[#e4edf5] bg-white">
            <div class="mx-auto flex max-w-[1040px] items-center justify-between gap-4 px-5 py-2.5 sm:px-8">
                <Link :href="route('business.corporate')" class="flex items-center gap-3">
                    <img src="/Assets/Images/logo.webp" alt="TapakLokal Logo" class="h-8 w-auto object-contain" />
                    <span class="border-l border-slate-200 pl-3 text-[9px] font-bold tracking-[.16em]">FOR<br />CORPORATES</span>
                </Link>
                <Link
                    :href="route('corporate.login')"
                    class="group inline-flex items-center gap-1.5 rounded-full border border-[#0088ff] bg-[#f0f7ff] px-3.5 py-1 text-xs font-semibold text-[#0088ff] transition-all duration-200 ease-in-out hover:border-[#0088ff] hover:bg-[#0088ff] hover:text-white active:scale-[0.98] sm:text-sm"
                >
                    <span>Masuk corporate</span>
                    <ArrowRight :stroke-width="2.5" class="size-3.5 transition-transform duration-200 ease-in-out group-hover:translate-x-1" />
                </Link>
            </div>
        </header>

        <!-- Main Card Container (Centered in 1 Frame) -->
        <main class="mx-auto flex flex-1 w-full max-w-[1040px] items-center justify-center px-4 py-3 sm:px-6 lg:py-4">
            <div class="relative w-full rounded-[24px] border border-[#e3ecf5] bg-white shadow-[0_15px_45px_-18px_rgba(38,91,140,.16)] sm:rounded-[28px] lg:grid lg:grid-cols-[0.82fr_1.18fr]">
                <!-- Left Column (Brand & Info) -->
                <aside class="hidden flex-col justify-center rounded-l-[24px] border-b border-[#e4edf5] bg-gradient-to-br from-[#eaf4fd] via-[#f4f9fd] to-[#e6f1fb] p-6 sm:p-7 sm:rounded-l-[28px] lg:flex lg:border-b-0 lg:border-r">
                    <div>
                        <h1 class="text-[28px] font-bold leading-[1.15] tracking-[-.035em] sm:text-[32px] lg:text-[34px]">
                            Perjalanan tim,<br /><span class="text-[#0088ff]">lebih terarah.</span>
                        </h1>

                        <ol class="mt-4 space-y-2">
                            <li v-for="(item, index) in ['Daftarkan perusahaan dan PIC', 'Tim kami memverifikasi pengajuan', 'Undang tim dan mulai ajukan perjalanan']" :key="item" class="flex items-center gap-2.5 rounded-xl border border-white/80 bg-white/70 px-3 py-2 shadow-2xs backdrop-blur-xs">
                                <span class="grid size-5 shrink-0 place-items-center rounded-full bg-[#0088ff] text-[10px] font-bold text-white shadow-xs">{{ index + 1 }}</span>
                                <span class="text-[12px] font-semibold text-[#173451]">{{ item }}</span>
                            </li>
                        </ol>

                        <div class="mt-4 flex justify-center">
                            <img src="/Assets/Images/Corporate/login.svg" alt="Ilustrasi registrasi corporate" class="h-[145px] w-full max-w-[240px] object-contain" />
                        </div>
                    </div>
                </aside>

                <!-- Right Column (Registration Form) -->
                <section class="p-5 sm:p-6 lg:p-7" aria-labelledby="corporate-register-heading">
                    <div class="mb-3.5">
                        <p class="text-[9px] font-bold uppercase tracking-[.15em] text-[#228cd1]">Registrasi perusahaan</p>
                        <h2 id="corporate-register-heading" class="mt-0.5 text-[20px] font-bold leading-tight tracking-tight sm:text-[22px]">Daftarkan perusahaanmu</h2>
                        <p class="mt-1 text-[11.5px] leading-4 text-[#7186a0]">Lengkapi data PIC dan profil perusahaan untuk memulai workspace tim Anda.</p>
                    </div>

                    <p v-if="draftRestored" role="status" class="mb-3 rounded-xl bg-sky-50 p-2.5 text-xs text-sky-800">
                        Isian sebelumnya sudah dipulihkan. Periksa data dan konfirmasi persetujuan sebelum mengirim pengajuan.
                    </p>
                    <p v-if="storageError" role="alert" class="mb-3 rounded-xl bg-red-50 p-2.5 text-xs text-red-700">
                        {{ storageError }}
                    </p>

                    <form @submit.prevent="submit">
                        <fieldset :disabled="form.processing" class="space-y-3">
                            <div class="grid gap-2.5 sm:grid-cols-2">
                                <label class="auth-label">
                                    Nama PIC *
                                    <span class="auth-field">
                                        <UserRound class="auth-field-icon" />
                                        <input v-model="form.pic_name" required maxlength="150" autocomplete="name" placeholder="Nama lengkap PIC" />
                                    </span>
                                </label>
                                <label class="auth-label">
                                    Jabatan *
                                    <CustomSelect
                                        v-model="form.position"
                                        :options="positionOptions"
                                        :icon="Briefcase"
                                        placeholder="Pilih jabatan"
                                        required
                                        class="mt-1"
                                    />
                                </label>
                                <label class="auth-label">
                                    Email kantor *
                                    <span class="auth-field">
                                        <Mail class="auth-field-icon" />
                                        <input v-model="form.work_email" required type="email" autocomplete="email" placeholder="pic@perusahaan.com" />
                                    </span>
                                </label>
                                <label class="auth-label">
                                    Nomor kontak *
                                    <span class="auth-field">
                                        <Phone class="auth-field-icon" />
                                        <input v-model="form.phone" required type="tel" autocomplete="tel" placeholder="+62 812 3456 7890" />
                                    </span>
                                </label>
                                <label class="auth-label sm:col-span-2">
                                    Nama resmi perusahaan *
                                    <span class="auth-field">
                                        <Building2 class="auth-field-icon" />
                                        <input v-model="form.name" required maxlength="180" autocomplete="organization" placeholder="PT Nama Perusahaan Indonesia" />
                                    </span>
                                </label>

                                <!-- Password fields (shown when not logged in) -->
                                <template v-if="!loggedIn">
                                    <label class="auth-label">
                                        Kata sandi akun *
                                        <span class="auth-field">
                                            <LockKeyhole class="auth-field-icon" />
                                            <input
                                                v-model="form.password"
                                                :type="showPassword ? 'text' : 'password'"
                                                required
                                                minlength="8"
                                                maxlength="128"
                                                autocomplete="new-password"
                                                placeholder="Minimal 8 karakter"
                                            />
                                            <button
                                                type="button"
                                                class="grid size-7 shrink-0 place-items-center rounded-lg text-[#7c92ac] hover:text-[#0088ff]"
                                                :aria-label="showPassword ? 'Sembunyikan kata sandi' : 'Tampilkan kata sandi'"
                                                @click="showPassword = !showPassword"
                                            >
                                                <component :is="showPassword ? EyeOff : Eye" class="size-3.5" />
                                            </button>
                                        </span>
                                    </label>
                                    <label class="auth-label">
                                        Ulangi kata sandi *
                                        <span class="auth-field">
                                            <LockKeyhole class="auth-field-icon" />
                                            <input
                                                v-model="form.password_confirmation"
                                                type="password"
                                                required
                                                minlength="8"
                                                maxlength="128"
                                                autocomplete="new-password"
                                                placeholder="Ketik ulang kata sandi"
                                            />
                                        </span>
                                    </label>
                                </template>

                                <label class="auth-label">
                                    Anggaran perjalanan bulanan *
                                    <CustomSelect
                                        v-model="form.budget_range"
                                        :options="budgetOptions"
                                        :icon="Wallet"
                                        placeholder="Pilih kisaran"
                                        required
                                        class="mt-1"
                                    />
                                </label>
                                <label class="auth-label">
                                    Mengetahui TapakLokal dari *
                                    <CustomSelect
                                        v-model="form.source"
                                        :options="sourceOptions"
                                        :icon="Compass"
                                        placeholder="Pilih sumber"
                                        required
                                        class="mt-1"
                                    />
                                </label>
                            </div>

                            <!-- Persetujuan -->
                            <div class="border-t border-[#eaf1f7] pt-2.5 text-[11px] leading-4 text-[#6b829e]">
                                <label class="flex cursor-pointer items-start gap-2.5">
                                    <input v-model="form.consent" required type="checkbox" class="mt-0.5 size-3.5 shrink-0 accent-[#0088ff]" />
                                    <span>Saya menyatakan data benar dan mengizinkan TapakLokal menggunakan data ini untuk verifikasi serta menghubungi PIC terkait layanan perusahaan. *</span>
                                </label>
                            </div>
                        </fieldset>

                        <p v-for="(error, key) in form.errors" :key="key" role="alert" class="mt-2 rounded-lg bg-red-50 px-2.5 py-1.5 text-xs leading-4 text-red-700">
                            {{ error }}
                        </p>

                        <button
                            :disabled="form.processing"
                            class="mt-4 flex min-h-[42px] w-full items-center justify-center gap-2 rounded-full bg-[#0088ff] px-4 py-2.5 text-xs font-bold text-white shadow-[0_4px_12px_-4px_rgba(0,136,255,.4)] transition hover:bg-[#0078e2] active:scale-[0.98] disabled:cursor-wait disabled:opacity-60 sm:text-sm"
                        >
                            <span>{{ form.processing ? 'Memproses pengajuan…' : loggedIn ? 'Ajukan registrasi perusahaan' : 'Daftar & ajukan perusahaan' }}</span>
                            <ArrowRight class="size-4" />
                        </button>

                        <p class="mt-3 text-center text-xs text-[#7c92ac]">
                            Sudah punya akun corporate?
                            <Link :href="route('corporate.login')" class="font-bold text-[#0088ee] hover:underline">Masuk corporate</Link>
                        </p>
                    </form>
                </section>
            </div>
        </main>
    </div>
</template>

<style scoped>
@reference "../../css/app.css";
.auth-label { @apply block text-[11px] font-semibold text-[#385675]; }
.auth-field { @apply mt-1 flex min-h-[38px] items-center gap-2.5 rounded-xl border border-[#dfe8f2] bg-[#fbfdff] px-3 py-1 transition focus-within:border-[#0088ff] focus-within:bg-white focus-within:ring-2 focus-within:ring-[#0088ff]/10; }
.auth-field-icon { @apply size-4 shrink-0 text-[#8daac4]; }
.auth-field input { @apply min-w-0 flex-1 border-0 bg-transparent py-1.5 text-[12.5px] font-normal text-[#173451] outline-none placeholder:text-[#a1b1c5]; }
.auth-select { @apply min-w-0 flex-1 border-0 bg-transparent py-1.5 text-[12.5px] font-normal text-[#173451] outline-none; }
.corporate-register button:focus-visible, .corporate-register a:focus-visible { outline: 2px solid #0088ff; outline-offset: 3px; }
</style>
