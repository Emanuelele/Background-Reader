<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Illuminate\Routing\Redirector;
use Illuminate\Support\Facades\DB;
use Socialite;
use Exception;

class DiscordLogin extends Controller {

    /**
     * Show the login view.
     *
     * @return View
     */
    public function index(): View {
        return view('login');
    }

    /**
     * Redirect the user to the Discord authentication page.
     *
     * @return RedirectResponse
     */
    public function redirectToDiscord(): RedirectResponse {
        return Socialite::driver('discord')->redirect();
    }

    /**
     * Handle the login process after authentication with Discord.
     *
     * @return RedirectResponse
     */
    public function login(): RedirectResponse {
        try {
            $user = Socialite::driver('discord')->stateless()->user();
            $avatarurl = Socialite::driver('discord')->stateless()->user()->getAvatar();
        } catch(Exception $e) {
            return redirect()->route('login')->withErrors('Errore durante l\'autenticazione con Discord.');
        }
        $auth = auth()->loginUsingId($user->id, true);
        if(!$auth) return redirect()->route('login')->withErrors('Accesso negato');
        DB::table('ws_users')->where('id', $user->id)->update([
            'avatar' => is_null($avatarurl) ? "https://cdn.discordapp.com/icons/1015976925367378040/a_8aab7490e9efb8cc53487de73e4521c7.webp?size=240" : $avatarurl,
            'username' => $user->user['global_name']
        ]);
        return redirect()->route('dashboard');
    }
}
