<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import { ref, computed } from 'vue';
import TeacherLayout from '@/Layouts/TeacherLayout.vue';

const props = defineProps({
    batches: Array,
    teacherTimezone: {
        type: String,
        default: 'Asia/Dhaka'
    },
});

const form = useForm({
    batch_id: '',
    title: '',
    scheduled_date: '',
    scheduled_time: '',
    duration_minutes: 45,
    description: '',
    zoom_start_url: '',
    zoom_join_url: '',
});

// Get today's date in YYYY-MM-DD format for min date
const today = new Date().toISOString().split('T')[0];

const selectedBatch = computed(() => {
    return props.batches?.find(b => b.id == form.batch_id);
});

const submit = () => {
    form.post(route('teacher.classes.store'), {
        preserveScroll: true,
        onSuccess: () => {
            form.reset();
        },
    });
};
</script>

<template>
    <Head title="Create Class Session" />

    <TeacherLayout>
        <div class="max-w-2xl mx-auto space-y-6">
            <!-- Header -->
            <div class="bg-gradient-to-r from-emerald-600 to-teal-500 rounded-2xl p-6 text-white relative overflow-hidden">
                <div class="absolute top-0 right-0 w-32 h-32 bg-white/10 rounded-full -translate-y-1/2 translate-x-1/2"></div>
                <div class="relative z-10">
                    <Link :href="route('teacher.schedule')" class="inline-flex items-center gap-2 text-emerald-100 hover:text-white mb-4 text-sm">
                        ← Back to Schedule
                    </Link>
                    <h1 class="text-2xl font-bold">📅 Schedule New Class</h1>
                    <p class="text-emerald-100 mt-1">Create a new class session for your students</p>
                </div>
            </div>

            <!-- Form -->
            <form @submit.prevent="submit" class="space-y-6">
                <!-- Batch Selection -->
                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 p-6">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">📚 Select Batch</h3>
                    
                    <div v-if="batches?.length" class="grid gap-3">
                        <label 
                            v-for="batch in batches" 
                            :key="batch.id"
                            :class="[
                                'flex items-center p-4 rounded-xl border-2 cursor-pointer transition-all',
                                form.batch_id == batch.id 
                                    ? 'border-emerald-500 bg-emerald-50 dark:bg-emerald-900/20' 
                                    : 'border-gray-200 dark:border-gray-600 hover:border-emerald-300'
                            ]"
                        >
                            <input 
                                type="radio" 
                                v-model="form.batch_id" 
                                :value="batch.id"
                                class="sr-only"
                            />
                            <div class="flex-1">
                                <p class="font-medium text-gray-900 dark:text-white">{{ batch.name }}</p>
                                <p class="text-sm text-gray-500 dark:text-gray-400">
                                    {{ batch.course?.title }} • {{ batch.enrolled_students }} students
                                </p>
                            </div>
                            <div v-if="form.batch_id == batch.id" class="w-6 h-6 bg-emerald-500 rounded-full flex items-center justify-center">
                                <span class="text-white text-sm">✓</span>
                            </div>
                        </label>
                    </div>
                    <div v-else class="text-center py-8 text-gray-500">
                        <p>You don't have any batches assigned yet.</p>
                    </div>
                    <p v-if="form.errors.batch_id" class="text-red-500 text-sm mt-2">{{ form.errors.batch_id }}</p>
                </div>

                <!-- Class Details -->
                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 p-6">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">📝 Class Details</h3>
                    
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Class Title *</label>
                            <input 
                                v-model="form.title"
                                type="text"
                                placeholder="e.g., Surah Al-Fatiha - Lesson 1"
                                class="w-full rounded-xl border-gray-200 dark:border-gray-600 dark:bg-gray-700 focus:border-emerald-500 focus:ring-emerald-500"
                                required
                            />
                            <p v-if="form.errors.title" class="text-red-500 text-sm mt-1">{{ form.errors.title }}</p>
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Date *</label>
                                <input 
                                    v-model="form.scheduled_date"
                                    type="date"
                                    :min="today"
                                    class="w-full rounded-xl border-gray-200 dark:border-gray-600 dark:bg-gray-700 focus:border-emerald-500 focus:ring-emerald-500"
                                    required
                                />
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Time *</label>
                                <input 
                                    v-model="form.scheduled_time"
                                    type="time"
                                    class="w-full rounded-xl border-gray-200 dark:border-gray-600 dark:bg-gray-700 focus:border-emerald-500 focus:ring-emerald-500"
                                    required
                                />
                            </div>
                        </div>
                        <div class="bg-blue-50 dark:bg-blue-900/20 rounded-lg p-3 flex items-center gap-2">
                            <span class="text-lg">🌍</span>
                            <p class="text-sm text-blue-700 dark:text-blue-300">
                                Time is in <strong>{{ teacherTimezone }}</strong> timezone. 
                                <Link href="/teacher/settings" class="underline hover:text-blue-800">Change in Settings</Link>
                            </p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Duration (minutes)</label>
                            <select 
                                v-model="form.duration_minutes"
                                class="w-full rounded-xl border-gray-200 dark:border-gray-600 dark:bg-gray-700 focus:border-emerald-500 focus:ring-emerald-500"
                            >
                                <option :value="30">30 minutes</option>
                                <option :value="45">45 minutes</option>
                                <option :value="60">1 hour</option>
                                <option :value="90">1.5 hours</option>
                                <option :value="120">2 hours</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Description (optional)</label>
                            <textarea 
                                v-model="form.description"
                                rows="3"
                                placeholder="What will be covered in this class..."
                                class="w-full rounded-xl border-gray-200 dark:border-gray-600 dark:bg-gray-700 focus:border-emerald-500 focus:ring-emerald-500"
                            ></textarea>
                        </div>
                    </div>
                </div>

                <!-- Zoom Links -->
                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 p-6">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">🎥 Zoom Meeting</h3>
                    
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Zoom Start URL (Teacher)</label>
                            <input 
                                v-model="form.zoom_start_url"
                                type="url"
                                placeholder="https://zoom.us/j/..."
                                class="w-full rounded-xl border-gray-200 dark:border-gray-600 dark:bg-gray-700 focus:border-emerald-500 focus:ring-emerald-500"
                            />
                            <p class="text-xs text-gray-400 mt-1">Your host link to start the meeting</p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Zoom Join URL (Students)</label>
                            <input 
                                v-model="form.zoom_join_url"
                                type="url"
                                placeholder="https://zoom.us/j/..."
                                class="w-full rounded-xl border-gray-200 dark:border-gray-600 dark:bg-gray-700 focus:border-emerald-500 focus:ring-emerald-500"
                            />
                            <p class="text-xs text-gray-400 mt-1">Link for students to join the meeting</p>
                        </div>
                    </div>
                </div>

                <!-- Submit Button -->
                <div class="flex justify-end gap-4">
                    <Link :href="route('teacher.schedule')" class="px-6 py-3 bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 font-semibold rounded-xl hover:bg-gray-200 dark:hover:bg-gray-600 transition">
                        Cancel
                    </Link>
                    <button 
                        type="submit"
                        :disabled="form.processing || !form.batch_id"
                        class="px-8 py-3 bg-gradient-to-r from-emerald-500 to-teal-500 text-white font-semibold rounded-xl hover:from-emerald-600 hover:to-teal-600 transition shadow-lg disabled:opacity-50"
                    >
                        <span v-if="form.processing">Creating...</span>
                        <span v-else>📅 Create Class Session</span>
                    </button>
                </div>
            </form>
        </div>
    </TeacherLayout>
</template>
