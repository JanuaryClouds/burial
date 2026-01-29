<?php

namespace Tests\Feature;

use App\Models\BurialAssistance;
use Http;
use Illuminate\Http\Client\Request;
use Tests\TestCase;
use App\Services\ProcessLogService;

class DisbursementTest extends TestCase
{
    public $processLogService;

    public function setUp(): void
    {
        parent::setUp();
        $this->processLogService = new ProcessLogService();
    }

    /**
     * A basic feature test example.
     */
    public function test_api_post_to_disbursement(): void
    {
        Http::fake([
            config('services.disbursement.url') => 
                Http::response(['message' => 'Disbursement created successfully', 'reference' => '2025-0001'], 201),
        ]);

        $service = app(ProcessLogService::class);
        $result = $service->createDisbursement(
            data: [
                'extra_data' => [
                    'cheque_number' => '12345',
                    'amount' => '21000',
                ],
                'date_in' => '2023-01-01',
            ],
            claimant: 'John Doe',
            deceased: 'Jane Doe',
            dod: '1999-01-01'
        );

        $this->assertEquals('Disbursement created successfully', $result['message']);
        Http::assertSent(function (Request $request) {
            return $request->url() === config('services.disbursement.url')
                && $request->hasHeader('X-Secret-Key')
                && $request['key'] === 'burial'
                && $request['amount'] === '21000';
        });
    }
}
