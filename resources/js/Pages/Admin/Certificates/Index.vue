<script setup>
import { Head, Link, router } from '@inertiajs/vue3';
import { ref } from 'vue';
import AdminLayout from '@/Layouts/AdminLayout.vue';

const props = defineProps({
    certificates: Object,
    courses: Array,
    stats: Object,
    filters: Object
});

const search = ref(props.filters?.search || '');
const selectedCourse = ref(props.filters?.course_id || '');

const searchCertificates = () => {
    router.get('/admin/certificates', {
        search: search.value,
        course_id: selectedCourse.value
    }, { preserveState: true });
};

const deleteCertificate = (id) => {
    if (confirm('Are you sure you want to delete this certificate?')) {
        router.delete(`/admin/certificates/${id}`);
    }
};
</script>

<template>
    <AdminLayout>
        <Head title="Certificate Management" />
        
        <div class="py-6">
            <!-- Header -->
            <div class="mb-6">
                <h1 class="text-3xl font-bold text-gray-900">📜 Certificate Management</h1>
                <p class="text-gray-600 mt-1">Manage and issue course completion certificates</p>
            </div>
            
            <!-- Statistics Cards -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                <div class="bg-white rounded-lg shadow p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-gray-600">Total Certificates</p>
                            <p class="text-3xl font-bold text-gray-900">{{ stats.total_certificates }}</p>
                        </div>
                        <div class="p-3 bg-emerald-100 rounded-full">
                            <svg class="w-8 h-8 text-emerald-600" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M9 2a2 2 0 00-2 2v8a2 2 0 002 2h6a2 2 0 002-2V6.414A2 2 0 0016.414 5L14 2.586A2 2 0 0012.586 2H9z"/>
                            </svg>
                        </div>
                    </div>
                </div>
                
                <div class="bg-white rounded-lg shadow p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-gray-600">This Month</p>
                            <p class="text-3xl font-bold text-gray-900">{{ stats.this_month }}</p>
                        </div>
                        <div class="p-3 bg-blue-100 rounded-full">
                            <svg class="w-8 h-8 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd"/>
                            </svg>
                        </div>
                    </div>
                </div>
                
                <div class="bg-white rounded-lg shadow p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-gray-600">Pending Generation</p>
                            <p class="text-3xl font-bold text-gray-900">{{ stats.pending_enrollments }}</p>
                        </div>
                        <div class="p-3 bg-orange-100 rounded-full">
                            <svg class="w-8 h-8 text-orange-600" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"/>
                            </svg>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Action Buttons -->
            <div class="bg-white rounded-lg shadow p-4 mb-6 flex gap-3">
                <Link href="/admin/certificates/eligible" 
                      class="px-4 py-2 bg-emerald-600 text-white rounded-lg hover:bg-emerald-700 font-semibold">
                    🎓 Generate New Certificates
                </Link>
                <Link href="/admin/certificates/student-progress" 
                      class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 font-semibold">
                    📊 Student Progress
                </Link>
            </div>
            
            <!-- Search & Filter -->
            <div class="bg-white rounded-lg shadow p-4 mb-6">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <input 
                        v-model="search"
                        @keyup.enter="searchCertificates"
                        type="text" 
                        placeholder="Search by student name or certificate number..."
                        class="px-4 py-2 border rounded-lg focus:ring-2 focus:ring-emerald-500"
                    />
                    <select 
                        v-model="selectedCourse"
                        @change="searchCertificates"
                        class="px-4 py-2 border rounded-lg focus:ring-2 focus:ring-emerald-500"
                    >
                        <option value="">All Courses</option>
                        <option v-for="course in courses" :key="course.id" :value="course.id">
                            {{ course.title }}
                        </option>
                    </select>
                    <button 
                        @click="searchCertificates"
                        class="px-4 py-2 bg-gray-700 text-white rounded-lg hover:bg-gray-800"
                    >
                        Search
                    </button>
                </div>
            </div>
            
            <!-- Certificates Table -->
            <div class="bg-white rounded-lg shadow overflow-hidden">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Certificate #
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Student
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Course
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Completion
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Issued Date
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Actions
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <tr v-for="cert in certificates.data" :key="cert.id" class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="font-mono text-sm font-semibold text-gray-900">
                                    {{ cert.certificate_number }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900">{{ cert.student_name }}</div>
                                <div class="text-sm text-gray-500">{{ cert.user?.email }}</div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm text-gray-900">{{ cert.course_title }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-3 py-1 text-xs font-semibold rounded-full bg-emerald-100 text-emerald-800">
                                    {{ Math.round(cert.completion_percentage) }}%
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ new Date(cert.created_at).toLocaleDateString() }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm space-x-2">
                                <a :href="`/certificates/verify/${cert.verification_code}`" 
                                   target="_blank"
                                   class="text-blue-600 hover:text-blue-900">
                                    Verify
                                </a>
                                <button 
                                    @click="deleteCertificate(cert.id)"
                                    class="text-red-600 hover:text-red-900">
                                    Delete
                                </button>
                            </td>
                        </tr>
                        <tr v-if="certificates.data.length === 0">
                            <td colspan="6" class="px-6 py-12 text-center text-gray-500">
                                No certificates found.
                            </td>
                        </tr>
                    </tbody>
                </table>
                
                <!-- Pagination -->
                <div v-if="certificates.links" class="px-6 py-4 bg-gray-50 border-t">
                    <div class="flex justify-between items-center">
                        <div class="text-sm text-gray-700">
                            Showing {{ certificates.from }} to {{ certificates.to }} of {{ certificates.total }} results
                        </div>
                        <div class="flex gap-2">
                            <template v-for="link in certificates.links" :key="link.label">
                                <Link v-if="link.url"
                                      :href="link.url"
                                      :class="[
                                          'px-3 py-1 border rounded',
                                          link.active ? 'bg-emerald-600 text-white border-emerald-600' : 'bg-white text-gray-700 hover:bg-gray-50'
                                      ]"
                                      v-html="link.label"
                                />
                                <span v-else
                                      :class="[
                                          'px-3 py-1 border rounded',
                                          'bg-gray-100 text-gray-400 cursor-not-allowed'
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
