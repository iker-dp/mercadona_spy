<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use App\Models\TelegramUser;

class LookForNewTelegramUsers extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:look-for-new-telegram-users';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Register new Telegram users in the database.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $telegramToken = env('TELEGRAM_API_KEY'); 

        $response = Http::get("https://api.telegram.org/bot$telegramToken/getUpdates");

        if ($response->successful()) {
            $data = $response->json();

            if (isset($data['result'])) {
                $results = $data['result'];

                foreach ($results as $update) {
                    if (isset($update['message'])) {
                        $chatId = $update['message']['chat']['id'];

                        TelegramUser::firstOrCreate(['chat_id' => $chatId]);
                    }
                }
            }
        }

        $this->info('New chats registered.');
    }
}
