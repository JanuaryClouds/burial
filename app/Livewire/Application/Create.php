<?php

namespace App\Livewire\Application;

use App\Models\Application;
use App\Models\Beneficiary;
use App\Models\Client;
use App\Services\ImageService;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Rule;
use Livewire\Component;
use Livewire\WithFileUploads;

class Create extends Component
{
    use WithFileUploads;

    public Collection $clientOptions;
    public Collection $beneficiaryOptions;

    #[Rule('required|exists:clients,uuid')]
    public ?string $clientUuid = null;

    #[Rule('required|exists:beneficiaries,uuid')]
    public ?string $beneficiaryUuid = null;

    #[Rule('required|exists:relationships,id')]
    public string $relationshipId;

    public ?Client $client = null;

    public ?Beneficiary $beneficiary = null;

    public $images = [];

    public bool $agreedToTerms = false;

    public function mount()
    {
        $this->clientOptions = Client::whereDoesntHave('application')
            ->with('user')
            ->where('user_id', '=', Auth::id())
            ->orderByDesc('created_at')
            ->get()
            ->mapWithKeys(fn($client) => [$client->uuid => $client->fullname() . ' (created in ' . Carbon::parse($client->created_at)->format('d M Y, h:i A') . ')']);

        $this->beneficiaryOptions = Beneficiary::whereDoesntHave('application')
            ->where('created_by', '=', Auth::id())
            ->orderBy('created_at')
            ->get()
            ->mapWithKeys(fn($beneficiary) => [$beneficiary->uuid => $beneficiary->fullname() . ' (created in ' . Carbon::parse($beneficiary->created_at)->format('d M Y, h:i A') . ')']);
    
        if (session()->has('client_uuid')) {
            $this->clientUuid = session('client_uuid');
        }

        if (session()->has('beneficiary_uuid')) {
            $this->beneficiaryUuid = session('beneficiary_uuid');
        }
    }

    public function removeImage(string $key)
    {
        unset($this->images[$key]);
    }

    public function clearForm()
    {
        $this->reset([
            'clientUuid',
            'beneficiaryUuid',
            'relationshipId',
            'images',
            'client',
            'beneficiary'
        ]);
        
        $this->dispatch('notification:alert', [
            'type' => 'success',
            'title' => 'Form Cleared',
            'text' => 'The form has been cleared. You can now start a new application.'
        ]);
    }

    public function save()
    {
        try {
            $this->validate();
        } catch (ValidationException $e) {
            $this->dispatch('notification:alert', [
                'type' => 'error',
                'title' => 'Invalid Form Submitted',
                'text' => 'Please check your inputs and try again.',
            ]);

            report($e);
            return;
        }
        
        $imageService = App(ImageService::class);

        try {
            DB::transaction(function () use ($imageService) {
                $application = Application::create([
                    'client_uuid' => $this->clientUuid,
                    'beneficiary_uuid' => $this->beneficiaryUuid,
                    'relationship_id' => $this->relationshipId
                ]);

                foreach ($this->images as $key => $file) {
                    if (!$file) {
                        continue;
                    }

                    $filename = $application->tracking_no .'-'. Str::replace($key, '_', '-');
                    $imageService->post($filename, $file);    
                }

                activity()
                    ->causedBy(Auth::user())
                    ->withProperties([
                        'ip' => request()->ip(),
                        'browser' => request()->header('User-Agent'),
                        'application_uuid' => $application->uuid,
                    ])
                    ->log('Application created with '. count($this->images) . ' images');

                $this->reset();

                $this->dispatch('notification:alert', [
                    'type' => 'success',
                    'title' => 'Application Submitted Successfully',
                    'text' => 'Your application has been submitted. The information you have attached will no longer be available for editing.'
                ]);

                $this->redirect(route('application.show', $application));
            });
        } catch (\Throwable $th) {
            if (app()->hasDebugModeEnabled()) {
                $this->dispatch('notification:alert', [
                    'type' => 'error',
                    'title' => 'Error',
                    'text' => $th->getMessage()
                ]);
            } else {
                $this->dispatch('notification:alert', [
                    'type' => 'error',
                    'text' => 'Something went wrong. Try again later.'
                ]);
            }

            activity()
                ->withProperties([
                    'application' => $application->uuid ?? null,
                    'ip' => request()->ip(),
                    'browser' => request()->userAgent(),
                ])
                ->causedBy(Auth::user())
                ->log('Failed to create application');
        }
    }

    public function render()
    {
        return view('livewire.application.create');
    }
}
