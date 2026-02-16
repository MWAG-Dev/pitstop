<?php

use App\Models\Ticket;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Schedule::call(function () {
    Ticket::where('created_at', '<', now()->subDays(90))->delete();
})->dailyAt('02:15')->name('tickets:prune');
