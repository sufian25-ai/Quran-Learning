<script setup>
import { ref, watch } from 'vue';
import { Head, Link, useForm, router } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { debounce } from 'lodash';

const props = defineProps({
    classes: Object,
    filters: Object,
});

const search = ref(props.filters.search || '');
const status = ref(props.filters.status || '');
const date = ref(props.filters.date || '');

watch([search, status, date], debounce(([newSearch, newStatus, newDate]) => {
    router.get(
        route('admin.classes.index'),
        { search: newSearch, status: newStatus, date: newDate },
        { preserveState: true, replace: true }
    );
}, 300));

const form = useForm({});

const deleteClass = (id) => {
    if (confirm('Are you sure you want to delete this class? This action cannot be undone.')) {
        form.delete(route('admin.classes.destroy', id), {
            preserveScroll: true,
        });
    }
};

const formatDate = (dateString) => {
    const d = new Date(dateString);
    return d.toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' }) + 
           ' ' + d.toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit' });
};

const getStatusColor = (status) => {
    return {
        scheduled: 'bg-blue-100 text-blue-800',
        live: 'bg-green-100 text-green-800',
        completed: 'bg-gray-100 text-gray-800',
        cancelled: 'bg-red-100 text-red-800',
        rescheduled: 'bg-amber-100 text-amber-800',
    }[status] || 'bg-gray-100 text-gray-800';
};
</script>

<template>
    <Head title="Manage Classes | Admin" />

    <AdminLayout>
        <div class="space-y-6">
            <!-- Header -->
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div>
                    <h2 class="text-2xl font-bold text-gray-900">Manage Classes</h2>
                    <p class="text-gray-500 text-sm mt-1">View and manage all class sessions, track attendance.</p>
                </div>
            </div>

            <!-- Filters -->
            <div class="bg-white p-4 rounded-xl shadow-sm border border-gray-100 flex flex-wrap gap-4 items-center">
                <div class="relative flex-1 min-w-[200px]">
                    <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400">🔍</span>
                    <input
                        v-model="search"
                        type="text"
                        placeholder="Search classes, teachers, batches..."
                        class="w-full pl-10 pr-4 py-2 rounded-lg border border-gray-200 focus:border-primary-500 focus:ring-primary-500"
                    />
                </div>
                
                <select
                    v-model="status"
                    class="rounded-lg border border-gray-200 py-2 px-4 focus:border-primary-500 focus:ring-primary-500"
                >
                    <option value="">All Statuses</option>
                    <option value="scheduled">Scheduled</option>
                    <option value="live">Live</option>
                    <option value="completed">Completed</option>
                    <option value="cancelled">Cancelled</option>
                </select>

                <input
                    v-model="date"
                    type="date"
                    class="rounded-lg border border-gray-200 py-2 px-4 focus:border-primary-500 focus:ring-primary-500"
                />

                <button 
                    @click="search = ''; status = ''; date = ''"
                    class="text-sm text-gray-500 hover:text-gray-700"
                >
                    Clear Filters
                </button>
            </div>

            <!-- Table -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left">
                        <thead class="bg-gray-50 border-b border-gray-100">
                            <tr>
                                <th class="px-6 py-3 text-xs font-semibold text-gray-500 uppercase">Class Info</th>
                                <th class="px-6 py-3 text-xs font-semibold text-gray-500 uppercase">Teacher</th>
                                <th class="px-6 py-3 text-xs font-semibold text-gray-500 uppercase">Schedule</th>
                                <th class="px-6 py-3 text-xs font-semibold text-gray-500 uppercase">Status</th>
                                <th class="px-6 py-3 text-xs font-semibold text-gray-500 uppercase">Attendance</th>
                                <th class="px-6 py-3 text-xs font-semibold text-gray-500 uppercase text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            <tr v-if="classes.data.length === 0">
                                <td colspan="6" class="px-6 py-12 text-center text-gray-500">
                                    No classes found matching your criteria.
                                </td>
                            </tr>
                            <tr v-for="class_ in classes.data" :key="class_.id" class="hover:bg-gray-50 transition">
                                <td class="px-6 py-4">
                                    <p class="font-medium text-gray-900">{{ class_.title }}</p>
                                    <p class="text-sm text-gray-500">{{ class_.batch?.name ?? 'Private Class' }}</p>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-2">
                                        <div class="w-8 h-8 rounded-full bg-primary-100 text-primary-600 flex items-center justify-center font-bold text-xs">
                                            {{ class_.teacher?.name?.charAt(0) ?? 'T' }}
                                        </div>
                                        <span class="text-sm text-gray-700">{{ class_.teacher?.name }}</span>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <p class="text-sm text-gray-900">{{ formatDate(class_.scheduled_at) }}</p>
                                    <p class="text-xs text-gray-500">{{ class_.duration_minutes }} mins</p>
                                </td>
                                <td class="px-6 py-4">
                                    <span :class="['px-2 py-1 rounded-md text-xs font-medium capitalize', getStatusColor(class_.status)]">
                                        {{ class_.status }}
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-2">
                                        <span class="text-lg font-semibold text-gray-900">{{ class_.attendee_count }}</span>
                                        <span class="text-xs text-gray-500">students present</span>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-right space-x-2">
                                    <button 
                                        @click="deleteClass(class_.id)"
                                        class="text-red-500 hover:text-red-700 text-sm font-medium transition"
                                    >
                                        Delete
                                    </button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div v-if="classes.last_page > 1" class="px-6 py-4 border-t border-gray-100 flex justify-center">
                    <div class="flex gap-1">
                        <Link 
                            v-for="(link, i) in classes.links" 
                            :key="i"
                            :href="link.url"
                            v-html="link.label"
                            :class="[
                                'px-3 py-1 rounded text-sm',
                                link.active ? 'bg-primary-500 text-white' : 'text-gray-500 hover:bg-gray-100',
                                !link.url && 'opacity-50 cursor-not-allowed'
                            ]"
                        />
                    </div>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>
