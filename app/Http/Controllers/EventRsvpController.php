<?php

namespace App\Http\Controllers;

use App\Models\Event;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EventRsvpController extends Controller
{
    /**
     * Shared handler: works both as a normal form POST/DELETE (redirect back)
     * and as an AJAX request from the event page (JSON response).
     */
    private function respond(Request $request, ?string $message, ?array $errors = null)
    {
        if ($request->expectsJson()) {
            return $errors
                ? response()->json(['ok' => false, 'message' => $errors['rsvp']], 422)
                : response()->json(['ok' => true, 'message' => $message]);
        }

        return $errors
            ? back()->withInput()->withErrors($errors)
            : back()->with('status', $message);
    }

    public function store(Request $request, Event $event)
    {
        $this->authorize('rsvp', $event);

        // Draft events cannot be joined — only published ones are open.
        if (! $event->isPublished()) {
            abort(403, 'This event is not open for registration yet.');
        }

        $user = Auth::user();

        // Filière-restricted events accept matching students only.
        if ($event->filiere && $user->filiere !== $event->filiere) {
            return $this->respond($request, null, [
                'rsvp' => "This event is reserved for {$event->filiere} students.",
            ]);
        }

        if ($event->attendees()->where('user_id', $user->id)->exists()) {
            return $this->respond($request, 'You are already registered.');
        }

        if ($event->capacity && $event->attendees()->count() >= $event->capacity) {
            return $this->respond($request, null, ['rsvp' => 'This event is full.']);
        }

        $event->attendees()->attach($user->id);

        return $this->respond($request, 'You are registered for this event.');
    }

    public function destroy(Request $request, Event $event)
    {
        $this->authorize('rsvp', $event);

        // Only cancel an RSVP that actually exists — never detach blindly.
        $registered = $event->attendees()->where('user_id', Auth::id())->exists();

        if (! $registered) {
            return $this->respond($request, 'You are not registered for this event.');
        }

        $event->attendees()->detach(Auth::id());

        return $this->respond($request, 'RSVP cancelled.');
    }
}
