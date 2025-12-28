<script setup>
import { Head, Link } from '@inertiajs/vue3';
import { ref, computed } from 'vue';

const props = defineProps({
    auth: Object,
    batch: {
        type: Object,
        required: true
    },
    students: {
        type: Array,
        default: () => []
    },
    upcomingClasses: {
        type: Array,
        default: () => []
    },
    recentClasses: {
        type: Array,
        default: () => []
    }
});

const activeTab = ref('overview');

const navigation = [
    { name: 'Dashboard', href: '/teacher', icon: '🏠' },
    { name: 'Batches', href: '/teacher/batches', icon: '📅', active: true },
];

const tabs = [
    { id: 'overview', label: 'Overview', icon: '📊' },
    { id: 'students', label: 'Students', icon: '👥' },
    { id: 'classes', label: 'Classes', icon: '📅' },
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

const getStatusBadge = (status) => {
    const badges = {
        'present': 'bg-green-100 text-green-700',
        'absent': 'bg-red-100 text-red-700',
        'late': 'bg-yellow-100 text-yellow-700',
    };
    return badges[status] || 'bg-gray-100 text-gray-700';
};
</script>

<template>
    <Head :title="`${batch.name} | Teacher`" />

    <div class="min-h-screen bg-gray-100">
        <!-- Sidebar -->
        <aside class="fixed inset-y-0 left-0 z-50 w-64 bg-gray-900">
            <div class="flex items-center h-16 px-6 border-b border-gray-800">
                <Link href="/" class="flex items-center space-x-3">
                    <div class="w-8 h-8 bg-gradient-to-br from-primary-500 to-primary-400 rounded-lg flex items-center justify-center">
                        <span class="text-white font-bold">Q</span>
                    </div>
                    <span class="text-lg font-bold text-white">Teacher Panel</span>
                </Link>
            </div>
            <nav class="mt-6 px-3">
                <Link
                    v-for="item in navigation"
                    :key="item.name"
                    :href="item.href"
                    :class="[
                        'flex items-center px-4 py-3 mb-1 rounded-xl text-sm font-medium transition-colors',
                        item.active ? 'bg-primary-500 text-white' : 'text-gray-300 hover:bg-gray-800 hover:text-white'
                    ]"
                >
                    <span class="text-lg mr-3">{{ item.icon }}</span>
                    {{ item.name }}
                </Link>
            </nav>
        </aside>

        <!-- Main Content -->
        <div class="ml-64">
            <!-- Header -->
            <header class="bg-gradient-to-r from-primary-500 to-primary-600 text-white">
                <div class="px-6 py-8">
                    <div class="flex items-center mb-4">
                        <Link href="/teacher" class="text-primary-100 hover:text-white mr-2">Dashboard</Link>
                        <span class="text-primary-200 mx-2">/</span>
                        <span>{{ batch.name }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <div>
                            <h1 class="text-2xl font-bold mb-1">{{ batch.name }}</h1>
                            <p class="text-primary-100">{{ batch.course?.title }}</p>
                        </div>
                        <div class="flex items-center gap-4">
                            <span class="px-3 py-1 bg-white/20 rounded-full text-sm">
                                {{ batch.status }}
                            </span>
                        </div>
                    </div>

                    <!-- Stats -->
                    <div class="grid grid-cols-4 gap-4 mt-6">
                        <div class="bg-white/10 rounded-xl p-4 text-center">
                            <p class="text-2xl font-bold">{{ students.length }}</p>
                            <p class="text-primary-100 text-sm">Students</p>
                        </div>
                        <div class="bg-white/10 rounded-xl p-4 text-center">
                            <p class="text-2xl font-bold">{{ upcomingClasses.length }}</p>
                            <p class="text-primary-100 text-sm">Upcoming</p>
                        </div>
                        <div class="bg-white/10 rounded-xl p-4 text-center">
                            <p class="text-2xl font-bold">{{ recentClasses.length }}</p>
                            <p class="text-primary-100 text-sm">Completed</p>
                        </div>
                        <div class="bg-white/10 rounded-xl p-4 text-center">
                            <p class="text-2xl font-bold">{{ batch.max_students }}</p>
                            <p class="text-primary-100 text-sm">Max Capacity</p>
                        </div>
                    </div>
                </div>

                <!-- Tabs -->
                <div class="px-6 flex space-x-1">
                    <button
                        v-for="tab in tabs"
                        :key="tab.id"
                        @click="activeTab = tab.id"
                        :class="[
                            'px-6 py-3 text-sm font-medium rounded-t-xl transition-colors',
                            activeTab === tab.id
                                ? 'bg-gray-100 text-gray-900'
                                : 'text-primary-100 hover:text-white'
                        ]"
                    >
                        <span class="mr-2">{{ tab.icon }}</span>
                        {{ tab.label }}
                    </button>
                </div>
            </header>

            <main class="p-6">
                <!-- Overview Tab -->
                <div v-if="activeTab === 'overview'" class="grid lg:grid-cols-2 gap-6">
                    <!-- Next Class -->
                    <div class="bg-white rounded-xl shadow-sm p-6">
                        <h3 class="font-semibold text-gray-900 mb-4">📅 Next Class</h3>
                        <div v-if="upcomingClasses.length" class="p-4 bg-primary-50 rounded-xl">
                            <div class="flex items-center justify-between">
                                <div>
                                    <h4 class="font-medium text-gray-900">{{ upcomingClasses[0].title }}</h4>
                                    <p class="text-sm text-gray-500">
                                        {{ formatDate(upcomingClasses[0].scheduled_at) }} at {{ formatTime(upcomingClasses[0].scheduled_at) }}
                                    </p>
                                </div>
                                <a
                                    v-if="upcomingClasses[0].zoom_start_url"
                                    :href="upcomingClasses[0].zoom_start_url"
                                    target="_blank"
                                    class="px-4 py-2 bg-primary-500 hover:bg-primary-600 text-white text-sm font-medium rounded-lg"
                                >
                                    Start Class
                                </a>
                            </div>
                        </div>
                        <p v-else class="text-gray-500 text-center py-6">No upcoming classes</p>
                    </div>

                    <!-- Schedule -->
                    <div class="bg-white rounded-xl shadow-sm p-6">
                        <h3 class="font-semibold text-gray-900 mb-4">🕐 Class Schedule</h3>
                        <div v-if="batch.schedule?.length" class="space-y-2">
                            <div
                                v-for="(item, index) in batch.schedule"
                                :key="index"
                                class="flex items-center justify-between p-3 bg-gray-50 rounded-lg"
                            >
                                <span class="font-medium text-gray-700">{{ item.day }}</span>
                                <span class="text-gray-500">{{ item.time }}</span>
                            </div>
                        </div>
                        <p v-else class="text-gray-500 text-center py-6">No schedule set</p>
                    </div>

                    <!-- Quick Stats -->
                    <div class="bg-white rounded-xl shadow-sm p-6 lg:col-span-2">
                        <h3 class="font-semibold text-gray-900 mb-4">📊 Batch Info</h3>
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                            <div class="p-4 bg-gray-50 rounded-xl">
                                <p class="text-sm text-gray-500">Start Date</p>
                                <p class="font-medium text-gray-900">{{ batch.start_date || 'Not set' }}</p>
                            </div>
                            <div class="p-4 bg-gray-50 rounded-xl">
                                <p class="text-sm text-gray-500">End Date</p>
                                <p class="font-medium text-gray-900">{{ batch.end_date || 'Not set' }}</p>
                            </div>
                            <div class="p-4 bg-gray-50 rounded-xl">
                                <p class="text-sm text-gray-500">Course</p>
                                <p class="font-medium text-gray-900">{{ batch.course?.title }}</p>
                            </div>
                            <div class="p-4 bg-gray-50 rounded-xl">
                                <p class="text-sm text-gray-500">Status</p>
                                <p class="font-medium text-gray-900 capitalize">{{ batch.status }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Students Tab -->
                <div v-if="activeTab === 'students'" class="bg-white rounded-xl shadow-sm overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="font-semibold text-gray-900">Enrolled Students ({{ students.length }})</h3>
                    </div>
                    <table v-if="students.length" class="w-full">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Student</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Enrolled</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Attendance</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Progress</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            <tr v-for="student in students" :key="student.id" class="hover:bg-gray-50">
                                <td class="px-6 py-4">
                                    <div class="flex items-center">
                                        <div class="w-10 h-10 rounded-full bg-primary-100 flex items-center justify-center text-primary-700 font-medium">
                                            {{ student.name?.charAt(0).toUpperCase() }}
                                        </div>
                                        <div class="ml-4">
                                            <div class="font-medium text-gray-900">{{ student.name }}</div>
                                            <div class="text-sm text-gray-500">{{ student.email }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-500">{{ student.enrolled_date || 'N/A' }}</td>
                                <td class="px-6 py-4 text-sm text-gray-900">{{ student.attendance_rate || 0 }}%</td>
                                <td class="px-6 py-4">
                                    <div class="w-full bg-gray-100 rounded-full h-2">
                                        <div
                                            class="bg-primary-500 h-2 rounded-full"
                                            :style="{ width: `${student.progress || 0}%` }"
                                        ></div>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                    <div v-else class="text-center py-12">
                        <span class="text-4xl mb-4 block">👥</span>
                        <p class="text-gray-500">No students enrolled yet</p>
                    </div>
                </div>

                <!-- Classes Tab -->
                <div v-if="activeTab === 'classes'" class="space-y-6">
                    <!-- Upcoming -->
                    <div class="bg-white rounded-xl shadow-sm p-6">
                        <h3 class="font-semibold text-gray-900 mb-4">Upcoming Classes</h3>
                        <div v-if="upcomingClasses.length" class="space-y-3">
                            <div
                                v-for="c in upcomingClasses"
                                :key="c.id"
                                class="flex items-center justify-between p-4 border border-gray-200 rounded-xl"
                            >
                                <div>
                                    <h4 class="font-medium text-gray-900">{{ c.title }}</h4>
                                    <p class="text-sm text-gray-500">{{ formatDate(c.scheduled_at) }} • {{ formatTime(c.scheduled_at) }}</p>
                                </div>
                                <a
                                    v-if="c.zoom_start_url"
                                    :href="c.zoom_start_url"
                                    target="_blank"
                                    class="px-4 py-2 bg-primary-500 hover:bg-primary-600 text-white text-sm font-medium rounded-lg"
                                >
                                    Start
                                </a>
                            </div>
                        </div>
                        <p v-else class="text-gray-500 text-center py-4">No upcoming classes</p>
                    </div>

                    <!-- Recent -->
                    <div class="bg-white rounded-xl shadow-sm p-6">
                        <h3 class="font-semibold text-gray-900 mb-4">Recent Classes</h3>
                        <div v-if="recentClasses.length" class="space-y-3">
                            <div
                                v-for="c in recentClasses"
                                :key="c.id"
                                class="flex items-center justify-between p-4 border border-gray-200 rounded-xl"
                            >
                                <div>
                                    <h4 class="font-medium text-gray-900">{{ c.title }}</h4>
                                    <p class="text-sm text-gray-500">{{ formatDate(c.scheduled_at) }}</p>
                                </div>
                                <span class="px-3 py-1 bg-green-100 text-green-700 text-xs rounded-full">Completed</span>
                            </div>
                        </div>
                        <p v-else class="text-gray-500 text-center py-4">No completed classes</p>
                    </div>
                </div>
            </main>
        </div>
    </div>
</template>
