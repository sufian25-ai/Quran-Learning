<script setup>
import { ref } from 'vue';
import { Link, router } from '@inertiajs/vue3';

const props = defineProps({
    show: {
        type: Boolean,
        default: false
    },
    course: {
        type: Object,
        required: true
    },
    enrollment: {
        type: Object,
        required: true
    }
});

const emit = defineEmits(['close', 'reviewed']);

const rating = ref(0);
const comment = ref('');
const hoverRating = ref(0);
const isSubmitting = ref(false);

const ratingLabels = ['', 'Poor', 'Fair', 'Good', 'Very Good', 'Excellent'];

const displayRating = () => hoverRating.value || rating.value;

const setRating = (r) => {
    rating.value = r;
    hoverRating.value = 0;
};

const submitReview = () => {
    if (rating.value === 0) return;
    
    isSubmitting.value = true;
    
    router.post('/reviews', {
        rating: rating.value,
        comment: comment.value,
        course_id: props.course.id,
        enrollment_id: props.enrollment.id,
        teacher_id: props.course.teacher_id || null,
    }, {
        preserveScroll: true,
        onSuccess: () => {
            emit('reviewed');
            emit('close');
        },
        onFinish: () => {
            isSubmitting.value = false;
        }
    });
};

const skipReview = () => {
    // Mark that user skipped review
    router.post(`/enrollments/${props.enrollment.id}/skip-review`, {}, {
        preserveScroll: true,
    });
    emit('close');
};
</script>

<template>
    <Teleport to="body">
        <div 
            v-if="show" 
            class="fixed inset-0 z-50 flex items-center justify-center p-4"
        >
            <!-- Backdrop -->
            <div 
                class="absolute inset-0 bg-black/50 backdrop-blur-sm"
                @click="$emit('close')"
            ></div>

            <!-- Modal -->
            <div class="relative bg-white rounded-3xl shadow-2xl max-w-lg w-full p-8 transform transition-all">
                <!-- Close button -->
                <button 
                    @click="$emit('close')"
                    class="absolute top-4 right-4 p-2 text-gray-400 hover:text-gray-600 rounded-lg hover:bg-gray-100"
                >
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>

                <!-- Celebration Icon -->
                <div class="text-center mb-6">
                    <div class="inline-flex items-center justify-center w-20 h-20 bg-gradient-to-br from-primary-400 to-primary-600 rounded-full mb-4">
                        <span class="text-4xl">🎉</span>
                    </div>
                    <h2 class="text-2xl font-bold text-gray-900">Congratulations!</h2>
                    <p class="text-gray-500 mt-2">
                        You've completed <span class="font-medium text-primary-600">{{ course.title }}</span>
                    </p>
                </div>

                <!-- Rating Request -->
                <div class="bg-gray-50 rounded-2xl p-6 mb-6">
                    <p class="text-center text-gray-700 mb-4">
                        How was your learning experience?
                    </p>

                    <!-- Star Rating -->
                    <div class="flex justify-center space-x-2 mb-2">
                        <button
                            v-for="i in 5"
                            :key="i"
                            type="button"
                            @click="setRating(i)"
                            @mouseenter="hoverRating = i"
                            @mouseleave="hoverRating = 0"
                            class="text-4xl transition-transform hover:scale-110 focus:outline-none"
                        >
                            <span :class="i <= displayRating() ? 'text-yellow-400' : 'text-gray-300'">
                                {{ i <= displayRating() ? '★' : '☆' }}
                            </span>
                        </button>
                    </div>
                    <p class="text-center text-sm font-medium" :class="displayRating() ? 'text-gray-700' : 'text-gray-400'">
                        {{ ratingLabels[displayRating()] || 'Tap a star to rate' }}
                    </p>
                </div>

                <!-- Comment (shows after rating) -->
                <div v-if="rating > 0" class="mb-6 animate-fadeIn">
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Share your thoughts (optional)
                    </label>
                    <textarea
                        v-model="comment"
                        rows="3"
                        class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:border-primary-500 focus:ring-2 focus:ring-primary-500/20 transition-colors resize-none"
                        placeholder="What did you learn? How was the teaching?"
                    ></textarea>
                </div>

                <!-- Action Buttons -->
                <div class="flex space-x-3">
                    <button
                        @click="skipReview"
                        class="flex-1 py-3 text-gray-600 hover:text-gray-800 font-medium transition-colors"
                    >
                        Maybe Later
                    </button>
                    <button
                        @click="submitReview"
                        :disabled="rating === 0 || isSubmitting"
                        class="flex-1 py-3 bg-primary-500 hover:bg-primary-600 text-white font-semibold rounded-xl transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
                    >
                        {{ isSubmitting ? 'Submitting...' : 'Submit Review' }}
                    </button>
                </div>
            </div>
        </div>
    </Teleport>
</template>

<style scoped>
.animate-fadeIn {
    animation: fadeIn 0.3s ease-out;
}

@keyframes fadeIn {
    from {
        opacity: 0;
        transform: translateY(-10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}
</style>
