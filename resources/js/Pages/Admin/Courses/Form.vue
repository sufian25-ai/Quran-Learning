<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import { ref, computed } from 'vue';
import AdminLayout from '@/Layouts/AdminLayout.vue';

const props = defineProps({
    auth: Object,
    course: {
        type: Object,
        default: null
    }
});

const isEditing = computed(() => !!props.course);

const form = useForm({
    title: props.course?.title || '',
    slug: props.course?.slug || '',
    short_description: props.course?.short_description || '',
    description: props.course?.description || '',
    category: props.course?.category || 'quran_reading',
    level: props.course?.level || 'beginner',
    duration_weeks: props.course?.duration_weeks || 12,
    classes_per_week: props.course?.classes_per_week || 3,
    class_duration_minutes: props.course?.class_duration_minutes || 45,
    price_group: props.course?.price_group || 49,
    price_private: props.course?.price_private || 99,
    syllabus: props.course?.syllabus || [],
    learning_outcomes: props.course?.learning_outcomes || [],
    requirements: props.course?.requirements || [],
    is_featured: props.course?.is_featured || false,
    status: props.course?.status || 'draft',
});

const syllabusInput = ref('');
const outcomeInput = ref('');
const requirementInput = ref('');

const categories = [
    { id: 'quran_reading', name: 'Quran Reading' },
    { id: 'tajweed', name: 'Tajweed' },
    { id: 'hifz', name: 'Hifz (Memorization)' },
    { id: 'arabic', name: 'Arabic Language' },
    { id: 'islamic_studies', name: 'Islamic Studies' },
];

const levels = [
    { id: 'beginner', name: 'Beginner' },
    { id: 'intermediate', name: 'Intermediate' },
    { id: 'advanced', name: 'Advanced' },
    { id: 'all_levels', name: 'All Levels' },
];

const generateSlug = () => {
    form.slug = form.title
        .toLowerCase()
        .replace(/[^a-z0-9]+/g, '-')
        .replace(/(^-|-$)/g, '');
};

const addSyllabus = () => {
    if (syllabusInput.value.trim()) {
        form.syllabus.push({ title: syllabusInput.value.trim(), topics: [] });
        syllabusInput.value = '';
    }
};

const removeSyllabus = (index) => {
    form.syllabus.splice(index, 1);
};

const addOutcome = () => {
    if (outcomeInput.value.trim()) {
        form.learning_outcomes.push(outcomeInput.value.trim());
        outcomeInput.value = '';
    }
};

const removeOutcome = (index) => {
    form.learning_outcomes.splice(index, 1);
};

const addRequirement = () => {
    if (requirementInput.value.trim()) {
        form.requirements.push(requirementInput.value.trim());
        requirementInput.value = '';
    }
};

const removeRequirement = (index) => {
    form.requirements.splice(index, 1);
};

const submit = () => {
    if (isEditing.value) {
        form.put(`/admin/courses/${props.course.id}`);
    } else {
        form.post('/admin/courses');
    }
};
</script>

<template>
    <Head :title="isEditing ? 'Edit Course' : 'Create Course'" />

    <AdminLayout>
        <!-- Header -->
        <div class="flex items-center justify-between mb-6">
            <div class="flex items-center">
                <Link href="/admin/courses" class="text-gray-400 hover:text-gray-600 mr-4">
                    ← Back
                </Link>
                <h1 class="text-2xl font-bold text-gray-900">
                    {{ isEditing ? 'Edit Course' : 'Create New Course' }}
                </h1>
            </div>
            <div class="flex items-center gap-3">
                <Link href="/admin/courses" class="px-4 py-2 text-gray-600 hover:text-gray-900">
                    Cancel
                </Link>
                <button
                    @click="submit"
                    :disabled="form.processing"
                    class="px-6 py-2 bg-primary-500 hover:bg-primary-600 text-white font-medium rounded-lg disabled:opacity-50"
                >
                    {{ form.processing ? 'Saving...' : (isEditing ? 'Update Course' : 'Create Course') }}
                </button>
            </div>
        </div>

        <form @submit.prevent="submit" class="max-w-4xl">
            <!-- Basic Info -->
            <div class="bg-white rounded-xl shadow-sm p-6 mb-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-6">Basic Information</h2>
                
                <div class="grid grid-cols-1 gap-6">
                    <div>
                        <label for="title" class="block text-sm font-medium text-gray-700 mb-2">Course Title *</label>
                        <input
                            id="title"
                            v-model="form.title"
                            @blur="!isEditing && generateSlug()"
                            type="text"
                            required
                            class="w-full rounded-lg border-gray-200 focus:border-primary-500 focus:ring-primary-500"
                            placeholder="e.g., Quran Reading for Beginners"
                        />
                        <p v-if="form.errors.title" class="mt-1 text-sm text-red-500">{{ form.errors.title }}</p>
                    </div>

                    <div>
                        <label for="slug" class="block text-sm font-medium text-gray-700 mb-2">URL Slug *</label>
                        <div class="flex">
                            <span class="inline-flex items-center px-3 rounded-l-lg border border-r-0 border-gray-200 bg-gray-50 text-gray-500 text-sm">
                                /courses/
                            </span>
                            <input
                                id="slug"
                                v-model="form.slug"
                                type="text"
                                required
                                class="flex-1 rounded-r-lg border-gray-200 focus:border-primary-500 focus:ring-primary-500"
                                placeholder="quran-reading-beginners"
                            />
                        </div>
                        <p v-if="form.errors.slug" class="mt-1 text-sm text-red-500">{{ form.errors.slug }}</p>
                    </div>

                    <div>
                        <label for="short_description" class="block text-sm font-medium text-gray-700 mb-2">Short Description *</label>
                        <textarea
                            id="short_description"
                            v-model="form.short_description"
                            rows="2"
                            required
                            class="w-full rounded-lg border-gray-200 focus:border-primary-500 focus:ring-primary-500"
                            placeholder="Brief description for course cards (max 200 characters)"
                        ></textarea>
                    </div>

                    <div>
                        <label for="description" class="block text-sm font-medium text-gray-700 mb-2">Full Description</label>
                        <textarea
                            id="description"
                            v-model="form.description"
                            rows="5"
                            class="w-full rounded-lg border-gray-200 focus:border-primary-500 focus:ring-primary-500"
                            placeholder="Detailed course description..."
                        ></textarea>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label for="category" class="block text-sm font-medium text-gray-700 mb-2">Category *</label>
                            <select
                                id="category"
                                v-model="form.category"
                                required
                                class="w-full rounded-lg border-gray-200 focus:border-primary-500 focus:ring-primary-500"
                            >
                                <option v-for="cat in categories" :key="cat.id" :value="cat.id">
                                    {{ cat.name }}
                                </option>
                            </select>
                        </div>
                        <div>
                            <label for="level" class="block text-sm font-medium text-gray-700 mb-2">Level *</label>
                            <select
                                id="level"
                                v-model="form.level"
                                required
                                class="w-full rounded-lg border-gray-200 focus:border-primary-500 focus:ring-primary-500"
                            >
                                <option v-for="lvl in levels" :key="lvl.id" :value="lvl.id">
                                    {{ lvl.name }}
                                </option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Schedule & Duration -->
            <div class="bg-white rounded-xl shadow-sm p-6 mb-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-6">Schedule & Duration</h2>
                
                <div class="grid grid-cols-3 gap-4">
                    <div>
                        <label for="duration_weeks" class="block text-sm font-medium text-gray-700 mb-2">Duration (weeks)</label>
                        <input
                            id="duration_weeks"
                            v-model.number="form.duration_weeks"
                            type="number"
                            min="1"
                            class="w-full rounded-lg border-gray-200 focus:border-primary-500 focus:ring-primary-500"
                        />
                    </div>
                    <div>
                        <label for="classes_per_week" class="block text-sm font-medium text-gray-700 mb-2">Classes per Week</label>
                        <input
                            id="classes_per_week"
                            v-model.number="form.classes_per_week"
                            type="number"
                            min="1"
                            max="7"
                            class="w-full rounded-lg border-gray-200 focus:border-primary-500 focus:ring-primary-500"
                        />
                    </div>
                    <div>
                        <label for="class_duration_minutes" class="block text-sm font-medium text-gray-700 mb-2">Class Duration (min)</label>
                        <input
                            id="class_duration_minutes"
                            v-model.number="form.class_duration_minutes"
                            type="number"
                            min="15"
                            step="15"
                            class="w-full rounded-lg border-gray-200 focus:border-primary-500 focus:ring-primary-500"
                        />
                    </div>
                </div>
            </div>

            <!-- Pricing -->
            <div class="bg-white rounded-xl shadow-sm p-6 mb-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-6">Pricing</h2>
                
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label for="price_group" class="block text-sm font-medium text-gray-700 mb-2">Group Class Price ($/month)</label>
                        <div class="relative">
                            <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-500">$</span>
                            <input
                                id="price_group"
                                v-model.number="form.price_group"
                                type="number"
                                min="0"
                                step="0.01"
                                class="w-full pl-8 rounded-lg border-gray-200 focus:border-primary-500 focus:ring-primary-500"
                            />
                        </div>
                    </div>
                    <div>
                        <label for="price_private" class="block text-sm font-medium text-gray-700 mb-2">Private Class Price ($/month)</label>
                        <div class="relative">
                            <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-500">$</span>
                            <input
                                id="price_private"
                                v-model.number="form.price_private"
                                type="number"
                                min="0"
                                step="0.01"
                                class="w-full pl-8 rounded-lg border-gray-200 focus:border-primary-500 focus:ring-primary-500"
                            />
                        </div>
                    </div>
                </div>
            </div>

            <!-- Learning Outcomes -->
            <div class="bg-white rounded-xl shadow-sm p-6 mb-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-6">Learning Outcomes</h2>
                
                <div class="flex gap-2 mb-4">
                    <input
                        v-model="outcomeInput"
                        @keyup.enter.prevent="addOutcome"
                        type="text"
                        class="flex-1 rounded-lg border-gray-200 focus:border-primary-500 focus:ring-primary-500"
                        placeholder="What will students learn?"
                    />
                    <button
                        @click="addOutcome"
                        type="button"
                        class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg"
                    >
                        Add
                    </button>
                </div>

                <ul v-if="form.learning_outcomes.length" class="space-y-2">
                    <li
                        v-for="(outcome, index) in form.learning_outcomes"
                        :key="index"
                        class="flex items-center justify-between p-3 bg-gray-50 rounded-lg"
                    >
                        <span class="text-sm text-gray-700">{{ outcome }}</span>
                        <button @click="removeOutcome(index)" type="button" class="text-red-500 hover:text-red-700">×</button>
                    </li>
                </ul>
                <p v-else class="text-gray-400 text-sm">No learning outcomes added</p>
            </div>

            <!-- Syllabus -->
            <div class="bg-white rounded-xl shadow-sm p-6 mb-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-6">Syllabus</h2>
                
                <div class="flex gap-2 mb-4">
                    <input
                        v-model="syllabusInput"
                        @keyup.enter.prevent="addSyllabus"
                        type="text"
                        class="flex-1 rounded-lg border-gray-200 focus:border-primary-500 focus:ring-primary-500"
                        placeholder="Module/Week title"
                    />
                    <button
                        @click="addSyllabus"
                        type="button"
                        class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg"
                    >
                        Add Module
                    </button>
                </div>

                <ul v-if="form.syllabus.length" class="space-y-2">
                    <li
                        v-for="(item, index) in form.syllabus"
                        :key="index"
                        class="flex items-center justify-between p-3 bg-gray-50 rounded-lg"
                    >
                        <span class="text-sm text-gray-700">Week {{ index + 1 }}: {{ item.title }}</span>
                        <button @click="removeSyllabus(index)" type="button" class="text-red-500 hover:text-red-700">×</button>
                    </li>
                </ul>
                <p v-else class="text-gray-400 text-sm">No syllabus items added</p>
            </div>

            <!-- Status & Featured -->
            <div class="bg-white rounded-xl shadow-sm p-6 mb-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-6">Publishing</h2>
                
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label for="status" class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                        <select
                            id="status"
                            v-model="form.status"
                            class="w-full rounded-lg border-gray-200 focus:border-primary-500 focus:ring-primary-500"
                        >
                            <option value="draft">Draft</option>
                            <option value="published">Published</option>
                            <option value="archived">Archived</option>
                        </select>
                    </div>
                    <div class="flex items-center">
                        <label class="flex items-center cursor-pointer">
                            <input
                                v-model="form.is_featured"
                                type="checkbox"
                                class="rounded border-gray-300 text-primary-500 focus:ring-primary-500"
                            />
                            <span class="ml-2 text-sm text-gray-700">Featured Course</span>
                        </label>
                    </div>
                </div>
            </div>

            <!-- Submit -->
            <div class="flex justify-end gap-3">
                <Link href="/admin/courses" class="px-6 py-3 text-gray-600 hover:text-gray-900">
                    Cancel
                </Link>
                <button
                    type="submit"
                    :disabled="form.processing"
                    class="px-8 py-3 bg-primary-500 hover:bg-primary-600 text-white font-medium rounded-lg disabled:opacity-50"
                >
                    {{ form.processing ? 'Saving...' : (isEditing ? 'Update Course' : 'Create Course') }}
                </button>
            </div>
        </form>
    </AdminLayout>
</template>
