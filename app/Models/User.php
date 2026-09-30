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
        return $this->hasRole('team_lead') && $this->team === $team;
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

    public function isPresident(): bool
    {
        return $this->hasRole('president');
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
