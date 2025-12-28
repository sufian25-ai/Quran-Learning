<script setup>
import { Head, Link } from '@inertiajs/vue3';
import { ref, computed, onMounted } from 'vue';
import PublicLayout from '@/Layouts/PublicLayout.vue';
import gsap from 'gsap';

const props = defineProps({
    teachers: {
        type: Array,
        default: () => []
    }
});

const selectedSpecialization = ref('');

const specializations = [
    { id: '', name: 'All', icon: '✨' },
    { id: 'Quran Reading', name: 'Quran Reading', icon: '📖' },
    { id: 'Tajweed', name: 'Tajweed', icon: '🎙️' },
    { id: 'Hifz', name: 'Hifz', icon: '🧠' },
    { id: 'Arabic', name: 'Arabic', icon: '🔤' },
    { id: 'Islamic Studies', name: 'Islamic Studies', icon: '🕌' },
];

// Filter teachers by specialization
const displayTeachers = computed(() => {
    if (!selectedSpecialization.value) {
        return props.teachers;
    }
    return props.teachers.filter(teacher => 
        teacher.specializations?.some(spec => 
            spec.toLowerCase().includes(selectedSpecialization.value.toLowerCase())
        )
    );
});

// Stats computed from actual data
const stats = computed(() => [
    { value: `${props.teachers.length}+`, label: 'Qualified Teachers', icon: '👨‍🏫' },
    { value: '15+', label: 'Countries', icon: '🌍' },
    { value: '20+', label: 'Languages', icon: '🗣️' },
    { value: averageRating.value, label: 'Average Rating', icon: '⭐' },
]);

const averageRating = computed(() => {
    if (props.teachers.length === 0) return '5.0';
    const avg = props.teachers.reduce((sum, t) => sum + (t.rating || 5), 0) / props.teachers.length;
    return avg.toFixed(1);
});

onMounted(() => {
    // Hero animations
    gsap.from('.hero-title', {
        y: 60,
        opacity: 0,
        duration: 1,
        ease: 'power3.out'
    });

    gsap.from('.hero-subtitle', {
        y: 40,
        opacity: 0,
        duration: 1,
        delay: 0.2,
        ease: 'power3.out'
    });

    // Filter buttons animation
    gsap.from('.filter-btn', {
        y: 20,
        opacity: 0,
        duration: 0.4,
        stagger: 0.05,
        delay: 0.4,
        ease: 'power2.out'
    });

    // Stats animation
    gsap.from('.stat-card', {
        scale: 0.9,
        opacity: 0,
        duration: 0.5,
        stagger: 0.1,
        delay: 0.5,
        ease: 'back.out(1.7)'
    });

    // Teacher cards animation
    gsap.from('.teacher-card', {
        y: 50,
        opacity: 0,
        duration: 0.6,
        stagger: 0.1,
        delay: 0.6,
        ease: 'power2.out'
    });

    // Floating particles
    gsap.to('.particle', {
        y: -80,
        opacity: 0,
        duration: 4,
        stagger: {
            each: 0.3,
            repeat: -1
        },
        ease: 'power1.out'
    });
});
</script>

<template>
    <Head title="Our Teachers | QuranLearn" />
    
    <PublicLayout>
        <!-- Hero Section with Particles -->
        <section class="relative pt-28 pb-24 overflow-hidden bg-gradient-to-br from-purple-900 via-indigo-800 to-purple-900">
            <!-- Islamic Pattern Overlay -->
            <div class="absolute inset-0 opacity-10">
                <div class="absolute inset-0" style="background-image: url('data:image/svg+xml,%3Csvg width=\'60\' height=\'60\' viewBox=\'0 0 60 60\' xmlns=\'http://www.w3.org/2000/svg\'%3E%3Cg fill=\'none\' fill-rule=\'evenodd\'%3E%3Cg fill=\'%23ffffff\' fill-opacity=\'0.4\'%3E%3Cpath d=\'M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z\'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E');"></div>
            </div>

            <!-- Floating Particles -->
            <div class="absolute inset-0 pointer-events-none">
                <div v-for="n in 15" :key="n" class="particle absolute w-2 h-2 bg-amber-400 rounded-full opacity-40" 
                    :style="{ left: Math.random() * 100 + '%', top: Math.random() * 100 + '%' }"></div>
            </div>

            <!-- Glowing Orbs -->
            <div class="absolute top-20 left-10 w-72 h-72 bg-purple-500/30 rounded-full blur-3xl"></div>
            <div class="absolute bottom-20 right-10 w-96 h-96 bg-indigo-500/20 rounded-full blur-3xl"></div>

            <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center max-w-4xl mx-auto">
                    <h1 class="hero-title text-4xl sm:text-5xl lg:text-6xl font-display font-bold text-white mb-6">
                        Meet Our
                        <span class="text-transparent bg-clip-text bg-gradient-to-r from-amber-400 to-yellow-300">Qualified Teachers</span>
                    </h1>
                    <p class="hero-subtitle text-xl text-purple-100 max-w-2xl mx-auto mb-10">
                        Learn from certified scholars with years of experience teaching Quran, Tajweed, and Arabic to students worldwide.
                    </p>

                    <!-- Filter Buttons with Glassmorphism -->
                    <div class="inline-flex flex-wrap items-center justify-center gap-2 bg-white/10 backdrop-blur-xl rounded-2xl p-3 border border-white/20">
                        <button
                            v-for="spec in specializations"
                            :key="spec.id"
                            @click="selectedSpecialization = spec.id"
                            :class="[
                                'filter-btn px-5 py-2.5 rounded-xl text-sm font-semibold transition-all duration-300 flex items-center gap-2',
                                selectedSpecialization === spec.id
                                    ? 'bg-gradient-to-r from-amber-500 to-yellow-500 text-gray-900 shadow-lg scale-105'
                                    : 'text-white hover:bg-white/10'
                            ]"
                        >
                            <span>{{ spec.icon }}</span>
                            {{ spec.name }}
                        </button>
                    </div>
                </div>

                <!-- Stats Row -->
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mt-12 max-w-3xl mx-auto">
                    <div 
                        v-for="stat in stats"
                        :key="stat.label"
                        class="stat-card bg-white/10 backdrop-blur-sm rounded-xl p-4 text-center border border-white/10"
                    >
                        <span class="text-2xl">{{ stat.icon }}</span>
                        <p class="text-2xl font-bold text-white mt-1">{{ stat.value }}</p>
                        <p class="text-purple-200 text-sm">{{ stat.label }}</p>
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

        <!-- Teachers Grid -->
        <section class="py-16 bg-white -mt-1">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <!-- No Teachers State -->
                <div v-if="displayTeachers.length === 0" class="text-center py-20">
                    <div class="text-6xl mb-4">👨‍🏫</div>
                    <h3 class="text-2xl font-bold text-gray-900 mb-2">No Teachers Found</h3>
                    <p class="text-gray-600 mb-6">
                        {{ selectedSpecialization ? 'No teachers found for this specialization. Try selecting another category.' : 'Teachers will appear here once they are verified.' }}
                    </p>
                    <button 
                        v-if="selectedSpecialization"
                        @click="selectedSpecialization = ''"
                        class="px-6 py-3 bg-purple-500 text-white font-semibold rounded-xl hover:bg-purple-600 transition-colors"
                    >
                        View All Teachers
                    </button>
                </div>

                <!-- Teachers Grid -->
                <div v-else class="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
                    <div
                        v-for="teacher in displayTeachers"
                        :key="teacher.id"
                        class="teacher-card group"
                    >
                        <div class="relative bg-white rounded-3xl overflow-hidden shadow-lg hover:shadow-2xl transition-all duration-500 hover:-translate-y-2 border border-gray-100">
                            <!-- Header with Gradient -->
                            <div :class="['h-36 bg-gradient-to-br relative', teacher.gradient || 'from-emerald-500 to-teal-600']">
                                <!-- Pattern overlay -->
                                <div class="absolute inset-0 opacity-20" style="background-image: url('data:image/svg+xml,%3Csvg width=\'20\' height=\'20\' viewBox=\'0 0 20 20\' xmlns=\'http://www.w3.org/2000/svg\'%3E%3Cg fill=\'%23ffffff\' fill-opacity=\'0.5\'%3E%3Cpath d=\'M0 0h20L10 10zm10 10L0 20h20z\'/%3E%3C/g%3E%3C/svg%3E');"></div>
                                
                                <!-- Availability Badge -->
                                <div class="absolute top-4 right-4">
                                    <span v-if="teacher.is_available" class="inline-flex items-center px-3 py-1 bg-white/90 backdrop-blur-sm text-green-600 text-xs font-bold rounded-full shadow">
                                        <span class="w-2 h-2 bg-green-500 rounded-full mr-1.5 animate-pulse"></span>
                                        Available
                                    </span>
                                    <span v-else class="inline-flex items-center px-3 py-1 bg-white/90 backdrop-blur-sm text-gray-500 text-xs font-bold rounded-full shadow">
                                        Unavailable
                                    </span>
                                </div>

                                <!-- Rating Badge -->
                                <div class="absolute top-4 left-4">
                                    <span class="inline-flex items-center px-3 py-1 bg-amber-400 text-gray-900 text-xs font-bold rounded-full shadow">
                                        ⭐ {{ Number(teacher.rating).toFixed(1) }}
                                    </span>
                                </div>
                            </div>

                            <!-- Avatar -->
                            <div class="relative -mt-14 flex justify-center">
                                <div class="w-28 h-28 rounded-2xl bg-white p-1.5 shadow-xl transform group-hover:scale-110 group-hover:rotate-3 transition-all duration-300">
                                    <img 
                                        v-if="teacher.avatar" 
                                        :src="teacher.avatar" 
                                        :alt="teacher.name"
                                        class="w-full h-full rounded-xl object-cover"
                                    />
                                    <div v-else class="w-full h-full rounded-xl bg-gradient-to-br from-gray-100 to-gray-200 flex items-center justify-center">
                                        <span class="text-5xl">👨‍🏫</span>
                                    </div>
                                </div>
                            </div>

                            <!-- Content -->
                            <div class="p-6 pt-4">
                                <h3 class="text-xl font-bold text-gray-900 text-center mb-1">{{ teacher.name }}</h3>
                                
                                <!-- Rating & Reviews -->
                                <div class="flex items-center justify-center gap-2 text-sm text-gray-500 mb-4">
                                    <span>{{ teacher.reviews_count }} reviews</span>
                                    <span>•</span>
                                    <span>{{ teacher.students_taught }}+ students</span>
                                </div>

                                <!-- Specializations -->
                                <div class="flex flex-wrap justify-center gap-2 mb-4">
                                    <span
                                        v-for="spec in (teacher.specializations || []).slice(0, 3)"
                                        :key="spec"
                                        class="px-3 py-1 bg-gradient-to-r from-purple-100 to-indigo-100 text-purple-700 text-xs font-semibold rounded-full"
                                    >
                                        {{ spec }}
                                    </span>
                                </div>

                                <!-- Bio -->
                                <p class="text-gray-600 text-sm mb-4 line-clamp-2 text-center">{{ teacher.bio }}</p>

                                <!-- Languages -->
                                <div class="flex items-center justify-center gap-2 text-sm text-gray-400 mb-6">
                                    <span class="text-base">🌍</span>
                                    <span>{{ (teacher.languages || ['English']).slice(0, 3).join(' • ') }}</span>
                                </div>

                                <!-- Action Buttons -->
                                <div class="flex gap-3">
                                    <Link
                                        :href="`/teachers/${teacher.id}`"
                                        class="flex-1 py-3 text-center bg-gray-100 hover:bg-gray-200 text-gray-700 font-semibold rounded-xl transition-all hover:scale-105"
                                    >
                                        View Profile
                                    </Link>
                                    <Link
                                        href="/courses"
                                        :class="[
                                            'flex-1 py-3 text-center font-semibold rounded-xl transition-all hover:scale-105',
                                            teacher.is_available
                                                ? 'bg-gradient-to-r from-purple-500 to-indigo-600 hover:from-purple-600 hover:to-indigo-700 text-white shadow-lg'
                                                : 'bg-gray-100 text-gray-400 cursor-not-allowed'
                                        ]"
                                    >
                                        Book Class
                                    </Link>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Why Our Teachers Section -->
        <section class="py-20 bg-gradient-to-br from-gray-50 to-purple-50/30">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center mb-12">
                    <span class="inline-block px-4 py-2 bg-purple-100 text-purple-700 text-sm font-semibold rounded-full mb-4">Why Choose Our Teachers</span>
                    <h2 class="text-3xl font-bold text-gray-900">Excellence in Every Lesson</h2>
                </div>
                
                <div class="grid md:grid-cols-3 gap-8">
                    <div class="bg-white rounded-2xl p-8 shadow-lg hover:shadow-xl transition-all hover:-translate-y-1">
                        <div class="w-14 h-14 rounded-xl bg-gradient-to-r from-emerald-500 to-teal-600 flex items-center justify-center text-2xl mb-6">
                            📜
                        </div>
                        <h3 class="text-xl font-bold text-gray-900 mb-3">Certified & Verified</h3>
                        <p class="text-gray-600">All our teachers hold authentic Ijazah (certification) with verified chains of transmission.</p>
                    </div>
                    <div class="bg-white rounded-2xl p-8 shadow-lg hover:shadow-xl transition-all hover:-translate-y-1">
                        <div class="w-14 h-14 rounded-xl bg-gradient-to-r from-blue-500 to-indigo-600 flex items-center justify-center text-2xl mb-6">
                            🎯
                        </div>
                        <h3 class="text-xl font-bold text-gray-900 mb-3">Personalized Teaching</h3>
                        <p class="text-gray-600">One-on-one sessions tailored to your learning pace, goals, and schedule preferences.</p>
                    </div>
                    <div class="bg-white rounded-2xl p-8 shadow-lg hover:shadow-xl transition-all hover:-translate-y-1">
                        <div class="w-14 h-14 rounded-xl bg-gradient-to-r from-purple-500 to-pink-600 flex items-center justify-center text-2xl mb-6">
                            🌐
                        </div>
                        <h3 class="text-xl font-bold text-gray-900 mb-3">Global & Multilingual</h3>
                        <p class="text-gray-600">Teachers from 15+ countries speaking 20+ languages to serve you better.</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- Become a Teacher CTA -->
        <section class="py-24 bg-gradient-to-br from-purple-900 via-indigo-800 to-purple-900 relative overflow-hidden">
            <!-- Pattern overlay -->
            <div class="absolute inset-0 opacity-10">
                <div class="absolute inset-0" style="background-image: url('data:image/svg+xml,%3Csvg width=\'60\' height=\'60\' viewBox=\'0 0 60 60\' xmlns=\'http://www.w3.org/2000/svg\'%3E%3Cg fill=\'none\'%3E%3Cg fill=\'%23ffffff\' fill-opacity=\'0.4\'%3E%3Cpath d=\'M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z\'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E');"></div>
            </div>
            
            <!-- Glowing orbs -->
            <div class="absolute top-0 left-1/4 w-96 h-96 bg-purple-500/20 rounded-full blur-3xl"></div>
            <div class="absolute bottom-0 right-1/4 w-80 h-80 bg-indigo-500/20 rounded-full blur-3xl"></div>
            
            <div class="relative max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
                <span class="text-6xl mb-6 block">🌟</span>
                <h2 class="text-3xl sm:text-4xl font-display font-bold text-white mb-6">
                    Want to Join Our Team?
                </h2>
                <p class="text-xl text-purple-100 mb-10 max-w-2xl mx-auto">
                    If you're a qualified Quran teacher looking to share your knowledge with students worldwide, we'd love to hear from you.
                </p>
                <div class="flex flex-col sm:flex-row gap-4 justify-center">
                    <Link
                        href="/teachers/apply"
                        class="inline-flex items-center justify-center px-8 py-4 bg-gradient-to-r from-amber-500 to-yellow-500 hover:from-amber-600 hover:to-yellow-600 text-gray-900 text-lg font-semibold rounded-2xl transition-all hover:shadow-xl hover:scale-105"
                    >
                        Apply to Teach ✨
                    </Link>
                    <Link
                        href="/contact"
                        class="inline-flex items-center justify-center px-8 py-4 bg-white/10 backdrop-blur-sm border-2 border-white/30 text-white text-lg font-semibold rounded-2xl hover:bg-white/20 transition-all hover:scale-105"
                    >
                        Learn More
                    </Link>
                </div>
            </div>
        </section>
    </PublicLayout>
</template>

<style scoped>
.line-clamp-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

/* Smooth animations */
.teacher-card,
.stat-card,
.filter-btn {
    will-change: transform;
}
</style>
