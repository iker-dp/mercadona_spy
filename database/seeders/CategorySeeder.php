<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Http;
use App\Models\Category;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $client = new \GuzzleHttp\Client();
        $response = $client->request('GET', 'https://tienda.mercadona.es/api/categories/');

        $body = json_decode($response->getBody(), true);

        $major_categories = $body['results'];
        
        foreach ($major_categories as $major_category) {
            $category_db = Category::create([
                'api_id' => $major_category['id'],
                'name' => $major_category['name'],
            ]);

            $categories = $major_category['categories'];
            foreach ($categories as $category) {
                $category_db = Category::create([
                    'api_id' => $category['id'],
                    'name' => $category['name'],
                ]);
                print("Category $category_db->id created\n");
            }

            print("Category $category_db->id created\n");
        }
    }
}
