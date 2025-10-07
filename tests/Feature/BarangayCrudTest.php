<?php

namespace Tests\Feature;

use App\Models\Barangay;
use App\Models\District;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class BarangayCrudTest extends TestCase
{
    /**
     * A basic feature test example.
     */

    use RefreshDatabase;
    protected $superadmin;
    protected $district;

    protected function setUp(): void
    {
        parent::setUp();
        Role::firstOrCreate(['name' => 'superadmin']);
        $this->district = District::factory()->create();
        $this->superadmin = User::factory()->superadmin()->create();
        $this->actingAs($this->superadmin);
    }
    
    public function test_create_barangay()
    {
        $payload = [
            'name' => 'Test Barangay',
            'district_id' => $this->district->id,
            'remarks' => 'test remarks',
        ];
        
        $response = $this->post(route('superadmin.cms.store', ['type' => 'barangays']), $payload);

        $response->assertRedirect();
        $this->assertDatabaseHas('barangays', $payload);
    }

    public function test_list_barangay() {
        Barangay::factory()->count(5)->create();
        $response = $this->get(route('superadmin.cms.barangays'));
        $response->assertOk();
        $response->assertSee('Barangays');
    }

    public function test_update_barangay() {
        $barangay = Barangay::factory()->create();
        $updateData = ['name' => 'Update barangay', 'district_id' => $barangay->district_id];
        $response = $this->post(route('superadmin.cms.update', ['type' => 'barangays', 'id' => $barangay->id]), $updateData);
        $response->assertRedirect();
        $this->assertDatabaseHas('barangays', ['id' => $barangay->id]);
    }

    public function test_delete_barangay() {
        $barangay = Barangay::factory()->create();
        $response = $this->post(route('superadmin.cms.delete', ['type' => 'barangays', 'id' => $barangay->id]));
        $response->assertRedirect();
        $this->assertDatabaseMissing('barangays', ['id' => $barangay->id]);
    }
}
