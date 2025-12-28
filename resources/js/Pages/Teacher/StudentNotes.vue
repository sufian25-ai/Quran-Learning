<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import { ref, computed } from 'vue';

const props = defineProps({
    auth: Object,
    students: {
        type: Array,
        default: () => []
    },
    batches: {
        type: Array,
        default: () => []
    }
});

const navigation = [
    { name: 'Dashboard', href: '/teacher', icon: '🏠' },
    { name: 'My Batches', href: '/teacher/batches', icon: '📅' },
    { name: 'Resources', href: '/teacher/resources', icon: '📁' },
    { name: 'Student Notes', href: '/teacher/notes', icon: '📝', active: true },
];

const selectedBatch = ref('');
const selectedStudent = ref(null);
const searchQuery = ref('');
const showNoteModal = ref(false);

const filteredStudents = computed(() => {
    let result = props.students;
    
    if (selectedBatch.value) {
        result = result.filter(s => s.batch_id === selectedBatch.value);
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

const form = useForm({
    note: '',
    type: 'general',
});

const openNoteModal = (student) => {
    selectedStudent.value = student;
    form.note = student.teacher_note || '';
    form.type = student.note_type || 'general';
    showNoteModal.value = true;
};

const saveNote = () => {
    form.post(`/teacher/students/${selectedStudent.value.id}/notes`, {
        preserveScroll: true,
        onSuccess: () => {
            showNoteModal.value = false;
            // Update local state
            const student = props.students.find(s => s.id === selectedStudent.value.id);
            if (student) {
                student.teacher_note = form.note;
                student.note_type = form.type;
            }
        },
    });
};

const noteTypes = [
    { value: 'general', label: 'General', color: 'bg-gray-100 text-gray-700' },
    { value: 'progress', label: 'Progress', color: 'bg-green-100 text-green-700' },
    { value: 'concern', label: 'Concern', color: 'bg-yellow-100 text-yellow-700' },
    { value: 'followup', label: 'Follow-up', color: 'bg-blue-100 text-blue-700' },
];

const getNoteColor = (type) => {
    return noteTypes.find(t => t.value === type)?.color || 'bg-gray-100 text-gray-700';
};
</script>

<template>
    <Head title="Student Notes | Teacher" />

    <div class="min-h-screen bg-gray-100">
        <!-- Sidebar -->
        <aside class="fixed inset-y-0 left-0 z-50 w-64 bg-gray-900">
            <div class="flex items-center h-16 px-6 border-b border-gray-800">
                <Link href="/" class="flex items-center space-x-3">
                    <div class="w-8 h-8 bg-gradient-to-br from-primary-500 to-primary-400 rounded-lg flex items-center justify-center">
                        <span class="text-white font-bold">Q</span>
                    </div>
                    <span class="text-lg font-bold text-white">Teacher Panel</span>
                </Link>
            </div>
            <nav class="mt-6 px-3">
                <Link
                    v-for="item in navigation"
                    :key="item.name"
                    :href="item.href"
                    :class="[
                        'flex items-center px-4 py-3 mb-1 rounded-xl text-sm font-medium transition-colors',
                        item.active ? 'bg-primary-500 text-white' : 'text-gray-300 hover:bg-gray-800 hover:text-white'
                    ]"
                >
                    <span class="text-lg mr-3">{{ item.icon }}</span>
                    {{ item.name }}
                </Link>
            </nav>
        </aside>

        <!-- Main Content -->
        <div class="ml-64">
            <header class="bg-white shadow-sm sticky top-0 z-40">
                <div class="flex items-center justify-between h-16 px-6">
                    <h1 class="text-xl font-semibold text-gray-900">Student Notes</h1>
                </div>
            </header>

            <main class="p-6">
                <!-- Filters -->
                <div class="bg-white rounded-xl shadow-sm p-4 mb-6">
                    <div class="flex gap-4">
                        <div class="flex-1 relative">
                            <input
                                v-model="searchQuery"
                                type="text"
                                placeholder="Search students..."
                                class="w-full pl-10 pr-4 py-3 rounded-xl border-gray-200 focus:border-primary-500 focus:ring-primary-500"
                            />
                            <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                            </svg>
                        </div>
                        <select
                            v-model="selectedBatch"
                            class="w-64 py-3 rounded-xl border-gray-200 focus:border-primary-500 focus:ring-primary-500"
                        >
                            <option value="">All Batches</option>
                            <option v-for="batch in batches" :key="batch.id" :value="batch.id">
                                {{ batch.name }}
                            </option>
                        </select>
                    </div>
                </div>

                <!-- Students Grid -->
                <div v-if="filteredStudents.length" class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
                    <div
                        v-for="student in filteredStudents"
                        :key="student.id"
                        class="bg-white rounded-xl shadow-sm p-6 cursor-pointer hover:shadow-lg transition-all"
                        @click="openNoteModal(student)"
                    >
                        <div class="flex items-start justify-between mb-4">
                            <div class="flex items-center">
                                <div class="w-12 h-12 rounded-full bg-primary-100 flex items-center justify-center text-xl text-primary-700 font-medium">
                                    {{ student.name?.charAt(0).toUpperCase() }}
                                </div>
                                <div class="ml-4">
                                    <h3 class="font-semibold text-gray-900">{{ student.name }}</h3>
                                    <p class="text-sm text-gray-500">{{ student.email }}</p>
                                </div>
                            </div>
                            <span v-if="student.note_type" :class="['px-2 py-1 text-xs rounded-full', getNoteColor(student.note_type)]">
                                {{ student.note_type }}
                            </span>
                        </div>

                        <div class="space-y-2">
                            <div class="flex items-center justify-between text-sm text-gray-500">
                                <span>{{ student.batch_name }}</span>
                                <span>{{ student.progress || 0 }}% progress</span>
                            </div>
                            
                            <div v-if="student.teacher_note" class="p-3 bg-gray-50 rounded-lg">
                                <p class="text-sm text-gray-600 line-clamp-2">{{ student.teacher_note }}</p>
                            </div>
                            <div v-else class="p-3 border-2 border-dashed border-gray-200 rounded-lg text-center">
                                <p class="text-sm text-gray-400">Click to add notes</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Empty State -->
                <div v-else class="bg-white rounded-xl shadow-sm p-12 text-center">
                    <span class="text-5xl mb-4 block">📝</span>
                    <h3 class="text-lg font-semibold text-gray-900 mb-2">No Students Found</h3>
                    <p class="text-gray-500">{{ searchQuery || selectedBatch ? 'No students match your filters.' : 'Students will appear here when they enroll in your batches.' }}</p>
                </div>
            </main>
        </div>

        <!-- Note Modal -->
        <div v-if="showNoteModal" class="fixed inset-0 z-50 flex items-center justify-center p-4">
            <div class="absolute inset-0 bg-black/50" @click="showNoteModal = false"></div>
            <div class="relative bg-white rounded-2xl shadow-xl w-full max-w-lg p-6">
                <div class="flex items-center mb-6">
                    <div class="w-12 h-12 rounded-full bg-primary-100 flex items-center justify-center text-xl text-primary-700 font-medium">
                        {{ selectedStudent?.name?.charAt(0).toUpperCase() }}
                    </div>
                    <div class="ml-4">
                        <h2 class="text-lg font-semibold text-gray-900">{{ selectedStudent?.name }}</h2>
                        <p class="text-sm text-gray-500">{{ selectedStudent?.email }}</p>
                    </div>
                </div>
                
                <form @submit.prevent="saveNote" class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Note Type</label>
                        <div class="flex gap-2">
                            <button
                                v-for="type in noteTypes"
                                :key="type.value"
                                type="button"
                                @click="form.type = type.value"
                                :class="[
                                    'px-4 py-2 text-sm font-medium rounded-lg border transition-colors',
                                    form.type === type.value
                                        ? type.color + ' border-transparent'
                                        : 'border-gray-200 text-gray-500 hover:bg-gray-50'
                                ]"
                            >
                                {{ type.label }}
                            </button>
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Notes</label>
                        <textarea
                            v-model="form.note"
                            rows="5"
                            class="w-full rounded-lg border-gray-200 focus:border-primary-500 focus:ring-primary-500"
                            placeholder="Add notes about this student's progress, concerns, or follow-ups..."
                        ></textarea>
                    </div>

                    <div class="flex gap-3 pt-2">
                        <button
                            type="button"
                            @click="showNoteModal = false"
                            class="flex-1 py-3 border border-gray-200 text-gray-700 font-medium rounded-lg hover:bg-gray-50"
                        >
                            Cancel
                        </button>
                        <button
                            type="submit"
                            :disabled="form.processing"
                            class="flex-1 py-3 bg-primary-500 hover:bg-primary-600 text-white font-medium rounded-lg disabled:opacity-50"
                        >
                            {{ form.processing ? 'Saving...' : 'Save Notes' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</template>
