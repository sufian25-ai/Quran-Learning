<script setup>
import { Head, Link } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';

defineProps({
    auth: Object,
    stats: {
        type: Object,
        default: () => ({
            total_students: 0,
            total_teachers: 0,
            total_courses: 0,
            active_enrollments: 0,
            revenue_this_month: 0,
            pending_tickets: 0,
        })
    },
    recentEnrollments: {
        type: Array,
        default: () => []
    },
    recentPayments: {
        type: Array,
        default: () => []
    },
    topCourses: {
        type: Array,
        default: () => []
    }
});

const formatCurrency = (amount) => {
    return new Intl.NumberFormat('en-US', {
        style: 'currency',
        currency: 'USD',
    }).format(amount);
};

const formatDate = (dateString) => {
    return new Date(dateString).toLocaleDateString('en-US', {
        month: 'short',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit',
    });
};
</script>

<template>
    <Head title="Admin Dashboard" />

    <AdminLayout>
        <!-- Page Title -->
        <div class="mb-8">
            <h1 class="text-2xl font-bold text-gray-900">Dashboard Overview</h1>
            <p class="text-gray-500 mt-1">Welcome back! Here's what's happening today.</p>
        </div>

        <!-- Stats Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-6 gap-6 mb-8">
            <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-500">Total Students</p>
                        <p class="text-2xl font-bold text-gray-900 mt-1">{{ stats.total_students.toLocaleString() }}</p>
                    </div>
                    <div class="w-12 h-12 rounded-xl bg-blue-100 flex items-center justify-center text-2xl">
                        👥
                    </div>
                </div>
                <div class="mt-4 flex items-center text-sm">
                    <span class="text-green-500">↑ 12%</span>
                    <span class="text-gray-400 ml-2">vs last month</span>
                </div>
            </div>

            <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-500">Teachers</p>
                        <p class="text-2xl font-bold text-gray-900 mt-1">{{ stats.total_teachers }}</p>
                    </div>
                    <div class="w-12 h-12 rounded-xl bg-green-100 flex items-center justify-center text-2xl">
                        👨‍🏫
                    </div>
                </div>
                <div class="mt-4 flex items-center text-sm">
                    <span class="text-green-500">↑ 3</span>
                    <span class="text-gray-400 ml-2">new this month</span>
                </div>
            </div>

            <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-500">Published Courses</p>
                        <p class="text-2xl font-bold text-gray-900 mt-1">{{ stats.total_courses }}</p>
                    </div>
                    <div class="w-12 h-12 rounded-xl bg-purple-100 flex items-center justify-center text-2xl">
                        📚
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-500">Active Enrollments</p>
                        <p class="text-2xl font-bold text-gray-900 mt-1">{{ stats.active_enrollments.toLocaleString() }}</p>
                    </div>
                    <div class="w-12 h-12 rounded-xl bg-orange-100 flex items-center justify-center text-2xl">
                        📝
                    </div>
                </div>
                <div class="mt-4 flex items-center text-sm">
                    <span class="text-green-500">↑ 8%</span>
                    <span class="text-gray-400 ml-2">vs last month</span>
                </div>
            </div>

            <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-500">Revenue (Month)</p>
                        <p class="text-2xl font-bold text-primary-500 mt-1">{{ formatCurrency(stats.revenue_this_month) }}</p>
                    </div>
                    <div class="w-12 h-12 rounded-xl bg-primary-100 flex items-center justify-center text-2xl">
                        💰
                    </div>
                </div>
                <div class="mt-4 flex items-center text-sm">
                    <span class="text-green-500">↑ 15%</span>
                    <span class="text-gray-400 ml-2">vs last month</span>
                </div>
            </div>

            <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-500">Open Tickets</p>
                        <p class="text-2xl font-bold text-gray-900 mt-1">{{ stats.pending_tickets }}</p>
                    </div>
                    <div class="w-12 h-12 rounded-xl bg-red-100 flex items-center justify-center text-2xl">
                        🎫
                    </div>
                </div>
            </div>
        </div>

        <div class="grid lg:grid-cols-3 gap-6">
            <!-- Recent Enrollments -->
            <div class="lg:col-span-2 bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-lg font-semibold text-gray-900">Recent Enrollments</h3>
                    <Link href="/admin/enrollments" class="text-primary-500 hover:text-primary-600 text-sm font-medium">
                        View All →
                    </Link>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead>
                            <tr class="text-left text-sm text-gray-500 border-b border-gray-100">
                                <th class="pb-3 font-medium">Student</th>
                                <th class="pb-3 font-medium">Course</th>
                                <th class="pb-3 font-medium">Type</th>
                                <th class="pb-3 font-medium">Amount</th>
                                <th class="pb-3 font-medium">Date</th>
                            </tr>
                        </thead>
                        <tbody class="text-sm">
                            <tr v-for="enrollment in recentEnrollments" :key="enrollment.id" class="border-b border-gray-50 hover:bg-gray-50">
                                <td class="py-4">
                                    <div class="flex items-center">
                                        <div class="w-8 h-8 rounded-full bg-gray-200 flex items-center justify-center text-sm font-medium">
                                            {{ enrollment.user?.name?.charAt(0) || 'S' }}
                                        </div>
                                        <span class="ml-3 font-medium text-gray-900">{{ enrollment.user?.name }}</span>
                                    </div>
                                </td>
                                <td class="py-4 text-gray-600">{{ enrollment.course?.title }}</td>
                                <td class="py-4">
                                    <span :class="[
                                        'px-2 py-1 text-xs rounded-full',
                                        enrollment.type === 'private' ? 'bg-purple-100 text-purple-700' : 'bg-blue-100 text-blue-700'
                                    ]">
                                        {{ enrollment.type }}
                                    </span>
                                </td>
                                <td class="py-4 font-medium text-gray-900">{{ formatCurrency(enrollment.amount) }}</td>
                                <td class="py-4 text-gray-500">{{ formatDate(enrollment.created_at) }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div v-if="!recentEnrollments.length" class="text-center py-12 text-gray-500">
                    No recent enrollments
                </div>
            </div>

            <!-- Top Courses -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-lg font-semibold text-gray-900">Top Courses</h3>
                </div>

                <div class="space-y-4">
                    <div
                        v-for="(course, index) in topCourses"
                        :key="course.id"
                        class="flex items-center"
                    >
                        <span :class="[
                            'w-8 h-8 rounded-full flex items-center justify-center text-sm font-bold',
                            index === 0 ? 'bg-gold-100 text-gold-700' :
                            index === 1 ? 'bg-gray-200 text-gray-600' :
                            index === 2 ? 'bg-orange-100 text-orange-700' :
                            'bg-gray-100 text-gray-500'
                        ]">
                            {{ index + 1 }}
                        </span>
                        <div class="ml-3 flex-1">
                            <p class="font-medium text-gray-900 text-sm">{{ course.title }}</p>
                            <p class="text-xs text-gray-500">{{ course.total_enrollments }} enrollments</p>
                        </div>
                        <span class="text-sm font-medium text-gray-900">
                            {{ formatCurrency(course.price_group) }}
                        </span>
                    </div>
                </div>

                <div v-if="!topCourses.length" class="text-center py-12 text-gray-500">
                    No courses yet
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="mt-8 grid md:grid-cols-4 gap-4">
            <Link
                href="/admin/users/create"
                class="flex items-center p-4 bg-white rounded-xl border border-gray-200 hover:border-primary-300 hover:shadow-md transition-all"
            >
                <div class="w-10 h-10 rounded-lg bg-blue-100 flex items-center justify-center text-xl">➕</div>
                <span class="ml-3 font-medium text-gray-900">Add User</span>
            </Link>
            <Link
                href="/admin/courses/create"
                class="flex items-center p-4 bg-white rounded-xl border border-gray-200 hover:border-primary-300 hover:shadow-md transition-all"
            >
                <div class="w-10 h-10 rounded-lg bg-purple-100 flex items-center justify-center text-xl">📚</div>
                <span class="ml-3 font-medium text-gray-900">Add Course</span>
            </Link>
            <Link
                href="/admin/batches/create"
                class="flex items-center p-4 bg-white rounded-xl border border-gray-200 hover:border-primary-300 hover:shadow-md transition-all"
            >
                <div class="w-10 h-10 rounded-lg bg-green-100 flex items-center justify-center text-xl">📅</div>
                <span class="ml-3 font-medium text-gray-900">Create Batch</span>
            </Link>
            <Link
                href="/admin/reports"
                class="flex items-center p-4 bg-white rounded-xl border border-gray-200 hover:border-primary-300 hover:shadow-md transition-all"
            >
                <div class="w-10 h-10 rounded-lg bg-orange-100 flex items-center justify-center text-xl">📊</div>
                <span class="ml-3 font-medium text-gray-900">View Reports</span>
            </Link>
        </div>
    </AdminLayout>
</template>
