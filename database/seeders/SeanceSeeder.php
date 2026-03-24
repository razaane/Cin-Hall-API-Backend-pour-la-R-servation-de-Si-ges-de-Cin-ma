<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class SeanceSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('seances')->insert([
            [
                'film_id' => 1,
                'room_id' => 1,
                'start_time' => Carbon::now()->addHours(2),
                'language' => 'FR',
                'type' => 'normale',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'film_id' => 2,
                'room_id' => 2,
                'start_time' => Carbon::now()->addHours(4),
                'language' => 'EN',
                'type' => 'vip',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'film_id' => 1,
                'room_id' => 3,
                'start_time' => Carbon::now()->addDay(),
                'language' => 'AR',
                'type' => 'normale',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}