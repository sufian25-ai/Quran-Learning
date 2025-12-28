<script setup>
import { Head } from '@inertiajs/vue3';
import { ref, computed, watch } from 'vue';
import StudentLayout from '@/Layouts/StudentLayout.vue';

defineProps({
    auth: Object
});

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

// Juz page mappings (actual Quran page numbers for each Juz)
const juzPageStart = [1, 22, 42, 62, 82, 102, 121, 142, 162, 182, 201, 222, 242, 262, 282, 302, 322, 342, 362, 382, 402, 422, 442, 462, 482, 502, 522, 542, 562, 582];

const selectedPara = ref(null);
const currentPage = ref(1);
const nightMode = ref(false);
const zoomLevel = ref(100);
const showTwoPages = ref(true);
const highlightColor = ref('yellow');
const showHighlightTools = ref(false);
const loading = ref(false);
const pageVerses = ref({});

// Calculate actual Quran page number
const getActualPageNumber = (juzNumber, pageWithinJuz) => {
    const startPage = juzPageStart[juzNumber - 1];
    return startPage + pageWithinJuz - 1;
};

// Fetch verses for a specific page
const fetchPageVerses = async (pageNumber) => {
    if (pageVerses.value[pageNumber]) return; // Already cached
    
    try {
        const response = await fetch(`https://api.quran.com/api/v4/quran/verses/uthmani?page_number=${pageNumber}`);
        const data = await response.json();
        pageVerses.value[pageNumber] = data.verses?.map(v => v.text_uthmani) || [];
    } catch (error) {
        console.error('Error fetching page:', error);
        pageVerses.value[pageNumber] = [];
    }
};

// Calculate which pages to show (two-page spread)
const displayPages = computed(() => {
    if (!selectedPara.value) return [];
    if (!showTwoPages.value) {
        return [currentPage.value];
    }
    // For two-page view, show current and next page
    const rightPage = currentPage.value + 1;
    return rightPage <= 20 ? [currentPage.value, rightPage] : [currentPage.value];
});

// Watch for page changes and fetch verses
watch([selectedPara, currentPage, displayPages], async () => {
    if (!selectedPara.value) return;
    
    loading.value = true;
    for (const pageNum of displayPages.value) {
        const actualPage = getActualPageNumber(selectedPara.value.number, pageNum);
        await fetchPageVerses(actualPage);
    }
    loading.value = false;
}, { immediate: true });

// Get verses for a specific display page
const getVersesForPage = (pageNum) => {
    if (!selectedPara.value) return [];
    const actualPage = getActualPageNumber(selectedPara.value.number, pageNum);
    return pageVerses.value[actualPage] || [];
};

const selectPara = (para) => {
    selectedPara.value = para;
    currentPage.value = 1;
};

const goToPage = (page) => {
    if (page >= 1 && page <= 20) {
        currentPage.value = page;
    }
};

const nextPages = () => {
    if (showTwoPages.value) {
        if (currentPage.value + 2 <= 20) {
            currentPage.value += 2;
        }
    } else {
        if (currentPage.value < 20) {
            currentPage.value++;
        }
    }
};

const prevPages = () => {
    if (showTwoPages.value) {
        if (currentPage.value - 2 >= 1) {
            currentPage.value -= 2;
        } else {
            currentPage.value = 1;
        }
    } else {
        if (currentPage.value > 1) {
            currentPage.value--;
        }
    }
};

const zoomIn = () => {
    if (zoomLevel.value < 150) zoomLevel.value += 10;
};

const zoomOut = () => {
    if (zoomLevel.value > 70) zoomLevel.value -= 10;
};

// Keyboard navigation
const handleKeydown = (e) => {
    if (e.key === 'ArrowRight' || e.key === 'ArrowDown') {
        nextPages();
    } else if (e.key === 'ArrowLeft' || e.key === 'ArrowUp') {
        prevPages();
    }
};
</script>

<template>
    <Head title="Hifz Quran Reader" />

    <StudentLayout>
        <div class="min-h-screen" :class="nightMode ? 'bg-slate-900' : 'bg-gradient-to-b from-amber-50 to-orange-50'">
            <!-- Top Header Bar -->
            <div :class="[
                'sticky top-0 z-20 border-b shadow-sm',
                nightMode ? 'bg-slate-800 border-slate-700' : 'bg-white/95 backdrop-blur-sm border-amber-200'
            ]">
                <div class="max-w-7xl mx-auto px-4 py-3">
                    <div class="flex items-center justify-between">
                        <!-- Logo & Title -->
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-amber-500 to-orange-600 flex items-center justify-center text-white font-bold shadow-lg">
                                📖
                            </div>
                            <div>
                                <h1 :class="['text-lg font-bold', nightMode ? 'text-white' : 'text-gray-900']">
                                    Hifz Quran Reader
                                </h1>
                                <p :class="['text-xs', nightMode ? 'text-slate-400' : 'text-gray-500']">
                                    Memorization & Study • 30 Paras
                                </p>
                            </div>
                        </div>

                        <!-- Controls -->
                        <div class="flex items-center gap-2">
                            <!-- Search -->
                            <div :class="[
                                'hidden md:flex items-center gap-2 px-3 py-2 rounded-xl border',
                                nightMode ? 'bg-slate-700 border-slate-600' : 'bg-gray-50 border-gray-200'
                            ]">
                                <span class="text-gray-400">🔍</span>
                                <input 
                                    type="text" 
                                    placeholder="Search Quran..." 
                                    :class="[
                                        'bg-transparent text-sm w-40 focus:outline-none',
                                        nightMode ? 'text-white placeholder-slate-400' : 'text-gray-700'
                                    ]"
                                />
                            </div>

                            <!-- Bookmark -->
                            <button :class="[
                                'p-2 rounded-lg transition-colors',
                                nightMode ? 'hover:bg-slate-700 text-slate-300' : 'hover:bg-gray-100'
                            ]">
                                🔖
                            </button>

                            <!-- Night Mode Toggle -->
                            <button 
                                @click="nightMode = !nightMode"
                                :class="[
                                    'flex items-center gap-2 px-3 py-2 rounded-xl font-medium text-sm transition-all',
                                    nightMode 
                                        ? 'bg-amber-500 text-white' 
                                        : 'bg-slate-800 text-white'
                                ]"
                            >
                                {{ nightMode ? '☀️ Light' : '🌙 Night' }}
                            </button>

                            <!-- Language -->
                            <button :class="[
                                'px-3 py-2 rounded-xl font-medium text-sm',
                                nightMode ? 'bg-slate-700 text-white' : 'bg-gray-100 text-gray-700'
                            ]">
                                العربية
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="flex">
                <!-- Sidebar - Para List -->
                <div :class="[
                    'w-72 min-h-[calc(100vh-4rem)] border-r overflow-y-auto',
                    nightMode ? 'bg-slate-800 border-slate-700' : 'bg-white border-amber-200'
                ]">
                    <div class="p-4">
                        <h3 :class="['font-bold mb-4', nightMode ? 'text-white' : 'text-gray-900']">
                            📚 Select Para (Juz)
                        </h3>
                        
                        <div class="space-y-2">
                            <button
                                v-for="para in paras"
                                :key="para.number"
                                @click="selectPara(para)"
                                :class="[
                                    'w-full flex items-center gap-3 p-3 rounded-xl transition-all text-left',
                                    selectedPara?.number === para.number
                                        ? nightMode 
                                            ? 'bg-amber-500/20 border border-amber-500/50'
                                            : 'bg-amber-100 border border-amber-300'
                                        : nightMode
                                            ? 'hover:bg-slate-700'
                                            : 'hover:bg-amber-50'
                                ]"
                            >
                                <span :class="[
                                    'w-10 h-10 rounded-xl flex items-center justify-center font-bold text-sm shadow',
                                    selectedPara?.number === para.number
                                        ? 'bg-gradient-to-br from-amber-500 to-orange-600 text-white'
                                        : nightMode
                                            ? 'bg-slate-700 text-slate-300'
                                            : 'bg-amber-100 text-amber-700'
                                ]">
                                    {{ para.number }}
                                </span>
                                <div class="flex-1 min-w-0">
                                    <p :class="['font-medium truncate', nightMode ? 'text-white' : 'text-gray-900']">
                                        {{ para.name }}
                                    </p>
                                    <p :class="['text-xs', nightMode ? 'text-slate-400' : 'text-gray-500']">
                                        20 Pages
                                    </p>
                                </div>
                                <span :class="['text-lg', nightMode ? 'text-amber-400' : 'text-amber-700']">
                                    {{ para.nameArabic }}
                                </span>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Main Content Area -->
                <div class="flex-1 p-6" @keydown="handleKeydown" tabindex="0">
                    <!-- No Para Selected State -->
                    <div v-if="!selectedPara" class="flex items-center justify-center h-[calc(100vh-12rem)]">
                        <div class="text-center">
                            <div class="text-8xl mb-6 animate-pulse">📖</div>
                            <h3 :class="['text-2xl font-bold mb-2', nightMode ? 'text-white' : 'text-gray-900']">
                                Select a Para
                            </h3>
                            <p :class="nightMode ? 'text-slate-400' : 'text-gray-500'">
                                Choose a Para (Juz) from the left to start reading
                            </p>
                        </div>
                    </div>

                    <!-- Mushaf View -->
                    <div v-else>
                        <!-- Para Header & Tools -->
                        <div class="flex items-center justify-between mb-4">
                            <div class="flex items-center gap-3">
                                <button 
                                    @click="selectedPara = null"
                                    :class="[
                                        'px-3 py-2 rounded-lg text-sm font-medium flex items-center gap-2',
                                        nightMode ? 'bg-slate-700 text-white hover:bg-slate-600' : 'bg-white text-gray-700 hover:bg-gray-50 shadow'
                                    ]"
                                >
                                    ← Back
                                </button>
                                <h2 :class="['text-xl font-bold', nightMode ? 'text-white' : 'text-gray-900']">
                                    📖 Para {{ selectedPara.number }} - {{ selectedPara.name }}
                                </h2>
                            </div>

                            <!-- Tool Bar -->
                            <div class="flex items-center gap-2">
                                <!-- Highlight Tools -->
                                <button 
                                    @click="showHighlightTools = !showHighlightTools"
                                    :class="[
                                        'px-3 py-2 rounded-lg text-sm font-medium flex items-center gap-2',
                                        nightMode ? 'bg-slate-700 text-white' : 'bg-white text-gray-700 shadow'
                                    ]"
                                >
                                    ✏️ Highlight
                                </button>

                                <!-- View Toggle -->
                                <button 
                                    @click="showTwoPages = !showTwoPages"
                                    :class="[
                                        'px-3 py-2 rounded-lg text-sm font-medium',
                                        nightMode ? 'bg-slate-700 text-white' : 'bg-white text-gray-700 shadow'
                                    ]"
                                >
                                    {{ showTwoPages ? '📖 Two Pages' : '📄 Single' }}
                                </button>

                                <!-- Zoom Controls -->
                                <div :class="[
                                    'flex items-center gap-1 px-2 py-1 rounded-lg',
                                    nightMode ? 'bg-slate-700' : 'bg-white shadow'
                                ]">
                                    <button @click="zoomOut" class="p-1 hover:bg-gray-100 dark:hover:bg-slate-600 rounded">➖</button>
                                    <span :class="['text-sm font-medium w-12 text-center', nightMode ? 'text-white' : 'text-gray-700']">
                                        {{ zoomLevel }}%
                                    </span>
                                    <button @click="zoomIn" class="p-1 hover:bg-gray-100 dark:hover:bg-slate-600 rounded">➕</button>
                                </div>
                            </div>
                        </div>

                        <!-- Highlight Color Picker -->
                        <div v-if="showHighlightTools" :class="[
                            'flex items-center gap-4 mb-4 p-3 rounded-xl',
                            nightMode ? 'bg-slate-700' : 'bg-white shadow'
                        ]">
                            <span :class="['text-sm font-medium', nightMode ? 'text-white' : 'text-gray-700']">✏️ Highlight</span>
                            <span :class="['text-sm', nightMode ? 'text-slate-300' : 'text-gray-500']">✕ Remove</span>
                            <span :class="['text-sm', nightMode ? 'text-slate-400' : 'text-gray-500']">Select highlighter color:</span>
                            <div class="flex gap-2">
                                <button 
                                    v-for="color in ['yellow', 'green', 'blue', 'pink', 'orange']"
                                    :key="color"
                                    @click="highlightColor = color"
                                    :class="[
                                        'w-6 h-6 rounded-full border-2 transition-transform',
                                        highlightColor === color ? 'scale-125 border-gray-800' : 'border-transparent',
                                        {
                                            'bg-yellow-400': color === 'yellow',
                                            'bg-green-400': color === 'green',
                                            'bg-blue-400': color === 'blue',
                                            'bg-pink-400': color === 'pink',
                                            'bg-orange-400': color === 'orange',
                                        }
                                    ]"
                                ></button>
                            </div>
                        </div>

                        <!-- Mushaf Book View -->
                        <div class="flex justify-center">
                            <div 
                                class="relative"
                                :style="{ transform: `scale(${zoomLevel / 100})`, transformOrigin: 'top center' }"
                            >
                                <div :class="[
                                    'flex gap-4 p-6 rounded-2xl shadow-2xl',
                                    nightMode ? 'bg-slate-800' : 'bg-amber-100'
                                ]">
                                    <!-- Page(s) -->
                                    <div 
                                        v-for="pageNum in displayPages"
                                        :key="pageNum"
                                        :class="[
                                            'w-[350px] h-[500px] rounded-lg shadow-inner flex flex-col overflow-hidden',
                                            nightMode 
                                                ? 'bg-slate-900 border-2 border-slate-700' 
                                                : 'bg-[#FDF8E8] border-4 border-amber-300'
                                        ]"
                                    >
                                        <!-- Page Header -->
                                        <div :class="[
                                            'px-4 py-2 border-b text-center',
                                            nightMode ? 'border-slate-700 bg-slate-800' : 'border-amber-200 bg-amber-50'
                                        ]">
                                            <span :class="['text-sm font-medium', nightMode ? 'text-amber-400' : 'text-amber-700']">
                                                Para {{ selectedPara.number }} • Page {{ pageNum }}
                                            </span>
                                        </div>

                                        <!-- Page Content (Mushaf Style) -->
                                        <div class="flex-1 p-4 overflow-y-auto">
                                            <!-- Loading State -->
                                            <div v-if="loading" class="flex items-center justify-center h-full">
                                                <div class="animate-spin w-8 h-8 border-4 border-amber-500 border-t-transparent rounded-full"></div>
                                            </div>
                                            
                                            <!-- Verses Content -->
                                            <div v-else :class="[
                                                'border-2 rounded-lg p-4 h-full',
                                                nightMode ? 'border-slate-600' : 'border-amber-300'
                                            ]">
                                                <!-- Page Indicator -->
                                                <div class="text-center mb-3">
                                                    <span :class="[
                                                        'text-xs font-medium px-2 py-1 rounded-full',
                                                        nightMode ? 'bg-amber-500/20 text-amber-400' : 'bg-amber-200 text-amber-800'
                                                    ]">
                                                        পারা {{ selectedPara.number }} • পৃষ্ঠা {{ pageNum }}
                                                    </span>
                                                </div>
                                                
                                                <!-- Dynamic Arabic Quran Text from API -->
                                                <div 
                                                    class="quran-text"
                                                    :class="nightMode ? 'text-amber-100' : 'text-gray-900'"
                                                >
                                                    <span 
                                                        v-for="(verse, idx) in getVersesForPage(pageNum)" 
                                                        :key="idx"
                                                        class="inline"
                                                    >
                                                        {{ verse }}
                                                        <span class="text-amber-600 dark:text-amber-400 text-sm mx-0.5">۝</span>
                                                    </span>
                                                    
                                                    <!-- No verses fallback -->
                                                    <p v-if="!getVersesForPage(pageNum).length" class="text-center text-gray-400 font-sans">
                                                        ⏳ Loading verses...
                                                    </p>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Page Number -->
                                        <div :class="[
                                            'px-4 py-2 text-center',
                                            nightMode ? 'bg-slate-800' : 'bg-amber-50'
                                        ]">
                                            <span :class="[
                                                'inline-flex items-center justify-center w-8 h-8 rounded-full text-sm font-bold',
                                                nightMode ? 'bg-amber-500/20 text-amber-400' : 'bg-amber-200 text-amber-800'
                                            ]">
                                                {{ pageNum }}
                                            </span>
                                        </div>
                                    </div>
                                </div>

                                <!-- Book Spine Effect -->
                                <div v-if="showTwoPages && displayPages.length === 2" :class="[
                                    'absolute top-6 bottom-6 left-1/2 w-2 -translate-x-1/2',
                                    nightMode ? 'bg-gradient-to-r from-slate-700 via-slate-600 to-slate-700' : 'bg-gradient-to-r from-amber-300 via-amber-400 to-amber-300'
                                ]"></div>
                            </div>
                        </div>

                        <!-- Navigation Controls -->
                        <div :class="[
                            'fixed bottom-6 left-1/2 -translate-x-1/2 flex items-center gap-4 px-6 py-3 rounded-2xl shadow-2xl',
                            nightMode ? 'bg-slate-800 border border-slate-700' : 'bg-white border border-gray-200'
                        ]">
                            <button 
                                @click="prevPages"
                                :disabled="currentPage <= 1"
                                :class="[
                                    'px-4 py-2 rounded-lg font-medium transition-colors flex items-center gap-2',
                                    currentPage <= 1 
                                        ? 'opacity-50 cursor-not-allowed' 
                                        : nightMode 
                                            ? 'hover:bg-slate-700 text-white' 
                                            : 'hover:bg-gray-100 text-gray-700'
                                ]"
                            >
                                ‹ Previous
                            </button>

                            <!-- Juz Selector -->
                            <select 
                                v-model="selectedPara"
                                :class="[
                                    'px-3 py-2 rounded-lg border text-sm',
                                    nightMode ? 'bg-slate-700 border-slate-600 text-white' : 'bg-gray-50 border-gray-200'
                                ]"
                            >
                                <option v-for="para in paras" :key="para.number" :value="para">
                                    Juz {{ para.number }}
                                </option>
                            </select>

                            <!-- Page Input -->
                            <div class="flex items-center gap-2">
                                <span :class="['text-sm', nightMode ? 'text-slate-400' : 'text-gray-500']">🔍</span>
                                <span :class="['text-sm', nightMode ? 'text-slate-400' : 'text-gray-500']">Page</span>
                                <input 
                                    type="number" 
                                    :value="currentPage"
                                    @change="goToPage(parseInt($event.target.value))"
                                    min="1"
                                    max="20"
                                    :class="[
                                        'w-16 px-2 py-1 rounded-lg border text-center text-sm',
                                        nightMode ? 'bg-slate-700 border-slate-600 text-white' : 'bg-gray-50 border-gray-200'
                                    ]"
                                />
                                <button 
                                    :class="[
                                        'px-3 py-1 rounded-lg text-sm font-medium',
                                        nightMode ? 'bg-amber-500 text-white' : 'bg-amber-500 text-white'
                                    ]"
                                >
                                    Go
                                </button>
                            </div>

                            <!-- Current Page Display -->
                            <div :class="['text-sm', nightMode ? 'text-slate-300' : 'text-gray-600']">
                                <span class="font-medium">Current Page</span>
                                <br>
                                <span :class="['font-bold', nightMode ? 'text-amber-400' : 'text-amber-600']">
                                    {{ currentPage }} - {{ Math.min(currentPage + 1, 20) }}
                                </span>
                            </div>

                            <button 
                                @click="nextPages"
                                :disabled="currentPage >= 19"
                                :class="[
                                    'px-4 py-2 rounded-lg font-medium transition-colors flex items-center gap-2',
                                    currentPage >= 19 
                                        ? 'opacity-50 cursor-not-allowed' 
                                        : nightMode 
                                            ? 'hover:bg-slate-700 text-white' 
                                            : 'hover:bg-gray-100 text-gray-700'
                                ]"
                            >
                                Next ›
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </StudentLayout>
</template>

<style>
/* Import professional Quran fonts */
@import url('https://fonts.googleapis.com/css2?family=Amiri+Quran&family=Scheherazade+New:wght@400;700&display=swap');

.font-arabic {
    font-family: 'Amiri Quran', 'Scheherazade New', 'KFGQPC Uthmanic Script HAFS', 'Traditional Arabic', 'Noto Naskh Arabic', serif;
    font-feature-settings: "liga" 1, "clig" 1;
    text-rendering: optimizeLegibility;
    -webkit-font-smoothing: antialiased;
}

/* Quran verse styling for proper display */
.quran-text {
    font-family: 'Amiri Quran', 'Scheherazade New', serif;
    font-size: 1.35rem;
    line-height: 2.1;
    letter-spacing: 0;
    word-spacing: 2px;
    text-align: justify;
    direction: rtl;
}

/* Verse number styling */
.verse-number {
    font-family: 'Amiri Quran', serif;
    font-size: 0.9em;
    margin: 0 4px;
}
</style>
