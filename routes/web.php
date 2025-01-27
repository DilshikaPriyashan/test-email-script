<?php

use App\Http\Controllers\ClientRegistrationController;
use App\Http\Controllers\HomeController;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'index']);

Route::prefix('invitation')->name('invitation.')->group(function () {
    Route::get('{code}', [ClientRegistrationController::class, 'index'])->name('index');
    Route::post('/accept', [ClientRegistrationController::class, 'accept'])->name('accept');
});
