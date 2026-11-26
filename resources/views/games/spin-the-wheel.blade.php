<x-app-layout>
    <x-slot name="header">
        <div class="flex items-start justify-between gap-4 w-full flex-wrap">
            <div>
                <h1 class="page-header-title">Spin the Wheel</h1>
                <p class="page-header-sub">
                    Pick the next presenter — every number from 1 to {{ $total }} comes up once.
                </p>
            </div>
            <div class="flex items-center gap-2 flex-wrap">
                <span class="chip chip--brand chip-lg" id="headerRemaining">
                    {{ $remaining }} of {{ $total }} left
                </span>
                <button type="button" class="btn btn-outline btn-sm" id="toggleBoard" aria-pressed="false" aria-controls="presenterBoard">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M3 3l18 18M10.584 10.587a2 2 0 002.828 2.83M9.363 5.365A9.466 9.466 0 0112 5c4.478 0 8.268 2.943 9.542 7a9.52 9.52 0 01-2.19 3.56M6.228 6.228A9.5 9.5 0 002.458 12c1.274 4.057 5.064 7 9.542 7a9.46 9.46 0 004.29-1.032" />
                    </svg>
                    <span id="toggleBoardLabel">Hide board</span>
                </button>
            </div>
        </div>
    </x-slot>

    <div class="content-wrap">
        <div class="wheel-layout">

            {{-- Wheel ---------------------------------------------------- --}}
            <section class="wheel-stage animate-fade-in-up">
                <div class="wheel-wrap">
                    <div class="wheel-pointer" aria-hidden="true"></div>

                    <div class="wheel-shell">
                        <div class="wheel" id="wheel" aria-hidden="true"></div>
                    </div>

                    <div class="wheel-hub {{ $last ? 'is-result' : '' }}">
                        <span class="wheel-hub-label" id="hubLabel">{{ $last ? 'Number' : 'Presenter' }}</span>
                        <span class="wheel-hub-number" id="hubNumber">{{ $last ?? '?' }}</span>
                        <span class="wheel-hub-caption" id="hubCaption">{{ $last ? 'presenting' : 'spin to begin' }}</span>
                    </div>
                </div>

                <div class="wheel-actions">
                    <button type="button" id="spinButton" class="btn btn-primary btn-lg wheel-spin-btn"
                        @if ($remaining === 0) disabled @endif>
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                        </svg>
                        <span class="spin-label">Spin the wheel</span>
                    </button>
                    <p class="wheel-hint" id="wheelHint">
                        {{ $remaining === 0
                            ? 'Every number has presented. Reset the board for a new round.'
                            : $remaining.' number'.($remaining === 1 ? '' : 's').' still in the wheel.' }}
                    </p>
                </div>

                <div class="announce-bar {{ $last ? 'is-live' : '' }}" id="announceBar" aria-live="polite" aria-atomic="true">
                    <div class="announce-number" id="announceNumber">{{ $last ? 'N° '.$last : '—' }}</div>
                    <div class="announce-copy">
                        <span class="announce-eyebrow">Now presenting</span>
                        <p class="announce-text" id="announceText">
                            {{ $last
                                ? 'Number '.$last.' is going to present themselves now.'
                                : 'Spin to reveal who goes next.' }}
                        </p>
                    </div>
                </div>
            </section>

            {{-- Board ---------------------------------------------------- --}}
            <aside class="wheel-aside stack stack--sm animate-fade-in-up delay-100" id="presenterBoard">
                <section class="card">
                    <div class="card-header">
                        <div>
                            <h3 class="card-title">Presenter board</h3>
                            <p class="card-subtitle">Completed numbers are filled — no repeats</p>
                        </div>
                        <span class="chip" id="remainingChip">{{ $remaining }} left</span>
                    </div>

                    <div class="card-body">
                        <div class="number-grid" id="numberGrid">
                            @for ($n = 1; $n <= $total; $n++)
                                <span class="number-cell {{ in_array($n, $drawn, true) ? 'is-drawn' : '' }}"
                                    data-number="{{ $n }}">{{ $n }}</span>
                            @endfor
                        </div>
                    </div>

                    <div class="card-footer">
                        <span class="text-xs text-muted" id="drawnCount">{{ count($drawn) }} drawn</span>
                        <button type="button" class="btn btn-outline btn-sm" id="resetButton"
                            @if (count($drawn) === 0) disabled @endif>
                            Reset board
                        </button>
                    </div>
                </section>
            </aside>

        </div>
    </div>

    {{-- Big result card shown after every draw -------------------------------- --}}
    <div class="result-overlay" id="resultOverlay">
        <div class="result-card" id="resultCard" role="dialog" aria-modal="true" aria-labelledby="resultText">
            <div class="result-media" id="resultMedia">
                <img class="result-image" id="resultImage" src="/images/memes/meme1.png" alt="Presenter meme">
            </div>
            <div class="result-content">
                <div class="result-header">
                    <div class="result-number" id="resultNumber">—</div>
                    <div class="result-copy">
                        <span class="result-eyebrow">Now presenting</span>
                        <p class="result-text" id="resultText">Number — is going to present themselves now.</p>
                    </div>
                </div>
                <div class="result-actions">
                    <button type="button" class="btn btn-primary" id="resultSpinAgain">Spin again</button>
                    <button type="button" class="btn btn-invert-text" id="resultClose">Close</button>
                </div>
            </div>
        </div>
    </div>

    <style>
        .wheel-layout {
            display: grid;
            grid-template-columns: 1fr;
            gap: 24px;
            align-items: start;
        }

        @media (min-width: 1024px) {
            .wheel-layout { grid-template-columns: minmax(0, 1.5fr) minmax(0, 0.7fr); }
            .wheel-layout.is-board-hidden { grid-template-columns: 1fr; }
        }

        .wheel-layout.is-board-hidden .wheel-aside { display: none; }

        .wheel-layout.is-board-hidden .wheel-wrap { width: min(100%, 500px); }

        #toggleBoard[aria-pressed="true"] {
            color: var(--bordeaux-700);
            background: var(--bordeaux-50);
            border-color: var(--bordeaux-200);
        }

        .wheel-stage {
            position: relative;
            overflow: hidden;
            padding: 40px 24px 24px;
            border-radius: var(--radius-card);
            background: linear-gradient(165deg, #FFFFFF 0%, #FDF6F8 55%, #FBEBEF 100%);
            box-shadow: var(--elevation-2);
        }

        .wheel-stage::before {
            content: "";
            position: absolute;
            inset: 0;
            pointer-events: none;
            background-image: radial-gradient(circle at 1px 1px, rgba(155, 27, 63, .08) 1px, transparent 0);
            background-size: 20px 20px;
        }

        .wheel-wrap {
            position: relative;
            width: min(100%, 430px);
            aspect-ratio: 1;
            margin: 0 auto;
        }

        .wheel-shell {
            position: absolute;
            inset: 0;
            padding: 14px;
            border-radius: 50%;
            background: radial-gradient(circle at 30% 18%, #C42A55, #7A1531 58%, #5C0F24 100%);
            box-shadow:
                0 30px 60px -22px rgba(92, 15, 36, .60),
                inset 0 2px 6px rgba(255, 255, 255, .35),
                inset 0 -12px 26px rgba(0, 0, 0, .35);
            transition: box-shadow 300ms var(--ease-standard);
        }

        .wheel-shell.is-spinning {
            animation: wheelGlow 1.4s ease-in-out infinite;
        }

        @keyframes wheelGlow {
            0%, 100% {
                box-shadow:
                    0 30px 60px -22px rgba(92, 15, 36, .60),
                    0 0 0 0 rgba(245, 166, 35, .35),
                    inset 0 2px 6px rgba(255, 255, 255, .35),
                    inset 0 -12px 26px rgba(0, 0, 0, .35);
            }
            50% {
                box-shadow:
                    0 30px 68px -20px rgba(92, 15, 36, .70),
                    0 0 0 9px rgba(245, 166, 35, .16),
                    inset 0 2px 6px rgba(255, 255, 255, .35),
                    inset 0 -12px 26px rgba(0, 0, 0, .35);
            }
        }

        .wheel {
            position: relative;
            width: 100%;
            height: 100%;
            border-radius: 50%;
            overflow: hidden;
            background: repeating-conic-gradient(from 0deg,
                #FFFDFC 0deg 7.2deg,
                #F4DFE5 7.2deg 14.4deg);
            box-shadow:
                inset 0 0 0 2px rgba(92, 15, 36, .22),
                inset 0 0 32px rgba(122, 21, 49, .18);
            transition: transform 4200ms cubic-bezier(.12, .75, .04, 1);
            will-change: transform;
        }

        /* Thin divider every 10 numbers. */
        .wheel::before {
            content: "";
            position: absolute;
            inset: 0;
            border-radius: 50%;
            background: repeating-conic-gradient(from 0deg,
                rgba(155, 27, 63, .55) 0deg .5deg,
                transparent .5deg 72deg);
            opacity: .75;
        }

        /* Vignette + inner rim. */
        .wheel::after {
            content: "";
            position: absolute;
            inset: 0;
            border-radius: 50%;
            background: radial-gradient(circle at 50% 50%,
                transparent 52%,
                rgba(122, 21, 49, .10) 72%,
                rgba(92, 15, 36, .28) 100%);
            pointer-events: none;
        }

        .wheel-pointer {
            position: absolute;
            top: -18px;
            left: 50%;
            z-index: 4;
            width: 34px;
            height: 48px;
            transform: translateX(-50%);
            filter: drop-shadow(0 8px 10px rgba(92, 15, 36, .35));
        }

        .wheel-pointer::before {
            content: "";
            position: absolute;
            top: 0;
            left: 50%;
            width: 24px;
            height: 24px;
            border-radius: 50%;
            transform: translateX(-50%);
            background: radial-gradient(circle at 35% 30%, #FCC94E, #D98406);
            box-shadow: inset 0 0 0 3px rgba(255, 255, 255, .45);
        }

        .wheel-pointer::after {
            content: "";
            position: absolute;
            top: 17px;
            left: 50%;
            transform: translateX(-50%);
            border-left: 11px solid transparent;
            border-right: 11px solid transparent;
            border-top: 28px solid #F5A623;
        }

        .wheel-hub {
            position: absolute;
            top: 50%;
            left: 50%;
            z-index: 3;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            width: 34%;
            aspect-ratio: 1;
            padding: 10px;
            border-radius: 50%;
            text-align: center;
            transform: translate(-50%, -50%);
            background: radial-gradient(circle at 35% 25%, #FFFFFF, #FBF3F5 60%, #F3DEE4 100%);
            box-shadow:
                0 0 0 6px rgba(255, 255, 255, .55),
                0 18px 32px -12px rgba(92, 15, 36, .55),
                inset 0 0 0 1px rgba(155, 27, 63, .18);
        }

        .wheel-hub-label,
        .wheel-hub-caption {
            font-size: 9px;
            font-weight: 700;
            line-height: 1.2;
            letter-spacing: .16em;
            text-transform: uppercase;
        }

        .wheel-hub-label { color: var(--bordeaux-700); }

        .wheel-hub-number {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-width: 1.7em;
            margin: 3px 0;
            padding: 4px 10px;
            border-radius: 10px;
            font-size: clamp(34px, 9vw, 54px);
            font-weight: 700;
            line-height: 1;
            letter-spacing: -.03em;
            color: var(--bordeaux-800);
            font-variant-numeric: tabular-nums;
            background: transparent;
            transition:
                background 220ms var(--ease-standard),
                color 220ms var(--ease-standard),
                box-shadow 220ms var(--ease-standard);
        }

        .wheel-hub.is-result .wheel-hub-number {
            color: #fff;
            background: linear-gradient(145deg, var(--bordeaux-600), var(--bordeaux-800));
            box-shadow: 0 10px 22px -9px rgba(92, 15, 36, .75);
            animation: numberSettle 520ms var(--ease-decelerate) both;
        }

        @keyframes numberSettle {
            0% { transform: scale(.82); }
            55% { transform: scale(1.1); }
            100% { transform: scale(1); }
        }

        .wheel-hub-caption { color: var(--gray-500); }

        .wheel-hub.is-live { animation: hubPop 600ms var(--ease-decelerate) both; }

        @keyframes hubPop {
            0% { transform: translate(-50%, -50%) scale(.86); }
            55% { transform: translate(-50%, -50%) scale(1.07); }
            100% { transform: translate(-50%, -50%) scale(1); }
        }

        .wheel-actions {
            position: relative;
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 8px;
            margin-top: 26px;
        }

        .wheel-spin-btn { min-width: 240px; }

        .wheel-hint {
            font-size: 12px;
            color: var(--gray-500);
            text-align: center;
        }

        .announce-bar {
            position: relative;
            display: flex;
            align-items: center;
            gap: 18px;
            overflow: hidden;
            margin-top: 24px;
            padding: 16px 20px;
            border-radius: 6px;
            color: #fff;
            background: linear-gradient(135deg, var(--bordeaux-700) 0%, var(--bordeaux-900) 100%);
            box-shadow: var(--elevation-4);
        }

        .announce-bar::after {
            content: "";
            position: absolute;
            top: -70px;
            right: -40px;
            width: 190px;
            height: 190px;
            border-radius: 50%;
            pointer-events: none;
            background: radial-gradient(circle, rgba(245, 166, 35, .38), transparent 70%);
        }

        .announce-number {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            min-width: 76px;
            padding: 8px 12px;
            border-radius: 8px;
            font-size: 26px;
            font-weight: 700;
            line-height: 1;
            letter-spacing: -.02em;
            color: var(--saffron-300);
            background: rgba(255, 255, 255, .10);
            box-shadow: inset 0 0 0 1px rgba(255, 255, 255, .14);
            font-variant-numeric: tabular-nums;
            text-align: center;
            transition:
                background 220ms var(--ease-standard),
                color 220ms var(--ease-standard),
                box-shadow 220ms var(--ease-standard);
        }

        .announce-bar.is-live .announce-number {
            color: #3B2F0B;
            background: linear-gradient(145deg, var(--saffron-300), var(--saffron-500));
            box-shadow: 0 10px 24px -10px rgba(245, 166, 35, .9);
            animation: numberSettle 520ms var(--ease-decelerate) both;
        }

        .announce-copy { position: relative; z-index: 1; min-width: 0; }

        .announce-eyebrow {
            display: block;
            font-size: 9px;
            font-weight: 700;
            letter-spacing: .22em;
            text-transform: uppercase;
            color: rgba(255, 255, 255, .55);
        }

        .announce-text {
            margin-top: 2px;
            font-size: 15px;
            font-weight: 500;
            line-height: 1.4;
            color: #fff;
        }

        .announce-bar.is-live { animation: announceIn 500ms var(--ease-decelerate) both; }

        @keyframes announceIn {
            0% { opacity: .55; transform: translateY(6px) scale(.985); }
            100% { opacity: 1; transform: none; }
        }

        /* Big result card ---------------------------------------------------- */
        .result-overlay {
            position: fixed;
            inset: 0;
            z-index: 90;
            display: none;
            align-items: center;
            justify-content: center;
            padding: 24px;
            background: rgba(26, 20, 22, .68);
            -webkit-backdrop-filter: blur(4px);
            backdrop-filter: blur(4px);
        }

        .result-overlay.is-open {
            display: flex;
            animation: fadeIn 220ms var(--ease-standard) both;
        }

        .result-card {
            position: relative;
            display: flex;
            flex-direction: column;
            width: min(94vw, 560px);
            max-height: 88vh;
            overflow: hidden;
            border-radius: 12px;
            background: #1A1416;
            box-shadow: 0 40px 90px -22px rgba(0, 0, 0, .72);
            animation: resultCardIn 500ms var(--ease-decelerate) both;
        }

        .result-media {
            position: relative;
            display: flex;
            align-items: center;
            justify-content: center;
            flex: 1 1 auto;
            min-height: 220px;
            max-height: 54vh;
            overflow: hidden;
            background: #120D0F;
        }

        .result-image {
            display: block;
            width: 100%;
            height: 100%;
            max-height: 54vh;
            object-fit: contain;
        }

        .result-content {
            position: relative;
            z-index: 1;
            display: flex;
            flex-direction: column;
            gap: 14px;
            padding: 16px 20px 20px;
            color: #fff;
            background: #1A1416;
            border-top: 1px solid rgba(255, 255, 255, .08);
        }

        .result-header {
            display: flex;
            align-items: center;
            gap: 14px;
        }

        .result-copy {
            min-width: 0;
        }

        .result-eyebrow {
            display: block;
            font-size: 10px;
            font-weight: 700;
            letter-spacing: .24em;
            text-transform: uppercase;
            color: var(--saffron-300);
        }

        .result-number {
            display: inline-flex;
            align-items: flex-start;
            flex-shrink: 0;
            gap: 2px;
            padding: 6px 14px 8px;
            border-radius: 10px;
            font-size: clamp(28px, 5vw, 40px);
            font-weight: 700;
            line-height: 1;
            letter-spacing: -.03em;
            color: #3B2F0B;
            font-variant-numeric: tabular-nums;
            background: linear-gradient(145deg, var(--saffron-300), var(--saffron-500));
            box-shadow: 0 12px 28px -14px rgba(0, 0, 0, .75);
        }

        .result-number::before {
            content: "N°";
            margin-top: .32em;
            margin-right: .14em;
            font-size: .36em;
            font-weight: 700;
            letter-spacing: .02em;
        }

        .result-text {
            margin-top: 4px;
            font-size: 15px;
            font-weight: 500;
            line-height: 1.4;
            color: #fff;
        }

        .result-actions {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
        }

        @keyframes resultCardIn {
            0% { opacity: 0; transform: translateY(18px) scale(.94); }
            100% { opacity: 1; transform: none; }
        }

        .number-grid {
            display: grid;
            grid-template-columns: repeat(8, 1fr);
            gap: 6px;
        }

        .number-cell {
            display: grid;
            place-items: center;
            aspect-ratio: 1;
            border-radius: 3px;
            font-size: 12px;
            font-weight: 600;
            color: var(--gray-700);
            background: var(--gray-100);
            font-variant-numeric: tabular-nums;
            transition: background 150ms var(--ease-standard),
                        color 150ms var(--ease-standard),
                        box-shadow 150ms var(--ease-standard);
        }

        .number-cell.is-drawn {
            color: var(--teal-700);
            background: var(--teal-100);
            box-shadow: inset 0 0 0 1px var(--teal-200);
            text-decoration: line-through;
            opacity: 1;
        }

        .number-cell.is-current {
            color: #fff;
            background: var(--bordeaux-700);
            box-shadow: 0 6px 14px -4px rgba(155, 27, 63, .55);
            opacity: 1;
            text-decoration: none;
            animation: cellPop 450ms var(--ease-decelerate) both;
        }

        @keyframes cellPop {
            0% { transform: scale(.82); }
            60% { transform: scale(1.14); }
            100% { transform: scale(1.08); }
        }

        @media (max-width: 639px) {
            .wheel-stage { padding: 34px 12px 18px; }

            .number-grid { grid-template-columns: repeat(10, 1fr); }

            .announce-bar { align-items: flex-start; gap: 14px; padding: 14px 16px; }

            .announce-number { min-width: 60px; font-size: 22px; }

            .result-overlay { padding: 16px; }

            .result-media { min-height: 180px; max-height: 46vh; }

            .result-image { max-height: 46vh; }

            .result-content { padding: 14px 16px 16px; }

            .result-header { gap: 12px; }

            .result-text { font-size: 14px; }

            .result-actions .btn { flex: 1 1 auto; }
        }
    </style>

    <script>
        (() => {
            'use strict';

            const wheel = document.getElementById('wheel');
            const wheelShell = document.querySelector('.wheel-shell');
            const wheelHub = document.querySelector('.wheel-hub');
            const spinButton = document.getElementById('spinButton');
            const resetButton = document.getElementById('resetButton');
            const hubNumber = document.getElementById('hubNumber');
            const hubLabel = document.getElementById('hubLabel');
            const hubCaption = document.getElementById('hubCaption');
            const announceBar = document.getElementById('announceBar');
            const announceNumber = document.getElementById('announceNumber');
            const announceText = document.getElementById('announceText');
            const remainingChip = document.getElementById('remainingChip');
            const headerRemaining = document.getElementById('headerRemaining');
            const drawnCount = document.getElementById('drawnCount');
            const wheelHint = document.getElementById('wheelHint');
            const resultOverlay = document.getElementById('resultOverlay');
            const resultMedia = document.getElementById('resultMedia');
            const resultImage = document.getElementById('resultImage');
            const resultNumber = document.getElementById('resultNumber');
            const resultText = document.getElementById('resultText');
            const resultClose = document.getElementById('resultClose');
            const resultSpinAgain = document.getElementById('resultSpinAgain');
            const DEFAULT_RESULT_IMAGE = '/images/memes/meme1.png';

            const TOTAL = Number(@json($total));
            const SPIN_URL = @json(route('games.spin-the-wheel.spin'));
            const RESET_URL = @json(route('games.spin-the-wheel.reset'));
            const CSRF = document.querySelector('meta[name="csrf-token"]').content;
            const REDUCED_MOTION = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

            const SEGMENT = 360 / TOTAL;
            const FULL_TURNS = 6;
            const SPIN_MS = REDUCED_MOTION ? 400 : 4200;

            let drawn = @json($drawn);
            let angle = 0;
            let spinning = false;
            let lastDrawn = @json($last);
            let resultReturnFocus = null;

            const SILENT_WAV = 'data:audio/wav;base64,UklGRigAAABXQVZFZm10IBIAAAABAAEARKwAAIhYAQACABAAAABkYXRhAgAAAAEA';
            const spinAudio = new Audio();
            const resultAudio = new Audio();
            spinAudio.preload = 'auto';
            resultAudio.preload = 'auto';

            const sleep = (ms) => new Promise((resolve) => setTimeout(resolve, ms));

            function unlockAudio(audio) {
                if (!audio || audio.dataset.unlocked === '1') {
                    return;
                }

                audio.dataset.unlocked = '1';
                audio.dataset.trackUrl = '';
                audio.src = SILENT_WAV;

                const attempt = audio.play();
                if (attempt && typeof attempt.catch === 'function') {
                    attempt.catch(() => {});
                }
            }

            function preloadAudio(audio, url) {
                if (!audio || !url || audio.dataset.trackUrl === url) {
                    return;
                }

                audio.dataset.trackUrl = url;
                audio.src = url;
                audio.load();
            }

            function stopAudio(audio) {
                if (!audio) {
                    return;
                }

                audio.pause();
                try {
                    audio.currentTime = 0;
                } catch (e) {
                    // Ignore when no media source is loaded yet.
                }
            }

            function playAudio(audio, url) {
                if (!audio || !url) {
                    return;
                }

                if (audio.dataset.trackUrl !== url) {
                    audio.dataset.trackUrl = url;
                    audio.src = url;
                }

                try {
                    audio.currentTime = 0;
                } catch (e) {
                    // Ignore before metadata loads.
                }

                const attempt = audio.play();
                if (attempt && typeof attempt.catch === 'function') {
                    attempt.catch(() => {
                        // Ignore browser autoplay restrictions.
                    });
                }
            }

            function randomNumber() {
                return Math.floor(Math.random() * TOTAL) + 1;
            }

            function renderBoard(current = null) {
                const drawnSet = new Set(drawn);

                document.querySelectorAll('.number-cell').forEach((cell) => {
                    const number = Number(cell.dataset.number);
                    cell.classList.toggle('is-drawn', drawnSet.has(number));
                    cell.classList.toggle('is-current', number === current);
                });

                const remaining = TOTAL - drawn.length;

                remainingChip.textContent = remaining + ' left';
                headerRemaining.textContent = remaining + ' of ' + TOTAL + ' left';
                drawnCount.textContent = drawn.length + ' drawn';

                if (remaining === 0) {
                    wheelHint.textContent = 'Every number has presented. Reset the board for a new round.';
                } else {
                    wheelHint.textContent = remaining + (remaining === 1 ? ' number' : ' numbers') + ' still in the wheel.';
                }

                spinButton.disabled = spinning || remaining === 0;
                resetButton.disabled = spinning || drawn.length === 0;
            }

            function setHub(number, label, caption) {
                hubNumber.textContent = number;
                hubLabel.textContent = label;
                hubCaption.textContent = caption;
            }

            function pulseHub() {
                wheelHub.classList.remove('is-live');
                void wheelHub.offsetWidth;
                wheelHub.classList.add('is-live');
            }

            function setAnnouncement(number, text) {
                announceNumber.textContent = number;
                announceText.textContent = text;
                announceBar.classList.remove('is-live');
                void announceBar.offsetWidth;
                announceBar.classList.add('is-live');
            }

            function openResult(number, text, imageUrl) {
                const chosenImage = imageUrl || DEFAULT_RESULT_IMAGE;

                resultImage.dataset.fallbackApplied = '0';
                resultImage.src = chosenImage;
                resultMedia.hidden = false;
                resultNumber.textContent = String(number);
                resultText.textContent = text;
                resultSpinAgain.disabled = drawn.length >= TOTAL;

                resultReturnFocus = document.activeElement;
                resultOverlay.classList.add('is-open');
                document.body.classList.add('dialog-open');

                window.setTimeout(() => resultClose.focus(), 60);
            }

            resultImage.addEventListener('error', () => {
                if (resultImage.dataset.fallbackApplied === '1') {
                    resultMedia.hidden = true;
                    return;
                }

                resultImage.dataset.fallbackApplied = '1';
                resultImage.src = DEFAULT_RESULT_IMAGE;
            });

            function closeResult() {
                stopAudio(resultAudio);

                if (!resultOverlay.classList.contains('is-open')) {
                    return;
                }

                resultOverlay.classList.remove('is-open');
                document.body.classList.remove('dialog-open');

                const target = resultReturnFocus;
                resultReturnFocus = null;

                if (target && document.contains(target)) {
                    target.focus();
                }
            }

            function targetAngleFor(number) {
                // CSS conic gradients start at the top and go clockwise, so the
                // segment centre must be rotated back under the top pointer.
                const desired = (360 - (number - 0.5) * SEGMENT) % 360;
                const current = ((angle % 360) + 360) % 360;
                const delta = (desired - current + 360) % 360;

                return angle + (360 * FULL_TURNS) + delta;
            }

            function rotateTo(number, duration) {
                angle = targetAngleFor(number);
                wheel.style.transitionDuration = duration + 'ms';
                wheel.style.transform = 'rotate(' + angle + 'deg)';
            }

            // Restore the winning segment under the pointer after a reload.
            const initialLast = @json($last);
            if (initialLast) {
                angle = (360 - (initialLast - 0.5) * SEGMENT) % 360;
                wheel.style.transition = 'none';
                wheel.style.transform = 'rotate(' + angle + 'deg)';
                void wheel.offsetWidth;
                wheel.style.transition = '';
            }

            async function request(url, method) {
                const response = await fetch(url, {
                    method,
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': CSRF,
                    },
                });

                let data = {};
                try {
                    data = await response.json();
                } catch (e) {
                    // Keep the generic message below.
                }

                if (!response.ok) {
                    const error = new Error(data.message || 'Something went wrong. Please try again.');
                    error.status = response.status;
                    error.data = data;
                    throw error;
                }

                return data;
            }

            function toast(message, type) {
                if (window.TGD && typeof window.TGD.toast === 'function') {
                    window.TGD.toast(message, type);
                }
            }

            async function spin() {
                if (spinning) {
                    return;
                }

                closeResult();
                stopAudio(spinAudio);
                stopAudio(resultAudio);
                unlockAudio(spinAudio);
                unlockAudio(resultAudio);
                spinning = true;
                spinButton.disabled = true;
                resetButton.disabled = true;
                spinButton.querySelector('.spin-label').textContent = 'Spinning…';
                wheelShell.classList.add('is-spinning');
                wheelHub.classList.remove('is-result');
                announceBar.classList.remove('is-live');
                announceNumber.textContent = '…';
                announceText.textContent = 'The wheel is turning.';
                setHub('?', 'Drawing', 'please wait');

                const ticker = setInterval(() => {
                    hubNumber.textContent = String(randomNumber());
                }, 50);

                try {
                    const data = await request(SPIN_URL, 'POST');

                    drawn = data.drawn || drawn;
                    lastDrawn = data.number;

                    if (data.image) {
                        const preload = new Image();
                        preload.src = data.image;
                    }

                    if (data.spin_audio) {
                        playAudio(spinAudio, data.spin_audio);
                    }

                    if (data.result_audio) {
                        preloadAudio(resultAudio, data.result_audio);
                    }

                    rotateTo(data.number, SPIN_MS);
                    await sleep(SPIN_MS);
                    clearInterval(ticker);
                    stopAudio(spinAudio);

                    setHub(String(data.number), 'Number', 'presenting');
                    wheelHub.classList.add('is-result');
                    pulseHub();
                    setAnnouncement('N° ' + data.number, data.announcement);
                    renderBoard(data.number);
                    openResult(data.number, data.announcement, data.image);

                    if (data.result_audio) {
                        playAudio(resultAudio, data.result_audio);
                    }
                } catch (error) {
                    clearInterval(ticker);
                    stopAudio(spinAudio);

                    if (error.status === 409) {
                        drawn = error.data?.drawn || drawn;
                        setHub('—', 'Board', 'complete');
                        wheelHub.classList.remove('is-result');
                        setAnnouncement('—', 'All ' + TOTAL + ' numbers have presented. Reset the board to start again.');
                    } else {
                        setHub(lastDrawn ? String(lastDrawn) : '?', lastDrawn ? 'Number' : 'Presenter', lastDrawn ? 'presenting' : 'ready');
                        wheelHub.classList.toggle('is-result', Boolean(lastDrawn));
                        announceText.textContent = lastDrawn
                            ? 'Number ' + lastDrawn + ' is going to present themselves now.'
                            : 'Spin to reveal who goes next.';
                    }

                    toast(error.message, 'error');
                } finally {
                    clearInterval(ticker);
                    spinning = false;
                    wheelShell.classList.remove('is-spinning');
                    spinButton.querySelector('.spin-label').textContent = 'Spin the wheel';
                    renderBoard();
                }
            }

            async function reset() {
                if (spinning) {
                    return;
                }

                const confirmed = window.TGD && await window.TGD.confirm({
                    title: 'Reset the board?',
                    message: 'Every drawn number goes back into the wheel and a new round starts with all ' + TOTAL + ' numbers.',
                    confirmLabel: 'Reset board',
                    cancelLabel: 'Keep playing',
                    danger: true,
                });

                if (window.TGD && confirmed === false) {
                    return;
                }

                resetButton.disabled = true;

                try {
                    const data = await request(RESET_URL, 'DELETE');

                    drawn = data.drawn || [];
                    lastDrawn = null;
                    angle = 0;

                    wheel.style.transition = 'none';
                    wheel.style.transform = 'rotate(0deg)';
                    void wheel.offsetWidth;
                    wheel.style.transition = '';

                    setHub('?', 'Presenter', 'spin to begin');
                    wheelHub.classList.remove('is-live', 'is-result');
                    announceNumber.textContent = '—';
                    announceText.textContent = 'Spin to reveal who goes next.';
                    announceBar.classList.remove('is-live');
                    stopAudio(spinAudio);
                    closeResult();
                    renderBoard();
                    toast(data.message, 'success');
                } catch (error) {
                    toast(error.message, 'error');
                } finally {
                    renderBoard();
                }
            }

            spinButton.addEventListener('click', spin);
            resetButton.addEventListener('click', reset);

            // Big result card interactions.
            resultClose.addEventListener('click', closeResult);

            resultSpinAgain.addEventListener('click', () => {
                closeResult();
                spin();
            });

            resultOverlay.addEventListener('click', (event) => {
                if (event.target === resultOverlay) {
                    closeResult();
                }
            });

            document.addEventListener('keydown', (event) => {
                if (event.key === 'Escape' && resultOverlay.classList.contains('is-open')) {
                    event.preventDefault();
                    closeResult();
                }
            });

            resultOverlay.addEventListener('keydown', (event) => {
                if (event.key !== 'Tab') {
                    return;
                }

                const focusable = Array.from(resultOverlay.querySelectorAll('button:not([disabled])'));

                if (focusable.length === 0) {
                    return;
                }

                const first = focusable[0];
                const last = focusable[focusable.length - 1];

                if (event.shiftKey && document.activeElement === first) {
                    event.preventDefault();
                    last.focus();
                } else if (!event.shiftKey && document.activeElement === last) {
                    event.preventDefault();
                    first.focus();
                }
            });

            // Hide/show the presenter board, remembered between visits.
            const wheelLayout = document.querySelector('.wheel-layout');
            const toggleBoard = document.getElementById('toggleBoard');
            const toggleBoardLabel = document.getElementById('toggleBoardLabel');
            const BOARD_HIDDEN_KEY = 'spin-the-wheel.board-hidden';
            let boardHidden = false;

            try {
                boardHidden = window.localStorage.getItem(BOARD_HIDDEN_KEY) === '1';
            } catch (e) {
                // Storage can be unavailable in private modes.
            }

            function setBoardHidden(hidden) {
                wheelLayout.classList.toggle('is-board-hidden', hidden);
                toggleBoard.setAttribute('aria-pressed', hidden ? 'true' : 'false');
                toggleBoardLabel.textContent = hidden ? 'Show board' : 'Hide board';
            }

            setBoardHidden(boardHidden);

            toggleBoard.addEventListener('click', () => {
                boardHidden = !boardHidden;
                setBoardHidden(boardHidden);

                try {
                    window.localStorage.setItem(BOARD_HIDDEN_KEY, boardHidden ? '1' : '0');
                } catch (e) {
                    // Ignore storage failures.
                }
            });

            renderBoard(@json($last));
        })();
    </script>
</x-app-layout>
