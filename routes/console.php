<?php

\Illuminate\Support\Facades\Schedule::command('bookings:expire')->everyMinute()->withoutOverlapping()->onOneServer();
\Illuminate\Support\Facades\Schedule::command('payments:reconcile')->everyFiveMinutes()->withoutOverlapping()->onOneServer();

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');
