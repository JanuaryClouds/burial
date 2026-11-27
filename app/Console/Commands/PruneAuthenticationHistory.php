<?php

namespace App\Console\Commands;

use App\Services\Auth\AuthenticationLog;
use Illuminate\Console\Command;
use Spatie\SuspiciousLogin\Models\LoginAttempt;

class PruneAuthenticationHistory extends Command
{
    protected $signature = 'auth-monitor:prune {--dry-run : Count records without deleting them}';

    protected $description = 'Prune authentication activities and credential attempts using the monitoring retention period';

    public function handle(): int
    {
        $days = (int) config('suspicious-login.prune_after_days', 365);
        if ($days < 1) {
            $this->error('Authentication retention must be at least one day.');

            return self::FAILURE;
        }
        $cutoff = now()->subDays($days);
        foreach ([AuthenticationLog::query(true), LoginAttempt::query()] as $query) {
            $query->where('created_at', '<=', $cutoff);
            $this->line($query->getModel()->getTable().': '.$query->count());
            if (! $this->option('dry-run')) {
                do {
                    $ids = (clone $query)->orderBy('id')->limit(500)->pluck('id');
                    $query->getModel()->newQuery()->whereKey($ids)->delete();
                } while ($ids->count() === 500);
            }
        }

        return self::SUCCESS;
    }
}
