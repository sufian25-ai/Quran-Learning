<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import StudentLayout from '@/Layouts/StudentLayout.vue';
import { ref } from 'vue';

defineProps({
    tickets: {
        type: Array,
        default: () => []
    }
});

const showForm = ref(false);

const form = useForm({
    subject: '',
    message: '',
    priority: 'normal',
});

const submit = () => {
    form.post('/support', {
        onSuccess: () => {
            showForm.value = false;
            form.reset();
        },
    });
};

const getStatusColor = (status) => {
    switch (status) {
        case 'open': return 'bg-blue-100 text-blue-700';
        case 'pending': return 'bg-yellow-100 text-yellow-700';
        case 'resolved': return 'bg-green-100 text-green-700';
        default: return 'bg-gray-100 text-gray-700';
    }
};

const formatDate = (dateString) => {
    return new Date(dateString).toLocaleDateString('en-US', {
        month: 'short',
        day: 'numeric',
        year: 'numeric'
    });
};
</script>

<template>
    <Head title="Support" />

    <StudentLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="font-display text-2xl font-bold text-gray-900">
                        Support Center 💬
                    </h2>
                    <p class="text-gray-500 mt-1">Get help with your learning journey</p>
                </div>
                <button
                    @click="showForm = !showForm"
                    class="inline-flex items-center px-4 py-2 bg-primary-500 hover:bg-primary-600 text-white text-sm font-semibold rounded-xl transition-colors"
                >
                    {{ showForm ? 'Cancel' : 'New Ticket' }}
                </button>
            </div>
        </template>

        <div class="py-8">
            <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
                <!-- New Ticket Form -->
                <div v-if="showForm" class="bg-white rounded-2xl shadow-soft p-6 mb-8">
                    <h3 class="font-semibold text-gray-900 mb-4">Create Support Ticket</h3>
                    <form @submit.prevent="submit" class="space-y-4">
                        <div>
                            <label for="subject" class="block text-sm font-medium text-gray-700 mb-1">Subject</label>
                            <input
                                id="subject"
                                v-model="form.subject"
                                type="text"
                                class="w-full px-4 py-2 border border-gray-300 rounded-xl focus:ring-primary-500 focus:border-primary-500"
                                placeholder="Brief description of your issue"
                                required
                            />
                        </div>
                        <div>
                            <label for="priority" class="block text-sm font-medium text-gray-700 mb-1">Priority</label>
                            <select
                                id="priority"
                                v-model="form.priority"
                                class="w-full px-4 py-2 border border-gray-300 rounded-xl focus:ring-primary-500 focus:border-primary-500"
                            >
                                <option value="low">Low</option>
                                <option value="normal">Normal</option>
                                <option value="high">High</option>
                            </select>
                        </div>
                        <div>
                            <label for="message" class="block text-sm font-medium text-gray-700 mb-1">Message</label>
                            <textarea
                                id="message"
                                v-model="form.message"
                                rows="4"
                                class="w-full px-4 py-2 border border-gray-300 rounded-xl focus:ring-primary-500 focus:border-primary-500"
                                placeholder="Describe your issue in detail..."
                                required
                            ></textarea>
                        </div>
                        <button
                            type="submit"
                            :disabled="form.processing"
                            class="w-full py-3 bg-primary-500 hover:bg-primary-600 text-white font-semibold rounded-xl transition-colors disabled:opacity-50"
                        >
                            {{ form.processing ? 'Submitting...' : 'Submit Ticket' }}
                        </button>
                    </form>
                </div>

                <!-- FAQ Section -->
                <div class="bg-white rounded-2xl shadow-soft p-6 mb-8">
                    <h3 class="font-semibold text-gray-900 mb-4">Frequently Asked Questions</h3>
                    <div class="space-y-4">
                        <details class="group border-b pb-4">
                            <summary class="cursor-pointer font-medium text-gray-700 flex justify-between items-center">
                                How do I join my live class?
                                <span class="text-gray-400 group-open:rotate-180 transition-transform">▼</span>
                            </summary>
                            <p class="mt-2 text-gray-600 text-sm">
                                Go to your Dashboard and find the "Upcoming Classes" section. Click "Join Class" when it's time for your session. Make sure you have Zoom installed.
                            </p>
                        </details>
                        <details class="group border-b pb-4">
                            <summary class="cursor-pointer font-medium text-gray-700 flex justify-between items-center">
                                Where can I find class recordings?
                                <span class="text-gray-400 group-open:rotate-180 transition-transform">▼</span>
                            </summary>
                            <p class="mt-2 text-gray-600 text-sm">
                                All recordings are available in the "Class Recordings" section. You can access it from your dashboard or the main navigation menu.
                            </p>
                        </details>
                        <details class="group border-b pb-4">
                            <summary class="cursor-pointer font-medium text-gray-700 flex justify-between items-center">
                                How do I access study materials?
                                <span class="text-gray-400 group-open:rotate-180 transition-transform">▼</span>
                            </summary>
                            <p class="mt-2 text-gray-600 text-sm">
                                Visit the "Resources" section to download study materials like PDFs, audio files, and other content uploaded by your teachers.
                            </p>
                        </details>
                    </div>
                </div>

                <!-- My Tickets -->
                <div class="bg-white rounded-2xl shadow-soft p-6">
                    <h3 class="font-semibold text-gray-900 mb-4">My Support Tickets</h3>
                    
                    <div v-if="tickets.length" class="space-y-3">
                        <div
                            v-for="ticket in tickets"
                            :key="ticket.id"
                            class="p-4 border border-gray-200 rounded-xl hover:border-primary-200 transition-colors"
                        >
                            <div class="flex items-center justify-between">
                                <h4 class="font-medium text-gray-900">{{ ticket.subject }}</h4>
                                <span :class="['text-xs px-2 py-1 rounded-full font-medium', getStatusColor(ticket.status)]">
                                    {{ ticket.status }}
                                </span>
                            </div>
                            <p class="text-sm text-gray-500 mt-1">
                                Created: {{ formatDate(ticket.created_at) }}
                            </p>
                        </div>
                    </div>
                    
                    <div v-else class="text-center py-8">
                        <p class="text-gray-500">No support tickets yet. If you need help, create a new ticket above!</p>
                    </div>
                </div>

                <!-- Contact Info -->
                <div class="mt-8 text-center text-gray-500">
                    <p>Need immediate help? Email us at <a href="mailto:support@quranlearn.com" class="text-primary-500">support@quranlearn.com</a></p>
                </div>
            </div>
        </div>
    </StudentLayout>
</template>
