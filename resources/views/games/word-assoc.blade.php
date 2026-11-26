<x-app-layout>
    <x-slot name="header">
        <div class="wa-header">
            <div>
                <h1 class="page-header-title">Word Association</h1>
                <p class="page-header-sub">
                    One figure, sixty seconds. Decode the picture into a word or phrase.
                </p>
            </div>
            <div class="wa-header-actions">
                <span class="chip chip--brand chip-lg" id="headerRemaining">
                    {{ $remaining }} of {{ $total }} left
                </span>
            </div>
        </div>
    </x-slot>

    <div class="content-wrap">
        <div class="wa-layout">

            {{-- Stage ---------------------------------------------------- --}}
            <section class="wa-stage card animate-fade-in-up">
                <div class="wa-stage-top">
                    <div>
                        <span class="wa-eyebrow" id="roundLabel">Ready when you are</span>
                        <p class="wa-round-copy" id="statusLine">Draw a figure and the one-minute clock starts.</p>
                    </div>

                    <div class="wa-timer" id="timer" aria-label="Time remaining">
                        <svg viewBox="0 0 120 120" aria-hidden="true">
                            <circle class="wa-timer-track" cx="60" cy="60" r="52"></circle>
                            <circle class="wa-timer-progress" id="timerProgress" cx="60" cy="60" r="52"></circle>
                        </svg>
                        <div class="wa-timer-text" aria-live="off">
                            <span class="wa-timer-value" id="timerValue">{{ $seconds }}</span>
                            <span class="wa-timer-unit">sec</span>
                        </div>
                    </div>
                </div>

                <div class="wa-figure-wrap">
                    <div class="wa-figure" id="figureText" aria-live="polite">
                        <span class="wa-placeholder">?</span>
                    </div>
                </div>

                <p class="wa-hintline" id="hintLine" hidden></p>

                <div class="wa-answer" id="answerPanel" hidden aria-live="polite">
                    <span class="wa-answer-label">The meaning of the figure</span>
                    <p class="wa-answer-word" id="answerWord">—</p>
                    <p class="wa-answer-explain" id="answerExplain"></p>
                </div>

                <div class="wa-actions">
                    <button type="button" class="btn btn-primary btn-lg" id="drawButton">
                        Draw a figure
                    </button>
                    <button type="button" class="btn btn-outline btn-lg" id="revealButton" disabled>
                        Reveal answer
                    </button>
                    <button type="button" class="btn btn-success btn-lg" id="nextButton" disabled>
                        Next figure
                    </button>
                </div>
            </section>

            {{-- Aside ---------------------------------------------------- --}}
            <aside class="wa-aside stack stack--sm animate-fade-in-up delay-100">
                <section class="card">
                    <div class="card-header">
                        <div>
                            <h3 class="card-title">How to play</h3>
                            <p class="card-subtitle">Sound it out, don't just read it</p>
                        </div>
                    </div>
                    <div class="card-body wa-how">
                        <ol>
                            <li>Look at the figure — the emoji replace a letter or a whole word.</li>
                            <li>Say the picture out loud. The answer hides in the sound.</li>
                            <li>You have 60 seconds. Reveal early if the room cracks it.</li>
                        </ol>
                        <p class="wa-example">
                            <strong>Example:</strong> F👀D → “food with eyes” → <em>see food</em> → <strong>seafood</strong>.
                        </p>
                    </div>
                </section>

                <section class="card">
                    <div class="card-header">
                        <div>
                            <h3 class="card-title">The deck</h3>
                            <p class="card-subtitle">Played figures are filled</p>
                        </div>
                        <span class="chip" id="remainingChip">{{ $remaining }} left</span>
                    </div>

                    <div class="card-body">
                        <div class="wa-deck" id="deck">
                            @for ($i = 1; $i <= $total; $i++)
                                <span class="wa-cell {{ in_array($i, $used, true) ? 'is-used' : '' }}"
                                    data-id="{{ $i }}">{{ $i }}</span>
                            @endfor
                        </div>
                    </div>

                    <div class="card-footer">
                        <span class="text-xs text-muted" id="usedCount">{{ count($used) }} played</span>
                        <button type="button" class="btn btn-outline btn-sm" id="resetButton"
                            @if (count($used) === 0 && ! $current) disabled @endif>
                            Reset deck
                        </button>
                    </div>
                </section>
            </aside>

        </div>
    </div>

    <style>
        .wa-header {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 16px;
            flex-wrap: wrap;
            width: 100%;
        }

        .wa-header-actions { display: flex; align-items: center; gap: 8px; flex-wrap: wrap; }

        .wa-layout {
            display: grid;
            grid-template-columns: 1fr;
            gap: 24px;
            align-items: start;
        }

        @media (min-width: 1024px) {
            .wa-layout { grid-template-columns: minmax(0, 1.5fr) minmax(0, 0.7fr); }
        }

        .wa-stage {
            display: flex;
            flex-direction: column;
            gap: 18px;
            padding: 24px;
            min-height: 520px;
        }

        .wa-stage-top {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
        }

        .wa-eyebrow {
            display: block;
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: .22em;
            color: var(--gray-500);
        }

        .wa-round-copy {
            margin-top: 6px;
            font-size: 13px;
            color: var(--gray-600);
            max-width: 34rem;
        }

        /* Countdown ring */
        .wa-timer {
            position: relative;
            width: 88px;
            height: 88px;
            flex-shrink: 0;
        }

        .wa-timer svg {
            width: 100%;
            height: 100%;
            transform: rotate(-90deg);
        }

        .wa-timer-track {
            fill: none;
            stroke: var(--gray-200);
            stroke-width: 7;
        }

        .wa-timer-progress {
            fill: none;
            stroke: var(--bordeaux-700);
            stroke-width: 7;
            stroke-linecap: round;
            transition: stroke-dashoffset 220ms linear, stroke 250ms var(--ease-standard);
        }

        .wa-timer.is-low .wa-timer-progress { stroke: var(--danger); }
        .wa-timer.is-low .wa-timer-value { color: var(--danger); }

        .wa-timer-text {
            position: absolute;
            inset: 0;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
        }

        .wa-timer-value {
            font-size: 25px;
            font-weight: 700;
            line-height: 1;
            color: var(--gray-900);
            font-variant-numeric: tabular-nums;
        }

        .wa-timer-unit {
            margin-top: 3px;
            font-size: 10px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: .16em;
            color: var(--gray-500);
        }

        /* Figure */
        .wa-figure-wrap {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 230px;
            padding: 28px 20px;
            border: 1px dashed var(--gray-300);
            border-radius: 18px;
            background:
                radial-gradient(circle at 50% 25%, var(--bordeaux-50), transparent 62%),
                var(--gray-50);
        }

        .wa-figure {
            font-family: 'Plus Jakarta Sans', 'Inter', system-ui, sans-serif;
            font-size: clamp(2.5rem, 8vw, 5rem);
            font-weight: 800;
            line-height: 1.12;
            letter-spacing: .05em;
            text-align: center;
            color: var(--bordeaux-800);
            word-break: break-word;
            animation: waPop 360ms var(--ease-decelerate) both;
        }

        .wa-figure .wa-placeholder { color: var(--gray-300); }

        @keyframes waPop {
            0% { transform: scale(.94); opacity: 0; }
            100% { transform: scale(1); opacity: 1; }
        }

        .wa-hintline {
            margin: -4px 0 0;
            padding: 10px 14px;
            border-radius: 10px;
            background: var(--warning-bg);
            color: var(--warning-fg);
            font-size: 13px;
            box-shadow: inset 0 0 0 1px var(--saffron-100);
        }

        .wa-answer {
            padding: 16px 18px;
            border-radius: 14px;
            background: var(--teal-50);
            box-shadow: inset 0 0 0 1px var(--teal-100);
            animation: waPop 320ms var(--ease-decelerate) both;
        }

        .wa-answer-label {
            display: block;
            font-size: 10px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: .2em;
            color: var(--teal-700);
        }

        .wa-answer-word {
            margin-top: 4px;
            font-size: 26px;
            font-weight: 700;
            line-height: 1.15;
            color: var(--gray-900);
        }

        .wa-answer-explain {
            margin-top: 6px;
            font-size: 13px;
            color: var(--gray-600);
        }

        .wa-actions {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
        }

        .wa-actions .btn { flex: 1 1 160px; justify-content: center; }

        /* Aside */
        .wa-how ol {
            margin: 0;
            padding-left: 20px;
            display: flex;
            flex-direction: column;
            gap: 8px;
            font-size: 13px;
            color: var(--gray-700);
        }

        .wa-example {
            margin-top: 14px;
            padding: 12px 14px;
            border-radius: 10px;
            background: var(--gray-50);
            box-shadow: inset 0 0 0 1px var(--gray-200);
            font-size: 12.5px;
            color: var(--gray-700);
        }

        .wa-deck {
            display: grid;
            grid-template-columns: repeat(10, 1fr);
            gap: 6px;
        }

        .wa-cell {
            aspect-ratio: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 5px;
            background: var(--gray-100);
            color: var(--gray-500);
            font-size: 11px;
            font-weight: 700;
            font-variant-numeric: tabular-nums;
            transition: background 160ms var(--ease-standard),
                        color 160ms var(--ease-standard),
                        box-shadow 160ms var(--ease-standard);
        }

        .wa-cell.is-used {
            background: var(--teal-100);
            color: var(--teal-700);
            box-shadow: inset 0 0 0 1px var(--teal-200);
            text-decoration: line-through;
        }

        .wa-cell.is-current {
            background: var(--bordeaux-700);
            color: #fff;
            box-shadow: 0 6px 14px -4px rgba(155, 27, 63, .55);
            text-decoration: none;
            animation: waCellPop 420ms var(--ease-decelerate) both;
        }

        @keyframes waCellPop {
            0% { transform: scale(.82); }
            60% { transform: scale(1.14); }
            100% { transform: scale(1.06); }
        }

        @media (max-width: 639px) {
            .wa-stage { padding: 18px; min-height: 0; }
            .wa-figure-wrap { min-height: 180px; }
            .wa-timer { width: 72px; height: 72px; }
            .wa-timer-value { font-size: 21px; }
            .wa-deck { grid-template-columns: repeat(6, 1fr); }
            .wa-actions .btn { flex: 1 1 100%; }
        }
    </style>

    <script>
        (() => {
            'use strict';

            const TOTAL = Number(@json($total));
            const SECONDS = Number(@json($seconds));
            const INITIAL = @json($current);
            const INITIAL_USED = @json($used);
            const INITIAL_REMAINING = Number(@json($remaining));

            const DRAW_URL = @json(route('games.word-assoc.draw'));
            const REVEAL_URL = @json(route('games.word-assoc.reveal'));
            const RESET_URL = @json(route('games.word-assoc.reset'));
            const CSRF = document.querySelector('meta[name="csrf-token"]').content;

            const figureText = document.getElementById('figureText');
            const answerPanel = document.getElementById('answerPanel');
            const answerWord = document.getElementById('answerWord');
            const answerExplain = document.getElementById('answerExplain');
            const hintLine = document.getElementById('hintLine');
            const drawButton = document.getElementById('drawButton');
            const revealButton = document.getElementById('revealButton');
            const nextButton = document.getElementById('nextButton');
            const resetButton = document.getElementById('resetButton');
            const timer = document.getElementById('timer');
            const timerProgress = document.getElementById('timerProgress');
            const timerValue = document.getElementById('timerValue');
            const deck = document.getElementById('deck');
            const remainingChip = document.getElementById('remainingChip');
            const headerRemaining = document.getElementById('headerRemaining');
            const usedCount = document.getElementById('usedCount');
            const roundLabel = document.getElementById('roundLabel');
            const statusLine = document.getElementById('statusLine');

            const CIRCUMFERENCE = 2 * Math.PI * 52;
            timerProgress.style.strokeDasharray = CIRCUMFERENCE.toFixed(2);
            timerProgress.style.strokeDashoffset = '0';

            let used = Array.isArray(INITIAL_USED) ? INITIAL_USED : [];
            let remaining = INITIAL_REMAINING;
            let current = INITIAL;
            let state = 'idle'; // idle | playing | revealed
            let remainingSeconds = SECONDS;
            let tickHandle = null;
            let revealing = false;
            let hintShown = false;

            function request(url, method) {
                return fetch(url, {
                    method,
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': CSRF,
                    },
                }).then(async (response) => {
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
                });
            }

            function toast(message, type) {
                if (window.TGD && typeof window.TGD.toast === 'function') {
                    window.TGD.toast(message, type);
                }
            }

            function setTimer(seconds) {
                remainingSeconds = seconds;

                timerValue.textContent = String(seconds);
                timer.classList.toggle('is-low', seconds <= 10);

                const progress = Math.min(1, Math.max(0, seconds / SECONDS));
                timerProgress.style.strokeDashoffset = (CIRCUMFERENCE * (1 - progress)).toFixed(2);

                if (state === 'playing' && seconds <= 20 && !hintShown && hintLine.textContent) {
                    hintShown = true;
                    hintLine.hidden = false;
                }
            }

            function stopTimer() {
                if (tickHandle !== null) {
                    clearInterval(tickHandle);
                    tickHandle = null;
                }
            }

            function runTimer(seconds) {
                stopTimer();

                const end = Date.now() + seconds * 1000;
                setTimer(seconds);

                tickHandle = setInterval(() => {
                    const left = Math.max(0, Math.ceil((end - Date.now()) / 1000));
                    setTimer(left);

                    if (left <= 0) {
                        stopTimer();
                        revealAnswer();
                    }
                }, 200);
            }

            function render() {
                const currentId = current ? Number(current.id) : null;

                deck.querySelectorAll('.wa-cell').forEach((cell) => {
                    const id = Number(cell.dataset.id);
                    cell.classList.toggle('is-used', used.includes(id));
                    cell.classList.toggle('is-current', state !== 'idle' && id === currentId);
                });

                remainingChip.textContent = remaining + ' left';
                headerRemaining.textContent = remaining + ' of ' + TOTAL + ' left';
                usedCount.textContent = used.length + ' played';

                drawButton.disabled = state !== 'idle' || remaining === 0;
                revealButton.disabled = state !== 'playing';
                nextButton.disabled = state !== 'revealed' || remaining === 0;
                resetButton.disabled = (used.length === 0 && ! current) || state === 'playing';
            }

            function setIdleFigure() {
                figureText.innerHTML = '<span class="wa-placeholder">?</span>';
            }

            function clearAnswer() {
                answerPanel.hidden = true;
                answerWord.textContent = '—';
                answerExplain.textContent = '';
                hintLine.hidden = true;
                hintLine.textContent = '';
                hintShown = false;
            }

            function showAnswer(data) {
                stopTimer();
                state = 'revealed';
                revealing = false;

                answerWord.textContent = data.answer;
                answerExplain.textContent = data.explanation;
                answerPanel.hidden = false;

                roundLabel.textContent = 'Answer revealed';
                statusLine.textContent = 'The meaning of the figure is on the board. Next figure when the room is ready.';

                render();
            }

            async function revealAnswer() {
                if (state !== 'playing' || revealing) {
                    return;
                }

                revealing = true;
                stopTimer();
                revealButton.disabled = true;
                statusLine.textContent = 'Revealing the meaning…';

                try {
                    const data = await request(REVEAL_URL, 'POST');
                    showAnswer(data);
                } catch (error) {
                    revealing = false;

                    if (remainingSeconds > 0) {
                        runTimer(remainingSeconds);
                    }

                    statusLine.textContent = 'Could not reveal the answer. Try again.';
                    toast(error.message, 'error');
                    render();
                }
            }

            async function draw() {
                if (state === 'playing' || remaining === 0) {
                    return;
                }

                drawButton.disabled = true;
                nextButton.disabled = true;
                revealButton.disabled = true;

                try {
                    const data = await request(DRAW_URL, 'POST');

                    used = Array.isArray(data.used) ? data.used : used;
                    remaining = Number(data.remaining);
                    current = { id: data.id, figure: data.figure };

                    clearAnswer();
                    figureText.textContent = data.figure;
                    hintLine.textContent = 'Hint: ' + data.hint;

                    state = 'playing';

                    roundLabel.textContent = 'Figure ' + data.id + ' of ' + TOTAL;
                    statusLine.textContent = 'One minute on the clock — decode the figure.';

                    render();
                    runTimer(Number(data.seconds) || SECONDS);
                } catch (error) {
                    if (error.status === 409) {
                        used = Array.isArray(error.data?.used) ? error.data.used : used;
                        remaining = 0;
                        state = 'idle';
                        current = null;
                        setIdleFigure();
                        clearAnswer();
                        roundLabel.textContent = 'Deck complete';
                        statusLine.textContent = 'Every figure has been played. Reset the deck for a new round.';
                    } else {
                        state = 'idle';
                        statusLine.textContent = 'Could not draw a figure. Try again.';
                    }

                    toast(error.message, 'error');
                    render();
                }
            }

            async function reset() {
                if (state === 'playing') {
                    return;
                }

                const confirmed = window.TGD && await window.TGD.confirm({
                    title: 'Reset the deck?',
                    message: 'All ' + TOTAL + ' figures go back into the deck and a new round starts.',
                    confirmLabel: 'Reset deck',
                    cancelLabel: 'Keep playing',
                    danger: true,
                });

                if (window.TGD && confirmed === false) {
                    return;
                }

                resetButton.disabled = true;

                try {
                    const data = await request(RESET_URL, 'DELETE');

                    stopTimer();
                    used = [];
                    remaining = TOTAL;
                    current = null;
                    state = 'idle';
                    revealing = false;

                    setIdleFigure();
                    clearAnswer();
                    setTimer(SECONDS);

                    roundLabel.textContent = 'Ready when you are';
                    statusLine.textContent = 'Draw a figure and the one-minute clock starts.';

                    render();
                    toast(data.message, 'success');
                } catch (error) {
                    toast(error.message, 'error');
                    render();
                }
            }

            drawButton.addEventListener('click', draw);
            revealButton.addEventListener('click', revealAnswer);
            nextButton.addEventListener('click', draw);
            resetButton.addEventListener('click', reset);

            // Restore a figure that was drawn before a page reload.
            if (current) {
                figureText.textContent = current.figure;
                state = 'playing';
                roundLabel.textContent = 'Figure ' + current.id + ' of ' + TOTAL;
                statusLine.textContent = 'One minute on the clock — decode the figure.';
                render();
                runTimer(SECONDS);
            } else {
                render();
            }
        })();
    </script>
</x-app-layout>
