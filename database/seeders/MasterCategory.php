<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MasterCategory extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            [
                'uuid' => "23b5a803-69e4-4c3b-b0f4-19e2f3747c08",
                'name' => "pendakian",
            ],
            [
                'uuid' => "25c1f3b6-5cd9-4e5d-9850-98358f615c7e",
                'name' => "jualan",
            ]
        ];

        DB::table('master_product_category')->insert($categories);
    }
}
