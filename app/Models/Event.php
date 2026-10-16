<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'location',
        'starts_at',
        'ends_at',
        'capacity',
        'team',
        'filiere',
        'created_by',
        'published_at',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'starts_at' => 'datetime',
            'ends_at' => 'datetime',
            'published_at' => 'datetime',
        ];
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function attendees()
    {
        return $this->belongsToMany(User::class)
            ->withPivot('attended')
            ->withTimestamps();
    }

    public function isPublished(): bool
    {
        return $this->status === 'published'
            || ($this->published_at !== null && $this->published_at->isPast());
    }

    /**
     * The single source of truth for "who may see this event".
     * Published events are visible to EVERY authenticated member;
     * drafts stay private to their creator and the management roles.
     */
    public static function publishedScope($query)
    {
        return $query->where(function ($q) {
            $q->where('status', 'published')
              ->orWhere(function ($q2) {
                  $q2->whereNotNull('published_at')
                     ->where('published_at', '<=', now());
              });
        });
    }
}
