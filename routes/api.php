<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\telegram\BotController;


Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::group(['prefix' => 'telegramm-bot',], function () {
    Route::post('create-anime-advert', [BotController::class, 'createAnimeAdvert']);
    Route::post('create-sexy-advert', [BotController::class, 'createSexyAdvert']);
    Route::post('create-list-button-advert', [BotController::class, 'createListButtonAdvert']);

    Route::post('update-status-group', [BotController::class, 'updateStatusGroup']);
    Route::post('get-statistic', [BotController::class, 'getStatistic']);
    Route::get('get-status-group', [BotController::class, 'getStatusGroups']);
});