<script setup>
import { Head, Link } from '@inertiajs/vue3';
import { onMounted } from 'vue';
import PublicLayout from '@/Layouts/PublicLayout.vue';
import MetaTags from '@/Components/SEO/MetaTags.vue';
import { seoConfig } from '@/utils/seo.js';
import gsap from 'gsap';

const values = [
    {
        icon: '📖',
        title: 'Authentic Teaching',
        description: 'We follow traditional methods of Quran education with qualified scholars who have authentic chains of transmission (Ijazah).',
        gradient: 'from-emerald-500 to-teal-600',
    },
    {
        icon: '🌍',
        title: 'Global Access',
        description: 'Making quality Quran education accessible to Muslims worldwide, regardless of location or timezone.',
        gradient: 'from-blue-500 to-indigo-600',
    },
    {
        icon: '❤️',
        title: 'Student-Centered',
        description: 'Every student is unique. We personalize learning paths to fit individual goals, pace, and schedules.',
        gradient: 'from-rose-500 to-pink-600',
    },
    {
        icon: '🤝',
        title: 'Community',
        description: 'Building a supportive community of learners who encourage and motivate each other.',
        gradient: 'from-amber-500 to-orange-600',
    },
];

const stats = [
    { value: '5,000+', label: 'Students Taught', icon: '👨‍🎓' },
    { value: '50+', label: 'Qualified Teachers', icon: '👨‍🏫' },
    { value: '45+', label: 'Countries Reached', icon: '🌍' },
    { value: '98%', label: 'Satisfaction Rate', icon: '⭐' },
];

const team = [
    {
        name: 'Sheikh Ahmad Hassan',
        role: 'Founder & Head of Curriculum',
        bio: '20+ years teaching Quran, holder of multiple Ijazahs in Qira\'at',
        gradient: 'from-emerald-500 to-teal-600',
    },
    {
        name: 'Dr. Fatima Ali',
        role: 'Director of Education',
        bio: 'PhD in Islamic Studies, specialist in Tajweed methodology',
        gradient: 'from-purple-500 to-pink-600',
    },
    {
        name: 'Ustadh Mohammed Rahman',
        role: 'Lead Hifz Instructor',
        bio: 'Hafiz of Quran, trained at Al-Azhar University',
        gradient: 'from-blue-500 to-indigo-600',
    },
    {
        name: 'Sarah Johnson',
        role: 'Head of Operations',
        bio: '15 years in EdTech, passionate about accessible education',
        gradient: 'from-amber-500 to-orange-600',
    },
];

const milestones = [
    { year: '2020', title: 'Founded', description: 'Started with 5 teachers and a vision' },
    { year: '2021', title: '1000 Students', description: 'Reached our first major milestone' },
    { year: '2022', title: 'Global Expansion', description: 'Teachers from 15+ countries joined' },
    { year: '2023', title: '5000+ Students', description: 'Serving learners worldwide' },
];

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

    // Stat cards animation
    gsap.from('.stat-card', {
        y: 50,
        opacity: 0,
        duration: 0.6,
        stagger: 0.1,
        delay: 0.3,
        ease: 'power2.out'
    });

    // Value cards animation
    gsap.from('.value-card', {
        y: 40,
        opacity: 0,
        duration: 0.5,
        stagger: 0.1,
        scrollTrigger: {
            trigger: '.values-section',
            start: 'top 80%'
        },
        ease: 'power2.out'
    });

    // Team cards animation
    gsap.from('.team-card', {
        scale: 0.9,
        opacity: 0,
        duration: 0.5,
        stagger: 0.1,
        scrollTrigger: {
            trigger: '.team-section',
            start: 'top 80%'
        },
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

    // Timeline animation
    gsap.from('.timeline-item', {
        x: -50,
        opacity: 0,
        duration: 0.5,
        stagger: 0.15,
        scrollTrigger: {
            trigger: '.timeline-section',
            start: 'top 80%'
        },
        ease: 'power2.out'
    });
});
</script>

<template>
    <Head title="About Us | QuranLearn" />
    <!-- SEO Meta Tags -->
    <MetaTags 
        :title="seoConfig.about.title"
        :description="seoConfig.about.description"
        :keywords="seoConfig.about.keywords"
    />
    
    <PublicLayout>
        <!-- Hero Section with Particles -->
        <section class="relative pt-28 pb-24 overflow-hidden bg-gradient-to-br from-emerald-900 via-teal-800 to-emerald-900">
            <!-- Islamic Pattern Overlay -->
            <div class="absolute inset-0 opacity-10">
                <div class="absolute inset-0" style="background-image: url('data:image/svg+xml,%3Csvg width=\'60\' height=\'60\' viewBox=\'0 0 60 60\' xmlns=\'http://www.w3.org/2000/svg\'%3E%3Cg fill=\'none\' fill-rule=\'evenodd\'%3E%3Cg fill=\'%23ffffff\' fill-opacity=\'0.4\'%3E%3Cpath d=\'M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z\'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E');"></div>
            </div>

            <!-- Floating Particles -->
            <div class="absolute inset-0 pointer-events-none">
                <div v-for="n in 15" :key="n" class="particle absolute w-2 h-2 bg-gold-400 rounded-full opacity-40" 
                    :style="{ left: Math.random() * 100 + '%', top: Math.random() * 100 + '%' }"></div>
            </div>

            <!-- Glowing Orbs -->
            <div class="absolute top-20 left-10 w-72 h-72 bg-emerald-500/30 rounded-full blur-3xl"></div>
            <div class="absolute bottom-20 right-10 w-96 h-96 bg-teal-500/20 rounded-full blur-3xl"></div>

            <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="max-w-4xl mx-auto text-center">
                    <h1 class="hero-title text-4xl sm:text-5xl lg:text-6xl font-display font-bold text-white mb-6">
                        Our Mission: Making Quran Education
                        <span class="text-transparent bg-clip-text bg-gradient-to-r from-gold-400 to-amber-300 block mt-2">Accessible to All</span>
                    </h1>
                    <p class="hero-subtitle text-xl text-emerald-100 max-w-2xl mx-auto">
                        We believe every Muslim deserves access to quality Quran education, regardless of where they live. 
                        Our platform connects students with qualified teachers for a transformative learning experience.
                    </p>
                </div>
            </div>

            <!-- Wave Decoration -->
            <div class="absolute bottom-0 left-0 right-0">
                <svg viewBox="0 0 1440 120" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M0 120L60 110C120 100 240 80 360 70C480 60 600 60 720 65C840 70 960 80 1080 85C1200 90 1320 90 1380 90L1440 90V120H1380C1320 120 1200 120 1080 120C960 120 840 120 720 120C600 120 480 120 360 120C240 120 120 120 60 120H0Z" fill="white"/>
                </svg>
            </div>
        </section>

        <!-- Stats Section with Glassmorphism -->
        <section class="py-12 bg-white -mt-1">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
                    <div 
                        v-for="stat in stats" 
                        :key="stat.label" 
                        class="stat-card group relative bg-gradient-to-br from-emerald-50 to-teal-50 rounded-2xl p-6 text-center hover:shadow-xl transition-all duration-300 hover:-translate-y-2 overflow-hidden"
                    >
                        <!-- Shine effect -->
                        <div class="absolute inset-0 bg-gradient-to-r from-transparent via-white/60 to-transparent -translate-x-full group-hover:translate-x-full transition-transform duration-700"></div>
                        
                        <span class="text-3xl mb-3 block transform group-hover:scale-110 transition-transform">{{ stat.icon }}</span>
                        <p class="text-3xl sm:text-4xl font-bold bg-gradient-to-r from-emerald-600 to-teal-600 bg-clip-text text-transparent">{{ stat.value }}</p>
                        <p class="text-gray-600 mt-1 font-medium">{{ stat.label }}</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- Our Story Section -->
        <section class="py-24 bg-white timeline-section">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="grid lg:grid-cols-2 gap-16 items-center">
                    <div>
                        <span class="inline-block px-4 py-2 bg-emerald-100 text-emerald-700 text-sm font-semibold rounded-full mb-6">Our Journey</span>
                        <h2 class="text-4xl font-display font-bold text-gray-900 mb-6">
                            From a Dream to a
                            <span class="text-transparent bg-clip-text bg-gradient-to-r from-emerald-600 to-teal-600">Global Platform</span>
                        </h2>
                        <div class="prose prose-lg text-gray-600 space-y-4">
                            <p>
                                QuranLearn was born from a simple observation: millions of Muslims around the world 
                                struggle to find quality Quran education that fits their busy lives.
                            </p>
                            <p>
                                Founded in 2020 by a team of Islamic scholars and technology professionals, 
                                we set out to bridge this gap by creating an online platform that combines 
                                traditional teaching methods with modern technology.
                            </p>
                            <p>
                                Today, we're proud to serve students from over 45 countries, helping them 
                                read, recite, and memorize the Holy Quran with proper Tajweed under the 
                                guidance of certified teachers.
                            </p>
                        </div>
                    </div>
                    
                    <!-- Quranic Verse Card -->
                    <div class="relative">
                        <div class="absolute inset-0 bg-gradient-to-br from-emerald-500 to-teal-600 rounded-3xl rotate-3 opacity-20"></div>
                        <div class="relative bg-gradient-to-br from-emerald-50 to-teal-50 rounded-3xl p-10 border border-emerald-100 shadow-xl">
                            <div class="absolute -top-4 -right-4 w-16 h-16 bg-gradient-to-r from-gold-400 to-amber-500 rounded-2xl flex items-center justify-center shadow-lg rotate-12">
                                <span class="text-3xl">📖</span>
                            </div>
                            <p class="font-arabic text-4xl text-emerald-800 mb-6 text-center leading-relaxed">
                                إِقْرَأْ بِاسْمِ رَبِّكَ الَّذِي خَلَقَ
                            </p>
                            <div class="text-center">
                                <p class="text-gray-700 italic text-lg mb-2">
                                    "Read in the name of your Lord who created"
                                </p>
                                <p class="text-emerald-600 font-medium">Surah Al-Alaq (96:1)</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Timeline -->
                <div class="mt-20">
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
                        <div 
                            v-for="(milestone, index) in milestones" 
                            :key="milestone.year"
                            class="timeline-item relative bg-white rounded-2xl p-6 border border-gray-100 shadow-lg hover:shadow-xl transition-all hover:-translate-y-1"
                        >
                            <div class="absolute -top-3 left-6 px-3 py-1 bg-gradient-to-r from-emerald-500 to-teal-600 text-white text-sm font-bold rounded-full">
                                {{ milestone.year }}
                            </div>
                            <h4 class="text-lg font-bold text-gray-900 mt-4">{{ milestone.title }}</h4>
                            <p class="text-gray-500 text-sm mt-1">{{ milestone.description }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Values Section -->
        <section class="py-24 bg-gradient-to-br from-gray-50 to-emerald-50/30 values-section">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center max-w-3xl mx-auto mb-16">
                    <span class="inline-block px-4 py-2 bg-emerald-100 text-emerald-700 text-sm font-semibold rounded-full mb-6">What We Stand For</span>
                    <h2 class="text-4xl font-display font-bold text-gray-900 mb-4">Our Core Values</h2>
                    <p class="text-xl text-gray-600">
                        These principles guide everything we do at QuranLearn.
                    </p>
                </div>

                <div class="grid md:grid-cols-2 lg:grid-cols-4 gap-8">
                    <div
                        v-for="value in values"
                        :key="value.title"
                        class="value-card group bg-white rounded-3xl p-8 shadow-lg hover:shadow-2xl transition-all duration-500 hover:-translate-y-2 overflow-hidden relative"
                    >
                        <!-- Background gradient on hover -->
                        <div :class="['absolute inset-0 bg-gradient-to-br opacity-0 group-hover:opacity-5 transition-opacity duration-500', value.gradient]"></div>
                        
                        <!-- Icon with gradient background -->
                        <div :class="['w-16 h-16 rounded-2xl bg-gradient-to-br flex items-center justify-center text-3xl mb-6 transform group-hover:scale-110 group-hover:rotate-3 transition-all duration-300 shadow-lg', value.gradient]">
                            {{ value.icon }}
                        </div>
                        
                        <h3 class="text-xl font-bold text-gray-900 mb-3">{{ value.title }}</h3>
                        <p class="text-gray-600 leading-relaxed">{{ value.description }}</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- Team Section -->
        <section class="py-24 bg-white team-section">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center max-w-3xl mx-auto mb-16">
                    <span class="inline-block px-4 py-2 bg-emerald-100 text-emerald-700 text-sm font-semibold rounded-full mb-6">The People Behind QuranLearn</span>
                    <h2 class="text-4xl font-display font-bold text-gray-900 mb-4">Meet Our Leadership</h2>
                    <p class="text-xl text-gray-600">
                        Dedicated professionals committed to transforming Quran education.
                    </p>
                </div>

                <div class="grid md:grid-cols-2 lg:grid-cols-4 gap-8">
                    <div
                        v-for="member in team"
                        :key="member.name"
                        class="team-card group"
                    >
                        <div class="relative bg-white rounded-3xl overflow-hidden shadow-lg hover:shadow-2xl transition-all duration-500 hover:-translate-y-2">
                            <!-- Header with gradient -->
                            <div :class="['h-32 bg-gradient-to-br relative', member.gradient]">
                                <div class="absolute inset-0 bg-[url(\'data:image/svg+xml,%3Csvg width=%2220%22 height=%2220%22 viewBox=%220 0 20 20%22 xmlns=%22http://www.w3.org/2000/svg%22%3E%3Cg fill=%22%23ffffff%22 fill-opacity=%220.05%22%3E%3Cpath d=%22M0 0h20L10 10zm10 10L0 20h20z%22/%3E%3C/g%3E%3C/svg%3E\')]"></div>
                            </div>
                            
                            <!-- Avatar -->
                            <div class="relative -mt-12 flex justify-center">
                                <div class="w-24 h-24 rounded-2xl bg-white p-1 shadow-xl transform group-hover:scale-105 transition-transform">
                                    <div class="w-full h-full rounded-xl bg-gradient-to-br from-gray-100 to-gray-200 flex items-center justify-center">
                                        <span class="text-4xl">👤</span>
                                    </div>
                                </div>
                            </div>

                            <!-- Content -->
                            <div class="p-6 pt-4 text-center">
                                <h3 class="text-lg font-bold text-gray-900">{{ member.name }}</h3>
                                <p class="text-emerald-600 text-sm font-medium mb-3">{{ member.role }}</p>
                                <p class="text-gray-500 text-sm">{{ member.bio }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- CTA Section -->
        <section class="py-24 bg-gradient-to-br from-emerald-900 via-teal-800 to-emerald-900 relative overflow-hidden">
            <!-- Pattern overlay -->
            <div class="absolute inset-0 opacity-10">
                <div class="absolute inset-0" style="background-image: url('data:image/svg+xml,%3Csvg width=\'60\' height=\'60\' viewBox=\'0 0 60 60\' xmlns=\'http://www.w3.org/2000/svg\'%3E%3Cg fill=\'none\'%3E%3Cg fill=\'%23ffffff\' fill-opacity=\'0.4\'%3E%3Cpath d=\'M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z\'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E');"></div>
            </div>
            
            <!-- Glowing orbs -->
            <div class="absolute top-0 left-1/4 w-96 h-96 bg-emerald-500/20 rounded-full blur-3xl"></div>
            <div class="absolute bottom-0 right-1/4 w-80 h-80 bg-teal-500/20 rounded-full blur-3xl"></div>
            
            <div class="relative max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
                <span class="text-6xl mb-6 block animate-bounce">🌟</span>
                <h2 class="text-3xl sm:text-4xl font-display font-bold text-white mb-6">
                    Join Our Growing Community
                </h2>
                <p class="text-xl text-emerald-100 mb-10 max-w-2xl mx-auto">
                    Start your Quran learning journey today and become part of our global family of learners.
                </p>
                <div class="flex flex-col sm:flex-row gap-4 justify-center">
                    <Link
                        href="/register"
                        class="inline-flex items-center justify-center px-8 py-4 bg-gradient-to-r from-gold-500 to-amber-500 hover:from-gold-600 hover:to-amber-600 text-gray-900 text-lg font-semibold rounded-2xl transition-all hover:shadow-xl hover:scale-105"
                    >
                        Start Learning Today ✨
                    </Link>
                    <Link
                        href="/contact"
                        class="inline-flex items-center justify-center px-8 py-4 bg-white/10 backdrop-blur-sm border-2 border-white/30 text-white text-lg font-semibold rounded-2xl hover:bg-white/20 transition-all hover:scale-105"
                    >
                        Contact Us
                    </Link>
                </div>
            </div>
        </section>
    </PublicLayout>
</template>

<style scoped>
/* Smooth animations */
.value-card,
.team-card,
.stat-card,
.timeline-item {
    will-change: transform;
}
</style>
