<?php

namespace App\Livewire\User;

use App\Services\Auth\UserService;
use Illuminate\Validation\ValidationException;
use Livewire\Component;

class ChangePassword extends Component
{
    public string $currentPassword = '';

    public string $newPassword = '';

    public string $confirmNewPassword = '';

    public function boot(): void
    {
        abort_unless(auth()->user()?->can('changePassword', auth()->user()), 403);
    }

    public function save(): void
    {
        try {
            app(UserService::class)->changePassword(auth()->user(), [
                'currentPassword' => $this->currentPassword,
                'newPassword' => $this->newPassword,
                'confirmNewPassword' => $this->confirmNewPassword,
            ]);
        } catch (ValidationException $exception) {
            $this->reset('currentPassword', 'newPassword', 'confirmNewPassword');
            throw $exception;
        }
        $this->reset('currentPassword', 'newPassword', 'confirmNewPassword');
        session()->flash('success', 'Your password has been changed. Please sign in again.');
        $this->redirectRoute('admin.login');
    }

    public function render()
    {
        return view('livewire.user.change-password');
    }
}
