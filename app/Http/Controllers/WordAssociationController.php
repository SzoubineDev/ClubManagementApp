<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Word association (rebus) game: a figure is shown for one minute and the
 * room has to decode the picture into a word or phrase. Letters, words and
 * emoji are mixed, so the answer hides in the sound and shape of the figure.
 *
 * Examples: F👀D -> "food with eyes" -> "see food" -> seafood.
 *           🌙MARE -> "night + mare" -> nightmare.
 */
class WordAssociationController extends Controller
{
    private const ROUND_SECONDS = 60;

    private const SESSION_USED = 'word_assoc.used';

    private const SESSION_CURRENT = 'word_assoc.current';

    /**
     * 50 distinct figures. Each answer is used only once, and the emoji
     * families are deliberately spread out so no theme dominates the deck.
     *
     * @var array<int, array{id:int, figure:string, answer:string, explanation:string, hint:string}>
     */
    private const PUZZLES = [
        [
            'id' => 1,
            'figure' => 'F👀D',
            'answer' => 'Seafood',
            'explanation' => "The two O's are eyes — 'see food' sounds like seafood.",
            'hint' => 'You would find it on a restaurant menu.',
        ],
        [
            'id' => 2,
            'figure' => '👁️LAND',
            'answer' => 'Island',
            'explanation' => "An eye sounds like 'I' — I + land = island.",
            'hint' => 'It is surrounded by water.',
        ],
        [
            'id' => 3,
            'figure' => 'I📈',
            'answer' => 'Irate',
            'explanation' => 'The letter I plus a rate = irate.',
            'hint' => 'Another word for angry.',
        ],
        [
            'id' => 4,
            'figure' => 'BEE🍃',
            'answer' => 'Belief',
            'explanation' => "BEE sounds like 'be' — be + leaf = belief.",
            'hint' => 'Something you hold to be true.',
        ],
        [
            'id' => 5,
            'figure' => '🐝FORE',
            'answer' => 'Before',
            'explanation' => "A bee plus 'fore' sounds like before.",
            'hint' => 'Earlier in time.',
        ],
        [
            'id' => 6,
            'figure' => '🐝LOW',
            'answer' => 'Below',
            'explanation' => 'A bee plus low sounds like below.',
            'hint' => 'Underneath.',
        ],
        [
            'id' => 7,
            'figure' => '🐰CUT',
            'answer' => 'Haircut',
            'explanation' => 'A hare sounds like hair — hair + cut = haircut.',
            'hint' => 'A trip to the barber.',
        ],
        [
            'id' => 8,
            'figure' => '🐰STYLE',
            'answer' => 'Hairstyle',
            'explanation' => 'A hare sounds like hair — hair + style = hairstyle.',
            'hint' => 'The way your hair is shaped.',
        ],
        [
            'id' => 9,
            'figure' => '🌙MARE',
            'answer' => 'Nightmare',
            'explanation' => 'The moon stands for night — night + mare = nightmare.',
            'hint' => 'A bad dream.',
        ],
        [
            'id' => 10,
            'figure' => '🌙GOWN',
            'answer' => 'Nightgown',
            'explanation' => 'The moon stands for night — night + gown = nightgown.',
            'hint' => 'Sleepwear.',
        ],
        [
            'id' => 11,
            'figure' => '🌙CLUB',
            'answer' => 'Nightclub',
            'explanation' => 'The moon stands for night — night + club = nightclub.',
            'hint' => 'A place people dance until late.',
        ],
        [
            'id' => 12,
            'figure' => '☀️FLOWER',
            'answer' => 'Sunflower',
            'explanation' => 'Sun + flower = sunflower.',
            'hint' => 'A tall yellow bloom.',
        ],
        [
            'id' => 13,
            'figure' => '☀️RISE',
            'answer' => 'Sunrise',
            'explanation' => 'Sun + rise = sunrise.',
            'hint' => 'Dawn.',
        ],
        [
            'id' => 14,
            'figure' => '🌊SHELL',
            'answer' => 'Seashell',
            'explanation' => 'Sea + shell = seashell.',
            'hint' => 'You find them on the beach.',
        ],
        [
            'id' => 15,
            'figure' => '🌊HORSE',
            'answer' => 'Seahorse',
            'explanation' => 'Sea + horse = seahorse.',
            'hint' => 'A fish with a curled tail.',
        ],
        [
            'id' => 16,
            'figure' => '🍯MOON',
            'answer' => 'Honeymoon',
            'explanation' => 'Honey + moon = honeymoon.',
            'hint' => 'A trip taken after a wedding.',
        ],
        [
            'id' => 17,
            'figure' => '🍯BEE',
            'answer' => 'Honeybee',
            'explanation' => 'Honey + bee = honeybee.',
            'hint' => 'A buzzing honey maker.',
        ],
        [
            'id' => 18,
            'figure' => '🐈FISH',
            'answer' => 'Catfish',
            'explanation' => 'Cat + fish = catfish.',
            'hint' => 'A fish with whiskers.',
        ],
        [
            'id' => 19,
            'figure' => '🧈FLY',
            'answer' => 'Butterfly',
            'explanation' => 'Butter + fly = butterfly.',
            'hint' => 'A colourful winged insect.',
        ],
        [
            'id' => 20,
            'figure' => '🐉FLY',
            'answer' => 'Dragonfly',
            'explanation' => 'Dragon + fly = dragonfly.',
            'hint' => 'An insect with four wings.',
        ],
        [
            'id' => 21,
            'figure' => '⭐LIGHT',
            'answer' => 'Starlight',
            'explanation' => 'Star + light = starlight.',
            'hint' => 'The glow of the night sky.',
        ],
        [
            'id' => 22,
            'figure' => 'H🔥TDOG',
            'answer' => 'Hotdog',
            'explanation' => 'The O in HOT is fire — hot + dog = hotdog.',
            'hint' => 'A sausage in a bun.',
        ],
        [
            'id' => 23,
            'figure' => '🔥WORKS',
            'answer' => 'Fireworks',
            'explanation' => 'Fire + works = fireworks.',
            'hint' => 'A noisy sky display.',
        ],
        [
            'id' => 24,
            'figure' => '🧊BERG',
            'answer' => 'Iceberg',
            'explanation' => 'Ice + berg = iceberg.',
            'hint' => 'Most of it is underwater.',
        ],
        [
            'id' => 25,
            'figure' => '🧊SKATING',
            'answer' => 'Ice skating',
            'explanation' => 'Ice + skating = ice skating.',
            'hint' => 'A winter rink activity.',
        ],
        [
            'id' => 26,
            'figure' => '❄️MAN',
            'answer' => 'Snowman',
            'explanation' => 'Snow + man = snowman.',
            'hint' => 'Built with a carrot nose.',
        ],
        [
            'id' => 27,
            'figure' => '❄️BALL',
            'answer' => 'Snowball',
            'explanation' => 'Snow + ball = snowball.',
            'hint' => 'Rolled up for a fight.',
        ],
        [
            'id' => 28,
            'figure' => '🌧️BOW',
            'answer' => 'Rainbow',
            'explanation' => 'Rain + bow = rainbow.',
            'hint' => 'Seven colours after the storm.',
        ],
        [
            'id' => 29,
            'figure' => '🌧️COAT',
            'answer' => 'Raincoat',
            'explanation' => 'Rain + coat = raincoat.',
            'hint' => 'Worn in wet weather.',
        ],
        [
            'id' => 30,
            'figure' => '🧀BURGER',
            'answer' => 'Cheeseburger',
            'explanation' => 'Cheese + burger = cheeseburger.',
            'hint' => 'A burger with a melted slice.',
        ],
        [
            'id' => 31,
            'figure' => '🧀CAKE',
            'answer' => 'Cheesecake',
            'explanation' => 'Cheese + cake = cheesecake.',
            'hint' => 'A rich dessert.',
        ],
        [
            'id' => 32,
            'figure' => '🥜BUTTER',
            'answer' => 'Peanut butter',
            'explanation' => 'Peanut + butter = peanut butter.',
            'hint' => 'A spread in a jar.',
        ],
        [
            'id' => 33,
            'figure' => '🥚PLANT',
            'answer' => 'Eggplant',
            'explanation' => 'Egg + plant = eggplant.',
            'hint' => 'A purple vegetable.',
        ],
        [
            'id' => 34,
            'figure' => '🥛SHAKE',
            'answer' => 'Milkshake',
            'explanation' => 'Milk + shake = milkshake.',
            'hint' => 'A cold drink with a straw.',
        ],
        [
            'id' => 35,
            'figure' => '🦁FISH',
            'answer' => 'Lionfish',
            'explanation' => 'Lion + fish = lionfish.',
            'hint' => 'A striped fish with venomous spines.',
        ],
        [
            'id' => 36,
            'figure' => '🦁TIGER',
            'answer' => 'Liger',
            'explanation' => 'Lion + tiger = liger.',
            'hint' => 'A big-cat hybrid.',
        ],
        [
            'id' => 37,
            'figure' => '🦓CROSSING',
            'answer' => 'Zebra crossing',
            'explanation' => 'Zebra + crossing = zebra crossing.',
            'hint' => 'Where pedestrians cross.',
        ],
        [
            'id' => 38,
            'figure' => '🦊GRAPES',
            'answer' => 'Sour grapes',
            'explanation' => 'The fox and the grapes — sour grapes.',
            'hint' => 'Pretending you never wanted it.',
        ],
        [
            'id' => 39,
            'figure' => '📚WORM',
            'answer' => 'Bookworm',
            'explanation' => 'Book + worm = bookworm.',
            'hint' => 'Someone who reads a lot.',
        ],
        [
            'id' => 40,
            'figure' => '📚MARK',
            'answer' => 'Bookmark',
            'explanation' => 'Book + mark = bookmark.',
            'hint' => 'It holds your place.',
        ],
        [
            'id' => 41,
            'figure' => '🍳CAKE',
            'answer' => 'Pancake',
            'explanation' => 'Pan + cake = pancake.',
            'hint' => 'A flat breakfast cake.',
        ],
        [
            'id' => 42,
            'figure' => '💧MELON',
            'answer' => 'Watermelon',
            'explanation' => 'Water + melon = watermelon.',
            'hint' => 'A big green fruit.',
        ],
        [
            'id' => 43,
            'figure' => '💧FALL',
            'answer' => 'Waterfall',
            'explanation' => 'Water + fall = waterfall.',
            'hint' => 'A river dropping over a cliff.',
        ],
        [
            'id' => 44,
            'figure' => '💨MILL',
            'answer' => 'Windmill',
            'explanation' => 'Wind + mill = windmill.',
            'hint' => 'It turns in the breeze.',
        ],
        [
            'id' => 45,
            'figure' => '🪨STAR',
            'answer' => 'Rockstar',
            'explanation' => 'Rock + star = rockstar.',
            'hint' => 'A famous musician.',
        ],
        [
            'id' => 46,
            'figure' => '🍵POT',
            'answer' => 'Teapot',
            'explanation' => 'Tea + pot = teapot.',
            'hint' => 'It pours at tea time.',
        ],
        [
            'id' => 47,
            'figure' => 'POP🌽',
            'answer' => 'Popcorn',
            'explanation' => 'Pop + corn = popcorn.',
            'hint' => 'A cinema snack.',
        ],
        [
            'id' => 48,
            'figure' => '🏖️CASTLE',
            'answer' => 'Sandcastle',
            'explanation' => 'Sand + castle = sandcastle.',
            'hint' => 'Built at the beach.',
        ],
        [
            'id' => 49,
            'figure' => 'KEY🕳️',
            'answer' => 'Keyhole',
            'explanation' => 'Key + hole = keyhole.',
            'hint' => 'Where the key goes.',
        ],
        [
            'id' => 50,
            'figure' => 'CUP📋',
            'answer' => 'Cupboard',
            'explanation' => "Cup + board = cupboard (say 'cubberd').",
            'hint' => 'Where plates are stored.',
        ],
    ];

    public function index(Request $request): View
    {
        $used = $this->used($request);
        $current = $this->currentPuzzle($request);

        return view('games.word-assoc', [
            'total' => count(self::PUZZLES),
            'remaining' => count(self::PUZZLES) - count($used),
            'used' => $used,
            'current' => $current === null ? null : [
                'id' => $current['id'],
                'figure' => $current['figure'],
            ],
            'seconds' => self::ROUND_SECONDS,
        ]);
    }

    public function draw(Request $request): JsonResponse
    {
        $used = $this->used($request);

        $available = array_values(array_filter(
            self::PUZZLES,
            static fn (array $puzzle): bool => ! in_array($puzzle['id'], $used, true)
        ));

        if ($available === []) {
            return response()->json([
                'message' => 'All '.count(self::PUZZLES).' figures have been played. Reset the deck to start a new round.',
                'remaining' => 0,
                'used' => $used,
            ], 409);
        }

        $puzzle = $available[random_int(0, count($available) - 1)];

        $used[] = $puzzle['id'];
        sort($used);

        $request->session()->put(self::SESSION_USED, $used);
        $request->session()->put(self::SESSION_CURRENT, $puzzle['id']);

        return response()->json([
            'id' => $puzzle['id'],
            'figure' => $puzzle['figure'],
            'hint' => $puzzle['hint'],
            'seconds' => self::ROUND_SECONDS,
            'used' => $used,
            'remaining' => count(self::PUZZLES) - count($used),
        ]);
    }

    public function reveal(Request $request): JsonResponse
    {
        $puzzle = $this->currentPuzzle($request);

        if ($puzzle === null) {
            return response()->json([
                'message' => 'Draw a figure before asking for the answer.',
            ], 409);
        }

        return response()->json([
            'id' => $puzzle['id'],
            'figure' => $puzzle['figure'],
            'answer' => $puzzle['answer'],
            'explanation' => $puzzle['explanation'],
        ]);
    }

    public function reset(Request $request): JsonResponse
    {
        $request->session()->forget([self::SESSION_USED, self::SESSION_CURRENT]);

        return response()->json([
            'message' => 'The deck is shuffled back to all '.count(self::PUZZLES).' figures.',
            'used' => [],
            'remaining' => count(self::PUZZLES),
        ]);
    }

    /**
     * @return array<int, int>
     */
    private function used(Request $request): array
    {
        $used = $request->session()->get(self::SESSION_USED, []);

        if (! is_array($used)) {
            return [];
        }

        $validIds = array_column(self::PUZZLES, 'id');

        $used = array_values(array_intersect(
            array_unique(array_map('intval', $used)),
            $validIds
        ));

        sort($used);

        return $used;
    }

    /**
     * @return array{id:int, figure:string, answer:string, explanation:string, hint:string}|null
     */
    private function currentPuzzle(Request $request): ?array
    {
        $id = $request->session()->get(self::SESSION_CURRENT);

        if (! is_numeric($id)) {
            return null;
        }

        foreach (self::PUZZLES as $puzzle) {
            if ($puzzle['id'] === (int) $id) {
                return $puzzle;
            }
        }

        return null;
    }
}
