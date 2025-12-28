<script setup>
import { Head, Link, router } from '@inertiajs/vue3';
import { ref } from 'vue';
import AdminLayout from '@/Layouts/AdminLayout.vue';

const props = defineProps({
    enrollments: Object,
    stats: Object,
    filters: Object
});

const search = ref(props.filters?.search || '');
const statusFilter = ref(props.filters?.status || '');

const getStatusColor = (status) => {
    const colors = {
        'active': 'bg-green-100 text-green-700',
        'pending': 'bg-yellow-100 text-yellow-700',
        'completed': 'bg-blue-100 text-blue-700',
        'cancelled': 'bg-red-100 text-red-700',
    };
    return colors[status] || 'bg-gray-100 text-gray-700';
};

const formatCurrency = (amount) => {
    return new Intl.NumberFormat('en-US', { style: 'currency', currency: 'USD' }).format(amount || 0);
};

const formatDate = (date) => {
    return new Date(date).toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' });
};

const activateEnrollment = (id) => {
    router.post(`/admin/enrollments/${id}/activate`, {}, { preserveScroll: true });
};

const deleteEnrollment = (id) => {
    if (confirm('Are you sure you want to delete this enrollment?')) {
        router.delete(`/admin/enrollments/${id}`, { preserveScroll: true });
    }
};

const doSearch = () => {
    router.get('/admin/enrollments', { search: search.value, status: statusFilter.value }, { preserveState: true });
};
</script>

<template>
    <Head title="Manage Enrollments" />

    <AdminLayout>
        <!-- Header -->
        <div class="mb-6">
            <h1 class="text-2xl font-bold text-gray-900">Manage Enrollments</h1>
        </div>

        <!-- Stats -->
        <div class="grid grid-cols-4 gap-4 mb-6">
            <div class="bg-white rounded-xl p-4 shadow-sm">
                <p class="text-2xl font-bold text-gray-900">{{ stats?.total || 0 }}</p>
                <p class="text-sm text-gray-500">Total</p>
            </div>
            <div class="bg-white rounded-xl p-4 shadow-sm">
                <p class="text-2xl font-bold text-green-600">{{ stats?.active || 0 }}</p>
                <p class="text-sm text-gray-500">Active</p>
            </div>
            <div class="bg-white rounded-xl p-4 shadow-sm">
                <p class="text-2xl font-bold text-yellow-600">{{ stats?.pending || 0 }}</p>
                <p class="text-sm text-gray-500">Pending</p>
            </div>
            <div class="bg-white rounded-xl p-4 shadow-sm">
                <p class="text-2xl font-bold text-blue-600">{{ stats?.completed || 0 }}</p>
                <p class="text-sm text-gray-500">Completed</p>
            </div>
        </div>

        <!-- Search & Filter -->
        <div class="mb-6 flex items-center space-x-4">
            <input
                v-model="search"
                @keyup.enter="doSearch"
                type="text"
                placeholder="Search by student or course..."
                class="flex-1 max-w-md px-4 py-2 rounded-xl border border-gray-200 focus:border-primary-500"
            />
            <select
                v-model="statusFilter"
                @change="doSearch"
                class="px-4 py-2 rounded-xl border border-gray-200"
            >
                <option value="">All Status</option>
                <option value="active">Active</option>
                <option value="pending">Pending</option>
                <option value="completed">Completed</option>
                <option value="cancelled">Cancelled</option>
            </select>
            <button @click="doSearch" class="px-4 py-2 bg-primary-500 text-white rounded-xl">
                Search
            </button>
        </div>

        <!-- Enrollments Table -->
        <div class="bg-white rounded-2xl shadow-sm overflow-hidden">
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-4 text-left text-sm font-medium text-gray-500">Student</th>
                        <th class="px-6 py-4 text-left text-sm font-medium text-gray-500">Course</th>
                        <th class="px-6 py-4 text-left text-sm font-medium text-gray-500">Batch</th>
                        <th class="px-6 py-4 text-left text-sm font-medium text-gray-500">Type</th>
                        <th class="px-6 py-4 text-left text-sm font-medium text-gray-500">Amount</th>
                        <th class="px-6 py-4 text-left text-sm font-medium text-gray-500">Status</th>
                        <th class="px-6 py-4 text-left text-sm font-medium text-gray-500">Date</th>
                        <th class="px-6 py-4 text-left text-sm font-medium text-gray-500">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    <tr v-for="enrollment in enrollments.data" :key="enrollment.id" class="hover:bg-gray-50">
                        <td class="px-6 py-4">
                            <div>
                                <p class="font-medium text-gray-900">{{ enrollment.user?.name }}</p>
                                <p class="text-xs text-gray-500">{{ enrollment.user?.email }}</p>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-gray-600">{{ enrollment.course?.title }}</td>
                        <td class="px-6 py-4 text-gray-600">{{ enrollment.batch?.name || '-' }}</td>
                        <td class="px-6 py-4">
                            <span :class="[
                                'px-2 py-1 text-xs rounded-full',
                                enrollment.type === 'private' ? 'bg-purple-100 text-purple-700' : 'bg-blue-100 text-blue-700'
                            ]">
                                {{ enrollment.type }}
                            </span>
                        </td>
                        <td class="px-6 py-4 font-medium text-gray-900">{{ formatCurrency(enrollment.amount) }}</td>
                        <td class="px-6 py-4">
                            <span :class="['px-3 py-1 text-xs font-medium rounded-full', getStatusColor(enrollment.status)]">
                                {{ enrollment.status }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-gray-500 text-sm">{{ formatDate(enrollment.created_at) }}</td>
                        <td class="px-6 py-4">
                            <div class="flex space-x-2">
                                <button
                                    v-if="enrollment.status === 'pending'"
                                    @click="activateEnrollment(enrollment.id)"
                                    class="px-3 py-1 text-xs bg-green-500 text-white rounded-lg hover:bg-green-600"
                                >
                                    Activate
                                </button>
                                <button
                                    @click="deleteEnrollment(enrollment.id)"
                                    class="px-3 py-1 text-xs text-red-600 hover:text-red-800"
                                >
                                    🗑️
                                </button>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>

            <div v-if="!enrollments.data?.length" class="p-12 text-center text-gray-500">
                No enrollments found
            </div>
        </div>
    </AdminLayout>
</template>
