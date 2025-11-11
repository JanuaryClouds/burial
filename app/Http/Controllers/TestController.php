<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class TestController extends Controller
{
    public function post(Request $request) {
        dd($request->all());
        return redirect()->back();
    }
}
