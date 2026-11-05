<!DOCTYPE html>

<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <title>Apply — The Great Debaters Club · FST Errachidia</title>
    <meta name="description" content="Apply to join The Great Debaters Club at FST Errachidia. Master public speaking, critical reasoning, and trilingual debate.">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Archivo+Black&family=Plus+Jakarta+Sans:ital,wght@0,400;0,500;0,600;0,700;1,400&family=Roboto+Condensed:wght@400;700;900&display=swap" rel="stylesheet">

    @vite([
    'resources/css/app.css',
    'resources/css/landing.css',
    'resources/css/recrute.css',
    ])
</head>

<body class="nb">

    {{-- ============================ HEADER ============================ --}}
    <header class="nb-nav nb-nav--recrute" aria-label="Recruitment Navigation">
        <div class="nb-nav-inner">
            <a href="{{ route('landing') }}" class="nb-wordmark" aria-label="TGD Home">
                <div class="nb-wordmark-crest">
                    <img src="{{ asset('images/logo.jpg') }}" alt="TGD Crest">
                </div>
                <div class="nb-wordmark-text">
                    <span class="nb-wordmark-title">The Great Debaters</span>
                    <span class="nb-wordmark-sub">FST Errachidia · UMI</span>
                </div>
            </a>

            <div class="nb-nav-actions nb-nav-actions--recrute">
                <a href="{{ route('landing') }}" class="nb-login-link">← Back</a>
                <a href="{{ route('login') }}" class="nb-btn nb-btn--outline nb-btn--sm">Log in</a>
            </div>
        </div>
    </header>

    {{-- ============================= MAIN ============================= --}}
    <main class="nb-recrute-page">
        <div class="nb-container">

            {{-- Hero --}}
            <div class="nb-recrute-hero">
                <div class="nb-hero-kicker" style="margin-bottom:12px;">
                    <span class="nb-badge nb-badge--burgundy nb-badge--dot">2026 / 2027 Season</span>
                    <span class="nb-badge nb-badge--gold">Applications open</span>
                </div>
                <h1 class="nb-h2">Join <em>The Floor.</em></h1>
                <p class="nb-section-sub">Open to all FST students. No prior experience needed.</p>
            </div>

            {{-- Flash alerts --}}
            @if (session('success'))
            <div class="nb-alert-banner nb-alert-banner--success" role="alert">
                <div class="nb-alert-icon">✓</div>
                <div>
                    <h3 class="nb-alert-title">Application submitted</h3>
                    <p class="nb-alert-desc">{{ session('success') }}</p>
                </div>
            </div>
            @endif

            @if (session('error'))
            <div class="nb-alert-banner nb-alert-banner--error" role="alert">
                <div class="nb-alert-icon">!</div>
                <div>
                    <h3 class="nb-alert-title">Something went wrong</h3>
                    <p class="nb-alert-desc">{{ session('error') }}</p>
                </div>
            </div>
            @endif

            @if ($errors->any())
            <div class="nb-alert-banner nb-alert-banner--error" role="alert">
                <div class="nb-alert-icon">!</div>
                <div>
                    <h3 class="nb-alert-title">Please review the fields below</h3>
                    <p class="nb-alert-desc">Some required information is missing or needs correction.</p>
                </div>
            </div>
            @endif

            {{-- Split layout: sidebar + form --}}
            <div class="nb-recrute-split">

                {{-- ========================= SIDEBAR ========================= --}}
                <aside class="nb-recrute-sidebar">
                    <div class="nb-sidebar-box">
                        <span class="nb-badge nb-badge--gold" style="font-size:11px;">Why TGD</span>
                        <h3 class="nb-sidebar-title">A serious edge for any career.</h3>

                        <ul class="nb-sidebar-perks">
                            <li>
                                <span class="nb-perk-bullet">✓</span>
                                <div>
                                    <strong>Trilingual debate</strong>
                                    <span>English · Français · العربية</span>
                                </div>
                            </li>
                            <li>
                                <span class="nb-perk-bullet">✓</span>
                                <div>
                                    <strong>Weekly speaking clinics</strong>
                                    <span>Voice · presence · delivery</span>
                                </div>
                            </li>
                            <li>
                                <span class="nb-perk-bullet">✓</span>
                                <div>
                                    <strong>Regional tournaments</strong>
                                    <span>Represent FST Errachidia</span>
                                </div>
                            </li>
                            <li>
                                <span class="nb-perk-bullet">✓</span>
                                <div>
                                    <strong>Critical thinking</strong>
                                    <span>Logic · structure · rebuttal</span>
                                </div>
                            </li>
                        </ul>

                        <div class="nb-sidebar-meta">
                            <div class="nb-meta-row">
                                <span class="nb-meta-label">Fee</span>
                                <span class="nb-meta-val" style="color:var(--nb-burgundy); font-weight:800;">Free</span>
                            </div>
                            <div class="nb-meta-row">
                                <span class="nb-meta-label">Meets</span>
                                <span class="nb-meta-val">Wed · 15:00</span>
                            </div>
                            <div class="nb-meta-row">
                                <span class="nb-meta-label">Venue</span>
                                <span class="nb-meta-val">Amphi A</span>
                            </div>
                        </div>
                    </div>
                </aside>

                {{-- ========================== FORM ========================== --}}
                <section class="nb-recrute-form-card">
                    <form action="{{ route('recrute.store') }}" method="POST" class="nb-rec-form" novalidate>
                        @csrf

                        {{-- STEP 01 --}}
                        <div class="nb-form-group-head">
                            <span class="nb-form-step-num">01</span>
                            <div>
                                <h2 class="nb-form-group-title">Personal details</h2>
                            </div>
                        </div>

                        <div class="nb-form-row nb-form-row--2col">
                            <div class="nb-field">
                                <label for="first_name" class="nb-field-label">First name <span class="nb-req">*</span></label>
                                <input type="text" id="first_name" name="first_name"
                                    value="{{ old('first_name') }}" placeholder="Soulaimane"
                                    class="nb-input {{ $errors->has('first_name') ? 'is-invalid' : '' }}" required>
                                @error('first_name')<span class="nb-field-error">{{ $message }}</span>@enderror
                            </div>

                            <div class="nb-field">
                                <label for="last_name" class="nb-field-label">Last name <span class="nb-req">*</span></label>
                                <input type="text" id="last_name" name="last_name"
                                    value="{{ old('last_name') }}" placeholder="Zoubine"
                                    class="nb-input {{ $errors->has('last_name') ? 'is-invalid' : '' }}" required>
                                @error('last_name')<span class="nb-field-error">{{ $message }}</span>@enderror
                            </div>
                        </div>

                        <div class="nb-form-row nb-form-row--2col">
                            <div class="nb-field">
                                <label for="phone_number" class="nb-field-label">Phone / WhatsApp <span class="nb-req">*</span></label>
                                <input type="tel" id="phone_number" name="phone_number"
                                    value="{{ old('phone_number') }}" placeholder="06 12 34 56 78"
                                    class="nb-input {{ $errors->has('phone_number') ? 'is-invalid' : '' }}" required>
                                @error('phone_number')<span class="nb-field-error">{{ $message }}</span>@enderror
                            </div>

                            <div class="nb-field">
                                <label for="email" class="nb-field-label">Email <span class="nb-req">*</span></label>
                                <input type="email" id="email" name="email"
                                    value="{{ old('email') }}" placeholder="you@edu.umi.ac.ma"
                                    class="nb-input {{ $errors->has('email') ? 'is-invalid' : '' }}" required>
                                @error('email')<span class="nb-field-error">{{ $message }}</span>@enderror
                            </div>
                        </div>

                        <div class="nb-form-row nb-form-row--2col">
                            <div class="nb-field">
                                <label for="cne" class="nb-field-label">CNE / Massar <span class="nb-req">*</span></label>
                                <input type="text" id="cne" name="cne"
                                    value="{{ old('cne') }}" placeholder="D130000000"
                                    class="nb-input {{ $errors->has('cne') ? 'is-invalid' : '' }}" required>
                                @error('cne')<span class="nb-field-error">{{ $message }}</span>@enderror
                            </div>

                            <div class="nb-field">
                                <label for="filiere" class="nb-field-label">Filière <span class="nb-req">*</span></label>
                                <select id="filiere" name="filiere"
                                    class="nb-input nb-select {{ $errors->has('filiere') ? 'is-invalid' : '' }}" required>
                                    <option value="" disabled {{ old('filiere') ? '' : 'selected' }}>Select your program…</option>
                                    <option value="DEUST MIP" {{ old('filiere') === 'DEUST MIP' ? 'selected' : '' }}>DEUST MIP</option>
                                    <option value="DEUST BCG" {{ old('filiere') === 'DEUST BCG' ? 'selected' : '' }}>DEUST BCG</option>
                                    <option value="LST" {{ old('filiere') === 'LST' ? 'selected' : '' }}>LST</option>
                                    <option value="Cycle Ingénieur" {{ old('filiere') === 'Cycle Ingénieur' ? 'selected' : '' }}>Cycle Ingénieur</option>
                                    <option value="MST" {{ old('filiere') === 'MST' ? 'selected' : '' }}>MST</option>
                                    <option value="Doctorat" {{ old('filiere') === 'Doctorat' ? 'selected' : '' }}>Doctorat</option>
                                    <option value="Autre" {{ old('filiere') === 'Autre' ? 'selected' : '' }}>Autre</option>
                                </select>
                                @error('filiere')<span class="nb-field-error">{{ $message }}</span>@enderror
                            </div>
                        </div>

                        {{-- STEP 02 --}}
                        <div class="nb-form-group-head" style="margin-top:28px;">
                            <span class="nb-form-step-num">02</span>
                            <div>
                                <h2 class="nb-form-group-title">Preferred section</h2>
                            </div>
                        </div>

                        <div class="nb-section-choice-grid">
                            <label class="nb-choice-card">
                                <input type="radio" name="preferred_section" value="EN"
                                    {{ old('preferred_section', 'EN') === 'EN' ? 'checked' : '' }} class="nb-choice-radio">
                                <div class="nb-choice-box">
                                    <span class="nb-choice-flag">🇬🇧</span>
                                    <strong class="nb-choice-title">English</strong>
                                    <span class="nb-choice-desc">British Parliamentary</span>
                                </div>
                            </label>

                            <label class="nb-choice-card">
                                <input type="radio" name="preferred_section" value="FR"
                                    {{ old('preferred_section') === 'FR' ? 'checked' : '' }} class="nb-choice-radio">
                                <div class="nb-choice-box">
                                    <span class="nb-choice-flag">🇫🇷</span>
                                    <strong class="nb-choice-title">Français</strong>
                                    <span class="nb-choice-desc">Art oratoire</span>
                                </div>
                            </label>

                            <label class="nb-choice-card">
                                <input type="radio" name="preferred_section" value="AR"
                                    {{ old('preferred_section') === 'AR' ? 'checked' : '' }} class="nb-choice-radio">
                                <div class="nb-choice-box">
                                    <span class="nb-choice-flag">🇲🇦</span>
                                    <strong class="nb-choice-title">العربية</strong>
                                    <span class="nb-choice-desc">مناظرة برلمانية</span>
                                </div>
                            </label>
                        </div>
                        @error('preferred_section')
                        <span class="nb-field-error" style="display:block; margin-top:8px;">{{ $message }}</span>
                        @enderror

                        {{-- STEP 03 --}}
                        <div class="nb-form-group-head" style="margin-top:28px;">
                            <span class="nb-form-step-num">03</span>
                            <div>
                                <h2 class="nb-form-group-title">Motivation</h2>
                            </div>
                        </div>

                        <div class="nb-field">
                            <label for="motivation" class="nb-field-label">Why do you want to join? <span class="nb-req">*</span></label>
                            <textarea id="motivation" name="motivation" rows="4"
                                placeholder="A few honest sentences — what draws you to debate?"
                                class="nb-input nb-textarea {{ $errors->has('motivation') ? 'is-invalid' : '' }}"
                                required>{{ old('motivation') }}</textarea>
                            @error('motivation')<span class="nb-field-error">{{ $message }}</span>@enderror
                        </div>

                        {{-- Submit --}}
                        <div class="nb-form-submit-row">
                            <button type="submit" class="nb-btn nb-btn--primary nb-btn-lg" style="width:100%; justify-content:center;">
                                Submit application →
                            </button>
                            <p class="nb-submit-assurance">
                                🔒 Your data stays private with the TGD Bureau.
                            </p>
                        </div>
                    </form>
                </section>

            </div>
        </div>
    </main>

    {{-- ============================ FOOTER ============================ --}}
    <footer class="nb-footer" style="margin-top:60px;">
        <div class="nb-container">
            <div class="nb-footer-bottom" style="margin-top:0; padding-top:0; border-top:none;">
                <span>© {{ date('Y') }} The Great Debaters Club · FST Errachidia (UMI)</span>
                <span>Think. Speak. Change.</span>
            </div>
        </div>
    </footer>

</body>

</html>