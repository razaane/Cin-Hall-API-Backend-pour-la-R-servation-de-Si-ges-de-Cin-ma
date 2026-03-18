<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1️⃣ Genres
        DB::table('genres')->insert([
            ['name'=>'Action','created_at'=>now(),'updated_at'=>now()],
            ['name'=>'Aventure','created_at'=>now(),'updated_at'=>now()],
            ['name'=>'Comédie','created_at'=>now(),'updated_at'=>now()],
        ]);

        // 2️⃣ Films
        DB::table('films')->insert([
            [
                'genre_id'=>1,
                'title'=>'Avengers',
                'description'=>'Les super-héros unissent leurs forces pour sauver le monde.',
                'duration'=>150,
                'min_age'=>12,
                'created_at'=>now(),
                'updated_at'=>now(),
            ],
            [
                'genre_id'=>2,
                'title'=>'Spiderman',
                'description'=>'Peter Parker combat les vilains de New York.',
                'duration'=>130,
                'min_age'=>10,
                'created_at'=>now(),
                'updated_at'=>now(),
            ],
        ]);

        // 3️⃣ Users
        DB::table('users')->insert([
            ['name'=>'Test User 1','email'=>'test1@test.com','password'=>Hash::make('123456')],
            ['name'=>'Test User 2','email'=>'test2@test.com','password'=>Hash::make('123456')],
        ]);

        // 4️⃣ Rooms
        DB::table('rooms')->insert([
            ['name'=>'Salle 1','type'=>'normale','total_seats'=>100,'created_at'=>now(),'updated_at'=>now()],
            ['name'=>'Salle 2','type'=>'vip','total_seats'=>50,'created_at'=>now(),'updated_at'=>now()],
        ]);

        // 5️⃣ Seances
        DB::table('seances')->insert([
            ['room_id'=>1,'film_id'=>1,'start_time'=>Carbon::now()->addHours(1),'type'=>'normale','created_at'=>now(),'updated_at'=>now()],
            ['room_id'=>2,'film_id'=>2,'start_time'=>Carbon::now()->addHours(2),'type'=>'vip','created_at'=>now(),'updated_at'=>now()],
        ]);

        // 6️⃣ Reservations
        DB::table('reservations')->insert([
            ['user_id'=>1,'seance_id'=>1,'number_of_seats'=>2,'status'=>'pending','created_at'=>now(),'updated_at'=>now()],
            ['user_id'=>2,'seance_id'=>2,'number_of_seats'=>4,'status'=>'confirmed','created_at'=>now(),'updated_at'=>now()],
        ]);
    }
}
