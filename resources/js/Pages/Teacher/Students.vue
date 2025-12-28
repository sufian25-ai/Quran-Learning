<script setup>
import { Head, Link } from '@inertiajs/vue3';
import { ref, computed } from 'vue';
import TeacherLayout from '@/Layouts/TeacherLayout.vue';

const props = defineProps({
    auth: Object,
    students: {
        type: Array,
        default: () => []
    },
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
const selectedBatch = ref('');

const filteredStudents = computed(() => {
    let result = props.students;
    
    if (selectedBatch.value) {
        result = result.filter(s => s.batch_id == selectedBatch.value);
    }
    
    if (searchQuery.value) {
        const query = searchQuery.value.toLowerCase();
        result = result.filter(s => 
            s.name.toLowerCase().includes(query) ||
            s.email.toLowerCase().includes(query)
        );
    }
    
    return result;
});
</script>

<template>
    <Head title="Students | Teacher" />

    <TeacherLayout>
        <template #header>
            <div>
                <h2 class="font-display text-xl font-bold text-gray-900">
                    My Students 👥
                </h2>
                <p class="text-gray-500 text-sm">All students enrolled in your batches</p>
            </div>
        </template>

        <div class="space-y-6">
            <!-- Stats -->
            <div class="grid grid-cols-2 gap-4">
                <div class="group bg-gradient-to-br from-emerald-500 to-teal-600 rounded-2xl p-5 text-white transition-all hover:scale-105 hover:shadow-xl">
                    <div class="flex items-center justify-between mb-3">
                        <span class="text-3xl group-hover:animate-bounce">👥</span>
                        <span class="text-2xl font-bold">{{ stats.total_students || 0 }}</span>
                    </div>
                    <p class="text-emerald-100 text-sm">Total Students</p>
                </div>
                <div class="group bg-gradient-to-br from-blue-500 to-indigo-600 rounded-2xl p-5 text-white transition-all hover:scale-105 hover:shadow-xl">
                    <div class="flex items-center justify-between mb-3">
                        <span class="text-3xl group-hover:animate-bounce">📚</span>
                        <span class="text-2xl font-bold">{{ stats.total_batches || 0 }}</span>
                    </div>
                    <p class="text-blue-100 text-sm">Active Batches</p>
                </div>
            </div>

            <!-- Filters -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-4 flex gap-4">
                <div class="flex-1 relative">
                    <input
                        v-model="searchQuery"
                        type="text"
                        placeholder="Search students..."
                        class="w-full pl-10 pr-4 py-3 rounded-xl border-gray-200 focus:border-emerald-500 focus:ring-emerald-500"
                    />
                    <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                </div>
                <select
                    v-model="selectedBatch"
                    class="rounded-xl border-gray-200 focus:border-emerald-500 focus:ring-emerald-500"
                >
                    <option value="">All Batches</option>
                    <option v-for="batch in batches" :key="batch.id" :value="batch.id">
                        {{ batch.name }} ({{ batch.enrollments_count }})
                    </option>
                </select>
            </div>

            <!-- Students Grid -->
            <div v-if="filteredStudents.length" class="grid md:grid-cols-2 lg:grid-cols-3 gap-4">
                <div
                    v-for="student in filteredStudents"
                    :key="student.id"
                    class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5 hover:shadow-md hover:border-emerald-200 transition-all group"
                >
                    <div class="flex items-center gap-4 mb-4">
                        <div class="w-12 h-12 rounded-full bg-gradient-to-br from-emerald-400 to-teal-500 flex items-center justify-center text-white font-bold text-lg">
                            {{ student.name?.charAt(0).toUpperCase() }}
                        </div>
                        <div>
                            <h4 class="font-medium text-gray-900 group-hover:text-emerald-600 transition-colors">{{ student.name }}</h4>
                            <p class="text-sm text-gray-500">{{ student.email }}</p>
                        </div>
                    </div>
                    <div class="space-y-2 text-sm">
                        <div class="flex justify-between">
                            <span class="text-gray-500">Batch</span>
                            <span class="text-gray-900 font-medium">{{ student.batch_name }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-500">Progress</span>
                            <span class="text-gray-900 font-medium">{{ student.progress }}%</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-500">Attendance</span>
                            <span class="text-emerald-600 font-medium">{{ student.attendance_rate }}%</span>
                        </div>
                    </div>
                    <div class="w-full bg-gray-100 rounded-full h-2 mt-4">
                        <div
                            class="bg-gradient-to-r from-emerald-400 to-teal-500 h-2 rounded-full transition-all"
                            :style="{ width: `${student.progress}%` }"
                        ></div>
                    </div>
                </div>
            </div>
            <div v-else class="bg-white rounded-2xl shadow-sm border border-gray-100 p-12 text-center">
                <div class="w-20 h-20 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <span class="text-4xl">👥</span>
                </div>
                <h3 class="text-lg font-semibold text-gray-900 mb-2">No Students Found</h3>
                <p class="text-gray-500">{{ searchQuery || selectedBatch ? 'Try adjusting your filters.' : 'No students enrolled in your batches yet.' }}</p>
            </div>
        </div>
    </TeacherLayout>
</template>
