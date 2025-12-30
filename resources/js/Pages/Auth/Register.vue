<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import { ref, computed, onMounted } from 'vue';
import gsap from 'gsap';

const form = useForm({
    name: '',
    email: '',
    password: '',
    password_confirmation: '',
    role: 'student',
});

// Password strength
const passwordStrength = computed(() => {
    const password = form.password;
    if (!password) return { score: 0, label: '', color: 'bg-gray-200' };
    
    let score = 0;
    if (password.length >= 8) score++;
    if (/[A-Z]/.test(password)) score++;
    if (/[0-9]/.test(password)) score++;
    if (/[^A-Za-z0-9]/.test(password)) score++;
    
    const levels = [
        { score: 0, label: '', color: 'bg-gray-200' },
        { score: 1, label: 'Weak', color: 'bg-red-500' },
        { score: 2, label: 'Fair', color: 'bg-orange-500' },
        { score: 3, label: 'Good', color: 'bg-yellow-500' },
        { score: 4, label: 'Strong', color: 'bg-green-500' },
    ];
    return levels[score];
});

const submit = () => {
    form.post(route('register'), {
        onFinish: () => form.reset('password', 'password_confirmation'),
    });
};

// GSAP Animations
onMounted(() => {
    gsap.from('.left-content', {
        x: -50,
        opacity: 0,
        duration: 1,
        ease: 'power3.out'
    });

    gsap.from('.benefit-item', {
        x: -30,
        opacity: 0,
        duration: 0.5,
        stagger: 0.1,
        delay: 0.3,
        ease: 'power2.out'
    });

    gsap.from('.register-form', {
        y: 30,
        opacity: 0,
        duration: 0.8,
        delay: 0.2,
        ease: 'power2.out'
    });

    gsap.to('.floating-element', {
        y: -15,
        duration: 2.5,
        repeat: -1,
        yoyo: true,
        ease: 'power1.inOut',
        stagger: 0.3
    });
});
</script>

<template>
    <Head title="Register | QuranLearn - Start Learning Quran" />

    <div class="min-h-screen flex">
        <!-- Left Side - Benefits -->
        <div class="hidden lg:flex lg:w-1/2 bg-gradient-to-br from-emerald-800 via-teal-700 to-emerald-900 relative overflow-hidden">
            <!-- Islamic Pattern -->
            <div class="absolute inset-0 opacity-10">
                <div class="absolute inset-0" style="background-image: url('data:image/svg+xml,%3Csvg width=\'60\' height=\'60\' viewBox=\'0 0 60 60\' xmlns=\'http://www.w3.org/2000/svg\'%3E%3Cg fill=\'none\' fill-rule=\'evenodd\'%3E%3Cg fill=\'%23ffffff\' fill-opacity=\'0.4\'%3E%3Cpath d=\'M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z\'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E');"></div>
            </div>

            <!-- Floating Decorations -->
            <div class="floating-element absolute bottom-32 left-10 w-48 h-48 bg-gradient-to-br from-gold-400/20 to-amber-500/20 rounded-full blur-2xl"></div>
            <div class="floating-element absolute top-40 right-16 w-32 h-32 bg-white/10 rounded-full blur-xl"></div>
            <div class="floating-element absolute top-1/2 left-1/4 w-24 h-24 bg-emerald-400/10 rounded-full blur-xl"></div>
            
            <!-- Content -->
            <div class="left-content relative z-10 flex flex-col justify-center px-12 xl:px-20">
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
                    Begin Your<br/>
                    <span class="text-transparent bg-clip-text bg-gradient-to-r from-gold-400 to-amber-300">Quranic Journey</span>
                </h1>
                
                <p class="text-lg text-emerald-100 mb-10 max-w-md">
                    Join thousands of students and start learning the Quran with expert teachers from around the world.
                </p>

                <!-- Benefits -->
                <div class="space-y-4">
                    <div class="benefit-item flex items-center bg-white/5 backdrop-blur-sm rounded-xl px-4 py-3 border border-white/10">
                        <span class="w-12 h-12 rounded-xl bg-gradient-to-br from-emerald-400 to-teal-500 flex items-center justify-center mr-4 text-xl shadow-lg">🎓</span>
                        <div class="text-white">
                            <p class="font-semibold">Free Trial Class</p>
                            <p class="text-sm text-emerald-200">No payment required to start</p>
                        </div>
                    </div>
                    <div class="benefit-item flex items-center bg-white/5 backdrop-blur-sm rounded-xl px-4 py-3 border border-white/10">
                        <span class="w-12 h-12 rounded-xl bg-gradient-to-br from-blue-400 to-indigo-500 flex items-center justify-center mr-4 text-xl shadow-lg">👨‍🏫</span>
                        <div class="text-white">
                            <p class="font-semibold">Expert Teachers</p>
                            <p class="text-sm text-emerald-200">Certified Quran scholars</p>
                        </div>
                    </div>
                    <div class="benefit-item flex items-center bg-white/5 backdrop-blur-sm rounded-xl px-4 py-3 border border-white/10">
                        <span class="w-12 h-12 rounded-xl bg-gradient-to-br from-purple-400 to-pink-500 flex items-center justify-center mr-4 text-xl shadow-lg">📅</span>
                        <div class="text-white">
                            <p class="font-semibold">Flexible Schedule</p>
                            <p class="text-sm text-emerald-200">Learn at your own pace</p>
                        </div>
                    </div>
                    <div class="benefit-item flex items-center bg-white/5 backdrop-blur-sm rounded-xl px-4 py-3 border border-white/10">
                        <span class="w-12 h-12 rounded-xl bg-gradient-to-br from-amber-400 to-orange-500 flex items-center justify-center mr-4 text-xl shadow-lg">🏆</span>
                        <div class="text-white">
                            <p class="font-semibold">Certificates</p>
                            <p class="text-sm text-emerald-200">Get recognized for your learning</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Side - Register Form -->
        <div class="flex-1 flex items-center justify-center p-6 sm:p-8 bg-gradient-to-br from-gray-50 to-white relative overflow-hidden">
            <!-- Background Decoration -->
            <div class="absolute top-0 right-0 w-96 h-96 bg-emerald-100 rounded-full -translate-y-1/2 translate-x-1/2 opacity-50 blur-3xl"></div>
            <div class="absolute bottom-0 left-0 w-64 h-64 bg-gold-100 rounded-full translate-y-1/2 -translate-x-1/2 opacity-50 blur-3xl"></div>

            <div class="w-full max-w-md relative z-10">
                <!-- Mobile Logo -->
                <div class="lg:hidden text-center mb-6">
                    <Link href="/" class="inline-flex items-center space-x-3">
                        <div class="w-12 h-12 bg-gradient-to-br from-emerald-500 to-teal-600 rounded-xl flex items-center justify-center shadow-lg">
                            <span class="text-2xl">📖</span>
                        </div>
                        <span class="text-xl font-display font-bold text-gray-900">QuranLearn</span>
                    </Link>
                </div>

                <div class="register-form bg-white rounded-3xl shadow-2xl p-6 sm:p-8 border border-gray-100">
                    <div class="text-center mb-6">
                        <div class="inline-flex items-center justify-center w-16 h-16 bg-gradient-to-br from-emerald-500 to-teal-600 rounded-2xl mb-4 shadow-lg">
                            <span class="text-3xl">✨</span>
                        </div>
                        <h2 class="text-2xl font-display font-bold text-gray-900">Create Your Account</h2>
                        <p class="text-gray-500 mt-1">Start learning Quran today</p>
                    </div>

                    <form @submit.prevent="submit" class="space-y-4">
                        <!-- Role Selection -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">I want to join as</label>
                            <div class="grid grid-cols-2 gap-3">
                                <button
                                    type="button"
                                    @click="form.role = 'student'"
                                    :class="[
                                        'p-4 rounded-xl border-2 text-center transition-all hover:scale-105',
                                        form.role === 'student'
                                            ? 'border-emerald-500 bg-gradient-to-br from-emerald-50 to-teal-50 text-emerald-700 shadow-lg'
                                            : 'border-gray-200 hover:border-gray-300 text-gray-600'
                                    ]"
                                >
                                    <span class="text-3xl block mb-1">📚</span>
                                    <span class="font-semibold">Student</span>
                                    <p class="text-xs mt-1 opacity-70">Learn Quran</p>
                                </button>
                                <button
                                    type="button"
                                    @click="form.role = 'teacher'"
                                    :class="[
                                        'p-4 rounded-xl border-2 text-center transition-all hover:scale-105',
                                        form.role === 'teacher'
                                            ? 'border-emerald-500 bg-gradient-to-br from-emerald-50 to-teal-50 text-emerald-700 shadow-lg'
                                            : 'border-gray-200 hover:border-gray-300 text-gray-600'
                                    ]"
                                >
                                    <span class="text-3xl block mb-1">👨‍🏫</span>
                                    <span class="font-semibold">Teacher</span>
                                    <p class="text-xs mt-1 opacity-70">Teach Quran</p>
                                </button>
                            </div>
                        </div>

                        <div>
                            <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Full Name</label>
                            <div class="relative">
                                <span class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-400">👤</span>
                                <input
                                    id="name"
                                    type="text"
                                    v-model="form.name"
                                    required
                                    autofocus
                                    autocomplete="name"
                                    class="w-full pl-12 pr-4 py-3 rounded-xl border-2 border-gray-200 focus:border-emerald-500 focus:ring-4 focus:ring-emerald-500/20 transition-all bg-gray-50 focus:bg-white"
                                    placeholder="Your full name"
                                />
                            </div>
                            <p v-if="form.errors.name" class="mt-1 text-sm text-red-500">{{ form.errors.name }}</p>
                        </div>

                        <div>
                            <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email Address</label>
                            <div class="relative">
                                <span class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-400">📧</span>
                                <input
                                    id="email"
                                    type="email"
                                    v-model="form.email"
                                    required
                                    autocomplete="username"
                                    class="w-full pl-12 pr-4 py-3 rounded-xl border-2 border-gray-200 focus:border-emerald-500 focus:ring-4 focus:ring-emerald-500/20 transition-all bg-gray-50 focus:bg-white"
                                    placeholder="you@example.com"
                                />
                            </div>
                            <p v-if="form.errors.email" class="mt-1 text-sm text-red-500">{{ form.errors.email }}</p>
                        </div>

                        <div>
                            <label for="password" class="block text-sm font-medium text-gray-700 mb-1">Password</label>
                            <div class="relative">
                                <span class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-400">🔒</span>
                                <input
                                    id="password"
                                    type="password"
                                    v-model="form.password"
                                    required
                                    autocomplete="new-password"
                                    class="w-full pl-12 pr-4 py-3 rounded-xl border-2 border-gray-200 focus:border-emerald-500 focus:ring-4 focus:ring-emerald-500/20 transition-all bg-gray-50 focus:bg-white"
                                    placeholder="••••••••"
                                />
                            </div>
                            <!-- Password Strength Indicator -->
                            <div v-if="form.password" class="mt-2">
                                <div class="flex items-center gap-2">
                                    <div class="flex-1 h-2 bg-gray-200 rounded-full overflow-hidden">
                                        <div 
                                            :class="['h-full transition-all duration-300', passwordStrength.color]" 
                                            :style="{ width: (passwordStrength.score * 25) + '%' }"
                                        ></div>
                                    </div>
                                    <span class="text-xs font-medium" :class="passwordStrength.color.replace('bg-', 'text-')">
                                        {{ passwordStrength.label }}
                                    </span>
                                </div>
                            </div>
                            <p v-if="form.errors.password" class="mt-1 text-sm text-red-500">{{ form.errors.password }}</p>
                        </div>

                        <div>
                            <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-1">Confirm Password</label>
                            <div class="relative">
                                <span class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-400">🔐</span>
                                <input
                                    id="password_confirmation"
                                    type="password"
                                    v-model="form.password_confirmation"
                                    required
                                    autocomplete="new-password"
                                    class="w-full pl-12 pr-4 py-3 rounded-xl border-2 border-gray-200 focus:border-emerald-500 focus:ring-4 focus:ring-emerald-500/20 transition-all bg-gray-50 focus:bg-white"
                                    placeholder="••••••••"
                                />
                            </div>
                            <p v-if="form.errors.password_confirmation" class="mt-1 text-sm text-red-500">{{ form.errors.password_confirmation }}</p>
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
                                Creating account...
                            </span>
                            <span v-else>Create Free Account →</span>
                        </button>

                        <p class="text-xs text-gray-400 text-center">
                            By registering, you agree to our 
                            <a href="#" class="text-emerald-600 hover:underline">Terms</a> and 
                            <a href="#" class="text-emerald-600 hover:underline">Privacy Policy</a>
                        </p>
                    </form>

                    <!-- Social Login -->
                    <div class="mt-6">
                        <div class="relative">
                            <div class="absolute inset-0 flex items-center">
                                <div class="w-full border-t border-gray-200"></div>
                            </div>
                            <div class="relative flex justify-center text-sm">
                                <span class="px-2 bg-white text-gray-500">Or continue with</span>
                            </div>
                        </div>

                        <div class="mt-6">
                            <a :href="route('auth.google')" 
                               class="w-full flex items-center justify-center gap-3 px-4 py-3 border-2 border-gray-200 rounded-xl hover:bg-gray-50 hover:border-gray-300 transition-all hover:scale-[1.02] group">
                                <img src="https://www.svgrepo.com/show/475656/google-color.svg" class="w-6 h-6" alt="Google">
                                <span class="font-medium text-gray-700 group-hover:text-gray-900">Sign up with Google</span>
                            </a>
                        </div>
                    </div>

                    <div class="relative my-6">
                        <div class="absolute inset-0 flex items-center">
                            <div class="w-full border-t border-gray-200"></div>
                        </div>
                        <div class="relative flex justify-center text-sm">
                            <span class="px-4 bg-white text-gray-500">Already have an account?</span>
                        </div>
                    </div>

                    <Link 
                        :href="route('login')" 
                        class="block w-full py-3 text-center border-2 border-emerald-500 text-emerald-600 hover:bg-emerald-50 font-semibold rounded-xl transition-all hover:scale-[1.02]"
                    >
                        Sign In Instead
                    </Link>
                </div>

                <p class="text-center text-sm text-gray-400 mt-6">
                    © {{ new Date().getFullYear() }} QuranLearn. All rights reserved.
                </p>
            </div>
        </div>
    </div>
</template>
