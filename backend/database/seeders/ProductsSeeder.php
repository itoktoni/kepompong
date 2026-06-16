<?php

namespace Database\Seeders;

use App\Models\Products;
use Faker\Factory as Faker;
use Illuminate\Database\Seeder;

class ProductsSeeder extends Seeder
{
    public function run(): void
    {
        $faker = Faker::create('id_ID');
        $now = now();
        $chunks = [];

        foreach (range(1, 10) as $item) {
            $products[] = [
                'products_id' => $item,
                'products_nama' => $faker->name,
                'products_keterangan' => $faker->sentence(),
            ];
        }

        Products::insert($products);

        for ($i = 0; $i < 10000; $i++) {
            $chunks[] = [
                'products_nama' => $faker->words(rand(2, 4), true),
                'products_harga' => $faker->randomFloat(2, 10000, 50000000),
                'products_keterangan' => $faker->sentence(),
                'products_id_category' => rand(1, 10),
            ];

            if (count($chunks) === 500) {
                Products::insert($chunks);
                $chunks = [];
            }
        }

        if ($chunks) {
            Products::insert($chunks);
        }
    }
}
