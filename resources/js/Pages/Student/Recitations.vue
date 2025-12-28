<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import { ref, computed } from 'vue';
import StudentLayout from '@/Layouts/StudentLayout.vue';

const props = defineProps({
    auth: Object,
    submissions: {
        type: Array,
        default: () => []
    },
    surahs: {
        type: Array,
        default: () => []
    }
});

const showModal = ref(false);
const isRecording = ref(false);
const audioBlob = ref(null);
const audioUrl = ref(null);
const mediaRecorder = ref(null);

const form = useForm({
    surah_id: '',
    ayah_from: 1,
    ayah_to: 1,
    audio: null,
    notes: '',
});

const selectedSurah = computed(() => {
    return props.surahs.find(s => s.id == form.surah_id);
});

const startRecording = async () => {
    try {
        const stream = await navigator.mediaDevices.getUserMedia({ audio: true });
        mediaRecorder.value = new MediaRecorder(stream);
        const chunks = [];

        mediaRecorder.value.ondataavailable = (e) => chunks.push(e.data);
        mediaRecorder.value.onstop = () => {
            audioBlob.value = new Blob(chunks, { type: 'audio/webm' });
            audioUrl.value = URL.createObjectURL(audioBlob.value);
            form.audio = new File([audioBlob.value], 'recitation.webm', { type: 'audio/webm' });
        };

        mediaRecorder.value.start();
        isRecording.value = true;
    } catch (error) {
        alert('Could not access microphone. Please allow microphone access.');
    }
};

const stopRecording = () => {
    if (mediaRecorder.value) {
        mediaRecorder.value.stop();
        mediaRecorder.value.stream.getTracks().forEach(track => track.stop());
        isRecording.value = false;
    }
};

const submitRecitation = () => {
    form.post('/recitations', {
        onSuccess: () => {
            showModal.value = false;
            form.reset();
            audioBlob.value = null;
            audioUrl.value = null;
        },
    });
};

const getStatusColor = (status) => {
    const colors = {
        'pending': 'bg-amber-100 text-amber-700',
        'in_review': 'bg-blue-100 text-blue-700',
        'reviewed': 'bg-green-100 text-green-700',
        'approved': 'bg-emerald-100 text-emerald-700',
        'needs_improvement': 'bg-red-100 text-red-700',
    };
    return colors[status] || 'bg-gray-100 text-gray-700';
};

const formatDate = (dateString) => {
    return new Date(dateString).toLocaleDateString('en-US', {
        month: 'short', day: 'numeric', year: 'numeric'
    });
};
</script>

<template>
    <Head title="My Recitations | Quran Learning" />

    <StudentLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="font-display text-xl font-bold text-gray-900">🎤 My Recitations</h2>
                    <p class="text-gray-500 text-sm">Submit and track your recitation progress</p>
                </div>
                <button
                    @click="showModal = true"
                    class="px-4 py-2 bg-emerald-500 hover:bg-emerald-600 text-white font-medium rounded-xl transition-colors"
                >
                    + New Recitation
                </button>
            </div>
        </template>

        <div class="space-y-6">
            <!-- Submissions List -->
            <div v-if="submissions.length" class="space-y-4">
                <div
                    v-for="submission in submissions"
                    :key="submission.id"
                    class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6"
                >
                    <div class="flex items-start justify-between mb-4">
                        <div>
                            <h3 class="font-semibold text-gray-900">
                                {{ submission.surah?.name }} - Ayah {{ submission.ayah_range }}
                            </h3>
                            <p class="text-sm text-gray-500">{{ formatDate(submission.created_at) }}</p>
                        </div>
                        <span :class="['px-3 py-1 text-xs font-medium rounded-full capitalize', getStatusColor(submission.status)]">
                            {{ submission.status.replace('_', ' ') }}
                        </span>
                    </div>

                    <!-- Audio Player -->
                    <audio :src="submission.audio_url" controls class="w-full mb-4"></audio>

                    <!-- Feedback -->
                    <div v-if="submission.feedback" class="bg-emerald-50 rounded-xl p-4 mt-4">
                        <div class="flex items-center justify-between mb-3">
                            <h4 class="font-medium text-gray-900">Teacher Feedback</h4>
                            <div class="flex">
                                <span v-for="i in 5" :key="i" class="text-lg">
                                    {{ i <= submission.feedback.overall_rating ? '⭐' : '☆' }}
                                </span>
                            </div>
                        </div>
                        <p class="text-gray-700 mb-3">{{ submission.feedback.feedback_text }}</p>
                        <div class="grid grid-cols-3 gap-4 text-center">
                            <div>
                                <p class="text-2xl font-bold text-emerald-600">{{ submission.feedback.average_score }}%</p>
                                <p class="text-xs text-gray-500">Average Score</p>
                            </div>
                        </div>
                        <p class="text-sm text-gray-500 mt-2">- {{ submission.feedback.teacher_name }}</p>
                    </div>
                </div>
            </div>

            <!-- Empty State -->
            <div v-else class="bg-white rounded-2xl shadow-sm border border-gray-100 p-12 text-center">
                <div class="w-20 h-20 bg-emerald-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <span class="text-4xl">🎤</span>
                </div>
                <h3 class="text-lg font-semibold text-gray-900 mb-2">No Recitations Yet</h3>
                <p class="text-gray-500 mb-6">Submit your first recitation to get feedback from your teacher</p>
                <button
                    @click="showModal = true"
                    class="px-6 py-3 bg-emerald-500 hover:bg-emerald-600 text-white font-medium rounded-xl transition-colors"
                >
                    Submit First Recitation
                </button>
            </div>
        </div>

        <!-- Submit Modal -->
        <div v-if="showModal" class="fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4">
            <div class="bg-white rounded-2xl max-w-lg w-full p-6">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-lg font-semibold text-gray-900">Submit Recitation</h3>
                    <button @click="showModal = false" class="text-gray-400 hover:text-gray-600">✕</button>
                </div>

                <form @submit.prevent="submitRecitation" class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Surah</label>
                        <select
                            v-model="form.surah_id"
                            required
                            class="w-full rounded-xl border-gray-200 focus:border-emerald-500 focus:ring-emerald-500"
                        >
                            <option value="">Select a Surah</option>
                            <option v-for="surah in surahs" :key="surah.id" :value="surah.id">
                                {{ surah.surah_number }}. {{ surah.name_english }} ({{ surah.name_arabic }})
                            </option>
                        </select>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">From Ayah</label>
                            <input
                                v-model="form.ayah_from"
                                type="number"
                                min="1"
                                :max="selectedSurah?.total_ayahs || 999"
                                required
                                class="w-full rounded-xl border-gray-200 focus:border-emerald-500 focus:ring-emerald-500"
                            />
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">To Ayah</label>
                            <input
                                v-model="form.ayah_to"
                                type="number"
                                :min="form.ayah_from"
                                :max="selectedSurah?.total_ayahs || 999"
                                required
                                class="w-full rounded-xl border-gray-200 focus:border-emerald-500 focus:ring-emerald-500"
                            />
                        </div>
                    </div>

                    <!-- Recording Section -->
                    <div class="bg-gray-50 rounded-xl p-4">
                        <label class="block text-sm font-medium text-gray-700 mb-3">Record Your Recitation</label>
                        
                        <div v-if="!audioUrl" class="text-center">
                            <button
                                type="button"
                                @click="isRecording ? stopRecording() : startRecording()"
                                :class="[
                                    'w-20 h-20 rounded-full flex items-center justify-center text-3xl transition-all',
                                    isRecording 
                                        ? 'bg-red-500 text-white animate-pulse' 
                                        : 'bg-emerald-500 text-white hover:bg-emerald-600'
                                ]"
                            >
                                {{ isRecording ? '⏹' : '🎤' }}
                            </button>
                            <p class="text-sm text-gray-500 mt-2">
                                {{ isRecording ? 'Recording... Click to stop' : 'Click to start recording' }}
                            </p>
                        </div>

                        <div v-else class="space-y-3">
                            <audio :src="audioUrl" controls class="w-full"></audio>
                            <button
                                type="button"
                                @click="audioUrl = null; audioBlob = null; form.audio = null"
                                class="text-sm text-red-500 hover:text-red-600"
                            >
                                🗑 Delete and record again
                            </button>
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Notes (optional)</label>
                        <textarea
                            v-model="form.notes"
                            rows="2"
                            placeholder="Any specific areas you'd like feedback on?"
                            class="w-full rounded-xl border-gray-200 focus:border-emerald-500 focus:ring-emerald-500"
                        ></textarea>
                    </div>

                    <div class="flex gap-3">
                        <button
                            type="button"
                            @click="showModal = false"
                            class="flex-1 py-3 border border-gray-200 text-gray-700 font-medium rounded-xl hover:bg-gray-50"
                        >
                            Cancel
                        </button>
                        <button
                            type="submit"
                            :disabled="form.processing || !form.audio"
                            class="flex-1 py-3 bg-emerald-500 hover:bg-emerald-600 text-white font-medium rounded-xl disabled:opacity-50"
                        >
                            {{ form.processing ? 'Submitting...' : 'Submit' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </StudentLayout>
</template>
