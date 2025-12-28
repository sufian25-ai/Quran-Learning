<script setup>
import { ref, computed } from 'vue';
import { Link, usePage } from '@inertiajs/vue3';

const page = usePage();
const auth = computed(() => page.props.auth);
const currentPath = computed(() => page.url);

const sidebarOpen = ref(true);

const navigation = [
    { name: 'Dashboard', href: '/admin', icon: '🏠' },
    { name: 'Users', href: '/admin/users', icon: '👥' },
    { name: 'Teachers', href: '/admin/teachers', icon: '👨‍🏫' },
    { name: 'Courses', href: '/admin/courses', icon: '📚' },
    { name: 'Batches', href: '/admin/batches', icon: '📅' },
    { name: 'Enrollments', href: '/admin/enrollments', icon: '📝' },
    { name: 'Payments', href: '/admin/payments', icon: '💳' },
    { name: 'Analytics', href: '/admin/analytics', icon: '📊' },
    { name: 'Reviews', href: '/admin/reviews', icon: '⭐' },
    { name: 'Chat Support', href: '/admin/chat', icon: '💬' },
    { name: 'Tickets', href: '/admin/tickets', icon: '🎫' },
    { name: 'Settings', href: '/admin/settings', icon: '⚙️' },
];

const isActive = (href) => {
    if (href === '/admin') {
        return currentPath.value === '/admin';
    }
    return currentPath.value.startsWith(href);
};
</script>

<template>
    <div class="min-h-screen bg-gray-100">
        <!-- Sidebar -->
        <aside
            :class="[
                'fixed inset-y-0 left-0 z-50 w-64 bg-gray-900 transform transition-transform duration-300',
                sidebarOpen ? 'translate-x-0' : '-translate-x-full'
            ]"
        >
            <!-- Logo -->
            <div class="flex items-center justify-between h-16 px-6 border-b border-gray-800">
                <Link href="/" class="flex items-center space-x-3">
                    <div class="w-8 h-8 bg-gradient-to-br from-primary-500 to-primary-400 rounded-lg flex items-center justify-center">
                        <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M12 2L2 7l10 5 10-5-10-5zM2 17l10 5 10-5M2 12l10 5 10-5"/>
                        </svg>
                    </div>
                    <span class="font-display text-lg font-bold text-white">
                        Admin Panel
                    </span>
                </Link>
            </div>

            <!-- Navigation -->
            <nav class="mt-6 px-3">
                <Link
                    v-for="item in navigation"
                    :key="item.name"
                    :href="item.href"
                    :class="[
                        'flex items-center px-4 py-3 mb-1 rounded-xl text-sm font-medium transition-colors',
                        isActive(item.href)
                            ? 'bg-primary-500 text-white'
                            : 'text-gray-300 hover:bg-gray-800 hover:text-white'
                    ]"
                >
                    <span class="text-lg mr-3">{{ item.icon }}</span>
                    {{ item.name }}
                </Link>
            </nav>

            <!-- User Info -->
            <div class="absolute bottom-0 left-0 right-0 p-4 border-t border-gray-800">
                <div class="flex items-center">
                    <div class="w-10 h-10 rounded-full bg-gradient-to-br from-primary-400 to-primary-600 flex items-center justify-center text-white font-semibold">
                        {{ auth.user?.name?.charAt(0) || 'A' }}
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-white">{{ auth.user?.name }}</p>
                        <p class="text-xs text-gray-400">Administrator</p>
                    </div>
                </div>
            </div>
        </aside>

        <!-- Main Content -->
        <div :class="['transition-all duration-300', sidebarOpen ? 'ml-64' : 'ml-0']">
            <!-- Top Header -->
            <header class="bg-white shadow-sm sticky top-0 z-40">
                <div class="flex items-center justify-between h-16 px-6">
                    <button
                        @click="sidebarOpen = !sidebarOpen"
                        class="p-2 rounded-lg text-gray-500 hover:bg-gray-100"
                    >
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                        </svg>
                    </button>

                    <div class="flex items-center space-x-4">
                        <button class="relative p-2 rounded-lg text-gray-500 hover:bg-gray-100">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                            </svg>
                        </button>
                        <Link
                            :href="route('logout')"
                            method="post"
                            as="button"
                            class="px-4 py-2 text-sm text-gray-600 hover:text-gray-900"
                        >
                            Logout
                        </Link>
                    </div>
                </div>
            </header>

            <!-- Page Content -->
            <main class="p-6">
                <slot />
            </main>
        </div>
    </div>
</template>
