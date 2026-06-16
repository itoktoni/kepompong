<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use App\Models\Variant;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PosProductSeeder extends Seeder
{
    public function run(): void
    {
        // Disable foreign key checks temporarily to allow truncation
        DB::statement('SET FOREIGN_KEY_CHECKS=0');

        // Clear existing data (child tables first, then parent tables)
        Variant::truncate();
        Product::truncate();
        Category::truncate();

        // Re-enable foreign key checks
        DB::statement('SET FOREIGN_KEY_CHECKS=1');

        // Define real categories for computer spareparts
        $categories = [
            ['category_nama' => 'Processor', 'category_keterangan' => 'CPU dan Processor'],
            ['category_nama' => 'Motherboard', 'category_keterangan' => 'Motherboard / Mainboard'],
            ['category_nama' => 'RAM', 'category_keterangan' => 'Memori RAM'],
            ['category_nama' => 'VGA', 'category_keterangan' => 'Kartu Grafis'],
            ['category_nama' => 'Storage', 'category_keterangan' => 'SSD dan HDD'],
            ['category_nama' => 'PSU', 'category_keterangan' => 'Power Supply'],
            ['category_nama' => 'Casing', 'category_keterangan' => 'Casing Komputer'],
            ['category_nama' => 'Monitor', 'category_keterangan' => 'Monitor LED/LCD'],
            ['category_nama' => 'Keyboard Mouse', 'category_keterangan' => 'Keyboard dan Mouse'],
            ['category_nama' => 'Aksesoris', 'category_keterangan' => 'Aksesoris Komputer'],
        ];

        // Insert categories
        $categoryIds = [];
        foreach ($categories as $index => $cat) {
            $categoryIds[$cat['category_nama']] = $index + 1;
            Category::create([
                'category_id' => $index + 1,
                'category_nama' => $cat['category_nama'],
                'category_keterangan' => $cat['category_keterangan'],
            ]);
        }

        // Real computer spareparts products
        $products = [
            // Processor (10 items)
            ['Intel Core i3-12100', 1850000, 'Processor', 'Processor 12th Gen, 4 Cores'],
            ['Intel Core i5-12400', 2850000, 'Processor', 'Processor 12th Gen, 6 Cores'],
            ['Intel Core i5-13400', 3200000, 'Processor', 'Processor 13th Gen, 10 Cores'],
            ['Intel Core i7-13700', 5500000, 'Processor', 'Processor 13th Gen, 16 Cores'],
            ['Intel Core i9-13900K', 9500000, 'Processor', 'Processor 13th Gen, 24 Cores'],
            ['AMD Ryzen 5 5600', 2200000, 'Processor', 'AMD Ryzen 5, 6 Cores'],
            ['AMD Ryzen 5 7600', 3500000, 'Processor', 'AMD Ryzen 5, 6 Cores AM5'],
            ['AMD Ryzen 7 5800X', 4200000, 'Processor', 'AMD Ryzen 7, 8 Cores'],
            ['AMD Ryzen 7 7700X', 5800000, 'Processor', 'AMD Ryzen 7, 8 Cores AM5'],
            ['AMD Ryzen 9 7950X', 11500000, 'Processor', 'AMD Ryzen 9, 16 Cores'],

            // Motherboard (10 items)
            ['ASUS PRIME H610M-E', 1250000, 'Motherboard', 'Motherboard LGA1700, mATX'],
            ['MSI PRO B660M-A', 1800000, 'Motherboard', 'Motherboard LGA1700, mATX'],
            ['Gigabyte B660M DS3H', 1400000, 'Motherboard', 'Motherboard LGA1700, mATX'],
            ['ASUS TUF B550-PLUS', 2500000, 'Motherboard', 'Motherboard AM4, ATX'],
            ['MSI MAG B550 TOMAHAWK', 2800000, 'Motherboard', 'Motherboard AM4, ATX'],
            ['Gigabyte B550 AORUS PRO', 3200000, 'Motherboard', 'Motherboard AM4, ATX'],
            ['ASUS ROG STRIX B650E-F', 5800000, 'Motherboard', 'Motherboard AM5, ATX'],
            ['MSI MEG X670E ACE', 12000000, 'Motherboard', 'Motherboard AM5, E-ATX'],
            ['ASRock B650M PG Riptide', 2800000, 'Motherboard', 'Motherboard AM5, mATX'],
            ['Gigabyte Z790 AORUS MASTER', 8500000, 'Motherboard', 'Motherboard LGA1700, ATX'],

            // RAM (10 items)
            ['Corsair Vengeance 8GB DDR4 3200', 350000, 'RAM', 'RAM 8GB DDR4 3200MHz'],
            ['Corsair Vengeance 16GB DDR4 3200', 650000, 'RAM', 'RAM 16GB DDR4 3200MHz'],
            ['G.Skill Trident Z 16GB DDR4 3600', 750000, 'RAM', 'RAM 16GB DDR4 3600MHz'],
            ['G.Skill Trident Z 32GB DDR4 3200', 1200000, 'RAM', 'RAM 32GB DDR4 3200MHz'],
            ['Kingston Fury Beast 16GB DDR5 5200', 1100000, 'RAM', 'RAM 16GB DDR5 5200MHz'],
            ['Corsair Vengeance 32GB DDR5 5600', 1800000, 'RAM', 'RAM 32GB DDR5 5600MHz'],
            ['G.Skill Trident Z5 32GB DDR5 6000', 2200000, 'RAM', 'RAM 32GB DDR5 6000MHz'],
            ['TeamGroup T-Force 16GB DDR5 6000', 950000, 'RAM', 'RAM 16GB DDR5 6000MHz'],
            ['Patriot Viper 16GB DDR4 3000', 580000, 'RAM', 'RAM 16GB DDR4 3000MHz'],
            ['Adata XPG 32GB DDR4 3600', 1050000, 'RAM', 'RAM 32GB DDR4 3600MHz'],

            // VGA (10 items)
            ['ASUS GTX 1650 4GB', 2200000, 'VGA', 'VGA NVIDIA GTX 1650 4GB'],
            ['MSI GTX 1660 SUPER 6GB', 3200000, 'VGA', 'VGA NVIDIA GTX 1660 Super 6GB'],
            ['ASUS RTX 3060 12GB', 4800000, 'VGA', 'VGA NVIDIA RTX 3060 12GB'],
            ['MSI RTX 3060 Ti 8GB', 5500000, 'VGA', 'VGA NVIDIA RTX 3060 Ti 8GB'],
            ['ASUS RTX 3070 8GB', 7500000, 'VGA', 'VGA NVIDIA RTX 3070 8GB'],
            ['Gigabyte RTX 4070 12GB', 9500000, 'VGA', 'VGA NVIDIA RTX 4070 12GB'],
            ['MSI RTX 4080 16GB', 14500000, 'VGA', 'VGA NVIDIA RTX 4080 16GB'],
            ['ASUS RX 6600 8GB', 3800000, 'VGA', 'VGA AMD RX 6600 8GB'],
            ['Sapphire RX 6650 XT 8GB', 4500000, 'VGA', 'VGA AMD RX 6650 XT 8GB'],
            ['PowerColor RX 7900 XTX', 18000000, 'VGA', 'VGA AMD RX 7900 XTX 24GB'],

            // Storage (10 items)
            ['Kingston NV2 256GB NVMe', 320000, 'Storage', 'SSD NVMe 256GB'],
            ['Samsung 980 500GB NVMe', 680000, 'Storage', 'SSD NVMe 500GB'],
            ['WD Blue SN570 500GB NVMe', 580000, 'Storage', 'SSD NVMe 500GB'],
            ['Samsung 980 Pro 1TB NVMe', 1400000, 'Storage', 'SSD NVMe 1TB'],
            ['WD Black SN850X 1TB NVMe', 1600000, 'Storage', 'SSD NVMe 1TB Gaming'],
            ['Crucial P3 Plus 2TB NVMe', 1800000, 'Storage', 'SSD NVMe 2TB'],
            ['Seagate Barracuda 1TB HDD', 480000, 'Storage', 'HDD 1TB 7200RPM'],
            ['WD Blue 2TB HDD', 680000, 'Storage', 'HDD 2TB 5400RPM'],
            ['Seagate IronWolf 4TB HDD', 1200000, 'Storage', 'HDD 4TB NAS'],
            ['Kingston Fury Renegade 2TB NVMe', 2500000, 'Storage', 'SSD NVMe 2TB Gaming'],

            // PSU (8 items)
            ['Corsair CV450 450W 80+ Bronze', 420000, 'PSU', 'Power Supply 450W 80+ Bronze'],
            ['Cooler Master MWE 550W 80+ Bronze', 580000, 'PSU', 'Power Supply 550W 80+ Bronze'],
            ['Corsair RM650 650W 80+ Gold', 1200000, 'PSU', 'Power Supply 650W 80+ Gold'],
            ['ASUS TUF Gaming 750W 80+ Gold', 1350000, 'PSU', 'Power Supply 750W 80+ Gold'],
            ['MSI MPG A850GF 850W 80+ Gold', 1500000, 'PSU', 'Power Supply 850W 80+ Gold'],
            ['Corsair RM850x 850W 80+ Gold', 1850000, 'PSU', 'Power Supply 850W 80+ Gold Full Modular'],
            ['Seasonic Focus GX-750 750W', 1600000, 'PSU', 'Power Supply 750W 80+ Gold'],
            ['be quiet! Straight Power 11 650W', 1700000, 'PSU', 'Power Supply 650W 80+ Platinum'],

            // Casing (7 items)
            ['Logitech G Pro X', 1800000, 'Casing', 'Casing Mid Tower Gaming'],
            ['Corsair 4000D Airflow', 1200000, 'Casing', 'Casing Mid Tower ATX'],
            ['NZXT H510 Flow', 1100000, 'Casing', 'Casing Mid Tower Mesh'],
            ['MSI MPG GUNGNIR 110R', 1350000, 'Casing', 'Casing Mid Tower Gaming RGB'],
            ['Lian Li Lancool II Mesh', 1500000, 'Casing', 'Casing Mid Tower Mesh RGB'],
            ['Fractal Design Torrent', 2800000, 'Casing', 'Casing Full Tower High Airflow'],
            ['be quiet! Silent Base 802', 2500000, 'Casing', 'Casing Mid Tower Silent'],

            // Monitor (10 items)
            ['LG 22MK600 22 inch FHD', 1400000, 'Monitor', 'Monitor 22 inch FHD IPS'],
            ['Samsung S24R350 24 inch FHD', 1600000, 'Monitor', 'Monitor 24 inch FHD IPS 75Hz'],
            ['Dell P2422H 24 inch FHD', 2200000, 'Monitor', 'Monitor 24 inch FHD IPS USB-C'],
            ['ASUS VG249Q 24 inch FHD 144Hz', 2500000, 'Monitor', 'Monitor 24 inch Gaming 144Hz'],
            ['LG 27GL850 27 inch QHD 144Hz', 4800000, 'Monitor', 'Monitor 27 inch Nano IPS 144Hz'],
            ['Samsung Odyssey G7 27 inch', 6500000, 'Monitor', 'Monitor 27 inch Curved Gaming 240Hz'],
            ['Dell U2722D 27 inch QHD', 5500000, 'Monitor', 'Monitor 27 inch QHD IPS USB-C'],
            ['ASUS ProArt PA279CV 27 inch 4K', 5800000, 'Monitor', 'Monitor 27 inch 4K IPS'],
            ['LG 32UN880 32 inch 4K', 7200000, 'Monitor', 'Monitor 32 inch 4K IPS USB-C'],
            ['Samsung CRG9 49 inch Ultrawide', 15000000, 'Monitor', 'Monitor 49 inch Curved 32:9'],

            // Keyboard Mouse (10 items)
            ['Logitech MK275 Combo', 280000, 'Keyboard Mouse', 'Keyboard + Mouse Wireless'],
            ['Logitech K380 + M350', 450000, 'Keyboard Mouse', 'Keyboard + Mouse Portable'],
            ['Logitech MK545 Combo', 850000, 'Keyboard Mouse', 'Keyboard + Mouse Wireless Advanced'],
            ['Razer Deathadder V3', 780000, 'Keyboard Mouse', 'Mouse Gaming 6400 DPI'],
            ['Logitech G502 HERO', 850000, 'Keyboard Mouse', 'Mouse Gaming HERO 25K'],
            ['Corsair K65 RGB Mini', 1200000, 'Keyboard Mouse', 'Keyboard Mechanical 60% RGB'],
            ['Razer Huntsman V3 Pro', 1500000, 'Keyboard Mouse', 'Keyboard Mechanical Opto'],
            ['Logitech G915 TKL', 2800000, 'Keyboard Mouse', 'Keyboard Mechanical Wireless RGB'],
            ['SteelSeries Apex Pro', 3200000, 'Keyboard Mouse', 'Keyboard Mechanical Adjustable'],
            ['ASUS ROG Azoth', 3500000, 'Keyboard Mouse', 'Keyboard 75% OLED Gaming'],

            // Aksesoris (15 items)
            ['Cooler Master Hyper 212', 480000, 'Aksesoris', 'CPU Cooler Tower'],
            ['Noctua NH-D15', 1400000, 'Aksesoris', 'CPU Cooler Premium Dual Tower'],
            ['Arctic Liquid Freezer II 240', 1800000, 'Aksesoris', 'AIO Liquid Cooler 240mm'],
            ['Corsair H150i Elite Capellix', 3200000, 'Aksesoris', 'AIO Liquid Cooler 360mm RGB'],
            ['NZXT Kraken X63', 2200000, 'Aksesoris', 'AIO Liquid Cooler 280mm'],
            ['Cable Management Kit', 120000, 'Aksesoris', 'Cable ties, sleeve, clips'],
            ['Thermal Paste Arctic MX-4', 150000, 'Aksesoris', 'Thermal paste 4 gram'],
            ['Thermal Paste Noctua NT-H1', 180000, 'Aksesoris', 'Thermal paste 3.5 gram'],
            ['Case Fan Arctic P12 3-Pack', 280000, 'Aksesoris', 'Fan 120mm 3 pcs'],
            ['Corsair QL120 RGB 3-Pack', 680000, 'Aksesoris', 'Fan 120mm RGB 3 pcs'],
            ['Noctua NF-A12x25 2-Pack', 750000, 'Aksesoris', 'Fan 120mm Premium 2 pcs'],
            ['Webcam Logitech C920', 850000, 'Aksesoris', 'Webcam Full HD 1080p'],
            ['Headset Logitech G332', 580000, 'Aksesoris', 'Headset Gaming Stereo'],
            ['Mousepad Corsair MM300', 280000, 'Aksesoris', 'Mousepad Gaming XL'],
            ['UPS APC 650VA', 680000, 'Aksesoris', 'UPS 650VA Backup Power'],
        ];

        // Insert products
        foreach ($products as $product) {
            Product::create([
                'product_nama' => $product[0],
                'product_harga' => $product[1],
                'product_id_category' => $categoryIds[$product[2]],
                'product_keterangan' => $product[3],
            ]);
        }
    }
}
