<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\FuneralAssistance;

class FuneralAssistanceController extends Controller
{
    public function index() {
        $page_title = 'Funeral Assistances';
        $resource = 'funeral-assistances';
        $renderColumns = ['client_id', 'action'];
        $data = FuneralAssistance::select('id', 'client_id')
            ->with('client')
            ->get();

        return view('admin.funeral.index', compact('data', 'page_title', 'resource', 'renderColumns'));
    }
}
