<script setup>
import { Head, Link, useForm, usePage } from '@inertiajs/vue3';
import { computed, watch } from 'vue';
import TeacherLayout from '@/Layouts/TeacherLayout.vue';

const props = defineProps({
    student: Object,
    enrollment: Object,
    skills: Object,
});

const form = useForm({
    quran_reading: props.skills?.quran_reading || 0,
    tajweed: props.skills?.tajweed || 0,
    pronunciation: props.skills?.pronunciation || 0, 
    memorization: props.skills?.memorization || 0,
    fluency: props.skills?.fluency || 0,
    notes: props.skills?.notes || '',
});

// Calculate overall progress based on weighted skills
const overallProgress = computed(() => {
    return Math.round(
        (form.quran_reading * 0.25) +
        (form.tajweed * 0.25) +
        (form.pronunciation * 0.20) +
        (form.memorization * 0.20) +
        (form.fluency * 0.10)
    );
});

const skills = [
    { key: 'quran_reading', name: 'Quran Reading', namebn: 'কুরআন পড়া', weight: 25, color: 'emerald' },
    { key: 'tajweed', name: 'Tajweed Rules', namebn: 'তাজবীদ', weight: 25, color: 'blue' },
    { key: 'pronunciation', name: 'Pronunciation', namebn: 'উচ্চারণ', weight: 20, color: 'purple' },
    { key: 'memorization', name: 'Memorization', namebn: 'হিফজ/মুখস্থ', weight: 20, color: 'amber' },
    { key: 'fluency', name: 'Fluency', namebn: 'সাবলীলতা', weight: 10, color: 'pink' },
];

const getGradientClass = (color) => {
    const gradients = {
        emerald: 'from-emerald-500 to-teal-500',
        blue: 'from-blue-500 to-indigo-500',
        purple: 'from-purple-500 to-pink-500',
        amber: 'from-amber-500 to-orange-500',
        pink: 'from-pink-500 to-rose-500',
    };
    return gradients[color] || gradients.emerald;
};

const getProgressColor = (value) => {
    if (value >= 80) return 'bg-emerald-500';
    if (value >= 60) return 'bg-blue-500';
    if (value >= 40) return 'bg-amber-500';
    return 'bg-red-500';
};

const submit = () => {
    form.post(route('teacher.student.progress.update', props.enrollment.id), {
        preserveScroll: true,
    });
};
</script>

<template>
    <Head :title="`${student.name} - Progress`" />

    <TeacherLayout>
        <div class="space-y-6">
            <!-- Header -->
            <div class="bg-gradient-to-r from-blue-600 via-indigo-500 to-purple-500 rounded-2xl p-6 text-white relative overflow-hidden">
                <div class="absolute top-0 right-0 w-40 h-40 bg-white/10 rounded-full -translate-y-1/2 translate-x-1/2"></div>
                <div class="absolute bottom-0 left-0 w-24 h-24 bg-white/5 rounded-full translate-y-1/2 -translate-x-1/2"></div>
                <div class="relative z-10">
                    <Link :href="route('teacher.students')" class="inline-flex items-center gap-2 text-blue-100 hover:text-white mb-4 text-sm">
                        ← Back to Students
                    </Link>
                    <div class="flex items-center gap-4">
                        <div class="w-16 h-16 rounded-full bg-white/20 flex items-center justify-center text-2xl font-bold">
                            {{ student.name?.charAt(0).toUpperCase() }}
                        </div>
                        <div>
                            <h1 class="text-2xl font-bold">{{ student.name }}</h1>
                            <p class="text-blue-100">{{ student.email }}</p>
                            <p class="text-sm text-blue-200 mt-1">{{ enrollment.course?.title }} • {{ enrollment.batch?.name }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Overall Progress Card -->
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Overall Progress</h3>
                    <span :class="['text-4xl font-bold', overallProgress >= 80 ? 'text-emerald-500' : overallProgress >= 60 ? 'text-blue-500' : overallProgress >= 40 ? 'text-amber-500' : 'text-red-500']">
                        {{ overallProgress }}%
                    </span>
                </div>
                <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-4 overflow-hidden">
                    <div 
                        :class="['h-full rounded-full transition-all duration-500', getProgressColor(overallProgress)]"
                        :style="{ width: `${overallProgress}%` }"
                    ></div>
                </div>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-2">
                    সব skills complete হলে 100% হবে।
                </p>
            </div>

            <!-- Skills Section -->
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
                <div class="p-5 border-b border-gray-100 dark:border-gray-700">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">📚 Skills Assessment</h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">প্রতিটি skill 0-100% এ rate করুন</p>
                </div>
                
                <div class="p-6 space-y-8">
                    <div v-for="skill in skills" :key="skill.key" class="space-y-3">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                <div :class="['w-10 h-10 rounded-xl bg-gradient-to-r flex items-center justify-center text-white text-sm font-bold', getGradientClass(skill.color)]">
                                    {{ skill.weight }}%
                                </div>
                                <div>
                                    <p class="font-medium text-gray-900 dark:text-white">{{ skill.name }}</p>
                                    <p class="text-sm text-gray-500 dark:text-gray-400">{{ skill.namebn }}</p>
                                </div>
                            </div>
                            <div class="flex items-center gap-3">
                                <input 
                                    v-model.number="form[skill.key]"
                                    type="number"
                                    min="0"
                                    max="100"
                                    class="w-20 text-center rounded-lg border-gray-200 dark:border-gray-600 dark:bg-gray-700 focus:border-blue-500 focus:ring-blue-500 font-bold"
                                />
                                <span class="text-gray-500">%</span>
                            </div>
                        </div>
                        <div class="relative">
                            <input 
                                v-model.number="form[skill.key]"
                                type="range"
                                min="0"
                                max="100"
                                :class="['w-full h-3 rounded-full appearance-none cursor-pointer', `accent-${skill.color}-500`]"
                                style="background: linear-gradient(to right, var(--tw-gradient-from), var(--tw-gradient-to));"
                            />
                            <div class="flex justify-between text-xs text-gray-400 mt-1">
                                <span>0%</span>
                                <span>25%</span>
                                <span>50%</span>
                                <span>75%</span>
                                <span>100%</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Notes Section -->
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 p-6">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">📝 Teacher Notes</h3>
                <textarea 
                    v-model="form.notes"
                    rows="4"
                    placeholder="Add notes about student's performance, areas to improve, feedback..."
                    class="w-full rounded-xl border-gray-200 dark:border-gray-600 dark:bg-gray-700 focus:border-blue-500 focus:ring-blue-500"
                ></textarea>
            </div>

            <!-- Submit Button -->
            <div class="flex justify-end gap-4">
                <Link :href="route('teacher.students')" class="px-6 py-3 bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 font-semibold rounded-xl hover:bg-gray-200 dark:hover:bg-gray-600 transition">
                    Cancel
                </Link>
                <button 
                    @click="submit"
                    :disabled="form.processing"
                    class="px-8 py-3 bg-gradient-to-r from-blue-500 to-indigo-500 text-white font-semibold rounded-xl hover:from-blue-600 hover:to-indigo-600 transition shadow-lg disabled:opacity-50"
                >
                    <span v-if="form.processing">Saving...</span>
                    <span v-else>💾 Save Progress</span>
                </button>
            </div>
        </div>
    </TeacherLayout>
</template>

<style scoped>
input[type="range"] {
    -webkit-appearance: none;
    background: linear-gradient(to right, #10b981, #3b82f6, #8b5cf6);
    border-radius: 9999px;
}
input[type="range"]::-webkit-slider-thumb {
    -webkit-appearance: none;
    width: 24px;
    height: 24px;
    border-radius: 50%;
    background: white;
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
    cursor: pointer;
    border: 3px solid #3b82f6;
}
input[type="range"]::-moz-range-thumb {
    width: 24px;
    height: 24px;
    border-radius: 50%;
    background: white;
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
    cursor: pointer;
    border: 3px solid #3b82f6;
}
</style>
