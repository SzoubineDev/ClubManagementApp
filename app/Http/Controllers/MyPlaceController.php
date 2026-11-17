<?php

namespace App\Http\Controllers;

use App\Models\Video;
use Illuminate\View\View;

/**
 * My Place — the vice-president's personal library.
 *
 * A read-only pool of 200 YouTube videos (100 English, 100 Arabic) about
 * motivation, letting go of the past and focusing on the future. Curated
 * through the seeder and watched on YouTube only.
 */
class MyPlaceController extends Controller
{
    public function index(): View
    {
        $this->authorize('viewMyPlaceAny', Video::class);

        // Show a fresh random selection of 12 on every reload. The full
        // library stays available; only the visible slice rotates.
        $videos = Video::query()
            ->inLibrary(Video::LIBRARY_MY_PLACE)
            ->inRandomOrder()
            ->limit(12)
            ->get();

        $total = Video::query()->inLibrary(Video::LIBRARY_MY_PLACE)->count();

        return view('my-place.index', compact('videos', 'total'));
    }
}
