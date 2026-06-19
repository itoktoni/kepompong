<?php
namespace Database\Seeders\Tables;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PaymentMethodSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        /**
         * Command :
         * artisan seed:generate --mode=table --tables=payment_method
         *
         */

        $dataTables = [
            [
                'payment_method_active' => 1,
                'payment_method_category' => 'qris',
                'payment_method_id' => 1,
                'payment_method_nama' => '⛆ QRIS',
                'payment_method_person' => 'Kepompong - Airpay Shopee',
                'payment_method_rekening' => '',
                'payment_method_transfer' => 'Tinggal Scan ⛆, Masukkan Nominal, dan Bayar!

Pastikan angka yang Anda masukkan tepat sesuai nominal di atas agar sistem bisa memverifikasi transaksi Anda secara otomatis.',
            ],
            [
                'payment_method_active' => 1,
                'payment_method_category' => 'gopay',
                'payment_method_id' => 3,
                'payment_method_nama' => '🟩 Gopay',
                'payment_method_person' => 'Itok toni laksono',
                'payment_method_rekening' => 8111040159,
                'payment_method_transfer' => 'Topup atau Transfer! ⚡

TopUp atau Transfer ke nomor E-wallet lalu bayar tepat sesuai nominal yang tertera. Sistem otomatis kami yang akan memverifikasinya secara otomatis!',
            ],
            [
                'payment_method_active' => 1,
                'payment_method_category' => 'blu',
                'payment_method_id' => 2,
                'payment_method_nama' => '💳 BANK BLUE DIGITAL',
                'payment_method_person' => 'Itok toni laksono',
                'payment_method_rekening' => 175202,
                'payment_method_transfer' => 'Tinggal Salin & Transfer! ⚡

Copy nomor rekening, lalu bayar tepat sesuai nominal yang tertera. Sistem otomatis kami yang akan memverifikasinya secara otomatis!',
            ],
        ];

        DB::table("payment_method")->insert($dataTables);
    }
}