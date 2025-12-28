<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import { ref, computed, watch } from 'vue';
import PublicLayout from '@/Layouts/PublicLayout.vue';

const props = defineProps({
    course: {
        type: Object,
        required: true
    },
    batches: {
        type: Array,
        default: () => []
    },
    previewData: {
        type: Object,
        default: null
    },
    auth: Object
});

const step = ref(1);
const selectedType = ref('group');
const selectedBatch = ref(null);
const couponCode = ref('');
const couponApplied = ref(false);
const couponDiscount = ref(0);
const isProcessing = ref(false);

const form = useForm({
    course_id: props.course.id,
    type: 'group',
    batch_id: null,
    coupon_code: '',
    payment_method: 'stripe',
});

// Selected batch details
const selectedBatchDetails = computed(() => {
    if (selectedType.value === 'private' || !selectedBatch.value) return null;
    return props.batches.find(b => b.id === selectedBatch.value);
});

// Price calculation
const basePrice = computed(() => {
    return selectedType.value === 'group' 
        ? parseFloat(props.course.pricing?.group || 0)
        : parseFloat(props.course.pricing?.private || 0);
});

const discount = computed(() => couponDiscount.value);

const finalPrice = computed(() => {
    return Math.max(0, basePrice.value - discount.value);
});

// Navigate steps
const nextStep = () => {
    if (step.value === 1) {
        form.type = selectedType.value;
        if (selectedType.value === 'group' && !selectedBatch.value) {
            alert('Please select a batch');
            return;
        }
        form.batch_id = selectedBatch.value;
    }
    step.value++;
};

const prevStep = () => {
    step.value--;
};

// Apply coupon
const applyCoupon = async () => {
    if (!couponCode.value) return;
    
    isProcessing.value = true;
    try {
        const response = await fetch('/api/v1/enrollments/preview', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
            },
            body: JSON.stringify({
                course_id: props.course.id,
                type: selectedType.value,
                batch_id: selectedBatch.value,
                coupon_code: couponCode.value,
            }),
        });
        
        const data = await response.json();
        
        if (data.success && data.data.discount > 0) {
            couponApplied.value = true;
            couponDiscount.value = data.data.discount;
            form.coupon_code = couponCode.value;
        } else {
            alert(data.message || 'Invalid coupon code');
        }
    } catch (error) {
        alert('Error applying coupon');
    }
    isProcessing.value = false;
};

// Submit enrollment
const submitEnrollment = () => {
    if (!props.auth?.user) {
        window.location.href = `/login?redirect=/enroll/${props.course.slug}`;
        return;
    }
    
    isProcessing.value = true;
    form.post('/enrollments', {
        onSuccess: () => {
            step.value = 4; // Success step
        },
        onError: () => {
            isProcessing.value = false;
        },
    });
};

const formatCurrency = (amount) => {
    return new Intl.NumberFormat('en-US', {
        style: 'currency',
        currency: 'USD',
    }).format(amount);
};
</script>

<template>
    <Head :title="`Enroll in ${course.title}`" />
    
    <PublicLayout>
        <div class="min-h-screen bg-gray-50 pt-24 pb-12">
            <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
                <!-- Progress Steps -->
                <div class="mb-8">
                    <div class="flex items-center justify-center">
                        <template v-for="(stepName, index) in ['Type', 'Review', 'Payment', 'Done']" :key="index">
                            <div class="flex items-center">
                                <div :class="[
                                    'w-10 h-10 rounded-full flex items-center justify-center font-semibold transition-colors',
                                    step > index + 1 ? 'bg-primary-500 text-white' :
                                    step === index + 1 ? 'bg-primary-500 text-white' :
                                    'bg-gray-200 text-gray-500'
                                ]">
                                    <span v-if="step > index + 1">✓</span>
                                    <span v-else>{{ index + 1 }}</span>
                                </div>
                                <span :class="[
                                    'ml-2 text-sm font-medium hidden sm:block',
                                    step >= index + 1 ? 'text-gray-900' : 'text-gray-500'
                                ]">
                                    {{ stepName }}
                                </span>
                            </div>
                            <div v-if="index < 3" :class="[
                                'w-12 sm:w-24 h-1 mx-2 rounded',
                                step > index + 1 ? 'bg-primary-500' : 'bg-gray-200'
                            ]"></div>
                        </template>
                    </div>
                </div>

                <!-- Step 1: Choose Type & Batch -->
                <div v-if="step === 1" class="bg-white rounded-2xl shadow-soft p-8">
                    <h2 class="text-2xl font-bold text-gray-900 mb-2">Choose Your Learning Path</h2>
                    <p class="text-gray-500 mb-8">Select how you'd like to learn {{ course.title }}</p>

                    <!-- Class Type Selection -->
                    <div class="grid md:grid-cols-2 gap-6 mb-8">
                        <button
                            @click="selectedType = 'group'"
                            :class="[
                                'p-6 rounded-xl border-2 text-left transition-all',
                                selectedType === 'group' 
                                    ? 'border-primary-500 bg-primary-50' 
                                    : 'border-gray-200 hover:border-gray-300'
                            ]"
                        >
                            <div class="flex items-start justify-between mb-4">
                                <span class="text-3xl">👥</span>
                                <span v-if="selectedType === 'group'" class="w-6 h-6 bg-primary-500 rounded-full flex items-center justify-center text-white text-sm">✓</span>
                            </div>
                            <h3 class="text-lg font-semibold text-gray-900 mb-1">Group Class</h3>
                            <p class="text-sm text-gray-500 mb-4">Learn with other students in a scheduled batch</p>
                            <p class="text-2xl font-bold text-primary-500">{{ formatCurrency(course.pricing?.group || 0) }}<span class="text-sm text-gray-400 font-normal">/month</span></p>
                        </button>

                        <button
                            @click="selectedType = 'private'; selectedBatch = null"
                            :class="[
                                'p-6 rounded-xl border-2 text-left transition-all',
                                selectedType === 'private' 
                                    ? 'border-primary-500 bg-primary-50' 
                                    : 'border-gray-200 hover:border-gray-300'
                            ]"
                        >
                            <div class="flex items-start justify-between mb-4">
                                <span class="text-3xl">👤</span>
                                <span v-if="selectedType === 'private'" class="w-6 h-6 bg-primary-500 rounded-full flex items-center justify-center text-white text-sm">✓</span>
                            </div>
                            <h3 class="text-lg font-semibold text-gray-900 mb-1">Private 1-on-1</h3>
                            <p class="text-sm text-gray-500 mb-4">Personalized classes with flexible scheduling</p>
                            <p class="text-2xl font-bold text-primary-500">{{ formatCurrency(course.pricing?.private || 0) }}<span class="text-sm text-gray-400 font-normal">/month</span></p>
                        </button>
                    </div>

                    <!-- Batch Selection (for Group) -->
                    <div v-if="selectedType === 'group'" class="mb-8">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Select a Batch</h3>
                        
                        <div v-if="batches.length" class="space-y-3">
                            <label
                                v-for="batch in batches"
                                :key="batch.id"
                                :class="[
                                    'flex items-center p-4 rounded-xl border-2 cursor-pointer transition-all',
                                    selectedBatch === batch.id
                                        ? 'border-primary-500 bg-primary-50'
                                        : 'border-gray-200 hover:border-gray-300'
                                ]"
                            >
                                <input
                                    type="radio"
                                    v-model="selectedBatch"
                                    :value="batch.id"
                                    class="sr-only"
                                />
                                <div class="flex-1">
                                    <div class="flex items-center justify-between">
                                        <h4 class="font-medium text-gray-900">{{ batch.name }}</h4>
                                        <span :class="[
                                            'px-3 py-1 text-xs font-medium rounded-full',
                                            batch.available_slots > 5 ? 'bg-green-100 text-green-700' : 'bg-orange-100 text-orange-700'
                                        ]">
                                            {{ batch.available_slots }} spots left
                                        </span>
                                    </div>
                                    <div class="flex items-center mt-2 text-sm text-gray-500">
                                        <span>📅 Starts {{ batch.start_date }}</span>
                                        <span class="mx-2">•</span>
                                        <span>🕐 {{ batch.formatted_schedule }}</span>
                                    </div>
                                    <p v-if="batch.teacher" class="text-sm text-gray-500 mt-1">
                                        👨‍🏫 {{ batch.teacher.name }}
                                    </p>
                                </div>
                                <div v-if="selectedBatch === batch.id" class="ml-4 w-6 h-6 bg-primary-500 rounded-full flex items-center justify-center text-white text-sm">✓</div>
                            </label>
                        </div>

                        <div v-else class="text-center py-8 text-gray-500">
                            <p>No batches available at the moment.</p>
                            <p class="text-sm">Consider a private class instead.</p>
                        </div>
                    </div>

                    <!-- Private Class Info -->
                    <div v-if="selectedType === 'private'" class="mb-8 p-6 bg-blue-50 rounded-xl">
                        <h4 class="font-medium text-blue-900 mb-2">📋 Private Class Benefits</h4>
                        <ul class="text-sm text-blue-700 space-y-1">
                            <li>✓ Flexible scheduling that fits your timezone</li>
                            <li>✓ Personalized pace and curriculum</li>
                            <li>✓ Full attention from your teacher</li>
                            <li>✓ Schedule changes allowed with notice</li>
                        </ul>
                    </div>

                    <div class="flex justify-between">
                        <Link :href="`/courses/${course.slug}`" class="px-6 py-3 text-gray-600 hover:text-gray-900">
                            ← Back to Course
                        </Link>
                        <button
                            @click="nextStep"
                            :disabled="selectedType === 'group' && !selectedBatch"
                            :class="[
                                'px-8 py-3 rounded-xl font-semibold transition-all',
                                selectedType === 'group' && !selectedBatch
                                    ? 'bg-gray-200 text-gray-400 cursor-not-allowed'
                                    : 'bg-primary-500 hover:bg-primary-600 text-white hover:shadow-glow'
                            ]"
                        >
                            Continue →
                        </button>
                    </div>
                </div>

                <!-- Step 2: Review & Coupon -->
                <div v-if="step === 2" class="bg-white rounded-2xl shadow-soft p-8">
                    <h2 class="text-2xl font-bold text-gray-900 mb-6">Review Your Order</h2>

                    <!-- Order Summary -->
                    <div class="border border-gray-200 rounded-xl p-6 mb-6">
                        <div class="flex items-start justify-between mb-4">
                            <div>
                                <h3 class="font-semibold text-gray-900">{{ course.title }}</h3>
                                <p class="text-sm text-gray-500">{{ selectedType === 'group' ? 'Group Class' : 'Private 1-on-1' }}</p>
                            </div>
                            <span class="text-2xl">📖</span>
                        </div>

                        <div v-if="selectedBatchDetails" class="text-sm text-gray-500 mb-4 pl-4 border-l-2 border-gray-200">
                            <p><strong>Batch:</strong> {{ selectedBatchDetails.name }}</p>
                            <p><strong>Schedule:</strong> {{ selectedBatchDetails.formatted_schedule }}</p>
                            <p><strong>Starts:</strong> {{ selectedBatchDetails.start_date }}</p>
                        </div>

                        <div class="border-t border-gray-100 pt-4 space-y-2">
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-500">Monthly Fee</span>
                                <span class="text-gray-900">{{ formatCurrency(basePrice) }}</span>
                            </div>
                            <div v-if="discount > 0" class="flex justify-between text-sm text-green-600">
                                <span>Coupon Discount</span>
                                <span>-{{ formatCurrency(discount) }}</span>
                            </div>
                            <div class="flex justify-between text-lg font-bold pt-2 border-t border-gray-100">
                                <span>Total</span>
                                <span class="text-primary-500">{{ formatCurrency(finalPrice) }}/month</span>
                            </div>
                        </div>
                    </div>

                    <!-- Coupon Code -->
                    <div class="mb-8">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Have a coupon code?</label>
                        <div class="flex gap-3">
                            <input
                                v-model="couponCode"
                                type="text"
                                placeholder="Enter coupon code"
                                :disabled="couponApplied"
                                class="flex-1 px-4 py-3 rounded-xl border-gray-200 focus:border-primary-500 focus:ring-primary-500"
                            />
                            <button
                                @click="applyCoupon"
                                :disabled="couponApplied || isProcessing"
                                :class="[
                                    'px-6 py-3 rounded-xl font-semibold transition-colors',
                                    couponApplied
                                        ? 'bg-green-100 text-green-700'
                                        : 'bg-gray-100 hover:bg-gray-200 text-gray-700'
                                ]"
                            >
                                {{ couponApplied ? 'Applied ✓' : 'Apply' }}
                            </button>
                        </div>
                    </div>

                    <div class="flex justify-between">
                        <button @click="prevStep" class="px-6 py-3 text-gray-600 hover:text-gray-900">
                            ← Back
                        </button>
                        <button
                            @click="nextStep"
                            class="px-8 py-3 bg-primary-500 hover:bg-primary-600 text-white rounded-xl font-semibold transition-all hover:shadow-glow"
                        >
                            Continue to Payment →
                        </button>
                    </div>
                </div>

                <!-- Step 3: Payment -->
                <div v-if="step === 3" class="bg-white rounded-2xl shadow-soft p-8">
                    <h2 class="text-2xl font-bold text-gray-900 mb-6">Payment Details</h2>

                    <!-- Payment Method Selection -->
                    <div class="space-y-4 mb-8">
                        <label class="flex items-center p-4 rounded-xl border-2 cursor-pointer transition-all border-primary-500 bg-primary-50">
                            <input type="radio" v-model="form.payment_method" value="stripe" class="sr-only" />
                            <div class="w-12 h-8 bg-white rounded flex items-center justify-center mr-4 border">
                                <span class="text-blue-600 font-bold text-sm">💳</span>
                            </div>
                            <div class="flex-1">
                                <h4 class="font-medium text-gray-900">Credit/Debit Card</h4>
                                <p class="text-sm text-gray-500">Visa, Mastercard, American Express</p>
                            </div>
                            <div class="w-6 h-6 bg-primary-500 rounded-full flex items-center justify-center text-white text-sm">✓</div>
                        </label>
                    </div>

                    <!-- Order Summary -->
                    <div class="bg-gray-50 rounded-xl p-6 mb-8">
                        <h4 class="font-medium text-gray-900 mb-4">Order Summary</h4>
                        <div class="space-y-2 text-sm">
                            <div class="flex justify-between">
                                <span class="text-gray-500">{{ course.title }}</span>
                                <span>{{ formatCurrency(basePrice) }}</span>
                            </div>
                            <div v-if="discount > 0" class="flex justify-between text-green-600">
                                <span>Discount</span>
                                <span>-{{ formatCurrency(discount) }}</span>
                            </div>
                            <div class="flex justify-between font-bold text-lg pt-2 border-t border-gray-200">
                                <span>Total Due Today</span>
                                <span class="text-primary-500">{{ formatCurrency(finalPrice) }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- Terms -->
                    <p class="text-sm text-gray-500 mb-6">
                        By clicking "Complete Enrollment", you agree to our 
                        <Link href="/terms" class="text-primary-500 hover:underline">Terms of Service</Link> and 
                        <Link href="/privacy" class="text-primary-500 hover:underline">Privacy Policy</Link>. 
                        You'll be charged {{ formatCurrency(finalPrice) }} monthly until you cancel.
                    </p>

                    <div class="flex justify-between">
                        <button @click="prevStep" class="px-6 py-3 text-gray-600 hover:text-gray-900">
                            ← Back
                        </button>
                        <button
                            @click="submitEnrollment"
                            :disabled="isProcessing"
                            class="px-8 py-4 bg-primary-500 hover:bg-primary-600 text-white rounded-xl font-semibold transition-all hover:shadow-glow disabled:opacity-50"
                        >
                            <span v-if="isProcessing">Processing...</span>
                            <span v-else>Complete Enrollment - {{ formatCurrency(finalPrice) }}</span>
                        </button>
                    </div>
                </div>

                <!-- Step 4: Success -->
                <div v-if="step === 4" class="bg-white rounded-2xl shadow-soft p-8 text-center">
                    <div class="w-20 h-20 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-6">
                        <span class="text-4xl">🎉</span>
                    </div>
                    <h2 class="text-2xl font-bold text-gray-900 mb-2">Enrollment Successful!</h2>
                    <p class="text-gray-500 mb-8">
                        You're now enrolled in {{ course.title }}. Welcome to your learning journey!
                    </p>

                    <div class="bg-gray-50 rounded-xl p-6 mb-8 text-left">
                        <h4 class="font-medium text-gray-900 mb-4">What's Next?</h4>
                        <ul class="space-y-3 text-sm text-gray-600">
                            <li class="flex items-start">
                                <span class="text-primary-500 mr-2">1.</span>
                                Check your email for confirmation and class details
                            </li>
                            <li class="flex items-start">
                                <span class="text-primary-500 mr-2">2.</span>
                                Visit your dashboard to see upcoming classes
                            </li>
                            <li class="flex items-start">
                                <span class="text-primary-500 mr-2">3.</span>
                                Download any resources before your first class
                            </li>
                        </ul>
                    </div>

                    <div class="flex justify-center gap-4">
                        <Link href="/dashboard" class="px-8 py-3 bg-primary-500 hover:bg-primary-600 text-white rounded-xl font-semibold">
                            Go to Dashboard
                        </Link>
                        <Link href="/courses" class="px-8 py-3 border border-gray-200 hover:border-gray-300 text-gray-700 rounded-xl font-semibold">
                            Browse More Courses
                        </Link>
                    </div>
                </div>
            </div>
        </div>
    </PublicLayout>
</template>
