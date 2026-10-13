<?php

namespace App\Providers;

use App\Models\User;
use App\Policies\MemberPolicy;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        Gate::policy(User::class, MemberPolicy::class);

        // Allow {member} route binding to find soft-deleted users too
        Route::bind('member', function ($value) {
            return User::withTrashed()->findOrFail($value);
        });

        /*
        |--------------------------------------------------------------------------
        | Layout rendering fix (root cause of unstyled auth pages)
        |--------------------------------------------------------------------------
        | The auth controllers return bare views (view('auth.login'), etc.) with
        | no layout wrapper, so the rendered HTML had no <head> and no @vite()
        | asset links — app.css never loaded on /login, /register, etc.
        |
        | Every guest view (auth/*, suspended) now starts with
        | <x-guest-layout>, and every authenticated view uses <x-app-layout>.
        | Those tags resolve through Breeze's App\View\Components\GuestLayout /
        | AppLayout classes to layouts/guest.blade.php and layouts/app.blade.php,
        | which are now SELF-CONTAINED HTML documents: each one includes its own
        | <head> with @vite(['resources/css/app.css', 'resources/js/app.js']).
        | The fragile nested <x-base-layout> alias was removed from them, so the
        | stylesheet link can no longer be dropped by failed component
        | resolution. base.blade.php remains available as a plain document shell.
        */
        // Anonymous layout aliases — <x-base-layout> resolves to layouts/base
        Blade::component('layouts.base', 'base-layout');

        // Class-based Breeze components registered explicitly so they can never
        // collide with the anonymous layout aliases above. This guarantees:
        //   <x-guest-layout> -> App\View\Components\GuestLayout -> layouts/guest
        //   <x-app-layout>   -> App\View\Components\AppLayout   -> layouts/app
        Blade::component('App\View\Components\GuestLayout', 'guest-layout');
        Blade::component('App\View\Components\AppLayout', 'app-layout');
    }
}
