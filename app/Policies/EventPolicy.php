<?php

namespace App\Policies;

use App\Models\Event;
use App\Models\User;

class EventPolicy
{
    public function viewAny(User $user): bool
    {
        // Every authenticated member can browse events (published only).
        return true;
    }

    public function view(User $user, Event $event): bool
    {
        // Published events are open to every authenticated member.
        if ($event->status === 'published' || $event->isPublished()) {
            return true;
        }

        // Drafts: creator + management roles + same-team team leads only.
        return $user->id === $event->created_by
            || $user->isManager()
            || ($user->hasRole('team_lead') && $event->team === $user->team);
    }

    public function create(User $user): bool
    {
        // Defensive fallback: management roles ALWAYS have event-creation
        // rights, even if Spatie's permission cache is stale or pivot rows
        // are missing (a recurring cause of "admin can't create events").
        if ($user->isManager()) {
            return true;
        }

        if ($user->hasRole('team_lead')) {
            return $user->can('events.create');
        }

        return false;
    }

    public function update(User $user, Event $event): bool
    {
        // Global managers can always edit (defensive against stale caches).
        if ($user->isManager()) {
            return true;
        }

        if (! $user->can('events.update')) {
            return false;
        }

        // Team leads are scoped to their own team
        if ($user->hasRole('team_lead')) {
            return $event->team === $user->team;
        }

        return false;
    }

    public function delete(User $user, Event $event): bool
    {
        if ($user->isManager()) {
            return true;
        }

        if (! $user->can('events.delete')) {
            return false;
        }

        // Team leads cannot delete unless they also hold a global role
        if ($user->hasRole('team_lead') && ! $user->hasAnyRole([
            'president',
            'vice_president',
            'secretary_general',
        ])) {
            return false;
        }

        return true;
    }

    public function publish(User $user, Event $event): bool
    {
        // Managers can always publish/unpublish drafts.
        return $user->isManager() || $user->can('events.publish');
    }

    public function manageAttendance(User $user, Event $event): bool
    {
        // Global managers can manage any event's attendance
        if ($user->isManager()) {
            return true;
        }

        if (! $user->can('events.manageAttendance')) {
            return false;
        }

        // Team leads are scoped to their own team
        if ($user->hasRole('team_lead')) {
            return $event->team === $user->team;
        }

        return false;
    }
    public function rsvp(User $user, Event $event): bool
    {
        // Any authenticated member can RSVP (published events only — the
        // controller double-checks status server-side).
        return true;
    }
}
