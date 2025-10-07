<?php

namespace Tests\Feature;

use App\Models\Claimant;
use App\Models\Deceased;
use App\Models\Sex;
use App\Models\User;
use Database\Seeders\SeederForTesting;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Database\Eloquent\Factories\Factory;
use Tests\TestCase;

class BurialAssistanceCrudTest extends TestCase
{
    /**
     * A basic feature test example.
    */
    
    use RefreshDatabase;
    protected $deceased;
    protected $claimant;
    protected $admin;

    public function setUp(): void
    {
        parent::setUp();

        $this->artisan('migrate');
        $this->seed(SeederForTesting::class);
        $this->deceased = Deceased::factory()->makeOne();
        $this->claimant = Claimant::factory()->makeOne();
        $this->admin = User::factory()->admin()->create();
    }

    public function test_create_assistance() {
        $payLoad = [
            "deceased" => [
                "first_name" => $this->deceased->first_name,
                "middle_name" => $this->deceased->middle_name ?? null,
                "last_name" => $this->deceased->last_name,
                "suffix" => $this->deceased->suffix ?? null,
                "date_of_birth" => $this->deceased->date_of_birth,
                "date_of_death" => $this->deceased->date_of_death,
                "gender" => $this->deceased->gender,
                "address" => $this->deceased->address,
                "barangay_id" => $this->deceased->barangay_id,
                "religion_id" => $this->deceased->religion_id,
            ],
            "claimant" => [
                "first_name" => $this->claimant->first_name,
                "middle_name" => $this->claimant->middle_name ?? null,
                "last_name" => $this->claimant->last_name,
                "suffix" => $this->claimant->suffix ?? null,
                "relationship_to_deceased" => $this->claimant->relationship_to_deceased,
                "mobile_number" => $this->claimant->mobile_number,
                "address" => $this->claimant->address,
                "barangay_id" => $this->claimant->barangay_id,
            ],
            "funeraria" => "Test Funeraria",
            "amount" => 1000,
            "remarks" => "Test Remarks",
        ];
        $response = $this->post(route('guest.burial-assistance.store'), $payLoad);
        $response->assertRedirect();
        $this->assertDatabaseHas('burial_assistances', ['remarks' => 'Test Remarks']);
    }
}
