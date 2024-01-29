<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Exception;
use Illuminate\Support\Facades\DB;
use App\Models\Background;
use Illuminate\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Auth;

class BackgroundController extends Controller {
    
    /**
     * Create a new Background record based on the provided Request data.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function make(Request $request): JsonResponse {
        try{
            DB::beginTransaction();
            $validateData = $request->validate(Background::$rules);
            Background::create($validateData);
            DB::commit();
            return response()->json('Request ok', 200);
        } catch(Exception $e) {
            DB::rollBack();
            return response()->json('Bad request' . $e, 400);
        }
    }

    /**
     * Retrieve backgrounds of a specific type and render a view.
     *
     * @param string $type
     * @return View
     */
    public function getBackgroundFromType(string $type): View{
        $backgrounds = Background::where('type', $type)->get();
        return view('backgrounds', ['backgrounds' => $backgrounds, 'navVisualBackgroundType' => $type]);
    }

    /**
     * Retrieve all backgrounds and render a view.
     *
     * @return View
     */
    public function getAllBackground(): View{
        $backgrounds = Background::all();
        return view('backgrounds', ['backgrounds' => $backgrounds]);
    }

    /**
     * Delete a background based on the provided Request data.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function deleteBackground(Request $Request) {
        try {
            DB::beginTransaction();
            $background = Background::find($Request->background_id);
            if ($background) {
                $background->delete();
                DB::commit();
                return response()->json(['success' => 'Background eliminato con successo'], 200);
            }
            DB::rollBack();
            return response()->json(['error' => 'Background non trovato'], 404);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json(['error' => 'Errore nell\'eliminazione del background, riprova più tardi'], 500);
        }
    }

    /**
     * Update a background based on the provided Request data.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function updateBackground(Request $Request): JsonResponse {
        try { 
            DB::beginTransaction();
            $background = Background::find($Request->background_id);
            if ($background) {
                $data = $Request->validate(Background::$rules);
                $background->update($data);
                DB::commit();
                return response()->json(['success' => 'Background modificato con successo'], 200);
            }
            DB::rollBack();
            return response()->json(['error' => 'Background non trovato'], 404);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json(['error' => 'Errore nella modifica del Background, riprova più tardi'], 500);
        }
    }

    /**
     * Make a cURL request to the Discord API with the provided Discord ID.
     *
     * @param string $discordId
     * @return array
     */
    private function makeDiscordApiRequest(string $discordId): array {
        $apiUrl = "https://discord.com/api/v10/users/{$discordId}";
        $ch = curl_init($apiUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Authorization: Bot ' . env('DISCORD_BOT_TOKEN')]);
        $response = curl_exec($ch);

        if (!$response) {
            return [
                'error' => 'Errore nel server, riprova più tardi',
                'data' => [],
            ];
        }

        return json_decode($response, true);
    }

    /**
     * Get the full URL of the Discord user's avatar.
     *
     * @param string $discordId
     * @param string|null $avatarHash
     * @return string|null
     */
    private function getAvatarUrl(string $discordId, string $avatarHash): ?string {
        return is_null($avatarHash || $discordId) ? "https://cdn.discordapp.com/icons/1015976925367378040/a_8aab7490e9efb8cc53487de73e4521c7.webp?size=240" : 
            "https://cdn.discordapp.com/avatars/{$discordId}/{$avatarHash}.png";
    }

    /**
     * Get Discord user information based on the provided Discord ID.
     *
     * @param string $discordId
     * @return JsonResponse
     */
    public function getDiscordUserInfo(Request $Request): JsonResponse {
        $data = array_merge($this->makeDiscordApiRequest($Request->discord_id), $this->backgroundCountFromDiscordUserId($Request->discord_id));
        return response()->json(['success' => 'Informazioni caricate', 'data' => $data], 200);
    }

    /**
     * Get Discord user information based on the provided Discord ID.
     *
     * @param string $discordId
     * @return array
     */
    private function getDiscordUserInfoPv(string $discordId): array {
        return array_merge($this->makeDiscordApiRequest($discordId), $this->backgroundCountFromDiscordUserId($discordId));
    }

    /**
     * Get background counts based on Discord user ID.
     *
     * @param string $discord_id
     * @return array
     */
    private function backgroundCountFromDiscordUserId(string $discord_id): array {
        return [
            'isWhitelisted' => $this->isDiscorduserWhitelisted($discord_id),
            'isBanned' => $this->isDiscorduserBanned($discord_id),
            'new' => DB::table('backgrounds')->where('discord_id', $discord_id)->count(),
            'approved' => DB::table('backgrounds')->where('type', 'approved')->where('discord_id', $discord_id)->count(),
            'denied' => DB::table('backgrounds')->where('type', 'denied')->where('discord_id', $discord_id)->count(),
        ];
    }

    /**
     * Check if a Discord user is whitelisted.
     *
     * @param string $discord_id
     * @return bool
     */
    public function isDiscorduserWhitelisted(string $discord_id): bool {
        return false; //to-do
    }

    /**
     * Check if a Discord user is banned.
     *
     * @param string $discord_id
     * @return bool
     */
    public function isDiscorduserBanned(string $discord_id): bool {
        return false; //to-do
    }

    /**
     * Read the oldest new background and retrieve additional information.
     *
     * @return View
     */
    public function readBackground(): View {
        $background =  Background::where('type', 'new')->orderBy('created_at', 'asc')->first();
        $backgrounds =  Background::where('discord_id', $background->discord_id)->orderBy('created_at', 'asc');
        if(!$background) return view('readbackground', [
            'oldestNewBackground' => null,
            'additionalInfo' => null,
        ]);
        $additionalInfo = $this->getDiscordUserInfoPv($background->discord_id);
        return view('readbackground', [
            'background' => $background,
            'backgrounds' => $backgrounds,
            'additionalInfo' => $additionalInfo,
            'avatarUrl' => $this->getAvatarUrl($additionalInfo['id'], $additionalInfo['avatar']),
        ]);
    }

    public function sendDiscordWebhook($message, $type): void {
        if($type == "approved") $webhookUrl = 'https://discord.com/api/webhooks/1201365218371063878/jQiMRIk1hGJ5JkjrS2rdk0TgyFk1DyCvKk3jcLX08ZTZmKGTY9yibAM_HL-YzAN-X76d';
        else $webhookUrl = 'https://discord.com/api/webhooks/1201367735997841500/Fn1332abNEAlarsojxIHdoKIsBeu-yaMHuqRNMxzV-ywix8FO2aHIwCyrm3Bat0afUAc';
        $response = Http::post($webhookUrl, [
            'content' => $message,
        ]);
    }

    public function resultBackground(Request $Request): JsonResponse  {
        try {
            $background = Background::find($Request->background_id);
            if(!$background) return response()->json(['error' => 'Background non trovato'], 400);
            if($Request->result == "approved") $message = '<@'.$background->discord_id.'> Background approvato! Note: "'.$background->note.'". By <@'.Auth::user()->id.'>';
            else $message = '<@'.$background->discord_id.'> Background NON approvato! Note:"'.$background->note.'". By <@'.Auth::user()->id.'>';
            $this->sendDiscordWebhook($message, $Request->result);
            return response()->json(['success' => 'Background approvato con successo'], 200);
        } catch(Exception $e) {
            return response()->json(['error' => 'Errore nel server'.$e], 500);
        }
        
    }
}