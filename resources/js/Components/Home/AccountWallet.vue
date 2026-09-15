<script setup>
import { CreditCard, ShieldCheck, Wallet } from 'lucide-vue-next';
defineEmits(['transactions']);
</script>
<template>
    <section>
        <h2 class="text-xl font-extrabold text-[#26364d]">Saldo & Pembayaran</h2>
        <p class="mt-2 text-xs text-slate-500">Ringkasan saldo dan metode pembayaran akunmu.</p>
        <div class="mt-5 rounded-2xl bg-gradient-to-br from-[#1045a5] to-[#078cff] p-6 text-white"><Wallet class="size-6" /><p class="mt-4 text-xs text-white/75">Saldo tersedia</p><p class="mt-2 text-3xl font-bold">Rp 0</p><p class="mt-4 text-[11px] leading-5 text-white/75">Saldo contoh. Isi saldo dan penarikan belum tersedia.</p></div>
        <div class="mt-4 rounded-2xl border border-[#e1e8f3] bg-white p-6"><div class="flex items-center gap-3"><CreditCard class="size-5 text-[#078cff]" /><h3 class="text-sm font-bold">Metode pembayaran</h3></div><p class="mt-4 text-sm leading-6 text-slate-500">Belum ada metode pembayaran tersimpan. Pilihan pembayaran mengikuti metode yang tersedia pada pesanan.</p><button class="mt-5 rounded-lg bg-[#1045a5] px-4 py-2.5 text-xs font-semibold text-white hover:bg-[#083788]" @click="$emit('transactions')">Lihat daftar transaksi</button></div>
        <p class="mt-4 flex items-start gap-2 text-xs leading-5 text-slate-400"><ShieldCheck class="size-4 shrink-0" />Informasi saldo adalah pratinjau dan belum terhubung ke layanan pembayaran.</p>
    </section>
</template>
