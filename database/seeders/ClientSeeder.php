<?php

namespace Database\Seeders;

use App\Models\Client;
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
        
        Client::factory()->count(10)->create()
            ->each(function ($client) use ($users) {
                $client->user_id = $users->random()->id;
                $client->save();
            });
    }
}
