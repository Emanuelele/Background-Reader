<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Exception;
use Illuminate\Support\Facades\DB;
use App\Models\Background;
use Illuminate\View\View;
use Illuminate\Http\JsonResponse;

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
        return view('newbackgrounds', ['backgrounds' => $backgrounds, 'navVisualBackgroundType' => $type]);
    }

    /**
     * Retrieve all backgrounds and render a view.
     *
     * @return View
     */
    public function getAllBackground(): View{
        $backgrounds = Background::all();
        return view('newbackgrounds', ['backgrounds' => $backgrounds]);
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
     * Get Discord user information based on the provided Discord ID.
     *
     * @param string $discordId
     * @return JsonResponse
     */
    public function getDiscordUserInfo(string $discordId): JsonResponse {
        $apiUrl = "https://discord.com/api/v10/users/{$discordId}";
        $ch = curl_init($apiUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Authorization: Bot ' . env('DISCORD_BOT_TOKEN'),]);
        $response = curl_exec($ch);
        if(!$response) return response()->json(['error' => 'Errore nel server, riprova più tardi', 'data' => []], 500);
        $data = array_merge(json_decode($response, true), $this->backgroundCountFromDiscordUserId($discordId));
        return response()->json(['success' => 'Informazioni caricate', 'data' => $data], 200);
    }

    /**
     * Get background counts based on Discord user ID.
     *
     * @param string $discord_id
     * @return array
     */
    public function backgroundCountFromDiscordUserId(string $discord_id): array {
        return [
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
}
