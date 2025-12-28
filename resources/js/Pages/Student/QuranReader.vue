<script setup>
import { Head, Link } from '@inertiajs/vue3';
import { ref, computed, watch } from 'vue';
import StudentLayout from '@/Layouts/StudentLayout.vue';

const props = defineProps({
    auth: Object,
    surahs: {
        type: Array,
        default: () => []
    }
});

const selectedSurah = ref(null);
const ayahs = ref([]);
const loading = ref(false);
const currentAyah = ref(0);
const playingAudio = ref(null);
const showTranslation = ref(true);
const hifzMode = ref(false);

const surahGroups = computed(() => {
    const groups = {
        'Juz Amma (78-114)': props.surahs.filter(s => s.surah_number >= 78),
        'Short Surahs (50-77)': props.surahs.filter(s => s.surah_number >= 50 && s.surah_number < 78),
        'Medium Surahs (20-49)': props.surahs.filter(s => s.surah_number >= 20 && s.surah_number < 50),
        'Long Surahs (1-19)': props.surahs.filter(s => s.surah_number < 20),
    };
    return groups;
});

const loadSurah = async (surah) => {
    selectedSurah.value = surah;
    loading.value = true;
    try {
        // Fetch from Quran.com API
        const response = await fetch(`https://api.quran.com/api/v4/quran/verses/uthmani?chapter_number=${surah.surah_number}`);
        const data = await response.json();
        
        // Fetch translations
        const translationResponse = await fetch(`https://api.quran.com/api/v4/quran/translations/131?chapter_number=${surah.surah_number}`);
        const translationData = await translationResponse.json();
        
        ayahs.value = data.verses.map((verse, index) => ({
            number: index + 1,
            text_arabic: verse.text_uthmani,
            translation: translationData.translations?.[index]?.text?.replace(/<[^>]*>/g, '') || '',
            audio_url: `https://cdn.islamic.network/quran/audio/128/ar.alafasy/${verse.verse_key.replace(':', '')}.mp3`,
        }));
    } catch (error) {
        console.error('Error loading surah:', error);
    }
    loading.value = false;
};

const playAyah = (index) => {
    if (playingAudio.value) {
        playingAudio.value.pause();
    }
    const audio = new Audio(ayahs.value[index].audio_url);
    playingAudio.value = audio;
    currentAyah.value = index;
    audio.play();
    audio.onended = () => {
        if (index < ayahs.value.length - 1) {
            playAyah(index + 1);
        } else {
            playingAudio.value = null;
            currentAyah.value = null;
        }
    };
};

const stopAudio = () => {
    if (playingAudio.value) {
        playingAudio.value.pause();
        playingAudio.value = null;
        currentAyah.value = null;
    }
};

const saveProgress = async (lastAyah) => {
    if (!selectedSurah.value) return;
    try {
        await fetch(`/quran/progress/${selectedSurah.value.id}`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content,
            },
            body: JSON.stringify({ last_ayah_read: lastAyah }),
        });
    } catch (error) {
        console.error('Error saving progress:', error);
    }
};
</script>

<template>
    <Head title="Quran Reader" />

    <StudentLayout>
        <div class="flex h-[calc(100vh-4rem)]">
            <!-- Sidebar - Surah List -->
            <div class="w-80 bg-white border-r border-gray-200 overflow-y-auto">
                <div class="sticky top-0 bg-white border-b border-gray-200 p-4">
                    <h2 class="font-display text-lg font-bold text-gray-900">📖 Quran Reader</h2>
                    <p class="text-sm text-gray-500">114 Surahs</p>
                </div>
                
                <div class="p-2">
                    <div v-for="(groupSurahs, groupName) in surahGroups" :key="groupName" class="mb-4">
                        <h3 class="px-3 py-2 text-xs font-semibold text-gray-400 uppercase">{{ groupName }}</h3>
                        <button
                            v-for="surah in groupSurahs"
                            :key="surah.id"
                            @click="loadSurah(surah)"
                            :class="[
                                'w-full flex items-center justify-between px-3 py-3 rounded-xl text-left transition-all',
                                selectedSurah?.id === surah.id 
                                    ? 'bg-emerald-50 border border-emerald-200' 
                                    : 'hover:bg-gray-50'
                            ]"
                        >
                            <div class="flex items-center gap-3">
                                <span class="w-8 h-8 rounded-lg bg-emerald-100 text-emerald-600 flex items-center justify-center text-sm font-bold">
                                    {{ surah.surah_number }}
                                </span>
                                <div>
                                    <p class="font-medium text-gray-900">{{ surah.name_english }}</p>
                                    <p class="text-xs text-gray-500">{{ surah.total_ayahs }} ayahs</p>
                                </div>
                            </div>
                            <span class="text-lg">{{ surah.name_arabic }}</span>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Main Content -->
            <div class="flex-1 overflow-y-auto bg-gradient-to-b from-emerald-50 to-white">
                <div v-if="!selectedSurah" class="flex items-center justify-center h-full">
                    <div class="text-center">
                        <span class="text-6xl mb-4 block">📖</span>
                        <h3 class="text-xl font-semibold text-gray-900 mb-2">Select a Surah</h3>
                        <p class="text-gray-500">Choose a surah from the left panel to start reading</p>
                    </div>
                </div>

                <div v-else>
                    <!-- Surah Header -->
                    <div class="sticky top-0 bg-white/80 backdrop-blur-sm border-b border-gray-200 p-4 z-10">
                        <div class="max-w-3xl mx-auto flex items-center justify-between">
                            <div>
                                <h2 class="text-2xl font-bold text-gray-900">{{ selectedSurah.name_english }}</h2>
                                <p class="text-gray-500">{{ selectedSurah.name_arabic }} • {{ selectedSurah.total_ayahs }} Ayahs</p>
                            </div>
                            <div class="flex items-center gap-2">
                                <button
                                    @click="showTranslation = !showTranslation"
                                    :class="['px-3 py-2 rounded-lg text-sm font-medium transition-colors', showTranslation ? 'bg-emerald-100 text-emerald-700' : 'bg-gray-100 text-gray-700']"
                                >
                                    Translation
                                </button>
                                <button
                                    @click="hifzMode = !hifzMode"
                                    :class="['px-3 py-2 rounded-lg text-sm font-medium transition-colors', hifzMode ? 'bg-purple-100 text-purple-700' : 'bg-gray-100 text-gray-700']"
                                >
                                    🧠 Hifz Mode
                                </button>
                                <button
                                    v-if="playingAudio"
                                    @click="stopAudio"
                                    class="px-3 py-2 bg-red-100 text-red-700 rounded-lg text-sm font-medium"
                                >
                                    ⏹ Stop
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Loading -->
                    <div v-if="loading" class="flex items-center justify-center py-20">
                        <div class="animate-spin w-8 h-8 border-4 border-emerald-500 border-t-transparent rounded-full"></div>
                    </div>

                    <!-- Ayahs -->
                    <div v-else class="max-w-3xl mx-auto p-6 space-y-4">
                        <!-- Bismillah -->
                        <div v-if="selectedSurah.surah_number !== 9" class="text-center py-6">
                            <p class="text-3xl font-arabic text-gray-800">بِسْمِ اللَّهِ الرَّحْمَٰنِ الرَّحِيمِ</p>
                            <p v-if="showTranslation" class="text-gray-500 mt-2">In the name of Allah, the Most Gracious, the Most Merciful</p>
                        </div>

                        <div
                            v-for="(ayah, index) in ayahs"
                            :key="index"
                            @click="saveProgress(ayah.number)"
                            :class="[
                                'bg-white rounded-2xl p-6 shadow-sm border transition-all cursor-pointer',
                                currentAyah === index 
                                    ? 'border-emerald-400 ring-2 ring-emerald-200' 
                                    : 'border-gray-100 hover:border-emerald-200',
                                hifzMode ? 'group' : ''
                            ]"
                        >
                            <div class="flex items-start justify-between mb-4">
                                <span class="w-10 h-10 rounded-full bg-emerald-100 text-emerald-600 flex items-center justify-center font-bold text-sm">
                                    {{ ayah.number }}
                                </span>
                                <button
                                    @click.stop="playAyah(index)"
                                    class="p-2 rounded-full hover:bg-emerald-100 transition-colors"
                                >
                                    <span v-if="currentAyah === index" class="text-emerald-600 animate-pulse">🔊</span>
                                    <span v-else>▶️</span>
                                </button>
                            </div>

                            <p :class="[
                                'text-2xl leading-loose text-right font-arabic text-gray-900 mb-4',
                                hifzMode ? 'blur-sm group-hover:blur-none transition-all' : ''
                            ]">
                                {{ ayah.text_arabic }}
                            </p>

                            <p v-if="showTranslation" :class="[
                                'text-gray-600 leading-relaxed',
                                hifzMode ? 'blur-sm group-hover:blur-none transition-all' : ''
                            ]">
                                {{ ayah.translation }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </StudentLayout>
</template>

<style>
.font-arabic {
    font-family: 'Scheherazade New', 'Traditional Arabic', serif;
}
</style>
