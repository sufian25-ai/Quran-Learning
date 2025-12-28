<script setup>
import { Head, Link } from '@inertiajs/vue3';
import StudentLayout from '@/Layouts/StudentLayout.vue';

defineProps({
    auth: Object,
    stats: {
        type: Object,
        default: () => ({})
    },
    surahs: {
        type: Array,
        default: () => []
    },
    tajweedSkills: {
        type: Array,
        default: () => []
    },
    recentSessions: {
        type: Array,
        default: () => []
    }
});

const getStatusColor = (status) => {
    const colors = {
        'not_started': 'bg-gray-200',
        'in_progress': 'bg-amber-400',
        'completed': 'bg-emerald-400',
        'memorized': 'bg-purple-500',
    };
    return colors[status] || 'bg-gray-200';
};

const getStatusEmoji = (status) => {
    const emojis = {
        'not_started': '',
        'in_progress': '📖',
        'completed': '✓',
        'memorized': '🏆',
    };
    return emojis[status] || '';
};
</script>

<template>
    <Head title="My Progress | Quran Learning" />

    <StudentLayout>
        <template #header>
            <div>
                <h2 class="font-display text-xl font-bold text-gray-900">📊 My Progress</h2>
                <p class="text-gray-500 text-sm">Track your Quran learning journey</p>
            </div>
        </template>

        <div class="space-y-6">
            <!-- Stats Overview -->
            <div class="grid grid-cols-2 lg:grid-cols-5 gap-4">
                <div class="bg-gradient-to-br from-emerald-500 to-teal-600 rounded-2xl p-5 text-white">
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-3xl">📚</span>
                        <span class="text-2xl font-bold">{{ stats.surahs_started }}</span>
                    </div>
                    <p class="text-emerald-100 text-sm">Surahs Started</p>
                </div>
                <div class="bg-gradient-to-br from-blue-500 to-indigo-600 rounded-2xl p-5 text-white">
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-3xl">✓</span>
                        <span class="text-2xl font-bold">{{ stats.surahs_completed }}</span>
                    </div>
                    <p class="text-blue-100 text-sm">Completed</p>
                </div>
                <div class="bg-gradient-to-br from-purple-500 to-pink-600 rounded-2xl p-5 text-white">
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-3xl">🏆</span>
                        <span class="text-2xl font-bold">{{ stats.surahs_memorized }}</span>
                    </div>
                    <p class="text-purple-100 text-sm">Memorized</p>
                </div>
                <div class="bg-gradient-to-br from-amber-500 to-orange-600 rounded-2xl p-5 text-white">
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-3xl">📖</span>
                        <span class="text-2xl font-bold">{{ stats.total_ayahs_read }}</span>
                    </div>
                    <p class="text-amber-100 text-sm">Ayahs Read</p>
                </div>
                <div class="bg-gradient-to-br from-rose-500 to-red-600 rounded-2xl p-5 text-white">
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-3xl">🧠</span>
                        <span class="text-2xl font-bold">{{ stats.total_ayahs_memorized }}</span>
                    </div>
                    <p class="text-rose-100 text-sm">Ayahs Memorized</p>
                </div>
            </div>

            <div class="grid lg:grid-cols-3 gap-6">
                <!-- Surah Progress Grid -->
                <div class="lg:col-span-2 bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                    <div class="flex items-center justify-between mb-6">
                        <h3 class="font-semibold text-gray-900">📖 Surah Progress</h3>
                        <div class="flex items-center gap-4 text-xs">
                            <span class="flex items-center gap-1"><span class="w-3 h-3 rounded bg-gray-200"></span> Not Started</span>
                            <span class="flex items-center gap-1"><span class="w-3 h-3 rounded bg-amber-400"></span> In Progress</span>
                            <span class="flex items-center gap-1"><span class="w-3 h-3 rounded bg-emerald-400"></span> Completed</span>
                            <span class="flex items-center gap-1"><span class="w-3 h-3 rounded bg-purple-500"></span> Memorized</span>
                        </div>
                    </div>

                    <div class="grid grid-cols-10 sm:grid-cols-12 lg:grid-cols-14 gap-1">
                        <Link
                            v-for="surah in surahs"
                            :key="surah.id"
                            href="/quran"
                            :class="[
                                'aspect-square rounded-lg flex items-center justify-center text-xs font-medium transition-all hover:scale-110 cursor-pointer',
                                getStatusColor(surah.progress?.status || 'not_started'),
                                surah.progress?.status === 'memorized' ? 'text-white' : 'text-gray-600'
                            ]"
                            :title="`${surah.surah_number}. ${surah.name_english} - ${surah.progress?.status || 'not_started'}`"
                        >
                            {{ surah.surah_number }}
                        </Link>
                    </div>
                </div>

                <!-- Tajweed Skills -->
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                    <h3 class="font-semibold text-gray-900 mb-6">🎯 Tajweed Skills</h3>
                    <div class="space-y-4">
                        <div v-for="skill in tajweedSkills" :key="skill.id">
                            <div class="flex items-center justify-between mb-1">
                                <span class="text-sm font-medium text-gray-700">{{ skill.name_english }}</span>
                                <span class="text-sm text-gray-500">{{ skill.skill_level }}%</span>
                            </div>
                            <div class="w-full bg-gray-100 rounded-full h-2">
                                <div
                                    class="h-2 rounded-full transition-all"
                                    :style="{ width: `${skill.skill_level}%`, backgroundColor: skill.color_code }"
                                ></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recent Activity -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                <h3 class="font-semibold text-gray-900 mb-6">📈 This Week's Activity</h3>
                <div class="flex items-end justify-between h-32 gap-2">
                    <div
                        v-for="session in recentSessions"
                        :key="session.date"
                        class="flex-1 flex flex-col items-center"
                    >
                        <div
                            :style="{ height: `${Math.max(session.ayahs_read * 2, 10)}px` }"
                            class="w-full bg-gradient-to-t from-emerald-500 to-teal-400 rounded-t-lg transition-all hover:from-emerald-600"
                        ></div>
                        <span class="text-xs text-gray-500 mt-2">{{ session.date }}</span>
                        <span class="text-xs font-medium text-gray-700">{{ session.ayahs_read }}</span>
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="grid md:grid-cols-3 gap-4">
                <Link
                    href="/quran"
                    class="flex items-center p-4 bg-gradient-to-r from-emerald-500 to-teal-600 rounded-xl text-white hover:shadow-lg transition-all"
                >
                    <span class="text-3xl mr-4">📖</span>
                    <div>
                        <p class="font-semibold">Continue Reading</p>
                        <p class="text-sm text-emerald-100">Open Quran Reader</p>
                    </div>
                </Link>
                <Link
                    href="/recitations"
                    class="flex items-center p-4 bg-gradient-to-r from-purple-500 to-pink-600 rounded-xl text-white hover:shadow-lg transition-all"
                >
                    <span class="text-3xl mr-4">🎤</span>
                    <div>
                        <p class="font-semibold">Submit Recitation</p>
                        <p class="text-sm text-purple-100">Record & get feedback</p>
                    </div>
                </Link>
                <Link
                    href="/leaderboard"
                    class="flex items-center p-4 bg-gradient-to-r from-amber-500 to-orange-600 rounded-xl text-white hover:shadow-lg transition-all"
                >
                    <span class="text-3xl mr-4">🏆</span>
                    <div>
                        <p class="font-semibold">Leaderboard</p>
                        <p class="text-sm text-amber-100">See top learners</p>
                    </div>
                </Link>
            </div>
        </div>
    </StudentLayout>
</template>
