<?php

namespace Tests\Feature;

use App\Services\PsaClassificationService;
use Tests\TestCase;

class PsaClassificationTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    public function test_get_regions()
    {
        $regions = (new PsaClassificationService)->getRegions();

        $this->assertIsArray($regions);
        $this->assertNotEmpty($regions);
    }
}
