<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>The Alphabet — The Great Debaters</title>
    <meta name="description" content="The English alphabet A to Z — The Great Debaters, FST Errachidia.">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Playfair+Display:ital,wght@0,500;0,700;1,500;1,700&display=swap" rel="stylesheet">

    <style>
        :root {
            --ink: #140d10;
            --ink-2: #1f1218;
            --burgundy: #7a1531;
            --burgundy-2: #9b1b3f;
            --gold: #d4a24a;
            --gold-2: #f2d9a4;
            --cream: #fbf7f0;
            --muted: rgba(251, 247, 240, .62);
            --line: rgba(251, 247, 240, .12);
            --ease: cubic-bezier(.22, .61, .36, 1);
            --display: 'Playfair Display', Georgia, serif;
            --body: 'Inter', system-ui, sans-serif;
        }

        * { box-sizing: border-box; }

        html, body { min-height: 100%; }

        body {
            margin: 0;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            background: var(--ink);
            color: var(--cream);
            font-family: var(--body);
            -webkit-font-smoothing: antialiased;
            overflow-x: hidden;
        }

        body::before {
            content: "";
            position: fixed;
            inset: 0;
            z-index: 0;
            pointer-events: none;
            background:
                radial-gradient(900px 620px at 10% 0%, rgba(155, 27, 63, .3), transparent 62%),
                radial-gradient(760px 560px at 92% 100%, rgba(212, 162, 74, .12), transparent 62%);
        }

        ::selection { background: var(--gold); color: var(--ink); }

        .grain {
            position: fixed;
            inset: 0;
            z-index: 100;
            pointer-events: none;
            opacity: .035;
            background-image: url("data:image/svg+xml,%3Csvg viewBox='0 0 200 200' xmlns='http://www.w3.org/2000/svg'%3E%3Cfilter id='n'%3E%3CfeTurbulence type='fractalNoise' baseFrequency='.8' numOctaves='3' stitchTiles='stitch'/%3E%3C/filter%3E%3Crect width='100%25' height='100%25' filter='url(%23n)'/%3E%3C/svg%3E");
        }

        /* ---------------------------------------------------------------
           Top bar
           --------------------------------------------------------------- */
        .topbar {
            position: relative;
            z-index: 2;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
            padding: 18px clamp(18px, 4vw, 48px);
            border-bottom: 1px solid var(--line);
        }

        .brand {
            display: flex;
            align-items: center;
            gap: 11px;
            text-decoration: none;
            color: var(--cream);
            font-size: .68rem;
            font-weight: 600;
            letter-spacing: .2em;
            text-transform: uppercase;
        }

        .brand img {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            object-fit: cover;
            border: 1px solid rgba(212, 162, 74, .45);
        }

        .topnav {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .topnav a {
            padding: 8px 13px;
            border: 1px solid rgba(251, 247, 240, .14);
            border-radius: 999px;
            color: rgba(251, 247, 240, .72);
            font-size: .66rem;
            font-weight: 600;
            letter-spacing: .12em;
            text-transform: uppercase;
            text-decoration: none;
            transition: color .2s var(--ease), border-color .2s var(--ease), background .2s var(--ease);
        }

        .topnav a:hover,
        .topnav a:focus-visible {
            color: var(--gold-2);
            border-color: rgba(212, 162, 74, .5);
            background: rgba(212, 162, 74, .1);
            outline: none;
        }

        /* ---------------------------------------------------------------
           Page & hero
           --------------------------------------------------------------- */
        .page {
            position: relative;
            z-index: 1;
            flex: 1;
            width: min(1180px, 100%);
            margin: 0 auto;
            padding: clamp(30px, 5vw, 62px) clamp(18px, 4vw, 48px) clamp(34px, 5vw, 56px);
        }

        .hero { text-align: center; }

        .kicker {
            margin: 0;
            font-size: clamp(.64rem, .9vw, .76rem);
            font-weight: 600;
            letter-spacing: .3em;
            text-transform: uppercase;
            color: var(--gold);
        }

        .hero h1 {
            margin: 14px 0 0;
            font-family: var(--display);
            font-size: clamp(2.6rem, 7vw, 5.4rem);
            font-weight: 500;
            line-height: .95;
            letter-spacing: -.02em;
        }

        .hero h1 em { color: var(--gold-2); font-style: italic; }

        .hero__sub {
            max-width: 38rem;
            margin: 18px auto 0;
            font-size: clamp(.9rem, 1.2vw, 1.05rem);
            line-height: 1.6;
            color: rgba(251, 247, 240, .68);
        }

        .hero__sub strong { color: var(--gold-2); font-weight: 600; }

        .hero__rule {
            width: min(220px, 34vw);
            height: 1px;
            margin: 26px auto 0;
            background: linear-gradient(90deg, transparent, var(--gold), transparent);
        }

        /* ---------------------------------------------------------------
           Alphabet grid
           --------------------------------------------------------------- */
        .letters {
            display: grid;
            grid-template-columns: repeat(7, minmax(0, 1fr));
            gap: clamp(9px, 1.1vw, 15px);
            margin-top: clamp(28px, 4vw, 46px);
        }

        .letter {
            position: relative;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            gap: 2px;
            aspect-ratio: 1;
            padding: 10px;
            border: 1px solid var(--line);
            border-radius: 6px;
            background: linear-gradient(145deg, rgba(255, 255, 255, .055), rgba(255, 255, 255, .012));
            color: var(--cream);
            font: inherit;
            cursor: pointer;
            overflow: hidden;
            transition:
                transform .3s var(--ease),
                border-color .3s var(--ease),
                background .3s var(--ease),
                box-shadow .3s var(--ease);
        }

        .letter::before {
            content: "";
            position: absolute;
            inset: 0;
            pointer-events: none;
            background: radial-gradient(120px 120px at 50% 0%, rgba(212, 162, 74, .16), transparent 70%);
            opacity: 0;
            transition: opacity .3s var(--ease);
        }

        .letter:hover,
        .letter:focus-visible {
            transform: translateY(-4px);
            border-color: rgba(212, 162, 74, .62);
            background: linear-gradient(145deg, rgba(212, 162, 74, .12), rgba(255, 255, 255, .02));
            box-shadow: 0 22px 40px -26px rgba(0, 0, 0, .9);
            outline: none;
        }

        .letter:hover::before,
        .letter:focus-visible::before { opacity: 1; }

        .letter.is-speaking {
            border-color: var(--gold);
            box-shadow: 0 0 0 3px rgba(212, 162, 74, .18), 0 22px 40px -26px rgba(0, 0, 0, .9);
        }

        .letter__num {
            position: absolute;
            top: 9px;
            left: 11px;
            font-size: .58rem;
            font-weight: 600;
            letter-spacing: .14em;
            color: rgba(251, 247, 240, .38);
            font-variant-numeric: tabular-nums;
        }

        .letter__upper {
            font-family: var(--display);
            font-size: clamp(1.9rem, 4.2vw, 3.4rem);
            font-weight: 500;
            line-height: 1;
        }

        .letter__lower {
            font-family: var(--display);
            font-style: italic;
            font-size: clamp(.9rem, 1.7vw, 1.35rem);
            line-height: 1;
            color: var(--gold-2);
            opacity: .8;
        }

        /* ---------------------------------------------------------------
           Footer
           --------------------------------------------------------------- */
        .foot {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            margin-top: clamp(32px, 4.5vw, 54px);
            padding-top: 20px;
            border-top: 1px solid var(--line);
            font-size: .66rem;
            letter-spacing: .16em;
            text-transform: uppercase;
            color: rgba(251, 247, 240, .42);
        }

        .foot strong {
            font-family: var(--display);
            font-size: .92rem;
            font-weight: 500;
            letter-spacing: .02em;
            text-transform: none;
            color: rgba(251, 247, 240, .78);
        }

        /* ---------------------------------------------------------------
           Responsive
           --------------------------------------------------------------- */
        @media (max-width: 1000px) {
            .letters { grid-template-columns: repeat(6, minmax(0, 1fr)); }
        }

        @media (max-width: 820px) {
            .letters { grid-template-columns: repeat(5, minmax(0, 1fr)); }
        }

        @media (max-width: 640px) {
            .letters { grid-template-columns: repeat(4, minmax(0, 1fr)); }
            .brand span { display: none; }
            .topnav a { padding: 7px 10px; font-size: .6rem; }
        }

        @media (max-width: 420px) {
            .letters { grid-template-columns: repeat(3, minmax(0, 1fr)); }
        }

        @media (prefers-reduced-motion: reduce) {
            *, *::before, *::after {
                animation-duration: .01ms !important;
                animation-iteration-count: 1 !important;
                transition-duration: .01ms !important;
            }
        }
    </style>
</head>

<body>
    <div class="grain" aria-hidden="true"></div>

    <header class="topbar">
        <a href="{{ route('landing') }}" class="brand">
            <img src="{{ asset('images/logo.jpg') }}" alt="The Great Debaters logo">
            <span>The Great Debaters</span>
        </a>

        <nav class="topnav" aria-label="Quick links">
            <a href="{{ route('presentation') }}">Presentation</a>
            <a href="{{ route('landing') }}">Home</a>
        </nav>
    </header>

    <main class="page">
        <section class="hero">
            <p class="kicker">A · B · C · D · E</p>
            <h1>The <em>Alphabet</em></h1>
            <p class="hero__sub">
                <strong>{{ $totalLetters }} letters</strong> from A to Z. Click any letter to hear it out loud.
            </p>
            <div class="hero__rule" aria-hidden="true"></div>
        </section>

        <section class="letters" id="letters" aria-label="The English alphabet">
            @foreach ($letters as $letter)
                <button type="button" class="letter" data-letter="{{ $letter }}"
                    aria-label="Letter {{ $letter }}, lowercase {{ strtolower($letter) }}">
                    <span class="letter__num" aria-hidden="true">{{ str_pad($loop->iteration, 2, '0', STR_PAD_LEFT) }}</span>
                    <span class="letter__upper" aria-hidden="true">{{ $letter }}</span>
                    <span class="letter__lower" aria-hidden="true">{{ strtolower($letter) }}</span>
                </button>
            @endforeach
        </section>

        <footer class="foot">
            <strong>The Great Debaters</strong>
            <span>Think. Speak. Change. · FST Errachidia</span>
        </footer>
    </main>

    <script>
        (() => {
            'use strict';

            const letters = Array.from(document.querySelectorAll('.letter'));
            const synth = window.speechSynthesis;
            const utteranceSupported = 'SpeechSynthesisUtterance' in window;

            let activeButton = null;
            let activeTimer = null;

            function highlight(button) {
                if (activeButton) {
                    activeButton.classList.remove('is-speaking');
                }

                activeButton = button;
                button.classList.add('is-speaking');

                window.clearTimeout(activeTimer);
                activeTimer = window.setTimeout(() => {
                    button.classList.remove('is-speaking');
                    if (activeButton === button) {
                        activeButton = null;
                    }
                }, 800);
            }

            function speak(button) {
                const letter = button.dataset.letter;

                highlight(button);

                if (!synth || !utteranceSupported) {
                    return;
                }

                synth.cancel();

                const utterance = new SpeechSynthesisUtterance(letter);
                utterance.lang = 'en-US';
                utterance.rate = 0.85;
                utterance.pitch = 1;

                synth.speak(utterance);
            }

            letters.forEach((button) => {
                button.addEventListener('click', () => speak(button));
            });

            // Escape clears any active highlight.
            document.addEventListener('keydown', (event) => {
                if (event.key === 'Escape' && activeButton) {
                    activeButton.classList.remove('is-speaking');
                    activeButton = null;
                    window.clearTimeout(activeTimer);

                    if (synth) {
                        synth.cancel();
                    }
                }
            });
        })();
    </script>
</body>

</html>
