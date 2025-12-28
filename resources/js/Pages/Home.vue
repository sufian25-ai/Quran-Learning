<script setup>
import { Head, Link } from '@inertiajs/vue3';
import { onMounted, ref } from 'vue';
import PublicLayout from '@/Layouts/PublicLayout.vue';
import MetaTags from '@/Components/SEO/MetaTags.vue';
import { seoConfig } from '@/utils/seo.js';
import gsap from 'gsap';

defineProps({
    featuredCourses: {
        type: Array,
        default: () => []
    },
    stats: {
        type: Object,
        default: () => ({
            students: 5000,
            teachers: 50,
            courses: 20,
            countries: 45
        })
    }
});

const features = [
    {
        icon: '🎓',
        title: 'Qualified Teachers',
        description: 'Learn from certified scholars with Ijazah credentials and years of teaching experience.',
        gradient: 'from-emerald-400 to-teal-500'
    },
    {
        icon: '🌍',
        title: 'Learn Anywhere',
        description: 'Join live classes from anywhere in the world with our seamless Zoom integration.',
        gradient: 'from-blue-400 to-indigo-500'
    },
    {
        icon: '📱',
        title: 'Flexible Schedule',
        description: 'Choose from group classes or private 1-on-1 sessions that fit your schedule.',
        gradient: 'from-purple-400 to-pink-500'
    },
    {
        icon: '📊',
        title: 'Track Progress',
        description: 'Monitor your learning journey with detailed progress tracking and achievements.',
        gradient: 'from-orange-400 to-red-500'
    },
    {
        icon: '🏆',
        title: 'Earn Certificates',
        description: 'Receive official certificates upon completing courses to showcase your achievement.',
        gradient: 'from-amber-400 to-yellow-500'
    },
    {
        icon: '💬',
        title: '24/7 Support',
        description: 'Get help anytime with our dedicated support team ready to assist you.',
        gradient: 'from-cyan-400 to-blue-500'
    }
];

const testimonials = [
    {
        name: 'Sarah Ahmed',
        location: 'London, UK',
        image: '/images/avatars/sarah.jpg',
        text: 'The teachers are incredibly patient and skilled. My children have improved their Quran reading dramatically in just 3 months.',
        rating: 5
    },
    {
        name: 'Mohammed Ali',
        location: 'Toronto, Canada',
        image: '/images/avatars/mohammed.jpg',
        text: 'I finally learned proper Tajweed after years of trying. The one-on-one sessions made all the difference.',
        rating: 5
    },
    {
        name: 'Fatima Hassan',
        location: 'Sydney, Australia',
        image: '/images/avatars/fatima.jpg',
        text: 'The flexible scheduling is perfect for my busy lifestyle. I can learn at my own pace without stress.',
        rating: 5
    }
];

// GSAP Animations
onMounted(() => {
    // Hero section animations
    gsap.from('.hero-badge', {
        y: -30,
        opacity: 0,
        duration: 0.8,
        ease: 'power3.out'
    });

    gsap.from('.hero-title', {
        y: 50,
        opacity: 0,
        duration: 1,
        delay: 0.2,
        ease: 'power3.out'
    });

    gsap.from('.hero-subtitle', {
        y: 30,
        opacity: 0,
        duration: 0.8,
        delay: 0.4,
        ease: 'power3.out'
    });

    gsap.from('.hero-buttons', {
        y: 30,
        opacity: 0,
        duration: 0.8,
        delay: 0.6,
        ease: 'power3.out'
    });

    gsap.from('.hero-trust', {
        y: 20,
        opacity: 0,
        duration: 0.8,
        delay: 0.8,
        ease: 'power3.out'
    });

    // Stats counter animation - immediate with delay
    gsap.from('.stat-item', {
        y: 40,
        opacity: 0,
        duration: 0.6,
        stagger: 0.1,
        delay: 0.5,
        ease: 'power2.out'
    });

    // Features animation - immediate with delay
    gsap.from('.feature-card', {
        y: 40,
        opacity: 0,
        duration: 0.6,
        stagger: 0.08,
        delay: 0.8,
        ease: 'power2.out'
    });

    // Courses animation - immediate with delay
    gsap.from('.course-card', {
        y: 30,
        opacity: 0,
        duration: 0.5,
        stagger: 0.1,
        delay: 1,
        ease: 'power2.out'
    });

    // Testimonials animation - immediate with delay
    gsap.from('.testimonial-card', {
        y: 30,
        opacity: 0,
        duration: 0.6,
        stagger: 0.15,
        delay: 1.2,
        ease: 'power2.out'
    });

    // Floating animations
    gsap.to('.floating-decoration', {
        y: -20,
        duration: 3,
        repeat: -1,
        yoyo: true,
        ease: 'power1.inOut',
        stagger: 0.5
    });
    // 3D Quran Rising Animation
    gsap.from('.floating-animation-container', {
        y: 200,
        opacity: 0,
        duration: 1.5,
        delay: 0.2, // Start early
        ease: 'power3.out'
    });

    // Animate Rehal Leg unfolding (optional polish)
    gsap.from('.rehal-leg', {
        rotationX: 90,
        opacity: 0,
        duration: 1.2,
        delay: 0.5,
        stagger: 0.2,
        ease: 'back.out(1.7)'
    });
});
</script>

<template>
    <Head title="Learn Quran Online | QuranLearn" />
    
    <PublicLayout>
        <!-- SEO Meta Tags -->
        <MetaTags 
            :title="seoConfig.home.title"
            :description="seoConfig.home.description"
            :keywords="seoConfig.home.keywords"
        />
        
        <!-- Head for legacy compat -->
        <Head title="Home" />
        <!-- Hero Section with Quran Book Animation -->
        <section class="relative min-h-screen flex items-center overflow-hidden bg-gradient-to-b from-emerald-900 via-emerald-800 to-teal-900">
            <!-- Islamic Pattern Background -->
            <div class="absolute inset-0 opacity-10">
                <div class="absolute inset-0" style="background-image: url('data:image/svg+xml,%3Csvg width=\'60\' height=\'60\' viewBox=\'0 0 60 60\' xmlns=\'http://www.w3.org/2000/svg\'%3E%3Cg fill=\'none\' fill-rule=\'evenodd\'%3E%3Cg fill=\'%23ffffff\' fill-opacity=\'0.4\'%3E%3Cpath d=\'M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z\'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E');"></div>
            </div>

            <!-- Light Rays Effect -->
            <div class="absolute inset-0 flex items-center justify-center">
                <div class="light-rays"></div>
            </div>

            <!-- Floating Particles -->
            <div class="particles-container">
                <div class="particle" v-for="n in 30" :key="n" :style="{
                    left: Math.random() * 100 + '%',
                    animationDelay: Math.random() * 5 + 's',
                    animationDuration: (3 + Math.random() * 4) + 's'
                }"></div>
            </div>

            <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-20 pb-20">
                <div class="grid lg:grid-cols-2 gap-12 items-center">
                    <!-- Left Content -->
                    <div class="text-center lg:text-left animate-fade-in z-10">
                        <div class="inline-flex items-center px-4 py-2 bg-white/10 backdrop-blur-sm rounded-full text-white text-sm font-medium mb-6">
                            <span class="animate-pulse-slow mr-2">✨</span>
                            Trusted by 5,000+ students worldwide
                        </div>
                        
                        <h1 class="text-4xl sm:text-5xl lg:text-6xl font-display font-bold text-white leading-tight mb-6">
                            Learn the <span class="text-gold-400">Holy Quran</span> with Expert Teachers
                        </h1>
                        
                        <p class="text-xl text-emerald-100 mb-8 max-w-xl mx-auto lg:mx-0">
                            Join thousands of students learning Quran, Tajweed, and Arabic with certified scholars. 
                            Start your spiritual journey today.
                        </p>

                        <div class="flex flex-col sm:flex-row gap-4 justify-center lg:justify-start">
                            <Link
                                href="/register"
                                class="inline-flex items-center justify-center px-8 py-4 bg-gold-500 hover:bg-gold-600 text-gray-900 text-lg font-semibold rounded-2xl transition-all hover:shadow-glow hover:-translate-y-0.5"
                            >
                                Start Free Trial
                                <svg class="ml-2 w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
                                </svg>
                            </Link>
                            <Link
                                href="/read-quran"
                                class="inline-flex items-center justify-center px-8 py-4 bg-white/10 backdrop-blur-sm hover:bg-white/20 text-white text-lg font-semibold rounded-2xl border border-white/30 transition-all hover:-translate-y-0.5"
                            >
                                📖 Read Quran Free
                            </Link>
                        </div>

                        <!-- Trust Indicators -->
                        <div class="mt-12 flex flex-wrap items-center justify-center lg:justify-start gap-8">
                            <div class="flex items-center text-white/80">
                                <div class="flex -space-x-3">
                                    <div class="w-10 h-10 rounded-full bg-emerald-400/30 border-2 border-white/30 flex items-center justify-center text-sm">👨‍🏫</div>
                                    <div class="w-10 h-10 rounded-full bg-gold-400/30 border-2 border-white/30 flex items-center justify-center text-sm">👩‍🏫</div>
                                    <div class="w-10 h-10 rounded-full bg-teal-400/30 border-2 border-white/30 flex items-center justify-center text-sm">👨‍🏫</div>
                                </div>
                                <span class="ml-3">50+ Expert Teachers</span>
                            </div>
                            <div class="flex items-center text-white/80">
                                <div class="flex text-gold-400">
                                    <svg v-for="i in 5" :key="i" class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                    </svg>
                                </div>
                                <span class="ml-2">4.9/5 Rating</span>
                            </div>
                        </div>
                    </div>

                    <!-- Right Content - Advanced 3D Quran Animation -->
                    <div class="relative h-[600px] w-full perspective-1000 flex items-center justify-center z-10">
                        <!-- Divine Clouds Base -->
                        <div class="divine-cloud-base"></div>
                        
                        <!-- Floating Container -->
                        <div class="floating-animation-container">
                            <!-- Helper: Light Shafts -->
                            <div class="light-shafts"></div>

                            <!-- 3D Book - Redesigned to match reference -->
                            <div class="quran-book-3d">
                                <!-- Left Side (Cover + Page) -->
                                <div class="book-left-side">
                                    <!-- Dark Cover Edge -->
                                    <div class="cover-edge cover-edge-left"></div>
                                    <!-- Page with Gold Border -->
                                    <div class="page-with-border page-left">
                                        <div class="gold-frame">
                                            <div class="page-lines">
                                                <p class="arabic-text-line" style="direction: rtl;">بِسْمِ ٱللَّهِ ٱلرَّحْمَٰنِ ٱلرَّحِيمِ</p>
                                                <p class="arabic-text-line" style="direction: rtl;">ٱلْحَمْدُ لِلَّهِ رَبِّ ٱلْعَٰلَمِينَ</p>
                                                <p class="arabic-text-line" style="direction: rtl;">ٱلرَّحْمَٰنِ ٱلرَّحِيمِ</p>
                                                <p class="arabic-text-line" style="direction: rtl;">مَٰلِكِ يَوْمِ ٱلدِّينِ</p>
                                                <p class="arabic-text-line" style="direction: rtl;">إِيَّاكَ نَعْبُدُ وَإِيَّاكَ نَسْتَعِينُ</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Center Spine (Red with Gold Dots) -->
                                <div class="book-spine-center">
                                    <div class="spine-dot" v-for="n in 6" :key="n"></div>
                                </div>

                                <!-- Right Side (Cover + Page) -->
                                <div class="book-right-side">
                                    <!-- Page with Gold Border -->
                                    <div class="page-with-border page-right">
                                        <div class="gold-frame">
                                            <div class="page-lines">
                                                <p class="arabic-text-line" style="direction: rtl;">ٱهْدِنَا ٱلصِّرَٰطَ ٱلْمُسْتَقِيمَ</p>
                                                <p class="arabic-text-line" style="direction: rtl;">صِرَٰطَ ٱلَّذِينَ أَنْعَمْتَ عَلَيْهِمْ</p>
                                                <p class="arabic-text-line" style="direction: rtl;">غَيْرِ ٱلْمَغْضُوبِ عَلَيْهِمْ</p>
                                                <p class="arabic-text-line" style="direction: rtl;">وَلَا ٱلضَّآلِّينَ</p>
                                                <p class="arabic-text-line" style="direction: rtl;">آمِينَ</p>
                                            </div>
                                        </div>
                                    </div>
                                    <!-- Dark Cover Edge -->
                                    <div class="cover-edge cover-edge-right"></div>
                                </div>
                            </div>  <!-- end quran-book-3d -->
                        </div>  <!-- end floating-animation-container -->
                    </div>  <!-- end right content container -->
                </div>  <!-- end grid -->
            </div>  <!-- end max-w-7xl -->
        

            <!-- Scroll Indicator -->
            <div class="absolute bottom-8 left-1/2 -translate-x-1/2 text-white/60 animate-bounce">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3"/>
                </svg>
            </div>
        </section>

        <!-- Stats Section -->
        <section class="stats-section py-20 bg-gradient-to-br from-emerald-50 to-teal-50 relative overflow-hidden">
            <!-- Decorations -->
            <div class="floating-decoration absolute top-10 left-10 w-32 h-32 bg-emerald-200 rounded-full opacity-30 blur-2xl"></div>
            <div class="floating-decoration absolute bottom-10 right-10 w-48 h-48 bg-teal-200 rounded-full opacity-30 blur-2xl"></div>
            
            <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="grid grid-cols-2 md:grid-cols-4 gap-8">
                    <div class="stat-item text-center bg-white/80 backdrop-blur-sm rounded-3xl p-8 shadow-lg border border-white">
                        <div class="text-5xl mb-2">👨‍🎓</div>
                        <p class="text-4xl font-bold bg-gradient-to-r from-emerald-500 to-teal-600 text-transparent bg-clip-text">{{ stats.students.toLocaleString() }}+</p>
                        <p class="text-gray-600 mt-1 font-medium">Happy Students</p>
                    </div>
                    <div class="stat-item text-center bg-white/80 backdrop-blur-sm rounded-3xl p-8 shadow-lg border border-white">
                        <div class="text-5xl mb-2">👨‍🏫</div>
                        <p class="text-4xl font-bold bg-gradient-to-r from-blue-500 to-indigo-600 text-transparent bg-clip-text">{{ stats.teachers }}+</p>
                        <p class="text-gray-600 mt-1 font-medium">Expert Teachers</p>
                    </div>
                    <div class="stat-item text-center bg-white/80 backdrop-blur-sm rounded-3xl p-8 shadow-lg border border-white">
                        <div class="text-5xl mb-2">📚</div>
                        <p class="text-4xl font-bold bg-gradient-to-r from-purple-500 to-pink-600 text-transparent bg-clip-text">{{ stats.courses }}+</p>
                        <p class="text-gray-600 mt-1 font-medium">Courses</p>
                    </div>
                    <div class="stat-item text-center bg-white/80 backdrop-blur-sm rounded-3xl p-8 shadow-lg border border-white">
                        <div class="text-5xl mb-2">🌍</div>
                        <p class="text-4xl font-bold bg-gradient-to-r from-amber-500 to-orange-600 text-transparent bg-clip-text">{{ stats.countries }}+</p>
                        <p class="text-gray-600 mt-1 font-medium">Countries</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- FREE QURAN READING SECTION - SEO Optimized -->
        <section class="py-20 bg-gradient-to-b from-emerald-900 via-emerald-800 to-teal-900 text-white relative overflow-hidden">
            <!-- Islamic Pattern Background -->
            <div class="absolute inset-0 opacity-10">
                <div class="absolute inset-0" style="background-image: url('data:image/svg+xml,%3Csvg width=\'60\' height=\'60\' viewBox=\'0 0 60 60\' xmlns=\'http://www.w3.org/2000/svg\'%3E%3Cg fill=\'none\' fill-rule=\'evenodd\'%3E%3Cg fill=\'%23ffffff\' fill-opacity=\'0.4\'%3E%3Cpath d=\'M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z\'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E');"></div>
            </div>
            
            <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center mb-12">
                    <span class="inline-flex items-center px-4 py-2 bg-white/10 backdrop-blur-sm rounded-full text-white text-sm font-medium mb-4">
                        📖 100% Free Access
                    </span>
                    <h2 class="text-3xl sm:text-4xl font-display font-bold mb-4">
                        Read Quran Online for <span class="text-gold-400">FREE</span>
                    </h2>
                    <p class="text-xl text-emerald-100 max-w-2xl mx-auto">
                        Access the Holy Quran anytime, anywhere. No registration required.
                        Read with Arabic text and translations.
                    </p>
                </div>

                <div class="grid md:grid-cols-2 gap-8 max-w-4xl mx-auto">
                    <!-- Surah Reader Card -->
                    <Link href="/read-quran" class="bg-white/10 backdrop-blur-sm rounded-3xl p-8 hover:bg-white/20 transition-all hover:-translate-y-2 group">
                        <div class="text-6xl mb-4">📖</div>
                        <h3 class="text-2xl font-bold mb-2 group-hover:text-emerald-200">Quran Reader</h3>
                        <p class="text-emerald-100 mb-4">Read by Surah • All 114 Surahs • With Translation</p>
                        <ul class="text-sm text-emerald-200 space-y-2 mb-6">
                            <li>✓ Arabic Text with Tajweed</li>
                            <li>✓ English Translation</li>
                            <li>✓ Beautiful Reading Experience</li>
                        </ul>
                        <span class="inline-flex items-center px-6 py-3 bg-white text-emerald-700 rounded-xl font-semibold group-hover:bg-emerald-100 transition-colors">
                            Start Reading
                            <svg class="ml-2 w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
                            </svg>
                        </span>
                    </Link>

                    <!-- Hifz Reader Card -->
                    <Link href="/hifz-quran" class="bg-white/10 backdrop-blur-sm rounded-3xl p-8 hover:bg-white/20 transition-all hover:-translate-y-2 group">
                        <div class="text-6xl mb-4">📚</div>
                        <h3 class="text-2xl font-bold mb-2 group-hover:text-emerald-200">Hifz Quran</h3>
                        <p class="text-emerald-100 mb-4">Read by Para (Juz) • 30 Paras • Mushaf Style</p>
                        <ul class="text-sm text-emerald-200 space-y-2 mb-6">
                            <li>✓ Para-based Navigation</li>
                            <li>✓ Two-Page Mushaf View</li>
                            <li>✓ Perfect for Memorization</li>
                        </ul>
                        <span class="inline-flex items-center px-6 py-3 bg-white text-emerald-700 rounded-xl font-semibold group-hover:bg-emerald-100 transition-colors">
                            Start Reading
                            <svg class="ml-2 w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
                            </svg>
                        </span>
                    </Link>
                </div>
            </div>
        </section>

        <!-- Features Section -->
        <section class="features-section py-24 bg-gradient-to-b from-white to-gray-50">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center max-w-3xl mx-auto mb-16">
                    <span class="inline-flex items-center px-4 py-2 bg-emerald-100 text-emerald-700 rounded-full text-sm font-medium mb-4">
                        ✨ Why Choose Us
                    </span>
                    <h2 class="text-3xl sm:text-4xl font-display font-bold text-gray-900 mb-4">
                        Why Choose <span class="text-transparent bg-clip-text bg-gradient-to-r from-emerald-500 to-teal-600">QuranLearn</span>?
                    </h2>
                    <p class="text-xl text-gray-600">
                        Everything you need to master Quran recitation and deepen your understanding.
                    </p>
                </div>

                <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
                    <div
                        v-for="(feature, index) in features"
                        :key="index"
                        class="feature-card group bg-white rounded-3xl p-8 shadow-xl hover:shadow-2xl transition-all duration-500 hover:-translate-y-2 border-2 border-gray-200 relative overflow-hidden"
                    >
                        <!-- Gradient Background on Hover -->
                        <div :class="['absolute inset-0 bg-gradient-to-br opacity-0 group-hover:opacity-10 transition-opacity duration-500', feature.gradient]"></div>
                        
                        <div :class="['relative z-10 w-16 h-16 rounded-2xl flex items-center justify-center text-3xl mb-6 shadow-xl bg-gradient-to-br', feature.gradient]">
                            {{ feature.icon }}
                        </div>
                        <h3 class="relative z-10 text-xl font-bold text-gray-900 mb-3 group-hover:text-emerald-600 transition-colors">{{ feature.title }}</h3>
                        <p class="relative z-10 text-gray-700 font-medium">{{ feature.description }}</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- Courses Preview Section -->
        <section class="py-24 bg-white">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-12">
                    <div>
                        <h2 class="text-3xl sm:text-4xl font-display font-bold text-gray-900 mb-2">
                            Popular Courses
                        </h2>
                        <p class="text-xl text-gray-600">Start your learning journey with our top-rated courses</p>
                    </div>
                    <Link
                        href="/courses"
                        class="mt-4 sm:mt-0 inline-flex items-center text-primary-500 hover:text-primary-600 font-semibold"
                    >
                        View All Courses
                        <svg class="ml-1 w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                        </svg>
                    </Link>
                </div>

                <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
                    <!-- Course Card 1 - Quran Reading Basics -->
                    <Link href="/courses" class="course-card group">
                        <div class="bg-white rounded-3xl overflow-hidden shadow-xl hover:shadow-2xl transition-all duration-500 hover:-translate-y-2 border-2 border-gray-200">
                            <!-- Thumbnail with Overlay -->
                            <div class="relative h-48 overflow-hidden">
                                <div class="absolute inset-0 bg-gradient-to-br from-emerald-500 to-teal-600"></div>
                                <div class="absolute inset-0 flex items-center justify-center">
                                    <span class="text-7xl transform group-hover:scale-125 transition-transform duration-500">📖</span>
                                </div>
                                
                                <!-- Badges -->
                                <div class="absolute top-4 left-4 flex gap-2">
                                    <span class="px-3 py-1 text-xs font-bold rounded-full shadow-lg bg-gradient-to-r from-green-400 to-emerald-500 text-white">
                                        Beginner
                                    </span>
                                    <span class="px-3 py-1 bg-gradient-to-r from-amber-400 to-yellow-500 text-gray-900 text-xs font-bold rounded-full shadow-lg">
                                        Popular
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
                                <h3 class="text-xl font-bold text-gray-900 mb-2 group-hover:text-emerald-600 transition-colors">
                                    Quran Reading Basics
                                </h3>
                                <p class="text-gray-700 text-sm mb-4 font-medium">
                                    Learn to read the Quran with proper pronunciation from scratch.
                                </p>

                                <!-- Footer -->
                                <div class="flex items-center justify-between pt-4 border-t border-gray-100">
                                    <!-- Rating -->
                                    <div class="flex items-center gap-1">
                                        <div class="flex text-amber-400">
                                            <svg v-for="i in 5" :key="i" class="w-4 h-4 fill-current" viewBox="0 0 20 20">
                                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                            </svg>
                                        </div>
                                        <span class="text-gray-600 text-sm font-medium">4.9 (120)</span>
                                    </div>

                                    <!-- Price -->
                                    <div class="text-right">
                                        <p class="text-2xl font-bold text-emerald-600">$49</p>
                                        <p class="text-xs text-gray-500">/month</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </Link>

                    <!-- Course Card 2 - Tajweed Mastery -->
                    <Link href="/courses" class="course-card group">
                        <div class="bg-white rounded-3xl overflow-hidden shadow-xl hover:shadow-2xl transition-all duration-500 hover:-translate-y-2 border-2 border-gray-200">
                            <!-- Thumbnail with Overlay -->
                            <div class="relative h-48 overflow-hidden">
                                <div class="absolute inset-0 bg-gradient-to-br from-blue-500 to-indigo-600"></div>
                                <div class="absolute inset-0 flex items-center justify-center">
                                    <span class="text-7xl transform group-hover:scale-125 transition-transform duration-500">🎙️</span>
                                </div>
                                
                                <!-- Badges -->
                                <div class="absolute top-4 left-4 flex gap-2">
                                    <span class="px-3 py-1 text-xs font-bold rounded-full shadow-lg bg-gradient-to-r from-blue-400 to-indigo-500 text-white">
                                        Intermediate
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
                                <h3 class="text-xl font-bold text-gray-900 mb-2 group-hover:text-emerald-600 transition-colors">
                                    Tajweed Mastery
                                </h3>
                                <p class="text-gray-700 text-sm mb-4 font-medium">
                                    Master the rules of Quranic recitation with expert guidance.
                                </p>

                                <!-- Footer -->
                                <div class="flex items-center justify-between pt-4 border-t border-gray-100">
                                    <!-- Rating -->
                                    <div class="flex items-center gap-1">
                                        <div class="flex text-amber-400">
                                            <svg v-for="i in 5" :key="i" class="w-4 h-4 fill-current" viewBox="0 0 20 20">
                                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                            </svg>
                                        </div>
                                        <span class="text-gray-600 text-sm font-medium">4.8 (85)</span>
                                    </div>

                                    <!-- Price -->
                                    <div class="text-right">
                                        <p class="text-2xl font-bold text-emerald-600">$69</p>
                                        <p class="text-xs text-gray-500">/month</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </Link>

                    <!-- Course Card 3 - Quran Memorization (Hifz) -->
                    <Link href="/courses" class="course-card group">
                        <div class="bg-white rounded-3xl overflow-hidden shadow-xl hover:shadow-2xl transition-all duration-500 hover:-translate-y-2 border-2 border-gray-200">
                            <!-- Thumbnail with Overlay -->
                            <div class="relative h-48 overflow-hidden">
                                <div class="absolute inset-0 bg-gradient-to-br from-purple-500 to-pink-600"></div>
                                <div class="absolute inset-0 flex items-center justify-center">
                                    <span class="text-7xl transform group-hover:scale-125 transition-transform duration-500">🧠</span>
                                </div>
                                
                                <!-- Badges -->
                                <div class="absolute top-4 left-4 flex gap-2">
                                    <span class="px-3 py-1 text-xs font-bold rounded-full shadow-lg bg-gradient-to-r from-gray-400 to-gray-500 text-white">
                                        All Levels
                                    </span>
                                </div>
                                <div class="absolute top-4 right-4">
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
                                <h3 class="text-xl font-bold text-gray-900 mb-2 group-hover:text-emerald-600 transition-colors">
                                    Quran Memorization (Hifz)
                                </h3>
                                <p class="text-gray-700 text-sm mb-4 font-medium">
                                    Structured program to memorize the Holy Quran with expert support.
                                </p>

                                <!-- Footer -->
                                <div class="flex items-center justify-between pt-4 border-t border-gray-100">
                                    <!-- Rating -->
                                    <div class="flex items-center gap-1">
                                        <div class="flex text-amber-400">
                                            <svg v-for="i in 5" :key="i" class="w-4 h-4 fill-current" viewBox="0 0 20 20">
                                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                            </svg>
                                        </div>
                                        <span class="text-gray-600 text-sm font-medium">5.0 (65)</span>
                                    </div>

                                    <!-- Price -->
                                    <div class="text-right">
                                        <p class="text-2xl font-bold text-emerald-600">$99</p>
                                        <p class="text-xs text-gray-500">/month</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </Link>
                </div>
            </div>
        </section>

        <!-- Testimonials Section -->
        <section class="relative py-24 bg-gradient-to-br from-emerald-900 via-teal-800 to-emerald-900 text-white overflow-hidden">
            <!-- Islamic Pattern Overlay -->
            <div class="absolute inset-0 opacity-10">
                <div class="absolute inset-0" style="background-image: url('data:image/svg+xml,%3Csvg width=\'60\' height=\'60\' viewBox=\'0 0 60 60\' xmlns=\'http://www.w3.org/2000/svg\'%3E%3Cg fill=\'none\' fill-rule=\'evenodd\'%3E%3Cg fill=\'%23ffffff\' fill-opacity=\'0.4\'%3E%3Cpath d=\'M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z\'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E');"></div>
            </div>

            <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center max-w-3xl mx-auto mb-16">
                    <h2 class="text-3xl sm:text-4xl font-display font-bold mb-4">
                        What Our Students Say
                    </h2>
                    <p class="text-xl text-emerald-100">
                        Join thousands of satisfied students who have transformed their Quran journey.
                    </p>
                </div>

                <div class="grid md:grid-cols-3 gap-8">
                    <div
                        v-for="(testimonial, index) in testimonials"
                        :key="index"
                        class="bg-white/10 backdrop-blur-sm rounded-2xl p-8 border border-white/10 hover:bg-white/15 transition-colors"
                    >
                        <div class="flex text-amber-400 mb-4">
                            <svg v-for="i in testimonial.rating" :key="i" class="w-5 h-5 fill-current" viewBox="0 0 20 20">
                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                            </svg>
                        </div>
                        <p class="text-white/90 mb-6 leading-relaxed">"{{ testimonial.text }}"</p>
                        <div class="flex items-center">
                            <div class="w-12 h-12 rounded-full bg-emerald-500/50 flex items-center justify-center text-lg font-bold border border-white/20">
                                {{ testimonial.name.charAt(0) }}
                            </div>
                            <div class="ml-3">
                                <p class="font-semibold text-white">{{ testimonial.name }}</p>
                                <p class="text-sm text-emerald-200">{{ testimonial.location }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- CTA Section -->
        <section class="py-24 bg-white">
            <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
                <h2 class="text-3xl sm:text-4xl font-display font-bold text-gray-900 mb-6">
                    Ready to Start Your Quran Journey?
                </h2>
                <p class="text-xl text-gray-600 mb-8">
                    Join thousands of students learning Quran with qualified teachers. 
                    Start with a free trial class today.
                </p>
                <div class="flex flex-col sm:flex-row gap-4 justify-center">
                    <Link
                        href="/register"
                        class="inline-flex items-center justify-center px-8 py-4 bg-primary-500 hover:bg-primary-600 text-white text-lg font-semibold rounded-2xl transition-all hover:shadow-glow hover:-translate-y-0.5"
                    >
                        Start Free Trial
                        <svg class="ml-2 w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
                        </svg>
                    </Link>
                    <Link
                        href="/contact"
                        class="inline-flex items-center justify-center px-8 py-4 bg-white hover:bg-gray-50 text-gray-700 text-lg font-semibold rounded-2xl border-2 border-gray-200 transition-all hover:-translate-y-0.5"
                    >
                        Contact Us
                    </Link>
                </div>
            </div>
        </section>
    </PublicLayout>
</template>

<style scoped>
/* 3D Book Animation Styles */
.perspective-1000 {
    perspective: 1000px;
}

.book-container {
    width: 320px;
    height: 400px;
    position: relative;
    transform-style: preserve-3d;
}

.book {
    width: 100%;
    height: 100%;
    position: relative;
    transform-style: preserve-3d;
    animation: bookOpen 4s ease-out forwards;
    animation-delay: 0.5s;
}

@keyframes bookOpen {
    0% {
        transform: rotateY(0deg);
    }
    100% {
        transform: rotateY(-20deg);
    }
}

/* Book Spine */
.book-spine {
    position: absolute;
    width: 25px;
    height: 100%;
    left: 50%;
    transform: translateX(-50%) rotateY(90deg) translateZ(12px);
    background: linear-gradient(to right, #5d4037, #795548, #5d4037);
    border-radius: 2px;
}

/* Book Pages Base */
.book-page {
    position: absolute;
    width: 50%;
    height: 100%;
    transform-style: preserve-3d;
    transform-origin: left center;
    transition: transform 0.5s ease;
}

.page-content {
    width: 100%;
    height: 100%;
    border-radius: 0 8px 8px 0;
    box-shadow: 0 10px 30px rgba(0,0,0,0.3);
}

/* Left Page (Back Cover) - Opens to show inner page */
.book-page-left {
    left: 0;
    transform-origin: right center;
    animation: leftPageOpen 4s ease-out forwards;
    animation-delay: 0.5s;
}

.book-page-left .page-content {
    background: linear-gradient(135deg, #1b5e20 0%, #2e7d32 50%, #1b5e20 100%);
    border-radius: 8px 0 0 8px;
}

@keyframes leftPageOpen {
    0% {
        transform: rotateY(0deg);
    }
    100% {
        transform: rotateY(-160deg);
    }
}

/* Right Page (Front Cover) - Opens to show inner page */
.book-page-right {
    right: 0;
    left: auto;
    transform-origin: left center;
    animation: rightPageOpen 4s ease-out forwards;
    animation-delay: 0.5s;
}

.book-page-right .page-content {
    background: linear-gradient(135deg, #1b5e20 0%, #2e7d32 50%, #1b5e20 100%);
    border: 4px solid #c9a227;
}

@keyframes rightPageOpen {
    0% {
        transform: rotateY(0deg);
    }
    100% {
        transform: rotateY(160deg);
    }
}

/* Cover Design */
.cover-design {
    width: 100%;
    height: 100%;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 20px;
}

.cover-border {
    position: absolute;
    inset: 15px;
    border: 2px solid #c9a227;
    border-radius: 4px;
}

.cover-center {
    text-align: center;
}

.cover-medallion {
    width: 100px;
    height: 100px;
    border-radius: 50%;
    background: linear-gradient(135deg, #c9a227, #ffd54f, #c9a227);
    display: flex;
    align-items: center;
    justify-content: center;
    border: 3px solid #8b6914;
    box-shadow: 0 5px 15px rgba(0,0,0,0.3);
}

.cover-title {
    font-family: 'Amiri', serif;
    font-size: 1rem;
    color: #1b5e20;
    font-weight: bold;
}

/* Inner Pages */
.book-inner-left, .book-inner-right {
    width: 50%;
    height: 100%;
    position: absolute;
    top: 0;
    overflow: hidden; /* Clip overflowing text */
    transform-style: preserve-3d;
}

.book-inner-left {
    left: 0;
    transform-origin: right center;
    transform: rotateY(-35deg); /* More tilt for V-shape */
}

.book-inner-right {
    right: 0;
    left: 50%;
    transform-origin: left center;
    transform: rotateY(35deg); /* More tilt for V-shape */
}

.page-paper {
    background: linear-gradient(to bottom, #fefae0, #f5f1d9);
    border: 1px solid #d4c89a;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    padding: 30px;
}

.arabic-text {
    font-family: 'Amiri Quran', 'Scheherazade New', serif;
    font-size: 1.3rem;
    color: #1a1a1a;
    text-align: center;
    margin-bottom: 15px;
    line-height: 2;
}

.arabic-text-small {
    font-family: 'Amiri Quran', 'Scheherazade New', serif;
    font-size: 1rem;
    color: #333;
    text-align: center;
    line-height: 2;
}

/* --- Advanced 3D Hero Animation Styles --- */

.perspective-1000 {
    perspective: 1200px;
}

.floating-animation-container {
    position: relative;
    width: 300px;
    height: 300px;
    transform-style: preserve-3d;
    animation: divineGlowFloat 6s ease-in-out infinite;
}

/* Divine Cloud Base (Rising Light) */
.divine-cloud-base {
    position: absolute;
    bottom: -100px;
    width: 600px;
    height: 300px;
    background: radial-gradient(ellipse at center, rgba(255, 255, 255, 0.2) 0%, rgba(16, 185, 129, 0) 70%);
    filter: blur(40px);
    transform: rotateX(70deg);
    animation: cloudPulse 4s ease-in-out infinite;
    pointer-events: none;
}

/* Light Shafts */
.light-shafts {
    position: absolute;
    inset: -100px;
    background: conic-gradient(from 0deg, transparent 0deg, rgba(234, 179, 8, 0.1) 20deg, transparent 40deg, rgba(255, 255, 255, 0.1) 60deg, transparent 360deg);
    transform: translateY(-50px) scale(1.5);
    filter: blur(10px);
    opacity: 0;
    animation: shaftsRotate 20s linear infinite;
}

/* Rehal (Book Stand) */
.rehal-stand {
    position: absolute;
    bottom: 0;
    width: 260px;
    height: 180px;
    left: 20px;
    transform-style: preserve-3d;
    transform: rotateX(20deg);
}

.rehal-leg {
    position: absolute;
    width: 140px;
    height: 200px;
    background-color: #1a1a1a; /* Dark Charcoal Base */
    /* Gold Geometric Lattice Pattern */
    background-image: 
        linear-gradient(45deg, rgba(218, 165, 32, 0.4) 25%, transparent 25%, transparent 75%, rgba(218, 165, 32, 0.4) 75%, rgba(218, 165, 32, 0.4)), 
        linear-gradient(-45deg, rgba(218, 165, 32, 0.4) 25%, transparent 25%, transparent 75%, rgba(218, 165, 32, 0.4) 75%, rgba(218, 165, 32, 0.4));
    background-size: 24px 24px;
    background-position: 0 0, 12px 12px;
    
    border: 3px solid #FFD700; /* Bright Gold Border */
    border-radius: 8px;
    box-shadow: 
        inset 0 0 40px #000, /* Deep inner depth */
        0 8px 16px rgba(0,0,0,0.6); /* Outer shadow */
}

.rehal-leg-left {
    transform: rotateZ(45deg) skewX(10deg);
    left: 40px;
}

.rehal-leg-right {
    transform: rotateZ(-45deg) skewX(-10deg);
    right: 40px;
}

/* 3D Quran Book */
.quran-book-3d {
    position: absolute;
    top: -60px;
    left: 50%;
    transform: translateX(-50%);
    width: 340px;
    height: 260px;
    display: flex;
    align-items: flex-start;
    justify-content: center;
    transform-style: preserve-3d;
}

/* Left and Right Book Sides */
.book-left-side, .book-right-side {
    display: flex;
    height: 240px;
    transform-style: preserve-3d;
}

.book-left-side {
    transform-origin: right center;
    transform: rotateY(-15deg); /* Reduced for natural look */
}

.book-right-side {
    transform-origin: left center;
    transform: rotateY(15deg); /* Reduced for natural look */
}

/* Dark Cover Edges */
.cover-edge {
    width: 15px;
    height: 100%;
    background: linear-gradient(to bottom, #2D1810, #1a0f0a);
    border: 1px solid #3D2820;
}

.cover-edge-left {
    border-radius: 4px 0 0 4px;
    box-shadow: -3px 0 8px rgba(0,0,0,0.4);
}

.cover-edge-right {
    border-radius: 0 4px 4px 0;
    box-shadow: 3px 0 8px rgba(0,0,0,0.4);
}

/* Page with Gold Border */
.page-with-border {
    width: 140px;
    height: 100%;
    background: #F5EFE0; /* Cream */
    display: flex;
    align-items: center;
    justify-content: center;
    box-shadow: 0 5px 15px rgba(0,0,0,0.2);
}

.gold-frame {
    width: 90%;
    height: 90%;
    border: 2px solid #DAA520;
    border-radius: 4px;
    padding: 10px;
    position: relative;
}

/* Gold corner dots */
.gold-frame::before,
.gold-frame::after {
    content: '';
    position: absolute;
    width: 6px;
    height: 6px;
    background: #DAA520;
    border-radius: 50%;
}

.gold-frame::before {
    top: 4px;
    left: 4px;
}

.gold-frame::after {
    top: 4px;
    right: 4px;
}

.page-lines {
    display: flex;
    flex-direction: column;
    gap: 6px;
    overflow: hidden;
}

.arabic-text-line {
    font-family: 'Amiri', 'Amiri Quran', serif;
    font-size: 15px;
    color: #333;
    text-align: center;
    line-height: 1.6;
    margin: 0;
}

/* Center Spine - Red with Gold Dots */
.book-spine-center {
    width: 20px;
    height: 240px;
    background: linear-gradient(to bottom, #8B1A1A, #6B0F0F, #8B1A1A);
    border-left: 2px solid #DAA520;
    border-right: 2px solid #DAA520;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: space-evenly;
    padding: 15px 0;
    box-shadow: 
        inset 0 0 10px rgba(0,0,0,0.3),
        0 5px 15px rgba(0,0,0,0.3);
    z-index: 10;
}

.spine-dot {
    width: 8px;
    height: 8px;
    background: radial-gradient(circle, #FFD700 40%, #DAA520 100%);
    border-radius: 50%;
    box-shadow: 0 1px 3px rgba(0,0,0,0.3);
}

/* Pages */
.pages-container {
    position: absolute;
    width: 300px;
    height: 220px;
    transform-style: preserve-3d;
}

.turning-pages {
    position: absolute;
    width: 300px;
    height: 220px;
    transform-style: preserve-3d;
    transform-style: preserve-3d;
    z-index: 20;
}

/* Book Spine Binding - Center tilts down */
.book-spine-binding {
    position: absolute;
    top: 0;
    left: 50%;
    transform: translateX(-50%) rotateX(45deg);
    width: 20px;
    height: 220px;
    background: linear-gradient(to right, #8B4513 0%, #A0522D 50%, #8B4513 100%);
    border-left: 2px solid #DAA520;
    border-right: 2px solid #DAA520;
    z-index: 25;
    box-shadow: 
        inset 0 0 10px rgba(0,0,0,0.5),
        0 5px 15px rgba(0,0,0,0.3);
}

/* Old spine cover - keep for reference */
.book-spine-cover {
    display: none; /* Hidden - replaced by book-spine-binding */
}

.page-turn {
    position: absolute;
    top: 5px;
    left: 50%; /* Pivot from center */
    width: 142px; /* Slight overlap */
    height: 210px;
    height: 210px;
    transform-style: preserve-3d;
    transform-origin: left center;
    /* CSS Animation for Page Turning Loop */
    animation: turnPage 5s infinite cubic-bezier(0.645, 0.045, 0.355, 1);
    animation-delay: calc(var(--index) * 0.5s);
}

@keyframes turnPage {
    0% { transform: rotateY(0deg); }
    15% { transform: rotateY(-30deg); }
    50% { transform: rotateY(-180deg); } /* Flip completely */
    100% { transform: rotateY(-180deg); }
}

.page-front, .page-back {
    position: absolute;
    width: 100%;
    height: 100%;
    background: #FFFBF0; /* Cream paper */
    border: 1px solid #E5E7EB;
    backface-visibility: hidden;
    display: flex;
    align-items: center;
    justify-content: center;
    overflow: hidden; /* Clip text outside page */
}

.page-back {
    transform: rotateY(180deg);
    background: #FAFAF9;
}

.page-content-mock {
    width: 80%;
    height: 80%;
    display: flex;
    flex-direction: column;
    gap: 8px;
    opacity: 0.6;
}

.line {
    width: 100%;
    height: 2px;
    background: #D1D5DB;
    border-radius: 2px;
}

/* Animations */
@keyframes divineGlowFloat {
    0%, 100% { transform: translateY(0); }
    50% { transform: translateY(-20px); }
}

@keyframes cloudPulse {
    0%, 100% { opacity: 0.6; transform: scale(1) rotateX(70deg); }
    50% { opacity: 0.9; transform: scale(1.2) rotateX(70deg); }
}

@keyframes shaftsRotate {
    from { transform: rotate(0deg) scale(1.5); }
    to { transform: rotate(360deg) scale(1.5); }
}

/* Animation Utilities */
.animate-fade-in {
    animation: fadeIn 1s ease-out forwards;
}

@keyframes fadeIn {
    from {
        opacity: 0;
        transform: translateY(20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

/* Ensure all animated elements are visible */
.feature-card,
.stat-item,
.course-card,
.testimonial-card,
.pricing-card {
    opacity: 1 !important;
    visibility: visible !important;
}

/* Smooth entrance animations using CSS */
.feature-card {
    animation: slideUp 0.5s ease-out forwards;
    animation-delay: calc(var(--index, 0) * 0.1s);
}

.stat-item {
    animation: slideUp 0.4s ease-out forwards;
    animation-delay: calc(var(--index, 0) * 0.08s);
}

@keyframes slideUp {
    from {
        opacity: 0.5;
        transform: translateY(20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}
</style>
