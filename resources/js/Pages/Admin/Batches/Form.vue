<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import { ref, computed, watch } from 'vue';
import AdminLayout from '@/Layouts/AdminLayout.vue';

const props = defineProps({
    auth: Object,
    batch: {
        type: Object,
        default: null
    },
    courses: {
        type: Array,
        default: () => []
    },
    teachers: {
        type: Array,
        default: () => []
    }
});

const isEditing = computed(() => !!props.batch);

const form = useForm({
    name: props.batch?.name || '',
    course_id: props.batch?.course_id || '',
    teacher_id: props.batch?.teacher_id || '',
    start_date: props.batch?.start_date || '',
    end_date: props.batch?.end_date || '',
    max_students: props.batch?.max_students || 10,
    schedule: props.batch?.schedule || [],
    status: props.batch?.status || 'upcoming',
    is_accepting_enrollments: props.batch?.is_accepting_enrollments ?? true,
});

const days = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];
const selectedDays = ref(props.batch?.schedule?.map(s => s.day) || []);
const scheduleTime = ref(props.batch?.schedule?.[0]?.time || '09:00');

watch(selectedDays, (newDays) => {
    form.schedule = newDays.map(day => ({
        day,
        time: scheduleTime.value,
    }));
});

watch(scheduleTime, (newTime) => {
    form.schedule = form.schedule.map(s => ({
        ...s,
        time: newTime,
    }));
});

const toggleDay = (day) => {
    const index = selectedDays.value.indexOf(day);
    if (index > -1) {
        selectedDays.value.splice(index, 1);
    } else {
        selectedDays.value.push(day);
    }
};

const submit = () => {
    if (isEditing.value) {
        form.put(`/admin/batches/${props.batch.id}`);
    } else {
        form.post('/admin/batches');
    }
};
</script>

<template>
    <Head :title="isEditing ? 'Edit Batch' : 'Create Batch'" />

    <AdminLayout>
        <!-- Header -->
        <div class="flex items-center justify-between mb-6">
            <div class="flex items-center">
                <Link href="/admin/batches" class="text-gray-400 hover:text-gray-600 mr-4">
                    ← Back
                </Link>
                <h1 class="text-2xl font-bold text-gray-900">
                    {{ isEditing ? 'Edit Batch' : 'Create New Batch' }}
                </h1>
            </div>
            <div class="flex items-center gap-3">
                <Link href="/admin/batches" class="px-4 py-2 text-gray-600 hover:text-gray-900">
                    Cancel
                </Link>
                <button
                    @click="submit"
                    :disabled="form.processing"
                    class="px-6 py-2 bg-primary-500 hover:bg-primary-600 text-white font-medium rounded-lg disabled:opacity-50"
                >
                    {{ form.processing ? 'Saving...' : (isEditing ? 'Update Batch' : 'Create Batch') }}
                </button>
            </div>
        </div>

        <form @submit.prevent="submit" class="max-w-3xl">
            <!-- Basic Info -->
            <div class="bg-white rounded-xl shadow-sm p-6 mb-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-6">Basic Information</h2>
                
                <div class="space-y-6">
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700 mb-2">Batch Name *</label>
                        <input
                            id="name"
                            v-model="form.name"
                            type="text"
                            required
                            class="w-full rounded-lg border-gray-200 focus:border-primary-500 focus:ring-primary-500"
                            placeholder="e.g., January 2025 - Morning Batch"
                        />
                        <p v-if="form.errors.name" class="mt-1 text-sm text-red-500">{{ form.errors.name }}</p>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label for="course_id" class="block text-sm font-medium text-gray-700 mb-2">Course *</label>
                            <select
                                id="course_id"
                                v-model="form.course_id"
                                required
                                class="w-full rounded-lg border-gray-200 focus:border-primary-500 focus:ring-primary-500"
                            >
                                <option value="">Select a course</option>
                                <option v-for="course in courses" :key="course.id" :value="course.id">
                                    {{ course.title }}
                                </option>
                            </select>
                        </div>
                        <div>
                            <label for="teacher_id" class="block text-sm font-medium text-gray-700 mb-2">Teacher *</label>
                            <select
                                id="teacher_id"
                                v-model="form.teacher_id"
                                required
                                class="w-full rounded-lg border-gray-200 focus:border-primary-500 focus:ring-primary-500"
                            >
                                <option value="">Select a teacher</option>
                                <option v-for="teacher in teachers" :key="teacher.id" :value="teacher.id">
                                    {{ teacher.name }}
                                </option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Dates -->
            <div class="bg-white rounded-xl shadow-sm p-6 mb-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-6">Duration</h2>
                
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label for="start_date" class="block text-sm font-medium text-gray-700 mb-2">Start Date *</label>
                        <input
                            id="start_date"
                            v-model="form.start_date"
                            type="date"
                            required
                            class="w-full rounded-lg border-gray-200 focus:border-primary-500 focus:ring-primary-500"
                        />
                    </div>
                    <div>
                        <label for="end_date" class="block text-sm font-medium text-gray-700 mb-2">End Date</label>
                        <input
                            id="end_date"
                            v-model="form.end_date"
                            type="date"
                            class="w-full rounded-lg border-gray-200 focus:border-primary-500 focus:ring-primary-500"
                        />
                    </div>
                </div>
            </div>

            <!-- Schedule -->
            <div class="bg-white rounded-xl shadow-sm p-6 mb-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-6">Class Schedule</h2>
                
                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-3">Class Days *</label>
                    <div class="flex flex-wrap gap-2">
                        <button
                            v-for="day in days"
                            :key="day"
                            type="button"
                            @click="toggleDay(day)"
                            :class="[
                                'px-4 py-2 rounded-lg text-sm font-medium transition-colors',
                                selectedDays.includes(day)
                                    ? 'bg-primary-500 text-white'
                                    : 'bg-gray-100 text-gray-600 hover:bg-gray-200'
                            ]"
                        >
                            {{ day.slice(0, 3) }}
                        </button>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label for="schedule_time" class="block text-sm font-medium text-gray-700 mb-2">Class Time</label>
                        <input
                            id="schedule_time"
                            v-model="scheduleTime"
                            type="time"
                            class="w-full rounded-lg border-gray-200 focus:border-primary-500 focus:ring-primary-500"
                        />
                    </div>
                    <div>
                        <label for="max_students" class="block text-sm font-medium text-gray-700 mb-2">Max Students</label>
                        <input
                            id="max_students"
                            v-model.number="form.max_students"
                            type="number"
                            min="1"
                            max="50"
                            class="w-full rounded-lg border-gray-200 focus:border-primary-500 focus:ring-primary-500"
                        />
                    </div>
                </div>
            </div>

            <!-- Status -->
            <div class="bg-white rounded-xl shadow-sm p-6 mb-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-6">Status</h2>
                
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label for="status" class="block text-sm font-medium text-gray-700 mb-2">Batch Status</label>
                        <select
                            id="status"
                            v-model="form.status"
                            class="w-full rounded-lg border-gray-200 focus:border-primary-500 focus:ring-primary-500"
                        >
                            <option value="upcoming">Upcoming</option>
                            <option value="active">Active</option>
                            <option value="completed">Completed</option>
                            <option value="cancelled">Cancelled</option>
                        </select>
                    </div>
                    <div class="flex items-center">
                        <label class="flex items-center cursor-pointer">
                            <input
                                v-model="form.is_accepting_enrollments"
                                type="checkbox"
                                class="rounded border-gray-300 text-primary-500 focus:ring-primary-500"
                            />
                            <span class="ml-2 text-sm text-gray-700">Accepting Enrollments</span>
                        </label>
                    </div>
                </div>
            </div>

            <!-- Submit -->
            <div class="flex justify-end gap-3">
                <Link href="/admin/batches" class="px-6 py-3 text-gray-600 hover:text-gray-900">
                    Cancel
                </Link>
                <button
                    type="submit"
                    :disabled="form.processing"
                    class="px-8 py-3 bg-primary-500 hover:bg-primary-600 text-white font-medium rounded-lg disabled:opacity-50"
                >
                    {{ form.processing ? 'Saving...' : (isEditing ? 'Update Batch' : 'Create Batch') }}
                </button>
            </div>
        </form>
    </AdminLayout>
</template>
