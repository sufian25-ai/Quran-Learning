<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import { ref, computed } from 'vue';
import TeacherLayout from '@/Layouts/TeacherLayout.vue';

const props = defineProps({
    auth: Object,
    classSession: {
        type: Object,
        required: true
    },
    students: {
        type: Array,
        default: () => []
    }
});

const form = useForm({
    attendance: props.students.reduce((acc, student) => {
        acc[student.id] = student.attendance_status || 'present';
        return acc;
    }, {}),
    class_summary: props.classSession.summary || '',
    topics_covered: props.classSession.topics_covered || '',
});

const submit = () => {
    form.post(`/teacher/classes/${props.classSession.id}/attendance`, {
        preserveScroll: true,
    });
};

const attendanceOptions = [
    { value: 'present', label: 'Present', color: 'bg-green-500', icon: '✅' },
    { value: 'late', label: 'Late', color: 'bg-yellow-500', icon: '⏰' },
    { value: 'absent', label: 'Absent', color: 'bg-red-500', icon: '❌' },
    { value: 'excused', label: 'Excused', color: 'bg-blue-500', icon: '📝' },
];

const getAttendanceColor = (status) => {
    const colors = {
        'present': 'bg-green-100 text-green-700 border-green-300',
        'late': 'bg-yellow-100 text-yellow-700 border-yellow-300',
        'absent': 'bg-red-100 text-red-700 border-red-300',
        'excused': 'bg-blue-100 text-blue-700 border-blue-300',
    };
    return colors[status] || 'bg-gray-100 text-gray-700 border-gray-200';
};

const markAllPresent = () => {
    props.students.forEach(student => {
        form.attendance[student.id] = 'present';
    });
};

const presentCount = computed(() => 
    Object.values(form.attendance).filter(s => s === 'present' || s === 'late').length
);

const absentCount = computed(() => 
    Object.values(form.attendance).filter(s => s === 'absent').length
);
</script>

<template>
    <Head :title="`Attendance - ${classSession.title} | Teacher`" />

    <TeacherLayout>
        <template #header>
            <div class="flex items-center gap-4">
                <Link href="/teacher/dashboard" class="text-gray-400 hover:text-gray-600">
                    ← Back
                </Link>
                <div>
                    <h2 class="font-display text-xl font-bold text-gray-900">
                        Mark Attendance ✅
                    </h2>
                    <p class="text-gray-500 text-sm">{{ classSession.title }}</p>
                </div>
            </div>
        </template>

        <div class="space-y-6">
            <!-- Class Info Banner -->
            <div class="bg-gradient-to-r from-emerald-500 to-teal-600 rounded-2xl p-6 text-white relative overflow-hidden">
                <div class="absolute top-0 right-0 w-32 h-32 bg-white/10 rounded-full -translate-y-1/2 translate-x-1/2"></div>
                <div class="relative z-10">
                    <h2 class="text-2xl font-bold mb-2">{{ classSession.title }}</h2>
                    <p class="text-emerald-100">{{ classSession.batch?.name }} • {{ classSession.course?.title }}</p>
                    <div class="flex items-center gap-6 mt-4 text-sm text-emerald-100">
                        <span>📅 {{ classSession.date }}</span>
                        <span>🕐 {{ classSession.time }}</span>
                        <span>⏱️ {{ classSession.duration_minutes }} min</span>
                        <span>👥 {{ students.length }} students</span>
                    </div>
                </div>
            </div>

            <form @submit.prevent="submit">
                <div class="grid lg:grid-cols-3 gap-6">
                    <!-- Student List -->
                    <div class="lg:col-span-2">
                        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                            <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
                                <h3 class="font-semibold text-gray-900">Students ({{ students.length }})</h3>
                                <button
                                    type="button"
                                    @click="markAllPresent"
                                    class="text-sm text-emerald-500 hover:text-emerald-600 font-medium"
                                >
                                    ✅ Mark All Present
                                </button>
                            </div>

                            <div class="divide-y divide-gray-100">
                                <div
                                    v-for="student in students"
                                    :key="student.id"
                                    class="p-4 flex items-center justify-between hover:bg-gray-50 transition-colors"
                                >
                                    <div class="flex items-center gap-4">
                                        <div class="w-12 h-12 rounded-full bg-gradient-to-br from-emerald-400 to-teal-500 flex items-center justify-center text-white font-bold">
                                            {{ student.name?.charAt(0).toUpperCase() }}
                                        </div>
                                        <div>
                                            <p class="font-medium text-gray-900">{{ student.name }}</p>
                                            <p class="text-sm text-gray-500">{{ student.email }}</p>
                                        </div>
                                    </div>

                                    <div class="flex items-center gap-2">
                                        <button
                                            v-for="option in attendanceOptions"
                                            :key="option.value"
                                            type="button"
                                            @click="form.attendance[student.id] = option.value"
                                            :class="[
                                                'px-3 py-1.5 text-xs font-medium rounded-lg border-2 transition-all',
                                                form.attendance[student.id] === option.value
                                                    ? getAttendanceColor(option.value)
                                                    : 'bg-gray-50 text-gray-500 border-gray-200 hover:bg-gray-100'
                                            ]"
                                        >
                                            {{ option.label }}
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <div v-if="!students.length" class="p-12 text-center">
                                <span class="text-4xl mb-4 block">👥</span>
                                <p class="text-gray-500">No students enrolled in this class</p>
                            </div>
                        </div>
                    </div>

                    <!-- Summary & Stats -->
                    <div class="space-y-6">
                        <!-- Attendance Stats -->
                        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                            <h3 class="font-semibold text-gray-900 mb-4">📊 Attendance Summary</h3>
                            <div class="space-y-3">
                                <div class="flex items-center justify-between p-3 bg-green-50 rounded-xl">
                                    <span class="text-gray-700">Present/Late</span>
                                    <span class="font-bold text-green-600 text-lg">{{ presentCount }}</span>
                                </div>
                                <div class="flex items-center justify-between p-3 bg-red-50 rounded-xl">
                                    <span class="text-gray-700">Absent</span>
                                    <span class="font-bold text-red-600 text-lg">{{ absentCount }}</span>
                                </div>
                                <div class="border-t pt-3 flex items-center justify-between">
                                    <span class="font-medium text-gray-900">Attendance Rate</span>
                                    <span class="font-bold text-emerald-600 text-xl">
                                        {{ students.length ? Math.round((presentCount / students.length) * 100) : 0 }}%
                                    </span>
                                </div>
                            </div>
                        </div>

                        <!-- Class Summary -->
                        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                            <h3 class="font-semibold text-gray-900 mb-4">📝 Class Summary</h3>
                            <div class="space-y-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Topics Covered</label>
                                    <input
                                        v-model="form.topics_covered"
                                        type="text"
                                        placeholder="e.g., Surah Al-Fatiha, Tajweed rules"
                                        class="w-full rounded-xl border-gray-200 focus:border-emerald-500 focus:ring-emerald-500"
                                    />
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Notes</label>
                                    <textarea
                                        v-model="form.class_summary"
                                        rows="4"
                                        placeholder="Any notes about this class..."
                                        class="w-full rounded-xl border-gray-200 focus:border-emerald-500 focus:ring-emerald-500"
                                    ></textarea>
                                </div>
                            </div>
                        </div>

                        <!-- Submit -->
                        <button
                            type="submit"
                            :disabled="form.processing"
                            class="w-full py-4 bg-gradient-to-r from-emerald-500 to-teal-600 hover:from-emerald-600 hover:to-teal-700 text-white font-semibold rounded-xl transition-all disabled:opacity-50 shadow-lg hover:shadow-xl"
                        >
                            {{ form.processing ? 'Saving...' : '💾 Save Attendance' }}
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </TeacherLayout>
</template>
