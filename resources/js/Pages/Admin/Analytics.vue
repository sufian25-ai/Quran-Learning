<script setup>
import { Head, Link } from '@inertiajs/vue3';
import { ref, computed } from 'vue';
import AdminLayout from '@/Layouts/AdminLayout.vue';

const props = defineProps({
    auth: Object,
    stats: {
        type: Object,
        default: () => ({})
    },
    revenueChart: {
        type: Array,
        default: () => []
    },
    enrollmentChart: {
        type: Array,
        default: () => []
    },
    topCourses: {
        type: Array,
        default: () => []
    },
    recentEnrollments: {
        type: Array,
        default: () => []
    }
});

const selectedPeriod = ref('month');

const formatCurrency = (amount) => {
    return new Intl.NumberFormat('en-US', {
        style: 'currency',
        currency: 'USD',
        minimumFractionDigits: 0,
    }).format(amount || 0);
};

const formatDate = (dateString) => {
    return new Date(dateString).toLocaleDateString('en-US', {
        month: 'short',
        day: 'numeric',
    });
};

const maxRevenue = computed(() => {
    return Math.max(...props.revenueChart.map(r => r.amount), 1);
});
</script>

<template>
    <Head title="Analytics | Admin" />

    <AdminLayout>
        <!-- Header -->
        <div class="flex items-center justify-between mb-6">
            <h1 class="text-2xl font-bold text-gray-900">Analytics & Reports</h1>
            <div class="flex items-center gap-2">
                <select
                    v-model="selectedPeriod"
                    class="rounded-lg border-gray-200 text-sm focus:border-primary-500 focus:ring-primary-500"
                >
                    <option value="week">Last 7 Days</option>
                    <option value="month">Last 30 Days</option>
                    <option value="quarter">Last 90 Days</option>
                    <option value="year">Last Year</option>
                </select>
            </div>
        </div>

        <!-- Key Metrics -->
        <div class="grid grid-cols-4 gap-6 mb-6">
            <div class="bg-white rounded-xl shadow-sm p-6">
                <div class="flex items-center justify-between mb-4">
                    <span class="text-2xl">💰</span>
                    <span class="text-xs text-green-500 font-medium bg-green-50 px-2 py-1 rounded">
                        +{{ stats.revenue_growth || 0 }}%
                    </span>
                </div>
                <p class="text-2xl font-bold text-gray-900">{{ formatCurrency(stats.total_revenue) }}</p>
                <p class="text-sm text-gray-500">Total Revenue</p>
            </div>
            <div class="bg-white rounded-xl shadow-sm p-6">
                <div class="flex items-center justify-between mb-4">
                    <span class="text-2xl">👥</span>
                    <span class="text-xs text-green-500 font-medium bg-green-50 px-2 py-1 rounded">
                        +{{ stats.user_growth || 0 }}%
                    </span>
                </div>
                <p class="text-2xl font-bold text-gray-900">{{ stats.total_users || 0 }}</p>
                <p class="text-sm text-gray-500">Total Users</p>
            </div>
            <div class="bg-white rounded-xl shadow-sm p-6">
                <div class="flex items-center justify-between mb-4">
                    <span class="text-2xl">📝</span>
                    <span class="text-xs text-green-500 font-medium bg-green-50 px-2 py-1 rounded">
                        +{{ stats.enrollment_growth || 0 }}%
                    </span>
                </div>
                <p class="text-2xl font-bold text-gray-900">{{ stats.total_enrollments || 0 }}</p>
                <p class="text-sm text-gray-500">Total Enrollments</p>
            </div>
            <div class="bg-white rounded-xl shadow-sm p-6">
                <div class="flex items-center justify-between mb-4">
                    <span class="text-2xl">📊</span>
                </div>
                <p class="text-2xl font-bold text-gray-900">{{ stats.completion_rate || 0 }}%</p>
                <p class="text-sm text-gray-500">Completion Rate</p>
            </div>
        </div>

        <div class="grid lg:grid-cols-3 gap-6">
            <!-- Revenue Chart -->
            <div class="lg:col-span-2 bg-white rounded-xl shadow-sm p-6">
                <h3 class="font-semibold text-gray-900 mb-6">Revenue Overview</h3>
                <div class="h-64 flex items-end justify-between gap-2">
                    <div
                        v-for="(item, index) in revenueChart"
                        :key="index"
                        class="flex-1 flex flex-col items-center"
                    >
                        <div
                            class="w-full bg-primary-500 rounded-t-lg transition-all hover:bg-primary-600"
                            :style="{ height: `${(item.amount / maxRevenue) * 100}%`, minHeight: '4px' }"
                        ></div>
                        <span class="text-xs text-gray-400 mt-2">{{ item.label }}</span>
                    </div>
                </div>
            </div>

            <!-- Top Courses -->
            <div class="bg-white rounded-xl shadow-sm p-6">
                <h3 class="font-semibold text-gray-900 mb-4">Top Performing Courses</h3>
                <div class="space-y-4">
                    <div
                        v-for="(course, index) in topCourses"
                        :key="course.id"
                        class="flex items-center"
                    >
                        <span class="w-6 h-6 rounded-full bg-primary-100 text-primary-700 text-xs font-medium flex items-center justify-center mr-3">
                            {{ index + 1 }}
                        </span>
                        <div class="flex-1 min-w-0">
                            <p class="font-medium text-gray-900 truncate">{{ course.title }}</p>
                            <p class="text-sm text-gray-500">{{ course.enrollments }} enrollments</p>
                        </div>
                        <span class="text-sm font-medium text-gray-900">{{ formatCurrency(course.revenue) }}</span>
                    </div>
                </div>
                <div v-if="!topCourses.length" class="text-center text-gray-400 py-6">
                    No course data yet
                </div>
            </div>
        </div>

        <!-- Enrollment Trends & Recent Activity -->
        <div class="grid lg:grid-cols-2 gap-6 mt-6">
            <!-- Enrollment Chart -->
            <div class="bg-white rounded-xl shadow-sm p-6">
                <h3 class="font-semibold text-gray-900 mb-6">Enrollment Trends</h3>
                <div class="space-y-4">
                    <div
                        v-for="item in enrollmentChart"
                        :key="item.label"
                        class="flex items-center"
                    >
                        <span class="w-20 text-sm text-gray-500">{{ item.label }}</span>
                        <div class="flex-1 mx-4">
                            <div class="h-4 bg-gray-100 rounded-full overflow-hidden">
                                <div
                                    class="h-full bg-gradient-to-r from-primary-400 to-primary-500 rounded-full"
                                    :style="{ width: `${(item.count / (stats.total_enrollments || 1)) * 100 * 3}%` }"
                                ></div>
                            </div>
                        </div>
                        <span class="text-sm font-medium text-gray-900">{{ item.count }}</span>
                    </div>
                </div>
            </div>

            <!-- Recent Enrollments -->
            <div class="bg-white rounded-xl shadow-sm p-6">
                <h3 class="font-semibold text-gray-900 mb-4">Recent Enrollments</h3>
                <div class="space-y-4">
                    <div
                        v-for="enrollment in recentEnrollments"
                        :key="enrollment.id"
                        class="flex items-center"
                    >
                        <div class="w-10 h-10 rounded-full bg-primary-100 flex items-center justify-center text-primary-700 font-medium">
                            {{ enrollment.user?.name?.charAt(0).toUpperCase() }}
                        </div>
                        <div class="ml-3 flex-1 min-w-0">
                            <p class="font-medium text-gray-900 truncate">{{ enrollment.user?.name }}</p>
                            <p class="text-sm text-gray-500 truncate">{{ enrollment.course?.title }}</p>
                        </div>
                        <span class="text-xs text-gray-400">{{ formatDate(enrollment.created_at) }}</span>
                    </div>
                </div>
                <div v-if="!recentEnrollments.length" class="text-center text-gray-400 py-6">
                    No recent enrollments
                </div>
            </div>
        </div>
    </AdminLayout>
</template>
