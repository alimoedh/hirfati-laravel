<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends Factory<User>
 */
class UserFactory extends Factory
{
    protected static ?string $password;

    public function definition(): array
    {
        return [
            'full_name'           => fake()->name(),
            'email'               => fake()->unique()->safeEmail(),
            'phone'               => fake()->unique()->numerify('077#######'),
            'password'            => static::$password ??= Hash::make('password'),
            'role'                => 'client',
            'avatar'              => 'default-avatar.png',
            'is_verified'         => false,
            'is_active'           => true,
            'remember_token'      => Str::random(10),
            'loyalty_points'      => 0,
            'total_points_earned' => 0,
            'total_points_spent'  => 0,
        ];
    }

    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_verified' => false,
        ]);
    }

    public function craftsman(): static
    {
        return $this->state(fn (array $attributes) => [
            'role' => 'craftsman',
        ]);
    }

    public function admin(): static
    {
        return $this->state(fn (array $attributes) => [
            'role'        => 'admin',
            'is_verified' => true,
        ]);
    }
}
