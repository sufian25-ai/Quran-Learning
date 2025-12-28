<x-mail::message>
    # 🎤 Your Recitation Has Been Reviewed!

    Assalamu Alaikum **{{ $userName }}**,

    Great news! Your teacher has reviewed your recitation of **{{ $surahName }}** (Ayahs {{ $ayahRange }}).

    ---

    ## ⭐ Overall Rating

    @for ($i = 1; $i <= 5; $i++)
        @if ($i <= $overallRating)
            ⭐
        @else
            ☆
        @endif
    @endfor

    ---

    ## 📊 Detailed Scores

    <x-mail::table>
        | Skill | Score |
        |:------|------:|
        | 🗣️ Pronunciation | {{ $pronunciationScore }}% |
        | 📖 Tajweed | {{ $tajweedScore }}% |
        | 💨 Fluency | {{ $fluencyScore }}% |
    </x-mail::table>

    ---

    ## 💬 Teacher's Feedback

    > {{ $feedbackText }}
    >
    > — **{{ $teacherName }}**

    ---

    <x-mail::button :url="url('/recitations')" color="success">
        📖 View All Recitations
    </x-mail::button>

    Keep practicing! Every recitation brings you closer to perfection. 🌟

    Jazakallah Khair,<br>
    **{{ config('app.name') }}** Team
</x-mail::message>