<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\LocaleVoiceCrudController;
use App\Http\Controllers\Api\VideoProcessingController;
Route::get('/', function () {
    return view('welcome');
});

Route::get('admin/createactor', function () {
    return view('createactor');
});

Route::get('admin/api/voices', [LocaleVoiceCrudController::class, 'getVoices']);


Route::get('videos/next-to-process', [VideoProcessingController::class, 'getNextVideo']);
