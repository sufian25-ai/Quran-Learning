<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Schedule class reminders every 5 minutes
Schedule::command('reminders:classes --minutes=30')->everyFiveMinutes();
Schedule::command('reminders:classes --minutes=15')->everyFiveMinutes();

// Cleanup old closed chat conversations daily at 2am
Schedule::command('chats:cleanup --days=7')->dailyAt('02:00');

