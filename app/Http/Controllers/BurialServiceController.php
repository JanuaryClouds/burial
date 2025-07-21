<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class BurialServiceController extends Controller
{
    //

    public function new() {
        return view("admin.newBurialService");
    }

    public function history() {
        // TODO: Fetch all histories of burial services
        return view("admin.burialServiceHistory");
    }

    public function providers() {
        // TODO: Fetch all burial service providers
        return view("admin.burialServiceProviders");
    }

    public function newProvider() {
        return view('admin.newBurialServiceProvider');
    }
}
