<?php
namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        User::create([
            'name'     => 'Admin',
            'email'    => 'admin@gmail.com',
            'password' => Hash::make('123456789'),
            'role'     => 'admin'
        ]);

        User::create([
            'name'     => 'Razane',
            'email'    => 'razane@gmail.com',
            'password' => Hash::make('123456789'),
            'role'     => 'user'
        ]);

        User::create([
            'name'     => 'Khaoula',
            'email'    => 'khaoula@gmail.com',
            'password' => Hash::make('123456789'),
            'role'     => 'user'
        ]);

        User::create([
            'name'     => 'hanane',
            'email'    => 'hanane@gmail.com',
            'password' => Hash::make('123456789'),
            'role'     => 'user'
        ]);
    }
}