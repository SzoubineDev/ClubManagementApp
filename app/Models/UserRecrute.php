<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserRecrute extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'users_recrute';

    /**
     * Disable updated_at — table only has created_at.
     */
    const UPDATED_AT = null;

    /**
     * Application review statuses (exact database values).
     */
    public const STATUS_PENDING = 'pending';

    public const STATUS_ACCEPTED = 'accepted';

    public const STATUS_REJECTED = 'rejected';

    public const STATUS_PASSED_TO_TEST = 'passed_to_test';

    /**
     * Every allowed status, in workflow order.
     *
     * @var array<int, string>
     */
    public const STATUSES = [
        self::STATUS_PENDING,
        self::STATUS_ACCEPTED,
        self::STATUS_REJECTED,
        self::STATUS_PASSED_TO_TEST,
    ];

    /**
     * Every debate section code an applicant can choose.
     *
     * @var array<int, string>
     */
    public const SECTIONS = ['AR', 'FR', 'EN'];

    /**
     * Human-readable labels for each debate section.
     *
     * @var array<string, string>
     */
    public const SECTION_LABELS = [
        'EN' => 'English',
        'FR' => 'French',
        'AR' => 'Arabic',
    ];

    /**
     * Maps a section lead's `team` value to the application section code.
     *
     * @var array<string, string>
     */
    public const SECTION_BY_TEAM = [
        'arabic' => 'AR',
        'english' => 'EN',
        'french' => 'FR',
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'first_name',
        'last_name',
        'phone_number',
        'email',
        'cne',
        'filiere',
        'preferred_section',
        'motivation',
        'status',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'created_at' => 'datetime',
    ];

    /**
     * Get the candidate's full name.
     */
    public function getFullNameAttribute(): string
    {
        return trim($this->first_name.' '.$this->last_name);
    }

    /**
     * Human-readable label for the current status.
     */
    public function statusLabel(): string
    {
        return match ($this->status) {
            self::STATUS_ACCEPTED => 'Accepted',
            self::STATUS_REJECTED => 'Rejected',
            self::STATUS_PASSED_TO_TEST => 'Passed to test',
            default => 'Pending',
        };
    }

    /**
     * Design-system class used to colour the status badge.
     */
    public function statusBadgeClass(): string
    {
        return match ($this->status) {
            self::STATUS_ACCEPTED => 'status-badge--accepted',
            self::STATUS_REJECTED => 'status-badge--rejected',
            self::STATUS_PASSED_TO_TEST => 'status-badge--passed',
            default => 'status-badge--pending',
        };
    }

    /**
     * Human-readable label for the preferred debate section.
     */
    public function sectionLabel(): string
    {
        return self::SECTION_LABELS[$this->preferred_section] ?? ($this->preferred_section ?: '—');
    }
}
