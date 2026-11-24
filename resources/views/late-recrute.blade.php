<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Late application — The Great Debaters Club</title>
    <meta name="description" content="Submit a late application to join The Great Debaters Club at FST Errachidia.">

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

    <a class="recruit-skip" href="#application">Skip to the late application form</a>

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

    <main class="recruit-main" id="application">
        <div class="recruit-heading">
            <p class="recruit-eyebrow">2026 / 2027 · Late recruitment</p>
            <h1>Late application form</h1>
            <p class="recruit-lead">Recruitment for this season has ended, but late applications are still open. Complete the form below to apply to The Great Debaters Club. It takes about two minutes.</p>
        </div>

        <div class="recruit-alert recruit-alert--success" role="note">
            <div><strong>Late application.</strong> Applications submitted here are marked as late so the Bureau can review them separately.</div>
        </div>

        @if (session('success'))
            <div class="recruit-alert recruit-alert--success" role="status">
                <div><strong>Application received.</strong> {{ session('success') }}</div>
            </div>
        @endif

        @if (session('error'))
            <div class="recruit-alert recruit-alert--error" role="alert">
                <div><strong>Something went wrong.</strong> {{ session('error') }}</div>
            </div>
        @endif

        @if ($errors->any())
            <div class="recruit-alert recruit-alert--error" role="alert" tabindex="-1">
                <div>
                    <strong>Please review the form.</strong>
                    <ul>
                        @foreach ($errors->all() as $message)
                            <li>{{ $message }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        @endif

        <div class="recruit-layout">
            <div class="recruit-primary">
                <form action="{{ route('late_recrute.store') }}" method="POST" class="recruit-form">

                    @csrf

                    {{-- 01 · Personal information ------------------------------------ --}}
                    <section class="recruit-section" aria-labelledby="personal-heading">
                        <div class="recruit-section-head">
                            <span class="recruit-section-index" aria-hidden="true">01</span>
                            <div>
                                <h2 id="personal-heading">Personal information</h2>
                                <p>Tell us how to identify and contact you.</p>
                            </div>
                        </div>

                        <div class="recruit-grid">
                            <div class="recruit-field">
                                <label class="recruit-label" for="first_name">First name <span class="recruit-req" aria-hidden="true">*</span></label>
                                <input class="recruit-control @error('first_name') is-invalid @enderror"
                                       id="first_name" name="first_name" type="text"
                                       value="{{ old('first_name') }}" placeholder="e.g. Soulaimane"
                                       autocomplete="given-name" required
                                       @error('first_name') aria-invalid="true" aria-describedby="first_name-error" @enderror>
                                @error('first_name') <small class="recruit-error" id="first_name-error">{{ $message }}</small> @enderror
                            </div>

                            <div class="recruit-field">
                                <label class="recruit-label" for="last_name">Last name <span class="recruit-req" aria-hidden="true">*</span></label>
                                <input class="recruit-control @error('last_name') is-invalid @enderror"
                                       id="last_name" name="last_name" type="text"
                                       value="{{ old('last_name') }}" placeholder="e.g. Zoubine"
                                       autocomplete="family-name" required
                                       @error('last_name') aria-invalid="true" aria-describedby="last_name-error" @enderror>
                                @error('last_name') <small class="recruit-error" id="last_name-error">{{ $message }}</small> @enderror
                            </div>

                            <div class="recruit-field">
                                <label class="recruit-label" for="phone_number">Phone / WhatsApp <span class="recruit-req" aria-hidden="true">*</span></label>
                                <input class="recruit-control @error('phone_number') is-invalid @enderror"
                                       id="phone_number" name="phone_number" type="tel"
                                       value="{{ old('phone_number') }}" placeholder="06 12 34 56 78"
                                       autocomplete="tel" required
                                       @error('phone_number') aria-invalid="true" aria-describedby="phone_number-error" @enderror>
                                @error('phone_number') <small class="recruit-error" id="phone_number-error">{{ $message }}</small> @enderror
                            </div>

                            <div class="recruit-field">
                                <label class="recruit-label" for="email">Email address <span class="recruit-req" aria-hidden="true">*</span></label>
                                <input class="recruit-control @error('email') is-invalid @enderror"
                                       id="email" name="email" type="email"
                                       value="{{ old('email') }}" placeholder="you@example.com"
                                       autocomplete="email" required
                                       @error('email') aria-invalid="true" aria-describedby="email-error" @enderror>
                                @error('email') <small class="recruit-error" id="email-error">{{ $message }}</small> @enderror
                            </div>

                            <div class="recruit-field">
                                <label class="recruit-label" for="cne">CNE / Massar <span class="recruit-req" aria-hidden="true">*</span></label>
                                <input class="recruit-control @error('cne') is-invalid @enderror"
                                       id="cne" name="cne" type="text"
                                       value="{{ old('cne') }}" placeholder="Your student identifier" required
                                       @error('cne') aria-invalid="true" aria-describedby="cne-error" @enderror>
                                @error('cne') <small class="recruit-error" id="cne-error">{{ $message }}</small> @enderror
                            </div>

                            <div class="recruit-field">
                                <label class="recruit-label" for="filiere">Programme / Filière <span class="recruit-req" aria-hidden="true">*</span></label>
                                <select class="recruit-control @error('filiere') is-invalid @enderror"
                                        id="filiere" name="filiere" required
                                        @error('filiere') aria-invalid="true" aria-describedby="filiere-error" @enderror>
                                    <option value="" disabled {{ old('filiere') ? '' : 'selected' }}>Choose your programme</option>
                                    @foreach (['DEUST MIP', 'DEUST BCG', 'LST', 'Cycle Ingénieur', 'MST', 'Doctorat', 'Autre'] as $programme)
                                        <option value="{{ $programme }}" {{ old('filiere') === $programme ? 'selected' : '' }}>
                                            {{ $programme }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('filiere') <small class="recruit-error" id="filiere-error">{{ $message }}</small> @enderror
                            </div>
                        </div>
                    </section>

                    {{-- 02 · Preferred section --------------------------------------- --}}
                    <section class="recruit-section" aria-labelledby="section-heading">
                        <div class="recruit-section-head">
                            <span class="recruit-section-index" aria-hidden="true">02</span>
                            <div>
                                <h2 id="section-heading">Preferred section</h2>
                                <p>Select the language section you would like to join.</p>
                            </div>
                        </div>

                        <div class="recruit-choices" role="radiogroup" aria-labelledby="section-heading"
                             @error('preferred_section') aria-describedby="preferred_section-error" @enderror>
                            <label class="recruit-choice">
                                <input type="radio" name="preferred_section" value="EN"
                                       {{ old('preferred_section', 'EN') === 'EN' ? 'checked' : '' }} required>
                                <span class="recruit-choice-dot" aria-hidden="true"></span>
                                <span class="recruit-choice-copy"><strong>English</strong><small>British Parliamentary</small></span>
                            </label>
                            <label class="recruit-choice">
                                <input type="radio" name="preferred_section" value="FR"
                                       {{ old('preferred_section') === 'FR' ? 'checked' : '' }}>
                                <span class="recruit-choice-dot" aria-hidden="true"></span>
                                <span class="recruit-choice-copy"><strong>Français</strong><small>Art oratoire</small></span>
                            </label>
                            <label class="recruit-choice">
                                <input type="radio" name="preferred_section" value="AR"
                                       {{ old('preferred_section') === 'AR' ? 'checked' : '' }}>
                                <span class="recruit-choice-dot" aria-hidden="true"></span>
                                <span class="recruit-choice-copy"><strong>العربية</strong><small>مناظرة برلمانية</small></span>
                            </label>
                        </div>
                        @error('preferred_section') <small class="recruit-error" id="preferred_section-error">{{ $message }}</small> @enderror
                    </section>

                    {{-- 03 · Motivation ---------------------------------------------- --}}
                    <section class="recruit-section" aria-labelledby="motivation-heading">
                        <div class="recruit-section-head">
                            <span class="recruit-section-index" aria-hidden="true">03</span>
                            <div>
                                <h2 id="motivation-heading">Motivation</h2>
                                <p>There is no perfect answer. We want to hear from you.</p>
                            </div>
                        </div>

                        <div class="recruit-field">
                            <label class="recruit-label" for="motivation">Why do you want to join the club? <span class="recruit-req" aria-hidden="true">*</span></label>
                            <textarea class="recruit-control @error('motivation') is-invalid @enderror"
                                      id="motivation" name="motivation" rows="5" maxlength="2500"
                                      placeholder="Write a few sentences about your interest in debate..." required
                                      @error('motivation') aria-invalid="true" aria-describedby="motivation-error" @enderror>{{ old('motivation') }}</textarea>
                            <div class="recruit-meta">
                                @error('motivation')
                                    <small class="recruit-error" id="motivation-error">{{ $message }}</small>
                                @else
                                    <small class="recruit-hint">Between 15 and 2500 characters.</small>
                                @enderror
                                <small class="recruit-count" data-count-for="motivation">{{ mb_strlen((string) old('motivation')) }} / 2500</small>
                            </div>
                        </div>
                    </section>

                    <div class="recruit-actions">
                        <p class="recruit-required"><span class="recruit-req" aria-hidden="true">*</span> Required fields</p>
                        <button type="submit" class="recruit-submit">
                            <span class="recruit-submit-label">Submit application <span class="recruit-arrow" aria-hidden="true">→</span></span>
                            <span class="recruit-submit-loading">
                                <span class="recruit-spinner" aria-hidden="true"></span> Submitting…
                            </span>
                        </button>
                    </div>
                    <p class="recruit-privacy">Your information will only be used for club recruitment.</p>
                </form>
            </div>

            <aside class="recruit-aside" aria-label="Application information">
                <div class="recruit-aside-card">
                    <h2>What happens next</h2>
                    <p>From application to your first session.</p>
                    <ol class="recruit-steps">
                        <li>
                            <span class="recruit-step-num" aria-hidden="true">1</span>
                            <span class="recruit-step-body"><strong>Submit your application</strong><span>Fill in the form on this page.</span></span>
                        </li>
                        <li>
                            <span class="recruit-step-num" aria-hidden="true">2</span>
                            <span class="recruit-step-body"><strong>The Bureau reviews it</strong><span>Every application is read by the board.</span></span>
                        </li>
                        <li>
                            <span class="recruit-step-num" aria-hidden="true">3</span>
                            <span class="recruit-step-body"><strong>We contact you</strong><span>Expect a message via WhatsApp or email.</span></span>
                        </li>
                    </ol>
                </div>

                <div class="recruit-aside-card">
                    <h2>Who can apply</h2>
                    <ul class="recruit-list">
                        <li>Open to all FST Errachidia students, every year and filière.</li>
                        <li>No prior debating experience required.</li>
                        <li>Membership is free.</li>
                        <li>Choose English, French or Arabic debate.</li>
                    </ul>
                </div>
            </aside>
        </div>
    </main>

    <footer class="recruit-footer">
        <div class="recruit-footer-inner">
            <span>© {{ date('Y') }} The Great Debaters Club</span>
            <span>FST Errachidia · Université Moulay Ismaïl</span>
        </div>
    </footer>

    <script>
        // Progressive enhancement only — the form works fully without JS.
        (function () {
            // Live character counter for the motivation field.
            var field = document.getElementById('motivation');
            var counter = document.querySelector('[data-count-for="motivation"]');
            if (field && counter) {
                var max = parseInt(field.getAttribute('maxlength'), 10) || 2500;
                var warnAt = max - 200;
                var update = function () {
                    var length = field.value.length;
                    counter.textContent = length + ' / ' + max;
                    counter.classList.toggle('is-near', length > warnAt);
                };
                field.addEventListener('input', update);
                update();
            }

            // Loading state on submit (native validation runs first).
            var form = document.querySelector('.recruit-form');
            var submit = form && form.querySelector('.recruit-submit');
            if (form && submit) {
                form.addEventListener('submit', function () {
                    submit.classList.add('is-submitting');
                    submit.disabled = true;
                    submit.setAttribute('aria-busy', 'true');
                });
            }
        })();
    </script>
</body>
</html>
