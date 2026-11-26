<?php

use App\Http\Controllers\AlphabetController;
use App\Http\Controllers\ApplicationController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DesignVideoController;
use App\Http\Controllers\DocumentController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\EventRsvpController;
use App\Http\Controllers\MemberController;
use App\Http\Controllers\MyPlaceController;
use App\Http\Controllers\PasswordRecoveryController;
use App\Http\Controllers\PresentationController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RecruitmentController;
use App\Http\Controllers\SectionAttendanceController;
use App\Http\Controllers\SpinTheWheelController;
use App\Http\Controllers\VideoController;
use App\Http\Controllers\WordAssociationController;
use App\Http\Middleware\EnsureUserIsActive;
use App\Http\Middleware\RedirectIfSuspended;
use App\Models\Event;
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

// Suspension notice — viewable by guests or as a fallback
Route::get('/account-suspended', function () {
    if (auth()->check()) {
        return redirect()->route('suspended');
    }

    return view('suspended', [
        'user' => null,
        'reason' => session('suspension_reason') ?? session('reason'),
        'suspended_at' => session('suspended_at'),
    ]);
})->name('suspended.guest');

// Suspended users land here with full context
Route::middleware('auth')->get('/suspended', function () {
    $user = auth()->user();

    if (! $user->isSuspended()) {
        return redirect()->route('dashboard');
    }

    return view('suspended', [
        'user' => $user,
        'reason' => $user->suspension_reason,
        'suspended_at' => $user->suspended_at,
    ]);
})->name('suspended');

// Landing page — logged-in users get redirected straight to the app
Route::get('/', function () {
    if (auth()->check()) {
        return redirect()->route('events.index');
    }

    // Published upcoming events shown on the landing "What's next?" section.
    $upcomingEvents = Event::query()
        ->where(function ($q) {
            Event::publishedScope($q);
        })
        ->where('starts_at', '>=', now())
        ->orderBy('starts_at')
        ->take(3)
        ->get();

    return view('landing', compact('upcomingEvents'));
})->name('landing');

// Integration day — standalone full-screen presentation deck
Route::get('/presentation', [PresentationController::class, 'index'])->name('presentation');

// Alphabet reference page — standalone, themed like the presentation
Route::get('/alphabets', [AlphabetController::class, 'index'])->name('alphabets');

// Recruitment — Call for new members (closed for the season)
Route::get('/recrute', [RecruitmentController::class, 'create'])->name('recrute');
Route::post('/recrute', [RecruitmentController::class, 'store'])->name('recrute.store');

// Late recruitment — applications submitted after the season closed
Route::get('/late_recrute', [RecruitmentController::class, 'createLate'])->name('late_recrute');
Route::post('/late_recrute', [RecruitmentController::class, 'storeLate'])->name('late_recrute.store');

/*
|--------------------------------------------------------------------------
| Identity-verified password recovery (no email sending)
|--------------------------------------------------------------------------
| Step 1: GET/POST /password/recover   -> verify email + birthday + CNE
| Step 2: GET /password/reset          -> new-password form (session grant)
| Step 3: POST /password/reset         -> apply new password, burn grant
*/
Route::middleware(['guest', RedirectIfSuspended::class])
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

Route::middleware(['auth', EnsureUserIsActive::class])->group(function () {

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

    // Games — presenter picker (unique draws per session)
    Route::get('games/spin_the_wheel', [SpinTheWheelController::class, 'index'])
        ->name('games.spin-the-wheel');
    Route::post('games/spin_the_wheel', [SpinTheWheelController::class, 'spin'])
        ->name('games.spin-the-wheel.spin');
    Route::delete('games/spin_the_wheel', [SpinTheWheelController::class, 'reset'])
        ->name('games.spin-the-wheel.reset');

    // Games — word association (rebus figures, 60-second rounds)
    Route::get('games/word_assoc', [WordAssociationController::class, 'index'])
        ->name('games.word-assoc');
    Route::post('games/word_assoc/draw', [WordAssociationController::class, 'draw'])
        ->name('games.word-assoc.draw');
    Route::post('games/word_assoc/reveal', [WordAssociationController::class, 'reveal'])
        ->name('games.word-assoc.reveal');
    Route::delete('games/word_assoc', [WordAssociationController::class, 'reset'])
        ->name('games.word-assoc.reset');

    // Members console — admin-only surfaces (policies enforce per-action)
    Route::get('members', [MemberController::class, 'index'])->name('members.index');
    Route::get('members/{member}', [MemberController::class, 'show'])->name('members.show');
    Route::get('members/{member}/edit', [MemberController::class, 'edit'])->name('members.edit');
    Route::put('members/{member}', [MemberController::class, 'update'])->name('members.update');
    Route::post('members/{member}/suspend', [MemberController::class, 'suspend'])->name('members.suspend');
    Route::post('members/{member}/unsuspend', [MemberController::class, 'unsuspend'])->name('members.unsuspend');
    Route::post('members/{member}/promote', [MemberController::class, 'promote'])->name('members.promote');
    Route::delete('members/{member}', [MemberController::class, 'destroy'])->name('members.destroy');
    Route::post('members/{member}/restore', [MemberController::class, 'restore'])->name('members.restore');

    // Applications console — recruitment applications (policies enforce access)
    Route::get('applications', [ApplicationController::class, 'index'])->name('applications.index');
    Route::get('applications/export', [ApplicationController::class, 'export'])->name('applications.export');
    Route::patch('applications/{application}', [ApplicationController::class, 'updateStatus'])->name('applications.status');
    Route::delete('applications/{application}', [ApplicationController::class, 'destroy'])->name('applications.destroy');

    // Section chef — daily presence register for their own section
    Route::get('section/attendance', [SectionAttendanceController::class, 'index'])->name('section.attendance.index');
    Route::post('section/attendance', [SectionAttendanceController::class, 'store'])->name('section.attendance.store');

    // Media library — read-only, visible to the media manager
    Route::get('videos', [VideoController::class, 'index'])->name('videos.index');

    // My Place — the vice-president's personal curation
    Route::get('my-place', [MyPlaceController::class, 'index'])->name('my-place.index');

    // Design library — read-only video-editing tutorials for the design manager
    Route::get('design/videos', [DesignVideoController::class, 'index'])->name('design.videos.index');

    // Official documents — assistant general (policies enforce access)
    Route::get('documents', [DocumentController::class, 'index'])->name('documents.index');
    Route::get('documents/room-request', [DocumentController::class, 'downloadRoomRequest'])->name('documents.room-request');

    // Profile
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});
