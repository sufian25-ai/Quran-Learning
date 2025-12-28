<script setup>
import { Head, Link } from '@inertiajs/vue3';
import { ref } from 'vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';

const props = defineProps({
    auth: Object,
    certificate: {
        type: Object,
        required: true
    }
});

const downloading = ref(false);

const downloadCertificate = async () => {
    downloading.value = true;
    // In production, this would generate a PDF
    window.open(`/certificates/${props.certificate.id}/download`, '_blank');
    setTimeout(() => downloading.value = false, 2000);
};

const shareCertificate = () => {
    if (navigator.share) {
        navigator.share({
            title: `Certificate of Completion - ${props.certificate.course}`,
            text: `I completed ${props.certificate.course} at QuranLearn!`,
            url: window.location.href,
        });
    } else {
        navigator.clipboard.writeText(window.location.href);
        alert('Link copied to clipboard!');
    }
};
</script>

<template>
    <Head :title="`Certificate - ${certificate.course}`" />

    <AuthenticatedLayout>
        <div class="py-12">
            <div class="max-w-4xl mx-auto px-4">
                <!-- Action Buttons -->
                <div class="flex justify-center gap-4 mb-8">
                    <button
                        @click="downloadCertificate"
                        :disabled="downloading"
                        class="px-6 py-3 bg-primary-500 hover:bg-primary-600 text-white font-medium rounded-xl transition-all disabled:opacity-50 flex items-center"
                    >
                        <svg v-if="!downloading" class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                        </svg>
                        <svg v-else class="w-5 h-5 mr-2 animate-spin" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" fill="none"/>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                        </svg>
                        {{ downloading ? 'Generating...' : 'Download PDF' }}
                    </button>
                    <button
                        @click="shareCertificate"
                        class="px-6 py-3 bg-gray-100 hover:bg-gray-200 text-gray-700 font-medium rounded-xl transition-all flex items-center"
                    >
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.368 2.684 3 3 0 00-5.368-2.684z"/>
                        </svg>
                        Share
                    </button>
                </div>

                <!-- Certificate Preview -->
                <div class="bg-white rounded-2xl shadow-2xl overflow-hidden">
                    <!-- Certificate Design -->
                    <div class="relative bg-gradient-to-br from-primary-50 via-white to-gold-50 p-12 min-h-[600px] flex flex-col items-center justify-center border-8 border-double border-primary-100">
                        <!-- Decorative Border -->
                        <div class="absolute inset-4 border-2 border-primary-200 rounded-xl pointer-events-none"></div>
                        
                        <!-- Corner Decorations -->
                        <div class="absolute top-8 left-8 w-12 h-12 text-3xl">☪️</div>
                        <div class="absolute top-8 right-8 w-12 h-12 text-3xl">📖</div>
                        <div class="absolute bottom-8 left-8 w-12 h-12 text-3xl">🌙</div>
                        <div class="absolute bottom-8 right-8 w-12 h-12 text-3xl">⭐</div>

                        <!-- Content -->
                        <div class="text-center relative z-10">
                            <!-- Logo -->
                            <div class="flex justify-center mb-6">
                                <div class="w-20 h-20 bg-gradient-to-br from-primary-500 to-primary-600 rounded-2xl flex items-center justify-center shadow-lg">
                                    <span class="text-4xl">📖</span>
                                </div>
                            </div>

                            <h2 class="text-primary-600 font-display text-xl mb-2">QURANLEARN ACADEMY</h2>
                            
                            <h1 class="text-4xl font-display font-bold text-gray-900 mb-4 mt-8">
                                Certificate of Completion
                            </h1>

                            <p class="text-gray-600 mb-8">This is to certify that</p>

                            <p class="text-3xl font-display font-bold text-primary-600 mb-8 border-b-2 border-primary-200 pb-4 px-8 inline-block">
                                {{ certificate.student_name }}
                            </p>

                            <p class="text-gray-600 mb-4">has successfully completed the course</p>

                            <p class="text-2xl font-display font-semibold text-gray-900 mb-8">
                                "{{ certificate.course }}"
                            </p>

                            <p class="text-gray-500 mb-8">
                                Completed on {{ certificate.completed_date }}
                            </p>

                            <!-- Signatures -->
                            <div class="flex justify-center gap-20 mt-12">
                                <div class="text-center">
                                    <div class="w-32 border-t-2 border-gray-300 pt-2">
                                        <p class="font-medium text-gray-700">{{ certificate.teacher_name }}</p>
                                        <p class="text-sm text-gray-500">Course Instructor</p>
                                    </div>
                                </div>
                                <div class="text-center">
                                    <div class="w-32 border-t-2 border-gray-300 pt-2">
                                        <p class="font-medium text-gray-700">QuranLearn</p>
                                        <p class="text-sm text-gray-500">Academy Director</p>
                                    </div>
                                </div>
                            </div>

                            <!-- Certificate ID -->
                            <p class="text-xs text-gray-400 mt-12">
                                Certificate ID: {{ certificate.certificate_id }}
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Course Summary -->
                <div class="bg-white rounded-xl shadow-sm p-6 mt-6">
                    <h3 class="font-semibold text-gray-900 mb-4">Course Summary</h3>
                    <div class="grid grid-cols-3 gap-4">
                        <div class="text-center p-4 bg-gray-50 rounded-lg">
                            <p class="text-2xl font-bold text-primary-600">{{ certificate.classes_attended }}</p>
                            <p class="text-sm text-gray-500">Classes Attended</p>
                        </div>
                        <div class="text-center p-4 bg-gray-50 rounded-lg">
                            <p class="text-2xl font-bold text-primary-600">{{ certificate.duration_weeks }}</p>
                            <p class="text-sm text-gray-500">Weeks</p>
                        </div>
                        <div class="text-center p-4 bg-gray-50 rounded-lg">
                            <p class="text-2xl font-bold text-primary-600">{{ certificate.xp_earned }}</p>
                            <p class="text-sm text-gray-500">XP Earned</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
