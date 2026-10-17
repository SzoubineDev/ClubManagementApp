<?php

namespace App\Policies;

use App\Models\User;

class MemberPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasAnyRole(['president', 'vice_president', 'rh', 'admin']);
    }

    public function view(User $user, User $member): bool
    {
        if ($user->id === $member->id) {
            return true;
        }

        return $user->hasAnyRole(['president', 'vice_president', 'rh', 'admin']);
    }

    /**
     * Can open the edit page at all?
     */
    public function manage(User $user, User $member): bool
    {
        if ($user->id === $member->id) {
            return false;
        }

        if ($member->isPresident()) {
            return false; // Nobody edits a president
        }

        return $user->hasAnyRole(['president', 'vice_president', 'admin']);
    }

    public function updateTeam(User $user, User $member): bool
    {
        if ($user->id === $member->id) {
            return false;
        }

        if ($member->isPresident()) {
            return false;
        }

        return $user->hasAnyRole(['president', 'vice_president', 'admin']);
    }

    public function updateRoles(User $user, User $member): bool
    {
        if (! $user->hasAnyRole(['president', 'admin'])) {
            return false;
        }

        if ($user->id === $member->id) {
            return false;
        }

        if ($member->isPresident()) {
            return false;
        }

        return true;
    }

    public function proposeRole(User $user, User $member): bool
    {
        if ($user->id === $member->id) {
            return false;
        }

        if ($member->isPresident()) {
            return false;
        }

        return $user->hasAnyRole(['president', 'rh', 'admin']);
    }

    public function approveRoleRequest(User $user): bool
    {
        return $user->isPresident();
    }

    public function suspend(User $user, User $member): bool
    {
        if (! $user->hasAnyRole(['president', 'admin'])) {
            return false;
        }

        // Admins cannot suspend presidents or vice presidents.
        if ($user->hasRole('admin') && ! $user->isPresident()
            && $member->hasAnyRole(['president', 'vice_president'])) {
            return false;
        }

        return $user->id !== $member->id && ! $member->isPresident();
    }

    public function unsuspend(User $user, User $member): bool
    {
        return $user->hasAnyRole(['president', 'admin']);
    }

    public function delete(User $user, User $member): bool
    {
        if (! $user->hasAnyRole(['president', 'admin'])) {
            return false;
        }

        // Admins cannot delete higher-ranked managers.
        if ($user->hasRole('admin') && ! $user->isPresident()
            && $member->hasAnyRole(['president', 'vice_president'])) {
            return false;
        }

        return $user->id !== $member->id && ! $member->isPresident();
    }

    public function restore(User $user, User $member): bool
    {
        return $user->hasAnyRole(['president', 'admin']);
    }
}
