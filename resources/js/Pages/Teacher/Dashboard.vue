<script setup>
import { Head, Link, usePage } from '@inertiajs/vue3';
import TeacherLayout from '@/Layouts/TeacherLayout.vue';
import { computed } from 'vue';

const props = defineProps({
    stats: Object,
    todaysClasses: Array,
    upcomingClasses: Array,
    batches: Array,
});

const page = usePage();
const userName = computed(() => page.props.auth?.user?.name?.split(' ')[0] || 'Teacher');

const formatTime = (dateString) => {
    return new Date(dateString).toLocaleTimeString('en-US', {
        hour: 'numeric',
        minute: '2-digit',
        hour12: true,
    });
};

const formatDate = (dateString) => {
    return new Date(dateString).toLocaleDateString('en-US', {
        weekday: 'short',
        month: 'short',
        day: 'numeric',
    });
};

const isToday = (dateString) => {
    const date = new Date(dateString);
    const today = new Date();
    return date.toDateString() === today.toDateString();
};

const getTimeUntil = (dateString) => {
    const date = new Date(dateString);
    const now = new Date();
    const diffMs = date - now;
    const diffMins = Math.floor(diffMs / 60000);
    const diffHours = Math.floor(diffMins / 60);
    
    if (diffMins < 0) return 'Started';
    if (diffMins < 60) return `${diffMins}m`;
    if (diffHours < 24) return `${diffHours}h`;
    return `${Math.floor(diffHours / 24)}d`;
};

const getStatusColor = (status) => {
    const colors = {
        scheduled: 'bg-blue-100 text-blue-700 dark:bg-blue-900/50 dark:text-blue-300',
        live: 'bg-green-100 text-green-700 dark:bg-green-900/50 dark:text-green-300 animate-pulse',
        completed: 'bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-300',
        cancelled: 'bg-red-100 text-red-700 dark:bg-red-900/50 dark:text-red-300',
    };
    return colors[status] || colors.scheduled;
};
</script>

<template>
    <Head title="Teacher Dashboard" />

    <TeacherLayout>
        <div class="space-y-6">
            <!-- Hero Banner -->
            <div class="bg-gradient-to-r from-emerald-600 via-teal-500 to-cyan-500 rounded-3xl p-6 lg:p-8 text-white relative overflow-hidden">
                <!-- Animated background shapes -->
                <div class="absolute top-0 right-0 w-64 h-64 bg-white/10 rounded-full -translate-y-1/2 translate-x-1/2 animate-pulse"></div>
                <div class="absolute bottom-0 left-0 w-40 h-40 bg-white/5 rounded-full translate-y-1/2 -translate-x-1/2"></div>
                <div class="absolute top-1/2 left-1/3 w-20 h-20 bg-white/10 rounded-full animate-bounce" style="animation-duration: 3s;"></div>
                
                <div class="relative z-10">
                    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-6">
                        <!-- Welcome Text -->
                        <div>
                            <h1 class="text-3xl lg:text-4xl font-bold mb-2">
                                Welcome back, {{ userName }}! 👋
                            </h1>
                            <p class="text-emerald-100 text-lg">
                                Here's what's happening with your classes today
                            </p>
                            <div class="flex items-center gap-4 mt-4">
                                <span class="inline-flex items-center px-3 py-1.5 bg-white/20 rounded-full text-sm font-medium backdrop-blur">
                                    📚 {{ stats.active_batches }} Active Batches
                                </span>
                                <span class="inline-flex items-center px-3 py-1.5 bg-white/20 rounded-full text-sm font-medium backdrop-blur">
                                    📅 {{ stats.classes_today }} Classes Today
                                </span>
                            </div>
                        </div>
                        
                        <!-- CTA Buttons -->
                        <div class="flex flex-col sm:flex-row gap-3">
                            <Link
                                href="/teacher/classes/create"
                                class="inline-flex items-center justify-center px-6 py-3 bg-white text-emerald-600 font-semibold rounded-xl hover:bg-emerald-50 transition-all shadow-lg hover:shadow-xl hover:-translate-y-0.5"
                            >
                                ➕ Create Class
                            </Link>
                            <Link
                                :href="route('teacher.schedule')"
                                class="inline-flex items-center justify-center px-6 py-3 bg-white/20 text-white font-semibold rounded-xl hover:bg-white/30 backdrop-blur transition-all"
                            >
                                📅 View Schedule
                            </Link>
                            <Link
                                :href="route('teacher.students')"
                                class="inline-flex items-center justify-center px-6 py-3 bg-white/20 text-white font-semibold rounded-xl hover:bg-white/30 backdrop-blur transition-all"
                            >
                                👥 My Students
                            </Link>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Stats Cards with Gradients -->
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
                <!-- Students Card -->
                <div class="group bg-gradient-to-br from-emerald-500 to-teal-600 rounded-2xl p-5 text-white transition-all duration-300 hover:scale-105 hover:shadow-xl cursor-pointer relative overflow-hidden">
                    <div class="absolute -top-4 -right-4 w-20 h-20 bg-white/10 rounded-full"></div>
                    <div class="relative z-10">
                        <div class="flex items-center justify-between mb-3">
                            <span class="text-3xl group-hover:animate-bounce">👥</span>
                            <span class="text-3xl font-bold">{{ stats.total_students }}</span>
                        </div>
                        <p class="text-emerald-100 text-sm font-medium">Total Students</p>
                    </div>
                </div>

                <!-- Batches Card -->
                <div class="group bg-gradient-to-br from-blue-500 to-indigo-600 rounded-2xl p-5 text-white transition-all duration-300 hover:scale-105 hover:shadow-xl cursor-pointer relative overflow-hidden">
                    <div class="absolute -top-4 -right-4 w-20 h-20 bg-white/10 rounded-full"></div>
                    <div class="relative z-10">
                        <div class="flex items-center justify-between mb-3">
                            <span class="text-3xl group-hover:animate-bounce">📚</span>
                            <span class="text-3xl font-bold">{{ stats.active_batches }}</span>
                        </div>
                        <p class="text-blue-100 text-sm font-medium">Active Batches</p>
                    </div>
                </div>

                <!-- Classes Today Card -->
                <div class="group bg-gradient-to-br from-purple-500 to-pink-600 rounded-2xl p-5 text-white transition-all duration-300 hover:scale-105 hover:shadow-xl cursor-pointer relative overflow-hidden">
                    <div class="absolute -top-4 -right-4 w-20 h-20 bg-white/10 rounded-full"></div>
                    <div class="relative z-10">
                        <div class="flex items-center justify-between mb-3">
                            <span class="text-3xl group-hover:animate-bounce">📅</span>
                            <span class="text-3xl font-bold">{{ stats.classes_today }}</span>
                        </div>
                        <p class="text-purple-100 text-sm font-medium">Classes Today</p>
                    </div>
                </div>

                <!-- Rating Card -->
                <div class="group bg-gradient-to-br from-amber-500 to-orange-600 rounded-2xl p-5 text-white transition-all duration-300 hover:scale-105 hover:shadow-xl cursor-pointer relative overflow-hidden">
                    <div class="absolute -top-4 -right-4 w-20 h-20 bg-white/10 rounded-full"></div>
                    <div class="relative z-10">
                        <div class="flex items-center justify-between mb-3">
                            <span class="text-3xl group-hover:animate-bounce">⭐</span>
                            <span class="text-3xl font-bold">{{ stats.average_rating?.toFixed(1) || '5.0' }}</span>
                        </div>
                        <p class="text-amber-100 text-sm font-medium">Average Rating</p>
                    </div>
                </div>
            </div>

            <div class="grid lg:grid-cols-3 gap-6">
                <!-- Main Content - Left 2 Columns -->
                <div class="lg:col-span-2 space-y-6">
                    <!-- Next Class Highlight -->
                    <div v-if="todaysClasses.length > 0" class="bg-gradient-to-r from-emerald-500 to-teal-500 rounded-2xl p-6 text-white relative overflow-hidden">
                        <div class="absolute top-0 right-0 w-32 h-32 bg-white/10 rounded-full -translate-y-1/2 translate-x-1/2"></div>
                        <div class="relative z-10">
                            <div class="flex items-center justify-between mb-4">
                                <span class="px-3 py-1.5 bg-white/20 rounded-full text-sm font-medium backdrop-blur">
                                    🎯 Next Class
                                </span>
                                <span class="text-emerald-100 text-sm">in {{ getTimeUntil(todaysClasses[0].scheduled_at) }}</span>
                            </div>
                            <h3 class="text-2xl font-bold mb-2">{{ todaysClasses[0].title }}</h3>
                            <p class="text-emerald-100 mb-4">
                                {{ todaysClasses[0].batch?.name || 'Private Session' }} • {{ formatTime(todaysClasses[0].scheduled_at) }}
                            </p>
                            <div class="flex gap-3">
                                <a v-if="todaysClasses[0].zoom_start_url" 
                                   :href="todaysClasses[0].zoom_start_url" 
                                   target="_blank"
                                   class="inline-flex items-center px-6 py-3 bg-white text-emerald-600 font-semibold rounded-xl hover:bg-emerald-50 transition-all shadow-lg">
                                    🎥 Start Class
                                </a>
                                <Link :href="route('teacher.attendance', todaysClasses[0].id)"
                                      class="inline-flex items-center px-6 py-3 bg-white/20 text-white font-semibold rounded-xl hover:bg-white/30 backdrop-blur transition-all">
                                    ✅ Take Attendance
                                </Link>
                            </div>
                        </div>
                    </div>

                    <!-- Today's Classes List -->
                    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
                        <div class="p-5 border-b border-gray-100 dark:border-gray-700 flex items-center justify-between">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white flex items-center gap-2">
                                📅 Today's Classes
                            </h3>
                            <span class="px-3 py-1 bg-emerald-100 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-300 text-sm font-medium rounded-full">
                                {{ todaysClasses.length }} classes
                            </span>
                        </div>
                        
                        <div class="p-5">
                            <div v-if="todaysClasses.length === 0" class="text-center py-12">
                                <div class="w-20 h-20 bg-gradient-to-br from-gray-100 to-gray-200 dark:from-gray-700 dark:to-gray-600 rounded-full flex items-center justify-center mx-auto mb-4">
                                    <span class="text-4xl">📅</span>
                                </div>
                                <p class="text-gray-500 dark:text-gray-400 mb-2">No classes scheduled for today</p>
                                <p class="text-sm text-gray-400 dark:text-gray-500">Enjoy your day off! 🎉</p>
                            </div>
                            <div v-else class="space-y-3">
                                <div v-for="cls in todaysClasses" :key="cls.id" 
                                     class="group flex items-center justify-between p-4 bg-gradient-to-r from-gray-50 to-transparent dark:from-gray-700/50 rounded-xl border-2 border-transparent hover:border-emerald-200 dark:hover:border-emerald-800 transition-all duration-300 hover:shadow-md">
                                    <div class="flex items-center gap-4">
                                        <div class="w-16 h-16 bg-gradient-to-br from-emerald-500 to-teal-500 rounded-xl flex flex-col items-center justify-center text-white shadow-lg group-hover:scale-110 transition-transform">
                                            <span class="text-lg font-bold">{{ formatTime(cls.scheduled_at).split(':')[0] }}</span>
                                            <span class="text-xs opacity-80">{{ formatTime(cls.scheduled_at).includes('PM') ? 'PM' : 'AM' }}</span>
                                        </div>
                                        <div>
                                            <p class="font-semibold text-gray-900 dark:text-white group-hover:text-emerald-600 dark:group-hover:text-emerald-400 transition-colors">{{ cls.title }}</p>
                                            <p class="text-sm text-gray-500 dark:text-gray-400">{{ cls.batch?.name }} • {{ cls.duration_minutes }} min</p>
                                        </div>
                                    </div>
                                    <div class="flex items-center gap-3">
                                        <span :class="getStatusColor(cls.status)" class="px-3 py-1 text-xs font-semibold rounded-full">
                                            {{ cls.status }}
                                        </span>
                                        <Link :href="route('teacher.attendance', cls.id)"
                                              class="px-4 py-2 bg-emerald-500 hover:bg-emerald-600 text-white text-sm font-medium rounded-lg transition-colors shadow-sm hover:shadow-md">
                                            Attendance
                                        </Link>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Upcoming Classes -->
                    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
                        <div class="p-5 border-b border-gray-100 dark:border-gray-700 flex items-center justify-between">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white flex items-center gap-2">
                                🗓️ Upcoming This Week
                            </h3>
                            <Link :href="route('teacher.schedule')" class="text-emerald-500 hover:text-emerald-600 text-sm font-medium">
                                View All →
                            </Link>
                        </div>
                        
                        <div class="p-5">
                            <div v-if="upcomingClasses.length === 0" class="text-center py-8 text-gray-500 dark:text-gray-400">
                                <span class="text-3xl block mb-2">🎉</span>
                                No more classes scheduled this week
                            </div>
                            <div v-else class="space-y-3">
                                <div v-for="cls in upcomingClasses.slice(0, 5)" :key="cls.id" 
                                     class="group flex items-center justify-between p-3 hover:bg-gray-50 dark:hover:bg-gray-700/50 rounded-xl transition-all">
                                    <div class="flex items-center gap-3">
                                        <div class="w-14 h-14 bg-gradient-to-br from-blue-100 to-indigo-100 dark:from-blue-900/30 dark:to-indigo-900/30 rounded-xl flex flex-col items-center justify-center group-hover:scale-110 transition-transform">
                                            <span class="text-xs font-bold text-blue-600 dark:text-blue-400 uppercase">{{ new Date(cls.scheduled_at).toLocaleDateString('en-US', { weekday: 'short' }) }}</span>
                                            <span class="text-lg font-bold text-blue-700 dark:text-blue-300">{{ new Date(cls.scheduled_at).getDate() }}</span>
                                        </div>
                                        <div>
                                            <p class="font-medium text-gray-900 dark:text-white">{{ cls.title }}</p>
                                            <p class="text-sm text-gray-500 dark:text-gray-400">
                                                {{ formatDate(cls.scheduled_at) }} at {{ formatTime(cls.scheduled_at) }}
                                            </p>
                                        </div>
                                    </div>
                                    <span class="text-sm text-gray-400 dark:text-gray-500 group-hover:text-emerald-500 transition-colors">
                                        {{ cls.batch?.name }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Sidebar - Right Column -->
                <div class="space-y-6">
                    <!-- My Batches Card -->
                    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
                        <div class="p-5 border-b border-gray-100 dark:border-gray-700 flex items-center justify-between">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">📚 My Batches</h3>
                            <Link :href="route('teacher.batches')" class="text-emerald-500 hover:text-emerald-600 text-sm font-medium">
                                View All
                            </Link>
                        </div>
                        <div class="p-5">
                            <div v-if="batches.length === 0" class="text-center py-6 text-gray-500 dark:text-gray-400">
                                <span class="text-3xl block mb-2">📭</span>
                                No batches assigned yet
                            </div>
                            <div v-else class="space-y-3">
                                <Link v-for="batch in batches.slice(0, 4)" :key="batch.id"
                                      :href="route('teacher.batch.detail', batch.id)"
                                      class="group block p-4 bg-gradient-to-r from-gray-50 to-transparent dark:from-gray-700/50 rounded-xl hover:from-emerald-50 dark:hover:from-emerald-900/20 transition-all duration-300 border border-transparent hover:border-emerald-200 dark:hover:border-emerald-800">
                                    <div class="flex items-center justify-between">
                                        <div>
                                            <p class="font-semibold text-gray-900 dark:text-white group-hover:text-emerald-600 dark:group-hover:text-emerald-400 transition-colors">{{ batch.name }}</p>
                                            <p class="text-sm text-gray-500 dark:text-gray-400">{{ batch.course?.title }}</p>
                                        </div>
                                        <div class="flex items-center gap-2 px-3 py-1.5 bg-emerald-100 dark:bg-emerald-900/30 rounded-full">
                                            <span class="text-emerald-600 dark:text-emerald-400">👥</span>
                                            <span class="text-sm font-semibold text-emerald-700 dark:text-emerald-300">{{ batch.enrolled_students }}</span>
                                        </div>
                                    </div>
                                </Link>
                            </div>
                        </div>
                    </div>

                    <!-- Quick Actions Card -->
                    <div class="bg-gradient-to-br from-slate-800 to-slate-900 rounded-2xl p-6 text-white relative overflow-hidden">
                        <div class="absolute top-0 right-0 w-32 h-32 bg-white/5 rounded-full -translate-y-1/2 translate-x-1/2"></div>
                        <div class="relative z-10">
                            <h3 class="text-lg font-semibold mb-4 flex items-center gap-2">
                                ⚡ Quick Actions
                            </h3>
                            <div class="space-y-2">
                                <Link :href="route('teacher.schedule')" 
                                      class="flex items-center gap-3 p-3 bg-white/10 hover:bg-white/20 rounded-xl transition-all group">
                                    <div class="p-2 bg-blue-500/30 rounded-lg group-hover:bg-blue-500/50 transition-colors">
                                        <span class="text-xl">📅</span>
                                    </div>
                                    <span class="font-medium">View Schedule</span>
                                    <span class="ml-auto opacity-50 group-hover:opacity-100 group-hover:translate-x-1 transition-all">→</span>
                                </Link>
                                <Link :href="route('teacher.students')" 
                                      class="flex items-center gap-3 p-3 bg-white/10 hover:bg-white/20 rounded-xl transition-all group">
                                    <div class="p-2 bg-emerald-500/30 rounded-lg group-hover:bg-emerald-500/50 transition-colors">
                                        <span class="text-xl">👥</span>
                                    </div>
                                    <span class="font-medium">My Students</span>
                                    <span class="ml-auto opacity-50 group-hover:opacity-100 group-hover:translate-x-1 transition-all">→</span>
                                </Link>
                                <Link :href="route('teacher.resources')" 
                                      class="flex items-center gap-3 p-3 bg-white/10 hover:bg-white/20 rounded-xl transition-all group">
                                    <div class="p-2 bg-purple-500/30 rounded-lg group-hover:bg-purple-500/50 transition-colors">
                                        <span class="text-xl">📁</span>
                                    </div>
                                    <span class="font-medium">Resources</span>
                                    <span class="ml-auto opacity-50 group-hover:opacity-100 group-hover:translate-x-1 transition-all">→</span>
                                </Link>
                                <Link :href="route('teacher.earnings')" 
                                      class="flex items-center gap-3 p-3 bg-white/10 hover:bg-white/20 rounded-xl transition-all group">
                                    <div class="p-2 bg-amber-500/30 rounded-lg group-hover:bg-amber-500/50 transition-colors">
                                        <span class="text-xl">💰</span>
                                    </div>
                                    <span class="font-medium">Earnings</span>
                                    <span class="ml-auto opacity-50 group-hover:opacity-100 group-hover:translate-x-1 transition-all">→</span>
                                </Link>
                            </div>
                        </div>
                    </div>

                    <!-- Rating Card -->
                    <div class="bg-gradient-to-br from-amber-400 via-orange-500 to-red-500 rounded-2xl p-6 text-white relative overflow-hidden">
                        <div class="absolute -top-6 -right-6 w-24 h-24 bg-white/10 rounded-full"></div>
                        <div class="absolute -bottom-4 -left-4 w-16 h-16 bg-white/10 rounded-full"></div>
                        <div class="relative z-10">
                            <div class="flex items-center justify-between mb-4">
                                <h3 class="font-semibold">Your Rating</h3>
                                <span class="text-3xl">⭐</span>
                            </div>
                            <p class="text-5xl font-bold mb-2">{{ stats.average_rating?.toFixed(1) || '5.0' }}</p>
                            <p class="text-amber-100">Based on {{ stats.total_reviews || 0 }} reviews</p>
                            <div class="mt-4 flex gap-1">
                                <span v-for="i in 5" :key="i" class="text-2xl" :class="i <= Math.round(stats.average_rating || 5) ? '' : 'opacity-30'">⭐</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </TeacherLayout>
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
