<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Role;

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
        return [
            'first_name' => $this->faker->firstName(),
            'middle_name' => $this->faker->optional()->lastName(),
            'last_name' => $this->faker->lastName(),
            'email' => fake()->unique()->safeEmail(),
            // 'email_verified_at' => now(),
            'contact_number' => $this->faker->regexify('09[0-9]{9}'),
            'password' => static::$password ??= Hash::make('password'),
            // 'remember_token' => Str::random(10),
        ];
    }

    /**
     * Indicate that the model's email address should be unverified.
     */
    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }

    public function superadmin()
    {
        return $this->afterCreating(function ($user) {
            $role = Role::firstOrCreate(['name' => 'superadmin']);
            $user->assignRole($role);
        });
    }

    public function admin()
    {
        return $this->afterCreating(function ($user) {
            $role = Role::firstOrCreate(['name' => 'admin']);
            $user->assignRole($role);
        });
    }
}
