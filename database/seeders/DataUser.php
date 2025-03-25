<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class DataUser extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user = [
            [
                'uuid' => "4e2593f0-4d36-4284-8d9b-54b846f4b25c",
                'name' => "admin",
                'email' => "admin@gmail.com",
                'password' => Hash::make('admin'),
            ]
        ];

        DB::table('users')->insert($user);
    }
}
