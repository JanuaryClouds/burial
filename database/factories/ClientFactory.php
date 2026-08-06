<?php

namespace Database\Factories;

use App\Models\Barangay;
use App\Models\Client;
use App\Models\ClientDemographic;
use App\Models\ClientSocialInfo;
use App\Models\District;
use Illuminate\Database\Eloquent\Factories\Factory;

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
        return [
            'date_of_birth' => $this->faker->date('Y-m-d'),
            'house_no' => $this->faker->buildingNumber(),
            'street' => $this->faker->streetName(),
            'barangay_id' => Barangay::inRandomOrder()->first()->id,
            'district_id' => District::inRandomOrder()->first()->id,
            'city' => 'Taguig City',
            'contact_number' => $this->faker->regexify('09[0-9]{9}'),
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
