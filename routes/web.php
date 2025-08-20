<?php

use Vanguard\UserActivity\Http\Controllers\Web\ActivityController;
use Vanguard\UserActivity\Http\Controllers\Web\UserActivityController;

Route::group(['middleware' => ['auth', 'verified']], function () {

    Route::get('profile/activity', [UserActivityController::class, 'show'])->name('profile.activity');

    Route::get('activity', [ActivityController::class, 'index'])->name('activity.index')
        ->middleware('permission:users.activity');

    Route::get('activity/user/{user}/log', [UserActivityController::class, 'index'])->name('activity.user')
        ->middleware('permission:users.activity');
});
