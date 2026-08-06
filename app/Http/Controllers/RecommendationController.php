<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreRecommendationRequest;
use App\Http\Requests\UpdateRecommendationRequest;
use App\Models\Application;
use App\Models\Recommendation;
use App\Services\RecommendationService;

class RecommendationController extends Controller
{
    public function __construct(
        protected RecommendationService $recommendationServices,
    ) {}

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
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
    public function store(StoreRecommendationRequest $request, Application $application)
    {
        try {
            $this->recommendationServices->store($request->validated(), $application->uuid);
            activity()
                ->causedBy(auth()->user())
                ->withProperties([
                    'application' => $application,
                ])
                ->log('Recommendation created');

            return back()->with('success', 'Successfully created an assistance recommendation. Please wait for approval.');
        } catch (\Throwable $th) {
            activity()
                ->causedBy(auth()->user())
                ->withProperties([
                    'application' => $application,
                ])
                ->log('Unable to create a recommendation');

            return back()->with('error', 'Failed to create a recommendation to the application');
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Recommendation $recommendation)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Recommendation $recommendation)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateRecommendationRequest $request, Recommendation $recommendation)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Recommendation $recommendation)
    {
        //
    }
}
