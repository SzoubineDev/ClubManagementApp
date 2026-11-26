<!DOCTYPE html>
<html lang="en" dir="ltr">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>The Alphabet — The Great Debaters</title>
    <meta name="description" content="Switch between the English alphabet A–Z and the Arabic alphabet — The Great Debaters, FST Errachidia.">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Amiri:ital,wght@0,400;0,700;1,400&family=Inter:wght@400;500;600;700&family=Playfair+Display:ital,wght@0,500;0,700;1,500;1,700&display=swap" rel="stylesheet">

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
            --arabic: 'Amiri', 'Noto Naskh Arabic', 'Traditional Arabic', serif;
        }

        * { box-sizing: border-box; }

        [hidden] { display: none !important; }

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

        /* Arabic hero variant */
        [data-hero="ar"] .kicker {
            font-family: var(--arabic);
            font-size: clamp(1rem, 1.6vw, 1.25rem);
            font-weight: 700;
            letter-spacing: 0;
            text-transform: none;
        }

        [data-hero="ar"] h1 {
            font-family: var(--arabic);
            font-size: clamp(2.8rem, 7vw, 5.6rem);
            font-weight: 700;
            line-height: 1.15;
            letter-spacing: 0;
        }

        [data-hero="ar"] h1 em { font-style: normal; }

        [data-hero="ar"] .hero__sub {
            font-family: var(--arabic);
            font-size: clamp(1.05rem, 1.5vw, 1.28rem);
            line-height: 1.85;
        }

        /* ---------------------------------------------------------------
           Alphabet switch
           --------------------------------------------------------------- */
        .switch {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            margin-top: 22px;
            padding: 4px;
            border: 1px solid var(--line);
            border-radius: 999px;
            background: rgba(20, 13, 16, .5);
            backdrop-filter: blur(8px);
        }

        .switch button {
            padding: 8px 18px;
            border: 0;
            border-radius: 999px;
            background: transparent;
            color: rgba(251, 247, 240, .66);
            font-family: var(--body);
            font-size: .68rem;
            font-weight: 600;
            letter-spacing: .14em;
            text-transform: uppercase;
            cursor: pointer;
            transition: background .2s var(--ease), color .2s var(--ease);
        }

        .switch button[data-alphabet-target="ar"] {
            font-family: var(--arabic);
            font-size: .98rem;
            font-weight: 700;
            letter-spacing: 0;
            text-transform: none;
        }

        .switch button:hover:not(.is-active) { color: var(--gold-2); }

        .switch button.is-active {
            background: var(--gold);
            color: var(--ink);
        }

        .switch button:focus-visible {
            outline: 2px solid var(--gold-2);
            outline-offset: 2px;
        }

        /* ---------------------------------------------------------------
           Alphabet grids
           --------------------------------------------------------------- */
        .letters {
            display: grid;
            grid-template-columns: repeat(7, minmax(0, 1fr));
            gap: clamp(9px, 1.1vw, 15px);
            margin-top: clamp(28px, 4vw, 44px);
        }

        .letter {
            position: relative;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            gap: 1px;
            aspect-ratio: 1;
            padding: 12px 10px 10px;
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
            inset-inline-start: 11px;
            font-size: .58rem;
            font-weight: 600;
            letter-spacing: .12em;
            color: rgba(251, 247, 240, .38);
            font-variant-numeric: tabular-nums;
        }

        .letter__char {
            font-family: var(--arabic);
            font-size: clamp(2rem, 4.6vw, 3.5rem);
            font-weight: 700;
            line-height: 1.1;
        }

        .letter__name {
            font-family: var(--arabic);
            font-size: clamp(.86rem, 1.4vw, 1.08rem);
            line-height: 1.2;
            color: var(--gold-2);
        }

        .letter__latin {
            font-family: var(--body);
            font-size: .56rem;
            font-weight: 500;
            letter-spacing: .1em;
            text-transform: uppercase;
            color: rgba(251, 247, 240, .4);
        }

        /* English grid variant */
        .letters--en .letter__char {
            font-family: var(--display);
            font-size: clamp(1.9rem, 4.2vw, 3.4rem);
            font-weight: 500;
        }

        .letters--en .letter__name {
            font-family: var(--display);
            font-style: italic;
            font-size: clamp(.9rem, 1.7vw, 1.35rem);
            opacity: .85;
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
            margin-top: clamp(32px, 4.5vw, 52px);
            padding-top: 20px;
            border-top: 1px solid var(--line);
        }

        .foot strong {
            font-family: var(--display);
            font-size: .95rem;
            font-weight: 500;
            letter-spacing: .02em;
            color: rgba(251, 247, 240, .78);
        }

        .foot span {
            font-size: .66rem;
            letter-spacing: .16em;
            text-transform: uppercase;
            color: rgba(251, 247, 240, .46);
        }

        .foot span[data-footer="ar"] {
            font-family: var(--arabic);
            font-size: .9rem;
            letter-spacing: 0;
            text-transform: none;
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
            <div class="hero__lang" data-hero="en">
                <p class="kicker">A · B · C · D · E</p>
                <h1>The <em>Alphabet</em></h1>
                <p class="hero__sub">
                    <strong>{{ $englishTotal }} letters</strong> from A to Z. Click any letter to hear it.
                </p>
            </div>

            <div class="hero__lang" data-hero="ar" hidden>
                <p class="kicker">ا · ب · ت · ث · ج</p>
                <h1>الأبجدية <em>العربية</em></h1>
                <p class="hero__sub">
                    <strong>{{ $arabicTotal }} حرفاً</strong> من الألف إلى الياء. اضغط على أي حرف لسماعه.
                </p>
            </div>

            <div class="hero__rule" aria-hidden="true"></div>

            <div class="switch" role="group" aria-label="Choose alphabet">
                <button type="button" class="is-active" data-alphabet-target="en" aria-pressed="true">English</button>
                <button type="button" data-alphabet-target="ar" aria-pressed="false">العربية</button>
            </div>
        </section>

        <section class="letters letters--en" data-grid="en" dir="ltr" aria-label="English alphabet">
            @foreach ($englishLetters as $letter)
                <button type="button" class="letter" data-letter="{{ $letter['char'] }}"
                    data-speak="{{ $letter['char'] }}" data-speech-lang="en-US"
                    aria-label="Letter {{ $letter['char'] }}">
                    <span class="letter__num" aria-hidden="true">{{ str_pad($loop->iteration, 2, '0', STR_PAD_LEFT) }}</span>
                    <span class="letter__char" aria-hidden="true">{{ $letter['char'] }}</span>
                    <span class="letter__name" aria-hidden="true">{{ $letter['name'] }}</span>
                </button>
            @endforeach
        </section>

        <section class="letters letters--ar" data-grid="ar" dir="rtl" aria-label="الأبجدية العربية" hidden>
            @foreach ($arabicLetters as $letter)
                <button type="button" class="letter" data-letter="{{ $letter['char'] }}"
                    data-speak="{{ $letter['name'] }}" data-speech-lang="ar-SA"
                    aria-label="حرف {{ $letter['name'] }}">
                    <span class="letter__num" aria-hidden="true">{{ str_pad($loop->iteration, 2, '0', STR_PAD_LEFT) }}</span>
                    <span class="letter__char" aria-hidden="true">{{ $letter['char'] }}</span>
                    <span class="letter__name" aria-hidden="true">{{ $letter['name'] }}</span>
                    <span class="letter__latin" aria-hidden="true">{{ $letter['latin'] }}</span>
                </button>
            @endforeach
        </section>

        <footer class="foot">
            <strong>The Great Debaters</strong>
            <span data-footer="en">Think. Speak. Change. · FST Errachidia</span>
            <span data-footer="ar" hidden>فكر. تحدث. غيّر. · كلية العلوم والتقنيات بالراشيدية</span>
        </footer>
    </main>

    <script>
        (() => {
            'use strict';

            const root = document.documentElement;
            const synth = window.speechSynthesis;
            const utteranceSupported = 'SpeechSynthesisUtterance' in window;

            const heroes = Array.from(document.querySelectorAll('[data-hero]'));
            const grids = Array.from(document.querySelectorAll('[data-grid]'));
            const footers = Array.from(document.querySelectorAll('[data-footer]'));
            const switchButtons = Array.from(document.querySelectorAll('[data-alphabet-target]'));

            const titles = {
                en: 'The Alphabet — The Great Debaters',
                ar: 'الأبجدية العربية — The Great Debaters',
            };

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
                }, 900);
            }

            function stopSpeech() {
                if (activeButton) {
                    activeButton.classList.remove('is-speaking');
                    activeButton = null;
                }

                window.clearTimeout(activeTimer);

                if (synth) {
                    synth.cancel();
                }
            }

            function speak(button) {
                const text = button.dataset.speak || button.dataset.letter;
                const lang = button.dataset.speechLang || 'en-US';

                highlight(button);

                if (!synth || !utteranceSupported) {
                    return;
                }

                synth.cancel();

                const utterance = new SpeechSynthesisUtterance(text);
                utterance.lang = lang;
                utterance.rate = lang.toLowerCase().startsWith('ar') ? 0.8 : 0.85;
                utterance.pitch = 1;

                // Use a matching installed voice when one is available.
                const prefix = lang.slice(0, 2).toLowerCase();
                const voice = synth.getVoices().find((candidate) =>
                    candidate.lang.toLowerCase().startsWith(prefix)
                );

                if (voice) {
                    utterance.voice = voice;
                }

                synth.speak(utterance);
            }

            function setLanguage(lang, { persist = true } = {}) {
                const isArabic = lang === 'ar';
                const active = isArabic ? 'ar' : 'en';

                root.lang = active;
                root.dir = isArabic ? 'rtl' : 'ltr';
                document.title = titles[active];

                heroes.forEach((element) => {
                    element.hidden = element.dataset.hero !== active;
                });

                grids.forEach((element) => {
                    element.hidden = element.dataset.grid !== active;
                });

                footers.forEach((element) => {
                    element.hidden = element.dataset.footer !== active;
                });

                switchButtons.forEach((button) => {
                    const isActive = button.dataset.alphabetTarget === active;
                    button.classList.toggle('is-active', isActive);
                    button.setAttribute('aria-pressed', isActive ? 'true' : 'false');
                });

                stopSpeech();

                if (persist) {
                    try {
                        localStorage.setItem('alphabet-lang', active);
                    } catch (error) {
                        // Storage can be unavailable in private mode.
                    }

                    const url = new URL(window.location.href);
                    url.searchParams.set('lang', active);
                    window.history.replaceState(null, '', url);
                }
            }

            switchButtons.forEach((button) => {
                button.addEventListener('click', () => setLanguage(button.dataset.alphabetTarget));
            });

            document.querySelectorAll('.letter').forEach((button) => {
                button.addEventListener('click', () => speak(button));
            });

            document.addEventListener('keydown', (event) => {
                if (event.key === 'Escape') {
                    stopSpeech();
                }
            });

            // Restore the last choice, or use ?lang=ar / ?lang=en.
            let initial = 'en';

            try {
                const requested = new URLSearchParams(window.location.search).get('lang');
                const stored = localStorage.getItem('alphabet-lang');

                if (requested === 'ar' || requested === 'en') {
                    initial = requested;
                } else if (stored === 'ar' || stored === 'en') {
                    initial = stored;
                }
            } catch (error) {
                // Ignore unavailable storage.
            }

            setLanguage(initial, { persist: false });
        })();
    </script>
</body>

</html>
