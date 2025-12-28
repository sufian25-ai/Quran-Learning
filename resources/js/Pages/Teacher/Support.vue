<script setup>
import { Head, Link, useForm, usePage } from '@inertiajs/vue3';
import TeacherLayout from '@/Layouts/TeacherLayout.vue';
import { ref } from 'vue';

defineProps({
    tickets: {
        type: Array,
        default: () => []
    }
});

const page = usePage();
const showForm = ref(false);

const form = useForm({
    subject: '',
    message: '',
    priority: 'normal',
    category: 'technical',
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
        case 'open': return 'bg-blue-100 text-blue-700 dark:bg-blue-900/50 dark:text-blue-400';
        case 'pending': return 'bg-yellow-100 text-yellow-700 dark:bg-yellow-900/50 dark:text-yellow-400';
        case 'resolved': return 'bg-green-100 text-green-700 dark:bg-green-900/50 dark:text-green-400';
        default: return 'bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-300';
    }
};

const formatDate = (dateString) => {
    return new Date(dateString).toLocaleDateString('en-US', {
        month: 'short',
        day: 'numeric',
        year: 'numeric'
    });
};

const teacherFaqs = [
    {
        question: 'How do I start a live class?',
        answer: 'Go to your Dashboard and find the scheduled class in "Today\'s Schedule". Click "Start Class" when it\'s time. This will launch the Zoom meeting for your students.'
    },
    {
        question: 'How do I mark attendance?',
        answer: 'After completing a class, click "Mark Attendance" from the Quick Actions. Select present/absent for each student and add class notes if needed.'
    },
    {
        question: 'How do I upload resources for my students?',
        answer: 'Go to Resources in the sidebar, click "Upload Resource", select the course and batch, then upload your PDF, audio, or video files.'
    },
    {
        question: 'When and how will I receive payment?',
        answer: 'Payments are processed on the 1st and 15th of each month. You can track your earnings in the Earnings section. Payments are sent via bKash/Nagad.'
    },
    {
        question: 'How do I review student recitations?',
        answer: 'Navigate to Recitations in your dashboard. You\'ll see pending submissions from your students. Listen and provide feedback with ratings.'
    },
    {
        question: 'How can I add a new batch?',
        answer: 'Only admins can create new batches. Contact support with your preferred schedule and course details, and we\'ll set it up for you.'
    }
];

const categories = [
    { value: 'technical', label: '🔧 Technical Issue' },
    { value: 'payment', label: '💰 Payment & Earnings' },
    { value: 'student', label: '👥 Student Related' },
    { value: 'schedule', label: '📅 Schedule Change' },
    { value: 'other', label: '📝 Other' },
];
</script>

<template>
    <Head title="Teacher Support" />

    <TeacherLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="font-display text-2xl font-bold text-gray-900 dark:text-white">
                        Teacher Support Center 🎓
                    </h2>
                    <p class="text-gray-500 dark:text-slate-400 mt-1">Get help with teaching, payments, and technical issues</p>
                </div>
                <button
                    @click="showForm = !showForm"
                    class="inline-flex items-center px-5 py-2.5 bg-emerald-500 hover:bg-emerald-600 text-white text-sm font-semibold rounded-xl transition-all shadow-lg shadow-emerald-500/20"
                >
                    <span class="mr-2">{{ showForm ? '✕' : '✉️' }}</span>
                    {{ showForm ? 'Cancel' : 'New Ticket' }}
                </button>
            </div>
        </template>

        <div class="py-6">
            <div class="max-w-5xl mx-auto px-4">
                <!-- New Ticket Form -->
                <div v-if="showForm" class="bg-white dark:bg-slate-800 rounded-2xl shadow-soft p-6 mb-8 border-l-4 border-emerald-500">
                    <div class="flex items-center gap-3 mb-6">
                        <div class="w-10 h-10 rounded-xl bg-emerald-100 dark:bg-emerald-900/50 flex items-center justify-center">
                            <span class="text-xl">📝</span>
                        </div>
                        <div>
                            <h3 class="font-semibold text-gray-900 dark:text-white">Create Support Ticket</h3>
                            <p class="text-sm text-gray-500 dark:text-slate-400">We typically respond within 24 hours</p>
                        </div>
                    </div>
                    
                    <form @submit.prevent="submit" class="space-y-4">
                        <div class="grid md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1">Category</label>
                                <select v-model="form.category" class="w-full px-4 py-2.5 border border-gray-200 dark:border-slate-600 dark:bg-slate-700 dark:text-white rounded-xl focus:ring-emerald-500 focus:border-emerald-500">
                                    <option v-for="cat in categories" :key="cat.value" :value="cat.value">
                                        {{ cat.label }}
                                    </option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1">Priority</label>
                                <select v-model="form.priority" class="w-full px-4 py-2.5 border border-gray-200 dark:border-slate-600 dark:bg-slate-700 dark:text-white rounded-xl focus:ring-emerald-500 focus:border-emerald-500">
                                    <option value="low">🟢 Low - Can wait</option>
                                    <option value="normal">🟡 Normal</option>
                                    <option value="high">🔴 High - Urgent</option>
                                </select>
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1">Subject</label>
                            <input v-model="form.subject" type="text" class="w-full px-4 py-2.5 border border-gray-200 dark:border-slate-600 dark:bg-slate-700 dark:text-white rounded-xl focus:ring-emerald-500 focus:border-emerald-500" placeholder="Brief description of your issue" required />
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1">Message</label>
                            <textarea v-model="form.message" rows="4" class="w-full px-4 py-2.5 border border-gray-200 dark:border-slate-600 dark:bg-slate-700 dark:text-white rounded-xl focus:ring-emerald-500 focus:border-emerald-500" placeholder="Please describe your issue in detail. Include any relevant batch names, dates, or screenshots..." required></textarea>
                        </div>
                        <div class="flex justify-end">
                            <button type="submit" :disabled="form.processing" class="px-6 py-2.5 bg-emerald-500 hover:bg-emerald-600 text-white font-semibold rounded-xl transition-colors disabled:opacity-50 shadow-lg shadow-emerald-500/20">
                                {{ form.processing ? 'Submitting...' : 'Submit Ticket' }}
                            </button>
                        </div>
                    </form>
                </div>

                <!-- Quick Help Cards -->
                <div class="grid md:grid-cols-3 gap-4 mb-8">
                    <div class="bg-gradient-to-br from-emerald-500 to-teal-600 rounded-2xl p-5 text-white">
                        <div class="text-3xl mb-2">📧</div>
                        <h4 class="font-semibold mb-1">Email Support</h4>
                        <p class="text-sm text-emerald-100">teacher@quranlearn.com</p>
                    </div>
                    <div class="bg-gradient-to-br from-blue-500 to-indigo-600 rounded-2xl p-5 text-white">
                        <div class="text-3xl mb-2">💬</div>
                        <h4 class="font-semibold mb-1">WhatsApp</h4>
                        <p class="text-sm text-blue-100">+880 1XXX-XXXXXX</p>
                    </div>
                    <div class="bg-gradient-to-br from-purple-500 to-pink-600 rounded-2xl p-5 text-white">
                        <div class="text-3xl mb-2">📱</div>
                        <h4 class="font-semibold mb-1">Office Hours</h4>
                        <p class="text-sm text-purple-100">Sat-Thu, 9 AM - 6 PM</p>
                    </div>
                </div>

                <!-- Teacher FAQs -->
                <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-soft p-6 mb-8">
                    <div class="flex items-center gap-3 mb-6">
                        <div class="w-10 h-10 rounded-xl bg-amber-100 dark:bg-amber-900/50 flex items-center justify-center">
                            <span class="text-xl">❓</span>
                        </div>
                        <div>
                            <h3 class="font-semibold text-gray-900 dark:text-white">Frequently Asked Questions</h3>
                            <p class="text-sm text-gray-500 dark:text-slate-400">Common questions from teachers</p>
                        </div>
                    </div>
                    
                    <div class="space-y-3">
                        <details v-for="(faq, index) in teacherFaqs" :key="index" class="group bg-gray-50 dark:bg-slate-700 rounded-xl">
                            <summary class="cursor-pointer font-medium text-gray-700 dark:text-slate-200 flex justify-between items-center p-4 hover:bg-gray-100 dark:hover:bg-slate-600 rounded-xl transition-colors">
                                {{ faq.question }}
                                <span class="text-gray-400 group-open:rotate-180 transition-transform ml-2">▼</span>
                            </summary>
                            <p class="px-4 pb-4 text-gray-600 dark:text-slate-400 text-sm leading-relaxed">
                                {{ faq.answer }}
                            </p>
                        </details>
                    </div>
                </div>

                <!-- My Tickets -->
                <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-soft p-6">
                    <div class="flex items-center justify-between mb-6">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-xl bg-blue-100 dark:bg-blue-900/50 flex items-center justify-center">
                                <span class="text-xl">🎫</span>
                            </div>
                            <div>
                                <h3 class="font-semibold text-gray-900 dark:text-white">My Support Tickets</h3>
                                <p class="text-sm text-gray-500 dark:text-slate-400">Track your previous requests</p>
                            </div>
                        </div>
                        <span class="text-sm text-gray-500 dark:text-slate-400">{{ tickets.length }} ticket(s)</span>
                    </div>
                    
                    <div v-if="tickets.length" class="space-y-3">
                        <div
                            v-for="ticket in tickets"
                            :key="ticket.id"
                            class="p-4 border border-gray-100 dark:border-slate-700 rounded-xl hover:border-emerald-200 dark:hover:border-emerald-800 transition-colors"
                        >
                            <div class="flex items-center justify-between">
                                <h4 class="font-medium text-gray-900 dark:text-white">{{ ticket.subject }}</h4>
                                <span :class="['text-xs px-3 py-1 rounded-full font-medium', getStatusColor(ticket.status)]">
                                    {{ ticket.status }}
                                </span>
                            </div>
                            <p class="text-sm text-gray-500 dark:text-slate-400 mt-2">
                                #{{ ticket.id }} • Created: {{ formatDate(ticket.created_at) }}
                            </p>
                        </div>
                    </div>
                    
                    <div v-else class="text-center py-12">
                        <div class="text-5xl mb-4">🎉</div>
                        <p class="text-gray-500 dark:text-slate-400">No support tickets yet!</p>
                        <p class="text-sm text-gray-400 dark:text-slate-500 mt-1">Check the FAQs above or create a ticket if you need help.</p>
                    </div>
                </div>
            </div>
        </div>
    </TeacherLayout>
</template>
