<script setup>
import { Head, Link, router } from '@inertiajs/vue3';
import { ref, computed } from 'vue';
import AdminLayout from '@/Layouts/AdminLayout.vue';

const props = defineProps({
    reviews: {
        type: Array,
        default: () => []
    },
    filters: {
        type: Object,
        default: () => ({ status: 'pending' })
    }
});

const selectedStatus = ref(props.filters.status || 'pending');

const filteredReviews = computed(() => {
    if (selectedStatus.value === 'all') {
        return props.reviews;
    }
    return props.reviews.filter(r => r.status === selectedStatus.value);
});

const statusOptions = [
    { value: 'pending', label: 'Pending', color: 'bg-yellow-100 text-yellow-700' },
    { value: 'approved', label: 'Approved', color: 'bg-green-100 text-green-700' },
    { value: 'rejected', label: 'Rejected', color: 'bg-red-100 text-red-700' },
    { value: 'all', label: 'All', color: 'bg-gray-100 text-gray-700' },
];

const getStatusColor = (status) => {
    const option = statusOptions.find(o => o.value === status);
    return option?.color || 'bg-gray-100 text-gray-700';
};

const formatDate = (date) => {
    return new Date(date).toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'short',
        day: 'numeric'
    });
};

const renderStars = (rating) => {
    return '⭐'.repeat(rating) + '☆'.repeat(5 - rating);
};

const approveReview = (reviewId) => {
    router.post(`/admin/reviews/${reviewId}/approve`, {}, { preserveScroll: true });
};

const rejectReview = (reviewId) => {
    router.post(`/admin/reviews/${reviewId}/reject`, {}, { preserveScroll: true });
};

const deleteReview = (reviewId) => {
    if (confirm('Are you sure you want to delete this review?')) {
        router.delete(`/admin/reviews/${reviewId}`, { preserveScroll: true });
    }
};
</script>

<template>
    <Head title="Review Moderation" />

    <AdminLayout>
        <!-- Header -->
        <div class="mb-6">
            <h1 class="text-2xl font-bold text-gray-900">Review Moderation</h1>
        </div>

        <!-- Stats -->
        <div class="grid grid-cols-4 gap-4 mb-6">
            <div 
                v-for="option in statusOptions.slice(0, 3)" 
                :key="option.value"
                class="bg-white rounded-xl p-4 shadow-sm cursor-pointer transition-all hover:shadow-md"
                :class="{ 'ring-2 ring-primary-500': selectedStatus === option.value }"
                @click="selectedStatus = option.value"
            >
                <p class="text-2xl font-bold text-gray-900">
                    {{ reviews.filter(r => r.status === option.value).length }}
                </p>
                <p class="text-sm text-gray-500">{{ option.label }}</p>
            </div>
            <div 
                class="bg-white rounded-xl p-4 shadow-sm cursor-pointer transition-all hover:shadow-md"
                :class="{ 'ring-2 ring-primary-500': selectedStatus === 'all' }"
                @click="selectedStatus = 'all'"
            >
                <p class="text-2xl font-bold text-gray-900">{{ reviews.length }}</p>
                <p class="text-sm text-gray-500">Total</p>
            </div>
        </div>

        <!-- Reviews List -->
        <div class="bg-white rounded-2xl shadow-sm overflow-hidden">
            <div class="p-6 border-b border-gray-100">
                <h3 class="font-semibold text-gray-900">
                    {{ selectedStatus === 'all' ? 'All' : statusOptions.find(o => o.value === selectedStatus)?.label }} Reviews
                </h3>
            </div>

            <div v-if="filteredReviews.length" class="divide-y divide-gray-100">
                <div 
                    v-for="review in filteredReviews" 
                    :key="review.id"
                    class="p-6 hover:bg-gray-50 transition-colors"
                >
                    <div class="flex items-start justify-between">
                        <div class="flex-1">
                            <!-- Header -->
                            <div class="flex items-center space-x-3 mb-2">
                                <div class="w-10 h-10 rounded-full bg-gradient-to-br from-primary-400 to-primary-600 flex items-center justify-center text-white font-semibold">
                                    {{ review.student?.name?.charAt(0) || '?' }}
                                </div>
                                <div>
                                    <p class="font-medium text-gray-900">{{ review.student?.name || 'Anonymous' }}</p>
                                    <p class="text-xs text-gray-500">{{ formatDate(review.created_at) }}</p>
                                </div>
                                <span :class="['px-2 py-1 text-xs font-medium rounded-full', getStatusColor(review.status)]">
                                    {{ review.status }}
                                </span>
                            </div>

                            <!-- Course/Teacher Info -->
                            <div class="ml-13 mb-3">
                                <p v-if="review.course" class="text-sm text-gray-600">
                                    <span class="font-medium">Course:</span> {{ review.course.title }}
                                </p>
                                <p v-if="review.teacher" class="text-sm text-gray-600">
                                    <span class="font-medium">Teacher:</span> {{ review.teacher.name }}
                                </p>
                            </div>

                            <!-- Rating -->
                            <div class="ml-13 mb-2">
                                <span class="text-lg">{{ renderStars(review.rating) }}</span>
                                <span class="ml-2 text-sm text-gray-500">({{ review.rating }}/5)</span>
                            </div>

                            <!-- Comment -->
                            <p class="ml-13 text-gray-700">{{ review.comment || 'No comment provided' }}</p>
                        </div>

                        <!-- Actions -->
                        <div class="flex items-center space-x-2">
                            <button
                                v-if="review.status === 'pending'"
                                @click="approveReview(review.id)"
                                class="px-3 py-2 bg-green-500 hover:bg-green-600 text-white text-sm font-medium rounded-lg transition-colors"
                            >
                                ✓ Approve
                            </button>
                            <button
                                v-if="review.status === 'pending'"
                                @click="rejectReview(review.id)"
                                class="px-3 py-2 bg-yellow-500 hover:bg-yellow-600 text-white text-sm font-medium rounded-lg transition-colors"
                            >
                                ✕ Reject
                            </button>
                            <button
                                @click="deleteReview(review.id)"
                                class="px-3 py-2 border border-red-200 hover:bg-red-50 text-red-600 text-sm font-medium rounded-lg transition-colors"
                            >
                                🗑️
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <div v-else class="p-12 text-center">
                <p class="text-4xl mb-4">📭</p>
                <h4 class="font-medium text-gray-900 mb-2">No reviews found</h4>
                <p class="text-gray-500 text-sm">
                    {{ selectedStatus === 'pending' ? 'No pending reviews to moderate' : 'No reviews in this category' }}
                </p>
            </div>
        </div>
    </AdminLayout>
</template>

<style scoped>
.ml-13 {
    margin-left: 3.25rem;
}
</style>
