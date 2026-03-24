<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ReservationSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('reservations')->insert([
            [
                'user_id' => 1,
                'seance_id' => 1,
                'status' => 'pending',
                'number_of_seats' => '2',
                'expires_at' => Carbon::now()->addMinutes(30),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'user_id' => 1,
                'seance_id' => 2,
                'status' => 'confirmed',
                'number_of_seats' => '3',
                'expires_at' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'user_id' => 2,
                'seance_id' => 1,
                'status' => 'paid',
                'number_of_seats' => '1',
                'expires_at' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'user_id' => 3,
                'seance_id' => 3,
                'status' => 'canceled',
                'number_of_seats' => '4',
                'expires_at' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}