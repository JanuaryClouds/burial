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

        // $data = [
        //     'key' => 'burial',
        //     'payee' => 'John Doe',
        //     'particulars' => 'For payment of funeral to the deceased who died Januart 1, 1999 on as per Ordinance No.34 series Of 2015',
        //     'cheque_number' => '12345',
        //     'amount' => '21000',
        //     'date' => '2023-01-01',
        // ];

        // Http::fake([
        //     route('test.component.post') => Http::sequence()
        //         ->push(['message' => 'Disbursement created successfully', 'data' => $data['key'].' '.$data['payee'].' '.$data['particulars'].' '.$data['cheque_number'].' '.$data['amount'].' '.$data['date'].''], 201)
        //         ->push(['message' => 'Disbursement created failed'], 400),
        // ]);

        // $response = Http::withHeaders([
        //     'X-Secret-Key' => env('API_KEY_DISBURSEMENT_SYSTEM'),
        //     'Content-Type' => 'application/json',
        // ])
        //     ->post(route('test.component.post'), $data);

        // $this->assertEquals(201, $response->status());
        // $this->assertEquals($data['key'].' '.$data['payee'].' '.$data['particulars'].' '.$data['cheque_number'].' '.$data['amount'].' '.$data['date'].'', $response->json()['data']);

        // $response = Http::withHeaders([
        //     'X-Secret-Key' => env('API_KEY_DISBURSEMENT_SYSTEM'),
        //     'Content-Type' => 'application/json',
        // ])
        //     ->post(route('test.component.post'), $data);

        // $this->assertEquals(400, $response->status());
        // $this->assertEquals('Disbursement created failed', $response->json()['message']);
    }
}
