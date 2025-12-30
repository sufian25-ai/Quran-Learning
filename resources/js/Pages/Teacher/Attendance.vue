<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import { ref, computed } from 'vue';
import TeacherLayout from '@/Layouts/TeacherLayout.vue';

const props = defineProps({
    classSession: Object,
    students: Array,
    existingAttendance: {
        type: Object,
        default: () => ({})
    }
});

const form = useForm({
    attendance: props.students.reduce((acc, student) => {
        acc[student.id] = props.existingAttendance[student.id] || 'present';
        return acc;
    }, {}),
    class_summary: '',
    topics_covered: '',
});

const attendanceCounts = computed(() => {
    const counts = { present: 0, absent: 0, late: 0 };
    Object.values(form.attendance).forEach(status => {
        if (counts[status] !== undefined) counts[status]++;
    });
    return counts;
});

const setAllStatus = (status) => {
    props.students.forEach(student => {
        form.attendance[student.id] = status;
    });
};

const submit = () => {
    form.post(route('teacher.attendance.store', props.classSession.id), {
        preserveScroll: true,
    });
};

const getStatusColor = (status) => {
    return {
        present: 'bg-emerald-500 text-white',
        absent: 'bg-red-500 text-white',
        late: 'bg-amber-500 text-white',
    }[status] || 'bg-gray-200 text-gray-700';
};

const getButtonClass = (studentId, status) => {
    const isActive = form.attendance[studentId] === status;
    const colors = {
        present: isActive ? 'bg-emerald-500 text-white shadow-lg' : 'bg-emerald-100 text-emerald-700 hover:bg-emerald-200',
        absent: isActive ? 'bg-red-500 text-white shadow-lg' : 'bg-red-100 text-red-700 hover:bg-red-200',
        late: isActive ? 'bg-amber-500 text-white shadow-lg' : 'bg-amber-100 text-amber-700 hover:bg-amber-200',
    };
    return colors[status];
};
</script>

<template>
    <Head title="Mark Attendance" />

    <TeacherLayout>
        <div class="space-y-6">
            <!-- Header -->
            <div class="bg-gradient-to-r from-emerald-600 to-teal-500 rounded-2xl p-6 text-white relative overflow-hidden">
                <div class="absolute top-0 right-0 w-32 h-32 bg-white/10 rounded-full -translate-y-1/2 translate-x-1/2"></div>
                <div class="relative z-10">
                    <Link :href="route('teacher.dashboard')" class="inline-flex items-center gap-2 text-emerald-100 hover:text-white mb-4 text-sm">
                        ← Back to Dashboard
                    </Link>
                    <h1 class="text-2xl font-bold mb-2">{{ classSession.title }}</h1>
                    <p class="text-emerald-100">
                        {{ classSession.batch?.name }} • {{ classSession.date }} at {{ classSession.time }}
                    </p>
                </div>
            </div>

            <!-- Quick Stats -->
            <div class="grid grid-cols-3 gap-4">
                <div class="bg-emerald-50 dark:bg-emerald-900/30 rounded-xl p-4 text-center">
                    <p class="text-3xl font-bold text-emerald-600 dark:text-emerald-400">{{ attendanceCounts.present }}</p>
                    <p class="text-sm text-emerald-700 dark:text-emerald-300">Present</p>
                </div>
                <div class="bg-red-50 dark:bg-red-900/30 rounded-xl p-4 text-center">
                    <p class="text-3xl font-bold text-red-600 dark:text-red-400">{{ attendanceCounts.absent }}</p>
                    <p class="text-sm text-red-700 dark:text-red-300">Absent</p>
                </div>
                <div class="bg-amber-50 dark:bg-amber-900/30 rounded-xl p-4 text-center">
                    <p class="text-3xl font-bold text-amber-600 dark:text-amber-400">{{ attendanceCounts.late }}</p>
                    <p class="text-sm text-amber-700 dark:text-amber-300">Late</p>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="bg-white dark:bg-gray-800 rounded-xl p-4 border border-gray-100 dark:border-gray-700">
                <p class="text-sm text-gray-500 dark:text-gray-400 mb-3">Quick Actions:</p>
                <div class="flex gap-2 flex-wrap">
                    <button @click="setAllStatus('present')" class="px-4 py-2 bg-emerald-100 text-emerald-700 rounded-lg hover:bg-emerald-200 transition text-sm font-medium">
                        ✓ Mark All Present
                    </button>
                    <button @click="setAllStatus('absent')" class="px-4 py-2 bg-red-100 text-red-700 rounded-lg hover:bg-red-200 transition text-sm font-medium">
                        ✗ Mark All Absent
                    </button>
                </div>
            </div>

            <!-- Students List -->
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
                <div class="p-5 border-b border-gray-100 dark:border-gray-700">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                        Students ({{ students.length }})
                    </h3>
                </div>
                
                <div v-if="students.length === 0" class="p-12 text-center text-gray-500">
                    <span class="text-4xl block mb-4">👥</span>
                    <p>No students enrolled in this batch</p>
                </div>
                
                <div v-else class="divide-y divide-gray-100 dark:divide-gray-700">
                    <div v-for="student in students" :key="student.id" 
                         class="p-4 flex items-center justify-between hover:bg-gray-50 dark:hover:bg-gray-700/50 transition">
                        <div class="flex items-center gap-4">
                            <div class="w-12 h-12 rounded-full bg-gradient-to-br from-emerald-400 to-teal-500 flex items-center justify-center text-white font-bold text-lg">
                                {{ student.name?.charAt(0).toUpperCase() }}
                            </div>
                            <div>
                                <p class="font-medium text-gray-900 dark:text-white">{{ student.name }}</p>
                                <p class="text-sm text-gray-500 dark:text-gray-400">{{ student.email }}</p>
                            </div>
                        </div>
                        <div class="flex gap-2">
                            <button 
                                v-for="status in ['present', 'absent', 'late']" 
                                :key="status"
                                @click="form.attendance[student.id] = status"
                                :class="['px-4 py-2 rounded-lg font-medium transition-all text-sm capitalize', getButtonClass(student.id, status)]"
                            >
                                {{ status }}
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Class Notes -->
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 p-6">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Class Notes</h3>
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Topics Covered</label>
                        <input 
                            v-model="form.topics_covered" 
                            type="text"
                            placeholder="e.g., Surah Al-Fatiha, Tajweed rules..."
                            class="w-full rounded-xl border-gray-200 dark:border-gray-600 dark:bg-gray-700 focus:border-emerald-500 focus:ring-emerald-500"
                        />
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Class Summary</label>
                        <textarea 
                            v-model="form.class_summary" 
                            rows="3"
                            placeholder="Write a brief summary of the class..."
                            class="w-full rounded-xl border-gray-200 dark:border-gray-600 dark:bg-gray-700 focus:border-emerald-500 focus:ring-emerald-500"
                        ></textarea>
                    </div>
                </div>
            </div>

            <!-- Submit Button -->
            <div class="flex justify-end gap-4">
                <Link :href="route('teacher.dashboard')" class="px-6 py-3 bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 font-semibold rounded-xl hover:bg-gray-200 dark:hover:bg-gray-600 transition">
                    Cancel
                </Link>
                <button 
                    @click="submit"
                    :disabled="form.processing"
                    class="px-8 py-3 bg-gradient-to-r from-emerald-500 to-teal-500 text-white font-semibold rounded-xl hover:from-emerald-600 hover:to-teal-600 transition shadow-lg disabled:opacity-50"
                >
                    <span v-if="form.processing">Saving...</span>
                    <span v-else>✓ Save Attendance</span>
                </button>
            </div>
        </div>
    </TeacherLayout>
</template>
