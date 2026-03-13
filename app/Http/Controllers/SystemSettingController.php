<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateSystemSettingsRequest;
use App\Models\SystemSetting;
use App\Services\SystemSettingService;

class SystemSettingController extends Controller
{
    protected $systemSettingServices;

    public function __construct(SystemSettingService $systemSettingService)
    {
        $this->systemSettingServices = $systemSettingService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user = auth()->user();
        if (! $user || ! $user->hasRole('superadmin')) {
            return redirect()->back()->with('warning', 'You do not have access to this page.');
        }

        return view('system.index', [
            'settings' => SystemSetting::first(),
            'page_title' => 'System Settings',
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateSystemSettingsRequest $request, SystemSetting $systemSetting)
    {
        $ip = request()->ip();
        $browser = request()->header('User-Agent');

        try {
            $this->systemSettingServices->update($request->validated());

            activity()
                ->causedBy(auth()->user())
                ->withProperties(['ip' => $ip, 'browser' => $browser])
                ->log('System settings have been updated.');

            return redirect()
                ->back()
                ->with('success', 'Successfully updated system settings.');
        } catch (\Throwable $th) {
            activity()
                ->causedBy(auth()->user())
                ->withProperties(['ip' => $ip, 'browser' => $browser])
                ->log('Failed to update system settings.');

            return redirect()
                ->back()
                ->with('error', 'Unable to update system settings.');
        }
    }
}
