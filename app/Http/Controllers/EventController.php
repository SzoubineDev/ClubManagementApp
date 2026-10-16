<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreEventRequest;
use App\Http\Requests\UpdateEventRequest;
use App\Models\Event;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Http\Request;

class EventController extends Controller
{

    public function index(): View
    {
        $this->authorize('viewAny', Event::class);

        $user = auth()->user();

        $query = Event::with('creator')->withCount('attendees')->latest('starts_at');

        // Global roles (admins/president/VP/SG) see every event they create or manage.
        $isGlobalManager = $user->can('events.create') && $user->hasAnyRole([
            'president', 'vice_president', 'secretary_general', 'admin',
        ]);

        // Everyone else (plain members + team leads): only PUBLISHED upcoming
        // events — so an admin's newly created event appears here the moment it
        // is published, and drafts/unpublished events stay invisible to them.
        if (! $isGlobalManager) {
            $query->where(function ($q) use ($user) {
                $q->where(function ($q1) {
                    $q1->whereNotNull('published_at')
                       ->where('published_at', '<=', now());
                });
                if ($user->team) {
                    $q->orWhere(function ($q2) use ($user) {
                        $q2->where('team', $user->team);
                    });
                }
            });
        }

        $events = $query->paginate(15);

        return view('events.index', compact('events'));
    }

    public function create(): View
    {
        $this->authorize('create', Event::class);

        return view('events.create');
    }

    public function store(StoreEventRequest $request): RedirectResponse
    {
        $event = Event::create([
            ...$request->validated(),
            'created_by' => auth()->id(),
            // Publish immediately so the event appears in members' "Upcoming events"
            // (landing page + app) as soon as the admin creates it.
            'published_at' => now(),
        ]);

        return redirect()
            ->route('events.show', $event)
            ->with('status', 'Event created and published.');
    }

    public function show(Event $event): View
    {
        $this->authorize('view', $event);

        $event->load('creator', 'attendees');

        return view('events.show', compact('event'));
    }

    public function edit(Event $event): View
    {
        $this->authorize('update', $event);

        return view('events.edit', compact('event'));
    }

    public function update(UpdateEventRequest $request, Event $event): RedirectResponse
    {
        $event->update($request->validated());

        return redirect()
            ->route('events.show', $event)
            ->with('status', 'Event updated.');
    }

    public function destroy(Event $event): RedirectResponse
    {
        $this->authorize('delete', $event);

        $event->delete();

        return redirect()
            ->route('events.index')
            ->with('status', 'Event deleted.');
    }

    public function publish(Event $event): RedirectResponse
    {
        $this->authorize('publish', $event);

        $event->update(['published_at' => now()]);

        return back()->with('status', 'Event published.');
    }
    public function attendance(Event $event): View
    {
        $this->authorize('manageAttendance', $event);

        $event->load('attendees');

        return view('events.attendance', compact('event'));
    }

    public function updateAttendance(Request $request, Event $event): RedirectResponse
    {
        $this->authorize('manageAttendance', $event);

        $attendedIds = $request->input('attended', []);

        foreach ($event->attendees as $attendee) {
            $event->attendees()->updateExistingPivot($attendee->id, [
                'attended' => in_array($attendee->id, $attendedIds),
            ]);
        }

        return back()->with('status', 'Attendance saved.');
    }
}
