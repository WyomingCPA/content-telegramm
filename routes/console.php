<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

//vk
Schedule::command('command:get-vk-post')->hourly();
Schedule::command('command:delete-old-post')->everyTwoHours();
Schedule::command('command:publish-anime-queue')->hourly();
Schedule::command('command:publish-anime2-queue')->everyThreeHours();
Schedule::command('command:publish-sexy-queue')->everyTwoHours();
//support
Schedule::command('command:check-is-file')->daily();

//advert
Schedule::command('app:create-sexy-ads')->daily();
Schedule::command('app:create-anime-ads')->daily();
//$schedule->command('command:create-advert-list-button')->daily();

Schedule::command('app:delete-ads')->everyThreeHours();


