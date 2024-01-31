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
use GuzzleHttp\Client;
use Barryvdh\DomPDF\PDF;
use Dompdf\Dompdf;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Validator;

class BackgroundController extends Controller {
    /** BACKGROUND METHODS **/
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
    public function getBackgroundFromType(string $type): View {
        $backgrounds = Background::where('type', $type)->get();
        return view('backgrounds', ['backgrounds' => $backgrounds]);
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
    public function deleteBackground(Request $Request): JsonResponse {
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

    /** DISCORD METHODS **/

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
     * @return array
     */
    private function getDiscordUserInfo(string $background_id): array {
        $background = Background::find($background_id);
        return array_merge($this->makeDiscordApiRequest($background->discord_id), $this->backgroundInfoFromDiscordUserId($background_id));
    }

    /**
     * Get background counts based on Discord user ID.
     *
     * @param string $background_id
     * @return array
     */
    private function backgroundInfoFromDiscordUserId(string $background_id): array {
        $background = Background::find($background_id);
        return [
            'isWhitelisted' => $this->isDiscorduserWhitelisted($background->discord_id),
            'isBanned' => $this->isDiscorduserBanned($background->discord_id),
            'new' => DB::table('backgrounds')->where('discord_id', $background->discord_id)->count(),
            'approved' => DB::table('backgrounds')->where('type', 'approved')->where('discord_id', $background->discord_id)->count(),
            'denied' => DB::table('backgrounds')->where('type', 'denied')->where('discord_id', $background->discord_id)->count(),
            'note' => DB::table('backgrounds')->where('id', $background_id)->value('note'),
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

    /** ACTIONS METHODS **/
    /**
     * Read the oldest new background and retrieve additional information.
     *
     * @return View
     */
    public function readBackground(): View | RedirectResponse {
        $background =  Background::where('type', 'new')->orderBy('created_at', 'asc')->first();
        if(!$background) return redirect()->route('dashboard')->withErrors("Non ci sono background da leggere");
        $backgrounds =  Background::where('discord_id', $background->discord_id)->whereNotIn('id', [$background->id])->orderBy('created_at', 'asc')->get();
        $additionalInfo = $this->getDiscordUserInfo($background->id);
        return view('readbackground', [
            'background' => $background,
            'backgrounds' => $backgrounds,
            'additionalInfo' => $additionalInfo,
            'avatarUrl' => $this->getAvatarUrl($additionalInfo['id'], $additionalInfo['avatar']),
        ]);
    }

    /**
     * Display additional information about a specific background.
     *
     * @param \Illuminate\Http\Request $Request The incoming HTTP request containing the background_id parameter.
     * @return \Illuminate\View\View The view displaying additional information about the background.
     */
    public function backgroundMoreInfo(Request $Request): View {
        $background =  Background::find($Request->background_id);
        $backgrounds =  Background::where('discord_id', $background->discord_id)->whereNotIn('id', [$background->id])->orderBy('created_at', 'asc')->get();
        if(!$background) return view('readbackground', [
            'oldestNewBackground' => null,
            'additionalInfo' => null,
        ]);
        $additionalInfo = $this->getDiscordUserInfo($background->id);
        return view('readbackground', [
            'background' => $background,
            'backgrounds' => $backgrounds,
            'additionalInfo' => $additionalInfo,
            'avatarUrl' => $this->getAvatarUrl($additionalInfo['id'], $additionalInfo['avatar']),
        ]);
    }


    /** TO-FINISH AND IMPLEMENTING */
    public function sendDiscordWebhook($message): void {
        $webhookUrl = 'https://discord.com/api/webhooks/1201704436758753320/G8ppdeMi4V71qmtUC70ONyjL1Ax-t9wnvfd-ZdkhNlAoRQHdMXXIoPrW2TSwJrF9aNqz';
        $response = Http::post($webhookUrl, [
            'content' => $message,
        ]);
    }
    
    /**
     * Approve or disapprove a background and send a Discord webhook notification.
     *
     * @param \Illuminate\Http\Request $request The incoming HTTP request.
     * @return \Illuminate\Http\JsonResponse A JSON response indicating the success or error of the operation.
     */
    public function resultBackground(Request $Request): JsonResponse  {
        try {
            $background = Background::find($Request->background_id);
            if(!$background) return response()->json(['error' => 'Background non trovato'], 400);
            if($Request->result == "approved") $message = '<@'.$background->discord_id.'> Background approvato! Note: "'.$background->note.'". By <@'.Auth::user()->id.'>';
            else $message = '<@'.$background->discord_id.'> Background NON approvato! Note:"'.$background->note.'". By <@'.Auth::user()->id.'>';
            $this->sendDiscordWebhook($message);
            $background->reader = Auth::user()->id;
            $background->save();
            return response()->json(['success' => 'Background approvato con successo'], 200);
        } catch(Exception $e) {
            return response()->json(['error' => 'Errore nel server'.$e], 500);
        }
        
    }

    /**
     * Extracts the file ID from a Google Docs link.
     *
     * @param string $link The link from which to extract the file ID.
     * @return string|null The file ID, or null if not found.
     */
    private function extractFileIdFromLink($link): ?string {
        preg_match('/\/document\/d\/([^\/]+)\//', $link, $matches);
        if (isset($matches[1])) return $matches[1];
        return null;
    }

    /**
     * Check if a Google Docs link is accessible and public.
     *
     * @param string $link The Google Docs link to check.
     * @return bool True if the link is accessible and public, false otherwise.
     */
    private function isGoogleDocLinkPublic(string $link): bool {
        try {
            $apiKey = env('GOOGLE_API_KEY');
            $fileId = $this->extractFileIdFromLink($link);
            $exportUrl = "https://www.googleapis.com/drive/v3/files/{$fileId}/export?key={$apiKey}&mimeType=application/pdf";
            $response = Http::get($exportUrl);
            if ($response->successful()) {
                return true;
            } else {
                return false;
            }
        } catch (Exception $e) {
            return false;
        }
    }

    /**
     * Save a background PDF file from Google Drive to local storage.
     *
     * @param \Illuminate\Http\Request $request The incoming HTTP request.
     * @return \Illuminate\Http\JsonResponse A JSON response indicating the success or error of the operation.
     */
    public function saveBackground(Request $Request): JsonResponse {
        try {
            $background = Background::find($Request->background_id);
            if(str_starts_with($background->link, "pdf_")) return response()->json(['success' => 'Documento già salvato in database']);
            $apiKey = env('GOOGLE_API_KEY');
            $fileId = $this->extractFileIdFromLink($background->link);
            $exportUrl = "https://www.googleapis.com/drive/v3/files/{$fileId}/export?key={$apiKey}&mimeType=application/pdf";
            $response = Http::get($exportUrl);
            if ($response->successful()) {
                $pdfFileName = 'pdf_' . now()->format('YmdHis') . '.pdf';
                $pdfPath = public_path('backgrounds' . DIRECTORY_SEPARATOR . $pdfFileName);
                file_put_contents($pdfPath, $response->body());
                $background->link = $pdfFileName;
                $background->update();
                return response()->json(['success' => 'Download e salvataggio completati con successo']);
            } else {
                return response()->json(['error' => 'Errore nella richiesta al server Google'], 500);
            }
        } catch (\Exception $e) {
            return response()->json(['error' => 'Errore durante la richiesta al server Google: '], 500);
        }
    }

    /**
     * Get dashboard statistics for the authenticated user.
     *
     * @return JsonResponse
     */
    public function getDahboardStats(): JsonResponse {
        $user = Auth::user();
        $newBackgroundsCount = Background::whereBetween('created_at', [now()->subDays(7), now()])->count();
        $deniedBackgroundsCount = Background::where('type', 'denied')
            ->whereBetween('created_at', [now()->subDays(7), now()])
            ->count();
        $currentWeekNewBackgroundsCount = Background::whereBetween('created_at', [now()->subDays(14), now()->subDays(7)])->count();
        $previousWeekNewBackgroundsCount = Background::whereBetween('created_at', [now()->subDays(14), now()->subDays(7)])->count();
        $percentageChangeNewBackgrounds = ($previousWeekNewBackgroundsCount !== 0)
            ? (($newBackgroundsCount - $previousWeekNewBackgroundsCount) / $previousWeekNewBackgroundsCount) * 100
            : $newBackgroundsCount * 100;
        $currentWeekDeniedBackgroundsCount = Background::where('type', 'denied')
            ->whereBetween('created_at', [now()->subDays(14), now()->subDays(7)])
            ->count();
        $previousWeekDeniedBackgroundsCount = Background::where('type', 'denied')
            ->whereBetween('created_at', [now()->subDays(21), now()->subDays(14)])
            ->count();
        $percentageChangeDeniedBackgrounds = ($previousWeekDeniedBackgroundsCount !== 0)
            ? (($deniedBackgroundsCount - $previousWeekDeniedBackgroundsCount) / $previousWeekDeniedBackgroundsCount) * 100
            : $deniedBackgroundsCount * 100;
        $currentMonthReadCount = Background::where('reader', $user->id)
            ->whereBetween('created_at', [now()->startOfMonth(), now()->endOfMonth()])
            ->count();
        $previousMonthReadCount = Background::where('reader', $user->id)
            ->whereBetween('created_at', [now()->subMonth()->startOfMonth(), now()->subMonth()->endOfMonth()])
            ->count();
        $percentageChangeReadCount = ($previousMonthReadCount !== 0)
            ? (($currentMonthReadCount - $previousMonthReadCount) / $previousMonthReadCount) * 100
            : $currentMonthReadCount * 100;
        $formatPercentage = function ($percentage) {
            return ($percentage >= 0) ? "+{$percentage}%" : "{$percentage}%";
        };
        $data = [
            'current_month_read_count' => $currentMonthReadCount,
            'percentage_change_read_count' => $formatPercentage($percentageChangeReadCount),
            'new_backgrounds_count' => $newBackgroundsCount,
            'percentage_change_new_backgrounds' => $formatPercentage($percentageChangeNewBackgrounds),
            'denied_backgrounds_count' => $deniedBackgroundsCount,
            'percentage_change_denied_backgrounds' => $formatPercentage($percentageChangeDeniedBackgrounds),
        ];
        return response()->json(['success' => 'Statistiche caricate con successo', 'data' => $data]);
    }

    /** API METHODS **/

    /**
     * API endpoint to submit a new background request.
     *
     * @param \Illuminate\Http\Request $Request The incoming HTTP request.
     * @return \Illuminate\Http\JsonResponse A JSON response indicating the success or error of the operation.
     */
    public function newBackgroundApi(Request $Request): JsonResponse {
        try {
            $validator = Validator::make($Request->all(), [
                'google_doc_link' => 'required|url',
                'discord_id' => 'required|string',
            ]);
            if ($validator->fails()) return response()->json(['error' => 'Bad request', 'details' => $validator->errors()], 400);
            if (!$this->isGoogleDocLinkPublic($Request->google_doc_link)) return response()->json(['error' => 'Il link del documento Google non è pubblico o è invalido'], 400);
            $deniedBackgroundsCount = Background::where('type', 'denied')->where('discord_id', $Request->discord_id)->count();
            $newBackgroundsCount = Background::where('type', 'new')->where('discord_id', $Request->discord_id)->count();
            if($newBackgroundsCount > 0) return response()->json(['error' => 'Hai già presentato un background: '], 400);
            if ($deniedBackgroundsCount >= 3) return response()->json(['error' => 'Hai già più di tre background rifiutati'], 400);
            DB::beginTransaction();
            $background = new Background();
            $background->discord_id = $Request->discord_id;
            $background->type = 'new';
            $background->link = $Request->google_doc_link;
            $background->generality = $Request->generality;
            $background->save();
            DB::commit();
            return response()->json(['success' => 'Verifica e registrazione bg completati con successo'], 200);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json(['error' => 'Errore durante la verifica del background: ' . $e->getMessage()], 500);
        }
    }
}