<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MasterProduct extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $product = [
            [
                'uuid' => "f9a9bf51-10b9-4e8e-b6e2-796debc96ac2",
                'category_id' => "23b5a803-69e4-4c3b-b0f4-19e2f3747c08",
                'name' => "tenda",
                'kode_barang' => "PRD-001",
                'harga' => 12000
            ],
            [
                'uuid' => "29302baf-c35d-4cfe-a57b-f122dd4c5778",
                'category_id' => "25c1f3b6-5cd9-4e5d-9850-98358f615c7e",
                'name' => "stand",
                'kode_barang' => "PRD-002",
                'harga' => 12000
            ]
        ];

        DB::table('master_product')->insert($product);
    }
}
