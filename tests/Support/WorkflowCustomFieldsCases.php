<?php

namespace Tests\Support;

use App\Livewire\Workflow\Edit;
use App\Livewire\Workflow\History\Create;
use App\Livewire\Workflow\History\Index;
use App\Livewire\Workflow\History\Show;
use App\Models\CustomField;
use App\Models\Recommendation;
use App\Models\User;
use App\Models\Workflow;
use App\Models\WorkflowHistory;
use App\Models\WorkflowStage;
use App\Services\RemarkService;
use App\Services\WorkflowCustomFieldService;
use App\Services\WorkflowHistoryService;
use App\Services\WorkflowVersionService;
use Database\Seeders\WorkflowSeeder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Livewire\Livewire;
use Spatie\Permission\Models\Role;

trait WorkflowCustomFieldsCases
{
    public function test_help_text_is_versioned_editable_and_rendered_for_the_pinned_stage(): void
    {
        $data = $this->customDefinition();
        $data['stages'][0]['custom_fields'][0]['help_text'] = '  Include leading zeros <exactly as printed>.  ';
        $version = app(WorkflowVersionService::class)->publish($data);
        $stage = $version->stages()->with('customFields')->first();
        $this->assertSame('Include leading zeros <exactly as printed>.', $stage->customFields->first()->help_text);
        $this->assertSame($version->uuid, app(WorkflowVersionService::class)->publish($data, $version)->uuid);
        $application = $this->application();
        $this->recommend($application, $version);
        $editor = Livewire::test(Edit::class, ['workflow' => $version])
            ->assertSet('stages.0.custom_fields.0.help_text', 'Include leading zeros <exactly as printed>.')
            ->set('stages.0.custom_fields.0.help_text', 'A different instruction.')
            ->call('save')->assertHasNoErrors();
        $this->assertSame(2, $editor->get('workflow')->version_number);
        $this->assertSame('Include leading zeros <exactly as printed>.', $stage->customFields->first()->fresh()->help_text);
        Livewire::test(Create::class, ['application' => $application])
            ->set('dateIn', '2026-10-26T09:00:00')->set('toStageUuid', $stage->uuid)
            ->assertSet('customFields.0.help_text', 'Include leading zeros <exactly as printed>.')
            ->assertSee('Include leading zeros <exactly as printed>.')
            ->assertDontSee('A different instruction.');
    }

    public function test_help_text_defaults_to_null_and_rejects_invalid_values(): void
    {
        $service = app(WorkflowCustomFieldService::class);
        foreach ([null, '', '   ', false] as $empty) {
            $fields = $this->fields();
            $fields[0]['help_text'] = $empty;
            $this->assertNull($service->normalize($fields, 'stages.0.custom_fields')[0]['help_text']);
        }
        $this->assertNull($service->normalize($this->fields(), 'stages.0.custom_fields')[0]['help_text']);
        foreach ([str_repeat('a', 1001), ['invalid']] as $invalid) {
            $fields = $this->fields();
            $fields[0]['help_text'] = $invalid;
            try {
                $service->normalize($fields, 'stages.0.custom_fields');
                $this->fail('Invalid help text was accepted.');
            } catch (ValidationException $exception) {
                $this->assertArrayHasKey('stages.0.custom_fields.0.help_text', $exception->errors());
            }
        }
    }

    public function test_timeline_details_remain_visible_after_clicks_and_polling(): void
    {
        (require database_path('migrations/data/2026_07_10_200015_create_remarks_table.php'))->up();
        $version = app(WorkflowVersionService::class)->publish($this->customDefinition());
        $stage = $version->stages()->with('customFields')->first();
        $application = $this->application();
        $run = $this->recommend($application, $version);
        $history = $this->record($run, $stage, $this->values($stage));
        $button = "showHistoryDetails('".$history->uuid."')";
        Livewire::test(Index::class, ['application' => $application])
            ->assertSee($button, false)
            ->call('showHistoryDetails', $history->uuid)
            ->assertDispatched('load-details', historyUuid: $history->uuid)
            ->assertSee($button, false)
            ->call('clearSelectedHistory')->assertSee($button, false)
            ->call('showRemarks', 'WorkflowHistory', $history->uuid)->assertSee($button, false)
            ->call('refresh')->assertSee($button, false);
    }

    public function test_timeline_details_are_hidden_only_when_fields_and_values_are_absent(): void
    {
        $recommendation = new Recommendation;
        $recommendation->forceFill(['uuid' => (string) Str::uuid(), 'created_at' => now()]);
        $recommendation->setRelation('funeralAssistanceType', null);
        $recommendation->setRelation('workflow', null);
        $recommendation->setRelation('remarks', new Collection);
        $histories = new Collection;
        foreach ([[0, 0], [1, 0], [0, 1]] as [$fieldCount, $valueCount]) {
            $stage = new WorkflowStage;
            $stage->forceFill(['name' => 'Stage', 'custom_fields_count' => $fieldCount]);
            $history = new WorkflowHistory;
            $history->forceFill(['uuid' => (string) Str::uuid(), 'date_in' => now(), 'custom_values_count' => $valueCount]);
            $history->setRelation('toStage', $stage);
            $history->setRelation('remarks', new Collection);
            $history->preventsLazyLoading = true;
            $histories->push($history);
        }
        $recommendation->setRelation('workflowHistory', $histories);
        $recommendation->preventsLazyLoading = true;
        $html = view('workflow.history.index.partials.recommendation-timeline', [
            'recommendations' => new Collection([$recommendation]),
        ])->render();
        $this->assertStringNotContainsString("showHistoryDetails('".$histories[0]->uuid."')", $html);
        $this->assertStringContainsString("showHistoryDetails('".$histories[1]->uuid."')", $html);
        $this->assertStringContainsString("showHistoryDetails('".$histories[2]->uuid."')", $html);
        $this->assertSame(4, substr_count($html, 'showRemarks('));
    }

    public function test_failure_after_custom_values_rolls_back_history_values_and_progress(): void
    {
        $version = app(WorkflowVersionService::class)->publish($this->customDefinition());
        $stage = $version->stages()->with('customFields')->first();
        $application = $this->application();
        $run = $this->recommend($application, $version);
        $remarks = \Mockery::mock(RemarkService::class);
        $remarks->shouldReceive('store')->once()->andThrow(new \RuntimeException('Simulated remark failure'));
        $this->app->instance(RemarkService::class, $remarks);
        try {
            app(WorkflowHistoryService::class)->store([
                'recommendation_uuid' => $run->uuid, 'expected_stage_uuid' => null,
                'to_stage_uuid' => $stage->uuid, 'date_in' => now(), 'date_out' => now(),
                'custom_values' => $this->values($stage), 'remark' => 'Side note',
            ]);
            $this->fail('A failed remark save was accepted.');
        } catch (\RuntimeException $exception) {
            $this->assertSame('Simulated remark failure', $exception->getMessage());
        }
        $this->assertSame(0, WorkflowHistory::count());
        $this->assertSame(0, DB::table('custom_field_values')->count());
        $this->assertNull($application->fresh()->current_workflow_stage_uuid);
    }

    public function test_fields_from_new_versions_cannot_be_submitted_to_an_older_run(): void
    {
        $data = $this->customDefinition();
        $version = app(WorkflowVersionService::class)->publish($data);
        $stage = $version->stages()->with('customFields')->first();
        $run = $this->recommend($this->application(), $version);
        $data['stages'][0]['custom_fields'][0]['label'] = 'Revised label';
        $next = app(WorkflowVersionService::class)->publish($data, $version);
        $newStage = $next->stages()->with('customFields')->first();
        $this->expectException(ValidationException::class);
        $this->record($run, $stage, $this->values($newStage));
    }

    public function test_non_superadmin_cannot_invoke_field_editor_actions(): void
    {
        $version = $this->publish();
        $editor = Livewire::test(Edit::class, ['workflow' => $version]);
        $key = $editor->get('stages')[0]['key'];
        $user = User::create(['email' => 'staff-field-editor@example.test']);
        $user->assignRole(Role::findOrCreate('staff', 'web'));
        $this->actingAs($user);
        $editor->call('addField', $key)->assertForbidden();
        $this->assertSame(0, CustomField::count());
    }

    public function test_successful_processing_clears_custom_field_form(): void
    {
        $version = app(WorkflowVersionService::class)->publish($this->customDefinition());
        $stage = $version->stages()->with('customFields')->first();
        $application = $this->application();
        $this->recommend($application, $version);
        Livewire::test(Create::class, ['application' => $application])
            ->set('dateIn', '2026-10-25T09:00:00')->set('toStageUuid', $stage->uuid)
            ->set('customValues', $this->values($stage))->set('dateOut', '2026-10-25T09:05:00')
            ->call('submit')->assertHasNoErrors()->assertSet('customValues', [])->assertSet('customFields', []);
        $this->assertSame($stage->uuid, $application->fresh()->current_workflow_stage_uuid);
    }

    private function fields(): array
    {
        return [
            ['name' => 'check_number', 'label' => 'Check Number', 'input_type' => 'text', 'required' => true],
            ['name' => 'amount', 'label' => 'Amount', 'input_type' => 'number', 'required' => true],
            ['name' => 'available', 'label' => 'Availability', 'input_type' => 'date', 'required' => true],
            ['name' => 'secret', 'label' => 'Secret', 'input_type' => 'password', 'required' => false],
        ];
    }

    private function customDefinition(): array
    {
        $data = $this->definition();
        $data['stages'][0]['custom_fields'] = $this->fields();

        return $data;
    }

    private function record($run, $stage, array $values, ?string $expected = null): WorkflowHistory
    {
        return app(WorkflowHistoryService::class)->store([
            'recommendation_uuid' => $run->uuid, 'expected_stage_uuid' => $expected, 'to_stage_uuid' => $stage->uuid,
            'date_in' => '2026-10-25 09:00:00', 'date_out' => '2026-10-25 09:05:00',
            'custom_values' => $values, 'reason' => $expected ? 'Return for review' : null,
        ]);
    }

    private function values($stage, array $overrides = []): array
    {
        return $stage->customFields->mapWithKeys(fn ($field) => [$field->uuid => array_merge([
            'check_number' => '00001234', 'amount' => '-123456789012345678.12345',
            'available' => '2026-10-25', 'secret' => 'confidential-check-detail',
        ], $overrides)[$field->name]])->all();
    }

    public function test_field_only_publication_clones_definitions_and_unchanged_saves_do_nothing(): void
    {
        $data = $this->customDefinition();
        $version = app(WorkflowVersionService::class)->publish($data);
        $original = $version->stages()->first()->customFields;
        $this->assertSame($version->uuid, app(WorkflowVersionService::class)->publish($data, $version)->uuid);
        $data['stages'][0]['custom_fields'][0]['label'] = 'Updated check label';
        $next = app(WorkflowVersionService::class)->publish($data, $version);
        $clones = $next->stages()->first()->customFields;
        $this->assertSame(2, $next->version_number);
        $this->assertSame([], array_intersect($original->modelKeys(), $clones->modelKeys()));
        $this->assertSame('Check Number', $original->first()->fresh()->label);
        $this->assertSame('Updated check label', $clones->first()->label);
        $this->assertSame([1, 2, 3, 4], $clones->pluck('order_no')->all());
    }

    public function test_invalid_definition_rolls_back_permission_tags_and_workflow(): void
    {
        foreach (['duplicate', 'type', 'required', 'name'] as $case) {
            $data = $this->customDefinition();
            $data['stages'][0]['permission_id'] = 'New Field Permission';
            if ($case === 'duplicate') {
                $data['stages'][0]['custom_fields'][1]['name'] = 'check_number';
            }
            if ($case === 'type') {
                $data['stages'][0]['custom_fields'][0]['input_type'] = 'file';
            }
            if ($case === 'required') {
                $data['stages'][0]['custom_fields'][0]['required'] = 'maybe';
            }
            if ($case === 'name') {
                $data['stages'][0]['custom_fields'][0]['name'] = 'Bad Name';
            }
            try {
                app(WorkflowVersionService::class)->publish($data);
                $this->fail('Invalid definition was accepted: '.$case);
            } catch (ValidationException $exception) {
                $this->assertNotEmpty($exception->errors());
            }
            $this->assertSame(0, Workflow::count());
            $this->assertSame(0, DB::table('permissions')->where('name', 'Workflow New Field Permission')->count());
        }
    }

    public function test_values_preserve_identifiers_precision_and_encrypted_passwords(): void
    {
        $version = app(WorkflowVersionService::class)->publish($this->customDefinition());
        $stage = $version->stages()->with('customFields')->first();
        $application = $this->application();
        $run = $this->recommend($application, $version);
        $history = $this->record($run, $stage, $this->values($stage));
        $saved = $history->customValues()->with('customField')->get()->keyBy(fn ($value) => $value->customField->name);
        $this->assertSame('00001234', $saved['check_number']->value);
        $this->assertSame('-123456789012345678.12345', $saved['amount']->value);
        $this->assertSame('confidential-check-detail', Crypt::decryptString($saved['secret']->value));
        $this->assertStringNotContainsString('confidential-check-detail', DB::table('activity_log')->pluck('properties')->implode(''));
        $this->assertArrayNotHasKey('value', $saved['secret']->toArray());
        Livewire::test(Show::class, ['application' => $application])->call('loadDetails', $history->uuid)
            ->assertSee('00001234')->assertSee('confidential-check-detail')->assertSee('Availability');
    }

    public function test_required_types_unknown_fields_and_atomic_rollback(): void
    {
        $version = app(WorkflowVersionService::class)->publish($this->customDefinition());
        $stage = $version->stages()->with('customFields')->first();
        $application = $this->application();
        $run = $this->recommend($application, $version);
        $invalid = [[], $this->values($stage, ['amount' => '1e5']), $this->values($stage, ['available' => '2026-02-30']),
            $this->values($stage, ['check_number' => str_repeat('a', 256)]), $this->values($stage, ['secret' => ['bad']]),
            array_merge($this->values($stage), [(string) Str::uuid() => 'foreign'])];
        foreach ($invalid as $values) {
            try {
                $this->record($run, $stage, $values);
                $this->fail('Invalid values were accepted.');
            } catch (ValidationException $exception) {
                $this->assertNotEmpty($exception->errors());
                $this->assertStringNotContainsString('confidential-check-detail', $exception->getMessage());
            }
            $this->assertSame(0, WorkflowHistory::count());
            $this->assertSame(0, DB::table('custom_field_values')->count());
            $this->assertNull($application->fresh()->current_workflow_stage_uuid);
        }
        $history = $this->record($run, $stage, $this->values($stage, ['amount' => '0', 'secret' => '']));
        $this->assertSame(3, $history->customValues()->count());
    }

    public function test_repeat_visits_and_old_runs_keep_their_own_values(): void
    {
        $data = $this->customDefinition();
        $version = app(WorkflowVersionService::class)->publish($data);
        $stages = $version->stages()->with('customFields')->get();
        $application = $this->application();
        $run = $this->recommend($application, $version);
        $first = $this->record($run, $stages[0], $this->values($stages[0]));
        $this->record($run, $stages[1], [], $stages[0]->uuid);
        $second = $this->record($run, $stages[0], $this->values($stages[0], ['check_number' => '00009999']), $stages[1]->uuid);
        $data['stages'][0]['custom_fields'] = [];
        $next = app(WorkflowVersionService::class)->publish($data, $version);
        $fieldUuid = $stages[0]->customFields->firstWhere('name', 'check_number')->uuid;
        $this->assertSame('00001234', $first->customValues()->where('custom_field_uuid', $fieldUuid)->first()->value);
        $this->assertSame('00009999', $second->customValues()->where('custom_field_uuid', $fieldUuid)->first()->value);
        $this->assertSame($version->uuid, $run->fresh()->workflow_uuid);
        $this->assertSame(0, $next->stages()->first()->customFields()->count());
    }

    public function test_published_fields_and_recorded_values_are_immutable(): void
    {
        $version = app(WorkflowVersionService::class)->publish($this->customDefinition());
        $stage = $version->stages()->with('customFields')->first();
        $run = $this->recommend($this->application(), $version);
        $history = $this->record($run, $stage, $this->values($stage));
        foreach ([fn () => $stage->customFields->first()->update(['label' => 'Changed']),
            fn () => $stage->customFields->first()->delete(),
            fn () => $stage->customFields()->create($this->fields()[0] + ['order_no' => 5]),
            fn () => $history->customValues()->first()->update(['value' => 'changed']),
            fn () => $history->customValues()->first()->delete()] as $operation) {
            try {
                $operation();
                $this->fail('Immutable record was changed.');
            } catch (\LogicException $exception) {
                $this->assertNotEmpty($exception->getMessage());
            }
        }
    }

    public function test_editor_field_actions_restore_order_and_preserve_generated_names(): void
    {
        $version = $this->publish();
        $editor = Livewire::test(Edit::class, ['workflow' => $version]);
        $key = $editor->get('stages')[0]['key'];
        $editor->call('addField', $key)->set('stages.0.custom_fields.0.label', 'DV Number')
            ->assertSet('stages.0.custom_fields.0.name', 'dv_number')
            ->set('stages.0.custom_fields.0.label', 'Voucher Number')
            ->assertSet('stages.0.custom_fields.0.name', 'dv_number')
            ->call('addField', $key)->set('stages.0.custom_fields.1.label', 'Amount');
        $fieldKey = $editor->get('stages')[0]['custom_fields'][0]['key'];
        $editor->call('moveField', $key, $fieldKey, 1)->assertSet('stages.0.custom_fields.1.name', 'dv_number')
            ->call('removeField', $key, $fieldKey)->call('restoreField', $key, $fieldKey)
            ->call('removeStage', $key)->call('restoreStage', $key);
        $this->assertCount(2, $editor->get('stages')[1]['custom_fields']);
        $this->assertSame(0, CustomField::count());
        $editor->call('save')->assertHasNoErrors();
        $this->assertSame(2, CustomField::count());
        Livewire::test(Edit::class, ['workflow' => $version])->call('addField', $key)->assertForbidden();
    }

    public function test_switching_target_clears_values_and_refresh_discards_them(): void
    {
        $version = app(WorkflowVersionService::class)->publish($this->customDefinition());
        $stage = $version->stages()->with('customFields')->first();
        $application = $this->application();
        $this->recommend($application, $version);
        Livewire::test(Create::class, ['application' => $application])
            ->set('toStageUuid', $stage->uuid)->assertSet('customFields.0.label', 'Check Number')
            ->set('customValues', $this->values($stage))->set('toStageUuid', null)
            ->assertSet('customValues', [])->assertSet('customFields', [])
            ->set('toStageUuid', $stage->uuid)->set('customValues', $this->values($stage))
            ->call('refresh')->assertSet('customValues', [])->assertSet('customFields', []);
    }

    public function test_history_details_reject_another_application_and_unauthorized_viewers(): void
    {
        $version = $this->publish();
        $application = $this->application();
        $other = $this->application();
        $run = $this->recommend($other, $version);
        $history = $this->log($run, $version->stages()->first()->uuid);
        Livewire::test(Show::class, ['application' => $application])->call('loadDetails', $history->uuid)->assertNotFound();
        $outsider = User::create(['email' => 'outsider@example.test']);
        $this->actingAs($outsider);
        Livewire::withHeaders(['Accept' => 'application/json'])->test(Show::class, ['application' => $other])->assertForbidden();
    }

    public function test_default_seed_fields_are_required_and_reseeding_preserves_cms_changes(): void
    {
        (new WorkflowSeeder)->run();
        $version = Workflow::latestVersion()->first();
        $stages = $version->stages()->with('customFields')->get()->keyBy('name');
        $this->assertSame(['obr_number'], $stages['Budget']->customFields->pluck('name')->all());
        $this->assertSame(['check_number', 'check_available_date'], $stages['Releasing']->customFields->pluck('name')->all());
        $this->assertSame(3, CustomField::where('required', true)->count());
        $data = ['name' => $version->name, 'description' => $version->description, 'stages' => $version->stages()->with('customFields')->get()
            ->map(fn ($stage) => ['name' => $stage->name, 'description' => $stage->description, 'permission_id' => (string) $stage->permission_id,
                'custom_fields' => []])->all()];
        $next = app(WorkflowVersionService::class)->publish($data, $version);
        (new WorkflowSeeder)->run();
        $this->assertSame(2, Workflow::count());
        $this->assertSame(0, $next->stages()->withCount('customFields')->get()->sum('custom_fields_count'));
    }

    public function test_factory_supplies_required_values_and_details_do_not_lazy_load(): void
    {
        $version = app(WorkflowVersionService::class)->publish($this->customDefinition());
        $application = $this->application();
        $run = $this->recommend($application, $version);
        $history = WorkflowHistory::factory()->create(['recommendation_uuid' => $run->uuid]);
        $this->assertSame(3, $history->customValues()->count());
        $violations = [];
        Model::preventLazyLoading();
        Model::handleLazyLoadingViolationUsing(function ($model, $relation) use (&$violations) {
            $violations[] = $relation;
        });
        try {
            Livewire::test(Show::class, ['application' => $application])->call('loadDetails', $history->uuid)->assertSee('Check Number');
            $this->assertSame([], $violations);
        } finally {
            Model::handleLazyLoadingViolationUsing(null);
            Model::preventLazyLoading(false);
        }
    }
}
