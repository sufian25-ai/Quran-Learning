<script setup>
import { ref, computed } from 'vue';
import { useForm } from '@inertiajs/vue3';

const props = defineProps({
    courseId: {
        type: Number,
        required: true
    },
    teacherId: {
        type: Number,
        default: null
    },
    enrollmentId: {
        type: Number,
        default: null
    },
    courseName: {
        type: String,
        default: ''
    },
    teacherName: {
        type: String,
        default: ''
    },
    existingReview: {
        type: Object,
        default: null
    }
});

const emit = defineEmits(['submitted', 'cancelled']);

const showForm = ref(!props.existingReview);
const hoverRating = ref(0);

const form = useForm({
    rating: props.existingReview?.rating || 0,
    comment: props.existingReview?.comment || '',
    course_id: props.courseId,
    teacher_id: props.teacherId,
    enrollment_id: props.enrollmentId,
});

const ratingLabels = ['', 'Poor', 'Fair', 'Good', 'Very Good', 'Excellent'];

const displayRating = computed(() => {
    return hoverRating.value || form.rating;
});

const setRating = (rating) => {
    form.rating = rating;
    hoverRating.value = 0;
};

const submit = () => {
    form.post('/reviews', {
        preserveScroll: true,
        onSuccess: () => {
            emit('submitted');
            showForm.value = false;
        },
    });
};

const cancel = () => {
    form.reset();
    emit('cancelled');
};
</script>

<template>
    <div class="bg-white rounded-2xl shadow-soft p-6">
        <!-- Existing Review Display -->
        <div v-if="existingReview && !showForm" class="text-center">
            <p class="text-gray-500 mb-2">You've already reviewed this course</p>
            <div class="flex justify-center mb-2">
                <span v-for="i in 5" :key="i" class="text-2xl">
                    {{ i <= existingReview.rating ? '⭐' : '☆' }}
                </span>
            </div>
            <p class="text-gray-700 italic">"{{ existingReview.comment }}"</p>
            <button 
                @click="showForm = true"
                class="mt-4 text-primary-500 hover:text-primary-600 text-sm font-medium"
            >
                Edit Review
            </button>
        </div>

        <!-- Review Form -->
        <div v-else>
            <div class="text-center mb-6">
                <h3 class="text-lg font-semibold text-gray-900">Rate Your Experience</h3>
                <p class="text-sm text-gray-500 mt-1">
                    Share your thoughts about <span class="font-medium">{{ courseName }}</span>
                    <span v-if="teacherName"> with {{ teacherName }}</span>
                </p>
            </div>

            <form @submit.prevent="submit" class="space-y-6">
                <!-- Star Rating -->
                <div class="text-center">
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
                            <span :class="i <= displayRating ? 'text-yellow-400' : 'text-gray-300'">
                                {{ i <= displayRating ? '★' : '☆' }}
                            </span>
                        </button>
                    </div>
                    <p class="text-sm font-medium" :class="displayRating ? 'text-gray-700' : 'text-gray-400'">
                        {{ ratingLabels[displayRating] || 'Click to rate' }}
                    </p>
                    <p v-if="form.errors.rating" class="mt-1 text-sm text-red-500">
                        {{ form.errors.rating }}
                    </p>
                </div>

                <!-- Comment -->
                <div>
                    <label for="comment" class="block text-sm font-medium text-gray-700 mb-2">
                        Your Review (Optional)
                    </label>
                    <textarea
                        id="comment"
                        v-model="form.comment"
                        rows="4"
                        class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:border-primary-500 focus:ring-2 focus:ring-primary-500/20 transition-colors resize-none"
                        placeholder="What did you like about this course? How was the teaching quality?"
                    ></textarea>
                    <p v-if="form.errors.comment" class="mt-1 text-sm text-red-500">
                        {{ form.errors.comment }}
                    </p>
                </div>

                <!-- Submit Buttons -->
                <div class="flex justify-end space-x-3">
                    <button
                        v-if="existingReview"
                        type="button"
                        @click="cancel"
                        class="px-6 py-3 border border-gray-200 text-gray-600 rounded-xl hover:bg-gray-50 transition-colors"
                    >
                        Cancel
                    </button>
                    <button
                        type="submit"
                        :disabled="form.processing || form.rating === 0"
                        class="px-6 py-3 bg-primary-500 hover:bg-primary-600 text-white font-medium rounded-xl transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
                    >
                        <span v-if="form.processing">Submitting...</span>
                        <span v-else>{{ existingReview ? 'Update Review' : 'Submit Review' }}</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</template>
