<?php

namespace Tests\Feature;

use App\Models\BeneficiaryFamily;
use App\Models\User;
use App\Models\Workflow;
use App\View\Components\PageNavigation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Route;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class PageNavigationTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        config(['database.default' => 'navigation_testing',
            'database.connections.navigation_testing' => ['driver' => 'sqlite', 'database' => ':memory:', 'prefix' => ''],
            'cache.default' => 'array']);
        foreach (['genders', 'relationships', 'civilStatus', 'religions', 'nationalities', 'educations', 'districts', 'assistances', 'modes'] as $lookup) {
            Cache::put($lookup, collect(), 3600);
        }
    }

    private function navigation(string $name, array $parameters = [], ?User $user = null, ?string $label = null): PageNavigation
    {
        $request = Request::create(route($name, $parameters));
        $route = clone Route::getRoutes()->getByName($name);
        $route->bind($request);
        foreach ($parameters as $key => $value) {
            $route->setParameter($key, $value);
        }
        $request->setRouteResolver(fn () => $route);
        $this->app->instance('request', $request);
        $request->setUserResolver(fn () => $user);

        return new PageNavigation($request, currentLabel: $label);
    }

    public function test_top_level_pages_have_no_navigation_or_empty_surface(): void
    {
        foreach (['dashboard', 'application.index', 'client.index', 'beneficiary.index', 'system.index', 'report.index'] as $name) {
            $navigation = $this->navigation($name);

            $this->assertSame([], $navigation->items);
            $this->assertNull($navigation->parent);
            $this->assertStringNotContainsString('<nav', $navigation->render()->with($navigation->data())->render());
        }
    }

    public function test_admin_sections_and_edits_have_explicit_parents(): void
    {
        $workflow = new Workflow;
        $workflow->setRawAttributes(['uuid' => 'workflow-123', 'name' => 'Burial Assistance', 'version_number' => 2]);

        $navigation = $this->navigation('workflows.edit', ['workflow' => $workflow], label: 'Edit Burial Assistance');

        $this->assertSame(['System', 'Workflows', 'Edit Burial Assistance'], array_column($navigation->items, 'label'));
        $this->assertSame(route('workflows.index'), $navigation->parent['url']);
        $this->assertNull($navigation->items[2]['url']);

        $navigation = $this->navigation('workflows.stage.edit', ['workflow' => $workflow, 'stage' => 'stage-456']);

        $this->assertSame($workflow->label(), $navigation->parent['label']);
        $this->assertSame(route('workflows.edit', $workflow), $navigation->parent['url']);

        foreach (['workflows.index', 'role.index', 'document_requirements.index', 'funeral_assistance_types.index', 'mode_of_assistances.index', 'user.index', 'system.settings', 'system.logs.index'] as $name) {
            $this->assertSame(route('system.index'), $this->navigation($name)->parent['url']);
        }
    }

    public function test_record_edits_return_to_details_and_create_pages_return_to_indexes(): void
    {
        foreach (['client', 'beneficiary'] as $resource) {
            $navigation = $this->navigation($resource.'.edit', [$resource => 'record-123']);
            $this->assertSame(route($resource.'.show', 'record-123'), $navigation->parent['url']);
        }

        foreach (['client', 'beneficiary', 'application', 'workflows', 'role'] as $resource) {
            $this->assertSame(route($resource.'.index'), $this->navigation($resource.'.create')->parent['url']);
        }

        foreach (['document_requirements' => 'document', 'funeral_assistance_types' => 'funeralAssistanceType', 'mode_of_assistances' => 'modeOfAssistance', 'role' => 'role'] as $resource => $parameter) {
            $this->assertSame(route($resource.'.index'), $this->navigation($resource.'.edit', [$parameter => 'record-123'])->parent['url']);
        }
    }

    public function test_deep_application_and_family_pages_link_to_the_bound_parent(): void
    {
        foreach (['application.assessment.create', 'application.recommendation.create'] as $name) {
            $navigation = $this->navigation($name, ['application' => 'application-123']);
            $this->assertSame(route('application.show', 'application-123'), $navigation->parent['url']);
        }

        $member = new BeneficiaryFamily;
        $member->setRawAttributes(['uuid' => 'member-123', 'beneficiary_uuid' => 'beneficiary-456']);
        $navigation = $this->navigation('beneficiary.family.edit', ['member' => $member]);
        $this->assertSame(route('beneficiary.show', 'beneficiary-456'), $navigation->parent['url']);
        $this->assertSame(route('system.logs.index'), $this->navigation('system.logs.show', ['log' => 123])->parent['url']);
        $this->assertSame(route('application.index'), $this->navigation('application.search')->parent['url']);
    }

    public function test_profile_navigation_does_not_send_regular_users_to_admin_pages(): void
    {
        $user = new User;
        $user->setRelation('roles', collect());
        foreach (['user.show', 'user.edit'] as $name) {
            $navigation = $this->navigation($name, ['user' => 123], $user);
            $this->assertSame(route('dashboard'), $navigation->parent['url']);
            $this->assertNotContains(route('user.index'), array_column($navigation->items, 'url'));
        }

        $user->setRelation('roles', collect([new Role(['name' => 'superadmin'])]));
        $this->assertTrue($user->hasRole('superadmin'));
        $this->assertSame(route('user.index'), $this->navigation('user.edit', ['user' => 123], $user)->parent['url']);
    }

    public function test_blade_component_escapes_labels_and_renders_both_responsive_presentations(): void
    {
        $label = '<img src=x onerror=alert(1)>';
        $this->navigation('client.edit', ['client' => 'client-123'], label: $label);
        $html = Blade::render('<x-page-navigation :current-label="$label" />', ['label' => $label]);

        $this->assertStringContainsString('aria-label="Breadcrumb"', $html);
        $this->assertStringContainsString('aria-current="page"', $html);
        $this->assertStringContainsString('d-none d-md-flex', $html);
        $this->assertStringContainsString('d-md-none', $html);
        $this->assertStringContainsString(e($label), $html);
        $this->assertStringNotContainsString($label, $html);
        $this->assertStringContainsString(route('client.show', 'client-123'), $html);
    }
}
