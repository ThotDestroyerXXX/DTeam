<?php

namespace Database\Seeders;

use App\Models\GameLibrary;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class GameLibrarySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        GameLibrary::factory(100)->create();
    }
}
