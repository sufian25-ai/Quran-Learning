<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';

const props = defineProps({
    enrollment: {
        type: Object,
        required: true
    }
});

const form = useForm({});

const processPayment = () => {
    form.post(`/checkout/${props.enrollment.id}/pay`);
};

const formatPrice = (amount) => {
    return new Intl.NumberFormat('en-US', {
        style: 'currency',
        currency: 'USD',
    }).format(amount || 0);
};
</script>

<template>
    <Head title="Checkout" />

    <AuthenticatedLayout>
        <div class="min-h-[80vh] flex items-center justify-center py-12">
            <div class="max-w-lg w-full mx-auto px-4">
                <div class="bg-white rounded-2xl shadow-soft p-8">
                    <!-- Header -->
                    <div class="text-center mb-8">
                        <span class="text-5xl mb-4 block">💳</span>
                        <h1 class="text-2xl font-bold text-gray-900">Complete Your Enrollment</h1>
                        <p class="text-gray-500 mt-2">One step away from starting your journey</p>
                    </div>

                    <!-- Order Summary -->
                    <div class="bg-gray-50 rounded-xl p-6 mb-8">
                        <h2 class="font-semibold text-gray-900 mb-4">Order Summary</h2>
                        
                        <div class="space-y-3 text-sm">
                            <div class="flex justify-between">
                                <span class="text-gray-500">Course</span>
                                <span class="font-medium text-gray-900">{{ enrollment.course?.title }}</span>
                            </div>
                            <div v-if="enrollment.batch" class="flex justify-between">
                                <span class="text-gray-500">Batch</span>
                                <span class="font-medium text-gray-900">{{ enrollment.batch?.name }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-500">Type</span>
                                <span class="font-medium text-gray-900 capitalize">{{ enrollment.type }} Class</span>
                            </div>
                            <div class="border-t border-gray-200 pt-3 mt-3">
                                <div class="flex justify-between text-lg">
                                    <span class="font-semibold text-gray-900">Total</span>
                                    <span class="font-bold text-primary-600">{{ formatPrice(enrollment.amount) }}</span>
                                </div>
                                <p class="text-xs text-gray-400 mt-1">Per month</p>
                            </div>
                        </div>
                    </div>

                    <!-- Payment Method -->
                    <div class="mb-8">
                        <h2 class="font-semibold text-gray-900 mb-4">Payment Method</h2>
                        <div class="space-y-3">
                            <label class="flex items-center p-4 border-2 border-primary-500 rounded-xl cursor-pointer bg-primary-50">
                                <input type="radio" name="payment" value="stripe" checked class="text-primary-500 focus:ring-primary-500" />
                                <span class="ml-3 text-xl">💳</span>
                                <span class="ml-2 font-medium text-gray-900">Credit Card (Demo)</span>
                            </label>
                        </div>
                        <p class="text-xs text-gray-400 mt-3 text-center">
                            This is a demo payment. No actual charges will be made.
                        </p>
                    </div>

                    <!-- Submit Button -->
                    <button
                        @click="processPayment"
                        :disabled="form.processing"
                        class="w-full py-4 bg-primary-500 hover:bg-primary-600 text-white text-lg font-semibold rounded-xl transition-all disabled:opacity-50 disabled:cursor-not-allowed"
                    >
                        <span v-if="form.processing" class="flex items-center justify-center">
                            <svg class="animate-spin h-5 w-5 mr-3" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" fill="none"/>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"/>
                            </svg>
                            Processing...
                        </span>
                        <span v-else>Pay {{ formatPrice(enrollment.amount) }}</span>
                    </button>

                    <!-- Back Link -->
                    <div class="text-center mt-6">
                        <Link :href="`/courses/${enrollment.course?.slug}`" class="text-gray-500 hover:text-gray-700 text-sm">
                            ← Cancel and go back
                        </Link>
                    </div>

                    <!-- Security Note -->
                    <div class="flex items-center justify-center mt-6 text-xs text-gray-400">
                        <span class="mr-2">🔒</span>
                        Secured by Stripe. Your payment info is encrypted.
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
