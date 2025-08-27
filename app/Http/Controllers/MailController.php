<?php

namespace App\Http\Controllers;

use App\Mail\MailRepresentative;
use App\Mail\ProviderMail;
use App\Models\BurialAssistanceRequest;
use App\Models\BurialService;
use App\Models\BurialServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class MailController extends Controller
{
    public function service(Request $request, $id) {
        $request->validate([
            'subject' => 'required|string|max:64',
            'message' => 'required|string',
        ]);

        $service = BurialService::find($id)->first();

        if ($service) {
            Mail::to($service->representative_email)->send(new MailRepresentative(
                $service,
                $request->subject,
                $request->message,
            ));

            return redirect()->back()->with('success', 'Message sent successfully to the service client.');
        } else {
            return redirect()->back()->with('error', 'Assistance request not found.');
        }
    }

    public function request(Request $request, $uuid) {
        $request->validate([
            'subject' => 'required|string|max:64',
            'message' => 'required|string',
        ]);

        $assistanceRequest = BurialAssistanceRequest::where("uuid", $uuid)->first();

        if ($assistanceRequest) {
            Mail::to($assistanceRequest->representative_email)->send(new MailRepresentative(
                $assistanceRequest,
                $request->subject,
                $request->message,
            ));

            return redirect()->back()->with('success', 'Message sent successfully to the requestor.');
        } else {
            return redirect('')->back()->with('error', 'Assistance request not found.');
        }
    }

    public function provider(Request $request, $id) {
        $request->validate([
            'subject' => 'required|string|max:64',
            'message' => 'required|string',
        ]);
        $provider = BurialServiceProvider::find($id);
        if ($provider) {
            Mail::to($provider->email)->send(new ProviderMail(
                $provider,
                $request->subject,
                $request->message,
            ));

            return redirect()->back()->with('success', 'Message sent successfully to the service provider.');
        } else {
            return redirect()->back()->with('error', 'Service provider not found.');
        }
    }    
}
