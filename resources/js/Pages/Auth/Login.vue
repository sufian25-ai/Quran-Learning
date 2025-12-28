<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import { onMounted } from 'vue';
import gsap from 'gsap';

defineProps({
    canResetPassword: {
        type: Boolean,
    },
    status: {
        type: String,
    },
});

const form = useForm({
    email: '',
    password: '',
    remember: false,
});

const submit = () => {
    form.post(route('login'), {
        onFinish: () => form.reset('password'),
    });
};

// GSAP Animations
onMounted(() => {
    // Animate left panel
    gsap.from('.left-panel-content', {
        x: -50,
        opacity: 0,
        duration: 1,
        ease: 'power3.out'
    });

    // Animate features list
    gsap.from('.feature-item', {
        x: -30,
        opacity: 0,
        duration: 0.6,
        stagger: 0.15,
        delay: 0.3,
        ease: 'power2.out'
    });

    // Animate form
    gsap.from('.login-form', {
        y: 30,
        opacity: 0,
        duration: 0.8,
        delay: 0.2,
        ease: 'power2.out'
    });

    // Animate decorative circles
    gsap.to('.floating-circle', {
        y: -20,
        duration: 3,
        repeat: -1,
        yoyo: true,
        ease: 'power1.inOut',
        stagger: 0.5
    });

    // Particle animations
    gsap.to('.particle', {
        y: -100,
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
    <Head title="Login | QuranLearn" />

    <div class="min-h-screen flex">
        <!-- Left Side - Decorative with Enhanced Design -->
        <div class="hidden lg:flex lg:w-1/2 bg-gradient-to-br from-emerald-800 via-teal-700 to-emerald-900 relative overflow-hidden">
            <!-- Islamic Pattern Background -->
            <div class="absolute inset-0 opacity-10">
                <div class="absolute inset-0" style="background-image: url('data:image/svg+xml,%3Csvg width=\'60\' height=\'60\' viewBox=\'0 0 60 60\' xmlns=\'http://www.w3.org/2000/svg\'%3E%3Cg fill=\'none\' fill-rule=\'evenodd\'%3E%3Cg fill=\'%23ffffff\' fill-opacity=\'0.4\'%3E%3Cpath d=\'M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z\'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E');"></div>
            </div>

            <!-- Floating Particles -->
            <div class="absolute inset-0 pointer-events-none">
                <div v-for="n in 15" :key="n" class="particle absolute w-2 h-2 bg-gold-400 rounded-full opacity-30" 
                    :style="{ left: Math.random() * 100 + '%', top: (50 + Math.random() * 50) + '%' }"></div>
            </div>

            <!-- Decorative Floating Circles -->
            <div class="floating-circle absolute bottom-20 left-10 w-64 h-64 bg-white/5 rounded-full blur-xl"></div>
            <div class="floating-circle absolute top-32 right-20 w-48 h-48 bg-gold-400/10 rounded-full blur-xl"></div>
            <div class="floating-circle absolute top-1/2 left-1/3 w-32 h-32 bg-emerald-400/10 rounded-full blur-xl"></div>
            
            <!-- Content -->
            <div class="left-panel-content relative z-10 flex flex-col justify-center px-12 xl:px-20">
                <!-- Logo -->
                <div class="mb-10">
                    <Link href="/" class="flex items-center space-x-3 group">
                        <div class="w-14 h-14 bg-gradient-to-br from-gold-400 to-amber-500 rounded-2xl flex items-center justify-center shadow-lg group-hover:scale-110 transition-transform">
                            <span class="text-3xl">📖</span>
                        </div>
                        <span class="text-2xl font-display font-bold text-white">QuranLearn</span>
                    </Link>
                </div>

                <h1 class="text-4xl xl:text-5xl font-display font-bold text-white leading-tight mb-6">
                    Continue Your<br/>
                    <span class="text-transparent bg-clip-text bg-gradient-to-r from-gold-400 to-amber-300">Quranic Journey</span>
                </h1>
                
                <p class="text-lg text-emerald-100 mb-10 max-w-md">
                    Welcome back! Sign in to access your courses, track your progress, and continue learning.
                </p>

                <!-- Features -->
                <div class="space-y-5">
                    <div class="feature-item flex items-center text-white/90 bg-white/5 backdrop-blur-sm rounded-xl px-4 py-3">
                        <span class="w-10 h-10 rounded-full bg-gradient-to-br from-emerald-400 to-teal-500 flex items-center justify-center mr-4 text-lg shadow-lg">📚</span>
                        <span>Access all your enrolled courses</span>
                    </div>
                    <div class="feature-item flex items-center text-white/90 bg-white/5 backdrop-blur-sm rounded-xl px-4 py-3">
                        <span class="w-10 h-10 rounded-full bg-gradient-to-br from-blue-400 to-indigo-500 flex items-center justify-center mr-4 text-lg shadow-lg">📊</span>
                        <span>Track your learning progress</span>
                    </div>
                    <div class="feature-item flex items-center text-white/90 bg-white/5 backdrop-blur-sm rounded-xl px-4 py-3">
                        <span class="w-10 h-10 rounded-full bg-gradient-to-br from-amber-400 to-orange-500 flex items-center justify-center mr-4 text-lg shadow-lg">🏆</span>
                        <span>Earn certificates & achievements</span>
                    </div>
                </div>

                <!-- Stats -->
                <div class="mt-12 flex gap-8">
                    <div class="text-center">
                        <p class="text-3xl font-bold text-white">5000+</p>
                        <p class="text-sm text-emerald-200">Students</p>
                    </div>
                    <div class="text-center">
                        <p class="text-3xl font-bold text-white">50+</p>
                        <p class="text-sm text-emerald-200">Teachers</p>
                    </div>
                    <div class="text-center">
                        <p class="text-3xl font-bold text-white">4.9</p>
                        <p class="text-sm text-emerald-200">Rating</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Side - Login Form -->
        <div class="flex-1 flex items-center justify-center p-8 bg-gradient-to-br from-gray-50 to-white relative overflow-hidden">
            <!-- Background Decoration -->
            <div class="absolute top-0 right-0 w-96 h-96 bg-emerald-100 rounded-full -translate-y-1/2 translate-x-1/2 opacity-50 blur-3xl"></div>
            <div class="absolute bottom-0 left-0 w-64 h-64 bg-gold-100 rounded-full translate-y-1/2 -translate-x-1/2 opacity-50 blur-3xl"></div>

            <div class="w-full max-w-md relative z-10">
                <!-- Mobile Logo -->
                <div class="lg:hidden text-center mb-8">
                    <Link href="/" class="inline-flex items-center space-x-3">
                        <div class="w-12 h-12 bg-gradient-to-br from-emerald-500 to-teal-600 rounded-xl flex items-center justify-center shadow-lg">
                            <span class="text-2xl">📖</span>
                        </div>
                        <span class="text-xl font-display font-bold text-gray-900">QuranLearn</span>
                    </Link>
                </div>

                <div class="login-form bg-white rounded-3xl shadow-2xl p-8 border border-gray-100">
                    <div class="text-center mb-8">
                        <div class="inline-flex items-center justify-center w-16 h-16 bg-gradient-to-br from-emerald-500 to-teal-600 rounded-2xl mb-4 shadow-lg">
                            <span class="text-3xl">👋</span>
                        </div>
                        <h2 class="text-2xl font-display font-bold text-gray-900">Welcome Back!</h2>
                        <p class="text-gray-500 mt-2">Sign in to continue your learning</p>
                    </div>

                    <div v-if="status" class="mb-4 font-medium text-sm text-green-600 bg-green-50 p-4 rounded-xl border border-green-100">
                        {{ status }}
                    </div>

                    <form @submit.prevent="submit" class="space-y-6">
                        <div class="group">
                            <label for="email" class="block text-sm font-medium text-gray-700 mb-2">
                                Email Address
                            </label>
                            <div class="relative">
                                <span class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-400">📧</span>
                                <input
                                    id="email"
                                    type="email"
                                    v-model="form.email"
                                    required
                                    autofocus
                                    autocomplete="username"
                                    class="w-full pl-12 pr-4 py-4 rounded-xl border-2 border-gray-200 focus:border-emerald-500 focus:ring-4 focus:ring-emerald-500/20 transition-all bg-gray-50 focus:bg-white"
                                    placeholder="you@example.com"
                                />
                            </div>
                            <p v-if="form.errors.email" class="mt-2 text-sm text-red-500">
                                {{ form.errors.email }}
                            </p>
                        </div>

                        <div class="group">
                            <label for="password" class="block text-sm font-medium text-gray-700 mb-2">
                                Password
                            </label>
                            <div class="relative">
                                <span class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-400">🔒</span>
                                <input
                                    id="password"
                                    type="password"
                                    v-model="form.password"
                                    required
                                    autocomplete="current-password"
                                    class="w-full pl-12 pr-4 py-4 rounded-xl border-2 border-gray-200 focus:border-emerald-500 focus:ring-4 focus:ring-emerald-500/20 transition-all bg-gray-50 focus:bg-white"
                                    placeholder="••••••••"
                                />
                            </div>
                            <p v-if="form.errors.password" class="mt-2 text-sm text-red-500">
                                {{ form.errors.password }}
                            </p>
                        </div>

                        <div class="flex items-center justify-between">
                            <label class="flex items-center cursor-pointer group">
                                <input
                                    type="checkbox"
                                    v-model="form.remember"
                                    class="w-5 h-5 rounded border-gray-300 text-emerald-500 focus:ring-emerald-500"
                                />
                                <span class="ml-2 text-sm text-gray-600 group-hover:text-gray-900 transition-colors">Remember me</span>
                            </label>

                            <Link
                                v-if="canResetPassword"
                                :href="route('password.request')"
                                class="text-sm text-emerald-600 hover:text-emerald-700 font-medium"
                            >
                                Forgot password?
                            </Link>
                        </div>

                        <button
                            type="submit"
                            :disabled="form.processing"
                            class="w-full py-4 bg-gradient-to-r from-emerald-500 to-teal-600 hover:from-emerald-600 hover:to-teal-700 text-white font-semibold rounded-xl transition-all hover:shadow-xl hover:scale-[1.02] disabled:opacity-50 disabled:cursor-not-allowed disabled:hover:scale-100"
                        >
                            <span v-if="form.processing" class="flex items-center justify-center">
                                <svg class="animate-spin h-5 w-5 mr-2" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" fill="none"/>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                                </svg>
                                Signing in...
                            </span>
                            <span v-else>Sign In →</span>
                        </button>
                    </form>

                    <!-- Divider -->
                    <div class="relative my-8">
                        <div class="absolute inset-0 flex items-center">
                            <div class="w-full border-t border-gray-200"></div>
                        </div>
                        <div class="relative flex justify-center text-sm">
                            <span class="px-4 bg-white text-gray-500">New to QuranLearn?</span>
                        </div>
                    </div>

                    <Link 
                        :href="route('register')" 
                        class="block w-full py-4 text-center border-2 border-emerald-500 text-emerald-600 hover:bg-emerald-50 font-semibold rounded-xl transition-all hover:scale-[1.02]"
                    >
                        Create Free Account
                    </Link>
                </div>

                <!-- Footer -->
                <p class="text-center text-sm text-gray-400 mt-8">
                    © {{ new Date().getFullYear() }} QuranLearn. All rights reserved.
                </p>
            </div>
        </div>
    </div>
</template>

<style scoped>
/* Smooth hover transitions */
input:focus {
    transform: translateY(-1px);
}
</style>
