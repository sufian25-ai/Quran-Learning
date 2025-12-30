<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';

const props = defineProps({
    enrollment: Object
});

const form = useForm({
    enrollment_id: props.enrollment.id,
    student_name: props.enrollment.student_name,
    course_title: props.enrollment.course_title,
    course_description: props.enrollment.course_description || '',
    completion_percentage: props.enrollment.progress || 100,
    grade: null,
    instructor_name: props.enrollment.instructor,
    issued_by: 'QuranLearn Administration'
});

const submit = () => {
    form.post('/admin/certificates');
};
</script>

<template>
    <AdminLayout>
        <Head title="Create Certificate" />
        
        <div class="py-6 max-w-3xl mx-auto">
            <div class="flex justify-between items-center mb-6">
                <h1 class="text-3xl font-bold text-gray-900">📜 Create Certificate Manually</h1>
                <Link href="/admin/certificates" class="px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700">
                    ← Back
                </Link>
            </div>
            
            <!-- Student Info Banner -->
            <div class="bg-gradient-to-r from-emerald-600 to-teal-600 rounded-lg shadow p-6 mb-6 text-white">
                <h2 class="text-2xl font-bold mb-2">{{ enrollment.student_name }}</h2>
                <p class="text-emerald-100">{{ enrollment.student_email }}</p>
                <p class="text-emerald-100 mt-2">Course: <strong>{{ enrollment.course_title }}</strong></p>
                <p class="text-emerald-100">Progress: <strong>{{ enrollment.progress || 100 }}%</strong></p>
            </div>
            
            <!-- Certificate Form -->
            <form @submit.prevent="submit" class="bg-white rounded-lg shadow p-6">
                <div class="space-y-4">
                    <!-- Student Name -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Student Name *
                        </label>
                        <input 
                            v-model="form.student_name" 
                            type="text" 
                            required
                            class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-emerald-500"
                        />
                    </div>
                    
                    <!-- Course Title -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Course Title *
                        </label>
                        <input 
                            v-model="form.course_title" 
                            type="text" 
                            required
                            class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-emerald-500"
                        />
                    </div>
                    
                    <!-- Course Description -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Course Description
                        </label>
                        <textarea 
                            v-model="form.course_description" 
                            rows="3"
                            class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-emerald-500"
                        ></textarea>
                    </div>
                    
                    <!-- Completion Percentage -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Completion: {{ form.completion_percentage }}% *
                        </label>
                        <input 
                            v-model="form.completion_percentage" 
                            type="range" 
                            min="0" 
                            max="100"
                            class="w-full"
                        />
                    </div>
                    
                    <!-- Grade -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Grade (Optional %)
                        </label>
                        <input 
                            v-model="form.grade" 
                            type="number" 
                            min="0" 
                            max="100"
                            step="0.01"
                            class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-emerald-500"
                        />
                    </div>
                    
                    <!-- Instructor Name -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Instructor Name *
                        </label>
                        <input 
                            v-model="form.instructor_name" 
                            type="text" 
                            required
                            class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-emerald-500"
                        />
                    </div>
                    
                    <!-- Issued By -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Issued By *
                        </label>
                        <input 
                            v-model="form.issued_by" 
                            type="text" 
                            required
                            class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-emerald-500"
                        />
                    </div>
                    
                    <!-- Submit Button -->
                    <div class="flex gap-3 pt-4">
                        <button 
                            type="submit" 
                            :disabled="form.processing"
                            class="flex-1 px-6 py-3 bg-emerald-600 text-white rounded-lg hover:bg-emerald-700 font-semibold disabled:opacity-50"
                        >
                            {{ form.processing ? 'Creating...' : '✓ Create Certificate' }}
                        </button>
                        <Link 
                            href="/admin/certificates"
                            class="px-6 py-3 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 font-semibold text-center"
                        >
                            Cancel
                        </Link>
                    </div>
                </div>
            </form>
        </div>
    </AdminLayout>
</template>
