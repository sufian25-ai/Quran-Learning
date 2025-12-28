<script setup>
import { Head, Link } from '@inertiajs/vue3';
import { ref, computed } from 'vue';
import PublicLayout from '@/Layouts/PublicLayout.vue';

const props = defineProps({
    course: {
        type: Object,
        required: true
    },
    auth: {
        type: Object,
        default: null
    }
});

const selectedTab = ref('overview');
const selectedType = ref('group');

const tabs = [
    { id: 'overview', name: 'Overview' },
    { id: 'curriculum', name: 'Curriculum' },
    { id: 'teachers', name: 'Teachers' },
    { id: 'reviews', name: 'Reviews' },
];

const currentPrice = computed(() => {
    return selectedType.value === 'group' 
        ? props.course.pricing?.group 
        : props.course.pricing?.private;
});

const formattedPrice = computed(() => {
    return '$' + (Number(currentPrice.value) || 0).toFixed(2);
});

const getLevelBadgeClass = (level) => {
    const classes = {
        'beginner': 'bg-green-100 text-green-700',
        'intermediate': 'bg-blue-100 text-blue-700',
        'advanced': 'bg-purple-100 text-purple-700',
        'all_levels': 'bg-gray-100 text-gray-700',
    };
    return classes[level] || 'bg-gray-100 text-gray-700';
};

const getLevelLabel = (level) => {
    const labels = {
        'beginner': 'Beginner',
        'intermediate': 'Intermediate', 
        'advanced': 'Advanced',
        'all_levels': 'All Levels',
    };
    return labels[level] || level;
};
</script>

<template>
    <Head :title="`${course.title} | QuranLearn`" />
    
    <PublicLayout>
        <!-- Hero Section -->
        <section class="pt-32 pb-12 bg-gradient-to-br from-primary-600 to-primary-700 text-white relative overflow-hidden">
            <!-- Background Pattern -->
            <div class="absolute inset-0 opacity-10">
                <div class="absolute inset-0" style="background-image: url('data:image/svg+xml,%3Csvg width=\'60\' height=\'60\' viewBox=\'0 0 60 60\' xmlns=\'http://www.w3.org/2000/svg\'%3E%3Cg fill=\'none\' fill-rule=\'evenodd\'%3E%3Cg fill=\'%23ffffff\' fill-opacity=\'0.4\'%3E%3Cpath d=\'M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z\'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E');"></div>
            </div>

            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative">
                <!-- Breadcrumb -->
                <nav class="mb-6">
                    <ol class="flex items-center space-x-2 text-sm text-primary-200">
                        <li><Link href="/" class="hover:text-white">Home</Link></li>
                        <li><span>/</span></li>
                        <li><Link href="/courses" class="hover:text-white">Courses</Link></li>
                        <li><span>/</span></li>
                        <li class="text-white">{{ course.title }}</li>
                    </ol>
                </nav>

                <div class="grid lg:grid-cols-3 gap-12">
                    <!-- Course Info -->
                    <div class="lg:col-span-2">
                        <div class="flex items-center gap-3 mb-4">
                            <span :class="['px-3 py-1 text-xs font-medium rounded-full', getLevelBadgeClass(course.level), 'bg-white/20 text-white']">
                                {{ getLevelLabel(course.level) }}
                            </span>
                            <span v-if="course.is_featured" class="px-3 py-1 bg-gold-500/20 text-gold-300 text-xs font-medium rounded-full">
                                ⭐ Featured Course
                            </span>
                        </div>

                        <h1 class="text-3xl sm:text-4xl lg:text-5xl font-display font-bold mb-4">
                            {{ course.title }}
                        </h1>

                        <p class="text-xl text-primary-100 mb-6">
                            {{ course.short_description }}
                        </p>

                        <!-- Meta Info -->
                        <div class="flex flex-wrap items-center gap-6 text-sm">
                            <div class="flex items-center">
                                <span class="text-gold-400 mr-1">★</span>
                                <span class="font-medium">
                                    {{ (Number(course.rating?.average) || 5.0).toFixed(1) }}
                                </span>
                                <span class="text-primary-200 ml-1">
                                    ({{ course.rating?.count || 0 }} reviews)
                                </span>
                            </div>
                            <div class="flex items-center text-primary-200">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.25 2.25 0 11-4.5 0 2.25 2.25 0 014.5 0z"/>
                                </svg>
                                {{ course.total_enrollments || 0 }} students
                            </div>
                            <div class="flex items-center text-primary-200">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                {{ course.duration_weeks }} weeks
                            </div>
                            <div class="flex items-center text-primary-200">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>
                                {{ course.classes_per_week }}x per week
                            </div>
                        </div>
                    </div>

                    <!-- Enrollment Card -->
                    <div class="lg:col-span-1">
                        <div class="bg-white rounded-2xl shadow-soft p-6 sticky top-24">
                            <!-- Preview Image -->
                            <div class="aspect-video bg-gradient-to-br from-primary-100 to-primary-50 rounded-xl flex items-center justify-center mb-6">
                                <span class="text-6xl">📖</span>
                            </div>

                            <!-- Class Type Selector -->
                            <div class="flex bg-gray-100 rounded-xl p-1 mb-6">
                                <button
                                    @click="selectedType = 'group'"
                                    :class="[
                                        'flex-1 py-3 px-4 rounded-lg text-sm font-medium transition-colors',
                                        selectedType === 'group' ? 'bg-white text-gray-900 shadow' : 'text-gray-500 hover:text-gray-700'
                                    ]"
                                >
                                    Group Class
                                </button>
                                <button
                                    @click="selectedType = 'private'"
                                    :class="[
                                        'flex-1 py-3 px-4 rounded-lg text-sm font-medium transition-colors',
                                        selectedType === 'private' ? 'bg-white text-gray-900 shadow' : 'text-gray-500 hover:text-gray-700'
                                    ]"
                                >
                                    Private 1-on-1
                                </button>
                            </div>

                            <!-- Price -->
                            <div class="text-center mb-6">
                                <p class="text-4xl font-bold text-gray-900">{{ formattedPrice }}</p>
                                <p class="text-gray-500">per month</p>
                            </div>

                            <!-- Enroll Button -->
                            <Link
                                :href="`/enroll/${course.slug}?type=${selectedType}`"
                                class="block w-full py-4 bg-primary-500 hover:bg-primary-600 text-white text-center text-lg font-semibold rounded-xl transition-all hover:shadow-glow"
                            >
                                Enroll Now
                            </Link>

                            <p class="text-center text-sm text-gray-500 mt-4">
                                7-day money-back guarantee
                            </p>

                            <!-- Features List -->
                            <div class="border-t border-gray-100 mt-6 pt-6 space-y-4">
                                <div class="flex items-center text-sm text-gray-600">
                                    <svg class="w-5 h-5 text-primary-500 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                    </svg>
                                    {{ course.classes_per_week }} live classes per week
                                </div>
                                <div class="flex items-center text-sm text-gray-600">
                                    <svg class="w-5 h-5 text-primary-500 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                    </svg>
                                    {{ course.class_duration_minutes }} minutes per class
                                </div>
                                <div class="flex items-center text-sm text-gray-600">
                                    <svg class="w-5 h-5 text-primary-500 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                    </svg>
                                    Class recordings access
                                </div>
                                <div class="flex items-center text-sm text-gray-600">
                                    <svg class="w-5 h-5 text-primary-500 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                    </svg>
                                    Certificate upon completion
                                </div>
                                <div class="flex items-center text-sm text-gray-600">
                                    <svg class="w-5 h-5 text-primary-500 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                    </svg>
                                    Progress tracking & badges
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Content Tabs -->
        <section class="py-12 bg-white">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="lg:grid lg:grid-cols-3 lg:gap-12">
                    <div class="lg:col-span-2">
                        <!-- Tab Navigation -->
                        <div class="border-b border-gray-200 mb-8">
                            <nav class="flex space-x-8">
                                <button
                                    v-for="tab in tabs"
                                    :key="tab.id"
                                    @click="selectedTab = tab.id"
                                    :class="[
                                        'py-4 px-1 font-medium text-sm border-b-2 transition-colors',
                                        selectedTab === tab.id
                                            ? 'border-primary-500 text-primary-600'
                                            : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'
                                    ]"
                                >
                                    {{ tab.name }}
                                </button>
                            </nav>
                        </div>

                        <!-- Tab Content: Overview -->
                        <div v-if="selectedTab === 'overview'" class="space-y-8">
                            <div>
                                <h2 class="text-2xl font-bold text-gray-900 mb-4">About This Course</h2>
                                <div class="prose prose-lg max-w-none text-gray-600">
                                    <p>{{ course.description || course.short_description }}</p>
                                </div>
                            </div>

                            <div v-if="course.learning_outcomes?.length">
                                <h3 class="text-xl font-bold text-gray-900 mb-4">What You'll Learn</h3>
                                <div class="grid md:grid-cols-2 gap-4">
                                    <div
                                        v-for="(outcome, index) in course.learning_outcomes"
                                        :key="index"
                                        class="flex items-start"
                                    >
                                        <svg class="w-6 h-6 text-primary-500 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                        </svg>
                                        <span class="text-gray-600">{{ outcome }}</span>
                                    </div>
                                </div>
                            </div>

                            <div v-if="course.requirements?.length">
                                <h3 class="text-xl font-bold text-gray-900 mb-4">Requirements</h3>
                                <ul class="list-disc list-inside space-y-2 text-gray-600">
                                    <li v-for="(req, index) in course.requirements" :key="index">
                                        {{ req }}
                                    </li>
                                </ul>
                            </div>
                        </div>

                        <!-- Tab Content: Curriculum -->
                        <div v-if="selectedTab === 'curriculum'" class="space-y-4">
                            <h2 class="text-2xl font-bold text-gray-900 mb-4">Course Curriculum</h2>
                            <p class="text-gray-600">{{ course.syllabus || 'Detailed curriculum coming soon.' }}</p>
                        </div>

                        <!-- Tab Content: Teachers -->
                        <div v-if="selectedTab === 'teachers'" class="space-y-6">
                            <h2 class="text-2xl font-bold text-gray-900 mb-4">Meet Your Teachers</h2>
                            
                            <div v-if="course.batches?.length" class="space-y-6">
                                <div
                                    v-for="batch in course.batches"
                                    :key="batch.id"
                                    class="bg-gray-50 rounded-xl p-6"
                                >
                                    <div class="flex items-center">
                                        <div class="w-16 h-16 rounded-full bg-primary-100 flex items-center justify-center text-2xl">
                                            👨‍🏫
                                        </div>
                                        <div class="ml-4">
                                            <h4 class="font-semibold text-gray-900">{{ batch.teacher?.name || 'Teacher' }}</h4>
                                            <p class="text-sm text-gray-500">{{ batch.name }}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <p v-else class="text-gray-600">Teacher information coming soon.</p>
                        </div>

                        <!-- Tab Content: Reviews -->
                        <div v-if="selectedTab === 'reviews'" class="space-y-6">
                            <div class="flex items-center justify-between mb-4">
                                <h2 class="text-2xl font-bold text-gray-900">Student Reviews</h2>
                                <div class="flex items-center">
                                    <span class="text-3xl font-bold text-gray-900 mr-2">
                                        {{ (Number(course.rating?.average) || 5.0).toFixed(1) }}
                                    </span>
                                    <div class="flex text-gold-500">
                                        <svg v-for="i in 5" :key="i" class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                        </svg>
                                    </div>
                                </div>
                            </div>

                            <div v-if="course.reviews?.length" class="space-y-6">
                                <div
                                    v-for="review in course.reviews"
                                    :key="review.id"
                                    class="border-b border-gray-100 pb-6 last:border-0"
                                >
                                    <div class="flex items-center mb-3">
                                        <div class="w-10 h-10 rounded-full bg-primary-100 flex items-center justify-center">
                                            {{ review.student?.name?.charAt(0) || 'S' }}
                                        </div>
                                        <div class="ml-3">
                                            <p class="font-medium text-gray-900">{{ review.student?.name || 'Student' }}</p>
                                            <div class="flex text-gold-500 text-sm">
                                                <svg v-for="i in review.rating" :key="i" class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                                </svg>
                                            </div>
                                        </div>
                                    </div>
                                    <p class="text-gray-600">{{ review.comment }}</p>
                                </div>
                            </div>
                            <p v-else class="text-gray-600">No reviews yet. Be the first to review this course!</p>
                        </div>
                    </div>

                    <!-- Right Sidebar - Available Batches -->
                    <div class="lg:col-span-1 mt-12 lg:mt-0">
                        <div class="bg-gray-50 rounded-2xl p-6">
                            <h3 class="font-semibold text-gray-900 mb-4">Upcoming Batches</h3>
                            
                            <div v-if="course.batches?.length" class="space-y-4">
                                <div
                                    v-for="batch in course.batches"
                                    :key="batch.id"
                                    class="bg-white rounded-xl p-4 border border-gray-200"
                                >
                                    <h4 class="font-medium text-gray-900 mb-2">{{ batch.name }}</h4>
                                    <div class="text-sm text-gray-500 space-y-1">
                                        <p>📅 Starts: {{ batch.start_date }}</p>
                                        <p>🕐 {{ batch.formatted_schedule }}</p>
                                        <p>👥 {{ batch.available_slots }} spots left</p>
                                    </div>
                                </div>
                            </div>
                            <p v-else class="text-sm text-gray-500">
                                No batches scheduled yet. Private classes are available.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </PublicLayout>
</template>
