@props(['bodyClass' => 'app-shell', 'extraCss' => []])

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Uni Club') }}</title>

    {{-- UI type: Inter for body, Plus Jakarta Sans for headings --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Plus+Jakarta+Sans:wght@500;600;700&display=swap"
        rel="stylesheet">

    {{-- Custom design system (no Tailwind) --}}
    @php $css = array_merge(['resources/css/app.css', 'resources/css/brutal.css'], $extraCss); @endphp
    @vite(array_merge($css, ['resources/js/app.js']))
    <style>[x-cloak] { display: none !important; }</style>
</head>

<body class="{{ $bodyClass }}" x-data="{ mobileOpen: false }">
    {{ $slot }}
</body>

</html>
