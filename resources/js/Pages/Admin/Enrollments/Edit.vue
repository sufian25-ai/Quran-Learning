<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';

const props = defineProps({
    enrollment: Object,
    teachers: Array,
    batches: Array
});

const form = useForm({
    batch_id: props.enrollment.batch_id,
    status: props.enrollment.status,
    progress: props.enrollment.progress || 0,
    type: props.enrollment.type
});

const submit = () => {
    form.put(`/admin/enrollments/${props.enrollment.id}`);
};
</script>

<template>
    <AdminLayout>
        <Head title="Assign Teacher & Batch" />
        
        <div class="py-6 max-w-4xl mx-auto">
            <!-- Header -->
            <div class="flex justify-between items-center mb-6">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">🎯 Assign Teacher & Batch</h1>
                    <p class="text-gray-600 mt-1">Configure enrollment details</p>
                </div>
                <Link href="/admin/enrollments" class="px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700">
                    ← Back to Enrollments
                </Link>
            </div>
            
            <!-- Student & Course Info Card -->
            <div class="bg-gradient-to-r from-emerald-600 to-teal-600 rounded-lg shadow-lg p-6 mb-6 text-white">
                <div class="grid md:grid-cols-2 gap-6">
                    <div>
                        <p class="text-emerald-100 text-sm mb-1">Student</p>
                        <p class="text-2xl font-bold">{{ enrollment.user.name }}</p>
                        <p class="text-emerald-100">{{ enrollment.user.email }}</p>
                    </div>
                    <div>
                        <p class="text-emerald-100 text-sm mb-1">Enrolled Course</p>
                        <p class="text-2xl font-bold">{{ enrollment.course.title }}</p>
                        <p class="text-emerald-100">Enrolled: {{ enrollment.created_at }}</p>
                    </div>
                </div>
            </div>
            
            <!-- Assignment Form -->
            <form @submit.prevent="submit" class="bg-white rounded-lg shadow p-6">
                <div class="space-y-6">
                    <!-- Batch Assignment -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            📅 Assign to Batch
                        </label>
                        <select 
                            v-model="form.batch_id"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500"
                        >
                            <option :value="null">-- Select Batch --</option>
                            <option v-for="batch in batches" :key="batch.id" :value="batch.id">
                                {{ batch.name }} 
                                ({{ batch.type === 'private' ? 'Private 👤' : 'Group 👥' }})
                                - Teacher: {{ batch.teacher?.name || 'Not assigned' }}
                            </option>
                        </select>
                        <p class="mt-2 text-sm text-gray-500">
                            Choose a batch with assigned teacher. The student will be assigned to that teacher automatically.
                        </p>
                        
                        <!-- Current Batch Info -->
                        <div v-if="enrollment.batch" class="mt-3 p-3 bg-emerald-50 border border-emerald-200 rounded-lg">
                            <p class="text-sm font-medium text-emerald-900">Currently Assigned:</p>
                            <p class="text-sm text-emerald-700">
                                {{ enrollment.batch.name }} - Teacher: {{ enrollment.batch.teacher?.name }}
                            </p>
                        </div>
                    </div>
                    
                    <!-- Enrollment Type -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            🎓 Enrollment Type
                        </label>
                        <div class="flex gap-4">
                            <label class="flex items-center">
                                <input 
                                    type="radio" 
                                    v-model="form.type" 
                                    value="private"
                                    class="mr-2 text-emerald-600 focus:ring-emerald-500"
                                />
                                <span class="text-gray-700">Private 1-on-1</span>
                            </label>
                            <label class="flex items-center">
                                <input 
                                    type="radio" 
                                    v-model="form.type" 
                                    value="group"
                                    class="mr-2 text-emerald-600 focus:ring-emerald-500"
                                />
                                <span class="text-gray-700">Group Class</span>
                            </label>
                        </div>
                    </div>
                    
                    <!-- Progress -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            📊 Course Progress: {{ form.progress }}%
                        </label>
                        <input 
                            type="range" 
                            v-model="form.progress" 
                            min="0" 
                            max="100"
                            class="w-full h-2 bg-gray-200 rounded-lg appearance-none cursor-pointer accent-emerald-600"
                        />
                        <div class="flex justify-between text-xs text-gray-500 mt-1">
                            <span>0%</span>
                            <span>50%</span>
                            <span>100%</span>
                        </div>
                    </div>
                    
                    <!-- Status -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            🎯 Enrollment Status
                        </label>
                        <select 
                            v-model="form.status"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500"
                        >
                            <option value="pending">⏳ Pending - Waiting for assignment</option>
                            <option value="active">✅ Active - Classes can begin</option>
                            <option value="completed">🎓 Completed - Course finished</option>
                            <option value="cancelled">❌ Cancelled</option>
                        </select>
                    </div>
                    
                    <!-- Payment Info (Read-only) -->
                    <div class="bg-gray-50 rounded-lg p-4">
                        <h3 class="text-sm font-medium text-gray-700 mb-2">💳 Payment Information</h3>
                        <div class="grid grid-cols-2 gap-4 text-sm">
                            <div>
                                <span class="text-gray-600">Amount:</span>
                                <span class="font-semibold ml-2">BDT {{ enrollment.amount }}</span>
                            </div>
                            <div>
                                <span class="text-gray-600">Payment Status:</span>
                                <span :class="[
                                    'ml-2 px-2 py-1 rounded text-xs font-semibold',
                                    enrollment.payment_status === 'completed' ? 'bg-emerald-100 text-emerald-800' : 'bg-orange-100 text-orange-800'
                                ]">
                                    {{ enrollment.payment_status }}
                                </span>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Action Buttons -->
                    <div class="flex gap-3 pt-4">
                        <button 
                            type="submit" 
                            :disabled="form.processing"
                            class="flex-1 px-6 py-3 bg-emerald-600 text-white rounded-lg hover:bg-emerald-700 font-semibold disabled:opacity-50"
                        >
                            {{ form.processing ? 'Saving...' : '✓ Save Assignment' }}
                        </button>
                        <Link 
                            href="/admin/enrollments"
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
