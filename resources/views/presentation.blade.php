<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Journée d'Intégration — The Great Debaters</title>
    <meta name="description" content="The Great Debaters — Journée d'Intégration presentation.">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Amiri:wght@400;700&family=Inter:wght@400;500;600;700&family=Playfair+Display:ital,wght@0,500;0,700;1,500;1,700&display=swap" rel="stylesheet">

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
            --green: #006233;
            --red: #c1272d;
            --ease: cubic-bezier(.22, .61, .36, 1);
            --display: 'Playfair Display', Georgia, serif;
            --body: 'Inter', system-ui, sans-serif;
            --arabic: 'Amiri', 'Noto Naskh Arabic', serif;
        }

        * { box-sizing: border-box; }

        html, body { height: 100%; }

        body {
            margin: 0;
            overflow: hidden;
            background: var(--ink);
            color: var(--cream);
            font-family: var(--body);
            -webkit-font-smoothing: antialiased;
        }

        ::selection { background: var(--gold); color: var(--ink); }

        button { font: inherit; color: inherit; }

        .grain {
            position: fixed;
            inset: 0;
            z-index: 100;
            pointer-events: none;
            opacity: .035;
            background-image: url("data:image/svg+xml,%3Csvg viewBox='0 0 200 200' xmlns='http://www.w3.org/2000/svg'%3E%3Cfilter id='n'%3E%3CfeTurbulence type='fractalNoise' baseFrequency='.8' numOctaves='3' stitchTiles='stitch'/%3E%3C/filter%3E%3Crect width='100%25' height='100%25' filter='url(%23n)'/%3E%3C/svg%3E");
        }

        /* ---------------------------------------------------------------
           Deck & slides
           --------------------------------------------------------------- */
        .deck { position: fixed; inset: 0; }

        .slide {
            position: absolute;
            inset: 0;
            z-index: 0;
            display: flex;
            align-items: flex-start;
            justify-content: center;
            padding: clamp(28px, 5vw, 82px);
            opacity: 0;
            visibility: hidden;
            transform: translateY(30px) scale(.985);
            transition:
                opacity .75s var(--ease),
                transform .75s var(--ease),
                visibility 0s linear .75s;
            overflow-x: hidden;
            overflow-y: auto;
        }

        .slide.is-active {
            z-index: 2;
            opacity: 1;
            visibility: visible;
            transform: none;
            transition:
                opacity .75s var(--ease),
                transform .75s var(--ease),
                visibility 0s;
        }

        .slide.is-prev { transform: translateY(-30px) scale(.985); }

        .slide::before {
            content: "";
            position: absolute;
            inset: 0;
            pointer-events: none;
            background:
                radial-gradient(900px 600px at 12% 8%, rgba(155, 27, 63, .26), transparent 62%),
                radial-gradient(760px 540px at 88% 92%, rgba(212, 162, 74, .11), transparent 62%);
        }

        .slide__inner {
            position: relative;
            z-index: 1;
            width: min(1240px, 100%);
            margin: auto;
        }

        .kicker {
            margin: 0;
            font-size: clamp(.66rem, .9vw, .8rem);
            font-weight: 600;
            letter-spacing: .3em;
            text-transform: uppercase;
            color: var(--gold);
        }

        /* Reveal animation */
        [data-reveal] {
            opacity: 0;
            transform: translateY(22px);
            transition: opacity .8s var(--ease), transform .8s var(--ease);
        }

        .slide.is-active [data-reveal] { opacity: 1; transform: none; }

        [data-delay="1"] { transition-delay: .08s; }
        [data-delay="2"] { transition-delay: .16s; }
        [data-delay="3"] { transition-delay: .24s; }
        [data-delay="4"] { transition-delay: .32s; }
        [data-delay="5"] { transition-delay: .40s; }
        [data-delay="6"] { transition-delay: .48s; }

        /* ---------------------------------------------------------------
           Intro
           --------------------------------------------------------------- */
        .slide--intro .slide__inner { text-align: center; }

        .intro__crest {
            width: clamp(64px, 7vw, 92px);
            aspect-ratio: 1;
            margin: 0 auto 22px;
            border-radius: 50%;
            overflow: hidden;
            border: 1px solid rgba(212, 162, 74, .5);
            box-shadow: 0 0 0 8px rgba(212, 162, 74, .06);
        }

        .intro__crest img { width: 100%; height: 100%; object-fit: cover; }

        .intro__title {
            margin: 18px 0 0;
            font-family: var(--display);
            font-size: clamp(3rem, 8.5vw, 7.5rem);
            font-weight: 500;
            line-height: .92;
            letter-spacing: -.025em;
        }

        .intro__title em { color: var(--gold-2); font-style: italic; }

        .intro__welcome {
            margin: 20px 0 0;
            font-size: clamp(.95rem, 1.5vw, 1.25rem);
            letter-spacing: .05em;
        }

        .intro__rule {
            width: min(220px, 30vw);
            height: 1px;
            margin: 26px auto;
            background: linear-gradient(90deg, transparent, var(--gold), transparent);
        }

        .intro__brand {
            margin: 0;
            font-family: var(--display);
            font-size: clamp(1.15rem, 1.9vw, 1.55rem);
        }

        .intro__brand span {
            display: block;
            margin-top: 9px;
            font-family: var(--body);
            font-size: .7rem;
            letter-spacing: .32em;
            text-transform: uppercase;
            color: var(--muted);
        }

        .intro__meta {
            margin: 30px 0 0;
            font-size: .76rem;
            letter-spacing: .2em;
            text-transform: uppercase;
            color: var(--muted);
        }

        .intro__star {
            position: absolute;
            right: -4vw;
            bottom: -10vw;
            z-index: 0;
            font-size: 46vw;
            line-height: 1;
            color: rgba(212, 162, 74, .035);
            pointer-events: none;
        }

        /* ---------------------------------------------------------------
           National anthem
           --------------------------------------------------------------- */
        .slide--anthem {
            background:
                radial-gradient(circle at 50% 35%, #a51f2b 0%, #7d121c 55%, #470a11 100%);
        }

        .slide--anthem::before {
            background-image:
                radial-gradient(circle at 50% 50%, rgba(255, 255, 255, .07) 0 1.5px, transparent 2.5px),
                radial-gradient(circle at 0 0, rgba(255, 255, 255, .05) 0 1.5px, transparent 2.5px);
            background-size: 46px 46px, 46px 46px;
            background-position: 0 0, 23px 23px;
            opacity: .55;
        }

        .anthem {
            display: grid;
            grid-template-columns: minmax(200px, 290px) 1fr;
            gap: clamp(28px, 5vw, 64px);
            align-items: center;
        }

        .anthem__flag { text-align: center; }

        .flag {
            position: relative;
            aspect-ratio: 3 / 2;
            overflow: hidden;
            border-radius: 4px;
            background: var(--red);
            border: 1px solid rgba(255, 255, 255, .18);
            box-shadow: 0 34px 80px -32px rgba(0, 0, 0, .9);
        }

        .flag::after {
            content: "";
            position: absolute;
            inset: 0;
            background: linear-gradient(115deg, transparent 32%, rgba(255, 255, 255, .14) 50%, transparent 68%);
            transform: translateX(-120%);
            animation: flagShine 7s var(--ease) infinite;
        }

        @keyframes flagShine {
            0%, 55% { transform: translateX(-120%); }
            100% { transform: translateX(120%); }
        }

        .flag__star {
            position: absolute;
            inset: 0;
            width: 36%;
            height: 36%;
            margin: auto;
            fill: none;
            stroke: var(--green);
            stroke-width: 4;
            stroke-linejoin: round;
            filter: drop-shadow(0 2px 4px rgba(0, 0, 0, .25));
        }

        .anthem__flag-caption {
            margin: 14px 0 0;
            font-family: var(--arabic);
            font-size: 1rem;
            color: rgba(255, 255, 255, .72);
        }

        .anthem__copy { min-width: 0; }

        .anthem h2 {
            margin: 10px 0 0;
            font-family: var(--display);
            font-size: clamp(2rem, 4.4vw, 4rem);
            font-weight: 500;
            line-height: 1;
        }

        .anthem h2 em { color: var(--gold-2); font-style: italic; }

        .anthem__stand {
            margin: 12px 0 0;
            font-size: .74rem;
            letter-spacing: .18em;
            text-transform: uppercase;
            color: rgba(255, 255, 255, .72);
        }

        .anthem__columns {
            display: grid;
            grid-template-columns: 1.1fr .9fr;
            gap: clamp(20px, 3vw, 44px);
            margin-top: 22px;
            align-items: start;
        }

        .anthem__ar {
            font-family: var(--arabic);
            font-size: clamp(.84rem, 1.15vw, 1.04rem);
            line-height: 1.72;
            color: #fff;
            text-align: right;
        }

        .anthem__ar p,
        .anthem__fr p { margin: 0; }

        .anthem__fr {
            padding-left: 18px;
            border-left: 1px solid rgba(255, 255, 255, .2);
            font-size: clamp(.66rem, .86vw, .8rem);
            line-height: 1.55;
            color: rgba(255, 255, 255, .78);
        }

        .anthem__motto {
            margin: 18px 0 0;
            font-family: var(--arabic);
            font-size: clamp(.95rem, 1.4vw, 1.2rem);
            letter-spacing: .08em;
            color: var(--gold-2);
        }

        /* ---------------------------------------------------------------
           Bureau member
           --------------------------------------------------------------- */
        .member {
            display: grid;
            grid-template-columns: minmax(240px, 360px) 1fr;
            gap: clamp(28px, 6vw, 80px);
            align-items: center;
        }

        .member__ghost {
            position: absolute;
            right: -1%;
            bottom: -10%;
            z-index: 0;
            font-family: var(--display);
            font-size: clamp(9rem, 24vw, 22rem);
            line-height: 1;
            color: rgba(251, 247, 240, .03);
            pointer-events: none;
        }

        .member__photo-wrap {
            position: relative;
            width: 100%;
            max-width: 360px;
            aspect-ratio: 4 / 5;
            overflow: hidden;
            border-radius: 4px;
            background: var(--ink-2);
            box-shadow: 0 40px 80px -40px rgba(0, 0, 0, .9);
        }

        .member__photo-wrap::after {
            content: "";
            position: absolute;
            inset: 0;
            pointer-events: none;
            background: linear-gradient(180deg, transparent 52%, rgba(20, 13, 16, .55));
        }

        .member__photo {
            width: 100%;
            height: 100%;
            object-fit: cover;
            object-position: center top;
            filter: saturate(.92) contrast(1.04);
            transition: transform 1.2s var(--ease);
        }

        .slide.is-active .member__photo { transform: scale(1.035); }

        .member__initials {
            position: absolute;
            inset: 0;
            display: none;
            align-items: center;
            justify-content: center;
            font-family: var(--display);
            font-size: 5rem;
            color: rgba(251, 247, 240, .16);
        }

        .member__photo-wrap.is-missing .member__initials { display: flex; }

        .member__badge {
            position: absolute;
            top: 14px;
            left: 14px;
            z-index: 2;
            padding: 6px 10px;
            font-size: .62rem;
            font-weight: 600;
            letter-spacing: .16em;
            text-transform: uppercase;
            color: var(--gold-2);
            background: rgba(20, 13, 16, .58);
            border: 1px solid rgba(212, 162, 74, .4);
            border-radius: 2px;
            backdrop-filter: blur(6px);
        }

        .member__info { min-width: 0; }

        .member__name {
            margin: 12px 0 0;
            font-family: var(--display);
            font-size: clamp(2.5rem, 5.4vw, 5rem);
            font-weight: 500;
            line-height: .94;
            letter-spacing: -.02em;
        }

        .member__name em { color: var(--gold-2); font-style: italic; }

        .member__role {
            margin: 22px 0 0;
            font-size: clamp(.88rem, 1.25vw, 1.08rem);
            font-weight: 600;
            letter-spacing: .22em;
            text-transform: uppercase;
            color: var(--gold);
        }

        .member__dept {
            margin: 7px 0 0;
            font-size: .76rem;
            letter-spacing: .16em;
            text-transform: uppercase;
            color: var(--muted);
        }

        .member__line {
            max-width: 34rem;
            margin: 26px 0 0;
            font-size: clamp(.92rem, 1.25vw, 1.12rem);
            line-height: 1.6;
            color: rgba(251, 247, 240, .78);
        }

        /* ---------------------------------------------------------------
           Introduce yourself
           --------------------------------------------------------------- */
        .meet { text-align: center; }

        .meet h2 {
            margin: 12px 0 0;
            font-family: var(--display);
            font-size: clamp(2.4rem, 5.2vw, 4.8rem);
            font-weight: 500;
            line-height: 1;
        }

        .meet h2 em { color: var(--gold-2); font-style: italic; }

        .meet__sub {
            max-width: 44rem;
            margin: 18px auto 0;
            font-size: clamp(.9rem, 1.2vw, 1.05rem);
            line-height: 1.6;
            color: rgba(251, 247, 240, .7);
        }

        .meet__prompts {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 14px;
            max-width: 900px;
            margin: 34px auto 0;
            text-align: left;
        }

        .meet__prompt {
            padding: clamp(18px, 2.4vw, 28px);
            border: 1px solid var(--line);
            border-radius: 4px;
            background: linear-gradient(145deg, rgba(255, 255, 255, .05), rgba(255, 255, 255, .015));
        }

        .meet__prompt span {
            font-size: .68rem;
            font-weight: 700;
            letter-spacing: .18em;
            color: var(--gold);
        }

        .meet__prompt h3 {
            margin: 12px 0 6px;
            font-family: var(--display);
            font-size: clamp(1.2rem, 1.8vw, 1.6rem);
            font-weight: 500;
        }

        .meet__prompt p {
            margin: 0;
            font-size: .84rem;
            line-height: 1.5;
            color: rgba(251, 247, 240, .62);
        }

        .meet__cta {
            margin: 30px 0 0;
            font-family: var(--display);
            font-size: clamp(1.3rem, 2.2vw, 2rem);
            font-style: italic;
            color: var(--gold-2);
        }

        /* ---------------------------------------------------------------
           Games
           --------------------------------------------------------------- */
        .games { text-align: center; }

        .games h2 {
            margin: 10px 0 0;
            font-family: var(--display);
            font-size: clamp(3rem, 7vw, 6.5rem);
            font-weight: 500;
            line-height: .95;
        }

        .games h2 em { color: var(--gold-2); font-style: italic; }

        .games__sub {
            max-width: 40rem;
            margin: 18px auto 0;
            font-size: clamp(.9rem, 1.2vw, 1.05rem);
            line-height: 1.6;
            color: rgba(251, 247, 240, .7);
        }

        .games__cards {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 16px;
            max-width: 960px;
            margin: 32px auto 0;
            text-align: left;
        }

        .game-card {
            padding: clamp(20px, 3vw, 34px);
            border: 1px solid rgba(212, 162, 74, .28);
            border-radius: 5px;
            background: linear-gradient(145deg, rgba(255, 255, 255, .055), rgba(255, 255, 255, .015));
            transition: border-color .3s var(--ease), transform .3s var(--ease);
        }

        .game-card:hover { border-color: rgba(212, 162, 74, .6); transform: translateY(-3px); }

        .game-card__icon { font-size: clamp(2rem, 3vw, 3rem); line-height: 1; }

        .game-card h3 {
            margin: 16px 0 8px;
            font-family: var(--display);
            font-size: clamp(1.4rem, 2.2vw, 2rem);
            font-weight: 500;
        }

        .game-card p {
            margin: 0;
            font-size: .9rem;
            line-height: 1.6;
            color: rgba(251, 247, 240, .66);
        }

        .games__cta {
            margin: 28px 0 0;
            font-family: var(--display);
            font-size: clamp(1.4rem, 2.4vw, 2.2rem);
            font-style: italic;
            color: var(--gold-2);
        }

        /* ---------------------------------------------------------------
           Outro
           --------------------------------------------------------------- */
        .outro { text-align: center; }

        .outro h2 {
            margin: 14px 0 0;
            font-family: var(--display);
            font-size: clamp(2.6rem, 6vw, 6rem);
            font-weight: 500;
            line-height: .98;
            letter-spacing: -.02em;
        }

        .outro h2 em { color: var(--gold-2); font-style: italic; }

        .outro__love {
            margin: 22px 0 0;
            font-family: var(--display);
            font-size: clamp(1.6rem, 3vw, 2.6rem);
            font-style: italic;
            color: var(--gold-2);
        }

        .outro__next {
            max-width: 40rem;
            margin: 14px auto 0;
            font-size: clamp(.95rem, 1.3vw, 1.15rem);
            line-height: 1.6;
            color: rgba(251, 247, 240, .75);
        }

        .outro__brand {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 8px;
            margin-top: 38px;
        }

        .outro__brand img {
            width: 46px;
            height: 46px;
            border-radius: 50%;
            object-fit: cover;
            border: 1px solid rgba(212, 162, 74, .45);
        }

        .outro__brand span {
            font-family: var(--display);
            font-size: 1.1rem;
        }

        .outro__brand small {
            font-size: .66rem;
            letter-spacing: .3em;
            text-transform: uppercase;
            color: var(--muted);
        }

        /* ---------------------------------------------------------------
           HUD, progress, hint
           --------------------------------------------------------------- */
        .hud {
            position: fixed;
            left: 0;
            right: 0;
            z-index: 50;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
            padding: 18px clamp(18px, 3vw, 36px);
            pointer-events: none;
            transition: opacity .4s var(--ease);
        }

        .hud--top { top: 0; }
        .hud--bottom { bottom: 0; padding-bottom: 24px; }

        .hud__brand {
            display: flex;
            align-items: center;
            gap: 10px;
            pointer-events: auto;
            font-size: .68rem;
            font-weight: 600;
            letter-spacing: .2em;
            text-transform: uppercase;
            color: rgba(251, 247, 240, .58);
        }

        .hud__brand img {
            width: 30px;
            height: 30px;
            border-radius: 50%;
            object-fit: cover;
            border: 1px solid rgba(212, 162, 74, .42);
        }

        .hud__actions {
            display: flex;
            align-items: center;
            gap: 8px;
            pointer-events: auto;
        }

        .hud__btn {
            padding: 8px 12px;
            border: 1px solid rgba(251, 247, 240, .14);
            border-radius: 999px;
            background: rgba(20, 13, 16, .35);
            color: rgba(251, 247, 240, .7);
            font-size: .66rem;
            font-weight: 600;
            letter-spacing: .12em;
            text-transform: uppercase;
            cursor: pointer;
            backdrop-filter: blur(8px);
            transition: background .2s var(--ease), border-color .2s var(--ease), color .2s var(--ease);
        }

        .hud__btn:hover {
            color: var(--gold-2);
            border-color: rgba(212, 162, 74, .5);
            background: rgba(212, 162, 74, .1);
        }

        .hud__counter {
            margin-left: 4px;
            font-size: .72rem;
            letter-spacing: .16em;
            color: rgba(251, 247, 240, .5);
            font-variant-numeric: tabular-nums;
        }

        .hud__counter #currentNum { color: var(--gold-2); }

        .hud__sep { margin: 0 5px; opacity: .45; }

        .hud__controls {
            display: flex;
            gap: 8px;
            pointer-events: auto;
        }

        .hud__controls button {
            width: 42px;
            height: 42px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border: 1px solid rgba(251, 247, 240, .16);
            border-radius: 50%;
            background: rgba(20, 13, 16, .4);
            color: var(--cream);
            font-size: 1rem;
            cursor: pointer;
            backdrop-filter: blur(8px);
            transition: background .2s var(--ease), border-color .2s var(--ease), opacity .2s var(--ease);
        }

        .hud__controls button:hover:not(:disabled) {
            background: rgba(212, 162, 74, .16);
            border-color: rgba(212, 162, 74, .5);
        }

        .hud__controls button:disabled { opacity: .25; cursor: default; }

        .progress {
            position: fixed;
            left: 0;
            right: 0;
            bottom: 0;
            z-index: 60;
            height: 3px;
            background: rgba(251, 247, 240, .08);
        }

        .progress__bar {
            display: block;
            width: 0;
            height: 100%;
            background: linear-gradient(90deg, var(--burgundy-2), var(--gold));
            transition: width .5s var(--ease);
        }

        .hint {
            position: fixed;
            left: 50%;
            bottom: 30px;
            z-index: 55;
            transform: translateX(-50%);
            padding: 9px 15px;
            border: 1px solid rgba(251, 247, 240, .12);
            border-radius: 999px;
            background: rgba(20, 13, 16, .55);
            color: rgba(251, 247, 240, .62);
            font-size: .68rem;
            letter-spacing: .06em;
            backdrop-filter: blur(8px);
            pointer-events: none;
            transition: opacity .6s var(--ease), transform .6s var(--ease);
        }

        .hint.is-hidden { opacity: 0; transform: translate(-50%, 10px); }

        .hint kbd {
            padding: 1px 5px;
            border: 1px solid rgba(251, 247, 240, .2);
            border-radius: 3px;
            font-family: var(--body);
            font-size: .64rem;
            color: var(--gold-2);
        }

        body.is-hud-hidden .hud,
        body.is-hud-hidden .hint { opacity: 0; pointer-events: none; }

        /* Edge click zones */
        .nav-zone {
            position: fixed;
            top: 0;
            bottom: 0;
            z-index: 30;
            width: 8vw;
            border: 0;
            background: transparent;
            cursor: pointer;
        }

        .nav-zone--prev { left: 0; }
        .nav-zone--next { right: 0; }

        .nav-zone--prev:hover { background: linear-gradient(90deg, rgba(212, 162, 74, .06), transparent); }
        .nav-zone--next:hover { background: linear-gradient(270deg, rgba(212, 162, 74, .06), transparent); }

        @media (hover: none) { .nav-zone { display: none; } }

        /* ---------------------------------------------------------------
           Overview
           --------------------------------------------------------------- */
        .overview {
            position: fixed;
            inset: 0;
            z-index: 80;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: clamp(18px, 4vw, 48px);
            background: rgba(10, 6, 8, .86);
            backdrop-filter: blur(18px);
            opacity: 0;
            visibility: hidden;
            transition: opacity .35s var(--ease), visibility .35s;
        }

        .overview.is-open { opacity: 1; visibility: visible; }

        .overview__panel {
            width: min(1100px, 100%);
            max-height: 88vh;
            overflow: auto;
        }

        .overview__head {
            display: flex;
            align-items: flex-end;
            justify-content: space-between;
            gap: 20px;
            margin-bottom: 22px;
        }

        .overview__head h2 {
            margin: 4px 0 0;
            font-family: var(--display);
            font-size: clamp(1.8rem, 3vw, 2.6rem);
            font-weight: 500;
        }

        .overview__close {
            padding: 9px 14px;
            border: 1px solid rgba(251, 247, 240, .16);
            border-radius: 999px;
            background: transparent;
            color: rgba(251, 247, 240, .7);
            font-size: .68rem;
            letter-spacing: .12em;
            text-transform: uppercase;
            cursor: pointer;
        }

        .overview__close:hover { border-color: var(--gold); color: var(--gold-2); }

        .overview__grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(170px, 1fr));
            gap: 10px;
        }

        .overview__item {
            display: flex;
            flex-direction: column;
            gap: 9px;
            padding: 14px;
            text-align: left;
            border: 1px solid rgba(251, 247, 240, .12);
            border-radius: 4px;
            background: rgba(255, 255, 255, .03);
            cursor: pointer;
            transition: background .2s var(--ease), border-color .2s var(--ease);
        }

        .overview__item:hover { border-color: rgba(212, 162, 74, .5); background: rgba(212, 162, 74, .08); }

        .overview__item.is-current { border-color: var(--gold); background: rgba(212, 162, 74, .12); }

        .overview__num {
            font-size: .66rem;
            letter-spacing: .18em;
            color: var(--gold);
        }

        .overview__title {
            font-family: var(--display);
            font-size: 1.02rem;
            line-height: 1.2;
        }

        .overview__kind {
            font-size: .6rem;
            letter-spacing: .14em;
            text-transform: uppercase;
            color: rgba(251, 247, 240, .42);
        }

        /* ---------------------------------------------------------------
           Responsive
           --------------------------------------------------------------- */
        @media (max-width: 900px) {
            .anthem { grid-template-columns: 1fr; gap: 26px; }
            .anthem__flag { max-width: 240px; margin: 0 auto; }
            .anthem__columns { grid-template-columns: 1fr; }
            .anthem__fr {
                padding-top: 14px;
                padding-left: 0;
                border-top: 1px solid rgba(255, 255, 255, .2);
                border-left: 0;
            }

            .member { grid-template-columns: 1fr; gap: 24px; text-align: center; }
            .member__photo-wrap { max-width: 220px; margin: 0 auto; }
            .member__line { margin-left: auto; margin-right: auto; }

            .meet__prompts { grid-template-columns: 1fr; }
            .games__cards { grid-template-columns: 1fr; }
        }

        @media (max-width: 640px) {
            .slide { padding: 26px 18px 84px; }
            .hud__brand span { display: none; }
            .hud__btn { padding: 7px 10px; font-size: .6rem; }
            .hint { display: none; }
            .intro__title { font-size: clamp(2.6rem, 13vw, 4rem); }
            .member__name { font-size: clamp(2rem, 9vw, 2.8rem); }
        }

        @media (max-height: 820px) {
            .slide--anthem .anthem__ar { font-size: .76rem; line-height: 1.5; }
            .slide--anthem .anthem__fr { font-size: .62rem; line-height: 1.4; }
            .slide--anthem .anthem h2 { font-size: clamp(1.8rem, 3.4vw, 2.8rem); }
            .slide--anthem .anthem__columns { margin-top: 12px; }
            .slide--anthem .anthem__stand { margin-top: 8px; }
            .slide--anthem .anthem__motto { margin-top: 10px; }
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

    <main class="deck" id="deck">

        {{-- 01 · Introduction ------------------------------------------------ --}}
        <section class="slide slide--intro is-active" data-slide="1" data-title="Introduction" data-kind="Intro" aria-hidden="false">
            <span class="intro__star" aria-hidden="true">✦</span>
            <div class="slide__inner intro">
                <div class="intro__crest" data-reveal>
                    <img src="{{ asset('images/logo.jpg') }}" alt="The Great Debaters logo">
                </div>
                <p class="kicker" data-reveal data-delay="1">FST Errachidia · Université Moulay Ismaïl</p>
                <h1 class="intro__title" data-reveal data-delay="2">Journée<br><em>d'Intégration</em></h1>
                <p class="intro__welcome" data-reveal data-delay="3">Bienvenue · مرحباً · Azul · Welcome</p>
                <div class="intro__rule" data-reveal data-delay="4"></div>
                <p class="intro__brand" data-reveal data-delay="5">
                    The Great Debaters
                    <span>Think. Speak. Change.</span>
                </p>
                <p class="intro__meta" data-reveal data-delay="6">2026 / 2027 Season · L3</p>
            </div>
        </section>

        {{-- 02 · National anthem ------------------------------------------- --}}
        <section class="slide slide--anthem" data-slide="2" data-title="Hymne Chérifien" data-kind="Anthem" aria-hidden="true">
            <div class="slide__inner anthem">
                <div class="anthem__flag" data-reveal>
                    <div class="flag">
                        <svg class="flag__star" viewBox="0 0 100 100" aria-hidden="true">
                            <polygon points="50,8 61,38 93,38 67,57 77,89 50,70 23,89 33,57 7,38 39,38" />
                        </svg>
                    </div>
                    <p class="anthem__flag-caption">المملكة المغربية</p>
                </div>

                <div class="anthem__copy">
                    <p class="kicker" data-reveal data-delay="1">Hymne National · النشيد الوطني</p>
                    <h2 data-reveal data-delay="2">Hymne <em>Chérifien</em></h2>
                    <p class="anthem__stand" data-reveal data-delay="3">Debout pour l'hymne national · Please stand</p>

                    <div class="anthem__columns">
                        <div class="anthem__ar" dir="rtl" lang="ar" data-reveal data-delay="4">
                            <p>مَنْبِتَ الأَحْرَارِ</p>
                            <p>مَشْرِقَ الأَنْوَارِ</p>
                            <p>مُنْتَدَى السُّؤْدَدِ وَحِمَاهُ</p>
                            <p>دُمْتَ مُنْتَدَاهُ وَحِمَاهُ</p>
                            <p>عِشْتَ فِي الأَوْطَانِ</p>
                            <p>لِلْعُلَا عُنْوَانِ</p>
                            <p>مِلْءَ كُلِّ جَنَانِ</p>
                            <p>ذِكْرَى كُلِّ لِسَانِ</p>
                            <p>بِالرُّوحِ</p>
                            <p>بِالجَسَدِ</p>
                            <p>هَبَّ فَتَاكَ</p>
                            <p>لَبَّى نِدَاكَ</p>
                            <p>فِي فَمِي وَفِي دَمِي</p>
                            <p>هَوَاكَ ثَارَ نُورٌ وَنَارْ</p>
                            <p>إِخْوَتِي هَيَّا</p>
                            <p>لِلْعُلَا سَعْيَا</p>
                            <p>نُشْهِدِ الدُّنْيَا</p>
                            <p>أَنَّا هُنَا نَحْيَا</p>
                            <p>بِشِعَارِ</p>
                            <p>اللهُ الوَطَنُ المَلِكُ</p>
                        </div>

                        <div class="anthem__fr" lang="fr" data-reveal data-delay="5">
                            <p>Berceau des hommes libres,</p>
                            <p>Source des lumières,</p>
                            <p>Terre de souveraineté et de paix,</p>
                            <p>Tu es à la fois son forum et son rempart.</p>
                            <p>Tu as vécu parmi les nations,</p>
                            <p>Comme un symbole de grandeur,</p>
                            <p>Remplissant tous les cœurs,</p>
                            <p>Et présent sur toutes les lèvres.</p>
                            <p>Par l'âme,</p>
                            <p>Par le corps,</p>
                            <p>Ton fils s'est levé,</p>
                            <p>Répondant à ton appel.</p>
                            <p>Dans ma bouche et dans mon sang,</p>
                            <p>Ton amour a fait jaillir lumière et feu.</p>
                            <p>Frères, allons</p>
                            <p>Vers la gloire,</p>
                            <p>Proclamons au monde</p>
                            <p>Que c'est ici que nous vivons</p>
                            <p>Sous la devise :</p>
                            <p>Dieu, la Patrie, le Roi.</p>
                        </div>
                    </div>

                    <p class="anthem__motto" data-reveal data-delay="6">الله · الوطن · الملك</p>
                </div>
            </div>
        </section>

        {{-- 03–10 · Bureau members ----------------------------------------- --}}
        @foreach ($members as $member)
            @php
                $nameParts = explode(' ', $member['name']);
                $lastName = array_pop($nameParts);
                $firstName = implode(' ', $nameParts);
                $memberNumber = str_pad($loop->iteration, 2, '0', STR_PAD_LEFT);
            @endphp

            <section class="slide slide--member" data-slide="{{ $loop->iteration + 2 }}"
                data-title="{{ $member['name'] }}" data-kind="Le Bureau" aria-hidden="true">
                <span class="member__ghost" aria-hidden="true">{{ $memberNumber }}</span>

                <div class="slide__inner member">
                    <div class="member__photo-wrap" data-reveal>
                        <span class="member__badge">{{ $member['dept'] }}</span>
                        <img class="member__photo" src="{{ asset($member['photo']) }}"
                            alt="{{ $member['name'] }} — {{ $member['role'] }}" loading="eager"
                            onerror="this.style.display='none'; this.parentElement.classList.add('is-missing');">
                        <span class="member__initials" aria-hidden="true">{{ $member['initials'] }}</span>
                    </div>

                    <div class="member__info">
                        <p class="kicker" data-reveal data-delay="1">
                            Le Bureau · {{ $memberNumber }} / {{ str_pad(count($members), 2, '0', STR_PAD_LEFT) }}
                        </p>
                        <h2 class="member__name" data-reveal data-delay="2">
                            {{ $firstName }}<br><em>{{ $lastName }}</em>
                        </h2>
                        <p class="member__role" data-reveal data-delay="3">{{ $member['role'] }}</p>
                        <p class="member__dept" data-reveal data-delay="4">{{ $member['dept'] }}</p>
                        <p class="member__line" data-reveal data-delay="5">{{ $member['line'] }}</p>
                    </div>
                </div>
            </section>
        @endforeach

        {{-- 11 · Members introduce themselves ------------------------------ --}}
        <section class="slide slide--meet" data-slide="{{ count($members) + 3 }}"
            data-title="Members introduce themselves" data-kind="Icebreaker" aria-hidden="true">
            <div class="slide__inner meet">
                <p class="kicker" data-reveal>À vous la parole · Your turn</p>
                <h2 data-reveal data-delay="1">Introduce <em>yourself.</em></h2>
                <p class="meet__sub" data-reveal data-delay="2">
                    One minute each. Tell us who you are and what you want to bring to the club this year.
                </p>

                <div class="meet__prompts">
                    <div class="meet__prompt" data-reveal data-delay="3">
                        <span>01</span>
                        <h3>Your name</h3>
                        <p>And where you come from.</p>
                    </div>
                    <div class="meet__prompt" data-reveal data-delay="4">
                        <span>02</span>
                        <h3>Your section</h3>
                        <p>English, French, or Arabic.</p>
                    </div>
                    <div class="meet__prompt" data-reveal data-delay="5">
                        <span>03</span>
                        <h3>Your goal</h3>
                        <p>One thing you want to achieve this year.</p>
                    </div>
                </div>

                <p class="meet__cta" data-reveal data-delay="6">The floor is yours.</p>
            </div>
        </section>

        {{-- 12 · Games time ------------------------------------------------ --}}
        <section class="slide slide--games" data-slide="{{ count($members) + 4 }}"
            data-title="Games Time" data-kind="Games" aria-hidden="true">
            <div class="slide__inner games">
                <p class="kicker" data-reveal>Interlude</p>
                <h2 data-reveal data-delay="1">Games <em>Time</em></h2>
                <p class="games__sub" data-reveal data-delay="2">
                    Two quick games to break the ice and get everyone talking.
                </p>

                <div class="games__cards">
                    <article class="game-card" data-reveal data-delay="3">
                        <span class="game-card__icon" aria-hidden="true">🎡</span>
                        <h3>Spin the Wheel</h3>
                        <p>Pick the next presenter. Every number comes up once, so everyone gets a turn.</p>
                    </article>
                    <article class="game-card" data-reveal data-delay="4">
                        <span class="game-card__icon" aria-hidden="true">🧠</span>
                        <h3>Word Association</h3>
                        <p>One figure, sixty seconds. Decode the picture and shout the answer.</p>
                    </article>
                </div>

                <p class="games__cta" data-reveal data-delay="5">Let's play.</p>
            </div>
        </section>

        {{-- 13 · Outro ----------------------------------------------------- --}}
        <section class="slide slide--outro" data-slide="{{ count($members) + 5 }}"
            data-title="Thank you" data-kind="Outro" aria-hidden="true">
            <div class="slide__inner outro">
                <p class="kicker" data-reveal>Merci · شكراً · Thank you</p>
                <h2 data-reveal data-delay="1">Thank you guys<br><em>for your presence.</em></h2>
                <p class="outro__love" data-reveal data-delay="2">We love you.</p>
                <p class="outro__next" data-reveal data-delay="3">
                    Come next time — we're gonna have lots of fun.
                </p>
                <div class="outro__brand" data-reveal data-delay="4">
                    <img src="{{ asset('images/logo.jpg') }}" alt="">
                    <span>The Great Debaters</span>
                    <small>Think. Speak. Change.</small>
                </div>
            </div>
        </section>

    </main>

    {{-- Edge navigation zones ------------------------------------------------ --}}
    <button type="button" class="nav-zone nav-zone--prev" id="zonePrev" aria-label="Previous slide"></button>
    <button type="button" class="nav-zone nav-zone--next" id="zoneNext" aria-label="Next slide"></button>

    {{-- HUD ---------------------------------------------------------------- --}}
    <header class="hud hud--top">
        <div class="hud__brand">
            <img src="{{ asset('images/logo.jpg') }}" alt="">
            <span>The Great Debaters</span>
        </div>

        <div class="hud__actions">
            <button type="button" class="hud__btn" id="overviewBtn">Overview</button>
            <button type="button" class="hud__btn" id="fullscreenBtn">Fullscreen</button>
            <span class="hud__counter">
                <span id="currentNum">01</span><span class="hud__sep">/</span><span>{{ str_pad($totalSlides, 2, '0', STR_PAD_LEFT) }}</span>
            </span>
        </div>
    </header>

    <footer class="hud hud--bottom">
        <span></span>
        <div class="hud__controls">
            <button type="button" id="prevBtn" aria-label="Previous slide">←</button>
            <button type="button" id="nextBtn" aria-label="Next slide">→</button>
        </div>
    </footer>

    <div class="progress" aria-hidden="true">
        <span class="progress__bar" id="progressBar"></span>
    </div>

    <div class="hint" id="hint">
        <kbd>←</kbd> <kbd>→</kbd> navigate · <kbd>F</kbd> fullscreen · <kbd>O</kbd> overview · <kbd>H</kbd> hide UI
    </div>

    {{-- Overview ----------------------------------------------------------- --}}
    <div class="overview" id="overview" aria-hidden="true">
        <div class="overview__panel" role="dialog" aria-modal="true" aria-label="Slide overview">
            <div class="overview__head">
                <div>
                    <p class="kicker">The Great Debaters</p>
                    <h2>Slide overview</h2>
                </div>
                <button type="button" class="overview__close" id="closeOverviewBtn">Close</button>
            </div>
            <div class="overview__grid" id="overviewGrid"></div>
        </div>
    </div>

    <script>
        (() => {
            'use strict';

            const slides = Array.from(document.querySelectorAll('.slide'));
            const total = slides.length;

            const currentNum = document.getElementById('currentNum');
            const progressBar = document.getElementById('progressBar');
            const prevBtn = document.getElementById('prevBtn');
            const nextBtn = document.getElementById('nextBtn');
            const zonePrev = document.getElementById('zonePrev');
            const zoneNext = document.getElementById('zoneNext');
            const fullscreenBtn = document.getElementById('fullscreenBtn');
            const overviewBtn = document.getElementById('overviewBtn');
            const overview = document.getElementById('overview');
            const overviewGrid = document.getElementById('overviewGrid');
            const closeOverviewBtn = document.getElementById('closeOverviewBtn');
            const hint = document.getElementById('hint');

            let index = 0;
            let overviewOpen = false;
            let hintTimer = null;

            const pad = (value) => String(value).padStart(2, '0');

            function updateHud() {
                currentNum.textContent = pad(index + 1);
                progressBar.style.width = ((index + 1) / total * 100) + '%';
                prevBtn.disabled = index === 0;
                nextBtn.disabled = index === total - 1;

                overviewGrid.querySelectorAll('.overview__item').forEach((item, i) => {
                    item.classList.toggle('is-current', i === index);
                });
            }

            function goTo(target, push = true) {
                target = Math.max(0, Math.min(total - 1, target));

                if (target === index && slides[index].classList.contains('is-active')) {
                    updateHud();
                    return;
                }

                slides.forEach((slide, i) => {
                    const active = i === target;
                    slide.classList.toggle('is-active', active);
                    slide.classList.toggle('is-prev', i < target);
                    slide.setAttribute('aria-hidden', active ? 'false' : 'true');

                    if (active) {
                        slide.scrollTop = 0;
                    }
                });

                index = target;
                updateHud();

                if (push) {
                    history.replaceState(null, '', '#' + (index + 1));
                }
            }

            function next() { goTo(index + 1); }
            function prev() { goTo(index - 1); }

            // Build the overview grid from the slide data.
            slides.forEach((slide, i) => {
                const button = document.createElement('button');
                button.type = 'button';
                button.className = 'overview__item';
                button.innerHTML =
                    '<span class="overview__num">' + pad(i + 1) + '</span>' +
                    '<span class="overview__title"></span>' +
                    '<span class="overview__kind"></span>';

                button.querySelector('.overview__title').textContent = slide.dataset.title || 'Slide ' + (i + 1);
                button.querySelector('.overview__kind').textContent = slide.dataset.kind || '';
                button.addEventListener('click', () => {
                    goTo(i);
                    closeOverview();
                });

                overviewGrid.appendChild(button);
            });

            function openOverview() {
                overviewOpen = true;
                overview.classList.add('is-open');
                overview.setAttribute('aria-hidden', 'false');
                updateHud();
                closeOverviewBtn.focus();
            }

            function closeOverview() {
                if (!overviewOpen) {
                    return;
                }

                overviewOpen = false;
                overview.classList.remove('is-open');
                overview.setAttribute('aria-hidden', 'true');
                overviewBtn.focus();
            }

            function toggleOverview() {
                overviewOpen ? closeOverview() : openOverview();
            }

            function toggleFullscreen() {
                if (!document.fullscreenElement) {
                    const request = document.documentElement.requestFullscreen;
                    if (request) {
                        request.call(document.documentElement).catch(() => {});
                    }
                } else if (document.exitFullscreen) {
                    document.exitFullscreen().catch(() => {});
                }
            }

            document.addEventListener('fullscreenchange', () => {
                fullscreenBtn.textContent = document.fullscreenElement ? 'Exit fullscreen' : 'Fullscreen';
            });

            document.addEventListener('keydown', (event) => {
                if (event.target.closest('input, textarea, select')) {
                    return;
                }

                if (event.key === 'Escape') {
                    if (overviewOpen) {
                        event.preventDefault();
                        closeOverview();
                    }
                    return;
                }

                if (overviewOpen) {
                    if (event.key === 'ArrowRight' || event.key === 'ArrowDown') {
                        event.preventDefault();
                        next();
                    } else if (event.key === 'ArrowLeft' || event.key === 'ArrowUp') {
                        event.preventDefault();
                        prev();
                    }
                    return;
                }

                switch (event.key) {
                    case 'ArrowRight':
                    case 'ArrowDown':
                    case ' ':
                    case 'PageDown':
                        event.preventDefault();
                        next();
                        break;
                    case 'ArrowLeft':
                    case 'ArrowUp':
                    case 'PageUp':
                        event.preventDefault();
                        prev();
                        break;
                    case 'Home':
                        event.preventDefault();
                        goTo(0);
                        break;
                    case 'End':
                        event.preventDefault();
                        goTo(total - 1);
                        break;
                    case 'f':
                    case 'F':
                        event.preventDefault();
                        toggleFullscreen();
                        break;
                    case 'o':
                    case 'O':
                        event.preventDefault();
                        toggleOverview();
                        break;
                    case 'h':
                    case 'H':
                        event.preventDefault();
                        document.body.classList.toggle('is-hud-hidden');
                        break;
                }
            });

            prevBtn.addEventListener('click', prev);
            nextBtn.addEventListener('click', next);
            zonePrev.addEventListener('click', prev);
            zoneNext.addEventListener('click', next);
            fullscreenBtn.addEventListener('click', toggleFullscreen);
            overviewBtn.addEventListener('click', openOverview);
            closeOverviewBtn.addEventListener('click', closeOverview);

            overview.addEventListener('click', (event) => {
                if (event.target === overview) {
                    closeOverview();
                }
            });

            // Touch swipe.
            let touchStartX = 0;
            let touchStartY = 0;

            document.addEventListener('touchstart', (event) => {
                if (event.touches.length !== 1) {
                    return;
                }

                touchStartX = event.touches[0].clientX;
                touchStartY = event.touches[0].clientY;
            }, { passive: true });

            document.addEventListener('touchend', (event) => {
                if (event.changedTouches.length !== 1) {
                    return;
                }

                const dx = event.changedTouches[0].clientX - touchStartX;
                const dy = event.changedTouches[0].clientY - touchStartY;

                if (Math.abs(dx) < 60 || Math.abs(dx) < Math.abs(dy)) {
                    return;
                }

                dx < 0 ? next() : prev();
            }, { passive: true });

            // Open on the slide in the URL hash, if it is valid.
            const hash = Number(window.location.hash.replace('#', ''));
            const initial = Number.isFinite(hash) && hash >= 1 && hash <= total ? hash - 1 : 0;

            slides.forEach((slide, i) => {
                slide.classList.toggle('is-active', i === initial);
                slide.setAttribute('aria-hidden', i === initial ? 'false' : 'true');
            });

            index = initial;
            updateHud();

            // Fade the keyboard hint after a few seconds.
            hintTimer = window.setTimeout(() => hint.classList.add('is-hidden'), 6500);

            document.addEventListener('mousemove', () => {
                if (!hint.classList.contains('is-hidden')) {
                    window.clearTimeout(hintTimer);
                    hintTimer = window.setTimeout(() => hint.classList.add('is-hidden'), 4000);
                }
            }, { passive: true });
        })();
    </script>
</body>

</html>
