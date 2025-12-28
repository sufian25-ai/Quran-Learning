<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';
import PublicLayout from '@/Layouts/PublicLayout.vue';

const form = useForm({
    name: '',
    email: '',
    phone: '',
    subject: '',
    message: '',
});

const submitted = ref(false);

const subjects = [
    'General Inquiry',
    'Course Information',
    'Technical Support',
    'Billing & Payments',
    'Teacher Application',
    'Partnership',
    'Other',
];

const submitForm = () => {
    form.post('/contact', {
        onSuccess: () => {
            submitted.value = true;
            form.reset();
        },
    });
};

const contactInfo = [
    {
        icon: '📧',
        title: 'Email Us',
        info: 'support@quranlearning.com',
        description: 'We reply within 24 hours',
    },
    {
        icon: '💬',
        title: 'Live Chat',
        info: 'Available 24/7',
        description: 'Chat with our support team',
    },
    {
        icon: '📞',
        title: 'Call Us',
        info: '+1 (555) 123-4567',
        description: 'Mon-Fri 9AM-6PM EST',
    },
    {
        icon: '📍',
        title: 'Office',
        info: 'New York, USA',
        description: 'By appointment only',
    },
];
</script>

<template>
    <Head title="Contact Us | QuranLearn" />
    
    <PublicLayout>
        <!-- Hero -->
        <section class="pt-32 pb-16 bg-gradient-to-br from-primary-50 to-white">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
                <h1 class="text-4xl sm:text-5xl font-display font-bold text-gray-900 mb-4">
                    Get in Touch
                </h1>
                <p class="text-xl text-gray-600 max-w-2xl mx-auto">
                    Have questions about our courses or need help? We're here to assist you on your Quran learning journey.
                </p>
            </div>
        </section>

        <!-- Contact Info Cards -->
        <section class="py-12 bg-white">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="grid md:grid-cols-4 gap-6">
                    <div
                        v-for="(item, index) in contactInfo"
                        :key="index"
                        class="text-center p-6 bg-gray-50 rounded-2xl hover:bg-primary-50 transition-colors"
                    >
                        <span class="text-3xl mb-3 block">{{ item.icon }}</span>
                        <h3 class="font-semibold text-gray-900 mb-1">{{ item.title }}</h3>
                        <p class="text-primary-600 font-medium">{{ item.info }}</p>
                        <p class="text-sm text-gray-500 mt-1">{{ item.description }}</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- Contact Form & Map -->
        <section class="py-16 bg-gray-50">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="grid lg:grid-cols-2 gap-12">
                    <!-- Form -->
                    <div class="bg-white rounded-2xl shadow-soft p-8">
                        <h2 class="text-2xl font-bold text-gray-900 mb-6">Send us a Message</h2>

                        <div v-if="submitted" class="text-center py-12">
                            <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
                                <span class="text-3xl">✅</span>
                            </div>
                            <h3 class="text-xl font-semibold text-gray-900 mb-2">Message Sent!</h3>
                            <p class="text-gray-500 mb-6">We'll get back to you within 24 hours.</p>
                            <button
                                @click="submitted = false"
                                class="text-primary-500 hover:text-primary-600 font-medium"
                            >
                                Send another message
                            </button>
                        </div>

                        <form v-else @submit.prevent="submitForm" class="space-y-6">
                            <div class="grid md:grid-cols-2 gap-6">
                                <div>
                                    <label for="name" class="block text-sm font-medium text-gray-700 mb-2">Full Name</label>
                                    <input
                                        id="name"
                                        v-model="form.name"
                                        type="text"
                                        required
                                        class="input"
                                        placeholder="Your name"
                                    />
                                </div>
                                <div>
                                    <label for="email" class="block text-sm font-medium text-gray-700 mb-2">Email</label>
                                    <input
                                        id="email"
                                        v-model="form.email"
                                        type="email"
                                        required
                                        class="input"
                                        placeholder="you@example.com"
                                    />
                                </div>
                            </div>

                            <div class="grid md:grid-cols-2 gap-6">
                                <div>
                                    <label for="phone" class="block text-sm font-medium text-gray-700 mb-2">Phone (Optional)</label>
                                    <input
                                        id="phone"
                                        v-model="form.phone"
                                        type="tel"
                                        class="input"
                                        placeholder="+1 (555) 000-0000"
                                    />
                                </div>
                                <div>
                                    <label for="subject" class="block text-sm font-medium text-gray-700 mb-2">Subject</label>
                                    <select
                                        id="subject"
                                        v-model="form.subject"
                                        required
                                        class="input"
                                    >
                                        <option value="">Select a subject</option>
                                        <option v-for="subject in subjects" :key="subject" :value="subject">
                                            {{ subject }}
                                        </option>
                                    </select>
                                </div>
                            </div>

                            <div>
                                <label for="message" class="block text-sm font-medium text-gray-700 mb-2">Message</label>
                                <textarea
                                    id="message"
                                    v-model="form.message"
                                    required
                                    rows="5"
                                    class="input resize-none"
                                    placeholder="How can we help you?"
                                ></textarea>
                            </div>

                            <button
                                type="submit"
                                :disabled="form.processing"
                                class="w-full py-4 bg-primary-500 hover:bg-primary-600 text-white font-semibold rounded-xl transition-all hover:shadow-glow disabled:opacity-50"
                            >
                                {{ form.processing ? 'Sending...' : 'Send Message' }}
                            </button>
                        </form>
                    </div>

                    <!-- Info Side -->
                    <div class="space-y-8">
                        <div class="bg-white rounded-2xl shadow-soft p-8">
                            <h3 class="text-lg font-semibold text-gray-900 mb-4">Frequently Asked Questions</h3>
                            <div class="space-y-4">
                                <div>
                                    <h4 class="font-medium text-gray-900">How do I enroll in a course?</h4>
                                    <p class="text-sm text-gray-500 mt-1">Browse our courses, select one you like, and click "Enroll Now" to get started.</p>
                                </div>
                                <div>
                                    <h4 class="font-medium text-gray-900">Can I get a refund?</h4>
                                    <p class="text-sm text-gray-500 mt-1">Yes, we offer a 7-day money-back guarantee on all plans.</p>
                                </div>
                                <div>
                                    <h4 class="font-medium text-gray-900">How do I join a class?</h4>
                                    <p class="text-sm text-gray-500 mt-1">Log in to your dashboard and click "Join Class" when it's time for your session.</p>
                                </div>
                            </div>
                            <Link href="/pricing" class="inline-flex items-center text-primary-500 hover:text-primary-600 font-medium mt-4">
                                View all FAQs →
                            </Link>
                        </div>

                        <div class="bg-gradient-to-br from-primary-500 to-primary-600 rounded-2xl p-8 text-white">
                            <h3 class="text-lg font-semibold mb-4">Want to become a teacher?</h3>
                            <p class="text-primary-100 mb-6">
                                Join our team of qualified Quran teachers and help students worldwide on their learning journey.
                            </p>
                            <Link
                                href="/teachers/apply"
                                class="inline-flex items-center px-6 py-3 bg-white text-primary-600 font-semibold rounded-xl hover:bg-primary-50 transition-colors"
                            >
                                Apply Now
                            </Link>
                        </div>

                        <div class="bg-white rounded-2xl shadow-soft p-8">
                            <h3 class="text-lg font-semibold text-gray-900 mb-4">Office Hours</h3>
                            <div class="space-y-2 text-sm">
                                <div class="flex justify-between">
                                    <span class="text-gray-500">Monday - Friday</span>
                                    <span class="font-medium text-gray-900">9:00 AM - 6:00 PM EST</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-500">Saturday</span>
                                    <span class="font-medium text-gray-900">10:00 AM - 4:00 PM EST</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-500">Sunday</span>
                                    <span class="font-medium text-gray-900">Closed</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </PublicLayout>
</template>

<style scoped>
.input {
    @apply w-full px-4 py-3 rounded-xl border-gray-200 focus:border-primary-500 focus:ring-primary-500 transition-colors;
}
</style>
