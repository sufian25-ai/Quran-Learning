<script setup>
import { Head, Link } from '@inertiajs/vue3';
import { ref, computed } from 'vue';
import StudentLayout from '@/Layouts/StudentLayout.vue';

const props = defineProps({
    auth: Object,
    leaderboard: {
        type: Array,
        default: () => []
    },
    userRank: {
        type: Object,
        default: null
    },
    timeframe: {
        type: String,
        default: 'all'
    },
    userBadges: {
        type: Array,
        default: () => []
    },
    streakInfo: {
        type: Object,
        default: () => ({})
    }
});

const selectedTimeframe = ref(props.timeframe);

const getMedalEmoji = (rank) => {
    const medals = { 1: '🥇', 2: '🥈', 3: '🥉' };
    return medals[rank] || '';
};

const getRankClass = (rank) => {
    if (rank === 1) return 'bg-gradient-to-r from-yellow-400 to-amber-500 text-white';
    if (rank === 2) return 'bg-gradient-to-r from-gray-300 to-gray-400 text-gray-800';
    if (rank === 3) return 'bg-gradient-to-r from-amber-600 to-amber-700 text-white';
    return 'bg-gray-100 text-gray-700';
};

const formatPoints = (points) => {
    if (points >= 1000) return `${(points / 1000).toFixed(1)}k`;
    return points;
};
</script>

<template>
    <Head title="Leaderboard" />

    <StudentLayout>
        <div class="py-8">
            <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
                <!-- Header -->
                <div class="text-center mb-8">
                    <h1 class="text-3xl font-display font-bold text-gray-900 mb-2">🏆 Leaderboard</h1>
                    <p class="text-gray-500">Top learners in our community</p>
                </div>

                <!-- Your Stats Card -->
                <div class="bg-gradient-to-r from-primary-500 to-primary-600 rounded-2xl p-6 mb-6 text-white">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <div class="w-16 h-16 rounded-full bg-white/20 flex items-center justify-center text-2xl font-bold">
                                {{ auth.user?.name?.charAt(0).toUpperCase() }}
                            </div>
                            <div class="ml-4">
                                <h3 class="text-xl font-semibold">{{ auth.user?.name }}</h3>
                                <p class="text-primary-100">Rank #{{ userRank?.rank || '---' }}</p>
                            </div>
                        </div>
                        <div class="text-right">
                            <p class="text-3xl font-bold">{{ formatPoints(userRank?.points || 0) }}</p>
                            <p class="text-primary-100">XP Points</p>
                        </div>
                    </div>
                    
                    <!-- Streak & Badges Summary -->
                    <div class="grid grid-cols-3 gap-4 mt-6 pt-6 border-t border-white/20">
                        <div class="text-center">
                            <p class="text-2xl font-bold">🔥 {{ streakInfo.current || 0 }}</p>
                            <p class="text-xs text-primary-100">Day Streak</p>
                        </div>
                        <div class="text-center">
                            <p class="text-2xl font-bold">🏅 {{ userBadges.length }}</p>
                            <p class="text-xs text-primary-100">Badges</p>
                        </div>
                        <div class="text-center">
                            <p class="text-2xl font-bold">📚 {{ userRank?.classes_attended || 0 }}</p>
                            <p class="text-xs text-primary-100">Classes</p>
                        </div>
                    </div>
                </div>

                <!-- Timeframe Selector -->
                <div class="flex justify-center gap-2 mb-6">
                    <Link
                        v-for="tf in [
                            { value: 'week', label: 'This Week' },
                            { value: 'month', label: 'This Month' },
                            { value: 'all', label: 'All Time' }
                        ]"
                        :key="tf.value"
                        :href="`/leaderboard?timeframe=${tf.value}`"
                        :class="[
                            'px-4 py-2 rounded-lg text-sm font-medium transition-colors',
                            selectedTimeframe === tf.value
                                ? 'bg-primary-500 text-white'
                                : 'bg-gray-100 text-gray-600 hover:bg-gray-200'
                        ]"
                    >
                        {{ tf.label }}
                    </Link>
                </div>

                <!-- Leaderboard Table -->
                <div class="bg-white rounded-2xl shadow-soft overflow-hidden">
                    <div class="divide-y divide-gray-100">
                        <div
                            v-for="(user, index) in leaderboard"
                            :key="user.id"
                            :class="[
                                'flex items-center p-4 transition-colors',
                                user.id === auth.user?.id ? 'bg-primary-50' : 'hover:bg-gray-50'
                            ]"
                        >
                            <!-- Rank -->
                            <div :class="['w-10 h-10 rounded-full flex items-center justify-center font-bold text-sm', getRankClass(index + 1)]">
                                {{ index + 1 <= 3 ? getMedalEmoji(index + 1) : index + 1 }}
                            </div>

                            <!-- User Info -->
                            <div class="flex-1 ml-4">
                                <div class="flex items-center">
                                    <div class="w-10 h-10 rounded-full bg-primary-100 flex items-center justify-center text-primary-700 font-medium">
                                        {{ user.name?.charAt(0).toUpperCase() }}
                                    </div>
                                    <div class="ml-3">
                                        <p class="font-medium text-gray-900">
                                            {{ user.name }}
                                            <span v-if="user.id === auth.user?.id" class="ml-2 px-2 py-0.5 bg-primary-100 text-primary-700 text-xs rounded-full">You</span>
                                        </p>
                                        <p class="text-sm text-gray-500">{{ user.level || 'Beginner' }}</p>
                                    </div>
                                </div>
                            </div>

                            <!-- Stats -->
                            <div class="text-right">
                                <p class="text-lg font-bold text-gray-900">{{ formatPoints(user.points) }}</p>
                                <p class="text-xs text-gray-400">XP</p>
                            </div>
                        </div>
                    </div>

                    <div v-if="!leaderboard.length" class="p-12 text-center">
                        <span class="text-5xl mb-4 block">🏆</span>
                        <p class="text-gray-500">No learners on the leaderboard yet. Start learning to be the first!</p>
                    </div>
                </div>

                <!-- Your Badges -->
                <div v-if="userBadges.length" class="mt-8">
                    <h2 class="text-xl font-semibold text-gray-900 mb-4">Your Badges</h2>
                    <div class="grid grid-cols-4 sm:grid-cols-6 gap-4">
                        <div
                            v-for="badge in userBadges"
                            :key="badge.id"
                            class="aspect-square bg-white rounded-xl shadow-sm p-4 flex flex-col items-center justify-center text-center group hover:shadow-lg transition-all"
                        >
                            <span class="text-3xl mb-2">{{ badge.icon }}</span>
                            <p class="text-xs font-medium text-gray-700 group-hover:text-primary-600">{{ badge.name }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </StudentLayout>
</template>
