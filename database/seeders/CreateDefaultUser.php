<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class CreateDefaultUser extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('users')->insert(
            [
                'name' => 'Ricardo',
                'email' => 'ricardo@gmail.com',
                'password' => '123456',
                'created_at' => now(),
                'updated_at' => now()
            ]
        );
    }
}
