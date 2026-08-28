<?php

namespace App\Traits;

use Illuminate\Support\Carbon;

trait HasWorkHours
{
    protected function reschedule(Carbon $dateTime): Carbon
    {
        return Carbon::parse($dateTime)->addDay()->setTime(config('services.cswdo.work_hours.start'), 0, 0)->addMinutes(rand(0, 10));
    }
}
