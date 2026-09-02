<?php

namespace App\Traits;

use Illuminate\Support\Carbon;

trait HasWorkHours
{
    protected function reschedule(Carbon $dateTime): Carbon
    {
        return Carbon::parse($dateTime)->addDay()->setTime(config('services.cswdo.work_hours.start'), 0, 0)->addMinutes(rand(0, 10));
    }

    /**
     * Generate a date_in and date_out pair that both fall within office hours.
     * date_out is clamped to the ending hour if the random duration exceeds it.
     */
    protected function generateWorkHoursDateTimePair(
        Carbon $base,
        int $minInMinutes = 5,
        int $maxInMinutes = 20,
        int $minOutMinutes = 5,
        int $maxOutMinutes = 20,
    ): array {
        $endHour = config('services.cswdo.work_hours.end');

        $dateIn = Carbon::generateRandomDateTime(
            $base,
            Carbon::parse($base)->addMinutes(rand($minInMinutes, $maxInMinutes))
        );

        $proposedDateOut = Carbon::parse($dateIn)->addMinutes(rand($minOutMinutes, $maxOutMinutes));

        if ($proposedDateOut->hour >= $endHour) {
            $dateOut = Carbon::parse($dateIn)->setTime($endHour, 0, 0);
        } else {
            $dateOut = $proposedDateOut;
        }

        return [$dateIn, $dateOut];
    }
}
