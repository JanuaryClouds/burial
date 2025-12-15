<?php

namespace Tests\Feature;

use App\Models\Deceased;
use Database\Seeders\SeederForTesting;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DeceasedCrudTest extends TestCase
{
    use RefreshDatabase;

    protected $deceased;

    protected function setUp(): void
    {
        parent::setUp();
        $this->artisan('migrate');
        $this->seed(SeederForTesting::class);
        $this->deceased = Deceased::factory()->makeOne();
        $this->deceased->date_of_death = $this->deceased->date_of_death->format('Y-m-d');
    }

    public function test_create_deceased()
    {
        $response = Deceased::create($this->deceased->toArray());
        $this->assertDatabaseHas('deceased', $this->deceased->toArray());
    }
}
