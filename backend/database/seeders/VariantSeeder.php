<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\Variant;
use Illuminate\Database\Seeder;

class VariantSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $products = Product::with('has_category')->get();

        foreach ($products as $product) {
            $category = $product->has_category?->category_nama ?? '';

            // Processor variants
            if (stripos($category, 'Processor') !== false) {
                $this->createProcessorVariants($product);
            }
            // Motherboard variants
            elseif (stripos($category, 'Motherboard') !== false) {
                $this->createMotherboardVariants($product);
            }
            // RAM variants
            elseif (stripos($category, 'RAM') !== false) {
                $this->createRamVariants($product);
            }
            // VGA variants
            elseif (stripos($category, 'VGA') !== false) {
                $this->createVgaVariants($product);
            }
            // Storage variants
            elseif (stripos($category, 'Storage') !== false) {
                $this->createStorageVariants($product);
            }
            // PSU variants
            elseif (stripos($category, 'PSU') !== false) {
                $this->createPsuVariants($product);
            }
            // Monitor variants
            elseif (stripos($category, 'Monitor') !== false) {
                $this->createMonitorVariants($product);
            }
            // Keyboard Mouse variants
            elseif (stripos($category, 'Keyboard') !== false || stripos($category, 'Mouse') !== false) {
                $this->createKeyboardMouseVariants($product);
            }
            // Generic variants
            else {
                $this->createGenericVariants($product);
            }
        }
    }

    private function createProcessorVariants(Product $product)
    {
        $basePrice = $product->product_harga;
        $suffix = explode(' ', $product->product_nama);
        $brand = $suffix[0] ?? '';

        $variants = [
            'Box' => 0,
            'Tray' => -150000,
            'Box + Wraith Cooler' => 250000,
            'Box + Liquid Cooler' => 850000,
        ];

        foreach ($variants as $name => $priceDiff) {
            Variant::create([
                'product_id' => $product->product_id,
                'variant_nama' => $brand.' '.$name,
                'variant_harga' => $basePrice + $priceDiff,
                'variant_deskripsi' => 'Variant '.$name.' untuk '.$product->product_nama,
            ]);
        }
    }

    private function createMotherboardVariants(Product $product)
    {
        $basePrice = $product->product_harga;

        $variants = [
            'Standard' => 0,
            'WiFi Version' => 350000,
            'Premium' => 750000,
        ];

        foreach ($variants as $name => $priceDiff) {
            Variant::create([
                'product_id' => $product->product_id,
                'variant_nama' => $name,
                'variant_harga' => $basePrice + $priceDiff,
                'variant_deskripsi' => 'Variant '.$name,
            ]);
        }
    }

    private function createRamVariants(Product $product)
    {
        $basePrice = $product->product_harga;

        $variants = [
            'Single Stick' => 0,
            'Dual Channel (2x)' => 150000,
            'Quad Channel (4x)' => 450000,
        ];

        foreach ($variants as $name => $priceDiff) {
            Variant::create([
                'product_id' => $product->product_id,
                'variant_nama' => $name,
                'variant_harga' => $basePrice + $priceDiff,
                'variant_deskripsi' => 'Paket '.$name,
            ]);
        }
    }

    private function createVgaVariants(Product $product)
    {
        $basePrice = $product->product_harga;

        $variants = [
            'Reference' => 0,
            'Overclocked' => 250000,
            'Premium Edition' => 550000,
        ];

        foreach ($variants as $name => $priceDiff) {
            Variant::create([
                'product_id' => $product->product_id,
                'variant_nama' => $name,
                'variant_harga' => $basePrice + $priceDiff,
                'variant_deskripsi' => 'VGA '.$name,
            ]);
        }
    }

    private function createStorageVariants(Product $product)
    {
        $basePrice = $product->product_harga;
        $isSsd = stripos($product->product_nama, 'SSD') !== false;

        if ($isSsd) {
            $variants = [
                'No Heatsink' => 0,
                'With Heatsink' => 75000,
                'RGB Heatsink' => 175000,
            ];
        } else {
            $variants = [
                '7200 RPM' => 0,
                '5400 RPM' => -100000,
            ];
        }

        foreach ($variants as $name => $priceDiff) {
            Variant::create([
                'product_id' => $product->product_id,
                'variant_nama' => $name,
                'variant_harga' => $basePrice + $priceDiff,
                'variant_deskripsi' => 'Storage '.$name,
            ]);
        }
    }

    private function createPsuVariants(Product $product)
    {
        $basePrice = $product->product_harga;

        $variants = [
            'Non-Modular' => 0,
            'Semi-Modular' => 200000,
            'Fully Modular' => 450000,
        ];

        foreach ($variants as $name => $priceDiff) {
            Variant::create([
                'product_id' => $product->product_id,
                'variant_nama' => $name,
                'variant_harga' => $basePrice + $priceDiff,
                'variant_deskripsi' => 'PSU '.$name,
            ]);
        }
    }

    private function createMonitorVariants(Product $product)
    {
        $basePrice = $product->product_harga;

        $variants = [
            'No Stand' => -150000,
            'With Stand' => 0,
            'With Mount Kit' => 75000,
            'Premium Stand' => 250000,
        ];

        foreach ($variants as $name => $priceDiff) {
            Variant::create([
                'product_id' => $product->product_id,
                'variant_nama' => $name,
                'variant_harga' => $basePrice + $priceDiff,
                'variant_deskripsi' => 'Monitor '.$name,
            ]);
        }
    }

    private function createKeyboardMouseVariants(Product $product)
    {
        $basePrice = $product->product_harga;

        $variants = [
            'Wired' => 0,
            'Wireless' => 175000,
            'Wireless + Rechargeable' => 325000,
        ];

        foreach ($variants as $name => $priceDiff) {
            Variant::create([
                'product_id' => $product->product_id,
                'variant_nama' => $name,
                'variant_harga' => $basePrice + $priceDiff,
                'variant_deskripsi' => $product->product_nama.' '.$name,
            ]);
        }
    }

    private function createGenericVariants(Product $product)
    {
        $basePrice = $product->product_harga;

        $variants = [
            'Standard' => 0,
            'Premium' => 200000,
        ];

        foreach ($variants as $name => $priceDiff) {
            Variant::create([
                'product_id' => $product->product_id,
                'variant_nama' => $name,
                'variant_harga' => $basePrice + $priceDiff,
                'variant_deskripsi' => $product->product_nama.' '.$name,
            ]);
        }
    }
}
