<?php

namespace Tests\Feature;

use App\Models\User;
use Tests\TestCase;

class BeneficiaryFamilyCompositionTest extends TestCase
{
    public function test_partial_renders_family_fields_as_vanilla_js(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->get(route('beneficiary.create'))
            ->assertOk()
            ->assertSee('name="fam_name[]"', false)
            ->assertSee('name="fam_sex_id[]"', false)
            ->assertSee('name="fam_age[]"', false)
            ->assertSee('name="fam_civil_id[]"', false)
            ->assertSee('name="fam_relationship_id[]"', false)
            ->assertSee('name="fam_occupation[]"', false)
            ->assertSee('name="fam_income[]"', false)
            ->assertSee('id="family-member-template"', false)
            ->assertSee('id="family-add-btn"', false)
            ->assertSee('data-family-remove', false)
            ->assertSee('data-control="select2"', false)
            ->assertSee('window.initSelect2', false);

        // The old Livewire/AlpineJS bindings from the family composition partial
        // are gone (scoped to strings unique to the previous implementation).
        $this->actingAs($user)
            ->get(route('beneficiary.create'))
            ->assertDontSee('wire:click="addMember"', false)
            ->assertDontSee('wire:model="members.', false)
            ->assertDontSee('family-limit-reached', false)
            ->assertDontSee('x-model="family.', false);
    }

    public function test_partial_starts_with_a_single_empty_row(): void
    {
        $user = User::factory()->create();

        $html = $this->actingAs($user)
            ->get(route('beneficiary.create'))
            ->assertOk()
            ->getContent();

        $start = strpos($html, 'id="family-members"');
        $end = strpos($html, '<template', $start);
        $renderedRows = substr($html, $start, $end - $start);

        $this->assertSame(1, substr_count($renderedRows, 'class="family-member-row"'));
    }

    public function test_partial_restores_old_values_after_failed_validation(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->withSession([
                '_old_input' => [
                    'fam_name' => ['Juan Dela Cruz', 'Maria Santos'],
                    'fam_sex_id' => [1, 2],
                    'fam_age' => [30, 25],
                    'fam_civil_id' => [1, 2],
                    'fam_relationship_id' => [1, 2],
                    'fam_occupation' => ['Driver', 'Teacher'],
                    'fam_income' => [15000, 20000],
                ],
            ])
            ->get(route('beneficiary.create'))
            ->assertOk()
            ->assertSee('value="Juan Dela Cruz"', false)
            ->assertSee('value="Maria Santos"', false)
            ->assertSee('value="30"', false)
            ->assertSee('value="Driver"', false)
            ->assertSee('value="15000"', false);
    }
}
