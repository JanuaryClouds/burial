<?php

namespace App\Livewire\User;

use App\Livewire\Forms\CreateUserForm;
use App\Services\ActivityLoggerService;
use App\Services\Auth\UserService;
use App\Traits\Livewire\HasPlaceholder;
use Illuminate\Validation\ValidationException;
use Livewire\Component;
use Spatie\Permission\Models\Role;

class Create extends Component
{
    use HasPlaceholder;

    public function boot(): void
    {
        abort_unless(auth()->user()?->isSystemAdministrator(), 403);
    }

    public CreateUserForm $form;

    public function getRoleOptionsProperty(): array
    {
        return Role::where('guard_name', 'web')->whereNotIn('name', ['staff', 'superadmin'])->orderBy('name')->pluck('name')->all();
    }

    public function clearForm()
    {
        $this->form->reset();
    }

    public function save()
    {
        try {
            $this->form->validate();
        } catch (\Exception $e) {
            $this->form->reset('password', 'confirmPassword');
            $this->dispatch('notification:toast', [
                'type' => 'error',
                'text' => app()->hasDebugModeEnabled() ? $e->getMessage() : config('constants.errors.validation'),
            ]);

            return;
        }

        try {
            $user = app(UserService::class)->store($this->form->all());

            $this->dispatch('notification:alert', [
                'type' => 'success',
                'text' => 'Successfully created new user',
            ]);

            ActivityLoggerService::logSuccess('Successfully created new user', [
                'id' => $user->id,
            ]);

            $this->form->reset();
        } catch (ValidationException $exception) {
            $this->form->reset('password', 'confirmPassword');
            foreach ($exception->errors() as $key => $messages) {
                $this->addError('form.'.$key, $messages[0]);
            }
        } catch (\Throwable $th) {
            $this->dispatch('notification:alert', [
                'type' => 'error',
                'text' => app()->hasDebugModeEnabled() ? $th->getMessage() : config('constants.errors.unknown'),
            ]);

            activity('account-management')->causedBy(auth()->user())
                ->withProperties(['exception_type' => $th::class])->log('Account creation failed');
        }
    }

    public function render()
    {
        return view('livewire.user.create');
    }
}
