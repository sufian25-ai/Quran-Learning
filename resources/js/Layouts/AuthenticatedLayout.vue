<script setup>
import { ref } from 'vue';
import Dropdown from '@/Components/Dropdown.vue';
import DropdownLink from '@/Components/DropdownLink.vue';
import NavLink from '@/Components/NavLink.vue';
import ResponsiveNavLink from '@/Components/ResponsiveNavLink.vue';
import { Link } from '@inertiajs/vue3';

const showingNavigationDropdown = ref(false);

const navigation = [
    { name: 'Dashboard', href: 'dashboard', icon: '🏠' },
    { name: 'My Courses', href: 'enrollments', icon: '📚' },
    { name: 'Schedule', href: 'classes', icon: '📅' },
    { name: 'Resources', href: 'resources', icon: '📁' },
    { name: 'Recordings', href: 'recordings', icon: '🎥' },
    { name: 'Leaderboard', href: 'leaderboard', icon: '🏆' },
];
</script>

<template>
    <div class="min-h-screen bg-gray-50">
        <!-- Top Navigation -->
        <nav class="fixed top-0 left-0 right-0 z-50 bg-white border-b border-gray-200">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex h-16 justify-between items-center">
                    <!-- Logo -->
                    <div class="flex items-center">
                        <Link href="/" class="flex items-center space-x-3">
                            <div class="w-10 h-10 bg-gradient-to-br from-primary-500 to-primary-600 rounded-xl flex items-center justify-center">
                                <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M12 2L2 7l10 5 10-5-10-5zM2 17l10 5 10-5M2 12l10 5 10-5"/>
                                </svg>
                            </div>
                            <span class="font-display text-xl font-bold text-gray-900 hidden sm:block">
                                Quran<span class="text-primary-500">Learn</span>
                            </span>
                        </Link>

                        <!-- Navigation Links -->
                        <div class="hidden md:flex items-center ml-10 space-x-1">
                            <NavLink
                                v-for="item in navigation"
                                :key="item.name"
                                :href="route(item.href)"
                                :active="route().current(item.href + '*')"
                                class="px-4 py-2 rounded-lg text-sm font-medium transition-colors"
                            >
                                <span class="mr-2">{{ item.icon }}</span>
                                {{ item.name }}
                            </NavLink>
                        </div>
                    </div>

                    <!-- Right Side -->
                    <div class="flex items-center space-x-4">
                        <!-- Points & Streak -->
                        <div class="hidden sm:flex items-center space-x-4">
                            <div class="flex items-center text-sm text-gray-600">
                                <span class="text-lg mr-1">⭐</span>
                                <span class="font-medium">{{ $page.props.auth?.user?.points || 0 }}</span>
                            </div>
                            <div class="flex items-center text-sm text-orange-500">
                                <span class="text-lg mr-1">🔥</span>
                                <span class="font-medium">{{ $page.props.auth?.user?.learningStreak?.current_streak || 0 }}</span>
                            </div>
                        </div>

                        <!-- Notifications -->
                        <Link href="/notifications" class="relative p-2 rounded-lg text-gray-500 hover:bg-gray-100 transition-colors">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                            </svg>
                            <span class="absolute top-1 right-1 w-2 h-2 bg-red-500 rounded-full"></span>
                        </Link>

                        <!-- User Dropdown -->
                        <Dropdown align="right" width="48">
                            <template #trigger>
                                <button class="flex items-center space-x-3 p-1.5 rounded-xl hover:bg-gray-100 transition-colors">
                                    <div class="w-9 h-9 rounded-full bg-gradient-to-br from-primary-400 to-primary-600 flex items-center justify-center text-white font-semibold">
                                        {{ $page.props.auth.user.name.charAt(0) }}
                                    </div>
                                    <div class="hidden lg:block text-left">
                                        <p class="text-sm font-medium text-gray-900">{{ $page.props.auth.user.name }}</p>
                                        <p class="text-xs text-gray-500">{{ $page.props.auth?.roles?.[0]?.charAt(0).toUpperCase() + $page.props.auth?.roles?.[0]?.slice(1) || 'User' }}</p>
                                    </div>
                                    <svg class="w-4 h-4 text-gray-400 hidden lg:block" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                    </svg>
                                </button>
                            </template>

                            <template #content>
                                <div class="px-4 py-3 border-b border-gray-100">
                                    <p class="text-sm font-medium text-gray-900">{{ $page.props.auth.user.name }}</p>
                                    <p class="text-xs text-gray-500">{{ $page.props.auth.user.email }}</p>
                                </div>
                                <DropdownLink :href="route('profile.edit')">
                                    <span class="mr-2">👤</span> Profile Settings
                                </DropdownLink>
                                <DropdownLink href="/notifications">
                                    <span class="mr-2">🔔</span> Notifications
                                </DropdownLink>
                                <DropdownLink href="/leaderboard">
                                    <span class="mr-2">🏆</span> Leaderboard
                                </DropdownLink>
                                <DropdownLink href="/settings">
                                    <span class="mr-2">⚙️</span> Settings
                                </DropdownLink>
                                <DropdownLink href="/support">
                                    <span class="mr-2">💬</span> Get Help
                                </DropdownLink>
                                <div class="border-t border-gray-100 my-1"></div>
                                <DropdownLink :href="route('logout')" method="post" as="button">
                                    <span class="mr-2">🚪</span> Log Out
                                </DropdownLink>
                            </template>
                        </Dropdown>

                        <!-- Mobile Menu Button -->
                        <button
                            @click="showingNavigationDropdown = !showingNavigationDropdown"
                            class="md:hidden p-2 rounded-lg text-gray-500 hover:bg-gray-100 transition-colors"
                        >
                            <svg v-if="!showingNavigationDropdown" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                            </svg>
                            <svg v-else class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Mobile Navigation -->
            <div
                v-if="showingNavigationDropdown"
                class="md:hidden border-t border-gray-200 bg-white"
            >
                <div class="px-4 py-3 space-y-1">
                    <ResponsiveNavLink
                        v-for="item in navigation"
                        :key="item.name"
                        :href="route(item.href)"
                        :active="route().current(item.href + '*')"
                    >
                        <span class="mr-2">{{ item.icon }}</span>
                        {{ item.name }}
                    </ResponsiveNavLink>
                </div>
                <div class="px-4 py-3 border-t border-gray-200 flex justify-around">
                    <div class="flex items-center text-sm text-gray-600">
                        <span class="text-lg mr-1">⭐</span>
                        <span class="font-medium">{{ $page.props.auth?.user?.points || 0 }} pts</span>
                    </div>
                    <div class="flex items-center text-sm text-orange-500">
                        <span class="text-lg mr-1">🔥</span>
                        <span class="font-medium">{{ $page.props.auth?.user?.learningStreak?.current_streak || 0 }} days</span>
                    </div>
                </div>
            </div>
        </nav>

        <!-- Page Content -->
        <div class="pt-16">
            <!-- Page Header -->
            <header v-if="$slots.header" class="bg-white border-b border-gray-200">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
                    <slot name="header" />
                </div>
            </header>

            <!-- Main Content -->
            <main class="bg-gray-50 min-h-[calc(100vh-4rem)]">
                <slot />
            </main>
        </div>
    </div>
</template>
