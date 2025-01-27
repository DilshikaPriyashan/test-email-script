<?php

use App\Http\Controllers\API\V1\ClientEmailSendController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::prefix('v1')->name('v1.')->group(function () {
    Route::get('{team}/send-email', ClientEmailSendController::class)->name('send-email');
});
