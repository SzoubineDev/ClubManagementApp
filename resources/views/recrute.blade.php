<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Apply — The Great Debaters Club</title>
    <meta name="description" content="Apply to join The Great Debaters Club at FST Errachidia.">
    @vite([
        'resources/css/app.css',
        'resources/css/landing.css',
        'resources/css/recrute.css',
        'resources/js/app.js',
    ])
</head>
<body class="nb recruit-page">

    <header class="recruit-header">
        <a href="{{ route('landing') }}" class="recruit-brand">
            <img src="{{ asset('images/logo.jpg') }}" alt="The Great Debaters Club">
            <span>
                <strong>The Great Debaters</strong>
                <small>FST Errachidia · UMI</small>
            </span>
        </a>
        <a href="{{ route('landing') }}" class="recruit-back">Back to website</a>
    </header>

    <main class="recruit-main">
        <div class="recruit-heading">
            <p class="recruit-eyebrow">2026 / 2027 · Recruitment</p>
            <h1>Application form</h1>
            <p>Complete the form below to apply to The Great Debaters Club.</p>
        </div>

        @if (session('success'))
            <div class="recruit-alert recruit-alert--success" role="status">{{ session('success') }}</div>
        @endif

        @if (session('error'))
            <div class="recruit-alert recruit-alert--error" role="alert">{{ session('error') }}</div>
        @endif

        @if ($errors->any())
            <div class="recruit-alert recruit-alert--error" role="alert">
                Please check the highlighted fields and try again.
            </div>
        @endif

        <form action="{{ route('recrute.store') }}" method="POST" class="recruit-form">
            @csrf

            <section class="form-section" aria-labelledby="personal-heading">
                <div class="form-section-heading">
                    <span class="section-index">01</span>
                    <div>
                        <h2 id="personal-heading">Personal information</h2>
                        <p>Tell us how to identify and contact you.</p>
                    </div>
                </div>

                <div class="form-grid">
                    <div class="material-field">
                        <label for="first_name">First name <span>*</span></label>
                        <input id="first_name" name="first_name" type="text"
                               value="{{ old('first_name') }}" placeholder="e.g. Soulaimane"
                               autocomplete="given-name" required
                               class="@error('first_name') is-invalid @enderror">
                        @error('first_name') <small class="field-error">{{ $message }}</small> @enderror
                    </div>

                    <div class="material-field">
                        <label for="last_name">Last name <span>*</span></label>
                        <input id="last_name" name="last_name" type="text"
                               value="{{ old('last_name') }}" placeholder="e.g. Zoubine"
                               autocomplete="family-name" required
                               class="@error('last_name') is-invalid @enderror">
                        @error('last_name') <small class="field-error">{{ $message }}</small> @enderror
                    </div>

                    <div class="material-field">
                        <label for="phone_number">Phone / WhatsApp <span>*</span></label>
                        <input id="phone_number" name="phone_number" type="tel"
                               value="{{ old('phone_number') }}" placeholder="06 12 34 56 78"
                               autocomplete="tel" required
                               class="@error('phone_number') is-invalid @enderror">
                        @error('phone_number') <small class="field-error">{{ $message }}</small> @enderror
                    </div>

                    <div class="material-field">
                        <label for="email">Email address <span>*</span></label>
                        <input id="email" name="email" type="email"
                               value="{{ old('email') }}" placeholder="you@example.com"
                               autocomplete="email" required
                               class="@error('email') is-invalid @enderror">
                        @error('email') <small class="field-error">{{ $message }}</small> @enderror
                    </div>

                    <div class="material-field">
                        <label for="cne">CNE / Massar <span>*</span></label>
                        <input id="cne" name="cne" type="text"
                               value="{{ old('cne') }}" placeholder="Your student identifier" required
                               class="@error('cne') is-invalid @enderror">
                        @error('cne') <small class="field-error">{{ $message }}</small> @enderror
                    </div>

                    <div class="material-field">
                        <label for="filiere">Programme / Filière <span>*</span></label>
                        <select id="filiere" name="filiere" required
                                class="@error('filiere') is-invalid @enderror">
                            <option value="" disabled {{ old('filiere') ? '' : 'selected' }}>Choose your programme</option>
                            @foreach (['DEUST MIP', 'DEUST BCG', 'LST', 'Cycle Ingénieur', 'MST', 'Doctorat', 'Autre'] as $programme)
                                <option value="{{ $programme }}" {{ old('filiere') === $programme ? 'selected' : '' }}>
                                    {{ $programme }}
                                </option>
                            @endforeach
                        </select>
                        @error('filiere') <small class="field-error">{{ $message }}</small> @enderror
                    </div>
                </div>
            </section>

            <section class="form-section" aria-labelledby="section-heading">
                <div class="form-section-heading">
                    <span class="section-index">02</span>
                    <div>
                        <h2 id="section-heading">Preferred section</h2>
                        <p>Select the language section you would like to join.</p>
                    </div>
                </div>

                <div class="radio-fieldset">
                    <label class="radio-option">
                        <input type="radio" name="preferred_section" value="EN"
                               {{ old('preferred_section', 'EN') === 'EN' ? 'checked' : '' }} required>
                        <span class="radio-control"></span>
                        <span class="radio-copy"><strong>English</strong><small>British Parliamentary</small></span>
                    </label>
                    <label class="radio-option">
                        <input type="radio" name="preferred_section" value="FR"
                               {{ old('preferred_section') === 'FR' ? 'checked' : '' }}>
                        <span class="radio-control"></span>
                        <span class="radio-copy"><strong>Français</strong><small>Art oratoire</small></span>
                    </label>
                    <label class="radio-option">
                        <input type="radio" name="preferred_section" value="AR"
                               {{ old('preferred_section') === 'AR' ? 'checked' : '' }}>
                        <span class="radio-control"></span>
                        <span class="radio-copy"><strong>العربية</strong><small>مناظرة برلمانية</small></span>
                    </label>
                </div>
                @error('preferred_section') <small class="field-error">{{ $message }}</small> @enderror
            </section>

            <section class="form-section" aria-labelledby="motivation-heading">
                <div class="form-section-heading">
                    <span class="section-index">03</span>
                    <div>
                        <h2 id="motivation-heading">Motivation</h2>
                        <p>There is no perfect answer. We want to hear from you.</p>
                    </div>
                </div>

                <div class="material-field material-field--textarea">
                    <label for="motivation">Why do you want to join the club? <span>*</span></label>
                    <textarea id="motivation" name="motivation" rows="5"
                              placeholder="Write a few sentences about your interest in debate..."
                              required class="@error('motivation') is-invalid @enderror">{{ old('motivation') }}</textarea>
                    @error('motivation') <small class="field-error">{{ $message }}</small> @enderror
                </div>
            </section>

            <div class="form-actions">
                <p><span aria-hidden="true">*</span> Required fields</p>
                <button type="submit">Submit application <span aria-hidden="true">→</span></button>
            </div>
            <p class="form-privacy">Your information will only be used for club recruitment.</p>
        </form>
    </main>

    <footer class="recruit-footer">
        © {{ date('Y') }} The Great Debaters Club · FST Errachidia
    </footer>
</body>
</html>
