<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import { ref, computed } from 'vue';
import TeacherLayout from '@/Layouts/TeacherLayout.vue';

const props = defineProps({
    auth: Object,
    resources: {
        type: Array,
        default: () => []
    },
    courses: {
        type: Array,
        default: () => []
    }
});

const showUploadModal = ref(false);
const searchQuery = ref('');

const filteredResources = computed(() => {
    if (!searchQuery.value) return props.resources;
    const query = searchQuery.value.toLowerCase();
    return props.resources.filter(r => 
        r.title.toLowerCase().includes(query) ||
        r.course?.title?.toLowerCase().includes(query)
    );
});

const form = useForm({
    title: '',
    description: '',
    course_id: '',
    type: 'pdf',
    file: null,
    is_public: false,
});

const uploadResource = () => {
    form.post('/teacher/resources', {
        onSuccess: () => {
            form.reset();
            showUploadModal.value = false;
        },
        forceFormData: true,
    });
};

const deleteResource = (id) => {
    if (confirm('Are you sure you want to delete this resource?')) {
        useForm({}).delete(`/teacher/resources/${id}`);
    }
};

const resourceTypes = [
    { value: 'pdf', label: 'PDF', icon: '📄' },
    { value: 'video', label: 'Video', icon: '🎥' },
    { value: 'audio', label: 'Audio', icon: '🎵' },
    { value: 'document', label: 'Doc', icon: '📝' },
    { value: 'image', label: 'Image', icon: '🖼️' },
];

const getTypeIcon = (type) => {
    const icons = { pdf: '📄', video: '🎥', audio: '🎵', document: '📝', image: '🖼️' };
    return icons[type] || '📁';
};

const getTypeColor = (type) => {
    const colors = {
        pdf: 'bg-red-100 text-red-600',
        video: 'bg-purple-100 text-purple-600',
        audio: 'bg-blue-100 text-blue-600',
        document: 'bg-green-100 text-green-600',
        image: 'bg-yellow-100 text-yellow-600',
    };
    return colors[type] || 'bg-gray-100 text-gray-600';
};

const formatFileSize = (bytes) => {
    if (!bytes) return 'N/A';
    const sizes = ['Bytes', 'KB', 'MB', 'GB'];
    const i = Math.floor(Math.log(bytes) / Math.log(1024));
    return `${(bytes / Math.pow(1024, i)).toFixed(1)} ${sizes[i]}`;
};

const formatDate = (dateString) => {
    return new Date(dateString).toLocaleDateString('en-US', {
        month: 'short',
        day: 'numeric',
        year: 'numeric'
    });
};

const handleFileChange = (e) => {
    form.file = e.target.files[0];
};
</script>

<template>
    <Head title="Resources | Teacher" />

    <TeacherLayout>
        <template #header>
            <div class="flex items-center justify-between w-full">
                <div>
                    <h2 class="font-display text-xl font-bold text-gray-900">
                        My Resources 📁
                    </h2>
                    <p class="text-gray-500 text-sm">Upload and manage teaching materials</p>
                </div>
                <button
                    @click="showUploadModal = true"
                    class="px-4 py-2 bg-emerald-500 hover:bg-emerald-600 text-white font-medium rounded-xl transition-colors"
                >
                    + Upload Resource
                </button>
            </div>
        </template>

        <div class="space-y-6">
            <!-- Search -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-4">
                <div class="relative">
                    <input
                        v-model="searchQuery"
                        type="text"
                        placeholder="Search resources..."
                        class="w-full pl-10 pr-4 py-3 rounded-xl border-gray-200 focus:border-emerald-500 focus:ring-emerald-500"
                    />
                    <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                </div>
            </div>

            <!-- Resources List -->
            <div v-if="filteredResources.length" class="space-y-4">
                <div
                    v-for="resource in filteredResources"
                    :key="resource.id"
                    class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 hover:shadow-md hover:border-emerald-200 transition-all group"
                >
                    <div class="flex items-start justify-between">
                        <div class="flex items-start space-x-4">
                            <div :class="['w-14 h-14 rounded-xl flex items-center justify-center text-2xl group-hover:scale-110 transition-transform', getTypeColor(resource.type)]">
                                {{ getTypeIcon(resource.type) }}
                            </div>
                            <div>
                                <h3 class="font-semibold text-gray-900 mb-1 group-hover:text-emerald-600 transition-colors">{{ resource.title }}</h3>
                                <p v-if="resource.description" class="text-sm text-gray-500 mb-2">{{ resource.description }}</p>
                                <div class="flex items-center gap-3 text-sm text-gray-400 flex-wrap">
                                    <span class="bg-gray-100 px-2 py-0.5 rounded">{{ resource.course?.title }}</span>
                                    <span class="uppercase font-medium">{{ resource.type }}</span>
                                    <span>{{ formatFileSize(resource.file_size) }}</span>
                                    <span>{{ formatDate(resource.created_at) }}</span>
                                    <span v-if="resource.is_public" class="px-2 py-0.5 bg-green-100 text-green-700 rounded text-xs font-medium">Public</span>
                                </div>
                            </div>
                        </div>
                        <div class="flex items-center gap-2">
                            <a
                                :href="resource.file_url"
                                target="_blank"
                                class="p-2 text-gray-400 hover:text-emerald-500 transition-colors"
                                title="Download"
                            >
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                                </svg>
                            </a>
                            <button
                                @click="deleteResource(resource.id)"
                                class="p-2 text-gray-400 hover:text-red-500 transition-colors"
                                title="Delete"
                            >
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Empty State -->
            <div v-else class="bg-white rounded-2xl shadow-sm border border-gray-100 p-12 text-center">
                <div class="w-20 h-20 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <span class="text-4xl">📁</span>
                </div>
                <h3 class="text-lg font-semibold text-gray-900 mb-2">No Resources Yet</h3>
                <p class="text-gray-500 mb-6">Upload resources for your students to download</p>
                <button
                    @click="showUploadModal = true"
                    class="px-6 py-3 bg-emerald-500 hover:bg-emerald-600 text-white font-medium rounded-xl transition-colors"
                >
                    Upload First Resource
                </button>
            </div>
        </div>

        <!-- Upload Modal -->
        <div v-if="showUploadModal" class="fixed inset-0 z-50 flex items-center justify-center p-4">
            <div class="absolute inset-0 bg-black/50" @click="showUploadModal = false"></div>
            <div class="relative bg-white rounded-2xl shadow-xl w-full max-w-lg p-6">
                <h2 class="text-xl font-semibold text-gray-900 mb-6">📤 Upload Resource</h2>
                
                <form @submit.prevent="uploadResource" class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Title</label>
                        <input
                            v-model="form.title"
                            type="text"
                            required
                            class="w-full rounded-xl border-gray-200 focus:border-emerald-500 focus:ring-emerald-500"
                            placeholder="Resource title"
                        />
                        <p v-if="form.errors.title" class="mt-1 text-sm text-red-500">{{ form.errors.title }}</p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Course</label>
                        <select
                            v-model="form.course_id"
                            required
                            class="w-full rounded-xl border-gray-200 focus:border-emerald-500 focus:ring-emerald-500"
                        >
                            <option value="">Select Course</option>
                            <option v-for="course in courses" :key="course.id" :value="course.id">
                                {{ course.title }}
                            </option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Type</label>
                        <div class="grid grid-cols-5 gap-2">
                            <button
                                v-for="type in resourceTypes"
                                :key="type.value"
                                type="button"
                                @click="form.type = type.value"
                                :class="[
                                    'p-3 rounded-xl border-2 text-center text-sm transition-all',
                                    form.type === type.value
                                        ? 'border-emerald-500 bg-emerald-50 text-emerald-700'
                                        : 'border-gray-200 hover:border-gray-300'
                                ]"
                            >
                                <span class="text-xl block mb-1">{{ type.icon }}</span>
                                <span class="text-xs">{{ type.label }}</span>
                            </button>
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">File</label>
                        <input
                            type="file"
                            @change="handleFileChange"
                            required
                            class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-medium file:bg-emerald-50 file:text-emerald-700 hover:file:bg-emerald-100"
                        />
                        <p v-if="form.errors.file" class="mt-1 text-sm text-red-500">{{ form.errors.file }}</p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Description (Optional)</label>
                        <textarea
                            v-model="form.description"
                            rows="2"
                            class="w-full rounded-xl border-gray-200 focus:border-emerald-500 focus:ring-emerald-500"
                            placeholder="Brief description..."
                        ></textarea>
                    </div>

                    <label class="flex items-center cursor-pointer">
                        <input v-model="form.is_public" type="checkbox" class="rounded text-emerald-500 focus:ring-emerald-500" />
                        <span class="ml-2 text-sm text-gray-600">Make this resource public</span>
                    </label>

                    <div class="flex gap-3 pt-4">
                        <button
                            type="button"
                            @click="showUploadModal = false"
                            class="flex-1 py-3 border border-gray-200 text-gray-700 font-medium rounded-xl hover:bg-gray-50"
                        >
                            Cancel
                        </button>
                        <button
                            type="submit"
                            :disabled="form.processing"
                            class="flex-1 py-3 bg-emerald-500 hover:bg-emerald-600 text-white font-medium rounded-xl disabled:opacity-50"
                        >
                            {{ form.processing ? 'Uploading...' : 'Upload' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </TeacherLayout>
</template>
