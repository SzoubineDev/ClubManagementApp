<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\User;
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

        // Published events the user hasn't RSVP'd to, scoped by team
        $visibleEvents = $this->visibleEventsQuery($user)
            ->whereNotIn('id', $upcomingRsvps->pluck('id'))
            ->orderBy('starts_at')
            ->limit(3)
            ->get();

        // Personal stats
        $stats = [
            'attended' => $user->events()->wherePivot('attended', true)->count(),
            'rsvps' => $user->events()->where('starts_at', '>=', now())->count(),
            'member_since' => $user->created_at,
        ];

        // President-only: count of pending things (placeholder for later)
        $pending = 0;

        return view('dashboard', compact(
            'user',
            'upcomingRsvps',
            'visibleEvents',
            'stats',
            'pending',
        ));
    }

    /**
     * Events this user is allowed to see — matches the logic in EventController::index
     */
    private function visibleEventsQuery(User $user)
    {
        $query = Event::query()
            ->whereNotNull('published_at')
            ->where('published_at', '<=', now())
            ->with('creator');

        if (
            $user->hasRole('team_lead') &&
            ! $user->hasAnyRole(['president', 'vice_president', 'secretary_general'])
        ) {
            $query->where(function ($q) use ($user) {
                $q->where('team', $user->team)
                    ->orWhereNull('team');
            });
        }

        return $query;
    }
}
