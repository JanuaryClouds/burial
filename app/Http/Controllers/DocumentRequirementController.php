<?php

namespace App\Http\Controllers;

use App\Models\DocumentRequirement;
use Illuminate\Http\Request;

class DocumentRequirementController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('document-requirement.index', [
            'pageTitle' => 'Required Documents to Submit',
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(DocumentRequirement $documentRequirement)
    {
        return view('document-requirement.edit', [
            'pageTitle' => 'Edit Document',
            'document' => $documentRequirement
        ]);
    }
}
