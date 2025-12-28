<x-mail::message>
    # 🎉 Welcome to QuranLearn!

    Assalamu Alaikum **{{ $userName }}**,

    Alhamdulillah! Your account has been created successfully. We're thrilled to have you join our community of Quran
    learners! 🌟

    ---

    ## 🚀 Get Started

    Here's what you can do next:

    <x-mail::table>
        | Step | Action |
        |:-----|:-------|
        | 1️⃣ | Browse our courses and find the perfect one for you |
        | 2️⃣ | Set up your learning schedule |
        | 3️⃣ | Meet your teacher and start learning! |
    </x-mail::table>

    ---

    <x-mail::button :url="url('/courses')" color="success">
        📚 Browse Courses
    </x-mail::button>

    ---

    ## ✨ What You'll Learn

    - 📖 **Quran Reading** - Perfect your recitation
    - 🎵 **Tajweed Rules** - Master the art of Quran pronunciation
    - 🧠 **Hifz Program** - Memorize the Holy Quran
    - 🗣️ **Arabic Language** - Understand what you recite

    ---

    <x-mail::panel>
        ### 💬 Need Help?

        Our support team is here for you 24/7. Just reply to this email or visit our support center.
    </x-mail::panel>

    May Allah bless your journey to learning His words! 🤲

    Jazakallah Khair,<br>
    **{{ config('app.name') }}** Team

    <x-mail::subcopy>
        Your login email: {{ $email }}
    </x-mail::subcopy>
</x-mail::message>