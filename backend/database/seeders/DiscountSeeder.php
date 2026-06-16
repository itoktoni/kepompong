<?php

namespace Database\Seeders;

use App\Models\Discount;
use Illuminate\Database\Seeder;

class DiscountSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $discounts = [
            [
                'discount_code' => 'HEMAT10',
                'discount_nama' => 'Diskon 10%',
                'discount_type' => 'percentage',
                'discount_value' => 10,
                'discount_min_transaction' => 0,
                'discount_max_amount' => 50000,
                'discount_active' => true,
            ],
            [
                'discount_code' => 'DISKON50K',
                'discount_nama' => 'Diskon Rp 50.000',
                'discount_type' => 'fixed',
                'discount_value' => 50000,
                'discount_min_transaction' => 500000,
                'discount_max_amount' => null,
                'discount_active' => true,
            ],
            [
                'discount_code' => 'GRATIS20',
                'discount_nama' => 'Diskon 20%',
                'discount_type' => 'percentage',
                'discount_value' => 20,
                'discount_min_transaction' => 1000000,
                'discount_max_amount' => 200000,
                'discount_active' => true,
            ],
            [
                'discount_code' => 'AWALAN',
                'discount_nama' => 'Diskon 5% Semua Transaksi',
                'discount_type' => 'percentage',
                'discount_value' => 5,
                'discount_min_transaction' => 0,
                'discount_max_amount' => 25000,
                'discount_active' => true,
            ],
            [
                'discount_code' => 'MEMBER',
                'discount_nama' => 'Diskon Member 15%',
                'discount_type' => 'percentage',
                'discount_value' => 15,
                'discount_min_transaction' => 0,
                'discount_max_amount' => 100000,
                'discount_active' => true,
            ],
        ];

        foreach ($discounts as $discount) {
            Discount::updateOrCreate(
                ['discount_code' => $discount['discount_code']],
                $discount
            );
        }
    }
}
