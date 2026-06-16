<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use Faker\Factory as Faker;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $faker = Faker::create('id_ID');
        $now = now();
        $chunks = [];

        foreach (range(1, 10) as $item) {
            $category[] = [
                'category_id' => $item,
                'category_nama' => $faker->name,
                'category_keterangan' => $faker->sentence(),
            ];
        }

        Category::insert($category);

        for ($i = 0; $i < 10000; $i++) {
            $chunks[] = [
                'product_nama' => $faker->words(rand(2, 4), true),
                'product_harga' => $faker->randomFloat(2, 10000, 50000000),
                'product_keterangan' => $faker->sentence(),
                'product_id_category' => rand(1, 10),
            ];

            if (count($chunks) === 500) {
                Product::insert($chunks);
                $chunks = [];
            }
        }

        if ($chunks) {
            Product::insert($chunks);
        }
    }
}
