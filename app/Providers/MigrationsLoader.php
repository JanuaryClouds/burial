<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class MigrationsLoader extends ServiceProvider
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
        $systemMigrations = database_path('migrations/system');
        $referenceMigrations = database_path('migrations/reference');
        $dataMigrations = database_path('migrations/data');
        $workflowMigrations = database_path('migrations/workflow');
        $customFieldsMigrations = database_path('migrations/custom_fields');

        $this->loadMigrationsFrom([
            $systemMigrations,
            $referenceMigrations,
            $dataMigrations,
            $workflowMigrations,
            $customFieldsMigrations,
        ]);
    }
}
