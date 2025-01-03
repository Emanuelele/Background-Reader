<?php

use App\Http\Controllers\ProfileController;

use App\Http\Controllers\DiscordLogin;
use App\Http\Controllers\DiscordController;
use App\Http\Controllers\BackgroundController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\View;
use App\Http\Controllers\Api\LogController;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('login');
});
Route::post('discord/tags/', [DiscordController::class, 'getDiscordTags']);
Route::middleware('auth')->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');
    Route::get('dashboardstats', [BackgroundController::class, 'getDashboardStats'])->name('dashboard.stats');
    
    Route::get('backgroundsget/{type?}', [BackgroundController::class, 'getBackgrounds'])->name('background.get');
    Route::get('readbackground', [BackgroundController::class, 'readBackground'])->name('background.read');
    Route::post('showmoreinfo', [BackgroundController::class, 'backgroundMoreInfo'])->name('background.info');
    Route::post('resultBackground', [BackgroundController::class, 'resultBackground'])->name('background.result');
    Route::patch('updatebackground', [BackgroundController::class, 'updateBackground'])->name('background.update');
    Route::post('downloadbackground', [BackgroundController::class, 'saveBackground'])->name('background.save');
    Route::get('/logs/{category?}', [LogController::class, 'index'])->name('logs.index');
    Route::get('/logs/discord/{category?}', [LogController::class, 'index'])->name('logs.discord.index');
    Route::get('background/{filename}', function ($filename) {
        return view('viewpdf', ['filename' => $filename]);
    })->name('background.view');
});

Route::middleware('auth', 'admin')->group(function () {
    Route::delete('deletebackground', [BackgroundController::class, 'deleteBackground'])->name('background.delete');
});

Route::middleware('guest')->group(function () {
    Route::get('login', [DiscordLogin::class, 'index'])->name('login');
    Route::get('discordredirect', [DiscordLogin::class, 'redirectToDiscord'])->name('login.handler');
    Route::get('discordlogin', [DiscordLogin::class, 'login']);
});

require __DIR__.'/auth.php';
