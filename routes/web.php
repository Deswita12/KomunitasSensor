<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\GuideController;
use App\Http\Controllers\CommunityController;
use App\Http\Controllers\HelpRequestController;
use App\Http\Controllers\Api\SensorProxyController;
use Illuminate\Support\Facades\Route;

Route::get('/', [DashboardController::class, 'index'])->name('home');
Route::get('/data', [DashboardController::class, 'index'])->name('data.dashboard');
Route::get('/panduan', [GuideController::class, 'index'])->name('panduan');
Route::get('/komunitas', [CommunityController::class, 'index'])->name('komunitas');
Route::get('/komunitas/{post:slug}', [CommunityController::class, 'show'])->name('komunitas.show');

Route::post('/bantuan', [HelpRequestController::class, 'store'])->name('bantuan.store');
Route::get('/data/info', [DashboardController::class, 'info'])->name('data.info');
Route::get('/komunitas/testimoni/list', [CommunityController::class, 'testimonialsList'])->name('komunitas.testimoni.list');
// Route::post('/komunitas/testimoni/{testimonial}/like', [CommunityController::class, 'likeTestimonial'])->name('komunitas.testimoni.like');

// Proxy API — throttle sekali saja, prefix sekali saja
Route::prefix('api/proxy')->middleware('throttle:30,1')->group(function () {
    Route::get('/smart-citizen/{deviceId}', [SensorProxyController::class, 'smartCitizen']);
    Route::get('/bmkg', [SensorProxyController::class, 'bmkg']);
    Route::post('/ai-analysis', [SensorProxyController::class, 'aiAnalysis']);

});