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

        // Managers (president/VP/SG/admin + team leads) also see drafts.
        // Plain members only ever see PUBLISHED events — enforced here, server-side.
        // NOTE: role-based check ONLY (via User::isManager()). This used to be
        // gated on the Spatie permission `events.create`, which silently denied
        // presidents whose permission pivot rows were missing/stale.
        $isGlobalManager = $user->isManager() || $user->hasRole('team_lead');

        if (! $isGlobalManager) {
            $query->where(function ($q) use ($user) {
                Event::publishedScope($q);
                if ($user->hasRole('team_lead')) {
                    $q->where('team', $user->team);
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
        // Status is normalized server-side: defaults to draft, publishes only
        // when an authorized manager explicitly chose "Publish event".
        $event = Event::create([
            ...$request->creationData(),
            'created_by' => auth()->id(),
        ]);

        return redirect()
            ->route('events.show', $event)
            ->with('status', $event->isPublished()
                ? 'Event created and published — members can now see it.'
                : 'Event saved as draft. Only managers can see it until you publish it.');
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
        $data = $request->validated();

        // Publish/draft toggle from the edit form buttons.
        $action = $request->input('submit_action');
        if ($action === 'publish') {
            $data['status'] = 'published';
            $data['published_at'] = $event->published_at ?? now();
        } elseif ($action === 'draft') {
            $data['status'] = 'draft';
            $data['published_at'] = null;
        }

        $event->update($data);

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

    public function publish(Event $event)
    {
        $this->authorize('publish', $event);

        $event->update(['published_at' => $event->published_at ?? now(), 'status' => 'published']);

        if (request()->expectsJson()) {
            return response()->json([
                'ok' => true,
                'message' => 'Event published.',
                'status' => $event->status,
            ]);
        }

        return back()->with('status', 'Event published.');
    }

    public function unpublish(Event $event)
    {
        $this->authorize('publish', $event);

        $event->update(['published_at' => null, 'status' => 'draft']);

        if (request()->expectsJson()) {
            return response()->json([
                'ok' => true,
                'message' => 'Event moved back to draft.',
                'status' => $event->status,
            ]);
        }

        return back()->with('status', 'Event moved back to draft.');
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

        // Attendance is only meaningful for events that actually happened.
        if (! $event->isPublished()) {
            return back()->withErrors(['attendance' => 'Publish the event before logging attendance.']);
        }

        if ($event->starts_at->isFuture()) {
            return back()->withErrors(['attendance' => "This event hasn't started yet."]);
        }

        $attendedIds = array_map('intval', (array) $request->input('attended', []));

        foreach ($event->attendees as $attendee) {
            $event->attendees()->updateExistingPivot($attendee->id, [
                'attended' => in_array($attendee->id, $attendedIds),
            ]);
        }

        return back()->with('status', 'Attendance saved.');
    }
}
