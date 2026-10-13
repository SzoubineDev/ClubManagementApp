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
        | Layout & shared component aliases
        |--------------------------------------------------------------------------
        | Registered explicitly so <x-app-layout>, <x-guest-layout> and
        | <x-base-layout> always resolve to the real layouts in
        | resources/views/layouts — guaranteeing every page (including the
        | auth pages) loads the custom design system stylesheet via @vite().
        */
        Blade::component('layouts.app', 'app-layout');
        Blade::component('layouts.guest', 'guest-layout');
        Blade::component('layouts.base', 'base-layout');
    }
}
