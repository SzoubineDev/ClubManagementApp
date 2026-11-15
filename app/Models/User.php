<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasRoles, SoftDeletes;

    protected $fillable = [
        'name',
        'email',
        'password',
        'team',
        'cne',
        'birthday',
        'filiere',
        'is_suspended',
        'suspended_at',
        'suspension_reason',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'birthday' => 'date',
            'is_suspended' => 'boolean',
            'suspended_at' => 'datetime',
        ];
    }

    public function isTeamLeadOf(string $team): bool
    {
        return $this->hasAnyRole(['team_lead', 'section_chef']) && $this->team === $team;
    }

    /**
     * Is this user the chef (lead) of a debate section?
     *
     * `section_chef` is the production role; `team_lead` is kept as a
     * backwards-compatible alias for older data.
     */
    public function isSectionChef(): bool
    {
        return $this->hasAnyRole(['section_chef', 'team_lead']);
    }

    /**
     * The section this user leads, as a lowercase team name.
     */
    public function sectionTeam(): ?string
    {
        if (! $this->isSectionChef()) {
            return null;
        }

        return in_array($this->team, ['arabic', 'english', 'french'], true)
            ? $this->team
            : null;
    }

    /**
     * Human-readable section name for the chef's own section.
     */
    public function sectionName(): ?string
    {
        return match ($this->sectionTeam()) {
            'arabic' => 'Arabic',
            'english' => 'English',
            'french' => 'French',
            default => null,
        };
    }

    /**
     * The recruitment section code (AR / EN / FR) this user leads, if any.
     *
     * Section chefs store their section as a lowercase team name
     * (arabic / english / french).
     */
    public function recruitmentSection(): ?string
    {
        return UserRecrute::SECTION_BY_TEAM[$this->sectionTeam()] ?? null;
    }

    public function events()
    {
        return $this->belongsToMany(Event::class)
            ->withPivot('attended')
            ->withTimestamps();
    }

    public function isRegisteredFor(Event $event): bool
    {
        return $this->events()->where('event_id', $event->id)->exists();
    }

    /**
     * Daily section-presence records (separate from event attendance).
     */
    public function attendanceRecords()
    {
        return $this->hasMany(AttendanceRecord::class);
    }

    public function isPresident(): bool
    {
        return $this->hasRole('president');
    }

    /**
     * Management roles in this club's Spatie setup. There is no literal
     * "admin" role in production — the president / vice-president are the
     * admins. Used as a defensive fallback so permission checks never fail
     * because of stale caches or missing pivot rows.
     */
    public function isManager(): bool
    {
        return $this->hasAnyRole([
            'president',
            'vice_president',
            'secretary_general',
            'admin',
        ]);
    }

    public function isSuspended(): bool
    {
        return (bool) $this->is_suspended;
    }

    public function suspend(string $reason = null): void
    {
        $this->update([
            'is_suspended' => true,
            'suspended_at' => now(),
            'suspension_reason' => $reason,
        ]);
    }

    public function unsuspend(): void
    {
        $this->update([
            'is_suspended' => false,
            'suspended_at' => null,
            'suspension_reason' => null,
        ]);
    }

    // Role change requests this user has submitted
    public function roleChangeRequestsMade()
    {
        return $this->hasMany(RoleChangeRequest::class, 'requested_by');
    }

    // Role change requests targeting this user
    public function roleChangeRequestsReceived()
    {
        return $this->hasMany(RoleChangeRequest::class, 'target_user_id');
    }
}
