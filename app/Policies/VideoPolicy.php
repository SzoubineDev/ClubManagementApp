<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Video;

/**
 * Video-library authorization.
 *
 * - The `media` library is visible to the media manager.
 * - The `design` library is visible to the design manager.
 *
 * Both libraries are read-only, so only view abilities are defined.
 */
class VideoPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasRole('media_manager');
    }

    public function viewDesignAny(User $user): bool
    {
        return $user->hasRole('design_manager');
    }
}
