<script setup>
import { Head, Link } from '@inertiajs/vue3';
import { ref, computed } from 'vue';
import StudentLayout from '@/Layouts/StudentLayout.vue';

const props = defineProps({
    enrollment: {
        type: Object,
        required: true
    },
    upcomingClasses: {
        type: Array,
        default: () => []
    },
    recentClasses: {
        type: Array,
        default: () => []
    },
    resources: {
        type: Array,
        default: () => []
    }
});

const activeTab = ref('overview');

const tabs = [
    { id: 'overview', label: 'Overview', icon: '📊' },
    { id: 'classes', label: 'Classes', icon: '📅' },
    { id: 'resources', label: 'Resources', icon: '📁' },
    { id: 'progress', label: 'Progress', icon: '🎯' },
];

const formatDate = (dateString) => {
    return new Date(dateString).toLocaleDateString('en-US', {
        weekday: 'short',
        month: 'short',
        day: 'numeric',
    });
};

const formatTime = (dateString) => {
    return new Date(dateString).toLocaleTimeString('en-US', {
        hour: '2-digit',
        minute: '2-digit',
    });
};

const getStatusColor = (status) => {
    const colors = {
        'scheduled': 'bg-blue-100 text-blue-700',
        'completed': 'bg-green-100 text-green-700',
        'cancelled': 'bg-red-100 text-red-700',
    };
    return colors[status] || 'bg-gray-100 text-gray-700';
};
</script>

<template>
    <Head :title="enrollment.course?.title || 'Course Details'" />

    <StudentLayout>
        <!-- Hero Section -->
        <div class="bg-gradient-to-br from-primary-500 to-primary-600 text-white">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
                <!-- Breadcrumb -->
                <nav class="flex items-center text-sm text-primary-100 mb-6">
                    <Link href="/dashboard" class="hover:text-white">Dashboard</Link>
                    <span class="mx-2">/</span>
                    <Link href="/enrollments" class="hover:text-white">My Courses</Link>
                    <span class="mx-2">/</span>
                    <span>{{ enrollment.course?.title }}</span>
                </nav>

                <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between">
                    <div>
                        <span class="inline-flex items-center px-3 py-1 bg-white/20 text-white text-sm font-medium rounded-full mb-4">
                            {{ enrollment.type === 'private' ? 'Private 1-on-1' : 'Group Class' }}
                        </span>
                        <h1 class="text-3xl font-display font-bold mb-2">{{ enrollment.course?.title }}</h1>
                        <p v-if="enrollment.batch" class="text-primary-100">{{ enrollment.batch.name }}</p>
                    </div>

                    <div class="mt-6 lg:mt-0 flex items-center space-x-4">
                        <Link
                            v-if="upcomingClasses.length"
                            :href="`/classes/${upcomingClasses[0].id}/join`"
                            class="inline-flex items-center px-6 py-3 bg-white text-primary-600 font-semibold rounded-xl hover:bg-primary-50 transition-colors"
                        >
                            Join Next Class
                        </Link>
                    </div>
                </div>

                <!-- Progress Stats -->
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mt-8">
                    <div class="bg-white/10 rounded-xl p-4 text-center">
                        <p class="text-3xl font-bold">{{ enrollment.progress_percentage || 0 }}%</p>
                        <p class="text-primary-100 text-sm">Progress</p>
                    </div>
                    <div class="bg-white/10 rounded-xl p-4 text-center">
                        <p class="text-3xl font-bold">{{ enrollment.classes_attended || 0 }}</p>
                        <p class="text-primary-100 text-sm">Classes Attended</p>
                    </div>
                    <div class="bg-white/10 rounded-xl p-4 text-center">
                        <p class="text-3xl font-bold">{{ enrollment.classes_total || 0 }}</p>
                        <p class="text-primary-100 text-sm">Total Classes</p>
                    </div>
                    <div class="bg-white/10 rounded-xl p-4 text-center">
                        <p class="text-3xl font-bold">{{ upcomingClasses.length }}</p>
                        <p class="text-primary-100 text-sm">Upcoming</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tabs -->
        <div class="bg-white border-b border-gray-200 sticky top-16 z-30">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex space-x-1">
                    <button
                        v-for="tab in tabs"
                        :key="tab.id"
                        @click="activeTab = tab.id"
                        :class="[
                            'px-6 py-4 text-sm font-medium border-b-2 transition-colors',
                            activeTab === tab.id
                                ? 'border-primary-500 text-primary-600'
                                : 'border-transparent text-gray-500 hover:text-gray-700'
                        ]"
                    >
                        <span class="mr-2">{{ tab.icon }}</span>
                        {{ tab.label }}
                    </button>
                </div>
            </div>
        </div>

        <!-- Tab Content -->
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <!-- Overview Tab -->
            <div v-if="activeTab === 'overview'" class="grid lg:grid-cols-3 gap-8">
                <div class="lg:col-span-2 space-y-6">
                    <!-- Next Class Card -->
                    <div v-if="upcomingClasses.length" class="bg-white rounded-2xl shadow-soft p-6">
                        <h3 class="font-semibold text-gray-900 mb-4">📅 Next Class</h3>
                        <div class="flex items-center justify-between p-4 bg-primary-50 rounded-xl">
                            <div>
                                <h4 class="font-medium text-gray-900">{{ upcomingClasses[0].title }}</h4>
                                <p class="text-sm text-gray-500">
                                    {{ formatDate(upcomingClasses[0].scheduled_at) }} at {{ formatTime(upcomingClasses[0].scheduled_at) }}
                                </p>
                            </div>
                            <Link
                                :href="`/classes/${upcomingClasses[0].id}/join`"
                                class="px-4 py-2 bg-primary-500 hover:bg-primary-600 text-white text-sm font-medium rounded-lg"
                            >
                                Join
                            </Link>
                        </div>
                    </div>

                    <!-- Progress Overview -->
                    <div class="bg-white rounded-2xl shadow-soft p-6">
                        <h3 class="font-semibold text-gray-900 mb-4">📊 Progress Overview</h3>
                        <div class="mb-4">
                            <div class="flex justify-between text-sm mb-2">
                                <span class="text-gray-600">Course Progress</span>
                                <span class="font-medium">{{ enrollment.progress_percentage || 0 }}%</span>
                            </div>
                            <div class="w-full bg-gray-100 rounded-full h-3">
                                <div
                                    class="bg-gradient-to-r from-primary-400 to-primary-600 h-3 rounded-full"
                                    :style="{ width: `${enrollment.progress_percentage || 0}%` }"
                                ></div>
                            </div>
                        </div>
                        <p class="text-sm text-gray-500">
                            You've attended {{ enrollment.classes_attended || 0 }} of {{ enrollment.classes_total || 0 }} classes.
                            Keep it up! 🎉
                        </p>
                    </div>

                    <!-- Recent Classes -->
                    <div class="bg-white rounded-2xl shadow-soft p-6">
                        <h3 class="font-semibold text-gray-900 mb-4">📝 Recent Classes</h3>
                        <div v-if="recentClasses.length" class="space-y-3">
                            <div
                                v-for="c in recentClasses"
                                :key="c.id"
                                class="flex items-center justify-between p-3 bg-gray-50 rounded-xl"
                            >
                                <div>
                                    <h4 class="font-medium text-gray-900 text-sm">{{ c.title }}</h4>
                                    <p class="text-xs text-gray-500">{{ formatDate(c.scheduled_at) }}</p>
                                </div>
                                <span :class="['px-2 py-1 text-xs rounded-full', getStatusColor(c.status)]">
                                    {{ c.status }}
                                </span>
                            </div>
                        </div>
                        <p v-else class="text-gray-500 text-sm text-center py-4">No recent classes</p>
                    </div>
                </div>

                <!-- Sidebar -->
                <div class="space-y-6">
                    <!-- Course Info Card -->
                    <div class="bg-white rounded-2xl shadow-soft p-6">
                        <h3 class="font-semibold text-gray-900 mb-4">Course Info</h3>
                        <dl class="space-y-3 text-sm">
                            <div class="flex justify-between">
                                <dt class="text-gray-500">Enrollment Type</dt>
                                <dd class="text-gray-900 font-medium">{{ enrollment.type === 'private' ? 'Private' : 'Group' }}</dd>
                            </div>
                            <div class="flex justify-between">
                                <dt class="text-gray-500">Status</dt>
                                <dd class="text-gray-900 font-medium">{{ enrollment.status }}</dd>
                            </div>
                            <div class="flex justify-between">
                                <dt class="text-gray-500">Enrolled</dt>
                                <dd class="text-gray-900 font-medium">{{ enrollment.enrolled_date || 'N/A' }}</dd>
                            </div>
                        </dl>
                    </div>

                    <!-- Teacher Card -->
                    <div v-if="enrollment.teacher" class="bg-white rounded-2xl shadow-soft p-6">
                        <h3 class="font-semibold text-gray-900 mb-4">Your Teacher</h3>
                        <div class="flex items-center">
                            <div class="w-12 h-12 rounded-full bg-primary-100 flex items-center justify-center text-xl">
                                👨‍🏫
                            </div>
                            <div class="ml-4">
                                <p class="font-medium text-gray-900">{{ enrollment.teacher.name }}</p>
                                <p class="text-sm text-gray-500">Instructor</p>
                            </div>
                        </div>
                    </div>

                    <!-- Quick Actions -->
                    <div class="bg-white rounded-2xl shadow-soft p-6">
                        <h3 class="font-semibold text-gray-900 mb-4">Quick Actions</h3>
                        <div class="space-y-2">
                            <Link href="/support" class="flex items-center p-3 rounded-xl hover:bg-gray-50 transition-colors">
                                <span class="text-xl mr-3">💬</span>
                                <span class="text-gray-700">Contact Support</span>
                            </Link>
                            <button @click="activeTab = 'resources'" class="w-full flex items-center p-3 rounded-xl hover:bg-gray-50 transition-colors">
                                <span class="text-xl mr-3">📁</span>
                                <span class="text-gray-700">View Resources</span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Classes Tab -->
            <div v-if="activeTab === 'classes'" class="space-y-6">
                <div class="bg-white rounded-2xl shadow-soft p-6">
                    <h3 class="font-semibold text-gray-900 mb-6">Upcoming Classes</h3>
                    <div v-if="upcomingClasses.length" class="space-y-4">
                        <div
                            v-for="c in upcomingClasses"
                            :key="c.id"
                            class="flex items-center justify-between p-4 border border-gray-200 rounded-xl hover:border-primary-200 transition-colors"
                        >
                            <div class="flex items-center">
                                <div class="w-14 h-14 rounded-xl bg-primary-100 flex items-center justify-center">
                                    <span class="text-primary-600 font-bold">{{ formatDate(c.scheduled_at).split(' ')[1] }}</span>
                                </div>
                                <div class="ml-4">
                                    <h4 class="font-medium text-gray-900">{{ c.title }}</h4>
                                    <p class="text-sm text-gray-500">{{ formatDate(c.scheduled_at) }} • {{ formatTime(c.scheduled_at) }}</p>
                                </div>
                            </div>
                            <Link
                                :href="`/classes/${c.id}/join`"
                                class="px-4 py-2 bg-primary-500 hover:bg-primary-600 text-white text-sm font-medium rounded-lg"
                            >
                                Join Class
                            </Link>
                        </div>
                    </div>
                    <p v-else class="text-gray-500 text-center py-8">No upcoming classes scheduled</p>
                </div>
            </div>

            <!-- Resources Tab -->
            <div v-if="activeTab === 'resources'" class="space-y-6">
                <div class="bg-white rounded-2xl shadow-soft p-6">
                    <h3 class="font-semibold text-gray-900 mb-6">Course Resources</h3>
                    <div v-if="resources.length" class="space-y-3">
                        <a
                            v-for="resource in resources"
                            :key="resource.id"
                            :href="resource.download_url"
                            target="_blank"
                            class="flex items-center justify-between p-4 border border-gray-200 rounded-xl hover:border-primary-200 hover:bg-primary-50 transition-colors"
                        >
                            <div class="flex items-center">
                                <span class="text-2xl mr-4">📄</span>
                                <div>
                                    <h4 class="font-medium text-gray-900">{{ resource.title }}</h4>
                                    <p class="text-sm text-gray-500">{{ resource.type }} • {{ resource.size }}</p>
                                </div>
                            </div>
                            <span class="text-primary-500">Download →</span>
                        </a>
                    </div>
                    <p v-else class="text-gray-500 text-center py-8">No resources available yet</p>
                </div>
            </div>

            <!-- Progress Tab -->
            <div v-if="activeTab === 'progress'" class="space-y-6">
                <div class="bg-white rounded-2xl shadow-soft p-6">
                    <h3 class="font-semibold text-gray-900 mb-6">Your Learning Journey</h3>
                    <div class="text-center py-12">
                        <span class="text-6xl mb-4 block">🚧</span>
                        <h4 class="text-xl font-semibold text-gray-900 mb-2">Coming Soon</h4>
                        <p class="text-gray-500">Detailed progress tracking will be available soon.</p>
                    </div>
                </div>
            </div>
        </div>
    </StudentLayout>
</template>
