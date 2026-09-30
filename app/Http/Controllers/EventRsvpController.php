<?php

namespace App\Http\Controllers;

use App\Models\Event;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;

class EventRsvpController extends Controller
{
    public function store(Event $event): RedirectResponse
    {
        $this->authorize('rsvp', $event);

        $user = Auth::user();

        if ($event->attendees()->where('user_id', $user->id)->exists()) {
            return back()->with('status', 'You are already registered.');
        }

        if ($event->capacity && $event->attendees()->count() >= $event->capacity) {
            return back()->withErrors(['rsvp' => 'This event is full.']);
        }

        $event->attendees()->attach($user->id);

        return back()->with('status', 'You are registered for this event.');
    }

    public function destroy(Event $event): RedirectResponse
    {
        $this->authorize('rsvp', $event);

        $event->attendees()->detach(Auth::id());

        return back()->with('status', 'RSVP cancelled.');
    }
}
