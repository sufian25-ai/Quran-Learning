<script setup>
import { Head, Link, router } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';

const props = defineProps({
    eligibleEnrollments: Object,
    allEnrollments: Object  
});

const generateCertificate = (enrollmentId) => {
    if (confirm('Generate certificate for this student?')) {
        router.post(`/admin/certificates/generate/${enrollmentId}`);
    }
};

const bulkGenerate = () => {
    if (confirm('Generate certificates for ALL eligible students?')) {
        router.post('/admin/certificates/bulk-generate');
    }
};
</script>

<template>
    <AdminLayout>
        <Head title="Generate Certificates" />
        
        <div class="py-6">
            <div class="flex justify-between items-center mb-6">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">🎓 Generate Certificates</h1>
                    <p class="text-gray-600 mt-1">Issue certificates to students who completed courses</p>
                </div>
                <Link href="/admin/certificates" class="px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700">
                    ← Back to Certificates
                </Link>
            </div>
            
            <!-- Eligible Students (100% Complete) -->
            <div class="bg-white rounded-lg shadow mb-6">
                <div class="px-6 py-4 border-b bg-emerald-50 flex justify-between items-center">
                    <div>
                        <h2 class="text-xl font-bold text-emerald-900">✅ Ready for Certificate</h2>
                        <p class="text-sm text-emerald-700">Students who completed their courses (100%)</p>
                    </div>
                    <button 
                        @click="bulkGenerate"
                        v-if="eligibleEnrollments.data.length > 0"
                        class="px-4 py-2 bg-emerald-600 text-white rounded-lg hover:bg-emerald-700 font-semibold"
                    >
                        Generate All ({{ eligibleEnrollments.total }})
                    </button>
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
                    <tbody class="divide-y divide-gray-200">
                        <tr v-for="enrollment in eligibleEnrollments.data" :key="enrollment.id" class="hover:bg-gray-50">
                            <td class="px-6 py-4">
                                <div class="text-sm font-medium text-gray-900">{{ enrollment.user?.name }}</div>
                                <div class="text-sm text-gray-500">{{ enrollment.user?.email }}</div>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-900">{{ enrollment.course?.title }}</td>
                            <td class="px-6 py-4">
                                <span class="px-3 py-1 text-xs font-semibold rounded-full bg-emerald-100 text-emerald-800">
                                    100%
                                </span>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-500">
                                {{ new Date(enrollment.course_completed_at || enrollment.updated_at).toLocaleDateString() }}
                            </td>
                            <td class="px-6 py-4">
                                <button 
                                    @click="generateCertificate(enrollment.id)"
                                    class="px-4 py-2 bg-emerald-600 text-white text-sm rounded hover:bg-emerald-700 font-semibold"
                                >
                                    Generate Certificate
                                </button>
                            </td>
                        </tr>
                        <tr v-if="eligibleEnrollments.data.length === 0">
                            <td colspan="5" class="px-6 py-8 text-center text-gray-500">
                                No students ready for certificates at this time.
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            
            <!-- Manual Certificate Generation (All Students) -->
            <div class="bg-white rounded-lg shadow">
                <div class="px-6 py-4 border-b bg-orange-50">
                    <h2 class="text-xl font-bold text-orange-900">⚠️ Manual Certificate Issuance</h2>
                    <p class="text-sm text-orange-700">Generate certificates for students with incomplete progress (Admin Override)</p>
                </div>
                
                <table class="min-w-full">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Student</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Course</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Progress</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        <tr v-for="enrollment in allEnrollments.data" :key="enrollment.id" class="hover:bg-gray-50">
                            <td class="px-6 py-4">
                                <div class="text-sm font-medium text-gray-900">{{ enrollment.user?.name }}</div>
                                <div class="text-sm text-gray-500">{{ enrollment.user?.email }}</div>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-900">{{ enrollment.course?.title }}</td>
                            <td class="px-6 py-4">
                                <div class="flex items-center">
                                    <div class="w-24 bg-gray-200 rounded-full h-2 mr-2">
                                        <div class="bg-blue-600 h-2 rounded-full" 
                                             :style="{ width: (enrollment.progress || 0) + '%' }"></div>
                                    </div>
                                    <span class="text-sm text-gray-600">{{ Math.round(enrollment.progress || 0) }}%</span>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <span :class="[
                                    'px-2 py-1 text-xs rounded-full',
                                    enrollment.status === 'completed' ? 'bg-emerald-100 text-emerald-800' :
                                    enrollment.status === 'active' ? 'bg-blue-100 text-blue-800' :
                                    'bg-gray-100 text-gray-800'
                                ]">
                                    {{ enrollment.status }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <button 
                                    @click="generateCertificate(enrollment.id)"
                                    class="px-4 py-2 bg-orange-600 text-white text-sm rounded hover:bg-orange-700 font-semibold"
                                >
                                    Force Generate
                                </button>
                            </td>
                        </tr>
                        <tr v-if="allEnrollments.data.length === 0">
                            <td colspan="5" class="px-6 py-8 text-center text-gray-500">
                                No enrollments available for manual certificate generation.
                            </td>
                        </tr>
                    </tbody>
                </table>
                
                <!-- Pagination -->
                <div v-if="allEnrollments.links" class="px-6 py-4 bg-gray-50 border-t">
                    <div class="flex justify-between items-center">
                        <div class="text-sm text-gray-700">
                            Showing {{ allEnrollments.from }} to {{ allEnrollments.to }} of {{ allEnrollments.total }}
                        </div>
                        <div class="flex gap-2">
                            <template v-for="link in allEnrollments.links" :key="link.label">
                                <Link v-if="link.url"
                                      :href="link.url"
                                      :class="[
                                          'px-3 py-1 border rounded',
                                          link.active ? 'bg-orange-600 text-white' : 'bg-white text-gray-700 hover:bg-gray-50'
                                      ]"
                                      v-html="link.label"
                                />
                                <span v-else
                                      :class="[
                                          'px-3 py-1 border rounded bg-gray-100 text-gray-400 cursor-not-allowed'
                                      ]"
                                      v-html="link.label"
                                />
                            </template>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>
