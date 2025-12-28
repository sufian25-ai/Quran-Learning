<script setup>
import { Head, Link } from '@inertiajs/vue3';
import { ref, computed } from 'vue';
import StudentLayout from '@/Layouts/StudentLayout.vue';

const props = defineProps({
    resources: {
        type: Array,
        default: () => []
    }
});

const searchQuery = ref('');
const selectedType = ref('');
const selectedCourse = ref('');

const filteredResources = computed(() => {
    let result = props.resources;
    
    if (searchQuery.value) {
        const query = searchQuery.value.toLowerCase();
        result = result.filter(r => 
            r.title.toLowerCase().includes(query) ||
            r.description?.toLowerCase().includes(query)
        );
    }
    
    if (selectedType.value) {
        result = result.filter(r => r.type === selectedType.value);
    }
    
    if (selectedCourse.value) {
        result = result.filter(r => r.course_id === selectedCourse.value);
    }
    
    return result;
});

const uniqueCourses = computed(() => {
    const courses = {};
    props.resources.forEach(r => {
        if (r.course) {
            courses[r.course.id] = r.course.title;
        }
    });
    return Object.entries(courses).map(([id, title]) => ({ id, title }));
});

const resourceTypes = ['pdf', 'video', 'audio', 'document', 'image'];

const formatDate = (dateString) => {
    return new Date(dateString).toLocaleDateString('en-US', {
        month: 'short',
        day: 'numeric',
        year: 'numeric'
    });
};

const formatFileSize = (bytes) => {
    if (!bytes) return 'N/A';
    const sizes = ['Bytes', 'KB', 'MB', 'GB'];
    const i = Math.floor(Math.log(bytes) / Math.log(1024));
    return `${(bytes / Math.pow(1024, i)).toFixed(1)} ${sizes[i]}`;
};

const getTypeIcon = (type) => {
    const icons = {
        'pdf': '📄',
        'video': '🎥',
        'audio': '🎵',
        'document': '📝',
        'image': '🖼️',
    };
    return icons[type] || '📁';
};

const getTypeColor = (type) => {
    const colors = {
        'pdf': 'bg-red-100 text-red-600',
        'video': 'bg-purple-100 text-purple-600',
        'audio': 'bg-blue-100 text-blue-600',
        'document': 'bg-green-100 text-green-600',
        'image': 'bg-yellow-100 text-yellow-600',
    };
    return colors[type] || 'bg-gray-100 text-gray-600';
};
</script>

<template>
    <Head title="Resources" />

    <StudentLayout>
        <template #header>
            <div>
                <h2 class="font-display text-xl font-bold text-gray-900">
                    Learning Resources 📚
                </h2>
                <p class="text-gray-500 text-sm">Download course materials and study guides</p>
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
                                    placeholder="Search resources..."
                                    class="w-full pl-10 pr-4 py-3 rounded-xl border-gray-200 focus:border-primary-500 focus:ring-primary-500"
                                />
                                <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                                </svg>
                            </div>
                        </div>
                        <div class="md:w-48">
                            <select
                                v-model="selectedType"
                                class="w-full py-3 rounded-xl border-gray-200 focus:border-primary-500 focus:ring-primary-500"
                            >
                                <option value="">All Types</option>
                                <option v-for="type in resourceTypes" :key="type" :value="type" class="capitalize">
                                    {{ type.toUpperCase() }}
                                </option>
                            </select>
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

                <!-- Resources List -->
                <div v-if="filteredResources.length" class="space-y-4">
                    <div
                        v-for="resource in filteredResources"
                        :key="resource.id"
                        class="bg-white rounded-2xl shadow-soft p-6 hover:shadow-lg transition-all"
                    >
                        <div class="flex items-start justify-between">
                            <div class="flex items-start space-x-4">
                                <!-- Icon -->
                                <div :class="['w-14 h-14 rounded-xl flex items-center justify-center text-2xl', getTypeColor(resource.type)]">
                                    {{ getTypeIcon(resource.type) }}
                                </div>
                                
                                <!-- Info -->
                                <div class="flex-1">
                                    <h3 class="font-semibold text-gray-900 mb-1">{{ resource.title }}</h3>
                                    <p v-if="resource.description" class="text-sm text-gray-500 mb-2">{{ resource.description }}</p>
                                    <div class="flex items-center gap-4 text-sm text-gray-400">
                                        <span>{{ resource.course?.title }}</span>
                                        <span>•</span>
                                        <span class="uppercase">{{ resource.type }}</span>
                                        <span>•</span>
                                        <span>{{ formatFileSize(resource.file_size) }}</span>
                                        <span>•</span>
                                        <span>{{ formatDate(resource.created_at) }}</span>
                                    </div>
                                </div>
                            </div>

                            <!-- Download Button -->
                            <a
                                :href="resource.download_url"
                                target="_blank"
                                class="flex items-center px-4 py-2 bg-primary-500 hover:bg-primary-600 text-white text-sm font-medium rounded-lg transition-colors"
                            >
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                                </svg>
                                Download
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Empty State -->
                <div v-else class="bg-white rounded-2xl shadow-soft p-12 text-center">
                    <span class="text-6xl mb-4 block">📚</span>
                    <h3 class="text-xl font-semibold text-gray-900 mb-2">No Resources Found</h3>
                    <p class="text-gray-500 mb-6">
                        {{ searchQuery || selectedType || selectedCourse ? 'No resources match your filters.' : 'Resources will appear here as you enroll in courses.' }}
                    </p>
                    <Link
                        href="/courses"
                        class="inline-flex items-center px-6 py-3 bg-primary-500 hover:bg-primary-600 text-white font-semibold rounded-xl transition-colors"
                    >
                        Browse Courses
                    </Link>
                </div>
            </div>
        </div>
    </StudentLayout>
</template>
