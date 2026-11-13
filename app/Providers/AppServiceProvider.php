<?php

namespace App\Providers;

use App\Http\Middleware\CheckUserIfActive;
use App\Models\User;
use App\Services\Auth\AccountAccess;
use App\Services\Auth\AccountSession;
use App\Services\Mail\OutgoingMailPolicy;
use Illuminate\Auth\Events\Login;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Mail\Events\MessageSending;
use Illuminate\Mail\Events\MessageSent;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;
use Livewire\Livewire;
use Spatie\Permission\Middleware\RoleMiddleware;

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
        Event::listen(Login::class, function (Login $event): void {
            if ($event->guard === 'web' && $event->user instanceof User && request()->hasSession()) {
                app(AccountSession::class)->start($event->user, request());
            }
        });
        RateLimiter::for('document-generation', fn ($request) => Limit::perMinute((int) config('security.generation_per_minute'))->by('generation:'.($request->user()?->id ?? $request->ip())));
        RateLimiter::for('document-downloads', fn ($request) => Limit::perMinute((int) config('security.downloads_per_minute'))->by('downloads:'.($request->user()?->id ?? $request->ip())));
        Event::listen(MessageSending::class, [OutgoingMailPolicy::class, 'sending']);
        Event::listen(MessageSent::class, [OutgoingMailPolicy::class, 'sent']);
        RateLimiter::for('password-reset', fn ($request) => Limit::perMinute(5)->by((string) $request->ip()));
        RateLimiter::for('mail-retry', fn ($request) => Limit::perMinute(5)->by((string) $request->user()?->id));
        Model::preventLazyLoading(! app()->isProduction());
        Livewire::addPersistentMiddleware([CheckUserIfActive::class, RoleMiddleware::class]);
        \Livewire\on('mount', fn ($component) => app(AccountAccess::class)->enforceComponent($component));
        \Livewire\on('hydrate', fn ($component) => app(AccountAccess::class)->enforceComponent($component));
    }
}
