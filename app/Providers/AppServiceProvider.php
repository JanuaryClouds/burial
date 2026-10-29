<?php

namespace App\Providers;

use App\Http\Middleware\CheckUserIfActive;
use App\Services\Auth\AccountAccess;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\ServiceProvider;
use Livewire\Livewire;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Model::preventLazyLoading(! app()->isProduction());
        Livewire::addPersistentMiddleware([CheckUserIfActive::class]);
        \Livewire\on('mount', fn ($component) => app(AccountAccess::class)->enforceComponent($component));
        \Livewire\on('hydrate', fn ($component) => app(AccountAccess::class)->enforceComponent($component));
    }
}
