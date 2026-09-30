<?php

use App\Http\Controllers\Website\AdmissionController;
use App\Http\Controllers\Website\ContactController;
use App\Http\Controllers\Website\HomeController;
use Illuminate\Support\Facades\Route;

Route::controller(HomeController::class)->group(function () {
    Route::get('/', 'index')->name('home');
    Route::get('/about', 'about')->name('about');
    Route::get('/contact', 'contact')->name('contact');
    Route::get('/news', 'news')->name('news');
    Route::get('/events', 'events')->name('events');
    Route::get('/admission', 'admission')->name('admission');
});

Route::post('/contact', [ContactController::class, 'store'])
    ->middleware('throttle:5,1')
    ->name('contact.store');

Route::post('/admission', [AdmissionController::class, 'store'])
    ->middleware('throttle:5,1')
    ->name('admission.store');
