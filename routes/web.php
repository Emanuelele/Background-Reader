<?php

use App\Http\Controllers\ProfileController;

use App\Http\Controllers\DiscordLogin;
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
});

Route::middleware('guest')->group(function () {
    Route::get('login', [DiscordLogin::class, 'index'])->name('login');
    Route::get('discordredirect', [DiscordLogin::class, 'redirectToDiscord'])->name('login.handler');
    Route::get('discordlogin', [DiscordLogin::class, 'login']);
});

require __DIR__.'/auth.php';
