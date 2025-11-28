<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Laravel\Fortify\Features;
use Illuminate\Support\Facades\Auth;

use App\Http\Controllers\telegram\TelegramController;
use App\Http\Controllers\dashboard\DashboardController;
use App\Http\Controllers\group\GroupController;
use App\Http\Controllers\vk\VkController;
use App\Http\Controllers\queue\VkController as QueueVkController;
use App\Http\Controllers\queue\QueueController;
use App\Http\Controllers\tumblr\TumblrController;

Auth::routes();

Route::get('/', function () {
    return redirect('dashboard');
});
require __DIR__ . '/settings.php';

Route::group(['prefix' => 'dashboard', 'middleware' => 'auth',], function () {
    Route::get('', [DashboardController::class, 'index'])->name('main');;
});


Route::group(['prefix' => 'vk', 'middleware' => 'auth',], function () {
    Route::get('anime-photo-all', [VkController::class, 'vkAnimeAll'])->name('vk.anime-photo-all');
    Route::get('sexy-photo-all', [VkController::class, 'sexyPhotoAll']);
    Route::post('vk-anime-publish', [VkController::class, 'vkAnimePublish'])->name('vk.mass.anime.photo.publish');
    Route::post('vk-sexy-publish', [VkController::class, 'vkSexyPublish'])->name('vk.mass.sexy-photo.publish');
    Route::post('vk-anime-set-queue', [VkController::class, 'vkAnimeSetQueue'])->name('vk.mass.anime-photo.set-queue');

    Route::post('post-hidden', [VkController::class, 'postHidden'])->name('vk.hidden');
});

Route::group(['prefix' => 'tumblr', 'middleware' => 'auth',], function () {
    Route::get('anime-photo-all', [TumblrController::class, 'animePhotoAll'])->name('tumblr.anime-photo-all');
    Route::get('sexy-photo-all', [TumblrController::class, 'sexyPhotoAll'])->name('tumblr.sexy-photo-all');
});

Route::group(['prefix' => 'queue', 'middleware' => 'auth',], function () {
    Route::post('set', [QueueController::class, 'set'])->name('queue.set-queue');
    Route::post('unset', [QueueController::class, 'unset'])->name('queue.unset-queue');
    Route::get('anime-photo-vk', [QueueVkController::class, 'vkAnime']);
    Route::get('sexy-photo-vk', [QueueVkController::class, 'vkSexy']);
});

Route::group(['prefix' => 'telegram', 'middleware' => 'auth',], function () {
    Route::get('anime-photo-all', [TelegramController::class, 'animePhotoAll']);
    Route::get('sexy-photo-all', [TelegramController::class, 'sexyPhotoAll']);
    Route::get('sexy-video-all', [TelegramController::class, 'sexyVideoAll']);
    Route::post('anime-photo-telegram-publish', [TelegramController::class, 'animePhotoPublish'])->name('telegram.anime-photo');
    Route::post('sexy-photo-telegram-publish', [TelegramController::class, 'sexyPhotoPublish'])->name('telegram.sexy-photo');
    Route::post('sexy-video-telegram-publish', [TelegramController::class, 'sexyVideoPublish'])->name('telegram.sexy-video');
});

Route::group(['prefix' => 'group', 'middleware' => 'auth',], function () {
    Route::get('index', [GroupController::class, 'index']);
    Route::get('source/{id}', [GroupController::class, 'source'])->name('group.source');
    Route::post('source-store/{id}', [GroupController::class, 'sourceStore'])->name('group.source-store');

    Route::patch('update-status-source/{id}', [GroupController::class, 'updateStatusSource'])->name('group.update-status-source');
    Route::get('create', [GroupController::class, 'create']);
    Route::post('store', [GroupController::class, 'store'])->name('group.store');
    Route::patch('update-status/{id}', [GroupController::class, 'updateStatus'])->name('group.update-status');
    Route::post('delete/{id}', [GroupController::class, 'delete'])->name('group.delete');
    Route::post('delete-source/{id}', [GroupController::class, 'deleteSource'])->name('group.delete-source');
});

Route::view('home', 'home');
Route::view('test2', 'welcome');
