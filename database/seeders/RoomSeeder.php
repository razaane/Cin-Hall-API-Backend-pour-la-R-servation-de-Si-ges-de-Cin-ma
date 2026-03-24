<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RoomSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('rooms')->insert([
            [
                'name' => 'Salle 1',
                'type' => 'normale',
                'total_seats' => 100,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Salle 2',
                'type' => 'vip',
                'total_seats' => 50,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Salle 3',
                'type' => 'normale',
                'total_seats' => 80,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}