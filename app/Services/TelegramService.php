<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use RuntimeException;

class TelegramService
{
    public function getBotToken(): ?string
    {
        $token = (string) config('services.telegram.bot_token');
        $token = trim($token);

        return $token !== '' ? $token : null;
    }

    public function getBotUsername(): ?string
    {
        $username = (string) config('services.telegram.bot_username');
        $username = trim($username);

        return $username !== '' ? $username : null;
    }

    public function buildStartLink(string $startPayload): ?string
    {
        $username = $this->getBotUsername();
        if (!$username) {
            return null;
        }

        return "https://t.me/{$username}?start={$startPayload}";
    }

    public function sendMessage(string $chatId, string $text): bool
    {
        $token = $this->getBotToken();
        if (!$token) {
            return false;
        }

        $response = Http::asForm()->post("https://api.telegram.org/bot{$token}/sendMessage", [
            'chat_id' => $chatId,
            'text' => $text,
            'parse_mode' => 'HTML',
            'disable_web_page_preview' => true,
        ]);

        return $response->ok() && (bool) data_get($response->json(), 'ok');
    }

    public function getUpdates(?int $offset = null): array
    {
        $token = $this->getBotToken();
        if (!$token) {
            return [];
        }

        $payload = [
            'timeout' => 0,
        ];

        if ($offset !== null) {
            $payload['offset'] = $offset;
        }

        $response = Http::get("https://api.telegram.org/bot{$token}/getUpdates", $payload);

        if (!$response->ok()) {
            $body = $response->body();
            $body = is_string($body) ? trim($body) : '';
            throw new RuntimeException('Telegram getUpdates gagal (HTTP ' . $response->status() . '): ' . $body);
        }

        $result = data_get($response->json(), 'result');
        return is_array($result) ? $result : [];
    }

    public function getLastUpdateId(): ?int
    {
        return Cache::get('telegram:last_update_id');
    }

    public function setLastUpdateId(int $updateId): void
    {
        Cache::forever('telegram:last_update_id', $updateId);
    }
}
