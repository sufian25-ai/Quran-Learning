<script setup>
import { Head, Link, router, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';
import AdminLayout from '@/Layouts/AdminLayout.vue';

defineProps({
    auth: Object,
    users: {
        type: Object,
        default: () => ({ data: [], meta: {} })
    },
    filters: Object
});

const search = ref('');
const roleFilter = ref('');
const showDeleteModal = ref(false);
const userToDelete = ref(null);

const deleteForm = useForm({});

const searchUsers = () => {
    router.get('/admin/users', { 
        search: search.value,
        role: roleFilter.value
    }, { preserveState: true });
};

const confirmDelete = (user) => {
    userToDelete.value = user;
    showDeleteModal.value = true;
};

const deleteUser = () => {
    deleteForm.delete(`/admin/users/${userToDelete.value.id}`, {
        onSuccess: () => {
            showDeleteModal.value = false;
            userToDelete.value = null;
        },
    });
};

const getRoleBadge = (roles) => {
    if (!roles || !roles.length) return { class: 'bg-gray-100 text-gray-700', label: 'User' };
    const role = roles[0];
    const badges = {
        'admin': { class: 'bg-red-100 text-red-700', label: 'Admin' },
        'teacher': { class: 'bg-blue-100 text-blue-700', label: 'Teacher' },
        'student': { class: 'bg-green-100 text-green-700', label: 'Student' },
    };
    return badges[role] || { class: 'bg-gray-100 text-gray-700', label: role };
};

const getStatusBadge = (isActive) => {
    return isActive
        ? 'bg-green-100 text-green-700'
        : 'bg-gray-100 text-gray-700';
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
    <Head title="Users | Admin" />

    <AdminLayout>
        <!-- Header -->
        <div class="flex items-center justify-between mb-6">
            <h1 class="text-2xl font-bold text-gray-900">User Management</h1>
            <Link
                href="/admin/users/create"
                class="inline-flex items-center px-4 py-2 bg-primary-500 hover:bg-primary-600 text-white text-sm font-medium rounded-lg transition-colors"
            >
                + Add User
            </Link>
        </div>

        <!-- Filters & Search -->
        <div class="bg-white rounded-xl shadow-sm p-4 mb-6">
            <div class="flex items-center justify-between gap-4">
                <div class="flex-1 max-w-md">
                    <div class="relative">
                        <input
                            v-model="search"
                            @keyup.enter="searchUsers"
                            type="text"
                            placeholder="Search users by name or email..."
                            class="w-full pl-10 pr-4 py-2 rounded-lg border-gray-200 focus:border-primary-500 focus:ring-primary-500"
                        />
                        <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                    </div>
                </div>
                <div class="flex items-center gap-2">
                    <select 
                        v-model="roleFilter"
                        @change="searchUsers"
                        class="rounded-lg border-gray-200 text-sm"
                    >
                        <option value="">All Roles</option>
                        <option value="admin">Admin</option>
                        <option value="teacher">Teacher</option>
                        <option value="student">Student</option>
                    </select>
                    <select class="rounded-lg border-gray-200 text-sm">
                        <option>All Status</option>
                        <option value="active">Active</option>
                        <option value="inactive">Inactive</option>
                    </select>
                </div>
            </div>
        </div>

        <!-- Users Table -->
        <div class="bg-white rounded-xl shadow-sm overflow-hidden">
            <table class="w-full">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">User</th>
                        <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Role</th>
                        <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Joined</th>
                        <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Last Login</th>
                        <th class="px-6 py-4 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    <tr v-for="user in users.data" :key="user.id" class="hover:bg-gray-50">
                        <td class="px-6 py-4">
                            <div class="flex items-center">
                                <div class="w-10 h-10 rounded-full bg-primary-100 flex items-center justify-center text-primary-700 font-medium">
                                    {{ user.name?.charAt(0).toUpperCase() }}
                                </div>
                                <div class="ml-4">
                                    <div class="font-medium text-gray-900">{{ user.name }}</div>
                                    <div class="text-sm text-gray-500">{{ user.email }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <span :class="['px-2 py-1 text-xs font-medium rounded-full', getRoleBadge(user.roles).class]">
                                {{ getRoleBadge(user.roles).label }}
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            <span :class="['px-2 py-1 text-xs font-medium rounded-full', getStatusBadge(user.is_active)]">
                                {{ user.is_active ? 'Active' : 'Inactive' }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-600">{{ formatDate(user.created_at) }}</td>
                        <td class="px-6 py-4 text-sm text-gray-600">{{ formatDate(user.last_login_at) }}</td>
                        <td class="px-6 py-4 text-right">
                            <div class="flex items-center justify-end gap-2">
                                <Link
                                    :href="`/admin/users/${user.id}/edit`"
                                    class="p-2 text-gray-400 hover:text-primary-500"
                                    title="Edit"
                                >
                                    ✏️
                                </Link>
                                <button
                                    @click="confirmDelete(user)"
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
            <div v-if="!users.data?.length" class="text-center py-12">
                <span class="text-4xl mb-4 block">👥</span>
                <h3 class="text-lg font-medium text-gray-900 mb-2">No users found</h3>
                <p class="text-gray-500 mb-4">Try adjusting your search or filters.</p>
            </div>

            <!-- Pagination -->
            <div v-if="users.meta?.last_page > 1" class="px-6 py-4 border-t border-gray-200">
                <div class="flex items-center justify-between">
                    <p class="text-sm text-gray-500">
                        Showing {{ users.meta.current_page }} of {{ users.meta.last_page }} pages
                    </p>
                    <div class="flex gap-2">
                        <Link
                            v-if="users.meta.current_page > 1"
                            :href="`/admin/users?page=${users.meta.current_page - 1}`"
                            class="px-3 py-1 border border-gray-200 rounded-lg text-sm hover:bg-gray-50"
                        >
                            Previous
                        </Link>
                        <Link
                            v-if="users.meta.current_page < users.meta.last_page"
                            :href="`/admin/users?page=${users.meta.current_page + 1}`"
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
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Delete User</h3>
                    <p class="text-gray-600 mb-6">
                        Are you sure you want to delete "{{ userToDelete?.name }}"?
                        This action cannot be undone.
                    </p>
                    <div class="flex justify-end gap-3">
                        <button
                            @click="showDeleteModal = false"
                            class="px-4 py-2 text-gray-600 hover:text-gray-900"
                        >
                            Cancel
                        </button>
                        <button
                            @click="deleteUser"
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
