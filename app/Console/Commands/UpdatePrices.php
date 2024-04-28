<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Support\Facades\Http;
use App\Models\TelegramUser;

class UpdatePrices extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:update-prices';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Actualiza los precios de los productos en la base de datos';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $final_message = "";

        $major_categories = Category::all();
        foreach ($major_categories as $major_category) {
            $client = new \GuzzleHttp\Client();
            $response = $client->request('GET', 'https://tienda.mercadona.es/api/categories/' . $major_category->api_id);
            // $response = $client->request('GET', 'https://tienda.mercadona.es/api/categories/12');

            $body = json_decode($response->getBody(), true);

            $categories = $body['categories'];
            foreach ($categories as $category) {
                foreach ($category['products'] as $product) {
                    $message = "";

                    $product_db = Product::where('api_id', $product['id'])->first();
                    $product_db->previous_price = $product_db->actual_price;
                    // $product_db->previous_price = -1;

                    $product_db->actual_price = $product['price_instructions']['bulk_price'];

                    $product_db->save();

                    if ($product_db->actual_price > $product_db->previous_price) {
                        $difference = $product_db->actual_price - $product_db->previous_price;
                        $message .= "-> El " . $product_db->name . " ha subido en " . $difference . "€\n";
                    }

                    usleep(300000);
                    print("Product $product_db->name updated\n");
                }
                if ($message != "") {
                    $final_message .= "Actualización de precios de la categoría " . $category['name'] . ":\n";
                    $final_message .= $message;
                    $this->sendTelegramMessage($final_message);
                }
            }
        }

        $this->sendTelegramMessage($message);

        $this->info('Actualización de precios la base de datos completada.');
    }

    public function sendTelegramMessage($message)
    {
        $chatIds = TelegramUser::all()->pluck('chat_id')->toArray();

        foreach ($chatIds as $chatId) {
            $telegramToken = env('TELEGRAM_API_KEY');

            $response = Http::post("https://api.telegram.org/bot$telegramToken/sendMessage", [
                'chat_id' => $chatId,
                'text' => $message
            ]);
            print($response);

            if ($response->successful()) {
                $this->info('Mensaje enviado con éxito a ' . $chatId);
            } else {
                $this->error('Error al enviar el mensaje a Telegram.');
            }
        }
    }
}
