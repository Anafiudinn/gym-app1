<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Helpers\WhatsappHelper; // Jangan lupa import Helper kamu!

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// TAMBAHKAN INI:
Route::get('/wa-status', function () {
    return response()->json([
        'connected' => WhatsappHelper::checkStatus()
    ]);
});