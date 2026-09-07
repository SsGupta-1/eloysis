<?php

use App\Http\Controllers\Website\ContactController;
use App\Http\Controllers\Website\HomeController;
use App\Http\Controllers\Website\AdmissionController;
use Illuminate\Support\Facades\Route;

Route::controller(HomeController::class)->group(function () {

    Route::get('/', 'index')->name('home');

    // Route::get('/contact', 'contact')->name('contact');

    Route::get('/news', 'news')->name('news');
    
    Route::get('/admission', 'admission')->name('admission');
    
});

Route::post('/contact', [ContactController::class, 'store'])->name('contact.store');
Route::post('/admission', [AdmissionController::class, 'store'])->name('admission.store');

// Route::post('/contact', function () {
//     return response()->json([
//         'status' => true,
//         'message' => 'Route Working'
//     ]);
// })->name('contact.store');
