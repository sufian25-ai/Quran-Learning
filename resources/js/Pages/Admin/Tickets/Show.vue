<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';

const props = defineProps({
    auth: Object,
    ticket: {
        type: Object,
        required: true
    },
    replies: {
        type: Array,
        default: () => []
    }
});

const form = useForm({
    message: '',
});

const submitReply = () => {
    form.post(`/admin/tickets/${props.ticket.id}/reply`, {
        onSuccess: () => form.reset(),
        preserveScroll: true,
    });
};

const getStatusColor = (status) => {
    const colors = {
        'open': 'bg-yellow-100 text-yellow-700',
        'in_progress': 'bg-blue-100 text-blue-700',
        'resolved': 'bg-green-100 text-green-700',
        'closed': 'bg-gray-100 text-gray-700',
    };
    return colors[status] || 'bg-gray-100 text-gray-700';
};

const getPriorityColor = (priority) => {
    const colors = {
        'low': 'bg-gray-100 text-gray-700',
        'normal': 'bg-blue-100 text-blue-700',
        'high': 'bg-orange-100 text-orange-700',
        'urgent': 'bg-red-100 text-red-700',
    };
    return colors[priority] || 'bg-gray-100 text-gray-700';
};

const formatDate = (dateString) => {
    return new Date(dateString).toLocaleString('en-US', {
        month: 'short', day: 'numeric', year: 'numeric',
        hour: '2-digit', minute: '2-digit'
    });
};
</script>

<template>
    <Head :title="`Ticket #${ticket.id} | Admin`" />

    <AdminLayout>
        <div class="max-w-4xl">
            <!-- Header -->
            <div class="mb-6">
                <Link href="/admin/tickets" class="text-gray-500 hover:text-gray-700">
                    ← Back to Tickets
                </Link>
                <h1 class="text-2xl font-bold text-gray-900 mt-4">Ticket #{{ ticket.id }}</h1>
            </div>

            <!-- Ticket Info -->
            <div class="bg-white rounded-2xl shadow-sm p-6 mb-6">
                <div class="flex items-start justify-between mb-4">
                    <div>
                        <h3 class="text-xl font-semibold text-gray-900">{{ ticket.subject }}</h3>
                        <p class="text-sm text-gray-500 mt-1">
                            By {{ ticket.user?.name }} ({{ ticket.user?.email }}) • {{ formatDate(ticket.created_at) }}
                        </p>
                    </div>
                    <div class="flex items-center gap-2">
                        <span :class="['px-3 py-1 text-xs font-medium rounded-full capitalize', getPriorityColor(ticket.priority)]">
                            {{ ticket.priority }}
                        </span>
                        <span :class="['px-3 py-1 text-xs font-medium rounded-full capitalize', getStatusColor(ticket.status)]">
                            {{ ticket.status?.replace('_', ' ') }}
                        </span>
                    </div>
                </div>
                <p class="text-gray-700 whitespace-pre-wrap">{{ ticket.message }}</p>
                
                <div class="mt-6 flex gap-2" v-if="ticket.status !== 'resolved'">
                    <Link
                        :href="`/admin/tickets/${ticket.id}/close`"
                        method="post"
                        as="button"
                        class="px-4 py-2 bg-green-500 hover:bg-green-600 text-white text-sm font-medium rounded-lg transition-colors"
                    >
                        ✓ Mark as Resolved
                    </Link>
                </div>
            </div>

            <!-- Replies -->
            <div class="bg-white rounded-2xl shadow-sm p-6 mb-6">
                <h4 class="font-semibold text-gray-900 mb-4">Replies ({{ replies.length }})</h4>
                
                <div v-if="replies.length" class="space-y-4">
                    <div
                        v-for="reply in replies"
                        :key="reply.id"
                        :class="[
                            'p-4 rounded-xl',
                            reply.is_admin ? 'bg-blue-50 border-l-4 border-blue-500' : 'bg-gray-50'
                        ]"
                    >
                        <div class="flex items-center justify-between mb-2">
                            <span class="font-medium text-gray-900">
                                {{ reply.user?.name }}
                                <span v-if="reply.is_admin" class="text-xs bg-blue-100 text-blue-700 px-2 py-0.5 rounded ml-2">Admin</span>
                            </span>
                            <span class="text-sm text-gray-500">{{ formatDate(reply.created_at) }}</span>
                        </div>
                        <p class="text-gray-700 whitespace-pre-wrap">{{ reply.message }}</p>
                    </div>
                </div>
                <p v-else class="text-gray-500 text-center py-4">No replies yet</p>
            </div>

            <!-- Reply Form -->
            <div class="bg-white rounded-2xl shadow-sm p-6">
                <h4 class="font-semibold text-gray-900 mb-4">Send Reply</h4>
                <form @submit.prevent="submitReply">
                    <textarea
                        v-model="form.message"
                        rows="4"
                        required
                        placeholder="Type your reply..."
                        class="w-full rounded-xl border-gray-200 focus:border-primary-500 focus:ring-primary-500 mb-4"
                    ></textarea>
                    <button
                        type="submit"
                        :disabled="form.processing"
                        class="px-6 py-3 bg-primary-500 hover:bg-primary-600 text-white font-medium rounded-xl transition-colors disabled:opacity-50"
                    >
                        {{ form.processing ? 'Sending...' : 'Send Reply' }}
                    </button>
                </form>
            </div>
        </div>
    </AdminLayout>
</template>
