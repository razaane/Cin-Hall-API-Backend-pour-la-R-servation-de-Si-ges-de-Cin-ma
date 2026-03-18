<?php
namespace Database\Seeders;

use App\Models\Genre;
use Illuminate\Database\Seeder;

class GenreSeeder extends Seeder
{
    public function run(): void
    {
        $genres = [
            'Action',
            'Comedie',
            'Drame',
            'Horreur',
            'Science-Fiction',
            'Romance',
            'Thriller',
            'Animation',
            'Documentaire',
            'Aventure',
        ];

        foreach ($genres as $genre) {
            Genre::firstOrCreate(['name' => $genre]);
        }
    }
}