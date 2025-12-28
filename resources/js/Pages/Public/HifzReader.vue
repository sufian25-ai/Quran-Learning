<script setup>
import { Head, Link } from '@inertiajs/vue3';
import { ref, computed, watch } from 'vue';

// No auth required for public access

// Para (Juz) data - 30 Paras, each with ~20 pages
const paras = computed(() => {
    return Array.from({ length: 30 }, (_, i) => ({
        number: i + 1,
        name: paraNames[i] || `Para ${i + 1}`,
        nameArabic: paraArabicNames[i] || '',
        pages: 20
    }));
});

// Para names
const paraNames = [
    'Alif Lam Meem', 'Sayaqul', 'Tilkal Rusul', 'Lan Tana Lu', 'Wal Muhsanat',
    'La Yuhibbullah', 'Wa Iza Samiu', 'Wa Lau Annana', 'Qalal Mala', 'Wa A\'lamu',
    'Ya\'tazirun', 'Wa Mamin Dabbah', 'Wa Ma Ubarri', 'Rubama', 'Subhanalla Zi',
    'Qal Alam', 'Iqtaraba Lin Nas', 'Qadd Aflaha', 'Wa Qala Llazina', 'Amman Khalaq',
    'Utlu Ma Uhiya', 'Wa Man Yaqnut', 'Wa Mali', 'Faman Azlamu', 'Ilayhi Yuraddu',
    'Ha Meem', 'Qala Fama Khatbukum', 'Qadd Sami Allah', 'Tabaraka Llazi', 'Amma Yatasaalun'
];

const paraArabicNames = [
    'آلم', 'سَيَقُولُ', 'تِلْكَ الرُّسُلُ', 'لَنْ تَنَالُوا', 'وَالْمُحْصَنَاتُ',
    'لا يُحِبُّ اللَّهُ', 'وَإِذَا سَمِعُوا', 'وَلَوْ أَنَّنَا', 'قَالَ الْمَلأُ', 'وَاعْلَمُوا',
    'يَعْتَذِرُونَ', 'وَمَا مِنْ دَابَّةٍ', 'وَمَا أُبَرِّئُ', 'رُبَمَا', 'سُبْحَانَ الَّذِي',
    'قَالَ أَلَمْ', 'اقْتَرَبَ لِلنَّاسِ', 'قَدْ أَفْلَحَ', 'وَقَالَ الَّذِينَ', 'أَمَّنْ خَلَقَ',
    'اتْلُ مَا أُوحِيَ', 'وَمَنْ يَقْنُتْ', 'وَمَا لِيَ', 'فَمَنْ أَظْلَمُ', 'إِلَيْهِ يُرَدُّ',
    'حم', 'قَالَ فَمَا خَطْبُكُمْ', 'قَدْ سَمِعَ اللَّهُ', 'تَبَارَكَ الَّذِي', 'عَمَّ يَتَسَاءَلُونَ'
];

// Juz page mappings
const juzPageStart = [1, 22, 42, 62, 82, 102, 121, 142, 162, 182, 201, 222, 242, 262, 282, 302, 322, 342, 362, 382, 402, 422, 442, 462, 482, 502, 522, 542, 562, 582];

const selectedPara = ref(null);
const currentPage = ref(1);
const loading = ref(false);
const pageVerses = ref({});
const showSidebar = ref(false);

// Calculate actual Quran page number
const getActualPageNumber = (juzNumber, pageWithinJuz) => {
    const startPage = juzPageStart[juzNumber - 1];
    return startPage + pageWithinJuz - 1;
};

// Fetch verses for a specific page
const fetchPageVerses = async (pageNumber) => {
    if (pageVerses.value[pageNumber]) return;
    
    try {
        const response = await fetch(`https://api.quran.com/api/v4/quran/verses/uthmani?page_number=${pageNumber}`);
        const data = await response.json();
        pageVerses.value[pageNumber] = data.verses?.map(v => v.text_uthmani) || [];
    } catch (error) {
        console.error('Error fetching page:', error);
        pageVerses.value[pageNumber] = [];
    }
};

// Watch for page changes
watch([selectedPara, currentPage], async () => {
    if (!selectedPara.value) return;
    
    loading.value = true;
    const actualPage = getActualPageNumber(selectedPara.value.number, currentPage.value);
    await fetchPageVerses(actualPage);
    loading.value = false;
}, { immediate: true });

// Get verses for current page
const currentVerses = computed(() => {
    if (!selectedPara.value) return [];
    const actualPage = getActualPageNumber(selectedPara.value.number, currentPage.value);
    return pageVerses.value[actualPage] || [];
});

const selectPara = (para) => {
    selectedPara.value = para;
    currentPage.value = 1;
    showSidebar.value = false; // Close sidebar on mobile after selection
};

const nextPage = () => {
    if (currentPage.value < 20) currentPage.value++;
};

const prevPage = () => {
    if (currentPage.value > 1) currentPage.value--;
};
</script>

<template>
    <Head>
        <title>Hifz Quran Online - Read Quran by Para (Juz) | QuranLearn</title>
        <meta name="description" content="Read Quran online for Hifz memorization. 30 Paras (Juz) with 20 pages each. Mushaf style view with Arabic text. Free access." />
        <meta name="keywords" content="Hifz Quran, Para Quran, Juz, Quran Memorization, Read Quran Online, Mushaf, Arabic Quran" />
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
                    <Link href="/read-quran" class="hidden sm:block text-gray-600 hover:text-emerald-600 font-medium text-sm">
                        📖 Quran Reader
                    </Link>
                    <Link href="/login" class="px-3 py-1.5 sm:px-4 sm:py-2 bg-emerald-500 text-white rounded-lg sm:rounded-xl font-medium text-sm hover:bg-emerald-600 transition-colors">
                        Sign In
                    </Link>
                </nav>
            </div>
        </header>

        <!-- Mobile Para Selector Button -->
        <div v-if="!selectedPara" class="md:hidden px-4 py-3 bg-emerald-500">
            <button 
                @click="showSidebar = !showSidebar"
                class="w-full py-3 bg-white text-emerald-700 rounded-xl font-semibold flex items-center justify-center gap-2"
            >
                📚 Select Para (Juz) to Start Reading
            </button>
        </div>

        <!-- Hero Section - Mobile Optimized -->
        <section v-if="!selectedPara && !showSidebar" class="py-8 sm:py-12 px-4">
            <div class="max-w-4xl mx-auto text-center">
                <h1 class="text-2xl sm:text-4xl md:text-5xl font-bold text-gray-900 mb-3 sm:mb-4">
                    Read <span class="text-emerald-600">Hifz Quran</span>
                </h1>
                <p class="text-base sm:text-xl text-gray-600 mb-6 sm:mb-8">
                    30 Paras (Juz) • Perfect for Memorization
                </p>
                <div class="flex flex-col sm:flex-row justify-center gap-3 sm:gap-4">
                    <button @click="showSidebar = true" class="px-6 py-3 bg-emerald-500 text-white rounded-xl font-semibold hover:bg-emerald-600 transition-colors">
                        📚 Start Reading
                    </button>
                    <Link href="/read-quran" class="px-6 py-3 border-2 border-emerald-500 text-emerald-600 rounded-xl font-semibold hover:bg-emerald-50 transition-colors">
                        📖 Surah Mode
                    </Link>
                </div>
            </div>
        </section>

        <div class="max-w-7xl mx-auto px-3 sm:px-4 pb-12">
            <!-- Mobile Para List (Full Screen Overlay) -->
            <div 
                v-if="showSidebar && !selectedPara" 
                class="md:hidden fixed inset-0 bg-white z-40 overflow-y-auto"
            >
                <div class="sticky top-0 bg-emerald-500 text-white p-4 flex items-center justify-between">
                    <h2 class="font-bold text-lg">📚 Select Para</h2>
                    <button @click="showSidebar = false" class="p-2 hover:bg-emerald-600 rounded-lg">✕</button>
                </div>
                <div class="p-3">
                    <button
                        v-for="para in paras"
                        :key="para.number"
                        @click="selectPara(para)"
                        class="w-full flex items-center justify-between px-4 py-4 rounded-xl text-left transition-all mb-2 bg-gray-50 hover:bg-emerald-50 active:bg-emerald-100"
                    >
                        <div class="flex items-center gap-3">
                            <span class="w-12 h-12 rounded-xl bg-emerald-500 text-white flex items-center justify-center text-lg font-bold">
                                {{ para.number }}
                            </span>
                            <div>
                                <p class="font-semibold text-gray-900">{{ para.name }}</p>
                                <p class="text-sm text-gray-500">20 Pages</p>
                            </div>
                        </div>
                        <span class="text-xl text-emerald-700">{{ para.nameArabic }}</span>
                    </button>
                </div>
            </div>

            <!-- Desktop Layout -->
            <div class="hidden md:flex gap-6">
                <!-- Sidebar - Para List (Desktop) -->
                <div class="w-72 lg:w-80 bg-white rounded-2xl shadow-lg overflow-hidden sticky top-24 h-[calc(100vh-8rem)]">
                    <div class="p-4 bg-emerald-500 text-white">
                        <h2 class="font-bold text-lg">📚 Para List</h2>
                        <p class="text-sm text-emerald-100">30 Paras (Juz)</p>
                    </div>
                    
                    <div class="overflow-y-auto h-full pb-20">
                        <div class="p-2">
                            <button
                                v-for="para in paras"
                                :key="para.number"
                                @click="selectPara(para)"
                                :class="[
                                    'w-full flex items-center justify-between px-3 py-3 rounded-xl text-left transition-all mb-1',
                                    selectedPara?.number === para.number 
                                        ? 'bg-emerald-50 border border-emerald-200' 
                                        : 'hover:bg-gray-50'
                                ]"
                            >
                                <div class="flex items-center gap-3">
                                    <span :class="[
                                        'w-10 h-10 rounded-xl flex items-center justify-center text-sm font-bold',
                                        selectedPara?.number === para.number 
                                            ? 'bg-emerald-500 text-white' 
                                            : 'bg-emerald-100 text-emerald-600'
                                    ]">
                                        {{ para.number }}
                                    </span>
                                    <div>
                                        <p class="font-medium text-gray-900">{{ para.name }}</p>
                                        <p class="text-xs text-gray-500">20 Pages</p>
                                    </div>
                                </div>
                                <span class="text-lg text-emerald-700">{{ para.nameArabic }}</span>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Desktop Main Content -->
                <div class="flex-1">
                    <div v-if="!selectedPara" class="bg-white rounded-2xl shadow-lg p-12 text-center">
                        <div class="text-8xl mb-6">📚</div>
                        <h3 class="text-2xl font-bold text-gray-900 mb-2">Select a Para</h3>
                        <p class="text-gray-500">Choose a Para (Juz) from the left panel</p>
                    </div>
                    
                    <!-- Desktop Reading View - Inside Flex -->
                    <div v-else class="bg-white rounded-2xl shadow-lg overflow-hidden">
                <!-- Para Header - Mobile Optimized -->
                <div class="bg-gradient-to-r from-emerald-500 to-teal-600 text-white p-3 sm:p-6">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-2 sm:gap-4">
                            <button 
                                @click="selectedPara = null; showSidebar = false" 
                                class="px-2 py-1 sm:px-3 sm:py-1 bg-white/20 hover:bg-white/30 rounded-lg text-xs sm:text-sm"
                            >
                                ← Back
                            </button>
                            <div>
                                <h2 class="text-sm sm:text-2xl font-bold">Para {{ selectedPara.number }}</h2>
                                <p class="text-xs sm:text-base text-emerald-100">{{ selectedPara.nameArabic }} • Page {{ currentPage }}/20</p>
                            </div>
                        </div>
                        <span class="px-2 py-1 sm:px-4 sm:py-2 bg-white/20 rounded-lg sm:rounded-xl text-xs sm:text-sm">
                            📖 {{ currentPage }}/20
                        </span>
                    </div>
                </div>

                <!-- Page Navigation - Mobile Optimized -->
                <div class="bg-emerald-50 p-2 sm:p-4 border-b border-emerald-100 flex items-center justify-between gap-2">
                    <button 
                        @click="prevPage" 
                        :disabled="currentPage <= 1"
                        :class="[
                            'px-3 py-2 sm:px-4 sm:py-2 rounded-lg sm:rounded-xl font-medium transition-colors text-xs sm:text-sm',
                            currentPage <= 1 
                                ? 'bg-gray-100 text-gray-400 cursor-not-allowed' 
                                : 'bg-emerald-500 text-white hover:bg-emerald-600 active:bg-emerald-700'
                        ]"
                    >
                        ‹ Prev
                    </button>

                    <div class="flex items-center gap-1 sm:gap-2">
                        <span class="text-xs sm:text-sm text-gray-600 hidden sm:inline">Page</span>
                        <select 
                            v-model="currentPage" 
                            class="px-2 py-1.5 sm:px-3 sm:py-2 border border-emerald-200 rounded-lg bg-white text-emerald-700 font-medium text-sm"
                        >
                            <option v-for="p in 20" :key="p" :value="p">{{ p }}</option>
                        </select>
                        <span class="text-xs sm:text-sm text-gray-600">/20</span>
                    </div>

                    <button 
                        @click="nextPage" 
                        :disabled="currentPage >= 20"
                        :class="[
                            'px-3 py-2 sm:px-4 sm:py-2 rounded-lg sm:rounded-xl font-medium transition-colors text-xs sm:text-sm',
                            currentPage >= 20 
                                ? 'bg-gray-100 text-gray-400 cursor-not-allowed' 
                                : 'bg-emerald-500 text-white hover:bg-emerald-600 active:bg-emerald-700'
                        ]"
                    >
                        Next ›
                    </button>
                </div>

                <!-- Quran Content - Mobile Optimized -->
                <div class="p-3 sm:p-8">
                    <!-- Loading State -->
                    <div v-if="loading" class="flex items-center justify-center py-16 sm:py-20">
                        <div class="animate-spin w-10 h-10 sm:w-12 sm:h-12 border-4 border-emerald-500 border-t-transparent rounded-full"></div>
                    </div>

                    <!-- Quran Page -->
                    <div v-else class="bg-amber-50 border-2 sm:border-4 border-emerald-600 rounded-xl sm:rounded-2xl p-4 sm:p-8 min-h-[400px] sm:min-h-[500px]">
                        <!-- Page Header -->
                        <div class="text-center mb-4 sm:mb-6 pb-3 sm:pb-4 border-b-2 border-emerald-300">
                            <span class="text-xs sm:text-sm font-medium text-emerald-700">
                                পারা {{ selectedPara.number }} • পৃষ্ঠা {{ currentPage }}
                            </span>
                        </div>

                        <!-- Arabic Text - Responsive Font Size -->
                        <div class="quran-text-mobile sm:quran-text text-center" dir="rtl">
                            <span 
                                v-for="(verse, idx) in currentVerses" 
                                :key="idx"
                                class="inline"
                            >
                                {{ verse }}
                                <span class="text-emerald-600 mx-0.5 sm:mx-1">۝</span>
                            </span>
                            
                            <p v-if="!currentVerses.length && !loading" class="text-center text-gray-400 py-8 sm:py-10">
                                ⏳ Loading...
                            </p>
                        </div>

                        <!-- Page Footer -->
                        <div class="text-center mt-4 sm:mt-6 pt-3 sm:pt-4 border-t-2 border-emerald-300">
                            <span class="inline-flex items-center justify-center w-10 h-10 sm:w-12 sm:h-12 rounded-full bg-emerald-100 text-emerald-700 font-bold text-base sm:text-lg">
                                {{ currentPage }}
                            </span>
                        </div>
                    </div>
                </div>
                <!-- Swipe Navigation Hint (Mobile) -->
                <div class="md:hidden text-center py-3 bg-gray-50 text-gray-500 text-xs">
                    Use buttons above to navigate pages
                </div>
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
                    <Link href="/read-quran" class="text-gray-400 hover:text-white">Quran</Link>
                    <Link href="/courses" class="text-gray-400 hover:text-white">Courses</Link>
                </div>
            </div>
        </footer>
    </div>
</template>

<style>
@import url('https://fonts.googleapis.com/css2?family=Amiri+Quran&family=Scheherazade+New:wght@400;700&display=swap');

/* Desktop Quran Text */
.quran-text {
    font-family: 'Amiri Quran', 'Scheherazade New', serif;
    font-size: 1.5rem;
    line-height: 2.2;
    word-spacing: 4px;
    color: #1a1a1a;
}

/* Mobile Quran Text - Larger and more readable */
.quran-text-mobile {
    font-family: 'Amiri Quran', 'Scheherazade New', serif;
    font-size: 1.25rem;
    line-height: 2.4;
    word-spacing: 2px;
    color: #1a1a1a;
}

@media (min-width: 640px) {
    .quran-text-mobile {
        font-size: 1.5rem;
        line-height: 2.2;
        word-spacing: 4px;
    }
}
</style>
