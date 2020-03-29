<?php

Route::get('/activity', 'ActivityController@index');
Route::get('/stats/activity', 'StatsController@show');
