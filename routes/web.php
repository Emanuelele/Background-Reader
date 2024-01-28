<?php

use App\Http\Controllers\ProfileController;

use App\Http\Controllers\DiscordLogin;
use App\Http\Controllers\BackgroundController;
use Illuminate\Support\Facades\Route;

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
Route::middleware('auth')->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');
    Route::get('backgroundget/{type}', [BackgroundController::class, 'getBackgroundFromType'])->name('background.get');
    Route::get('getbackground', [BackgroundController::class, 'getAllBackground'])->name('background.getall');
    Route::get('showmoreinfo', [BackgroundController::class, 'getDiscordUserInfo'])->name('background.info');
    Route::get('showmoreinfo/{id}', [BackgroundController::class, 'getDiscordUserInfo']);
    Route::patch('updatebackground', [BackgroundController::class, 'updateBackground'])->name('background.update');
    Route::delete('deletebackground', [BackgroundController::class, 'deleteBackground'])->name('background.delete');
});

Route::middleware('guest')->group(function () {
    Route::get('login', [DiscordLogin::class, 'index'])->name('login');
    Route::get('discordredirect', [DiscordLogin::class, 'redirectToDiscord'])->name('login.handler');
    Route::get('discordlogin', [DiscordLogin::class, 'login']);
});

require __DIR__.'/auth.php';
