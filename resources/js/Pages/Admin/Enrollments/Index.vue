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
const selectedStatus = ref(props.filters?.status || '');

const searchEnrollments = () => {
    router.get('/admin/enrollments', {
        search: search.value,
        status: selectedStatus.value
    }, { preserveState: true });
};

const deleteEnrollment = (id) => {
    if (confirm('Are you sure you want to delete this enrollment?')) {
        router.delete(`/admin/enrollments/${id}`);
    }
};

const updateStatus = (id, newStatus) => {
    router.post(`/admin/enrollments/${id}/update-status`, {
        status: newStatus
    });
};
</script>

<template>
    <AdminLayout>
        <Head title="Manage Enrollments" />
        
        <div class="py-6">
            <!-- Header -->
            <div class="mb-6">
                <h1 class="text-3xl font-bold text-gray-900">📝 Manage Enrollments</h1>
                <p class="text-gray-600 mt-1">Assign teachers, batches and manage student enrollments</p>
            </div>
            
            <!-- Statistics Cards -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
                <div class="bg-white rounded-lg shadow p-6">
                    <div class="text-sm text-gray-600">Total Enrollments</div>
                    <div class="text-3xl font-bold text-gray-900">{{ stats.total }}</div>
                </div>
                <div class="bg-white rounded-lg shadow p-6">
                    <div class="text-sm text-gray-600">Active</div>
                    <div class="text-3xl font-bold text-emerald-600">{{ stats.active }}</div>
                </div>
                <div class="bg-white rounded-lg shadow p-6">
                    <div class="text-sm text-gray-600">Pending</div>
                    <div class="text-3xl font-bold text-orange-600">{{ stats.pending }}</div>
                </div>
                <div class="bg-white rounded-lg shadow p-6">
                    <div class="text-sm text-gray-600">Completed</div>
                    <div class="text-3xl font-bold text-blue-600">{{ stats.completed }}</div>
                </div>
            </div>
            
            <!-- Search & Filter -->
            <div class="bg-white rounded-lg shadow p-4 mb-6">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <input 
                        v-model="search"
                        @keyup.enter="searchEnrollments"
                        type="text" 
                        placeholder="Search by student name or course..."
                        class="px-4 py-2 border rounded-lg focus:ring-2 focus:ring-emerald-500"
                    />
                    <select 
                        v-model="selectedStatus"
                        @change="searchEnrollments"
                        class="px-4 py-2 border rounded-lg focus:ring-2 focus:ring-emerald-500"
                    >
                        <option value="">All Status</option>
                        <option value="pending">Pending</option>
                        <option value="active">Active</option>
                        <option value="completed">Completed</option>
                        <option value="cancelled">Cancelled</option>
                    </select>
                    <button 
                        @click="searchEnrollments"
                        class="px-4 py-2 bg-gray-700 text-white rounded-lg hover:bg-gray-800"
                    >
                        Search
                    </button>
                </div>
            </div>
            
            <!-- Enrollments Table -->
            <div class="bg-white rounded-lg shadow overflow-hidden">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Student</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Course</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Batch/Teacher</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Type</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Progress</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <tr v-for="enroll in enrollments.data" :key="enroll.id" class="hover:bg-gray-50">
                            <td class="px-6 py-4">
                                <div class="text-sm font-medium text-gray-900">{{ enroll.user?.name }}</div>
                                <div class="text-sm text-gray-500">{{ enroll.user?.email }}</div>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-900">{{ enroll.course?.title }}</td>
                            <td class="px-6 py-4">
                                <div v-if="enroll.batch" class="text-sm">
                                    <div class="font-medium text-gray-900">{{ enroll.batch.name }}</div>
                                    <div class="text-gray-500">{{ enroll.batch.teacher?.name }}</div>
                                </div>
                                <span v-else class="text-sm text-gray-400">Not assigned</span>
                            </td>
                            <td class="px-6 py-4">
                                <span :class="[
                                    'px-2 py-1 text-xs rounded-full',
                                    enroll.type === 'private' ? 'bg-purple-100 text-purple-800' : 'bg-blue-100 text-blue-800'
                                ]">
                                    {{ enroll.type }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center">
                                    <div class="w-16 bg-gray-200 rounded-full h-2 mr-2">
                                        <div class="bg-emerald-600 h-2 rounded-full" 
                                             :style="{ width: (enroll.progress || 0) + '%' }"></div>
                                    </div>
                                    <span class="text-sm text-gray-600">{{ Math.round(enroll.progress || 0) }}%</span>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <select 
                                    :value="enroll.status"
                                    @change="updateStatus(enroll.id, $event.target.value)"
                                    :class="[
                                        'px-2 py-1 text-xs rounded-full border-0',
                                        enroll.status === 'active' ? 'bg-emerald-100 text-emerald-800' :
                                        enroll.status === 'pending' ? 'bg-orange-100 text-orange-800' :
                                        enroll.status === 'completed' ? 'bg-blue-100 text-blue-800' :
                                        'bg-gray-100 text-gray-800'
                                    ]"
                                >
                                    <option value="pending">Pending</option>
                                    <option value="active">Active</option>
                                    <option value="completed">Completed</option>
                                    <option value="cancelled">Cancelled</option>
                                </select>
                            </td>
                            <td class="px-6 py-4 text-sm space-x-2">
                                <Link :href="`/admin/enrollments/${enroll.id}/edit`" 
                                      class="text-emerald-600 hover:text-emerald-900 font-semibold">
                                    Assign
                                </Link>
                                <button 
                                    @click="deleteEnrollment(enroll.id)"
                                    class="text-red-600 hover:text-red-900">
                                    Delete
                                </button>
                            </td>
                        </tr>
                        <tr v-if="enrollments.data.length === 0">
                            <td colspan="7" class="px-6 py-12 text-center text-gray-500">
                                No enrollments found.
                            </td>
                        </tr>
                    </tbody>
                </table>
                
                <!-- Pagination -->
                <div v-if="enrollments.links" class="px-6 py-4 bg-gray-50 border-t">
                    <div class="flex justify-between items-center">
                        <div class="text-sm text-gray-700">
                            Showing {{ enrollments.from }} to {{ enrollments.to }} of {{ enrollments.total }}
                        </div>
                        <div class="flex gap-2">
                            <template v-for="link in enrollments.links" :key="link.label">
                                <Link v-if="link.url"
                                      :href="link.url"
                                      :class="[
                                          'px-3 py-1 border rounded',
                                          link.active ? 'bg-emerald-600 text-white' : 'bg-white text-gray-700 hover:bg-gray-50'
                                      ]"
                                      v-html="link.label"
                                />
                                <span v-else
                                      class="px-3 py-1 border rounded bg-gray-100 text-gray-400 cursor-not-allowed"
                                      v-html="link.label"
                                />
                            </template>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>
