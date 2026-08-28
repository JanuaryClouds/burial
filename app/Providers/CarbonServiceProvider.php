<?php

namespace App\Providers;

use Illuminate\Support\Carbon;
use Illuminate\Support\ServiceProvider;

class CarbonServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        Carbon::macro('startingHour', function () {
            return $this->copy()->setTime(config('services.cswdo.work_hours.start'), 0, 0);
        });

        Carbon::macro('endingHour', function () {
            return $this->copy()->setTime(config('services.cswdo.work_hours.end'), 0, 0);
        });

        Carbon::macro('isOutsideWorkHours', function (): bool {
            if ($this->isSunday()) {
                return true;
            }

            return $this->lte(Carbon::startingHour()) || $this->gte(Carbon::endingHour());
        });

        Carbon::macro('generateRandomDateTime', function (Carbon $from, Carbon $to) {
            do {
                $randomDateTime = Carbon::createFromTimestamp(
                    random_int($from->timestamp, $to->timestamp),
                    $from->timezone
                );

                if ($randomDateTime->greaterThan(now())) {
                    break;
                }
            } while ($randomDateTime->isOutsideWorkHours());

            return $randomDateTime;
        });
    }
}
