<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Presenter picker: draws a unique number from 1 to 80 for the current
 * session. Once a number has been drawn it never comes back until the
 * board is reset, so each member presents exactly once per round.
 */
class SpinTheWheelController extends Controller
{
    private const MIN = 1;

    private const MAX = 80;

    private const MEME_COUNT = 20;

    private const MEME_EXTENSIONS = ['png', 'jpg', 'jpeg', 'webp', 'gif'];

    private const AUDIO_COUNT = 10;

    private const AUDIO_EXTENSIONS = ['mp3', 'wav', 'ogg', 'm4a'];

    private const SESSION_DRAWN = 'spin_the_wheel.drawn';

    private const SESSION_LAST = 'spin_the_wheel.last';

    private const SESSION_AUDIO_QUEUE = 'spin_the_wheel.audio_queue';

    private const SESSION_AUDIO_LAST = 'spin_the_wheel.audio_last';

    public function index(Request $request): View
    {
        $drawn = $this->drawn($request);

        return view('games.spin-the-wheel', [
            'drawn' => $drawn,
            'remaining' => self::MAX - count($drawn),
            'total' => self::MAX,
            'last' => $request->session()->get(self::SESSION_LAST),
        ]);
    }

    public function spin(Request $request): JsonResponse
    {
        $drawn = $this->drawn($request);

        $available = array_values(array_diff(range(self::MIN, self::MAX), $drawn));

        if ($available === []) {
            return response()->json([
                'message' => 'All '.self::MAX.' numbers have already been drawn. Reset the board to start a new round.',
                'remaining' => 0,
                'drawn' => $drawn,
            ], 409);
        }

        $number = $available[random_int(0, count($available) - 1)];

        $drawn[] = $number;
        sort($drawn);

        $request->session()->put(self::SESSION_DRAWN, $drawn);
        $request->session()->put(self::SESSION_LAST, $number);

        return response()->json([
            'number' => $number,
            'drawn' => $drawn,
            'remaining' => self::MAX - count($drawn),
            'announcement' => 'Number '.$number.' is going to present themselves now.',
            'image' => $this->randomMemeImage(),
            'spin_audio' => $this->nextShuffledAudio($request, 'spin'),
            'result_audio' => $this->nextShuffledAudio($request, 'result'),
        ]);
    }

    public function reset(Request $request): JsonResponse
    {
        $request->session()->forget([self::SESSION_DRAWN, self::SESSION_LAST]);

        return response()->json([
            'message' => 'The board is clear. All '.self::MAX.' numbers are back in the wheel.',
            'drawn' => [],
            'remaining' => self::MAX,
        ]);
    }

    /**
     * Pick one of the meme cards that actually exist on disk (meme1 … meme20),
     * supporting common image extensions so missing files never break the card.
     */
    private function randomMemeImage(): ?string
    {
        $images = $this->numberedAssets('images/memes', 'meme', self::MEME_COUNT, self::MEME_EXTENSIONS);

        if ($images === []) {
            return null;
        }

        return asset($images[random_int(0, count($images) - 1)]);
    }

    /**
     * Draw the next audio file from a shuffled cycle of available tracks
     * (e.g. spin1..spin10 or result1..result10). Every track plays once before
     * the list is reshuffled, and consecutive cycles never start with the same
     * track that just ended the previous one.
     */
    private function nextShuffledAudio(Request $request, string $prefix): ?string
    {
        $available = $this->numberedAssets('audios', $prefix, self::AUDIO_COUNT, self::AUDIO_EXTENSIONS);

        if ($available === []) {
            return null;
        }

        $queueKey = self::SESSION_AUDIO_QUEUE.'.'.$prefix;
        $lastKey = self::SESSION_AUDIO_LAST.'.'.$prefix;

        $queue = $request->session()->get($queueKey, []);
        if (! is_array($queue)) {
            $queue = [];
        }

        $availableSet = array_flip($available);
        $queue = array_values(array_filter(
            $queue,
            static fn ($path): bool => is_string($path) && isset($availableSet[$path])
        ));

        $last = $request->session()->get($lastKey);
        if (! is_string($last)) {
            $last = null;
        }

        if ($queue === []) {
            $queue = $available;
            $this->fisherYatesShuffle($queue);

            // Prevent an immediate repeat across the cycle boundary when multiple tracks exist.
            if (count($queue) > 1 && $last !== null && $queue[0] === $last) {
                $swapIndex = random_int(1, count($queue) - 1);
                [$queue[0], $queue[$swapIndex]] = [$queue[$swapIndex], $queue[0]];
            }
        }

        $chosen = array_shift($queue);

        $request->session()->put($queueKey, $queue);
        $request->session()->put($lastKey, $chosen);

        return asset($chosen);
    }

    /**
     * @param  array<int, string>  $items
     */
    private function fisherYatesShuffle(array &$items): void
    {
        for ($i = count($items) - 1; $i > 0; $i--) {
            $j = random_int(0, $i);
            [$items[$i], $items[$j]] = [$items[$j], $items[$i]];
        }
    }

    /**
     * Collect one existing relative asset path per numbered slot (1..$maxCount)
     * in a public subdirectory, matching extensions case-insensitively.
     *
     * @param  array<int, string>  $extensions
     * @return array<int, string>
     */
    private function numberedAssets(string $relativeDir, string $prefix, int $maxCount, array $extensions): array
    {
        $directory = public_path($relativeDir);

        if (! is_dir($directory)) {
            return [];
        }

        $files = scandir($directory) ?: [];
        $byName = [];

        foreach ($files as $file) {
            if ($file === '.' || $file === '..' || ! is_file($directory.DIRECTORY_SEPARATOR.$file)) {
                continue;
            }

            $byName[strtolower($file)] = $file;
        }

        $assets = [];

        for ($i = 1; $i <= $maxCount; $i++) {
            foreach ($extensions as $ext) {
                $key = strtolower($prefix.$i.'.'.$ext);

                if (isset($byName[$key])) {
                    $assets[] = $relativeDir.'/'.$byName[$key];
                    break;
                }
            }
        }

        return $assets;
    }

    /**
     * @return array<int, int>
     */
    private function drawn(Request $request): array
    {
        $drawn = $request->session()->get(self::SESSION_DRAWN, []);

        if (! is_array($drawn)) {
            return [];
        }

        $drawn = array_values(array_intersect(
            array_unique(array_map('intval', $drawn)),
            range(self::MIN, self::MAX)
        ));

        sort($drawn);

        return $drawn;
    }
}
