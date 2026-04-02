<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

//vk
Schedule::command('command:get-post-from-vk')->hourly();
Schedule::command('command:delete-old-post')->everyTwoHours();
Schedule::command('command:publish-anime-queue')->hourly();
Schedule::command('command:publish-anime2-queue')->hourly()->sendOutputTo(storage_path('logs/publish-anime2-queue.log'));
Schedule::command('command:publish-sexy-queue')->everyTwoHours();

//tumblr 
Schedule::command('command:queue-anime-photo-tumblr')->everyThreeHours();
Schedule::command('app:get-post-from-tumblr-anime')->hourly();

//telegram
Schedule::command('app:get-post-from-telegramm-sexy')->hourly();
Schedule::command('app:get-post-from-telegramm-sexy-video')->hourly();
Schedule::command('app:get-post-photo-anime')->hourly();

//
Schedule::command('app:chech-is-file')->daily();

//advert
Schedule::command('app:create-sexy-ads')->daily();
Schedule::command('app:create-anime-ads')->daily();
Schedule::command('app:create-anime-addcit-advert')->everySixHours();
Schedule::command('app:create-advert-post')->everySixHours();
//Schedule::command('app:create-advert-tix-list-button')->daily();
//$schedule->command('command:create-advert-list-button')->daily();
Schedule::command('app:queue-sexy-video-telegramm')->everyTenMinutes();
Schedule::command('app:queue-anime-photo-telegram')->everyFifteenMinutes();
Schedule::command('app:queue-sexy-photo-telegram')->everyFifteenMinutes();

Schedule::command('app:delete-ads')->hourly();
