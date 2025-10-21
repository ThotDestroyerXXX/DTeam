<?php

namespace Database\Seeders;

use App\Enums\Role;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class FriendListSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // select 5 random user id for every user that is of user role, and insert into friend_lists table
        $userIds = User::where('role', Role::USER->value)->pluck('id')->toArray();

        foreach ($userIds as $userId) {
            $possibleFriends = array_diff($userIds, [$userId]);
            $friendIds = (array)array_rand(array_flip($possibleFriends), 5);

            foreach ($friendIds as $friendId) {
                DB::table('friend_lists')->insert([
                    'id' => Str::ulid()->toString(),
                    'user_id' => $userId,
                    'friend_id' => $friendId,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }
}
