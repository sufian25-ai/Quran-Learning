<script setup>
import { Head, Link } from '@inertiajs/vue3';
import { ref, computed } from 'vue';
import PublicLayout from '@/Layouts/PublicLayout.vue';
import MetaTags from '@/Components/SEO/MetaTags.vue';
import { seoConfig } from '@/utils/seo.js';
import gsap from 'gsap';

const props = defineProps({
    courses: {
        type: Object,
        default: () => ({ data: [], meta: {} })
    },
    categories: {
        type: Array,
        default: () => []
    },
    filters: {
        type: Object,
        default: () => ({})
    }
});

const search = ref(props.filters.search || '');
const category = ref(props.filters.category || '');
const level = ref(props.filters.level || '');
const sort = ref(props.filters.sort || 'popularity');

const levels = [
    { id: '', name: 'All Levels' },
    { id: 'beginner', name: 'Beginner' },
    { id: 'intermediate', name: 'Intermediate' },
    { id: 'advanced', name: 'Advanced' },
];

const sortOptions = [
    { id: 'popularity', name: 'Most Popular' },
    { id: 'newest', name: 'Newest' },
    { id: 'rating', name: 'Highest Rated' },
    { id: 'price_low', name: 'Price: Low to High' },
    { id: 'price_high', name: 'Price: High to Low' },
];

const categoryCards = [
    { id: 'quran_reading', name: 'Quran Reading', icon: '📖', color: 'from-emerald-500 to-teal-600', count: 12 },
    { id: 'tajweed', name: 'Tajweed', icon: '🎙️', color: 'from-blue-500 to-indigo-600', count: 8 },
    { id: 'hifz', name: 'Hifz', icon: '🧠', color: 'from-purple-500 to-pink-600', count: 6 },
    { id: 'arabic', name: 'Arabic', icon: '🔤', color: 'from-orange-500 to-red-600', count: 10 },
    { id: 'islamic_studies', name: 'Islamic Studies', icon: '🕌', color: 'from-amber-500 to-yellow-600', count: 5 },
];

const applyFilters = () => {
    const params = {};
    if (search.value) params.search = search.value;
    if (category.value) params.category = category.value;
    if (level.value) params.level = level.value;
    if (sort.value !== 'popularity') params.sort = sort.value;
    
    router.get('/courses', params, {
        preserveState: true,
        preserveScroll: true,
    });
};

const clearFilters = () => {
    search.value = '';
    category.value = '';
    level.value = '';
    sort.value = 'popularity';
    router.get('/courses');
};

const selectCategory = (cat) => {
    category.value = category.value === cat ? '' : cat;
    applyFilters();
};

// Debounced search
let searchTimeout;
watch(search, () => {
    clearTimeout(searchTimeout);
    searchTimeout = setTimeout(applyFilters, 500);
});

watch([level, sort], applyFilters);

const hasActiveFilters = computed(() => {
    return search.value || category.value || level.value || sort.value !== 'popularity';
});

const getLevelBadgeClass = (lvl) => {
    const classes = {
        'beginner': 'bg-gradient-to-r from-green-400 to-emerald-500 text-white',
        'intermediate': 'bg-gradient-to-r from-blue-400 to-indigo-500 text-white',
        'advanced': 'bg-gradient-to-r from-purple-400 to-pink-500 text-white',
        'all_levels': 'bg-gradient-to-r from-gray-400 to-gray-500 text-white',
    };
    return classes[lvl] || 'bg-gray-100 text-gray-700';
};

const getLevelLabel = (lvl) => {
    const labels = {
        'beginner': 'Beginner',
        'intermediate': 'Intermediate',
        'advanced': 'Advanced',
        'all_levels': 'All Levels',
    };
    return labels[lvl] || lvl;
};

const getCategoryIcon = (cat) => {
    const icons = {
        'quran_reading': '📖',
        'tajweed': '🎙️',
        'hifz': '🧠',
        'arabic': '🔤',
        'islamic_studies': '🕌',
    };
    return icons[cat] || '📚';
};

// GSAP Animations
onMounted(() => {
    // Animate hero section
    gsap.from('.hero-title', {
        y: 50,
        opacity: 0,
        duration: 1,
        ease: 'power3.out'
    });

    gsap.from('.hero-subtitle', {
        y: 30,
        opacity: 0,
        duration: 1,
        delay: 0.2,
        ease: 'power3.out'
    });

    // Animate category cards
    gsap.from('.category-card', {
        y: 40,
        opacity: 0,
        duration: 0.6,
        stagger: 0.1,
        delay: 0.3,
        ease: 'power2.out'
    });

    // Animate course cards
    gsap.from('.course-card', {
        y: 50,
        opacity: 0,
        duration: 0.5,
        stagger: 0.08,
        delay: 0.5,
        ease: 'power2.out'
    });

    // Floating particles animation
    gsap.to('.particle', {
        y: -100,
        opacity: 0,
        duration: 3,
        stagger: {
            each: 0.2,
            repeat: -1
        },
        ease: 'power1.out'
    });
});
</script>

<template>
    <PublicLayout>
        <!-- SEO Meta Tags -->
        <MetaTags 
            :title="seoConfig.courses.title"
            :description="seoConfig.courses.description"
            :keywords="seoConfig.courses.keywords"
        />
        
        <Head title="Courses | QuranLearn - Learn Quran Online" />
    
        <!-- Hero Section with Particles -->
        <section class="relative pt-28 pb-16 overflow-hidden bg-gradient-to-br from-emerald-900 via-teal-800 to-emerald-900">
            <!-- Islamic Pattern Overlay -->
            <div class="absolute inset-0 opacity-10">
                <div class="absolute inset-0" style="background-image: url('data:image/svg+xml,%3Csvg width=\'60\' height=\'60\' viewBox=\'0 0 60 60\' xmlns=\'http://www.w3.org/2000/svg\'%3E%3Cg fill=\'none\' fill-rule=\'evenodd\'%3E%3Cg fill=\'%23ffffff\' fill-opacity=\'0.4\'%3E%3Cpath d=\'M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z\'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E');"></div>
            </div>

            <!-- Floating Particles -->
            <div class="absolute inset-0 pointer-events-none">
                <div v-for="n in 20" :key="n" class="particle absolute w-2 h-2 bg-gold-400 rounded-full opacity-40" 
                    :style="{ left: Math.random() * 100 + '%', top: Math.random() * 100 + '%' }"></div>
            </div>

            <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center max-w-4xl mx-auto">
                    <h1 class="hero-title text-4xl sm:text-5xl lg:text-6xl font-display font-bold text-white mb-6">
                        Discover Your Path to
                        <span class="text-transparent bg-clip-text bg-gradient-to-r from-gold-400 to-amber-300">Quranic Excellence</span>
                    </h1>
                    <p class="hero-subtitle text-xl text-emerald-100 mb-10 max-w-2xl mx-auto">
                        Join thousands of students learning from expert scholars. Choose from our carefully crafted courses tailored for every level.
                    </p>

                    <!-- Search Bar with Glassmorphism -->
                    <div class="max-w-2xl mx-auto">
                        <div class="relative bg-white/10 backdrop-blur-xl rounded-2xl p-2 border border-white/20 shadow-2xl">
                            <div class="flex items-center gap-2">
                                <div class="flex-1 relative">
                                    <svg class="absolute left-4 top-1/2 -translate-y-1/2 w-5 h-5 text-white/60" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                                    </svg>
                                    <input
                                        v-model="search"
                                        type="text"
                                        placeholder="Search courses, topics, or teachers..."
                                        class="w-full pl-12 pr-4 py-4 bg-transparent text-white placeholder-white/50 border-0 focus:ring-0 text-lg"
                                    />
                                </div>
                                <button class="px-8 py-4 bg-gradient-to-r from-gold-500 to-amber-500 hover:from-gold-600 hover:to-amber-600 text-gray-900 font-semibold rounded-xl transition-all hover:shadow-lg hover:scale-105">
                                    Search
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Wave Decoration -->
            <div class="absolute bottom-0 left-0 right-0">
                <svg viewBox="0 0 1440 120" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M0 120L60 110C120 100 240 80 360 70C480 60 600 60 720 65C840 70 960 80 1080 85C1200 90 1320 90 1380 90L1440 90V120H1380C1320 120 1200 120 1080 120C960 120 840 120 720 120C600 120 480 120 360 120C240 120 120 120 60 120H0Z" fill="white"/>
                </svg>
            </div>
        </section>

        <!-- Category Cards -->
        <section class="py-12 bg-white -mt-1">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <h2 class="text-2xl font-bold text-gray-900 mb-8 text-center">Browse by Category</h2>
                <div class="grid grid-cols-2 md:grid-cols-5 gap-4">
                    <button
                        v-for="cat in categoryCards"
                        :key="cat.id"
                        @click="selectCategory(cat.id)"
                        :class="[
                            'category-card group relative p-6 rounded-2xl transition-all duration-300 overflow-hidden',
                            category === cat.id 
                                ? 'ring-4 ring-emerald-500 ring-offset-2 scale-105' 
                                : 'hover:scale-105 hover:shadow-xl'
                        ]"
                    >
                        <div :class="['absolute inset-0 bg-gradient-to-br opacity-90', cat.color]"></div>
                        <div class="relative z-10 text-center text-white">
                            <span class="text-4xl mb-3 block transform group-hover:scale-110 transition-transform">{{ cat.icon }}</span>
                            <h3 class="font-semibold text-sm">{{ cat.name }}</h3>
                            <p class="text-xs opacity-80 mt-1">{{ cat.count }} courses</p>
                        </div>
                        <!-- Shine effect on hover -->
                        <div class="absolute inset-0 bg-gradient-to-r from-transparent via-white/20 to-transparent -translate-x-full group-hover:translate-x-full transition-transform duration-700"></div>
                    </button>
                </div>
            </div>
        </section>

        <!-- Filters & Course Grid -->
        <section class="py-12 bg-gray-50">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <!-- Filter Bar -->
                <div class="bg-white rounded-2xl shadow-lg p-6 mb-8 border border-gray-100">
                    <div class="flex flex-wrap items-center gap-4">
                        <div class="flex items-center gap-2">
                            <span class="text-gray-500 text-sm font-medium">Filter by:</span>
                        </div>
                        
                        <!-- Level Filter -->
                        <select
                            v-model="level"
                            class="px-4 py-2 rounded-xl bg-gray-50 border-gray-200 text-sm focus:border-emerald-500 focus:ring-emerald-500"
                        >
                            <option v-for="l in levels" :key="l.id" :value="l.id">{{ l.name }}</option>
                        </select>

                        <!-- Sort -->
                        <select
                            v-model="sort"
                            class="px-4 py-2 rounded-xl bg-gray-50 border-gray-200 text-sm focus:border-emerald-500 focus:ring-emerald-500"
                        >
                            <option v-for="s in sortOptions" :key="s.id" :value="s.id">{{ s.name }}</option>
                        </select>

                        <div class="ml-auto flex items-center gap-4">
                            <button
                                v-if="hasActiveFilters"
                                @click="clearFilters"
                                class="text-sm text-emerald-600 hover:text-emerald-700 font-medium"
                            >
                                Clear all filters
                            </button>
                            <span class="text-gray-400">|</span>
                            <span class="text-gray-600 text-sm">
                                <strong>{{ courses.meta?.total || courses.data.length }}</strong> courses found
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Course Grid -->
                <div v-if="courses.data.length > 0" class="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
                    <Link
                        v-for="(course, index) in courses.data"
                        :key="course.id"
                        :href="`/courses/${course.slug}`"
                        class="course-card group"
                    >
                        <div class="bg-white rounded-3xl overflow-hidden shadow-lg hover:shadow-2xl transition-all duration-500 hover:-translate-y-2 border border-gray-100">
                            <!-- Thumbnail with Overlay -->
                            <div class="relative h-48 overflow-hidden">
                                <div :class="['absolute inset-0 bg-gradient-to-br', categoryCards.find(c => c.id === course.category)?.color || 'from-emerald-500 to-teal-600']"></div>
                                <div class="absolute inset-0 flex items-center justify-center">
                                    <span class="text-7xl transform group-hover:scale-125 transition-transform duration-500">{{ getCategoryIcon(course.category) }}</span>
                                </div>
                                
                                <!-- Badges -->
                                <div class="absolute top-4 left-4 flex gap-2">
                                    <span :class="['px-3 py-1 text-xs font-bold rounded-full shadow-lg', getLevelBadgeClass(course.level)]">
                                        {{ getLevelLabel(course.level) }}
                                    </span>
                                </div>
                                <div v-if="course.is_featured" class="absolute top-4 right-4">
                                    <span class="px-3 py-1 bg-gradient-to-r from-amber-400 to-yellow-500 text-gray-900 text-xs font-bold rounded-full shadow-lg animate-pulse">
                                        ⭐ Featured
                                    </span>
                                </div>

                                <!-- Hover Overlay -->
                                <div class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 transition-opacity duration-300 flex items-center justify-center">
                                    <span class="px-6 py-3 bg-white text-emerald-600 font-semibold rounded-xl transform scale-90 group-hover:scale-100 transition-transform">
                                        View Course →
                                    </span>
                                </div>
                            </div>

                            <!-- Content -->
                            <div class="p-6">
                                <h3 class="text-xl font-bold text-gray-900 mb-2 group-hover:text-emerald-600 transition-colors line-clamp-1">
                                    {{ course.title }}
                                </h3>
                                <p class="text-gray-500 text-sm mb-4 line-clamp-2">
                                    {{ course.short_description }}
                                </p>

                                <!-- Stats -->
                                <div class="flex items-center gap-4 text-sm text-gray-400 mb-4">
                                    <span class="flex items-center gap-1">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                        </svg>
                                        {{ course.duration_weeks }} weeks
                                    </span>
                                    <span>•</span>
                                    <span>{{ course.classes_per_week }}x/week</span>
                                </div>

                                <!-- Footer -->
                                <div class="flex items-center justify-between pt-4 border-t border-gray-100">
                                    <!-- Rating -->
                                    <div class="flex items-center gap-1">
                                        <div class="flex text-amber-400">
                                            <svg v-for="i in 5" :key="i" class="w-4 h-4" :class="i <= Math.round(course.rating?.average || 5) ? 'fill-current' : 'fill-gray-200'" viewBox="0 0 20 20">
                                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                            </svg>
                                        </div>
                                        <span class="text-gray-500 text-sm">({{ course.rating?.count || 0 }})</span>
                                    </div>

                                    <!-- Price -->
                                    <div class="text-right">
                                        <p class="text-2xl font-bold text-emerald-600">{{ course.pricing?.formatted_group || '$49' }}</p>
                                        <p class="text-xs text-gray-400">per month</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </Link>
                </div>

                <!-- Empty State -->
                <div v-else class="text-center py-20 bg-white rounded-3xl shadow-lg">
                    <div class="text-8xl mb-6 animate-bounce">📚</div>
                    <h3 class="text-2xl font-bold text-gray-900 mb-3">No courses found</h3>
                    <p class="text-gray-500 mb-8 max-w-md mx-auto">
                        We couldn't find any courses matching your criteria. Try adjusting your filters or search terms.
                    </p>
                    <button
                        @click="clearFilters"
                        class="inline-flex items-center px-8 py-4 bg-gradient-to-r from-emerald-500 to-teal-600 hover:from-emerald-600 hover:to-teal-700 text-white font-semibold rounded-2xl transition-all hover:shadow-xl hover:scale-105"
                    >
                        Clear All Filters
                    </button>
                </div>

                <!-- Pagination -->
                <div v-if="courses.meta && courses.meta.last_page > 1" class="flex justify-center mt-12">
                    <nav class="flex items-center gap-2 bg-white rounded-2xl shadow-lg p-2">
                        <Link
                            v-if="courses.meta.current_page > 1"
                            :href="`?page=${courses.meta.current_page - 1}`"
                            class="px-5 py-3 rounded-xl text-gray-600 hover:bg-emerald-50 hover:text-emerald-600 font-medium transition-colors"
                        >
                            ← Previous
                        </Link>
                        <span class="px-5 py-3 bg-emerald-500 text-white rounded-xl font-bold">
                            {{ courses.meta.current_page }}
                        </span>
                        <span class="text-gray-400">of {{ courses.meta.last_page }}</span>
                        <Link
                            v-if="courses.meta.current_page < courses.meta.last_page"
                            :href="`?page=${courses.meta.current_page + 1}`"
                            class="px-5 py-3 rounded-xl text-gray-600 hover:bg-emerald-50 hover:text-emerald-600 font-medium transition-colors"
                        >
                            Next →
                        </Link>
                    </nav>
                </div>
            </div>
        </section>

        <!-- CTA Section -->
        <section class="py-20 bg-gradient-to-b from-emerald-900 via-emerald-800 to-teal-900 relative overflow-hidden">
            <div class="absolute inset-0 opacity-10">
                <div class="absolute inset-0" style="background-image: url('data:image/svg+xml,%3Csvg width=\'60\' height=\'60\' viewBox=\'0 0 60 60\' xmlns=\'http://www.w3.org/2000/svg\'%3E%3Cg fill=\'none\'%3E%3Cg fill=\'%23ffffff\' fill-opacity=\'0.4\'%3E%3Cpath d=\'M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z\'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E');"></div>
            </div>
            <div class="relative max-w-4xl mx-auto px-4 text-center">
                <h2 class="text-3xl sm:text-4xl font-bold text-white mb-6">
                    Can't decide which course to take?
                </h2>
                <p class="text-xl text-emerald-100 mb-10">
                    Book a free consultation with our advisors and get personalized recommendations.
                </p>
                <div class="flex flex-col sm:flex-row gap-4 justify-center">
                    <Link href="/contact" class="inline-flex items-center justify-center px-8 py-4 bg-white hover:bg-gray-100 text-emerald-700 text-lg font-semibold rounded-2xl transition-all hover:shadow-xl hover:scale-105">
                        📞 Book Free Consultation
                    </Link>
                    <Link href="/read-quran" class="inline-flex items-center justify-center px-8 py-4 bg-emerald-500/30 hover:bg-emerald-500/40 text-white text-lg font-semibold rounded-2xl border-2 border-white/30 transition-all hover:scale-105">
                        📖 Try Reading Quran Free
                    </Link>
                </div>
            </div>
        </section>
    </PublicLayout>
</template>

<style scoped>
.line-clamp-1 {
    display: -webkit-box;
    -webkit-line-clamp: 1;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

.line-clamp-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

/* Custom animation for featured badge */
@keyframes shimmer {
    0% { background-position: -200% 0; }
    100% { background-position: 200% 0; }
}

.animate-shimmer {
    background: linear-gradient(90deg, transparent, rgba(255,255,255,0.4), transparent);
    background-size: 200% 100%;
    animation: shimmer 2s infinite;
}
</style>
