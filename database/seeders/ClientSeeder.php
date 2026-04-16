<?php

namespace Database\Seeders;

use App\Models\Client;
use App\Models\Notification;
use App\Models\User;
use Illuminate\Database\Seeder;
use Str;

class ClientSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = User::factory()->count(10)->create([
            'citizen_id' => fn () => Str::uuid()->toString(),
        ]);

        Client::factory()->count(10)->create([
            'user_id' => fn () => $users->random()->id,
        ]);

        Notification::factory()->count(10)->create([
            'notifiable_id' => fn () => $users->random()->id,
            'notifiable_type' => User::class,
        ]);
    }
}
