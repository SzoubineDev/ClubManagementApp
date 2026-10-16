<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $user = auth()->user();

        // Events the user has RSVP'd to, still upcoming
        $upcomingRsvps = $user->events()
            ->where('starts_at', '>=', now())
            ->orderBy('starts_at')
            ->limit(3)
            ->get();

        // Published events the user hasn't RSVP'd to, scoped by team/filière.
        // NOTE: these are passed to the view as $upcomingEvents — the single
        // list every role sees on the dashboard. Admins additionally see
        // their own drafts (clearly badged) so they can finish them.
        $visibleEvents = $this->visibleEventsQuery($user)
            ->whereNotIn('id', $upcomingRsvps->pluck('id'))
            ->orderBy('starts_at')
            ->limit(5)
            ->get();

        if ($user->can('create', Event::class)) {
            $drafts = Event::query()
                ->where(function ($q) use ($user) {
                    $q->where('status', 'draft')->orWhereNull('published_at');
                })
                ->where('created_by', $user->id)
                ->orderBy('starts_at')
                ->limit(2)
                ->get();
            $visibleEvents = $visibleEvents->concat($drafts)->unique('id')->take(5);
        }

        // Personal stats
        $stats = [
            'attended' => $user->events()->wherePivot('attended', true)->count(),
            'rsvps' => $user->events()->where('starts_at', '>=', now())->count(),
            'member_since' => $user->created_at,
        ];

        // ---- Admin-only aggregates (server-side gated by the same policy
        // the members console uses — never trust the Blade layer alone) ----
        $isAdmin = $user->can('viewAny', User::class);

        $adminStats = null;
        $recentMembers = collect();

        if ($isAdmin) {
            $adminStats = [
                'total_members' => User::count(),
                'active_members' => User::where('is_suspended', false)->count(),
                'suspended_members' => User::where('is_suspended', true)->count(),
                'upcoming_events' => Event::where('starts_at', '>=', now())
                    ->where(function ($q) {
                        $q->whereNull('published_at')->orWhere('published_at', '<=', now());
                    })->count(),
                'published_events' => Event::whereNotNull('published_at')
                    ->where('published_at', '<=', now())->count(),
                'total_participation' => DB::table('event_user')->count(),
                'draft_events' => Event::whereNull('published_at')->count(),
            ];

            $recentMembers = User::with('roles')
                ->latest()
                ->limit(5)
                ->get();
        }

        return view('dashboard', compact(
            'user',
            'upcomingRsvps',
            'visibleEvents',
            'stats',
            'isAdmin',
            'adminStats',
            'recentMembers',
        ) + [
            // The single "Upcoming Events" list rendered by the view.
            'upcomingEvents' => $visibleEvents->merge($upcomingRsvps)->unique('id')->sortBy('starts_at')->values(),
            'upcomingCount' => $visibleEvents->count() + $upcomingRsvps->count(),
            'recentAttendanceCount' => $stats['attended'],
        ]);
    }

    /**
     * Events this user is allowed to see — matches the logic in EventController::index:
     * published events are visible to EVERY authenticated member (admins included).
     */
    private function visibleEventsQuery(User $user)
    {
        $query = Event::with('creator');

        Event::publishedScope($query);

        if (
            $user->hasRole('team_lead') &&
            ! $user->hasAnyRole(['president', 'vice_president', 'secretary_general'])
        ) {
            $query->where(function ($q) use ($user) {
                $q->where('team', $user->team)
                    ->orWhereNull('team');
            });
        }

        // A member only sees filière-restricted events that match their own.
        $query->where(function ($q) use ($user) {
            $q->whereNull('filiere')
              ->orWhere('filiere', $user->filiere);
        });

        return $query;
    }
}
