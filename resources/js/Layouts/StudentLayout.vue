<script setup>
import { ref, computed, onMounted, onUnmounted } from 'vue';
import { Link, usePage } from '@inertiajs/vue3';
import Dropdown from '@/Components/Dropdown.vue';
import DropdownLink from '@/Components/DropdownLink.vue';
import DarkModeToggle from '@/Components/DarkModeToggle.vue';
import { useDarkMode } from '@/composables/useDarkMode';

const sidebarOpen = ref(false);
const userMenuOpen = ref(false);
const userDropdown = ref(null);
const page = usePage();
const { isDark, initDarkMode } = useDarkMode();

// Close dropdown when clicking outside
const handleClickOutside = (event) => {
    if (userDropdown.value && !userDropdown.value.contains(event.target)) {
        userMenuOpen.value = false;
    }
};

onMounted(() => {
    initDarkMode();
    document.addEventListener('click', handleClickOutside);
});

onUnmounted(() => {
    document.removeEventListener('click', handleClickOutside);
});

const navigation = [
    { name: 'Dashboard', href: '/student/dashboard', icon: '🏠', route: 'student.dashboard' },
    { name: 'Quran Reader', href: '/quran', icon: '📖', route: 'quran' },
    { name: 'Hifz Reader', href: '/hifz', icon: '📚', route: 'hifz' },
    { name: 'My Progress', href: '/progress', icon: '📊', route: 'progress' },
    { name: 'Recitations', href: '/recitations', icon: '🎤', route: 'recitations' },
    { name: 'My Courses', href: '/enrollments', icon: '📚', route: 'enrollments' },
    { name: 'Schedule', href: '/classes', icon: '📅', route: 'classes' },
    { name: 'Recordings', href: '/recordings', icon: '🎥', route: 'recordings' },
    { name: 'Resources', href: '/resources', icon: '📁', route: 'resources' },
    { name: 'Leaderboard', href: '/leaderboard', icon: '🏆', route: 'leaderboard' },
    { name: 'Certificates', href: '/certificates', icon: '🏅', route: 'certificates.index' },
    { name: 'Notifications', href: '/notifications', icon: '🔔', route: 'notifications' },
];

const bottomNavigation = [
    { name: 'Settings', href: '/settings', icon: '⚙️', route: 'settings' },
    { name: 'Support', href: '/support', icon: '💬', route: 'support' },
];

const isActive = (routeName) => {
    return page.url.startsWith(navigation.find(n => n.route === routeName)?.href || '');
};
</script>

<template>
    <div class="min-h-screen bg-gray-50 dark:bg-slate-900 transition-colors duration-300">
        <!-- Mobile sidebar backdrop -->
        <div 
            v-if="sidebarOpen" 
            @click="sidebarOpen = false"
            class="fixed inset-0 bg-black/50 z-40 lg:hidden"
        ></div>

        <!-- Sidebar -->
        <aside 
            :class="[
                'fixed top-0 left-0 z-50 h-screen w-64 bg-white dark:bg-slate-800 border-r border-gray-200 dark:border-slate-700 transform transition-all duration-300 ease-in-out lg:translate-x-0 flex flex-col',
                sidebarOpen ? 'translate-x-0' : '-translate-x-full'
            ]"
        >
            <!-- Logo (Fixed at top) -->
            <div class="flex-shrink-0 h-16 flex items-center px-6 border-b border-gray-200 dark:border-slate-700">
                <Link href="/" class="flex items-center space-x-3">
                    <div class="w-10 h-10 bg-gradient-to-br from-primary-500 to-primary-600 rounded-xl flex items-center justify-center">
                        <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M12 2L2 7l10 5 10-5-10-5zM2 17l10 5 10-5M2 12l10 5 10-5"/>
                        </svg>
                    </div>
                    <span class="font-display text-xl font-bold text-gray-900 dark:text-white">
                        Quran<span class="text-primary-500">Learn</span>
                    </span>
                </Link>
            </div>

            <!-- User Info (Fixed at top) - Enhanced -->
            <div class="flex-shrink-0 p-4 border-b border-gray-200 dark:border-slate-700">
                <!-- Profile Card -->
                <div class="relative bg-gradient-to-br from-primary-50 to-emerald-50 dark:from-slate-700 dark:to-slate-600 rounded-2xl p-4 transition-all duration-300 hover:shadow-lg">
                    <!-- Avatar with Online Indicator -->
                    <div class="flex items-center gap-4">
                        <div class="relative group">
                            <!-- Animated Ring -->
                            <div class="absolute -inset-1.5 bg-gradient-to-r from-primary-400 via-emerald-400 to-teal-400 rounded-full blur opacity-40 group-hover:opacity-75 transition duration-500 animate-pulse"></div>
                            
                            <!-- Avatar - BIGGER -->
                            <div class="relative w-16 h-16 rounded-full bg-gradient-to-br from-primary-400 to-emerald-500 flex items-center justify-center text-white font-bold text-2xl overflow-hidden ring-4 ring-white dark:ring-slate-700 shadow-xl">
                                <img
                                    v-if="$page.props.auth.user.avatar"
                                    :src="$page.props.auth.user.avatar"
                                    class="w-full h-full object-cover"
                                    alt="Avatar"
                                />
                                <span v-else class="drop-shadow-lg">{{ $page.props.auth.user.name.charAt(0).toUpperCase() }}</span>
                            </div>
                            
                            <!-- Online Indicator -->
                            <div class="absolute bottom-0 right-0 w-4 h-4 bg-green-500 border-2 border-white dark:border-slate-700 rounded-full shadow-sm">
                                <div class="absolute inset-0 bg-green-400 rounded-full animate-ping opacity-75"></div>
                            </div>
                        </div>
                        
                        <!-- User Details -->
                        <div class="flex-1 min-w-0">
                            <p class="font-bold text-gray-900 dark:text-white truncate text-lg">
                                {{ $page.props.auth.user.name }}
                            </p>
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-medium bg-primary-100 dark:bg-primary-900/50 text-primary-700 dark:text-primary-300 mt-1">
                                📚 Student
                            </span>
                            
                            <!-- Compact Stats - Below Name -->
                            <div class="flex items-center gap-3 mt-2">
                                <span class="flex items-center gap-1 text-xs text-amber-600 dark:text-amber-400 font-medium">
                                    ⭐ {{ $page.props.auth?.user?.points || 0 }}
                                </span>
                                <span class="flex items-center gap-1 text-xs text-orange-600 dark:text-orange-400 font-medium">
                                    🔥 {{ $page.props.auth?.user?.learningStreak?.current_streak || 0 }} days
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Navigation (Scrollable) -->
            <nav class="flex-1 overflow-y-auto p-4 space-y-1">
                <p class="px-3 py-2 text-xs font-semibold text-gray-400 dark:text-slate-500 uppercase tracking-wider">Main Menu</p>
                <Link
                    v-for="item in navigation"
                    :key="item.name"
                    :href="item.href"
                    :class="[
                        'flex items-center px-3 py-2.5 rounded-xl text-sm font-medium transition-all duration-200',
                        $page.url.startsWith(item.href) || $page.url === item.href
                            ? 'bg-primary-50 dark:bg-primary-900/30 text-primary-600 dark:text-primary-400 border-l-4 border-primary-500'
                            : 'text-gray-600 dark:text-slate-300 hover:bg-gray-100 dark:hover:bg-slate-700 hover:text-gray-900 dark:hover:text-white'
                    ]"
                    @click="sidebarOpen = false"
                >
                    <span class="text-lg mr-3">{{ item.icon }}</span>
                    {{ item.name }}
                </Link>

                <div class="my-4 border-t border-gray-200 dark:border-slate-700"></div>
                
                <p class="px-3 py-2 text-xs font-semibold text-gray-400 dark:text-slate-500 uppercase tracking-wider">Support</p>
                <Link
                    v-for="item in bottomNavigation"
                    :key="item.name"
                    :href="item.href"
                    :class="[
                        'flex items-center px-3 py-2.5 rounded-xl text-sm font-medium transition-all duration-200',
                        $page.url.startsWith(item.href)
                            ? 'bg-primary-50 dark:bg-primary-900/30 text-primary-600 dark:text-primary-400 border-l-4 border-primary-500'
                            : 'text-gray-600 dark:text-slate-300 hover:bg-gray-100 dark:hover:bg-slate-700 hover:text-gray-900 dark:hover:text-white'
                    ]"
                    @click="sidebarOpen = false"
                >
                    <span class="text-lg mr-3">{{ item.icon }}</span>
                    {{ item.name }}
                </Link>
            </nav>

            <!-- Logout (Fixed at bottom) -->
            <div class="flex-shrink-0 p-4 border-t border-gray-200 dark:border-slate-700">
                <Link
                    :href="route('logout')"
                    method="post"
                    as="button"
                    class="flex items-center w-full px-3 py-2.5 rounded-xl text-sm font-medium text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/20 transition-colors"
                >
                    <span class="text-lg mr-3">🚪</span>
                    Log Out
                </Link>
            </div>
        </aside>

        <!-- Main Content -->
        <div class="lg:pl-64">
            <!-- Top Bar -->
            <header class="sticky top-0 z-30 h-16 bg-white dark:bg-slate-800 border-b border-gray-200 dark:border-slate-700 flex items-center px-4 lg:px-8 transition-colors">
                <!-- Mobile menu button -->
                <button
                    @click="sidebarOpen = true"
                    class="lg:hidden p-2 rounded-lg text-gray-500 dark:text-slate-400 hover:bg-gray-100 dark:hover:bg-slate-700 transition-colors mr-4"
                >
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                    </svg>
                </button>

                <!-- Page Header -->
                <div class="flex-1">
                    <slot name="header" />
                </div>

                <!-- Right side actions -->
                <div class="flex items-center space-x-3">
                    <!-- Dark Mode Toggle -->
                    <DarkModeToggle />

                    <Link 
                        href="/notifications" 
                        class="relative p-2 rounded-lg text-gray-500 dark:text-slate-400 hover:bg-gray-100 dark:hover:bg-slate-700 transition-colors"
                    >
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                        </svg>
                        <span class="absolute top-1 right-1 w-2 h-2 bg-red-500 rounded-full"></span>
                    </Link>
                    
                    <Link
                        href="/courses"
                        class="hidden sm:inline-flex items-center px-4 py-2 bg-primary-500 hover:bg-primary-600 text-white text-sm font-semibold rounded-xl transition-colors"
                    >
                        Browse Courses
                    </Link>

                    <!-- User Dropdown -->
                    <div class="relative" ref="userDropdown">
                        <button
                            @click="userMenuOpen = !userMenuOpen"
                            class="flex items-center gap-2 px-3 py-2 rounded-xl bg-gray-100 dark:bg-slate-700 hover:bg-gray-200 dark:hover:bg-slate-600 transition-colors"
                        >
                            <div class="w-8 h-8 rounded-full bg-gradient-to-br from-primary-400 to-primary-600 flex items-center justify-center text-white font-semibold text-sm">
                                {{ $page.props.auth.user.name.charAt(0) }}
                            </div>
                            <span class="hidden md:block text-sm font-medium text-gray-700 dark:text-slate-200 max-w-[100px] truncate">
                                {{ $page.props.auth.user.name }}
                            </span>
                            <svg class="w-4 h-4 text-gray-500 dark:text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                            </svg>
                        </button>

                        <!-- Dropdown Menu -->
                        <div
                            v-if="userMenuOpen"
                            class="absolute right-0 mt-2 w-56 bg-white dark:bg-slate-800 rounded-xl shadow-lg border border-gray-200 dark:border-slate-700 py-2 z-50"
                        >
                            <div class="px-4 py-3 border-b border-gray-100 dark:border-slate-700">
                                <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $page.props.auth.user.name }}</p>
                                <p class="text-xs text-gray-500 dark:text-slate-400 truncate">{{ $page.props.auth.user.email }}</p>
                            </div>
                            <Link href="/settings" class="flex items-center gap-2 px-4 py-2 text-sm text-gray-700 dark:text-slate-300 hover:bg-gray-100 dark:hover:bg-slate-700" @click="userMenuOpen = false">
                                <span>⚙️</span> Settings
                            </Link>
                            <Link href="/settings/notifications" class="flex items-center gap-2 px-4 py-2 text-sm text-gray-700 dark:text-slate-300 hover:bg-gray-100 dark:hover:bg-slate-700" @click="userMenuOpen = false">
                                <span>🔔</span> Notifications
                            </Link>
                            <div class="border-t border-gray-100 dark:border-slate-700 my-1"></div>
                            <Link
                                :href="route('logout')"
                                method="post"
                                as="button"
                                class="flex items-center gap-2 w-full px-4 py-2 text-sm text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/20"
                            >
                                <span>🚪</span> Log Out
                            </Link>
                        </div>
                    </div>
                </div>
            </header>

            <!-- Page Content -->
            <main class="p-4 lg:p-8 min-h-[calc(100vh-4rem)]">
                <slot />
            </main>
        </div>
    </div>
</template>

<style scoped>
/* Hide scrollbar for sidebar */
nav::-webkit-scrollbar {
    width: 4px;
}
nav::-webkit-scrollbar-track {
    background: transparent;
}
nav::-webkit-scrollbar-thumb {
    background: #e5e7eb;
    border-radius: 2px;
}
.dark nav::-webkit-scrollbar-thumb {
    background: #475569;
}
nav::-webkit-scrollbar-thumb:hover {
    background: #d1d5db;
}
.dark nav::-webkit-scrollbar-thumb:hover {
    background: #64748b;
}
</style>
