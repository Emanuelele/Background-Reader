<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Config;
use Illuminate\Support\Facades\Auth;

class DiscordController extends Controller {

    /**
     * Retrieves Discord user information using the user ID.
     *
     * @param string $userId - The Discord user ID.
     * @return array - User information in JSON format.
     */
    public static function getDiscordUserInfo($userId): ?array {
        $token = env('DISCORD_BOT_TOKEN');
        $guildId = Config::get('discord.serverid');
        $response = Http::withHeaders([
            'Authorization' => 'Bot ' . $token,
        ])->get("https://discord.com/api/v10/guilds/{$guildId}/members/{$userId}");
        return $response->successful() ? $response->json() : null;
    }

    /**
     * Adds a role to a Discord user in the specified server.
     *
     * @param string $userId - The Discord user ID.
     * @return array - JSON response of the role addition operation.
     */
    public static function addRole($userId, $roleId): ?array {
        $token = env('DISCORD_BOT_TOKEN');
        $guildId = Config::get('discord.serverid');
        $response = Http::withHeaders([
            'Authorization' => 'Bot ' . $token,
            'Content-Type' => 'application/json',
        ])->put("https://discord.com/api/v10/guilds/{$guildId}/members/{$userId}/roles/{$roleId}", []);
        return $response->successful() ? $response->json() : null;
    }

    /**
     * Removes a role from a Discord user in the specified server.
     *
     * @param string $userId - The Discord user ID.
     * @return array - JSON response of the role removal operation.
     */
    public static function removeRole($userId, $roleId): ?array {
        $token = env('DISCORD_BOT_TOKEN');
        $guildId = Config::get('discord.serverid');
        $response = Http::withHeaders([
            'Authorization' => 'Bot ' . $token,
            'Content-Type' => 'application/json',
        ])->delete("https://discord.com/api/v10/guilds/{$guildId}/members/{$userId}/roles/{$roleId}");
        return $response->successful() ? $response->json() : null;
    }

    /**
     * Checks if a Discord user is whitelisted based on their user ID.
     *
     * @param string $userId - The Discord user ID.
     * @return bool - True if the user is whitelisted, false otherwise.
     */
    public static function isDiscordUserWhitelisted($userId): bool {
        $userInfo = self::getDiscordUserInfo($userId);
        return isset($userInfo['roles']) && in_array(Config::get('discord.whitelistrole'), $userInfo['roles']);
    }

    /**
     * Get the count of users with a specific role in Discord.
     *
     * @param string $roleId - The Discord role ID.
     * @return int|null - The number of users with the specified role or null if the operation fails.
     */
    public static function getCountUsersWithRole($roleId): ?int {
        $token = env('DISCORD_BOT_TOKEN');
        $guildId = Config::get('discord.serverid');
        $response = Http::withHeaders([
            'Authorization' => 'Bot ' . $token,
        ])->get("https://discord.com/api/v10/guilds/{$guildId}/members");
        \Log::error("Errore API Discord: " . $response);
        if (!$response->successful()) return null;
        $members = $response->json();
        $count = 0;
        foreach ($members as $member) if(in_array($roleId, $member['roles'])) $count++;
        return $count;
    }

    /**
     * Checks if a Discord user has a specific role based on their user ID.
     *
     * @param string $userId - The Discord user ID.
     * @param string $roleId - The Discord role ID to check.
     * @return bool - True if the user has the specified role, false otherwise.
     */
    public static function hasRole($userId, $roleId): bool {
        $userInfo = self::getDiscordUserInfo($userId);
        if (isset($userInfo['roles'])) return in_array($roleId, $userInfo['roles']);
        return false;
    }

    /**
     * Sends an embed message to a Discord channel.
     *
     * @param string $channelId - The Discord channel ID where the embed message will be sent.
     * @param array $embedData - An associative array containing the embed message data.
     * @return bool - True if the embed message is sent successfully, false otherwise.
     */
    public static function sendEmbedMessage($channelId, $embedData): bool {
        $token = env('DISCORD_BOT_TOKEN');

        $response = Http::withHeaders([
            'Authorization' => 'Bot ' . $token,
            'Content-Type' => 'application/json',
        ])->post("https://discord.com/api/v10/channels/{$channelId}/messages", [
            'embeds' => [$embedData],
        ]);

        return $response->successful();
    }

    /**
     * Make a cURL request to the Discord API with the provided Discord ID.
     *
     * @param string $discordId
     * @return array
     */
    public static function makeDiscordApiRequest(string $discordId): array {
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
    public static function getAvatarUrl(string $discordId, string $avatarHash): ?string {
        return is_null($avatarHash || $discordId) ? "https://cdn.discordapp.com/icons/1015976925367378040/a_8aab7490e9efb8cc53487de73e4521c7.webp?size=240" : 
            "https://cdn.discordapp.com/avatars/{$discordId}/{$avatarHash}.png";
    }

    /**
     * Send a Discord message with the result of an evaluation.
     *
     * @param string $channelId - The ID of the Discord channel to send the message to.
     * @param string $userId - The ID of the user to whom the evaluation result refers.
     * @param string $result - The result of the evaluation ("approved" or "rejected").
     * @param string $note - Any additional notes associated with the result.
     * @return void
     */
    public static function sendDiscordResultMessage($channelId, $userId, $result, $note): void {
        if($result == "approved"){
            $color = 0x00FF00;
            $esito = "Approvato";
        } else {
            $color = 16711680;
            $esito = "Rifiutato";
        }
        $embedData = [
            'title' => 'Risultato valutazione background',
            'description' => '**Background di:**    <@'.$userId.'>' . PHP_EOL .
                            '**Esito:**    '. $esito . PHP_EOL .
                            '**Note:**    '. $note . PHP_EOL .
                            '**Valutato da:**    <@'.Auth::user()->id.'>',
            'color' => $color,
        ];
        self::sendEmbedMessage($channelId, $embedData);
    }
}
