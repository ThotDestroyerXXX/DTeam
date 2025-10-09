<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            AgeRatingSeeder::class,
            CountrySeeder::class,
            ItemSeeder::class,
            RatingTypeSeeder::class,
            UserSeeder::class,
            PublisherSeeder::class,
            GenreSeeder::class,
            GameSeeder::class,
            GameLibrarySeeder::class,
        ]);
    }
}
