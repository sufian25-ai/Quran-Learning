<script setup>
import { Head, Link, router } from '@inertiajs/vue3';
import { ref } from 'vue';
import AdminLayout from '@/Layouts/AdminLayout.vue';

const props = defineProps({
    teachers: Object,
    filters: Object
});

const search = ref(props.filters?.search || '');

const getStatusColor = (status) => {
    const colors = {
        'approved': 'bg-green-100 text-green-700',
        'pending': 'bg-yellow-100 text-yellow-700',
        'rejected': 'bg-red-100 text-red-700',
    };
    return colors[status] || 'bg-gray-100 text-gray-700';
};

const approveTeacher = (id) => {
    router.post(`/admin/teachers/${id}/approve`, {}, { preserveScroll: true });
};

const rejectTeacher = (id) => {
    router.post(`/admin/teachers/${id}/reject`, {}, { preserveScroll: true });
};

const doSearch = () => {
    router.get('/admin/teachers', { search: search.value }, { preserveState: true });
};
</script>

<template>
    <Head title="Manage Teachers" />

    <AdminLayout>
        <!-- Header -->
        <div class="flex items-center justify-between mb-6">
            <h1 class="text-2xl font-bold text-gray-900">Manage Teachers</h1>
        </div>

        <!-- Search -->
        <div class="mb-6 flex items-center space-x-4">
            <div class="flex-1 max-w-md">
                <input
                    v-model="search"
                    @keyup.enter="doSearch"
                    type="text"
                    placeholder="Search teachers..."
                    class="w-full px-4 py-2 rounded-xl border border-gray-200 focus:border-primary-500 focus:ring-2 focus:ring-primary-500/20"
                />
            </div>
            <button @click="doSearch" class="px-4 py-2 bg-primary-500 text-white rounded-xl">
                Search
            </button>
        </div>

        <!-- Teachers Table -->
        <div class="bg-white rounded-2xl shadow-sm overflow-hidden">
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-4 text-left text-sm font-medium text-gray-500">Teacher</th>
                        <th class="px-6 py-4 text-left text-sm font-medium text-gray-500">Email</th>
                        <th class="px-6 py-4 text-left text-sm font-medium text-gray-500">Status</th>
                        <th class="px-6 py-4 text-left text-sm font-medium text-gray-500">Batches</th>
                        <th class="px-6 py-4 text-left text-sm font-medium text-gray-500">Rating</th>
                        <th class="px-6 py-4 text-left text-sm font-medium text-gray-500">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    <tr v-for="teacher in teachers.data" :key="teacher.id" class="hover:bg-gray-50">
                        <td class="px-6 py-4">
                            <div class="flex items-center">
                                <div class="w-10 h-10 rounded-full bg-primary-100 flex items-center justify-center text-primary-600 font-semibold">
                                    {{ teacher.name.charAt(0) }}
                                </div>
                                <span class="ml-3 font-medium text-gray-900">{{ teacher.name }}</span>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-gray-600">{{ teacher.email }}</td>
                        <td class="px-6 py-4">
                            <span :class="['px-3 py-1 text-xs font-medium rounded-full', getStatusColor(teacher.status)]">
                                {{ teacher.status }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-gray-600">{{ teacher.batches_count }}</td>
                        <td class="px-6 py-4">
                            <span class="text-yellow-500">⭐</span>
                            {{ teacher.rating?.toFixed(1) || 'N/A' }}
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex space-x-2">
                                <button
                                    v-if="teacher.status === 'pending'"
                                    @click="approveTeacher(teacher.id)"
                                    class="px-3 py-1 text-xs bg-green-500 text-white rounded-lg hover:bg-green-600"
                                >
                                    Approve
                                </button>
                                <button
                                    v-if="teacher.status === 'pending'"
                                    @click="rejectTeacher(teacher.id)"
                                    class="px-3 py-1 text-xs bg-red-500 text-white rounded-lg hover:bg-red-600"
                                >
                                    Reject
                                </button>
                                <Link
                                    :href="`/admin/teachers/${teacher.id}`"
                                    class="px-3 py-1 text-xs border border-gray-200 rounded-lg hover:bg-gray-50"
                                >
                                    View
                                </Link>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>

            <div v-if="!teachers.data?.length" class="p-12 text-center text-gray-500">
                No teachers found
            </div>
        </div>
    </AdminLayout>
</template>
