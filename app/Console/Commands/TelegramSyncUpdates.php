<?php

namespace App\Console\Commands;

use App\Services\TelegramService;
use App\User;
use App\UserTelegram;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class TelegramSyncUpdates extends Command
{
    protected $signature = 'telegram:sync-updates {--limit=50} {--reset : Reset last update offset cache so old updates can be read again}';

    protected $description = 'Sync Telegram getUpdates and connect chat_id to users via /start <token>';

    public function handle(TelegramService $telegram): int
    {
        if (!$telegram->getBotToken()) {
            $this->error('TELEGRAM_BOT_TOKEN belum diset di .env');
            return self::FAILURE;
        }

        if ((bool) $this->option('reset')) {
            Cache::forget('telegram:last_update_id');
            $this->info('Offset Telegram di-reset (cache telegram:last_update_id dihapus)');
        }

        $lastUpdateId = $telegram->getLastUpdateId();
        $offset = $lastUpdateId ? $lastUpdateId + 1 : null;

        $updates = $telegram->getUpdates($offset);
        if (!$updates) {
            $this->info('Tidak ada update baru');
            return self::SUCCESS;
        }

        $limit = (int) $this->option('limit');
        $updates = array_slice($updates, 0, max(1, $limit));

        $connected = 0;
        $latestUpdateId = $lastUpdateId;

        foreach ($updates as $update) {
            $updateId = (int) data_get($update, 'update_id');
            if ($updateId) {
                $latestUpdateId = max((int) $latestUpdateId, $updateId);
            }

            $text = (string) data_get($update, 'message.text', '');
            $chatId = data_get($update, 'message.chat.id');

            if (!$text || !$chatId) {
                continue;
            }

            $text = trim($text);
            if (stripos($text, '/start') !== 0) {
                continue;
            }

            $parts = preg_split('/\s+/', $text);
            $token = isset($parts[1]) ? trim((string) $parts[1]) : '';
            if ($token === '') {
                continue;
            }

            $user = User::where('telegram_link_token', $token)->first();
            if (!$user) {
                continue;
            }

            DB::transaction(function () use ($user, $chatId, &$connected) {
                $chatId = (string) $chatId;

                $existing = UserTelegram::where('chat_id', $chatId)->first();

                if ($existing) {
                    $existing->user_id = $user->id;
                    $existing->is_active = true;
                    $existing->save();

                    UserTelegram::where('user_id', $user->id)
                        ->where('id', '!=', $existing->id)
                        ->where('is_active', true)
                        ->update(['is_active' => false]);
                } else {
                    UserTelegram::where('user_id', $user->id)->where('is_active', true)->update(['is_active' => false]);

                    UserTelegram::create([
                        'user_id' => $user->id,
                        'chat_id' => $chatId,
                        'is_active' => true,
                    ]);
                }

                $connected++;
            });
        }

        if ($latestUpdateId !== null) {
            $telegram->setLastUpdateId((int) $latestUpdateId);
        }

        $this->info("Selesai. Berhasil connect: {$connected}");

        return self::SUCCESS;
    }
}
