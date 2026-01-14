<?php

namespace Database\Factories;

use App\Models\Barangay;
use App\Models\Client;
use App\Models\ClientBeneficiary;
use App\Models\ClientBeneficiaryFamily;
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
            'first_name' => $this->faker->firstName(),
            'middle_name' => $this->faker->optional()->lastName(),
            'last_name' => $this->faker->lastName(),
            'suffix' => $this->faker->optional()->randomElement(['Jr.', 'Sr.', 'I', 'II', 'III', 'IV', 'V', 'VI', 'VII', 'VIII', 'IX', 'X']),
            'age' => $this->faker->numberBetween(1, 100),
            'date_of_birth' => $this->faker->date('Y-m-d'),
            'house_no' => $this->faker->buildingNumber(),
            'street' => $this->faker->streetName(),
            'barangay_id' => Barangay::inRandomOrder()->first()->id,
            'district_id' => District::inRandomOrder()->first()->id,
            'city' => 'Taguig City',
            'contact_no' => $this->faker->regexify('09[0-9]{9}'),
        ];
    }

    public function configure()
    {
        return $this->afterCreating(function (Client $client) {
            ClientBeneficiary::factory()->create([
                'client_id' => $client->id,
            ]);

            ClientSocialInfo::factory()->create([
                'client_id' => $client->id,
            ]);

            ClientDemographic::factory()->create([
                'client_id' => $client->id,
            ]);

            ClientBeneficiaryFamily::factory()->count(5)->create([
                'client_id' => $client->id,
            ]);
        });
    }
}
