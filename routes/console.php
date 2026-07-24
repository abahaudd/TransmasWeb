<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;
use Spatie\Health\Models\HealthCheckResultHistoryItem;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Foundation maintenance schedule (run `php artisan schedule:work` locally,
// or a cron entry for `php artisan schedule:run` in production).
Schedule::command('activitylog:clean')->daily()->at('01:00');
Schedule::command('backup:clean')->daily()->at('01:30');
Schedule::command('backup:run')->daily()->at('02:00');
Schedule::command('health:check')->everyFiveMinutes();
Schedule::command('model:prune', ['--model' => [HealthCheckResultHistoryItem::class]])->daily();
