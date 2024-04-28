<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Product;
use App\Models\Category;

class UpdatePrices extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'update-prices';

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
        $major_categories = Category::all();
        foreach ($major_categories as $major_category) {
            print($major_category->id);

            $client = new \GuzzleHttp\Client();
            $response = $client->request('GET', 'https://tienda.mercadona.es/api/categories/'. $major_category->api_id);

            $body = json_decode($response->getBody(), true);
            
            $categories = $body['categories'];
            foreach ($categories as $category) {
                foreach ($category['products'] as $product) {
                    $product_db = Product::where('api_id', $product['id'])->first();
                    $product_db->previous_price = $product_db->actual_price;
                    $product_db->actual_price = $product['price_instructions']['bulk_price'];
                    $product_db->save();

                    usleep(300000);
                    print("Product $product_db->name updated\n");
                }
            }
        }

        $this->info('Actualización de la base de datos completada.');
    }
}
