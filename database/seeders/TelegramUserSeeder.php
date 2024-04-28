<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Http;
use App\Models\TelegramUser;

class TelegramUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $telegramToken = env('TELEGRAM_API_KEY'); 

        $response = Http::get("https://api.telegram.org/bot{$telegramToken}/getUpdates");

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
    }
}
