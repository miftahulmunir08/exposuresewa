<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MasterCustomer extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $product = [
            [
                'uuid' => "3c851a69-b5d5-438b-b692-703c5c63c845",
                'name' => "andi",
                'email' => "andi@sewabarang.com",
                'phone' => "08551886763",
                'jabatan' => "staff indomart",
            ],
            [
                'uuid' => "e94ff49e-237b-4d11-9519-7db5bd899cf9",
                'name' => "amir",
                'email' => "amir@sewabarang.com",
                'phone' => "08551886763",
                'jabatan' => "",
            ]
        ];

        DB::table('master_customer')->insert($product);
    }
}
