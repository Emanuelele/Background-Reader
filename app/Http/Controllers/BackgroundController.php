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
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Validator;
use Config;

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

    /**
     * Get Discord user information based on the provided Discord ID.
     *
     * @param string $discordId
     * @return array
     */
    private function getDiscordUserInfo(string $background_id): array {
        $background = Background::find($background_id);
        return array_merge(DiscordController::makeDiscordApiRequest($background->discord_id), $this->backgroundInfoFromDiscordUserId($background_id));
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
            'isWhitelisted' => DiscordController::isDiscorduserWhitelisted($background->discord_id),
            'new' => DB::table('backgrounds')->where('discord_id', $background->discord_id)->count(),
            'approved' => DB::table('backgrounds')->where('type', 'approved')->where('discord_id', $background->discord_id)->count(),
            'denied' => DB::table('backgrounds')->where('type', 'denied')->where('discord_id', $background->discord_id)->count(),
            'note' => DB::table('backgrounds')->where('id', $background_id)->value('note'),
        ];
    }

    /**
     * Read the oldest new background and retrieve additional information.
     *
     * @return View
     */
    public function readBackground(): View | RedirectResponse {
        $background =  Background::where('type', 'new')->orderBy('haspriority', 'desc')->orderBy('created_at', 'asc')->first();
        if(!$background) return redirect()->route('dashboard')->withErrors("Non ci sono background da leggere");
        $backgrounds =  Background::where('discord_id', $background->discord_id)->whereNotIn('id', [$background->id])->orderBy('created_at', 'asc')->get();
        $additionalInfo = $this->getDiscordUserInfo($background->id);
        return view('readbackground', [
            'background' => $background,
            'backgrounds' => $backgrounds,
            'additionalInfo' => $additionalInfo,
            'avatarUrl' => DiscordController::getAvatarUrl($additionalInfo['id'], $additionalInfo['avatar']),
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
            'avatarUrl' => DiscordController::getAvatarUrl($additionalInfo['id'], $additionalInfo['avatar']),
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
            if($Request->result == "approved") {
                DiscordController::removeRole($background->discord_id, Config::get('discord.waitingbg'));
                DiscordController::removeRole($background->discord_id, Config::get('discord.bgdenied'));
                DiscordController::addRole($background->discord_id, Config::get('discord.bgapproved'));
            }
            else {
                DiscordController::removeRole($background->discord_id, Config::get('discord.waitingbg'));
                DiscordController::addRole($background->discord_id, Config::get('discord.bgdenied'));
                DiscordController::removeRole($background->discord_id, Config::get('discord.bgapproved'));
            }
            DiscordController::sendDiscordResultMessage(Config::get('discord.resultchannel'), $background->discord_id, $Request->result, $background->note);
            $background->reader = Auth::user()->id;
            $background->save();
            return response()->json(['success' => 'Background approvato con successo'], 200);
        } catch(Exception $e) {
            return response()->json(['error' => 'Errore nel server'], 500);
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
                $background->update([
                    'link' => $pdfFileName,
                ]);
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
    public function getDashboardStats(): JsonResponse {
        $user = Auth::user();
        $newBackgroundsCount = Background::whereBetween('created_at', [now()->subDays(7), now()])
            ->count();
        $deniedBackgroundsCount = Background::where('type', 'denied')
            ->whereBetween('created_at', [now()->subDays(7), now()])
            ->count();
        $currentWeekNewBackgroundsCount = Background::whereBetween('created_at', [now()->subDays(14), now()->subDays(7)])
            ->count();
        $previousWeekNewBackgroundsCount = Background::whereBetween('created_at', [now()->subDays(14), now()->subDays(7)])
            ->count();
        $currentWeekDeniedBackgroundsCount = Background::where('type', 'denied')
            ->whereBetween('created_at', [now()->subDays(14), now()->subDays(7)])
            ->count();
        $previousWeekDeniedBackgroundsCount = Background::where('type', 'denied')
            ->whereBetween('created_at', [now()->subDays(21), now()->subDays(14)])
            ->count();
        $currentMonthReadCount = Background::where('reader', $user->id)
            ->whereBetween('created_at', [now()->startOfMonth(), now()->endOfMonth()])
            ->count();
        $currentMonthDeniedCount = Background::where('reader', $user->id)
            ->where('type', 'denied')
            ->whereBetween('created_at', [now()->startOfMonth(), now()->endOfMonth()])
            ->count();
        $currentMonthApprovedCount = Background::where('reader', $user->id)
            ->where('type', 'approved')
            ->whereBetween('created_at', [now()->startOfMonth(), now()->endOfMonth()])
            ->count();
        $previousMonthReadCount = Background::where('reader', $user->id)
            ->whereBetween('created_at', [now()->subMonth()->endOfMonth(), now()->subDays(30)])
            ->count();
        $previousMonthDeniedCount = Background::where('reader', $user->id)
            ->where('type', 'denied')
            ->whereBetween('created_at', [now()->subMonth()->endOfMonth(), now()->subDays(30)])
            ->count();
        $previousMonthApprovedCount = Background::where('reader', $user->id)
            ->where('type', 'approved')
            ->whereBetween('created_at', [now()->subMonth()->endOfMonth(), now()->subDays(30)])
            ->count();
        $percentageChangeNewBackgrounds = ($previousWeekNewBackgroundsCount !== 0)
            ? (($newBackgroundsCount - $previousWeekNewBackgroundsCount) / $previousWeekNewBackgroundsCount) * 100
            : $newBackgroundsCount * 100;
        $percentageChangeDeniedBackgrounds = ($previousWeekDeniedBackgroundsCount !== 0)
            ? (($deniedBackgroundsCount - $previousWeekDeniedBackgroundsCount) / $previousWeekDeniedBackgroundsCount) * 100
            : $deniedBackgroundsCount * 100;
        $percentageChangeReadCount = ($previousMonthReadCount !== 0)
            ? (($currentMonthReadCount - $previousMonthReadCount) / $previousMonthReadCount) * 100
            : $currentMonthReadCount * 100;
        $percentageChangeDeniedCount = ($previousMonthDeniedCount !== 0)
            ? (($currentMonthDeniedCount - $previousMonthDeniedCount) / $previousMonthDeniedCount) * 100
            : $currentMonthDeniedCount * 100;
        $percentageChangeApprovedCount = ($previousMonthApprovedCount !== 0)
            ? (($currentMonthApprovedCount - $previousMonthApprovedCount) / $previousMonthApprovedCount) * 100
            : $currentMonthApprovedCount * 100;
        $formatPercentage = function ($percentage) {
            return ($percentage >= 0) ? "+{$percentage}%" : "{$percentage}%";
        };
        //$whitelistUsersCount = DiscordController::getCountUsersWithRole(Config::get('discord.whitelistrole'));
        //$whitelistDeniedUsersCount = DiscordController::getCountUsersWithRole(Config::get('discord.whitelistrole'));
        $data = [
            //'whitelist_users_count' => $whitelistUsersCount,
            //'whitelist_denied_users_count' => $whitelistDeniedUsersCount,
            'current_month_read_count' => $currentMonthReadCount,
            'percentage_change_read_count' => $formatPercentage($percentageChangeReadCount),
            'current_month_denied_count' => $currentMonthDeniedCount,
            'percentage_change_denied_count' => $formatPercentage($percentageChangeDeniedCount),
            'current_month_approved_count' => $currentMonthApprovedCount,
            'percentage_change_approved_count' => $formatPercentage($percentageChangeApprovedCount),
            'new_backgrounds_count' => $newBackgroundsCount,
            'percentage_change_new_backgrounds' => $formatPercentage($percentageChangeNewBackgrounds),
            'denied_backgrounds_count' => $deniedBackgroundsCount,
            'percentage_change_denied_backgrounds' => $formatPercentage($percentageChangeDeniedBackgrounds),
        ];
        return response()->json(['success' => 'Statistiche caricate con successo', 'data' => $data]);
    }

    /**
     * API endpoint to submit a new background request.
     *
     * @param Request $Request The incoming HTTP request.
     * @return JsonResponse A JSON response indicating the success or error of the operation.
     */
    public function newBackgroundApi(Request $Request): JsonResponse { //TO_DO: controllo ruolo wl, 
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
            if(DiscordController::hasRole($Request->discord_id, Config::get('discord.whitelistrole'))) $background->haspriority = 1;
            $background->save();
            DB::commit();
            return response()->json(['success' => 'Verifica e registrazione bg completati con successo'], 200);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json(['error' => 'Errore durante la verifica del background: ' . $e->getMessage()], 500);
        }
    }
}