<?php

namespace Tests\Feature;

use App\Livewire\Recommendation\Show;
use App\Livewire\Workflow\Create;
use App\Livewire\Workflow\Edit;
use App\Livewire\Workflow\Index;
use App\Models\Application;
use App\Models\CustomField;
use App\Models\FuneralAssistanceType;
use App\Models\Recommendation;
use App\Models\User;
use App\Models\Workflow;
use App\Models\WorkflowHistory;
use App\Services\RecommendationService;
use App\Services\WorkflowHistoryService;
use App\Services\WorkflowVersionAudit;
use App\Services\WorkflowVersionService;
use Database\Seeders\WorkflowHistorySeeder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\QueryException;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Livewire\Livewire;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\Process\Process;
use Tests\Support\WorkflowCustomFieldsCases;
use Tests\TestCase;

class WorkflowEditingTest extends TestCase
{
    use WorkflowCustomFieldsCases;

    protected function setUp(): void
    {
        parent::setUp();
        config([
            'database.default' => 'workflow_editor_testing',
            'database.connections.workflow_editor_testing' => [
                'driver' => 'sqlite', 'database' => ':memory:', 'prefix' => '', 'foreign_key_constraints' => true,
            ],
            'cache.default' => 'array', 'session.driver' => 'array',
            'app.key' => 'base64:'.base64_encode(str_repeat('w', 32)),
        ]);
        foreach (['genders', 'relationships', 'civilStatus', 'religions', 'nationalities', 'educations', 'districts', 'assistances', 'modes'] as $lookup) {
            Cache::put($lookup, collect(), 3600);
        }
        app(PermissionRegistrar::class)->forgetCachedPermissions();
        $this->createLegacySchema();
        $this->migration()->up();
        foreach (glob(database_path('migrations/custom_fields/*.php')) as $path) {
            (require $path)->up();
        }
        $user = User::create(['email' => 'editor@example.test']);
        $user->assignRole(Role::findOrCreate('superadmin', 'web'));
        $this->actingAs($user);
    }

    protected function tearDown(): void
    {
        DB::purge('workflow_editor_testing');
        parent::tearDown();
    }

    private function createLegacySchema(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('email')->nullable();
            $table->timestamps();
        });
        foreach (['roles', 'permissions'] as $name) {
            Schema::create($name, function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('guard_name');
                $table->timestamps();
                $table->unique(['name', 'guard_name']);
            });
        }
        foreach (['role', 'permission'] as $name) {
            Schema::create('model_has_'.$name.'s', function (Blueprint $table) use ($name) {
                $table->unsignedBigInteger($name.'_id');
                $table->string('model_type');
                $table->unsignedBigInteger('model_id');
            });
        }
        Schema::create('role_has_permissions', function (Blueprint $table) {
            $table->unsignedBigInteger('role_id');
            $table->unsignedBigInteger('permission_id');
        });
        Schema::create('workflows', function (Blueprint $table) {
            $table->uuid('uuid')->primary();
            $table->string('name');
            $table->text('description');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();
        });
        Schema::create('workflow_stages', function (Blueprint $table) {
            $table->uuid('uuid')->primary();
            $table->foreignUuid('workflow_uuid')->constrained('workflows', 'uuid')->cascadeOnDelete();
            $table->string('name');
            $table->text('description');
            $table->integer('position')->nullable();
            $table->foreignId('permission_id')->nullable()->constrained('permissions')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();
            $table->unique(['workflow_uuid', 'position']);
        });
        Schema::create('funeral_assistance_types', function (Blueprint $table) {
            $table->uuid('uuid')->primary();
            $table->string('name');
            $table->text('description');
            $table->foreignUuid('workflow_uuid')->nullable()->constrained('workflows', 'uuid');
            $table->timestamps();
            $table->softDeletes();
        });
        Schema::create('clients', function (Blueprint $table) {
            $table->uuid('uuid')->primary();
            $table->unsignedBigInteger('user_id');
            $table->timestamps();
        });
        Schema::create('beneficiaries', function (Blueprint $table) {
            $table->uuid('uuid')->primary();
            $table->timestamps();
        });
        Schema::create('applications', function (Blueprint $table) {
            $table->uuid('uuid')->primary();
            $table->string('tracking_no');
            $table->string('qr_code');
            $table->uuid('client_uuid');
            $table->uuid('beneficiary_uuid')->nullable();
            $table->foreignUuid('current_workflow_stage_uuid')->nullable()->constrained('workflow_stages', 'uuid')->nullOnDelete();
            $table->timestamps();
        });
        foreach (['assessments', 'closures', 'rejections', 'cancellations', 'referrals'] as $name) {
            Schema::create($name, function (Blueprint $table) {
                $table->uuid('uuid')->primary();
                $table->uuid('application_uuid');
                $table->timestamps();
                $table->softDeletes();
            });
        }
        Schema::create('interviews', function (Blueprint $table) {
            $table->uuid('uuid')->primary();
            $table->uuid('client_uuid');
            $table->string('status')->default('done');
            $table->timestamps();
        });
        Schema::create('mode_of_assistances', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->timestamps();
            $table->softDeletes();
        });
        DB::table('mode_of_assistances')->insert(['id' => 1, 'name' => 'Cash']);
        Schema::create('recommendations', function (Blueprint $table) {
            $table->uuid('uuid')->primary();
            $table->foreignUuid('application_uuid')->constrained('applications', 'uuid')->cascadeOnDelete();
            $table->uuid('funeral_assistance_type_uuid')->nullable();
            $table->decimal('amount_extended', 10, 2);
            $table->unsignedBigInteger('mode_of_assistance_id');
            $table->unsignedBigInteger('recommended_by');
            $table->string('status')->default('pending');
            $table->timestamp('approved_at')->nullable();
            $table->timestamps();
        });
        Schema::create('workflow_histories', function (Blueprint $table) {
            $table->uuid('uuid')->primary();
            $table->foreignUuid('recommendation_uuid')->constrained('recommendations', 'uuid')->cascadeOnDelete();
            $table->foreignUuid('from_stage_uuid')->nullable()->constrained('workflow_stages', 'uuid')->cascadeOnDelete();
            $table->foreignUuid('to_stage_uuid')->nullable()->constrained('workflow_stages', 'uuid')->cascadeOnDelete();
            $table->dateTime('date_in');
            $table->dateTime('date_out');
            $table->text('reason')->nullable();
            $table->unsignedBigInteger('processed_by')->nullable();
            $table->timestamps();
        });
        Schema::create('activity_log', function (Blueprint $table) {
            $table->id();
            $table->string('log_name')->nullable();
            $table->text('description');
            $table->string('event')->nullable();
            $table->nullableMorphs('subject');
            $table->nullableMorphs('causer');
            $table->json('properties')->nullable();
            $table->json('attribute_changes')->nullable();
            $table->uuid('batch_uuid')->nullable();
            $table->timestamps();
        });
    }

    private function migration()
    {
        return require database_path('migrations/workflow/2026_10_23_110000_version_workflows_and_pin_recommendations.php');
    }

    private function definition(string $name = 'Funeral Assistance'): array
    {
        $permission = Permission::findOrCreate('Workflow Review', 'web');

        return ['name' => $name, 'description' => 'Workflow description', 'stages' => [
            ['name' => 'Review', 'description' => 'Review records', 'permission_id' => (string) $permission->id],
            ['name' => 'Release', 'description' => 'Release assistance', 'permission_id' => (string) $permission->id],
        ]];
    }

    private function publish(string $name = 'Funeral Assistance'): Workflow
    {
        return app(WorkflowVersionService::class)->publish($this->definition($name));
    }

    private function application(): Application
    {
        $clientUuid = (string) Str::uuid();
        DB::table('clients')->insert(['uuid' => $clientUuid, 'user_id' => 999]);
        $beneficiaryUuid = (string) Str::uuid();
        DB::table('beneficiaries')->insert(['uuid' => $beneficiaryUuid]);
        $application = Application::create(['client_uuid' => $clientUuid, 'beneficiary_uuid' => $beneficiaryUuid]);
        DB::table('assessments')->insert(['uuid' => (string) Str::uuid(), 'application_uuid' => $application->uuid]);

        return $application;
    }

    private function type(Workflow $version): FuneralAssistanceType
    {
        return FuneralAssistanceType::create(['name' => 'Burial', 'description' => 'Assistance type', 'workflow_uuid' => $version->uuid]);
    }

    private function recommend(Application $application, Workflow $version, ?FuneralAssistanceType $type = null): Recommendation
    {
        $type ??= $this->type($version);

        return app(RecommendationService::class)->store([
            'workflow_uuid' => $version->uuid, 'funeral_assistance_type_uuid' => $type->uuid,
            'amount_extended' => 1000, 'mode_of_assistance_id' => 1,
            'expected_recommendation_uuid' => $application->fresh()->current_recommendation_uuid,
        ], $application->uuid);
    }

    private function log(Recommendation $run, string $target, ?string $expected = null, ?string $reason = null): WorkflowHistory
    {
        return app(WorkflowHistoryService::class)->store([
            'recommendation_uuid' => $run->uuid, 'expected_stage_uuid' => $expected, 'to_stage_uuid' => $target,
            'date_in' => '2026-10-23 09:00:00', 'date_out' => '2026-10-23 09:05:00', 'reason' => $reason,
        ]);
    }

    public function test_publication_clones_stages_and_advances_suggestions_without_moving_runs(): void
    {
        $v1 = $this->publish();
        $type = $this->type($v1);
        $application = $this->application();
        $run = $this->recommend($application, $v1, $type);
        $stage = $v1->stages()->orderBy('position')->first();
        $history = $this->log($run, $stage->uuid);
        $snapshot = $v1->fresh()->getAttributes();
        $stages = $v1->stages()->get()->map->getAttributes()->all();
        $data = $this->definition();
        $data['stages'][0]['name'] = 'New review';
        $v2 = app(WorkflowVersionService::class)->publish($data, $v1);
        $this->assertSame(2, $v2->version_number);
        $this->assertSame($v1->workflow_group_uuid, $v2->workflow_group_uuid);
        $this->assertSame($snapshot, $v1->fresh()->getAttributes());
        $this->assertSame($stages, $v1->stages()->get()->map->getAttributes()->all());
        $this->assertEmpty(array_intersect($v1->stages()->pluck('uuid')->all(), $v2->stages()->pluck('uuid')->all()));
        $this->assertSame($v2->uuid, $type->fresh()->workflow_uuid);
        $this->assertSame($v1->uuid, $run->fresh()->workflow_uuid);
        $this->assertSame($stage->uuid, $application->fresh()->current_workflow_stage_uuid);
        $this->assertSame($v1->uuid, $application->fresh()->currentWorkflow()->uuid);
        $this->assertSame($history->uuid, WorkflowHistory::first()->uuid);
        $this->assertTrue($v1->fresh()->is_active);
    }

    public function test_unchanged_definitions_do_not_create_versions(): void
    {
        $v1 = $this->publish();
        $same = app(WorkflowVersionService::class)->publish($this->definition(), $v1);
        $this->assertSame($v1->uuid, $same->uuid);
        $this->assertSame(1, Workflow::count());
    }

    public function test_stale_publish_cannot_fork_the_version_sequence(): void
    {
        $v1 = $this->publish();
        $data = $this->definition();
        $data['description'] = 'Changed';
        app(WorkflowVersionService::class)->publish($data, $v1);
        try {
            app(WorkflowVersionService::class)->publish($data, $v1);
            $this->fail('Stale publication accepted.');
        } catch (ValidationException $exception) {
            $this->assertArrayHasKey('name', $exception->errors());
        }
        $this->assertSame([1, 2], Workflow::orderBy('version_number')->pluck('version_number')->all());
    }

    public function test_invalid_publication_rolls_back_versions_stages_and_permission_tags(): void
    {
        $data = $this->definition();
        $data['stages'][0]['permission_id'] = 'Must rollback';
        $data['stages'][1]['permission_id'] = '999999';
        try {
            app(WorkflowVersionService::class)->publish($data);
            $this->fail('Invalid permission accepted.');
        } catch (ValidationException $exception) {
            $this->assertArrayHasKey('stages.1.permission_id', $exception->errors());
        }
        $this->assertSame(0, Workflow::count());
        $this->assertFalse(Permission::where('name', 'Workflow Must rollback')->exists());
        $this->assertSame(0, DB::table('workflow_name_reservations')->count());
    }

    public function test_name_conflicts_include_other_groups_historical_names(): void
    {
        $first = $this->publish('Reserved name');
        $data = $this->definition('Renamed');
        app(WorkflowVersionService::class)->publish($data, $first);
        $this->expectException(ValidationException::class);
        $this->publish('Reserved name');
    }

    public function test_saved_workflows_and_stages_are_immutable(): void
    {
        $version = $this->publish();
        foreach ([
            fn () => $version->update(['name' => 'Changed']),
            fn () => $version->delete(),
            fn () => $version->forceDelete(),
            fn () => $version->stages()->first()->update(['name' => 'Changed']),
            fn () => $version->stages()->first()->delete(),
            fn () => $version->stages()->create(['name' => 'Extra', 'description' => 'Extra', 'position' => 3]),
        ] as $mutation) {
            try {
                $mutation();
                $this->fail('Published definition changed.');
            } catch (\LogicException $exception) {
                $this->assertMatchesRegularExpression('/immutable|cannot be deleted/', $exception->getMessage());
            }
        }
        $this->assertSame('Funeral Assistance', $version->fresh()->name);
        $this->assertSame(2, $version->stages()->count());
    }

    public function test_restrictive_foreign_keys_preserve_referenced_definitions(): void
    {
        $version = $this->publish();
        $this->recommend($this->application(), $version);
        $this->expectException(QueryException::class);
        DB::table('workflows')->where('uuid', $version->uuid)->delete();
    }

    public function test_new_recommendation_restarts_on_chosen_version_and_preserves_previous_history(): void
    {
        $v1 = $this->publish();
        $application = $this->application();
        $type = $this->type($v1);
        $old = $this->recommend($application, $v1, $type);
        $history = $this->log($old, $v1->stages()->orderBy('position')->first()->uuid);
        $data = $this->definition();
        $data['description'] = 'Second version';
        $v2 = app(WorkflowVersionService::class)->publish($data, $v1);
        $new = $this->recommend($application, $v2, $type);
        $this->assertSame($new->uuid, $application->fresh()->current_recommendation_uuid);
        $this->assertNull($application->fresh()->current_workflow_stage_uuid);
        $this->assertSame(1, $application->fresh()->toStage()->position);
        $this->assertSame($v1->uuid, $old->fresh()->workflow_uuid);
        $this->assertSame('pending', $old->fresh()->status);
        $this->assertSame($history->uuid, $old->workflowHistory()->first()->uuid);
        $this->assertSame($v2->uuid, $new->workflow_uuid);
    }

    public function test_staff_can_override_classification_and_reassignment_never_changes_the_run(): void
    {
        $suggested = $this->publish();
        $override = $this->publish('Other flow');
        $type = $this->type($suggested);
        $application = $this->application();
        $run = $this->recommend($application, $override, $type);
        $type->update(['workflow_uuid' => null]);
        $this->assertSame($override->uuid, $application->fresh()->currentWorkflow()->uuid);
        $this->assertSame($override->uuid, $run->fresh()->workflow_uuid);
    }

    public function test_inactive_latest_version_never_falls_back_to_older_active_version(): void
    {
        $v1 = $this->publish();
        $data = $this->definition();
        $data['description'] = 'v2';
        $v2 = app(WorkflowVersionService::class)->publish($data, $v1);
        app(WorkflowVersionService::class)->setAvailability($v2, false);
        $this->assertTrue($v1->fresh()->is_active);
        $this->assertSame(0, Workflow::selectable()->count());
        $this->assertSame(2, Workflow::count());
        $this->expectException(ValidationException::class);
        $this->recommend($this->application(), $v1);
    }

    public function test_pause_blocks_a_stale_stage_form_and_resuming_preserves_its_position(): void
    {
        $version = $this->publish();
        $application = $this->application();
        $run = $this->recommend($application, $version);
        $stages = $version->stages()->orderBy('position')->get();
        $this->log($run, $stages[0]->uuid);
        app(WorkflowVersionService::class)->setAvailability($version, false);
        try {
            $this->log($run, $stages[1]->uuid, $stages[0]->uuid);
            $this->fail('Paused run advanced.');
        } catch (ValidationException $exception) {
            $this->assertArrayHasKey('toStageUuid', $exception->errors());
        }
        $this->assertSame(1, WorkflowHistory::count());
        $this->assertSame($stages[0]->uuid, $application->fresh()->current_workflow_stage_uuid);
        app(WorkflowVersionService::class)->setAvailability($version, true);
        $this->log($run, $stages[1]->uuid, $stages[0]->uuid);
        $this->assertSame($stages[1]->uuid, $application->fresh()->current_workflow_stage_uuid);
    }

    public function test_only_the_deactivated_version_pauses_and_a_new_recommendation_can_escape_it(): void
    {
        $v1 = $this->publish();
        $application = $this->application();
        $type = $this->type($v1);
        $old = $this->recommend($application, $v1, $type);
        $data = $this->definition();
        $data['description'] = 'v2';
        $v2 = app(WorkflowVersionService::class)->publish($data, $v1);
        app(WorkflowVersionService::class)->setAvailability($v1, false);
        $this->assertTrue($v2->fresh()->is_active);
        $new = $this->recommend($application, $v2, $type);
        app(WorkflowVersionService::class)->setAvailability($v1, true);
        $this->assertSame($new->uuid, $application->fresh()->current_recommendation_uuid);
        $this->expectException(ValidationException::class);
        $this->log($old, $v1->stages()->first()->uuid);
    }

    public function test_cross_version_targets_and_stale_progress_are_rejected(): void
    {
        $v1 = $this->publish();
        $application = $this->application();
        $run = $this->recommend($application, $v1);
        $other = $this->publish('Other');
        try {
            $this->log($run, $other->stages()->first()->uuid);
            $this->fail('Cross-version stage accepted.');
        } catch (ValidationException $exception) {
            $this->assertArrayHasKey('toStageUuid', $exception->errors());
        }
        $stages = $v1->stages()->orderBy('position')->get();
        $this->log($run, $stages[0]->uuid);
        $this->expectException(ValidationException::class);
        $this->log($run, $stages[1]->uuid);
    }

    public function test_target_permission_authorization_and_backward_reason_are_enforced(): void
    {
        $version = $this->publish();
        $run = $this->recommend($this->application(), $version);
        $stages = $version->stages()->orderBy('position')->get();
        $staff = User::create(['email' => 'staff@example.test']);
        $staff->givePermissionTo('Workflow Review');
        $this->actingAs($staff);
        $this->log($run, $stages[0]->uuid);
        $this->log($run, $stages[1]->uuid, $stages[0]->uuid);
        try {
            $this->log($run, $stages[0]->uuid, $stages[1]->uuid);
            $this->fail('Backward reason omitted.');
        } catch (ValidationException $exception) {
            $this->assertArrayHasKey('reason', $exception->errors());
        }
        $this->log($run, $stages[0]->uuid, $stages[1]->uuid, 'Missing document');
        $staff->revokePermissionTo('Workflow Review');
        $this->expectException(HttpException::class);
        $this->log($run, $stages[1]->uuid, $stages[0]->uuid);
    }

    public function test_editor_draft_changes_are_local_and_one_save_publishes_all_changes(): void
    {
        $version = $this->publish();
        $editor = Livewire::test(Edit::class, ['workflow' => $version])->assertSee('Save New Version');
        $editor->set('name', 'Edited flow')->set('stages.0.name', 'Changed review')->call('addStage');
        $this->assertSame(1, Workflow::count());
        $this->assertSame(2, $version->stages()->count());
        $key = $editor->get('stages')[2]['key'];
        $editor->set('stages.2.name', 'Extra')->set('stages.2.description', 'Extra stage')
            ->set('stages.2.permission_id', 'New tag')->call('moveStage', $key, -1)
            ->call('removeStage', $key)->call('restoreStage', $key)->call('save')
            ->assertHasNoErrors()->assertNoRedirect();
        $latest = Workflow::latestVersion()->first();
        $this->assertSame(2, $latest->version_number);
        $this->assertSame(3, $latest->stages()->count());
        $this->assertSame('Funeral Assistance', $version->fresh()->name);
        $this->assertTrue(Permission::where('name', 'Workflow New tag')->exists());
    }

    public function test_new_workflow_requires_a_complete_draft(): void
    {
        Livewire::test(Create::class)->set('name', 'New flow')->set('description', 'Description')
            ->call('save')->assertHasErrors(['stages.0.name', 'stages.0.permission_id']);
        $this->assertSame(0, Workflow::count());
    }

    public function test_historical_editor_cannot_save_but_can_toggle_its_version_availability(): void
    {
        $v1 = $this->publish();
        $data = $this->definition();
        $data['description'] = 'v2';
        app(WorkflowVersionService::class)->publish($data, $v1);
        Livewire::test(Edit::class, ['workflow' => $v1])->assertSet('historical', true)->assertSee('read-only')
            ->call('setAvailability', $v1->uuid, false)->assertHasNoErrors();
        $this->assertFalse($v1->fresh()->is_active);
        Livewire::test(Edit::class, ['workflow' => $v1])->call('save')->assertStatus(403);
    }

    public function test_workflow_index_groups_versions_and_includes_status_and_version_columns(): void
    {
        $v1 = $this->publish();
        $data = $this->definition();
        $data['description'] = 'v2';
        $v2 = app(WorkflowVersionService::class)->publish($data, $v1);
        app(WorkflowVersionService::class)->setAvailability($v2, false);
        $index = Livewire::test(Index::class);
        $rows = $index->get('mappedWorkflows');
        $this->assertCount(1, $rows);
        $this->assertSame(2, $rows->first()['version']);
        $this->assertSame('Inactive', $rows->first()['status']['label']);
        $this->assertContains('version', $index->get('columns')->pluck('data')->all());
    }

    public function test_assistance_type_edit_route_binds_the_requested_record_and_uses_an_existing_view(): void
    {
        $type = $this->type($this->publish());
        $request = Request::create(route('funeral_assistance_types.edit', $type), 'GET');
        $router = app('router');
        $route = $router->getRoutes()->match($request);
        $this->assertSame('funeral_assistance_types.edit', $route->getName());
        $router->substituteBindings($route);
        $router->substituteImplicitBindings($route);
        $this->assertTrue($type->is($route->parameter('funeralAssistanceType')));
        $view = $route->run();
        $this->assertSame('funeral-assistance-type.edit', $view->name());
        $this->assertTrue(view()->exists($view->name()));
        $this->assertTrue($type->is($view->getData()['funeralAssistanceType']));
    }

    public function test_assistance_suggestions_exclude_old_versions_and_backend_rejects_tampering(): void
    {
        $v1 = $this->publish();
        $type = $this->type($v1);
        $data = $this->definition();
        $data['description'] = 'v2';
        $v2 = app(WorkflowVersionService::class)->publish($data, $v1);
        $editor = Livewire::test(\App\Livewire\FuneralAssistanceType\Edit::class, ['funeralAssistanceType' => $type->fresh()]);
        $this->assertSame([$v2->uuid => $v2->label()], $editor->get('workflows'));
        $editor->set('form.workflowUuid', $v1->uuid)->call('save')->assertHasErrors('form.workflowUuid');
        $this->assertSame($v2->uuid, $type->fresh()->workflow_uuid);
    }

    public function test_historical_recommendation_display_does_not_switch_to_a_new_run(): void
    {
        $version = $this->publish();
        $application = $this->application();
        $type = $this->type($version);
        $old = $this->recommend($application, $version, $type);
        $display = Livewire::test(Show::class, ['recommendation' => $old]);
        $this->recommend($application, $version, $type);
        $display->dispatch('refreshRecommendation')->assertSet('recommendation.uuid', $old->uuid)->assertSee($version->label());
    }

    public function test_audit_is_read_only_and_detects_conflicting_stage_evidence(): void
    {
        $version = $this->publish();
        $application = $this->application();
        $run = $this->recommend($application, $version);
        $this->log($run, $version->stages()->first()->uuid);
        $this->assertSame([], app(WorkflowVersionAudit::class)->inspect()['errors']);
        $other = $this->publish('Other');
        DB::table('applications')->where('uuid', $application->uuid)->update(['current_workflow_stage_uuid' => $other->stages()->first()->uuid]);
        $before = DB::table('applications')->get()->toJson();
        $this->assertNotEmpty(app(WorkflowVersionAudit::class)->inspect()['errors']);
        $this->assertSame($before, DB::table('applications')->get()->toJson());
    }

    public function test_recommendation_pin_cannot_be_changed(): void
    {
        $version = $this->publish();
        $run = $this->recommend($this->application(), $version);
        $this->expectException(\LogicException::class);
        $run->update(['workflow_uuid' => $this->publish('Other')->uuid]);
    }

    public function test_only_superadmin_can_publish_or_change_availability(): void
    {
        $version = $this->publish();
        $this->actingAs(User::create(['email' => 'viewer@example.test']));
        Livewire::test(Edit::class, ['workflow' => $version])->assertStatus(403);
        Livewire::test(Create::class)->assertStatus(403);
        $this->expectException(HttpException::class);
        app(WorkflowVersionService::class)->setAvailability($version, false);
    }

    public function test_populated_legacy_migration_keeps_stage_ids_and_uses_evidence_over_the_type_default(): void
    {
        DB::purge('workflow_editor_testing');
        $this->createLegacySchema();
        $a = (string) Str::uuid();
        $b = (string) Str::uuid();
        foreach ([$a, $b] as $uuid) {
            DB::table('workflows')->insert(['uuid' => $uuid, 'name' => $uuid, 'description' => 'Preserved', 'is_active' => true]);
        }
        DB::table('permissions')->insert(['id' => 1, 'name' => 'Workflow Review', 'guard_name' => 'web']);
        $stage = (string) Str::uuid();
        DB::table('workflow_stages')->insert(['uuid' => $stage, 'workflow_uuid' => $a, 'name' => 'Original stage',
            'description' => 'Preserved stage', 'position' => 1, 'permission_id' => 1]);
        $type = (string) Str::uuid();
        DB::table('funeral_assistance_types')->insert(['uuid' => $type, 'name' => 'Type', 'description' => 'Type', 'workflow_uuid' => $b]);
        $application = (string) Str::uuid();
        $run = (string) Str::uuid();
        DB::table('applications')->insert(['uuid' => $application, 'client_uuid' => (string) Str::uuid(),
            'tracking_no' => 'LEGACY', 'qr_code' => 'LEGACY', 'current_workflow_stage_uuid' => $stage]);
        DB::table('recommendations')->insert(['uuid' => $run, 'application_uuid' => $application,
            'funeral_assistance_type_uuid' => $type, 'amount_extended' => 1000, 'mode_of_assistance_id' => 1,
            'recommended_by' => 1, 'created_at' => '2026-10-01 09:00:00']);
        $history = (string) Str::uuid();
        DB::table('workflow_histories')->insert(['uuid' => $history, 'recommendation_uuid' => $run,
            'to_stage_uuid' => $stage, 'date_in' => '2026-10-01 09:00:00', 'date_out' => '2026-10-01 09:05:00']);
        $this->migration()->up();
        $this->assertSame($a, DB::table('recommendations')->where('uuid', $run)->value('workflow_uuid'));
        $this->assertSame($run, DB::table('applications')->where('uuid', $application)->value('current_recommendation_uuid'));
        $this->assertSame($stage, DB::table('applications')->where('uuid', $application)->value('current_workflow_stage_uuid'));
        $this->assertSame($stage, DB::table('workflow_histories')->where('uuid', $history)->value('to_stage_uuid'));
        $this->assertSame('Original stage', DB::table('workflow_stages')->where('uuid', $stage)->value('name'));
        $this->assertSame($b, DB::table('funeral_assistance_types')->where('uuid', $type)->value('workflow_uuid'));
        $this->assertSame(1, DB::table('workflows')->where('uuid', $a)->value('version_number'));
    }

    public function test_audit_rejects_tied_legacy_latest_recommendations_without_guessing(): void
    {
        $version = $this->publish();
        $application = $this->application();
        $run = $this->recommend($application, $version);
        DB::table('applications')->where('uuid', $application->uuid)->update(['current_recommendation_uuid' => null]);
        $copy = $run->getAttributes();
        $copy['uuid'] = (string) Str::uuid();
        DB::table('recommendations')->insert($copy);
        $result = app(WorkflowVersionAudit::class)->inspect();
        $this->assertStringContainsString('tied latest', implode(' ', $result['errors']));
    }

    public function test_stage_form_stays_paused_and_rejects_a_pause_after_mount(): void
    {
        $version = $this->publish();
        $application = $this->application();
        $run = $this->recommend($application, $version);
        $component = Livewire::test(\App\Livewire\Workflow\History\Create::class, ['application' => $application->fresh()])
            ->assertSet('paused', false)->assertSet('recommendationUuid', $run->uuid);
        app(WorkflowVersionService::class)->setAvailability($version, false);
        $component->set('toStageUuid', $version->stages()->first()->uuid)
            ->set('dateIn', '2026-10-23 09:00:00')->set('dateOut', '2026-10-23 09:05:00')
            ->call('submit')->assertHasErrors('toStageUuid');
        Livewire::test(\App\Livewire\Workflow\History\Create::class, ['application' => $application->fresh()])
            ->assertSet('paused', true)->assertSee('Stage processing is paused');
        $this->assertSame(0, WorkflowHistory::count());
    }

    public function test_workflow_and_recommendation_factories_use_the_publication_and_restart_services(): void
    {
        $version = Workflow::factory()->create();
        $this->assertNotNull($version->published_at);
        $this->assertTrue($version->isSelectable());
        $application = $this->application();
        $type = $this->type($version);
        $run = Recommendation::factory()->create(['application_uuid' => $application->uuid,
            'funeral_assistance_type_uuid' => $type->uuid, 'recommended_by' => auth()->id()]);
        $this->assertSame($run->uuid, $application->fresh()->current_recommendation_uuid);
        $this->assertSame($version->uuid, $run->workflow_uuid);
    }

    public function test_recommendation_form_preselects_the_suggestion_and_allows_an_override(): void
    {
        $suggested = $this->publish();
        $type = $this->type($suggested);
        $override = $this->publish('Alternative flow');
        $application = $this->application();
        $form = Livewire::test(\App\Livewire\Recommendation\Create::class, ['application' => $application])
            ->set('createNew', true)->set('form.funeralAssistanceTypeUuid', $type->uuid)
            ->assertSet('form.workflowUuid', $suggested->uuid)
            ->set('form.workflowUuid', $override->uuid)
            ->set('form.amountExtended', 1234.50)->set('form.modeOfAssistanceId', 1)
            ->call('save')->assertHasNoErrors()->assertNoRedirect();
        $this->assertSame($override->uuid, $application->fresh()->currentWorkflow()->uuid);
        $form->assertSet('expectedRecommendationUuid', $application->fresh()->current_recommendation_uuid);
    }

    public function test_stale_recommendation_selection_is_rejected_after_publication(): void
    {
        $v1 = $this->publish();
        $type = $this->type($v1);
        $application = $this->application();
        $data = $this->definition();
        $data['description'] = 'Updated';
        app(WorkflowVersionService::class)->publish($data, $v1);
        try {
            $this->recommend($application, $v1, $type);
            $this->fail('Superseded selection accepted.');
        } catch (ValidationException $exception) {
            $this->assertArrayHasKey('form.workflowUuid', $exception->errors());
        }
        $this->assertSame(0, Recommendation::count());
    }

    public function test_permission_options_are_queried_once_per_editor_and_never_per_stage(): void
    {
        $data = $this->definition();
        $data['stages'] = array_fill(0, 8, $data['stages'][0]);
        $version = app(WorkflowVersionService::class)->publish($data);
        DB::enableQueryLog();
        DB::flushQueryLog();
        $editor = Livewire::test(Edit::class, ['workflow' => $version]);
        $queries = fn () => collect(DB::getQueryLog())->filter(fn ($query) => str_contains($query['query'], 'from "permissions"') && str_contains($query['query'], '"name" like'))->count();
        $this->assertSame(1, $queries());
        DB::flushQueryLog();
        $editor->call('addStage');
        $this->assertSame(0, $queries());
        DB::disableQueryLog();
    }

    public function test_simultaneous_publications_serialize_and_do_not_fork(): void
    {
        $database = sys_get_temp_dir().DIRECTORY_SEPARATOR.'workflow-concurrency-'.Str::uuid().'.sqlite';
        touch($database);
        $readyA = $database.'-ready-a';
        $readyB = $database.'-ready-b';
        $go = $database.'-go';
        $processes = [];
        try {
            DB::purge('workflow_editor_testing');
            config(['database.connections.workflow_editor_testing.database' => $database]);
            $this->createLegacySchema();
            $this->migration()->up();
            foreach (glob(database_path('migrations/custom_fields/*.php')) as $path) {
                (require $path)->up();
            }
            $actor = User::create(['email' => 'concurrency@example.test']);
            $actor->assignRole(Role::findOrCreate('superadmin', 'web'));
            $this->actingAs($actor);
            $base = app(WorkflowVersionService::class)->publish($this->customDefinition());
            foreach ([$readyA, $readyB] as $ready) {
                $process = new Process([
                    PHP_BINARY, base_path('tests/Support/publish-workflow-version.php'),
                    $database, $base->uuid, $ready, $go,
                ], base_path(), null, null, 25);
                $process->start();
                $processes[] = $process;
            }
            $deadline = microtime(true) + 15;
            while (! file_exists($readyA) || ! file_exists($readyB)) {
                foreach ($processes as $worker) {
                    if (! $worker->isRunning()) {
                        $this->fail($worker->getErrorOutput().$worker->getOutput());
                    }
                }
                if (microtime(true) > $deadline) {
                    $this->fail('Concurrent publication workers did not become ready.');
                }
                usleep(10000);
            }
            file_put_contents($go, 'go');
            $outcomes = [];
            foreach ($processes as $process) {
                $process->wait();
                $this->assertTrue($process->isSuccessful(), $process->getErrorOutput().$process->getOutput());
                $this->assertJson($process->getOutput(), $process->getErrorOutput().$process->getOutput());
                $outcomes[] = json_decode($process->getOutput(), true, 512, JSON_THROW_ON_ERROR)['result'];
            }
            sort($outcomes);
            $this->assertSame(['published', 'stale'], $outcomes);
            $this->assertSame([1, 2], Workflow::orderBy('version_number')->pluck('version_number')->all());
            $this->assertSame(8, CustomField::count());
        } finally {
            foreach ($processes as $process) {
                if ($process->isRunning()) {
                    $process->stop();
                }
            }
            DB::purge('workflow_editor_testing');
            foreach ([$readyA, $readyB, $go, $database] as $file) {
                if (is_file($file)) {
                    unlink($file);
                }
            }
        }
    }

    public function test_processing_seeders_and_forms_do_not_lazy_load_workflow_relationships(): void
    {
        $version = $this->publish();
        $application = $this->application();
        $run = $this->recommend($application, $version);
        $this->recommend($this->application(), $version);
        $violations = [];
        Model::handleLazyLoadingViolationUsing(
            function ($model, $relation) use (&$violations) {
                $violations[] = get_class($model).'.'.$relation;
            }
        );
        try {
            $applications = Application::with(['activeRecommendation.workflow', 'workflowStage'])->get();
            foreach ($applications as $item) {
                $item->preventsLazyLoading = true;
                $item->activeRecommendation->preventsLazyLoading = true;
                $this->assertSame($version->uuid, $item->currentWorkflow()->uuid);
                $this->assertNotNull($item->toStage());
            }
            $item = Application::findOrFail($application->uuid);
            $item->preventsLazyLoading = true;
            Livewire::test(\App\Livewire\Workflow\History\Create::class, ['application' => $item])
                ->assertSet('recommendationUuid', $run->uuid)->call('refresh');
            (new WorkflowHistorySeeder)->run();
            $this->assertSame([], $violations);
        } finally {
            Model::handleLazyLoadingViolationUsing(null);
        }
    }

    public function test_read_only_audit_supports_migration_sql_previews(): void
    {
        $queries = DB::pretend(fn () => app(WorkflowVersionAudit::class)->inspect());
        $this->assertNotEmpty($queries);
        $this->assertFalse(collect($queries)->contains(fn ($query) => preg_match('/^(insert|update|delete|alter|create|drop)\\s/i', $query['query']) === 1));
    }
}
