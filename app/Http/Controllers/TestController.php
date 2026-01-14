<?php

namespace App\Http\Controllers;

use App\Models\Client;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class TestController extends Controller
{
    public function get($id)
    {
        $client = Client::find($id);

        $pdf = Pdf::loadView('pdf.certificate-of-eligibility',
            compact('client'))
            ->setPaper('letter', 'portrait');

        return $pdf->stream("certificate-of-eligibility-{$client->id}.pdf");
    }

    public function post(Request $request) {
        return $request->json();
    }
}
