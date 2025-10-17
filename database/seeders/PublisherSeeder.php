<?php

namespace Database\Seeders;

use App\Enums\Role;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class PublisherSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get original publishers from JSON
        $originalPublishers = json_decode(File::get('database/data/publishers_data.json'), true);

        // Get available user IDs from database
        $userIds = DB::table('users')->pluck('id')->toArray();

        // Exit if no users found
        if (empty($userIds)) {
            echo "No users found in database. Please run the UserSeeder first.\n";
            return;
        }

        $publishers = [];

        // Process each publisher and assign a random user_id
        foreach ($originalPublishers as $publisher) {
            // Pick a random user_id
            $randomUserId = null;
            do {
                $randomUserId = $userIds[array_rand($userIds)];
            } while (in_array($randomUserId, array_column($publishers, 'user_id')));


            // Add ULID as id and user_id to publisher data
            $publisher['id'] = Str::ulid()->toString();
            $publisher['user_id'] = $randomUserId;
            $publisher['created_at'] = now();
            $publisher['updated_at'] = now();

            // Add to collection
            $publishers[] = $publisher;

            DB::table('users')->where('id', $randomUserId)->update(['role' => Role::PUBLISHER->value]);
        }

        // Insert all publishers with user_id
        DB::table('publishers')->insert($publishers);
    }
}
