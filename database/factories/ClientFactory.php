<?php

namespace Database\Factories;

use App\Models\Barangay;
use App\Models\Client;
use App\Models\ClientDemographic;
use App\Models\ClientSocialInfo;
use App\Models\District;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Client>
 */
class ClientFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    protected $model = Client::class;

    public function definition()
    {
        // $isFromPateros = rand(0, 9) == 9;

        return [
            'date_of_birth' => $this->faker->date('Y-m-d'),
            'region_code' => '13',
            // 'province_code' => $isFromPateros ? '815' : null,
            // 'municipality_code' => $isFromPateros ? '817' : null,
            // 'barangay_code' => $isFromPateros ? (string) rand(1, 10) : (string) rand(1, 38),
            'street' => $this->faker->streetName(),
            'house_no' => $this->faker->buildingNumber(),
            'contact_number' => $this->faker->regexify('09[0-9]{9}'),
            'created_at' => $this->faker->dateTimeBetween(now()->subWeek(), now()),
        ];
    }

    public function configure()
    {
        return $this->afterCreating(function (Client $client) {
            ClientSocialInfo::factory()->create([
                'client_uuid' => $client->uuid,
            ]);

            ClientDemographic::factory()->create([
                'client_uuid' => $client->uuid,
            ]);
        });
    }
}
