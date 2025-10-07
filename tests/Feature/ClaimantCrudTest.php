<?php

namespace Tests\Feature;

use App\Models\Claimant;
use Database\Seeders\SeederForTesting;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ClaimantCrudTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    use RefreshDatabase;
    protected $claimant;
    public function setUp(): void {
        parent::setUp();
        $this->artisan('migrate');
        $this->seed(SeederForTesting::class);
        $this->claimant = Claimant::factory()->makeOne();
    }

    public function test_create_claimant() {
        $response = Claimant::create($this->claimant->toArray());
        $this->assertDatabaseHas('claimants', $this->claimant->toArray());
    }
}
