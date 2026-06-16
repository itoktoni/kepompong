<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PosSeeder extends Seeder
{
    public function run(): void
    {
        // Insert categories
        DB::table('category')->insert([
            ['category_id' => 1, 'category_nama' => 'Makanan', 'category_keterangan' => 'Makanan'],
            ['category_id' => 2, 'category_nama' => 'Minuman', 'category_keterangan' => 'Minuman'],
            ['category_id' => 3, 'category_nama' => 'Snack', 'category_keterangan' => 'Snack'],
            ['category_id' => 4, 'category_nama' => 'Dessert', 'category_keterangan' => 'Dessert'],
        ]);

        // Insert products
        DB::table('product')->insert([
            ['product_id' => 1, 'product_nama' => 'Nasi Goreng', 'product_harga' => 25000, 'product_keterangan' => '', 'product_id_category' => 1],
            ['product_id' => 2, 'product_nama' => 'Mie Ayam', 'product_harga' => 20000, 'product_keterangan' => '', 'product_id_category' => 1],
            ['product_id' => 3, 'product_nama' => 'Ayam Bakar', 'product_harga' => 35000, 'product_keterangan' => '', 'product_id_category' => 1],
            ['product_id' => 4, 'product_nama' => 'Soto Betawi', 'product_harga' => 30000, 'product_keterangan' => '', 'product_id_category' => 1],
            ['product_id' => 5, 'product_nama' => 'Es Teh Manis', 'product_harga' => 8000, 'product_keterangan' => '', 'product_id_category' => 2],
            ['product_id' => 6, 'product_nama' => 'Kopi Susu', 'product_harga' => 18000, 'product_keterangan' => '', 'product_id_category' => 2],
            ['product_id' => 7, 'product_nama' => 'Jus Alpukat', 'product_harga' => 15000, 'product_keterangan' => '', 'product_id_category' => 2],
            ['product_id' => 8, 'product_nama' => 'Air Mineral', 'product_harga' => 5000, 'product_keterangan' => '', 'product_id_category' => 2],
            ['product_id' => 9, 'product_nama' => 'Kentang Goreng', 'product_harga' => 15000, 'product_keterangan' => '', 'product_id_category' => 3],
            ['product_id' => 10, 'product_nama' => 'Risoles', 'product_harga' => 10000, 'product_keterangan' => '', 'product_id_category' => 3],
            ['product_id' => 11, 'product_nama' => 'Tahu Crispy', 'product_harga' => 12000, 'product_keterangan' => '', 'product_id_category' => 3],
            ['product_id' => 12, 'product_nama' => 'Cireng', 'product_harga' => 8000, 'product_keterangan' => '', 'product_id_category' => 3],
            ['product_id' => 13, 'product_nama' => 'Es Krim', 'product_harga' => 12000, 'product_keterangan' => '', 'product_id_category' => 4],
            ['product_id' => 14, 'product_nama' => 'Pudding', 'product_harga' => 10000, 'product_keterangan' => '', 'product_id_category' => 4],
            ['product_id' => 15, 'product_nama' => 'Brownies', 'product_harga' => 18000, 'product_keterangan' => '', 'product_id_category' => 4],
            ['product_id' => 16, 'product_nama' => 'Cheesecake', 'product_harga' => 25000, 'product_keterangan' => '', 'product_id_category' => 4],
        ]);
    }
}
