<?php

namespace App\Livewire\Auth;

use App\Services\Auth\LoginService;
use Livewire\Component;

class Login extends Component
{
    public string $email = '';

    public string $password = '';

    public function updatedEmail(): void
    {
        $this->resetErrorBag();
    }

    public function submit(): void
    {
        $this->resetErrorBag();
        try {
            $result = app(LoginService::class)->attempt(['email' => $this->email, 'password' => $this->password]);
        } finally {
            $this->reset('password');
        }
        if (isset($result['redirect'])) {
            $this->redirect($result['redirect']);

            return;
        }
        if ($result['retryAfter'] === 0) {
            $this->addError('credentials', $result['message']);
        }
        $this->dispatch('login-failed', retryAfter: $result['retryAfter'], recovery: $result['failures'] >= 3);
    }

    public function render()
    {
        return view('livewire.auth.login');
    }
}
