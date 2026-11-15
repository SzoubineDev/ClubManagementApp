<?php

namespace App\Http\Controllers;

use App\Models\Video;
use Illuminate\View\View;

/**
 * Design library: video-editing tutorials for the design manager. Read-only:
 * the library is curated through the seeder and videos are only watched on
 * YouTube.
 */
class DesignVideoController extends Controller
{
    public function index(): View
    {
        $this->authorize('viewDesignAny', Video::class);

        $videos = Video::query()
            ->inLibrary(Video::LIBRARY_DESIGN)
            ->inRandomOrder()
            ->limit(10)
            ->get();

        $total = Video::query()->inLibrary(Video::LIBRARY_DESIGN)->count();

        return view('design-videos.index', compact('videos', 'total'));
    }
}
