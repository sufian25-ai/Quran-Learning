<x-mail::message>
    # ⏰ Class Starting Soon!

    Assalamu Alaikum **{{ $userName }}**,

    Your Quran class is starting in **{{ $startsIn }}**!

    ---

    ## 📚 Class Details

    | | |
    |:--|:--|
    | **Course** | {{ $classTitle }} |
    | **Teacher** | {{ $teacherName }} |
    | **Date** | {{ $scheduledDate }} |
    | **Time** | {{ $scheduledTime }} |

    ---

    <x-mail::button :url="$meetingLink" color="success">
        🎥 Join Class Now
    </x-mail::button>

    ### 📝 Quick Tips:
    - Make sure you have Wudu
    - Find a quiet place
    - Have your Quran ready
    - Test your microphone

    May Allah bless your learning journey! 🤲

    Jazakallah Khair,<br>
    **{{ config('app.name') }}** Team
</x-mail::message>