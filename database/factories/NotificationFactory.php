<?php

namespace Database\Factories;

use App\Models\Notification;
use Illuminate\Database\Eloquent\Factories\Factory;
use Str;

/**
 * @extends Factory<Notification>
 */
class NotificationFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'id' => (string) Str::uuid(),
            'payload' => json_encode([
                'subject' => $this->faker->sentence(3),
                'body' => $this->faker->sentence(10),
            ]),
            'created_at' => $this->faker->dateTimeBetween('-1 week', now()),
        ];
    }
}
