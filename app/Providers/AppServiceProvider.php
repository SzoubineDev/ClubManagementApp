<?php

namespace App\Providers;

use App\Models\User;
use App\Policies\MemberPolicy;
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
    }
}
