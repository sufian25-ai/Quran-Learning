<script setup>
import { Head, Link } from '@inertiajs/vue3';
import { ref, computed } from 'vue';

const props = defineProps({
    surahs: {
        type: Array,
        default: () => []
    }
});

const selectedSurah = ref(null);
const ayahs = ref([]);
const loading = ref(false);
const showTranslation = ref(true);
const showSidebar = ref(false);

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
    showSidebar.value = false; // Close sidebar on mobile
    loading.value = true;
    try {
        const response = await fetch(`https://api.quran.com/api/v4/quran/verses/uthmani?chapter_number=${surah.surah_number}`);
        const data = await response.json();
        
        const translationResponse = await fetch(`https://api.quran.com/api/v4/quran/translations/131?chapter_number=${surah.surah_number}`);
        const translationData = await translationResponse.json();
        
        ayahs.value = data.verses.map((verse, index) => ({
            number: index + 1,
            text_arabic: verse.text_uthmani,
            translation: translationData.translations?.[index]?.text?.replace(/<[^>]*>/g, '') || '',
        }));
    } catch (error) {
        console.error('Error loading surah:', error);
    }
    loading.value = false;
};
</script>

<template>
    <Head>
        <title>Read Quran Online Free - Holy Quran with Translation | QuranLearn</title>
        <meta name="description" content="Read the Holy Quran online for free with English and Bengali translations. Beautiful Arabic text with audio recitation. All 114 Surahs available." />
        <meta name="keywords" content="Quran, Holy Quran, Read Quran Online, Quran Translation, Arabic Quran, Quran English, Quran Bangla, Surah, Ayat" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" />
    </Head>

    <div class="min-h-screen bg-gradient-to-b from-emerald-50 to-white">
        <!-- Header - Mobile Optimized -->
        <header class="bg-white shadow-sm sticky top-0 z-50">
            <div class="max-w-7xl mx-auto px-3 sm:px-4 py-3 flex items-center justify-between">
                <Link href="/" class="flex items-center space-x-2">
                    <div class="w-8 h-8 sm:w-10 sm:h-10 bg-gradient-to-br from-emerald-500 to-teal-600 rounded-lg sm:rounded-xl flex items-center justify-center text-white text-sm sm:text-lg">
                        📖
                    </div>
                    <span class="font-bold text-base sm:text-xl text-gray-900">
                        Quran<span class="text-emerald-600">Learn</span>
                    </span>
                </Link>
                
                <nav class="flex items-center gap-2 sm:gap-4">
                    <Link href="/hifz-quran" class="hidden sm:block text-gray-600 hover:text-emerald-600 font-medium text-sm">
                        📚 Hifz Quran
                    </Link>
                    <Link href="/login" class="px-3 py-1.5 sm:px-4 sm:py-2 bg-emerald-500 text-white rounded-lg sm:rounded-xl font-medium text-sm hover:bg-emerald-600 transition-colors">
                        Sign In
                    </Link>
                </nav>
            </div>
        </header>

        <!-- Mobile Surah Selector Button -->
        <div v-if="!selectedSurah" class="md:hidden px-4 py-3 bg-emerald-500">
            <button 
                @click="showSidebar = !showSidebar"
                class="w-full py-3 bg-white text-emerald-700 rounded-xl font-semibold flex items-center justify-center gap-2"
            >
                📖 Select Surah to Start Reading
            </button>
        </div>

        <!-- Hero Section - Mobile Optimized -->
        <section v-if="!selectedSurah && !showSidebar" class="py-8 sm:py-12 px-4">
            <div class="max-w-4xl mx-auto text-center">
                <h1 class="text-2xl sm:text-4xl md:text-5xl font-bold text-gray-900 mb-3 sm:mb-4">
                    Read the <span class="text-emerald-600">Holy Quran</span>
                </h1>
                <p class="text-base sm:text-xl text-gray-600 mb-6 sm:mb-8">
                    All 114 Surahs with Arabic text and translation
                </p>
                <div class="flex flex-col sm:flex-row justify-center gap-3 sm:gap-4">
                    <button @click="showSidebar = true" class="px-6 py-3 bg-emerald-500 text-white rounded-xl font-semibold hover:bg-emerald-600 transition-colors">
                        📖 Start Reading
                    </button>
                    <Link href="/hifz-quran" class="px-6 py-3 border-2 border-emerald-500 text-emerald-600 rounded-xl font-semibold hover:bg-emerald-50 transition-colors">
                        📚 Hifz Mode (Para)
                    </Link>
                </div>
            </div>
        </section>

        <div class="max-w-7xl mx-auto px-3 sm:px-4 pb-12">
            <!-- Mobile Surah List (Full Screen Overlay) -->
            <div 
                v-if="showSidebar && !selectedSurah" 
                class="md:hidden fixed inset-0 bg-white z-40 overflow-y-auto"
            >
                <div class="sticky top-0 bg-emerald-500 text-white p-4 flex items-center justify-between">
                    <h2 class="font-bold text-lg">📖 Select Surah</h2>
                    <button @click="showSidebar = false" class="p-2 hover:bg-emerald-600 rounded-lg">✕</button>
                </div>
                <div class="p-3">
                    <div v-for="(groupSurahs, groupName) in surahGroups" :key="groupName" class="mb-4">
                        <h3 class="px-2 py-2 text-xs font-semibold text-gray-400 uppercase">{{ groupName }}</h3>
                        <button
                            v-for="surah in groupSurahs"
                            :key="surah.id"
                            @click="loadSurah(surah)"
                            class="w-full flex items-center justify-between px-4 py-4 rounded-xl text-left transition-all mb-2 bg-gray-50 hover:bg-emerald-50 active:bg-emerald-100"
                        >
                            <div class="flex items-center gap-3">
                                <span class="w-10 h-10 rounded-lg bg-emerald-500 text-white flex items-center justify-center text-sm font-bold">
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

            <!-- Desktop Layout -->
            <div class="hidden md:flex gap-6">
                <!-- Sidebar - Surah List (Desktop) -->
                <div class="w-80 bg-white rounded-2xl shadow-lg overflow-hidden sticky top-24 h-[calc(100vh-8rem)]">
                    <div class="p-4 bg-emerald-500 text-white">
                        <h2 class="font-bold text-lg">📖 Surah List</h2>
                        <p class="text-sm text-emerald-100">114 Surahs</p>
                    </div>
                    
                    <div class="overflow-y-auto h-full pb-20">
                        <div v-for="(groupSurahs, groupName) in surahGroups" :key="groupName" class="p-2">
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

                <!-- Desktop Main Content -->
                <div class="flex-1">
                    <div v-if="!selectedSurah" class="bg-white rounded-2xl shadow-lg p-12 text-center">
                        <div class="text-8xl mb-6">📖</div>
                        <h3 class="text-2xl font-bold text-gray-900 mb-2">Select a Surah</h3>
                        <p class="text-gray-500">Choose a Surah from the left panel</p>
                    </div>
                    
                    <!-- Desktop Reading View - Inside Flex -->
                    <div v-else class="bg-white rounded-2xl shadow-lg overflow-hidden">
                <!-- Surah Header - Mobile Optimized -->
                <div class="bg-gradient-to-r from-emerald-500 to-teal-600 text-white p-3 sm:p-6">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-2 sm:gap-4">
                            <button 
                                @click="selectedSurah = null; showSidebar = false" 
                                class="px-2 py-1 sm:px-3 sm:py-1 bg-white/20 hover:bg-white/30 rounded-lg text-xs sm:text-sm"
                            >
                                ← Back
                            </button>
                            <div>
                                <h2 class="text-sm sm:text-2xl font-bold">{{ selectedSurah.name_english }}</h2>
                                <p class="text-xs sm:text-base text-emerald-100">{{ selectedSurah.name_arabic }} • {{ selectedSurah.total_ayahs }} Ayahs</p>
                            </div>
                        </div>
                        <button
                            @click="showTranslation = !showTranslation"
                            :class="['px-2 py-1 sm:px-4 sm:py-2 rounded-lg text-xs sm:text-sm font-medium transition-colors', showTranslation ? 'bg-white text-emerald-600' : 'bg-emerald-400 text-white']"
                        >
                            {{ showTranslation ? '✓ Translation' : 'Translation' }}
                        </button>
                    </div>
                </div>

                <!-- Loading State -->
                <div v-if="loading" class="flex items-center justify-center py-16 sm:py-20">
                    <div class="animate-spin w-10 h-10 sm:w-12 sm:h-12 border-4 border-emerald-500 border-t-transparent rounded-full"></div>
                </div>

                <!-- Ayahs Content - Mobile Optimized -->
                <div v-else class="p-3 sm:p-6 space-y-3 sm:space-y-4">
                    <!-- Bismillah -->
                    <div v-if="selectedSurah.surah_number !== 9" class="text-center py-4 sm:py-6 border-b border-gray-100">
                        <p class="text-xl sm:text-3xl font-arabic text-gray-800">بِسْمِ اللَّهِ الرَّحْمَٰنِ الرَّحِيمِ</p>
                        <p v-if="showTranslation" class="text-gray-500 mt-2 text-xs sm:text-base">In the name of Allah, the Most Gracious, the Most Merciful</p>
                    </div>

                    <div
                        v-for="(ayah, index) in ayahs"
                        :key="index"
                        class="bg-gray-50 rounded-xl sm:rounded-2xl p-4 sm:p-6 hover:bg-emerald-50 transition-colors"
                    >
                        <div class="flex items-start justify-between mb-3 sm:mb-4">
                            <span class="w-8 h-8 sm:w-10 sm:h-10 rounded-full bg-emerald-100 text-emerald-600 flex items-center justify-center font-bold text-xs sm:text-sm">
                                {{ ayah.number }}
                            </span>
                        </div>

                        <p class="quran-text-mobile sm:quran-text text-right text-gray-900 mb-3 sm:mb-4" dir="rtl">
                            {{ ayah.text_arabic }}
                        </p>

                        <p v-if="showTranslation" class="text-gray-600 leading-relaxed border-t border-gray-200 pt-3 sm:pt-4 text-sm sm:text-base">
                            {{ ayah.translation }}
                        </p>
                    </div>
                </div>
                <!-- End Ayahs Content -->
                    </div>
                    <!-- End Desktop Reading View v-else -->
                </div>
                <!-- End flex-1 -->
            </div>
            <!-- End Desktop Layout -->
        </div>

        <!-- Footer - Mobile Optimized -->
        <footer class="bg-gray-900 text-white py-6 sm:py-8">
            <div class="max-w-7xl mx-auto px-4 text-center">
                <p class="text-gray-400 text-sm">© 2024 QuranLearn</p>
                <div class="mt-3 sm:mt-4 flex flex-wrap justify-center gap-4 sm:gap-6 text-sm">
                    <Link href="/" class="text-gray-400 hover:text-white">Home</Link>
                    <Link href="/hifz-quran" class="text-gray-400 hover:text-white">Hifz Quran</Link>
                    <Link href="/courses" class="text-gray-400 hover:text-white">Courses</Link>
                </div>
            </div>
        </footer>
    </div>
</template>

<style>
@import url('https://fonts.googleapis.com/css2?family=Amiri+Quran&family=Scheherazade+New:wght@400;700&display=swap');

.font-arabic {
    font-family: 'Amiri Quran', 'Scheherazade New', serif;
}

/* Desktop Quran Text */
.quran-text {
    font-family: 'Amiri Quran', 'Scheherazade New', serif;
    font-size: 1.75rem;
    line-height: 2.2;
}

/* Mobile Quran Text */
.quran-text-mobile {
    font-family: 'Amiri Quran', 'Scheherazade New', serif;
    font-size: 1.25rem;
    line-height: 2.4;
}

@media (min-width: 640px) {
    .quran-text-mobile {
        font-size: 1.75rem;
        line-height: 2.2;
    }
}
</style>
