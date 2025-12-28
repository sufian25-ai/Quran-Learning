<x-mail::message>
    # 📊 Your Weekly Progress Report

    Assalamu Alaikum **{{ $userName }}**,

    Here's a summary of your Quran learning progress this week. MashAllah, you're doing great! 🌟

    ---

    ## 📈 This Week's Stats

    <x-mail::table>
        | Activity | Progress |
        |:---------|:--------:|
        | 📖 Ayahs Read | {{ $ayahsRead }} |
        | 🧠 Ayahs Memorized | {{ $ayahsMemorized }} |
        | ✅ Surahs Completed | {{ $surahsCompleted }} |
        | 🎤 Recitations Submitted | {{ $recitationsSubmitted }} |
        | ⏱️ Time Spent | {{ $timeSpent }} mins |
    </x-mail::table>

    ---

    ## 🏆 Your Achievements

    | | |
    |:--|:--|
    | 🔥 **Current Streak** | {{ $currentStreak }} days |
    | ⭐ **Total Points** | {{ $totalPoints }} |

    ---

    <x-mail::panel>
        ### 💡 Tip of the Week

        *"The best among you are those who learn the Quran and teach it."*
        — Prophet Muhammad ﷺ (Sahih Bukhari)
    </x-mail::panel>

    ---

    <x-mail::button :url="url('/progress')" color="success">
        📊 View Full Progress
    </x-mail::button>

    Keep up the excellent work! May Allah make your journey easy. 🤲

    Jazakallah Khair,<br>
    **{{ config('app.name') }}** Team
</x-mail::message>