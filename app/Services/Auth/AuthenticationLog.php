<?php

namespace App\Services\Auth;

use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Spatie\Activitylog\Models\Activity;

class AuthenticationLog
{
    public const NAMES = ['authentication', 'account-management', 'superadmin-security', 'permissions'];

    public const DESCRIPTIONS = ['Successfully created role', 'Successfully updated role', 'Successfully deleted role',
        'Successfully created new role', 'Created a new role', 'Updated the role', 'Deleted the role', 'Failed to save new role', 'Failed to create role', 'Failed to update role', 'Failed to delete role', 'Unsuccessful login attempt', 'Inactive account login attempt'];

    public static function isSecurity(Activity $activity): bool
    {
        return in_array($activity->log_name, self::NAMES, true) || in_array($activity->description, self::DESCRIPTIONS, true);
    }

    /** @return Builder<Activity> */
    public static function query(bool $security): Builder
    {
        $query = Activity::query();
        $securityQuery = function (Builder $q): void {
            $q->whereNotNull('properties->security')->orWhere(function (Builder $legacy): void {
                $legacy->where(fn (Builder $names) => $names->whereIn('log_name', self::NAMES)->orWhereIn('description', self::DESCRIPTIONS))
                    ->where(function (Builder $actor): void {
                        $actor->whereNull('causer_id')->orWhereNull('causer_type')->orWhereDoesntHaveMorph('causer', [User::class])
                            ->orWhereHasMorph('causer', [User::class], fn (Builder $user) => $user->whereNull('citizen_uuid'));
                    })
                    ->where(function (Builder $subject): void {
                        $subject->whereNull('subject_type')->orWhereNull('subject_id')
                            ->orWhereDoesntHaveMorph('subject', [User::class])
                            ->orWhereHasMorph('subject', [User::class], fn (Builder $user) => $user->whereNull('citizen_uuid'));
                    });
            });
        };

        return $security ? $query->where($securityQuery) : $query->whereNot($securityQuery);
    }
}
