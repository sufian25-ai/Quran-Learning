<script setup>
import { Head, Link, router, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';
import AdminLayout from '@/Layouts/AdminLayout.vue';

defineProps({
    auth: Object,
    courses: {
        type: Object,
        default: () => ({ data: [], meta: {} })
    },
    filters: Object
});

const search = ref('');
const showDeleteModal = ref(false);
const courseToDelete = ref(null);

const deleteForm = useForm({});

const searchCourses = () => {
    router.get('/admin/courses', { search: search.value }, { preserveState: true });
};

const confirmDelete = (course) => {
    courseToDelete.value = course;
    showDeleteModal.value = true;
};

const deleteCourse = () => {
    deleteForm.delete(`/admin/courses/${courseToDelete.value.id}`, {
        onSuccess: () => {
            showDeleteModal.value = false;
            courseToDelete.value = null;
        },
    });
};

const getStatusBadge = (status) => {
    const badges = {
        'published': 'bg-green-100 text-green-700',
        'draft': 'bg-yellow-100 text-yellow-700',
        'archived': 'bg-gray-100 text-gray-700',
    };
    return badges[status] || 'bg-gray-100 text-gray-700';
};

const formatPrice = (price) => {
    return new Intl.NumberFormat('en-US', {
        style: 'currency',
        currency: 'USD',
        minimumFractionDigits: 0,
    }).format(price || 0);
};
</script>

<template>
    <Head title="Courses | Admin" />

    <AdminLayout>
        <!-- Header -->
        <div class="flex items-center justify-between mb-6">
            <h1 class="text-2xl font-bold text-gray-900">Course Management</h1>
            <Link
                href="/admin/courses/create"
                class="inline-flex items-center px-4 py-2 bg-primary-500 hover:bg-primary-600 text-white text-sm font-medium rounded-lg transition-colors"
            >
                + Add Course
            </Link>
        </div>

        <!-- Filters & Search -->
        <div class="bg-white rounded-xl shadow-sm p-4 mb-6">
            <div class="flex items-center justify-between gap-4">
                <div class="flex-1 max-w-md">
                    <div class="relative">
                        <input
                            v-model="search"
                            @keyup.enter="searchCourses"
                            type="text"
                            placeholder="Search courses..."
                            class="w-full pl-10 pr-4 py-2 rounded-lg border-gray-200 focus:border-primary-500 focus:ring-primary-500"
                        />
                        <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                    </div>
                </div>
                <div class="flex items-center gap-2">
                    <select class="rounded-lg border-gray-200 text-sm">
                        <option>All Status</option>
                        <option value="published">Published</option>
                        <option value="draft">Draft</option>
                        <option value="archived">Archived</option>
                    </select>
                    <select class="rounded-lg border-gray-200 text-sm">
                        <option>All Categories</option>
                        <option value="quran_reading">Quran Reading</option>
                        <option value="tajweed">Tajweed</option>
                        <option value="hifz">Hifz</option>
                    </select>
                </div>
            </div>
        </div>

        <!-- Courses Table -->
        <div class="bg-white rounded-xl shadow-sm overflow-hidden">
            <table class="w-full">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Course</th>
                        <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Category</th>
                        <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Level</th>
                        <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Price</th>
                        <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Enrollments</th>
                        <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-4 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    <tr v-for="course in courses.data" :key="course.id" class="hover:bg-gray-50">
                        <td class="px-6 py-4">
                            <div class="flex items-center">
                                <div class="w-10 h-10 rounded-lg bg-primary-100 flex items-center justify-center text-lg">
                                    📖
                                </div>
                                <div class="ml-4">
                                    <div class="font-medium text-gray-900">{{ course.title }}</div>
                                    <div class="text-sm text-gray-500">{{ course.slug }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-600">{{ course.category }}</td>
                        <td class="px-6 py-4 text-sm text-gray-600 capitalize">{{ course.level }}</td>
                        <td class="px-6 py-4">
                            <div class="text-sm font-medium text-gray-900">{{ formatPrice(course.price_group) }}</div>
                            <div class="text-xs text-gray-500">Group</div>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-900">{{ course.total_enrollments || 0 }}</td>
                        <td class="px-6 py-4">
                            <span :class="['px-2 py-1 text-xs font-medium rounded-full', getStatusBadge(course.status)]">
                                {{ course.status }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-right">
                            <div class="flex items-center justify-end gap-2">
                                <Link
                                    :href="`/admin/courses/${course.id}/edit`"
                                    class="p-2 text-gray-400 hover:text-primary-500"
                                    title="Edit"
                                >
                                    ✏️
                                </Link>
                                <button
                                    @click="confirmDelete(course)"
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
            <div v-if="!courses.data?.length" class="text-center py-12">
                <span class="text-4xl mb-4 block">📚</span>
                <h3 class="text-lg font-medium text-gray-900 mb-2">No courses yet</h3>
                <p class="text-gray-500 mb-4">Get started by creating your first course.</p>
                <Link href="/admin/courses/create" class="text-primary-500 hover:text-primary-600 font-medium">
                    + Add Course
                </Link>
            </div>

            <!-- Pagination -->
            <div v-if="courses.meta?.last_page > 1" class="px-6 py-4 border-t border-gray-200">
                <div class="flex items-center justify-between">
                    <p class="text-sm text-gray-500">
                        Showing {{ courses.meta.current_page }} of {{ courses.meta.last_page }} pages
                    </p>
                    <div class="flex gap-2">
                        <Link
                            v-if="courses.meta.current_page > 1"
                            :href="`/admin/courses?page=${courses.meta.current_page - 1}`"
                            class="px-3 py-1 border border-gray-200 rounded-lg text-sm hover:bg-gray-50"
                        >
                            Previous
                        </Link>
                        <Link
                            v-if="courses.meta.current_page < courses.meta.last_page"
                            :href="`/admin/courses?page=${courses.meta.current_page + 1}`"
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
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Delete Course</h3>
                    <p class="text-gray-600 mb-6">
                        Are you sure you want to delete "{{ courseToDelete?.title }}"?
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
                            @click="deleteCourse"
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
