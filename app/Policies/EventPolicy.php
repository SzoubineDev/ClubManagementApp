<?php

namespace App\Policies;

use App\Models\Event;
use App\Models\User;

class EventPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('events.viewAny');
    }

    public function view(User $user, Event $event): bool
    {
        if (! $user->can('events.view')) {
            return false;
        }

        // Unpublished events are only visible to creators and higher roles
        if (! $event->isPublished()) {
            return $user->id === $event->created_by
                || $user->hasAnyRole([
                    'president',
                    'vice_president',
                    'secretary_general',
                    'team_lead',
                ]);
        }

        return true;
    }

    public function create(User $user): bool
    {
        return $user->can('events.create');
    }

    public function update(User $user, Event $event): bool
    {
        if (! $user->can('events.update')) {
            return false;
        }

        // Global roles can update anything
        if ($user->hasAnyRole([
            'president',
            'vice_president',
            'secretary_general',
        ])) {
            return true;
        }

        // Team leads are scoped to their own team
        if ($user->hasRole('team_lead')) {
            return $event->team === $user->team;
        }

        return false;
    }

    public function delete(User $user, Event $event): bool
    {
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
        return $user->can('events.publish');
    }

    public function manageAttendance(User $user, Event $event): bool
    {
        if (! $user->can('events.manageAttendance')) {
            return false;
        }

        // Global roles can manage any event's attendance
        if ($user->hasAnyRole([
            'president',
            'vice_president',
            'secretary_general',
        ])) {
            return true;
        }

        // Team leads are scoped to their own team
        if ($user->hasRole('team_lead')) {
            return $event->team === $user->team;
        }

        return false;
    }
    public function rsvp(User $user, Event $event): bool
    {
        return $user->can('events.rsvp');
    }
}
