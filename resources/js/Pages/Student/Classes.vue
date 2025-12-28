<script setup>
import { Head, Link } from '@inertiajs/vue3';
import StudentLayout from '@/Layouts/StudentLayout.vue';
</script>

<template>
    <Head title="My Schedule" />

    <StudentLayout>
        <template #header>
            <h2 class="font-display text-2xl font-bold text-gray-900">
                My Schedule 📅
            </h2>
        </template>

        <div class="py-8">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="bg-white rounded-2xl shadow-soft p-12 text-center">
                    <span class="text-6xl mb-4 block">🚧</span>
                    <h3 class="text-xl font-semibold text-gray-900 mb-2">Coming Soon</h3>
                    <p class="text-gray-500 mb-6">This page is under construction.</p>
                    <Link href="/dashboard" class="inline-flex items-center px-6 py-3 bg-primary-500 hover:bg-primary-600 text-white font-semibold rounded-xl">
                        Back to Dashboard
                    </Link>
                </div>
            </div>
        </div>
    </StudentLayout>
</template>
