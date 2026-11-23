<?php

namespace App\Http\Controllers;

use App\Models\WalkInIntake;
use App\Services\WalkInIntakeService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Gate;

class WalkInIntakeController extends Controller
{
    public function index(): View
    {
        Gate::authorize('walk-in-intake');
        $intakes = WalkInIntake::with(['application', 'encoder'])
            ->when(! auth()->user()->isSystemAdministrator(), fn ($query) => $query
                ->where(fn ($query) => $query->whereNotNull('application_uuid')->orWhere('encoded_by', auth()->id())));

        return view('walk-in.index', ['pageTitle' => 'Walk-in Intake', 'intakes' => $intakes->latest()->get()]);
    }

    public function create(): View
    {
        Gate::authorize('walk-in-intake');

        return view('walk-in.edit', ['pageTitle' => 'New Walk-in Intake', 'intake' => null]);
    }

    public function edit(WalkInIntake $intake): View
    {
        app(WalkInIntakeService::class)->authorize($intake);
        abort_if($intake->application_uuid !== null, 403);

        return view('walk-in.edit', ['pageTitle' => 'Resume Walk-in Intake', 'intake' => $intake]);
    }

    public function receipt(): View
    {
        Gate::authorize('walk-in-intake');
        $receipt = session()->pull('walk_in_receipt');
        abort_unless(is_array($receipt) && ($receipt['expires'] ?? 0) >= now()->timestamp, 404);
        $intake = WalkInIntake::with('application')->findOrFail($receipt['uuid']);
        Gate::authorize('view', $intake->application);

        return view('walk-in.receipt', ['pageTitle' => 'Walk-in Acknowledgment', 'intake' => $intake, 'code' => Crypt::decryptString($receipt['code'])]);
    }

    public function replaceCode(Request $request, WalkInIntake $intake): View
    {
        Gate::authorize('walk-in-intake');
        $data = $request->validate(['reason' => ['required', 'string', 'max:2000'], 'identity_verified' => ['accepted']]);
        $code = app(WalkInIntakeService::class)->replaceCode($intake->uuid, $data['reason'], true);

        return view('walk-in.receipt', ['pageTitle' => 'Replacement Walk-in Claim Code', 'intake' => $intake->refresh()->load('application'), 'code' => $code]);
    }

    public function claimForm(): View
    {
        Gate::authorize('view-walk-in-claim');

        return view('walk-in.claim', ['pageTitle' => 'Link My Walk-in Records']);
    }

    public function claim(Request $request): RedirectResponse
    {
        Gate::authorize('claim-walk-in');
        $request->validate(['claim_code' => ['required', 'string', 'max:128'], 'confirmed' => ['accepted']]);
        $application = app(WalkInIntakeService::class)->claim($request->string('claim_code')->toString());

        return redirect()->route('application.show', $application)->with('success', 'Your walk-in records are now linked to your TLC account.');
    }
}
