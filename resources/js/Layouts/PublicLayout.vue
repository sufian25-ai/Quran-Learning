<script setup>
import { ref, onMounted, onUnmounted } from 'vue';
import { Link, usePage } from '@inertiajs/vue3';
import ChatWidget from '@/Components/ChatWidget.vue';
import FacebookPixel from '@/Components/Analytics/FacebookPixel.vue';
import GoogleAnalytics from '@/Components/Analytics/GoogleAnalytics.vue';

const isScrolled = ref(false);
const isMobileMenuOpen = ref(false);
const userMenuOpen = ref(false);
const userDropdown = ref(null);
const page = usePage();

// Check if user is logged in
const isLoggedIn = computed(() => !!page.props.auth?.user);
const user = computed(() => page.props.auth?.user);

import { computed } from 'vue';

onMounted(() => {
    window.addEventListener('scroll', () => {
        isScrolled.value = window.scrollY > 20;
    });
    document.addEventListener('click', handleClickOutside);
});

onUnmounted(() => {
    document.removeEventListener('click', handleClickOutside);
});

const handleClickOutside = (event) => {
    if (userDropdown.value && !userDropdown.value.contains(event.target)) {
        userMenuOpen.value = false;
    }
};

const navigation = [
    { name: 'Courses', href: '/courses' },
    { name: 'Teachers', href: '/teachers' },
    { name: 'Pricing', href: '/pricing' },
    { name: 'About', href: '/about' },
];

// Get dashboard link based on user role
const getDashboardLink = () => {
    if (!user.value) return '/login';
    const role = user.value.role;
    if (role === 'admin') return '/admin';
    if (role === 'teacher') return '/teacher/dashboard';
    return '/student/dashboard';
};
</script>

<template>
    <div class="min-h-screen bg-white">
        <!-- Navigation -->
        <nav
            :class="[
                'fixed top-0 left-0 right-0 z-50 transition-all duration-300',
                isScrolled ? 'bg-white/95 backdrop-blur-md shadow-soft' : 'bg-transparent'
            ]"
        >
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between items-center h-20">
                    <!-- Logo -->
                    <Link href="/" class="flex items-center space-x-3">
                        <div class="w-10 h-10 bg-gradient-to-br from-primary-500 to-primary-600 rounded-xl flex items-center justify-center">
                            <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M12 2L2 7l10 5 10-5-10-5zM2 17l10 5 10-5M2 12l10 5 10-5"/>
                            </svg>
                        </div>
                        <span class="font-display text-xl font-bold text-gray-900">
                            Quran<span class="text-primary-500">Learn</span>
                        </span>
                    </Link>

                    <!-- Desktop Navigation -->
                    <div class="hidden md:flex items-center space-x-8">
                        <Link
                            v-for="item in navigation"
                            :key="item.name"
                            :href="item.href"
                            class="text-gray-600 hover:text-primary-500 font-medium transition-colors"
                        >
                            {{ item.name }}
                        </Link>
                    </div>

                    <!-- Auth Buttons - Show different content based on login status -->
                    <div class="hidden md:flex items-center space-x-4">
                        <!-- Logged In: Show User Dropdown -->
                        <template v-if="isLoggedIn">
                            <Link
                                :href="getDashboardLink()"
                                class="text-gray-700 hover:text-primary-500 font-medium transition-colors"
                            >
                                📊 Dashboard
                            </Link>
                            
                            <div class="relative" ref="userDropdown">
                                <button
                                    @click="userMenuOpen = !userMenuOpen"
                                    class="flex items-center gap-2 px-3 py-2 rounded-xl bg-gray-100 hover:bg-gray-200 transition-colors"
                                >
                                    <div class="w-8 h-8 rounded-full bg-gradient-to-br from-primary-400 to-primary-600 flex items-center justify-center text-white font-semibold text-sm">
                                        {{ user.name?.charAt(0) || 'U' }}
                                    </div>
                                    <span class="text-sm font-medium text-gray-700 max-w-[120px] truncate">
                                        {{ user.name }}
                                    </span>
                                    <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                    </svg>
                                </button>

                                <!-- Dropdown Menu -->
                                <div
                                    v-if="userMenuOpen"
                                    class="absolute right-0 mt-2 w-56 bg-white rounded-xl shadow-lg border border-gray-200 py-2 z-50"
                                >
                                    <div class="px-4 py-3 border-b border-gray-100">
                                        <p class="text-sm font-medium text-gray-900">{{ user.name }}</p>
                                        <p class="text-xs text-gray-500 truncate">{{ user.email }}</p>
                                    </div>
                                    <Link :href="getDashboardLink()" class="flex items-center gap-2 px-4 py-2 text-sm text-gray-700 hover:bg-gray-100" @click="userMenuOpen = false">
                                        <span>🏠</span> Dashboard
                                    </Link>
                                    <Link href="/settings" class="flex items-center gap-2 px-4 py-2 text-sm text-gray-700 hover:bg-gray-100" @click="userMenuOpen = false">
                                        <span>⚙️</span> Settings
                                    </Link>
                                    <div class="border-t border-gray-100 my-1"></div>
                                    <Link
                                        :href="route('logout')"
                                        method="post"
                                        as="button"
                                        class="flex items-center gap-2 w-full px-4 py-2 text-sm text-red-600 hover:bg-red-50"
                                    >
                                        <span>🚪</span> Log Out
                                    </Link>
                                </div>
                            </div>
                        </template>

                        <!-- Not Logged In: Show Sign In / Register -->
                        <template v-else>
                            <Link
                                href="/login"
                                class="text-gray-700 hover:text-primary-500 font-medium transition-colors"
                            >
                                Sign In
                            </Link>
                            <Link
                                href="/register"
                                class="bg-primary-500 hover:bg-primary-600 text-white px-6 py-2.5 rounded-xl font-semibold transition-all hover:shadow-glow"
                            >
                                Start Learning
                            </Link>
                        </template>
                    </div>

                    <!-- Mobile Menu Button -->
                    <button
                        @click="isMobileMenuOpen = !isMobileMenuOpen"
                        class="md:hidden p-2 rounded-lg text-gray-600 hover:bg-gray-100"
                    >
                        <svg v-if="!isMobileMenuOpen" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                        </svg>
                        <svg v-else class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>

                <!-- Mobile Menu -->
                <div
                    v-if="isMobileMenuOpen"
                    class="md:hidden bg-white border-t border-gray-100 py-4"
                >
                    <div class="space-y-3">
                        <Link
                            v-for="item in navigation"
                            :key="item.name"
                            :href="item.href"
                            class="block px-4 py-2 text-gray-600 hover:text-primary-500 hover:bg-gray-50 rounded-lg"
                        >
                            {{ item.name }}
                        </Link>
                        <div class="border-t border-gray-100 pt-3 px-4 space-y-3">
                            <!-- Mobile: Logged In -->
                            <template v-if="isLoggedIn">
                                <div class="flex items-center gap-3 py-2">
                                    <div class="w-10 h-10 rounded-full bg-gradient-to-br from-primary-400 to-primary-600 flex items-center justify-center text-white font-semibold">
                                        {{ user.name?.charAt(0) || 'U' }}
                                    </div>
                                    <div>
                                        <p class="font-medium text-gray-900">{{ user.name }}</p>
                                        <p class="text-xs text-gray-500">{{ user.email }}</p>
                                    </div>
                                </div>
                                <Link :href="getDashboardLink()" class="block text-primary-600 font-medium py-2">
                                    📊 Go to Dashboard
                                </Link>
                                <Link
                                    :href="route('logout')"
                                    method="post"
                                    as="button"
                                    class="block w-full text-left text-red-600 font-medium py-2"
                                >
                                    🚪 Log Out
                                </Link>
                            </template>
                            
                            <!-- Mobile: Not Logged In -->
                            <template v-else>
                                <Link href="/login" class="block text-gray-700 font-medium">
                                    Sign In
                                </Link>
                                <Link
                                    href="/register"
                                    class="block bg-primary-500 text-white text-center py-3 rounded-xl font-semibold"
                                >
                                    Start Learning
                                </Link>
                            </template>
                        </div>
                    </div>
                </div>
            </div>
        </nav>

        <!-- Main Content -->
        <main>
            <slot />
        </main>

        <!-- Footer -->
        <footer class="bg-gray-900 text-white">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
                <div class="grid grid-cols-1 md:grid-cols-4 gap-12">
                    <!-- Brand -->
                    <div class="md:col-span-1">
                        <div class="flex items-center space-x-3 mb-6">
                            <div class="w-10 h-10 bg-gradient-to-br from-primary-500 to-primary-400 rounded-xl flex items-center justify-center">
                                <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M12 2L2 7l10 5 10-5-10-5zM2 17l10 5 10-5M2 12l10 5 10-5"/>
                                </svg>
                            </div>
                            <span class="font-display text-xl font-bold">QuranLearn</span>
                        </div>
                        <p class="text-gray-400 text-sm leading-relaxed">
                            World-class Quran education with qualified teachers. Learn at your own pace, from anywhere in the world.
                        </p>
                    </div>

                    <!-- Company -->
                    <div>
                        <h4 class="font-semibold text-lg mb-4">Company</h4>
                        <ul class="space-y-3 text-gray-400">
                            <li><Link href="/about" class="hover:text-white transition-colors">About Us</Link></li>
                            <li><Link href="/teachers" class="hover:text-white transition-colors">Our Teachers</Link></li>
                            <li><Link href="/careers" class="hover:text-white transition-colors">Careers</Link></li>
                            <li><Link href="/contact" class="hover:text-white transition-colors">Contact</Link></li>
                        </ul>
                    </div>

                    <!-- Courses -->
                    <div>
                        <h4 class="font-semibold text-lg mb-4">Courses</h4>
                        <ul class="space-y-3 text-gray-400">
                            <li><Link href="/courses?category=quran_reading" class="hover:text-white transition-colors">Quran Reading</Link></li>
                            <li><Link href="/courses?category=tajweed" class="hover:text-white transition-colors">Tajweed</Link></li>
                            <li><Link href="/courses?category=hifz" class="hover:text-white transition-colors">Hifz Program</Link></li>
                            <li><Link href="/courses?category=arabic" class="hover:text-white transition-colors">Arabic Language</Link></li>
                        </ul>
                    </div>

                    <!-- Support -->
                    <div>
                        <h4 class="font-semibold text-lg mb-4">Support</h4>
                        <ul class="space-y-3 text-gray-400">
                            <li><Link href="/faq" class="hover:text-white transition-colors">FAQ</Link></li>
                            <li><Link href="/help" class="hover:text-white transition-colors">Help Center</Link></li>
                            <li><Link href="/privacy" class="hover:text-white transition-colors">Privacy Policy</Link></li>
                            <li><Link href="/terms" class="hover:text-white transition-colors">Terms of Service</Link></li>
                        </ul>
                    </div>
                </div>

                <div class="border-t border-gray-800 mt-12 pt-8 flex flex-col md:flex-row justify-between items-center">
                    <p class="text-gray-400 text-sm">
                        © {{ new Date().getFullYear() }} QuranLearn. All rights reserved.
                    </p>
                    <div class="flex items-center space-x-6 mt-4 md:mt-0">
                        <a href="#" class="text-gray-400 hover:text-white transition-colors">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>
                        </a>
                        <a href="#" class="text-gray-400 hover:text-white transition-colors">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M23.953 4.57a10 10 0 01-2.825.775 4.958 4.958 0 002.163-2.723c-.951.555-2.005.959-3.127 1.184a4.92 4.92 0 00-8.384 4.482C7.69 8.095 4.067 6.13 1.64 3.162a4.822 4.822 0 00-.666 2.475c0 1.71.87 3.213 2.188 4.096a4.904 4.904 0 01-2.228-.616v.06a4.923 4.923 0 003.946 4.827 4.996 4.996 0 01-2.212.085 4.936 4.936 0 004.604 3.417 9.867 9.867 0 01-6.102 2.105c-.39 0-.779-.023-1.17-.067a13.995 13.995 0 007.557 2.209c9.053 0 13.998-7.496 13.998-13.985 0-.21 0-.42-.015-.63A9.935 9.935 0 0024 4.59z"/></svg>
                        </a>
                        <a href="#" class="text-gray-400 hover:text-white transition-colors">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M12.017 0C5.396 0 .029 5.367.029 11.987c0 5.079 3.158 9.417 7.618 11.162-.105-.949-.199-2.403.041-3.439.219-.937 1.406-5.957 1.406-5.957s-.359-.72-.359-1.781c0-1.663.967-2.911 2.168-2.911 1.024 0 1.518.769 1.518 1.688 0 1.029-.653 2.567-.992 3.992-.285 1.193.6 2.165 1.775 2.165 2.128 0 3.768-2.245 3.768-5.487 0-2.861-2.063-4.869-5.008-4.869-3.41 0-5.409 2.562-5.409 5.199 0 1.033.394 2.143.889 2.741.099.12.112.225.085.345-.09.375-.293 1.199-.334 1.363-.053.225-.172.271-.401.165-1.495-.69-2.433-2.878-2.433-4.646 0-3.776 2.748-7.252 7.92-7.252 4.158 0 7.392 2.967 7.392 6.923 0 4.135-2.607 7.462-6.233 7.462-1.214 0-2.354-.629-2.758-1.379l-.749 2.848c-.269 1.045-1.004 2.352-1.498 3.146 1.123.345 2.306.535 3.55.535 6.607 0 11.985-5.365 11.985-11.987C23.97 5.39 18.592.026 11.985.026L12.017 0z"/></svg>
                        </a>
                        <a href="#" class="text-gray-400 hover:text-white transition-colors">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M12 0C5.374 0 0 5.373 0 12c0 5.302 3.438 9.8 8.207 11.387.599.111.793-.261.793-.577v-2.234c-3.338.726-4.033-1.416-4.033-1.416-.546-1.387-1.333-1.756-1.333-1.756-1.089-.745.083-.729.083-.729 1.205.084 1.839 1.237 1.839 1.237 1.07 1.834 2.807 1.304 3.492.997.107-.775.418-1.305.762-1.604-2.665-.305-5.467-1.334-5.467-5.931 0-1.311.469-2.381 1.236-3.221-.124-.303-.535-1.524.117-3.176 0 0 1.008-.322 3.301 1.23A11.509 11.509 0 0112 5.803c1.02.005 2.047.138 3.006.404 2.291-1.552 3.297-1.23 3.297-1.23.653 1.653.242 2.874.118 3.176.77.84 1.235 1.911 1.235 3.221 0 4.609-2.807 5.624-5.479 5.921.43.372.823 1.102.823 2.222v3.293c0 .319.192.694.801.576C20.566 21.797 24 17.3 24 12c0-6.627-5.373-12-12-12z"/></svg>
                        </a>
                    </div>
                </div>
            </div>
        </footer>

        <!-- Chat Widget -->
        <ChatWidget />
        
        <!-- Analytics -->
        <FacebookPixel />
        <GoogleAnalytics />
    </div>
</template>
