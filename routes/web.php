<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Carbon;
Route::get('/', function () {
    return view('welcome');
});

Route::get('/test-time', function () {
    return Carbon::now()->toDateTimeString();
});

