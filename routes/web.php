<?php

use App\Http\Controllers\VideoController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/videos', [VideoController::class, 'index'])->name('video.index');
Route::get('/video/upload', [VideoController::class, 'showUploadForm'])->name('video.upload.form');
Route::post('/video/upload', [VideoController::class, 'uploadVideo'])->name('video.upload');
Route::get('/video/play/{videoID}', [VideoController::class, 'playVideo'])->name('video.play');
