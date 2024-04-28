<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Category;
use App\Models\Product;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $major_categories = Category::all();
        foreach ($major_categories as $major_category) {

            $client = new \GuzzleHttp\Client();
            $response = $client->request('GET', 'https://tienda.mercadona.es/api/categories/' . $major_category->api_id);

            $body = json_decode($response->getBody(), true);

            $categories = $body['categories'];
            foreach ($categories as $category) {
                foreach ($category['products'] as $product) {
                    $product_db = Product::create([
                        'api_id' => $product['id'],
                        'name' => $product['display_name'],
                        'actual_price' => $product['price_instructions']['bulk_price'],
                        'previous_price' => $product['price_instructions']['bulk_price'],
                        'category_id' => $major_category->api_id
                    ]);

                    print("Product $product_db->name created\n");
                }
            }
        }
    }
}
