<script setup>
import { Head, Link, router, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';
import AdminLayout from '@/Layouts/AdminLayout.vue';

defineProps({
    auth: Object,
    batches: {
        type: Object,
        default: () => ({ data: [], meta: {} })
    },
    filters: Object
});

const search = ref('');
const statusFilter = ref('');
const showDeleteModal = ref(false);
const batchToDelete = ref(null);

const deleteForm = useForm({});

const searchBatches = () => {
    router.get('/admin/batches', { 
        search: search.value,
        status: statusFilter.value
    }, { preserveState: true });
};

const confirmDelete = (batch) => {
    batchToDelete.value = batch;
    showDeleteModal.value = true;
};

const deleteBatch = () => {
    deleteForm.delete(`/admin/batches/${batchToDelete.value.id}`, {
        onSuccess: () => {
            showDeleteModal.value = false;
            batchToDelete.value = null;
        },
    });
};

const getStatusBadge = (status) => {
    const badges = {
        'upcoming': 'bg-blue-100 text-blue-700',
        'active': 'bg-green-100 text-green-700',
        'completed': 'bg-gray-100 text-gray-700',
        'cancelled': 'bg-red-100 text-red-700',
    };
    return badges[status] || 'bg-gray-100 text-gray-700';
};

const formatDate = (dateString) => {
    if (!dateString) return 'N/A';
    return new Date(dateString).toLocaleDateString('en-US', {
        month: 'short',
        day: 'numeric',
        year: 'numeric'
    });
};
</script>

<template>
    <Head title="Batches | Admin" />

    <AdminLayout>
        <!-- Header -->
        <div class="flex items-center justify-between mb-6">
            <h1 class="text-2xl font-bold text-gray-900">Batch Management</h1>
            <Link
                href="/admin/batches/create"
                class="inline-flex items-center px-4 py-2 bg-primary-500 hover:bg-primary-600 text-white text-sm font-medium rounded-lg transition-colors"
            >
                + Create Batch
            </Link>
        </div>

        <!-- Filters & Search -->
        <div class="bg-white rounded-xl shadow-sm p-4 mb-6">
            <div class="flex items-center justify-between gap-4">
                <div class="flex-1 max-w-md">
                    <div class="relative">
                        <input
                            v-model="search"
                            @keyup.enter="searchBatches"
                            type="text"
                            placeholder="Search batches..."
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
                        @change="searchBatches"
                        class="rounded-lg border-gray-200 text-sm"
                    >
                        <option value="">All Status</option>
                        <option value="upcoming">Upcoming</option>
                        <option value="active">Active</option>
                        <option value="completed">Completed</option>
                        <option value="cancelled">Cancelled</option>
                    </select>
                </div>
            </div>
        </div>

        <!-- Batches Table -->
        <div class="bg-white rounded-xl shadow-sm overflow-hidden">
            <table class="w-full">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Batch</th>
                        <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Course</th>
                        <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Teacher</th>
                        <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Schedule</th>
                        <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Students</th>
                        <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-4 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    <tr v-for="batch in batches.data" :key="batch.id" class="hover:bg-gray-50">
                        <td class="px-6 py-4">
                            <div>
                                <div class="font-medium text-gray-900">{{ batch.name }}</div>
                                <div class="text-sm text-gray-500">Starts: {{ formatDate(batch.start_date) }}</div>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-600">{{ batch.course?.title || 'N/A' }}</td>
                        <td class="px-6 py-4 text-sm text-gray-600">{{ batch.teacher?.name || 'Unassigned' }}</td>
                        <td class="px-6 py-4 text-sm text-gray-500">{{ batch.formatted_schedule || 'Not set' }}</td>
                        <td class="px-6 py-4">
                            <div class="text-sm">
                                <span class="font-medium text-gray-900">{{ batch.enrolled_count || 0 }}</span>
                                <span class="text-gray-400">/ {{ batch.max_students }}</span>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <span :class="['px-2 py-1 text-xs font-medium rounded-full capitalize', getStatusBadge(batch.status)]">
                                {{ batch.status }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-right">
                            <div class="flex items-center justify-end gap-2">
                                <Link
                                    :href="`/admin/batches/${batch.id}/edit`"
                                    class="p-2 text-gray-400 hover:text-primary-500"
                                    title="Edit"
                                >
                                    ✏️
                                </Link>
                                <button
                                    @click="confirmDelete(batch)"
                                    class="p-2 text-gray-400 hover:text-red-500"
                                    title="Delete"
                                >
                                    🗑️
                                </button>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>

            <!-- Empty State -->
            <div v-if="!batches.data?.length" class="text-center py-12">
                <span class="text-4xl mb-4 block">📅</span>
                <h3 class="text-lg font-medium text-gray-900 mb-2">No batches yet</h3>
                <p class="text-gray-500 mb-4">Create your first batch to start enrolling students.</p>
                <Link href="/admin/batches/create" class="text-primary-500 hover:text-primary-600 font-medium">
                    + Create Batch
                </Link>
            </div>

            <!-- Pagination -->
            <div v-if="batches.meta?.last_page > 1" class="px-6 py-4 border-t border-gray-200">
                <div class="flex items-center justify-between">
                    <p class="text-sm text-gray-500">
                        Page {{ batches.meta.current_page }} of {{ batches.meta.last_page }}
                    </p>
                    <div class="flex gap-2">
                        <Link
                            v-if="batches.meta.current_page > 1"
                            :href="`/admin/batches?page=${batches.meta.current_page - 1}`"
                            class="px-3 py-1 border border-gray-200 rounded-lg text-sm hover:bg-gray-50"
                        >
                            Previous
                        </Link>
                        <Link
                            v-if="batches.meta.current_page < batches.meta.last_page"
                            :href="`/admin/batches?page=${batches.meta.current_page + 1}`"
                            class="px-3 py-1 border border-gray-200 rounded-lg text-sm hover:bg-gray-50"
                        >
                            Next
                        </Link>
                    </div>
                </div>
            </div>
        </div>

        <!-- Delete Modal -->
        <div v-if="showDeleteModal" class="fixed inset-0 z-50 overflow-y-auto">
            <div class="flex items-center justify-center min-h-screen px-4">
                <div class="fixed inset-0 bg-black/50" @click="showDeleteModal = false"></div>
                <div class="relative bg-white rounded-2xl p-6 w-full max-w-md">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Delete Batch</h3>
                    <p class="text-gray-600 mb-6">
                        Are you sure you want to delete "{{ batchToDelete?.name }}"?
                        This will also remove all associated class sessions.
                    </p>
                    <div class="flex justify-end gap-3">
                        <button
                            @click="showDeleteModal = false"
                            class="px-4 py-2 text-gray-600 hover:text-gray-900"
                        >
                            Cancel
                        </button>
                        <button
                            @click="deleteBatch"
                            :disabled="deleteForm.processing"
                            class="px-4 py-2 bg-red-500 hover:bg-red-600 text-white rounded-lg"
                        >
                            {{ deleteForm.processing ? 'Deleting...' : 'Delete' }}
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>
