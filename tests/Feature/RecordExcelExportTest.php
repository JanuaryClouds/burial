<?php

namespace Tests\Feature;

use App\Models\Application;
use App\Models\Beneficiary;
use App\Models\Client;
use App\Models\User;
use App\Models\WalkInIntake;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Tests\TestCase;

class RecordExcelExportTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        config(['database.default' => 'export_testing', 'database.connections.export_testing' => ['driver' => 'sqlite', 'database' => ':memory:', 'prefix' => '', 'foreign_key_constraints' => true], 'app.key' => 'base64:'.base64_encode(str_repeat('x', 32)), 'cache.default' => 'array', 'session.driver' => 'array']);
        Artisan::call('migrate', ['--database' => 'export_testing', '--force' => true]);
    }

    public function test_only_report_staff_can_download_and_filter_dates_must_be_valid(): void
    {
        $this->actingAs(User::factory()->admin()->create());
        foreach (['clients', 'applications', 'beneficiaries'] as $report) {
            $this->getJson(route($report.'.export.all'))->assertForbidden();
        }
        $this->actingAs(User::factory()->superadmin()->create());
        $this->get(route('clients.export.all', ['from' => '2026-03-01', 'until' => '2026-01-01']))->assertSessionHasErrors('until');
    }

    public function test_workbooks_include_only_submitted_records_and_selected_history(): void
    {
        $this->actingAs($staff = User::factory()->superadmin()->create());
        foreach (['sexes', 'relationships', 'religions'] as $table) {
            DB::table($table)->insert(['name' => $table]);
        }
        $owner = User::factory()->create();
        $intake = WalkInIntake::create(['encoded_by' => $staff->id, 'data' => []]);
        $client = new Client(['date_of_birth' => '1980-01-01', 'contact_number' => '00123']);
        $client->forceFill(['walk_in_intake_uuid' => $intake->uuid, 'identity_snapshot' => ['first_name' => 'Juan', 'last_name' => 'Cruz']])->save();
        Client::create(['user_id' => $owner->id, 'date_of_birth' => '1981-01-01', 'contact_number' => '00999']);
        $beneficiary = Beneficiary::create(['first_name' => '=Example', 'last_name' => 'Family', 'sex_id' => 1, 'religion_id' => 1, 'created_by' => $owner->id, 'date_of_birth' => '1950-01-01', 'date_of_death' => '2025-12-31', 'pwd' => false]);
        $application = Application::create(['client_uuid' => $client->uuid, 'beneficiary_uuid' => $beneficiary->uuid, 'relationship_id' => 1]);
        $intake->forceFill(['application_uuid' => $application->uuid])->save();
        foreach (['clients', 'beneficiaries', 'applications'] as $report) {
            $filters = ['from' => today()->toDateString(), 'until' => today()->toDateString(), 'status' => 'pending'];
            if ($report === 'applications') {
                $filters['workflow_history'] = 1;
            } elseif ($report === 'beneficiaries') {
                $filters += ['sex' => 1, 'pwd' => '0'];
            }
            $response = $this->get(route($report.'.export.all', $filters))->assertOk()->assertDownload();
            $path = $response->baseResponse->getFile()->getPathname();
            try {
                $book = IOFactory::load($path);
                $sheet = $book->getSheet(0);
                $this->assertStringContainsString($staff->fullname(), $sheet->getCell('A3')->getValue());
                $this->assertSame('Records on this sheet: 1', $sheet->getCell('A7')->getValue());
                if ($report === 'applications') {
                    $this->assertSame([
                        'Tracking No.', 'Application Date', 'SWA / Worker', 'Current Claimant', 'Beneficiary',
                        'Relationship to Beneficiary', 'Beneficiary Date of Birth', 'Age at Death', 'Sex',
                        'Claimant Barangay', 'Claimant Address', 'Claimant Contact Number', 'Assistance Type',
                        'Recommended Amount', 'Date of Death', 'Application Status',
                    ], $sheet->rangeToArray('A8:'.$sheet->getHighestDataColumn(8).'8')[0]);
                    $this->assertSame('B9', $sheet->getFreezePane());
                    $this->assertSame('A8:P9', $sheet->getAutoFilter()->getRange());
                    $this->assertSame('Juan Cruz', $sheet->getCell('D9')->getValue());
                    $this->assertSame('pending', $sheet->getCell('P9')->getValue());
                    $this->assertNull($sheet->getCell('C9')->getValue());
                    $this->assertSame(['Applications', 'Workflow History'], $book->getSheetNames());
                    $this->assertSame($application->tracking_no, $sheet->getCell('A9')->getValue());
                } elseif ($report === 'clients') {
                    $this->assertSame('Juan Cruz', $sheet->getCell('B9')->getValue());
                    $this->assertSame('00123', $sheet->getCell('E9')->getValue());
                } else {
                    $this->assertSame('s', $sheet->getCell('B9')->getDataType());
                }
            } finally {
                unlink($path);
            }
        }
        $response = $this->get(route('applications.export.all', ['claimant_changes' => 1]))->assertDownload();
        $path = $response->baseResponse->getFile()->getPathname();
        try {
            $this->assertSame(['Applications', 'Claimant Changes'], IOFactory::load($path)->getSheetNames());
        } finally {
            unlink($path);
        }
        $this->get(route('applications.export.all', ['status' => 'closed']))->assertRedirect()->assertSessionHas('export_message');
        $this->getJson(route('applications.export.all', ['status' => 'closed']))
            ->assertNotFound()
            ->assertJsonPath('message', 'No records match these export filters.');
    }
}
