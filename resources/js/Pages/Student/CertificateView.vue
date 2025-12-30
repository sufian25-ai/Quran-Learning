<script setup>
import { Head, Link } from '@inertiajs/vue3';
import StudentLayout from '@/Layouts/StudentLayout.vue';

const props = defineProps({
    certificate: Object
});
</script>

<template>
    <StudentLayout>
        <Head :title="`Certificate - ${certificate.certificate_number}`" />
        
        <div class="py-6 max-w-5xl mx-auto">
            <!-- Header -->
            <div class="flex justify-between items-center mb-6">
                <h1 class="text-3xl font-bold text-gray-900">📜 My Certificate</h1>
                <Link href="/student/certificates" class="px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700">
                    ← Back to All Certificates
                </Link>
            </div>
            
            <!-- Certificate Display -->
            <div id="certificate-container" class="bg-white rounded-lg shadow-lg overflow-hidden mb-6">
                <!-- Certificate Content -->
                <div class="bg-gradient-to-br from-emerald-50 to-teal-50 p-12 text-center border-8 border-emerald-600">
                    <!-- Logo -->
                    <div class="text-6xl font-bold text-emerald-600 mb-4">📖 QuranLearn</div>
                    <div class="text-sm text-gray-600 mb-8 tracking-widest">ISLAMIC LEARNING ACADEMY</div>
                    
                    <!-- Title -->
                    <div class="text-5xl font-bold text-gray-900 mb-4">CERTIFICATE</div>
                    <div class="text-2xl text-gray-600 mb-8">of Achievement</div>
                    
                    <!-- Student Name -->
                    <div class="text-lg text-gray-600 italic mb-2">This is to certify that</div>
                    <div class="text-4xl font-bold text-gray-900 mb-8 border-b-2 border-emerald-600 inline-block px-8 pb-2">
                        {{ certificate.student_name }}
                    </div>
                    
                    <!-- Course -->
                    <div class="text-lg text-gray-700 mb-4">has successfully completed the course</div>
                    <div class="text-3xl font-bold text-emerald-600 mb-6">{{ certificate.course_title }}</div>
                    
                    <!-- Stats -->
                    <div class="text-lg text-gray-700 mb-8">
                        with a completion rate of <strong>{{ certificate.completion_percentage }}%</strong>
                        <span v-if="certificate.grade"> and achieved a grade of <strong>{{ certificate.grade }}%</strong></span>
                    </div>
                    
                    <!-- Signatures -->
                    <div class="flex justify-around max-w-3xl mx-auto mt-12">
                        <div class="text-center">
                            <div class="border-t-2 border-gray-900 pt-2 mb-2 px-12"></div>
                            <div class="font-semibold">{{ certificate.instructor_name }}</div>
                            <div class="text-sm text-gray-600">Course Instructor</div>
                        </div>
                        <div class="text-center">
                            <div class="border-t-2 border-gray-900 pt-2 mb-2 px-12"></div>
                            <div class="font-semibold">{{ certificate.issued_by }}</div>
                            <div class="text-sm text-gray-600">QuranLearn Director</div>
                            <div class="text-xs text-gray-500 mt-1">{{ certificate.course_completed_at }}</div>
                        </div>
                    </div>
                </div>
                
                <!-- Certificate Info Bar -->
                <div class="bg-emerald-600 text-white p-4 flex justify-between items-center print:hidden">
                    <div>
                        <div class="text-xs opacity-75">Certificate Number</div>
                        <div class="font-mono font-bold">{{ certificate.certificate_number }}</div>
                    </div>
                    <div>
                        <div class="text-xs opacity-75">Verification Code</div>
                        <div class="font-mono font-bold">{{ certificate.verification_code }}</div>
                    </div>
                    <div>
                        <div class="text-xs opacity-75">Issue Date</div>
                        <div class="font-bold">{{ certificate.created_at }}</div>
                    </div>
                </div>
            </div>
            
            <!-- Action Buttons -->
            <div class="flex gap-4 justify-center print:hidden">
                <button 
                    @click="printCertificate" 
                    class="px-8 py-4 bg-emerald-600 text-white rounded-lg hover:bg-emerald-700 font-semibold flex items-center gap-2"
                >
                    <span>🖨️</span> Print Certificate
                </button>
                <a 
                    :href="`/certificates/verify/${certificate.verification_code}`" 
                    target="_blank"
                    class="px-8 py-4 bg-blue-600 text-white rounded-lg hover:bg-blue-700 font-semibold flex items-center gap-2"
                >
                    <span>✓</span> Verify Online
                </a>
            </div>
            
            <!-- Verification Instructions -->
            <div class="mt-8 bg-blue-50 border border-blue-200 rounded-lg p-6 print:hidden">
                <h3 class="font-bold text-blue-900 mb-2">📌 How to Verify This Certificate:</h3>
                <ol class="list-decimal list-inside text-blue-800 space-y-1">
                    <li>Visit: <strong>quranlearn.com/certificates/verify</strong></li>
                    <li>Enter verification code: <strong>{{ certificate.verification_code }}</strong></li>
                    <li>Or scan QR code (if applicable)</li>
                </ol>
            </div>
        </div>
    </StudentLayout>
</template>

<script>
export default {
    methods: {
        printCertificate() {
            window.print();
        }
    }
}
</script>

<style>
@media print {
    /* Globally hide layout elements from StudentLayout */
    aside, header, nav, footer {
        display: none !important;
    }

    /* Hide specific layout wrappers if needed */
    .min-h-screen, .lg\:pl-64 {
        margin: 0 !important;
        padding: 0 !important;
        background: white !important;
    }

    /* Hide everything in body by default */
    body > * {
        visibility: hidden;
    }

    /* Make the page wrapper visible (so we can see specific children) */
    /* Inertia app root */
    #app {
        visibility: visible;
    }

    /* Ensure the certificate container and its children are visible */
    #certificate-container, 
    #certificate-container * {
        visibility: visible;
    }

    /* Force the certificate to take over the screen */
    #certificate-container {
        position: fixed;
        left: 0;
        top: 0;
        width: 100%;
        height: 100%;
        margin: 0;
        padding: 0;
        z-index: 99999;
        background: white;
        
        /* Flex center for perfect paper alignment */
        display: flex !important;
        flex-direction: column;
        justify-content: center;
        align-items: center;
    }

    /* Hide elements marked with print:hidden explicitly */
    .print\:hidden {
        display: none !important;
    }

    /* Force background graphics */
    * {
        -webkit-print-color-adjust: exact !important;
        print-color-adjust: exact !important;
    }
}

@page {
    size: landscape;
    margin: 0;
}
</style>
