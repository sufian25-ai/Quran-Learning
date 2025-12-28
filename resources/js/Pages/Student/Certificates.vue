<script setup>
import { Head, Link } from '@inertiajs/vue3';
import StudentLayout from '@/Layouts/StudentLayout.vue';

defineProps({
    certificates: {
        type: Array,
        default: () => []
    }
});
</script>

<template>
    <Head title="My Certificates" />

    <StudentLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="font-display text-2xl font-bold text-gray-900">
                        My Certificates 🏅
                    </h2>
                    <p class="text-gray-500 mt-1">Your earned certificates of completion</p>
                </div>
            </div>
        </template>

        <div class="py-8">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div v-if="certificates.length" class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
                    <Link
                        v-for="cert in certificates"
                        :key="cert.id"
                        :href="`/certificates/${cert.id}`"
                        class="bg-white rounded-2xl shadow-soft p-6 hover:shadow-lg transition-all border border-gray-100 hover:border-primary-200"
                    >
                        <div class="flex items-center justify-center mb-4">
                            <div class="w-20 h-20 bg-gradient-to-br from-amber-100 to-amber-50 rounded-full flex items-center justify-center">
                                <span class="text-4xl">🏅</span>
                            </div>
                        </div>
                        <h3 class="font-semibold text-gray-900 text-center mb-2">
                            {{ cert.course?.title || 'Course Certificate' }}
                        </h3>
                        <p class="text-sm text-gray-500 text-center mb-3">
                            Certificate #{{ cert.certificate_number }}
                        </p>
                        <div class="flex justify-between text-xs text-gray-400 border-t pt-3">
                            <span>{{ cert.classes_attended }} classes</span>
                            <span>{{ cert.issued_at }}</span>
                        </div>
                    </Link>
                </div>

                <div v-else class="bg-white rounded-2xl shadow-soft p-12 text-center">
                    <p class="text-6xl mb-4">📜</p>
                    <h3 class="text-xl font-semibold text-gray-900 mb-2">No certificates yet</h3>
                    <p class="text-gray-500 mb-6">Complete a course to earn your first certificate!</p>
                    <Link
                        href="/enrollments"
                        class="inline-flex items-center px-6 py-3 bg-primary-500 hover:bg-primary-600 text-white font-semibold rounded-xl"
                    >
                        View My Courses
                    </Link>
                </div>
            </div>
        </div>
    </StudentLayout>
</template>
