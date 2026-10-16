<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\EventRsvpController;
use App\Http\Controllers\MemberController;
use App\Http\Controllers\PasswordRecoveryController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Breeze auth routes (login, register, verify-email, confirm-password…)
|--------------------------------------------------------------------------
*/
require __DIR__.'/auth.php';

/*
|--------------------------------------------------------------------------
| Public routes
|--------------------------------------------------------------------------
*/

// Suspension notice — viewable by guests (e.g. right after a blocked login)
// and by suspended users bounced out of the app by EnsureUserIsActive.
Route::middleware('guest')->get('/account-suspended', function () {
    return view('suspended');
})->name('suspended.guest');

// Suspended users who still hold a session land here instead of the app.
Route::middleware('auth')->get('/suspended', function () {
    return view('suspended');
})->name('suspended');

// Landing page — logged-in users get redirected straight to the app
Route::get('/', function () {
    if (auth()->check()) {
        return redirect()->route('events.index');
    }

    // Published upcoming events shown on the landing "What's next?" section.
    $upcomingEvents = \App\Models\Event::query()
        ->where(function ($q) { \App\Models\Event::publishedScope($q); })
        ->where('starts_at', '>=', now())
        ->orderBy('starts_at')
        ->take(3)
        ->get();

    return view('landing', compact('upcomingEvents'));
})->name('landing');

/*
|--------------------------------------------------------------------------
| Identity-verified password recovery (no email sending)
|--------------------------------------------------------------------------
| Step 1: GET/POST /password/recover   -> verify email + birthday + CNE
| Step 2: GET /password/reset          -> new-password form (session grant)
| Step 3: POST /password/reset         -> apply new password, burn grant
*/
Route::middleware(['guest', \App\Http\Middleware\RedirectIfSuspended::class])
    ->group(function () {
        Route::get('password/recover', [PasswordRecoveryController::class, 'show'])
            ->name('password.recover');

        Route::post('password/recover', [PasswordRecoveryController::class, 'verify'])
            ->middleware('throttle:6,1') // per IP: 6/min …
            ->name('password.recover.verify');

        Route::get('password/reset', [PasswordRecoveryController::class, 'showResetForm'])
            ->name('password.reset.form');

        Route::post('password/reset', [PasswordRecoveryController::class, 'reset'])
            ->middleware('throttle:6,1')
            ->name('password.reset.apply');
    });

/*
|--------------------------------------------------------------------------
| Authenticated routes
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', \App\Http\Middleware\EnsureUserIsActive::class])->group(function () {

    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])
        ->middleware(['verified'])
        ->name('dashboard');

    // Events — custom actions MUST come before the resource
    Route::post('events/{event}/rsvp', [EventRsvpController::class, 'store'])
        ->name('events.rsvp');
    Route::delete('events/{event}/rsvp', [EventRsvpController::class, 'destroy'])
        ->name('events.rsvp.cancel');

    Route::get('events/{event}/attendance', [EventController::class, 'attendance'])
        ->name('events.attendance');
    Route::patch('events/{event}/attendance', [EventController::class, 'updateAttendance'])
        ->name('events.attendance.update');

    Route::patch('events/{event}/publish', [EventController::class, 'publish'])
        ->name('events.publish');
    Route::patch('events/{event}/unpublish', [EventController::class, 'unpublish'])
        ->name('events.unpublish');

    Route::resource('events', EventController::class);

    // Members console — admin-only surfaces (policies enforce per-action)
    Route::get('members', [MemberController::class, 'index'])->name('members.index');
    Route::get('members/{member}', [MemberController::class, 'show'])->name('members.show');
    Route::get('members/{member}/edit', [MemberController::class, 'edit'])->name('members.edit');
    Route::put('members/{member}', [MemberController::class, 'update'])->name('members.update');
    Route::post('members/{member}/suspend', [MemberController::class, 'suspend'])->name('members.suspend');
    Route::post('members/{member}/unsuspend', [MemberController::class, 'unsuspend'])->name('members.unsuspend');
    Route::delete('members/{member}', [MemberController::class, 'destroy'])->name('members.destroy');
    Route::post('members/{member}/restore', [MemberController::class, 'restore'])->name('members.restore');

    // Profile
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});
