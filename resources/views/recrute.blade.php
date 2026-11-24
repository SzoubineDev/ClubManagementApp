@php
    // Local number 0712051658 → international format for WhatsApp (Morocco +212).
    $whatsappUrl = 'https://wa.me/212712051658?text=' . rawurlencode('Hello, I would like more information about recruitment.');
@endphp
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Recruitment closed — The Great Debaters Club</title>
    <meta name="description" content="Recruitment is over for this season at The Great Debaters Club, FST Errachidia.">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">

    @vite([
        'resources/css/app.css',
        'resources/css/landing.css',
        'resources/css/recrute.css',
        'resources/js/app.js',
    ])
</head>
<body class="recruit-page">

    <a class="recruit-skip" href="#notice">Skip to the notice</a>

    <header class="recruit-header">
        <div class="recruit-header-inner">
            <a href="{{ route('landing') }}" class="recruit-brand">
                <img src="{{ asset('images/logo.jpg') }}" alt="">
                <span class="recruit-brand-text">
                    <strong>The Great Debaters</strong>
                    <small>FST Errachidia · UMI</small>
                </span>
            </a>
            <a href="{{ route('landing') }}" class="recruit-back">
                <span class="recruit-arrow" aria-hidden="true">←</span> Back to website
            </a>
        </div>
    </header>

    <main class="recruit-main" id="notice">
        <div class="recruit-closed">
            <div class="recruit-closed-intro">
                <span class="recruit-closed-icon" aria-hidden="true">
                    <svg viewBox="0 0 24 24" width="26" height="26" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                        <rect x="3" y="4.5" width="18" height="16" rx="2.5"></rect>
                        <path d="M8 2.5v4M16 2.5v4M3 9.5h18"></path>
                        <path d="m9.5 14 5 4M14.5 14l-5 4"></path>
                    </svg>
                </span>
                <p class="recruit-eyebrow">2026 / 2027 · Recruitment</p>
                <h1>Recruitment is over for this season.</h1>
                <p class="recruit-lead">For more information, send us a message on WhatsApp.</p>
            </div>

            <div class="recruit-langs">
                <article class="recruit-lang" lang="fr" dir="ltr">
                    <span class="recruit-lang-tag">Français</span>
                    <p class="recruit-lang-message">Le recrutement est terminé pour cette saison.</p>
                    <p class="recruit-lang-text">
                        Pour plus d'informations,
                        <a href="{{ $whatsappUrl }}" target="_blank" rel="noopener noreferrer">envoyez-nous un message</a>.
                    </p>
                </article>

                <article class="recruit-lang" lang="ar" dir="rtl">
                    <span class="recruit-lang-tag">العربية</span>
                    <p class="recruit-lang-message">انتهى الترشيح لهذا الموسم.</p>
                    <p class="recruit-lang-text">
                        لمزيد من المعلومات،
                        <a href="{{ $whatsappUrl }}" target="_blank" rel="noopener noreferrer">أرسل لنا رسالة</a>.
                    </p>
                </article>

                <article class="recruit-lang" lang="en" dir="ltr">
                    <span class="recruit-lang-tag">English</span>
                    <p class="recruit-lang-message">Recruitment is over for this season.</p>
                    <p class="recruit-lang-text">
                        For more information,
                        <a href="{{ $whatsappUrl }}" target="_blank" rel="noopener noreferrer">send us a message</a>.
                    </p>
                </article>
            </div>

            <div class="recruit-wa">
                <div class="recruit-wa-copy">
                    <strong>WhatsApp</strong>
                    <span>07 12 05 16 58</span>
                </div>
                <a class="recruit-wa-btn" href="{{ $whatsappUrl }}" target="_blank" rel="noopener noreferrer">
                    <svg viewBox="0 0 24 24" width="20" height="20" fill="currentColor" aria-hidden="true">
                        <path d="M12.04 2c-5.5 0-9.96 4.46-9.96 9.96 0 1.76.46 3.45 1.32 4.95L2 22l5.25-1.38a9.9 9.9 0 0 0 4.79 1.22h.01c5.5 0 9.96-4.46 9.96-9.96S17.54 2 12.04 2Zm0 18.15h-.01a8.2 8.2 0 0 1-4.18-1.14l-.3-.18-3.11.82.83-3.04-.2-.31a8.17 8.17 0 0 1-1.25-4.34c0-4.52 3.68-8.2 8.21-8.2a8.2 8.2 0 0 1 8.2 8.21c0 4.52-3.68 8.18-8.19 8.18Zm4.5-6.13c-.25-.12-1.47-.72-1.69-.8-.23-.09-.39-.13-.56.12-.17.25-.64.8-.79.96-.14.17-.29.19-.54.06-.25-.12-1.05-.39-2-1.23-.74-.66-1.24-1.47-1.38-1.72-.15-.25-.02-.39.11-.51.11-.11.25-.29.37-.43.12-.14.17-.25.25-.42.08-.17.04-.31-.02-.43-.06-.12-.56-1.34-.76-1.84-.2-.48-.4-.42-.56-.43h-.48c-.17 0-.43.06-.66.31-.23.25-.87.85-.87 2.07 0 1.22.89 2.4 1.01 2.57.12.17 1.75 2.67 4.24 3.74.59.26 1.05.41 1.41.52.59.19 1.13.16 1.56.1.48-.07 1.47-.6 1.68-1.18.21-.58.21-1.08.14-1.18-.06-.11-.22-.17-.47-.29Z"/>
                    </svg>
                    <span>Send us a message</span>
                </a>
            </div>
        </div>
    </main>

    <footer class="recruit-footer">
        <div class="recruit-footer-inner">
            <span>© {{ date('Y') }} The Great Debaters Club</span>
            <span>FST Errachidia · Université Moulay Ismaïl</span>
        </div>
    </footer>
</body>
</html>
