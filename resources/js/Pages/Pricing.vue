<script setup>
import { Head, Link } from '@inertiajs/vue3';
import { ref, onMounted } from 'vue';
import PublicLayout from '@/Layouts/PublicLayout.vue';
import MetaTags from '@/Components/SEO/MetaTags.vue';
import { seoConfig } from '@/utils/seo.js';
import gsap from 'gsap';

defineProps({
    courses: {
        type: Array,
        default: () => []
    }
});

const billingCycle = ref('monthly');

const plans = [
    {
        name: 'Starter',
        description: 'Perfect for beginners starting their Quran journey',
        monthlyPrice: 49,
        yearlyPrice: 470,
        icon: '🌱',
        gradient: 'from-emerald-400 to-teal-500',
        features: [
            'Quran Reading Basics course',
            '2 group classes per week',
            '45-minute sessions',
            'Access to class recordings',
            'Basic learning resources',
            'Progress tracking',
            'Community access',
        ],
        notIncluded: [
            'Private 1-on-1 classes',
            'Hifz program',
            'Certificate of completion',
        ],
        popular: false,
    },
    {
        name: 'Standard',
        description: 'Most popular choice for serious learners',
        monthlyPrice: 79,
        yearlyPrice: 758,
        icon: '⭐',
        gradient: 'from-blue-500 to-indigo-600',
        features: [
            'All Starter features',
            'Tajweed Mastery course',
            '3 group classes per week',
            '60-minute sessions',
            'All learning resources',
            '1 private session/month',
            'Certificate of completion',
            'Priority support',
        ],
        notIncluded: [
            'Unlimited private classes',
            'Full Hifz program',
        ],
        popular: true,
    },
    {
        name: 'Premium',
        description: 'Full access for dedicated students',
        monthlyPrice: 149,
        yearlyPrice: 1430,
        icon: '👑',
        gradient: 'from-amber-500 to-orange-600',
        features: [
            'All Standard features',
            'Hifz Program access',
            '5 classes per week',
            '2 private sessions/month',
            'Personalized learning path',
            'Arabic language course',
            'Dedicated teacher support',
            'Family discount (20%)',
        ],
        notIncluded: [],
        popular: false,
    },
    {
        name: 'Private',
        description: 'Personalized 1-on-1 learning experience',
        monthlyPrice: 199,
        yearlyPrice: 1910,
        icon: '💎',
        gradient: 'from-purple-500 to-pink-600',
        features: [
            'Unlimited private sessions',
            'Flexible scheduling',
            'Personalized curriculum',
            'All courses included',
            'Recorded sessions access',
            'Direct teacher communication',
            'Family sharing (2 members)',
            'VIP support',
        ],
        notIncluded: [],
        popular: false,
    },
];

const faqs = [
    {
        question: 'Can I try before I subscribe?',
        answer: 'Yes! We offer a free trial class so you can experience our teaching style before committing to a subscription.',
    },
    {
        question: 'What if I miss a class?',
        answer: 'All classes are recorded and available for 30 days. You can watch the recording anytime to catch up.',
    },
    {
        question: 'Can I change my plan?',
        answer: 'Absolutely! You can upgrade or downgrade your plan at any time. Changes take effect at your next billing cycle.',
    },
    {
        question: 'Is there a family discount?',
        answer: 'Yes, Premium members get 20% off for additional family members, and Private plan includes family sharing.',
    },
    {
        question: 'What payment methods do you accept?',
        answer: 'We accept all major credit cards (Visa, Mastercard, Amex), PayPal, and local payment methods like bKash for Bangladesh.',
    },
    {
        question: 'What is your refund policy?',
        answer: 'We offer a 7-day money-back guarantee. If you\'re not satisfied within the first week, we\'ll refund your payment.',
    },
];

const formatPrice = (price) => {
    return new Intl.NumberFormat('en-US', {
        style: 'currency',
        currency: 'USD',
        minimumFractionDigits: 0,
    }).format(price);
};

const getPrice = (plan) => {
    return billingCycle.value === 'monthly' ? plan.monthlyPrice : plan.yearlyPrice;
};

const getMonthlyEquivalent = (plan) => {
    return billingCycle.value === 'yearly' ? Math.round(plan.yearlyPrice / 12) : null;
};

const getSavings = (plan) => {
    return plan.monthlyPrice * 12 - plan.yearlyPrice;
};

// GSAP Animations
onMounted(() => {
    gsap.from('.hero-content', {
        y: 50,
        opacity: 0,
        duration: 1,
        ease: 'power3.out'
    });

    gsap.from('.pricing-card', {
        y: 60,
        opacity: 0,
        duration: 0.7,
        stagger: 0.15,
        delay: 0.3,
        ease: 'power2.out'
    });

    gsap.from('.faq-item', {
        y: 30,
        opacity: 0,
        duration: 0.5,
        stagger: 0.1,
        delay: 0.5,
        ease: 'power2.out'
    });

    // Floating animation for cards
    gsap.to('.pricing-card:nth-child(2)', {
        y: -10,
        duration: 2,
        repeat: -1,
        yoyo: true,
        ease: 'power1.inOut'
    });
});
</script>

<template>
    <PublicLayout>
        <!-- SEO Meta Tags -->
        <MetaTags 
            :title="seoConfig.pricing.title"
            :description="seoConfig.pricing.description"
            :keywords="seoConfig.pricing.keywords"
        />
        
        <Head title="Pricing | QuranLearn - Affordable Quran Learning" />
        <!-- Hero Section -->
        <section class="relative pt-28 pb-16 overflow-hidden bg-gradient-to-br from-emerald-900 via-teal-800 to-emerald-900">
            <!-- Islamic Pattern -->
            <div class="absolute inset-0 opacity-10">
                <div class="absolute inset-0" style="background-image: url('data:image/svg+xml,%3Csvg width=\'60\' height=\'60\' viewBox=\'0 0 60 60\' xmlns=\'http://www.w3.org/2000/svg\'%3E%3Cg fill=\'none\' fill-rule=\'evenodd\'%3E%3Cg fill=\'%23ffffff\' fill-opacity=\'0.4\'%3E%3Cpath d=\'M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z\'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E');"></div>
            </div>

            <!-- Floating decorations -->
            <div class="absolute top-20 left-10 w-64 h-64 bg-gold-400/10 rounded-full blur-3xl"></div>
            <div class="absolute bottom-10 right-20 w-48 h-48 bg-emerald-400/10 rounded-full blur-3xl"></div>

            <div class="hero-content relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
                <span class="inline-flex items-center px-4 py-2 bg-white/10 backdrop-blur-sm rounded-full text-white text-sm font-medium mb-6">
                    💰 Save 20% with yearly billing
                </span>
                <h1 class="text-4xl sm:text-5xl lg:text-6xl font-display font-bold text-white mb-6">
                    Simple, <span class="text-transparent bg-clip-text bg-gradient-to-r from-gold-400 to-amber-300">Transparent</span> Pricing
                </h1>
                <p class="text-xl text-emerald-100 max-w-2xl mx-auto mb-10">
                    Choose the perfect plan for your Quran learning journey. All plans include access to qualified teachers and our learning platform.
                </p>

                <!-- Billing Toggle -->
                <div class="inline-flex items-center bg-white/10 backdrop-blur-sm rounded-2xl p-1.5 border border-white/20">
                    <button
                        @click="billingCycle = 'monthly'"
                        :class="[
                            'px-8 py-3 rounded-xl text-sm font-semibold transition-all',
                            billingCycle === 'monthly'
                                ? 'bg-white text-emerald-700 shadow-lg'
                                : 'text-white/70 hover:text-white'
                        ]"
                    >
                        Monthly
                    </button>
                    <button
                        @click="billingCycle = 'yearly'"
                        :class="[
                            'px-8 py-3 rounded-xl text-sm font-semibold transition-all flex items-center gap-2',
                            billingCycle === 'yearly'
                                ? 'bg-white text-emerald-700 shadow-lg'
                                : 'text-white/70 hover:text-white'
                        ]"
                    >
                        Yearly
                        <span class="px-2 py-0.5 bg-gradient-to-r from-green-400 to-emerald-500 text-white text-xs rounded-full">-20%</span>
                    </button>
                </div>
            </div>

            <!-- Wave -->
            <div class="absolute bottom-0 left-0 right-0">
                <svg viewBox="0 0 1440 120" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M0 120L60 110C120 100 240 80 360 70C480 60 600 60 720 65C840 70 960 80 1080 85C1200 90 1320 90 1380 90L1440 90V120H1380C1320 120 1200 120 1080 120C960 120 840 120 720 120C600 120 480 120 360 120C240 120 120 120 60 120H0Z" fill="white"/>
                </svg>
            </div>
        </section>

        <!-- Pricing Cards -->
        <section class="py-16 bg-white -mt-1">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="grid md:grid-cols-2 lg:grid-cols-4 gap-6">
                    <div
                        v-for="(plan, index) in plans"
                        :key="index"
                        :class="[
                            'pricing-card relative rounded-3xl p-6 transition-all duration-500 hover:scale-105',
                            plan.popular
                                ? 'bg-gradient-to-br from-emerald-600 to-teal-700 text-white shadow-2xl ring-4 ring-emerald-500/50 lg:-mt-4 lg:mb-4'
                                : 'bg-white border-2 border-gray-200 shadow-lg hover:border-emerald-400 hover:shadow-2xl'
                        ]"
                    >
                        <!-- Popular Badge -->
                        <div v-if="plan.popular" class="absolute -top-4 left-1/2 -translate-x-1/2">
                            <span class="bg-gradient-to-r from-amber-400 to-orange-500 text-white text-xs font-bold px-6 py-2 rounded-full shadow-lg animate-pulse">
                                🔥 MOST POPULAR
                            </span>
                        </div>

                        <!-- Plan Icon & Header -->
                        <div class="mb-6 text-center">
                            <div :class="['inline-flex items-center justify-center w-16 h-16 rounded-2xl text-3xl mb-4', plan.popular ? 'bg-white/20' : 'bg-gradient-to-br ' + plan.gradient + ' text-white shadow-lg']">
                                {{ plan.icon }}
                            </div>
                            <h3 :class="['text-2xl font-bold mb-2', plan.popular ? 'text-white' : 'text-gray-900']">
                                {{ plan.name }}
                            </h3>
                            <p :class="['text-sm', plan.popular ? 'text-emerald-100' : 'text-gray-700']">
                                {{ plan.description }}
                            </p>
                        </div>

                        <!-- Price -->
                        <div class="mb-6 text-center">
                            <div class="flex items-baseline justify-center">
                                <span :class="['text-5xl font-bold', plan.popular ? 'text-white' : 'text-gray-900']">
                                    {{ formatPrice(getPrice(plan)) }}
                                </span>
                                <span :class="['ml-2', plan.popular ? 'text-emerald-100' : 'text-gray-500']">
                                    /{{ billingCycle === 'monthly' ? 'mo' : 'yr' }}
                                </span>
                            </div>
                            <p v-if="billingCycle === 'yearly'" :class="['text-sm mt-2', plan.popular ? 'text-emerald-100' : 'text-green-600']">
                                Save {{ formatPrice(getSavings(plan)) }}/year
                            </p>
                        </div>

                        <!-- CTA Button -->
                        <Link
                            href="/register"
                            :class="[
                                'block w-full py-4 text-center font-bold rounded-xl transition-all mb-6',
                                plan.popular
                                    ? 'bg-white text-emerald-700 hover:bg-emerald-50 hover:scale-105 shadow-lg'
                                    : 'bg-gradient-to-r from-emerald-500 to-teal-600 text-white hover:from-emerald-600 hover:to-teal-700 hover:shadow-lg'
                            ]"
                        >
                            Get Started →
                        </Link>

                        <!-- Features -->
                        <ul class="space-y-3">
                            <li
                                v-for="(feature, i) in plan.features"
                                :key="i"
                                class="flex items-start"
                            >
                                <svg :class="['w-5 h-5 mr-3 flex-shrink-0', plan.popular ? 'text-emerald-200' : 'text-emerald-500']" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                </svg>
                                <span :class="['text-sm font-medium', plan.popular ? 'text-white' : 'text-gray-800']">
                                    {{ feature }}
                                </span>
                            </li>
                            <li
                                v-for="(feature, i) in plan.notIncluded"
                                :key="`not-${i}`"
                                class="flex items-start opacity-50"
                            >
                                <svg class="w-5 h-5 mr-3 flex-shrink-0 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                                <span :class="['text-sm line-through', plan.popular ? 'text-emerald-200' : 'text-gray-400']">
                                    {{ feature }}
                                </span>
                            </li>
                        </ul>
                    </div>
                </div>

                <!-- Money Back Guarantee -->
                <div class="mt-16 text-center">
                    <div class="inline-flex items-center px-8 py-4 bg-gradient-to-r from-green-50 to-emerald-50 rounded-2xl border border-green-200 shadow-lg">
                        <span class="text-4xl mr-4">🛡️</span>
                        <div class="text-left">
                            <p class="text-green-700 font-bold text-lg">7-Day Money-Back Guarantee</p>
                            <p class="text-green-600 text-sm">Not satisfied? Get a full refund, no questions asked.</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- FAQ Section -->
        <section class="py-24 bg-gradient-to-br from-gray-50 to-white">
            <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center mb-12">
                    <span class="inline-flex items-center px-4 py-2 bg-emerald-100 text-emerald-700 rounded-full text-sm font-medium mb-4">
                        ❓ Got Questions?
                    </span>
                    <h2 class="text-3xl sm:text-4xl font-display font-bold text-gray-900">
                        Frequently Asked Questions
                    </h2>
                </div>

                <div class="space-y-4">
                    <details
                        v-for="(faq, index) in faqs"
                        :key="index"
                        class="faq-item bg-white rounded-2xl border border-gray-200 overflow-hidden group hover:shadow-lg transition-shadow"
                    >
                        <summary class="flex items-center justify-between p-6 cursor-pointer hover:bg-gray-50 transition-colors">
                            <span class="font-semibold text-gray-900 pr-4">{{ faq.question }}</span>
                            <span class="flex-shrink-0 w-8 h-8 bg-emerald-100 rounded-full flex items-center justify-center group-open:bg-emerald-500 transition-colors">
                                <svg class="w-4 h-4 text-emerald-600 group-open:text-white transform group-open:rotate-180 transition-all" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                </svg>
                            </span>
                        </summary>
                        <div class="px-6 pb-6 pt-2">
                            <p class="text-gray-600 leading-relaxed">{{ faq.answer }}</p>
                        </div>
                    </details>
                </div>
            </div>
        </section>

        <!-- CTA Section -->
        <section class="py-24 bg-gradient-to-b from-emerald-900 via-emerald-800 to-teal-900 relative overflow-hidden">
            <div class="absolute inset-0 opacity-10">
                <div class="absolute inset-0" style="background-image: url('data:image/svg+xml,%3Csvg width=\'60\' height=\'60\' viewBox=\'0 0 60 60\' xmlns=\'http://www.w3.org/2000/svg\'%3E%3Cg fill=\'none\'%3E%3Cg fill=\'%23ffffff\' fill-opacity=\'0.4\'%3E%3Cpath d=\'M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z\'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E');"></div>
            </div>
            <div class="relative max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
                <div class="inline-flex items-center justify-center w-20 h-20 bg-white/20 rounded-3xl mb-8">
                    <span class="text-5xl">📖</span>
                </div>
                <h2 class="text-3xl sm:text-4xl font-display font-bold text-white mb-6">
                    Ready to Start Your Quran Journey?
                </h2>
                <p class="text-xl text-emerald-100 mb-10">
                    Join thousands of students learning Quran with qualified teachers. Start your free trial today!
                </p>
                <div class="flex flex-col sm:flex-row gap-4 justify-center">
                    <Link
                        href="/register"
                        class="inline-flex items-center justify-center px-10 py-5 bg-white text-emerald-700 text-lg font-bold rounded-2xl transition-all hover:shadow-2xl hover:scale-105"
                    >
                        Start Free Trial →
                    </Link>
                    <Link
                        href="/contact"
                        class="inline-flex items-center justify-center px-10 py-5 border-2 border-white/30 text-white text-lg font-semibold rounded-2xl transition-all hover:bg-white/10"
                    >
                        📞 Contact Sales
                    </Link>
                </div>
            </div>
        </section>
    </PublicLayout>
</template>

<style scoped>
details[open] summary ~ * {
    animation: slideDown 0.3s ease-out;
}

@keyframes slideDown {
    from {
        opacity: 0;
        transform: translateY(-10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

/* Ensure pricing cards are always visible */
.pricing-card {
    opacity: 1 !important;
    visibility: visible !important;
    transform: translateY(0) !important;
}

.faq-item {
    opacity: 1 !important;
    visibility: visible !important;
}

.hero-content {
    opacity: 1 !important;
    visibility: visible !important;
}
</style>
