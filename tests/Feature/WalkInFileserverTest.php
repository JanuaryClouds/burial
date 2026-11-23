<?php

namespace Tests\Feature;

use App\Services\WalkInFileserverService;
use Illuminate\Support\Facades\Http;
use PHPUnit\Framework\Attributes\TestWith;
use Tests\TestCase;

class WalkInFileserverTest extends TestCase
{
    #[TestWith(['local', true, false, false, 'primary fileserver upload service is enabled'])]
    #[TestWith(['local', true, true, true, 'primary fileserver upload service is enabled'])]
    #[TestWith(['local', false, false, true, null])]
    #[TestWith(['local', false, true, true, null])]
    #[TestWith(['local', false, true, false, 'fileserver mock is offline'])]
    #[TestWith(['production', true, false, false, null])]
    #[TestWith(['staging', false, false, false, 'primary fileserver upload service is disabled'])]
    #[TestWith(['production', true, false, true, 'fileserver mock is enabled or running'])]
    #[TestWith(['production', false, false, true, 'fileserver mock is enabled or running'])]
    #[TestWith(['production', true, true, false, 'fileserver mock is enabled or running'])]
    public function test_submission_obeys_environment_primary_and_mock_availability(string $environment, bool $primary, bool $mockEnabled, bool $online, ?string $expected): void
    {
        app()->instance('env', $environment);
        config(['services.fileserver.enable.post' => $primary, 'services.fileserver.mock' => $mockEnabled, 'services.fileserver.mock_port' => 18991]);
        Http::preventStrayRequests();
        Http::fake(['http://127.0.0.1:18991/health' => Http::response($online ? ['service' => 'burial-fileserver-mock'] : [], $online ? 200 : 404)]);
        $warning = app(WalkInFileserverService::class)->submissionWarning();
        if ($expected === null) {
            $this->assertNull($warning);
        } else {
            $this->assertStringContainsString($expected, $warning);
        }
        Http::assertNotSent(fn ($request) => $request->url() !== 'http://127.0.0.1:18991/health');
    }

    public function test_an_unrelated_service_on_the_port_is_not_treated_as_the_mock(): void
    {
        app()->instance('env', 'local');
        config(['services.fileserver.enable.post' => false, 'services.fileserver.mock_port' => 18991]);
        Http::fake(['http://127.0.0.1:18991/health' => Http::response(['service' => 'unrelated-service'])]);
        $this->assertStringContainsString('fileserver mock is offline', app(WalkInFileserverService::class)->submissionWarning());
    }

    public function test_connection_failure_produces_actionable_warning_without_leaking_transport_details(): void
    {
        app()->instance('env', 'local');
        config(['services.fileserver.enable.post' => false, 'services.fileserver.mock_port' => 18991]);
        Http::fake(['http://127.0.0.1:18991/health' => Http::failedConnection()]);
        $warning = app(WalkInFileserverService::class)->submissionWarning();
        $this->assertStringContainsString('Start the configured fileserver mock', $warning);
        $this->assertStringNotContainsString('127.0.0.1', $warning);
    }
}
