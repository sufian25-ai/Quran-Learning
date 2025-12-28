<script setup>
import { Head, router } from '@inertiajs/vue3';
import { ref } from 'vue';
import PublicLayout from '@/Layouts/PublicLayout.vue';

const props = defineProps({
    certificate: {
        type: Object,
        default: null
    },
    searchCode: {
        type: String,
        default: ''
    }
});

const verificationCode = ref(props.searchCode || '');
const isSearching = ref(false);

const verifyCertificate = () => {
    if (!verificationCode.value.trim()) return;
    
    isSearching.value = true;
    router.get(`/certificates/verify/${verificationCode.value.trim()}`);
};
</script>

<template>
    <PublicLayout>
        <Head title="Verify Certificate" />
        
        <div class="py-24 bg-gradient-to-br from-emerald-50 to-teal-50 min-h-screen">
            <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
                <!-- Header -->
                <div class="text-center mb-12">
                    <div class="inline-flex items-center justify-center w-20 h-20 bg-emerald-600 rounded-full mb-4">
                        <svg class="w-10 h-10 text-white" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M9 2a2 2 0 00-2 2v8a2 2 0 002 2h6a2 2 0 002-2V6.414A2 2 0 0016.414 5L14 2.586A2 2 0 0012.586 2H9z"/>
                            <path d="M3 8a2 2 0 012-2v10h8a2 2 0 01-2 2H5a2 2 0 01-2-2V8z"/>
                        </svg>
                    </div>
                    <h1 class="text-4xl font-bold text-gray-900 mb-3">Certificate Verification</h1>
                    <p class="text-lg text-gray-600">Verify the authenticity of QuranLearn certificates</p>
                </div>
                
                <!-- Search Form -->
                <div class="bg-white rounded-2xl shadow-xl p-8 mb-8">
                    <label for="verification-code" class="block text-sm font-semibold text-gray-700 mb-3">
                        Enter Verification Code
                    </label>
                    <div class="flex gap-3">
                        <input 
                            id="verification-code"
                            v-model="verificationCode"
                            type="text" 
                            placeholder="XXXX-XXXX-XXXX"
                            class="flex-1 px-4 py-4 text-lg border-2 border-gray-300 rounded-xl focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 transition-all"
                            @keyup.enter="verifyCertificate"
                        />
                        <button 
                            @click="verifyCertificate" 
                            :disabled="!verificationCode.trim() || isSearching"
                            class="px-8 py-4 bg-emerald-600 text-white font-semibold rounded-xl hover:bg-emerald-700 disabled:opacity-50 disabled:cursor-not-allowed transition-all shadow-lg hover:shadow-xl"
                        >
                            <span v-if="!isSearching">Verify</span>
                            <span v-else>Searching...</span>
                        </button>
                    </div>
                    <p class="mt-3 text-sm text-gray-500">
                        The verification code can be found on the certificate. Example: ABCD-1234-EFGH
                    </p>
                </div>
                
                <!-- Results - Valid Certificate -->
                <div v-if="certificate" class="bg-emerald-50 border-4 border-emerald-200 rounded-2xl p-8 shadow-lg">
                    <div class="flex items-center mb-6">
                        <div class="flex-shrink-0">
                            <div class="w-16 h-16 bg-emerald-600 rounded-full flex items-center justify-center">
                                <svg class="w-8 h-8 text-white" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                </svg>
                            </div>
                        </div>
                        <div class="ml-4">
                            <h2 class="text-3xl font-bold text-emerald-900">✓ Certificate Verified</h2>
                            <p class="text-emerald-700">This is an authentic QuranLearn certificate</p>
                        </div>
                    </div>
                    
                    <div class="bg-white rounded-xl p-6 space-y-4">
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <dt class="text-sm font-semibold text-gray-500 uppercase tracking-wide">Student Name</dt>
                                <dd class="mt-1 text-xl font-bold text-gray-900">{{ certificate.student_name }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-semibold text-gray-500 uppercase tracking-wide">Certificate Number</dt>
                                <dd class="mt-1 text-lg font-mono text-gray-700">{{ certificate.certificate_number }}</dd>
                            </div>
                        </div>
                        
                        <div class="border-t pt-4">
                            <dt class="text-sm font-semibold text-gray-500 uppercase tracking-wide">Course Completed</dt>
                            <dd class="mt-1 text-lg font-semibold text-gray-900">{{ certificate.course_title }}</dd>
                        </div>
                        
                        <div class="grid grid-cols-2 gap-4 border-t pt-4">
                            <div>
                                <dt class="text-sm font-semibold text-gray-500 uppercase tracking-wide">Completion Date</dt>
                                <dd class="mt-1 text-gray-900">
                                    {{ new Date(certificate.course_completed_at).toLocaleDateString('en-US', { month: 'long', day: 'numeric', year: 'numeric' }) }}
                                </dd>
                            </div>
                            <div>
                                <dt class="text-sm font-semibold text-gray-500 uppercase tracking-wide">Issue Date</dt>
                                <dd class="mt-1 text-gray-900">
                                    {{ new Date(certificate.created_at).toLocaleDateString('en-US', { month: 'long', day: 'numeric', year: 'numeric' }) }}
                                </dd>
                            </div>
                        </div>
                        
                        <div v-if="certificate.completion_percentage" class="border-t pt-4">
                            <dt class="text-sm font-semibold text-gray-500 uppercase tracking-wide mb-2">Completion Rate</dt>
                            <div class="flex items-center">
                                <div class="flex-1 bg-gray-200 rounded-full h-3 mr-3">
                                    <div class="bg-emerald-600 h-3 rounded-full transition-all" 
                                         :style="{ width: certificate.completion_percentage + '%' }"></div>
                                </div>
                                <span class="text-lg font-bold text-emerald-600">
                                    {{ Math.round(certificate.completion_percentage) }}%
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Results - Invalid Certificate -->
                <div v-else-if="searchCode && !certificate" class="bg-red-50 border-4 border-red-200 rounded-2xl p-8 text-center shadow-lg">
                    <div class="inline-flex items-center justify-center w-16 h-16 bg-red-600 rounded-full mb-4">
                        <svg class="w-8 h-8 text-white" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                        </svg>
                    </div>
                    <h3 class="text-2xl font-bold text-red-900 mb-2">Certificate Not Found</h3>
                    <p class="text-red-700 mb-4">
                        The verification code you entered is invalid or the certificate does not exist in our records.
                    </p>
                    <p class="text-sm text-red-600">
                        Please check the code and try again. If you believe this is an error, contact support.
                    </p>
                </div>
                
                <!-- How to Verify Section -->
                <div class="mt-12 bg-white rounded-xl shadow-lg p-6">
                    <h3 class="text-lg font-bold text-gray-900 mb-4">How to Verify a Certificate</h3>
                    <ol class="space-y-3 text-gray-700">
                        <li class="flex items-start">
                            <span class="flex-shrink-0 w-6 h-6 bg-emerald-600 text-white rounded-full flex items-center justify-center text-sm font-bold mr-3">1</span>
                            <span>Locate the <strong>verification code</strong> on the certificate (format: XXXX-XXXX-XXXX)</span>
                        </li>
                        <li class="flex items-start">
                            <span class="flex-shrink-0 w-6 h-6 bg-emerald-600 text-white rounded-full flex items-center justify-center text-sm font-bold mr-3">2</span>
                            <span>Enter the code in the field above</span>
                        </li>
                        <li class="flex items-start">
                            <span class="flex-shrink-0 w-6 h-6 bg-emerald-600 text-white rounded-full flex items-center justify-center text-sm font-bold mr-3">3</span>
                            <span>Click "Verify" to check authenticity</span>
                        </li>
                        <li class="flex items-start">
                            <span class="flex-shrink-0 w-6 h-6 bg-emerald-600 text-white rounded-full flex items-center justify-center text-sm font-bold mr-3">4</span>
                            <span>View the certificate details if valid</span>
                        </li>
                    </ol>
                </div>
            </div>
        </div>
    </PublicLayout>
</template>
