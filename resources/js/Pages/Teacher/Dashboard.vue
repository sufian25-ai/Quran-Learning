<script setup>
import { Head, Link } from '@inertiajs/vue3';
import TeacherLayout from '@/Layouts/TeacherLayout.vue';

defineProps({
    auth: Object,
    stats: {
        type: Object,
        default: () => ({
            total_students: 0,
            active_batches: 0,
            classes_today: 0,
            classes_this_week: 0,
            average_rating: 5.0,
            total_reviews: 0,
        })
    },
    todaysClasses: {
        type: Array,
        default: () => []
    },
    upcomingClasses: {
        type: Array,
        default: () => []
    },
    batches: {
        type: Array,
        default: () => []
    }
});

const formatTime = (dateString) => {
    const date = new Date(dateString);
    return date.toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit' });
};

const getStatusColor = (status) => {
    const colors = {
        'scheduled': 'bg-blue-100 text-blue-700',
        'live': 'bg-green-100 text-green-700 animate-pulse',
        'completed': 'bg-gray-100 text-gray-700',
        'cancelled': 'bg-red-100 text-red-700',
    };
    return colors[status] || 'bg-gray-100 text-gray-700';
};

const getTimeUntil = (dateString) => {
    const date = new Date(dateString);
    const now = new Date();
    const diffMs = date - now;
    const diffMins = Math.floor(diffMs / 60000);
    if (diffMins < 0) return 'Now';
    if (diffMins < 60) return `${diffMins}m`;
    return `${Math.floor(diffMins / 60)}h`;
};
</script>

<template>
    <Head title="Teacher Dashboard" />

    <TeacherLayout>
        <template #header>
            <div>
                <h2 class="font-display text-xl font-bold text-gray-900">
                    Teacher Dashboard 👨‍🏫
                </h2>
                <p class="text-gray-500 text-sm">Manage your classes and students</p>
            </div>
        </template>

        <div class="space-y-6">
            <!-- Hero Stats Banner -->
            <div class="bg-gradient-to-r from-slate-800 via-slate-700 to-emerald-800 rounded-3xl p-6 lg:p-8 text-white relative overflow-hidden">
                <!-- Animated background shapes -->
                <div class="absolute top-0 right-0 w-64 h-64 bg-emerald-500/10 rounded-full -translate-y-1/2 translate-x-1/2 animate-pulse"></div>
                <div class="absolute bottom-0 left-0 w-40 h-40 bg-white/5 rounded-full translate-y-1/2 -translate-x-1/2"></div>
                
                <div class="relative z-10">
                    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-6">
                        <!-- Main Stats -->
                        <div class="flex items-center gap-6 lg:gap-10">
                            <div class="text-center">
                                <div class="w-16 h-16 lg:w-20 lg:h-20 rounded-2xl bg-white/10 backdrop-blur flex items-center justify-center mb-2">
                                    <span class="text-3xl lg:text-4xl">👥</span>
                                </div>
                                <p class="text-2xl lg:text-3xl font-bold">{{ stats.total_students }}</p>
                                <p class="text-slate-300 text-sm">Students</p>
                            </div>
                            <div class="hidden sm:block h-16 w-px bg-white/20"></div>
                            <div class="text-center">
                                <div class="w-16 h-16 lg:w-20 lg:h-20 rounded-2xl bg-white/10 backdrop-blur flex items-center justify-center mb-2">
                                    <span class="text-3xl lg:text-4xl">📚</span>
                                </div>
                                <p class="text-2xl lg:text-3xl font-bold">{{ stats.active_batches }}</p>
                                <p class="text-slate-300 text-sm">Batches</p>
                            </div>
                            <div class="hidden sm:block h-16 w-px bg-white/20"></div>
                            <div class="text-center">
                                <div class="w-16 h-16 lg:w-20 lg:h-20 rounded-2xl bg-emerald-500/30 backdrop-blur flex items-center justify-center mb-2">
                                    <span class="text-3xl lg:text-4xl">⭐</span>
                                </div>
                                <p class="text-2xl lg:text-3xl font-bold">{{ (Number(stats.average_rating) || 5.0).toFixed(1) }}</p>
                                <p class="text-slate-300 text-sm">Rating</p>
                            </div>
                        </div>
                        <!-- CTA -->
                        <div class="flex flex-col sm:flex-row gap-3">
                            <Link
                                v-if="todaysClasses.length && todaysClasses[0].zoom_start_url"
                                :href="todaysClasses[0].zoom_start_url"
                                target="_blank"
                                class="inline-flex items-center justify-center px-6 py-3 bg-emerald-500 text-white font-semibold rounded-xl hover:bg-emerald-400 transition-all shadow-lg hover:shadow-xl hover:-translate-y-0.5"
                            >
                                🎥 Start Next Class
                            </Link>
                            <Link
                                href="/teacher/schedule"
                                class="inline-flex items-center justify-center px-6 py-3 bg-white/10 text-white font-semibold rounded-xl hover:bg-white/20 backdrop-blur transition-all"
                            >
                                📅 Full Schedule
                            </Link>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Quick Stats Cards -->
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
                <div class="group bg-gradient-to-br from-emerald-500 to-teal-600 rounded-2xl p-5 text-white transition-all hover:scale-105 hover:shadow-xl cursor-pointer">
                    <div class="flex items-center justify-between mb-3">
                        <span class="text-3xl group-hover:animate-bounce">📅</span>
                        <span class="text-2xl font-bold">{{ stats.classes_today }}</span>
                    </div>
                    <p class="text-emerald-100 text-sm">Classes Today</p>
                </div>
                <div class="group bg-gradient-to-br from-blue-500 to-indigo-600 rounded-2xl p-5 text-white transition-all hover:scale-105 hover:shadow-xl cursor-pointer">
                    <div class="flex items-center justify-between mb-3">
                        <span class="text-3xl group-hover:animate-bounce">🗓️</span>
                        <span class="text-2xl font-bold">{{ stats.classes_this_week }}</span>
                    </div>
                    <p class="text-blue-100 text-sm">This Week</p>
                </div>
                <div class="group bg-gradient-to-br from-amber-500 to-orange-600 rounded-2xl p-5 text-white transition-all hover:scale-105 hover:shadow-xl cursor-pointer">
                    <div class="flex items-center justify-between mb-3">
                        <span class="text-3xl group-hover:animate-bounce">💬</span>
                        <span class="text-2xl font-bold">{{ stats.total_reviews }}</span>
                    </div>
                    <p class="text-amber-100 text-sm">Reviews</p>
                </div>
                <div class="group bg-gradient-to-br from-purple-500 to-pink-600 rounded-2xl p-5 text-white transition-all hover:scale-105 hover:shadow-xl cursor-pointer">
                    <div class="flex items-center justify-between mb-3">
                        <span class="text-3xl group-hover:animate-bounce">👥</span>
                        <span class="text-2xl font-bold">{{ stats.total_students }}</span>
                    </div>
                    <p class="text-purple-100 text-sm">Total Students</p>
                </div>
            </div>

            <div class="grid lg:grid-cols-3 gap-6">
                <!-- Today's Classes -->
                <div class="lg:col-span-2 space-y-6">
                    <!-- Next Class Highlight -->
                    <div v-if="todaysClasses.length" class="bg-gradient-to-r from-emerald-500 to-teal-500 rounded-2xl p-6 text-white relative overflow-hidden">
                        <div class="absolute top-0 right-0 w-32 h-32 bg-white/10 rounded-full -translate-y-1/2 translate-x-1/2"></div>
                        <div class="relative z-10">
                            <div class="flex items-center justify-between mb-4">
                                <span class="px-3 py-1 bg-white/20 rounded-full text-sm font-medium flex items-center gap-2">
                                    <span class="w-2 h-2 bg-white rounded-full animate-pulse"></span>
                                    Next Class in {{ getTimeUntil(todaysClasses[0].scheduled_at) }}
                                </span>
                                <span class="text-emerald-100 text-sm">{{ todaysClasses[0].enrolled_students || 0 }} students</span>
                            </div>
                            <h3 class="text-xl font-bold mb-2">{{ todaysClasses[0].title }}</h3>
                            <p class="text-emerald-100 mb-4">{{ todaysClasses[0].batch?.name }} • {{ formatTime(todaysClasses[0].scheduled_at) }}</p>
                            <div class="flex gap-3">
                                <a
                                    v-if="todaysClasses[0].zoom_start_url"
                                    :href="todaysClasses[0].zoom_start_url"
                                    target="_blank"
                                    class="inline-flex items-center px-6 py-3 bg-white text-emerald-600 font-semibold rounded-xl hover:bg-emerald-50 transition-all shadow-lg"
                                >
                                    🎥 Start Class
                                </a>
                                <Link
                                    :href="`/teacher/classes/${todaysClasses[0].id}`"
                                    class="inline-flex items-center px-4 py-3 bg-white/20 text-white font-medium rounded-xl hover:bg-white/30 transition-all"
                                >
                                    View Details
                                </Link>
                            </div>
                        </div>
                    </div>

                    <!-- Today's Schedule -->
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-lg font-semibold text-gray-900">📅 Today's Schedule</h3>
                            <span class="px-3 py-1 bg-emerald-100 text-emerald-700 text-sm font-medium rounded-full">
                                {{ todaysClasses.length }} classes
                            </span>
                        </div>

                        <div v-if="todaysClasses.length" class="space-y-3">
                            <div
                                v-for="class_ in todaysClasses"
                                :key="class_.id"
                                class="flex items-center justify-between p-4 rounded-xl border-2 border-gray-100 hover:border-emerald-200 hover:shadow-md transition-all group"
                            >
                                <div class="flex items-center gap-4">
                                    <div class="w-14 h-14 rounded-xl bg-gradient-to-br from-emerald-500 to-teal-600 flex items-center justify-center text-white font-bold text-lg shadow-lg group-hover:scale-110 transition-transform">
                                        {{ formatTime(class_.scheduled_at).split(':')[0] }}
                                    </div>
                                    <div>
                                        <h4 class="font-medium text-gray-900">{{ class_.title }}</h4>
                                        <p class="text-sm text-gray-500">
                                            {{ formatTime(class_.scheduled_at) }} • {{ class_.duration_minutes || 45 }} min • {{ class_.enrolled_students || 0 }} students
                                        </p>
                                    </div>
                                </div>
                                <div class="flex items-center gap-3">
                                    <span :class="['px-3 py-1 text-xs font-medium rounded-full', getStatusColor(class_.status)]">
                                        {{ class_.status }}
                                    </span>
                                    <a
                                        v-if="class_.zoom_start_url"
                                        :href="class_.zoom_start_url"
                                        target="_blank"
                                        class="px-4 py-2 bg-emerald-500 hover:bg-emerald-600 text-white text-sm font-medium rounded-lg transition-colors"
                                    >
                                        Start
                                    </a>
                                </div>
                            </div>
                        </div>
                        <div v-else class="text-center py-12">
                            <div class="w-20 h-20 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                                <span class="text-4xl">🎉</span>
                            </div>
                            <h4 class="font-medium text-gray-900 mb-2">No classes today</h4>
                            <p class="text-gray-500 text-sm">Enjoy your day off!</p>
                        </div>
                    </div>

                    <!-- Upcoming This Week -->
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-lg font-semibold text-gray-900">📆 Upcoming This Week</h3>
                            <Link href="/teacher/schedule" class="text-emerald-500 hover:text-emerald-600 text-sm font-medium">
                                View All →
                            </Link>
                        </div>

                        <div v-if="upcomingClasses.length" class="space-y-2">
                            <div
                                v-for="class_ in upcomingClasses.slice(0, 5)"
                                :key="class_.id"
                                class="flex items-center justify-between p-3 rounded-xl hover:bg-gray-50 transition-colors group"
                            >
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-lg bg-gray-100 flex items-center justify-center text-gray-600 font-medium text-sm group-hover:bg-emerald-100 group-hover:text-emerald-600 transition-colors">
                                        {{ new Date(class_.scheduled_at).toLocaleDateString('en-US', { weekday: 'short' }) }}
                                    </div>
                                    <div>
                                        <h4 class="font-medium text-gray-900 text-sm">{{ class_.title }}</h4>
                                        <p class="text-xs text-gray-500">{{ formatTime(class_.scheduled_at) }} • {{ class_.batch?.name }}</p>
                                    </div>
                                </div>
                                <span class="text-sm text-gray-400">{{ class_.enrolled_students || 0 }} 👥</span>
                            </div>
                        </div>
                        <p v-else class="text-gray-500 text-sm text-center py-6">No upcoming classes this week</p>
                    </div>
                </div>

                <!-- Sidebar -->
                <div class="space-y-6">
                    <!-- Quick Actions -->
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                        <h3 class="font-semibold text-gray-900 mb-4">⚡ Quick Actions</h3>
                        <div class="space-y-2">
                            <Link
                                v-for="action in [
                                    { href: '/teacher/attendance', icon: '✅', label: 'Mark Attendance', color: 'hover:bg-green-50' },
                                    { href: '/teacher/resources', icon: '📤', label: 'Upload Resources', color: 'hover:bg-blue-50' },
                                    { href: '/teacher/batches', icon: '📚', label: 'My Batches', color: 'hover:bg-purple-50' },
                                    { href: '/teacher/students', icon: '👥', label: 'View Students', color: 'hover:bg-amber-50' },
                                    { href: '/teacher/earnings', icon: '💰', label: 'Earnings', color: 'hover:bg-emerald-50' },
                                ]"
                                :key="action.href"
                                :href="action.href"
                                :class="['flex items-center p-3 rounded-xl transition-all group', action.color]"
                            >
                                <span class="text-xl mr-3 group-hover:scale-110 transition-transform">{{ action.icon }}</span>
                                <span class="text-gray-700 font-medium group-hover:text-gray-900">{{ action.label }}</span>
                                <span class="ml-auto text-gray-300 group-hover:text-emerald-500 transition-colors">→</span>
                            </Link>
                        </div>
                    </div>

                    <!-- My Batches -->
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="font-semibold text-gray-900">📚 My Batches</h3>
                            <Link href="/teacher/batches" class="text-emerald-500 text-sm font-medium">View All</Link>
                        </div>
                        <div v-if="batches.length" class="space-y-3">
                            <Link
                                v-for="batch in batches.slice(0, 4)"
                                :key="batch.id"
                                :href="`/teacher/batches/${batch.id}`"
                                class="block p-3 rounded-xl border border-gray-100 hover:border-emerald-200 hover:shadow-md transition-all group"
                            >
                                <div class="flex items-center justify-between">
                                    <div>
                                        <h4 class="font-medium text-gray-900 text-sm group-hover:text-emerald-600 transition-colors">{{ batch.name }}</h4>
                                        <p class="text-xs text-gray-500">{{ batch.course?.title }}</p>
                                    </div>
                                    <div class="text-right">
                                        <p class="text-sm font-bold text-gray-900">{{ batch.enrolled_students }}</p>
                                        <p class="text-xs text-gray-400">students</p>
                                    </div>
                                </div>
                            </Link>
                        </div>
                        <p v-else class="text-gray-500 text-sm text-center py-4">No batches assigned</p>
                    </div>

                    <!-- Performance Card -->
                    <div class="bg-gradient-to-br from-slate-700 to-slate-800 rounded-2xl p-6 text-white relative overflow-hidden">
                        <div class="absolute -top-6 -right-6 w-24 h-24 bg-emerald-500/20 rounded-full"></div>
                        <div class="relative z-10">
                            <h3 class="font-semibold mb-4">📊 Performance</h3>
                            <div class="space-y-4">
                                <div>
                                    <div class="flex justify-between text-sm mb-1">
                                        <span class="text-slate-300">Student Satisfaction</span>
                                        <span>{{ Math.round(stats.average_rating / 5 * 100) }}%</span>
                                    </div>
                                    <div class="w-full bg-slate-600 rounded-full h-2">
                                        <div
                                            class="bg-gradient-to-r from-emerald-400 to-teal-400 h-2 rounded-full transition-all"
                                            :style="{ width: `${(stats.average_rating / 5) * 100}%` }"
                                        ></div>
                                    </div>
                                </div>
                                <div class="pt-4 border-t border-slate-600 flex justify-between items-center">
                                    <span class="text-slate-300 text-sm">Your Rating</span>
                                    <div class="flex items-center">
                                        <span class="text-2xl mr-2">⭐</span>
                                        <span class="text-2xl font-bold">{{ (Number(stats.average_rating) || 5.0).toFixed(1) }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </TeacherLayout>
</template>
