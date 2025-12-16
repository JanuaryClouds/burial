<?php

namespace Tests\Feature;

use Http;
use Tests\TestCase;

class DisbursementTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    public function test_api_post_to_disbursement(): void
    {
        $data = [
            'key' => 'burial',
            'payee' => 'John Doe',
            'particulars' => 'For payment of funeral to the deceased who died Januart 1, 1999 on as per Ordinance No.34 series Of 2015',
            'cheque_number' => '12345',
            'amount' => '21000',
            'date' => '2023-01-01',
        ];

        Http::fake([
            route('test.component.post') => Http::sequence()
                ->push(['message' => 'Disbursement created successfully', 'data' => $data['key'].' '.$data['payee'].' '.$data['particulars'].' '.$data['cheque_number'].' '.$data['amount'].' '.$data['date'].''], 201)
                ->push(['message' => 'Disbursement created failed'], 400),
        ]);

        $response = Http::withHeaders([
            'X-Secret-Key' => env('API_KEY_DISBURSEMENT_SYSTEM'),
            'Content-Type' => 'application/json',
        ])
            ->post(route('test.component.post'), $data);

        $this->assertEquals(201, $response->status());
        $this->assertEquals($data['key'].' '.$data['payee'].' '.$data['particulars'].' '.$data['cheque_number'].' '.$data['amount'].' '.$data['date'].'', $response->json()['data']);

        $response = Http::withHeaders([
            'X-Secret-Key' => env('API_KEY_DISBURSEMENT_SYSTEM'),
            'Content-Type' => 'application/json',
        ])
            ->post(route('test.component.post'), $data);

        $this->assertEquals(400, $response->status());
        $this->assertEquals('Disbursement created failed', $response->json()['message']);
    }
}
