<script setup>
import { ref, onMounted } from 'vue';
import { Link, usePage } from '@inertiajs/vue3';
import DarkModeToggle from '@/Components/DarkModeToggle.vue';
import { useDarkMode } from '@/composables/useDarkMode';

const sidebarOpen = ref(false);
const page = usePage();
const { isDark, initDarkMode } = useDarkMode();

onMounted(() => {
    initDarkMode();
});

const navigation = [
    { name: 'Dashboard', href: '/teacher/dashboard', icon: '🏠' },
    { name: 'My Batches', href: '/teacher/batches', icon: '📚' },
    { name: 'Schedule', href: '/teacher/schedule', icon: '📅' },
    { name: 'Attendance', href: '/teacher/attendance', icon: '✅' },
    { name: 'Resources', href: '/teacher/resources', icon: '📤' },
    { name: 'Students', href: '/teacher/students', icon: '👥' },
    { name: 'Earnings', href: '/teacher/earnings', icon: '💰' },
];

const bottomNavigation = [
    { name: 'Settings', href: '/settings', icon: '⚙️' },
    { name: 'Support', href: '/support', icon: '💬' },
];
</script>

<template>
    <div class="min-h-screen bg-gray-50 dark:bg-slate-900 transition-colors duration-300">
        <!-- Mobile sidebar backdrop -->
        <div 
            v-if="sidebarOpen" 
            @click="sidebarOpen = false"
            class="fixed inset-0 bg-black/50 z-40 lg:hidden transition-opacity"
        ></div>

        <!-- Sidebar -->
        <aside 
            :class="[
                'fixed top-0 left-0 z-50 h-full w-64 bg-gradient-to-b from-slate-800 to-slate-900 transform transition-transform duration-300 ease-in-out lg:translate-x-0',
                sidebarOpen ? 'translate-x-0' : '-translate-x-full'
            ]"
        >
            <!-- Logo -->
            <div class="h-16 flex items-center px-6 border-b border-slate-700">
                <Link href="/" class="flex items-center space-x-3">
                    <div class="w-10 h-10 bg-gradient-to-br from-emerald-400 to-teal-500 rounded-xl flex items-center justify-center">
                        <span class="text-white text-xl">📖</span>
                    </div>
                    <span class="font-display text-xl font-bold text-white">
                        Quran<span class="text-emerald-400">Learn</span>
                    </span>
                </Link>
            </div>

            <!-- User Info -->
            <div class="p-4 border-b border-slate-700">
                <div class="flex items-center space-x-3">
                    <div class="w-12 h-12 rounded-full bg-gradient-to-br from-emerald-400 to-teal-500 flex items-center justify-center text-white font-bold text-lg">
                        {{ $page.props.auth.user.name.charAt(0) }}
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium text-white truncate">{{ $page.props.auth.user.name }}</p>
                        <p class="text-xs text-emerald-300">👨‍🏫 Teacher</p>
                    </div>
                </div>
                <!-- Stats -->
                <div class="flex items-center space-x-4 mt-3 text-sm">
                    <div class="flex items-center text-slate-300">
                        <span class="mr-1">⭐</span>
                        <span class="font-medium">5.0</span>
                    </div>
                    <div class="flex items-center text-emerald-400">
                        <span class="mr-1">👥</span>
                        <span class="font-medium">Active</span>
                    </div>
                </div>
            </div>

            <!-- Navigation -->
            <nav class="flex-1 p-4 space-y-1 overflow-y-auto">
                <p class="px-3 py-2 text-xs font-semibold text-slate-500 uppercase tracking-wider">Teaching</p>
                <Link
                    v-for="item in navigation"
                    :key="item.name"
                    :href="item.href"
                    :class="[
                        'flex items-center px-3 py-2.5 rounded-xl text-sm font-medium transition-all duration-200 group',
                        $page.url.startsWith(item.href) || $page.url === item.href
                            ? 'bg-emerald-500/20 text-emerald-400 border-l-4 border-emerald-400'
                            : 'text-slate-300 hover:bg-slate-700/50 hover:text-white'
                    ]"
                    @click="sidebarOpen = false"
                >
                    <span class="text-lg mr-3 group-hover:scale-110 transition-transform">{{ item.icon }}</span>
                    {{ item.name }}
                </Link>

                <div class="my-4 border-t border-slate-700"></div>
                
                <p class="px-3 py-2 text-xs font-semibold text-slate-500 uppercase tracking-wider">Account</p>
                <Link
                    v-for="item in bottomNavigation"
                    :key="item.name"
                    :href="item.href"
                    :class="[
                        'flex items-center px-3 py-2.5 rounded-xl text-sm font-medium transition-all duration-200 group',
                        $page.url.startsWith(item.href)
                            ? 'bg-emerald-500/20 text-emerald-400 border-l-4 border-emerald-400'
                            : 'text-slate-300 hover:bg-slate-700/50 hover:text-white'
                    ]"
                    @click="sidebarOpen = false"
                >
                    <span class="text-lg mr-3 group-hover:scale-110 transition-transform">{{ item.icon }}</span>
                    {{ item.name }}
                </Link>
            </nav>

            <!-- Logout -->
            <div class="p-4 border-t border-slate-700">
                <Link
                    :href="route('logout')"
                    method="post"
                    as="button"
                    class="flex items-center w-full px-3 py-2.5 rounded-xl text-sm font-medium text-red-400 hover:bg-red-500/10 transition-colors"
                >
                    <span class="text-lg mr-3">🚪</span>
                    Log Out
                </Link>
            </div>
        </aside>

        <!-- Main Content -->
        <div class="lg:pl-64">
            <!-- Top Bar -->
            <header class="sticky top-0 z-30 h-16 bg-white dark:bg-slate-800 border-b border-gray-200 dark:border-slate-700 flex items-center px-4 lg:px-8 shadow-sm transition-colors">
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
                        href="/teacher/schedule"
                        class="hidden sm:inline-flex items-center px-4 py-2 bg-emerald-500 hover:bg-emerald-600 text-white text-sm font-semibold rounded-xl transition-colors"
                    >
                        📅 Full Schedule
                    </Link>
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
nav::-webkit-scrollbar {
    width: 4px;
}
nav::-webkit-scrollbar-track {
    background: transparent;
}
nav::-webkit-scrollbar-thumb {
    background: #475569;
    border-radius: 2px;
}
</style>
