<script setup>
import { Head, Link, router } from '@inertiajs/vue3';
import { ref, onMounted, onUnmounted, computed } from 'vue';
import StudentLayout from '@/Layouts/StudentLayout.vue';

const props = defineProps({
    classSession: {
        type: Object,
        required: true
    },
    auth: Object
});

const joinStatus = ref('preparing'); // preparing, ready, joining, joined, error
const systemCheck = ref({
    camera: { status: 'pending', message: 'Checking camera...' },
    microphone: { status: 'pending', message: 'Checking microphone...' },
    internet: { status: 'pending', message: 'Checking connection...' },
});
const errorMessage = ref('');
const timeUntilClass = ref('');

// Timer for countdown
let countdownInterval = null;

const classTime = computed(() => {
    const date = new Date(props.classSession.scheduled_at);
    return date.toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit' });
});

const classDate = computed(() => {
    const date = new Date(props.classSession.scheduled_at);
    return date.toLocaleDateString('en-US', { weekday: 'long', month: 'long', day: 'numeric' });
});

const canJoin = computed(() => {
    const classTime = new Date(props.classSession.scheduled_at);
    const now = new Date();
    const diffMinutes = (classTime - now) / (1000 * 60);
    return diffMinutes <= 10; // Can join 10 minutes before
});

const updateCountdown = () => {
    const classTime = new Date(props.classSession.scheduled_at);
    const now = new Date();
    const diff = classTime - now;

    if (diff <= 0) {
        timeUntilClass.value = 'Class has started!';
        return;
    }

    const hours = Math.floor(diff / (1000 * 60 * 60));
    const minutes = Math.floor((diff % (1000 * 60 * 60)) / (1000 * 60));
    const seconds = Math.floor((diff % (1000 * 60)) / 1000);

    if (hours > 0) {
        timeUntilClass.value = `${hours}h ${minutes}m ${seconds}s`;
    } else if (minutes > 0) {
        timeUntilClass.value = `${minutes}m ${seconds}s`;
    } else {
        timeUntilClass.value = `${seconds}s`;
    }
};

const runSystemCheck = async () => {
    // Check camera
    try {
        const stream = await navigator.mediaDevices.getUserMedia({ video: true });
        stream.getTracks().forEach(track => track.stop());
        systemCheck.value.camera = { status: 'success', message: 'Camera ready' };
    } catch (e) {
        systemCheck.value.camera = { status: 'warning', message: 'Camera not accessible (optional)' };
    }

    // Check microphone
    try {
        const stream = await navigator.mediaDevices.getUserMedia({ audio: true });
        stream.getTracks().forEach(track => track.stop());
        systemCheck.value.microphone = { status: 'success', message: 'Microphone ready' };
    } catch (e) {
        systemCheck.value.microphone = { status: 'error', message: 'Microphone not accessible' };
    }

    // Check internet
    try {
        const start = performance.now();
        await fetch('/api/health', { method: 'HEAD' });
        const latency = Math.round(performance.now() - start);
        systemCheck.value.internet = { 
            status: latency < 200 ? 'success' : 'warning', 
            message: `Connection: ${latency}ms` 
        };
    } catch (e) {
        systemCheck.value.internet = { status: 'success', message: 'Connection OK' };
    }

    joinStatus.value = 'ready';
};

const joinClass = () => {
    if (!props.classSession.zoom_join_url) {
        errorMessage.value = 'Meeting link not available yet. Please refresh the page.';
        joinStatus.value = 'error';
        return;
    }

    joinStatus.value = 'joining';
    
    // Record join attempt
    fetch(`/api/v1/classes/${props.classSession.id}/join`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
        },
    });

    // Open Zoom link in new window
    window.open(props.classSession.zoom_join_url, '_blank');
    
    setTimeout(() => {
        joinStatus.value = 'joined';
    }, 2000);
};

const getStatusIcon = (status) => {
    switch(status) {
        case 'success': return '✅';
        case 'warning': return '⚠️';
        case 'error': return '❌';
        default: return '⏳';
    }
};

const getStatusColor = (status) => {
    switch(status) {
        case 'success': return 'text-green-600';
        case 'warning': return 'text-orange-500';
        case 'error': return 'text-red-500';
        default: return 'text-gray-400';
    }
};

onMounted(() => {
    updateCountdown();
    countdownInterval = setInterval(updateCountdown, 1000);
    runSystemCheck();
});

onUnmounted(() => {
    if (countdownInterval) {
        clearInterval(countdownInterval);
    }
});
</script>

<template>
    <Head :title="`Join Class: ${classSession.title}`" />

    <StudentLayout>
        <div class="py-8">
            <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
                <!-- Class Info Card -->
                <div class="bg-white rounded-2xl shadow-soft p-8 mb-6">
                    <div class="flex items-start justify-between mb-6">
                        <div>
                            <span class="inline-flex items-center px-3 py-1 bg-primary-100 text-primary-700 text-sm font-medium rounded-full mb-3">
                                {{ classSession.batch?.course?.title || 'Private Class' }}
                            </span>
                            <h1 class="text-2xl font-bold text-gray-900 mb-2">{{ classSession.title }}</h1>
                            <p class="text-gray-500">{{ classSession.batch?.name || 'One-on-One Session' }}</p>
                        </div>
                        <span class="text-4xl">📖</span>
                    </div>

                    <!-- Class Details -->
                    <div class="grid sm:grid-cols-3 gap-4 mb-6">
                        <div class="bg-gray-50 rounded-xl p-4 text-center">
                            <p class="text-sm text-gray-500 mb-1">Date</p>
                            <p class="font-semibold text-gray-900">{{ classDate }}</p>
                        </div>
                        <div class="bg-gray-50 rounded-xl p-4 text-center">
                            <p class="text-sm text-gray-500 mb-1">Time</p>
                            <p class="font-semibold text-gray-900">{{ classTime }}</p>
                        </div>
                        <div class="bg-gray-50 rounded-xl p-4 text-center">
                            <p class="text-sm text-gray-500 mb-1">Duration</p>
                            <p class="font-semibold text-gray-900">{{ classSession.duration_minutes }} min</p>
                        </div>
                    </div>

                    <!-- Teacher Info -->
                    <div v-if="classSession.teacher" class="flex items-center p-4 bg-gray-50 rounded-xl">
                        <div class="w-12 h-12 rounded-full bg-primary-100 flex items-center justify-center text-xl">
                            👨‍🏫
                        </div>
                        <div class="ml-4">
                            <p class="font-medium text-gray-900">{{ classSession.teacher.name }}</p>
                            <p class="text-sm text-gray-500">Your Instructor</p>
                        </div>
                    </div>
                </div>

                <!-- Countdown Timer -->
                <div v-if="!canJoin" class="bg-gradient-to-br from-primary-500 to-primary-600 rounded-2xl p-8 text-white text-center mb-6">
                    <p class="text-primary-100 mb-2">Class starts in</p>
                    <p class="text-5xl font-bold mb-4">{{ timeUntilClass }}</p>
                    <p class="text-primary-100 text-sm">You can join 10 minutes before the class starts</p>
                </div>

                <!-- System Check -->
                <div class="bg-white rounded-2xl shadow-soft p-8 mb-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">System Check</h3>
                    
                    <div class="space-y-3">
                        <div v-for="(check, key) in systemCheck" :key="key" class="flex items-center justify-between p-3 bg-gray-50 rounded-xl">
                            <div class="flex items-center">
                                <span class="text-xl mr-3">{{ getStatusIcon(check.status) }}</span>
                                <span class="font-medium text-gray-900 capitalize">{{ key }}</span>
                            </div>
                            <span :class="['text-sm', getStatusColor(check.status)]">{{ check.message }}</span>
                        </div>
                    </div>
                </div>

                <!-- Join Button Area -->
                <div class="bg-white rounded-2xl shadow-soft p-8 text-center">
                    <template v-if="joinStatus === 'preparing'">
                        <div class="animate-pulse">
                            <div class="w-16 h-16 bg-gray-200 rounded-full mx-auto mb-4"></div>
                            <p class="text-gray-500">Preparing your classroom...</p>
                        </div>
                    </template>

                    <template v-else-if="joinStatus === 'ready'">
                        <template v-if="canJoin">
                            <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
                                <span class="text-3xl">🎓</span>
                            </div>
                            <h3 class="text-xl font-semibold text-gray-900 mb-2">Ready to Join!</h3>
                            <p class="text-gray-500 mb-6">Your classroom is ready. Click below to join the Zoom meeting.</p>
                            <button
                                @click="joinClass"
                                class="px-10 py-4 bg-green-500 hover:bg-green-600 text-white text-lg font-semibold rounded-xl transition-all hover:shadow-lg transform hover:-translate-y-0.5"
                            >
                                🎥 Join Class Now
                            </button>
                        </template>
                        <template v-else>
                            <div class="w-16 h-16 bg-orange-100 rounded-full flex items-center justify-center mx-auto mb-4">
                                <span class="text-3xl">⏰</span>
                            </div>
                            <h3 class="text-xl font-semibold text-gray-900 mb-2">Not Yet Time</h3>
                            <p class="text-gray-500">The join button will be available 10 minutes before class.</p>
                        </template>
                    </template>

                    <template v-else-if="joinStatus === 'joining'">
                        <div class="animate-bounce">
                            <div class="w-16 h-16 bg-primary-100 rounded-full flex items-center justify-center mx-auto mb-4">
                                <span class="text-3xl">🚀</span>
                            </div>
                        </div>
                        <p class="text-gray-700 font-medium">Launching Zoom...</p>
                    </template>

                    <template v-else-if="joinStatus === 'joined'">
                        <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
                            <span class="text-3xl">✅</span>
                        </div>
                        <h3 class="text-xl font-semibold text-gray-900 mb-2">Zoom Launched!</h3>
                        <p class="text-gray-500 mb-6">If Zoom didn't open, click the button below.</p>
                        <a
                            :href="classSession.zoom_join_url"
                            target="_blank"
                            class="inline-block px-8 py-3 bg-primary-500 hover:bg-primary-600 text-white font-semibold rounded-xl"
                        >
                            Open Zoom Again
                        </a>
                    </template>

                    <template v-else-if="joinStatus === 'error'">
                        <div class="w-16 h-16 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-4">
                            <span class="text-3xl">❌</span>
                        </div>
                        <h3 class="text-xl font-semibold text-red-600 mb-2">Unable to Join</h3>
                        <p class="text-gray-500 mb-6">{{ errorMessage }}</p>
                        <button
                            @click="router.reload()"
                            class="px-8 py-3 bg-gray-100 hover:bg-gray-200 text-gray-700 font-semibold rounded-xl"
                        >
                            Refresh Page
                        </button>
                    </template>
                </div>

                <!-- Tips -->
                <div class="mt-6 p-6 bg-blue-50 rounded-xl">
                    <h4 class="font-medium text-blue-900 mb-3">💡 Tips for a Great Class</h4>
                    <ul class="text-sm text-blue-700 space-y-2">
                        <li>• Find a quiet place with minimal background noise</li>
                        <li>• Keep your Quran or reading material ready</li>
                        <li>• Test your audio before the teacher starts</li>
                        <li>• Have a notebook ready for notes</li>
                    </ul>
                </div>

                <!-- Back Link -->
                <div class="mt-6 text-center">
                    <Link href="/dashboard" class="text-primary-500 hover:text-primary-600 font-medium">
                        ← Back to Dashboard
                    </Link>
                </div>
            </div>
        </div>
    </StudentLayout>
</template>
