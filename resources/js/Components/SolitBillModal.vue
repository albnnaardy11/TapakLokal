<script setup>
import { ref, computed } from 'vue';
import {
    X,
    ReceiptText,
    Users,
    Plus,
    Trash2,
    Share2,
    Copy,
    Check,
    Calculator
} from 'lucide-vue-next';

const props = defineProps({
    isOpen: {
        type: Boolean,
        default: false,
    },
});

const emit = defineEmits(['close']);

const tripTitle = ref('Open Trip Bromo Savana 2026');
const memberCount = ref(4);
const copied = ref(false);

const expenses = ref([
    { id: 1, name: 'Sewa Jeep Bromo 4x4', amount: 850000 },
    { id: 2, name: 'Tiket Masuk TNBTS (4 Orang)', amount: 160000 },
    { id: 3, name: 'Makan Rawon Nguling Bareng', amount: 240000 },
    { id: 4, name: 'Logistik Kopi & Snack Penanjakan', amount: 90000 },
]);

const newExpenseName = ref('');
const newExpenseAmount = ref('');

function addExpense() {
    if (!newExpenseName.value || !newExpenseAmount.value) return;
    expenses.value.push({
        id: Date.now(),
        name: newExpenseName.value,
        amount: Number(newExpenseAmount.value),
    });
    newExpenseName.value = '';
    newExpenseAmount.value = '';
}

function removeExpense(id) {
    expenses.value = expenses.value.filter(e => e.id !== id);
}

const totalExpense = computed(() => {
    return expenses.value.reduce((acc, curr) => acc + curr.amount, 0);
});

const perPersonShare = computed(() => {
    if (!memberCount.value || memberCount.value <= 0) return 0;
    return Math.ceil(totalExpense.value / memberCount.value);
});

function formatRupiah(num) {
    return new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: 'IDR',
        maximumFractionDigits: 0,
    }).format(num);
}

function copySummary() {
    const text = `🧾 *Solit Bill TapakLokal*
Trip: ${tripTitle.value}
Jumlah Teman: ${memberCount.value} Orang
Total Biaya: ${formatRupiah(totalExpense.value)}
---------------------------
👉 *Patungan Per Orang:* ${formatRupiah(perPersonShare.value)}

Hitung otomatis di TapakLokal!`;
    navigator.clipboard.writeText(text);
    copied.value = true;
    setTimeout(() => {
        copied.value = false;
    }, 2000);
}
</script>

<template>
    <div
        v-if="isOpen"
        class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60 backdrop-blur-xs transition-opacity"
    >
        <div class="bg-white rounded-3xl max-w-lg w-full p-6 shadow-2xl border border-gray-100 relative overflow-hidden max-h-[90vh] flex flex-col">
            <!-- Modal Header -->
            <div class="flex items-center justify-between pb-4 border-b border-gray-100">
                <div class="flex items-center gap-2.5">
                    <div class="w-10 h-10 rounded-xl bg-blue-50 text-[#0052cc] flex items-center justify-center font-bold">
                        <ReceiptText class="w-5 h-5" />
                    </div>
                    <div>
                        <h3 class="font-bold text-slate-900 text-lg leading-tight">Solit Bill Petualang</h3>
                        <p class="text-xs text-slate-500">Patungan trip adil tanpa selisih koin</p>
                    </div>
                </div>
                <button
                    type="button"
                    @click="emit('close')"
                    class="p-2 text-slate-400 hover:text-slate-700 rounded-full hover:bg-slate-100 transition cursor-pointer"
                >
                    <X class="w-5 h-5" />
                </button>
            </div>

            <!-- Body Content -->
            <div class="space-y-4 py-4 overflow-y-auto flex-1 pr-1">
                <!-- Trip Name & Member Count -->
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-bold text-slate-600 mb-1">Nama Trip</label>
                        <input
                            v-model="tripTitle"
                            type="text"
                            class="w-full text-xs bg-slate-50 border border-slate-200 rounded-xl px-3 py-2 font-medium focus:ring-2 focus:ring-[#0052cc] focus:outline-none"
                        />
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-600 mb-1">Jumlah Teman Patungan</label>
                        <input
                            v-model.number="memberCount"
                            type="number"
                            min="1"
                            class="w-full text-xs bg-slate-50 border border-slate-200 rounded-xl px-3 py-2 font-bold text-[#0052cc] focus:ring-2 focus:ring-[#0052cc] focus:outline-none"
                        />
                    </div>
                </div>

                <!-- Expense List -->
                <div>
                    <label class="block text-xs font-bold text-slate-600 mb-2">Rincian Pengeluaran Trip</label>
                    <div class="space-y-2">
                        <div
                            v-for="item in expenses"
                            :key="item.id"
                            class="flex items-center justify-between bg-slate-50 px-3 py-2 rounded-xl text-xs font-medium border border-slate-100"
                        >
                            <span class="text-slate-700">{{ item.name }}</span>
                            <div class="flex items-center gap-3">
                                <span class="font-bold text-slate-900">{{ formatRupiah(item.amount) }}</span>
                                <button
                                    type="button"
                                    @click="removeExpense(item.id)"
                                    class="text-slate-400 hover:text-red-500 transition cursor-pointer"
                                >
                                    <Trash2 class="w-3.5 h-3.5" />
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Add Expense Form -->
                    <div class="grid grid-cols-12 gap-2 mt-3 pt-3 border-t border-gray-100">
                        <input
                            v-model="newExpenseName"
                            type="text"
                            placeholder="Pengeluaran baru..."
                            class="col-span-6 text-xs bg-white border border-slate-200 rounded-xl px-3 py-1.5 focus:ring-2 focus:ring-[#0052cc] focus:outline-none"
                        />
                        <input
                            v-model="newExpenseAmount"
                            type="number"
                            placeholder="Nominal (Rp)"
                            class="col-span-4 text-xs bg-white border border-slate-200 rounded-xl px-3 py-1.5 focus:ring-2 focus:ring-[#0052cc] focus:outline-none"
                        />
                        <button
                            type="button"
                            @click="addExpense"
                            class="col-span-2 bg-[#0052cc] hover:bg-[#0042a3] text-white rounded-xl text-xs font-bold flex items-center justify-center cursor-pointer"
                        >
                            <Plus class="w-4 h-4" />
                        </button>
                    </div>
                </div>

                <!-- Per Person Calculation Result Card -->
                <div class="bg-gradient-to-r from-blue-50 to-indigo-50 border border-blue-200/70 rounded-2xl p-4 text-center">
                    <div class="text-[11px] font-bold text-slate-500 uppercase tracking-wider">
                        Total Biaya: {{ formatRupiah(totalExpense) }} / {{ memberCount }} Orang
                    </div>
                    <div class="text-2xl font-black text-[#0052cc] mt-1">
                        {{ formatRupiah(perPersonShare) }}
                        <span class="text-xs font-normal text-slate-500">/ orang</span>
                    </div>
                </div>
            </div>

            <!-- Footer Action -->
            <div class="pt-3 border-t border-gray-100 flex items-center gap-2">
                <button
                    type="button"
                    @click="copySummary"
                    class="flex-1 bg-[#0052cc] hover:bg-[#0042a3] text-white py-2.5 px-4 rounded-xl text-xs font-bold transition flex items-center justify-center gap-1.5 cursor-pointer shadow-sm"
                >
                    <Check v-if="copied" class="w-4 h-4" />
                    <Copy v-else class="w-4 h-4" />
                    <span>{{ copied ? 'Tersalin!' : 'Salin Rincian ke WhatsApp' }}</span>
                </button>
                <button
                    type="button"
                    @click="emit('close')"
                    class="px-4 py-2.5 rounded-xl border border-gray-200 text-xs font-bold text-slate-600 hover:bg-gray-50 transition cursor-pointer"
                >
                    Tutup
                </button>
            </div>
        </div>
    </div>
</template>
