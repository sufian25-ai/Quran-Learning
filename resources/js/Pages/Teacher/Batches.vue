<script setup>
import { Head, Link } from '@inertiajs/vue3';
import { ref, computed } from 'vue';
import TeacherLayout from '@/Layouts/TeacherLayout.vue';

const props = defineProps({
    auth: Object,
    batches: {
        type: Array,
        default: () => []
    },
    stats: {
        type: Object,
        default: () => ({})
    }
});

const searchQuery = ref('');

const filteredBatches = computed(() => {
    if (!searchQuery.value) return props.batches;
    const query = searchQuery.value.toLowerCase();
    return props.batches.filter(b => 
        b.name.toLowerCase().includes(query) ||
        b.course?.title?.toLowerCase().includes(query)
    );
});

const getStatusBadge = (status) => {
    const badges = {
        'upcoming': 'bg-blue-100 text-blue-700',
        'active': 'bg-green-100 text-green-700',
        'completed': 'bg-gray-100 text-gray-700',
    };
    return badges[status] || 'bg-gray-100 text-gray-700';
};
</script>

<template>
    <Head title="My Batches | Teacher" />

    <TeacherLayout>
        <template #header>
            <div>
                <h2 class="font-display text-xl font-bold text-gray-900">
                    My Batches 📚
                </h2>
                <p class="text-gray-500 text-sm">Manage your teaching batches</p>
            </div>
        </template>

        <div class="space-y-6">
            <!-- Stats -->
            <div class="grid grid-cols-2 lg:grid-cols-3 gap-4">
                <div class="group bg-gradient-to-br from-emerald-500 to-teal-600 rounded-2xl p-5 text-white transition-all hover:scale-105 hover:shadow-xl">
                    <div class="flex items-center justify-between mb-3">
                        <span class="text-3xl group-hover:animate-bounce">📚</span>
                        <span class="text-2xl font-bold">{{ stats.total_batches || 0 }}</span>
                    </div>
                    <p class="text-emerald-100 text-sm">Total Batches</p>
                </div>
                <div class="group bg-gradient-to-br from-green-500 to-emerald-600 rounded-2xl p-5 text-white transition-all hover:scale-105 hover:shadow-xl">
                    <div class="flex items-center justify-between mb-3">
                        <span class="text-3xl group-hover:animate-bounce">✅</span>
                        <span class="text-2xl font-bold">{{ stats.active_batches || 0 }}</span>
                    </div>
                    <p class="text-green-100 text-sm">Active Batches</p>
                </div>
                <div class="group bg-gradient-to-br from-blue-500 to-indigo-600 rounded-2xl p-5 text-white transition-all hover:scale-105 hover:shadow-xl">
                    <div class="flex items-center justify-between mb-3">
                        <span class="text-3xl group-hover:animate-bounce">👥</span>
                        <span class="text-2xl font-bold">{{ stats.total_students || 0 }}</span>
                    </div>
                    <p class="text-blue-100 text-sm">Total Students</p>
                </div>
            </div>

            <!-- Search -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-4">
                <div class="relative">
                    <input
                        v-model="searchQuery"
                        type="text"
                        placeholder="Search batches..."
                        class="w-full pl-10 pr-4 py-3 rounded-xl border-gray-200 focus:border-emerald-500 focus:ring-emerald-500"
                    />
                    <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                </div>
            </div>

            <!-- Batches Grid -->
            <div v-if="filteredBatches.length" class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
                <Link
                    v-for="batch in filteredBatches"
                    :key="batch.id"
                    :href="`/teacher/batches/${batch.id}`"
                    class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 hover:shadow-lg hover:border-emerald-200 transition-all group"
                >
                    <div class="flex items-start justify-between mb-4">
                        <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-emerald-100 to-teal-100 flex items-center justify-center text-2xl group-hover:scale-110 transition-transform">
                            📚
                        </div>
                        <span :class="['px-2 py-1 text-xs font-medium rounded-full capitalize', getStatusBadge(batch.status)]">
                            {{ batch.status }}
                        </span>
                    </div>
                    <h3 class="font-semibold text-gray-900 mb-1 group-hover:text-emerald-600 transition-colors">
                        {{ batch.name }}
                    </h3>
                    <p class="text-sm text-gray-500 mb-4">{{ batch.course?.title }}</p>
                    <div class="flex items-center justify-between text-sm text-gray-400">
                        <span class="flex items-center gap-1">
                            <span>👥</span>
                            <span>{{ batch.enrolled_students }} students</span>
                        </span>
                        <span class="text-emerald-500 group-hover:translate-x-1 transition-transform">→</span>
                    </div>
                </Link>
            </div>

            <!-- Empty State -->
            <div v-else class="bg-white rounded-2xl shadow-sm border border-gray-100 p-12 text-center">
                <div class="w-20 h-20 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <span class="text-4xl">📚</span>
                </div>
                <h3 class="text-lg font-semibold text-gray-900 mb-2">No Batches Found</h3>
                <p class="text-gray-500">{{ searchQuery ? 'No batches match your search.' : 'You have not been assigned any batches yet.' }}</p>
            </div>
        </div>
    </TeacherLayout>
</template>
