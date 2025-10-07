<?php

namespace Tests\Feature;

use App\Models\Barangay;
use App\Models\Deceased;
use App\Models\Religion;
use App\Models\Sex;
use Database\Seeders\SeederForTesting;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class DeceasedCrudTest extends TestCase
{

    use RefreshDatabase;
    protected $deceased;
    public function setUp(): void {
        parent::setUp();
        $this->artisan('migrate');
        $this->seed(SeederForTesting::class);
        $this->deceased = Deceased::factory()->makeOne();
        $this->deceased->date_of_death = $this->deceased->date_of_death->format('Y-m-d');
    }

    public function test_create_deceased() {
        $response = Deceased::create($this->deceased->toArray());
        $this->assertDatabaseHas('deceased', $this->deceased->toArray());
    }
}
