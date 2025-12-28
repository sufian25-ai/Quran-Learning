<script setup>
import { Head, Link, router } from '@inertiajs/vue3';
import { ref } from 'vue';
import AdminLayout from '@/Layouts/AdminLayout.vue';

const props = defineProps({
    tickets: Object,
    stats: Object,
    filters: Object
});

const search = ref(props.filters?.search || '');
const statusFilter = ref(props.filters?.status || '');

const getStatusColor = (status) => {
    const colors = {
        'open': 'bg-red-100 text-red-700',
        'pending': 'bg-yellow-100 text-yellow-700',
        'resolved': 'bg-green-100 text-green-700',
        'closed': 'bg-gray-100 text-gray-700',
    };
    return colors[status] || 'bg-gray-100 text-gray-700';
};

const getPriorityColor = (priority) => {
    const colors = {
        'high': 'text-red-600',
        'normal': 'text-yellow-600',
        'low': 'text-gray-600',
    };
    return colors[priority] || 'text-gray-600';
};

const formatDate = (date) => {
    return new Date(date).toLocaleDateString('en-US', { month: 'short', day: 'numeric', hour: '2-digit', minute: '2-digit' });
};

const closeTicket = (id) => {
    router.post(`/admin/tickets/${id}/close`, {}, { preserveScroll: true });
};

const doSearch = () => {
    router.get('/admin/tickets', { search: search.value, status: statusFilter.value }, { preserveState: true });
};
</script>

<template>
    <Head title="Support Tickets" />

    <AdminLayout>
        <!-- Header -->
        <div class="mb-6">
            <h1 class="text-2xl font-bold text-gray-900">Support Tickets</h1>
        </div>

        <!-- Stats -->
        <div class="grid grid-cols-4 gap-4 mb-6">
            <div class="bg-white rounded-xl p-4 shadow-sm">
                <p class="text-2xl font-bold text-gray-900">{{ stats?.total || 0 }}</p>
                <p class="text-sm text-gray-500">Total Tickets</p>
            </div>
            <div class="bg-white rounded-xl p-4 shadow-sm">
                <p class="text-2xl font-bold text-red-600">{{ stats?.open || 0 }}</p>
                <p class="text-sm text-gray-500">Open</p>
            </div>
            <div class="bg-white rounded-xl p-4 shadow-sm">
                <p class="text-2xl font-bold text-yellow-600">{{ stats?.pending || 0 }}</p>
                <p class="text-sm text-gray-500">Pending</p>
            </div>
            <div class="bg-white rounded-xl p-4 shadow-sm">
                <p class="text-2xl font-bold text-green-600">{{ stats?.resolved || 0 }}</p>
                <p class="text-sm text-gray-500">Resolved</p>
            </div>
        </div>

        <!-- Search & Filter -->
        <div class="mb-6 flex items-center space-x-4">
            <input
                v-model="search"
                @keyup.enter="doSearch"
                type="text"
                placeholder="Search tickets..."
                class="flex-1 max-w-md px-4 py-2 rounded-xl border border-gray-200 focus:border-primary-500"
            />
            <select
                v-model="statusFilter"
                @change="doSearch"
                class="px-4 py-2 rounded-xl border border-gray-200"
            >
                <option value="">All Status</option>
                <option value="open">Open</option>
                <option value="pending">Pending</option>
                <option value="resolved">Resolved</option>
            </select>
            <button @click="doSearch" class="px-4 py-2 bg-primary-500 text-white rounded-xl">
                Search
            </button>
        </div>

        <!-- Tickets Table -->
        <div class="bg-white rounded-2xl shadow-sm overflow-hidden">
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-4 text-left text-sm font-medium text-gray-500">Subject</th>
                        <th class="px-6 py-4 text-left text-sm font-medium text-gray-500">User</th>
                        <th class="px-6 py-4 text-left text-sm font-medium text-gray-500">Status</th>
                        <th class="px-6 py-4 text-left text-sm font-medium text-gray-500">Priority</th>
                        <th class="px-6 py-4 text-left text-sm font-medium text-gray-500">Date</th>
                        <th class="px-6 py-4 text-left text-sm font-medium text-gray-500">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    <tr v-for="ticket in tickets.data" :key="ticket.id" class="hover:bg-gray-50">
                        <td class="px-6 py-4">
                            <p class="font-medium text-gray-900">{{ ticket.subject }}</p>
                            <p class="text-xs text-gray-500 mt-1">{{ ticket.message }}</p>
                        </td>
                        <td class="px-6 py-4">
                            <p class="text-gray-900">{{ ticket.user?.name }}</p>
                            <p class="text-xs text-gray-500">{{ ticket.user?.email }}</p>
                        </td>
                        <td class="px-6 py-4">
                            <span :class="['px-3 py-1 text-xs font-medium rounded-full', getStatusColor(ticket.status)]">
                                {{ ticket.status }}
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            <span :class="['text-sm font-medium', getPriorityColor(ticket.priority)]">
                                {{ ticket.priority }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-gray-500 text-sm">{{ formatDate(ticket.created_at) }}</td>
                        <td class="px-6 py-4">
                            <div class="flex space-x-2">
                                <Link
                                    :href="`/admin/tickets/${ticket.id}`"
                                    class="px-3 py-1 text-xs bg-primary-500 text-white rounded-lg hover:bg-primary-600"
                                >
                                    View
                                </Link>
                                <button
                                    v-if="ticket.status !== 'resolved'"
                                    @click="closeTicket(ticket.id)"
                                    class="px-3 py-1 text-xs border border-gray-200 rounded-lg hover:bg-gray-50"
                                >
                                    Close
                                </button>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>

            <div v-if="!tickets.data?.length" class="p-12 text-center text-gray-500">
                No tickets found
            </div>
        </div>
    </AdminLayout>
</template>
