<?php

namespace Database\Factories;

use App\Models\Barangay;
use App\Models\Beneficiary;
use App\Models\BeneficiaryFamily;
use App\Models\Client;
use App\Models\ClientDemographic;
use App\Models\ClientSocialInfo;
use App\Models\District;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Client>
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
            'id' => $this->faker->uuid(),
            'date_of_birth' => $this->faker->date('Y-m-d'),
            'house_no' => $this->faker->buildingNumber(),
            'street' => $this->faker->streetName(),
            'barangay_id' => Barangay::inRandomOrder()->first()->id,
            'district_id' => District::inRandomOrder()->first()->id,
            'city' => 'Taguig City',
        ];
    }

    public function configure()
    {
        return $this->afterCreating(function (Client $client) {
            Beneficiary::factory()->create([
                'client_id' => $client->id,
            ]);

            ClientSocialInfo::factory()->create([
                'client_id' => $client->id,
            ]);

            ClientDemographic::factory()->create([
                'client_id' => $client->id,
            ]);

            BeneficiaryFamily::factory()->count(5)->create([
                'client_id' => $client->id,
            ]);
        });
    }
}
