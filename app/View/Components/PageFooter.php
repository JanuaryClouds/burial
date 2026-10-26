<?php

namespace App\View\Components;

use App\Models\SystemSetting;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class PageFooter extends Component
{
    public function render(): View
    {
        $email = trim((string) SystemSetting::query()->value('department_email'))
            ?: trim((string) config('services.cswdo.department_email'))
            ?: 'cswdotaguigcity@gmail.com';

        return view('components.page-footer', ['email' => $email]);
    }
}
