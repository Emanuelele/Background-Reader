<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BackgroundController;
use App\Http\Middleware\IpFilterMiddleware;
use App\Http\Controllers\Api\LogController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::middleware('api', 'ip.filter')->group(function () {
    Route::post('/newbackground', [BackgroundController::class, 'newBackgroundApi']);
    Route::prefix('logs')->group(function () {
        Route::post('/', [LogController::class, 'store']);
    });
});