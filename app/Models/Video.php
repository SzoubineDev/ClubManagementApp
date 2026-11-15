<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Video extends Model
{
    use HasFactory;

    public const LIBRARY_MEDIA = 'media';

    public const LIBRARY_DESIGN = 'design';

    protected $fillable = [
        'title',
        'description',
        'youtube_url',
        'thumbnail_url',
        'sort_order',
        'library',
    ];

    protected $casts = [
        'sort_order' => 'integer',
    ];

    public function scopeInLibrary($query, string $library)
    {
        return $query->where('library', $library);
    }

    /**
     * Extract the 11-character YouTube video id from any common URL form.
     */
    public function youtubeId(): ?string
    {
        if (preg_match(
            '~(?:youtu\.be/|youtube\.com/(?:watch\?[^#]*v=|embed/|shorts/|live/))([A-Za-z0-9_-]{11})~i',
            $this->youtube_url,
            $matches
        )) {
            return $matches[1];
        }

        return null;
    }

    /**
     * Card thumbnail: an explicitly provided image wins, otherwise we use
     * YouTube's own poster frame for the video.
     */
    public function thumbnail(): ?string
    {
        if ($this->thumbnail_url) {
            return $this->thumbnail_url;
        }

        $id = $this->youtubeId();

        return $id ? "https://img.youtube.com/vi/{$id}/hqdefault.jpg" : null;
    }
}
