<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

//vk
Schedule::command('command:get-post-from-vk')->hourly()->runInBackground()->withoutOverlapping();
Schedule::command('command:delete-old-post')->everyTwoHours()->runInBackground()->withoutOverlapping();
Schedule::command('command:publish-anime-queue')->hourly()->runInBackground()->withoutOverlapping();
Schedule::command('command:publish-anime2-queue')->hourly()->runInBackground()->withoutOverlapping();
Schedule::command('command:publish-sexy-queue')->everyTwoHours()->runInBackground()->withoutOverlapping();

//tumblr 
Schedule::command('command:queue-anime-photo-tumblr')->everyThreeHours()->runInBackground()->withoutOverlapping();
Schedule::command('app:get-post-from-tumblr-anime')->hourly()->runInBackground()->withoutOverlapping();

//telegram
Schedule::command('app:get-post-from-telegramm-sexy')->hourly()->runInBackground()->withoutOverlapping();
Schedule::command('app:get-post-from-telegramm-sexy-video')->hourly()->runInBackground()->withoutOverlapping();
Schedule::command('app:get-post-photo-anime')->hourly()->runInBackground()->withoutOverlapping();

//
Schedule::command('app:chech-is-file')->daily()->runInBackground()->withoutOverlapping();

//advert
Schedule::command('app:create-sexy-ads')->daily()->runInBackground()->withoutOverlapping();
Schedule::command('app:create-anime-ads')->daily()->runInBackground()->withoutOverlapping();
Schedule::command('app:create-anime-addcit-advert')->everySixHours()->runInBackground()->withoutOverlapping();
Schedule::command('app:create-advert-post')->everySixHours()->runInBackground()->withoutOverlapping();
//Schedule::command('app:create-advert-tix-list-button')->daily();
//$schedule->command('command:create-advert-list-button')->daily();

//public post type telegram
Schedule::command('app:queue-sexy-video-telegramm')->everyTenMinutes()->runInBackground()->withoutOverlapping();
Schedule::command('app:queue-anime-photo-telegram')->everyFifteenMinutes()->runInBackground()->withoutOverlapping();
Schedule::command('app:queue-anime-addcit-photo-telegram')->everyTenMinutes()->runInBackground()->withoutOverlapping();
Schedule::command('app:queue-sexy-photo-telegram')->everyFifteenMinutes()->runInBackground()->withoutOverlapping();


Schedule::command('app:delete-ads')->hourly()->runInBackground()->withoutOverlapping();
