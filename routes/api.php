<?php

use Vanguard\UserActivity\Http\Controllers\Api\ActivityController;
use Vanguard\UserActivity\Http\Controllers\Api\StatsController;

Route::get('/activity', [ActivityController::class, 'index']);
Route::get('/stats/activity', [StatsController::class, 'show']);
