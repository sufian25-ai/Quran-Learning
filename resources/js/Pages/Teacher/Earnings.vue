<script setup>
import { Head, Link } from '@inertiajs/vue3';
import TeacherLayout from '@/Layouts/TeacherLayout.vue';

defineProps({
    auth: Object,
    stats: {
        type: Object,
        default: () => ({
            total_earnings: 0,
            classes_this_month: 0,
            rate_per_class: 500,
            pending_payout: 0,
        })
    },
    monthlyEarnings: {
        type: Array,
        default: () => []
    }
});

const formatCurrency = (amount) => {
    return new Intl.NumberFormat('en-BD', { style: 'currency', currency: 'BDT' }).format(amount);
};
</script>

<template>
    <Head title="Earnings | Teacher" />

    <TeacherLayout>
        <template #header>
            <div>
                <h2 class="font-display text-xl font-bold text-gray-900">
                    Earnings 💰
                </h2>
                <p class="text-gray-500 text-sm">Track your teaching income</p>
            </div>
        </template>

        <div class="space-y-6">
            <!-- Stats -->
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
                <div class="group bg-gradient-to-br from-emerald-500 to-teal-600 rounded-2xl p-5 text-white transition-all hover:scale-105 hover:shadow-xl">
                    <div class="flex items-center justify-between mb-3">
                        <span class="text-3xl group-hover:animate-bounce">💰</span>
                    </div>
                    <p class="text-2xl font-bold">{{ formatCurrency(stats.total_earnings) }}</p>
                    <p class="text-emerald-100 text-sm">This Month</p>
                </div>
                <div class="group bg-gradient-to-br from-blue-500 to-indigo-600 rounded-2xl p-5 text-white transition-all hover:scale-105 hover:shadow-xl">
                    <div class="flex items-center justify-between mb-3">
                        <span class="text-3xl group-hover:animate-bounce">📅</span>
                    </div>
                    <p class="text-2xl font-bold">{{ stats.classes_this_month }}</p>
                    <p class="text-blue-100 text-sm">Classes Completed</p>
                </div>
                <div class="group bg-gradient-to-br from-amber-500 to-orange-600 rounded-2xl p-5 text-white transition-all hover:scale-105 hover:shadow-xl">
                    <div class="flex items-center justify-between mb-3">
                        <span class="text-3xl group-hover:animate-bounce">⏱️</span>
                    </div>
                    <p class="text-2xl font-bold">{{ formatCurrency(stats.rate_per_class) }}</p>
                    <p class="text-amber-100 text-sm">Rate Per Class</p>
                </div>
                <div class="group bg-gradient-to-br from-purple-500 to-pink-600 rounded-2xl p-5 text-white transition-all hover:scale-105 hover:shadow-xl">
                    <div class="flex items-center justify-between mb-3">
                        <span class="text-3xl group-hover:animate-bounce">🏦</span>
                    </div>
                    <p class="text-2xl font-bold">{{ formatCurrency(stats.pending_payout) }}</p>
                    <p class="text-purple-100 text-sm">Pending Payout</p>
                </div>
            </div>

            <!-- Monthly Breakdown -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-6">📊 Monthly Breakdown</h3>
                
                <div class="space-y-4">
                    <div
                        v-for="month in monthlyEarnings"
                        :key="month.month"
                        class="flex items-center justify-between p-4 rounded-xl bg-gray-50 hover:bg-emerald-50 transition-colors"
                    >
                        <div>
                            <p class="font-medium text-gray-900">{{ month.month }}</p>
                            <p class="text-sm text-gray-500">{{ month.classes }} classes completed</p>
                        </div>
                        <div class="text-right">
                            <p class="text-lg font-bold text-emerald-600">{{ formatCurrency(month.earnings) }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Payout Info -->
            <div class="bg-gradient-to-br from-slate-700 to-slate-800 rounded-2xl p-6 text-white">
                <h3 class="font-semibold mb-4">💳 Payout Information</h3>
                <p class="text-slate-300 mb-4">Payouts are processed on the 1st of every month via bank transfer.</p>
                <div class="flex gap-3">
                    <Link
                        href="/settings"
                        class="px-4 py-2 bg-white/10 hover:bg-white/20 rounded-xl transition-colors"
                    >
                        Update Bank Details
                    </Link>
                </div>
            </div>
        </div>
    </TeacherLayout>
</template>
