<?php
namespace Database\Seeders;

use App\Models\Film;
use App\Models\Genre;
use Illuminate\Database\Seeder;

class FilmSeeder extends Seeder
{
    public function run(): void
    {
        $films = [
            [
                'title'       => 'Inception',
                'description' => 'Un voleur qui entre dans les rêves des gens.',
                'duration'    => 148,
                'min_age'     => 13,
                'trailer_url' => 'https://youtube.com/watch?v=YoHD9XEInc0',
                'genre'       => 'Science-Fiction',
            ],
            [
                'title'       => 'The Dark Knight',
                'description' => 'Batman affronte le Joker à Gotham City.',
                'duration'    => 152,
                'min_age'     => 13,
                'trailer_url' => 'https://youtube.com/watch?v=EXeTwQWrcwY',
                'genre'       => 'Action',
            ],
            [
                'title'       => 'Interstellar',
                'description' => 'Des astronautes voyagent à travers un trou de ver.',
                'duration'    => 169,
                'min_age'     => 10,
                'trailer_url' => 'https://youtube.com/watch?v=zSWdZVtXT7E',
                'genre'       => 'Science-Fiction',
            ],
            [
                'title'       => 'The Shining',
                'description' => 'Une famille isolée dans un hôtel hanté.',
                'duration'    => 144,
                'min_age'     => 18,
                'trailer_url' => 'https://youtube.com/watch?v=S014oGZiSdI',
                'genre'       => 'Horreur',
            ],
            [
                'title'       => 'Intouchables',
                'description' => 'L\'amitié entre un riche paralysé et son aide-soignant.',
                'duration'    => 112,
                'min_age'     => 0,
                'trailer_url' => 'https://youtube.com/watch?v=xl-rKSd6cV0',
                'genre'       => 'Comédie',
            ],
            [
                'title'       => 'Titanic',
                'description' => 'Une histoire d\'amour sur le célèbre navire.',
                'duration'    => 195,
                'min_age'     => 10,
                'trailer_url' => 'https://youtube.com/watch?v=2e-eXJ6HgkQ',
                'genre'       => 'Romance',
            ],
            [
                'title'       => 'Parasite',
                'description' => 'Une famille pauvre s\'infiltre chez une famille riche.',
                'duration'    => 132,
                'min_age'     => 16,
                'trailer_url' => 'https://youtube.com/watch?v=5xH0HfJHsaY',
                'genre'       => 'Thriller',
            ],
            [
                'title'       => 'The Lion King',
                'description' => 'Simba doit reprendre sa place de roi.',
                'duration'    => 88,
                'min_age'     => 0,
                'trailer_url' => 'https://youtube.com/watch?v=7TavVZMewpY',
                'genre'       => 'Animation',
            ],
        ];

        foreach ($films as $filmData) {
            $genre = Genre::where('name', $filmData['genre'])->first();

            Film::firstOrCreate(
                ['title' => $filmData['title']],
                [
                    'description' => $filmData['description'],
                    'duration'    => $filmData['duration'],
                    'min_age'     => $filmData['min_age'],
                    'trailer_url' => $filmData['trailer_url'],
                    'image'       => null,
                    'genre_id'    => $genre->id,
                ]
            );
        }
    }
}