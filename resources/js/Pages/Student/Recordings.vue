<script setup>
import { Head, Link } from '@inertiajs/vue3';
import { ref, computed } from 'vue';
import StudentLayout from '@/Layouts/StudentLayout.vue';

const props = defineProps({
    recordings: {
        type: Array,
        default: () => []
    }
});

const searchQuery = ref('');
const selectedCourse = ref('');

const filteredRecordings = computed(() => {
    let result = props.recordings;
    
    if (searchQuery.value) {
        const query = searchQuery.value.toLowerCase();
        result = result.filter(r => 
            r.title.toLowerCase().includes(query) ||
            r.course?.title?.toLowerCase().includes(query)
        );
    }
    
    if (selectedCourse.value) {
        result = result.filter(r => r.course_id === selectedCourse.value);
    }
    
    return result;
});

const uniqueCourses = computed(() => {
    const courses = {};
    props.recordings.forEach(r => {
        if (r.course) {
            courses[r.course.id] = r.course.title;
        }
    });
    return Object.entries(courses).map(([id, title]) => ({ id, title }));
});

const formatDate = (dateString) => {
    return new Date(dateString).toLocaleDateString('en-US', {
        month: 'short',
        day: 'numeric',
        year: 'numeric'
    });
};

const formatDuration = (minutes) => {
    if (!minutes) return 'N/A';
    const hrs = Math.floor(minutes / 60);
    const mins = minutes % 60;
    return hrs > 0 ? `${hrs}h ${mins}m` : `${mins}m`;
};
</script>

<template>
    <Head title="Recorded Classes" />

    <StudentLayout>
        <template #header>
            <div>
                <h2 class="font-display text-xl font-bold text-gray-900">
                    Recorded Classes 🎥
                </h2>
                <p class="text-gray-500 text-sm">Watch your missed classes anytime</p>
            </div>
        </template>

        <div class="py-8">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <!-- Filters -->
                <div class="bg-white rounded-2xl shadow-soft p-6 mb-6">
                    <div class="flex flex-col md:flex-row gap-4">
                        <div class="flex-1">
                            <div class="relative">
                                <input
                                    v-model="searchQuery"
                                    type="text"
                                    placeholder="Search recordings..."
                                    class="w-full pl-10 pr-4 py-3 rounded-xl border-gray-200 focus:border-primary-500 focus:ring-primary-500"
                                />
                                <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                                </svg>
                            </div>
                        </div>
                        <div class="md:w-64">
                            <select
                                v-model="selectedCourse"
                                class="w-full py-3 rounded-xl border-gray-200 focus:border-primary-500 focus:ring-primary-500"
                            >
                                <option value="">All Courses</option>
                                <option v-for="course in uniqueCourses" :key="course.id" :value="course.id">
                                    {{ course.title }}
                                </option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Recordings Grid -->
                <div v-if="filteredRecordings.length" class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
                    <div
                        v-for="recording in filteredRecordings"
                        :key="recording.id"
                        class="bg-white rounded-2xl shadow-soft overflow-hidden hover:shadow-lg transition-all group"
                    >
                        <!-- Video Thumbnail -->
                        <div class="relative aspect-video bg-gray-900">
                            <div class="absolute inset-0 flex items-center justify-center">
                                <div class="w-16 h-16 rounded-full bg-white/20 flex items-center justify-center group-hover:bg-primary-500 transition-colors cursor-pointer">
                                    <svg class="w-8 h-8 text-white ml-1" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M8 5v14l11-7z"/>
                                    </svg>
                                </div>
                            </div>
                            <!-- Duration Badge -->
                            <div class="absolute bottom-3 right-3 bg-black/70 text-white text-xs px-2 py-1 rounded">
                                {{ formatDuration(recording.duration_minutes) }}
                            </div>
                        </div>

                        <!-- Info -->
                        <div class="p-4">
                            <h3 class="font-semibold text-gray-900 mb-1 line-clamp-2">{{ recording.title }}</h3>
                            <p class="text-sm text-gray-500 mb-3">{{ recording.course?.title }}</p>
                            
                            <div class="flex items-center justify-between text-sm">
                                <span class="text-gray-500">{{ formatDate(recording.recorded_at) }}</span>
                                <a
                                    :href="recording.recording_url"
                                    target="_blank"
                                    class="text-primary-500 hover:text-primary-600 font-medium"
                                >
                                    Watch →
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Empty State -->
                <div v-else class="bg-white rounded-2xl shadow-soft p-12 text-center">
                    <span class="text-6xl mb-4 block">🎥</span>
                    <h3 class="text-xl font-semibold text-gray-900 mb-2">No Recordings Yet</h3>
                    <p class="text-gray-500 mb-6">
                        {{ searchQuery || selectedCourse ? 'No recordings match your filters.' : 'Class recordings will appear here after your classes.' }}
                    </p>
                    <Link
                        href="/enrollments"
                        class="inline-flex items-center px-6 py-3 bg-primary-500 hover:bg-primary-600 text-white font-semibold rounded-xl transition-colors"
                    >
                        View My Courses
                    </Link>
                </div>
            </div>
        </div>
    </StudentLayout>
</template>
