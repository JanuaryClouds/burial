<?php

namespace App\Http\Controllers;

use App\Models\Application;
use App\Http\Requests\StoreApplicationRequest;
use App\Http\Requests\UpdateApplicationRequest;
use App\Services\ApplicationService;
use App\Services\DatatableService;

class ApplicationController extends Controller
{
    public function __construct(
        protected DatatableService $datatableServices,
        protected ApplicationService $applicationServices,
    ) {}

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $data = $this->applicationServices->index();

        if (request()->expectsJson()) {
            return response()->json([
                'data' => $data->values(),
            ]);
        }

        return view('application.index', [
            'data' => $data,
            'columns' => $this->datatableServices->getColumns($data),
            'page_title' => 'Applications',
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreApplicationRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Application $application)
    {
        return view('application.show', [
            'application' => $application,
            'page_title' => $application->tracking_no
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Application $application)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateApplicationRequest $request, Application $application)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Application $application)
    {
        //
    }
}
