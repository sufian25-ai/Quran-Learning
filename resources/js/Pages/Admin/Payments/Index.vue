<script setup>
import { Head, Link, router } from '@inertiajs/vue3';
import { ref } from 'vue';
import AdminLayout from '@/Layouts/AdminLayout.vue';

defineProps({
    auth: Object,
    payments: {
        type: Object,
        default: () => ({ data: [], meta: {} })
    },
    stats: {
        type: Object,
        default: () => ({})
    },
    filters: Object
});

const search = ref('');
const statusFilter = ref('');
const dateRange = ref('');

const searchPayments = () => {
    router.get('/admin/payments', { 
        search: search.value,
        status: statusFilter.value,
        date_range: dateRange.value
    }, { preserveState: true });
};

const getStatusBadge = (status) => {
    const badges = {
        'completed': 'bg-green-100 text-green-700',
        'pending': 'bg-yellow-100 text-yellow-700',
        'failed': 'bg-red-100 text-red-700',
        'refunded': 'bg-purple-100 text-purple-700',
    };
    return badges[status] || 'bg-gray-100 text-gray-700';
};

const formatCurrency = (amount) => {
    return new Intl.NumberFormat('en-US', {
        style: 'currency',
        currency: 'USD',
    }).format(amount || 0);
};

const formatDate = (dateString) => {
    if (!dateString) return 'N/A';
    return new Date(dateString).toLocaleDateString('en-US', {
        month: 'short',
        day: 'numeric',
        year: 'numeric',
        hour: '2-digit',
        minute: '2-digit',
    });
};

const getGatewayIcon = (gateway) => {
    const icons = {
        'stripe': '💳',
        'sslcommerz': '🏦',
        'paypal': '🅿️',
        'manual': '📝',
    };
    return icons[gateway] || '💰';
};
</script>

<template>
    <Head title="Payments | Admin" />

    <AdminLayout>
        <!-- Header -->
        <div class="flex items-center justify-between mb-6">
            <h1 class="text-2xl font-bold text-gray-900">Payment Management</h1>
            <button class="inline-flex items-center px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm font-medium rounded-lg transition-colors">
                📊 Export Report
            </button>
        </div>

        <!-- Stats Cards -->
        <div class="grid grid-cols-4 gap-6 mb-6">
            <div class="bg-white rounded-xl shadow-sm p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-500">Total Revenue</p>
                        <p class="text-2xl font-bold text-gray-900">{{ formatCurrency(stats.total_revenue) }}</p>
                    </div>
                    <span class="text-3xl">💰</span>
                </div>
            </div>
            <div class="bg-white rounded-xl shadow-sm p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-500">This Month</p>
                        <p class="text-2xl font-bold text-green-600">{{ formatCurrency(stats.monthly_revenue) }}</p>
                    </div>
                    <span class="text-3xl">📈</span>
                </div>
            </div>
            <div class="bg-white rounded-xl shadow-sm p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-500">Pending</p>
                        <p class="text-2xl font-bold text-yellow-600">{{ formatCurrency(stats.pending_amount) }}</p>
                    </div>
                    <span class="text-3xl">⏳</span>
                </div>
            </div>
            <div class="bg-white rounded-xl shadow-sm p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-500">Total Transactions</p>
                        <p class="text-2xl font-bold text-gray-900">{{ stats.total_transactions || 0 }}</p>
                    </div>
                    <span class="text-3xl">📋</span>
                </div>
            </div>
        </div>

        <!-- Filters & Search -->
        <div class="bg-white rounded-xl shadow-sm p-4 mb-6">
            <div class="flex items-center justify-between gap-4">
                <div class="flex-1 max-w-md">
                    <div class="relative">
                        <input
                            v-model="search"
                            @keyup.enter="searchPayments"
                            type="text"
                            placeholder="Search by transaction ID or user..."
                            class="w-full pl-10 pr-4 py-2 rounded-lg border-gray-200 focus:border-primary-500 focus:ring-primary-500"
                        />
                        <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                    </div>
                </div>
                <div class="flex items-center gap-2">
                    <select 
                        v-model="statusFilter"
                        @change="searchPayments"
                        class="rounded-lg border-gray-200 text-sm"
                    >
                        <option value="">All Status</option>
                        <option value="completed">Completed</option>
                        <option value="pending">Pending</option>
                        <option value="failed">Failed</option>
                        <option value="refunded">Refunded</option>
                    </select>
                    <select 
                        v-model="dateRange"
                        @change="searchPayments"
                        class="rounded-lg border-gray-200 text-sm"
                    >
                        <option value="">All Time</option>
                        <option value="today">Today</option>
                        <option value="week">This Week</option>
                        <option value="month">This Month</option>
                        <option value="year">This Year</option>
                    </select>
                </div>
            </div>
        </div>

        <!-- Payments Table -->
        <div class="bg-white rounded-xl shadow-sm overflow-hidden">
            <table class="w-full">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Transaction</th>
                        <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">User</th>
                        <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Course</th>
                        <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Gateway</th>
                        <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Amount</th>
                        <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                        <th class="px-6 py-4 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    <tr v-for="payment in payments.data" :key="payment.id" class="hover:bg-gray-50">
                        <td class="px-6 py-4">
                            <div class="font-mono text-sm text-gray-900">{{ payment.transaction_id }}</div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex items-center">
                                <div class="w-8 h-8 rounded-full bg-primary-100 flex items-center justify-center text-primary-700 text-xs font-medium">
                                    {{ payment.user?.name?.charAt(0).toUpperCase() }}
                                </div>
                                <div class="ml-3">
                                    <div class="text-sm font-medium text-gray-900">{{ payment.user?.name }}</div>
                                    <div class="text-xs text-gray-500">{{ payment.user?.email }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-600">{{ payment.enrollment?.course?.title || 'N/A' }}</td>
                        <td class="px-6 py-4">
                            <div class="flex items-center text-sm text-gray-600">
                                <span class="mr-2">{{ getGatewayIcon(payment.gateway) }}</span>
                                <span class="capitalize">{{ payment.gateway }}</span>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <span class="font-medium text-gray-900">{{ formatCurrency(payment.amount) }}</span>
                        </td>
                        <td class="px-6 py-4">
                            <span :class="['px-2 py-1 text-xs font-medium rounded-full capitalize', getStatusBadge(payment.status)]">
                                {{ payment.status }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-500">{{ formatDate(payment.paid_at || payment.created_at) }}</td>
                        <td class="px-6 py-4 text-right">
                            <div class="flex items-center justify-end gap-2">
                                <button class="p-2 text-gray-400 hover:text-primary-500" title="View Details">
                                    👁️
                                </button>
                                <button 
                                    v-if="payment.status === 'completed'"
                                    class="p-2 text-gray-400 hover:text-orange-500" 
                                    title="Process Refund"
                                >
                                    ↩️
                                </button>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>

            <!-- Empty State -->
            <div v-if="!payments.data?.length" class="text-center py-12">
                <span class="text-4xl mb-4 block">💳</span>
                <h3 class="text-lg font-medium text-gray-900 mb-2">No payments found</h3>
                <p class="text-gray-500">Payments will appear here once students start enrolling.</p>
            </div>

            <!-- Pagination -->
            <div v-if="payments.meta?.last_page > 1" class="px-6 py-4 border-t border-gray-200">
                <div class="flex items-center justify-between">
                    <p class="text-sm text-gray-500">
                        Page {{ payments.meta.current_page }} of {{ payments.meta.last_page }}
                    </p>
                    <div class="flex gap-2">
                        <Link
                            v-if="payments.meta.current_page > 1"
                            :href="`/admin/payments?page=${payments.meta.current_page - 1}`"
                            class="px-3 py-1 border border-gray-200 rounded-lg text-sm hover:bg-gray-50"
                        >
                            Previous
                        </Link>
                        <Link
                            v-if="payments.meta.current_page < payments.meta.last_page"
                            :href="`/admin/payments?page=${payments.meta.current_page + 1}`"
                            class="px-3 py-1 border border-gray-200 rounded-lg text-sm hover:bg-gray-50"
                        >
                            Next
                        </Link>
                    </div>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>
