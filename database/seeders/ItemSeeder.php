<?php

namespace Database\Seeders;

use App\Models\Item;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Faker\Factory as Faker;

class ItemSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // $faker = Faker::create('id_ID');
        // for ($i = 1; $i <= 20; $i++) {

        //     // $harga_beli = $faker->numberBetween(1000, 100000);
        //     $harga_jual = $faker->numberBetween(1000, 100000);
        //     // Looping sampai harga jual > harga beli

        //     $stock_dus = $faker->numberBetween(1, 10); // Contoh jumlah stok dus
        //     $stock_rcg = $faker->numberBetween(1, 10); // Contoh jumlah stok renceng
        //     $stock_pcs = $faker->numberBetween(1, 100); // Contoh jumlah stok pcs
        //     $dus_in_pcs = $faker->numberBetween(10, 20); // Jumlah pcs per dus
        //     $rcg_in_pcs = $faker->numberBetween(5, 10); // Jumlah pcs per renceng

        //     Item::create([
        //         'nama' => $faker->word(),
        //         'merek' => $faker->company,
        //         // 'harga_beli' => $harga_beli,
        //         'harga_jual' => $harga_jual,
        //         'stock_dus' => $stock_dus,
        //         'stock_rcg' => $stock_rcg,
        //         'stock_pcs' => $stock_pcs,
        //         'dus_in_pcs' => $dus_in_pcs,
        //         'rcg_in_pcs' => $rcg_in_pcs,
        //     ]);
        // }

        Item::create([
            'nama' => 'DESAKU BAWANG PUTIH (8 Pack x 2 Rcg x 12 Pcs)',
            'merek' => 'PT. CITRA SATRIA UTAMA',
            'kategori' => 'Bumbu',
            'stock_dus' => 1,
            'stock_rcg' => 16,
            'stock_pcs' => 192,
            'dus_in_pcs' => 192,
            'rcg_in_pcs' => 12,
            'harga_jual' => 10000,
        ]);

        Item::create([
            'nama' => 'DESAKU KUNYIT Rp. 1000 (18 Pack x 2 Rcg x 12 Pcs)',
            'merek' => 'PT. CITRA SATRIA UTAMA',
            'kategori' => 'Bumbu',
            'stock_dus' => 1,
            'stock_rcg' => 36,
            'stock_pcs' => 432,
            'dus_in_pcs' => 432,
            'rcg_in_pcs' => 12,
            'harga_jual' => 10000,
        ]);

        Item::create([
            'nama' => 'LADAKU RP 1.000 ( 8 PACK X 6 RCG X 12 PCS)',
            'merek' => 'PT. CITRA SATRIA UTAMA',
            'kategori' => 'Bumbu',
            'stock_dus' => 1,
            'stock_rcg' => 48,
            'stock_pcs' => 576,
            'dus_in_pcs' => 576,
            'rcg_in_pcs' => 12,
            'harga_jual' => 1000,
        ]);

        Item::create([
            'nama' => 'LIQDISHW MAMA JERUK NIPIS POUCH 680 ML',
            'merek' => 'PT.SRIWIJAYA DISTRIBUSINDO',
            'kategori' => 'Pembersih',
            'stock_dus' => 1,
            'stock_rcg' => 0,
            'stock_pcs' => 12,
            'dus_in_pcs' => 12,
            'rcg_in_pcs' => 0,
            'harga_jual' => 8000,
        ]);

        Item::create([
            'nama' => 'ZINC SHAMPOO ACTIVE FRESH DB SCT 10 ML',
            'merek' => 'PT.SRIWIJAYA DISTRIBUSINDO',
            'kategori' => 'Pembersih',
            'stock_dus' => 1,
            'stock_rcg' => 21,
            'stock_pcs' => 252,
            'dus_in_pcs' => 252,
            'rcg_in_pcs' => 12,
            'harga_jual' => 15000,
        ]);

        Item::create([
            'nama' => 'RAPIKA UNGU PCH 300 ML',
            'merek' => 'PT.SRIWIJAYA DISTRIBUSINDO',
            'kategori' => 'Pembersih',
            'stock_dus' => 1,
            'stock_rcg' => 0,
            'stock_pcs' => 12,
            'dus_in_pcs' => 12,
            'rcg_in_pcs' => 0,
            'harga_jual' => 22000,
        ]);

        Item::create([
            'nama' => 'SOKLIN LIQUID DET ANTI BAC SCT 20ML R12',
            'merek' => 'PT.SRIWIJAYA DISTRIBUSINDO',
            'kategori' => 'Pembersih',
            'stock_dus' => 1,
            'stock_rcg' => 10,
            'stock_pcs' => 120,
            'dus_in_pcs' => 120,
            'rcg_in_pcs' => 12,
            'harga_jual' => 18000,
        ]);

        Item::create([
            'nama' => 'SOKLIN LIQUID DET PERFUME SCT 20ML R12',
            'merek' => 'PT.SRIWIJAYA DISTRIBUSINDO',
            'kategori' => 'Pembersih',
            'stock_dus' => 1,
            'stock_rcg' => 10,
            'stock_pcs' => 120,
            'dus_in_pcs' => 120,
            'rcg_in_pcs' => 12,
            'harga_jual' => 18000,
        ]);

        Item::create([
            'nama' => 'SOKLIN LIQUID DET SOFT SCT 20ML R12',
            'merek' => 'PT.SRIWIJAYA DISTRIBUSINDO',
            'kategori' => 'Pembersih',
            'stock_dus' => 1,
            'stock_rcg' => 10,
            'stock_pcs' => 120,
            'dus_in_pcs' => 120,
            'rcg_in_pcs' => 12,
            'harga_jual' => 18000,
        ]);

        Item::create([
            'nama' => 'SOKLIN LIQUID VIOLET SOFT SCT 20ML R12',
            'merek' => 'PT.SRIWIJAYA DISTRIBUSINDO',
            'kategori' => 'Pembersih',
            'stock_dus' => 1,
            'stock_rcg' => 10,
            'stock_pcs' => 120,
            'dus_in_pcs' => 120,
            'rcg_in_pcs' => 12,
            'harga_jual' => 18000,
        ]);

        Item::create([
            'nama' => 'SEDAAP MIE SINGAPORE SPICY LAKSA 83GR',
            'merek' => 'PT.SRIWIJAYA DISTRIBUSINDO',
            'kategori' => 'Makanan',
            'stock_dus' => 1,
            'stock_rcg' => 0,
            'stock_pcs' => 40,
            'dus_in_pcs' => 40,
            'rcg_in_pcs' => 0,
            'harga_jual' => 2000,
        ]);

        Item::create([
            'nama' => 'ROYALE SWEET FLORAL SCT 13ML',
            'merek' => 'PT.SRIWIJAYA DISTRIBUSINDO',
            'kategori' => 'Pembersih',
            'stock_dus' => 1,
            'stock_rcg' => 24,
            'stock_pcs' => 288,
            'dus_in_pcs' => 288,
            'rcg_in_pcs' => 12,
            'harga_jual' => 20000,
        ]);

        Item::create([
            'nama' => 'ROYALE SOFT HIJAB B.VELVET SCT 13ML',
            'merek' => 'PT.SRIWIJAYA DISTRIBUSINDO',
            'kategori' => 'Pembersih',
            'stock_dus' => 1,
            'stock_rcg' => 24,
            'stock_pcs' => 288,
            'dus_in_pcs' => 288,
            'rcg_in_pcs' => 12,
            'harga_jual' => 20000,
        ]);

        Item::create([
            'nama' => 'ROYALE SOFT RED HOT SUMMER SCT 13ML',
            'merek' => 'PT.SRIWIJAYA DISTRIBUSINDO',
            'kategori' => 'Pembersih',
            'stock_dus' => 1,
            'stock_rcg' => 24,
            'stock_pcs' => 288,
            'dus_in_pcs' => 288,
            'rcg_in_pcs' => 12,
            'harga_jual' => 20000,
        ]);

        Item::create([
            'nama' => 'FORTUNE PP 1 LTR X 24',
            'merek' => 'CV. MEKAR ABADI',
            'kategori' => 'Bahan Pokok',
            'stock_dus' => 1,
            'stock_rcg' => 0,
            'stock_pcs' => 24,
            'dus_in_pcs' => 24,
            'rcg_in_pcs' => 0,
            'harga_jual' => 30000,
        ]);

        Item::create([
            'nama' => 'MINYAK KITA SP 1 LT X 12',
            'merek' => 'CV. MEKAR ABADI',
            'kategori' => 'Bahan Pokok',
            'stock_dus' => 1,
            'stock_rcg' => 0,
            'stock_pcs' => 12,
            'dus_in_pcs' => 12,
            'rcg_in_pcs' => 0,
            'harga_jual' => 25000,
        ]);

        Item::create([
            'nama' => 'FORTUNE RICE PREMIUM @5 KG',
            'merek' => 'CV. MEKAR ABADI',
            'kategori' => 'Bahan Pokok',
            'stock_dus' => 0,
            'stock_rcg' => 0,
            'stock_pcs' => 1,
            'dus_in_pcs' => 1,
            'rcg_in_pcs' => 0,
            'harga_jual' => 50000,
        ]);

        Item::create([
            'nama' => 'FORTUNE RICE PREMIUM @10 KG',
            'merek' => 'CV. MEKAR ABADI',
            'kategori' => 'Bahan Pokok',
            'stock_dus' => 0,
            'stock_rcg' => 0,
            'stock_pcs' => 1,
            'dus_in_pcs' => 1,
            'rcg_in_pcs' => 0,
            'harga_jual' => 100000,
        ]);

        Item::create([
            'nama' => 'FORTUNE RICE PREMIUM @ 20 KG',
            'merek' => 'CV. MEKAR ABADI',
            'kategori' => 'Bahan Pokok',
            'stock_dus' => 0,
            'stock_rcg' => 0,
            'stock_pcs' => 1,
            'dus_in_pcs' => 1,
            'rcg_in_pcs' => 0,
            'harga_jual' => 200000,
        ]);

        Item::create([
            'nama' => 'TEPUNG TULIP 25 KG',
            'merek' => 'CV. MEKAR ABADI',
            'kategori' => 'Bahan Pokok',
            'stock_dus' => 0,
            'stock_rcg' => 0,
            'stock_pcs' => 1,
            'dus_in_pcs' => 1,
            'rcg_in_pcs' => 0,
            'harga_jual' => 15000,
        ]);

        Item::create([
            'nama' => 'GULA PSM 1 KG X 20',
            'merek' => 'CV. MEKAR ABADI',
            'kategori' => 'Bahan Pokok',
            'stock_dus' => 1,
            'stock_rcg' => 0,
            'stock_pcs' => 20,
            'dus_in_pcs' => 20,
            'rcg_in_pcs' => 0,
            'harga_jual' => 15000,
        ]);

        Item::create([
            'nama' => 'SUN KARA CUBE 65 ML X 36 PCS',
            'merek' => 'CV. MEKAR ABADI',
            'kategori' => 'Bahan Pokok',
            'stock_dus' => 1,
            'stock_rcg' => 0,
            'stock_pcs' => 36,
            'dus_in_pcs' => 36,
            'rcg_in_pcs' => 0,
            'harga_jual' => 15000,
        ]);

        Item::create([
            'nama' => 'CASSIE FACIAL 250 GR 2 PLY (1 X 60 PCS)',
            'merek' => 'CV. MEKAR ABADI',
            'kategori' => 'Kesehatan',
            'stock_dus' => 1,
            'stock_rcg' => 0,
            'stock_pcs' => 60,
            'dus_in_pcs' => 60,
            'rcg_in_pcs' => 0,
            'harga_jual' => 15000,
        ]);

        Item::create([
            'nama' => 'CASSIE FACIAL 1000 GR (1 X 12 PCS)',
            'merek' => 'CV. MEKAR ABADI',
            'kategori' => 'Kesehatan',
            'stock_dus' => 1,
            'stock_rcg' => 0,
            'stock_pcs' => 12,
            'dus_in_pcs' => 12,
            'rcg_in_pcs' => 0,
            'harga_jual' => 15000,
        ]);

        Item::create([
            'nama' => 'KERUPUK 1000 CRACKERS ORANGE @5KG/BALL',
            'merek' => 'CV. MEKAR ABADI',
            'kategori' => 'Makanan Kering',
            'stock_dus' => 0,
            'stock_rcg' => 0,
            'stock_pcs' => 1,
            'dus_in_pcs' => 1,
            'rcg_in_pcs' => 0,
            'harga_jual' => 15000,
        ]);

        Item::create([
            'nama' => 'KERUPUK 1000 CRACKERS PUTIH @5KG/BALL',
            'merek' => 'CV. MEKAR ABADI',
            'kategori' => 'Makanan Kering',
            'stock_dus' => 0,
            'stock_rcg' => 0,
            'stock_pcs' => 1,
            'dus_in_pcs' => 1,
            'rcg_in_pcs' => 0,
            'harga_jual' => 15000,
        ]);

        Item::create([
            'nama' => 'MINYAKKITA PP 1 LT X 12',
            'merek' => 'PT. EVERBRIGHT',
            'kategori' => 'Bahan Pokok',
            'stock_dus' => 1,
            'stock_rcg' => 0,
            'stock_pcs' => 12,
            'dus_in_pcs' => 12,
            'rcg_in_pcs' => 0,
            'harga_jual' => 25000,
        ]);






    }
}
