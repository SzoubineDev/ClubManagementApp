<?php

use App\Http\Controllers\EventController;
use App\Http\Controllers\EventRsvpController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PasswordRecoveryController;
use App\Http\Controllers\MemberController;
/*
|--------------------------------------------------------------------------
| Public routes
|--------------------------------------------------------------------------
*/

Route::middleware('auth')->get('/suspended', function () {
    return view('suspended');
})->name('suspended');
// Landing page — logged-in users get redirected straight to the app
Route::get('/', function () {
    if (auth()->check()) {
        return redirect()->route('events.index');
    }
    return view('landing');
})->name('landing');

/*
|--------------------------------------------------------------------------
| Authenticated routes
|--------------------------------------------------------------------------
*/

Route::middleware('auth')->group(function () {

    // Dashboard
    Route::get('/dashboard', [\App\Http\Controllers\DashboardController::class, 'index'])
        ->middleware(['auth', 'verified'])
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

    Route::resource('events', EventController::class);
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
Route::middleware('guest')->group(function () {
    Route::get('password/recover', [PasswordRecoveryController::class, 'show'])
        ->name('password.recover');
    Route::post('password/recover', [PasswordRecoveryController::class, 'verify'])
        ->middleware('throttle:5,1')
        ->name('password.recover.verify');
});
require __DIR__ . '/auth.php';
