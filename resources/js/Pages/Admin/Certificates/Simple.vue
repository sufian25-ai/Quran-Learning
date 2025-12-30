<script setup>
import { Head, Link, router } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';

const props = defineProps({
    completedEnrollments: Object,
    certificates: Object,
    stats: Object
});

const deleteCertificate = (id) => {
    if (confirm('Delete this certificate?')) {
        router.delete(`/admin/certificates/${id}`);
    }
};
</script>

<template>
    <AdminLayout>
        <Head title="Certificate Management" />
        
        <div class="py-6">
            <h1 class="text-3xl font-bold text-gray-900 mb-6">📜 Certificate Management</h1>
            
            <!-- Stats -->
            <div class="grid grid-cols-3 gap-4 mb-6">
                <div class="bg-white rounded-lg shadow p-6">
                    <div class="text-sm text-gray-600">Total Certificates</div>
                    <div class="text-3xl font-bold text-emerald-600">{{ stats.total_certificates }}</div>
                </div>
                <div class="bg-white rounded-lg shadow p-6">
                    <div class="text-sm text-gray-600">Completed Students</div>
                    <div class="text-3xl font-bold text-blue-600">{{ stats.completed_students }}</div>
                </div>
                <div class="bg-white rounded-lg shadow p-6">
                    <div class="text-sm text-gray-600">Need Certificate</div>
                    <div class="text-3xl font-bold text-orange-600">{{ stats.pending }}</div>
                </div>
            </div>
            
            <!-- Completed Students (Need Certificate) -->
            <div class="bg-white rounded-lg shadow mb-6">
                <div class="px-6 py-4 border-b bg-orange-50">
                    <h2 class="text-xl font-bold text-orange-900">⏳ Students Needing Certificate</h2>
                    <p class="text-sm text-orange-700">Click "Create Certificate" to issue manually</p>
                </div>
                <table class="min-w-full">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Student</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Course</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Progress</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Completed</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y">
                        <tr v-for="enroll in completedEnrollments.data" :key="enroll.id" class="hover:bg-gray-50">
                            <td class="px-6 py-4">
                                <div class="text-sm font-medium text-gray-900">{{ enroll.user?.name }}</div>
                                <div class="text-sm text-gray-500">{{ enroll.user?.email }}</div>
                            </td>
                            <td class="px-6 py-4 text-sm">{{ enroll.course?.title }}</td>
                            <td class="px-6 py-4">
                                <span class="px-2 py-1 text-xs rounded-full bg-emerald-100 text-emerald-800">
                                    {{ enroll.progress || 100 }}%
                                </span>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-500">
                                {{ new Date(enroll.updated_at).toLocaleDateString() }}
                            </td>
                            <td class="px-6 py-4">
                                <Link :href="`/admin/certificates/create/${enroll.id}`" 
                                      class="px-4 py-2 bg-emerald-600 text-white text-sm rounded hover:bg-emerald-700 font-semibold">
                                    Create Certificate
                                </Link>
                            </td>
                        </tr>
                        <tr v-if="completedEnrollments.data.length === 0">
                            <td colspan="5" class="px-6 py-8 text-center text-gray-500">
                                All completed students have certificates! 🎉
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            
            <!-- Issued Certificates -->
            <div class="bg-white rounded-lg shadow">
                <div class="px-6 py-4 border-b bg-emerald-50">
                    <h2 class="text-xl font-bold text-emerald-900">✅ Issued Certificates</h2>
                </div>
                <table class="min-w-full">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Certificate #</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Student</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Course</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Issued</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y">
                        <tr v-for="cert in certificates.data" :key="cert.id" class="hover:bg-gray-50">
                            <td class="px-6 py-4">
                                <span class="font-mono text-sm font-semibold">{{ cert.certificate_number }}</span>
                            </td>
                            <td class="px-6 py-4 text-sm">{{ cert.student_name }}</td>
                            <td class="px-6 py-4 text-sm">{{ cert.course_title }}</td>
                            <td class="px-6 py-4 text-sm text-gray-500">
                                {{ new Date(cert.created_at).toLocaleDateString() }}
                            </td>
                            <td class="px-6 py-4 text-sm space-x-2">
                                <a :href="`/certificates/verify/${cert.verification_code}`" 
                                   target="_blank"
                                   class="text-blue-600 hover:text-blue-900">
                                    View
                                </a>
                                <button @click="deleteCertificate(cert.id)"
                                        class="text-red-600 hover:text-red-900">
                                    Delete
                                </button>
                            </td>
                        </tr>
                        <tr v-if="certificates.data.length === 0">
                            <td colspan="5" class="px-6 py-8 text-center text-gray-500">
                                No certificates issued yet.
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </AdminLayout>
</template>
