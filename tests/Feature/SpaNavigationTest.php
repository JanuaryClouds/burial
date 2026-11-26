<?php

namespace Tests\Feature;

use App\Models\Application;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\DB;
use Tests\Support\EnvironmentFixtures;
use Tests\TestCase;

class SpaNavigationTest extends TestCase
{
    use EnvironmentFixtures;

    protected function setUp(): void
    {
        parent::setUp();
        $this->initializeEnvironment();
    }

    protected function tearDown(): void
    {
        DB::purge('environment_testing');
        parent::tearDown();
    }

    private function links(string $html, string $url): \DOMNodeList
    {
        $document = new \DOMDocument;
        @$document->loadHTML($html);

        return (new \DOMXPath($document))->query('//a[@href="'.$url.'"]');
    }

    public function test_dashboard_enables_core_sidebar_links_but_keeps_system_navigation_normal(): void
    {
        $this->staff();

        $response = $this->get(route('dashboard'))->assertOk();

        foreach (['dashboard', 'application.index', 'client.index', 'beneficiary.index'] as $name) {
            $links = $this->links($response->getContent(), route($name));
            $this->assertGreaterThan(0, $links->length, $name);
            foreach ($links as $link) {
                $this->assertTrue($link->hasAttribute('wire:navigate'), $name);
            }
        }
        foreach ($this->links($response->getContent(), route('system.index')) as $link) {
            $this->assertFalse($link->hasAttribute('wire:navigate'));
        }
        $response->assertSee('data-navigate-track', false)
            ->assertSee('data-spa-navigation-loader', false)
            ->assertSee(asset('images/city_logo.webp'), false)
            ->assertSee('Loading page');
    }

    public function test_only_opted_in_tables_render_navigation_metadata(): void
    {
        $columns = collect([['data' => 'name'], ['data' => 'show_route']]);
        $data = collect([['name' => 'Record', 'show_route' => route('application.show', 'example')]]);

        $html = Blade::render('<x-data-table data-table-id="core" :columns="$columns" :data="$data" :navigate="true" />
            <x-data-table data-table-id="ordinary" :columns="$columns" :data="$data" />', compact('columns', 'data'));

        $document = new \DOMDocument;
        @$document->loadHTML($html);
        $xpath = new \DOMXPath($document);
        $this->assertSame('true', $xpath->query('//table[@id="dataTable-core"]')->item(0)->getAttribute('data-navigate-records'));
        $this->assertSame('false', $xpath->query('//table[@id="dataTable-ordinary"]')->item(0)->getAttribute('data-navigate-records'));
    }

    public function test_citizen_lists_keep_the_same_response_and_enable_record_navigation(): void
    {
        $this->citizen();

        foreach (['dashboard', 'application.index', 'client.index', 'beneficiary.index'] as $name) {
            $this->withHeader('X-Livewire-Navigate', '')->get(route($name))->assertOk()
                ->assertSee('data-navigate-records="true"', false);
        }
    }

    public function test_navigation_requests_cannot_read_another_citizens_application(): void
    {
        $owner = $this->citizen();
        $application = Application::factory()->create([
            'client_uuid' => $this->client($owner)->uuid,
            'beneficiary_uuid' => $this->beneficiary($owner)->uuid,
            'relationship_id' => 1,
        ]);
        $this->citizen();

        $this->withHeader('X-Livewire-Navigate', '')->get(route('application.show', $application))
            ->assertRedirect(route('dashboard'))
            ->assertSessionHas('error', 'You do not have permission to access this page.');
    }
}
