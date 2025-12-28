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
    return date.toLocaleDateString('en-US', { weekday: 'short', month: 'short', day: 'numeric' });
};

const getStatusColor = (status) => {
    const colors = {
        'scheduled': 'bg-blue-100 text-blue-700',
        'live': 'bg-green-100 text-green-700 animate-pulse',
        'completed': 'bg-gray-100 text-gray-700',
        'cancelled': 'bg-red-100 text-red-700',
    };
    return colors[status] || 'bg-gray-100 text-gray-700';
};

const groupByDate = (classes) => {
    const groups = {};
    classes.forEach(c => {
        const date = new Date(c.scheduled_at).toDateString();
        if (!groups[date]) groups[date] = [];
        groups[date].push(c);
    });
    return groups;
};
</script>

<template>
    <Head title="Schedule | Teacher" />

    <TeacherLayout>
        <template #header>
            <div>
                <h2 class="font-display text-xl font-bold text-gray-900">
                    Full Schedule 📅
                </h2>
                <p class="text-gray-500 text-sm">Your upcoming classes for the next 2 weeks</p>
            </div>
        </template>

        <div class="space-y-6">
            <div v-if="classes.length">
                <div v-for="(dayClasses, date) in groupByDate(classes)" :key="date" class="mb-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-3 flex items-center gap-2">
                        <span class="w-8 h-8 rounded-lg bg-emerald-100 text-emerald-600 flex items-center justify-center text-sm">
                            {{ new Date(date).getDate() }}
                        </span>
                        {{ formatDate(date) }}
                    </h3>
                    <div class="space-y-3">
                        <div
                            v-for="class_ in dayClasses"
                            :key="class_.id"
                            class="bg-white rounded-2xl shadow-sm border border-gray-100 p-4 flex items-center justify-between hover:shadow-md hover:border-emerald-200 transition-all group"
                        >
                            <div class="flex items-center gap-4">
                                <div class="w-16 h-16 rounded-xl bg-gradient-to-br from-emerald-500 to-teal-600 flex flex-col items-center justify-center text-white">
                                    <span class="text-lg font-bold">{{ formatTime(class_.scheduled_at).split(':')[0] }}</span>
                                    <span class="text-xs">{{ formatTime(class_.scheduled_at).split(' ')[1] }}</span>
                                </div>
                                <div>
                                    <h4 class="font-medium text-gray-900 group-hover:text-emerald-600 transition-colors">{{ class_.title }}</h4>
                                    <p class="text-sm text-gray-500">{{ class_.batch?.name }} • {{ class_.batch?.course?.title }}</p>
                                    <p class="text-xs text-gray-400 mt-1">{{ class_.duration_minutes }} minutes</p>
                                </div>
                            </div>
                            <div class="flex items-center gap-3">
                                <span :class="['px-3 py-1 text-xs font-medium rounded-full capitalize', getStatusColor(class_.status)]">
                                    {{ class_.status }}
                                </span>
                                <a
                                    v-if="class_.zoom_start_url"
                                    :href="class_.zoom_start_url"
                                    target="_blank"
                                    class="px-4 py-2 bg-emerald-500 hover:bg-emerald-600 text-white text-sm font-medium rounded-lg transition-colors"
                                >
                                    Start
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div v-else class="bg-white rounded-2xl shadow-sm border border-gray-100 p-12 text-center">
                <div class="w-20 h-20 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <span class="text-4xl">📅</span>
                </div>
                <h3 class="text-lg font-semibold text-gray-900 mb-2">No Scheduled Classes</h3>
                <p class="text-gray-500">You don't have any classes scheduled for the next 2 weeks.</p>
            </div>
        </div>
    </TeacherLayout>
</template>
