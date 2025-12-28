<script setup>
import { Head, Link } from '@inertiajs/vue3';
import StudentLayout from '@/Layouts/StudentLayout.vue';

defineProps({
    auth: Object,
    stats: {
        type: Object,
        default: () => ({
            active_courses: 0,
            classes_attended: 0,
            current_streak: 0,
            longest_streak: 0,
            points: 0,
            badges_count: 0,
        })
    },
    upcomingClasses: {
        type: Array,
        default: () => []
    },
    enrollments: {
        type: Array,
        default: () => []
    },
    recentBadges: {
        type: Array,
        default: () => []
    }
});

const isToday = (dateString) => {
    const date = new Date(dateString);
    const today = new Date();
    return date.toDateString() === today.toDateString();
};

const formatClassTime = (dateString) => {
    const date = new Date(dateString);
    return date.toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit' });
};

const formatClassDate = (dateString) => {
    const date = new Date(dateString);
    const today = new Date();
    const tomorrow = new Date(today);
    tomorrow.setDate(tomorrow.getDate() + 1);

    if (date.toDateString() === today.toDateString()) return 'Today';
    if (date.toDateString() === tomorrow.toDateString()) return 'Tomorrow';
    return date.toLocaleDateString('en-US', { weekday: 'short', month: 'short', day: 'numeric' });
};

const getTimeUntil = (dateString) => {
    const date = new Date(dateString);
    const now = new Date();
    const diffMs = date - now;
    const diffMins = Math.floor(diffMs / 60000);
    const diffHours = Math.floor(diffMins / 60);
    const diffDays = Math.floor(diffHours / 24);

    if (diffMins < 60) return `${diffMins}m`;
    if (diffHours < 24) return `${diffHours}h`;
    return `${diffDays}d`;
};
</script>

<template>
    <Head title="Dashboard" />

    <StudentLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="font-display text-xl font-bold text-gray-900">
                        Welcome back, {{ auth.user.name.split(' ')[0] }}! 👋
                    </h2>
                    <p class="text-gray-500 text-sm">Here's your learning overview</p>
                </div>
            </div>
        </template>

        <div class="space-y-6">
            <!-- Hero Stats Banner -->
            <div class="bg-gradient-to-r from-primary-600 via-primary-500 to-teal-500 rounded-3xl p-6 lg:p-8 text-white relative overflow-hidden">
                <!-- Animated background shapes -->
                <div class="absolute top-0 right-0 w-64 h-64 bg-white/10 rounded-full -translate-y-1/2 translate-x-1/2 animate-pulse"></div>
                <div class="absolute bottom-0 left-0 w-32 h-32 bg-white/5 rounded-full translate-y-1/2 -translate-x-1/2"></div>
                
                <div class="relative z-10">
                    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-6">
                        <!-- Main Stats -->
                        <div class="flex items-center gap-8">
                            <div class="text-center">
                                <div class="w-20 h-20 rounded-2xl bg-white/20 backdrop-blur flex items-center justify-center mb-2">
                                    <span class="text-4xl">🔥</span>
                                </div>
                                <p class="text-3xl font-bold">{{ stats.current_streak }}</p>
                                <p class="text-primary-100 text-sm">Day Streak</p>
                            </div>
                            <div class="hidden md:block h-16 w-px bg-white/20"></div>
                            <div class="text-center">
                                <div class="w-20 h-20 rounded-2xl bg-white/20 backdrop-blur flex items-center justify-center mb-2">
                                    <span class="text-4xl">⭐</span>
                                </div>
                                <p class="text-3xl font-bold">{{ stats.points.toLocaleString() }}</p>
                                <p class="text-primary-100 text-sm">XP Points</p>
                            </div>
                            <div class="hidden md:block h-16 w-px bg-white/20"></div>
                            <div class="text-center">
                                <div class="w-20 h-20 rounded-2xl bg-white/20 backdrop-blur flex items-center justify-center mb-2">
                                    <span class="text-4xl">🏅</span>
                                </div>
                                <p class="text-3xl font-bold">{{ stats.badges_count }}</p>
                                <p class="text-primary-100 text-sm">Badges</p>
                            </div>
                        </div>
                        <!-- CTA -->
                        <div class="flex flex-col sm:flex-row gap-3">
                            <Link
                                href="/courses"
                                class="inline-flex items-center justify-center px-6 py-3 bg-white text-primary-600 font-semibold rounded-xl hover:bg-primary-50 transition-all shadow-lg hover:shadow-xl hover:-translate-y-0.5"
                            >
                                📚 Browse Courses
                            </Link>
                            <Link
                                href="/leaderboard"
                                class="inline-flex items-center justify-center px-6 py-3 bg-white/20 text-white font-semibold rounded-xl hover:bg-white/30 backdrop-blur transition-all"
                            >
                                🏆 Leaderboard
                            </Link>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Quick Stats Cards -->
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
                <div class="group bg-gradient-to-br from-blue-500 to-blue-600 rounded-2xl p-5 text-white transition-all hover:scale-105 hover:shadow-xl cursor-pointer">
                    <div class="flex items-center justify-between mb-3">
                        <span class="text-3xl group-hover:animate-bounce">📚</span>
                        <span class="text-2xl font-bold">{{ stats.active_courses }}</span>
                    </div>
                    <p class="text-blue-100 text-sm">Active Courses</p>
                </div>
                <div class="group bg-gradient-to-br from-green-500 to-emerald-600 rounded-2xl p-5 text-white transition-all hover:scale-105 hover:shadow-xl cursor-pointer">
                    <div class="flex items-center justify-between mb-3">
                        <span class="text-3xl group-hover:animate-bounce">🎓</span>
                        <span class="text-2xl font-bold">{{ stats.classes_attended }}</span>
                    </div>
                    <p class="text-green-100 text-sm">Classes Attended</p>
                </div>
                <div class="group bg-gradient-to-br from-amber-500 to-orange-600 rounded-2xl p-5 text-white transition-all hover:scale-105 hover:shadow-xl cursor-pointer">
                    <div class="flex items-center justify-between mb-3">
                        <span class="text-3xl group-hover:animate-bounce">🔥</span>
                        <span class="text-2xl font-bold">{{ stats.current_streak }}</span>
                    </div>
                    <p class="text-amber-100 text-sm">Day Streak</p>
                </div>
                <div class="group bg-gradient-to-br from-purple-500 to-indigo-600 rounded-2xl p-5 text-white transition-all hover:scale-105 hover:shadow-xl cursor-pointer">
                    <div class="flex items-center justify-between mb-3">
                        <span class="text-3xl group-hover:animate-bounce">🏆</span>
                        <span class="text-2xl font-bold">{{ stats.longest_streak }}</span>
                    </div>
                    <p class="text-purple-100 text-sm">Best Streak</p>
                </div>
            </div>

            <div class="grid lg:grid-cols-3 gap-6">
                <!-- Upcoming Classes -->
                <div class="lg:col-span-2 space-y-6">
                    <!-- Next Class Card -->
                    <div v-if="upcomingClasses.length" class="bg-gradient-to-r from-primary-500 to-primary-600 rounded-2xl p-6 text-white relative overflow-hidden">
                        <div class="absolute top-0 right-0 w-32 h-32 bg-white/10 rounded-full -translate-y-1/2 translate-x-1/2"></div>
                        <div class="relative z-10">
                            <div class="flex items-center justify-between mb-4">
                                <span class="px-3 py-1 bg-white/20 rounded-full text-sm font-medium">
                                    📅 Next Class {{ isToday(upcomingClasses[0].scheduled_at) ? 'Today' : formatClassDate(upcomingClasses[0].scheduled_at) }}
                                </span>
                                <span class="text-primary-100 text-sm">in {{ getTimeUntil(upcomingClasses[0].scheduled_at) }}</span>
                            </div>
                            <h3 class="text-xl font-bold mb-2">{{ upcomingClasses[0].title }}</h3>
                            <p class="text-primary-100 mb-4">{{ upcomingClasses[0].batch?.name || 'Private Session' }} • {{ formatClassTime(upcomingClasses[0].scheduled_at) }}</p>
                            <Link
                                :href="`/classes/${upcomingClasses[0].id}/join`"
                                class="inline-flex items-center px-6 py-3 bg-white text-primary-600 font-semibold rounded-xl hover:bg-primary-50 transition-all shadow-lg"
                            >
                                🎥 Join Class
                            </Link>
                        </div>
                    </div>

                    <!-- Upcoming Classes List -->
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-lg font-semibold text-gray-900">📅 Upcoming Classes</h3>
                            <Link href="/classes" class="text-primary-500 hover:text-primary-600 text-sm font-medium">
                                View All →
                            </Link>
                        </div>

                        <div v-if="upcomingClasses.length" class="space-y-3">
                            <div
                                v-for="(class_, index) in upcomingClasses.slice(0, 4)"
                                :key="class_.id"
                                :class="[
                                    'flex items-center justify-between p-4 rounded-xl border-2 transition-all duration-300 hover:shadow-md cursor-pointer group',
                                    isToday(class_.scheduled_at) 
                                        ? 'border-primary-200 bg-gradient-to-r from-primary-50 to-transparent' 
                                        : 'border-gray-100 hover:border-primary-200'
                                ]"
                            >
                                <div class="flex items-center gap-4">
                                    <div :class="[
                                        'w-14 h-14 rounded-xl flex flex-col items-center justify-center text-center transition-all group-hover:scale-110',
                                        isToday(class_.scheduled_at) ? 'bg-primary-500 text-white' : 'bg-gray-100 text-gray-600'
                                    ]">
                                        <span class="text-xs font-medium uppercase">{{ new Date(class_.scheduled_at).toLocaleDateString('en-US', { weekday: 'short' }) }}</span>
                                        <span class="text-lg font-bold">{{ new Date(class_.scheduled_at).getDate() }}</span>
                                    </div>
                                    <div>
                                        <h4 class="font-medium text-gray-900">{{ class_.title }}</h4>
                                        <p class="text-sm text-gray-500">
                                            {{ formatClassTime(class_.scheduled_at) }} • {{ class_.duration_minutes || 45 }} min
                                        </p>
                                    </div>
                                </div>
                                <Link
                                    v-if="isToday(class_.scheduled_at)"
                                    :href="`/classes/${class_.id}/join`"
                                    class="px-4 py-2 bg-primary-500 hover:bg-primary-600 text-white text-sm font-medium rounded-lg transition-colors"
                                >
                                    Join
                                </Link>
                                <span v-else class="text-sm text-gray-400 group-hover:text-primary-500">
                                    {{ formatClassDate(class_.scheduled_at) }}
                                </span>
                            </div>
                        </div>
                        <div v-else class="text-center py-12">
                            <div class="w-20 h-20 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                                <span class="text-4xl">📅</span>
                            </div>
                            <p class="text-gray-500 mb-4">No upcoming classes</p>
                            <Link href="/courses" class="text-primary-500 hover:underline font-medium">
                                Enroll in a course
                            </Link>
                        </div>
                    </div>

                    <!-- My Courses -->
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-lg font-semibold text-gray-900">📚 My Courses</h3>
                            <Link href="/enrollments" class="text-primary-500 hover:text-primary-600 text-sm font-medium">
                                View All →
                            </Link>
                        </div>

                        <div v-if="enrollments.length" class="space-y-3">
                            <Link
                                v-for="enrollment in enrollments.slice(0, 3)"
                                :key="enrollment.id"
                                :href="`/my-courses/${enrollment.id}`"
                                class="block p-4 rounded-xl border border-gray-100 hover:border-primary-200 hover:shadow-md transition-all group"
                            >
                                <div class="flex items-center justify-between mb-3">
                                    <h4 class="font-medium text-gray-900 group-hover:text-primary-600 transition-colors">
                                        {{ enrollment.course?.title }}
                                    </h4>
                                    <span :class="[
                                        'px-2 py-1 text-xs font-medium rounded-full',
                                        enrollment.status === 'active' ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-600'
                                    ]">
                                        {{ enrollment.status }}
                                    </span>
                                </div>
                                <div class="flex items-center gap-4">
                                    <div class="flex-1">
                                        <div class="w-full bg-gray-100 rounded-full h-2">
                                            <div
                                                class="bg-gradient-to-r from-primary-400 to-primary-600 h-2 rounded-full transition-all duration-500"
                                                :style="{ width: `${enrollment.progress_percentage || 0}%` }"
                                            ></div>
                                        </div>
                                    </div>
                                    <span class="text-sm font-medium text-gray-600">{{ enrollment.progress_percentage || 0 }}%</span>
                                </div>
                            </Link>
                        </div>
                        <div v-else class="text-center py-12">
                            <div class="w-20 h-20 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                                <span class="text-4xl">📚</span>
                            </div>
                            <p class="text-gray-500 mb-4">No courses yet</p>
                            <Link href="/courses" class="inline-flex items-center px-6 py-3 bg-primary-500 text-white font-semibold rounded-xl hover:bg-primary-600 transition-colors">
                                Browse Courses
                            </Link>
                        </div>
                    </div>
                </div>

                <!-- Sidebar -->
                <div class="space-y-6">
                    <!-- Learning Streak Card -->
                    <div class="bg-gradient-to-br from-orange-400 via-orange-500 to-red-500 rounded-2xl p-6 text-white relative overflow-hidden">
                        <div class="absolute -top-6 -right-6 w-24 h-24 bg-white/10 rounded-full"></div>
                        <div class="absolute -bottom-4 -left-4 w-16 h-16 bg-white/10 rounded-full"></div>
                        <div class="relative z-10">
                            <div class="flex items-center justify-between mb-4">
                                <h3 class="font-semibold">Learning Streak</h3>
                                <span class="text-3xl animate-pulse">🔥</span>
                            </div>
                            <p class="text-5xl font-bold mb-2">{{ stats.current_streak }}</p>
                            <p class="text-orange-100">Start your streak today!</p>
                            <div class="mt-4 pt-4 border-t border-white/20 flex justify-between items-center">
                                <span class="text-sm text-orange-100">Best: {{ stats.longest_streak }} days</span>
                                <Link href="/leaderboard" class="text-sm font-medium hover:underline">View All →</Link>
                            </div>
                        </div>
                    </div>

                    <!-- Recent Badges -->
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="font-semibold text-gray-900">🏅 Recent Badges</h3>
                            <Link href="/leaderboard" class="text-primary-500 text-sm font-medium">View All</Link>
                        </div>
                        <div v-if="recentBadges.length" class="grid grid-cols-3 gap-3">
                            <div
                                v-for="badge in recentBadges.slice(0, 6)"
                                :key="badge.id"
                                class="aspect-square bg-gradient-to-br from-gray-50 to-gray-100 rounded-xl p-3 flex flex-col items-center justify-center text-center hover:scale-105 transition-transform cursor-pointer group"
                            >
                                <span class="text-2xl mb-1 group-hover:animate-bounce">{{ badge.icon }}</span>
                                <p class="text-xs text-gray-600 font-medium truncate w-full">{{ badge.name }}</p>
                            </div>
                        </div>
                        <div v-else class="text-center py-8">
                            <span class="text-4xl mb-2 block">🎯</span>
                            <p class="text-gray-500 text-sm">No badges yet. Keep learning!</p>
                        </div>
                    </div>

                    <!-- Quick Actions -->
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                        <h3 class="font-semibold text-gray-900 mb-4">⚡ Quick Actions</h3>
                        <div class="space-y-2">
                            <Link
                                v-for="action in [
                                    { href: '/classes', icon: '📅', label: 'My Schedule', color: 'hover:bg-blue-50' },
                                    { href: '/recordings', icon: '🎥', label: 'Recordings', color: 'hover:bg-purple-50' },
                                    { href: '/resources', icon: '📁', label: 'Resources', color: 'hover:bg-green-50' },
                                    { href: '/certificates', icon: '🏅', label: 'Certificates', color: 'hover:bg-amber-50' },
                                    { href: '/support', icon: '💬', label: 'Get Help', color: 'hover:bg-pink-50' },
                                ]"
                                :key="action.href"
                                :href="action.href"
                                :class="['flex items-center p-3 rounded-xl transition-all group', action.color]"
                            >
                                <span class="text-xl mr-3 group-hover:scale-110 transition-transform">{{ action.icon }}</span>
                                <span class="text-gray-700 font-medium group-hover:text-gray-900">{{ action.label }}</span>
                                <span class="ml-auto text-gray-300 group-hover:text-primary-500 transition-colors">→</span>
                            </Link>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </StudentLayout>
</template>

<style scoped>
@keyframes float {
    0%, 100% { transform: translateY(0); }
    50% { transform: translateY(-10px); }
}
.animate-float {
    animation: float 3s ease-in-out infinite;
}
</style>
