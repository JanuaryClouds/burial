<?php

namespace App\Http\Controllers;

use App\Mail\VerifyAssistanceRequestMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class VerificationController extends Controller
{
    public function sendCode(Request $request) {
        $email = $request->input("email");
        $code = rand(100000, 999999);
        session(["verification_code" => $code]);
        Mail::to($email)->send(new VerifyAssistanceRequestMail($code));

        return response()->json(["success" => true]);
    }

    public function verifyCode(Request $request) {
        $inputCode = $request->input("code");
        $sessionCode = session("verification_code");

        if ($inputCode == $sessionCode) {
            return response()->json(["valid" => true]);
        } else {
            return response()->json(["valid" => false]);
        }
    }
}
