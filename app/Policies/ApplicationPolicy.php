<?php

namespace App\Policies;

use App\Models\User;
use App\Models\UserRecrute;

/**
 * Authorization for recruitment applications (`users_recrute`).
 *
 * Applications hold personal data (phone, CNE, motivation), so access is
 * limited to the club's management roles and the lead of the applicant's own
 * section. Every backend action is authorised independently — hiding the
 * navigation link is never enough.
 */
class ApplicationPolicy
{
    /**
     * The management roles allowed to review every recruitment application.
     *
     * @var array<int, string>
     */
    private const MANAGER_ROLES = [
        'president',
        'vice_president',
        'secretary_general',
        'admin',
    ];

    public function viewAny(User $user): bool
    {
        return $user->hasAnyRole(self::MANAGER_ROLES)
            || $user->recruitmentSection() !== null
            || $user->can('applications.viewAny');
    }

    public function view(User $user, UserRecrute $application): bool
    {
        return $user->hasAnyRole(self::MANAGER_ROLES)
            || $user->can('applications.view')
            || $this->leadsApplicationSection($user, $application);
    }

    public function updateStatus(User $user, UserRecrute $application): bool
    {
        return $user->hasAnyRole(self::MANAGER_ROLES) || $user->can('applications.update');
    }

    public function delete(User $user, UserRecrute $application): bool
    {
        return $user->hasAnyRole(self::MANAGER_ROLES) || $user->can('applications.delete');
    }

    /**
     * Does this section lead own the applicant's section?
     */
    private function leadsApplicationSection(User $user, UserRecrute $application): bool
    {
        $section = $user->recruitmentSection();

        return $section !== null && $application->preferred_section === $section;
    }
}
