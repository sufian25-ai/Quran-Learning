<script setup>
import { Head, Link } from '@inertiajs/vue3';
import TeacherLayout from '@/Layouts/TeacherLayout.vue';

defineProps({
    auth: Object,
    classes: {
        type: Array,
        default: () => []
    }
});

const formatTime = (dateString) => {
    const date = new Date(dateString);
    return date.toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit' });
};

const formatDate = (dateString) => {
    const date = new Date(dateString);
    return date.toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' });
};
</script>

<template>
    <Head title="Attendance | Teacher" />

    <TeacherLayout>
        <template #header>
            <div>
                <h2 class="font-display text-xl font-bold text-gray-900">
                    Attendance ✅
                </h2>
                <p class="text-gray-500 text-sm">Mark attendance for your classes</p>
            </div>
        </template>

        <div class="space-y-6">
            <div v-if="classes.length" class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="divide-y divide-gray-100">
                    <Link
                        v-for="class_ in classes"
                        :key="class_.id"
                        :href="`/teacher/classes/${class_.id}/attendance`"
                        class="flex items-center justify-between p-4 hover:bg-gray-50 transition-colors group"
                    >
                        <div class="flex items-center gap-4">
                            <div :class="[
                                'w-12 h-12 rounded-xl flex items-center justify-center text-white',
                                class_.attendance_marked ? 'bg-green-500' : 'bg-amber-500'
                            ]">
                                <span class="text-xl">{{ class_.attendance_marked ? '✓' : '!' }}</span>
                            </div>
                            <div>
                                <h4 class="font-medium text-gray-900 group-hover:text-emerald-600 transition-colors">{{ class_.title }}</h4>
                                <p class="text-sm text-gray-500">{{ class_.batch?.name }} • {{ formatDate(class_.scheduled_at) }} at {{ formatTime(class_.scheduled_at) }}</p>
                            </div>
                        </div>
                        <div class="flex items-center gap-3">
                            <span :class="[
                                'px-3 py-1 text-xs font-medium rounded-full',
                                class_.attendance_marked ? 'bg-green-100 text-green-700' : 'bg-amber-100 text-amber-700'
                            ]">
                                {{ class_.attendance_marked ? 'Marked' : 'Pending' }}
                            </span>
                            <span class="text-gray-400 group-hover:text-emerald-500 transition-colors">→</span>
                        </div>
                    </Link>
                </div>
            </div>
            <div v-else class="bg-white rounded-2xl shadow-sm border border-gray-100 p-12 text-center">
                <div class="w-20 h-20 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <span class="text-4xl">✅</span>
                </div>
                <h3 class="text-lg font-semibold text-gray-900 mb-2">No Classes Yet</h3>
                <p class="text-gray-500">You haven't conducted any classes yet.</p>
            </div>
        </div>
    </TeacherLayout>
</template>
