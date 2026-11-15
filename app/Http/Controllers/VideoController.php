<?php

namespace App\Http\Controllers;

use App\Models\Video;
use Illuminate\View\View;

/**
 * Media library for the media manager. Read-only: the library is curated
 * through the seeder and videos are only watched on YouTube.
 */
class VideoController extends Controller
{
    public function index(): View
    {
        $this->authorize('viewAny', Video::class);

        // Show a fresh random selection on every reload. The full library
        // stays available; only the visible slice rotates.
        $videos = Video::query()
            ->inLibrary(Video::LIBRARY_MEDIA)
            ->inRandomOrder()
            ->limit(10)
            ->get();

        $total = Video::query()->inLibrary(Video::LIBRARY_MEDIA)->count();

        return view('videos.index', compact('videos', 'total'));
    }
}
