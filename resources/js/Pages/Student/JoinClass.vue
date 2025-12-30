<script setup>
import { Head, Link, usePage } from '@inertiajs/vue3';
import StudentLayout from '@/Layouts/StudentLayout.vue';
import { computed } from 'vue';

const props = defineProps({
    classSession: Object,
});

const page = usePage();

const formatTime = (dateString) => {
    return new Date(dateString).toLocaleTimeString('en-US', {
        hour: 'numeric',
        minute: '2-digit',
        hour12: true,
    });
};

const formatDate = (dateString) => {
    return new Date(dateString).toLocaleDateString('en-US', {
        weekday: 'long',
        month: 'long',
        day: 'numeric',
        year: 'numeric',
    });
};

const isLive = computed(() => {
    const now = new Date();
    const classTime = new Date(props.classSession.scheduled_at);
    const endTime = new Date(classTime.getTime() + props.classSession.duration_minutes * 60000);
    return now >= classTime && now <= endTime;
});

const isPast = computed(() => {
    const now = new Date();
    const classTime = new Date(props.classSession.scheduled_at);
    const endTime = new Date(classTime.getTime() + props.classSession.duration_minutes * 60000);
    return now > endTime;
});

const getTimeUntil = () => {
    const now = new Date();
    const classTime = new Date(props.classSession.scheduled_at);
    const diffMs = classTime - now;
    const diffMins = Math.floor(diffMs / 60000);
    const diffHours = Math.floor(diffMins / 60);
    const diffDays = Math.floor(diffHours / 24);
    
    if (diffMs < 0) return 'Class has started';
    if (diffDays > 0) return `${diffDays} days ${diffHours % 24} hours`;
    if (diffHours > 0) return `${diffHours} hours ${diffMins % 60} minutes`;
    return `${diffMins} minutes`;
};
</script>

<template>
    <Head :title="`Join: ${classSession.title}`" />

    <StudentLayout>
        <div class="max-w-2xl mx-auto space-y-6">
            <!-- Header -->
            <div class="text-center mb-8">
                <Link href="/enrollments" class="inline-flex items-center gap-2 text-gray-500 hover:text-emerald-600 mb-4 text-sm">
                    ← Back to My Courses
                </Link>
            </div>

            <!-- Class Card -->
            <div class="bg-gradient-to-br from-emerald-600 via-teal-500 to-cyan-500 rounded-3xl p-8 text-white relative overflow-hidden shadow-2xl">
                <!-- Background decoration -->
                <div class="absolute top-0 right-0 w-48 h-48 bg-white/10 rounded-full -translate-y-1/2 translate-x-1/2"></div>
                <div class="absolute bottom-0 left-0 w-32 h-32 bg-white/5 rounded-full translate-y-1/2 -translate-x-1/2"></div>
                <div class="absolute top-1/2 right-10 w-16 h-16 bg-white/10 rounded-full animate-pulse"></div>
                
                <div class="relative z-10">
                    <!-- Live Badge -->
                    <div v-if="isLive" class="inline-flex items-center gap-2 px-4 py-2 bg-red-500 rounded-full text-sm font-semibold mb-4 animate-pulse">
                        <span class="w-2 h-2 bg-white rounded-full animate-ping"></span>
                        🔴 LIVE NOW
                    </div>
                    <div v-else-if="isPast" class="inline-flex items-center gap-2 px-4 py-2 bg-gray-600 rounded-full text-sm font-medium mb-4">
                        ✓ Class Ended
                    </div>
                    <div v-else class="inline-flex items-center gap-2 px-4 py-2 bg-white/20 rounded-full text-sm font-medium mb-4 backdrop-blur">
                        ⏰ Starts in {{ getTimeUntil() }}
                    </div>
                    
                    <h1 class="text-3xl font-bold mb-3">{{ classSession.title }}</h1>
                    <p class="text-emerald-100 text-lg mb-6">
                        {{ classSession.batch?.name }} • {{ classSession.course?.title }}
                    </p>
                    
                    <div class="grid grid-cols-2 gap-4 text-sm">
                        <div class="bg-white/15 rounded-xl p-4 backdrop-blur">
                            <p class="text-emerald-100 mb-1">📅 Date</p>
                            <p class="font-semibold">{{ formatDate(classSession.scheduled_at) }}</p>
                        </div>
                        <div class="bg-white/15 rounded-xl p-4 backdrop-blur">
                            <p class="text-emerald-100 mb-1">🕐 Time</p>
                            <p class="font-semibold">{{ formatTime(classSession.scheduled_at) }}</p>
                        </div>
                        <div class="bg-white/15 rounded-xl p-4 backdrop-blur">
                            <p class="text-emerald-100 mb-1">⏱️ Duration</p>
                            <p class="font-semibold">{{ classSession.duration_minutes }} minutes</p>
                        </div>
                        <div class="bg-white/15 rounded-xl p-4 backdrop-blur">
                            <p class="text-emerald-100 mb-1">👨‍🏫 Teacher</p>
                            <p class="font-semibold">{{ classSession.teacher?.name || 'TBA' }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Join Button -->
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 p-8 text-center">
                <div v-if="classSession.zoom_join_url" class="space-y-6">
                    <div class="w-20 h-20 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-2xl flex items-center justify-center mx-auto shadow-lg">
                        <span class="text-4xl">🎥</span>
                    </div>
                    <div>
                        <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-2">Ready to Join?</h3>
                        <p class="text-gray-500 dark:text-gray-400 mb-6">Click the button below to join your class via Zoom</p>
                    </div>
                    <a 
                        :href="classSession.zoom_join_url"
                        target="_blank"
                        :class="[
                            'inline-flex items-center justify-center gap-3 px-8 py-4 text-lg font-semibold rounded-2xl transition-all shadow-lg',
                            isLive 
                                ? 'bg-gradient-to-r from-red-500 to-pink-500 text-white hover:from-red-600 hover:to-pink-600 animate-pulse'
                                : isPast
                                    ? 'bg-gray-300 text-gray-500 cursor-not-allowed'
                                    : 'bg-gradient-to-r from-blue-500 to-indigo-600 text-white hover:from-blue-600 hover:to-indigo-700'
                        ]"
                        :disabled="isPast"
                    >
                        🎥 {{ isLive ? 'Join Class Now!' : isPast ? 'Class Ended' : 'Join via Zoom' }}
                    </a>
                </div>
                <div v-else class="space-y-4">
                    <div class="w-20 h-20 bg-gray-100 dark:bg-gray-700 rounded-2xl flex items-center justify-center mx-auto">
                        <span class="text-4xl">⏳</span>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-900 dark:text-white">Join Link Coming Soon</h3>
                    <p class="text-gray-500 dark:text-gray-400">The teacher will share the class link before the session starts.</p>
                </div>
            </div>

            <!-- Instructions -->
            <div class="bg-amber-50 dark:bg-amber-900/20 rounded-2xl p-6 border border-amber-200 dark:border-amber-800">
                <h4 class="font-semibold text-amber-800 dark:text-amber-300 mb-3 flex items-center gap-2">
                    💡 Before Joining
                </h4>
                <ul class="text-amber-700 dark:text-amber-400 text-sm space-y-2">
                    <li>• Make sure you have Zoom installed on your device</li>
                    <li>• Test your microphone and camera before class</li>
                    <li>• Find a quiet place with good internet connection</li>
                    <li>• Keep your Quran or study materials ready</li>
                    <li>• Join 5 minutes early to avoid missing the start</li>
                </ul>
            </div>
        </div>
    </StudentLayout>
</template>
