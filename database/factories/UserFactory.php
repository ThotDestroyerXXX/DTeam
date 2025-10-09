<?php

namespace Database\Factories;

use App\Enums\Role;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory
{
    /**
     * The current password being used by the factory.
     */
    protected static ?string $password;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        /* user data
         $table->ulid('id')->primary();
            $table->string('nickname')->unique();
            $table->string('real_name');
            $table->string('profile_picture_url')->nullable();
            $table->enum('role', array_column(Role::cases(), 'value'))->default(Role::USER->value);
            $table->text('bio')->nullable();
            $table->string('unique_code')->unique();
            $table->bigInteger('wallet')->default(0);
            $table->bigInteger('point')->default(0);
            $table->string('background_url')->nullable();
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->foreignId('country_id')->nullable()->constrained('countries')->nullOnDelete(); -> 195 countries
            $table->rememberToken();
            $table->timestamps();
        */
        return [
            'nickname' => fake()->unique()->userName(),
            'real_name' => fake()->name(),
            'profile_picture_url' => fake()->imageUrl(),
            'role' => Role::USER->value,
            'bio' => fake()->text(),
            'unique_code' => fake()->numerify('##########'),
            'wallet' => fake()->numberBetween(0, 100000),
            'point' => fake()->numberBetween(0, 1000),
            'background_url' => fake()->imageUrl(),
            'email' => fake()->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password' => static::$password ??= Hash::make('password'),
            'remember_token' => Str::random(10),
            'country_id' => fake()->numberBetween(1, 195),
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }

    /**
     * Indicate that the model's email address should be unverified.
     */
    public function unverified(): static
    {
        return $this->state(fn(array $attributes) => [
            'email_verified_at' => null,
        ]);
    }
}
