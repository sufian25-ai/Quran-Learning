<script setup>
import { Head, Link } from '@inertiajs/vue3';
import StudentLayout from '@/Layouts/StudentLayout.vue';

defineProps({
    enrollments: {
        type: Array,
        default: () => []
    }
});

const getStatusBadge = (status) => {
    const badges = {
        'active': 'bg-green-100 text-green-700',
        'completed': 'bg-blue-100 text-blue-700',
        'paused': 'bg-yellow-100 text-yellow-700',
        'cancelled': 'bg-red-100 text-red-700',
    };
    return badges[status] || 'bg-gray-100 text-gray-700';
};

const getTypeLabel = (type) => {
    return type === 'private' ? 'Private 1-on-1' : 'Group Class';
};
</script>

<template>
    <Head title="My Courses" />

    <StudentLayout>
        <template #header>
            <div>
                <h2 class="font-display text-xl font-bold text-gray-900">
                    My Courses 📚
                </h2>
                <p class="text-gray-500 text-sm">Track your learning progress</p>
            </div>
        </template>

        <div class="py-8">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <!-- Course Cards -->
                <div v-if="enrollments.length" class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
                    <Link
                        v-for="enrollment in enrollments"
                        :key="enrollment.id"
                        :href="`/my-courses/${enrollment.id}`"
                        class="bg-white rounded-2xl shadow-soft overflow-hidden hover:shadow-lg transition-all hover:-translate-y-1"
                    >
                        <!-- Course Header -->
                        <div class="bg-gradient-to-br from-primary-500 to-primary-600 p-6 text-white">
                            <div class="flex items-start justify-between mb-4">
                                <span :class="['px-3 py-1 text-xs font-medium rounded-full', getStatusBadge(enrollment.status)]">
                                    {{ enrollment.status }}
                                </span>
                                <span class="text-3xl">📖</span>
                            </div>
                            <h3 class="text-lg font-semibold mb-1">{{ enrollment.course?.title }}</h3>
                            <p class="text-primary-100 text-sm">{{ getTypeLabel(enrollment.type) }}</p>
                        </div>

                        <!-- Course Body -->
                        <div class="p-6">
                            <!-- Batch Info -->
                            <div v-if="enrollment.batch" class="flex items-center text-sm text-gray-500 mb-4">
                                <span class="mr-2">📅</span>
                                <span>{{ enrollment.batch.name }}</span>
                            </div>

                            <!-- Progress Bar -->
                            <div class="mb-4">
                                <div class="flex items-center justify-between text-sm mb-2">
                                    <span class="text-gray-600">Progress</span>
                                    <span class="font-medium text-gray-900">{{ enrollment.progress_percentage || 0 }}%</span>
                                </div>
                                <div class="w-full bg-gray-100 rounded-full h-2">
                                    <div
                                        class="bg-primary-500 h-2 rounded-full transition-all"
                                        :style="{ width: `${enrollment.progress_percentage || 0}%` }"
                                    ></div>
                                </div>
                            </div>

                            <!-- Stats -->
                            <div class="flex items-center justify-between text-sm">
                                <div class="text-center">
                                    <p class="font-semibold text-gray-900">{{ enrollment.classes_attended || 0 }}</p>
                                    <p class="text-gray-500 text-xs">Attended</p>
                                </div>
                                <div class="text-center">
                                    <p class="font-semibold text-gray-900">{{ enrollment.classes_total || 0 }}</p>
                                    <p class="text-gray-500 text-xs">Total</p>
                                </div>
                                <div class="text-center">
                                    <p class="font-semibold text-gray-900">{{ enrollment.next_class_date || 'N/A' }}</p>
                                    <p class="text-gray-500 text-xs">Next Class</p>
                                </div>
                            </div>
                        </div>

                        <!-- Footer -->
                        <div class="px-6 py-4 border-t border-gray-100 bg-gray-50">
                            <div class="flex items-center justify-between">
                                <span class="text-sm text-gray-500">
                                    Enrolled {{ enrollment.enrolled_date }}
                                </span>
                                <span class="text-primary-500 text-sm font-medium">
                                    View Details →
                                </span>
                            </div>
                        </div>
                    </Link>
                </div>

                <!-- Empty State -->
                <div v-else class="bg-white rounded-2xl shadow-soft p-12 text-center">
                    <span class="text-6xl mb-4 block">📚</span>
                    <h3 class="text-xl font-semibold text-gray-900 mb-2">No Courses Yet</h3>
                    <p class="text-gray-500 mb-6">Start your Quran learning journey today!</p>
                    <Link
                        href="/courses"
                        class="inline-flex items-center px-8 py-4 bg-primary-500 hover:bg-primary-600 text-white font-semibold rounded-xl transition-all hover:shadow-glow"
                    >
                        Browse Courses
                    </Link>
                </div>
            </div>
        </div>
    </StudentLayout>
</template>
