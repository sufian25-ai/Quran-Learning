<script setup>
import { Head, Link, router } from '@inertiajs/vue3';
import { ref, computed } from 'vue';
import AdminLayout from '@/Layouts/AdminLayout.vue';

const props = defineProps({
    conversations: {
        type: Object,
        default: () => ({ data: [] })
    },
    filters: {
        type: Object,
        default: () => ({})
    }
});

const selectedStatus = ref(props.filters.status || 'all');

const statusFilters = [
    { id: 'all', name: 'All', icon: '📋' },
    { id: 'pending', name: 'Pending', icon: '⏳' },
    { id: 'active', name: 'Active', icon: '💬' },
    { id: 'closed', name: 'Closed', icon: '✅' },
];

const filterByStatus = (status) => {
    selectedStatus.value = status;
    router.get('/admin/chat', { status }, { preserveState: true });
};

const getStatusBadge = (status) => {
    const badges = {
        pending: 'bg-yellow-100 text-yellow-700',
        active: 'bg-green-100 text-green-700',
        closed: 'bg-gray-100 text-gray-600',
    };
    return badges[status] || 'bg-gray-100 text-gray-600';
};

const pendingCount = computed(() => {
    return props.conversations.data.filter(c => c.status === 'pending').length;
});
</script>

<template>
    <Head title="Chat Support | Admin" />

    <AdminLayout>
        <!-- Header -->
        <div class="flex items-center justify-between mb-8">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Chat Support</h1>
                <p class="text-gray-500 mt-1">Manage conversations with visitors and students</p>
            </div>
            <div v-if="pendingCount > 0" class="px-4 py-2 bg-yellow-100 text-yellow-800 rounded-xl font-medium animate-pulse">
                {{ pendingCount }} pending conversation{{ pendingCount > 1 ? 's' : '' }}
            </div>
        </div>

        <!-- Status Filter Tabs -->
        <div class="flex gap-2 mb-6 bg-white p-2 rounded-xl shadow-sm border border-gray-100">
            <button
                v-for="filter in statusFilters"
                :key="filter.id"
                @click="filterByStatus(filter.id)"
                :class="[
                    'px-4 py-2 rounded-lg font-medium transition-all flex items-center gap-2',
                    selectedStatus === filter.id
                        ? 'bg-emerald-500 text-white shadow-md'
                        : 'text-gray-600 hover:bg-gray-100'
                ]"
            >
                <span>{{ filter.icon }}</span>
                {{ filter.name }}
            </button>
        </div>

        <!-- Conversations List -->
        <div class="bg-white rounded-2xl shadow-lg border border-gray-100 overflow-hidden">
            <!-- Empty State -->
            <div v-if="conversations.data.length === 0" class="p-12 text-center">
                <div class="text-6xl mb-4">💬</div>
                <h3 class="text-xl font-semibold text-gray-900 mb-2">No conversations yet</h3>
                <p class="text-gray-500">When visitors start a chat, they'll appear here.</p>
            </div>

            <!-- Conversation Items -->
            <div v-else class="divide-y divide-gray-100">
                <Link
                    v-for="conversation in conversations.data"
                    :key="conversation.id"
                    :href="`/admin/chat/${conversation.id}`"
                    class="block p-5 hover:bg-gray-50 transition-colors"
                >
                    <div class="flex items-start justify-between">
                        <div class="flex items-start gap-4">
                            <!-- Avatar -->
                            <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-emerald-400 to-teal-500 flex items-center justify-center text-white font-bold text-lg">
                                {{ conversation.guest_name?.charAt(0) || 'G' }}
                            </div>
                            
                            <div>
                                <div class="flex items-center gap-2">
                                    <h3 class="font-semibold text-gray-900">{{ conversation.guest_name }}</h3>
                                    <span v-if="conversation.has_unread" class="w-2 h-2 bg-red-500 rounded-full animate-pulse"></span>
                                </div>
                                <p class="text-sm text-gray-500">{{ conversation.guest_email }}</p>
                                <p class="text-gray-600 mt-1 line-clamp-1">{{ conversation.last_message }}</p>
                            </div>
                        </div>
                        
                        <div class="text-right">
                            <span :class="['inline-block px-3 py-1 rounded-full text-xs font-medium', getStatusBadge(conversation.status)]">
                                {{ conversation.status }}
                            </span>
                            <p class="text-xs text-gray-400 mt-2">{{ conversation.last_message_at }}</p>
                            <p v-if="conversation.assigned_to" class="text-xs text-emerald-600 mt-1">
                                Assigned: {{ conversation.assigned_to }}
                            </p>
                        </div>
                    </div>
                </Link>
            </div>
        </div>
    </AdminLayout>
</template>

<style scoped>
.line-clamp-1 {
    display: -webkit-box;
    -webkit-line-clamp: 1;
    -webkit-box-orient: vertical;
    overflow: hidden;
}
</style>
